<?php

function action_tispipskelet_conf_exporte() {
    global $visiteur_session;
    
    $id_auteur = $visiteur_session['id_auteur'];
//    $arg = _request('arg');
//    $args = explode(":",$arg);
	
    // le 1er element de _request('arg') est id_rubrique=XXX
//    $Targs = explode("=", $args[0]);
//    $id_rubrique = $Targs[1];
    $hash = _request('hash');
    
    $redirect = _request('redirect');
	  if ($redirect==NULL) $redirect="cfg?cfg=TiSpiP_Export";
    
    include_spip("inc/securiser_action");
    
//    if (!autoriser('creerarticledans', 'rubrique', $id_rubrique)) die(_T('avis_non_acces_page'));

   $file= addslashes($_FILES['tispip_zip']['name']);
	$pat[0]="/.zip/";$pat[1]="/tispip_/";
	$nom_file= preg_replace($pat, "", $file);
	$mess_ok='';
	// ss-rep temporaire specifique de l'auteur en cours: tmp/tispip/id_auteur/ => le creer si il n'existe pas
    $base_dezip = _DIR_TMP."tispip_".$nom_file."/";   // avec / final
    if (!is_dir($base_dezip)) if (!sous_repertoire(_DIR_TMP,'tispip_'.$nom_file)) die (_T('tispip:err_repertoire_tmp'));  
    $rep_dezip = $base_dezip.$id_auteur.'/';
    if (!is_dir($rep_dezip)) if (!sous_repertoire($base_dezip,$id_auteur)) die (_T('tispip:err_repertoire_tmp'));
    
	if ((isset($_POST['nom_conf'])) && ($_POST['nom_conf']!='') ) {
		$nom_conf = $_POST['nom_conf'];
		if(isset($GLOBALS['meta']['plugin'])) {
			$result = unserialize($GLOBALS['meta']['plugin']);
		
			$text_save="<div class='annonce'>";
// .. Création d'un objet DOM avec ses 2 param's et ajout au document xml à créer
 			$doc = new DomDocument('1.0', 'iso-8859-1');
			$sources = $doc->createElement('tispip');
			$doc->appendChild($sources);
			
			if(isset($result['TISPIPSKELET']['version'])) {
				$r= $result['TISPIPSKELET']['version'];
								// .. On créé l'élément ' param ' et on l'ajoute au document ..
								$src_texte = $doc->createElement('param');
								$sources->appendChild($src_texte);
	
								// .. On créé l'attribut ' nom ' et on l'ajoute au document ..
								$src_texte_attribut = $doc->createAttribute('nom');
								$src_texte->appendChild($src_texte_attribut);
								// .. On ajoute les valeurs d'attributs au document ..
								$src_valeurAttribut = $doc->createTextNode("Nom");
								$src_texte_attribut->appendChild($src_valeurAttribut);
	
								// .. On créé l'attribut ' valeur ' et on l'ajoute au document ..
								$src_texte_attribut = $doc->createAttribute('valeur');
								$src_texte->appendChild($src_texte_attribut);
								// .. On ajoute les valeurs d'attributs au document ..
								$src_valeurAttribut = $doc->createTextNode("$nom_conf");
								$src_texte_attribut->appendChild($src_valeurAttribut);				

								// .. On créé l'élément ' param ' et on l'ajoute au document ..
								$src_texte = $doc->createElement('param');
								$sources->appendChild($src_texte);

								// .. On créé l'attribut ' nom ' et on l'ajoute au document ..
								$src_texte_attribut = $doc->createAttribute('nom');
								$src_texte->appendChild($src_texte_attribut);
								// .. On ajoute les valeurs d'attributs au document ..
								$src_valeurAttribut = $doc->createTextNode("Version");
								$src_texte_attribut->appendChild($src_valeurAttribut);
	
								// .. On créé l'attribut ' valeur ' et on l'ajoute au document ..
								$src_texte_attribut = $doc->createAttribute('valeur');
								$src_texte->appendChild($src_texte_attribut);
								// .. On ajoute les valeurs d'attributs au document ..
								$src_valeurAttribut = $doc->createTextNode("$r");
								$src_texte_attribut->appendChild($src_valeurAttribut);				
			}
			
// .. Définir une matrice : String ..
				$conf_actuelle=lire_config('tispipskelet_conf');
				if ($conf_actuelle) {
					$text_conf_actuelle ="<div>";
					foreach($conf_actuelle as $nom => $val){
						if (is_array($val)){
							ksort($val);
							$enfant= $doc->createElement($nom);
							$sources->appendChild($enfant);
							foreach($val as $nom => $val){
								if($val !=''){
									// .. On créé l'élément ' param ' et on l'ajoute au document ..
									$src_texte = $doc->createElement('param');
									 $enfant->appendChild($src_texte);
		
									// .. On créé l'attribut ' nom ' et on l'ajoute au document ..
									$src_texte_attribut = $doc->createAttribute('nom');
									$src_texte->appendChild($src_texte_attribut);
									// .. On ajoute les valeurs d'attributs au document ..
									$src_valeurAttribut = $doc->createTextNode("$nom");
									$src_texte_attribut->appendChild($src_valeurAttribut);
		
									// .. On créé l'attribut ' valeur ' et on l'ajoute au document ..
									$src_texte_attribut = $doc->createAttribute('valeur');
									$src_texte->appendChild($src_texte_attribut);
									// .. On ajoute les valeurs d'attributs au document ..
									$src_valeurAttribut = $doc->createTextNode("$val");
									$src_texte_attribut->appendChild($src_valeurAttribut);
								}
							}
						}else {
							if($val !=''){
								// .. On créé l'élément ' param ' et on l'ajoute au document ..
								$src_texte = $doc->createElement('param');
								$sources->appendChild($src_texte);
	
								// .. On créé l'attribut ' nom ' et on l'ajoute au document ..
								$src_texte_attribut = $doc->createAttribute('nom');
								$src_texte->appendChild($src_texte_attribut);
								// .. On ajoute les valeurs d'attributs au document ..
								$src_valeurAttribut = $doc->createTextNode("$nom");
								$src_texte_attribut->appendChild($src_valeurAttribut);
	
								// .. On créé l'attribut ' valeur ' et on l'ajoute au document ..
								$src_texte_attribut = $doc->createAttribute('valeur');
								$src_texte->appendChild($src_texte_attribut);
								// .. On ajoute les valeurs d'attributs au document ..
								$src_valeurAttribut = $doc->createTextNode("$val");
								$src_texte_attribut->appendChild($src_valeurAttribut);
							}
						}
					}
					$text_conf_actuelle.="<br style='clear:both;' /></div>";
					
				}

// .. On créé le fichier XML 
	if (!@opendir(_DIR_TMP."tispip")){
		mkdir(_DIR_TMP."tispip", 0777);
	}
	$doc->save(_DIR_TMP."tispip/tispip_".$nom_conf.'.xml');
	$doc->saveXML();
	
	if(file_exists($file=_DIR_TMP."tispip/tispip_".$nom_conf.".xml")){
		$text_save.="Le fichier <strong>tispip_".$nom_conf.".xml</strong> &agrave; été sauvegardé ";
		$text_save.=" et copi&eacute; dans le r&eacute;pertoire <strong>tmp</strong>.<br />";
		
		if (!@opendir(_DIR_IMG."tispip")){
			mkdir(_DIR_IMG."tispip", 0777);
		}
		$newfile=_DIR_IMG."tispip/tispip_".$nom_conf.".xml";
		copy($file,$newfile);
									
		//on copie temporairement dans IMG/config
		$newfile2=_DIR_IMG."config/tispip_".$nom_conf.".xml";
		copy($file,$newfile2);
		file_exists($newfile2) ? $text_save.="xml dans IMG/config/ => OK<br />" : '';
		
		// et on cree le zip
		include_spip("inc/pclzip");
		$file_zip=_DIR_IMG.'tispip/tispip_'.$nom_conf.'.zip';
		$archive = new PclZip($file_zip);
		$v_list = $archive->create(_DIR_IMG.'config/',
                             PCLZIP_OPT_ADD_PATH, $nom_conf,
                             PCLZIP_OPT_REMOVE_PATH, _DIR_IMG
                             );
		if (($list = $archive->listContent()) == 0) {
			die("Error : ".$archive->errorInfo(true));
		}
		$listcontent=array();
		for ($i=0; $i<sizeof($list); $i++) {
			for(reset($list[$i]); $key = key($list[$i]); next($list[$i])) {
      	$listcontent[]= "File $i / [$key] = ".$list[$i][$key]."<br>";
		}
//		$text_save.= $listcontent;
//		$text_save.= "<br />";
  }

		$text_zip.="Vous pouvez t&eacute;l&eacute;charger le fichier zip en cliquant sur <a href=\""._DIR_IMG."tispip/tispip_".$nom_conf.".zip\" title=''>ce lien</a>";
//		on supprime le fichier .xml du dossier config
		unlink($newfile2) ; 
		
		$text_save.=$text_zip;
 				
						
		$text_save.="</div>";
		$mess_ok.= $text_save;
	} else {
		$mess_ok.= "Erreur";
	}
}
}

	// lire le rep des sauvegardes (dans IMG/tispip)
						$lerep =_DIR_IMG."tispip"; 
						$nb=0;
						$list_sauvegarde='';
						    $text_sauvegarde='<div class="annonce">';
//						$texte_sauvegarde.='Vous disposez des sauvegardes suivantes : ';
						 if ($rep = @opendir($lerep))  {
						    while (($file = readdir($rep)) !== false) {
						      if($file != ".." && $file != "." && preg_match("/.zip/i", $file)){
						      	$pat[0]="/.zip/";$pat[1]="/tispip_/";
					    		  	$nom_file= preg_replace($pat, "", $file);
					    		  	if($nom_file!=''){
								      $list_sauvegarde.= '<a href="'.$lerep.'/'.$file.'">T&eacute;l&eacute;charger <strong>'.$nom_file.'</strong></a><br />';
										$nb++;
										//	$filelist[] = $file;
					    		  	}
						       }
						    } 
						closedir($rep);
						      	$nb==1 ? $text_sauvegarde.='Une sauvegarde est actuellement disponible dans le r&eacute;pertoire IMG/tispip/<br />' : '';
						      	$nb>1 ? $text_sauvegarde.=$nb.' sauvegardes sont actuellement disponibles dans le r&eacute;pertoire IMG/tispip/<br />' : '';
						}
						$list_sauvegarde=='' ? $list_sauvegarde.= 'Aucune sauvegarde n&rsquo;est actuellement disponible dans le r&eacute;pertoire IMG/tispip/<br />' : '';
						$text_sauvegarde.=$list_sauvegarde;
						$text_sauvegarde.='</div>';
							      $mess_ok.= $text_sauvegarde;

	
    if (!function_exists('effacer_repertoire_temporaire')) include_spip('inc/getdocument');
	// vider le contenu du rep de dezippage
 //   effacer_repertoire_temporaire($rep_dezip);
    
	// aller sur la page de l'article qui vient d'etre cree
    redirige_par_entete(parametre_url(str_replace("&amp;","&",urldecode($redirect)),'message',$mess_ok,'&'));
}
?>