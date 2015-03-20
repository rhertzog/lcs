<?php
include "basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
session_name("Lcs");
@session_start();
$login=$_SESSION['login'];
 session_write_close();
if ( empty ($login) ) {
     do_redirect ( "../../lcs/logout.php" );
}
?>
