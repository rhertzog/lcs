<?php 
/**
 * Darwin System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Darwin.inc.php 197 2009-04-30 10:41:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * Darwin sysinfo class
 * get all the required information from Darwin system
 * information may be incomplete
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Darwin extends BSDCommon {
    /**
     * define the regexp for log parser
     */
    public function __construct()
    {
        parent::__construct();
        $this->error->addWarning("The Darwin version of phpSysInfo is work in progress, some things currently don't work!");
        $this->setCPURegExp1("CPU: (.*) \((.*)-MHz (.*)\)");
        $this->setCPURegExp2("/(.*) ([0-9]+) ([0-9]+) ([0-9]+) ([0-9]+)/");
        $this->setSCSIRegExp1("^(.*): <(.*)> .*SCSI.*device");
    }
    
    /**
     * get a value from sysctl command
     *
     * @param string $key key of the value to get
     *
     * @return string
     */
    protected function grabkey($key)
    {
        $s = "";
        if (CommonFunctions::executeProgram('sysctl', $key, $s, PSI_DEBUG)) {
            $s = ereg_replace($key.': ', '', $s);
            $s = ereg_replace($key.' = ', '', $s);
            return $s;
        } else {
            return '';
        } 
    }
    
    /**
     * get a value from ioreg command
     *
     * @param string $key key of the value to get
     *
     * @return string
     */
    private function _grabioreg($key)
    {
        $s = "";
        if (CommonFunctions::executeProgram('ioreg', '-cls "'.$key.'" | grep "'.$key.'"', $s, PSI_DEBUG)) {
            $s = ereg_replace('\|', '', $s);
            $s = ereg_replace('\+\-\o', '', $s);
            $s = ereg_replace('[ ]+', '', $s);
            $s = ereg_replace('<[^>]+>', '', $s);
            return $s;
        } else {
            return '';
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
        $a = "";
        if (CommonFunctions::executeProgram('sysctl', '-n kern.boottime', $a, PSI_DEBUG)) {
            $sys_ticks = time() - $a;
            return $sys_ticks;
        } else {
            return 0;
        } 
    }
    
    /**
     * get CPU information
     *
     * @return array
     */
    public function cpuinfo()
    {
        $results = array();
        $buf = "";
        $buffer = array();
        if (!CommonFunctions::executeProgram('hostinfo', '| grep "Processor type"', $buf, PSI_DEBUG)) {
            $buf = 'N.A.';
        } 
        $results['model'] = ereg_replace('Processor type: ', '', $buf);
        $results['cpus'] = $this->grabkey('hw.ncpu');
        $results['cpuspeed'] = round($this->grabkey('hw.cpufrequency') / 1000000);
        $results['busspeed'] = round($this->grabkey('hw.busfrequency') / 1000000);
        $results['cache'] = round($this->grabkey('hw.l2cachesize'));
        
        if (CommonFunctions::rfts(APP_ROOT.'/data/ModelTranslation.txt', $buffer)) {
            $buffer = split("\n", $buffer);
            foreach ($buffer as $line) {
                $ar_buf = split(":", $line, 2);
                if ($modelRAW === trim($ar_buf[0])) {
                    $results['model'] = trim($ar_buf[1]);
                } 
            } 
        } 
        return $results;
    }
    
    /**
     * get the pci device information out of ioreg
     *
     * @return array
     */
    public function pci()
    {
        $results = array();
        $s = $this->_grabioreg('IOPCIDevice');
        $lines = split("\n", $s);
        for ($i = 0, $max = sizeof($lines); $i < $max; $i++) {
            $ar_buf = preg_split("/\s+/", $lines[$i], 19);
            $results[$i] = $ar_buf[0];
        } 
        asort($results);
        return array_values(array_unique($results));
    }
    
    /**
     * get the ide device information out of ioreg
     *
     * @return array
     */
    public function ide()
    {
        $results = array();
        $s = $this->_grabioreg('IOATABlockStorageDevice');
        $lines = split("\n", $s);
        $j = 0;
        for ($i = 0, $max = sizeof($lines); $i < $max; $i++) {
            $ar_buf = preg_split("/\/\//", $lines[$i], 19);
            if (isset($ar_buf[1]) && $ar_buf[1] == 'class IOMedia' && preg_match('/Media/', $ar_buf[0])) {
                $results[$j++]['model'] = $ar_buf[0];
            } 
        } 
        asort($results);
        return array_values(array_unique($results));
    }
    
    /**
     * get memory and swap information
     *
     * @return array
     */
    public function memory()
    {
        $pstat = "";
        $s = $this->grabkey('hw.memsize');
        $results['ram'] = array();
        $results['swap'] = array();
        $results['devswap'] = array();
        if (!CommonFunctions::executeProgram('vm_stat', '', $pstat, PSI_DEBUG)) {
            $pstat = '';
        } 
        $lines = split("\n", $pstat);
        for ($i = 0, $max = sizeof($lines); $i < $max; $i++) {
            $ar_buf = preg_split("/\s+/", $lines[$i], 19);
            if ($i == 1) {
                // calculate free memory from page sizes (each page = 4MB)
                $results['ram']['free'] = $ar_buf[2] * 4 * 1024;
            } 
        } 
        $results['ram']['total'] = $s;
        $results['ram']['shared'] = 0;
        $results['ram']['buffers'] = 0;
        $results['ram']['used'] = $results['ram']['total'] - $results['ram']['free'];
        $results['ram']['cached'] = 0;
        $results['ram']['percent'] = round(($results['ram']['used'] * 100) / $results['ram']['total']);
        
        
        $swapBuff = "";
        if (CommonFunctions::executeProgram('sysctl', 'vm.swapusage | colrm 1 22', $swapBuff, PSI_DEBUG)) {
            $swap1 = split('M', $swapBuff);
            $swap2 = split('=', $swap1[1]);
            $swap3 = split('=', $swap1[2]);
            
            $results['swap']['total'] = $swap1[0] * 1024 * 1024;
            $results['swap']['used'] = $swap2[1] * 1024 * 1024;
            $results['swap']['free'] = $swap3[1] * 1024 * 1024;
            if ($results['swap']['total'] <= 0) {
                $results['swap']['percent'] = 0;
            } else {
                $results['swap']['percent'] = round(($results['swap']['used'] * 100) / $results['swap']['total']);
            } 
        } 
        return $results;
    }
    
    /**
     * get network information
     *
     * @return array
     */
    public function network()
    {
        $netstat = "";
        if (CommonFunctions::executeProgram('netstat', '-nbdi | cut -c1-24,42- | grep Link', $netstat, PSI_DEBUG)) {
            $lines = split("\n", $netstat);
            $results = array();
            for ($i = 0, $max = sizeof($lines); $i < $max; $i++) {
                $ar_buf = preg_split("/\s+/", $lines[$i], 10);
                if (! empty($ar_buf[0])) {
                    $results[$ar_buf[0]] = array();
                    $results[$ar_buf[0]]['rx_bytes'] = $ar_buf[5];
                    $results[$ar_buf[0]]['tx_bytes'] = $ar_buf[8];
                    $results[$ar_buf[0]]['errs'] = $ar_buf[4] + $ar_buf[7];
                    $results[$ar_buf[0]]['drop'] = isset($ar_buf[10]) ? $ar_buf[10] : 0;
                } 
            } 
            return $results;
        } else {
            return array();
        } 
    }
    
    /**
     * get icon name
     *
     * @return string
     */
    public function distroicon()
    {
        $result = 'Darwin.png';
        return $result;
    }
    
    /**
     * get distribution name
     *
     * @return string
     */
    public function distro()
    {
        if (!CommonFunctions::executeProgram('system_profiler', 'SPSoftwareDataType', $buffer, PSI_DEBUG)) {
            parent::distro();
        } else {
            $arrBuff = split("\n", $buffer);
            foreach ($arrBuff as $line) {
                $arrLine = split(':', $line);
                if (trim($arrLine[0]) === "System Version") {
                    $result = trim($arrLine[1]);
                    break;
                } 
            } 
            return $result;
        } 
    } 
}
?>
