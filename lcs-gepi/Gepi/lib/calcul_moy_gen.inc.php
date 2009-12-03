<?php
/*
 * $Id: calcul_moy_gen.inc.php 3077 2009-04-29 10:05:50Z crob $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * Portion de code qui calcule des tableaux suivants
     $moy_gen_classe =  tableau des moyennes g�n�rales de la classe
     $moy_gen_eleve =  tableau des moyennes g�n�rales d'�l�ves
   le script � besoin de :
     - $id_classe : la classe concern�e
     - $periode_num : la p�riode concern�e
*/

/*
function calc_moy_debug($texte){
	// Passer � 1 la variable pour g�n�rer un fichier de debug...
	$debug=0;
	if($debug==1){
		$fich=fopen("/tmp/calc_moy_debug.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}
*/

$quartile1_classe_gen = 0;
$quartile2_classe_gen = 0;
$quartile3_classe_gen = 0;
$quartile4_classe_gen = 0;
$quartile5_classe_gen = 0;
$quartile6_classe_gen = 0;

$quartile1_grp=array();
$quartile2_grp=array();
$quartile3_grp=array();
$quartile4_grp=array();
$quartile5_grp=array();
$quartile6_grp=array();

// On appelle la liste des �l�ves de la classe
/*
$appel_liste_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c
    WHERE (
    e.login = c.login and
    c.id_classe = '".$id_classe."' and
    c.periode='".$periode_num."'
    )
    ORDER BY e.nom, e.prenom");
*/
$sql="SELECT e.* FROM eleves e, j_eleves_classes c
    WHERE (
    e.login = c.login and
    c.id_classe = '".$id_classe."' and
    c.periode='".$periode_num."'
    )
    ORDER BY e.nom, e.prenom";
$appel_liste_eleves = mysql_query($sql);
calc_moy_debug($sql."\n");
//echo "$sql<br />";
$nombre_eleves = mysql_num_rows($appel_liste_eleves);
calc_moy_debug("\$nombre_eleves=$nombre_eleves\n");


// On appelle la liste des mati�res de la classe
if ($affiche_categories) {
    calc_moy_debug("\$affiche_categories=$affiche_categories\n");
	// On utilise les valeurs sp�cifi�es pour la classe en question
	/*
	$appel_liste_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
	"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
	"WHERE ( " .
	"jgc.categorie_id = jmcc.categorie_id AND " .
	"jgc.id_classe='".$id_classe."' AND " .
	"jgm.id_groupe=jgc.id_groupe AND " .
	"m.matiere = jgm.id_matiere" .
	") " .
	"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
	*/
	$sql="SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id, jgc.mode_moy ".
	"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m, matieres_categories mc " .
	"WHERE ( " .
	"mc.id=jmcc.categorie_id AND ".
	"jgc.categorie_id = jmcc.categorie_id AND " .
	"jgc.id_classe=jmcc.classe_id AND " .
	"jgc.id_classe='".$id_classe."' AND " .
	"jgm.id_groupe=jgc.id_groupe AND " .
	"m.matiere = jgm.id_matiere" .
	") " .
	"ORDER BY jmcc.priority,mc.priority,jgc.priorite,m.nom_complet";
	calc_moy_debug($sql."\n");
	$appel_liste_groupes = mysql_query($sql);
} else {
    calc_moy_debug("\$affiche_categories=\n");
	/*
	$appel_liste_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef
	FROM j_groupes_classes jgc, j_groupes_matieres jgm
	WHERE (
	jgc.id_classe='".$id_classe."' AND
	jgm.id_groupe=jgc.id_groupe
	)
	ORDER BY jgc.priorite,jgm.id_matiere");
	*/
	$sql="SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.mode_moy
	FROM j_groupes_classes jgc, j_groupes_matieres jgm
	WHERE (
	jgc.id_classe='".$id_classe."' AND
	jgm.id_groupe=jgc.id_groupe
	)
	ORDER BY jgc.priorite,jgm.id_matiere";
	calc_moy_debug($sql."\n");
	$appel_liste_groupes = mysql_query($sql);
}

//echo "$sql<br />";

$nombre_groupes = mysql_num_rows($appel_liste_groupes);
calc_moy_debug("\$nombre_groupes=$nombre_groupes\n");

// Initialisation des tableaux li�s aux calculs des moyennes g�n�rales
$current_group = array();
$current_coef = array();
$current_mode_moy = array();

//======================================
// Ajout: boireaus 20080408
$current_coef_eleve=array();
//======================================

$moy_gen_classe = array();
$moy_gen_eleve = array();
$moy_cat_eleve = array();
$moy_cat_classe = array();
$moy_cat_min = array();
$moy_cat_max = array();
//$total_coef = array();
$total_coef_classe = array();
$total_coef_eleve = array();

//$total_coef_cat = array();
$total_coef_cat_classe = array();
$total_coef_cat_eleve = array();

$i=0;
//$get_cat = mysql_query("SELECT id FROM matieres_categories");
$get_cat = mysql_query("SELECT id,nom_complet FROM matieres_categories");
$categories = array();
$tab_noms_categories = array();
$tab_id_categories = array();
while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
	$categories[] = $row["id"];
	$tab_noms_categories[$row["id"]]=$row["nom_complet"];
	$tab_id_categories[$row["nom_complet"]]=$row["id"];

	//echo "\$tab_noms_categories[".$row["id"]."]=".$row["nom_complet"]."<br />";
	//echo "\$tab_id_categories[".$row["nom_complet"]."]=".$row["id"]."<br />";
}

//echo "\$nombre_eleves=$nombre_eleves<br />";
while ($i < $nombre_eleves) {
    //$total_coef[$i] = 0;
    $total_coef_classe[$i] = 0;
    $total_coef_eleve[$i] = 0;

	//======================================
	// Ajout: boireaus 20080408
	$current_coef_eleve[$i]=array();
	//======================================

    $moy_gen_eleve[$i] = 0;
    $moy_gen_classe[$i] = 0;
    $moy_cat_classe[$i] = array();
    $moy_cat_min[$i] = array();
    $moy_cat_max[$i] = array();
    $moy_cat_eleve[$i] = array();

    //$total_coef_cat[$i] = array();
    $total_coef_cat_classe[$i] = array();
    $total_coef_cat_eleve[$i] = array();
	foreach($categories as $cat_id) {
    	$moy_cat_eleve[$i][$cat_id] = 0;
    	//$total_coef_cat[$i][$cat_id] = 0;
    	$total_coef_cat_classe[$i][$cat_id] = 0;
    	$total_coef_cat_eleve[$i][$cat_id] = 0;
    	$moy_cat_classe[$i][$cat_id] = 0;

    	$moy_cat_min[$i][$cat_id] = "-";
    	$moy_cat_max[$i][$cat_id] = "-";
    }

	//=================================
	// Pour le cas o� une mati�re est saisie en "Aucune" cat�gorie dans
	// Gestion des bases/Gestion des classes/<classe> Enseignements
	// La cat�gorie "Aucune" n'existe pas dans 'matieres_categories'
	$moy_cat_eleve[$i][0] = 0;
	$total_coef_cat_classe[$i][0] = 0;
	$total_coef_cat_eleve[$i][0] = 0;
	$moy_cat_classe[$i][0] = 0;

	$moy_cat_min[$i][0] = "-";
	$moy_cat_max[$i][0] = "-";
	//=================================

	// Temoin que la moyenne g�n�rale de l'�l�ve peut avoir une signification
	$temoin_au_moins_une_matiere_avec_note[$i]="n";

    $i++;
}

// Pour d�bugger:
$lignes_debug="";
$ele_login_debug="FOULON_A";

// Pr�paration des donn�es
$j=0;
$prev_cat = null;
while ($j < $nombre_groupes) {
    $group_id = mysql_result($appel_liste_groupes, $j, "id_groupe");
    $current_group[$j] = get_group($group_id);

	calc_moy_debug("\$current_group[$j]['name']=".$current_group[$j]['name']."\n");
	calc_moy_debug("\$current_group[$j]['matiere']['matiere']=".$current_group[$j]['matiere']['matiere']."\n");

	$current_coef[$j] = mysql_result($appel_liste_groupes, $j, "coef");
	calc_moy_debug("\$current_coef[$j]=mysql_result(\$appel_liste_groupes, $j, \"coef\")=$current_coef[$j]\n");

	if(isset($coefficients_a_1)){
		if($coefficients_a_1=="oui"){
			$current_coef[$j]=1;
		}
	}
	calc_moy_debug("\$current_coef[$j]=$current_coef[$j]\n");

	$current_mode_moy[$j]=mysql_result($appel_liste_groupes, $j, "mode_moy");
	calc_moy_debug("\$current_mode_moy[$j]=mysql_result(\$appel_liste_groupes, $j, \"mode_moy\")=$current_mode_moy[$j]\n");

    if ($current_group[$j]["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat) {
    	$prev_cat = $current_group[$j]["classes"]["classes"][$id_classe]["categorie_id"];
    }

    // Moyenne de la classe dans la mati�re $current_matiere[$j]
    $sql="SELECT round(avg(note),1) moyenne
        FROM matieres_notes
        WHERE (
        statut ='' AND
        id_groupe='".$current_group[$j]["id"]."' AND
        periode='$periode_num'
        )
        ";
	calc_moy_debug("$sql\n");
	$current_classe_matiere_moyenne_query = mysql_query($sql);

	$current_classe_matiere_moyenne[$j] = mysql_result($current_classe_matiere_moyenne_query, 0, "moyenne");
	calc_moy_debug("\$current_classe_matiere_moyenne[$j]=$current_classe_matiere_moyenne[$j]\n");

	//===================================
	// Effectif du groupe pour le rang:
    $sql="SELECT 1=1
        FROM matieres_notes
        WHERE (
        statut ='' AND
        id_groupe='".$current_group[$j]["id"]."' AND
        periode='$periode_num'
        )
        ";
	calc_moy_debug("$sql\n");
	$req_current_group_effectif_avec_note = mysql_query($sql);
	$current_group_effectif_avec_note[$j] = mysql_num_rows($req_current_group_effectif_avec_note);
	//===================================

    // Calcul de la moyenne des �l�ves et de la moyenne de la classe pour l'enseignement courant ($j)
    $i=0;

	$sql="SELECT MIN(note) note_min, MAX(note) note_max FROM matieres_notes
		WHERE (
		periode='$periode_num' AND
		id_groupe='".$current_group[$j]["id"]."' AND
		statut=''
		)";
	$res_note_min_max=mysql_query($sql);
	$moy_min_classe_grp[$j]= @mysql_result($res_note_min_max, 0, "note_min");
	$moy_max_classe_grp[$j]= @mysql_result($res_note_min_max, 0, "note_max");
	//======================================

	$sql="SELECT COUNT(note) as quartile1 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=15)";
	//echo "$sql<br />";
	$quartile1_grp[$j]=sql_query1($sql);
	$sql="SELECT COUNT(note) as quartile2 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=12 AND
								note<15)";
	//echo "$sql<br />";
	$quartile2_grp[$j]=sql_query1($sql);
	$sql="SELECT COUNT(note) as quartile3 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=10 AND
								note<12)";
	//echo "$sql<br />";
	$quartile3_grp[$j]=sql_query1($sql);
	//echo "\$quartile3_grp[$j]=".$quartile3_grp[$j]."<br />";
	$sql="SELECT COUNT(note) as quartile4 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=8 AND
								note<10)";
	//echo "$sql<br />";
	$quartile4_grp[$j]=sql_query1($sql);
	//echo "\$quartile4_grp[$j]=".$quartile4_grp[$j]."<br />";
	$sql="SELECT COUNT(note) as quartile5 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note>=5 AND
								note<8)";
	//echo "$sql<br />";
	$quartile5_grp[$j]=sql_query1($sql);
	//echo "\$quartile5_grp[$j]=".$quartile5_grp[$j]."<br />";
	$sql="SELECT COUNT(note) as quartile6 FROM matieres_notes WHERE (periode='$periode_num' AND
								id_groupe='".$current_group[$j]["id"]."' AND
								statut='' AND
								note<5)";
	//echo "$sql<br />";
	$quartile6_grp[$j]=sql_query1($sql);
	//echo "\$quartile6_grp[$j]=".$quartile6_grp[$j]."<br />";

	//======================================
    while ($i < $nombre_eleves) {
        $current_eleve_login[$i] = mysql_result($appel_liste_eleves, $i, "login");

		if($current_eleve_login[$i]==$ele_login_debug) {$lignes_debug.="<p>\$current_group[$j]['name']=".$current_group[$j]['name']."<br />";}

		//echo "\$current_eleve_login[$i]=".$current_eleve_login[$i]."<br />";

        // Maintenant on regarde si l'�l�ve suit bien cette mati�re ou pas
        if (in_array($current_eleve_login[$i], $current_group[$j]["eleves"][$periode_num]["list"])) {
			calc_moy_debug("\$current_group[$j]['name']=".$current_group[$j]['name']."\n");

			//=====================================
			// R�cup�ration de la note et du statut
            $sql="SELECT distinct * FROM matieres_notes
            WHERE (
            login='".$current_eleve_login[$i]."' AND
            periode='$periode_num' AND
            id_groupe='".$current_group[$j]["id"]."'
            )";
			//if($current_eleve_login[$i]=="ALLAIX_N") {echo "$sql<br />";}
			calc_moy_debug("$sql\n");
			//echo "$sql<br />";
			$current_eleve_note_query = mysql_query($sql);

			//echo "\$current_group[$j]['name']=".$current_group[$j]['name']."<br />";
			if(mysql_num_rows($current_eleve_note_query)>0) {
				$lig_tmp=mysql_fetch_object($current_eleve_note_query);
				//$current_eleve_note[$j][$i] = @mysql_result($current_eleve_note_query, 0, "note");
				$current_eleve_note[$j][$i]=$lig_tmp->note;
				calc_moy_debug("\$current_eleve_note[$j][$i]=".$current_eleve_note[$j][$i]."\n");

				//$current_eleve_statut[$j][$i] = @mysql_result($current_eleve_note_query, 0, "statut");
				$current_eleve_statut[$j][$i]=$lig_tmp->statut;
				calc_moy_debug("\$current_eleve_statut[$j][$i]=".$current_eleve_statut[$j][$i]."\n");

				//echo "\$current_eleve_note[$j][$i]=".$current_eleve_note[$j][$i]."<br />";
				//echo "\$current_eleve_statut[$j][$i]=".$current_eleve_statut[$j][$i]."<br />";
			}
			else {
				$current_eleve_note[$j][$i]="-";
				$current_eleve_statut[$j][$i]="";
			}

			if($current_eleve_login[$i]==$ele_login_debug) {
				$lignes_debug.="\$current_eleve_note[$j][$i]=".$current_eleve_note[$j][$i]."<br />";
				$lignes_debug.="\$current_eleve_statut[$j][$i]=".$current_eleve_statut[$j][$i]."<br />";
			}
			//=====================================

			//=====================================
            // On teste si l'�l�ve a un coef sp�cifique pour cette mati�re
			calc_moy_debug("\$coefficients_a_1=$coefficients_a_1\n");
			if((isset($coefficients_a_1))&&($coefficients_a_1=="oui")) {
				$coef_eleve=1;
			}
			else{
				$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
						"login = '".$current_eleve_login[$i]."' AND " .
						"id_groupe = '".$current_group[$j]["id"]."' AND " .
						"name = 'coef')";
				//echo "$sql<br />";
				calc_moy_debug("$sql\n");
				$test_coef = mysql_query($sql);
				if (mysql_num_rows($test_coef) > 0) {
					$coef_eleve = mysql_result($test_coef, 0);
				} else {
					$coef_eleve = $current_coef[$j];
				}
			}

			if($current_mode_moy[$j]=='sup10') {
				if(($current_eleve_note[$j][$i]!="")&&($current_eleve_note[$j][$i]!="-")&&($current_eleve_note[$j][$i]<10)) {
					$coef_eleve=0;
				}
			}

			$current_coef_eleve[$i][$j]=$coef_eleve;
			if($current_eleve_login[$i]==$ele_login_debug) {$lignes_debug.="\$current_coef_eleve[$i][$j]=".$current_coef_eleve[$i][$j]."<br />";}
			//=====================================

			//=====================================
			if ((isset($affiche_rang))&&($affiche_rang=='y')) {
				$current_eleve_rang[$j][$i] = @mysql_result($current_eleve_note_query, 0, "rang");
				if(($current_eleve_rang[$j][$i]==0)||($current_eleve_rang[$j][$i]=="-1")) {$current_eleve_rang[$j][$i]="-";}
			}
			//=====================================

			//=====================================
			if (($current_eleve_note[$j][$i] != '') and ($current_eleve_statut[$j][$i] == '')) {
				if($current_eleve_note[$j][$i]>=15) {
					$place_eleve_grp[$j][$i]=1;
				}
				elseif(($current_eleve_note[$j][$i]>=12)&&($current_eleve_note[$j][$i]<15)) {
					$place_eleve_grp[$j][$i]=2;
				}
				elseif(($current_eleve_note[$j][$i]>=10)&&($current_eleve_note[$j][$i]<12)) {
					$place_eleve_grp[$j][$i]=3;
				}
				elseif(($current_eleve_note[$j][$i]>=8)&&($current_eleve_note[$j][$i]<12)) {
					$place_eleve_grp[$j][$i]=4;
				}
				elseif(($current_eleve_note[$j][$i]>=5)&&($current_eleve_note[$j][$i]<8)) {
					$place_eleve_grp[$j][$i]=5;
				}
				elseif($current_eleve_note[$j][$i]<5) {
					$place_eleve_grp[$j][$i]=6;
				}
			}
			//=====================================

			calc_moy_debug("\$coef_eleve=$coef_eleve\n");
			calc_moy_debug("\$current_eleve_note[$j][$i]=".$current_eleve_note[$j][$i]."\n");
			calc_moy_debug("\$current_eleve_statut[$j][$i]=".$current_eleve_statut[$j][$i]."\n");

			//=====================================
            if ($coef_eleve != 0) {
               //if (($current_eleve_note[$j][$i] != '') and ($current_eleve_statut[$j][$i] == '')) {
               if (($current_eleve_note[$j][$i] != '') and ($current_eleve_note[$j][$i] != '-') and ($current_eleve_statut[$j][$i] == '')) {

					// Temoin que la moyenne g�n�rale de l'�l�ve peut avoir une signification
					if($coef_eleve!=0) {$temoin_au_moins_une_matiere_avec_note[$i]="y";}

					$total_coef_eleve[$i] += $coef_eleve;
					calc_moy_debug("\$total_coef_eleve[$i]=$total_coef_eleve[$i]\n");

                    $total_coef_cat_eleve[$i][$prev_cat] += $coef_eleve;
					calc_moy_debug("\$total_coef_cat_eleve[$i][$prev_cat]=".$total_coef_cat_eleve[$i][$prev_cat]."\n");

                    $moy_gen_eleve[$i] += $coef_eleve*$current_eleve_note[$j][$i];
					calc_moy_debug("\$moy_gen_eleve[$i]=$moy_gen_eleve[$i]\n");

                    $moy_cat_eleve[$i][$prev_cat] += $coef_eleve*$current_eleve_note[$j][$i];
					calc_moy_debug("\$moy_cat_eleve[$i][$prev_cat]=".$moy_cat_eleve[$i][$prev_cat]."\n");

					if($current_eleve_login[$i]==$ele_login_debug) {
						$lignes_debug.="\$total_coef_cat_eleve[$i][$prev_cat]=".$total_coef_cat_eleve[$i][$prev_cat]."<br />";
						$lignes_debug.="\$moy_gen_eleve[$i]=".$moy_gen_eleve[$i]."<br />";
						$lignes_debug.="\$moy_cat_eleve[$i][$prev_cat]=".$moy_cat_eleve[$i][$prev_cat]."<br />";
					}

                }
            }
			//=====================================

			//=====================================
			// Il ne faut pas augmenter si il n'y a aucune note dans la mati�re $j.
			if($current_classe_matiere_moyenne[$j]!="") {
				$total_coef_classe[$i] += $current_coef[$j];
				calc_moy_debug("\$total_coef_classe[$i]=$total_coef_classe[$i]\n");

				//$moy_gen_classe[$i] += $coef_eleve*$current_classe_matiere_moyenne[$j];
				$moy_gen_classe[$i] += $current_coef[$j]*$current_classe_matiere_moyenne[$j];
				calc_moy_debug("\$moy_gen_classe[$i]=$moy_gen_classe[$i]\n");

				/*
				//$total_coef_cat[$i][$prev_cat] += $coef_eleve;
				$total_coef_cat_classe[$i][$prev_cat] += $current_coef[$j];
				calc_moy_debug("\$total_coef_cat_classe[$i][$prev_cat]=".$total_coef_cat_classe[$i][$prev_cat]."\n");

				//$moy_cat_classe[$i][$prev_cat] += $coef_eleve*$current_classe_matiere_moyenne[$j];
				$moy_cat_classe[$i][$prev_cat] += $current_coef[$j]*$current_classe_matiere_moyenne[$j];
				calc_moy_debug("\$moy_cat_classe[$i][$prev_cat]=".$moy_cat_classe[$i][$prev_cat]."\n");
				*/

				if($current_eleve_login[$i]==$ele_login_debug) {
					$lignes_debug.="\$total_coef_classe[$i]=".$total_coef_classe[$i]."<br />";
					//$lignes_debug.="\$total_coef_cat_classe[$i][$prev_cat]=".$total_coef_cat_classe[$i][$prev_cat]."<br />";
					$lignes_debug.="\$moy_gen_classe[$i]=".$moy_gen_classe[$i]."<br />";
					//$lignes_debug.="\$moy_cat_classe[$i][$prev_cat] += $current_coef[$j]*$current_classe_matiere_moyenne[$j]<br />";
					//$lignes_debug.="\$moy_cat_classe[$i][$prev_cat]=".$moy_cat_classe[$i][$prev_cat]."<br />";
				}
			}
			//=====================================

        }
        $i++;
		calc_moy_debug("==============================\n");
    }
    $j++;
}

$lignes_debug.="<p>";

$i = 0;
while ($i < $nombre_eleves) {
    //if ($total_coef[$i] != 0) {
	/*
	if($current_eleve_login[$i]=='BERTHON_G') {
		echo "\$total_coef_eleve[$i]=".$total_coef_eleve[$i]."<br />";
	}
	*/
    if ($total_coef_eleve[$i] != 0) {
        $place_eleve_classe[$i] = "";
        //$moy_gen_eleve[$i] = $moy_gen_eleve[$i]/$total_coef[$i];
		if($temoin_au_moins_une_matiere_avec_note[$i]=="y") {
	        $moy_gen_eleve[$i] = $moy_gen_eleve[$i]/$total_coef_eleve[$i];

			if($current_eleve_login[$i]==$ele_login_debug) {
				$lignes_debug.="\$moy_gen_eleve[$i]=".$moy_gen_eleve[$i]."/".$total_coef_eleve[$i]."<br />";
			}
		}
		else {
	        $moy_gen_eleve[$i]="-";
		}
		calc_moy_debug("\$moy_gen_eleve[$i]=$moy_gen_eleve[$i]\n");

		if($current_eleve_login[$i]==$ele_login_debug) {
			$lignes_debug.="\$moy_gen_eleve[$i]=".$moy_gen_eleve[$i]."<br />";
		}

		if($total_coef_classe[$i] != 0){
			//$moy_gen_classe[$i] = $moy_gen_classe[$i]/$total_coef[$i];
			if($current_eleve_login[$i]==$ele_login_debug) {
				$lignes_debug.="\$moy_gen_classe[$i]=".$moy_gen_classe[$i]."/".$total_coef_classe[$i]."<br />";
			}

			$moy_gen_classe[$i] = $moy_gen_classe[$i]/$total_coef_classe[$i];
			calc_moy_debug("\$moy_gen_classe[$i]=$moy_gen_classe[$i]\n");
			//$moy_gen_classe[$i] = number_format($moy_gen_classe[$i],1, ',', ' ');
		}
		else{
			$moy_gen_classe[$i]="-";
		}

		if($current_eleve_login[$i]==$ele_login_debug) {
			$lignes_debug.="\$moy_gen_classe[$i]=".$moy_gen_classe[$i]."<br />";
		}

        // Pr�paration des donn�es pour affichage des graphiques
        if ($affiche_graph == 'y')  {
            if ($moy_gen_eleve[$i] >= 15) {$quartile1_classe_gen++; $place_eleve_classe[$i] = 1;}
            else if (($moy_gen_eleve[$i] >= 12) and ($moy_gen_eleve[$i] < 15)) {$quartile2_classe_gen++;$place_eleve_classe[$i] = 2;}
            else if (($moy_gen_eleve[$i] >= 10) and ($moy_gen_eleve[$i] < 12)) {$quartile3_classe_gen++;$place_eleve_classe[$i] = 3;}
            else if (($moy_gen_eleve[$i] >= 8) and ($moy_gen_eleve[$i] < 10)) {$quartile4_classe_gen++;$place_eleve_classe[$i] = 4;}
            else if (($moy_gen_eleve[$i] >= 5) and ($moy_gen_eleve[$i] < 8)) {$quartile5_classe_gen++;$place_eleve_classe[$i] = 5;}
            else {$quartile6_classe_gen++;$place_eleve_classe[$i] = 6;}
        }
        // Eric d�plac� en fin de fichier dans une nouvelle boucle.
        //$moy_gen_eleve[$i] = number_format($moy_gen_eleve[$i],1, ',', ' ');
        //$moy_gen_classe[$i] = number_format($moy_gen_classe[$i],1, ',', ' ');

    } else {
        $moy_gen_eleve[$i] = "-";
        $moy_gen_classe[$i] = "-";
    }

    foreach($categories as $cat) {
		/*
	    //if ($total_coef_cat[$i][$cat] != 0) {
	    if ($total_coef_cat_classe[$i][$cat] != 0) {
			if($current_eleve_login[$i]==$ele_login_debug) {
				$lignes_debug.="\$moy_cat_classe[$i][$cat]=".$moy_cat_classe[$i][$cat]."/".$total_coef_cat_classe[$i][$cat]."<br />";
			}

	        $moy_cat_classe[$i][$cat] = $moy_cat_classe[$i][$cat]/$total_coef_cat_classe[$i][$cat];
	        $moy_cat_classe[$i][$cat] = number_format($moy_cat_classe[$i][$cat],1, ',', ' ');
			calc_moy_debug("\$moy_cat_classe[$i][$cat]=".$moy_cat_classe[$i][$cat]."\n");
	    } else {
	        $moy_cat_classe[$i][$cat] = "-";
	    }

		if($current_eleve_login[$i]==$ele_login_debug) {
			$lignes_debug.="\$moy_cat_classe[$i][$cat]=".$moy_cat_classe[$i][$cat]."<br />";
		}
		*/

	    if ($total_coef_cat_eleve[$i][$cat] != 0) {
			if($current_eleve_login[$i]==$ele_login_debug) {
				$lignes_debug.="\$moy_cat_eleve[$i][$cat]=".$moy_cat_eleve[$i][$cat]."/".$total_coef_cat_eleve[$i][$cat]."<br />";
			}

	        $moy_cat_eleve[$i][$cat] = $moy_cat_eleve[$i][$cat]/$total_coef_cat_eleve[$i][$cat];
	        //$moy_cat_eleve[$i][$cat] = number_format($moy_cat_eleve[$i][$cat],1, ',', ' ');
			calc_moy_debug("\$moy_cat_eleve[$i][$cat]=".$moy_cat_eleve[$i][$cat]."\n");
	    } else {
	        $moy_cat_eleve[$i][$cat] = "-";
	    }

		if($current_eleve_login[$i]==$ele_login_debug) {
			$lignes_debug.="\$moy_cat_eleve[$i][$cat]=".$moy_cat_eleve[$i][$cat]."<br />";
		}
    }
    $i++;
	calc_moy_debug("==============================\n");
}


// Recherche des moyennes min/max/classe de cat�gories
foreach($categories as $cat) {
	$moy_min_categorie[$cat]=1000;
	$moy_max_categorie[$cat]=-1;

	$moy_classe_categorie[$cat]=0;
	$tmp_eff=0;

	$i = 0;
	while ($i < $nombre_eleves) {
		//echo "\$moy_cat_eleve[$i][$cat]=".$moy_cat_eleve[$i][$cat]."<br />";
		if($moy_cat_eleve[$i][$cat]!="-") {
			if($moy_cat_eleve[$i][$cat]<$moy_min_categorie[$cat]) {
				$moy_min_categorie[$cat]=$moy_cat_eleve[$i][$cat];
			}
			if($moy_cat_eleve[$i][$cat]>$moy_max_categorie[$cat]) {
				$moy_max_categorie[$cat]=$moy_cat_eleve[$i][$cat];
			}
			$moy_classe_categorie[$cat]+=$moy_cat_eleve[$i][$cat];

			// On formate avec virgule la moyenne de cat�gorie pour l'�l�ve
			// *** A FAIRE *** Il faudrait prendre en compte le nombre de d�cimales demand�es sur le mod�le
	        //$moy_cat_eleve[$i][$cat] = number_format($moy_cat_eleve[$i][$cat],1, ',', ' ');

			// On incr�mente le nombre d'�l�ves qui ont une moyenne sur la cat�gorie
			$tmp_eff++;
		}

		$i++;
	}

	if($moy_min_categorie[$cat]==1000) {
		$moy_min_categorie[$cat]="-";
	}

	if($moy_max_categorie[$cat]==-1) {
		$moy_max_categorie[$cat]="-";
	}

	if($tmp_eff>0) {
		$lignes_debug.="\$moy_classe_categorie[$cat]=$moy_classe_categorie[$cat]/$tmp_eff=";
		$moy_classe_categorie[$cat]=$moy_classe_categorie[$cat]/$tmp_eff;
		$lignes_debug.="$moy_classe_categorie[$cat]<br />";
	}
	else {
		$moy_classe_categorie[$cat]="-";
		$lignes_debug.="\$moy_classe_categorie[$cat]=$moy_classe_categorie[$cat]<br />";
	}

	$lignes_debug.="\$moy_max_categorie[$cat]=$moy_max_categorie[$cat]<br />";
	$lignes_debug.="\$moy_min_categorie[$cat]=$moy_min_categorie[$cat]<br />";

	// Pour chaque �l�ve, on met les m�mes moyennes min/classe/max
	// de cat�gorie parce que sinon, on pourrait arriver � l'aberration suivante:
	// Un �l�ve, seul de la classe � avoir une combinaison d'options, aurait
	// sa moyenne de cat�gorie qui serait �galement min, max et classe
	$i = 0;
	while ($i < $nombre_eleves) {
		$moy_cat_min[$i][$cat]=$moy_min_categorie[$cat];
		$moy_cat_max[$i][$cat]=$moy_max_categorie[$cat];
		$moy_cat_classe[$i][$cat]=$moy_classe_categorie[$cat];

		$i++;
	}
}


// DEBUG:
//echo $lignes_debug;

//Ajout Eric pour avoir les moyennes g�n�rales minimum et maximum sur les bulletins

//$moy_min_classe = min($moy_gen_eleve);
//$moy_min_classe=20;
$moy_min_classe=21;
for ( $i=0 ; $i < sizeof($moy_gen_eleve) ; $i++ ) {
	if($moy_gen_eleve[$i]!="-"){
		if($moy_gen_eleve[$i]<$moy_min_classe){
			$moy_min_classe=$moy_gen_eleve[$i];
		}
	}
}
if($moy_min_classe==21) {
	$moy_min_classe="-";
}

//$moy_min_classe = number_format($moy_min_classe,1, ',', ' ');
$moy_max_classe = max($moy_gen_eleve);
//$moy_max_classe = number_format($moy_max_classe,1, ',', ' ');

//Calcul de la moyenne g�n�rale de la classe
$nb_elv_classe=sizeof($moy_gen_eleve);
$moy_generale_classe = 0;
$effectif_avec_moyenne=0;
for ( $i=0 ; $i < $nb_elv_classe ; $i++ ) {
	$moy_generale_classe += $moy_gen_eleve[$i];
	if($temoin_au_moins_une_matiere_avec_note[$i]=='y') {$effectif_avec_moyenne++;}
}
//$moy_generale_classe = $moy_generale_classe / $nb_elv_classe;
if($effectif_avec_moyenne!=0) {
	$moy_generale_classe=$moy_generale_classe/$effectif_avec_moyenne;
	//$moy_generale_classe = number_format($moy_generale_classe,1, ',', ' ');
}
else {
	$moy_generale_classe="-";
}

/*
for ( $i=0 ; $i < sizeof($moy_gen_eleve) ; $i++ ) {
	if($moy_gen_eleve[$i]!='-') {$moy_gen_eleve[$i] = number_format($moy_gen_eleve[$i],1, ',', ' ');}
}
*/

// On fournit en entr�e:
//     - $id_classe : la classe concern�e
//     - $periode_num : la p�riode concern�e
// On r�cup�re en sortie:
//     - $moy_gen_eleve[$i]
//     - $moy_gen_classe[$i]
//     - $moy_generale_classe
//     - $moy_max_classe
//     - $moy_min_classe

// A VERIFIER, mais s'il n'y a pas de coef sp�cifique pour un �l�ve, on devrait avoir
//             $moy_gen_classe[$i] == $moy_generale_classe

//     - $moy_cat_classe[$i][$cat]
//     - $moy_cat_eleve[$i][$cat]

//     - $moy_cat_min[$i][$cat] �gale � $moy_min_categorie[$cat]
//     - $moy_cat_max[$i][$cat] �gale � $moy_max_categorie[$cat]

// L� le positionnement au niveau moyenne g�n�rale:
//     - $quartile1_classe_gen
//       �
//     - $quartile6_classe_gen
//     - $place_eleve_classe[$i]

// On a r�cup�r� en interm�diaire les
//     - $current_eleve_login[$i]
//     - $current_group[$j]
//     - $current_eleve_note[$j][$i]
//     - $current_eleve_statut[$j][$i]
//     - $current_coef[$j] (qui peut �tre diff�rent du $coef_eleve pour une mati�re sp�cifique)
//     - $categories -> id
//     - $current_classe_matiere_moyenne[$j] (moyenne de la classe dans la mati�re)

// AJOUT�:
//     - $current_coef_eleve[$i][$j]
//     - $moy_min_classe_grp[$j]
//     - $moy_max_classe_grp[$j]
//     - $current_eleve_rang[$j][$i] sous r�serve que $affiche_rang=='y'
//     - $quartile1_grp[$j] � $quartile6_grp[$j]
//     - $place_eleve_grp[$j][$i]
//     - $current_group_effectif_avec_note[$j] pour le nombre de "vraies" moyennes pour le rang (pas disp, abs,...)

?>