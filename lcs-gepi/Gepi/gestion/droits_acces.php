<?php
/*
* $Id: droits_acces.php 3315 2009-07-30 17:29:54Z crob $
*
* Copyright 2001-2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};
// Check access

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg = '';


if (isset($_POST['OK'])) {
	if (isset($_POST['GepiRubConseilProf'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiRubConseilProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiRubConseilProf !";
	}

	if (isset($_POST['CommentairesTypesPP'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("CommentairesTypesPP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de CommentairesTypesPP !";
	}

	if (isset($_POST['GepiRubConseilScol'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiRubConseilScol", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiRubConseilScol !";
	}

	if (isset($_POST['CommentairesTypesScol'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("CommentairesTypesScol", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de CommentairesTypesScol !";
	}

	if (isset($_POST['GepiProfImprBul'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiProfImprBul", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiProfImprBul !";
	}

	if (isset($_POST['GepiProfImprBulSettings'])) {
		$temp = "yes";
	} else {
		$temp ="no";
	}
	if (!saveSetting("GepiProfImprBulSettings", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiProfImprBulSettings !";
	}


	if (isset($_POST['GepiAccesRestrAccesAppProfP'])) {
		$temp = "yes";
	} else {
		$temp ="no";
	}
	if (!saveSetting("GepiAccesRestrAccesAppProfP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesRestrAccesAppProfP !";
	}


	if (isset($_POST['GepiAdminImprBulSettings'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAdminImprBulSettings", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAdminImprBulSettings !";
	}

	if (isset($_POST['GepiScolImprBulSettings'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiScolImprBulSettings", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiScolImprBulSettings !";
	}

	if (isset($_POST['GepiAccesReleveScol'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveScol", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveScol !";
	}

	if (isset($_POST['GepiAccesReleveCpe'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveCpe", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveCpe !";
	}

	if (isset($_POST['GepiAccesReleveProfP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveProfP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveProfP !";
	}
	if (isset($_POST['GepiAccesReleveProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveProf !";
	}
	if (isset($_POST['GepiAccesReleveProfTousEleves'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveProfTousEleves", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveProf !";
	}
	if (isset($_POST['GepiAccesReleveProfToutesClasses'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveProfToutesClasses", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveProfToutesClasses !";
	}
	if (isset($_POST['GepiAccesMoyennesProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesMoyennesProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesMoyennesProf !";
	}
	if (isset($_POST['GepiAccesMoyennesProfTousEleves'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesMoyennesProfTousEleves", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesMoyennesProfTousEleves !";
	}
	if (isset($_POST['GepiAccesMoyennesProfToutesClasses'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesMoyennesProfToutesClasses", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesMoyennesProfToutesClasses !";
	}
	if (isset($_POST['GepiAccesReleveEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveEleve !";
	}

if (isset($_POST['GepiAccesOptionsReleveEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesOptionsReleveEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesOptionsReleveEleve !";
	}

	if (isset($_POST['GepiAccesCahierTexteEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCahierTexteEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCahierTexteEleve !";
	}

	if (isset($_POST['GepiAccesReleveParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesReleveParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesReleveParent !";
	}

	if (isset($_POST['GepiAccesOptionsReleveParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesOptionsReleveParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesOptionsReleveParent !";
	}

	if (isset($_POST['GepiAccesCahierTexteParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCahierTexteParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCahierTexteParent !";
	}

	if (isset($_POST['GepiPasswordReinitProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitProf !";
	}

	if (isset($_POST['GepiPasswordReinitScolarite'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitScolarite", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitScolarite !";
	}

	if (isset($_POST['GepiPasswordReinitCpe'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitCpe", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitCpe !";
	}

	if (isset($_POST['GepiPasswordReinitAdmin'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitAdmin", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitAdmin !";
	}

	if (isset($_POST['GepiPasswordReinitEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitEleve !";
	}

	if (isset($_POST['GepiPasswordReinitParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiPasswordReinitParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiPasswordReinitParent !";
	}

	if (isset($_POST['GepiAccesEquipePedaEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEquipePedaEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEquipePedaEleve !";
	}

	if (isset($_POST['GepiAccesEquipePedaParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEquipePedaParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEquipePedaParent !";
	}

	if (isset($_POST['GepiAccesEquipePedaEmailEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEquipePedaEmailEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEquipePedaEmailEleve !";
	}

	if (isset($_POST['GepiAccesCpePPEmailEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCpePPEmailEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCpePPEmailEleve !";
	}

	if (isset($_POST['GepiAccesEquipePedaEmailParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEquipePedaEmailParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEquipePedaEmailParent !";
	}

	if (isset($_POST['GepiAccesCpePPEmailParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesCpePPEmailParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesCpePPEmailParent !";
	}
	if (isset($_POST['GepiAccesBulletinSimpleProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleProf !";
	}
	if (isset($_POST['GepiAccesBulletinSimpleProfTousEleves'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleProfTousEleves", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleProfTousEleves !";
	}
	if (isset($_POST['GepiAccesBulletinSimpleProfToutesClasses'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleProfToutesClasses", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleProfToutesClasses !";
	}
	if (isset($_POST['GepiAccesBulletinSimpleParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleParent !";
	}

	if (isset($_POST['GepiAccesBulletinSimpleEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimpleEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimpleEleve !";
	}

	if (isset($_POST['GepiAccesGraphEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGraphEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGraphEleve !";
	}

	if (isset($_POST['GepiAccesGraphParent'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGraphParent", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGraphParent !";
	}


	if (isset($_POST['GepiAccesVisuToutesEquipProf'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiAccesVisuToutesEquipProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesVisuToutesEquipProf !";
	}

	if (isset($_POST['GepiAccesVisuToutesEquipScol'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiAccesVisuToutesEquipScol", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesVisuToutesEquipScol !";
	}

	if (isset($_POST['GepiAccesVisuToutesEquipCpe'])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting("GepiAccesVisuToutesEquipCpe", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesVisuToutesEquipCpe !";
	}

	// Ann�es ant�rieures
	if (isset($_POST['AAProfTout'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAProfTout", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAProfTout !";
	}

	if (isset($_POST['AAProfPrinc'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAProfPrinc", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAProfPrinc !";
	}

	if (isset($_POST['AAProfClasses'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAProfClasses", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAProfClasses !";
	}

	if (isset($_POST['AAProfGroupes'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAProfGroupes", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAProfGroupes !";
	}

	if (isset($_POST['AACpeTout'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AACpeTout", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AACpeTout !";
	}

	if (isset($_POST['AACpeResp'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AACpeResp", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AACpeResp !";
	}

	if (isset($_POST['AAScolTout'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAScolTout", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAScolTout !";
	}

	if (isset($_POST['AAScolResp'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAScolResp", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAScolResp !";
	}

	if (isset($_POST['AAResponsable'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAResponsable", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAResponsable !";
	}

	if (isset($_POST['AAEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("AAEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de AAEleve !";
	}


	if (isset($_POST['GepiAccesModifMaPhotoProfesseur'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoProfesseur", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoProfesseur !";
	}

	if (isset($_POST['GepiAccesModifMaPhotoAdministrateur'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoAdministrateur", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoAdministrateur !";
	}

	if (isset($_POST['GepiAccesModifMaPhotoScolarite'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoScolarite", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoScolarite !";
	}

	if (isset($_POST['GepiAccesModifMaPhotoCpe'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoCpe", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoCpe !";
	}

	if (isset($_POST['GepiAccesModifMaPhotoEleve'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesModifMaPhotoEleve", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesModifMaPhotoEleve !";
	}

	if (isset($_POST['GepiAccesGestElevesProfP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGestElevesProfP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGestElevesProfP !";
	}

	if (isset($_POST['GepiAccesGestPhotoElevesProfP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGestPhotoElevesProfP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGestPhotoElevesProfP !";
	}


	if (isset($_POST['GepiAccesBulletinSimplePP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesBulletinSimplePP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesBulletinSimplePP !";
	}



	if (isset($_POST['appreciations_types_profs'])) {
		if (!saveSetting("appreciations_types_profs", 'y')) {
			$msg .= "Erreur lors de l'enregistrement de l'autorisation d'utilisation d'appr�ciations-types pour les ".$gepiSettings['denomination_professeurs']." !";
		}
	}
	else{
		if (!saveSetting("appreciations_types_profs", 'n')) {
			$msg .= "Erreur lors de l'enregistrement de l'interdiction d'utilisation d'appr�ciations-types pour les ".$gepiSettings['denomination_professeurs']." !";
		}
	}

	if (isset($_POST['GepiAccesSaisieEctsPP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesSaisieEctsPP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesSaisieEctsPP !";
	}

	if (isset($_POST['GepiAccesSaisieEctsScolarite'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesSaisieEctsScolarite", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesSaisieEctsScolarite !";
	}

	if (isset($_POST['GepiAccesEditionDocsEctsPP'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEditionDocsEctsPP", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEditionDocsEctsPP !";
	}

	if (isset($_POST['GepiAccesEditionDocsEctsScolarite'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesEditionDocsEctsScolarite", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesEditionDocsEctsScolarite !";
	}




	if (isset($_POST['GepiAccesGestElevesProf'])) {
		$temp = "yes";
	} else {
		$temp = "no";
	}
	if (!saveSetting("GepiAccesGestElevesProf", $temp)) {
		$msg .= "Erreur lors de l'enregistrement de GepiAccesGestElevesProf !";
	}





	$tab_droits_ele_trombi=array('GepiAccesEleTrombiTousEleves',
'GepiAccesEleTrombiElevesClasse',
'GepiAccesEleTrombiPersonnels',
'GepiAccesEleTrombiProfsClasse');
	for($i=0;$i<count($tab_droits_ele_trombi);$i++) {
		if (isset($_POST[$tab_droits_ele_trombi[$i]])) {
			$temp = "yes";
		} else {
			$temp = "no";
		}
		if (!saveSetting("$tab_droits_ele_trombi[$i]", $temp)) {
			$msg .= "Erreur lors de l'enregistrement de $tab_droits_ele_trombi[$i] !";
		}
		/*
		else {
			$msg .= "Enregistrement de $tab_droits_ele_trombi[$i]=$temp !<br />";
		}
		*/
	}
}

// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont �t� enregistr�es !";

//debug_var();


//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
// End standart header
$titre_page = "Droits d'acc�s";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="index.php"<?php
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
?>><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<form action="droits_acces.php" method="post" name="form1" style="width: 100%;">
<table class='menu' style='width: 90%; margin-left: auto; margin-right: auto;' cellpadding="10" summary='Param�trage des droits'>
	<tr>
		<th colspan="2">
		Param�trage des droits d'acc�s
		</th>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">Un <?php echo $gepiSettings['denomination_professeur']; ?></td>
		<td>
			<table border='0' summary='Professeur'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveProf" id="GepiAccesReleveProf" value="yes" <?php if (getSettingValue("GepiAccesReleveProf")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveProf' style='cursor: pointer;'> a acc�s aux relev�s de notes des <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveProfTousEleves" id="GepiAccesReleveProfTousEleves" value="yes" <?php if (getSettingValue("GepiAccesReleveProfTousEleves")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveProfTousEleves' style='cursor: pointer;'> a acc�s aux relev�s de notes de tous les <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne (<i>si case non coch�e, le <?php echo $gepiSettings['denomination_professeur']; ?> ne voit que les <?php echo $gepiSettings['denomination_eleves']; ?> de ses groupes d'enseignement et pas les autres <?php echo $gepiSettings['denomination_eleves']; ?> des classes concern�es</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveProfToutesClasses" id="GepiAccesReleveProfToutesClasses" value="yes" <?php if (getSettingValue("GepiAccesReleveProfToutesClasses")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveProfToutesClasses' style='cursor: pointer;'> a acc�s aux relev�s de notes des <?php echo $gepiSettings['denomination_eleves']; ?> de toutes les classes</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesMoyennesProf" id="GepiAccesMoyennesProf" value="yes" <?php if (getSettingValue("GepiAccesMoyennesProf")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesMoyennesProf' style='cursor: pointer;'> a acc�s aux moyennes des <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesMoyennesProfTousEleves" id="GepiAccesMoyennesProfTousEleves" value="yes" <?php if (getSettingValue("GepiAccesMoyennesProfTousEleves")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesMoyennesProfTousEleves' style='cursor: pointer;'> a acc�s aux moyennes de tous les <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne (<i>si case non coch�e, le <?php echo $gepiSettings['denomination_professeur']; ?> ne voit que les <?php echo $gepiSettings['denomination_eleves']; ?> de ses groupes d'enseignement et pas les autres <?php echo $gepiSettings['denomination_eleves']; ?> des classes concern�es</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesMoyennesProfToutesClasses" id="GepiAccesMoyennesProfToutesClasses" value="yes" <?php if (getSettingValue("GepiAccesMoyennesProfToutesClasses")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesMoyennesProfToutesClasses' style='cursor: pointer;'> a acc�s aux moyennes des <?php echo $gepiSettings['denomination_eleves']; ?> de toutes les classes</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesBulletinSimpleProf" id="GepiAccesBulletinSimpleProf" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleProf")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimpleProf' style='cursor: pointer;'> a acc�s aux bulletins simples des <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesBulletinSimpleProfTousEleves" id="GepiAccesBulletinSimpleProfTousEleves" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleProfTousEleves")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimpleProfTousEleves' style='cursor: pointer;'> a acc�s aux bulletins simples de tous les <?php echo $gepiSettings['denomination_eleves']; ?> des classes dans lesquelles il enseigne (<i>si case non coch�e, le <?php echo $gepiSettings['denomination_professeur']; ?> ne voit que les <?php echo $gepiSettings['denomination_eleves']; ?> de ses groupes d'enseignement et pas les autres <?php echo $gepiSettings['denomination_eleves']; ?> des classes concern�es</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesBulletinSimpleProfToutesClasses" id="GepiAccesBulletinSimpleProfToutesClasses" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleProfToutesClasses")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimpleProfToutesClasses' style='cursor: pointer;'> a acc�s aux bulletins simples des <?php echo $gepiSettings['denomination_eleves']; ?> de toutes les classes</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="appreciations_types_profs" id="appreciations_types_profs" value="y" <?php if (getSettingValue("appreciations_types_profs")=='y') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='appreciations_types_profs' style='cursor: pointer;'> peut utiliser des appr�ciations-types sur les bulletins.</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitProf" id="GepiPasswordReinitProf" value="yes" <?php if (getSettingValue("GepiPasswordReinitProf")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitProf' style='cursor: pointer;'> peut r�initialiser lui-m�me son mot de passe perdu (<i>si fonction activ�e</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesVisuToutesEquipProf" id="GepiAccesVisuToutesEquipProf" value="yes" <?php if (getSettingValue("GepiAccesVisuToutesEquipProf")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesVisuToutesEquipProf' style='cursor: pointer;'> a acc�s � la Visualisation de toutes les �quipes</label></td>
			</tr>

			<!-- Ann�es ant�rieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAProfTout" id="AAProfTout" value="yes" <?php if (getSettingValue("AAProfTout")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAProfTout' style='cursor: pointer;'> a acc�s aux donn�es d'ann�es ant�rieures pour tous les <?php echo $gepiSettings['denomination_eleves']; ?></label></td>
			</tr>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAProfClasses" id="AAProfClasses" value="yes" <?php if (getSettingValue("AAProfClasses")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAProfClasses' style='cursor: pointer;'> a acc�s aux donn�es ant�rieures des <?php echo $gepiSettings['denomination_eleves']; ?> des classes pour lesquelles il fournit un enseignement<br />
				(<i>sans n�cessairement avoir tous les <?php echo $gepiSettings['denomination_eleves']; ?> de la classe</i>)</label></td>
			</tr>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAProfGroupes" id="AAProfGroupes" value="yes" <?php if (getSettingValue("AAProfGroupes")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAProfGroupes' style='cursor: pointer;'> a acc�s aux donn�es ant�rieures des <?php echo $gepiSettings['denomination_eleves']; ?> des groupes auxquels il enseigne<br />
				(<i>il a ces <?php echo $gepiSettings['denomination_eleves']; ?> en classe</i>)
				</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesGestElevesProf" id="GepiAccesGestElevesProf" value="yes" <?php if (getSettingValue("GepiAccesGestElevesProf")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesGestElevesProf' style='cursor: pointer;'> a acc�s aux fiches des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est professeur.</label>
				</td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesModifMaPhotoProfesseur" id="GepiAccesModifMaPhotoProfesseur" value="yes" <?php if (getSettingValue("GepiAccesModifMaPhotoProfesseur")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesModifMaPhotoProfesseur' style='cursor: pointer;'> a le droit d'envoyer/modifier lui-m�me sa photo dans 'G�rer mon compte'
				</label></td>
			</tr>

			</table>
		</td>
	</tr>
	<tr>
		<!-- Professeur principal-->
		<td style="font-variant: small-caps;">Un <?php echo getSettingValue("gepi_prof_suivi"); ?></td>
		<td>
			<table border='0' summary='Professeur principal'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiRubConseilProf" id="GepiRubConseilProf" value="yes" <?php if (getSettingValue("GepiRubConseilProf")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiRubConseilProf' style='cursor: pointer;'> peut saisir les avis du conseil de classe pour sa classe</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="CommentairesTypesPP" id="CommentairesTypesPP" value="yes" <?php if (getSettingValue("CommentairesTypesPP")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='CommentairesTypesPP' style='cursor: pointer;'> peut utiliser des commentaires-types dans ces saisies d'avis du conseil de classe<br />(<i>sous r�serve de pouvoir saisir les avis du conseil de classe</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiProfImprBul" id="GepiProfImprBul" value="yes" <?php if (getSettingValue("GepiProfImprBul")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiProfImprBul' style='cursor: pointer;'> �dite/imprime les bulletins p�riodiques des classes dont il a la charge.<br />
				<span class='small'>(<i>Par d�faut, seul un utilisateur ayant le statut scolarit� peut �diter les bulletins</i>)</span></label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiProfImprBulSettings" id="GepiProfImprBulSettings" value="yes" <?php if (getSettingValue("GepiProfImprBulSettings")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiProfImprBulSettings' style='cursor: pointer;'> a acc�s au param�trage de l'impression des bulletins (<i>lorsqu'il est autoris� � �diter/imprimer les bulletins</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesRestrAccesAppProfP" id="GepiAccesRestrAccesAppProfP" value="yes" <?php if (getSettingValue("GepiAccesRestrAccesAppProfP")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesRestrAccesAppProfP' style='cursor: pointer;'> a acc�s au param�trage des acc�s <?php echo $gepiSettings['denomination_responsables']; ?>/<?php echo $gepiSettings['denomination_eleves']; ?> aux appr�ciations/avis des classes dont il est <?php echo getSettingValue("gepi_prof_suivi"); ?></label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesBulletinSimplePP" id="GepiAccesBulletinSimplePP" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimplePP")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimplePP' style='cursor: pointer;'> a acc�s aux bulletins simples des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est <?php echo getSettingValue("gepi_prof_suivi");?></label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveProfP" id="GepiAccesReleveProfP" value="yes" <?php if (getSettingValue("GepiAccesReleveProfP")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveProfP' style='cursor: pointer;'> a acc�s aux relev�s des classes dont il est <?php echo getSettingValue("gepi_prof_suivi"); ?></label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesGestElevesProfP" id="GepiAccesGestElevesProfP" value="yes" <?php if (getSettingValue("GepiAccesGestElevesProfP")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesGestElevesProfP' style='cursor: pointer;'> a acc�s aux fiches des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est <?php echo getSettingValue("gepi_prof_suivi"); ?></label>
				</td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'>
				<input type="checkbox" name="GepiAccesGestPhotoElevesProfP" id="GepiAccesGestPhotoElevesProfP" value="yes" <?php if (getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes') echo "checked"; ?> onchange='changement();' />
				</td>
				<td style='border: 0px;'><label for='GepiAccesGestPhotoElevesProfP' style='cursor: pointer;'>
				 a acc�s � l'upload des photos de ses <?php echo $gepiSettings['denomination_eleves']; ?> si le module trombinoscope est activ� et si le <?php echo $gepiSettings['denomination_professeur']; ?> a acc�s aux fiches <?php echo $gepiSettings['denomination_eleves']; ?> (<i>ci-dessus</i>).</label></td>
			</tr>

			<!-- Ann�es ant�rieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAProfPrinc" id="AAProfPrinc" value="yes" <?php if (getSettingValue("AAProfPrinc")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAProfPrinc' style='cursor: pointer;'> a acc�s aux donn�es d'ann�es ant�rieures des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est <?php echo $gepiSettings['denomination_professeur']; ?> principal</label></td>
			</tr>
           	<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesSaisieEctsPP" id="GepiAccesSaisieEctsPP" value="yes" <?php if (getSettingValue("GepiAccesSaisieEctsPP")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesSaisieEctsPP' style='cursor: pointer;'> peut saisir les cr�dits ECTS pour sa classe</label></td>
			</tr>
           	<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEditionDocsEctsPP" id="GepiAccesEditionDocsEctsPP" value="yes" <?php if (getSettingValue("GepiAccesEditionDocsEctsPP")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEditionDocsEctsPP' style='cursor: pointer;'> peut �diter les relev�s ECTS pour sa classe</label></td>
			</tr>
			</table>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">La scolarit�</td>
		<td>
			<table border='0' summary='Scolarit�'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiRubConseilScol" id="GepiRubConseilScol" value="yes" <?php if (getSettingValue("GepiRubConseilScol")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiRubConseilScol' style='cursor: pointer;'> peut saisir les avis du conseil de classe</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="CommentairesTypesScol" id="CommentairesTypesScol" value="yes" <?php if (getSettingValue("CommentairesTypesScol")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='CommentairesTypesScol' style='cursor: pointer;'> peut utiliser des commentaires-types dans ces saisies d'avis du conseil de classe<br />(<i>sous r�serve de pouvoir saisir les avis du conseil de classe</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiScolImprBulSettings" id="GepiScolImprBulSettings" value="yes" <?php if (getSettingValue("GepiScolImprBulSettings")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiScolImprBulSettings' style='cursor: pointer;'> a acc�s au param�trage de l'impression des bulletins</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveScol" id="GepiAccesReleveScol" value="yes" <?php if (getSettingValue("GepiAccesReleveScol")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveScol' style='cursor: pointer;'> a acc�s � tous les relev�s de notes de toutes les classes</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitScolarite" id="GepiPasswordReinitScolarite" value="yes" <?php if (getSettingValue("GepiPasswordReinitScolarite")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitScolarite' style='cursor: pointer;'> peut r�initialiser elle-m�me son mot de passe perdu (<i>si fonction activ�e</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesVisuToutesEquipScol" id="GepiAccesVisuToutesEquipScol" value="yes" <?php if (getSettingValue("GepiAccesVisuToutesEquipScol")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesVisuToutesEquipScol' style='cursor: pointer;'> a acc�s � la Visualisation de toutes les �quipes</label></td>
			</tr>

			<!-- Ann�es ant�rieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAScolTout" id="AAScolTout" value="yes" <?php if (getSettingValue("AAScolTout")=='yes') echo "checked"; ?> /></td>
				<td style='border: 0px;'><label for='AAScolTout' style='cursor: pointer;'> a acc�s aux donn�es d'ann�es ant�rieures de tous les <?php echo $gepiSettings['denomination_eleves']; ?></label></td>
			</tr>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAScolResp" id="AAScolResp" value="yes" <?php if (getSettingValue("AAScolResp")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAScolResp' style='cursor: pointer;'> a acc�s aux donn�es d'ann�es ant�rieures des <?php echo $gepiSettings['denomination_eleves']; ?> des classes dont il est responsable</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesModifMaPhotoScolarite" id="GepiAccesModifMaPhotoScolarite" value="yes" <?php if (getSettingValue("GepiAccesModifMaPhotoScolarite")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesModifMaPhotoScolarite' style='cursor: pointer;'> a le droit d'envoyer/modifier lui-m�me sa photo dans 'G�rer mon compte'
				</label></td>
			</tr>

           	<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesSaisieEctsScolarite" id="GepiAccesSaisieEctsScolarite" value="yes" <?php if (getSettingValue("GepiAccesSaisieEctsScolarite")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesSaisieEctsScolarite' style='cursor: pointer;'> peut saisir les cr�dits ECTS</label></td>
			</tr>
           	<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEditionDocsEctsScolarite" id="GepiAccesEditionDocsEctsScolarite" value="yes" <?php if (getSettingValue("GepiAccesEditionDocsEctsScolarite")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEditionDocsEctsScolarite' style='cursor: pointer;'> peut �diter les relev�s d'ECTS</label></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">Un CPE</td>
		<td>
			<table border='0' summary='CPE'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveCpe" id="GepiAccesReleveCpe" value="yes" <?php if (getSettingValue("GepiAccesReleveCpe")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveCpe' style='cursor: pointer;'> a acc�s � tous les relev�s de notes de toutes les classes</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitCpe" id="GepiPasswordReinitCpe" value="yes" <?php if (getSettingValue("GepiPasswordReinitCpe")=='yes') echo "checked"; ?> /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitCpe' style='cursor: pointer;'> peut r�initialiser lui-m�me son mot de passe perdu (<i>si fonction activ�e</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesVisuToutesEquipCpe" id="GepiAccesVisuToutesEquipCpe" value="yes" <?php if (getSettingValue("GepiAccesVisuToutesEquipCpe")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesVisuToutesEquipCpe' style='cursor: pointer;'> a acc�s � la Visualisation de toutes les �quipes</label></td>
			</tr>

			<!-- Ann�es ant�rieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AACpeTout" id="AACpeTout" value="yes" <?php if (getSettingValue("AACpeTout")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AACpeTout' style='cursor: pointer;'> a acc�s aux donn�es d'ann�es ant�rieures de tous les <?php echo $gepiSettings['denomination_eleves']; ?></label></td>
			</tr>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AACpeResp" id="AACpeResp" value="yes" <?php if (getSettingValue("AACpeResp")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AACpeResp' style='cursor: pointer;'> a acc�s aux donn�es d'ann�es ant�rieures des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesModifMaPhotoCpe" id="GepiAccesModifMaPhotoCpe" value="yes" <?php if (getSettingValue("GepiAccesModifMaPhotoCpe")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesModifMaPhotoCpe' style='cursor: pointer;'> a le droit d'envoyer/modifier lui-m�me sa photo dans 'G�rer mon compte'
				</label></td>
			</tr>

			</table>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">Un administrateur</td>
		<td>
			<table border='0' summary='Administrateur'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAdminImprBulSettings" id="GepiAdminImprBulSettings" value="yes" <?php if (getSettingValue("GepiAdminImprBulSettings")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAdminImprBulSettings' style='cursor: pointer;'> a acc�s au param�trage de l'impression des bulletins</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitAdmin" id="GepiPasswordReinitAdmin" value="yes" <?php if (getSettingValue("GepiPasswordReinitAdmin")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitAdmin' style='cursor: pointer;'> peut r�initialiser lui-m�me son mot de passe perdu (<i>si fonction activ�e</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesModifMaPhotoAdministrateur" id="GepiAccesModifMaPhotoAdministrateur" value="yes" <?php if (getSettingValue("GepiAccesModifMaPhotoAdministrateur")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesModifMaPhotoAdministrateur' style='cursor: pointer;'> a le droit d'envoyer/modifier lui-m�me sa photo dans 'G�rer mon compte'
				</label></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">Un <?php echo $gepiSettings['denomination_eleve']; ?></td>
		<td>
			<table border='0' summary='El�ve'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveEleve" id="GepiAccesReleveEleve" value="yes" <?php if (getSettingValue("GepiAccesReleveEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveEleve' style='cursor: pointer;'> a acc�s � ses relev�s de notes</label></td>
			</tr>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesOptionsReleveEleve" id="GepiAccesOptionsReleveEleve" value="yes" <?php if (getSettingValue("GepiAccesOptionsReleveEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesOptionsReleveEleve' style='cursor: pointer;'> a acc�s aux options du relev�s de notes (<i>nom court, coef, date des devoirs, ...</i>)</label></td>
			</tr>


			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCahierTexteEleve" id="GepiAccesCahierTexteEleve" value="yes" <?php if (getSettingValue("GepiAccesCahierTexteEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCahierTexteEleve' style='cursor: pointer;'> a acc�s � son cahier de texte</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitEleve" id="GepiPasswordReinitEleve" value="yes" <?php if (getSettingValue("GepiPasswordReinitEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitEleve' style='cursor: pointer;'> peut r�initialiser lui-m�me son mot de passe perdu (<i>si fonction activ�e</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEquipePedaEleve" id="GepiAccesEquipePedaEleve" value="yes" <?php if (getSettingValue("GepiAccesEquipePedaEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEquipePedaEleve' style='cursor: pointer;'> a acc�s � l'�quipe p�dagogique le concernant</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCpePPEmailEleve" id="GepiAccesCpePPEmailEleve" value="yes" <?php if (getSettingValue("GepiAccesCpePPEmailEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCpePPEmailEleve' style='cursor: pointer;'> a acc�s aux adresses email de son CPE et de son professeur principal (<i>param�tre utile seulement si le param�tre suivant est d�coch�</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEquipePedaEmailEleve" id="GepiAccesEquipePedaEmailEleve" value="yes" <?php if (getSettingValue("GepiAccesEquipePedaEmailEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEquipePedaEmailEleve' style='cursor: pointer;'> a acc�s aux adresses email de l'�quipe p�dagogique le concernant</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;' valign='top'><input type="checkbox" name="GepiAccesBulletinSimpleEleve" id="GepiAccesBulletinSimpleEleve" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleEleve")=='yes') echo "checked"; ?> onchange='changement();' /><a name='bull_simp_ele'></a></td>
				<td style='border: 0px;'>
				<label for='GepiAccesBulletinSimpleEleve' style='cursor: pointer;'> a acc�s � ses bulletins simplifi�s</label>
				<br />
				<?php
					$acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
					if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';}
					$delais_apres_cloture=getSettingValue('delais_apres_cloture');
					if(!ereg("^[0-9]*$",$delais_apres_cloture)) {$delais_apres_cloture=0;}

					echo "<span style='font-size:x-small'>";
					if($acces_app_ele_resp=='manuel') {
						echo "L'acc�s aux appr�ciations est donn� manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Acc�s aux appr�ciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='date') {
						echo "L'acc�s aux appr�ciations est ouvert � la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Acc�s aux appr�ciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='periode_close') {
						echo "L'acc�s aux appr�ciations est ouvert automatiquement ";
						if($delais_apres_cloture>0) {echo $delais_apres_cloture." jours apr�s ";}
						echo "la cl�ture de la p�riode par un compte scolarit�.";
					}
					echo "</span>";
					echo "<br />";
					echo "<span style='font-size:x-small'>";
					echo "Le mode d'ouverture de l'acc�s se param�tre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion g�n�rale/Configuration g�n�rale</a>";
					echo "</span>";

				?>
				</td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;' valign='top'><input type="checkbox" name="GepiAccesGraphEleve" id="GepiAccesGraphEleve" value="yes" <?php if (getSettingValue("GepiAccesGraphEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesGraphEleve' style='cursor: pointer;'> a acc�s � la visualisation graphique de ses r�sultats</label>
				<br />
				<?php

					echo "<span style='font-size:x-small'>";
					if($acces_app_ele_resp=='manuel') {
						echo "L'acc�s aux appr�ciations est donn� manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Acc�s aux appr�ciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='date') {
						echo "L'acc�s aux appr�ciations est ouvert � la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Acc�s aux appr�ciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='periode_close') {
						echo "L'acc�s aux appr�ciations est ouvert automatiquement ";
						if($delais_apres_cloture>0) {echo $delais_apres_cloture." jours apr�s ";}
						echo "la cl�ture de la p�riode par un compte scolarit�.";
					}
					echo "</span>";
					echo "<br />";
					echo "<span style='font-size:x-small'>";
					echo "Le mode d'ouverture de l'acc�s se param�tre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion g�n�rale/Configuration g�n�rale</a>";
					echo "</span>";

				?>
				</td>
			</tr>

			<!-- Ann�es ant�rieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAEleve" id="AAEleve" value="yes" <?php if (getSettingValue("AAEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAEleve' style='cursor: pointer;'> a acc�s � ses donn�es d'ann�es ant�rieures</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesModifMaPhotoEleve" id="GepiAccesModifMaPhotoEleve" value="yes" <?php if (getSettingValue("GepiAccesModifMaPhotoEleve")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesModifMaPhotoEleve' style='cursor: pointer;'> a le droit d'envoyer/modifier lui-m�me sa photo dans 'G�rer mon compte'
				<br /><i>(voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</i>
				</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEleTrombiTousEleves" id="GepiAccesEleTrombiTousEleves" value="yes" <?php if (getSettingValue("GepiAccesEleTrombiTousEleves")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEleTrombiTousEleves' style='cursor: pointer;'> a acc�s au trombinoscope de tous les <?php echo $gepiSettings['denomination_eleves']; ?> de l'�tablissement.<br />
				<i>(sous r�serve que le module Trombinoscope-�l�ve soit activ�.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</i>
				</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEleTrombiElevesClasse" id="GepiAccesEleTrombiElevesClasse" value="yes" <?php if (getSettingValue("GepiAccesEleTrombiElevesClasse")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEleTrombiElevesClasse' style='cursor: pointer;'> a acc�s au trombinoscope des <?php echo $gepiSettings['denomination_eleves']; ?> de sa classe.<br />
				<i>(sous r�serve que le module Trombinoscope-�l�ve soit activ�.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</i>
				</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEleTrombiPersonnels" id="GepiAccesEleTrombiPersonnels" value="yes" <?php if (getSettingValue("GepiAccesEleTrombiPersonnels")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEleTrombiPersonnels' style='cursor: pointer;'> a acc�s au trombinoscope de tous les personnels de l'�tablissement.<br />
				<i>(sous r�serve que le module Trombinoscope-personnels soit activ�.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</i>
				</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEleTrombiProfsClasse" id="GepiAccesEleTrombiProfsClasse" value="yes" <?php if (getSettingValue("GepiAccesEleTrombiProfsClasse")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEleTrombiProfsClasse' style='cursor: pointer;'> a acc�s au trombinoscope des <?php echo $gepiSettings['denomination_professeurs']; ?> de sa classe.<br />
				<i>(sous r�serve que le module Trombinoscope-personnels soit activ�.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'acc&egrave;s)</i>
				</label></td>
			</tr>

			</table>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">Un <?php echo $gepiSettings['denomination_responsable']; ?></td>
		<td>
			<table border='0' summary='Responsable'>
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesReleveParent" id="GepiAccesReleveParent" value="yes" <?php if (getSettingValue("GepiAccesReleveParent")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesReleveParent' style='cursor: pointer;'> a acc�s aux relev�s de notes des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesOptionsReleveParent" id="GepiAccesOptionsReleveParent" value="yes" <?php if (getSettingValue("GepiAccesOptionsReleveParent")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesOptionsReleveParent' style='cursor: pointer;'> a acc�s aux options du relev�s de notes (<i>nom court, coef, date des devoirs,...</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCahierTexteParent" id="GepiAccesCahierTexteParent" value="yes" <?php if (getSettingValue("GepiAccesCahierTexteParent")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCahierTexteParent' style='cursor: pointer;'> a acc�s au cahier de texte des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiPasswordReinitParent" id="GepiPasswordReinitParent" value="yes" <?php if (getSettingValue("GepiPasswordReinitParent")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiPasswordReinitParent' style='cursor: pointer;'> peut r�initialiser lui-m�me son mot de passe perdu (<i>si fonction activ�e</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEquipePedaParent" id="GepiAccesEquipePedaParent" value="yes" <?php if (getSettingValue("GepiAccesEquipePedaParent")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEquipePedaParent' style='cursor: pointer;'> a acc�s � l'�quipe p�dagogique concernant les <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesCpePPEmailParent" id="GepiAccesCpePPEmailParent" value="yes" <?php if (getSettingValue("GepiAccesCpePPEmailParent")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesCpePPEmailParent' style='cursor: pointer;'> a acc�s aux adresses email du CPE et du professeur principal responsables des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable (<i>param�tre utile seulement si le param�tre suivant est d�coch�</i>)</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="GepiAccesEquipePedaEmailParent" id="GepiAccesEquipePedaEmailParent" value="yes" <?php if (getSettingValue("GepiAccesEquipePedaEmailParent")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesEquipePedaEmailParent' style='cursor: pointer;'> a acc�s aux adresses email de l'�quipe p�dagogique concernant les <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;' valign='top'><input type="checkbox" name="GepiAccesBulletinSimpleParent" id="GepiAccesBulletinSimpleParent" value="yes" <?php if (getSettingValue("GepiAccesBulletinSimpleParent")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesBulletinSimpleParent' style='cursor: pointer;'> a acc�s aux bulletins simplifi�s des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label>
				<br />
				<?php

					echo "<span style='font-size:x-small'>";
					if($acces_app_ele_resp=='manuel') {
						echo "L'acc�s aux appr�ciations est donn� manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Acc�s aux appr�ciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='date') {
						echo "L'acc�s aux appr�ciations est ouvert � la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Acc�s aux appr�ciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='periode_close') {
						echo "L'acc�s aux appr�ciations est ouvert automatiquement ";
						if($delais_apres_cloture>0) {echo $delais_apres_cloture." jours apr�s ";}
						echo "la cl�ture de la p�riode par un compte scolarit�.";
					}
					echo "</span>";
					echo "<br />";
					echo "<span style='font-size:x-small'>";
					echo "Le mode d'ouverture de l'acc�s se param�tre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion g�n�rale/Configuration g�n�rale</a>";
					echo "</span>";
				?>
				</td>
			</tr>

			<tr valign='top'>
				<td style='border: 0px;' valign='top'><input type="checkbox" name="GepiAccesGraphParent" id="GepiAccesGraphParent" value="yes" <?php if (getSettingValue("GepiAccesGraphParent")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='GepiAccesGraphParent' style='cursor: pointer;'> a acc�s � la visualisation graphique des r�sultats des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label>
				<br />
				<?php

					echo "<span style='font-size:x-small'>";
					if($acces_app_ele_resp=='manuel') {
						echo "L'acc�s aux appr�ciations est donn� manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Acc�s aux appr�ciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='date') {
						echo "L'acc�s aux appr�ciations est ouvert � la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Acc�s aux appr�ciations et avis du conseil</a>.";
					}
					elseif($acces_app_ele_resp=='periode_close') {
						echo "L'acc�s aux appr�ciations est ouvert automatiquement ";
						if($delais_apres_cloture>0) {echo $delais_apres_cloture." jours apr�s ";}
						echo "la cl�ture de la p�riode par un compte scolarit�.";
					}
					echo "</span>";
					echo "<br />";
					echo "<span style='font-size:x-small'>";
					echo "Le mode d'ouverture de l'acc�s se param�tre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion g�n�rale/Configuration g�n�rale</a>";
					echo "</span>";
				?>
			</td>
			</tr>

			<!-- Ann�es ant�rieures -->
			<tr valign='top'>
				<td style='border: 0px;'><input type="checkbox" name="AAResponsable" id="AAResponsable" value="yes" <?php if (getSettingValue("AAResponsable")=='yes') echo "checked"; ?> onchange='changement();' /></td>
				<td style='border: 0px;'><label for='AAResponsable' style='cursor: pointer;'> a acc�s aux donn�es d'ann�es ant�rieures des <?php echo $gepiSettings['denomination_eleves']; ?> dont il est responsable</label></td>
			</tr>
			</table>
		</td>
	</tr>
</table>
<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" name = "OK" value="Enregistrer" style="font-variant: small-caps;" /></center>
</form>
<p><br /></p>
<?php require("../lib/footer.inc.php");?>