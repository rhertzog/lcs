<?
session_start();
$dpid=$_GET['dpid'];
$p=$_GET['p'];
$v=$_GET['v'];
$n=$_GET['n'];
$d=$_GET['d'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>...::: Interface d'administration Serveur LCS :::...</title>
    <link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>
<?php

include ("/var/www/lcs/includes/headerauth.inc.php");
include("modules_commun.php");
include ("flag.php");
if(empty($_SERVER['QUERY_STRING'])) $QSTRING = "";
else $QSTRING = $_SERVER['QUERY_STRING'];
?>
    <script TYPE="text/javascript">
	<!--
		autorefresh = parseInt('3');
		if (autorefresh > 0) 
			{
			  <?php 
			  	if ($flip!="1")
					echo "  setTimeout(\"self.location.href = self.location.protocol + '//' + self.location.host + self.location.pathname + '" . ($QSTRING=="" ? "" : "?$QSTRING") . "'\", 1000 * autorefresh);";
			  ?>
			 }
	 // -->
    </script>
  </head>
<body>
<?php
	if (isset($p)) { // il s'agit d'une installation
		$nf="lcs-".$n;
		$fecran = cree_nom_fichier_ecran($nf);
                $MSG = "Installation";
        }                
	if (isset($dpid))   { // il s'agit d'une desinstallation
		
	if (!isset($_SESSION['nommod'])) {
		$result = mysql_query("SELECT * FROM applis WHERE id='$dpid'");
		if (!$result) die("Erreur lors de la requ&#234;te MySQL");
		$row = mysql_fetch_object($result);
		$_SESSION['nommod']= $row->name;
		}
		
		$nf="lcs-".$_SESSION['nommod'];
		$fecran = cree_nom_fichier_ecran($nf);
                $MSG = "Desinstallation";
        }                
	              
	if (file_exists($fecran))
		{
		  $df = fopen($fecran,"r");
		  fpassthru($df);
		} 
if ($flip!="1") {
 	echo "<DIV style=\"text-align: center; height:80px\"><IMG ALT=\"Patientez...\" SRC=\"../Plugins/Images/patientez.gif\"></DIV>\n";
        echo "<DIV class=\"alert_msg\">$MSG module LCS, patientez quelques minutes ...</DIV>\n";
}        
?>
<script TYPE="text/javascript">
	<!--
		self.scrollTo(0,1000000);
	//-->
</script>
<?        
include ("/var/www/lcs/includes/pieds_de_page.inc.php");		
if ($flip=="1"){
	$Cmd="mv ".$fecran. " /usr/share/lcs/Modules/Logs/".date('Ymd_His')."_".$MSG."_".$nf.".html";
	exec($Cmd,$x,$y);
	$flg= fopen("flag.php",w);
	fputs($flg,"<? \$flip=0; ?>");
	fclose($flg);
	session_destroy();}
?>
