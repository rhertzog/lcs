<html>
<?
include ("flag.php");
if (isset($verrou)){
	if ($verrou==1) {
	echo "<html>
	<head>
    <meta HTTP-EQUIV='Content-Type' CONTENT='tetx/html; charset=utf-8'>
	<title>...::: Interface d\'administration Serveur LCS :::...</title>
	<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>
	</head>
	<body>
	<h1>Gestion des Modules LCS</h1>
	<div class='alert_msg'>Un autre processus d'Installation/D&#233;sinstallation est en cours. Attendez qu'il se termine ... </div>
	</body>
	</html>";
	exit;
	}
}
?>
<frameset ROWS="0,*" BORDER="NO">
	<frame SRC="modules_desinstallation.php?<?php echo $_SERVER['QUERY_STRING']; ?>">
	<frame SRC="modules_refecran.php?<?php echo $_SERVER['QUERY_STRING']; ?>" NAME="ecran" ID="ecran">
</frameset>
</html>
	
