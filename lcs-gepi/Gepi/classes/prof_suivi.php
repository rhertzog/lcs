<?php
/*
* $Id: prof_suivi.php 6604 2011-03-03 13:46:55Z crob $
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

// Global configuration file
// Initialisations files
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

include "../lib/periodes.inc.php";

if (isset($is_posted) and ($is_posted == '1')) {
	check_token();

	$call_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' AND e.login = c.login)");
	$nombreligne = mysql_num_rows($call_eleves);
	//=========================
	// AJOUT: boireaus 20071010
	$log_eleve=$_POST['log_eleve'];
	$prof_principal=isset($_POST['prof_principal']) ? $_POST['prof_principal'] : NULL;
	//=========================
	$k = 0;
	While ($k < $nombreligne) {
		$login_eleve = mysql_result($call_eleves, $k, 'login');

		//=========================
		// AJOUT: boireaus 20071010
		// R�cup�ration du num�ro de l'�l�ve dans les saisies:
		$num_eleve=-1;
		for($i=0;$i<count($log_eleve);$i++){
			if($login_eleve==$log_eleve[$i]){
				$num_eleve=$i;
				break;
			}
		}
		if($num_eleve!=-1){

			//=========================
			// MODIF : boireaus 20071010
			//$prof_login = 'prof_'.$login_eleve;
			//$reg_prof = isset($_POST[$prof_login])?$_POST[$prof_login]:NULL;
			$reg_prof="";
			if(isset($prof_principal[$num_eleve])){$reg_prof=$prof_principal[$num_eleve];}
			//=========================

			$call_profsuivi_eleve = mysql_query("SELECT professeur FROM j_eleves_professeurs WHERE (login = '$login_eleve' AND id_classe='$id_classe')");
			$eleve_profsuivi = @mysql_result($call_profsuivi_eleve, '0', 'professeur');
			if (($reg_prof == '') and ($eleve_profsuivi != '')) {
				$reg = mysql_query("DELETE FROM j_eleves_professeurs WHERE (login='$login_eleve' AND id_classe='$id_classe')");
			}
			if  (($reg_prof != '') and ($eleve_profsuivi != '') and ($reg_prof != $eleve_profsuivi)) {
				$reg_data = mysql_query("UPDATE j_eleves_professeurs SET professeur ='$reg_prof' WHERE (login='$login_eleve' AND id_classe='$id_classe')");
			}
			if  (($reg_prof != '') and ($eleve_profsuivi == '')) {
					$reg_data = mysql_query("INSERT INTO j_eleves_professeurs VALUES ('$login_eleve', '$reg_prof', '$id_classe')");
			}
		}
		$k++;
	}
	header("Location: classes_const.php?id_classe=$id_classe");
	die();
}


//**************** EN-TETE **************************************
$titre_page = "Gestion des classes | ".ucfirst(getSettingValue("gepi_prof_suivi"));
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************
$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_classe, "0", "classe");
?>
<p class='bold'><a href="classes_const.php?id_classe=<?php echo $id_classe;?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>|<a href="help.php"> Aide </a></p>
<p class='bold'>Classe : <?php echo $classe; ?></p>
<?php
if (!isset($nb_prof) or ($nb_prof == '')) {
	// On regarde combien il y a de profs de suivi actuellement dans la classe
	$call_profsuivi = mysql_query("SELECT DISTINCT professeur FROM j_eleves_professeurs WHERE id_classe='$id_classe'");
	$nb_prof = mysql_num_rows($call_profsuivi);
?>

	<p>
	<?php
		echo getSettingValue("gepi_prof_suivi");
	?> : pr�cisez le nombre dans la classe :</p>
	<form enctype="multipart/form-data" action="prof_suivi.php" method="post">
	<select size = '1' name='nb_prof'>
	<?php for ($i=1;$i<6;$i++) {
		echo "<option value='$i'";
		// Si il existe d�j� des profs de suivi dans la classe, on propose par d�faut, un nombre de profs �gal au nombre de profs de suivi.
		if ($i == $nb_prof) {echo " selected ";}
		echo ">$i</option>\n";
	}
	?>
	</select>
	<input type='submit' value='Valider' /><br />
	<input type='hidden' name='id_classe' value='<?php echo $id_classe;?>' />
	</form>
	<?php
} else if (!isset($etape2) or ($etape2 != 'yes')) {
?>
	<p>Pour chaque <?php echo getSettingValue("gepi_prof_suivi"); ?>, pr�cisez le professeur : </p>
	<?php
	$call_profsuivi = mysql_query("SELECT DISTINCT professeur FROM j_eleves_professeurs WHERE id_classe='$id_classe'");
	$nb_prof_exist = mysql_num_rows($call_profsuivi);
	$i = 0;

	while ($i < $nb_prof_exist) {
		$prof_classe[$i] = mysql_result($call_profsuivi,$i,'professeur');
		$i++;
	}

	$call_prof = mysql_query("SELECT DISTINCT u.login, u.nom, u.prenom " .
				"FROM utilisateurs u, j_groupes_professeurs jgp, j_groupes_classes jgc WHERE (" .
				"u.statut = 'professeur' and " .
				"u.login = jgp.login and " .
				"jgp.id_groupe = jgc.id_groupe and " .
				"jgc.id_classe = '".$id_classe."'" .
				") ORDER BY u.login");
	$nb = mysql_num_rows($call_prof);
	echo "<form enctype=\"multipart/form-data\" action=\"prof_suivi.php\" method=post>\n";
	for ($i=1; $i < $nb_prof+1; $i++) {
		echo "<p><select name='prof_suivi[$i]'>\n";
		echo "<option value=''>(vide)</option>\n";
		$j='0';
		$flag_selected = 1;
		while ($j < $nb) {
			$profsuivi = mysql_result($call_prof, $j, "login");
			$prof_nom = mysql_result($call_prof, $j, "nom");
			$prof_prenom = mysql_result($call_prof, $j, "prenom");
			echo "<option value='$profsuivi'";
			$k = 0;
			while ($k < $nb_prof_exist) {
				if (($prof_classe[$k] == $profsuivi) and ($flag_selected == 1))  {
					echo " selected ";
					$prof_classe[$k] = '';
					$flag_selected = 0;
				}
				$k++;
			}
			//echo ">$prof_prenom $prof_nom</option>\n";
			echo ">".ucwords(strtolower($prof_prenom))." ".strtoupper($prof_nom)."</option>\n";
			$j++;
		}
		echo "</select></p>\n";
	}
	?>
	<input type='submit' value='Enregistrer' /><br />
	<input type='hidden' name='id_classe' value='<?php echo $id_classe;?>' />
	<input type='hidden' name='nb_prof' value='<?php echo $nb_prof;?>' />
	<input type='hidden' name='etape2' value='yes' />
	<input type='hidden' name='etape3' value='no' />
	</form>
	<?php

} else if ($etape3 != 'yes') {
	$etape2 = 'no';
	$nb_prof_suivi=0;
	for ($i=1; $i < $nb_prof+1; $i++) {
		if ($prof_suivi[$i] != '') {
			$nb_prof_suivi++;
			$tab_prof[$nb_prof_suivi] = $prof_suivi[$i];
			$etape2 = 'yes';
		}
	}
	if ($etape2 == 'no') {
		echo "<p>Vous n'avez pas d�fini de ".getSettingValue("gepi_prof_suivi")." !</p>\n";
		echo "<form enctype=\"multipart/form-data\" action=\"prof_suivi.php\" method=post>\n";
		echo "<input type='submit' value='Retour' /><br />\n";
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		echo "<input type='hidden' name='nb_prof' value='$nb_prof' />\n";
		echo "</form>\n";
	} else {
		echo "<form enctype=\"multipart/form-data\" action=\"prof_suivi.php\" method=\"post\">\n";
		$call_eleves = mysql_query("SELECT DISTINCT j.login FROM j_eleves_classes j WHERE (j.id_classe = '$id_classe') ORDER BY login");
		$nombreligne = mysql_num_rows($call_eleves);
		if ($nombreligne == '0') {
			echo "<p>Il n'y a pas d'�l�ves actuellement dans cette classe.</p>\n";
			die();
		} else {
			//echo "<p>Cliquez sur le bouton \"Enregistrer\" en bas de la page pour enregistrer.</p>\n";
			echo "<p>Cliquez sur le bouton \"Enregistrer\" pour valider.</p>\n";
			echo "<center><input type='submit' value='Enregistrer' /></center><br />\n";
			$k = '0';
			echo "<table border='1' cellpadding='5' class='boireaus' summary='Choix des �l�ves'>\n";
			echo "<tr><th>Nom Pr�nom</th>\n";
			for ($i=1; $i < $nb_prof_suivi+1; $i++) {
				$call_prof = mysql_query("SELECT * FROM utilisateurs WHERE login = '$tab_prof[$i]'");
				$prof_nom = mysql_result($call_prof, 0, "nom");
				$prof_prenom = mysql_result($call_prof, 0, "prenom");
				echo "<th><p class='small'>".ucfirst(getSettingValue("gepi_prof_suivi"))." :<br />$prof_nom $prof_prenom<br />\n";
				echo "<a href=\"javascript:CocheColonne(".$i.")\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
				//echo " / <a href=\"javascript:DecocheColonne(".$i.")\"><img src='../images/disabled.png' width='15' height='15' alt='Tout d�cocher' /></a>";
				echo "</p></th>\n";
			}
			echo "<th><p class='small'>Pas de ".getSettingValue("gepi_prof_suivi")."<br />\n";
			echo "<a href=\"javascript:CocheColonne(".$i.")\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>";
			echo "</p></th>\n";
			echo "</tr>\n";

			$alt=1;
			While ($k < $nombreligne) {
				$login_eleve = mysql_result($call_eleves, $k, 'login');
				$prof_login = "prof_".$login_eleve;
				$call_data_eleves = mysql_query("SELECT * FROM eleves WHERE (login = '$login_eleve')");
				$nom_eleve = @mysql_result($call_data_eleves, '0', 'nom');
				$prenom_eleve = @mysql_result($call_data_eleves, '0', 'prenom');
				$call_profsuivi_eleve = mysql_query("SELECT * FROM j_eleves_professeurs WHERE (login = '$login_eleve' and id_classe='$id_classe')");
				$eleve_profsuivi = @mysql_result($call_profsuivi_eleve, '0', 'professeur');
				$prof_login = "prof_".$login_eleve;

				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<td><p>".strtoupper($nom_eleve)." $prenom_eleve\n";
				//=========================
				// AJOUT: boireaus 20071010
				echo "<input type='hidden' name='log_eleve[$k]' value=\"$login_eleve\" />\n";
				//=========================
				echo "</p></td>\n";
				$flag_prof = 'no';
				for ($i=1; $i < $nb_prof_suivi+1; $i++) {
					//=========================
					// AJOUT: boireaus 20071010
					//echo "<td><p><input type='radio' name='$prof_login' id='case_".$i."_".$k."' value='$tab_prof[$i]'";
					echo "<td><p><input type='radio' name='prof_principal[$k]' id='case_".$i."_".$k."' value='$tab_prof[$i]'";
					//=========================
					if (($eleve_profsuivi == $tab_prof[$i]) or ($nb_prof_suivi==1)) {
						$flag_prof = 'yes';
						echo " checked ";
					}
					echo " /></p></td>\n";
				}
				//=========================
				// AJOUT: boireaus 20071010
				//echo "<td><p><input type='radio' name='$prof_login' id='case_".$i."_".$k."' value=''";
				echo "<td><p><input type='radio' name='prof_principal[$k]' id='case_".$i."_".$k."' value=''";
				//=========================
				if (($flag_prof == 'no') and ($nb_prof_suivi!=1)) {
					echo " checked ";
				}
				echo " /></p></td>\n";
				echo "</tr>\n";
				$k++;
			}
			echo "</table>\n";
			echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";
			echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
			echo "<input type='hidden' name='nb_prof' value='$nb_prof' />\n";
			echo "<input type='hidden' name='etape2' value='yes' />\n";
			echo "<input type='hidden' name='etape3' value='yes' />\n";
			echo "<input type='hidden' name='nb_prof_suivi' value='$nb_prof_suivi' />\n";
			echo "<input type='hidden' name='is_posted' value='1' />\n";
			echo add_token_field();
			echo "</form>\n";


			echo "<script type='text/javascript'>

function CocheColonne(i) {
	for (var ki=0;ki<$k;ki++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = true;
		}
	}
}

/*
function DecocheColonne(i) {
	for (var ki=0;ki<$k;ki++) {
		if(document.getElementById('case_'+i+'_'+ki)){
			document.getElementById('case_'+i+'_'+ki).checked = false;
		}
	}
}
*/

</script>
";

		}
	}
}

require("../lib/footer.inc.php");
?>