<?
	include "includes/secure_no_header.inc.php";

	function EcrireFichier($nom, $titre, $buffer) {
		global $uid ,$baseurl;
		$nom = noaccent($nom);
		$nf = "/home/".$uid."/public_html/Documents/file/".$nom;
		$fp = @fopen( $nf, "w");
           
        	$data = "<html><head><title>$titre</title></head><body>\n";
        	$data .= $buffer;
        	$data.= "\n";
        	$data.= "</body></html>";
        	$desc = @fwrite($fp, $data);
        	@fclose($fp);

	        //rechercher si ress dejala sinon l'inserer dans le pot de ressources en private. :)
		$base = $baseurl."~".$uid."/Documents/file/";
		$url = $base.$nom;
	
		$sql = "select * from monlcs_db.ml_ressources where url ='$url'";
		$c = mysql_query($sql) or die("Requete $sql invalide!");
		if ( mysql_num_rows($c) == 0) {
			//inserer la ressource
			$date = date('Y-m-d');
		       $gestRSS='null';
			$stat = 'private';
			if (!eregi('Note_',$titre))
				$titre = "Note_".$uid."_".$titre;
			$sql ="REPLACE INTO `monlcs_db`.`ml_ressources` ("
			."`id` ,"
			."`titre` ,"
			."`url` ,"
			."`RSS_template` ,"
			."`owner` ,"
			."`statut` ,"
			."`ajoutee_le` ,"
			."`url_vignette` "
			.")"
			." VALUES ("
			." NULL , '$titre', '$url', '$gestRSS', '$uid', '$stat', '$date', NULL "
			.");";

			$c=mysql_query($sql) or die("ERR $sql");
		}


	}

	
		if ($_POST) {
			extract($_POST);
			$Filter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
			$msg = cleanaccent($Filter->process($msg));
			if ($save_html == 'Y') {
				//Sauver la note en html dans /home/admin/public_html/Documents/
				$nf = $note."_".$titre.".html";
				EcrireFichier($nf, stripcslashes($titre), stripcslashes($msg));
			}	

			$sql2 =" SELECT * FROM `monlcs_db`.`ml_notes` WHERE menu='$id' and setter='$uid' and id='$note' ";
			$c2 = mysql_query($sql2) or die("ERR $sql2");
	
	
			if ( mysql_num_rows($c2) != 0 ) {
			//update
				$sql3 =" UPDATE `monlcs_db`.`ml_notes` SET msg='$msg', titre='$titre', x='$x', y='$y', z='$z', w='$w', h='$h', min='$min'  WHERE menu='$id' and setter='$uid' and id='$note' ";
				$c3 = mysql_query($sql3) or die("ERR $sql3");
				echo $sql3;
			} else {
				$sql4 =" REPLACE INTO `monlcs_db`.`ml_notes` (
				`id` ,
				`menu` ,
				`setter` ,
		  		`cible` ,
       	              `titre` ,
				`x` ,
				`y` ,
				`z` ,
				`w` ,
				`h` ,
				`msg` ,
				`min` 
				) VALUES (
				'$note' , '$id','$uid','' ,'$titre', '$x','$y','$z','$w','$h','$msg','$min'
				);";
				echo $sql4;
				$c4 = mysql_query($sql4) or die("ERR $sql");
			}
		}
?>


