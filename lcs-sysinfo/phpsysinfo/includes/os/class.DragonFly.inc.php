<?php 
/**
 * DragonFly System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.DragonFly.inc.php 197 2009-04-30 10:41:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * DragonFly sysinfo class
 * get all the required information from DragonFly system
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class DragonFly extends BSDCommon {
    /**
     * define the regexp for log parser
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCPURegExp1("^cpu(.*)\, (.*) MHz");
        $this->setCPURegExp2("^(.*) at scsibus.*: <(.*)> .*");
        $this->setSCSIRegExp2("^(da[0-9]): (.*)MB ");
        $this->setPCIRegExp1("/(.*): <(.*)>(.*) (pci|legacypci)[0-9]$/");
        $this->setPCIRegExp2("/(.*): <(.*)>.* at [0-9\.]+$/");
    }
    
    /**
     * UpTime
     * time the system is running
     *
     * @return integer
     */
    public function uptime()
    {
        $a = $this->grab_key('kern.boottime');
        preg_match("/sec = ([0-9]+)/", $a, $buf);
        $sys_ticks = time() - $buf[1];
        return $sys_ticks;
    }
    
    /**
     * load bar not implemented yet, so call everytime with false
     *
     * @param boolean $bar control if a bar is displayed
     *
     * @return array
     */
    public function loadavg($bar = false)
    {
        return parent::loadavg(false);
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
                $results[$ar_buf_b[0]]['rx_bytes'] = $ar_buf_b[5];
                $results[$ar_buf_b[0]]['tx_bytes'] = $ar_buf_b[8];
                $results[$ar_buf_b[0]]['errs'] = $ar_buf_n[4] + $ar_buf_n[6];
                $results[$ar_buf_b[0]]['drop'] = $ar_buf_n[8];
            } 
        } 
        return $results;
    }
    
    /**
     * get the ide information
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
            if (preg_match('/^(.*): (.*) <(.*)> at (ata[0-9]\-(.*)) (.*)/', $buf, $ar_buf)) {
                $results[$ar_buf[1]]['model'] = $ar_buf[3];
                if (preg_match("/^acd[0-9](.*)/", $ar_buf[1])) {
                    $results[$ar_buf[1]]['media'] = $ar_buf[2];
                } else {
                    $results[$ar_buf[1]]['media'] = 'Hard Disk';
                    $results[$ar_buf[1]]['capacity'] = $ar_buf[2] * 1024;
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
        $result = 'DragonFly.png';
        return ($result);
    } 
}
?>
