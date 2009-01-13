<?php
if (!defined ("LOCAL_SECURITY_KEY"))
 die ();

include_once ('include/openvpn-functions.inc');
include_once ('include/openssl-functions.inc');

openvpn_load_server_conf ();
if (!isset ($openvpn['Server'])) html_error ("The OpenVPN server has no configuration file");

openvpn_load_status ();
if (!isset ($openvpn['Server']['Title'])) html_error ("The OpenVPN server has no status file");

openssl_load_database ();
openvpn_find_connected ();

// load Smarty library
require('Smarty.class.php');
$smarty = new Smarty;

// Decoration
$smarty->assign ('Company_Name',  $config['Company_Name']);
$smarty->assign ('Company_Logo',  $config['Company_Logo']);
$smarty->assign ('URL_Home_Page', $config['URL_Home_Page']);
$smarty->assign ('title',         'OpenVPN Web GUI : OpenSSL certificate');

// Status Bar
$smarty->assign ('Server',        $openvpn['Server']);
$smarty->assign ('Seconds_Ago',   time () - $openvpn['Server']['Time']);
$smarty->assign ('Certificates',  $openssl['Database']);

// Action
$smarty->assign ('action',        'CERTIFICATES');
$smarty->assign ('subaction',     'VIEW');

// Action-Specific

$cntotal = count ($openssl['Database']);

$GETidDEC = sscanf ($GETid, '%X');

for ($j = 0; $j < $cntotal; $j++)
 if ($openssl['Database'][$j]['Serial'] == $GETid)
 {
  $openssl['Database'][$j]['ExpDate'] = str_openssldata_to_string ($openssl['Database'][$j]['ExpDate']);
  $openssl['Database'][$j]['RevDate'] = str_openssldata_to_string ($openssl['Database'][$j]['RevDate']);
  $smarty->assign ('Certificate',        $openssl['Database'][$j]);
  $smarty->assign ('CertificateFileCRT', openssl_get_filename ($GETidDEC[0], '.pem'));
  $smarty->assign ('CertificateFileKEY', openssl_get_filename ($GETidDEC[0], '.key'));
  $smarty->assign ('CertificateFileCSR', openssl_get_filename ($GETidDEC[0], '.csr'));
  break;
 }

// Display
$smarty->assign  ('PluginPath',    $config['PluginsAbsolutePath']);
$smarty->assign  ('Plugins',       $config['Plugins']);
$smarty->assign  ('Page',          'action-viewcertificate.tpl');
$smarty->display ('page.tpl');
?>
