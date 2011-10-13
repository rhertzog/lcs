<?php

//include only that one, rest required files will be included from it
include "qrlib.php";
//QRcode::png('wawa is a chief', 'filename.png'); // creates file 
QRcode::png(str_replace("*amp*", "&", $_GET['qrurl'])); // creates code image and outputs it directly into browser

?>
