<?php
include("mathpublisher.php") ;
$message="<m> y=ax^2+bx+c </m>";
$size='11';
$pathtoimg='img';
if (!isset($pathtoimg)) $pathtoimg="img/";
if ((!isset($size)) || $size<10) $size=14;
if ( isset($message) && $message!='' ) 
	{
	echo(mathfilter($message,$size,$pathtoimg));
	}
?>