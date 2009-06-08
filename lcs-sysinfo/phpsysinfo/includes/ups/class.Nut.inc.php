<?php 
/**
 * Nut class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_UPS
 * @author    Artem Volk <artvolk@mail.ru>
 * @author    Anders Häggström <hagge@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Nut.inc.php 220 2009-05-25 09:04:20Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting ups information from upsc program
 *
 * @category  PHP
 * @package   PSI_UPS
 * @author    Artem Volk <artvolk@mail.ru>
 * @author    Anders Häggström <hagge@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Nut implements PSI_Interface_UPS
{
    /**
     * internal storage for all gathered data
     *
     * @var array
     */
    private $_output = array();
    
    /**
     * get all information from all configured ups and store output in internal array
     */
    public function __construct()
    {
        $output = '';
        CommonFunctions::executeProgram('upsc', '-l', $output);
        $ups_names = explode("\n", $output);
        
        $temp = '';
        foreach ($ups_names as $value) {
            CommonFunctions::executeProgram('upsc', $value, $temp);
            $this->_output[$value] = $temp;
        }
    }
    
    /**
     * check if a specific value is set in an array
     *
     * @param object $hash array in which a specific value should be found
     * @param object $key  key that is looked for in the array
     *
     * @return array
     */
    private function _checkIsSet($hash, $key)
    {
        return isset($hash[$key]) ? $hash[$key] : '';
    }
    
    /**
     * parse the input and store data in resultset for xml generation
     *
     * @return array
     */
    public function info()
    {
        if (isset($this->_output) && ! empty($this->_output)) {
            $results = array();
            foreach ($this->_output as $name=>$value) {
                $temp = explode("\n", $value);
                $ups_data = array();
                foreach ($temp as $value) {
                    $line = explode(': ', $value);
                    $ups_data[$line[0]] = isset($line[1]) ? trim($line[1]) : '';
                }
                
                //General
                $result_ups['name'] = $name;
                $result_ups['model'] = $this->_checkIsSet($ups_data, 'ups.model');
                $result_ups['mode'] = $this->_checkIsSet($ups_data, 'driver.name');
                $result_ups['start_time'] = '';
                $result_ups['status'] = $this->_checkIsSet($ups_data, 'ups.status');
                $result_ups['power_nominal'] = $this->_checkIsSet($ups_data, 'ups.power.nominal');
                $result_ups['beeper_status'] = $this->_checkIsSet($ups_data, 'ups.beeper.status');
                $result_ups['temperature'] = '';
                
                //Outages
                $result_ups['outages'] = '';
                $result_ups['outages_count'] = '';
                $result_ups['last_outage'] = '';
                $result_ups['last_outage_finish'] = '';
                
                //Line
                $result_ups['linein_voltage'] = $this->_checkIsSet($ups_data, 'input.voltage');
                $result_ups['linein_voltage_nominal'] = $this->_checkIsSet($ups_data, 'input.voltage.nominal');
                $result_ups['linein_voltage_minimum'] = $this->_checkIsSet($ups_data, 'input.voltage.minimum');
                $result_ups['linein_voltage_maximum'] = $this->_checkIsSet($ups_data, 'input.voltage.maximum');
                $result_ups['linein_frequency'] = $this->_checkIsSet($ups_data, 'input.frequency');
                $result_ups['lineout_voltage'] = $this->_checkIsSet($ups_data, 'output.voltage');
                $result_ups['lineout_frequency'] = $this->_checkIsSet($ups_data, 'output.frequency');
                $result_ups['load_percent'] = $this->_checkIsSet($ups_data, 'ups.load');
                
                //Battery
                $result_ups['battery_voltage'] = $this->_checkIsSet($ups_data, 'battery.voltage');
                $result_ups['battery_charge_percent'] = $this->_checkIsSet($ups_data, 'battery.charge');
                
                $result_ups['time_left_minutes'] = '';
                
                $results[] = $result_ups;
            }
        }
        return $results;
    }
}
?>
