<?php

/**
 *
 *
 * @version $Id: select_jours.php 1807 2008-05-09 15:26:10Z jjocal $
 * @copyright 2008
 *
 * Fichier qui renvoie un select des jours ouvr�s de l'�tablissement
 * pour l'int�grer dans un fomulaire
 *
 */

// On r�cup�re les infos utiles pour le fonctionnement des requ�tes sql
$niveau_arbo = 1;
require_once("../lib/initialisations.inc.php");

// S�curit� : �viter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
$analyse[4] = isset($analyse[4]) ? $analyse[4] : NULL;
	if ($analyse[4] == "select_jours.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_jours"; // name du select
$id_select = isset($nom_id_select) ? (' id="'.$nom_id_select.'"') : NULL; // id du select
if (!isset($nom_selected)) {
	$nom_selected = isset($nom_jour) ? $nom_jour : NULL; // permet de d�finir le selected
}

echo '
	<select name="'.$increment.'"'.$id_select.'>
		<option value="aucun">Liste des jours</option>
';
// On appele la liste des cr�neaux
$query = mysql_query("SELECT * FROM horaires_etablissement LIMIT 0, 7")
			OR error_reporting('Erreur dans la recherche des jours : '.mysql_error());

while($jours = mysql_fetch_array($query)){
	// le selected
	if (strtoupper($jours["jour_horaire_etablissement"]) == $nom_selected) {
		$selected = ' selected="selected"';
	}else{
		$selected = '';
	}
	echo '
		<option value="'.$jours["id_horaire_etablissement"].'"'.$selected.'>'.ucfirst($jours["jour_horaire_etablissement"]).'</option>';
}

echo '
	</select>
';

?>