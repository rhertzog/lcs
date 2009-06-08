<?php 
/**
 * SMART Plugin
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_SMART
 * @author    Antoine Bertin <diaoulael@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.SMART.inc.php 228 2009-06-03 05:56:44Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 
 /**
 * SMART plugin, which displays all SMART informations available
 *
 * @category  PHP
 * @package   PSI_Plugin_SMART
 * @author    Antoine Bertin <diaoulael@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class SMART extends PSI_Plugin
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
     * variable, which holds PSI_PLUGIN_SMART_IDS well formated datas
     * @var array
     */
    private $_ids = array();
    
    /**
     * read the data into an internal array and also call the parent constructor
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
        switch (PSI_PLUGIN_SMART_ACCESS) {
        case 'command':
            $disks = explode(',', PSI_PLUGIN_SMART_DEVICES);
            foreach ($disks as $disk) {
                $buffer = "";
                CommonFunctions::executeProgram('smartctl', '--all '.((PSI_PLUGIN_SMART_DEVICE) ? '--device '.PSI_PLUGIN_SMART_DEVICE : '').' '.$disk, $buffer, $this->_debug);
                $this->_filecontent[$disk] = $buffer;
            }
            $fullIds = explode(',', PSI_PLUGIN_SMART_IDS);
            foreach ($fullIds as $fullId) {
                $arrFullId = preg_split('/-/', $fullId);
                $this->_ids[$arrFullId[0]] = strtolower($arrFullId[1]);
            }
            break;
        default:
            $this->global_error->addError("switch(PSI_PLUGIN_SMART_ACCESS)", "Bad SMART configuration in SMART.config.php");
            break;
        }
    }
    
    /**
     * doing all tasks to get the required informations that the plugin needs
     * result is stored in an internal array
     *
     * @return void
     */
    public function execute()
    {
        if ( empty($this->_filecontent)) {
            return;
        }
        foreach ($this->_filecontent as $disk=>$result) {
            preg_match('/Vendor Specific SMART Attributes with Thresholds\:\n(.*)\n((.|\n)*)\n\nSMART Error Log Version\:/', $result, $vendorInfos);
            $labels = preg_split('/[\s]+/', $vendorInfos[1]);
            foreach ($labels as $k=>$v) {
                $labels[$k] = str_replace('#', '', strtolower($v));
            }
            $lines = preg_split('/\n/', $vendorInfos[2]);
            $i = 0; // Line number
            foreach ($lines as $line) {
                $line = preg_replace('/^[\s]+/', '', $line);
                $values = preg_split('/[\s]+/', $line);
                $j = 0;
                foreach ($values as $value) {
                    $this->_result[$disk][$i][$labels[$j]] = $value;
                    $j++;
                }
                $i++;
            }
        }
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
        $root = $dom->createElement('Plugin_'.__CLASS__);
        $dom->appendChild($root);
        $xml = simplexml_import_dom($dom);
        if ( empty($this->_result) || empty($this->_ids)) {
            return $xml;
        }
        
        $columnsChild = $xml->addChild('columns');
        // Fill the xml with preferences
        foreach ($this->_ids as $id=>$column_name) {
            $columnChild = $columnsChild->addChild('column');
            $columnChild->addAttribute('id', XML::toUTF8($id, $enc));
            $columnChild->addAttribute('name', XML::toUTF8($column_name, $enc));
        }
        
        $disksChild = $xml->addChild('disks');
        // Now fill the xml with S.M.A.R.T datas
        foreach ($this->_result as $diskName=>$diskInfos) {
            $diskChild = $disksChild->addChild('disk');
            $diskChild->addAttribute('name', XML::toUTF8($diskName, $enc));
            foreach ($diskInfos as $lineInfos) {
                $lineChild = $diskChild->addChild('attribute');
                foreach ($lineInfos as $label=>$value) {
                    $lineChild->addAttribute($label, XML::toUTF8($value, $enc));
                }
            }
        }
        return $xml;
    }
}
?>
