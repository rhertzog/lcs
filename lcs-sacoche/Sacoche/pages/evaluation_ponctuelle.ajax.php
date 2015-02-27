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

/*
 *   /!\ Cette page est aussi appelée par le script [evaluation_demande_professeur.js]
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if($_SESSION['SESAMATH_ID']==ID_DEMO) {exit('Action désactivée pour la démo...');}

$action    = (isset($_POST['f_action'])) ? Clean::texte($_POST['f_action'])  : NULL;
$item_id   = (isset($_POST['f_item']))   ? Clean::entier($_POST['f_item'])   : NULL;
$eleve_id  = (isset($_POST['f_eleve']))  ? Clean::entier($_POST['f_eleve'])  : NULL;
$note_val  = (isset($_POST['f_note']))   ? Clean::texte($_POST['f_note'])    : NULL;
$devoir_id = (isset($_POST['f_devoir'])) ? Clean::entier($_POST['f_devoir']) : NULL;
$groupe_id = (isset($_POST['f_groupe'])) ? Clean::entier($_POST['f_groupe']) : NULL;

$tab_notes = array( 'RR' , 'R' , 'V' , 'VV' , 'NN' , 'NE' , 'NF' , 'NR' , 'ABS' , 'DISP' , 'REQ' , 'X' );

if( ($action=='enregistrer_note') && $item_id && $eleve_id && in_array($note_val,$tab_notes) && ($devoir_id!==NULL) && ($groupe_id!==NULL) )
{
  // Nom du devoir
  $tab_jour = array(
    0 => 'dimanche',
    1 => 'lundi',
    2 => 'mardi',
    3 => 'mercredi',
    4 => 'jeudi',
    5 => 'vendredi',
    6 => 'samedi',
  );
  $tab_mois = array(
     1 => 'janvier',
     2 => 'février',
     3 => 'mars',
     4 => 'avril',
     5 => 'mai',
     6 => 'juin',
     7 => 'juillet',
     8 => 'août',
     9 => 'septembre',
    10 => 'octobre',
    11 => 'novembre',
    12 => 'décembre',
  );
  $description = 'Évaluation ponctuelle du '.$tab_jour[date("w")].' '.date("j").' '.$tab_mois[date("n")].' '.date("Y").'.';
  // On cherche le devoir correspondant.
  $presence_devoir = FALSE;
  if( ($devoir_id) && ($groupe_id) && DB_STRUCTURE_PROFESSEUR::DB_tester_devoir_ponctuel_prof_by_ids( $devoir_id , $_SESSION['USER_ID'] ,  $groupe_id ) )
  {
    $presence_devoir = TRUE;
  }
  else
  {
    //  Si absence d'identifiants transmis, alors soit le devoir n'existe pas, soit il existe et c'est la 1ère saisie d'une série
    $DB_ROW = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_ponctuel_prof_by_date( $_SESSION['USER_ID'] ,  TODAY_MYSQL , $description );
    if(!empty($DB_ROW))
    {
      $presence_devoir = TRUE;
      $devoir_id = $DB_ROW['devoir_id'];
      $groupe_id = $DB_ROW['groupe_id'];
    }
  }
  // Si pas de devoir, il faut l'ajouter
  if(!$presence_devoir)
  {
    // Commencer par créer un nouveau groupe de type "eval", utilisé uniquement pour cette évaluation (c'est transparent pour le professeur) ; y associe automatiquement le prof, en responsable du groupe
    $groupe_id = DB_STRUCTURE_PROFESSEUR::DB_ajouter_groupe_par_prof( 'eval' /*groupe_type*/ , '' /*groupe_nom*/ , 0 /*niveau_id*/ );
    // Insèrer l'enregistrement de l'évaluation
    $devoir_id = DB_STRUCTURE_PROFESSEUR::DB_ajouter_devoir( $_SESSION['USER_ID'] , $groupe_id , TODAY_MYSQL , $description , TODAY_MYSQL /*date_visible_mysql*/ ,NULL /*date_autoeval_mysql*/ , '' /*doc_sujet*/ , '' /*doc_corrige*/ , 'alpha' /*eleves_ordre*/ );
  }
  // Maintenant on recupère le contenu de la base déjà enregistré pour le comparer avec la saisie envoyée.
  $presence_item   = FALSE;
  $presence_eleve  = FALSE;
  $presence_saisie = FALSE;
  $DB_TAB = ($presence_devoir) ? DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_saisies( $devoir_id , TRUE /*with_REQ*/ ) : array() ;
  foreach($DB_TAB as $DB_ROW)
  {
    if($DB_ROW['item_id']==$item_id)
    {
      $presence_item = TRUE ;
    }
    if($DB_ROW['eleve_id']==$eleve_id)
    {
      $presence_eleve = TRUE ;
    }
    if( ($DB_ROW['item_id']==$item_id) && ($DB_ROW['eleve_id']==$eleve_id) )
    {
      $presence_saisie = $DB_ROW['saisie_note'] ;
      break; // Pas besoin de tester davantage, on sort du foreach()
    }
  }
  // On enregistre les modifications.
  $info = $description.' ('.afficher_identite_initiale($_SESSION['USER_NOM'],FALSE,$_SESSION['USER_PRENOM'],TRUE,$_SESSION['USER_GENRE']).')';
  if(!$presence_item)
  {
    // 'ajouter' plutôt que 'creer' car en cas d'ajout puis de suppression d'une note à un élève, un item peut se retrouver déjà affecté à un devoir sans qu'il n'y ait de note trouvée
    DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_item( $devoir_id , array($item_id) , 'ajouter' );
  }
  if(!$presence_eleve)
  {
    // 'ajouter' plutôt que 'creer' car en cas d'ajout puis de suppression d'une note à un élève, un élève peut se retrouver déjà affecté à un devoir sans qu'il n'y ait de note trouvée
    DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_eleve( $devoir_id , $groupe_id , array($eleve_id) , 'ajouter' );
  }
  if($presence_saisie==FALSE)
  {
    if($note_val!='X')
    {
      DB_STRUCTURE_PROFESSEUR::DB_ajouter_saisie( $_SESSION['USER_ID'] , $eleve_id , $devoir_id , $item_id , TODAY_MYSQL , $note_val , $info , TODAY_MYSQL );
    }
  }
  else
  {
    if($note_val=='X')
    {
      DB_STRUCTURE_PROFESSEUR::DB_supprimer_saisie( $eleve_id , $devoir_id , $item_id );
    }
    elseif($presence_saisie!=$note_val)
    {
      DB_STRUCTURE_PROFESSEUR::DB_modifier_saisie( $_SESSION['USER_ID'] , $eleve_id , $devoir_id , $item_id , $note_val , $info );
    }
  }
  // Afficher le retour
  exit_json( TRUE ,  array( 'devoir_id'=>$devoir_id , 'groupe_id'=>$groupe_id ) );
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// On ne devrait pas en arriver là...
// ////////////////////////////////////////////////////////////////////////////////////////////////////

exit_json( FALSE , 'Erreur avec les données transmises !' );
?>
