<?php
#header ('Content-type: text/html; charset=utf-8');
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();


#__/__/__/__/__/__/__/__/__/__/
# Test de création json
#__/__/__/__/__/__/__/__/__/__/
$i=0;
$_j= array();

# cas d'un partage
if( $_POST["ou"] == "group" ) {
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
        if(!is_dir("../data/".$group)){$mkdir = mkdir("../data/".$group, 0770);}
        $fp=fopen("../data/".$group."/ICON_".$login.'_'.$_t.".json","w");
        $json_fp=json_encode($_j);
        $fwrite = fwrite($fp,$json_fp);
        fclose($fp);
    }
    
    echo json_encode($_j);
}
else{

    $_j["wallpaper"]     = htmlentities($_POST["wallpaper"]);
    $_j["pos_wallpaper"] = $_POST["pos_wallpaper"];
    $_j["iconsize"]      = $_POST["iconsize"];
    $_j["iconsfield"]    = $_POST["iconsfield"];
    $_j["bgcolor"]       = $_POST["bgcolor"];
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