<?php 
/**
 * Basic Sensor Functions
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Interfaces
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.PSI_Interface_Sensor.inc.php 214 2009-05-25 08:32:40Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * define which methods every sensor class for phpsysinfo must implement
 * to be recognized and fully work without errors, these are the methods which
 * are called from outside to include the information in the main application
 *
 * @category  PHP
 * @package   PSI_Interfaces
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
interface PSI_Interface_Sensor
{
    /**
     * get temperature information
     *
     * @return array
     */
    function temperature();
    
    /**
     * get voltage information
     *
     * @return array
     */
    function voltage();
    
    /**
     * get fan information
     *
     * @return array
     */
    function fans();
}
?>
