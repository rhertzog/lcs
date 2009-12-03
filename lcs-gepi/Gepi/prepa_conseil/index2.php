<?php
/*
* $Id: index2.php 3077 2009-04-29 10:05:50Z crob $
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

$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

if (isset($id_classe)) {
	// On regarde si le type est correct :
	if (!is_numeric($id_classe)) {
		tentative_intrusion("2", "Changement de la valeur de id_classe pour un type non num�rique.");
		echo "Erreur.";
		require ("../lib/footer.inc.php");
		die();
	}
	// On teste si le professeur a le droit d'acc�der � cette classe
	if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
		$test = mysql_num_rows(mysql_query("SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
		if ($test == "0") {
			tentative_intrusion("2", "Tentative d'acc�s par un prof � une classe dans laquelle il n'enseigne pas, sans en avoir l'autorisation.");
			echo "Vous ne pouvez pas acc�der � cette classe car vous n'y �tes pas professeur !";
			require ("../lib/footer.inc.php");
			die();
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Visualisation des notes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<!--p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a-->
<?php
if (isset($id_classe)) {

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>";

	$current_eleve_classe = sql_query1("SELECT classe FROM classes WHERE id='$id_classe'");
	//echo " | <a href=\"index2.php\">Choisir une autre classe</a>";


	// ===========================================
	// Ajout lien classe pr�c�dente / classe suivante
	if($_SESSION['statut']=='scolarite'){
		$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";
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
		echo " | Classe : <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
	}
	if(isset($id_class_suiv)){
		if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a>";}
	}
	//fin ajout lien classe pr�c�dente / classe suivante
	// ===========================================
	//echo " | Classe : ".$current_eleve_classe."</p>\n";
	echo "</p>\n";
	echo "</form>\n";

	echo "<form target=\"_blank\" name=\"visu_toutes_notes\" method=\"post\" action=\"visu_toutes_notes.php\">\n";
	echo "<table border=\"1\" cellspacing=\"1\" cellpadding=\"10\" summary=\"Choix de la p�riode\"><tr>";
	echo "<td valign=\"top\"><strong>Choisissez&nbsp;la&nbsp;p�riode&nbsp;:&nbsp;</strong><br />\n";
	include "../lib/periodes.inc.php";
	$i="1";
	while ($i < $nb_periode) {
		echo "<br />\n<input type=\"radio\" name=\"num_periode\" id='num_periode_$i' value=\"$i\" ";
		if ($i == 1) echo "checked ";
		echo "/>&nbsp;";
		echo "<label for='num_periode_$i' style='cursor:pointer;'>\n";
		echo ucfirst($nom_periode[$i]);
		echo "</label>\n";
		$i++;
	}
	echo "<br />\n<input type=\"radio\" name=\"num_periode\" id='num_periode_annee' value=\"annee\" />&nbsp;";
	echo "<label for='num_periode_annee' style='cursor:pointer;'>\n";
	echo "Ann�e enti�re";
	echo "</label>\n";
	echo "</td>\n";

	echo "<td valign=\"top\">\n";
	echo "<strong>Param�tres d'affichage</strong><br />\n";
	echo "<input type=\"hidden\" name=\"id_classe\" value=\"".$id_classe."\" />";

	echo "<table border='0' width='100%' summary=\"Param�tres du tableau\">\n";
	echo "<tr>\n";
	echo "<td>\n";

		echo "<table border='0' summary=\"Param�tres\">\n";
		echo "<tr>\n";
		echo "<td>Largeur en pixel du tableau : </td>\n";
		echo "<td><input type=text name=larg_tab size=3 value=\"680\" /></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>Bords en pixel du tableau : </td>\n";
		echo "<td><input type=text name=bord size=3 value=\"1\" /></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "<label for='couleur_alterne' style='cursor:pointer;'>\n";
		echo "Couleurs de fond des lignes altern�es : \n";
		echo "</label>\n";
		echo "</td>\n";
		echo "<td><input type=\"checkbox\" name=\"couleur_alterne\" id=\"couleur_alterne\" checked /></td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "<td>\n";

		echo "<table border='0' summary=\"Affichages suppl�mentaires\">\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_abs\" id=\"aff_abs\" checked /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_abs' style='cursor:pointer;'>\n";
		echo "Afficher les absences";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_reg\" id=\"aff_reg\" checked /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_reg' style='cursor:pointer;'>\n";
		echo "Afficher le r�gime\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"aff_doub\" id=\"aff_doub\" checked /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_doub' style='cursor:pointer;'>\n";
		echo "Afficher la mention doublant\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."';");
		// On teste la pr�sence d'au moins un coeff pour afficher la colonne des coef
		$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

		if (($affiche_rang == 'y') and ($test_coef != 0)) {
			echo "<tr>\n";
			echo "<td><input type=\"checkbox\" name=\"aff_rang\" id=\"aff_rang\" checked /></td>\n";
			echo "<td>\n";
			echo "<label for='aff_rang' style='cursor:pointer;'>\n";
			echo "Afficher le rang des �l�ves\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

		echo "<tr>\n";
		echo "<td valign='top'><input type=\"checkbox\" name=\"aff_date_naiss\" id=\"aff_date_naiss\" /></td>\n";
		echo "<td>\n";
		echo "<label for='aff_date_naiss' style='cursor:pointer;'>\n";
		echo "Afficher la date de naissance des �l�ves\n";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

/*
	echo "<br />\nLargeur en pixel du tableau : <input type=text name=larg_tab size=3 value=\"680\" />";
	echo "<br />\nBords en pixel du tableau : <input type=text name=bord size=3 value=\"1\" />";
	echo "<br />\nCouleurs de fond des lignes altern�es : <input type=\"checkbox\" name=\"couleur_alterne\" checked />";
	echo "<br /><br /><table cellpadding=\"3\"><tr><td>\n<input type=\"checkbox\" name=\"aff_abs\" checked />Afficher les absences</td>
	<td><input type=\"checkbox\" name=\"aff_reg\" checked /> Afficher le r�gime</td>
	<td><input type=\"checkbox\" name=\"aff_doub\" checked />Afficher la mention doublant</td>";
	$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
	// On teste la pr�sence d'au moins un coeff pour afficher la colonne des coef
	$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));


	if (($affiche_rang == 'y') and ($test_coef != 0)) {
	echo "<td><input type=\"checkbox\" name=\"aff_rang\" checked />Afficher le rang des �l�ves</td>";
	}
	echo "</tr></table>";
*/
	echo "<br />\n<center><input type=\"submit\" name=\"ok\" value=\"Valider\" /></center>";
	echo "<br />\n<span class='small'>Remarque : le tableau des notes s'affiche sans en-t�te et dans une nouvelle page. Pour revenir � cet �cran, il vous suffit de fermer la fen�tre du tableau des notes.</span>";
	if ($_SESSION['statut'] == "professeur"
	AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"
	AND getSettingValue("GepiAccesMoyennesProfToutesTousEleves") != "yes") {
		echo "<br />\n<span class='small'>Si vous n'enseignez pas � des classes enti�res, seuls les �l�ves auxquels vous enseignez appara�tront dans la liste, et les moyennes calcul�s ne prendront en compte que les �l�ves affich�s.</span>";
	}
	echo "</td></tr>\n</table>\n";

	//$sql="SELECT DISTINCT jgc.id_groupe, g.name, g.description, jgc.coef FROM groupes g, j_groupes_classes jgc WHERE id_classe='$id_classe' AND g.id=jgc.id_groupe ORDER BY g.name;";
	$sql="SELECT DISTINCT jgc.id_groupe, g.name, g.description, jgc.coef, jgc.mode_moy FROM groupes g, j_groupes_classes jgc WHERE id_classe='$id_classe' AND g.id=jgc.id_groupe ORDER BY g.name;";
	//echo "$sql<br />";
	$res_coef_grp=mysql_query($sql);
	echo "<input type='checkbox' id='utiliser_coef_perso' name='utiliser_coef_perso' value='y' onchange=\"display_div_coef_perso()\" /><label for='utiliser_coef_perso'> Utiliser des coefficients personnalis�s.</label><br />\n";

	echo "<div id='div_coef_perso'>\n";
	echo "<table class='boireaus' summary='Coefficients personnalis�s'>\n";
	echo "<tr>\n";
	echo "<th>Identifiant</th>\n";
	echo "<th>Nom de l'enseignement</th>\n";
	echo "<th>Description de l'enseignement</th>\n";
	echo "<th>Coefficient</th>\n";
	echo "<th>Note&gt;10</th>\n";
	echo "</tr>\n";

	$alt=1;
	$num_id=1;
	while ($lig_cg=mysql_fetch_object($res_coef_grp)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td>$lig_cg->id_groupe</td>\n";
		echo "<td>".htmlentities($lig_cg->name)."</td>\n";
		echo "<td>".htmlentities($lig_cg->description)."</td>\n";
		echo "<td><input type='text' id=\"n".$num_id."\" onKeyDown=\"clavier(this.id,event);\" onfocus=\"javascript:this.select()\" name='coef_perso[$lig_cg->id_groupe]' value='$lig_cg->coef' size='3' autocomplete='off' /></td>\n";
		echo "<td><input type='checkbox' name='note_sup_10[$lig_cg->id_groupe]' value='y' ";
		if($lig_cg->mode_moy=='sup10') {echo "checked ";}
		echo "/></td>\n";
		echo "</tr>\n";
		$num_id++;
	}
	echo "</table>\n";

	echo "<p><i>Remarque:</i> Si des coefficients sp�cifiques ont �t� mis en place pour certains �l�ves (<i>voir en compte administrateur Gestion des bases/Gestion des classes/Enseignements/&lt;ENSEIGNEMENT&gt;/Eleves inscrits</i>), ils ne seront pas �cras�s par les valeurs saisies ici.</p>\n";
	echo "</div>\n";

	echo "<script type='text/javascript'>
function display_div_coef_perso() {
//alert('grrrr');
if(document.getElementById('utiliser_coef_perso').checked==true) {
document.getElementById('div_coef_perso').style.display='';
}
else {
document.getElementById('div_coef_perso').style.display='none';
}
}
display_div_coef_perso();
</script>\n";

	echo "</form>\n";
} else {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a>";

	//echo "</p><strong>Visualiser les notes par classe :</strong><br />";
	echo "</p>\n";
	echo "<strong>Visualiser les moyennes par classe :</strong><br />";
	//$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
	//$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	if($_SESSION['statut']=='scolarite'){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes"){
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe");
	}
	elseif($_SESSION['statut'] == 'professeur' and getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes") {
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c  ORDER BY c.classe");
	}
	elseif($_SESSION['statut']=='cpe'){
		$appel_donnees=mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
			p.id_classe = c.id AND
			jec.id_classe=c.id AND
			jec.periode=p.num_periode AND
			jecpe.e_login=jec.login AND
			jecpe.cpe_login='".$_SESSION['login']."'
			ORDER BY classe");
	}
	$lignes = mysql_num_rows($appel_donnees);

	if($lignes==0){
		echo "<p>Aucune classe ne vous est attribu�e.<br />Contactez l'administrateur pour qu'il effectue le param�trage appropri� dans la Gestion des classes.</p>\n";
	}
	else{
		$i = 0;
		$nb_class_par_colonne=round($lignes/3);
			//echo "<table width='100%' border='1'>\n";
			echo "<table width='100%' summary=\"Choix de la classe\">\n";
			echo "<tr valign='top' align='center'>\n";
			echo "<td align='left'>\n";
		while($i < $lignes){
		$id_classe = mysql_result($appel_donnees, $i, "id");
		$display_class = mysql_result($appel_donnees, $i, "classe");
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}
		echo "<a href='index2.php?id_classe=$id_classe'>".ucfirst($display_class)."</a><br />\n";
		$i++;
		}
		echo "</table>\n";
	}
}
require("../lib/footer.inc.php");
?>