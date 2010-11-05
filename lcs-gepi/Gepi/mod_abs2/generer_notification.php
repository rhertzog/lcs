<?php
/**
 *
 * @version $Id: generer_notification.php 5364 2010-09-20 19:22:11Z jjacquard $
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

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

include_once 'lib/function.php';

//r�cup�ration des param�tres de la requ�te
$id_notification = isset($_POST["id_notification"]) ? $_POST["id_notification"] :(isset($_GET["id_notification"]) ? $_GET["id_notification"] :NULL);

$notification = AbsenceEleveNotificationQuery::create()->findPk($id_notification);

$retour_envoi = '';

if ($notification == null && !isset($_POST["creation_notification"])) {
    $message_enregistrement .= 'Generation impossible : notification non trouv�e. ';
    include("visu_notification.php");
    die();
}

if ($notification->getTypeNotification() != AbsenceEleveNotification::$TYPE_COURRIER && $notification->getStatutEnvoi() != AbsenceEleveNotification::$STATUT_INITIAL) {
    $message_enregistrement .= 'G�n�ration impossible : envoi d�j� effectu�. ';
    include("visu_notification.php");
    die();
}

if ($notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_COURRIER) {
    // Load the template
    $modele_lettre_parents=repertoire_modeles("absence_modele_lettre_parents.odt");
    include_once '../orm/helpers/AbsencesNotificationHelper.php';
    $TBS = AbsencesNotificationHelper::MergeNotification($notification, $modele_lettre_parents);

    $notification->setDateEnvoi('now');
    $notification->setStatutEnvoi(AbsenceEleveNotification::$STATUT_EN_COURS);
    $notification->save();

    // Output as a download file (some automatic fields are merged here)
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, 'abs_notif_'.$notification->getId().'.odt');
    die();

} else if ($notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_EMAIL) {
    // Load the template
    $email=repertoire_modeles('absence_email.txt');
    include_once '../orm/helpers/AbsencesNotificationHelper.php';
    $TBS = AbsencesNotificationHelper::MergeNotification($notification, $email);
    $message = $TBS->Source;

    $retour_envoi = AbsencesNotificationHelper::EnvoiNotification($notification, $message);

} else if ($notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_SMS) {
    // Load the template
    $sms=repertoire_modeles('absence_sms.txt');
    include_once '../orm/helpers/AbsencesNotificationHelper.php';
    $TBS = AbsencesNotificationHelper::MergeNotification($notification, $sms);
    $message = $TBS->Source;

    $retour_envoi = AbsencesNotificationHelper::EnvoiNotification($notification, $message);
}
if ($notification->getStatutEnvoi() == AbsenceEleveNotification::$STATUT_SUCCES) {
    $message_enregistrement = 'Envoi r�ussi. '.$retour_envoi;
} else {
    $message_enregistrement = '�chec de l\'envoi. '.$retour_envoi;
}
include('visu_notification.php');
?>
