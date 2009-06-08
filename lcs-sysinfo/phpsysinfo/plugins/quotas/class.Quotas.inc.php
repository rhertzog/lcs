<?php 
/**
 * QUOTAS Plugin
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_Quotas
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Quotas.inc.php 228 2009-06-03 05:56:44Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * quota Plugin, which displays all quotas on the machine
 * display all quotas in a sortable table with the current values which are determined by
 * calling the "repquota" command line utility, another way is to provide
 * a file with the output of the repquota utility, so there is no need to run a execute by the
 * webserver, the format of the command is written down in the quota.config.php file, where also
 * the method of getting the information is configured
 *
 * @category  PHP
 * @package   PSI_Plugin_Quotas
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Quotas extends PSI_Plugin
{
    /**
     * variable, which holds the content of the command
     * @var array
     */
    private $_filecontent = array();
    
    /**
     * variable, which holds the result before the xml is generated out of this array
     * @var array
     */
    private $_result = array();
    
    /**
     * controls if debugging is turned on or off, value is defined in the main config.php
     * @var boolean
     */
    private $_debug = PSI_DEBUG;
    
    /**
     * read the data into an internal array and also call the parent constructor
     */
    public function __construct()
    {
        $buffer = "";
        parent::__construct(__CLASS__);
        switch (PSI_PLUGIN_QUOTAS_ACCESS) {
        case 'command':
            CommonFunctions::executeProgram("repquota", "-au", $buffer, $this->_debug);
            break;
        case 'data':
            CommonFunctions::rfts(APP_ROOT."/data/quotas.txt", $buffer);
            break;
        default:
            $this->global_error->addConfigError("__construct()", "PSI_PLUGIN_QUOTAS_ACCESS");
            break;
        }
        if (trim($buffer) != "") {
            $this->_filecontent = explode("\n", $buffer);
            unset($this->_filecontent[0]);
        } else {
            $this->_filecontent = array();
        }
    }
    
    /**
     * doing all tasks to get the required informations that the plugin needs
     * result is stored in an internal array<br>the array is build like a tree,
     * so that it is possible to get only a specific process with the childs
     *
     * @return void
     */
    public function execute()
    {
        $i = 0;
        if ( empty($this->_filecontent)) {
            return;
        }
        foreach ($this->_filecontent as $thisline) {
            $thisline = preg_replace("/([\s]--)/", "", $thisline);
            $thisline = preg_split("/(\s)/e", $thisline, -1, PREG_SPLIT_NO_EMPTY);
            if (count($thisline) == 7) {
                $quotas[$i]['user'] = str_replace("--", "", $thisline[0]);
                $quotas[$i]['byte_used'] = $thisline[1] * 1024;
                $quotas[$i]['byte_soft'] = $thisline[2] * 1024;
                $quotas[$i]['byte_hard'] = $thisline[3] * 1024;
                if ($thisline[3] != 0) {
                    $quotas[$i]['byte_percent_used'] = round((($quotas[$i]['byte_used'] / $quotas[$i]['byte_hard']) * 100), 1);
                } else {
                    $quotas[$i]['byte_percent_used'] = 0;
                }
                $quotas[$i]['file_used'] = $thisline[4];
                $quotas[$i]['file_soft'] = $thisline[5];
                $quotas[$i]['file_hard'] = $thisline[6];
                if ($thisline[6] != 0) {
                    $quotas[$i]['file_percent_used'] = round((($quotas[$i]['file_used'] / $quotas[$i]['file_hard']) * 100), 1);
                } else {
                    $quotas[$i]['file_percent_used'] = 0;
                }
                $i++;
            }
        }
        $this->_result = $quotas;
    }
    
    /**
     * generates the XML content for the plugin
     *
     * @param string $enc base encoding
     *
     * @return SimpleXMLObject entire XML content for the plugin
     */
    public function xml($enc)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement("Plugin_".__CLASS__);
        $dom->appendChild($root);
        $xml = simplexml_import_dom($dom);
        if ( empty($this->_result)) {
            return $xml;
        }
        foreach ($this->_result as $quota) {
            $quotaChild = $xml->addChild("Quota");
            $quotaChild->addChild("User", XML::toUTF8($quota['user'], $enc));
            $quotaChild->addChild("ByteUsed", XML::toUTF8($quota['byte_used'], $enc));
            $quotaChild->addChild("ByteSoft", XML::toUTF8($quota['byte_soft'], $enc));
            $quotaChild->addChild("ByteHard", XML::toUTF8($quota['byte_hard'], $enc));
            $quotaChild->addChild("BytePercentUsed", XML::toUTF8($quota['byte_percent_used'], $enc));
            $quotaChild->addChild("FileUsed", XML::toUTF8($quota['file_used'], $enc));
            $quotaChild->addChild("FileSoft", XML::toUTF8($quota['file_soft'], $enc));
            $quotaChild->addChild("FileHard", XML::toUTF8($quota['file_hard'], $enc));
            $quotaChild->addChild("FilePercentUsed", XML::toUTF8($quota['file_percent_used'], $enc));
        }
        return $xml;
    }
}
?>
