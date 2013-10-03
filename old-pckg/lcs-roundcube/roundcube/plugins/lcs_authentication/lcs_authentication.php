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
	
	  function authenticate($args)
	  {	
		$IDPERS=0;$LOGIN="";
		if (! empty($_COOKIE["LCSAuth"]))
		{ 
            $SESS=$_COOKIE["LCSAuth"];

			include ("/usr/share/lcs/roundcube/plugins/lcs_authentication/config_auth_lcs.inc.php");
			# Search idpers
			$IDPERS=exec ("mysql -e \"SELECT idpers from $DBAUTH.sessions where sess='$SESS'\" -u $USERAUTH -p$PASSAUTH");

			# Search login
			$LOGIN=exec ("mysql -e \"SELECT login FROM $DBAUTH.personne WHERE id=$IDPERS \" -u $USERAUTH -p$PASSAUTH");
	
			# Search and decode LCS cookie pass
			if ($IDPERS != "0") 
				  $PASS = urldecode( xoft_decode($_COOKIE['LCSuser'],$key_priv) );	

	    	if ( !empty($LOGIN) && !empty($PASS) ) {
	      		$args['user'] = $LOGIN;
	      		$args['pass'] = $PASS;
	    	}
	    	$args['cookiecheck'] = false;
	    	return $args;
	    }
	  }
	  
	  
	} # End class lcs_authentication
