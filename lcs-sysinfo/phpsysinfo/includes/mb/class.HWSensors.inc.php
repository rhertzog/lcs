<?php
/**
 * hwsensors sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.HWSensors.inc.php 185 2009-04-14 07:40:43Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from hwsensors
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class HWSensors implements PSI_Interface_Sensor
{
    /**
     * object for error handling
     *
     * @var Error
     */
    private $_error;

    /**
     * content to parse
     *
     * @var array
     */
    private $_lines = array ();

    /**
     * fill the private content var through tcp or file access
     */
    function __construct()
    {
        $this->_error = Error::Singleton();
        switch(strtolower(PSI_SENSOR_ACCESS))
        {
        case 'tcp':
            $lines = "";
            CommonFunctions::executeProgram('sysctl', '-w hw.sensors', $lines);
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
        $ar_buf = array ();
        $results = array ();
        $j = 0;
        foreach ($this->_lines as $line) {
            $ar_buf = preg_split("/[\s,]+/", $line);
            if ( isset ($ar_buf[3]) && $ar_buf[2] == 'temp') {
                $results[$j]['label'] = $ar_buf[1];
                $results[$j]['value'] = $ar_buf[3];
                $results[$j]['limit'] = '70.0';
                $results[$j]['percent'] = $results[$j]['value']*100/
                $results[$j]['limit'];
                $j++;
            }
        }
        return $results;
    }

    /**
     * get fan information
     *
     * @return array fans in array with lable
     */
    public function fans()
    {
        $ar_buf = array ();
        $results = array ();
        $j = 0;
        foreach ($this->_lines as $line) {
            $ar_buf = preg_split("/[\s,]+/", $line);
            if ( isset ($ar_buf[3]) && $ar_buf[2] == 'fanrpm') {
                $results[$j]['label'] = $ar_buf[1];
                $results[$j]['value'] = $ar_buf[3];
                $j++;
            }
        }
        return $results;
    }

    /**
     * get voltage information
     *
     * @return array voltage in array with lable
     */
    public function voltage()
    {
        $ar_buf = array ();
        $results = array ();
        $j = 0;
        foreach ($this->_lines as $line) {
            $ar_buf = preg_split("/[\s,]+/", $line);
            if ( isset ($ar_buf[3]) && $ar_buf[2] == 'volts_dc') {
                $results[$j]['label'] = $ar_buf[1];
                $results[$j]['value'] = $ar_buf[3];
                $results[$j]['min'] = '0.00';
                $results[$j]['max'] = '0.00';
                $j++;
            }
        }
        return $results;
    }
}
?>
