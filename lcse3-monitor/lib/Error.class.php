<?php
/**
 * Error class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Error
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id$
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * class for the error handling in phpsysinfo
 *
 * @category  PHP
 * @package   PSI_Error
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Error
{
    /**
     * holds the instance of this class
     *
     * @static
     * @var object
     */
    private static $_instance;

    /**
     * holds the error messages
     *
     * @var array
     */
    private $_arrErrorList = array ();

    /**
     * current number ob errors
     *
     * @var integer
     */
    private $_errors = 0;

    /**
     * initalize some used vars
     */
    private function __construct()
    {
        $this->_errors = 0;
        $this->_arrErrorList = array ();
    }

    /**
     * Singleton function
     *
     * @return Error instance of the class
     */
    public static function singleton()
    {
        if (! isset (self::$_instance)) {
            $c = __CLASS__ ;
            self::$_instance = new $c;
        }
        return self::$_instance;
    }

    /**
     * triggers an error when somebody tries to clone the object
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error("Can't be cloned", E_USER_ERROR);
    }

    /**
     * adds an error to the internal list
     *
     * @param string $strCommand Command, which cause the Error
     * @param string $strMessage additional Message, to describe the Error
     *
     * @return void
     */
    public function addError($strCommand, $strMessage)
    {
        $index = count($this->_arrErrorList)+1;
        $this->_arrErrorList[$index]['command'] = $strCommand;
        $this->_arrErrorList[$index]['message'] = $this->_trace($strMessage);
        $this->_errors++;
    }

    /**
     * add a config error to the internal list
     *
     * @param object $strCommand Command, which cause the Error
     * @param object $strMessage additional Message, to describe the Error
     *
     * @return void
     */
    public function addConfigError($strCommand, $strMessage)
    {
        $this->addError($strCommand, "Wrong Value in config.php for ".$strMessage);
    }

    /**
     * adds a waraning to the internal list
     *
     * @param string $strMessage Warning message to display
     *
     * @return void
     */
    public function addWarning($strMessage)
    {
        $index = count($this->_arrErrorList)+1;
        $this->_arrErrorList[$index]['command'] = "WARN";
        $this->_arrErrorList[$index]['message'] = $strMessage;
    }

    /**
     * converts the internal error and warning list in a html table
     *
     * @return string contains a HTML table which can be used to echo out the errors
     */
    public function errorsAsHTML()
    {
        $strHTMLString = "";
        $strWARNString = "";
        $strHTMLhead = "<table width=\"100%\" border=\"0\">\n"."\t<tr>\n"."\t\t<td><font size=\"-1\"><b>Command</b></font></td>\n"."\t\t<td><font size=\"-1\"><b>Message</b></font></td>\n"."\t</tr>\n";
        $strHTMLfoot = "</table>\n";
        if ($this->_errors > 0) {
            foreach ($this->_arrErrorList as $arrLine) {
                if ($arrLine['command'] == "WARN") {
                    $strWARNString .= "<font size=\"-1\"><b>WARNING: ".str_replace("\n", "<br/>", htmlspecialchars($arrLine['message']))."</b></font><br/>\n";
                } else {
                    $strHTMLString .= "\t<tr>\n"."\t\t<td><font size=\"-1\">".htmlspecialchars($arrLine['command'])."</font></td>\n"."\t\t<td><font size=\"-1\">".str_replace("\n", "<br/>", $arrLine['message'])."</font></td>\n"."\t</tr>\n";
                }
            }
        }
        if (! empty($strHTMLString)) {
            $strHTMLString = $strWARNString.$strHTMLhead.$strHTMLString.$strHTMLfoot;
        } else {
            $strHTMLString = $strWARNString;
        }
        return $strHTMLString;
    }

    /**
     * converts the internal error and warning list to a XML file
     *
     * @return string XML data containing the errors
     */
    public function errorsAsXML()
    {
        $dom = new DOMDocument();
        $root = $dom->createElement("phpsysinfo");
        $dom->appendChild($root);
        $xml = simplexml_import_dom($dom);
        $generation = $xml->addChild('Generation');
        $generation->addAttribute('version', Monitor::MONITORVERSION);
        $generation->addAttribute('timestamp', time());
        if ($this->_errors > 0) {
            foreach ($this->_arrErrorList as $arrLine) {
                $error = $xml->addChild('Error');
                $error->addChild('Function', $arrLine['command']);
                $error->addChild('Message', $arrLine['message']);
            }
        }
        return $xml->asXML();
    }
    /**
     * add the errors to an existing xml document
     *
     * @param SimpleXMLObject &$xml reference existing simplexmlobject to which errors are added if present
     *
     * @return void
     */
    public function errorsAddToXML( & $xml)
    {
        if ($this->_errors > 0) {
            $xmlerr = $xml->addChild('Errors');
            foreach ($this->_arrErrorList as $arrLine) {
                $error = $xmlerr->addChild('Error');
                $error->addChild('Function', utf8_encode(trim(htmlspecialchars($arrLine['command']))));
                $error->addChild('Message', utf8_encode(trim(htmlspecialchars($arrLine['message']))));
            }
        }
    }
    /**
     * check if errors exists
     *
     * @return boolean true if are errors logged, false if not
     */
    public function errorsExist()
    {
        if ($this->_errors > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * generate a function backtrace for error diagnostic, function is genearally based on code submitted in the php reference page
     *
     * @param string $strMessage additional message to display
     *
     * @return string formatted string of the backtrace
     */
    private function _trace($strMessage)
    {
        $arrTrace = array_reverse(debug_backtrace());
        $strFunc = '';
        $strBacktrace = htmlspecialchars($strMessage)."\n\n";
        foreach ($arrTrace as $val) {
            // avoid the last line, which says the error is from the error class
            if ($val == $arrTrace[count($arrTrace)-1]) {
                break;
            }
            $strBacktrace .= str_replace(".", ".", $val['file']).' on line '.$val['line'];
            if ($strFunc) {
                $strBacktrace .= ' in function '.$strFunc;
            }
            if ($val['function'] == 'include' || $val['function'] == 'require' || $val['function'] == 'include_once' || $val['function'] == 'require_once') {
                $strFunc = '';
            } else {
                $strFunc = $val['function'].'(';
                if ( isset ($val['args'][0])) {
                    $strFunc .= ' ';
                    $strComma = '';
                    foreach ($val['args'] as $val) {
                        $strFunc .= $strComma.$this->_printVar($val);
                        $strComma = ', ';
                    }
                    $strFunc .= ' ';
                }
                $strFunc .= ')';
            }
            $strBacktrace .= "\n";
        }
        return $strBacktrace;
    }
    /**
     * convert some special vars into better readable output
     *
     * @param mixed $var value, which should be formatted
     *
     * @return string formatted string
     */
    private function _printVar($var)
    {
        if (is_string($var)) {
            $search = array ("\x00", "\x0a", "\x0d", "\x1a", "\x09");
            $replace = array ('\0', '\n', '\r', '\Z', '\t');
            return ('"'.str_replace($search, $replace, $var).'"');
        } elseif (is_bool($var)) {
            if ($var) {
                return ('true');
            } else {
                return ('false');
            }
        } elseif (is_array($var)) {
            $strResult = 'array( ';
            $strComma = '';
            foreach ($var as $key=>$val) {
                $strResult .= $strComma.$this->_printVar($key).' => '.$this->_printVar($val);
                $strComma = ', ';
            }
            $strResult .= ' )';
            return ($strResult);
        }
        // anything else, just let php try to print it
        return (var_export($var, true));
    }
}
?>

