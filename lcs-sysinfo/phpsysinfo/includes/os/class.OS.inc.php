<?php 
/**
 * Basic OS Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.OS.inc.php 219 2009-05-25 09:00:39Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * Basic OS functions for all OS classes
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
abstract class OS implements PSI_Interface_OS
{
    /**
     * object for error handling
     *
     * @var Error
     */
    protected $error;
    
    /**
     * build the global Error object
     */
    public function __construct()
    {
        $this->error = Error::singleton();
    }
    
    /**
     * get os specific encoding
     *
     * @see PSI_Interface_OS::getEncoding()
     *
     * @return string
     */
    function getEncoding()
    {
        return null;
    }
}
?>
