<?php
/**
 * hddtemp sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.HDDTemp.inc.php 185 2009-04-14 07:40:43Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from hddtemp
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @author    T.A. van Roermund <timo@van-roermund.nl>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class HDDTemp
{
    /**
     * object for error handling
     *
     * @var Error
     */
    private $_error;

    /**
     * create the error object
     */
    public function __construct()
    {
        $this->_error = Error::Singleton();
    }


    /**
     * get the temperature information from hddtemp
     * access is available through tcp or command
     *
     * @return array temperatures in array
     */
    public function temperature()
    {
        $ar_buf = array ();
        $results = array ();
        switch(PSI_HDD_TEMP) {
        case "tcp":
            $lines = '';
            // Timo van Roermund: connect to the hddtemp daemon, use a 5 second timeout.
            $fp = @fsockopen('localhost', 7634, $errno, $errstr, 5);
            // if connected, read the output of the hddtemp daemon
            if ($fp) {
                while (!feof($fp)) {
                    $lines .= fread($fp, 1024);
                }
                fclose($fp);
            } else {
                $this->_error->addError("HDDTemp error", $errno.", ".$errstr);
            }
            $lines = str_replace("||", "|\n|", $lines);
            $ar_buf = explode("\n", $lines);
            break;
        case "command":
            $strDrives = "";
            $strContent = "";
            $hddtemp_value = "";
            if (CommonFunctions::rfts("/proc/diskstats", $strContent, 0, 4096, false)) {
                $arrContent = explode("\n", $strContent);
                foreach ($arrContent as $strLine) {
                    preg_match("/^\s(.*)\s([a-z]*)\s(.*)/", $strLine, $arrSplit);
                    if (! empty($arrSplit[2])) {
                        $strDrive = '/dev/'.$arrSplit[2];
                        if (file_exists($strDrive)) {
                            $strDrives = $strDrives.$strDrive.' ';
                        }
                    }
                }
            } else {
                if (CommonFunctions::rfts("/proc/partitions", $strContent, 0, 4096, false)) {
                    $arrContent = explode("\n", $strContent);
                    foreach ($arrContent as $strLine) {
                        if (!preg_match("/^\s(.*)\s([\/a-z0-9]*(\/disc))\s(.*)/", $strLine, $arrSplit)) {
                            preg_match("/^\s(.*)\s([a-z]*)\s(.*)/", $strLine, $arrSplit);
                        }
                        if (! empty($arrSplit[2])) {
                            $strDrive = '/dev/'.$arrSplit[2];
                            if (file_exists($strDrive)) {
                                $strDrives = $strDrives.$strDrive.' ';
                            }
                        }
                    }
                }
            }
            if (trim($strDrives) == "") {
                return array ();
            }
            if (CommonFunctions::executeProgram("hddtemp", $strDrives, $hddtemp_value)) {
                $hddtemp_value = explode("\n", $hddtemp_value);
                foreach ($hddtemp_value as $line) {
                    $temp = preg_split("/:\s/", $line, 3);
                    if (count($temp) == 3 && preg_match("/^[0-9]/", $temp[2])) {
                        preg_match("/^([0-9]*)(.*)/", $temp[2], $ar_temp);
                        $temp[2] = trim($ar_temp[1]);
                        $temp[3] = trim($ar_temp[2]);
                        array_push($ar_buf, "|".implode("|", $temp)."|");
                    }
                }
            } else {
                return array ();
            }
            break;
        default:
            $this->_error->addConfigError("temperature()", "PSI_HDD_TEMP");
            break;
        }
        // Timo van Roermund: parse the info from the hddtemp daemon.
        $i = 0;
        foreach ($ar_buf as $line) {
            $data = array ();
            if (ereg("\|(.*)\|(.*)\|(.*)\|(.*)\|", $line, $data)) {
                if (trim($data[3]) != "ERR") {
                    // get the info we need
                    $results[$i]['label'] = $data[1];
                    if (is_numeric($data[3])) {
                        $results[$i]['value'] = $data[3];
                    } else {
                        $results[$i]['value'] = 0;
                    }
                    //extra processing because of a bug in some versions of hddtemp
                    $results[$i]['model'] = trim(str_replace("\x10\x80", "", $data[2]));
                    $i++;
                }
            }
        }
        return $results;
    }
}
?>
