<?php
/** Outils compl�mentaires de gestion des AID
 * 
 * $Id: share-aid.inc.php 7692 2011-08-11 00:26:10Z regis $
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Aid
 * @subpackage Initialisation
 *
*/


/**
 * Fonction v�rifiant les droits d'acc�s au module selon l'identifiant
 *
 * $champ : si non vide, on v�rifie le droit sur ce champ en particulier, si $champ='', on v�rifie le droit de modifier la fiche projet
 * 
 * Cas particulier : $champ = 'eleves_profs'
 * Cette valeur permet de g�rer le fait que n'apparaissent pas sur les fiches publiques :
 * - Les el�ves responsables du projet,
 * - les professeurs responsables du projet,
 * - les �l�ves faisant partie du projet.
 * 
 * $_login : identifiant de la personne pour laquelle on v�rifie les droits, 
 * si le login n'est pas pr�cis�, on est dans l'interface publique
 * 
 * $mode : utilis� uniquement si $champ est non vide
 * - $mode = W -> l'utilisateur a-t-il acc�s en �criture ?
 * - Autres valeurs de W -> l'utilisateur a-t-il acc�s en lecture ?
 * 
 * @param type $_login Login � v�rifier
 * @param type $aid_id identifiant de l'AID
 * @param type $indice_aid identifiant de la cat�gorie d'AID
 * @param type $champ champ � v�rifier
 * @param type $mode mode recherch�
 * @param type $annee
 * @return type 
 */
function VerifAccesFicheProjet($_login,$aid_id,$indice_aid,$champ,$mode,$annee='') {
 //$annee='' signifie qu'il s'agit de l'ann�e courante
 if ($annee=='') {
    // Les outils compl�metaires sont-ils activ�s ?
    $test_active = sql_query1("select indice_aid from aid_config WHERE outils_complementaires = 'y' and indice_aid='".$indice_aid."'");
    // Les outils compl�menatires ne sont activ�s pour aucune AID, on renvoie FALSE
    if ($test_active == -1) {
        return FALSE;
        die();
    }

    // Si le champ n'est pas activ�, on ne l'affiche pas !
    // Deux valeurs possibles :
    // 0 -> le champ n'est pas utilis�
    // 1 -> Le champ est utilis�
    if ($champ != "") {
        $statut_champ = sql_query1("select statut from droits_aid where id = '".$champ."'");
        if ($statut_champ == 0) {
            return FALSE;
            die();
        }
    }

    // Dans la suite,
    // Les outils compl�mentaires sont activ�s

    if ($_login!='') {
        $statut_login = sql_query1("select statut from utilisateurs where login='".$_login."' and etat='actif' ");
    } else {
        // si le login n'est pas pr�cis�, on est dans l'interface publique
        $statut_login = "public";
    }
    // Admin ?
    if  ($statut_login == "administrateur") {
        return TRUE;
        die();
    }

    // S'agit-il d'un super gestionnaire ?
    $test_super_gestionnaire = sql_query1("select count(id_utilisateur) from j_aidcateg_super_gestionnaires where indice_aid='".$indice_aid."' and id_utilisateur='".$_login."'");
    if  ($test_super_gestionnaire != "0") {
        return TRUE;
        die();
    }

    // S'agit-il d'un utilisateurs ayant des droits sur l'ensemble des AID de la cat�gorie
    $test_droits_special = sql_query1("select count(id_utilisateur) from j_aidcateg_utilisateurs where indice_aid='".$indice_aid."' and id_utilisateur='".$_login."'");
    // Cas d'un �l�ve
    if (($statut_login=="eleve")) {
        // s'il s'agit d'un �l�ve, les �l�ves ont-ils acc�s en modification ?
        // Si l'utilisateur a des droits sp�ciaux, il peut modifier
        $CheckAccessEleve = sql_query1("select eleve_peut_modifier from aid where id = '".$aid_id."' and indice_aid = '".$indice_aid."'");
        if ($CheckAccessEleve != "y") {
            if ($champ == "") {return FALSE; die();}
        }
        // L'�l�ve est-il responsable de cet AID ?
        $CheckAccessEleve2 = sql_query1("select count(login) from j_aid_eleves_resp WHERE (login='".$_SESSION['login']."' and indice_aid='".$indice_aid."' and id_aid='".$aid_id."')");
        if ($CheckAccessEleve2 == 0) {
             if ($champ == "") {return FALSE; die();}
        }
    }
    // Cas d'un professeur
    if (($statut_login=="professeur")) {

        // s'il s'agit d'un prof, les profs ont-ils acc�s en modification ?
        $CheckAccessProf = sql_query1("select prof_peut_modifier from aid where id = '".$aid_id."' and indice_aid = '".$indice_aid."'");
        if (($CheckAccessProf != "y") and ($test_droits_special==0) ) {
            if ($champ == "") {return FALSE; die();}
        }

        // Le profeseur est-il responsable de cet AID ?
        $CheckAccessProf2 = sql_query1("select count(id_utilisateur) from j_aid_utilisateurs WHERE (id_utilisateur='".$_SESSION['login']."' and indice_aid='".$indice_aid."' and id_aid='".$aid_id."')");
        if (($CheckAccessProf2 == 0) and ($test_droits_special==0) ) {
            if ($champ == "") {return FALSE; die();}
        }
    }
    // Cas d'un CPE
    if (($statut_login=="cpe")) {
        // s'il s'agit d'un CPE, les cpe ont-ils acc�s en modification ?
        // Si l'utilisateur a des droits sp�ciaux, il peut modifier
        $CheckAccessCPE = sql_query1("select cpe_peut_modifier from aid where id = '".$aid_id."' and indice_aid = '".$indice_aid."'");
        if (($CheckAccessCPE != "y") and ($test_droits_special==0)) {
            if ($champ == "") {return FALSE; die();}
        }
    }
    // S'il s'agit d'un responsable, de la scolarit� ou de secours, pas d'acc�s
    if (($statut_login=="responsable") or ($statut_login=="scolarite") or ($statut_login=="secours")) {
        return FALSE;
        die();
    }
    // Si le champ n'est pas pr�cis�, c'est termin�
    // Si le champ est pr�cis�, on regarde si l'utilisateur a les droits de modif de ce champ
    //
    if ($champ == "") {
        // Si $champ == "", cela signifie qu'on demande l'acc�s � une page priv�e de modif ou de visualisation
        if ($_login !="")
            return TRUE;
        else
            return FALSE;
    } else {
        // Le champ est pr�cis�. On cherche � savoir si l'utilisateur a le droit de voir et/ou de modifier ce champ
        $CheckAccess = sql_query1("select ".$statut_login." from droits_aid where id = '".$champ."'");
        // $CheckAccess='V' -> possibilit� de modifier et de voir le champ
        // $CheckAccess='F' -> possibilit� de voir le champ mais pas de le modifier
        // $CheckAccess='-' -> Interdiction de voir et ou de modifier le champ
        if (($mode != 'W') and ($CheckAccess != '-'))
            return (TRUE);
        else if (($mode == 'W') and ($CheckAccess == 'V'))
            return (TRUE);
        else
            return (FALSE);

    }
  } else {
  // il s'agit de projets d'une ann�e pass�e...
    // Les outils compl�metaires sont-ils activ�s ?
    $test_active = sql_query1("select id from archivage_types_aid WHERE outils_complementaires = 'y' and id='".$indice_aid."'");
    // Les outils compl�menatires ne sont activ�s pour aucune AID, on renvoie FALSE
    if ($test_active == -1) {
        return FALSE;
        die();
    }

    if ($_login!='') {
        $statut_login = sql_query1("select statut from utilisateurs where login='".$_login."' and etat='actif' ");
    } else {
        // si le login n'est pas pr�cis�, on est dans l'interface publique
        $statut_login = "public";
    }

    if ($champ == 'eleves_profs') {
    # Cas particulier du champ eleves_profs : ce champ permet de g�rer le fait que n'apparaissent pas sur les fiches publiques :
    # Les el�ves responsables du projet,
    # les professeurs responsables du projet,
    # les �l�ves faisant partie du projet.
        if ($statut_login == "public")
            return FALSE;
        else
            return TRUE;
    } else if ($champ != "") {
    // Si le champ n'est pas activ�, on ne l'affiche pas !
    // Deux valeurs possibles :
    // 0 -> le champ n'est pas utilis�
    // 1 -> Le champ est utilis�

        $statut_champ = sql_query1("select statut from droits_aid where id = '".$champ."'");
        if ($statut_champ == 0) {
            return FALSE;
            die();
        }
    }
    // Admin ?
    if  ($statut_login == "administrateur") {
        return TRUE;
        die();
    }
    if ($champ == "") {
    // Si $champ == "", cela signifie qu'on demande l'acc�s � une page priv�e de modif ou de visualisation
       return FALSE;
    // Si le champ est pr�cis�, on regarde si l'utilisateur a les droits de modif de ce champ
    } else {
        // Le champ est pr�cis�. On cherche � savoir si l'utilisateur a le droit de voir et/ou de modifier ce champ
        $CheckAccess = sql_query1("select ".$statut_login." from droits_aid where id = '".$champ."'");
        // $CheckAccess='V' -> possibilit� de modifier et de voir le champ
        // $CheckAccess='F' -> possibilit� de voir le champ mais pas de le modifier
        // $CheckAccess='-' -> Interdiction de voir et ou de modifier le champ
        if (($mode != 'W') and ($CheckAccess != '-'))
            return (TRUE);
        else if (($mode == 'W') and ($CheckAccess == 'V'))
            return (TRUE);
        else
            return (FALSE);
    }
  }
}

/**
 * v�rifie si un Aid est actif
 * 
 * @param int $indice_aid Indice de l'aid
 * @param string $aid_id Id de l'aid
 * @param string $annee l'ann�e de recherche (ann�e courante si vide)
 * @return boolean 
 */
function VerifAidIsAcive($indice_aid,$aid_id,$annee='') {
    if ($annee=='')
      $test_active = sql_query1("SELECT indice_aid FROM aid_config WHERE outils_complementaires = 'y' and indice_aid='".$indice_aid."'");
    else
      $test_active = sql_query1("SELECT id FROM archivage_types_aid WHERE outils_complementaires = 'y' and id='".$indice_aid."'");
    if ($test_active == -1)
       return FALSE;
    else {
       if ($aid_id != "") {
         if ($annee=='')
           $test_aid_existe = sql_query1("select count(id) from aid WHERE indice_aid='".$indice_aid."' and id='".$aid_id."'");
        else
           $test_aid_existe = sql_query1("select count(id) from archivage_aids WHERE id_type_aid='".$indice_aid."' and id='".$aid_id."'");
        if ($test_aid_existe != 1)
           return FALSE;
        else
           return TRUE;
       } else
           return TRUE;

    }
}

/**
 * renvoie le libell� du champ
 * 
 * @param string $champ Id du champ � tester
 * @return string Le libell�
 */
function LibelleChampAid($champ) {
    $nom = sql_query1("select description from droits_aid where id = '".$champ."'");
    return $nom;
}

/**
 * Calcule le niveau de gestion des AIDs
 * 
 * - 0 : aucun droit
 * - 1 : peut uniquement ajouter / supprimer des �l�ves
 * - 2 : (pas encore impl�menter) peut uniquement ajouter / supprimer des �l�ves et des professeurs responsables
 * - 3 : ...
 * - 10 : Peut tout faire
 *
 * @param string $_login Login de l'utilisateur
 * @param int $indice_aid Indice de l'aid
 * @param string $aid_id Id de l'aid
 * @return int le niveau de gestion
 */
function NiveauGestionAid($_login,$_indice_aid,$_id_aid="") {
    if ($_SESSION['statut'] == "administrateur") {
        return 10;
        die();
    }
    if (getSettingValue("active_mod_gest_aid")=="y") {
      // l'id de l'aid n'est pas d�fini : on regarde si l'utilisateur est gestionnaire d'au moins une aid dans la cat�gorie
      if ($_id_aid == "") {
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."')");
        $test2 = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."')");
        if ($test2 >= 1) {
            return 5;
        } else if ($test1 >= 1) {
            return 1;
        } else
          return 0;
      } else {
      // l'id de l'aid est d�fini : on regarde si l'utilisateur est gestionnaire de cette aid
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."' and id_aid = '".$_id_aid."')");
        $test2 = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_indice_aid."')");
        if ($test2 >= 1) {
            return 5;
        } else if ($test1 >= 1) {
            return 1;
        } else
          return 0;
      }
    } else
      return 0;
}

/**
 * V�rifie si un utilisateurs � des droits de suppression sur un Aid
 * 
 * @param string $_login Login de l'utilisateur
 * @param string $_action Action � tester
 * @param string $_cible1 Non utilis� mais obligatoire
 * @param int $_cible2 id_aid
 * @param int $_cible3 indice_aid
 * @return bool TRUE si l'utilisateur a les droits
 * @see getSettingValue()
 */
function PeutEffectuerActionSuppression($_login,$_action,$_cible1,$_cible2,$_cible3) {
    if ($_SESSION['statut'] == "administrateur") {
        return TRUE;
        die();
    }
    if (getSettingValue("active_mod_gest_aid")=="y") {
      if (($_action=="del_eleve_aid") or ($_action=="del_prof_aid") or ($_action=="del_aid")) {
      // on regarde si l'utilisateur est gestionnaire de l'aid
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_cible3."' and id_aid = '".$_cible2."')");
        $test2 = sql_query1("SELECT count(id_utilisateur) FROM j_aidcateg_super_gestionnaires WHERE (id_utilisateur = '" . $_login . "' and indice_aid = '".$_cible3."')");
        $test = max($test1,$test2);
        if ($test >= 1) {
            return TRUE;
        } else {
            return FALSE;
        }
      }
    } else
    return FALSE;
}




?>
