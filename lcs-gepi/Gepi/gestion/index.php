<?php
/*
 * $Id: index.php 5121 2010-08-26 23:03:52Z regis $
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
 
 
/* ---------Variables envoy�es au gabarit
*
*	$tbs_menu
*				-> classe								classe CSS
*				-> image								icone du lien
*				-> texte								texte du titre du menu
*				-> entree								entr�es du menu
*							-> lien						lien vers la page
*							-> titre   				texte du lien
*							-> expli					explications
*	$niveau_arbo									Niveau dans l'arborescence
*	$titre_page										Titre de la page
*	$tbs_last_connection					Vide, pour ne pas avoir d'erreur dans le bandeau
*	$tbs_retour										Lien retour arri�re
*	$tbs_ariane										Fil d'arianne
*
*
*	Variables h�rit�es de :
*
*	header_template.inc
*	header_barre_prof_template.inc
*	footer_template.inc.php
*
 */

$niveau_arbo = 1;

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
// Begin standart header
$titre_page = "Gestion g�n�rale";
$tbs_last_connection="";

// ====== Inclusion des balises head et du bandeau =====
include_once("./../lib/header_template.inc");
// require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

/*
<!-- <p class='bold'><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a></p> -->
*/
	$tbs_retour="../accueil.php"; 
	$tbs_ariane[0]=array("titre" => "accueil" , "lien"=>"../accueil.php");
	
if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la cr�ation du fil d'ariane";

/*
<!-- 
<center>
-->
<!-- 
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
 -->
*/
	$nummenu=0;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/securite.png' , 'texte'=>"S�curit�");
	$chemin = array();
  $titre = array();
  $expli = array();
  
  $chemin = "gestion_connect.php";
  $titre = "Gestion des connexions";
  $expli = "Affichage des connexions en cours, activation/d�sactivation des connexions pour le site, protection contre les attaques forces brutes, journal des connexions, changement de mot de passe obligatoire.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "security_panel.php";
  $titre = "Panneau de contr�le s�curit�";
  $expli = "Visualiser les tentatives d'utilisation ill�gale de Gepi.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "security_policy.php";
  $titre = "Politique de s�curit�";
  $expli = "D�finir les seuils d'alerte et les actions � entreprendre dans le cas de tentatives d'intrusion ou d'acc�s ill�gal � des ressources.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "../mod_serveur/test_serveur.php";
  $titre = "Configuration serveur";
  $expli = "Voir la configuration du serveur php/Mysql pour v&eacute;rifier la compatibilit&eacute; avec Gepi.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);

/*
<!--
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
 -->
*/ 
 
	$nummenu=1;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/configure.png' , 'texte'=>"G�n�ral");
	$chemin = array();
  $titre = array();
  $expli = array();
  
  $chemin = "param_gen.php";
  $titre = "Configuration g�n�rale";
  $expli = "Permet de modifier des param�tres g�n�raux (nom de l'�tablissement, adresse, ...).";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "droits_acces.php";
  $titre = "Droits d'acc�s";
  $expli = "Modifier les droits d'acc�s � certaines fonctionnalit�s selon le statut de l'utilisateur.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "options_connect.php";
  $titre = "Options de connexions";
  $expli = "Gestion de la proc�dure automatis�e de r�cup�ration de mot de passe, param�trage du mode de connexion (autonome ou Single Sign-On), changement de mot de passe obligatoire, r�glage de la dur�e de conservation des connexions, suppression de toutes les entr�es du journal de connexion.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "modify_impression.php";
  $titre = "Gestion de la fiche \"bienvenue\"";
  $expli = "Permet de modifier la feuille d'information � imprimer pour chaque nouvel utilisateur cr��.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "config_prefs.php";
  $titre = "Param�trage de l'interface ".$gepiSettings['denomination_professeur'];
  $expli = "Param�trage des items de l'interface simplifi�e pour certaines pages. Gestion du menu en barre horizontale.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);

  $chemin = "param_couleurs.php";
  $titre = "Param�trage des couleurs";
  $expli = "Param�trage des couleurs de fond d'�cran et du d�grad� d'ent�te.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);

  $chemin = "param_ordre_item.php";
  $titre = "Param�trage de l'ordre des menus";
  $expli = "Param�trage de l'ordre des items dans les menus";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);

/*
<!--
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
 -->
*/
 
	$nummenu=2;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/database.png' , 'texte'=>"Gestion des bases de donn�es");
	$chemin = array();
  $titre = array();
  $expli = array();  

  $chemin = "accueil_sauve.php";
  $titre = "Sauvegardes et restauration";
  $expli = "Sauvegarder la base GEPI sous la forme d'un fichier au format \"mysql\".<br />
    Restaurer des donn�es dans la base Mysql de GEPI � partir d'un fichier.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "../utilitaires/maj.php";
  $titre = "Mise � jour de la base";
  $expli = "Permet d'effectuer une mise � jour de la base MySql apr�s un changement de version  de GEPI.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "../utilitaires/clean_tables.php";
  $titre = "Nettoyage des tables";
  $expli = "Proc�der � un nettoyage des tables de la base MySql de GEPI (suppression de certains doublons et/ou lignes obsol�tes ou orphelines).";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "efface_base.php";
  $titre = "Effacer la base";
  $expli = "Permet de r�initialiser les bases en effa�ant toutes les donn�es ".$gepiSettings['denomination_eleves']." de la base.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);

  if ($multisite != 'y') {
	$chemin = "efface_photos.php";
	$titre = "Effacer les photos";
	$expli = "Permet d'effacer les photos des ".$gepiSettings['denomination_eleves']." qui ne sont plus dans la base.";
	$tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  }
  
  $chemin = "gestion_temp_dir.php";
  $titre = "Gestion des dossiers temporaires";
  $expli = "Permet de contr�ler le volume occup� par les dossiers temporaires (<em>utilis�s notamment pour g�n�rer les fichiers tableur OpenOffice (ODS), lorsque la fonction est activ�e dans le module carnet de notes</em>), de supprimer ces dossiers,...";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli); 
  
  $chemin = "gestion_base_test.php";
  $titre = "Gestion des donn�es de test";
  $expli = "Permet d'inserer des donn�es de test dans la base. Ne pas utiliser sur une base de production.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);

/*
<!--
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
 -->
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
<!--
</table>
 -->
*/

	$nummenu=3;
	$tbs_menu[$nummenu]=array('classe'=>'accueil' , 'image'=>'../images/icons/package.png' , 'texte'=>"Outils d'initialisation");
	$chemin = array();
  $titre = array();
  $expli = array();
	
if (LDAPServer::is_setup()) {	
	
	$chemin="../init_scribe_ng/index.php";
	$titre = "Initialisation � partir de l'annuaire LDAP du serveur Eole Scribe NG";
	$expli = "Permet d'importer les donn�es ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", mati�res directement depuis le serveur LDAP de Scribe NG.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);

	$chemin="../init_lcs/index.php";
	$titre = "Initialisation � partir de l'annuaire LDAP du serveur LCS";
	$expli = "Permet d'importer les donn�es ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", mati�res directement depuis le serveur LDAP de LCS.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
    
}
	
	$chemin = "../init_csv/index.php";
	$titre = "Initialisation des donn�es � partir de fichiers CSV";
	$expli = "Permet d'importer les donn�es ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", mati�res depuis des fichiers CSV, par exemple des exports depuis Sconet.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "../init_xml2/index.php";
  $titre = "Initialisation des donn�es � partir de fichiers XML";
  $expli = "Permet d'importer les donn�es ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", mati�res depuis les exports XML de Sconet/STS.<br />
	<strong>Nouvelle proc�dure:</strong> Plus simple et moins gourmande en ressources que l'ancienne m�thode ci-dessous.";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
  
  $chemin = "../init_xml/index.php";
  $titre = "Initialisation des donn�es � partir de fichiers XML convertis en CSV";
  $expli = "Permet d'importer les donn�es ".$gepiSettings['denomination_eleves'].", classes, ".$gepiSettings['denomination_professeurs'].", mati�res depuis les exports XML de Sconet/STS.<br />
	<em>Les XML sont trait�s pour g�n�rer des fichiers CSV qui sont ensuite r�clam�s dans les diff�rentes �tapes de l'initialisation.</em>";
  $tbs_menu[$nummenu]['entree'][]=array('lien'=>$chemin , 'titre'=>$titre, 'expli'=>$expli);
	
	
/*
<!--
</center>
 -->
 */

	$tbs_microtime	="";
	$tbs_pmv="";
	require_once ("./../lib/footer_template.inc.php");

	
//==================================
// D�commenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
//debug_var();

include('./../templates/origine/gestion_generale_template.php');

?>
