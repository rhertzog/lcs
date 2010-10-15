<?php
/*
* Last modification  : 29/11/2006
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

//INSERT INTO droits VALUES ('/impression/impression.php', 'V', 'V', 'V', 'V', 'V', 'V', 'Impression des listes (PDF)', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE **************************************
$titre_page = "Impression de listes au format PDF";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo " | <a href='./impression_serie.php'>Impressions multiples</a>";
echo " | <a href='./parametres_impression_pdf.php'>R�gler les param�tres du PDF</a>";
echo "</p>\n";


$id_classe=isset($_GET['id_classe']) ? $_GET["id_classe"] : NULL;
$id_groupe=isset($_GET['id_groupe']) ? $_GET["id_groupe"] : NULL;
$ok=isset($_GET['ok']) ? $_GET["ok"] : NULL;


echo "<h3>Liste des classes : </h3>\n";

// Pour tout le monde la possibilit� d'imprimer la liste de toutes les classes par p�riode.
echo "<p>S�l�ctionnez la classe et la p�riode pour lesquels vous souhaitez imprimer une liste d'�l�ves au format PDF :</p>\n";

    //si statut scolarite ==> on affiche que les classes de compte scolarit�
	if($_SESSION['statut']=='scolarite'){
       $login_scolarite = $_SESSION['login'];
	   $sql="SELECT c.id, c.classe, jsc.login, jsc.id_classe 
	         FROM classes c, j_scol_classes jsc
			 WHERE (jsc.login='$login_scolarite'
			 AND jsc.id_classe=c.id)
			 ORDER BY c.classe";
	} else { //pour tous les statuts sauf scolarit�
	   $sql="SELECT id,classe FROM classes ORDER BY classe";
	}
	$result_classes=mysql_query($sql);
	$nb_classes = mysql_num_rows($result_classes);

	if(mysql_num_rows($result_classes)==0){
		echo "<p>Il semble qu'aucune classe n'ait encore �t� cr��e.</p>\n";
	}
	else{
		$nb_classes=mysql_num_rows($result_classes);
		$nb_class_par_colonne=round($nb_classes/3);
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='left'>\n";
		$cpt=0;
		//echo "<td style='padding: 0 10px 0 10px'>\n";
		echo "<td>\n";
		echo "<table border='0'>\n";
		while($lig_class=mysql_fetch_object($result_classes)){
			if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
				echo "</table>\n";
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td>\n";
				echo "<table border='0'>\n";
			}

			$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$lig_class->id' ORDER BY num_periode";
			$res_per=mysql_query($sql);

			if(mysql_num_rows($res_per)==0){
				echo "<p>ERREUR: Aucune p�riode n'est d�finie pour la classe $lig_class->classe</p>\n";
				echo "</body></html>\n";
				die();
			}
			else{
				echo "<tr>\n";
				echo "<td>$lig_class->classe</td>\n";
				while($lig_per=mysql_fetch_object($res_per)){
					echo "<td> - <a href='liste_pdf.php?id_classe=$lig_class->id&amp;periode_num=$lig_per->num_periode' target='_blank'>".$lig_per->nom_periode."</a></td>\n";
				}
				echo "</tr>\n";
			}
			$cpt++;
		}
		echo "</table>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}	
		
// Module toutes les classes

	if($_SESSION['statut']=='professeur'){
		echo "<p>S�l�ctionnez l'enseignement et la p�riode pour lesquels vous souhaitez imprimer une liste alphab�tique d'�l�ves au format PDF :</p>\n";
		$sql="SELECT DISTINCT g.id,g.description FROM groupes g, j_groupes_professeurs jgp WHERE
			jgp.login = '".$_SESSION['login']."' AND
			g.id=jgp.id_groupe
			ORDER BY g.description";
		$res_grp=mysql_query($sql);

		if(mysql_num_rows($res_grp)==0){
			echo "<p>Vous n'avez apparemment aucun enseignement.</p>\n";
			echo "</body></html>\n";
			die();
		}
		else{
			echo "<table>\n";
			while($lig_grp=mysql_fetch_object($res_grp)){
				echo "<tr>\n";
				unset($tabnumper);
				unset($tabnomper);
				$sql="SELECT DISTINCT c.classe,c.id FROM classes c, j_groupes_classes jgc WHERE
					jgc.id_groupe='$lig_grp->id' AND
					jgc.id_classe=c.id
					ORDER BY c.classe";
				//echo "$sql<br />\n";
				$res_class=mysql_query($sql);
				if(mysql_num_rows($res_class)>0){
					$chaine_class="";
					$cpt=0;
					while($lig_class=mysql_fetch_object($res_class)){
						$chaine_class.=",$lig_class->classe";

						if($cpt==0){
							$tabnumper=array();
							$tabnomper=array();
							$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$lig_class->id' ORDER BY num_periode";
							$res_per=mysql_query($sql);
							if(mysql_num_rows($res_per)==0){
								echo "<p>ERREUR: Aucune p�riode n'est d�finie pour la classe $lig_class->classe</p>\n";
								echo "</body></html>\n";
								die();
							}
							else{
								while($lig_per=mysql_fetch_object($res_per)){
									$tabnumper[]=$lig_per->num_periode;
									$tabnomper[]=$lig_per->nom_periode;
								}
							}
						}
						$cpt++;
					}
					$chaine_class=substr($chaine_class,1);

				}

				echo "<td>\n";
				echo "<b>".htmlentities($lig_grp->description)."</b> ($chaine_class) : ";
				echo "</td>\n";
				for($i=0;$i<count($tabnumper);$i++){
					if($i>0){echo "<td> - </td>\n";}
					echo "<td>\n";
					echo htmlentities($tabnomper[$i])." : Tri <a href='liste_pdf.php?id_groupe=$lig_grp->id&amp;periode_num=$tabnumper[$i]' target='_blank'>Alpha</a> - <a href='liste_pdf.php?id_groupe=$lig_grp->id&amp;periode_num=$tabnumper[$i]&amp;tri=classes' target='_blank'>Classe</a>\n";
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
			echo "</table>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
//}
require("../lib/footer.inc.php");
?>