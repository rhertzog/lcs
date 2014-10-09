<?php
/*
        note action
       Afficher un fichier mp3 dans le lecteur xspf.swf à partir d'une url ( pas besoin de télécharger le mp3 sur le wiki).
        Syntax: {{mp3 url="http://lieudump3" titre="on peut mettre un titre" class="ouonveutlelecteur"}}
*/


$urlmp3=$vars['url'];
$titremp3=$vars['titre'];
$urlmst = $this->config[url_site];
if ($vars['class']=="") { $laclass="nul";} else $laclass="attach_".$vars['class'];

		$output =
		
		"<div class=\"$laclass\"><object type=\"application/x-shockwave-flash\" data=\"".$urlmst."player_mp3_maxi.swf\" width=\"250\" height=\"35\">
		<param name=\"movie\" value=\"".$urlmst."player_mp3_maxi.swf\" />
		<param name=\"bgcolor\" value=\"#ffffff\" />
		<param name=\"FlashVars\" value=\"mp3=$urlmp3&amp;showstop=1&amp;showinfo=1&amp;showvolume=1&amp;showloading=always\" />
		</object>\n".
	
		"<br />\n".
		"<a href=\"$urlmp3\">T&eacute;l&eacute;charger $titremp3</a>\n";
		
		print($output);

		echo "</div>";


?>