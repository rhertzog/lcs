<?php

	$user = $_POST['user'] ;
	$file = $_POST['file'] ;
	$data  = $_POST['data'] ;
	$groups  = $_POST['groups'] ;
	$ch="<?xml version='1.0' encoding='utf-8'?>\r\n" ;
	$data = $ch.str_replace( "\'", '"', $data);
	
	$dir = "/var/www/lcs/desktop/xml/";
	if (!is_dir($dir)) {
		echo "Erreur! le dossier xml n'existe pas"; return;
	}else{
		if($groups!=''){
			$classes = explode(",", $groups);
			foreach($classes as $classe){

				$userrep = $dir."/".$classe."/" ;
				if (!is_dir($userrep)) {mkdir ($dir."/".$classe, 0755); }
				$classefile = $dir."/".$classe."/".$file.'_'.$classe.'.xml' ;
				$fp=fopen($classefile,'w');
				fwrite($fp,$data);
				fclose($fp);

			}

		}else{
			$userrep = $dir."/".$user."/" ;
			if (!is_dir($userrep)) {mkdir ($dir."/".$user, 0755); }
			$userfile = $dir."/".$user."/".$file.'.xml' ;
			$fp=fopen($userfile,'w');
			fwrite($fp,$data);
			fclose($fp);
		}

		
		echo $file;
		return;  
    }
	
?>