<?php

//
// This url is open when the user clicks on the key logo next to the 
// 'user name' field of the login page
//

//$swekey_promo_url='http://www.swekey.com?promo=LinuxCommunicationServer';
$swekey_promo_url='http://lcs.crdp.ac-caen.fr/Docs/lcs_swekey.html';

//
// By default the swekey module uses the http swekey servers
// If you need more security use the https server (higher response time)
//

$swekey_check_server='http://auth-check.musbe.net';
$swekey_status_server='http://auth-status.musbe.net';
$swekey_rndtoken_server='http://auth-rnd-gen.musbe.net';

//$swekey_check_server='https://auth-check-ssl.musbe.net';
//$swekey_status_server='https://auth-status-ssl.musbe.net';
//$swekey_rndtoken_server='https://auth-rnd-gen-ssl.musbe.net';


//
// By default we allow users that have disabled swekeys 
// (less secure but user friendly)
//

$swekey_allow_disabled=true;


//
// By default each user can manage its swekey 
// Set this value to false ifd only the admin can manage the swekeys
//

$swekey_user_managment=true;


//
// By default we accept all swekeys
// A brand is a 8 chars hexacimal numver (upper case)
// Brands are coma separated
//

$swekey_brands='';

?>