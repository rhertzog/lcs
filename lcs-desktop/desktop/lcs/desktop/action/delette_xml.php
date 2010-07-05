<?php

$user = $_POST['user'] ;
$file = $_POST['file'] ;
$data  = $_POST['data'] ;
	
$dir = "/var/www/lcs/desktop/xml/";
if (!is_dir($dir)) {
	echo "Erreur! le dossier xml n'existe pas";
}else{
	$userrep = $dir.$user."/" ;
	if (!is_dir($userrep)){
		echo "Erreur! le dossier xml n'existe pas"; 
	}else{
//		echo 'Euhhh... \n';
		$userfile = $userrep.$file.'.xml' ;
		$command='rm '. $userfile;
		exec($command);
		echo 'Oueeee ! '.$file .' est jarT';
	}
}

?>