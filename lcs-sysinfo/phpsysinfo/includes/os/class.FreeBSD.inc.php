<?php 
/**
 * FreeBSD System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.FreeBSD.inc.php 197 2009-04-30 10:41:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * FreeBSD sysinfo class
 * get all the required information from FreeBSD system
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class FreeBSD extends BSDCommon {
    /**
     * define the regexp for log parser
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCPURegExp1("CPU: (.*) \((.*)-MHz (.*)\)");
        $this->setCPURegExp2("/(.*) ([0-9]+) ([0-9]+) ([0-9]+) ([0-9]+)/");
        $this->setSCSIRegExp1("^(.*): <(.*)> .*SCSI.*device");
        $this->setSCSIRegExp2("^(da[0-9]): (.*)MB ");
        $this->setPCIRegExp1("/(.*): <(.*)>(.*) pci[0-9]$/");
        $this->setPCIRegExp2("/(.*): <(.*)>.* at [.0-9]+ irq/");
    }
    
    /**
     * UpTime
     * time the system is running
     *
     * @return integer
     */
    public function uptime()
    {
        $s = explode(' ', $this->grabkey('kern.boottime'));
        $a = ereg_replace('{ ', '', $s[3]);
        $sys_ticks = time() - $a;
        return $sys_ticks;
    }
    
    /**
     * get network information
     *
     * @return array
     */
    public function network()
    {
        $netstat = "";
        if (!CommonFunctions::executeProgram('netstat', '-nibd | grep Link', $netstat, PSI_DEBUG)) {
            $netstat = '';
        } 
        $lines = split("\n", $netstat);
        $results = array();
        for ($i = 0, $max = sizeof($lines); $i < $max; $i++) {
            $ar_buf = preg_split("/\s+/", $lines[$i]);
            if (! empty($ar_buf[0])) {
                $results[$ar_buf[0]] = array();
                if (strlen($ar_buf[3]) < 15) {
                    $results[$ar_buf[0]]['rx_bytes'] = $ar_buf[5];
                    $results[$ar_buf[0]]['tx_bytes'] = $ar_buf[8];
                    $results[$ar_buf[0]]['errs'] = $ar_buf[4] + $ar_buf[7];
                    $results[$ar_buf[0]]['drop'] = $ar_buf[10];
                } else {
                    $results[$ar_buf[0]]['rx_bytes'] = $ar_buf[6];
                    $results[$ar_buf[0]]['tx_bytes'] = $ar_buf[9];
                    $results[$ar_buf[0]]['errs'] = $ar_buf[5] + $ar_buf[8];
                    $results[$ar_buf[0]]['drop'] = $ar_buf[11];
                } 
            } 
        } 
        return $results;
    }
    
    /**
     * get icon name
     *
     * @return string
     */
    public function distroicon()
    {
        $result = 'FreeBSD.png';
        return $result;
    }
    
    /**
     * extend the memory information with additional values
     *
     * @param array &$results memory array to which the additional information are added
     *
     * @return array memory array with the additional information
     */
    protected function memoryadditional(&$results)
    {
        $pagesize = $this->grabkey("hw.pagesize");
        $results['ram']['cached'] = $this->grabkey("vm.stats.vm.v_cache_count") * $pagesize;
        $results['ram']['cached_percent'] = round($results['ram']['cached'] * 100 / $results['ram']['total']);
        $results['ram']['app'] = $this->grabkey("vm.stats.vm.v_active_count") * $pagesize;
        $results['ram']['app_percent'] = round($results['ram']['app'] * 100 / $results['ram']['total']);
        $results['ram']['buffers'] = $results['ram']['used'] - $results['ram']['app'] - $results['ram']['cached'];
        $results['ram']['buffers_percent'] = round($results['ram']['buffers'] * 100 / $results['ram']['total']);
    } 
}
?>
