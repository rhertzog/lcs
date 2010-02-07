<?
	include "includes/secure_no_header.inc.php";

	if ($_POST) {
		extract($_POST);
		
		//$titre = html_entity_decode($titre);
		$titre = htmlentities(utf8_decode(urldecode($titre)));
		$descr = htmlentities(utf8_decode(urldecode($descr)));
		$titre_ress = htmlentities(utf8_decode(urldecode($titre_ress)));
		//alimenter ml_scenarios
		//rechercher idR ditinguer MODULE - NOTE - RETROUVER ID sinon créer la ressource a partir de $content.
		$idR = -1;
		if (trim($type) =='ressource') {

			//if (eregi('/monlcs/modules',$content)) {
        	               				
				//die($rep);
				//dans le cas d'un module on sort
			//}
				
                       $sql = "select * from monlcs_db.ml_ressources where url = '".htmlentities($content)."'";
                       $c = mysql_query($sql) or die ("ERR ". $sql);
 	               if (mysql_num_rows($c) != 0) {
        	              	$res = mysql_fetch_object($c);
				$idR = $res->id;
				
                       } else {
        	                $idR = '';
				$date = date('Y-m-d');
				$sqlR ="INSERT INTO `monlcs_db`.`ml_ressources` ("
				."`id` ,"
				."`titre` ,"
				."`url` ,"
				."`RSS_template` ,"
				."`owner` ,"
				."`statut` ,"
				."`ajoutee_le` ,"
				."`url_vignette` ,"
				."`descr`"
				.")"
				." VALUES ("
				."'$idR' , '$titre_ress', '$content', 'null', '$uid', 'private', '$date', '$vignette' ,'$descrAdd'"
				.");";
				//die($sqlR);
				$cR=mysql_query($sqlR) or die("ERR $sqlR");
				
				$sqlId ="SELECT * FROM `monlcs_db`.`ml_ressources` WHERE titre='$titre_ress' and owner ='$uid' LIMIT 1;";
				$cId = mysql_query($sqlId) or die ("ERR ". $sqlId);
				$resId = mysql_fetch_object($cId);
				$idR = $resId->id;

			}
		
		}
		
		if (trim($type) =='note') {
			 $sqlN = "select id from monlcs_db.ml_notes ORDER bY id DESC LIMIT 1;";
			 $cN = mysql_query($sqlN) or die ("ERR ". $sqlN);
			 $res = mysql_fetch_object($cN);
			 $maxN = trim($res->id);
			 $idR =$maxN+1.0;
			 
			 $sql4 =" INSERT INTO `monlcs_db`.`ml_notes` (
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
				'$idR' , 'scenario_choix','$uid',NULL ,'".utf8_decode(urldecode($titre_ress))."', '$x','$y','$z','$w','$h','".utf8_decode(urldecode($content))."','N');";
			$c = mysql_query($sql4) or die ("ERR ". $sql4);	
		}
		
		
		
		$cible[0]='';
		$cible[strlen($cible)-1]='';
		$arr_cible = explode('#',$cible);
		
		
		$sq ="select * from monlcs_db.ml_scenarios where id_ressource='$idR' and id_scen='$id_scen' and setter='$uid';";
		$c = mysql_query($sq) or die("ERR $sq");
		if (mysql_num_rows($c) == 0  && ($idR != -1) ) {
			foreach($arr_cible as $cible) {
				$cible = trim($cible);
				if ($cible != '') {
					$sqlX =" INSERT INTO `monlcs_db`.`ml_scenarios` (
					`id` ,
					`id_scen` ,
					`setter` ,
					`titre` ,
					`id_ressource` ,
					`cible` ,
					`type` ,
					`matiere` ,
					`x` ,
					`y` ,
					`z` ,
					`w` ,
					`h`,
					`min`,
					`descr`
					)
					VALUES (
					NULL ,'$id_scen','$uid','$titre', '$idR', '$cible','$type', '$matiere','$x','$y','$z','$w','$h','$min','$descr'
					);";

				$cIns = mysql_query($sqlX) or die("ERR $sqlX");
				//$msg .= " <BR />Success $sqlX";
				}
			}//foreach
		}//if
		
		//die($sqlX);
		
	}//if post


?>


