<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 14/03/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/


	$user = $_POST['user'] ;
	$url = $_POST['file'] ;
	$n = $_POST['nb_items'] ;
	$resp = '';
$file = file($url);
$file = implode("",$file);
if(preg_match("/<title>(.+)<\/title>/i",$file,$m))
    $resp .= $m[1];
else
   $resp .= "Cette page semble ne pas poss&eacute;der de titre";

// Supposons que les balises ci-dessus sont disponibles sur example.com
$tags = get_meta_tags($url);

// Notez que les clés sont en minuscule, et
// le . a été remplacé par _ dans la clé
$resp .= " |,| auteur : ". htmlspecialchars($tags['author']);       // auteur
$resp .= " |,| motcle : ". htmlspecialchars($tags['keywords']);     // mots-cle
$resp .= " |,| description : ". htmlspecialchars($tags['description']);  // description
/*

foreach($tags as $k => $val){
	$resp .= $k.' => '.$val."\r\n";
}


		echo $resp;
*/
	include("/var/www/lcs/desktop/action/rsslib.php");
	$resp.= "|,|".RSS_Display($url, 5,1,1);
		echo $resp;
?>
