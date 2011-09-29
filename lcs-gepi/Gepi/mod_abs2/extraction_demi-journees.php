<?php
/**
 *
 * @version $Id: extraction_demi-journees.php 8056 2011-08-30 20:43:42Z jjacquard $
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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
require_once("../lib/initialisationsPropel.inc.php");
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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On v�rifie si le module est activ�
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activ�.");
}

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

include_once 'lib/function.php';

// Initialisation des variables
//r�cup�ration des param�tres de la requ�te
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] :(isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] :(isset($_SESSION["nom_eleve"]) ? $_SESSION["nom_eleve"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :(isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$date_absence_eleve_debut = isset($_POST["date_absence_eleve_debut"]) ? $_POST["date_absence_eleve_debut"] :(isset($_GET["date_absence_eleve_debut"]) ? $_GET["date_absence_eleve_debut"] :(isset($_SESSION["date_absence_eleve_debut"]) ? $_SESSION["date_absence_eleve_debut"] : NULL));
$date_absence_eleve_fin = isset($_POST["date_absence_eleve_fin"]) ? $_POST["date_absence_eleve_fin"] :(isset($_GET["date_absence_eleve_fin"]) ? $_GET["date_absence_eleve_fin"] :(isset($_SESSION["date_absence_eleve_fin"]) ? $_SESSION["date_absence_eleve_fin"] : NULL));
$type_extrait = isset($_POST["type_extrait"]) ? $_POST["type_extrait"] :(isset($_GET["type_extrait"]) ? $_GET["type_extrait"] : NULL);
$affichage = isset($_POST["affichage"]) ? $_POST["affichage"] :(isset($_GET["affichage"]) ? $_GET["affichage"] : NULL);

if (isset($id_classe) && $id_classe != null) $_SESSION['id_classe_abs'] = $id_classe;
if (isset($date_absence_eleve_debut) && $date_absence_eleve_debut != null) $_SESSION['date_absence_eleve_debut'] = $date_absence_eleve_debut;
if (isset($date_absence_eleve_fin) && $date_absence_eleve_fin != null) $_SESSION['date_absence_eleve_fin'] = $date_absence_eleve_fin;

if ($date_absence_eleve_debut != null) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/",".",$date_absence_eleve_debut));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
    $dt_date_absence_eleve_debut->setDate($dt_date_absence_eleve_debut->format('Y'), $dt_date_absence_eleve_debut->format('m') - 1, $dt_date_absence_eleve_debut->format('d'));
}
if ($date_absence_eleve_fin != null) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/",".",$date_absence_eleve_fin));
} else {
    $dt_date_absence_eleve_fin = new DateTime('now');
}

$inverse_date=false;
if($dt_date_absence_eleve_debut->format("U")>$dt_date_absence_eleve_fin->format("U")){
    $date2=clone $dt_date_absence_eleve_fin;
    $dt_date_absence_eleve_fin= $dt_date_absence_eleve_debut;
    $dt_date_absence_eleve_debut= $date2;
    $message="Les dates de d�but et de fin ont �t� invers�es.";
    $inverse_date=true;
    $_SESSION['date_absence_eleve_debut'] = $dt_date_absence_eleve_debut->format('d/m/Y');
    $_SESSION['date_absence_eleve_fin'] = $dt_date_absence_eleve_fin->format('d/m/Y'); 
}
$dt_date_absence_eleve_debut->setTime(0,0,0);
$dt_date_absence_eleve_fin->setTime(23,59,59);

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
$dojo=true;
//**************** EN-TETE *****************
$titre_page = "Les absences";
if ($affichage != 'ods') {// on affiche pas de html
    require_once("../lib/header.inc");

    include('menu_abs2.inc.php');
    include('menu_bilans.inc.php');
    ?>
    <div id="contain_div" class="css-panes">
         <?php if (isset($message)){
          echo'<h2 class="no">'.$message.'</h2>';
        }?>
    <p>
      <strong>Pr�cision:</strong> Un manquement � l'obligation de pr�sence sur une heure, entraine le d�compte de la demi-journ�e correspondante pour l'�l�ve.
    </p>
    <form dojoType="dijit.form.Form" id="choix_extraction" name="choix_extraction" action="<?php $_SERVER['PHP_SELF']?>" method="post">
    <h2>Les demi-journ�es
    du	
    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_debut" name="date_absence_eleve_debut" value="<?php echo $dt_date_absence_eleve_debut->format('Y-m-d')?>" />
    au               
    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_fin" name="date_absence_eleve_fin" value="<?php echo $dt_date_absence_eleve_fin->format('Y-m-d')?>" />
	</h2>
    Nom (facultatif) : <input dojoType="dijit.form.TextBox" type="text" style="width : 10em" name="nom_eleve" size="10" value="<?php echo $nom_eleve?>"/>

    <?php
    //on affiche une boite de selection avec les classe
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
	$classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
    } else {
	$classe_col = $utilisateur->getClasses();
    }
    if (!$classe_col->isEmpty()) {
	    echo ("Classe : <select dojoType=\"dijit.form.Select\" style=\"width :12em;font-size:12px;\" name=\"id_classe\">");
	    echo "<option value='-1'>Toutes les classes</option>\n";
	    foreach ($classe_col as $classe) {
		    echo "<option value='".$classe->getId()."'";
		    if ($id_classe == $classe->getId()) echo " selected='selected' ";
		    echo ">";
		    echo $classe->getNom();
		    echo "</option>\n";
	    }
	    echo "</select> ";
    } else {
	echo 'Aucune classe avec �l�ve affect� n\'a �t� trouv�e';
    }
    ?>
    </p>
	<p>
    <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="html">Afficher</button>
    <button type="submit"  style="font-size:12px" dojoType="dijit.form.Button" name="affichage" value="ods">Enregistrer au format ods</button>
	</p>
	</form>

    <?php
}
if ($affichage != null && $affichage != '') {
    $eleve_query = EleveQuery::create()->orderByNom()->orderByPrenom()->distinct();
    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    } else {
		$eleve_query->filterByUtilisateurProfessionnel($utilisateur);
    }
    if ($id_classe !== null && $id_classe != -1) {
		$eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
    }
    if ($nom_eleve !== null && $nom_eleve != '') {
		$eleve_query->filterByNomOrPrenomLike($nom_eleve);
    }
    $eleve_col = $eleve_query->distinct()->find();

    foreach ($eleve_col as $eleve) {
	$eleve->setVirtualColumn('DemiJourneesAbsencePreRempli', $eleve->getDemiJourneesAbsence($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count());
	$eleve->setVirtualColumn('DemiJourneesNonJustifieesPreRempli', $eleve->getDemiJourneesNonJustifieesAbsence($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count());
	$eleve->setVirtualColumn('RetardsPreRempli', $eleve->getRetards($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count());
    }
}

if ($affichage == 'html') {
    echo 'Total �l�ves : '.$eleve_col->count();
    echo '<table style="border:1px solid">';
    $precedent_eleve_id = null;
    echo '<tr style="border:1px solid">';

    echo '<td style="border:1px solid;">';
    echo 'Nom Pr�nom';
    echo '</td>';

    echo '<td style="border:1px solid;">';
    echo 'Classe';
    echo '</td>';

    echo '<td style="border:1px solid;">';
    echo 'nbre de demi-journ�es d\'absence';
    echo '</td>';

    echo '<td style="border:1px solid;">';
    echo 'non justifiees';
    echo '</td>';

    echo '<td style="border:1px solid;">';
    echo 'nbre de retards';
    echo '</td>';

    echo '</tr>';

    $nb_demijournees = 0;
    $nb_nonjustifiees = 0;
    $nb_retards = 0;
    foreach ($eleve_col as $eleve) {
	    echo '<tr style="border:1px solid">';
	    
	    echo '<td style="border:1px solid;">';
	    echo $eleve->getNom().' '.$eleve->getPrenom();
	    echo '</td>';

	    echo '<td style="border:1px solid;">';
	    echo $eleve->getClasseNom();
	    echo '</td>';

	    echo '<td style="border:1px solid;">';
	    echo $eleve->getDemiJourneesAbsencePreRempli();
	    $nb_demijournees = $nb_demijournees + $eleve->getDemiJourneesAbsencePreRempli();
	    echo '</td>';

	    echo '<td style="border:1px solid;">';
	    echo $eleve->getDemiJourneesNonJustifieesPreRempli();
	    $nb_nonjustifiees = $nb_nonjustifiees + $eleve->getDemiJourneesNonJustifieesPreRempli();
	    echo '</td>';

	    echo '<td style="border:1px solid;">';
	    echo $eleve->getRetardsPreRempli();
	    $nb_retards = $nb_retards + $eleve->getRetardsPreRempli();
	    echo '</td>';

	    echo '</tr>';
    }
    echo '<tr style="border:1px solid">';

    echo '<td style="border:1px solid;">';
    echo 'Total �l�ves : ';
    echo $eleve_col->count();
    echo '</td>';

    echo '<td style="border:1px solid;">';
    echo '</td>';

    echo '<td style="border:1px solid;">';
    echo $nb_demijournees;
    echo '</td>';

    echo '<td style="border:1px solid;">';
    echo $nb_nonjustifiees;
    echo '</td>';

    echo '<td style="border:1px solid;">';
    echo $nb_retards;
    echo '</td>';

    echo '</tr>';
    echo '<h5>Extraction faite le '.date("d/m/Y - h:i").'</h5>';
} else if ($affichage == 'ods') {
    // load the TinyButStrong libraries    
	include_once('../tbs/tbs_class.php'); // TinyButStrong template engine
    
    //include_once('../tbs/plugins/tbsdb_php.php');
    $TBS = new clsTinyButStrong; // new instance of TBS
    include_once('../tbs/plugins/tbs_plugin_opentbs.php');
    $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

    // Load the template
	$extraction_demi_journees=repertoire_modeles('absence_extraction_demi-journees.ods');
    $TBS->LoadTemplate($extraction_demi_journees);

    $titre = 'Extrait des demi-journ�es d\'absences du '.$dt_date_absence_eleve_debut->format('d/m/Y').' au '.$dt_date_absence_eleve_fin->format('d/m/Y');
    $classe = null;
    if ($id_classe != null && $id_classe != '') {
	$classe = ClasseQuery::create()->findOneById($id_classe);
	if ($classe != null) {
	    $titre .= ' pour la classe '.$classe->getNom();
	}
    }
    if ($nom_eleve != null && $nom_eleve != '' ) {
	$titre .= ' pour les �l�ves dont le nom ou le pr�nom contient '.$nom_eleve;
    }
    $TBS->MergeField('titre', $titre);

    $eleve_array_avec_data = Array();
    $nb_demijournees = 0;
    $nb_nonjustifiees = 0;
    $nb_retards = 0;
    foreach ($eleve_col as $eleve) {
	$eleve_array_avec_data[$eleve->getPrimaryKey()] = Array(
	    'eleve' => $eleve
	    , 'getDemiJourneesAbsencePreRempli' => $eleve->getDemiJourneesAbsencePreRempli()
	    , 'getDemiJourneesNonJustifieesPreRempli' => $eleve->getDemiJourneesNonJustifieesPreRempli()
	    , 'getRetardsPreRempli' => $eleve->getRetardsPreRempli()
		);

	    $nb_demijournees = $nb_demijournees + $eleve->getDemiJourneesAbsencePreRempli();
	    $nb_nonjustifiees = $nb_nonjustifiees + $eleve->getDemiJourneesNonJustifieesPreRempli();
	    $nb_retards = $nb_retards + $eleve->getRetardsPreRempli();
    }


    $TBS->MergeBlock('eleve_col', $eleve_array_avec_data);

    $TBS->MergeField('eleve_count', $eleve_col->count());
    $TBS->MergeField('nb_demijournees',$nb_demijournees);
    $TBS->MergeField('nb_nonjustifiees', $nb_nonjustifiees);
    $TBS->MergeField('nb_retards', $nb_retards);

    // Output as a download file (some automatic fields are merged here)
    $nom_fichier = 'extrait_demi-journee_';
    if ($classe != null) {
	$nom_fichier .= $classe->getNom().'_';
    }
    $nom_fichier .=  $dt_date_absence_eleve_fin->format("d_m_Y").'.ods';
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $nom_fichier);
}
?>
	</div>
<?php
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dojo.parser");
    dojo.require("dijit.form.Button");    
    dojo.require("dijit.form.Form");    
    dojo.require("dijit.form.DateTextBox");
    dojo.require("dijit.form.TextBox");
    dojo.require("dijit.form.Select");
    </script>';
require_once("../lib/footer.inc.php");
?>