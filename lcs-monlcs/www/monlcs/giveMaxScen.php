<?
include "includes/secure_no_header.inc.php";


$sql = "select * from monlcs_db.ml_scenarios order BY id_scen DESC;";
$c = mysql_query($sql) or die ("ERR $sql");
if (mysql_num_rows($c) != 0)
	print(mysql_result($c,0,'id_scen'));
else
	print('0');

?>