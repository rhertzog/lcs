<?php

/**
 *
 * @version $Id: edt_cours.class.php 4869 2010-07-22 13:32:34Z jjocal $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

if (!$_SESSION["login"]) {
	DIE();
}

// ============================= classe php de construction des emplois du temps =================================== //

/**
 * la classe edt impl�mente tous les param�tres indispensables sur les informations utiles
 * � l'organisation des emplois du temps.
 */

class edt{

	public $id; // permet de pr�ciser l'id du cours en question
	public $edt_gr; // les �l�ves {enseignements AID edt_gr}
	public $type_grpe; // le type de groupe d'�l�ves {ENS AID EDT}
	public $id_grpe; // l'identifiant de l'id_groupe apr�s traitement par type_gr();
	public $edt_jour; // voir table horaires_etablissement
	public $edt_creneau; // voir table
	public $edt_debut; // 0 = d�but du cr�neau 0.5 = milieu d'un cr�neau
	public $edt_duree; //  en nbre de demis-cr�neaux
	public $edt_salle; // voir table salle_cours
	public $edt_semaine; // type de semaine comme d�fini dans la table edt_semaines
	public $edt_calend; // cours rattach� � une p�riode pr�cise d�finie dans le calendrier
	public $edt_modif; // pour savoir s'il s'agit d'un cours temporaire sur une semaine pr�cise =0 si ce n'est pas le cas.
	public $edt_prof; // qui est le professeur qui anime le cours (login)
	public $type; // permet de d�finir s'il s'agit d'un type {prof, eleve, classe, salle)

	public $sem = 0; // permet de r�cup�rer un num�ro de semaine autre que l'actuel $sem incr�mente ou d�cr�mente par rapport � la semaine actuelle

	public function __construct($id = NULL){

		if (isset($id) AND is_numeric($id)) {
			$this->id = $id;
		}else{
			$this->id = NULL;
		}
	}

	public function infos(){

		/**
		* Si le cours est connu, on peut afficher toutes ses caract�ristiques
		* On d�finit tous les attributs de l'objet
		*/

		$sql = "SELECT edt_cours.*, numero_salle FROM edt_cours, salle_cours
												WHERE id_cours = '".$this->id."'
												AND edt_cours.id_salle = salle_cours.id_salle
												LIMIT 1";
		$query = mysql_query($sql) OR trigger_error('Impossible de r�cup�rer les infos du cours.', E_USER_ERROR);
		$rep = mysql_fetch_array($query);

		// on charge les variables de classe
		$this->edt_gr = $rep["id_groupe"];
		$this->edt_aid = $rep["id_aid"];
		$this->edt_jour = $rep["jour_semaine"];
		$this->edt_creneau = $rep["id_definie_periode"];
		$this->edt_debut = $rep["heuredeb_dec"];
		$this->edt_duree = $rep["duree"];
		$this->edt_salle = $rep["numero_salle"];
		$this->edt_semaine = $rep["id_semaine"];
		$this->edt_calend = $rep["id_calendrier"];
		$this->edt_modif = $rep["modif_edt"];
		$this->edt_prof = $rep["login_prof"];
	}

	public function semaine_actu(){

		/**
		* On cherche � d�terminer � quel type de semaine se rattache la semaine actuelle
		* Il y a deux possibilit�s : soit l'�tablissement utilise les semaines classiques ISO soit il a d�fini
		* des num�ros sp�ciaux.
 		*/

		//
		$rep = array();

		$sem = date("W") + ($this->sem);

		$query_s = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE id_edt_semaine = '".$sem."' LIMIT 1");
		$rep["type"] = mysql_result($query_s, 0,"type_edt_semaine");

		$query_se = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE num_semaines_etab = '".$sem."' LIMIT 1");
		$compter = mysql_num_rows($query_se);
		if ($compter >= 1) {
			$rep["etab"] = mysql_result($query_se, 0, "type_edt_semaine");
		}
		$rep["etab"] = '';

		return $rep;
	}

	public function jours_de_la_semaine(){
		/**
		* Affiche les dates de la semaine demand�e
		* */
		return 'Il faut que j\'ajoute cette m�thode publique edt::jours_de_la_semaine() pour afficher les dates de la semaine vue ;)';
	}

	public function creneau($cren){
		// On cherche le cr�neau de d�but du cours
		$sql_c = "SELECT * FROM edt_creneaux WHERE type_creneau != 'pause' AND id_definie_periode = '".$cren."' LIMIT 1";
		$query_c = mysql_query($sql_c);
		$verif = mysql_num_rows($query_c);

		if ($verif >= 1) {
			$rep = mysql_result($query_c, 0,"heuredebut_definie_periode");
		}

		return $rep;
	}

	public function joursOuverts(){
		// Liste des jours ouverts
		$sql = "SELECT jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1 LIMIT 7";
		$query = mysql_query($sql);
		$retour = array();
		$i = 0;
		while($rep = mysql_fetch_array($query)){
			$retour[] = $rep["jour_horaire_etablissement"];
			$i++;
		}
		$retour["nbre"] = $i;

		return $retour;
	}

	public function debut(){
		// On cherche si ce cours commence au d�but ou au milieu d'un cours $this->edt_debut
		// n veut dire qu'il commence au milieu du cours et y veut dir qu'il commence au d�but du cours
		if ($this->edt_debut == '0.5') {
			$debut = 'n';
		}elseif($this->edt_debut == '0'){
			$debut = 'y';
		}

		return $debut;
	}

	public function duree(){
		// La dur�e doit �tre connu en nombre de demi-cr�neaux et en nombre de cr�neaux
		if (isset($this->edt_duree)) {
			$duree["demis"] = $this->edt_duree;
			$test_duree = $this->edt_duree / 2;
			if (is_int($test_duree)) {
				$duree["creneaux"] = $test_duree;
			}else{
				$duree["creneaux"] = $test_duree.'.5';
			}
		}

		return $duree;

	}

	public function calend(){
		// Pour tout savoir sur la p�riode du cours si = 0, pas de p�riode rattach�e
		if (isset($this->edt_calend)) {
			$calend = $this->edt_calend;
			if ($calend != 0) {
				// On recherche les infos sur la p�riode existante
				$query = mysql_query("SELECT nom_calendrier, debut_calendrier_ts, fin_calendrier_ts
															FROM edt_calendrier
															WHERE id_calendrier = '".$calend."'");
				$retour = mysql_fetch_array($query);
			}else{
				$retour = 'n';
			}
		}else{
			return 'erreur';
		}
		return $retour;
	}

	public function prof(){
		// Pour savoir qui est le professeur qui anime le cours $this->edt_prof;
		if (isset($this->edt_prof)) {
			$sql = "SELECT nom, prenom, civilite FROM utilisateurs WHERE login = '".$this->edt_prof."'";
			$query = mysql_query($sql);
			$retour = mysql_fetch_array($query);
			//$retour = $this->edt_prof;
		}else{
			return 'erreur';
		}
		return $retour;
	}

	public function matiere(){
		$matiere = NULL;
		if ($this->edt_gr != NULL) {
			// C'est donc un 'groupe'
			$sql = "SELECT * FROM groupes WHERE id = '".$this->edt_gr."'";
			$query = mysql_query($sql);

			$matiere = mysql_fetch_array($query);

		}elseif($this->edt_aid != NULL){
			// C'est donc une AID
		}else{
			return 'erreur_M';
		}
		return $matiere;
	}

	public function eleves(){
		$eleves = NULL; // en attendant d'impl�menter cette m�thode
		// pour connaitre la liste des �l�ves concern�s par ce cours
		if ($this->edt_gr != NULL) {
			// C'est donc un 'groupe'
			// $sql = "SELECT .....";
			// A TERMINER ICI
		}
		return $eleves;
	}

	public function couleur_cours(){
		// On r�cup�re la mati�re
		//$test_grp = $this->type_gr($this->edt_gr);
		$sql = '';
		if ($this->edt_gr != NULL) {
			// on peut alors r�cup�rer la mati�re et la couleur rattach�es � cette mati�re
			$sql = "SELECT matiere FROM matieres m, j_groupes_matieres jgm
									WHERE jgm.id_matiere = m.matiere
									AND jgm.id_groupe = '".$this->edt_gr."' LIMIT 1";

		}
		if ($sql != '') {
			$query = mysql_query($sql);
			$matiere = mysql_fetch_array($query);

			// on cherche la couleur rattach�e
			$sql2 = "SELECT valeur FROM edt_setting WHERE reglage = 'M_".$matiere["matiere"]."' LIMIT 1";
			$query2 = mysql_query($sql2);
			$verif = mysql_num_rows($query2);

			if ($verif == 1) {
				$couleur = mysql_result($query2, 0,"valeur");
			}else{
				$couleur = 'silver';
			}

		}else{
			$couleur = '';
		}

		return $couleur;
	}

}

/**
 * classe de mise en page de l'edt
 *
 */
class edtAfficher{

	public $largeur_creneau = 90;
	public $largeur_jour = 60;
	public $hauteur_entete = 60;
	public $hauteur_creneau = 100;
	public $aff_jour = 'gauche'; // peut �tre modifi�e pour enlever le jour � gauche du div
	public $type_edt = 'prof'; // peut �tre modifi�e pour un �l�ve
	/**
	 * Constructor
	 * @access protected
	 */
	function __construct(){
		// vide pour le moment
	}

	public function liste_creneaux(){
		// Renvoie la liste des cr�neaux d'une journ�e
		$sql = "SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode FROM edt_creneaux
							WHERE type_creneaux != 'pause'
							AND type_creneaux != 'repas'
							ORDER BY heuredebut_definie_periode";

		$query = mysql_query($sql);
		$rep["nbre"] = mysql_num_rows($query);

		if ($query AND $rep["nbre"] > 0) {

			for($a = 0 ; $a < $rep["nbre"] ; $a++){
				$rep[$a]["id"] = mysql_result($query, $a, "id_definie_periode");
				$rep[$a]["nom"] = mysql_result($query, $a, "nom_definie_periode");
				$rep[$a]["horaire"] = substr(mysql_result($query, $a, "heuredebut_definie_periode"), 0, 5);
			}

		}
		return $rep;
	}

	public function entete_creneaux($reglage = NULL){

		$rep = '';

		$liste_creneaux = $this->liste_creneaux();

		$rep .= "\n".'
			<div style="width: '.(($this->largeur_creneau * $liste_creneaux["nbre"]) + $this->largeur_jour).'px; height: '.$this->hauteur_entete.'px; border-top: 2px dotted silver;">
				<div class="creneau prem" style="width: '.$this->largeur_jour.'px;">&nbsp;</div>';

		for($a = 0 ; $a < $liste_creneaux["nbre"] ; $a++){

			if ($reglage == 'heures') {
				$cren = $liste_creneaux[$a]["horaire"];
			}elseif($reglage == 'noms'){
				$cren = $liste_creneaux[$a]["nom"];
			}else{
				// Par d�faut, le r�glage est 'heures'
				$cren = $liste_creneaux[$a]["horaire"];
			}

			if ($a < ($liste_creneaux["nbre"] - 1)) {
				$class = 'creneau';
			}else{
				$class = 'creneau_d';
			}

			if ($a == 0) {
				$margin_left = $this->largeur_jour;
			}else{
				$margin_left = ($this->largeur_creneau * ($a + 1)) - ($this->largeur_creneau - $this->largeur_jour);
			}

			$rep .= "\n".'<div class="'.$class.'" style="margin-left: '.$margin_left.'px; width: '.$this->largeur_creneau.'px;">'.$cren.'</div>';

		}

		$rep .= "\n".'</div>';

		$entete = $rep;

		return $entete;
	}

	public function afficher_cours_jour($jour, $prof){

		$retour = '';

		$liste_cours = $this->edt_jour($jour, $prof);
		// petite verif sur le contenu
		if (!is_array($liste_cours) AND substr($liste_cours, 0, 7) == 'Ce_mode') {
			return $liste_cours;
			exit;
		}

		$liste_creneaux = $this->liste_creneaux();

		if ($this->aff_jour == 'gauche') {

			$largeur = $this->largeur_creneau + 1;
			$aff_jour_gauche = 'oui';

		}elseif($this->aff_jour == 'cache'){

			$largeur = $this->largeur_creneau;
			$aff_jour_gauche = 'non';

		}else{
			echo 'le mode '.$this->aff_jour.' n\'est pas impl�ment� => ERREUR.';
			echo '<br />Vous avez le choix entre "gauche" et "cache" (par d�faut, c\'est gauche).';
			exit();
		}

		$retour .= '<div style="width: '.($largeur) * $liste_creneaux["nbre"].'px; height: '.$this->hauteur_creneau.'px; border-bottom: 2px dotted silver;">';

		if ($aff_jour_gauche == 'oui') {
			$retour .= '<div style="width: '.$this->largeur_jour.'px; height: '.($this->hauteur_creneau - 1).'px; font-size: 12px; text-align: center; border-right: 2px solid grey; position: absolute;"><br />
			'.$jour.'</div>';
		}

		for($a = 0 ; $a < $liste_cours["nbre"] ; $a++){

			$cours = new edt($liste_cours[$a]["id_cours"]);
			$cours_i = $cours->infos();

			$ou = $this->placer_cours($cours);

			$retour .= '
			<div class="affedtcours" style="'.$ou["margin"].' '.$ou["width"].' height: '.($this->hauteur_creneau - 1).'px; background: '.$cours->couleur_cours().';">';

			$retour .= $this->contenu_cours($cours);

			$retour .= '</div>
			';
		}
		$retour .=('</div>'."\n");

		return $retour;
	}

	protected function ordre_creneau(edt $cours){

		$creneaux = $this->liste_creneaux();
		// On cherche le num�ro du cr�neau en question (premier, deuxi�mre, troisi�me, ...)
		$test = 'n';
		for($o = 0 ; $o < $creneaux['nbre'] ; $o++){
			if ($creneaux[$o]["id"] == $cours->edt_creneau) {
				$test = $o + 1;
			}
		}
		return $test;
	}

	protected function placer_cours(edt $cours){

		// $cours doit �tre une instance de la classe edt... Il faudra peut-�tre v�rifier cela
		$test = $this->ordre_creneau($cours);

		if ($test == 1) {
			$rep["margin"] = 'margin-left: '.$this->largeur_jour.'px;';
		}else{

			if ($cours->edt_debut == '0') {
				$rep["margin"] = 'margin-left: '.(((($test - 1) * $this->largeur_creneau) + $this->largeur_creneau) - ($this->largeur_creneau - $this->largeur_jour)).'px;';
			}elseif($cours->edt_debut == '0.5'){
				// C'est le m�me calcul que sur le pr�c�dent mais on y ajoute un demi-cr�neau
				$rep["margin"] = 'margin-left: '.(((($test - 1) * $this->largeur_creneau) + $this->largeur_creneau) - ($this->largeur_creneau - $this->largeur_jour) + ($this->largeur_creneau / 2)).'px;';
			}else{
				$rep["margin"] = 'Il manque une info.';
			}

		}

		$rep["width"] = 'width: '.(($cours->edt_duree * ($this->largeur_creneau / 2)) - 1).'px;';

		return $rep;
	}

	protected function edt_jour($jour, $user_login){
		/**
		* m�thode qui renvoie l'edt d'un prof
		* sur un jour donn� Il faudra ajouter les aid
		*
		*/

		$rep = array();
		$sem = edt::semaine_actu();

		if ($this->type_edt == 'prof') {

			$sql = "SELECT id_cours FROM edt_cours WHERE
								login_prof = '".$user_login."'
								AND jour_semaine = '".$jour."'
								AND (id_semaine = '".$sem["type"]."' OR id_semaine = '0')
							ORDER BY id_definie_periode";

		}elseif($this->type_edt == 'eleve'){

			$sql = "SELECT id_cours FROM edt_cours, j_eleves_groupes WHERE
								edt_cours.jour_semaine = '".$jour."'
								AND edt_cours.id_groupe = j_eleves_groupes.id_groupe
								AND login = '".$user_login."'
								AND (id_semaine = '".$sem["type"]."' OR id_semaine = '0')
							ORDER BY edt_cours.id_semaine";

		}elseif($this->type_edt == 'classe'){

			$sql = "SELECT * FROM edt_cours, j_groupes_classes, classes WHERE
								edt_cours.jour_semaine = '".$jour."'
								AND edt_cours.id_groupe = j_groupes_classes.id_groupe
								AND j_groupes_classes.id_classe = classes.id
								AND classes.classe = '".$user_login."'
								AND (id_semaine = '".$sem["type"]."' OR id_semaine = '0')
							ORDER BY edt_cours.id_groupe";

		}else{
			return 'Ce_mode '.$this->type_edt.' n\'est pas encore disponible';
		}

		$query = mysql_query($sql);
		$rep["nbre"] = mysql_num_rows($query);

		for($a = 0 ; $a < $rep["nbre"] ; $a++){
			$reponse = mysql_fetch_array($query);

			$rep[$a]["id_cours"] = $reponse["id_cours"];

		}

		return $rep;
	}

	protected function contenu_cours(edt $cours){
		/**
		* m�thode qui g�re l'affichage d'un cours dans le div
		*
		*/
		// $cours doit �tre une instance de la classe edt... Il faudra peut-�tre v�rifier cela
		// Le professeur
		$contenu = '';

		$prof = $cours->prof();
		$matiere = $cours->matiere();

		$contenu .= '<p style="text-align: center;">'.
			$prof["civilite"].$prof["nom"].' '.substr($prof["prenom"], 0, 1).'.<br />'.
			$matiere["name"].'<br /><i>salle&nbsp;'.$cours->edt_salle.'</i>
			</p>';

		return $contenu;
	}

	public function aujourdhui(){
		/**
		* m�thode qui donne le jour d'aujourd'hui en toute lettre et en Fran�ais
		*
		*/
		$jour_num = date("N") - 1;

		$jours_semaine = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');

		return $jours_semaine[$jour_num];
	}

}