<?php

/**
 *
 * @copyright 2008-2013
 *
 */

$niveau_arbo = 1;
// Initialisations files
require_once("../lib/initialisations.inc.php");
require_once("../cahier_texte/fonctions_cdt.inc.php");

// Ici, il faudra vérifier les droits et les autorisations sur les flux
$type_rss = isset($_GET["type"]) ? $_GET["type"] : NULL;
$eleve_l = isset($_GET["ele_l"]) ? $_GET["ele_l"] : NULL;
$uri = isset($_GET["uri"]) ? $_GET["uri"] : NULL;

// On vérifie si la table des uri existe (si elle existe, elle est forcément remplie
$test_table = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'rss_users'"));
if ($test_table == 0) {
	die();
}
else {

	// On peut alors vérifier si l'uri demandée est la bonne
	$test_uri = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT id FROM rss_users WHERE user_login = '".$eleve_l."' AND user_uri = '".$uri."' LIMIT 1"));

	if ($test_uri == 1) {
		// C'est bon, on peut générer les réponses
	}else{
		die();
	}
}

$rss_email_mode=getSettingValue('rss_email_mode');
$rss_email_prof=getSettingAOui('rss_email_prof');
if($rss_email_mode=='email_admin') {
	$rss_email_defaut=getSettingValue('gepiAdminAdress');
}
else {
	$rss_email_defaut=getSettingValue('gepiSchoolEmail');
}
$tab_rss_infos_prof=array();

// =========================== Cahier de textes =================================
if ($type_rss == "cdt") {
	$items = retourneDevoirs($eleve_l);
	function get_prof_login($prof_l) {
		global $tab_rss_infos_prof, $rss_email_prof;

		if(isset($tab_rss_infos_prof[$prof_l]["civ_nom"])) {
			$rep=$tab_rss_infos_prof[$prof_l]["civ_nom"];
		}
		else {
			/*
			 * permet de construire le nom du prof avec la bonne civilité
			*/
			$sql = "SELECT nom, civilite, show_email, email FROM utilisateurs WHERE login = '".$prof_l."' AND statut = 'professeur'";
			$query = mysqli_query($GLOBALS["mysqli"], $sql);
			$test = mysqli_num_rows($query);
			if ($test == 1) {
				// c'est bon, on construit son nom
				$prof = mysqli_fetch_array($query);
				if (isset($prof["civilite"])) {
					$civilite = $prof["civilite"];
				}
				else {
				 	$civilite = "";
				}
				$rep = $civilite.'&nbsp;'.$prof["nom"];

				$tab_rss_infos_prof[$prof_l]["civ_nom"]=$rep;
				if($rss_email_prof) {
					if($prof["show_email"]=="yes") {
						$tab_rss_infos_prof[$prof_l]["email"]=$prof["email"];
					}
				}
			}
			else {
				$rep = 'Erreur dans la reconnaissance de l\'enseignant';
			}
		}

		return $rep;
	}
	$noms["nom"] = $noms["prenom"] = NULL;
	$noms = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT nom, prenom FROM eleves WHERE login = '".$eleve_l."' LIMIT 1"));
	$title_rss = 'Cahier de textes - '.getSettingValue("gepiSchoolName").' ('.getSettingValue("gepiYear").')';
	$title_rss.= ': '.get_nom_prenom_eleve($eleve_l);
	$description_rss = 'Les devoirs à faire de '.$noms["nom"].' '.$noms["prenom"];
}
// =========================fin des cahiers de textes ===========================

$ServerProtocole = ( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on') ) ? 'https://' : 'http://' ;

 // Import de la classe RSSFeed
require('RSSFeed/RSSFeed.class.php');

 // Création des entêtes du flux RSS
//$oRssFeed = new RSSFeed('utf-8');
$oRssFeed = new RSSFeed('UTF-8');
$oRssFeed->setCloud($_SERVER["SERVER_NAME"], $_SERVER["REMOTE_PORT"], $gepiPath, '', 'http');
$oRssFeed->setProtectString(true);
$oRssFeed->setTitle($title_rss);
$oRssFeed->setDescription($description_rss);
$oRssFeed->setLink($ServerProtocole.$_SERVER["SERVER_NAME"].$gepiPath);
$oRssFeed->setPubDate('2007-12-31');
$oRssFeed->setLastBuildDate(date('Y-m-d'));
//$oRssFeed->setWebMaster(getSettingValue("gepiSchoolEmail"),'ADMIN');
//$oRssFeed->setManagingEditor(getSettingValue("gepiSchoolEmail"),'ADMIN');
$oRssFeed->setWebMaster($rss_email_defaut,'ADMIN');
$oRssFeed->setManagingEditor($rss_email_defaut,'ADMIN');
$oRssFeed->setImage($gepiPath.'/favicon.ico', 'GEPI', $ServerProtocole.$_SERVER["SERVER_NAME"]);
$oRssFeed->setCopyright('(L) - GEPI '.getSettingValue('version'));
$oRssFeed->setGenerator('Généré par RSSFeed Class de Hugo "Emacs" HAMON - http://www.apprendre-php.com');
$oRssFeed->setLanguage('fr');

$finfo = finfo_open(FILEINFO_MIME_TYPE);

// Ajout des news au flux
if ($items["cdt_dev"]["count"] != 0) {
	for($a = 0; $a < $items["cdt_dev"]["count"] ; $a++)
	{

		// On récupère des données
		$donnees = get_group($items["cdt_dev"][$a]["id_groupe"]);
		$prof = get_prof_login($items["cdt_dev"][$a]["id_login"]);

		// Récupération de l'email
		//$sEmail = getSettingValue("gepiSchoolEmail");
		if(isset($tab_rss_infos_prof[$prof]["email"])) {
			$sEmail = $tab_rss_infos_prof[$prof]["email"];
		}
		else {
			$sEmail = $rss_email_defaut;
		}
		$oRssItem = new RSSFeedItem();
		$oRssItem->setTitle($donnees["description"].' - Pour le '.date("d-m-Y", $items["cdt_dev"][$a]["date_ct"]));

		$contenu_courant='-> Travail donné par '.$prof.' : '.$items["cdt_dev"][$a]["contenu"];
		$tab_autres_docs_joints=array();
		if(isset($items["cdt_dev"][$a]["doc_joint"])) {
			//$contenu_courant.="\n";
			if(count($items["cdt_dev"][$a]["doc_joint"])==1) {
				$contenu_courant.=" - Un document est joint : ";
			}
			else {
				$contenu_courant.=" - ".count($items["cdt_dev"][$a]["doc_joint"])." documents sont joints : ";
			}
			$tab_autres_docs_joints=array();
			for($loop=0;$loop<count($items["cdt_dev"][$a]["doc_joint"]);$loop++) {
				//$contenu_courant.="\n".$items["cdt_dev"][$a]["doc_joint"][$loop]['titre'];
				//$contenu_courant.="\n"."<a href='https://".$_SERVER["SERVER_NAME"].$gepiPath."/".preg_replace("|^../|","",$items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])."'>".$items["cdt_dev"][$a]["doc_joint"][$loop]['titre']."</a>";
				if($loop>0) {$contenu_courant.=", ";}
				$url_doc_courant="https://".$_SERVER["SERVER_NAME"].$gepiPath."/".preg_replace("|^../|","",$items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement']);
				$contenu_courant.=$items["cdt_dev"][$a]["doc_joint"][$loop]['titre']." ($url_doc_courant)";

				$mimeType="";
				if(file_exists($items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
					$mimeType=finfo_file($finfo, $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement']);
					if($mimeType=="") {
						if(preg_match("/.jpg$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="image/jpeg";
						}
						elseif(preg_match("/.jpeg$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="image/jpeg";
						}
						elseif(preg_match("/.png$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="image/png";
						}
						elseif(preg_match("/.gif$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="image/gif";
						}
						elseif(preg_match("/.mp3$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="audio/mpeg";
						}
						elseif(preg_match("/.wav$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="audio/x-wav";
						}
						elseif(preg_match("/.zip$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="application/zip";
						}
						elseif(preg_match("/.ggb$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="application/zip";
						}
						elseif(preg_match("/.txt$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="text/plain";
						}
						elseif(preg_match("/.doc$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="application/msword";
						}
						elseif(preg_match("/.pdf$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="application/pdf";
						}
						elseif(preg_match("/.xls$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="application/vnd.ms-excel";
						}
						elseif(preg_match("/.pps$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="application/vnd.ms-powerpoint";
						}
						elseif(preg_match("/.odt$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="application/vnd.oasis.opendocument.text";
						}
						elseif(preg_match("/.ods$/i", $items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement'])) {
							$mimeType="application/vnd.oasis.opendocument.spreadsheet";
						}
					}
				}

				if($mimeType!="") {
					// Une seule enclosure est supportée par RSS
					// S'il y a plusieurs doc joints, seul le dernier est proposé...
					if($loop==0) {
						$oRssItem->setEnclosure($url_doc_courant, filesize($items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement']), $mimeType);
					}
					else {
						$tab_autres_docs_joints[]=array($url_doc_courant, filesize($items["cdt_dev"][$a]["doc_joint"][$loop]['emplacement']), $mimeType);
					}
				}
			}
		}
		$oRssItem->setDescription($contenu_courant);

		$oRssItem->setLink($ServerProtocole.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php');
		$oRssItem->setGuid($ServerProtocole.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php', true);
		if(!empty($sEmail))
		{
			$oRssItem->setAuthor($sEmail, 'ADMIN');
		}
		$oRssItem->setPubDate(date("Y-m-d h:i:s", $items["cdt_dev"][$a]["date_ct"]));
		$oRssFeed->appendItem($oRssItem);
		$oRssItem = null;

		if(count($tab_autres_docs_joints)>0) {
			for($loop=0;$loop<count($tab_autres_docs_joints);$loop++) {
				$oRssItem = new RSSFeedItem();
				$oRssItem->setTitle($donnees["description"].' - Pour le '.date("d-m-Y", $items["cdt_dev"][$a]["date_ct"]));

				$contenu_courant='-> Travail donné par '.$prof.' : '.$items["cdt_dev"][$a]["contenu"];
				$contenu_courant='-> Document joint';

				$oRssItem->setEnclosure($tab_autres_docs_joints[$loop][0], $tab_autres_docs_joints[$loop][1], $tab_autres_docs_joints[$loop][2]);

				$oRssItem->setDescription($contenu_courant);

				$oRssItem->setLink($ServerProtocole.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php');
				$oRssItem->setGuid($ServerProtocole.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php', true);
				if(!empty($sEmail))
				{
					$oRssItem->setAuthor($sEmail, 'ADMIN');
				}
				$oRssItem->setPubDate(date("Y-m-d h:i:s", $items["cdt_dev"][$a]["date_ct"]));
				$oRssFeed->appendItem($oRssItem);
				$oRssItem = null;
			}
		}
	}
}elseif($items["cdt_dev"]["count"] == 0){

	// Récupération de l'email
	//$sEmail = getSettingValue("gepiSchoolEmail");
	$sEmail = $rss_email_defaut;
	$oRssItem = new RSSFeedItem();
	$oRssItem->setTitle('Le cahier de textes est vide');
	$oRssItem->setDescription('Rien &agrave; afficher -> Il faut toujours revoir les le&ccedil;ons du jour.');
	$oRssItem->setLink($ServerProtocole.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php');
	$oRssItem->setGuid($ServerProtocole.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php', true);
	if(!empty($sEmail))
	{
		$oRssItem->setAuthor($sEmail, 'ADMIN');
	}
	$oRssItem->setPubDate(date("Y-m-d h:i:s"));
	$oRssFeed->appendItem($oRssItem);
	$oRssItem = null;

}else{

	// Récupération de l'email
	//$sEmail = getSettingValue("gepiSchoolEmail");
	$sEmail = $rss_email_defaut;
	$oRssItem = new RSSFeedItem();
	$oRssItem->setTitle('ERREUR sur le CDT');
	$oRssItem->setDescription('Rien &agrave; afficher -> Il faut toujours apprendre les le&ccedil;ons du jour.');
	$oRssItem->setLink($ServerProtocole.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php');
	$oRssItem->setGuid($ServerProtocole.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php', true);
	if(!empty($sEmail))
	{
		$oRssItem->setAuthor($sEmail, 'ADMIN');
	}
	$oRssItem->setPubDate(date("Y-m-d h:i:s"));
	$oRssFeed->appendItem($oRssItem);
	$oRssItem = null;

}

 // Sauvegarde du flux RSS
$oRssFeed->save('../temp/rss-news.xml');
// Affichage sur la sortie standard
header('Content-Type: text/xml; charset=UTF-8');
$oRssFeed->display();
?>
