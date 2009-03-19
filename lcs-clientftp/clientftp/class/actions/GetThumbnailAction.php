<?php
/**
  * Class represents action creates thumbnail (if it doesn't exist) and sends it to user.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */ 


/**
  * Class represents action creates thumbnail (if it doesn't exist) and sends it to user.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */ 
class GetThumbnailAction extends ActionTemplate {
	
	/**
	  * Creates if it neccessary thumbnail and sends it to the client.
	  * 
	  * @todo remove UploadFormAction :: createAndSaveThumbnail() call, it's private method.
	  * @access public
	  */
	function perform() {
		
		if ( !isset($_REQUEST['filename']) ) {
			Browser_Utilities :: log("[GetThumbnailAction.perform] lack of file name!", "error");
			return;
		}
		
		$path = stripslashes ($_REQUEST['filename']);
		$filename = basename( $path );
		
		if (!BrowserHelper :: isGraphicalFile($filename)) {
			Browser_Utilities :: log("[GetThumbnailAction.perform] requested file is not a graphical file: ". $filename, "error");
			return;
		}

		// base + relative + file
		$fullpath = Browser :: getBrowserRoot().Browser_Utilities :: getSeparator().$path;
				
		if( !file_exists( $fullpath ) ) {
			Browser_Utilities :: log("[GetThumbnailAction.perform] file doesn't exist: ". $fullpath, "error");
			return;
		}
		
		$thumbnail = BrowserHelper :: getThumbnailPath($fullpath);
		Browser_Utilities :: log("[GetThumbnailAction.perform] thumbnail: [". $thumbnail . "] for file: [".$path."]" , "info");
		
		$imageToReturn = null;
		
		// check if thumbnail exist
		if ( file_exists( $thumbnail ) ) {
			Browser_Utilities :: log("[GetThumbnailAction.perform] thumbnail exists", "debug");
			$imageToReturn = ImagesUtils :: createThumbnail($thumbnail);
		} else {
			Browser_Utilities :: log("[GetThumbnailAction.perform] thumbnail doesn't exist! ", "info");
			
			if ( Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.create") == "true" ) {
				// create new thumbnail
				UploadFormAction :: createAndSaveThumbnail($fullpath);
				$imageToReturn = ImagesUtils :: createThumbnail($thumbnail);
			} else {
				$imageToReturn = ImagesUtils :: createThumbnail($fullpath);
			}
		}
		
		$mimeType = BrowserHelper :: getMimeType($filename);
		$fileExt = BrowserHelper :: getFileExtension($filename);
		
		header('Content-type: ' . $mimeType );
		
		if ( $fileExt == 'jpeg' || $fileExt == 'jpg') {
			imagejpeg($imageToReturn);
		} else if ($fileExt == 'gif') {
			imagegif($imageToReturn);
		} else if ($fileExt == 'png') {
			imagepng($imageToReturn);
		}
		exit;
	}
}

?>
