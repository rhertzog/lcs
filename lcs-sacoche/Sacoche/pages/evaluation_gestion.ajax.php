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
if( ($_SESSION['SESAMATH_ID']==ID_DEMO) && (!in_array($_POST['f_action'],array('lister_evaluations','ordonner','indiquer_eleves_deja','saisir','voir','voir_repart','archiver_repart','imprimer_cartouche','generer_tableau_scores_vierge_csv','generer_tableau_scores_rempli_csv','generer_tableau_scores_vierge_pdf','generer_tableau_scores_rempli_pdf'))) ) {exit('Action désactivée pour la démo...');}

$action           = (isset($_POST['f_action']))           ? Clean::texte($_POST['f_action'])                : '';
$type             = (isset($_POST['f_type']))             ? Clean::texte($_POST['f_type'])                  : '';
$aff_classe_txt   = (isset($_POST['f_aff_classe']))       ? Clean::texte($_POST['f_aff_classe'])            : '';
$aff_classe_id    = (isset($_POST['f_aff_classe']))       ? Clean::entier(substr($_POST['f_aff_classe'],1)) : 0;
$aff_periode      = (isset($_POST['f_aff_periode']))      ? Clean::entier($_POST['f_aff_periode'])          : 0;
$date_debut       = (isset($_POST['f_date_debut']))       ? Clean::date_fr($_POST['f_date_debut'])          : '';
$date_fin         = (isset($_POST['f_date_fin']))         ? Clean::date_fr($_POST['f_date_fin'])            : '';
$ref              = (isset($_POST['f_ref']))              ? Clean::texte($_POST['f_ref'])                   : '';
$date             = (isset($_POST['f_date']))             ? Clean::date_fr($_POST['f_date'])                : '';
$date_fr          = (isset($_POST['f_date_fr']))          ? Clean::date_fr($_POST['f_date_fr'])             : '';
$date_visible     = (isset($_POST['f_date_visible']))     ? Clean::date_fr($_POST['f_date_visible'])        : ''; // JJ/MM/AAAA ou "identique" (est alors transformé en 00/00/0000)
$date_autoeval    = (isset($_POST['f_date_autoeval']))    ? Clean::date_fr($_POST['f_date_autoeval'])       : ''; // JJ/MM/AAAA mais peut valoir 00/00/0000
$description      = (isset($_POST['f_description']))      ? Clean::texte($_POST['f_description'])           : '';
$doc_sujet        = (isset($_POST['f_doc_sujet']))        ? Clean::texte($_POST['f_doc_sujet'])             : ''; // Pas Clean::fichier() car transmis pour "dupliquer" (et "modifier") avec le chemin complet http://...
$doc_corrige      = (isset($_POST['f_doc_corrige']))      ? Clean::texte($_POST['f_doc_corrige'])           : ''; // Pas Clean::fichier() car transmis pour "dupliquer" (et "modifier") avec le chemin complet http://...
$groupe           = (isset($_POST['f_groupe']))           ? Clean::texte($_POST['f_groupe'])                : '';
$groupe_nom       = (isset($_POST['f_groupe_nom']))       ? Clean::texte($_POST['f_groupe_nom'])            : '';
$eleves_ordre     = (isset($_POST['f_eleves_ordre']))     ? Clean::texte($_POST['f_eleves_ordre'])          : '';
$eleve_id         = (isset($_POST['f_eleve_id']))         ? Clean::entier($_POST['f_eleve_id'])             : 0;
$msg_objet        = (isset($_POST['f_msg_objet']))        ? Clean::texte($_POST['f_msg_objet'])             : '';
$msg_data         = (isset($_POST['f_msg_data']))         ? Clean::texte($_POST['f_msg_data'])              : '';
$msg_url          = (isset($_POST['f_msg_url']))          ? Clean::texte($_POST['f_msg_url'])               : '';
$msg_autre        = (isset($_POST['f_msg_autre']))        ? Clean::texte($_POST['f_msg_autre'])             : '';
$repartition_type = (isset($_POST['f_repartition_type'])) ? Clean::texte($_POST['f_repartition_type'])      : '';
$cart_detail      = (isset($_POST['f_detail']))           ? Clean::texte($_POST['f_detail'])                : '';
$cart_cases_nb    = (isset($_POST['f_cases_nb']))         ? Clean::entier($_POST['f_cases_nb'])             : '';
$cart_contenu     = (isset($_POST['f_contenu']))          ? Clean::texte($_POST['f_contenu'])               : '';
$orientation      = (isset($_POST['f_orientation']))      ? Clean::texte($_POST['f_orientation'])           : '';
$marge_min        = (isset($_POST['f_marge_min']))        ? Clean::texte($_POST['f_marge_min'])             : '';
$couleur          = (isset($_POST['f_couleur']))          ? Clean::texte($_POST['f_couleur'])               : '';
$fond             = (isset($_POST['f_fond']))             ? Clean::texte($_POST['f_fond'])                  : '';
$only_req         = (isset($_POST['f_restriction_req']))  ? TRUE                                            : FALSE;
$doc_objet        = (isset($_POST['f_doc_objet']))        ? Clean::texte($_POST['f_doc_objet'])             : '';
$doc_url          = (isset($_POST['f_doc_url']))          ? Clean::texte($_POST['f_doc_url'])               : '';
$fini             = (isset($_POST['f_fini']))             ? Clean::texte($_POST['f_fini'])                  : '';

$chemin_devoir      = CHEMIN_DOSSIER_DEVOIR.$_SESSION['BASE'].DS;
$url_dossier_devoir = URL_DIR_DEVOIR.$_SESSION['BASE'].'/';
$fnom_export = $_SESSION['BASE'].'_'.Clean::fichier($groupe_nom).'_'.Clean::fichier($description).'_'.fabriquer_fin_nom_fichier__date_et_alea();

// Si "ref" est renseigné (pour Éditer ou Retirer ou Saisir ou ...), il contient l'id de l'évaluation + '_' + l'initiale du type de groupe + l'id du groupe
// Dans le cas d'une duplication, "ref" sert à retrouver l'évaluation d'origine pour évenuellement récupérer l'ordre des items
if(mb_strpos($ref,'_'))
{
  list($devoir_id,$groupe_temp) = explode('_',$ref,2);
  $devoir_id = Clean::entier($devoir_id);
  // Si "groupe" est transmis en POST (pour Ajouter ou Éditer), il faut le prendre comme référence nouvelle ; sinon, on prend le groupe extrait de "ref"
  $groupe = ($groupe) ? $groupe : Clean::texte($groupe_temp) ;
}
else
{
  $devoir_id = 0;
}

// Si "groupe" est transmis via "ref", il contient l'initiale du type de groupe + l'id du groupe
if($groupe)
{
  $groupe_type_initiale = $groupe{0};
  $tab_groupe  = array('classe'=>'C','groupe'=>'G','besoin'=>'B','eval'=>'E');
  $groupe_type = array_search($groupe_type_initiale,$tab_groupe);
  $groupe_id   = Clean::entier(mb_substr($groupe,1));
}
else
{
  $groupe_type = 'eval';
  $groupe_id   = 0;
}

// Contrôler la liste des items transmis
$tab_id      = (isset($_POST['tab_id'])) ? explode(',',$_POST['tab_id']) : array() ;
$tab_id      = Clean::map_entier($tab_id);
$tab_id      = array_filter($tab_id,'positif');
// Contrôler la liste des items transmis
$tab_items   = (isset($_POST['f_compet_liste'])) ? explode('_',$_POST['f_compet_liste']) : array() ;
$tab_items   = Clean::map_entier($tab_items);
$tab_items   = array_filter($tab_items,'positif');
$nb_items    = count($tab_items);
// Contrôler la liste des élèves transmis (sur des élèves sélectionnés uniquement)
$tab_eleves  = (isset($_POST['f_eleve_liste']))  ? explode('_',$_POST['f_eleve_liste'])  : array() ;
$tab_eleves  = Clean::map_entier($tab_eleves);
$tab_eleves  = array_filter($tab_eleves,'positif');
$nb_eleves   = count($tab_eleves);
// Contrôler la liste des profs transmis
$tab_profs   = array();
$tab_droits  = array( 'v'=>'voir' , 's'=>'saisir' , 'm'=>'modifier' );
$profs_liste = (isset($_POST['f_prof_liste'])) ? $_POST['f_prof_liste'] : '' ;
$tmp_tab     = ($profs_liste) ? explode('_',$profs_liste) : array() ;
foreach($tmp_tab as $valeur)
{
  $droit   = $valeur{0};
  $id_prof = (int)substr($valeur,1);
  if( isset($tab_droits[$droit]) && ($id_prof>0) && ( ($action!='dupliquer') || ($id_prof!=$_SESSION['USER_ID']) ) )
  {
    $tab_profs[$id_prof] = $tab_droits[$droit];
  }
  else
  {
    $profs_liste = str_replace( array( '_'.$valeur , $valeur.'_' , $valeur ) , '' , $profs_liste );
  }
}
$nb_profs   = count($tab_profs);
// Liste des notes transmises
$tab_notes  = (isset($_POST['f_notes'])) ? explode(',',$_POST['f_notes']) : array() ;

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Afficher une liste d'évaluations
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='lister_evaluations') && $type && ( ($type=='selection') || ($aff_classe_txt && $aff_classe_id) ) && ( $aff_periode || ($date_debut && $date_fin) ) )
{
  // Restreindre la recherche à une période donnée, cas d'une date personnalisée (toujours le cas pour une sélection d'élèves)
  if($aff_periode==0)
  {
    // Formater les dates
    $date_debut_mysql = convert_date_french_to_mysql($date_debut);
    $date_fin_mysql   = convert_date_french_to_mysql($date_fin);
    // Vérifier que la date de début est antérieure à la date de fin
    if($date_debut_mysql>$date_fin_mysql)
    {
      exit('Erreur : la date de début est postérieure à la date de fin !');
    }
  }
  // Restreindre la recherche à une période donnée, cas d'une période associée à une classe ou à un groupe
  else
  {
    $DB_ROW = DB_STRUCTURE_COMMUN::DB_recuperer_dates_periode( $aff_classe_id , $aff_periode );
    if(empty($DB_ROW))
    {
      exit('Erreur : cette classe et cette période ne sont pas reliées !');
    }
    // Formater les dates
    $date_debut_mysql = $DB_ROW['jointure_date_debut'];
    $date_fin_mysql   = $DB_ROW['jointure_date_fin'];
  }
  // Lister les évaluations
  $script = '';
  $classe_id = ($aff_classe_txt!='d2') ? $aff_classe_id : -1 ; // 'd2' est transmis si on veut toutes les classes / tous les groupes ; classe_id vaut 0 si selection d'élèves
  $DB_TAB = DB_STRUCTURE_PROFESSEUR::DB_lister_devoirs_prof( $_SESSION['USER_ID'] , $classe_id , $date_debut_mysql , $date_fin_mysql );
  if(!empty($DB_TAB))
  {
    // Récupérer le nb de saisies déjà effectuées par évaluation (ça posait trop de problème dans la requête précédente : saisies comptées plusieurs fois, évaluations sans saisies non retournées...)
    $tab_devoir_id = array();
    foreach($DB_TAB as $DB_ROW)
    {
      $tab_devoir_id[$DB_ROW['devoir_id']] = $DB_ROW['devoir_id'];
    }
    $tab_nb_saisies_effectuees = array_fill_keys($tab_devoir_id,0);
    $DB_TAB2 = DB_STRUCTURE_PROFESSEUR::DB_lister_nb_saisies_par_evaluation( implode(',',$tab_devoir_id) );
    foreach($DB_TAB2 as $DB_ROW)
    {
      $tab_nb_saisies_effectuees[$DB_ROW['devoir_id']] = $DB_ROW['saisies_nombre'];
    }
    // Récupérer les effectifs des classes et groupes
    $tab_effectifs = array();
    if($type=='groupe')
    {
      $DB_TAB2 = DB_STRUCTURE_PROFESSEUR::DB_lister_effectifs_groupes();
      foreach($DB_TAB2 as $DB_ROW)
      {
        $tab_effectifs[$DB_ROW['groupe_id']] = $DB_ROW['eleves_nombre'];
      }
    }
    foreach($DB_TAB as $DB_ROW)
    {
      // Profs avec qui on partage des droits
      if(!$DB_ROW['partage_listing'])
      {
        $profs_liste = '';
        $profs_nombre = 'non';
        $profs_bulle  = '';
      }
      else
      {
        $profs_liste  = $DB_ROW['partage_listing'];
        $nb_profs = mb_substr_count($profs_liste,'_')+1;
        $profs_nombre = ($nb_profs+1).' profs';
        $profs_bulle  = ($nb_profs<10) ? ' <img alt="" src="./_img/bulle_aide.png" width="16" height="16" class="bulle_profs" />' : '' ;
      }
      // Droit sur cette évaluation
      if($DB_ROW['proprio_id']==$_SESSION['USER_ID'])
      {
        $niveau_droit = 4; // propriétaire
      }
      elseif($profs_liste) // forcément
      {
        $search_liste = '_'.$profs_liste.'_';
        if( strpos( $search_liste, '_m'.$_SESSION['USER_ID'].'_' ) !== FALSE )
        {
          $niveau_droit = 3; // modifier
        }
        elseif( strpos( $search_liste, '_s'.$_SESSION['USER_ID'].'_' ) !== FALSE )
        {
          $niveau_droit = 2; // saisir
        }
        elseif( strpos( $search_liste, '_v'.$_SESSION['USER_ID'].'_' ) !== FALSE )
        {
          $niveau_droit = 1; // voir
        }
        else
        {
          exit('Erreur : droit attribué sur le devoir n°'.$DB_ROW['devoir_id'].' non trouvé !');
        }
      }
      else
      {
        exit('Erreur : vous n\'êtes ni propriétaire ni bénéficiaire de droits sur le devoir n°'.$DB_ROW['devoir_id'].' !');
      }
      $date_affich   = convert_date_mysql_to_french($DB_ROW['devoir_date']);
      $date_visible  = ($DB_ROW['devoir_date']==$DB_ROW['devoir_visible_date']) ? 'identique'  : convert_date_mysql_to_french($DB_ROW['devoir_visible_date']) ;
      $date_autoeval = ($DB_ROW['devoir_autoeval_date']===NULL)                 ? 'sans objet' : convert_date_mysql_to_french($DB_ROW['devoir_autoeval_date']) ;
      $ref = $DB_ROW['devoir_id'].'_'.strtoupper($DB_ROW['groupe_type']{0}).$DB_ROW['groupe_id'];
      $cs = ($DB_ROW['items_nombre']>1) ? 's' : '';
      $us = ($type=='groupe') ? '' : ( ($DB_ROW['users_nombre']>1) ? 's' : '' );
      $eleves_bulle = (($type=='selection') && ($DB_ROW['users_nombre']<10)) ? ' <img alt="" src="./_img/bulle_aide.png" width="16" height="16" class="bulle_eleves" />' : '' ;
      $image_sujet   = ($DB_ROW['devoir_doc_sujet'])   ? '<a href="'.$DB_ROW['devoir_doc_sujet'].'" target="_blank" class="no_puce"><img alt="sujet" src="./_img/document/sujet_oui.png" title="Sujet disponible." /></a>' : '<img alt="sujet" src="./_img/document/sujet_non.png" />' ;
      $image_corrige = ($DB_ROW['devoir_doc_corrige']) ? '<a href="'.$DB_ROW['devoir_doc_corrige'].'" target="_blank" class="no_puce"><img alt="corrigé" src="./_img/document/corrige_oui.png" title="Corrigé disponible." /></a>' : '<img alt="corrigé" src="./_img/document/corrige_non.png" />' ;
      $effectif_eleve = ($type=='groupe') ? $tab_effectifs[$DB_ROW['groupe_id']] : $DB_ROW['users_nombre'] ;
      $nb_saisies_possibles = $DB_ROW['items_nombre']*$effectif_eleve;
      $remplissage_nombre   = $tab_nb_saisies_effectuees[$DB_ROW['devoir_id']].'/'.$nb_saisies_possibles ;
      $remplissage_class    = (!$tab_nb_saisies_effectuees[$DB_ROW['devoir_id']]) ? 'br' : ( ($tab_nb_saisies_effectuees[$DB_ROW['devoir_id']]<$nb_saisies_possibles) ? 'bj' : 'bv' ) ;
      $remplissage_class2   = ($DB_ROW['devoir_fini']) ? ' bf' : '' ;
      $remplissage_contenu  = ($DB_ROW['devoir_fini']) ? '<span>terminé</span><i>'.$remplissage_nombre.'</i>' : '<span>'.$remplissage_nombre.'</span><i>terminé</i>' ;
      $remplissage_lien1    = ($niveau_droit<4)  ? '' : '<a href="#fini" class="fini" title="Cliquer pour indiquer (ou pas) qu\'il n\'y a plus de saisies à effectuer.">' ;
      $remplissage_lien2    = ($niveau_droit<4)  ? '' : '</a>' ;
      $remplissage_td_title = ($niveau_droit==4) ? '' : ' title="Clôture restreinte au propriétaire de l\'évaluation ('.html($DB_ROW['proprietaire']).')."' ;
      // Afficher une ligne du tableau
      echo'<tr>';
      echo  '<td>'.$date_affich.'</td>';
      echo  '<td>'.$date_visible.'</td>';
      echo  '<td>'.$date_autoeval.'</td>';
      echo  ($type=='groupe') ? '<td class="'.$DB_ROW['devoir_eleves_ordre'].'">'.html($DB_ROW['groupe_nom']).'</td>' : '<td class="'.$DB_ROW['devoir_eleves_ordre'].'">'.$DB_ROW['users_nombre'].' élève'.$us.$eleves_bulle.'</td>' ;
      echo  '<td id="proprio_'.$DB_ROW['proprio_id'].'">'.$profs_nombre.$profs_bulle.'</td>';
      echo  '<td>'.html($DB_ROW['devoir_info']).'</td>';
      echo  '<td>'.$DB_ROW['items_nombre'].' item'.$cs.'</td>';
      echo  '<td>'.$image_sujet.$image_corrige;
      echo  ($niveau_droit==4) ? '<q class="uploader_doc" title="Ajouter / retirer un sujet ou une correction."></q>' : '<q class="uploader_doc_non" title="Upload restreint au propriétaire de l\'évaluation ('.html($DB_ROW['proprietaire']).')."></q>' ;
      echo  '</td>';
      echo  '<td class="'.$remplissage_class.$remplissage_class2.'"'.$remplissage_td_title.'>'.$remplissage_lien1.$remplissage_contenu.$remplissage_lien2.'</td>';
      echo  '<td class="nu" id="devoir_'.$ref.'">';
      echo    ($niveau_droit>=3) ? '<q class="modifier" title="Modifier cette évaluation (date, description, ...)."></q>' : '<q class="modifier_non" title="Action nécessitant le droit de modification (voir '.html($DB_ROW['proprietaire']).')."></q>' ;
      echo    ($niveau_droit>=3) ? '<q class="ordonner" title="Réordonner les items de cette évaluation."></q>' : '<q class="ordonner_non" title="Action nécessitant le droit de modification (voir '.html($DB_ROW['proprietaire']).')."></q>' ;
      echo    '<q class="dupliquer" title="Dupliquer cette évaluation."></q>';
      echo    ($niveau_droit==4) ? '<q class="supprimer" title="Supprimer cette évaluation."></q>' : '<q class="supprimer_non" title="Suppression restreinte au propriétaire de l\'évaluation ('.html($DB_ROW['proprietaire']).')."></q>' ;
      echo    '<q class="imprimer" title="Imprimer un cartouche pour cette évaluation."></q>';
      echo    ($niveau_droit>=2) ? '<q class="saisir" title="Saisir les acquisitions des élèves à cette évaluation."></q>' : '<q class="saisir_non" title="Action nécessitant le droit de saisie (voir '.html($DB_ROW['proprietaire']).')."></q>' ;
      echo    '<q class="voir" title="Voir les acquisitions des élèves à cette évaluation."></q>';
      echo    '<q class="voir_repart" title="Voir les répartitions des élèves à cette évaluation."></q>';
      echo  '</td>';
      echo'</tr>';
      $script .= 'tab_items["'.$ref.'"]="'.$DB_ROW['items_listing'].'";';
      $script .= 'tab_profs["'.$ref.'"]="'.$profs_liste.'";';
      $script .= ($type=='selection') ? 'tab_eleves["'.$ref.'"]="'.$DB_ROW['users_listing'].'";' : '' ;
      $script .= 'tab_sujets["'.$ref.'"]="'.$DB_ROW['devoir_doc_sujet'].'";';
      $script .= 'tab_corriges["'.$ref.'"]="'.$DB_ROW['devoir_doc_corrige'].'";';
    }
  }
  else
  {
    echo'<tr><td class="nu probleme" colspan="10">Cliquer sur l\'icône ci-dessus (symbole "+" dans un rond vert) pour ajouter une évaluation.</td></tr>';
  }
  
  echo'<SCRIPT>'.$script;
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Ajouter une nouvelle évaluation
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( (($action=='ajouter')||(($action=='dupliquer')&&($devoir_id))) && $type && $date && $date_visible && $date_autoeval && ( ($groupe_type && $groupe_id) || $nb_eleves ) && $nb_items && in_array($eleves_ordre,array('alpha','classe')) )
{
  $date_mysql          = convert_date_french_to_mysql($date);
  $date_visible_mysql  = convert_date_french_to_mysql($date_visible);
  $date_autoeval_mysql = convert_date_french_to_mysql($date_autoeval);
  // Tester les dates
  $date_stamp          = strtotime($date_mysql);
  $date_visible_stamp  = strtotime($date_visible_mysql);
  $date_autoeval_stamp = strtotime($date_autoeval_mysql);
  $mini_stamp          = strtotime("-3 month");
  $maxi_stamp          = strtotime("+3 month");
  $maxi_visible_stamp  = strtotime("+10 month");
  if( ($date_stamp<$mini_stamp) || ($date_stamp>$maxi_stamp) )
  {
    exit('Date devoir trop éloignée !');
  }
  if( ($date_visible_stamp<$mini_stamp) || ($date_visible_stamp>$maxi_visible_stamp) )
  {
    exit('Date visible trop éloignée !');
  }
  if( ($date_autoeval!='00/00/0000') && ( ($date_autoeval_stamp<$mini_stamp) || ($date_autoeval_stamp>$maxi_visible_stamp) ) )
  {
    exit('Date fin auto-éval. trop éloignée !');
  }
  if( ($date_autoeval!='00/00/0000') && ($date_autoeval_mysql<$date_visible_mysql) )
  {
    exit('Date fin auto-éval. avant date visible !');
  }
  // Ordre des élèves
  if($groupe_type=='classe')
  {
    $eleves_ordre = 'alpha';
  }
  else
  {
    Form::save_choix('evaluation_gestion');
  }
  // En cas de duplication d'une évaluation comportant un fichier d'énoncé ou de corrigé, il faut aussi en dupliquer ces documents sous un autre nom.
  // Sinon, lors de la suppression de l'un des devoirs, l'autre perd ses documents associés.
  // On ne peut pas intégrer dans le nouveau nom l'id du nouveau devoir car il n'est pas encore connu, mais on peut en modifier le timestamp.
  if($action=='dupliquer')
  {
    $tab_doc_objet = array( 'sujet' , 'corrige' );
    foreach($tab_doc_objet as $objet)
    {
      $masque_recherche = '#^'.str_replace('.','\.',$url_dossier_devoir).'devoir_([0-9]+)_('.$objet.')_([0-9]+)\.([a-z]{2,4})$#' ;
      $url_actuelle = ${'doc_'.$objet};
      if(preg_match( $masque_recherche , $url_actuelle ))
      {
        $masque_remplacement = $url_dossier_devoir.'devoir_$1_$2_'.time().'.$4';
        $url_nouvelle = preg_replace( $masque_recherche , $masque_remplacement , $url_actuelle );
        copy ( url_to_chemin($url_actuelle) , url_to_chemin($url_nouvelle) );
        ${'doc_'.$objet} = $url_nouvelle;
      }
    }
  }
  if($type=='selection')
  {
    // Commencer par créer un nouveau groupe de type "eval", utilisé uniquement pour cette évaluation (c'est transparent pour le professeur) ; y associe automatiquement le prof, en responsable du groupe
    $groupe_id = DB_STRUCTURE_PROFESSEUR::DB_ajouter_groupe_par_prof( $groupe_type , '' , 0 );
  }
  // Insèrer l'enregistrement de l'évaluation
  $devoir_id2 = DB_STRUCTURE_PROFESSEUR::DB_ajouter_devoir( $_SESSION['USER_ID'] , $groupe_id , $date_mysql , $description , $date_visible_mysql , $date_autoeval_mysql , $doc_sujet , $doc_corrige , $eleves_ordre );
  if($type=='selection')
  {
    // Affecter tous les élèves choisis
    DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_eleve( $devoir_id2 , $groupe_id , $tab_eleves , 'creer' );
  }
  if($nb_profs)
  {
    // Affecter tous les profs choisis
    DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_prof( $devoir_id2 , $tab_profs , 'creer' );
  }
  // Insérer les enregistrements des items de l'évaluation
  DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_item( $devoir_id2 , $tab_items , 'dupliquer' , $devoir_id );
  // Insérer les scores 'REQ' (cas d'une création d'évaluation depuis une synthèse, à partir d'items personnalisés par élève)
  if(!empty($_SESSION['TMP']['req_user_item']))
  {
    $info = 'À saisir ('.afficher_identite_initiale($_SESSION['USER_NOM'],FALSE,$_SESSION['USER_PRENOM'],TRUE).')';
    foreach($_SESSION['TMP']['req_user_item'] as $req)
    {
      list($eleve_id,$item_id) = explode('x',$req);
      DB_STRUCTURE_PROFESSEUR::DB_ajouter_saisie( $_SESSION['USER_ID'] , $eleve_id , $devoir_id2 , $item_id , $date_mysql , 'REQ' , $info , $date_visible_mysql );
    }
    unset($_SESSION['TMP']['req_user_item']);
  }
  // Récupérer l'effectif de la classe ou du groupe
  $effectif_eleve = ($type=='groupe') ? DB_STRUCTURE_PROFESSEUR::DB_lister_effectifs_groupes($groupe_id) : $nb_eleves ;
  // Afficher le retour
  $date_visible  = ($date_visible==$date)         ? 'identique'  : $date_visible  ;
  $date_autoeval = ($date_autoeval=='00/00/0000') ? 'sans objet' : $date_autoeval ;
  $ref = $devoir_id2.'_'.strtoupper($groupe_type{0}).$groupe_id;
  $cs = ($nb_items >1) ? 's' : '' ;
  $us = ($nb_eleves>1) ? 's' : '' ;
  $eleves_bulle = (($type=='selection') && ($nb_eleves<10)) ? ' <img alt="" src="./_img/bulle_aide.png" width="16" height="16" class="bulle_eleves" />' : '' ;
  $profs_nombre = ($nb_profs) ? ($nb_profs+1).' profs' : 'non' ;
  $profs_bulle  = ($nb_profs && ($nb_profs<10)) ? ' <img alt="" src="./_img/bulle_aide.png" width="16" height="16" class="bulle_profs" />' : '' ;
  $image_sujet   = ($doc_sujet)   ? '<a href="'.$doc_sujet.'" target="_blank" class="no_puce"><img alt="sujet" src="./_img/document/sujet_oui.png" title="Sujet disponible." /></a>'         : '<img alt="sujet" src="./_img/document/sujet_non.png" />' ;
  $image_corrige = ($doc_corrige) ? '<a href="'.$doc_corrige.'" target="_blank" class="no_puce"><img alt="corrigé" src="./_img/document/corrige_oui.png" title="Corrigé disponible." /></a>' : '<img alt="corrigé" src="./_img/document/corrige_non.png" />' ;
  $nb_saisies_possibles = $nb_items*$effectif_eleve;
  $remplissage_nombre   = '0/'.$nb_saisies_possibles ;
  $remplissage_class    = 'br';
  $remplissage_class2   = '' ;
  $remplissage_contenu  = '<span>'.$remplissage_nombre.'</span><i>terminé</i>';
  $remplissage_lien1    = '<a href="#fini" class="fini" title="Cliquer pour indiquer (ou pas) qu\'il n\'y a plus de saisies à effectuer.">';
  $remplissage_lien2    = '</a>';
  echo'<td>'.$date.'</td>';
  echo'<td>'.$date_visible.'</td>';
  echo'<td>'.$date_autoeval.'</td>';
  echo ($type=='groupe') ? '<td class="'.$eleves_ordre.'">{{GROUPE_NOM}}</td>' : '<td class="'.$eleves_ordre.'">'.$nb_eleves.' élève'.$us.$eleves_bulle.'</td>' ;
  echo'<td id="proprio_'.$_SESSION['USER_ID'].'">'.$profs_nombre.$profs_bulle.'</td>';
  echo'<td>'.html($description).'</td>';
  echo'<td>'.$nb_items.' item'.$cs.'</td>';
  echo'<td>'.$image_sujet.$image_corrige.'<q class="uploader_doc" title="Ajouter / retirer un sujet ou une correction."></q></td>';
  echo'<td class="'.$remplissage_class.$remplissage_class2.'">'.$remplissage_lien1.$remplissage_contenu.$remplissage_lien2.'</td>';
  echo'<td class="nu" id="devoir_'.$ref.'">';
  echo  '<q class="modifier" title="Modifier cette évaluation (date, description, ...)."></q>';
  echo  '<q class="ordonner" title="Réordonner les items de cette évaluation."></q>';
  echo  '<q class="dupliquer" title="Dupliquer cette évaluation."></q>';
  echo  '<q class="supprimer" title="Supprimer cette évaluation."></q>';
  echo  '<q class="imprimer" title="Imprimer un cartouche pour cette évaluation."></q>';
  echo  '<q class="saisir" title="Saisir les acquisitions des élèves à cette évaluation."></q>';
  echo  '<q class="voir" title="Voir les acquisitions des élèves à cette évaluation."></q>';
  echo  '<q class="voir_repart" title="Voir les répartitions des élèves à cette évaluation."></q>';
  echo'</td>';
  echo'<SCRIPT>';
  echo'tab_items["'.$ref.'"]="'.implode('_',$tab_items).'";';
  echo'tab_profs["'.$ref.'"]="'.$profs_liste.'";';
  echo ($type=='selection') ? 'tab_eleves["'.$ref.'"]="'.implode('_',$tab_eleves).'";' : '' ;
  echo'tab_sujets["'.$ref.'"]="'.$doc_sujet.'";';
  echo'tab_corriges["'.$ref.'"]="'.$doc_corrige.'";';
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Modifier une évaluation existante
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='modifier') && $devoir_id && $groupe_type && $groupe_id && $date && $date_visible && $date_autoeval && ( ($type=='groupe') || $nb_eleves ) && $nb_items && in_array($fini,array('oui','non')) && in_array($eleves_ordre,array('alpha','classe')) )
{
  $date_mysql          = convert_date_french_to_mysql($date);
  $date_visible_mysql  = convert_date_french_to_mysql($date_visible);
  $date_autoeval_mysql = convert_date_french_to_mysql($date_autoeval);
  // Tester les dates
  $date_stamp          = strtotime($date_mysql);
  $date_visible_stamp  = strtotime($date_visible_mysql);
  $date_autoeval_stamp = strtotime($date_autoeval_mysql);
  $mini_stamp          = strtotime("-10 month");
  $maxi_stamp          = strtotime("+10 month");
  if( ($date_stamp<$mini_stamp) || ($date_stamp>$maxi_stamp) )
  {
    exit('Date devoir trop éloignée !');
  }
  if( ($date_visible_stamp<$mini_stamp) || ($date_visible_stamp>$maxi_stamp) )
  {
    exit('Date visible trop éloignée !');
  }
  if( ($date_autoeval!='00/00/0000') && ( ($date_autoeval_stamp<$mini_stamp) || ($date_autoeval_stamp>$maxi_stamp) ) )
  {
    exit('Date fin auto-éval. trop éloignée !');
  }
  if( ($date_autoeval!='00/00/0000') && ($date_autoeval_mysql<$date_visible_mysql) )
  {
    exit('Date fin auto-éval. avant date visible !');
  }
  // Tester les droits
  $proprio_id = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_id( $devoir_id );
  if($proprio_id==$_SESSION['USER_ID'])
  {
    $niveau_droit = 4; // propriétaire
    $proprietaire_genre  = $_SESSION['USER_GENRE'];
    $proprietaire_nom    = $_SESSION['USER_NOM'];
    $proprietaire_prenom = $_SESSION['USER_PRENOM'];
  }
  elseif($profs_liste) // forcément
  {
    $search_liste = '_'.$profs_liste.'_';
    if( strpos( $search_liste, '_m'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      $niveau_droit = 3; // modifier
    }
    elseif( strpos( $search_liste, '_s'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      exit('Erreur : droit insuffisant attribué sur le devoir n°'.$devoir_id.' (niveau 2 au lieu de 3) !'); // saisir
    }
    elseif( strpos( $search_liste, '_v'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      exit('Erreur : droit insuffisant attribué sur le devoir n°'.$devoir_id.' (niveau 1 au lieu de 3) !'); // voir
    }
    else
    {
      exit('Erreur : droit attribué sur le devoir n°'.$devoir_id.' non trouvé !');
    }
    $DB_ROW = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_identite( $devoir_id );
    $proprietaire_genre  = $DB_ROW['user_genre'];
    $proprietaire_nom    = $DB_ROW['user_nom'];
    $proprietaire_prenom = $DB_ROW['user_prenom'];
  }
  else
  {
    exit('Erreur : vous n\'êtes ni propriétaire ni bénéficiaire de droits sur le devoir n°'.$devoir_id.' !');
  }
  $proprietaire_identite = $proprietaire_nom.' '.$proprietaire_prenom;
  $proprietaire_archive  = afficher_identite_initiale($proprietaire_nom,FALSE,$proprietaire_prenom,TRUE,$proprietaire_genre);
  // Ordre des élèves
  if($groupe_type=='classe')
  {
    $eleves_ordre = 'alpha';
  }
  else
  {
    Form::save_choix('evaluation_gestion');
  }
  // sacoche_devoir (maj des paramètres date & info)
  DB_STRUCTURE_PROFESSEUR::DB_modifier_devoir( $devoir_id , $proprio_id , $date_mysql , $description , $proprietaire_archive , $date_visible_mysql , $date_autoeval_mysql , $eleves_ordre );
  if($type=='selection')
  {
    // sacoche_jointure_user_groupe + sacoche_saisie pour les users supprimés
    DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_eleve( $devoir_id , $groupe_id , $tab_eleves , 'substituer' );
  }
  elseif($type=='groupe')
  {
    // sacoche_devoir (maj groupe_id) + sacoche_saisie pour TOUS les users !
    DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_groupe( $devoir_id , $groupe_id );
  }
  // sacoche_jointure_devoir_prof ; à restreindre en cas de modification d'une évaluation dont on n'est pas le propriétaire
  if($proprio_id==$_SESSION['USER_ID'])
  {
    if($nb_profs)
    {
      // Mofifier les affectations des profs choisis
      DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_prof( $devoir_id , $tab_profs , 'substituer' );
    }
    else
    {
      // Au cas où on aurait retiré les droits à tous
      DB_STRUCTURE_PROFESSEUR::DB_supprimer_liaison_devoir_prof($devoir_id);
    }
  }
  // sacoche_jointure_devoir_item + sacoche_saisie pour les items supprimés
  DB_STRUCTURE_PROFESSEUR::DB_modifier_liaison_devoir_item( $devoir_id , $tab_items , 'substituer' );
  // Récupérer le nb de saisies déjà effectuées pour l'évaluation
  $nb_saisies_effectuees = DB_STRUCTURE_PROFESSEUR::DB_lister_nb_saisies_par_evaluation($devoir_id);
  // Récupérer l'effectif de la classe ou du groupe
  $effectif_eleve = ($type=='groupe') ? DB_STRUCTURE_PROFESSEUR::DB_lister_effectifs_groupes($groupe_id) : $nb_eleves ;
  // Afficher le retour
  $date_visible  = ($date_visible==$date)         ? 'identique'  : $date_visible  ;
  $date_autoeval = ($date_autoeval=='00/00/0000') ? 'sans objet' : $date_autoeval ;
  $ref = $devoir_id.'_'.strtoupper($groupe_type{0}).$groupe_id;
  $cs = ($nb_items>1)  ? 's' : '' ;
  $us = ($nb_eleves>1) ? 's' : '' ;
  $eleves_bulle = (($type=='selection') && ($nb_eleves<10)) ? ' <img alt="" src="./_img/bulle_aide.png" width="16" height="16" class="bulle_eleves" />' : '' ;
  $profs_nombre = ($nb_profs) ? ($nb_profs+1).' profs' : 'non' ;
  $profs_bulle  = ($nb_profs && ($nb_profs<10)) ? ' <img alt="" src="./_img/bulle_aide.png" width="16" height="16" class="bulle_profs" />' : '' ;
  $image_sujet   = ($doc_sujet)   ? '<a href="'.$doc_sujet.'" target="_blank" class="no_puce"><img alt="sujet" src="./_img/document/sujet_oui.png" title="Sujet disponible." /></a>'         : '<img alt="sujet" src="./_img/document/sujet_non.png" />' ;
  $image_corrige = ($doc_corrige) ? '<a href="'.$doc_corrige.'" target="_blank" class="no_puce"><img alt="corrigé" src="./_img/document/corrige_oui.png" title="Corrigé disponible." /></a>' : '<img alt="corrigé" src="./_img/document/corrige_non.png" />' ;
  $nb_saisies_possibles = $nb_items*$effectif_eleve;
  $remplissage_nombre   = $nb_saisies_effectuees.'/'.$nb_saisies_possibles ;
  $remplissage_class    = (!$nb_saisies_effectuees) ? 'br' : ( ($nb_saisies_effectuees<$nb_saisies_possibles) ? 'bj' : 'bv' ) ;
  $remplissage_class2   = ($fini=='oui') ? ' bf' : '' ;
  $remplissage_contenu  = ($fini=='oui') ? '<span>terminé</span><i>'.$remplissage_nombre.'</i>' : '<span>'.$remplissage_nombre.'</span><i>terminé</i>' ;
  $remplissage_lien1    = ($niveau_droit<4)  ? '' : '<a href="#fini" class="fini" title="Cliquer pour indiquer (ou pas) qu\'il n\'y a plus de saisies à effectuer.">' ;
  $remplissage_lien2    = ($niveau_droit<4)  ? '' : '</a>' ;
  $remplissage_td_title = ($niveau_droit==4) ? '' : ' title="Clôture restreinte au propriétaire de l\'évaluation ('.html($proprietaire_identite).')."' ;
  echo'<td>'.$date.'</td>';
  echo'<td>'.$date_visible.'</td>';
  echo'<td>'.$date_autoeval.'</td>';
  echo ($type=='groupe') ? '<td class="'.$eleves_ordre.'">{{GROUPE_NOM}}</td>' : '<td class="'.$eleves_ordre.'">'.$nb_eleves.' élève'.$us.$eleves_bulle.'</td>' ;
  echo'<td id="proprio_'.$proprio_id.'">'.$profs_nombre.$profs_bulle.'</td>';
  echo'<td>'.html($description).'</td>';
  echo'<td>'.$nb_items.' item'.$cs.'</td>';
  echo'<td>'.$image_sujet.$image_corrige;
  echo ($niveau_droit==4) ? '<q class="uploader_doc" title="Ajouter / retirer un sujet ou une correction."></q>' : '<q class="uploader_doc_non" title="Upload restreint au propriétaire de l\'évaluation ('.html($proprietaire_identite).')."></q>' ;
  echo'</td>';
  echo'<td class="'.$remplissage_class.$remplissage_class2.'"'.$remplissage_td_title.'>'.$remplissage_lien1.$remplissage_contenu.$remplissage_lien2.'</td>';
  echo'<td class="nu" id="devoir_'.$ref.'">';
  echo ($niveau_droit>=3) ? '<q class="modifier" title="Modifier cette évaluation (date, description, ...)."></q>' : '<q class="modifier_non" title="Action nécessitant le droit de modification (voir '.html($proprietaire_identite).')."></q>' ;
  echo ($niveau_droit>=3) ? '<q class="ordonner" title="Réordonner les items de cette évaluation."></q>' : '<q class="ordonner_non" title="Action nécessitant le droit de modification (voir '.html($proprietaire_identite).')."></q>' ;
  echo  '<q class="dupliquer" title="Dupliquer cette évaluation."></q>';
  echo ($niveau_droit==4) ? '<q class="supprimer" title="Supprimer cette évaluation."></q>' : '<q class="supprimer_non" title="Suppression restreinte au propriétaire de l\'évaluation ('.html($proprietaire_identite).')."></q>' ;
  echo  '<q class="imprimer" title="Imprimer un cartouche pour cette évaluation."></q>';
  echo  '<q class="saisir" title="Saisir les acquisitions des élèves à cette évaluation."></q>'; // niveau de droit à 3 ou 4 donc au moins à 2
  echo  '<q class="voir" title="Voir les acquisitions des élèves à cette évaluation."></q>';
  echo  '<q class="voir_repart" title="Voir les répartitions des élèves à cette évaluation."></q>';
  echo'</td>';
  echo'<SCRIPT>';
  echo'tab_items["'.$ref.'"]="'.implode('_',$tab_items).'";';
  echo'tab_profs["'.$ref.'"]="'.$profs_liste.'";';
  echo ($type=='selection') ? 'tab_eleves["'.$ref.'"]="'.implode('_',$tab_eleves).'";' : '' ;
  echo'tab_sujets["'.$ref.'"]="'.$doc_sujet.'";';
  echo'tab_corriges["'.$ref.'"]="'.$doc_corrige.'";';
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Supprimer une évaluation existante
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='supprimer') && $devoir_id && ( ($type=='groupe') || $groupe_id ) )
{
  // Vérification des droits
  $proprio_id = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_id( $devoir_id );
  if($proprio_id!=$_SESSION['USER_ID'])
  {
    exit('Erreur : vous n\'êtes pas propriétaire du devoir n°'.$devoir_id.' !');
  }
  // On y va
  if($type=='selection')
  {
    // supprimer le groupe spécialement associé (invisible à l'utilisateur) et les entrées dans sacoche_jointure_user_groupe pour une évaluation avec des élèves piochés en dehors de tout groupe prédéfini
    DB_STRUCTURE_PROFESSEUR::DB_supprimer_groupe_par_prof( $groupe_id , $groupe_type , FALSE /*with_devoir*/ );
    SACocheLog::ajouter('Suppression d\'un regroupement ('.$groupe_type.' '.$groupe_id.'), sans les devoirs associés.');
  }
  // on supprime l'évaluation avec ses saisies
  DB_STRUCTURE_PROFESSEUR::DB_supprimer_devoir_et_saisies( $devoir_id );
  SACocheLog::ajouter('Suppression d\'un devoir ('.$devoir_id.') avec les saisies associées.');
  // Afficher le retour
  exit('<td>ok</td>');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Afficher le formulaire pour réordonner les items d'une évaluation
// La vérification de droits suffisants s'effectuera lors de la soumission.
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='ordonner') && $devoir_id )
{
  // liste des items
  $DB_TAB_COMP = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_items( $devoir_id , FALSE /*with_lien*/ , TRUE /*with_coef*/ );
  if(empty($DB_TAB_COMP))
  {
    exit('Aucun item n\'est associé à cette évaluation !');
  }
  foreach($DB_TAB_COMP as $DB_ROW)
  {
    $item_ref = $DB_ROW['item_ref'];
    $texte_socle = ($DB_ROW['entree_id']) ? ' [S]' : ' [–]';
    $texte_coef  = ' ['.$DB_ROW['item_coef'].']';
    echo'<li id="i'.$DB_ROW['item_id'].'"><b>'.html($item_ref.$texte_socle.$texte_coef).'</b> - '.html($DB_ROW['item_nom']).'</li>';
  }
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Indiquer la liste des élèves associés à une évaluation de même nom (uniquement pour une sélection d'élèves)
// Reprise d'un développement initié par Alain Pottier <alain.pottier613@orange.fr> et publié le 08/02/2012
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='indiquer_eleves_deja') && $description && $date_debut )
{
  $date_debut_mysql = convert_date_french_to_mysql($date_debut);
  $DB_TAB = DB_STRUCTURE_PROFESSEUR::DB_lister_eleves_devoirs($_SESSION['USER_ID'],$description,$date_debut_mysql);
  $tab_retour = array();
  foreach($DB_TAB as $DB_ROW)
  {
    $tab_retour[] = $DB_ROW['user_id'].'_'.convert_date_mysql_to_french($DB_ROW['devoir_date']);
  }
  exit( 'ok,'.implode(',',$tab_retour) );
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Afficher le formulaire pour saisir les items acquis par les élèves à une évaluation
// Voir les items acquis par les élèves à une évaluation
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( in_array($action,array('saisir','voir')) && $devoir_id && $groupe_id && $date_fr && in_array($eleves_ordre,array('alpha','classe')) ) // $description et $groupe_nom sont aussi transmis
{
  $with_lien = ($action=='voir') ? TRUE : FALSE ;
  // liste des items
  $DB_TAB_COMP = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_items( $devoir_id , $with_lien , TRUE /*with_coef*/ );
  // liste des élèves
  $DB_TAB_USER = DB_STRUCTURE_COMMUN::DB_lister_users_regroupement( 'eleve' /*profil*/ , TRUE /*statut*/ , $groupe_type , $groupe_id , $eleves_ordre );
  // liste des commentaires audio ou texte
  $DB_TAB_MSG = DB_STRUCTURE_COMMENTAIRE::DB_lister_devoir_commentaires($devoir_id);
  // Let's go
  $item_nb = count($DB_TAB_COMP);
  if(!$item_nb)
  {
    exit('Aucun item n\'est associé à cette évaluation !');
  }
  $eleve_nb = count($DB_TAB_USER);
  if(!$eleve_nb)
  {
    exit('Aucun élève n\'est associé à cette évaluation !');
  }
  $check_largeur = ($_SESSION['BROWSER']['mobile']) ? ' checked' : '' ;
  $tab_affich  = array(); // tableau bi-dimensionnel [n°ligne=id_item][n°colonne=id_user]
  $tab_user_id = array(); // pas indispensable, mais plus lisible
  $tab_comp_id = array(); // pas indispensable, mais plus lisible
  $tab_affich['head'][0] = '<td>';
  if($action=='saisir')
  {
    $tab_affich['head'][0].= '<span class="manuel"><a class="pop_up" href="'.SERVEUR_DOCUMENTAIRE.'?fichier=support_professeur__evaluations_saisie_resultats">DOC : Saisie des résultats.</a></span>';
    $tab_affich['head'][0].= '<p>';
    $tab_affich['head'][0].= '<label for="radio_clavier"><input type="radio" id="radio_clavier" name="mode_saisie" value="clavier" /> <span class="eval pilot_keyboard">Piloter au clavier</span></label> <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Sélectionner un rectangle blanc<br />au clavier (flèches) ou à la souris<br />puis utiliser les touches suivantes :<br />&nbsp;1 ; 2 ; 3 ; 4 ; A ; D ; E ; F ; N ; P ; R ; suppr .<br />Pour un report multiple, presser avant<br />C (Colonne), L (Ligne) ou T (Tableau)." /><br />';
    $tab_affich['head'][0].= '<span id="arrow_continue"><label for="arrow_continue_down"><input type="radio" id="arrow_continue_down" name="arrow_continue" value="down" /> <span class="eval arrow_continue_down">par élève</span></label>&nbsp;&nbsp;&nbsp;<label for="arrow_continue_rigth"><input type="radio" id="arrow_continue_rigth" name="arrow_continue" value="rigth" /> <span class="eval arrow_continue_rigth">par item</span></label></span><br />';
    $tab_affich['head'][0].= '<label for="radio_souris"><input type="radio" id="radio_souris" name="mode_saisie" value="souris" /> <span class="eval pilot_mouse">Piloter à la souris</span></label> <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Survoler une case du tableau avec la souris<br />puis cliquer sur une des images proposées." />';
    $tab_affich['head'][0].= '</p>';
  }
  $tab_affich['head'][0].= '<p>';
  $tab_affich['head'][0].= '<label for="check_largeur"><input type="checkbox" id="check_largeur" name="check_largeur" value="retrecir_largeur"'.$check_largeur.' /> <span class="eval retrecir_largeur">Largeur optimale</span></label> <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Diminuer la largeur des colonnes<br />si les élèves sont nombreux." /><br />';
  $tab_affich['head'][0].= '<label for="check_hauteur"><input type="checkbox" id="check_hauteur" name="check_hauteur" value="retrecir_hauteur" /> <span class="eval retrecir_hauteur">Hauteur optimale</span></label> <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Diminuer la hauteur des lignes<br />si les items sont nombreux." />';
  $tab_affich['head'][0].= '</p>';
  $tab_affich['head'][0].= '</td>';
  $tab_affich['foot_texte'][0] = '<th>Commentaire écrit</th>';
  $tab_affich['foot_audio'][0] = '<th>Commentaire audio</th>';
  // première ligne (noms prénoms des élèves)
  $br = ($_SESSION['BROWSER']['mobile']) ? '' : '&amp;br' ;
  $q_texte = ($action=='saisir') ? '<q class="texte_enregistrer" title="Saisir un commentaire écrit."></q>'      : '<q class="texte_consulter_non" title="Pas de commentaire écrit."></q>' ;
  $q_audio = ($action=='saisir') ? '<q class="audio_enregistrer" title="Enregistrer un commentaire audio."></q>' : '<q class="audio_ecouter_non" title="Pas de commentaire audio."></q>' ;
  foreach($DB_TAB_USER as $DB_ROW)
  {
    $tab_affich['head'][$DB_ROW['user_id']] = '<th><img id="image_'.$DB_ROW['user_id'].'" alt="'.html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']).'" src="./_img/php/etiquette.php?dossier='.$_SESSION['BASE'].'&amp;nom='.urlencode($DB_ROW['user_nom']).'&amp;prenom='.urlencode($DB_ROW['user_prenom']).$br.'" /></th>';
    $tab_user_id[$DB_ROW['user_id']] = html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']);
    // On initialise ces cellules, qui seront remplacées si besoin par une autre valeur dans la boucle suivante
    $tab_affich['foot_texte'][$DB_ROW['user_id']] = '<td id="texte_'.$DB_ROW['user_id'].'">'.$q_texte.'</td>';
    $tab_affich['foot_audio'][$DB_ROW['user_id']] = '<td id="audio_'.$DB_ROW['user_id'].'">'.$q_audio.'</td>';
  }
  if(!empty($DB_TAB_MSG))
  {
    $tab_balise = array(
      'saisir' => array(
        'texte' => '<q class="texte_enregistrer" title="Modifier le commentaire écrit."></q>',
        'audio' => '<q class="audio_enregistrer" title="Modifier le commentaire audio."></q>',
      ),
      'voir' => array(
        'texte' => '<q class="texte_consulter" title="Commentaire écrit disponible."></q>',
        'audio' => '<q class="audio_ecouter" title="Commentaire audio disponible."></q>',
      )
    );
    foreach($DB_TAB_MSG as $DB_ROW)
    {
      foreach($tab_balise[$action] as $msg_objet => $balise_html)
      {
        if($DB_ROW['jointure_'.$msg_objet])
        {
          $tab_affich['foot_'.$msg_objet][$DB_ROW['eleve_id']] = '<td id="'.$msg_objet.'_'.$DB_ROW['eleve_id'].'" class="off">'.$balise_html.'</td>';
        }
      }
    }
  }
  // première colonne (noms items)
  foreach($DB_TAB_COMP as $DB_ROW)
  {
    $item_ref = $DB_ROW['item_ref'];
    $texte_socle = ($DB_ROW['entree_id']) ? ' [S]' : ' [–]';
    $texte_coef  = ' ['.$DB_ROW['item_coef'].']';
    $texte_lien_avant = ( ($action=='voir') && ($DB_ROW['item_lien']) ) ? '<a target="_blank" href="'.html($DB_ROW['item_lien']).'">' : '';
    $texte_lien_apres = ( ($action=='voir') && ($DB_ROW['item_lien']) ) ? '</a>' : '';
    $tab_affich[$DB_ROW['item_id']][0] = '<th><b>'.$texte_lien_avant.html($item_ref.$texte_socle.$texte_coef).$texte_lien_apres.'</b> <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="'.html(html($DB_ROW['item_nom'])).'" /><div>'.html($DB_ROW['item_nom']).'</div></th>'; // Volontairement 2 html() pour le title sinon &lt;* est pris comme une balise html par l'infobulle.
    $tab_comp_id[$DB_ROW['item_id']] = $item_ref;
  }
  // cases centrales...
  $num_colonne = 0;
  foreach($tab_user_id as $user_id=>$val_user)
  {
    $num_colonne++;
    $num_ligne=0;
    foreach($tab_comp_id as $comp_id=>$val_comp)
    {
      $num_ligne++;
      if($action=='saisir')
      {
        // ... avec un champ input de base
        $tab_affich[$comp_id][$user_id] = '<td class="td_clavier" id="td_C'.$num_colonne.'L'.$num_ligne.'"><input type="text" class="X" value="X" id="C'.$num_colonne.'L'.$num_ligne.'" name="'.$comp_id.'x'.$user_id.'" readonly /></td>';
      }
      elseif($action=='voir')
      {
        // ... vierges
        $tab_affich[$comp_id][$user_id] = '<td title="'.$val_user.'<br />'.$val_comp.'">-</td>';
      }
    }
  }
  // ajouter le contenu
  $DB_TAB = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_saisies( $devoir_id , TRUE /*with_REQ*/ );
  $bad = 'class="X" value="X"';
  foreach($DB_TAB as $DB_ROW)
  {
    // Test pour éviter les pbs des élèves changés de groupes ou des items modifiés en cours de route
    if(isset($tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']]))
    {
      if($action=='saisir')
      {
        $bon = 'class="'.$DB_ROW['saisie_note'].'" value="'.$DB_ROW['saisie_note'].'"';
        $tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']] = str_replace($bad,$bon,$tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']]);
      }
      elseif($action=='voir')
      {
        $tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']] = str_replace('>-<','>'.Html::note_image($DB_ROW['saisie_note'],'','',FALSE).'<',$tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']]);
      }
    }
  }
  //
  // c'est fini ; affichage du retour
  //
  $tbody_class = ($_SESSION['BROWSER']['mobile']) ? 'v' : 'h' ;
  foreach($tab_affich as $comp_id => $tab_user)
  {
    if(!is_int($comp_id))
    {
      switch($comp_id)
      {
        case 'head'       : echo'<thead>';break;
        case 'foot_texte' : echo'<tfoot>';break;
        case 'foot_audio' : break;
      }
    }
    echo ( ($comp_id=='foot_texte') || ($comp_id=='foot_audio') ) ? '<tr class="no_margin">' : '<tr>' ;
    foreach($tab_user as $user_id => $val)
    {
      echo $val;
    }
    echo'</tr>';
    if(!is_int($comp_id))
    {
      switch($comp_id)
      {
        case 'head'       : echo'</thead>';break;
        case 'foot_texte' : break;
        case 'foot_audio' : echo'</tfoot><tbody class="'.$tbody_class.'">';break;
      }
    }
  }
  echo'</tbody>';
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Générer un csv à récupérer pour une saisie déportée, vide ou plein.
// Générer un pdf contenant un tableau de saisie, vide ou plein.
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( in_array($action,array('generer_tableau_scores_vierge_csv','generer_tableau_scores_rempli_csv','generer_tableau_scores_vierge_pdf','generer_tableau_scores_rempli_pdf')) && $devoir_id && $groupe_id && $date_fr && in_array($eleves_ordre,array('alpha','classe')) && $couleur && $fond ) // $description et $groupe_nom sont aussi transmis
{
  list( , , , $remplissage , $format ) = explode('_',$action);
  $tab_scores  = array(); // tableau bi-dimensionnel [id_item][id_user]
  $tab_user_id = array(); // pas indispensable, mais plus lisible
  $tab_comp_id = array(); // pas indispensable, mais plus lisible
  // liste des items
  $DB_TAB_COMP = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_items( $devoir_id , FALSE /*with_lien*/ , TRUE /*with_coef*/ );
  // liste des élèves
  $DB_TAB_USER = DB_STRUCTURE_COMMUN::DB_lister_users_regroupement( 'eleve' /*profil*/ , TRUE /*statut*/ , $groupe_type , $groupe_id , $eleves_ordre );
  // Let's go
  $item_nb = count($DB_TAB_COMP);
  if(!$item_nb)
  {
    exit('Aucun item n\'est associé à cette évaluation !');
  }
  $eleve_nb = count($DB_TAB_USER);
  if(!$eleve_nb)
  {
    exit('Aucun élève n\'est associé à cette évaluation !');
  }
  // liste items
  foreach($DB_TAB_COMP as $DB_ROW)
  {
    $item_ref = $DB_ROW['item_ref'];
    $tab_comp_id[$DB_ROW['item_id']] = $item_ref;
  }
  // liste élèves
  foreach($DB_TAB_USER as $DB_ROW)
  {
    $tab_user_id[$DB_ROW['user_id']] = html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']);
  }
  // récupération des scores
  foreach($tab_user_id as $user_id=>$val_user)
  {
    foreach($tab_comp_id as $comp_id=>$val_comp)
    {
      $tab_scores[$comp_id][$user_id] = '';
    }
  }
  if($remplissage=='rempli')
  {
    $DB_TAB = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_saisies( $devoir_id , TRUE /*with_REQ*/ );
    foreach($DB_TAB as $DB_ROW)
    {
      // Test pour éviter les pbs des élèves changés de groupes ou des items modifiés en cours de route
      if(isset($tab_scores[$DB_ROW['item_id']][$DB_ROW['eleve_id']]))
      {
        $tab_scores[$DB_ROW['item_id']][$DB_ROW['eleve_id']] = $DB_ROW['saisie_note'];
      }
    }
  }
  //
  // pdf contenant un tableau de saisie vide ou plein
  //
  if($format=='pdf')
  {
    Form::save_choix('evaluation_archivage');
    $tab_couleurs = array( 'oui'=>'couleur' , 'non'=>'monochrome' );
    $tableau_PDF = new PDF_evaluation_tableau( FALSE /*officiel*/ , 'landscape' /*orientation*/ , 10 /*marge_gauche*/ , 10 /*marge_droite*/ , 10 /*marge_haut*/ , 10 /*marge_bas*/ , $couleur , $fond );
    $tableau_PDF->saisie_initialiser( $eleve_nb , $item_nb );
    // 1ère ligne : référence devoir, noms élèves
    $tableau_PDF->saisie_entete( $groupe_nom , $date_fr , $description , $DB_TAB_USER );
    // ligne suivantes : référence item, cases vides ou pleines
    $tab_scores = ($remplissage=='rempli') ? $tab_scores : NULL ;
    $tableau_PDF->saisie_cases_eleves( $DB_TAB_COMP , $DB_TAB_USER , $eleve_nb , $tab_scores );
    // On enregistre le PDF
    $fichier_nom = 'tableau_'.$remplissage.'_'.$tab_couleurs[$couleur].'_'.$fnom_export.'.pdf';
    FileSystem::ecrire_sortie_PDF( CHEMIN_DOSSIER_EXPORT.$fichier_nom , $tableau_PDF );
    // Affichage du lien
    exit('<a target="_blank" href="'.URL_DIR_EXPORT.$fichier_nom.'"><span class="file file_pdf">Tableau '.$remplissage.' (format <em>pdf</em>).</span></a>');
  }
  //
  // csv contenant un tableau de saisie vide ou plein
  //
  if($format=='csv')
  {
    $tab_conversion = array(
      ''     => ' ' ,
      'RR'   => '1' ,
      'R'    => '2' ,
      'V'    => '3' ,
      'VV'   => '4' ,
      'ABS'  => 'A' ,
      'DISP' => 'D' ,
      'NE'   => 'E' ,
      'NF'   => 'F' ,
      'NN'   => 'N' ,
      'NR'   => 'R' ,
      'REQ'  => 'P' ,
    );
    $csv_colonne_texte = array();
    // première colonne (références items) pour le CSV + dernière colonne (noms items) pour le CSV
    foreach($DB_TAB_COMP as $DB_ROW)
    {
      $item_ref    = $DB_ROW['item_ref'];
      $texte_socle = ($DB_ROW['entree_id']) ? ' [S]' : ' [–]';
      $texte_coef  = ' ['.$DB_ROW['item_coef'].']';
      $tab_scores[$DB_ROW['item_id']][0] = $DB_ROW['item_id'];
      $csv_colonne_texte[$DB_ROW['item_id']] = $item_ref.$texte_socle.$texte_coef.' '.$DB_ROW['item_nom'];
    }
    $separateur = ';';
    // première ligne (identifiants des élèves) + dernière ligne (noms prénoms des élèves)
    $csv_ligne_eleve_nom = $separateur;
    $csv_ligne_eleve_id  = $separateur;
    $csv_nb_colonnes = 0;
    foreach($DB_TAB_USER as $DB_ROW)
    {
      $csv_ligne_eleve_nom .= '"'.$DB_ROW['user_prenom'].' '.$DB_ROW['user_nom'].'"'.$separateur;
      $csv_ligne_eleve_id  .= $DB_ROW['user_id'].$separateur;
      $csv_nb_colonnes++;
    }
    $export_csv = $csv_ligne_eleve_id."\r\n";
    // première colonne (identifiants items) + cases centrales vides ou pleines + dernière colonne (noms items)
    foreach($tab_comp_id as $comp_id=>$val_comp)
    {
      $export_csv .= $tab_scores[$comp_id][0].$separateur;
      if($remplissage=='vierge')
      {
        $export_csv .= str_repeat($separateur,$csv_nb_colonnes);
      }
      else
      {
        foreach($tab_user_id as $user_id=>$val_user)
        {
          $export_csv .= $tab_conversion[$tab_scores[$comp_id][$user_id]].$separateur;
        }
      }
      $export_csv .= $csv_colonne_texte[$comp_id]."\r\n";
    }
    // Fin du csv
    $export_csv .= $csv_ligne_eleve_nom."\r\n\r\n";
    $export_csv .= $groupe_nom."\r\n".$date_fr."\r\n".$description."\r\n\r\n";
    $export_csv .= 'CODAGES AUTORISÉS : 1 2 3 4 A D E F N P R'."\r\n";
    // On enregistre le CSV
    $fichier_nom = 'saisie_deportee_'.$remplissage.'_'.$fnom_export.'.csv';
    FileSystem::ecrire_fichier( CHEMIN_DOSSIER_EXPORT.$fichier_nom , To::csv($export_csv) );
    // Affichage du lien
    exit('<a target="_blank" href="'.URL_DIR_EXPORT.$fichier_nom.'"><span class="file file_txt">Fichier '.$remplissage.' (format <em>csv</em>).</span></a>');
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Voir ou Archiver la répartition, nominative ou quantitative, des élèves par item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( in_array($action,array('voir_repart','archiver_repart')) && $devoir_id && $groupe_id && $date_fr ) // $description et $groupe_nom sont aussi transmis
{
  // liste des items
  $DB_TAB_ITEM = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_items( $devoir_id , TRUE /*with_lien*/ , TRUE /*with_coef*/ );
  // liste des élèves
  $DB_TAB_USER = DB_STRUCTURE_COMMUN::DB_lister_users_regroupement( 'eleve' /*profil*/ , TRUE /*statut*/ , $groupe_type , $groupe_id , 'alpha' /*eleves_ordre*/ );
  // Let's go
  $item_nb = count($DB_TAB_ITEM);
  if(!$item_nb)
  {
    exit('Aucun item n\'est associé à cette évaluation !');
  }
  $eleve_nb = count($DB_TAB_USER);
  if(!$eleve_nb)
  {
    exit('Aucun élève n\'est associé à cette évaluation !');
  }
  $tab_user_id = array(); // pas indispensable, mais plus lisible
  $tab_item_id = array(); // pas indispensable, mais plus lisible
  // noms prénoms des élèves
  foreach($DB_TAB_USER as $DB_ROW)
  {
    $tab_user_id[$DB_ROW['user_id']] = html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']);
  }
  // noms des items
  foreach($DB_TAB_ITEM as $DB_ROW)
  {
    $texte_socle = ($DB_ROW['entree_id']) ? ' [S]' : ' [–]';
    $texte_coef  = ' ['.$DB_ROW['item_coef'].']';
    $tab_item_id[$DB_ROW['item_id']] = array( $DB_ROW['item_ref'].$texte_socle.$texte_coef , $DB_ROW['item_nom'] , $DB_ROW['item_lien'] );
  }
  // tableaux utiles ou pour conserver les infos
  $tab_init_nominatif = array(
    'RR' => array() ,
    'R'  => array() ,
    'V'  => array() ,
    'VV' => array() ,
    'X'  => array() ,
  );
  $tab_init_quantitatif = array(
    'RR' => 0 ,
    'R'  => 0 ,
    'V'  => 0 ,
    'VV' => 0 ,
    'X'  => 0 ,
  );
  $tab_repartition_nominatif   = array();
  $tab_repartition_quantitatif = array();
  $tab_selection_nominatif     = array();
  // initialisation
  foreach($tab_item_id as $item_id=>$tab_infos_item)
  {
    $tab_repartition_nominatif[$item_id]   = $tab_init_nominatif;
    $tab_repartition_quantitatif[$item_id] = $tab_init_quantitatif;
    $tab_selection_nominatif[$item_id]     = $tab_init_nominatif;
  }
  // remplissage
  $DB_TAB = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_saisies( $devoir_id , FALSE /*with_REQ*/ );
  foreach($DB_TAB as $DB_ROW)
  {
    // Test pour éviter les pbs des élèves changés de groupes ou des items modifiés en cours de route
    if( isset($tab_user_id[$DB_ROW['eleve_id']]) && isset($tab_item_id[$DB_ROW['item_id']]) )
    {
      $note  = isset($tab_init_quantitatif[$DB_ROW['saisie_note']]) ? $DB_ROW['saisie_note'] : 'X' ; // Regrouper ce qui n'est pas RR R V VV
      $eleve = isset($tab_init_quantitatif[$DB_ROW['saisie_note']]) ? $tab_user_id[$DB_ROW['eleve_id']] : $tab_user_id[$DB_ROW['eleve_id']].' ('.$DB_ROW['saisie_note'].')' ; // Ajouter la note si hors RR R V VV
      $checkbox_user = '<input type="checkbox" name="id_user[]" value="'.$DB_ROW['eleve_id'].'" />';
      $checkbox_req  = '<input type="checkbox" name="id_req[]" value="'.$DB_ROW['eleve_id'].'x'.$DB_ROW['item_id'].'" />';
      $tab_repartition_nominatif[$DB_ROW['item_id']][$note][] = $eleve;
      $tab_repartition_quantitatif[$DB_ROW['item_id']][$note]++;
      $tab_selection_nominatif[$DB_ROW['item_id']][$note][] = $checkbox_user.$checkbox_req.' '.$eleve;
    }
  }
  //
  // Sorties HTML (affichage direct + page avec cases à cocher)
  //
  if($action=='voir_repart')
  {
    // 1e ligne : référence des codes
    $affichage_repartition_head = '<th class="nu"></th>';
    foreach($tab_init_quantitatif as $note=>$vide)
    {
      $affichage_repartition_head .= ($note!='X') ? '<th>'.Html::note_image($note,'','',FALSE).'</th>' : '<th>Autre</th>' ;
    }
    // PARTIE 1 : assemblage / affichage du tableau avec la répartition quantitative
    echo'<thead><tr>'.$affichage_repartition_head.'</tr></thead><tbody>';
    foreach($tab_item_id as $item_id=>$tab_infos_item)
    {
      $texte_lien_avant = ($tab_infos_item[2]) ? '<a target="_blank" href="'.html($tab_infos_item[2]).'">' : '';
      $texte_lien_apres = ($tab_infos_item[2]) ? '</a>' : '';
      echo'<tr>';
      echo'<th><b>'.$texte_lien_avant.html($tab_infos_item[0]).$texte_lien_apres.'</b><br />'.html($tab_infos_item[1]).'</th>';
      foreach($tab_repartition_quantitatif[$item_id] as $code=>$note_nb)
      {
        echo'<td style="font-size:'.round(75+100*$note_nb/$eleve_nb).'%">'.round(100*$note_nb/$eleve_nb).'%</td>';
      }
      echo'</tr>';
    }
    echo'</tbody>';
    // Séparateur
    echo'<SEP>';
    // PARTIE 2 : assemblage / affichage du tableau avec la répartition nominative
    echo'<thead><tr>'.$affichage_repartition_head.'</tr></thead><tbody>';
    foreach($tab_item_id as $item_id=>$tab_infos_item)
    {
      $texte_lien_avant = ($tab_infos_item[2]) ? '<a target="_blank" href="'.html($tab_infos_item[2]).'">' : '';
      $texte_lien_apres = ($tab_infos_item[2]) ? '</a>' : '';
      echo'<tr>';
      echo'<th><b>'.$texte_lien_avant.html($tab_infos_item[0]).$texte_lien_apres.'</b><br />'.html($tab_infos_item[1]).'</th>';
      foreach($tab_repartition_nominatif[$item_id] as $code=>$tab_eleves)
      {
        echo'<td>'.implode('<br />',$tab_eleves).'</td>';
      }
      echo'</tr>';
    }
    echo'</tbody>';
    // Séparateur
    echo'<SEP>';
    // PARTIE 3 : assemblage de la page HTML avec cases à cocher
    $affichage_HTML  = '<style type="text/css">'.$_SESSION['CSS'].'</style>'.NL;
    $affichage_HTML .= '<h1>Exploitation d\'une évaluation</h1>'.NL;
    $affichage_HTML .= '<h2>'.$groupe_nom.' | '.$date_fr.' | '.$description.'</h2>'.NL;
    $affichage_HTML .= '<hr />'.NL;
    $affichage_HTML .= '<form id="form_synthese" action="#" method="post">'.NL;
    $affichage_HTML .= HtmlForm::afficher_synthese_exploitation('eleves + eleves-items').NL;
    $affichage_HTML .= '<table class="eval_exploitation">'.NL;
    $affichage_HTML .= '<thead><tr>'.$affichage_repartition_head.'</tr></thead>'.NL;
    $affichage_HTML .= '<tbody>';
    foreach($tab_item_id as $item_id=>$tab_infos_item)
    {
      $affichage_HTML .= '<tr>';
      $affichage_HTML .= '<th><b>'.html($tab_infos_item[0]).'</b><br />'.html($tab_infos_item[1]).'</th>';
      foreach($tab_selection_nominatif[$item_id] as $code=>$tab_eleves)
      {
        $affichage_HTML .= '<td>'.implode('<br />',$tab_eleves).'</td>';
      }
      $affichage_HTML .= '</tr>';
    }
    $affichage_HTML .= '</tbody>'.NL;
    $affichage_HTML .= '</table>'.NL;
    $affichage_HTML .= '</form>'.NL;
    // On enregistre la sortie HTML
    $fichier_nom = 'evaluation_'.$devoir_id.'_'.fabriquer_fin_nom_fichier__date_et_alea();
    FileSystem::ecrire_fichier(CHEMIN_DOSSIER_EXPORT.$fichier_nom.'.html' , $affichage_HTML );
    // Affichage de l'adresse
    echo'./releve_html.php?fichier='.$fichier_nom;
    // Terminé !
    exit();
  }
  //
  // Sortie PDF
  //
  elseif( ($action=='archiver_repart') && $repartition_type && $couleur && $fond )
  {
    Form::save_choix('evaluation_statistiques');
    if($repartition_type=='quantitative')
    {
      $tableau_PDF = new PDF_evaluation_tableau( FALSE /*officiel*/ , 'portrait' /*orientation*/ , 10 /*marge_gauche*/ , 10 /*marge_droite*/ , 10 /*marge_haut*/ , 10 /*marge_bas*/ , $couleur , $fond );
      $tableau_PDF->repartition_quantitative_initialiser($item_nb);
      // 1ère ligne : référence du devoir et des codes
      $tableau_PDF->repartition_quantitative_entete( $groupe_nom , $date_fr , $description , $tab_init_quantitatif );
      // ligne suivantes : référence item, cases répartition quantitative
      foreach($tab_item_id as $item_id=>$tab_infos_item)
      {
        // ligne de répartition pour 1 item : référence item
        $tableau_PDF->saisie_reference_item( $tab_infos_item[0] , $tab_infos_item[1] );
        // ligne de répartition pour 1 item : cases répartition quantitative
        $tableau_PDF->repartition_quantitative_cases_eleves( $tab_repartition_quantitatif[$item_id] , $eleve_nb );
      }
    }
    elseif($repartition_type=='nominative')
    {
      $tableau_PDF = new PDF_evaluation_tableau( FALSE /*officiel*/ , 'landscape' /*orientation*/ , 10 /*marge_gauche*/ , 10 /*marge_droite*/ , 10 /*marge_haut*/ , 10 /*marge_bas*/ , $couleur , $fond );
      // il faut additionner le nombre maxi d'élèves par case de chaque item (sans descendre en dessous de 4 pour avoir la place d'afficher l'intitulé de l'item) afin de prévoir le nb de lignes nécessaires
      $somme = 0;
      foreach($tab_repartition_quantitatif as $item_id => $tab_effectifs)
      {
        $somme += max(4,max($tab_effectifs));
      }
      $tableau_PDF->repartition_nominative_initialiser($somme);
      foreach($tab_item_id as $item_id=>$tab_infos_item)
      {
        // 1ère ligne : nouvelle page si besoin + référence du devoir et des codes si besoin
        $tableau_PDF->repartition_nominative_entete( $groupe_nom , $date_fr , $description , $tab_init_quantitatif , $tab_repartition_quantitatif[$item_id] );
        // ligne de répartition pour 1 item : référence item
        $tableau_PDF->saisie_reference_item( $tab_infos_item[0] , $tab_infos_item[1] );
        // ligne de répartition pour 1 item : cases répartition nominative
        $tableau_PDF->repartition_nominative_cases_eleves( $tab_repartition_nominatif[$item_id] );
      }
    }
    // On enregistre le PDF
    $tab_couleurs = array( 'oui'=>'couleur' , 'non'=>'monochrome' );
    $fichier_nom = 'repartition_'.$repartition_type.'_'.$tab_couleurs[$couleur].'_'.$fnom_export.'.pdf';
    FileSystem::ecrire_sortie_PDF( CHEMIN_DOSSIER_EXPORT.$fichier_nom , $tableau_PDF );
    exit('<a target="_blank" href="'.URL_DIR_EXPORT.$fichier_nom.'"><span class="file file_pdf">Répartition '.$repartition_type.' (format <em>pdf</em>).</span></a>');
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Mettre à jour l'ordre des items d'une évaluation
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='enregistrer_ordre') && $devoir_id && count($tab_id) )
{
  // Tester les droits
  $proprio_id = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_id( $devoir_id );
  if($proprio_id==$_SESSION['USER_ID'])
  {
    $niveau_droit = 4; // propriétaire
  }
  elseif($profs_liste) // forcément
  {
    $search_liste = '_'.$profs_liste.'_';
    if( strpos( $search_liste, '_m'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      $niveau_droit = 3; // modifier
    }
    elseif( strpos( $search_liste, '_s'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      exit('Erreur : droit insuffisant attribué sur le devoir n°'.$devoir_id.' (niveau 2 au lieu de 3) !'); // saisir
    }
    elseif( strpos( $search_liste, '_v'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      exit('Erreur : droit insuffisant attribué sur le devoir n°'.$devoir_id.' (niveau 1 au lieu de 3) !'); // voir
    }
    else
    {
      exit('Erreur : droit attribué sur le devoir n°'.$devoir_id.' non trouvé !');
    }
  }
  else
  {
    exit('Erreur : vous n\'êtes ni propriétaire ni bénéficiaire de droits sur le devoir n°'.$devoir_id.' !');
  }
  // Mise à jour dans la base
  DB_STRUCTURE_PROFESSEUR::DB_modifier_ordre_item( $devoir_id , $tab_id );
  exit('<ok>');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Mettre à jour les items acquis par les élèves à une évaluation
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='enregistrer_saisie') && $devoir_id && $date_fr && $date_visible && count($tab_notes) )
{
  // Tester les droits
  $proprio_id = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_id( $devoir_id );
  if($proprio_id==$_SESSION['USER_ID'])
  {
    $niveau_droit = 4; // propriétaire
  }
  elseif($profs_liste) // forcément
  {
    $search_liste = '_'.$profs_liste.'_';
    if( strpos( $search_liste, '_m'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      $niveau_droit = 3; // modifier
    }
    elseif( strpos( $search_liste, '_s'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      $niveau_droit = 2; // saisir
    }
    elseif( strpos( $search_liste, '_v'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      exit('Erreur : droit insuffisant attribué sur le devoir n°'.$devoir_id.' (niveau 1 au lieu de 2) !'); // voir
    }
    else
    {
      exit('Erreur : droit attribué sur le devoir n°'.$devoir_id.' non trouvé !');
    }
  }
  else
  {
    exit('Erreur : vous n\'êtes ni propriétaire ni bénéficiaire de droits sur le devoir n°'.$devoir_id.' !');
  }
  // On y va
  $nb_saisies_possibles  = 0;
  $nb_saisies_effectuees = 0;
  // Tout est transmis : il faut comparer avec le contenu de la base pour ne mettre à jour que ce dont il y a besoin
  // On récupère les notes transmises dans $tab_post
  $tab_post = array();
  foreach($tab_notes as $key_note)
  {
    list( $key , $note ) = explode('_',$key_note);
    list( $item_id , $eleve_id ) = explode('x',$key);
    if( (int)$item_id && (int)$eleve_id )
    {
      $tab_post[$item_id.'x'.$eleve_id] = $note;
      $nb_saisies_possibles++;
      $nb_saisies_effectuees += ( ($note!='X') && ($note!='REQ') ) ? 1 : 0 ;
    }
  }
  // On recupère le contenu de la base déjà enregistré pour le comparer ; on remplit au fur et à mesure $tab_nouveau_modifier / $tab_nouveau_supprimer
  // $tab_demande_supprimer sert à supprimer des demandes d'élèves dont on met une note.
  $tab_nouveau_modifier = array();
  $tab_nouveau_supprimer = array();
  $tab_demande_supprimer = array();
  $DB_TAB = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_saisies( $devoir_id , TRUE /*with_REQ*/ );
  foreach($DB_TAB as $DB_ROW)
  {
    $key = $DB_ROW['item_id'].'x'.$DB_ROW['eleve_id'];
    if(isset($tab_post[$key])) // Test nécessaire si élève ou item évalués dans ce devoir, mais retiré depuis (donc non transmis dans la nouvelle saisie, mais à conserver).
    {
      if($tab_post[$key]!=$DB_ROW['saisie_note'])
      {
        if($tab_post[$key]=='X')
        {
          // valeur de la base à supprimer... sauf en cas d'évaluation partagée :
          // en effet, dans ce cas, plusieurs collègues peuvent co-saisir en même temps,
          // il est plus prudent de ne pas écraser des notes qui viendraient d'être enregistrées par des collègues,
          // quitte à se passer de la possibilité de retirer une note saisie par un collègue,
          // dans ce cas il faut d'abord la modifier (la modification restant possible) pour s'attribuer la paternité de la saisie, avant de la supprimer.
          // (mais bon, on touche là à une situation rarissime..)
          if( $DB_ROW['prof_id']==$_SESSION['USER_ID'])
          {
            $tab_nouveau_supprimer[$key] = $key;
          }
        }
        else
        {
          // valeur de la base à modifier
          $tab_nouveau_modifier[$key] = $tab_post[$key];
          if($DB_ROW['saisie_note']=='REQ')
          {
            // demande d'évaluation à supprimer
            $tab_demande_supprimer[$key] = $key;
          }
        }
      }
      unset($tab_post[$key]);
    }
  }
  // Il reste dans $tab_post les données à ajouter (mises dans $tab_nouveau_ajouter) et les données qui ne servent pas (non enregistrées et non saisies)
  $tab_nouveau_ajouter = array_filter($tab_post,'sans_rien');
  // Il n'y a plus qu'à mettre à jour la base
  if( !count($tab_nouveau_ajouter) && !count($tab_nouveau_modifier) && !count($tab_nouveau_supprimer) )
  {
    exit('Aucune modification détectée !');
  }
  // L'information associée à la note comporte le nom de l'évaluation + celui du professeur (c'est une information statique, conservée sur plusieurs années)
  $date_mysql         = convert_date_french_to_mysql($date_fr);
  $date_visible_mysql = ($date_visible=='00/00/0000') ? $date_mysql : convert_date_french_to_mysql($date_visible);
  $info = $description.' ('.afficher_identite_initiale($_SESSION['USER_NOM'],FALSE,$_SESSION['USER_PRENOM'],TRUE,$_SESSION['USER_GENRE']).')';
  foreach($tab_nouveau_ajouter as $key => $note)
  {
    list($item_id,$eleve_id) = explode('x',$key);
    DB_STRUCTURE_PROFESSEUR::DB_ajouter_saisie( $_SESSION['USER_ID'] , $eleve_id , $devoir_id , $item_id , $date_mysql , $note , $info , $date_visible_mysql );
  }
  foreach($tab_nouveau_modifier as $key => $note)
  {
    list($item_id,$eleve_id) = explode('x',$key);
    DB_STRUCTURE_PROFESSEUR::DB_modifier_saisie( $_SESSION['USER_ID'] , $eleve_id , $devoir_id , $item_id , $note , $info );
  }
  foreach($tab_nouveau_supprimer as $key => $key)
  {
    list($item_id,$eleve_id) = explode('x',$key);
    DB_STRUCTURE_PROFESSEUR::DB_supprimer_saisie( $eleve_id , $devoir_id , $item_id );
  }
  foreach($tab_demande_supprimer as $key => $key)
  {
    list($item_id,$eleve_id) = explode('x',$key);
    DB_STRUCTURE_DEMANDE::DB_supprimer_demande_precise_eleve_item( $eleve_id , $item_id );
  }
  $remplissage_nombre   = $nb_saisies_effectuees.'/'.$nb_saisies_possibles ;
  $remplissage_class    = (!$nb_saisies_effectuees) ? 'br' : ( ($nb_saisies_effectuees<$nb_saisies_possibles) ? 'bj' : 'bv' ) ;
  $remplissage_class2   = ($fini=='oui') ? ' bf' : '' ;
  $remplissage_contenu  = ($fini=='oui') ? '<span>terminé</span><i>'.$remplissage_nombre.'</i>' : '<span>'.$remplissage_nombre.'</span><i>terminé</i>' ;
  $remplissage_lien1    = '<a href="#fini" class="fini" title="Cliquer pour indiquer (ou pas) qu\'il n\'y a plus de saisies à effectuer.">';
  $remplissage_lien2    = '</a>';
  exit('<td class="'.$remplissage_class.$remplissage_class2.'">'.$remplissage_lien1.$remplissage_contenu.$remplissage_lien2.'</td>');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Imprimer un cartouche d'une évaluation
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='imprimer_cartouche') && $devoir_id && $groupe_id && $date_fr && $cart_detail && in_array($cart_cases_nb,array(1,5)) && $cart_contenu && $orientation && $marge_min && $couleur && $fond && in_array($eleves_ordre,array('alpha','classe')) )
{
  Form::save_choix('evaluation_cartouche');
  $with_nom    = (substr($cart_contenu,0,8)=='AVEC_nom')  ? TRUE : FALSE ;
  $with_result = (substr($cart_contenu,9)=='AVEC_result') ? TRUE : FALSE ;
  // liste des items
  $DB_TAB_COMP = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_items( $devoir_id , FALSE /*with_lien*/ , TRUE /*with_coef*/ );
  // liste des élèves
  $DB_TAB_USER = DB_STRUCTURE_COMMUN::DB_lister_users_regroupement( 'eleve' /*profil*/ , TRUE /*statut*/ , $groupe_type , $groupe_id , $eleves_ordre );
  // Let's go
  if(empty($DB_TAB_COMP))
  {
    exit('Aucun item n\'est associé à cette évaluation !');
  }
  if(empty($DB_TAB_USER))
  {
    exit('Aucun élève n\'est associé à cette évaluation !');
  }
  $tab_result  = array(); // tableau bi-dimensionnel [n°ligne=id_item][n°colonne=id_user]
  $tab_user_id = array(); // pas indispensable, mais plus lisible
  $tab_comp_id = array(); // pas indispensable, mais plus lisible
  $tab_user_nb_req = array(); // pour retenir le nb d'items par utilisateur : variable et utile uniquement si cartouche avec les demandes d'évaluations 
  $tab_user_comm   = array(); // pour retenir les commentaires écrits pour par élève
  // enregistrer noms prénoms des élèves
  foreach($DB_TAB_USER as $DB_ROW)
  {
    $tab_user_id[$DB_ROW['user_id']] = ($with_nom) ? html($DB_ROW['user_prenom'].' '.$DB_ROW['user_nom']) : '' ;
    $tab_user_nb_req[$DB_ROW['user_id']] = 0 ;
  }
  // enregistrer refs noms items
  foreach($DB_TAB_COMP as $DB_ROW)
  {
    $item_ref = $DB_ROW['item_ref'];
    $texte_socle = ($DB_ROW['entree_id']) ? '[S] ' : '[–] ';
    $texte_coef  = '['.$DB_ROW['item_coef'].'] ';
    $tab_comp_id[$DB_ROW['item_id']] = array($item_ref,$texte_socle.$texte_coef.$DB_ROW['item_nom']);
  }
  // résultats vierges
  foreach($tab_user_id as $user_id=>$val_user)
  {
    $tab_user_comm[$user_id] = NULL;
    foreach($tab_comp_id as $comp_id=>$val_comp)
    {
      $tab_result[$comp_id][$user_id] = '';
    }
  }
  // compléter si demandé avec les résultats et/ou les demandes d'évaluations
  if($with_result || $only_req)
  {
    $DB_TAB = DB_STRUCTURE_PROFESSEUR::DB_lister_devoir_saisies( $devoir_id , $only_req );
    foreach($DB_TAB as $DB_ROW)
    {
      // Test pour éviter les pbs des élèves changés de groupes ou des items modifiés en cours de route
      if(isset($tab_result[$DB_ROW['item_id']][$DB_ROW['eleve_id']]))
      {
        $valeur = ($with_result) ? $DB_ROW['saisie_note'] : ( ($DB_ROW['saisie_note']) ? 'REQ' : '' ) ;
        if($valeur)
        {
          $tab_result[$DB_ROW['item_id']][$DB_ROW['eleve_id']] = $valeur ;
          $tab_user_nb_req[$DB_ROW['eleve_id']]++;
        }
      }
    }
  }
  // liste des commentaires audio ou texte, si demandé avec les résultats
  if($with_result)
  {
    $DB_TAB_MSG = DB_STRUCTURE_COMMENTAIRE::DB_lister_devoir_commentaires($devoir_id);
    if(!empty($DB_TAB_MSG))
    {
      foreach($DB_TAB_MSG as $DB_ROW)
      {
        if($DB_ROW['jointure_texte'])
        {
          // On récupère le contenu du fichier
          $msg_url = $DB_ROW['jointure_texte'];
          if(strpos($msg_url,URL_DIR_SACOCHE)===0)
          {
            $fichier_chemin = url_to_chemin($msg_url);
            $msg_data = is_file($fichier_chemin) ? file_get_contents($fichier_chemin) : 'Erreur : fichier avec le contenu du commentaire non trouvé.' ;
          }
          else
          {
            $msg_data = cURL::get_contents($msg_url);
          }
          $tab_user_comm[$DB_ROW['eleve_id']] = $msg_data;
        }
      }
    }
  }
  // On attaque l'élaboration des sorties HTML, CSV, TEX et PDF
  $cartouche_HTM = '<hr />';
  $cartouche_HTM.= '<a target="_blank" href="'.URL_DIR_EXPORT.'cartouche_'.$fnom_export.'.pdf"><span class="file file_pdf">Cartouches &rarr; Archiver / Imprimer (format <em>pdf</em>).</span></a><br />';
  $cartouche_HTM.= '<a target="_blank" href="./force_download.php?fichier=cartouche_'.$fnom_export.'.csv"><span class="file file_txt">Cartouches &rarr; Récupérer / Manipuler (fichier <em>csv</em> pour tableur).</span></a><br />';
  $cartouche_HTM.= '<a target="_blank" href="'.URL_DIR_EXPORT.'cartouche_'.$fnom_export.'.tex"><span class="file file_tex">Cartouches &rarr; Récupérer / Manipuler (fichier <em>LaTeX</em> pour connaisseurs).</span></a>';
  $cartouche_CSV = '';
  $cartouche_TEX = '';
  $separateur  = ';';
  $tab_codes = array(
    'RR' => TRUE ,
    'R'  => TRUE ,
    'V'  => TRUE ,
    'VV' => TRUE ,
    'X'  => FALSE ,
  );
  // Appel de la classe et définition de qqs variables supplémentaires pour la mise en page PDF
  $item_nb = count($tab_comp_id);
  if(!$only_req)
  {
    $tab_user_nb_req = array_fill_keys( array_keys($tab_user_nb_req) , $item_nb );
  }
  $cartouche_PDF = new PDF_evaluation_cartouche( FALSE /*officiel*/ , $orientation , $marge_min /*marge_gauche*/ , $marge_min /*marge_droite*/ , $marge_min /*marge_haut*/ , $marge_min /*marge_bas*/ , $couleur , $fond , 'oui' /*legende*/ );
  $cartouche_PDF->initialiser($cart_detail,$item_nb,$cart_cases_nb);
  if($cart_detail=='minimal')
  {
    // dans le cas d'un cartouche minimal...
    foreach($tab_user_id as $user_id=>$val_user)
    {
      if($tab_user_nb_req[$user_id])
      {
        $colonnes_nb = $tab_user_nb_req[$user_id];
        $lignes_comm = ($tab_user_comm[$user_id]) ? max( 2 , ceil(mb_strlen($tab_user_comm[$user_id])/125) ) : 0 ;
        $lignes_nb   = 1 + 2 + $cart_cases_nb + $lignes_comm; // marge incluse
        $texte_entete = $date_fr.' - '.$description.' - '.$val_user;
        $case_vide    = ($cart_cases_nb==1) ? '' : '<th class="nu"></th>' ;
        $cartouche_HTM .= '<table class="bilan"><thead><tr>'.$case_vide.'<th colspan="'.$colonnes_nb.'">'.html($texte_entete).'</th></tr></thead><tbody>';
        $cartouche_CSV .= $texte_entete."\r\n";
        $cartouche_TEX .= To::latex($texte_entete)."\r\n";
        $cartouche_PDF->entete( $texte_entete , $lignes_nb , $cart_detail , $cart_cases_nb );
        if($cart_cases_nb==1)
        {
          // ... avec une case à remplir
          $rows_htm = array( 'item' => '' , 'note'=> '' );
          $rows_csv = array( 'item' => '' , 'note'=> '' );
          $rows_tex = array( 'item' => '' , 'note'=> '' );
        }
        else
        {
          // ... avec 5 cases dont une à cocher
          $rows_htm = array( 'item' => '<td class="nu"></td>' );
          $rows_csv = array( 'item' => $separateur );
          $rows_tex = array( 'item' => ' & ' );
          $colonnes_nb += 1;
          foreach($tab_codes as $note_code => $is_note )
          {
            $rows_htm[$note_code] = ($is_note) ? '<td class="hc">'.Html::note_image($note_code,'','',FALSE).'</td>' : '<td class="hc">autre</td>';
            $rows_csv[$note_code] = ($is_note) ? '"'.To::note_texte($note_code).'"'.$separateur                               : '"autre"'.$separateur;
            $rows_tex[$note_code] = ($is_note) ? '\begin{tabular}{c}'.To::note_texte($note_code).'\end{tabular} & '           : '\begin{tabular}{c}autre\end{tabular} ';
          }
        }
        foreach($tab_comp_id as $comp_id => $tab_val_comp)
        {
          if( ($only_req==FALSE) || ($tab_result[$comp_id][$user_id]) )
          {
            $cols_nb++;
            $note = ($tab_result[$comp_id][$user_id]!='REQ') ? $tab_result[$comp_id][$user_id] : '' ; // Si on voulait récupérer les items ayant fait l'objet d'une demande d'évaluation, il n'y a pour autant pas lieu d'afficher les paniers sur les cartouches.
            list($ref_matiere,$ref_suite) = explode('.',$tab_val_comp[0],2);
            $rows_htm['item'] .= '<td class="hc">'.html($tab_val_comp[0]).'</td>';
            $rows_csv['item'] .= '"'.$tab_val_comp[0].'"'.$separateur;
            $rows_tex['item'] .= '\begin{tabular}{c}'.To::latex($ref_matiere).'\\\\'.To::latex($ref_suite).'\end{tabular} & ';
            if($cart_cases_nb==1)
            {
              // ... avec une case à remplir
              $rows_htm['note'] .= '<td class="hc">'.Html::note_image($note,$date_fr,$description,FALSE).'</td>';
              $rows_csv['note'] .= '"'.To::note_texte($note).'"'.$separateur;
              $rows_tex['note'] .= '\begin{tabular}{c}'.To::note_texte($note).'\end{tabular} & ';
            }
            else
            {
              // ... avec 5 cases dont une à cocher
              foreach($tab_codes as $note_code => $is_note )
              {
                if($is_note)
                {
                  $coche = ($note_code==$note) ? 'XXX' : NULL ;
                }
                else
                {
                  $coche = ( $note && !isset($tab_codes[$note]) ) ? $note : NULL ;
                }
                $rows_htm[$note_code] .= ($coche) ? '<td class="hc">'.$coche.'</td>'               : '<td class="hc">&nbsp;</td>';
                $rows_csv[$note_code] .= ($coche) ? '"'.$coche.'"'.$separateur                     : $separateur;
                $rows_tex[$note_code] .= ($coche) ? '\begin{tabular}{c}'.$coche.'\end{tabular} & ' : '& ';
              }
            }
            $cartouche_PDF->minimal_competence( $tab_val_comp[0] , $note , $cart_cases_nb );
          }
        }
        // Enlever un '& ' surnuméraire
        foreach($rows_tex as $note_code => $tex_contenu)
        {
          $rows_tex[$note_code] = mb_substr($tex_contenu,0,-2);
        }
        // Commentaire écrit
        $row_htm_comm = '';
        $row_csv_comm = '';
        $row_tex_comm = '';
        if($lignes_comm)
        {
          $row_htm_comm = '<tr><td colspan="'.$colonnes_nb.'"><div class="appreciation">'.html($tab_user_comm[$user_id]).'</div></td></tr>';
          $row_csv_comm = $tab_user_comm[$user_id]."\r\n";
          $row_tex_comm = '\multicolumn{'.$colonnes_nb.'}{|l|}{'.To::latex($tab_user_comm[$user_id]).'} \\\\'."\r\n".'\hline'."\r\n";
        }
        $cartouche_HTM .= '<tr>'.implode('</tr><tr>',$rows_htm).'</tr>'.$row_htm_comm.'</tbody></table>';
        $cartouche_CSV .= implode("\r\n",$rows_csv)."\r\n".$row_csv_comm."\r\n";
        $cartouche_TEX .= '\begin{center}'."\r\n".'\begin{tabular}{|'.str_repeat('c|',$colonnes_nb).'}'."\r\n".'\hline'."\r\n".implode(' \\\\'."\r\n".'\hline'."\r\n",$rows_tex).' \\\\'."\r\n".'\hline'.$row_tex_comm."\r\n".'\end{tabular}'."\r\n".'\end{center}'."\r\n\r\n";
        $cartouche_PDF->commentaire_interligne( $cart_cases_nb+1 /*decalage_nb_lignes*/ , $tab_user_comm[$user_id] /*commentaire éventuel*/ , $lignes_comm );
      }
    }
  }
  elseif($cart_detail=='complet')
  {
    // dans le cas d'un cartouche complet...
    foreach($tab_user_id as $user_id=>$val_user)
    {
      if($tab_user_nb_req[$user_id])
      {
        $lignes_comm = ($tab_user_comm[$user_id]) ? max( 2 , ceil(mb_strlen($tab_user_comm[$user_id])/125) ) : 0 ;
        $colonnes_nb = ($cart_cases_nb==1) ? 3 : 2 ;
        $lignes_nb   = 1 + 1 + $tab_user_nb_req[$user_id] + $lignes_comm ; // marge incluse
        $texte_entete = $date_fr.' - '.$description.' - '.$val_user;
        if($cart_cases_nb==1)
        {
          // ... avec une case à remplir
          $cartouche_HTM .= '<table class="bilan"><thead><tr><th colspan="'.$colonnes_nb.'">'.html($texte_entete).'</th></tr></thead><tbody>';
          $cartouche_CSV .= $texte_entete."\r\n";
          $cartouche_TEX .= To::latex($texte_entete)."\r\n".'\begin{center}'."\r\n".'\begin{tabular}{|c|l|p{2em}|}'."\r\n".'\hline'."\r\n";
        }
        else
        {
          // ... avec 5 cases dont une à cocher
          $cols_htm = '';
          $cols_csv = '';
          $cols_tex = '';
          foreach($tab_codes as $note_code => $is_note )
          {
            $cols_htm .= ($is_note) ? '<td class="hc">'.Html::note_image($note_code,'','',FALSE).'</td>' : '<td class="hc">autre</td>';
            $cols_csv .= ($is_note) ? '"'.To::note_texte($note_code).'"'.$separateur                               : '"autre"'.$separateur;
            $cols_tex .= ($is_note) ? '\begin{tabular}{c}'.To::note_texte($note_code).'\end{tabular} & '           : '\begin{tabular}{c}autre\end{tabular} ';
          }
          $cartouche_HTM .= '<table class="bilan"><thead><tr><th colspan="'.$colonnes_nb.'">'.html($texte_entete).'</th>'.$cols_htm.'</tr></thead><tbody>';
          $cartouche_CSV .= $texte_entete.$separateur.$separateur.$cols_csv."\r\n";
          $cartouche_TEX .= To::latex($texte_entete)."\r\n".'\begin{center}'."\r\n".'\begin{tabular}{|c|l|'.str_repeat('p{2em}|',5).'}'."\r\n".'\hline'."\r\n";
          $cartouche_TEX .= ' & & '.$cols_tex.' \\\\'."\r\n".'\hline'."\r\n";
        }
        $cartouche_PDF->entete( $texte_entete , $lignes_nb , $cart_detail , $cart_cases_nb );
        foreach($tab_comp_id as $comp_id=>$tab_val_comp)
        {
          if( ($only_req==FALSE) || ($tab_result[$comp_id][$user_id]) )
          {
            $note = ($tab_result[$comp_id][$user_id]!='REQ') ? $tab_result[$comp_id][$user_id] : '' ; // Si on voulait récupérer les items ayant fait l'objet d'une demande d'évaluation, il n'y a pour autant pas lieu d'afficher les paniers sur les cartouches.
            if($cart_cases_nb==1)
            {
              // ... avec une case à remplir
              $cartouche_HTM .= '<tr><td>'.html($tab_val_comp[0]).'</td><td>'.html($tab_val_comp[1]).'</td><td>'.Html::note_image($note,$date_fr,$description,FALSE).'</td></tr>';
              $cartouche_CSV .= '"'.$tab_val_comp[0].'"'.$separateur.'"'.$tab_val_comp[1].'"'.$separateur.'"'.To::note_texte($note).'"'."\r\n";
              $cartouche_TEX .= To::latex($tab_val_comp[0]).' & '.To::latex($tab_val_comp[1]).' & '.To::note_texte($note).' \\\\'."\r\n".'\hline'."\r\n";
            }
            else
            {
              // ... avec 5 cases dont une à cocher
              $cartouche_HTM .= '<tr><td>'.html($tab_val_comp[0]).'</td><td>'.html($tab_val_comp[1]).'</td>';
              $cartouche_CSV .= '"'.$tab_val_comp[0].'"'.$separateur.'"'.$tab_val_comp[1].'"'.$separateur;
              $cartouche_TEX .= To::latex($tab_val_comp[0]).' & '.To::latex($tab_val_comp[1]);
              foreach($tab_codes as $note_code => $is_note )
              {
                $colonnes_nb++;
                if($is_note)
                {
                  $coche = ($note_code==$note) ? 'XXX' : NULL ;
                }
                else
                {
                  $coche = ( $note && !isset($tab_codes[$note]) ) ? $note : NULL ;
                }
                $cartouche_HTM .= ($coche) ? '<td class="hc">'.$coche.'</td>'               : '<td class="hc">&nbsp;</td>';
                $cartouche_CSV .= ($coche) ? '"'.$coche.'"'.$separateur                     : $separateur;
                $cartouche_TEX .= ($coche) ? ' & \begin{tabular}{c}'.$coche.'\end{tabular}' : ' &';
              }
              $cartouche_HTM .= '</tr>';
              $cartouche_CSV .= "\r\n";
              $cartouche_TEX .= ' \\\\'."\r\n".'\hline'."\r\n";
            }
            $cartouche_PDF->complet_competence( $tab_val_comp[0] , $tab_val_comp[1] , $note , $cart_cases_nb );
          }
        }
        // Commentaire écrit
        if($lignes_comm)
        {
          $cartouche_HTM .= '<tr><td colspan="'.$colonnes_nb.'"><div class="appreciation">'.html($tab_user_comm[$user_id]).'</div></td></tr>';
          $cartouche_CSV .= $tab_user_comm[$user_id]."\r\n";
          $cartouche_TEX .= '\multicolumn{'.$colonnes_nb.'}{|l|}{'.To::latex($tab_user_comm[$user_id]).'} \\\\'."\r\n".'\hline'."\r\n";
        }
        $cartouche_HTM .= '</tbody></table>';
        $cartouche_CSV .= "\r\n";
        $cartouche_TEX .= '\end{tabular}'."\r\n".'\end{center}'."\r\n\r\n";
        $cartouche_PDF->commentaire_interligne( 0 /*decalage_nb_lignes*/ , $tab_user_comm[$user_id] /*commentaire éventuel*/ , $lignes_comm );
      }
    }
  }
  // On archive le cartouche dans un fichier csv
  FileSystem::ecrire_fichier(    CHEMIN_DOSSIER_EXPORT.'cartouche_'.$fnom_export.'.csv' , To::csv($cartouche_CSV) );
  // On archive le cartouche dans un fichier tex
  FileSystem::ecrire_fichier(    CHEMIN_DOSSIER_EXPORT.'cartouche_'.$fnom_export.'.tex' , $cartouche_TEX );
  // On archive le cartouche dans un fichier pdf
  FileSystem::ecrire_sortie_PDF( CHEMIN_DOSSIER_EXPORT.'cartouche_'.$fnom_export.'.pdf' , $cartouche_PDF );
  // Affichage
  exit($cartouche_HTM);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Traiter une demande d'importation d'une saisie déportée ; on n'enregistre rien, on ne fait que décrypter le contenu du fichier et renvoyer une chaine résultante au javascript
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( (isset($_GET['f_action'])) && ($_GET['f_action']=='importer_saisie_csv') )
{
  $fichier_nom = 'saisie_deportee_'.$_SESSION['BASE'].'_'.$_SESSION['USER_ID'].'_'.fabriquer_fin_nom_fichier__date_et_alea().'.<EXT>';
  $result = FileSystem::recuperer_upload( CHEMIN_DOSSIER_IMPORT /*fichier_chemin*/ , $fichier_nom /*fichier_nom*/ , array('txt','csv') /*tab_extensions_autorisees*/ , NULL /*tab_extensions_interdites*/ , NULL /*taille_maxi*/ , NULL /*filename_in_zip*/ );
  if($result!==TRUE)
  {
    exit('Erreur : '.$result);
  }
  $contenu_csv = file_get_contents(CHEMIN_DOSSIER_IMPORT.FileSystem::$file_saved_name);
  $contenu_csv = To::deleteBOM(To::utf8($contenu_csv)); // Mettre en UTF-8 si besoin et retirer le BOM éventuel
  $tab_lignes = extraire_lignes($contenu_csv); // Extraire les lignes du fichier
  $separateur = extraire_separateur_csv($tab_lignes[0]); // Déterminer la nature du séparateur
  // Pas de ligne d'en-tête à supprimer
  // Mémoriser les eleve_id de la 1ère ligne
  $tab_eleve = array();
  $tab_elements = str_getcsv($tab_lignes[0],$separateur);
  unset($tab_elements[0]);
  foreach ($tab_elements as $num_colonne => $element_contenu)
  {
    $eleve_id = Clean::entier($element_contenu);
    if($eleve_id)
    {
      $tab_eleve[$num_colonne] = $eleve_id ;
    }
  }
  // Parcourir les lignes suivantes et mémoriser les scores
  $retour = '|';
  unset($tab_lignes[0]);
  $scores_autorises = '1234AaDdNnEeFfRrPp';
  foreach ($tab_lignes as $ligne_contenu)
  {
    $tab_elements = str_getcsv($ligne_contenu,$separateur);
    $item_id = Clean::entier($tab_elements[0]);
    if($item_id)
    {
      foreach ($tab_eleve as $num_colonne => $eleve_id)
      {
        if( (isset($tab_elements[$num_colonne])) && ($tab_elements[$num_colonne]!='') )
        {
          $score = $tab_elements[$num_colonne];
          if(strpos($scores_autorises,$score)!==FALSE)
          {
            $retour .= $eleve_id.'.'.$item_id.'.'.strtoupper($score).'|';
          }
        }
      }
    }
  }
  exit($retour);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Référencer un sujet ou un corrigé d'évaluation
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='referencer_document') && $devoir_id && in_array($doc_objet,array('sujet','corrige')) && $doc_url )
{
  // Vérification des droits
  $proprio_id = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_id( $devoir_id );
  if($proprio_id!=$_SESSION['USER_ID'])
  {
    exit('Erreur : vous n\'êtes pas propriétaire du devoir n°'.$devoir_id.' !');
  }
  // Mise à jour dans la base
  DB_STRUCTURE_PROFESSEUR::DB_modifier_devoir_document( $devoir_id , $doc_objet , $doc_url );
  // Retour
  exit('ok');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Uploader un sujet ou un corrigé d'évaluation
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='uploader_document') && $devoir_id && in_array($doc_objet,array('sujet','corrige')) )
{
  // Vérification des droits
  $proprio_id = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_id( $devoir_id );
  if($proprio_id!=$_SESSION['USER_ID'])
  {
    exit('Erreur : vous n\'êtes pas propriétaire du devoir n°'.$devoir_id.' !');
  }
  // Récupération du fichier
  $fichier_nom = 'devoir_'.$devoir_id.'_'.$doc_objet.'_'.$_SERVER['REQUEST_TIME'].'.<EXT>'; // pas besoin de le rendre inaccessible -> fabriquer_fin_nom_fichier__date_et_alea() inutilement lourd
  $result = FileSystem::recuperer_upload( $chemin_devoir /*fichier_chemin*/ , $fichier_nom /*fichier_nom*/ , NULL /*tab_extensions_autorisees*/ , array('bat','com','exe','php','zip') /*tab_extensions_interdites*/ , FICHIER_TAILLE_MAX /*taille_maxi*/ , NULL /*filename_in_zip*/ );
  if($result!==TRUE)
  {
    exit($result);
  }
  // Mise à jour dans la base
  DB_STRUCTURE_PROFESSEUR::DB_modifier_devoir_document( $devoir_id , $doc_objet , $url_dossier_devoir.FileSystem::$file_saved_name );
  // Retour
  exit('ok'.']¤['.$ref.']¤['.$doc_objet.']¤['.$url_dossier_devoir.FileSystem::$file_saved_name);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Retirer un sujet ou un corrigé d'évaluation
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='retirer_document') && $devoir_id && in_array($doc_objet,array('sujet','corrige')) && $doc_url )
{
  // Vérification des droits
  $proprio_id = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_id( $devoir_id );
  if($proprio_id!=$_SESSION['USER_ID'])
  {
    exit('Erreur : vous n\'êtes pas propriétaire du devoir n°'.$devoir_id.' !');
  }
  // Suppression du fichier, uniquement si ce n'est pas un lien externe ou vers un devoir d'un autre établissement
  if(mb_strpos($doc_url,$url_dossier_devoir)===0)
  {
    // Il peut ne pas être présent sur le serveur en cas de restauration de base ailleurs, etc.
    FileSystem::supprimer_fichier( url_to_chemin($doc_url) , TRUE /*verif_exist*/ );
  }
  // Mise à jour dans la base
  DB_STRUCTURE_PROFESSEUR::DB_modifier_devoir_document( $devoir_id , $doc_objet , '' );
  // Retour
  exit('ok');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Déclarer (ou pas) une évaluation complète en saisie
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='maj_fini') && $devoir_id && in_array($fini,array('oui','non')) )
{
  // Vérification des droits
  $proprio_id = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_id( $devoir_id );
  if($proprio_id!=$_SESSION['USER_ID'])
  {
    exit('Erreur : vous n\'êtes pas propriétaire du devoir n°'.$devoir_id.' !');
  }
  // Mise à jour dans la base
  DB_STRUCTURE_PROFESSEUR::DB_modifier_devoir_fini( $devoir_id , $fini );
  // Retour
  exit('ok');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Récupérer un commentaire audio ou texte
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='recuperer_message') && $devoir_id && $eleve_id && in_array($msg_objet,array('audio','texte')) )
{
  $msg_url = DB_STRUCTURE_COMMENTAIRE::DB_recuperer_devoir_commentaire($devoir_id,$eleve_id,$msg_objet);
  if(empty($msg_url))
  {
    exit('Erreur : commentaire introuvable !');
  }
  // [audio] => On renvoie le lien
  if($msg_objet=='audio')
  {
    if(strpos($msg_url,URL_DIR_SACOCHE)!==0)
    {
      // Violation des directives CSP si on essaye de le lire sur un serveur distant -> on le récupère et le copie localement temporairement
      $msg_data = cURL::get_contents($msg_url);
      $fichier_nom = 'devoir_'.$devoir_id.'_eleve_'.$eleve_id.'_audio_copie.mp3';
      FileSystem::ecrire_fichier( CHEMIN_DOSSIER_IMPORT.$fichier_nom , $msg_data );
      $msg_url = URL_DIR_IMPORT.$fichier_nom;
    }
    $msg_data = $msg_url;
  }
  // [texte] => On récupère le contenu du fichier ;  pas de html() sinon ce n'est pas décodé dans le textarea...
  if($msg_objet=='texte')
  {
    if(strpos($msg_url,URL_DIR_SACOCHE)===0)
    {
      $fichier_chemin = url_to_chemin($msg_url);
      $msg_data = is_file($fichier_chemin) ? file_get_contents($fichier_chemin) : 'Erreur : fichier avec le contenu du commentaire non trouvé.' ;
    }
    else
    {
      $msg_data = cURL::get_contents($msg_url);
    }
  }
  // Retour
  exit('ok'.']¤['.$msg_url.']¤['.$msg_data);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Enregistrer un commentaire texte ou audio
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ( ($action=='enregistrer_texte') || ($action=='enregistrer_audio') ) && $devoir_id && $eleve_id && in_array($msg_autre,array('oui','non')) )
{
  $msg_objet = substr($action,-5);
  // Tester les droits
  $proprio_id = DB_STRUCTURE_PROFESSEUR::DB_recuperer_devoir_prorietaire_id( $devoir_id );
  if($proprio_id==$_SESSION['USER_ID'])
  {
    $niveau_droit = 4; // propriétaire
  }
  elseif($profs_liste) // forcément
  {
    $search_liste = '_'.$profs_liste.'_';
    if( strpos( $search_liste, '_m'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      $niveau_droit = 3; // modifier
    }
    elseif( strpos( $search_liste, '_s'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      $niveau_droit = 2; // saisir
    }
    elseif( strpos( $search_liste, '_v'.$_SESSION['USER_ID'].'_' ) !== FALSE )
    {
      exit('Erreur : droit insuffisant attribué sur le devoir n°'.$devoir_id.' (niveau 1 au lieu de 2) !'); // voir
    }
    else
    {
      exit('Erreur : droit attribué sur le devoir n°'.$devoir_id.' non trouvé !');
    }
  }
  else
  {
    exit('Erreur : vous n\'êtes ni propriétaire ni bénéficiaire de droits sur le devoir n°'.$devoir_id.' !');
  }
  // Supprimer un éventuel fichier précédent
  if( $msg_url && (mb_strpos($msg_url,$url_dossier_devoir)===0) )
  {
    // Il peut ne pas être présent sur le serveur en cas de restauration de base ailleurs, etc.
    FileSystem::supprimer_fichier( url_to_chemin($msg_url) , TRUE /*verif_exist*/ );
  }
  // Mise à jour dans la base
  if($msg_data)
  {
    if($action=='enregistrer_audio')
    {
      // extraire les données binaires brutes
      $msg_data = substr($msg_data, strpos($msg_data,',') + 1 );
      // les décoder
      $msg_data = base64_decode($msg_data);
      $ext = 'mp3';
    }
    else
    {
      $ext = 'txt';
    }
    $fichier_nom = 'devoir_'.$devoir_id.'_eleve_'.$eleve_id.'_'.$msg_objet.'_'.$_SERVER['REQUEST_TIME'].'.'.$ext; // pas besoin de le rendre inaccessible -> fabriquer_fin_nom_fichier__date_et_alea() inutilement lourd
    DB_STRUCTURE_COMMENTAIRE::DB_remplacer_devoir_commentaire( $devoir_id , $eleve_id , $msg_objet , $url_dossier_devoir.$fichier_nom );
    // et enregistrement du fichier
    FileSystem::ecrire_fichier( $chemin_devoir.$fichier_nom , $msg_data );
  }
  else
  {
    if($msg_autre=='oui')
    {
      DB_STRUCTURE_COMMENTAIRE::DB_remplacer_devoir_commentaire( $devoir_id , $eleve_id , $msg_objet , '' );
    }
    else
    {
      DB_STRUCTURE_COMMENTAIRE::DB_supprimer_devoir_commentaire( $devoir_id , $eleve_id );
    }
  }
  // Retour
  $retour = ($msg_data) ? $url_dossier_devoir.$fichier_nom : 'supprimé' ;
  exit('ok'.']¤['.$retour);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Il se peut que rien n'ait été récupéré à cause de l'upload d'un fichier trop lourd
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if(empty($_POST))
{
  exit('Erreur : aucune donnée reçue ! Fichier trop lourd ? '.InfoServeur::minimum_limitations_upload());
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// On ne devrait pas en arriver là !
// ////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');

?>
