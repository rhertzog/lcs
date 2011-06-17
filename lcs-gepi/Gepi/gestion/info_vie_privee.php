<?php

/*
 * Last modification  : 14/03/2005
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


//**************** EN-TETE *****************
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************

?>
<H1 class='gepi'>GEPI - Vie Priv�e</H1>
<?php
echo "<h2>Cadre l�gal</h2>";
echo "<p>Gepi est un logiciel de traitement de donn�es entrant dans le cadre des Environnements Num�riques de Travail (ENT).";
echo "<br/>A ce titre, il est soumis � un encadrement l�gal particulier. Nous vous invitons � consulter <a href='http://www.cnil.fr/vos-responsabilites/declarer-a-la-cnil/declarer-un-fichier/declaration/mon-secteur-dactivite/mon-theme/je-dois-declarer/declaration-selectionnee/dec-mode/DISPLAYSINGLEFICHEDECL/dec-uid/30/'>l'Arr�t� du 30 novembre 2006</a> relatif aux dispositifs de traitement de donn�es au sein du minist�re de l'�ducation nationale.</p>";

if (getSettingValue("num_enregistrement_cnil") != '')  {

echo "<h2>D�claration � la CNIL</h2>";

echo "Conform�ment � l'article 16 de la loi 78-17 du 6 janvier 1978, dite loi informatique et libert�, nous vous informons

 que le pr�sent site a fait l'objet d'une d�claration de traitement automatis� d'informations nominatives aupr�s de la CNIL

  : le site est enregistr� sous le n� ".getSettingValue("num_enregistrement_cnil");

}

echo "<H2>1/ Cookies</H2>";

echo "A chacune de vos visites GEPI tente de g�n�rer un cookie de session. L'acceptation de ce cookie par votre navigateur est obligatoire pour acc�der au site. Ce cookie de session est un cookie temporaire exig� pour des

raisons de s�curit�. Ce type de cookie n'enregistre pas d'information sur votre ordinateur, il vous attribue un num�ro de session

 qu'il communique au serveur pour pouvoir suivre votre session en toute s�curit�. Il est mis temporairement dans la m�moire de

  votre ordinateur et est exploitable uniquement durant le temps de connexion. Il est ensuite d�truit lorsque vous vous d�connectez ou

  lorsque vous fermez toutes les fen�tres de votre navigateur.";



echo "<H2>2/ Informations transmises</H2>";



echo "Lors de l'ouverture d'une session certaines informations sont transmises au serveur :

<ul>

<li>le num�ro de votre session (voir ci-dessus),</li>

<li>votre identifiant,</li>

<li>l'adresse IP de votre machine,</li>

<li>le type de votre navigateur,

<li>l'origine de la connexion au pr�sent site,</li>

<li>les heures et dates de d�but et fin de la session.</li>

</ul>";

switch (getSettingValue("duree_conservation_logs")) {

case 30:

$duree="un mois";

break;

case 60:

$duree="deux mois";

break;

case 183:

$duree="six mois";

break;

case 365:

$duree="un an";

break;

}

echo "Pour des raisons de s�curit�, ces informations sont conserv�es pendant <b>".$duree."</b> � partir de leur enregistrement.";



echo "<H2>3/ S�curit�</H2>";

echo "<b>Par mesure de s�curit�, pensez � vous d�connecter � la fin de votre visite sur le site (lien en haut � droite).</b>";

require("../lib/footer.inc.php");
?>