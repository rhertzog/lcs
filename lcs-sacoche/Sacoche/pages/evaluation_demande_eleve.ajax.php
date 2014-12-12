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

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if($_SESSION['SESAMATH_ID']==ID_DEMO) {exit('Action désactivée pour la démo...');}

$action     = (isset($_POST['f_action']))     ? Clean::texte($_POST['f_action'])      : '';
$demande_id = (isset($_POST['f_demande_id'])) ? Clean::entier($_POST['f_demande_id']) : 0;
$item_id    = (isset($_POST['f_item_id']))    ? Clean::entier($_POST['f_item_id'])    : 0;
$matiere_id = (isset($_POST['f_matiere_id'])) ? Clean::entier($_POST['f_matiere_id']) : 0;
$prof_id    = (isset($_POST['f_prof_id']))    ? Clean::entier($_POST['f_prof_id'])    : -1;
$score      = (isset($_POST['score']))        ? Clean::entier($_POST['score'])        : -2; // normalement entier entre 0 et 100 ou -1 si non évalué

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Supprimer une demande
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='supprimer') && $demande_id && $item_id && $matiere_id && ($prof_id!=-1) )
{
  $nb_suppression = DB_STRUCTURE_DEMANDE::DB_supprimer_demande_precise_id($demande_id);
  if(!$nb_suppression)
  {
    exit('La demande transmise a déjà été supprimée !');
  }
  // Récupérer la référence et le nom de l'item
  $DB_ROW = DB_STRUCTURE_DEMANDE::DB_recuperer_item_infos($item_id);
  // Ajout aux flux RSS des profs concernés
  $titre = 'Demande retirée par '.afficher_identite_initiale($_SESSION['USER_NOM'],FALSE,$_SESSION['USER_PRENOM'],TRUE);
  $texte = $_SESSION['USER_PRENOM'].' '.$_SESSION['USER_NOM'].' retire sa demande '.$DB_ROW['item_ref'].' "'.$DB_ROW['item_nom'].'"';
  $guid  = 'demande_'.$demande_id.'_del';
  if($prof_id)
  {
    RSS::modifier_fichier_prof($prof_id,$titre,$texte,$guid);
  }
  else
  {
    // On récupère les profs...
    $DB_TAB = DB_STRUCTURE_DEMANDE::DB_recuperer_professeurs_eleve_matiere( $_SESSION['USER_ID'] , $_SESSION['ELEVE_CLASSE_ID'] , $matiere_id );
    if(!empty($DB_TAB))
    {
      foreach($DB_TAB as $DB_ROW)
      {
        RSS::modifier_fichier_prof($DB_ROW['user_id'],$titre,$texte,$guid);
      }
    }
  }
  // Affichage du retour
  exit('ok');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Actualiser un score
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='actualiser_score') && $demande_id && $item_id && ($score>-2) )
{
  $tab_devoirs = array();
  $DB_TAB = DB_STRUCTURE_DEMANDE::DB_lister_result_eleve_item( $_SESSION['USER_ID'] , $item_id );
  foreach($DB_TAB as $DB_ROW)
  {
    $tab_devoirs[] = array('note'=>$DB_ROW['note']);
  }
  $score_new = (count($tab_devoirs)) ? calculer_score($tab_devoirs,$DB_ROW['calcul_methode'],$DB_ROW['calcul_limite']) : FALSE ;
  if( ( ($score==-1) && ($score_new!==FALSE) ) || ( ($score>-1) && ($score_new!==$score) ) )
  {
    // maj score
    $score_new_bdd = ($score_new!=-1) ? $score_new : NULL ;
    DB_STRUCTURE_DEMANDE::DB_modifier_demande_score( $demande_id , $score_new_bdd );
  }
  $score_retour = str_replace( '</td>' , ' <q class="actualiser" title="Actualiser le score (enregistré lors de la demande)."></q></td>' , Html::td_score( $score_new , 'score' /*methode_tri*/ , '' /*pourcent*/ ) );
  exit($score_retour);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// On ne devrait pas en arriver là...
// ////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');

?>
