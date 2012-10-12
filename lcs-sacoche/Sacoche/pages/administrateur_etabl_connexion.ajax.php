<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Générale Publique GNU pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if($_SESSION['SESAMATH_ID']==ID_DEMO) {exit('Action désactivée pour la démo...');}

$f_connexion_mode = (isset($_POST['f_connexion_mode'])) ? Clean::texte($_POST['f_connexion_mode'])  : '';
$f_connexion_ref  = (isset($_POST['f_connexion_ref']))  ? Clean::texte($_POST['f_connexion_ref'])   : '';
$cas_serveur_host = (isset($_POST['cas_serveur_host'])) ? Clean::texte($_POST['cas_serveur_host'])  : '';
$cas_serveur_port = (isset($_POST['cas_serveur_port'])) ? Clean::entier($_POST['cas_serveur_port']) : 0;
$cas_serveur_root = (isset($_POST['cas_serveur_root'])) ? Clean::texte($_POST['cas_serveur_root'])  : '';
$gepi_saml_url    = (isset($_POST['gepi_saml_url']))    ? Clean::texte($_POST['gepi_saml_url'])     : '';
$gepi_saml_rne    = (isset($_POST['gepi_saml_rne']))    ? Clean::uai($_POST['gepi_saml_rne'])       : '';
$gepi_saml_certif = (isset($_POST['gepi_saml_certif'])) ? Clean::texte($_POST['gepi_saml_certif'])  : '';

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Mode de connexion (normal, SSO...)
// ////////////////////////////////////////////////////////////////////////////////////////////////////

require(CHEMIN_DOSSIER_INCLUDE.'tableau_sso.php');

if(!isset($tab_connexion_info[$f_connexion_mode][$f_connexion_ref]))
{
	exit('Erreur avec les données transmises !');
}

list($f_connexion_departement,$f_connexion_nom) = explode('|',$f_connexion_ref);

if($f_connexion_mode=='normal')
{
	DB_STRUCTURE_COMMUN::DB_modifier_parametres( array('connexion_mode'=>$f_connexion_mode,'connexion_nom'=>$f_connexion_nom,'connexion_departement'=>$f_connexion_departement) );
	// ne pas oublier de mettre aussi à jour la session (normalement faudrait pas car connecté avec l'ancien mode, mais sinon pb d'initalisation du formulaire)
	$_SESSION['CONNEXION_MODE']        = $f_connexion_mode;
	$_SESSION['CONNEXION_NOM']         = $f_connexion_nom;
	$_SESSION['CONNEXION_DEPARTEMENT'] = $f_connexion_departement;
	exit('ok');
}

if($f_connexion_mode=='cas')
{
	// Vérifier les paramètres CAS en reprenant le code de phpCAS
	if ( empty($cas_serveur_host) || !preg_match('/[\.\d\-abcdefghijklmnopqrstuvwxyz]*/',$cas_serveur_host) )
	{
		exit('Syntaxe du domaine incorrecte !');
	}
	if ( ($cas_serveur_port == 0) || !is_int($cas_serveur_port) )
	{
		exit('Numéro du port incorrect !');
	}
	if ( !preg_match('/[\.\d\-_abcdefghijklmnopqrstuvwxyz\/]*/',$cas_serveur_root) )
	{
		exit('Syntaxe du chemin incorrect !');
	}
	// C'est ok
	DB_STRUCTURE_COMMUN::DB_modifier_parametres( array('connexion_mode'=>$f_connexion_mode,'connexion_nom'=>$f_connexion_nom,'connexion_departement'=>$f_connexion_departement,'cas_serveur_host'=>$cas_serveur_host,'cas_serveur_port'=>$cas_serveur_port,'cas_serveur_root'=>$cas_serveur_root) );
	// ne pas oublier de mettre aussi à jour la session (normalement faudrait pas car connecté avec l'ancien mode, mais sinon pb d'initalisation du formulaire)
	$_SESSION['CONNEXION_MODE']        = $f_connexion_mode;
	$_SESSION['CONNEXION_NOM']         = $f_connexion_nom;
	$_SESSION['CONNEXION_DEPARTEMENT'] = $f_connexion_departement;
	$_SESSION['CAS_SERVEUR_HOST'] = $cas_serveur_host;
	$_SESSION['CAS_SERVEUR_PORT'] = $cas_serveur_port;
	$_SESSION['CAS_SERVEUR_ROOT'] = $cas_serveur_root;
	exit('ok');
}

if($f_connexion_mode=='gepi')
{
	// Vérifier les paramètres GEPI-SAML
	// Le RNE n'étant pas obligatoire, et pas forcément un vrai RNE dans Gepi (pour les établ sans UAI, c'est un identifiant choisi...), on ne vérifie rien.
	// Pas de vérif particulière de l'empreinte du certificat non plus, ne sachant pas s'il peut y avoir plusieurs formats.
	// Donc on va se contenter de vraiment vérifier l'URL de Gepi via une requête cURL
	if(strlen($gepi_saml_url)<8)
	{
		exit('Adresse de GEPI manquante !');
	}
	if(empty($gepi_saml_certif))
	{
		exit('Signature (empreinte du certificat) manquante !');
	}
	$gepi_saml_url = (substr($gepi_saml_url,-1)=='/') ? substr($gepi_saml_url,0,-1) : $gepi_saml_url ;
	$fichier_distant = url_get_contents($gepi_saml_url.'/bandeau.css'); // Le mieux serait d'appeler le fichier du web-services... si un jour il y en a un...
	if(substr($fichier_distant,0,6)=='Erreur')
	{
		exit('Adresse de Gepi incorrecte [ '.$fichier_distant.' ]');
	}
	// C'est ok
	DB_STRUCTURE_COMMUN::DB_modifier_parametres( array('connexion_mode'=>$f_connexion_mode,'connexion_nom'=>$f_connexion_nom,'connexion_departement'=>$f_connexion_departement,'gepi_url'=>$gepi_saml_url,'gepi_rne'=>$gepi_saml_rne,'gepi_certificat_empreinte'=>$gepi_saml_certif) );
	// ne pas oublier de mettre aussi à jour la session (normalement faudrait pas car connecté avec l'ancien mode, mais sinon pb d'initalisation du formulaire)
	$_SESSION['CONNEXION_MODE']        = $f_connexion_mode;
	$_SESSION['CONNEXION_NOM']         = $f_connexion_nom;
	$_SESSION['CONNEXION_DEPARTEMENT'] = $f_connexion_departement;
	$_SESSION['GEPI_URL'] = $gepi_saml_url;
	$_SESSION['GEPI_RNE'] = $gepi_saml_rne;
	$_SESSION['GEPI_CERTIFICAT_EMPREINTE'] = $gepi_saml_certif;
	exit('ok');
}

?>
