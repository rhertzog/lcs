<?php
/**
  * Class represents Logout action.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */


/**
  * Class represents Logout action. All session scope variables associated with user are
  * cleared.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */
class LogoutAction extends ActionTemplate {
	
	/**
	 * Logout user and redirect to default page.
	 *
	 * @access public
	 */
	function perform() {
		
		// unset old values
		unset($_SESSION[BROWSER_AUTHENTICATED_USER]);
		unset($_SESSION[BROWSER_AUTHENTICATED_USER_ROOT]);
		
		//go to main page
		header('Location: '.Browser_Utilities :: getValueFromConfiguration("browser.main.file"));
		exit;
	}
}

?>
