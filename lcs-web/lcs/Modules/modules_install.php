<HTML>
<?
include ("flag.php");
if (isset($verrou)){
	if ($verrou==1) {
	echo "<HTML>
	<HEAD>
    <META HTTP-EQUIV='Content-Type' CONTENT='tetx/html; charset=ISO-8859-1'>
	<TITLE>...::: Interface d\'administration Serveur LCS :::...</TITLE>
	<LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>
	</HEAD>
	<BODY>
	<H1>Gestion des Modules LCS</H1>
	<div class='alert_msg'>Un autre processus d'Installation/Désinstallation est en cours. Attendez qu'il se termine ... </div>
	</BODY></HTML>";exit;}
	}
?>

<FRAMESET ROWS="0,*" BORDER="NO">
	<FRAME SRC="modules_installation.php?<?php echo $QUERY_STRING; ?>">
	<FRAME SRC="modules_refecran.php?<?php echo $QUERY_STRING; ?>" NAME="ecran" ID="ecran">
</FRAMESET>
</HTML>

