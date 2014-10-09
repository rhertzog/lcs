<?php
/*
        lien action box
        {{box url="http://lien.com" texte="Texte du lien" titre="Titre du lien"}}


Copyright 2009   Pierre Lachance 

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


*/


if (empty($vars['url'])) echo "Pas de crit&egrave;re url!";

else {
	if ($_SESSION['box']=="oui") {

		$urlmst = $this->config[url_site];


		if ($vars['large']=="") { $large="800";} else { $large = htmlspecialchars($vars['large'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");}
		$url = htmlspecialchars($vars['url'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");
		$text = htmlspecialchars($vars['texte'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");

		$title = htmlspecialchars($vars['titre'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1"); 

		if (!preg_match("/:\/\//", $url))
			{
				$url = "http://".$url;	//Very important for xss (avoid javascript:() hacking)
			}
		if (empty($text)) $text = $url;
		if (empty($large)) $large = "800";

		echo "<a  params=\"lightwindow_width=$large\" class=\"lightwindow\"  href=\"$url\" title=\"$title\">$text</a>";
		
		
		
	}
	else { $_SESSION['box'] = "oui";
		$urlmst = $this->config[url_site];


		if ($vars['large']=="") { $large="800";} else { $large = htmlspecialchars($vars['large'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");}
		$url = htmlspecialchars($vars['url'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");
		$text = htmlspecialchars($vars['texte'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");

		$title = htmlspecialchars($vars['titre'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1"); 

		if (!preg_match("/:\/\//", $url))
			{
				$url = "http://".$url;	//Very important for xss (avoid javascript:() hacking)
			}
		if (empty($text)) $text = $url;
		if (empty($large)) $large = "800";
				echo "
		<script type=\"text/javascript\" src=\"".$urlmst."lightwindow/javascript/prototype.js\"></script>
		<script type=\"text/javascript\" src=\"".$urlmst."lightwindow/javascript/scriptaculous.js?load=effects\"></script>
		<script type=\"text/javascript\" src=\"".$urlmst."lightwindow/javascript/lightwindow.js\"></script>
		<link rel=\"stylesheet\" href=\"".$urlmst."lightwindow/css/lightwindow.css\" type=\"text/css\" media=\"screen\" />";
		echo "<a  params=\"lightwindow_width=$large\" class=\"lightwindow\"  href=\"$url\" title=\"$title\">$text</a>";
		
	}
}

?>



