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

$action            = (isset($_POST['f_action']))            ? clean_texte($_POST['f_action'])            : '';
$proxy_used        = (isset($_POST['f_proxy_used']))        ? 'oui'                                      : '';
$proxy_name        = (isset($_POST['f_proxy_name']))        ? clean_texte($_POST['f_proxy_name'])        : '';
$proxy_port        = (isset($_POST['f_proxy_port']))        ? clean_entier($_POST['f_proxy_port'])       : 0;
$proxy_type        = (isset($_POST['f_proxy_type']))        ? clean_texte($_POST['f_proxy_type'])        : '';
$proxy_auth_used   = (isset($_POST['f_proxy_auth_used']))   ? 'oui'                                      : '';
$proxy_auth_method = (isset($_POST['f_proxy_auth_method'])) ? clean_texte($_POST['f_proxy_auth_method']) : '';
$proxy_auth_user   = (isset($_POST['f_proxy_auth_user']))   ? clean_texte($_POST['f_proxy_auth_user'])   : '';
$proxy_auth_pass   = (isset($_POST['f_proxy_auth_pass']))   ? clean_texte($_POST['f_proxy_auth_pass'])   : '';

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
// Tester les réglages actuellement enregistrés
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

if($action=='tester')
{
	$requete_reponse = url_get_contents(SERVEUR_VERSION);
	$affichage = (preg_match('#^[0-9]{4}\-[0-9]{2}\-[0-9]{2}[a-z]?$#',$requete_reponse)) ? '<label class="valide">Échange réussi avec le serveur http://sacoche.sesamath.net</label>' : '<label class="erreur">Échec de l\'échange avec le serveur http://sacoche.sesamath.net &rarr; '.$requete_reponse.'</label>' ;
	exit('<h2>Résultat du test</h2>'.$affichage);
}

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
// Enregistrer des nouveaux réglages
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

fabriquer_fichier_hebergeur_info( array('SERVEUR_PROXY_USED'=>$proxy_used,'SERVEUR_PROXY_NAME'=>$proxy_name,'SERVEUR_PROXY_PORT'=>$proxy_port,'SERVEUR_PROXY_TYPE'=>$proxy_type,'SERVEUR_PROXY_AUTH_USED'=>$proxy_auth_used,'SERVEUR_PROXY_AUTH_METHOD'=>$proxy_auth_method,'SERVEUR_PROXY_AUTH_USER'=>$proxy_auth_user,'SERVEUR_PROXY_AUTH_PASS'=>$proxy_auth_pass) );
exit('ok');

?>
