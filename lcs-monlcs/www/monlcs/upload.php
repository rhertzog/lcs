<?php

 	include "includes/secure_no_header.inc.php";
 	

	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		echo "There was a problem with the upload";
		exit(0);
	} else {
		
		//deplacer le fichier ici
                $upload_dir = "/home/".$uid."/public_html";
		$filename = trim($_FILES['Filedata']['name']); // file name
		$filename = str_replace(' ','_',$filename);
		$ext = array_reverse(explode('.',$filename));
		$extension = trim($ext[0]);
		$filebody = implode('.',array_reverse(array_slice($ext,1)));

		if (!in_array($extension,array('flv','pdf','ggb','swf','mm','pr')))
			die ("Erreur d'extension de fichier !");
		
		if (!is_dir($upload_dir.'/monlcs_'.$extension.'/')) {
			mkdir($upload_dir.'/monlcs_'.$extension,0770);
		}
	
		if ( ($extension == 'pdf') && !is_dir($upload_dir.'/monlcs_swf') )
				mkdir($upload_dir.'/monlcs_swf',0770);
	
		move_uploaded_file($_FILES['Filedata']['tmp_name'], $upload_dir.'/monlcs_'.$extension.'/'.$filename);
 		
		if (preg_match('#\.[pdf]+$#is',$filename)) {
			$viewer = '/var/www/monlcs/modules/rfxview.swf';
			$src = $upload_dir.'/monlcs_'.$extension.'/'.$filename;
			$dest = $upload_dir.'/monlcs_swf/'.$filebody.'_pdf.swf';
			exec("pdf2swf -z -S -w \"$src\" -o \"$dest\" ");
			exec("swfcombine -z $viewer viewport=".'"'.$dest.'"'." -o \"$dest\" ");	

		}

		$result = 'OK';
		echo '<html><head><title>-</title></head><body>';
    		echo '<script language="JavaScript" type="text/javascript">'."\n";
    		echo 'var parDoc = window.parent.document;';

    		if ($result == 'OK')
    		{
        		echo 'parDoc.getElementById("filename").value = "'.$filename.'";';
		}
 		echo "\n".'</script></body></html>';
		exit(); // do not go futher

	}
?>
