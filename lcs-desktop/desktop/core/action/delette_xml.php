<?php

$user = $_POST['user'] ;
$file = $_POST['file'] ;	
$userrep = "/home/$user/Profile/";
if (!is_dir($userrep)) {
	$mess= utf8_decode("Erreur! le dossier \"/home/$user/Profile/\" n'existe pas");
	$img="alert";
	$title="Erreur !";
}else{
	$userfile = $userrep.$file.'.xml' ;
	if(!is_file($userfile)){
		$mess= utf8_decode('Le fichier '.$file .'.xml n&rsquo;existe pas');
		$img="alert";
		$title="Erreur !";
	}else{
		$command='rm '. $userfile;
		exec($command);
		$mess= utf8_decode('Le fichier '.$file .'.xml a été supprimé. Cliquez-moi pour actualiser votre bureau');
		$img="info";
		$title="Information";
	}
}
$resp='{mess:"'.$mess.'",img:"'.$img.'",title:"'.$title.'"}';
echo $resp;
?>