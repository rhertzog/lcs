<?php
/*
 * $Id: index3.php 2396 2008-09-15 14:58:40Z tbelliard $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//Initialisation
unset($id_classe);
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : NULL);
unset($login_eleve);
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] : (isset($_GET["login_eleve"]) ? $_GET["login_eleve"] : NULL);

$error_login = false;
// Quelques filtrages de d�part pour pr�-initialiser la variable qui nous importe ici : $login_eleve
if ($_SESSION['statut'] == "responsable") {
	$get_eleves = mysql_query("SELECT e.login " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."' AND (re.resp_legal='1' OR re.resp_legal='2'))");

	if (mysql_num_rows($get_eleves) == 1) {
		// Un seul �l�ve associ� : on initialise tout de suite la variable $login_eleve
		$login_eleve = mysql_result($get_eleves, 0);
	} elseif (mysql_num_rows($get_eleves) == 0) {
		$error_login = true;
	}
	// Si le nombre d'�l�ves associ�s est sup�rieur � 1, alors soit $login_eleve a �t� d�j� d�fini, soit il faut pr�senter un choix.

} else if ($_SESSION['statut'] == "eleve") {
	if ($login_eleve != null and (strtoupper($login_eleve) != strtoupper($_SESSION['login']))) {
		tentative_intrusion(2, "Tentative d'un ".$gepiSettings['denomination_eleve']." de visualiser le bulletin simplifi� d'un autre ".$gepiSettings['denomination_eleve'].".");
	}
	// Si l'utilisateur identifi� est un �l�ve, pas le choix, il ne peut consulter que son �quipe p�dagogique
	$login_eleve = $_SESSION['login'];
}

if ($login_eleve and $login_eleve != null) {
	// On r�cup�re la classe de l'�l�ve, pour d�terminer automatiquement le nombre de p�riodes
	// On part du postulat que m�me si l'�l�ve change de classe en cours d'ann�e, c'est pour aller
	// dans une classe qui a le m�me nombre de p�riodes...
	$id_classe = mysql_result(mysql_query("SELECT id_classe FROM j_eleves_classes jec WHERE login = '".$login_eleve."' LIMIT 1"), 0);
}

if (isset($id_classe)) {
	// On regarde si le type est correct :
	if (!is_numeric($id_classe)) {
		tentative_intrusion("2", "Changement de la valeur de id_classe pour un type non num�rique.");
		echo "Erreur.";
		require ("../lib/footer.inc.php");
		die();
	}
	// On teste si un professeur a le droit d'acc�der � cette classe
	//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {

	//echo "\$_SESSION['statut']=".$_SESSION['statut']."<br />";
	//echo "\getSettingValue(\"GepiAccesBulletinSimpleProfToutesClasses\")=".getSettingValue("GepiAccesBulletinSimpleProfToutesClasses")."<br />";

	if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes") {

		//echo "SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')<br />";

		if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
			$test = mysql_num_rows(mysql_query("SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
			if ($test == "0") {
				tentative_intrusion("2", "Tentative d'acc�s par un prof � une classe dans laquelle il n'enseigne pas, sans en avoir l'autorisation.");
				echo "Vous ne pouvez pas acc�der � cette classe car vous n'y �tes pas professeur !";
				require ("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$gepi_prof_suivi=getSettingValue("gepi_prof_suivi");
			//echo "\$gepi_prof_suivi=$gepi_prof_suivi<br/>";

			$test = mysql_num_rows(mysql_query("SELECT 1=1 FROM j_eleves_classes jec, j_eleves_professeurs jep WHERE (jep.professeur='".$_SESSION['login']."' AND jep.login=jec.login AND jec.id_classe = '".$id_classe."')"));
			if ($test == "0") {
				tentative_intrusion("2", "Tentative d'acc�s par un prof � une classe dans laquelle il n'est pas $gepi_prof_suivi, sans en avoir l'autorisation.");
				echo "Vous ne pouvez pas acc�der � cette classe car vous n'y �tes pas $gepi_prof_suivi!";
				require ("../lib/footer.inc.php");
				die();
			}
		}
	}
}


//**************** EN-TETE *******************************
$titre_page = "Edition simplifi�e des bulletins";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ****************************
?>
<script type='text/javascript' language='javascript'>
function active(num) {
 document.form_choix_edit.choix_edit[num].checked=true;
}

function change_periode(){
var indi=document.form_choix_edit.periode1.selectedIndex;
document.form_choix_edit.periode2.value=indi+1;
}
</script>
<?php
//echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

// Si on a eu une erreur sur l'association responsable->�l�ve
if ($_SESSION['statut'] == "responsable" and $error_login == true) {
	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	echo "<p>Il semble que vous ne soyez associ� � aucun ".$gepiSettings['denomination_eleve'].". Contactez l'administrateur pour r�soudre cette erreur.</p>";
	require "../lib/footer.inc.php";
	die();
}

// V�rifications de s�curit�
if (
	($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesBulletinSimpleParent") != "yes") OR
	($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesBulletinSimpleEleve") != "yes")
	) {
	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	tentative_intrusion(1, "Tentative d'acc�s aux bulletins simplifi�s sans autorisation.");
	echo "<p>Vous n'�tes pas autoris� � visualiser cette page.</p>";
	require "../lib/footer.inc.php";
	die();
}


if (!isset($id_classe) and $_SESSION['statut'] != "responsable" AND $_SESSION['statut'] != "eleve") {
	// Le choix de la classe n'est pas encore fait et l'on n'est ni responsable, ni �l�ve

	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

    //$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
    //$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");

	if($_SESSION['statut'] == 'scolarite'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	//elseif(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiAccesReleveProf")=='yes')){
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes"){

		// C'est un prof et l'acc�s "a acc�s aux bulletins simples des �l�ves de toutes les classes" n'est pas donn�
		//$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";

		if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
			$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
		}
		elseif(getSettingValue("GepiAccesBulletinSimplePP") == "yes") {
			$sql="SELECT DISTINCT c.* FROM classes c,
											j_eleves_classes jec,
											j_eleves_professeurs jep
									WHERE jec.id_classe=c.id AND
											jep.login=jec.login AND
											jep.professeur='".$_SESSION['login']."'
									ORDER BY c.classe;";
		}
		else {
			tentative_intrusion(1, "Tentative d'acc�s aux bulletins simplifi�s sans autorisation.");
			echo "<p>Vous n'�tes pas autoris� � visualiser cette page.</p>";
			require "../lib/footer.inc.php";
			die();
		}
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") == "yes") {
		// C'est un prof et l'acc�s "a acc�s aux bulletins simples des �l�ves de toutes les classes" est donn�
		$sql="SELECT DISTINCT c.* FROM classes c  ORDER BY c.classe";
	}
	//elseif(($_SESSION['statut'] == 'cpe')&&(getSettingValue("GepiAccesReleveCpe")=='yes')){
	elseif($_SESSION['statut'] == 'cpe' OR $_SESSION['statut'] == 'autre'){
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe";
	}
	//echo "$sql<br />\n";
	$calldata = mysql_query($sql);
    $nombreligne = mysql_num_rows($calldata);
    echo " | Total : $nombreligne classes </p>\n";

	if($nombreligne==0){
		echo "<p>Aucune classe ne vous est attribu�e.<br />Contactez l'administrateur pour qu'il effectue le param�trage appropri� dans la Gestion des classes.</p>\n";
	}
	else{
		echo "<p>Cliquez sur la classe pour laquelle vous souhaitez extraire les bulletins</p>\n";
		//echo "<table border=0>\n";
		$nb_class_par_colonne=round($nombreligne/3);
			//echo "<table width='100%' border='1'>\n";
			echo "<table width='100%' summary='Choix de la classe'>\n";
			echo "<tr valign='top' align='center'>\n";
			echo "<td align='left'>\n";
		$i = 0;
		while ($i < $nombreligne){
			$id_classe = mysql_result($calldata, $i, "id");
			$classe_liste = mysql_result($calldata, $i, "classe");
			//echo "<tr><td><a href='index3.php?id_classe=$id_classe'>$classe_liste</a></td></tr>\n";
			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td align='left'>\n";
			}
			echo "<a href='index3.php?id_classe=$id_classe'>$classe_liste</a><br />\n";
			$i++;
		}
		echo "</table>\n";
	}
} else if ($_SESSION['statut'] == "responsable" AND $login_eleve == null) {
	// Si on est l�, c'est que le responsable est responsable de plusieurs �l�ves. Il doit donc
	// choisir celui pour lequel il souhaite visualiser le bulletin simplifi�

	echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

	$quels_eleves = mysql_query("SELECT e.login, e.nom, e.prenom " .
				"FROM eleves e, responsables2 re, resp_pers r WHERE (" .
				"e.ele_id = re.ele_id AND " .
				"re.pers_id = r.pers_id AND " .
				"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2'))");

	echo "<p>Cliquez sur le nom d'un ".$gepiSettings['denomination_eleve']." pour visualiser son bulletin simplifi� :</p>";
	while ($current_eleve = mysql_fetch_object($quels_eleves)) {
		echo "<p><a href='index3.php?login_eleve=".$current_eleve->login."'>".$current_eleve->prenom." ".$current_eleve->nom."</a></p>";
	}
} else if (!isset($choix_edit)) {
	// ====================
	// boireaus 20071207
	// Je ne saisis pas bien comment $choix_edit peut �tre affect� sans register_globals=on
	// Nulle part la variable n'a l'air r�cup�r�e en POST ou autre...
	// ====================

    if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
	    //echo " | <a href = \"index3.php\">Choisir une autre classe</a> ";

		echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

		echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

		// Ajout lien classe pr�c�dente / classe suivante
		if($_SESSION['statut']=='scolarite'){
			$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
		}
		elseif($_SESSION['statut']=='professeur'){

			//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";

			if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
				$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
			}
			elseif(getSettingValue("GepiAccesBulletinSimplePP") == "yes") {
				$sql="SELECT DISTINCT c.id,c.classe FROM classes c,
												j_eleves_classes jec,
												j_eleves_professeurs jep
										WHERE jec.id_classe=c.id AND
												jep.login=jec.login AND
												jep.professeur='".$_SESSION['login']."'
										ORDER BY c.classe;";
			}
			else {
				tentative_intrusion(1, "Tentative d'acc�s aux bulletins simplifi�s sans autorisation.");
				echo "<p>Vous n'�tes pas autoris� � visualiser cette page.</p>";
				require "../lib/footer.inc.php";
				die();
			}

		}
		elseif($_SESSION['statut']=='cpe'){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
				p.id_classe = c.id AND
				jec.id_classe=c.id AND
				jec.periode=p.num_periode AND
				jecpe.e_login=jec.login AND
				jecpe.cpe_login='".$_SESSION['login']."'
				ORDER BY classe";
		}
		elseif($_SESSION['statut'] == 'autre'){

			// On recherche toutes les classes pour ce statut qui n'est accessible que si l'admin a donn� les bons droits
			$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe";

		}
		//echo "$sql<br />\n";
		/*
		$res_class_tmp=mysql_query($sql);
		if(mysql_num_rows($res_class_tmp)>0){
			$id_class_prec=0;
			$id_class_suiv=0;
			$temoin_tmp=0;
			while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				if($lig_class_tmp->id==$id_classe){
					$temoin_tmp=1;
					if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
						$id_class_suiv=$lig_class_tmp->id;
					}
					else{
						$id_class_suiv=0;
					}
				}
				if($temoin_tmp==0){
					$id_class_prec=$lig_class_tmp->id;
				}
			}
			if(mysql_num_rows($res_class_tmp)>1){
				echo " | <a href = \"index3.php\">Choisir une autre classe</a> ";
			}
		}
		*/

		$chaine_options_classes="";

		$res_class_tmp=mysql_query($sql);
		if(mysql_num_rows($res_class_tmp)>0){
			$id_class_prec=0;
			$id_class_suiv=0;
			$temoin_tmp=0;
			while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				if($lig_class_tmp->id==$id_classe){
					$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
					$temoin_tmp=1;
					if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
						$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
						$id_class_suiv=$lig_class_tmp->id;
					}
					else{
						$id_class_suiv=0;
					}
				}
				else {
					$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				}
				if($temoin_tmp==0){
					$id_class_prec=$lig_class_tmp->id;
				}
			}
		}

		// =================================
		if(isset($id_class_prec)){
			if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Classe pr�c�dente</a>";}
		}

		if($chaine_options_classes!="") {
			echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
		}

		if(isset($id_class_suiv)){
			if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a></p>";}
		}
		//fin ajout lien classe pr�c�dente / classe suivante

		echo "</form>\n";


	    $classe_eleve = mysql_query("SELECT * FROM classes WHERE id='$id_classe'");
	    $nom_classe = mysql_result($classe_eleve, 0, "classe");
	    echo "<p class='grand'>Classe de $nom_classe</p>\n";
	    echo "<form enctype=\"multipart/form-data\" action=\"edit_limite.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
	    echo "<table summary='Choix des �l�ves'>\n";
		echo "<tr>\n";
	    echo "<td><input type=\"radio\" name=\"choix_edit\" id='choix_edit_1' value=\"1\" checked /></td>\n";
	    echo "<td><label for='choix_edit_1' style='cursor: pointer;'>Les bulletins simplifi�s de tous les ".$gepiSettings['denomination_eleves']." de la classe";
		if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesBulletinSimpleProfTousEleves") != "yes" AND getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes") {
			echo " (uniquement les ".$gepiSettings['denomination_eleves']." que j'ai en cours)";
		}
		echo "</label></td></tr>\n";

	    $call_suivi = mysql_query("SELECT DISTINCT professeur FROM j_eleves_professeurs WHERE id_classe='$id_classe' ORDER BY professeur");
	    $nb_lignes = mysql_num_rows($call_suivi);
	    $indice = 1;
	    if ($nb_lignes > 1) {
	        echo "<tr>\n";
	        echo "<td><input type=\"radio\" name=\"choix_edit\" id='choix_edit_3' value=\"3\" /></td>\n";
	        echo "<td><label for='choix_edit_3' style='cursor: pointer;'>Uniquement les bulletins simplifi�s des ".$gepiSettings['denomination_eleves']." dont le ".getSettingValue("gepi_prof_suivi")." est :</label>\n";
	        echo "<select size=\"1\" name=\"login_prof\" onclick=\"active(1)\">\n";
	        $i=0;
	        while ($i < $nb_lignes) {
	            $login_pr = mysql_result($call_suivi,$i,"professeur");
	            $call_prof = mysql_query("SELECT * FROM utilisateurs WHERE login='$login_pr'");
	            $nom_prof = mysql_result($call_prof,0,"nom");
	            $prenom_prof = mysql_result($call_prof,0,"prenom");
	            echo "<option value=".$login_pr.">".$nom_prof." ".$prenom_prof."</option>\n";
	            $i++;
	        }
	        echo "</select></td></tr>\n";
	        $indice = 2;
	    }


	    echo "<tr>\n";
	    echo "<td><input type=\"radio\" id='choix_edit_2' name=\"choix_edit\" value=\"2\" /></td>\n";
	    echo "<td><label for='choix_edit_2' style='cursor: pointer;'>Uniquement le bulletin simplifi� de l'".$gepiSettings['denomination_eleve']." s�lectionn� ci-contre : </label>\n";
	    echo "<select size=\"1\" name=\"login_eleve\" onclick=\"active(".$indice.")\">\n";

	    //if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
	    if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesBulletinSimpleProfTousEleves") != "yes" AND getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes") {
		    $sql="SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jeg.login AND " .
				"jeg.login = jec.login AND " .
				"jeg.id_groupe = jgp.id_groupe AND " .
				"jgp.login = '".$_SESSION['login']."') " .
				"ORDER BY e.nom,e.prenom";
	    } else {
		    $sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe = '$id_classe' and j.login=e.login) order by nom";
	    }
		//echo "$sql<br />\n";
		$call_eleve = mysql_query($sql);
	    $nombreligne = mysql_num_rows($call_eleve);
	    $i = "0" ;
	    while ($i < $nombreligne) {
	        $eleve = mysql_result($call_eleve, $i, 'login');
	        $nom_el = mysql_result($call_eleve, $i, 'nom');
	        $prenom_el = mysql_result($call_eleve, $i, 'prenom');
	        echo "<option value=$eleve>$nom_el  $prenom_el </option>\n";
	        $i++;
	    }
	    echo "</select></td></tr>\n";

		echo "<tr>\n";
	    echo "<td><input type=\"radio\" name=\"choix_edit\" id='choix_edit_4' value=\"4\" /></td>\n";
	    echo "<td><label for='choix_edit_4' style='cursor: pointer;'>Le bulletin simplifi� des appr�ciations sur le groupe-classe";
		echo "</label></td></tr>\n";

		echo "</table>\n";
    } else {
		echo "<p class=\"bold\"><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>";

    	$eleve = mysql_query("SELECT e.nom, e.prenom FROM eleves e WHERE e.login = '".$login_eleve."'");
    	$prenom_eleve = mysql_result($eleve, 0, "prenom");
    	$nom_eleve = mysql_result($eleve, 0, "nom");

	    echo "<p class='grand'>".ucfirst($gepiSettings['denomination_eleve'])." : ".$prenom_eleve." ".$nom_eleve."</p>\n";
	    echo "<form enctype=\"multipart/form-data\" action=\"edit_limite.php\" method=\"post\" name=\"form_choix_edit\" target=\"_blank\">\n";
	    echo "<input type=\"hidden\" name=\"choix_edit\" value=\"2\" />\n";
	    echo "<input type=\"hidden\" name=\"login_eleve\" value=\"".$login_eleve."\" />\n";
    }
    echo "<p>Choisissez la(les) p�riode(s) : </p><br />\n";
    include "../lib/periodes.inc.php";
    echo "De la p�riode : <select onchange=\"change_periode()\" size=1 name=\"periode1\">\n";
    $i = "1" ;
    while ($i < $nb_periode) {
       echo "<option value=$i>$nom_periode[$i] </option>\n";
       $i++;
    }
    echo "</select>\n";
    echo "&nbsp;� la p�riode : <select size=1 name=\"periode2\">\n";
    $i = "1" ;
    while ($i < $nb_periode) {
       echo "<option value=$i>$nom_periode[$i] </option>\n";
       $i++;
    }
    echo "</select>\n";
    echo "<input type=hidden name=id_classe value=$id_classe />\n";
    echo "<br /><br /><center><input type=submit value=Valider /></center>\n";
    echo "</form>\n";
}
require("../lib/footer.inc.php");
?>