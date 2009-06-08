<?php 
/**
 * PSSTATUS Plugin
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_PSStatus
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.PSStatus.inc.php 228 2009-06-03 05:56:44Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * process Plugin, which displays the status of configured processes
 * a simple view which shows a process name and the status
 * status determined by calling the "pidof" command line utility, another way is to provide
 * a file with the output of the pidof utility, so there is no need to run a executeable by the
 * webserver, the format of the command is written down in the psstatus.config.php file, where also
 * the method of getting the information is configured
 * processes that should be checked are also defined in psstatus.config.php
 *
 * @category  PHP
 * @package   PSI_Plugin_PSStatus
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class PSStatus extends PSI_Plugin
{
    /**
     * variable, which holds the content of the command
     * @var array
     */
    private $_filecontent = array();
    
    /**
     * variable, which holds the result before the xml is generated out of this array
     * @var array
     */
    private $_result = array();
    
    /**
     * controls if debugging is turned on or off, value is defined in the main config.php
     * @var boolean
     */
    private $_debug = PSI_DEBUG;
    
    /**
     * read the data into an internal array and also call the parent constructor
     */
    public function __construct()
    {
        $buffer = "";
        parent::__construct(__CLASS__);
        switch (PSI_PLUGIN_PSSTATUS_ACCESS) {
        case 'command':
            if (PHP_OS == 'WINNT') {
                $objLocator = new COM("WbemScripting.SWbemLocator");
                $wmi = $objLocator->ConnectServer();
                $process_wmi = $wmi->InstancesOf('Win32_Process');
                foreach ($process_wmi as $process) {
                    $this->_filecontent[] = array(trim($process->Caption), trim($process->ProcessId));
                }
            } else {
                $processes = preg_split("/[\s]?,[\s]?/", PSI_PLUGIN_PSSTATUS_PROCESSES, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($processes as $process) {
                    CommonFunctions::executeProgram("pidof", "-s ".$process, $buffer, $this->_debug);
                    if (strlen(trim($buffer)) > 0) {
                        $this->_filecontent[] = array(trim($process), trim($buffer));
                    }
                }
            }
            break;
        case 'data':
            CommonFunctions::rfts(APP_ROOT."/data/psstatus.txt", $buffer);
            $processes = explode("\n", $buffer);
            foreach ($processes as $process) {
                $ps = preg_split("/[\s]?\|[\s]?/", $process, -1, PREG_SPLIT_NO_EMPTY);
                if (count($ps) == 2) {
                    $this->_filecontent[] = array(trim($ps[0]), trim($ps[1]));
                }
            }
            break;
        default:
            $this->global_error->addError("switch(PSI_PLUGIN_PSSTATUS_ACCESS)", "Bad psstatus configuration in psstatus.config.php");
            break;
        }
    }
    
    /**
     * doing all tasks to get the required informations that the plugin needs
     * result is stored in an internal array<br>the array is build like a tree,
     * so that it is possible to get only a specific process with the childs
     *
     * @return void
     */
    public function execute()
    {
        if ( empty($this->_filecontent)) {
            return;
        }
        foreach (preg_split("/[\s]?,[\s]?/", PSI_PLUGIN_PSSTATUS_PROCESSES, -1, PREG_SPLIT_NO_EMPTY) as $process) {
            if ($this->_recursiveinarray($process, $this->_filecontent)) {
                $this->_result[] = array($process, true);
            } else {
                $this->_result[] = array($process, false);
            }
        }
    }
    
    /**
     * generates the XML content for the plugin
     *
     * @param string $enc base encoding
     *
     * @return SimpleXMLObject entire XML content for the plugin
     */
    public function xml($enc)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement("Plugin_".__CLASS__);
        $dom->appendChild($root);
        $xml = simplexml_import_dom($dom);
        if ( empty($this->_result)) {
            return $xml;
        }
        foreach ($this->_result as $ps) {
            $xmlps = $xml->addChild("ProcessStatus");
            $xmlps->addChild("Name", XML::toUTF8($ps[0], $enc));
            $xmlps->addChild("Status", XML::toUTF8($ps[1] ? 1 : 0, $enc));
        }
        return $xml;
    }
    
    /**
     * checks an array recursive if an value is in, extended version of in_array()
     *
     * @param mixed $needle   what to find
     * @param array $haystack where to find
     *
     * @return boolean true - found<br>false - not found
     */
    private function _recursiveinarray($needle, $haystack)
    {
        foreach ($haystack as $stalk) {
            if ($needle == $stalk || (is_array($stalk) && $this->_recursiveinarray($needle, $stalk))) {
                return true;
            }
        }
        return false;
    }
}
?>
