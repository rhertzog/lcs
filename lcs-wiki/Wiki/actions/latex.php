<?php
/* 
titre: Action mathlatex pour Wikini
Auteur: Goëau Hervé d'après Clément Seifert
License: GPL
*/

if (!defined("WIKINI_VERSION"))
 {
         die ("accès direct interdit");
 }
 
$expression = $this->GetParameter("expression");
$baseurl = "http://math.spip.org/tex.php?";
$fullurl = $baseurl . rawurlencode($expression);

echo "<img src=\"$fullurl\" alt=\"expression mathlatex\" valign=\"middle\"/>";

?> 