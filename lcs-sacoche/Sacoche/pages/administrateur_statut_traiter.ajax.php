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
if($_SESSION['SESAMATH_ID']==ID_DEMO){exit('Action désactivée pour la démo...');}

$action = (isset($_GET['action']))  ? $_GET['action']  : '';
$profil = (isset($_POST['profil'])) ? $_POST['profil'] : '';
$tab_select_users = (isset($_POST['select_users'])) ? array_map('clean_entier',explode(',',$_POST['select_users'])) : array() ;

function positif($n) {return $n;}
$tab_select_users = array_filter($tab_select_users,'positif');
$nb = count($tab_select_users);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Réintégrer des comptes élèves
//	Réintégrer des comptes professeurs et/ou directeurs
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

if( ($action=='reintegrer') && $nb )
{
	foreach($tab_select_users as $user_id)
	{
		// Mettre à jour l'enregistrement
		DB_STRUCTURE_modifier_utilisateur( $user_id , array(':statut'=>1) );
	}
	$s = ($nb>1) ? 's' : '';
	if($profil=='eleves')
	{
		exit('OK'.$nb.' élève'.$s.' réintégré'.$s.'.');
	}
	else
	{
		exit('OK'.$nb.' professeur'.$s.' et/ou directeur'.$s.' réintégré'.$s.'.');
	}
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Supprimer des comptes élèves
//	Supprimer des comptes professeurs et/ou directeurs
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

if( ($action=='supprimer') && $nb )
{
	foreach($tab_select_users as $user_id)
	{
		// Mettre à jour l'enregistrement
		$param_profil = ($profil=='eleves') ? 'eleve' : 'professeur' ; // On transmet 'professeur' y compris pour les directeurs.
		DB_STRUCTURE_supprimer_utilisateur($user_id,$param_profil);
	}
	$s = ($nb>1) ? 's' : '';
	if($profil=='eleves')
	{
		exit('OK'.$nb.' élève'.$s.' supprimé'.$s.'.');
	}
	else
	{
		exit('OK'.$nb.' professeur'.$s.' et/ou directeur'.$s.' supprimé'.$s.'.');
	}
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	On ne devrait pas en arriver là...
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

exit('Erreur avec les données transmises !');

?>