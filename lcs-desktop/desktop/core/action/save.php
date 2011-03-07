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
list ($idpers, $login)= isauth();


#__/__/__/__/__/__/__/__/__/__/
# Test de création json
#__/__/__/__/__/__/__/__/__/__/
$i=0;
$_j= array();
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
        $_t= htmlentities( preg_replace( "# #", "_", $_j['txt']  ) );
        if($login==$group) $resDir = "/home/".$login."/Documents/Ressources" ;
        else if( acces_btn_admin($idpers, $login) == "Y" )  $resDir =  "../data/".$group;
        else return;
        if(!is_dir($resDir)){mkdir($resDir, 0770);}
        $fp=fopen($resDir."/ICON_".$login.'_'.$_t.".json","w");
        $json_fp=json_encode($_j);
        $fwrite = fwrite($fp,$json_fp);
        fclose($fp);
        $lg.=$group;
    }
    
   // echo json_encode($_j);
        echo json_encode( array('default'=> 'Enregistrement effectué ! '.$_POST['myress'].' | '.count($_POST['groups']).' | '.$lg.' | '.$login.' | '.$resDir.' | '.acces_btn_admin($idpers, $login)) );
}
else {

    $_j["wallpaper"]     = htmlentities($_POST["wallpaper"]);
    $_j["pos_wallpaper"] = $_POST["pos_wallpaper"];
    $_j["iconsize"]      = $_POST["iconsize"];
    $_j["iconsfield"]    = $_POST["iconsfield"];
    $_j["bgcolor"]       = $_POST["bgcolor"];
    $_j["bgopct"]        = $_POST["bgopct"];
    $_j["quicklaunch"]   = $_POST["quicklaunch"];
    $_j["s_idart"]       = $_POST["s_idart"];
    $_j["winsize"]       = $_POST["winsize"];
    $_j["data"]          = $_POST["data"];

    foreach($_POST['icons'] as $k=>$val){
        $_j['icons'][$k] = $val;
    }

    if( $_POST["ou"] != '1' ) {
        $fp=fopen("/home/".$login."/Profile/PREFS_".$login.".json","w");
        $json_fp=json_encode($_j);
        fwrite($fp,$json_fp);
        fclose($fp);

        echo json_encode( array('default'=> 'prefs_user : Enregistrement effectué') );
    }

}

#__/__/__/__/__/__/__/__/__/__/
# Preferences par defaut
#__/__/__/__/__/__/__/__/__/__/
if( $_POST["ou"] == '1' ) {
    $_fp=fopen("../json/PREFS_default.json","w");
    $_json_fp=json_encode($_j);
    fwrite($_fp,$_json_fp);
    fclose($_fp);
    #echo $_json_fp;
    echo json_encode( array('default'=> 'Préférences par défaiut : enregistrement effectué') );
} 
//

?> 