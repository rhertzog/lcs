<?php

function UserSwekeyId($user_name)
{
	// REQUIRED: return the id of the swekey that is attached to the user '$user_name'
	// If the user has no swekey attached retrun '' 
	// You should have a request like 
	// "SELECT swekey_id from users where username=$user_name";
	require "/var/www/lcs/includes/headerauth.inc.php";
	$query = "SELECT id_swekey FROM swekey WHERE login='$user_name';";
            $result=mysql_query($query);
            if ($result && mysql_num_rows($result)) {
               $user=mysql_result($result,0);
               return $user;
               }
            else return '';
	}


// This function returns false if the user 
function SwekeyLoginVerify($user_name)
{
	$swekey_id = UserSwekeyId($user_name);
	if(! empty($swekey_id))
	{
		if (ereg('^[A-F0-9]{32}$',$swekey_id)) 
	    {
	    	require_once 'my_swekey_config.php';
	    	global $gSwekeyStatusServer;
			$gSwekeyStatusServer = $swekey_status_server;
		   	include_once('authframeres.inc.php');

	    	if (! IsSwekeyAuthenticated($swekey_id, $swekey_allow_disabled))
	    	{
	    		// OPTIONAL: Output a message like 'Swekey $swekey_id is required to login...'; 
	    		return false;
	    	}
		}	
	}	
	return true;
}

?>
