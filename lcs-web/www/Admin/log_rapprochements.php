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

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");

$head = "<html>\n";
$head .= "	<head>\n";
$head .= "         <title>...::: Interface d'administration Serveur LCS :::...</title>\n";
$head .= '<meta http-equiv="content-type" content="text/html;charset=utf-8" />';
$head .= "         <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
$head .= "         <link rel='stylesheet' href='./style/stylesort.css' />\n";
$head .= " 		   <link rel='stylesheet' type='text/css' media='screen' href='../libjs/jquery-ui/css/redmond/jquery-ui.css'> \n";
$head .= "         <link rel='stylesheet' type='text/css' media='screen' href='../libjs/jqGrid/css/ui.jqgrid.css' >\n";
$head .= "         <script type='text/javascript' src='../libjs/jquery/jquery.js'></script>\n";
$head .= "         <script type='text/javascript' src='../libjs/jqGrid/js/i18n/grid.locale-fr.js'></script>\n";
$head .= "         <script type='text/javascript' src='../libjs/jqGrid/js/jquery.jqGrid.min.js'></script>\n";
$head .= "         <script type='text/javascript' src='./js/script_rapproche.js'></script>\n";
$head .= "	</head>\n";
$head .= "	<body>\n";
$msgIntro = "<h1> Comptes LCS / ENT rapproch&eacute;s</h1>\n <br />";

if (ldap_get_right("lcs_is_admin",$login)!="Y") {
    echo $head;
    die (gettext("Vous n'avez pas les droits suffisants pour acc&eacute;der &agrave; cette fonction")."</body></html>");
}

if ($mod_auth=="LCS") {
    echo $head;
    die (gettext("<div class='error_msg'>Le mode d'authentification LCS doit etre ENT pour acceder a cette page.</div>")."</body></html>");
}

echo $head;
echo $msgIntro;
echo '<table id="sorter_rappro"></table>
<div id="pager_rappro"></div>';
include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
