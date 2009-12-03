<?php
/*
 * $Id: notanet_admin.php 2147 2008-07-23 09:01:04Z tbelliard $
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
};

// Check access
// Pour GEPI 1.4.3 � 1.4.4
// INSERT INTO droits VALUES('/mod_notanet/notanet_admin.php','V','F','F','F','F','F','Gestion du module NOTANET','');
// Pour GEPI 1.5.x
// INSERT INTO droits VALUES('/mod_notanet/notanet_admin.php','V','F','F','F','F','F','F','Gestion du module NOTANET','');
if (!checkAccess()) {
   header("Location: ../logout.php?auto=1");
   die();
}


if (isset($_POST['activer'])) {
    if (!saveSetting("active_notanet", $_POST['activer'])){
		$msg = "Erreur lors de l'enregistrement du param�tre activation/d�sactivation !";
	}
	else{
		$msg = "Les modifications ont �t� enregistr�es !";
	}
}


// header
$titre_page = "Gestion du module Notanet/Fiches Brevet";
require_once("../lib/header.inc");

?>
<p class='bold'><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Activation du module Notanet/Fiches Brevet</h2>

<form action="notanet_admin.php" name="form1" method="post">

<p>Le module Notanet/Fiches Brevet concernet les coll�ges.<br />
<label for='activer_y' style='cursor: pointer;'><input type="radio" name="activer" id="activer_y" value="y" <?php if (getSettingValue("active_notanet")=='y') echo " checked"; ?> />
&nbsp;Activer l'acc�s au module Notanet/Fiches Brevet</label><br />
<label for='activer_n' style='cursor: pointer;'><input type="radio" name="activer" id="activer_n" value="n" <?php if (getSettingValue("active_notanet")=='n') echo " checked"; ?> />
&nbsp;D�sactiver l'acc�s au module Notanet/Fiches Brevet</label></p>

<input type="hidden" name="is_posted" value="1" />

<br />
<br />
<center><input type="submit" value="Enregistrer" style="font-variant: small-caps;" /></center>
</form>

<?php require("../lib/footer.inc.php");?>