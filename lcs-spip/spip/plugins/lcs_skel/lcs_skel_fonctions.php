<?php
// Le filtre [(#URL_SITE_SPIP|url_itunes)] ou  [(#URL_SITE|url_itunes)] 
// Transforme les url type http en url itunes (itpc)
function url_itunes($texte) {
	$texte = ereg_replace("http://","itpc://",$texte);
	return $texte;
}

//extrait du plugin TITRE_PARENT par bb, James
//http://www.spip-contrib.net/balise-TITRE-PARENT
// Le filtre [(#ID_RUBRIQUE|titre_parent)]
function titre_parent($id_rubrique) {
	$fetch = function_exists('sql_fetch') ? 'sql_fetch' : 'spip_fetch_array';
	if(!($id_rubrique = intval($id_rubrique))) return '';
	$q = 'SELECT titre FROM spip_rubriques WHERE id_rubrique='.$id_rubrique;
	if($r = spip_query($q))
		if($row = $fetch($r))
			return $row['titre'];
	return '';
}
// Le filtre [(#TEXTE|class_incrementer)] 
// Transforme les url type http en url itunes (itpc)
function class_incrementer($texte) {
	$texte = ereg_replace('ul class="spip"','ul class="incremental"',$texte);
	
	return $texte;
}
// Le filtre [(#TEXTE|class_incrementer)] 
// Transforme les url type http en url itunes (itpc)
function img_backend($texte) {
	$texte = ereg_replace("'","&rsquo;",$texte);
	$texte = ereg_replace('"',"&quot;",$texte);
	$texte = ereg_replace('<',"&lt;",$texte);
	$texte = ereg_replace('>',"&gt;",$texte);
	$texte = ereg_replace('local',"http://".$_SERVER["SERVER_NAME"]."/local",$texte);
	
	return $texte;
}

// Le filtre [(#TEXTE|aff_object)] 
// force l'affichage des <object> dans s5 (class="layout")
function aff_object($texte) {
	$texte = ereg_replace("<dl class='spip_document_5 spip_documents spip_documents_center' >","<ul class='incremental'><li>",$texte);
	$texte = ereg_replace("<dt class='spip_doc_titre'>",'',$texte);
	$texte = ereg_replace('</dt>','',$texte);
	$texte = ereg_replace('</dl>','</li></ul>',$texte);
	return $texte;
}
// Array html2rgb(string color)
// Ce code convertit les couleurs HTML (cod�es en hexa), en RGB :
function RGBtoHexa($color)
{
  // gestion du #...
  if (substr($color,0,1)=="#") $color=substr($color,1,6);

  $tablo[0] = hexdec(substr($color, 0, 2));
  $tablo[1] = hexdec(substr($color, 2, 2));
  $tablo[2] = hexdec(substr($color, 4, 2));
  $color=$tablo[0].",".$tablo[1].",".$tablo[2]; 
  return $color;
}
/*
 * Fonction prenom_nom()
 *
 * Auteur : Fran�ois Schreuer <francois@schreuer.org>
 * http://francois.schreuer.org/
 *
 * Copyright : GNU Public Licence
 *
 * Si le nom ET le pr�nom sont pr�sents, on les renvoie concat�n�s et
 * s�par�s par un espace ins�cable, le nom �tant pass� en majuscules
 *
 * Dans le cas contraire (soit dans le cas o� au moins des deux �l�ments
 * est vide), on renvoie les deux d'un coup (et celui qui n'est pas vide
 * sera affich�). Et s'ils sont tous les deux vides, on renverra du vide,
 * comme il est de bon ton dans ce genre de situation.
 * 
 */
function prenom_nom($texte) {
	if(strstr(ereg_replace("(@-|@-|@ |@|#-|#_|# |#)","",$texte),"*")) {
		if(prenom($texte) && nom($texte))
			return prenom($texte)."&nbsp;".majuscules(nom($texte));
		else
			return prenom($texte).nom($texte);
	}
	else
		return $texte;
}

/*
 * Fonction prenom_nom()
 * 
 * Auteur : Fran�ois Schreuer <francois@schreuer.org>
 * http://francois.schreuer.org/
 * 
 * Copyright : GNU Public Licence
 * 
 * Cette fonction :
 * - enl�ve le signe distinctif des secr�taires de r�daction;
 * - renvoie le pr�nom apr�s l'avoir pass� en minuscules et 
 *   avoir pass� l'initiale en majuscules.
 * 
 */
function prenom($texte) {
	$texte = ereg_replace("(@-|@-|@ |@|#-|#_|# |#)","",$texte); // On commence par virer le signe distinctif d'un secr�taire de r�daction ou celui d'un traducteur
	if(strstr($texte,"*")) {
		if($prenom = trim(ereg_replace("(.*)\*(.*)","\\2",$texte))) {
			return harmonise_noms($prenom);
		}
		else {
			if(substr($texte,0,1) == "*")
				return harmonise_noms($texte);
			else
				return "";
		}
	}
	else
		return "";
}

/*
 * Fonction nom()
 * 
 * Auteur : Fran�ois Schreuer <francois@schreuer.org>
 * http://francois.schreuer.org/
 * 
 * Copyright : GNU Public Licence
 * 
 * Cette fonction :
 * - enl�ve le signe distinctif des secr�taires de r�daction;
 * - renvoie le nom apr�s l'avoir pass� en minuscules et 
 *   avoir pass� l'initiale en majuscules
 * 
 */
function nom($texte) {
	$texte = ereg_replace("(@-|@-|@ |@|#-|#_|# |#)","",$texte); // On commence par virer le signe distinctif des secr�taires de r�daction
	if(strstr($texte,"*")) {
		if($nom = trim(ereg_replace("(.*)\*(.*)","\\1",$texte))) {
			return harmonise_noms($nom);
		}
		else {
			if(substr($texte,0,1) == "*")
				return "";
			else
				return harmonise_noms($texte);
		}
	}
	else {
		return $texte;
	}
}

/*
 * Fonction harmonise_noms()
 * 
 * Auteur : Fran�ois Schreuer <francois@schreuer.org>
 * 
 * Copyright : GNU Public Licence
 * 
 * Harmonise le format de l'affichage des noms.
 * 
 * Vous pouvez changer facilement le mod�le qui vous convient
 * en activant la ligne ad�quate.
 * 
 */
function harmonise_noms($texte) {

	// Passe tout en minuscule et met les initiales en majuscules
	//return ucwords_amelioree(strtolower(trim(str_replace("*"," ",str_replace("_"," ",$texte)))));

	// Ne fait rien
	 return trim($texte);

	// Passe tout en majuscules (avec la fonction idoine de SPIP)
	// return majuscules(trim($texte));

	// Met les initiales en majuscules
	// return ucwords_amelioree(trim($texte));

}


/*
 * Fonction ucwords_amelioree()
 *
 * Auteur : Fran�ois Schreuer <francois@schreuer.org>
 *
 * Copyright : GNU Public Licence
 *
 * Transforme la premi�re lettre de chaque mot (et de chaque
 * partie d'un mot compos�) d'une cha�ne en majuscule. Convertit
 * aussi les caract�res accentu�s.
 *
 * Par exemple, "jean-�dern hallier" devient "Jean-�dern Hallier"
 *
 */
function ucwords_amelioree($texte) {

	// On commence par les mots qui suivent un espace
	$tableau_1 = explode(" ",$texte);
    for($i=0;$i<sizeof($tableau_1);$i++) {
		$tableau_1[$i] = ucfirst_fr($tableau_1[$i]); }
	$texte = implode(" ",$tableau_1);
	
	// puis un espace ins�cable
	$tableau_2 = explode("&nbsp;",$texte);
    for($i=0;$i<sizeof($tableau_2);$i++) {
		$tableau_2[$i] = ucfirst_fr($tableau_2[$i]); }
	$texte = implode("&nbsp;",$tableau_2);

	// enfin un tiret
	$tableau_3 = explode("-",$texte);
    for($i=0;$i<sizeof($tableau_3);$i++) {
		$tableau_3[$i] = ucfirst_fr($tableau_3[$i]); }
	$texte = implode("-",$tableau_3);

	// Et on renvoie le tout
	return $texte;
}

/*
 * Fonction ucfirst_fr()
 *
 * Auteur : Fran�ois Schreuer <francois@schreuer.org>
 *
 * Copyright : GNU Public Licence
 *
 * Transforme la premi�re lettre d'une cha�ne en majuscule
 * en traitant aussi les caract�res accentu�s. Il s'agit
 * donc d'une version am�lior�e de ucfirst_fr()
 *
 * NB : Cette fonction a besoin de la fonction majuscules()
 * de SPIP
 *
 */
function ucfirst_fr($chaine) {
	return majuscules(substr($chaine,0,1)).substr($chaine,1);
}

/*
 * Autre �criture possible pour ucwords_amelioree() (nettement
 * plus jolie mais il faut encore impl�menter dedans le
 * traitement des caract�res fran�ais) :

function ucwords_amelioree($texte) {
����return ucwords(preg_replace_callback('`(\w+)(-)(\w+)`','mot_compose',$texte));
}

����// Sous-fonction de la pr�c�dente
����function mot_compose($match){
��������return $match[1].$match[2].ucfirst($match[3]);
����}

 *
 */
 
 /*
 Filtre |retour_rss
 A voir si utile et a tester
 remplace les curli &8217; par un apostrophe
 */
 function retour_rss($texte) {
	$texte = str_replace("&8217;","'",$texte);
	return $texte;
}
?>