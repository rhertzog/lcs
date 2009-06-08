<?php
/**
 * MDSTAT Plugin Config File
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_MDStatus
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: MDStatus.config.php 168 2009-03-26 15:07:50Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * define how to access the mdstat statistic data
 * - 'file' /proc/mdstat is read
 * - 'data' (a file must be available in the data directory of the phpsysinfo installation with the filename "mdstat.txt"; content is the output from "cat /proc/mdstat")
 */
define('PSI_PLUGIN_MDSTAT_ACCESS', 'data');
?>
