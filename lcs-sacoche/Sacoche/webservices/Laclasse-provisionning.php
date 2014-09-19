<?php
if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}

/**
 * Ce fichier n'est présent que sur l'installation SACoche du département du Rhône.
 * 
 * Webservice servant à ajouter un établissement sur le SACoche de l'ENT du Rhône (69)
 * Laclasse.com version 3 <http://www.laclasse.com/v3/>
 * et à le peupler au maximum ("provisionning des établissements et des comptes en mode api").
 * 
 * L'appel à ce webservices de provisionning se fait par l'ENT via l'url
 * http://domaine_serveur/rep_install_sacoche/webservices.php?qui=Laclasse-provisionning&uai=[code uai de l'établissement à provisionner]
 * 
 * @see https://docs.google.com/spreadsheets/d/1fY47KVVEGQHvx-qCrVT4mWpBJEPAPSW1s1-B8Slo9m8/edit#gid=0
 */

require(CHEMIN_DOSSIER_WEBSERVICES.'Laclasse.class.php');

/**
 * Vérification des paramètres
 */
if(!$WS_uai)
{
  exit_json(400, 'Paramètre UAI manquant.');
}
if(!tester_UAI($WS_uai))
{
  exit_json(400, 'Paramètre UAI incorrect ('.$WS_uai.').');
}
if(substr($WS_uai,0,3)!=='069')
{
  exit_json(400, 'Paramètre UAI hors département 69 ('.$WS_uai.').');
}

/****************************************************************************************************
 * Code de test à décommenter si on veut enregistrer les fichiers récupéré afin de les étudier.
 ****************************************************************************************************/

// http://sacoche-prog.local/webservices.php?qui=Laclasse-provisionning&uai=0693331W
/*
$tab_services = array( '' , 'matieres' , 'classes' , 'groupes' , 'users' );
$tab_services = array( 'profs' );
$tab_services = array( 'parents' );
$tab_services = array( 'eleves' );
foreach($tab_services as $service)
{
  $tab_annuaire = Laclasse::get_info_from_annuaire( $WS_uai , $service , TRUE , TRUE );
  file_put_contents( 'erasme_'.$WS_uai.'_'.$service , print_r($tab_annuaire,TRUE) );
}
exit('ok');
*/

/****************************************************************************************************
 * Tableaux pour stocker les informations utiles.
 ****************************************************************************************************/

$tab_etablissement   = array();
$tab_classes         = array();
$tab_groupes         = array();
$tab_personnels      = array();
$tab_administrateurs = array();
$tab_eleves          = array();
$tab_parents         = array();
$tab_matieres        = array();

/****************************************************************************************************
 * Récupérer les informations du fichier principal.
 ****************************************************************************************************/

$tab_annuaire = Laclasse::get_info_from_annuaire( $WS_uai , '' , TRUE /*exit_if_error*/ , TRUE /*with_details*/ );

// L'établissement

$tab_etablissement['localisation'] = Clean::codepostal($tab_annuaire['code_postal']).' '.Clean::commune($tab_annuaire['ville']);
$tab_etablissement['denomination'] = Clean::structure( str_replace( array('Collège CLG-','Collège CLG PR-') , 'Collège ' , $tab_annuaire['full_name'] ) );
$tab_etablissement['adresse1']     = Clean::adresse($tab_annuaire['adresse']);
$tab_etablissement['adresse2']     = Clean::codepostal($tab_annuaire['code_postal']).' '.Clean::commune($tab_annuaire['ville']);
$tab_etablissement['telephone']    = Clean::texte( str_replace( '+33 4' , '04' , $tab_annuaire['telephone']) );
$tab_etablissement['fax']          = Clean::texte( str_replace( '+33 4' , '04' , $tab_annuaire['fax']) );

// Les classes
if(!empty($tab_annuaire['classes']))
{
  foreach($tab_annuaire['classes'] as $tab_infos)
  {
    $classe_id  = Clean::entier($tab_infos['id']);
    $classe_ref = Clean::ref($tab_infos['libelle_aaf']);
    $classe_nom = ($tab_infos['libelle']) ? Clean::ref($tab_infos['libelle']) : $classe_ref ;
    $classe_mef = Clean::texte($tab_infos['code_mef_aaf']);
    $tab_classes[$classe_id] = array(
      'ref' => $classe_ref,
      'nom' => $classe_nom,
      'mef' => $classe_mef,
    );
  }
}

// Les groupes
if(!empty($tab_annuaire['groupes_eleves']))
{
  foreach($tab_annuaire['groupes_eleves'] as $tab_infos)
  {
    $groupe_id  = Clean::entier($tab_infos['id']);
    $groupe_ref = Clean::ref($tab_infos['libelle_aaf']);
    $groupe_nom = ($tab_infos['libelle']) ? Clean::ref($tab_infos['libelle']) : $groupe_ref ;
    $groupe_mef = ($tab_infos['code_mef_aaf']) ? Clean::texte($tab_infos['code_mef_aaf']) : $groupe_ref{0} ;
    $tab_groupes[$groupe_id] = array(
      'ref' => $groupe_ref,
      'nom' => $groupe_nom,
      'mef' => $groupe_mef,
    );
  }
}

// Les personnels
$tab_id_ent_to_id_user = array();
if(!empty($tab_annuaire['personnel']))
{
  foreach($tab_annuaire['personnel'] as $tab_infos)
  {
    $user_id     = Clean::entier($tab_infos['id']);
    $user_id_ent = Clean::id_ent($tab_infos['id_ent']);
    $user_nom    = Clean::nom($tab_infos['nom']) ;
    $user_prenom = Clean::prenom($tab_infos['prenom']) ;
    if( in_array( $tab_infos['profil_id'] , array('DIR','DOC','ENS') ) )
    {
      // Personnels de direction, enseignants y compris professeur documentaliste
      $profil_sigle = $tab_infos['profil_id'];
    }
    else if($tab_infos['profil_id']=='ETA')
    {
      if($tab_infos['libelle']=='EDUCATION')
      {
        // CPE
        $profil_sigle = 'EDU';
      }
      elseif($tab_infos['description']=='ENCADRE. SUR. DES ELEVES (HORS INTERNAT)')
      {
        // Surveillants
        $profil_sigle = 'SUR';
      }
      elseif($tab_infos['libelle']=='ASSISTANT D\'EDUCATION')
      {
        // AED
        $profil_sigle = 'AED';
      }
      elseif($tab_infos['libelle']=='ORIENTATION')
      {
        // Co-Psy
        $profil_sigle = 'ORI';
      }
      else
      {
        // On laisse tomber les personnels administratifs
        $profil_sigle = NULL;
      }
    }
    else
    {
      // On laisse tomber les personnels medico-sociaux
      $profil_sigle = NULL;
    }
    if($profil_sigle)
    {
      $user_profil = $profil_sigle;
      $user_matiere = ($user_profil=='ENS') ? Clean::texte($tab_infos['description']) : NULL ;
      $tab_personnels[$user_id] = array(
        'id_ent'  => $user_id_ent,
        'nom'     => $user_nom,
        'prenom'  => $user_prenom,
        'profil'  => $user_profil,
        'matiere' => $user_matiere,
        'login'   => '',
        'email'   => '',
        'classes' => array(),
        'groupes' => array(),
      );
      $tab_id_ent_to_id_user[$user_id_ent] = $user_id;
    }
  }
}

// Les administrateurs
if(!empty($tab_annuaire['contacts']))
{
  foreach($tab_annuaire['contacts'] as $tab_infos)
  {
    if( ($tab_infos['profil_id']=='ADM_ETB') && isset($tab_id_ent_to_id_user[$tab_infos['id_ent']]) )
    {
      /**
       * Remarque : les administrateurs ayant le même id_ent que leur compte personnel correspondant, il ne peut leur être appliqué un accès via l'ENT
       */
      $user_id     = $tab_id_ent_to_id_user[$tab_infos['id_ent']];
      $user_nom    = Clean::nom($tab_infos['nom']) ;
      $user_prenom = Clean::prenom($tab_infos['prenom']) ;
      $tab_administrateurs[$user_id] = array(
        'nom'     => $user_nom,
        'prenom'  => $user_prenom,
        'email'   => '',
      );
    }
  }
}

// Les élèves
if(!empty($tab_annuaire['eleves']))
{
  foreach($tab_annuaire['eleves'] as $tab_infos)
  {
    $user_id        = Clean::entier($tab_infos['user_id']);
    $user_sconet_id = Clean::entier($tab_infos['id_sconet']);
    $user_nom       = Clean::nom($tab_infos['nom']) ;
    $user_prenom    = Clean::prenom($tab_infos['prenom']) ;
    $user_id_ent    = Clean::id_ent($tab_infos['id_ent']);
    $tab_eleves[$user_id] = array(
      'sconet_id' => $user_sconet_id,
      'nom'       => $user_nom,
      'prenom'    => $user_prenom,
      'id_ent'    => $user_id_ent,
      'login'     => '',
      'classe'    => NULL,
      'groupes'   => array(),
    );
  }
}

// Les parents
if(!empty($tab_annuaire['parents']))
{
  foreach($tab_annuaire['parents'] as $tab_infos)
  {
    $user_id         = Clean::entier($tab_infos['user_id']);
    $user_nom        = Clean::nom($tab_infos['nom']) ;
    $user_prenom     = Clean::prenom($tab_infos['prenom']) ;
    $user_id_ent     = Clean::id_ent($tab_infos['id_ent']);
    $tab_parents[$user_id] = array(
      'nom'       => $user_nom,
      'prenom'    => $user_prenom,
      'id_ent'    => $user_id_ent,
      'login'     => '',
      'adresse'   => array(),
      'enfants'   => array(),
    );
  }
}

/****************************************************************************************************
 * Récupérer les informations sur les matières.
 ****************************************************************************************************/

$tab_annuaire = Laclasse::get_info_from_annuaire( $WS_uai , 'matieres' , TRUE /*exit_if_error*/ , TRUE /*with_details*/ );
if(!empty($tab_annuaire))
{
  foreach($tab_annuaire as $tab_infos)
  {
    /**
     * Exemples de valeurs trouvées pour { id ; libelle_long } :
     * 003800 / TRAITEMENT DES DIFFICULTES SCOLAIRES
     * 061300 / MATHEMATIQUES
     * 062300 / PHYSIQUE-CHIMIE
     * 100D00 / COURSE ORIENTATION
     * 030602 / ESPAGNOL LV2
     */
    if(ctype_digit($tab_infos['id']))
    {
      $id = Clean::entier($tab_infos['id']);
      $matiere_id      = ($id%100==0) ? $id/100 : NULL ;
      $matiere_libelle = Clean::texte($tab_infos['libelle_long']);
      $tab_matieres[$id] = array(
        'matiere_id'      => $matiere_id,
        'matiere_libelle' => $matiere_libelle,
      );
    }
  }
}

/****************************************************************************************************
 * Récupérer les adresses mails des personnels et les affectations aux classes / groupes.
 ****************************************************************************************************/

$tab_annuaire = Laclasse::get_info_from_annuaire( $WS_uai , 'users' , TRUE /*exit_if_error*/ , TRUE /*with_details*/ );
if(!empty($tab_annuaire['data']))
{
  foreach($tab_annuaire['data'] as $tab_infos)
  {
    $user_id = Clean::entier($tab_infos['id']);
    // Cas d'un personnel : on ajoute login et email
    if(isset($tab_personnels[$user_id]))
    {
      $tab_personnels[$user_id]['login'] = Clean::lower($tab_infos['login']);
      $tab_personnels[$user_id]['email'] = Clean::courriel($tab_infos['emails'][0]['adresse']);
      // Cas d'un prof : on ajoute classes et groupes
      if( ($tab_personnels[$user_id]['profil']=='ENS') && (!empty($tab_infos['enseigne_regroupements'])) )
      {
        foreach($tab_infos['enseigne_regroupements'] as $tab_infos_groupe)
        {
          // Il y a des profs sur plusieurs établissements...
          if($tab_infos_groupe['etablissement_code_uai']==$WS_uai)
          {
            $groupe_id = Clean::entier($tab_infos_groupe['id']);
            if( ($tab_infos_groupe['type']=='CLS') && (isset($tab_classes[$groupe_id])) )
            {
              $tab_personnels[$user_id]['classes'][] = $groupe_id;
            }
            else if( ($tab_infos_groupe['type']=='GRP') && (isset($tab_groupes[$groupe_id])) )
            {
              $tab_personnels[$user_id]['groupes'][] = $groupe_id;
            }
          }
        }
      }
    }
    // Cas d'un élève : on ajoute son login, sa classe et ses groupes
    if(isset($tab_eleves[$user_id]))
    {
      $tab_eleves[$user_id]['login'] = Clean::lower($tab_infos['login']);
      if(!empty($tab_infos['eleve_regroupements']))
      {
        foreach($tab_infos['eleve_regroupements'] as $tab_infos_groupe)
        {
          // Au cas où il y aurait des élèves sur plusieurs établissements...
          if($tab_infos_groupe['etablissement_code_uai']==$WS_uai)
          {
            $groupe_id = Clean::entier($tab_infos_groupe['id']);
            if( ($tab_infos_groupe['type']=='CLS') && (isset($tab_classes[$groupe_id])) )
            {
              $tab_eleves[$user_id]['classe'] = $groupe_id;
            }
            else if( ($tab_infos_groupe['type']=='GRP') && (isset($tab_groupes[$groupe_id])) )
            {
              $tab_eleves[$user_id]['groupes'][] = $groupe_id;
            }
          }
        }
      }
    }
    // Cas d'un parent : on ajoute son login + en théorie son adresse et ses liens aux élèves
    if(isset($tab_parents[$user_id]))
    {
      $tab_parents[$user_id]['login'] = Clean::lower($tab_infos['login']);
    }

  }
}

// On renseigne aussi les adresses mail des administrateurs.
// On a besoin qu'il y ait au moins un administrateur trouvé, dont au moins un avec un mail pour être contact et y envoyer les infos.
if(empty($tab_administrateurs))
{
  exit_json(400, 'Aucun administrateur trouvé.');

}
$nb_contacts = 0;
foreach($tab_administrateurs as $user_id => $tab_infos)
{
  if( isset($tab_personnels[$user_id]) && !empty($tab_personnels[$user_id]['email']) )
  {
    $tab_administrateurs[$user_id]['email'] = $tab_personnels[$user_id]['email'];
    $nb_contacts++;
  }
}
if(!$nb_contacts)
{
  exit_json(400, 'Aucun contact potentiel trouvé (administrateur(s) sans adresse mail).');
}



/****************************************************************************************************
 * Code de test à décommenter pour afficher le contenu des tableaux obtenus.
 ****************************************************************************************************/

$tableaux = array( 'tab_etablissement' , 'tab_classes' , 'tab_groupes' , 'tab_personnels' , 'tab_administrateurs' , 'tab_eleves' , 'tab_parents' , 'tab_matieres' );

echo'<pre>';
echo'<p id="top">';
foreach($tableaux as $tableau)
{
  echo'[<a href="#'.$tableau.'">'.$tableau.'</a>]&nbsp;&nbsp;&nbsp;';
}
echo'</p>';
foreach($tableaux as $tableau)
{
  echo'<div id="'.$tableau.'" style="font-weight:bold;background-color:#FF0;padding:1em;border:solid black 1px">'.$tableau.' [<a href="#top">top</a>]</div>';
  print_r(${$tableau});
}
echo'</pre>';
exit;
/*
*/

/****************************************************************************************************
 * Suite du code...
 ****************************************************************************************************/

$etabl_geo_id       = 1;

// Personnaliser certains paramètres de la structure
$tab_parametres = array();
$tab_parametres['version_base']               = VERSION_BASE_STRUCTURE;
$tab_parametres['webmestre_uai']              = $WS_uai;
// $tab_parametres['webmestre_denomination']     = $denomination;
  /* AJOUT DEBUT */
$tab_parametres['etablissement_denomination'] = $tab_etablissement['denomination'];
$tab_parametres['connexion_departement'] = '69';
$tab_parametres['connexion_mode']        = 'cas';
$tab_parametres['connexion_nom']         = 'laclasse';
$tab_parametres['cas_serveur_host']      = 'www.laclasse.com';
$tab_parametres['cas_serveur_port']      = '443';
$tab_parametres['cas_serveur_root']      = 'sso';
  /* AJOUT FIN */

/*
 * ****************************************************************************************************
 *  Point d'entrée du mode api
 * 
 * La démarche est la suivante :
 *  1. Création d'un établissement
 *  2. Paramétrage automatique du sso cas, en fonction de la configuration
 *  3. Nomination des administrateurs SACoche (les administrateurs d'établissement de l'ENT et les Superadmins, s'il y en a)
 *  4. Création des élèves
 *  5. création des Personnels de l'Education Nationale
 *  6. Création des parents
 * 
 * ***************************************************************************************************
 */

exit_json(200, "OK");

?>