<?php

// $Id$


function IsSwekeyAuthenticated($swekey_id, $allow_disabled)
{
	// delete the cookie
	@setcookie('swekey_dont_verify_'.$swekey_id, "0", time()-60000);

    $ids = GetAuthFrameRes();
    if (is_array($ids) && in_array($swekey_id, $ids))
    {
	    if ($allow_disabled)
    	    @setcookie('swekey_disabled_id', 'none', 0, '/');
        return true;
    }
	   
    if ($allow_disabled)
    {
    	include_once('swekey.php');
        $status = Swekey_GetStatus($swekey_id);
        if ($status == SWEKEY_STATUS_INACTIVE || $status == SWEKEY_STATUS_LOST || $status == SWEKEY_STATUS_STOLEN)
        {
        	@setcookie('swekey_disabled_id', $swekey_id, 0, '/');
            return true;
        }
    }

    return false;
}

function FilePath($id)
{
	$sp = session_save_path();

    if (empty($sp) && function_exists('sys_get_temp_dir'))
        $sp = sys_get_temp_dir();

	if (empty($sp))
		$sp = '/tmp';

	return $sp.'/swekey_'.md5($id).'.ids';
}

function UnserializeCookie($var)
{
    $ar = explode(",", $var);
    return array
    (
        'time' => empty($ar[0]) ? 0 : $ar[0],
        'session_id' => empty($ar[1]) ? "" : $ar[1],
        'file_id' => empty($ar[2]) ? 0 : $ar[2]
    );
}

function GetAuthFrameRes()
{
    if (empty($_COOKIE['swekey_authframe_session_id']))
        return null;
    
    // fixes magic_quotes_gpc
    $cookieval = str_replace('\\"', '"', $_COOKIE['swekey_authframe_session_id']);
  
    $cookie = UnserializeCookie($cookieval);
    if (empty($cookie))
        return null;
        
    if (empty($cookie['time']))
        return null;

    // timeout
    if ($cookie['time'] + 120 < time())
        return null;
 
    if (! empty($cookie['file_id']))
    {
        $res = @file_get_contents(FilePath($cookie['file_id']));
//error_log("file => $res ".$cookie['file_path']);
        if (empty($res))
            return null;

//        unlink($cookie['file_path']);
        return unserialize($res);
    }   
    

    if (empty($cookie['session_id']))
        return null;
                
	$valid_ids = null;

    $curr_sid = session_id();

	if ($cookie['session_id'] == $curr_sid)
	{
        if (isset($_SESSION['swekey_authframe']))
        	$valid_ids = $_SESSION['swekey_authframe']['valid_ids'];
    	unset($_SESSION['swekey_authframe']);
    }
    else
	{
		if (! empty($curr_sid))
            session_write_close();
            
		session_id($cookie['session_id']);
		session_start();

//foreach ($_SESSION as $k => $v) error_log("\$_SESSION $k => $v");
//foreach ($_SESSION['swekey_authframe'] as $k => $v) error_log("\$_SESSION['swekey_authframe'] $k => $v");
//foreach ($_SESSION['swekey_authframe']['valid_ids'] as $k => $v) error_log("\$_SESSION['swekey_authframe']['valid_ids']  $k => $v");

        if (isset($_SESSION['swekey_authframe']))
        	$valid_ids = $_SESSION['swekey_authframe']['valid_ids'];

		$_SESSION = array();
		session_destroy();

		if (! empty($curr_sid))
		{
    		session_id($curr_sid);
	       	session_start();
	    }
	}

//foreach ($valid_ids as $v) error_log("GetAuthFrameRes xxx => $v");
	
    return $valid_ids;
}

