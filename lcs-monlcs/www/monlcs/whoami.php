<?php

	include "includes/secure_no_header.inc.php";
	if ($ML_Adm == 'Y') {
			die(stringForJavascript("admin"));
 		} else { 
			if (is_administratif($uid))
				die(stringForJavascript("administratif"));
			if (is_eleve($uid))
				die(stringForJavascript("eleve"));
			else 
				die(stringForJavascript("prof"));
   		}

?>
