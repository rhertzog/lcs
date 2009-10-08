<?php

//Script de gestion HelpDesk
require_once('./include/common.inc.php');

//if ($_POST['xhr']) {
	
	if (!$HD->authenticate()) {
		//die(var_dump($HD));
		if (eregi('[Reseau]',$HD->getMessage()))
			$json = file_get_contents('./templates/network_error.tpl');
		else	
			$json = file_get_contents('./templates/auth_error.tpl');
	}
	else {

		$json = file_get_contents('./templates/window1.tpl');
		
		$rep = $HD->getResponse();
		//die($rep);
		@$xml  = new SimpleXMLElement($rep);
                $xml_domaine =  (string) $xml->domaine;
                //die("$xml_domaine ? $domain");
		if (trim($domain) != trim($xml_domaine)) {
			$HD->setMessage("[Erreur] D&eacute;sol&eacute; votre jeton est valide mais le domaine associ&eacute; est erronn&eacute;");
			$json = file_get_contents('./templates/network_error.tpl');
		} else {
		
			if ($xml_domaine)		
				$json .= "var domaine='$xml_domaine';";
			else	
				$json .= "var domaine=null;";

			if (eregi('mustRegister="1"',$rep)) 
				$json .= 'var mustRegister=1;';
			elseif (eregi('mustRegister="2"',$rep)) 
				$json .= 'var mustRegister=2;';
			else
				$json .= 'var mustRegister=0;';

		}
	}
	
	$json = str_replace("%MSG%",$HD->getMessage(),$json);
	die($json);

//}

?>
