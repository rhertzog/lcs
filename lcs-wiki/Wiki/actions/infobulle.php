<?php

/*
infobulle.php
{{infobulle mot="mot à infobuller" titre="Titre de la bulle" texte="texte de l'infobulle" lien="http://recitmst.qc.ca/wikinimst/"}}

Copyright 2010  Etienne Roy et Pierre Lachance 


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


$mot = $this->getParameter("mot"); 
$texte = $this->getParameter("texte");
if ($vars['lien']=="") { $lien="#";} else { $lien = htmlspecialchars($vars['lien'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");}
if ($vars['titre']=="") { $titre="";} else { $titre = htmlspecialchars($vars['titre'],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1");}


echo "<a class=\"info_bulle\" href=\"$lien\"> $mot";
echo '<span class="info_bulle">';
echo	"<span class=\"headerinfo\">$titre</span>";
echo	'	<span class="contentinfo">';
echo "$texte";
echo '		</span>	';
echo '		<span class="footerinfo"></span>	';
echo '	</span> ';
echo '</a>';

?> 

