<html>
<?
include ("flag.php");
if (isset($verrou)){
	if ($verrou==1) {
		echo "<html>\n
		<head>\n
    		<meta HTTP-EQUIV='Content-Type' CONTENT='tetx/html; charset=utf-8'>\n
			<title>...::: Interface d\'administration Serveur LCS :::...</title>\n
			<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
		</head>\n
		<body>\n
			<h1>Gestion des Modules LCS</h1>\n
			<div class='alert_msg'>Un autre processus d'Installation/D&#233;sinstallation est en cours. Attendez qu'il se termine ... </div>\n
		</body>\n
	</html>\n";
	exit;
	}
}
?>

<frameset ROWS="0,*" BORDER="NO">
	<frame SRC="modules_installation.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
	<frame SRC="modules_refecran.php?<?php echo $_SERVER['QUERY_STRING']; ?>" NAME="ecran" ID="ecran">
</frameset>
</html>

