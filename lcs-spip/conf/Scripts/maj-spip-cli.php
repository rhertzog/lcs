<?php
echo "0.";
define('_DIR_RESTREINT_ABS', 'ecrire/');
include_once _DIR_RESTREINT_ABS.'inc_version.php';
echo "1.";
include_spip('base/create');
spip_connect();
echo "2.";
creer_base();
include_spip('base/upgrade');
maj_base();
echo "3.";
include_spip('inc/acces');
include_spip('inc/config');
ecrire_acces();
?>