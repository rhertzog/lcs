<?php
	include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "../Includes/config.inc.php";
	include "../Includes/func_maint.inc.php";

	// Register Global POST
	$sqlfile=$_FILES['sqlfile']['name'];
	// RESTAURATION BASE
	if (isset ($sqlfile)) {
		$resp = "";
		// upload du fichier
		$tmpsqlfile = $_FILES["sqlfile"]["tmp_name"];
		system ("/usr/bin/mysql -u $USERAUTH -p$PASSAUTH $DBAUTHMAINT < $tmpsqlfile", $ret);
		if ($ret==-1 )
			echo $ret." Echec de la restauration";
		else 
			echo $ret." Le fichier ".$sqlfile." a été restauré." ;
	}                                       

?>