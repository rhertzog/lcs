<?php
/**
 * lmsensor sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.LMSensors.inc.php 185 2009-04-14 07:40:43Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from lmsensor
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class LMSensors implements PSI_Interface_Sensor
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
        switch(strtolower(PSI_SENSOR_ACCESS)) {
        case 'command':
            $lines = "";
            if (CommonFunctions::executeProgram("sensors", "", $lines)) {
                // Martijn Stolk: Dirty fix for misinterpreted output of sensors,
                // where info could come on next line when the label is too long.
                $lines = str_replace(":\n", ":", $lines);
                $lines = str_replace("\n\n", "\n", $lines);
                $this->_lines = explode("\n", $lines);
            }
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
        $sensors_value = $this->_lines;
        foreach ($sensors_value as $line) {
            $data = array ();
            if (ereg("(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)", $line, $data)) {
                ;
            } elseif (ereg("(.*):(.*)\((.*)=(.*)\)(.*)", $line, $data)) {
                ;
            } else {
                (ereg("(.*):(.*)", $line, $data));
            }
            if (count($data) > 1) {
                $temp = substr(trim($data[2]), -1);
                switch($temp) {
                case "C":
                case "F":
                    array_push($ar_buf, $line);
                }
            }
        }
        $i = 0;
        foreach ($ar_buf as $line) {
            unset ($data);
            if (ereg("(.*):(.*).C[ ]*\((.*)=(.*).C,(.*)=(.*).C\)(.*)\)", $line, $data)) {
                ;
            } elseif (ereg("(.*):(.*).C[ ]*\((.*)=(.*).C,(.*)=(.*).C\)(.*)", $line, $data)) {
                ;
            } elseif (ereg("(.*):(.*).C[ ]*\((.*)=(.*).C\)(.*)", $line, $data)) {
                ;
            } else {
                (ereg("(.*):(.*).C", $line, $data));
            }
            foreach ($data as $key=>$value) {
                if (preg_match("/^\+?([0-9\.]+).?$/", trim($value), $newvalue)) {
                    $data[$key] = trim($newvalue[1]);
                } else {
                    $data[$key] = trim($value);
                }
            }
            $results[$i]['label'] = $data[1];
            $results[$i]['value'] = $data[2];
            if ( isset ($data[6]) && $data[2] > $data[6]) {
                $results[$i]['limit'] = "75";
                $results[$i]['perce'] = "75";
            } else {
                $results[$i]['limit'] = isset ($data[4])?$data[4]:"75";
                $results[$i]['perce'] = isset ($data[6])?$data[6]:"75";
            }
            if ($results[$i]['limit'] < $results[$i]['perce']) {
                $results[$i]['limit'] = $results[$i]['perce'];
            }
            $i++;
        }
        asort($results);
        return array_values($results);
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
        $sensors_value = $this->_lines;
        foreach ($sensors_value as $line) {
            $data = array ();
            if (ereg("(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)", $line, $data)) {
                ;
            } elseif (ereg("(.*):(.*)\((.*)=(.*)\)(.*)", $line, $data)) {
                ;
            } else {
                ereg("(.*):(.*)", $line, $data);
            }
            if (count($data) > 1) {
                $temp = explode(" ", trim($data[2]));
                if (count($temp) == 1) {
                    $temp = explode("\xb0", trim($data[2]));
                }
                if ( isset ($temp[1])) {
                    switch($temp[1]) {
                    case "RPM":
                        array_push($ar_buf, $line);
                    }
                }
            }
        }
        $i = 0;
        foreach ($ar_buf as $line) {
            unset ($data);
            if (ereg("(.*):(.*) RPM  \((.*)=(.*) RPM,(.*)=(.*)\)(.*)\)", $line, $data)) {
                ;
            } elseif (ereg("(.*):(.*) RPM  \((.*)=(.*) RPM,(.*)=(.*)\)(.*)", $line, $data)) {
                ;
            } elseif (ereg("(.*):(.*) RPM  \((.*)=(.*) RPM\)(.*)", $line, $data)) {
                ;
            } else {
                ereg("(.*):(.*) RPM", $line, $data);
            }
            $results[$i]['label'] = trim($data[1]);
            $results[$i]['value'] = trim($data[2]);
            $results[$i]['min'] = isset ($data[4])?trim($data[4]):0;
            $i++;
        }
        asort($results);
        return array_values($results);
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
        $sensors_value = $this->lines;
        foreach ($sensors_value as $line) {
            $data = array ();
            if (ereg("(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)", $line, $data)) {
                ;
            } else {
                ereg("(.*):(.*)", $line, $data);
            }
            if (count($data) > 1) {
                $temp = explode(" ", trim($data[2]));
                if (count($temp) == 1) {
                    $temp = explode("\xb0", trim($data[2]));
                }
                if ( isset ($temp[1])) {
                    switch($temp[1]) {
                    case "V":
                        array_push($ar_buf, $line);
                    }
                }
            }
        }
        $i = 0;
        foreach ($ar_buf as $line) {
            unset ($data);
            if (ereg("(.*):(.*) V  \((.*)=(.*) V,(.*)=(.*) V\)(.*)\)", $line, $data)) {
                ;
            } elseif (ereg("(.*):(.*) V  \((.*)=(.*) V,(.*)=(.*) V\)(.*)", $line, $data)) {
                ;
            } else {
                ereg("(.*):(.*) V$", $line, $data);
            }
            foreach ($data as $key=>$value) {
                if (preg_match("/^\+?([0-9\.]+)$/", trim($value), $newvalue)) {
                    $data[$key] = trim($newvalue[1]);
                } else {
                    $data[$key] = trim($value);
                }
            }
            if ( isset ($data[1])) {
                $results[$i]['label'] = $data[1];
                $results[$i]['value'] = $data[2];
                $results[$i]['min'] = isset ($data[4])?$data[4]:0;
                $results[$i]['max'] = isset ($data[6])?$data[6]:0;
                $i++;
            }
        }
        return $results;
    }
}
?>
