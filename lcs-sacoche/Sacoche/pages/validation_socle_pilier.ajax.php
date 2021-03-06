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
if(($_SESSION['SESAMATH_ID']==ID_DEMO)&&($_POST['f_action']!='Afficher_bilan')&&($_POST['f_action']!='Afficher_information')){exit('Action désactivée pour la démo...');}

$action       = (isset($_POST['f_action']))       ? Clean::texte($_POST['f_action'])       : '';
$eleve_id     = (isset($_POST['f_user']))         ? Clean::entier($_POST['f_user'])        : 0;
$palier_id    = (isset($_POST['f_palier']))       ? Clean::entier($_POST['f_palier'])      : 0;
$pilier_id    = (isset($_POST['f_pilier']))       ? Clean::entier($_POST['f_pilier'])      : 0; // Sert à afficher les informations pour aider à valider un pilier précis pour un élève donné.
$groupe_type  = (isset($_POST['f_groupe_type']))  ? Clean::texte($_POST['f_groupe_type'])  : '';
$eleves_ordre = (isset($_POST['f_eleves_ordre'])) ? Clean::texte($_POST['f_eleves_ordre']) : '';
// Normalement ce sont des tableaux qui sont transmis, mais au cas où...
$tab_pilier = (isset($_POST['f_pilier'])) ? ( (is_array($_POST['f_pilier'])) ? $_POST['f_pilier'] : explode(',',$_POST['f_pilier']) ) : array() ;
$tab_eleve  = (isset($_POST['f_eleve']))  ? ( (is_array($_POST['f_eleve']))  ? $_POST['f_eleve']  : explode(',',$_POST['f_eleve'])  ) : array() ;
$tab_pilier = array_filter( Clean::map_entier($tab_pilier) , 'positif' );
$tab_eleve  = array_filter( Clean::map_entier($tab_eleve)  , 'positif' );

$listing_eleve_id = implode(',',$tab_eleve);

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Afficher le tableau avec les états de validations
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='Afficher_bilan') && $palier_id && count($tab_pilier) && count($tab_eleve) && $groupe_type && $eleves_ordre )
{
  Form::save_choix('validation_socle_pilier');
  $affichage = '';
  $tab_modif_cellule = array();  // ['html'] , ['class'] , ['title'] , ['data_etat']
  // Tableau des langues
  $tfoot = '';
  require(CHEMIN_DOSSIER_INCLUDE.'tableau_langues_socle.php');
  // Récupérer les données des élèves
  $eleves_ordre = ($groupe_type=='Classes') ? 'alpha' : $eleves_ordre ;
  $tab_eleve_infos = DB_STRUCTURE_BILAN::DB_lister_eleves_cibles( $listing_eleve_id , $eleves_ordre , FALSE /*with_gepi*/ , TRUE /*with_langue*/ , FALSE /*with_brevet_serie*/ );
  if(!is_array($tab_eleve_infos))
  {
    exit('Aucun élève trouvé correspondant aux identifiants transmis !');
  }
  // Afficher la première ligne du tableau avec les étiquettes des élèves
  $tab_eleve_id = array(); // listing des ids des élèves mis à jour au cas où la récupération dans la base soit différente des ids transmis...
  $affichage .= '<thead><tr>';
  foreach($tab_eleve_infos as $eleve_id => $tab_eleve)
  {
    extract($tab_eleve);  // $eleve_nom $eleve_prenom $eleve_langue
    $affichage .= '<th><img id="I'.$eleve_id.'" alt="'.html($eleve_nom.' '.$eleve_prenom).'" src="./_img/php/etiquette.php?dossier='.$_SESSION['BASE'].'&amp;nom='.urlencode($eleve_nom).'&amp;prenom='.urlencode($eleve_prenom).'" /></th>';
    $tfoot .= '<td class="L'.$eleve_langue.'" title="'.$tab_langues[$eleve_langue]['texte'].'"></td>';
    $tab_eleve_id[] = $eleve_id;
  }
  $affichage .= '<th><img alt="Tous les élèves" src="./_img/php/etiquette.php?dossier='.$_SESSION['BASE'].'&amp;nom='.urlencode('TOUS LES ÉLÈVES').'" /></th>';
  $affichage .= '<th class="nu">&nbsp;&nbsp;&nbsp;</th>';
  $affichage .= '<th class="nu">';
  $affichage .=   '<p><label for="Afficher_pourcentage"><input type="checkbox" id="Afficher_pourcentage" /> <span for="Afficher_pourcentage" class="socle_info voir">Afficher / Masquer le nombre d\'items du socle validés et invalidés.</span></label></p>';
  $affichage .=   '<p class="danger">Rappel : la validation d\'une compétence est définitive (une invalidation peut être changée).</p>';
  $affichage .=   '<p><button id="Enregistrer_validation" type="button" class="valider">Enregistrer les validations</button> <button id="fermer_zone_validation" type="button" class="retourner">Retour</button><label id="ajax_msg_validation"></label></p>';
  $affichage .=   '<div><button id="go_precedent_groupe" type="button" class="go_precedent" title="Classe / groupe précédent.">&nbsp;</button> <button id="go_suivant_groupe" type="button" class="go_suivant" title="Classe / groupe suivant.">&nbsp;</button> <span class="m1 b">@GROUPE@</span></div>';
  $affichage .=   '<div><button id="go_precedent_palier" type="button" class="go_precedent" title="Palier précédent.">&nbsp;</button> <button id="go_suivant_palier" type="button" class="go_suivant" title="Palier suivant.">&nbsp;</button> <span class="m1 b">@PALIER@</span></div>';
  $affichage .= '</th>';
  $affichage .= '</tr></thead>';
  $affichage .= '<tbody>';
  // Afficher la ligne du tableau avec les validations pour des piliers choisis
  $affichage .= '<tr>';
  foreach($tab_eleve_id as $eleve_id)
  {
    $affichage .= '<th id="U'.$eleve_id.'" class="down1" title="Modifier la validation de toutes les compétences pour cet élève."></th>';
  }
  $affichage .= '<th id="P'.$palier_id.'" class="diag1" title="Modifier la validation de toutes les compétences pour tous les élèves."></th>';
  $affichage .= '<th class="nu" colspan="2"></th>';
  $affichage .= '</tr>';
  // Récupérer l'arborescence des piliers du palier du socle (enfin... uniquement les piliers, ça suffit ici)
  $tab_pilier_id = array(); // listing des ids des piliers mis à jour au cas où la récupération dans la base soit différente des ids transmis...
  $DB_TAB = DB_STRUCTURE_SOCLE::DB_recuperer_piliers($palier_id);
  foreach($DB_TAB as $DB_ROW)
  {
    $pilier_id = $DB_ROW['rubrique_id'];
    if(in_array($pilier_id,$tab_pilier))
    {
      $tab_pilier_id[] = $pilier_id;
      // Afficher la ligne du tableau avec les validations des piliers, puis le nom du pilier (officiellement compétence)
      $affichage .= '<tr>';
      foreach($tab_eleve_id as $eleve_id)
      {
        $affichage .= '<td id="U'.$eleve_id.'C'.$pilier_id.'"></td>'; // class/title + data-etat + contenu seront ajoutés ensuite 
        $tab_modif_cellule[$eleve_id][$pilier_id] = array( 'html_v1'=>'0' , 'html_v0'=>'0' , 'class'=>' class="v2"' , 'title'=>'' , 'data_etat'=>'' );
      }
      $affichage .= '<th id="C'.$pilier_id.'" class="left1" title="Modifier la validation de cette compétence pour tous les élèves."></th>';
      $affichage .= '<th class="nu" colspan="2"><div class="n1">'.html($DB_ROW['rubrique_nom']).'</div></th>';
      $affichage .= '</tr>';
    }
  }
  $affichage .= '</tbody>';
  // Ligne avec le drapeau de la LV, si compétence concernée sélectionnée.
  $affichage .= count(array_intersect($tab_pilier_id,$tab_langue_piliers)) ? '<tfoot>'.$tfoot.'<th class="nu"></th><th class="nu" colspan="2"></th></tfoot>' : '' ;
  // Récupérer la liste des jointures (validations)
  $listing_eleve_id  = implode(',',$tab_eleve_id);
  $listing_pilier_id = implode(',',$tab_pilier_id);
  $DB_TAB = DB_STRUCTURE_SOCLE::DB_lister_jointure_user_pilier( $listing_eleve_id , $listing_pilier_id , 0 /*palier_id*/ ); // en fait on connait aussi le palier mais la requête est plus simple (pas de jointure) avec les piliers
  foreach($DB_TAB as $DB_ROW)
  {
    $title_etat = ($DB_ROW['validation_pilier_etat']) ? 'Validé' : 'Invalidé' ;
    $data_etat  = ($DB_ROW['validation_pilier_etat']) ? ' data-etat="lock"' : '' ;
    $tab_modif_cellule[$DB_ROW['user_id']][$DB_ROW['pilier_id']]['class'] = ' class="v'.$DB_ROW['validation_pilier_etat'].'"';
    $tab_modif_cellule[$DB_ROW['user_id']][$DB_ROW['pilier_id']]['title'] = ' title="'.$title_etat.' le '.convert_date_mysql_to_french($DB_ROW['validation_pilier_date']).' par '.html($DB_ROW['validation_pilier_info']).'"';
    $tab_modif_cellule[$DB_ROW['user_id']][$DB_ROW['pilier_id']]['data_etat']  = $data_etat;
  }

  // Compter le nombre d'items validés par élève et compétence
  $DB_TAB = DB_STRUCTURE_SOCLE::DB_lister_nombre_validations_eleves_items( $listing_eleve_id , $listing_pilier_id );
  foreach($DB_TAB as $DB_ROW)
  {
    $tab_modif_cellule[$DB_ROW['user_id']][$DB_ROW['pilier_id']]['html_v'.$DB_ROW['validation_entree_etat']] = $DB_ROW['nombre'];
  }

  // Afficher le résultat après adaptation des cellules "centrales".
  $tab_bad = array();
  $tab_bon = array();
  foreach($tab_eleve_id as $eleve_id)
  {
    foreach($tab_pilier_id as $pilier_id)
    {
      extract($tab_modif_cellule[$eleve_id][$pilier_id]);  // $data_etat $class $title $html_v1 $html_v0
      $html = ($tab_modif_cellule[$eleve_id][$pilier_id]['data_etat']) ? '' : ( ($html_v1 || $html_v0) ? $html_v1.'<br />'.$html_v0 : '-' ) ;
      $tab_bad[] = 'U'.$eleve_id.'C'.$pilier_id.'"></td>';
      $tab_bon[] = 'U'.$eleve_id.'C'.$pilier_id.'"'.$data_etat.$class.$title.'>'.$html.'</td>';
    }
  }
  $affichage = str_replace($tab_bad,$tab_bon,$affichage);
  // Afficher le résultat
  exit($affichage);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Afficher les informations pour aider à valider un pilier précis pour un élève donné
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='Afficher_information') && $eleve_id && $pilier_id )
{
  // Récupération de la liste des validations des items du palier
  $tab_item = array();  // [entree_id] => 0/1;
  $DB_TAB = DB_STRUCTURE_SOCLE::DB_lister_jointure_user_entree($eleve_id,$listing_entree_id='',$domaine_id=0,$pilier_id,$palier_id=0);
  if(empty($DB_TAB))
  {
    exit('Aucune validation d\'item n\'est renseignée pour cette compétence !');
  }
  foreach($DB_TAB as $DB_ROW)
  {
    $tab_item[$DB_ROW['entree_id']] = $DB_ROW['validation_entree_etat'];
  }
  // Récupérer l'arborescence du pilier du socle ; préparer l'affichage et comptabiliser les différents états de validation
  $tab_texte_stats = array(1=>'validé',0=>'invalidé',2=>'non renseigné');
  $tab_texte_items = array(1=>'OUI',0=>'NON',2=>'???');
  $tab_validation_socle = array(1=>0,0=>0,2=>0);
  $affichage_socle = '';
  $DB_TAB = DB_STRUCTURE_SOCLE::DB_recuperer_arborescence_pilier($pilier_id);
  $section_id = 0;
  foreach($DB_TAB as $DB_ROW)
  {
    if( (!is_null($DB_ROW['section_id'])) && ($DB_ROW['section_id']!=$section_id) )
    {
      $section_id = $DB_ROW['section_id'];
      $affichage_socle .= '<div class="n2 i">'.html($DB_ROW['section_nom']).'</div>';
      $entree_id  = 0;
    }
    if( (!is_null($DB_ROW['entree_id'])) && ($DB_ROW['entree_id']!=$entree_id) )
    {
      $entree_id = $DB_ROW['entree_id'];
      $etat = (isset($tab_item[$DB_ROW['entree_id']])) ? $tab_item[$DB_ROW['entree_id']] : 2 ;
      $affichage_socle .= '<div class="n3"><tt class="v'.$etat.'">'.$tab_texte_items[$etat].'</tt>'.html($DB_ROW['entree_nom']).'</div>';
      $tab_validation_socle[$etat]++;
    }
  }
  // Ligne de stats
  foreach($tab_validation_socle as $etat => $nb)
  {
    $s = ($nb>1) ? 's' : '' ;
    echo'<span class="v'.$etat.'">'.$nb.' '.$tab_texte_stats[$etat].$s.'</span>';
  }
  // Paragraphe des items
  echo'@'.$affichage_socle;
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Enregistrer les états de validation
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($action=='Enregistrer_validation')
{
  // Récupérer les triplets {eleve;pilier;valid}
  $tab_valid = (isset($_POST['f_valid'])) ? explode(',',$_POST['f_valid']) : array() ;
  $tab_post = array();
  // Au passage, enregistrer la liste des items et des élèves
  $tab_eleve_id  = array();
  $tab_pilier_id = array();
  foreach($tab_valid as $string_infos)
  {
    $string_infos = str_replace( array('U','C','V') , '_' , $string_infos);
    list($rien,$eleve_id,$pilier_id,$valid) = explode('_',$string_infos);
    $tab_post[$pilier_id.'x'.$eleve_id] = (int)$valid;
    $tab_eleve_id[$eleve_id]   = $eleve_id;
    $tab_pilier_id[$pilier_id] = $pilier_id;
  }
  if( (!count($tab_post)) || (count($tab_eleve_id)*count($tab_pilier_id)!=count($tab_post)) )
  {
    exit('Erreur détectée avec les validations transmises !');
  }
  // On recupère le contenu de la base déjà enregistré pour le comparer
  $listing_eleve_id  = implode(',',$tab_eleve_id);
  $listing_pilier_id = implode(',',$tab_pilier_id);
  $DB_TAB = DB_STRUCTURE_SOCLE::DB_lister_jointure_user_pilier($listing_eleve_id,$listing_pilier_id,$palier_id=0);
  // On remplit au fur et à mesure $tab_nouveau_modifier et $tab_nouveau_supprimer
  $tab_nouveau_modifier = array();
  $tab_nouveau_supprimer = array();
  foreach($DB_TAB as $DB_ROW)
  {
    $key = $DB_ROW['pilier_id'].'x'.$DB_ROW['user_id'];
    // Attention : on ne peut pas modifier un pilier déjà validé (verrouillage)
    if($DB_ROW['validation_pilier_etat']!=1)
    {
      if($tab_post[$key]==2)
      {
        // Validation présente dans la base mais annulée par le formulaire
        $tab_nouveau_supprimer[$key] = $key;
      }
      elseif($tab_post[$key]!=$DB_ROW['validation_pilier_etat'])
      {
        // Validation présente dans la base mais modifiée par le formulaire
        $tab_nouveau_modifier[$key] = $tab_post[$key];
      }
      // Sinon, validation présente dans la base et confirmée par le formulaire : RAS
    }
    unset($tab_post[$key]);
  }
  // Il reste dans $tab_post les validations à ajouter (mises dans $tab_nouveau_ajouter) et les validations à ignorer (non effectuées par le formulaire)
  // On remplit $tab_nouveau_ajouter
  // Validation absente dans la base mais effectuée par le formulaire
  $tab_nouveau_ajouter = array_filter($tab_post,'is_renseigne');
  // Sinon, validation absente dans la base et absente du formulaire : RAS

  // Il n'y a plus qu'à mettre à jour la base
  if( !count($tab_nouveau_ajouter) && !count($tab_nouveau_modifier) && !count($tab_nouveau_supprimer) )
  {
    exit('Aucune modification détectée !');
  }
  // L'information associée à la validation comporte le nom du validateur (c'est une information statique, conservée sur plusieurs années)
  $info = afficher_identite_initiale($_SESSION['USER_NOM'],FALSE,$_SESSION['USER_PRENOM'],TRUE,$_SESSION['USER_GENRE']);
  foreach($tab_nouveau_ajouter as $key => $etat)
  {
    list($pilier_id,$eleve_id) = explode('x',$key);
    DB_STRUCTURE_SOCLE::DB_ajouter_validation('pilier',$eleve_id,$pilier_id,$etat,TODAY_MYSQL,$info);
  }
  foreach($tab_nouveau_modifier as $key => $etat)
  {
    list($pilier_id,$eleve_id) = explode('x',$key);
    DB_STRUCTURE_SOCLE::DB_modifier_validation('pilier',$eleve_id,$pilier_id,$etat,TODAY_MYSQL,$info);
  }
  foreach($tab_nouveau_supprimer as $key)
  {
    list($pilier_id,$eleve_id) = explode('x',$key);
    DB_STRUCTURE_SOCLE::DB_supprimer_validation('pilier',$eleve_id,$pilier_id);
  }
  exit('OK');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// On ne devrait pas en arriver là...
// ////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');
?>
