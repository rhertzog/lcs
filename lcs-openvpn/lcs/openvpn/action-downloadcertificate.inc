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

// Check permissions :)
// ...

$GETidDEC = sscanf ($GETid, '%X');

$sExt = '.pem';
if (isset ($_REQUEST['Type']))
 if     ($_REQUEST['Type'] == 'CRT') $sExt = '.pem';
 elseif ($_REQUEST['Type'] == 'KEY') $sExt = '.key';
 elseif ($_REQUEST['Type'] == 'CSR') $sExt = '.csr';
 elseif ($_REQUEST['Type'] == 'ZIP') $sExt = '.zip';

if ($sExt == '.zip')
{
 include_once ('include/zip.lib.php');
 $zipData = new zipfile ();

 // Add the certificate files
 foreach (array ('.pem', '.key', '.csr') as $Ext2zip)
 {
  if ($config['Download']['ZIP'][$Ext2zip])
  {
   $File2zip  = openssl_get_filename ($GETidDEC[0], $Ext2zip);
   $File2name = basename ($File2zip, '.pem');
   // Cheat the extension
   if ($Ext2zip == '.pem') $File2name .= '.crt';

   if (is_file ($File2zip))
    $zipData->addFile (implode ('', file ($File2zip)), $File2name);
  }
 }

 // Add the extra files
 foreach ($config['Download']['ZIP']['Others'] as $File2zip)
 {
  if (is_file ('downloads/'. $File2zip))
   $zipData->addFile (implode ('', file ('downloads/'. $File2zip)), $File2zip);
 }

 // Flush the ZIP file to the client
 html_download_data ($zipData -> file (), openssl_hex_serial ($GETidDEC[0]) .'.zip');
}
else
{
 if (($sCertificateFile = openssl_get_filename ($GETidDEC[0], $sExt)) == '') html_error ("Can't find requested file");

 // Cheat the extension
 if ($sExt == '.pem') $sExt = '.crt';

 // Download
 html_download ($sCertificateFile, openssl_hex_serial ($GETidDEC[0]) . $sExt);
 //html_dump ('$GLOBALS', $GLOBALS);
}

?>
