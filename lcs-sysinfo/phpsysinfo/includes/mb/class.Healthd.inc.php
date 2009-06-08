<?php
/**
 * healthd sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Healthd.inc.php 185 2009-04-14 07:40:43Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from healthd
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Healthd implements PSI_Interface_Sensor
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
    public function __construct()
    {
        $this->_error = Error::Singleton();
        switch(strtolower(PSI_SENSOR_ACCESS)) {
        case 'command':
            $lines = "";
            CommonFunctions::executeProgram('healthdc', '-t', $lines);
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
        $ar_buf = preg_split("/\t+/", $this->_lines);
        $results[0]['label'] = 'temp1';
        $results[0]['value'] = $ar_buf[1];
        $results[0]['limit'] = '70.0';
        $results[0]['percent'] = $results[0]['value']*100/$results[0]['limit'];
        $results[1]['label'] = 'temp2';
        $results[1]['value'] = $ar_buf[2];
        $results[1]['limit'] = '70.0';
        $results[1]['percent'] = $results[1]['value']*100/$results[1]['limit'];
        $results[2]['label'] = 'temp3';
        $results[2]['value'] = $ar_buf[3];
        $results[2]['limit'] = '70.0';
        $results[2]['percent'] = $results[2]['value']*100/$results[2]['limit'];
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
        $ar_buf = preg_split("/\t+/", $this->_lines);
        $results[0]['label'] = 'fan1';
        $results[0]['value'] = $ar_buf[4];
        $results[0]['min'] = '3000';
        $results[1]['label'] = 'fan2';
        $results[1]['value'] = $ar_buf[5];
        $results[1]['min'] = '3000';
        $results[2]['label'] = 'fan3';
        $results[2]['value'] = $ar_buf[6];
        $results[2]['min'] = '3000';
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
        $ar_buf = preg_split("/\t+/", $this->_lines);
        $results[0]['label'] = 'Vcore1';
        $results[0]['value'] = $ar_buf[7];
        $results[0]['min'] = '0.00';
        $results[0]['max'] = '0.00';
        $results[1]['label'] = 'Vcore2';
        $results[1]['value'] = $ar_buf[8];
        $results[1]['min'] = '0.00';
        $results[1]['max'] = '0.00';
        $results[2]['label'] = '3volt';
        $results[2]['value'] = $ar_buf[9];
        $results[2]['min'] = '0.00';
        $results[2]['max'] = '0.00';
        $results[3]['label'] = '+5Volt';
        $results[3]['value'] = $ar_buf[10];
        $results[3]['min'] = '0.00';
        $results[3]['max'] = '0.00';
        $results[4]['label'] = '+12Volt';
        $results[4]['value'] = $ar_buf[11];
        $results[4]['min'] = '0.00';
        $results[4]['max'] = '0.00';
        $results[5]['label'] = '-12Volt';
        $results[5]['value'] = $ar_buf[12];
        $results[5]['min'] = '0.00';
        $results[5]['max'] = '0.00';
        $results[6]['label'] = '-5Volt';
        $results[6]['value'] = $ar_buf[13];
        $results[6]['min'] = '0.00';
        $results[6]['max'] = '0.00';
        return $results;
    }
}
?>
