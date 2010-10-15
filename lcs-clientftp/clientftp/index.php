<?php
# Authentification LCS
include "/var/www/lcs/includes/config.inc.php";
include "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers,$user)= isauth();
/*
if ($idpers && $user) {

} else 
    header("Location:../lcs/auth.php");
*/
if ( ! $idpers  || ! $user ) header("Location:../lcs/auth.php");
/**
  * Controller, all requests go here, controler creates all system variables, loads configuration and creates instance of action class which handles request.
  *  
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.controller
  */
  
// start session and buffer
session_start();
ob_start();

/** 
  * Browser base folder that contains all files. 
  */
define('BROWSER_BASE', dirname(__FILE__));


/**
  * File system path separator. 
  */
define('BROWSER_SEPARATOR', "/");

/**
  * Browser properties files. 
  */
define('BROWSER_PROPERTIES_FILE', "conf".BROWSER_SEPARATOR."system.properties");

/**
  * Browser log level. 
  */
define('BROWSER_LOG_LEVEL', "INFO");

/** 
  * Browser log file. It will be located in log directory.
  */
define('BROWSER_LOG_FILE', "clientftp.log");

/**
  * It is required to keep in session information about authenticated user. If session doesn't contain that information then it imply that 
  * there is no authenticated user. 
  */
define('BROWSER_AUTHENTICATED_USER', 'AUTHENTICATED_USER');

/**
  * Custom root location for user, this variable should be set only if user's root is different then default one.
  */
define('BROWSER_AUTHENTICATED_USER_ROOT', 'AUTHENTICATED_USER_ROOT');

/**
  * Name of URL parameter which defines start location for OFB. 
  */
define('REQUEST_START_LOCATION', 'path');

/** 
  * Core Browser utilities 
  */
require_once (BROWSER_BASE.BROWSER_SEPARATOR.'class'.BROWSER_SEPARATOR.'Browser_Utilities.php');

/**
  * Browser configuration. 
  */
$_BROWSER_CONFIGURATION = Browser_Utilities :: loadProperites(BROWSER_BASE.BROWSER_SEPARATOR.BROWSER_PROPERTIES_FILE);

/* define class directory */
$_INCLUDE_DIR = array ();
$_INCLUDE_DIR[] = BROWSER_BASE.BROWSER_SEPARATOR.'class';
$_INCLUDE_DIR[] = BROWSER_BASE.BROWSER_SEPARATOR.'class'.BROWSER_SEPARATOR.'actions'.BROWSER_SEPARATOR.'ActionTemplate.php';
$_INCLUDE_DIR[] = BROWSER_BASE.BROWSER_SEPARATOR.'class'.BROWSER_SEPARATOR.'actions';
$_INCLUDE_DIR[] = BROWSER_BASE.BROWSER_SEPARATOR.'lib';

/**
  * include all dependencies 
  */
Browser_Utilities :: includeRequiredClasses($_INCLUDE_DIR);

/* end of definition and configuration of Browser */

/* *****************************************************************************/
/* ****************************** AUTHENTICATION *******************************/
/* **************************************************************************** */

/* log if it is a first visit */
Browser_Utilities :: checkIfFirstVisit();

/* if authentication.enforce is set to false then user is automaticaly loged in as a anonymous */
$_AUTHENTICATION_ENFORCE = Browser_Utilities :: getValueFromConfiguration("authentication.enforce");

if ( $_AUTHENTICATION_ENFORCE == 'false' && !isset($_SESSION[BROWSER_AUTHENTICATED_USER]) ) {
	Browser_Utilities :: log("setting user to anonymous", "info" );
	$_SESSION[BROWSER_AUTHENTICATED_USER] = 'anonymous';
}

/* *****************************************************************************/
/* ******************************** DISPATCHER *********************************/
/* **************************************************************************** */

$_DISPATH = Browser_Utilities :: getValueFromConfiguration("browser.dispatch");
$_ACTION_OBJECT = null;

if ( isset ( $_REQUEST[ $_DISPATH ] ) ) { $_ACTION_OBJECT =& ActionsFactory :: createInstance( $_REQUEST[ $_DISPATH ] );} 
else { $_ACTION_OBJECT =& ActionsFactory :: createInstance(); }

/* Clean (erase) the output buffer and turn off output buffering */
ob_end_clean();

$_ACTION_OBJECT->performAction();

/* Flush (send) the output buffer and turn off output buffering */
ob_end_flush();

?>
