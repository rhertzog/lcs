<?php
if (!defined ("LOCAL_SECURITY_KEY"))
 die ();

include_once ('include/openvpn-functions.inc');
include_once ('include/openssl-functions.inc');

$PUTcountryName            = (isset ($_REQUEST['Country'])    ? ($_REQUEST['Country'])    : NULL);
$PUTstateOrProvinceName    = (isset ($_REQUEST['Province'])   ? ($_REQUEST['Province'])   : NULL);
$PUTlocalityName           = (isset ($_REQUEST['City'])       ? ($_REQUEST['City'])       : NULL);
$PUTorganizationName       = (isset ($_REQUEST['Company'])    ? ($_REQUEST['Company'])    : NULL);
$PUTorganizationalUnitName = (isset ($_REQUEST['Department']) ? ($_REQUEST['Department']) : NULL);
$PUTcommonName             = (isset ($_REQUEST['CommonName']) ? ($_REQUEST['CommonName']) : NULL);
$PUTemailAddress           = (isset ($_REQUEST['Email'])      ? ($_REQUEST['Email'])      : NULL);
$PUTvalidity               = (isset ($_REQUEST['Expiration']) ? ($_REQUEST['Expiration']) : NULL);

$Debug = '';

// Test run. Use different openssl folder
$TestPath = '/usr/local/etc/openvpn/test-server/';

$Result = openssl_build_key ($Debug,
                             array ('countryName'            => $PUTcountryName,
                                    'stateOrProvinceName'    => $PUTstateOrProvinceName,
                                    'localityName'           => $PUTlocalityName,
                                    'organizationName'       => $PUTorganizationName,
                                    'organizationalUnitName' => $PUTorganizationalUnitName,
                                    'commonName'             => $PUTcommonName,
                                    'emailAddress'           => $PUTemailAddress
                                   ),
                             $PUTvalidity
                            );

if ($Result != FALSE)
{
# header ('Location: '. $_SERVER["PHP_SELF"] .'?Action=ViewCertificate&id='. $Result);
 html_postredir ($_SERVER["PHP_SELF"] .'?Action=ViewCertificate&id='. $Result);
}
else
{
 echo '<pre>'. $Debug .'</pre>';
}
?>
