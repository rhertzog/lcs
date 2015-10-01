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
if(!isset($STEP))       {exit('Ce fichier ne peut être appelé directement !');}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Étape 20 - Extraction des données (tous les cas)
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if(!is_file(CHEMIN_DOSSIER_IMPORT.$fichier_dest))
{
  exit('Erreur : le fichier récupéré et enregistré n\'a pas été retrouvé !');
}

// Pour récupérer les données des utilisateurs ; on prend comme indice $sconet_id ou $reference suivant le mode d'import
/*
 * On utilise la forme moins commode   ['nom'][i]=... ['prenom'][i]=...
 * au lieu de la forme plus habituelle [i]['nom']=... [i]['prenom']=...
 * parce qu'ensuite cela permet d'effectuer un tri multicolonnes.
 */
$tab_users_fichier                 = array();
$tab_users_fichier['sconet_id']    = array();
$tab_users_fichier['sconet_num']   = array();
$tab_users_fichier['reference']    = array();
$tab_users_fichier['profil_sigle'] = array(); // Notamment pour distinguer les personnels
$tab_users_fichier['genre']        = array();
$tab_users_fichier['nom']          = array();
$tab_users_fichier['prenom']       = array();
$tab_users_fichier['classe']       = array(); // Avec id sconet_id ou reference // Classe de l'élève || Classes du professeur, avec indication PP
$tab_users_fichier['groupe']       = array(); // Avec id sconet_id // Groupes de l'élève || Groupes du professeur
$tab_users_fichier['matiere']      = array(); // Avec id sconet_id // Matières du professeur, avec indication méthode récupération
$tab_users_fichier['adresse']      = array(); // Avec id sconet_id // Adresse du responsable légal
$tab_users_fichier['enfant']       = array(); // Avec id sconet_id // Liste des élèves rattachés
$tab_users_fichier['birth_date']   = array();
$tab_users_fichier['courriel']     = array();

// Pour récupérer les données des classes et des groupes
$tab_classes_fichier['ref']    = array();
$tab_classes_fichier['nom']    = array();
$tab_classes_fichier['niveau'] = array();
$tab_groupes_fichier['ref']    = array();
$tab_groupes_fichier['nom']    = array();
$tab_groupes_fichier['niveau'] = array();

// Pour retenir à part les dates de sortie Sconet des élèves
$_SESSION['tmp']['date_sortie'] = array();

$init_negatif = -1000;

function filter_init_negatif($var)
{
  global $init_negatif;
  return($var==$init_negatif);
}

// On passe aux différentes procédures selon le mode d'import...

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction sconet_professeurs_directeurs
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='sconet') && ($import_profil=='professeur') )
{
  $xml = @simplexml_load_file(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  if($xml===FALSE)
  {
    exit('Erreur : le fichier transmis n\'est pas un XML valide !');
  }
  $editeur_prive_edt = (string)$xml->PARAMETRES->APPLICATION_SOURCE;
  if($editeur_prive_edt)
  {
    exit('Erreur : le fichier transmis est issu d\'un éditeur privé d\'emploi du temps, pas de STS !');
  }
  $uai = @(string)$xml->PARAMETRES->UAJ->attributes()->CODE;
  if(!$uai)
  {
    exit('Erreur : le contenu du fichier transmis ne correspond pas à ce qui est attendu !');
  }
  if($uai!=$_SESSION['WEBMESTRE_UAI'])
  {
    exit('Erreur : le fichier transmis est issu de l\'établissement '.$uai.' et non '.$_SESSION['WEBMESTRE_UAI'].' !');
  }
  /*
   * Les matières des profs peuvent être récupérées de 2 façons :
   * 1. $xml->DONNEES->INDIVIDUS->INDIVIDU->DISCIPLINES->DISCIPLINE->attributes()->CODE
   *    On récupère alors un code formé d'une lettre (L ou C) et de 4 chiffres (matières que le prof est apte à enseigner, son service peut préciser les choses...).
   *    Je n'ai pas trouvé de correspondance officielle -> Le tableau $tab_discipline_code_discipline_TO_matiere_code_gestion donne les principales.
   */
  $tab_discipline_code_discipline_TO_matiere_code_gestion = array();
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.0080'] = array('DOC');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.0100'] = array('PHILO');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.02..'] = array('FRANC');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.0421'] = array('ALL1','ALL2');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.0422'] = array('AGL1','AGL2');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.0424'] = array('CHI1','CHI2');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.0426'] = array('ESP1','ESP2');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.0429'] = array('ITA1','ITA2');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.0433'] = array('POR1','POR2');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.0434'] = array('RUS1','RUS2');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1000'] = array('HIGEO','EDCIV');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1100'] = array('SES');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1300'] = array('MATHS');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1315'] = array('MATHS','PH-CH');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1400'] = array('TECHN');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1500'] = array('PH-CH');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1600'] = array('SVT');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1615'] = array('SVT','PH-CH');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1700'] = array('EDMUS');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1800'] = array('A-PLA');
  $tab_discipline_code_discipline_TO_matiere_code_gestion['.1900'] = array('EPS');
  /*
   * Les matières des profs peuvent être récupérées de 2 façons :
   * 2. $xml->DONNEES->STRUCTURES->DIVISIONS->DIVISION->SERVICES->SERVICE->attributes()->CODE_MATIERE
   *    On récupère alors, si l'emploi du temps est rensigné, un code expliqué dans $xml->NOMENCLATURES->MATIERES->MATIERE.
   *    -> Le tableau $tab_matiere_code_matiere_TO_matiere_code_gestion liste ce contenu des nomenclatures.
   */
  $tab_matiere_code_matiere_TO_matiere_code_gestion = array();
  if( ($xml->NOMENCLATURES) && ($xml->NOMENCLATURES->MATIERES) && ($xml->NOMENCLATURES->MATIERES->MATIERE) )
  {
    foreach ($xml->NOMENCLATURES->MATIERES->MATIERE as $matiere)
    {
      $matiere_code_matiere = (string) $matiere->attributes()->CODE; // (string) obligatoire sinon il n'aime pas une clef commençant par 0...
      $matiere_code_gestion = (string) $matiere->CODE_GESTION; // (string) obligatoire sinon il n'aime pas une clef commençant par 0...
      $tab_matiere_code_matiere_TO_matiere_code_gestion[$matiere_code_matiere] = $matiere_code_gestion;
    }
  }
  //
  // On passe les utilisateurs en revue : on mémorise leurs infos, y compris les PP, d'éventuelles matières affectées, d'éventuelles classes présentes
  //
  $date_aujourdhui = date('Y-m-d');
  if( ($xml->DONNEES) && ($xml->DONNEES->INDIVIDUS) && ($xml->DONNEES->INDIVIDUS->INDIVIDU) )
  {
    $tab_genre = array( 0=>'I' , 1=>'M' , 2=>'F' );
    foreach ($xml->DONNEES->INDIVIDUS->INDIVIDU as $individu)
    {
      $fonction = Clean::ref($individu->FONCTION) ;
      if( (isset($_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$fonction])) && (in_array($_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$fonction],array('professeur','directeur'))) )
      {
        $sconet_id = Clean::entier($individu->attributes()->ID);
        $civilite  = Clean::entier($individu->SEXE); // L'attribut <CIVILITE> est aussi présent et apparemment identique.
        $i_fichier  = $sconet_id;
        $tab_users_fichier['sconet_id'   ][$i_fichier] = $sconet_id;
        $tab_users_fichier['sconet_num'  ][$i_fichier] = 0;
        $tab_users_fichier['reference'   ][$i_fichier] = '';
        $tab_users_fichier['profil_sigle'][$i_fichier] = $fonction;
        $tab_users_fichier['genre'       ][$i_fichier] = isset($tab_genre[$civilite]) ? $tab_genre[$civilite] : 'I' ;
        $tab_users_fichier['nom'         ][$i_fichier] = Clean::nom($individu->NOM_USAGE);
        $tab_users_fichier['prenom'      ][$i_fichier] = Clean::prenom($individu->PRENOM);
        $tab_users_fichier['courriel'    ][$i_fichier] = '';
        $tab_users_fichier['classe'      ][$i_fichier] = array();
        $tab_users_fichier['groupe'      ][$i_fichier] = array();
        $tab_users_fichier['matiere'     ][$i_fichier] = array();
        // Indication éventuelle de professeur principal
        if( ($individu->PROFS_PRINC) && ($individu->PROFS_PRINC->PROF_PRINC) )
        {
          foreach ($individu->PROFS_PRINC->PROF_PRINC as $prof_princ)
          {
            $classe_ref = Clean::ref($prof_princ->CODE_STRUCTURE);
            $date_fin   = Clean::ref($prof_princ->DATE_FIN);
            $i_classe   = 'i'.Clean::login($classe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
            if($date_fin >= $date_aujourdhui)
            {
              $tab_users_fichier['classe'][$i_fichier][$i_classe] = 'PP';
            }
            // Au passage on ajoute la classe trouvée
            if(!isset($tab_classes_fichier['ref'][$i_classe]))
            {
              $tab_classes_fichier['ref'][$i_classe]    = $classe_ref;
              $tab_classes_fichier['nom'][$i_classe]    = $classe_ref;
              $tab_classes_fichier['niveau'][$i_classe] = $classe_ref;
            }
          }
        }
        // Indication éventuelle des matières du professeur (toujours renseigné pour les profs, mais matières potentielles et non effectivement enseignées, et usage d'un code discipline pas commode à décrypter)
        if( ($individu->DISCIPLINES) && ($individu->DISCIPLINES->DISCIPLINE) )
        {
          foreach ($individu->DISCIPLINES->DISCIPLINE as $discipline)
          {
            $discipline_code_discipline = (string) $discipline->attributes()->CODE;
            foreach ($tab_discipline_code_discipline_TO_matiere_code_gestion as $masque_recherche => $tab_matiere_code_gestion)
            {
              if(preg_match('/^'.$masque_recherche.'$/',$discipline_code_discipline))
              {
                foreach ($tab_matiere_code_gestion as $matiere_code_gestion)
                {
                  $tab_users_fichier['matiere'][$i_fichier][$matiere_code_gestion] = 'discipline';
                }
                break;
              }
            }
          }
        }
      }
    }
  }
  //
  // On passe les classes en revue : on mémorise leurs infos, y compris les profs rattachés éventuels, et les matières associées
  //
  if( ($xml->DONNEES) && ($xml->DONNEES->STRUCTURE) && ($xml->DONNEES->STRUCTURE->DIVISIONS) && ($xml->DONNEES->STRUCTURE->DIVISIONS->DIVISION) )
  {
    foreach ($xml->DONNEES->STRUCTURE->DIVISIONS->DIVISION as $division)
    {
      $classe_ref = Clean::ref($division->attributes()->CODE);
      $classe_nom = Clean::texte($division->LIBELLE_LONG);
      $i_classe   = 'i'.Clean::login($classe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
      // Au passage on ajoute la classe trouvée
      if(!isset($tab_classes_fichier['ref'][$i_classe]))
      {
        $tab_classes_fichier['ref'   ][$i_classe] = $classe_ref;
        $tab_classes_fichier['nom'   ][$i_classe] = $classe_nom;
        $tab_classes_fichier['niveau'][$i_classe] = $classe_ref;
      }
      else
      {
        $tab_classes_fichier['nom'][$i_classe]    = $classe_nom;
      }
      if( ($division->SERVICES) && ($division->SERVICES->SERVICE) )
      {
        foreach ($division->SERVICES->SERVICE as $service)
        {
          $matiere_code_matiere = (string) $service->attributes()->CODE_MATIERE; // (string) obligatoire sinon pb avec une clef commençant par 0...
          if( ($service->ENSEIGNANTS) && ($service->ENSEIGNANTS->ENSEIGNANT) )
          {
            foreach ($service->ENSEIGNANTS->ENSEIGNANT as $enseignant)
            {
              $i_fichier = Clean::entier($enseignant->attributes()->ID);
              // Il arrive que des individus soient présents dans le fichier mais sans fonction ($xml->DONNEES->INDIVIDUS->INDIVIDU->FONCTION)
              // Ce peut être un congé longue maladie, un congé maternité, une retraite en cours d'année...
              // Du coup, ils ne sont pas récupérés dans $tab_users_fichier[]
              // Par contre, il peut y avoir dans le fichier des reliquats de services (associations classes et matières)
              // Il faut les ignorer sous peine de récolter "array_multisort(): Array sizes are inconsistent" en fin d'étape 2.
              if(isset($tab_users_fichier['sconet_id'][$i_fichier]))
              {
                // associer la classe au prof
                if(!isset($tab_users_fichier['classe'][$i_fichier][$i_classe]))
                {
                  $tab_users_fichier['classe'][$i_fichier][$i_classe] = 'prof';
                }
                // associer la matière au prof
                if(isset($tab_matiere_code_matiere_TO_matiere_code_gestion[$matiere_code_matiere]))
                {
                  $matiere_code_gestion = $tab_matiere_code_matiere_TO_matiere_code_gestion[$matiere_code_matiere];
                  $tab_users_fichier['matiere'][$i_fichier][$matiere_code_gestion] = 'service';
                }
              }
            }
          }
        }
      }
    }
  }
  //
  // On passe les groupes en revue : on mémorise leurs infos, y compris les profs rattachés éventuels, et les matières associées
  //
  if( ($xml->DONNEES) && ($xml->DONNEES->STRUCTURE) && ($xml->DONNEES->STRUCTURE->GROUPES) && ($xml->DONNEES->STRUCTURE->GROUPES->GROUPE) )
  {
    foreach ($xml->DONNEES->STRUCTURE->GROUPES->GROUPE as $groupe)
    {
      $groupe_ref = Clean::ref($groupe->attributes()->CODE);
      $groupe_nom = Clean::texte($groupe->LIBELLE_LONG);
      $i_groupe   = 'i'.Clean::login($groupe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
      // Au passage on ajoute le groupe trouvé
      if(!isset($tab_groupes_fichier['ref'][$i_groupe]))
      {
        $tab_groupes_fichier['ref'   ][$i_groupe] = $groupe_ref;
        $tab_groupes_fichier['nom'   ][$i_groupe] = $groupe_nom;
        $tab_groupes_fichier['niveau'][$i_groupe] = $groupe_ref;
      }
      if( ($groupe->SERVICES) && ($groupe->SERVICES->SERVICE) )
      {
        foreach ($groupe->SERVICES->SERVICE as $service)
        {
          $matiere_code_matiere = (string) $service->attributes()->CODE_MATIERE; // (string) obligatoire sinon il n'aime pas une clef commençant par 0...
          if( ($service->ENSEIGNANTS) && ($service->ENSEIGNANTS->ENSEIGNANT) )
          {
            foreach ($service->ENSEIGNANTS->ENSEIGNANT as $enseignant)
            {
              $i_fichier = Clean::entier($enseignant->attributes()->ID);
              // Il arrive que des individus soient présents dans le fichier mais sans fonction ($xml->DONNEES->INDIVIDUS->INDIVIDU->FONCTION)
              // Ce peut être un congé longue maladie, un congé maternité, une retraite en cours d'année...
              // Du coup, ils ne sont pas récupérés dans $tab_users_fichier[]
              // Par contre, il peut y avoir dans le fichier des reliquats de services (associations classes et matières)
              // Il faut les ignorer sous peine de récolter "array_multisort(): Array sizes are inconsistent" en fin d'étape 2.
              if(isset($tab_users_fichier['sconet_id'][$i_fichier]))
              {
                // associer le groupe au prof
                if(!isset($tab_users_fichier['groupe'][$i_fichier][$i_groupe]))
                {
                  $tab_users_fichier['groupe'][$i_fichier][$i_groupe] = 'prof';
                }
                // associer la matière au prof
                if(isset($tab_matiere_code_matiere_TO_matiere_code_gestion[$matiere_code_matiere]))
                {
                  $matiere_code_gestion = $tab_matiere_code_matiere_TO_matiere_code_gestion[$matiere_code_matiere];
                  $tab_users_fichier['matiere'][$i_fichier][$matiere_code_gestion] = 'service';
                }
              }
            }
          }
        }
      }
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction sconet_eleves
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='sconet') && ($import_profil=='eleve') )
{
  $xml = @simplexml_load_file(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  if($xml===FALSE)
  {
    exit('Erreur : le fichier transmis n\'est pas un XML valide !');
  }
  $uai = $xml->PARAMETRES->UAJ;
  if($uai===FALSE)
  {
    exit('Erreur : le fichier transmis n\'est pas correct (erreur de numéro UAI) !');
  }
  // tableau temporaire qui sera effacé, servant à retenir le niveau de l'élève en attendant de connaître sa classe.
  $tab_users_fichier['niveau'] = array();
  //
  // On passe les utilisateurs en revue : on mémorise leurs infos, plus leur niveau
  //
  if( ($xml->DONNEES) && ($xml->DONNEES->ELEVES) && ($xml->DONNEES->ELEVES->ELEVE) )
  {
    $tab_genre = array( 0=>'I' , 1=>'M' , 2=>'F' );
    foreach ($xml->DONNEES->ELEVES->ELEVE as $eleve)
    {
      $i_fichier = Clean::entier($eleve->attributes()->ELEVE_ID);
      $civilite  = Clean::entier($eleve->CODE_SEXE);
      if($eleve->DATE_SORTIE)
      {
        $_SESSION['tmp']['date_sortie'][$i_fichier] = (string) $eleve->DATE_SORTIE; // format fr (jj/mm/aaaa)
      }
      else
      {
        $tab_users_fichier['sconet_id'   ][$i_fichier] = $i_fichier;
        $tab_users_fichier['sconet_num'  ][$i_fichier] = Clean::entier($eleve->attributes()->ELENOET);
        $tab_users_fichier['reference'   ][$i_fichier] = Clean::ref($eleve->ID_NATIONAL);
        $tab_users_fichier['profil_sigle'][$i_fichier] = 'ELV' ;
        $tab_users_fichier['genre'       ][$i_fichier] = isset($tab_genre[$civilite]) ? $tab_genre[$civilite] : 'I' ;
        $tab_users_fichier['nom'         ][$i_fichier] = Clean::nom($eleve->NOM);
        $tab_users_fichier['prenom'      ][$i_fichier] = Clean::prenom($eleve->PRENOM);
        $tab_users_fichier['birth_date'  ][$i_fichier] = Clean::texte($eleve->DATE_NAISS);
        $tab_users_fichier['courriel'    ][$i_fichier] = Clean::courriel($eleve->MEL);
        $tab_users_fichier['classe'      ][$i_fichier] = '';
        $tab_users_fichier['groupe'      ][$i_fichier] = array();
        $tab_users_fichier['niveau'      ][$i_fichier] = Clean::ref($eleve->CODE_MEF);
      }
    }
  }
  //
  // On passe les liaisons élèves/classes-groupes en revue : on mémorise leurs infos, et les élèves rattachés
  //
  if( ($xml->DONNEES) && ($xml->DONNEES->STRUCTURES) && ($xml->DONNEES->STRUCTURES->STRUCTURES_ELEVE) )
  {
    foreach ($xml->DONNEES->STRUCTURES->STRUCTURES_ELEVE as $structures_eleve)
    {
      $i_fichier = Clean::entier($structures_eleve->attributes()->ELEVE_ID);
      if(!isset($_SESSION['tmp']['date_sortie'][$i_fichier]))  // les élèves marqués comme sortis de l'établissement sont encore dans le fichier reliés à une classe et d'autres bricoles...
      {
        foreach ($structures_eleve->STRUCTURE as $structure)
        {
          if($structure->TYPE_STRUCTURE == 'D')
          {
            $classe_ref = Clean::ref($structure->CODE_STRUCTURE);
            $i_classe   = 'i'.Clean::login($classe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
            $tab_users_fichier['classe'][$i_fichier] = $i_classe;
            if(!isset($tab_classes_fichier['ref'][$i_classe]))
            {
              $tab_classes_fichier['ref'   ][$i_classe] = $classe_ref;
              $tab_classes_fichier['nom'   ][$i_classe] = $classe_ref;
              $tab_classes_fichier['niveau'][$i_classe] = '';
            }
            if($tab_users_fichier['niveau'][$i_fichier])
            {
              $tab_classes_fichier['niveau'][$i_classe] = $tab_users_fichier['niveau'][$i_fichier];
            }
          }
          elseif($structure->TYPE_STRUCTURE == 'G')
          {
            $groupe_ref = Clean::ref($structure->CODE_STRUCTURE);
            $i_groupe   = 'i'.Clean::login($groupe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
            if(!isset($tab_users_fichier['groupe'][$i_fichier][$i_groupe]))
            {
              $tab_users_fichier['groupe'][$i_fichier][$i_groupe] = $groupe_ref;
            }
            if(!isset($tab_groupes_fichier['ref'][$i_groupe]))
            {
              $tab_groupes_fichier['ref'   ][$i_groupe] = $groupe_ref;
              $tab_groupes_fichier['nom'   ][$i_groupe] = $groupe_ref;
              $tab_groupes_fichier['niveau'][$i_groupe] = '';
            }
            if($tab_users_fichier['niveau'][$i_fichier])
            {
              $tab_groupes_fichier['niveau'][$i_groupe] = $tab_users_fichier['niveau'][$i_fichier];
            }
          }
        }
      }
    }
  }
  // suppression du tableau temporaire
  unset($tab_users_fichier['niveau']);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction sconet_parents
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='sconet') && ($import_profil=='parent') )
{
  $xml = @simplexml_load_file(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  if($xml===FALSE)
  {
    exit('Erreur : le fichier transmis n\'est pas un XML valide !');
  }
  $uai = (string)$xml->PARAMETRES->UAJ;
  if(!$uai)
  {
    exit('Erreur : le fichier transmis ne comporte pas de numéro UAI !');
  }
  if($uai!=$_SESSION['WEBMESTRE_UAI'])
  {
    exit('Erreur : le fichier transmis est issu de l\'établissement '.$uai.' et non '.$_SESSION['WEBMESTRE_UAI'].' !');
  }
  //
  // On recense les adresses dans un tableau temporaire.
  //
  $tab_adresses = array();
  if( ($xml->DONNEES) && ($xml->DONNEES->ADRESSES) && ($xml->DONNEES->ADRESSES->ADRESSE) )
  {
    foreach ($xml->DONNEES->ADRESSES->ADRESSE as $adresse)
    {
      $tab_adresses[Clean::entier($adresse->attributes()->ADRESSE_ID)] = array(
        Clean::adresse($adresse->LIGNE1_ADRESSE) ,
        Clean::adresse($adresse->LIGNE2_ADRESSE) ,
        Clean::adresse($adresse->LIGNE3_ADRESSE) ,
        Clean::adresse($adresse->LIGNE4_ADRESSE) ,
        Clean::entier($adresse->CODE_POSTAL) ,
        Clean::commune($adresse->LIBELLE_POSTAL) ,
        Clean::pays($adresse->LL_PAYS) ,
      );
    }
  }
  $nb_adresses = count($tab_adresses);
  // L'import Sconet peut apporter beaucoup de parents rattachés à des élèves sortis de l'établissement et encore présents dans le fichier.
  // Alors on récupère la liste des id_sconet des élèves actuels et on contrôle par la suite qu'il est dans la liste des enfants du parent.
  // Par ailleurs, l'import de base-élèves n'utilise pas les id sconet : il est donc plus facile de prendre les id de la base comme indices du tableau des enfants pour harmoniser les procédures.
  $tab_eleves_actuels = array();
  $DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_users( 'eleve' /*profil_type*/ , 1 /*only_actuels*/ , 'user_id,user_sconet_id' /*liste_champs*/ , FALSE /*with_classe*/ , FALSE /*tri_statut*/ );
  foreach($DB_TAB as $DB_ROW)
  {
    $tab_eleves_actuels[$DB_ROW['user_sconet_id']] = $DB_ROW['user_id'];
  }
  //
  // On recense les liens de responsabilités dans un tableau temporaire.
  // On ne garde que les resp. légaux, les contacts n'ont pas à avoir accès aux notes ou à un éventuel bulletin.
  //
  $tab_enfants = array();
  $nb_lien_responsabilite = 0;
  if( ($xml->DONNEES) && ($xml->DONNEES->RESPONSABLES) && ($xml->DONNEES->RESPONSABLES->RESPONSABLE_ELEVE) )
  {
    foreach ($xml->DONNEES->RESPONSABLES->RESPONSABLE_ELEVE as $responsable)
    {
      $num_responsable = Clean::entier($responsable->RESP_LEGAL);
      if($num_responsable)
      {
        $eleve_sconet_id = Clean::entier($responsable->ELEVE_ID);
        if(isset($tab_eleves_actuels[$eleve_sconet_id]))
        {
          $tab_enfants[Clean::entier($responsable->PERSONNE_ID)][$tab_eleves_actuels[$eleve_sconet_id]] = $num_responsable;
          $nb_lien_responsabilite++;
        }
      }
    }
  }
  //
  // On passe les parents en revue : on mémorise leurs infos (dont adresses et enfants)
  // Si pas d'enfant trouvé, on laisse tomber, c'est en effet le choix par défaut de Gepi qui indique : "ne pas proposer d'ajouter les responsables non associés à des élèves (de telles entrées peuvent subsister en très grand nombre dans Sconet)".
  //
  if( ($xml->DONNEES) && ($xml->DONNEES->PERSONNES) && ($xml->DONNEES->PERSONNES->PERSONNE) )
  {
    $tab_genre = array( ''=>'I' , 'M.'=>'M' , 'MME'=>'F' );
    foreach ($xml->DONNEES->PERSONNES->PERSONNE as $personne)
    {
      $i_fichier = Clean::entier($personne->attributes()->PERSONNE_ID);
      $civilite  = Clean::texte($personne->LC_CIVILITE); // L'attribut <LL_CIVILITE> est aussi présent.
      if(isset($tab_enfants[$i_fichier]))
      {
        $i_adresse = Clean::entier($personne->ADRESSE_ID);
        $tab_users_fichier['sconet_id'   ][$i_fichier] = $i_fichier;
        $tab_users_fichier['sconet_num'  ][$i_fichier] = 0;
        $tab_users_fichier['reference'   ][$i_fichier] = '';
        $tab_users_fichier['profil_sigle'][$i_fichier] = 'TUT' ;
        $tab_users_fichier['genre'       ][$i_fichier] = isset($tab_genre[$civilite]) ? $tab_genre[$civilite] : 'I' ;
        $tab_users_fichier['nom'         ][$i_fichier] = Clean::nom($personne->NOM);
        $tab_users_fichier['prenom'      ][$i_fichier] = Clean::prenom($personne->PRENOM);
        $tab_users_fichier['courriel'    ][$i_fichier] = Clean::courriel($personne->MEL);
        $tab_users_fichier['adresse'     ][$i_fichier] = isset($tab_adresses[$i_adresse]) ? $tab_adresses[$i_adresse] : array('','','','',0,'','') ;
        $tab_users_fichier['enfant'      ][$i_fichier] = $tab_enfants[$i_fichier];
      }
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction tableur_professeurs_directeurs
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='tableur') && ($import_profil=='professeur') )
{
  $contenu = file_get_contents(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  $contenu = To::deleteBOM(To::utf8($contenu)); // Mettre en UTF-8 si besoin et retirer le BOM éventuel
  $tab_lignes = extraire_lignes($contenu); // Extraire les lignes du fichier
  $separateur = extraire_separateur_csv($tab_lignes[0]); // Déterminer la nature du séparateur
  unset($tab_lignes[0]); // Supprimer la 1e ligne
  //
  // On passe les utilisateurs en revue : on mémorise leurs infos
  //
  $tab_genre = array(
    0     =>'I' ,
    ''    =>'I' ,
    1     =>'M' ,
    'M'   =>'M' ,
    'M.'  =>'M' ,
    'G'   =>'M' ,
    2     =>'F' ,
    'MME' =>'F' ,
    'F'   =>'F' ,
  );
  foreach ($tab_lignes as $ligne_contenu)
  {
    $tab_elements = str_getcsv($ligne_contenu,$separateur);
    $tab_elements = array_slice($tab_elements,0,8);
    if(count($tab_elements)>=5)
    {
      list($reference,$genre,$nom,$prenom,$profil,$courriel,$classes,$groupes) = $tab_elements + array_fill(0,8,NULL); // Evite des NOTICE en initialisant les valeurs manquantes
      $profil = Clean::ref($profil);
      if( ($nom!='') && ($prenom!='') && isset($_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$profil]) && in_array($_SESSION['TAB_PROFILS_ADMIN']['TYPE'][$profil],array('professeur','directeur')) )
      {
        $tab_users_fichier['sconet_id'   ][] = 0;
        $tab_users_fichier['sconet_num'  ][] = 0;
        $tab_users_fichier['reference'   ][] = Clean::ref($reference);
        $tab_users_fichier['profil_sigle'][] = $profil;
        $tab_users_fichier['genre'       ][] = isset($tab_genre[$genre]) ? $tab_genre[$genre] : 'I' ;
        $tab_users_fichier['nom'         ][] = Clean::nom($nom);
        $tab_users_fichier['prenom'      ][] = Clean::prenom($prenom);
        $tab_users_fichier['courriel'    ][] = Clean::courriel($courriel);
        // classes
        $tab_user_classes = array();
        if(strlen($classes))
        {
          $tab_classes = explode('|',$classes);
          foreach ($tab_classes as $classe)
          {
            $classe_ref = mb_substr(Clean::ref($classe),0,8);
            $i_classe   = 'i'.Clean::login($classe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
            if( ($classe_ref) && (!isset($tab_classes_fichier['ref'][$i_classe])) )
            {
              $tab_classes_fichier['ref'   ][$i_classe] = $classe_ref;
              $tab_classes_fichier['nom'   ][$i_classe] = $classe_ref;
              $tab_classes_fichier['niveau'][$i_classe] = $classe_ref;
            }
            if(!isset($tab_user_classes[$i_classe]))
            {
              $tab_user_classes[$i_classe] = $classe_ref;
            }
          }
        }
        $tab_users_fichier['classe'][] = $tab_user_classes;
        // groupes
        $tab_user_groupes = array();
        if(strlen($groupes))
        {
          $tab_groupes = explode('|',$groupes);
          foreach ($tab_groupes as $groupe)
          {
            $groupe_ref = mb_substr(Clean::ref($groupe),0,8);
            $i_groupe   = 'i'.Clean::login($groupe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
            if( ($groupe_ref) && (!isset($tab_groupes_fichier['ref'][$i_groupe])) )
            {
              $tab_groupes_fichier['ref'   ][$i_groupe] = $groupe_ref;
              $tab_groupes_fichier['nom'   ][$i_groupe] = $groupe_ref;
              $tab_groupes_fichier['niveau'][$i_groupe] = $groupe_ref;
            }
            if(!isset($tab_user_groupes[$i_groupe]))
            {
              $tab_user_groupes[$i_groupe] = $groupe_ref;
            }
          }
        }
        $tab_users_fichier['groupe'][] = $tab_user_groupes;
      }
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction tableur_eleves
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='tableur') && ($import_profil=='eleve') )
{
  $contenu = file_get_contents(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  $contenu = To::deleteBOM(To::utf8($contenu)); // Mettre en UTF-8 si besoin et retirer le BOM éventuel
  $tab_lignes = extraire_lignes($contenu); // Extraire les lignes du fichier
  $separateur = extraire_separateur_csv($tab_lignes[0]); // Déterminer la nature du séparateur
  unset($tab_lignes[0]); // Supprimer la 1e ligne
  //
  // On passe les utilisateurs en revue : on mémorise leurs infos et les classes trouvées
  //
  $tab_genre = array(
    0     =>'I' ,
    ''    =>'I' ,
    1     =>'M' ,
    'M'   =>'M' ,
    'M.'  =>'M' ,
    'G'   =>'M' ,
    2     =>'F' ,
    'MME' =>'F' ,
    'F'   =>'F' ,
  );
  foreach ($tab_lignes as $ligne_contenu)
  {
    $tab_elements = str_getcsv($ligne_contenu,$separateur);
    $tab_elements = array_slice($tab_elements,0,8);
    if(count($tab_elements)>=6)
    {
      list(
        $reference ,
        $genre ,
        $nom ,
        $prenom ,
        $birth_date ,
        $courriel ,
        $classe ,
        $groupes
      ) = $tab_elements + array_fill(0,8,NULL); // Evite des NOTICE en initialisant les valeurs manquantes
      if( ($nom!='') && ($prenom!='') )
      {
        $tab_users_fichier['sconet_id'   ][] = 0;
        $tab_users_fichier['sconet_num'  ][] = 0;
        $tab_users_fichier['reference'   ][] = Clean::ref($reference);
        $tab_users_fichier['profil_sigle'][] = 'ELV';
        $tab_users_fichier['genre'       ][] = isset($tab_genre[$genre]) ? $tab_genre[$genre] : 'I' ;
        $tab_users_fichier['nom'         ][] = Clean::nom($nom);
        $tab_users_fichier['prenom'      ][] = Clean::prenom($prenom);
        $tab_users_fichier['birth_date'  ][] = Clean::texte($birth_date);
        $tab_users_fichier['courriel'    ][] = Clean::courriel($courriel);
        // classe
        $classe_ref = mb_substr(Clean::ref($classe),0,8);
        $i_classe   = 'i'.Clean::login($classe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
        $tab_users_fichier['classe'][]     = $i_classe;
        if( ($classe_ref) && (!isset($tab_classes_fichier['ref'][$i_classe])) )
        {
          $tab_classes_fichier['ref'   ][$i_classe] = $classe_ref;
          $tab_classes_fichier['nom'   ][$i_classe] = $classe_ref;
          $tab_classes_fichier['niveau'][$i_classe] = $classe_ref;
        }
        // groupes
        $tab_user_groupes = array();
        if(strlen($groupes))
        {
          $tab_groupes = explode('|',$groupes);
          foreach ($tab_groupes as $groupe)
          {
            $groupe_ref = mb_substr(Clean::ref($groupe),0,8);
            $i_groupe   = 'i'.Clean::login($groupe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
            if( ($groupe_ref) && (!isset($tab_groupes_fichier['ref'][$i_groupe])) )
            {
              $tab_groupes_fichier['ref'   ][$i_groupe] = $groupe_ref;
              $tab_groupes_fichier['nom'   ][$i_groupe] = $groupe_ref;
              $tab_groupes_fichier['niveau'][$i_groupe] = $groupe_ref;
            }
            if(!isset($tab_user_groupes[$i_groupe]))
            {
              $tab_user_groupes[$i_groupe] = $groupe_ref;
            }
          }
        }
        $tab_users_fichier['groupe'][] = $tab_user_groupes;
      }
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction tableur_parents
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='tableur') && ($import_profil=='parent') )
{
  $contenu = file_get_contents(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  $contenu = To::deleteBOM(To::utf8($contenu)); // Mettre en UTF-8 si besoin et retirer le BOM éventuel
  $tab_lignes = extraire_lignes($contenu); // Extraire les lignes du fichier
  $separateur = extraire_separateur_csv($tab_lignes[0]); // Déterminer la nature du séparateur
  unset($tab_lignes[0]); // Supprimer la 1e ligne
  // L'import ne contient aucun id parent ni enfant.
  // On récupère la liste des références des élèves actuels pour comparer au contenu du fichier.
  $tab_eleves_actuels  = array();
  $tab_responsabilites = array();
  $DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_users( 'eleve' /*profil_type*/ , 1 /*only_actuels*/ , 'user_id,user_reference' /*liste_champs*/ , FALSE /*with_classe*/ , FALSE /*tri_statut*/ );
  foreach($DB_TAB as $DB_ROW)
  {
    $tab_eleves_actuels[ $DB_ROW['user_id']] = $DB_ROW['user_reference'];
    $tab_responsabilites[$DB_ROW['user_id']] = 0;
  }
  //
  // On passe les utilisateurs en revue : on mémorise leurs infos et les classes trouvées
  //
  $tab_genre = array(
    0     =>'I' ,
    ''    =>'I' ,
    1     =>'M' ,
    'M'   =>'M' ,
    'M.'  =>'M' ,
    'G'   =>'M' ,
    2     =>'F' ,
    'MME' =>'F' ,
    'F'   =>'F' ,
  );
  $tab_adresses_uniques = array();
  foreach ($tab_lignes as $ligne_contenu)
  {
    $tab_elements = str_getcsv($ligne_contenu,$separateur);
    $tab_elements = array_slice($tab_elements,0,21);
    if(count($tab_elements)>=13)
    {
      list(
        $reference ,
        $genre ,
        $nom ,
        $prenom ,
        $courriel ,
        $adresse_ligne1 ,
        $adresse_ligne2 ,
        $adresse_ligne3 ,
        $adresse_ligne4 ,
        $codepostal ,
        $commune ,
        $pays ,
        $enfant1 ,
        $enfant2 ,
        $enfant3 ,
        $enfant4 ,
        $enfant5 ,
        $enfant6 ,
        $enfant7 ,
        $enfant8 ,
        $enfant9
      ) = $tab_elements + array_fill(0,21,NULL); // Evite des NOTICE en initialisant les valeurs manquantes
      if( ($nom!='') && ($prenom!='') && ($enfant1!='') )
      {
        // enfants
        $tab_enfants = array();
        for( $num_enfant=1 ; $num_enfant<10 ; $num_enfant++ )
        {
          $enfant_ref = Clean::ref(${'enfant'.$num_enfant});
          if(!$enfant_ref) break;
          $enfant_id = array_search( $enfant_ref , $tab_eleves_actuels );
          if($enfant_id)
          {
            $tab_responsabilites[$enfant_id]++;
            $tab_enfants[$enfant_id] = $tab_responsabilites[$enfant_id];
          }
        }
        //
        // Si pas d'enfant trouvé, on laisse tomber, comme pour Sconet.
        //
        if( count($tab_enfants) )
        {
          $tab_users_fichier['sconet_id'   ][] = 0;
          $tab_users_fichier['sconet_num'  ][] = 0;
          $tab_users_fichier['reference'   ][] = Clean::ref($reference);
          $tab_users_fichier['profil_sigle'][] = 'TUT';
          $tab_users_fichier['genre'       ][] = isset($tab_genre[$genre]) ? $tab_genre[$genre] : 'I' ;
          $tab_users_fichier['nom'         ][] = Clean::nom($nom);
          $tab_users_fichier['prenom'      ][] = Clean::prenom($prenom);
          $tab_users_fichier['courriel'    ][] = Clean::courriel($courriel);
          $tab_users_fichier['adresse'     ][] = array( Clean::adresse($adresse_ligne1) , Clean::adresse($adresse_ligne2) , Clean::adresse($adresse_ligne3) , Clean::adresse($adresse_ligne4) , Clean::codepostal($codepostal) , Clean::commune($commune) , Clean::pays($pays) ) ;
          $tab_users_fichier['enfant'      ][] = $tab_enfants;
          $tab_adresses_uniques[$adresse_ligne1.'#'.$adresse_ligne2.'#'.$adresse_ligne3.'#'.$adresse_ligne4.'#'.$codepostal.'#'.$commune.'#'.$pays] = TRUE;
        }
      }
    }
  }
  $nb_lien_responsabilite = array_sum($tab_responsabilites);
  $nb_adresses = count($tab_adresses_uniques);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction base_eleves_eleves
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='base_eleves') && ($import_profil=='eleve') )
{
  $contenu = file_get_contents(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  $contenu = To::deleteBOM(To::utf8($contenu)); // Mettre en UTF-8 si besoin et retirer le BOM éventuel
  $tab_lignes = extraire_lignes($contenu); // Extraire les lignes du fichier
  $separateur = extraire_separateur_csv($tab_lignes[0]); // Déterminer la nature du séparateur
  // Utiliser la 1e ligne pour repérer les colonnes intéressantes
  $tab_numero_colonne = array(
    'nom'        => $init_negatif ,
    'prenom'     => $init_negatif ,
    'birth_date' => $init_negatif ,
    'genre'      => $init_negatif ,
    'niveau'     => $init_negatif ,
    'classe'     => $init_negatif ,
  );
  $tab_elements = str_getcsv($tab_lignes[0],$separateur);
  $numero_max = 0;
  foreach ($tab_elements as $numero=>$element)
  {
    switch($element)
    {
      case 'Nom Elève'      : $tab_numero_colonne['nom'   ]     = $numero; $numero_max = max($numero_max,$numero); break; // normalement 0
      case 'Prénom Elève'   : $tab_numero_colonne['prenom']     = $numero; $numero_max = max($numero_max,$numero); break; // normalement 2
      case 'Date naissance' : $tab_numero_colonne['birth_date'] = $numero; $numero_max = max($numero_max,$numero); break; // normalement 3
      case 'Sexe'           : $tab_numero_colonne['genre']      = $numero; $numero_max = max($numero_max,$numero); break; // normalement 4
      case 'Niveau'         : $tab_numero_colonne['niveau']     = $numero; $numero_max = max($numero_max,$numero); break; // normalement 14
      case 'Classe'         : $tab_numero_colonne['classe']     = $numero; $numero_max = max($numero_max,$numero); break; // normalement 15
    }
  }
  if(array_sum($tab_numero_colonne)<0)
  {
    exit('Erreur : un ou plusieurs champs n\'ont pas pu être repérés ("'.implode(' ";" ',array_keys(array_filter($tab_numero_colonne,'filter_init_negatif'))).'") !');
  }
  unset($tab_lignes[0]); // Supprimer la 1e ligne
  /*
   * Des difficultés se posent.
   * D'une part, les noms des niveaux et des classes ne semblent pas soumis à un format particulier ; on peut facilement dépasser les 20 caractères maxi autorisés par SACoche
   * D'autre part il n'existe pas de référence courte pour une classe.
   * Enfin, des classes sont sur plusieurs niveaux, donc comportent plusieurs groupes !
   */
  $tab_bon = array(); $tab_bad = array();
  $tab_bon[] = 'T';   $tab_bad[] = array('Toute ','toute ','TOUTE ');
  $tab_bon[] = 'P';   $tab_bad[] = array('Petite ','petite ','PETITE ');
  $tab_bon[] = 'M';   $tab_bad[] = array('Moyenne ','moyenne ','MOYENNE ');
  $tab_bon[] = 'G';   $tab_bad[] = array('Grande ','grande ','GRANDE ');
  $tab_bon[] = 'S';   $tab_bad[] = array('Section ','section ','SECTION ');
  $tab_bon[] = 'C';   $tab_bad[] = array('Cours ','cours ','COURS ');
  $tab_bon[] = 'P';   $tab_bad[] = array('Préparatoire','préparatoire','PRÉPARATOIRE','Preparatoire','preparatoire','PREPARATOIRE');
  $tab_bon[] = 'E';   $tab_bad[] = array('Élémentaire ','élémentaire ','ÉLÉMENTAIRE ','Elementaire ','elementaire ','ELEMENTAIRE ','Elémentaire ','elémentaire ','ELÉMENTAIRE ');
  $tab_bon[] = 'M';   $tab_bad[] = array('Moyen ','moyen ','MOYEN ');
  $tab_bon[] = '1';   $tab_bad[] = array('1er ','1ER ','1ere ','1ERE ','1ère ','1ÈRE ','premier ','PREMIER ','première ','PREMIÈRE ','premiere ','PREMIERE ');
  $tab_bon[] = '2';   $tab_bad[] = array('2e ','2E ','2eme ','2EME ','2ème ','2ÈME ','deuxième ','DEUXIÈME ','deuxieme ','DEUXIEME ','seconde ','SECONDE ');
  $tab_bon[] = '-';   $tab_bad[] = '- ';
  $tab_bon[] = '';    $tab_bad[] = array('Classe ','classe ','CLASSE ');
  $tab_bon[] = '';    $tab_bad[] = array('De ','de ','DE ');
  $tab_bon[] = '';    $tab_bad[] = array('Maternelle','maternelle','MATERNELLE');
  $tab_bon[] = '';    $tab_bad[] = array('Année','année','ANNÉE','Annee','annee','ANNEE');
  //
  // On passe les utilisateurs en revue : on mémorise leurs infos, les classes trouvées, les groupes trouvés
  //
  $tab_genre = array( ''=>'I' , 'M'=>'M' , 'F'=>'F' );
  foreach ($tab_lignes as $ligne_contenu)
  {
    $tab_elements = str_getcsv($ligne_contenu,$separateur);
    if(count($tab_elements)>$numero_max)
    {
      $nom        = $tab_elements[$tab_numero_colonne['nom']   ];
      $prenom     = $tab_elements[$tab_numero_colonne['prenom']];
      $genre      = isset($tab_genre[$tab_elements[$tab_numero_colonne['genre']]]) ? $tab_genre[$tab_elements[$tab_numero_colonne['genre']]] : 'I' ;
      $birth_date = strpos($tab_elements[$tab_numero_colonne['birth_date']],'-') ? convert_date_mysql_to_french($tab_elements[$tab_numero_colonne['birth_date']]) : $tab_elements[$tab_numero_colonne['birth_date']] ; // Selon les fichiers, trouvé au format français ou mysql
      $niveau     = $tab_elements[$tab_numero_colonne['niveau']];
      $classe     = $tab_elements[$tab_numero_colonne['classe']];
      if( ($nom!='') && ($prenom!='') && ($niveau!='') && ($classe!='') )
      {
        // Réduire la longueur du niveau et de la classe
        foreach ($tab_bon as $i=>$bon)
        {
          $niveau = str_replace($tab_bad[$i],$bon,$niveau);
          $classe = str_replace($tab_bad[$i],$bon,$classe);
        }
        $niveau_ref = mb_substr(Clean::ref($niveau),0,8);
        $classe_nom = mb_substr('['.$niveau_ref.'] '.$classe,0,20); // On fait autant de classes que de groupes de niveaux par classes.
        $classe_ref = mb_substr(Clean::ref($niveau_ref.'_'.md5($niveau_ref.$classe)),0,8);
        $i_classe   = 'i'.Clean::login($classe_ref); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
        $tab_users_fichier['sconet_id'   ][] = 0;
        $tab_users_fichier['sconet_num'  ][] = 0;
        $tab_users_fichier['reference'   ][] = '';
        $tab_users_fichier['profil_sigle'][] = 'ELV';
        $tab_users_fichier['genre'       ][] = $genre;
        $tab_users_fichier['nom'         ][] = Clean::nom($nom);
        $tab_users_fichier['prenom'      ][] = Clean::prenom($prenom);
        $tab_users_fichier['birth_date'  ][] = Clean::texte($birth_date);
        $tab_users_fichier['courriel'    ][] = '';
        $tab_users_fichier['classe'      ][] = $i_classe;
        if( ($classe_ref) && (!isset($tab_classes_fichier['ref'][$i_classe])) )
        {
          $tab_classes_fichier['ref'   ][$i_classe] = $classe_ref;
          $tab_classes_fichier['nom'   ][$i_classe] = $classe_nom;
          $tab_classes_fichier['niveau'][$i_classe] = $niveau_ref;
        }
      }
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction base_eleves_parents
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='base_eleves') && ($import_profil=='parent') )
{
  $contenu = file_get_contents(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  $contenu = To::deleteBOM(To::utf8($contenu)); // Mettre en UTF-8 si besoin et retirer le BOM éventuel
  $tab_lignes = extraire_lignes($contenu); // Extraire les lignes du fichier
  $separateur = extraire_separateur_csv($tab_lignes[0]); // Déterminer la nature du séparateur
  // Utiliser la 1e ligne pour repérer les colonnes intéressantes
  $tab_numero_colonne = array(
    'genre'         => $init_negatif ,
    'nom'           => $init_negatif ,
    'prenom'        => $init_negatif ,
    'adresse'       => $init_negatif ,
    'codepostal'    => $init_negatif ,
    'commune'       => $init_negatif ,
    'pays'          => $init_negatif ,
    'courriel'      => $init_negatif ,
    'enfant_nom'    => array() ,
    'enfant_prenom' => array() ,
  );
  $tab_elements = str_getcsv($tab_lignes[0],$separateur);
  $numero_max = 0;
  foreach ($tab_elements as $numero=>$element)
  {
    switch($element)
    {
      case 'Civilité Responsable'  : $tab_numero_colonne['genre']           = $numero; $numero_max = max($numero_max,$numero); break; // normalement 0
      case 'Nom responsable'       : $tab_numero_colonne['nom']             = $numero; $numero_max = max($numero_max,$numero); break; // normalement 2
      case 'Prénom responsable'    : $tab_numero_colonne['prenom']          = $numero; $numero_max = max($numero_max,$numero); break; // normalement 3
      case 'Adresse responsable'   : $tab_numero_colonne['adresse']         = $numero; $numero_max = max($numero_max,$numero); break; // normalement 4
      case 'CP responsable'        : $tab_numero_colonne['codepostal']      = $numero; $numero_max = max($numero_max,$numero); break; // normalement 5
      case 'Commune responsable'   : $tab_numero_colonne['commune']         = $numero; $numero_max = max($numero_max,$numero); break; // normalement 6
      case 'Pays'                  : $tab_numero_colonne['pays']            = $numero; $numero_max = max($numero_max,$numero); break; // normalement 7
      case 'Courriel'              : $tab_numero_colonne['courriel']        = $numero; $numero_max = max($numero_max,$numero); break; // normalement 8
      case 'Nom de famille enfant' : $tab_numero_colonne['enfant_nom'][]    = $numero;                                         break; // normalement 14 18 22 ...
      case 'Prénom enfant'         : $tab_numero_colonne['enfant_prenom'][] = $numero;                                         break; // normalement 15 19 23 ...
    }
  }
  $nb_enfants_maxi = min( count($tab_numero_colonne['enfant_nom']) , count($tab_numero_colonne['enfant_prenom']) );
  if( (array_sum($tab_numero_colonne)<0) || ($nb_enfants_maxi==0) )
  {
    exit('Erreur : un ou plusieurs champs n\'ont pas pu être repérés ("'.implode(' ";" ',array_keys(array_filter($tab_numero_colonne,'filter_init_negatif'))).'") !');
  }
  $numero_max = max( $numero_max , $tab_numero_colonne['enfant_nom'][0] , $tab_numero_colonne['enfant_prenom'][0] );
  unset($tab_lignes[0]); // Supprimer la 1e ligne
  // L'import ne contient aucun id parent ni enfant.
  // On récupère la liste des noms prénoms des élèves actuels pour comparer au contenu du fichier.
  $tab_eleves_actuels  = array();
  $tab_responsabilites = array();
  $DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_users( 'eleve' /*profil_type*/ , 1 /*only_actuels*/ , 'user_id,user_nom,user_prenom' /*liste_champs*/ , FALSE /*with_classe*/ , FALSE /*tri_statut*/ );
  foreach($DB_TAB as $DB_ROW)
  {
    $tab_eleves_actuels[$DB_ROW['user_id']] = $DB_ROW['user_nom'].' '.$DB_ROW['user_prenom'];
    $tab_responsabilites[$DB_ROW['user_id']] = 0;
  }
  //
  // On passe les utilisateurs en revue : on mémorise leurs infos, les adresses trouvées, les enfants trouvés
  //
  $tab_genre = array( ''=>'I' , 'M.'=>'M' , 'MME'=>'F' );
  $tab_adresses_uniques = array();
  foreach ($tab_lignes as $ligne_contenu)
  {
    $tab_elements = str_getcsv($ligne_contenu,$separateur);
    if(count($tab_elements)>$numero_max)
    {
      $genre      = isset($tab_genre[$tab_elements[$tab_numero_colonne['genre']]]) ? $tab_genre[$tab_elements[$tab_numero_colonne['genre']]] : 'I' ;
      $nom        = Clean::nom(       $tab_elements[$tab_numero_colonne['nom']       ]);
      $prenom     = Clean::prenom(    $tab_elements[$tab_numero_colonne['prenom']    ]);
      $courriel   = Clean::courriel(  $tab_elements[$tab_numero_colonne['courriel']  ]);
      $adresse    = Clean::adresse(   $tab_elements[$tab_numero_colonne['adresse']   ]);
      $codepostal = Clean::codepostal($tab_elements[$tab_numero_colonne['codepostal']]);
      $commune    = Clean::commune(   $tab_elements[$tab_numero_colonne['commune']   ]);
      $pays       = Clean::pays(      $tab_elements[$tab_numero_colonne['pays']      ]);
      if( ($nom!='') && ($prenom!='') )
      {
        $tab_enfants = array();
        for( $num_enfant=0 ; $num_enfant<$nb_enfants_maxi ; $num_enfant++ )
        {
          if ( !isset($tab_elements[$tab_numero_colonne['enfant_nom'][$num_enfant]]) || !isset($tab_elements[$tab_numero_colonne['enfant_prenom'][$num_enfant]]) )
          {
            break;
          }
          $enfant_nom    = Clean::nom(   $tab_elements[$tab_numero_colonne['enfant_nom'   ][$num_enfant]]);
          $enfant_prenom = Clean::prenom($tab_elements[$tab_numero_colonne['enfant_prenom'][$num_enfant]]);
          $enfant_id     = array_search( $enfant_nom.' '.$enfant_prenom , $tab_eleves_actuels );
          if($enfant_id)
          {
            $tab_responsabilites[$enfant_id]++;
            $tab_enfants[$enfant_id] = $tab_responsabilites[$enfant_id];
          }
        }
        //
        // Si pas d'enfant trouvé, on laisse tomber, comme pour Sconet.
        //
        if( count($tab_enfants) )
        {
          $tab_users_fichier['sconet_id'   ][] = 0;
          $tab_users_fichier['sconet_num'  ][] = 0;
          $tab_users_fichier['reference'   ][] = '';
          $tab_users_fichier['profil_sigle'][] = 'TUT';
          $tab_users_fichier['genre'       ][] = $genre;
          $tab_users_fichier['nom'         ][] = $nom;
          $tab_users_fichier['prenom'      ][] = $prenom;
          $tab_users_fichier['courriel'    ][] = $courriel;
          $tab_users_fichier['adresse'     ][] = array( $adresse , '' , '' , '' , $codepostal , $commune , $pays );
          $tab_users_fichier['enfant'      ][] = $tab_enfants;
          $tab_adresses_uniques[$adresse.'#'.$codepostal.'#'.$commune.'#'.$pays] = TRUE;
        }
      }
    }
  }
  $nb_lien_responsabilite = array_sum($tab_responsabilites);
  $nb_adresses = count($tab_adresses_uniques);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction factos_eleves
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='factos') && ($import_profil=='eleve') )
{
  $contenu = file_get_contents(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  $contenu = To::deleteBOM(To::utf8($contenu)); // Mettre en UTF-8 si besoin et retirer le BOM éventuel
  $tab_lignes = extraire_lignes($contenu); // Extraire les lignes du fichier
  $separateur = extraire_separateur_csv($tab_lignes[0]); // Déterminer la nature du séparateur
  // Utiliser la 1e ligne pour repérer les colonnes intéressantes
  $tab_numero_colonne = array(
    'sconet_num' => $init_negatif ,
    'nom'        => $init_negatif ,
    'prenom'     => $init_negatif ,
    'genre'      => $init_negatif ,
    'birth_date' => $init_negatif ,
    'classe'     => $init_negatif ,
  );
  $tab_elements = str_getcsv($tab_lignes[0],$separateur);
  $numero_max = 0;
  foreach ($tab_elements as $numero=>$element)
  {
    switch($element)
    {
      case "Identifiant GEP"   : $tab_numero_colonne['sconet_num'] = $numero; $numero_max = max($numero_max,$numero); break;
      case "Nom de l'élève"    : $tab_numero_colonne['nom'   ]     = $numero; $numero_max = max($numero_max,$numero); break;
      case "Prénom élève"      : $tab_numero_colonne['prenom']     = $numero; $numero_max = max($numero_max,$numero); break;
      case "Sexe"              : $tab_numero_colonne['genre']      = $numero; $numero_max = max($numero_max,$numero); break;
      case "Date de naissance" : $tab_numero_colonne['birth_date'] = $numero; $numero_max = max($numero_max,$numero); break;
      case "Classe"            : $tab_numero_colonne['classe']     = $numero; $numero_max = max($numero_max,$numero); break;
    }
  }
  if(array_sum($tab_numero_colonne)<0)
  {
    exit('Erreur : un ou plusieurs champs n\'ont pas pu être repérés ("'.implode(' ";" ',array_keys(array_filter($tab_numero_colonne,'filter_init_negatif'))).'") !');
  }
  unset($tab_lignes[0]); // Supprimer la 1e ligne
  //
  // On passe les utilisateurs en revue : on mémorise leurs infos, les classes trouvées
  // Attention : en l'absence de donnée, un champ peut contenir la valeur "Non saisi"
  //
  $tab_genre = array( ''=>'I' , 'Non saisi'=>'I' , 'Masculin'=>'M' , 'Féminin'=>'F' );
  foreach ($tab_lignes as $ligne_contenu)
  {
    $tab_elements = str_getcsv($ligne_contenu,$separateur);
    if(count($tab_elements)>$numero_max)
    {
      $sconet_num = ($tab_elements[$tab_numero_colonne['sconet_num']]!="Non saisi") ? $tab_elements[$tab_numero_colonne['sconet_num']] : '' ;
      $nom        = ($tab_elements[$tab_numero_colonne['nom'       ]]!="Non saisi") ? $tab_elements[$tab_numero_colonne['nom'       ]] : '' ;
      $prenom     = ($tab_elements[$tab_numero_colonne['prenom'    ]]!="Non saisi") ? $tab_elements[$tab_numero_colonne['prenom'    ]] : '' ;
      $genre      = isset($tab_genre[$tab_elements[$tab_numero_colonne['genre']]]) ? $tab_genre[$tab_elements[$tab_numero_colonne['genre']]] : 'I' ;
      $birth_date = ($tab_elements[$tab_numero_colonne['birth_date']]!="Non saisi") ? $tab_elements[$tab_numero_colonne['birth_date']] : '' ;
      $classe     = ($tab_elements[$tab_numero_colonne['classe'    ]]!="Non saisi") ? $tab_elements[$tab_numero_colonne['classe'    ]] : '' ;
      if( ($nom!='') && ($prenom!='') && ($classe!='') )
      {
        $i_classe   = 'i'.Clean::login($classe); // 'i' car la référence peut être numérique (ex : 61) et cela pose problème que l'indice du tableau soit un entier (ajouter (string) n'y change rien) lors du array_multisort().
        $tab_users_fichier['sconet_id'   ][] = 0;
        $tab_users_fichier['sconet_num'  ][] = Clean::entier($sconet_num);
        $tab_users_fichier['reference'   ][] = '';
        $tab_users_fichier['profil_sigle'][] = 'ELV' ;
        $tab_users_fichier['genre'       ][] = $genre;
        $tab_users_fichier['nom'         ][] = Clean::nom($nom);
        $tab_users_fichier['prenom'      ][] = Clean::prenom($prenom);
        $tab_users_fichier['birth_date'  ][] = Clean::texte($birth_date);
        $tab_users_fichier['courriel'    ][] = '';
        $tab_users_fichier['classe'      ][] = $i_classe;
        if( !isset($tab_classes_fichier['ref'][$i_classe]))
        {
          $tab_classes_fichier['ref'   ][$i_classe] = mb_substr(Clean::ref($classe),0,8);
          $tab_classes_fichier['nom'   ][$i_classe] = mb_substr(Clean::texte($classe),0,20);
          $tab_classes_fichier['niveau'][$i_classe] = '';
        }
      }
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Extraction factos_parents
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if( ($import_origine=='factos') && ($import_profil=='parent') )
{
  $contenu = file_get_contents(CHEMIN_DOSSIER_IMPORT.$fichier_dest);
  $contenu = To::deleteBOM(To::utf8($contenu)); // Mettre en UTF-8 si besoin et retirer le BOM éventuel
  $tab_lignes = extraire_lignes($contenu); // Extraire les lignes du fichier
  $separateur = extraire_separateur_csv($tab_lignes[0]); // Déterminer la nature du séparateur
  // Utiliser la 1e ligne pour repérer les colonnes intéressantes
  $tab_numero_colonne = array(
    'sconet_num_1'     => $init_negatif ,
    'sconet_num_2'     => $init_negatif ,
    'genre_1'          => $init_negatif ,
    'genre_2'          => $init_negatif ,
    'nom_1'            => $init_negatif ,
    'nom_2'            => $init_negatif ,
    'prenom_1'         => $init_negatif ,
    'prenom_2'         => $init_negatif ,
    'courriel_1'       => $init_negatif ,
    'courriel_2'       => $init_negatif ,
    'adresse_ligne1_1' => $init_negatif ,
    'adresse_ligne1_2' => $init_negatif ,
    'adresse_ligne2_1' => $init_negatif ,
    'adresse_ligne2_2' => $init_negatif ,
    'adresse_ligne3_1' => $init_negatif ,
    'adresse_ligne3_2' => $init_negatif ,
    'code_postal_1'    => $init_negatif ,
    'code_postal_2'    => $init_negatif ,
    'commune_1'        => $init_negatif ,
    'commune_2'        => $init_negatif ,
    'pays_1'           => $init_negatif ,
    'pays_2'           => $init_negatif ,
    'enfant_sconet'    => $init_negatif ,
    'enfant_nom'       => $init_negatif ,
    'enfant_prenom'    => $init_negatif ,
  );
  $tab_elements = str_getcsv($tab_lignes[0],$separateur);
  $numero_max = 0;
  // 1) Les noms des champs manquent d'homogénéité ("Code postal du responsable 1" vs "CpVille Resp2" etc) ; cela fait très amateur...
  // 2) Pour le responsable 1 le champ "Ville du responsable 1" ne contient que la ville comme attendu (par exemple "LONDON"),
  //     mais pour le responsable 2 le champ "Ville Resp2" contient code postal + ville (par exemple "W7 1JQ LONDON").
  //     Pour avoir la ville du resp 2 il faut donc faire "la différence" entre le champ "Ville Resp2" avec "CpVille Resp2"...
  foreach ($tab_elements as $numero=>$element)
  {
    switch($element)
    {
      // parent 1
      case "Identifiant GEP du responsable 1"       : $tab_numero_colonne['sconet_num_1']     = $numero; $numero_max = max($numero_max,$numero); break;
      case "Civilité du responsable 1"              : $tab_numero_colonne['genre_1']          = $numero; $numero_max = max($numero_max,$numero); break;
      case "Nom du responsable 1"                   : $tab_numero_colonne['nom_1']            = $numero; $numero_max = max($numero_max,$numero); break;
      case "Prénom du responsable 1"                : $tab_numero_colonne['prenom_1']         = $numero; $numero_max = max($numero_max,$numero); break;
      case "Email Resp1"                            : $tab_numero_colonne['courriel_1']       = $numero; $numero_max = max($numero_max,$numero); break;
      case "Adresse1 du responsable 1"              : $tab_numero_colonne['adresse_ligne1_1'] = $numero; $numero_max = max($numero_max,$numero); break;
      case "Adresse2 du responsable 1"              : $tab_numero_colonne['adresse_ligne2_1'] = $numero; $numero_max = max($numero_max,$numero); break;
      case "Adresse3 du responsable 1"              : $tab_numero_colonne['adresse_ligne3_1'] = $numero; $numero_max = max($numero_max,$numero); break;
      case "Code postal du responsable 1"           : $tab_numero_colonne['code_postal_1']    = $numero; $numero_max = max($numero_max,$numero); break;
      case "Ville du responsable 1"                 : $tab_numero_colonne['commune_1']        = $numero; $numero_max = max($numero_max,$numero); break;
      case "Pays de domiciliation du responsable 1" : $tab_numero_colonne['pays_1']           = $numero; $numero_max = max($numero_max,$numero); break; // ?????
      // parent 2
      case "Identifiant GEP R2"                     : $tab_numero_colonne['sconet_num_2']     = $numero; $numero_max = max($numero_max,$numero); break;
      case "Civilité du resp2"                      : $tab_numero_colonne['genre_2']          = $numero; $numero_max = max($numero_max,$numero); break;
      case "Nom du responsable 2"                   : $tab_numero_colonne['nom_2']            = $numero; $numero_max = max($numero_max,$numero); break;
      case "Prénom du Responsable 2"                : $tab_numero_colonne['prenom_2']         = $numero; $numero_max = max($numero_max,$numero); break;
      case "EMail Resp2"                            : $tab_numero_colonne['courriel_2']       = $numero; $numero_max = max($numero_max,$numero); break;
      case "Adresse1 Resp2"                         : $tab_numero_colonne['adresse_ligne1_2'] = $numero; $numero_max = max($numero_max,$numero); break;
      case "Adresse2 Resp2"                         : $tab_numero_colonne['adresse_ligne2_2'] = $numero; $numero_max = max($numero_max,$numero); break;
      case "Adresse3 Resp2"                         : $tab_numero_colonne['adresse_ligne3_2'] = $numero; $numero_max = max($numero_max,$numero); break;
      case "CpVille Resp2"                          : $tab_numero_colonne['code_postal_2']    = $numero; $numero_max = max($numero_max,$numero); break;
      case "Ville Resp2"                            : $tab_numero_colonne['commune_2']        = $numero; $numero_max = max($numero_max,$numero); break;
      case "Pays Resp2"                             : $tab_numero_colonne['pays_2']           = $numero; $numero_max = max($numero_max,$numero); break;
      // enfant
      case "Identifiant GEP"                        : $tab_numero_colonne['enfant_sconet']    = $numero; $numero_max = max($numero_max,$numero); break;
      case "Nom de l'élève"                         : $tab_numero_colonne['enfant_nom'   ]    = $numero; $numero_max = max($numero_max,$numero); break;
      case "Prénom élève"                           : $tab_numero_colonne['enfant_prenom']    = $numero; $numero_max = max($numero_max,$numero); break;
    }
  }
  if(array_sum($tab_numero_colonne)<0)
  {
    exit('Erreur : un ou plusieurs champs n\'ont pas pu être repérés ("'.implode(' ";" ',array_keys(array_filter($tab_numero_colonne,'filter_init_negatif'))).'") !');
  }
  unset($tab_lignes[0]); // Supprimer la 1e ligne
  // On récupère les élèves pour vérifier que ceux trouvé dans le fichier des parents sont bien dasn la base.
  $tab_eleves_actuels  = array();
  $DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_users( 'eleve' /*profil_type*/ , 1 /*only_actuels*/ , 'user_id,user_sconet_elenoet' /*liste_champs*/ , FALSE /*with_classe*/ , FALSE /*tri_statut*/ );
  foreach($DB_TAB as $DB_ROW)
  {
    $tab_eleves_actuels[ $DB_ROW['user_sconet_elenoet']] = $DB_ROW['user_id'];
  }
  //
  // On passe les utilisateurs en revue : on mémorise leurs infos, les adresses trouvées, les enfants trouvés
  //
  $tab_genre = array( ''=>'I' , 'Non saisi'=>'I' , 'M.'=>'M' , 'M'=>'M' , 'MME'=>'F' , 'Mme'=>'F' , 'MLLE'=>'F' , 'Mlle'=>'F' );
  $tab_adresses_uniques = array();
  $nb_lien_responsabilite = 0;
  foreach ($tab_lignes as $ligne_contenu)
  {
    $tab_elements = str_getcsv($ligne_contenu,$separateur);
    if(count($tab_elements)>$numero_max)
    {
      $sconet_num_eleve = ( $tab_elements[$tab_numero_colonne['enfant_sconet']] && ($tab_elements[$tab_numero_colonne['enfant_sconet']]!='Non saisi') ) ? Clean::entier($tab_elements[$tab_numero_colonne['enfant_sconet']]) : NULL ;
      if( $sconet_num_eleve && isset($tab_eleves_actuels[$sconet_num_eleve]) )
      {
        $sconet_num_resp1 = ( $tab_elements[$tab_numero_colonne['sconet_num_1']] && ($tab_elements[$tab_numero_colonne['sconet_num_1']]!='Non saisi') ) ? Clean::entier($tab_elements[$tab_numero_colonne['sconet_num_1']]) : NULL ;
        if($sconet_num_resp1)
        {
          $tab_users_fichier['sconet_id'   ][$sconet_num_resp1] = 0;
          $tab_users_fichier['sconet_num'  ][$sconet_num_resp1] = $sconet_num_resp1;
          $tab_users_fichier['reference'   ][$sconet_num_resp1] = '';
          $tab_users_fichier['profil_sigle'][$sconet_num_resp1] = 'TUT' ;
          $tab_users_fichier['genre'       ][$sconet_num_resp1] = isset($tab_genre[$tab_elements[$tab_numero_colonne['genre_1']]]) ? $tab_genre[$tab_elements[$tab_numero_colonne['genre_1']]] : 'I' ;
          $tab_users_fichier['nom'         ][$sconet_num_resp1] = Clean::nom($tab_elements[$tab_numero_colonne['nom_1']]);
          $tab_users_fichier['prenom'      ][$sconet_num_resp1] = Clean::prenom($tab_elements[$tab_numero_colonne['prenom_1']]);
          $tab_users_fichier['courriel'    ][$sconet_num_resp1] = Clean::courriel($tab_elements[$tab_numero_colonne['courriel_1']]);
          $tab_users_fichier['enfant'      ][$sconet_num_resp1][$tab_eleves_actuels[$sconet_num_eleve]] = 1;
          $tab_users_fichier['adresse'     ][$sconet_num_resp1] = array(
            Clean::adresse($tab_elements[$tab_numero_colonne['adresse_ligne1_1']]) ,
            Clean::adresse($tab_elements[$tab_numero_colonne['adresse_ligne2_1']]) ,
            Clean::adresse($tab_elements[$tab_numero_colonne['adresse_ligne3_1']]) ,
            '' ,
            Clean::commune($tab_elements[$tab_numero_colonne['code_postal_1']]) , // pas un nombre entier à l'étranger
            Clean::commune($tab_elements[$tab_numero_colonne['commune_1']]) ,
            Clean::pays($tab_elements[$tab_numero_colonne['pays_1']]) ,
          );
          $nb_lien_responsabilite++;
          $tab_adresses_uniques[ implode('#',$tab_users_fichier['adresse'][$sconet_num_resp1]) ] = TRUE;
        }
        $sconet_num_resp2 = ( $tab_elements[$tab_numero_colonne['sconet_num_2']] && ($tab_elements[$tab_numero_colonne['sconet_num_2']]!='Non saisi') ) ? Clean::entier($tab_elements[$tab_numero_colonne['sconet_num_2']]) : NULL ;
        if($sconet_num_resp2)
        {
          $tab_users_fichier['sconet_id'   ][$sconet_num_resp2] = 0;
          $tab_users_fichier['sconet_num'  ][$sconet_num_resp2] = $sconet_num_resp2;
          $tab_users_fichier['reference'   ][$sconet_num_resp2] = '';
          $tab_users_fichier['profil_sigle'][$sconet_num_resp2] = 'TUT' ;
          $tab_users_fichier['genre'       ][$sconet_num_resp2] = isset($tab_genre[$tab_elements[$tab_numero_colonne['genre_2']]]) ? $tab_genre[$tab_elements[$tab_numero_colonne['genre_2']]] : 'I' ;
          $tab_users_fichier['nom'         ][$sconet_num_resp2] = Clean::nom($tab_elements[$tab_numero_colonne['nom_2']]);
          $tab_users_fichier['prenom'      ][$sconet_num_resp2] = Clean::prenom($tab_elements[$tab_numero_colonne['prenom_2']]);
          $tab_users_fichier['courriel'    ][$sconet_num_resp2] = Clean::courriel($tab_elements[$tab_numero_colonne['courriel_2']]);
          $tab_users_fichier['enfant'      ][$sconet_num_resp2][$tab_eleves_actuels[$sconet_num_eleve]] = 2;
          $tab_users_fichier['adresse'     ][$sconet_num_resp2] = array(
            Clean::adresse($tab_elements[$tab_numero_colonne['adresse_ligne1_2']]) ,
            Clean::adresse($tab_elements[$tab_numero_colonne['adresse_ligne2_2']]) ,
            Clean::adresse($tab_elements[$tab_numero_colonne['adresse_ligne3_2']]) ,
            '' ,
            Clean::commune($tab_elements[$tab_numero_colonne['code_postal_2']]) , // pas un nombre entier à  l'étranger
            Clean::commune($tab_elements[$tab_numero_colonne['commune_2']]) ,
            Clean::pays($tab_elements[$tab_numero_colonne['pays_2']]) ,
          );
          $nb_lien_responsabilite++;
          $tab_adresses_uniques[ implode('#',$tab_users_fichier['adresse'][$sconet_num_resp2]) ] = TRUE;
          // Correctif pour obtenir la vraie valeur de "commune_2"
          $tab_users_fichier['adresse'][$sconet_num_resp2][5] = mb_substr( $tab_users_fichier['adresse'][$sconet_num_resp2][5] , mb_strlen($tab_users_fichier['adresse'][$sconet_num_resp2][4])+1 );
        }
      }
    }
  }
  $nb_adresses = count($tab_adresses_uniques);
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Fin des différents cas possibles
// ////////////////////////////////////////////////////////////////////////////////////////////////////

// Tableaux pour les étapes 61/62/71/72
$tab_i_classe_TO_id_base  = array();
$tab_i_groupe_TO_id_base  = array();
$tab_i_fichier_TO_id_base = array();
$tab_liens_id_base = array('classes'=>$tab_i_classe_TO_id_base,'groupes'=>$tab_i_groupe_TO_id_base,'users'=>$tab_i_fichier_TO_id_base);

// On trie
switch($import_origine.'+'.$import_profil)
{
  case 'sconet+professeur' :
    $test1 = array_multisort(
      $tab_users_fichier['nom']   , SORT_ASC,SORT_STRING,
      $tab_users_fichier['prenom'], SORT_ASC,SORT_STRING,
      $tab_users_fichier['genre'],
      $tab_users_fichier['courriel'],
      $tab_users_fichier['sconet_id'],
      $tab_users_fichier['sconet_num'],
      $tab_users_fichier['reference'],
      $tab_users_fichier['profil_sigle'],
      $tab_users_fichier['classe'],
      $tab_users_fichier['groupe'],
      $tab_users_fichier['matiere']
    );
    $test2 = array_multisort(
      $tab_classes_fichier['niveau'], SORT_DESC,SORT_STRING,
      $tab_classes_fichier['ref']   , SORT_ASC,SORT_STRING,
      $tab_classes_fichier['nom']   , SORT_ASC,SORT_STRING
    );
    $test3 = array_multisort(
      $tab_groupes_fichier['niveau'], SORT_DESC,SORT_STRING,
      $tab_groupes_fichier['ref']   , SORT_ASC,SORT_STRING,
      $tab_groupes_fichier['nom']   , SORT_ASC,SORT_STRING
    );
    break;
  case 'tableur+professeur' :
    $test1 = array_multisort(
      $tab_users_fichier['nom']   , SORT_ASC,SORT_STRING,
      $tab_users_fichier['prenom'], SORT_ASC,SORT_STRING,
      $tab_users_fichier['genre'],
      $tab_users_fichier['courriel'],
      $tab_users_fichier['sconet_id'],
      $tab_users_fichier['sconet_num'],
      $tab_users_fichier['reference'],
      $tab_users_fichier['profil_sigle'],
      $tab_users_fichier['classe'],
      $tab_users_fichier['groupe']
    );
    $test2 = array_multisort(
      $tab_classes_fichier['niveau'], SORT_DESC,SORT_STRING,
      $tab_classes_fichier['ref']   , SORT_ASC,SORT_STRING,
      $tab_classes_fichier['nom']   , SORT_ASC,SORT_STRING
    );
    $test3 = array_multisort(
      $tab_groupes_fichier['niveau'], SORT_DESC,SORT_STRING,
      $tab_groupes_fichier['ref']   , SORT_ASC,SORT_STRING,
      $tab_groupes_fichier['nom']   , SORT_ASC,SORT_STRING
    );
    break;
  case  'sconet+eleve' :
  case 'tableur+eleve' :
    $test1 = array_multisort(
      $tab_users_fichier['nom']   , SORT_ASC,SORT_STRING,
      $tab_users_fichier['prenom'], SORT_ASC,SORT_STRING,
      $tab_users_fichier['genre'],
      $tab_users_fichier['birth_date'],
      $tab_users_fichier['courriel'],
      $tab_users_fichier['sconet_id'],
      $tab_users_fichier['sconet_num'],
      $tab_users_fichier['reference'],
      $tab_users_fichier['profil_sigle'],
      $tab_users_fichier['classe'],
      $tab_users_fichier['groupe']
    );
    $test2 = array_multisort(
      $tab_classes_fichier['niveau'], SORT_DESC,SORT_STRING,
      $tab_classes_fichier['ref']   , SORT_ASC,SORT_STRING,
      $tab_classes_fichier['nom']   , SORT_ASC,SORT_STRING
    );
    $test3 = array_multisort(
      $tab_groupes_fichier['niveau'], SORT_DESC,SORT_STRING,
      $tab_groupes_fichier['ref']   , SORT_ASC,SORT_STRING,
      $tab_groupes_fichier['nom']   , SORT_ASC,SORT_STRING
    );
    break;
  case 'base_eleves+eleve' :
    $test2 = array_multisort(
      $tab_classes_fichier['niveau'], SORT_DESC,SORT_STRING,
      $tab_classes_fichier['ref']   , SORT_ASC,SORT_STRING,
      $tab_classes_fichier['nom']   , SORT_ASC,SORT_STRING
    );
  case      'factos+eleve' :
    $test1 = array_multisort(
      $tab_users_fichier['nom']   , SORT_ASC,SORT_STRING,
      $tab_users_fichier['prenom'], SORT_ASC,SORT_STRING,
      $tab_users_fichier['genre'],
      $tab_users_fichier['birth_date'],
      $tab_users_fichier['courriel'],
      $tab_users_fichier['sconet_id'],
      $tab_users_fichier['sconet_num'],
      $tab_users_fichier['reference'],
      $tab_users_fichier['profil_sigle'],
      $tab_users_fichier['classe']
    );
    break;
  case      'sconet+parent' :
  case 'base_eleves+parent' :
  case     'tableur+parent' :
  case      'factos+parent' :
    $test1 = array_multisort(
      $tab_users_fichier['nom']   , SORT_ASC,SORT_STRING,
      $tab_users_fichier['prenom'], SORT_ASC,SORT_STRING,
      $tab_users_fichier['genre'],
      $tab_users_fichier['courriel'],
      $tab_users_fichier['sconet_id'],
      $tab_users_fichier['sconet_num'],
      $tab_users_fichier['reference'],
      $tab_users_fichier['profil_sigle'],
      $tab_users_fichier['adresse'],
      $tab_users_fichier['enfant']
    );
    break;
}

// Outil de résolution de bug ; le test1 provoque parfois l'erreur "Array sizes are inconsistent".
// Edit au 11/05/2012 : a priori c'est corrigé, mais je laisse quand même le test au cas où, ça ne coûte rien...
if(!$test1)
{
  ajouter_log_PHP( 'Import fichier '.$import_origine.' '.$import_profil /*log_objet*/ , serialize($tab_users_fichier) /*log_contenu*/ , __FILE__ /*log_fichier*/ , __LINE__ /*log_ligne*/ , TRUE /*only_sesamath*/ );
}

// On enregistre
FileSystem::ecrire_fichier(CHEMIN_DOSSIER_IMPORT.'import_'.$import_origine.'_'.$import_profil.'_'.$_SESSION['BASE'].'_'.session_id().'_users.txt'        ,serialize($tab_users_fichier));
FileSystem::ecrire_fichier(CHEMIN_DOSSIER_IMPORT.'import_'.$import_origine.'_'.$import_profil.'_'.$_SESSION['BASE'].'_'.session_id().'_classes.txt'      ,serialize($tab_classes_fichier));
FileSystem::ecrire_fichier(CHEMIN_DOSSIER_IMPORT.'import_'.$import_origine.'_'.$import_profil.'_'.$_SESSION['BASE'].'_'.session_id().'_groupes.txt'      ,serialize($tab_groupes_fichier));
FileSystem::ecrire_fichier(CHEMIN_DOSSIER_IMPORT.'import_'.$import_origine.'_'.$import_profil.'_'.$_SESSION['BASE'].'_'.session_id().'_liens_id_base.txt',serialize($tab_liens_id_base));

// On affiche le bilan des utilisateurs trouvés
if(count($tab_users_fichier['profil_sigle']))
{
  // Nom des profils
  $tab_profils_libelles = array();
  $DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_profils_parametres( 'user_profil_nom_long_singulier,user_profil_nom_long_pluriel' /*listing_champs*/ , FALSE /*only_actif*/ );
  foreach($DB_TAB as $DB_ROW)
  {
    $tab_profils_libelles[$DB_ROW['user_profil_sigle']] = array( 1=>$DB_ROW['user_profil_nom_long_singulier'] , 2=>$DB_ROW['user_profil_nom_long_pluriel'] );
  }
  // Boucle pour l'affichage
  $tab_profil_nombre = array_count_values($tab_users_fichier['profil_sigle']);
  foreach ($tab_profil_nombre as $profil=>$nombre)
  {
    $s = ($nombre>1) ? 's' : '' ;
    echo'<p><label class="valide">'.$nombre.' '.$tab_profils_libelles[$profil][min(2,$nombre)].' trouvé'.$s.'.</label></p>'.NL;
  }
}
else if($import_profil=='parent')
{
  exit('<p><label class="alerte">Aucun parent trouvé ayant un enfant dans l\'établissement : importer d\'abord les élèves !</label></p>');
}
else
{
  exit('<p><label class="alerte">Aucun utilisateur trouvé !</label></p>');
}

// On affiche le bilan des classes trouvées
 if($import_profil!='parent')
{
  $nombre = count($tab_classes_fichier['ref']);
  if($nombre)
  {
    $s = ($nombre>1) ? 's' : '' ;
    echo'<p><label class="valide">'.$nombre.' classe'.$s.' trouvée'.$s.'.</label></p>'.NL;
  }
  else
  {
    echo'<p><label class="alerte">Aucune classe trouvée !</label></p>'.NL;
  }
}

// On affiche le bilan des groupes trouvés
if( ($import_profil!='parent') && ($import_origine!='base_eleves') && ($import_origine!='factos') )
{
  $nombre = count($tab_groupes_fichier['ref']);
  if($nombre)
  {
    $s = ($nombre>1) ? 's' : '' ;
    echo'<p><label class="valide">'.$nombre.' groupe'.$s.' trouvé'.$s.'.</label></p>'.NL;
  }
  else
  {
    echo'<p><label class="alerte">Aucun groupe trouvé !</label></p>'.NL;
  }
}

// On affiche le bilan des parents trouvés
if($import_profil=='parent')
{
  if($nb_adresses)
  {
    $s = ($nb_adresses>1) ? 's' : '' ;
    echo'<p><label class="valide">'.$nb_adresses.' adresse'.$s.' trouvée'.$s.'.</label></p>'.NL;
  }
  else
  {
    echo'<p><label class="alerte">Aucune adresse trouvée !</label></p>'.NL;
  }
  if($nb_lien_responsabilite)
  {
    $s = ($nb_lien_responsabilite>1) ? 's' : '' ;
    echo'<p><label class="valide">'.$nb_lien_responsabilite.' lien'.$s.' de responsabilité'.$s.' trouvé'.$s.'.</label></p>'.NL;
  }
  else
  {
    echo'<p><label class="alerte">Aucun lien de responsabilité trouvé !</label></p>'.NL;
  }
}

// Fin de l'extraction
$STEP = ($import_profil=='parent') ? '5' : '3' ;
echo'<ul class="puce p"><li><a href="#step'.$STEP.'1" id="passer_etape_suivante">Passer à l\'étape 3.</a><label id="ajax_msg">&nbsp;</label></li></ul>'.NL;

// Notifications (rendues visibles ultérieurement)
$notification_contenu = date('d-m-Y H:i:s').' '.$_SESSION['USER_PRENOM'].' '.$_SESSION['USER_NOM'].' importe un fichier d\'utilisateurs type '.$import_origine.' / '.$import_profil.'.'."\r\n";
DB_STRUCTURE_NOTIFICATION::enregistrer_action_admin( $notification_contenu , $_SESSION['USER_ID'] );

?>
