<?php
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Desinstallation d'un module»
   modules_installes.php
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
        $a=$purifier->purify($_GET['a']);
        $pid=$purifier->purify($_GET['pid']);
        $np=$purifier->purify($_GET['np']);
        $action=$purifier->purify($_GET['action']);
        $nom_module=$purifier->purify($_GET['nommod']);
}
include("modules_commun.php");

echo "<HTML>\n";
echo "	<HEAD>\n";
echo "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
echo "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
echo "		<LINK  href='boutons_style.css' rel='StyleSheet' type='text/css'>\n";
echo "      <link rel='stylesheet' href='../Admin/style/stylesort.css' />\n";
echo "		<script type='text/javascript' src='../Admin/js/script.js'></script>\n";
echo "	</HEAD>\n";
echo "	<BODY>\n";
echo $msgIntro;


parsage_du_fichier_xml(); // utilisee par la fonction maj_dispo afin de ne pas parser &#224; chaque appel de la fonction
//modification du type de maj
if (isset($_GET['np'])) {
    $cmd="/usr/bin/sudo -u root /usr/share/lcs/scripts/action_modul.sh ".escapeshellarg($np)." ".escapeshellarg($action);
    exec($cmd);
}

//activation/desactivation
if (isset($_GET['a']) && isset($_GET['pid']))
    {
    $pid=mysql_real_escape_string($pid);
    mysql_query("UPDATE applis SET value='$a' WHERE id='$pid';") or die("Erreur lors de l'activation/d&#233;sactivation du plugin...");
    }
//recherche de la branche du sourceslist
$branch="";
$commande ='cat /etc/apt/sources.list | grep deb | grep Lcs | cut -d" " -f3';
exec($commande,$branche,$ret_val);
if ($ret_val== 0)
    {
    switch ($branche[0])
            {
            case "Lcs":
                $branch = 'stable';
                break;
            case "LcsTesting":
                $branch = 'testing';
                break;
            case "LcsXP":
                $branch = 'exp&#233;rimentale';
                break;
            }
    }
//recherche urlmajmod
$url=explode('/',$urlmajmod);
switch ($url[count($url)-2])
    {
    case "modulesLcswheezy":
        $urlmaj = 'stable';
        break;
    case "modulesLcswheezyTesting":
        $urlmaj = 'testing';
        break;
    case "modulesLcswheezyXP":
        $urlmaj = 'exp&#233;rimentale';
        break;
    }

//recherche des paquets mis en hold
$pack_hold=array();
$cmd='dpkg --get-selections | grep hold | cut  -f1 | cut -d"-" -f2';
exec($cmd,$pack_hold,$ret_val);

$query="SELECT * from applis where type='M' OR type='N' OR type='S'";
$result=mysql_query($query);
if ($result)
    {
          if ( mysql_num_rows($result) !=0 ) {
            // Affichage des Modules installes
            echo "<H3>Modules install&#233;s </H3>\n";
            //Affichage des infos
            if ($branch!="" || $urlmaj!="")
            {
            echo '<div class="mesg"> Avec la configuration actuelle du LCS,<br />
            <ul><li>l\'autorisation de la mise &#224; jour  d\'un module est relative &#224; la branche :<ul>';
            if ($branch!="") echo '<li><u>'.$branch.'</u> pour les mises &#224; jour automatiques (nocturnes)';
            if ($urlmaj!="") echo '<li><u>'.$urlmaj.'</u> pour les mises &#224; jour manuelles ';
            echo '</ul>';
            if ($urlmaj!="") echo '<li>la disponiblit&#233; affich&#233;e d\'une mise &#224; jour est relative &#224; la branche <u>'.$urlmaj.'</u>';
            echo '</ul></div>';
            }
            echo '<div id="wrapper">
            <table cellpadding="0" cellspacing="0"  class="sortable" id="sorter">';
            echo '<th>Nom</th><Th>Description</Th><Th class="nosort">Version</Th><Th class="nosort">Aide</Th><Th class="nosort">Activation</Th><Th class="nosort">Autorisation_Maj</Th>
            <Th class="nosort">Maj_dispo</Th><Th class="nosort">Action</Th></TR>';
            while ($r=mysql_fetch_object($result))
                {
                list ($v,$plug) = maj_dispo($r->name);
                echo "<TR>\n";
                echo "<TD>" . $r->name . "</TD>\n";
                echo "<TD>" . utf8_encode($r->descr) . "</TD>\n";
                echo "<TD>" . $r->version . "</TD>\n";
                echo "<TD class=\"centr\"><A HREF=\"../../doc/" . $r->name . "/html/index.html\" TITLE=\"Aide\"><IMG SRC=\"../Modules/Images/plugins_help.png\" ALT=\"Aide\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\" /></A></TD>\n";
                if (($r->type =='N'|| $r->type =='M') && $r->value != "0")
                echo "<TD class=\"centr\"><A HREF=\"modules_installes.php?pid=" . $r->id ."&jeton=".md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])). "&a=0\"><IMG SRC=\"../Modules/Images/plugins_desactiver.png\" TITLE=\"D&#233;sactiver\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
                elseif (($r->type =='N'|| $r->type =='M') && $r->value != "1")
                echo "<TD class=\"centr\"><A HREF=\"modules_installes.php?pid=" . $r->id."&jeton=".md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])). "&a=1\"><IMG SRC=\"../Modules/Images/plugins_activer.png\" TITLE=\"Activer\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
                else
                echo "<TD>&nbsp;</TD>\n";
                $nom_paquet= "lcs-".mb_strtolower($r->name);
                if ($r->type =='N' && (!in_array ($r->name, $pack_hold)))
                echo '<TD class="buttons"><a href="modules_installes.php?np='.$nom_paquet."&jeton=".md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'&action=hold" class="positive" title="Interdire la mise &#224; jour de ce module ">&nbsp;OUI&nbsp;</a></TD>';
                elseif  ($r->type =='N' && (in_array ($r->name, $pack_hold)))
                echo '<TD class="buttons "><a href="modules_installes.php?np='.$nom_paquet."&jeton=".md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'&action=install" class="negative" title="Autoriser la mise &#224; jour de ce module">&nbsp;NON&nbsp;</a></TD>';
                else
                echo "<TD>&nbsp;</TD>\n";
                if ($v != false)
                echo "<TD><A HREF=\"modules_install.php?p=" . $plug["serveur"] . "&n=" .$r->name ."&jeton=".md5($_SESSION['token'].htmlentities("/Modules/modules_install.php")) . "\"><IMG SRC=\"../Modules/Images/plugins_maj.png\" TITLE=\"Mettre &#224; jour\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
                else
                echo "<TD title='Pas de mise &#224; jour disponible'>&nbsp;</TD>\n";
                echo "<TD class=\"centr\"><A HREF=\"modules_installes.php?dpid=" . $r->id . "&nommod=".$r->name."&jeton=".md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF']))."\"><IMG SRC=\"../Modules/Images/plugins_desinstall.png\" TITLE=\"Desinstaller\" BORDER=\"0\" WIDTH=\"29\" HEIGHT=\"28\"/></A></TD>\n";
                echo "</TR>\n";
                 }
                echo "</TABLE>";
                echo '</div>
                <script type="text/javascript">
                var sorter=new table.sorter("sorter");
                sorter.init("sorter",0);
                </script>';
          }
          else
              {
               echo "<H3>Pas de module install&#233;.</H3>\n";
               }
}

mysql_free_result($result);
if (isset($_GET['dpid']))
    {
    $jeton="&jeton=".md5($_SESSION['token'].htmlentities("/Modules/modules_desinstall.php"));
    echo "<script type='text/javascript'>";
    echo " if (confirm('Confirmez vous la suppression du module ".$nom_module." ? ')){";
    echo ' location.href = "modules_desinstall.php';
    echo '"+ "?dpid=" + "'.$dpid.$jeton.'" ;} else {';
    echo ' location.href = "';
    echo $_SERVER['PHP_SELF'];
    echo '"   ;} </script> ';
    }
include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
