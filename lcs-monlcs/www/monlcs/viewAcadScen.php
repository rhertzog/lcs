<? 
	session_start();
	header("text/javascript");
	include "includes/secure_no_header.inc.php";
	$content = "";

	if ($_POST) 
		extract($_POST);
	if ($_GET) 
		extract($_GET);
	
	$_SESSION['tokenImport'] = $jeton;
	$url = "$dir_url_distant/cache_xml/$jeton.xml";
	
	
	$ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $retourDistant = curl_exec($ch);
        curl_close($ch);

        $content .= "alert('$retourDistant');";
	if (!eregi('<response>',$content))
		$content = "Erreur: import impossible.";	
	else {
	
	// TODO mouliner le xml
	// extraire les propriètés du xml dans div_acad_import
	// a partir de la afficher les ressources dans des fenêtres dhtmlwindow
	// offrir la commande pour importer les ressources et sauvegarder le scenario
	// ? Placer la commande import académique dans le menu contextuel
	
	$xml = simplexml_load_string($retourDistant);
		
		foreach ( $xml->xpath('/response/*') as $item) {
			$cle = $item->getName();
			$val = utf8_decode(strip_tags($item->asXML())); 
			
			if ($cle != 'ressource') 
				$retour .= "<div class=info id=scenario_$cle>".utf8_decode($val)."</div>";
			else {
				$compte++;
				$retour.= "<div id=ress$compte class=ressource>";
				$entry = simplexml_load_string($item->asXML());
				foreach ( $entry->xpath('/ressource/*') as $entry_item) {
					$cle2 = $entry_item->getName();
					if ($cle2 == 'url')
						$val2 = $entry_item->asXML();
					else
					        $val2 = utf8_decode($entry_item->asXML());
					$arr_ress[][$cle2]=$val2;
					$retour .= "<div class=info2 id=ress".$compte."_".$cle2.">".utf8_decode(strip_tags($entry_item))."</div>";
				}
				$retour.="</div>";
			}
		}
	$retour.= "<div id=ress_number class=ress_number>$compte</div>";
		
	$content = "$retour";	
	

	}
	//die(var_dump($_SESSION));
	print(stringForJavascript($content));
	
?>
