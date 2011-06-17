<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

function action_supprimer_zone_groupe_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();
	$redirect=_request('redirect');
	if ($id_zone = intval($arg)
	 AND autoriser('supprimer','zone',$id_zone)) {
		$id_rubrique=sql_getfetsel("id_rubrique","spip_zones_rubriques", "id_zone=".sql_quote(intval($id_zone)));
		$id_parent=sql_getfetsel("id_parent","spip_rubriques", "id_rubrique=".sql_quote(intval($id_rubrique)));
	 	include_spip('action/editer_zone');
	 	
	 	accesrestreint_supprime_zone($id_zone);
		// Supprimer la rubrique attachee (si vide)
		if (tester_rubrique_zone_vide($id_rubrique)) {
			sql_delete ('spip_rubriques', 'id_rubrique='.sql_quote(intval($id_rubrique)));
		} else {
			$err="La rubrique ".$id_rubrique." n'est pas vide. Elle ne peut être supprimée. Tout le contenu de cette rubrique sera maintenant visible par tout visiteur.";
			$ret=array("mess"=>$err);
    return $ret;
		}
	}
}
// http://doc.spip.org/@tester_rubrique_vide
function tester_rubrique_zone_vide($id_rubrique) {
	if (sql_countsel('spip_rubriques', "id_parent=$id_rubrique"))
		return false;

	if (sql_countsel('spip_articles', "id_rubrique=$id_rubrique AND (statut='publie' OR statut='prepa' OR statut='prop')"))
		return false;

	if (sql_countsel('spip_breves', "id_rubrique=$id_rubrique AND (statut='publie' OR statut='prop')"))
		return false;

	if (sql_countsel('spip_syndic', "id_rubrique=$id_rubrique AND (statut='publie' OR statut='prop')"))
		return false;

	if (sql_countsel('spip_documents_liens', "id_objet=".intval($id_rubrique)." AND objet='rubrique'"))
		return false;

	return true;
}

?>