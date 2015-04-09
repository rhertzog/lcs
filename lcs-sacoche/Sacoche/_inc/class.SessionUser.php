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

class SessionUser
{

  /**
   * Tester si mdp du webmestre transmis convient.
   * 
   * @param string    $password
   * @return string   'ok' ou un message d'erreur
   */
  public static function tester_authentification_webmestre($password)
  {
    global $PAGE;
    $password_crypte = crypter_mdp($password);
    return ($password_crypte==WEBMESTRE_PASSWORD_MD5) ? 'ok' : 'Mot de passe incorrect ! Nouvelle tentative autorisée dans '.$_SESSION['FORCEBRUTE'][$PAGE]['DELAI'].'s.' ;
  }

  /**
   * Tester si mdp d'un développeur SACoche transmis convient.
   * On réutilise des éléments de la connexion webmestre.
   * 
   * @param string    $password
   * @return string   'ok' ou un message d'erreur
   */
  public static function tester_authentification_developpeur($password)
  {
    return ServeurCommunautaire::tester_auth_devel( crypter_mdp($password) );
  }

  /**
   * Tester si les données transmises permettent d'authentifier un partenaire (convention ENT serveur Sésamath).
   * 
   * @param int       $partenaire_id
   * @param string    $password
   * @return array(string,array)   ('ok',$DB_ROW) ou (message_d_erreur,tableau_vide)
   */
  public static function tester_authentification_partenaire($partenaire_id,$password)
  {
    // Récupérer les données associées à ce partenaire.
    $DB_ROW = DB_WEBMESTRE_PUBLIC::DB_recuperer_donnees_partenaire($partenaire_id);
    // Si id non trouvé...
    if(empty($DB_ROW))
    {
      return array( 'Partenaire introuvable !' , array() );
    }
    // Si mdp incorrect...
    if($DB_ROW['partenaire_password']!=crypter_mdp($password))
    {
      global $PAGE;
      return array( 'Mot de passe incorrect ! Nouvelle tentative autorisée dans '.$_SESSION['FORCEBRUTE'][$PAGE]['DELAI'].'s.' , array() );
    }
    // Enregistrement d'un cookie sur le poste client servant à retenir le partenariat sélectionné si identification avec succès
    Cookie::definir( COOKIE_PARTENAIRE , $DB_ROW['partenaire_id'] , 31536000 /* 60*60*24*365 = 1 an */ );
    // Si on arrive ici c'est que l'identification s'est bien effectuée !
    return array( 'ok' , $DB_ROW );
  }

  /**
   * Tester si les données transmises permettent d'authentifier un utilisateur (sauf webmestre).
   * 
   * En cas de connexion avec les identifiants SACoche, la reconnaissance s'effectue sur le couple login/password.
   * En cas de connexion depuis un service SSO extérieur type CAS, la reconnaissance s'effectue en comparant l'identifiant transmis (via $login) avec l'id ENT de jointure connu de SACoche.
   * En cas de connexion utilisant GEPI, la reconnaissance s'effectue en comparant le login GEPI transmis avec l'id Gepi de jointure connu de SACoche.
   * 
   * @param int       $BASE
   * @param string    $login
   * @param string    $password
   * @param string    $mode_connection 'normal' | 'cas' | 'shibboleth' | 'siecle' | 'vecteur_parent' | 'gepi' | 'ldap' (?)
   * @param string    $parent_nom      facultatif, seulement pour $mode_connection = 'vecteur_parent'
   * @param string    $parent_prenom   facultatif, seulement pour $mode_connection = 'vecteur_parent'
   * @return array(string,array)   ('ok',$DB_ROW) ou (message_d_erreur,tableau_vide)
   */
  public static function tester_authentification_utilisateur($BASE,$login,$password,$mode_connection,$parent_nom='',$parent_prenom='')
  {
    // En cas de multi-structures, il faut charger les paramètres de connexion à la base concernée
    // Sauf pour une connexion à un ENT, car alors il a déjà fallu les charger pour récupérer les paramètres de connexion à l'ENT
    if( ($BASE) && ($mode_connection=='normal') )
    {
      charger_parametres_mysql_supplementaires($BASE);
    }
    // Récupérer les données associées à l'utilisateur.
    $DB_ROW = DB_STRUCTURE_PUBLIC::DB_recuperer_donnees_utilisateur($mode_connection,$login,$parent_nom,$parent_prenom);
    // Si login (ou identifiant SSO) non trouvé...
    if(empty($DB_ROW))
    {
      switch($mode_connection)
      {
        case 'normal'         : $message = 'Nom d\'utilisateur incorrect !'; break;
        case 'cas'            : $message = 'Identification réussie mais identifiant CAS "'       .$login.'" inconnu dans SACoche !<br />Un administrateur doit renseigner que l\'identifiant ENT associé à votre compte SACoche est "' .$login.'"&hellip;<br />Il doit pour cela se connecter à SACoche, menu [Gestion&nbsp;courante], et indiquer pour votre compte dans le champ [Id.&nbsp;ENT] la valeur "' .$login.'".'; break;
        case 'shibboleth'     : $message = 'Identification réussie mais identifiant Shibboleth "'.$login.'" inconnu dans SACoche !<br />Un administrateur doit renseigner que l\'identifiant ENT associé à votre compte SACoche est "' .$login.'"&hellip;<br />Il doit pour cela se connecter à SACoche, menu [Gestion&nbsp;courante], et indiquer pour votre compte dans le champ [Id.&nbsp;ENT] la valeur "' .$login.'".'; break;
        case 'siecle'         : $message = 'Identification réussie mais identifiant Sconet "'    .$login.'" inconnu dans SACoche !<br />Un administrateur doit renseigner que l\'identifiant Sconet associé à votre compte SACoche est "' .$login.'"&hellip;<br />Il doit pour cela se connecter à SACoche, menu [Gestion&nbsp;courante], et indiquer pour votre compte dans le champ [Id.&nbsp;Sconet] la valeur "' .$login.'".'; break;
        case 'vecteur_parent' : $message = 'Identification réussie mais compte parent introuvable dans SACoche !<br />Le compte SACoche d\'un responsable légal dont le nom est "' .$parent_nom.'", le prénom est "' .$parent_prenom.'", et ayant la charge d\'un enfant dont l\'identifiant Sconet est "' .$login.'", n\'a pas été trouvé.'; break;
        case 'gepi'           : $message = 'Identification réussie mais login GEPI "'            .$login.'" inconnu dans SACoche !<br />Un administrateur doit renseigner que l\'identifiant GEPI associé à votre compte SACoche est "'.$login.'"&hellip;<br />Il doit pour cela se connecter à SACoche, menu [Gestion&nbsp;courante], et indiquer pour votre compte dans le champ [Id.&nbsp;Gepi] la valeur "'.$login.'".'; break;
      }
      return array($message,array());
    }
    // Blocage éventuel par le webmestre ou un administrateur ou l'automate
    LockAcces::stopper_si_blocage( $BASE , $DB_ROW['user_profil_sigle'] );
    // Si mdp incorrect...
    if( ($mode_connection=='normal') && ($DB_ROW['user_password']!=crypter_mdp($password)) )
    {
      global $PAGE;
      return array( 'Mot de passe incorrect ! Nouvelle tentative autorisée dans '.$_SESSION['FORCEBRUTE'][$PAGE]['DELAI'].'s.' , array() );
    }
    // Si compte desactivé...
    if($DB_ROW['user_sortie_date']<=TODAY_MYSQL)
    {
      return array( 'Identification réussie mais ce compte est desactivé !' , array() );
    }
    // Mémoriser la date de la (dernière) connexion (pour les autres cas, sera enregistré lors de la confirmation de la prise en compte des infos CNIL).
    if( ($DB_ROW['user_connexion_date']!==NULL) || in_array($DB_ROW['user_profil_type'],array('webmestre','administrateur')) )
    {
      DB_STRUCTURE_PUBLIC::DB_enregistrer_date_connexion($DB_ROW['user_id']);
    }
    // Enregistrement d'un cookie sur le poste client servant à retenir le dernier établissement sélectionné si identification avec succès
    Cookie::definir( COOKIE_STRUCTURE , $BASE , 31536000 /* 60*60*24*365 = 1 an */ );
    // Enregistrement d'un cookie sur le poste client servant à retenir le dernier mode de connexion utilisé si identification avec succès
    Cookie::definir( COOKIE_AUTHMODE , $mode_connection );
    // Si on arrive ici c'est que l'identification s'est bien effectuée !
    return array( 'ok' , $DB_ROW );
  }

  /**
   * Enregistrer en session les informations authentifiant un utilisateur (sauf profils webmestre / développeur / partenaire).
   * 
   * @param int     $BASE
   * @param array   $DB_ROW   ligne issue de la table sacoche_user correspondant à l'utilisateur qui se connecte.
   * @return void
   */
  public static function initialiser_utilisateur($BASE,$DB_ROW)
  {
    // Récupérer et Enregistrer en session les données associées à l'établissement (indices du tableau de session en majuscules).
    $DB_TAB_PARAM = DB_STRUCTURE_PUBLIC::DB_lister_parametres();
    $tab_type_entier  = array(
      'SESAMATH_ID',
      'MOIS_BASCULE_ANNEE_SCOLAIRE',
      'DROIT_ELEVE_DEMANDES',
      'CALCUL_VALEUR_RR',
      'CALCUL_VALEUR_R',
      'CALCUL_VALEUR_V',
      'CALCUL_VALEUR_VV',
      'CALCUL_SEUIL_R',
      'CALCUL_SEUIL_V',
      'CALCUL_LIMITE',
      'CAS_SERVEUR_PORT',
      'ENVELOPPE_HORIZONTAL_GAUCHE',
      'ENVELOPPE_HORIZONTAL_MILIEU',
      'ENVELOPPE_HORIZONTAL_DROITE',
      'ENVELOPPE_VERTICAL_HAUT',
      'ENVELOPPE_VERTICAL_MILIEU',
      'ENVELOPPE_VERTICAL_BAS',
      'OFFICIEL_ARCHIVE_AJOUT_MESSAGE_COPIE',
      'OFFICIEL_ARCHIVE_RETRAIT_TAMPON_SIGNATURE',
      'OFFICIEL_BULLETIN_ONLY_SOCLE',
      'OFFICIEL_BULLETIN_APPRECIATION_RUBRIQUE_LONGUEUR',
      'OFFICIEL_BULLETIN_APPRECIATION_RUBRIQUE_REPORT',
      'OFFICIEL_BULLETIN_APPRECIATION_GENERALE_LONGUEUR',
      'OFFICIEL_BULLETIN_APPRECIATION_GENERALE_REPORT',
      'OFFICIEL_BULLETIN_ASSIDUITE',
      'OFFICIEL_BULLETIN_BARRE_ACQUISITIONS',
      'OFFICIEL_BULLETIN_ACQUIS_TEXTE_NOMBRE',
      'OFFICIEL_BULLETIN_ACQUIS_TEXTE_CODE',
      'OFFICIEL_BULLETIN_MOYENNE_SCORES',
      'OFFICIEL_BULLETIN_CONVERSION_SUR_20',
      'OFFICIEL_BULLETIN_MOYENNE_CLASSE',
      'OFFICIEL_BULLETIN_MOYENNE_GENERALE',
      'OFFICIEL_BULLETIN_FUSION_NIVEAUX',
      'OFFICIEL_MARGE_GAUCHE',
      'OFFICIEL_MARGE_DROITE',
      'OFFICIEL_MARGE_HAUT',
      'OFFICIEL_MARGE_BAS',
      'OFFICIEL_RELEVE_ONLY_SOCLE',
      'OFFICIEL_RELEVE_APPRECIATION_RUBRIQUE_LONGUEUR',
      'OFFICIEL_RELEVE_APPRECIATION_RUBRIQUE_REPORT',
      'OFFICIEL_RELEVE_APPRECIATION_GENERALE_LONGUEUR',
      'OFFICIEL_RELEVE_APPRECIATION_GENERALE_REPORT',
      'OFFICIEL_RELEVE_ASSIDUITE',
      'OFFICIEL_RELEVE_ETAT_ACQUISITION',
      'OFFICIEL_RELEVE_MOYENNE_SCORES',
      'OFFICIEL_RELEVE_POURCENTAGE_ACQUIS',
      'OFFICIEL_RELEVE_CONVERSION_SUR_20',
      'OFFICIEL_RELEVE_CASES_NB',
      'OFFICIEL_RELEVE_AFF_COEF',
      'OFFICIEL_RELEVE_AFF_SOCLE',
      'OFFICIEL_RELEVE_AFF_DOMAINE',
      'OFFICIEL_RELEVE_AFF_THEME',
      'OFFICIEL_SOCLE_APPRECIATION_RUBRIQUE_LONGUEUR',
      'OFFICIEL_SOCLE_APPRECIATION_RUBRIQUE_REPORT',
      'OFFICIEL_SOCLE_APPRECIATION_GENERALE_LONGUEUR',
      'OFFICIEL_SOCLE_APPRECIATION_GENERALE_REPORT',
      'OFFICIEL_SOCLE_ASSIDUITE',
      'OFFICIEL_SOCLE_ONLY_PRESENCE',
      'OFFICIEL_SOCLE_POURCENTAGE_ACQUIS',
      'OFFICIEL_SOCLE_ETAT_VALIDATION',
      'USER_DALTONISME',
    );
    $tab_type_tableau = array(
      'CSS_BACKGROUND-COLOR',
      'CALCUL_VALEUR',
      'CALCUL_SEUIL',
      'NOTE_IMAGE',
      'NOTE_TEXTE',
      'NOTE_LEGENDE',
      'ACQUIS_TEXTE',
      'ACQUIS_LEGENDE',
      'ETABLISSEMENT',
      'ENVELOPPE',
      'OFFICIEL',
      'CAS_SERVEUR',
    );
    foreach($DB_TAB_PARAM as $DB_ROW_PARAM)
    {
      $parametre_nom = strtoupper($DB_ROW_PARAM['parametre_nom']);
      // Certains paramètres sont de type entier.
      $parametre_valeur = (in_array($parametre_nom,$tab_type_entier)) ? (int) $DB_ROW_PARAM['parametre_valeur'] : $DB_ROW_PARAM['parametre_valeur'] ;
      // Certains paramètres sont à enregistrer sous forme de tableau.
      $find = FALSE;
      foreach($tab_type_tableau as $key1)
      {
        $longueur_key1 = strlen($key1);
        if(substr($parametre_nom,0,$longueur_key1)==$key1)
        {
          $key2 = substr($parametre_nom,$longueur_key1+1);
          $_SESSION[$key1][$key2] = $parametre_valeur ;
          $find = TRUE;
          break;
        }
      }
      // Les autres paramètres sont à enregistrer tels quels.
      if(!$find)
      {
        $_SESSION[$parametre_nom] = $parametre_valeur ;
      }
    }
    // Enregistrer en session le numéro de la base.
    $_SESSION['BASE']                   = $BASE;
    // C'est un utilisateur d'un établissement.
    $_SESSION['USER_ETABLISSEMENT']     = TRUE;
    // Enregistrer en session les données associées au profil de l'utilisateur.
    $_SESSION['USER_PROFIL_SIGLE']      = $DB_ROW['user_profil_sigle'];
    $_SESSION['USER_PROFIL_TYPE']       = $DB_ROW['user_profil_type'];
    $_SESSION['USER_PROFIL_NOM_COURT']  = $DB_ROW['user_profil_nom_court_singulier'];
    $_SESSION['USER_PROFIL_NOM_LONG']   = $DB_ROW['user_profil_nom_long_singulier'];
    $_SESSION['USER_JOIN_GROUPES']      = $DB_ROW['user_profil_join_groupes'];   // Seuls les enseignants sont rattachés à des classes et groupes définis ; les autres le sont à tout l'établissement.
    $_SESSION['USER_JOIN_MATIERES']     = $DB_ROW['user_profil_join_matieres'] ; // Seuls les directeurs sont rattachés à toutes les matières ; les autres le sont à des matières définies.
    $_SESSION['USER_MDP_LONGUEUR_MINI'] = (int) $DB_ROW['user_profil_mdp_longueur_mini'];
    $_SESSION['USER_DUREE_INACTIVITE']  = (int) $DB_ROW['user_profil_duree_inactivite'];
    // Enregistrer en session les données personnelles de l'utilisateur.
    $_SESSION['USER_ID']                = (int) $DB_ROW['user_id'];
    $_SESSION['USER_GENRE']             = $DB_ROW['user_genre'];
    $_SESSION['USER_NOM']               = $DB_ROW['user_nom'];
    $_SESSION['USER_PRENOM']            = $DB_ROW['user_prenom'];
    $_SESSION['USER_NAISSANCE_DATE']    = $DB_ROW['user_naissance_date'];
    $_SESSION['USER_EMAIL']             = $DB_ROW['user_email'];
    $_SESSION['USER_EMAIL_ORIGINE']     = $DB_ROW['user_email_origine'];
    $_SESSION['USER_LOGIN']             = $DB_ROW['user_login'];
    $_SESSION['USER_LANGUE']            = $DB_ROW['user_langue'];
    $_SESSION['USER_DALTONISME']        = $DB_ROW['user_daltonisme'];
    $_SESSION['USER_ID_ENT']            = $DB_ROW['user_id_ent'];
    $_SESSION['USER_ID_GEPI']           = $DB_ROW['user_id_gepi'];
    $_SESSION['USER_PARAM_ACCUEIL']     = $DB_ROW['user_param_accueil'];
    $_SESSION['ELEVE_CLASSE_ID']        = (int) $DB_ROW['eleve_classe_id'];
    $_SESSION['ELEVE_CLASSE_NOM']       = $DB_ROW['groupe_nom'];
    $_SESSION['ELEVE_LANGUE']           = (int) $DB_ROW['eleve_langue'];
    $_SESSION['DELAI_CONNEXION']        = (int) $DB_ROW['delai_connexion_secondes']; // Vaut (int)NULL = 0 à la 1e connexion, mais dans ce cas $_SESSION['FIRST_CONNEXION'] est testé avant.
    $_SESSION['FIRST_CONNEXION']        = ($DB_ROW['user_connexion_date']===NULL) ? TRUE : FALSE ;
    if( ($DB_ROW['user_connexion_date']===NULL) && ($DB_ROW['user_profil_type']!='administrateur') )
    {
      $_SESSION['STOP_CNIL'] = TRUE;
    }
    // Récupérer et Enregistrer en session les données des élèves associées à un responsable légal.
    if($_SESSION['USER_PROFIL_TYPE']=='parent')
    {
      $_SESSION['OPT_PARENT_ENFANTS']   = DB_STRUCTURE_COMMUN::DB_OPT_enfants_parent($_SESSION['USER_ID']);
      $_SESSION['OPT_PARENT_CLASSES']   = DB_STRUCTURE_COMMUN::DB_OPT_classes_parent($_SESSION['USER_ID']);
      $_SESSION['NB_ENFANTS'] = (is_array($_SESSION['OPT_PARENT_ENFANTS'])) ? count($_SESSION['OPT_PARENT_ENFANTS']) : 0 ;
      if( ($_SESSION['NB_ENFANTS']==1) && (is_array($_SESSION['OPT_PARENT_CLASSES'])) )
      {
        $_SESSION['ELEVE_CLASSE_ID']    = (int) $_SESSION['OPT_PARENT_CLASSES'][0]['valeur'];
        $_SESSION['ELEVE_CLASSE_NOM']   = $_SESSION['OPT_PARENT_CLASSES'][0]['texte'];
      }
    }
    // Récupérer et Enregistrer en session les données associées aux profils utilisateurs d'un établissement, activés ou non.
    if($_SESSION['USER_PROFIL_TYPE']=='administrateur')
    {
      $_SESSION['TAB_PROFILS_ADMIN'] = array( 'TYPE'=>array() , 'LOGIN_MODELE'=>array() , 'MDP_LONGUEUR_MINI'=>array() , 'DUREE_INACTIVITE'=>array() );
      $DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_profils_parametres( 'user_profil_type,user_profil_login_modele,user_profil_mdp_longueur_mini,user_profil_mdp_date_naissance,user_profil_duree_inactivite' /*listing_champs*/ , FALSE /*only_actif*/ );
      foreach($DB_TAB as $DB_ROW)
      {
        $_SESSION['TAB_PROFILS_ADMIN']['TYPE']              [$DB_ROW['user_profil_sigle']] = $DB_ROW['user_profil_type'];
        $_SESSION['TAB_PROFILS_ADMIN']['LOGIN_MODELE']      [$DB_ROW['user_profil_sigle']] = $DB_ROW['user_profil_login_modele'];
        $_SESSION['TAB_PROFILS_ADMIN']['MDP_LONGUEUR_MINI'] [$DB_ROW['user_profil_sigle']] = (int) $DB_ROW['user_profil_mdp_longueur_mini'];
        $_SESSION['TAB_PROFILS_ADMIN']['MDP_DATE_NAISSANCE'][$DB_ROW['user_profil_sigle']] = (int) $DB_ROW['user_profil_mdp_date_naissance'];
        $_SESSION['TAB_PROFILS_ADMIN']['DUREE_INACTIVITE']  [$DB_ROW['user_profil_sigle']] = (int) $DB_ROW['user_profil_duree_inactivite'];
      }
    }
    // Récupérer et Enregistrer en session les noms des profils utilisateurs d'un établissement (activés) pour afficher les droits de certaines pages.
    else
    {
      $_SESSION['TAB_PROFILS_DROIT'] = array( 'TYPE'=>array() , 'JOIN_GROUPES'=>array() , 'JOIN_MATIERES'=>array() , 'NOM_LONG_PLURIEL'=>array() );
      $DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_profils_parametres( 'user_profil_type,user_profil_join_groupes,user_profil_join_matieres,user_profil_nom_long_pluriel' /*listing_champs*/ , TRUE /*only_actif*/ );
      foreach($DB_TAB as $DB_ROW)
      {
        $_SESSION['TAB_PROFILS_DROIT']['TYPE']            [$DB_ROW['user_profil_sigle']] = $DB_ROW['user_profil_type'];
        $_SESSION['TAB_PROFILS_DROIT']['JOIN_GROUPES']    [$DB_ROW['user_profil_sigle']] = $DB_ROW['user_profil_join_groupes'];
        $_SESSION['TAB_PROFILS_DROIT']['JOIN_MATIERES']   [$DB_ROW['user_profil_sigle']] = $DB_ROW['user_profil_join_matieres'];
        $_SESSION['TAB_PROFILS_DROIT']['NOM_LONG_PLURIEL'][$DB_ROW['user_profil_sigle']] = $DB_ROW['user_profil_nom_long_pluriel'];
      }
    }
    // Fabriquer $_SESSION['IMG_...'] et $_SESSION['BACKGROUND_...'] en fonction de $_SESSION['USER_DALTONISME'] à partir de $_SESSION['NOTE_IMAGE_...'] et $_SESSION['CSS_BACKGROUND-COLOR']['...']
    // remarque : $_SESSION['USER_DALTONISME'] ne peut être utilisé que pour les profils élèves/parents/profs/directeurs, pas les admins ni le webmestre
    SessionUser::adapter_daltonisme() ;
    // Enregistrer en session le CSS personnalisé
    SessionUser::actualiser_style();
    // Enregistrer en session le menu personnalisé
    SessionUser::memoriser_menu();
    // Juste pour davantage de lisibilité si besoin de debug...
    ksort($_SESSION);
    // Enfin, on profite de cet événement pour faire du ménage ou simuler une tâche planifiée
    SessionUser::cron();
  }

  /**
   * Enregistrer en session les informations authentifiant le webmestre.
   * 
   * @param void
   * @return void
   */
  public static function initialiser_webmestre()
  {
    // Numéro de la base
    $_SESSION['BASE']                          = 0;
    // Ce n'est pas un utilisateur d'un établissement.
    $_SESSION['USER_ETABLISSEMENT']            = FALSE;
    // Données associées au profil de l'utilisateur.
    $_SESSION['USER_PROFIL_SIGLE']             = 'WBM';
    $_SESSION['USER_PROFIL_TYPE']              = 'webmestre';
    $_SESSION['USER_PROFIL_NOM_COURT']         = 'webmestre';
    $_SESSION['USER_PROFIL_NOM_LONG']          = 'responsable du serveur (webmestre)';
    $_SESSION['USER_MDP_LONGUEUR_MINI']        = 6;
    $_SESSION['USER_DUREE_INACTIVITE']         = 15;
    // Données personnelles de l'utilisateur.
    $_SESSION['USER_ID']                       = 0;
    $_SESSION['USER_NOM']                      = WEBMESTRE_NOM;
    $_SESSION['USER_PRENOM']                   = WEBMESTRE_PRENOM;
    $_SESSION['USER_LANGUE']                   = LOCALE_DEFAULT;
    // Données associées à l'établissement.
    $_SESSION['SESAMATH_ID']                   = 0;
    $_SESSION['ETABLISSEMENT']['DENOMINATION'] = 'Gestion '.HEBERGEUR_INSTALLATION;
    $_SESSION['CONNEXION_MODE']                = 'normal';
    // Enregistrer en session le menu personnalisé
    SessionUser::memoriser_menu();
  }

  /**
   * Enregistrer en session les informations authentifiant un développeur.
   * 
   * @param void
   * @return void
   */
  public static function initialiser_developpeur()
  {
    // Numéro de la base
    $_SESSION['BASE']                          = 0;
    // Ce n'est pas un utilisateur d'un établissement.
    $_SESSION['USER_ETABLISSEMENT']            = FALSE;
    // Données associées au profil de l'utilisateur.
    $_SESSION['USER_PROFIL_SIGLE']             = 'DVL';
    $_SESSION['USER_PROFIL_TYPE']              = 'developpeur';
    $_SESSION['USER_PROFIL_NOM_COURT']         = 'devel';
    $_SESSION['USER_PROFIL_NOM_LONG']          = 'développeur SACoche';
    $_SESSION['USER_MDP_LONGUEUR_MINI']        = 6;
    $_SESSION['USER_DUREE_INACTIVITE']         = 15;
    // Données personnelles de l'utilisateur.
    $_SESSION['USER_ID']                       = 0;
    $_SESSION['USER_NOM']                      = 'SACoche';
    $_SESSION['USER_PRENOM']                   = 'développeur';
    $_SESSION['USER_LANGUE']                   = LOCALE_DEFAULT;
    // Données associées à l'établissement.
    $_SESSION['SESAMATH_ID']                   = 0;
    $_SESSION['ETABLISSEMENT']['DENOMINATION'] = HEBERGEUR_INSTALLATION;
    $_SESSION['CONNEXION_MODE']                = 'normal';
    // Enregistrer en session le menu personnalisé
    SessionUser::memoriser_menu();
  }

  /**
   * Enregistrer en session les informations authentifiant un partenaire.
   * 
   * @param array   $DB_ROW   ligne issue de la table sacoche_partenaire correspondant à l'utilisateur qui se connecte.
   * @return void
   */
  public static function initialiser_partenaire($DB_ROW)
  {
    // Numéro de la base
    $_SESSION['BASE']                          = 0;
    // Ce n'est pas un utilisateur d'un établissement.
    $_SESSION['USER_ETABLISSEMENT']            = FALSE;
    // Données associées au profil de l'utilisateur.
    $_SESSION['USER_PROFIL_SIGLE']             = 'ENT';
    $_SESSION['USER_PROFIL_TYPE']              = 'partenaire';
    $_SESSION['USER_PROFIL_NOM_COURT']         = 'partenaire';
    $_SESSION['USER_PROFIL_NOM_LONG']          = 'partenariat conventionné (ENT)';
    $_SESSION['USER_MDP_LONGUEUR_MINI']        = 6;
    $_SESSION['USER_DUREE_INACTIVITE']         = 15;
    // Données personnelles de l'utilisateur.
    $_SESSION['USER_ID']                       = (int) $DB_ROW['partenaire_id'];
    $_SESSION['USER_NOM']                      = $DB_ROW['partenaire_nom'];
    $_SESSION['USER_PRENOM']                   = $DB_ROW['partenaire_prenom'];
    $_SESSION['USER_LANGUE']                   = LOCALE_DEFAULT;
    $_SESSION['USER_CONNECTEURS']              = $DB_ROW['partenaire_connecteurs'];
    // Données associées à l'établissement.
    $_SESSION['SESAMATH_ID']                   = 0;
    $_SESSION['ETABLISSEMENT']['DENOMINATION'] = $DB_ROW['partenaire_denomination'];
    $_SESSION['CONNEXION_MODE']                = 'normal';
    // Enregistrer en session le menu personnalisé
    SessionUser::memoriser_menu();
  }

  /**
   * Compléter la session avec les informations de style dépendant du daltonisme + des choix paramétrés au niveau de l'établissement (couleurs, codes de notation).
   * 
   * @param void
   * @return void
   */
  public static function adapter_daltonisme()
  {
    // codes de notation
    $_SESSION['IMG_RR'] = $_SESSION['USER_DALTONISME'] ? './_img/note/daltonisme/h/RR.gif' : './_img/note/choix/h/'.$_SESSION['NOTE_IMAGE']['RR'].'.gif' ;
    $_SESSION['IMG_R' ] = $_SESSION['USER_DALTONISME'] ? './_img/note/daltonisme/h/R.gif'  : './_img/note/choix/h/'.$_SESSION['NOTE_IMAGE']['R' ].'.gif' ;
    $_SESSION['IMG_V' ] = $_SESSION['USER_DALTONISME'] ? './_img/note/daltonisme/h/V.gif'  : './_img/note/choix/h/'.$_SESSION['NOTE_IMAGE']['V' ].'.gif' ;
    $_SESSION['IMG_VV'] = $_SESSION['USER_DALTONISME'] ? './_img/note/daltonisme/h/VV.gif' : './_img/note/choix/h/'.$_SESSION['NOTE_IMAGE']['VV'].'.gif' ;
    // couleurs des états d'acquisition
    $_SESSION['BACKGROUND_NA'] = $_SESSION['USER_DALTONISME'] ? '#909090' : $_SESSION['CSS_BACKGROUND-COLOR']['NA'] ;
    $_SESSION['BACKGROUND_VA'] = $_SESSION['USER_DALTONISME'] ? '#BEBEBE' : $_SESSION['CSS_BACKGROUND-COLOR']['VA'] ;
    $_SESSION['BACKGROUND_A']  = $_SESSION['USER_DALTONISME'] ? '#EAEAEA' : $_SESSION['CSS_BACKGROUND-COLOR']['A'] ;
    // couleurs des états de validation
    $_SESSION['BACKGROUND_V0'] = $_SESSION['USER_DALTONISME'] ? '#909090' : '#FF9999' ; // validation négative
    $_SESSION['BACKGROUND_V1'] = $_SESSION['USER_DALTONISME'] ? '#EAEAEA' : '#99FF99' ; // validation positive
    $_SESSION['BACKGROUND_V2'] = $_SESSION['USER_DALTONISME'] ? '#BEBEBE' : '#BBBBFF' ; // validation en attente
    $_SESSION['OPACITY']       = $_SESSION['USER_DALTONISME'] ? 1         : 0.3 ;
  }

  /**
   * Compléter la session avec les informations de style dépendant des choix paramétrés au niveau de l'établissement (couleurs, codes de notation).
   * 
   * @param void
   * @return void
   */
  public static function actualiser_style()
  {
    $_SESSION['CSS']  = '';
    // codes de notation
    $_SESSION['CSS'] .= 'table.scor_eval tbody.h td input.RR {background:#FFF url('.$_SESSION['IMG_RR'].') no-repeat center center;}'.NL;
    $_SESSION['CSS'] .= 'table.scor_eval tbody.h td input.R  {background:#FFF url('.$_SESSION['IMG_R' ].') no-repeat center center;}'.NL;
    $_SESSION['CSS'] .= 'table.scor_eval tbody.h td input.V  {background:#FFF url('.$_SESSION['IMG_V' ].') no-repeat center center;}'.NL;
    $_SESSION['CSS'] .= 'table.scor_eval tbody.h td input.VV {background:#FFF url('.$_SESSION['IMG_VV'].') no-repeat center center;}'.NL;
    $_SESSION['CSS'] .= 'table.scor_eval tbody.v td input.RR {background:#FFF url('.str_replace('/h/','/v/',$_SESSION['IMG_RR']).') no-repeat center center;}'.NL;
    $_SESSION['CSS'] .= 'table.scor_eval tbody.v td input.R  {background:#FFF url('.str_replace('/h/','/v/',$_SESSION['IMG_R' ]).') no-repeat center center;}'.NL;
    $_SESSION['CSS'] .= 'table.scor_eval tbody.v td input.V  {background:#FFF url('.str_replace('/h/','/v/',$_SESSION['IMG_V' ]).') no-repeat center center;}'.NL;
    $_SESSION['CSS'] .= 'table.scor_eval tbody.v td input.VV {background:#FFF url('.str_replace('/h/','/v/',$_SESSION['IMG_VV']).') no-repeat center center;}'.NL;
    // couleurs des états d'acquisition
    $_SESSION['CSS'] .= 'table th.r , table td.r , div.r ,span.r ,label.r {background-color:'.$_SESSION['BACKGROUND_NA'].'}'.NL;
    $_SESSION['CSS'] .= 'table th.o , table td.o , div.o ,span.o ,label.o {background-color:'.$_SESSION['BACKGROUND_VA'].'}'.NL;
    $_SESSION['CSS'] .= 'table th.v , table td.v , div.v ,span.v ,label.v {background-color:'.$_SESSION['BACKGROUND_A'].'}'.NL;
    // couleurs des états de validation
    $_SESSION['CSS'] .= '#tableau_validation tbody th.down0 {background:'.$_SESSION['BACKGROUND_V0'].' url(./_img/socle/arrow_down.gif) no-repeat center center;opacity:'.$_SESSION['OPACITY'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody th.down1 {background:'.$_SESSION['BACKGROUND_V1'].' url(./_img/socle/arrow_down.gif) no-repeat center center;opacity:'.$_SESSION['OPACITY'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody th.down2 {background:'.$_SESSION['BACKGROUND_V2'].' url(./_img/socle/arrow_down.gif) no-repeat center center;opacity:'.$_SESSION['OPACITY'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody th.left0 {background:'.$_SESSION['BACKGROUND_V0'].' url(./_img/socle/arrow_left.gif) no-repeat center center;opacity:'.$_SESSION['OPACITY'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody th.left1 {background:'.$_SESSION['BACKGROUND_V1'].' url(./_img/socle/arrow_left.gif) no-repeat center center;opacity:'.$_SESSION['OPACITY'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody th.left2 {background:'.$_SESSION['BACKGROUND_V2'].' url(./_img/socle/arrow_left.gif) no-repeat center center;opacity:'.$_SESSION['OPACITY'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody th.diag0 {background:'.$_SESSION['BACKGROUND_V0'].' url(./_img/socle/arrow_diag.gif) no-repeat center center;opacity:'.$_SESSION['OPACITY'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody th.diag1 {background:'.$_SESSION['BACKGROUND_V1'].' url(./_img/socle/arrow_diag.gif) no-repeat center center;opacity:'.$_SESSION['OPACITY'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody th.diag2 {background:'.$_SESSION['BACKGROUND_V2'].' url(./_img/socle/arrow_diag.gif) no-repeat center center;opacity:'.$_SESSION['OPACITY'].'}'.NL;
    $_SESSION['CSS'] .= 'th.v0 , td.v0 , span.v0 {background:'.$_SESSION['BACKGROUND_V0'].'}'.NL;
    $_SESSION['CSS'] .= 'th.v1 , td.v1 , span.v1 {background:'.$_SESSION['BACKGROUND_V1'].'}'.NL;
    $_SESSION['CSS'] .= 'th.v2 , td.v2 , span.v2 {background:'.$_SESSION['BACKGROUND_V2'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation td.v0 {color:'.$_SESSION['BACKGROUND_V0'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation td.v1 {color:'.$_SESSION['BACKGROUND_V1'].'}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation td.v2 {color:'.$_SESSION['BACKGROUND_V2'].'}'.NL;
    $_SESSION['CSS'] .= '#zone_information .v0 {background:'.$_SESSION['BACKGROUND_V0'].';padding:0 1em;margin-right:1ex}'.NL;
    $_SESSION['CSS'] .= '#zone_information .v1 {background:'.$_SESSION['BACKGROUND_V1'].';padding:0 1em;margin-right:1ex}'.NL;
    $_SESSION['CSS'] .= '#zone_information .v2 {background:'.$_SESSION['BACKGROUND_V2'].';padding:0 1em;margin-right:1ex}'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody td[data-etat=lock] {background:'.$_SESSION['BACKGROUND_V1'].' url(./_img/socle/lock.gif) no-repeat center center;} /* surclasse une classe v0 ou v1 ou v2 car défini après */'.NL;
    $_SESSION['CSS'] .= '#tableau_validation tbody td[data-etat=done] {background-image:url(./_img/socle/done.gif);background-repeat:no-repeat;background-position:center center;} /* pas background pour ne pas écraser background-color défini avant */'.NL;
  }

  /**
   * Enregistrer en session le menu selon le profil et éventuellement les droits de l'utilisateur.
   * 
   * @param void
   * @return void
   */
  public static function memoriser_menu()
  {
    $line_height = 37+1; // @see ./_css/style.css --> #menu li li a {line-height:30px}
    $numero_menu = 0;
    require(CHEMIN_DOSSIER_MENUS.'menu_'.$_SESSION['USER_PROFIL_TYPE'].'.php'); // récupère $tab_menu & $tab_sous_menu
    $_SESSION['MENU'] = '<ul id="menu"><li><a class="boussole" href="#">'.html(Lang::_("MENU")).'</a><ul>'.NL;
    $nombre_menu = count($tab_menu);
    foreach($tab_menu as $menu_id => $menu_titre)
    {
      $_SESSION['MENU'] .= '<li><a class="fleche" href="#">'.html($menu_titre).'</a><ul>'.NL;
      $nombre_sous_menu = count($tab_sous_menu[$menu_id]);
      $premier_sous_menu = TRUE;
      foreach($tab_sous_menu[$menu_id] as $sous_menu_id => $tab)
      {
        $nombre_cases_decalage = min( $numero_menu , $numero_menu-($nombre_menu-$nombre_sous_menu) );
        $style = ($premier_sous_menu && $nombre_cases_decalage) ? ' style="margin-top:-'.($nombre_cases_decalage*$line_height).'px"' : '' ;
        $_SESSION['MENU'] .= '<li><a class="'.$tab['class'].'"'.$style.' href="./index.php?'.$tab['href'].'">'.html($tab['texte']).'</a></li>'.NL;
        $premier_sous_menu = FALSE;
      }
      $_SESSION['MENU'] .= '</ul></li>'.NL;
      $numero_menu++;
    }
    $_SESSION['MENU'] .= '</ul></li></ul>'.NL;
  }

  /**
   * Tâches pseudo-planifiées exécutées lors de la connexion d'un utilisateur d'un établissement
   * 
   * @param void
   * @return void
   */
  public static function cron()
  {
    // On essaye de faire en sorte que plusieurs connexions ne lancent pas ces procédures simultanément
    $fichier_lock = CHEMIN_DOSSIER_TMP.'lock.txt';
    if(!file_exists($fichier_lock))
    {
      // On écrit un marqueur
      FileSystem::ecrire_fichier($fichier_lock,'');
      // On efface les fichiers temporaires obsolètes
      FileSystem::nettoyer_fichiers_temporaires($_SESSION['BASE']);
      // On rend visibles les notifications en attente et on supprime les notifications obsolètes
      Sesamail::envoyer_notifications();
      DB_STRUCTURE_NOTIFICATION::DB_supprimer_log_anciens();
      // On efface le marqueur
      FileSystem::supprimer_fichier($fichier_lock);
    }
    // Si le fichier témoin du nettoyage existe, on vérifie que sa présence n'est pas anormale (cela s'est déjà produit...)
    else
    {
      if( $_SERVER['REQUEST_TIME'] - filemtime($fichier_lock) > 30 )
      {
        FileSystem::supprimer_fichier($fichier_lock);
      }
    }
  }

}
?>