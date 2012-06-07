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

$methode    = (isset($_POST['f_methode'])) ? clean_texte($_POST['f_methode'])  : '';
$matiere_id = (isset($_POST['f_matiere'])) ? clean_entier($_POST['f_matiere']) : 0;
$niveau_id  = (isset($_POST['f_niveau']))  ? clean_entier($_POST['f_niveau'])  : 0;

//	////////////////////////////////////////////////////////////////////////////////////////////////////
//	Modifier le mode de synthèse d'un référentiel
//	////////////////////////////////////////////////////////////////////////////////////////////////////

if( in_array($methode,array('inconnu','sans','domaine','theme')) && $matiere_id && $niveau_id )
{
	DB_STRUCTURE_REFERENTIEL::DB_modifier_referentiel( $matiere_id , $niveau_id , array(':mode_synthese'=>$methode) );
	exit('ok');
}

//	////////////////////////////////////////////////////////////////////////////////////////////////////
//	On ne devrait pas en arriver là
//	////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');

?>