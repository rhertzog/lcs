<?php
/* lcs/includes/jlcipher.inc.php derniere modification : 20/11/2009 */

# Constantes
$MaxLifeTime = "20"; /* seconde */

# Messages d'erreur
$MsgError[1]="Possible spoof IP source address";
$MsgError[2]="MaxTimeLife expire";
$MsgError[3]=$MsgError[1]." and ".$MsgError[2];
$MsgError[4]="Authentification error";

function header_crypto_html( $titre)
{
        ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
        <head>
                <title><?php echo $titre ?></title>
                <link  href='../c/lcs.css' rel='StyleSheet' type='text/css'/>
		<meta http-equiv="Pragma" content="No-Cache"/>
                <script language = 'javascript' type = 'text/javascript' src="../lcs/crypto.js"></script>
                <script language = 'javascript' type = 'text/javascript' src="../lcs/public_key.js"></script>
                <script language = 'javascript' type = 'text/javascript'>
                        <!--
                        function auth_popup() {
                                window.focus();
                                auth_popupWin = window.open("../lcs/auth_lcs.html","auth_lcs","width=600,height=400,resizable=no,scrollbars=no,toolbar=no,menubar=no,status=no");
                                auth_popupWin.focus();
                        }
                <?php if ( preg_match("/Authentification/i", $titre)) { ?>
                        function encrypt(f) {
                                var endTime=new Date();
                                f.time.value =  (endTime.getTime()-startTime.getTime())/1000.0;
                                encode = f.dummy.value+"|"+f.client_ip.value+"|"+f.timestamp.value+"|"+f.time.value;
                                f.string_auth.value=rsaEncode(public_key_e,public_key_pq,encode);
                                f.dummy.value="******";
                                f.timestamp.value="";
                                f.time.value="";
				return true;
                        }

                        function timerStart() {
                                startTime=new Date();
                        }

                        timerStart();
                <?php } else { ?>

                        function encrypt(f) {
                                if (f.dummy.value!="") {
                                        f.string_auth.value=rsaEncode(public_key_e,public_key_pq,f.dummy.value);
                                        f.dummy.value="******";
                                }
                                if (f.dummy1.value!="") {
                                        f.string_auth1.value=rsaEncode(public_key_e,public_key_pq,f.dummy1.value);
                                        f.dummy1.value="******";
                                }
                                if (f.dummy2.value!="") {
                                        f.string_auth2.value=rsaEncode(public_key_e,public_key_pq,f.dummy2.value);
                                        f.dummy2.value="******";
                                }
				return true;
                        }

                <?php } ?>

                        // -->
                </script>
        </head>
        <body class="auth">
<?php
}

function crypto_nav()
{
         global   $HTTP_USER_AGENT;
        // Affichage logo crypto
        if (preg_match("/Mozilla\/4.7/i", $HTTP_USER_AGENT)) {
                echo " <a href='../lcs/auth_lcs.html' onclick='auth_popup(); return false' target='_blank'><img src='../lcs/images/no_crypto.gif' alt='Crytpage des mots de passe actif !' width='48' height='48' border='0' /></a>";
        } else {
                echo " <a href='../lcs/auth_lcs.html' onclick='auth_popup(); return false' target='_blank'><img src='../lcs/images/crypto.gif' alt='Attention, avec ce navigateur votre mot de passe va circuler en clair sur le r&#233;seau !' width='48' height='48' border='0' /></a>";
        }
}

function detect_key_orig()
{
        $myFile = file( "public_key.js");
        if ( preg_match("/103891665,103273136,154427652,173536111,49193/i",$myFile[1])) return true;  else return false; 
}

?>
