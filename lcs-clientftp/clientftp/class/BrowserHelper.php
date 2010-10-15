<?php
/**
  * Class contains set of useful static methods, main class, Browser class delegates some functinality here.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <http://filebrowser.mbsoftware.pl>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */

/**
  * Class contains set of useful static methods, main class, Browser class delegates some functinality here.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <http://filebrowser.mbsoftware.pl>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */
class BrowserHelper {
	
	/**
	  * Method evaluates entry permissions and returns it in unix format.
	  *
	  * @access public
	  * @static
	  *
	  * @param $entry - entry for which permissions will be evaluated.
	  * @return string which represents permissions in unix format.
	  */
	function getPermissions($entry) {
		
		$perms = fileperms($entry);
		
		if (($perms & 0xC000) == 0xC000) {
			// socket
			$info = 's';
		} elseif (($perms & 0xA000) == 0xA000) {
			// symbolic link
			$info = 'l';
		} elseif (($perms & 0x8000) == 0x8000) {
			// normal file
			$info = '-';
		} elseif (($perms & 0x6000) == 0x6000) {
			$info = 'b';
		} elseif (($perms & 0x4000) == 0x4000) {
			// directory
			$info = 'd';
		} elseif (($perms & 0x2000) == 0x2000) {
			$info = 'c';
		} elseif (($perms & 0x1000) == 0x1000) {
			$info = 'p';
		} else {
			// unknown
			$info = 'u';
		}
		
		// onwer
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
		
		// group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
		
		// others
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

		return $info;
	}
	
	

	/**
	  * Method returns file extension (lower case) based on filename.
	  *
	  * @access public
	  * @static
	  *
	  * @param $filename - full filename (without path).
	  * @return string - file extension.
	  */
	function getFileExtension($filename = "") {
	 
		$dotPosition = strrpos($filename, ".");
		
		// initial value, file name could not contain any dot
		$fileExt = $filename;
		if ($dotPosition !== false) {
			$fileExt = substr($filename, ++$dotPosition);
		}
		
		return strtolower($fileExt);
	}
	
	/**
	  * Method returns true if given file is a graphical file (based on extension).  Extensions for graphical files: jpeg, jpg, gif and png.
	  *
	  * @access public
	  * @static
	  *
	  * @param $filename - full filename (without path).
	  * @return boolean - true if file is a graphical file.
	  */
	function isGraphicalFile($filename = "") {
	 
		$fileExt = BrowserHelper :: getFileExtension($filename);
		
		if( $fileExt == 'jpeg' || $fileExt == 'jpg' || $fileExt == 'gif' || $fileExt == 'png') {
			return true;	
		}
		
		return false;
	}
	
	
	/**
	  * Method returns full path to thumbnail file based on original file location. Thumbnail directory is definied in 
	  * configuration by property <i>browser.thumbnail.directory</i>, thumbnail file name is the same as name of the 
	  * original file and additionally contains at the end size of file, based on properties <i>browser.thumbnail.max.width</i> 
	  * and <i>browser.thumbnail.max.height</i>.
	  *
	  * @access public
	  * @static
	  *
	  * @param $filename - full path to file.
	  * @return string - full path to file thumbnail.
	  */
	function getThumbnailPath($filePath = "") {
	 
		$filename = basename($filePath);
		$fileExt = BrowserHelper :: getFileExtension($filename);
		$filenameWithoutExt = substr($filename, 0, strlen($filename) - strlen($fileExt) - 1);
		
		$thumbnailDirectory = dirname($filePath) . Browser_Utilities :: getSeparator() .
			Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.directory");
			
		if ( !is_dir( $thumbnailDirectory ) ) { mkdir($thumbnailDirectory); }
		
		return 	dirname($filePath) . Browser_Utilities :: getSeparator() .
			Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.directory") . Browser_Utilities :: getSeparator() .
			$filenameWithoutExt . "_". 
			Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.max.width"). "-".
			Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.max.height") . "." . $fileExt;
	}
	
	
	/**
	  * Method returns true id thumbnail could be created. 
	  * It checks if file is a graphical file, if yes, then thumbnail could be created.
	  *
	  * @access public
	  * @static
	  * @todo check if required gd lib functions are available!
	  *
	  * @param $filename - just a name, without path, "pict.jpg" is ok, but "picts/pict.jpg" is not ok!
	  * @return boolean - true if thumbnail is possible for given file.
	  */
	function isThumbnailPossible($filename = "") {
	 
		if ( BrowserHelper :: isGraphicalFile($filename)) {
			return true;
		}
		
		return false;
	}
	
	
	/**
	  * Method deletes thumbnail for given file.
	  *
	  * @access public
	  * @static
	  *
	  * @param fileLocation - absolute path to file like "/home/www/public/picts/pict.jpg".
	  */
	function deleteThumbnail($fileLocation = "") {
	 
		// check if for that type of file thumbnail is possible
		if ( BrowserHelper :: isThumbnailPossible(basename($fileLocation))) {
			
			$thumbnailLocation = dirname($fileLocation) . 
						Browser_Utilities :: getSeparator().Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.directory").
						Browser_Utilities :: getSeparator().basename($fileLocation);
			
			Browser_Utilities :: log("[BrowserHelper.deleteThumbnail] removing: ". $thumbnailLocation, "info");
			
			unlink($thumbnailLocation);
		}
	}
	

	/**
	  * Method loads information about mime types and returns mime type associated with given file extension.
	  *
	  * Note that method loads external file 'conf/mime.types.properties' to find information about extentions and 
	  * associated with them mime types 
	  *
	  * @access public
	  * @static
	  *
	  * @param filename - name of the file for which mime type will be returned.
	  * @return string - mime type.
	  */
	function getMimeType($filename) {
		$mimeTypes = Browser_Utilities :: loadProperites("conf".Browser_Utilities :: getSeparator() ."mime.types.properties");
		$fileExt = BrowserHelper :: getFileExtension($filename);	
		
		$mimeType = $mimeTypes['default'];
		
		if ( isset($mimeTypes[ $fileExt ]) ) {
			$mimeType = $mimeTypes[ $fileExt ];
		}
		
		return $mimeType;
	}
	
	
	/**
	  * Method erase files and directories (including all subtree).
	  *
	  * @access public
	  * @static
	  *
	  * @param mixed $path - must be a file name (foo.txt), or directory name.
	  * @return boolean - true if entry was successfully deleted.
	  */
	function rm($path) {
		Browser_Utilities :: log("[BrowserHelper.rm] path: " .$path, "info" );
		if ( !is_dir( $path ) ) {
			// delete file
			return unlink($path);
		}
			
		$d = dir($path);
		$result = true;
		
		while (false !== ($entry = $d->read())) {
			
			if ($entry == "." || $entry == "..") continue;
			$result = BrowserHelper :: rm( $path . Browser_Utilities :: getSeparator() . $entry );
		}
		
		$d->close();
		
		// check if all files and subdirectories was deleted successfully
		if ( !$result ) {
			return $result;
		}
		
		// delete dir
		return rmdir($path);
	}
	
	/**
	  * Method moves directory or file from one localisation to another. If source is a directory
	  * then method will recursively move all files and subdirectories.
	  *
	  * @access public
	  * @static
	  *
	  * @param string $source - path to file or directory which will be moved. 
	  * @param string $destination - path to file or directory where file will be moved. 
	  * @return boolean set to true if entry was moved successfully.
	  */
	function moveEntry($source, $destination) {
		
		if ( is_dir($source) ) {
			
			if (!mkdir($destination)) { return false; }
			Browser_Utilities :: log("[BrowserHelper.moveEntry] directory created [".$destination."] ", "info" );

			$d = dir($source);
			
			while (false !== ($entry = $d->read())) {
				
				if ($entry == "." || $entry == "..") continue;
				
				$sourcePath =  $source . Browser_Utilities :: getSeparator() . $entry;
				$destinationPath = $destination . Browser_Utilities :: getSeparator() . $entry;

				Browser_Utilities :: log("[BrowserHelper.moveEntry] checking [".$sourcePath."] ", "debug" );
				
				if (is_dir($sourcePath)) {
					if (!BrowserHelper :: moveEntry($sourcePath, $destinationPath)) { return false; }
				} else {
					if (!rename($sourcePath, $destinationPath)) { return false; }
				}
			}
			
			$d->close();
			rmdir($source);
			
			return true;
		} else {
			return rename($source, $destination);
		}
	}
	
	
	/**
	  * Method copies directory or file from one localisation to another. If source is a directory
	  * then method will recursively copy all files and subdirectories.
	  *
	  * @access public
	  * @static
	  *
	  * @param string $source - path to file or directory which will be copied. 
	  * @param string $destination - path to file or directory where file will be copied. 
	  * @return boolean set to true if entry was copied successfully.
	  */
	function copyEntry($source, $destination) {
		
		if ( is_dir($source) ) {
			
			if (!mkdir($destination)) { return false; }
			Browser_Utilities :: log("[BrowserHelper.copyEntry] directory created [".$destination."] ", "info" );

			$d = dir($source);
			
			while (false !== ($entry = $d->read())) {
				
				if ($entry == "." || $entry == "..") continue;
				
				$sourcePath =  $source . Browser_Utilities :: getSeparator() . $entry;
				$destinationPath = $destination . Browser_Utilities :: getSeparator() . $entry;

				Browser_Utilities :: log("[BrowserHelper.copyEntry] checking [".$sourcePath."] ", "debug" );
				
				if (is_dir($sourcePath)) {
					if (!BrowserHelper :: copyEntry($sourcePath, $destinationPath)) { return false; }
				} else {
					if (!copy($sourcePath, $destinationPath)) { return false; }
				}
			}
			
			$d->close();
			return true;
		} else {
			return copy($source, $destination);
		}
	}
}
?>
