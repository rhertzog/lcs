<?
	include "includes/secure_no_header.inc.php";

	if($_POST) {
		extract($_POST);
		$titre = htmlspecialchars(urldecode($titre));
		

		if (eregi('Cmd',$idR)) {
			die('Cmd pas géré ici !');
			$idR = substr($idR,4);
		}


		//alimenter ml_scenarios
		$idR = substr($idR,8);
		
		if (eregi('Note',$idR)) {
			$type = 'note';
			$idR = substr($idR,4);
		} else {
			$type ='ressource';
		}	
	
		
		$cible[0]='';
		$cible[strlen($cible)-1]='';
		$arr_cible = explode('#',$cible);
		
		
		$sq ="select * from monlcs_db.ml_scenarios where id_ressource='$idR' and id_scen='$id_scen' and setter='$uid';";
		$c = mysql_query($sq) or die("ERR $sq");
		if (mysql_num_rows($c) == 0 ) {
			foreach($arr_cible as $cible) {
				$cible = trim($cible);
				if ($cible != '') {
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
					`min`,
					`descr`
					)
					VALUES (
					NULL ,'$id_scen','$uid','$titre', '$idR', '$cible','$type', '$matiere','$x','$y','$z','$w','$h','$min','$descr'
					);";

				$cIns = mysql_query($sql) or die("ERR $sql");
				$msg .= " <BR />Success $sql";
				}
			}//foreach
		}//if
		die($msg);
	}//if post


?>


