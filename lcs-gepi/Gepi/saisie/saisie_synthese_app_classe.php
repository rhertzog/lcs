<?php
/*
* $Id: saisie_synthese_app_classe.php 6727 2011-03-29 15:14:30Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
@set_time_limit(0);

// On indique qu'il faut creer des variables non prot�g�es (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Classe choisie:
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
// P�riodes de visualisation:
$periode1=isset($_POST['periode1']) ? $_POST['periode1'] : NULL;
$periode2=isset($_POST['periode2']) ? $_POST['periode2'] : NULL;
// P�riode de saisie:
$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);

$url_retour=isset($_POST['url_retour']) ? $_POST['url_retour'] : NULL;
if(preg_match("#/saisie/saisie_avis1.php#",$_SERVER['HTTP_REFERER'])) {$url_retour="../saisie/saisie_avis1.php?id_classe=$id_classe&amp;periode_num=$num_periode";}
if(preg_match("#/saisie/saisie_avis2.php#",$_SERVER['HTTP_REFERER'])) {$url_retour="../saisie/saisie_avis2.php?id_classe=$id_classe&amp;periode_num=$num_periode";}

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

//debug_var();

//if((isset($periode1))&&(isset($periode2))&&(isset($id_classe))&&(isset($num_periode))) {
if((isset($id_classe))&&(isset($num_periode))) {

	if(!isset($periode1)) {$periode1=$num_periode;}
	if(!isset($periode2)) {$periode2=$num_periode;}

	if($_SESSION['statut']=='professeur') {
		// Le prof est-il PP
		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jep.professeur='".$_SESSION['login']."' AND jec.login=jep.login AND jec.periode='$num_periode';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			//tentative_intrusion("2", "Tentative d'acc�s par un prof � un bulletin simplifi� d'un �l�ve qu'il n'a pas en cours, sans en avoir l'autorisation.");

			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'�tes pas autoris� � saisir la synth�se pour cette classe."));

			die();
		}
	}
	else {
		// Sinon, c'est un compte scolarit�.
		$sql="SELECT 1=1 FROM j_scol_classes WHERE id_classe='$id_classe';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'�tes pas autoris� � saisir la synth�se pour cette classe."));
			die();
		}
	}

	// Tout est choisi, on va passer � l'affichage

	//$synthese="";

	//if(isset($_POST['no_anti_inject_synthese'])) {
	if (isset($NON_PROTECT["synthese"])) {
		check_token();

		// On enregistre la synthese
		$synthese=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["synthese"]));

		//$synthese=my_ereg_replace('(\\\r\\\n)+',"\r\n",$synthese);
		$synthese=preg_replace('/(\\\r\\\n)+/',"\r\n",$synthese);
		$synthese=preg_replace('/(\\\r)+/',"\r",$synthese);
		$synthese=preg_replace('/(\\\n)+/',"\n",$synthese);

		$sql="SELECT 1=1 FROM synthese_app_classe WHERE id_classe='$id_classe' AND periode='$num_periode';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$sql="INSERT INTO synthese_app_classe SET id_classe='$id_classe', periode='$num_periode', synthese='$synthese';";
			$insert=mysql_query($sql);
			if(!$insert) {$msg="Erreur lors de l'enregistrement de la synth�se.";}
			else {$msg="La synth�se a �t� enregistr�e.";}
		}
		else {
			$sql="UPDATE synthese_app_classe SET synthese='$synthese' WHERE id_classe='$id_classe' AND periode='$num_periode';";
			$update=mysql_query($sql);
			if(!$update) {$msg="Erreur lors de la mise � jour de la synth�se.";}
			else {$msg="La synth�se a �t� mise � jour.";}
		}
	}

	$sql="SELECT * FROM synthese_app_classe WHERE (id_classe='$id_classe' AND periode='$num_periode');";
	//echo "$sql<br />";
	$res_current_synthese=mysql_query($sql);
	$synthese=@mysql_result($res_current_synthese, 0, "synthese");

	$titre_page="Synth�se classe";
	require_once("../lib/header.inc");
	include "../lib/periodes.inc.php";
	include "../lib/bulletin_simple.inc.php";
	include "../lib/bulletin_simple_classe.inc.php";

	echo "<p class=\"bold\">";
	if(isset($url_retour)) {
		echo "<a href=\"$url_retour\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour � la saisie des avis</a>";
	}
	else {
		echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	}
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre classe</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Autre p�riode</a>\n";
	echo "</p>\n";

	$gepiYear = getSettingValue("gepiYear");
	
	if ($periode1 > $periode2) {
		$temp = $periode2;
		$periode2 = $periode1;
		$periode1 = $temp;
	}
	// On teste la pr�sence d'au moins un coeff pour afficher la colonne des coef
	$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
	//echo "\$test_coef=$test_coef<br />";
	// Apparemment, $test_coef est r�affect� plus loin dans un des include()
	$nb_coef_superieurs_a_zero=$test_coef;
	
	// On regarde si on affiche les cat�gories de mati�res
	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}
	
	// Si le rang des �l�ves est demand�, on met � jour le champ rang de la table matieres_notes
	$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
	if ($affiche_rang == 'y') {
		$periode_num=$periode1;
		while ($periode_num < $periode2+1) {
			include "../lib/calcul_rang.inc.php";
			$periode_num++;
		}
	}
	
	/*
	// On regarde si on affiche les cat�gories de mati�res
	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}
	*/
	//echo "\$choix_edit=$choix_edit<br />";
	
	//=========================
	// AJOUT: boireaus 20080316
	$coefficients_a_1="non";
	$affiche_graph = 'n';
	/*
	$get_cat = mysql_query("SELECT id FROM matieres_categories");
	$categories = array();
	while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
		$categories[] = $row["id"];
	}
	*/
	unset($tab_moy_gen);
	unset($tab_moy);
	//unset($tab_moy_cat_classe);
	for($loop=$periode1;$loop<=$periode2;$loop++) {
		$periode_num=$loop;
		include "../lib/calcul_moy_gen.inc.php";
		$tab_moy_gen[$loop]=$moy_generale_classe;
		//$tab_moy_cat_classe

		$tab_moy['periodes'][$periode_num]=array();
		$tab_moy['periodes'][$periode_num]['tab_login_indice']=$tab_login_indice;         // [$login_eleve]
		$tab_moy['periodes'][$periode_num]['moy_gen_eleve']=$moy_gen_eleve;               // [$i]
		$tab_moy['periodes'][$periode_num]['moy_gen_eleve1']=$moy_gen_eleve1;             // [$i]
		//$tab_moy['periodes'][$periode_num]['moy_gen_classe1']=$moy_gen_classe1;           // [$i]
		$tab_moy['periodes'][$periode_num]['moy_generale_classe']=$moy_generale_classe;
		$tab_moy['periodes'][$periode_num]['moy_generale_classe1']=$moy_generale_classe1;
		$tab_moy['periodes'][$periode_num]['moy_max_classe']=$moy_max_classe;
		$tab_moy['periodes'][$periode_num]['moy_min_classe']=$moy_min_classe;
	
		// Il faudrait r�cup�rer/stocker les cat�gories?
		$tab_moy['periodes'][$periode_num]['moy_cat_eleve']=$moy_cat_eleve;               // [$i][$cat]
		$tab_moy['periodes'][$periode_num]['moy_cat_classe']=$moy_cat_classe;             // [$i][$cat]
		$tab_moy['periodes'][$periode_num]['moy_cat_min']=$moy_cat_min;                   // [$i][$cat]
		$tab_moy['periodes'][$periode_num]['moy_cat_max']=$moy_cat_max;                   // [$i][$cat]
	
		$tab_moy['periodes'][$periode_num]['quartile1_classe_gen']=$quartile1_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile2_classe_gen']=$quartile2_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile3_classe_gen']=$quartile3_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile4_classe_gen']=$quartile4_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile5_classe_gen']=$quartile5_classe_gen;
		$tab_moy['periodes'][$periode_num]['quartile6_classe_gen']=$quartile6_classe_gen;
		$tab_moy['periodes'][$periode_num]['place_eleve_classe']=$place_eleve_classe;
	
		$tab_moy['periodes'][$periode_num]['current_eleve_login']=$current_eleve_login;   // [$i]
		//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
		if($loop==$periode1) {
			$tab_moy['current_group']=$current_group;                                     // [$j]
		}
		$tab_moy['periodes'][$periode_num]['current_eleve_note']=$current_eleve_note;     // [$j][$i]
		$tab_moy['periodes'][$periode_num]['current_eleve_statut']=$current_eleve_statut; // [$j][$i]
		//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
		$tab_moy['periodes'][$periode_num]['current_coef']=$current_coef;                 // [$j]
		$tab_moy['periodes'][$periode_num]['current_classe_matiere_moyenne']=$current_classe_matiere_moyenne; // [$j]
	
		$tab_moy['periodes'][$periode_num]['current_coef_eleve']=$current_coef_eleve;     // [$i][$j] ATTENTION
		$tab_moy['periodes'][$periode_num]['moy_min_classe_grp']=$moy_min_classe_grp;     // [$j]
		$tab_moy['periodes'][$periode_num]['moy_max_classe_grp']=$moy_max_classe_grp;     // [$j]
		if(isset($current_eleve_rang)) {
			// $current_eleve_rang n'est pas renseign� si $affiche_rang='n'
			$tab_moy['periodes'][$periode_num]['current_eleve_rang']=$current_eleve_rang; // [$j][$i]
		}
		$tab_moy['periodes'][$periode_num]['quartile1_grp']=$quartile1_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile2_grp']=$quartile2_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile3_grp']=$quartile3_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile4_grp']=$quartile4_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile5_grp']=$quartile5_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['quartile6_grp']=$quartile6_grp;               // [$j]
		$tab_moy['periodes'][$periode_num]['place_eleve_grp']=$place_eleve_grp;           // [$j][$i]
	
		$tab_moy['periodes'][$periode_num]['current_group_effectif_avec_note']=$current_group_effectif_avec_note; // [$j]
	
		/*
		// De calcul_moy_gen.inc.php, on r�cup�re en sortie:
		//     - $moy_gen_eleve[$i]
		//     - $moy_gen_eleve1[$i] idem avec les coef forc�s � 1
		//     - $moy_gen_classe[$i]
		//     - $moy_gen_classe1[$i] idem avec les coef forc�s � 1
		//     - $moy_generale_classe
		//     - $moy_max_classe
		//     - $moy_min_classe
		
		// A VERIFIER, mais s'il n'y a pas de coef sp�cifique pour un �l�ve, on devrait avoir
		//             $moy_gen_classe[$i] == $moy_generale_classe
		// NON: Cela correspond � un mode de calcul qui ne retient que les mati�res suivies par l'�l�ve pour calculer la moyenne g�n�rale
		//      Le LATIN n'est pas compt� dans cette moyenne g�n�rale si l'�l�ve ne fait pas latin.
		//      L'Allemand n'est pas comptabilis� si l'�l�ve ne fait pas allemand
		// FAIRE LE TOUR DES PAGES POUR VIRER TOUS CES $moy_gen_classe s'il en reste?
		
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
		//     - $tab_login_indice[LOGIN_ELEVE]=$i
		
		//     $categories[] = $row["id"];
		//     $tab_noms_categories[$row["id"]]=$row["nom_complet"];
		//     $tab_id_categories[$row["nom_complet"]]=$row["id"];
		
		*/

	}

	$tab_moy['categories']['id']=$categories;
	$tab_moy['categories']['nom_from_id']=$tab_noms_categories;
	$tab_moy['categories']['id_from_nom']=$tab_id_categories;
	
	
	$sql="SELECT DISTINCT e.*
	FROM eleves e, j_eleves_classes c 
	WHERE (
	c.id_classe='$id_classe' AND 
	e.login = c.login
	) ORDER BY e.nom,e.prenom;";
	$res_ele= mysql_query($sql);
	if(mysql_num_rows($res_ele)>0) {
		while($lig_ele=mysql_fetch_object($res_ele)) {
			$tab_moy['eleves'][]=$lig_ele->login;
			/*
			$tab_moy['ele'][$lig_ele->login]=array();
			$tab_moy['ele'][$lig_ele->login]['nom']=$lig_ele->nom;
			$tab_moy['ele'][$lig_ele->login]['prenom']=$lig_ele->prenom;
			$tab_moy['ele'][$lig_ele->login]['sexe']=$lig_ele->sexe;
			$tab_moy['ele'][$lig_ele->login]['naissance']=$lig_ele->naissance;
			$tab_moy['ele'][$lig_ele->login]['elenoet']=$lig_ele->elenoet;
			*/
		}
	}

	$display_moy_gen=sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."'");


	bulletin_classe($tab_moy, $nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$nb_coef_superieurs_a_zero,$affiche_categories);

	// Formulaire de saisie
	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
	echo add_token_field();
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
	echo "<input type='hidden' name='periode1' value='$periode1' />\n";
	echo "<input type='hidden' name='periode2' value='$periode2' />\n";

	if(isset($url_retour)) {echo "<input type='hidden' name='url_retour' value='$url_retour' />\n";}

	echo "<a name='synthese'></a>\n";
	echo "<p><b>Saisie de la synth�se pour le groupe classe&nbsp;:</b><br />\n";
	echo "<textarea class='wrap' name=\"no_anti_inject_synthese\" rows='5' cols='60' onchange=\"changement()\"";
	echo ">".stripslashes($synthese)."</textarea>\n";

	echo "<br /><center><input type='submit' value=Valider /></center>\n";

	require("../lib/footer.inc.php");
	die();

}

//********************************
$titre_page="Synth�se classe";
require_once("../lib/header.inc");
//********************************

if(!isset($id_classe)) {
	// Choix de la classe:
	if($_SESSION['statut']=='professeur') {
		// Le prof est-il PP
		$sql="SELECT DISTINCT id, classe FROM j_eleves_professeurs jep, j_eleves_classes jec WHERE jep.professeur='".$_SESSION['login']."' AND jec.login=jep.login;";
		$res_classe=mysql_query($sql);
		if(mysql_num_rows($res_classe)==0) {
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'�tes pas autoris� � saisir la synth�se d'une classe."));
			die();
		}
	}
	else {
		$sql="SELECT DISTINCT id, classe FROM j_scol_classes jsc, classes c WHERE jsc.id_classe=c.id ORDER BY c.classe;";
		$res_classe=mysql_query($sql);
		if(mysql_num_rows($res_classe)==0) {
			header('Location: ../accueil.php&msg='.rawurlencode("Vous n'�tes pas autoris� � saisir la synth�se pour une classe."));
			die();
		}
	}

	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre classe</a>\n";
	echo "</p>\n";

	$nombreligne = mysql_num_rows($res_classe);
	echo "<p>Cliquez sur la classe pour laquelle vous souhaitez saisir la synth�se des appr�ciations groupe classe.</p>\n";
	//echo "<table border=0>\n";
	$nb_class_par_colonne=round($nombreligne/3);
		//echo "<table width='100%' border='1'>\n";
		echo "<table width='100%' summary='Choix de la classe'>\n";
		echo "<tr valign='top' align='center'>\n";
		echo "<td align='left'>\n";
	$i = 0;
	while ($i < $nombreligne){
		$id_classe = mysql_result($res_classe, $i, "id");
		$classe_liste = mysql_result($res_classe, $i, "classe");
		//echo "<tr><td><a href='index3.php?id_classe=$id_classe'>$classe_liste</a></td></tr>\n";
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>$classe_liste</a><br />\n";
		$i++;
	}
	echo "</table>\n";

}
else {
	// Choix de la p�riode de saisie et des p�riodes d'affichage
	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	echo "</p>\n";

	$sql="SELECT * FROM classes WHERE id='$id_classe'";
	$res_classe=mysql_query($sql);
	if(mysql_num_rows($res_classe)==0) {
		echo "<p style='color:red'>La classe choisie n'existe pas.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$nom_classe = mysql_result($res_classe, 0, "classe");

	echo "<p class='grand'>Classe de $nom_classe</p>\n";

	include "../lib/periodes.inc.php";

	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' AND verouiller!='O';";
	$res_per=mysql_query($sql);
	if(mysql_num_rows($res_classe)==0) {
		echo "<p>Toutes les p�riodes sont closes pour cette classe.<br />Plus aucune modification n'est possible.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

	echo "<p>Choisissez la p�riode pour laquelle vous souhaitez saisir la synth�se&nbsp;: <br />\n";
	$cpt=0;
	while($lig=mysql_fetch_object($res_per)) {
		echo "<input type='radio' name='num_periode' id='num_periode_$lig->num_periode' value='$lig->num_periode' ";
		if($cpt==0) {echo "checked ";}
		echo "/><label for='num_periode_$lig->num_periode'>".$nom_periode[$lig->num_periode]."</label><br />\n";
		$cpt++;
	}

	echo "<p>Choisissez la(les) p�riode(s) � afficher dans le bulletin de classe&nbsp;: <br />\n";
	echo "De la p�riode : <select onchange=\"change_periode()\" size=1 name=\"periode1\">\n";
	$i = "1" ;
	while ($i < $nb_periode) {
		echo "<option value=$i>$nom_periode[$i] </option>\n";
		$i++;
	}
	echo "</select>\n";
	echo "&nbsp;� la p�riode : <select size=1 name=\"periode2\">\n";
	$i = "1" ;
	while ($i < $nb_periode) {
		echo "<option value=$i>$nom_periode[$i] </option>\n";
		$i++;
	}
	echo "</select>\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	echo "<br /><br /><center><input type='submit' value='Valider' /></center>\n";
	echo "</form>\n";

}

require("../lib/footer.inc.php");
?>