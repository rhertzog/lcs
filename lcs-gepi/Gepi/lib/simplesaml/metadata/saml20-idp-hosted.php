<?php
/**
 * SAML 2.0 IdP configuration for simpleSAMLphp.
 *
 * See: https://rnd.feide.no/content/idp-hosted-metadata-reference
 */

//__DYNAMIC:1__ reprÃ©sente l'entityID du fournisseur d'identitÃ©. Il est remplacÃ© par l'url du serveur si c'est spÃ©cifiÃ© dynamic.
// si pour une configuration manuelle un autre entityID est prÃ©cisÃ©, il doit correspondre Ã  l'entityID
// prÃ©cisÃ© dans le fichier simplesaml/metadate/saml20-idp-remote.php du fournisseur de service (sacoche)
if (getSettingValue('gepiEnableIdpSaml20') == 'yes') {
	$metadata['gepi-idp'] = array(
		/*
		 * The hostname of the server (VHOST) that will use this SAML entity.
		 *
		 * Can be '__DEFAULT__', to use this entry by default.
		 */
		'host' => '__DEFAULT__',

		/* X.509 key and certificate. Relative to the cert directory. */
		'privatekey' => 'server.pem',
		'certificate' => 'server.crt',

		/*
		 * Authentication source to use. Must be one that is configured in
		 * 'config/authsources.php'.
		 */
		'auth' => getSettingValue('auth_simpleSAML_source'),//on utilise la source configurée par défaut dans l'admin gepi

		/* Uncomment the following to use the uri NameFormat on attributes. */
		'AttributeNameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri',
	
		'logouttype' => 'iframe',
	
	);
	if ($metadata['gepi-idp']['auth'] == null || $metadata['gepi-idp']['auth'] == 'unset') {
		$metadata['gepi-idp']['auth'] = 'Authentification au choix entre toutes les sources configurees';
	}
}

