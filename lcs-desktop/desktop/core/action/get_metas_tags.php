<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 14/03/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
header ('Content-type: text/html; charset=utf-8');

	$user = $_POST['user'] ;
	$url = $_POST['url'] ;
	$resp = array();

# on va chercher le fichier
### mettre un timeOut en cas de non réponse
$file = file($url);
$resp['url'] =  htmlentities($url);
$file = implode("",$file);
if(preg_match("/<title>(.+)<\/title>/i",$file,$m))
    $resp['title'] = $m[1];
else
   $resp['error']= utf8_encode("Erreur ! Cette page semble ne pas poss&eacute;der de titre");

// recherche de tous les appels 'RSS Feed'
if (preg_match_all('#<link[^>]+type="application/rss\+xml"[^>]*>#is', $file, $rawMatches)) {
  // extraire l'url de chaque appel
  foreach ($rawMatches[0] as $rawMatch) {
    if (preg_match('#href="([^"]+)"#i', $rawMatch, $rawUrl)) {
      $resp['rss'] = $rawUrl[1];
    }
  }
}

$tags = get_meta_tags($url);

#$resp['author'] = $tags['author'];       // auteur
#$resp['keywords'] = $tags['keywords'];     // mots-cle
#$resp['description'] = $tags['description'];  // description

foreach($tags as $k => $val){
	$resp[$k] =$val;
}

echo json_encode($resp);

?>