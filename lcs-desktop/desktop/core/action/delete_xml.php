<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 14/03/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/

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