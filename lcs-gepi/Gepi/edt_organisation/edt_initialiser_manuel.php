<?php

/**
 *
 *
 * @version $Id: edt_initialiser_manuel.php 4190 2010-03-28 12:30:43Z adminpaulbert $
 * @copyright 2008
 */
// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");
require_once("./fonctions_cours.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

/*/ S�curit�
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}*/

// ====================== Les variables =======================
$initialiser = isset($_POST["initialiser"]) ? $_POST["initialiser"] : NULL;
$choix_prof = isset($_POST["prof"]) ? $_POST["prof"] : NULL;
$enseignement = isset($_POST["enseignement"]) ? $_POST["enseignement"] : NULL;
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] : "";
$ch_heure = isset($_POST["ch_heure"]) ? $_POST["ch_heure"] : NULL;
$ch_jour_semaine = isset($_POST["ch_jour_semaine"]) ? $_POST["ch_jour_semaine"] : NULL;
$duree = isset($_POST["duree"]) ? $_POST["duree"] : NULL;
$heure_debut = isset($_POST["heure_debut"]) ? $_POST["heure_debut"] : NULL;
$choix_semaine = isset($_POST["choix_semaine"]) ? $_POST["choix_semaine"] : NULL;
$login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;
$init = isset($_GET["init"]) ? $_GET["init"] : NULL;

echo "
<html>
<head>
	<title>Titre</title>

	<script type='text/javascript' src='".$gepiPath."/lib/brainjar_drag.js'></script>
	<script type='text/javascript' src='".$gepiPath."/lib/position.js'></script>
</head>

<body style=\"background-color: #eeffee;\">

	<div id=\"edt_aff_manuel\">
<p>Pour les entr&eacute;es simples, la saisie manuelle est possible.
Elle vous permettra de rentrer heure par heure des informations
en v&eacute;rifiant si deux cours ne se chevauchent pas.</p>
<p>Nous vous conseillons pour cela de saisir les emplois du temps des professeurs directement sur leur affichage.
 Pour cela, cliquez sur [Visionner], [Professeur], puis vous choisissez le professeur dans la liste d�roulante.
  En cliquant ensuite sur (-+-), une fen�tre apparait o� vous pouvez saisir les cours. Un module de v�rification
  des cours est pr�sent mais ne peut se substituer � un v�ritable logiciel de fabrication des emplois du temps.</p>

	</div>

	<h4 style=\"color: red\">Attention ! seuls les enseignements
d&eacute;finis dans Gepi peuvent appara&icirc;tre dans l'emploi du temps. Si vous avez des cours sans notes (soutien, ATP,...), vous pouvez utiliser les AID.</h4>
<br />";


	// Saisie manuelle de l'emploi du temps
echo '
	<span class="legend">Vous ne devriez utiliser ce menu que pour de rares occasions car il n\'est pas aussi
	performant que la m�thode d�crite plus haut.</span>
		<form action="edt_initialiser_manuel.php" name="choix_prof" method="post">
	<fieldset id="init_edt1">
		<legend>Saisie manuelle</legend>

		<select name="prof" onchange=\'document.choix_prof.submit();\'>
			<option value="rien">Choix du professeur</option>
	';

	$tab_select = renvoie_liste("prof");

	echo "	\n";
for($i=0;$i<count($tab_select);$i++) {
	if(isset($choix_prof)){
		if($choix_prof==$tab_select[$i]["login"]){
			$selected=" selected='selected'";
		}
		else{
			$selected="";
		}
	}
	else{
		$selected="";
	}
	echo "
			<option value='".$tab_select[$i]["login"]."'".$selected.">".$tab_select[$i]["nom"].' '.$tab_select[$i]["prenom"]."</option>\n";
}

echo '
 		</select>
			<input type="hidden" name="initialiser" value="ok" />
	</fieldset>
		</form>
	<br />';

	// Ensuite, on propose la liste des enseignements de ce professeur associ�s � la mati�re
if (isset($choix_prof)) {
	echo '
			<form action="edt_initialiser_manuel.php" name="choix_enseignement" method="post">
	<fieldset id="init_edt2">
		<legend>Choix du cours</legend>

<table border="0" cellspacing="4" cellpadding="0">
	<tr>
		<td>

			<select name="enseignement">';
	echo "\n";

		$tab_enseignements = get_groups_for_prof($choix_prof);
	echo "
				<option value=\"rien\">Choix de l'enseignement</option>\n";

	// On d�termine le selected
	for($i=0; $i<count($tab_enseignements); $i++) {
		if(isset($enseignement)){
			if($enseignement == $tab_enseignements[$i]["id"]){
				$selected = ' selected="selected"';
			}else{
				$selected = '';
			}
		}else{
			$selected = '';
		}
			echo "
				<option value=\"".$tab_enseignements[$i]["id"]."\"".$selected.">".$tab_enseignements[$i]["classlist_string"]." : ".$tab_enseignements[$i]["description"]."</option>\n";
	}

	// On ajoute sa liste des aid
		$tab_aid = renvoieAid("prof", $choix_prof);
	for($i = 0; $i < count($tab_aid); $i++) {
		$nom_aid = mysql_fetch_array(mysql_query("SELECT nom FROM aid WHERE id = '".$tab_aid[$i]["id_aid"]."'"));
		echo '
				<option value="AID|'.$tab_aid[$i]["id_aid"].'">'.$nom_aid["nom"].'</option>
		';
	}

	echo '
			</select>

			<input type="hidden" name="initialiser" value="ok" />
			<input type="hidden" name="prof" value="'.$choix_prof.'" />
		</td>
		<td>
			<select name="ch_jour_semaine">
				<option value="rien">Jour</option>';
	echo "\n";

	// On propose aussi le choix du jour

	$req_jour = mysql_query("SELECT id_horaire_etablissement, jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1");
	$rep_jour = mysql_fetch_array($req_jour);
	$nbre = mysql_num_rows($req_jour);

	$tab_select_jour = array();

	for($a = 0; $a < $nbre; $a++) {
		$tab_select_jour[$a]["id"] = mysql_result($req_jour, $a, "id_horaire_etablissement");
		$tab_select_jour[$a]["jour_sem"] = mysql_result($req_jour, $a, "jour_horaire_etablissement");
		if(isset($ch_jour_semaine)){
			if($ch_jour_semaine == $tab_select_jour[$a]["jour_sem"]){
				$selected = " selected='selected'";
			}else{
				$selected = "";
			}
		}else{
			$selected = "";
		}
		echo "
		<option value='".$tab_select_jour[$a]["jour_sem"]."'".$selected.">".$tab_select_jour[$a]["jour_sem"]."</option>\n";
	}
	echo '
			</select>
		</td>

		<td>
			<select name="ch_heure">
				<option value="rien">Horaire</option>';
	echo "\n";
	// On propose aussi le choix de l'horaire

	$req_heure = mysql_query("SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	$rep_heure = mysql_num_rows($req_heure);
	$tab_select_heure = array();

	for($b=0; $b<$rep_heure; $b++) {

		$tab_select_heure[$b]["id_heure"] = mysql_result($req_heure, $b, "id_definie_periode");
		$tab_select_heure[$b]["creneaux"] = mysql_result($req_heure, $b, "nom_definie_periode");
		$tab_select_heure[$b]["heure_debut"] = mysql_result($req_heure, $b, "heuredebut_definie_periode");
		$tab_select_heure[$b]["heure_fin"] = mysql_result($req_heure, $b, "heurefin_definie_periode");
		if(isset($ch_heure)){
			if($ch_heure == $tab_select_heure[$b]["id_heure"]){
				$selected=" selected='selected'";
			}else{
				$selected="";
			}
		}else{
			$selected="";
		}
		echo "
		<option value='".$tab_select_heure[$b]["id_heure"]."'".$selected.">".$tab_select_heure[$b]["creneaux"]." : ".$tab_select_heure[$b]["heure_debut"]." - ".$tab_select_heure[$b]["heure_fin"]."</option>\n";

	}
	echo '
			</select>
		</td>
		<td>
		</td>
	</tr>
	<tr>
		<td>
			<select name="heure_debut">
				<option value="0">Le cours commence au d�but d\'un cr�neau</option>
				<option value="0.5">Le cours commence au milieu d\'un cr�neau</option>
			</select>
		</td>';

	// On propose aussi le choix du type de semaine et l'heure de d�but du cours

	echo '
		<td>
			<select name="duree">
				<option value="2">1 heure</option>
				<option value="3">1.5 heure</option>
				<option value="4">2 heures</option>
				<option value="5">2.5 heures</option>
				<option value="6">3 heures</option>
				<option value="7">3.5 heures</option>
				<option value="8">4 heures</option>
				<option value="0.5">1/2 heure</option>
			</select>
		</td>
		<td>
			<select name="choix_semaine">
				<option value="0">Toutes les semaines</option>
		';
	// on r�cup�re les types de semaines
				//<option value="2">Semaines paires</option>
				//<option value="1">Semaines impaires</option>
	$req_semaines = mysql_query('SELECT SQL_SMALL_RESULT DISTINCT type_edt_semaine FROM edt_semaines LIMIT 10');
	//mysql_query("SELECT type_edt_semaine FROM edt_semaines");
	//mysql_query('SELECT SQL_SMALL_RESULT DISTINCT type_edt_semaine FROM edt_semaines LIMIT 10');
	$nbre_semaines = mysql_num_rows($req_semaines);

	for ($s=0; $s<$nbre_semaines; $s++) {
			$rep_semaines[$s]["type_edt_semaine"] = mysql_result($req_semaines, $s, "type_edt_semaine");
		echo '
				<option value="'.$rep_semaines[$s]["type_edt_semaine"].'">Semaine '.$rep_semaines[$s]["type_edt_semaine"].'</option>
		';
	}

	echo '
			</select>
		</td>
		<td>
		</td>
	</tr>
	<tr>
		<td>
			<select  name="login_salle">
				<option value="rien">Salle</option>
	';
	// Choix de la salle
	$tab_select_salle = renvoie_liste("salle");

	for($c=0;$c<count($tab_select_salle);$c++) {
		if(isset($login_salle)){
			if($login_salle==$tab_select_salle[$c]["id_salle"]){
				$selected=" selected='selected'";
			}else{
				$selected="";
			}
		}else{
			$selected="";
		}
		echo "
				<option value='".$tab_select_salle[$c]["id_salle"]."'".$selected.">".$tab_select_salle[$c]["nom_salle"]."</option>\n";
	}
	echo '
			</select>
		</td>
		<td>
			<select name="periode_calendrier">
				<option value="rien">Ann�e enti�re</option>
	';
	// Choix de la p�riode d�finie dans le calendrier
	$req_calendrier = mysql_query("SELECT * FROM edt_calendrier WHERE etabferme_calendrier = '1' AND etabvacances_calendrier = '0'");
	$nbre_calendrier = mysql_num_rows($req_calendrier);
		for ($a=0; $a<$nbre_calendrier; $a++) {
			$rep_calendrier[$a]["id_calendrier"] = mysql_result($req_calendrier, $a, "id_calendrier");
			$rep_calendrier[$a]["nom_calendrier"] = mysql_result($req_calendrier, $a, "nom_calendrier");
			echo '
				<option value="'.$rep_calendrier[$a]["id_calendrier"].'">'.$rep_calendrier[$a]["nom_calendrier"].'</option>
			'."\n";
		}

	echo '
			</select>
		</td>
		<td>
			<input type="submit" name="Valider" value="Valider" />
		</td>
		<td>
		</td>
	</tr>
	</table>
	</fieldset>
	</form>';

	// Traitement et enregistrement des entr�es manuelles de l'EdT

	if (isset($choix_prof) AND ($enseignement == "rien" OR $login_salle == "rien" OR $ch_heure == "rien" OR $ch_jour_semaine == "rien")) {
		echo '
	<p class="refus">Vous devez renseigner tous les champs !</p><br />';
	}else {
		if (isset($choix_prof) AND $enseignement != NULL) {
			//echo "<font color=\"green\">OK Tout Est OK !</font>";


			// V�rification que la salle est libre � ce jour cette heure
			$verif_salle = mysql_query("SELECT id_cours FROM edt_cours WHERE
						id_salle ='".$login_salle."' AND
						jour_semaine = '".$ch_jour_semaine."' AND
						id_definie_periode = '".$ch_heure."' AND
						(id_semaine = '0' OR id_semaine = '".$choix_semaine."')")
							 or die ('Erreur dans la verif salle');
			$rep_verif_s = mysql_fetch_array($verif_salle);

			$nbre_verif_s = mysql_num_rows($verif_salle);
			if ($nbre_verif_s != 0) {
				$req_present_s = mysql_query("SELECT id_groupe id_aid, FROM edt_cours WHERE id_cours = '".$rep_verif_s['id_cours']."'");
				$rep_present_s = mysql_fetch_array($req_present_s);
				// On v�rifie si ce n'est pas une AID
				if ($rep_present_s['id_aid'] != "") {
					$aid = $rep_present_s['id_aid'];
					echo "<p class=\"refus\">Cette salle est d�j� occup�e par un groupe AID( ".$aid." ).</p>";
				}else{
					$tab_present_s = get_group($rep_present_s["id_groupe"]);
					echo "<p class=\"refus\">Cette salle est d�j� occup�e par les ".$tab_present_s["classlist_string"]." en ".$tab_present_s["description"]."</p><br />";
				}

			}

			// V�rification que ce prof n'a pas d�j� cours � ce moment l�
			$verif_prof = mysql_query("SELECT * FROM edt_cours, j_groupes_professeurs WHERE
									edt_cours.jour_semaine = '".$ch_jour_semaine."' AND
									edt_cours.id_definie_periode = '".$ch_heure."' AND
									(edt_cours.id_semaine = '".$choix_semaine."' OR edt_cours.id_semaine = '0') AND
									edt_cours.id_groupe = j_groupes_professeurs.id_groupe AND
									login = '".$choix_prof."' AND
									edt_cours.heuredeb_dec = '".$heure_debut."' AND
									edt_cours.login_prof = '".$choix_prof."'")
										or die('erreur verif prof !');
			$rep_verif_prof = mysql_fetch_array($verif_prof);
			$nbre_verif_prof = mysql_num_rows($verif_prof);
			if ($nbre_verif_prof != 0) {
				// On v�rifie si ce n'est pas une AID
				if ($verif_prof['id_aid'] != "") {
					$aid = $verif_prof['id_aid'];
					echo "<p class=\"refus\">Ce professeur a d�j� cours avec un groupe AID ( ".$aid." ).</p>";
				}else{
					$tab_present_p = get_group($rep_verif_prof["id_groupe"]);
					echo "<p class=\"refus\">Ce professeur a d�j� cours avec les ".$tab_present_p["classlist_string"]." en ".$tab_present_p["description"]."</p><br />";
				}
			}

			// Si c'est bon, on enregistre le cours dans l'EdT
			if ($nbre_verif_prof === 0 AND $nbre_verif_s === 0) {
				$insert_edt = mysql_query("INSERT INTO edt_cours
				(id_cours, id_groupe, id_salle, jour_semaine, id_definie_periode, duree, heuredeb_dec, id_semaine, modif_edt, login_prof)
					VALUE ('', '".$enseignement."', '".$login_salle."', '".$ch_jour_semaine."', '".$ch_heure."', '".$duree."', '".$heure_debut."', '".$choix_semaine."', '0', '".$choix_prof."')") or die('Erreur dans l\'enregistrement, il faut recommencer !');

				// et on affiche les infos sur le cours enregistr�
					$contenu = "";
				if ($id_aid != "") {
					// c'est une AID et donc on r�cup�re les infos de cette AID
					$query1 = mysql_query("SELECT nom, indice_aid FROM aid WHERE id = '".$id_aid."'");
					$rep_aid = mysql_fetch_array($query1);
					$tab_infos["classlist_string"] = $rep_aid["nom"];
					// puis le nom de l'AID
					$rep_nom_aid = mysql_fetch_array(mysql_query("SELECT nom FROM aid_config WHERE indice_aid = '".$rep_aid["indice_aid"]."'"));
					$tab_infos["description"] = $rep_nom_aid["nom"];
					// $contenu est la liste des �l�ves
					$query = mysql_query("SELECT login FROM j_aid_eleves WHERE id_aid = '".$id_aid."'");
					$nbre = mysql_num_rows($query);
					for($a = 0; $a < $nbre; $a++){
						$nom[$a] = mysql_result($query, $a, "login");
						// On r�cup�re ses nom et pr�nom
						$query_n = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$nom[$a]."'"));
						$contenu .= $query_n["nom"].' '.$query_n["prenom"].'<br />';
					}

				}else {
					// C'est un enseignement de la table GROUPES
					$tab_infos = get_group($enseignement);

					foreach ($tab_infos["eleves"][1]["users"] as $eleve_login) {
						$contenu .= $eleve_login['nom']." ".$eleve_login['prenom']."<br />";
					}
				}

				$titre_listeleve = "Liste des �l�ves";

				$classe_js = "<a href=\"#\" onmouseover=\"afficher_div('nouveau_cours','Y',10,10);return false;\">Liste</a>
					".creer_div_infobulle("nouveau_cours", $titre_listeleve, "#330033", $contenu, "#FFFFFF", 15,0,"n","n","y","n");
				echo "<p>Ce cours est enregistr� :<font color=\"green\" size=\"1\">
					Les ".$tab_infos["classlist_string"]." en ".$tab_infos["description"]." avec ".$choix_prof." (".$classe_js.").</font></p>";
			}
		}
	}
}else {
//echo '
//		</fieldset>';
}
echo '</body></html>';
?>