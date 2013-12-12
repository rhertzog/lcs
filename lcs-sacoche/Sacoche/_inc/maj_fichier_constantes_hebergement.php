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

$tab_constantes_manquantes = array();

// A compter du 05/12/2010, ajout de paramètres dans le fichier de constantes pour paramétrer cURL. [TODO] peut être retiré dans un an environ
if(!defined('SERVEUR_PROXY_USED'))
{
  $tab_constantes_manquantes += array('SERVEUR_PROXY_USED'=>'','SERVEUR_PROXY_NAME'=>'','SERVEUR_PROXY_PORT'=>'','SERVEUR_PROXY_TYPE'=>'','SERVEUR_PROXY_AUTH_USED'=>'','SERVEUR_PROXY_AUTH_METHOD'=>'','SERVEUR_PROXY_AUTH_USER'=>'','SERVEUR_PROXY_AUTH_PASS'=>'');
}

// A compter du 26/05/2011, ajout de paramètres dans le fichier de constantes pour les dates CNIL. [TODO] peut être retiré dans un an environ
if(!defined('CNIL_NUMERO'))
{
  $tab_constantes_manquantes += array('CNIL_NUMERO'=>HEBERGEUR_CNIL,'CNIL_DATE_ENGAGEMENT'=>'','CNIL_DATE_RECEPISSE'=>'');
}

// A compter du 14/03/2012, ajout de paramètres dans le fichier de constantes pour les fichiers associés aux devoirs. [TODO] peut être retiré dans un an environ
if(!defined('FICHIER_DUREE_CONSERVATION'))
{
  $tab_constantes_manquantes += array('FICHIER_TAILLE_MAX'=>500,'FICHIER_DUREE_CONSERVATION'=>12);
}

// A compter du 18/10/2012, ajout de paramètre dans le fichier de constantes pour le chemin des logs phpCAS. [TODO] peut être retiré dans un an environ
if(!defined('CHEMIN_LOGS_PHPCAS'))
{
  $tab_constantes_manquantes += array('CHEMIN_LOGS_PHPCAS'=>CHEMIN_DOSSIER_TMP);
  $ancien_fichier = CHEMIN_DOSSIER_TMP.'debugcas_'.md5($_SERVER['DOCUMENT_ROOT']).'.txt';
  FileSystem::supprimer_fichier( $ancien_fichier , TRUE /*verif_exist*/ );
}

// A compter du 17/10/2012, ajout de paramètre dans le fichier de constantes pour fixer un niveau de restriction de droits d'accès (CHMOD). [TODO] peut être retiré dans un an environ
if(!defined('SYSTEME_UMASK'))
{
  $tab_constantes_manquantes += array('SYSTEME_UMASK'=>'000');
}

// A compter du 30/11/2013, ajout de paramètre dans le fichier de constantes pour indiquer si les admins peuvent modifier les coordonnées de la personne contact (multi-structures). [TODO] peut être retiré dans un an environ
if(!defined('CONTACT_MODIFICATION_USER'))
{
  $tab_constantes_manquantes += array('CONTACT_MODIFICATION_USER'=>'non','CONTACT_MODIFICATION_MAIL'=>'non');
}

// Application patch si besoin
if(count($tab_constantes_manquantes))
{
  FileSystem::fabriquer_fichier_hebergeur_info($tab_constantes_manquantes);
}

?>
