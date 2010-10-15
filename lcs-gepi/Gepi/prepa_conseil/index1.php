<?php
/*
 * $Id: index1.php 3323 2009-08-05 10:06:18Z crob $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisations files
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!isset($en_tete)) $en_tete = "yes";
if (!isset($stat)) $stat = "no";
if (!isset($larg_tab)) $larg_tab = 680;
if (!isset($bord)) $bord = 1;
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "nom");
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
}

if (count($current_group["classes"]["list"]) > 1) {
    $multiclasses = true;
} else {
    $multiclasses = false;
    $order_by = "nom";
}


if((isset($_POST['col_tri']))&&($_POST['col_tri']==1)) {
	$order_by = "nom";
}

//=====================================================
if ((isset($_POST['mode']))&&($_POST['mode']=='csv')) {

	$now = gmdate('D, d M Y H:i:s') . ' GMT';

	$chaine_titre="export";
	if(isset($current_group)) {
		//$chaine_titre=$current_group['name']."_".$current_group['description'];
		$chaine_titre=$current_group['name']."_".my_ereg_replace(",","_",$current_group['classlist_string']);
	}

	$nom_fic=$chaine_titre."_".$now.".csv";

	// Filtrer les caract�res dans le nom de fichier:
	$nom_fic=my_ereg_replace("[^a-zA-Z0-9_.-]","",remplace_accents($nom_fic,'all'));

	header('Content-Type: text/x-csv');
	header('Expires: ' . $now);
	// lem9 & loic1: IE need specific headers
	if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
		header('Content-Disposition: inline; filename="' . $nom_fic . '"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	} else {
		header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
		header('Pragma: no-cache');
	}

	$fd="";
	//$fd.=affiche_tableau_csv2($nb_lignes_tableau, $nb_col, $ligne1_csv, $col);

	$ligne1_csv=$_POST['ligne1_csv'];
	//$col_csv=$_POST['col_csv'];
	$lignes_csv=$_POST['lignes_csv'];

	$fd.=my_ereg_replace(";",",",my_ereg_replace("&#039;","'",html_entity_decode($ligne1_csv[1])));
	for($i=2;$i<=count($ligne1_csv);$i++) {
		$fd.=";".my_ereg_replace(";",",",my_ereg_replace("&#039;","'",html_entity_decode($ligne1_csv[$i])));
	}
	$fd.="\n";

	for($j=0;$j<count($lignes_csv);$j++) {
		$fd.=my_ereg_replace("&#039;","'",html_entity_decode($lignes_csv[$j])."\n");
	}

	echo $fd;
	die();
}
//=====================================================




include "../lib/periodes.inc.php";
//**************** EN-TETE *****************
if ($en_tete == "yes") $titre_page = "Visualisation des moyennes et appr�ciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

if (isset($_SESSION['chemin_retour'])) {$retour = $_SESSION['chemin_retour'];} else {$retour = "index1.php";}

if ($en_tete!="yes"){
	echo "<script type='text/javascript'>
	document.body.style.backgroundColor='white';
</script>\n";
}

if($_SESSION['statut']=='professeur'){
	//++++++++++++++++++++++++++++++++++++++++++++++++++
	// A FAIRE
	// TEST: Est-ce que le PP a acc�s aux appr�ciations ?
	//       Cr�er un droit dans Droits d'acc�s ?
	if(getSettingValue("GepiAccesBulletinSimplePP")=='yes') {
		$acces_pp="y";
	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++
}

if (!$current_group) {
    unset($_SESSION['chemin_retour']);
    echo "<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
    echo "<p>Votre choix :</p>\n";
    //$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    //$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	if($_SESSION['statut']=='scolarite'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	elseif($_SESSION['statut']=='professeur'){
		$appel_donnees=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
	}
	elseif($_SESSION['statut']=='cpe'){
		$appel_donnees=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe");
	}
	elseif($_SESSION['statut']=='secours'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c ORDER BY classe");
	}

	$lignes = mysql_num_rows($appel_donnees);
	//echo "\$lignes=$lignes<br />";

	if($lignes==0) {
		echo "<p>Aucune classe ne vous est attribu�e.<br />Contactez l'administrateur pour qu'il effectue le param�trage appropri� dans la Gestion des classes.</p>\n";
	}
	else {
		$nb_class_par_colonne=round($lignes/3);
		/*
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='center'>\n";
			echo "<td>\n";
		*/

		echo "<table border='0'>\n";

		$i = 0;
		while($i < $lignes) {
		/*
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td>\n";
		}
		*/

			$id_classe = mysql_result($appel_donnees, $i, "id");
			$aff_class = 'no';
			$groups = get_groups_for_class($id_classe);
			//echo "\$id_classe=$id_classe et count(\$groups)=".count($groups)."<br />";

			foreach($groups as $group) {
				$temoin_pp="no";
				$flag2 = "no";
				//if ($_SESSION['statut']!='scolarite') {
				// Seuls les comptes scolarite, professeur et secours ont acc�s � cette page.
				if ($_SESSION['statut']=='professeur') {
					$test = mysql_query("SELECT count(*) FROM j_groupes_professeurs
					WHERE (id_groupe='" . $group["id"]."' and login = '" . $_SESSION["login"] . "')");
					if (mysql_result($test, 0) == 1) {$flag2 = 'yes';}

					if($acces_pp=="y") {
						$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jec.login=jep.login AND jec.id_classe=jep.id_classe AND jep.professeur='".$_SESSION['login']."' AND jec.id_classe='$id_classe';";
						$test=mysql_query($sql);
						if(mysql_num_rows($test)>0) {
							$flag2 = 'yes';
							$temoin_pp="yes";
						}
					}
				} else {
					$flag2 = 'yes';
				}

				if ($flag2 == "yes") {
					$display_class = mysql_result($appel_donnees, $i, "classe");
					//echo "<span class='norme'>";
					//if ($aff_class == 'no') {echo "<span class='norme'><b>$display_class</b> : ";$aff_class = 'yes';}
					//if ($aff_class == 'no') {echo "<b>$display_class</b> : ";$aff_class = 'yes';}
					//echo "<a href='index1.php?id_groupe=" . $group["id"] . "'>" . $group["description"] . "</a> - ";
					//echo "<a href='index1.php?id_groupe=" . $group["id"] . "'>" . htmlentities($group["description"]) . "</a></span> - \n";


					if ($aff_class == 'no') {
						echo "<tr>\n";
						echo "<td valign='top'>\n";
						echo "<span class='norme'>";
						echo "<b>$display_class</b> : ";
						echo "</span>";
						echo "</td>\n";
						echo "<td>\n";

						$aff_class = 'yes';
					}

					echo "<span class='norme'>";
					echo "<a href='index1.php?id_groupe=" . $group["id"] . "'>" . htmlentities($group["description"]) . " </a>\n";

					// pas de nom si c'est un prof qui demande la page.
					//if ($_SESSION['statut']!='professeur') {
					if(($_SESSION['statut']!='professeur')||($temoin_pp=="yes")) {
						$id_groupe_en_cours = $group["id"];
						//recherche profs du groupe
						$sql_prof_groupe = "SELECT jgp.login,u.nom,u.prenom FROM j_groupes_professeurs jgp,utilisateurs u WHERE jgp.id_groupe='$id_groupe_en_cours' AND u.login=jgp.login";
						$result_prof_groupe=mysql_query($sql_prof_groupe);
						echo "(";
						$cpt=0;
						$nb_profs = mysql_num_rows($result_prof_groupe);
						while($lig_prof=mysql_fetch_object($result_prof_groupe)){
							if (($nb_profs !=1) AND ($cpt<$nb_profs-1)){
							echo "$lig_prof->nom ".ucfirst(strtolower($lig_prof->prenom))." - ";
							} else {
							echo "$lig_prof->nom ".ucfirst(strtolower($lig_prof->prenom));
							}
							$cpt++;
						}
						echo ")";
					}

					echo "</span>\n";
					echo "<br />\n";
				}
			}
			//if ($flag2 == 'yes') {echo "</span><br /><br />\n";}
			//if ($flag2 == 'yes') {echo "<br /><br />\n";}
			if ($flag2 == 'yes') {echo "</td></tr>\n";}
			$i++;
		}
		echo "</table>\n";
	}
        /*
	echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
	*/

} else if (!isset($choix_visu)) {
	echo "<form enctype=\"multipart/form-data\" name= \"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"get\">\n";
    echo "<p class='bold'>\n";
	echo "<a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

	if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
		if($_SESSION['statut']=='professeur') {
			$login_prof_groupe_courant=$_SESSION["login"];
		}
		else {
			$tmp_current_group=get_group($id_groupe);

			$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
		}

		$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis mati�re");

		if(!empty($tab_groups)) {

			$chaine_options_classes="";

			$num_groupe=-1;
			$nb_groupes_suivies=count($tab_groups);

			//echo "count(\$tab_groups)=".count($tab_groups)."<br />";

			$id_grp_prec=0;
			$id_grp_suiv=0;
			$temoin_tmp=0;
			//foreach($tab_groups as $tmp_group) {
			for($loop=0;$loop<count($tab_groups);$loop++) {
				if($tab_groups[$loop]['id']==$id_groupe){
					$num_groupe=$loop;

					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";

					$temoin_tmp=1;
					if(isset($tab_groups[$loop+1])){
						$id_grp_suiv=$tab_groups[$loop+1]['id'];

						//$chaine_options_classes.="<option value='".$tab_groups[$loop+1]['id']."'>".$tab_groups[$loop+1]['name']." (".$tab_groups[$loop+1]['classlist_string'].")</option>\n";
					}
					else{
						$id_grp_suiv=0;
					}
				}
				else {
					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
				}

				if($temoin_tmp==0){
					$id_grp_prec=$tab_groups[$loop]['id'];

					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
				}
			}
			// =================================

			/*
			if(isset($id_grp_prec)){
				if($id_grp_prec!=0){
					echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_num=$periode_num";
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement pr�c�dent</a>";
				}
			}
			*/

			if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {
				/*
				echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";
				*/

				echo " | <select name='id_groupe' onchange=\"document.forms['form1'].submit();\">\n";
				//echo "<select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
				echo $chaine_options_classes;
				echo "</select>\n";
			}

			/*
			if(isset($id_grp_suiv)){
				if($id_grp_suiv!=0){
					echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num";
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement suivant</a>";
					}
			}
			*/
		}
		// =================================
	}

	echo "</p>\n";
	echo "</form>\n";

	$test_acces_pp="n";
	if(($_SESSION['statut']=='professeur')&&($acces_pp=="y")) {
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_groupes jeg WHERE jeg.login=jep.login AND jep.professeur='".$_SESSION['login']."' AND jeg.id_groupe='$id_groupe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$test_acces_pp="y";
		}
	}

    if ((!(check_prof_groupe($_SESSION['login'],$id_groupe))) and ($_SESSION['statut']!='scolarite') and ($_SESSION['statut']!='secours') and ($test_acces_pp=="n")) {
        echo "<p>Vous n'�tes pas dans cette classe le professeur de la mati�re choisie !</p>\n";
        echo "<p><a href='index1.php'>Retour � l'accueil</a></p>\n";
        die();
    }

    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire\">";
    echo "<p class='bold'>Groupe : " . htmlentities($current_group["description"]) ." " . htmlentities($current_group["classlist_string"]) . " | Mati�re : " . htmlentities($current_group["matiere"]["nom_complet"]) . "&nbsp;&nbsp;<input type='submit' value='Valider' /></p>\n";
    echo "<p>Choisissez les donn�es � imprimer (vous pouvez cocher plusieurs cases) : </p>\n";
    $i="1";
    while ($i < $nb_periode) {
        $name = "visu_note_".$i;
        echo "<p><input type='checkbox' name='$name' id='$name' value='yes' /><label for='$name' style='cursor: pointer;'>".ucfirst($nom_periode[$i])." - Extraire les moyennes</label></p>\n";
    $i++;
    }
    $i="1";
    while ($i < $nb_periode) {
            $name = "visu_app_".$i;
            echo "<p><input type='checkbox' name='$name' id='$name' value='yes' /><label for='$name' style='cursor: pointer;'>".ucfirst($nom_periode[$i])." - Extraire les appr�ciations</label></p>\n";
    $i++;
    }

	//==========================================
	// MODIF: boireaus 20080407
	// Le rang doit-il �tre affich�
	// On autorise le prof � obtenir les rangs m�me si on ne les met pas sur le bulletin
	// Et le calcul des rangs est effectu� apr�s soumission du formulaire si l'option rang est coch�e
	$aff_rang="y";
	$affiche_categories="n";
	/*
	for($i=0;$i<count($current_group["classes"]["list"]);$i++) {
		$sql="SELECT display_rang FROM classes WHERE id='".$current_group["classes"]["list"][$i]."';";
		$test_rang=mysql_query($sql);
		$lig_rang=mysql_fetch_object($test_rang);
		if($lig_rang->display_rang=="y") {
			$id_classe=$current_group["classes"]["list"][$i];

			$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

			$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe';";
			$res_per=mysql_query($sql);
			while($lig_per=mysql_fetch_object($res_per)) {
				$periode_num=$lig_per->num_periode;
				include("../lib/calcul_rang.inc.php");
			}
		}
		else {
			$aff_rang="n";
			break;
		}
	}
	*/
	if($aff_rang=="y") {
	    echo "<p><input type='checkbox' name='afficher_rang' id='afficher_rang' value='yes' /><label for='afficher_rang' style='cursor: pointer;'>Afficher le rang des �l�ves.</label></p>\n";
	}
	//==========================================

    echo "<p><input type='checkbox' name='stat' id='stat' value='yes' /><label for='stat' style='cursor: pointer;'>Afficher les statistiques sur les moyennes extraites (moyenne g�n�rale, pourcentages, ...)</label></p>\n";
    if ($multiclasses) {
        echo "<p><input type='radio' name='order_by' id='order_by_nom' value='nom' checked /><label for='order_by_nom' style='cursor: pointer;'> Classer les �l�ves par ordre alphab�tique </label>";
        echo "<br/><input type='radio' name='order_by' id='order_by_classe' value='classe' /><label for='order_by_classe' style='cursor: pointer;'> Classer les �l�ves par classe</label></p>";
    }
    echo "<input type='submit' value='Valider' />\n";
    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    echo "</form>\n";
} else {


	$test_acces_pp="n";
	if(($_SESSION['statut']=='professeur')&&($acces_pp=="y")) {
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_groupes jeg WHERE jeg.login=jep.login AND jep.professeur='".$_SESSION['login']."' AND jeg.id_groupe='$id_groupe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$test_acces_pp="y";
		}
	}

    if ((!(check_prof_groupe($_SESSION['login'],$id_groupe))) and ($_SESSION['statut']!='scolarite') and ($_SESSION['statut']!='secours') and ($test_acces_pp=="n")) {
        echo "<p>Vous n'�tes pas dans cette classe le professeur de la mati�re choisie !</p>\n";
        echo "<p><a href='index1.php'>Retour � l'accueil</a></p>\n";
        die();
    }


    $nombre_eleves = count($current_group["eleves"]["all"]["list"]);
	//echo "\$nombre_eleves=$nombre_eleves<br />";

    // On commence par mettre la liste dans l'ordre souhait�
    if ($order_by != "classe") {
        $liste_eleves = $current_group["eleves"]["all"]["list"];
    } else {
        // Ici, on trie par classe
        // On va juste cr�er une liste des �l�ves pour chaque classe
        $tab_classes = array();
        foreach($current_group["classes"]["list"] as $classe_id) {
            $tab_classes[$classe_id] = array();
        }
        // On passe maintenant �l�ve par �l�ve et on les met dans la bonne liste selon leur classe
        foreach($current_group["eleves"]["all"]["list"] as $eleve_login) {
            $classe = $current_group["eleves"]["all"]["users"][$eleve_login]["classe"];
            $tab_classes[$classe][] = $eleve_login;
			//echo "$eleve_login ";
        }
		//echo "<br />";
        // On met tout �a � la suite
        $liste_eleves = array();
        foreach($current_group["classes"]["list"] as $classe_id) {
            $liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
        }
    }

	//debug_var();

	//==========================================
	// MODIF: boireaus 20080407
	$nb_eleves_avant_include=$nombre_eleves;
	// Le rang doit-il �tre affich�
	// On autorise le prof � obtenir les rangs m�me si on ne les met pas sur le bulletin
	$aff_rang="n";
	if((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes")) {
		$aff_rang="y";
		$affiche_categories="n";
		for($i=0;$i<count($current_group["classes"]["list"]);$i++) {
			/*
			$sql="SELECT display_rang FROM classes WHERE id='".$current_group["classes"]["list"][$i]."';";
			$test_rang=mysql_query($sql);
			$lig_rang=mysql_fetch_object($test_rang);
			if($lig_rang->display_rang=="y") {
			*/
				$id_classe=$current_group["classes"]["list"][$i];

				$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

				$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe';";
				$res_per=mysql_query($sql);
				while($lig_per=mysql_fetch_object($res_per)) {
					$periode_num=$lig_per->num_periode;
					include("../lib/calcul_rang.inc.php");
				}
			/*
			}
			else {
				$aff_rang="n";
				break;
			}
			*/
		}
	}
	$nombre_eleves=$nb_eleves_avant_include;
	//echo "\$nombre_eleves=$nombre_eleves<br />";

	//==========================================


    $nb_col = 1;
    if ($multiclasses) $nb_col++;

	//==========================================
	// MODIF: boireaus 20080407
	//if ($aff_rang=="y") {$nb_col++;}
	//==========================================

    $total_notes = 0;
    $min_notes = '-';
    $max_notes = '-';
    $pourcent_i8 = 0;
    $pourcent_se12 = 0;
    $pourcent_se8_ie12 = 0;
    $i = "0";
    $eleve_login = null;
    foreach($liste_eleves as $eleve_login) {
		//echo "$eleve_login ";
        // La variable affiche_ligne teste si on affiche une ligne ou non : si l'�l�ve suit la mati�re pour au moins une p�riode, on affiche la ligne concernant l'�l�ve. Si l'�l�ve ne suit pas la mati�re pour aucune des p�riodes, on n'affiche pas la ligne conernant l'�l�ve.
        $affiche_ligne[$i] = 'no';
        $login_eleve[$i] = $eleve_login;
        $k=0;
		//echo "\$nb_periode=$nb_periode<br />";
        while ($k < $nb_periode) {
            $temp1 = "visu_note_".$k;
            $temp2 = "visu_app_".$k;
            if (isset($_POST[$temp1]) or isset($_POST[$temp2]) or isset($_GET[$temp1]) or isset($_GET[$temp2])) {

                if (!in_array($eleve_login, $current_group["eleves"][$k]["list"])) {
                    $option[$i][$k] = "non";
                } else {
                    $option[$i][$k] = "oui";
                    $affiche_ligne[$i] = 'yes';
                }
				//echo "\$option[$i][$k]=".$option[$i][$k]."<br />";
            }
            $k++;
        }
		//echo "$eleve_login : \$affiche_ligne[$i]=$affiche_ligne[$i]<br />";
        $i++;
    }
    //
    // Calcul du nombre de colonnes � afficher et d�finition de la premi�re ligne � afficher
    //
    $ligne1[1] = "Nom Pr�nom";
    $ligne1_csv[1] = "Nom Pr�nom";
    if ($multiclasses) {
		$ligne1[2] = "Classe";
		$ligne1_csv[2] = "Classe";
    }
	$k = 1;
//    if (isset($_POST['stat']) or isset($_GET['stat'])) $only_stats = 'yes'; else $only_stats = 'no';
    while ($k < $nb_periode) {

		//==========================================
		// MODIF: boireaus 20080407
		if ($aff_rang=="y") {
			$temoin_periode=0;

			$temp = "visu_note_".$k;
			if (isset($_POST[$temp]) or isset($_GET[$temp])) {
				$temoin_periode++;
			}

			$temp = "visu_app_".$k;
			if (isset($_POST[$temp]) or isset($_GET[$temp])) {
				$temoin_periode++;
			}

			if($temoin_periode>0) {
				$nb_col++;
				$ligne1[$nb_col] = "Rang P".$k;
				$ligne1_csv[$nb_col] = "Rang P".$k;
			}
		}
		//==========================================

        $temp = "visu_note_".$k;
        if (isset($_POST[$temp]) or isset($_GET[$temp])) {
            $nb_col++;
//            $only_stats = 'no';
            $ligne1[$nb_col] = "Note P".$k;
            $ligne1_csv[$nb_col] = "Note P".$k;
        }
        $temp = "visu_app_".$k;
        if (isset($_POST[$temp]) or isset($_GET[$temp])) {
            $nb_col++;
            $ligne1[$nb_col] = "Appr�ciation P".$k;
            $ligne1_csv[$nb_col] = "Appr�ciation P".$k;
        }
        $k++;
    }
    if ($stat == "yes") {
        $nb_col++;
        $ligne1[$nb_col] = "Moyenne";
        $ligne1_csv[$nb_col] = "Moyenne";
		/*
		if($aff_rang=="y") {
			$nb_col++;
			$ligne1[$nb_col] = "Rang";
			$ligne1_csv[$nb_col] = "Rang";
		}
		*/
    }
    $i = 0;
    $nb_lignes = '0';
    $nb_notes = '0';
	$rg=array();
	//echo "\$nombre_eleves=$nombre_eleves<br />\n";
    while($i < $nombre_eleves) {
		//echo "\$login_eleve[$i]=$login_eleve[$i] et \$affiche_ligne[$i]=$affiche_ligne[$i]<br />";
        if ($affiche_ligne[$i] == 'yes') {
            // Calcul de la moyenne
            if ($stat == "yes") {
				$col[$nb_col][$nb_lignes] = '';
				$col_csv[$nb_col][$nb_lignes] = '';
			}
            for ($k=1;$k<$nb_periode;$k++) {
                if (in_array($login_eleve[$i], $current_group["eleves"][$k]["list"])) {
                    $col[1][$nb_lignes] = $current_group["eleves"][$k]["users"][$login_eleve[$i]]["prenom"] . " " . $current_group["eleves"][$k]["users"][$login_eleve[$i]]["nom"];
                    $col_csv[1][$nb_lignes] = $current_group["eleves"][$k]["users"][$login_eleve[$i]]["prenom"] . " " . $current_group["eleves"][$k]["users"][$login_eleve[$i]]["nom"];
                    if ($multiclasses) $col[2][$nb_lignes] = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$login_eleve[$i]]["classe"]]["classe"];
                    if ($multiclasses) $col_csv[2][$nb_lignes] = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$login_eleve[$i]]["classe"]]["classe"];


					//echo "<p>\$col_csv[1][$nb_lignes]=".$col_csv[1][$nb_lignes]."<br />";

                    break;
                }
            }

            $k=1;
            $j=1;
            if ($multiclasses) $j++;
            while ($k < $nb_periode) {

				//==========================================
				// MODIF: boireaus 20080407
				if ($aff_rang=="y") {
					$temoin_periode=0;

					$temp = "visu_note_".$k;
					if (isset($_POST[$temp]) or isset($_GET[$temp])) {
						$temoin_periode++;
					}

					$temp = "visu_app_".$k;
					if (isset($_POST[$temp]) or isset($_GET[$temp])) {
						$temoin_periode++;
					}

					if($temoin_periode>0) {
						$j++;
						$note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$login_eleve[$i]' AND id_groupe = '".$current_group["id"] . "' AND periode='$k')");
						if($note_query) {
							$_statut = @mysql_result($note_query, 0, "statut");
							//$note = @mysql_result($note_query, 0, "note");
							$rang = @mysql_result($note_query, 0, "rang");
							if ($option[$i][$k] == "non") {
								//$col[$j][$nb_lignes] = "-";
								//$col[$j][$nb_lignes] = "<span style='text-align:center;'>-</span>";
								$col[$j][$nb_lignes] = "<center>-</center>";
								$col_csv[$j][$nb_lignes] = "-";
							} else {
								//$col[$j][$nb_lignes] = $rang;
								//$col[$j][$nb_lignes] = "<span style='text-align:center;'>".$rang."</span>";
								if($rang!="") {
									$col[$j][$nb_lignes] = "<center>".$rang."</center>";
									$col_csv[$j][$nb_lignes] = $rang;
								}
								else {
									$col[$j][$nb_lignes] = "<center>-</center>";
									$col_csv[$j][$nb_lignes] = "-";
								}
							}
						}
						else {
							$col[$j][$nb_lignes] = "<center>-</center>";
							$col_csv[$j][$nb_lignes] = "-";
						}
					}
				}
				//==========================================

                $temp = "visu_note_".$k;
                if (isset($_POST[$temp]) or isset($_GET[$temp])) {
                    $j++;
                    $note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$login_eleve[$i]' AND id_groupe = '".$current_group["id"] . "' AND periode='$k')");
                    $_statut = @mysql_result($note_query, 0, "statut");
                    $note = @mysql_result($note_query, 0, "note");
                    if ($option[$i][$k] == "non") {
                        $col[$j][$nb_lignes] = "<center>-</center>";
						$col_csv[$j][$nb_lignes] = "-";
                    } else {
                        if ($_statut != '') {
                            //$col[$j][$nb_lignes] = $_statut;
                            $col[$j][$nb_lignes] = "<center>".$_statut."</center>";
							$col_csv[$j][$nb_lignes] = $_statut;
						} else {
                            if ($note != '') {
                                //$col[$j][$nb_lignes] = number_format($note,1,',','');
								$col[$j][$nb_lignes] = "<center>".number_format($note,1,',','')."</center>";
								$col_csv[$j][$nb_lignes] = number_format($note,1,',','');
                                if ($stat == "yes") {
                                    $col[$nb_col][$nb_lignes] += $note;
                                    if (!isset($nb_note[$nb_lignes])) $nb_note[$nb_lignes]=1; else $nb_note[$nb_lignes]++;
                                }
                            } else {
                                //$col[$j][$nb_lignes] = '-';
								$col[$j][$nb_lignes] = "<center>-</center>";
								$col_csv[$j][$nb_lignes] = "-";
                            }
                        }
                    }

					//echo "\$col_csv[$j][$nb_lignes]=".$col_csv[$j][$nb_lignes]."<br />";

                }
                $temp = "visu_app_".$k;
                if (isset($_POST[$temp]) or isset($_GET[$temp])) {

                    $j++;
                    $app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$login_eleve[$i]' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
                    $app = @mysql_result($app_query, 0, "appreciation");

					//++++++++++++++++++++++++
					// Modif d'apr�s F.Boisson
					// notes dans appreciation
					$sql="SELECT cnd.note, cd.note_sur FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$login_eleve[$i]."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$k' AND cnd.statut='';";
					$result_nbct=mysql_query($sql);
					$string_notes='';
					if ($result_nbct ) {
						while ($snnote =  mysql_fetch_assoc($result_nbct)) {
							if ($string_notes != '') $string_notes .= ", ";
							$string_notes .= $snnote['note'];
							if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
								$string_notes .= "/".$snnote['note_sur'];
							}
						}
					}
					$app = str_replace('@@Notes', $string_notes,$app);
					//++++++++++++++++++++++++


                    if ($app != '') {
						// =========================================
						// MODIF: boireaus
									//$col[$j][$nb_lignes] = $app;
						if((strstr($app,">"))||(strstr($app,"<"))){
							$col[$j][$nb_lignes] = $app;
						}
						else{
							$col[$j][$nb_lignes] = nl2br($app);
						}
						$col_csv[$j][$nb_lignes] = $app;
						// =========================================
                    } else {
                        $col[$j][$nb_lignes] = '-';
						$col_csv[$j][$nb_lignes] = "-";
                    }
                }
                $k++;
            }
            if ($stat == "yes") {
                if ($col[$nb_col][$nb_lignes] != '') {
                    // moyenne de chaque �l�ve
                    $temp = round($col[$nb_col][$nb_lignes]/$nb_note[$nb_lignes],1);
                    $col[$nb_col][$nb_lignes] = "<center>".number_format($temp,1,',','')."</center>";
					$col_csv[$nb_col][$nb_lignes] = number_format($temp,1,',','');
                    // Total des moyennes de chaque �l�ve
                    $total_notes += $temp;
                    $nb_notes++;
                    if ($min_notes== '-') $min_notes = 20;
                    $min_notes = min($min_notes,$temp);
                    $max_notes = max($max_notes,$temp);
                    if ($temp < 8) $pourcent_i8++;
                    if ($temp >= 12) $pourcent_se12++;
                } else {
                    //$col[$nb_col][$nb_lignes] = '-' ;
                    $col[$nb_col][$nb_lignes] = '<center>-</center>' ;
                    $col_csv[$nb_col][$nb_lignes] = '-' ;
                }
            }
            $nb_lignes++;

			//$rg[]=$i;
        }

		$rg[$i]=$i;

        $i++;
    }

	//==============================================
	// AJOUT: boireaus 20080418
	// Calculs pour la colonne RANG sur la moyenne des p�riodes
	if(($stat=="yes")&&($aff_rang=="y")) {
		//$tmp_tab=$col[$nb_col];
		//for($loop=0;$loop<count($tmp_tab);$loop++) {
			//echo "\$tmp_tab[$loop]=".$tmp_tab[$loop]."<br />";

		unset($rg);

		for($loop=0;$loop<count($col[$nb_col]);$loop++) {
			//$tmp_tab[$loop]=$col[$nb_col][$loop];
			//$tmp_tab[$loop]=$col_csv[$nb_col][$loop];
			$tmp_tab[$loop]=my_ereg_replace(",",".",$col_csv[$nb_col][$loop]);
			//echo "\$tmp_tab[$loop]=".$tmp_tab[$loop]."<br />";

			$rg[$loop]=$loop;
		}

		array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
		/*
		echo "count(\$rg)=".count($rg)."<br />";
		for($loop=0;$loop<count($rg);$loop++) {
			echo "\$rg[$loop]=".$rg[$loop]."<br />";
		}
		*/

		$i=0;
		$rang_prec = 1;
		$note_prec='';
		//while ($i < $nombre_eleves) {
		while($i < count($col_csv[1])) {
			$ind = $rg[$i];
			if ($tmp_tab[$i] == "-") {
				//$rang_gen = '0';
				$rang_gen = '-';
			}
			else {
				if ($tmp_tab[$i] == $note_prec) {
					$rang_gen = $rang_prec;
				}
				else {
					$rang_gen = $i+1;
				}
				$note_prec = $tmp_tab[$i];
				$rang_prec = $rang_gen;
			}

			//$col[$nb_col+1][$ind]="ind=$ind, i=$i et rang_gen=$rang_gen";
			$col[$nb_col+1][$ind]="<center>".$rang_gen."</center>";
			$col_csv[$nb_col+1][$ind]=$rang_gen;

			$i++;
		}
	}
	//==============================================


	//=========================
	// MODIF: boireaus 20080421
	// Pour permettre de trier autrement...
	if((isset($_POST['col_tri']))&&($_POST['col_tri']!='')) {
		//echo "\$_POST['col_tri']=".$_POST['col_tri']."<br />";
		$col_tri=$_POST['col_tri'];

		$nb_colonnes=$nb_col;
		if(($stat=="yes")&&($aff_rang=="y")) {
			$nb_colonnes=$nb_col+1;
		}

		// V�rifier si $col_tri est bien un entier compris entre 0 et $nb_col ou $nb_col+1
		if((strlen(my_ereg_replace("[0-9]","",$col_tri))==0)&&($col_tri>0)&&($col_tri<=$nb_colonnes)) {
			//echo "<table>";
			//echo "<tr><td valign='top'>";
			unset($tmp_tab);
			//for($loop=0;$loop<count($col[$col_tri]);$loop++) {
			for($loop=0;$loop<count($col_csv[1]);$loop++) {
				// Il faut le POINT au lieu de la VIRGULE pour obtenir un tri correct sur les notes
				$tmp_tab[$loop]=my_ereg_replace(",",".",$col_csv[$col_tri][$loop]);
				//echo "\$tmp_tab[$loop]=".$tmp_tab[$loop]."<br />";
			}
			/*
			echo "</td>";
			echo "<td valign='top'>";

			$i=0;
			while($i < $nombre_eleves) {
				echo $col_csv[1][$i]."<br />";
				$i++;
			}
			echo "</td>";
			echo "<td valign='top'>";
			*/

			$i=0;
			unset($rg);
			//while($i < $nombre_eleves) {
			while($i < count($col_csv[1])) {
				$rg[$i]=$i;
				$i++;
			}

			//echo "count(\$rg)=".count($rg)."<br />";
			//echo "count(\$tmp_tab)=".count($tmp_tab)."<br />";
			// Tri du tableau avec stockage de l'ordre dans $rg d'apr�s $tmp_tab
			array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);

			/*
			$i=0;
			while($i < $nombre_eleves) {
				echo "\$rg[$i]=".$rg[$i]."<br />";
				$i++;
			}
			echo "</td>";
			echo "<td valign='top'>";
			*/

			// On utilise des tableaux temporaires le temps de la r�affectation dans l'ordre
			$tmp_col=array();
			$tmp_col_csv=array();

			$i=0;
			$rang_prec = 1;
			$note_prec='';
			//while ($i < $nombre_eleves) {
			while ($i < count($col_csv[1])) {
				$ind = $rg[$i];
				if ($tmp_tab[$i] == "-") {
					//$rang_gen = '0';
					$rang_gen = '-';
				}
				else {
					if ($tmp_tab[$i] == $note_prec) {
						$rang_gen = $rang_prec;
					}
					else {
						$rang_gen = $i+1;
					}
					$note_prec = $tmp_tab[$i];
					$rang_prec = $rang_gen;
				}

				//$col[$nb_col+1][$ind]="ind=$ind, i=$i et rang_gen=$rang_gen";
				for($m=1;$m<=$nb_colonnes;$m++) {
					/*
					echo "\$tmp_col[$m][$ind]=\$col[$m][$i]=".$col[$m][$i]."<br />";
					$tmp_col[$m][$ind]="<center>".$col[$m][$i]."</center>";
					$tmp_col_csv[$m][$ind]=$rang_gen;
					*/
					//echo "\$tmp_col[$m][$i]=\$col[$m][$ind]=".$col[$m][$ind]."<br />";
					$tmp_col[$m][$i]=$col[$m][$ind];
					$tmp_col_csv[$m][$ind]=$col_csv[$m][$ind];

				}
				$i++;
			}
			//echo "</td></tr>";
			//echo "</table>";

			// On r�affecte les valeurs dans le tableau initial � l'aide du tableau temporaire
			if((isset($_POST['sens_tri']))&&($_POST['sens_tri']=="inverse")) {
				for($m=1;$m<=$nb_colonnes;$m++) {
					//for($i=0;$i<$nombre_eleves;$i++) {
					for($i=0;$i<count($col_csv[1]);$i++) {
						//$col[$m][$i]=$tmp_col[$m][$nombre_eleves-1-$i];
						//$col_csv[$m][$i]=$tmp_col_csv[$m][$nombre_eleves-1-$i];
						$col[$m][$i]=$tmp_col[$m][count($col_csv[1])-1-$i];
						$col_csv[$m][$i]=$tmp_col_csv[$m][count($col_csv[1])-1-$i];
					}
				}
			}
			else {
				for($m=1;$m<=$nb_colonnes;$m++) {
					$col[$m]=$tmp_col[$m];
					$col_csv[$m]=$tmp_col_csv[$m];
				}
			}
		}
	}
	//=========================


    //
    // On teste s'il y a des moyennes, min et max � calculer :
    //
    $k = 1;
    $test = 0;
    while ($k < $nb_periode) {
        $temp = "visu_note_".$k;
        if (isset($_POST[$temp]) or isset($_GET[$temp])) {$test = 1;}
        $k++;
    }
    //
    // S'il y a des moyennes, min et max � calculer, on le fait :
    //
    if ($test == 1) {
        $k=1;
        $j=1;
        if ($multiclasses) $j++;
        while ($k < $nb_periode) {
			if ($aff_rang=="y") {
				$temoin_periode=0;

				$temp = "visu_note_".$k;
				if (isset($_POST[$temp]) or isset($_GET[$temp])) {
					$temoin_periode++;
				}

				$temp = "visu_app_".$k;
				if (isset($_POST[$temp]) or isset($_GET[$temp])) {
					$temoin_periode++;
				}

				if($temoin_periode>0) {
					$j++;
                    //$col[$j][$nb_lignes] = '-';
					//$col[$j][$nb_lignes+1] = '-';
					//$col[$j][$nb_lignes+2] = '-';
					$col[$j][$nb_lignes] = "<center>-</center>";
					$col[$j][$nb_lignes+1] = "<center>-</center>";
					$col[$j][$nb_lignes+2] = "<center>-</center>";
                    $col_csv[$j][$nb_lignes] = '-' ;
                    $col_csv[$j][$nb_lignes+1] = '-' ;
                    $col_csv[$j][$nb_lignes+2] = '-' ;
				}
			}

            $temp = "visu_note_".$k;
            if (isset($_POST[$temp]) or isset($_GET[$temp])) {

                $j++;
                $call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (id_groupe='$id_groupe' AND statut ='' AND periode='$k')");
                $call_max = mysql_query("SELECT max(note) note_max FROM matieres_notes WHERE (id_groupe='$id_groupe' AND statut ='' AND periode='$k')");
                $call_min = mysql_query("SELECT min(note) note_min FROM matieres_notes WHERE (id_groupe='$id_groupe' AND statut ='' AND periode='$k')");
                $temp = @mysql_result($call_moyenne, 0, "moyenne");
                if ($temp != '') {
                    //$col[$j][$nb_lignes] = number_format($temp,1,',','');
					$col[$j][$nb_lignes] = "<center>".number_format($temp,1,',','')."</center>";
                    $col_csv[$j][$nb_lignes] = number_format($temp,1,',','') ;
                } else {
                    //$col[$j][$nb_lignes] = '-';
					$col[$j][$nb_lignes] = "<center>-</center>";
                    $col_csv[$j][$nb_lignes] = '-' ;
                }
                $temp = @mysql_result($call_min, 0, "note_min");
                if ($temp != '') {
                    //$col[$j][$nb_lignes+1] = number_format($temp,1,',','');
					$col[$j][$nb_lignes+1] = "<center>".number_format($temp,1,',','')."</center>";
                    $col_csv[$j][$nb_lignes+1] = number_format($temp,1,',','') ;
                } else {
                    //$col[$j][$nb_lignes+1] = '-';
					$col[$j][$nb_lignes+1] = "<center>-</center>";
                    $col_csv[$j][$nb_lignes+1] = '-' ;
                }
                $temp = @mysql_result($call_max, 0, "note_max");
                if ($temp != '') {
                    //$col[$j][$nb_lignes+2] = number_format($temp,1,',','');
					$col[$j][$nb_lignes+2] = "<center>".number_format($temp,1,',','')."</center>";
                    $col_csv[$j][$nb_lignes+2] = number_format($temp,1,',','') ;
                } else {
                    //$col[$j][$nb_lignes+2] = '-';
					$col[$j][$nb_lignes+2] = "<center>-</center>";
                    $col_csv[$j][$nb_lignes+2] = '-' ;
                }
            }
            $temp = "visu_app_".$k;
            if (isset($_POST[$temp]) or isset($_GET[$temp])) {
                $j++;
                $col[$j][$nb_lignes] = '-';
                $col[$j][$nb_lignes+1] = '-';
                $col[$j][$nb_lignes+2] = '-';
                $col_csv[$j][$nb_lignes] = '-';
                $col_csv[$j][$nb_lignes+1] = '-';
                $col_csv[$j][$nb_lignes+2] = '-';
            }
            $k++;
        }
        if ($stat == "yes") {
            // moyenne g�n�rale de la classe
            if ($total_notes != 0) {
				$col[$nb_col][$nb_lignes] ="<center>".number_format(round($total_notes/$nb_notes,1),1,',','')."</center>";
				$col_csv[$nb_col][$nb_lignes] =number_format(round($total_notes/$nb_notes,1),1,',','');

				$col[$nb_col][$nb_lignes+1] = "<center>".number_format($min_notes,1,',','')."</center>";
				$col_csv[$nb_col][$nb_lignes+1] = number_format($min_notes,1,',','');
				$col[$nb_col][$nb_lignes+2] = "<center>".number_format($max_notes,1,',','')."</center>";
				$col_csv[$nb_col][$nb_lignes+2] = number_format($max_notes,1,',','');
			}
			else {
				$col[$nb_col][$nb_lignes] = "<center>-</center>";
				$col_csv[$nb_col][$nb_lignes] = "-";

				$col[$nb_col][$nb_lignes+1] = "<center>-</center>";
				$col_csv[$nb_col][$nb_lignes+1] = "-";
				$col[$nb_col][$nb_lignes+2] = "<center>-</center>";
				$col_csv[$nb_col][$nb_lignes+2] = "-";
			}
			//$col_csv[$nb_col][$nb_lignes]=$col[$nb_col][$nb_lignes];

            //$moy_gen = $col[$nb_col][$nb_lignes];
            $moy_gen = $col_csv[$nb_col][$nb_lignes];

            //$col[$nb_col][$nb_lignes+1] = "<center>".$min_notes."</center>";
            //$col_csv[$nb_col][$nb_lignes+1] = $min_notes;
            //$col[$nb_col][$nb_lignes+2] = "<center>".$max_notes."</center>";
            //$col_csv[$nb_col][$nb_lignes+2] = $max_notes;

			if($nb_notes!=0){
				$pourcent_se8_ie12 = number_format(($nb_notes-$pourcent_se12-$pourcent_i8)*100/$nb_notes,1,',','');
				if ($pourcent_i8 != '-') $pourcent_i8 = number_format(round($pourcent_i8*100/$nb_notes,1),1,',','');
				if ($pourcent_se12 != '-') $pourcent_se12 = number_format(round($pourcent_se12*100/$nb_notes,1),1,',','');
			}
			else{
				$pourcent_se8_ie12="-";
				$pourcent_i8='-';
				$pourcent_se12='-';
			}
        }
    }
    if ($test == 1) {
        //$col[1][$nb_lignes] = '<center><b>Moyenne</b></center>';
        //$col[1][$nb_lignes+1] = '<center><b>Min.</b></center>';
        //$col[1][$nb_lignes+2] = '<center><b>Max.</b></center>';
        $col[1][$nb_lignes] = '<b>Moyenne</b>';
        $col[1][$nb_lignes+1] = '<b>Min.</b>';
        $col[1][$nb_lignes+2] = '<b>Max.</b>';

        $col_csv[1][$nb_lignes] = 'Moyenne';
        $col_csv[1][$nb_lignes+1] = 'Min.';
        $col_csv[1][$nb_lignes+2] = 'Max.';
        if ($multiclasses) {
            $col[2][$nb_lignes] = '&nbsp;';
            $col[2][$nb_lignes+1] = '&nbsp;';
            $col[2][$nb_lignes+2] = '&nbsp;';

            $col_csv[2][$nb_lignes] = '';
            $col_csv[2][$nb_lignes+1] = '';
            $col_csv[2][$nb_lignes+2] = '';
        }
        $nb_lignes = $nb_lignes + 3;
    }

	//==============================================
	// AJOUT: boireaus 20080418
	// Affichage de la colonne RANG sur la moyenne des p�riodes
	if(($stat=="yes")&&($aff_rang=="y")) {

		$nb_col++;
		$ligne1[$nb_col] = "Rang";
		$ligne1_csv[$nb_col] = "Rang";

        $col[$nb_col][$nb_lignes-1] = "<center>-</center>";
        $col[$nb_col][$nb_lignes-2] = "<center>-</center>";
        $col[$nb_col][$nb_lignes-3] = "<center>-</center>";
        $col_csv[$nb_col][$nb_lignes-1] = '-';
        $col_csv[$nb_col][$nb_lignes-2] = '-';
        $col_csv[$nb_col][$nb_lignes-3] = '-';
	}
	//==============================================

    //
    // Affichage du tableau
    //
    if (!isset($larg_tab)) {$larg_tab = 680;}
    if (!isset($bord)) {$bord = 1;}
	//echo "\n<!-- Formulaire pour l'affichage sans ent�te -->\n";
    //echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire1\"  target=\"_blank\">\n";
    //echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire1\"";
    if ($en_tete == "yes") {
		//echo " target=\"_blank\">\n";

		echo "<form enctype=\"multipart/form-data\" name= \"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"get\">\n";
		echo "<p class='bold'><a href=\"index1.php?id_groupe=$id_groupe\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

		if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
			if($_SESSION['statut']=='professeur') {
				$login_prof_groupe_courant=$_SESSION["login"];
			}
			else {
				$tmp_current_group=get_group($id_groupe);

				$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
			}

			$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis mati�re");

			if(!empty($tab_groups)) {

				$chaine_options_classes="";

				$num_groupe=-1;
				$nb_groupes_suivies=count($tab_groups);

				//echo "count(\$tab_groups)=".count($tab_groups)."<br />";

				$id_grp_prec=0;
				$id_grp_suiv=0;
				$temoin_tmp=0;
				//foreach($tab_groups as $tmp_group) {
				for($loop=0;$loop<count($tab_groups);$loop++) {
					if($tab_groups[$loop]['id']==$id_groupe){
						$num_groupe=$loop;

						//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
						$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";

						$temoin_tmp=1;
						if(isset($tab_groups[$loop+1])){
							$id_grp_suiv=$tab_groups[$loop+1]['id'];

							//$chaine_options_classes.="<option value='".$tab_groups[$loop+1]['id']."'>".$tab_groups[$loop+1]['name']." (".$tab_groups[$loop+1]['classlist_string'].")</option>\n";
						}
						else{
							$id_grp_suiv=0;
						}
					}
					else {
						//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
						$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					}

					if($temoin_tmp==0){
						$id_grp_prec=$tab_groups[$loop]['id'];

						//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					}
				}
				// =================================

				/*
				if(isset($id_grp_prec)){
					if($id_grp_prec!=0){
						echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_num=$periode_num";
						echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement pr�c�dent</a>";
					}
				}
				*/

				if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {
					/*
					echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";
					*/

					//echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";
					echo " | <select name='id_groupe' onchange=\"document.forms['form1'].submit();\">\n";
					//echo "P�riode $periode_num: <select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
					echo $chaine_options_classes;
					echo "</select> | \n";
				}

				/*
				if(isset($id_grp_suiv)){
					if($id_grp_suiv!=0){
						echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num";
						echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement suivant</a>";
						}
				}
				*/
			}
			// =================================
		}

		// Ins�rer les m�mes choix:

		$k=1;
        while ($k < $nb_periode) {
			$temp = "visu_note_".$k;
			if (isset($_POST[$temp]) or isset($_GET[$temp])) {
				echo "<input type='hidden' name='visu_note_$k' value='yes' />\n";
			}

			$temp = "visu_app_".$k;
			if (isset($_POST[$temp]) or isset($_GET[$temp])) {
				echo "<input type='hidden' name='visu_app_$k' value='yes' />\n";
			}

			$k++;
		}

		if (isset($_POST['afficher_rang']) or isset($_GET['afficher_rang'])) {
			echo "<input type='hidden' name='afficher_rang' value='yes' />\n";
		}

		if (isset($_POST['stat']) or isset($_GET['stat'])) {
			echo "<input type='hidden' name='stat' value='yes' />\n";
		}

		if (isset($_POST['choix_visu']) or isset($_GET['choix_visu'])) {
			echo "<input type='hidden' name='choix_visu' value='yes' />\n";
		}

		echo "</form>\n";

		echo "\n<!-- Formulaire pour l'affichage sans ent�te -->\n";
		echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire1\"  target=\"_blank\">\n";
		echo "<p><input type=\"submit\" value=\"Visualiser sans l'en-t�te\" /></p>\n";
	}
	else {
		// On ne place ici cette annonce de formulaire que pour avoir du code HTML valide:
		echo "\n<!-- Formulaire pour l'affichage sans ent�te -->\n";
		echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire1\"  target=\"_blank\">\n";
	}
	/*
	else {
		echo ">\n";
	}
	*/
	//=========================
	// MODIF: boireaus 20080421
	// Pour permettre de trier autrement...
	// Ligne de test:
	// echo "<input type=\"button\" value=\"Visualiser sans l'en-t�te et tri col 2\" onclick=\"document.getElementById('col_tri').value='2';document.forms['formulaire1'].submit();\" /><br />\n";
    //echo "<input type='hidden' name='col_tri' id='col_tri' value='' />\n";
	//=========================

    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    if ($stat == "yes") echo "<input type='hidden' name='stat' value='yes' />\n";
    $i="1";
    while ($i < $nb_periode) {
        $name1 = "visu_note_".$i;
        //if (isset($_POST[$name1])) {
        if ((isset($_POST[$name1]))||(isset($_GET[$name1]))) {
            //$temp1 = $_POST[$name1];
            $temp1 = isset($_POST[$name1]) ? $_POST[$name1] : $_GET[$name1];
            echo "<input type='hidden' name='$name1' value='$temp1' />\n";
        }
        $name2 = "visu_app_".$i;
        //if (isset($_POST[$name2])) {
        if ((isset($_POST[$name2]))||(isset($_GET[$name2]))) {
            //$temp2 = $_POST[$name2];
            $temp2 = isset($_POST[$name2]) ? $_POST[$name2] : $_GET[$name2];
            echo "<input type='hidden' name='$name2' value='$temp2' />\n";
        }

        $i++;
    }
	//=========================
	// MODIF: boireaus 20080421
	// Pour avoir le rang sur le tableau sans ent�te
	//if((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes")) {
	if(((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes"))||((isset($_GET['afficher_rang']))&&($_GET['afficher_rang']=="yes"))) {
		echo "<input type='hidden' name='afficher_rang' value='yes' />\n";
	}
	//=========================
    echo "<input type='hidden' name='en_tete' value='no' />\n";
    echo "<input type='hidden' name='larg_tab' value='$larg_tab' />\n";
    echo "<input type='hidden' name='bord' value='$bord' />\n";

	if(isset($order_by)){
		echo "<p><input type='hidden' name='order_by' value='$order_by' />\n";
	}

	if(isset($sens_tri)){
		echo "<p><input type='hidden' name='sens_tri' value='$sens_tri' />\n";
	}
	if(isset($col_tri)){
		echo "<p><input type='hidden' name='col_tri' value='$col_tri' />\n";
	}

    echo "</form>\n";


	//=======================================================
	// MODIF: boireaus 20080421
	// Pour permettre de trier autrement...
	echo "\n<!-- Formulaire pour l'affichage avec tri sur la colonne cliqu�e -->\n";
    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire_tri\">\n";

    echo "<input type='hidden' name='col_tri' id='col_tri' value='' />\n";
    echo "<input type='hidden' name='sens_tri' id='sens_tri' value='' />\n";

    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    if ($stat == "yes") echo "<input type='hidden' name='stat' value='yes' />\n";
    $i="1";
    while ($i < $nb_periode) {
        $name1 = "visu_note_".$i;
        if (isset($_POST[$name1])) {
            $temp1 = $_POST[$name1];
            echo "<input type='hidden' name='$name1' value='$temp1' />\n";
        }
        $name2 = "visu_app_".$i;
        if (isset($_POST[$name2])) {
            $temp2 = $_POST[$name2];
            echo "<input type='hidden' name='$name2' value='$temp2' />\n";
        }

        $i++;
    }
	if((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes")) {
		echo "<input type='hidden' name='afficher_rang' value='yes' />\n";
	}
    if ($en_tete == "yes") {
		echo "<input type='hidden' name='en_tete' value='yes' />\n";
	}
	else {
		echo "<input type='hidden' name='en_tete' value='no' />\n";
	}
    echo "<input type='hidden' name='larg_tab' value='$larg_tab' />\n";
    echo "<input type='hidden' name='bord' value='$bord' />\n";

	if(isset($order_by)){
		echo "<p><input type='hidden' name='order_by' id='order_by' value='$order_by' />\n";
	}

    echo "</form>\n";
	//=======================================================



	//=======================================================
	echo "\n<!-- Formulaire pour l'export CSV -->\n";
    echo "<div style='width:10em;float:right;'>\n";
    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"form_csv\" target='_blank'>\n";

	for($i=1;$i<=count($ligne1_csv);$i++) {
		echo "<input type='hidden' name='ligne1_csv[$i]' value=\"$ligne1_csv[$i]\" />\n";
	}
	echo "<br />\n";

	$lignes_csv=array();
	for($i=1;$i<=count($col_csv);$i++) {
		for($j=0;$j<count($col_csv[$i]);$j++) {
			if(!isset($lignes_csv[$j])) {
				$lignes_csv[$j]=$col_csv[$i][$j];
			}
			else {
				$lignes_csv[$j].=";".my_ereg_replace('"',"'",my_ereg_replace(";",",",my_ereg_replace("&#039;","'",html_entity_decode($col_csv[$i][$j]))));
			}

			//echo "<input type='hidden' name='col_csv_".$i."[$j]' value='".$col_csv[$i][$j]."' />\n";
		}
		//echo "<br />\n";
	}

	for($j=0;$j<count($col_csv[1]);$j++) {
		echo "<input type='hidden' name='lignes_csv[$j]' value=\"".$lignes_csv[$j]."\" />\n";
		//echo "<br />\n";
	}

	echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
	echo "<input type='hidden' name='mode' value='csv' />\n";
	// On ne met le bouton que pour l'affichage avec ent�te
	if ($en_tete == "yes") {echo "<input type='submit' value='G�n�rer un CSV' />\n";}
    echo "</form>\n";
    echo "</div>\n";
	//=======================================================


	echo "\n<!-- Formulaire pour ... -->\n";
    echo "<form enctype=\"multipart/form-data\" action=\"index1.php\" method=\"post\" name=\"formulaire2\">\n";
    if ($en_tete == "yes")
        parametres_tableau($larg_tab, $bord);
//    else echo "<p class=small><a href=\"index1.php?id_classe=$id_classe&choix_matiere=$choix_matiere\">Retour</a>";
    echo "<p class='bold'>" . $_SESSION['nom'] . " " . $_SESSION['prenom'] . " | Ann�e : ".getSettingValue("gepiYear")." | Groupe : " . htmlentities($current_group["description"]) . " (" . $current_group["classlist_string"] . ") | Mati�re : " . htmlentities($current_group["matiere"]["nom_complet"]);
    echo "</p>\n";
    echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
    echo "<input type='hidden' name='choix_visu' value='yes' />\n";
    if ($stat == "yes") echo "<input type='hidden' name='stat' value='yes' />\n";
    $i="1";
    while ($i < $nb_periode) {
        $name1 = "visu_note_".$i;
        if (isset($_POST[$name1])) {
            $temp1 = $_POST[$name1];
            echo "<input type='hidden' name='$name1' value='$temp1' />\n";
        }
        $name2 = "visu_app_".$i;
        if (isset($_POST[$name2])) {
            $temp2 = $_POST[$name2];
            echo "<input type='hidden' name='$name2' value='$temp2' />\n";
        }
        $i++;
    }
	if(isset($order_by)) {echo "<input type='hidden' name='order_by' value='$order_by' />\n";}

	if((isset($_POST['afficher_rang']))&&($_POST['afficher_rang']=="yes")) {
		echo "<input type='hidden' name='afficher_rang' value='yes' />\n";
	}

    echo "</form>\n";
//    $appel_donnees_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND c.login = e.login) ORDER BY e.nom, e.prenom");
//    $nombre_eleves = mysql_num_rows($appel_donnees_eleves);



    //if (isset($col)) affiche_tableau($nb_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord,0,0,"");

	//function affiche_tableau_index1($nombre_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord, $col1_centre, $col_centre, $couleur_alterne) {

    if (isset($col)) {
		$couleur_alterne="";
		$col1_centre=0;
		$col_centre=0;
		echo "<table border=\"$bord\" cellspacing=\"0\" width=\"$larg_tab\" cellpadding=\"1\" summary=''>\n";
		echo "<tr>\n";
		$j = 1;
		while($j < $nb_col+1) {
			echo "<th class='small'>";
			if(!eregi("Appr�ciation",$ligne1[$j])) {
				echo "<a href='#' onclick=\"document.getElementById('col_tri').value='$j';";
				if(my_eregi("Rang",$ligne1[$j])) {echo "document.getElementById('sens_tri').value='inverse';";}
				if($ligne1[$j]=="Classe") {echo "if(document.getElementById('order_by')) {document.getElementById('order_by').value='classe';}";}
				if($ligne1[$j]=="Nom Pr�nom") {echo "if(document.getElementById('order_by')) {document.getElementById('order_by').value='nom';}";}
				echo "document.forms['formulaire_tri'].submit();\">";
				echo $ligne1[$j];
				echo "</a>";
			}
			else {
				echo $ligne1[$j];
			}
			echo "</th>\n";
			$j++;
		}
		echo "</tr>\n";
		$i = "0";
		$bg_color = "";
		$flag = "1";
		while($i < $nb_lignes) {
			if ($couleur_alterne) {
				if ($flag==1) $bg_color = "bgcolor=\"#C0C0C0\""; else $bg_color = "     " ;
			}

			echo "<tr>\n";
			$j = 1;
			while($j < $nb_col+1) {
				if ((($j == 1) and ($col1_centre == 0)) or (($j != 1) and ($col_centre == 0))){
					echo "<td class='small' ".$bg_color.">{$col[$j][$i]}</td>\n";
				} else {
					echo "<td align=\"center\" class='small' ".$bg_color.">{$col[$j][$i]}</td>\n";
				}
				$j++;
			}
			echo "</tr>\n";
			if ($flag == "1") $flag = "0"; else $flag = "1";
			$i++;
		}
		echo "</table>\n";
	}

    if ($test == 1 and  $stat == "yes") {
        echo "<br /><table border=\"$bord\" cellpadding=\"5\" cellspacing=\"1\" width=\"$larg_tab\" summary=''><tr><td>
        <b>Moyenne g�n�rale de la classe : ".$moy_gen."</b>
        <br /><br /><b>Pourcentage des �l�ves ayant une moyenne g�n�rale : </b><ul>\n";
        echo "<li>inf�rieure strictement � 8 : <b>".$pourcent_i8."</b></li>\n";
        echo "<li>entre 8 et 12 : <b>".$pourcent_se8_ie12."</b></li>\n";
        echo "<li>sup�rieure ou �gale � 12 : <b>".$pourcent_se12."</b></li></ul></td></tr></table>\n";
    }

}
require("../lib/footer.inc.php");
?>
