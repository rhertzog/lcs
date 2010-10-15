<?php
/*
 * $Id: index.php 2396 2008-09-15 14:58:40Z tbelliard $
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
$titre_page = "Outil de visualisation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a></p>
<center>
<p>
Vous pouvez choisir ci-dessous diff�rents moyens de visualisation :
</p>

<!--table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5 -->
<table width="700" class="bordercolor" summary="Choix de l'outil">
<tr>
    <td width=200><a href="eleve_classe.php"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> par rapport � la classe</a></td>
    <td>Permet de comparer les r�sultats d'un <?php echo $gepiSettings['denomination_eleve'];?> vis � vis des r�sultats moyens de la classe, mati�re par mati�re, p�riode par p�riode.</td>
</tr>
<tr>
    <td width=200><a href="eleve_eleve.php"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> par rapport � un autre <?php echo $gepiSettings['denomination_eleve'];?></a></td>
    <td>Permet de comparer les r�sultats d'un <?php echo $gepiSettings['denomination_eleve'];?> vis � vis des r�sultats d'un autre <?php echo $gepiSettings['denomination_eleve'];?> (quelconque), mati�re par mati�re, p�riode par p�riode (permet �galement de comparer les r�sultats de l'ann�e pass�e pour un redoublant).</td>
</tr>
<tr>
    <td width=200><a href="evol_eleve.php">Evolution d'un <?php echo $gepiSettings['denomination_eleve'];?> sur l'ann�e</a></td>
    <td>Permet de visualiser l'�volution des r�sultats d'un <?php echo $gepiSettings['denomination_eleve'];?> sur l'ann�e, mati�re par mati�re.</td>
</tr>
<tr>
    <td width=200><a href="evol_eleve_classe.php">Evolution d'un <?php echo $gepiSettings['denomination_eleve'];?> et classe sur l'ann�e</a></td>
    <td>Permet de visualiser l'�volution des r�sultats d'un <?php echo $gepiSettings['denomination_eleve'];?> vis � vis de l'�volution de la classe, mati�re par mati�re.</td>
</tr>
<tr>
    <td width=200><a href="stats_classe.php">Evolution des moyennes de classes</a></td>
    <td>Permet d'obtenir les diff�rentes moyennes de la classe (maxi, mini, moyenne, etc.) mati�re par mati�re, avec �volution sur l'ann�e.</td>
</tr>
<tr>
    <td width=200><a href="classe_classe.php">Classe par rapport � autre classe</a></td>
    <td>Permet de comparer les r�sultats d'une classe vis � vis d'une autre classe, mati�re par mati�re, p�riode par p�riode.</td>
</tr>
<tr>
    <td width=200><a href="affiche_eleve.php?type_graphe=courbe"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> par rapport � un <?php echo $gepiSettings['denomination_eleve'];?> ou une moyenne</a></td>
    <td><b>Graphique en courbe</b>: Permet de comparer les r�sultats d'un <?php echo $gepiSettings['denomination_eleve'];?>, par rapport aux moyennes min/max/classe et par rapport � un autre <?php echo $gepiSettings['denomination_eleve'];?>, mati�re par mati�re, p�riode par p�riode.<br />Alternativement, ce choix permet d'obtenir les courbes des 3 trimestres.</td>
</tr>
<tr>
    <td width=200><a href="affiche_eleve.php?type_graphe=etoile"><?php echo ucfirst($gepiSettings['denomination_eleve']);?> par rapport � un <?php echo $gepiSettings['denomination_eleve'];?> ou une moyenne</a></td>
    <td><b>Graphique en �toile/polygone</b>: Permet de comparer les r�sultats d'un <?php echo $gepiSettings['denomination_eleve'];?>, par rapport aux moyennes min/max/classe et par rapport � un autre <?php echo $gepiSettings['denomination_eleve'];?>, mati�re par mati�re, p�riode par p�riode.<br />Alternativement, ce choix permet d'obtenir les polygones des 3 trimestres.</td>
</tr>
</table>
<p><br /></p>
</center>
<?php require("../lib/footer.inc.php");?>