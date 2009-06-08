<?php 
/**
 * MDSTAT Plugin
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_MDStatus
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.MDStatus.inc.php 228 2009-06-03 05:56:44Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * mdstat Plugin, which displays a snapshot of the kernel's RAID/md state
 * a simple view which shows supported types and RAID-Devices which are determined by
 * parsing the "/proc/mdstat" file, another way is to provide
 * a file with the output of the /proc/mdstat file, so there is no need to run a execute by the
 * webserver, the format of the command is written down in the mdstat.config.php file, where also
 * the method of getting the information is configured
 *
 * @category  PHP
 * @package   PSI_Plugin_MDStatus
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class MDStatus extends PSI_Plugin
{
    /**
     * variable, which holds the content of the command
     * @var array
     */
    private $_filecontent = "";
    
    /**
     * variable, which holds the result before the xml is generated out of this array
     * @var array
     */
    private $_result = array();
    
    /**
     * read the data into an internal array and also call the parent constructor
     */
    public function __construct()
    {
        $buffer = "";
        parent::__construct((__CLASS__));
        switch (PSI_PLUGIN_MDSTAT_ACCESS) {
        case 'file':
            CommonFunctions::rfts("/proc/mdstat", $buffer);
            break;
        case 'data':
            CommonFunctions::rfts(APP_ROOT."/data/mdstat.txt", $buffer);
            break;
        default:
            $this->global_error->addConfigError("__construct()", "PSI_PLUGIN_MDSTAT_ACCESS");
            break;
        }
        if (trim($buffer) != "") {
            $this->_filecontent = explode("\n", $buffer);
        } else {
            $this->_filecontent = array();
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
        // get the supported types
        if (preg_match('/[a-zA-Z]* : (\[([a-z0-9])*\]([ \n]))+/', $this->_filecontent[0], $res)) {
            $parts = explode(" : ", $res[0]);
            $parts = explode(" ", $parts[1]);
            $count = 0;
            foreach ($parts as $types) {
                if (trim($types) != "") {
                    $this->_result['supported_types'][$count++] = substr(trim($types), 1, -1);
                }
            }
        }
        // get disks
        if (preg_match("/^read_ahead/", $this->_filecontent[2])) {
            $count = 2;
        } else {
            $count = 1;
        }
        $cnt_filecontent = count($this->_filecontent);
        do {
            $parts = explode(" : ", $this->_filecontent[$count]);
            $dev = trim($parts[0]);
            if (count($parts) == 2) {
                $details = explode(' ', $parts[1]);
                if (!strstr($details[0], 'inactive')) {
                    $this->_result['devices'][$dev]['level'] = $details[1];
                }
                $this->_result['devices'][$dev]['status'] = $details[0];
                for ($i = 2, $cnt_details = count($details); $i < $cnt_details; $i++) {
                    preg_match('/(([a-z0-9])+)(\[([0-9]+)\])(\([SF ]\))?/', trim($details[$i]), $partition);
                    if (count($partition) == 5 || count($partition) == 6) {
                        $this->_result['devices'][$dev]['partitions'][$partition[1]]['raid_index'] = substr(trim($partition[3]), 1, -1);
                        if (isset($partition[5])) {
                            $search = array("(", ")");
                            $replace = array("", "");
                            $this->_result['devices'][$dev]['partitions'][$partition[1]]['status'] = str_replace($search, $replace, trim($partition[5]));
                        } else {
                            $this->_result['devices'][$dev]['partitions'][$partition[1]]['status'] = " ";
                        }
                    }
                }
                $count++;
                $optionline = $this->_filecontent[$count - 1].$this->_filecontent[$count];
                if ($pos = strpos($optionline, "k chunk")) {
                    $this->_result['devices'][$dev]['chunk_size'] = trim(substr($optionline, $pos - 3, 3));
                } else {
                    $this->_result['devices'][$dev]['chunk_size'] = -1;
                }
                if ($pos = strpos($optionline, "super non-persistent")) {
                    $this->_result['devices'][$dev]['pers_superblock'] = 0;
                } else {
                    $this->_result['devices'][$dev]['pers_superblock'] = 1;
                }
                if ($pos = strpos($optionline, "algorithm")) {
                    $this->_result['devices'][$dev]['algorithm'] = trim(substr($optionline, $pos + 9, 2));
                } else {
                    $this->_result['devices'][$dev]['algorithm'] = -1;
                }
                if (preg_match('/(\[[0-9]?\/[0-9]\])/', $optionline, $res)) {
                    $slashpos = strpos($res[0], '/');
                    $this->_result['devices'][$dev]['registered'] = substr($res[0], 1, $slashpos - 1);
                    $this->_result['devices'][$dev]['active'] = substr($res[0], $slashpos + 1, strlen($res[0]) - $slashpos - 2);
                } else {
                    $this->_result['devices'][$dev]['registered'] = -1;
                    $this->_result['devices'][$dev]['active'] = -1;
                }
                if (preg_match(('/([a-z]+)([ ]?)=([ ]?)([0-9\.]+)%/'), $this->_filecontent[$count + 1], $res) || (preg_match(('/([a-z]+)([ ]?)=([ ]?)([0-9\.]+)/'), $optionline, $res))) {
                    list($this->_result['devices'][$dev]['action']['name'], $this->_result['devices'][$dev]['action']['percent']) = explode("=", str_replace("%", "", $res[0]));
                    if (preg_match(('/([a-z]*=[0-9\.]+[a-z]+)/'), $this->_filecontent[$count + 1], $res)) {
                        $time = explode("=", $res[0]);
                        list($this->_result['devices'][$dev]['action']['finish_time'], $this->_result['devices'][$dev]['action']['finish_unit']) = sscanf($time[1], '%f%s');
                    } else {
                        $this->_result['devices'][$dev]['action']['finish_time'] = -1;
                        $this->_result['devices'][$dev]['action']['finish_unit'] = -1;
                    }
                } else {
                    $this->_result['devices'][$dev]['action']['name'] = -1;
                    $this->_result['devices'][$dev]['action']['percent'] = -1;
                    $this->_result['devices'][$dev]['action']['finish_time'] = -1;
                    $this->_result['devices'][$dev]['action']['finish_unit'] = -1;
                }
            } else {
                $count++;
            }
        } while ($cnt_filecontent > $count);
        $lastline = $this->_filecontent[$cnt_filecontent - 2];
        if (strpos($lastline, "unused devices") !== false) {
            $parts = explode(":", $lastline);
            $search = array("<", ">");
            $replace = array("", "");
            $this->_result['unused_devs'] = trim(str_replace($search, $replace, $parts[1]));
        } else {
            $this->_result['unused_devs'] = -1;
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
        $sup = $xml->addChild("Supported_Types");
        foreach ($this->_result['supported_types'] as $type) {
            $typ = $sup->addChild("Type");
            $typ->addChild("Name", XML::toUTF8($type, $enc));
        }
        foreach ($this->_result['devices'] as $key=>$device) {
            $dev = $xml->addChild("Device");
            $dev->addChild("Device_Name", XML::toUTF8($key, $enc));
            $dev->addChild("Level", XML::toUTF8($device["level"], $enc));
            $dev->addChild("Disk_Status", XML::toUTF8($device["status"], $enc));
            $dev->addChild("Chunk_Size", XML::toUTF8($device["chunk_size"], $enc));
            $dev->addChild("Persistend_Superblock", XML::toUTF8($device["pers_superblock"], $enc));
            $dev->addChild("Algorithm", XML::toUTF8($device["algorithm"], $enc));
            $dev->addChild("Disks_Registered", XML::toUTF8($device["registered"], $enc));
            $dev->addChild("Disks_Active", XML::toUTF8($device["active"], $enc));
            $action = $dev->addChild("Action");
            $action->addChild("Percent", XML::toUTF8($device['action']['percent'], $enc));
            $action->addChild("Name", XML::toUTF8($device['action']['name'], $enc));
            $action->addChild("Time_To_Finish", XML::toUTF8($device['action']['finish_time'], $enc));
            $action->addChild("Time_Unit", XML::toUTF8($device['action']['finish_unit'], $enc));
            $disks = $dev->addChild("Disks");
            foreach ($device['partitions'] as $diskkey=>$disk) {
                $disktemp = $disks->addChild("Disk");
                $disktemp->addChild("Name", XML::toUTF8($diskkey, $enc));
                $disktemp->addChild("Status", XML::toUTF8($disk['status'], $enc));
                $disktemp->addChild("Index", XML::toUTF8($disk['raid_index'], $enc));
            }
        }
        $xml->addChild("Unused_Devices", XML::toUTF8($this->_result['unused_devs'], $enc));
        return $xml;
    }
}
?>
