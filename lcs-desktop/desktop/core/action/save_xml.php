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
	$data  = $_POST['data'] ;
	//$groups  = $_POST['groups'] ;
	$ch="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n" ;
	$data = $ch.str_replace( "\'", '"', $data);

	$home = "/home/".$user;
	if (!is_dir($home)) {
		$mess= utf8_decode("Erreur! le dossier \"$home\" n'existe pas");
		$img="alert";
		$title="Erreur !";
		$infos="";
	}
	$userprofil = $home."/Profile" ;
	$userfile = $userprofil."/".$file.'.xml' ;
	//$command=" do $userfile; chown www-data:lcs-users $userfile;  chmod 750 $userfile; done";
	//exec($command);
/*	if(!is_file($userfile){
		$mess= utf8_decode("Erreur! le dossier \"$home\" n'existe pas");
		$img="alert";
		$title="Erreur !";
	}else{
*/
		$fp=fopen($userfile,'w');
		fwrite($fp,$data);
		fclose($fp);
//	}
	if(is_readable($userfile)){
		$mess= utf8_decode('Vos pr&eacute;f&eacute;rences ont &eacute;t&eacute; enregistr&eacute;es');
		$img="info";
		$title="Information";
		//echo "Vos pr&eacute;f&eacute;rences sont enregistr&eacute;es";
		$stat = stat($userfile);
		if ($stat) {
			$infos= '<div style=\"text-align:left;\">';
			$infos.= 'Dossier : '.$userprofil."<br />";
			$infos.="Fichier : ".$file.'.xml'."<br />";
			$infos.="Cr&eacute;&eacute; le : ". @date('d M Y - H:i:s',$stat['ctime'])."<br />";
			$size=number_format(intval($stat['size'])/1000 , 2);
//			echo "Poids du fichier : ". $size." Ko<br />";
			$infos.="</div>";
		}
	}else{
		echo "Erreur &agrave la cr&eacute;ation du fichier";
		$mess= utf8_decode("Erreur! le dossier \"$home\" n'existe pas");
		$img="alert";
		$title="Erreur !";
		$infos="";
	}
$resp='{mess:"'.$mess.'",img:"'.$img.'",title:"'.$title.'",infos:"'.$infos.'"}';
echo $resp;
?>