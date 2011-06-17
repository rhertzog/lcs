<?php

@set_time_limit(0);
/*
 * $Id: clean_tables.php 5937 2010-11-21 17:42:55Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_POST, EXTR_OVERWRITE);


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

// Page bourrin�e... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();

$liste_tables_del = array(
"classes",
"eleves",
"groupes",
"responsables",
"j_eleves_groupes",
"j_groupes_classes",
"j_groupes_professeurs",
"j_groupes_matieres",
"j_eleves_classes",
"j_professeurs_matieres",
"matieres",
"periodes",
"utilisateurs"
);

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : Nettoyage des tables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold>|<a href="index.php">Retour accueil initialisation</a>|</p>
<?php
echo "<center><h3 class='gepi'>Sixi�me phase d'initialisation<br />Nettoyage des tables</h3></center>";
if (!isset($is_posted)) {
   echo "<p><b>ATTENTION ...</b> : vous ne devez proc�der � cette op�ration uniquement si toutes les donn�es (�l�ves, classes, professeurs, disciplines, options) ont �t� d�finies !</p>";
   echo "<p>Les donn�es inutiles import�es � partir des fichiers GEP lors des diff�rentes phases d'initialisation seront effac�es !</p>";
   echo "<form enctype='multipart/form-data' action='clean_tables.php' method='post'>";
   echo "<input type=hidden name='is_posted' value='yes' />";
   echo "<p><input type=submit value='Proc�der au nettoyage' />";
   echo "</form>";
} else {
   $j=0;
   $flag=0;
   while (($j < count($liste_tables_del)) and ($flag==0)) {
       if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)==0) {
           $flag=1;
       }
       $j++;
   }
   if ($flag != 0){
       echo "<p><b>ATTENTION ...</b><br />";
       echo "L'initialisation des donn�es de l'ann�e n'est pas termin�e, certaines donn�es concernant les �l�ves, les classes, les groupes, les professeurs ou les mati�res sont manquantes. La proc�dure ne peut continuer !</p>";
		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
       die();
   }
   //Suppression des donn�es inutiles dans la tables utilisateurs
   echo "<h3 class='gepi'>V�rification des donn�es concernant les professeurs</h3>";
   $req = mysql_query("select login from utilisateurs where (statut = 'professeur' and etat='actif')");
   $sup = 'no';
   $nb_prof = mysql_num_rows($req);
   $i = 0;
   while ($i < $nb_prof) {
       $login_prof = mysql_result($req, $i, 'login');
       $test = mysql_query("select id_professeur from j_professeurs_matieres where id_professeur = '$login_prof'");
       if (mysql_num_rows($test)==0) {
           $del = @mysql_query("delete from utilisateurs where login = '$login_prof'");
           echo "Le professeur $login_prof a �t� supprim� de la base.<br />";
           $sup = 'yes';
       } else {
           $test = mysql_query("select login from j_groupes_professeurs where login = '$login_prof'");
           if (mysql_num_rows($test)==0) {
               $del = @mysql_query("delete from utilisateurs where login = '$login_prof'");
               echo "Le professeur $login_prof a �t� supprim� de la base.<br />";
               $sup = 'yes';
            }
       }
       $i++;
   }
   if ($sup == 'no') {
       echo "<p>Aucun professeur n'a �t� supprim� !</p>";
    }
    //Suppression des donn�es inutiles dans la tables des mati�res
   echo "<h3 class='gepi'>V�rification des donn�es concernant les mati�res</h3>";
   $req = mysql_query("select matiere from matieres");
    $sup = 'no';
   $nb_mat = mysql_num_rows($req);
   $i = 0;
   while ($i < $nb_mat) {
       $mat = mysql_result($req, $i, 'matiere');
        $test1 = mysql_query("select id_matiere from j_professeurs_matieres where id_matiere = '$mat'");
        if (mysql_num_rows($test1)==0) {
            $test2 = mysql_query("select id_matiere from j_groupes_matieres where id_matiere = '$mat'");
           if (mysql_num_rows($test2)==0) {
               $del = @mysql_query("delete from matieres where matiere = '$mat'");
               echo "La mati�re $mat a �t� supprim�e de la base.<br />";
               $sup = 'yes';
           }
       }
       $i++;
    }
   if ($sup == 'no') {
       echo "<p>Aucune mati�re n'a �t� supprim�e !</p>";
    }
    //Suppression des donn�es inutiles dans la tables des responsables
   echo "<h3 class='gepi'>V�rification des donn�es concernant les responsables des �l�ves</h3>";
   $req = mysql_query("select ereno, nom1, prenom1 from responsables");
   $sup = 'no';
   $nb_resp = mysql_num_rows($req);
   $i = 0;
    while ($i < $nb_resp) {
        $resp = mysql_result($req, $i, 'ereno');
       $test1 = mysql_query("select ereno from eleves where ereno = '$resp'");
       if (mysql_num_rows($test1)==0) {
           $nom_resp = mysql_result($req, $i, 'nom1');
           $prenom_resp = mysql_result($req, $i, 'prenom1');
           $del = @mysql_query("delete from responsables where ereno = '$resp'");
           echo "Le responsable ".$prenom_resp." ".$nom_resp." a �t� supprim� de la base.<br />";
          $sup = 'yes';
       }
       $i++;
   }
   if ($sup == 'no') {
       echo "<p>Aucun responsable n'a �t� supprim� !</p>";
    }


	//Suppression des donn�es inutiles dans la table j_eleves_etablissements
	echo "<h3 class='gepi'>V�rification des donn�es concernant l'�tablissement d'origine des �l�ves</h3>\n";

	//SELECT e.* FROM eleves e LEFT JOIN j_eleves_etablissements jec ON jec.id_eleve=e.elenoet WHERE jec.id_eleve is NULL;
	//SELECT jec.* FROM j_eleves_etablissements jec LEFT JOIN eleves e ON jec.id_eleve=e.elenoet WHERE e.elenoet IS NULL;
	$sql="SELECT jec.* FROM j_eleves_etablissements jec
			LEFT JOIN eleves e ON jec.id_eleve=e.elenoet
			WHERE e.elenoet IS NULL;";
	$res_jee=mysql_query($sql);
	if(mysql_num_rows($res_jee)==0) {
		echo "<p>Aucune association �l�ve/�tablissement n'a �t� supprim�e.</p>\n";
	}
	else {
		$cpt_suppr_jee=0;
		while($lig_jee=mysql_fetch_object($res_jee)) {
			$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='".$lig_jee->id_eleve."' AND id_etablissement='".$lig_jee->id_etablissement."';";
			$suppr=mysql_query($sql);
			if($suppr) {
				$cpt_suppr_jee++;
			}
		}
		echo "<p>$cpt_suppr_jee association(s) �l�ve/�tablissement a(ont) �t� supprim�e(s).<br />(<i>pour des �l�ves qui ne sont plus dans l'�tablissement</i>).</p>\n";
	}


	echo "<p><br /></p>\n";
   echo "<p>Fin de la proc�dure !</p>";
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
