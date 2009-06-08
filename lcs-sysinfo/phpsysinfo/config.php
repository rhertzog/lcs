<?php
/**
 * PSI Config File
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: config.php.new 208 2009-05-18 06:28:09Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */

// ********************************
//        MAIN PARAMETERS
// ********************************

/**
 * Turn on dubugging of some functions and include errors and warnings in xml and provide a popup for displaying errors
 * - false : no debug infos are stored in xml or displayed
 * - true : debug infos stored in xml and displayed *be carfull if set this to true, may include infos from your pc*
 */
define('PSI_DEBUG', false);

/**
 * Additional paths where to look for installed programs
 * Example : define('PSI_ADD_PATHS', '/opt/bin','/opt/sbin');
 */
define('PSI_ADD_PATHS', false);

/**
 * Plugins that should be included in xml and output (!!!plugin names are case-sensitive!!!)
 * List of plugins should look like "plugin,plugin,plugin". See /plugins directory
 * - define('PSI_PLUGINS', 'plugin,plugin'); // list of plugins
 * - define('PSI_PLUGINS', false); //no plugins
 */
define('PSI_PLUGINS', false);


// ********************************
//       DISPLAY PARAMETERS
// ********************************

/**
 * Define the default language 
 */
define('PSI_DEFAULT_LANG', 'fr');

/**
 * Define the default template 
 */
define('PSI_DEFAULT_TEMPLATE', 'lcs');

/**
 * Show or hide language picklist
 */
define('PSI_SHOW_PICKLIST_LANG', true);

/**
 * Show or hide template picklist
 */
define('PSI_SHOW_PICKLIST_TEMPLATE', true);

/**
 * Define the intervalle for refreshing data in ms
 * - 0 = disabled
 * - 1000 = 1 second
 * - Default is 10 seconds
 */
define('PSI_REFRESH',10000);

/**
 * Show a graph for current cpuload
 * - true = displayed, but it's a performance hit (because we have to wait to get a value, 1 second)
 * - false = will not be displayed
 */
define('PSI_LOAD_BAR', false);

/**
 * Display the virtual host name and address
 * - Default is canonical host name and address
 * - Use define('PSI_USE_VHOST', true); to display virtual host name.
 */
define('PSI_USE_VHOST', false);

/**
 * Controls the units & format for network, memory and filesystem 
 * - 1 KiB = 2^10 bytes = 1,024 bytes
 * - 1 KB = 10^3 bytes = 1,000 bytes
 * - 'PiB'    everything is in PeBiByte
 * - 'TiB'    everything is in TeBiByte
 * - 'GiB'    everything is in GiBiByte
 * - 'MiB'    everything is in MeBiByte
 * - 'KiB'    everything is in KiBiByte
 * - 'auto_binary' everything is automatic done if value is to big for, e.g MiB then it will be in GiB
 * - 'PB'    everything is in PetaByte
 * - 'TB'    everything is in TeraByte
 * - 'GB'    everything is in GigaByte
 * - 'MB'    everything is in MegaByte
 * - 'KB'    everything is in KiloByte
 * - 'auto_decimal' everything is automatic done if value is to big for, e.g MB then it will be in GB
 */
define('PSI_BYTE_FORMAT', 'auto_binary');

/**
 * Format in which temperature is displayed
 * - 'c'    shown in celsius
 * - 'f'    shown in fahrenheit
 * - 'c-f'  both shown first celsius and fahrenheit in braces
 * - 'f-c'  both shown first fahrenheit and celsius in braces
 */
define('PSI_TEMP_FORMAT', 'c');


// ********************************
//       SENSORS PARAMETERS
// ********************************

/**
 * Define the motherboard monitoring program (!!!names are case-sensitive!!!)
 * We support the following programs so far
 * - 1. LMSensors  http://www.lm-sensors.org/
 * - 2. Healthd    http://healthd.thehousleys.net/
 * - 3. HWSensors  http://www.openbsd.org/
 * - 4. MBMon      http://www.nt.phys.kyushu-u.ac.jp/shimizu/download/download.html
 * - 5. MBM5       http://mbm.livewiredev.com/
 * - 6. Coretemp
 * - 7. IPMI       http://openipmi.sourceforge.net/
 * - 8. K8Temp     http://hur.st/k8temp/
 * Example: If you want to use lmsensors : define('PSI_SENSOR_PROGRAM', 'LMSensors');
 */
define('PSI_SENSOR_PROGRAM', false);

/**
 * Define hot to access the monitor program
 * Available methods for the above list are in the following list
 * default method 'file' should be fine for everybody
 * !!! tcp connections are only made local and on the default port !!!
 * - 1. LMSensors  command
 * - 2. Healthd    command
 * - 3. HWSensors  command
 * - 4. MBMon      command, tcp
 * - 5. MBM5       file
 * - 6. Coretemp   command
 * - 7. IPMI       command
 * - 8. K8Temp     command
 */
define('PSI_SENSOR_ACCESS', 'file');

/**
 * Hddtemp program
 * If the hddtemp program is available we can read the temperature, if hdd is smart capable
 * !!ATTENTION!! hddtemp might be a security issue
 * - define('PSI_HDD_TEMP', 'tcp');	     // read data from hddtemp deamon (localhost:7634)
 * - define('PSI_HDD_TEMP', 'command');  // read data from hddtemp programm (must be set suid)
 */
define('PSI_HDD_TEMP', false);


// ********************************
//      FILESYSTEM PARAMETERS
// ********************************

/**
 * Show mount point
 * - true = show mount point
 * - false = do not show mount point
 */
define('PSI_SHOW_MOUNT_POINT', true);

/**
 * Show bind
 * - true = display filesystems mounted with the bind options under Linux
 * - false = hide them
 */
define('PSI_SHOW_BIND', false);

/**
 * Show inode usage
 * - true = display used inodes in percent
 * - false = hide them
 */
define('PSI_SHOW_INODES', true);

/**
 * Hide mounts
 * Example : define('PSI_HIDE_MOUNTS', '/home,/usr');
 */
define('PSI_HIDE_MOUNTS', '');

/**
 * Hide filesystem types
 * Example : define('PSI_HIDE_FS_TYPES', 'tmpfs,usbfs');
 */
define('PSI_HIDE_FS_TYPES', '');

/**
 * Hide partitions
 * Example : define('PSI_HIDE_DISKS', 'rootfs');
 */
define('PSI_HIDE_DISKS', '');


// ********************************
//      NETWORK PARAMETERS
// ********************************

/**
 * Hide network interfaces
 * Example : define('PSI_HIDE_NETWORK_INTERFACE', 'eth0,sit0');
 */
define('PSI_HIDE_NETWORK_INTERFACE', '');


// ********************************
//        UPS PARAMETERS
// ********************************

/**
 * Define the ups monitoring program (!!!names are case-sensitive!!!)
 * We support the following programs so far
 * - 1. Apcupsd  http://www.apcupsd.com/
 * - 2. Nut      http://www.networkupstools.org/
 * Example: If you want to use Apcupsd : define('PSI_UPS_PROGRAM', 'Apcupsd');
 */
define('PSI_UPS_PROGRAM', false);

/**
 * Apcupsd supports multiple UPSes
 * You can specify comma delimited list in the form <hostname>:<port> or <ip>:<port>. The defaults are: 127.0.0.1:3551
 * See the following parameters in apcupsd.conf: NETSERVER, NISIP, NISPORT
 */
define('PSI_UPS_APCUPSD_LIST', '127.0.0.1:3551');

?>
