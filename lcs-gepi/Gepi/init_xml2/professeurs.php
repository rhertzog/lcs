<?php
@set_time_limit(0);
/*
* $Id: professeurs.php 4070 2010-02-05 19:43:06Z adminpaulbert $
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

//================================================
// Fonction de g�n�ration de mot de passe r�cup�r�e sur TotallyPHP
// Aucune mention de licence pour ce script...

/*
 * The letter l (lowercase L) and the number 1
 * have been removed, as they can be mistaken
 * for each other.
*/

function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    //while ($i <= 7) {
    while ($i <= 5) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;
}
//================================================

function affiche_debug($texte){
	// Passer � 1 la variable pour g�n�rer l'affichage des infos de debug...
	$debug=0;
	if($debug==1){
		echo "<font color='green'>".$texte."</font>";
		flush();
	}
}


$liste_tables_del = array(
//"absences",
//"aid",
//"aid_appreciations",
//"aid_config",
//"avis_conseil_classe",
//"classes",
//"droits",
//"eleves",
//"responsables",
//"etablissements",
//"j_aid_eleves",
"j_aid_utilisateurs",
"j_aid_utilisateurs_gest",
"j_groupes_professeurs",
//"j_eleves_classes",
//"j_eleves_etablissements",
"j_eleves_professeurs",
//"j_eleves_regime",
//"j_professeurs_matieres",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
//"observatoire",
//"observatoire_comment",
//"observatoire_config",
//"observatoire_niveaux",
"observatoire_j_resp_champ",
//"observatoire_suivi",
//"periodes",
//"periodes_observatoire",
"tempo2",
//"temp_gep_import",
"tempo",
//"utilisateurs",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
//"setting"
);

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : Importation des professeurs";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On v�rifie si l'extension d_base est active
//verif_active_dbase();

?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php
echo "<center><h3 class='gepi'>Quatri�me phase d'initialisation<br />Importation des professeurs</h3></center>\n";

if (!isset($step1)) {
	$j=0;
	$flag=0;
	while (($j < count($liste_tables_del)) and ($flag==0)) {
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1){
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$flag=1;
			}
		}
		$j++;
	}

	$test = mysql_result(mysql_query("SELECT count(*) FROM utilisateurs WHERE statut='professeur'"),0);
	if ($test != 0) {$flag=1;}

	if ($flag != 0){
		echo "<p><b>ATTENTION ...</b><br />\n";
		echo "Des donn�es concernant les professeurs sont actuellement pr�sentes dans la base GEPI<br /></p>\n";
		echo "<p>Si vous poursuivez la proc�dure les donn�es telles que notes, appr�ciations, ... seront effac�es.</p>\n";
		echo "<ul><li>Seules la table contenant les utilisateurs (professeurs, admin, ...) et la table mettant en relation les mati�res et les professeurs seront conserv�es.</li>\n";
		echo "<li>Les professeurs de l'ann�e pass�e pr�sents dans la base GEPI et non pr�sents dans la base CSV de cette ann�e ne sont pas effac�s de la base GEPI mais simplement d�clar�s \"inactifs\".</li>\n";
		echo "</ul>\n";
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo "<input type=hidden name='step1' value='y' />\n";
		echo "<input type='submit' name='confirm' value='Poursuivre la proc�dure' />\n";
		echo "</form>\n";
		echo "</div>\n";
		echo "</body>\n";
		echo "</html>\n";
		die();
	}
}

if (!isset($is_posted)) {
	$j=0;
	while ($j < count($liste_tables_del)) {
		$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE '$liste_tables_del[$j]'"));
		if($test==1){
			if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
				$del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
			}
		}
		$j++;
	}
	$del = @mysql_query("DELETE FROM tempo2");

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	//echo "<p>Importation du fichier <b>F_wind.csv</b> contenant les donn�es relatives aux professeurs.";

	echo "<p>Importation du fichier <b>sts.xml</b> contenant les donn�es relatives aux professeurs.\n";
	//echo "<p>Veuillez pr�ciser le nom complet du fichier <b>F_wind.csv</b>.";
	echo "<input type=hidden name='is_posted' value='yes' />\n";
	echo "<input type=hidden name='step1' value='y' />\n";
	//echo "<p><input type='file' size='80' name='dbf_file' />";
	echo "<br /><br /><p>Quelle formule appliquer pour la g�n�ration du login ?</p>\n";

	echo "<input type='radio' name='login_gen_type' id='login_gen_type_name' value='name' checked /> <label for='login_gen_type_name'  style='cursor: pointer;'>nom</label>\n";
	echo "<br /><input type='radio' name='login_gen_type' id='login_gen_type_name8' value='name8' /> <label for='login_gen_type_name8'  style='cursor: pointer;'>nom (tronqu� � 8 caract�res)</label>\n";
	echo "<br /><input type='radio' name='login_gen_type' id='login_gen_type_fname8' value='fname8' /> <label for='login_gen_type_fname8'  style='cursor: pointer;'>pnom (tronqu� � 8 caract�res)</label>\n";
	echo "<br /><input type='radio' name='login_gen_type' id='login_gen_type_fname19' value='fname19' /> <label for='login_gen_type_fname19'  style='cursor: pointer;'>pnom (tronqu� � 19 caract�res)</label>\n";
	echo "<br /><input type='radio' name='login_gen_type' id='login_gen_type_firstdotname' value='firstdotname' /> <label for='login_gen_type_firstdotname'  style='cursor: pointer;'>prenom.nom</label>\n";
	echo "<br /><input type='radio' name='login_gen_type' id='login_gen_type_firstdotname19' value='firstdotname19' /> <label for='login_gen_type_firstdotname19'  style='cursor: pointer;'>prenom.nom (tronqu� � 19 caract�res)</label>\n";
	echo "<br /><input type='radio' name='login_gen_type' id='login_gen_type_namef8' value='namef8' /> <label for='login_gen_type_namef8'  style='cursor: pointer;'>nomp (tronqu� � 8 caract�res)</label>\n";
	echo "<br /><input type='radio' name='login_gen_type' id='login_gen_type_lcs' value='lcs' /> <label for='login_gen_type_lcs'  style='cursor: pointer;'>pnom (fa�on LCS)</label>\n";
	if (getSettingValue("use_ent") == "y") {
		echo "<br /><input type='radio' name='login_gen_type' id='login_gen_type_ent' value='ent' checked=\"checked\" />
			<label for='login_gen_type_ent'  style='cursor: pointer;'>
			Les logins sont produits par un ENT (<span title=\"Vous devez adapter le code du fichier ci-dessus vers la ligne 710.\">Attention !</span>)</label>\n";

	}
	echo "<br />\n";
	echo "<br />\n";

	// Modifications jjocal dans le cas o� c'est un serveur CAS qui s'occupe de tout
	if (getSettingValue("use_sso") == "cas") {
		$checked1 = ' checked="checked"';
		$checked0 = '';
	}else{
		$checked1 = '';
		$checked0 = ' checked="checked"';
	}

	echo "<p>Ces comptes seront-ils utilis�s en Single Sign-On avec CAS ou LemonLDAP ? (laissez 'non' si vous ne savez pas de quoi il s'agit)</p>\n";
	echo "<input type='radio' name='sso' id='sso_n' value='no'".$checked0." /> <label for='sso_n' style='cursor: pointer;'>Non</label>\n";
	echo "<br /><input type='radio' name='sso' id='sso_y' value='yes'".$checked1." /> <label for='sso_y' style='cursor: pointer;'>Oui (aucun mot de passe ne sera g�n�r�)</label>\n";
	echo "<br />\n";
	echo "<br />\n";


	echo "<p>Dans le cas o� la r�ponse � la question pr�c�dente est Non, voulez-vous:</p>\n";
	echo "<p><input type=\"radio\" name=\"mode_mdp\" id='mode_mdp_alea' value=\"alea\" checked /> <label for='mode_mdp_alea' style='cursor: pointer;'>G�n�rer un mot de passe al�atoire pour chaque professeur</label>.<br />\n";
	echo "<input type=\"radio\" name=\"mode_mdp\" id='mode_mdp_date' value=\"date\" /> <label for='mode_mdp_date' style='cursor: pointer;'>Utiliser plut�t la date de naissance au format 'aaaammjj' comme mot de passe initial (<i>il devra �tre modifi� au premier login</i>)</label>.</p>\n";
	echo "<br />\n";

	echo "<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";
	echo "<p><br /></p>\n";

}
else {
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas d�fini!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}

	$dest_file="../temp/".$tempdir."/sts.xml";
	$fp=fopen($dest_file,"r");
	if(!$fp){
		echo "<p>Le XML STS Emploi du temps n'a pas l'air pr�sent dans le dossier temporaire.<br />Auriez-vous saut� une �tape???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	/*
	echo "<p>Lecture du fichier STS Emploi du temps...<br />\n";
	while(!feof($fp)){
		$ligne[]=fgets($fp,4096);
	}
	fclose($fp);
	*/
	flush();

	echo "<p>";
	echo "Analyse du fichier pour extraire les informations de la section INDIVIDUS...<br />\n";

	$cpt=0;
	$temoin_professeurs=0;
	$temoin_profs_princ=-1;
	$temoin_prof_princ=-1;
	$temoin_disciplines=-1;
	$temoin_disc=-1;
	$prof=array();
	$i=0;
	$temoin_prof=0;
	//while($cpt<count($ligne)){
	while(!feof($fp)){
		$ligne=fgets($fp,4096);
		//echo htmlentities($ligne[$cpt])."<br />\n";
		//if(strstr($ligne[$cpt],"<INDIVIDUS>")){
		if(strstr($ligne,"<INDIVIDUS>")){
			echo "D�but de la section INDIVIDUS � la ligne <span style='color: blue;'>$cpt</span><br />\n";
			flush();
			$temoin_professeurs++;
		}
		//if(strstr($ligne[$cpt],"</INDIVIDUS>")){
		if(strstr($ligne,"</INDIVIDUS>")){
			echo "Fin de la section INDIVIDUS � la ligne <span style='color: blue;'>$cpt</span><br />\n";
			flush();
			$temoin_professeurs++;
		}
		if($temoin_professeurs==1){
			//if(strstr($ligne[$cpt],"<INDIVIDU ")){
			if(strstr($ligne,"<INDIVIDU ")){
				$prof[$i]=array();
				unset($tabtmp);
				//$tabtmp=explode('"',strstr($ligne[$cpt]," ID="));
				$tabtmp=explode('"',strstr($ligne," ID="));
				$prof[$i]["id"]=trim($tabtmp[1]);
				//$tabtmp=explode('"',strstr($ligne[$cpt]," TYPE="));
				$tabtmp=explode('"',strstr($ligne," TYPE="));
				$prof[$i]["type"]=trim($tabtmp[1]);
				$temoin_prof=1;
			}
			//if(strstr($ligne[$cpt],"</INDIVIDU>")){
			if(strstr($ligne,"</INDIVIDU>")){
				$temoin_prof=0;
				$i++;
			}
			if($temoin_prof==1){
				//if(strstr($ligne[$cpt],"<SEXE>")){
				if(strstr($ligne,"<SEXE>")){
					unset($tabtmp);
					//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
					$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
					//$prof[$i]["sexe"]=$tabtmp[2];
					$prof[$i]["sexe"]=trim(my_ereg_replace("[^1-2]","",$tabtmp[2]));
				}
				//if(strstr($ligne[$cpt],"<CIVILITE>")){
				if(strstr($ligne,"<CIVILITE>")){
					unset($tabtmp);
					//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
					$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
					//$prof[$i]["civilite"]=$tabtmp[2];
					$prof[$i]["civilite"]=trim(my_ereg_replace("[^1-3]","",$tabtmp[2]));
				}
				//if(strstr($ligne[$cpt],"<NOM_USAGE>")){
				if(strstr($ligne,"<NOM_USAGE>")){
					unset($tabtmp);
					//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
					$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
					//$prof[$i]["nom_usage"]=$tabtmp[2];
					$prof[$i]["nom_usage"]=trim(my_ereg_replace("[^a-zA-Z -]","",$tabtmp[2]));
				}
				//if(strstr($ligne[$cpt],"<NOM_PATRONYMIQUE>")){
				if(strstr($ligne,"<NOM_PATRONYMIQUE>")){
					unset($tabtmp);
					//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
					$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
					//$prof[$i]["nom_patronymique"]=$tabtmp[2];
					$prof[$i]["nom_patronymique"]=trim(my_ereg_replace("[^a-zA-Z -]","",$tabtmp[2]));
				}
				//if(strstr($ligne[$cpt],"<PRENOM>")){
				if(strstr($ligne,"<PRENOM>")){
					unset($tabtmp);
					//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
					$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
					//$prof[$i]["prenom"]=$tabtmp[2];
					$prof[$i]["prenom"]=trim(my_ereg_replace("[^a-zA-Z0-9������������������������������_. -]","",$tabtmp[2]));
				}
				//if(strstr($ligne[$cpt],"<DATE_NAISSANCE>")){
				if(strstr($ligne,"<DATE_NAISSANCE>")){
					unset($tabtmp);
					//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
					$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
					//$prof[$i]["date_naissance"]=$tabtmp[2];
					$prof[$i]["date_naissance"]=trim(my_ereg_replace("[^0-9-]","",$tabtmp[2]));
				}
				//if(strstr($ligne[$cpt],"<GRADE>")){
				if(strstr($ligne,"<GRADE>")){
					unset($tabtmp);
					//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
					$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));

					// Suppression des guillemets �ventuels
					//$prof[$i]["grade"]=trim($tabtmp[2]);
					$prof[$i]["grade"]=my_ereg_replace('"','',trim($tabtmp[2]));
				}
				//if(strstr($ligne[$cpt],"<FONCTION>")){
				if(strstr($ligne,"<FONCTION>")){
					unset($tabtmp);
					//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
					$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));

					// Suppression des guillemets �ventuels
					//$prof[$i]["fonction"]=trim($tabtmp[2]);
					$prof[$i]["fonction"]=my_ereg_replace('"','',trim($tabtmp[2]));
				}



				//if(strstr($ligne[$cpt],"<PROFS_PRINC>")){
				if(strstr($ligne,"<PROFS_PRINC>")){
					$temoin_profs_princ=1;
					//$prof[$i]["prof_princs"]=array();
					$j=0;
				}
				//if(strstr($ligne[$cpt],"</PROFS_PRINC>")){
				if(strstr($ligne,"</PROFS_PRINC>")){
					$temoin_profs_princ=0;
				}

				if($temoin_profs_princ==1){

					//if(strstr($ligne[$cpt],"<PROF_PRINC>")){
					if(strstr($ligne,"<PROF_PRINC>")){
						$temoin_prof_princ=1;
						$prof[$i]["prof_princ"]=array();
					}
					//if(strstr($ligne[$cpt],"</PROF_PRINC>")){
					if(strstr($ligne,"</PROF_PRINC>")){
						$temoin_prof_princ=0;
						$j++;
					}

					if($temoin_prof_princ==1){
						//if(strstr($ligne[$cpt],"<CODE_STRUCTURE>")){
						if(strstr($ligne,"<CODE_STRUCTURE>")){
							unset($tabtmp);
							//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
							$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));

							// Suppression des guillemets �ventuels
							//$prof[$i]["prof_princ"][$j]["code_structure"]=trim($tabtmp[2]);
							$prof[$i]["prof_princ"][$j]["code_structure"]=my_ereg_replace('"','',trim($tabtmp[2]));

							$temoin_au_moins_un_prof_princ="oui";
						}

						//if(strstr($ligne[$cpt],"<DATE_DEBUT>")){
						if(strstr($ligne,"<DATE_DEBUT>")){
							unset($tabtmp);
							//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
							$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
							$prof[$i]["prof_princ"][$j]["date_debut"]=trim($tabtmp[2]);
						}
						//if(strstr($ligne[$cpt],"<DATE_FIN>")){
						if(strstr($ligne,"<DATE_FIN>")){
							unset($tabtmp);
							//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
							$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));
							$prof[$i]["prof_princ"][$j]["date_fin"]=trim($tabtmp[2]);
						}
					}
				}




				//if(strstr($ligne[$cpt],"<DISCIPLINES>")){
				if(strstr($ligne,"<DISCIPLINES>")){
					$temoin_disciplines=1;
					$prof[$i]["disciplines"]=array();
					$j=0;
				}
				//if(strstr($ligne[$cpt],"</DISCIPLINES>")){
				if(strstr($ligne,"</DISCIPLINES>")){
					$temoin_disciplines=0;
				}



				if($temoin_disciplines==1){
					//if(strstr($ligne[$cpt],"<DISCIPLINE ")){
					if(strstr($ligne,"<DISCIPLINE ")){
						$temoin_disc=1;
						unset($tabtmp);
						//$tabtmp=explode('"',strstr($ligne[$cpt]," CODE="));
						$tabtmp=explode('"',strstr($ligne," CODE="));

						// Suppression des guillemets �ventuels
						//$prof[$i]["disciplines"][$j]["code"]=trim($tabtmp[1]);
						$prof[$i]["disciplines"][$j]["code"]=my_ereg_replace('"','',trim($tabtmp[1]));
					}
					//if(strstr($ligne[$cpt],"</DISCIPLINE>")){
					if(strstr($ligne,"</DISCIPLINE>")){
						$temoin_disc=0;
						$j++;
					}

					if($temoin_disc==1){
						//if(strstr($ligne[$cpt],"<LIBELLE_COURT>")){
						if(strstr($ligne,"<LIBELLE_COURT>")){
							unset($tabtmp);
							//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
							$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));

							// Suppression des guillemets �ventuels
							//$prof[$i]["disciplines"][$j]["libelle_court"]=trim($tabtmp[2]);
							$prof[$i]["disciplines"][$j]["libelle_court"]=my_ereg_replace('"','',trim($tabtmp[2]));
						}
						//if(strstr($ligne[$cpt],"<NB_HEURES>")){
						if(strstr($ligne,"<NB_HEURES>")){
							unset($tabtmp);
							//$tabtmp=explode(">",my_ereg_replace("<",">",$ligne[$cpt]));
							$tabtmp=explode(">",my_ereg_replace("<",">",$ligne));

							// Suppression des guillemets �ventuels
							//$prof[$i]["disciplines"][$j]["nb_heures"]=trim($tabtmp[2]);
							$prof[$i]["disciplines"][$j]["nb_heures"]=my_ereg_replace('"','',trim($tabtmp[2]));
						}
					}
				}


			}
		}
		$cpt++;
	}
	fclose($fp);

	// Les $prof[$i]["disciplines"] ne sont pas utilis�es sauf � titre informatif � l'affichage...
	// Les $prof[$i]["prof_princ"][$j]["code_structure"] peuvent �tre exploit�es � ce niveau pour d�signer les profs principaux.

	//========================================================

	// On commence par rendre inactifs tous les professeurs
	$req = mysql_query("UPDATE utilisateurs set etat='inactif' where statut = 'professeur'");

	// on efface la ligne "display_users" dans la table "setting" de fa�on � afficher tous les utilisateurs dans la page  /utilisateurs/index.php
	$req = mysql_query("DELETE from setting where NAME = 'display_users'");


	echo "<p>Dans le tableau ci-dessous, les identifiants en rouge correspondent � des professeurs nouveaux dans la base GEPI. les identifiants en vert correspondent � des professeurs d�tect�s dans les fichiers CSV mais d�j� pr�sents dans la base GEPI.<br /><br />Il est possible que certains professeurs ci-dessous, bien que figurant dans le fichier CSV, ne soient plus en exercice dans votre �tablissement cette ann�e. C'est pourquoi il vous sera propos� en fin de proc�dure d'initialsation, un nettoyage de la base afin de supprimer ces donn�es inutiles.</p>\n";
	echo "<table border='1' class='boireaus' cellpadding='2' cellspacing='2' summary='Tableau des professeurs'>\n";
	echo "<tr><th><p class=\"small\">Identifiant du professeur</p></th><th><p class=\"small\">Nom</p></th><th><p class=\"small\">Pr�nom</p></th><th>Mot de passe *</th></tr>\n";

	srand();

	$nb_reg_no = 0;

	/*
	//=========================
	$fp=fopen($dbf_file['tmp_name'],"r");
	// On lit une ligne pour passer la ligne d'ent�te:
	$ligne = fgets($fp, 4096);
	//=========================
	for($k = 1; ($k < $nblignes+1); $k++){
		//$ligne = dbase_get_record($fp,$k);
		if(!feof($fp)){
			$ligne = fgets($fp, 4096);
			if(trim($ligne)!=""){
				$tabligne=explode(";",$ligne);
				for($i = 0; $i < count($tabchamps); $i++) {
					//$affiche[$i] = dbase_filter(trim($ligne[$tabindice[$i]]));
					$affiche[$i] = dbase_filter(trim($tabligne[$tabindice[$i]]));
				}
	*/

	$tab_nouveaux_profs=array();

	$alt=1;
	for($k=0;$k<count($prof);$k++){
		if(isset($prof[$k]["fonction"])){
			if($prof[$k]["fonction"]=="ENS"){
				if($prof[$k]["sexe"]=="1"){
					$civilite="M.";
				}
				else{
					$civilite="Mme";
				}

				switch($prof[$k]["civilite"]){
					case 1:
						$civilite="M.";
						break;
					case 2:
						$civilite="Mme";
						break;
					case 3:
						$civilite="Mlle";
						break;
				}

				if($_POST['mode_mdp']=="alea"){
					$mdp=createRandomPassword();
				}
				else{
					$date=str_replace("-","",$prof[$k]["date_naissance"]);
					$mdp=$date;
				}

				//echo $prof[$k]["nom_usage"].";".$prof[$k]["prenom"].";".$civi.";"."P".$prof[$k]["id"].";"."ENS".";".$date."<br />\n";
				//$chaine=$prof[$k]["nom_usage"].";".$prof[$k]["prenom"].";".$civi.";"."P".$prof[$k]["id"].";"."ENS".";".$mdp;


				$prenoms = explode(" ",$prof[$k]["prenom"]);
				$premier_prenom = $prenoms[0];
				$prenom_compose = '';
				if (isset($prenoms[1])) $prenom_compose = $prenoms[0]."-".$prenoms[1];


				// On effectue d'abord un test sur le NUMIND
				$sql="select login from utilisateurs where (
				numind='P".$prof[$k]["id"]."' and
				numind!='' and
				statut='professeur')";
				//echo "<tr><td>$sql</td></tr>";
				$test_exist = mysql_query($sql);
				$result_test = mysql_num_rows($test_exist);
				if ($result_test == 0) {
					// On tente ensuite une reconnaissance sur nom/pr�nom, si le test NUMIND a �chou�
					$sql="select login from utilisateurs where (
					nom='".traitement_magic_quotes($prof[$k]["nom_usage"])."' and
					prenom = '".traitement_magic_quotes($premier_prenom)."' and
					statut='professeur')";

					// Pour debug:
					//echo "$sql<br />";
					$test_exist = mysql_query($sql);
					$result_test = mysql_num_rows($test_exist);
					if ($result_test == 0) {
						if ($prenom_compose != '') {
							$test_exist2 = mysql_query("select login from utilisateurs
							where (
							nom='".traitement_magic_quotes($prof[$k]["nom_usage"])."' and
							prenom = '".traitement_magic_quotes($prenom_compose)."' and
							statut='professeur'
							)");
							$result_test2 = mysql_num_rows($test_exist2);
							if ($result_test2 == 0) {
								$exist = 'no';
							} else {
								$exist = 'yes';
								$login_prof_gepi = mysql_result($test_exist2,0,'login');
							}
						} else {
							$exist = 'no';
						}
					} else {
						$exist = 'yes';
						$login_prof_gepi = mysql_result($test_exist,0,'login');
					}
				} else {
					$exist = 'yes';
					$login_prof_gepi = mysql_result($test_exist,0,'login');
				}

				if ($exist == 'no') {

					// Aucun professeur ne porte le m�me nom dans la base GEPI. On va donc rentrer ce professeur dans la base

					$prof[$k]["prenom"]=traitement_magic_quotes(corriger_caracteres($prof[$k]["prenom"]));

					if ($_POST['login_gen_type'] == "name") {
						$temp1 = $prof[$k]["nom_usage"];
						$temp1 = strtoupper($temp1);
						$temp1 = my_ereg_replace(" ","", $temp1);
						$temp1 = my_ereg_replace("-","_", $temp1);
						$temp1 = my_ereg_replace("'","", $temp1);
						$temp1 = strtoupper(remplace_accents($temp1,"all"));
						//$temp1 = substr($temp1,0,8);

					} elseif ($_POST['login_gen_type'] == "name8") {
						$temp1 = $prof[$k]["nom_usage"];
						$temp1 = strtoupper($temp1);
						$temp1 = my_ereg_replace(" ","", $temp1);
						$temp1 = my_ereg_replace("-","_", $temp1);
						$temp1 = my_ereg_replace("'","", $temp1);
						$temp1 = strtoupper(remplace_accents($temp1,"all"));
						$temp1 = substr($temp1,0,8);
					} elseif ($_POST['login_gen_type'] == "fname8") {
						$temp1 = $prof[$k]["prenom"]{0} . $prof[$k]["nom_usage"];
						$temp1 = strtoupper($temp1);
						$temp1 = my_ereg_replace(" ","", $temp1);
						$temp1 = my_ereg_replace("-","_", $temp1);
						$temp1 = my_ereg_replace("'","", $temp1);
						$temp1 = strtoupper(remplace_accents($temp1,"all"));
						$temp1 = substr($temp1,0,8);
					} elseif ($_POST['login_gen_type'] == "fname19") {
						$temp1 = $prof[$k]["prenom"]{0} . $prof[$k]["nom_usage"];
						$temp1 = strtoupper($temp1);
						$temp1 = my_ereg_replace(" ","", $temp1);
						$temp1 = my_ereg_replace("-","_", $temp1);
						$temp1 = my_ereg_replace("'","", $temp1);
						$temp1 = strtoupper(remplace_accents($temp1,"all"));
						$temp1 = substr($temp1,0,19);
					} elseif ($_POST['login_gen_type'] == "firstdotname") {
						if ($prenom_compose != '') {
							$firstname = $prenom_compose;
						} else {
							$firstname = $premier_prenom;
						}

						$temp1 = $firstname . "." . $prof[$k]["nom_usage"];
						$temp1 = strtoupper($temp1);

						$temp1 = my_ereg_replace(" ","", $temp1);
						$temp1 = my_ereg_replace("-","_", $temp1);
						$temp1 = my_ereg_replace("'","", $temp1);
						$temp1 = strtoupper(remplace_accents($temp1,"all"));
						//$temp1 = substr($temp1,0,19);
					} elseif ($_POST['login_gen_type'] == "firstdotname19") {
						if ($prenom_compose != '') {
							$firstname = $prenom_compose;
						} else {
							$firstname = $premier_prenom;
						}

						$temp1 = $firstname . "." . $prof[$k]["nom_usage"];
						$temp1 = strtoupper($temp1);
						$temp1 = my_ereg_replace(" ","", $temp1);
						$temp1 = my_ereg_replace("-","_", $temp1);
						$temp1 = my_ereg_replace("'","", $temp1);
						$temp1 = strtoupper(remplace_accents($temp1,"all"));
						$temp1 = substr($temp1,0,19);
					} elseif ($_POST['login_gen_type'] == "namef8") {
						$temp1 =  substr($prof[$k]["nom_usage"],0,7) . $prof[$k]["prenom"]{0};
						$temp1 = strtoupper($temp1);
						$temp1 = my_ereg_replace(" ","", $temp1);
						$temp1 = my_ereg_replace("-","_", $temp1);
						$temp1 = my_ereg_replace("'","", $temp1);
						$temp1 = strtoupper(remplace_accents($temp1,"all"));
						//$temp1 = substr($temp1,0,8);
					} elseif ($_POST['login_gen_type'] == "lcs") {
						$nom = $prof[$k]["nom_usage"];
						$nom = strtolower($nom);
						if (preg_match("/\s/",$nom)) {
							$noms = preg_split("/\s/",$nom);
							$nom1 = $noms[0];
							if (strlen($noms[0]) < 4) {
								$nom1 .= "_". $noms[1];
								$separator = " ";
							} else {
								$separator = "-";
							}
						} else {
							$nom1 = $nom;
							$sn = ucfirst($nom);
						}
						$firstletter_nom = $nom1{0};
						$firstletter_nom = strtoupper($firstletter_nom);
						$prenom = $prof[$k]["prenom"];
						$prenom1 = $prof[$k]["prenom"]{0};
						$temp1 = $prenom1 . $nom1;
						$temp1 = remplace_accents($temp1,"all");
					}elseif($_POST['login_gen_type'] == 'ent'){

						if (getSettingValue("use_ent") == "y") {
							// Charge � l'organisme utilisateur de pourvoir � cette fonctionnalit�
							// le code suivant n'est qu'une m�thode propos�e pour relier Gepi � un ENT
							$bx = 'oui';
							if (isset($bx) AND $bx == 'oui') {
								// On va chercher le login de l'utilisateur dans la table cr��e
								$sql_p = "SELECT login_u FROM ldap_bx
											WHERE nom_u = '".strtoupper($prof[$k]["nom_usage"])."'
											AND prenom_u = '".strtoupper($prof[$k]["prenom"])."'
											AND statut_u = 'teacher'";
								$query_p = mysql_query($sql_p);
								$nbre = mysql_num_rows($query_p);
								if ($nbre >= 1 AND $nbre < 2) {
									$temp1 = mysql_result($query_p, 0,"login_u");
								}else{
									// Il faudrait alors proposer une alternative � ce cas
									$temp1 = "erreur_".$k;
								}
							}
						}else{
							Die('Vous n\'avez pas autoris� Gepi � utiliser un ENT');
						}
					}

					$login_prof = $temp1;
					//$login_prof = remplace_accents($temp1,"all");
					// On teste l'unicit� du login que l'on vient de cr�er
					$m = 2;
					$test_unicite = 'no';
					$temp = $login_prof;
					while ($test_unicite != 'yes') {
						$test_unicite = test_unique_login($login_prof);

						if ($test_unicite != 'yes') {
							$login_prof = $temp.$m;
							$m++;
						}
					}
					$prof[$k]["nom_usage"] = traitement_magic_quotes(corriger_caracteres($prof[$k]["nom_usage"]));
					// Mot de passe et change_mdp

					$changemdp = 'y';

					//echo "<tr><td colspan='4'>strlen($affiche[5])=".strlen($affiche[5])."<br />\$affiche[4]=$affiche[4]<br />\$_POST['sso']=".$_POST['sso']."</td></tr>";
					if (strlen($mdp)>2 and $prof[$k]["fonction"]=="ENS" and $_POST['sso'] == "no") {
						//
						$pwd = md5(trim($mdp)); //NUMEN
						//$mess_mdp = "NUMEN";
						if($_POST['mode_mdp']=='alea'){
							$mess_mdp = "$mdp";
						}
						else{
							$mess_mdp = "Mot de passe d'apr�s la date de naissance";
						}
						//echo "<tr><td colspan='4'>NUMEN: $affiche[5] $pwd</td></tr>";
					} elseif ($_POST['sso']== "no") {
						$pwd = md5(rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9).rand (1,9));
						$mess_mdp = $pwd;
						//echo "<tr><td colspan='4'>Choix 2: $pwd</td></tr>";
			//                       $mess_mdp = "Inconnu (compte bloqu�)";
					} elseif ($_POST['sso'] == "yes") {
						$pwd = '';
						$mess_mdp = "aucun (sso)";
						$changemdp = 'n';
						//echo "<tr><td colspan='4'>sso</td></tr>";
					}

					// utilise le pr�nom compos� s'il existe, plut�t que le premier pr�nom

					//$res = mysql_query("INSERT INTO utilisateurs VALUES ('".$login_prof."', '".$prof[$k]["nom_usage"]."', '".$premier_prenom."', '".$civilite."', '".$pwd."', '', 'professeur', 'actif', 'y', '')");
					//$sql="INSERT INTO utilisateurs SET login='$login_prof', nom='".$prof[$k]["nom_usage"]."', prenom='$premier_prenom', civilite='$civilite', password='$pwd', statut='professeur', etat='actif', change_mdp='y'";
					$sql="INSERT INTO utilisateurs SET login='$login_prof', nom='".$prof[$k]["nom_usage"]."', prenom='$premier_prenom', civilite='$civilite', password='$pwd', statut='professeur', etat='actif', change_mdp='".$changemdp."', numind='P".$prof[$k]["id"]."'";
					$res = mysql_query($sql);
					// Pour debug:
					//echo "<tr><td colspan='4'>$sql</td></tr>";

					$tab_nouveaux_profs[]="$login_prof|$mess_mdp";

					if(!$res){$nb_reg_no++;}
					$res = mysql_query("INSERT INTO tempo2 VALUES ('".$login_prof."', '"."P".$prof[$k]["id"]."')");

					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td><p><font color='red'>".$login_prof."</font></p></td><td><p>".$prof[$k]["nom_usage"]."</p></td><td><p>".$premier_prenom."</p></td><td>".$mess_mdp."</td></tr>\n";
				} else {
// 					//$res = mysql_query("UPDATE utilisateurs set etat='actif' where login = '".$login_prof_gepi."'");
					// On corrige aussi les nom/pr�nom/civilit� et numind parce que la reconnaissance a aussi pu se faire sur le nom/pr�nom
					$res = mysql_query("UPDATE utilisateurs set etat='actif', nom='".$prof[$k]["nom_usage"]."', prenom='$premier_prenom', civilite='$civilite', numind='P".$prof[$k]["id"]."' where login = '".$login_prof_gepi."'");
					if(!$res) $nb_reg_no++;
					$res = mysql_query("INSERT INTO tempo2 VALUES ('".$login_prof_gepi."', '"."P".$prof[$k]["id"]."')");

					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td><p><font color='green'>".$login_prof_gepi."</font></p></td><td><p>".$prof[$k]["nom_usage"]."</p></td><td><p>".$prof[$k]["prenom"]."</p></td><td>Inchang�</td></tr>\n";
				}
			}
		}
	}
	//dbase_close($fp);
	//fclose($fp);
	echo "</table>\n";


	if((isset($tab_nouveaux_profs))&&(count($tab_nouveaux_profs)>0)) {
		echo "<form action='../utilisateurs/impression_bienvenue.php' method='post' target='_blank'>\n";
		echo "<p>Imprimer les fiches bienvenue pour les nouveaux professeurs&nbsp;: \n";
		for($i=0;$i<count($tab_nouveaux_profs);$i++) {
			$tmp_tab=explode('|',$tab_nouveaux_profs[$i]);
			echo "<input type='hidden' name='user_login[]' value='$tmp_tab[0]' />\n";
			echo "<input type='hidden' name='mot_de_passe[]' value='$tmp_tab[1]' />\n";
		}
		echo "<input type='submit' value='Imprimer' /></p>\n";
		echo "</form>\n";
	}

	if ($nb_reg_no != 0) {
		echo "<p>Lors de l'enregistrement des donn�es il y a eu <span style='color:red;'>$nb_reg_no erreurs</span>. Essayez de trouvez la cause de l'erreur et recommencez la proc�dure avant de passer � l'�tape suivante.\n";
	}
	else {
		echo "<p>L'importation des professeurs dans la base GEPI a �t� effectu�e avec succ�s !</p>\n";

		/*
		echo "<p><b>* Pr�cision sur les mots de passe (en non-SSO) :</b><br />
		(il est conseill� d'imprimer cette page)</p>
		<ul>
		<li>Lorsqu'un nouveau professeur est ins�r� dans la base GEPI, son mot de passe lors de la premi�re
		connexion � GEPI est son NUMEN.</li>
		<li>Si le NUMEM n'est pas disponible dans le fichier F_wind.csv, GEPI g�n�re al�atoirement
		un mot de passe.</li></ul>";
		*/
		echo "<p><b>* Pr�cision sur les mots de passe (en non-SSO) :</b></p>\n";
		echo "<ul>
		<li>Lorsqu'un nouveau professeur est ins�r� dans la base GEPI, son mot de passe lors de la premi�re
		connexion � GEPI est celui choisi � l'�tape pr�c�dente:<br />
			<ul>
			<li>Mot de passe dapr�s la date de naissance au format 'aaaammjj', ou</li>
			<li>un mot de passe g�n�r� al�atoirement par GEPI.<br />(il est alors conseill� d'imprimer cette page)</li>
			</ul>
		</ul>\n";
		if ($_POST['sso'] != "yes") {
			echo "<p><b>Dans tous les cas le nouvel utilisateur est amen� � changer son mot de passe lors de sa premi�re connexion.</b></p>\n";
		}
		echo "<br />\n<p>Vous pouvez proc�der � la cinqui�me phase d'affectation des mati�res � chaque professeur, d'affectation des professeurs dans chaque classe et de d�finition des options suivies par les �l�ves.</p>\n";
	}


	// Cr�ation du f_div.csv pour l'import des profs principaux plus loin
	affiche_debug("Cr�ation du f_div.csv pour l'import des profs principaux lors d'une autre �tape.<br />\n");
	$fich=fopen("../temp/$tempdir/f_div.csv","w+");
	$chaine="DIVCOD;NUMIND";
	if($fich){
		fwrite($fich,html_entity_decode_all_version($chaine)."\n");
	}
	affiche_debug($chaine."<br />\n");
	/*
	for($i=0;$i<count($divisions);$i++){
		$numind_pp="";
		for($m=0;$m<count($prof);$m++){
			for($n=0;$n<count($prof[$m]["prof_princ"]);$n++){
				if($prof[$m]["prof_princ"][$n]["code_structure"]==$divisions[$i]["code"]){
					$numind_pp="P".$prof[$m]["id"];
				}
			}
		}
		$chaine=$divisions[$i]["code"].";".$numind_pp;
		if($fich){
			fwrite($fich,html_entity_decode_all_version($chaine)."\n");
		}
		affiche_debug($chaine."<br />\n");
	}
	*/

	$tabchaine=array();
	for($m=0;$m<count($prof);$m++){
		if(isset($prof[$m]["prof_princ"])){
			for($n=0;$n<count($prof[$m]["prof_princ"]);$n++){
				$tabchaine[]=$prof[$m]["prof_princ"][$n]["code_structure"].";"."P".$prof[$m]["id"];
				//$chaine=$prof[$m]["prof_princ"][$n]["code_structure"].";"."P".$prof[$m]["id"];
				//if($fich){
				//	fwrite($fich,html_entity_decode_all_version($chaine)."\n");
				//}
				affiche_debug($chaine."<br />\n");
			}
		}
	}
	sort($tabchaine);
	for($i=0;$i<count($tabchaine);$i++){
		if($fich){
			fwrite($fich,html_entity_decode_all_version($tabchaine[$i])."\n");
		}
	}
	fclose($fich);


	if (getSettingValue("use_ent") == "y"){

		echo '<p style="text-align: center; font-weight: bold;"><a href="../mod_ent/gestion_ent_profs.php">V�rifier les logins avant de poursuivre</a></p>'."\n";

	}else{

		echo '<p style="text-align: center; font-weight: bold;"><a href="prof_disc_classe_csv.php">Proc�der � la cinqui�me phase d\'initialisation</a></p>'."\n";

	}

	echo "<p><br /></p>\n";
}

require("../lib/footer.inc.php");
?>