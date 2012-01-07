<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* @load_setting.php
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

$iam_user = $_POST['user']  ;
$_actn = $_POST['actn'] ;	

/**
* $_srvr : tableau des  parametres serverur
*@type : Object
*/
$_srvr	= array();

/**
* $_etab : tableau des infos etablissement tirees de l'annuaire LDAP
*@type : Object
*/
$_etab	=  array();

/**
* $_user : tableau des infos utilisateurs tirees de l'annuaire LDAP
*@type : Object
*/
$_user	= array();

/**
* $_prefs : preferences d'affichage utissateur
* a la premiere connexion, soit la conf par defaut definie ici,
* soit la conf par defaut enregistree par l'admin
* ensuite lecture du fichier de conf utilisateur (/home/[user]/Profile/PREFS_[user].json)
*@type : Object
*/
$_opts	= array(
	"wallpaper" => "core/images/misc/LCS_Desktop.jpg", // wallpaper
	"pos_wallpaper" => "wallpaper", //position du wpp. Accepte "wallpaper", 
	"bgcolor" => "#6789ab",// background color
	"bgopct" => 50, // background opacity
	"iconsize" => 48, // icons size
	"iconsfield" => 50, // height of the display field of all icons (percent)
	"quicklaunch" => 0, // display quicklaunch
	"winsize" => "content", // size of opening windows (values : [content], [fullwin], [perso] )
	"win_w" => 60, 	// width of opening window if winsize value is perso (percent of desktop width)
	"win_h" => 60 ,	// height of opening window if winsize value is perso (percent of desktop height)
	"s_idart" => 0 // id dernier article du forum vu par le user (date)
);

/**
* $_prms : tableau des  parametres admin desktop
*@type : Object
*/
$_prms	= array(
	"lang" => "fr",
	"maintUrl" => '', 	
	"showGroups" => 0, 	
	"notifForumFreq" => 10 	// frequence of refresf of the forum notification, frequence de rafraichissement de la notif du forum
);

/**
* $_apps : tableau des applis dispo et validees. sous la forme cle=d_appli et valeur=tableau (lien, icone, categorie, etc)
*@type : Object
*/
$_apps	= array();

/**
* $_noap : tableau des applis masquees aux eleves dans le menu
*@type : Object
*@TODO : n'est pas encore utilise
*/
$_noap	= array();

/**
* $_admn : tableau menu admministration. 
* est merge avec les applis ($_apps)
*@type : Object
*/
$_admn	= array();

/**
* $_icns : tableau des icones du bureau de l'itilisateur
* a la premiere connexion on envoie:
* soit la conf par defaut enregistree par l'admin
* soit la liste des applis
* ensuite lecture de la conf utilisateur
*@type : Array
*/
$_icns	=array();

/**
* $_qkch : tableau des icones de la barre quicklaunch
*@type : Object
*@TODO : n'est pas encore fonctionnel
*/
$_qkch	= array();

/**
* $_ress : tableau des ressources partagess
*@type : Object
*/
$_ress=array();

/**
* $_ssmn :  tableau des sous-menus
* cree a la mano, les sous-menus ne sont pas ds la base
* aurait sans doute plus sa place dans un fichier de conf
*@type : Object
*/
$_ssmn = array(
// annuaire
	"annu" => array(
		"search" => array(
			"txt" => "Effectuer une recherche",
			"url" =>  "../Annu/search.php"
		),
		"view" => array(
			"txt" => "Voir ma fiche",
			"url" =>  "../Annu/me.php"
		),
		"edit" => array(
			"txt" => "Modifier ma fiche",
			"url" => "../Annu/mod_entry.php"
		),
		"chpw" => array(
			"txt" => "Changer de mot de passe",
			"url" =>  "../Annu/mod_pwd.php"
 		)
	),
	// forum
	"spip" => array(
		"new" => array(
			"txt" => "&Eacute;crire un nouvel article",
			"url" => "../spip/ecrire/?exec=articles_edit&new=oui"
		)
	),
	// valeurs de l'url en fonction du webmail (squirrelmail ou roundcube)
	"webmail" => array(
		"view" => array(
			"txt" => "Consulter vos messages",
			"url" =>  ""
		),
		"compose" => array(
			"txt" => "Envoyer un message",
			"url" => ""
		)
	),
	//maintenance
	"maintenance" => array(
		"call" => array(
			"txt" => "Demande de support",
			"url" =>  "../Plugins/Maintenance/demande_support.php"
		),
		"wait" => array(
			"txt" => "En attente",
			"url" =>  "../Plugins/Maintenance/index.php?mnu_choice=wait"
	 	),
		"progress" => array(
			"txt" => "En cours",
			"url" => "../Plugins/Maintenance/index.php?mnu_choice=myspool"
		),
		"history" => array(
			"txt" => "Historique",
			"url" =>  "../Plugins/Maintenance/index.php?mnu_choice=wait"
	 	)
	)
);



/**
********- Functions -**********
*/

/**
*  infosEtab() : 
*@type : function
*@return : array()  (infos etablissement)
*/
function infosEtab() {
	$etb=array();
	$result=mysql_query("SELECT * from params where srv_id=0");
	if ($result) {
		while ($r=mysql_fetch_array($result))
		{
			$$r["name"]=$r["value"];
		}
		$etb['tyetab']=utf8_encode($organizationalunit);
		$etb['etab']=utf8_encode($organization);
		$etb['ville']=utf8_encode($locality);
		$etb['acad']=utf8_encode($province);
	}
	else
		$etb['$error'] = "Les param&#232;tres &#233;tablissement semblent absents de la base de donn&#233;e";
	
	return $etb;
}

/**
*  menuApplis() : 
*@type : function
*@param : $login - string : login de l'utilisateur)
*@param : $idpers - integer : id de l'utilisateur)
*@param : $stgo  - string : url des stats)
*@return : array()  (liste des applis)
*/
function menuApplis($login, $idpers, $_ssmn) {
	
	// on passe par les stats
	$stgo="../lcs/statandgo.php?use=";
	
	// tableau du type d'applis (buro, service, application, ressources aide)
	$_srvc=array("pma", "clientftp","elfinder","maintenance","maintinfo", "squirrelmail", "roundcube");
	
	// Tableau des noms affiches dans le menu, certains etant beaucoup trop longs ou techniques.
	// Voir pour une modif en amont ?
	$_ttl = array("pma"=>"Gestion base de donn&#233;es", "spip"=>"Forum LCS", "elfinder"=>"Explorateur de fichiers", "clientftp"=>"Explorateur de fichiers", "smbwebclient"=>"Client SE3", "squirrelmail"=>"Webmail", "roundcube"=>"Webmail");
	
	// Creation du tableau du menu deroulant des applis et ajout de l'item Preferences
	$_applis= array(
		// l'annuaire n'est pas dans la bdd, on l'ajoute a la mano
		'annu' => array(
			"txt" => "Annuaire des utilisateurs",
			"url" => $stgo."Annu",
			"rev" => "annu",
			"img" => "core/images/app/lcslogo-annu.png",
			"typ" => "srvc",
			"smn" =>$_ssmn['annu']
		)
	);

	// lien de connexion
	// peut-être a deplacer 
	if ( $idpers==0 ) {
		$_applis['auth'] = array(
			"txt" => "Se connecter",
			"url" => "../lcs/auth.php",
			"rev" => "auth",
			"img" => "core/images/icons/icon_22_connect.png",
			"typ" => "buro"
		);
	}else{
		$_applis['auth'] = array(
			"txt" => "Se d&eacute;connecter",
			"url" => "../lcs/logout.php",
			"rev" => "auth",
			"img" => "core/images/icons/icon_22_stop.png",
			"typ" => "buro"
		);
	};
	
	// on va chercher les aplis dans la bdd
	$query = "SELECT * from applis where type='N' or type='M' or type='P' or name ='squirrelmail' or name ='roundcube' and name !='desktop' order by name";
	$result=mysql_query($query);
	if ($result) {
		while ($r=mysql_fetch_object($result)) {
			// on verifie que l'appli est bien validee -> is't correct, misterFi ?
			if ( ( $r->value == "1" ) and !file_exists("/usr/share/lcs/Plugins/".$r->chemin."/.applihide") ) {

				// on fixe le chemin des images
				$imgdktp= "images/app/lcslogo-".strtolower($r->name).".png";
				$imgplgn= "../Plugins/".strtolower($r->name)."/Images/plugin_icon.png";
				$imgdflt= "images/app/lcslogo-default.png";
				// et on affche, dans l'ordre :
				// - l'icone made in desktop si elle existe
				if( is_file("/usr/share/lcs/desktop/core/".$imgdktp) ) $imgicon = "core/".$imgdktp;
				// - l'icone de l'appli si elle existe
				else if( is_file("../../".$imgplgn) ) $imgicon = $imgplgn;
				// - l'icone par defaut made in desktop
				else $imgicon = "core/".$imgdflt;
				
				$app_name = $r->name;
				// on enregistre les applis du jour pour les nouvelles applis ou les applis invalidees
				$applisDuJour[] = $app_name ;
				// ya t'il un sous-menu
				$smn = isset($_ssmn[strtolower($r->name)]) ? $_ssmn[strtolower($r->name)] : '';
					
				// squirrelmail ou roundcube 
				if ( (strtolower($app_name) == "squirrelmail") || (strtolower($app_name) == "roundcube") ) {
					
					// nom et texte des sous menus identiques dans les deux cas
					$app_name = "webmail";
					$smn["view"]["txt"] = "Voir mes messages";
					$smn["view"]["url"] = $stgo.$r->name;
					$smn["compose"]["txt"] = "Ecrire un nouveau message";
					
					// pour l'url ecrire ou ecrire a, on teste 
					$smn["compose"]["url"] = strtolower($r->name) == "squirrelmail" ? "../squirrelmail/src/compose.php?mailbox=INBOX&startMessage=1":"../roundcube/?_task=mail&_action=compose";
					$smn["compose"]["to"] =  strtolower($r->name) == "squirrelmail" ? "../squirrelmail/src/compose.php?send_to=" : $smn["compose"]["url"]."&_to=";
					
					// fichier appele pour les notifs de messages
					$notifurl= strtolower($r->name) == "squirrelmail"?"../squirrelmail/plugins/notify/notify-desktop.php":"../roundcube/plugins/lcs-notify-desktop/index.php";
				}

				// on inhibe le coup des deux noms de l'appli maintenance
				//@TODO: A TESTER
				if ( strtolower($r->name) == "maintenance" || strtolower($r->name) == "maintinfo") {
					$app_name = "maintenance";
					$smn = $_ssmn["maintenance"];
				}

				// les autres applis
				$_applis[$app_name] = array(
					'txt' => isset($_ttl[strtolower($r->name)])?$_ttl[strtolower($r->name)]: utf8_encode($r->descr),
					'url'  => $stgo.$r->name,
					'rev'  => strtolower($r->name),
					'img'  => $imgicon,
					"typ" => in_array(strtolower($r->name), $_srvc) ? "srvc": "appl",
					"smn" => $smn
				);
				if ( (strtolower($app_name) == "webmail") ) 
					$_applis[$app_name]["notifurl"]=$notifurl;
			}
		}
		$_mess=array("ok"=>"Les applis sont bien en bdd (".mysql_num_rows($result).")");
	}
	else
		$_mess=array("error"=>"Pas d'applis dispo en bdd");
	
	return $_applis;
};

/**
*  urlAccueil() : 
*@type : function
*@return : string  url_accueil
*/
function urlAccueil() {
	$result=mysql_query("SELECT `value` FROM `applis` WHERE name='url_accueil'");
	if ($result) 
		$r=mysql_result($result, 0);
	else 
		$r='';
	
	return $r;
}

/**
*  infosUser() : 
*@type : function
*@param : $login (login user)
*@param : $idpers (idpers user)
*@return : Object  (infos utilisateur)
*/
function infosUser($login, $idpers, $pwchg) {
	if ( $idpers!=0 ) { 
		
		
		// les infos ldap
		list($user, $groups)=people_get_variables($login, true);
		$_usr=$user;
		$_usr["login"] = isset($login) ? $login:'default'; //le login du user
		$_usr["idpers"] = $idpers; // idpers
		$_usr["pwchg"] = $pwchg?"N":"Y" ;  // password modifié ?
		
		//test listes de diffusion
		//@boulet@ la je ne comprends pas tout 
		exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/mailing_list.cf", $AllOutPut, $ReturnValueShareName);
		$listediff = 0;
		if ( count($AllOutPut) >= 1) $listediff = 1;
		// fin test listes de diffusion
		
		// infos de connexion
		// a passer en chaine de langue l
		// revoir aussi le message de changement de mot de passe a deplacer
		// et aussi changer le message d'invite dans lcs-web
		if (dispstats($idpers) < 2) { 
			$_usr['connect'] = "F&#233;licitations, vous venez de vous connecter pour la 1&#232;re fois sur votre espace perso Lcs. Afin de garantir la confidentialit&#233; de vos donn&#233;es, nous vous encourageons, &agrave; changer votre mot de passe <a class=\"open_win ext_link\" href=\"../Annu/mod_pwd.php\" rel=\"annu\" title=\"\">en suivant ce lien... </a>";
		} else {
			$accord == "";
			if ($user["sexe"] == "F") $accord="e";
			$_usr['connect'] = displogin($idpers) . "<br />Vous vous &ecirc;tes connect&eacute;".$accord." " . dispstats($idpers) . " fois &agrave; votre espace perso." ;
		}
		
		// les groupes
		if ( count($groups) ) 
		{
			$dirIcn ="../data/";
			$co=$ma=$eq=$di=$cl=0;
			$tbl_gp = array("Administratifs","Profs","Eleves");
			$ptrn= array('Classe','Cours','Equipe','Matiere');
	
			for ($loop=0; $loop < count ($groups) ; $loop++) {
				$gex = explode('_', $groups[$loop]["cn"]);
				$g = preg_replace('/ /','',$gex[0]);
				// si on est admin
				if ($groups[$loop]["cn"] == "admins") 
				{
					// on passe Admin en "faux" groupe principal
					$_usr['grps']['gp']= 'Admins';
					// et le reste dans Autres
					$_usr['grps']['Autres'][$loop] = $groups[$loop]["cn"];
				}
				// sinon on recherche le groupe principal
				else if (in_array($groups[$loop]["cn"], $tbl_gp))
				{ 
					$_usr['grps']['gp'] = preg_replace('/ /','',$gex[0]);
				} 
				// on recherche les groupes Classe,Cours,Equipe,Matiere
				else if( in_array($g, $ptrn) )
				//else if( $g == 'Classe' || $g == 'Cours' || $g == 'Equipe' || $g == 'Matiere'   )
				{ 
					$_usr['grps'][$g][] = preg_replace('/$g/',' ', $groups[$loop]["cn"]);
				}
				// et les autres
				else $_usr['grps']['Autres'][] = $groups[$loop]["cn"];
				// Teste si n&#233;cessit&#233; d'affichage menu Ouverture/Fermeture Bdd et espace web perso des Eleves
				// A confirmer .. is't ok, misterfi ?
				if ($groups[$loop]["cn"]=="Eleves") $ToggleAff=1;
		    }
		}
	} 
	else 
	{
		$_usr['idpers']=$idpers;
	}
	return $_usr;
}

/**
*  loadOpts() : 
*@type : function
*@return : Object   opts: prefs user
*/
function loadOpts($login) {
	$opts=array();

	//si un fichier prefs existe, on va le chercher
	if ( is_file('/home/'.$login.'/Profile/PREFS_'.$login.'.json') ) {
		$url='/home/'.$login.'/Profile/PREFS_'.$login.'.json';
		// compat version precedente
		$objsn = json_decode(file_get_contents($url));
		foreach( $objsn as $k=>$val) {
			if ( $k!="icons" ) $opts[$k]=$val ;
		}
	return $opts;
	}
	//sinon on prends le fichier prefs_defaut s'il existe
	else if( is_file('../json/PREFS_default.json') ) {
		$url='../json/PREFS_default.json';
		$optsjsn = json_decode(file_get_contents($url));
		foreach( $optsjsn as $k=>$val) 
		{
			$opts[$k]=$val ;
		}
	return $opts;
	}
	
	else return false;
	
}

/**
*  loadPrms() : 
*@type : function
*@return : Object   opts: prefs user
*/
function loadPrms() {
	$prms=array();

	//si un fichier prefs existe, on va le chercher
	if( is_file('../json/PARAMS_admin.json') ) {
		$urlprms='../json/PARAMS_admin.json';
		$prmsjsn = json_decode(file_get_contents($urlprms));
		foreach( $prmsjsn as $k=>$val) 
		{
			$prms[$k]=$val ;
		}
	}
	
	return $prms;
}

/**
*  loadIcons() : 
*@type : function
*@return : array   icns:liste des icônes )
*/
function loadIcons($login, $apps) {
	$icns=array();
	// le path des fichiers json
	$urlicn='/home/'.$login.'/Profile/ICONS_'.$login.'.json';
	$urlicndef='../json/ICONS_default.json';
	//si un fichier icons existe, on va le chercher
	if ( is_file($urlicn) ) {
		$icns = json_decode(file_get_contents($urlicn));
	}
	//sinon on prends le fichier icons_defaut s'il existe
	else if( is_file($urlicndef) ) {
		$icns = json_decode(file_get_contents($urlicndef));
	}
	// sinon on prends la liste des applis (cas de la première connexion de l'admin)
	else {
		foreach ($apps as $app=>$icon) 
		{
			if( $app!="admin" && $app!="auth" && $app!="apdesk" && $icon['typ']!="buro" && $icon['typ']!="aide") {
				array_push($icns, $icon);
			}
		}
	}
	
	return $icns;
}

/**
*  loadRess() : les "liens partages"
*@type : function
*@param : $gp - groupe principal du user 
*@return : array()  tableau des liens:params
*/
function loadRess($gp) {
	$dirData ="../data/";
	$ress=array();
	if( is_dir($dirData) ) {
		$dIcn = scandir($dirData);
		foreach($dIcn as $k=>$dirGp){
			if($dirGp !="." && $dirGp!=".." && is_dir($dirData.$dirGp)) {
				$files = scandir($dirData.$dirGp);
				foreach($files as $t=>$icn){
					if($icn !="." && $icn!=".." &&$icn !=$icnLast &&  is_file($dirData.$dirGp."/".$icn)){
						$_ficn=$dirData.$dirGp."/".$icn;
						if ($gp == "Admins") 
						$ress[$icn] =  json_decode(file_get_contents($_ficn));
						else	if ($dirGp == $gp) 
						$ress[$icn] =  json_decode(file_get_contents($_ficn));
					
						$icsLast=$icn;
					}
				}
			}
		}
	}
	return $ress;
}

/**
*  menuAdmin() : 
*@type : function
*@param : $login (login user)
*@return : array()  (menu administration)
*/
function menuAdmin($idpers, $login, $liens) {
	if ( acces_btn_admin($idpers, $login) == "Y") { // acces au menu d'administration
		for ($i=0; $i< count($liens); $i++) {
			// Affichage item menu
			if ( (strlen($liens[$i][0]) > 0) && ( ldap_get_right($liens[$i][1],$login)=="Y" ) ) 
				$apps['admin'][$i] = array(
				"txt" => $liens[$i][0],
				"url" => "#",
				"rev" => "admin",
				"img" => "core/images/app/lcslogo-admin.png",
				"typ" => "admn",
				"smn"=>array()
				);
			if ( count($liens[$i]) > 0 );
			  for ($j=2; $j< count($liens[$i]); $j=$j+3) {
				if ( ldap_get_right($liens[$i][$j+2],$login)=="Y" ) {
					// On vire le target quand il existe (cas de pla)
					$tmp = explode ("\"",$liens[$i][$j+1]);
					# if ( $tmp[1] == "target='_new'" ) $liens[$i][$j+1] = $tmp[0];
					if ( preg_match("/target/",$tmp[1]) ) $liens[$i][$j+1] = $tmp[0];
					$apps['admin'][$i]['smn'][]= array(
						"txt" => $liens[$i][$j],
						"url" => "../Admin/".preg_replace("/\/Admin\//","",$liens[$i][$j+1])
					);
		
				}
			  }	
		}

	return $apps;
	} // Fin menu admin
	
	return ;
			
}


/**
* Chargement des parametres
* Mettre un swith pour n'envoyer que ce qu'il faut ???
*/
// infos serveur
$_srvr	= array(
	"domain" 		=> $domain, // domains
	"baseurl" 		=> $baseurl, // url lcs
	"url_accueil" 	=> urlAccueil(), // url page d'accueil definie ds la conf generale serveur 
	"stgo"			=> "../lcs/statandgo.php?use=" // url redirigee par les stats
);

// infos etab
$_etab	=  infosEtab();

// pas d'authentification
// On pourrait ajouter la verif php du pwdMustChange
// pour plus de securite et ne renvoyer que ce qu'il faut
// c'est fait en js mais la fonction est-elle ultra sensible ?
if ( $idpers == "0" ) 
{
	$_user["idpers"]=0;
	// tableau renvoye
	$resp=array(
		"user"	=> $_user,
		"srvr" 	=> $_srvr ,
		"opts" 	=> loadOpts('default') != false ?  array_merge( $_opts, loadOpts( 'default' ) )  : $_opts ,
		"etab" 	=> $_etab 
	);
}
else 
{
	
	//user
	$_user = infosUser($login, $idpers, pwdMustChange($login) );
	//les prefs user 
	$_opts = loadOpts('default') != false ? array_merge( $_opts, loadOpts( $login ) ) : $_opts;
	
	//les options admin 
	$_prms = array_merge( $_prms, loadPrms() );
	
	// les ressources
	$_ress = array_merge( $_ress, loadRess( isset( $_user["grps"]["gp"] ) ? $_user["grps"]["gp"] : "admin") );

	// les applis ( du menu deroulant )
	$_apps = menuApplis($login, $idpers, $_ssmn);
	// on place le lien pour l'appel maintenance
	if ( isset( $_apps["maintenance"]) &&  $_prms["maintUrl"] == "" ) $_prms["maintUrl"] = $_apps["maintenance"]["smn"]["call"]["url"];
	// else $_prms["maintUrl"] = $_apps["webmail"]["smn"]["compose"]["to"]. $_prms["maintUrl"]=="" ? "admin@".$_srvr["domain"] :  $_prms["maintUrl"];
	else $_prms["maintUrl"] = $_apps["webmail"]["smn"]["compose"]["to"]."admin@".$_srvr["domain"] ;
	
	// les icones
	$_icns = loadIcons( $login, $_apps );
	// compatibilite version anterieure.
	// on regarde si les icônes sont enregistres dans PREFS_...json
	if ( !is_file('/home/'.$login.'/Profile/ICONS_'.$login.'.json') && is_file('/home/'.$login.'/Profile/PREFS_'.$login.'.json')  )
	{
		$oldurl='/home/'.$login.'/Profile/PREFS_'.$login.'.json';
		$oldjsn = json_decode(file_get_contents($oldurl));
		foreach( $oldjsn as $k=>$val) {
			if ( $k=="icons" ) $_icns = $val;
		}
	}

	// admin
	if ( ldap_get_right("lcs_is_admin",$login)=="Y")  {
		//hummm ??? verifier à quoi ça sert
		$_user['statut']='admin';
		// on renomme le lien "Ajouter une icone"
		$_apps['addicon']['txt']= "Ajouter un lien partag&eacute;";
		// le menu admin
		getmenuarray();
		$_admn	= menuAdmin($idpers, $login, $liens);
		// qu'on merge avec le menu applis
		$_apps = array_merge($_apps , $_admn);
	}
	
	// tableau renvoye
	$resp=array(
		"user"	=> $_user,
		"srvr" 	=> $_srvr ,
		"etab" 	=> $_etab ,
		"opts" 	=> $_opts ,
		"prms" 	=> $_prms ,
		"apps" 	=> $_apps ,
		"icns" 	=> $_icns ,
		"ress" 	=> $_ress,
		'test'=>count($_icns[0]),
		'test2'=>!is_array($_icns),
	);
}
// et hop!
echo json_encode( $resp );
exit();


?>