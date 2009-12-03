<?php
/*
 * $Id: index.php 3355 2009-08-30 12:22:53Z crob $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Gestion g�n�rale";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>
<p class='bold'><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a></p>
<center>
<table class='menu' summary='Menu s�curit�'>
<tr>
	<th colspan='2'><img src='../images/icons/securite.png' alt='S�curit�' class='link'/> - S�curit�</th>
</tr>
<tr>
    <td width='200'><a href="gestion_connect.php">Gestion des connexions</a></td>
    <td>Affichage des connexions en cours, activation/d�sactivation des connexions pour le site, protection contre les attaques forces brutes, journal des connexions, changement de mot de passe obligatoire.
    </td>
</tr>
<tr>
    <td width='200'><a href="security_panel.php">Panneau de contr�le s�curit�</a></td>
    <td>Visualiser les tentatives d'utilisation ill�gale de Gepi.
    </td>
</tr>
<tr>
    <td width='200'><a href="security_policy.php">Politique de s�curit�</a></td>
    <td>D�finir les seuils d'alerte et les actions � entreprendre dans le cas de tentatives d'intrusion ou d'acc�s ill�gal � des ressources.
    </td>
</tr>
<tr>
	<td width="200"><a href="../mod_serveur/test_serveur.php">Configuration serveur</a></td>
	<td>Voir la configuration du serveur php/Mysql pour v&eacute;rifier la compatibilit&eacute; avec Gepi.</td>
</tr>
</table>

<table class='menu' summary='Menu g�n�ral'>
<tr>
	<th colspan='2'><img src='../images/icons/configure.png' alt='Configuration' class='link' /> - G�n�ral</th>
</tr>
<tr>
    <td width='200'><a href="param_gen.php">Configuration g�n�rale</a></td>
    <td>Permet de modifier des param�tres g�n�raux (nom de l'�tablissement, adresse, ...).
    </td>
</tr>
<tr>
    <td width='200'><a href="droits_acces.php">Droits d'acc�s</a></td>
    <td>Modifier les droits d'acc�s � certaines fonctionnalit�s selon le statut de l'utilisateur.
    </td>
</tr>
<tr>
    <td width='200'><a href="options_connect.php">Options de connexions</a></td>
    <td>Gestion de la proc�dure automatis�e de r�cup�ration de mot de passe, param�trage du mode de connexion (autonome ou Single Sign-On), changement de mot de passe obligatoire, r�glage de la dur�e de conservation des connexions, suppression de toutes les entr�es du journal de connexion.
    </td>
</tr>
<tr>
    <td width='200'><a href="modify_impression.php">Gestion de la fiche "bienvenue"</a></td>
    <td>Permet de modifier la feuille d'information � imprimer pour chaque nouvel utilisateur cr��.
    </td>
</tr>
<tr>
    <td width='200'><a href="config_prefs.php">Param�trage de l'interface <?php echo $gepiSettings['denomination_professeur']; ?></a></td>
    <td>Param�trage des items de l'interface simplifi�e pour certaines pages. Gestion du menu en barre horizontale.</td>
</tr>
<tr>
    <td width='200'><a href="param_couleurs.php">Param�trage des couleurs</a></td>
    <td>Param�trage des couleurs de fond d'�cran et du d�grad� d'ent�te.</td>
</tr>
</table>

<table class='menu' summary='Menu gestion des BDD'>
<tr>
	<th colspan='2'><img src='../images/icons/database.png' alt='Gestion bases de donn�es' class='link' /> - Gestion des bases de donn�es </th>
</tr>
<tr>
    <td width='200'><a href="accueil_sauve.php">Sauvegardes et restauration</a></td>
    <td>Sauvegarder la base GEPI sous la forme d'un fichier au format "mysql".<br />
    Restaurer des donn�es dans la base Mysql de GEPI � partir d'un fichier.
    </td>
</tr>
<tr>
    <td width='200'><a href="../utilitaires/maj.php">Mise � jour de la base</a></td>
    <td>Permet d'effectuer une mise � jour de la base MySql apr�s un changement de version  de GEPI.
    </td>
</tr>
<tr>
    <td width='200'><a href="../utilitaires/clean_tables.php">Nettoyage des tables</a></td>
    <td>Proc�der � un nettoyage des tables de la base MySql de GEPI (suppression de certains doublons et/ou lignes obsol�tes ou orphelines).
    </td>
</tr>
<tr>
    <td width='200'><a href="efface_base.php">Effacer la base</a></td>
    <td>Permet de r�initialiser les bases en effa�ant toutes les donn�es <?php echo $gepiSettings['denomination_eleves']; ?> de la base.
    </td>
</tr>
<tr>
    <td width='200'><a href="efface_photos.php">Effacer les photos</a></td>
    <td>Permet d'effacer les photos des <?php echo $gepiSettings['denomination_eleves']; ?> qui ne sont plus dans la base.</td>
</tr>
<tr>
    <td width='200'><a href="gestion_temp_dir.php">Gestion des dossiers temporaires</a></td>
    <td>Permet de contr�ler le volume occup� par les dossiers temporaires (<i>utilis�s notamment pour g�n�rer les fichiers tableur OpenOffice (ODS), lorsque la fonction est activ�e dans le module carnet de notes</i>), de supprimer ces dossiers,...</td>
</tr>

</table>

<table class='menu' summary='Menu initialisation'>
<tr>
<th colspan='2'><img src='../images/icons/package.png' alt='Initialisation' class='link' /> - Outils d'initialisation</th>
</tr>
<?php

if (LDAPServer::is_setup()) {
    ?>
<tr>
    <td width='200'><a href="../init_scribe/index.php">Initialisation � partir de l'annuaire LDAP du serveur Eole Scribe</a></td>
    <td>Permet d'importer les donn�es <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, mati�res directement depuis le serveur LDAP de Scribe.
    </td>
</tr>
<tr>
    <td width='200'><a href="../init_lcs/index.php">Initialisation � partir de l'annuaire LDAP du serveur LCS</a></td>
    <td>Permet d'importer les donn�es <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, mati�res directement depuis le serveur LDAP de LCS.
    </td>
</tr>
<?php
}
?>
<tr>
    <td width='200'><a href="../init_csv/index.php">Initialisation des donn�es � partir de fichiers CSV</a></td>
    <td>Permet d'importer les donn�es <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, mati�res depuis des fichiers CSV, par exemple des exports depuis Sconet.
    </td>
</tr>
<tr>
    <td width='200'><a href="../init_xml2/index.php">Initialisation des donn�es � partir de fichiers XML</a></td>
    <td>Permet d'importer les donn�es <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, mati�res depuis les exports XML de Sconet/STS.<br />
	<b>Nouvelle proc�dure:</b> Plus simple et moins gourmande en ressources que l'ancienne m�thode ci-dessous.
    </td>
</tr>
<tr>
    <td width='200'><a href="../init_xml/index.php">Initialisation des donn�es � partir de fichiers XML</a></td>
    <td>Permet d'importer les donn�es <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, mati�res depuis les exports XML de Sconet/STS.<br />
	<i>Les XML sont trait�s pour g�n�rer des fichiers CSV qui sont ensuite r�clam�s dans les diff�rentes �tapes de l'initialisation.</i>
    </td>
</tr>
<!--tr>
    <td width='200'><a href="../init_dbf_sts/index.php">Initialisation des donn�es � partir de fichiers DBF et XML</a> (OBSOLETE)</td>
    <td>Permet d'importer les donn�es <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, mati�res depuis deux fichiers DBF et l'export XML de STS.<br />
	<span style='color:red; '>Cette solution ne sera plus maintenue dans la future version 1.5.2 de Gepi.</span>
    </td>
</tr>
<tr>
    <td width='200'><a href="../initialisation/index.php">Initialisation des donn�es � partir des fichiers GEP</a> (OBSOLETE)</td>
    <td>Permet d'importer les donn�es <?php echo $gepiSettings['denomination_eleves']; ?>, classes, <?php echo $gepiSettings['denomination_professeurs']; ?>, mati�res depuis les fichiers GEP. Cette proc�dure est d�sormais obsol�te avec la g�n�ralisation de Sconet.<br />
	<span style='color:red; '>Cette solution ne sera plus maintenue dans la future version 1.5.2 de Gepi.</span>
    </td>
</tr-->
</table>
</center>
<?php require("../lib/footer.inc.php");?>