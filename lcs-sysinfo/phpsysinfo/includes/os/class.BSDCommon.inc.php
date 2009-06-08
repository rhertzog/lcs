<?php 
/**
 * BSDCommon Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.BSDCommon.inc.php 197 2009-04-30 10:41:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * BSDCommon class
 * get all the required information for BSD Like systems
 * no need to implement in every class the same methods
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
abstract class BSDCommon extends OS {
    /**
     * content of the syslog
     *
     * @var array
     */
    private $_dmesg;
    
    /**
     * regexp1 for cpu information out of the syslog
     *
     * @var string
     */
    private $_CPURegExp1 = "";
    
    /**
     * regexp2 for cpu information out of the syslog
     *
     * @var string
     */
    private $_CPURegExp2 = "";
    
    /**
     * regexp1 for scsi information out of the syslog
     *
     * @var string
     */
    private $_SCSIRegExp1 = "";
    
    /**
     * regexp2 for scsi information out of the syslog
     *
     * @var string
     */
    private $_SCSIRegExp2 = "";
    
    /**
     * regexp1 for pci information out of the syslog
     *
     * @var string
     */
    private $_PCIRegExp1 = "";
    
    /**
     * regexp1 for pci information out of the syslog
     *
     * @var string
     */
    private $_PCIRegExp2 = "";
    
    /**
     * call parent constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * setter for cpuregexp1
     *
     * @param string $value value to set
     *
     * @return void
     */
    protected function setCPURegExp1($value)
    {
        $this->_CPURegExp1 = $value;
    }
    
    /**
     * setter for cpuregexp2
     *
     * @param string $value value to set
     *
     * @return void
     */
    protected function setCPURegExp2($value)
    {
        $this->_CPURegExp2 = $value;
    }
    
    /**
     * setter for scsiregexp1
     *
     * @param string $value value to set
     *
     * @return void
     */
    protected function setSCSIRegExp1($value)
    {
        $this->_SCSIRegExp1 = $value;
    }
    
    /**
     * setter for scsiregexp2
     *
     * @param string $value value to set
     *
     * @return void
     */
    protected function setSCSIRegExp2($value)
    {
        $this->_SCSIRegExp2 = $value;
    }
    
    /**
     * setter for pciregexp1
     *
     * @param string $value value to set
     *
     * @return void
     */
    protected function setPCIRegExp1($value)
    {
        $this->_PCIRegExp1 = $value;
    }
    
    /**
     * setter for pciregexp2
     *
     * @param string $value value to set
     *
     * @return void
     */
    protected function setPCIRegExp2($value)
    {
        $this->_PCIRegExp2 = $value;
    }
    
    /**
     * read /var/run/dmesg.boot, but only if we haven't already
     *
     * @return array
     */
    protected function readdmesg()
    {
        $buf = "";
        if (!$this->_dmesg) {
            if (PHP_OS == "Darwin") {
                $this->_dmesg = array();
            } else {
                if (CommonFunctions::rfts('/var/run/dmesg.boot', $buf)) {
                    $parts = explode("rebooting", $buf);
                    $this->_dmesg = explode("\n", $parts[count($parts) - 1]);
                } else {
                    $this->_dmesg = array();
                } 
            } 
        } 
        return $this->_dmesg;
    }
    
    /**
     * get a value from sysctl command
     *
     * @param string $key key for the value to get
     *
     * @return string
     */
    protected function grabkey($key)
    {
        $buf = "";
        if (CommonFunctions::executeProgram('sysctl', "-n $key", $buf, PSI_DEBUG)) {
            return $buf;
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
            $result = "N.A.";
        } 
        return $result;
    }
    
    /**
     * Canonical Host Name
     *
     @return string
     */
    public function chostname()
    {
        $buf = "";
        if (CommonFunctions::executeProgram('hostname', '', $buf, PSI_DEBUG)) {
            return $buf;
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
     * IP of the Virtual Host Name
     *
     *  @return string
     */
    public function vipaddr()
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
        $s = $this->grabkey('kern.version');
        $a = explode(':', $s);
        return $a[0].$a[1].':'.$a[2];
    }
    
    /**
     * Number of Users
     *
     * @return integer
     */
    public function users()
    {
        $buf = "";
        if (CommonFunctions::executeProgram('who', '| wc -l', $buf, PSI_DEBUG)) {
            return $buf;
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
        $s = $this->grabkey('vm.loadavg');
        $s = ereg_replace('{ ', '', $s);
        $s = ereg_replace(' }', '', $s);
        $results['avg'] = explode(' ', $s);
        if ($bar) {
            if ($fd = $this->grabkey('kern.cp_time')) {
                // Find out the CPU load
                // user + sys = load
                // total = total
                preg_match($this->_CPURegExp2, $fd, $res);
                $load = $res[2] + $res[3] + $res[4]; // cpu.user + cpu.sys
                $total = $res[2] + $res[3] + $res[4] + $res[5]; // cpu.total
                // we need a second value, wait 1 second befor getting (< 1 second no good value will occour)
                sleep(1);
                $fd = $this->grabkey('kern.cp_time');
                preg_match($this->_CPURegExp2, $fd, $res);
                $load2 = $res[2] + $res[3] + $res[4];
                $total2 = $res[2] + $res[3] + $res[4] + $res[5];
                $results['cpupercent'] = (100 * ($load2 - $load)) / ($total2 - $total);
            } 
        } 
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
        $results['model'] = $this->grabkey('hw.model');
        $results['cpus'] = $this->grabkey('hw.ncpu');
        for ($i = 0, $max = count($this->readdmesg()); $i < $max; $i++) {
            $buf = $this->_dmesg[$i];
            if (preg_match("/".$this->_CPURegExp1."/", $buf, $ar_buf)) {
                $results['cpuspeed'] = round($ar_buf[2]);
                break;
            } 
        } 
        return $results;
    }
    
    /**
     * SCSI devices
     * get the scsi device information out of dmesg
     *
     * @return array
     */
    public function scsi()
    {
        $results = array();
        $ar_buf = array();
        for ($i = 0, $max = count($this->readdmesg()); $i < $max; $i++) {
            $buf = $this->_dmesg[$i];
            if (preg_match("/".$this->_SCSIRegExp1."/", $buf, $ar_buf)) {
                $s = $ar_buf[1];
                $results[$s]['model'] = $ar_buf[2];
                $results[$s]['media'] = 'Hard Disk';
            } elseif (preg_match("/".$this->_SCSIRegExp2."/", $buf, $ar_buf)) {
                $s = $ar_buf[1];
                $results[$s]['capacity'] = $ar_buf[2] * 2048 * 1.049;
            } 
        } 
        asort($results);
        return $results;
    }
    
    /**
     * PCI devices
     * get the pci device information out of dmesg
     *
     * @return array
     */
    public function pci()
    {
        $results = array();
        if (!(is_array($results = Parser::lspci()) || is_array($results = Parser::pciconf()))) {
            for ($i = 0, $s = 0; $i < count($this->readdmesg()); $i++) {
                $buf = $this->_dmesg[$i];
                if (preg_match("/".$this->_PCIRegExp1."/", $buf, $ar_buf)) {
                    $results[$s++] = $ar_buf[1].": ".$ar_buf[2];
                } elseif (preg_match("/".$this->_PCIRegExp2."/", $buf, $ar_buf)) {
                    $results[$s++] = $ar_buf[1].": ".$ar_buf[2];
                } 
            } 
            asort($results);
        } 
        return $results;
    }
    
    /**
     * IDE devices
     * get the ide device information out of dmesg
     *
     * @return array
     */
    public function ide()
    {
        $results = array();
        $s = 0;
        for ($i = 0, $max = count($this->readdmesg()); $i < $max; $i++) {
            $buf = $this->_dmesg[$i];
            if (preg_match('/^(ad[0-9]+): (.*)MB <(.*)> (.*) (.*)/', $buf, $ar_buf)) {
                $s = $ar_buf[1];
                $results[$s]['model'] = $ar_buf[3];
                $results[$s]['media'] = 'Hard Disk';
                $results[$s]['capacity'] = $ar_buf[2] * 1024;
            } elseif (preg_match('/^(acd[0-9]+): (.*) <(.*)> (.*)/', $buf, $ar_buf)) {
                $s = $ar_buf[1];
                $results[$s]['model'] = $ar_buf[3];
                $results[$s]['media'] = 'CD-ROM';
            } 
        } 
        asort($results);
        return $results;
    }
    
    /**
     * USB devices
     * place holder function until we add acual usb detection
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
     * Physical memory information and Swap Space information
     *
     * @return array
     */
    public function memory()
    {
        $pstat = "";
        $s = $this->grabkey('hw.physmem');
        if (PHP_OS == 'FreeBSD' || PHP_OS == 'OpenBSD') {
            // vmstat on fbsd 4.4 or greater outputs kbytes not hw.pagesize
            // I should probably add some version checking here, but for now
            // we only support fbsd 4.4
            $pagesize = 1024;
        } else {
            $pagesize = $this->grabkey('hw.pagesize');
        } 
        $results['ram'] = array();
        if (!CommonFunctions::executeProgram('vmstat', '', $pstat, PSI_DEBUG)) {
            $pstat = '';
        } 
        $lines = split("\n", $pstat);
        for ($i = 0, $max = sizeof($lines); $i < $max; $i++) {
            $ar_buf = preg_split("/\s+/", trim($lines[$i]), 19);
            if ($i == 2) {
                if (PHP_OS == 'NetBSD' || PHP_OS == 'DragonFly') {
                    $results['ram']['free'] = $ar_buf[4] * 1024;
                } else {
                    $results['ram']['free'] = $ar_buf[4] * $pagesize;
                } 
            } 
        } 
        $results['ram']['total'] = $s;
        $results['ram']['shared'] = 0;
        $results['ram']['buffers'] = 0;
        $results['ram']['used'] = $results['ram']['total'] - $results['ram']['free'];
        $results['ram']['cached'] = 0;
        $results['ram']['percent'] = round(($results['ram']['used'] * 100) / $results['ram']['total']);
        if (PHP_OS == 'OpenBSD' || PHP_OS == 'NetBSD') {
            if (!CommonFunctions::executeProgram('swapctl', '-l -k', $pstat, PSI_DEBUG)) {
                $pstat = '';
            } 
        } else {
            if (!CommonFunctions::executeProgram('swapinfo', '-k', $pstat, PSI_DEBUG)) {
                $pstat = '';
            } 
        } 
        $lines = split("\n", $pstat);
        $results['swap']['total'] = 0;
        $results['swap']['used'] = 0;
        $results['swap']['free'] = 0;
        $results['swap']['percent'] = 0;
        for ($i = 1, $max = sizeof($lines); $i < $max; $i++) {
            $ar_buf = preg_split("/\s+/", $lines[$i], 6);
            if ($ar_buf[0] != 'Total') {
                $results['swap']['total'] = $results['swap']['total'] + $ar_buf[1] * 1024;
                $results['swap']['used'] = $results['swap']['used'] + $ar_buf[2] * 1024;
                $results['swap']['free'] = $results['swap']['free'] + $ar_buf[3] * 1024;
                $results['devswap'][$i - 1] = array();
                $results['devswap'][$i - 1]['dev'] = $ar_buf[0];
                $results['devswap'][$i - 1]['total'] = $ar_buf[1] * 1024;
                $results['devswap'][$i - 1]['used'] = $ar_buf[2] * 1024;
                $results['devswap'][$i - 1]['free'] = ($results['devswap'][$i - 1]['total'] - $results['devswap'][$i - 1]['used']);
                if ($ar_buf[2] > 0) {
                    $results['devswap'][$i - 1]['percent'] = round(($ar_buf[2] * 100) / $ar_buf[1]);
                } else {
                    $results['devswap'][$i - 1]['percent'] = 0;
                } 
            } 
        } 
        if (($i - 1) > 0) {
            if (($results['swap']['total'] > 0)) {
                $results['swap']['percent'] = ceil(($results['swap']['used'] * 100) / $results['swap']['total']);
                
            } else {
                $results['swap']['percent'] = 0;
            } 
        } 
        if (is_callable(array($this, 'memoryadditional'))) {
            $this->memoryadditional($results);
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
        return Parser::df();
    }
    
    /**
     * Distribution
     *
     * @return string
     */
    public function distro()
    {
        $result = "";
        if (CommonFunctions::executeProgram('uname', '-s', $result, PSI_DEBUG)) {
            return $result;
        } else {
            return 'N.A.';
        } 
    } 
}
?>
