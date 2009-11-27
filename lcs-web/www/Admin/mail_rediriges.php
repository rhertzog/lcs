<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS 
   Log des redirections de mails
   mail_rediriges.php
   Equipe Tice academie de Caen
   06/11/2009 
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");

$head = "<html>\n";
$head .= "	<head>\n";
$head .= "         <title>...::: Interface d'administration Serveur LCS :::...</title>\n";
$head .= "         <link rel='stylesheet' href='./style/stylesort.css' />\n";
$head .= "         <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
$head .= "         <script type='text/javascript' src='./js/script.js'></script>\n";
$head .= "	</head>\n";
$head .= "	<body>\n";

$msgIntro = "<h1>Historique de la redirection des mails</h1>\n";

list ($idpers, $login)= isauth();


if (ldap_get_right("lcs_is_admin",$login)!="Y") {
    echo $head;
    die (gettext("Vous n'avez pas les droits suffisants pour acc&eacute;der &agrave; cette fonction")."</body></html>");
}

//test si squirrelmail est installe pour redirection mails
$query="SELECT value from applis where name='squirrelmail'";
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
    die (gettext("<div class='error_msg'>Cette fonction n&eacute;cessite Squirrelmail fonctionnel</div>")."</body></html>");
}
        echo $head;	
	echo $msgIntro;
	$query="SELECT * from redirmail where 1 order by `id` ASC ";
	$result=mysql_query($query);
	if ($result) 
		{
          if ( mysql_num_rows($result) !=0 ) {      
          echo '<div id="wrapper">
			<table cellpadding="0" cellspacing="0"  class="sortable" id="sorter">
			<tr>
				<th>Auteur</th>
				<th>Login redirig&eacute;</th>
				<th>Adresse perso</th>
				<th>Copie</th>
				<th>Date</th>
				<th>Remote IP</th>
			</tr>';
          while ($r=mysql_fetch_object($result))
	  	{ 
		  echo "<tr>\n";
		  echo "<td>" .$r->faitpar.  "</td>\n";
		  echo "<td>" .$r->pour. "</td>\n";
 		  echo "<td>".$r->vers."</td>\n";		  
		  echo "<td>".$r->copie."</td>\n";		  
		  echo "<td>".$r->date."</td>\n";
		  echo "<td>".$r->remote_ip."</td\n";
		  echo "</tr>\n";
		}
          echo "</table>";
          echo '</div>
			<script type="text/javascript">
			var sorter=new table.sorter("sorter");
			sorter.init("sorter",4);
			</script>';
          } else {
              echo "<h3>Pas d'adresse mail redirig&eacute;e.</h3>\n";      
          }
        }

	mysql_free_result($result);
        mysql_close();
	include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
