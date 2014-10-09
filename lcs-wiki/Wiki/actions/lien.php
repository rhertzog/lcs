<?php
/*
        lien action
        {{lien url="http://lien.com" texte="Texte du lien" titre="Titre du lien" image="http://lienversimage"}}
*/
if (empty($vars['url'])) echo "Pas de crit&egrave;re url!";
else {
$url = htmlspecialchars($vars['url'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");
$text = htmlspecialchars($vars['texte'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");

$image = htmlspecialchars($vars['image'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");
$title = htmlspecialchars($vars['titre'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1"); 

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