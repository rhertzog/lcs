<?php
/*
* $Id: param_gen.php 5354 2010-09-20 17:32:08Z crob $
*
* Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg = '';
if (isset($_POST['sup_logo'])) {
	$dest = '../images/';
	$ok = false;
	if ($f = @fopen("$dest/.test", "w")) {
		@fputs($f, '<'.'?php $ok = true; ?'.'>');
		@fclose($f);
		include("$dest/.test");
	}
	if (!$ok) {
		$msg = "Probl�me d'�criture sur le r�pertoire. Veuillez signaler ce probl�me � l'administrateur du site";
	} else {
		$old = getSettingValue("logo_etab");
		if (($old != '') and (file_exists($dest.$old))) unlink($dest.$old);
		$msg = "Le logo a �t� supprim�.";
		if (!saveSetting("logo_etab", '')) $msg .= "Erreur lors de l'enregistrement dans la table setting !";

	}

}
if (isset($_POST['valid_logo'])) {
	$doc_file = isset($_FILES["doc_file"]) ? $_FILES["doc_file"] : NULL;
	//if (ereg("\.([^.]+)$", $doc_file['name'], $match)) {
	//$match=array();
	//if (my_ereg("\.([^.]+)$", $doc_file['name'], $match)) {
	if (((function_exists("mb_ereg"))&&(mb_ereg("\.([^.]+)$", $doc_file['name'], $match)))||((function_exists("ereg"))&&(ereg("\.([^.]+)$", $doc_file['name'], $match)))) {
		$ext = strtolower($match[1]);
		if ($ext!='jpg' and $ext!='png'and $ext!='gif') {
		//if ($ext!='jpg' and $ext!='jpeg' and $ext!='png'and $ext!='gif') {
			$msg = "les seules extensions autoris�es sont gif, png et jpg";
		} else {
			$dest = '../images/';
			$ok = false;
			if ($f = @fopen("$dest/.test", "w")) {
				@fputs($f, '<'.'?php $ok = true; ?'.'>');
				@fclose($f);
				include("$dest/.test");
			}
			if (!$ok) {
				$msg = "Probl�me d'�criture sur le r�pertoire IMAGES. Veuillez signaler ce probl�me � l'administrateur du site";
			} else {
				$old = getSettingValue("logo_etab");
				if (file_exists($dest.$old)) @unlink($dest.$old);
				if (file_exists($dest.$doc_file)) @unlink($dest.$doc_file);
				$ok = @copy($doc_file['tmp_name'], $dest.$doc_file['name']);
				if (!$ok) $ok = @move_uploaded_file($doc_file['tmp_name'], $dest.$doc_file['name']);
				if (!$ok) {
					$msg = "Probl�me de transfert : le fichier n'a pas pu �tre transf�r� sur le r�pertoire IMAGES. Veuillez signaler ce probl�me � l'administrateur du site";
				} else {
					$msg = "Le fichier a �t� transf�r�.";
				}
				if (!saveSetting("logo_etab", $doc_file['name'])) {
				$msg .= "Erreur lors de l'enregistrement dans la table setting !";
				}

			}
		}
	} else {
		$msg = "Le fichier s�lectionn� n'est pas valide !";
	}
}
// Max session length
if (isset($_POST['sessionMaxLength'])) {
	if (!(my_ereg ("^[0-9]{1,}$", $_POST['sessionMaxLength'])) || $_POST['sessionMaxLength'] < 1) {
		$_POST['sessionMaxLength'] = 30;
	}
	if (!saveSetting("sessionMaxLength", $_POST['sessionMaxLength'])) {
		$msg .= "Erreur lors de l'enregistrement da dur�e max d'inactivit� !";
	}
}
if (isset($_POST['gepiSchoolRne'])) {
	if (!saveSetting("gepiSchoolRne", $_POST['gepiSchoolRne'])) {
		$msg .= "Erreur lors de l'enregistrement du num�ro RNE de l'�tablissement !";
	}
}
if (isset($_POST['gepiYear'])) {
	if (!saveSetting("gepiYear", $_POST['gepiYear'])) {
		$msg .= "Erreur lors de l'enregistrement de l'ann�e scolaire !";
	}
}
if (isset($_POST['gepiSchoolName'])) {
	if (!saveSetting("gepiSchoolName", $_POST['gepiSchoolName'])) {
		$msg .= "Erreur lors de l'enregistrement du nom de l'�tablissement !";
	}
}
if (isset($_POST['gepiSchoolStatut'])) {
	if (!saveSetting("gepiSchoolStatut", $_POST['gepiSchoolStatut'])) {
		$msg .= "Erreur lors de l'enregistrement du statut de l'�tablissement !";
	}
}
if (isset($_POST['gepiSchoolAdress1'])) {
	if (!saveSetting("gepiSchoolAdress1", $_POST['gepiSchoolAdress1'])) {
		$msg .= "Erreur lors de l'enregistrement de l'adresse !";
	}
}
if (isset($_POST['gepiSchoolAdress2'])) {
	if (!saveSetting("gepiSchoolAdress2", $_POST['gepiSchoolAdress2'])) {
		$msg .= "Erreur lors de l'enregistrement de l'adresse !";
	}
}
if (isset($_POST['gepiSchoolZipCode'])) {
	if (!saveSetting("gepiSchoolZipCode", $_POST['gepiSchoolZipCode'])) {
		$msg .= "Erreur lors de l'enregistrement du code postal !";
	}
}
if (isset($_POST['gepiSchoolCity'])) {
	if (!saveSetting("gepiSchoolCity", $_POST['gepiSchoolCity'])) {
		$msg .= "Erreur lors de l'enregistrement de la ville !";
	}
}
if (isset($_POST['gepiSchoolPays'])) {
	if (!saveSetting("gepiSchoolPays", $_POST['gepiSchoolPays'])) {
		$msg .= "Erreur lors de l'enregistrement du pays !";
	}
}
if (isset($_POST['gepiSchoolAcademie'])) {
	if (!saveSetting("gepiSchoolAcademie", $_POST['gepiSchoolAcademie'])) {
		$msg .= "Erreur lors de l'enregistrement de l'acad�mie !";
	}
}
if (isset($_POST['gepiSchoolTel'])) {
	if (!saveSetting("gepiSchoolTel", $_POST['gepiSchoolTel'])) {
		$msg .= "Erreur lors de l'enregistrement du num�ro de t�l�phone !";
	}
}
if (isset($_POST['gepiSchoolFax'])) {
	if (!saveSetting("gepiSchoolFax", $_POST['gepiSchoolFax'])) {
		$msg .= "Erreur lors de l'enregistrement du num�ro de fax !";
	}
}
if (isset($_POST['gepiSchoolEmail'])) {
	if (!saveSetting("gepiSchoolEmail", $_POST['gepiSchoolEmail'])) {
		$msg .= "Erreur lors de l'adresse �lectronique !";
	}
}
if (isset($_POST['gepiAdminNom'])) {
	if (!saveSetting("gepiAdminNom", $_POST['gepiAdminNom'])) {
		$msg .= "Erreur lors de l'enregistrement du nom de l'administrateur !";
	}
}
if (isset($_POST['gepiAdminPrenom'])) {
	if (!saveSetting("gepiAdminPrenom", $_POST['gepiAdminPrenom'])) {
		$msg .= "Erreur lors de l'enregistrement du pr�nom de l'administrateur !";
	}
}
if (isset($_POST['gepiAdminFonction'])) {
	if (!saveSetting("gepiAdminFonction", $_POST['gepiAdminFonction'])) {
		$msg .= "Erreur lors de l'enregistrement de la fonction de l'administrateur !";
	}
}

if (isset($_POST['gepiAdminAdress'])) {
	if (!saveSetting("gepiAdminAdress", $_POST['gepiAdminAdress'])) {
		$msg .= "Erreur lors de l'enregistrement de l'adresse email !";
	}
}

if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {
		if (isset($_POST['gepiAdminAdressPageLogin'])) {
			if (!saveSetting("gepiAdminAdressPageLogin", 'y')) {
				$msg .= "Erreur lors de l'enregistrement de l'affichage de adresse email sur la page de login !";
			}
		}
		else{
			if (!saveSetting("gepiAdminAdressPageLogin", 'n')) {
				$msg .= "Erreur lors de l'enregistrement du non-affichage de adresse email sur la page de login !";
			}
		}

		if (isset($_POST['contact_admin_mailto'])) {
			if (!saveSetting("contact_admin_mailto", 'y')) {
				$msg .= "Erreur lors de l'enregistrement de 'contact_admin_mailto' !";
			}
		}
		else {
			if (!saveSetting("contact_admin_mailto", 'n')) {
				$msg .= "Erreur lors de l'enregistrement de 'contact_admin_mailto' !";
			}
		}
		
	}
}

if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {
		if (isset($_POST['gepiAdminAdressFormHidden'])) {
			if (!saveSetting("gepiAdminAdressFormHidden", 'n')) {
				$msg .= "Erreur lors de l'enregistrement de l'affichage de adresse email dans le formulaire [Contacter l'administrateur] !";
			}
		}
		else{
			if (!saveSetting("gepiAdminAdressFormHidden", 'y')) {
				$msg .= "Erreur lors de l'enregistrement du non-affichage de adresse email dans le formulaire [Contacter l'administrateur] !";
			}
		}
	}
}


if (isset($_POST['longmin_pwd'])) {
	if (!saveSetting("longmin_pwd", $_POST['longmin_pwd'])) {
		$msg .= "Erreur lors de l'enregistrement de la longueur minimale du mot de passe !";
	}
}

if (isset($_POST['mode_generation_pwd_majmin'])) {
	if (!saveSetting("mode_generation_pwd_majmin", $_POST['mode_generation_pwd_majmin'])) {
		$msg .= "Erreur lors de l'enregistrement du param�tre Min/Maj sur les mots de passe !";
	}
}

if (isset($_POST['is_posted'])) {
	if (isset($_POST['mode_generation_pwd_excl'])) {
		if (!saveSetting("mode_generation_pwd_excl", $_POST['mode_generation_pwd_excl'])) {
			$msg .= "Erreur lors de l'enregistrement du param�tre d'exclusion des caract�res pr�tant � confusion sur les mots de passe !";
		}
	}
	else{
		if (!saveSetting("mode_generation_pwd_excl", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du param�tre d'exclusion des caract�res pr�tant � confusion sur les mots de passe !";
		}
	}

	//===============================================================
	// Traitement des problemes de points d'interrogation � la place des accents
	if (isset($_POST['mode_utf8_bulletins_pdf'])) {
		if (!saveSetting("mode_utf8_bulletins_pdf", $_POST['mode_utf8_bulletins_pdf'])) {
			$msg .= "Erreur lors de l'enregistrement du param�tre mode_utf8_bulletins_pdf !";
		}
	}
	else{
		if (!saveSetting("mode_utf8_bulletins_pdf", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du param�tre mode_utf8_bulletins_pdf !";
		}
	}
	/*
	if (isset($_POST['mode_utf8_listes_pdf'])) {
		if (!saveSetting("mode_utf8_listes_pdf", $_POST['mode_utf8_listes_pdf'])) {
			$msg .= "Erreur lors de l'enregistrement du param�tre mode_utf8_listes_pdf !";
		}
	}
	else{
		if (!saveSetting("mode_utf8_listes_pdf", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du param�tre mode_utf8_listes_pdf !";
		}
	}
	*/
	if (isset($_POST['mode_utf8_visu_notes_pdf'])) {
		if (!saveSetting("mode_utf8_visu_notes_pdf", $_POST['mode_utf8_visu_notes_pdf'])) {
			$msg .= "Erreur lors de l'enregistrement du param�tre mode_utf8_visu_notes_pdf !";
		}
	}
	else{
		if (!saveSetting("mode_utf8_visu_notes_pdf", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du param�tre mode_utf8_visu_notes_pdf !";
		}
	}

	if (isset($_POST['type_bulletin_par_defaut'])) {
		if(($_POST['type_bulletin_par_defaut']=='html')||($_POST['type_bulletin_par_defaut']=='pdf')) {
			if (!saveSetting("type_bulletin_par_defaut", $_POST['type_bulletin_par_defaut'])) {
				$msg .= "Erreur lors de l'enregistrement du param�tre type_bulletin_par_defaut !";
			}
		}
		else {
			$msg .= "Valeur erron�e pour l'enregistrement du param�tre type_bulletin_par_defaut !";
		}
	}

	/*
	if (isset($_POST['mode_utf8_releves_pdf'])) {
		if (!saveSetting("mode_utf8_releves_pdf", $_POST['mode_utf8_releves_pdf'])) {
			$msg .= "Erreur lors de l'enregistrement du param�tre mode_utf8_releves_pdf !";
		}
	}
	else{
		if (!saveSetting("mode_utf8_releves_pdf", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du param�tre mode_utf8_releves_pdf !";
		}
	}
	*/

	if (isset($_POST['exp_imp_chgt_etab'])) {
		if (!saveSetting("exp_imp_chgt_etab", $_POST['exp_imp_chgt_etab'])) {
			$msg .= "Erreur lors de l'enregistrement du param�tre exp_imp_chgt_etab !";
		}
	}
	else{
		if (!saveSetting("exp_imp_chgt_etab", 'no')) {
			$msg .= "Erreur lors de l'enregistrement du param�tre exp_imp_chgt_etab !";
		}
	}

	if (isset($_POST['ele_lieu_naissance'])) {
		if (!saveSetting("ele_lieu_naissance", $_POST['ele_lieu_naissance'])) {
			$msg .= "Erreur lors de l'enregistrement du param�tre ele_lieu_naissance !";
		}
	}
	else{
		if (!saveSetting("ele_lieu_naissance", 'no')) {
			$msg .= "Erreur lors de l'enregistrement du param�tre ele_lieu_naissance !";
		}
	}

	if (isset($_POST['avis_conseil_classe_a_la_mano'])) {
		if (!saveSetting("avis_conseil_classe_a_la_mano", $_POST['avis_conseil_classe_a_la_mano'])) {
			$msg .= "Erreur lors de l'enregistrement du param�tre avis_conseil_classe_a_la_mano !";
		}
	}
	else{
		if (!saveSetting("avis_conseil_classe_a_la_mano", 'n')) {
			$msg .= "Erreur lors de l'enregistrement du param�tre avis_conseil_classe_a_la_mano !";
		}
	}


	//===============================================================
}

// D�nomination du professeur de suivi
if (isset($_POST['gepi_prof_suivi'])) {
	if (!saveSetting("gepi_prof_suivi", $_POST['gepi_prof_suivi'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_prof_suivi !";
	}
}

// D�nomination des professeurs
if (isset($_POST['denomination_professeur'])) {
	if (!saveSetting("denomination_professeur", $_POST['denomination_professeur'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_professeur !";
	}
}
if (isset($_POST['denomination_professeurs'])) {
	if (!saveSetting("denomination_professeurs", $_POST['denomination_professeurs'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_professeurs !";
	}
}

// D�nomination des responsables l�gaux
if (isset($_POST['denomination_responsable'])) {
	if (!saveSetting("denomination_responsable", $_POST['denomination_responsable'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_responsable !";
	}
}
if (isset($_POST['denomination_responsables'])) {
	if (!saveSetting("denomination_responsables", $_POST['denomination_responsables'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_responsables !";
	}
}

// D�nomination des �l�ves
if (isset($_POST['denomination_eleve'])) {
	if (!saveSetting("denomination_eleve", $_POST['denomination_eleve'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_eleve !";
	}
}
if (isset($_POST['denomination_eleves'])) {
	if (!saveSetting("denomination_eleves", $_POST['denomination_eleves'])) {
		$msg .= "Erreur lors de l'enregistrement de denomination_eleves !";
	}
}
// Initialiser � 'Boite'
if (isset($_POST['gepi_denom_boite'])) {
	if (!saveSetting("gepi_denom_boite", $_POST['gepi_denom_boite'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_denom_boite !";
	}
}
if (isset($_POST['gepi_denom_boite_genre'])) {
	if (!saveSetting("gepi_denom_boite_genre", $_POST['gepi_denom_boite_genre'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_denom_boite_genre !";
	}
}

if (isset($_POST['gepi_stylesheet'])) {
	if (!saveSetting("gepi_stylesheet", $_POST['gepi_stylesheet'])) {
		$msg .= "Erreur lors de l'enregistrement de l'ann�e scolaire !";
	}
}

if (isset($_POST['num_enregistrement_cnil'])) {
	if (!saveSetting("num_enregistrement_cnil", $_POST['num_enregistrement_cnil'])) {
		$msg .= "Erreur lors de l'enregistrement du num�ro d'enregistrement � la CNIL !";
	}
}

if (isset($_POST['mode_generation_login'])) {
	if (!saveSetting("mode_generation_login", $_POST['mode_generation_login'])) {
		$msg .= "Erreur lors de l'enregistrement du mode de g�n�ration des logins !";
	}
	// On en profite pour mettre � jour la variable $longmax_login -> settings : longmax_login
			$nbre_carac = 12;
		if ($_POST['mode_generation_login'] == 'name8' OR $_POST['mode_generation_login'] == 'fname8' OR $_POST['mode_generation_login'] == 'namef8') {
			$nbre_carac = 8;
		}
		elseif ($_POST['mode_generation_login'] == 'fname19' OR $_POST['mode_generation_login'] == 'firstdotname19') {
			$nbre_carac = 19;
		}
		elseif ($_POST['mode_generation_login'] == 'firstdotname') {
			$nbre_carac = 30;
		}
		else {
			$nbre_carac = 12;
		}
	$req = "UPDATE setting SET value = '".$nbre_carac."' WHERE name = 'longmax_login'";
	$modif_maxlong = mysql_query($req);
}


if (isset($_POST['unzipped_max_filesize'])) {
	$unzipped_max_filesize=$_POST['unzipped_max_filesize'];
	if(substr($unzipped_max_filesize,0,1)=="-") {$unzipped_max_filesize=-1;}
	elseif(strlen(my_ereg_replace("[0-9]","",$unzipped_max_filesize))!=0) {
		$unzipped_max_filesize=10;
		$msg .= "Caract�res invalides pour le param�tre unzipped_max_filesize<br />Initialisation � 10 Mo !";
	}

	if (!saveSetting("unzipped_max_filesize", $unzipped_max_filesize)) {
		$msg .= "Erreur lors de l'enregistrement du param�tre unzipped_max_filesize !";
	}
}


if (isset($_POST['gepi_pmv'])) {
	if (!saveSetting("gepi_pmv", $_POST['gepi_pmv'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_pmv !";
	}
}

if (isset($_POST['delais_apres_cloture'])) {
	$delais_apres_cloture=$_POST['delais_apres_cloture'];
	if (!(my_ereg ("^[0-9]{1,}$", $delais_apres_cloture)) || $delais_apres_cloture < 0) {
		//$delais_apres_cloture=0;
		$msg .= "Erreur lors de l'enregistrement de delais_apres_cloture !";
	}
	else {
		if (!saveSetting("delais_apres_cloture", $delais_apres_cloture)) {
			$msg .= "Erreur lors de l'enregistrement de delais_apres_cloture !";
		}
	}
}

if (isset($_POST['acces_app_ele_resp'])) {
	$acces_app_ele_resp=$_POST['acces_app_ele_resp'];
	if (!saveSetting("acces_app_ele_resp", $acces_app_ele_resp)) {
		$msg .= "Erreur lors de l'enregistrement de acces_app_ele_resp !";
	}
}

/*
if(isset($_POST['is_posted'])){
	if (isset($_POST['export_cn_ods'])) {
		//if (!saveSetting("export_cn_ods", $_POST['export_cn_ods'])) {
		if (!saveSetting("export_cn_ods", 'y')) {
			$msg .= "Erreur lors de l'enregistrement de l'autorisation de l'export au format ODS !";
		}
	}
	else{
		if (!saveSetting("export_cn_ods", 'n')) {
			$msg .= "Erreur lors de l'enregistrement de l'interdiction de l'export au format ODS !";
		}
	}
}
*/

// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont �t� enregistr�es !";


//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
// End standart header
$titre_page = "Param�tres g�n�raux";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold><a href="index.php#param_gen"<?php
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
?>><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<form action="param_gen.php" method="post" name="form1" style="width: 100%;">
<table style="width: 100%; border: 0;" cellpadding="5" cellspacing="5" summary='Param�tres'>
	<tr>
		<td style="width: 60%;font-variant: small-caps;">
		Ann�e scolaire :
		</td>
		<td><input type="text" name="gepiYear" size="20" value="<?php echo(getSettingValue("gepiYear")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Num�ro RNE de l'�tablissement :
		</td>
		<td><input type="text" name="gepiSchoolRne" size="8" value="<?php echo(getSettingValue("gepiSchoolRne")); ?>" onchange='changement()' />
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		Nom de l'�tablissement :
		</td>
		<td><input type="text" name="gepiSchoolName" size="20" value="<?php echo(getSettingValue("gepiSchoolName")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Statut de l'�tablissement :<br />
		(<span style='font-style:italic;font-size:x-small'>utilis� pour certains documents officiels</span>)
		</td>
		<td>
                    <select name='gepiSchoolStatut' onchange='changement()'>
			<option value='public'<?php if (getSettingValue("gepiSchoolStatut")=='public') echo " SELECTED"; ?>>�tablissement public</option>
			<option value='prive_sous_contrat'<?php if (getSettingValue("gepiSchoolStatut")=='prive_sous_contrat') echo " SELECTED"; ?>>�tablissement priv� sous contrat</option>
			<option value='prive_hors_contrat'<?php if (getSettingValue("gepiSchoolStatut")=='prive_hors_contrat') echo " SELECTED"; ?>>�tablissement priv� hors contrat</option>
                    </select>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Adresse de l'�tablissement :
		</td>
		<td><input type="text" name="gepiSchoolAdress1" size="40" value="<?php echo(getSettingValue("gepiSchoolAdress1")); ?>" onchange='changement()' /><br />
		<input type="text" name="gepiSchoolAdress2" size="40" value="<?php echo(getSettingValue("gepiSchoolAdress2")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Code postal :
		</td>
		<td><input type="text" name="gepiSchoolZipCode" size="20" value="<?php echo(getSettingValue("gepiSchoolZipCode")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Ville :
		</td>
		<td><input type="text" name="gepiSchoolCity" size="20" value="<?php echo(getSettingValue("gepiSchoolCity")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Pays :<br />
		(<span style='font-style:italic;font-size:x-small'>Le pays est utilis� pour comparer avec celui des responsables dans les blocs adresse des courriers adress�s aux responsables</span>)
		</td>
		<td><input type="text" name="gepiSchoolPays" size="20" value="<?php echo(getSettingValue("gepiSchoolPays")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Acad�mie :<br />
		(<span style='font-style:italic;font-size:x-small'>utilis� pour certains documents officiels</span>)
		</td>
		<td><input type="text" name="gepiSchoolAcademie" size="20" value="<?php echo(getSettingValue("gepiSchoolAcademie")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		T�l�phone �tablissement :
		</td>
		<td><input type="text" name="gepiSchoolTel" size="20" value="<?php echo(getSettingValue("gepiSchoolTel")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Fax �tablissement :
		</td>
		<td><input type="text" name="gepiSchoolFax" size="20" value="<?php echo(getSettingValue("gepiSchoolFax")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		E-mail �tablissement :
		</td>
		<td><input type="text" name="gepiSchoolEmail" size="20" value="<?php echo(getSettingValue("gepiSchoolEmail")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Nom de l'administrateur du site :
		</td>
		<td><input type="text" name="gepiAdminNom" size="20" value="<?php echo(getSettingValue("gepiAdminNom")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Pr�nom de l'administrateur du site :
		</td>
		<td><input type="text" name="gepiAdminPrenom" size="20" value="<?php echo(getSettingValue("gepiAdminPrenom")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Fonction de l'administrateur du site :
		</td>
		<td><input type="text" name="gepiAdminFonction" size="20" value="<?php echo(getSettingValue("gepiAdminFonction")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Email de l'administrateur du site :
		</td>
		<td><input type="text" name="gepiAdminAdress" size="20" value="<?php echo(getSettingValue("gepiAdminAdress")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<label for='gepiAdminAdressPageLogin' style='cursor: pointer;'>Faire apparaitre le lien [Contacter l'administrateur] sur la page de login :</label>
		</td>
		<td>
		<input type="checkbox" id='gepiAdminAdressPageLogin' name="gepiAdminAdressPageLogin" value="y"
		<?php
			if(getSettingValue("gepiAdminAdressPageLogin")!='n'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<label for='gepiAdminAdressFormHidden' style='cursor: pointer;'>Faire apparaitre l'adresse de l'administrateur dans le formulaire [Contacter l'administrateur] :</label>
		</td>
		<td>
		<input type="checkbox" name="gepiAdminAdressFormHidden" id="gepiAdminAdressFormHidden" value="n"
		<?php
			if(getSettingValue("gepiAdminAdressFormHidden")!='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		<label for='contact_admin_mailto' style='cursor: pointer;'>Remplacer le formulaire [Contacter l'administrateur] par un lien mailto :</label>
		</td>
		<td>
		<input type="checkbox" id='contact_admin_mailto' name="contact_admin_mailto" value="y"
		<?php
			if(getSettingValue("contact_admin_mailto")=='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Dur�e maximum d'inactivit� : <br />
		<span class='small'>(Dur�e d'inactivit�, en minutes, au bout de laquelle un utilisateur est automatiquement d�connect� de Gepi.) Attention, la variable session.maxlifetime dans le fichier php.ini est r�gl�e � <?php echo(ini_get("session.gc_maxlifetime")); ?> secondes, soit un maximum de <?php echo(ini_get("session.gc_maxlifetime")/60); ?> minutes pour la session.</span>
		</td>
		<td><input type="text" name="sessionMaxLength" size="20" value="<?php echo(getSettingValue("sessionMaxLength")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Longueur minimale du mot de passe :</td>
		<td><input type="text" name="longmin_pwd" size="20" value="<?php echo(getSettingValue("longmin_pwd")); ?>" onchange='changement()' />
		</td>
	</tr>
		<?php 
			if (isset($use_custom_denominations) && $use_custom_denominations) {
		?>
	<tr>
		<td style="font-variant: small-caps;">
		D�nomination des professeurs :</td>
		<td>Sing. :<input type="text" name="denomination_professeur" size="20" value="<?php echo(getSettingValue("denomination_professeur")); ?>" onchange='changement()' />
		<br/>Pluriel :<input type="text" name="denomination_professeurs" size="20" value="<?php echo(getSettingValue("denomination_professeurs")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		D�nomination des �l�ves :</td>
		<td>Sing. :<input type="text" name="denomination_eleve" size="20" value="<?php echo(getSettingValue("denomination_eleve")); ?>" />
		<br/>Pluriel :<input type="text" name="denomination_eleves" size="20" value="<?php echo(getSettingValue("denomination_eleves")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		D�nomination des responsables l�gaux :</td>
		<td>Sing. :<input type="text" name="denomination_responsable" size="20" value="<?php echo(getSettingValue("denomination_responsable")); ?>" onchange='changement()' />
		<br/>Pluriel :<input type="text" name="denomination_responsables" size="20" value="<?php echo(getSettingValue("denomination_responsables")); ?>" onchange='changement()' />
		</td>
	</tr>
		<?php 
			} 
		?>
	<tr>
		<td style="font-variant: small-caps;">
		D�nomination du professeur charg� du suivi des �l�ves :</td>
		<td><input type="text" name="gepi_prof_suivi" size="20" value="<?php echo(getSettingValue("gepi_prof_suivi")); ?>" onchange='changement()' />
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		D�signation des boites/conteneurs/emplacements/sous-mati�res :</td>
		<td>
		<input type="text" name="gepi_denom_boite" size="20" value="<?php echo(getSettingValue("gepi_denom_boite")); ?>" onchange='changement()' /><br />
		<table summary='Genre'><tr valign='top'><td>Genre:</td><td>
		<input type="radio" name="gepi_denom_boite_genre" id="gepi_denom_boite_genre_m" value="m" <?php if(getSettingValue("gepi_denom_boite_genre")=="m"){echo 'checked';} ?> onchange='changement()' /> <label for='gepi_denom_boite_genre_m' style='cursor: pointer;'>Masculin</label><br />
		<input type="radio" name="gepi_denom_boite_genre" id="gepi_denom_boite_genre_f" value="f" <?php if(getSettingValue("gepi_denom_boite_genre")=="f"){echo 'checked';} ?> onchange='changement()' /> <label for='gepi_denom_boite_genre_f' style='cursor: pointer;'>F�minin</label><br />
		</td></tr></table>
		</td>
	</tr>
	<tr>
		<td style="font-variant: small-caps;">
		Mode de g�n�ration automatique des logins :</td>
	<td>
	<select name='mode_generation_login' onchange='changement()'>
			<option value='name8'<?php if (getSettingValue("mode_generation_login")=='name8') echo " SELECTED"; ?>> nom (tronqu� � 8 caract�res)</option>
			<option value='fname8'<?php if (getSettingValue("mode_generation_login")=='fname8') echo " SELECTED"; ?>> pnom (tronqu� � 8 caract�res)</option>
			<option value='fname19'<?php if (getSettingValue("mode_generation_login")=='fname19') echo " SELECTED"; ?>> pnom (tronqu� � 19 caract�res)</option>
			<option value='firstdotname'<?php if (getSettingValue("mode_generation_login")=='firstdotname') echo " SELECTED"; ?>> prenom.nom (tronqu� � 30 caract�res)</option>
			<option value='firstdotname19'<?php if (getSettingValue("mode_generation_login")=='firstdotname19') echo " SELECTED"; ?>> prenom.nom (tronqu� � 19 caract�res)</option>
			<option value='namef8'<?php if (getSettingValue("mode_generation_login")=='namef8') echo " SELECTED"; ?>> nomp (tronqu� � 8 caract�res)</option>
	</select>
	</td>
	</tr>


	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		Mode de g�n�ration des mots de passe :<br />(<i style='font-size:small;'>Jeu de caract�res � utiliser en plus des caract�res num�riques</i>)</td>
	<td valign='top'>
		<input type="radio" name="mode_generation_pwd_majmin" id="mode_generation_pwd_majmin_y" value="y" <?php if((getSettingValue("mode_generation_pwd_majmin")=="y")||(getSettingValue("mode_generation_pwd_majmin")=="")) {echo 'checked';} ?> onchange='changement()' /> <label for='mode_generation_pwd_majmin_y' style='cursor: pointer;'>Majuscules et minuscules</label><br />
		<input type="radio" name="mode_generation_pwd_majmin" id="mode_generation_pwd_majmin_n" value="n" <?php if(getSettingValue("mode_generation_pwd_majmin")=="n"){echo 'checked';} ?> onchange='changement()' /> <label for='mode_generation_pwd_majmin_n' style='cursor: pointer;'>Minuscules seulement</label><br />

		<table border='0' summary='Pass'>
		<tr>
		<td valign='top'>
		<input type="checkbox" name="mode_generation_pwd_excl" id="mode_generation_pwd_excl" value="y" <?php if(getSettingValue("mode_generation_pwd_excl")=="y") {echo 'checked';} ?> onchange='changement()' />
		</td>
		<td valign='top'> <label for='mode_generation_pwd_excl' style='cursor: pointer;'>Exclure les caract�res pr�tant � confusion (<i>i, 1, l, I, 0, O, o</i>)</label><br />
		</td>
		</tr>
		</table>
	</td>
	</tr>


	<!-- Traitement des problemes de points d'interrogation � la place des accents -->
<?php
/*
	// Apparemment, ce n'est pas utile...
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_releves_pdf' style='cursor: pointer;'>Traitement UTF8 des caract�res accentu�s des relev�s de notes PDF&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_releves_pdf' name="mode_utf8_releves_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_releves_pdf")=='y'){echo " checked";}
		?>
		/>
		</td>
	</tr>
*/
?>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_visu_notes_pdf' style='cursor: pointer;'>Traitement UTF8 des caract�res accentu�s dans la visualisation des notes du carnet de notes&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_visu_notes_pdf' name="mode_utf8_visu_notes_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_visu_notes_pdf")=='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		Type de bulletins par d�faut&nbsp;:
		</td>
		<td>
		<input type="radio" id='type_bulletin_par_defaut_pdf' name="type_bulletin_par_defaut" value="pdf"
		<?php
			if(getSettingValue("type_bulletin_par_defaut")=='pdf') {echo " checked";}
		?>
		onchange='changement()' /><label for='type_bulletin_par_defaut_pdf'>&nbsp;PDF</label><br />
		<input type="radio" id='type_bulletin_par_defaut_html' name="type_bulletin_par_defaut" value="html"
		<?php
			if(getSettingValue("type_bulletin_par_defaut")!='pdf') {echo " checked";}
		?>
		onchange='changement()' /><label for='type_bulletin_par_defaut_html'>&nbsp;HTML</label>
		</td>
	</tr>

<?php
/*
	// Apparemment, ce n'est pas utile...
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_listes_pdf' style='cursor: pointer;'>Traitement UTF8 des caract�res accentu�s des listes PDF&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_listes_pdf' name="mode_utf8_listes_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_listes_pdf")=='y'){echo " checked";}
		?>
		/>
		</td>
	</tr>
*/
?>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		<label for='mode_utf8_bulletins_pdf' style='cursor: pointer;'>Traitement UTF8 des caract�res accentu�s des bulletins PDF&nbsp;:</label>
		</td>
		<td>
		<input type="checkbox" id='mode_utf8_bulletins_pdf' name="mode_utf8_bulletins_pdf" value="y"
		<?php
			if(getSettingValue("mode_utf8_bulletins_pdf")=='y'){echo " checked";}
		?>
		onchange='changement()' />
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		Feuille de style � utiliser :</td>
	<td>
	<select name='gepi_stylesheet' onchange='changement()'>
			<option value='style'<?php if (getSettingValue("gepi_stylesheet")=='style') echo " SELECTED"; ?>> Nouveau design</option>
			<option value='style_old'<?php if (getSettingValue("gepi_stylesheet")=='style_old') echo " SELECTED"; ?>> Design proche des anciennes versions (1.4.*)</option>
	</select>
	</td>
	</tr>
	<?php
/*
		echo "<tr>\n";
		if(file_exists("../lib/ss_zip.class.php")){
			echo "<td style='font-variant: small-caps;'>Permettre l'export des carnets de notes au format ODS :<br />(<i>si les professeurs ne font pas le m�nage apr�s g�n�ration des exports,<br />ces fichiers peuvent prendre de la place sur le serveur</i>)</td>\n";
			echo "<td><input type='checkbox' name='export_cn_ods' value='y'";
			if(getSettingValue('export_cn_ods')=='y'){
				echo ' checked';
			}
			echo " />";
			echo "</td>\n";
		}
		else{
			echo "<td style='font-variant: small-caps;'>En mettant en place la biblioth�que 'ss_zip_.class.php' dans le dossier '/lib/', vous pouvez g�n�rer des fichiers tableur ODS pour permettre des saisies hors ligne, la conservation de donn�es,...<br />Voir <a href='http://smiledsoft.com/demos/phpzip/' style=''>http://smiledsoft.com/demos/phpzip/</a><br />Une version limit�e est disponible gratuitement.</td>\n";
			echo "<td>&nbsp;</td>\n";

			// Comme la biblioth�que n'est pas pr�sente, on force la valeur � 'n':
			$svg_param=saveSetting("export_cn_ods", 'n');
		}
		echo "</tr>\n";
*/
	?>
	<?php
		echo "<tr>\n";
		if(file_exists("../lib/pclzip.lib.php")){
			echo "<td style='font-variant: small-caps;'>Taille maximale extraite des fichiers d�zipp�s:<br />
(<i style='font-size:small;'>Un fichier d�zipp� peut prendre �norm�ment de place.<br />
Par prudence, il convient de fixer une limite � la taille d'un fichier extrait.<br />
En mettant z�ro, vous ne fixez aucune limite.<br />
En mettant une valeur n�gative, vous d�sactivez le d�sarchivage</i>)</td>\n";
			echo "<td valign='top'><input type='text' name='unzipped_max_filesize' value='";
			$unzipped_max_filesize=getSettingValue('unzipped_max_filesize');
			if($unzipped_max_filesize==""){
				echo '10';
			}
			else {
				echo $unzipped_max_filesize;
			}
			echo "' size='3' onchange='changement()' /> Mo";
			echo "</td>\n";
		}
		else{
			echo "<td style='font-variant: small-caps;'>En mettant en place la biblioth�que 'pclzip.lib.php' dans le dossier '/lib/', vous pouvez envoyer des fichiers Zipp�s vers le serveur.<br />Voir <a href='http://www.phpconcept.net/pclzip/index.php' style=''";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">http://www.phpconcept.net/pclzip/index.php</a></td>\n";
			echo "<td>&nbsp;</td>\n";
		}
		echo "</tr>\n";
	?>

	<!--tr>
		<td style="font-variant: small-caps;">
		<a name='delais_apres_cloture'></a>
		Nombre de jours avant d�verrouillage de l'acc�s aux appr�ciations des bulletins pour les responsables et les �l�ves une fois la p�riode close&nbsp;:<br />
		<div style='font-variant: normal; font-style: italic; font-size: small;'>Sous r�serve:<br />
		<ul>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>de cr�er des comptes pour les responsables et �l�ves,</li>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>d'autoriser l'acc�s aux bulletins simplifi�s ou aux graphes dans <a href='droits_acces.php'<?php
			//echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			?>>Droits d'acc�s</a></li>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>d'opter pour le mode de d�verrouillage automatique sur le crit�re "p�riode close".</li>
		</ul>
		</div>
		</td>
		<td valign='top'>
			<?php
			/*
			$delais_apres_cloture=getSettingValue("delais_apres_cloture");
			if($delais_apres_cloture=="") {$delais_apres_cloture=0;}
			echo "<input type='text' name='delais_apres_cloture' size='2' value='$delais_apres_cloture' onchange='changement()' />\n";
			*/
			?>
		</td>
	</tr-->


	<tr>
		<td style="font-variant: small-caps;">
		<a name='mode_ouverture_acces_appreciations'></a>
		<a name='delais_apres_cloture'></a>
		Mode d'acc�s aux bulletins et r�sultats graphiques, pour les �l�ves et leurs
responsables&nbsp;:<br />
		<div style='font-variant: normal; font-style: italic; font-size: small;'>Sous r�serve:<br />
		<ul>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>de cr�er des comptes pour les responsables et �l�ves,</li>
			<li style='font-variant: normal; font-style: italic; font-size: small;'>d'autoriser l'acc�s aux bulletins simplifi�s ou aux graphes dans <a href='droits_acces.php#bull_simp_ele'<?php
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			?>>Droits d'acc�s</a></li>
		</ul>
		</div>
		</td>
		<td valign='top'>
			<?php
			$acces_app_ele_resp=getSettingValue("acces_app_ele_resp");
			if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';}

			echo "<input type='radio' name='acces_app_ele_resp' id='acces_app_ele_resp_manuel' value='manuel' onchange='changement()' ";
			if($acces_app_ele_resp=='manuel') {echo "checked ";}
			echo "/><label for='acces_app_ele_resp_manuel'>manuel (<i>ouvert par la scolarit�, classe par classe</i>)</label><br />\n";

			echo "<input type='radio' name='acces_app_ele_resp' id='acces_app_ele_resp_date' value='date' onchange='changement()' ";
			if($acces_app_ele_resp=='date') {echo "checked ";}
			echo "/><label for='acces_app_ele_resp_date'>� une date choisie (<i>par la scolarit�</i>)</label><br />\n";

			$delais_apres_cloture=getSettingValue("delais_apres_cloture");
			if($delais_apres_cloture=="") {$delais_apres_cloture=0;}

			echo "<input type='radio' name='acces_app_ele_resp' id='acces_app_ele_resp_periode_close' value='periode_close' onchange='changement()' ";
			if($acces_app_ele_resp=='periode_close') {echo "checked ";}
			echo "/><label for='acces_app_ele_resp_periode_close'> <input type='text' name='delais_apres_cloture' value='$delais_apres_cloture' size='1' onchange='changement()' /> jours apr�s la cl�ture de la p�riode</label>\n";
			?>
		</td>
	</tr>



	<tr>
		<td style="font-variant: small-caps; vertical-align:top;">
		<a name='avis_conseil_classe_a_la_mano'></a>
		Les avis du conseil sont remplis&nbsp;:
		</td>
		<td valign='top'>

			<?php
			$avis_conseil_classe_a_la_mano=getSettingValue("avis_conseil_classe_a_la_mano");
			if($avis_conseil_classe_a_la_mano=="") {$avis_conseil_classe_a_la_mano="n";}

			echo "<input type='radio' name='avis_conseil_classe_a_la_mano' id='avis_conseil_classe_saisis' value='n'";
			if($avis_conseil_classe_a_la_mano=='n') {echo " checked";}
			echo " onchange='changement()' />\n";
			echo "<label for='avis_conseil_classe_saisis' style='cursor: pointer'> avant l'impression des bulletins</label>\n";
			echo "<br />\n";
			echo "<input type='radio' name='avis_conseil_classe_a_la_mano' id='avis_conseil_classe_a_la_mano' value='y'";
			if($avis_conseil_classe_a_la_mano=='y') {echo " checked";}
			echo " onchange='changement()' />";
			echo "<label for='avis_conseil_classe_a_la_mano' style='cursor: pointer'> � la main sur les bulletins imprim�s</label>\n";
			?>
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		<a name='ancre_ele_lieu_naissance'></a>
		<label for='ele_lieu_naissance' style='cursor: pointer'>Faire apparaitre les lieux de naissance des �l�ves&nbsp;:</label><br />
		<div style='font-variant: normal; font-style: italic; font-size: small;'>
			Conditionn� par l'utilisation des 'code_commune_insee' import�s depuis Sconet et par l'import des correspondances 'code_commune_insee/commune' dans la table 'communes' depuis <a href='../eleves/import_communes.php' <?php
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			?>>Import des communes</a>.<br />
		</div>
		</td>
		<td valign='top'>
			<?php
			$ele_lieu_naissance=getSettingValue("ele_lieu_naissance");
			if($ele_lieu_naissance=="") {$ele_lieu_naissance="no";}
			echo "<input type='checkbox' name='ele_lieu_naissance' id='ele_lieu_naissance' value='y'";
			if($ele_lieu_naissance=='y') {echo " checked";}
			echo " onchange='changement()' />\n";
			?>
		</td>
	</tr>

	<tr>
		<td style="font-variant: small-caps;">
		<a name='ancre_exp_imp_chgt_etab'></a>
		<label for='exp_imp_chgt_etab' style='cursor: pointer'>Permettre l'export/import des bulletins d'�l�ves au format CSV&nbsp;:</label><br />
		<div style='font-variant: normal; font-style: italic; font-size: small;'>
			Le fichier peut �tre g�n�r� pour un �l�ve qui quitte l'�tablissement en cours d'ann�e.<br />
			L'�tablissement qui re�oit l'�l�ve peut utiliser ce fichier pour importer les bulletins.<br />
		</div>
		</td>
		<td valign='top'>
			<?php
			$exp_imp_chgt_etab=getSettingValue("exp_imp_chgt_etab");
			if($exp_imp_chgt_etab=="") {$exp_imp_chgt_etab="no";}
			echo "<input type='checkbox' name='exp_imp_chgt_etab' id='exp_imp_chgt_etab' value='yes'";
			if($exp_imp_chgt_etab=='yes') {echo " checked";}
			echo " onchange='changement()' />\n";
			?>
		</td>
	</tr>


	<tr>
		<td style="font-variant: small-caps;">
		N� d'enregistrement � la CNIL : <br />
		<span class='small'>Conform�ment � l'article 16 de la loi 78-17 du 6 janvier 1978, dite loi informatique et libert�,
		cette installation de GEPI doit faire l'objet d'une d�claration de traitement automatis� d'informations nominatives aupr�s
		de la CNIL. Si ce n'est pas encore le cas, laissez libre le champ ci-contre</span>
		</td>
		<td><input type="text" name="num_enregistrement_cnil" size="20" value="<?php echo(getSettingValue("num_enregistrement_cnil")); ?>" onchange='changement()' />
		</td>
	</tr>
</table>
<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" name = "OK" value="Enregistrer" style="font-variant: small-caps;" /></center>
</form>
<hr />
<form enctype="multipart/form-data" action="param_gen.php" method="post" name="form2" style="width: 100%;">
<table border='0' cellpadding="5" cellspacing="5" summary='Logo'>
<?php
echo "<tr><td colspan=2 style=\"font-variant: small-caps;\"><b>Logo de l'�tablissement : </b></td></tr>\n";
echo "<tr><td colspan=2>Le logo est visible sur les bulletins officiels, ainsi que sur la page d'accueil publique des cahiers de texte</td></tr>\n";
echo "<tr><td>Modifier le Logo (png, jpg et gif uniquement) : ";
echo "<input type=\"file\" name=\"doc_file\" onchange='changement()' />\n";
echo "<input type=\"submit\" name=\"valid_logo\" value=\"Enregistrer\" /><br />\n";
echo "Supprimer le logo : <input type=\"submit\" name=\"sup_logo\" value=\"Supprimer le logo\" /></td>\n";


$nom_fic_logo = getSettingValue("logo_etab");

$nom_fic_logo_c = "../images/".$nom_fic_logo;
if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
echo "<td><b>Logo actuel : </b><br /><img src=\"".$nom_fic_logo_c."\" border='0' alt=\"logo\" /></td>\n";
} else {
echo "<td><b><i>Pas de logo actuellement</i></b></td>\n";
}
echo "</tr></table></form>\n";
?>

<p><i>Remarques&nbsp;</i> Les transparences sur les images PNG, GIF ne permettent pas une impression PDF (<i>canal alpha non support� par fpdf</i>).<br />
Il a aussi �t� signal� que les JPEG progressifs/entrelac�s peuvent perturber la g�n�ration de PDF.</p>

<hr />
<form enctype="multipart/form-data" action="param_gen.php" method="post" name="form3" style="width: 100%;">
<table border='0' cellpadding="5" cellspacing="5" summary='Pmv'>
	<tr>
		<td style="font-variant: small-caps;">
		Tester la pr�sence du module phpMyVisite (<i>pmv.php</i>) :</td>
	<td>
		<input type="radio" name="gepi_pmv" id="gepi_pmv_y" value="y" <?php if(getSettingValue("gepi_pmv")!="n"){echo 'checked';} ?> onchange='changement()' /><label for='gepi_pmv_y' style='cursor: pointer;'> Oui</label><br />
		<input type="radio" name="gepi_pmv" id="gepi_pmv_n" value="n" <?php if(getSettingValue("gepi_pmv")=="n"){echo 'checked';} ?> onchange='changement()' /><label for='gepi_pmv_n' style='cursor: pointer;'> Non</label><br />
	</td>
	</tr>
</table>

<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" name = "OK" value="Enregistrer" style="font-variant: small-caps;" /></center>

<table summary='Remarque'><tr><td valign='top'><i>Remarque:</i></td><td>Il arrive que ce test de pr�sence provoque un affichage d'erreur (<i>� propos de pmv.php</i>).<br />
Dans ce cas, d�sactivez simplement le test.</td></tr></table>
</form>
<p><br /></p>

<?php
require("../lib/footer.inc.php");
?>
