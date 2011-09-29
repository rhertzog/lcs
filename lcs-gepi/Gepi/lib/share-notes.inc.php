<?php
/** Fonctions de manipulation des conteneurs
 * 
 * $Id: share-notes.inc.php 7747 2011-08-14 13:45:46Z regis $
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Notes
 * @subpackage Initialisation
 *
*/

/**
 * D�truit les conteneurs vides qui ne sont pas rattach�s � un parent
 *
 * @param int $id_conteneur Id du conteneur
 * @param int $id_racine Id du conteneur racine
 * @todo � v�rifier
 */
function test_conteneurs_vides($id_conteneur,$id_racine) {
        // On teste si le conteneur est vide
        if ($id_conteneur !=0) {
            $sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$id_conteneur'");
            $nb_dev = mysql_num_rows($sql);
            $sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$id_conteneur'");
            $nb_cont = mysql_num_rows($sql);
            if (($nb_dev == 0) or ($nb_cont == 0)) {
                $query_parent = mysql_query("SELECT parent FROM cn_conteneurs WHERE id='$id_conteneur'");
                $id_par = mysql_result($query_parent, 0, 'parent');
                $sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_conteneur'");
                test_conteneurs_vides($id_par,$id_racine);
            }
        }
}

/**
 * Met � jour les moyennes des conteneurs
 *
 * @param array $_current_group les informations du groupes obtenues � partir de get_group()
 * @param int $periode_num le num�ro de la p�riode
 * @param int $id_racine Id du conteneur racine
 * @param int $id_conteneur Id du conteneur
 * @param string $arret si yes, on ne recalcule pas les sous-conteneurs
 * @see get_group()
 * @see calcule_moyenne()
 */
function mise_a_jour_moyennes_conteneurs($_current_group, $periode_num,$id_racine,$id_conteneur,&$arret) {
    //remarque : les variables $periode_num et id_racine auraient pus �tre r�cup�r�es
    //� partir de $id_conteneur, mais on �vite ainsi trop de calculs !

	if(isset($_current_group["eleves"][$periode_num])) {
		foreach ($_current_group["eleves"][$periode_num]["list"] as $_eleve_login) {
			if($_eleve_login!=""){
				calcule_moyenne($_eleve_login, $id_racine, $id_conteneur);
			}
		}
	
		if ($arret != 'yes') {
			//
			// D�termination du conteneur parent
			$query_id_parent = mysql_query("SELECT parent FROM cn_conteneurs WHERE id='$id_conteneur'");
			$id_parent = mysql_result($query_id_parent, 0, 'parent');
			if ($id_parent != 0) {
				$arret = 'no';
				mise_a_jour_moyennes_conteneurs($_current_group, $periode_num,$id_racine,$id_parent,$arret);
			} else {
				$arret = 'yes';
				mise_a_jour_moyennes_conteneurs($_current_group, $periode_num,$id_racine,$id_racine,$arret);
			}
	
		}
	}
}



//
// 
//
/**
 *Liste des sous-conteneurs d'un conteneur
 * 
 * Modifie les tableaux passer par r�f�rence
 * 
 * L'index de chaque tableau est identique  pour un sous-conteneur donn�
 * 
 * Utilise fdebug() pour le suivi des actions
 * 
 * @param int $id_conteneur id du conteneur
 * @param int $nb_sous_cont nombre de sous-conteneurs (pass� par r�f�rence)
 * @param array $nom_sous_cont nom des sous-conteneurs (pass� par r�f�rence)
 * @param array $coef_sous_cont coef des sous-conteneurs (pass� par r�f�rence)
 * @param array $id_sous_cont Id des sous-conteneurs
 * @param array $display_bulletin_sous_cont y si le sous conteneur doit �tre affich� (pass� par r�f�rence)
 * @param text $type all pour rechercher aussi les sous-sous-conteneurs
 * @see fdebug()
 */
function sous_conteneurs($id_conteneur,&$nb_sous_cont,&$nom_sous_cont,&$coef_sous_cont,&$id_sous_cont,&$display_bulletin_sous_cont,$type) {
	fdebug("===================================\n");
	fdebug("LANCEMENT DE sous_conteneurs() SUR\n");
	fdebug("id_conteneur=$id_conteneur avec type=$type\n");

    $query_sous_cont = mysql_query("SELECT * FROM cn_conteneurs WHERE (parent ='$id_conteneur' and id!='$id_conteneur') order by nom_court");
    $nb = mysql_num_rows($query_sous_cont);
    $i=0;
    while ($i < $nb) {
        $nom_sous_cont[$nb_sous_cont] = mysql_result($query_sous_cont, $i, 'nom_court');
        $coef_sous_cont[$nb_sous_cont] = mysql_result($query_sous_cont, $i, 'coef');
        $id_sous_cont[$nb_sous_cont] = mysql_result($query_sous_cont, $i, 'id');
        $display_bulletin_sous_cont[$nb_sous_cont] = mysql_result($query_sous_cont, $i, 'display_bulletin');
        $temp = $id_sous_cont[$nb_sous_cont];
        $nb_sous_cont++;
        if ($type=='all') {
            sous_conteneurs($temp,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all');
        }
        $i++;
    }
}


/**
 * Calcul la moyenne d'un conteneur
 * 
 * @param text $login login de l"�l�ve
 * @param type $id_racine Id racine du conteneur
 * @param type $id_conteneur Id du conteneur
 * @see fdebug
 * @see sous_conteneurs()
 * @see getSettingValue()
 * @see number_format()
 */
function calcule_moyenne($login, $id_racine, $id_conteneur) {
	fdebug("===================================\n");
	fdebug("LANCEMENT DE calcule_moyenne SUR\n");
	fdebug("login=$login: id_racine=$id_racine - id_conteneur=$id_conteneur\n");

    $total_point = 0;
    $somme_coef = 0;
    $exist_dev_fac = '';
    // On efface les moyennes de la table
    $sql="DELETE FROM cn_notes_conteneurs WHERE (login='$login' and id_conteneur='$id_conteneur');";
	fdebug("$sql\n");
    $delete = mysql_query($sql);

    // Appel des param�tres du conteneur
    $appel_conteneur = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='$id_conteneur'");
    $arrondir =  mysql_result($appel_conteneur, 0, 'arrondir');
    $mode =  mysql_result($appel_conteneur, 0, 'mode');
    $ponderation = mysql_result($appel_conteneur, 0, 'ponderation');

	fdebug("Conteneur n�$id_conteneur\n");
	fdebug("\$arrondir=$arrondir\n");
	fdebug("\$mode=$mode\n");
	fdebug("\$ponderation=$ponderation\n");

    // D�termination des sous-conteneurs � prendre en compte
    $nom_sous_cont = array();
    $id_sous_cont  = array();
    $coef_sous_cont = array();
    $nb_sous_cont = 0;
    if ($mode==1) {
        //la moyenne s'effectue sur toutes les notes contenues � la racine ou dans les sous-conteneurs
        // sans tenir compte des options d�finies dans cette(ces) bo�te(s).

        // on s'int�resse � tous les conteneurs fils, petit-fils, ...
        sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all');
        //
        // On fait la moyenne des devoirs du conteneur et des sous-conteneurs
        $nb_boucle = $nb_sous_cont+1;
        $id_cont[0] = $id_conteneur;
        $i=1;
        while ($i < $nb_boucle) {
            $id_cont[$i] = $id_sous_cont[$i-1];
            $i++;
        }

    } else {
        //la moyenne s'effectue sur toutes les notes contenues � la racine du conteneur
        //et sur les moyennes du ou des sous-conteneurs, en tenant compte des options dans ce(s) bo�te(s).

        // On s'int�resse uniquement aux conteneurs fils
        sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'');
        //
        // on ne fait la moyenne que des devoirs du conteneur
        $nb_boucle = 1;
        $id_cont[0] = $id_conteneur;

    }



    //
    // Prise en compte de la pond�ration
    // Calcul de l'indice du coefficient � pond�rer
    //
    if ($ponderation != 0) {
        $sql="SELECT * FROM cn_devoirs WHERE id_conteneur='$id_conteneur' ORDER BY date,id";
		fdebug("$sql\n");
        $appel_dev = mysql_query($sql);
        $nb_dev  = mysql_num_rows($appel_dev);
		fdebug("\$nb_dev=$nb_dev\n");
        $max = 0;
        $indice_pond = 0;
        $k = 0;
        while ($k < $nb_dev) {
            $id_dev = mysql_result($appel_dev, $k, 'id');
            $coef[$k] = mysql_result($appel_dev, $k, 'coef');
			fdebug("\$id_dev=$id_dev : \$coef[$k]=$coef[$k]\n");
            $sql="SELECT * FROM cn_notes_devoirs WHERE (login='$login' AND id_devoir='$id_dev')";
			fdebug("$sql\n");
            $note_query = mysql_query($sql);
            $statut = @mysql_result($note_query, 0, "statut");
            $note = @mysql_result($note_query, 0, "note");
			fdebug("\$nb_dev=$nb_dev\n");
            if (($statut == '') and ($note!='')) {
                if (($note > $max) or (($note == $max) and ($coef[$k] > $coef[$indice_pond]))) {
                    $max = $note;
                    $indice_pond = $k;
                }
            }
            $k++;
        }
    }
	if(isset($indice_pond)) {
		fdebug("\$indice_pond=$indice_pond\n");
		fdebug("\$max=$max\n");
	}


    //
    // Calcul du total des points et de la somme des coefficients
    //
/**
 * @todo Pour $mode==1, pour les devoirs � Bonus, il faudrait faire la liste de tous les devoirs situ�s dans le conteneur et les sous-conteneurs tri�s par date et parcourir ici ces devoirs au lieu de faire une boucle sur la liste des sous-conteneurs (while ($j < $nb_boucle))
 */
    $j=0;
	//=========================
	// AJOUT: boireaus 20080202
	$m=0;
	//=========================
    while ($j < $nb_boucle) {
		//=========================
		// MODIF: boireaus 20080202
        $sql="SELECT * FROM cn_devoirs WHERE id_conteneur='$id_cont[$j]' ORDER BY date,id";
		fdebug("$sql\n");
        $appel_dev = mysql_query($sql);
		//=========================
        $nb_dev  = mysql_num_rows($appel_dev);
        $k = 0;
        while ($k < $nb_dev) {
            $id_dev = mysql_result($appel_dev, $k, 'id');
			fdebug("\n\$id_dev=$id_dev\n");

            $coef[$k] = mysql_result($appel_dev, $k, 'coef');
			fdebug("\$coef[$k]=$coef[$k]\n");

            $note_sur[$k] = mysql_result($appel_dev, $k, 'note_sur');
			fdebug("\$note_sur[$k]=$note_sur[$k]\n");

            $ramener_sur_referentiel[$k] = mysql_result($appel_dev, $k, 'ramener_sur_referentiel');
			fdebug("\$ramener_sur_referentiel[$k]=$ramener_sur_referentiel[$k]\n");

            // Prise en compte de la pond�ration
            if (($ponderation != 0) and ($j==0) and ($k==$indice_pond)) $coef[$k] = $coef[$k] + $ponderation;
			fdebug("\$ponderation=$ponderation\n");

            $facultatif[$k] = mysql_result($appel_dev, $k, 'facultatif');
			fdebug("\$facultatif[$k]=$facultatif[$k]\n");

            $note_query = mysql_query("SELECT * FROM cn_notes_devoirs WHERE (login='$login' AND id_devoir='$id_dev')");
            $statut = @mysql_result($note_query, 0, "statut");
			fdebug("\$statut=$statut\n");

            $note = @mysql_result($note_query, 0, "note");
			fdebug("\$note=$note\n");

            if (($statut == '') and ($note!='')) {
                if ($note_sur[$k] != getSettingValue("referentiel_note")) {
                    if ($ramener_sur_referentiel[$k] != 'V') {
                        //on ramene la note sur le referentiel mais on modifie le coefficient pour prendre en compte le r�f�rentiel
                        $note = $note * getSettingValue("referentiel_note") / $note_sur[$k];
                        $coef[$k] = $coef[$k] * $note_sur[$k] / getSettingValue("referentiel_note");
                    } else {
                        //on fait comme si c'�tait une note sur le referentiel avec une regle de trois ;)
                        $note = $note * getSettingValue("referentiel_note") / $note_sur[$k];
                    }
                }
                fdebug("Correction note autre que sur referentiel : \$note=$note\n");
                fdebug("Correction note autre que sur referentiel : \$coef[$k]=$coef[$k]\n");

                if ($facultatif[$k] == 'O') {
                    // le devoir n'est pas facultatif (Obligatoire) et entre syst�matiquement dans le calcul de la moyenne si le coef est diff�rent de z�ro
					fdebug("\$total_point = $total_point + $coef[$k] * $note = ");
                    $total_point = $total_point + $coef[$k]*$note;
					fdebug("$total_point\n");

					fdebug("\$somme_coef = $somme_coef + $coef[$k] = ");
                    $somme_coef = $somme_coef + $coef[$k];
					fdebug("$somme_coef\n");
                } else if ($facultatif[$k] == 'B') {
                    //le devoir est facultatif comme un bonus : seuls les points sup�rieurs � 10 sont pris en compte dans le calcul de la moyenne.
                    if ($note > ($note_sur[$k]/2)) {
                        $total_point = $total_point + $coef[$k]*$note;
                        $somme_coef = $somme_coef + $coef[$k];
                    }
                } else {
                    //$facultatif == 'N' le devoir est facultatif comme une note : Le devoir est pris en compte dans la moyenne uniquement s'il am�liore la moyenne de l'�l�ve.
                    $exist_dev_fac = 'yes';
					//=========================
					// MODIF: boireaus 20080202
					// On ne compte pas la note dans la moyenne pour le moment.
					// On regardera plus loin si cela am�liore la moyenne ou non.
					$f_coef[$m]=$coef[$k];
					$points[$m] = $f_coef[$m]*$note;

					fdebug("\$points[$m]=$points[$m]\n");
					fdebug("\$f_coef[$m]=$f_coef[$m]\n");

					$m++;
					//=========================
                }
				fdebug("\$total_point=$total_point\n");
				fdebug("\$somme_coef=$somme_coef\n");
				
            }
            $k++;
        }
        $j++;
    }


    //
    // Prise en comptes des sous-conteneurs si mode=2
    //
    if ($mode == 2) {
		fdebug("\$mode=$mode\n");
        $j=0;
        while ($j < $nb_sous_cont) {
            $sql="SELECT coef FROM cn_conteneurs WHERE id='$id_sous_cont[$j]'";
			fdebug("$sql\n");
            $appel_cont = mysql_query($sql);
            $coefficient = mysql_result($appel_cont, 0, 'coef');
			fdebug("\$coefficient=$coefficient\n");

            $sql="SELECT * FROM cn_notes_conteneurs WHERE (login='$login' AND id_conteneur='$id_sous_cont[$j]')";
			fdebug("$sql\n");
            $moyenne_query = mysql_query($sql);
            $statut_moy = @mysql_result($moyenne_query, 0, "statut");
			fdebug("\$statut_moy=$statut_moy\n");

            if ($statut_moy == 'y') {
                $moy = @mysql_result($moyenne_query, 0, "note");
				fdebug("\$moy=$moy\n");

				fdebug("\$somme_coef = $somme_coef + $coefficient = ");
                $somme_coef = $somme_coef + $coefficient;
				fdebug("$somme_coef\n");

				fdebug("\$total_point = $total_point + $coefficient * $moy = ");
                $total_point = $total_point + $coefficient*$moy;
				fdebug("$total_point\n");
            }
            $j++;
        }
    }


    //
    // calcul de la moyenne des �valuations
    //
	//=========================
	// A FAIRE: boireaus 20080202
/**
 * @todo Il faudrait consid�rer le cas vicieux: pr�sence de note � bonus et pas d'autre note...
 */

    if ($somme_coef != 0) {
	//=========================
		fdebug("\$moyenne= = $total_point / $somme_coef = ");
        $moyenne = $total_point/$somme_coef;
		fdebug($moyenne."\n");
        //
        // si un des devoirs a l'option "N", on prend la meilleure moyenne :
        //
		// Ca ne fonctionne bien que pour $mode==2
/**
 * @todo  Pour $mode==1, il faudrait faire la liste de tous les devoirs situ�s dans le conteneur et les sous-conteneurs tri�s par date et parcourir ces devoirs plus haut au lieu de faire une boucle sur la liste des sous-conteneurs
 */
        if ($exist_dev_fac == 'yes') {
			fdebug("\$exist_dev_fac=".$exist_dev_fac."\n");
			
			$m=0;
            while ($m<count($points)) {
				fdebug("count(\$points)=".count($points)."\n");
				if((isset($points[$m]))&&(isset($f_coef[$m]))) {
					fdebug("\$points[$m]=$points[$m] et \$f_coef[$m]=$f_coef[$m]\n");
					$tmp_moy=($total_point+$points[$m])/($somme_coef+$f_coef[$m]);
					fdebug("\$tmp_moy=$tmp_moy et \$moyenne=$moyenne\n");
					if($tmp_moy>$moyenne){
						$moyenne=$tmp_moy;
						$total_point=$total_point+$points[$m];
						$somme_coef=$somme_coef+$f_coef[$m];
					}
					fdebug("\$moyenne=$moyenne\n");
				}
				$m++;
			}
        }

		fdebug("Moyenne avant arrondi: $moyenne\n");

        //
        // Calcul des arrondis
        //
        if ($arrondir == 's1') {
            // s1 : arrondir au dixi�me de point sup�rieur
			fdebug("Mode s1:
   \$moyenne=$moyenne
   10*\$moyenne=".(10*$moyenne)."
   ceil(10*\$moyenne)=".ceil(10*$moyenne)."
   ceil(10*\$moyenne)/10=".(ceil(10*$moyenne)/10)."
   number_format(ceil(10*\$moyenne)/10,1,'.','')=".number_format(ceil(10*$moyenne)/10,1,'.','')."
   number_format(ceil(100*\$moyenne)/100,1,'.','')=".number_format(ceil(100*$moyenne)/100,1,'.','')."\n");
            //$moyenne = number_format(ceil(10*$moyenne)/10,1,'.','');
			$moyenne = number_format(ceil(strval(10*$moyenne))/10,1,'.','');
        } else if ($arrondir == 's5') {
            // s5 : arrondir au demi-point sup�rieur
            $moyenne = number_format(ceil(strval(2*$moyenne))/2,1,'.','');
        } else if ($arrondir == 'se') {
            // se : arrondir au point entier sup�rieur
            $moyenne = number_format(ceil(strval($moyenne)),1,'.','');
        } else if ($arrondir == 'p1') {
            // s1 : arrondir au dixi�me le plus proche
            $moyenne = number_format(round(strval(10*$moyenne))/10,1,'.','');
        } else if ($arrondir == 'p5') {
            // s5 : arrondir au demi-point le plus proche
            $moyenne = number_format(round(strval(2*$moyenne))/2,1,'.','');
        } else if ($arrondir == 'pe') {
            // se : arrondir au point entier le plus proche
            $moyenne = number_format(round(strval($moyenne)),1,'.','');
        }

        $sql="INSERT INTO cn_notes_conteneurs SET login='$login', id_conteneur='$id_conteneur',note='$moyenne',statut='y',comment='';";
		fdebug("$sql\n");
        $register = mysql_query($sql);

    } else {
        $sql="INSERT INTO cn_notes_conteneurs SET login='$login', id_conteneur='$id_conteneur',note='0',statut='',comment='';";
		fdebug("$sql\n");
        $register = mysql_query($sql);

    }

}

/**
 * V�rifie qu'un carnet de notes appartient bien � enseignant
 *
 * @param text $_login Login de l'enseignant
 * @param int $_id_racine Id du carnet de notes
 * @return bool TRUE si l'enseignant peut acc�der au carnet de notes
 */
function Verif_prof_cahier_notes ($_login,$_id_racine) {
    if(empty($_login) || empty($_id_racine)) {return FALSE;die();}
    $test_prof = mysql_query("SELECT id_groupe FROM cn_cahier_notes WHERE id_cahier_notes ='" . $_id_racine . "'");
    $_id_groupe = mysql_result($test_prof, 0, 'id_groupe');

    $call_prof = mysql_query("SELECT login FROM j_groupes_professeurs WHERE (id_groupe='".$_id_groupe."' and login='" . $_login . "')");
    $nb = mysql_num_rows($call_prof);

    if ($nb != 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Ajoute du code pour pr�parer un tableau pour calculer et afficher les statistiques sur les notes d'une classe 
 * 
 * Fonction � appeler avec une portion de code du type:
 * 
 * echo "<div style='position: fixed; top: 200px; right: 200px;'>\n";
 * 
 * javascript_tab_stat('tab_stat_',$cpt);
 * 
 * echo "</div>\n";
 * 
 * @param string $pref_id prefixe des Id des balises <td...>
 * @param int $cpt 
 */
function javascript_tab_stat($pref_id,$cpt) {
	
	$alt=1;
	echo "<table class='boireaus'>\n";
	echo "<caption style='display:none;'>Statistiques</caption>";
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>Moyenne</th>\n";
	echo "<td id='".$pref_id."moyenne'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>1er quartile</th>\n";
	echo "<td id='".$pref_id."q1'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>M�diane</th>\n";
	echo "<td id='".$pref_id."mediane'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>3� quartile</th>\n";
	echo "<td id='".$pref_id."q3'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>Min</th>\n";
	echo "<td id='".$pref_id."min'></td>\n";
	echo "</tr>\n";

	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<th>Max</th>\n";
	echo "<td id='".$pref_id."max'></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<script type='text/javascript' language='JavaScript'>

function calcul_moy_med() {
	var eff_utile=0;
	var total=0;
	var valeur;
	var tab_valeur=new Array();
	var i=0;
	var j=0;
	var n=0;
	var mediane;
	var moyenne;
	var q1;
	var q3;
	var rang=0;

	for(i=0;i<$cpt;i++) {
		if(document.getElementById('n'+i)) {
			valeur=document.getElementById('n'+i).value;

			valeur=valeur.replace(',','.');

			if((valeur!='abs')&&(valeur!='disp')&&(valeur!='-')&&(valeur!='')) {
				tab_valeur[j]=valeur;
				// Tambouille pour �viter que 'valeur' soit pris pour une chaine de caract�res
				total=eval((total*100+valeur*100)/100);
				eff_utile++;
				j++;
			}
		}
	}
	if(eff_utile>0) {
		moyenne=Math.round(10*total/eff_utile)/10;
		document.getElementById('".$pref_id."moyenne').innerHTML=moyenne;

		tab_valeur.sort((function(a,b){return a - b}));
		n=tab_valeur.length;
		if(n/2==Math.round(n/2)) {
			// Les indices commencent � z�ro
			// Tambouille pour �viter que 'valeur' soit pris pour une chaine de caract�res
			mediane=((eval(100*tab_valeur[n/2-1]+100*tab_valeur[n/2]))/100)/2;
		}
		else {
			mediane=tab_valeur[(n-1)/2];
		}
		document.getElementById('".$pref_id."mediane').innerHTML=mediane;

		if(eff_utile>=4) {
			rang=Math.ceil(eff_utile/4);
			q1=tab_valeur[rang-1];

			rang=Math.ceil(3*eff_utile/4);
			q3=tab_valeur[rang-1];

			document.getElementById('".$pref_id."q1').innerHTML=q1;
			document.getElementById('".$pref_id."q3').innerHTML=q3;
		}
		else {
			document.getElementById('".$pref_id."q1').innerHTML='-';
			document.getElementById('".$pref_id."q3').innerHTML='-';
		}

		document.getElementById('".$pref_id."min').innerHTML=tab_valeur[0];
		document.getElementById('".$pref_id."max').innerHTML=tab_valeur[n-1];
	}
	else {
		document.getElementById('".$pref_id."moyenne').innerHTML='-';
		document.getElementById('".$pref_id."mediane').innerHTML='-';
		document.getElementById('".$pref_id."q1').innerHTML='-';
		document.getElementById('".$pref_id."q3').innerHTML='-';
		document.getElementById('".$pref_id."min').innerHTML='-';
		document.getElementById('".$pref_id."max').innerHTML='-';
	}
}

calcul_moy_med();
</script>
";
}

/**
 * Calcule les statistiques d'un tableau de notes
 * 
 * - 'moyenne' -> moyenne des notes
 * - 'mediane' -> mediane des notes
 * - 'min'     -> note minimale
 * - 'max'     -> note maximale
 * - 'q1'      -> premier quartile
 * - 'q3'      -> troisi�me quartile
 *
 * @param array $tab Tableau de notes � traiter
 * @return array Tableau de statistiques
 */
function calcule_moy_mediane_quartiles($tab) {
	$tab2=array();

	$total=0;
	for($i=0;$i<count($tab);$i++) {
		if(($tab[$i]!='')&&($tab[$i]!='-')&&($tab[$i]!='&nbsp;')&&($tab[$i]!='abs')&&($tab[$i]!='disp')) {
			$tab2[]=preg_replace('/,/','.',$tab[$i]);
			$total+=preg_replace('/,/','.',$tab[$i]);
		}
	}

	// Initialisation
	$tab_retour['moyenne']='-';
	$tab_retour['mediane']='-';
	$tab_retour['min']='-';
	$tab_retour['max']='-';
	$tab_retour['q1']='-';
	$tab_retour['q3']='-';

	if(count($tab2)>0) {
		sort($tab2);

		$moyenne=round(10*$total/count($tab2))/10;

		if(count($tab2)%2==0) {
			$mediane=($tab2[count($tab2)/2-1]+$tab2[count($tab2)/2])/2;
		}
		else {
			$mediane=$tab2[(count($tab2)-1)/2];
		}

		$min=min($tab2);
		$max=max($tab2);

		if(count($tab2)>=4) {
			$q1=$tab2[ceil(count($tab2)/4)-1];
			$q3=$tab2[ceil(3*count($tab2)/4)-1];
		}

		$tab_retour['moyenne']=$moyenne;
		$tab_retour['mediane']=$mediane;
		$tab_retour['min']=$min;
		$tab_retour['max']=$max;
		$tab_retour['q1']=$q1;
		$tab_retour['q3']=$q3;
	}

	return $tab_retour;
}

/**
 * Renvoie l'Id du carnet de notes d'un groupe pour une p�riode
 *
 * @param int $id_groupe Id du groupe
 * @param int $periode_num num�ro de la p�riode
 * @return int Id du carnet de notes
 */
function get_cn_from_id_groupe_periode_num($id_groupe, $periode_num) {
	$id_cahier_notes="";

	$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='$id_groupe' AND periode='$periode_num';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		$id_cahier_notes=$lig->id_cahier_notes;
	}
	return $id_cahier_notes;
}


// Fonction de recherche des conteneurs derniers enfants (sans enfants (non parents, en somme))
// avec recalcul des moyennes lanc�...
/**
 *
 * @global int
 * @global int
 * @global int
 * @param int $id_parent_tmp 
 */
function recherche_enfant($id_parent_tmp){
	global $current_group, $periode_num, $id_racine;
	$sql="SELECT * FROM cn_conteneurs WHERE parent='$id_parent_tmp'";
	//echo "<!-- $sql -->\n";
	$res_enfant=mysql_query($sql);
	if(mysql_num_rows($res_enfant)>0){
		while($lig_conteneur_enfant=mysql_fetch_object($res_enfant)){
			recherche_enfant($lig_conteneur_enfant->id);
		}
	}
	else{
		$arret = 'no';
		$id_conteneur_enfant=$id_parent_tmp;
		// Mise_a_jour_moyennes_conteneurs pour un enfant non parent...
		mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur_enfant,$arret);
	}
}





?>
