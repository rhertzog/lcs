<?php 
/**
 * Basic OS Functions
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Interfaces
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.PSI_Interface_OS.inc.php 219 2009-05-25 09:00:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * define which methods a os class for phpsysinfo must implement
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
interface PSI_Interface_OS
{
    /**
     * Virtual Host Name
     *
     * @return string
     */
    function vhostname();
    
    /**
     * IP of the Virtual Host Name
     *
     *  @return string
     */
    function vipaddr();
    
    /**
     * Canonical Host Name
     *
     * @return string
     */
    function chostname();
    
    /**
     * IP of the Canonical Host Name
     *
     * @return string
     */
    function ipaddr();
    
    /**
     * Kernel Version
     *
     * @return string
     */
    function kernel();
    
    /**
     * UpTime
     * time the system is running
     *
     * @return integer
     */
    function uptime();
    
    /**
     * Number of Users
     *
     * @return integer
     */
    function users();
    
    /**
     * Processor Load
     * optionally create a loadbar
     *
     * @param boolean $bar include a bar
     *
     * @return array
     */
    function loadavg($bar = false);
    
    /**
     * CPU information
     *
     * @return array
     */
    function cpuinfo();
    
    /**
     * PCI devices
     *
     * @return array
     */
    function pci();
    
    /**
     * IDE devices
     *
     * @return array
     */
    function ide();
    
    /**
     * SCSI devices
     *
     * @return array
     */
    function scsi();
    
    /**
     * USB devices
     *
     * @return array
     */
    function usb();
    
    /**
     * Network devices
     *
     * @return array
     */
    function network();
    
    /**
     * Physical memory information and Swap Space information
     *
     * @return array
     */
    function memory();
    
    /**
     * filesystem information
     *
     * @return array
     */
    function filesystems();
    
    /**
     * Distribution
     *
     * @return string
     */
    function distro();
    
    /**
     * Distribution Icon
     *
     * @return string
     */
    function distroicon();
    
    /**
     * get a special encoding from os where phpsysinfo is running
     *
     * @return string
     */
    function getEncoding();
}
?>
