<?php

// $Id$

function Swekey_GetIntegrationScript($params)
{
	$javascript_included = false;

	$output = "\n\n<!-- Swekey Integration Begin -->\n";
	$output .= "<!-- PHP-Integration-Kit 1.0.1.4002 08/25/09 -->\n";

    if (empty($params['session_id']))
        $params['session_id'] = '9999';
        
        
 	if (!$params['user_logged'])
	{
		if (! empty($_COOKIE['swekey_disabled_id']))
		{
	        $output .= '<script type="text/javascript">'."\n"
	    		.'document.cookie = "swekey_disabled_id=; path=/;";'."\n" 
	            .'</script>'."\n";
		}	
	}

    // We are logged with a swekey
    if (isset($params['logout_url']) && $params['user_logged'] && mb_strlen($params['user_swekey_id']) == 32)
    {
		$disabled_swekey = '';
		if (empty($_COOKIE['swekey_disabled_id']))
		{
	    	include_once('swekey.php');
	        $status = Swekey_GetStatus($params['user_swekey_id']);
	        if ($status == SWEKEY_STATUS_INACTIVE || $status == SWEKEY_STATUS_LOST || $status == SWEKEY_STATUS_STOLEN)
	        {
		        $disabled_swekey = $params['user_swekey_id'];
		        $output .= '<script type="text/javascript">'."\n"
		    		.'document.cookie = "swekey_disabled_id='.$params['user_swekey_id'].'; path=/;";'."\n" 
		            .'</script>'."\n";
	        }
	        else
	        {
		        $output .= '<script type="text/javascript">'."\n"
		    		.'document.cookie = "swekey_disabled_id=none; path=/;";'."\n" 
		            .'</script>'."\n";
	        }
		}
		else
			$disabled_swekey = $_COOKIE['swekey_disabled_id'];	
		
		if ($disabled_swekey != $params['user_swekey_id'])    
		{    		
			if (! $javascript_included)	
			{
		    	$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey.js"></script>'."\n";
				$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey_integrate.js"></script>'."\n";
				$javascript_included = true;
			}
	        $output .= '<script type="text/javascript">'."\n"
	            .'swekey_logout_url = "'.$params['logout_url'].'";'."\n" 
	            .'swekey_to_check = "'.$params['user_swekey_id'].'";'."\n" 
	    		.'document.cookie = "swekey_proposed='.$params['session_id'].'; path=/;";'."\n"   // never propose
	     		.'setTimeout("check_swekey_presence()", 1000);'."\n"
	            .'</script>'."\n";
	    }
    }

    // We are logged but we don't use a swekey
    if (! empty($params['attach_url']) && $params['user_logged'] && empty($params['user_swekey_id']))
    {
		if (! $javascript_included)	
		{
	    	$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey.js"></script>'."\n";
			$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey_integrate.js"></script>'."\n";
			$javascript_included = true;
		}
        $output .= '<script type="text/javascript">'."\n";
        $output .= 'swekey_session_id = "'.$params['session_id'].'";'."\n";
        $output .= 'swekey_attach_url = "'.$params['attach_url'].'";'."\n";
        if (isset($params['brands']))
            $output .= 'swekey_brands = "'.$params['brands'].'";'."\n";
        if (isset($params['str_attach_ask']))
            $output .= 'swekey_str_attach_ask = "'.$params['str_attach_ask'].'";'."\n";
        if (isset($params['str_attach_success']))
            $output .= 'swekey_str_attach_success = "'.$params['str_attach_success'].'";'."\n";
        if (isset($params['str_attach_failed']))
            $output .= 'swekey_str_attach_failed = "'.$params['str_attach_failed'].'";'."\n";
        $output .= 'swekey_propose_to_attach();'."\n";
        $output .= '</script>'."\n";
    }    
    
    // We are not logged
    if (isset($params['loginname_path']))
    {					
		if (! $javascript_included)	
		{
	    	$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey.js"></script>'."\n";
			$output .= '<script type="text/javascript" src="'.$params['swekey_url'].'swekey_integrate.js"></script>'."\n";
			$javascript_included = true;
		}
        $output .= '<script type="text/javascript">'."\n";
        $output .= 'swekey_artwork_path = "'.$params['swekey_url'].'";'."\n";
        $output .= 'swekey_loginname_path = '.$params['loginname_path'].';'."\n";
        if (isset($params['mutltiple_loginnames_input']))
            $output .= 'swekey_mutltiple_loginnames_input = true;'."\n";
        if (! empty($params['swekey_promo_url']))
        {
     		if (mb_strpos($params['swekey_promo_url'], '://') === false)
 	            $output .= 'swekey_promo_url = "http://www.swekey.com?promo='.$params['swekey_promo_url'].'";'."\n";
            else
    	        $output .= 'swekey_promo_url = "'.$params['swekey_promo_url'].'";'."\n";
        }
        else if (! empty($params['promo']))
            $output .= 'swekey_promo_url = "http://www.swekey.com?promo='.$params['promo'].'";'."\n";
		else 
            $output .= 'swekey_promo_url = "http://www.swekey.com?promo=none";'."\n";
        if (isset($params['brands']))
            $output .= 'swekey_brands = "'.$params['brands'].'";'."\n";
        if (isset($params['loginname_resolve_url']))
            $output .= 'swekey_loginname_resolve_url = "'.$params['loginname_resolve_url'].'";'."\n";
       if (isset($params['authframe_url']))
            $output .= 'swekey_authframe_url = "'.$params['authframe_url'].'";'."\n";
        if (! empty($params['force_authframe_url']))
            $output .= 'swekey_force_authframe_url = true;'."\n";
        if (isset($params['show_unplugged']))
            $output .= 'swekey_show_unplugged = "'.$params['show_unplugged'].'";'."\n";
        if (isset($params['image_xoffset']))
            $output .= 'swekey_image_xoffset = "'.$params['image_xoffset'].'";'."\n";
        if (isset($params['image_yoffset']))
            $output .= 'swekey_image_yoffset = "'.$params['image_yoffset'].'";'."\n";
        if (isset($params['loginname_width_offset']))
            $output .= 'swekey_loginname_width_offset = "'.$params['loginname_width_offset'].'";'."\n";
        if (isset($params['str_unplugged']))
            $output .= 'swekey_str_unplugged = "'.$params['str_unplugged'].'";'."\n";
        if (isset($params['str_plugged']))
            $output .= 'swekey_str_plugged = "'.$params['str_plugged'].'";'."\n";

        $output .= 'swekey_login_integrate();'."\n";

        $output .= '</script>'."\n";
    }   

	$output .= "<!-- Swekey Integration End -->\n\n";
    
    return $output;
}
