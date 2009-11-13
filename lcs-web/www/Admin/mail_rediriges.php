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
$msgIntro = "<H1>Historique de la redirection des mails</H1>\n";
list ($idpers, $login)= isauth();

if (ldap_get_right("lcs_is_admin",$login)!="Y")
  die (gettext("Vous n'avez pas les droits suffisants pour accéder à cette fonction")."</BODY></HTML>");
 //test si squirrelmail est installe pour redirection mails
  $query="SELECT value from applis where name='squirrelmail'";
  $result=mysql_query($query);
  if ($result) 
	{
          if ( mysql_num_rows($result) !=0 ) {
          $r=mysql_fetch_object($result);
          $test_squir=$r->value;
          }
          else $test_squir="0";
          }
          else $test_squir="0";

   //fin test squirrelmail


	echo "<HTML>\n";
	echo "	<HEAD>\n";
	echo "		<TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n";
	echo "		<link rel='stylesheet' href='./style/stylesort.css' />\n";
	echo "		<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n";
	echo "<script type='text/javascript' src='./js/script.js'></script> 		\n";
	echo "	</HEAD>\n";
    	echo "	<BODY>\n";
	if ($test_squir=="0")die (gettext("<div class='error_msg'>Cette fonction n&eacute;cessite Squirrelmail fonctionnel</div>")."</BODY></HTML>");
	
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
		  echo "<TR>\n";
		  echo "<TD>" .$r->faitpar.  "</TD>\n";
		  echo "<TD>" .$r->pour. "</TD>\n";                  
 		  echo "<TD>".$r->vers."</TD>\n";		  
		  echo "<TD>".$r->copie."</TD>\n";		  
		  echo "<TD>".$r->date."</TD>\n";
		  echo "<TD>".$r->remote_ip."</TD>\n";
		  echo "</TR>\n";
		}
          echo "</TABLE>";
          echo '</div>
			<script type="text/javascript">
			var sorter=new table.sorter("sorter");
			sorter.init("sorter",4);
			</script>';
          } else {
              echo "<H3>Pas d'adresse mail redirig&eacute;e.</H3>\n";      
          }
        }
        
	mysql_free_result($result);
        mysql_close();
	include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
