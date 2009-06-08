<?php
/**
 * compress js files and send them to the browser on the fly
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_JS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: js.php 172 2009-03-30 14:08:04Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * application root path
 *
 * @var string
 */
define('APP_ROOT', dirname(( __FILE__ )));

require_once APP_ROOT.'/includes/autoloader.inc.php';

$filepath = APP_ROOT.'/js/';

if ( isset ($_GET['name'])) {
    $file = basename(htmlspecialchars($_GET['name']));
    if (strtolower(substr($file, 0, 6)) == 'jquery' && ! isset ($_GET['plugin'])) {
        $filepath .= 'jQuery/';
    } else {
        if ( isset ($_GET['plugin'])) {
            $filepath = APP_ROOT.'/plugins/'.basename(htmlspecialchars($_GET['plugin'])).'/js/';
        } else {
            $filepath .= 'phpSysInfo/';
        }
    }
    $script = $filepath.$file.'.js';
    if (file_exists($script)) {
        $packer = new JavaScriptPacker(file_get_contents($script));
        header("content-type: application/x-javascript");
        echo $packer->pack();
    }
}
?>
