<?php
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
session_name("Cdt_Lcs");
@session_start();
$filename = "../json_files/".$_SESSION['login'].".json";
$contents=file_get_contents($filename);
echo $contents;
?>
