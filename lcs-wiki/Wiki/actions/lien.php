<?php
/*
        lien action
        {{lien url="http://lien.com" texte="Texte du lien" titre="Titre du lien" image="http://lienversimage"}}
*/
if (empty($vars['url'])) echo "Pas de crit&egrave;re url!";
else {
$url = htmlspecialchars($vars['url']);
$text = htmlspecialchars($vars['texte']);

$image = htmlspecialchars($vars['image']);
$title = htmlspecialchars($vars['titre']); 

if (!preg_match("/:\/\//", $url))
	{
		$url = "http://".$url;	//Very important for xss (avoid javascript:() hacking)
	}
if (empty($text)) $text = $url;
if (!preg_match("/:\/\//", $image))
	{
		$image = "http://".$image;
	}
if (!empty($vars['image'])) $text = "<img src=\"$image\" alt=\"$text\" />";


echo '<a href="' . $url . '" title="' . $title . '">' . $text . '</a>';
}

?>