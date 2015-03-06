<?php
	
	class lcs_authentication extends rcube_plugin
	{
		public $task = 'login';
	
		function init()
	  	{
	    	$this->add_hook('startup', array($this, 'startup'));	    
	    	$this->add_hook('authenticate', array($this, 'authenticate'));
	  	}

	  	function startup($args)
	  	{
	    	// change action to login
	    	if ( empty($args['action']) && empty($_SESSION['user_id']) )
				$args['action'] = 'login';
			return $args;
	  	}
	
	  	function authenticate($args) {			$LOGIN="";      	if (! empty($_COOKIE["Lcs"])) {        		require_once ("/var/www/lcs/includes/functions.inc.php");        		# Search login        		$file="/var/lib/php5/sess_".$_COOKIE['Lcs'];        		$ch= mb_split('"',file_get_contents ($file));        		$LOGIN=$ch[1];        		# Search and decode LCS cookie pass        		if ($LOGIN != "")  $PASS = urldecode( xoft_decode($_COOKIE['LCSuser'],$key_priv) );        		if ( !empty($LOGIN) && !empty($PASS) ) {            	$args['user'] = $LOGIN;            	$args['pass'] = $PASS;        		}        		$args['cookiecheck'] = false;        		$args['valid'] = true;        		return $args;        	}
	  	}		  
	} # End class lcs_authentication
