<?php
	session_start();
	
	if ($_POST) {
		
		require_once("/var/www/lcs/includes/headerauth.inc.php");
		require_once("/var/www/Annu/includes/ldap.inc.php");
		require_once("/var/www/Annu/includes/ihm.inc.php");
		require_once("/var/www/lcs/includes/jlcipher.inc.php");
	
	list ($idpers, $login)= isauth();
		
	
	if (is_admin('Lcs_is_admin', $login) != "Y")
		die('Pb de droits');
	if ($login != 'admin')
		die('Seulement pour admin!');
	if ($_POST['login'] == '-')
		die('Mauvais user!');
	
	$info = explode('@',$_POST['login']);
		
	$sess_user = base64_encode($info[0]);
	$_SESSION['userHD'] = $sess_user;
	
	die('SUCCESS');
	
	}
	
?>
