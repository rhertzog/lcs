<?php 
/**
 * class autoloader
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: autoloader.inc.php 224 2009-05-28 06:41:11Z jacky672 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * automatic loading classes when using them
 *
 * @param string $class_name name of the class which must be loaded
 *
 * @return void
 */
function __autoload($class_name)
{
    $class_name = str_replace('-', '', $class_name);
    $dirs = array('/plugins/'.strtolower($class_name).'/', '/includes/', '/includes/interface/', '/includes/os/', '/includes/mb/', '/includes/plugin/', '/includes/xml/', '/includes/web/', '/includes/error/', '/includes/js/', '/includes/output/', '/includes/ups/');
    
    foreach ($dirs as $dir) {
        if (file_exists(APP_ROOT.$dir.'class.'.$class_name.'.inc.php')) {
            include_once APP_ROOT.$dir.'class.'.$class_name.'.inc.php';
            return;
        }
    }
    
    $error = Error::singleton();
    
    $error->addError("_autoload(\"".$class_name."\")", "autoloading of class file (class.".$class_name.".inc.php) failed!");
    echo $error->errorsAsHTML();
    die();
}

?>
