<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* @load_prefs.php
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version 0.2~20 Lcs-2.4.8
* Derniere mise a jour" => "28/02/2011
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/

header ('Content-type" => "text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
require "/var/www/Annu/includes/ldap.inc.php";
list ($idpers, $login)= isauth();

$user = $_POST['user']  ;
$fn = $_POST['fn'] ;	


#
#textes (voir multilinguisme en js -> a modifier)
# A revoir
$t_fr = array( "firstConnect" => "F&#233;licitations, vous venez de vous connecter pour la 1&#232;re fois sur votre espace perso Lcs. Afin de garantir la confidentialit&#233; de vos donn&#233;es, nous vous encourageons, &agrave; changer votre mot de passe <a class=\"open_win ext_link\" href=\"../Annu/mod_pwd.php\" rel=\"annu\" title=\"\">en suivant ce lien... </a>",
//$t_fr = array( "firstConnect" => "premiere_connexion",
	"lastConnect" => "derniere_connexion",
	"nbConnect" => "nombre_connexions"
	);
	
#
# prefs user :  les preferences par defaut
#
$prefs= array(
	"wallpaper" => "core/images/misc/LCS_Desktop.jpg", // le wallpaper par defaut hors connexion
	"s_idart" => "0", // dernier article spip
	"idpers" => $idpers, // idpers
	"domain" => $domain, // damains
	"baseurl" => $baseurl, // url lcs
	"pwchg" => pwdMustChange($login) ? "N" : "Y" , // password modifié ?
	"user" => array( // preferences utilisateur
		"login" => $login ? $login:'unconnected', //le login du user
		"wallpaper" => "core/images/misc/LCS_Desktop.jpg", // wallpaper
		"pos_wallpaper" => "wallpaper", //position du wpp. Accepte "wallpaper", 
		"bgcolor" => "#f5f5f5",
		"bgopct" => "50",
		"iconsize" => "48",
		"iconsfield" => "50",
		"quicklaunch" => "0",
		"winsize" => "content",
		"icons" => array(
		)
	)//,"btr" => array()
);

#
# settings
# 
$stgo = "../lcs/statandgo.php?use=";
$applis= array(
	"prefs" => array(
		"txt" => "Pr&eacute;f&eacute;rences",
		"url" => "core/user_form_prefs.php",
		"rev" => "prefs",
		"img" => "core/images/app/lcslogo-prefs.png",
		"typ" => "buro"
	)
);
if ( $idpers==0 ) {
	$applis['auth'] = array(
		"txt" => "Se connecter",
		"url" => "../lcs/auth.php",
		"rev" => "auth",
		"img" => "core/images/icons/icon_22_connect.png",
		"typ" => "buro"
	);
}else{
	$applis['auth'] = array(
		"txt" => "Se d&eacute;connecter",
		"url" => "../lcs/logout.php",
		"rev" => "auth",
		"img" => "core/images/icons/icon_22_stop.png",
		"typ" => "buro"
	);
}

#
# informations etab
$result=mysql_query("SELECT * from params where srv_id=0");
if ($result)
while ($r=mysql_fetch_array($result))
$$r["name"]=$r["value"];
else
die ("param&#232;tres absents de la base de donn&#233;e");
mysql_free_result($result);
$prefs['tyetab']=$organizationalunit;
$prefs['etab']=$organization;
$prefs['ville']=$locality;
$prefs['acad']=$province;
#

#
# menus admin
#
if ( acces_btn_admin($idpers, $login) == "Y") { // acces au menu d'administration
	getmenuarray();
	for ($i=0; $i< count($liens); $i++) {
		// Affichage item menu
		if ( (strlen($liens[$i][0]) > 0) && ( ldap_get_right($liens[$i][1],$login)=="Y" ) ) 
			$applis['admin'][$i] = array(
			"txt" => $liens[$i][0],
			"url" => "#",
			"rev" => "admin",
			"img" => "core/images/app/lcslogo-admin.png",
			"typ" => "admn",
			"smn"=>array()
			);
		if ( count($liens[$i]) > 0 ) #echo "<ul>\n";
		  for ($j=2; $j< count($liens[$i]); $j=$j+3) {
			if ( ldap_get_right($liens[$i][$j+2],$login)=="Y" ) {
				// On vire le target quand il existe (cas de pla)
				$tmp = explode ("\"",$liens[$i][$j+1]);
				# if ( $tmp[1] == "target='_new'" ) $liens[$i][$j+1] = $tmp[0];
				if ( preg_match("/target/",$tmp[1]) ) $liens[$i][$j+1] = $tmp[0];
				$applis['admin'][$i]['smn'][]= array(
					"txt" => $liens[$i][$j],
					"url" => "../Admin/".preg_replace("/\/Admin\//","",$liens[$i][$j+1])
				);
	
			}
		  }	
	}
	//
	$dirData ="../data/";
	if( is_dir($dirData) ) {
		$prefs['ress']=array();
		$dIcn = scandir($dirData);
		foreach($dIcn as $k=>$dirGp){
			if($dirGp !="." && $dirGp!=".." && is_dir($dirData.$dirGp)){
				$files = scandir($dirData.$dirGp);
				foreach($files as $t=>$icn){
					if($icn !="." && $icn!=".." &&$icn !=$icnLast &&  is_file($dirData.$dirGp."/".$icn)){
						$_ficn=$dirData.$dirGp."/".$icn;
						$prefs['ress'][$icn] =  json_decode(file_get_contents($_ficn));
						$icsLast=$icn;
					}
				}
			}
		}
	}
			
	$applis['addicon']= array(
		"txt" => "Ajouter un lien partag&eacute;",
		"url" => "#",
		"rev" => "addicon",
		"img" => "core/images/app/lcslogo-lcs.png",
		"typ" => "buro",
		"smn" => ""
	);

	$prefs['user']['statut']='admin';
} // Fin menu admin

#
# applis
#
list($user, $groups)=people_get_variables($login, true);

# Un utilisateur est authentifie  et a modifie son mot de passe
# on affiche les menus applis

	# connexion bdd
	# Revoir : 
	if (!@mysql_select_db($DBAUTH, $authlink)) 
		die ("S&#233;lection de base de donn&#233;es impossible.");
	    
	$query = "SELECT * from applis";
	$result = @mysql_query($query, $authlink);
	if ($result)
		while ($r=@mysql_fetch_array($result))
			$$r["name"]=$r["value"];
	else
		die ("Param&#232;tres absents de la base de donn&#233;es.");
	@mysql_free_result($result);
	// ../../
	# modules
	$queryM="SELECT  name, value from applis where type='M' order by name";
	$resultM=mysql_query($queryM);
	if ($resultM) {
		while ( $r=mysql_fetch_object($resultM) ) {
			if ( $r->name == "filexplorer" ) $filexplorer = true;
			if ( $r->name == "pma" ) $pma = true;
			if ( $r->name == "smbwebclient" ) $smbwebclient = true;            
		}
	}
	mysql_free_result($resultM);
if ( $idpers!=0 && !pwdMustChange($login)) {
	# settings
	# parametres des liens applis
	
	// Appli Annuaire
	$applis['annu']= array(
		"txt" => "Annuaire des utilisateurs",
		"url" => $stgo."Annu",
		"rev" => "annu",
		"img" => "core/images/app/lcslogo-annu.png",
		"typ" => "srvc",
		"smn" => array(
			"0" => array(
				"txt" => "Effectuer une recherche",
				"url" =>  "../Annu/search.php"
			),
			"1" => array(
				"txt" => "Voir ma fiche",
				"url" =>  "../Annu/me.php"
			),
			"2" => array(
				"txt" => "Modifier ma fiche",
				"url" => "../Annu/mod_entry.php"
			),
			"3" => array(
				"txt" => "Changer de mot de passe",
				"url" =>  "../Annu/mod_pwd.php"
	 		)
		)
	);
	# spip
	if(isset($spip)){
		$applis['spip']= array(
			"txt" => "Forum",
			"url" => $stgo."spip",
			"rev" => "spip",
			"img" => "core/images/app/lcslogo-spip.png",
			"typ" => "appl",
			"smn" => array(
				"0" => array(
					"txt" => "&Eacute;crire un nouvel article",
					"url" => "../spip/ecrire/?exec=articles_edit&new=oui"
				)
			)
		);
	}
	# webmail
	if(isset($squirrelmail)){
		$test_webmail=$squirrelmail;
		$app_webmail= array(
			"txt" => "Webmail",
			"url" => $stgo."squirrelmail",
			"rev" => "squirrelmail",
			"img" => "core/images/app/lcslogo-webmail.png",
			"typ" => "srvc",
			"smn" => array(
				"0" => array(
					"txt" => "Consulter vos messages",
					"url" =>  $stgo."squirrelmail"
				),
				"1" => array(
					"txt" => "Envoyer un message",
					"url" => "../squirrelmail/src/compose.php?mailbox=INBOX&startMessage=1"
				)
			)
		);
		$test_squir='1';
	}        
	else $test_webmail="0";
    //test si roundcube est installe pour redirection mails
	if (isset($roundcube)) {
		$test_webmail=$roundcube;
		$app_webmail= array(
			"txt" => "Webmail",
			"url" => $stgo."roundcube",
			"rev" => "roundcube",
			"img" => "core/images/app/lcslogo-webmail.png",
			"typ" => "srvc",
			"smn" => array(
				"0" => array(
					"txt" => "Consulter vos messages",
					"url" =>  $stgo."roundcube"
				),
				"1" => array(
					"txt" => "Envoyer un message",
					"url" =>  "../roundcube/?_task=mail&_action=compose"
				)
			)
		);
		$test_squir='1';
	}
    //fin test roundcube
    //webmail == squirelamil ou roundcube
	$applis['webmail'] = $app_webmail;
	
	# filexplorer
	if (isset($filexplorer)) {
		$applis['filexplorer']= array(
			"txt" => "Explorateur de fichiers",
			"url" => "../filexplorer/",
			"rev" => "filexplorer",
			"img" => "core/images/app/lcslogo-filexplorer.png",
			"typ" => "srvc",
			"smn" => ""
		);
	}
	# filexplorer
	/*
	if (is_dir('../../../elfinder') ){
		$applis['filexplorer']= array(
			"txt" => "Explorateur de fichiers",
			"url" => "../elfinder/",
			"rev" => "filexplorer",
			"img" => "core/images/app/lcslogo-filexplorer.png",
			"typ" => "srvc",
			"smn" => ""
		);
	}
	*/
	# phpmyadmin
	if (isset($pma)) {
		$applis['pma']= array(
			"txt" => "Gestion base de donn&eacute;es",
			"url" => $stgo."pma",
			"rev" => "pma",
			"img" => "core/images/app/lcslogo-pma.png",
			"typ" => "srvc",
			"smn" => ""
		);
	}
	# smbwebclient
	if ( $se3netbios != "" && $se3domain != "" && isset($smbwebclient) ) {
		$applis['smbwc']= array(
			"txt" => "Client SE3",
			"url" => $stgo."smbwc",
			"rev" => "smbwc",
			"img" => "core/images/app/lcslogo-smbwc.png",
			"typ" => "srvc",
			"smn" => ""
		);
	}
	
	
	# Liens dynamiques vers les plugins installes 
	$query="SELECT * from applis where type='P' OR type='N' order by name";
	$result=mysql_query($query);
	if ($result) {
		while ($r=mysql_fetch_object($result)) {
			if (( $r->value == "1" ) and ! ( file_exists("/usr/share/lcs/Plugins/".$r->chemin."/.applihide"))) {
				$imgdktp= "images/app/lcslogo-".strtolower($r->name).".png";
				$imgplgn= "../Plugins/".$r->chemin."/Images/plugin_icon.png";
				$applis[strtolower($r->name)] = array(
					'txt' => $r->descr,
					'url'  => $stgo.$r->name,
					'rev'  => strtolower($r->name),
					'img'  => is_file("../".$imgdktp) ? "core/".$imgdktp : $imgplgn,
					"typ" => "appl",
					'top'  => "",
					'left' => ""
				);
				if(strtolower($r->name) == "maintinfo" ) 
				$applis["maintinfo"]["smn"] = array(
					"0" => array(
						"txt" => "Demande de support",
						"url" =>  "../Plugins/Maintenance/demande_support.php"
					),
					"1" => array(
						"txt" => "En attente",
						"url" =>  "../Plugins/Maintenance/index.php?mnu_choice=wait"
	 				),
					"2" => array(
						"txt" => "En cours",
						"url" => "../Plugins/Maintenance/index.php?mnu_choice=myspool"
					),
					"3" => array(
						"txt" => "Historique",
						"url" =>  "../Plugins/Maintenance/index.php?mnu_choice=wait"
	 				)
				);
				$applis["maintinfo"]["typ"] = "srvc";
			}
		}
	}
}//fin applis

#le wmail
$prefs['wmail']=$test_squir;

# page d'accueil hors connexion
	// Page d'accueil 
if ( $url_redirect == "accueil.php" || $url_redirect == "../squidGuard/pageinterdite.html" ) 
	$url_accueil = $url_redirect;
	// (voir pour monLcs)
if ( $url_accueil == "accueil.php" && is_dir ("/var/www/monlcs") )  
  	$url_accueil = "../spip/";
$prefs['url_accueil']=$url_accueil;

# monLcs

$prefs['monlcs'] = is_dir("/var/www/monlcs") ? 1 : 0;

#
# infos user
#
if ( $idpers!=0 ) { 
	$infos=array();
	$infos['idpers']=$idpers;
	//test listes de diffusion
	exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/mailing_list.cf", $AllOutPut, $ReturnValueShareName);
	$listediff = 0;
	if ( count($AllOutPut) >= 1) $listediff = 1;
	// fin test listes de diffusion
	
	$infos = $user;
	if (pwdMustChange($login)) { 
		$infos['connect'] = $t_fr['firstConnect'];
	} else {
		$accord == "";
		if ($user["sexe"] == "F") $accord="e";
		$infos['connect'] =$t_fr['lastConnect']. displogin($idpers) . "<br />Vous vous &ecirc;tes connect&eacute;".$accord." " . dispstats($idpers) . " fois &agrave; votre espace perso." ;
	}
	
	if ( count($groups) ) {
		$dirIcn ="../data/";
		$co=$ma=$eq=$di=$cl=0;
		$tbl_gp = array("Administratifs","Profs","Eleves");
		$ptrn= array('/Classe/','/Cours/','/Equipe/','/Matière/');

		if ($prefs['user']['statut']=='admin') {
		}

		for ($loop=0; $loop < count ($groups) ; $loop++) {
			// on recherche les icones attribuees aux groupes
			$dIcn =$dirIcn. $groups[$loop]["cn"];
			if( is_dir($dIcn) ) {
				$files = scandir($dIcn);
				foreach($files as $k=>$r){
					$prefs['files'][$k.'_'.$loop] = $r;
					$_f=$dIcn."/".$r;
					if($r !="." && $r!=".." && is_file($_f)){
					//$prefs['icons'][$r]['file']=  $_f;
						$prefs['icons'][$r]=  json_decode(file_get_contents($_f));
					}
				}
			}
			
			$gex = explode('_', $groups[$loop]["cn"]);
			$g = preg_replace('/ /','',$gex[0]);
				if (in_array($groups[$loop]["cn"], $tbl_gp)){ 
					$infos['group']['gp']['name'] = preg_replace('/ /','',$gex[0]);
			} else 
				 //	$infos['group'][$g][$loop]['name'] = preg_replace('/ /','',$gex[1]);
				 	$infos['group'][$g][$loop] = preg_replace('/$g/',' ', $groups[$loop]["cn"]);
	
	
			// Teste si n&#233;cessit&#233; d'affichage menu Ouverture/Fermeture Bdd et espace web perso des Eleves
			if ($groups[$loop]["cn"]=="Eleves") $ToggleAff=1;
	    	}
	}
} else {
	$infos['idpers']="no_".$idpers;
}

# TODO: supprimer la variable $obj. N'est plus utilisee
$obj = "nul";

#__/__/__/__/__/__/__/__/__/__/__/__/__/__/
# Cas de la maj depuis une version < 2.4.8
# On recupere les prefs dans le fichier PREFS_user.xml
# TODO: A supprimeer dans les versions futures
if(is_file($uXml="/home/".$login."/Profile/PREFS_".$login.".xml")){
	$prefs['user'] = USERPREFS_Display_Icons($uXml,40,1,1);
	// on supprime les fichiers des versions < 2.4.8
	$cmd='rm /home/'.$login.'/Profile/PREFS_'.$login.'*';
	exec($cmd);
} 
#__/__/__/__/__/__/__/__/__/__/__/__/__/__/

#
# si un fichier prefs existe, on va le chercher
#
else if ( is_file('/home/'.$login.'/Profile/PREFS_'.$login.'.json') ) {
	$url='/home/'.$login.'/Profile/PREFS_'.$login.'.json';
	$objsn = json_decode(file_get_contents($url));
	#$prefs['user'] = $objsn;
	foreach( $objsn as $k=>$val) {
		$k=="icons" ? $prefs['user']['icons'] = $val : $prefs['user'][$k]=$val;
	}
}
else if( is_file('../json/PREFS_default.json') ) {
	$url='../json/PREFS_default.json';
	$objsn = json_decode(file_get_contents($url));
	foreach( $objsn as $k=>$val) 
	{
		$k!="icons" ? $prefs['user'][$k]=$val : '';
	}
	foreach($applis as $appli)
	{
		array_push($prefs['user']['icons'], $appli);
	}
}else {
	$ca=0;
foreach ($applis as $app=>$icon) {
	if( $app!="admin" && $app!="auth" && $app!="apdesk") {
	$prefs['user']['icons'][$ca]=$icon;
	$ca++;
	}
}
}

#
# on renvoie quoi ? ( data.fn  )
#
#		$prefs['applis'] = $applis;
#		$prefs['user']['infos'] = $infos;
#		$prefs['pref'] = $obj ;
#       $print_r = print_r( $prefs );

switch($fn) {
	case "all":
		$prefs['applis'] = $applis;
		$prefs['user']['infos'] = $infos;
		#$prefs['pref'] = $obj ;
		echo json_encode( $prefs );
		$_fp=fopen("../json/PREFS_settings_default.json","w");
		$_json_fp=json_encode( $prefs );
		fwrite($_fp,$_json_fp);
		fclose($_fp);
		break;
		
	case "applis":
		echo json_encode( array( "user"=>array( "icons"=>$applis ) ) );
		break;
		
	case "icons":
		echo json_encode( array( "user"=>array( "icons"=>$applis ), "prefs"=>json_decode($obj) ) );
		break;
		
	default: return;
}


/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
 * Recuperation des prefs en .xml Lcs-Desktop < 2.4.8
 * A supprimer dans les versions futures
__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/

$USERPREFS_Content = array();

function USERPREFS_Display_Tags($item, $type)
{
		$y = array();
		$tnl = $item->getElementsByTagName("icontext");
		$tnl = $tnl->item(0);
		$text = $tnl->firstChild->textContent;

		$tnl = $item->getElementsByTagName("iconurl");
		$tnl = $tnl->item(0);
		$link = $tnl->firstChild->textContent;
		
		$tnl = $item->getElementsByTagName("iconwin");
		$tnl = $tnl->item(0);
		$win = $tnl->firstChild->textContent;		

		$tnl = $item->getElementsByTagName("icontitle");
		$tnl = $tnl->item(0);
		$title = $tnl->firstChild->textContent;		

		$tnl = $item->getElementsByTagName("iconrev");
		$tnl = $tnl->item(0);
		$rev = $tnl->firstChild->textContent;		

		$tnl = $item->getElementsByTagName("iconimg");
		$tnl = $tnl->item(0);
		$img = $tnl->firstChild->textContent;

		$y["txt"] = $text;
		$y["link"] = $link;
		$y["win"] = $win;		
		$y["title"] = $title;		
		$y["rev"] = $rev;
		$y["img"] = $img;
		$y["type"] = $type;
		
		return $y;
}

function USERPREFS_Display_Icons($url, $size = 40, $site = 0, $withdate = 0)
{
	global $USERPREFS_Content;

	$opened = false;
	$oldPrefs = array();
	#$page = "";
	$iconsDock ="";
	$site = (intval($site) == 0) ? 1 : 0;

	$doc  = new DOMDocument();
	$doc->load($url);

	$channels = $doc->getElementsByTagName("userburo");
	
	$USERPREFS_Content = array();
	
	foreach($channels as $channel)
	{
	$items = $channel->getElementsByTagName("icon");
		foreach($items as $item)
		{
			$y = USERPREFS_Display_Tags($item, 1);	// recuperation des icones
			array_push($USERPREFS_Content, $y);
		}
	}

	if($size > 0)
		$recents = array_slice($USERPREFS_Content, $site, $size + 1 - $site);
		$oldPrefs['icons']=$recents;

	$tnl= $channel->getElementsByTagName("quicklaunch");
	$tnl = $tnl->item(0);
	$oldPrefs['quicklaunch'] = $tnl->firstChild->textContent;
	
	$tnl= $channel->getElementsByTagName("wallpaper");
	$tnl = $tnl->item(0);
	$oldPrefs['wallpaper'] = $tnl->firstChild->textContent;

	$tnl= $channel->getElementsByTagName("pos_wallpaper");
	$tnl = $tnl->item(0);
	$oldPrefs['pos_wallpaper'] = $tnl->firstChild->textContent;

	$tnl= $channel->getElementsByTagName("bgcolor");
	$tnl = $tnl->item(0);
	$oldPrefs['bgcolor'] = $tnl->firstChild->textContent;
	
	$tnl= $channel->getElementsByTagName("iconsize");
	$tnl = $tnl->item(0);
	$oldPrefs['iconsize'] = $tnl->firstChild->textContent;

	$tnl= $channel->getElementsByTagName("iconsfield");
	$tnl = $tnl->item(0);
	$oldPrefs['iconsfield'] = $tnl->firstChild->textContent;

	$tnl= $channel->getElementsByTagName("s_idart");
	$tnl = $tnl->item(0);
	$oldPrefs['s_idart'] = $tnl->firstChild->textContent;
	
	$tnl= $channel->getElementsByTagName("winsize");
	$tnl = $tnl->item(0);
	$oldPrefs['winsize'] = $tnl->firstChild->textContent;
	
	return $oldPrefs;
	
}

?>