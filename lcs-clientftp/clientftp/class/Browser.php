<?php
/**
  * Class exposes core functionality of Online File Browser application. Its purpose is
  * to be facade for clients. 
  *
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */

/**
  * Class exposes core functionality of Online File Browser application. Its purpose is
  * to be facade for clients. Clients can then expose functionality for external world in various 
  * different ways: AJAX, Web Service.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */
class Browser {
	
	/**
	  * Variable holds information about base path (root) for Online File Browser. 
	  *
	  * @access public
	  * @var string 
	  */
	var $basepath;
	
	/**
	  * Variable holds information about authenicated user login. 
	  *
	  * @access public
	  * @var string 
	  */
	var $username;
	
	
	/**
	 * Default constructor. It calculates value of <i>basepath</i> variable.
	 *
	 * @access public
	 */
	function Browser() {

		$this->basepath = Browser :: getBrowserRoot();
		Browser_Utilities :: log("Browser user: [" . $_SESSION[BROWSER_AUTHENTICATED_USER] . "] base dir: [".$this->basepath."]", "info");
	}
		
	
	/**
	  * Method reads from configuration <i>browser.root</i> property and then based on that returns absolute path which 
	  * is a root for Online File Browser users. 
	  *
	  * Property from core configuration <i>browser.root</i> can be override by custom user value from 
	  * user.properties file.
	  *
	  * It also checks if defined directory exists, if not then  new directory 'BROWSER_BASE/public-ofb-root' 
	  * will be created and used as a root.
	  *
	  * Please note that in configuration files user can use absolute or relative path, but this method
	  * will always return absolute path.
	  *
	  * @access public
	  * @static
	  *
	  * @return string which represents absolute path for Online File Browser root.
	  */
	function getBrowserRoot() {
		global $user;
		$browserRoot = "";
		#$dir = Browser_Utilities :: getValueFromConfiguration("browser.root");
		$dir="/home/".$user;
		// check if user has a custom root
		if ( isset($_SESSION[BROWSER_AUTHENTICATED_USER_ROOT]) ) {
			$dir = $_SESSION[BROWSER_AUTHENTICATED_USER_ROOT];
		}
		
		// check if it is absoulute path
		// assumtion: unix type paths e.g. /home/domain/www/sth
		if (substr($dir,0,1) == "/") {
			$browserRoot = $dir;
		}
		else {
			// it's relative path
			if ( strlen($dir) > 0 && $dir != "." ) {
				$browserRoot = BROWSER_BASE . Browser_Utilities :: getSeparator() . $dir;
			} else {
				$browserRoot = BROWSER_BASE;
			}
		}
		
		if ( !is_dir($browserRoot) ) {
			Browser_Utilities :: log("[Browser.getBrowserRoot] root: [".$browserRoot."] is not a directory, OFB attempt to use BROWSER_BASE/public-ofb-root directory as a root.", "fatal");
			
			// create directory 'public-ofb-root' if not exists
			$newDir = BROWSER_BASE . Browser_Utilities :: getSeparator() .'public-ofb-root';
			if ( !is_dir( $newDir ) ) { 
				mkdir( $newDir ); 
				Browser_Utilities :: log("[Browser.getBrowserRoot] directory: [".$newDir."] created", "info");
			}
			
			$browserRoot = $newDir;
		}
		
		return realpath($browserRoot);
	}
	
	/**
	  * Method reads from configuration <i>browser.templates.directory</i> and <i>template.set.name</i> 
	  * properties and then based on that returns absolute path to templates.
	  *
	  * Please note that in configuration files user can use absolute or relative path, but this method
	  * will always return absolute path.
	  *
	  * @access public
	  * @static
	  *
	  * @return string which represents absolute path to templates.
	  */
	function getTemplatesRoot() {
		
		$templatesDir = "";
		$dir = Browser_Utilities :: getValueFromConfiguration("browser.templates.directory");
		$templatesSet = Browser_Utilities :: getValueFromConfiguration("template.set.name");
		
		// user specific templates set ?
		
		// check if it is absoulute path
		// assumtion: unix type paths e.g. /home/domain/www/sth
		if (substr($dir,0,1) == "/") {
			$templatesDir = $dir;
		}
		else {
			// it's relative path
			if ( strlen($dir) > 0 && $dir != "." ) {
				$templatesDir = BROWSER_BASE . Browser_Utilities :: getSeparator() . $dir;
			} else {
				$templatesDir = BROWSER_BASE;
			}
		}
		
		// add template name
		$templatesDir = $templatesDir . Browser_Utilities :: getSeparator() . $templatesSet;
		
		if ( !is_dir($templatesDir) ) {
			Browser_Utilities :: log("[Browser.getTemplatesRoot] location: [".$templatesDir."] is not a directory", "fatal");
			$templatesDir = BROWSER_BASE;
		}
		
		return realpath($templatesDir);
	}
	
	
	/**
	  * Method validates filename, it checks if extension is allowed. (based on configuration file, 
	  * property <i>browser.upload.allowed.extension</i>). If property is set to '*' then this method will
	  * always return true, as a result all extensions will be valid.
	  *
	  * @access public
	  * @static
	  *
	  * @param string $filename - name of the file to be validated. 
	  * @return boolean set to true when file name is allowed (valid).
	  */
	function isFileNameValid($filename) {

		$fileExt = BrowserHelper :: getFileExtension(basename($filename));
				
		Browser_Utilities :: log("[Browser.isFileNameValid] file ext: ".$fileExt, "debug");
		
		$allowedExtensions = Browser :: getAllowedExtensions();
		
		// asterisk means that all files are allowed to be uploaded
		if ($allowedExtensions == '*') { 
			return true; 
		}
		
		$allowedExtensionsArray = split(",", $allowedExtensions);
		
		foreach($allowedExtensionsArray as $ext) {
			if (strtolower($ext) == strtolower($fileExt)) {
				
				Browser_Utilities :: log("[Browser.isFileNameValid] file ext: ".$fileExt." is allowed", "info");
				return true;
			}
		}
		
		Browser_Utilities :: log("[Browser.isFileNameValid] file ext: ".$fileExt." is not allowed", "info");
		return false;
	}
	
	
	/**
	  * Method validates filename, it checks if extension is editable. (based on configuration file, 
	  * property <i>browser.editable.extensions</i>).
	  *
	  * @access public
	  * @static
	  *
	  * @param string $filename - name of the file to be validated. 
	  * @return boolean set to true when file name is editable.
	  */
	function isFileEditable($filename) {
		$fileExt = BrowserHelper :: getFileExtension(basename($filename));
				
		Browser_Utilities :: log("[Browser.isFileEditable] file ext: ".$fileExt, "debug");
		
		$editableExtensions = Browser :: getEditableExtensions();
		$editableExtensionsArray = split(",", $editableExtensions);
		
		foreach($editableExtensionsArray as $ext) {
			if (strtolower($ext) == strtolower($fileExt)) {
				
				Browser_Utilities :: log("[Browser.isFileEditable] file ext: ".$fileExt." is editable", "debug");
				return true;
			}
		}
		
		Browser_Utilities :: log("[Browser.isFileEditable] file ext: ".$fileExt." is not editable", "debug");
		return false;
	}
	
	
	/**
	  * Method reads from configuration <i>browser.upload.allowed.extension</i> property and 
	  * return it. 
	  *
	  * @access public
	  * @static
	  *
	  * @return string which is a comma separated list of allowed extensions.
	  */
	function getAllowedExtensions() {
		return Browser_Utilities :: getValueFromConfiguration("browser.upload.allowed.extension");
	}
	
	
	/**
	  * Method reads from configuration <i>browser.editable.extensions</i> property and 
	  * return it. 
	  *
	  * @access public
	  * @static
	  *
	  * @return string which is a comma separated list of editable extensions.
	  */
	function getEditableExtensions() {
		return Browser_Utilities :: getValueFromConfiguration("browser.editable.extensions");
	}
	
	
	/**
	  * Method reads from configuration <i>icon.set.name</i> property and 
	  * return it. 
	  *
	  * @access public
	  * @static
	  *
	  * @return string which is a name of a set of the icons.
	  */
	function getIconSetName() {
		return Browser_Utilities :: getValueFromConfiguration("icon.set.name");
	}
	
	/**
	  * Method reads from configuration <i>browser.view.mode</i> property and 
	  * return it. 
	  *
	  * @access public
	  * @static
	  *
	  * @return string which is a view mode.
	  */
	function getViewMode() {
		return Browser_Utilities :: getValueFromConfiguration("browser.view.mode");
	}
	
	/**
	  * Method creates new directory in specified path. Parameter path is a full path relative to root (basepath).
	  * For instance, for basepath = /home/www/public and path = existing/newOne
	  * new directory will be created in following localisation: /home/www/public/existing/newOne
	  *
	  * @access public
	  *
	  * @param string $pathToNewDir - path to new directory (including new directory) relative to root. 
	  * @return boolean is set to true if directory was created successfully.
	  */
	function makeDir($pathToNewDir) {
		global $user;
		$fullPath = $this->basepath. Browser_Utilities :: getSeparator() . trim($pathToNewDir);
		$fullPath = trim(dirname($fullPath)) . Browser_Utilities :: getSeparator() .  trim(basename($fullPath));
		
		Browser_Utilities :: log("[makeDir] creating entry: " .$fullPath, "info" );
		
		if (mkdir($fullPath)) {
                        # LCS jLCF modif
			#chmod($fullPath, octdec(Browser_Utilities :: getValueFromConfiguration("browser.create.directory.permissions")));
                        exec ("/usr/bin/sudo /usr/share/lcs/scripts/chacces.sh 770 $user $fullPath");
			return true;
		} 
		
		return false;
	}
	
	
	/**
	  * Method creates new file in specified path. Parameter path is a full path relative to root (basepath).
	  * For instance, for basepath = /home/www/public and path = existing/newFile.txt
	  * new file will be created in following localisation: /home/www/public/existing/newFile.txt
	  *
	  * Please note that file will be created only if its extension imply that file is editable, in other words,
	  * extension of new file must be on list <i>browser.editable.extensions</i> in configuration file.
	  *
	  * @access public
	  *
	  * @param string $pathToNewFile - path to new file (including new file) relative to root. 
	  * @return boolean is set to true if file was created successfully.
	  */
	function makeFile($pathToNewFile) {
		global $user;
		$fullPath = $this->basepath. Browser_Utilities :: getSeparator() . trim($pathToNewFile);
		$fullPath = trim(dirname($fullPath)) . Browser_Utilities :: getSeparator() .  trim(basename($fullPath));
		
		// check if file extension is editable = allowed to be created
		if ( !Browser :: isFileEditable($fullPath) ) {
			Browser_Utilities :: log("[makeFile] file is not editable: " .$fullPath, "warn" );
			return false;
		}
		
		Browser_Utilities :: log("[makeFile] creating entry: " .$fullPath, "info" );
		
		$handle = fopen($fullPath, "x");
		
		if ($handle) {
			fclose($handle);
			#chmod($fullPath, octdec(Browser_Utilities :: getValueFromConfiguration("browser.create.file.permissions")));
                        exec ("/usr/bin/sudo /usr/share/lcs/scripts/chacces.sh 660 $user $fullPath");
			Browser_Utilities :: log("[makeFile] created successfully!", "info" );
			return true;
		} 
		
		Browser_Utilities :: log("[makeFile] creation failed!", "warn" );
		return false;
	}
	
	
	/**
	  * Method returns all entries (directories and files) in specified location. Location is a relative path to root (basepath).
	  * For instance, for basepath = /home/www/public and path = dir1/dir2 array of entries will be returned from 
	  * following localisation: /home/www/public/dir1/dir2
	  *
	  * Please note that directory where thumbnails are stored could be excluded from results. It can be specified in 
	  * configuration file - <i>browser.thumbnail.directory.hide</i> property.
	  *
	  * @access public
	  *
	  * @param string $path - path from where array of entries will be returned, path is relative to root. 
	  * @return array of <i>Entry</i> objects.
	  *
	  * @see Entry
	  */
	function getEntries($path) {

		$relativePath = $this->getRelativePath($path);
		$currentLocation = realpath($this->basepath . $relativePath);
	
		// get list of entries (raw data, result of php functions)
		$list = $this->getListOfEntries( $currentLocation );
	
		Browser_Utilities :: log("[getEntries] location: " .$currentLocation, "info" );
	
		$listOfEntries = array();
		$counter = 0;
		
		foreach ( $list as $entry ) {
			
			$pathToEntry = $currentLocation.Browser_Utilities :: getSeparator().$entry;
			
			// check if thumbnail directory should be hidden
			if ( Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.directory.hide") == "true" 
				&& Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.directory") == $entry ) {
					
					// skip that entry
					continue;
			}
			
			$entryObj =& new Entry();
			$entryObj->name = $entry;
			$entryObj->size = filesize($pathToEntry);
			$entryObj->type = filetype($pathToEntry);
			$entryObj->relativePath = substr($relativePath.Browser_Utilities :: getSeparator().$entry,1);
			$entryObj->thumbnail = BrowserHelper :: isThumbnailPossible($entry);
			if (is_dir($pathToEntry) ) { $entryObj->editable = false; }
			else { $entryObj->editable = Browser :: isFileEditable($pathToEntry); }
			$entryObj->lastModify = date ("M d Y H:i:s", filemtime($pathToEntry));
			$entryObj->permissions = BrowserHelper :: getPermissions($pathToEntry);
			
			$listOfEntries[$counter++] = $entryObj;
			
			Browser_Utilities :: log("[getEntries] adding entry to list: " .$entry, "debug" );
		}
		
		return $listOfEntries;
	}
	
	
	
	/**
	  * Method returns all entries (directories and files) in specified location. These are raw data, result of php functions.
	  *
	  * @access private
	  *
	  * @param string $path - absolute path from where array of entries will be returned. 
	  * @return array of names.
	  */
	function getListOfEntries( $path )
	{
		if ( !is_dir( $path ) ) return array();
			
		$d = dir($path);
		$listOfEntries = array();
		$counter = 0;
				
		while (false !== ($entry = $d->read())) {
			if ($entry == ".") continue;
                        # Modif LCS par jLCF 06/06/08
                        $pub= explode('/', $path);
                        if ( $entry == ".htaccess" && $pub[1] == "home" && $pub[3] == "public_html" && ! isset($pub[4]) )
                            $nodisplay = true;
                        #system ("echo '$pub[0] | $pub[1] | $pub[2] | $pub[3] | $pub[4] | $entry | $nodisplay' >> /tmp/ofb.log");
                        if ($entry != "Maildir" && $entry != "bin" && $entry != "lib" && $entry != "usr" && ! $nodisplay )
			$listOfEntries[$counter++] = $entry;
		}

		$d->close();
		return $listOfEntries;
	}
	
	
	/**
	  * Method returns content of file from specified location. Location is a relative path to root (basepath).
	  * For instance, for basepath = /home/www/public and path = dir1/file.txt file content will be returned from 
	  * following localisation: /home/www/public/dir1/file.txt
	  *
	  * Please note that file content will be returned only if its extension imply that file is editable, in other words,
	  * extension of new file must be on list <i>browser.editable.extensions</i> in configuration file.
	  *
	  * @access public
	  *
	  * @param string $path - path to file which content should be returned, path is relative to root. 
	  * @return string - file content.
	  */
	function getFileContent($path) {
		
		$currentLocation = $this->basepath . $this->getRelativePath($path);
		if (!file_exists($currentLocation) || $currentLocation == $this->basepath) { 
			Browser_Utilities :: log("[getFileContent] source file [" . $currentLocation . "] doesn't exists! ", "error" );
			return false;
		}
		
		Browser_Utilities :: log("[getFileContent] location: " .$currentLocation, "info" );
		
		if (file_exists($currentLocation) && Browser :: isFileEditable($currentLocation)) {
			
			#$output = file_get_contents($currentLocation);
                        $output = iconv('ISO-8859-1','UTF-8',file_get_contents($currentLocation));


			if ($result === FALSE) {
				Browser_Utilities :: log("[getFileContent] error while reading file: " .$currentLocation, "error" );
				return "";
			}
			
			return $output;
		}
		
		Browser_Utilities :: log("[getFileContent] file is not editable or not exists: " .$currentLocation, "info" );
		return "";
	}
	
	
	/**
	  * Method saves content to the file in specified location. Location is a relative path to root (basepath).
	  * For instance, for basepath = /home/www/public and path = dir1/file.txt file content will be saved to 
	  * following localisation: /home/www/public/dir1/file.txt
	  *
	  * Please note that file content will be saved only if its extension imply that file is editable, in other words,
	  * extension of new file must be on list <i>browser.editable.extensions</i> in configuration file.
	  *
	  * @access public
	  *
	  * @param string $path - path to file where content should be saved, path is relative to root. 
	  * @param string $content - content which will be saved. 
	  * @return boolean set to true if update was successfull.
	  */
	function saveFileContent($path, $content) {
		$content=iconv('UTF-8','ISO-8859-1',$content);
		$currentLocation = $this->basepath . $this->getRelativePath($path);
		if (!file_exists($currentLocation) || $currentLocation == $this->basepath) { 
			Browser_Utilities :: log("[saveFileContent] source file [" . $currentLocation . "] doesn't exists! ", "error" );
			return false;
		}
		
		Browser_Utilities :: log("[saveFileContent] path: " .$currentLocation, "info" );
		
		if (file_exists($currentLocation) && Browser :: isFileEditable($currentLocation)) {
			$f=fopen($currentLocation, 'a+');
			ftruncate($f, 0);
			fwrite($f, $content);
			fclose($f);
			
			Browser_Utilities :: log("[saveFileContent] update successfull, saved bytes: " . filesize($currentLocation), "info" );
			return true;
		}
		
		Browser_Utilities :: log("[saveFileContent] update failed!", "warn" );
		return false;
	}	
	
	
	
	/**
	  * Method updates entry (file or directory) name. Location of entry is a relative path to root (basepath).
	  * For instance, for basepath = /home/www/public and path = dir1/file.txt file in following 
	  * localisation: /home/www/public/dir1/file.txt will be updated.
	  *
	  * Note that you can always change name of directory but file can be updated if:
	  * - old name and new name are editable (extensions must be on list <i>browser.editable.extensions</i> in configuration file),
	  * - update won't change a file extension, for instance it's possible to change name of a file 'test.pdf' into 't2.pdf' even if file extension 'pdf' is not on the list of editable extensions.
	  *
	  * @access public
	  * @todo if it is a graphical file, then check if thumbnail name could be also updated.
	  *
	  * @param string $path - path to file or directory which will be updated. 
	  * @param string $name - new name for file or directory.
	  * @return boolean set to true if update was successfull.
	  */
	function updateEntryName($path, $name) {
		
		$relativePath = $this->getRelativePath($path);
		$currentLocation = realpath($this->basepath . $relativePath);
		
		if ($currentLocation == $this->basepath) { 
			Browser_Utilities :: log("[updateEntryName] can't rename directory: [" .$path . "] because it doesn't exist", "info" );
			return false; 
		}
		
		Browser_Utilities :: log("[updateEntryName] path: " .$currentLocation . " new name: " . $name, "info" );
// jLCF Modification test pour renommage .htaccess.txt	
		if (file_exists($currentLocation)) {
			if (	(is_file($currentLocation) && Browser :: isFileEditable($currentLocation) && Browser :: isFileEditable($name) ) || 
				 is_dir($currentLocation) || 
				(is_file($currentLocation) && BrowserHelper :: getFileExtension($name) ==  BrowserHelper :: getFileExtension(basename($currentLocation)) ) ||
                                (  ereg ("/.htaccess.txt$", $currentLocation ) )
			) {
				if( rename($currentLocation, dirname($currentLocation) . Browser_Utilities :: getSeparator() . trim($name)) ) {
					Browser_Utilities :: log("[updateEntryName] update successfull", "info" );
					
					// if it's a graphical file then delete old thumbnail
					BrowserHelper :: deleteThumbnail($currentLocation);
					return true;
				} else {
					Browser_Utilities :: log("[updateEntryName] update failed", "info" );
					return false;
				}
			} else {
				Browser_Utilities :: log("[updateEntryName] new name is not allowed", "info" );
				return false;
			}
		}
		
		Browser_Utilities :: log("[updateEntryName] file or directory doesn't exist", "warn" );
		return false;
	}
	
	
	/**
	  * Method copies all entries from source location to destination. Entry could be a file or directory.
	  * Parameter $entries is a a list (separated by '|' character) of entries to copy. Method checks if source
	  * entries don't exist, if destination is a directory and if source and destination files(directories) are not the same.
	  *
	  * @access public
	  *
	  * @param string $entries - list of paths to file or directory (separated by '|' character) which will be copied. 
	  * @param string $destination - path to a directory where entries will be copied. 
	  * @return boolean set to true if entries was copied successfully.
	  */
	function copyEntries($entries, $destination) 
	{
		Browser_Utilities :: log("[copyEntries] entries: [".$entries."] destination: [".$destination."]", "info" );
		
		$entriesArray = explode("|", $entries);
		
		foreach($entriesArray as $entry) {
			
			if ($entry == "") continue;
			
			Browser_Utilities :: log("[copyEntries] checking [" . $entry . "] ", "info" );
			
			$source = $this->basepath . $this->getRelativePath($entry);
			if (!file_exists($source) || $source == $this->basepath) { 
				Browser_Utilities :: log("[copyEntries] source file [" . $source . "] doesn't exists! ", "error" );
				return false;
			}
			
			$destinationDirectory = $this->basepath . Browser_Utilities :: getSeparator() . $destination ;
			if (!is_dir($destinationDirectory)) {
				Browser_Utilities :: log("[copyEntries] destination directory doesn't exists!", "error" );
				return false; 
			}
			
			$destinationFile = realpath($destinationDirectory). Browser_Utilities :: getSeparator() . basename($source);
			if (file_exists($destinationFile)) {
				Browser_Utilities :: log("[copyEntries] destination file already exists!", "error" );
				return false; 
			}
			
			if (strpos($destinationFile, realpath($source)) !== false) { 
				Browser_Utilities :: log("[copyEntries] can't copy directory to its subdirectory!", "error" );
				return false; 
			}
			
			Browser_Utilities :: log("[copyEntries] source: [".$source."] destination: [".$destinationFile."]", "info" );
			
			if ( BrowserHelper :: copyEntry($source, $destinationFile) ) {
				Browser_Utilities :: log("[copyEntries] success! ", "info" );
			} else {
				Browser_Utilities :: log("[copyEntries] failure! ", "error" );
				return false;
			}
		}
		
		return true;
	}
	
	
	/**
	  * Method moves all entries from source location to destination. Entry could be a file or directory.
	  * Parameter $entries is a a list (separated by '|' character) of entries to move. Method checks if source
	  * entries don't exist, if destination is a directory and if source and destination files(directories) are not the same.
	  *
	  * @access public
	  *
	  * @param string $entries - list of paths to file or directory (separated by '|' character) which will be moved. 
	  * @param string $destination - path to a directory where entries will be moved. 
	  * @return boolean set to true if entries was moved successfully.
	  */
	function moveEntries($entries, $destination) {
		
		Browser_Utilities :: log("[moveEntries] entries: [".$entries."] destination: [".$destination."]", "info" );
		
		$entriesArray = explode("|", $entries);
		
		foreach($entriesArray as $entry) {
			
			if ($entry == "") continue;
			
			Browser_Utilities :: log("[moveEntries] checking [" . $entry . "] ", "info" );
			
			$source = $this->basepath . $this->getRelativePath($entry);
			if (!file_exists($source) || $source == $this->basepath) { 
				Browser_Utilities :: log("[moveEntries] source file [" . $source . "] doesn't exists! ", "error" );
				return false;
			}
			
			$destinationDirectory = $this->basepath . Browser_Utilities :: getSeparator() . $destination ;
			if (!is_dir($destinationDirectory)) {
				Browser_Utilities :: log("[moveEntries] destination directory doesn't exists!", "error" );
				return false; 
			}
			
			$destinationFile = realpath($destinationDirectory). Browser_Utilities :: getSeparator() . basename($source);
			if (file_exists($destinationFile)) {
				Browser_Utilities :: log("[moveEntries] destination file already exists!", "error" );
				return false; 
			}
			
			if (strpos($destinationFile, realpath($source)) !== false) { 
				Browser_Utilities :: log("[moveEntries] can't copy directory to its subdirectory!", "error" );
				return false; 
			}
			
			Browser_Utilities :: log("[moveEntries] source: [".$source."] destination: [".$destinationFile."]", "info" );
			
			if ( BrowserHelper :: moveEntry($source, $destinationFile) ) {
				Browser_Utilities :: log("[moveEntries] success! ", "info" );
			} else {
				Browser_Utilities :: log("[moveEntries] failure! ", "error" );
				return false;
			}
		}
		
		return true;
	}
	
	
	/**
	  * Method deletes entry (file or directory). Location of entry is a relative path to root (basepath).
	  * For instance, for basepath = /home/www/public and path = dir1/dir2 directory in following 
	  * localisation: /home/www/public/dir1/dir2 will be deleted. 
	  *
	  * Note that if variable <i>path</i> points on directory then this directory and whole content inside will be deleted.
	  *
	  * @access public
	  *
	  * @param string $path - path to file or directory which will be deleted. 
	  * @return boolean set to true if entry was deleted successfully.
	  */
	function deleteItem($path) {
		Browser_Utilities :: log("[deleteItem] path: " .$path, "info" );
	   if ( $path != "public_html/.htaccess") {	
		$relativePath = $this->getRelativePath($path);
		$currentLocation = realpath($this->basepath . $relativePath);
		
		if ($currentLocation == $this->basepath) { 
			Browser_Utilities :: log("[deleteItem] can't remove entry: [" .$path . "] because it doesn't exist", "error" );
			return false; 
		}
		
		
		Browser_Utilities :: log("[deleteItem] full path: " .$currentLocation, "info" );
		
		if (file_exists($currentLocation)) {
			if (BrowserHelper :: rm($currentLocation)) {
				Browser_Utilities :: log("[deleteItem] item deleted successfully", "info" );
				
				// if it was a graphical file then check if thumbnail still exists
				BrowserHelper :: deleteThumbnail($currentLocation);
				return true;
			}
			
			Browser_Utilities :: log("[deleteItem] unable to deleted!", "warn" );
			return false;
		}
		
		Browser_Utilities :: log("[deleteItem] entry doesn't exist!", "warn" );
		return false;
            }
	}
	
	
	/**
	  * Method takes path which is relatetive to root, cleans it and returns clean relative path.
	  *
	  * For instance '../../dirname' will be replaced with 'something/else/dirname'.
	  * Method uses internally realpath() PHP method therefore if directory doesn't exist method returns empty string. 
	  *
	  * @access public
	  * @static
	  *
	  * @param string $path - path to file or directory. 
	  * @return string which represents relative path.
	  */
	function getRelativePath($path) {
		
		if (!isset($path)) { $path = "."; }
		
		$tempBasepath = realpath(Browser :: getBrowserRoot(). Browser_Utilities :: getSeparator() .$path);
		$relativePath = substr($tempBasepath,  strlen( Browser :: getBrowserRoot() ) );
		return $relativePath;
	}
	
	
	/**
	  * Method returns start location for OFB. Default value is "." which will set initial location to
	  * OFB root. Method checks if URL parameter <i>REQUEST_START_LOCATION</i> is defined, it uses value of this
	  * parameter to set custom start location.
	  *
	  * @access public
	  * @static
	  *
	  * @return string which represents start location for OFB client (browser).
	  */
	function getStartLocation() {
		
		$startLocation = ".";
		
		if ( isset($_REQUEST[REQUEST_START_LOCATION]) ) { 
			if ( is_dir($this->basepath . $this->getRelativePath($_REQUEST[REQUEST_START_LOCATION])) && $this->getRelativePath($_REQUEST[REQUEST_START_LOCATION]) != "" ) { 
				$startLocation = $_REQUEST[REQUEST_START_LOCATION];
			} else { Browser_Utilities :: log("[getStartLocation] directory [".$this->basepath . $this->getRelativePath($_REQUEST[REQUEST_START_LOCATION])."] doesn't exist!", "warn" ); }
		}
		
		return $startLocation;
	}
}
?>
