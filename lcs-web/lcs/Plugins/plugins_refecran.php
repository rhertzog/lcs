<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>...::: Interface d'administration Serveur LCS :::...</title>
    <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>
<?php
$dpid=$_GET['dpid'];
$majp=$_GET['majp'];
$p=$_GET['p'];
$v=$_GET['v'];
$s=$_GET['s'];
$d=$_GET['d'];
$n=$_GET['n'];
$stop=$_GET['stop'];

include ("/var/www/lcs/includes/headerauth.inc.php");
include("plugins_commun.php");
if(empty($_SERVER['QUERY_STRING'])) $QSTRING = "";
else $QSTRING = $_SERVER['QUERY_STRING'];
?>
    <script TYPE="text/javascript">
	<!--
		autorefresh = parseInt('3');
		if (autorefresh > 0) 
			{
			  <?php 
			  	if (!isset($stop))
					echo "  setTimeout(\"self.location.href = self.location.protocol + '//' + self.location.host + self.location.pathname + '" . ($QSTRING=="" ? "" : "?$QSTRING") . "'\", 1000 * autorefresh);";
			  ?>
			 }
	 // -->
    </script>
  </head>
<body>
<?php
	if (isset($p)) { // il s'agit d'une installation
		$fecran = cree_nom_fichier_ecran(get_nom_de_fichier($p));
                $MSG = "Installation";
        }                
	if (isset($dpid)) { // il s'agit d'une desinstallation
		$fecran = cree_nom_fichier_ecran($dpid);
                $MSG = "D&#233;sinstallation";
        }                
	if (isset($majp)) { // il s'agit d'une mise a jour
		$fecran = cree_nom_fichier_ecran($majp);
                $MSG = "Mise &#224; jour";
        }                
	if (file_exists($fecran))
		{
		  $df = fopen($fecran,"r");
		  fpassthru($df);
		} 
if (!isset($stop)) {
 	echo "<DIV style=\"text-align: center; height:80px\"><IMG ALT=\"Patientez...\" SRC=\"Images/patientez.gif\"></DIV>\n";
        echo "<DIV class=\"alert_msg\">$MSG plugin LCS, patientez...</DIV>\n";
}        
?>
<script TYPE="text/javascript">
	<!--
		self.scrollTo(0,1000000);
	//-->
</script>
<?        
include ("/var/www/lcs/includes/pieds_de_page.inc.php");		
if (isset($stop))
	unlink($fecran); // si on arrete le rafraichissement, on supprime le fichier d'ecran.
?>
