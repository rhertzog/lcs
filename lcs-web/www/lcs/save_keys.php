<?php   /* lcs/save_keys.php maj : 20/11/2009 */

require "./includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

// Register globals
$keys=$_POST['keys'];

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<HTML>\n";  
?>
<head>
<title>...:::  Sauvegarde du nouveau jeu de cl&#233;s d'authentification :::...</title>
<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>
</head>
<body>
<div align="center">
        <h2>Sauvegarde du nouveau jeu de cl&#233;s d'authentification</h2>
</div>
<?php
if ( is_admin("Lcs_is_admin",$login) == "Y" )  {
        // Decodage de la chaine d'authentification cote serveur avec une cle privee
        $tmp = preg_split ("/[\|]/",decodekey($keys),5);
        $p = $tmp[0];
        $q = $tmp[1];
        $pq = $tmp[2];
        $d = $tmp[3];
        $e = $tmp[4];

        if ( $p && $q && $pq && $d && $e ) {
                // sauvegarde de la cle publique
                $public_key="var public_key_e=[".$e."];\n";
                $public_key.="var public_key_pq=[".$pq."];\n";
                $fp=@fopen("public_key.js","w");
                if($fp) {
                        fputs($fp,$public_key."\n");
                        fclose($fp);
                        // sauvegarde de la cle privee
                        $private_key="#[ [d], [p], [q] ]\n";
                        $private_key.="value=[[$d],[$p],[$q]]\n";
                        $fp=@fopen("/usr/share/lcs/privatekey/privateKey.py","w");
                        if($fp) {
                                fputs($fp,$private_key."\n");
                                fclose($fp);
                                $msg = "<div align='center'>\n";
                                $msg .= "<p>".gettext("Votre nouvelle paire de cl&#233;s a &#233;t&#233; sauvegard&#233;e avec succ&#232;s.")."</p>\n";
                                $msg .= "<img src='images/warning.png' alt='Attention !' title='Attention !' />\n";
                                $msg .= "<p>".gettext("Lors de la prochaine authentification, il faudra que vous vidiez le cache de votre navigateur.")."</p>\n";
                                $msg .= "</div>\n";
                                echo $msg;

                        } else {
                                echo "<div align='center'><b>ERREUR</b> : ".gettext("Impossible de sauvegarder la nouvelle cl&#233; priv&#233;e.")."</div>\n";
                        }
                } else {
                        echo "<div align='center'><b>ERREUR</b> : ".gettext("Impossible de sauvegarder la nouvelle cl&#233; publique.")."</div>\n";
                }
        } else {
                echo "<div align='center'><b>ERREUR</b> : ".gettext("Impossible de sauvegarder cette paire de cl&#233;s.")."</div>\n";
        }
} else {
        echo "<div class=alert_msg>".gettext("Cette fonctionnalit&#233;, n&#233;cessite les droits d'administrateur du serveur LCS !")."</div>\n";
}
include ("./includes/pieds_de_page.inc.php");
?>
