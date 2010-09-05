<?php
/*
 * $Id: help.php 4950 2010-07-29 13:10:01Z regis $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//**************** EN-TETE *****************
$titre_page = "Aide en ligne Module Inscription";
//require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************


// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc");

if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la cr�ation du fil d'ariane";
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/



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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_inscription/help_template.php';

$tbs_last_connection=""; // On n'affiche pas les derni�res connexions
include($nom_gabarit);




/*
?>
<H1>Le module Inscription</H1>
Le module Inscription permet de d�finir un ou plusieurs items (journ�e, stage, intervention, ...), au(x)quel(s) les utilisateurs pourront s'inscrire ou se d�sinscrire en cochant ou d�cochant une croix.
<ul>
<li>La configuration du module est accessible aux administrateurs et � la scolarit�.</li>
<li>L'interface d'inscription/d�sinscription est accessible aux professeurs, cpe, administrateurs et scolarit&eacute;.</li>
</ul>

<p>Apr�s avoir activ� le module, les administrateurs et la scolarit&eacute; disposent dans la page d'accueil
 d'un nouveau module de configuration.</p>
<p>La premi�re �tape consiste � configurer ce module :
<ul>
<li><b>Activation / D�sactivation :</b>
<br />Tant que le module n'est pas enti�rement configur�, vous avez int�r�t � ne pas activer la page autorisant
les inscriptions. De cette fa�on, ce module reste invisible aux autres utilisateurs (professeurs et cpe).
<br />De m�me, lorsque les inscriptions sont closes, vous pouvez d�sactiver les inscriptions, tout en gardant l'acc�s au module de configuration.
</li>
<li><b>Liste des items :</b>
<br />C'est la liste des entit�s auxquelles les utilisateurs pourront s'incrire.
<br />Chaque entit� est carat�ris�e par un identifiant num�rique, une date (format AAAA/MM/JJ), une heure (20 caract�res max), une description (200 caract�res max).
</li>
<li><b>Titre du module :</b>
<br />Vous avez ici la possibilit� de personnaliser l'intitul� du module visible dans la page d'accueil.
</li>
<li><b>Texte explicatif :</b>
<br />
Ce texte sera visible par les personnes acc�dant au module d'inscription/d�sincription.
</li>
</ul>

<?php require("../lib/footer.inc.php"); */ ?>