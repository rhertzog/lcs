<?php
/*
 * @version: $Id: discipline_admin.php 2554 2008-10-12 14:49:29Z crob $
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

//INSERT INTO droits VALUES ( '/mod_ooo/ooo_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mod�le Ooo : Admin', '');
//$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/ooo_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mod�le Ooo : Admin', '');";

$msg = '';
if ((isset($_POST['is_posted']))&&(isset($_POST['activer']))) {
    if (!saveSetting("active_mod_ooo", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du param�tre activation/d�sactivation !";
}

if (isset($_POST['is_posted']) and ($msg=='')) {$msg = "Les modifications ont �t� enregistr�es !";}
// header
$titre_page = "Gestion du module mod�le Open Office";
require_once("../lib/header.inc");
?>
<p class=bold><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<h2>Configuration g�n�rale</h2>
<i>La d�sactivation du module mod�le Open Office n'entra�ne aucune suppression des donn�es. Lorsque le module est d�sactiv�, il n'est plus possible de g�rer ses propres mod�les.</i>

<br />
<form action="ooo_admin.php" name="form1" method="post">
<p>
<input type="radio" name="activer" id='activer_y' value="y" <?php if (getSettingValue("active_mod_ooo")=='y') echo " checked"; ?> />&nbsp;<label for='activer_y' style='cursor: pointer;'>Activer le module mod�le Open Office</label><br />
<input type="radio" name="activer" id='activer_n' value="n" <?php if (getSettingValue("active_mod_ooo")=='n') echo " checked"; ?> />&nbsp;<label for='activer_n' style='cursor: pointer;'>D�sactiver le module mod�le Open Office</label>
</p>

<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></center>
</form>
<?php
$nom_fichier_modele_ooo =''; //pour �viter un notice (la variable ne sert pas ici ..
include_once ("./lib/chemin.inc.php");
// test d'�criture dans le dossier mes_modeles
$dossier_test = "./".$nom_dossier_modeles_ooo_mes_modeles."dossier_test";
$resultat_mkdir = @mkdir($dossier_test);
if (!($resultat_mkdir)) {
	echo "<p style=\"color: red;\">ATTENTION : Les droits d'�criture sur le dossier /mod_ooo/$nom_dossier_modeles_ooo_mes_modeles sont incorrects. Gepi doit avoir les droits de cr�ation de dossiers et de fichiers dans ce dossier pour assurer le bon fonctionnement du module</p>";
	@rmdir($dossier_test);
}
$dossier_test = "./tmp/dossier_test";
$resultat_mkdir = @mkdir($dossier_test);
if (!($resultat_mkdir)) {
	echo "<p style=\"color: red;\">ATTENTION : Les droits d'�criture sur le dossier /mod_ooo/tmp/ sont incorrects. Gepi doit avoir les droits de cr�ation de dossiers et de fichiers dans ce dossier pour assurer le bon fonctionnement du module</p>";
	@rmdir($dossier_test);
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>