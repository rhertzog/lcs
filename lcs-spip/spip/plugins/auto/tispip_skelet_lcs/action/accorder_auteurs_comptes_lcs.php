<?php
/**
 * Plugin TiSpiP-Lcs pour Spip 2.0
 * Licence GPL (c) 2006-2008 (d0M0.b) 
 *
 */
function action_accorder_auteurs_comptes_lcs_dist(){
#	$securiser_action = charger_fonction('securiser_action','inc');
#	$arg = $securiser_action();
	$mess_ok='';
	$err='';
    $redirect = _request('redirect');
	  if ($redirect==NULL) $redirect="?exec=accorder_comptes_lcs";

	if (_request('arg')) {
						sql_updateq('spip_auteurs', array('statut' => '5poubelle'),
						'id_auteur='.intval($args));

		if(is_array(_request('tous'))){
			foreach(_request('tous') as $k=>$val){
							sql_updateq('spip_auteurs', array('statut' => '5poubelle'),
							'id_auteur='.intval($val));
			}
		}
/*
		if(_request('newstatut') && _request('newstatut')=="supprime"){
			sql_delete ('spip_auteurs', 'id_auteur='.sql_quote(intval($args)));
		}
*/
	}
}

?>