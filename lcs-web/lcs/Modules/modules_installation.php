<?
/* =============================================
   Projet LCS
   Administration serveur LCS «Installation d'un module»
   modules_installation.php
   Equipe Tice academie de Caen
   21/03/2014
   Distribu selon les termes de la licence GPL
   ============================================= */
session_name("Lcs");
@session_start();
include "/var/www/Annu/includes/check-token.php";
if (!check_variables()) exit;
if ( ! isset($_SESSION['login'])) {
    echo "<script type='text/javascript'>";
    echo 'alert("Suite \340 une p\351riode d\'inactivit\351 trop longue, votre session a expir\351 .\n\n Vous devez vous r\351authentifier");';
    echo 'location.href = "../lcs/logout.php"</script>';
    exit;
}
$login=$_SESSION['login'];
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$msgIntro = "<H1>Gestion des modules LCS</H1>\n";

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");
if (count($_GET)>0) {
        //configuration objet
        include ("/var/www/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        //purification des variables
        $p=$purifier->purify($_GET['p']);
        $v=$purifier->purify($_GET['v']);
        $n=$purifier->purify($_GET['n']);
        $d=$purifier->purify($_GET['d']);
}
include("modules_commun.php");
$nf="lcs-".$n;
//on supprime eventuellement les residus d'un processus interrompu
$net=" rm -f /tmp/ecran_install*";
exec($net);
//creation du fichier /tmp/ecran_install_nom_module.html
$fecran = cree_nom_fichier_ecran($nf);
creation_ecran($fecran,$msgIntro);
ecrit_ecran($fecran, "<H3>Installation de </H3> <B> " . $d ." $nf ". $v ." le ". date('d/m/Y &#224; H\hi')." </B> " );
ecrit_ecran($fecran,"<H3>Ex&#233;cution du script: </H3>");
//commande d'installation
$nfe=  escapeshellarg("lcs-".$n);
$cmd= "/usr/bin/sudo -H -u root /usr/share/lcs/scripts/gestpack.sh " .escapeshellarg($p). " install ".$nfe;
exec($cmd);
?>
</HTML>

