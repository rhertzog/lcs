<?php
/*
 * $Id: help.php 2147 2008-07-23 09:01:04Z tbelliard $
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
$titre_page = "Aide en ligne";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

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

<?php require("../lib/footer.inc.php");?>