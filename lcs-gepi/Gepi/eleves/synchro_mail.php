<?php
/*
 * $Id: synchro_mail.php 8183 2011-09-10 11:52:36Z crob $
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

$sql="SELECT 1=1 FROM droits WHERE id='/eleves/synchro_mail.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/eleves/synchro_mail.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Synchronisation des mail �l�ves',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


if(!isset($msg)){
	$msg="";
}

$suppr_infos_actions_diff_mail=isset($_GET['suppr_infos_actions_diff_mail']) ? $_GET['suppr_infos_actions_diff_mail'] : "n";

if((isset($_GET['synchroniser']))&&($_GET['synchroniser']=='y')) {
	check_token();

	$sql="SELECT u.*, e.email as e_email FROM utilisateurs u, eleves e WHERE e.login=u.login AND u.statut='eleve' AND u.email!=e.email ORDER BY e.nom, e.prenom;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg="Toutes les adresses mail �l�ves sont d�j� synchronis�es entre les tables 'eleves' et 'utilisateurs'.<br />\n";
	}
	else {
		$cpt=0;
		$erreur=0;
		if(getSettingValue('mode_email_ele')=='sconet') {
			while($lig=mysql_fetch_object($res)) {
				$sql="UPDATE utilisateurs SET email='$lig->e_email' WHERE login='$lig->login' AND statut='eleve';";
				$update=mysql_query($sql);
				if($update) {
					$cpt++;
				}
				else {
					$erreur++;
				}
			}
		}
		elseif(getSettingValue('mode_email_ele')=='mon_compte') {
			while($lig=mysql_fetch_object($res)) {
				$sql="UPDATE eleves SET email='$lig->email' WHERE login='$lig->login';";
				$update=mysql_query($sql);
				if($update) {
					$cpt++;
				}
				else {
					$erreur++;
				}
			}
		}

		if($cpt==0) {
			$msg="Aucune adresse n'a �t� mise � jour.<br />";
		}
		elseif($cpt==1) {
			$msg="Une adresse a �t� mise � jour.<br />";
			$suppr_infos_actions_diff_mail="y";
		}
		else {
			$msg="$cpt adresses ont �t� mises � jour.<br />";
			$suppr_infos_actions_diff_mail="y";
		}

		if($erreur==1) {
			$msg.="Une erreur s'est produite.<br />";
		}
		elseif($erreur>1) {
			$msg.="$erreur erreurs se sont produites.<br />";
		}
	}
}

//if((isset($_GET['suppr_infos_actions_diff_mail']))&&($_GET['suppr_infos_actions_diff_mail']=='y')) {
if($suppr_infos_actions_diff_mail=='y') {
	check_token();

	$sql="select * from infos_actions where titre like 'Adresse mail non synchro pour%' and description like '%adresse email renseign�e par l%�l�ve%';";
	$test_infos_actions=mysql_query($sql);
	if(mysql_num_rows($test_infos_actions)>0) {
		$sql="delete from infos_actions where titre like 'Adresse mail non synchro pour%' and description like '%adresse email renseign�e par l%�l�ve%';";
		$del=mysql_query($sql);
		if(!$del) {
			$msg.="ERREUR lors de la suppression des signalements de diff�rence de mail en page d'accueil.<br />\n";
		}
		else {
			$msg.="Suppression des signalements de diff�rence de mail en page d'accueil effectu�e.<br />\n";
		}
	}
	else {
		$msg.="Aucun signalement n'existait en page d'accueil.<br />\n";
	}
}

//**************** EN-TETE *******************************
$titre_page = "Synchronisation des adresses mail �l�ves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ***************************

//debug_var();

if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0){
		echo "<p>Une conversion des donn�es responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>Une conversion des donn�es �l�ves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p>Une conversion des donn�es �l�ves/responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}

?>
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
<?php
	$sql="select * from infos_actions where titre like 'Adresse mail non synchro pour%' and description like '%adresse email renseign�e par l%�l�ve%';";
	$test_infos_actions=mysql_query($sql);
	if(mysql_num_rows($test_infos_actions)>0) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?suppr_infos_actions_diff_mail=y".add_token_in_url()."'>Supprimer les signalements de diff�rences en page d'accueil</a>";
	}
	echo "</p>\n";

	$sql="SELECT u.*, e.email as e_email FROM utilisateurs u, eleves e WHERE e.login=u.login AND u.statut='eleve' AND u.email!=e.email ORDER BY e.nom, e.prenom;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Toutes les adresses mail �l�ves sont synchronis�es entre les tables 'eleves' et 'utilisateurs'.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>".mysql_num_rows($res)." adresses mail �l�ves diff�rent entre les tables 'eleves' et 'utilisateurs'.</p>\n";

	echo "<table class='boireaus' summary='Tableau des diff�rences'>\n";
	echo "<tr>\n";
	echo "<th>Nom</th>\n";
	echo "<th>Prenom</th>\n";
	echo "<th>Email utilisateur<br />(<i>G�rer mon compte</i>)</th>\n";
	echo "<th>Email �l�ve<br />(<i>Sconet,...</i>)</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysql_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td><a href='modify_ele.php?eleve_login=$lig->login'>$lig->nom</a></td>\n";
		echo "<td>$lig->prenom</td>\n";
		echo "<td>$lig->email</td>\n";
		echo "<td>$lig->e_email</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo "<p>Le param�trage de la synchronisation est actuellement&nbsp;:".getSettingValue('mode_email_ele')."</p>\n";

	if(getSettingValue('mode_email_ele')=='sconet') {
		echo "<p>Pour mettre � jour les email des comptes d'utilisateurs d'apr�s les valeurs Sconet, <a href='".$_SERVER['PHP_SELF']."?synchroniser=y".add_token_in_url()."'>cliquez ici</a>.</p>\n";
	}
	elseif(getSettingValue('mode_email_ele')=='mon_compte') {
		echo "<p>Pour mettre � jour les email des �l�ves d'apr�s les valeurs des comptes d'utilisateurs, <a href='".$_SERVER['PHP_SELF']."?synchroniser=y".add_token_in_url()."'>cliquez ici</a>.</p>\n";
	}
	elseif(getSettingValue('mode_email_ele')=='sso') {
		echo "<p style='color:red'>Situation non encore g�r�e.</p>\n";
	}

	if($_SESSION['statut']=='administrateur') {
		echo "<p>Ce param�trage peut �tre modifi� dans <a href='../gestion/param_gen.php#mode_email_ele'>Configuration g�n�rale</a></p>\n";
	}

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
?>
