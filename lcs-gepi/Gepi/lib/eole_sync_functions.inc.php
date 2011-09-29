<?php
 
 function add_user($_login, $_nom, $_prenom, $_civilite, $_statut, $_email = null) {
 	// Fonction d'ajout de l'utilisateur
	// On fait confiance ici aux valeurs retourn�es par le LDAP, donc pas de filtrage. 	
 		if ($_civilite == 1) {
 			$_civilite = "M.";
 		} elseif ($_civilite == 2) {
 			$_civilite = "Mme";
 		} elseif ($_civilite == 3) {
 			$_civilite = "Mlle";
 		} else {
 			$_civilite = "Mme";
 		}

 	// Si l'authentification CAS est configur�e, alors l'utilisateur sera mis en mode SSO
 	// Sinon il sera en mode Gepi classique...
 	if ($session_gepi->auth_sso == "cas") {
 		$auth_mode = "sso";
 	} else {
 		$auth_mode = "gepi";
 	}
 		
 	// Si l'utilisateur existe d�j�, on met simplement � jour ses informations...
 	$test = mysql_query("SELECT login FROM utilisateurs WHERE login = '" . $_login . "'");
 	if (mysql_num_rows($test) > 0) {
 		$record = mysql_query("UPDATE utilisateurs SET nom = '" . $_nom . "', prenom = '" . $_prenom . "', civilite = '" . $_civilite . "', statut = '" . $_statut . "', email = '" . $_email . "', auth_mode = '".$auth_mode."', etat = 'actif' WHERE login = '" . $_login . "'");
 	} else {
		$query = "INSERT into utilisateurs SET login= '" . $_login . "', nom = '" . $_nom . "', prenom = '" . $_prenom . "', password = '', salt = '', civilite = '" . $_civilite . "', statut = '" . $_statut . "', email = '" . $_email . "', auth_mode = '".$auth_mode."', etat ='actif', change_mdp = 'n'";
		$record = mysql_query($query);
 	}

	if ($record) {
		return true;
	} else {
		return false;
	} 	
 }
 
  function add_eleve($_login, $_nom, $_prenom, $_civilite, $_naissance, $_elenoet = 0) {
 	// Fonction d'ajout d'un �l�ve dans la base Gepi
 	
 	if ($_civilite != "M" && $_civilite != "F") {
 		if ($_civilite == 1) {
 			$_civilite = "M";
 		} else {
 			$_civilite = "F";
 		}
 	}
 	
 	// Si l'�l�ve existe d�j�, on met simplement � jour ses informations...
 	$test = mysql_query("SELECT login FROM eleves WHERE login = '" . $_login . "'");
 	if (mysql_num_rows($test) > 0) {
 		$record = mysql_query("UPDATE eleves SET nom = '" . $_nom . "', prenom = '" . $_prenom . "', sexe = '" . $_civilite . "', naissance = '" . $_naissance . "', elenoet = '" . $_elenoet . "' WHERE login = '" . $_login . "'");
 	} else {
		$query = "INSERT into eleves SET login= '" . $_login . "', nom = '" . $_nom . "', prenom = '" . $_prenom . "', sexe = '" . $_civilite . "', naissance = '" . $_naissance . "', elenoet = '" . $_elenoet . "'";
		$record = mysql_query($query);
 	}

	if ($record) {
		return true;
	} else {
		return false;
	} 	
 }
 
 
?>
