<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* @save.php
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version 2.4.8
* Derniere mise a jour: 06/03/2011
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/

header ('Content-type: text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
include("../includes/functions.inc.php");
#include("../includes/config_jqd.inc.php");
list ($idpers, $login)= isauth();


#__/__/__/__/__/__/__/__/__/__/
# Test de création json
#__/__/__/__/__/__/__/__/__/__/
$i=0;
$_j= array();
$_dflt= array();
$lg='';
# cas d'un partage
if( $_POST["ou"] == "group") {
    $c= count($_POST['icons']);
    foreach($_POST['icons'] as $k=>$val){
        $_j[$k] = $val;
    }
    $_j['owner']=$login;
    $_j['groups']=$_POST['groups'];
    /**/
    foreach($_POST['groups'] as $k=>$group){
        #$_t= htmlentities(trim($_j['txt']));
        $_t= htmlentities( preg_replace( "# #", "_", replace_accents($_j['txt'])  ) );
        if($login==$group) $resDir = "/home/".$login."/Documents/Ressources" ;
        else if( acces_btn_admin($idpers, $login) == "Y" )  $resDir =  "../data/".$group;
        else return;
        if(!is_dir($resDir)){mkdir($resDir, 0770);}
        $fp=fopen($resDir."/ICON_".$login.'_'.stripAccents($_t).".json","w");
        $json_fp=json_encode($_j);
        $fwrite = fwrite($fp,$json_fp);
        fclose($fp);
        $lg.=$group;
    }
    
   // echo json_encode($_j);
        exit ( json_encode( array('default'=> 'Enregistrement effectué ! ','filename'=>'ICON_'.$login.'_'.stripAccents($_t).'.json') ) );
}
else {
    $_j["wallpaper"]     		= htmlentities($_POST["wallpaper"]);
    $_j["pos_wallpaper"] 	= $_POST["pos_wallpaper"];
    $_j["iconsize"]      		= $_POST["iconsize"];
    $_j["iconsfield"]    		= $_POST["iconsfield"];
    $_j["iconcolor"]    		= $_POST["iconcolor"];
    $_j["bgcolor"]       		= $_POST["bgcolor"];
    $_j["bgopct"]        		= $_POST["bgopct"];
    $_j["quicklaunch"]   	= $_POST["quicklaunch"];
    $_j["s_idart"]       		= $_POST["s_idart"];
    $_j["winsize"]       		= $_POST["winsize"];
    $_j["win_h"]       		= $_POST["win_w"];
    $_j["win_w"]       		= $_POST["win_h"];
    $_j["data"]          		= $_POST["data"];
	$_j["s_idart"]      		= $_POST["s_idart"];

	$_icons          			= $_POST['icons'];
	/*
   foreach($_POST['icons'] as $k=>$val){
        $_j['icons'][$k] = $val;
    }
    */

    if( intval($_POST["defaultConf"]) != 1 ) {
    	$serial_j= serialize($_j);

        $fp=fopen("/home/".$login."/Profile/PREFS_".$login.".json","w");
        $json_fp=json_encode($_j);
        fwrite($fp,$json_fp);
        fclose($fp);
	    $_fpIcons=fopen("/home/".$login."/Profile/ICONS_".$login.".json","w");
	    $_json_fpIcons=json_encode($_icons);
	    fwrite($_fpIcons,$_json_fpIcons);
	    fclose($_fpIcons);

    	// Preparation a l'utilisation d'une bdd
    	// n'est pas en fonction dans cette version
    	// a modifier en 2.4.9
    	/*
			$rq2 = "INSERT INTO meta (id_user, name,value ) 
			VALUES ('$login', 'prefs','$serial_j') 
			ON DUPLICATE KEY UPDATE value='$serial_j'";
			// lancer la requęte
			$result2 = mysql_query($rq2); 
			if (!$result2)  // Si l'enregistrement est incorrect
			{                           
				$resp['error']= 1;
				$resp['mess']="Votre ressource n'a pas pu ętre enregistré &#333; cause d'une erreur syst&#323;me". mysql_error();
				mysql_close();     // refermer la connexion avec la base de données
				exit( json_encode($resp) );
			}
			else
			{
				$sql2 = mysql_insert_id();
			}
		*/
        exit ( json_encode( array('default'=> 'prefs_user : Enregistrement effectué', 'sql'=>$sql, 'sql2'=>$sql2) ) );
    }

	#__/__/__/__/__/__/__/__/__/__/
	# Preferences par defaut
	#__/__/__/__/__/__/__/__/__/__/
	if( intval($_POST["defaultConf"]) == 1 ) {
	    $_dflt["defaulticons"]      = $_POST["defaulticons"];
	    $_dflt["maintUrl"]      = $_POST["maintUrl"];
	    $_dflt["showGroups"]      = $_POST["showGroups"];
	    $_dflt["notifForumFreq"]      = $_POST["notifForumFreq"];
		$serial= serialize($_dflt);
		
		// on enregistre les prefs par defaut dans PREFS_default.json
	    $_fp=fopen("../json/PREFS_default.json","w");
	    $_json_fp=json_encode($_j);
	    fwrite($_fp,$_json_fp);
	    fclose($_fp);
		// on enregistre les icones par defaut dans ICONS_default.json
	    $_fpIcons=fopen("../json/ICONS_default.json","w");
	    $_json_fpIcons=json_encode($_icons);
	    fwrite($_fpIcons,$_json_fpIcons);
	    fclose($_fpIcons);
		// on enregistre les params admin dans ICONS_default.json
	    $_fpParams=fopen("../json/PARAMS_admin.json","w");
	    $_json_fpParams=json_encode($_dflt);
	    fwrite($_fpParams,$_json_fpParams);
	    fclose($_fpParams);

    	// Preparation a l'utilisation d'une bdd
    	// n'est pas en fonction dans cette version
    	// a modifier en 2.4.9
    	/*
				$rq = "INSERT INTO meta (id_user, name,value ) 
				VALUES ('desktop', 'default','$serial') 
				ON DUPLICATE KEY UPDATE value='$serial'";
				// lancer la requęte
				$result = mysql_query($rq); 
				if (!$result)  // Si l'enregistrement est incorrect
				{                           
					$resp['error']= 1;
					$resp['mess']="Votre ressource n'a pas pu ętre enregistré &#333; cause d'une erreur syst&#323;me". mysql_error();
					mysql_close();     // refermer la connexion avec la base de données
					exit( json_encode($resp) );
				}
				else
				{
					$sql = mysql_insert_id();
				}
		*/
	    exit ( json_encode( array('default'=> 'Préférences par défaiut : enregistrement effectué', 'sql'=>$sql) ) );
	} 
}
//
function replace_accents($string)
{
  return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
} 
?> 