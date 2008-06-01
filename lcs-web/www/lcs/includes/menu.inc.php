<?
$liens=array(0);
exec("ls /var/www/lcs/includes/menu.d/*.inc",$files,$return);
for ($i=0; $i< count($files); $i++)
include ($files[$i]);
?>
