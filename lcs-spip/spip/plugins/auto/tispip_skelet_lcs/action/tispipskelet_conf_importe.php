<?php

function action_tispipskelet_conf_importe() {
	global $visiteur_session;
	    
	$id_auteur = $visiteur_session['id_auteur'];
	$arg = _request('arg');
	$args = explode(":",$arg);
		
	// le 1er element de _request('arg') est id_rubrique=XXX
	$Targs = explode("=", $args[0]);
	$id_rubrique = $Targs[1];
	$hash = _request('hash');
    
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
    
	include_spip("inc/securiser_action");
//	if (!autoriser('creerarticledans', 'rubrique', $id_rubrique)) die(_T('avis_non_acces_page'));

   $file= addslashes($_FILES['tispip_zip']['name']);
   // on verifie l'extension zip
	$elementsChemin = pathinfo($file);
	$extensionFichier = $elementsChemin['extension'];
	if ($extensionFichier!='zip')  die (_T('Le fichier n&rsquo;est pas un fichier .zip'));

	$pat[0]="/.zip/";$pat[1]="/tispip_/";
	$nom_file= preg_replace($pat, "", $file);
	$mess_ok='';
	// ss-rep temporaire specifique de l'auteur en cours: tmp/tispip/id_auteur/ => le creer si il n'existe pas
    $base_dezip = _DIR_TMP."tispip_".$nom_file."/";   // avec / final
    if (!is_dir($base_dezip)) if (!sous_repertoire(_DIR_TMP,'tispip_'.$nom_file)) die (_T('tispip:err_repertoire_tmp'));  
    $rep_dezip = $base_dezip.$id_auteur.'/';
    if (!is_dir($rep_dezip)) if (!sous_repertoire($base_dezip,$id_auteur)) die (_T('tispip:err_repertoire_tmp'));
    
	// traitement d'un fichier zip envoye par $_POST 
    $fichier_zip = addslashes($_FILES['tispip_zip']['name']);
//echo $fichier_zip;
    if ($_FILES['tispip_zip']['name'] == '' 
        OR $_FILES['tispip_zip']['error'] != 0
        OR !move_uploaded_file($_FILES['tispip_zip']['tmp_name'], $rep_dezip.$fichier_zip)
       )  die(_T('odtspip:err_telechargement_fichier'));

  // dezipper le fichier odt a la mode SPIP
	include_spip("inc/pclzip");
	$zip = new PclZip($rep_dezip.$fichier_zip);
	$ok = $zip->extract(
		PCLZIP_OPT_PATH, $rep_dezip,
		PCLZIP_OPT_SET_CHMOD, _SPIP_CHMOD,
		PCLZIP_OPT_REPLACE_NEWER
	);
	  if ($zip->error_code < 0) {
		    spip_log('charger_decompresser erreur zip ' . $zip->error_code .' pour fichier ' . $rep_dezip.$fichier_zip);
		    die($zip->errorName(true));  //$zip->error_code
	  }

		$tispip_rep=preg_replace("/.zip/", "", $file)."/";
		$tispip_rep= _DIR_TMP.$tispip_rep."/IMG/config/";
		
		// on sauve les images de la conf en place => A FAIRE

	// et on copie les images de la nouvelle conf dans ./IMG/config/
	  $cpimg="cp -r -b "._DIR_TMP."tispip_".$nom_file."/".$id_auteur."/".$nom_file."/config/tispip_* "._DIR_IMG."config";
	  exec($cpimg);

	// on recupere le contenu du xml
	if(file_exists($filename=_DIR_TMP."tispip_".$nom_file."/".$id_auteur."/".$nom_file."/config/tispip_".$nom_file.".xml")){
		$nom_restor=$nom_file;
		$xml = simplexml_load_file($filename);
		$mess_valeurs='';
		
		$nb_params=$p_cnt = count($xml->param);
		for($i = 0; $i < $p_cnt; $i++) {
			foreach($xml->param[$i]->attributes() as $a => $b) {
				$a=='nom' ? $nom=$b : $valeur=$b;
			}
			ecrire_config('tispipskelet_conf/'.$nom, "$valeur");
			$nom=='Nom' ? $nom_import=$valeur : '';
			$tn=lire_config('tispipskelet_conf/'.$nom);
			$mess_valeurs.= $nom." : enregistr&eacute; | Valeur =>".$tn."<br />";
		}
		foreach($xml ->children() as $casiers => $casier) {
			// on ne recupere que les couleurs, la conf generale, les background et l'entete
			$casiers_recup=array('entete','couleurs','upload','css');
			if ($casiers !='param'){
				$p_cnt = count($xml->$casiers->param);
				$nb_params =$nb_params+$p_cnt;
				$mess_valeurs.= "<strong style=\"clear:both;\">".$casiers." : </strong> | ".$p_cnt."<br />";

				for($i = 0; $i < $p_cnt; $i++) {
					foreach($xml->$casiers->param[$i]->attributes() as $a => $b) {
						$a=='nom' ? $nom=$b : $valeur=$b;
					}
						$mess_valeurs.= $nom." | ".$valeur."<br />";
					ecrire_config('tispipskelet_conf/'.$casiers.'/'.$nom, "$valeur");
				}
//						$mess_ok.= " <br />";

			}
		}

		$mess_ok.="La configuration <strong>".$nom_import."</strong> est restaurée. (<i>".$nb_params." param&egrave;tres enregistr&eacute;s</i>).<br /> N&rsquo;oubliez pas de <strong>vider le cache</strong> ou de <strong>recalculer la page</strong> pour visualiser la nouvelle configuration graphique.";
		
//		$mess_ok.=$mess_valeurs;
		
	}else{
		$mess_ok.= "Pfeuuuu.... Y a po de fichier xml dans "._DIR_TMP."tispip_".$nom_file."/".$id_auteur."/".$nom_file."/config/tispip_".$nom_file.".xml";
		$mess_ok.= "il devrait etre la ->/tmp/tispip_raspoutine/1/raspoutine/config";
		
	}
	
    if (!function_exists('effacer_repertoire_temporaire')) include_spip('inc/getdocument');
	// vider le contenu du rep de dezippage
 //   effacer_repertoire_temporaire($rep_dezip);
    
	// aller sur la page de l'article qui vient d'etre cree
    redirige_par_entete(parametre_url(str_replace("&amp;","&",urldecode($redirect)),'message',$mess_ok,'&'));
}
?>