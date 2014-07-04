<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Desinstallation d'un module»
   Modules_desinstallation.php
   Equipe Tice academie de Caen
   28/03/2014
   Distribue selon les termes de la licence GPL
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
$msgIntro = "<H1>Gestion des Modules LCS</H1>\n";

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");
if (count($_GET)>0) {
        //configuration objet
        include ("/var/www/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        //purification des variables
        $dpid=$purifier->purify($_GET['dpid']);
}

include("modules_commun.php");
$dpid=mysql_real_escape_string($dpid);
$result = mysql_query("SELECT * FROM applis WHERE id='$dpid'");
if (!$result) die("Erreur lors de la requ&#232;te MySQL");
$row = mysql_fetch_object($result);
$version = $row->version;
$nommodule= $row->name;
$descrip=$row->descr;
mysql_free_result($result);
//on supprime eventuellement les residus d'un processus interrompu
$net=" rm -f /tmp/ecran_install*";
exec($net);
//creation du fichier /tmp/ecran_install_nom_module.html
$nf="lcs-".$nommodule;
$nfe=  escapeshellarg($nf);
$fecran = cree_nom_fichier_ecran($nf);
creation_ecran($fecran,$msgIntro);
ecrit_ecran($fecran, "<H3>D&#233;sinstallation de </H3><B>" . $descrip." $nf ".$version. " le ". date('d/m/Y &#224; H\hi')."</B>");
ecrit_ecran($fecran,"<H3>Execution du script</H3>");
//commande de desinstallation
$cmd= "/usr/bin/sudo -H -u root /usr/share/lcs/scripts/gestpack.sh 'deb http://lcs.crdp.ac-caen.fr/ Lcs main' 'remove --purge' ".$nfe;
exec($cmd);
?>
</html>

