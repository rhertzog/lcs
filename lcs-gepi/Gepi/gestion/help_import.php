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

<p class=bold>Aide � l'importation</p>
<p>Le fichier d'importation doit �tre au format csv (s�parateur : point-virgule)
<br />Le fichier doit contenir les diff�rents champs suivants, tous obligatoires :<br />
--> <B>IDENTIFIANT</B> : l'identifiant de l'�l�ve<br />
--> <B>Nom</B><br />
--> <B>Pr�nom</B><br />
--> <B>Sexe</B>  : F ou M<br />
--> <B>Date de naissance</B> : jj/mm/aaaa<br />
--> <B>Classe (fac.)</B> : le nom court d'une classe d�j� d�finie dans la base GEPI ou bien le caract�re - si l'�l�ve n'est pas affect� � une classe.<br />
--> <B>R�gime</B> : d/p (demi-pensionnaire) ext. (externe) int. (interne) ou i-e (interne extern�(e))<br />
--> <B>Doublant</B> : R (pour un doublant)  - (pour un non-doublant)<br />
--> <B><?php echo ucfirst(getSettingValue("gepi_prof_suivi")); ?></B> : l'identifiant d'un <?php echo getSettingValue("gepi_prof_suivi"); ?> d�j� d�fini dans la base GEPI ou bien le caract�re - si l'�l�ve n'a pas de <?php echo getSettingValue("gepi_prof_suivi"); ?>.<br />
--> <B>Identifiant de l'�tablissement d'origine </B> : le code RNE identifiant chaque �tablissement scolaire et d�j� d�fini dans la base GEPI, ou bien le caract�re - si l'�tablissement n'est pas connu.<br /></p>

<p class='bold'>IDENTIFIANT</p>
<p>Identifiant de l'�l�ve : il peut s'agir de n'importe quelle suite de caract�res et/ou de chiffres sans espace. Si ce format n'est pas respect�, la suite de caract�res ??? appara�t � la place de l'identifiant. Les identifiants qui apparaissent en rouge correspondent � des noms d'utilisateur d�j� existants dans la base GEPI. Les donn�es existantes seront alors �cras�es par les donn�es pr�sentes dans le fichier � importer !</p>
<p class='bold'>Nom</p>
<p>Nom de l'�l�ve. Il peut s'agir de n'importe quelle suite de caract�res et/ou de chiffres avec �ventuellement des espaces</p>
<p class='bold'>Pr�nom</p>
<p>Pr�nom de l'�l�ve. M�me remarque que pour le nom. Les noms et pr�noms qui apparaissent en bleu correspondent � des �l�ves existant dans la base GEPI et portant les m�mes noms et pr�noms.</p>
<p class='bold'>Sexe</p>
<p>Les seuls caract�res accept�s sont F pour f�minin et M pour masculin (respectez les majuscules). Si ce format n'est pas respect�, la suite de caract�res ??? appara�t.</p>
<p class='bold'>Date de naissance</p>
<p>Il s'agit de la date de naissance de l'�l�ve. Le seul format autoris� est jj/mm/aaaa. Par exemple, pour un �l�ve n� le 15 avril 1985, on tapera 15/04/1985. Si ce format n'est pas respect�, la suite de caract�res ??? appara�t.</p>
<p class='bold'>Classe</p>
<p>Classe dans laquelle l'�l�ve est affect�. Les seuls donn�es accept�es sont :
<br />--> le nom court d'une classe d�j� d�finie dans la base GEPI
<br />--> ou bien le caract�re - si l'�l�ve n'est pas affect� � une classe.
<br />Si la classe n'est pas d�finie dans la base GEPI, celle-ci sera consid�r�e comme erron�e.
<br />La proc�dure d'importation ne permet pas de changer un �l�ve de classe.
<br />En revanche, il est possible d'affecter � une classe, un �l�ve existant de la base qui n'est pas d�j� affect� � une classe.<br /></p>
<p class='bold'>R�gime</p>
<p>Les seules suites de caract�res accept�es sont "d/p", "ext.", "int." et "i-e" (respectez les minuscules). Dans tous les autres cas, la suite de caract�res ??? appara�t.
<br />--> d/p pour demi-pensionnaire,
<br />--> ext. pour externe,
<br />--> int. pour interne,
<br />--> i-e  pour interne extern�(e).</p>
<p class='bold'>Doublant</p>
<p>Les seuls caract�res accept�s sont "R" et "-". Dans tous les autres cas, la suite de caract�re ??? appara�t.
<br />--> R pour un doublant,
<br />--> - pour un non-doublant.</p>
<p class='bold'><?php echo ucfirst(getSettingValue("gepi_prof_suivi")); ?></p>
<p>L'identifiant d'un <?php echo getSettingValue("gepi_prof_suivi"); ?> d�j� d�fini dans la base GEPI ou bien le caract�re - si l'�l�ve n'a pas de <?php echo getSettingValue("gepi_prof_suivi"); ?>.
<br />Il s'agit obligatoirement d'un professeur de la classe de l'�l�ve. Dans le cas contraire, la suite de caract�res ??? appara�t. Il en est de m�me si la classe n'est pas d�finie.</p>
<p class='bold'>Identifiant de l'�tablissement d'origine </p>
<p>Le code RNE identifiant chaque �tablissement scolaire et d�j� d�fini dans la base GEPI, ou bien le caract�re - si l'�tablissement n'est pas connu.<br /></p>
<?php require("../lib/footer.inc.php");?>