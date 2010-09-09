<?php

	$user = $_POST['user'] ;
	$file = $_POST['file'] ;
	$data  = $_POST['data'] ;
	$groups  = $_POST['groups'] ;
	$ch="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n" ;
	$data = $ch.str_replace( "\'", '"', $data);

	$home = "/home/".$user;
	if (!is_dir($home)) {echo "Erreur! le dossier $user n'existe pas"; return;}
	$userprofil = $home."/Profile" ;
		if (!is_dir($userprofil)) {mkdir ($userprofil, 0770); }

		$userfile = $userprofil."/".$file.'.xml' ;
		$fp=fopen($userfile,'w');
		fwrite($fp,$data);
		fclose($fp);

	if(is_readable($userfile)){
		echo "Vos pr&eacute;f&eacute;rences sont enregistr&eacute;es";
		$stat = stat($userfile);
		if ($stat) {
			echo '<div style="text-align:left;">';
			echo 'Dossier : '.$userprofil."<br />";
			echo "Fichier : ".$file.'.xml'."<br />";
			echo "Cr&eacute;&eacute; le : ". @date('d M Y - H:i:s',$stat['ctime'])."<br />";
			$size=number_format(intval($stat['size'])/1000 , 2);
//			echo "Poids du fichier : ". $size." Ko<br />";
			echo "</div>";
			return;  
		}
	}else{
		echo "Erreur &agrave la cr&eacute;ation du fichier";
		return;  
	}
		
/*
foreach($stat as $k => $val){
	echo $k ." = ". $val."<br />";
}			
*/
	
?>