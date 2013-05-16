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

$action          = (isset($_POST['f_action']))        ? Clean::texte($_POST['f_action'])         : '';
$check           = (isset($_POST['f_check']))         ? Clean::entier($_POST['f_check'])         : 0;
$id              = (isset($_POST['f_id']))            ? Clean::entier($_POST['f_id'])            : 0;
$id_ent          = (isset($_POST['f_id_ent']))        ? Clean::texte($_POST['f_id_ent'])         : '';
$id_gepi         = (isset($_POST['f_id_gepi']))       ? Clean::texte($_POST['f_id_gepi'])        : '';
$sconet_id       = (isset($_POST['f_sconet_id']))     ? Clean::entier($_POST['f_sconet_id'])     : 0;
$sconet_num      = (isset($_POST['f_sconet_num']))    ? Clean::entier($_POST['f_sconet_num'])    : 0;
$reference       = (isset($_POST['f_reference']))     ? Clean::ref($_POST['f_reference'])        : '';
$profil          = 'ELV';
$nom             = (isset($_POST['f_nom']))           ? Clean::nom($_POST['f_nom'])              : '';
$prenom          = (isset($_POST['f_prenom']))        ? Clean::prenom($_POST['f_prenom'])        : '';
$birth_date      = (isset($_POST['f_birth_date']))    ? Clean::texte($_POST['f_birth_date'])     : '' ;
$login           = (isset($_POST['f_login']))         ? Clean::login($_POST['f_login'])          : '';
$password        = (isset($_POST['f_password']))      ? Clean::password($_POST['f_password'])    : '' ;
$sortie_date     = (isset($_POST['f_sortie_date']))   ? Clean::texte($_POST['f_sortie_date'])    : '' ;
$box_birth_date  = (isset($_POST['box_birth_date']))  ? Clean::entier($_POST['box_birth_date'])  : 0;
$box_login       = (isset($_POST['box_login']))       ? Clean::entier($_POST['box_login'])       : 0;
$box_password    = (isset($_POST['box_password']))    ? Clean::entier($_POST['box_password'])    : 0;
$box_sortie_date = (isset($_POST['box_sortie_date'])) ? Clean::entier($_POST['box_sortie_date']) : 0;
$groupe          = (isset($_POST['f_groupe']))        ? Clean::texte($_POST['f_groupe'])         : 'd2' ;
$groupe_type     = Clean::texte( substr($groupe,0,1) );
$groupe_id       = Clean::entier( substr($groupe,1) );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Ajouter un nouvel élève
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='ajouter') && $nom && $prenom && ($box_login || $login) && ($box_password || $password) && ($box_birth_date || $birth_date) && ($box_sortie_date || $sortie_date) )
{
  // Vérifier que l'identifiant ENT est disponible (parmi tous les utilisateurs de l'établissement)
  if($id_ent)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('id_ent',$id_ent) )
    {
      exit('Erreur : identifiant ENT déjà utilisé !');
    }
  }
  // Vérifier que l'identifiant GEPI est disponible (parmi tous les utilisateurs de l'établissement)
  if($id_gepi)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('id_gepi',$id_gepi) )
    {
      exit('Erreur : identifiant Gepi déjà utilisé !');
    }
  }
  // Vérifier que l'identifiant sconet est disponible (parmi les utilisateurs de même type de profil)
  if($sconet_id)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('sconet_id',$sconet_id,NULL,$_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$profil]) )
    {
      exit('Erreur : identifiant sconet déjà utilisé !');
    }
  }
  // Vérifier que le n° sconet est disponible (parmi les utilisateurs de même type de profil)
  if($sconet_num)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('sconet_elenoet',$sconet_num,NULL,$_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$profil]) )
    {
      exit('Erreur : numéro Sconet déjà utilisé !');
    }
  }
  // Vérifier que la référence est disponible (parmi les utilisateurs de même type de profil)
  if($reference)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('reference',$reference,NULL,$_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$profil]) )
    {
      exit('Erreur : référence déjà utilisée !');
    }
  }
  if($box_login)
  {
    // Construire puis tester le login (parmi tous les utilisateurs de l'établissement)
    $login = fabriquer_login($prenom,$nom,$profil);
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('login',$login) )
    {
      // Login pris : en chercher un autre en remplaçant la fin par des chiffres si besoin
      $login = DB_STRUCTURE_ADMINISTRATEUR::DB_rechercher_login_disponible($login);
    }
  }
  else
  {
    // Vérifier que le login transmis est disponible (parmi tous les utilisateurs de l'établissement)
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('login',$login) )
    {
      exit('Erreur : login déjà existant !');
    }
  }
  if($box_password)
  {
    // Générer un mdp aléatoire
    $password = fabriquer_mdp($profil);
  }
  else
  {
    // Vérifier que le mdp transmis est d'une longueur compatible
    if(mb_strlen($password)<$_SESSION['TAB_PROFILS_ADMIN']['MDP_LONGUEUR_MINI'][$profil])
    {
      exit('Erreur : mot de passe trop court pour ce profil !');
    }
  }
  // Cas de la date de naissance
  if($box_birth_date)
  {
    $birth_date = '-' ;
    $birth_date_mysql = NULL;
  }
  else
  {
    $birth_date_mysql = convert_date_french_to_mysql($birth_date);
  }
  // Insérer l'enregistrement
  $user_id = DB_STRUCTURE_COMMUN::DB_ajouter_utilisateur( $sconet_id , $sconet_num , $reference , $profil , $nom , $prenom , $birth_date_mysql , $login , crypter_mdp($password) , 0 /*eleve_classe_id*/ , $id_ent , $id_gepi );
  // Il peut (déjà !) falloir lui affecter une date de sortie...
  if($box_sortie_date)
  {
    $sortie_date = '-' ;
    $sortie_date_mysql = SORTIE_DEFAUT_MYSQL;
  }
  else
  {
    $sortie_date_mysql = convert_date_french_to_mysql($sortie_date);
    DB_STRUCTURE_ADMINISTRATEUR::DB_modifier_user( $user_id , array(':sortie_date'=>$sortie_date_mysql) );
  }
  // Si on inscrit un élève après avoir fait afficher une classe ou un groupe, alors on l'affecte dans la classe ou le groupe en question
  $tab_groupe_type = array( 'c'=>'classe' , 'g'=>'groupe' );
  if( isset($tab_groupe_type[$groupe_type]) && $groupe_id )
  {
    DB_STRUCTURE_ADMINISTRATEUR::DB_modifier_liaison_user_groupe_par_admin( $user_id , 'eleve' , $groupe_id , $tab_groupe_type[$groupe_type] , TRUE );
  }
  // Afficher le retour
  echo'<tr id="id_'.$user_id.'" class="new">';
  echo  '<td class="nu"><input type="checkbox" name="f_ids" value="'.$user_id.'" /></td>';
  echo  '<td class="label">'.html($id_ent).'</td>';
  echo  '<td class="label">'.html($id_gepi).'</td>';
  echo  '<td class="label">'.html($sconet_id).'</td>';
  echo  '<td class="label">'.html($sconet_num).'</td>';
  echo  '<td class="label">'.html($reference).'</td>';
  echo  '<td class="label">'.html($nom).'</td>';
  echo  '<td class="label">'.html($prenom).'</td>';
  echo  '<td class="label">'.$birth_date.'</td>';
  echo  '<td class="label new">'.html($login).' <img alt="" src="./_img/bulle_aide.png" title="Pensez à relever le login généré !" /></td>';
  echo  '<td class="label new">'.html($password).' <img alt="" src="./_img/bulle_aide.png" title="Pensez à noter le mot de passe !" /></td>';
  echo  '<td class="label">'.$sortie_date.'</td>';
  echo  '<td class="nu">';
  echo    '<q class="modifier" title="Modifier cet élève."></q>';
  echo  '</td>';
  echo'</tr>';
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Modifier un élève existant
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($action=='modifier') && $id && $nom && $prenom && ($box_login || $login) && ( $box_password || $password ) && ($box_birth_date || $birth_date) && ($box_sortie_date || $sortie_date) )
{
  $tab_donnees = array();
  // Vérifier que l'identifiant ENT est disponible (parmi tous les utilisateurs de l'établissement)
  if($id_ent)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('id_ent',$id_ent,$id) )
    {
      exit('Erreur : identifiant ENT déjà utilisé !');
    }
  }
  // Vérifier que l'identifiant GEPI est disponible (parmi tous les utilisateurs de l'établissement)
  if($id_gepi)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('id_gepi',$id_gepi,$id) )
    {
      exit('Erreur : identifiant Gepi déjà utilisé !');
    }
  }
  // Vérifier que l'identifiant sconet est disponible (parmi les utilisateurs de même type de profil)
  if($sconet_id)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('sconet_id',$sconet_id,$id,$_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$profil]) )
    {
      exit('Erreur : identifiant Sconet déjà utilisé !');
    }
  }
  // Vérifier que le n° sconet est disponible (parmi les utilisateurs de même type de profil)
  if($sconet_num)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('sconet_elenoet',$sconet_num,$id,$_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$profil]) )
    {
      exit('Erreur : numéro Sconet déjà utilisé !');
    }
  }
  // Vérifier que la référence est disponible (parmi les utilisateurs de même type de profil)
  if($reference)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('reference',$reference,$id,$_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$profil]) )
    {
      exit('Erreur : référence déjà utilisée !');
    }
  }
  // Vérifier que le login transmis est disponible (parmi tous les utilisateurs de l'établissement)
  if(!$box_login)
  {
    if( DB_STRUCTURE_ADMINISTRATEUR::DB_tester_utilisateur_identifiant('login',$login,$id) )
    {
      exit('Erreur : login déjà existant !');
    }
    $tab_donnees[':login'] = $login;
  }
  // Cas du mot de passe
  if(!$box_password)
  {
    $tab_donnees[':password'] = crypter_mdp($password);
  }
  // Cas de la date de naissance
  if($box_birth_date)
  {
    $birth_date = '-' ;
    $birth_date_mysql = NULL;
  }
  else
  {
    $birth_date_mysql = convert_date_french_to_mysql($birth_date);
  }
  // Cas de la date de sortie
  if($box_sortie_date)
  {
    $sortie_date = '-' ;
    $sortie_date_mysql = SORTIE_DEFAUT_MYSQL;
  }
  else
  {
    $sortie_date_mysql = convert_date_french_to_mysql($sortie_date);
  }
  // Mettre à jour l'enregistrement
  $tab_donnees += array(':sconet_id'=>$sconet_id,':sconet_num'=>$sconet_num,':reference'=>$reference,':nom'=>$nom,':prenom'=>$prenom,':birth_date'=>$birth_date_mysql,':id_ent'=>$id_ent,':id_gepi'=>$id_gepi,':sortie_date'=>$sortie_date_mysql);
  DB_STRUCTURE_ADMINISTRATEUR::DB_modifier_user( $id , $tab_donnees );
  // Afficher le retour
  $checked = ($check) ? ' checked' : '' ;
  echo'<td class="nu"><input type="checkbox" name="f_ids" value="'.$id.'"'.$checked.' /></td>';
  echo'<td class="label">'.html($id_ent).'</td>';
  echo'<td class="label">'.html($id_gepi).'</td>';
  echo'<td class="label">'.html($sconet_id).'</td>';
  echo'<td class="label">'.html($sconet_num).'</td>';
  echo'<td class="label">'.html($reference).'</td>';
  echo'<td class="label">'.html($nom).'</td>';
  echo'<td class="label">'.html($prenom).'</td>';
  echo'<td class="label">'.$birth_date.'</td>';
  echo'<td class="label">'.html($login).'</td>';
  echo ($box_password) ? '<td class="label i">champ crypté</td>' : '<td class="label new">'.$password.' <img alt="" src="./_img/bulle_aide.png" title="Pensez à noter le mot de passe !" /></td>' ;
  echo'<td class="label">'.$sortie_date.'</td>';
  echo'<td class="nu">';
  echo  '<q class="modifier" title="Modifier cet élève."></q>';
  echo'</td>';
  exit();
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Retirer des comptes
// Réintégrer des comptes
// Supprimer des comptes
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( in_array( $action , array('retirer','reintegrer','supprimer') ) )
{
  require(CHEMIN_DOSSIER_INCLUDE.'code_administrateur_comptes.php');
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// On ne devrait pas en arriver là !
// ////////////////////////////////////////////////////////////////////////////////////////////////////

exit('Erreur avec les données transmises !');

?>
