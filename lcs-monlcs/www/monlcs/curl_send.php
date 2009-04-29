<?php
    //script qui lance un script sur un autre domaine a partir du serveur
	//require_once("domxml-php4-php5.php");
		
	if ($_GET)
		extract($_GET);
	
	if ($_POST)
		extract($_POST);
	
	//retrouver le contenu d'une URI distante
	if (!isset($url))
		$url ='http://www.google.fr';
	
	if ($complement) {
		$url = urldecode($url).'?'.urlencode(utf8_decode($complement));
		$url=str_replace('%3D','=',$url);
		$url=str_replace('%26','&',$url);
		//die($url);
		//if (eregi('type_etab',$complement))
		//	die(htmlentities($url));
	}
	
	$ch = curl_init($url);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch, CURLOPT_CRLF, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$r['code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $r['cr'] = curl_exec($ch);
        $r['ce'] = curl_errno($ch);
	$retourDistant = utf8_decode(curl_exec($ch));
	//die(print_r($r));
	curl_close($ch);
	
	die($retourDistant);

	
?>
