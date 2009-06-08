<?php 
/**
 * HP-UX System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.HPUX.inc.php 197 2009-04-30 10:41:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * HP-UX sysinfo class
 * get all the required information from HP-UX system
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class HPUX implements PSI_Interface_OS {
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
        $ret = "";
        if (CommonFunctions::executeProgram('hostname', '', $ret)) {
            return $ret;
        } else {
            return 'N.A.';
        } 
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
     * HP-UX Version
     *
     * @return string
     */
    public function kernel()
    {
        $ret = "";
        if (CommonFunctions::executeProgram('uname', '-srvm', $ret)) {
            return $ret;
        } else {
            return 'N.A.';
        } 
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
        $result = -1;
        $ar_buf = array();
        if (CommonFunctions::executeProgram('uptime', '', $buf)) {
            if (preg_match("/up (\d+) days,\s*(\d+):(\d+),/", $buf, $ar_buf)) {
                $min = $ar_buf[3];
                $hours = $ar_buf[2];
                $days = $ar_buf[1];
                $result = $days * 86400 + $hours * 3600 + $min * 60;
            } 
        } 
        return $result;
    }
    
    /**
     * Number of Users
     *
     * @return integer
     */
    public function users()
    {
        $ret = "";
        if (CommonFunctions::executeProgram('who', '-q', $ret)) {
            $who = split('=', $ret);
            $result = $who[1];
            return $result;
        } else {
            return - 1;
        } 
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
        $ar_buf = array();
        $results['avg'] = array('N.A.', 'N.A.', 'N.A.');
        if (CommonFunctions::executeProgram('uptime', '', $buf)) {
            if (preg_match("/average: (.*), (.*), (.*)$/", $buf, $ar_buf)) {
                $results['avg'] = array($ar_buf[1], $ar_buf[2], $ar_buf[3]);
            } 
        } 
        return $results;
    }
    
    /**
     * CPU information
     * All of the tags here are highly architecture dependant
     *
     * @return array
     */
    public function cpuinfo()
    {
        $results = array();
        $ar_buf = array();
        $bufr = "";
        if (CommonFunctions::rfts('/proc/cpuinfo', $bufr)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                list($key, $value) = preg_split('/\s+:\s+/', trim($buf), 2);
                switch ($key) {
                case 'model name': 
                    $results['model'] = $value;
                    break;
                case 'cpu MHz': 
                    $results['cpuspeed'] = sprintf('%.2f', $value);
                    break;
                case 'cycle frequency [Hz]': 
                    // For Alpha arch - 2.2.x
                    $results['cpuspeed'] = sprintf('%.2f', $value / 1000000);
                    break;
                case 'clock': 
                    // For PPC arch (damn borked POS)
                    $results['cpuspeed'] = sprintf('%.2f', $value);
                    break;
                case 'cpu': 
                    // For PPC arch (damn borked POS)
                    $results['model'] = $value;
                    break;
                case 'revision': 
                    // For PPC arch (damn borked POS)
                    $results['model'] .= ' ( rev: '.$value.')';
                    break;
                case 'cpu model': 
                    // For Alpha arch - 2.2.x
                    $results['model'] .= ' ('.$value.')';
                    break;
                case 'cache size': 
                    $results['cache'] = $value * 1024;
                    break;
                case 'bogomips': 
                    $results['bogomips'] += $value;
                    break;
                case 'BogoMIPS': 
                    // For alpha arch - 2.2.x
                    $results['bogomips'] += $value;
                    break;
                case 'BogoMips': 
                    // For sparc arch
                    $results['bogomips'] += $value;
                    break;
                case 'cpus detected': 
                    // For Alpha arch - 2.2.x
                    $results['cpus'] += $value;
                    break;
                case 'system type': 
                    // Alpha arch - 2.2.x
                    $results['model'] .= ', '.$value.' ';
                    break;
                case 'platform string': 
                    // Alpha arch - 2.2.x
                    $results['model'] .= ' ('.$value.')';
                    break;
                case 'processor': 
                    $results['cpus'] += 1;
                    break;
                } 
            } 
        } 
        $keys = array_keys($results);
        $keys2be = array('model', 'cpuspeed', 'cache', 'bogomips', 'cpus');
        while ($ar_buf = each($keys2be)) {
            if (!in_array($ar_buf[1], $keys)) {
                $results[$ar_buf[1]] = 'N.A.';
            } 
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
        $bufr = "";
        $results = array();
        if (CommonFunctions::rfts('/proc/pci', $bufr)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                if (preg_match('/Bus/', $buf)) {
                    $device = true;
                    continue;
                } 
                if ($device) {
                    list($key, $value) = split(': ', $buf, 2);
                    if (!preg_match('/bridge/i', $key) && !preg_match('/USB/i', $key)) {
                        $results[] = preg_replace('/\([^\)]+\)\.$/', '', trim($value));
                    } 
                    $device = false;
                } 
            } 
        } 
        asort($results);
        return $results;
    }
    
    /**
     * IDE devices
     *
     * @return array
     */
    public function ide()
    {
        $buf = "";
        $results = array();
        $bufd = CommonFunctions::gdc('/proc/ide');
        foreach ($bufd as $file) {
            if (preg_match('/^hd/', $file)) {
                $results[$file] = array();
                // Check if device is CD-ROM (CD-ROM capacity shows as 1024 GB)
                if (CommonFunctions::rfts("/proc/ide/".$file."/media", $buf, 1)) {
                    $results[$file]['media'] = trim($buf);
                    if ($results[$file]['media'] == 'disk') {
                        $results[$file]['media'] = 'Hard Disk';
                    } 
                    if ($results[$file]['media'] == 'cdrom') {
                        $results[$file]['media'] = 'CD-ROM';
                    } 
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
                if (CommonFunctions::rfts("/proc/ide/".$file."/capacity", $buf, 1)) {
                    $results[$file]['capacity'] = trim($buf);
                    if ($results[$file]['media'] == 'CD-ROM') {
                        unset($results[$file]['capacity']);
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
        if (CommonFunctions::rfts('/proc/scsi/scsi', $bufr)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                if (preg_match('/Vendor/', $buf)) {
                    preg_match('/Vendor: (.*) Model: (.*) Rev: (.*)/i', $buf, $dev);
                    list($key, $value) = split(': ', $buf, 2);
                    $dev_str = $value;
                    $get_type = 1;
                    continue;
                } 
                if ($get_type) {
                    preg_match('/Type:\s+(\S+)/i', $buf, $dev_type);
                    $results[$s]['model'] = "$dev[1] $dev[2] ($dev_type[1])";
                    $results[$s]['media'] = "Hard Disk";
                    $s++;
                    $get_type = 0;
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
        $devstring = 0;
        $devnum = -1;
        if (CommonFunctions::rfts('/proc/bus/usb/devices', $bufr)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                if (preg_match('/^T/', $buf)) {
                    $devnum += 1;
                } 
                if (preg_match('/^S/', $buf)) {
                    $devstring = 1;
                } 
                if ($devstring) {
                    list($key, $value) = split(': ', $buf, 2);
                    list($key, $value2) = split('=', $value, 2);
                    $results[$devnum] .= " ".trim($value2);
                    $devstring = 0;
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
        $netstat = "";
        if (CommonFunctions::executeProgram('netstat', '-ni | tail -n +2', $netstat)) {
            $lines = split("\n", $netstat);
            $results = array();
            for ($i = 0, $max = sizeof($lines); $i < $max; $i++) {
                $ar_buf = preg_split("/\s+/", $lines[$i]);
                if (! empty($ar_buf[0]) && ! empty($ar_buf[3])) {
                    $results[$ar_buf[0]] = array();
                    $results[$ar_buf[0]]['rx_bytes'] = $ar_buf[4];
                    $results[$ar_buf[0]]['tx_bytes'] = $ar_buf[6];
                    $results[$ar_buf[0]]['errs'] = $ar_buf[5] + $ar_buf[7];
                    $results[$ar_buf[0]]['drop'] = $ar_buf[8];
                } 
            } 
            return $results;
        } else {
            return array();
        } 
    }
    
    /**
     * Physical memory information and Swap Space information
     *
     * @return array
     */
    public function memory()
    {
        $bufr = "";
        $results['swap'] = array();
        $results['devswap'] = array();
        if (CommonFunctions::rfts('/proc/meminfo', $bufr)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                if (preg_match('/Mem:\s+(.*)$/', $buf, $ar_buf)) {
                    $ar_buf = preg_split('/\s+/', $ar_buf[1], 6);
                    $results['ram']['total'] = $ar_buf[0];
                    $results['ram']['used'] = $ar_buf[1];
                    $results['ram']['free'] = $ar_buf[2];
                    $results['ram']['shared'] = $ar_buf[3];
                    $results['ram']['buffers'] = $ar_buf[4];
                    $results['ram']['cached'] = $ar_buf[5];
                    $results['ram']['percent'] = round(($results['ram']['used'] * 100) / $results['ram']['total']);
                } 
                if (preg_match('/Swap:\s+(.*)$/', $buf, $ar_buf)) {
                    $ar_buf = preg_split('/\s+/', $ar_buf[1], 3);
                    $results['swap']['total'] = $ar_buf[0];
                    $results['swap']['used'] = $ar_buf[1];
                    $results['swap']['free'] = $ar_buf[2];
                    $results['swap']['percent'] = ceil(($ar_buf[1] * 100) / (($ar_buf[0] <= 0) ? 1 : $ar_buf[0]));
                    // Get info on individual swap files
                    $swaps = "";
                    if (CommonFunctions::rfts('/proc/swaps', $swaps)) {
                        $swapdevs = split("\n", $swaps);
                        for ($i = 1, $max = (sizeof($swapdevs) - 1); $i < $max; $i++) {
                            $ar_buf = preg_split('/\s+/', $swapdevs[$i], 6);
                            $results['devswap'][$i - 1] = array();
                            $results['devswap'][$i - 1]['dev'] = $ar_buf[0];
                            $results['devswap'][$i - 1]['total'] = $ar_buf[2] * 1024;
                            $results['devswap'][$i - 1]['used'] = $ar_buf[3] * 1024;
                            $results['devswap'][$i - 1]['free'] = ($results['devswap'][$i - 1]['total'] - $results['devswap'][$i - 1]['used']);
                            $results['devswap'][$i - 1]['percent'] = round(($ar_buf[3] * 100) / $ar_buf[2]);
                        } 
                        break;
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
        $df = "";
        if (CommonFunctions::executeProgram('df', '-kP', $df, PSI_DEBUG)) {
            $mounts = split("\n", $df);
        } else {
            $mounts = array();
        } 
        $fstype = array();
        $s = "";
        if (CommonFunctions::executeProgram('mount', '-v', $s, PSI_DEBUG)) {
            $lines = explode("\n", $s);
        } else {
            $lines = array();
        } 
        $i = 0;
        while (list(, $line) = each($lines)) {
            $a = split(' ', $line);
            $fsdev[$a[0]] = $a[4];
        } 
        for ($i = 1, $j = 0, $max = sizeof($mounts); $i < $max; $i++) {
            $ar_buf = preg_split("/\s+/", $mounts[$i], 6);
            $results[$j] = array();
            $results[$j]['disk'] = $ar_buf[0];
            $results[$j]['size'] = $ar_buf[1] * 1024;
            $results[$j]['used'] = $ar_buf[2] * 1024;
            $results[$j]['free'] = $ar_buf[3] * 1024;
            $results[$j]['percent'] = $ar_buf[4];
            $results[$j]['mount'] = $ar_buf[5];
            ($fstype[$ar_buf[5]]) ? $results[$j]['fstype'] = $fstype[$ar_buf[5]] : $results[$j]['fstype'] = $fsdev[$ar_buf[0]];
            $j++;
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
        $result = 'HP-UX';
        return $result;
    }
    
    /**
     * Distribution Icon
     *
     * @return string
     */
    public function distroicon()
    {
        $result = 'unknown.png';
        return $result;
    } 
}
?>
