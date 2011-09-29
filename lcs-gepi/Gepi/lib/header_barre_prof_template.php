<?php

/**
 * Fichier qui permet de construire la barre de menu professeur
 * 
 * $Id: header_barre_prof_template.php 8251 2011-09-16 17:24:52Z crob $
 * 
 * Variables envoy�es au gabarit : liens de la barre de menu prof
 * - $tbs_menu_prof = array(lien , texte)
 *
 * @license GNU/GPL v2
 * @package General
 * @subpackage Affichage
 * @see acces()
 * @see get_groups_for_prof()
 * @see getSettingValue()
 * @see nb_saisies_bulletin()
 * @see retourneCours()
 */

/* This file is part of GEPI.
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
 *
 *
 */
  
// ====== SECURITE =======

if (!$_SESSION["login"]) {
    header("Location: ../logout.php?auto=2");
    die();
}

// Fonction g�n�rant le menu Plugins
include("tbs_menu_plugins.inc.php");

	//=======================================================
	$mes_groupes=get_groups_for_prof($_SESSION['login'],NULL,array('classes', 'periodes'));
	$tmp_mes_classes=array();
	foreach($mes_groupes as $tmp_group) {
		foreach($tmp_group["classes"]["classes"] as $key_id_classe => $value_tab_classe) {
			if(!in_array($value_tab_classe['classe'], $tmp_mes_classes)) {
				$tmp_mes_classes[$key_id_classe]=$value_tab_classe['classe'];
			}
		}
	}

	// Pour permettre d'utiliser le module EdT avec les autres modules
	$groupe_abs = $groupe_text = '';
	if (getSettingValue("autorise_edt_tous") == "y") {
		// Actuellement, ce professeur � ce cours (id_cours):
		$cours_actu = retourneCours($_SESSION["login"]);
		// Qui correspond � cet id_groupe :
		if ($cours_actu != "non") {
			$queryG = mysql_query("SELECT id_groupe, id_aid FROM edt_cours WHERE id_cours = '".$cours_actu."'");
			$groupe_actu = mysql_fetch_array($queryG);
			// Il faudrait v�rifier si ce n'est pas une AID
			if ($groupe_actu["id_aid"] != NULL) {
				$groupe_abs = '?groupe=AID|'.$groupe_actu["id_aid"].'&amp;menuBar=ok';
				$groupe_text = '';
			}else{
				$groupe_text = '?id_groupe='.$groupe_actu["id_groupe"].'&amp;year='.date("Y").'&amp;month='.date("n").'&amp;day='.date("d").'&amp;edit_devoir=';
				$groupe_abs = '?groupe='.$groupe_actu["id_groupe"].'&amp;menuBar=ok';
			}
		}
	}

	$compteur_menu=0;
	
	
	// Lien vers l'accueil
	$tbs_menu_prof[$compteur_menu]=array("lien"=>'/accueil.php', "texte"=>"Accueil");
	$compteur_menu++;

	/* On fixe l'ensemble des modules qui sont ouverts pour faire la liste des <li> */
	//=======================================================
	// module absence
	if (getSettingValue("active_module_absence_professeur")=='y') {
		if (getSettingValue("active_module_absence")=='y' ) {
		    $tbs_menu_prof[$compteur_menu]=array("lien"=>'/mod_absences/professeurs/prof_ajout_abs.php'.$groupe_abs , "texte"=>"Absences");
		} else if (getSettingValue("active_module_absence")=='2' ) {
		    $tbs_menu_prof[$compteur_menu]=array("lien"=>'/mod_abs2/index.php'.$groupe_abs , "texte"=>"Absences");
		}
		$compteur_menu++;
	}else{$barre_absence = '';}

	//=======================================================
	// Module Cahier de textes
	if (getSettingValue("active_cahiers_texte") == 'y') {
		$tbs_menu_prof[$compteur_menu]["lien"]='/cahier_texte/index.php'.$groupe_text;
		$tbs_menu_prof[$compteur_menu]["texte"]="C. de Textes";
		
		$tmp_sous_menu=array();
		$cpt_sous_menu=0;
		foreach($mes_groupes as $tmp_group) {
			$tmp_sous_menu[$cpt_sous_menu]['lien']='/cahier_texte/index.php?id_groupe='.$tmp_group['id'].'&amp;year='.strftime("%Y").'&amp;month='.strftime("%m").'&amp;day='.strftime("%d").'&amp;edit_devoir=';
			$tmp_sous_menu[$cpt_sous_menu]['texte']=$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)';
			$cpt_sous_menu++;
		}
		if(getSettingValue('GepiCahierTexteVersion')==2) {
			$tmp_sous_menu[$cpt_sous_menu]['lien']='/cahier_texte_2/see_all.php';
		}
		else {
			$tmp_sous_menu[$cpt_sous_menu]['lien']='/cahier_texte/see_all.php';
		}
		$tmp_sous_menu[$cpt_sous_menu]['texte']="Consultation des cahiers de textes";
		$cpt_sous_menu++;

		$tmp_sous_menu[$cpt_sous_menu]['lien']='/documents/archives/index.php';
		$tmp_sous_menu[$cpt_sous_menu]['texte']="Mes archives CDT";
		$cpt_sous_menu++;

		$tbs_menu_prof[$compteur_menu]["sous_menu"]=$tmp_sous_menu;
		$tbs_menu_prof[$compteur_menu]["niveau_sous_menu"]=2;

		$compteur_menu++;
	}else{$barre_textes = '';}

	//=======================================================
	// Module carnet de notes

	if(getSettingValue("active_carnets_notes") == 'y'){
		// Cahiers de notes
		$tbs_menu_prof[$compteur_menu]=array("lien"=> '/cahier_notes/index.php' , "texte"=>"Notes");
		$tmp_sous_menu=array();
		$cpt_sous_menu=0;
		foreach($mes_groupes as $tmp_group) {
			$tmp_sous_menu[$cpt_sous_menu]['lien']='/cahier_notes/index.php?id_groupe='.$tmp_group['id'];
			$tmp_sous_menu[$cpt_sous_menu]['texte']=$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)';
			$tmp_sous_menu2=array();
			$cpt_sous_menu2=0;
			for($loop=1;$loop<=count($tmp_group["periodes"]);$loop++) {
				$tmp_sous_menu2[$cpt_sous_menu2]['lien']='/cahier_notes/index.php?id_groupe='.$tmp_group['id'].'&amp;periode_num='.$loop;
				$tmp_sous_menu2[$cpt_sous_menu2]['texte']=$tmp_group["periodes"][$loop]["nom_periode"];
				if($tmp_group["classe"]["ver_periode"]["all"][$loop]>=2) {
					$tmp_sous_menu2[$cpt_sous_menu2]['texte'].=' <img src="'.$gepiPath.'/images/edit16.png" width="16" height="16" alt="P�riode non verrouill�e: Saisie possible" title="P�riode non verrouill�e: Saisie possible" />';
				}
				else {
					$tmp_sous_menu2[$cpt_sous_menu2]['texte'].=' <img src="'.$gepiPath.'/images/icons/securite.png" width="16" height="16" alt="P�riode verrouill�e: Saisie impossible" title="P�riode verrouill�e: Saisie impossible" />';
				}
				$cpt_sous_menu2++;
			}
			$tmp_sous_menu[$cpt_sous_menu]['sous_menu']=$tmp_sous_menu2;
			$tmp_sous_menu[$cpt_sous_menu]['niveau_sous_menu']=3;
			$cpt_sous_menu++;
		}

		if((getSettingValue("GepiAccesReleveProf") == "yes") OR
		(getSettingValue("GepiAccesReleveProfTousEleves") == "yes") OR
		(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes")) {
			$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/cahier_notes/visu_releve_notes_bis.php' , "texte"=>"Relev�s de notes");
			$cpt_sous_menu++;
		}

		if((getSettingValue("GepiAccesMoyennesProf") == "yes") OR
		(getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
		(getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")) {
			$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/cahier_notes/index2.php' , "texte"=>"Moyennes des carnets de notes");
			$tmp_sous_menu2=array();
			$cpt_sous_menu2=0;
			foreach($tmp_mes_classes as $key => $value) {
				$tmp_sous_menu2[$cpt_sous_menu2]=array("lien"=> '/cahier_notes/index2.php?id_classe='.$key , "texte"=>"$value");
				$cpt_sous_menu2++;
			}
			$tmp_sous_menu[$cpt_sous_menu]['sous_menu']=$tmp_sous_menu2;
			$tmp_sous_menu[$cpt_sous_menu]['niveau_sous_menu']=3;
			$cpt_sous_menu++;
		}

		if (getSettingValue('utiliser_sacoche') == 'yes' && getSettingValue('sacocheUrl') != '') {
			$tmp_sous_menu[$cpt_sous_menu] = array("lien"=> getSettingValue('sacocheUrl').'?sso&amp;id='.getSettingValue('sacoche_base') , "texte"=>"�valuation par comp�tence");
			$cpt_sous_menu++;
		}

		$tbs_menu_prof[$compteur_menu]['sous_menu']=$tmp_sous_menu;
		$tbs_menu_prof[$compteur_menu]['niveau_sous_menu']=2;
		$compteur_menu++;


		// Bulletins
		$tbs_menu_prof[$compteur_menu]=array("lien"=> '/saisie/index.php' , "texte"=>"Bulletins");
		$tmp_sous_menu=array();
		$cpt_sous_menu=0;

			// Notes des bulletins
			$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/saisie/index.php' , "texte"=>"Notes");
			$tmp_sous_menu2=array();
			$cpt_sous_menu2=0;
			foreach($mes_groupes as $tmp_group) {
				$tmp_sous_menu2[$cpt_sous_menu2]['lien']='/saisie/index.php?id_groupe='.$tmp_group['id'];
				$tmp_sous_menu2[$cpt_sous_menu2]['texte']=$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)';
	
				$tmp_sous_menu3=array();
				$cpt_sous_menu3=0;
	
				for($loop=1;$loop<=count($tmp_group["periodes"]);$loop++) {
					$tmp_sous_menu3[$cpt_sous_menu3]["lien"]='/saisie/saisie_notes.php?id_groupe='.$tmp_group['id'].'&amp;periode_cn='.$loop;
					$tmp_sous_menu3[$cpt_sous_menu3]["texte"]=$tmp_group["periodes"][$loop]["nom_periode"];
					$tmp_sous_menu3[$cpt_sous_menu3]["texte"].=' '.nb_saisies_bulletin("notes", $tmp_group["id"], $loop, "couleur");
					if($tmp_group["classe"]["ver_periode"]["all"][$loop]>=2) {
						$tmp_sous_menu3[$cpt_sous_menu3]["texte"].=' <img src="'.$gepiPath.'/images/edit16.png" width="16" height="16" alt="P�riode non verrouill�e: Saisie possible" title="P�riode non verrouill�e: Saisie possible" />';
					}
					else {
						$tmp_sous_menu3[$cpt_sous_menu3]["texte"].=' <img src="'.$gepiPath.'/images/icons/securite.png" width="16" height="16" alt="P�riode verrouill�e: Saisie impossible" title="P�riode verrouill�e: Saisie impossible" />';
					}
					$cpt_sous_menu3++;
				}
				$tmp_sous_menu2[$cpt_sous_menu2]['sous_menu']=$tmp_sous_menu3;
				$tmp_sous_menu2[$cpt_sous_menu2]['niveau_sous_menu']=4;
	
				$cpt_sous_menu2++;
			}
			$tmp_sous_menu[$cpt_sous_menu]['sous_menu']=$tmp_sous_menu2;
			$tmp_sous_menu[$cpt_sous_menu]['niveau_sous_menu']=3;
			$cpt_sous_menu++;



			// Appr�ciations des bulletins
			$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/saisie/index.php' , "texte"=>"Appr�ciations");
			$tmp_sous_menu2=array();
			$cpt_sous_menu2=0;
			foreach($mes_groupes as $tmp_group) {
				$tmp_sous_menu2[$cpt_sous_menu2]['lien']='/saisie/index.php?id_groupe='.$tmp_group['id'];
				$tmp_sous_menu2[$cpt_sous_menu2]['texte']=$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)';
	
				$tmp_sous_menu3=array();
				$cpt_sous_menu3=0;
	
				for($loop=1;$loop<=count($tmp_group["periodes"]);$loop++) {
					$tmp_sous_menu3[$cpt_sous_menu3]["lien"]='/saisie/saisie_appreciations.php?id_groupe='.$tmp_group['id'].'&amp;periode_cn='.$loop;
					$tmp_sous_menu3[$cpt_sous_menu3]["texte"]=$tmp_group["periodes"][$loop]["nom_periode"];
					$tmp_sous_menu3[$cpt_sous_menu3]["texte"].=' '.nb_saisies_bulletin("appreciations", $tmp_group["id"], $loop, "couleur");
					if($tmp_group["classe"]["ver_periode"]["all"][$loop]>=2) {
						$tmp_sous_menu3[$cpt_sous_menu3]["texte"].=' <img src="'.$gepiPath.'/images/edit16.png" width="16" height="16" alt="P�riode non verrouill�e: Saisie possible" title="P�riode non verrouill�e: Saisie possible" />';
					}
					else {
						$tmp_sous_menu3[$cpt_sous_menu3]["texte"].=' <img src="'.$gepiPath.'/images/icons/securite.png" width="16" height="16" alt="P�riode verrouill�e: Saisie impossible" title="P�riode verrouill�e: Saisie impossible" />';
					}
					$cpt_sous_menu3++;
				}
				$tmp_sous_menu2[$cpt_sous_menu2]['sous_menu']=$tmp_sous_menu3;
				$tmp_sous_menu2[$cpt_sous_menu2]['niveau_sous_menu']=4;
	
				$cpt_sous_menu2++;
			}
			$tmp_sous_menu[$cpt_sous_menu]['sous_menu']=$tmp_sous_menu2;
			$tmp_sous_menu[$cpt_sous_menu]['niveau_sous_menu']=3;
			$cpt_sous_menu++;


			// Mes moyennes et appr�ciations
			$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/prepa_conseil/index1.php' , "texte"=>"Mes moyennes et appr�ciations");
			$tmp_sous_menu2=array();
			$cpt_sous_menu2=0;
			foreach($mes_groupes as $tmp_group) {
				$tmp_sous_menu2[$cpt_sous_menu2]['lien']='/prepa_conseil/index1.php?id_groupe='.$tmp_group['id'];
				$tmp_sous_menu2[$cpt_sous_menu2]['texte']=$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)';
				$cpt_sous_menu2++;
			}
			$tmp_sous_menu[$cpt_sous_menu]['sous_menu']=$tmp_sous_menu2;
			$tmp_sous_menu[$cpt_sous_menu]['niveau_sous_menu']=3;
			$cpt_sous_menu++;


			// Visualisation des moyennes d'une classe bulletins
			if((getSettingValue("GepiAccesMoyennesProf") == "yes") OR
			(getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
			(getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")) {
				$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/prepa_conseil/index2.php' , "texte"=>"Visualiser toutes les moyennes d'une classe");
				$tmp_sous_menu2=array();
				$cpt_sous_menu2=0;
				foreach($tmp_mes_classes as $key => $value) {
					$tmp_sous_menu2[$cpt_sous_menu2]['lien']='/prepa_conseil/index2.php?id_classe='.$key;
					$tmp_sous_menu2[$cpt_sous_menu2]['texte']=$value;
					$cpt_sous_menu2++;
				}
				$tmp_sous_menu[$cpt_sous_menu]['sous_menu']=$tmp_sous_menu2;
				$tmp_sous_menu[$cpt_sous_menu]['niveau_sous_menu']=3;
				$cpt_sous_menu++;
			}


			// Visualisation des bulletins simplifi�s
			$affiche_li_bull_simp="n";
			if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
				$affiche_li_bull_simp="y";
			}
			elseif(getSettingValue("GepiAccesBulletinSimplePP") == "yes") {
				$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
											j_eleves_professeurs jep,
											j_eleves_classes jec
										WHERE jep.login=jeg.login AND
												jec.login=jeg.login AND
												jec.periode=jeg.periode AND
												jep.professeur='".$_SESSION['login']."';";
				$res_test_affiche_bull_simp=mysql_num_rows(mysql_query($sql));
				//echo "$sql";
				if($res_test_affiche_bull_simp>0) {$affiche_li_bull_simp="y";}
			}

			if($affiche_li_bull_simp=="y") {
				$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/prepa_conseil/index3.php' , "texte"=>"Bulletins simplifi�s");
				$tmp_sous_menu2=array();
				$cpt_sous_menu2=0;
				foreach($tmp_mes_classes as $key => $value) {
					$tmp_sous_menu2[$cpt_sous_menu2]['lien']='/prepa_conseil/index3.php?id_classe='.$key;
					$tmp_sous_menu2[$cpt_sous_menu2]['texte']=$value;
					$cpt_sous_menu2++;
				}
				$tmp_sous_menu[$cpt_sous_menu]['sous_menu']=$tmp_sous_menu2;
				$tmp_sous_menu[$cpt_sous_menu]['niveau_sous_menu']=3;
				$cpt_sous_menu++;
			}


			// Visualisation graphique des bulletins
			$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/visualisation/affiche_eleve.php' , "texte"=>"Graphes");
			$tmp_sous_menu2=array();
			$cpt_sous_menu2=0;
			foreach($tmp_mes_classes as $key => $value) {
				$tmp_sous_menu2[$cpt_sous_menu2]['lien']='/visualisation/affiche_eleve.php?id_classe='.$key;
				$tmp_sous_menu2[$cpt_sous_menu2]['texte']=$value;
				$cpt_sous_menu2++;
			}
			$tmp_sous_menu[$cpt_sous_menu]['sous_menu']=$tmp_sous_menu2;
			$tmp_sous_menu[$cpt_sous_menu]['niveau_sous_menu']=3;
			$cpt_sous_menu++;



		$tbs_menu_prof[$compteur_menu]['sous_menu']=$tmp_sous_menu;
		$tbs_menu_prof[$compteur_menu]['niveau_sous_menu']=2;
		$compteur_menu++;
	}else{$barre_note = '';}

	//=======================================================
	// Module emploi du temps
	if (getSettingValue("autorise_edt_tous") == "y") {
		$tbs_menu_prof[$compteur_menu]=array("lien"=> '/edt_organisation/index_edt.php?visioedt=prof1&amp;login_edt='.$_SESSION["login"].'&amp;type_edt_2=prof' , "texte"=>"Emploi du tps");

		$tmp_sous_menu=array();
		$cpt_sous_menu=0;

		$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/edt_organisation/index_edt.php?visioedt=classe1' , "texte"=>"EDT classe");
		$cpt_sous_menu++;

		$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/edt_organisation/index_edt.php?visioedt=prof1' , "texte"=>"EDT prof");
		$cpt_sous_menu++;

		$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/edt_organisation/index_edt.php?visioedt=salle1' , "texte"=>"EDT salle");
		$cpt_sous_menu++;

		$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/edt_organisation/index_edt.php?visioedt=eleve1' , "texte"=>"EDT �l�ve");
		$cpt_sous_menu++;

		$tbs_menu_prof[$compteur_menu]['sous_menu']=$tmp_sous_menu;
		$tbs_menu_prof[$compteur_menu]['niveau_sous_menu']=2;

		$compteur_menu++;
	}else{$barre_edt = '';}

	//=======================================================
	// Module discipline
	if (getSettingValue("active_mod_discipline")=='y') {
	    $tbs_menu_prof[$compteur_menu]=array("lien"=> '/mod_discipline/index.php' , "texte"=>"Discipline");
		$compteur_menu++;
	} else {$barre_discipline = '';}

	//=======================================================
	// Module notanet
	if (getSettingValue("active_notanet") == "y") {
		$tbs_menu_prof[$compteur_menu]=array("lien"=> '/mod_notanet/index.php' , "texte"=>"Brevet");
		$compteur_menu++;
	}else{ $barre_notanet = '';}

	//=======================================================
	$tbs_menu_prof[$compteur_menu]=array("lien"=> '/groupes/visu_mes_listes.php' , "texte"=>"�l�ves");
	$tmp_sous_menu=array();
	$cpt_sous_menu=0;

	if (acces('/eleves/visu_eleve.php',$_SESSION['statut'])==1) {
		$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/eleves/visu_eleve.php' , "texte"=>"Consult.�l�ve");
		$cpt_sous_menu++;
	}
	//else{ $barre_consult_eleve = '';}

	if(getSettingValue('active_module_trombinoscopes')=='y') {
		$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/mod_trombinoscopes/trombinoscopes.php' , "texte"=>"Trombinoscope");
		$tmp_sous_menu2=array();
		$cpt_sous_menu2=0;
		foreach($mes_groupes as $tmp_group) {
			$tmp_sous_menu2[$cpt_sous_menu2]['lien']='/mod_trombinoscopes/trombino_pdf.php?classe=&groupe='.$tmp_group['id'].'&equipepeda=&discipline=&statusgepi=&affdiscipline=';
			$tmp_sous_menu2[$cpt_sous_menu2]['texte']=$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)';
			$cpt_sous_menu2++;
		}
		$tmp_sous_menu[$cpt_sous_menu]['sous_menu']=$tmp_sous_menu2;
		$tmp_sous_menu[$cpt_sous_menu]['niveau_sous_menu']=3;

		$cpt_sous_menu++;
	}
	$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/groupes/visu_mes_listes.php' , "texte"=>"Mes listes");
	$cpt_sous_menu++;

	$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/groupes/mes_listes.php' , "texte"=>"Mes listes CSV");
	$cpt_sous_menu++;

	$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> '/impression/impression_serie.php' , "texte"=>"Mes listes PDF");
	$cpt_sous_menu++;

	$tbs_menu_prof[$compteur_menu]['sous_menu']=$tmp_sous_menu;
	$tbs_menu_prof[$compteur_menu]['niveau_sous_menu']=2;

	$compteur_menu++;

	//=======================================================
	// plugin

	$menu_plugins=tbs_menu_plugins();
	if (count($menu_plugins)>0) {
		$tbs_menu_prof[$compteur_menu] = array('lien'=>"",'texte'=>"Plugins",'sous_menu'=>$menu_plugins,'niveau_sous_menu'=>2);
		$compteur_menu++; 
	}

	
	//=======================================================
	$tbs_menu_prof[$compteur_menu]=array("lien"=> '/utilisateurs/mon_compte.php' , "texte"=>"Mon compte");
	$compteur_menu++;
	
?>
