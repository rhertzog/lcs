<?
include "includes/secure_no_header.inc.php";


$sql = "select * from monlcs_db.ml_notes order BY id DESC;";
$c = mysql_query($sql) or die ("ERR $sql");
if (mysql_num_rows($c) != 0)
	print(mysql_result($c,0,'id'));
else
	print('0');

?>