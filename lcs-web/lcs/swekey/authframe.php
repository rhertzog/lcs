<?php
/*
	Swekey Authentication Frame
	(c) Musbe, Inc. 2008
*/

// $Id$

	header("Cache-Control: no-cache, must-revalidate");	
	
	//security validation
	foreach ($_GET as $key => $val)
	{
		if ($val != htmlspecialchars($val))
		{
			echo "ERROR: Invalid query string";
			exit;
		}
	}

	// to verify that the file is accesible
	if (! empty($_GET['verify']))
	{
		echo $_GET['verify'];
		exit;
	}

	if (! empty($_GET['session_id']))
		session_id($_GET['session_id']);

    session_start();	

	$session_id = session_id();
      
    function FilePath($id)
    {
    	// In magento session_save_path is not the default one
    	if (! empty($_SESSION['swekey_authframe']['session_save_path']))
    		$sp = $_SESSION['swekey_authframe']['session_save_path'];
		else
    		$sp = session_save_path();
    	
        if (empty($sp) && function_exists('sys_get_temp_dir'))
            $sp = sys_get_temp_dir();

    	if (empty($sp))
    		$sp = '/tmp';
    
		return $sp.'/swekey_'.md5($id).'.ids';
    }

    // Sometime we can leave a file on the disk, this function purge them
    function PurgeSwekeyIds()
    {
        // we purge only once every 100 login
        if ((mt_rand() % 100) != 0) 
            return;
    
        $rootdir = session_save_path();
        $modif = filemtime($rootdir.'/swekey.mutex');

        // It is the first time, we create the file
        if ($modif != false)
        {
            @fclose(fopen($rootdir.'/swekey.mutex' , 'x'));
            return;
        }

        // we purge only once per hour
        if (time() - $modif < 3600) 
            return;
                
        touch($rootdir.'/swekey.mutex');
        
        if ($dh = opendir($rootdir)) 
        {
            while (($file = readdir($dh)) !== false) 
            {
                if (filetype($rootdir.'/'.$file) == "file" && mb_ereg('^swekey_[A-Za-z0-9]{32}\.ids$', $file) !== FALSE)
                {
                    $modif = filemtime($rootdir.'/'.$file);
                    if (time() - $modif > 300)  // a file is not valid more than 5 minutes
                        unlink($rootdir.'/'.$file);
                }
            }
            closedir($dh);
        }
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

    function SerializeCookie($var)
    {
        return 
            (empty($var['time']) ? '0' : $var['time']).','.
            (empty($var['session_id']) ? '' : $var['session_id']).','.
            (empty($var['file_id']) ? '' : $var['file_id']);
    }

    // Delete previous file   
    if (isset($_COOKIE['swekey_authframe_session_id']))  
    {  
        $cookie = UnserializeCookie($_COOKIE['swekey_authframe_session_id']);
        if (! empty($cookie['file_id']))
            $fileToDelete = $cookie['file_id'];
    }
                 
    $cookie = array();      
    
    $cookie['time'] = time();  

	// $use_file must be set to true for php applications that rewrote the session api
    // like Roundcube  
	if (!empty($_GET['use_file']))
	{
    	$use_file = 1;
    	$cookie['file_id'] = $session_id;
	}
	else
	{
        $use_file=0;
    	$cookie['session_id'] = $session_id;
	}
	
    setcookie('swekey_authframe_session_id', SerializeCookie($cookie)  ,time() + 60 * 60 * 24, '/'); 

	if (! empty($fileToDelete) && $fileToDelete != $cookie['file_id'])
        @unlink(FilePath($fileToDelete));
	
	include "swekey.php";
	
    if (isset($_GET['swekey_tokens']))
        $swekey_tokens = $_GET['swekey_tokens'];

    if (isset($_GET['swekey_ids']))
        $swekey_ids = $_GET['swekey_ids'];

    // very first call
     if (! isset($swekey_tokens) && ! isset($swekey_ids))
    {
        $_SESSION['swekey_authframe']['ids'] = "";
        $_SESSION['swekey_authframe']['rndtoken_server'] = isset($_GET['rndtoken_server']) ? $_GET['rndtoken_server'] : "";
        $_SESSION['swekey_authframe']['check_server'] = isset($_GET['check_server']) ? $_GET['check_server'] : "";
		$_SESSION['swekey_authframe']['session_save_path'] = isset($_GET['session_save_path']) ? $_GET['session_save_path'] : "";
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Swekey Authentication Frame</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  </head>

<body style="background-color:transparent;margin:0;padding:0;">
<script type="text/javascript" src="swekey.js"></script>
<?php

Swekey_InsertPlugin();

if (! isset($_SESSION['swekey_authframe']))
	$_SESSION['swekey_authframe'] = array();

if (! isset($_SESSION['swekey_authframe']['ids']) && ! isset($swekey_ids))
	$swekey_ids="";

// We have no swekey connected so no need to 
// calculate the tokens
if (isset($swekey_ids) && empty($swekey_ids))
{
	$_SESSION['swekey_authframe']['ids'] = "";
	$swekey_tokens = "";
	unset($swekey_ids);
}

// Check that we are not doing and auth in antother page
if (isset($swekey_ids) && (! isset($_SESSION['swekey_authframe']['auth_started']) || (time() - $_SESSION['swekey_authframe']['auth_started']) > 3))
{
	//error_log("authframe calculating tokens for $swekey_ids\n", 3, "/qwe.log");
	$_SESSION['swekey_authframe']['ids'] = $swekey_ids;
	Swekey_SetRndTokenServer($_SESSION['swekey_authframe']['rndtoken_server']); 
	$_SESSION['swekey_authframe']['rt'] = Swekey_GetFastRndToken();
	$_SESSION['swekey_authframe']['auth_started'] = time();
	?>		
	<script type="text/javascript">
	var tokens = "";
	var ids = Swekey_ListKeyIds();
	var connected_keys = ids.preg_split("/,/");
 	for (i in connected_keys) 
	    if (connected_keys[i] != null && connected_keys[i].length == 32)
		    tokens += connected_keys[i] + Swekey_GetSmartOtp(connected_keys[i], "<?php echo $_SESSION['swekey_authframe']['rt'];?>");
		    
	window.location.search = "?session_id=<?php echo $session_id;?>&use_file=<?php echo $use_file;?>&swekey_tokens=" + tokens;
	</script>
	</body>
	</html>
	<?php
	exit;
}

if (isset($swekey_tokens) && (isset($_SESSION['swekey_authframe']['rt']) || empty($swekey_tokens)))
{
//	error_log("authframe verifying tokens\n", 3, "/qwe.log");
	$_SESSION['swekey_authframe']['valid_ids'] = array();

	while (mb_strlen($swekey_tokens) >= 32 + 64)
	{
		$id = mb_substr($swekey_tokens, 0, 32);		
		$otp = mb_substr($swekey_tokens, 32, 64);
		$swekey_tokens = mb_substr($swekey_tokens, 32 + 64);		

		Swekey_SetCheckServer($_SESSION['swekey_authframe']['check_server']); 
        if (Swekey_CheckSmartOtp($id, $_SESSION['swekey_authframe']['rt'], $otp))
            $_SESSION['swekey_authframe']['valid_ids'][sizeof($_SESSION['swekey_authframe']['valid_ids'])] = $id;
	}

	// we store also in a file because we do not want to include the db session code 
	if (! empty($cookie['file_id']))
	{
//		@file_put_contents(FilePath($cookie['file_id']), serialize($_SESSION['swekey_authframe']['valid_ids']));
   		@unlink(FilePath($cookie['file_id']));
   		$file = fopen  (FilePath($cookie['file_id']) , "x");
   		if ($file != FALSE)
   		{
   	    	@fwrite($file, serialize($_SESSION['swekey_authframe']['valid_ids'])); 
			@fclose($file);
		}
		
		PurgeSwekeyIds();
    }
    
	unset($_SESSION['swekey_authframe']['rt']);
	unset($_SESSION['swekey_authframe']['auth_started']);	
}

echo("<p>");
if (! empty($_SESSION['swekey_authframe']['valid_ids']))
	foreach ($_SESSION['swekey_authframe']['valid_ids'] as $key) 
		echo "$key<br/>";

//foreach ($_SESSION['swekey_authframe']['valid_ids'] as $key) error_log("\$_SESSION['swekey_authframe']['valid_ids']  $key");

		
echo("done<br/></p>");


?>	

<script type="text/javascript">	

function Refresh()
{
	var ids = Swekey_ListKeyIds();
	if (ids != "<?php echo $_SESSION['swekey_authframe']['ids'];?>")
		window.location.search = "?session_id=<?php echo $session_id;?>&use_file=<?php echo $use_file;?>&swekey_ids=" + ids;
	else
		setTimeout("Refresh()", 1000);
}

function ForceRefresh()
{
	var ids = Swekey_ListKeyIds();
	if (ids != "")
		window.location.search = "?session_id=<?php echo $session_id;?>&use_file=<?php echo $use_file;?>&swekey_ids=" + ids;
}

Refresh();
setTimeout("ForceRefresh()", 1000 * 60); // we reload every minute to the authentication does not expire

</script>     
</body>
</html>