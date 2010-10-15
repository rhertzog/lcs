<?
include "includes/secure_no_header.inc.php";
		
	$sql2 =" DELETE FROM `monlcs_db`.`ml_rss` WHERE user='$uid'";
	$c2 = mysql_query($sql2) or die("ERR $sql2");
	
?>


