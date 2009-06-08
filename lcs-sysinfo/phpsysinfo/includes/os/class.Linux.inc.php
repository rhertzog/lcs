<?php 
/**
 * Linux System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Linux.inc.php 197 2009-04-30 10:41:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * Linux sysinfo class
 * get all the required information from Linux system
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Linux extends OS implements PSI_Interface_OS {
    /**
     * icon name for unknown distributions
     *
     * @var string
     */
    private $_icon = "unknown.png";
    
    /**
     * name for unknown distributions
     *
     * @var string
     */
    private $_distro = "unknown";
    
    /**
     * get the distro name and icon when create the sysinfo object
     * create the parser and set some default values
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $distro_info = "";
        $buf = "";
        $list = @parse_ini_file(APP_ROOT."/distros.ini", true);
        if (!$list) {
            return;
        } 
        // We have the '2> /dev/null' because Ubuntu gives an error on this command which causes the distro to be unknown
        if (CommonFunctions::executeProgram('lsb_release', '-a 2> /dev/null', $distro_info, PSI_DEBUG)) {
            $distro_tmp = split("\n", $distro_info);
            foreach ($distro_tmp as $info) {
                $info_tmp = split(':', $info, 2);
                $distro[$info_tmp[0]] = trim($info_tmp[1]);
                if (isset($list[$distro['Distributor ID']]['Image'])) {
                    $this->_icon = $list[$distro['Distributor ID']]['Image'];
                } 
                $this->_distro = $distro['Description'];
            } 
        } else {
            // Fall back in case 'lsb_release' does not exist ;)
            foreach ($list as $section=>$distribution) {
                if (!isset($distribution["Files"])) {
                    continue;
                } else {
                    foreach (explode(";", $distribution["Files"]) as $filename) {
                        if (file_exists($filename)) {
                            CommonFunctions::rfts($filename, $buf);
                            if (isset($distribution["Image"])) {
                                $this->_icon = $distribution["Image"];
                            } 
                            if (isset($distribution["Name"])) {
                                $this->_distro = $distribution["Name"]." ".trim($buf);
                            } else {
                                $this->_distro = trim($buf);
                            } 
                            break 2;
                        } 
                    } 
                } 
            } 
        } 
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
        $result = "";
        if (CommonFunctions::rfts('/proc/sys/kernel/hostname', $result, 1)) {
            $result = trim($result);
            $ip = gethostbyname($result);
            if ($ip != $result) {
                $result = gethostbyaddr($ip);
            } else {
                $result = 'Unknown';
            } 
        } else {
            $result = "N.A.";
        } 
        return $result;
    }
    
    /**
     * IP of the Canonical Host Name
     *
     * @return string
     */
    public function ipaddr()
    {
        if (!($result = getenv('SERVER_ADDR'))) {
            $result = gethostbyname($this->chostname());
        } 
        return $result;
    }
    
    /**
     * Kernel Version
     *
     * @return string
     */
    public function kernel()
    {
        $strBuf = "";
        if (CommonFunctions::executeProgram('uname', '-r', $strBuf, PSI_DEBUG)) {
            $result = trim($strBuf);
            if (CommonFunctions::executeProgram('uname', '-v', $strBuf, PSI_DEBUG)) {
                if (preg_match('/SMP/', $strBuf)) {
                    $result .= ' (SMP)';
                } 
            } 
            if (CommonFunctions::executeProgram('uname', '-m', $strBuf, PSI_DEBUG)) {
                $result .= ' '.trim($strBuf);
            } 
        } else {
            if (CommonFunctions::rfts('/proc/version', $strBuf, 1)) {
                if (preg_match('/version (.*?) /', $strBuf, $ar_buf)) {
                    $result = $ar_buf[1];
                    if (preg_match('/SMP/', $strBuf)) {
                        $result .= ' (SMP)';
                    } 
                } else {
                    $result = "N.A.";
                } 
            } else {
                $result = "N.A.";
            } 
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
        $buf = "";
        CommonFunctions::rfts('/proc/uptime', $buf, 1);
        $ar_buf = split(' ', $buf);
        $result = trim($ar_buf[0]);
        return $result;
    }
    
    /**
     * Number of Users
     *
     * @return integer
     */
    public function users()
    {
        $strResult = -1;
        $strBuf = "";
        if (CommonFunctions::executeProgram('who', '-q', $strBuf, PSI_DEBUG)) {
            $arrWho = split('=', $strBuf);
            $strResult = $arrWho[1];
        } 
        return $strResult;
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
        $buf = "";
        if (CommonFunctions::rfts('/proc/loadavg', $buf)) {
            $results['avg'] = preg_split("/\s/", $buf, 4);
            // don't need the extra values, only first three
            unset($results['avg'][3]);
        } else {
            $results['avg'] = array('N.A.', 'N.A.', 'N.A.');
        } 
        if ($bar) {
            if (CommonFunctions::rfts('/proc/stat', $buf, 1)) {
                $ab = 0;
                $ac = 0;
                $ad = 0;
                $ae = 0;
                sscanf($buf, "%*s %Ld %Ld %Ld %Ld", $ab, $ac, $ad, $ae);
                // Find out the CPU load
                // user + sys = load
                // total = total
                $load = $ab + $ac + $ad; // cpu.user + cpu.sys
                $total = $ab + $ac + $ad + $ae; // cpu.total
                // we need a second value, wait 1 second befor getting (< 1 second no good value will occour)
                sleep(1);
                CommonFunctions::rfts('/proc/stat', $buf, 1);
                sscanf($buf, "%*s %Ld %Ld %Ld %Ld", $ab, $ac, $ad, $ae);
                $load2 = $ab + $ac + $ad;
                $total2 = $ab + $ac + $ad + $ae;
                $results['cpupercent'] = (100 * ($load2 - $load)) / ($total2 - $total);
            } 
        } 
        return $results;
    }
    
    /**
     * CPU information
     * All of the tags here are highly architecture dependant.
     *
     * @return array
     */
    public function cpuinfo()
    {
        $bufr = "";
        $results = array("cpus"=>0);
        if (CommonFunctions::rfts('/proc/cpuinfo', $bufr)) {
            $bufe = explode("\n", $bufr);
            $results = array('cpus'=>0, 'bogomips'=>0);
            $arrBuff = array();
            foreach ($bufe as $buf) {
                $arrBuff = preg_split('/\s+:\s+/', trim($buf));
                if (count($arrBuff) == 2) {
                    $key = $arrBuff[0];
                    $value = $arrBuff[1];
                    switch ($key) {
                    case 'model name': 
                        $results['model'] = $value;
                        break;
                    case 'cpu MHz': 
                        $results['cpuspeed'] = sprintf('%.2f', $value);
                        break;
                    case 'cycle frequency [Hz]': 
                        $results['cpuspeed'] = sprintf('%.2f', $value / 1000000); // For Alpha arch - 2.2.x
                        break;
                    case 'clock': 
                        $results['cpuspeed'] = sprintf('%.2f', $value); // For PPC arch (damn borked POS)
                        break;
                    case 'cpu': 
                        $results['model'] = $value; // For PPC arch (damn borked POS)
                        break;
                    case 'L2 cache': 
                        $results['cache'] = $value * 1024; // More for PPC
                        break;
                    case 'revision': 
                        $results['model'] .= ' ( rev: '.$value.')'; // For PPC arch (damn borked POS)
                        break;
                    case 'cpu model': 
                        $results['model'] .= ' ('.$value.')'; // For Alpha arch - 2.2.x
                        break;
                    case 'cache size': 
                        $results['cache'] = (preg_replace("/[a-zA-Z]/", "", $value)) * 1024;
                        break;
                    case 'bogomips': 
                        $results['bogomips'] += $value;
                        break;
                    case 'BogoMIPS': 
                        $results['bogomips'] += $value; // For alpha arch - 2.2.x
                        break;
                    case 'BogoMips': 
                        $results['bogomips'] += $value; // For sparc arch
                        break;
                    case 'cpus detected': 
                        $results['cpus'] += $value; // For Alpha arch - 2.2.x
                        break;
                    case 'system type': 
                        $results['model'] .= ', '.$value.' '; // Alpha arch - 2.2.x
                        break;
                    case 'platform string': 
                        $results['model'] .= ' ('.$value.')'; // Alpha arch - 2.2.x
                        break;
                    case 'processor': 
                        $results['cpus'] += 1;
                        break;
                    case 'Cpu0ClkTck': 
                        $results['cpuspeed'] = sprintf('%.2f', hexdec($value) / 1000000); // Linux sparc64
                        break;
                    case 'Cpu0Bogo': 
                        $results['bogomips'] += $value; // Linux sparc64 & sparc32
                        break;
                    case 'ncpus probed': 
                        $results['cpus'] = $value; // Linux sparc64 & sparc32
                        break;
                    } 
                } 
            } 
            // sparc64 specific code follows
            // This adds the ability to display the cache that a CPU has
            // Originally made by Sven Blumenstein <bazik@gentoo.org> in 2004
            // Modified by Tom Weustink <freshy98@gmx.net> in 2004
            $sparclist = array('SUNW,UltraSPARC@0,0', 'SUNW,UltraSPARC-II@0,0', 'SUNW,UltraSPARC@1c,0', 'SUNW,UltraSPARC-IIi@1c,0', 'SUNW,UltraSPARC-II@1c,0', 'SUNW,UltraSPARC-IIe@0,0');
            foreach ($sparclist as $name) {
                if (CommonFunctions::rfts('/proc/openprom/'.$name.'/ecache-size', $buf, 1, 32, false)) {
                    $results['cache'] = base_convert($buf, 16, 10);
                } 
            } 
            // sparc64 specific code ends
            // XScale detection code
            if ($results['cpus'] == 0) {
                foreach ($bufe as $buf) {
                    $fields = preg_split('/\s*:\s*/', trim($buf), 2);
                    if (sizeof($fields) == 2) {
                        list($key, $value) = $fields;
                        switch ($key) {
                        case 'Processor': 
                            $results['cpus'] += 1;
                            $results['model'] = $value;
                            break;
                        case 'BogoMIPS': 
                            $results['cpuspeed'] = $value; //BogoMIPS are not BogoMIPS on this CPU, it's the speed, no BogoMIPS available
                            break;
                        case 'I size': 
                            $results['cache'] = $value * 1024;
                            break;
                        case 'D size': 
                            $results['cache'] += $value * 1024;
                            break;
                        } 
                    } 
                } 
            } 
        } 
        $keys = array_keys($results);
        $keys2be = array('model', 'cpuspeed', 'cpus');
        while ($ar_buf = each($keys2be)) {
            if (!in_array($ar_buf[1], $keys)) {
                $results[$ar_buf[1]] = '0';
            } 
        } 
        if (CommonFunctions::rfts('/proc/acpi/thermal_zone/THRM/temperature', $buf, 1, 4096, false)) {
            $results['temp'] = substr($buf, 25, 2);
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
        $strBuf = "";
        $arrResults = array();
        $booDevice = false;
        if (!$arrResults = Parser::lspci()) {
            if (CommonFunctions::rfts('/proc/pci', $strBuf, 0, 4096, false)) {
                $arrBuf = explode("\n", $strBuf);
                foreach ($arrBuf as $strLine) {
                    if (preg_match('/Bus/', $strLine)) {
                        $booDevice = true;
                        continue;
                    } 
                    if ($booDevice) {
                        list($strKey, $strValue) = split(': ', $strLine, 2);
                        if (!preg_match('/bridge/i', $strKey) && !preg_match('/USB/i ', $strKey)) {
                            $arrResults[] = preg_replace('/\([^\)]+\)\.$/', '', trim($strValue));
                        } 
                        $booDevice = false;
                    } 
                } 
                asort($arrResults);
            } 
        } 
        return $arrResults;
    }
    
    /**
     * IDE devices
     *
     * @return array
     */
    public function ide()
    {
        $results = array();
        $buf = "";
        $bufd = CommonFunctions::gdc('/proc/ide', false);
        foreach ($bufd as $file) {
            if (preg_match('/^hd/', $file)) {
                $results[$file] = array();
                if (CommonFunctions::rfts("/proc/ide/".$file."/media", $buf, 1)) {
                    $results[$file]['media'] = trim($buf);
                    if ($results[$file]['media'] == 'disk') {
                        $results[$file]['media'] = 'Hard Disk';
                        if (CommonFunctions::rfts("/proc/ide/".$file."/capacity", $buf, 1, 4096, false) || CommonFunctions::rfts("/sys/block/".$file."/size", $buf, 1, 4096, false)) {
                            $results[$file]['capacity'] = trim($buf) * 512 / 1024;
                        } 
                    } elseif ($results[$file]['media'] == 'cdrom') {
                        $results[$file]['media'] = 'CD-ROM';
                        unset($results[$file]['capacity']);
                    } 
                } else {
                    unset($results[$file]);
                } 
                if (CommonFunctions::rfts("/proc/ide/".$file."/model", $buf, 1)) {
                    $results[$file]['model'] = trim($buf);
                    if (preg_match('/WDC/', $results[$file]['model'])) {
                        $results[$file]['manufacture'] = 'Western Digital';
                    } elseif (preg_match('/IBM/', $results[$file]['model'])) {
                        $results[$file]['manufacture'] = 'IBM';
                    } elseif (preg_match('/FUJITSU/', $results[$file]['model'])) {
                        $results[$file]['manufacture'] = 'Fujitsu';
                    } else {
                        $results[$file]['manufacture'] = 'Unknown';
                    } 
                } 
            } 
        } 
        asort($results);
        return $results;
    }
    
    /**
     * SCSI devices
     *
     * @return array
     */
    public function scsi()
    {
        $bufr = "";
        $results = array();
        $dev_vendor = '';
        $dev_model = '';
        $dev_rev = '';
        $dev_type = '';
        $s = 1;
        $get_type = 0;
        if (CommonFunctions::executeProgram('lsscsi', '-c', $bufr, PSI_DEBUG) || CommonFunctions::rfts('/proc/scsi/scsi', $bufr, 0, 4096, PSI_DEBUG)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                if (preg_match('/Vendor/', $buf)) {
                    preg_match('/Vendor: (.*) Model: (.*) Rev: (.*)/i', $buf, $dev);
                    list($key, $value) = split(': ', $buf, 2);
                    $dev_str = $value;
                    $get_type = true;
                    continue;
                } 
                if ($get_type) {
                    preg_match('/Type:\s+(\S+)/i', $buf, $dev_type);
                    $results[$s]['model'] = "$dev[1] $dev[2] ($dev_type[1])";
                    $results[$s]['media'] = "Hard Disk";
                    $s++;
                    $get_type = false;
                } 
            } 
        } 
        asort($results);
        return $results;
    }
    
    /**
     * USB devices
     *
     * @return array
     */
    public function usb()
    {
        $bufr = "";
        $results = array();
        $devnum = -1;
        if (!CommonFunctions::executeProgram('lsusb', '', $bufr, PSI_DEBUG)) {
            if (CommonFunctions::rfts('/proc/bus/usb/devices', $bufr, 0, 4096, false)) {
                $bufe = explode("\n", $bufr);
                foreach ($bufe as $buf) {
                    if (preg_match('/^T/', $buf)) {
                        $devnum += 1;
                        $results[$devnum] = "";
                    } elseif (preg_match('/^S:/', $buf)) {
                        list($key, $value) = split(': ', $buf, 2);
                        list($key, $value2) = split('=', $value, 2);
                        if (trim($key) != "SerialNumber") {
                            $results[$devnum] .= " ".trim($value2);
                            $devstring = 0;
                        } 
                    } 
                } 
            } 
        } else {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                $device = preg_split("/ /", $buf, 7);
                if (isset($device[6]) && trim($device[6]) != "") {
                    $results[$devnum++] = trim($device[6]);
                } 
            } 
        } 
        return $results;
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
        $bufr = "";
        if (CommonFunctions::rfts('/proc/net/dev', $bufr)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                if (preg_match('/:/', $buf)) {
                    list($dev_name, $stats_list) = preg_split('/:/', $buf, 2);
                    $dev_name = trim($dev_name);
                    $stats = preg_split('/\s+/', trim($stats_list));
                    $results[$dev_name] = array();
                    $results[$dev_name]['rx_bytes'] = $stats[0];
                    $results[$dev_name]['tx_bytes'] = $stats[8];
                    $results[$dev_name]['errs'] = $stats[2] + $stats[10];
                    $results[$dev_name]['drop'] = $stats[3] + $stats[11];
                } 
            } 
        } 
        return $results;
    }
    
    /**
     * Physical memory information and Swap Space information
     *
     * @return array
     */
    public function memory()
    {
        $bufr = "";
        $results['ram'] = array('total'=>0, 'free'=>0, 'used'=>0, 'percent'=>0);
        $results['swap'] = array('total'=>0, 'free'=>0, 'used'=>0, 'percent'=>0);
        $results['devswap'] = array();
        if (CommonFunctions::rfts('/proc/meminfo', $bufr)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                if (preg_match('/^MemTotal:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $results['ram']['total'] = $ar_buf[1] * 1024;
                } elseif (preg_match('/^MemFree:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $results['ram']['free'] = $ar_buf[1] * 1024;
                } elseif (preg_match('/^Cached:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $results['ram']['cached'] = $ar_buf[1] * 1024;
                } elseif (preg_match('/^Buffers:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $results['ram']['buffers'] = $ar_buf[1] * 1024;
                } 
            } 
            $results['ram']['used'] = $results['ram']['total'] - $results['ram']['free'];
            $results['ram']['percent'] = round(($results['ram']['used'] * 100) / $results['ram']['total']);
            // values for splitting memory usage
            if (isset($results['ram']['cached']) && isset($results['ram']['buffers'])) {
                $results['ram']['app'] = $results['ram']['used'] - $results['ram']['cached'] - $results['ram']['buffers'];
                $results['ram']['app_percent'] = round(($results['ram']['app'] * 100) / $results['ram']['total']);
                $results['ram']['buffers_percent'] = round(($results['ram']['buffers'] * 100) / $results['ram']['total']);
                $results['ram']['cached_percent'] = round(($results['ram']['cached'] * 100) / $results['ram']['total']);
            } 
            $bufr = "";
            if (CommonFunctions::rfts('/proc/swaps', $bufr)) {
                $swaps = explode("\n", $bufr);
                for ($i = 1, $max = sizeof($swaps); $i < $max; $i++) {
                    if (trim($swaps[$i]) != "") {
                        $ar_buf = preg_split('/\s+/', $swaps[$i], 6);
                        $results['devswap'][$i - 1] = array();
                        $results['devswap'][$i - 1]['dev'] = $ar_buf[0];
                        $results['devswap'][$i - 1]['total'] = $ar_buf[2] * 1024;
                        $results['devswap'][$i - 1]['used'] = $ar_buf[3] * 1024;
                        $results['devswap'][$i - 1]['free'] = ($results['devswap'][$i - 1]['total'] - $results['devswap'][$i - 1]['used']);
                        $results['devswap'][$i - 1]['percent'] = round(($ar_buf[3] * 100) / $ar_buf[2]);
                        $results['swap']['total'] += $ar_buf[2] * 1024;
                        $results['swap']['used'] += $ar_buf[3] * 1024;
                        $results['swap']['free'] = $results['swap']['total'] - $results['swap']['used'];
                        $results['swap']['percent'] = round(($results['swap']['used'] * 100) / (($results['swap']['total'] <= 0) ? 1 : $results['swap']['total']));
                    } 
                } 
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
        return Parser::df("-P");
    }
    
    /**
     * Distribution
     *
     * @return string
     */
    public function distro()
    {
        return $this->_distro;
    }
    
    /**
     * Distribution Icon
     *
     * @return string
     */
    public function distroicon()
    {
        return $this->_icon;
    } 
}
?>
