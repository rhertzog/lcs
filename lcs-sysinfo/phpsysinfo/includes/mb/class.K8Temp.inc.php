<?php 
/**
 * K8Temp sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.K8Temp.inc.php 187 2009-04-14 08:38:44Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from k8temp
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class K8Temp implements PSI_Interface_Sensor
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
     * fill the private array
     */
    function __construct()
    {
        $this->_error = Error::Singleton();
        switch (strtolower(PSI_SENSOR_ACCESS)) {
        case 'command': $lines = "";
            CommonFunctions::executeProgram('k8temp', '', $lines);
            $this->_lines = explode("\n", $lines);
            break;
        default: $this->_error->addConfigError('__construct()', 'PSI_SENSOR_ACCESS');
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
        foreach ($this->_lines as $line) {
            if (preg_match('/(.*):\s*(\d*)/', $line, $data)) {
                if ($data[2] <> '0') {
                    $results[$i]['label'] = $data[1];
                    $results[$i]['limit'] = '70.0';
                    if ($data[2] > 250) {
                        $results[$i]['value'] = 0;
                        $results[$i]['percent'] = 0;
                    } else {
                        $results[$i]['value'] = $data[2];
                        $results[$i]['percent'] = $results[$i]['value'] * 100 / $results[$i]['limit'];
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
        return $results;
    }
}
?>
