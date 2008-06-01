<?php
/* Administration LCS */
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");

list ($idpers, $login)= isauth();
if ($idpers == "0")    header("Location:$urlauth");
?>
<FRAMESET COLS="220,*" BORDER="0">
	<FRAME SRC="menu.php" NAME="menu"></FRAME>
	<FRAME SRC="../phpsysinfo/" NAME="main"></FRAME>
</FRAMESET>
