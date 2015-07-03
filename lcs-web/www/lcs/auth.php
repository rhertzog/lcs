<?php
/*===========================================
   Projet LcSE3
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 19/03/2015
   ============================================= */
include ("./includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("./includes/jlcipher.inc.php");
session_name("Lcs");
@session_start();
// On n'autorise pas la reauthentification si deja authentifie
if ( isset($_SESSION['login']) && isset($_COOKIE['LCSuser'])) {
   header("Location:index.php");
    exit;
}
//on detruit les variables de session en cas de fermeture/ouverture du navigateur
$_SESSION = array();
// On detruit la session sur le serveur.
session_destroy();
//init variables
$error=$login=0;
// Purifier
if (count($_POST)>0 || count($_GET)>0 ) {
    //configuration objet
    include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    //purification des variables
    if (isset($_POST['login'])) $login=$purifier->purify(trim($_POST['login'])); else $login="" ;
    if (isset($_POST['dummy'])) $dummy=$purifier->purify($_POST['dummy']); else $dummy="" ;
    if (isset($_POST['string_auth'])) $string_auth=$purifier->purify($_POST['string_auth']); else $string_auth="" ;
    if (isset($_POST['time'])) $time=$purifier->purify($_POST['time']); else $time="" ;
    if (isset($_POST['client_ip'])) $client_ip=$purifier->purify($_POST['client_ip']); else $client_ip="" ;
    if (isset($_POST['timestamp'])) $timestamp=$purifier->purify($_POST['timestamp']); else $timestamp="" ;
    if (isset($_GET['error'])) $error=$purifier->purify($_GET['error']);
}


if ($login) {
    // Decodage de la chaine d'authentification cote serveur avec une cle privee extraction des parametres
    $tmp = preg_split ("/[\|]/",decodekey($string_auth),4);
    $pass = $tmp[0];
    $ip_src = $tmp[1];
    $timestamp = $tmp[2];
    $timewait = $tmp[3];
    $timetotal= $timewait+$timestamp+$MaxLifeTime;
    // Verification de la validite de la source IP et du du TimeStamp
    if ( $ip_src != remote_ip() && time() < $timetotal ) {
         $error = 1;
    } elseif ( time() > $timetotal && $ip_src == remote_ip() ) {
         $error = 2;
    }  elseif ( $ip_src != remote_ip() && time() > $timetotal ) {
         $error = 3;
    } elseif ( !open_session( mb_strtolower($login), $pass, $string_auth) ) {
         $error = 4;
    }
    // Interpretation erreurs
    if ($error)   {
         // Log en cas d'echec
         $fp=fopen($logpath."/error.log","a");
         if($fp) {
                  fputs($fp,"[".date("D M d H:i:s Y")."] [".$MsgError[$error]."] [client ".$ip_src."] [remote ip : ".remote_ip()."] [Login : ".$login."] [TimeStamp srv : ".time()."] [TimeTotal : ".$timetotal."] \n");
                  fclose($fp);
         }
         // Redirection vers la page d'authentification
         header("Location:auth.php?error=$error");
         exit;
    } else {
        // Log en cas de succes
        $fp=fopen($logpath."/acces.log","a");
        if($fp) {
            fputs($fp,date("M j H:i:s ")." Login succes for $login from ".remote_ip()."\n");
            fclose($fp);
        }
        //enregistrement dans la table statusages
        #
        # Detection de l'origine de la requete
        #
        list ($ip_client_prefix) = explode (".", remote_ip());
        list ($ip_serv_prefix) = explode (".",getenv("SERVER_ADDR"));
        if ( $ip_client_prefix == $ip_serv_prefix) $source="lan"; else $source="wan";
        #
        $date=date("YmdHis");
        $authlink=($GLOBALS["___mysqli_ston"] = mysqli_connect("$HOSTAUTH",  "$USERAUTH",  "$PASSAUTH"));
        if (!@((bool)mysqli_query( $authlink, "USE " . $DBAUTH))) die ("S&#233;lection de base de donn&#233;es impossible.");
        $query="INSERT INTO statusages VALUES ('Nogroup', 'auth_ok', '$date', '$source','$login')";
        $result=@mysqli_query($authlink, $query);
        // Run post_auth hook
        lcs_web_run_hook('post_auth',array(mb_strtolower($login), $pass));

        if ( file_exists ("/usr/share/lcs/spip/spip_session_lcs.php") ) {
            // Ouverture d'une session spip
            header("Location:../spip/spip_session_lcs.php?action=login");
        } else {
            echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
            echo "<!--\n";
            echo "top.location.href = '../lcs/index.php?url_redirect=accueil.php';\n";
            echo "//-->\n";
            echo "</script>\n";
        }
    }
}
header_crypto_html("...::: Authentification LCS :::...");
?>
<h3>Authentification</h3>
        <p>Afin de pouvoir rentrer dans votre espace perso LCS, vous devez fournir un identifiant et un mot de passe. En cas d'oubli, contactez <a href='mailto:<? echo "admin@$domain" ?>?subject=Mot de passe Intranet'>l'administrateur du syst&egrave;me</a>.</p>
        <form name = "auth" action="auth.php" method="post" onsubmit = "encrypt(document.auth)" >
                <table border='0'>
                        <tr>
                                <td>Identifiant :&nbsp;</td>
                                <td><input type="text" name="login" size="20" maxlength="30" autocomplete="off" /><br /></td>
                        </tr>
                        <tr>
                                <td>Mot de passe :&nbsp;</td>
                                <td>
                                        <input type= "password" value="" name="dummy" size="20"  maxlength="30" autocomplete="off"/>
                                        <input type="hidden" name="string_auth" value=""/>
                                        <input type="hidden" name="time" value=""/>
                                        <input type="hidden" name="client_ip" value="<? echo remote_ip(); ?>"/>
                                        <input type="hidden" name="timestamp" value="<? echo time(); ?>"/>
                                </td>
                        </tr>
                        <tr align="left">
                                <td>&nbsp;</td>
                                <td><input type="submit" value="Valider"/><br /></td>
                        </tr>
                </table>
        </form>
<?
// Affichage logo crypto
crypto_nav();
// Affichage des erreurs
switch ($error) {
         case "1" :
                echo "<div class='alert_msg'>L'adresse source de votre authentification a chang&#233;, veuillez vous r&#233;authentifier !</div>";
                break;
        case "2" :
                echo "<div class='alert_msg'>Votre d&#233;lai d'authentification est d&#233;pass&#233;, veuillez vous r&#233;authentifier !</div>";
                break;
        case "3" :
                echo "<div class='alert_msg'>Votre d&#233;lai d'authentification est d&#233;pass&#233; et votre adresse source a chang&#233;, veuillez vous r&#233;authentifier !</div>";
                break;
        case "4" :
                echo "<div class='alert_msg'>Erreur d'authentification !</div> ";
                break;
        default :
}
include ("./includes/pieds_de_page.inc.php");
?>
