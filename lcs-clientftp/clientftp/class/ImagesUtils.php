<?php
/**
  * Class provides graphical utilities (thumbnails) for Online File Browser.  
  *
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */

/**
  * Class provides graphical utilities (thumbnails) for Online File Browser.  
  *
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */
class ImagesUtils {

	/**
	  * Method creates thumbnail from original image.
	  * Method based on Michael Bailey's tutorial: {@link http://codewalkers.com/tutorials/42/1.html http://codewalkers.com/tutorials/42/1.html}. Thanks!
	  *
	  * Note that size of thumbnail is defined in configuration file (width: <i>browser.thumbnail.max.width</i> property and
	  * height: <i>browser.thumbnail.max.height</i> property).
	  *
	  * @access public
	  * @static
	  *
	  * @param string $imageLocation - path to image.
	  * @return resource - an image identifier representing the image.
	  */
	function createThumbnail($imageLocation) {
		
		$_MAX_WIDTH = Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.max.width");
		$_MAX_HEIGHT = Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.max.height");
		
		# Load image
		$img = null;
		$ext = BrowserHelper :: getFileExtension(basename($imageLocation));
		
		if ($ext == 'jpg' || $ext == 'jpeg') {
		    $img = @imagecreatefromjpeg($imageLocation);
		} else if ($ext == 'png') {
		    $img = @imagecreatefrompng($imageLocation);
		# Only if your version of GD includes GIF support
		} else if ($ext == 'gif') {
		    $img = @imagecreatefromgif($imageLocation);
		} else {
			Browser_Utilities :: log("[ImagesUtils.createThumbnail] file ext: ".$ext." is not allowed", "warn");
			return;
		}
		
		# If an image was successfully loaded, test the image for size
		if ($img) {
		
		    # Get image size and scale ratio
		    $width = imagesx($img);
		    $height = imagesy($img);
		    $scale = min( $_MAX_WIDTH /$width, $_MAX_HEIGHT /$height);
		
		    # If the image is larger than the max shrink it
		    if ($scale < 1) {
			$new_width = floor($scale*$width);
			$new_height = floor($scale*$height);
		
			# Create a new temporary image
			$tmp_img = imagecreatetruecolor($new_width, $new_height);
		
			# Copy and resize old image into new image
			imagecopyresized($tmp_img, $img, 0, 0, 0, 0,
					 $new_width, $new_height, $width, $height);
			imagedestroy($img);
			$img = $tmp_img;
		    }
		}
		
		# Create error image if necessary
		if (!$img) { $img = ImagesUtils :: createErrorImage(); }
		
		return $img;
	}
	
	
	/**
	  * Method creates error image with size defined in configuration file. (width: <i>browser.thumbnail.max.width</i> property and
	  * height: <i>browser.thumbnail.max.height</i> property).
	  * Method based on Michael Bailey's tutorial: {@link http://codewalkers.com/tutorials/42/1.html http://codewalkers.com/tutorials/42/1.html}. Thanks!
	  *
	  * @access private
	  * @static
	  *
	  * @return resource - an image identifier representing the image.
	  */
	function createErrorImage() {
		$_MAX_WIDTH = Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.max.width");
		$_MAX_HEIGHT = Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.max.height");
		
		$img = imagecreate($_MAX_WIDTH, $_MAX_HEIGHT);
		imagecolorallocate($img,0,0,0);
		$c = imagecolorallocate($img,70,70,70);
		imageline($img,0,0,$_MAX_WIDTH,$_MAX_HEIGHT,$c);
		imageline($img,$_MAX_WIDTH,0,0,$_MAX_HEIGHT,$c);
		
		return $img;
	}

}
?> 
