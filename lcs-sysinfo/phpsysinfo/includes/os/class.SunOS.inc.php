<?php 
/**
 * SunOS System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.SunOS.inc.php 197 2009-04-30 10:41:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * SunOS sysinfo class
 * get all the required information from SunOS systems
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class SunOS extends OS {
    /**
     * add warning to errors
     */
    public function __construct()
    {
        $this->error->addError("WARN", "The SunOS version of phpSysInfo is work in progress, some things currently don't work");
    }
    
    /**
     * Extract kernel values via kstat() interface
     *
     * @param string $key key for kstat programm
     *
     * @return string
     */
    private function _kstat($key)
    {
        $m = "";
        if (CommonFunctions::executeProgram('kstat', "-p d $key", $m, PSI_DEBUG)) {
            list($key, $value) = split("\t", trim($m), 2);
            return $value;
        } else {
            return '';
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
        if (CommonFunctions::executeProgram('uname', '-n', $result, PSI_DEBUG)) {
            $ip = gethostbyname($result);
            if ($ip != $result) {
                $result = gethostbyaddr($ip);
            } else {
                $result = 'Unknown';
            } 
        } else {
            $result = 'N.A.';
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
        $os = "";
        $version = "";
        if (!CommonFunctions::executeProgram('uname', '-s', $os, PSI_DEBUG)) {
            $os = 'N.A.';
        } 
        if (!CommonFunctions::executeProgram('uname', '-r', $version, PSI_DEBUG)) {
            $version = 'N.A.';
        } 
        return $os.' '.$version;
    }
    
    /**
     * UpTime
     * time the system is running
     *
     * @return integer
     */
    public function uptime()
    {
        $result = time() - $this->_kstat('unix:0:system_misc:boot_time');
        return $result;
    }
    
    /**
     * Number of Users
     *
     * @return integer
     */
    public function users()
    {
        $buf = "";
        if (CommonFunctions::executeProgram('who', '-q', $buf, PSI_DEBUG)) {
            $who = split('=', $buf);
            $result = $who[1];
            return $result;
        } else {
            return 'N.A.';
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
        $load1 = $this->_kstat('unix:0:system_misc:avenrun_1min');
        $load5 = $this->_kstat('unix:0:system_misc:avenrun_5min');
        $load15 = $this->_kstat('unix:0:system_misc:avenrun_15min');
        $results['avg'] = array(round($load1 / 256, 2), round($load5 / 256, 2), round($load15 / 256, 2));
        return $results;
    }
    
    /**
     * CPU information
     *
     * @return array
     */
    public function cpuinfo()
    {
        $results = array();
        $ar_buf = array();
        $buf = "";
        if (!CommonFunctions::executeProgram('uname', '-i', $buf, PSI_DEBUG)) {
            $buf = 'N.A.';
        } 
        $results['model'] = $buf;
        $results['cpuspeed'] = $this->_kstat('cpu_info:0:cpu_info0:clock_MHz');
        $results['cache'] = $this->_kstat('cpu_info:0:cpu_info0:cpu_type') * 1024;
        $results['cpus'] = $this->_kstat('unix:0:system_misc:ncpus');
        return $results;
    }
    
    /**
     * PCI information
     *
     * @return array
     */
    public function pci()
    {
        // FIXME
        $results = array();
        return $results;
    }
    
    /**
     * IDE information
     *
     * @return array
     */
    public function ide()
    {
        // FIXME
        $results = array();
        return $results;
    }
    
    /**
     * SCSI information
     *
     * @return array
     */
    public function scsi()
    {
        // FIXME
        $results = array();
        return $results;
    }
    
    /**
     * USB information
     *
     * @return array
     */
    public function usb()
    {
        // FIXME
        $results = array();
        return $results;
    }
    
    /**
     * Network devices
     *
     * @return array
     */
    public function network()
    {
        $results = array();
        $netstat = "";
        if (!CommonFunctions::executeProgram('netstat', '-ni | awk \'(NF ==10){print;}\'', $netstat, PSI_DEBUG)) {
            $netstat = '';
        } 
        $lines = split("\n", $netstat);
        $results = array();
        for ($i = 0, $max = sizeof($lines); $i < $max; $i++) {
            $ar_buf = preg_split("/\s+/", $lines[$i]);
            if ((! empty($ar_buf[0])) && ($ar_buf[0] != 'Name')) {
                $results[$ar_buf[0]] = array();
                $results[$ar_buf[0]]['rx_bytes'] = 0;
                $results[$ar_buf[0]]['tx_bytes'] = 0;
                $results[$ar_buf[0]]['errs'] = $ar_buf[5] + $ar_buf[7];
                $results[$ar_buf[0]]['drop'] = 0;
                preg_match('/^(\D+)(\d+)$/', $ar_buf[0], $intf);
                $prefix = $intf[1].':'.$intf[2].':'.$intf[1].$intf[2].':';
                $cnt = $this->_kstat($prefix.'drop');
                if ($cnt > 0) {
                    $results[$ar_buf[0]]['drop'] = $cnt;
                } 
                $cnt = $this->_kstat($prefix.'obytes64');
                if ($cnt > 0) {
                    $results[$ar_buf[0]]['tx_bytes'] = $cnt;
                } 
                $cnt = $this->_kstat($prefix.'rbytes64');
                if ($cnt > 0) {
                    $results[$ar_buf[0]]['rx_bytes'] = $cnt;
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
        $results['devswap'] = array();
        $results['ram'] = array();
        $pagesize = $this->_kstat('unix:0:seg_cache:slab_size');
        $results['ram']['total'] = $this->_kstat('unix:0:system_pages:pagestotal') * $pagesize;
        $results['ram']['used'] = $this->_kstat('unix:0:system_pages:pageslocked') * $pagesize;
        $results['ram']['free'] = $this->_kstat('unix:0:system_pages:pagesfree') * $pagesize;
        $results['ram']['shared'] = 0;
        $results['ram']['buffers'] = 0;
        $results['ram']['cached'] = 0;
        $results['ram']['percent'] = round(($results['ram']['used'] * 100) / $results['ram']['total']);
        $results['swap'] = array();
        $results['swap']['total'] = $this->_kstat('unix:0:vminfo:swap_avail') / 1024;
        $results['swap']['used'] = $this->_kstat('unix:0:vminfo:swap_alloc') / 1024;
        $results['swap']['free'] = $this->_kstat('unix:0:vminfo:swap_free') / 1024;
        $results['swap']['percent'] = ceil(($results['swap']['used'] * 100) / (($results['swap']['total'] <= 0) ? 1 : $results['swap']['total']));
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
        $dftypes = "";
        if (!CommonFunctions::executeProgram('df', '-k', $df, PSI_DEBUG)) {
            $df = '';
        } 
        $mounts = split("\n", $df);
        if (!CommonFunctions::executeProgram('df', '-n', $dftypes, PSI_DEBUG)) {
            $dftypes = '';
        } 
        $mounttypes = split("\n", $dftypes);
        for ($i = 1, $j = 0, $max = sizeof($mounts); $i < $max; $i++) {
            $ar_buf = preg_split('/\s+/', $mounts[$i], 6);
            $ty_buf = split(':', $mounttypes[$i - 1], 2);
            $results[$j] = array();
            $results[$j]['disk'] = $ar_buf[0];
            $results[$j]['size'] = $ar_buf[1] * 1024;
            $results[$j]['used'] = $ar_buf[2] * 1024;
            $results[$j]['free'] = $ar_buf[3] * 1024;
            $results[$j]['percent'] = round(($results[$j]['used'] * 100) / $results[$j]['size']);
            $results[$j]['mount'] = $ar_buf[5];
            $results[$j]['fstype'] = $ty_buf[1];
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
        $result = 'SunOS';
        return $result;
    }
    
    /**
     * Distribution Icon
     *
     * @return string
     */
    public function distroicon()
    {
        $result = 'SunOS.png';
        return $result;
    } 
}
?>
