<?php
// force une action en travaux si on n'est pas en zone ecrire ni admin

if(($tr_prive = strpos($_SERVER["PHP_SELF"],'/ecrire')) !== false) {
	// prive : les admins passent, les redac passent si '!Tous'
	$tr_acces = cout_autoriser() || !defined('_en_travaux_PRIVE');
} else {
	// public : les admins passent si 'SaufAdmin'
	$tr_acces = cout_autoriser() && defined('_en_travaux_ADMIN');
	// tentative pour prendre en compte les autres cas possibles d'exception
	$tr_acces |=
		isset($_GET['action']) || isset($_POST['action'])
	//	|| ($_POST['formulaire_action']=='login') // TODO : formulaire SPIP 2.0
		|| in_array($_GET['page'], array('login',
			'style_prive',       // filtrage de la feuille de style admin mise en squelette
			'style_prive_ie'))   // idem IE
		|| (strpos($_GET['page'],'.js') !== false) // filtrage de jquery.js par exemple qui sert pour la partie admin
		|| (strpos($_GET['page'],'.css') !== false); // on sait jamais...
}

// si aucune exception, on bloque le site pour travaux
if (!$tr_acces) {
	define('_en_travaux_MESSAGE', $tr_message);
	if($tr_prive) {
		// les actions ne fonctionnent pas ici
		action_cs_travaux(true);
		exit;
	}
	$_GET['action'] = "cs_travaux";
}

// nettoyage
unset($tr_acces, $tr_prive, $tr_message);

function action_cs_travaux($prive=false){
	include_spip('public/assembler');
//	echo recuperer_fond('fonds/en_travaux'.(defined('_SPIP19300')?'2':''), array(
	echo recuperer_fond('fonds/en_travaux', array(
		'titre'=>defined('_en_travaux_TITRE')?_T('info_travaux_titre'):$GLOBALS['meta']['nom_site'],
		// SPIP 2.0 : suppression pour l'instant de la possibilite de se logger directement pour un admin
		// car les redacteurs pourraient acceder qd meme au site (1 seule page, mais 1 page de trop)
		// 'login'=>defined('_en_travaux_ADMIN')?'oui':'',
		'form_login'=>defined('_SPIP19300')?'non':(defined('_en_travaux_ADMIN') || $prive?'oui':'non'),
		'prive'=>$prive?'oui':'non',
	));
	return true;
}
?>