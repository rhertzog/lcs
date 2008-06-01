<?
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
  <HEAD>
    <META content="text/html; charset=ISO-8859-1" http-equiv="content-type">
    <TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>
    <LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>
<?php

include ("/var/www/lcs/includes/headerauth.inc.php");
include("modules_commun.php");
include ("flag.php");
if(empty($QUERY_STRING)) $QSTRING = "";
else $QSTRING = $QUERY_STRING;
?>
    <SCRIPT TYPE="text/javascript">
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
    </SCRIPT>
  </HEAD>
<BODY>
<?php
	if (isset($p)) { // il s'agit d'une installation
		$nf="lcs-".$n;
		$fecran = cree_nom_fichier_ecran($nf);
                $MSG = "Installation";
        }                
	if (isset($dpid))   { // il s'agit d'une desinstallation
		
	if (!isset($_SESSION['nommod'])) {
		$result = mysql_query("SELECT * FROM applis WHERE id='$dpid'");
		if (!$result) die("Erreur lors de la requète MySQL");
		$row = mysql_fetch_object($result);
		$_SESSION['nommod']= $row->name;
		}
		
		$nf="lcs-".$_SESSION['nommod'];
		$fecran = cree_nom_fichier_ecran($nf);
                $MSG = "Désinstallation";
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
<SCRIPT TYPE="text/javascript">
	<!--
		self.scrollTo(0,1000000);
	//-->
</SCRIPT>
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
