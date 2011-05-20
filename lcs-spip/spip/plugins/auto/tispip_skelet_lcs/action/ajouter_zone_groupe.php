<?php
/**
 * Plugin pour Spip 2.0
 * Licence GPL (c) 2006-2008 (d0M0.b) 
 *
 */
function action_ajouter_zone_groupe_dist(){
#	$securiser_action = charger_fonction('securiser_action','inc');
#	$arg = $securiser_action();
	$mess_ok='';
	$err='';
    $redirect = _request('redirect');
	  if ($redirect==NULL) $redirect="peupler_zone_lcs";

	if (_request('arg')) {
		$v_zones = sql_fetsel('*','spip_zones', 'titre='.sql_quote(_request('arg')));
		if($v_zones['titre']) {
			$err="La zone" ._request('group')." existe deja";	
			   redirige_par_entete(parametre_url(str_replace("&amp;","&",urldecode($redirect)),'message',$err,'&'));
		}
		
		// nouvel zone
		$id_zone = sql_insertq("spip_zones", array("maj"=>"NOW()", "titre"=>_request('arg'), "descriptif"=>'', 'publique'=>'oui','privee'=>'oui'));

		//on cree une rubrique du meme non que le groupe et on la place dans la zone creee
		$prefixes=array('Equipe'=>'Equipes','Classe'=>'Classes','Cours'=>'Cours','Matiere'=>'Matières');
		// On regarde s'il esiste une rubrique de meme nom que la zone creee
		if(!$rub_exist= sql_fetsel("*","spip_rubriques", "titre=".sql_quote(_request('arg')))){
#		if(!$rub_exist= sql_getfetsel("id_rubrique","spip_rubriques", "titre=".sql_quote(_request('arg')))){
			include_spip('inc/rubriques');
			$id_parent=0;
			//pour les groupes secondaires Equipe, Classe, Matiere et Cours
			// on cree une rubrique (Equipes, Classes, Matieres et Cours) a la racine
			// et on place les rubriques crees dedans
#			$prefixes=array('Equipe'=>'Equipes','Classe'=>'Classes','Cours'=>'Cours','Matiere'=>'Matières');
			foreach($prefixes as $prefix=>$nom_rub) {
				if(preg_match("/".$prefix."/",_request('arg'))) {
					$rub_parent=$nom_rub;
		 			if ($id_rubrique= sql_getfetsel("id_rubrique","spip_rubriques", "titre=".sql_quote($rub_parent))) {
		 				 $id_parent=$id_rubrique;
		 			}
		 			else{
		 				$id_rubrique = sql_insertq("spip_rubriques", array(
							'titre' => $rub_parent,
							id_parent => 0,
							'statut' => 'new'));
							$id_parent=$id_rubrique;
						propager_les_secteurs();
						calculer_langues_rubriques();
		 			}
				}
			}
	
			// pour le groupe principal, on crée a la racine
			//	Pour les groupes secondaires, on regarde si la rubrique parent existe
			// sinon on la cree
		 	if ($id_rubrique= sql_getfetsel("id_rubrique","spip_rubriques", "titre=".sql_quote($rub_parent))) $id_parent=$id_rubrique;
#			$row_rub_parent['id_rubrique'] ? $id_parent=$row_rub_parent['id_rubrique'] : $id_parent=0;
			$id_rubrique = sql_insertq("spip_rubriques", array(
				'titre' => _request('arg'),
				id_parent => intval($id_parent),
				'statut' => 'new'));
			propager_les_secteurs();
			calculer_langues_rubriques();
			
			// on place la rubrique creee	et la rubrique p	arent ds la zone de meme nom
			$ids['id_zone']=$id_zone;
			$ids['id_rubrique']=$id_rubrique;
			$zone_rubrique = sql_insertq("spip_zones_rubriques", $ids,'',$serveur='connect',$option=true);
			$zone_parent = sql_insertq("spip_zones_rubriques", array("id_zone"=>$id_zone,"id_rubrique"=>$id_parent),'',$serveur='connect',$option=true);
		 			
		 	// pour le groupe Equipe on donne le droit a la zone sur la rubrique classe (si elle existe)
			$classe = preg_replace('/Equipe/', 'Classe', _request('arg'));
			if($id_rub_classe=sql_getfetsel("id_rubrique","spip_rubriques", "titre=".sql_quote($classe))) {
				$zone_rub_classe = sql_insertq("spip_zones_rubriques", array('id_zone'=>$id_zone, 'id_rubrique'=>$id_rub_classe),'',$serveur='connect',$option=true);
			}
		}else{
			$zone_rubrique = sql_insertq("spip_zones_rubriques", array('id_zone'=>$id_zone, 'id_rubrique'=>$rub_exist['id_rubrique']),'',$serveur='connect',$option=true);
			/*
			foreach($prefixes as $prefix=>$nom_rub) {
				if(preg_match("/".$prefix."/",_request('arg')))	$zone_parent = sql_insertq("spip_zones_rubriques", array('id_zone'=>$id_zone, 'id_rubrique'=>$rub_exist['id_parent']),'',$serveur='connect',$option=true);
			}
			$classe = preg_replace('/Equipe/', 'Classe', _request('arg'));
			if($id_rub_classe=sql_getfetsel("id_rubrique","spip_rubriques", "titre=".sql_quote($classe))) {
				$zone_rub_classe = sql_insertq("spip_zones_rubriques", array('id_zone'=>$id_zone, 'id_rubrique'=>$id_rub_classe),'',$serveur='connect',$option=true);
			}
			*/
		}
		
	}
	
//   redirige_par_entete(parametre_url(str_replace("&amp;","&",urldecode($redirect)),'message',$mes,'&'));
}

?>