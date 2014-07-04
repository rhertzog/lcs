<?php
/*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "../Annu/includes/check-token.php";
if (!check_acces()) exit;

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");

?>
<FRAMESET COLS="220,*" BORDER="0">
	<FRAME SRC="menu.php" NAME="menu"></FRAME>
	<FRAME SRC="../phpsysinfo/" NAME="main"></FRAME>
</FRAMESET>
