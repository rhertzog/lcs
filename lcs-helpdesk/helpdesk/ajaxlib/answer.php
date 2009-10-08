<?php require_once('../include/common.inc.php');  
	if ($_POST)
		extract($_POST);
	if ($_GET)
		extract($_GET);
	//$ticket = 1;
	$json = file_get_contents('../templates/answer.tpl');
	$json = str_replace('%ROWID%',$ticket,$json);
	die($json);
?>
