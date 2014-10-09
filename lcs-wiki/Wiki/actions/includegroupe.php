<?php
/*
include.php : Permet d'inclure une page Wiki dans un autre page

Copyright 2003  Eric FELDSTEIN
Copyright 2003  Charles NEPOTE
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

/* Paramètres :
 -- page : nom wiki de la page a inclure (obligatoire)
 -- class : nom de la classe de style à inclure (facultatif)
*/ 


// TODO : voir si on peut evite une variable globale
global $PageLocalisation;	//tableau (pile) indiquant si la page est locale ou distante
if (!isset($PageLocalisation)) {
	$PageLocalisation = array();
}

// récuperation du parametres
$incPage = array();	//la page courante : tableau associatif avec les cle 'name' et 'isLocal'
$incPage['name'] = $this->GetParameter("page");
$lienpage=$incPage['name'];
// TODO : améliorer le traitement des classes
if ($this->GetParameter("class")) {
 	$classes='';
	$array_classes = explode(" ", $this->GetParameter("class"));
	foreach ($array_classes as $c) { $classes = $classes . "include_" . $c . " "; }
}

// Affichage de la page ou d'un message d'erreur
if (empty($incPage['name'])) {
	echo $this->Format("//Le paramètre \"page\" est manquant.//");
} else {
	//determination de la localisation de la page
	if (preg_match("/^http:(.*)$/",$incPage['name'])||preg_match("/^https:(.*)$/",$incPage['name'])){
		//si le nom commence par "http:" => distante
		$incPage['isLocal'] = FALSE;
	}else{
		//le nom de la page n'est pas http://xxxx => la localisation de la page
		// courante = localisation de la page precedente
		if (count($PageLocalisation)==0){
			$incPage['isLocal']=TRUE;
		}else{
			$incPage['isLocal'] = $PageLocalisation[count($PageLocalisation)-1]['isLocal'];
		}
		if ($incPage['isLocal']==FALSE){
			//si la page est distante => calcul de l'URL complete par rapport a l'URL
			//de la page precedente. La page courante etant sur le meme serveur que
			//la page precedente !!
			$aURL = parse_url($PageLocalisation[count($PageLocalisation)-1]['name']);
			$PageUrl = $aURL['scheme'].'://';
			if (!empty($aURL['username'])){
				$PageUrl .=	$aURL['username'].':'.$aURL['password'].'@';
			}
			$PageUrl .= $aURL['host'].((empty($aURL['port'])?'':':'.$aURL['port']));
			$PageUrl .= $aURL['path'].'?wiki='.$incPage['name'];
			$incPage['name'] = $PageUrl;
		}
	}

	//recherche une reference circulaire
	$canInclude = True;
	foreach ($PageLocalisation as $aPage){
		if ($aPage['name']==$incPage['name']){
			$canInclude = False;
		}
	}
	if ($canInclude){
		array_push($PageLocalisation,$incPage);	//on sauve le fait que la page est local/distante
		if ($incPage['isLocal']){	//si c'est une page local
			//test le droit de lecture avant affichage
			if (!$this->HasAccess("read",$incPage['name'])){
				$output = "";
			}else{
				$incPage = $this->LoadPage($incPage['name']);
				$output = $this->Format($incPage["body"]);
			}
		}else{	//si c'est une page distante
			$incPage = implode(file($incPage['name']."/raw"));
			$output = $this->Format($incPage);			
		}
		array_pop($PageLocalisation);
		if (count($PageLocalisation)==0) unset($PageLocalisation);	//supprime le tableau global alloué
	}else{
		$output = $this->Format("//Référence circulaire de la page //".$incPage['name']);
	}
	if ($classes)
	{

	echo "<div class=\"", $classes,"\">\n", $output, "</div>\n";
	}
	else 
	{

	echo $output;	
	}	
}

?>
