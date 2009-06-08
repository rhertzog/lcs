<?php
/**
 * coretemp sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Coretemp.inc.php 185 2009-04-14 07:40:43Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting hardware temperature information through sysctl
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @author    William Johansson <radar@radhuset.org>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Coretemp implements PSI_Interface_Sensor
{
    /**
     * get temperature information
     *
     * @return array temperatures in array with lable
     */
    function temperature()
    {
        $results = array ();
        $smp = 1;
        CommonFunctions::executeProgram('sysctl', '-n kern.smp.cpus', $smp);
        for ($i = 0; $i < $smp; $i++) {
            $temp = 0;
            if (CommonFunctions::executeProgram('sysctl', '-n dev.cpu.'.$i.'.temperature', $temp)) {
                $results[$i]['label'] = "CPU ".($i+1);
                $results[$i]['value'] = $temp;
                $results[$i]['limit'] = '70.0';
                $results[$i]['percent'] = $results[$i]['value']*100/
                $results[$i]['limit'];
            }
        }
        return $results;
    }

    /**
     * get fan information
     *
     * @return array fans in array with lable
     */
    function fans()
    {
        return null;
    }

    /**
     * get voltage information
     *
     * @return array voltage in array with lable
     */
    function voltage()
    {
        return null;
    }
}
?>
