<?php
/**
 * ipmi sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.IPMI.inc.php 185 2009-04-14 07:40:43Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from ipmitool
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class IPMI implements PSI_Interface_Sensor
{
    /**
     * content to parse
     *
     * @var array
     */
    private $_lines = array ();

    /**
     * object for error handling
     *
     * @var Error
     */
    private $_error;

    /**
     * fill the private content var through tcp or file access
     */
    public function __construct()
    {
        $this->_error = Error::Singleton();
        switch(strtolower(PSI_SENSOR_ACCESS)) {
        case 'command':
            $lines = "";
            CommonFunctions::executeProgram('ipmitool', 'sensor', $lines);
            $this->_lines = explode("\n", $lines);
            break;
        default:
            $this->_error->addConfigError('__construct()', 'PSI_SENSOR_ACCESS');
            break;
        }
    }

    /**
     * get temperature information
     *
     * @return array temperatures in array with lable
     */
    public function temperature()
    {
        $result = array ();
        $i = 0;
        foreach ($this->_lines as $line) {
            $buffer = preg_split("/[ ]+\|[ ]+/", $line);
            if ($buffer[2] == "degrees C" && $buffer[5] != "na") {
                $result[$i]['label'] = $buffer[0];
                $result[$i]['value'] = $buffer[1];
                $result[$i]['limit'] = $buffer[8];
                $i++;
            }
        }
        return $result;
    }

    /**
     * get fan information
     *
     * @return array fans in array with lable
     */
    public function fans()
    {
        $result = array ();
        return $result;
    }

    /**
     * get voltage information
     *
     * @return array voltage in array with lable
     */
    public function voltage()
    {
        $result = array ();
        $i = 0;
        foreach ($this->_lines as $line) {
            $buffer = preg_split("/[ ]+\|[ ]+/", $line);
            if ($buffer[2] == "Volts" && $buffer[5] != "na") {
                $result[$i]['label'] = $buffer[0];
                $result[$i]['value'] = $buffer[1];
                $result[$i]['min'] = $buffer[5];
                $result[$i]['max'] = $buffer[8];
                $i++;
            }
        }
        return $result;
    }
}
?>
