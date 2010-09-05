<?php
/*
 * @version: $Id: index.php 4895 2010-07-25 19:22:54Z regis $
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

/**
 * Gestion des cahiers de textes
 * 
 * @param $_POST['activer'] activation/d�sactivation
 * @param $_POST['export_cn_ods'] autorisation de l'export au format OD
 * @param $_POST['referentiel_note'] referentiel de note
 * @param $_POST['note_autre_que_sur_referentiel'] note autre que sur referentiel
 * @param $_POST['is_posted']
 *
 */

$accessibilite="y";
$titre_page = "Gestion des carnets de notes";
$niveau_arbo = 1;
$gepiPathJava="./..";

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
};

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

/******************************************************************
 *    Enregistrement des variables pass�es en $_POST si besoin
 ******************************************************************/
$msg = '';
$post_reussi=FALSE;

if (isset($_POST['activer'])) {
    if (!saveSetting("active_carnets_notes", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du param�tre activation/d�sactivation !";
}


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

	/*
	if (isset($_POST['appreciations_types_profs'])) {
		if (!saveSetting("appreciations_types_profs", 'y')) {
			$msg .= "Erreur lors de l'enregistrement de l'autorisation d'utilisation d'appr�ciations-types pour les professeurs !";
		}
	}
	else{
		if (!saveSetting("appreciations_types_profs", 'n')) {
			$msg .= "Erreur lors de l'enregistrement de l'interdiction d'utilisation d'appr�ciations-types pour les professeurs !";
		}
	}
	*/
}
if (isset($_POST['referentiel_note'])) {
	if (!saveSetting("referentiel_note", $_POST['referentiel_note'])) {
		$msg .= "Erreur lors de l'enregistrement du referentiel de note !";
	}
}

if (isset($_POST['note_autre_que_sur_referentiel'])) {
	if (!saveSetting("note_autre_que_sur_referentiel", $_POST['note_autre_que_sur_referentiel'])) {
		$msg .= "Erreur lors de l'enregistrement de note_autre_que_sur_referentiel !";
	}
}


if (isset($_POST['is_posted']) and ($msg=='')){
  $msg = "Les modifications ont �t� enregistr�es !";
  $post_reussi=TRUE;
}

// on demande une validation si on quitte sans enregistrer les changements
$messageEnregistrer="Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?";
/****************************************************************
                     HAUT DE PAGE
****************************************************************/

// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc");

/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la cr�ation du fil d'ariane";
//$titre_page = "Gestion des cahiers de textes";
//require_once("../lib/header.inc");
/****************************************************************
			CONSTRUCTION DE LA PAGE
****************************************************************/
// header
// $titre_page = "Gestion des carnets de notes";
// require_once("../lib/header.inc");
/*
?>
<p class=bold><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<h2>Configuration g�n�rale</h2>
<i>La d�sactivation des carnets de notes n'entra�ne aucune suppression des donn�es. Lorsque le module est d�sactiv�, les professeurs n'ont pas acc�s au module.</i>
<br />
<form action="index.php" name="form1" method="post">

<p>
<input type="radio" name="activer" id='activer_y' value="y" <?php if (getSettingValue("active_carnets_notes")=='y') echo " checked"; ?> />&nbsp;<label for='activer_y' style='cursor: pointer;'>Activer les carnets de notes</label><br />
<input type="radio" name="activer" id='activer_n' value="n" <?php if (getSettingValue("active_carnets_notes")=='n') echo " checked"; ?> />&nbsp;<label for='activer_n' style='cursor: pointer;'>D�sactiver les carnets de notes</label>
</p>

<?php

	echo "<br />\n";

	echo "<p>\n";
	if(file_exists("../lib/ss_zip.class.php")){
		echo "<input type='checkbox' name='export_cn_ods' id='export_cn_ods' value='y'";
		if(getSettingValue('export_cn_ods')=='y'){
			echo ' checked';
		}
		echo " /> \n";
		echo "<label for='export_cn_ods' style='cursor: pointer;'>Permettre l'export des carnets de notes au format ODS.</label><br />(<i>si les professeurs ne font pas le m�nage apr�s g�n�ration des exports,<br />ces fichiers peuvent prendre de la place sur le serveur</i>)\n";
	}
	else{
		echo "En mettant en place la biblioth�que 'ss_zip_.class.php' dans le dossier '/lib/', vous pouvez g�n�rer des fichiers tableur ODS pour permettre des saisies hors ligne, la conservation de donn�es,...<br />Voir <a href='http://smiledsoft.com/demos/phpzip/' target='_blank'>http://smiledsoft.com/demos/phpzip/</a><br />Une version limit�e est disponible gratuitement.<br />Emplacement alternatif: <a href='http://stephane.boireau.free.fr/informatique/gepi/ss_zip.class.php.zip'>http://stephane.boireau.free.fr/informatique/gepi/ss_zip.class.php.zip</a>\n";

		// Comme la biblioth�que n'est pas pr�sente, on force la valeur � 'n':
		$svg_param=saveSetting("export_cn_ods", 'n');
	}
	echo "</p>\n";

 */
	/*
	echo "<br />\n";

	echo "<p>\n";
	echo "<input type='checkbox' name='appreciations_types_profs' id='appreciations_types_profs' value='y'";
	if(getSettingValue('appreciations_types_profs')=='y'){
		echo ' checked';
	}
	echo " /> \n";
	echo "<label for='appreciations_types_profs' style='cursor: pointer;'>Permettre aux professeurs d'utiliser des appr�ciations-types sur les bulletins.\n";
	echo "</p>\n";

?>
<br/>
<table>
	<tr>
		<td style="font-variant: small-caps;" valign='top'>
		R�f�rentiel des notes :</td>
		<td>
		<table summary='Genre'><tr valign='top'><td></td><td>
		R�f�rentiel des notes par d�faut : <input type="text" name="referentiel_note" size="8" value="<?php echo(getSettingValue("referentiel_note")); ?>" />
		</td></tr></table>
		<table summary='Genre'><tr valign='top'><td></td><td>
		<input type="radio" name="note_autre_que_sur_referentiel" id="note_sur_referentiel" value="V" <?php if(getSettingValue("note_autre_que_sur_referentiel")=="V"){echo 'checked';} ?> /><label for='note_sur_referentiel'> Autoriser les notes autre que sur le r�f�rentiel par d�faut</label><br />
		<input type="radio" name="note_autre_que_sur_referentiel" id="note_autre_que_referentiel" value="F" <?php if(getSettingValue("note_autre_que_sur_referentiel")=="F"){echo 'checked';} ?> /><label for='note_autre_que_referentiel'> Notes uniquement sur le r�f�rentiel par d�faut</label>
		</td></tr></table>
		</td>
	</tr>
</table>


<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></center>
</form>
<?php
	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	*/


/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseign�
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// D�commenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/cahier_notes_admin/index_template.php';

$tbs_last_connection=""; // On n'affiche pas les derni�res connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche);




?>
