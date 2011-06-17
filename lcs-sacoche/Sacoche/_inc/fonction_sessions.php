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

// Fonctions pour gérer les sessions.
// La session est transmise via le cookie "$_COOKIE['SESSION_NOM']".

// Paramétrage de la session
define('SESSION_NOM','SACoche-session');
session_name(SESSION_NOM);
session_cache_limiter('nocache');
session_cache_expire(180);

// Ouvrir une session existante
function open_old_session()
{
	$ID = $_COOKIE[SESSION_NOM];
	session_id($ID);
	return session_start();
}

// Créer une nouvelle session
function open_new_session()
{
	$ID = uniqid().md5('grain_de_sable'.mt_rand());	// Utiliser l'option préfixe ou entropie de uniqid() insère un '.' qui peut provoquer une erreur disant que les seuls caractères autorisés sont a-z, A-Z, 0-9 et -
	session_id($ID);
	return session_start();
}

// Initialiser une session existante
function init_session()
{
	$_SESSION = array();
	// Numéro de la base
	$_SESSION['BASE']             = 0;
	// Données associées à l'utilisateur.
	$_SESSION['USER_PROFIL']      = 'public';	// public / webmestre / administrateur / directeur / professeur / eleve
	$_SESSION['USER_ID']          = 0;
	$_SESSION['USER_NOM']         = '-';
	$_SESSION['USER_PRENOM']      = '-';
	// Données associées à l'établissement.
	$_SESSION['SESAMATH_ID']      = 0;
	$_SESSION['CONNEXION_MODE']   = 'normal';
	$_SESSION['DUREE_INACTIVITE'] = 30;
}

// Fermer une session existante
function close_session()
{
	$alerte_sso = ( isset($_SESSION['CONNEXION_MODE']) && ($_SESSION['CONNEXION_MODE']!='normal') &&($_SESSION['USER_PROFIL']!='administrateur') ) ? '&amp;f_base='.$_SESSION['BASE'].'&amp;f_mode='.$_SESSION['CONNEXION_MODE'] : false ;
	$_SESSION = array();
	setcookie(session_name(),'',time()-42000,'/');
	session_destroy();
	return $alerte_sso;
}

// Recherche d'une session existante et gestion des cas possibles.
function gestion_session($TAB_PROFILS_AUTORISES)
{
	global $ALERTE_SSO;
	// Messages d'erreurs possibles
	$tab_msg_alerte = array();
	$tab_msg_alerte[ 'I.2.a']['index'] = 'Session non retrouvée.\nConfigurez votre navigateur pour qu\'il accepte les cookies !\nRedirection vers l\'accueil...';
	$tab_msg_alerte[ 'I.2.a']['ajax']  = 'Session non retrouvée. Vérifiez l\'acceptation des cookies ! Retournez à l\'accueil...';
	$tab_msg_alerte[ 'I.2.b']['index'] = 'Tentative d\'accès direct à une page réservée !\nRedirection vers l\'accueil...';
	$tab_msg_alerte[ 'I.2.b']['ajax']  = 'Session perdue. Déconnectez-vous et reconnectez-vous...';
	$tab_msg_alerte['II.1.a']['index'] = 'Votre session a expiré !\nRedirection vers l\'accueil...';
	$tab_msg_alerte['II.1.a']['ajax']  = 'Session expirée. Déconnectez-vous et reconnectez-vous...';
	$tab_msg_alerte['II.3.a']['index'] = 'Tentative d\'accès direct à une page réservée !\nRedirection vers l\'accueil...';
	$tab_msg_alerte['II.3.a']['ajax']  = 'Page réservée. Retournez à l\'accueil...';
	$tab_msg_alerte['II.4.c']['index'] = 'Tentative d\'accès direct à une page réservée !\nRedirection vers l\'accueil...';
	$tab_msg_alerte['II.4.c']['ajax']  = 'Page réservée. Déconnexion effectuée. Retournez à l\'accueil...';
	// Zyva !
	if(!isset($_COOKIE[SESSION_NOM]))
	{
		// I. Aucune session transmise
		open_new_session(); init_session();
		if(!$TAB_PROFILS_AUTORISES['public'])
		{
			// I.2. Redirection : demande d'accès à une page réservée donc identification avant accès direct
			if(isset($_GET['verif_cookie']))
			{
				// I.2.a. L'utilisateur vient de s'identifier : c'est donc anormal, le cookie de session n'a pas été trouvé car le navigateur client n'enregistre pas les cookies
				alert_redirection_exit($tab_msg_alerte['I.2.a'][SACoche]);
			}
			else
			{
				// I.2.b. Session perdue ou expirée pour une raison diverse (cookies effacés, plusieurs onglets, expiration, ...)
				alert_redirection_exit($tab_msg_alerte['I.2.b'][SACoche]);
			}
		}
	}
	else
	{
		// II. id de session transmis
		open_old_session();
		if(!isset($_SESSION['USER_PROFIL']))
		{
			// II.1. Pas de session retrouvée (sinon cette variable serait renseignée)
			if(!$TAB_PROFILS_AUTORISES['public'])
			{
				// II.1.a. Session perdue ou expirée et demande d'accès à une page réservée : redirection pour une nouvelle identification
				$ALERTE_SSO = close_session(); open_new_session(); init_session();
				alert_redirection_exit($tab_msg_alerte['II.1.a'][SACoche]);
			}
			else
			{
				// II.1.b. Session perdue ou expirée et page publique : création d'une nouvelle session (éventuellement un message d'alerte pour indiquer session perdue ?)
				$ALERTE_SSO = close_session();open_new_session();init_session();
			}
		}
		elseif($_SESSION['USER_PROFIL'] == 'public')
		{
			// II.3. Personne non identifiée
			if(!$TAB_PROFILS_AUTORISES['public'])
			{
				// II.3.a. Espace non identifié => Espace identifié : redirection pour identification
				init_session();
				alert_redirection_exit($tab_msg_alerte['II.3.a'][SACoche]);
			}
			else
			{
				// II.3.b. Espace non identifié => Espace non identifié : RAS
			}
		}
		else
		{
			// II.4. Personne identifiée
			if($TAB_PROFILS_AUTORISES[$_SESSION['USER_PROFIL']])
			{
				// II.4.a. Espace identifié => Espace identifié identique : RAS
			}
			elseif($TAB_PROFILS_AUTORISES['public'])
			{
				// II.4.b. Espace identifié => Espace non identifié : création d'une nouvelle session vierge (éventuellement un message d'alerte pour indiquer session perdue ?)
				if (SACoche!='ajax')
				{
					// Ne pas déconnecter si on appelle le calendrier de l'espace public
					$ALERTE_SSO = close_session();open_new_session();init_session();
				}
			}
			elseif(!$TAB_PROFILS_AUTORISES['public'])
			{
				// II.4.c. Espace identifié => Autre espace identifié incompatible : redirection pour une nouvelle identification
				init_session();
				alert_redirection_exit($tab_msg_alerte['II.4.c'][SACoche]);
			}
		}
	}
}

?>