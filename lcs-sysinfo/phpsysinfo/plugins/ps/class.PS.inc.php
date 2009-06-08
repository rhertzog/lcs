<?php 
/**
 * PSSTATUS Plugin
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_PS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.PS.inc.php 228 2009-06-03 05:56:44Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * process Plugin, which displays all running processes
 * a simple tree view which is filled with the running processes which are determined by
 * calling the "ps" command line utility, another way is to provide
 * a file with the output of the ps utility, so there is no need to run a execute by the
 * webserver, the format of the command is written down in the ps.config.php file, where also
 * the method of getting the information is configured
 *
 * @category  PHP
 * @package   PSI_Plugin_PS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class PS extends PSI_Plugin
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
        switch (PSI_PLUGIN_PS_ACCESS) {
        case 'command':
            if (PHP_OS == 'WINNT') {
                $objLocator = new COM("WbemScripting.SWbemLocator");
                $wmi = $objLocator->ConnectServer();
                $os_wmi = $wmi->InstancesOf('Win32_OperatingSystem');
                foreach ($os_wmi as $os) {
                    $memtotal = $os->TotalVisibleMemorySize * 1024;
                }
                $process_wmi = $wmi->InstancesOf('Win32_Process');
                foreach ($process_wmi as $process) {
                    if (strlen(trim($process->CommandLine)) > 0) {
                        $ps = trim($process->CommandLine);
                    } else {
                        $ps = trim($process->Caption);
                    }
                    if (trim($process->ProcessId) != 0) {
                        $memusage = round(trim($process->WorkingSetSize) * 100 / $memtotal, 1);
                        //ParentProcessId
                        //Unique identifier of the process that creates a process. Process identifier numbers are reused, so they
                        //only identify a process for the lifetime of that process. It is possible that the process identified by
                        //ParentProcessId is terminated, so ParentProcessId may not refer to a running process. It is also
                        //possible that ParentProcessId incorrectly refers to a process that reuses a process identifier. You can
                        //use the CreationDate property to determine whether the specified parent was created after the process
                        //represented by this Win32_Process instance was created.
                        //=> subtrees of processes may be missing (WHAT TODO?!?)
                        $this->_filecontent[] = trim($process->ProcessId)." ".trim($process->ParentProcessId)." ".$memusage." ".$ps;
                    }
                }
            } else {
                CommonFunctions::executeProgram("ps", "-eo pid,ppid,pmem,args", $buffer, $this->_debug);
            }
            break;
        case 'data':
            CommonFunctions::rfts(APP_ROOT."/data/ps.txt", $buffer);
            break;
        default:
            $this->global_error->addConfigError("__construct()", "PSI_PLUGIN_PS_ACCESS");
            break;
        }
        if (PHP_OS != 'WINNT') {
            if (trim($buffer) != "") {
                $this->_filecontent = explode("\n", $buffer);
                unset($this->_filecontent[0]);
            } else {
                $this->_filecontent = array();
            }
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
        foreach ($this->_filecontent as $roworig) {
            $row = preg_split("/[\s]+/", trim($roworig), 4);
            if (count($row) != 4) {
                break;
            }
            foreach ($row as $key=>$val) {
                $items[$row[0]][$key] = $val;
            }
            $items[$row[1]]['childs'][$row[0]] = &$items[$row[0]];
        }
        $this->_result = $items[0];
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
        $xml = $this->_addchild($this->_result['childs'], $xml, $enc);
        return $xml;
    }
    
    /**
     * recursive function to allow appending child processes to a parent process
     *
     * @param array           $child part of the array which should be appended to the XML
     * @param SimpleXMLObject &$xml  XML-Object to which the array content is appended
     * @param string          $enc   base encoding
     *
     * @return SimpleXMLObject Object with the appended array content
     */
    private function _addchild($child, &$xml, $enc)
    {
        foreach ($child as $key=>$value) {
            $xmlnode = $xml->addchild("Process");
            foreach ($value as $key2=>$value2) {
                if (!is_array($value2)) {
                    switch ($key2) {
                    case 0:
                        $keyname = 'PID';
                        break;
                    case 1:
                        $keyname = 'PPID';
                        break;
                    case 2:
                        $keyname = 'MemoryUsage';
                        break;
                    case 3:
                        $keyname = 'Name';
                        break;
                    default:
                        $keyname = "";
                        break;
                    }
                    if ($keyname != "") {
                        $xmlnode->addchild($keyname, XML::toUTF8(htmlspecialchars($value2), $enc));
                    }
                } else {
                    $this->_addchild($value2, $xml, $enc);
                }
            }
        }
        return $xml;
    }
}
?>
