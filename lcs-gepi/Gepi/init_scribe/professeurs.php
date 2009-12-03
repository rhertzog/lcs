<?php
/*
 * Last modification  : 10/05/2006
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$titre_page = "Outil d'initialisation de l'ann�e : Importation des professeurs";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='../init_scribe/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (isset($_POST['is_posted'])) {
	// L'admin a valid� la proc�dure, on proc�de donc...
	include "../lib/eole_sync_functions.inc.php";
	// On commence par r�cup�rer tous les profs depuis le LDAP
	$ldap_server = new LDAPServer;
	$sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(&(uid=*)(objectclass=administrateur))");
	$info = ldap_get_entries($ldap_server->ds,$sr);
	
	// On met tous les professeurs en �tat inactif
	$update = mysql_query("UPDATE utilisateurs SET etat='inactif' WHERE statut='professeur'");
	
	for($i=0;$i<$info["count"];$i++) {
		
		// On ajoute l'utilisateur. La fonction s'occupe toute seule de v�rifier que
		// le login n'existe pas d�j� dans la base. S'il existe, on met simplement � jour
		// les informations
		
		// function add_user($_login, $_nom, $_prenom, $_civilite, $_statut, $_email) {
		
		// Scribe NG : ne pas modifier l'utilisateur admin
		if ($info[$i]["uid"][0] == "admin") {
			continue;
		}
		
		
		// Le mail et le code civilit� ne sont pas syst�matiquement renseign�s...
		
		if (!array_key_exists("mail", $info[$i])) {
			$info[$i]["mail"] = array();
			$info[$i]["mail"][0] = null;
		}
		
		if (!array_key_exists("codecivilite", $info[$i])) {
			$info[$i]["codecivilite"] = array();
			$info[$i]["codecivilite"][0] = 1;
		}
		
		$add = add_user($info[$i]["uid"][0],
						$info[$i]["sn"][0],
						$info[$i]["givenname"][0],
						$info[$i]["codecivilite"][0],
						"professeur",
						$info[$i]["mail"][0]
						);
					
						// Debug :
						//echo "<pre>";
						//print_r($info[$i]);
						//echo "</pre><br/><br/>";
	}
	
	echo "<p>Op�ration effectu�e.</p>";
	echo "<p>Vous pouvez v�rifier l'importation en allant sur la page de <a href='../utilisateurs/index.php'>gestion des utilisateurs</a>.</p>";
	echo "<br />";
	echo "<p><center><a href='disciplines.php'>Phase suivante : importation des mati�res</a></center></p>";
	
} else {
	echo "<p>L'op�ration d'importation des professeurs depuis le LDAP de Scribe va effectuer les op�rations suivantes :</p>";
	echo "<ul>";
	echo "<li>Passage � l'�tat 'inactif' de tous les professeurs d�j� pr�sents dans la base Gepi</li>";
	echo "<li>Tentative d'ajout de chaque utilisateur 'professeur' pr�sent dans le LDAP</li>";
	echo "<li>Si l'utilisateur n'existe pas, il est cr�� et est directement utilisable</li>";
	echo "<li>Si l'utilisateur existe d�j�, ses informations de base sont mises � jour et il passe en �tat 'actif', devenant directement utilisable</li>";
	echo "</ul>";
	echo "<form enctype='multipart/form-data' action='professeurs.php' method=post>";
    echo "<input type=hidden name='is_posted' value='yes'>";
    
    echo "<p>Etes-vous s�r de vouloir importer tous les utilisateurs depuis l'annuaire du serveur Scribe vers Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis s�r'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>