<?php

function action_pdf2swf_convert() {
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
    
    include_spip("inc/securiser_action");
    
//    if (!autoriser('creerarticledans', 'rubrique', $id_rubrique)) die(_T('avis_non_acces_page'));

	// ss-rep temporaire specifique de l'auteur en cours: tmp/pdf2swf/id_auteur/ => le creer si il n'existe pas
    $base_dezip = _DIR_TMP."pdf2swf/";   // avec / final
    if (!is_dir($base_dezip)) if (!sous_repertoire(_DIR_TMP,'pdf2swf')) die (_T('pdfswf:err_repertoire_tmp'));  
    $rep_dezip = $base_dezip.$id_auteur.'/';
    if (!is_dir($rep_dezip)) if (!sous_repertoire($base_dezip,$id_auteur)) die (_T('pdfswf:err_repertoire_tmp'));
    
	// traitement d'un fichier pdf envoye par $_POST 
    $fichier_zip = addslashes($_FILES['fichier_pdf']['name']);
    if ($_FILES['fichier_pdf']['name'] == '' 
        OR $_FILES['fichier_pdf']['error'] != 0
        OR !move_uploaded_file($_FILES['fichier_pdf']['tmp_name'], $rep_dezip.$fichier_zip)
       )  die(_T('pdfswf:err_telechargement_fichier'));

   // conversion du fichier pdf en swf
//    $command='pdf2swf -t '.$rep_dezip.$fichier_zip.' '.$rep_dezip.$fichier_zip.'.swf'; 
    $command='pdf2swf -t -B '. _DIR_PLUGIN_PDF2SWF.'fdplayer.swf  '.$rep_dezip.$fichier_zip.' '.$rep_dezip.$fichier_zip.'.swf'; 
	exec($command);
	
	//attacher le fichier pdf original a l'article
        if (!isset($ajouter_documents)) 
        	$ajouter_documents = charger_fonction('ajouter_documents','inc');
        
        // la y'a un bogue super-bizarre avec la fonction spip_abstract_insert() qui est donnee comme absente lors de l'appel de ajouter_document()
        if (!function_exists('spip_abstract_insert')) include_spip('base/abstract_sql');
        $id_doc_swf = $ajouter_documents($rep_dezip.$fichier_zip.'.swf', $fichier_zip.'.swf', "article", $id_article, 'document', 0, $toto='');

 
	// si necessaire attacher le fichier odt original a l'article et lui mettre un titre signifiant
    if (_request('attacher_pdf') == '1') {
        $id_doc_odt = $ajouter_documents($rep_dezip.$fichier_zip, $fichier_zip, "article", $id_article, 'document', 0, $toto='');

    }
    
    if (!function_exists('effacer_repertoire_temporaire')) include_spip('inc/getdocument');
	// vider le contenu du rep de dezippage
    effacer_repertoire_temporaire($rep_dezip);
    
	// aller sur la page de l'article qui vient d'etre cree
    redirige_par_entete(parametre_url(str_replace("&amp;","&",urldecode($redirect)),'id_article',$id_article,'&'));
}
?>