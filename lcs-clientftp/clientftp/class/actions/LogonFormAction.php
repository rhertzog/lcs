<?php
/**
  * Class represents Logon action.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */


/**
  * Class represents Logon action, if POST doesn't contain data from form then logon page is displayed. If POST contain data then 
  * credentials are verified. To verfiy credentials file with users is loaded. Name of file is defined in configuration, 
  * <i>authentication.credentials.file</i> property. File must be located in conf directory.
  *
  * If credentials are correct then username is put into session variable: BROWSER_AUTHENTICATED_USER and user 
  * is redirected to main page.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */
class LogonFormAction extends ActionTemplate {
	
	/**
	 * Show logon form and if page is post back then check credentials.
	 *
	 * @todo think about roles, each user could have different set of rights.
	 * @access public
	 */
	function perform() {
		
		$smarty = NULL;
		
		// unset old values
		unset($_SESSION[BROWSER_AUTHENTICATED_USER]);
		unset($_SESSION[BROWSER_AUTHENTICATED_USER_ROOT]);
		
		// check if it's post back or first display
		if ( isset ($_REQUEST['login']) && isset( $_REQUEST['password']) ) {
			
			/* username and password pairs */
			/* since 0.1.5 there are additional information such as custom root location */
			$_CREDENTIALS_CONFIGURATION = Browser_Utilities :: loadProperites(BROWSER_BASE.BROWSER_SEPARATOR.'conf'.BROWSER_SEPARATOR.Browser_Utilities :: getValueFromConfiguration("authentication.credentials.file"));
			
			if ( isset($_CREDENTIALS_CONFIGURATION[$_REQUEST['login']]) ) {
				if ($_CREDENTIALS_CONFIGURATION[$_REQUEST['login']] == $_REQUEST['password']) {
					
					// set authenticated user in session
					$_SESSION[BROWSER_AUTHENTICATED_USER] = $_REQUEST['login'];
					Browser_Utilities :: log("[LogonFormAction.perform] user " .$_REQUEST['login']. " authenticated", "info" );
					
					// check if user has a custom root location
					// key is login.root where login is a username
					if ( isset($_CREDENTIALS_CONFIGURATION[$_REQUEST['login'].'.root']) ) {
						$_SESSION[BROWSER_AUTHENTICATED_USER_ROOT] = $_CREDENTIALS_CONFIGURATION[$_REQUEST['login'].'.root'];
					}
					
					//go to main page
					header('Location: '.Browser_Utilities :: getValueFromConfiguration("browser.main.file"));
					exit;
				} else {
					// credentials are not ok
					Browser_Utilities :: log("[LogonFormAction.perform] invalid credentials for user [" .$_REQUEST['login']. "]", "info" );
					
					$smarty =& $this->getSmarty();
					$smarty->assign('result', 'INVALID_CREDENTIALS');
				}
			} else {
				// there are no such user
				Browser_Utilities :: log("[LogonFormAction.perform] there is no user [" .$_REQUEST['login']. "]", "info" );
				$smarty =& $this->getSmarty();
				$smarty->assign('result', 'INVALID_CREDENTIALS');
			}
		}
		
		$smarty =& $this->getSmarty();
		$smarty->assign('labels', Browser_Utilities :: loadProperites("conf". Browser_Utilities :: getSeparator() .Browser_Utilities :: getValueFromConfiguration("resource.labels.file")));
		$smarty->display("logonForm.tpl");
	}
}

?>
