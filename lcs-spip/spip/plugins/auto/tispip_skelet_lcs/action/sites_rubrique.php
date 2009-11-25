<?php

function action_sites_rubrique() {
    global $visiteur_session;
    
    $id_auteur = $visiteur_session['id_auteur'];
    $arg = _request('arg');
    $args = explode(":",$arg);
	
    // le 1er element de _request('arg') est id_article=XXX
    $Targs = explode("=", $args[0]);
    $id_rubrique = $Targs[1];
    $hash = _request('hash');
    
    $redirect = _request('redirect');
	  if ($redirect==NULL) $redirect="";
//    echo 'tispipskelet_conf/rubriques/afficher_site_'.$id_rubrique."<br />";
//    echo _request('afficher_site_'.$id_rubrique)."<br />";
    include_spip("inc/securiser_action");
    ecrire_config('tispipskelet_conf/rubriques/afficher_site_'.$id_rubrique, _request('afficher_site_'.$id_rubrique));
    
 
    
	// aller sur la page de l'article qui vient d'etre cree
    redirige_par_entete(parametre_url(str_replace("&amp;","&",urldecode($redirect)),'id_rubrique',$id_rubrique,'&'));
}
?>