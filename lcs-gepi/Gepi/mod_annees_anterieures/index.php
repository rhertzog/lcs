<?php
/*
 * $Id : $
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
    die();};

// INSERT INTO droits VALUES ('/mod_annees_anterieures/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Index donn�es ant�rieures', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Si le module n'est pas activ�...
if(getSettingValue('active_annees_anterieures')!="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'acc�s illicite?

	header("Location: ../logout.php?auto=1");
	die();
}




//**************** EN-TETE *****************
$titre_page = "Donn�es ant�rieures";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<div class='norme'><p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
echo "</p></div>\n";

echo "<p>L'utilisation de ce module n�cessite peut-�tre une autorisation particuli�re aupr�s de la CNIL.<br />
Voici pour info un extrait de mail pass� sur la liste de diffusion [gepi-users]:</p>\n";
echo "<pre>c'est dans mon document
http://julien.noel.free.fr/gepi/arguments_gepi_v_1.0.3.sxw

article 4
� Dur�e de conservation : A l'exception de celles concernant la classe,
le groupe, la division fr�quent�s et des options suivies au cours de
l'ann�e scolaire pr�c�dente qui peuvent �tre conserv�es pendant deux
ann�es scolaires, les informations relatives � la scolarit� des �l�ves
ainsi qu'� leur situation financi�re vis�es � l'article 3 c et d ne
doivent pas �tre conserv�es au-del� de l'ann�e scolaire pour laquelle
elles ont �t� enregistr�es, sauf dispositions l�gales contraires ; Les
informations relatives � l'identit� de l'�l�ve ainsi que de son
responsable l�gal vis�es � l'article 3 a et b ne doivent pas �tre
conserv�es au-del� du d�part de l'�l�ve de l'�tablissement. �. Voir
http://www.cnil.fr/index.php?id=1232 pour plus d'informations.</pre>\n";
echo "<p><br /></p>\n";


echo "<p>Au menu:</p>\n";
echo "<p>Les pages d'administration:</p>\n";
echo "<ul>\n";
echo "<li><p><a href='conservation_annee_anterieure.php'>Conservation des donn�es (*) de l'ann�e qui se termine</a><br />(<i>(*) autres que les AIDs</i>).</p></li>\n";
echo "<li><p><a href='archivage_aid.php'>Conservation des donn�es des AIDs</a>.</p></li>\n";
echo "<li><p><a href='nettoyer_annee_anterieure.php'>Nettoyage des donn�es d'�l�ves ayant quitt� l'�tablissement</a>.</p></li>\n";
echo "<li><p><a href='corriger_ine.php'>Correction des INE non renseign�s ou mal renseign�s lors de la conservation</a>.</p></li>\n";
echo "</ul>\n";

echo "<p>Les pages de consultation:</p>\n";
echo "<ul>\n";
echo "<li><p><a href='consultation_annee_anterieure.php'>Consulter les saisies ant�rieures</a></p></li>\n";
echo "<li><p>Une fonction de consultation s'ouvrant en popup: popup_annee_anterieure.php?logineleve=...<br />La fonction et la page sont pr�tes... reste � placer ici et l� des liens vers popup_annee_anterieure.php?logineleve=... en testant si le module est activ�, si le statut de l'utilisateur lui permet l'acc�s,...</p></li>\n";
echo "<li><p>Consultation d'un r�capitulatif des avis des conseils de classe.<br />La fonction et la page sont pr�tes... reste � placer ici et l� des liens vers popup_annee_anterieure.php?logineleve=... en testant si le module est activ�, si le statut de l'utilisateur lui permet l'acc�s,...</p></li>\n";
echo "</ul>\n";

/*
echo "<p>...</p>\n";
echo "<ul>\n";
echo "<li><p>A FAIRE: Une page de recherche selon divers crit�res: nom, pr�nom, ann�e,...</p></li>\n";
echo "<li><p>...</p></li>\n";
echo "</ul>\n";
*/

require("../lib/footer.inc.php");
?>