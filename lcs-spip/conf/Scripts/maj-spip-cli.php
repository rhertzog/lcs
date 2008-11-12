<?
@define('_DIR_RESTREINT_ABS', 'ecrire/');
include_once _DIR_RESTREINT_ABS.'inc_version.php';

include_spip('base/create');
spip_connect();

creer_base();
include_spip('base/upgrade');
maj_base();

include_spip('inc/acces');
include_spip('inc/config');
ecrire_acces();
init_config();
?>