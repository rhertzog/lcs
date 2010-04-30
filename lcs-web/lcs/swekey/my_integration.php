<?php

// include your comon files here
require "/var/www/lcs/includes/headerauth.inc.php";	

function swekey_localize($text)
{
	// OPTIONAL: Fill this function with the appropriate code if you need to localize your application
	$text=utf8_decode ( $text);
	return $text;
}


function IsAUserLogged()
{
	// REQUIRED: return true if a user is logged false otherwise
	list ($idprs,$login) = isauth();
   	if ($idprs=="0" || $idprs=="") return false;
   	else return true;
}

function LoggedUserSwekeyId()
{
	// REQUIRED: return the id of the swekey that is attached to the user that is logged
	// if the logged user has no swekey attached retrun '' 
	// You should have a request like 
	// "SELECT swekey_id from users where id=$logged_user_id";
	require "/var/www/lcs/includes/headerauth.inc.php";
	list ($idprs,$login) = isauth();
	$query = "SELECT id_swekey FROM swekey WHERE login='$login';";
    $result=mysql_query($query);
    if ($result && mysql_num_rows($result)) {
       $user=mysql_result($result,0);
       return $user;
    }
    else return '';
}


function SwekeyIntegrationScript()
{
	// REQUIRED: Comment this line after the first test
	$params = array();

	// include configuration file
	include_once 'my_swekey_config.php';
	$params['swekey_promo_url'] = $swekey_promo_url;	
	$params['brands'] = $swekey_brands; 		

	// REQUIRED: This is used to find the javascript files and the my-ajax.php file.
	// This value depend on the place you put the swekey directory
	// The path can be relative (swekey/) or absolute (http://wwww.mysite.com/swekey/)
	// Don't forget the trailing slash
	$params['swekey_url'] = '/swekey/';

	$isLogged = IsAUserLogged();
	if (empty($isLogged)) // not logged we must customize the login page
	{
		$params['user_logged'] = false;

	    // localized strings
	    $params['str_unplugged'] = swekey_localize('Pas de cl\351 d\351tect\351e');
	    $params['str_plugged'] = swekey_localize('Cl\351 connect\351e');
	
	    $params['loginname_path'] = '["login"]';
	    $params['loginname_resolve_url'] = $params['swekey_url'].'my_ajax.php?swekey_action=resolve&swekey_id=$swekey_id';
	    
	    // FINE-TUNING: use those two values to move the swekey logo that is next to the user_name Field  
	    $params['image_xoffset'] = '1px';
	    $params['image_yoffset'] = '-2px';

	    // FINE-TUNING: use this value if you want to reduce the width of the user_name Field  
	    //$params['loginname_width_offset'] = '10';

		// that is the authentication frame that is inserted in the login page		
	    $params['authframe_url'] = $params['swekey_url'].'authframe.php';
		$params['authframe_url'] .='?check_server='.urlencode($swekey_check_server);
		$params['authframe_url'] .= '&rndtoken_server='.urlencode($swekey_rndtoken_server);
		
		// OPTIONAL: If your application does tricky stuff about sessions like storing them in a DB just add the following line 
		// $params['authframe_url'] .= '&use_file=1';
	}
	else
	{
		$params['user_logged'] = true;
		$params['user_swekey_id'] = LoggedUserSwekeyId();
		if(! empty($params['user_swekey_id']))
		{
			// REQUIRED: Fill this value with an url that logs out the current used
			// This page will be loaded when the swekey will be unplugged. 	
			$params['logout_url'] = '/lcs/logout.php';
		}
		else
		{
			if ($swekey_user_managment)
			{
				$params['attach_url'] = $params['swekey_url'].'my_ajax.php?swekey_action=attach&swekey_id=$swekey_id';
				$params['session_id'] = session_id();

			    // localized strings
		        $params['str_attach_ask'] = swekey_localize("Une clé d'authentification swekey a été détectée.\\nVoulez-vous l\'associer à votre compte LCS ?");
		        $params['str_attach_success'] = swekey_localize('La clé swekey connectée est associée à votre compte');
		        $params['str_attach_failed'] = swekey_localize('Erreur dans l\'association de la swekey à votre compte');
			}				
		}
	}
	 
	include_once('swekey_integrate.inc.php');
	return Swekey_GetIntegrationScript($params);
}

?>
