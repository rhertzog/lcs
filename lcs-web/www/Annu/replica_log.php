<?php
if ( file_exists("/var/www/se3") ) $servertype="SE3"; else $servertype="LCS";

if ( $servertype== "SE3") {
    // Traduction
    require_once ("lang.inc.php");
    bindtextdomain('se3-annu',"/var/www/se3/locale");
    textdomain ('se3-annu');
}

// on rappelle la page tant que le resultat ErrReplica.txt n'existe pas
// $comment = $_GET['comment'];

if($_POST['action']=="ok" || (!file_exists("/tmp/ErrReplica.txt"))) {
	echo "<meta HTTP-EQUIV=\"Refresh\" CONTENT=\"1;url='$PHP_SELF'\">\n";
}

if ( $servertype== "SE3") {
    $path_script="/usr/share/se3/sbin";
    require "ihm.inc.php";
    require ("entete.inc.php");
    $login=isauth();
    if ($login == "") header("Location:$urlauth");
    // Aide
    $_SESSION["pageaide"]="R%C3%A9plication_d%27annuaires";
} else {
    // Cas du LCS
    $pathlcsorse3="../lcs/includes/";
    $path_script="/usr/share/lcs/sbin";
    include ("../lcs/includes/headerauth.inc.php");
    include ("../Annu/includes/ldap.inc.php");
    include ("../Annu/includes/ihm.inc.php");
    list ($idpers, $login)= isauth();
    if ($idpers == "0") header("Location:$urlauth");
    header_html();
}

$action = $_POST['action'];
$type = $_POST['type'];
$status = $_POST['status'];

echo "<h1>".gettext("R&#233;plication de l'annuaire LDAP")."</h1>";

if (is_admin("system_is_admin",$login)!="Y")
	die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");

//Lance le script mkslurpd
if ($action == "ok") {
	exec ("/usr/bin/sudo $path_script/mkslurpd -l $type -$status");
	$action="0";
}


if ($type=="anonymous") {	
	echo"<H3>".gettext("Log les diff&#233;rences entre les deux annuaires")."</H3>";
}
elseif ($type=="only_pass") {
	echo"<H3>".gettext("Synchronisation partielle de l'annuaire esclave")."</H3>";
}
elseif ($type=="full") {	
	echo"<H3>".gettext("Synchronisation totale de l'annuaire esclave")."</H3>";
}
else {
	echo"<H3>".gettext("Synchronisation des annuaires")."</H3>";
}

// Si le fichier de log existe on l'affiche
if(file_exists("/tmp/ErrReplica.txt") && $action != "0") {
	$fichier = fopen ("/tmp/ErrReplica.txt","r");
	sleep(5);
	while (!feof($fichier)) {
		$buffer = fgets($fichier,255);
		echo "<br>";
		echo $buffer;
	}
} else {  // Sinon on affiche une image d'attente
		?>	
		<center>
			<table align="center" border="2">
	  		<tbody>
	    		  <tr>
	      		    <td align="center"><?php echo gettext("Veuillez patienter"); ?></td>
	    		  </tr>
	    		  <tr>
	      		    <td>
				<center><img src='images/play_anim.gif' border='0'></center>	
	       		    </td>
	     		  </tr>
	   		</tbody>
	 		</table>
		<?php	 
}

include $pathlcsorse3."pdp.inc.php";
?>
