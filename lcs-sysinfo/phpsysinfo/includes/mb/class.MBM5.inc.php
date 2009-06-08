<?php
/**
 * MBM5 sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.MBM5.inc.php 185 2009-04-14 07:40:43Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from Motherboard Monitor 5
 * information retrival through csv file
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class MBM5 implements PSI_Interface_Sensor
{
    /**
     * object for error handling
     *
     * @var Error
     */
    private $_error;

    /**
     * array with the names of the labels
     *
     * @var array
     */
    private $_buf_label = array ();


    /**
     * array withe the values
     *
     * @var array
     */
    private $_buf_value = array ();

    /**
     * read the MBM5.csv file and fill the private arrays
     */
    function __construct()
    {
        $this->_error = Error::Singleton();
        switch(strtolower(PSI_SENSOR_ACCESS)) {
        case 'file':
            $buffer = "";
            CommonFunctions::rfts(APP_ROOT."/data/MBM5.csv", $buffer);
            if (strpos($buffer, ";") === false) {
                $delim = ",";
            } else {
                $delim = ";";
            }
            $buffer = split("\n", $buffer);
            $this->_buf_label = split($delim, substr($buffer[0], 0, -2));
            $this->_buf_value = split($delim, substr($buffer[1], 0, -2));
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
        $intCount = 0;
        for ($intPosi = 3; $intPosi < 6; $intPosi++) {
            if ($this->_buf_value[$intPosi] == 0) {
                continue ;
            }
            $results[$intCount]['label'] = $this->_buf_label[$intPosi];
            preg_match("/([0-9\.])*/", str_replace(",", ".", $this->_buf_value[$intPosi]), $hits);
            $results[$intCount]['value'] = $hits[0];
            $results[$intCount]['limit'] = '70.0';
            $intCount++;
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
        $intCount = 0;
        for ($intPosi = 13; $intPosi < 16; $intPosi++) {
            if (! isset ($this->_buf_value[$intPosi])) {
                continue ;
            }
            $results[$intCount]['label'] = $this->_buf_label[$intPosi];
            preg_match("/([0-9\.])*/", str_replace(",", ".", $this->_buf_value[$intPosi]), $hits);
            $results[$intCount]['value'] = $hits[0];
            $results[$intCount]['min'] = '3000';
            $intCount++;
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
        $intCount = 0;
        for ($intPosi = 6; $intPosi < 13; $intPosi++) {
            if ($this->_buf_value[$intPosi] == 0) {
                continue ;
            }
            $results[$intCount]['label'] = $this->_buf_label[$intPosi];
            preg_match("/([0-9\.])*/", str_replace(",", ".", $this->_buf_value[$intPosi]), $hits);
            $results[$intCount]['value'] = $hits[0];
            $results[$intCount]['min'] = '0.00';
            $results[$intCount]['max'] = '0.00';
            $intCount++;
        }
        return $results;
    }
}
?>
