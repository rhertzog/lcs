<?php
/*
 * $Id: create_eleve.php 6617 2011-03-03 18:18:36Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
}


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Initialisation des variables

$create_mode = isset($_POST["mode"]) ? $_POST["mode"] : NULL;
$_POST['reg_auth_mode'] = (!isset($_POST['reg_auth_mode']) OR !in_array($_POST['reg_auth_mode'], array("auth_locale", "auth_ldap", "auth_sso"))) ? "auth_locale" : $_POST['reg_auth_mode'];

$mdp_INE=isset($_POST["mdp_INE"]) ? $_POST["mdp_INE"] : (isset($_GET["mdp_INE"]) ? $_GET["mdp_INE"] : NULL);

if ($create_mode == "classe" OR $create_mode == "individual") {
	// On a une demande de cr�ation, on continue
	check_token();

	// On veut alimenter la variable $quels_eleves avec un r�sultat mysql qui contient
	// la liste des �l�ves pour lesquels on veut cr�er un compte
	$error = false;
	$msg = "";
	if ($create_mode == "individual") {
		$test = mysql_query("SELECT count(e.login) FROM eleves e WHERE (e.login = '" . $_POST['eleve_login'] ."')");
		if (mysql_result($test, 0) == "0") {
			$error = true;
			$msg .= "Erreur lors de la cr�ation de l'utilisateur : aucun �l�ve avec ce login n'a �t� trouv� !<br />";
		} else {
			$quels_eleves = mysql_query("SELECT e.* FROM eleves e WHERE (" .
				"e.login = '" . $_POST['eleve_login'] ."')");
		}
	} else {
		// On est en mode 'classe'
		if ($_POST['classe'] == "all") {
			$quels_eleves = mysql_query("SELECT distinct(e.login), e.nom, e.prenom, e.sexe, e.email " .
					"FROM classes c, j_eleves_classes jec, eleves e WHERE (" .
					"e.login = jec.login AND " .
					"jec.id_classe = c.id)");
			if (!$quels_eleves) $msg .= mysql_error();
		} elseif (is_numeric($_POST['classe'])) {
			$quels_eleves = mysql_query("SELECT distinct(e.login), e.nom, e.prenom, e.sexe, e.email " .
					"FROM classes c, j_eleves_classes jec, eleves e WHERE (" .
					"e.login = jec.login AND " .
					"jec.id_classe = '" . $_POST['classe']."')");
			if (!$quels_eleves) $msg .= mysql_error();
		} else {
			$error = true;
			$msg .= "Vous devez s�lectionner au moins une classe !<br />";
		}
	}

	if (!$error) {
		//check_token();

		$nb_comptes_preexistants=0;

		$nb_comptes = 0;
		while ($current_eleve = mysql_fetch_object($quels_eleves)) {
			// Cr�ation du compte utilisateur pour l'�l�ve consid�r�
			$reg = true;
			$civilite = '';
			if ($current_eleve->sexe == "M") {
				$civilite = "M.";
			} elseif ($current_eleve->sexe == "F") {
				$civilite = "Mlle";
			}

			// Si on a un acc�s LDAP en �criture, on cr�� le compte sur le LDAP
			// On ne proc�de que si le LDAP est configur� en �criture, qu'on a activ�
			// l'auth LDAP ou SSO, et que c'est un de ces deux modes qui a �t� choisi pour cet utilisateur.
			if (LDAPServer::is_setup() && $gepiSettings["ldap_write_access"] == "yes" && ($session_gepi->auth_ldap || $session_gepi->auth_sso) && ($_POST['reg_auth_mode'] == 'auth_ldap' || $_POST['reg_auth_mode'] == 'auth_sso')) {
				$write_ldap = true;
				$write_ldap_success = false;
				// On tente de cr�er l'utilisateur sur l'annuaire LDAP
				$ldap_server = new LDAPServer();
				if ($ldap_server->test_user($current_eleve->login)) {
					// L'utilisateur a �t� trouv� dans l'annuaire. On ne l'enregistre pas.
					$write_ldap_success = true;
					$msg.= "L'utilisateur n'a pas pu �tre ajout� � l'annuaire LDAP, car il y est d�j� pr�sent. Il va n�anmoins �tre cr�� dans la base Gepi.";
				} else {
					$write_ldap_success = $ldap_server->add_user($current_eleve->login, $current_eleve->nom, $current_eleve->prenom, $current_eleve->email, $civilite, '', 'eleve');
				}
			} else {
				$write_ldap = false;
			}


			if (!$write_ldap || ($write_ldap && $write_ldap_success)) {
				if ($_POST['reg_auth_mode'] == "auth_locale") {
					$reg_auth = "gepi";
				} elseif ($_POST['reg_auth_mode'] == "auth_ldap") {
					$reg_auth = "ldap";
				} elseif ($_POST['reg_auth_mode'] == "auth_sso") {
					$reg_auth = "sso";
				}

				$sql="SELECT 1=1 FROM utilisateurs WHERE login='".$current_eleve->login."';";
				//echo "$sql<br />";
				$test_existence_compte=mysql_query($sql);
				if(mysql_num_rows($test_existence_compte)==0) {
					$reg = mysql_query("INSERT INTO utilisateurs SET " .
							"login = '" . $current_eleve->login . "', " .
							"nom = '" . addslashes($current_eleve->nom) . "', " .
							"prenom = '". addslashes($current_eleve->prenom) ."', " .
							"password = '', " .
							"civilite = '" . $civilite."', " .
							"email = '" . $current_eleve->email . "', " .
							"statut = 'eleve', " .
							"etat = 'actif', " .
							"auth_mode = '".$reg_auth."', ".
							"change_mdp = 'n'");

					if (!$reg) {
						$msg .= "Erreur lors de la cr�ation du compte ".$current_eleve->login."<br />";
					} else {
						$nb_comptes++;
					}
				}
				else {
					// On compte les comptes existants
					$nb_comptes_preexistants++;
				}
			} else {
				$msg .= "Erreur lors de la cr�ation du compte ".$current_eleve->login." : l'utilisateur n'a pas pu �tre cr�� sur l'annuaire LDAP.<br />";

			}
		}
		if ($nb_comptes == 1) {
			$msg .= "Un compte a �t� cr�� avec succ�s.<br />";
		} elseif ($nb_comptes > 1) {
			$msg .= $nb_comptes." comptes ont �t� cr��s avec succ�s.<br />";
		}
		if ($nb_comptes > 0 && ($_POST['reg_auth_mode'] == "auth_locale" || $gepiSettings['ldap_write_access'] == "yes")) {

			if(isset($mdp_INE)) {
				$chaine_mdp_INE="&amp;mdp_INE=$mdp_INE";
			}
			else {
				$chaine_mdp_INE="";
			}

			if ($create_mode == "individual") {
				// Mode de cr�ation de compte individuel. On fait un lien sp�cifique pour la fiche de bienvenue
	            $msg .= "<a href='reset_passwords.php?user_login=".$_POST['eleve_login']."$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Imprimer la fiche 'identifiants'</a>";
			} else {
				// On est ici en mode de cr�ation par classe
				// Si on op�re sur toutes les classes, on ne sp�cifie aucune classe

            	if ($_POST['classe'] == "all") {
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;mode=html$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Imprimer la ou les fiche(s) 'identifiants' (Impression HTML)</a>";
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;mode=csv$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Imprimer la ou les fiche(s) 'identifiants' (Export CSV)</a>";
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;mode=pdf$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Imprimer la ou les fiche(s) 'identifiants' (Impression PDF)</a>";
				} elseif (is_numeric($_POST['classe'])) {
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;user_classe=".$_POST['classe']."&amp;mode=html$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Imprimer la ou les fiche(s) 'identifiants' (Impression HTML)</a>";
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;user_classe=".$_POST['classe']."&amp;mode=csv$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Imprimer la ou les fiche(s) 'identifiants' (Export CSV)</a>";
				    $msg .= "<br /><a href='reset_passwords.php?user_status=eleve&amp;user_classe=".$_POST['classe']."&amp;mode=pdf$chaine_mdp_INE".add_token_in_url()."' target='_blank'>Imprimer la ou les fiche(s) 'identifiants' (Impression PDF)</a>";
				}
			}
			$msg .= "<br />Vous devez effectuer cette op�ration maintenant !";
		} else {
			if ($nb_comptes > 0) {
				$msg .= "Vous avez cr�� des comptes d'acc�s en mode SSO ou LDAP, mais sans avoir configur� l'acc�s LDAP en �criture. En cons�quence, vous ne pouvez pas g�n�rer de mot de passe pour les utilisateurs.<br />";
			}
		}


		if($nb_comptes_preexistants>0) {
			if($nb_comptes_preexistants==1) {
				$msg.="Un compte existait d�j� pour la s�lection.<br />";
			}
			else {
				$msg.="$nb_comptes_preexistants comptes existaient d�j� pour la s�lection.<br />";
			}
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Cr�er des comptes d'acc�s �l�ves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var();
?>
<p class='bold'>
<a href="edit_eleve.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<?php
$quels_eleves = mysql_query("SELECT e.* FROM eleves e LEFT JOIN utilisateurs u ON e.login=u.login WHERE (" .
		"u.login IS NULL) " .
		"ORDER BY e.nom,e.prenom");
$nb = mysql_num_rows($quels_eleves);
if($nb==0){
	echo "<p>Tous les �l�ves ont un compte utilisateur, ou bien aucun �l�ve n'a encore �t� cr��.</p>\n";
}
else{
	//echo "<p>Les $nb �l�ves ci-dessous n'ont pas encore de compte d'acc�s � Gepi.</p>\n";
	echo "<p>$nb �l�ves n'ont pas encore de compte d'acc�s � Gepi.</p>\n";

	if (!$session_gepi->auth_locale && $gepiSettings['ldap_write_access'] != "yes") {
		echo "<p><b>Note :</b> Vous utilisez une authentification externe � Gepi (LDAP ou SSO) sans avoir d�fini d'acc�s en �criture � l'annuaire LDAP. Aucun mot de passe ne sera donc assign� aux utilisateurs que vous vous appr�tez � cr�er. Soyez certain de g�n�rer les login selon le m�me format que pour votre source d'authentification SSO.</p>\n";
	}

	echo "<p><b>Cr�er des comptes par lot</b> :</p>\n";
	echo "<blockquote>\n";

	echo "<p>S�lectionnez le mode d'authentification appliqu� aux comptes :</p>";
	echo "<form action='create_eleve.php' method='post'>\n";
	echo add_token_field();
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


	echo "<input type='hidden' name='mode' value='classe' />\n";
	echo "<select name='classe' size='1'>\n";
	echo "<option value='none'>S�lectionnez une classe</option>\n";
	echo "<option value='all'>Toutes les classes</option>\n";

	$quelles_classes = mysql_query("SELECT id,classe FROM classes ORDER BY classe");
	while ($current_classe = mysql_fetch_object($quelles_classes)) {
		echo "<option value='".$current_classe->id."'>".$current_classe->classe."</option>\n";
	}
	echo "</select>\n";

	echo "<input type='submit' name='Valider' value='Valider' />\n";

	echo "<p><input type='checkbox' name='mdp_INE' id='mdp_INE' value='y' /> <label for='mdp_INE' style='cursor:pointer'>Utiliser le num�ro national de l'�l�ve (<i>INE</i>) comme mot de passe initial lorsqu'il est renseign�.</label></p>\n";

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

	echo "</blockquote>\n";

	echo "<br />\n";



	echo "<p><b>Cr�er des comptes individuellement</b> :</p>\n";
	echo "<blockquote>\n";

	$afficher_tous_les_eleves=isset($_POST['afficher_tous_les_eleves']) ? $_POST['afficher_tous_les_eleves'] : "n";
	$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
	$critere_recherche=preg_replace("/[^a-zA-Z�������������ܽ�����������������_ -]/", "", $critere_recherche);


	$sql="SELECT e.* FROM eleves e LEFT JOIN utilisateurs u ON e.login=u.login WHERE (u.login IS NULL";
	if($afficher_tous_les_eleves!='y'){
		if($critere_recherche!=""){
			$sql.=" AND e.nom like '%".$critere_recherche."%'";
		}
	}
	$sql.=") ORDER BY e.nom,e.prenom";
	if($afficher_tous_les_eleves!='y'){
		if($critere_recherche==""){
			$sql.=" LIMIT 20";
		}
	}
	//echo "$sql<br />";
	$quels_eleves = mysql_query($sql);
	$nb2=mysql_num_rows($quels_eleves);


	echo "<p>";
	if(($afficher_tous_les_eleves!='y')&&($critere_recherche=="")){
		echo "Au plus $nb2 �l�ves sont affich�s ci-dessous (<i>pour limiter le temps de chargement de la page</i>).<br />\n";
	}
	echo "Utilisez le formulaire de recherche pour adapter la recherche.";
	echo "</p>\n";

	//====================================
	echo "<form enctype='multipart/form-data' name='form_rech' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<table style='border:1px solid black;' summary=\"Filtrage\">\n";
	echo "<tr>\n";
	echo "<td valign='top' rowspan='3'>\n";
	echo "Filtrage:";
	echo "</td>\n";
	echo "<td>\n";
	echo "<input type='submit' name='filtrage' value='Afficher' /> les �l�ves sans login dont le <b>nom</b> contient: ";
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
	echo "<input type='button' name='afficher_tous' value='Afficher tous les �l�ves sans login' onClick=\"document.getElementById('afficher_tous_les_eleves').value='y'; document.form_rech.submit();\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<input type='hidden' name='afficher_tous_les_eleves' id='afficher_tous_les_eleves' value='n' />\n";
	echo "</form>\n";
	//====================================
	echo "<br />\n";


	echo "<p>Cliquez sur le bouton 'Cr�er' d'un �l�ve pour cr�er un compte associ�.</p>\n";
	echo "<form id='form_create_one_eleve' action='create_eleve.php' method='post'>\n";
	echo add_token_field();
	echo "<input type='hidden' name='mode' value='individual' />\n";
	echo "<input type='hidden' name='mdp_INE' id='indiv_mdp_INE' value='' />\n";
	echo "<input id='eleve_login' type='hidden' name='eleve_login' value='' />\n";
	echo "<input type='hidden' name='critere_recherche' value='$critere_recherche' />\n";
	echo "<input type='hidden' name='afficher_tous_les_eleves' value='$afficher_tous_les_eleves' />\n";

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

	echo "<table class='boireaus' border='1' summary='Cr�er'>\n";
	$alt=1;
	while ($current_eleve = mysql_fetch_object($quels_eleves)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			echo "<input type='submit' value='Cr�er' onclick=\"$('eleve_login').value='".$current_eleve->login."';$('indiv_mdp_INE').value='n'; $('form_create_one_eleve').submit();\" />\n";
			echo "</td>\n";

			echo "<td>\n";
			echo "<input type='submit' value=\"Cr�er d'apr�s INE\" onclick=\"$('eleve_login').value='".$current_eleve->login."';$('indiv_mdp_INE').value='y'; $('form_create_one_eleve').submit();\" />\n";
			//echo "<input type='submit' value=\"Cr�er d'apr�s INE\" onclick=\"$('eleve_login').value='".$current_eleve->login."';$('indiv_mdp_INE').value='y';\" />\n";
			echo "</td>\n";

			echo "<td>".$current_eleve->nom." ".$current_eleve->prenom."</td>\n";


			echo "<td>\n";
			$tmp_class=get_class_from_ele_login($current_eleve->login);
			if(isset($tmp_class['liste'])) {
				echo $tmp_class['liste'];
			}
			else {
				echo "<span style='color:red;'>Aucune</span>";
			}
			echo "</td>\n";

		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</form>";
	echo "</blockquote>\n";
}
require("../lib/footer.inc.php");
?>