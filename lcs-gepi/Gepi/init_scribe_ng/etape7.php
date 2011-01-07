<?php

/*
 * $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard + auteur du script original (ac. Orl�ans-Tours)
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
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
require_once("../lib/LDAPServerScribe.class.php");
require_once("eleves_fonctions.php");
include("config_init_annuaire.inc.php");

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

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : importation des personnels administratifs";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Utilisation de la classe LDAP chargee et configuree
$ldap = new LDAPServerScribe();

echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if ($_POST['step'] == "7") {
	check_token(false);

    // On se connecte au LDAP
    $ldap->connect();

    // Si on a bien les donnees dans la session, on peut continuer
    /*
     * Recherche de tous les profs de l'�tablissement (pour ce RNE)
    */
    $personnels = $ldap->get_all_personnels();
    $nb_pers = $personnels['count'];


    /*
    * Ajout des profs
    */

    // Infos n�cessaires
    $nom_complet = '';
    $uid_as_login = '';
    $mail = '';

    // On parcours tous les utilisateurs et on les ajoute, si n�cessaire
    for($cpt=0; $cpt<$personnels['count']; $cpt++) {
        $uid_as_login = $personnels[$cpt][$ldap->champ_login][0];
        $nom = $personnels[$cpt][$ldap->champ_nom][0];
        $prenom = $personnels[$cpt][$ldap->champ_prenom][0];
        $civ = $personnels[$cpt]['personaltitle'][0];
        $mail = $personnels[$cpt][$ldap->champ_email][0];

        // On test si l'uid est deja connu de GEPI
        $compte_utilisateur = UtilisateurProfessionnelPeer::retrieveByPK($uid_as_login);
        if ($compte_utilisateur != null) {
            echo "L'utilisateur "
            .$compte_utilisateur->getPrenom()
            .$compte_utilisateur->getNom()
            ." (".$compte_utilisateur->getLogin()
            .") existe d&eacute;ja<br>";
        }
        else {
            $new_compte_utilisateur = new UtilisateurProfessionnel();
            $new_compte_utilisateur->setAuthMode('sso');
            $new_compte_utilisateur->setCivilite($civ);
            $new_compte_utilisateur->setEmail($mail);
            $new_compte_utilisateur->setEtat('actif');
            $new_compte_utilisateur->setLogin($uid_as_login);
            $new_compte_utilisateur->setNom($nom);
            $new_compte_utilisateur->setPrenom($prenom);
            $new_compte_utilisateur->setShowEmail('no');
            $new_compte_utilisateur->setStatut('scolarite');
            $new_compte_utilisateur->save();
        }
    } // fin parcours de tous les personnels
        /*
         * R�sum� des profs trouv�s :
         */
    echo "<br/><br/>Nombre de personnels trouv�s : $nb_pers"."<br/><br/>";

    echo "<form enctype='multipart/form-data' action='../accueil_admin.php' method=post>";
	//echo add_token_field();

    echo "<p>Si vous etes arriv&eacute;s &agrave; cette &eacute;tape, vous avez termin&eacute; l'import des donnees provenant de l'annuaire ENT.</p>";
    echo "<p>Vous pouvez maintenant aller dans la partie gestion des bases pour affiner les diff�rentes donn�es import�es.</p>";
    echo "<p>N'oubliez pas de v�rifier les comptes d'acc�s cr��s pour les personnels administratifs, qui ont tous �t� initialis�s par d�faut au statut 'scolarit�'.</p>";
    echo "<input type='submit' value='Acc&eacute;der &agrave la gestion des bases'>";
    echo "</form>";
}

else {
    // Affichage de la page des explications de l'etape 4 (aucune donnee postee)

    echo "<br><p>L'&eacute;tape 7 vous permet d'importer les comptes des personnels non-enseignant de l'�tablissement.</p>";
    echo "<p>Note importante : l'annuaire LDAP ne permettant pas de distinguer les personnels entre eux, tous les utilisateurs trouv�s et n'existant pas d�j� dans la base seront initialis�s avec le statut 'scolarite'. Il est donc indispensable que vous red�finissiez les bons statuts dans l'interface de gestion des comptes d'acc�s.</p>";
    echo "<form enctype='multipart/form-data' action='etape7.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='step' value='7'>";
    echo "<input type='submit' value='Je suis s�r'>";
    echo "</form>";
    echo "<br>";

    require("../lib/footer.inc.php");

}

?>
