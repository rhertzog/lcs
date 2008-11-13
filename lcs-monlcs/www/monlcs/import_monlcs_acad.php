<?php

if ($_GET)  {
	extract($_GET);
	if (!isset($etab) || !isset($jeton))
		die('KO');
	
	$url = $etab."/monlcs/exportRess.php?jeton=".$jeton;
       	
	$ch = curl_init();
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_CRLF, true);
	$retourDistant = curl_exec($ch);
	curl_close($ch);
	
	//mouliner le retour
	if ($retourDistant) {
		//die(htmlspecialchars($retourDistant));
		$xml = simplexml_load_string($retourDistant);
		die($xml->asXML());
	} else {
	die('rien');
	}
 
}

?>
 
