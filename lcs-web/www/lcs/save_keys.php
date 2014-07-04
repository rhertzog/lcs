<?php
/*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "../Annu/includes/check-token.php";
if (!check_acces()) exit;
$login=$_SESSION['login'];
if (count($_POST)>0) {
	//configuration objet
	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);
	//purification des variables
	$keys=$purifier->purify($_POST['keys']);
}
require "./includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
		// open acces for keys
		exec ("/usr/bin/sudo /usr/share/lcs/scripts/gestkeys.sh 'open'", $AllOutput, $ReturnValue);
		// put keys
		$public_key="var public_key_e=[".$e."];\n";
		$public_key.="var public_key_pq=[".$pq."];\n";
		$fp=@fopen("/usr/share/lcs/privatekey/public_key.js","w");
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
			} else
				echo "<div align='center'><b>ERREUR</b> : ".gettext("Impossible de sauvegarder la nouvelle cl&#233; priv&#233;e.")."</div>\n";

		} else
				echo "<div align='center'><b>ERREUR</b> : ".gettext("Impossible de sauvegarder la nouvelle cl&#233; publique.")."</div>\n";
    	// close acces for keys and move public_ley
    	exec ("/usr/bin/sudo /usr/share/lcs/scripts/gestkeys.sh 'close'", $AllOutput, $ReturnValue);

	} else
		echo "<div align='center'><b>ERREUR</b> : ".gettext("Impossible de sauvegarder cette paire de cl&#233;s.")."</div>\n";
} else
	echo "<div class=alert_msg>".gettext("Cette fonctionnalit&#233;, n&#233;cessite les droits d'administrateur du serveur LCS !")."</div>\n";

include ("./includes/pieds_de_page.inc.php");
?>
