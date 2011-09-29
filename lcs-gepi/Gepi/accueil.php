<?php
/**
 * $Id: accueil.php 7961 2011-08-25 09:49:50Z regis $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * Les entr�es du menu d'accueil sont dans la page class_php/class_page_accueil.php
 * 
 * @license GNU/GPL v2
 * @package General
 * @subpackage Accueil
 * @see class_page_accueil
 * @see test_maj()
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
 */

//=================================================================================
// Les entr�es du menu d'accueil sont dans la page class_php/class_page_accueil.php
//=================================================================================

/* ---------Variables envoy�es au gabarit
*	$tbs_gere_connect							affichage ou non du nombre de connect�s
*	$tbs_alert_sums								nombre d'alertes de s�curit�s
*	$tbs_statut_utilisateur				statut de l'utilisateur
*
*
*	----- tableaux -----
*	$tbs_message_admin						messages de s�curit� pour l'administrateur
*	$tbs_nom_connecte							nom des personnes connect�es
*				-> texte								NOM Pr�nom
*				-> style								Classe � appliquer
*				-> courriel							Adresse courriel
*				-> statut								Statut
*	$tbs_referencement								Demande de r�f�rencement
*				-> texte								Message
*				-> lien									lien vers la page
*				-> titre								titre du lien
*	$tbs_probleme_dir									messages : probl�mes de r�glage
*	$tbs_interface_graphique					lien vers l'interface graphique
*				-> lien
*				-> classe
*				-> titre
*	$tbs_message									Messagerie de GEPI
*				-> message							texte du message (avec les balises html de mise en page)
*				-> suite								hr si ce n'est pas le premier message
*	$tbs_canal_rss
*				-> lien									lien du flux
*				-> texte								texte du lien
*				-> mode									1 si r�cup�ration directe, 2 si fichier CSV
*				-> expli								explications
*	$tbs_menu
*				-> classe								classe CSS
*				-> image								icone du lien
*				-> texte								texte du titre du menu
*				-> entree								entr�es du menu
*							-> lien						lien vers la page
*							-> titre   				texte du lien
*							-> expli					explications
*
*	Variables h�rit�es de :
*
*	header_template.inc
*	header_barre_prof_template.inc
*	footer_template.inc.php
*
 */



// Initialisation des feuilles de style apr�s modification pour am�liorer l'accessibilit�
$accessibilite="y";

// Begin standart header
$titre_page = "Accueil GEPI";
$affiche_connexion = 'yes';
$niveau_arbo = 0;
$gepiPathJava=".";

/**
 * Fichiers d'initialisation
 */
require_once("./lib/initialisations.inc.php");

// On teste s'il y a une mise � jour de la base de donn�es � effectuer
if (test_maj()) {
    header("Location: ./utilitaires/maj.php");
}

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ./logout.php?auto=1");
    die();
}

// On v�rifie si l'utilisation du cdt n'est pas unique
if (getSettingValue("use_only_cdt") == 'y' AND $_SESSION["statut"] == 'professeur'){
  $cdt = (getSettingValue("GepiCahierTexteVersion") == '2') ? '_2' : '';
  header("Location:cahier_texte".$cdt."/index.php");
}

// S�curit�
if (!checkAccess()) {
    header("Location: ./logout.php?auto=2");
    die();
}


unset ($_SESSION['order_by']);
$test_https = 'y'; // pour ne pas avoir � refaire le test si on a besoin de l'URL compl�te (rss)
if (!isset($_SERVER['HTTPS'])
    OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
    OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https"))
{
	$test_https = 'n';
}

/**
 * Fonctions utiles uniquement pour l'administrateur
 */
if($_SESSION['statut']=='administrateur') {
  include_once("lib/share-admin.inc.php");
}

if($_SESSION['statut']=='professeur'){
	$accueil_simpl=isset($_GET['accueil_simpl']) ? $_GET['accueil_simpl'] : NULL;
	if(!isset($accueil_simpl)){
		$pref_accueil_simpl=getPref($_SESSION['login'],'accueil_simpl',"n");
		$accueil_simpl=$pref_accueil_simpl;
	}

	//$_SERVER['HTTP_REFERER']=	https://127.0.0.1/steph/gepi-trunk/accueil_simpl_prof.php
	//$_SERVER['REQUEST_URI']=	/steph/gepi-trunk/accueil.php
	if((isset($_SERVER['HTTP_REFERER']))&&(preg_match("#$gepiPath/accueil_simpl_prof.php$#",$_SERVER['HTTP_REFERER']))&&(isset($_SERVER['REQUEST_URI']))&&($_SERVER['REQUEST_URI']=="$gepiPath/accueil.php")) {
		// On ne va acc�der � l'accueil.php classique
	}
	else {
		if($accueil_simpl=="y"){
			header("Location: ./accueil_simpl_prof.php");
		}
	}
}
else{
	$accueil_simpl=NULL;
}

//debug_var();

// ====== Inclusion des fichiers de classes =====
$_SESSION['gepiPath']=$gepiPath;

include "class_php/class_menu_general.php";
include "class_php/class_page_accueil.php";
include "class_php/class_page_accueil_autre.php";

if ($_SESSION['statut']=="autre") {
  $afficheAccueil=new class_page_accueil_autre($gepiSettings, $niveau_arbo,$ordre_menus);
} else {
  $afficheAccueil=new class_page_accueil($_SESSION['statut'], $gepiSettings, $niveau_arbo,$ordre_menus);
}

if(isset($_GET['del_id_info'])) {
	check_token();

	if(!isset($msg)) {$msg="";}

	if(del_info_action($_GET['del_id_info'])) {
		$msg.="Action n� ".$_GET['del_id_info']." supprim�e.<br />";
	}
	else {
		$msg.="Erreur lors de la suppression de l'action n� ".$_GET['del_id_info'].".<br />";
	}
}

if((($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite'))&&(isset($_GET['del_id_acces_cdt']))) {
	check_token();

	if(!isset($msg)) {$msg="";}

	if(del_acces_cdt($_GET['del_id_acces_cdt'])) {
		$msg.="Acc�s CDT n� ".$_GET['del_id_acces_cdt']." supprim�.<br />";
	}
	else {
		$msg.="Erreur lors de la suppression de l'acc�s CDT n� ".$_GET['del_id_acces_cdt'].".<br />";
	}
}

$post_reussi=FALSE;
// ====== Inclusion des balises head et du bandeau =====
include_once("./lib/header_template.inc");
$tbs_statut_utilisateur = $_SESSION['statut'];

if (!suivi_ariane($_SERVER['PHP_SELF'],"Accueil"))
		echo "erreur lors de la cr�ation du fil d'ariane";
/*
// voir le fil d'Ariane pour debug
	foreach ($_SESSION['ariane']['lien'] as $index=>$lienActuel){
	  echo $lienActuel;
	  echo " => ";
	  echo $_SESSION['ariane']['texte'][$index];
	  echo "<br />";
	}
 *
 */

	// Initialisation de $_SESSION["retour"]
$_SESSION["retour"] = "";

//*************************************************************
// fin initialisation des tableaux d'affichage et des variables
//*************************************************************

if ($_SESSION['statut'] == "administrateur") {

    // V�rification et/ou changement du r�pertoire de backup
	if (!check_backup_directory()) {
		$afficheAccueil->message_admin[] = "Il y a eu un probl�me avec la mise � jour du r�pertoire de sauvegarde.
Veuillez v�rifier que le r�pertoire /backup de Gepi est accessible en �criture par le serveur (le serveur *uniquement* !)";
    }


	if (!check_user_temp_directory()) {		
		$afficheAccueil->message_admin[] = "Il y a eu un probl�me avec la mise � jour du r�pertoire temp.
Veuillez v�rifier que le r�pertoire /temp de Gepi est accessible en �criture par le serveur (le serveur *uniquement* !)";
	}else{
		$_SESSION['user_temp_directory']='y';
	}

	if ((getSettingValue("disable_login"))!='no'){
	  $afficheAccueil->message_admin[] = "Attention : le site est en cours de maintenance et temporairement inaccessible.";
	}

    // * affichage du nombre de connect� *
    // compte le nombre d'enregistrement dans la table
	$sql = "SELECT login FROM log WHERE END > now()";
	$res = sql_query($sql);
	$afficheAccueil->nb_connect = sql_count($res);
	if(mysql_num_rows($res)>1) {
		$titre="Personnes connect�es";
		$alt=1;
		while($lig_log=mysql_fetch_object($res)) {
			$sql="SELECT nom,prenom,statut,email FROM utilisateurs WHERE login='$lig_log->login';";
			$res_pers=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
			  $afficheAccueil->nom_connecte[]=array("style"=>'rouge',"courriel"=>"","texte"=>$lig_log->LOGIN,"statut"=>"???");
			}else{
				$lig_pers=mysql_fetch_object($res_pers);
				$alt=$alt*(-1);
                $afficheAccueil->nom_connecte[]=array("style"=>'lig'.$alt,"courriel"=>$lig_pers->email,"texte"=>strtoupper($lig_pers->nom)." ".ucfirst(strtolower($lig_pers->prenom)),"statut"=>$lig_pers->statut);
			}
		}
		$afficheAccueil->nb_connect_lien = "#";
	}else {
		$afficheAccueil->nb_connect_lien ="#";

	}
    $afficheAccueil->gere_connect= "1";

	// Lien vers le panneau de contr�le de s�curit�
	$alert_sums = mysql_result(mysql_query("SELECT SUM(niveau) FROM tentatives_intrusion WHERE (statut = 'new')"), 0);
	if (empty($alert_sums)) $alert_sums = "0";
	$afficheAccueil->alert_sums = $alert_sums;


// christian : demande d'enregistrement
	if (($force_ref)) {
		$afficheAccueil->referencement[]=array("texte"=>"Votre �tablissement n'est pas r�f�renc� parmi les utilisateurs de Gepi.","lien"=>"$gepiPath/referencement.php?etape=explication","titre"=>"Pourquoi est-ce utile ?");
		$afficheAccueil->referencement[]=array("texte"=>"Votre �tablissement n'est pas r�f�renc� parmi les utilisateurs de Gepi.","lien"=>"$gepiPath/referencement.php?etape=1","titre"=>"R�f�rencer votre �tablissement");
	}

// fin christian demande d'enregistrement

	// Test du mode de connexion (http ou https) :
	// FIXME: Les deux lignes ci-dessous ne sont-elles pas inutiles ?
	$uri = $_SERVER['PHP_SELF'];
	$parsed_uri = parse_url($uri);

	if (
		!isset($_SERVER['HTTPS'])
		OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
		OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https")
		) {
		$afficheAccueil->probleme_dir[]="Connexion non s�curis�e ! Vous *devez* acc�der � Gepi en HTTPS (v�rifiez la configuration de votre serveur web)";
		$test_https = 'n';
	}

	if (ini_get("register_globals") == "1") {
		$afficheAccueil->probleme_dir[]="PHP potentiellement mal configur� (register_globals=on)! Pour pr�venir certaines failles de s�curit�, vous *devez* configurer PHP avec le param�tre register_globals � off.";
	}
    
    if (version_compare(PHP_VERSION,'5.2.4')<0) {
        $afficheAccueil->probleme_dir[]="G�pi n�cessite une version de php sup�rieure � la version 5.2.4 pour fonctionner de mani�re optimale. Il est conseill� de mettre � jour votre version de PHP.";
    }
    if(file_exists('./lib/global.inc')){
        $afficheAccueil->probleme_dir[]="Le fichier global.inc dans le r�pertoire lib est obsol�te. Vous devez le supprimer manuellement.";
    }
    /**
     * Pour Debug : D�commenter les 2 lignes si pas en multisites
     */
    /**
     $_COOKIE['RNE']="essai";
     $multisite='y';
     */
    if (($multisite=='y')&&(isset($_COOKIE['RNE']))) {
      if (!check_photos_multisite($messageErreur)) {
        $afficheAccueil->probleme_dir[]=$messageErreur;
      }
    }
    
	$sql="SELECT DISTINCT id_groupe, declarant FROM j_signalement WHERE nature='erreur_affect';";
	$res_sign=mysql_query($sql);
	if(mysql_num_rows($res_sign)>0) {
		$tbs_signalement="<p class='tbs_signalement'>Une ou des erreurs d'affectation d'�l�ves ont �t� signal�es dans le ou les enseignements suivants&nbsp;:<br />\n";
		while($lig_sign=mysql_fetch_object($res_sign)) {
			$tmp_tab_champ=array('classes');
			$current_group_sign=get_group($lig_sign->id_groupe,$tmp_tab_champ);
			$current_group_sign['description']=str_replace  ( "&"  , "&amp;"  , $current_group_sign['description'] );
			$tbs_signalement.="<a href='groupes/edit_eleves.php?id_groupe=".$lig_sign->id_groupe."&amp;id_classe=".$current_group_sign['classes']['list'][0]."'>".$current_group_sign['name']." (<em>".$current_group_sign['description']." ".$current_group_sign['classlist_string']."</em>)</a> signal� par ".affiche_utilisateur($lig_sign->declarant,$current_group_sign['classes']['list'][0])."<br />\n";
		}
		$tbs_signalement.="</p>\n";
		$afficheAccueil->signalement=$tbs_signalement;
	}

	//echo "</div>\n";
}elseif(($_SESSION['statut']=="professeur")||($_SESSION['statut']=="scolarite")||($_SESSION['statut']=="cpe")||($_SESSION['statut']=="secours")){
	if (!check_user_temp_directory()) {

		$afficheAccueil->probleme_dir[]="Il y a eu un probl�me avec la mise � jour du r�pertoire temp.";
		$afficheAccueil->probleme_dir[]="Veuillez contacter l'administrateur pour r�soudre ce probl�me.";
		$_SESSION['user_temp_directory']='n';
	}else{
		$_SESSION['user_temp_directory']='y';
	}
}

// ----- interface graphique prof -----
if($_SESSION['statut']=="professeur"){
	$tbs_interface_graphique[]=array("classe"=>"bold","lien"=>"accueil_simpl_prof.php","titre"=>"Interface graphique");
}

//Affichage des messages
include("affichage_des_messages.inc.php");

//==========================================================================================
// La suite (d�tail du menu de la page d'accueil) est dans class_php/class_page_accueil.php
//==========================================================================================

$tbs_microtime	="";
$tbs_pmv="";
require_once ("./lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseign�
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// D�commenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//  $affiche_debug=debug_var();


$nom_gabarit = './templates/'.$_SESSION['rep_gabarits'].'/accueil_template.php';
include($nom_gabarit);


?>
