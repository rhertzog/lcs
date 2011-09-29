<?php
/**
 *
 * @version $Id: enregistrement_modif_saisie.php 7437 2011-07-18 19:20:27Z dblanqui $
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Initialisation des feuilles de style apr�s modification pour am�liorer l'accessibilit�
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On v�rifie si le module est activ�
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activ�.");
}

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activ�.");
}

//r�cup�ration des param�tres de la requ�te
$id_saisie = isset($_POST["id_saisie"]) ? $_POST["id_saisie"] :(isset($_GET["id_saisie"]) ? $_GET["id_saisie"] :NULL);
$date_debut = isset($_POST["date_debut"]) ? $_POST["date_debut"] :(isset($_GET["date_debut"]) ? $_GET["date_debut"] :NULL);
$date_fin = isset($_POST["date_fin"]) ? $_POST["date_fin"] :(isset($_GET["date_fin"]) ? $_GET["date_fin"] :NULL);
$commentaire = isset($_POST["commentaire"]) ? $_POST["commentaire"] :(isset($_GET["commentaire"]) ? $_GET["commentaire"] :NULL);
$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : Null);

$message_enregistrement = '';
$saisie = AbsenceEleveSaisieQuery::create()->includeDeleted()->findPk($id_saisie);
if ($saisie == null) {
    $message_enregistrement .= 'Modification impossible : saisie non trouv�e.';
    include("visu_saisie.php");
    die();
}
//on charge les traitements
$saisie->getAbsenceEleveTraitements();

if ( isset($_POST["creation_traitement"])) {
	//on charge les traitements
	$saisie->getAbsenceEleveTraitements();

	$traitement = new AbsenceEleveTraitement();
    $traitement->setUtilisateurProfessionnel($utilisateur);
    $traitement->addAbsenceEleveSaisie($saisie);
    $traitement->save();
    header("Location: ./visu_traitement.php?id_traitement=".$traitement->getId().'&menu='.$menu);
    die();
} elseif ( isset($_POST["modifier_type"])) {
    $message_enregistrement .= modif_type($saisie, $utilisateur);
    if ($message_enregistrement == '') {
	$message_enregistrement = 'Modification du type enregistr�e';
    }
    include("visu_saisie.php");
    die();
} elseif (isset($_GET["version"])) {
	if ($utilisateur->getStatut() != 'cpe' && $utilisateur->getStatut() != 'scolarite') {
	    $message_enregistrement .= 'Modification non autoris�e.';
	    include("visu_saisie.php");
	    die();
	}
        if ($saisie->getDeletedAt() != null) {
        $message_enregistrement .= 'Cette saisie est supprim�e. Vous devez la restaurer pour la modifier.';
        include("visu_saisie.php");
        die();
        }
	$saisie->toVersion($_GET["version"]);
	if ($saisie->isDeleted()) {
		$saisie->unDelete();
	} else {
		AbsenceEleveSaisiePeer::disableVersioning();
		$saisie->save();
		AbsenceEleveSaisiePeer::enableVersioning();
	}
	include("visu_saisie.php");
    die();
} elseif (isset($_POST["action"])) {
	if ($utilisateur->getStatut() == 'cpe' || $utilisateur->getStatut() == 'scolarite'
		|| ($utilisateur->getStatut() == 'professeur' && $saisie->getUtilisateurId() == $utilisateur->getPrimaryKey()) ) {
			//ok
	} else {
	    $message_enregistrement .= 'Modification non autoris�e.';
	    include("visu_saisie.php");
	    die();
	}
		
	if ($_POST["action"] == 'suppression') {
        
		$saisie->delete();
	} else if ($_POST["action"] == 'restauration') {
		$saisie->unDelete();
	}
	include("visu_saisie.php");
    die();
}


//la saisie est-elle modifiable ?
//Une saisie est modifiable ssi : elle appartient � l'utilisateur de la session si c'est un prof,
//elle date de moins d'une heure et l'option a ete coch� partie admin
if ($utilisateur->getStatut() == 'professeur') {
	if (!getSettingValue("abs2_modification_saisie_une_heure")=='y' || $saisie->getUtilisateurId() != $utilisateur->getPrimaryKey() || $saisie->getVersionCreatedAt('U') < (time() - 3600)) {
	    $message_enregistrement .= 'Modification non autoris�e.';
	    include("visu_saisie.php");
	    die();	
	}
} else {
	if ($utilisateur->getStatut() != 'cpe' && $utilisateur->getStatut() != 'scolarite') {
	    $message_enregistrement .= 'Modification non autoris�e.';
	    include("visu_saisie.php");
	    die();
	}
}
$saisie->setVersionCreatedBy($utilisateur->getLogin());


$saisie->setCommentaire($commentaire);
$date_debut = new DateTime(str_replace("/",".",$_POST['date_debut']));
$heure_debut = new DateTime($_POST['heure_debut']);
$date_debut->setTime($heure_debut->format('H'), $heure_debut->format('i'));
$jours_actuel = date('d/m/Y');
if ($utilisateur->getStatut() == 'professeur') {
    if (getSettingValue("abs2_saisie_prof_decale") != 'y') {
	if ($date_debut->format('d/m/Y') != $jours_actuel) {
	    $message_enregistrement .= "Saisie d'une date differente de la date courante non autoris�e.<br/>";
	    include("visu_saisie.php");
	    die();
	}
    }
    if (getSettingValue("abs2_saisie_prof_decale_journee") !='y' && getSettingValue("abs2_saisie_prof_decale") != 'y') {
       if ($saisie->getEdtCreneau() == null || $saisie->getEdtCreneau()->getHeuredebutDefiniePeriode('Hi') > $date_debut->format('Hi')) {
	    $message_enregistrement .= "Saisie hors creneau actuel non autoris�e.<br/>";
	    include("visu_saisie.php");
	    die();
       }
    }
}
$saisie->setDebutAbs($date_debut);

$date_fin = new DateTime(str_replace("/",".",$_POST['date_fin']));
$heure_fin = new DateTime($_POST['heure_fin']);
$date_fin->setTime($heure_fin->format('H'), $heure_fin->format('i'));
if ($utilisateur->getStatut() == 'professeur') {
    if (getSettingValue("abs2_saisie_prof_decale") != 'y') {
	if ($date_fin->format('d/m/Y') != $jours_actuel) {
	    $message_enregistrement .= "Saisie d'une date differente de la date courante non autoris�e.<br/>";
	    include("visu_saisie.php");
	    die();
	}
    }
    if (getSettingValue("abs2_saisie_prof_decale_journee") !='y' && getSettingValue("abs2_saisie_prof_decale") != 'y') {
       if ($saisie->getEdtCreneau() == null || $saisie->getEdtCreneau()->getHeurefinDefiniePeriode('Hi') < $date_fin->format('Hi')) {
	    $message_enregistrement .= "Saisie hors creneau actuel non autoris�e.<br/>";
	    include("visu_saisie.php");
	    die();
       }
    }
}
$saisie->setFinAbs($date_fin);

modif_type($saisie, $utilisateur);

if ($saisie->validate()) {
    $saisie->save();
    $message_enregistrement .= 'Modification enregistr�e';
    if ($saisie->getEleve() != null) {
    	$saisie->getEleve()->clearAbsenceEleveSaisiesParJour();
    	$saisie->getEleve()->clearAbsenceEleveSaisies();
    }
} else {
    $no_br = true;
    foreach ($saisie->getValidationFailures() as $erreurs) {
	$message_enregistrement .= $erreurs;
	if ($no_br) {
	    $no_br = false;
	} else {
	    $message_enregistrement .= '<br/>';
	}
    }
    $saisie->reload();
}

include("visu_saisie.php");

function modif_type ($saisie, $utilisateur) {
    $total_traitements = isset($_POST["total_traitements"]) ? $_POST["total_traitements"] :(isset($_GET["total_traitements"]) ? $_GET["total_traitements"] :0);
    $ajout_type_absence = isset($_POST["ajout_type_absence"]) ? $_POST["ajout_type_absence"] :(isset($_GET["ajout_type_absence"]) ? $_GET["ajout_type_absence"] :null);
    $message_enregistrement = '';
    for($i=0; $i<$total_traitements; $i++) {

	//on test si on a un traitement a modifer
	if (!(isset($_POST['id_traitement'][$i]) || $_POST['id_traitement'][$i] == -1) ) {
	    //$message_enregistrement .= "Probleme avec l'id traitement : ".$_POST['id_traitement'][$i]."<br/>";
	    continue;
	}

	//il faut trouver le traitement corespondant � l'id
	$criteria = new Criteria();
	$criteria->add(AbsenceEleveTraitementPeer::ID, $_POST['id_traitement'][$i]);
	$traitement = $saisie->getAbsenceEleveTraitements($criteria);
	if ($traitement->count() != 1) {
	    $message_enregistrement .= "Probleme avec l'id traitement : ".$_POST['id_traitement'][$i]."<br/>";
	    continue;
	}
	if (!$traitement->getFirst()->getModifiable()) {
	    $message_enregistrement .= "Traitement ".$_POST['id_traitement'][$i]." non modifiable<br/>";
	    continue;
	}

	//on test si on a un traitement a modifer
	$type = AbsenceEleveTypeQuery::create()->findPk($_POST['type_traitement'][$i]);
	if ($type == null) {
	    $message_enregistrement .= "Impossible de supprimer un type.<br/>";
	    continue;
	}
	if (!$type->isStatutAutorise($utilisateur->getStatut())) {
	    $message_enregistrement .= "Type d'absence non autoris� pour ce statut : ".$_POST['type_absence_eleve'][$i]."<br/>";
	    continue;
	}
	$traitement->getFirst()->setAbsenceEleveType($type);
	$traitement->getFirst()->save();
    }


    if ($ajout_type_absence != null && $ajout_type_absence != -1) {
	$type = AbsenceEleveTypeQuery::create()->findPk($ajout_type_absence);
	if ($type != null) {
	    if ($type->isStatutAutorise($utilisateur->getStatut())) {
		//on va creer un traitement avec le type d'absence associ�
		$traitement = new AbsenceEleveTraitement();
		$traitement->addAbsenceEleveSaisie($saisie);
		$traitement->setAbsenceEleveType($type);
		$traitement->setUtilisateurProfessionnel($utilisateur);
		$traitement->save();
		$saisie->addAbsenceEleveTraitement($traitement);
	    } else {
		$message_enregistrement .= "Type d'absence non autoris� pour ce statut : ".$_POST['type_absence_eleve'][$i]."<br/>";
	    }
	} else {
	    $message_enregistrement .= "Probleme avec l'id du type d'absence : ".$_POST['type_absence_eleve'][$i]."<br/>";
	}
    } else if ($ajout_type_absence == -1) {
	$message_enregistrement .= "Il faut pr�ciser un type<br/>";
    }

    return $message_enregistrement;
}
?>