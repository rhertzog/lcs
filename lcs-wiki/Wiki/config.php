<?php

include("/var/www/lcs/includes/config.inc.php");

$link = @mysql_connect($HOSTAUTH,$USERAUTH,$PASSAUTH);

if ($link) {
	if (!@mysql_select_db($DBAUTH, $link)){
		mysql_close($link);
		$link = false;
	}
	else{
		$res = mysql_query("SELECT value from params where name='baseurl'",$link);
		
		if ( mysql_num_rows($res) != 0 ) {
			while ($row = mysql_fetch_assoc($res)){
				$baseurl = $row['value'];
			}
		}
	}
}

?>
