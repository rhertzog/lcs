<?php 
/**
 * parser Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Parser.inc.php 218 2009-05-25 08:56:02Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * parser class with common used parsing metods
 *
 * @category  PHP
 * @package   PSI
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Parser
{
    /**
     * parsing the output of lspci command
     *
     * @return mixed
     */
    public static function lspci()
    {
        $arrResults = array();
        $strBuf = "";
        if (CommonFunctions::executeProgram("lspci", "", $strBuf, PSI_DEBUG)) {
            $arrLines = split("\n", $strBuf);
            foreach ($arrLines as $strLine) {
                list($strAddr, $strName) = explode(' ', trim($strLine), 2);
                $strName = preg_replace('/\(.*\)/', '', $strName);
                $arrResults[] = $strName;
            }
        }
        if ( empty($arrResults)) {
            return false;
        } else {
            asort($arrResults);
            return $arrResults;
        }
    }
    
    /**
     * parsing the output of pciconf command
     *
     * @return mixed
     */
    public static function pciconf()
    {
        $arrResults = array();
        $intS = 0;
        $strBuf = "";
        if (CommonFunctions::executeProgram("pciconf", "-lv", $strBuf, PSI_DEBUG)) {
            $arrLines = explode("\n", $strBuf);
            foreach ($arrLines as $strLine) {
                if (preg_match("/(.*) = '(.*)'/", $strLine, $arrParts)) {
                    if (trim($arrParts[1]) == "vendor") {
                        $arrResults[$intS] = trim($arrParts[2]);
                    } elseif (trim($arrParts[1]) == "device") {
                        $arrResults[$intS] .= " - ".trim($arrParts[2]);
                        $intS++;
                    }
                }
            }
        }
        if ( empty($arrResults)) {
            return false;
        } else {
            asort($arrResults);
            return $arrResults;
        }
    }
    
    /**
     * parsing the output of df command
     *
     * @param string $df_param additional parameter for df command
     *
     * @return array
     */
    public static function df($df_param = "")
    {
        $results = array();
        $j = 0;
        $df = "";
        $df2 = "";
        $mount = "";
        if (CommonFunctions::executeProgram('df', '-k '.$df_param, $df, PSI_DEBUG) || ! empty($df)) {
            $df = preg_split("/\n/", $df, -1, PREG_SPLIT_NO_EMPTY);
            natsort($df);
            if (PSI_SHOW_INODES) {
                if (CommonFunctions::executeProgram('df', '-i '.$df_param, $df2, PSI_DEBUG) || ! empty($df)) {
                    $df2 = preg_split("/\n/", $df2, -1, PREG_SPLIT_NO_EMPTY);
                    // Store inode use% in an associative array (df_inodes) for later use
                    foreach ($df2 as $df2_line) {
                        if (preg_match("/^(\S+).*\s([0-9]+)%/", $df2_line, $inode_buf)) {
                            $df_inodes[$inode_buf[1]] = $inode_buf[2];
                        }
                    }
                    unset($df2, $df2_line, $inode_buf);
                }
            }
            if (CommonFunctions::executeProgram('mount', '', $mount, PSI_DEBUG)) {
                $mount = preg_split("/\n/", $mount, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($mount as $mount_line) {
                    if (preg_match("/\S+ on (\S+) type (.*) \((.*)\)/", $mount_line, $mount_buf)) {
                        $mount_parm[$mount_buf[1]]['fstype'] = $mount_buf[2];
                        $mount_parm[$mount_buf[1]]['options'] = $mount_buf[3];
                    } elseif (preg_match("/\S+ (.*) on (\S+) \((.*)\)/", $mount_line, $mount_buf)) {
                        $mount_parm[$mount_buf[2]]['fstype'] = $mount_buf[1];
                        $mount_parm[$mount_buf[2]]['options'] = $mount_buf[3];
                    } elseif (preg_match("/\S+ on (\S+) \((\S+)(,\s(.*))?\)/", $mount_line, $mount_buf)) {
                        $mount_parm[$mount_buf[1]]['fstype'] = $mount_buf[2];
                        $mount_parm[$mount_buf[1]]['options'] = isset($mount_buf[4]) ? $mount_buf[4] : '';
                    }
                }
                unset($mount, $mount_line, $mount_buf);
                foreach ($df as $df_line) {
                    $df_buf1 = preg_split("/(\%\s)/", $df_line, 2);
                    if (count($df_buf1) != 2) {
                        continue;
                    }
                    preg_match("/(.*)(\s+)(([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)$)/", $df_buf1[0], $df_buf2);
                    $df_buf = array($df_buf2[1], $df_buf2[4], $df_buf2[6], $df_buf2[8], $df_buf2[10], $df_buf1[1]);
                    if (count($df_buf) == 6) {
                        $df_buf[5] = trim($df_buf[5]);
                        $results[$j] = array();
                        $results[$j]['disk'] = trim($df_buf[0]);
                        $results[$j]['size'] = $df_buf[1] * 1024;
                        $results[$j]['used'] = $df_buf[2] * 1024;
                        $results[$j]['free'] = $df_buf[3] * 1024;
                        if ($results[$j]['used'] < 0) {
                            $results[$j]['size'] = $results[$j]['free'] * 1024;
                            $results[$j]['free'] = 0;
                            $results[$j]['used'] = $results[$j]['size'] * 1024;
                        }
                        if ($results[$j]['size'] == 0) {
                            continue;
                        }
                        $results[$j]['percent'] = round(($results[$j]['used'] * 100) / $results[$j]['size']);
                        $results[$j]['mount'] = $df_buf[5];
                        $results[$j]['fstype'] = $mount_parm[$df_buf[5]]['fstype'];
                        $results[$j]['options'] = $mount_parm[$df_buf[5]]['options'];
                        if (PSI_SHOW_INODES && isset($df_inodes[$results[$j]['disk']])) {
                            $results[$j]['inodes'] = $df_inodes[$results[$j]['disk']];
                        }
                        $j++;
                    }
                }
                return $results;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
}
?>
