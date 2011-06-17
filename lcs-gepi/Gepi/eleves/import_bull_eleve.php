<?php

/*
 *
 * @version $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Import bulletin eleve";

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions compl�mentaires et/ou librairies utiles


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

// S�curit�
// SQL : INSERT INTO droits VALUES ( '/eleves/import_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation bulletin �l�ve', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/eleves/import_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation bulletin �l�ve', '');";
//


if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}




function get_nom_prenom_from_login($ele_login,$mode) {
	$retour="";

	$sql="SELECT nom,prenom FROM eleves WHERE login='$ele_login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$retour="LOGIN INCONNU";
	}
	else {
		$lig=mysql_fetch_object($res);

		if($mode=="np") {
			$retour=strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
		}
		else {
			$retour=ucfirst(strtolower($lig->prenom))." ".strtoupper($lig->nom);
		}
	}

	return $retour;
}

function get_infos_from_ele_login($ele_login,$mode) {
	$retour=array();

	//$sql="SELECT nom,prenom FROM eleves WHERE login='$ele_login';";
	$sql="SELECT * FROM eleves WHERE login='$ele_login';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$retour['denomination']="LOGIN INCONNU";
	}
	else {
		$lig=mysql_fetch_object($res);

		if($mode=="np") {
			$retour['denomination']=strtoupper($lig->nom)." ".ucfirst(strtolower($lig->prenom));
		}
		else {
			$retour['denomination']=ucfirst(strtolower($lig->prenom))." ".strtoupper($lig->nom);
		}

		$retour['nom']=$lig->nom;
		$retour['prenom']=$lig->prenom;
		$retour['no_gep']=$lig->no_gep;
		$retour['ele_id']=$lig->ele_id;
		$retour['elenoet']=$lig->elenoet;
		$retour['sexe']=$lig->sexe;
	}

	return $retour;
}

// PB: Il faut remplacer le login PROF par ANONYME_EXT... ou le nom de l'�tablissement
// A l'import, il faut avoir cr�� l'�l�ve,
//             cr�er des enseignements? dans une classe EXTERIEUR... il faut une classe par �l�ve...
//             si on a plusieurs arriv�es, �a fait des mati�res en plus,... pas un pb...
//             seules les mati�res suivies par l'�l�ve sont prises en compte...
//             cr�er des cours diff�rents pour chaque �l�ve pour �viter des moyennes de classe fantaisistes
// A l'export, une ligne pour l'association: LOGIN_ETAB -> Nom, pr�nom pour la table utilisateurs


// Nom: gepiSchoolName
// Pr�nom: gepiSchoolCity

// ======================== CSS et js particuliers ========================
$utilisation_win = "non";
$utilisation_jsdivdrag = "non";
//$javascript_specifique = ".js";
//$style_specifique = ".css";

// ===================== entete Gepi ======================================//
require_once("../lib/header.inc");
// ===================== fin entete =======================================//

$page="import_bull_eleve.php";

$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);

//debug_var();

//echo "<div class='norme'><p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "<div class='norme'><p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

if(getSettingValue('exp_imp_chgt_etab')!='yes') {
	// Pour activer le dispositif:
	// DELETE FROM setting WHERE name='exp_imp_chgt_etab';INSERT INTO setting SET name='exp_imp_chgt_etab', value='yes';
	echo "<p>Cette page est destin�e � importer les moyennes et appr�ciations du bulletin d'un �l�ve arrivant d'un autre �tablissement<br />\n";
	echo "L'�l�ve doit avoir �t� pr�alablement cr�� dans votre base et affect� dans une classe pour une p�riode au moins.</p>\n";
	echo "<p><br /></p>\n";
	echo "<p>Le dispositif (<i>encore en cours de test au 17/07/2008</i>) ne semble pas activ�.</p>\n";
	require_once("../lib/footer.inc.php");
	die();
}

//if(!isset($ele_login)) {
if((!isset($ele_login))&&(!isset($_POST['Recherche_sans_js']))) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Cette page est destin�e � importer les moyennes et appr�ciations du bulletin d'un �l�ve arrivant d'un autre �tablissement<br />\n";
	echo "L'�l�ve doit avoir �t� pr�alablement cr�� dans votre base et affect� dans une classe pour une p�riode au moins.</p>\n";

	echo "<p><i>Exemple:</i></p>\n";
	echo "<blockquote>\n";
	echo "<p>On inscrit un �l�ve arrivant en d�cembre dans une classe pour les trimestres 2 et 3.<br />Et seul le trimestre 1 sera import� depuis un fichier CSV.\n";
	echo "</p>\n";
	echo "</blockquote>\n";

	echo "<p class='bold'>Choix de l'�l�ve:</p>\n";
	// Formulaire pour navigateur SANS Javascript:
	echo "<noscript>
	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire1'>
		<p>
		Afficher les �l�ves dont le <b>nom</b> contient: <input type='text' name='rech_nom' value='' />
		<input type='hidden' name='page' value='$page' />
		<input type='submit' name='Recherche_sans_js' value='Rechercher' />
		</p>
	</form>
</noscript>\n";

	// Portion d'AJAX:
	echo "<script type='text/javascript'>
	function cherche_eleves() {
		rech_nom=document.getElementById('rech_nom').value;

		var url = 'liste_eleves.php';
		var myAjax = new Ajax.Request(
			url,
			{
				method: 'post',
				postBody: 'rech_nom='+rech_nom+'&page=$page',
				onComplete: affiche_eleves
			});
	}

	function affiche_eleves(xhr) {
		if (xhr.status == 200) {
			document.getElementById('liste_eleves').innerHTML = xhr.responseText;
		}
		else {
			document.getElementById('liste_eleves').innerHTML = xhr.status;
		}
	}
</script>\n";

	// DIV avec formulaire pour navigateur AVEC Javascript:
	echo "<div id='recherche_avec_js' style='display:none;'>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' onsubmit='cherche_eleves();return false;' method='post' name='formulaire'>";
	echo "<p>";
	echo "Afficher les �l�ves dont le <b>nom</b> contient: <input type='text' name='rech_nom' id='rech_nom' value='' />\n";
	echo "<input type='hidden' name='page' value='$page' />\n";
	echo "<input type='button' name='Recherche' value='Rechercher' onclick='cherche_eleves()' />\n";
	echo "</p>\n";
	echo "</form>\n";

	echo "<div id='liste_eleves'></div>\n";

	echo "</div>\n";
	echo "<script type='text/javascript'>document.getElementById('recherche_avec_js').style.display='';</script>\n";

	echo "<p><br /></p>\n";
	echo "<p><i>Remarque:</i></p>\n";
	echo "<blockquote>\n";
	echo "<p>Les bulletins de l'ancien �tablissement comporteront une information erron�e:<br />La structure de la table 'j_eleves_cpe' ne tient pas compte des p�riodes si bien que le CPE responsable pour les p�riodes o� l'�l�ve �tait dans son pr�c�dent �tablissement appra�tra comme �tant le CPE de votre �tablissement.<br />Il faudra changer la structure de 'j_eleves_cpe' dans une prochaine version de Gepi pour corriger ce bug.</p>\n";
	echo "</blockquote>\n";
}
elseif(isset($_POST['Recherche_sans_js'])) {
	// On ne passe ici que si JavaScript est d�sactiv�
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre �l�ve</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	include("recherche_eleve.php");
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre �l�ve</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	//$info_eleve=get_nom_prenom_from_login($ele_login,"pn");
	$info_eleve=get_infos_from_ele_login($ele_login,"pn");
	//if($info_eleve=="LOGIN INCONNU") {
	if($info_eleve['denomination']=="LOGIN INCONNU") {
		echo "<p>Le login '$ele_login' est inconnu dans la table 'eleves'.</p>\n";

		require_once("../lib/footer.inc.php");
		die;
	}

	echo "<p>Vous avez choisi ".$info_eleve['denomination']."<br />\n";

	if(!isset($_FILES["csv_file"])) {

		$tab_per=array();

		$sql="SELECT c.classe,jec.periode,p.nom_periode FROM j_eleves_classes jec, classes c, periodes p WHERE jec.login='$ele_login' AND jec.id_classe=c.id AND p.num_periode=jec.periode ORDER BY jec.periode;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "L'�l�ve n'est inscrit";
			if($info_eleve['sexe']=='F') {echo "e";}
			echo " dans aucune classe pour aucune p�riode.<br />\n";
			echo "</p>\n";
		}
		else {
			while($lig=mysql_fetch_object($res)) {
				if(!in_array($lig->periode,$tab_per)) {
					$tab_per[]=$lig->periode;
					echo "L'�l�ve est inscrit";
					if($info_eleve['sexe']=='F') {echo "e";}
					echo " en ".$lig->classe." pour la p�riode ".$lig->nom_periode.".<br />\n";
				}
			}
			echo "</p>\n";

			echo "<p>Seule(s) la ou les p�riode(s) non list�e(s) ci-dessus pourra(ont) �tre import�e(s) depuis le fichier CSV.</p>\n";
		}

		//echo nl2br(get_bull($ele_login));

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		echo add_token_field();
		echo "Fichier � importer: <input type='file' name='csv_file' value='' />\n";
		echo "<input type='hidden' name='ele_login' value=\"$ele_login\" />\n";
		echo "<input type='submit' name='envoi' value='Envoyer' />\n";
		echo "</form>\n";

		echo "<p><i>Attention</i>&nbsp;: Veillez � n'importer que des fichiers CSV g�n�r�s par Gepi.<br />Dans le doute, faites une sauvegarde pr�alable de la base.</p>\n";
	}
	else {

		//if((!is_array($_FILES["csv_file"]))||(!is_uploaded_file($_FILES["csv_file"]['tmp_name']))) {
		if(!is_uploaded_file($_FILES["csv_file"]['tmp_name'])) {
			echo "<p>Le fichier n'a pas �t� upload�...<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login'>Retour au choix du fichier</a></p>\n";
			require_once("../lib/footer.inc.php");
			die();
		}

		echo "<p>Fichier upload�...<br />\n";
		$csv_file=$_FILES["csv_file"];
		//echo "\$csv_file['tmp_name']=".$csv_file['tmp_name']."<br />";

		check_token(false);

		//flush();

		$sql="SELECT MAX(num_periode) AS nb_per FROM periodes p, j_eleves_classes jec WHERE jec.login='$ele_login' AND jec.id_classe=p.id_classe;";
		//echo "$sql<br />";

		//die();

		//flush();
		$res=mysql_query($sql);
		//$nb_per=3;
		$nb_per=0;
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			$nb_per=$lig->nb_per;
		}

		$tab_per=array();
		$sql="SELECT jec.periode FROM j_eleves_classes jec WHERE jec.login='$ele_login' ORDER BY jec.periode;";
		//echo "$sql<br />";
		//flush();
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {
				$tab_per[]=$lig->periode;
			}
		}

		echo "<p>Lecture du fichier: <br />\n";
		//$csv_file=$_FILES["csv_file"];
		$fich=fopen($csv_file['tmp_name'],"r");
		echo "<div style='color: green; border: 1px solid black;'>\n";
		while (!feof($fich)) {
			$ligne=fgets($fich, 4096);
			if(trim($ligne)!="") {
				//echo $ligne."<br />\n";
				echo htmlentities($ligne)."<br />\n";
			}
		}
		echo "</div>\n";
		fclose($fich);
		//echo "</p>\n";

		// Recherche des infos �tablissement
		//$csv_file=$_FILES["csv_file"];
		$fich=fopen($csv_file['tmp_name'],"r");
		while (!feof($fich)) {
			$ligne=fgets($fich, 4096);
			if(trim($ligne)!="") {
				if(substr($ligne,0,20)=="INFOS_ETABLISSEMENT;") {
					$tab_tmp=explode(";",$ligne);
					$nom_etab_ori=$tab_tmp[1];
					$ville_etab_ori=$tab_tmp[2];
					$rne_etab_ori=$tab_tmp[3];
				}
			}
		}
		fclose($fich);
		//echo "</p>\n";

		// Inscription dans j_eleves_etablissement
		$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='".$info_eleve['elenoet']."';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Insertion de l'association �l�ve/�tablissement d'origine: ";
			$sql="INSERT INTO j_eleves_etablissements SET id_eleve='".$info_eleve['elenoet']."', id_etablissement='$rne_etab_ori';";
			$res=mysql_query($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERREUR</span>";
				echo "</p>\n";
				//require_once("../lib/footer.inc.php");
				//die();
			}
		}
		else {
			echo "<p>Mise � jour de l'association �l�ve/�tablissement d'origine: ";
			$sql="UPDATE j_eleves_etablissements SET id_etablissement='$rne_etab_ori' WHERE id_eleve='".$info_eleve['elenoet']."';";
			$res=mysql_query($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERREUR</span>";
				echo "</p>\n";
				//require_once("../lib/footer.inc.php");
				//die();
			}
		}


		// Cr�er la classe
		//$sql="SELECT classe FROM classes WHERE classe LIKE '$nom_etab_ori%';";
		$sql="SELECT classe FROM classes WHERE classe='$nom_etab_ori';";
		//echo "$sql<br />";
		//flush();
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Cr�ation d'une classe '$nom_etab_ori': ";

			//$sql="INSERT INTO classes SET classe='$nom_etab_ori', nom_complet='$nom_etab_ori';";
			$sql="INSERT INTO classes SET classe='".$nom_etab_ori."',
										nom_complet='".$nom_etab_ori."',
										display_mat_cat='n',
										suivi_par='$nom_etab_ori',
										formule='Pour le conseil',
										format_nom='np',
										modele_bulletin_pdf='1'
										;";
			$res=mysql_query($sql);
			if($res) {
				$id_classe_etab=mysql_insert_id();
				$classe_etab=$nom_etab_ori;
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERREUR</span>";
				echo "</p>\n";
				require_once("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$cpt=1;
			while(true) {
				//$sql="SELECT classe FROM classes WHERE classe LIKE '".$nom_etab_ori.$cpt."';";
				$sql="SELECT classe FROM classes WHERE classe='".$nom_etab_ori.$cpt."';";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0) {
					echo "<p>Cr�ation d'une classe '".$nom_etab_ori.$cpt."': ";

					$sql="INSERT INTO classes SET classe='".$nom_etab_ori.$cpt."',
												nom_complet='".$nom_etab_ori.$cpt."',
												display_mat_cat='n',
												suivi_par='$nom_etab_ori',
												formule='Pour le conseil',
												format_nom='np',
												modele_bulletin_pdf='1'
												;";
					$res=mysql_query($sql);
					if($res) {
						$id_classe_etab=mysql_insert_id();
						$classe_etab=$nom_etab_ori.$cpt;
						echo "<span style='color:green;'>OK</span>";
						echo "</p>\n";
						break;
					}
					else {
						echo "<span style='color:red;'>ERREUR</span>";
						echo "</p>\n";
						require_once("../lib/footer.inc.php");
						die();
					}
				}
				else {
					//echo "$cpt<br />";
					//flush();
					$cpt++;
				}
			}
		}
		echo "<p>Vous pourrez renommer ult�rieurement la classe si vous le souhaitez.</p>\n";


		// Insertion du m�me nombre de p�riodes pour l'ancienne classe que pour l'actuelle
		// L'�l�ve ne sera pas affect� dans la classe pour toutes les p�riodes
		if($nb_per>0) {echo "<p>Cr�ation des p�riodes pour l'ancienne classe de l'�l�ve ('$classe_etab'): ";}
		for($i=1;$i<=$nb_per;$i++) {
			if($i>1) {echo ", ";}
			echo $i;
			$sql="INSERT INTO periodes SET num_periode='$i', nom_periode='P�riode $i', verouiller='O', id_classe='$id_classe_etab';";
			$res=mysql_query($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				//echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERREUR</span>";
				echo "</p>\n";
				//require_once("../lib/footer.inc.php");
				//die();
			}
		}


		// Cr�er l'utilisateur prof...
		$sql="SELECT login FROM utilisateurs WHERE nom='$nom_etab_ori' AND prenom='$ville_etab_ori';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Cr�ation d'un utilisateur professeur '$nom_etab_ori': ";

			// CREER UN LOGIN
			$login_etab=generate_unique_login($nom_etab_ori,$ville_etab_ori,"name");

			$sql="INSERT INTO utilisateurs SET login='$login_etab',
												nom='$nom_etab_ori',
												prenom='$ville_etab_ori',
												civilite='M.',
												password='',
												statut='professeur',
												etat='inactif';";
			$res=mysql_query($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERREUR</span>";
				echo "</p>\n";
				require_once("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$lig=mysql_fetch_object($test);
			$login_etab=$lig->login;
		}

		// Penser � inscrire dans j_scol_classe les comptes scolarit� qui ont la classe actuelle de l'�l�ve
		// et si l'�l�ve n'est dans aucune classe, proposer le lien.
		$sql="SELECT DISTINCT jsc.login FROM j_eleves_classes jec,j_scol_classes jsc WHERE jec.login='$ele_login' AND jec.id_classe=jsc.id_classe;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_query($res)) {
				$sql="SELECT 1=1 FROM j_scol_classes WHERE id_classe='$id_classe_etab' AND login='$lig->login';";
				$res1=mysql_query($sql);
				if(mysql_num_rows($res1)==0) {
					echo "Insertion de l'autorisation de consultation pour ".affiche_utilisateur($lig->login,'np').": ";
					$sql="INSERT INTO j_scol_classes SET id_classe='$id_classe_etab', login='$lig->login';";
					$res2=mysql_query($sql);
					if($res2) {
						echo "<span style='color:green;'>OK</span>";
						echo "</p>\n";
					}
					else {
						echo "<span style='color:red;'>ERREUR</span>";
						echo "</p>\n";
						//require_once("../lib/footer.inc.php");
						//die();
					}
				}
			}
		}


		/*
		// On a forc�ment une info erron�e sur le nom du CPE
		// parce que la table j_eleves_cpe n'a que deux champs e_login et cpe_login

		// Cr�er l'utilisateur CPE...
		$sql="SELECT login FROM utilisateurs WHERE nom='CPE $nom_etab_ori' AND prenom='$ville_etab_ori';";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Cr�ation d'un utilisateur 'CPE $nom_etab_ori': ";

			// CREER UN LOGIN
			$login_etab=generate_unique_login("CPE $nom_etab_ori","$ville_etab_ori","name");

			$sql="INSERT INTO utilisateurs SET login='$login_etab',
												nom='CPE $nom_etab_ori',
												prenom='$ville_etab_ori',
												civilite='M.',
												password='',
												statut='cpe',
												etat='inactif';";
			$res=mysql_query($sql);
			if($res) {
				echo "<span style='color:green;'>OK</span>";
				echo "</p>\n";
			}
			else {
				echo "<span style='color:red;'>ERREUR</span>";
				echo "</p>\n";
				require_once("../lib/footer.inc.php");
				die();
			}
		}
		else {
			$lig=mysql_fetch_object($test);
			$login_cpe_etab=$lig->login;
		}

		*/


		// Pour ne pas cr�er autant de groupes par mati�re qu'il y a de p�riodes:
		//$tab_src_id_groupes_crees=array();
		$tab_dst_id_groupes_crees=array();

		// Recherche des infos mati�res,...
		//$csv_file=$_FILES["csv_file"];
		$fich=fopen($csv_file['tmp_name'],"r");
		while (!feof($fich)) {
			$ligne=fgets($fich, 4096);
			if(trim($ligne)!="") {
				if(substr($ligne,0,20)=="AVIS_CONSEIL_CLASSE;") {
					$tab_tmp=explode(";",$ligne);

					$periode=$tab_tmp[1];
					if(!in_array($periode,$tab_per)) {
						$avis=$tab_tmp[2];


						// A REVOIR: Il y a une �conomie de requ�tes � faire sur les tests ci-dessous en stockant les infos dans un tableau


						// Cr�er l'association dans 'periodes' si elle n'est pas d�j� pr�sente
						$sql="SELECT 1=1 FROM periodes WHERE num_periode='$periode' AND id_classe='$id_classe_etab';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription de l'association p�riode/classe pour la p�riode '$periode': ";
							$sql="INSERT INTO periodes SET num_periode='$periode', nom_periode='P�riode $periode', verouiller='O', id_classe='$id_classe_etab';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Inscription de l'�l�ve dans la classe
						$sql="SELECT 1=1 FROM j_eleves_classes WHERE periode='$periode' AND id_classe='$id_classe_etab' AND login='$ele_login';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription de l'�l�ve dans la classe '$classe_etab' pour la p�riode '$periode': ";
							$sql="INSERT INTO j_eleves_classes SET periode='$periode', id_classe='$id_classe_etab', login='$ele_login';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Insertion de l'avis
						$sql="SELECT 1=1 FROM avis_conseil_classe WHERE login='$ele_login' AND periode='$periode';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription de l'avis du conseil de classe pour la p�riode '$periode': ";
							//$sql="INSERT INTO avis_conseil_classe SET login='$ele_login', periode='$periode', avis='$avis';";
							$sql="INSERT INTO avis_conseil_classe SET login='$ele_login', periode='$periode', avis='".my_ereg_replace("_POINT_VIRGULE_",";",$avis)."';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}
						else {
							// Ca ne devrait pas arriver...
							// Dans la lecture du fichier CSV, on ne retient que les p�riodes pour lesquelles il n'y a rien sur le bulletin de l'�l�ve dans le nouvel �tablissement...
							// ... ou plut�t les p�riodes pour lesquelles l'�l�ve n'est dans aucune classe (donc rien sur le bulletin)
							// Si on passe ici, c'est qu'il y a plusieurs lignes pour l'avis du conseil de classe pour une m�me p�riode dans ce CSV.
							echo "<p><span style='color:red;'>BIZARRE:</span> Il semble qu'il y ait plusieurs lignes d'avis du conseil de classe pour la p�riode '$periode'.</p>\n";
						}
					}
				}
				//else {
				elseif(substr($ligne,0,9)=="ABSENCES;") {

					$tab_tmp=explode(";",$ligne);

					$periode=$tab_tmp[1];
					if(!in_array($periode,$tab_per)) {
						$nb_absences=$tab_tmp[2];
						$non_justifie=$tab_tmp[3];
						$nb_retards=$tab_tmp[4];
						$app=$tab_tmp[5];

						$sql="SELECT 1=1 FROM absences WHERE login='$ele_login' AND periode='$periode';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription des absences/retards pour la p�riode '$periode': ";
							$sql="INSERT INTO absences SET login='$ele_login',
															periode='$periode',
															nb_absences='$nb_absences',
															non_justifie='$non_justifie',
															nb_retards='$nb_retards',
															appreciation='".my_ereg_replace("_POINT_VIRGULE_",";",$app)."';";
							//								appreciation='".$app."';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}
						else {
							// Ca ne devrait pas arriver...
							echo "<p><span style='color:red;'>BIZARRE:</span> Il semble qu'il y ait plusieurs lignes de totaux d'absences pour la p�riode '$periode'.</p>\n";
						}
					}
				}
				elseif((substr($ligne,0,20)!="INFOS_ETABLISSEMENT;")&&(substr($ligne,0,12)!="INFOS_ELEVE;")&&(substr($ligne,0,9)!="ABSENCES;")) {
					// $ligne devrait correspondre � une mati�re
					// Il faudrait identifier auparavant les mati�res et les associer aux mati�res du nouvel �tablissement...

					$tab_tmp=explode(";",$ligne);

					$periode=$tab_tmp[2];
					if(!in_array($periode,$tab_per)) {
						$matiere=$tab_tmp[0];
						$matiere_nom_complet=$tab_tmp[1];

						$src_id_groupe=$tab_tmp[3];

						$note=$tab_tmp[4];
						$statut=$tab_tmp[5];
						$app=$tab_tmp[6];

						// A REVOIR: Il y a une �conomie de requ�tes � faire sur les tests ci-dessous en stockant les infos dans un tableau


						// Cr�er l'association dans 'periodes' si elle n'est pas d�j� pr�sente
						$sql="SELECT 1=1 FROM periodes WHERE num_periode='$periode' AND id_classe='$id_classe_etab';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription de l'association p�riode/classe pour la p�riode '$periode': ";
							$sql="INSERT INTO periodes SET num_periode='$periode', nom_periode='P�riode $periode', verouiller='O', id_classe='$id_classe_etab';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Inscription de l'�l�ve dans la classe
						$sql="SELECT 1=1 FROM j_eleves_classes WHERE periode='$periode' AND id_classe='$id_classe_etab' AND login='$ele_login';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription de l'�l�ve dans la classe '$classe_etab' pour la p�riode '$periode': ";
							$sql="INSERT INTO j_eleves_classes SET periode='$periode', id_classe='$id_classe_etab', login='$ele_login';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}


						// Insertion de la mati�re
						//$sql="SELECT 1=1 FROM matieres WHERE matiere='$matiere' AND nom_complet='$matiere_nom_complet';";
						$sql="SELECT 1=1 FROM matieres WHERE matiere='$matiere';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription de la mati�re '".htmlentities($matiere)."' dans la table 'matieres': ";
							$sql="INSERT INTO matieres SET matiere='$matiere', nom_complet='$matiere_nom_complet';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Insertion de l'association prof/mati�re
						$sql="SELECT 1=1 FROM j_professeurs_matieres WHERE id_matiere='$matiere' AND id_professeur='$login_etab';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription de l'association professeur '$login_etab' / mati�re '".htmlentities($matiere)."' dans la table 'j_professeurs_matieres': ";
							$sql="INSERT INTO j_professeurs_matieres SET id_matiere='$matiere', id_professeur='$login_etab';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						}

						// Insertion du groupe
						// function create_group($_name, $_description, $_matiere, $_classes, $_categorie = 1)

						$tmp_classes=array($id_classe_etab);
						//if(in_array($src_id_groupe,$tab_src_id_groupes_crees) {
						if(isset($tab_dst_id_groupes_crees[$src_id_groupe])) {
							$current_id_groupe=$tab_dst_id_groupes_crees[$src_id_groupe];
						}
						else {
							$current_id_groupe=create_group($matiere, $matiere_nom_complet, $matiere, $tmp_classes);
						}
						//$current_id_groupe=mysql_insert_id();

						/*
						// FAIT PAR LE create_group()
						// Insertion de l'association groupe/classe
						echo "<p>Inscription de l'association groupe/classe dans la table 'j_groupes_classes': ";
						$sql="INSERT INTO j_groupes_classes SET id_groupe='$current_id_groupe', id_classe='$id_classe_etab', coef='1.0';";
						$res=mysql_query($sql);
						if($res) {
							echo "<span style='color:green;'>OK</span>";
							echo "</p>\n";
						}
						else {
							echo "<span style='color:red;'>ERREUR</span>";
							echo "</p>\n";
							require_once("../lib/footer.inc.php");
							die();
						}
						*/

						// Insertion de l'association groupe/�l�ve
						echo "<p>Inscription de l'association groupe/�l�ve dans la table 'j_eleves_groupes': ";
						$sql="INSERT INTO j_eleves_groupes SET id_groupe='$current_id_groupe', login='$ele_login', periode='$periode';";
						$res=mysql_query($sql);
						if($res) {
							echo "<span style='color:green;'>OK</span>";
							echo "</p>\n";
						}
						else {
							echo "<span style='color:red;'>ERREUR</span>";
							echo "</p>\n";
							require_once("../lib/footer.inc.php");
							die();
						}

						// Insertion de l'association groupe/prof
						$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$current_id_groupe' AND login='$login_etab';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							echo "<p>Inscription de l'association professeur '$login_etab' / groupe '".htmlentities($current_id_groupe)."' dans la table 'j_groupes_professeurs': ";
							$sql="INSERT INTO j_groupes_professeurs SET id_groupe='$current_id_groupe', login='$login_etab';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								//require_once("../lib/footer.inc.php");
								//die();
							}
						}

						/*
						// FAIT PAR LE create_group()
						// Insertion de l'association groupe/mati�re
						echo "<p>Inscription de l'association groupe/mati�re dans la table 'j_groupes_matieres': ";
						$sql="INSERT INTO j_groupes_matieres SET id_groupe='$current_id_groupe', matiere='$matiere';";
						$res=mysql_query($sql);
						if($res) {
							echo "<span style='color:green;'>OK</span>";
							echo "</p>\n";
						}
						else {
							echo "<span style='color:red;'>ERREUR</span>";
							echo "</p>\n";
							require_once("../lib/footer.inc.php");
							die();
						}

						// Il manquait aussi l'insertion dans matiere_categorie...
						*/

						// Insertion de la moyenne
						echo "<p>Inscription de la moyenne sur le bulletin dans la table 'matieres_notes': ";
						$sql="INSERT INTO matieres_notes SET login='$ele_login', id_groupe='$current_id_groupe', periode='$periode', note='$note', statut='$statut';";
						$res=mysql_query($sql);
						if($res) {
							echo "<span style='color:green;'>OK</span>";
							echo "</p>\n";
						}
						else {
							echo "<span style='color:red;'>ERREUR</span>";
							echo "</p>\n";
							require_once("../lib/footer.inc.php");
							die();
						}



						// Insertion de l'appr�ciation
						/*
						$sql="SELECT 1=1 FROM matieres_appreciations WHERE login='$ele_login' AND id_groupe='$current_id_groupe' AND periode='$periode';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
						*/
							echo "<p>Inscription de l'appr�ciation pour la mati�re '".htmlentities($matiere)."' sur la p�riode '$periode': ";
							//$sql="INSERT INTO matieres_appreciations SET login='$ele_login', periode='$periode', id_groupe='$current_id_groupe', appreciation='$app';";
							$sql="INSERT INTO matieres_appreciations SET login='$ele_login', periode='$periode', id_groupe='$current_id_groupe', appreciation='".my_ereg_replace("_POINT_VIRGULE_",";",$app)."';";
							$res=mysql_query($sql);
							if($res) {
								echo "<span style='color:green;'>OK</span>";
								echo "</p>\n";
							}
							else {
								echo "<span style='color:red;'>ERREUR</span>";
								echo "</p>\n";
								require_once("../lib/footer.inc.php");
								die();
							}
						/*
						}
						else {
							// Ca ne devrait pas arriver...
							echo "<p><span style='color:red;'>BIZARRE:</span> Il semble qu'il y ait plusieurs lignes d'appr�ciation pour un m�me groupe sur la p�riode '$periode'.</p>\n";
						}
						*/

						// Stockage de l'ancien groupe comme d�j� cr��:
						//$tab_src_id_groupes_crees[]=$src_id_groupe;
						$tab_dst_id_groupes_crees[$src_id_groupe]=$current_id_groupe;

					}
				}
			}
		}
		fclose($fich);
		//echo "</p>\n";

		echo "<p>Termin�.</p>\n";

	}
}


// Inclusion du bas de page
require_once("../lib/footer.inc.php");
?>
