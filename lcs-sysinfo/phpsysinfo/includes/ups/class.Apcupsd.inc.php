<?php 
/**
 * Apcupsd class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_UPS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Apcupsd.inc.php 220 2009-05-25 09:04:20Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting ups information from apcupsd program
 *
 * @category  PHP
 * @package   PSI_UPS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @author    Artem Volk <artvolk@mail.ru>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Apcupsd implements PSI_Interface_UPS
{
    /**
     * internal storage for all gathered data
     *
     * @var array
     */
    private $_output = array();
    
    /**
     * get all information from all configured ups in config.php and store output in internal array
     */
    public function __construct()
    {
        $upses = explode(',', PSI_UPS_APCUPSD_LIST);
        foreach ($upses as $ups) {
            $temp = '';
            CommonFunctions::executeProgram('apcaccess', 'status '.trim($ups), $temp);
            if (isset($temp) && ! empty($temp)) {
                $this->_output[] = $temp;
            }
        }
    }
    
    /**
     * parse the input and store data in resultset for xml generation
     *
     * @return array
     */
    public function info()
    {
        if (isset($this->_output) && count($this->_output) > 0) {
            $results = array();
            for ($i = 0, $cnt_output = count($this->_output); $i < $cnt_output; $i++) {
                // General info
                if (preg_match('/^UPSNAME\s*:\s*(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['name'] = trim($data[1]);
                } else {
                    $results[$i]['name'] = '';
                }
                if (preg_match('/^MODEL\s*:\s*(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['model'] = trim($data[1]);
                } else {
                    $results[$i]['model'] = '';
                }
                if (preg_match('/^UPSMODE\s*:\s*(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['mode'] = trim($data[1]);
                } else {
                    $results[$i]['mode'] = '';
                }
                if (preg_match('/^STARTTIME\s*:\s*(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['start_time'] = trim($data[1]);
                } else {
                    $results[$i]['start_time'] = '';
                }
                if (preg_match('/^STATUS\s*:\s*(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['status'] = trim($data[1]);
                } else {
                    $results[$i]['status'] = '';
                }
                if (preg_match('/^ITEMP\s*:\s*(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['temperature'] = trim($data[1]);
                } else {
                    $results[$i]['temperature'] = '';
                }
                // Outages
                if (preg_match('/^NUMXFERS\s*:\s*(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['outages_count'] = trim($data[1]);
                } else {
                    $results[$i]['outages_count'] = '';
                }
                if (preg_match('/^LASTXFER\s*:\s*(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['last_outage'] = trim($data[1]);
                } else {
                    $results[$i]['last_outage'] = '';
                }
                if (preg_match('/^XOFFBATT\s*:\s*(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['last_outage_finish'] = trim($data[1]);
                } else {
                    $results[$i]['last_outage_finish'] = '';
                }
                // Line
                if (preg_match('/^LINEV\s*:\s*(\d*\.\d*)(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['line_voltage'] = trim($data[1]);
                } else {
                    $results[$i]['line_voltage'] = '';
                }
                if (preg_match('/^LOADPCT\s*:\s*(\d*\.\d*)(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['load_percent'] = trim($data[1]);
                } else {
                    $results[$i]['load_percent'] = '';
                }
                // Battery
                if (preg_match('/^BATTV\s*:\s*(\d*\.\d*)(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['battery_voltage'] = trim($data[1]);
                } else {
                    $results[$i]['battery_voltage'] = '';
                }
                if (preg_match('/^BCHARGE\s*:\s*(\d*\.\d*)(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['battery_charge_percent'] = trim($data[1]);
                } else {
                    $results[$i]['battery_charge_percent'] = '';
                }
                if (preg_match('/^TIMELEFT\s*:\s*(\d*\.\d*)(.*)$/m', $this->_output[$i], $data)) {
                    $results[$i]['time_left_minutes'] = trim($data[1]);
                } else {
                    $results[$i]['time_left_minutes'] = '';
                }
            }
            return $results;
        }
    }
}
?>
