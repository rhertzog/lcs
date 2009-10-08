<?php
require_once("./include/common.inc.php");

$cmd = "/usr/sbin/gestEtabMonitor --xml ";
//$cmd = "/usr/bin/sudo -H -u root /usr/sbin/gestEtabMonitor --xml";
$ret = exec($cmd,$retour,$val);
if ($val == 0) {
	$lignes = array_reverse(explode("\n\r\n",implode("\n",$retour)));

	//header('application/xml');
	echo  htmlentities($lignes[0]);
} else die($val);

?>
