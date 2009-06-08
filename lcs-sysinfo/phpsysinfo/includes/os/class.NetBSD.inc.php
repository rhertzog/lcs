<?php 
/**
 * NetBSD System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.NetBSD.inc.php 197 2009-04-30 10:41:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * NetBSD sysinfo class
 * get all the required information from NetBSD systems
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class NetBSD extends BSDCommon {
    /**
     * define the regexp for log parser
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCPURegExp1("^cpu(.*)\, (.*) MHz");
        $this->setCPURegExp2("/user = (.*), nice = (.*), sys = (.*), intr = (.*), idle = (.*)/");
        $this->setSCSIRegExp1("^(.*) at scsibus.*: <(.*)> .*");
        $this->setSCSIRegExp2("^(da[0-9]): (.*)MB ");
        $this->setPCIRegExp1("/(.*) at pci[0-9] dev [0-9]* function [0-9]*: (.*)$/");
        $this->setPCIRegExp2("/\"(.*)\" (.*).* at [.0-9]+ irq/");
    }
    
    /**
     * UpTime
     * time the system is running
     *
     * @return integer
     */
    public function uptime()
    {
        $a = $this->grabkey('kern.boottime');
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
        $netstat_b = "";
        $netstat_n = "";
        CommonFunctions::executeProgram('netstat', '-nbdi | cut -c1-25,44- | grep "^[a-z]*[0-9][ \t].*Link"', $netstat_b);
        CommonFunctions::executeProgram('netstat', '-ndi | cut -c1-25,44- | grep "^[a-z]*[0-9][ \t].*Link"', $netstat_n);
        $lines_b = split("\n", $netstat_b);
        $lines_n = split("\n", $netstat_n);
        $results = array();
        for ($i = 0, $max = sizeof($lines_b); $i < $max; $i++) {
            $ar_buf_b = preg_split("/\s+/", $lines_b[$i]);
            $ar_buf_n = preg_split("/\s+/", $lines_n[$i]);
            if (! empty($ar_buf_b[0]) && ! empty($ar_buf_n[3])) {
                $results[$ar_buf_b[0]] = array();
                $results[$ar_buf_b[0]]['rx_bytes'] = $ar_buf_b[3];
                $results[$ar_buf_b[0]]['tx_bytes'] = $ar_buf_b[4];
                $results[$ar_buf_b[0]]['errs'] = $ar_buf_n[4] + $ar_buf_n[6];
                $results[$ar_buf_b[0]]['drop'] = $ar_buf_n[8];
            } 
        } 
        return $results;
    }
    
    /**
     * IDE information
     *
     * @return array
     */
    public function ide()
    {
        $results = array();
        $s = 0;
        $dmesg = $this->readdmesg();
        for ($i = 0, $max = count($dmesg); $i < $max; $i++) {
            $buf = $dmesg[$i];
            if (preg_match('/^(.*) at (pciide|wdc|atabus|atapibus)[0-9] (.*): <(.*)>/', $buf, $ar_buf)) {
                $s = $ar_buf[1];
                $results[$s]['model'] = $ar_buf[4];
                $results[$s]['media'] = 'Hard Disk';
                // now loop again and find the capacity
                for ($j = 0, $max1 = count($dmesg); $j < $max1; $j++) {
                    $buf_n = $dmesg[$j];
                    if (preg_match("/^($s): (.*), (.*), (.*)MB, .*$/", $buf_n, $ar_buf_n)) {
                        $results[$s]['capacity'] = $ar_buf_n[4] * 2048 * 1.049;
                    } elseif (preg_match("/^($s): (.*) MB, (.*), (.*), .*$/", $buf_n, $ar_buf_n)) {
                        $results[$s]['capacity'] = $ar_buf_n[2] * 2048;
                    } 
                } 
            } 
        } 
        asort($results);
        return $results;
    }
    
    /**
     * get icon name
     *
     * @return string
     */
    public function distroicon()
    {
        $result = 'NetBSD.png';
        return $result;
    } 
}
?>
