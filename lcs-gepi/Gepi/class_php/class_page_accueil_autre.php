<?php
/* $Id: class_page_accueil_autre.php 4934 2010-07-28 20:57:14Z regis $
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

class class_page_accueil_autre {


  public $titre_Menu=array();
  public $menu_item=array();
  public $canal_rss=array();
  public $message_admin=array();
  public $nom_connecte=array();
  public $referencement=array();
  public $message=array();
  public $probleme_dir=array();
  public $canal_rss_flux="";
  public $gere_connect="";
  public $alert_sums="";
  public $signalement="";
  public $nb_connect="";
  public $nb_connect_lien="";

  protected $ordre_menus=array();
  protected $cheminRelatif="";
  protected $loginUtilisateur="";
  public $statutUtilisateur="";
  protected $gepiSettings="";
  protected $test_prof_matiere="";
  protected $test_prof_suivi="";
  protected $test_prof_ects="";
  protected $test_scol_ects="";
  protected $test_prof_suivi_ects="";
  protected $test_https="";
  protected $a=0;
  protected $b=0;

/**
 * Construit les entr�es de la page d'accueil
 *
 * @author regis
 */
  function __construct($gepiSettings, $niveau_arbo,$ordre_menus) {


	switch ($niveau_arbo){
	  case 0:
		$this->cheminRelatif = './';
		break;
	  case 1:
		$this->cheminRelatif = '../';
		break;
	  case 2:
		$this->cheminRelatif = '../../';
		break;
	  case 3:
		$this->cheminRelatif = '../../../';
		break;
	  default:
		$this->cheminRelatif = './';
	}

	$this->statutUtilisateur = "autre";
	$this->gepiSettings=$gepiSettings;
	$this->loginUtilisateur=$_SESSION['login'];

	$this->chargeOrdreMenu($ordre_menus);

/***** Outils de gestion des absences vie scolaire *****/
	$this->verif_exist_ordre_menu('bloc_absences_vie_scol');
	if ($this->absences_vie_scol())
	$this->chargeAutreNom('bloc_absences_vie_scol');

/***** Cahier de texte CPE ***********/
	$this->verif_exist_ordre_menu('bloc_saisie');
	if ($this->cahierTexte()){
	  $this->chargeAutreNom('bloc_saisie');
	}

/***** Outils de relev� de notes *****/
	$this->verif_exist_ordre_menu('bloc_releve_notes');
	if ($this->releve_notes())
	$this->chargeAutreNom('bloc_releve_notes');

/***** Emploi du temps *****/
	$this->verif_exist_ordre_menu('bloc_emploi_du_temps');
	if ($this->emploiDuTemps())
	$this->chargeAutreNom('bloc_emploi_du_temps');

/***** gestion des trombinoscopes : module de Christian Chapel ***********/
	$this->verif_exist_ordre_menu('bloc_trombinoscope');
	if ($this->trombinoscope())
	$this->chargeAutreNom('bloc_trombinoscope');

/***** Visualisation / Impression *****/
	$this->verif_exist_ordre_menu('bloc_visulation_impression');
	if ($this->impression())
	$this->chargeAutreNom('bloc_visulation_impression');

/***** Outils de relev� ECTS *****/
	$this->verif_exist_ordre_menu('bloc_releve_ects');
	if ($this->releve_ECTS())
	$this->chargeAutreNom('bloc_releve_ects');

/***** Outils compl�mentaires de gestion des AID *****/
	$this->verif_exist_ordre_menu('bloc_outil_comp_gestion_aid');
	if ($this->gestionAID())
	$this->chargeAutreNom('bloc_outil_comp_gestion_aid');

/***** Outils de gestion des Bulletins scolaires *****/
	$this->verif_exist_ordre_menu('bloc_gestion_bulletins_scolaires');
	if ($this->bulletins())
	$this->chargeAutreNom('bloc_gestion_bulletins_scolaires');

/***** Gestion Notanet *****/
	$this->verif_exist_ordre_menu('bloc_notanet_fiches_brevet');
	if ($this->notanet())
	$this->chargeAutreNom('bloc_notanet_fiches_brevet');

/***** Gestion ann�es ant�rieures *****/
	$this->verif_exist_ordre_menu('bloc_annees_ant�rieures');
	if ($this->anneeAnterieure())
	$this->chargeAutreNom('bloc_annees_ant�rieures');

/***** Gestion des messages *****/
	$this->verif_exist_ordre_menu('bloc_panneau_affichage');
	if ($this->messages())
	$this->chargeAutreNom('bloc_panneau_affichage');

/***** Module inscription *****/
	$this->verif_exist_ordre_menu('bloc_module_inscriptions');
	if ($this->inscription())
	$this->chargeAutreNom('bloc_module_inscriptions');

/***** Module discipline *****/
	$this->verif_exist_ordre_menu('bloc_module_discipline');
	if ($this->discipline())
	$this->chargeAutreNom('bloc_module_discipline');

/***** Module plugins : affichage des menus des plugins en fonction des droits *****/
	$this->verif_exist_ordre_menu('');
	$this->plugins();

/***** Module Genese des classes *****/
	$this->verif_exist_ordre_menu('bloc_Genese_classes');
	if ($this->geneseClasses())
	$this->chargeAutreNom('bloc_Genese_classes');

/***** Module Epreuves blanches *****/
	$this->verif_exist_ordre_menu('bloc_epreuve_blanche');
	if ($this->epreuvesBlanches())
	$this->chargeAutreNom('bloc_epreuve_blanche');

/***** Module Admissions Post-Bac *****/
	$this->verif_exist_ordre_menu('bloc_admissions_post_bac');
	if ($this->adminPostBac())
	$this->chargeAutreNom('bloc_admissions_post_bac');

/***** Module Gestionnaire d'AID *****/
	$this->verif_exist_ordre_menu('bloc_Gestionnaire_aid');
	if ($this->gestionEleveAID())
	$this->chargeAutreNom('bloc_Gestionnaire_aid');



/***** Tri des menus *****/
  sort($this->titre_Menu);
  }

  private function itemPermis($chemin){
	$sql="SELECT ds.autorisation FROM `droits_speciaux` ds,  `droits_utilisateurs` du
				WHERE (ds.nom_fichier='".$chemin."'
				  AND ds.id_statut=du.id_statut
				  AND du.login_user='".$this->loginUtilisateur."');" ;
	$result=mysql_query($sql);
	if (!$result) {
	  return TRUE;
	} else {
	  $row = mysql_fetch_row($result) ;
	  if ($row[0]=='V' || $row[0]=='v'){
		return TRUE;
	  } else {
		return FALSE;
	  }
	}
  }

  protected function creeNouveauTitre($classe,$texte,$icone,$titre="",$alt=""){
	$this->titre_Menu[$this->a]=new menuGeneral();
	$this->titre_Menu[$this->a]->indexMenu=$this->a;
	$this->titre_Menu[$this->a]->classe=$classe;
	$this->titre_Menu[$this->a]->texte=$texte;
	$this->titre_Menu[$this->a]->icone['chemin']=$this->cheminRelatif.$icone;
	$this->titre_Menu[$this->a]->icone['titre']=$titre;
	$this->titre_Menu[$this->a]->icone['alt']=$alt;
  }

  protected function creeNouveauItem($chemin,$titre,$expli){
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin=$chemin;
	if ($this->itemPermis($nouveauItem->chemin))
	{
	  $nouveauItem->indexMenu=$this->a;
	  $nouveauItem->titre=$titre;
	  $nouveauItem->expli=$expli;
	  $nouveauItem->indexItem=$this->b;
	  $this->menu_item[]=$nouveauItem;
	  $this->b++;
	}
	unset($nouveauItem);
  }

  protected function creeNouveauItemPlugin($chemin,$titre,$expli){
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin=$chemin;
	$nouveauItem->indexMenu=$this->a;
	$nouveauItem->titre=$titre;
	$nouveauItem->expli=$expli;
	$nouveauItem->indexItem=$this->b;
	$this->menu_item[]=$nouveauItem;
	$this->b++;
	unset($nouveauItem);
  }

  protected function chargeOrdreMenu($ordre_menus){
	//$this->ordre_menus=$ordre_menus;
	$sql="SHOW TABLES LIKE 'mn_ordre_accueil'";
	$resp = mysql_query($sql);

	if(mysql_num_rows($resp)>0) {
	  $sql2="SELECT bloc, num_menu
			FROM mn_ordre_accueil
			WHERE statut
			LIKE '$this->statutUtilisateur' " ;
	  $resp2 = mysql_query($sql2);
	  if (mysql_num_rows($resp2)>0){
		while($lig_log=mysql_fetch_object($resp2)) {
		  $this->ordre_menus[$lig_log->bloc]=$lig_log->num_menu;
		}
	  }else{
		$this->ordre_menus=$ordre_menus;
	  }
	}else{
	  $this->ordre_menus=$ordre_menus;
	}
  }

  protected function verif_exist_ordre_menu($_item){
	if (!isset($this->ordre_menus[$_item]))
	  $this->ordre_menus[$_item] = max($this->ordre_menus)+1;
	  $this->a=$this->ordre_menus[$_item];
  }

  private function chargeAutreNom($bloc){
	$sql1="SHOW TABLES LIKE 'mn_ordre_accueil'";
	$resp1 = mysql_query($sql1);
	if(mysql_num_rows($resp1)>0) {
	  $sql="SELECT nouveau_nom FROM mn_ordre_accueil
			WHERE bloc LIKE '$bloc'
			AND statut LIKE 'autre'
			AND nouveau_nom NOT LIKE ''
			;";
	  $resp=mysql_query($sql);
	  if (mysql_num_rows($resp)>0){
		$this->titre_Menu[$this->a]->texte=mysql_fetch_object($resp)->nouveau_nom;
	  }
	}
  }
  
  protected function absences_vie_scol() {
	if (getSettingValue("active_module_absence")) {
	  $this->b=0;
	  $nouveauItem = new itemGeneral();
	  if (getSettingValue("active_module_absence")=='y' ) {
	  $this->creeNouveauItem('/mod_absences/gestion/gestion_absences.php',
			  "Gestion Absences, dispenses, retards et infirmeries",
			  "Cet outil vous permet de g�rer les absences, dispenses, retards et autres bobos � l'infirmerie des ".$this->gepiSettings['denomination_eleves'].".");
	  $this->creeNouveauItem('/mod_absences/gestion/voir_absences_viescolaire.php',
			  "Visualiser les absences",
			  "Vous pouvez visualiser cr�neau par cr�neau la saisie des absences.");
		$this->creeNouveauItem("/mod_absences/professeurs/prof_ajout_abs.php",
				"Gestion des Absences",
				"Cet outil vous permet de g�rer les absences des �l�ves");
	  } else if (getSettingValue("active_module_absence")=='2' ) {
		$this->creeNouveauItem("/mod_abs2/index.php",
				"Gestion des Absences",
				"Cet outil vous permet de g�rer les absences des �l�ves");
	  }
	  if ($this->b>0){
		$this->creeNouveauTitre('accueil',"Gestion des retards et absences",'images/icons/absences.png');
		return true;
	  }
    }
  }

  private function cahierTexte(){
	$this->b=0;
	if (getSettingValue("active_cahiers_texte")=='y') {
	  $this->creeNouveauItem("/cahier_texte/see_all.php",
			  "Cahier de textes",
			  "Permet de consulter les compte-rendus de s�ance et les devoirs � faire pour les enseignements de tous les ".$this->gepiSettings['denomination_eleves']);
	  $this->creeNouveauItem("/cahier_texte_admin/visa_ct.php",
			  "Visa des cahiers de textes",
			  "Permet de viser les cahiers de textes" );
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Cahier de texte",'images/icons/document.png');
	  return true;
	}
  }

  private function releve_notes(){
	$this->b=0;
	if (getSettingValue("active_carnets_notes")=='y') {
	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes_2.php",
			  "Visualisation et impression des relev�s de notes",
			  "Cet outil vous permet de visualiser � l'�cran et d'imprimer les relev�s de notes,
				".$this->gepiSettings['denomination_eleve']." par ".$this->gepiSettings['denomination_eleve'].",
				  classe par classe.");

	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes.php",
			  "Visualisation et impression des relev�s de notes",
			  "Cet outil vous permet de visualiser � l'�cran et d'imprimer les relev�s de notes,
				".$this->gepiSettings['denomination_eleve']." par ".$this->gepiSettings['denomination_eleve'].",
				  classe par classe.");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Relev�s de notes",'images/icons/document.png');
	  return true;
	}
  }
 
  private function emploiDuTemps(){
	$this->b=0;
    $this->creeNouveauItem("/edt_organisation/index_edt.php",
			"Emploi du temps",
			"Cet outil permet la consultation/gestion de l'emploi du temps.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Emploi du temps",'images/icons/document.png');
	  return true;
	}
  }  private function trombinoscope(){
	//On v�rifie si le module est activ�

	$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");
	$active_module_trombino_pers=getSettingValue("active_module_trombino_pers");

	$this->b=0;


	if (($active_module_trombinoscopes=='y')
			||($active_module_trombino_pers=='y')) {

	  $this->creeNouveauItem("/mod_trombinoscopes/trombinoscopes.php",
			  "Trombinoscopes",
			  "Cet outil vous permet de visualiser les trombinoscopes des classes.");

	  // On appelle les aid "trombinoscope"
	  $call_data = mysql_query("SELECT * FROM aid_config
								WHERE indice_aid= '".getSettingValue("num_aid_trombinoscopes")."'
								ORDER BY nom");
	  $nb_aid = mysql_num_rows($call_data);
	  $i=0;
	  while ($i < $nb_aid) {
		$indice_aid = @mysql_result($call_data, $i, "indice_aid");
		$call_prof = mysql_query("SELECT * FROM j_aid_utilisateurs_gest
								  WHERE (id_utilisateur = '" . $this->loginUtilisateur . "'
								  AND indice_aid = '$indice_aid')");
		$nb_result = mysql_num_rows($call_prof);
		if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
		  $nom_aid = @mysql_result($call_data, $i, "nom");
		  $this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "Cet outil vous permet de visualiser quels ".$this->gepiSettings['denomination_eleves']." ont le droit d'envoyer/modifier leur photo.");
		}
		$i++;
	  }
	}

	  if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Trombinoscope",'images/icons/trombinoscope.png');
		return true;
	  }
  }









  private function impression(){
	$this->b=0;

	$this->creeNouveauItem("/groupes/visu_profs_class.php",
			"Visualisation des �quipes p�dagogiques",
			"Ceci vous permet de conna�tre tous les ".$this->gepiSettings['denomination_professeurs']." des classes dans lesquelles vous intervenez, ainsi que les compositions des groupes concern�s.");
/*
	$this->creeNouveauItem("/eleves/liste_eleves.php",
			"Visualisation des �quipes p�dagogiques",
			"Ceci vous permet de conna�tre tous les ".$this->gepiSettings['denomination_professeurs']." des classes dans lesquelles vous intervenez, ainsi que les compositions des groupes concern�s.");
*/
	$this->creeNouveauItem("/eleves/visu_eleve.php",
			"Consultation d'un ".$this->gepiSettings['denomination_eleve'],
			"Ce menu vous permet de consulter dans une m�me page les informations concernant un ".$this->gepiSettings['denomination_eleve']." (enseignements suivis, bulletins, relev�s de notes, ".$this->gepiSettings['denomination_responsables'].",...). Certains �l�ments peuvent n'�tre accessibles que pour certaines cat�gories de visiteurs.");

	$this->creeNouveauItem("/impression/impression_serie.php",
			"Impression PDF de listes",
			"Ceci vous permet d'imprimer en PDF des listes avec les ".$this->gepiSettings['denomination_eleves'].", � l'unit� ou en s�rie. L'apparence des listes est param�trable.");

	  $this->creeNouveauItem("/groupes/mes_listes.php",
			  "Exporter mes listes",
			  "Ce menu permet de t�l�charger ses listes avec tous les ".$this->gepiSettings['denomination_eleves']." au format CSV avec les champs CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS.");

	$this->creeNouveauItem("/visualisation/index.php",
			"Outils graphiques de visualisation",
			"Visualisation graphique des r�sultats des ".$this->gepiSettings['denomination_eleves']." ou des classes, en croisant les donn�es de multiples mani�res.");
	$this->creeNouveauItem("/prepa_conseil/index1.php",
			"Visualiser mes moyennes et appr�ciations des bulletins",
			"Tableau r�capitulatif de vos moyennes et/ou appr�ciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.");
	$this->creeNouveauItem("/prepa_conseil/index1.php",
				"Visualiser les moyennes et appr�ciations des bulletins",
				"Tableau r�capitulatif des moyennes et/ou appr�ciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.");

	$this->creeNouveauItem("/prepa_conseil/index2.php",
			"Visualiser toutes les moyennes d'une classe",
			"Tableau r�capitulatif des moyennes d'une classe.");

	$this->creeNouveauItem("/prepa_conseil/index3.php",
			"Visualiser les bulletins simplifi�s",
			"Bulletins simplifi�s d'une classe.");
  	$call_data = mysql_query("SELECT * FROM aid_config 
					WHERE display_bulletin = 'y' 
					OR bull_simplifie = 'y' 
					ORDER BY nom");
	$nb_aid = mysql_num_rows($call_data);
	$i=0;
	while ($i < $nb_aid) {
	  $indice_aid = @mysql_result($call_data, $i, "indice_aid");
	  $call_prof = mysql_query("SELECT * FROM j_aid_utilisateurs 
								WHERE (id_utilisateur = '".$this->loginUtilisateur."'
								AND indice_aid = '".$indice_aid."')");
	  $nb_result = mysql_num_rows($call_prof);
	  if ($nb_result != 0) {
		$nom_aid = @mysql_result($call_data, $i, "nom");	 
		$this->creeNouveauItem("/prepa_conseil/visu_aid.php?indice_aid=".$indice_aid,
				"Visualiser des appr�ciations ".$nom_aid,
				"Cet outil permet la visualisation et l'impression des appr�ciations des ".$this->gepiSettings['denomination_eleves']." pour les ".$nom_aid.".");
	  }
	  $i++;
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Visualisation - Impression",'images/icons/print.png');
	  return true;
	}
  }
  
  protected function releve_ECTS(){
	$this->b=0;

	$chemin = array();
	$this->creeNouveauItem("/mod_ects/edition.php",
			  "G�n�ration des documents ECTS",
			  "Cet outil vous permet de g�n�rer les documents ECTS (relev�, attestation, annexe)
				pour les classes concern�es.");

	  $this->creeNouveauItem("/mod_ects/recapitulatif.php",
			  "Visualiser tous les ECTS",
			  "Visualiser les tableaux r�capitulatif par classe de tous les cr�dits ECTS.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Documents ECTS",'images/icons/releve.png');
	  return true;
	}
  }

  protected function gestionAID(){
	$this->b=0;

	$call_data = sql_query("SELECT distinct ac.indice_aid, ac.nom
		  FROM aid_config ac, aid a
		  WHERE ac.outils_complementaires = 'y'
		  AND a.indice_aid=ac.indice_aid
		  ORDER BY ac.nom_complet");
	$nb_aid = mysql_num_rows($call_data);

	$call_data2 = sql_query("SELECT id
		  FROM archivage_types_aid
		  WHERE outils_complementaires = 'y'");
	$nb_aid_annees_anterieures = mysql_num_rows($call_data2);
	$nb_total=$nb_aid+$nb_aid_annees_anterieures;

	if ($nb_total != 0) {
	  $i = 0;
	  while ($i<$nb_aid) {
		$indice_aid = mysql_result($call_data,$i,"indice_aid");
		$nom_aid = mysql_result($call_data,$i,"nom");
		if ($this->AfficheAid($indice_aid)) {
		  $this->creeNouveauItem("/aid/index_fiches.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "Tableau r�capitulatif, liste des ".$this->gepiSettings['denomination_eleves'].", ...");
		}
		$i++;
	  }
	  if (($nb_aid_annees_anterieures > 0)) {
		$this->creeNouveauItem("/aid/annees_anterieures_accueil.php",
				"Fiches projets des ann�es ant�rieures",
				"Acc�s aux fiches projets des ann�es ant�rieures");
	  }
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',
			  "Outils de visualisation et d'�dition des fiches projets",
			  'images/icons/document.png');
	  return true;
	}
  }
  
  private function AfficheAid($indice_aid){
    if ($this->statutUtilisateur == "eleve") {
        $test = sql_query1("SELECT count(login) FROM j_aid_eleves
				  WHERE login='".$this->loginUtilisateur."'
				  AND indice_aid='".$indice_aid."' ");
        if ($test == 0)
            return false;
        else
            return true;
    } else
        return true;
  }
  
  protected function bulletins(){
	$this->b=0;

	$this->creeNouveauItem("/bulletin/verif_bulletins.php",
			  "Outil de v�rification",
			  "Permet de v�rifier si toutes les rubriques des bulletins sont remplies.");
	$this->creeNouveauItem("/bulletin/verrouillage.php",
			  "Verrouillage/D�verrouillage des p�riodes",
			  "Permet de verrouiller ou d�verrouiller une p�riode pour une ou plusieurs classes.");
	$this->creeNouveauItem("/classes/acces_appreciations.php",
			  "Acc�s des ".$this->gepiSettings['denomination_eleves']." et ".$this->gepiSettings['denomination_responsables']." aux appr�ciations",
			  "Permet de d�finir quand les comptes ".$this->gepiSettings['denomination_eleves']." et ".$this->gepiSettings['denomination_responsables']."
			  (s'ils existent) peuvent acc�der aux appr�ciations des ".$this->gepiSettings['denomination_professeurs']."
				sur le bulletin et avis du conseil de classe.");
	$this->creeNouveauItem("/bulletin/param_bull.php",
			  "Param�tres d'impression des bulletins",
			  "Permet de modifier les param�tres de mise en page et d'impression des bulletins.");
	$this->creeNouveauItem("/responsables/index.php",
			  "Gestion des fiches ".$this->gepiSettings['denomination_responsables'],
			  "Cet outil vous permet de modifier/supprimer/ajouter des fiches
			  de ".$this->gepiSettings['denomination_responsables']." des ".$this->gepiSettings['denomination_eleves'].".");
	$this->creeNouveauItem("/eleves/index.php",
			  "Gestion des fiches ".$this->gepiSettings['denomination_eleves'],
			  "Cet outil vous permet de modifier/supprimer/ajouter des fiches ".$this->gepiSettings['denomination_eleves'].".");
	$this->creeNouveauItem("/bulletin/bull_index.php",
			  "Visualisation et impression des bulletins",
			  "Cet outil vous permet de visualiser � l'�cran et d'imprimer les bulletins, classe par classe.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Bulletins scolaires",'images/icons/bulletin_16.png');
	  return true;
	}
  }

  private function notanet(){
	$this->b=0;

	if ((getSettingValue("active_notanet")=='y')) {
	  $this->creeNouveauItem("/mod_notanet/index.php",
				"Notanet/Fiches Brevet",
				"Cet outil permet :<br />
				- d'effectuer les calculs et la g�n�ration du fichier CSV requis pour Notanet.
				L'op�ration renseigne �galement les tables n�cessaires pour g�n�rer les Fiches brevet.<br />
				- de g�n�rer les fiches brevet");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Notanet/Fiches Brevet",'images/icons/document.png');
	  return true;
	}
  }

  private function anneeAnterieure(){
	$this->b=0;

	if (getSettingValue("active_annees_anterieures")=='y') {
		$this->creeNouveauItem("/mod_annees_anterieures/index.php",
				"Ann�es ant�rieures",
				"Cet outil permet de g�rer et de consulter les donn�es d'ann�es ant�rieures (bulletins simplifi�s,...).");

		$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
				"Ann�es ant�rieures",
				"Cet outil permet de consulter les donn�es d'ann�es ant�rieures (bulletins simplifi�s,...).");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Ann�es ant�rieures",'images/icons/document.png');
	  return true;
	}
  }

  protected function messages(){
	$this->b=0;
	$this->creeNouveauItem("/messagerie/index.php",
			"Panneau d'affichage",
			"Cet outil permet la gestion des messages � afficher sur la page d'accueil des utilisateurs.");
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Panneau d'affichage",'images/icons/mail.png');
	  return true;
	}
  }

  protected function inscription(){
	$this->b=0;

	if (getSettingValue("active_inscription")=='y') {
	  $this->creeNouveauItem("/mod_inscription/inscription_config.php",
			  "Configuration du module d'inscription/visualisation",
			  "Configuration des diff�rents param�tres du module");

	  if (getSettingValue("active_inscription_utilisateurs")=='y'){
		$this->creeNouveauItem("/mod_inscription/inscription_index.php",
				"Acc�s au module d'inscription/visualisation",
				"S'inscrire ou se d�sinscrire - Consulter les inscriptions");
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Inscription",'images/icons/document.png');
	  return true;
	}
  }

  protected function discipline(){
	$this->b=0;

	$this->creeNouveauItem("/mod_discipline/index.php",
			"Discipline",
			"Signaler des incidents, prendre des mesures, des sanctions.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Discipline",'images/icons/document.png');
	  return true;
	}

  }

  private function plugins(){
	$this->b=0;

	$query = mysql_query('SELECT * FROM plugins WHERE ouvert = "y" order by description');

	while ($plugin = mysql_fetch_object($query)){
	$this->b=0;
	  $nomPlugin=$plugin->nom;
	  $this->verif_exist_ordre_menu('bloc_plugin_'.$nomPlugin);
	  // On offre la possibilit� d'inclure un fichier functions_nom_du_plugin.php
	  // Ce fichier peut lui-m�me contenir une fonction calcul_autorisation_nom_du_plugin voir plus bas.
	  if (file_exists($this->cheminRelatif."mod_plugins/".$nomPlugin."/functions_".$nomPlugin.".php"))
		include_once($this->cheminRelatif."mod_plugins/".$nomPlugin."/functions_".$nomPlugin.".php");

	  $querymenu = mysql_query('SELECT * FROM plugins_menus
								WHERE plugin_id = "'.$plugin->id.'"
								ORDER by titre_item');

	  while ($menuItem = mysql_fetch_object($querymenu)){
		// On regarde si le plugin a pr�vu une surcharge dans le calcul de l'affichage de l'item dans le menu
		// On commence par regarder si une fonction du type calcul_autorisation_nom_du_plugin existe
		$nom_fonction_autorisation = "calcul_autorisation_".$nomPlugin;

		if (function_exists($nom_fonction_autorisation))
		  // Si une fonction du type calcul_autorisation_nom_du_plugin existe, on calcule le droit de l'utilisateur � afficher cet item dans le menu
		  $result_autorisation = $nom_fonction_autorisation($this->loginUtilisateur,$menuItem->lien_item);
		else
		  $result_autorisation=true;

		if (($menuItem->user_statut == $this->statutUtilisateur) and ($result_autorisation)) {
		  $this->creeNouveauItemPlugin("/".$menuItem->lien_item,
				supprimer_numero(iconv("utf-8","iso-8859-1",$menuItem->titre_item)),
				iconv("utf-8","iso-8859-1",$menuItem->description_item));
		}

	  }

	  if ($this->b>0){
		$descriptionPlugin= iconv("utf-8","iso-8859-1",$plugin->description);
		$this->creeNouveauTitre('accueil',"$descriptionPlugin",'images/icons/package.png');
	  }

	}

  }

  protected function geneseClasses(){
	$this->b=0;
	$this->creeNouveauItem("/mod_genese_classes/index.php",
			"G�n�se des classes",
			"Effectuer la r�partition des �l�ves par classes en tenant comptes des options,...");
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"G�n�se des classes",'images/icons/document.png');
	  return true;
	}
  }

  protected function epreuvesBlanches(){
	$this->b=0;

	//insert into setting set name='active_mod_epreuve_blanche', value='y';
	if (getSettingValue("active_mod_epreuve_blanche")=='y') {
	  $this->creeNouveauItem("/mod_epreuve_blanche/index.php",
			  "�preuves blanches",
			  "Organisation d'�preuves blanches,...");
	}
//insert into setting set name='active_mod_epreuve_blanche', value='y';
	if (getSettingValue("active_mod_examen_blanc")=='y') {
	  $this->creeNouveauItem("/mod_examen_blanc/index.php",
			  "Examens blancs",
			  "Organisation d'examens blancs,...");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"�preuves blanches",'images/icons/document.png');
	  return true;
	}
  }

  protected function adminPostBac(){
	$this->b=0;

	if (getSettingValue("active_mod_apb")=='y') {
	  $this->creeNouveauItem("/mod_apb/index.php",
			  "Export APB",
			  "Export du fichier XML pour le syst�me Admissions Post-Bac");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Export Post-Bac",'images/icons/document.png');
	  return true;
	}
  }
 private function gestionEleveAID(){
	$this->b=0;

	if (getSettingValue("active_mod_gest_aid")=='y') {

	  $sql = "SELECT * FROM aid_config ";
	  // on exclue la rubrique permettant de visualiser quels �l�ves ont le droit d'envoyer/modifier leur photo
	  $flag_where = 'n';

	  if (getSettingValue("num_aid_trombinoscopes") != "") {
		$sql .= "WHERE indice_aid!= '".getSettingValue("num_aid_trombinoscopes")."'";
		$flag_where = 'y';
	  }

	  // si le plugin "gestion_autorisations_publications" existe et est activ�, on exclue la rubrique correspondante
	  $test_plugin = sql_query1("select ouvert from plugins where nom='gestion_autorisations_publications'");

	  if (($test_plugin=='y') and (getSettingValue("indice_aid_autorisations_publi") != ""))
		if ($flag_where == 'n')
		  $sql .= "WHERE indice_aid!= '".getSettingValue("indice_aid_autorisations_publi")."'";
		else
		  $sql .= "and indice_aid!= '".getSettingValue("indice_aid_autorisations_publi")."'";

	  $sql .= " ORDER BY nom";
	  $call_data = mysql_query($sql);
	  $nb_aid = mysql_num_rows($call_data);
	  $i=0;

	  while ($i < $nb_aid) {
		$indice_aid = @mysql_result($call_data, $i, "indice_aid");
		$call_prof = mysql_query("SELECT *
					FROM j_aid_utilisateurs_gest
					WHERE (id_utilisateur = '" . $this->loginUtilisateur . "'
					AND indice_aid = '$indice_aid')");
		$nb_result = mysql_num_rows($call_prof);

		if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
		  $nom_aid = @mysql_result($call_data, $i, "nom");
		  $this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "Cet outil vous permet de g�rer l'appartenance des �l�ves aux diff�rents groupes.");
		}

		$i++;
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Gestion des AID",'images/icons/document.png');
	  return true;
	}
  }




  
  
  
  
  
  

}
?>
