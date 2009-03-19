<?php
/**
  * Class is a template for other actions. Its job is to be a general structure, common point from which each specific
  * action inherits. General idea is that name of action to perform is passed as a URL parameter. 
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */

/**
  * Class is a template for other actions. Its job is to be a general structure, common point from which each specific
  * action inherits. General idea is that name of action to perform is passed as a URL parameter. 
  * 
  * For instance for URL: http://filebrowser.mbsoftware.pl/index.php?action=donwloadFile&filename=test.txt action is downloadFile
  * so controller will call ActionsFactory::createInstance() method and pass as a parameter name of action. Then factory will create
  * instance of class DownloadFileAction and return it to the controller which eventually will call on that object method performAction(). 
  *
  * Method performAction() will be called for each action, to create custom action you need to create new class which extends this class and
  * override method perform(). In body of perform you can put your custom action. 
  *
  * Method performAction() is responsible for ensuring that user is authenticated, if not then user is redirected to logon page.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */
class ActionTemplate {

	/**
	  * Holds information about action name. 
	  *
	  * @access public
	  * @var string 
	  */
	var $action = NULL;
	
	/**
	  * Holds Samrty instance. 
	  *
	  * @access private
	  * @var object 
	  */
	var $smarty = NULL;
	
	
	/**
	  * This method will perform actions, it checks if user is authenticated and after that 
	  * invokes specific action.  
	  *
	  * @access public
	  */
	function performAction() {
		
		/* check if given action need authentication, if yes then enforce it */
		if ( $this->isAuthenticationRequiredForAction() && !isset($_SESSION[BROWSER_AUTHENTICATED_USER] ) ) {
			Browser_Utilities :: log("authentication required for the user, redirecting to logon page", "info" );
			
			// display logon page
			header('Location: '.Browser_Utilities :: getValueFromConfiguration("browser.main.file") .'?'.Browser_Utilities :: getValueFromConfiguration("browser.dispatch").'=LogonForm');
			exit;
		}
		
		/* perform real action */
		$this->perform();
	}
	
	
	/**
	  * This method should be overriden by specific actions.
	  *
	  * @access public
	  */
	function perform() {
		
	}
	
	/**
	  * This method returns instance of Smart class. If Smarty wasn't included before then it will also 
	  * perform whole necessary configuration.
	  *
	  * @access private
	  *
	  * @return instance of Smarty class.
	  */
	function &getSmarty() {
		if ($this->smarty == NULL) {
			
                        # LCS modification 12 Juin 2008
			#$smartyDir = BROWSER_BASE . Browser_Utilities :: getSeparator() . 'lib' . Browser_Utilities :: getSeparator() . 'smarty';
			#define('SMARTY_DIR', $smartyDir . Browser_Utilities :: getSeparator());
			
			// include 
			#require_once (SMARTY_DIR . 'Smarty.class.php');
			require_once('Smarty.class.php');			
			// create object
			$this->smarty = new Smarty(); 
			
			// get templates location
			$path = Browser :: getTemplatesRoot();
			
			// set up smarty
			$this->smarty->template_dir =  $path . Browser_Utilities :: getSeparator() .'templates'. Browser_Utilities :: getSeparator();
			$this->smarty->compile_dir = $path . Browser_Utilities :: getSeparator() .'templates_c'. Browser_Utilities :: getSeparator();
			$this->smarty->cache_dir = $path . Browser_Utilities :: getSeparator() .'cache'. Browser_Utilities :: getSeparator();
			$this->smarty->config_dir = $path . Browser_Utilities :: getSeparator() .'configs'. Browser_Utilities :: getSeparator(); 
			
			$this->smarty->compile_check = true;
			$this->smarty->debugging = false; 
			
			Browser_Utilities :: log("SMARTY template directory = [" . $this->smarty->template_dir . "] for action: [" .$this->action. "]", "info" );
		}
		
		return $this->smarty;
	}
	
	
	/**
	  * Method returns true if authentication is required for given action.
	  * Method returns false for LogonFormAction to make it possible to display logon form.
	  *
	  * @access private
	  *
	  * @return boolean true if authentication is required for action.
	  */
	function isAuthenticationRequiredForAction() {
		
		Browser_Utilities :: log("[ActionTemplate.isAuthenticationRequiredForAction] checking: " . $this->action, "debug" );
		
		if ($this->action == 'LogonFormAction') { return false; }
		
		return true;
	}

}
?>
