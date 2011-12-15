<?php
/*
* $Id: reset_passwords.php 8612 2011-11-09 13:41:10Z crob $
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);


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

check_token();

// Ajout ERIC
$mode_impression = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : false);

$mdp_INE=isset($_GET["mdp_INE"]) ? $_GET["mdp_INE"] : NULL;

$affiche_adresse_resp=isset($_GET["affiche_adresse_resp"]) ? $_GET["affiche_adresse_resp"] : "n";

$nouveaux_seulement=isset($_GET["nouveaux_seulement"]) ? $_GET["nouveaux_seulement"] : "n";

//comme il y a une redirection pour une page Csv ou PDF, il ne faut pas envoyer les ent�tes dans ces 2 cas
if (!(($mode_impression=='csv') or ($mode_impression=='pdf'))) {
//**************** EN-TETE *****************************
//$titre_page = "Gestion des utilisateurs | R�initialisation des mots de passe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
}

//debug_var();

// On appelle la lib utilis�e pour la g�n�ration des mots de passe
include("randpass.php");


$user_login = isset($_POST["user_login"]) ? $_POST["user_login"] : (isset($_GET["user_login"]) ? $_GET["user_login"] : false);
$user_status = isset($_POST["user_status"]) ? $_POST["user_status"] : (isset($_GET["user_status"]) ? $_GET["user_status"] : false);
$user_classe = isset($_POST["user_classe"]) ? $_POST["user_classe"] : (isset($_GET["user_classe"]) ? $_GET["user_classe"] : false);


// REMARQUE:
// C'est un peu le bazar: on a un '$user_status' et un '$user_statut' extrait plus loin dans la boucle sur la liste des utilisateurs

// Il faut �tre s�r que l'on ne fait pas de r�initialisation accidentelle de tous les utilisateurs...
// On bloque donc l'op�ration si jamais un des trois param�tres n'a pas �t� pass� correctement, pour une raison ou une autre.

if ($user_login AND strtoupper($user_login) == strtoupper($_SESSION['login'])) {
	$user_login = false;
	echo "<p>ERREUR ! Utilisez l'interface 'G�rer mon compte' pour changer votre mot de passe !</p>";
	echo "</div></body></html>";
	die();
}

if ($user_status and !in_array($user_status, array("scolarite", "professeur", "cpe", "secours", "responsable", "eleve", "autre"))) {
	echo "<p>ERREUR ! L'identifiant de statut est erron�. L'op�ration ne peut pas continuer.</p>";
	echo "</div></body></html>";
	die();
}

if ($user_classe AND !is_numeric($user_classe)) {
	echo "<p>ERREUR ! L'identifiant de la classe est erron�. L'op�ration ne peut pas continuer.</p>";
	echo "</div></body></html>";
	die();
}
//----

//Ajout Eric ==> les donn�es � sortir sont diff�rentes suivant la demande de r�initialisation faite (elv / resp) et au niveau du responsable en fonction du fait classe / tous (dans ce cas, il faut rechercher la classe
$cas_traite = 0;

//echo "\$user_login=$user_login<br />";
//echo "\$mode_impression=$mode_impression<br />";


//TODO: Sans doute faudrait-il ajouter des tests ici, si jamais un jour quelqu'un d'autre que l'administrateur peut acc�der � la page.
if ($user_login) {
	// Si on est ici, c'est qu'on a demand� la r�initialisation du mot de passe d'un seul utilisateur. C'est simple :)

	// Sauf que si on r�initialise le mot de passe d'un prof ou d'un responsable, afficher 'classe' n'est pas appropri�
	//echo "temoin<br />";

	// On ne r�cup�re pas les infos responsable si statut='responsable'

	// ATTENTION: Un utilisateur inactif n'apparaitra pas...
	/*
	$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (" .
			"login = '" . $user_login ."' and " .
			"etat='actif' and " .
			"statut != 'administrateur')");
	*/

	// GROS DOUTE:
	// Qui peut acc�der � la page... NON: si seul l'administrateur peut acc�der, c'est OK.
	/*
	mysql> select * from droits where id like '%reset_password%';
	+-----------------------------------+----------------+------------+-----+-----------+-------+-------------+---------+------------------------------------+--------+
	| id                                | administrateur | professeur | cpe | scolarite | eleve | responsable | secours | description                        | statut |
	+-----------------------------------+----------------+------------+-----+-----------+-------+-------------+---------+------------------------------------+--------+
	| /utilisateurs/reset_passwords.php | V              | F          | F   | F         | F     | F           | F       | R�initialisation des mots de passe |        |
	+-----------------------------------+----------------+------------+-----+-----------+-------+-------------+---------+------------------------------------+--------+
	1 row in set (0.06 sec)

	mysql>
	*/

	// On a fourni un login... on peut retrouver le statut dans utilisateurs:
	$sql="SELECT statut FROM utilisateurs WHERE login='$user_login';";
	//echo "$sql<br />";
	$res_statut=mysql_query($sql);
	if(mysql_num_rows($res_statut)>0) {
		$lig_statut=mysql_fetch_object($res_statut);
		$user_status=$lig_statut->statut;
		//echo "\$user_status=$user_status<br />";
	}

	if ($user_status == "responsable") {
		/*
		$sql_user_info="SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email, re.pers_id, jec.id_classe, ra.*
						FROM utilisateurs u,
							resp_pers r,
							responsables2 re,
							classes c,
							j_eleves_classes jec,
							eleves e,
							resp_adr ra
						WHERE ( u.login = r.login AND
						u.statut = 'responsable' AND
						r.pers_id = re.pers_id AND
						re.ele_id = e.ele_id AND
						e.login = jec.login AND
						r.adr_id = ra.adr_id AND
						u.login = '$user_login')";
		*/

		$sql_user_info="SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email, u.auth_mode, re.pers_id, re.resp_legal, r.civilite, ra.*
						FROM utilisateurs u,
							resp_pers r,
							responsables2 re,
							resp_adr ra
						WHERE (u.login=r.login AND
								u.statut='responsable' AND
								r.adr_id=ra.adr_id AND
								re.pers_id=r.pers_id AND
								u.login='$user_login'
						)";

		/*
								AND
								(re.resp_legal='1' OR re.resp_legal='2')
		*/

		//echo "\$sql_user_info=$sql_user_info<br />";
		$call_user_info = mysql_query($sql_user_info);
		//echo "mysql_num_rows(\$call_user_info)=".mysql_num_rows($call_user_info)."<br />";

		//$cas_traite=2;
		// AVEC 2, il cherche � r�cup�rer la classe... et je l'ai vir�e de la requ�te...
		$cas_traite=1;

	}
	/*
	elseif($user_status == "eleve"){
		$sql_user_info = "SELECT DISTINCT (u.login), u.nom, u.prenom, u.statut, u.password, u.email, jec.id_classe
							FROM utilisateurs u, j_eleves_classes jec
							WHERE ( u.login != 'ADMIN'
							AND jec.login = u.login
							AND u.etat = 'actif'
							AND u.statut = 'eleve'
							AND u.login='$user_login')
							ORDER BY jec.id_classe ASC, u.nom ASC";
		$call_user_info = mysql_query($sql_user_info);
	}
	*/
	else{
		$sql="SELECT * FROM utilisateurs WHERE (" .
				"login = '" . $user_login ."' and " .
				"etat='actif' and " .
				"statut != 'administrateur');";
		//echo "$sql<br />\n";
		$call_user_info = mysql_query($sql);
	}
}
else {

	if ($user_status) {
		if ($user_classe) {
			// On a un statut et une classe. Cette op�ration s'applique soit aux �l�ves soit aux parents

			if ($user_status == "responsable") {
				// S�lection de tous les responsables d'�l�ves de la classe donn�e
				/*$call_user_info = mysql_query("SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email " .
						"FROM utilisateurs u, resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
						"u.login = r.login AND " .
						"u.statut = 'responsable' AND " .
						"r.pers_id = re.pers_id AND " .
						"re.ele_id = e.ele_id AND " .
						"e.login = jec.login AND " .
						"jec.id_classe = '".$user_classe."')");
				*/
				$sql_user_resp="SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email, u.auth_mode, re.pers_id, re.resp_legal, r.civilite, jec.id_classe, ra.*
								FROM utilisateurs u,
								     resp_pers r,
								     responsables2 re,
								     classes c,
								     j_eleves_classes jec,
								     eleves e,
								     resp_adr ra
								WHERE (
								u.statut = 'responsable' AND
							        u.login = r.login AND
								r.pers_id = re.pers_id AND
								re.ele_id = e.ele_id AND
								e.login = jec.login AND
								r.adr_id = ra.adr_id AND
								(re.resp_legal='1' OR re.resp_legal='2') AND
								jec.id_classe = '$user_classe')
								ORDER BY u.nom,u.prenom";
				$call_user_info = mysql_query($sql_user_resp);
				//echo $sql_user_resp."<br />\n";
				$cas_traite=1;

				$sql_classe = "SELECT * FROM classes WHERE id=$user_classe";
				$data_user_classe = mysql_query($sql_classe);
				$classe_resp= mysql_result($data_user_classe, 0, "classe");

			} elseif ($user_status == "eleve") {
				// S�lection de tous les utilisateurs �l�ves de la classe donn�e
				$call_user_info = mysql_query("SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email, u.auth_mode " .
						"FROM utilisateurs u, classes c, j_eleves_classes jec WHERE (" .
						"u.login = jec.login AND " .
						"jec.id_classe = '".$user_classe."') ORDER BY u.nom, u.prenom ");
			}
		}
		else {
			// Ici, on ne s'occupe pas de la classe, donc on s�lectionne tous les utilisateurs pour le statut consid�r�,
			// quel qu'il soit
			//pour les diff�rentes impressions, on va trier les informations par classe (pour faciliter la distribution) probl�me avec les ajouts en cours d'ann�e
			if ($user_status == "responsable") {
				/*$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (" .
					"login != '" . $_SESSION['login'] . "' AND " .
					"etat = 'actif' AND " .
					"statut = '" . $user_status . "')");*/
				//$sql_user_info =   "SELECT DISTINCT (e.ele_id), u.civilite, u.statut, u.password, u.email, u.auth_mode, rp.login, rp.nom, rp.prenom, rp.civilite, rp.pers_id, ra.* , r2.ele_id, r2.resp_legal, e.login, jec.id_classe
				$sql_user_info =   "SELECT DISTINCT (e.ele_id), u.civilite, u.statut, u.password, u.email, u.auth_mode, rp.login, rp.nom, rp.prenom, rp.civilite, rp.pers_id, ra.* , r2.ele_id, r2.resp_legal, jec.id_classe
									FROM utilisateurs u, resp_pers rp, resp_adr ra, responsables2 r2, eleves e, j_eleves_classes jec
									WHERE (
									u.login != 'ADMIN'
									AND u.etat = 'actif'
									AND u.statut = 'responsable'
									AND rp.login = u.login
									AND rp.adr_id = ra.adr_id
									AND rp.pers_id = r2.pers_id
									AND r2.ele_id = e.ele_id
									AND jec.login = e.login
									AND (r2.resp_legal='1' OR r2.resp_legal='2')
									)
									ORDER BY jec.id_classe, rp.nom, rp.prenom";
				//echo $sql_user_info;
				$call_user_info = mysql_query($sql_user_info);
				$cas_traite=2;

			} elseif ($user_status == "eleve"){
				$login_en_cours = $_SESSION['login'];
				$sql_user_info = "SELECT DISTINCT (u.login), u.nom, u.prenom, u.statut, u.password, u.email, u.auth_mode, jec.id_classe
								FROM utilisateurs u, j_eleves_classes jec
								WHERE ( u.login != 'ADMIN'
								AND jec.login = u.login
								AND u.etat = 'actif'
								AND u.statut = 'eleve' )
								ORDER BY jec.id_classe ASC, u.nom ASC";
				//echo $sql_user_info;
				$call_user_info = mysql_query($sql_user_info);
			}
		}
	}
	else {
		// Ni statut ni classe ni login n'ont �t� transmis. On s�lectionne alors tous les personnels de l'�tablissement,
		// c'est � dire tout le monde sauf l'administrateur connect� actuellement, les parents, et les �l�ves.

		$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (" .
				"login!='" . $_SESSION['login'] . "' and " .
				"etat='actif' and " .
				"(statut = 'professeur' OR " .
				"statut = 'scolarite' OR " .
				"statut = 'cpe' OR " .
				"statut = 'secours'))");
	}
}


$nb_users = mysql_num_rows($call_user_info);
/*
echo "\$call_user_info=$call_user_info<br />";
echo "\$nb_users=$nb_users<br />";
echo "\$user_status=$user_status<br />";
echo "\$cas_traite=$cas_traite<br />";
*/
//$cas_traite=1;

// =====================
unset($tab_password);
$tab_password=array();

unset($tab_non_INE_password);
$tab_non_INE_password=array();

if(($mode_impression!='pdf')&&($mode_impression!='csv')) {
	echo "<style type='text/css'>
#div_mdp_non_ine {
	border: 1px solid black;
	text-align:center;
	visibility:hidden;
}

@media print {
	#div_mdp_non_ine {
		display:none;
	}
}
</style>

<div id='div_mdp_non_ine'></div>\n";
}
// =====================

$p = 0;
$pcsv=0;
$saut = 1;
while ($p < $nb_users) {

	$user_login = mysql_result($call_user_info, $p, "login");
	$user_nom = mysql_result($call_user_info, $p, "nom");
	$user_prenom = mysql_result($call_user_info, $p, "prenom");
	$user_password = mysql_result($call_user_info, $p, "password");
	$user_statut = mysql_result($call_user_info, $p, "statut");
	$user_email = mysql_result($call_user_info, $p, "email");
	$user_auth_mode = mysql_result($call_user_info, $p, "auth_mode");

	//echo "\$user_login=$user_login<br />";
	//echo "\$user_auth_mode=$user_auth_mode<br />";

	//Pour les responsables :
	if ($cas_traite!=0) {
		$resp_num_legal= mysql_result($call_user_info, $p, "resp_legal");
		$resp_civilite= mysql_result($call_user_info, $p, "civilite");
		$resp_adr1=mysql_result($call_user_info, $p, "adr1");
		$resp_adr1=mysql_result($call_user_info, $p, "adr1");
		$resp_adr2=mysql_result($call_user_info, $p, "adr2");
		$resp_adr3=mysql_result($call_user_info, $p, "adr3");
		$resp_adr4=mysql_result($call_user_info, $p, "adr4");
		$resp_cp=mysql_result($call_user_info, $p, "cp");
		$resp_commune=mysql_result($call_user_info, $p, "commune");
		$resp_pays=mysql_result($call_user_info, $p, "pays");
		$resp_pers_id=mysql_result($call_user_info, $p, "pers_id");

		//recherche des �l�ves + leur classe associ�s aux responsables
		$sql_resp_eleves="SELECT DISTINCT c.id, e.* , c.*
							FROM responsables2 r2, eleves e, classes c, j_eleves_classes jec
							WHERE (
							r2.pers_id = '$resp_pers_id'
							AND r2.ele_id = e.ele_id
							AND e.login = jec.login
							AND jec.id_classe = c.id
							AND r2.resp_legal!='0'
							)";
		//echo "<br />\$sql_resp_eleves=".$sql_resp_eleves."<br />";
		$call_resp_eleves=mysql_query($sql_resp_eleves);
		$nb_elv_resp = mysql_num_rows($call_resp_eleves);
		//echo "\$nb_elv_resp=$nb_elv_resp<br />";

		//init du tableau elv_resp
		//for ($i=0;$i<7;$i++) {
		// =====================
		// MODIF: boireaus 20071102
		/*
		for ($i=0;$i<$nb_elv_resp;$i++) {
			$elv_resp['nom'][$i] = '';
			$elv_resp['prenom'][$i] = '';
			$elv_resp['classe'][$i] = '';
			$elv_resp['nom_complet_classe'][$i] = '';
		}
		*/
		// R�initialisation du tableau des enfants � la charge du responsable courant:
		unset($elv_resp);
		$elv_resp=array();
		// =====================

		$liste_elv_resp="";

		$i = 0;
		while ($i < $nb_elv_resp){
			/*
			// =====================
			// MODIF: boireaus 20071102
			// A quoi cela sert-il?
			// A la r�initialisation du tableau $elv_resp?
			// Mais si on d�passe 7 enfants?
			for ($j=$i;$j<7;$j++) {
				$elv_resp['nom'][$j] = '';
				$elv_resp['prenom'][$j] = '';
				$elv_resp['classe'][$j] = '';
				$elv_resp['nom_complet_classe'][$j] = '';
			}
			// =====================
			*/

			$elv_resp['login'][$i] = mysql_result($call_resp_eleves, $i, "login");
			$elv_resp['nom'][$i] = mysql_result($call_resp_eleves, $i, "nom");
			$elv_resp['prenom'][$i] = mysql_result($call_resp_eleves, $i, "prenom");
			$elv_resp['classe'][$i] = mysql_result($call_resp_eleves, $i, "classe");
			$elv_resp['nom_complet_classe'][$i] = mysql_result($call_resp_eleves, $i, "nom_complet");

			if($i>0){
				$liste_elv_resp.=", ";
			}
			$liste_elv_resp.=strtoupper($elv_resp['nom'][$i])." ".ucfirst(strtolower($elv_resp['prenom'][$i]))." (<i>".$elv_resp['classe'][$i]."</i>)";

			/*
			echo "\$elv_resp['nom'][$i]=".$elv_resp['nom'][$i]."<br />";
			echo "\$elv_resp['prenom'][$i]=".$elv_resp['prenom'][$i]."<br />";
			echo "\$elv_resp['classe'][$i]=".$elv_resp['classe'][$i]."<br />";
			echo "\$elv_resp['nom_complet_classe'][$i]=".$elv_resp['nom_complet_classe'][$i]."<br />";
			*/

			$i++;
		}

		// il va y avoir la classe � r�cuperer
		if ($cas_traite==2) {
			$user_classe = $resp_pers_id=mysql_result($call_user_info, $p, "id_classe");
			//recherche du nom court de la classe de la prsonne en cours
			$sql_classe = "SELECT * FROM classes WHERE id=$user_classe";
			//echo "\$sql_classe=$sql_classe<br />";
			$data_user_classe = mysql_query($sql_classe);
			$classe_resp= mysql_result($data_user_classe, 0, "classe");
		}


	}


	// On r�initialise le mot de passe

	// =====================
	// MODIF: boireaus 20071102
	//if(in_array(,$tab_password)){
	$temoin_user_deja_traite="n";
	if(isset($creation_comptes_classe)) {
		if(array_key_exists($user_login,$tab_password)){
			$new_password = $tab_password[$user_login];
			$temoin_user_deja_traite="y";
		}
		else{
			$sql="SELECT 1=1 FROM utilisateurs WHERE login='$user_login' AND password!='';";
			$test_pass_non_vide=mysql_query($sql);
			if(mysql_num_rows($test_pass_non_vide)>0){
				$new_password="<span style='color:red;'>Non modifi�</span>";
				$temoin_user_deja_traite="y";
			}
			else{
				if(($user_status=='eleve')&&($mdp_INE=='y')) {
					$sql="SELECT no_gep FROM eleves WHERE login='$user_login';";
					$res_ine=mysql_query($sql);
					if(mysql_num_rows($res_ine)>0){
						$lig_ine=mysql_fetch_object($res_ine);
						if($lig_ine->no_gep!='') {
							$new_password=$lig_ine->no_gep;
						}
						else {
							$new_password = pass_gen();
							$tab_non_INE_password[]="$user_nom $user_prenom";
						}
					}
					else {
						$new_password = pass_gen();
						$tab_non_INE_password[]="$user_nom $user_prenom";
					}
				}
				else {
					$new_password = pass_gen();
				}

				$tab_password[$user_login]=$new_password;

                                $save_new_pass = Session::change_password_gepi($user_login,$new_password);
                                if ($save_new_pass) {
                                    mysql_query("UPDATE utilisateurs SET change_mdp = 'y' WHERE login='$user_login'");
                                }
			}
		}
	}
	else{
		if(array_key_exists($user_login,$tab_password)){
			$new_password = $tab_password[$user_login];
			$temoin_user_deja_traite="y";
		}
		else{
			//$new_password = pass_gen();
			if(($user_status=='eleve')&&($mdp_INE=='y')) {
				$sql="SELECT no_gep FROM eleves WHERE login='$user_login';";
				$res_ine=mysql_query($sql);
				if(mysql_num_rows($res_ine)>0){
					$lig_ine=mysql_fetch_object($res_ine);
					if($lig_ine->no_gep!='') {
						$new_password=$lig_ine->no_gep;
					}
					else {
						$new_password = pass_gen();
						$tab_non_INE_password[]="$user_nom $user_prenom";
					}
				}
				else {
					$new_password = pass_gen();
					$tab_non_INE_password[]="$user_nom $user_prenom";
				}
			}
			else {
				$new_password = pass_gen();
			}
			$tab_password[$user_login]=$new_password;

			if ($user_auth_mode != "gepi") {
				// L'utilisateur est un utilisateur SSO. On enregistre un mot de passe vide.
					$save_new_pass = mysql_query("UPDATE utilisateurs SET password='', change_mdp = 'n' WHERE login='" . $user_login . "'");

				// Si l'acc�s LDAP en �criture est param�tr�, on va mettre � jour le mot de passe de l'utilisateur
				// directement dans l'annuaire.
				if ($gepiSettings['ldap_write_access'] == "yes") {
					$ldap_server = new LDAPServer;
					$reg_data = $ldap_server->update_user($user_login, '', '', '', '', $new_password,'');
				} else {
					// On r�initialise la variable $new_password � z�ro, pour �tre s�r
					// qu'il n'y ait pas de confusion par la suite.
					$new_password = '';
				}
			} else {
                                $save_new_pass = Session::change_password_gepi($user_login,$new_password);
                                if ($save_new_pass) {
                                    mysql_query("UPDATE utilisateurs SET change_mdp = 'y' WHERE login='$user_login'");
                                }
			}
		}
	}
	// =====================

	$call_matieres = mysql_query("SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '$user_login' ORDER BY ordre_matieres");
	$nb_mat = mysql_num_rows($call_matieres);
	$k = 0;
	while ($k < $nb_mat) {
		$user_matiere[$k] = mysql_result($call_matieres, $k, "id_matiere");
		$k++;
	}

	$call_data = mysql_query("SELECT * FROM classes");
	$nombre_classes = mysql_num_rows($call_data);
	$i = 0;
	while ($i < $nombre_classes){
		$classe[$i] = mysql_result($call_data, $i, "classe");
		$i++;
	}

	/*
	echo "\$user_login=$user_login<br />";
	echo "\$user_status=$user_status<br />";
	echo "\$mode_impression=$mode_impression<br />";
	*/

	// nouveaux_seulement
	if((!isset($nouveaux_seulement))||($nouveaux_seulement!="y")||(!preg_match("/Non modifi�/i", $new_password))) {

	// Ajout Eric
	switch ($mode_impression) {

	case 'html':
		//echo "TEMOIN 1<br />";

		if ($user_statut == "responsable") {
			$impression = getSettingValue("ImpressionFicheParent");
			$nb_fiches = getSettingValue("ImpressionNombreParent");
		} elseif ($user_statut == "eleve") {
			$impression = getSettingValue("ImpressionFicheEleve");
			$nb_fiches = getSettingValue("ImpressionNombreEleve");
		} else {
			$impression = getSettingValue("Impression");
			$nb_fiches = getSettingValue("ImpressionNombre");
		}

		//echo "get_class_from_ele_login($user_login)=".get_class_from_ele_login($user_login)."<br />";
		$tab_tmp_classe=get_class_from_ele_login($user_login);

		//$affiche_adresse_resp="y";
		if($affiche_adresse_resp=='y') {
			// R�cup�ration des variables du bloc adresses:
			// Liste de r�cup�ration � extraire de la boucle �l�ves pour limiter le nombre de requ�tes... A FAIRE
			// Il y a d'autres r�cup�ration de largeur et de positionnement du bloc adresse � extraire...
			// PROPORTION 30%/70% POUR LE 1er TABLEAU ET ...
			$largeur1=getSettingValue("addressblock_logo_etab_prop") ? getSettingValue("addressblock_logo_etab_prop") : 40;
			$largeur2=100-$largeur1;

			// Taille des polices sur le bloc adresse:
			$addressblock_font_size=getSettingValue("addressblock_font_size") ? getSettingValue("addressblock_font_size") : 12;

			// Taille de la cellule Classe et Ann�e scolaire sur le bloc adresse:
			$addressblock_classe_annee=getSettingValue("addressblock_classe_annee") ? getSettingValue("addressblock_classe_annee") : 35;
			// Calcul du pourcentage par rapport au tableau contenant le bloc Classe, Ann�e,...
			$addressblock_classe_annee2=round(100*$addressblock_classe_annee/(100-$largeur1));

			// D�bug sur l'ent�te pour afficher les cadres
			$addressblock_debug=getSettingValue("addressblock_debug") ? getSettingValue("addressblock_debug") : "n";

			$addressblock_length=getSettingValue("addressblock_length") ? getSettingValue("addressblock_length") : 6;
			$addressblock_padding_top=getSettingValue("addressblock_padding_top") ? getSettingValue("addressblock_padding_top") : 0;
			$addressblock_padding_text=getSettingValue("addressblock_padding_text") ? getSettingValue("addressblock_padding_text") : 0;
			$addressblock_padding_right=getSettingValue("addressblock_padding_right") ? getSettingValue("addressblock_padding_right") : 0;

			//$addressblock_debug="y";

			/*
			$ligne1="NOM PRENOM";
			$ligne2="3 rue de....";
			$ligne3="27300 BERNAY";
			*/

			$sql="SELECT ra.*,rp.nom,rp.prenom,rp.civilite FROM resp_adr ra, resp_pers rp WHERE rp.adr_id=ra.adr_id AND rp.login='$user_login';";
			$res_adr_resp=mysql_query($sql);
			if(mysql_num_rows($res_adr_resp)==0) {
				$ligne1="<font color='red'><b>ADRESSE MANQUANTE</b></font>";
				$ligne2="";
				$ligne3="";
			}
			else {
				$lig_adr_resp=mysql_fetch_object($res_adr_resp);

				$ligne1=$lig_adr_resp->civilite." ".$lig_adr_resp->nom." ".$lig_adr_resp->prenom;
				$ligne2=$lig_adr_resp->adr1;
				$ligne3=$lig_adr_resp->cp." ".$lig_adr_resp->commune;

				if($lig_adr_resp->civilite!="") {
					$ligne1=$lig_adr_resp->civilite." ".$lig_adr_resp->nom." ".$lig_adr_resp->prenom;
				}
				else {
					$ligne1="M.".$lig_adr_resp->nom." ".$lig_adr_resp->prenom;
				}

				$ligne2=$lig_adr_resp->adr1;
				if($lig_adr_resp->adr2!=""){
					$ligne2.="<br />\n".$lig_adr_resp->adr2;
				}
				if($lig_adr_resp->adr3!=""){
					$ligne2.="<br />\n".$lig_adr_resp->adr3;
				}
				if($lig_adr_resp->adr4!=""){
					$ligne2.="<br />\n".$lig_adr_resp->adr4;
				}
				$ligne3=$lig_adr_resp->cp." ".$lig_adr_resp->commune;

				if(($lig_adr_resp->pays!="")&&(strtolower($lig_adr_resp->pays)!=strtolower(getSettingValue('gepiSchoolPays')))) {
					if($ligne3!=" "){
						$ligne3.="<br />";
					}
					$ligne3.=$lig_adr_resp->pays;
				}

			}

			echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";

			// Cadre adresse du responsable:
			echo "<div style='float:right;
width:".$addressblock_length."mm;
padding-top:".$addressblock_padding_top."mm;
padding-bottom:".$addressblock_padding_text."mm;
padding-right:".$addressblock_padding_right."mm;\n";
			if($addressblock_debug=="y"){echo "border: 1px solid blue;\n";}
			echo "font-size: ".$addressblock_font_size."pt;
'>
<div align='left'>
$ligne1<br />
$ligne2<br />
$ligne3
</div>
</div>\n";



			// Cadre contenant le tableau Logo+Ad_etab et le nom, pr�nom,... de l'�l�ve:
			echo "<div style='float:left;
left:0px;
top:0px;
width:".$largeur1."%;\n";
			if($addressblock_debug=="y"){echo "border: 1px solid green;\n";}
			echo "'>\n";

		}

		echo "<table border='0' summary=\"$user_login\">\n";
		echo "<tr><td>A l'attention de </td><td><span class = \"bold\">" . $user_prenom . " " . $user_nom . "</span></td></tr>\n";
		//echo "<tr><td>Nom de login : </td><td><span class = \"bold\">" . $user_login . "</span></td></tr>\n";
		echo "<tr><td>Identifiant : </td><td><span class = \"bold\">" . $user_login . "</span></td></tr>\n";
		if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] != 'yes') {
			// En mode SSO ou LDAP sans acc�s en �criture, le mot de passe n'est pas modifiable par Gepi.
			echo "<tr><td>Le mot de passe de cet utilisateur n'est pas g�r� par Gepi.</td></tr>\n";
		}
		else {
			echo "<tr><td>Mot de passe : </td><td><span class = \"bold\">" . $new_password . "</span></td></tr>\n";
		}//if($cas_traite!=0){
		if ($user_statut == "responsable") {
			echo "<tr><td>Responsable de : </td><td><span class = \"bold\">";
			if($liste_elv_resp==""){
				echo "&nbsp;";
			}
			else{
				echo $liste_elv_resp;
			}

			//echo "<br />".$classe_resp;

			echo "</span></td></tr>\n";
		}
		//else{
		elseif ($user_statut == "eleve") {
			echo "<tr><td>Classe : </td><td><span class = \"bold\">";
			if(count($tab_tmp_classe)>0){
				$chaine="";
				foreach ($tab_tmp_classe as $key => $value){
					//echo "\$key=$key et \$value=$value et my_ereg_replace(\"[0-9]\",\"\",$key)=".my_ereg_replace("[0-9]","",$key)."<br />";
					// Avant il n'y avait qu'un $key=$id_classe... maintenant, on a aussi $key=id$id_classe dans get_class_from_ele_login() (de /lib/share.inc.php)
					if(strlen(my_ereg_replace("[0-9]","",$key))==0) {
						//$chaine.=", <a href='../classes/classes_const.php?id_classe=$key'>$value</a>";
						$chaine.=", $value";
					}
				}
				$chaine=substr($chaine,2);
				echo $chaine;
			}
			echo "</span></td></tr>\n";
		}


		echo "<tr><td>Adresse de courriel : </td><td><span class = \"bold\">" . $user_email . "&nbsp;</span></td></tr>\n";
		echo "</table>\n";

		if($affiche_adresse_resp=='y') {
			echo "</div>\n";

			echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";
		}

		// La fiche bienvenue:
		echo $impression;

		// Saut de page toutes les $nb_fiches fiches
		if ($saut == $nb_fiches) {
			echo "<p class='saut'>&nbsp;</p>\n";
			$saut = 1;
		} else {
			$saut++;
		}

		//$p++;

		break;

	case 'csv' :
		//===========================
		// MODIF: boireaus 20071102
		// Dans le cas du CSV, on ne g�n�re pas plusieurs fois la ligne correspondant � un m�me parent
		// Dans le cas du HTML et PDF par contre, on affiche autant de fois qu'il y a d'�l�ve � qui distribuer l'info, mais sans g�n�rer plusieurs fois le mot de passe pour le parent (le m�me mot de passe pour le parent sur les fiches distribu�es � ses diff�rents enfants).
		if($temoin_user_deja_traite!="y") {

			// cr�ation d'un tableau contenant toutes les informations � exporter
			$donnees_personne_csv['login'][$pcsv] = $user_login;
			$donnees_personne_csv['nom'][$pcsv] = $user_nom;
			$donnees_personne_csv['prenom'][$pcsv] = $user_prenom;
			$donnees_personne_csv['new_password'][$pcsv] = $new_password ;
			$donnees_personne_csv['user_email'][$pcsv] = $user_email;

			//echo "\$donnees_personne_csv['login'][$pcsv]=".$donnees_personne_csv['login'][$pcsv]."<br />";

			if ($user_status) {

				//recherche de la classe de l'�l�ve si mode
				if ($user_status == 'eleve') {
					$sql_classe = "SELECT DISTINCT classe FROM `classes` c, `j_eleves_classes` jec WHERE (jec.login='".$user_login."' AND jec.id_classe=c.id)";
					$data_user_classe = mysql_query($sql_classe);
					$classe_eleve = mysql_result($data_user_classe, 0, "classe");
					$donnees_personne_csv['classe'][$pcsv] = $classe_eleve;
				}

				//on poursuit le tableau $donnees_personne_csv avec l'adresse pour un mailling et des �l�ves associ�es
				if ($user_status =='responsable') {

					$donnees_personne_csv['classe'][$pcsv] = $classe_resp;

					/*
					$resp_num_legal= mysql_result($call_user_info, $pcsv, "resp_legal");
					$resp_civilite= mysql_result($call_user_info, $pcsv, "civilite");
					$resp_adr1=mysql_result($call_user_info, $pcsv, "adr1");
					$resp_adr1=mysql_result($call_user_info, $pcsv, "adr1");
					$resp_adr2=mysql_result($call_user_info, $pcsv, "adr2");
					$resp_adr3=mysql_result($call_user_info, $pcsv, "adr3");
					$resp_adr4=mysql_result($call_user_info, $pcsv, "adr4");
					$resp_cp=mysql_result($call_user_info, $pcsv, "cp");
					$resp_commune=mysql_result($call_user_info, $pcsv, "commune");
					$resp_pays=mysql_result($call_user_info, $pcsv, "pays");
					*/
					$resp_num_legal= mysql_result($call_user_info, $p, "resp_legal");
					$resp_civilite= mysql_result($call_user_info, $p, "civilite");
					$resp_adr1=mysql_result($call_user_info, $p, "adr1");
					$resp_adr1=mysql_result($call_user_info, $p, "adr1");
					$resp_adr2=mysql_result($call_user_info, $p, "adr2");
					$resp_adr3=mysql_result($call_user_info, $p, "adr3");
					$resp_adr4=mysql_result($call_user_info, $p, "adr4");
					$resp_cp=mysql_result($call_user_info, $p, "cp");
					$resp_commune=mysql_result($call_user_info, $p, "commune");
					$resp_pays=mysql_result($call_user_info, $p, "pays");

					//on met les donn�es dans le tableau
					$donnees_personne_csv['resp_legal'][$pcsv] = $resp_num_legal;
					$donnees_personne_csv['civilite'][$pcsv] = $resp_civilite;
					$donnees_personne_csv['adr1'][$pcsv] = $resp_adr1;
					$donnees_personne_csv['adr2'][$pcsv] = $resp_adr2;
					$donnees_personne_csv['adr3'][$pcsv] = $resp_adr3;
					$donnees_personne_csv['adr4'][$pcsv] = $resp_adr4;
					$donnees_personne_csv['cp'][$pcsv] = $resp_cp;
					$donnees_personne_csv['commune'][$pcsv] = $resp_commune;
					$donnees_personne_csv['pays'][$pcsv] = $resp_pays;

					//echo "\$donnees_personne_csv['adr1'][$pcsv]=".$donnees_personne_csv['adr1'][$pcsv]."<br />";
					//echo "\$donnees_personne_csv['commune'][$pcsv]=".$donnees_personne_csv['commune'][$pcsv]."<br />";

					// On cr�e une chaine de caract�res par �l�ves (Pr�nom, Nom, classe nom long et classe nom court)
					$nb_elv=sizeof($elv_resp['nom']);
					$i=0;
					while ($i < $nb_elv){
						$chaine_elv = "";
						$chaine_elv.=$elv_resp['prenom'][$i];
						$chaine_elv.=" ".$elv_resp['nom'][$i];
						$chaine_elv.=" ".$elv_resp['nom_complet_classe'][$i];
						if ($elv_resp['nom'][$i]!='') {$chaine_elv.=" (".$elv_resp['classe'][$i].")";}

						switch ($i) {
							case 0 : $donnees_personne_csv['elv1'][$pcsv] = $chaine_elv; Break;
							case 1 : $donnees_personne_csv['elv2'][$pcsv] = $chaine_elv; Break;
							case 2 : $donnees_personne_csv['elv3'][$pcsv] = $chaine_elv; Break;
							case 3 : $donnees_personne_csv['elv4'][$pcsv] = $chaine_elv; Break;
							case 4 : $donnees_personne_csv['elv5'][$pcsv] = $chaine_elv; Break;
							case 5 : $donnees_personne_csv['elv6'][$pcsv] = $chaine_elv; Break;
							case 6 : $donnees_personne_csv['elv7'][$pcsv] = $chaine_elv; Break;
						}
						switch ($i) {
							case 0 : $donnees_personne_csv['elv1_login'][$pcsv] = $elv_resp['login'][$i]; Break;
							case 1 : $donnees_personne_csv['elv2_login'][$pcsv] = $elv_resp['login'][$i]; Break;
							case 2 : $donnees_personne_csv['elv3_login'][$pcsv] = $elv_resp['login'][$i]; Break;
							case 3 : $donnees_personne_csv['elv4_login'][$pcsv] = $elv_resp['login'][$i]; Break;
							case 4 : $donnees_personne_csv['elv5_login'][$pcsv] = $elv_resp['login'][$i]; Break;
							case 5 : $donnees_personne_csv['elv6_login'][$pcsv] = $elv_resp['login'][$i]; Break;
							case 6 : $donnees_personne_csv['elv7_login'][$pcsv] = $elv_resp['login'][$i]; Break;
						}

						$i++;
					}
				}
			}
			$pcsv++;
		}
		//===========================

		break;

	case 'pdf': //uniquement pour les �l�ves
		// cr�ation d'un tableau contenant toutes les informations � exporter
		$donnees_personne_csv['login'][$p] = $user_login;
		$donnees_personne_csv['nom'][$p] = $user_nom;
		$donnees_personne_csv['prenom'][$p] = $user_prenom;
		$donnees_personne_csv['new_password'][$p] = $new_password ;
		$donnees_personne_csv['user_email'][$p] = $user_email;

		//recherche de la classe de l'�l�ve si mode
		if ($user_status) {
			if ($user_status == 'eleve') {
				$sql_classe = "SELECT DISTINCT classe FROM `classes` c, `j_eleves_classes` jec WHERE (jec.login='".$user_login."' AND jec.id_classe=c.id)";
				$data_user_classe = mysql_query($sql_classe);
				$classe_eleve = mysql_result($data_user_classe, 0, "classe");
			}
		}

		$donnees_personne_csv['classe'][$p] = $classe_eleve;

		//$p++;

		break;

	default:
		//echo "TEMOIN 2<br />";

		if ($user_statut == "responsable") {
			$impression = getSettingValue("ImpressionFicheParent");
			$nb_fiches = getSettingValue("ImpressionNombreParent");
		} elseif ($user_statut == "eleve") {
			$impression = getSettingValue("ImpressionFicheEleve");
			$nb_fiches = getSettingValue("ImpressionNombreEleve");
		} else {
			$impression = getSettingValue("Impression");
			$nb_fiches = getSettingValue("ImpressionNombre");
		}

		$tab_tmp_classe=get_class_from_ele_login($user_login);
		/*
		echo "get_class_from_ele_login($user_login)=".get_class_from_ele_login($user_login)."<br />";
		foreach($tab_tmp_classe as $key => $value) {
			echo "\$tab_tmp_classe[$key]=".$value."<br />";
		}
		*/

		//$affiche_adresse_resp="y";
		if($affiche_adresse_resp=='y') {
			// R�cup�ration des variables du bloc adresses:
			// Liste de r�cup�ration � extraire de la boucle �l�ves pour limiter le nombre de requ�tes... A FAIRE
			// Il y a d'autres r�cup�ration de largeur et de positionnement du bloc adresse � extraire...
			// PROPORTION 30%/70% POUR LE 1er TABLEAU ET ...
			$largeur1=getSettingValue("addressblock_logo_etab_prop") ? getSettingValue("addressblock_logo_etab_prop") : 40;
			$largeur2=100-$largeur1;

			// Taille des polices sur le bloc adresse:
			$addressblock_font_size=getSettingValue("addressblock_font_size") ? getSettingValue("addressblock_font_size") : 12;

			// Taille de la cellule Classe et Ann�e scolaire sur le bloc adresse:
			$addressblock_classe_annee=getSettingValue("addressblock_classe_annee") ? getSettingValue("addressblock_classe_annee") : 35;
			// Calcul du pourcentage par rapport au tableau contenant le bloc Classe, Ann�e,...
			$addressblock_classe_annee2=round(100*$addressblock_classe_annee/(100-$largeur1));

			// D�bug sur l'ent�te pour afficher les cadres
			$addressblock_debug=getSettingValue("addressblock_debug") ? getSettingValue("addressblock_debug") : "n";

			$addressblock_length=getSettingValue("addressblock_length") ? getSettingValue("addressblock_length") : 6;
			$addressblock_padding_top=getSettingValue("addressblock_padding_top") ? getSettingValue("addressblock_padding_top") : 0;
			$addressblock_padding_text=getSettingValue("addressblock_padding_text") ? getSettingValue("addressblock_padding_text") : 0;
			$addressblock_padding_right=getSettingValue("addressblock_padding_right") ? getSettingValue("addressblock_padding_right") : 0;

			$addressblock_debug="y";
			// R�cup�ration des variables du bloc adresses:
			// Liste de r�cup�ration � extraire de la boucle �l�ves pour limiter le nombre de requ�tes... A FAIRE
			// Il y a d'autres r�cup�ration de largeur et de positionnement du bloc adresse � extraire...
			// PROPORTION 30%/70% POUR LE 1er TABLEAU ET ...
			$largeur1=getSettingValue("addressblock_logo_etab_prop") ? getSettingValue("addressblock_logo_etab_prop") : 40;
			$largeur2=100-$largeur1;

			// Taille des polices sur le bloc adresse:
			$addressblock_font_size=getSettingValue("addressblock_font_size") ? getSettingValue("addressblock_font_size") : 12;

			// Taille de la cellule Classe et Ann�e scolaire sur le bloc adresse:
			$addressblock_classe_annee=getSettingValue("addressblock_classe_annee") ? getSettingValue("addressblock_classe_annee") : 35;
			// Calcul du pourcentage par rapport au tableau contenant le bloc Classe, Ann�e,...
			$addressblock_classe_annee2=round(100*$addressblock_classe_annee/(100-$largeur1));

			// D�bug sur l'ent�te pour afficher les cadres
			$addressblock_debug=getSettingValue("addressblock_debug") ? getSettingValue("addressblock_debug") : "n";

			$addressblock_length=getSettingValue("addressblock_length") ? getSettingValue("addressblock_length") : 6;
			$addressblock_padding_top=getSettingValue("addressblock_padding_top") ? getSettingValue("addressblock_padding_top") : 0;
			$addressblock_padding_text=getSettingValue("addressblock_padding_text") ? getSettingValue("addressblock_padding_text") : 0;
			$addressblock_padding_right=getSettingValue("addressblock_padding_right") ? getSettingValue("addressblock_padding_right") : 0;

			//$addressblock_debug="y";

			/*
			$ligne1="NOM PRENOM";
			$ligne2="3 rue de....";
			$ligne3="27300 BERNAY";
			*/

			$sql="SELECT ra.*,rp.nom,rp.prenom,rp.civilite FROM resp_adr ra, resp_pers rp WHERE rp.adr_id=ra.adr_id AND rp.login='$user_login';";
			$res_adr_resp=mysql_query($sql);
			if(mysql_num_rows($res_adr_resp)==0) {
				$ligne1="<font color='red'><b>ADRESSE MANQUANTE</b></font>";
				$ligne2="";
				$ligne3="";
			}
			else {
				$lig_adr_resp=mysql_fetch_object($res_adr_resp);

				$ligne1=$lig_adr_resp->civilite." ".$lig_adr_resp->nom." ".$lig_adr_resp->prenom;
				$ligne2=$lig_adr_resp->adr1;
				$ligne3=$lig_adr_resp->cp." ".$lig_adr_resp->commune;

				if($lig_adr_resp->civilite!="") {
					$ligne1=$lig_adr_resp->civilite." ".$lig_adr_resp->nom." ".$lig_adr_resp->prenom;
				}
				else {
					$ligne1="M.".$lig_adr_resp->nom." ".$lig_adr_resp->prenom;
				}

				$ligne2=$lig_adr_resp->adr1;
				if($lig_adr_resp->adr2!=""){
					$ligne2.="<br />\n".$lig_adr_resp->adr2;
				}
				if($lig_adr_resp->adr3!=""){
					$ligne2.="<br />\n".$lig_adr_resp->adr3;
				}
				if($lig_adr_resp->adr4!=""){
					$ligne2.="<br />\n".$lig_adr_resp->adr4;
				}
				$ligne3=$lig_adr_resp->cp." ".$lig_adr_resp->commune;

				if(($lig_adr_resp->pays!="")&&(strtolower($lig_adr_resp->pays)!=strtolower(getSettingValue('gepiSchoolPays')))) {
					if($ligne3!=" "){
						$ligne3.="<br />";
					}
					$ligne3.=$lig_adr_resp->pays;
				}

			}

			echo "<div style='clear: both; font-size: xx-small;'>&nbsp;</div>\n";

			// Cadre adresse du responsable:
			echo "<div style='float:right;
width:".$addressblock_length."mm;
padding-top:".$addressblock_padding_top."mm;
padding-bottom:".$addressblock_padding_text."mm;
padding-right:".$addressblock_padding_right."mm;\n";
			if($addressblock_debug=="y"){echo "border: 1px solid blue;\n";}
			echo "font-size: ".$addressblock_font_size."pt;
'>
<div align='left'>
$ligne1<br />
$ligne2<br />
$ligne3
</div>
</div>\n";



			// Cadre contenant le tableau Logo+Ad_etab et le nom, pr�nom,... de l'�l�ve:
			echo "<div style='float:left;
left:0px;
top:0px;
width:".$largeur1."%;\n";
			if($addressblock_debug=="y"){echo "border: 1px solid green;\n";}
			echo "'>\n";

		}

		echo "<table border='0' summary=\"$user_login\">\n";
		echo "<tr><td>A l'attention de </td><td><span class = \"bold\">" . $user_prenom . " " . $user_nom . "</span></td></tr>\n";
		//echo "<tr><td>Nom de login : </td><td><span class = \"bold\">" . $user_login . "</span></td></tr>\n";
		echo "<tr><td>Identifiant : </td><td><span class = \"bold\">" . $user_login . "</span></td></tr>\n";
		if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] != 'yes') {
			// En mode SSO ou LDAP sans acc�s en �criture, le mot de passe n'est pas modifiable par Gepi.
			echo "<tr><td>Le mot de passe de cet utilisateur n'est pas g�r� par Gepi.</td></tr>\n";
		}
		else {
			echo "<tr><td>Mot de passe : </td><td><span class = \"bold\">" . $new_password . "</span></td></tr>\n";
		}

		if ($user_statut == "responsable") {
			echo "<tr><td>Responsable de : </td><td><span class = \"bold\">";
			if($liste_elv_resp==""){
				echo "&nbsp;";
			}
			else{
				echo $liste_elv_resp;
			}

			//echo "<br />".$classe_resp;

			echo "</span></td></tr>\n";
		}
		//else{
		elseif ($user_statut == "eleve") {
			echo "<tr><td>Classe : </td><td><span class = \"bold\">";
			if(count($tab_tmp_classe)>0){
				$chaine="";
				foreach ($tab_tmp_classe as $key => $value){
					//echo "\$key=$key et \$value=$value et my_ereg_replace(\"[0-9]\",\"\",$key)=".my_ereg_replace("[0-9]","",$key)."<br />";
					// Avant il n'y avait qu'un $key=$id_classe... maintenant, on a aussi $key=id$id_classe dans get_class_from_ele_login() (de /lib/share.inc.php)
					if(strlen(my_ereg_replace("[0-9]","",$key))==0) {
						//$chaine.=", <a href='../classes/classes_const.php?id_classe=$key'>$value</a>";
						$chaine.=", $value";
					}
				}
				$chaine=substr($chaine,2);
				echo $chaine;

			}
			echo "</span></td></tr>\n";
		}

		/*
		echo "<tr><td>Classe : </td><td><span class = \"bold\">";
		if(count($tab_tmp_classe)>0){
			$chaine="";
			foreach ($tab_tmp_classe as $key => $value){
				//$chaine.=", <a href='../classes/classes_const.php?id_classe=$key'>$value</a>";
				$chaine.=", $value";
			}
			$chaine=substr($chaine,2);
			echo $chaine;
		}
		echo "</span></td></tr>\n";
		*/

		echo "<tr><td>Adresse de courriel : </td><td><span class = \"bold\">" . $user_email . "&nbsp;</span></td></tr>\n";
		echo "</table>";

		if($affiche_adresse_resp=='y') {
			echo "</div>\n";
		}

		/*
		// Bloc adresse responsable
		$addressblock_padding_right,
		$addressblock_padding_top,
		$addressblock_padding_text,
		$addressblock_length,
		$addressblock_font_size,
		*/

		echo $impression;
		if ($saut == $nb_fiches) {
			echo "<p class='saut'>&nbsp;</p>\n";
			$saut = 1;
		} else {
			$saut++;
		}

		//$p++;

	} //fin switch

	} // FIN nouveaux_seulement

	$p++;

}


// redirection � la fin de la g�n�ration des mots de passe
switch ($mode_impression) {
	case 'csv' :
		if(isset($donnees_personne_csv)){
			//sauvegarde des donn�es dans la session Admin
			$_SESSION['donnees_export_csv_password']=$donnees_personne_csv;

			//redirection vers password_csv.php
			header("Location: ./password_csv.php"); die();
		}
		else{
			echo "<p>Tous les comptes sont d�j� initialis�s.<br />On ne modifie pas les mots de passe.</p>\n";
		}
		break;
	case 'pdf' :
		if(isset($donnees_personne_csv)){
			//sauvegarde des donn�es dans la session Admin
			$_SESSION['donnees_export_csv_password']=$donnees_personne_csv;

			//redirection vers password_csv.php
			header("Location: ../impression/password_pdf.php"); die();
		}
		else{
			echo "<p>Tous les comptes sont d�j� initialis�s.<br />On ne modifie pas les mots de passe.</p>\n";
		}
		break;
}



// On n'arrive l� que si on n'a pas imprim� en CSV ou PDF
if(count($tab_non_INE_password)>0) {
	if(count($tab_non_INE_password)==1) {
		$chaine="L'�l�ve suivant n'a pas le num�ro INE renseign�.<br />Il a donc obtenu un mot de passe al�atoire:<br />";
	}
	else {
		$chaine="Les �l�ves suivants n'ont pas le num�ro INE renseign�.<br />Ils ont donc obtenu un mot de passe al�atoire:<br />";
	}

	for($i=0;$i<count($tab_non_INE_password);$i++) {
		if($i>0) {$chaine.=", ";}
		$chaine.=$tab_non_INE_password[$i];
	}

	echo "<script type='text/javascript'>
	document.getElementById('div_mdp_non_ine').innerHTML=\"$chaine\";
	document.getElementById('div_mdp_non_ine').style.visibility='visible';
</script>\n";
}

require("../lib/footer.inc.php");
?>