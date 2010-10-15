<?php
/*
 * $Id: create_responsable.php 4070 2010-02-05 19:43:06Z adminpaulbert $
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

// Initialisation des variables
$create_mode = isset($_POST["mode"]) ? $_POST["mode"] : NULL;

$_POST['reg_auth_mode'] = (!isset($_POST['reg_auth_mode']) OR !in_array($_POST['reg_auth_mode'], array("auth_locale", "auth_ldap", "auth_sso"))) ? "auth_locale" : $_POST['reg_auth_mode'];

if ($create_mode == "classe" OR $create_mode == "individual") {
	// On a une demande de cr�ation, on continue

	// On veut alimenter la variable $quels_parents avec un r�sultat mysql qui contient
	// la liste des parents pour lesquels on veut cr�er un compte
	$error = false;
	$msg = "";
	if ($create_mode == "individual") {
		//echo "grouik : ".$_POST['pers_id'];
		// $_POST['pers_id'] est filtr� automatiquement contre les injections SQL, on l'utilise directement
		$test = mysql_query("SELECT count(e.login) FROM eleves e, responsables2 re WHERE (e.ele_id = re.ele_id AND re.pers_id = '" . $_POST['pers_id'] ."')");
		if (mysql_result($test, 0) == "0") {
			$error = true;
			$msg .= "Erreur lors de la cr�ation de l'utilisateur : aucune association avec un �l�ve n'a �t� trouv�e !<br/>";
		} else {
			//$quels_parents = mysql_query("SELECT r.* FROM resp_pers r, responsables2 re WHERE (" .
			//$sql="SELECT DISTINCT(r.*) FROM resp_pers r, responsables2 re WHERE (" .
			$sql="SELECT DISTINCT r.* FROM resp_pers r, responsables2 re WHERE (" .
				"r.login = '' AND " .
				"r.pers_id = re.pers_id AND " .
				"re.pers_id = '" . $_POST['pers_id'] ."')";
			//echo "$sql<br />";
			$quels_parents = mysql_query($sql);
		}
	} else {
		// On est en mode 'classe'
		if ($_POST['classe'] == "all") {
			/*
			$quels_parents = mysql_query("SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
					"r.pers_id = re.pers_id AND " .
					"re.ele_id = e.ele_id AND " .
					"e.login = jec.login AND " .
					"jec.id_classe = c.id)");
			*/
			$sql = "SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
					"r.pers_id = re.pers_id AND " .
					"re.ele_id = e.ele_id AND " .
					"e.login = jec.login AND " .
					"jec.id_classe = c.id AND " .
					"(re.resp_legal='1' OR re.resp_legal='2'))";
			//echo "$sql<br />";
			$quels_parents = mysql_query($sql);
			if (!$quels_parents) $msg .= mysql_error();
		} elseif (is_numeric($_POST['classe'])) {
			/*
			$quels_parents = mysql_query("SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
					"r.pers_id = re.pers_id AND " .
					"re.ele_id = e.ele_id AND " .
					"e.login = jec.login AND " .
					"jec.id_classe = '" . $_POST['classe']."')");
			*/
			$sql="SELECT distinct(r.pers_id), r.nom, r.prenom, r.civilite, r.mel " .
					"FROM resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
					"r.login = '' AND " .
					"r.pers_id = re.pers_id AND " .
					"re.ele_id = e.ele_id AND " .
					"e.login = jec.login AND " .
					"jec.id_classe = '" . $_POST['classe']."' AND " .
					"(re.resp_legal='1' OR re.resp_legal='2'))";
			//echo "$sql<br />";
			$quels_parents = mysql_query($sql);
			if (!$quels_parents) $msg .= mysql_error();
		} else {
			$error = true;
			$msg .= "Vous devez s�lectionner au moins une classe !<br />";
		}
	}

	if (!$error) {
		$nb_comptes = 0;
		while ($current_parent = mysql_fetch_object($quels_parents)) {

			// Dans le cas o� Gepi est int�gr� dans un ENT, on va chercher les logins
			if (getSettingValue("use_ent") == "y") {
				// Charge � l'organisme utilisateur de pourvoir � cette fonctionnalit�
				// le code suivant n'est qu'une m�thode propos�e pour relier Gepi � un ENT
				$bx = 'oui';
				if (isset($bx) AND $bx == 'oui') {
					// On va chercher le login de l'utilisateur dans la table cr��e
					// C'est � ce niveau qu'il faut faire les modifications

					$sql_p = "SELECT login_u FROM ldap_bx
											WHERE nom_u = '".strtoupper($current_parent->nom)."'
											AND prenom_u = '".strtoupper($current_parent->prenom)."'
											AND statut_u = 'teacher'";

					$query_p = mysql_query($sql_p);
					$nbre = mysql_num_rows($query_p);

					if ($nbre >= 1 AND $nbre < 2) {
						$reg_login = mysql_result($query_p, 0,"login_u");
					}else{
						// Il faudrait alors proposer une alternative � ce cas et permettre de chercher � la main le bon responsable dans la source
						//$reg_login = "erreur_".$k; // en attendant une solution viable, on g�n�re le login du responsable
            $reg_login = generate_unique_login($current_parent->nom, $current_parent->prenom, getSettingValue("mode_generation_login"));
					}
				}
			} else {
				// Cr�ation du compte utilisateur pour le responsable consid�r�
				//echo "\$reg_login = generate_unique_login($current_parent->nom, $current_parent->prenom, ".getSettingValue("mode_generation_login").");<br />\n";
				$reg_login = generate_unique_login($current_parent->nom, $current_parent->prenom, getSettingValue("mode_generation_login"));
				// generate_unique_login() peut retourner 'false' en cas de pb
			}

			if(($reg_login)&&($reg_login!='')) {
				// Si on a un acc�s LDAP en �criture, on cr�� le compte sur le LDAP
				// On ne proc�de que si le LDAP est configur� en �criture, qu'on a activ�
				// l'auth LDAP ou SSO, et que c'est un de ces deux modes qui a �t� choisi pour cet utilisateur.
				if (LDAPServer::is_setup() && $gepiSettings["ldap_write_access"] == "yes" && ($session_gepi->auth_ldap || $session_gepi->auth_sso) && ($_POST['reg_auth_mode'] == 'auth_ldap' || $_POST['reg_auth_mode'] == 'auth_sso')) {
					$write_ldap = true;
					$write_ldap_success = false;
					// On tente de cr�er l'utilisateur sur l'annuaire LDAP
					$ldap_server = new LDAPServer();
					if ($ldap_server->test_user($_POST['new_login'])) {
						// L'utilisateur a �t� trouv� dans l'annuaire. On ne l'enregistre pas.
						$write_ldap_success = true;
						$msg.= "L'utilisateur n'a pas pu �tre ajout� � l'annuaire LDAP, car il y est d�j� pr�sent. Il va n�anmoins �tre cr�� dans la base Gepi.";
					} else {
						$write_ldap_success = $ldap_server->add_user($reg_login, $current_parent->nom, $current_parent->prenom, $current_parent->mel, $current_parent->civilite, '', 'responsable');
					}
				} else {
					$write_ldap = false;
				}
	
				if (!$write_ldap || ($write_ldap && $write_ldap_success)) {
					$reg = true;
					if ($_POST['reg_auth_mode'] == "auth_locale") {
						$reg_auth = "gepi";
					} elseif ($_POST['reg_auth_mode'] == "auth_ldap") {
						$reg_auth = "ldap";
					} elseif ($_POST['reg_auth_mode'] == "auth_sso") {
						$reg_auth = "sso";
					}
					$sql="INSERT INTO utilisateurs SET " .
							"login = '" . $reg_login . "', " .
							"nom = '" . addslashes($current_parent->nom) . "', " .
							"prenom = '". addslashes($current_parent->prenom) ."', " .
							"password = '', " .
							"civilite = '" . $current_parent->civilite."', " .
							"email = '" . $current_parent->mel . "', " .
							"statut = 'responsable', " .
							"etat = 'actif', " .
							"auth_mode = '".$reg_auth."', " .
							"change_mdp = 'n'";
					//echo "$sql<br />";
					$reg = mysql_query($sql);
	
					if (!$reg) {
						$msg .= "Erreur lors de la cr�ation du compte ".$reg_login."<br/>";
					} else {
						$sql="UPDATE resp_pers SET login = '" . $reg_login . "' WHERE (pers_id = '" . $current_parent->pers_id . "')";
						$reg2 = mysql_query($sql);
						//$msg.="$sql<br />";
						$nb_comptes++;
					}
				} else {
					$msg .= "Erreur lors de la cr�ation du compte ".$reg_login." : l'utilisateur n'a pas pu �tre cr�� sur l'annuaire LDAP.<br/>";
				}
			}
			else {
				$msg .= "Erreur lors de la g�n�ration d'un login pour '$current_parent->nom $current_parent->prenom'.<br/>";
			}
		}
		if ($nb_comptes == 1) {
			$msg .= "Un compte a �t� cr�� avec succ�s.<br/>";
		} elseif ($nb_comptes > 1) {
			$msg .= $nb_comptes." comptes ont �t� cr��s avec succ�s.<br/>";
		}

		// On propose de mettre � z�ro les mots de passe et d'imprimer les fiches bienvenue seulement
		// si au moins un utilisateur a �t� cr�� et si on n'est pas en mode SSO (sauf acc�s LDAP en �criture).

		if ($nb_comptes > 0 && ($_POST['reg_auth_mode'] == "auth_locale" || $gepiSettings['ldap_write_access'] == "yes")) {
			if ($create_mode == "individual") {
				// Mode de cr�ation de compte individuel. On fait un lien sp�cifique pour la fiche de bienvenue
				$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_login=".$reg_login."'>";
				$msg .= "Pour initialiser le(s) mot(s) de passe, vous devez suivre ce lien maintenant !";
				$msg .= "</a>";
			} else {
				// On est ici en mode de cr�ation par classe
				// Si on op�re sur toutes les classes, on ne sp�cifie aucune classe
				// =====================
				if ($_POST['classe'] == "all") {
				    $msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=html&amp;creation_comptes_classe=y'>Imprimer la ou les fiche(s) de bienvenue (Impression HTML)</a>";
				    $msg .= " ou <a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=html&amp;affiche_adresse_resp=y&amp;creation_comptes_classe=y'>(Impression HTML avec adresse)</a>";
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;mode=csv&amp;creation_comptes_classe=y'>Imprimer la ou les fiche(s) de bienvenue (Export CSV)</a>";
					$msg.="<br/>";
				} elseif (is_numeric($_POST['classe'])) {
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=html&amp;creation_comptes_classe=y'>Imprimer la ou les fiche(s) de bienvenue (Impression HTML)</a>";
					$msg .= " ou <a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=html&amp;affiche_adresse_resp=y&amp;creation_comptes_classe=y'>(Impression HTML avec adresse)</a>";
					$msg .= "<br/><a target='_blank' href='reset_passwords.php?user_status=responsable&amp;user_classe=".$_POST['classe']."&amp;mode=csv&amp;creation_comptes_classe=y'>Imprimer la ou les fiche(s) de bienvenue (Export CSV)</a>";
					$msg.="<br/>";
				}
				// =====================
				$msg .= "Pour initialiser le(s) mot(s) de passe, vous devez suivre ce lien maintenant !";
			}
			// =====================
			// MODIF: boireaus 20071102
			//$msg .= "<br/>Vous devez effectuer cette op�ration maintenant !";
			//$msg .= "Pour initialiser le(s) mot(s) de passe, vous devez suivre ce lien maintenant !";
			// =====================
		} else {
			if ($nb_comptes > 0) {
				$msg .= "Vous avez cr�� des comptes d'acc�s en mode SSO ou LDAP, mais sans avoir configur� l'acc�s LDAP en �criture. En cons�quence, vous ne pouvez pas g�n�rer de mot de passe pour les utilisateurs.<br/>";
			}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Cr�er des comptes d'acc�s responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="edit_responsable.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<?php

$afficher_tous_les_resp=isset($_POST['afficher_tous_les_resp']) ? $_POST['afficher_tous_les_resp'] : "n";
$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
$critere_recherche=my_ereg_replace("[^a-zA-Z�������������ܽ�����������������_ -]", "", $critere_recherche);

//$quels_parents = mysql_query("SELECT * FROM resp_pers WHERE login='' ORDER BY nom,prenom");
//$quels_parents = mysql_query("SELECT * FROM resp_pers WHERE login='' ORDER BY nom,prenom");
//$sql="SELECT * FROM resp_pers rp WHERE rp.login=''";

// Effectif total sans login:
//$sql="SELECT 1=1 FROM resp_pers rp WHERE rp.login=''";
$sql="SELECT DISTINCT rp.pers_id FROM resp_pers rp, responsables2 r WHERE rp.login='' AND rp.pers_id=r.pers_id AND (r.resp_legal='1' OR r.resp_legal='2');";
$nb = mysql_num_rows(mysql_query($sql));

$sql="SELECT * FROM resp_pers rp WHERE rp.login=''";

if($afficher_tous_les_resp!='y'){
	if($critere_recherche!=""){
		$sql.=" AND rp.nom like '%".$critere_recherche."%'";
	}
}
$sql.=" ORDER BY rp.nom, rp.prenom";

// Effectif sans login avec filtrage sur le nom:
$nb1 = mysql_num_rows(mysql_query($sql));

if($afficher_tous_les_resp!='y'){
	if($critere_recherche==""){
		$sql.=" LIMIT 20";
	}
}
//echo "$sql<br />\n";
$quels_parents = mysql_query($sql);


/*
$sql="SELECT rp.*, e.nom as ele_nom, e.prenom as ele_prenom,c.classe
						FROM resp_pers rp, responsables2 r, eleves e, j_eleves_classes jec, classes c
						WHERE rp.login='' AND
							rp.pers_id=r.pers_id AND
							r.ele_id=e.ele_id AND
							(re.resp_legal='1' OR re.resp_legal='2') AND
							jec.login=e.login AND
							jec.id_classe=c.id
						ORDER BY rp.nom,rp.prenom";
$quels_parents = mysql_query($sql);
*/

//$nb = mysql_num_rows($quels_parents);

// Effectif sans login avec filtrage sur le nom et limitation � un max de 20:
$nb2 = mysql_num_rows($quels_parents);

if($nb==0){
	echo "<p>Tous les responsables ont un login, ou bien aucun responsable n'est pr�sent dans la base de Gepi.</p>\n";
}
else{
	//echo "<p>Les $nb responsables ci-dessous n'ont pas encore de compte utilisateur.</p>\n";
	echo "<p>$nb responsables n'ont pas encore de compte utilisateur.</p>\n";
	echo "<p><em>Note : vous ne pouvez cr�er de comptes d'acc�s que pour les responsables d'�l�ves associ�s � des classes.</em></p>\n";

	if (getSettingValue("mode_generation_login") == null) {
		echo "<p><b>ATTENTION !</b> Vous n'avez pas d�fini le mode de g�n�ration des logins. Allez sur la page de <a href='../gestion/param_gen.php'>gestion g�n�rale</a> pour d�finir le mode que vous souhaitez utiliser. Par d�faut, les logins seront g�n�r�s au format pnom tronqu� � 8 caract�res (ex: ADURANT).</p>\n";
	}
	if (!$session_gepi->auth_locale && $gepiSettings['ldap_write_access'] != "yes") {
		echo "<p><b>Note :</b> Vous utilisez une authentification externe � Gepi (LDAP ou SSO) sans avoir d�fini d'acc�s en �criture � l'annuaire LDAP. Aucun mot de passe ne sera donc assign� aux utilisateurs que vous vous appr�tez � cr�er. Soyez certain de g�n�rer les login selon le m�me format que pour votre source d'authentification SSO.</p>\n";
	}

	echo "<p><b>Cr�er des comptes par lot</b> :</p>\n";
	echo "<blockquote>\n";
	echo "<form action='create_responsable.php' method='post'>\n";
	echo "<input type='hidden' name='mode' value='classe' />\n";
	//===========================
	// AJOUT: boireaus 20071102
	echo "<input type='hidden' name='creation_comptes_classe' value='y' />\n";
	//===========================
	echo "<p>S�lectionnez le mode d'authentification appliqu� aux comptes :</p>";

	echo "<select name='reg_auth_mode' size='1'>";
	if ($session_gepi->auth_locale) {
		echo "<option value='auth_locale'>Authentification locale (base Gepi)</option>";
	}
	if ($session_gepi->auth_ldap) {
		echo "<option value='auth_ldap'>Authentification LDAP</option>";
	}
	if ($session_gepi->auth_sso) {
		echo "<option value='auth_sso'>Authentification unique (SSO)</option>";
	}
	echo "</select>";

	echo "<p>S�lectionnez une classe ou bien l'ensemble des classes puis cliquez sur 'valider'.</p>\n";

	echo "<select name='classe' size='1'>\n";
	echo "<option value='none'>S�lectionnez une classe</option>\n";
	echo "<option value='all'>Toutes les classes</option>\n";

	$quelles_classes = mysql_query("SELECT id,classe FROM classes ORDER BY classe");
	while ($current_classe = mysql_fetch_object($quelles_classes)) {
		echo "<option value='".$current_classe->id."'>".$current_classe->classe."</option>\n";
	}
	echo "</select>\n";
	echo "<input type='submit' name='Valider' value='Valider' />\n";
	echo "</form>\n";


	include("randpass.php");

	echo "<p style='font-size:small;'>Lors de la cr�ation, les comptes re�oivent un mot de passe al�atoire choisi parmi les caract�res suivants: ";
	if (LOWER_AND_UPPER) {
		if(EXCLURE_CARACT_CONFUS) {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		}
		else {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		}
	} else {
		if(EXCLURE_CARACT_CONFUS) {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		}
		else {
			$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		}
	}
	$cpt=0;
	foreach($alphabet as $key => $value) {
		if($cpt>0) {echo ", ";}
		echo $value;
		$cpt++;
	}

	if(EXCLURE_CARACT_CONFUS) {
		$cpt=2;
	}
	else {
		$cpt=0;
	}
	for($i=$cpt;$i<=9;$i++) {
		echo ", $i";
	}
	echo ".</p>\n";

	echo "<br />\n";

	echo "</blockquote>\n";

	//echo "<br />\n";
	echo "<p><b>Cr�er des comptes individuellement</b> :</p>\n";
	echo "<blockquote>\n";

	echo "<p>";
	if(($afficher_tous_les_resp!='y')&&($critere_recherche=="")){
		echo "Au plus $nb2 responsables sont affich�s ci-dessous (<i>pour limiter le temps de chargement de la page</i>).<br />\n";
	}
	echo "Utilisez le formulaire de recherche pour adapter la recherche.";
	echo "</p>\n";

	//===================================
	//echo "<div style='border:1px solid black;'>\n";
	echo "<form enctype='multipart/form-data' name='form_rech' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<table style='border:1px solid black;' summary=\"Filtrage\">\n";
	echo "<tr>\n";
	echo "<td valign='top' rowspan='3'>\n";
	echo "Filtrage:";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='submit' name='filtrage' value='Afficher' /> les responsables sans login dont le <b>nom</b> contient: ";
	echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "ou";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<input type='button' name='afficher_tous' value='Afficher tous les responsables sans login' onClick=\"document.getElementById('afficher_tous_les_resp').value='y'; document.form_rech.submit();\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='afficher_tous_les_resp' id='afficher_tous_les_resp' value='n' />\n";
	echo "</form>\n";
	//echo "</div>\n";
	//===================================
	echo "<br />\n";

	echo "<p>Cliquez sur le bouton 'Cr�er' d'un responsable pour cr�er un compte associ�.</p>\n";
	echo "<form id='form_create_one_resp' action='create_responsable.php' method='post'>\n";
	echo "<input type='hidden' name='mode' value='individual' />\n";
	echo "<input id='create_pers_id' type='hidden' name='pers_id' value='' />\n";

	echo "<input type='hidden' name='critere_recherche' value='$critere_recherche' />\n";
	echo "<input type='hidden' name='afficher_tous_les_resp' value='$afficher_tous_les_resp' />\n";

	// S�lection du mode d'authentification
	echo "<p>Mode d'authentification : <select name='reg_auth_mode' size='1'>";
	if ($session_gepi->auth_locale) {
		echo "<option value='auth_locale'>Authentification locale (base Gepi)</option>";
	}
	if ($session_gepi->auth_ldap) {
		echo "<option value='auth_ldap'>Authentification LDAP</option>";
	}
	if ($session_gepi->auth_sso) {
		echo "<option value='auth_sso'>Authentification unique (SSO)</option>";
	}
	echo "</select>";
	echo "</p>";


	echo "<table class='boireaus' border='1' summary=\"Cr�er\">\n";
	$alt=1;
	while ($current_parent = mysql_fetch_object($quels_parents)) {

		$sql="SELECT DISTINCT e.ele_id, e.nom, e.prenom, c.classe, r.resp_legal
				FROM responsables2 r, eleves e, j_eleves_classes jec, classes c
				WHERE r.pers_id='".$current_parent->pers_id."' AND
					(r.resp_legal='1' OR r.resp_legal='2') AND
					r.ele_id=e.ele_id AND
					jec.login=e.login AND
					jec.id_classe=c.id";
		//echo "$sql<br />";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
				//echo "<td valign='top'>\n";
				echo "<td>\n";
				echo "<input type='submit' value='Cr�er' onclick=\"$('create_pers_id').value='".$current_parent->pers_id."'; $('form_create_one_resp').submit();\" />\n";
				echo "<td>".strtoupper($current_parent->nom)." ".ucfirst(strtolower($current_parent->prenom))."</td>\n";
				echo "<td>\n";
				while($lig_ele=mysql_fetch_object($test)){
					echo "Responsable l�gal $lig_ele->resp_legal de ".ucfirst(strtolower($lig_ele->prenom))." ".strtoupper($lig_ele->nom)." (<i>$lig_ele->classe</i>)<br />\n";
				}
				echo "</td>\n";
			echo "</tr>\n";
		}
	}
	echo "</table>\n";
	echo "</form>";
	echo "</blockquote>\n";
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>