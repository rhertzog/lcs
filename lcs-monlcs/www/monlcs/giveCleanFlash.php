<?php
if ($_GET)
	extract($_GET);

$foo = "flash";
	
$content = "<HTML>"
."<HEAD>"
."<TITLE>mon_lcs</TITLE>"
."</HEAD>"
."<BODY style=\"background-color: #D3D3D3;\";>"
."<OBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
."codebase=\"http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0\""
."ID=$foo WIDTH=100% HEIGHT=100%>"
."<PARAM NAME=movie VALUE=\"".$url."\"> <PARAM NAME=quality VALUE=high> <PARAM NAME=scale VALUE=exactfit> <PARAM NAME=wmode VALUE=transparent>"
." <EMBED src=\"".$url."\" quality=high scale=exactfit wmode=transparent WIDTH=100% HEIGHT=100% TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\"></EMBED>"
."</OBJECT>"
."</BODY>"
."</HTML>";

	
echo $content;
?>