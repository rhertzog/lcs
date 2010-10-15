<?php
/**
  * Puropse of class is to be a data transfer objects. It doesn't have any methods to perform business logic, it holds only data.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */

/**
  * Puropse of class is to be a data transfer objects. It doesn't have any methods to perform business logic, it holds only data.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */
class Entry  {

	/**
	  * Holds information about entry (directory, file) name. 
	  *
	  * @access public
	  * @var string 
	  */
	var $name = NULL;
	
	/**
	  * Holds information about entry (directory, file) type, it could be file or directory. 
	  *
	  * @access public
	  * @var string 
	  */
	var $type = NULL;
	
	/**
	  * Holds information about file size. 
	  *
	  * @access public
	  * @var int 
	  */
	var $size = NULL;
	
	/**
	  * Holds information about entry (directory, file) path, it's relative path from browser root to that entry. 
	  * For instance if browser basepath is '/home/www/public' and file is located in '/home/www/public/dir1/test.txt'
	  * then this variable will be set to 'dir1/test.txt'.
	  *
	  * @access public
	  * @var string 
	  */
	var $relativePath = NULL;
	
	/**
	  * This variable is set to true when entry is a graphical file and it is possible to create thumbnail for it.
	  *
	  * @access public
	  * @var boolean 
	  */
	var $thumbnail = NULL;
	
	/**
	  * Holds information about entry (directory, file) permissions. 
	  *
	  * @access public
	  * @var string 
	  */
	var $permissions = NULL;
	
	/**
	  * Set to true if file could be modified (its content). 
	  *
	  * @access public
	  * @var boolean 
	  */
	var $editable = false;
	
	/**
	  * Holds information about entry (directory, file) last modification date. 
	  *
	  * @access public
	  * @var string 
	  */
	var $lastModify = NULL;
	
}
?>