<?
include "includes/secure_no_header.inc.php";

$content2 .="<div style=padding: 30px; background-color: #fce;>";
$content2 .= "<form id=form1 name=form1>";
$content2 .= "<textarea id=edit name=edit1>";
$content2 .= "</textarea>";
$content2 .= "</form>";



print(stringForJavascript($content2));
?>
