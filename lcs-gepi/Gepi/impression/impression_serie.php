<?php
/*
* $Id: impression_serie.php 2947 2009-02-18 14:29:06Z eabgrall $
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

//INSERT INTO droits VALUES ('/impression/impression_serie.php', 'V', 'V', 'V', 'V', 'V', 'V', 'Impression des listes (PDF)', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$id_choix_periode=isset($_POST['id_choix_periode']) ? $_POST["id_choix_periode"] : 0;

 //**************** EN-TETE **************************************
 $titre_page = "Impression de listes au format PDF";
 require_once("../lib/header.inc");
 //**************** FIN EN-TETE **********************************
echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo " | <a href='./impression.php'>Impression rapide � l'unit�</a>";
echo " | <a href='./parametres_impression_pdf.php'>R�gler les param�tres du PDF</a>";
echo "</p>\n";

if ($id_choix_periode != 0) {
  $periode = "P�riode N�".$id_choix_periode;
  echo "<h3 align='center'>".$periode."</h3>\n";
  echo "<h3>Liste des classes : ";
   echo "</h3>\n";
} else {
  $periode="";
  echo "<h3>Liste des classes : ";
  echo "</h3>\n";
}

// s�lection multiple avec choix de la p�riode
echo "<div style=\"text-align: center;\">\n
   <fieldset>\n";

   if ($id_choix_periode == 0) {
       echo "<legend>S�lectionnez la p�riode pour laquelle vous souhaitez imprimer les listes.</legend>\n";
	   echo "<form method=\"post\" action=\"impression_serie.php\" name=\"imprime_serie\">\n";
	   $requete_periode = "SELECT DISTINCT `num_periode` FROM `periodes`";
	   $resultat_periode = mysql_query($requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysql_error());
       echo "<br />\n";
	   While ( $data_periode = mysql_fetch_array ($resultat_periode)) {
	       echo "<label for='id_choix_periode".$data_periode['num_periode']."' style='cursor: pointer;'>P�riode ".$data_periode['num_periode']." : </label><input type='radio' name='id_choix_periode' id='id_choix_periode".$data_periode['num_periode']."' value='".$data_periode['num_periode']."' /> <br />\n";
	   }
	   echo "<br /><br /> <input value=\"Valider la p�riode\" name=\"Valider\" type=\"submit\" />\n
              <br />\n";

    echo "</form>\n";
   } else {
       echo "<legend>S�lectionnez la (ou les) classe(s) pour lesquelles vous souhaitez imprimer les listes.</legend>\n";
	     echo "<form method=\"post\" action=\"liste_pdf.php\" target='_blank' name=\"imprime_pdf\">\n";
         if ($id_choix_periode != 0) {
				echo "<br />\n";

				echo "<select id='liste_classes' name='id_liste_classes[]' multiple='yes' size='5'>\n";
				    if($_SESSION['statut']=='scolarite'){ //n'affiche que les classes du profil scolarit�
                      $login_scolarite = $_SESSION['login'];
					  $requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` , jsc.login, jsc.id_classe
					                     FROM `periodes`, `classes` , `j_scol_classes` jsc
										 WHERE (jsc.login='$login_scolarite'
										 AND jsc.id_classe=classes.id
										 AND `periodes`.`num_periode` = ".$id_choix_periode."
										 AND `classes`.`id` = `periodes`.`id_classe`)
										 ORDER BY `nom_complet` ASC";
                    } else {
					  $requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` FROM `periodes`, `classes` WHERE `periodes`.`num_periode` = ".$id_choix_periode." AND `classes`.`id` = `periodes`.`id_classe` ORDER BY `nom_complet` ASC";
					}
					$resultat_classe = mysql_query($requete_classe) or die('Erreur SQL !'.$requete_classe.'<br />'.mysql_error());
					echo "		<optgroup label=\"-- Les classes --\">\n";
					While ( $data_classe = mysql_fetch_array ($resultat_classe)) {
								   echo "		<option value=\"";
								   echo $data_classe['id_classe'];
								   echo "\">";
								   echo $data_classe['nom_complet']." (".$data_classe['classe'].")";
								   echo "</option>\n";
					}
					echo "		</optgroup>\n";
				echo "	</select>\n";
				echo "<input value=\"".$id_choix_periode."\" name=\"id_periode\" type=\"hidden\" />\n";
				echo "<br /><br /> <input value=\"Valider les classes\" name=\"Valider\" type=\"submit\" />\n
          <br />\n";
				}
     echo "</form>\n";
	 }
   echo "</fieldset>\n
 </div>
 <br />";

if ($id_choix_periode != 0) {
 // Dans le cadre d'un professeur il peut choisir ses enseignements.
if($_SESSION['statut']=='professeur'){
echo "<h3>Liste des enseignements : </h3>\n";

$groups=get_groups_for_prof($_SESSION["login"]);

/*
echo "<pre>";
	print_r($groups);
echo "</pre>";
*/

// s�lection multiple avec choix de la p�riode
	echo "<div style=\"text-align: center;\">\n";
	echo "   <fieldset>\n
    <legend>S�lectionnez le (ou les) enseignement(s) pour lesquels vous souhaitez imprimer les listes.</legend>\n";
	 
	//echo "<form method=\"post\" action=\"liste_pdf.php\" name=\"imprime_pdf\">\n";
	echo "<form method=\"post\" action=\"liste_pdf.php\" target='_blank' name=\"imprime_pdf2\">\n";
	echo "<br />\n";
	//echo "<select id='liste_classes' name='id_liste_groupes[]' multiple='yes' size='5'>\n";
	echo "<select id='liste_groupes' name='id_liste_groupes[]' multiple='yes' size='5'>\n";
	echo "		<optgroup label=\"-- Les enseignements --\">\n";
	
	for($i=0;$i<count($groups);$i++){				
		echo "		<option value=\"";
		echo $groups[$i]['id'];
		echo "\">";
		echo $groups[$i]['matiere']['nom_complet']." (".$groups[$i]['classlist_string'].")";
		echo "</option>\n";			
	}	
	echo "		</optgroup>\n";
	echo "	</select>\n";
	echo "<br />Option de tri :<input type=\"radio\" name=\"tri\" value=\"classes\" />Par classe puis alphab�tique<input type=\"radio\" name=\"tri\" value=\"alpha\" checked /> Alphab�tique<br />\n";
    echo "<input value=\"".$id_choix_periode."\" name=\"id_periode\" type=\"hidden\" />\n";
    echo "<br /><br /> <input value=\"Valider les enseignements\" name=\"Valider\" type=\"submit\" />\n";
}
      echo "<br />\n
     </form>\n
   </fieldset>\n
 </div>";
}
// Fin de s�lection multiple avec choix de la p�riode.
require("../lib/footer.inc.php");
?>