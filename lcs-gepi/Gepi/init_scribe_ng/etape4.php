<?php

/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard + auteur du script original (ac. Orl�ans-Tours)
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
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : Importation des enseignants";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// Utilisation de la classe LDAP chargee et configuree
$ldap = new LDAPServerScribe();

echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if ($_POST['step'] == "4") {


    // On se connecte au LDAP
    $ldap->connect();

    // Si on a bien les donnees dans la session, on peut continuer
    /*
     * Recherche de tous les profs de l'�tablissement (pour ce RNE)
    */
    $profs = $ldap->get_all_profs();
    $nb_profs = $profs['count'];


    /*
    * Ajout des profs
    */

    // Infos n�cessaires pour le prof
    $nom_complet = '';
    $uid_as_login = '';
    $mail = '';

    // On parcours tous les profs et on les ajoute
    for($cpt=0; $cpt<$profs['count']; $cpt++) {
        $uid_as_login = $profs[$cpt][$ldap->champ_login][0];
        $nom = $profs[$cpt][$ldap->champ_nom][0];
        $prenom = $profs[$cpt][$ldap->champ_prenom][0];
        $civ = $profs[$cpt]['personaltitle'][0];
        $mail = $profs[$cpt][$ldap->champ_email][0];

        // On test si l'uid est deja connu de GEPI
        $compte_utilisateur_prof = UtilisateurProfessionnelPeer::retrieveByPK($uid_as_login);
        if ($compte_utilisateur_prof != null) {
            // Un prof ayant cet UID existe deja : soit c'est le m�me, on ne touche pas
            // Soit c'est un prof diff�rent qui a cet uid
            echo "le professeur "
            .$compte_utilisateur_prof->getPrenom()
            .$compte_utilisateur_prof->getNom()
            ." (".$compte_utilisateur_prof->getLogin()
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
            $new_compte_utilisateur->setStatut('professeur');
            $new_compte_utilisateur->save();
        }

        // Insertion de sa qualit� de prof principal si c'est le cas
        if ($profs[$cpt]['typeadmin'][0] == 2) {
            $crit_classe_courante = new Criteria();
            $crit_classe_courante->add(ClassePeer::CLASSE, $profs[$cpt]['divcod'][0]); // indice contient le nom de la classe (son numero)
            $classe_courante = ClassePeer::doSelect($crit_classe_courante);
            if ($classe_courante == null) {
               die("Erreur : impossible de recuperer la classe $indice");
            }
            if (count($classe_courante) > 1) {
               die("Erreur : plusieurs classes ayant le nom '$indice' sont pr&eacute;sentes.");
            }

           // Si on trouve la classe, et qu'il y en a bien qu'une seule, on recupere son id technique

           $crit_eleves_de_la_classe = new Criteria();
           $crit_eleves_de_la_classe->add(JEleveClassePeer::ID_CLASSE, $classe_courante[0]->getId());
           $eleves_de_la_classe = JEleveClassePeer::doSelect($crit_eleves_de_la_classe);
           if ($eleves_de_la_classe != null) {
             foreach($eleves_de_la_classe as $eleve) {
               $sql_ajout_rel_prof_princ = "INSERT INTO j_eleves_professeurs VALUES('".$eleve->getLogin()."','$uid_as_login',".$classe_courante[0]->getId().")";
               mysql_query($sql_ajout_rel_prof_princ);
             }
           }
        }
        else {
            echo "Le prof $prenom $nom n'est pas professeur principal<br>";
        }
    } // fin parcours de tous les profs
        /*
         * R�sum� des profs trouv�s :
         */
    echo "<br/><br/>Professeurs trouv�s : $nb_profs"."<br/><br/>";

    echo "<form enctype='multipart/form-data' action='etape5.php' method=post>";
    echo "<input type=hidden name='step' value='5'>";
    echo "<input type=hidden name='record' value='no'>";

    echo "<p>Passer &agrave; l'&eacute;tape 5 :</p>";
    echo "<input type='submit' value='Etape 5'>";
    echo "</form>";
}

else {
    // Affichage de la page des explications de l'etape 4 (aucune donnee postee)

    echo "<br><p>L'&eacute;tape 4 vous permet d'importer les professeurs et leur qualit&eacute; de professeur principal.</p>";
    echo "<form enctype='multipart/form-data' action='etape4.php' method=post>";
    echo "<input type=hidden name='step' value='4'>";
    echo "<input type='submit' value='Je suis s�r'>";
    echo "</form>";
    echo "<br>";

    require("../lib/footer.inc.php");

}

?>
