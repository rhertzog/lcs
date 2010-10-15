<?php
/**
  * Class provides core utilities for whole Online File Browser.  
  *
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */

/**
  * Class provides core utilities for whole Online File Browser.  
  *
  * @author Marek Blotny <marek@mbsoftware.pl>
  * @copyright Copyright &copy; 2006, Marek Blotny <{@link http://filebrowser.mbsoftware.pl http://filebrowser.mbsoftware.pl}>
  * @license http://www.opensource.org/licenses/mit-license.php The MIT License
  *
  * @package onlinefilebrowser
  */
class Browser_Utilities {

	/**
	  * Method loads external file with properties and returns array. It ignores lines 
	  * which starts with character <i>#</i>. The rest of lines should be in format 
	  * <i>property_name=property_value</i>.
	  *
	  * @access public
	  * @static
	  *
	  * @param string $filename - name of the file which contains properties.
	  * @return array with properties.
	  */
	function loadProperites($filename = "system.properties") {

		Browser_Utilities :: log("[Browser_Utilities.loadProperites] loading ".$filename, "debug");

		// read from file
		$filePtr= fopen($filename, "r");
		$output= fread($filePtr, filesize($filename));
		fclose($filePtr);

		// create a list 
		$list= explode("\n", $output);

		// convert
		$properties= array ();
		for ($i= 0; $i < sizeof($list); $i ++) {
			
			if (strpos(trim($list[$i]), "#") === false && strlen(trim($list[$i])) != 0) {
				$tempArray= explode("=", $list[$i]);
				$properties[trim($tempArray[0])] = trim($tempArray[1]);
				Browser_Utilities :: log("[Browser_Utilities.loadProperites] property key:[".trim($tempArray[0])."] found, value: [".trim($tempArray[1])."]", "debug");
			}
		}
		
		return $properties;
	}

	/**
	  * Method logs messages to log file. Name of log file is defined by <i>BROWSER_LOG_FILE</i> constant. 
	  * Default log level is defined by <i>BROWSER_LOG_LEVEL</i> constant. Possible log levels:
	  * - DEBUG
	  * - INFO
	  * - WARN
	  * - ERROR
	  * - FATAL
	  *
	  * If <i>BROWSER_LOG_LEVEL</i> is set to INFO then messages with level lower then INFO won't be saved to log file.
	  *
	  * @access public
	  * @static
	  *
	  * @param string $message - messages to be logged.
	  * @param string $level - log level.
	  */
	function log($message, $level= "debug") {

		$data = array ("DEBUG" => 0, "INFO" => 1, "WARN" => 2, "ERROR" => 3, "FATAL" => 4);
		
		if ($data[strtoupper($level)] >= $data[strtoupper(BROWSER_LOG_LEVEL)]) {
			
			$ip = (getenv('HTTP_X_FORWARDED_FOR')) ?  getenv('HTTP_X_FORWARDED_FOR') :  getenv('REMOTE_ADDR');
			
			$msgString = "[".strtoupper($level)."][" .$ip. "] ".date("G:i:s") . " " . $message . "\n";
			//echo $msgString."</br>";
			# LCS jLCF modif
			#$logPtr = @fopen('/var/log/lcs/ofb/'.date("m.d.y"). "_" . BROWSER_LOG_FILE, "a");
			$logPtr = @fopen('/var/log/lcs/'.BROWSER_LOG_FILE, "a");

			if($logPtr != NULL) {
				fwrite($logPtr, Browser_Utilities :: br2nl($msgString));
				fclose($logPtr);
				$logPtr = NULL;
			} 

		}
	}
	
	
	/**
	  * Method replaces or occurrances of <i>&lt;BR&gt;</i>, <i>&lt;br&gt;</i>, <i>&lt;BR/&gt;</i>, <i>&lt;br/&gt;</i> with new line sign.
	  *
	  * @access public
	  * @static
	  *
	  * @param string $data - text in which all occurrances will be replaced.
	  * @return string text with replaced values.
	  */
	function br2nl( $data ) {
		return preg_replace( '!<br.*>!iU', "\n", $data );
	}

	/**
	 * Method returns value for given key from global array $_BROWSER_CONFIGURATION.
	 *
	 * @access public
	 * @global array $_BROWSER_CONFIGURATION
	 * @static
	 *
	 * @param string $key - key for array.
	 * @return string value which is associated with given key.
	 */
	function getValueFromConfiguration($key, $logEnabled = true) {
		
		global $_BROWSER_CONFIGURATION;
		
		if ( !isset($_BROWSER_CONFIGURATION[$key]) ) {
			trigger_error("[Browser.getLogsRoot] There is no key: [" .$key. "] in properties", E_USER_ERROR);
			return "";
		}
		
		if ($logEnabled) {
			Browser_Utilities :: log("[Browser_Utilities.getValueFromConfiguration] returning from configuration: ".$_BROWSER_CONFIGURATION[$key], "debug");
		}
		
		return $_BROWSER_CONFIGURATION[$key];
	}
	
	
	
	/**
	 * Method includes all files which are in directores listed in array. 
	 * Array can also contains files, which will be included as well.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $includeDirs - array of localisations (or files) from which all files will be included.
	 */
	function includeRequiredClasses($includeDirs) {
		
		foreach ($includeDirs as $include) {

			Browser_Utilities :: log("[Browser_Utilities.includeRequiredClasses] checking ".$include, "debug");
		
			if (!is_dir($include)) {
				Browser_Utilities :: log("[Browser_Utilities.includeRequiredClasses] require_once ".$include, "debug");
				require_once ($include);
				continue;
			}

			$d= dir($include);

			while (false !== ($entry= $d->read())) {

				if ($entry == ".")
					continue;

				if (!is_dir($include.Browser_Utilities :: getSeparator().$entry)) {

					Browser_Utilities :: log("[Browser_Utilities.includeRequiredClasses]  require_once ".$include.Browser_Utilities :: getSeparator().$entry, "debug");
					require_once ($include. Browser_Utilities :: getSeparator() .$entry);
				}
			}

			$d->close();
		}

	}
	
	/**
	 * Method returns predefined path separator.
	 *
	 * @access public
	 * @static
	 *
	 * @returns string $includeDirs - path separator.
	 */
	function getSeparator() {
		return BROWSER_SEPARATOR;
	}
	
	/**
	 * Method checks if it's first user vists, it checks if session contains variable <i>FIRST</i> set to true. 
	 * If variable is not set then method logs that and set it to true. This is convenient method to log entrance of users on the web site.
	 *
	 * @access public
	 * @static
	 */
	function checkIfFirstVisit() {
		if ( !isset($_SESSION['FIRST']) ) {
			Browser_Utilities :: log("new user!", "info"); 
			$_SESSION['FIRST'] = true;
		}
	}
	
	/**
	 * Method returns text representation of object, it could be array, objects, arbitrary type. This is convenient method to log state of objects or arrays.
	 *
	 * Note that this method was made based on similar method from Smarty project.
	 *
	 * @access public
	 * @static
	 *
	 * @param mixed $var - object to be serialized.
	 * @param int $depth
	 * @param int $length - maximal length of variable values which will be serialized
	 */
	function smarty_modifier_debug_print_var($var, $depth= 0, $length= 80) {
		$_replace= array ("\n" => '<i>&#92;n</i>', "\r" => '<i>&#92;r</i>', "\t" => '<i>&#92;t</i>');
		if (is_array($var)) {
			$results= "<b>Array (".count($var).")</b>";
			foreach ($var as $curr_key => $curr_val) {
				$return= Browser_Utilities :: smarty_modifier_debug_print_var($curr_val, $depth +1, $length);
				$results .= "<br>".str_repeat('&nbsp;', $depth * 2)."<b>".strtr($curr_key, $_replace)."</b> =&gt; $return";
			}
		} else
			if (is_object($var)) {
				$object_vars= get_object_vars($var);
				$results= "<b>".get_class($var)." Object (".count($object_vars).")</b>";
				foreach ($object_vars as $curr_key => $curr_val) {
					$return= Browser_Utilities :: smarty_modifier_debug_print_var($curr_val, $depth +1, $length);
					$results .= "<br>".str_repeat('&nbsp;', $depth * 2)."<b>$curr_key</b> =&gt; $return";
				}
			} else
				if (is_resource($var)) {
					$results= '<i>'.(string) $var.'</i>';
				} else
					if (empty ($var) && $var != "0") {
						$results= '<i>empty</i>';
					} else {
						if (strlen($var) > $length) {
							$results= substr($var, 0, $length -3).'...';
						} else {
							$results= $var;
						}
						$results= htmlspecialchars($results);
						$results= strtr($results, $_replace);
					}
		return $results;
	}
	
	/**
	 * Function converts an Javascript escaped string back into a string with specified charset (default is iso-8859-1).
	 * Modified function from http://pure-essence.net/stuff/code/utf8RawUrlDecode.phps
	 *
	 * @access public
	 *
	 * @param string $source escaped with Javascript's escape() function
	 * @param string $iconv_to destination character set will be used as second paramether in the iconv function. Default is iso-8859-1.
	 * @return string
	 */
	function unescape($source, $iconv_to = 'iso-8859-1') {
		$decodedStr = '';
		$pos = 0;
		$len = strlen ($source);
		while ($pos < $len) {
			$charAt = substr ($source, $pos, 1);
			if ($charAt == '%') {
				$pos++;
				$charAt = substr ($source, $pos, 1);
				if ($charAt == 'u') {
					// we got a unicode character
					$pos++;
					$unicodeHexVal = substr ($source, $pos, 4);
					$unicode = hexdec ($unicodeHexVal);
					$decodedStr .= Browser_Utilities :: code2utf($unicode);
					$pos += 4;
				}
				else {
				// we have an escaped ascii character
				$hexVal = substr ($source, $pos, 2);
				$decodedStr .= chr (hexdec ($hexVal));
				$pos += 2;
				}
			}
			else {
				$decodedStr .= Browser_Utilities :: code2utf(ord($charAt));
				$pos++;
			}
		}
		
		if ($iconv_to != "iso-8859-1") {
			$decodedStr = iconv("iso-8859-1", $iconv_to, $decodedStr);
		}
		
		return $decodedStr;
	}
	
	/**
	 * Function coverts number of utf char into that character.
	 * Function taken from: http://sk2.php.net/manual/en/function.utf8-encode.php#49336
 	 *
	 * @access public
	 *
	 * @param int $num
	 * @return utf8char
	 */
	function code2utf($num){
		if ($num < 128) return chr($num);
		if ($num < 2048) return chr(($num>>6)+192).chr(($num&63)+128);
		if ($num < 65536) return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
		if ($num < 2097152) return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128) .chr(($num&63)+128);
		return '';
	}


}
?>
