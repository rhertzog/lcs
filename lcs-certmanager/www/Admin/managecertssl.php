<?
# /var/www/Admin/managecertssl.php derniere version du : 13/06/2013
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers, $login)= isauth();

$DEBUG = true;

// Messages d'aide
function msgaide($msg) {
    return ("&nbsp;<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('".$msg."')")."\"><img name=\"action_image2\"  src=\"../images/help-info.gif\"></u>");
}
$msg1="";

if ($idpers == "0") header("Location:$urlauth");
$html = "
	  <head>\n
	  <title>...::: Gestion certificats SSL  :::...</title>\n
	  <meta http-equiv='content-type' content='text/html;charset=utf-8' />\n
	  <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
	  <style type='text/css'>
		table {
			border-collapse:collapse; 
			border:1px solid #48659E;
			margin-bottom: 10px
		}
		tr.head {
			border:1px solid #48659E;
			background-color: #E1EFFB;
			text-align:center
		}
		td {
			border:1px solid #48659E;
		}		
		

	  </style>
	  </head>\n
	  <body>\n";
$html .= "<div id='container'><h2>Gestion certificats SSL LCS</h2>\n";
echo $html;
// debut is_admin
if (is_admin("system_is_admin",$login)=="Y") {

	// Traitement Formulaire
	if(isset($_POST["managecert"])) {
		if ( $DEBUG == "true" ) {
			echo "<u>DEBUG manage cert:</u></br>";
			echo "cert sel " . $_POST["sel"] ."<br/>";
			echo "cert erase " . $_POST["delete"] ."<br/>";
		}
		// test si le certificat selectionné est différent du certificat actif
		$query="SELECT id from sslcert where sel='1'";
		$result=mysql_query($query);
		if ($result) {
			$r=mysql_fetch_row($result);
			$sslcertsel = $r[0];
			if ( $DEBUG == "true" ) echo "id certificat actif ".$r[0]."<br />";
		}
		mysql_free_result($result);	
		if ( $sslcertsel != $_POST["sel"] ) {
			echo "Le certificat sélectionné est différent du certificat actif ! On change !<br />";
			echo "Le changement de certificat sera effectif dans une minute, attention, les services (apache, imap-sssl, cas) vont redémarrer !<br />";
			// recherche du nom du certificat selectionné
			$query="SELECT name from sslcert where id='".$_POST["sel"]."'";
			$result=mysql_query($query);
			if ($result) {
				$r=mysql_fetch_row($result);
				$sslcertname = $r[0];
				if ( $DEBUG == "true" ) echo "name certificat sel ".$r[0]."<br />";
			}
			mysql_free_result($result);	
			// Execution de la tache sudo
			exec("/usr/bin/sudo /usr/sbin/lcs-certmanager -s '$sslcertname'");

		} else {
			echo "Le certificat sélectionné est le même que le certificat actif ! Pas de modification.<br />";	
		}
		
		// test si le certificat a effacer est différent du certificat actif
		if ( $_POST["sel"] != $_POST["delete"] ) {
			// recherche du nom du certificat selectionné pour effacement
			$query="SELECT name from sslcert where id='".$_POST["delete"]."'";
			$result=mysql_query($query);
			if ($result) {
				$r=mysql_fetch_row($result);
				$sslcertname = $r[0];
				echo "name certificat a effacer ".$r[0]."<br />";
			}
			mysql_free_result($result);	
			// Execution de la tache sudo			
				echo "Le certificat a été effacé du magasin.<br />";
				exec("/usr/bin/sudo /usr/sbin/lcs-certmanager -r '$sslcertname'");
		} else {
			echo "Impossible d'effacer le certificat actif.</br>";
		}
	}	
	// En tete formulaire
	echo "<form name =\"managecertssl\" action=\"managecertssl.php\" method=\"post\">\n";
	// En tete tableau
	$html="
	<table>\n
	<tr class='head'>\n
		<td>Nom</td>\n
		<td>Commence</td>\n
		<td>Expire</td>\n
		<td>Sujet</td>\n
		<td>Selectionner</td>\n
		<td>Effacer</td>\n
	</tr>\n
	";
	echo $html;
	
    $query="SELECT * from sslcert";
	$result=mysql_query($query);
    if ($result) {
		while ($r=mysql_fetch_array($result)) {
			echo "<tr>\n";
			echo "<td>".$r['name']."</td>\n";
			echo "<td>". str_replace( "GMT", "", $r['notbefore']) . "</td>\n";
			echo "<td>". str_replace( "GMT", "",$r['notafter']) . "</td>\n";
			echo "<td>". str_replace( "subject= /", "",$r['description']) . "</td>\n";
			echo "<td><input type='radio'";  
			if ( $r['sel'] == "1" ) echo " checked ";
			echo "name='sel' value='".$r[id]."'></td>\n";
			echo "<td><input type='radio' name='delete' value='".$r[id]."'</td>\n";
			echo "<tr>\n";
		}
    }	
    mysql_free_result($result);
    // Fin tableau
    $html ="</table>\n";
    // Fin formulaire
    $html .= "<input type=\"hidden\" name=\"managecert\" value=\"true\">\n";
	$html .= "<input type=\"submit\" value=\"Valider sélections\">\n";
	$html .= "</form>\n";
	echo $html;
	
}
// fin is_admin
else echo "Vous n'avez pas les droits nécessaires pour ordonner cette action...";

echo "</div><!-- Fin container-->\n";
include ("../lcs/includes/pieds_de_page.inc.php");
?>