<?php
/*
* $Id: saisie_appreciations.php 3798 2009-11-25 17:21:41Z crob $
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

// On indique qu'il faut creer des variables non prot�g�es (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


// Initialistion
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
} else {
	$current_group = false;

	// Pourquoi poursuivre si le groupe n'est pas trouv�?
	$mess=rawurlencode("Anomalie: Vous arrivez sur cette page sans que l'enseignement soit s�lectionn� ! Si vous aviez bien s�lectionn� un enseignement, il se peut que vous ayez un module php du type 'suhosin' qui limite le nombre de variables pouvant �tre post�es dans un formulaire.");
	header("Location: index.php?msg=$mess");
	die();
}

if (count($current_group["classes"]["list"]) > 1) {
	$multiclasses = true;
} else {
	$multiclasses = false;
}

$periode_cn = isset($_POST["periode_cn"]) ? $_POST["periode_cn"] :(isset($_GET["periode_cn"]) ? $_GET["periode_cn"] :NULL);
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

include "../lib/periodes.inc.php";


if ($_SESSION['statut'] != "secours") {
	if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
		$mess=rawurlencode("Vous n'�tes pas professeur de cet enseignement !");
		header("Location: index.php?msg=$mess");
		die();
	}
}

$msg="";

if (isset($_POST['is_posted'])) {

	$indice_max_log_eleve=$_POST['indice_max_log_eleve'];

	$k=1;
	while ($k < $nb_periode) {
		//=========================
		// AJOUT: boireaus 20071003
		unset($log_eleve);
		$log_eleve=isset($_POST['log_eleve_'.$k]) ? $_POST['log_eleve_'.$k] : NULL;
		//=========================

		//=================================================
		// AJOUT: boireaus 20080201
		if(isset($_POST['app_grp_'.$k])){
			//echo "\$current_group[\"classe\"][\"ver_periode\"]['all'][$k]=".$current_group["classe"]["ver_periode"]['all'][$k]."<br />";
			//if($current_group["classe"]["ver_periode"]['all'][$k]!=0){

			//if(($current_group["classe"]["ver_periode"]['all'][$k]!=0)&&($current_group["classe"]["ver_periode"]['all'][$k]!=1)) {
			if(($current_group["classe"]["ver_periode"]['all'][$k]>=2)||
				(($current_group["classe"]["ver_periode"]['all'][$k]!=0)&&($_SESSION['statut']=='secours'))) {

				if (isset($NON_PROTECT["app_grp_".$k])){
					$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["app_grp_".$k]));
				}
				else{
					$app = "";
				}
				//echo "$k: $app<br />";
				// Contr�le des saisies pour supprimer les sauts de lignes surnum�raires.
				$app=my_ereg_replace('(\\\r\\\n)+',"\r\n",$app);

				$test_grp_app_query = mysql_query("SELECT * FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
				$test = mysql_num_rows($test_grp_app_query);
				if ($test != "0") {
					if ($app != "") {
						$register = mysql_query("UPDATE matieres_appreciations_grp SET appreciation='" . $app . "' WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
					} else {
						$register = mysql_query("DELETE FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
					}
					if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des donn�es de la p�riode $k pour le groupe/classe<br />";}

				} else {
					if ($app != "") {
						$register = mysql_query("INSERT INTO matieres_appreciations_grp SET id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
						if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des donn�es de la p�riode $k pour le groupe/classe<br />";}
					}
				}
			}
			else {
				$msg.="Anomalie: Tentative de saisie d'une appr�ciation de classe alors que la p�riode n'est pas ouverte en saisie.<br />";
			}
		}
		//=================================================

		if(isset($log_eleve)){
			//for($i=0;$i<count($log_eleve);$i++){
			for($i=0;$i<$indice_max_log_eleve;$i++){

				//echo "\$log_eleve[$i]=$log_eleve[$i]<br />\n";
				if(isset($log_eleve[$i])) {
					// On supprime le suffixe indiquant la p�riode:
					$reg_eleve_login=my_ereg_replace("_t".$k."$","",$log_eleve[$i]);

					//echo "\$i=$i<br />";
					//echo "\$reg_eleve_login=$reg_eleve_login<br />";

					// La p�riode est-elle ouverte?
					if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
						$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
						//if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N"){
						if(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="N")||
							(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]!="O")&&($_SESSION['statut']=='secours'))) {

							$nom_log = "app_eleve_".$k."_".$i;

							//echo "\$nom_log=$nom_log<br />";

							if (isset($NON_PROTECT[$nom_log])){
								$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
							}
							else{
								$app = "";
							}

							//echo "\$app=$app<br />";

							// Contr�le des saisies pour supprimer les sauts de lignes surnum�raires.
							$app=my_ereg_replace('(\\\r\\\n)+',"\r\n",$app);

							$test_eleve_app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
							$test = mysql_num_rows($test_eleve_app_query);
							if ($test != "0") {
								if ($app != "") {
									$register = mysql_query("UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
								} else {
									$register = mysql_query("DELETE FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
								}
								if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des donn�es de la p�riode $k pour l'�l�ve $reg_eleve_login<br />";}

							} else {
								if ($app != "") {
									$register = mysql_query("INSERT INTO matieres_appreciations SET login='$reg_eleve_login',id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
									if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des donn�es de la p�riode $k pour l'�l�ve $reg_eleve_login<br />";}
								}
							}
						}
					}
				}
			}
		}
		$k++;
	}

	if($msg=='') {
		// A partir de l�, toutes les appr�ciations ont �t� sauvegard�es proprement, on vide la table tempo
		$effacer = mysql_query("DELETE FROM matieres_appreciations_tempo WHERE id_groupe = '".$id_groupe."'")
		OR die('Erreur dans l\'effacement de la table temporaire (1) :'.mysql_error());
	}

	/*
	foreach ($current_group["eleves"]["all"]["list"] as $reg_eleve_login) {
		$k=1;
		while ($k < $nb_periode) {
			if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
					$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
					if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N"){
						$nom_log = $reg_eleve_login."_t".$k;
						if (isset($NON_PROTECT[$nom_log]))
						$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
						else
						$app = "";

						// Contr�le des saisies pour supprimer les sauts de lignes surnum�raire.
						$app=my_ereg_replace('(\\\r\\\n)+',"\r\n",$app);


						$test_eleve_app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
						$test = mysql_num_rows($test_eleve_app_query);
						if ($test != "0") {
							if ($app != "") {
								$register = mysql_query("UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
							} else {
								$register = mysql_query("DELETE FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
							}
							if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des donn�es de la p�riode $k pour l'�l�ve $reg_eleve_login<br />";}

						} else {
							if ($app != "") {
								$register = mysql_query("INSERT INTO matieres_appreciations SET login='$reg_eleve_login',id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
								if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des donn�es de la p�riode $k pour l'�l�ve $reg_eleve_login<br />";}
							}
						}
					}
			}
			$k++;
		}
	}
	*/

	if($msg=="") {
		$affiche_message = 'yes';
	}
}

if (!isset($periode_cn)) $periode_cn = 0;

$themessage = 'Des appr�ciations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont �t� enregistr�es !";
$utilisation_prototype = "ok";
$javascript_specifique = "saisie/scripts/js_saisie";
//**************** EN-TETE *****************
$titre_page = "Saisie des appr�ciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

?>
<script type="text/javascript" language="javascript">
change = 'no';

</script>
<?php



$matiere_nom = $current_group["matiere"]["nom_complet"];

echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form1' method=\"post\">\n";

echo "<p class='bold'>\n";
//if ($periode_cn != 0) {
if (($periode_cn != 0)&&($_SESSION['statut']!='secours')) {
	//echo "|<a href=\"../cahier_notes/index.php?id_groupe=$id_groupe&periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour</a>";
	echo "<a href=\"../cahier_notes/index.php?id_groupe=$id_groupe&amp;periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
} else {
	echo "<a href=\"index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>\n";
}
//echo "|<a href='saisie_notes.php?id_groupe=$id_groupe&periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les moyennes</a>";
echo " | <a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les moyennes</a>";
// enregistrement du chemin de retour pour la fonction imprimer
//$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?". $_SERVER['QUERY_STRING'];
if($_SERVER['QUERY_STRING']!='') {
	$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?". $_SERVER['QUERY_STRING'];
}
else {
	$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?id_groupe=$id_groupe";
}
echo " | <a href='../prepa_conseil/index1.php?id_groupe=$id_groupe'>Imprimer</a>\n";

//=========================
// AJOUT: boireaus 20071108
echo " | <a href='index.php?id_groupe=" . $current_group["id"] . "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Import/Export notes et appr�ciations</a>";
//=========================

/*
// =================================
// AJOUT: boireaus
// Pour proposer de passer � la classe suivante ou � la pr�c�dente
//$sql="SELECT id, classe FROM classes ORDER BY classe";
if($_SESSION['statut']=='secours'){
	$sql = "SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
}
else
*/
//if($_SESSION['statut']=='professeur'){
if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
	//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";

	if($_SESSION['statut']=='professeur') {
		$login_prof_groupe_courant=$_SESSION["login"];
	}
	else {
		$tmp_current_group=get_group($id_groupe);

		$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
	}

	//$tab_groups = get_groups_for_prof($_SESSION["login"],"classe puis mati�re");
	$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis mati�re");
	//$tab_groups = get_groups_for_prof($_SESSION["login"]);

	if(!empty($tab_groups)) {

		// Pour s'assurer de ne pas avoir deux fois le m�me groupe...
		$tmp_group=array();

		$chaine_options_classes="";

		$num_groupe=-1;
		$nb_groupes_suivies=count($tab_groups);

		//echo "count(\$tab_groups)=".count($tab_groups)."<br />";

		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		//foreach($tab_groups as $tmp_group) {
		for($loop=0;$loop<count($tab_groups);$loop++) {
			/*
			echo "\$tab_groups[$loop]['id']=".$tab_groups[$loop]['id']."<br />";
			echo "\$tab_groups[$loop]['name']=".$tab_groups[$loop]['name']."<br />";
			echo "\$tab_groups[$loop]['classlist_string']=".$tab_groups[$loop]['classlist_string']."<br />";
			*/
			// Pour s'assurer de ne pas avoir deux fois le m�me groupe...
			if(!in_array($tab_groups[$loop]['id'],$tmp_group)) {
				$tmp_group[]=$tab_groups[$loop]['id'];

				if($tab_groups[$loop]['id']==$id_groupe){
					$num_groupe=$loop;

					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";

					$temoin_tmp=1;
					if(isset($tab_groups[$loop+1])){
						$id_grp_suiv=$tab_groups[$loop+1]['id'];

						//$chaine_options_classes.="<option value='".$tab_groups[$loop+1]['id']."'>".$tab_groups[$loop+1]['name']." (".$tab_groups[$loop+1]['classlist_string'].")</option>\n";
					}
					else{
						$id_grp_suiv=0;
					}
				}
				else {
					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
				}

				if($temoin_tmp==0){
					$id_grp_prec=$tab_groups[$loop]['id'];

					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
				}
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_cn=$periode_cn";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement pr�c�dent</a>";
			}
		}

		if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {

			echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";

			//echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
			echo " | <select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
		}

		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_cn=$periode_cn";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement suivant</a>";
				}
		}
	}
	// =================================
}


echo "</p>\n";
if(isset($periode_cn)) {
	echo "<input type='hidden' name='periode_cn' value='$periode_cn' />\n";
}
echo "</form>\n";

echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" method=\"post\">\n";

//=========================
// AJOUT: boireaus 20090126
$insert_mass_appreciation_type=getSettingValue("insert_mass_appreciation_type");
if ($insert_mass_appreciation_type=="y") {
	// INSERT INTO setting SET name='insert_mass_appreciation_type', value='y';

	$sql="CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '');";
	$create_table=mysql_query($sql);

	// Pour tester:
	// INSERT INTO b_droits_divers SET login='toto', nom_droit='insert_mass_appreciation_type', valeur_droit='y';

	$sql="SELECT 1=1 FROM b_droits_divers WHERE login='".$_SESSION['login']."' AND nom_droit='insert_mass_appreciation_type' AND valeur_droit='y';";
	$res_droit=mysql_query($sql);
	if(mysql_num_rows($res_droit)>0) {
		$droit_insert_mass_appreciation_type="y";
	}
	else {
		$droit_insert_mass_appreciation_type="n";
	}

	if($droit_insert_mass_appreciation_type=="y") {
		echo "<div style='float:right; width:150px; border: 1px solid black; background-color: white; font-size: small; text-align:center;'>\n";
		echo "Ins�rer l'appr�ciation-type suivante pour toutes les appr�ciations vides: ";
		echo "<input type='text' name='ajout_a_textarea_vide' id='ajout_a_textarea_vide' value='-' size='10' /><br />\n";
		echo "<input type='button' name='ajouter_a_textarea_vide' value='Ajouter' onclick='ajoute_a_textarea_vide()' /><br />\n";
		echo "</div>\n";
	}
}
//=========================

echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";



echo "<h2 class='gepi'>Bulletin scolaire - Saisie des appr�ciations</h2>\n";
//echo "<p><b>Groupe : " . $current_group["description"] ." | Mati�re : $matiere_nom</b></p>\n";
echo "<p><b>Groupe : " . htmlentities($current_group["description"]) ." (".$current_group["classlist_string"].")</b></p>\n";

if ($multiclasses) {
	echo "<p>Affichage :";
	echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&amp;order_by=classe'>Regrouper les �l�ves par classe</a>";
	echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&amp;order_by=nom'>Afficher la liste par ordre alphab�tique</a>";
	echo "</p>\n";
}

// On commence par mettre la liste dans l'ordre souhait�
if ($order_by != "classe") {
	$liste_eleves = $current_group["eleves"]["all"]["list"];
} else {
	// Ici, on tri par classe
	// On va juste cr�er une liste des �l�ves pour chaque classe
	$tab_classes = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$tab_classes[$classe_id] = array();
	}
	// On passe maintenant �l�ve par �l�ve et on les met dans la bonne liste selon leur classe
	foreach($current_group["eleves"]["all"]["list"] as $eleve_login) {
		$classe = $current_group["eleves"]["all"]["users"][$eleve_login]["classe"];
		$tab_classes[$classe][] = $eleve_login;
	}
	// On met tout �a � la suite
	$liste_eleves = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
	}
}


// Fonction de renseignement du champ qui doit obtenir le focus apr�s validation
echo "<script type='text/javascript'>

function focus_suivant(num){
	temoin='';
	// La variable 'dernier' peut d�passer de l'effectif de la classe... mais cela n'est pas dramatique
	dernier=num+".count($liste_eleves)."
	// On parcourt les champs � partir de celui de l'�l�ve en cours jusqu'� rencontrer un champ existant
	// (pour r�ussir � passer un �l�ve qui ne serait plus dans la p�riode)
	// Apr�s validation, c'est ce champ qui obtiendra le focus si on n'�tait pas � la fin de la liste.
	for(i=num;i<dernier;i++){
		suivant=i+1;
		if(temoin==''){
			if(document.getElementById('n'+suivant)){
				document.getElementById('info_focus').value=suivant;
				temoin=suivant;
			}
		}
	}

	document.getElementById('info_focus').value=temoin;
}

</script>\n";

	// ====================== Modif pour la sauvegarde en ajax =================
$restauration = isset($_GET["restauration"]) ? $_GET["restauration"] : NULL;
	// On teste s'il existe des donn�es dans la table matieres_appreciations_tempo
	$sql_test = mysql_query("SELECT login FROM matieres_appreciations_tempo WHERE id_groupe = '" . $current_group["id"] . "'");
	$test = mysql_num_rows($sql_test);
		if ($test !== 0 AND $restauration == NULL) {
			// On envoie un message � l'utilisateur
			echo "
			<p class=\"red\">Certaines appr�ciations n'ont pas �t� enregistr�es lors de votre derni�re saisie.<br />
				Elles sont indiqu�es ci-dessous en rouge. Voulez-vous les restaurer ?
			</p>
			<p class=\"red\">
			<a href=\"./saisie_appreciations.php?id_groupe=".$current_group["id"]."&amp;restauration=oui\">OUI</a>
			(elles remplaceront alors la saisie pr�c�dente)
			 -
			<a href=\"./saisie_appreciations.php?id_groupe=".$current_group["id"]."&amp;restauration=non\">NON</a>
			(elles seront alors d�finitivement perdues)
			</p>
			";
		}

	// Dans tous les cas, si $restauration n'est pas NULL, il faut vider la table tempo des appr�ciations de ce groupe

//=================================
// AJOUT: boireaus 20080201
$k=1;
$num_id = 10;
while ($k < $nb_periode) {

	$app_query = mysql_query("SELECT * FROM matieres_appreciations_grp WHERE (id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
	$app_grp[$k] = @mysql_result($app_query, 0, "appreciation");

	$call_moyenne_t[$k] = mysql_query("SELECT round(avg(n.note),1) moyenne FROM matieres_notes n, j_eleves_groupes j " .
								"WHERE (" .
								"n.id_groupe='" . $current_group["id"] ."' AND " .
								"n.login = j.login AND " .
								"n.statut='' AND " .
								"j.id_groupe = n.id_groupe AND " .
								"n.periode='$k' AND j.periode='$k'" .
								")");
	$moyenne_t[$k] = mysql_result($call_moyenne_t[$k], 0, "moyenne");

	if ($moyenne_t[$k]=='') {
		$moyenne_t[$k]="&nbsp;";
	}

	$mess[$k]="";
	$mess[$k].="<td>".$moyenne_t[$k]."</td>\n";
	$mess[$k].="<td>\n";
	//if ($current_group["classe"]["ver_periode"]['all'][$k] == 0){
	//if(($current_group["classe"]["ver_periode"]['all'][$k] == 0)||($current_group["classe"]["ver_periode"]['all'][$k] == 1)) {
	if(((($current_group["classe"]["ver_periode"]['all'][$k] == 0)||($current_group["classe"]["ver_periode"]['all'][$k] == 1))&&($_SESSION['statut']!='secours'))||
	(($current_group["classe"]["ver_periode"]['all'][$k]==0)&&($_SESSION['statut']=='secours'))) {

		//$mess[$k].=htmlentities(nl2br($app_grp[$k]));
		$mess[$k].=nl2br($app_grp[$k]);
	}
	else {
		if(!isset($id_premier_textarea)) {$id_premier_textarea=$k.$num_id;}

		$mess[$k].="<input type='hidden' name='app_grp_".$k."' value=\"".$app_grp[$k]."\" />\n";
		//$mess[$k].="<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\"";
		//$mess[$k].="<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='100' style='white-space: nowrap;' onchange=\"changement()\"";
		$mess[$k].="<textarea id=\"n".$k.$num_id."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='100' onchange=\"changement()\"";
		// onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"

		$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");document.getElementById('focus_courant').value='".$k.$num_id."';\"";

		$mess[$k].=">".$app_grp[$k]."</textarea>\n";
	}

	// on affiche si besoin l'appr�ciation temporaire (en sauvegarde)
	//$mess[$k].=$eleve_app_t;
	$mess[$k].= "</td>\n";
	$k++;
}


echo "<table width=\"750\" class='boireaus' cellspacing=\"2\" cellpadding=\"5\" summary=\"Appr�ciation sur le groupe/classe\">\n";
echo "<tr>\n";
echo "<th width=\"200\"><div align=\"center\">&nbsp;</div></th>\n";
echo "<th width=\"30\"><div align=\"center\"><b>Moy.</b></div></th>\n";
echo "<th><div align=\"center\"><b>Appr�ciation sur le groupe/classe</b>\n";
echo "</div></th>\n";
echo "</tr>\n";
//=================================================


$num_id++;
$k=1;
$alt=1;
while ($k < $nb_periode) {
	$alt=$alt*(-1);
	if ($current_group["classe"]["ver_periode"]["all"][$k] == 0) {
		echo "<tr class='lig$alt'><td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span></td>\n";
	} else {
		echo "<tr class='lig$alt'><td>$nom_periode[$k]</td>\n";
	}
	echo $mess[$k];
	$k++;
}
echo "</tr>\n";
echo "</table>\n";
echo "<br />\n";


//=================================



$prev_classe = null;
//=================================================
// COMMENT�: boireaus 20080201
//$num_id = 10;
//=================================================
//=========================
// AJOUT: boireaus 20071010
// Compteur pour les �l�ves
$i=0;
//=========================
// Pour permettre le remplacement de la chaine _PRENOM_ par le pr�nom de l'�l�ve dans les commentaires types (ctp.php)
$chaine_champs_input_prenom="";
//=========================
foreach ($liste_eleves as $eleve_login) {

	$k=1;
	$temoin_photo="";

	while ($k < $nb_periode) {

		if (in_array($eleve_login, $current_group["eleves"][$k]["list"])) {
			//
			// si l'�l�ve appartient au groupe pour cette p�riode
			//
			$eleve_nom = $current_group["eleves"][$k]["users"][$eleve_login]["nom"];
			$eleve_prenom = $current_group["eleves"][$k]["users"][$eleve_login]["prenom"];
			$eleve_classe = $current_group["classes"]["classes"][$current_group["eleves"]["all"]["users"][$eleve_login]["classe"]]["classe"];
			$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$eleve_login]["classe"]]["id"];

			//========================
			// AJOUT boireaus 20071115
			if($k==1){
				$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login';";
				$res_ele=mysql_query($sql);
				$lig_ele=mysql_fetch_object($res_ele);
				$eleve_elenoet=$lig_ele->elenoet;

				// Photo...
				$photo=nom_photo($eleve_elenoet);
				//$temoin_photo="";
				if("$photo"!=""){
					$titre="$eleve_nom $eleve_prenom";

					$texte="<div align='center'>\n";
					$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$eleve_nom $eleve_prenom\" />";
					$texte.="<br />\n";
					$texte.="</div>\n";

					$temoin_photo="y";

					$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');
				}
			}
			//========================

			$suit_option[$k] = 'yes';
			//
			// si l'�l�ve suit la mati�re
			//
			if ($restauration != "oui" AND $restauration != "non") {
				// On r�cup�re l'appr�ciation tempo pour la rajouter � $eleve_app
				$app_t_query = mysql_query("SELECT * FROM matieres_appreciations_tempo WHERE
					(login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				$verif_t = mysql_num_rows($app_t_query);
				if ($verif_t != 0) {
					$eleve_app_t = "\n".'<p>Appr�ciation non enregistr�e : <span style="color: red;">'.@mysql_result($app_t_query, 0, "appreciation").'</span></p>';
				}else{
					$eleve_app_t = '';
				}
			}else{
				$eleve_app_t = '';
			}

			// Appel des appr�ciations (en v�rifiant si une restauration est demand�e ou non)
			if ($restauration == "oui") {
				$app_query = mysql_query("SELECT * FROM matieres_appreciations_tempo WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				// Si la sauvegarde ne donne rien pour cet �l�ve, on va quand m�me voir dans la table d�finitive
				$verif = mysql_num_rows($app_query);
				if ($verif == 0){
					$app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				}
			} else {
				$app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
			}
			$eleve_app = @mysql_result($app_query, 0, "appreciation");
			// Appel des notes
			$note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
			$eleve_statut = @mysql_result($note_query, 0, "statut");
			$eleve_note = @mysql_result($note_query, 0, "note");
			// Formatage de la note
			$note ="<center>";
			if ($eleve_statut != '') {
				$note .= $eleve_statut;
			} else {
				if ($eleve_note != '') {
					$note .= $eleve_note;
				} else {
					$note .= "&nbsp;";
				}
			}
			$note .= "</center>";

			$eleve_login_t[$k] = $eleve_login."_t".$k;
			//if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] != "N") {
			if ((($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]!="N")&&($_SESSION['statut']!='secours'))||
				(($current_group["classe"]["ver_periode"][$eleve_id_classe][$k]=="O")&&($_SESSION['statut']=='secours'))) {

				//
				// si la p�riode est verrouill�e
				//
				$mess[$k] = '';
				$mess[$k] =$mess[$k]."<td>".$note."</td>\n<td>";
				if ($eleve_app != '') {
					//$mess[$k] =$mess[$k].$eleve_app;
					if((strstr($eleve_app,">"))||(strstr($eleve_app,"<"))){
						$mess[$k] =$mess[$k].$eleve_app;
					}
					else{
						$mess[$k] =$mess[$k].nl2br($eleve_app);
					}
				} else {
					$mess[$k] =$mess[$k]."&nbsp;";
				}
				$mess[$k] =$mess[$k]."</td>\n";
			} else {

				// Ajout Eric affichage des notes au dessus de la saisie des appr�ciations
				$liste_notes ='';
				// Nombre de contr�les
				$sql="SELECT cnd.note, cd.note_sur FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$eleve_login."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$k' AND cnd.statut='';";
				//echo "\n<!--sql=$sql-->\n";
				$result_nbct=mysql_query($sql);
				$current_eleve_nbct=mysql_num_rows($result_nbct);

				// on prend les notes dans $string_notes
				$liste_notes='';
				if ($result_nbct ) {
					while ($snnote =  mysql_fetch_assoc($result_nbct)) {
						if ($liste_notes != '') $liste_notes .= ", ";
						$liste_notes .= $snnote['note'];
						if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
							$liste_notes .= "/".$snnote['note_sur'];
						}
					}
				}

				if ($current_eleve_nbct ==0) {
					$liste_notes='Pas de note dans le carnet pour cette p�riode.';
				}

				//$mess[$k] = "<td>".$note."</td>\n<td><textarea id=\"".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows=2 cols=100 wrap='virtual' onchange=\"changement()\">".$eleve_app."</textarea></td>\n";

				//$mess[$k] = "<td>".$note."</td>\n<td>Contenu du carnet de notes : ".$liste_notes."<br /><textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\">".$eleve_app."</textarea></td>\n";

				//$mess[$k] = "<td>".$note."</td>\n<td>Contenu du carnet de notes : ".$liste_notes."<br /><textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\" onfocus=\"document.getElementById('info_focus').value='n".$k.$num_id."'\">".$eleve_app."</textarea></td>\n";

				//=========================
				// MODIF: boireaus 20071003

				//$mess[$k] = "<td>".$note."</td>\n<td>Contenu du carnet de notes : ".$liste_notes."<br /><textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\" onfocus=\"focus_suivant(".$k.$num_id.");\">".$eleve_app."</textarea></td>\n";

				$mess[$k]="<td>".$note."</td>\n";
				$mess[$k].="<td>Contenu du carnet de notes : ".$liste_notes."<br />\n";
				$mess[$k].="<input type='hidden' name='log_eleve_".$k."[$i]' value=\"".$eleve_login_t[$k]."\" />\n";

				//Supprim� le 07/11/2009:
				//$mess[$k].="<input type='hidden' name='prenom_eleve_".$k."[$i]' id='prenom_eleve_".$k.$num_id."' value=\"".$eleve_prenom."\" />\n";
				$chaine_champs_input_prenom.="<input type='hidden' name='prenom_eleve_".$k."[$i]' id='prenom_eleve_".$k.$num_id."' value=\"".$eleve_prenom."\" />\n";

				//$mess[$k].="<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$i."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\" onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"";
				//$mess[$k].="<textarea id=\"n".$k.$num_id."\" style='white-space: nowrap;' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$i."\" rows='2' cols='100' onchange=\"changement()\" onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"";
				$mess[$k].="<textarea id=\"n".$k.$num_id."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$i."\" rows='2' cols='100' onchange=\"changement()\" onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"";

				//==================================
				// R�tablissement: boireaus 20080219
				// Pour revenir au champ suivant apr�s validation/enregistrement:
				// MODIF: boireaus 20080520
				//$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");\"";
				$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");document.getElementById('focus_courant').value='".$k.$num_id."';\"";
				//==================================

				$mess[$k].=">".$eleve_app."</textarea>\n";
				// on affiche si besoin l'appr�ciation temporaire (en sauvegarde)
				$mess[$k].=$eleve_app_t;
				$mess[$k].= "</td>\n";

				//=========================

			}
		}
		else {
			//
			// si l'�l�ve n'appartient pas au groupe pour cette p�riode.
			//
			$suit_option[$k] = 'no';
			$mess[$k] = "<td>&nbsp;</td><td><p class='small'>non suivie</p></td>\n";
		}
		$k++;
	}
	//
	//Affichage de la ligne
	//
	$display_eleve='no';
	$k=1;
	while ($k < $nb_periode) {
		if ($suit_option[$k] != 'no') {$display_eleve='yes';}
		$k++;
	}
	if ($display_eleve=='yes') {

		if ($multiclasses && $prev_classe != $eleve_classe && $order_by == 'classe') {
			if ($prev_classe != null) {
				echo "<hr style='width: 95%;'/>\n";
			}
			echo "<h3>Classe de " . $eleve_classe . "</h3>\n";
		}
		$prev_classe = $eleve_classe;
		//echo "<table width=\"750px\" border=\"1\" cellspacing=\"2\" cellpadding=\"5\">\n";
		/*
		echo "<table width=\"750\" border=\"1\" cellspacing=\"2\" cellpadding=\"5\">\n";
		echo "<tr>\n";
		echo "<td width=\"200\"><div align=\"center\">&nbsp;</div></td>\n";
		echo "<td width=\"30\"><div align=\"center\"><b>Moy.</b></div></td>\n";
		echo "<td><div align=\"center\"><b>$eleve_nom $eleve_prenom</b></div></td>\n";
		echo "</tr>\n";
		*/

		echo "<table width=\"750\" class='boireaus' cellspacing=\"2\" cellpadding=\"5\" summary=\"Tableau de $eleve_nom $eleve_prenom\">\n";
		echo "<tr>\n";
		echo "<th width=\"200\"><div align=\"center\">&nbsp;</div></th>\n";
		echo "<th width=\"30\"><div align=\"center\"><b>Moy.</b></div></th>\n";
		echo "<th><div align=\"center\"><b>$eleve_nom $eleve_prenom</b>\n";

		//==========================
		// AJOUT: boireaus 20071115
		// Lien photo...
		if($temoin_photo=="y"){
			//echo " <a href='#' onmouseover=\"afficher_div('photo_$eleve_login','y',-100,20);\"";
			echo " <a href='#' onmouseover=\"delais_afficher_div('photo_$eleve_login','y',-100,20,1000,10,10);\"";
			echo ">";
			echo "<img src='../images/icons/buddy.png' alt='$eleve_nom $eleve_prenom' />";
			echo "</a>";
		}
		//==========================

		/*

			$titre=$v_eleve_nom_prenom1;
			$texte="<div align='center'>\n";
			$photo=nom_photo($v_elenoet1);
			if("$photo"!=""){
				$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$v_eleve_nom_prenom1\" />";
				$texte.="<br />\n";
			}
			$texte.="</div>\n";

			$tabdiv_infobulle[]=creer_div_infobulle('info_popup_eleve1',$titre,"",$texte,"",14,0,'y','y','n','n')
		*/

		echo "</div></th>\n";
		echo "</tr>\n";


		$num_id++;
		$k=1;
		$alt=1;
		while ($k < $nb_periode) {
			$alt=$alt*(-1);
			if ($current_group["classe"]["ver_periode"]["all"][$k] == 0) {
				echo "<tr class='lig$alt'><td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span></td>\n";
			} else {
				echo "<tr class='lig$alt'><td>$nom_periode[$k]</td>\n";
			}
			echo $mess[$k];
			$k++;
		}
		echo "</tr>\n";
		//echo"</table>\n<p></p>";
		echo "</table>\n";
		//echo"<p>&nbsp;</p>\n";
		//echo"<p></p>\n";
		echo "<br />\n";
	}
	$i++;

}

echo "<input type='hidden' name='indice_max_log_eleve' value='$i' />\n";
?>
<input type="hidden" name="is_posted" value="yes" />
<input type="hidden" name="id_groupe" value="<?php echo "$id_groupe";?>" />
<input type="hidden" name="periode_cn" value="<?php echo "$periode_cn";?>" />
<center><div id="fixe"><input type="submit" value="Enregistrer" /><br />

<!-- DIV destin� � afficher un d�compte du temps restant pour ne pas se faire pi�ger par la fin de session -->
<div id='decompte'></div>

<?php
	//============================================
	// AJOUT: boireaus 20080520
	// Dispositif sp�cifique: d�commenter la ligne pour l'activer
	if(getSettingValue('appreciations_types_profs')=='y') {include('ctp.php');}
	//============================================
?>

<!-- Champ destin� � recevoir la valeur du champ suivant celui qui a le focus pour redonner le focus � ce champ apr�s une validation -->
<input type='hidden' id='info_focus' name='champ_info_focus' value='' />
<input type='hidden' id='focus_courant' name='focus_courant' value='' />
</div></center>
</form>

<?php

// ====================== DISPOSITIF CTP ========================
// Pour permettre le remplacement de la chaine _PRENOM_ par le pr�nom de l'�l�ve dans les commentaires types (ctp.php)
// Les champs INPUT des pr�noms sont ins�r�s hors du formulaire principal pour �viter d'envoyer trop de champs lors du submit (probl�mes avec suhosin qui limite le nombre de champs pouvant �tre POST�s)
echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" name='form2' method=\"post\">\n";
echo $chaine_champs_input_prenom;
echo "</form>\n";
// ==============================================================

// ====================== SYSTEME  DE SAUVEGARDE ========================
// Dans tous les cas, suite � une demande de restauration, et quelle que soit la r�ponse, les sauvegardes doivent �tre effac�es
if ($restauration == "oui" OR $restauration == "non") {
	$effacer = mysql_query("DELETE FROM matieres_appreciations_tempo WHERE id_groupe = '".$id_groupe."'")
	OR DIE('Erreur dans l\'effacement de la table temporaire (2) : '.mysql_error());
}
// Il faudra permettre de n'afficher ce d�compte que si l'administrateur le souhaite.

echo "<script type='text/javascript'>
cpt=".$tmp_timeout.";
compte_a_rebours='y';

function decompte(cpt){
	if(compte_a_rebours=='y'){
		document.getElementById('decompte').innerHTML=cpt;
		if(cpt>0){
			cpt--;
		}

		setTimeout(\"decompte(\"+cpt+\")\",1000);
	}
	else{
		document.getElementById('decompte').style.display='none';
	}
}

decompte(cpt);

";

// Apr�s validation, on donne le focus au champ qui suivait celui qui vient d'�tre rempli
if(isset($_POST['champ_info_focus'])){
	if($_POST['champ_info_focus']!=""){
		echo "// On positionne le focus...
	document.getElementById('n".$_POST['champ_info_focus']."').focus();
\n";
	}
}
elseif(isset($id_premier_textarea)) {
	echo "if(document.getElementById('n".$id_premier_textarea."')) {document.getElementById('n".$id_premier_textarea."').focus();}
if(document.getElementById('focus_courant')) {document.getElementById('focus_courant').value='$id_premier_textarea';}";
}

echo "</script>\n";

//=========================
// AJOUT: boireaus 20090126
if (($insert_mass_appreciation_type=="y")&&($droit_insert_mass_appreciation_type=="y")) {
	echo "<script type='text/javascript'>
	function ajoute_a_textarea_vide() {
		champs_textarea=document.getElementsByTagName('textarea');
		alert('champs_textarea.length='+champs_textarea.length);
		for(i=0;i<champs_textarea.length;i++){
			if(champs_textarea[i].value=='') {
				champs_textarea[i].value=document.getElementById('ajout_a_textarea_vide').value;
			}
		}
	}
</script>\n";
}
//=========================

?>
<p><br /></p>
<?php require("../lib/footer.inc.php");?>