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

$action = (isset($_POST['f_action'])) ? Clean::texte($_POST['f_action']) : '';
$etape  = (isset($_POST['etape']))    ? Clean::entier($_POST['etape'])   : 0;

$top_depart = microtime(TRUE);

$dossier_temp = CHEMIN_DOSSIER_DUMP.$_SESSION['BASE'].DS;

require(CHEMIN_DOSSIER_INCLUDE.'fonction_dump.php');

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Sauvegarder la base
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='sauvegarder') && $etape )
{
  if($etape==1)
  {
    // Créer ou vider le dossier temporaire
    FileSystem::creer_ou_vider_dossier($dossier_temp);
    // Bloquer l'application
    LockAcces::bloquer_application('automate',$_SESSION['BASE'],'Sauvegarde de la base en cours.');
  }
  // Remplir le dossier temporaire avec les fichiers de svg des tables
  $texte_etape = sauvegarder_tables_base_etablissement($dossier_temp,$etape);
  if(strpos($texte_etape,'terminée'))
  {
    $class = "valide";
    // Débloquer l'application
    LockAcces::debloquer_application('automate',$_SESSION['BASE']);
    // Zipper les fichiers de svg
    $fichier_zip_nom = 'dump_SACoche_'.$_SESSION['BASE'].'_'.fabriquer_fin_nom_fichier__date_et_alea().'.zip';
    FileSystem::zipper_fichiers($dossier_temp,CHEMIN_DOSSIER_DUMP,$fichier_zip_nom);
    // Supprimer le dossier temporaire
    FileSystem::supprimer_dossier($dossier_temp);
  }
  else
  {
    $class = "loader";
    $top_arrivee = microtime(TRUE);
    $duree = number_format($top_arrivee - $top_depart,2,',','');
    $texte_etape .= ' en '.$duree.'s';
  }
  // Afficher le retour
  echo'<li><label class="'.$class.'">'.$texte_etape.'.</label></li>';
  if(strpos($texte_etape,'terminée'))
  {
    echo'<li><a target="_blank" href="'.URL_DIR_DUMP.$fichier_zip_nom.'"><span class="file file_zip">Récupérer le fichier de sauvegarde au format ZIP.</span></a></li>';
  }
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Uploader et dezipper / vérifier un fichier à restaurer
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($action=='uploader')
{
  $fichier_upload_nom = 'dump_'.$_SESSION['BASE'].'_'.fabriquer_fin_nom_fichier__date_et_alea().'.zip';
  $result = FileSystem::recuperer_upload( CHEMIN_DOSSIER_IMPORT /*fichier_chemin*/ , $fichier_upload_nom /*fichier_nom*/ , array('zip') /*tab_extensions_autorisees*/ , NULL /*tab_extensions_interdites*/ , NULL /*taille_maxi*/ , NULL /*filename_in_zip*/ );
  if($result!==TRUE)
  {
    exit('<li><label class="alerte">Erreur : '.$result.'</label></li>');
  }
  // Créer ou vider le dossier temporaire
  FileSystem::creer_ou_vider_dossier($dossier_temp);
  // Dezipper dans le dossier temporaire
  $code_erreur = FileSystem::unzip( CHEMIN_DOSSIER_IMPORT.$fichier_upload_nom , $dossier_temp , FALSE /*use_ZipArchive*/ );
  if($code_erreur)
  {
    FileSystem::supprimer_dossier($dossier_temp); // Pas seulement vider, au cas où il y aurait des sous-dossiers créés par l'archive.
    exit('<li><label class="alerte">Erreur : votre archive ZIP n\'a pas pu être ouverte ('.FileSystem::$tab_zip_error[$code_erreur].') !</label></li>');
  }
  FileSystem::supprimer_fichier(CHEMIN_DOSSIER_IMPORT.$fichier_upload_nom);
  // Vérifier le contenu : noms des fichiers
  $fichier_taille_maximale = verifier_dossier_decompression_sauvegarde($dossier_temp);
  if(!$fichier_taille_maximale)
  {
    FileSystem::supprimer_dossier($dossier_temp); // Pas seulement vider, au cas où il y aurait des sous-dossiers créés par l'archive.
    exit('<li><label class="alerte">Erreur : votre archive ZIP ne semble pas contenir les fichiers d\'une sauvegarde de la base effectuée par SACoche !</label></li>');
  }
  // Vérifier le contenu : taille des requêtes
  if( !verifier_taille_requetes($fichier_taille_maximale) )
  {
    FileSystem::supprimer_dossier($dossier_temp); // Pas seulement vider, au cas où il y aurait des sous-dossiers créés par l'archive.
    exit('<li><label class="alerte">Erreur : votre archive ZIP contient au moins un fichier dont la taille dépasse la limitation <em>max_allowed_packet</em> de MySQL !</label></li>');
  }
  // Vérifier le contenu : version de la base compatible avec la version logicielle
  if( version_base_fichier_svg($dossier_temp) > VERSION_BASE_STRUCTURE )
  {
    FileSystem::supprimer_dossier($dossier_temp); // Pas seulement vider, au cas où il y aurait des sous-dossiers créés par l'archive.
    exit('<li><label class="alerte">Erreur : votre archive ZIP contient une sauvegarde plus récente que celle supportée par cette installation ! Le webmestre doit préalablement mettre à jour le programme...</label></li>');
  }
  // Afficher le retour
  echo'<li><label class="valide">Contenu du fichier récupéré avec succès.</label></li>';
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Restaurer la base
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='restaurer') && $etape )
{
  if($etape==1)
  {
    // Bloquer l'application
    LockAcces::bloquer_application('automate',$_SESSION['BASE'],'Restauration de la base en cours.');
    // Notifications (rendues visibles ultérieurement)
    $notification_contenu = date('d-m-Y H:i:s').' '.$_SESSION['USER_PRENOM'].' '.$_SESSION['USER_NOM'].' a lancé une restauration de la base de données.'."\r\n";
    DB_STRUCTURE_NOTIFICATION::enregistrer_action_admin( $notification_contenu , $_SESSION['USER_ID'] );
  }
  // Restaurer des fichiers de svg et mettre la base à jour si besoin.
  $texte_etape = restaurer_tables_base_etablissement($dossier_temp,$etape);
  if(strpos($texte_etape,'terminée'))
  {
    $class = "valide";
    // Débloquer l'application
    LockAcces::debloquer_application('automate',$_SESSION['BASE']);
    // Supprimer le dossier temporaire
    FileSystem::supprimer_dossier($dossier_temp);
  }
  else
  {
    $class = "loader";
    $top_arrivee = microtime(TRUE);
    $duree = number_format($top_arrivee - $top_depart,2,',','');
    $texte_etape .= ' en '.$duree.'s';
  }
  // Afficher le retour
  echo'<li><label class="'.$class.'">'.$texte_etape.'.</label></li>';
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Il se peut que rien n'ait été récupéré à cause de l'upload d'un fichier trop lourd
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if(empty($_POST))
{
  exit('Erreur : aucune donnée reçue ! Fichier trop lourd ? '.InfoServeur::minimum_limitations_upload());
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// On ne devrait pas en arriver là...
// ////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');

?>
