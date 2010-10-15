<?php
/**
  * Class represents upload action.
  *
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */


/**
  * Class represents upload action, it moves uploaded file into final destination. Class also performs validation, checks if file name is valid
  * based on Browser :: isFileNameValid() method. 
  *
  * If uploaded file is a graphical file and property in configuration file <i>browser.thumbnail.create</i> is set to true then creates thumbnail.
  * 
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser.action
  */
class UploadFormAction extends ActionTemplate {
	
	/**
	  * Process upload file, checks if file is valid, and saves file in final destination. 
	  * If it's a graphical file then thumbnail can be created (depends on configuration).
	  *
	  * @access public
	  */
	function perform() {
		global $user;
		$templateData = array();
	
		$smarty =& $this->getSmarty();
		$smarty->assign('result', 'failed');
		
		if(isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                        # Debut LCS jLCF modif 1
                        $_FILES['file']['name'] = utf8_encode($_FILES['file']['name']);
                        # Fin LCS jLCF modif 1						
			// base + relative
			$relativePath = Browser :: getRelativePath($_REQUEST['relativePath']);
			$directory = realpath(Browser :: getBrowserRoot() . $relativePath);
                        # Debut LCS jLCF modif 2
             		if ( $directory == "/home/$user" ) { 
                          Browser_Utilities :: log("[UploadFormAction.perform] path root upload not permit : $directory/". $_FILES['file']['name'], "warn");
                          $smarty->assign('result', 'failed');
                        } else {
                          # Fin LCS jLCF modif 2	
			  // base + relative + name
			  $uploadfile = $directory.
					Browser_Utilities :: getSeparator().
					$_FILES['file']['name'];
					
			  Browser_Utilities :: log("[UploadFormAction.perform] file uploaded: ". $uploadfile, "info");
			
			  // validate file
			  if (Browser :: isFileNameValid($_FILES['file']['name'])) {
				
				if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
					Browser_Utilities :: log("[UploadFormAction.perform] error while file move_uploaded_file: ". $_FILES['file']['name'], "error");
					$smarty->assign('result', 'failed');
				} else {
					// success !!
					$smarty->assign('result', 'success');
					# Debut LCS jLCF modif 3
					exec ("/usr/bin/sudo /usr/share/lcs/scripts/chacces.sh 660 $user $uploadfile");
                                        # test si archive auto unzip
                                        if ( ereg ("/lcsofbupload.zip$", $uploadfile ) ) {
                                            $dirtmp=$directory."/tmpupload";
                                            mkdir ($dirtmp);
                                            exec ("/usr/bin/unzip $uploadfile -d $dirtmp");
                                            exec ("/usr/bin/sudo /usr/share/lcs/scripts/chacces.sh 770 $user '$dirtmp/* -R'");
                                            exec ("/bin/mv $dirtmp/* $directory");
                                            exec ("/bin/rm -R $dirtmp");
                                            unlink ($uploadfile);
                                        }
                                        # Fin LCS jLCF modif 3
					// create thumbnail for a file
					// if it's a graphical file and if in configuration this option is enabled
					if (BrowserHelper :: isGraphicalFile($_FILES['file']['name']) && Browser_Utilities :: getValueFromConfiguration("browser.thumbnail.create") == "true") {
						UploadFormAction :: createAndSaveThumbnail($uploadfile);
					}
			       }
			} else {
				$smarty->assign('result', 'failed');
				Browser_Utilities :: log("[UploadFormAction.perform] file name is not allowed: ". $_FILES['file']['name'], "warn");
			}
                      } # LCS jLCF modif 2 fermeture accolade
		}
		
		$smarty->assign('labels', Browser_Utilities :: loadProperites("conf". Browser_Utilities :: getSeparator() .Browser_Utilities :: getValueFromConfiguration("resource.labels.file")));
		$smarty->display("uploadForm.tpl");
	}
	
	/**
	  * Method creates thumbnail.
	  *
	  * @access private
	  *
	  * @param uploadfile is a full path to uploaded file
	  */
	function createAndSaveThumbnail($uploadfile) {
		
		$directory = dirname($uploadfile);
		
		// create thumbnail in memory
		$image = ImagesUtils :: createThumbnail($uploadfile);
		$thumbnailFilename = BrowserHelper :: getThumbnailPath($uploadfile);   
		
		$fileExt =  BrowserHelper :: getFileExtension(basename($uploadfile));
		
		if ( $fileExt == 'jpeg' || $fileExt == 'jpg') {
			imagejpeg($image, $thumbnailFilename);
			Browser_Utilities :: log("[UploadFormAction.createAndSaveThumbnail] thumbnail created for file: ". basename($uploadfile), "info");
		} else if ($fileExt == 'gif') {
			imagegif($image, $thumbnailFilename);
			Browser_Utilities :: log("[UploadFormAction.createAndSaveThumbnail] thumbnail created for file: ". basename($uploadfile), "info");
		} else if ($fileExt == 'png') {
			imagepng($image, $thumbnailFilename);
			Browser_Utilities :: log("[UploadFormAction.createAndSaveThumbnail] thumbnail created for file: ". basename($uploadfile), "info");
		}
		
		imagedestroy($image);
	}
}

?>
