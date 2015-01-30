<?php

function action_docs_article() {
    global $visiteur_session;
    
    $id_auteur = $visiteur_session['id_auteur'];
    $arg = _request('arg');
    $args = explode(":",$arg);
	
    // le 1er element de _request('arg') est id_article=XXX
    $Targs = explode("=", $args[0]);
    $id_article = $Targs[1];
    $hash = _request('hash');
    
    $redirect = _request('redirect');
	  if ($redirect==NULL) $redirect="";
//    echo 'tispipskelet_conf/article/afficher_docs_'.$id_rubrique."<br />";
//    echo _request('afficher_site_'.$id_article)."<br />";

	//ecrire la valeur dans la table meta tispipskelet_conf
    include_spip("inc/securiser_action");
    ecrire_config('tispipskelet_conf/articles/afficher_docs_'.$id_article, _request('afficher_docs_article_'.$id_article));
	// aller sur la page de l'article
    redirige_par_entete(parametre_url(str_replace("&amp;","&",urldecode($redirect)),'id_article',$id_article,'&'));
}
?>