<?php
/**
  * Class represents action which sends file content to user, performs download.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */

/**
  * Class represents action which sends file content to user, performs download.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */
class DownloadFormAction extends ActionTemplate {
	
	/**
	 * Process download file.
	 *
	 * @access public
	 */
	function perform() {
		
		$file = $_REQUEST['filename'];
		$file = stripslashes (Browser_Utilities::unescape( $file ) );
		
		$fullpath = Browser :: getBrowserRoot() . Browser_Utilities :: getSeparator() . $file;
		$mimeType = BrowserHelper :: getMimeType(strtolower(basename($fullpath)));
		
		Browser_Utilities :: log("[DownloadFormAction] downloading: " . $fullpath, "info");
				
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-type: ' . $mimeType.'; charset=iso-8859-1' );
		
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") === false) {
			header('Content-Disposition: attachment; filename="' . basename($fullpath). '"');
		} else {
			header('Content-Disposition: attachment; filename="' . rawurlencode(basename($fullpath)). '"');
		}
		
		header("Content-Length: ".@filesize($fullpath));
		set_time_limit(0);
		@readfile(realpath($fullpath));
		exit;
	}

}

?>
