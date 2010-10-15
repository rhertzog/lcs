<?php
if ($_GET)
	extract($_GET);
//$url = "http://www.chaufferdanslanoirceur.org/";
$content ="<div style=\"height: 215px; width:250px;" 
."background:url('http://www.thumbalizr.com/api/?url=$url&width=300') "
."top center no-repeat;\">"
."</div>";
echo $content;
?>