<?php
include "includes/secure_no_header.inc.php";

if ($_POST) {
extract($_POST);

$ref=substr($ref,8);
if (eregi('Cmd',$ref)) {//if
	die(stringForJavascript('Pas de update pour les cmd ;)'));
	}
 
}//GET
print(stringForJavascript('ok'));		



?>
