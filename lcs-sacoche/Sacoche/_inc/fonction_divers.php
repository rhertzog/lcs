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

/*
 * Réciproque de html()
 * 
 * @param string
 * @return string
 */
function html_decode($text)
{
  return htmlspecialchars_decode($text,ENT_COMPAT) ;
}

/**
 * Fonctions utilisées avec array_filter() ; teste si différent de FALSE et de NULL.
 * @return bool
 */
function non_vide($n)
{
  return ($n!==FALSE) && ($n!==NULL) ;
}
/**
 * Fonctions utilisées avec array_filter() ; teste si différent de zéro.
 * @return bool
 */
function non_zero($n)
{
  return $n!==0 ;
}
/**
 * Fonctions utilisées avec array_filter() ; teste si strictement positif.
 * @return bool
 */
function positif($n)
{
  return $n>0 ;
}
/**
 * Fonctions utilisées avec array_filter() ; teste si différent "X" (pas "REQ" car désormais cela peut être saisi).
 * @return bool
 */
function sans_rien($note)
{
  return $note!='X' ;
}
/**
 * Fonctions utilisées avec array_filter() ; teste si différent de 2.
 * @return bool
 */
function is_renseigne($etat)
{
  return $etat!=2 ;
}

/**
 * Tester un item est considéré comme acquis au vu du score transmis.
 * 
 * Le seuil peut être celui défini globalement (par défaut si rien de transmis) ou un seuil testé ; peut être appelé avec array_filter().
 * 
 * @param int $score
 * @param int $seuil (facultatif)
 * @return bool
 */
function test_A($score,$seuil=NULL)
{
  $seuil = ($seuil===NULL) ? $_SESSION['CALCUL_SEUIL']['V'] : $seuil ;
  return $score>$seuil ;
}

/**
 * test_NA
 * Tester un item est considéré comme non acquis au vu du score transmis.
 * Le seuil peut être celui défini globalement (par défaut si rien de transmis) ou un seuil testé ; peut être appelé avec array_filter().
 * 
 * @param int $score
 * @param int $seuil
 * @return bool
 */
function test_NA($score,$seuil=NULL)
{
  $seuil = ($seuil===NULL) ? $_SESSION['CALCUL_SEUIL']['R'] : $seuil ;
  return $score<$seuil ;
}

/**
 * roundTo
 * Arrondir à une précision donnée, par exemple à 0,5 près
 * @see   http://fr.php.net/manual/fr/function.round.php#93747
 * @param float $nombre
 * @param float $precision
 * @return float
 */
function roundTo($nombre,$precision)
{
  return ($precision) ? round( $nombre/$precision , 0 ) * $precision : $nombre ;
}

/**
 * ceilTo
 * Arrondir à une précision donnée, par exemple à 0,5 près, par excès
 * @param float $nombre
 * @param float $precision
 * @return float
 */
function ceilTo($nombre,$precision)
{
  return ($precision) ? ceil( $nombre/$precision ) * $precision : $nombre ;
}

/**
 * floorTo
 * Arrondir à une précision donnée, par exemple à 0,5 près, par défaut
 * @param float $nombre
 * @param float $precision
 * @return float
 */
function floorTo($nombre,$precision)
{
  return ($precision) ? floor( $nombre/$precision ) * $precision : $nombre ;
}

/**
 * Calculer le score d'un item, à partir des notes transmises et des paramètres de calcul.
 * 
 * @param array  $tab_devoirs      $tab_devoirs[$i]['note'] = note
 * @param string $calcul_methode   'geometrique' / 'arithmetique' / 'classique' / 'moyenne' / 'bestof'
 * @param int    $calcul_limite    nb maxi d'éval à prendre en compte
 * @return int|FALSE
 */
function calculer_score($tab_devoirs,$calcul_methode,$calcul_limite)
{
  // on passe en revue les évaluations disponibles, et on retient les notes exploitables
  $tab_modele_bon = array('RR','R','V','VV');  // les notes prises en compte dans le calcul du score
  $tab_note = array(); // pour retenir les notes en question
  $nb_devoir = count($tab_devoirs);
  for($i=0;$i<$nb_devoir;$i++)
  {
    if(in_array($tab_devoirs[$i]['note'],$tab_modele_bon))
    {
      $tab_note[] = $_SESSION['CALCUL_VALEUR'][$tab_devoirs[$i]['note']];
    }
  }
  // si pas de notes exploitables, on arrête de suite (sinon, on est certain de pouvoir renvoyer un score)
  $nb_note = count($tab_note);
  if($nb_note==0)
  {
    return FALSE;
  }
  // si le paramétrage du référentiel l'indique, on tronque pour ne garder que les derniers résultats
  if( ($calcul_limite) && ($nb_note>$calcul_limite) )
  {
    $tab_note = array_slice($tab_note,-$calcul_limite);
    $nb_note = $calcul_limite;
  }
  // 1. Calcul de la note en fonction de la méthode du référentiel : 'geometrique','arithmetique','classique'
  if(in_array($calcul_methode,array('geometrique','arithmetique','classique')))
  {
    // 1a. Initialisation
    $somme_point = 0;
    $somme_coef = 0;
    $coef = 1;
    // 1b. Pour chaque devoir (note)...
    for($num_devoir=1 ; $num_devoir<=$nb_note ; $num_devoir++)
    {
      $somme_point += $tab_note[$num_devoir-1]*$coef;
      $somme_coef += $coef;
      $coef = ($calcul_methode=='geometrique') ? $coef*2 : ( ($calcul_methode=='arithmetique') ? $coef+1 : 1 ) ; // Calcul du coef de l'éventuel devoir suivant
    }
    // 1c. Calcul final du score
    return round( $somme_point/$somme_coef , 0 );
  }
  // 2. Calcul de la note en fonction de la méthode du référentiel : 'bestof1','bestof2','bestof3'
  if(in_array($calcul_methode,array('bestof1','bestof2','bestof3')))
  {
    // 2a. Initialisation
    $tab_notes = array();
    $nb_best = (int)substr($calcul_methode,-1);
    // 2b. Pour chaque devoir (note)...
    for($num_devoir=1 ; $num_devoir<=$nb_note ; $num_devoir++)
    {
      $tab_notes[] = $tab_note[$num_devoir-1];
    }
    // 2c. Calcul final du score
    rsort($tab_notes);
    $tab_notes = array_slice( $tab_notes , 0 , $nb_best );
    return round( array_sum($tab_notes)/count($tab_notes) , 0 );
  }
}

/**
 * Ajout d'un log PHP dans le fichier error-log du serveur Web
 * 
 * @param string $log_objet       objet du log
 * @param string $log_contenu     contenu du log
 * @param string $log_fichier     transmettre __FILE__
 * @param string $log_ligne       transmettre __LINE__
 * @param bool   $only_sesamath   [TRUE] pour une inscription uniquement sur le serveur Sésamath (par défaut), [FALSE] sinon
 * @return void
 */
function ajouter_log_PHP($log_objet,$log_contenu,$log_fichier,$log_ligne,$only_sesamath=TRUE)
{
  if( (!$only_sesamath) || (strpos(URL_INSTALL_SACOCHE,SERVEUR_PROJET)===0) )
  {
    $SEP = ' ║ ';
    error_log('SACoche info' . $SEP . $log_objet . $SEP . 'base '.$_SESSION['BASE'] . $SEP . 'user '.$_SESSION['USER_ID'] . $SEP . basename($log_fichier).' '.$log_ligne . $SEP . $log_contenu,0);
  }
}

/**
 * Charger les parametres mysql de connexion d'un établissement qui n'auraient pas été chargé par le fichier index ou ajax.
 * 
 * Dans le cas d'une installation de type multi-structures, on peut avoir besoin d'effectuer une requête sur une base d'établissement sans y être connecté :
 * => pour savoir si le mode de connexion est SSO ou pas (./pages/public_*.php)
 * => pour l'identification (méthode SessionUser::tester_authentification_utilisateur())
 * => pour le webmestre (création d'un admin, info sur les admins, initialisation du mdp...)
 * Dans le cas d'une installation de type multi-structures, on peut avoir besoin d'effectuer une requête sur la base du webmestre :
 * => pour avoir des infos sur le contact référent, ou l'état d'une convention ENT
 * 
 * @param int   $BASE   0 pour celle du webmestre
 * @param bool  $exit   TRUE par défaut (arrêt si erreur)
 * @return bool | exit
 */
function charger_parametres_mysql_supplementaires( $BASE , $exit=TRUE )
{
  $fichier_mysql_config_supplementaire = ($BASE) ? CHEMIN_DOSSIER_MYSQL.'serveur_sacoche_structure_'.$BASE.'.php' : CHEMIN_DOSSIER_MYSQL.'serveur_sacoche_webmestre.php' ;
  $fichier_class_config_supplementaire = ($BASE) ? CHEMIN_DOSSIER_INCLUDE.'class.DB.config.sacoche_structure.php' : CHEMIN_DOSSIER_INCLUDE.'class.DB.config.sacoche_webmestre.php' ;
  if(is_file($fichier_mysql_config_supplementaire))
  {
    global $_CONST; // Car si on charge les paramètres dans une fonction, ensuite ils ne sont pas trouvés par la classe de connexion.
    require($fichier_mysql_config_supplementaire);
    require($fichier_class_config_supplementaire);
    return TRUE;
  }
  else
  {
    if($exit)
    {
      exit_error( 'Paramètres BDD manquants' /*titre*/ , 'Les paramètres de connexion à la base de données n\'ont pas été trouvés.<br />Le fichier "'.FileSystem::fin_chemin($fichier_mysql_config_supplementaire).'" (base n°'.$BASE.') est manquant !' /*contenu*/ );
    }
    else
    {
      return FALSE;
    }
  }
}

/**
 * Fabriquer un login à partir de nom/prénom selon le format paramétré par l'administrateur (reste à tester sa disponibilité).
 * Cette fonction n'est appelée que depuis un espace administrateur ; $_SESSION['TAB_PROFILS_ADMIN']['LOGIN_MODELE'] est donc défini.
 * 
 * @param string $prenom
 * @param string $nom
 * @param string $profil_sigle
 * @return string
 */
function fabriquer_login($prenom,$nom,$profil_sigle)
{
  $modele = $_SESSION['TAB_PROFILS_ADMIN']['LOGIN_MODELE'][$profil_sigle];
  $login_prenom = mb_substr( str_replace(array('.','-','_'),'',Clean::login($prenom)) , 0 , mb_substr_count($modele,'p') );
  $login_nom    = mb_substr( str_replace(array('.','-','_'),'',Clean::login($nom))    , 0 , mb_substr_count($modele,'n') );
  $login_separe = str_replace(array('p','n'),'',$modele);
  $login = ($modele{0}=='p') ? $login_prenom.$login_separe.$login_nom : $login_nom.$login_separe.$login_prenom ;
  return $login;
}

/**
 * Fabriquer un mot de passe ; 8 caractères imposés.
 * Cette fonction peut être appelée par le webmestre ou à l'installation ; $_SESSION['TAB_PROFILS_ADMIN']['MDP_LONGUEUR_MINI'] n'est alors pas défini -> dans ce cas, on ne transmet pas de paramètre.
 * 
 * Certains caractères sont évités :
 * "e" sinon un tableur peut interpréter le mot de passe comme un nombre avec exposant
 * "i"j"1"l" pour éviter une confusion entre eux
 * "m"w" pour éviter la confusion avec "nn"vv"
 * "o"0" pour éviter une confusion entre eux
 * 
 * @param string $profil_sigle
 * @return string
 */
function fabriquer_mdp($profil_sigle=NULL)
{
  $nb_chars = ($profil_sigle) ? $_SESSION['TAB_PROFILS_ADMIN']['MDP_LONGUEUR_MINI'][$profil_sigle] : 8 ;
  return mb_substr(str_shuffle('2345678923456789aaaaauuuuubcdfghknpqrstvxyz'),0,$nb_chars);
}

/**
 * Crypter un mot de passe avant enregistrement dans la base.
 * 
 * Le "salage" complique la recherche d'un mdp à partir de son empreinte md5 en utilisant une table arc-en-ciel.
 * 
 * @param string $password
 * @return string
 */
function crypter_mdp($password)
{
  return md5('grain_de_sel'.$password);
}

/**
 * Fabrique une date et une valeur aléatoire pour terminer un nom de fichier.
 * 
 * @param void
 * @return string
 */
function fabriquer_fin_nom_fichier__date_et_alea()
{
  // date
  $chaine_date = date('Y-m-d_H\hi\m\i\ns\s'); // lisible par un humain et compatible avec le système de fichiers
  // valeur aléatoire
  $longueur_chaine = 15; // permet > 2x10^23 possibilités : même en en testant 1 milliard /s il faudrait plus de 7 millions d'années pour toutes les essayer
  $caracteres = '0123456789abcdefghijklmnopqrstuvwxyz';
  $alea_max = strlen($caracteres)-1;
  $chaine_alea = '';
  for( $i=0 ; $i<$longueur_chaine ; $i++ )
  {
    $chaine_alea .= $caracteres{mt_rand(0,$alea_max)};
  }
  // retour
  return $chaine_date.'_'.$chaine_alea;
}

/**
 * Fabrique une fin de fichier pseudo-aléatoire pour terminer un nom de fichier.
 * 
 * Le suffixe est suffisamment tordu pour le rendre un privé et non retrouvable par un utilisateur, mais sans être totalement aléatoire car il doit fixe (retrouvé).
 * Utilisé pour les flux RSS et les bilans officiels PDF.
 * 
 * @param string   $fichier_nom_debut
 * @return string
 */
function fabriquer_fin_nom_fichier__pseudo_alea($fichier_nom_debut)
{
  return md5($fichier_nom_debut.$_SERVER['DOCUMENT_ROOT']);
}

/**
 * Fabrique une fin de fichier pseudo-aléatoire pour terminer un nom de fichier.
 * 
 * Le suffixe est suffisamment tordu pour le rendre un privé et non retrouvable par un utilisateur, mais sans être totalement aléatoire car il doit fixe (retrouvé).
 * Utilisé pour les flux RSS, les bilans officiels PDF, les fiches brevet PDF.
 * 
 * @param int      $eleve_id
 * @param string   $bilan_type
 * @param int      $periode_id
 * @return string
 */
function fabriquer_nom_fichier_bilan_officiel( $eleve_id , $bilan_type , $periode_id )
{
  $fichier_bilan_officiel_nom_debut = 'user'.$eleve_id.'_officiel_'.$bilan_type.'_periode'.$periode_id;
  $fichier_bilan_officiel_nom_fin   = fabriquer_fin_nom_fichier__pseudo_alea($fichier_bilan_officiel_nom_debut);
  return $fichier_bilan_officiel_nom_debut.'_'.$fichier_bilan_officiel_nom_fin.'.pdf';
}

/**
 * Mettre à jour automatiquement la base si besoin ; à effectuer avant toute récupération des données sinon ça peut poser pb...
 * 
 * @param int   $BASE
 * @return void
 */
function maj_base_structure_si_besoin($BASE)
{
  $version_base_structure = DB_STRUCTURE_PUBLIC::DB_version_base();
  if($version_base_structure != VERSION_BASE_STRUCTURE)
  {
    // On ne met pas à jour la base tant que le webmestre bloque l'accès à l'application, car sinon cela pourrait se produire avant le transfert de tous les fichiers.
    if(LockAcces::tester_blocage('webmestre',0)===NULL)
    {
      // Bloquer l'application
      LockAcces::bloquer_application('automate',$BASE,'Mise à jour de la base en cours.');
      // Lancer une mise à jour de la base
      DB_STRUCTURE_MAJ_BASE::DB_maj_base($version_base_structure);
      // Log de l'action
      SACocheLog::ajouter('Mise à jour automatique de la base '.SACOCHE_STRUCTURE_BD_NAME.'.');
      // Débloquer l'application
      LockAcces::debloquer_application('automate',$BASE);
    }
  }
}

/**
 * Récupérer le numéro de la dernière version de SACoche disponible auprès du serveur communautaire.
 * 
 * @param void
 * @return string 'AAAA-MM-JJi' ou message d'erreur
 */
function recuperer_numero_derniere_version()
{
  $requete_reponse = cURL::get_contents(SERVEUR_VERSION);
  return (preg_match('#^[0-9]{4}\-[0-9]{2}\-[0-9]{2}[a-z]?$#',$requete_reponse)) ? $requete_reponse : 'Dernière version non détectée&hellip;' ;
}

/**
 * Retourner un tableau de lignes à partir d'un texte en se basant sur les retours chariot.
 * Utilisé notamment lors de la récupération d'un fichier CSV.
 * 
 * @param string   $texte
 * @return array
 */
function extraire_lignes($texte)
{
  $texte = trim($texte);
  $texte = str_replace('"','',$texte);
  $texte = str_replace(array("\r\n","\n\n","\r\r","\r","\n"),'®',$texte);
  return explode('®',$texte);
}

/**
 * Déterminer la nature du séparateur d'un fichier CSV.
 * 
 * @param string   $ligne   la première ligne du fichier
 * @return string
 */
function extraire_separateur_csv($ligne)
{
  $tab_separateur = array( ';'=>0 , ','=>0 , ':'=>0 , "\t"=>0 );
  foreach($tab_separateur as $separateur => $occurrence)
  {
    $tab_separateur[$separateur] = mb_substr_count($ligne,$separateur);
  }
  arsort($tab_separateur);
  reset($tab_separateur);
  return key($tab_separateur);
}

/**
 * Tester si une adresse de courriel semble normale.
 * 
 * Utilisé pour une récupération via un CSV parce que pour un champ de saisie javascript fait déjà le ménage.
 * http://fr2.php.net/manual/fr/function.preg-match.php#96910
 * 
 * @param string   $courriel
 * @return bool
 */
function tester_courriel($courriel)
{
  return preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/',$courriel) ? TRUE : FALSE;
}

/**
 * Vérifier que le domaine du serveur mail peut recevoir des mails, c'est à dire qu'il a un serveur de mail déclaré dans les DNS).
 * Ça évite tous les domaines avec une coquille du genre @gmaill.com, @hoatmail.com, @gmaol.com, @laoste.net, etc.
 *
 * @param string              $mail_adresse
 * @return array(string,bool) le domaine + TRUE|FALSE
 */
function tester_domaine_courriel_valide($mail_adresse)
{
  $mail_domaine = mb_substr( $mail_adresse , mb_strpos($mail_adresse,'@')+1 );
  $is_domaine_valide = ( ( function_exists('getmxrr') && getmxrr($mail_domaine, $tab_mxhosts) ) || (@fsockopen($mail_domaine,25,$errno,$errstr,5)) ) ? TRUE : FALSE ;
  return array($mail_domaine,$is_domaine_valide);
}

/**
 * Tester si un numéro UAI est valide.
 * 
 * Utilisé pour une récupération via un CSV parce que pour un champ de saisie javascript fait déjà le ménage.
 * 
 * @param string   $uai
 * @return bool
 */
function tester_UAI($uai)
{
  // Il faut 7 chiffres suivis d'une lettre.
  if(!preg_match('#^[0-9]{7}[a-zA-Z]{1}$#',$uai))
  {
    return FALSE;
  }
  // Il faut vérifier la clef de contrôle.
  $uai_nombre = substr($uai,0,7);
  $uai_lettre = substr($uai,-1);
  $reste = $uai_nombre - (23*floor($uai_nombre/23));
  $alphabet = 'ABCDEFGHJKLMNPRSTUVWXYZ';
  $clef = substr($alphabet,$reste,1);
  return ($clef==$uai_lettre) ? TRUE : FALSE;
}

/**
 * Tester si une date est valide : format AAAA-MM-JJ par exemple.
 * 
 * Utilisé pour une récupération via un CSV parce que pour un champ de saisie javascript fait déjà le ménage.
 * 
 * @param string   $date
 * @return bool
 */
function tester_date($date)
{
  $date_unix = strtotime($date);
  return ( ($date_unix!==FALSE) && ($date_unix!==-1) ) ? TRUE : FALSE ;
}

/**
 * Renvoyer les dimensions d'une image à mettre dans les attributs HTML si on veut limiter son affichage à une largeur / hauteur données.
 * 
 * @param int   $largeur_reelle
 * @param int   $hauteur_reelle
 * @param int   $largeur_maxi
 * @param int   $hauteur_maxi
 * @return array   [$largeur_imposee,$hauteur_imposee]
 */
function dimensions_affichage_image($largeur_reelle,$hauteur_reelle,$largeur_maxi,$hauteur_maxi)
{
  if( ($largeur_reelle>$largeur_maxi) || ($hauteur_reelle>$hauteur_maxi) )
  {
    $coef_reduction_largeur = $largeur_maxi/$largeur_reelle;
    $coef_reduction_hauteur = $hauteur_maxi/$hauteur_reelle;
    $coef_reduction = min($coef_reduction_largeur,$coef_reduction_hauteur);
    $largeur_imposee = round($largeur_reelle*$coef_reduction);
    $hauteur_imposee = round($hauteur_reelle*$coef_reduction);
    return array($largeur_imposee,$hauteur_imposee);
  }
  return array($largeur_reelle,$hauteur_reelle);
}

/**
 * Renvoyer les balises images à afficher et la chaine solution à mettre en session.
 * 
 * @return array   [$html_imgs,$captcha_soluce]
 */
function captcha()
{
  $tab_base64 = array(
    0 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAI4hI+pwe3fDJxPQjStWxV0HxzdpoXliYJMSqpmC0euKM/2TcYrqpv2t3O9PrTcpWikUEpKouKJKAAAOw==',
    1 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAIzhI+pwe3fDJxPznNBtlV3DHHMEnql6YipOqKr21JgJ7OBfdv4vNZw7ssES5Qi0LhRKBMFADs=',
    2 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAI4hI+pwe3fDJxP1gRtVOA6xIDbpwUHSW3dmIrhOrlvLM9XiZJ4rqYm/APSdBxesMjyZYTKF+eJKAAAOw==',
    3 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAI3hI+pwe3fDJxPQjStWxV0HxzdpoXliYJMebHsio1tHKmZa4t1zvfkB7OZZrof8UZJDpU3hVNRAAA7',
    4 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAI2hI+pwe3fDJxPTgRtPvviCngcuH2OFiXdKY0sGLopGwYygyqmXeN63iutREMhiYJEIWG/JqAAADs=',
    5 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAI4hI+pwe3fDJxPOmUjqJjzTQUZc4QiSI7T2EFse6EnWsbmbJt1Gsv72QP9gMEhsbiS3TRLF+aZKAAAOw==',
    6 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAI5hI+pwe3fDJxPQjStU6D2bR0e+DFiVAYnpaZc5r4jGmpmjIPkjvJ37PmsbpdEEWbrsSjJJY0DPRQAADs=',
    7 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAI2hI+pwe3fDJxPRnWcZThfegGaCAYeR1InWq5mCrkvKG+V/U3yrONs7PvdYKMeMVcCJo2dJqAAADs=',
    8 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAI4hI+pwe3fDJxPQjStWxV0HxzdpoXliYJMaYIieSVxjI2ky6Y3HOn9t8qpejXeZ/ijKFnKm+KZKAAAOw==',
    9 => 'R0lGODlhFAAUAIAAAGZm/93d/yH5BAEKAAAALAAAAAAUABQAAAI4hI+pwe3fDJxPQjStWxV0HxzdpoXliYJMSqrmt4qRaypuOSckle/9H4PVRh+Zr5ih8Y7Lou1pKAAAOw==',
  );
  $rand_keys   = str_shuffle('0123456789');
  $rand_values = str_shuffle('abcdefghijklmnopqrstuvxyz');
  $tab_values = array();
  $html_imgs = '';
  for( $i=1 ; $i<=6 ; $i++ )
  {
    $key   = $rand_keys{$i};
    $value = $rand_values{$i};
    $tab_values[$key] = $value;
    $html_imgs .= '<img class="captcha" id="cap_'.$value.'" src="data:image/gif;base64,'.$tab_base64[$key].'" />';
  }
  ksort($tab_values);
  $captcha_soluce = implode('',$tab_values);
  return array($html_imgs,$captcha_soluce);
}
/**
 * Passer d'une date MySQL AAAA-MM-JJ à une date française JJ/MM/AAAA.
 *
 * @param string $date_mysql AAAA-MM-JJ
 * @return string|NULL       JJ/MM/AAAA
 */
function convert_date_mysql_to_french($date_mysql)
{
  if($date_mysql===NULL) return '00/00/0000';
  list($annee,$mois,$jour) = explode('-',$date_mysql);
  return $jour.'/'.$mois.'/'.$annee;
}

/**
 * Passer d'une date MySQL AAAA-MM-JJ HH:MM:SS à une date française JJ/MM/AAAA HH:MM.
 *
 * @param string $datetime_mysql AAAA-MM-JJ HH:MM:SS
 * @return string                JJ/MM/AAAA HHhMMmin
 */
function convert_datetime_mysql_to_french($datetime_mysql)
{
  list( $partie_jour , $partie_heure ) = explode( ' ' , $datetime_mysql);
  list( $annee , $mois , $jour       ) = explode( '-' , $partie_jour);
  list( $heure , $minute , $seconde  ) = explode( ':' , $partie_heure);
  return $jour.'/'.$mois.'/'.$annee.' '.$heure.'h'.$minute.'min';
}

/**
 * Passer d'une date française JJ/MM/AAAA à une date MySQL AAAA-MM-JJ.
 *
 * @param string $date_fr JJ/MM/AAAA
 * @return string|NULL    AAAA-MM-JJ
 */
function convert_date_french_to_mysql($date_fr)
{
  if($date_fr=='00/00/0000') return NULL;
  list($jour,$mois,$annee) = explode('/',$date_fr);
  return $annee.'-'.$mois.'-'.$jour;
}

/**
 * Passer d'une date française JJ/MM/AAAA à une date J mois AAAA.
 *
 * @param string $date_fr JJ/MM/AAAA
 * @return string         J mois AAAA
 */
function convert_date_french_to_texte($date_fr)
{
  $tab_mois = array('01'=>'janvier','02'=>'février','03'=>'mars','04'=>'avril','05'=>'mai','06'=>'juin','07'=>'juillet','08'=>'août','09'=>'septembre','10'=>'octobre','11'=>'novembre','12'=>'décembre');
  if($date_fr=='00/00/0000') return '';
  list($jour,$mois,$annee) = explode('/',$date_fr);
  return intval($jour).' '.$tab_mois[$mois].' '.$annee;
}

/**
 * Passer d'une date française JJ/MM/AAAA à l'âge X ans et Y mois.
 *
 * Il existe des fonctions utilisables qu'à partir de PHP 5.2 ou 5.3.
 * @see http://fr.php.net/manual/fr/class.datetime.php
 * Ceci dit, le code élaboré est assez simple.
 *
 * @param string $date_fr JJ/MM/AAAA
 * @return string         X ans et Y mois
 */
function texte_age($date_fr)
{
  list($jour_birth,$mois_birth,$annee_birth) = explode('/',$date_fr);
  list($jour_today,$mois_today,$annee_today) = explode('/',TODAY_FR);
  $nb_annees = $annee_today - $annee_birth;
  $nb_mois   = $mois_today - $mois_birth;
  if( ($mois_birth>$mois_today) || (($mois_birth==$mois_today)&&($jour_birth>$jour_today)) )
  {
    $nb_annees-=1;
    $nb_mois+=12;
  }
  if($jour_birth>$jour_today)
  {
    $nb_mois-=1;
  }
  $s_annee = ($nb_annees>1) ? 's' : '' ;
  $aff_mois = ($nb_mois) ? ' et '.$nb_mois.' mois' : '' ;
  return $nb_annees.' an'.$s_annee.$aff_mois;
}

/**
 * Passer d'une date française JJ/MM/AAAA à l'affichage de la date de naissance et de l'âge.
 *
 * @param string $date_fr JJ/MM/AAAA
 * @return string
 */
function texte_ligne_naissance($date_fr)
{
  return 'Âge : '.texte_age($date_fr).' ('.convert_date_french_to_texte($date_fr).').';
}

/**
 * Renvoyer le 1er jour de l'année scolaire en cours, au format français JJ/MM/AAAA ou MySQL AAAA-MM-JJ.
 *
 * @param string $format           'mysql'|'french'
 * @param int    $annee_decalage   facultatif, pour les années scolaires précédentes ou suivantes
 * @return string
 */
function jour_debut_annee_scolaire($format,$annee_decalage=0)
{
  $jour  = '01';
  $mois  = sprintf("%02u",$_SESSION['MOIS_BASCULE_ANNEE_SCOLAIRE']);
  $annee = (date('n')<$_SESSION['MOIS_BASCULE_ANNEE_SCOLAIRE']) ? date('Y')+$annee_decalage-1 : date('Y')+$annee_decalage ;
  return ($format=='mysql') ? $annee.'-'.$mois.'-'.$jour : $jour.'/'.$mois.'/'.$annee ;
}

/**
 * Renvoyer le dernier jour de l'année scolaire en cours, au format français JJ/MM/AAAA ou MySQL AAAA-MM-JJ.
 * En fait, c'est plus exactement le 1er jour de l'année scolaire suivante...
 *
 * @param string $format           'mysql'|'french'
 * @param int    $annee_decalage   facultatif, pour les années scolaires précédentes ou suivantes
 * @return string
 */
function jour_fin_annee_scolaire($format,$annee_decalage=0)
{
  $jour  = '01';
  $mois  = sprintf("%02u",$_SESSION['MOIS_BASCULE_ANNEE_SCOLAIRE']);
  $annee = (date('n')<$_SESSION['MOIS_BASCULE_ANNEE_SCOLAIRE']) ? date('Y')+$annee_decalage : date('Y')+$annee_decalage+1 ;
  return ($format=='mysql') ? $annee.'-'.$mois.'-'.$jour : $jour.'/'.$mois.'/'.$annee ;
}

/**
 * Renvoyer l'année de session du DNB.
 *
 * @param void
 * @return string
 */
function annee_session_brevet()
{
  $mois_actuel    = date('n');
  $annee_actuelle = date('Y');
  $mois_bascule   = $_SESSION['MOIS_BASCULE_ANNEE_SCOLAIRE'];
  if($mois_bascule==1)
  {
    return $annee_actuelle;
  }
  else if($mois_actuel < $mois_bascule)
  {
    return $annee_actuelle;
  }
  else
  {
    return $annee_actuelle+1;
  }
}

/**
 * Tester si on est dans la période de rentrée
 * Par défaut : 01/08 -> 30/09
 *
 * @param void
 * @return bool
 */
function test_periode_rentree()
{
  $mois_actuel    = date('n');
  $mois_bascule   = $_SESSION['MOIS_BASCULE_ANNEE_SCOLAIRE'] ; // par défaut août
  $mois_rentree   = ($mois_bascule<12) ? $mois_bascule+1 : 1 ; // par défaut septembre
  return ($mois_actuel==$mois_bascule) || ($mois_actuel==$mois_rentree) ;
}

/**
 * Tester si on est dans la période de sortie
 * Par défaut : 01/06 -> 31/07
 *
 * @param void
 * @return bool
 */
function test_periode_sortie()
{
  $mois_actuel    = date('n');
  $mois_bascule   = $_SESSION['MOIS_BASCULE_ANNEE_SCOLAIRE'] ; // par défaut août
  $mois_sortie    = ($mois_bascule>1) ? $mois_bascule-1 : 12 ; // par défaut juillet
  $mois_fin_annee = ($mois_sortie>1)  ? $mois_sortie-1  : 12 ; // par défaut juin
  return ($mois_actuel==$mois_sortie) || ($mois_actuel==$mois_fin_annee) ;
}

/**
 * Renvoyer une taille de fichier lisible pour un humain :)
 * @see http://fr2.php.net/manual/fr/function.filesize.php#106569
 *
 * @param int $bytes
 * @param int $decimals (facultatif)
 * @return string
 */
function afficher_fichier_taille($bytes, $decimals = 1)
{
  $size_unit = ' KMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return round( $bytes / pow(1024,$factor) , $decimals ) . $size_unit[$factor].'o';
}

/**
 * Tester si un droit d'accès spécifique comporte une restriction aux PP ou aux coordonnateurs
 *
 * @param string $listing_droits_sigles
 * @param string $restriction   'ONLY_PP' | 'ONLY_COORD' | 'ONLY_LV'
 * @return bool
 */
function test_droit_specifique_restreint($listing_droits_sigles,$restriction)
{
  return (strpos($listing_droits_sigles,$restriction)!==FALSE);
}

/**
 * Tester si on a un droit d'accès spécifique
 *
 * @param string $listing_droits_sigles
 * @param int    $matiere_coord_or_groupe_pp_connu   si le droit comporte une restriction aux coordonnateurs matières | professeurs principaux, on peut déja connaitre et transmettre l'info (soit pour au moins une matière | classe, soit pour une matière | classe donnée)
 * @param int    $matiere_id_or_groupe_id_a_tester   si le droit comporte une restriction aux coordonnateurs matières | professeurs principaux, et si $matiere_coord_or_groupe_pp_connu n'est pas transmis, on peut chercher si le droit est bon soit pour une matière | classe donnée, soit pour au moins une matière | classe
 * @param string $forcer_profil_sigle                pour forcer à tester un profil donné au lieu du profil de l'utilisateur
 * @param string $forcer_profil_type                pour forcer à tester un profil donné au lieu du profil de l'utilisateur
 * @return bool
 */
function test_user_droit_specifique( $listing_droits_sigles , $matiere_coord_or_groupe_pp_connu=NULL , $matiere_id_or_groupe_id_a_tester=0 , $forcer_profil_sigle=NULL , $forcer_profil_type=NULL )
{
  $user_profil_sigle = (!$forcer_profil_sigle) ? $_SESSION['USER_PROFIL_SIGLE'] : $forcer_profil_sigle ;
  $user_profil_type  = (!$forcer_profil_type)  ? $_SESSION['USER_PROFIL_TYPE']  : $forcer_profil_type  ;
  $tableau_droits_sigles = explode(',',$listing_droits_sigles);
  $test_droit = in_array($user_profil_sigle,$tableau_droits_sigles);
  if( $test_droit && ($user_profil_type=='professeur') && ($_SESSION['USER_JOIN_GROUPES']=='config') && test_droit_specifique_restreint($listing_droits_sigles,'ONLY_PP') )
  {
    return ($matiere_coord_or_groupe_pp_connu!==NULL) ? (bool)$matiere_coord_or_groupe_pp_connu : DB_STRUCTURE_PROFESSEUR::DB_tester_prof_principal($_SESSION['USER_ID'],$matiere_id_or_groupe_id_a_tester) ;
  }
  if( $test_droit && ($user_profil_type=='professeur') && ($_SESSION['USER_JOIN_MATIERES']=='config') && test_droit_specifique_restreint($listing_droits_sigles,'ONLY_COORD') )
  {
    return ($matiere_coord_or_groupe_pp_connu!==NULL) ? (bool)$matiere_coord_or_groupe_pp_connu : DB_STRUCTURE_PROFESSEUR::tester_prof_coordonnateur($_SESSION['USER_ID'],$matiere_id_or_groupe_id_a_tester) ;
  }
  if( $test_droit && ($user_profil_type=='professeur') && ($_SESSION['USER_JOIN_MATIERES']=='config') && test_droit_specifique_restreint($listing_droits_sigles,'ONLY_LV') )
  {
    return DB_STRUCTURE_PROFESSEUR::tester_prof_langue_vivante($_SESSION['USER_ID']) ;
  }
  return $test_droit;
}

/**
 * Tester si on a un droit d'accès spécifique
 *
 * @param string $listing_droits_sigles
 * @param string $format   "li" | "br"
 * @return bool
 */
function afficher_profils_droit_specifique($listing_droits_sigles,$format)
{
  $tab_profils = array();
  $texte_testriction_pp    = test_droit_specifique_restreint($listing_droits_sigles,'ONLY_PP')    ? ' restreint aux professeurs principaux'  : '' ;
  $texte_testriction_coord = test_droit_specifique_restreint($listing_droits_sigles,'ONLY_COORD') ? ' restreint aux coordonnateurs matières' : '' ;
  $texte_testriction_lv    = test_droit_specifique_restreint($listing_droits_sigles,'ONLY_LV')    ? ' restreint aux professeurs de LV'       : '' ;
  $tableau_droits_sigles = explode(',',$listing_droits_sigles);
  foreach($tableau_droits_sigles as $droit_sigle)
  {
    if(isset($_SESSION['TAB_PROFILS_DROIT']['TYPE'][$droit_sigle]))
    {
      $profil_nom  = $_SESSION['TAB_PROFILS_DROIT']['NOM_LONG_PLURIEL'][$droit_sigle];
      $profil_nom .= ( ($_SESSION['TAB_PROFILS_DROIT']['TYPE'][$droit_sigle]=='professeur') && ($_SESSION['TAB_PROFILS_DROIT']['JOIN_GROUPES'][$droit_sigle]=='config')  ) ? $texte_testriction_pp    : '' ;
      $profil_nom .= ( ($_SESSION['TAB_PROFILS_DROIT']['TYPE'][$droit_sigle]=='professeur') && ($_SESSION['TAB_PROFILS_DROIT']['JOIN_MATIERES'][$droit_sigle]=='config') ) ? $texte_testriction_coord : '' ;
      $profil_nom .= ( ($_SESSION['TAB_PROFILS_DROIT']['TYPE'][$droit_sigle]=='professeur') && ($_SESSION['TAB_PROFILS_DROIT']['JOIN_MATIERES'][$droit_sigle]=='config') ) ? $texte_testriction_lv    : '' ;
      $tab_profils[] = $profil_nom;
    }
  }
  if(!count($tab_profils))
  {
    $tab_profils[] = 'aucun !';
  }
  return ($format=='li') ? '<ul class="puce">'.NL.'<li>'.implode('</li>'.NL.'<li>',$tab_profils).'</li>'.NL.'</ul>'.NL : implode('<br />',$tab_profils) ;
}

/**
 * Afficher un nom suivi d'un prénom (ou le contraire) dont l'un ou les deux sont éventuellement remplacés par leur initiale.
 * En cas de civilité présente, la deuxième partie sera retirée si seule son initiale est demandée.
 *
 * @param string $partie1
 * @param bool   $is_initiale1
 * @param string $partie2
 * @param bool   $is_initiale2
 * @param string $genre   (facultatif)
 * @return string
 */
function afficher_identite_initiale( $partie1 , $is_initiale1 , $partie2 , $is_initiale2 , $genre='I' )
{
  $tab_civilite = array( 'M'=>'M.' , 'F'=>'Mme' );
  $civilite = ($genre!='I') ? $tab_civilite[$genre] : '' ;
  $partie1 = ( $is_initiale1 && strlen($partie1) ) ? $partie1{0}.'.' : $partie1 ;
  $partie2 = ( $is_initiale2 && strlen($partie2) ) ? $partie2{0}.'.' : $partie2 ;
  return ($civilite && $is_initiale2) ? trim($civilite.' '.$partie1) : trim($civilite.' '.$partie1.' '.$partie2) ;
}

/**
 * Afficher un texte tronqué au dela d'un certain nombre de caractères
 *
 * @param string $texte
 * @param int    $longueur_max
 * @return string
 */
function afficher_texte_tronque( $texte , $longueur_max )
{
  if( mb_strlen($texte) < $longueur_max )
  {
    return $texte;
  }
  $pos_espace = mb_strpos( $texte , ' ' , $longueur_max-10 );
  $chaine_de_fin = ' [...]';
  if($pos_espace!==FALSE)
  {
    return mb_substr( $texte , 0 , $pos_espace ).$chaine_de_fin;
  }
  return mb_substr( $texte , 0 , $longueur_max-5 ).$chaine_de_fin;
}

/**
 * Formater les liens selon un code perso
 *
 * En attendant un éventuel textarea enrichi pour la saisie des messages (mais est-ce que ça ne risquerait pas de faire une page d'accueil folklorique ?), une petite fonction pour fabriquer des liens...
 * Format attendu : [desciptif|adresse|target]
 *
 * @param string $texte
 * @param int    $longueur_max
 * @param string $contexte   html | mail
 * @return string
 */
function make_lien($texte,$contexte)
{
  $masque_recherche = '#\[([^\|]+)\|([^\|]+)\|([^\|]*)\]#' ;
  $masque_remplacement = ($contexte=='html') ? '<a href="$2" target="$3">$1</a>' : '$1 [$2]' ;
  return str_replace( 'target=""' , '' , preg_replace( $masque_recherche , $masque_remplacement , $texte ) );
}

?>