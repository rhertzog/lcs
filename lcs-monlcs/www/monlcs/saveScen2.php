<?
	include "includes/secure_no_header.inc.php";
	if($_POST) {
		extract($_POST);

		if ($id_scen == -1)
			die("Erreur d'identifiant!");
		


		$idR = substr($idR,8);

		if (eregi('Cmd',$idR)) {
			die('Cmd pas géré ici !');
			$idR = substr($idR,4);
		}

		if (eregi('Note',$idR)) {
			$type = 'note';
			$idR = substr($idR,4);
		} else {
			$type ='ressource';
		}	
	
		//alimenter ml_scenarios

		$cibles =array();	
   		//retrouver les cibles de l'activité
		$sq ="select * from monlcs_db.ml_scenarios where id_scen ='$id_scen';";
		$c = mysql_query($sq) or die("ERR $sq");
		for($xx=0;$xx< mysql_num_rows($c);$xx++) {
				$R = mysql_fetch_object($c);
				$titre = $R->titre;
				$matiere = $R->matiere;
				if (!in_array($R->cible,$cibles))
					$cibles[] = $R->cible;		
		}
		
		if (count($cibles) == 0)
			die('Aucune cible');	
			
		$sq ="select * from monlcs_db.ml_scenarios where id_ressource='$idR' and id_scen='$id_scen' and setter='$uid' and type='$type';";
		$c = mysql_query($sq) or die("ERR $sq");
		
		if (mysql_num_rows($c) !=0 ) {
					
			for($xx=0;$xx< mysql_num_rows($c);$xx++) {
				$R = mysql_fetch_object($c);
				
				if ($vis == 'block') {
					
					if ($min == 'Y')
						$sqUp = "UPDATE monlcs_db.ml_scenarios SET x='$x',y='$y',z='0',min='$min' WHERE id='$R->id';";
					else
						$sqUp = "UPDATE monlcs_db.ml_scenarios SET x='$x',y='$y',z='$z',w='$w',h='$h',min='$min' WHERE id='$R->id';";
					$cUp = mysql_query($sqUp) or die("ERR $sqUp");
					echo "<BR />".$sqUp;
				} 
				 if ($vis == 'none') {
					$sqlDel = "DELETE from monlcs_db.ml_scenarios WHERE id='$R->id' and setter='$uid' LIMIT 1;";
					$cDel = mysql_query($sqlDel) or die("ERR $sqlDel");
					echo "<BR />".$sqlDel;

				}
			}
		} else {
		
		for ($yy = 0 ; $yy < count($cibles) ;$yy++) {
		   if ($vis == 'block') {
			$sql =" INSERT INTO `monlcs_db`.`ml_scenarios` (
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
			`min`
			)
			VALUES (
			NULL , '$id_scen', '$uid','$titre', '$idR', '$cibles[$yy]', '$type','$matiere','$x','$y','$z','$w','$h','$min'
			);";
			
			$cIns = mysql_query($sql) or die("ERR $sql");
			echo $sql;
		   }

		}//for
		}//else
			
	}//if post

?>


