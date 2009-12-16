<?php require_once('../include/common.inc.php');  
	if ($_POST)
		extract($_POST);
	if ($_GET)
		extract($_GET);
	$json = file_get_contents('../templates/editTicket.tpl');
	$json = str_replace('%ROWID%',$ticket,$json);
	if (!eregi('clos',$statut))
		$json = str_replace('%TEXT_REP%','R&#233;pondre',$json);
	else
		$json = str_replace('%TEXT_REP%','R&#233ouvrir',$json);
	$json = str_replace('%TITRE%',utf8_decode($title),$json);
	$json = str_replace('%CATEGORIE%',utf8_decode($categorie),$json);
	$json = str_replace('%SUBMITTER%',utf8_decode($submitter),$json);
	$json = str_replace('%DESCRIPTION%',utf8_decode($description),$json);
	$json = str_replace('%USER%',utf8_decode(ucwords($array_user['prenom']).' '.strtoupper($array_user['nom'])),$json);
	die($json);
?>
