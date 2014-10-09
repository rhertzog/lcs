<?php
/*
parserss.php
v 0.1.0

Syntaxe d'utilisation :
{{parserss url="http://www.thierrybazzanella.com/rss/test.xml" tagbegin="<item" tagend="</item>" link="<link>" title="<title>" other="<dc:date>"}}

Copyright (c) 2004; frog-man <frog-man@phpsecure.info>, Bazzanella <bazzanella@free.fr>

All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
if (!function_exists("unhtmlentities")) {
	function unhtmlentities ($string) 
	{
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr ($string, $trans_tbl);
	}
};

if (!function_exists("parseRSS"))
{
	function parseRSS($backendURL,$limit,$englobantdebut,$englobantfin,$donnee1,$donnee2,$donnee3) {
	//SE: Affiche donnee1 ,donnee2, donnee3 
	// donnee1 est une balise choisie. Par exemple <title>
	// donnee2 est une balise choisie. Par exemple <link>
	// donnee3 est une balise choisie. Par exemple <date>
	// englobantdebut et fin est une balise englobante. Par exemple <item> et </item>
	// La fonction affichera le contenu des balises donnee1 et donnee2 et optionnellement donnee3,
	// à la condition que donnee1,donnee2,donnee3 soit situées à l'intérieur de la balise englobante.
	// limit est le nombre de couple donnee1/donnee2
	// backendURL est l'url de l'interface xml.
	
	$englobant_debut = $englobantdebut;
	$englobant_fin = $englobantfin;
	$donnee1_debut = $donnee1;
	$donnee1_fin = "</".substr ($donnee1,1);
	$donnee2_debut = $donnee2;
	$donnee2_fin = "</".substr ($donnee2,1);
	if (!empty($donnee3)) {
		$donnee3_debut = $donnee3;
		$donnee3_fin = "</".substr ($donnee3,1);
	};
	
	 $file = fopen( $backendURL, "r");
	 if( $file ) {
		$raw = fread( $file, 32000 );
		fclose( $file );
		
		$param = $englobant_debut."(.*)".$englobant_fin;
		if( eregi($param, $raw, $rawitems ) ) {
			$items = explode($englobant_debut, $rawitems[0]);
			$nb = count($items );
			$max = (($nb-1) < $limit) ? ($nb-1) : $limit;
			
			$param1=$donnee1_debut."(.*)".$donnee1_fin;
			$param2=$donnee2_debut."(.*)".$donnee2_fin;
			if (!empty($donnee3)) {
				$param3=$donnee3_debut."(.*)".$donnee3_fin;
				};
			
			for( $i = 0; $i < $max; $i++ ) {
				eregi($param1,$items[$i+1], $d1 );
				eregi($param2,$items[$i+1], $d2 );
				if (!empty($donnee3)) {
					eregi($param3,$items[$i+1], $d3 );
					};
				
				if (!empty ($donnee3)) {
					echo "<a href=\"".$d1[1]."\" target=\"_blank\"><b>".stripslashes(unhtmlentities($d2[1]))."</b></a> [<i>".stripslashes(unhtmlentities($d3[1]))."</i>]<br/>";
				} else {
					echo "<a href=\"".$d1[1]."\" target=\"_blank\"><b>".stripslashes(unhtmlentities($d2[1]))."</b></a><br/>";
				}
			}
		}
	 }
	}
};

// récuperation des parametres :
$parseUrl = $this->GetParameter("url");
$parseLimit = $this->GetParameter("limit");
$parseEnglobantDebut = $this->GetParameter("tagbegin");
$parseEnglobantFin = $this->GetParameter("tagend");
$parseDonnee1 = $this->GetParameter("link");
$parseDonnee2 = $this->GetParameter("title");
$parseDonnee3 = $this->GetParameter("other");

// Vérifications des paramètres :
$ok = true;
if (empty($parseUrl)) {
	echo $this->Format("//Le paramètre \"url\" est manquant.//");
	$ok = false;
	};
	
if (empty($parseLimit)) {
	$parseLimit = 10;
	};
	
if (empty($parseEnglobantDebut)) {
	echo $this->Format("//Le paramètre \"tagbegin\" est manquant.//");
	$ok = false;
	};
	
if (empty($parseEnglobantFin)) {
	echo $this->Format("//Le paramètre \"tagend\" est manquant.//");
	$ok = false;
	};
	
if (empty($parseDonnee1)) {
	echo $this->Format("//Le paramètre \"link\" est manquant.//");
	$ok = false;
	};
	
if (empty($parseDonnee2)) {
	echo $this->Format("//Le paramètre \"title\" est manquant.//");
	$ok = false;
	};
	
if ($ok == true) {
		parseRSS($parseUrl,$parseLimit,$parseEnglobantDebut,$parseEnglobantFin,$parseDonnee1,$parseDonnee2,$parseDonnee3);
	};
?>
