<?php
/*
        note action
        afficher un lecteur pour video flv avec un lien vers le fichier au lieu de le télécharger dans le wiki.
	synthaxe {{flv url="http://lieuduflv" titre="on peut mettre titre" haut="hauteurduvideo" large="largeurduvideo" class="ouonveutvideo" }}
*/


$urlflv=$vars['url'];
if ($vars['class']=="") { $laclass="nul";} else $laclass="attach_".$vars['class'];
if ($vars['titre']=="") { $titre="";} else $titre=$vars['titre'];
if ($vars['large']=="") { $large="340";} else $large=$vars['large'];
if ($vars['haut']=="") { $haut="260";} else $haut=$vars['haut'];

$urlmst = $this->config[url_site];


	$output =
		"<div class=\"$laclass\">\n".
		"<object type=\"application/x-shockwave-flash\" width=\"$large\" height=\"$haut\" data=\"".$urlmst."player_flv.swf?flv=$urlflv&amp;width=$large&amp;height=$haut&amp;bgcolor1=cccccc&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=66FF33&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;showvolume=1&amp;srt=1&amp;textcolor=0&amp;showstop=1&amp;title=$titre&amp;startimage=preview.jpg\" />\n".
		"<param name=\"movie\" value=\"".$urlmst."player_flv.swf?flv=$urlflv&amp;width=$large&amp;height=$haut&amp;bgcolor1=cccccc&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=66FF33&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;showvolume=1&amp;srt=1&amp;textcolor=0&amp;showstop=1&amp;title=$titre&amp;startimage=preview.jpg\" />\n".
		"<param name=\"wmode\" value=\"transparent\" />\n".
		"</object>\n".
		"<br />\n".
		"<a href=\"$urlflv\" class=\"handout_video\">T&eacute;l&eacute;charger</a>\n";
	
		print($output);
		
		echo "</div>";
?>