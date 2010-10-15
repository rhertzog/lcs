<?
include "includes/secure_no_header.inc.php";

if($_POST) {
extract($_POST);

	
	if ($url == '')
		die('URL VIDE!');	
	$sql4 =" INSERT INTO `monlcs_db`.`ml_rss` (
			`id` ,
			`url` ,
			`user` 
			)
			VALUES (
			NULL , '$url','$uid'	);";
	echo $sql4;
	$c4 = mysql_query($sql4) or die("ERR $sql");

}




?>


