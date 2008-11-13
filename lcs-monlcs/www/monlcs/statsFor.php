<?php
include("includes/secure_no_header.inc.php");

if ($_GET) {
extract($_GET);
$buffer = "<br /><center><img src=mkGraph.php?ress=$ress&tab=$tab /></center>";
print(stringForJavascript($buffer));
}

?>