<?php
	$id1 = $_COOKIE["achess_id"];
	if (trim($id1) != "") {
		$fn = $id1 . "_chat.txt";
	} else {
		$fn = "chat.txt";
	}
	
	$msg = $_REQUEST["m"];

	$spam[] = "wojo";
	$spam[] = "nigger";
	$spam[] = "A P P L E";
	$spam[] = "w o j o";
	$spam[] = "cum";
	$spam[] = "dick";
	$spam[] = "EAT coon";

	$blockip[] = "72.60.167.89";
	
	$espam[] = "ajax";
	
	if ($msg != "")  {
//		$msg.=$_SERVER["REMOTE_ADDR"];
		if (strlen($msg) < 2) { die(); }
		if (strlen($msg) > 3) { 
			/* Smilies are ok */
			if (strtoupper($msg) == $msg) { die(); }
		}
		if (strlen($msg) > 250) { die(); }
		if (strlen($msg) > 15) { 
			if (substr_count($msg, substr($msg, 6, 8)) > 1) { die(); }
		}

		foreach ($blockip as $a) {
			if ($_SERVER["REMOTE_ADDR"] == $a) { die(); }
		}
		
		$mystring = strtoupper($msg);
		foreach ($spam as $a) {	
			 if (strpos($mystring, strtoupper($a)) === false) {
			 	/* Everything Ok Here */
			 } else {
			 	die();
			 }
		}		

		foreach ($espam as $a) {
			if (strtoupper($msg) == strtoupper($a)) { die(); }		
		}
		
		$maxlines = 20;
		
		$handle = fopen ($fn, 'r'); 
		$chattext = fread($handle, filesize($fn)); fclose($handle);
		
		$arr1 = explode("\n", $chattext);

		if (count($arr1) > $maxlines) {
			/* Pruning */
			$arr1 = array_reverse($arr1);
			for ($i=0; $i<$maxlines; $i++) { $arr2[$i] = $arr1[$i]; }
			$arr2 = array_reverse($arr2);			
		} else {
			$arr2 = $arr1;
		}
		

		$chattext = implode("\n", $arr2);

		if (substr_count($chattext, $msg) > 2) { die(); }

		$out = $chattext . $msg . "\n";
		$out = str_replace("\'", "'", $out);
		$out = str_replace("\\\"", "\"", $out);
		
		$handle = fopen ($fn, 'w'); fwrite ($handle, $out); fclose($handle);				
	}
?>
