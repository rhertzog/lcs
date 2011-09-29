<?php

/*
 * Param�tre de configuration sp�cifique � gepi :
 * 'portal_return_url' => 'mon url', //adresse de redirection lorsque l'on quitte gepi. Si cette url est pr�sente, on affiche un lien retour au portail et non un lien d�connection
 * 'do_source_logout' => true, //true par d�faut, Sur une identification distante sans single logout, il faut �viter et mettre false pour ne pas d�connecter le portail a tord
*/
$config = array(

	/*authentification admin saml en utilisant un profil administrateur gepi
	'admin' => array(
		'gepiauth:LocalDB',
		'required_statut' => 'administrateur',
		'name' => array(
		    'fr' => 'Administration simplesaml',
		),
	),*/

	//authentification gepi
	'Authentification locale gepi' => array(
		'gepiauth:LocalDB',
	),
    
	// authentification e-lyco SSO Nantes : 
	'Authentification cas e-lyco' => array(
		'gepicas:GepiCAS',
		'cas' => array(
		    'login' => 'https://cas.e-lyco.fr/login',
		    'validate' => 'https://cas.e-lyco.fr/validate',
		    'logout' => 'https://cas.e-lyco.fr/saml/Logout',
		),
		'search_table' => array(
		    'name' => 'sso_table_correspondance',
		    'cas_uid_column' => 'login_sso',
		    'gepi_login_column' => 'login_gepi',
		    'champ_cas_uid_retour' => 'username'
		),
		'portal_return_url' => 'http://cas.e-lyco.fr',
		'do_source_logout' => false,
	),


	//choix d'authentification entre utilisateur local et utilisateur cas e-lyco
	'Authentification au choix locale ou cas e-lyco' => array(
		'multiauth:MultiAuth',
		'sources' => array('Authentification locale gepi', 'Authentification cas e-lyco')
	),
    
	/*authentification en s'appuyant sur un gepi-maitre distant (� d�commenter sur le gepi esclave)
	'Authentification sur un serveur gepi distant' => array(
		'saml:SP',
		'idp' => 'gepi-idp',
		'entityID' => 'gepi-esclave-sp',
		//ce param�tre doit correspondre avec l' entityID dans le fichier simplesaml/metadata/saml20-sp-remote.php du fournisseur d'identit� (gepi maitre)
		'portal_return_url' => 'http://www.mon-serveur-esclave-gepi.fr',
		'do_source_logout' => false,
	),
	'Authentification au choix locale ou sur serveur gepi distant' => array(
		'multiauth:MultiAuth',
		'sources' => array('Authentification locale gepi', 'Authentification sur un serveur gepi distant')
	),
	*/
	
);

//configuration d'un choix multiple avec toutes les sources configur�es
$sources_array = array_keys($config);
if (!empty($sources_array)) {
	//la source d�finie ci dessous est utilis� par la classe SimpleSAML_Auth_GepiSimple dans les cas d'erreur de configuration de choix de source
	$config['Authentification au choix entre toutes les sources configurees'] = array('multiauth:MultiAuth', 'sources' => $sources_array);
}
