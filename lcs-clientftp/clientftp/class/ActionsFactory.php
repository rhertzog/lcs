<?php
/**
  * Factory is responsible for creating instance of object which will perform given action. 
  *  
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */
  
/**
  * Factory is responsible for creating instance of object which will perform given action. 
  * 
  * For instance if action is UploadFile then Factory is responsible for creating a instance
  * of <i>UploadFileAction</i> class. Action class must be a subclass of 
  * <i>ActionTemplate</i>. 
  *
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */
class ActionsFactory {
	
	
	/**
	  * Method creates instance of object which will perform later action specified as a parameter.
	  *
	  * @access public
	  * @static
	  *
	  * @param string $action - name of the action which denotes name of action class.
	  * @return object which represents given action. The object is a subclass of <i>ActionTemplate</i>
	  *	       object which exposes method performAction().
	  *
	  * @see ActionTemplate::performAction()
	  */
	function &createInstance($action = "Default") {
		
		// check if action is set
		if ( !isset($action) || strlen($action) == 0) { $action = "Default"; }
		
		$action = $action."Action";
		Browser_Utilities :: log("crating action object: [" . $action ."]", "debug");
		
		// create new object
		$object =& new $action();
		
		// set the name of action
		$object->action = $action;
		
		return $object;
	}
}

?>
