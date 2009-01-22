<?
/* lcs/auth.php version du : 17/10/2008 */
include ("./includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("./includes/jlcipher.inc.php");

// register global
if (isset($_POST['login'])) $dummy=$_POST['login']; else $dummy="" ;
if (isset($_POST['dummy'])) $dummy=$_POST['dummy']; else $dummy="" ;
if (isset($_POST['string_auth'])) $string_auth=$_POST['string_auth']; else $string_auth="" ;
if (isset($_POST['time'])) $time=$_POST['time']; else $time="" ;
if (isset($_POST['client_ip'])) $client_ip=$_POST['client_ip']; else $client_ip="" ;
if (isset($_POST['timestamp'])) $timestamp=$_POST['timestamp']; else $timestamp="" ;

        if ($login) {
                // Decodage de la chaine d'authentification cote serveur avec une cle privee
                exec ("/usr/bin/python ".$basedir."lcs/includes/decode.py '$string_auth'",$AllOutPut,$ReturnValue);
                // Extraction des parametres
                $tmp = split ("[\|]",$AllOutPut[0],4);
                $pass = $tmp[0];
                $ip_src = $tmp[1];
                $timestamp = $tmp[2];
                $timewait = $tmp[3];
	        $timetotal= $timewait+$timestamp+$MaxLifeTime;
                // Verification de la validite de la source IP  et du du TimeStamp
                if ( $ip_src != remote_ip() && time() <  $timetotal ) {
                        $error = 1;
                } elseif   ( time() >  $timetotal && $ip_src == remote_ip() ) {
                        $error = 2;
                }  elseif ( $ip_src != remote_ip()   &&   time() >  $timetotal ) {
                        $error = 3;
                } elseif ( !open_session( strtolower($login), $pass, $string_auth) ) {
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
                } else {
                      // log en cas de succes
                        // Log en cas de succes
                        $fp=fopen($logpath."/acces.log","a");
                        if($fp) {
                                fputs($fp,date("M j H:i:s ")." Login succes for $login from ".remote_ip()."\n");
                                fclose($fp);
                        }
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
                <p>Afin de pouvoir rentrer dans votre espace perso LCS, vous devez fournir un identifiant et un mot de passe. En cas d'oubli, contactez <a href='mailto:<? echo $MelAdminLCS ?>?subject=Mot de passe Intranet'>l'administrateur du système</a>.</p>
                <form name = "auth" action="auth.php" method="post" onsubmit = "encrypt(document.auth)" >
                        <table border='0'>
                                <tr>
                                        <td>Identifiant :&nbsp;</td>
                                        <td><input type="text" name="login" size="20" maxlength="30"/><br /></td>
                                </tr>
                                <tr>
                                        <td>Mot de passe :&nbsp;</td>
                                        <td>
                                                <input type= "password" value="" name="dummy" size="20"  maxlength="30"/>
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
                echo "<div class='alert_msg'>L'adresse source de votre authentification a chang&#233;e, veuillez vous r&#233;authentifier !</div>";
                break;
        case "2" :
                echo "<div class='alert_msg'>Votre d&#233;lais d'authentification est d&#233;pass&#233;, veuillez vous r&#233;authentifier !</div>";
                break;
        case "3" :
                echo "<div class='alert_msg'>Votre d&#233;lais d'authentification est d&#233;pass&#233; et votre adresse source a chang&#233;e, veuillez vous r&#233;authentifier !</div>";
                break;
        case "4" :
                echo "<div class='alert_msg'>Erreur d'authentification !</div> ";
                break;
        default :
}
include ("./includes/pieds_de_page.inc.php");
?>
