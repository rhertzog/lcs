<?php
/**
 * Ensemble des fonctions qui permettent d'afficher les emplois du temps des profs
 *
 *
 * Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// =============================================================================
//
//                                  PROTOS
//
// int      function DureeMax2Colonnes($jour_sem, $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j , $rang1, $rang2, $period)
// void     function ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem, $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_previous, &$tab_data, &$index_box, $period)
// array    function ConstruireEDTProf($type_edt, $login_edt) 
//
// =============================================================================




// =============================================================================
//
//          Si des cours se déroulent sur les mêmes créneaux (cas classique semaine A semaine B)
//          cette fonction permet de déterminer la hauteur maximum des deux colonnes à afficher
//          de façon à créer deux "div" conteneurs de width = 50% que l'on remplit par la suite
//
//          $jour_sem = lundi, mardi...
//          $login_edt = login du prof
//          $tab_id_creneaux = tableau contenant les créneaux (M1, M2...)
//          $elapse_time = position du pointeur de remplissage (0 = M1(début), 1 = M1(milieu), 2 = M2 etc...)
//          $req_creneau = requête sql passée
//          $j = indice pour indiquer le créneau concerné dans $tab_id_creneau
//          $rang1, $rang2 = indique sur quels enregistrements de la requête porte le calcul           
//          
//
// =============================================================================
function DureeMax2Colonnes($jour_sem, $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j , $rang1, $rang2, $period)
{
            mysqli_data_seek($req_creneau, 0);
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $id_semaine1 = $rep_creneau['id_semaine'];
            $duree1 = $rep_creneau['duree'];
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $id_semaine2 = $rep_creneau['id_semaine'];
            $duree2 = $rep_creneau['duree'];
            // ===== tests de sécurité sur $rang1 et $rang2
            if ($rang1 <=0) {
                $rang1 = 1;
            }
            if ($rang2 <=0) {
                $rang2 = 1;
            }
            if ((mysqli_num_rows($req_creneau) == 2) AND ($id_semaine1 == $id_semaine2) AND ($id_semaine1 != '0'))
            {
                // ========= étude du cas rebelle 15'' !!

                if ($duree1 == 1) {
                    $elapse_time1 = $elapse_time + $duree2;
                    $elapse_time2 = $elapse_time+1;
                    $duree1 = $duree2;
                    $duree2 = 0;
                }
                else {
                    $elapse_time1 = $elapse_time + $duree1;
                    $elapse_time2 = $elapse_time+1;
                    $duree2 = 0;
                }
                $j++;
            }
            else {
                // ************************ calcul de la durée max des deux colonnes dans tous les autres cas
                $elapse_time1 = $elapse_time;
                $elapse_time2 = $elapse_time;
                //echo "init ".$elapse_time." ";
                $duree1 = 0;
                $duree2 = 0;
                mysqli_data_seek($req_creneau, 0);
                $i = 0;
                while ($i < $rang1) {
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $i++;
                }
    
    
                $id_semaine1 = $rep_creneau['id_semaine'];

                mysqli_data_seek($req_creneau, 0);
                $i = 0;
                while ($i < $rang2) {
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $i++;
                }
                if ($rep_creneau) {
                    $id_semaine2 = $rep_creneau['id_semaine'];
    
                }
                else {
    
    
                    $req_id_semaine = mysqli_query($GLOBALS["mysqli"], "SELECT type_edt_semaine FROM edt_semaines GROUP BY type_edt_semaine") or die(mysqli_error($GLOBALS["mysqli"]));
    
                    if (mysqli_num_rows($req_id_semaine) <= 1) {
                        $id_semaine2 = '0';
                    }
                    else if (mysqli_num_rows($req_id_semaine) >= 2) {
                        $rep_id_semaine = mysqli_fetch_array($req_id_semaine);
                        if ($rep_id_semaine['type_edt_semaine'] == $id_semaine1) {
                            $rep_id_semaine = mysqli_fetch_array($req_id_semaine);
                        }
                        $id_semaine2 = $rep_id_semaine['type_edt_semaine'];
                    }
                    $duree2+=1;
                    $elapse_time2+=1;
    
                }
            }

            //echo " ".$elapse_time1." ".$elapse_time2;
            $k = $j;
            do
            {
                $req_demicreneau = LessonsFromDayTeacherSlotWeekPeriod($jour_sem, $login_edt, $tab_id_creneaux[$k], $id_semaine1, $period);
                $rep_demicreneau = mysqli_fetch_array($req_demicreneau);
                if ((mysqli_num_rows($req_demicreneau) == 0) || ($rep_demicreneau['id_semaine'] != $id_semaine1))
                {
                    if ($elapse_time1 < $elapse_time2)
                    {
                        $elapse_time1++;
                        $duree1++;
                        $k = (int)($elapse_time1 / 2);
                    }
                    else if ($elapse_time1 > $elapse_time2)
                    {
                        $elapse_aux = $elapse_time1;
                        $elapse_time1 = $elapse_time2;
                        $elapse_time2 = $elapse_aux;
                        $duree_aux = $duree1;
                        $duree1 = $duree2;
                        $duree2 = $duree_aux;
                        $id_semaine_aux = $id_semaine1;
                        $id_semaine1 = $id_semaine2;
                        $id_semaine2 = $id_semaine_aux;
                        $k = (int)($elapse_time1 / 2);
                    }
                    //echo "permute ".$elapse_time1." ".$elapse_time2." ";
                }
                else 
                {
                    if (($rep_demicreneau['heuredeb_dec'] != 0) AND ($elapse_time1%2 == 0) )
                    {
                        $duree1++;
                        $elapse_time1++;

                    }
                    if (($rep_demicreneau['heuredeb_dec'] == 0) AND ($elapse_time1%2 != 0)AND (mysqli_num_rows($req_demicreneau) == 2))
                    {                    
                        $rep_demicreneau = mysqli_fetch_array($req_demicreneau);
                    }
                    $elapse_time1 += $rep_demicreneau['duree'];
                    $duree1 += $rep_demicreneau['duree'];
                    $k = (int)($elapse_time1 / 2);

                    //echo "increase ".$elapse_time1." ".$elapse_time2." ";
                }
                if (!isset($tab_id_creneaux[$k])) {
                    $elapse_time2 = $elapse_time1;
                }
            }
            // ======= tests de sécurité "$elapse_time1 < 25"
            while (($elapse_time1 != $elapse_time2) AND ($elapse_time1 < 25) AND ($elapse_time2 < 25));
            //echo $elapse_time1 - $elapse_time;
            return ($elapse_time1 - $elapse_time);
}


// =============================================================================
//
//          Si des cours se déroulent sur les mêmes créneaux (cas classique semaine A semaine B)
//          cette fonction permet de remplir un des deux div conteneurs
//
//          $elapse_time = position du pointeur de remplissage (0 = M1(début), 1 = M1(milieu), 2 = M2 etc...)
//          $req_creneau = requête sql passée
//          $duree_max = hauteur maximum de la colonne (renvoyée par la fonction DureeMax2Colonnes)
//          $jour_sem = lundi, mardi...
//          $tab_id_creneaux = tableau contenant les créneaux (M1, M2...)
//          $j = indice pour indiquer le créneaux concerné dans $tab_id_creneau
//          $type_edt = "prof", "classe"... utilisé par la fonction AfficheCreneau de Julien Jocal
//          $login_edt = login du prof
//          $id_semaine_previous = '0', 'A' ou 'B'. uniquement utilisé pour remplir la seconde colonne et pour savoir quelle est l'id de la colonne précédente
//
// =============================================================================
function ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem, $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_previous, &$tab_data, &$index_box, $period)
{
	global $debug_edt;
	global $current_edt_id_cours; // id_cours utilisé pour les semaines A/B dans GetColor()
	global $week_selected;

	if($debug_edt=="y") {
		echo "<p><b>ConstruireColonne</b><br />";
	}

	$current_edt_id_cours="";

	$elapse_time1 = $elapse_time;

	// =============== 1 enregistrement existe : initialisation
	if ($rep_creneau = mysqli_fetch_array($req_creneau)) {

		$current_edt_id_cours=$rep_creneau['id_cours'];

		if($debug_edt=="y") {
			echo "\$current_edt_id_cours=$current_edt_id_cours<br />";
			echo "<strong>".get_info_id_cours($current_edt_id_cours)."</strong><br />";

			$current_edt_id_groupe=$rep_creneau['id_groupe'];
			echo "\$current_edt_id_groupe=$current_edt_id_groupe<br />";
			echo get_info_grp($current_edt_id_groupe)."<br />";
		}

		RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], "", "demicellule".$duree_max, "", "");
		$id_semaine = $rep_creneau['id_semaine'];
		$duree1 = (int)$rep_creneau['duree'];
		if (($rep_creneau['heuredeb_dec'] != 0) AND ($elapse_time1%2 == 0))  {
			RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], "", "cellule1", "cadre", "");
			$duree1++;
			//$elapse_time1++;
			$k = (int)($elapse_time1 / 2);
		}
		$contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem,$type_edt, $rep_creneau['id_groupe'], $rep_creneau['id_aid'],$id_semaine, $period);
		RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
		$elapse_time1 += $duree1;
		$k = (int)($elapse_time1 / 2);
	}
	// =============== aucun enregistrement trouvé : initialisation
	else {
		/*
		echo "DEBUG: plop<br />";
		echo "\$tab_data[$jour]=<pre>";
		print_r($tab_data[$jour]);
		echo "</pre>";
		*/
		RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$duree_max, "", "");
		if ($elapse_time1%2 == 0) {
			$duree1 = 2;
		}
		else {
			$duree1 = 1;
		}
		RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule".$duree1, "cadre", "");
		$elapse_time1 += $duree1;
		$k = (int)($elapse_time1 / 2);
		$rep_creneau['heuredeb_dec']=0;
		$rep_creneau['duree']=0;
	}

	// ================= procédure de remplissage
	$end_process = false;
	if (($rep_creneau['heuredeb_dec']==0) AND ($rep_creneau['duree']==1)) {


	if ((mysqli_num_rows($req_creneau) == 1) OR (mysqli_num_rows($req_creneau) == 2)) {
		// ========== étude des cas n°14,15
		RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
		$duree1++;
		$elapse_time1 ++;
		$k = (int)($elapse_time1 / 2);  
	}
	else if (mysqli_num_rows($req_creneau) == 3) {
		//echo "DEBUG: plip<br />";
		// ========== étude des cas n°19, 20
		$req_creneau_aux = $req_creneau;        // à voir : utiliser une requête auxiliaire ne permet pas apparemment de conserver la requête d'origine dans son état initial
		// c'est embêtant mais j'en ai tenu compte
		mysqli_data_seek($req_creneau_aux, 0);
		$rep_creneau_aux = mysqli_fetch_array($req_creneau_aux);
		$heuredeb_dec_1 = $rep_creneau_aux['heuredeb_dec'];
		$id_semaine_1 = $rep_creneau_aux['id_semaine'];
		$duree1_aux = $rep_creneau_aux['duree'];
		$id_groupe1_aux = $rep_creneau_aux['id_groupe'];
		$id_cours1_aux = $rep_creneau_aux['id_cours'];

		$rep_creneau_aux = mysqli_fetch_array($req_creneau_aux);
		$heuredeb_dec_2 = $rep_creneau_aux['heuredeb_dec'];
		$id_semaine_2 = $rep_creneau_aux['id_semaine'];
		$duree2_aux = $rep_creneau_aux['duree'];
		$id_groupe2_aux = $rep_creneau_aux['id_groupe'];
		$id_cours2_aux = $rep_creneau_aux['id_cours'];

		$rep_creneau_aux = mysqli_fetch_array($req_creneau_aux);
		$heuredeb_dec_3 = $rep_creneau_aux['heuredeb_dec'];
		$id_semaine_3 = $rep_creneau_aux['id_semaine'];
		$duree3_aux = $rep_creneau_aux['duree'];
		$id_groupe3_aux = $rep_creneau_aux['id_groupe'];
		$id_cours3_aux = $rep_creneau_aux['id_cours'];

		$current_edt_id_cours=$rep_creneau['id_cours'];

		if (($heuredeb_dec_1 != 0) AND ($id_semaine_1 == $rep_creneau['id_semaine'])) {

			$contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem,$type_edt, $rep_creneau['id_groupe'], $rep_creneau['id_aid'], $id_semaine_1, $period);
			RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$duree1_aux, GetColor($rep_creneau['id_groupe']), $contenu);
			$duree1 += (int)$duree1_aux;
			$elapse_time1 += (int)$duree1_aux;
			$k = (int)($elapse_time1 / 2);
		}
		else if (($heuredeb_dec_2 != 0) AND ($id_semaine_2 == $rep_creneau['id_semaine'])) {

			$contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem,$type_edt, $rep_creneau['id_groupe'], $rep_creneau['id_aid'], $id_semaine_2, $period);
			RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$duree2_aux, GetColor($rep_creneau['id_groupe']), $contenu);
			$duree1 += (int)$duree2_aux;
			$elapse_time1 += (int)$duree2_aux;
			$k = (int)($elapse_time1 / 2);
		}

		if (($heuredeb_dec_3 != 0) AND ($id_semaine_3 == $rep_creneau['id_semaine'])) {

			$contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem,$type_edt, $rep_creneau['id_groupe'], $rep_creneau['id_aid'], $id_semaine_3, $period);
			RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$duree3_aux, GetColor($rep_creneau['id_groupe']), $contenu);
			$duree1 += (int)$duree3_aux;
			$elapse_time1 += (int)$duree3_aux;
			$k = (int)($elapse_time1 / 2);
		}
		else {
			RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
			$duree1++;
			$elapse_time1 ++;
			$k = (int)($elapse_time1 / 2);
		}
	}
	else 
	{
		RemplirBox($elapse_time,$tab_data[$jour], $index_box, "erreur", $tab_id_creneaux[$j], "none", "none", "cellule2", "cadreRouge", "C01");
		$elapse_time1+=2;
	}


	}
	while (isset($tab_id_creneaux[$k]) AND (!$end_process) AND ($duree1<$duree_max)) {
		//if ($id_semaine_previous == '0') {
		if (isset($id_semaine)) {
			$req_demicreneau = LessonsFromDayTeacherSlotWeekPeriod($jour_sem, $login_edt, $tab_id_creneaux[$k], $id_semaine, $period);
		}
		else {
			$sql="SELECT id_cours, duree, id_groupe, id_aid, heuredeb_dec, id_semaine FROM edt_cours WHERE 
			jour_semaine = '".$jour_sem."' AND
			login_prof = '".$login_edt."' AND
			id_definie_periode = '".$tab_id_creneaux[$k]."' AND
			id_semaine <> '".$id_semaine_previous."' AND
			id_semaine <> '0' AND
			(id_calendrier = '".$period."' OR id_calendrier = '0')
			";
			if($debug_edt=="y") {
				echo "$sql<br />";
			}
			$req_demicreneau = mysqli_query($GLOBALS["mysqli"], $sql) or die(mysqli_error($GLOBALS["mysqli"]));

		}

		$rep_demicreneau = mysqli_fetch_array($req_demicreneau);
		if (mysqli_num_rows($req_demicreneau) == 2) {
			// =========== récupérer les deux cours
			mysqli_data_seek($req_demicreneau, 0);
			$rep_demicreneau = mysqli_fetch_array($req_demicreneau);
			$heuredeb_dec_demi1 = $rep_demicreneau['heuredeb_dec'];
			$rep_demicreneau = mysqli_fetch_array($req_demicreneau);
			$heuredeb_dec_demi2 = $rep_demicreneau['heuredeb_dec'];                

			// =========== afficher le bon cours
			if ($elapse_time1%2 == 0) {
				if ($heuredeb_dec_demi1 == 0) {
					mysqli_data_seek($req_demicreneau, 0);
					$rep_demicreneau = mysqli_fetch_array($req_demicreneau);
				}
				else {
					mysqli_data_seek($req_demicreneau, 0);
					$rep_demicreneau = mysqli_fetch_array($req_demicreneau);
					$rep_demicreneau = mysqli_fetch_array($req_demicreneau);
				}
			}
			else {
				if ($heuredeb_dec_demi1 != 0) {
					mysqli_data_seek($req_demicreneau, 0);
					$rep_demicreneau = mysqli_fetch_array($req_demicreneau);
				}
				else {
					mysqli_data_seek($req_demicreneau, 0);
					$rep_demicreneau = mysqli_fetch_array($req_demicreneau);
					$rep_demicreneau = mysqli_fetch_array($req_demicreneau);
				}
			}
			$contenu = ContenuCreneau($tab_id_creneaux[$k],$jour_sem,$type_edt, $rep_demicreneau['id_groupe'],$rep_demicreneau['id_aid'], "", $period);

			$current_edt_id_cours=$rep_demicreneau['id_cours'];
			//echo "demicreneau \$current_edt_id_cours=$current_edt_id_cours<br />";

			RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$k], $rep_demicreneau['id_groupe'], $rep_demicreneau['id_cours'], "cellule".$rep_demicreneau['duree'], GetColor($rep_demicreneau['id_groupe']), $contenu);

			$duree1 += (int)$rep_demicreneau['duree'];
			$elapse_time1 += (int)$rep_demicreneau['duree'];
			$k = (int)($elapse_time1 / 2);

		}
		else if (mysqli_num_rows($req_demicreneau) == 1) {
			//$rep_demicreneau = mysql_fetch_array($req_demicreneau);

			//echo "DEBUG: plup<br />";
			if (($rep_demicreneau['heuredeb_dec'] != 0) AND ($elapse_time1%2 == 0)) {
				RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$k], "", "", "cellule1", "cadre", "");
				$duree1++;
				$elapse_time1++;
			}

			$current_edt_id_cours=$rep_demicreneau['id_cours'];
			//echo "demicreneau \$current_edt_id_cours=$current_edt_id_cours<br />";

			$contenu = ContenuCreneau($tab_id_creneaux[$k],$jour_sem,$type_edt, $rep_demicreneau['id_groupe'],$rep_demicreneau['id_aid'], "", $period);
			RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$k], $rep_demicreneau['id_groupe'], $rep_demicreneau['id_cours'], "cellule".$rep_demicreneau['duree'], GetColor($rep_demicreneau['id_groupe']), $contenu);
			$duree1 += (int)$rep_demicreneau['duree'];
			$elapse_time1 += (int)$rep_demicreneau['duree'];

			if (($rep_demicreneau['heuredeb_dec'] == 0) AND ($rep_demicreneau['duree'] == 1))  {
				RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$k], "", "", "cellule1", "cadre", "");
				$duree1++;
				$elapse_time1++;
			}

			$k = (int)($elapse_time1 / 2);
		}
		else if ($duree1 < $duree_max) {
			//echo "DEBUG: plyp<br />";
			if ($elapse_time1%2 == 0) {
				RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$k], "", "", "cellule1", "cadre", "");
				$duree1++;
				$elapse_time1++;
				$k = (int)($elapse_time1 / 2);
			}
			else {
				RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$k], "", "", "cellule1", "cadre", "");
				$duree1++;
				$elapse_time1++;
				$k = (int)($elapse_time1 / 2);
			}
		}
		else {
			$end_process = true;
		}
	}
	RemplirBox($elapse_time1,$tab_data[$jour], $index_box, "fin_conteneur", "", "", "", "", "", "");

}
// =============================================================================
//
//
// =============================================================================
function GetColor($id_groupe) {
	global $week_selected;
	global $tab_cadreCouleur;
	global $current_edt_id_cours; // id_cours utilisé pour les semaines A/B dans GetColor()

	if($week_selected=="") {
		$week_selected=date("W");
	}

	//echo "GetColor \$week_selected=$week_selected<br />";

	$temoin_semAB="";
	if(($week_selected!="")&&(preg_match("/^[0-9]{1,}$/",$current_edt_id_cours))) {
		$sql="SELECT type_edt_semaine FROM edt_semaines WHERE num_edt_semaine='".$week_selected."';";
		//echo "$sql<br />";
		$res_type_sem=mysqli_query($GLOBALS["mysqli"], $sql);
		$type_sem = mysqli_fetch_array($res_type_sem);

		$req_sem = mysqli_query($GLOBALS["mysqli"], "SELECT id_semaine FROM edt_cours WHERE id_cours ='".$current_edt_id_cours."'");
		$rep_sem = mysqli_fetch_array($req_sem);
		if (($rep_sem["id_semaine"] != "0")&&($rep_sem["id_semaine"]!=$type_sem["type_edt_semaine"])) {
			$temoin_semAB="On ne met pas de couleur.";
		}
	}

	if (($temoin_semAB=="")&&(GetSettingEdt("edt_aff_couleur_prof") == "coul")) {
		if(isset($tab_cadreCouleur[$id_groupe])) {
			$ReturnColor=$tab_cadreCouleur[$id_groupe];
			//echo "Couleur déjà recherchée pour le groupe $id_groupe<br />";
		}
		else {
			$sql="SELECT id_matiere from j_groupes_matieres WHERE id_groupe ='".$id_groupe."';";
			//echo "$sql<br />";
			$req_matiere = mysqli_query($GLOBALS["mysqli"], $sql);
			$rep_matiere = mysqli_fetch_array($req_matiere);
			$matiere = $rep_matiere['id_matiere'];
			$recher_couleur = "M_".$matiere;
			$color = GetSettingEdt($recher_couleur);
			$ReturnColor = "cadreCouleur".$color;
			$tab_cadreCouleur[$id_groupe]=$ReturnColor;
		}
	}
	elseif($temoin_semAB!="") {
		$color="gainsboro";
		$ReturnColor = "cadreCouleur".$color;
	}
	else {
		$ReturnColor = "cadreCouleur";
	}
	//echo "\$ReturnColor=$ReturnColor<br />";

	return $ReturnColor;
}
// =============================================================================
//
//          Permet de construire l'emploi du temps du prof choisi
//          pour simplifier l'implémentation et faciliter le debuggage, la routine étudie séparément 
//          les cas de figures possibles. J'ai dénombré 26 situations différentes en prenant en compte les situations 
//          les plus improbables (exemple : Sur un créneau donné, le prof a deux cours d'1/2 heure chacun).
//          Ceci permet de contrôler les erreurs de saisies commises par l'admin ou permet simplement résister aux tests loufoques :)
//          j'ai numéroté et répertorié chacune de ces situations
//          Si Nombre d'enregistrements (sur le créneau observé) = 0 : 1 cas (n° 1)
//          Si Nombre d'enregistrements (sur le créneau observé) = 1 : 7 cas (n° 2 ,2' ,3 ,4 ,5 ,6 ,7)        
//          Si Nombre d'enregistrements (sur le créneau observé) = 2 : 12 cas (n° 8, 9 ,10 ,11 ,12 ,12',13, 14 ,15,15',15'' ,16)
//          Si Nombre d'enregistrements (sur le créneau observé) = 3 : 5 cas (n°17, 18, 19, 20 ,21)
//          Si Nombre d'enregistrements (sur le créneau observé) = 4 : 1 cas (n°22)
//          Si Nombre d'enregistrements (sur le créneau observé) >= 5 : situation non envisagée (pour l'emploi du temps d'un prof)
//
// =============================================================================
function ConstruireEDTProf($login_edt, $period) 
{
	global $debug_edt;
	global $current_edt_id_cours; // id_cours utilisé pour les semaines A/B dans GetColor()

	//$debug_edt="y";

	$table_data = array();
	$type_edt = "prof";

	if($debug_edt=="y") {
		echo "DEBUG: ConstruireEDTProf($login_edt, $period)<br />";
	}

$sql="SELECT jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1";
//echo "$sql<br />";
$req_jours = mysqli_query($GLOBALS["mysqli"], $sql) or die(mysqli_error($GLOBALS["mysqli"]));
$jour_sem_tab = array();
while($data_sem_tab = mysqli_fetch_array($req_jours)) {
	$jour_sem_tab[] = $data_sem_tab["jour_horaire_etablissement"];
    $tab_data['entete'][] = $data_sem_tab["jour_horaire_etablissement"];
}


$jour=0;
$sql="SELECT id_definie_periode FROM edt_creneaux
							WHERE type_creneaux != 'pause'";
//echo "$sql<br />";
$req_id_creneaux = mysqli_query($GLOBALS["mysqli"], $sql) or die(mysqli_error($GLOBALS["mysqli"]));
$nbre_lignes = mysqli_num_rows($req_id_creneaux);
if ($nbre_lignes == 0) {
    $nbre_lignes = 1;
}
if ($nbre_lignes > 10) {
    $nbre_lignes = 10;
}
$tab_data['nb_creneaux'] = $nbre_lignes;
$index_box = 0;
while (isset($jour_sem_tab[$jour])) {

if ($type_edt=="prof") {
    $tab_id_creneaux = retourne_id_creneaux();
    $j = 0;
    $elapse_time = 0;

	if($debug_edt=='y') {
		echo "tab_id_creneaux<br /><pre>";
		print_r($tab_id_creneaux);
		echo "</pre>";
		echo "<hr />";
	}

	$nb_tours=0;
    while (isset($tab_id_creneaux[$j])) {
    //while ((isset($tab_id_creneaux[$j]))&&($nb_tours<10)) {
        $req_creneau = LessonsFromDayTeacherSlotPeriod($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux[$j], $period);

		$rep_creneau = mysqli_fetch_array($req_creneau);
        $nb_rows = mysqli_num_rows($req_creneau);

		if($debug_edt=='y') {
			echo "Jour: ".$jour_sem_tab[$jour]."<br />";
			echo "Créneau: ".formate_info_id_definie_periode($tab_id_creneaux[$j])."<br />";
			echo "LessonsFromDayTeacherSlotPeriod<br />";
			echo "\$nb_rows=$nb_rows<br />";
			echo "<pre>";
			print_r($rep_creneau);
			echo "</pre>";
			if($rep_creneau['id_groupe']!="") {
				echo get_info_grp($rep_creneau['id_groupe'])."<br />";
			}
		}


        // ========================================== créneau vide
        if ($nb_rows == 0) {
            $heuredeb_dec = 0;
            if ($elapse_time%2 != 0) 
            {
                $heuredeb_dec = 1;
            }
            $delay = 2-$elapse_time%2;
            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule".$delay, "cadre", "");

            $elapse_time+=$delay;

        }
        // ========================================== 1 seul cours
        else if ($nb_rows == 1) {
		$current_edt_id_cours="";
		if(isset($rep_creneau['id_cours'])) {
			$current_edt_id_cours=$rep_creneau['id_cours'];
		}

            if ($rep_creneau['id_semaine'] != '0') {

                $duree_max = $rep_creneau['duree'];
                $heuredeb_dec = $rep_creneau['heuredeb_dec'];
                // ========= études des cas n°2 , 6 et 7
                if (($duree_max == 1)) {        // ||(($duree_max == 2) AND ($rep_creneau['heuredeb_dec'] == 0))
                    if (($heuredeb_dec == 0) AND ($elapse_time%2 != 0))
                    {

                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            $elapse_time++;
                    }
                    else 

                    {
                        $cell_height = 2;
                        if (($duree_max == 1) AND ($rep_creneau['heuredeb_dec'] != 0) AND ($elapse_time%2 != 0)) {
                            $cell_height = 1;
                        }
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule" . $cell_height, "", "");

                        if (($duree_max == 1) AND ($rep_creneau['heuredeb_dec'] != 0) AND ($elapse_time%2 == 0)) {
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            $elapse_time++;
                        }

                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], $rep_creneau['id_semaine'], $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$duree_max, GetColor($rep_creneau['id_groupe']), $contenu);

                        $elapse_time+=$duree_max;
                        if (($duree_max == 1) AND ($rep_creneau['heuredeb_dec'] == 0)) {


                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            $elapse_time++;
                        }
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule" . $cell_height, "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule" . $cell_height, "cadre", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                    }
           
                }
                // ======== étude du cas n°2' 
                else {
                    $duree_max1 = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j,1,2 ,$period);
                    $duree_max2 = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j,2,1 ,$period);
                    if ($duree_max1 >= $duree_max2) {
                        $duree_max = $duree_max1;
                    }
                    else {
                        $duree_max = $duree_max2;
                    }
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $rep_creneau['id_semaine'], $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
            }
            else {
                // ======== étude du cas n°5
                if (($rep_creneau['heuredeb_dec'] != 0) AND ($elapse_time%2 == 0)) {

                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                    $elapse_time++;
                }
                // ======== étude du cas n°3
                if (($rep_creneau['heuredeb_dec'] == 0) AND ($elapse_time%2 == 1) AND ($rep_creneau['duree'] == 1)) { 

                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                    $elapse_time++;
                }
    
                // ======== étude du cas n°4
                else {           
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                    $elapse_time+=(int)$rep_creneau['duree'];
                }
            }
        }
        // ========================================== 2 cours
        else if ($nb_rows == 2) {
            mysqli_data_seek($req_creneau, 0);
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree1 = $rep_creneau['duree'];
            $heuredeb_dec1 = $rep_creneau['heuredeb_dec'];
            $id_semaine1 = $rep_creneau['id_semaine'];
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree2 = $rep_creneau['duree'];
            $heuredeb_dec2 = $rep_creneau['heuredeb_dec'];
            $id_semaine2 = $rep_creneau['id_semaine'];
            mysqli_data_seek($req_creneau, 0);
            $rep_creneau = mysqli_fetch_array($req_creneau);

		$current_edt_id_cours="";
		if(isset($rep_creneau['id_cours'])) {
			$current_edt_id_cours=$rep_creneau['id_cours'];
		}

            // ========= étude du cas PapaTango 1 (Problème de Transition de edt version 1 vers edt version 2) 

            if (($id_semaine1 == '0') || ($id_semaine2 == '0')) {            
                if (($heuredeb_dec1 == 0) AND ($heuredeb_dec2 == 0)) {
                    $PapaTango = 1;
                }
                else if (($heuredeb_dec1 == 0.5) AND ($heuredeb_dec2 == 0.5)) {
                    $PapaTango = 1;
                }
                else {
                    $PapaTango = 0;
                }
            }
            // ========= étude des cas n°11, 12 et 13

            if ((($id_semaine1 == '0') || ($id_semaine2 == '0')) AND ($PapaTango == 0)) {
                if ($heuredeb_dec1 == 0) {
                    if ($id_semaine1 != '0') {
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                    }

                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);

                    if ($id_semaine1 != '0') {
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                    }
                    $elapse_time++;
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    // ====================== étude du cas 12'
                    if ($id_semaine2 != '0') {
                        $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 2,3 , $period);
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                        $id_semaine_to_use = $rep_creneau['id_semaine'];
                        ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                        $elapse_time += $duree_max;
                    }
                    else {


                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                        $elapse_time+=(int)$rep_creneau['duree'];
                    }
                }
                else {
                    if ($id_semaine2 != '0') {
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                    }
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);

                    if ($id_semaine2 != '0') {
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                    }
                    $elapse_time++;
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    // ====================== étude du cas n°12'
                    if ($id_semaine1 != '0') {
                        $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                        mysqli_data_seek($req_creneau, 0);
                        ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                        $id_semaine_to_use = $rep_creneau['id_semaine'];
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                        $elapse_time += $duree_max;
                    }
                    else {

                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                        $elapse_time+=(int)$rep_creneau['duree'];
                    }
                }
            }
            else {
                mysqli_data_seek($req_creneau, 0);
                $rep_creneau = mysqli_fetch_array($req_creneau);
                $id_semaine1 = $rep_creneau['id_semaine'];
                $heuredeb_dec1 = $rep_creneau['heuredeb_dec'];
                $rep_creneau = mysqli_fetch_array($req_creneau);
                $id_semaine2 = $rep_creneau['id_semaine'];
                $heuredeb_dec2 = $rep_creneau['heuredeb_dec'];

                if ($id_semaine1 != $id_semaine2) {
                    // ========= étude des cas n°8 et n°9 et n°14 et n°15 et n°16 et 10
                    $duree_max1 = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j ,1,2, $period);
                    $duree_max2 = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j ,2,1, $period);
                    if ($duree_max1 >= $duree_max2) {
                        $duree_max = $duree_max1;
                    }
                    else {
                        $duree_max = $duree_max2;
                    }
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $rep_creneau['id_semaine'], $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else {
                    // ========= étude du cas rebelle 15''
                    if ($heuredeb_dec1 == 0) {
                        if ($id_semaine1 != '0') {
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                        }

                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);

                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);

                        if ($id_semaine1 != '0') {
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        }
                        $elapse_time++;
                        $j=(int)($elapse_time/2);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        // ====================== 
                        if ($id_semaine2 != '0') {
                            $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                            mysqli_data_seek($req_creneau, 0);
                            $rep_creneau = mysqli_fetch_array($req_creneau);
                            ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                            $id_semaine_to_use = $rep_creneau['id_semaine'];
                            ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                            $elapse_time += $duree_max;
                        }
                        else {

                            $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                            $elapse_time+=(int)$rep_creneau['duree'];
                        }
                    }
                    else {
                        if ($id_semaine2 != '0') {
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                        }

                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        $rep_creneau = mysqli_fetch_array($req_creneau);

                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);

                        if ($id_semaine2 != '0') {
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        }
                        $elapse_time++;
                        $j=(int)($elapse_time/2);
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        // ====================== 
                        if ($id_semaine1 != '0') {
                            $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                            mysqli_data_seek($req_creneau, 0);
                            ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                            $id_semaine_to_use = $rep_creneau['id_semaine'];
                            $rep_creneau = mysqli_fetch_array($req_creneau);
                            ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                            $elapse_time += $duree_max;
                        }
                        else {
                            $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                            $elapse_time+=(int)$rep_creneau['duree'];
                        }
                    }

                }

            }
        }
        // ========================================== 3 cours
        else if ($nb_rows == 3) {
            mysqli_data_seek($req_creneau, 0);
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree1 = $rep_creneau['duree'];
            $heuredeb_dec1 = $rep_creneau['heuredeb_dec'];
            $id_semaine1 = $rep_creneau['id_semaine'];
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree2 = $rep_creneau['duree'];
            $heuredeb_dec2 = $rep_creneau['heuredeb_dec'];
            $id_semaine2 = $rep_creneau['id_semaine'];
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree3 = $rep_creneau['duree'];
            $heuredeb_dec3 = $rep_creneau['heuredeb_dec'];
            $id_semaine3 = $rep_creneau['id_semaine'];

		$current_edt_id_cours="";
		if(isset($rep_creneau['id_cours'])) {
			$current_edt_id_cours=$rep_creneau['id_cours'];
		}

            if (($id_semaine1 == '0') || ($id_semaine2 == '0')|| ($id_semaine3 == '0')) {
                // ======= étude du cas 17
                if (($heuredeb_dec1  == 0) AND ($id_semaine1 == '0')) {
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    $elapse_time+=(int)$rep_creneau['duree'];

                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 2,3 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if (($heuredeb_dec2  == 0) AND ($id_semaine2 == '0')) {
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    $elapse_time+=(int)$rep_creneau['duree'];

                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if (($heuredeb_dec3  == 0) AND ($id_semaine3 == '0')){
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                    $elapse_time+=(int)$rep_creneau['duree'];

                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,2 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                // ======= étude du cas 18
                else if (($heuredeb_dec1  == 0) AND ($heuredeb_dec2  == 0)){
                    mysqli_data_seek($req_creneau, 0);

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $elapse_time+=(int)$rep_creneau['duree'];

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);

                    $elapse_time+=(int)$rep_creneau['duree'];


                }
                else if (($heuredeb_dec1  == 0) AND ($heuredeb_dec3  == 0)){
                    mysqli_data_seek($req_creneau, 0);

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $elapse_time+=(int)$rep_creneau['duree'];

                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);

                    $elapse_time+=(int)$rep_creneau['duree'];

                }
                else if (($heuredeb_dec2  == 0) AND ($heuredeb_dec3  == 0)){
                    mysqli_data_seek($req_creneau, 0);

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $elapse_time+=(int)$rep_creneau['duree'];
     
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);


                    $elapse_time+=(int)$rep_creneau['duree'];

                }
                else 
                {
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "erreur", $tab_id_creneaux[$j], "none", "none", "cellule2", "cadreRouge", "C02");
                    $elapse_time+=2;
                }
            }
            // ========== étude des cas 19,20 et 21
            else {

                if (($heuredeb_dec1  == 0) AND ($heuredeb_dec2  == 0)){
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,2 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if (($heuredeb_dec1  == 0) AND ($heuredeb_dec3  == 0)){
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if (($heuredeb_dec2  == 0) AND ($heuredeb_dec3  == 0)){
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 2,3 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if ($heuredeb_dec1  == 0){
                    if ($id_semaine1 == $id_semaine2) {
                        $rang = 3;
                    }
                    else {
                        $rang = 2;
                    }
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,$rang , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    if ($rang == 3) {
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        $rep_creneau = mysqli_fetch_array($req_creneau);    
                    }
                    else {
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                    }

                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if ($heuredeb_dec2  == 0){
                    if ($id_semaine2 == $id_semaine1) {
                        $rang = 3;

                    }
                    else {
                        $rang = 1;
                    }
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 2,$rang , $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    if ($rang == 1) {
                        mysqli_data_seek($req_creneau, 0);    
                    }
                    else {
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                    }
                  
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if ($heuredeb_dec3  == 0){
                    if ($id_semaine3 == $id_semaine1) {
                        $rang = 2;
                    }
                    else {
                        $rang = 1;
                    }

                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 3,$rang , $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    if ($rang == 1) {
                        mysqli_data_seek($req_creneau, 0);    
                    }
                    else {
                        mysqli_data_seek($req_creneau, 0); 
                        $rep_creneau = mysqli_fetch_array($req_creneau);            
                    }  
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else {
                    // ============== 3 enseignements sur le même créneau
                    // ============== situation non envisagée
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "erreur", $tab_id_creneaux[$j], "none", "none", "cellule2", "cadreRouge", "C03");

                    $elapse_time+=2;

                }
            }
        }
        else if ($nb_rows == 4)
        {
        // ============= damned !! 4 cours sur le même créneau...
            $rang1 = 0;
            $rang2 = 0;
            mysqli_data_seek($req_creneau, 0);
            $rep_creneau = mysqli_fetch_array($req_creneau);
            if ($rep_creneau['heuredeb_dec'] == 0)
            {
                $rang1 = 1;
            }
            $rep_creneau = mysqli_fetch_array($req_creneau);
            if ($rep_creneau['heuredeb_dec'] == 0)
            {
                if ($rang1 == 0)
                {
                    $rang1 = 2;
                }
                else 
                {
                    $rang2 = 2;
                }
            }
            $rep_creneau = mysqli_fetch_array($req_creneau);
            if ($rep_creneau['heuredeb_dec'] == 0)
            {
                if ($rang1 == 0)
                {
                    $rang1 = 3;
                }
                else 
                {
                    $rang2 = 3;
                }
            }
            $rep_creneau = mysqli_fetch_array($req_creneau);
            if ($rep_creneau['heuredeb_dec'] == 0)
            {
                if ($rang1 == 0)
                {
                    $rang1 = 4;
                }
                else 
                {
                    $rang2 = 4;
                }
            }
            if (($rang1 == 0) OR ($rang2 == 0))
            {
                // ============= trois enseignements de front sur les 4
                // ============= situation non envisagée
                RemplirBox($elapse_time,$tab_data[$jour], $index_box, "erreur", $tab_id_creneaux[$j], "none", "none", "cellule2", "cadreRouge", "C04");

                $elapse_time += 2;
            }
            else {
                $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j ,$rang1,$rang2, $period);
                $l = 1;
                mysqli_data_seek($req_creneau, 0);
                while ($l < $rang1) {
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $l++;
                }
                ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                $id_semaine_to_use = $rep_creneau['id_semaine'];
                $l = 1;
                mysqli_data_seek($req_creneau, 0);
                while ($l < $rang2) {
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $l++;
                }
                ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                $elapse_time += $duree_max;
            }


        }
        else
        {
            // ============= gloups ! 5 enseignements ou plus sur le même créneau
            //               il y a une erreur dans la table edt_cours ou c'est une situation non envisagée
            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "erreur", $tab_id_creneaux[$j], "none", "none", "cellule2", "cadreRouge", "C05");

            $elapse_time += 2;
        }
        $j=(int)($elapse_time/2);

		if($debug_edt=='y') {
			echo "\$j=(int)($elapse_time/2)=$j<br />";
			echo "<hr />";
			$nb_tours++;
			flush();
			//$j++;
		}
    }
}

$jour++;
$index_box = 0;
}

// ***************************************    Remplissage des créneaux


$reglages_creneaux = GetSettingEdt("edt_aff_creneaux");
//Cas où le nom des créneaux sont inscrits à gauche
if ($reglages_creneaux == "noms") {
	$tab_creneaux = retourne_creneaux();
	$i=0;
	while($i<count($tab_creneaux)){
		$tab_id_creneaux = retourne_id_creneaux();
		$c=0;
		while($c<count($tab_id_creneaux)){
            //echo("                <div class=\"horaires\"><div class=\"cadre\"><strong>".$tab_creneaux[$i]."</strong></div></div>\n");
            $tab_data['creneaux'][$jour] = $tab_creneaux[$jour];
			$i ++;
			$c ++;
		}
	}
}

// Cas où les heures sont inscrites à gauche au lieu du nom des créneaux
elseif ($reglages_creneaux == "heures") {
	$tab_horaire = retourne_horaire();
	for($i=0; $i<count($tab_horaire); ) {

	$tab_id_creneaux = retourne_id_creneaux();
		$c=0;
		while($c<count($tab_id_creneaux)){
            //echo("                <div class=\"horaires\"><div class=\"cadre\"><strong>".$tab_horaire[$i]["heure_debut"]."<br />".$tab_horaire[$i]["heure_fin"]."</strong></div></div>\n");
            $tab_data['creneaux'][$i] = $tab_horaire[$i]["heure_debut"]."<br />".$tab_horaire[$i]["heure_fin"];
			$i++;
			$c ++;
		}
	}
}

return $tab_data;
}


// =============================================================================
//
//          Permet de construire l'emploi du temps du prof choisi
//          pour simplifier l'implémentation et faciliter le debuggage, la routine étudie séparément 
//          les cas de figures possibles. J'ai dénombré 26 situations différentes en prenant en compte les situations 
//          les plus improbables (exemple : Sur un créneau donné, le prof a deux cours d'1/2 heure chacun).
//          Ceci permet de contrôler les erreurs de saisies commises par l'admin ou permet simplement résister aux tests loufoques :)
//          j'ai numéroté et répertorié chacune de ces situations
//          Si Nombre d'enregistrements (sur le créneau observé) = 0 : 1 cas (n° 1)
//          Si Nombre d'enregistrements (sur le créneau observé) = 1 : 7 cas (n° 2 ,2' ,3 ,4 ,5 ,6 ,7)        
//          Si Nombre d'enregistrements (sur le créneau observé) = 2 : 12 cas (n° 8, 9 ,10 ,11 ,12 ,12',13, 14 ,15,15',15'' ,16)
//          Si Nombre d'enregistrements (sur le créneau observé) = 3 : 5 cas (n°17, 18, 19, 20 ,21)
//          Si Nombre d'enregistrements (sur le créneau observé) = 4 : 1 cas (n°22)
//          Si Nombre d'enregistrements (sur le créneau observé) >= 5 : situation non envisagée (pour l'emploi du temps d'un prof)
//
// =============================================================================
function ConstruireEDTProfDuJour($login_edt, $period, $jour) 
{
	global $current_edt_id_cours; // id_cours utilisé pour les semaines A/B dans GetColor()

    $table_data = array();
    $type_edt = "prof";

    $req_jours = mysqli_query($GLOBALS["mysqli"], "SELECT jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1") or die(mysqli_error($GLOBALS["mysqli"]));
    $jour_sem_tab = array();

    $entetes = ConstruireEnteteEDT();
    while (!isset($entetes['entete'][$jour])) {
        $jour--;
    }
    $jour_sem_tab[$jour] = $entetes['entete'][$jour];
    $tab_data['entete'][$jour] = $entetes['entete'][$jour];

$req_id_creneaux = mysqli_query($GLOBALS["mysqli"], "SELECT id_definie_periode FROM edt_creneaux
							WHERE type_creneaux != 'pause'") or die(mysqli_error($GLOBALS["mysqli"]));
$nbre_lignes = mysqli_num_rows($req_id_creneaux);
if ($nbre_lignes == 0) {
    $nbre_lignes = 1;
}
if ($nbre_lignes > 10) {
    $nbre_lignes = 10;
}
$tab_data['nb_creneaux'] = $nbre_lignes;
$index_box = 0;
while (isset($jour_sem_tab[$jour])) {

if ($type_edt=="prof") {
    $tab_id_creneaux = retourne_id_creneaux();
    $j = 0;
    $elapse_time = 0;
    while (isset($tab_id_creneaux[$j])) {
        $req_creneau = LessonsFromDayTeacherSlotPeriod($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux[$j], $period);
        $rep_creneau = mysqli_fetch_array($req_creneau);
        $nb_rows = mysqli_num_rows($req_creneau);


      
        // ========================================== créneau vide
        if ($nb_rows == 0) {
            $heuredeb_dec = 0;
            if ($elapse_time%2 != 0) 
            {
                $heuredeb_dec = 1;
            }
            $delay = 2-$elapse_time%2;
            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule".$delay, "cadre", "");

            $elapse_time+=$delay;

        }
        // ========================================== 1 seul cours
        else if ($nb_rows == 1) {

		$current_edt_id_cours="";
		if(isset($rep_creneau['id_cours'])) {
			$current_edt_id_cours=$rep_creneau['id_cours'];
		}

            if ($rep_creneau['id_semaine'] != '0') {
                $duree_max = $rep_creneau['duree'];
                $heuredeb_dec = $rep_creneau['heuredeb_dec'];

                // ========= études des cas n°2 , 6 et 7
                if (($duree_max == 1)) {        // ||(($duree_max == 2) AND ($rep_creneau['heuredeb_dec'] == 0))
                    if (($heuredeb_dec == 0) AND ($elapse_time%2 != 0))
                    {

                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            $elapse_time++;
                    }
                    else 

                    {
                        $cell_height = 2;
                        if (($duree_max == 1) AND ($rep_creneau['heuredeb_dec'] != 0) AND ($elapse_time%2 != 0)) {
                            $cell_height = 1;
                        }                    	
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule" . $cell_height, "", "");

                        if (($duree_max == 1) AND ($rep_creneau['heuredeb_dec'] != 0) AND ($elapse_time%2 == 0)) {

                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            $elapse_time++;
                        }



                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], $rep_creneau['id_semaine'], $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$duree_max, GetColor($rep_creneau['id_groupe']), $contenu);

                        $elapse_time+=$duree_max;
                        if (($duree_max == 1) AND ($rep_creneau['heuredeb_dec'] == 0)) {


                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            $elapse_time++;
                        }
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule" . $cell_height, "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule" . $cell_height, "cadre", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                    }
           
                }
                // ======== étude du cas n°2' 
                else {
                    $duree_max1 = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j,1,2 ,$period);
                    $duree_max2 = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j,2,1 ,$period);
                    if ($duree_max1 >= $duree_max2) {
                        $duree_max = $duree_max1;
                    }
                    else {
                        $duree_max = $duree_max2;
                    }
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $rep_creneau['id_semaine'], $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
            }
            else {
                // ======== étude du cas n°5
                if (($rep_creneau['heuredeb_dec'] != 0) AND ($elapse_time%2 == 0)) {

                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                    $elapse_time++;
                }
                // ======== étude du cas n°3
                if (($rep_creneau['heuredeb_dec'] == 0) AND ($elapse_time%2 == 1) AND ($rep_creneau['duree'] == 1)) { 

                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                    $elapse_time++;
                }
    
                // ======== étude du cas n°4
                else {           
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                    $elapse_time+=(int)$rep_creneau['duree'];
                }
            }
        }
        // ========================================== 2 cours
        else if ($nb_rows == 2) {
            mysqli_data_seek($req_creneau, 0);
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree1 = $rep_creneau['duree'];
            $heuredeb_dec1 = $rep_creneau['heuredeb_dec'];
            $id_semaine1 = $rep_creneau['id_semaine'];
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree2 = $rep_creneau['duree'];
            $heuredeb_dec2 = $rep_creneau['heuredeb_dec'];
            $id_semaine2 = $rep_creneau['id_semaine'];
            mysqli_data_seek($req_creneau, 0);
            $rep_creneau = mysqli_fetch_array($req_creneau);

		$current_edt_id_cours="";
		if(isset($rep_creneau['id_cours'])) {
			$current_edt_id_cours=$rep_creneau['id_cours'];
		}

            // ========= étude du cas PapaTango 1 (Problème de Transition de edt version 1 vers edt version 2) 

            if (($id_semaine1 == '0') || ($id_semaine2 == '0')) {            
                if (($heuredeb_dec1 == 0) AND ($heuredeb_dec2 == 0)) {
                    $PapaTango = 1;
                }
                else if (($heuredeb_dec1 == 0.5) AND ($heuredeb_dec2 == 0.5)) {
                    $PapaTango = 1;
                }
                else {
                    $PapaTango = 0;
                }
            }
            // ========= étude des cas n°11, 12 et 13

            if ((($id_semaine1 == '0') || ($id_semaine2 == '0')) AND ($PapaTango == 0)) {
                if ($heuredeb_dec1 == 0) {
                    if ($id_semaine1 != '0') {
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                    }

                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);

                    if ($id_semaine1 != '0') {
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                    }
                    $elapse_time++;
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    // ====================== étude du cas 12'
                    if ($id_semaine2 != '0') {
                        $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 2,3 , $period);
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                        $id_semaine_to_use = $rep_creneau['id_semaine'];
                        ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                        $elapse_time += $duree_max;
                    }
                    else {


                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                        $elapse_time+=(int)$rep_creneau['duree'];
                    }
                }
                else {
                    if ($id_semaine2 != '0') {
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                    }
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);

                    if ($id_semaine2 != '0') {
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                    }
                    $elapse_time++;
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    // ====================== étude du cas n°12'
                    if ($id_semaine1 != '0') {
                        $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                        mysqli_data_seek($req_creneau, 0);
                        ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                        $id_semaine_to_use = $rep_creneau['id_semaine'];
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                        $elapse_time += $duree_max;
                    }
                    else {

                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                        $elapse_time+=(int)$rep_creneau['duree'];
                    }
                }
            }
            else {
                mysqli_data_seek($req_creneau, 0);
                $rep_creneau = mysqli_fetch_array($req_creneau);
                $id_semaine1 = $rep_creneau['id_semaine'];
                $heuredeb_dec1 = $rep_creneau['heuredeb_dec'];
                $rep_creneau = mysqli_fetch_array($req_creneau);
                $id_semaine2 = $rep_creneau['id_semaine'];
                $heuredeb_dec2 = $rep_creneau['heuredeb_dec'];

			$current_edt_id_cours="";
			if(isset($rep_creneau['id_cours'])) {
				$current_edt_id_cours=$rep_creneau['id_cours'];
			}

                if ($id_semaine1 != $id_semaine2) {
                    // ========= étude des cas n°8 et n°9 et n°14 et n°15 et n°16 et 10
                    $duree_max1 = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j ,1,2, $period);
                    $duree_max2 = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j ,2,1, $period);
                    if ($duree_max1 >= $duree_max2) {
                        $duree_max = $duree_max1;
                    }
                    else {
                        $duree_max = $duree_max2;
                    }
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $rep_creneau['id_semaine'], $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else {
                    // ========= étude du cas rebelle 15''
                    if ($heuredeb_dec1 == 0) {
                        if ($id_semaine1 != '0') {
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                        }

                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);

                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);

                        if ($id_semaine1 != '0') {
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        }
                        $elapse_time++;
                        $j=(int)($elapse_time/2);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        // ====================== 
                        if ($id_semaine2 != '0') {
                            $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                            mysqli_data_seek($req_creneau, 0);
                            $rep_creneau = mysqli_fetch_array($req_creneau);
                            ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                            $id_semaine_to_use = $rep_creneau['id_semaine'];
                            ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                            $elapse_time += $duree_max;
                        }
                        else {

                            $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                            $elapse_time+=(int)$rep_creneau['duree'];
                        }
                    }
                    else {
                        if ($id_semaine2 != '0') {
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                        }

                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        $rep_creneau = mysqli_fetch_array($req_creneau);

                        $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                        RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);

                        if ($id_semaine2 != '0') {
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule1", "", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "vide", $tab_id_creneaux[$j], "", "", "cellule1", "cadre", "");
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");
                        }
                        $elapse_time++;
                        $j=(int)($elapse_time/2);
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        // ====================== 
                        if ($id_semaine1 != '0') {
                            $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                            mysqli_data_seek($req_creneau, 0);
                            ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                            $id_semaine_to_use = $rep_creneau['id_semaine'];
                            $rep_creneau = mysqli_fetch_array($req_creneau);
                            ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                            $elapse_time += $duree_max;
                        }
                        else {
                            $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                            $elapse_time+=(int)$rep_creneau['duree'];
                        }
                    }

                }

            }
        }
        // ========================================== 3 cours
        else if ($nb_rows == 3) {
            mysqli_data_seek($req_creneau, 0);
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree1 = $rep_creneau['duree'];
            $heuredeb_dec1 = $rep_creneau['heuredeb_dec'];
            $id_semaine1 = $rep_creneau['id_semaine'];
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree2 = $rep_creneau['duree'];
            $heuredeb_dec2 = $rep_creneau['heuredeb_dec'];
            $id_semaine2 = $rep_creneau['id_semaine'];
            $rep_creneau = mysqli_fetch_array($req_creneau);
            $duree3 = $rep_creneau['duree'];
            $heuredeb_dec3 = $rep_creneau['heuredeb_dec'];
            $id_semaine3 = $rep_creneau['id_semaine'];


		$current_edt_id_cours="";
		if(isset($rep_creneau['id_cours'])) {
			$current_edt_id_cours=$rep_creneau['id_cours'];
		}

            if (($id_semaine1 == '0') || ($id_semaine2 == '0')|| ($id_semaine3 == '0')) {
                // ======= étude du cas 17
                if (($heuredeb_dec1  == 0) AND ($id_semaine1 == '0')) {
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    $elapse_time+=(int)$rep_creneau['duree'];

                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 2,3 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if (($heuredeb_dec2  == 0) AND ($id_semaine2 == '0')) {
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    $elapse_time+=(int)$rep_creneau['duree'];

                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if (($heuredeb_dec3  == 0) AND ($id_semaine3 == '0')){
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);

                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);
                    $elapse_time+=(int)$rep_creneau['duree'];

                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,2 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                // ======= étude du cas 18
                else if (($heuredeb_dec1  == 0) AND ($heuredeb_dec2  == 0)){
                    mysqli_data_seek($req_creneau, 0);

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $elapse_time+=(int)$rep_creneau['duree'];

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);

                    $elapse_time+=(int)$rep_creneau['duree'];


                }
                else if (($heuredeb_dec1  == 0) AND ($heuredeb_dec3  == 0)){
                    mysqli_data_seek($req_creneau, 0);

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $elapse_time+=(int)$rep_creneau['duree'];

                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);

                    $elapse_time+=(int)$rep_creneau['duree'];

                }
                else if (($heuredeb_dec2  == 0) AND ($heuredeb_dec3  == 0)){
                    mysqli_data_seek($req_creneau, 0);

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "conteneur", $tab_id_creneaux[$j], "", "", "demicellule".$rep_creneau['duree'], "", "");
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule1", GetColor($rep_creneau['id_groupe']), $contenu);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "fin_conteneur", $tab_id_creneaux[$j], "", "", "", "", "");

                    $elapse_time+=(int)$rep_creneau['duree'];
     
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $contenu = ContenuCreneau($tab_id_creneaux[$j],$jour_sem_tab[$jour],$type_edt, $rep_creneau['id_groupe'],$rep_creneau['id_aid'], "", $period);
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$rep_creneau['duree'], GetColor($rep_creneau['id_groupe']), $contenu);


                    $elapse_time+=(int)$rep_creneau['duree'];

                }
                else 
                {
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "erreur", $tab_id_creneaux[$j], "none", "none", "cellule2", "cadreRouge", "C02");
                    $elapse_time+=2;
                }
            }
            // ========== étude des cas 19,20 et 21
            else {

                if (($heuredeb_dec1  == 0) AND ($heuredeb_dec2  == 0)){
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,2 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if (($heuredeb_dec1  == 0) AND ($heuredeb_dec3  == 0)){
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,3 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if (($heuredeb_dec2  == 0) AND ($heuredeb_dec3  == 0)){
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 2,3 , $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if ($heuredeb_dec1  == 0){
                    if ($id_semaine1 == $id_semaine2) {
                        $rang = 3;
                    }
                    else {
                        $rang = 2;
                    }
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 1,$rang , $period);
                    mysqli_data_seek($req_creneau, 0);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    if ($rang == 3) {
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        $rep_creneau = mysqli_fetch_array($req_creneau);    
                    }
                    else {
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                    }

                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if ($heuredeb_dec2  == 0){
                    if ($id_semaine2 == $id_semaine1) {
                        $rang = 3;

                    }
                    else {
                        $rang = 1;
                    }
                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 2,$rang , $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    if ($rang == 1) {
                        mysqli_data_seek($req_creneau, 0);    
                    }
                    else {
                        mysqli_data_seek($req_creneau, 0);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                        $rep_creneau = mysqli_fetch_array($req_creneau);
                    }
                  
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else if ($heuredeb_dec3  == 0){
                    if ($id_semaine3 == $id_semaine1) {
                        $rang = 2;
                    }
                    else {
                        $rang = 1;
                    }

                    $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j, 3,$rang , $period);
                    mysqli_data_seek($req_creneau, 0);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                    $id_semaine_to_use = $rep_creneau['id_semaine'];
                    if ($rang == 1) {
                        mysqli_data_seek($req_creneau, 0);    
                    }
                    else {
                        mysqli_data_seek($req_creneau, 0); 
                        $rep_creneau = mysqli_fetch_array($req_creneau);            
                    }  
                    ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                    $elapse_time += $duree_max;
                }
                else {
                    // ============== 3 enseignements sur le même créneau
                    // ============== situation non envisagée
                    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "erreur", $tab_id_creneaux[$j], "none", "none", "cellule2", "cadreRouge", "C03");

                    $elapse_time+=2;

                }
            }
        }
        else if ($nb_rows == 4)
        {
        // ============= damned !! 4 cours sur le même créneau...
            $rang1 = 0;
            $rang2 = 0;
            mysqli_data_seek($req_creneau, 0);
            $rep_creneau = mysqli_fetch_array($req_creneau);
            if ($rep_creneau['heuredeb_dec'] == 0)
            {
                $rang1 = 1;
            }
            $rep_creneau = mysqli_fetch_array($req_creneau);
            if ($rep_creneau['heuredeb_dec'] == 0)
            {
                if ($rang1 == 0)
                {
                    $rang1 = 2;
                }
                else 
                {
                    $rang2 = 2;
                }
            }
            $rep_creneau = mysqli_fetch_array($req_creneau);
            if ($rep_creneau['heuredeb_dec'] == 0)
            {
                if ($rang1 == 0)
                {
                    $rang1 = 3;
                }
                else 
                {
                    $rang2 = 3;
                }
            }
            $rep_creneau = mysqli_fetch_array($req_creneau);
            if ($rep_creneau['heuredeb_dec'] == 0)
            {
                if ($rang1 == 0)
                {
                    $rang1 = 4;
                }
                else 
                {
                    $rang2 = 4;
                }
            }
            if (($rang1 == 0) OR ($rang2 == 0))
            {
                // ============= trois enseignements de front sur les 4
                // ============= situation non envisagée
                RemplirBox($elapse_time,$tab_data[$jour], $index_box, "erreur", $tab_id_creneaux[$j], "none", "none", "cellule2", "cadreRouge", "C04");

                $elapse_time += 2;
            }
            else {
                $duree_max = DureeMax2Colonnes($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux, $elapse_time,$req_creneau, $j ,$rang1,$rang2, $period);
                $l = 1;
                mysqli_data_seek($req_creneau, 0);
                while ($l < $rang1) {
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $l++;
                }
                ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, '0', $tab_data,$index_box, $period);
                $id_semaine_to_use = $rep_creneau['id_semaine'];
                $l = 1;
                mysqli_data_seek($req_creneau, 0);
                while ($l < $rang2) {
                    $rep_creneau = mysqli_fetch_array($req_creneau);
                    $l++;
                }
                ConstruireColonne($elapse_time, $req_creneau, $duree_max, $jour_sem_tab[$jour], $jour, $tab_id_creneaux, $j, $type_edt, $login_edt, $id_semaine_to_use, $tab_data,$index_box, $period);
                $elapse_time += $duree_max;
            }


        }
        else
        {
            // ============= gloups ! 5 enseignements ou plus sur le même créneau
            //               il y a une erreur dans la table edt_cours ou c'est une situation non envisagée
            RemplirBox($elapse_time,$tab_data[$jour], $index_box, "erreur", $tab_id_creneaux[$j], "none", "none", "cellule2", "cadreRouge", "C05");

            $elapse_time += 2;
        }
        $j=(int)($elapse_time/2);
    }
}

$jour++;
$index_box = 0;
}

// ***************************************    Remplissage des créneaux


$reglages_creneaux = GetSettingEdt("edt_aff_creneaux");
//Cas où le nom des créneaux sont inscrits à gauche
if ($reglages_creneaux == "noms") {
	$tab_creneaux = retourne_creneaux();
	$i=0;
	while($i<count($tab_creneaux)){
		$tab_id_creneaux = retourne_id_creneaux();
		$c=0;
		while($c<count($tab_id_creneaux)){
            //echo("                <div class=\"horaires\"><div class=\"cadre\"><strong>".$tab_creneaux[$i]."</strong></div></div>\n");
            $tab_data['creneaux'][$jour] = $tab_creneaux[$jour];
			$i ++;
			$c ++;
		}
	}
}

// Cas où les heures sont inscrites à gauche au lieu du nom des créneaux
elseif ($reglages_creneaux == "heures") {
	$tab_horaire = retourne_horaire();
	for($i=0; $i<count($tab_horaire); ) {

	$tab_id_creneaux = retourne_id_creneaux();
		$c=0;
		while($c<count($tab_id_creneaux)){
            //echo("                <div class=\"horaires\"><div class=\"cadre\"><strong>".$tab_horaire[$i]["heure_debut"]."<br />".$tab_horaire[$i]["heure_fin"]."</strong></div></div>\n");
            $tab_data['creneaux'][$i] = $tab_horaire[$i]["heure_debut"]."<br />".$tab_horaire[$i]["heure_fin"];
			$i++;
			$c ++;
		}
	}
}

return $tab_data;
}

?>
