<?php


	/**
	 * SPIP-Météo : prévisions météo dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


 	include_spip('inc/presentation');
 	include_spip('meteo_fonctions');
	include_spip('inc/headers');


	/**
	 * exec_meteo_tous
	 *
	 * Tableau de bord du plugin
	 *
	 * @author Pierre Basson
	 **/
	function exec_meteo_tous() {
  		global $connect_statut, $connect_toutes_rubriques;

		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'meteo_tous'),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('meteo:meteo'), "naviguer", "meteo_tous");

		debut_gauche();

		debut_raccourcis();
		icone_horizontale(_T('meteo:ajouter_une_meteo'), generer_url_ecrire("meteo_edit","new=oui"), '../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png', 'creer.gif');
		fin_raccourcis();

    	debut_droite();
		echo meteo_afficher_meteos(_T('meteo:liste_des_meteos'), array("FROM" => 'spip_meteo', 'ORDER BY' => "ville"));

		echo fin_gauche();

		echo fin_page();

	}


?>