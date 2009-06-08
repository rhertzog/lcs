<?php
/**
 * mbmon sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.MBMon.inc.php 185 2009-04-14 07:40:43Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from mbmon
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class MBMon implements PSI_Interface_Sensor
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
        case 'tcp':
            $fp = fsockopen("localhost", 411, $errno, $errstr, 5);
            if ($fp) {
                $lines = "";
                while (!feof($fp)) {
                    $lines .= fread($fp, 1024);
                }
                $this->_lines = explode("\n", $lines);
            } else {
                $this->_error->addError("fsockopen()", $errno." ".$errstr);
            }
            break;
        case 'command':
            CommonFunctions::executeProgram('mbmon', '-c 1 -r', $lines);
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
        $results = array ();
        $i = 0;
        foreach ($this->_lines as $line) {
            if (preg_match('/^(TEMP\d*)\s*:\s*(.*)$/D', $line, $data)) {
                if ($data[2] <> '0') {
                    $results[$i]['label'] = $data[1];
                    $results[$i]['limit'] = '70.0';
                    if ($data[2] > 250) {
                        $results[$i]['value'] = 0;
                        $results[$i]['percent'] = 0;
                    } else {
                        $results[$i]['value'] = $data[2];
                        $results[$i]['percent'] = $results[$i]['value']*100/$results[$i]['limit'];
                    }
                    $i++;
                }
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
        $results = array ();
        $i = 0;
        foreach ($this->_lines as $line) {
            if (preg_match('/^(FAN\d*)\s*:\s*(.*)$/D', $line, $data)) {
                if ($data[2] <> '0') {
                    $results[$i]['label'] = $data[1];
                    $results[$i]['value'] = $data[2];
                    $results[$i]['min'] = '3000';
                    $i++;
                }
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
        $results = array ();
        $i = 0;
        foreach ($this->_lines as $line) {
            if (preg_match('/^(V.*)\s*:\s*(.*)$/D', $line, $data)) {
                if ($data[2] <> '+0.00') {
                    $results[$i]['label'] = $data[1];
                    $results[$i]['value'] = $data[2];
                    $results[$i]['min'] = '0.00';
                    $results[$i]['max'] = '0.00';
                    $i++;
                }
            }
        }
        return $results;
    }
}
?>
