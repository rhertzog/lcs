<?php 
/**
 * XML Generation class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_XML
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.XML.inc.php 230 2009-06-06 12:28:54Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * class for generation of the xml
 *
 * @category  PHP
 * @package   PSI_XML
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class XML
{
    /**
     * Sysinfo object where the information retrieval methods are included
     *
     * @var Sysinfo
     */
    private $_sysinfo;
    
    /**
     * xml object with the xml content
     *
     * @var SimpleXMLElement
     */
    private $_xml;
    
    /**
     * object for error handling
     *
     * @var Error
     */
    private $_errors;
    
    /**
     * array with all enabled plugins (name)
     *
     * @var array
     */
    private $_plugins;
    
    /**
     * plugin name if pluginrequest
     *
     * @var string
     */
    private $_plugin = '';
    
    /**
     * generate a xml for a plugin or for the main app
     *
     * @var boolean
     */
    private $_plugin_request = false;
    
    /**
     * generate the entire xml with all plugins or only a part of the xml (main or plugin)
     *
     * @var boolean
     */
    private $_complete_request = false;
    
    /**
     * doing some initial tasks
     * - generate the xml structure with the right header elements
     * - get the error object for error output
     * - get a instance of the sysinfo object
     *
     * @param boolean $complete   generate xml with all plugins or not
     * @param string  $pluginname name of the plugin
     *
     * @return void
     */
    public function __construct($complete = false, $pluginname = "")
    {
        $this->_errors = Error::singleton();
        if ($pluginname == "") {
            $this->_plugin_request = false;
            $this->_plugin = '';
        } else {
            $this->_plugin_request = true;
            $this->_plugin = $pluginname;
        }
        if ($complete) {
            $this->_complete_request = true;
        } else {
            $this->_complete_request = false;
        }
        $os = PHP_OS;
        $this->_sysinfo = new $os();
        $this->_plugins = CommonFunctions::getPlugins();
        $this->_xmlbody();
    }
    
    /**
     * generate common information
     *
     * @return void
     */
    private function _buildVitals()
    {
        $strLoadavg = '';
        $arrBuf = $this->_sysinfo->loadavg(PSI_LOAD_BAR);
        foreach ($arrBuf['avg'] as $strValue) {
            $strLoadavg .= $strValue.' ';
        }
        $vitals = $this->_xml->addChild('Vitals');
        if (PSI_USE_VHOST === true) {
            $vitals->addChild('Hostname', $this->_toUTF8(htmlspecialchars($this->_sysinfo->vhostname())));
            $vitals->addChild('IPAddr', $this->_toUTF8($this->_sysinfo->vipaddr()));
        } else {
            $vitals->addChild('Hostname', $this->_toUTF8(htmlspecialchars($this->_sysinfo->chostname())));
            $vitals->addChild('IPAddr', $this->_toUTF8($this->_sysinfo->ipaddr()));
        }
        $vitals->addChild('Kernel', $this->_toUTF8(htmlspecialchars($this->_sysinfo->kernel())));
        $vitals->addChild('Distro', $this->_toUTF8(htmlspecialchars($this->_sysinfo->distro())));
        $vitals->addChild('Distroicon', $this->_toUTF8(htmlspecialchars($this->_sysinfo->distroicon())));
        $vitals->addChild('Uptime', $this->_toUTF8($this->_sysinfo->uptime()));
        $vitals->addChild('Users', $this->_toUTF8($this->_sysinfo->users()));
        $vitals->addChild('LoadAvg', $this->_toUTF8($strLoadavg));
        if (isset($arrBuf['cpupercent'])) {
            $vitals->addChild('CPULoad', $this->_toUTF8(round($arrBuf['cpupercent'], 2)));
        }
    }
    
    /**
     * generate the network information
     *
     * @return void
     */
    private function _buildNetwork()
    {
        $arrNet = $this->_sysinfo->network();
        $network = $this->_xml->addChild('Network');
        $hideDevices = preg_split("/[\s]?,[\s]?/", PSI_HIDE_NETWORK_INTERFACE, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($arrNet as $strDev=>$arrStats) {
            if (!in_array(trim($strDev), $hideDevices)) {
                $device = $network->addChild('NetDevice');
                $device->addChild('Name', $this->_toUTF8(htmlspecialchars($strDev)));
                $device->addChild('RxBytes', $this->_toUTF8($arrStats['rx_bytes']));
                $device->addChild('TxBytes', $this->_toUTF8($arrStats['tx_bytes']));
                $device->addChild('Err', $this->_toUTF8($arrStats['errs']));
                $device->addChild('Drops', $this->_toUTF8($arrStats['drop']));
            }
        }
    }
    
    /**
     * generate the hardware information
     *
     * @return void
     */
    private function _buildHardware()
    {
        $hardware = $this->_xml->addChild('Hardware');
        $cpu = $hardware->addChild('CPU');
        $pci = $hardware->addChild('PCI');
        $ide = $hardware->addChild('IDE');
        $scsi = $hardware->addChild('SCSI');
        $usb = $hardware->addChild('USB');
        $arrSys = $this->_sysinfo->cpuinfo();
        $arrBuf = CommonFunctions::finddups($this->_sysinfo->pci());
        if (count($arrBuf)) {
            foreach ($arrBuf as $arrValue) {
                if (trim($arrValue) != '') {
                    $tmp = $pci->addChild('Device');
                    $tmp->addChild('Name', $this->_toUTF8($arrValue));
                }
            }
        }
        $arrBuf = $this->_sysinfo->ide();
        if (count($arrBuf)) {
            foreach ($arrBuf as $strKey=>$arrValue) {
                if (trim($arrValue) != '') {
                    $tmp = $ide->addChild('Device');
                    $tmp->addChild('Name', $strKey.': '.$this->_toUTF8($arrValue['model']));
                    if (isset($arrValue['capacity'])) {
                        $tmp->addChild('Capacity', $this->_toUTF8($arrValue['capacity']));
                    }
                }
            }
        }
        $arrBuf = $this->_sysinfo->scsi();
        if (count($arrBuf)) {
            foreach ($arrBuf as $strKey=>$arrValue) {
                if (trim($arrValue) != '') {
                    $tmp = $scsi->addChild('Device');
                    if ($strKey >= '0' && $strKey <= '9') {
                        $tmp->addChild('Name', $this->_toUTF8($arrValue['model']));
                    } else {
                        $tmp->addChild('Name', $this->_toUTF8($strKey.': '.$arrValue['model']));
                    }
                    if (isset($arrrValue['capacity'])) {
                        $tmp->addChild('Capacity', $this->_toUTF8($arrValue['capacity']));
                    }
                }
            }
        }
        $arrBuf = CommonFunctions::finddups($this->_sysinfo->usb());
        if (count($arrBuf)) {
            foreach ($arrBuf as $arrValue) {
                if (trim($arrValue) != '') {
                    $tmp = $usb->addChild('Device');
                    $tmp->addChild('Name', $this->_toUTF8($arrValue));
                }
            }
        }
        $cpu->addChild('Number', $arrSys['cpus']);
        $cpu->addChild('Model', $this->_toUTF8($arrSys['model']));
        if (isset($arrSys['temp'])) {
            $cpu->addChild('Cputemp', $this->_toUTF8($arrSys['temp']));
        }
        $cpu->addChild('Cpuspeed', $this->_toUTF8($arrSys['cpuspeed']));
        if (isset($arrSys['busspeed'])) {
            $cpu->addChild('Busspeed', $this->_toUTF8($arrSys['busspeed']));
        }
        if (isset($arrSys['cache'])) {
            $cpu->addChild('Cache', $this->_toUTF8($arrSys['cache']));
        }
        if (isset($arrSys['bogomips'])) {
            $cpu->addChild('Bogomips', $this->_toUTF8($arrSys['bogomips']));
        }
    }
    
    /**
     * generate the memory information
     *
     * @return void
     */
    private function _buildMemory()
    {
        $arrMem = $this->_sysinfo->memory();
        $i = 0;
        $memory = $this->_xml->addChild('Memory');
        $memory->addChild('Free', $arrMem['ram']['free']);
        $memory->addChild('Used', $arrMem['ram']['used']);
        $memory->addChild('Total', $arrMem['ram']['total']);
        $memory->addChild('Percent', $arrMem['ram']['percent']);
        if (isset($arrMem['ram']['app'])) {
            $memory->addChild('App', $this->_toUTF8($arrMem['ram']['app']));
            $memory->addChild('AppPercent', $this->_toUTF8($arrMem['ram']['app_percent']));
            $memory->addChild('Buffers', $this->_toUTF8($arrMem['ram']['buffers']));
            $memory->addChild('BuffersPercent', $this->_toUTF8($arrMem['ram']['buffers_percent']));
            $memory->addChild('Cached', $this->_toUTF8($arrMem['ram']['cached']));
            $memory->addChild('CachedPercent', $this->_toUTF8($arrMem['ram']['cached_percent']));
        }
        $swap = $this->_xml->addChild('Swap');
        $swapDev = $this->_xml->addChild('Swapdevices');
        if (count($arrMem['swap']) > 0) {
            $swap->addChild('Free', $this->_toUTF8($arrMem['swap']['free']));
            $swap->addChild('Used', $this->_toUTF8($arrMem['swap']['used']));
            $swap->addChild('Total', $this->_toUTF8($arrMem['swap']['total']));
            $swap->addChild('Percent', $this->_toUTF8($arrMem['swap']['percent']));
            foreach ($arrMem['devswap'] as $arrDevice) {
                $swapMount = $swapDev->addChild('Mount');
                $swapMount->addChild('MountPointID', $this->_toUTF8($i++));
                $swapMount->addChild('Type', $this->_toUTF8('Swap'));
                $dev = $swapMount->addChild('Device');
                $dev->addChild('Name', $this->_toUTF8($arrDevice['dev']));
                $swapMount->addChild('Percent', $this->_toUTF8($arrDevice['percent']));
                $swapMount->addChild('Free', $this->_toUTF8($arrDevice['free']));
                $swapMount->addChild('Used', $this->_toUTF8($arrDevice['used']));
                $swapMount->addChild('Size', $this->_toUTF8($arrDevice['total']));
            }
        }
    }
    
    /**
     * generate the filesysteminformation
     *
     * @return void
     */
    private function _buildFilesystems()
    {
        $hideMounts = array();
        $hideFstypes = array();
        $hideDisks = array();
        if (PSI_HIDE_MOUNTS != "") {
            $hideMounts = explode(',', PSI_HIDE_MOUNTS);
        }
        if (PSI_HIDE_FS_TYPES != "") {
            $hideFstypes = explode(',', PSI_HIDE_FS_TYPES);
        }
        if (PSI_HIDE_DISKS != "") {
            $hideDisks = explode(',', PSI_HIDE_DISKS);
        }
        $arrFs = $this->_sysinfo->filesystems();
        $fs = $this->_xml->addChild('FileSystem');
        for ($i = 0, $max = sizeof($arrFs); $i < $max; $i++) {
            if (!in_array($arrFs[$i]['mount'], $hideMounts, true) && !in_array($arrFs[$i]['fstype'], $hideFstypes, true) && !in_array($arrFs[$i]['disk'], $hideDisks, true)) {
                $mount = $fs->addChild('Mount');
                $mount->addchild('MountPointID', $this->_toUTF8($i));
                if (PSI_SHOW_MOUNT_POINT === true) {
                    $mount->addchild('MountPoint', $this->_toUTF8($arrFs[$i]['mount']));
                }
                $mount->addchild('Type', $this->_toUTF8($arrFs[$i]['fstype']));
                $dev = $mount->addchild('Device');
                $dev->addChild('Name', $this->_toUTF8($arrFs[$i]['disk']));
                $mount->addchild('Percent', $this->_toUTF8($arrFs[$i]['percent']));
                $mount->addchild('Free', $this->_toUTF8($arrFs[$i]['free']));
                $mount->addchild('Used', $this->_toUTF8($arrFs[$i]['used']));
                $mount->addchild('Size', $this->_toUTF8($arrFs[$i]['size']));
                if (isset($arrFs[$i]['options'])) {
                    $mount->addchild('MountOptions', $this->_toUTF8($arrFs[$i]['options']));
                }
                if (isset($arrFs[$i]['inodes'])) {
                    $mount->addchild('Inodes', $this->_toUTF8($arrFs[$i]['inodes']));
                }
            }
        }
    }
    
    /**
     * generate the motherboard information
     *
     * @return void
     */
    private function _buildMbinfo()
    {
        $mbinfoclass = PSI_SENSOR_PROGRAM;
        $mbinfo_data = new $mbinfoclass();
        $mbinfo = $this->_xml->addChild('MBinfo');
        $arrBuff = $mbinfo_data->temperature();
        if (sizeof($arrBuff) > 0) {
            $temp = $mbinfo->addChild('Temperature');
            foreach ($arrBuff as $arrValue) {
                $item = $temp->addChild('Item');
                $item->addChild('Label', $this->_toUTF8($arrValue['label']));
                $item->addChild('Value', $this->_toUTF8($arrValue['value']));
                $item->addChild('Limit', $this->_toUTF8($arrValue['limit']));
            }
        }
        $arrBuff = $mbinfo_data->fans();
        if (sizeof($arrBuff) > 0) {
            $fan = $mbinfo->addChild('Fans');
            foreach ($arrBuff as $arrValue) {
                $item = $fan->addChild('Item');
                $item->addChild('Label', $this->_toUTF8($arrValue['label']));
                $item->addChild('Value', $this->_toUTF8($arrValue['value']));
                $item->addChild('Min', $this->_toUTF8($arrValue['min']));
            }
        }
        $arrBuff = $mbinfo_data->voltage();
        if (sizeof($arrBuff) > 0) {
            $volt = $mbinfo->addChild('Voltage');
            foreach ($arrBuff as $arrValue) {
                $item = $volt->addChild('Item');
                $item->addChild('Label', $this->_toUTF8($arrValue['label']));
                $item->addChild('Value', $this->_toUTF8($arrValue['value']));
                $item->addChild('Min', $this->_toUTF8($arrValue['min']));
                $item->addChild('Max', $this->_toUTF8($arrValue['max']));
            }
        }
    }
    
    /**
     * generate the hddtemp information
     *
     * @return void
     */
    private function _buildHddtemp()
    {
        $hddtemp_data = new hddtemp();
        $arrBuf = $hddtemp_data->temperature();
        $hddtemp = $this->_xml->addChild('HDDTemp');
        for ($i = 0, $max = sizeof($arrBuf); $i < $max; $i++) {
            $item = $hddtemp->addChild('Item');
            $item->addChild('Label', $this->_toUTF8($arrBuf[$i]['label']));
            $item->addChild('Value', $this->_toUTF8($arrBuf[$i]['value']));
            $item->addChild('Model', $this->_toUTF8($arrBuf[$i]['model']));
        }
    }
    
    /**
     * generate the ups information
     *
     * @return void
     */
    private function _buildUpsinfo()
    {
        $upsinfoclass = PSI_UPS_PROGRAM;
        $upsinfo_data = new $upsinfoclass();
        $arrBuf = $upsinfo_data->info();
        if (isset($arrBuf) && ! empty($arrBuf)) {
            $upsinfo = $this->_xml->addChild('UPSinfo');
            for ($i = 0, $max = sizeof($arrBuf); $i < $max; $i++) {
                $item = $upsinfo->addChild('Ups');
                $item->addChild('Name', $this->_toUTF8($arrBuf[$i]['name']));
                $item->addChild('Model', $this->_toUTF8($arrBuf[$i]['model']));
                $item->addChild('Mode', $this->_toUTF8($arrBuf[$i]['mode']));
                $item->addChild('StartTime', $this->_toUTF8($arrBuf[$i]['start_time']));
                $item->addChild('Status', $this->_toUTF8($arrBuf[$i]['status']));
                $item->addChild('UPSTemperature', $this->_toUTF8($arrBuf[$i]['temperature']));
                $item->addChild('OutagesCount', $this->_toUTF8($arrBuf[$i]['outages_count']));
                $item->addChild('LastOutage', $this->_toUTF8($arrBuf[$i]['last_outage']));
                $item->addChild('LastOutageFinish', $this->_toUTF8($arrBuf[$i]['last_outage_finish']));
                $item->addChild('LineVoltage', $this->_toUTF8($arrBuf[$i]['line_voltage']));
                $item->addChild('LoadPercent', $this->_toUTF8($arrBuf[$i]['load_percent']));
                $item->addChild('BatteryVoltage', $this->_toUTF8($arrBuf[$i]['battery_voltage']));
                $item->addChild('BatteryChargePercent', $this->_toUTF8($arrBuf[$i]['battery_charge_percent']));
                $item->addChild('TimeLeftMinutes', $this->_toUTF8($arrBuf[$i]['time_left_minutes']));
            }
        }
    }
    
    /**
     * generate the xml document
     *
     * @return void
     */
    private function _buildXml()
    {
        if (!$this->_plugin_request || $this->_complete_request) {
            $this->_buildVitals();
            $this->_buildNetwork();
            $this->_buildHardware();
            $this->_buildMemory();
            $this->_buildFilesystems();
            if (PSI_MBINFO) {
                $this->_buildMbinfo();
            }
            if (PSI_HDDTEMP) {
                $this->_buildHddtemp();
            }
            if (PSI_UPSINFO) {
                $this->_buildUpsinfo();
            }
        }
        if ($this->_plugin_request || $this->_complete_request) {
            $this->_buildPlugins();
        }
        $this->_errors->errorsAddToXML($this->_xml);
    }
    
    /**
     * get the xml object
     *
     * @return string
     */
    public function getXml()
    {
        $this->_buildXml();
        return $this->_xml;
    }
    
    /**
     * include xml-trees of the plugins to the main xml
     *
     * @return void
     */
    private function _buildPlugins()
    {
        if (count($this->_plugins) > 0) {
            $plugins = array();
            $pluginroot = $this->_xml->addChild("Plugins");
            if ($this->_complete_request) {
                $plugins = $this->_plugins;
            }
            if ($this->_plugin_request) {
                $plugins = array($this->_plugin);
            }
            foreach ($plugins as $plugin) {
                $object = new $plugin();
                $object->execute();
                $this->_combinexml($pluginroot, $object->xml($this->_sysinfo->getEncoding()));
            }
        }
    }
    
    /**
     * append a xml-tree to another xml-tree
     *
     * @param SimpleXMLElement $parent    parent to which should be appended
     * @param SimpleXMLElement $new_child child that should be appended
     *
     * @return void
     */
    private function _combinexml(SimpleXMLElement $parent, SimpleXMLElement $new_child)
    {
        $node1 = dom_import_simplexml($parent);
        $dom_sxe = dom_import_simplexml($new_child);
        $node2 = $node1->ownerDocument->importNode($dom_sxe, true);
        $node1->appendChild($node2);
    }
    
    /**
     * convert a string into an UTF-8 string
     *
     * @param string $str        string to convert
     * @param string $strEncFrom base encoding of the string that should be converted
     *
     * @return string UTF-8 string
     */
    public static function toUTF8($str, $strEncFrom = null)
    {
        if (mb_detect_encoding($str) == 'UTF-8' && !defined('PSI_CONVERT_FROM_CHARSET')) {
            return trim($str);
        } else {
            if (defined('PSI_CONVERT_FROM_CHARSET')) {
                return mb_convert_encoding(trim($str), 'UTF-8', PSI_CONVERT_FROM_CHARSET);
            } else {
                if ($strEncFrom != null) {
                    return mb_convert_encoding(trim($str), 'UTF-8', $strEncFrom);
                } else {
                    return mb_convert_encoding(trim($str), 'UTF-8');
                }
            }
        }
    }
    
    /**
     * private wrapper for convert a string to utf8 by reading base encoding from sysinfo object
     *
     * @param string $str string which should be converted
     *
     * @return string UTF8 encoded string
     */
    private function _toUTF8($str)
    {
        return XML::toUTF8($str, $this->_sysinfo->getEncoding());
    }
    
    /**
     * build the xml structure where the content can be inserted
     *
     * @return void
     */
    private function _xmlbody()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement("phpsysinfo");
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:noNamespaceSchemaLocation', 'phpsysinfo.xsd');
        $dom->appendChild($root);
        $this->_xml = simplexml_import_dom($dom);
        $generation = $this->_xml->addChild('Generation');
        $generation->addAttribute('version', CommonFunctions::PSI_VERSION);
        $generation->addAttribute('timestamp', time());
        $options = $this->_xml->addChild('Options');
        $options->addChild('tempFormat', $this->_toUTF8(defined('PSI_TEMP_FORMAT') ? PSI_TEMP_FORMAT : 'c'));
        $options->addChild('byteFormat', $this->_toUTF8(defined('PSI_BYTE_FORMAT') ? PSI_BYTE_FORMAT : 'auto_binary'));
        $options->addChild('refresh', $this->_toUTF8(defined('PSI_REFRESH') ? PSI_REFRESH : 0));
        $options->addChild('showPickListTemplate', $this->_toUTF8(defined('PSI_SHOW_PICKLIST_TEMPLATE') ? PSI_SHOW_PICKLIST_TEMPLATE : false));
        $options->addChild('showPickListLang', $this->_toUTF8(defined('PSI_SHOW_PICKLIST_LANG') ? PSI_SHOW_PICKLIST_LANG : false));
        $plug = $options->addChild('Used_Plugins');
        if ($this->_complete_request && count($this->_plugins) > 0) {
            foreach ($this->_plugins as $plugin) {
                $plug->addChild('Plugin', $this->_toUTF8($plugin));
            }
        } elseif ($this->_plugin_request && count($this->_plugins) > 0) {
            $plug->addChild('Plugin', $this->_toUTF8($this->_plugin));
        }
        
    }
}
?>
