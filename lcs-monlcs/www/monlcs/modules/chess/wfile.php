<?php

	$do1 = $_COOKIE["achess_do"];
	$id1 = $_COOKIE["achess_id"];
	
	$fn[0] = "sessions/" . $id1 . ".txt";
	$fn[1] = "sessions/" . $id1 . "_info.txt";
 
 
	if ( (($do1 == "create") || ($do1 == "join")) && ($id1 != "") ) {
		$i = $_REQUEST["f"];
		$s = $_REQUEST["s"];
		
		if ((($i == 0) || ($i == 1)) && ($s != "")) {
			echo "write string: " . $s . " to file " . $fn[$i];
			$handle = fopen ($fn[$i], 'w'); fwrite ($handle, $s); fclose($handle);
		}
	}
	
?>