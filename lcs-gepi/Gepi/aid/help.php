<?php
/*
 * @version: $Id: help.php 2147 2008-07-23 09:01:04Z tbelliard $
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

<H2>Outils compl�mentaires de gestion des AIDs</H2>
<p>En activant les <b>outils compl�mentaires de gestion des AIDs</b>, vous avez
acc�s � des champs suppl�mentaires (attribution d'une salle, possibilit�
de d�finir un r�sum�, le type de production, des mots_cl�s, un public
destinataire...).</p>

<p>Ces donn�es suppl�mentaires sont accessibles � travers des fiches dite
"fiches projet".</p>

<p>Ces fiches sont accessibles dans GEPI � diff�rents
types d'utilisateurs connect�s (administrateur, professeur, cpe, �l�ve ou responsable)</p>

<p>Ces fiches sont �galement en partie accessibles dans l'interface publique de GEPI � diff�rents. Un param�trage permet de d�terminer les champs visibles ou non par le public.</p>

<p>Selon son statut (professeurs responsables, cpe ou �l�ves responsables) et lorsque l'administrateur a ouvert cette possibilit�,
l'utilisateur a acc�s en modification � certains champs de cette fiche.</p>
<p>En plus des professeurs responsable de chaque AID, l'administrateur peut d�signer des utilisateurs (professeurs ou CPE) ayant le droit de
modifier les fiches projet m�me lorsque l'administrateur a d�sactiv�
cette possibilit� pour les professeurs responsables.</p>
<?php require("../lib/footer.inc.php");?>