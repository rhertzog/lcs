<?php   /* lcs/save_keys.php maj : 28/11/2003 */
require "./includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");
?>

<head>
<title>...:::  Sauvegarde du nouveau jeu de clés d'authentification :::...</title>
<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>
</head>
<body>
<div align="center">
        <h2>Sauvegarde du nouveau jeu de clés d'authentification</h2>
</div>
<?php
if ( is_admin("Lcs_is_admin",$login) == "Y" )  {
        // Décodage de la chaine d'authentification coté serveur avec une clé privée
        exec ("/usr/bin/python ".$basedir."lcs/includes/decode.py '$keys'",$AllOutPut,$ReturnValue);
        $tmp = split ("[\|\]",$AllOutPut[0],5);
        $p = $tmp[0];
        $q = $tmp[1];
        $pq = $tmp[2];
        $d = $tmp[3];
        $e = $tmp[4];

        if ( $p && $q && $pq && $d && $e ) {
                // sauvegarde de la clé publique
                $public_key="var public_key_e=[".$e."];\n";
                $public_key.="var public_key_pq=[".$pq."];\n";
                $fp=@fopen("public_key.js","w");
                if($fp) {
                        fputs($fp,$public_key."\n");
                        fclose($fp);
                        // sauvegarde de la clé privée
                        $private_key="#[ [d], [p], [q] ]\n";
                        $private_key.="value=[[$d],[$p],[$q]]\n";
                        $fp=@fopen("includes/privateKey.py","w");
                        if($fp) {
                                fputs($fp,$private_key."\n");
                                fclose($fp);
                                echo "<div align='center'>".gettext("Votre nouvelle paire de clés a été sauvegardée avec succes.")."</div>\n";
                        } else {
                                echo "<div align='center'><b>ERREUR</b> : ".gettext("Impossible de sauvegarder la nouvelle clé privée.")."</div>\n";
                        }
                } else {
                        echo "<div align='center'><b>ERREUR</b> : ".gettext("Impossible de sauvegarder la nouvelle clé publique.")."</div>\n";
                }
        } else {
                echo "<div align='center'><b>ERREUR</b> : ".gettext("Impossible de sauvegarder cette paire de clés.")."</div>\n";
        }
} else {
        echo "<div class=alert_msg>".gettext("Cette fonctionnalité, nécessite les droits d'administrateur du serveur LCS !")."</div>\n";
}
include ("./includes/pieds_de_page.inc.php");
?>
