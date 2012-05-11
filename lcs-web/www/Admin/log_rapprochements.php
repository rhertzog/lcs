<?
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS 
   Log des comptes LCS / ENT rapproches
   log_rapprochements.php
   Equipe Tice academie de Caen
   07/12/2012 
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

$msgIntro = "<h1>Journaux des comptes LCS / ENT rapproch&eacute;s</h1>\n";

list ($idpers, $login)= isauth();


if (ldap_get_right("lcs_is_admin",$login)!="Y") {
    echo $head;
    die (gettext("Vous n'avez pas les droits suffisants pour acc&eacute;der &agrave; cette fonction")."</body></html>");
}



if ($mod_auth=="LCS") {
    echo $head;
    die (gettext("<div class='error_msg'>Le mode d'authentification LCS doit etre ENT pour acceder a cette page.</div>")."</body></html>");
}
    $logins=$_POST['logins'];

	// On efface les comptes rapproches selectionnes
    if ( count ($logins) > 0 ) {
    	for ($loop=0; $loop < count ($logins)  ; $loop++) {
    		//echo "login :".$logins[$loop]."</br>";
    		$query="DELETE FROM ent_lcs WHERE login_lcs='$logins[$loop]'";
			$result=@mysql_query($query) or die($query);
    	}
    }
    	
    echo $head;	
	echo $msgIntro;

	$query="select * from ent_lcs where id_ent!='' order by 'login_lcs' ASC;";
	$result=mysql_query($query);
	if ($result)  {
		if ( mysql_num_rows($result) !=0 ) {      	  
          	echo '<div id="wrapper">
            	<form action="log_rapprochements.php" method="post">
				<table cellpadding="0" cellspacing="0"  class="sortable" id="sorter">
				<tr>
					<th>Nom Pr&eacute;nom</th>
					<th>Login LCS</th>
					<th>ID ENT rapproch&eacute;</th>
				</tr>';
          	while ($r=mysql_fetch_object($result))
	  			{ 
	  				list($user, $groups)=people_get_variables($r->login_lcs, true);
	  				echo "<tr>\n";
		  			echo "<td>".$user["fullname"]."</td>\n";	
		  			echo "<td>" .$r->login_lcs. "</td>\n";
		  			echo "<td>".$r->id_ent."</td>\n";
		  			echo "<td><input type='checkbox' name='logins[]' value='".$r->login_lcs."'></td>\n";
		  			echo "</tr>\n";
				}
          	echo "</table>\n";
          	echo '<table class="remove">
          			<tr>
          				<td><input type="submit" name="remove" value="d&eacute;associer"></td>
          			</tr>
          		  </table>';
          	echo '</form>';
          	echo '</div>
			<script type="text/javascript">
				var sorter=new table.sorter("sorter");
				sorter.init("sorter",4);
			</script>';
		} else {
			echo "<h3>Pas de comptes LCS / ENT rapproch&eacute;s.</h3>\n";      
		}
	}
	mysql_free_result($result);
    mysql_close();
	include ("/var/www/lcs/includes/pieds_de_page.inc.php");
?>
