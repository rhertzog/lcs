<?php
/* lcs/includes/user_lcs.inc.php */

include ("/var/www/lcs/includes/config.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");

function auth_lcs()
{
	/* Test si une authentification est faite
		- Si oui, renvoie le login
	*/

	global $authlink,$DBAUTH;

	$ret= "";
	if (!empty($_COOKIE["LCSAuth"])) {
		$sess=$_COOKIE["LCSAuth"];
		$query="SELECT  idpers, remote_ip FROM sessions WHERE sess='$sess'";

		$result=@mysql_query($query, $authlink);
		if ($result && @mysql_num_rows($result) ) {
			$idpers = @mysql_result($result,0,0);
			list($ip_session,$null) = preg_split("/,/",mysql_result($result,0,1),2);
			list($first_remote_ip,$null) = preg_split("/,/",remote_ip(),2);
			@mysql_free_result($result);
			if ( $ip_session == $first_remote_ip ) {
				# Recherche le login dans la base personne  et  interrogation de l'annuaire
				$query="SELECT login FROM personne WHERE id=$idpers";
				$result=@mysql_query($query, $authlink);
				if ($result && @mysql_num_rows($result)) {
					$login=@mysql_result($result,0,0);
					@mysql_free_result($result);
					$ret=$login;
				}
			}
		}
		return  $ret;
	}
}

?>


