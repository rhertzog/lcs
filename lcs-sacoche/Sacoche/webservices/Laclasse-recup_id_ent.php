<?php
/* 
support_administrateur__gestion_mode_identification__laclasse
*/

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}

/**
 * Ce fichier n'est présent que sur l'installation SACoche du département du Rhône.
 * 
 * Il est chargé par ./pages/administrateur_fichier_identifiant.ajax.php
 * Son adresse est renseignée par la constante CHEMIN_FICHIER_WS_LACLASSE.
 * 
 * Il interroge l'annuaire pour récupérer les identifiants ENT.
 */

require(CHEMIN_DOSSIER_WEBSERVICES.'Laclasse.class.php');

/**
 * recuperer_infos_Laclasse
 * 
 * @param string   $UAI
 * @return array|string
 */
function recuperer_infos_Laclasse($UAI)
{
  // Appeler l'annuaire ENT Laclasse.com
  $tab_Laclasse = Laclasse::get_info_from_annuaire( $UAI , '' , FALSE /*exit_if_error*/ , TRUE /*with_details*/ );
  // Enregistrer la réponse pour aider au débuggage si besoin
  FileSystem::ecrire_fichier( CHEMIN_DOSSIER_IMPORT.'Laclasse_'.$UAI.'_recup_IdEnt_'.fabriquer_fin_nom_fichier__date_et_alea().'.txt' , print_r($tab_Laclasse,TRUE) );
  // On examine la réponse
  if(!is_array($tab_Laclasse))
  {
    exit($tab_Laclasse);
  }
  // Pour récupérer les données des utilisateurs
  $tab_users_annuaire              = array();
  $tab_users_annuaire['ordre']     = array();
  $tab_users_annuaire['profil']    = array();
  $tab_users_annuaire['id_ent']    = array();
  $tab_users_annuaire['nom']       = array();
  $tab_users_annuaire['prenom']    = array();
  $tab_users_annuaire['id_sconet'] = array(); // Ne servira que pour les élèves
  if(!empty($tab_Laclasse['personnel']))
  {
    foreach($tab_Laclasse['personnel'] as $tab_infos)
    {
      $user_profil = NULL;
      if( in_array( $tab_infos['profil_id'] , array('DIR','DOC','ENS') ) )
      {
        // Personnels de direction, enseignants y compris professeur documentaliste
        $ordre = 1;
        $user_profil = $tab_infos['profil_id'];
      }
      else if($tab_infos['profil_id']=='ETA')
      {
        if($tab_infos['libelle']=='EDUCATION')
        {
          // CPE
          $ordre = 1;
          $user_profil = 'EDU';
        }
        elseif($tab_infos['description']=='ENCADRE. SUR. DES ELEVES (HORS INTERNAT)')
        {
          // Surveillants
          $ordre = 4;
          $user_profil = 'SUR';
        }
        elseif($tab_infos['libelle']=='ASSISTANT D\'EDUCATION')
        {
          // AED
          $ordre = 4;
          $user_profil = 'AED';
        }
        elseif($tab_infos['libelle']=='ORIENTATION')
        {
          // Co-Psy
          $ordre = 4;
          $user_profil = 'ORI';
        }
        elseif($tab_infos['libelle']=='PERSONNELS ADMINISTRATIFS')
        {
          // Personnels administratifs
          $ordre = 4;
          $user_profil = 'ADF';
        }
      }
      else if($tab_infos['profil_id']=='EVS')
      {
        // Personnels medico-sociaux
        $ordre = 4;
        $user_profil = 'MDS';
      }
      if($user_profil)
      {
        $tab_users_annuaire['ordre'    ][] = $ordre;
        $tab_users_annuaire['profil'   ][] = $user_profil;
        $tab_users_annuaire['id_ent'   ][] = Clean::id_ent($tab_infos['id_ent']);
        $tab_users_annuaire['nom'      ][] = Clean::nom($tab_infos['nom']);
        $tab_users_annuaire['prenom'   ][] = Clean::prenom($tab_infos['prenom']);
        $tab_users_annuaire['id_sconet'][] = NULL;
      }
    }
  }
  // Les élèves
  if(!empty($tab_Laclasse['eleves']))
  {
    foreach($tab_Laclasse['eleves'] as $tab_infos)
    {
      $tab_users_annuaire['ordre'    ][] = 2;
      $tab_users_annuaire['profil'   ][] = 'ELV';
      $tab_users_annuaire['id_ent'   ][] = Clean::id_ent($tab_infos['id_ent']);
      $tab_users_annuaire['nom'      ][] = Clean::nom($tab_infos['nom']);
      $tab_users_annuaire['prenom'   ][] = Clean::prenom($tab_infos['prenom']);
      $tab_users_annuaire['id_sconet'][] = Clean::entier($tab_infos['id_sconet']);
    }
  }
  // Les parents
  if(!empty($tab_Laclasse['parents']))
  {
    foreach($tab_Laclasse['parents'] as $tab_infos)
    {
      $tab_users_annuaire['ordre'    ][] = 3;
      $tab_users_annuaire['profil'   ][] = 'TUT';
      $tab_users_annuaire['id_ent'   ][] = Clean::id_ent($tab_infos['id_ent']);
      $tab_users_annuaire['nom'      ][] = Clean::nom($tab_infos['nom']);
      $tab_users_annuaire['prenom'   ][] = Clean::prenom($tab_infos['prenom']);
      $tab_users_annuaire['id_sconet'][] = NULL;
    }
  }
  // On trie
  array_multisort(
    $tab_users_annuaire['ordre'] , SORT_ASC,SORT_NUMERIC,
    $tab_users_annuaire['profil'], SORT_ASC,SORT_STRING,
    $tab_users_annuaire['nom']   , SORT_ASC,SORT_STRING,
    $tab_users_annuaire['prenom'], SORT_ASC,SORT_STRING,
    $tab_users_annuaire['id_ent'],
    $tab_users_annuaire['id_sconet']
  );
  // On retire l'ordre dont on n'a plus besoin
  unset($tab_users_annuaire['ordre']);
  // On retourne le tableau
  return $tab_users_annuaire;
}

?>