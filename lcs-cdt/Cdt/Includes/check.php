<?php
Function check() {
$ticket=true;
if (count($_POST)>0){
	if (md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])) != $_POST['TA']) $ticket=false;
	}
if (isset($GET['TA'])) { 
	if ($GET['TA'] != md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])) )	$ticket=false;	
	}
return $ticket;
}
?>