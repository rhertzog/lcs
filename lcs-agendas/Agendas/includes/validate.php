<?php
include "basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
// recuperation de l'id dans la table session
list ($idpers, $login)= isauth();
if ( empty ($login) ) {
     do_redirect ( "../../lcs/auth.php" );
}
?>