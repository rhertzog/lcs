<?php
/*
 * @version $Id: demande_ldap.inc.php 1713 2008-04-15 14:02:32Z jjocal $
 *
 * Ce fichier peut servir � faire une demande � un annuaire ldap pour qu'il renvoie une information
 * juste apr�s l'authentification CAS du fichier lib/cas.inc.php
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

//Connexion LDAP (connect)
$ds = ldap_connect("ent-ldap.ac-bordeaux.fr");

if ($ds){

	$sr = ldap_bind($ds); // connexion anonyme, typique
	// pour un acc�s en lecture seule.

	$dn = "o=personne,dc=ac-bordeaux,dc=fr, c=fr";
	//$uid = "uid=".phpCAS::getUser();
	$uid = "uid=".$login;

	//Interrogation LDAP (ldapsearch)
	$sr = ldap_search($ds, "ou=personnes,dc=ac-bordeaux,dc=fr", $uid) ;
	$info = ldap_get_entries($ds, $sr);

	// DEBUG
	//print_r($info);

	// Il faudra faire attention au cas o� un utilisateur a plusieurs �tablissements
	// Le nombre de RNE se trouve dans $info[0]["ou"][count]
	$RNE = $info[0]["ou"][0];


	//Fermeture connexion LDAP
	ldap_close($ds);
	// On relance le login.php mais avec le RNE de l'�tablissement et le ticket CAS
	header("Location: login.php?rne=".$RNE."&ticket=".$_GET["ticket"]."");

}else{

	echo '<h4>Impossible de se connecter au serveur LDAP.</h4>';

}
?>