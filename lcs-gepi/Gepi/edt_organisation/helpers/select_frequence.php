<?php

/**
 *
 *
 * @version $Id: select_frequence.php 1593 2008-03-06 23:01:30Z jjocal $
 * @copyright 2008
 *
 * Fichier qui renvoie un select des types de semaine ainsiq ue des diff�rentes p�riodes du calendier de l'�tablissement
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
	if ($analyse[4] == "select_frequence.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_semaines_periodes";
$id_select = isset($nom_id_select) ? ' id="'.$nom_id_select.'"' : NULL;
$test_selected = isset($nom_selected) ? $nom_selected : NULL;

echo '
	<select name ="'.$increment.'"'.$id_select.'>
		<option value="aucun">Liste des types de semaine et des p�riodes du calendrier</option>';

// On r�cup�re les diff�rents type de semaine
$query = mysql_query("SELECT DISTINCT type_edt_semaine FROM edt_semaines ORDER BY type_edt_semaine LIMIT 5")
			OR error_reporting('Erreur dans la requ�te : '.mysql_error());

while($type_semaine = mysql_fetch_array($query)){

	echo '
	<option value="'.$type_semaine["type_edt_semaine"].'">Semaine '.$type_semaine["type_edt_semaine"].'</option>';
}

// On r�cup�re les diff�rentes p�riodes du calendrier
$query = mysql_query("SELECT id_calendrier, nom_calendrier FROM edt_calendrier WHERE numero_periode = '0' AND etabvacances_calendrier = '0'")
			OR error_reporting('Erreur dans la requ�te (p�riodes) : '.mysql_error());

while($periodes = mysql_fetch_array($query)){

	echo '
	<option value="'.$periodes["id_calendrier"].'">'.$periodes["nom_calendrier"].'</option>';
}
echo '
	</select>';

?>