<?php

$user = $_POST['user'] ;
$file = $_POST['file'] ;	
$ou = $_POST['ou'];

$koa=explode("_", $file);
// tous les fichiers icone dans desktop/core/data appartenant au user
if( $ou=='all' ) {
	$del_f= $file.".json";
	$mess= "le fichier est : ".$del_f;
	$img="info";
	$title="YEP !";
	$cmd='for i in /usr/share/lcs/desktop/core/data/*; do rm  $i/'.$del_f.'; done';
	$mess.= " / ".$koa[0]." | ".$koa[1]." | ".$koa[2]." | ".$koa[3]." | ".$cmd;
	exec($cmd);
	//foreach($res as $val){
	//$mess.= "<br>".$val." :: ";
	//}
}
else{
	$userrep = "/home/$user/Profile/";
	if (!is_dir($userrep)) {
		$mess= utf8_decode("Erreur! le dossier \"/home/$user/Profile/\" n'existe pas");
		$img="alert";
		$title="Erreur !";
	}else{
		$userfile = $userrep.$file.'.json' ;
		if(!is_file($userfile)){
			$mess= utf8_decode('Le fichier '.$file .'.json n&rsquo;existe pas');
			$img="alert";
			$title="Erreur !";
		}else{
			$command='rm '. $userfile;
			exec($command);
			$mess= utf8_decode('Le fichier '.$file .'.json a été supprimé. Cliquez-moi pour actualiser votre bureau');
			$img="info";
			$title="Information";
		}
	}
}
$resp=array(
"mess" => $mess,
"img" => $img,
"title" => $title
);
echo json_encode($resp);
?>