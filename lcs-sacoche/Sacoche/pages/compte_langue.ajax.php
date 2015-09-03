<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2009-2015
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

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if($_SESSION['SESAMATH_ID']==ID_DEMO) {exit('Action désactivée pour la démo...');}

$langue = (isset($_POST['f_langue'])) ? Clean::texte($_POST['f_langue']) : NULL;

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Mettre à jour son choix de langue
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($langue)
{
  // Vérifications
  if($langue=='defaut')
  {
    $langue = '';
  }
  elseif(!is_dir(LOCALE_DIR.DS.$langue))
  {
    exit('Erreur : dossier de langue "'.$langue.'" non trouvé !');
  }
  // C'est ok...
  DB_STRUCTURE_COMMUN::DB_modifier_user_parametre( $_SESSION['USER_ID'] , 'user_langue' , $langue );
  // On modifie aussi la session
  $_SESSION['USER_LANGUE'] = $langue ;
  // sans oublier le menu
  Lang::setlocale( LC_MESSAGES, Lang::get_locale_used() );
  SessionUser::memoriser_menu();
  // Retour
  exit('ok');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// On ne devrait pas en arriver là !
// ////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');

?>
