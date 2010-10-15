<?php
/**
  * Class represents default action, it creates and starts XOAD server which will talk with AXIS clients.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */


/**
  * Class represents default action, it creates and starts XOAD server which will talk with AXIS clients.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */
class DefaultAction extends ActionTemplate {
	
	/**
	 * Method creates and starts XOAD Server.
	 *
	 * @access public
	 */
	function perform() {
		
		define('XOAD_AUTOHANDLE', true);
		require_once ('lib/xoad/xoad.php');
		
		XOAD_Server::allowClasses('Browser');
		
		// if it's a XOAD call then following operation will the last one!
		if (XOAD_Server::runServer()) {
			exit;
		}
		
		// get smarty instance
		$smarty = $this->getSmarty();
		
		$smarty->assign('labels', Browser_Utilities :: loadProperites("conf". Browser_Utilities :: getSeparator() .Browser_Utilities :: getValueFromConfiguration("resource.labels.file")));
		//$this->smarty->debugging = true;
		$smarty->display("main.tpl");
	}
	
}

?>
