<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010-2014
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre Affero GPL 3 <https://www.gnu.org/licenses/agpl-3.0.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU Affero General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Publique Générale GNU Affero pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Publique Générale GNU Affero avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

// Mettre à jour l'élément de formulaire "f_niveau" et le renvoyer en HTML

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if($_SESSION['SESAMATH_ID']==ID_DEMO) {}

$niveau_famille_id = (isset($_POST['f_famille_niveau'])) ? Clean::entier($_POST['f_famille_niveau']) : 0;

if(!$niveau_famille_id)
{
  exit('Erreur avec les données transmises !');
}

exit( Form::afficher_select(DB_STRUCTURE_COMMUN::DB_OPT_niveaux_famille($niveau_famille_id) , FALSE /*select_nom*/ , 'niveaux_famille' /*option_first*/ , FALSE /*selection*/ , '' /*optgroup*/ ) );

?>
