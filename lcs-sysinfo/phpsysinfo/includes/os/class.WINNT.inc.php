<?php 
/**
 * WINNT System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.WINNT.inc.php 231 2009-06-06 12:32:46Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * WINNT sysinfo class
 * get all the required information from WINNT systems
 * information are retrieved through the WMI interface
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class WINNT extends OS
{
    /**
     * holds the COM object that we pull all the WMI data from
     *
     * @var Object
     */
    private $_wmi;
    
    /**
     * holds all devices, which are in the system
     *
     * @var array
     */
    private $_wmidevices;
    
    /**
     * store language encoding of the system to convert some output to utf-8
     *
     * @var string
     */
    private $_charset = "";
    
    /**
     * build the global Error object and create the WMI connection
     */
    public function __construct()
    {
        parent::__construct();
        // don't set this params for local connection, it will not work
        $strHostname = '';
        $strUser = '';
        $strPassword = '';
        
        // initialize the wmi object
        $objLocator = new COM('WbemScripting.SWbemLocator');
        if ($strHostname == "") {
            $this->_wmi = $objLocator->ConnectServer();
        } else {
            $this->_wmi = $objLocator->ConnectServer($strHostname, 'rootcimv2', $strHostname.'\\'.$strUser, $strPassword);
        }
        $this->_getCodeSet();
    }
    
    /**
     * store the codepage of the os for converting some strings to utf-8
     *
     * @return void
     */
    private function _getCodeSet()
    {
        $buffer = $this->_getWMI('Win32_OperatingSystem', array('CodeSet'));
        $this->_charset = 'windows-'.$buffer[0]['CodeSet'];
    }
    
    /**
     * function for getting a list of values in the specified context
     * optionally filter this list, based on the list from second parameter
     *
     * @param string $strClass name of the class where the values are stored
     * @param array  $strValue filter out only needed values, if not set all values of the class are returned
     *
     * @return array content of the class stored in an array
     */
    private function _getWMI($strClass, $strValue = array())
    {
        $arrData = array();
        $value = "";
        try {
            $objWEBM = $this->_wmi->Get($strClass);
            $arrProp = $objWEBM->Properties_;
            $arrWEBMCol = $objWEBM->Instances_();
            foreach ($arrWEBMCol as $objItem) {
                @reset($arrProp);
                $arrInstance = array();
                foreach ($arrProp as $propItem) {
                    eval("\$value = \$objItem->".$propItem->Name.";");
                    if ( empty($strValue)) {
                        $arrInstance[$propItem->Name] = trim($value);
                    } else {
                        if (in_array($propItem->Name, $strValue)) {
                            $arrInstance[$propItem->Name] = trim($value);
                        }
                    }
                }
                $arrData[] = $arrInstance;
            }
        }
        catch(Exception $e) {
            if (PSI_DEBUG) {
                $this->error->addError($e->getCode(), $e->getMessage());
            }
        }
        return $arrData;
    }
    
    /**
     * retrieve different device types from the system based on selector
     *
     * @param string $strType type of the devices that should be returned
     *
     * @return array list of devices of the specified type
     */
    private function _devicelist($strType)
    {
        if ( empty($this->_wmidevices)) {
            $this->_wmidevices = $this->_getWMI('Win32_PnPEntity', array('Name', 'PNPDeviceID'));
        }
        $list = array();
        foreach ($this->_wmidevices as $device) {
            if (substr($device['PNPDeviceID'], 0, strpos($device['PNPDeviceID'], "\\") + 1) == ($strType."\\")) {
                $list[] = $device['Name'];
            }
        }
        return $list;
    }
    
    /**
     * Virtual Host Name
     *
     * @return string
     */
    public function vhostname()
    {
        if (!($result = getenv('SERVER_NAME'))) {
            $result = 'N.A.';
        }
        return $result;
    }
    
    /**
     * IP of the Virtual Host Name
     *
     *  @return string
     */
    public function vipaddr()
    {
        return gethostbyname($this->vhostname());
    }
    
    /**
     * Canonical Host Name
     *
     @return string
     */
    public function chostname()
    {
        $buffer = $this->_getWMI('Win32_ComputerSystem', array('Name'));
        $result = $buffer[0]['Name'];
        $ip = gethostbyname($result);
        if ($ip != $result) {
            return gethostbyaddr($ip);
        } else {
            return 'Unknown';
        }
    }
    
    /**
     * IP of the Canonical Host Name
     *
     * @return string
     */
    public function ipaddr()
    {
        $buffer = $this->_getWMI('Win32_ComputerSystem', array('Name'));
        $result = $buffer[0]['Name'];
        return gethostbyname($result);
    }
    
    /**
     * Windows Version
     * includes also ServicePack Version if installed
     *
     * @return string
     */
    public function kernel()
    {
        $buffer = $this->_getWMI('Win32_OperatingSystem', array('Version', 'ServicePackMajorVersion'));
        $result = $buffer[0]['Version'];
        if ($buffer[0]['ServicePackMajorVersion'] > 0) {
            $result .= ' SP'.$buffer[0]['ServicePackMajorVersion'];
        }
        return $result;
    }
    
    /**
     * UpTime
     * time the system is running
     *
     * @return integer
     */
    public function uptime()
    {
        $result = 0;
        date_default_timezone_set('UTC');
        $buffer = $this->_getWMI('Win32_OperatingSystem', array('LastBootUpTime', 'LocalDateTime'));
        $byear = intval(substr($buffer[0]['LastBootUpTime'], 0, 4));
        $bmonth = intval(substr($buffer[0]['LastBootUpTime'], 4, 2));
        $bday = intval(substr($buffer[0]['LastBootUpTime'], 6, 2));
        $bhour = intval(substr($buffer[0]['LastBootUpTime'], 8, 2));
        $bminute = intval(substr($buffer[0]['LastBootUpTime'], 10, 2));
        $bseconds = intval(substr($buffer[0]['LastBootUpTime'], 12, 2));
        $lyear = intval(substr($buffer[0]['LocalDateTime'], 0, 4));
        $lmonth = intval(substr($buffer[0]['LocalDateTime'], 4, 2));
        $lday = intval(substr($buffer[0]['LocalDateTime'], 6, 2));
        $lhour = intval(substr($buffer[0]['LocalDateTime'], 8, 2));
        $lminute = intval(substr($buffer[0]['LocalDateTime'], 10, 2));
        $lseconds = intval(substr($buffer[0]['LocalDateTime'], 12, 2));
        $boottime = mktime($bhour, $bminute, $bseconds, $bmonth, $bday, $byear);
        $localtime = mktime($lhour, $lminute, $lseconds, $lmonth, $lday, $lyear);
        $result = $localtime - $boottime;
        return $result;
    }
    
    /**
     * Number of Users
     *
     * @return integer
     */
    public function users()
    {
        $users = 0;
        $buffer = $this->_getWMI('Win32_Process', array('Caption'));
        foreach ($buffer as $process) {
            if (strtoupper($process['Caption']) == strtoupper('explorer.exe')) {
                $users++;
            }
        }
        return $users;
    }
    
    /**
     * Processor Load
     * optionally create a loadbar
     *
     * @param boolean $bar include a bar
     *
     * @return array
     */
    public function loadavg($bar = false)
    {
        $buffer = $this->_getWMI('Win32_Processor', array('LoadPercentage'));
        $cpuload = array();
        for ($i = 0, $cnt_buffer = count($buffer); $i < $cnt_buffer; $i++) {
            $cpuload['avg'][] = $buffer[$i]['LoadPercentage'];
        }
        if ($bar) {
            $cpuload['cpupercent'] = array_sum($cpuload['avg']) / count($buffer);
        }
        return $cpuload;
    }
    
    /**
     * CPU information
     *
     * @return array
     */
    public function cpuinfo()
    {
        $buffer = $this->_getWMI('Win32_Processor', array('Name', 'L2CacheSize', 'CurrentClockSpeed', 'ExtClock', 'NumberOfCores'));
        $results['cpus'] = 0;
        foreach ($buffer as $cpu) {
            if (isset($cpu['NumberOfCores'])) {
                $results['cpus'] += $cpu['NumberOfCores'];
            } else {
                $results['cpus']++;
            }
            $results['model'] = $cpu['Name'];
            $results['cache'] = $cpu['L2CacheSize'] * 1024;
            $results['cpuspeed'] = $cpu['CurrentClockSpeed'];
            $results['busspeed'] = $cpu['ExtClock'];
        }
        return $results;
    }
    
    /**
     * PCI devices
     *
     * @return array
     */
    public function pci()
    {
        $pci = $this->_devicelist('PCI');
        return $pci;
    }
    
    /**
     * IDE devices
     *
     * @return array
     */
    public function ide()
    {
        $buffer = $this->_devicelist('IDE');
        $ide = array();
        foreach ($buffer as $device) {
            $ide[]['model'] = $device;
        }
        return $ide;
    }
    
    /**
     * SCSI devices
     *
     *  @return array
     */
    public function scsi()
    {
        $buffer = $this->_devicelist('SCSI');
        $scsi = array();
        foreach ($buffer as $device) {
            $scsi[]['model'] = $device;
        }
        return $scsi;
    }
    
    /**
     * USB devices
     *
     * @return array
     */
    public function usb()
    {
        $usb = $this->_devicelist('USB');
        return $usb;
    }
    
    /**
     * Network devices
     * includes also rx/tx bytes
     *
     * @return array
     */
    public function network()
    {
        $results = array();
        $buffer = $this->_getWMI('Win32_PerfRawData_Tcpip_NetworkInterface');
        foreach ($buffer as $device) {
            $dev_name = $device['Name'];
            // http://msdn.microsoft.com/library/default.asp?url=/library/en-us/wmisdk/wmi/win32_perfrawdata_tcpip_networkinterface.asp
            // there is a possible bug in the wmi interfaceabout uint32 and uint64: http://www.ureader.com/message/1244948.aspx, so that
            // magative numbers would occour, try to calculate the nagative value from total - positive number
            if ($device['BytesSentPersec'] < 0) {
                $results[$dev_name]['tx_bytes'] = $device['BytesTotalPersec'] - $device['BytesReceivedPersec'];
            } else {
                $results[$dev_name]['tx_bytes'] = $device['BytesSentPersec'];
            }
            if ($device['BytesReceivedPersec'] < 0) {
                $results[$dev_name]['rx_bytes'] = $device['BytesTotalPersec'] - $device['BytesSentPersec'];
            } else {
                $results[$dev_name]['rx_bytes'] = $device['BytesReceivedPersec'];
            }
            $results[$dev_name]['errs'] = $device['PacketsReceivedErrors'];
            $results[$dev_name]['drop'] = $device['PacketsReceivedDiscarded'];
        }
        return $results;
    }
    
    /**
     * Physical memory information and Swap Space information
     *
     * @link http://msdn2.microsoft.com/En-US/library/aa394239.aspx
     * @link http://msdn2.microsoft.com/en-us/library/aa394246.aspx
     * @return array
     */
    public function memory()
    {
        $buffer = $this->_getWMI("Win32_OperatingSystem", array('TotalVisibleMemorySize', 'FreePhysicalMemory'));
        $results['ram']['total'] = $buffer[0]['TotalVisibleMemorySize'] * 1024;
        $results['ram']['free'] = $buffer[0]['FreePhysicalMemory'] * 1024;
        // Calculate used physical memory.
        $results['ram']['used'] = $results['ram']['total'] - $results['ram']['free'];
        // Calculate percent used.
        $results['ram']['percent'] = ceil(($results['ram']['used'] * 100) / $results['ram']['total']);
        // Set the swap info to zero. Just in case, I guess.
        $results['swap']['total'] = 0;
        $results['swap']['used'] = 0;
        $results['swap']['free'] = 0;
        $results['swap']['percent'] = 0;
        
        $buffer = $this->_getWMI('Win32_PageFileUsage');
        $k = 0;
        $results['devswap'] = array();
        foreach ($buffer as $swapdevice) {
            $results['devswap'][$k]['dev'] = $swapdevice['Name'];
            $results['devswap'][$k]['total'] = $swapdevice['AllocatedBaseSize'] * 1024 * 1024;
            $results['devswap'][$k]['used'] = $swapdevice['CurrentUsage'] * 1024 * 1024;
            // Calculate free swap.
            $results['devswap'][$k]['free'] = ($swapdevice['AllocatedBaseSize'] - $swapdevice['CurrentUsage']) * 1024 * 1024;
            // Calculate percent used.
            $results['devswap'][$k]['percent'] = ceil($swapdevice['CurrentUsage'] * 100 / $swapdevice['AllocatedBaseSize']);
            // Calculate the swap totals.
            $results['swap']['total'] += $results['devswap'][$k]['total'];
            $results['swap']['used'] += $results['devswap'][$k]['used'];
            $results['swap']['free'] += $results['devswap'][$k]['free'];
            // Iterate the counter by one.
            $k += 1;
        }
        // Calculate the percent used of the total swap space.
        if ($k > 0) {
            if ($results['swap']['total'] <= 0) {
                $results['swap']['percent'] = 0;
            } else {
                $results['swap']['percent'] = ceil($results['swap']['used'] / $results['swap']['total'] * 100);
            }
        }
        return $results;
    }
    
    /**
     * filesystem information
     *
     * @return array
     */
    public function filesystems()
    {
        $typearray = array('Unknown', 'No Root Directory', 'Removable Disk', 'Local Disk', 'Network Drive', 'Compact Disc', 'RAM Disk');
        $floppyarray = array('Unknown', '5 1/4 in.', '3 1/2 in.', '3 1/2 in.', '3 1/2 in.', '3 1/2 in.', '5 1/4 in.', '5 1/4 in.', '5 1/4 in.', '5 1/4 in.', '5 1/4 in.', 'Other', 'HD', '3 1/2 in.', '3 1/2 in.', '5 1/4 in.', '5 1/4 in.', '3 1/2 in.', '3 1/2 in.', '5 1/4 in.', '3 1/2 in.', '3 1/2 in.', '8 in.');
        $buffer = $this->_getWMI('Win32_LogicalDisk', array('Name', 'Size', 'FreeSpace', 'FileSystem', 'DriveType', 'MediaType'));
        $k = 0;
        foreach ($buffer as $filesystem) {
            $results[$k]['mount'] = $filesystem['Name'];
            
            if ($filesystem['Size']) {
                $results[$k]['size'] = $filesystem['Size'];
            } else {
                $results[$k]['size'] = 0;
            }
            
            $results[$k]['used'] = ($filesystem['Size'] - $filesystem['FreeSpace']);
            
            if ($filesystem['FreeSpace']) {
                $results[$k]['free'] = $filesystem['FreeSpace'];
            } else {
                $results[$k]['free'] = 0;
            }
            
            // silence this line, nobody is having a floppy in the drive everytime
            if ($results[$k]['size'] <= 0) {
                $results[$k]['percent'] = 0;
            } else {
                $results[$k]['percent'] = ceil($results[$k]['used'] / $results[$k]['size'] * 100);
            }
            $results[$k]['fstype'] = $filesystem['FileSystem'];
            $results[$k]['disk'] = $typearray[$filesystem['DriveType']];
            if ($filesystem['MediaType'] != "" && $filesystem['DriveType'] == 2) {
                $results[$k]['disk'] .= " (".$floppyarray[$filesystem['MediaType']].")";
            }
            $k += 1;
        }
        return $results;
    }
    
    /**
     * Distribution
     *
     * @return string
     */
    public function distro()
    {
        $buffer = $this->_getWMI('Win32_OperatingSystem', array('Caption'));
        return $buffer[0]['Caption'];
    }
    
    /**
     * Distribution Icon
     *
     * @return string
     */
    public function distroicon()
    {
        $version = $this->kernel();
        if ($version[0] == 6) {
            $icon = 'vista.png';
        } else {
            $icon = 'xp.png';
        }
        return $icon;
    }
    
    /**
     * get os specific encoding
     *
     * @see OS::getEncoding()
     *
     * @return string
     */
    function getEncoding()
    {
        return $this->_charset;
    }
}
?>
