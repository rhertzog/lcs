<?
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
$head .= "         <link rel='stylesheet' href='./style/stylesort.css' />\n";
$head .= "         <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
$head .= " 		   <link rel='stylesheet' type='text/css' media='screen' href='../libjs/jquery-ui/css/redmond/jquery-ui.css'> \n";
$head .= "         <link rel='stylesheet' type='text/css' media='screen' href='../libjs/jqGrid/css/ui.jqgrid.css' >\n";
$head .= "         <script type='text/javascript' src='../libjs/jquery/jquery.js'></script>\n";
$head .= "         <script type='text/javascript' src='../libjs/jqGrid/js/i18n/grid.locale-fr.js'></script>\n";
$head .= "         <script type='text/javascript' src='../libjs/jqGrid/js/jquery.jqGrid.min.js'></script>\n";
$head .= "         <script type='text/javascript' src='./js/script_redir.js'></script>\n";
$head .= "	</head>\n";
$head .= "	<body>\n";

$msgIntro = "<h1>Redirection des mails</h1>\n <br/>";

if (ldap_get_right("lcs_is_admin",$login)!="Y") {
    echo $head;
    die (gettext("Vous n'avez pas les droits suffisants pour acc&eacute;der &agrave; cette fonction")."</body></html>");
}

//test si squirrelmail est installe pour redirection mails
$query="SELECT value from applis where name='squirrelmail' or name='roundcube'";
$result=mysql_query($query);
if ($result) {
    if ( mysql_num_rows($result) !=0 ) {
          $r=mysql_fetch_object($result);
          $test_squir=$r->value;
    } else $test_squir="0";
} else $test_squir="0";

//fin test squirrelmail

if ($test_squir=="0") {
    echo $head;
    die (gettext("<div class='error_msg'>Cette fonction n&eacute;cessite un Webmail fonctionnel</div>")."</body></html>");
}
echo $head;
echo $msgIntro;
echo '<table id="sorter"></table>
<div id="pager"></div>';
include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
