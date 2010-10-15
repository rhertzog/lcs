<?
 include "includes/secure.inc.php";

$content .="<div id=window1>";
$content .= "<div id=R1 class=floatingWindowContent>";
$content .= "	</div>";
$content .="	<div id=R2 class=floatingWindowContent>";
$content .="	</div>";
$content .="	<div id=R3 class=floatingWindowContent>";
$content .="	</div>";
$content .="	</div>";



print(stringForJavascript($content));