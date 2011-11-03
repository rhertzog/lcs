<?php
/*
 * $Id: modify_eleve.php 8478 2011-10-14 12:21:47Z crob $
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

unset($reg_login);
$reg_login = isset($_POST["reg_login"]) ? $_POST["reg_login"] : NULL;
unset($reg_nom);
$reg_nom = isset($_POST["reg_nom"]) ? $_POST["reg_nom"] : NULL;
unset($reg_prenom);
$reg_prenom = isset($_POST["reg_prenom"]) ? $_POST["reg_prenom"] : NULL;
unset($reg_email);
$reg_email = isset($_POST["reg_email"]) ? $_POST["reg_email"] : NULL;
unset($reg_sexe);
$reg_sexe = isset($_POST["reg_sexe"]) ? $_POST["reg_sexe"] : NULL;
unset($reg_no_nat);
$reg_no_nat = isset($_POST["reg_no_nat"]) ? $_POST["reg_no_nat"] : NULL;
unset($reg_no_gep);
$reg_no_gep = isset($_POST["reg_no_gep"]) ? $_POST["reg_no_gep"] : NULL;
unset($birth_year);
$birth_year = isset($_POST["birth_year"]) ? $_POST["birth_year"] : NULL;
unset($birth_month);
$birth_month = isset($_POST["birth_month"]) ? $_POST["birth_month"] : NULL;
unset($birth_day);
$birth_day = isset($_POST["birth_day"]) ? $_POST["birth_day"] : NULL;

//Gestion de la date de sortie de l'�tablissement
unset($date_sortie_jour);
$date_sortie_jour = isset($_POST["date_sortie_jour"]) ? $_POST["date_sortie_jour"] : "00";
unset($date_sortie_mois);
$date_sortie_mois = isset($_POST["date_sortie_mois"]) ? $_POST["date_sortie_mois"] : "00";
unset($date_sortie_annee);
$date_sortie_annee = isset($_POST["date_sortie_annee"]) ? $_POST["date_sortie_annee"] : "0000";

//=========================
// AJOUT: boireaus 20071107
unset($reg_regime);
$reg_regime = isset($_POST["reg_regime"]) ? $_POST["reg_regime"] : NULL;
unset($reg_doublant);
$reg_doublant = isset($_POST["reg_doublant"]) ? $_POST["reg_doublant"] : NULL;

//echo "\$reg_regime=$reg_regime<br />";
//echo "\$reg_doublant=$reg_doublant<br />";

//=========================


//=========================
$modif_adr_pers_id=isset($_GET["modif_adr_pers_id"]) ? $_GET["modif_adr_pers_id"] : NULL;
$adr_id=isset($_GET["adr_id"]) ? $_GET["adr_id"] : NULL;
//=========================


unset($reg_resp1);
$reg_resp1 = isset($_POST["reg_resp1"]) ? $_POST["reg_resp1"] : NULL;
unset($reg_resp2);
$reg_resp2 = isset($_POST["reg_resp2"]) ? $_POST["reg_resp2"] : NULL;

unset($reg_etab);
$reg_etab = isset($_POST["reg_etab"]) ? $_POST["reg_etab"] : NULL;

unset($mode);
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : NULL);
unset($order_type);
$order_type = isset($_POST["order_type"]) ? $_POST["order_type"] : (isset($_GET["order_type"]) ? $_GET["order_type"] : NULL);
unset($quelles_classes);
$quelles_classes = isset($_POST["quelles_classes"]) ? $_POST["quelles_classes"] : (isset($_GET["quelles_classes"]) ? $_GET["quelles_classes"] : NULL);
unset($eleve_login);
$eleve_login = isset($_POST["eleve_login"]) ? $_POST["eleve_login"] : (isset($_GET["eleve_login"]) ? $_GET["eleve_login"] : NULL);
//echo "\$eleve_login=$eleve_login<br />";

$definir_resp = isset($_POST["definir_resp"]) ? $_POST["definir_resp"] : (isset($_GET["definir_resp"]) ? $_GET["definir_resp"] : NULL);
if(($definir_resp!=1)&&($definir_resp!=2)){$definir_resp=NULL;}

$definir_etab = isset($_POST["definir_etab"]) ? $_POST["definir_etab"] : (isset($_GET["definir_etab"]) ? $_GET["definir_etab"] : NULL);


//=========================
// AJOUT: boireaus 20071212
// Pour l'arriv�e depuis la page index.php suite � une recherche
$motif_rech=isset($_POST['motif_rech']) ? $_POST['motif_rech'] : (isset($_GET['motif_rech']) ? $_GET['motif_rech'] : NULL);
//=========================

$journal_connexions=isset($_POST['journal_connexions']) ? $_POST['journal_connexions'] : (isset($_GET['journal_connexions']) ? $_GET['journal_connexions'] : 'n');
$duree=isset($_POST['duree']) ? $_POST['duree'] : NULL;

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


if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
	// On r�cup�re le RNE de l'�tablissement
	$rep_photos="../photos/".$_COOKIE['RNE']."/eleves/";
} else {
	$rep_photos="../photos/eleves/";
}

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {
	// Le deuxi�me responsable prend l'adresse du premier
	if((isset($modif_adr_pers_id))&&(isset($adr_id))) {
		check_token();
		$sql="UPDATE resp_pers SET adr_id='$adr_id' WHERE pers_id='$modif_adr_pers_id';";
		$update=mysql_query($sql);
		if(!$update){
			$msg="Echec de la modification de l'adresse du deuxi�me responsable.";
		}
	}


	/*
	foreach($_POST as $post => $val){
		echo $post.' : '.$val."<br />\n";
	}

	echo "\$eleve_login=$eleve_login<br />";
	echo "\$valider_choix_resp=$valider_choix_resp<br />";
	echo "\$definir_resp=$definir_resp<br />";
	*/
	// Validation d'un choix de responsable
	if((isset($eleve_login))&&(isset($definir_resp))&&(isset($_POST['valider_choix_resp']))) {
		check_token();

		if($definir_resp==1){
			$pers_id=$reg_resp1;
		}
		else{
			$pers_id=$reg_resp2;
		}

		if($pers_id==""){
			// Recherche de l'ele_id
			$sql="SELECT ele_id FROM eleves WHERE login='$eleve_login'";
			$res_ele=mysql_query($sql);
			if(mysql_num_rows($res_ele)==0){
				$msg="Erreur: L'�l�ve $eleve_login n'a pas l'air pr�sent dans la table 'eleves'.";
			}
			else{
				$lig_ele=mysql_fetch_object($res_ele);

				$sql="DELETE FROM responsables2 WHERE ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
				$suppr=mysql_query($sql);
				if($suppr){
					$msg="Suppression de l'association de l'�l�ve avec le responsable $definir_resp r�ussie.";
				}
				else{
					$msg="Echec de la suppression l'association de l'�l�ve avec le responsable $definir_resp.";
				}
			}
		}
		else{
			$sql="SELECT 1=1 FROM resp_pers WHERE pers_id='$pers_id'";
			$test=mysql_query($sql);

			if(mysql_num_rows($test)==0){
				$msg="Erreur: L'identifiant de responsable propos� n'existe pas.";
			}
			else{
				// Recherche de l'ele_id
				$sql="SELECT ele_id FROM eleves WHERE login='$eleve_login'";
				$res_ele=mysql_query($sql);
				if(mysql_num_rows($res_ele)==0){
					$msg="Erreur: L'�l�ve $eleve_login n'a pas l'air pr�sent dans la table 'eleves'.";
				}
				else{
					$lig_ele=mysql_fetch_object($res_ele);

					//$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$pers_id' AND ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
					$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
					$test=mysql_query($sql);

					if(mysql_num_rows($test)==0){
						$sql="INSERT INTO responsables2 SET pers_id='$pers_id', ele_id='$lig_ele->ele_id', resp_legal='$definir_resp', pers_contact='1'";
						$insert=mysql_query($sql);
						if($insert){
							$msg="Association de l'�l�ve avec le responsable $definir_resp r�ussie.";
						}
						else{
							$msg="Echec de l'association de l'�l�ve avec le responsable $definir_resp.";
						}
					}
					else{
						$sql="UPDATE responsables2 SET pers_id='$pers_id' WHERE ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
						$update=mysql_query($sql);
						if($update){
							$msg="Association de l'�l�ve avec le responsable $definir_resp r�ussie.";
						}
						else{
							$msg="Echec de l'association de l'�l�ve avec le responsable $definir_resp.";
						}
					}
				}
			}
		}
		unset($definir_resp);
	}

	//debug_var();

	// Validation d'un choix d'�tablissement d'origine
	if((isset($eleve_login))&&(isset($definir_etab))&&(isset($_POST['valider_choix_etab']))) {
		check_token();
	//if((isset($eleve_login))&&(isset($reg_no_gep))&&($reg_no_gep!="")&&(isset($definir_etab))&&(isset($_POST['valider_choix_etab']))) {

		$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login';";
		$res_elenoet=mysql_query($sql);
		if(mysql_num_rows($res_elenoet)>0) {
			$lig_elenoet=mysql_fetch_object($res_elenoet);
			$reg_no_gep=$lig_elenoet->elenoet;
			if($reg_no_gep!="") {
				if($reg_etab==""){
					//$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'";
					$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='$reg_no_gep'";
					$suppr=mysql_query($sql);
					if($suppr){
						$msg="Suppression de l'association de l'�l�ve avec un �tablissement r�ussie.";
					}
					else{
						$msg="Echec de la suppression l'association de l'�l�ve avec un �tablissement.";
					}
				}
				else{
					$sql="SELECT 1=1 FROM etablissements WHERE id='$reg_etab'";
					//echo "$sql<br />";
					$test=mysql_query($sql);

					if(mysql_num_rows($test)==0){
						$msg="Erreur: L'�tablissement choisi (<i>$reg_etab</i>) n'existe pas dans la table 'etablissement'.";
					}
					else{
						//$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'";
						$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_no_gep'";
						$test=mysql_query($sql);

						if(mysql_num_rows($test)==0){
							//$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$eleve_login', id_etablissement='$reg_etab'";
							$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_no_gep', id_etablissement='$reg_etab'";
							$insert=mysql_query($sql);
							if($insert){
								$msg="Association de l'�l�ve avec l'�tablissement $reg_etab r�ussie.";
							}
							else{
								$msg="Echec de l'association de l'�l�ve avec l'�tablissement $reg_etab.";
							}
						}
						else{
							//$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab' WHERE id_eleve='$eleve_login'";
							$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab' WHERE id_eleve='$reg_no_gep'";
							$update=mysql_query($sql);
							if($update){
								$msg="Association de l'�l�ve avec l'�tablissement $reg_etab r�ussie.";
							}
							else{
								$msg="Echec de l'association de l'�l�ve avec l'�tablissement $reg_etab.";
							}
						}
					}
				}
			}
		}
		unset($definir_etab);
	}


	//================================================
	// Validation de modifications dans le formulaire de nom, pr�nom,...
	if (isset($_POST['is_posted']) and ($_POST['is_posted'] == "1")) {
		check_token();

		// D�termination du format de la date de naissance
		$call_eleve_test = mysql_query("SELECT naissance FROM eleves WHERE 1");
		$test_eleve_naissance = @mysql_result($call_eleve_test, "0", "naissance");
		$format = strlen($test_eleve_naissance);


		// Cas de la cr�ation d'un �l�ve
		$reg_nom = trim($reg_nom);
		$reg_prenom = trim($reg_prenom);
		$reg_email = trim($reg_email);
		if ($reg_resp1 == '(vide)') $reg_resp1 = '';
		if (!preg_match ("/^[0-9]{4}$/", $birth_year)) {$birth_year = "1900";}
		if (!preg_match ("/^[0-9]{2}$/", $birth_month)) {$birth_month = "01";}
		if (!preg_match ("/^[0-9]{2}$/", $birth_day)) {$birth_day = "01";}
		if ($format == '10') {
			// YYYY-MM-DD
			$reg_naissance = $birth_year."-".$birth_month."-".$birth_day." 00:00:00";
		}
		else {
			if ($format == '8') {
				// YYYYMMDD
				$reg_naissance = $birth_year.$birth_month.$birth_day;
				settype($reg_naissance,"integer");
			} else {
				// Format inconnu
				$reg_naissance = $birth_year.$birth_month.$birth_day;
			}
		}
		
		//gestion de la date de sortie de l'�l�ve
		//echo "date_sortie_annee".$date_sortie_annee."<br/>";
		//echo "date_sortie_mois".$date_sortie_mois."<br/>";
		//echo "date_sortie_jour".$date_sortie_jour."<br/>";
		
		if (!preg_match ("/^[0-9]{4}$/", $date_sortie_annee)) {$date_sortie_annee = "0000";}
		if (!preg_match ("/^[0-9]{2}$/", $date_sortie_mois)) {$date_sortie_mois = "00";}
		if (!preg_match ("/^[0-9]{2}$/", $date_sortie_jour)) {$date_sortie_jour = "00";}
		
		//echo "date_sortie_annee".$date_sortie_annee."<br/>";
		//echo "date_sortie_mois".$date_sortie_mois."<br/>";
		//echo "date_sortie_jour".$date_sortie_jour."<br/>";

		//cr�ation de la chaine au format timestamp
		$date_de_sortie_eleve = $date_sortie_annee."-".$date_sortie_mois."-".$date_sortie_jour." 00:00:00"; 
		
		
		//===========================
		//AJOUT:
		if(!isset($msg)){$msg="";}
		//===========================

		$continue = 'yes';
		if (($reg_nom == '') or ($reg_prenom == '')) {
			$msg = "Les champs nom et pr�nom sont obligatoires.";
			$continue = 'no';
		}

		//$msg.="\$reg_login=$reg_login<br />";
		//if(isset($eleve_login)){$msg.="\$eleve_login=$eleve_login<br />";}

		// $reg_login non vide correspond � un nouvel �l�ve.
		// On a saisi un login avant de valider
		if (($continue == 'yes') and (isset($reg_login))) {
			// CE CAS NE DOIT PLUS SE PRODUIRE PUISQUE J'AI AJOUT� UNE PAGE add_eleve.php D'APRES L'ANCIENNE modify_eleve.php
			// On doit n�cessairement passer dans le else plus bas...

			//echo "\$reg_login=$reg_login<br/>";

			$msg = '';
			$ok = 'yes';
			if (preg_match("/^[a-zA-Z_]{1}[a-zA-Z0-9_]{0,11}$/", $reg_login)) {
				if ($reg_no_gep != '') {
					$test1 = mysql_query("SELECT login FROM eleves WHERE elenoet='$reg_no_gep'");
					$count1 = mysql_num_rows($test1);
					if ($count1 != "0") {
						//$msg .= "Erreur : un �l�ve ayant le m�me num�ro GEP existe d�j�.<br />";
						$msg .= "Erreur : un �l�ve ayant le m�me num�ro interne Sconet (elenoet) existe d�j�.<br />";
						$ok = 'no';
					}
				}

				if ($reg_no_nat != '') {
					$test2 = mysql_query("SELECT login FROM eleves WHERE no_gep='$reg_no_nat'");
					$count2 = mysql_num_rows($test2);
					if ($count2 != "0") {
						$msg .= "Erreur : un �l�ve ayant le m�me num�ro national existe d�j�.";
						$ok = 'no';
					}
				}

				if ($ok == 'yes') {
					$test = mysql_query("SELECT login FROM eleves WHERE login='$reg_login'");
					$count = mysql_num_rows($test);
					if ($count == "0") {

						if(!isset($ele_id)){
							// GENERER UN ele_id...
							/*
							$sql="SELECT MAX(ele_id) max_ele_id FROM eleves";
							$res_ele_id_eleve=mysql_query($sql);
							$max_ele_id = mysql_result($call_resp , 0, "max_ele_id");

							$sql="SELECT MAX(ele_id) max_ele_id FROM responsables2";
							$res_ele_id_responsables2=mysql_query($sql);
							$max_ele_id2 = mysql_result($call_resp , 0, "max_ele_id");

							if($max_ele_id2>$max_ele_id){$max_ele_id=$max_ele_id2;}
							$ele_id=$max_ele_id+1;
							*/
							// PB si on fait ensuite un import sconet le pers_id risque de ne pas correspondre... de provoquer des collisions.
							// QUAND ON LES METS A LA MAIN, METTRE UN ele_id, pers_id,... n�gatifs?

							// PREFIXER D'UN a...

							$sql="SELECT ele_id FROM eleves WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
							$res_ele_id_eleve=mysql_query($sql);
							if(mysql_num_rows($res_ele_id_eleve)>0){
								$tmp=0;
								$lig_ele_id_eleve=mysql_fetch_object($res_ele_id_eleve);
								$tmp=substr($lig_ele_id_eleve->ele_id,1);
								$tmp++;
								$max_ele_id=$tmp;
							}
							else{
								$max_ele_id=1;
							}

							$sql="SELECT ele_id FROM responsables2 WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
							$res_ele_id_responsables2=mysql_query($sql);
							if(mysql_num_rows($res_ele_id_responsables2)>0){
								$tmp=0;
								$lig_ele_id_responsables2=mysql_fetch_object($res_ele_id_responsables2);
								$tmp=substr($lig_ele_id_responsables2->ele_id,1);
								$tmp++;
								$max_ele_id2=$tmp;
							}
							else{
								$max_ele_id2=1;
							}

							$tmp=max($max_ele_id,$max_ele_id2);
							$ele_id="e".sprintf("%09d",max($max_ele_id,$max_ele_id2));
						}

						/*
						$reg_data1 = mysql_query("INSERT INTO eleves SET
							no_gep = '".$reg_no_nat."',
							nom='".$reg_nom."',
							prenom='".$reg_prenom."',
							login='".$reg_login."',
							sexe='".$reg_sexe."',
							naissance='".$reg_naissance."',
							elenoet = '".$reg_no_gep."',
							ereno = '".$reg_resp1."',
							ele_id = '".$ele_id."'
							");
						*/
						$reg_data1 = mysql_query("INSERT INTO eleves SET
							no_gep = '".$reg_no_nat."',
							nom='".$reg_nom."',
							prenom='".$reg_prenom."',
							email='".$reg_email ."',
							login='".$reg_login."',
							sexe='".$reg_sexe."',
							naissance='".$reg_naissance."',
							elenoet = '".$reg_no_gep."',
							ele_id = '".$ele_id."'
							");


						/*
						$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$reg_resp1'";
						$test_resp1=mysql_query($sql);
						if(mysql_num_rows($test_resp1)>0){
							// Il y a d�j� une association �l�ve/responsable (c'est bizarre pour un �l�ve que l'on inscrit maintenant???)
							$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$reg_resp1' AND resp_legal='2'";
							$test_resp1b=mysql_query($sql);
							if(mysql_num_rows($test_resp1b)==1){
								// Le responsable 2 devient responsable 1.
								$temoin_maj_resp="";
								$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND pers_id!='$reg_resp1' AND resp_legal='1'";
								$test_resp1c=mysql_query($sql);
								if(mysql_num_rows($test_resp1c)==1){
									$lig_autre_resp=mysql_fetch_object($test_resp1c);
									$sql="UPDATE responsables2 SET resp_legal='2' WHERE ele_id='$ele_id' AND pers_id='$lig_autre_resp->pers_id'";
									$res_update=mysql_query($sql);
									if(!$res_update){
										$msg.="Erreur lors de la mise � jour du responsable $lig_autre_resp->pers_id en responsable l�gal n�2.<br />\n";
										$temoin_maj_resp="PB";
									}
								}

								if($temoin_maj_resp==""){
									$sql="UPDATE responsables2 SET resp_legal='1' WHERE ele_id='$ele_id' AND pers_id='$reg_resp1'";
									$res_update=mysql_query($sql);
									if(!$res_update){
										$msg.="Erreur lors de la mise � jour du responsable $reg_resp1 en responsable l�gal n�1.<br />\n";
									}
								}
							}
							// Sinon, l'association est d�j� la bonne... pas de changement.
						}
						else{
							// Il n'y a pas encore d'association entre cet �l�ve et ce responsable
							$temoin_maj_resp="";
							$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND pers_id!='$reg_resp1' AND resp_legal='1'";
							$test_resp1c=mysql_query($sql);
							//if(mysql_num_rows($test_resp1c)==1){
							if(mysql_num_rows($test_resp1c)>0){
								$lig_autre_resp=mysql_fetch_object($test_resp1c);

								// Y avait-il un autre responsable l�gal n�2?
								$sql="DELETE FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='2'";
								$res_menage=mysql_query($sql);
								if(!$res_menage){
									$msg.="Erreur lors de la suppression de l'association avec le pr�c�dent responsable l�gal n�2.<br />";
									$temoin_maj_resp="PB";
								}
								else{
									// L'ancien resp_legal 1 devient resp_legal 2
									$sql="UPDATE responsables2 SET resp_legal='2' WHERE ele_id='$ele_id' AND pers_id='$lig_autre_resp->pers_id'";
									$res_update=mysql_query($sql);
									if(!$res_update){
										$msg.="Erreur lors de la mise � jour du responsable $lig_autre_resp->pers_id en responsable l�gal n�2.<br />\n";
										$temoin_maj_resp="PB";
									}
								}
							}

							if($temoin_maj_resp==""){
								$sql="INSERT INTO responsables2 SET ele_id='$ele_id', pers_id='$reg_resp1', resp_legal='1', pers_contact='1'";
								$reg_data2b=mysql_query($sql);
								if(!$reg_data2b){
									$msg.="Erreur lors de la mise � jour du responsable $reg_resp1 en responsable l�gal n�1.<br />\n";
								}
							}
						}
						*/

						// R�gime:
						$reg_data3 = mysql_query("INSERT INTO j_eleves_regime SET login='$reg_login', doublant='-', regime='d/p'");
						/*
						// R�gime et �tablissement d'origine:
						$call_test = mysql_query("SELECT * FROM j_eleves_etablissements WHERE id_eleve = '$reg_login'");
						$count2 = mysql_num_rows($call_test);
						if ($count2 == "0") {
							if ($reg_etab != "(vide)") {
								$reg_data2 = mysql_query("INSERT INTO j_eleves_etablissements VALUES ('$reg_login','$reg_etab')");
							}
						} else {
							if ($reg_etab != "(vide)") {
								$reg_data2 = mysql_query("UPDATE j_eleves_etablissements SET id_etablissement = '$reg_etab' WHERE id_eleve='$reg_login'");
							} else {
								$reg_data2 = mysql_query("DELETE FROM j_eleves_etablissements WHERE id_eleve='$reg_login'");
							}
						}
						*/
						if ((!$reg_data1) or (!$reg_data3)) {
							$msg = "Erreur lors de l'enregistrement des donn�es";
						} elseif ($mode == "unique") {
							$mess=rawurlencode("El�ve enregistr� !");
							header("Location: index.php?msg=$mess");
							die();
						} elseif ($mode == "multiple") {
							$mess=rawurlencode("El�ve enregistr�.Vous pouvez saisir l'�l�ve suivant.");
							header("Location: modify_eleve.php?mode=multiple&msg=$mess");
							die();
						}
					} else {
						$msg="Un �l�ve portant le m�me identifiant existe d�ja !";
					}
				}
			} else {
				$msg="L'identifiant choisi est constitu� au maximum de 12 caract�res : lettres, chiffres ou \"_\" et ne doit pas commencer par un chiffre !";
			}
		} else if ($continue == 'yes') {
			// C'est une mise � jour pour un �l�ve qui existait d�j� dans la table 'eleves'.
			$sql="UPDATE eleves SET date_sortie = '$date_de_sortie_eleve', no_gep = '$reg_no_nat', nom='$reg_nom',prenom='$reg_prenom',sexe='$reg_sexe',naissance='".$reg_naissance."', ereno='".$reg_resp1."', elenoet = '".$reg_no_gep."'";

			$temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve="n";
			if(getSettingValue('mode_email_ele')=='mon_compte') {
				$sql_test="SELECT email FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
				$res_email_utilisateur_ele=mysql_query($sql_test);
				if(mysql_num_rows($res_email_utilisateur_ele)>0) {
					// Faut-il ins�rer un email? si l'email utilisateur est vide?
				}
				else {
					$sql.=",email='$reg_email'";
					$temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve="y";
				}
			}
			else {
				$sql.=",email='$reg_email'";
			}
			$sql.=" WHERE login='".$eleve_login."'";

			// On nettoie les windozeries
			$reg_data = mysql_query($sql);
			if (!$reg_data) {
				$msg = "Erreur lors de l'enregistrement des donn�es";
			} elseif((getSettingValue('mode_email_ele')!='mon_compte')||($temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve=="y")) {
				// On met � jour la table utilisateurs si un compte existe pour cet �l�ve
				$test_login = mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '".$eleve_login ."'"), 0);
				if ($test_login > 0) {
					$res = mysql_query("UPDATE utilisateurs SET nom='".$reg_nom."', prenom='".$reg_prenom."', email='".$reg_email."' WHERE login = '".$eleve_login."'");
					//$msg.="TEMOIN test_login puis update<br />";
				}
			}




			if(isset($reg_doublant)){
				if ($reg_doublant!='R') {$reg_doublant = '-';}

				$call_regime = mysql_query("SELECT * FROM j_eleves_regime WHERE login='$eleve_login'");
				$nb_test_regime = mysql_num_rows($call_regime);
				if ($nb_test_regime == 0) {
					// On va se retrouver �ventuellement avec un r�gime vide... cela peut-il poser pb?
					$reg_data = mysql_query("INSERT INTO j_eleves_regime SET login='$eleve_login', doublant='$reg_doublant';");
					if (!($reg_data)) {$reg_ok = 'no';}
				} else {
					$reg_data = mysql_query("UPDATE j_eleves_regime SET doublant = '$reg_doublant' WHERE login='$eleve_login';");
					if (!($reg_data)) {$reg_ok = 'no';}
				}
			}

			if(isset($reg_regime)){
				if (($reg_regime!='i-e')&&($reg_regime!='int.')&&($reg_regime!='ext.')&&($reg_regime!='d/p')) {
					$reg_regime='d/p';
				}

				$call_regime = mysql_query("SELECT * FROM j_eleves_regime WHERE login='$eleve_login'");
				$nb_test_regime = mysql_num_rows($call_regime);
				if ($nb_test_regime == 0) {
					$reg_data = mysql_query("INSERT INTO j_eleves_regime SET login='$eleve_login', regime='$reg_regime'");
					if (!($reg_data)) {$reg_ok = 'no';}
				} else {
					$reg_data = mysql_query("UPDATE j_eleves_regime SET regime = '$reg_regime'  WHERE login='$eleve_login'");
					if (!($reg_data)) {$reg_ok = 'no';}
				}
			}



			/*
			$call_test = mysql_query("SELECT * FROM j_eleves_etablissements WHERE id_eleve = '$eleve_login'");
			$count = mysql_num_rows($call_test);
			if ($count == "0") {
				if ($reg_etab != "(vide)") {
					$reg_data = mysql_query("INSERT INTO j_eleves_etablissements VALUES ('$eleve_login','$reg_etab')");
				}
			} else {
				if ($reg_etab != "(vide)") {
					$reg_data = mysql_query("UPDATE j_eleves_etablissements SET id_etablissement = '$reg_etab' WHERE id_eleve='$eleve_login'");
				} else {
					$reg_data = mysql_query("DELETE FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'");
				}
			}
			*/

			if (!$reg_data) {
				$msg = "Erreur lors de l'enregistrement des donn�es ! ";
			} else {
				//$msg = "Les modifications ont bien �t� enregistr�es !";
				// MODIF POUR AFFICHER MES TEMOINS...
				$msg .= "Les modifications ont bien �t� enregistr�es ! ";
			}


			// Envoi de la photo
			if(isset($reg_no_gep)){
				//echo "\$reg_no_gep=$reg_no_gep<br />";
				if($reg_no_gep!=""){
					if(strlen(preg_replace("/[0-9]/","",$reg_no_gep))==0) {
						if(isset($_POST['suppr_filephoto'])){
							if($_POST['suppr_filephoto']=='y'){

								// R�cup�ration du nom de la photo en tenant compte des histoires des z�ro 02345.jpg ou 2345.jpg
								$photo=nom_photo($reg_no_gep);
/*
								if("$photo"!=""){
									if(unlink("../photos/eleves/$photo")){
 */
								if($photo){
									if(unlink($photo)){
										$msg.="La photo ".$photo." a �t� supprim�e. ";
									}
									else{
										$msg.="Echec de la suppression de la photo ".$photo." ";
									}
								}
								else{
									$msg.="Echec de la suppression de la photo correspondant � $reg_no_gep (<i>non trouv�e</i>) ";
								}
							}
						}

						// Contr�ler qu'un seul �l�ve a bien cet elenoet???
						$sql="SELECT 1=1 FROM eleves WHERE elenoet='$reg_no_gep'";
						$test=mysql_query($sql);
						$nb_elenoet=mysql_num_rows($test);
						if($nb_elenoet==1){
							// filephoto
							if(isset($_FILES['filephoto'])){
								$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
								if($filephoto_tmp!=""){
									$filephoto_name=$_FILES['filephoto']['name'];
									$filephoto_size=$_FILES['filephoto']['size'];
									// Tester la taille max de la photo?

									if(is_uploaded_file($filephoto_tmp)){
										$dest_file=$rep_photos.$reg_no_gep.".jpg";
										//echo "\$dest_file=$dest_file<br />";
										$source_file=stripslashes("$filephoto_tmp");
										$res_copy=copy("$source_file" , "$dest_file");
										if($res_copy){
											$msg.="Mise en place de la photo effectu�e.";
										}
										else{
											$msg.="Erreur lors de la mise en place de la photo.";
										}

										if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
											// si le redimensionnement des photos est activ� on redimenssionne
											$source = imagecreatefromjpeg($dest_file); // La photo est la source

											if (getSettingValue("active_module_trombinoscopes_rt")=='') {
												$destination = imagecreatetruecolor(getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"));
											} // On cr�e la miniature vide

											if (getSettingValue("active_module_trombinoscopes_rt")!='') {
												$destination = imagecreatetruecolor(getSettingValue("h_resize_trombinoscopes"), getSettingValue("l_resize_trombinoscopes"));
											} // On cr�e la miniature vide

											// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
											$largeur_source = imagesx($source);
											$hauteur_source = imagesy($source);
											$largeur_destination = imagesx($destination);
											$hauteur_destination = imagesy($destination);

											// On cr�e la miniature
											imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
											if (getSettingValue("active_module_trombinoscopes_rt")!='') {
												$degrees = getSettingValue("active_module_trombinoscopes_rt");
												// $destination = imagerotate($destination,$degrees);
												$destination = ImageRotateRightAngle($destination,$degrees);
											}
											// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
											imagejpeg($destination, $dest_file,100);
										}
									}
									else{
										$msg.="Erreur lors de l'upload de la photo.";
									}
								}
							}
						}
						elseif($nb_elenoet==0){
								//$msg.="Le num�ro GEP de l'�l�ve n'est pas enregistr� dans la table 'eleves'.";
								$msg.="Le num�ro interne Sconet (elenoet) de l'�l�ve n'est pas enregistr� dans la table 'eleves'.";
						}
						else{
							//$msg.="Le num�ro GEP est commun � plusieurs �l�ves. C'est une anomalie.";
							$msg.="Le num�ro interne Sconet (elenoet) est commun � plusieurs �l�ves. C'est une anomalie.";
						}
					}
					else{
						//$msg.="Le num�ro GEP propos� contient des caract�res non num�riques.";
						$msg.="Le num�ro interne Sconet (elenoet) propos� contient des caract�res non num�riques.";
					}
				}
			}


			$temoin_ele_id="";
			$sql="SELECT ele_id FROM eleves WHERE login='$eleve_login'";
			$res_ele_id_eleve=mysql_query($sql);
			if(mysql_num_rows($res_ele_id_eleve)==0){
				$msg.="Erreur: Le champ ele_id n'est pas pr�sent. Votre table 'eleves' n'a pas l'air � jour.<br />";
				$temoin_ele_id="PB";
			}
			else{
				$lig_tmp=mysql_fetch_object($res_ele_id_eleve);
				$ele_id=$lig_tmp->ele_id;
			}


			/*
			if($temoin_ele_id==""){
				$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$reg_resp1'";
				$test_resp1=mysql_query($sql);
				if(mysql_num_rows($test_resp1)>0){
					// Il y a d�j� une association �l�ve/responsable (c'est bizarre pour un �l�ve que l'on inscrit maintenant???)
					$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$reg_resp1' AND resp_legal='2'";
					$test_resp1b=mysql_query($sql);
					if(mysql_num_rows($test_resp1b)==1){
						// Le responsable 2 devient responsable 1.
						$temoin_maj_resp="";
						$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND pers_id!='$reg_resp1' AND resp_legal='1'";
						$test_resp1c=mysql_query($sql);
						if(mysql_num_rows($test_resp1c)==1){
							$lig_autre_resp=mysql_fetch_object($test_resp1c);
							$sql="UPDATE responsables2 SET resp_legal='2' WHERE ele_id='$ele_id' AND pers_id='$lig_autre_resp->pers_id'";
							$res_update=mysql_query($sql);
							if(!$res_update){
								$msg.="Erreur lors de la mise � jour du responsable $lig_autre_resp->pers_id en responsable l�gal n�2.<br />\n";
								$temoin_maj_resp="PB";
							}
						}

						if($temoin_maj_resp==""){
							$sql="UPDATE responsables2 SET resp_legal='1' WHERE ele_id='$ele_id' AND pers_id='$reg_resp1'";
							$res_update=mysql_query($sql);
							if(!$res_update){
								$msg.="Erreur lors de la mise � jour du responsable $reg_resp1 en responsable l�gal n�1.<br />\n";
							}
						}
					}
					// Sinon, l'association est d�j� la bonne... pas de changement.
				}
				else{
					// Il n'y a pas encore d'association entre cet �l�ve et ce responsable
					$temoin_maj_resp="";
					$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND pers_id!='$reg_resp1' AND resp_legal='1'";
					$test_resp1c=mysql_query($sql);
					//if(mysql_num_rows($test_resp1c)==1){
					if(mysql_num_rows($test_resp1c)>0){
						$lig_autre_resp=mysql_fetch_object($test_resp1c);

						// Y avait-il un autre responsable l�gal n�2?
						$sql="DELETE FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='2'";
						$res_menage=mysql_query($sql);
						if(!$res_menage){
							$msg.="Erreur lors de la suppression de l'association avec le pr�c�dent responsable l�gal n�2.<br />";
							$temoin_maj_resp="PB";
						}
						else{
							// L'ancien resp_legal 1 devient resp_legal 2
							$sql="UPDATE responsables2 SET resp_legal='2' WHERE ele_id='$ele_id' AND pers_id='$lig_autre_resp->pers_id'";
							$res_update=mysql_query($sql);
							if(!$res_update){
								$msg.="Erreur lors de la mise � jour du responsable $lig_autre_resp->pers_id en responsable l�gal n�2.<br />\n";
								$temoin_maj_resp="PB";
							}
						}
					}

					if($temoin_maj_resp==""){
						$sql="INSERT INTO responsables2 SET ele_id='$ele_id', pers_id='$reg_resp1', resp_legal='1', pers_contact='1'";
						$reg_data2b=mysql_query($sql);
						if(!$reg_data2b){
							$msg.="Erreur lors de la mise � jour du responsable $reg_resp1 en responsable l�gal n�1.<br />\n";
						}
					}
				}
			}
			*/




	/*
			$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$reg_resp1'";
			$test_resp1=mysql_query($sql);
			if(mysql_num_rows($test_resp1)){
				$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$ele_id' AND pers_id='$reg_resp1' AND resp_legal='2'";
				$test_resp1b=mysql_query($sql);
				if(mysql_num_rows($test_resp1b)==1){
					$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND pers_id!='$reg_resp1' AND resp_legal='1'";
					$test_resp1c=mysql_query($sql);
					if(mysql_num_rows($test_resp1c)==1){
						$lig_autre_resp=mysql_fetch_object($test_resp1c);
						$sql="UPDATE responsables2 SET resp_legal='2' WHERE ele_id='$ele_id' AND pers_id='$lig_autre_resp->pers_id'";
						$res_update=mysql_query($sql);
					}

					$sql="UPDATE responsables2 SET resp_legal='1' WHERE ele_id='$ele_id' AND pers_id='$reg_resp1'";
					$res_update=mysql_query($sql);
				}
			}
			else{
				$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND pers_id!='$reg_resp1' AND resp_legal='1'";
				$test_resp1c=mysql_query($sql);
				if(mysql_num_rows($test_resp1c)==1){
					$lig_autre_resp=mysql_fetch_object($test_resp1c);
					$sql="UPDATE responsables2 SET resp_legal='2' WHERE ele_id='$ele_id' AND pers_id='$lig_autre_resp->pers_id'";
					$res_update=mysql_query($sql);
				}

				$sql="INSERT INTO responsables2 SET ele_id='$ele_id', pers_id='$reg_resp1', resp_legal='1', pers_contact='1'";
				$reg_data2b=mysql_query($sql);
			}

			// AJOUTER DES TESTS DE SUCCES DE LA M�J.
	*/

		}
	}

	//================================================
}
elseif($_SESSION['statut']=="professeur"){
	if (isset($_POST['is_posted']) and ($_POST['is_posted'] == "1")) {
		if(!isset($msg)){$msg="";}

		// Envoi de la photo
		if(isset($reg_no_gep)) {
			if($reg_no_gep!="") {
				if(strlen(preg_replace("/[0-9]/","",$reg_no_gep))==0){
					if(isset($_POST['suppr_filephoto'])) {
						check_token();
						if($_POST['suppr_filephoto']=='y'){

							// R�cup�ration du nom de la photo en tenant compte des histoires des z�ro 02345.jpg ou 2345.jpg
							$photo=nom_photo($reg_no_gep);
/*
							if("$photo"!=""){
								if(unlink("../photos/eleves/$photo")){
 */
							if($photo){
								if(unlink($photo)){
									$msg.="La photo ".$photo." a �t� supprim�e. ";
								}
								else{
									$msg.="Echec de la suppression de la photo ".$photo." ";
								}
							}
							else{
								$msg.="Echec de la suppression de la photo correspondant � $reg_no_gep (<i>non trouv�e</i>) ";
							}
						}
					}

					// Contr�ler qu'un seul �l�ve a bien cet elenoet???
					$sql="SELECT 1=1 FROM eleves WHERE elenoet='$reg_no_gep'";
					$test=mysql_query($sql);
					$nb_elenoet=mysql_num_rows($test);
					if($nb_elenoet==1){
						// filephoto
						if(isset($_FILES['filephoto'])){
							check_token();
							$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
							if($filephoto_tmp!=""){
								$filephoto_name=$_FILES['filephoto']['name'];
								$filephoto_size=$_FILES['filephoto']['size'];
								// Tester la taille max de la photo?

								if(is_uploaded_file($filephoto_tmp)){
									$dest_file=$rep_photos.$reg_no_gep.jpg;
									$source_file=stripslashes("$filephoto_tmp");
									$res_copy=copy("$source_file" , "$dest_file");
									if($res_copy){
										$msg.="Mise en place de la photo effectu�e.";
									}
									else{
										$msg.="Erreur lors de la mise en place de la photo.";
									}
								}
								else{
									$msg.="Erreur lors de l'upload de la photo.";
								}
							}
						}
					}
					elseif($nb_elenoet==0){
							//$msg.="Le num�ro GEP de l'�l�ve n'est pas enregistr� dans la table 'eleves'.";
							$msg.="Le num�ro interne Sconet (elenoet) de l'�l�ve n'est pas enregistr� dans la table 'eleves'.";
					}
					else{
						//$msg.="Le num�ro GEP est commun � plusieurs �l�ves. C'est une anomalie.";
						$msg.="Le num�ro interne Sconet (elenoet) est commun � plusieurs �l�ves. C'est une anomalie.";
					}
				}
				else{
					//$msg.="Le num�ro GEP propos� contient des caract�res non num�riques.";
					$msg.="Le num�ro interne Sconet (elenoet) propos� contient des caract�res non num�riques.";
				}
			}
		}
	}
}

// On appelle les informations de l'utilisateur pour les afficher :
if (isset($eleve_login)) {
    $call_eleve_info = mysql_query("SELECT * FROM eleves WHERE login='$eleve_login'");
    $eleve_nom = mysql_result($call_eleve_info, "0", "nom");
    $eleve_prenom = mysql_result($call_eleve_info, "0", "prenom");
    $eleve_email = mysql_result($call_eleve_info, "0", "email");

	if(getSettingValue('mode_email_ele')=='mon_compte') {
		$sql_test="SELECT email FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
		$res_email_utilisateur_ele=mysql_query($sql_test);
		if(mysql_num_rows($res_email_utilisateur_ele)>0) {
			$tmp_lig_email=mysql_fetch_object($res_email_utilisateur_ele);

			if($tmp_lig_email->email!="") {
				if($tmp_lig_email->email!=$eleve_email) {
					//check_token();
					$sql="UPDATE eleves SET email='$tmp_lig_email->email' WHERE login='$eleve_login';";
					$update=mysql_query($sql);
					if(!$update) {
						if(!isset($msg)) {$msg="";}
						$msg.="Erreur lors de la mise � jour du mail de l'�l�ve d'apr�s son compte d'utilisateur<br />$eleve_email -&gt; $tmp_lig_email->email<br />";
					}
					else {
						if(!isset($msg)) {$msg="";}
						$msg.="Mise � jour de l'email de $eleve_login dans la table 'eleves' d'apr�s l'email de son compte utilisateur<br />$eleve_email -&gt; $tmp_lig_email->email<br />";
					}
				}
				$eleve_email = $tmp_lig_email->email;
			}
		}
	}

    $eleve_sexe = mysql_result($call_eleve_info, "0", "sexe");
    $eleve_naissance = mysql_result($call_eleve_info, "0", "naissance");
    if (strlen($eleve_naissance) == 10) {
        // YYYY-MM-DD
        $eleve_naissance_annee = substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = substr($eleve_naissance, 5, 2);
        $eleve_naissance_jour = substr($eleve_naissance, 8, 2);
    } elseif (strlen($eleve_naissance) == 8 ) {
        // YYYYMMDD
        $eleve_naissance_annee = substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = substr($eleve_naissance, 4, 2);
        $eleve_naissance_jour = substr($eleve_naissance, 6, 2);
    } elseif (strlen($eleve_naissance) == 19 ) {
        // YYYY-MM-DD xx:xx:xx
        $eleve_naissance_annee = substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = substr($eleve_naissance, 5, 2);
        $eleve_naissance_jour = substr($eleve_naissance, 8, 2);
    } else {
        // Format inconnu
        $eleve_naissance_annee = "??";
        $eleve_naissance_mois = "??";
        $eleve_naissance_jour = "????";
    }

    $eleve_lieu_naissance = mysql_result($call_eleve_info, "0", "lieu_naissance");

	//Date de sortie de l'�l�ve (timestamps), � z�ro par d�faut
	$eleve_date_de_sortie =mysql_result($call_eleve_info, "0", "date_sortie"); 
	
	//echo "Date de sortie de l'�l�ve dans la base :  $eleve_date_de_sortie <br/>";
    //conversion en seconde (timestamp)
    $eleve_date_de_sortie_time=strtotime($eleve_date_de_sortie);

	if ($eleve_date_de_sortie!=0) {
	//r�cup�ration du jour, du mois et de l'ann�e
	    $eleve_date_sortie_jour=date('j', $eleve_date_de_sortie_time); 
	    $eleve_date_sortie_mois=date('m', $eleve_date_de_sortie_time);
	    $eleve_date_sortie_annee=date('Y', $eleve_date_de_sortie_time); 
		//echo "La date n'est pas nulle J:$eleve_date_sortie_jour   M:$eleve_date_sortie_mois   A:$eleve_date_sortie_annee";
	} else {
	    $eleve_date_sortie_jour="00"; 
	    $eleve_date_sortie_mois="00";
	    $eleve_date_sortie_annee="0000"; 
	}
	
    //$eleve_no_resp = mysql_result($call_eleve_info, "0", "ereno");
    $reg_no_nat = mysql_result($call_eleve_info, "0", "no_gep");
    $reg_no_gep = mysql_result($call_eleve_info, "0", "elenoet");
	$reg_ele_id = mysql_result($call_eleve_info, "0", "ele_id");

    //$call_etab = mysql_query("SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$eleve_login' and e.id = j.id_etablissement)");
    $id_etab=0;
	if($reg_no_gep!="") {
		$call_etab = mysql_query("SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$reg_no_gep' and e.id = j.id_etablissement)");
	    $id_etab = @mysql_result($call_etab, "0", "id");
	}

	//echo "SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$eleve_login' and e.id = j.id_etablissement)<br />";

	//=========================
	// AJOUT: boireaus 20071107
	$sql="SELECT * FROM j_eleves_regime WHERE login='$eleve_login';";
	//echo "$sql<br />\n";
	$res_regime=mysql_query($sql);
	if(mysql_num_rows($res_regime)>0) {
		$lig_tmp=mysql_fetch_object($res_regime);
		$reg_regime=$lig_tmp->regime;
		$reg_doublant=$lig_tmp->doublant;
	}
	else {
		$reg_regime="d/p";
		$reg_doublant="-";
	}
	//=========================


	if(!isset($ele_id)){
		$ele_id=mysql_result($call_eleve_info, "0", "ele_id");
	}

	$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='1'";
	//echo "$sql<br />\n";
	$res_resp1=mysql_query($sql);
	if(mysql_num_rows($res_resp1)>0) {
		$lig_no_resp1=mysql_fetch_object($res_resp1);
		$eleve_no_resp1=$lig_no_resp1->pers_id;
	}
	else {
		$eleve_no_resp1=0;
	}
	//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

	$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='2'";
	//echo "$sql<br />\n";
	$res_resp2=mysql_query($sql);
	if(mysql_num_rows($res_resp2)>0){
		$lig_no_resp2=mysql_fetch_object($res_resp2);
		$eleve_no_resp2=$lig_no_resp2->pers_id;
	}
	else {
		$eleve_no_resp2=0;
	}


} else {
    if (isset($reg_nom)) {$eleve_nom = $reg_nom;}
    if (isset($reg_prenom)) {$eleve_prenom = $reg_prenom;}
    if (isset($reg_email)) {$eleve_email = $reg_email;}
    if (isset($reg_sexe)) {$eleve_sexe = $reg_sexe;}
    if (isset($reg_no_nat)) {$reg_no_nat = $reg_no_nat;}
    if (isset($reg_no_gep)) {$reg_no_gep = $reg_no_gep;}
    if (isset($birth_year)) {$eleve_naissance_annee = $birth_year;}
    if (isset($birth_month)) {$eleve_naissance_mois = $birth_month;}
    if (isset($birth_day)) {$eleve_naissance_jour = $birth_day;}

    if (isset($reg_lieu_naissance)) {$eleve_lieu_naissance=$reg_lieu_naissance;}

    //$eleve_no_resp = 0;
    $eleve_no_resp1 = 0;
    $eleve_no_resp2 = 0;
    $id_etab = 0;

	//=========================
	// AJOUT: boireaus 20071107
	// On ne devrait pas passer par l�.
	// Quand on arrive sur modify_elve.php, le login de l'�l�ve doit exister.
	$reg_regime="d/p";
	$reg_doublant="-";
	//=========================
}


$themessage  = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Gestion des �l�ves | Ajouter/Modifier une fiche �l�ve";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<div align='center'>
	<div id='message_target_blank' style='color:red;'></div>
</div>\n";

/*
if ((isset($order_type)) and (isset($quelles_classes))) {
    echo "<p class=bold><a href=\"index.php?quelles_classes=$quelles_classes&amp;order_type=$order_type\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
} else {
    echo "<p class=bold><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
}
*/

/*
// D�sactiv� pour permettre de renseigner un ELENOET manquant pour une conversion avec sconet
// Cela a en revanche �t� conserv� sur la page index.php
// On ne devrait donc arriver ici lorsqu'une conversion est r�clam�e qu'en venant de conversion.php pour remplir un ELENOET
if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0){
		echo "<p>Une conversion des donn�es �l�ves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>Une conversion des donn�es �l�ves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p>Une conversion des donn�es �l�ves/responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}
*/


?>
<!--form enctype="multipart/form-data" action="modify_eleve.php" method=post-->
<?php

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
	//eleve_login=$eleve_login&amp;definir_resp=1
	if(isset($definir_resp)){
		if(!isset($valider_choix_resp)){

			echo "<p class=bold><a href=\"modify_eleve.php?eleve_login=$eleve_login\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

			echo "<p>Choix du responsable l�gal <b>$definir_resp</b> pour <b>".casse_prenom($eleve_prenom)." ".strtoupper($eleve_nom)."</b></p>\n";

			$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
			$afficher_tous_les_resp=isset($_POST['afficher_tous_les_resp']) ? $_POST['afficher_tous_les_resp'] : "n";
			$critere_recherche=preg_replace("/[^a-zA-Z�������������ܽ�����������������_ -]/", "", $critere_recherche);

			if($critere_recherche==""){
				$critere_recherche=substr($eleve_nom,0,3);
			}

			$nb_resp=isset($_POST['nb_resp']) ? $_POST['nb_resp'] : 20;
			if(strlen(preg_replace("/[0-9]/","",$nb_resp))!=0) {
				$nb_resp=20;
			}
			$num_premier_resp_rech=isset($_POST['num_premier_resp_rech']) ? $_POST['num_premier_resp_rech'] : 0;
			if(strlen(preg_replace("/[0-9]/","",$num_premier_resp_rech))!=0) {
				$num_premier_resp_rech=0;
			}

			echo "<form enctype='multipart/form-data' name='form_rech' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			echo "<input type='hidden' name='definir_resp' value='$definir_resp' />\n";
			echo "<p align='center'><input type='submit' name='filtrage' value='Afficher' /> les ";
			echo "<input type='text' name='nb_resp' value='$nb_resp' size='3' />\n";
			echo " responsables dont le <b>nom</b> contient: ";
			echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
			echo " � partir de l'enregistrement ";
			echo "<input type='text' name='num_premier_resp_rech' value='$num_premier_resp_rech' size='4' />\n";
			echo "</p>\n";


			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";


			echo "<input type='hidden' name='afficher_tous_les_resp' id='afficher_tous_les_resp' value='n' />\n";
			echo "<p align='center'><input type='button' name='afficher_tous' value='Afficher tous les responsables' onClick=\"document.getElementById('afficher_tous_les_resp').value='y'; document.form_rech.submit();\" /></p>\n";
			echo "</form>\n";


			echo "<form enctype='multipart/form-data' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			echo "<input type='hidden' name='definir_resp' value='$definir_resp' />\n";

			if($definir_resp==1){
				$pers_id=$eleve_no_resp1;
			}
			else{
				$pers_id=$eleve_no_resp2;
			}

			//$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom,ra.* FROM responsables2 r, resp_adr ra, resp_pers rp WHERE r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id ORDER BY rp.nom, rp.prenom";
			//$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp ORDER BY rp.nom, rp.prenom";
			$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp";
			if($afficher_tous_les_resp!='y'){
				if($critere_recherche!=""){
					$sql.=" WHERE rp.nom like '%".$critere_recherche."%'";
				}
			}
			$sql.=" ORDER BY rp.nom, rp.prenom";
			if($afficher_tous_les_resp!='y'){
				$sql.=" LIMIT $num_premier_resp_rech, $nb_resp";
			}
			$call_resp=mysql_query($sql);
			$nombreligne = mysql_num_rows($call_resp);
			// si la table des responsables est non vide :
			if ($nombreligne != 0) {
				echo "<p align='center'><input type='submit' name='valider_choix_resp' value='Enregistrer' /></p>\n";
				echo "<table align='center' class='boireaus' summary='Responsable'>\n";
				echo "<tr>\n";
				echo "<td><input type='radio' name='reg_resp".$definir_resp."' value='' onchange='changement();' /></td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'><b>Responsable l�gal $definir_resp</b></td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'><b>Adresse</b></td>\n";
				echo "</tr>\n";

				$cpt=1;
				$alt=1;
				while($lig_resp=mysql_fetch_object($call_resp)){
					$alt=$alt*(-1);
					//if($cpt%2==0){$couleur="silver";}else{$couleur="white";}
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td><input type='radio' name='reg_resp".$definir_resp."' value='$lig_resp->pers_id' ";
					if($lig_resp->pers_id==$pers_id){
						echo "checked ";
					}
					echo "onchange='changement();' /></td>\n";
					//echo "<td><a href='../responsables/modify_resp.php?pers_id=$lig_resp->pers_id' target='_blank'>".strtoupper($lig_resp->nom)." ".ucfirst(strtolower($lig_resp->prenom))."</a></td>\n";
					echo "<td><a href='../responsables/modify_resp.php?pers_id=$lig_resp->pers_id&amp;quitter_la_page=y' target='_blank'>".strtoupper($lig_resp->nom)." ".casse_prenom($lig_resp->prenom)."</a></td>\n";
					echo "<td>";

					$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp WHERE rp.pers_id='$lig_resp->pers_id' AND rp.adr_id=ra.adr_id";
					$res_adr=mysql_query($sql);
					if(mysql_num_rows($res_adr)==0){
						// L'adresse du responsable n'est pas d�finie:
						//echo "<font color='red'>L'adresse du responsable l�gal n'est pas d�finie</font>: <a href='../responsables/modify_resp.php?pers_id=$lig_resp->pers_id' target='_blank'>D�finir l'adresse du responsable l�gal</a>\n";
						echo "&nbsp;";
					}
					else{
						$chaine_adr1="";
						$lig_adr=mysql_fetch_object($res_adr);
						if("$lig_adr->adr1"!=""){$chaine_adr1.="$lig_adr->adr1, ";}
						if("$lig_adr->adr2"!=""){$chaine_adr1.="$lig_adr->adr2, ";}
						if("$lig_adr->adr3"!=""){$chaine_adr1.="$lig_adr->adr3, ";}
						if("$lig_adr->adr4"!=""){$chaine_adr1.="$lig_adr->adr4, ";}
						if("$lig_adr->cp"!=""){$chaine_adr1.="$lig_adr->cp, ";}
						if("$lig_adr->commune"!=""){$chaine_adr1.="$lig_adr->commune";}
						if("$lig_adr->pays"!=""){$chaine_adr1.=" (<i>$lig_adr->pays</i>)";}
						echo $chaine_adr1;
					}

					echo "</td>\n";
					echo "</tr>\n";
					$cpt++;
				}

				echo "</table>\n";
				echo "<p align='center'><input type='submit' name='valider_choix_resp' value='Enregistrer' /></p>\n";
			}
			else{
				echo "<p>Aucun responsable n'est d�fini, ou aucun responsable correspond � la recherche.</p>\n";
			}

			echo "<p>Si le responsable l�gal ne figure pas dans la liste, vous pouvez l'ajouter � la base<br />\n";
			echo "(<i>apr�s avoir, le cas �ch�ant, sauvegard� cette fiche</i>)<br />\n";
			if($_SESSION['statut']=="scolarite") {
				echo "en vous rendant dans [<a href='../responsables/index.php'>Gestion des fiches responsables �l�ves</a>]</p>\n";
			}
			else{
				echo "en vous rendant dans [Gestion des bases-><a href='../responsables/index.php'>Gestion des responsables �l�ves</a>]</p>\n";
			}

			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";


			echo "</form>\n";
		}
		else{
			// On valide l'enregistrement...
			// ... il faut le faire plus haut avant le header...
		}
		require("../lib/footer.inc.php");
		die();
	}



	//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";



	if(isset($definir_etab)){
		if(!isset($valider_choix_etab)){
			echo "<p class=bold><a href=\"modify_eleve.php?eleve_login=$eleve_login\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

			//====================================================
			$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : (isset($_GET['critere_recherche']) ? $_GET['critere_recherche'] : "");
			$afficher_tous_les_etab=isset($_POST['afficher_tous_les_etab']) ? $_POST['afficher_tous_les_etab'] : (isset($_GET['afficher_tous_les_etab']) ? $_GET['afficher_tous_les_etab'] : "n");
			//$critere_recherche=my_ereg_replace("[^0-9a-zA-Z�������������ܽ�����������������_ -]", "", $critere_recherche);
			$critere_recherche=preg_replace("/[^0-9a-zA-Z�������������ܽ�����������������_ %-]/", "", preg_replace("/ /","%",$critere_recherche));
			// Saisir un espace ou % pour plusieurs portions du champ de recherche ou pour une apostrophe
			$champ_rech=isset($_POST['champ_rech']) ? $_POST['champ_rech'] : (isset($_GET['champ_rech']) ? $_GET['champ_rech'] : "nom");
			$tab_champs_recherche_autorises=array('nom','cp','ville','id');
			if(!in_array($champ_rech,$tab_champs_recherche_autorises)) {$champ_rech="nom";}

			/*
			if($critere_recherche==""){
				$critere_recherche=substr($eleve_nom,0,3);
			}
			*/

			$nb_etab=isset($_POST['nb_etab']) ? $_POST['nb_etab'] : (isset($_GET['nb_etab']) ? $_GET['nb_etab'] : 20);
			if(strlen(preg_replace("/[0-9]/","",$nb_etab))!=0) {
				$nb_etab=20;
			}
			$num_premier_etab_rech=isset($_POST['num_premier_etab_rech']) ? $_POST['num_premier_etab_rech'] : (isset($_GET['num_premier_etab_rech']) ? $_GET['num_premier_etab_rech'] : 0);
			if(strlen(preg_replace("/[0-9]/","",$num_premier_etab_rech))!=0) {
				$num_premier_etab_rech=0;
			}

			$etab_order_by=isset($_POST['etab_order_by']) ? $_POST['etab_order_by'] : (isset($_GET['etab_order_by']) ? $_GET['etab_order_by'] : "ville,nom");
			$tab_champs_etab_order_by_autorises=array('ville,nom','id','nom','cp');
			if(!in_array($etab_order_by,$tab_champs_etab_order_by_autorises)) {$etab_order_by="ville,nom";}


			echo "<div align='center'>\n";
			echo "<div style='width:90%; border: 1px solid black;'>\n";
			echo "<!-- Formulaire de recherche/filtrage parmi les �tablissements -->\n";
			echo "<form enctype='multipart/form-data' name='form_rech' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			echo "<input type='hidden' name='definir_etab' value='$definir_etab' />\n";
			echo "<table border='0' summary='Filtrage'>\n";
			echo "<tr>\n";
			echo "<td valign='top'>\n";
			//echo "<p align='center'>";
			echo "<input type='submit' name='filtrage' value='Afficher' /> les ";
			echo "<input type='text' name='nb_etab' value='$nb_etab' size='3' />\n";
			echo " �tablissements dont ";
			echo "</td>\n";
			echo "<td valign='top'>\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_nom' value='nom' ";
			if($champ_rech=="nom") {echo "checked ";}
			echo "/> <label for='champ_rech_nom' style='cursor: pointer;'>le <b>nom</b></label><br />\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_rne' value='id' ";
			if($champ_rech=="id") {echo "checked ";}
			echo "/> <label for='champ_rech_rne' style='cursor: pointer;'>le <b>RNE</b></label><br />\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_cp' value='cp' ";
			if($champ_rech=="cp") {echo "checked ";}
			echo "/> <label for='champ_rech_cp' style='cursor: pointer;'>le <b>code postal</b></label><br />\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_ville' value='ville' ";
			if($champ_rech=="ville") {echo "checked ";}
			echo "/> <label for='champ_rech_ville' style='cursor: pointer;'>la <b>ville</b></label>\n";

			echo "</td>\n";
			echo "<td valign='top'>\n";
			echo " contient: ";
			echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
			echo "<br />\n";
			echo "&nbsp;&nbsp;&nbsp;� partir de l'enregistrement ";
			echo "<input type='text' name='num_premier_etab_rech' value='$num_premier_etab_rech' size='4' />\n";
			//echo "</p>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";


			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";


			echo "<input type='hidden' name='afficher_tous_les_etab' id='afficher_tous_les_etab' value='n' />\n";
			echo "<p align='center'>";

			echo "<input type='submit' name='filtrage2' value='Afficher la s�lection' /> ou ";

			echo "<input type='button' name='afficher_tous' value='Afficher tous les �tablissements' onClick=\"document.getElementById('afficher_tous_les_etab').value='y'; document.form_rech.submit();\" /></p>\n";
			echo "</form>\n";
			echo "</div>\n";
			echo "</div>\n";
			//====================================================


			echo "<!-- Formulaire de choix de l'�tablissement -->\n";
			echo "<form enctype='multipart/form-data' name='form_choix_etab' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<p>Choix de l'�tablissement d'origine pour <b>".casse_prenom($eleve_prenom)." ".strtoupper($eleve_nom)."</b></p>\n";

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			//echo "<input type='hidden' name='reg_no_gep' value='$reg_no_gep' />\n";
			echo "<input type='hidden' name='definir_etab' value='y' />\n";


			//$sql="SELECT * FROM etablissements ORDER BY ville,nom";
			$sql="SELECT * FROM etablissements e";
			if($afficher_tous_les_etab!='y'){
				if($critere_recherche!=""){
					$sql.=" WHERE e.$champ_rech LIKE '%".$critere_recherche."%'";
				}
			}
			$sql.=" ORDER BY $etab_order_by";
			if($afficher_tous_les_etab!='y'){
				$sql.=" LIMIT $num_premier_etab_rech, $nb_etab";
			}
			//echo "$sql<br />";

			$chaine_param_tri="";
			if(isset($eleve_login)) {$chaine_param_tri.= "&amp;eleve_login=$eleve_login";}
			if(isset($definir_etab)) {$chaine_param_tri.= "&amp;definir_etab=$definir_etab";}
			if(isset($nb_etab)) {$chaine_param_tri.= "&amp;nb_etab=$nb_etab";}
			if(isset($champ_rech)) {$chaine_param_tri.= "&amp;champ_rech=$champ_rech";}
			if(isset($critere_recherche)) {$chaine_param_tri.= "&amp;critere_recherche=$critere_recherche";}
			if(isset($num_premier_etab_rech)) {$chaine_param_tri.= "&amp;num_premier_etab_rech=$num_premier_etab_rech";}
			if(isset($order_type)) {$chaine_param_tri.= "&amp;order_type=$order_type";}
			if(isset($quelles_classes)) {$chaine_param_tri.= "&amp;quelles_classes=$quelles_classes";}
			if(isset($motif_rech)) {$chaine_param_tri.= "&amp;motif_rech=$motif_rech";}
			if(isset($afficher_tous_les_etab)) {$chaine_param_tri.= "&amp;afficher_tous_les_etab=$afficher_tous_les_etab";}

			$call_etab=mysql_query($sql);
			$nombreligne = mysql_num_rows($call_etab);
			if ($nombreligne != 0) {
				echo "<p align='center'><input type='submit' name='valider_choix_etab' value='Valider' /></p>\n";
				echo "<table align='center' class='boireaus' border='1' summary='Etablissement'>\n";
				echo "<tr>\n";
				echo "<td><input type='radio' name='reg_etab' value='' /></td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=id";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>RNE</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>\n";
				echo "<b>Niveau</b>\n";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>\n";
				echo "<b>Type</b>\n";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=nom";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>Nom</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=cp";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>Code postal</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=ville,nom";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>Ville</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "</tr>\n";

				$temoin_checked="n";

				$cpt=1;
				$alt=1;
				while($lig_etab=mysql_fetch_object($call_etab)){
					//if($cpt%2==0){$couleur="silver";}else{$couleur="white";}
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					/*
					echo "<td style='text-align:center; background-color:$couleur;'><input type='radio' name='reg_etab' value='$lig_etab->id' ";
					if($lig_etab->id==$id_etab){
						echo "checked ";
					}
					echo "onchange='changement();' /></td>";
					echo "<td style='text-align:center; background-color:$couleur;'><a href='../etablissements/modify_etab.php?id=$lig_etab->id' target='_blank'>$lig_etab->id</a></td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->niveau</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->type</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->nom</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->cp</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->ville</td>\n";
					*/
					echo "<td><input type='radio' name='reg_etab' value='$lig_etab->id' ";
					if($lig_etab->id==$id_etab){
						echo "checked ";
						$temoin_checked="y";
					}
					echo "onchange='changement();' /></td>\n";
					echo "<td><a href='../etablissements/modify_etab.php?id=$lig_etab->id' target='_blank'>$lig_etab->id</a></td>\n";
					echo "<td>$lig_etab->niveau</td>\n";
					echo "<td>$lig_etab->type</td>\n";
					echo "<td>$lig_etab->nom</td>\n";
					echo "<td>$lig_etab->cp</td>\n";
					echo "<td>$lig_etab->ville</td>\n";

					echo "</tr>\n";
					$cpt++;
				}

				if(($temoin_checked=="n")&&($id_etab!=0)) {
					$sql="SELECT * FROM etablissements WHERE id='$id_etab';";
					$res_etab=mysql_query($sql);
					if(mysql_num_rows($res_etab)>0) {
						$lig_etab=mysql_fetch_object($res_etab);

						$alt=$alt*(-1);
						echo "<tr class='lig$alt white_hover'>\n";
						echo "<td><input type='radio' name='reg_etab' value='$lig_etab->id' ";
						echo "checked ";
						echo "onchange='changement();' /></td>\n";
						echo "<td><a href='../etablissements/modify_etab.php?id=$lig_etab->id' target='_blank'>$lig_etab->id</a></td>\n";
						echo "<td>$lig_etab->niveau</td>\n";
						echo "<td>$lig_etab->type</td>\n";
						echo "<td>$lig_etab->nom</td>\n";
						echo "<td>$lig_etab->cp</td>\n";
						echo "<td>$lig_etab->ville</td>\n";

						echo "</tr>\n";
					}
				}

				echo "</table>\n";
				echo "<p align='center'><input type='submit' name='valider_choix_etab' value='Valider' /></p>\n";
			}
			else{
				echo "<p>Aucun �tablissement n'est d�fini</p>\n";
			}

			echo "<p>Si un �tablissement ne figure pas dans la liste, vous pouvez l'ajouter � la base<br />\n";
			echo "en vous rendant dans [Gestion des bases-><a href='../etablissements/index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion des �tablissements</a>]</p>\n";


			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";


			echo "</form>\n";
		}
		else{
			// On valide l'enregistrement...
			// ... il faut le faire plus haut avant le header...
		}
		require("../lib/footer.inc.php");
		die();
	}
}


echo "<p class=bold><a href=\"index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
if ((isset($order_type)) and (isset($quelles_classes))) {
    //echo "<p class=bold><a href=\"index.php?quelles_classes=$quelles_classes&amp;order_type=$order_type\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

    echo " | <a href=\"index.php?quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "&amp;order_type=$order_type\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour � votre recherche</a>\n";
}
/*
else {
    echo "<p class=bold><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
}
*/
echo "</p>\n";


echo "<form enctype='multipart/form-data' name='form_rech' action='modify_eleve.php' method='post'>\n";
echo add_token_field();

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

//echo "\$eleve_login=$eleve_login<br />";

if(isset($eleve_login)) {
	//$sql="SELECT 1=1 FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
	$sql="SELECT 1=1 FROM utilisateurs WHERE login='$eleve_login';";
	$test_compte=mysql_query($sql);
	if(mysql_num_rows($test_compte)>0) {$compte_eleve_existe="y";} else {$compte_eleve_existe="n";}

	if(($compte_eleve_existe=="y")&&(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite"))) {
		//$journal_connexions=isset($_POST['journal_connexions']) ? $_POST['journal_connexions'] : (isset($_GET['journal_connexions']) ? $_GET['journal_connexions'] : 'n');
		//$duree=isset($_POST['duree']) ? $_POST['duree'] : NULL;
	
		echo "<div style='float:right; width:; height:;'><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;journal_connexions=y#connexion' title='Journal des connexions'><img src='../images/icons/document.png' width='16' height='16' alt='Journal des connexions' /></a></div>\n";
	}
}

//echo "<table border='1'>\n";
echo "<table summary='Informations �l�ve'>\n";
echo "<tr>\n";
echo "<td>\n";

echo "<table cellpadding='5' summary='Infos 1'>\n";
echo "<tr>\n";

$photo_largeur_max=150;
$photo_hauteur_max=150;

function redimensionne_image($photo){
	global $photo_largeur_max, $photo_hauteur_max;

	// prendre les informations sur l'image
	$info_image=getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur=$info_image[0];
	$hauteur=$info_image[1];

	// calcule le ratio de redimensionnement
	$ratio_l=$largeur/$photo_largeur_max;
	$ratio_h=$hauteur/$photo_hauteur_max;
	$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

	// d�finit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur=round($largeur/$ratio);
	$nouvelle_hauteur=round($hauteur/$ratio);

	return array($nouvelle_largeur, $nouvelle_hauteur);
}


if (isset($eleve_login)) {
	echo "<th style='text-align:left;'>Identifiant GEPI * : </th>
	<td>".$eleve_login."<input type=hidden name='eleve_login' size=20 ";
	if ($eleve_login) echo "value='$eleve_login'";
	echo " /></td>\n";
} else {
	echo "<th style='text-align:left;'>Identifiant GEPI * : </th>
	<td><input type=text name=reg_login size=20 value=\"\" onchange='changement();' /></td>\n";
}

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
	echo "</tr>
	<tr>
		<th style='text-align:left;'>Nom * : </th>
		<td><input type=text name='reg_nom' size=20 ";
	if (isset($eleve_nom)) {
		echo "value=\"".$eleve_nom."\"";
	}
	echo " onchange='changement();' /></td>
	</tr>
	<tr>
		<th style='text-align:left;'>Pr�nom * : </th>
		<td><input type=text name='reg_prenom' size=20 ";
	if (isset($eleve_prenom)) {
		echo "value=\"".$eleve_prenom."\"";
	}
	echo " onchange='changement();' /></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "	<th style='text-align:left;'>Email : </th>\n";
	echo "	<td>";

	if((isset($compte_eleve_existe))&&($compte_eleve_existe=="y")&&(getSettingValue('mode_email_ele')=='mon_compte')) {
		if (isset($eleve_email)) {
			echo $eleve_email;
		}
		else {
			echo "&nbsp;";
		}
	}
	else {
		echo "<input type=text name='reg_email' size=18 ";
		if (isset($eleve_email)) {
			echo "value=\"".$eleve_email."\"";
		}
		echo " onchange='changement();' />";
	}
	if((isset($eleve_email))&&($eleve_email!='')) {
		$tmp_date=getdate();
		echo " <a href='mailto:".$eleve_email."?subject=GEPI&amp;body=";
		if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
		echo ",%0d%0aCordialement.'>";
		echo "<img src='../images/imabulle/courrier.jpg' width='20' height='15' alt='Envoyer un courriel' border='0' />";
		echo "</a>";
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th style='text-align:left;'>Identifiant National : </th>\n";
    echo "<td><input type='text' name='reg_no_nat' size='20' ";
    if (isset($reg_no_nat)) echo "value=\"".$reg_no_nat."\"";
    echo " onchange='changement();' /></td>\n";

	echo "</tr>\n";

    //echo "<tr><td>Num�ro GEP : </td><td><input type=text name='reg_no_gep' size=20 ";
    echo "<tr><th style='text-align:left;'>Num�ro interne Sconet (<i>elenoet</i>) : </th><td><input type='text' name='reg_no_gep' size='20' ";
    if (isset($reg_no_gep)) echo "value=\"".$reg_no_gep."\"";
    echo " onchange='changement();' /></td>\n";
	echo "</tr>\n";
	
    echo "<tr><th style='text-align:left;'>Num�ro interne Sconet (<i>ele_id</i>) : </th><td>";
    if (isset($reg_ele_id)) {echo $reg_ele_id;}
    echo "</td>\n";
	echo "</tr>\n";
	
	//Date de sortie de l'�tablissement
    echo "<tr><th style='text-align:left;'>Date de sortie de l'�tablissement : <br/>(respecter format JJ/MM/AAAA)</th>";
	echo "<td><div class='norme'>";	
	echo "Jour  <input type='text' name='date_sortie_jour' size='2' onchange='changement();' value=\""; if (isset($eleve_date_sortie_jour) and ($eleve_date_sortie_jour!="00") ) echo $eleve_date_sortie_jour; echo "\"/>";
	echo " Mois  <input type='text' name='date_sortie_mois' size='2' onchange='changement();' value=\""; if (isset($eleve_date_sortie_mois) and ($eleve_date_sortie_mois!="00")) echo $eleve_date_sortie_mois; echo "\"/>";
	echo " Ann�e <input type='text' name='date_sortie_annee' size='4' onchange='changement();' value=\""; if (isset($eleve_date_sortie_annee) and ($eleve_date_sortie_annee!="0000")) echo $eleve_date_sortie_annee; echo "\"/>";
	echo "</td>\n";
	echo "</tr>\n";

}
else {
	echo "</tr>
	<tr>
		<th style='text-align:left;'>Nom * : </th>
		<td>";
	if (isset($eleve_nom)) {
		echo "$eleve_nom";
	}
	echo "</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Pr�nom * : </th>
		<td>";
	if (isset($eleve_prenom)) {
		echo "$eleve_prenom";
	}
	echo "</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Email : </th>
		<td>";
	if (isset($eleve_email)) {
		echo "$eleve_email";
	}
	echo "</td>
	</tr>
	<tr>
    <th style='text-align:left;'>Identifiant National : </th>\n";
    echo "<td>";
    if (isset($reg_no_nat)) echo "$reg_no_nat";
    echo "</td>\n";

	echo "</tr>\n";

    //echo "<tr><td>Num�ro GEP : </td><td><input type=text name='reg_no_gep' size=20 ";
    echo "<tr><th style='text-align:left;'>Num�ro interne Sconet (<i>elenoet</i>) : </th><td>";
    if (isset($reg_no_gep)) {
		echo "$reg_no_gep";
		if(getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes'){
			// N�cessaire pour les photos:
			echo "<input type='hidden' name='reg_no_gep' size='20' value=\"".$reg_no_gep."\" />\n";
		}
	}
    echo "</td>\n";
	echo "</tr>\n";
	
	if ($eleve_date_de_sortie!=0) {
		//Date de sortie de l'�tablissement
	    echo "<tr><th style='text-align:left;'>Date de sortie de l'�tablissement : <br/></th>";
		echo "<td><div class='norme'>";	
		
		if ((isset($eleve_date_sortie_jour)) and ($eleve_date_sortie_jour!="00")) echo $eleve_date_sortie_jour."/";
		if ((isset($eleve_date_sortie_mois)) and ($eleve_date_sortie_mois!="00")) echo $eleve_date_sortie_mois."/";
		if ((isset($eleve_date_sortie_annee)) and ($eleve_date_sortie_annee!="00")) echo $eleve_date_sortie_annee; 
		echo "</td>\n";
		echo "</tr>\n";
    }
}
echo "</table>\n";

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";
//echo "<td>\$reg_no_gep=$reg_no_gep</td>";
if(isset($reg_no_gep)){
	// R�cup�ration du nom de la photo en tenant compte des histoires des z�ro 02345.jpg ou 2345.jpg
	$photo=nom_photo($reg_no_gep);

	echo "<td align='center'>\n";
	$temoin_photo="non";
	//echo "<td>\$photo=$photo</td>";
	if($photo){
		//$photo="../photos/eleves/".$photo;
		if(file_exists($photo)){
			$temoin_photo="oui";
			//echo "<td>\n";
			echo "<div align='center'>\n";
			$dimphoto=redimensionne_image($photo);
			//echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />';
			echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border: 3px solid #FFFFFF;" alt="" />';
			//echo "</td>\n";
			//echo "<br />\n";
			echo "</div>\n";
			echo "<div style='clear:both;'></div>\n";
		}
	}

	//echo "getSettingValue(\"GepiAccesGestPhotoElevesProfP\")=".getSettingValue("GepiAccesGestPhotoElevesProfP")."<br />";
  if ((getSettingValue("active_module_trombinoscopes")=='y') and (($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")||(($_SESSION['statut']=="professeur")&&(getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes')))){
		echo "<div align='center'>\n";
		//echo "<span id='lien_photo' style='font-size:xx-small;'>";
		echo "<div id='lien_photo' style='border: 1px solid black; padding: 5px; margin: 5px;'>";
		echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';document.getElementById('lien_photo').style.display='none';return false;\">";
		if($temoin_photo=="oui"){
			//echo "Modifier le fichier photo</a>\n";
			echo "Modifier le fichier photo</a>\n";
		}
		else{
			//echo "Envoyer un fichier photo</a>\n";
			echo "Envoyer<br />un fichier<br />photo</a>\n";
		}
		//echo "</span>\n";
		echo "</div>\n";
		echo "<div id='div_upload_photo' style='display:none;'>";
		echo "<input type='file' name='filephoto' />\n";
		if("$photo"!=""){
			if(file_exists($photo)){
				echo "<br />\n";
				echo "<input type='checkbox' name='suppr_filephoto' id='suppr_filephoto' value='y' onchange='changement();' /><label for='suppr_filephoto' style='cursor:pointer;'> Supprimer la photo existante</label>\n";
			}
		}
		echo "</div>\n";
		echo "</div>\n";
	}
	echo "</td>\n";
}


// Lien vers les inscriptions � des groupes:
if(isset($eleve_login)){
	echo "<td valign='top'>\n";
	// style='border: 1px solid black; text-align:center;'

	//echo "\$reg_regime=$reg_regime<br />";
	//echo "\$reg_doublant=$reg_doublant<br />";

	if($_SESSION['statut']=="professeur") {
		echo "<table border='0' summary='Infos 2'>\n";

		echo "<tr><th style='text-align:left;'>N�(e) le: </th><td>$eleve_naissance_jour/$eleve_naissance_mois/$eleve_naissance_annee</td></tr>\n";
		if ($eleve_sexe == "M") {
			echo "<tr><th style='text-align:left;'>Sexe: </th><td>Masculin</td></tr>\n";
		}
		elseif($eleve_sexe == "F"){
			echo "<tr><th style='text-align:left;'>Sexe: </th><td>F�minin</td></tr>\n";
		}

		echo "<tr><th style='text-align:left;'>R�gime: </th><td>";
		if ($reg_regime == 'i-e') {
			echo "Interne-extern�";
		}
		elseif ($reg_regime == 'int.') {
			echo "Interne";
		}
		elseif ($reg_regime == 'd/p') {
			echo "Demi-pensionnaire";
		}
		elseif ($reg_regime == 'ext.') {
			echo "Externe";
		}
		echo "</td></tr>\n";

		if ($reg_doublant == 'R') {echo "<tr><th style='text-align:left;'>Redoublant</th><td>Oui</td></tr>\n";}

		echo "</table>\n";

	}
	else{
		//=========================
		// AJOUT: boireaus 20071107
		echo "<table style='border-collapse: collapse; border: 1px solid black;' align='center'  summary='R�gime'>\n";
		echo "<tr>\n";
		echo "<th>R�gime: </th>\n";
		echo "<td style='text-align: center; border: 0px;'>I-ext<br /><input type='radio' name='reg_regime' value='i-e' ";
		if ($reg_regime == 'i-e') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>Int<br/><input type='radio' name='reg_regime' value='int.' ";
		if ($reg_regime == 'int.') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>D/P<br/><input type='radio' name='reg_regime' value='d/p' ";
		if ($reg_regime == 'd/p') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>Ext<br/><input type='radio' name='reg_regime' value='ext.' ";
		if ($reg_regime == 'ext.') {echo " checked";}
		echo " onchange='changement();' /></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";
		//echo "<tr><td>&nbsp;</td></tr>\n";

		echo "<table style='border-collapse: collapse; border: 1px solid black;' align='center' summary='Redoublement'>\n";
		echo "<tr>\n";
		echo "<th>Redoublant: </th>\n";
		echo "<td style='text-align: center; border: 0px;'>O<br /><input type='radio' name='reg_doublant' value='R' ";
		if ($reg_doublant == 'R') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>N<br /><input type='radio' name='reg_doublant' value='-' ";
		if ($reg_doublant == '-') {echo " checked";}
		echo " onchange='changement();' /></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";
		//=========================

		echo "<div style='border: 1px solid black;'>\n";
		$sql="SELECT jec.id_classe,c.classe, jec.periode FROM j_eleves_classes jec, classes c WHERE jec.login='$eleve_login' AND jec.id_classe=c.id GROUP BY jec.id_classe ORDER BY jec.periode";
		$res_grp1=mysql_query($sql);
		if(mysql_num_rows($res_grp1)==0){
			echo "L'�l�ve n'est encore associ� � aucune classe.";
		}
		else{
			while($lig_classe=mysql_fetch_object($res_grp1)){
				//echo "Enseignements suivis en <a href='../classes/eleve_options.php?login_eleve=$eleve_login&amp;id_classe=$lig_classe->id_classe' target='_blank'>$lig_classe->classe</a><br />\n";
				echo "<a href='../classes/eleve_options.php?login_eleve=$eleve_login&amp;id_classe=$lig_classe->id_classe&amp;quitter_la_page=y' target='_blank'>Enseignements suivis</a> en $lig_classe->classe\n";
				echo "<br />\n";

				//echo "D�finir/consulter <a href='../classes/classes_const.php?id_classe=$lig_classe->id_classe&amp;quitter_la_page=y' target='_blank'>le r�gime, le professeur principal, le CPE responsable</a> de l'�l�ve.\n";
				//echo "<br />\n";
			}
		}
		echo "</div>\n";

		//=========================
		//$test_compte_actif=check_compte_actif($eleve_login);
		//if($test_compte_actif!=0) {
		if((isset($compte_eleve_existe))&&($compte_eleve_existe=="y")&&($_SESSION['statut']=="administrateur")) {
			echo "<div style='margin-top: 0.5em; text-align:center; border: 1px solid black;'>\n";
			echo affiche_actions_compte($eleve_login);
			echo "</div>\n";
		}
		//=========================

	}
	echo "</td>\n";
}

echo "</tr>\n";
echo "</table>\n";

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

if (($reg_no_gep == '') and (isset($eleve_login))) {
   //echo "<font color=red>ATTENTION : Cet �l�ve ne poss�de pas de num�ro GEP. Vous ne pourrez pas importer les absences � partir des fichiers GEP pour cet �l�ves.</font>\n";
   echo "<font color='red'>ATTENTION : Cet �l�ve ne poss�de pas de num�ro interne Sconet (<i>elenoet</i>). Vous ne pourrez pas importer les absences � partir des fichiers GEP/Sconet pour cet �l�ve.<br />Vous ne pourrez pas d�finir l'�tablissement d'origine de l'�l�ve.<br />Cet �l�ve ne pourra pas figurer dans le module trombinoscope.</font>\n";

	$sql="select value from setting where name='import_maj_xml_sconet'";
	$test_sconet=mysql_query($sql);
	if(mysql_num_rows($test_sconet)>0){
		$lig_tmp=mysql_fetch_object($test_sconet);
		if($lig_tmp->value=='1'){
			echo "<br />";
			echo "<font color='red'>Vous ne pourrez pas non plus effectuer les mises � jour de ses informations depuis Sconet<br />(<i>l'ELENOET et l'ELE_ID ne correspondront pas aux donn�es de Sconet</i>).</font>\n";
		}
	}
}
//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

/*
if($_SESSION['statut']=="professeur") {
	if ($eleve_sexe == "M") {
		echo "<b>Sexe:</b> Masculin<br />";
	}
	elseif($eleve_sexe == "F"){
		echo "<b>Sexe:</b> F�minin<br />";
	}

	echo "<b>N�(e) le</b>: $eleve_naissance<br />\n";
}
else{
*/
if($_SESSION['statut']!="professeur") {
?>
<center>
<!--table border = '1' CELLPADDING = '5'-->
<table class='boireaus' cellpadding='5' summary='Sexe'>
<tr><td><div class='norme'><b>Sexe :</b> <br />
<?php
if (!(isset($eleve_sexe))) {$eleve_sexe="M";}
?>
<label for='reg_sexeM' style='cursor: pointer;'><input type=radio name=reg_sexe id='reg_sexeM' value=M <?php if ($eleve_sexe == "M") { echo "CHECKED" ;} ?> onchange='changement();' /> Masculin</label>
<label for='reg_sexeF' style='cursor: pointer;'><input type=radio name=reg_sexe id='reg_sexeF' value=F <?php if ($eleve_sexe == "F") { echo "CHECKED" ;} ?> onchange='changement();' /> F�minin</label>
</div></td>

<td><div class='norme'>
<b>Date de naissance (respecter format 00/00/0000) :</b> <br />
Jour <input type=text name=birth_day size=2 onchange='changement();' value=<?php if (isset($eleve_naissance_jour)) echo $eleve_naissance_jour;?> />
Mois<input type=text name=birth_month size=2 onchange='changement();' value=<?php if (isset($eleve_naissance_mois)) echo $eleve_naissance_mois;?> />
Ann�e<input type=text name=birth_year size=4 onchange='changement();' value=<?php if (isset($eleve_naissance_annee)) echo $eleve_naissance_annee;?> />

<?php
if(getSettingValue('ele_lieu_naissance')=='y') {
	echo "<br />\n";
	echo "<b>Lieu de naissance&nbsp;:</b> ";
	if(isset($eleve_lieu_naissance)) {echo get_commune($eleve_lieu_naissance,1);}
	else {echo "<span style='color:red'>Non d�fini</span>";}
	echo "\n";
}
?>
</div></td>

</tr>
</table></center>

<p><b>Remarque</b> :
<br />- Les champs * sont obligatoires.</p>
<?php
}


echo "<input type=hidden name=is_posted value=\"1\" />\n";
if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
if (isset($eleve_login)) echo "<input type=hidden name=eleve_login value=\"$eleve_login\" />\n";
if (isset($mode)) echo "<input type=hidden name=mode value=\"$mode\" />\n";

if($_SESSION['statut']=='professeur'){
  if ((getSettingValue("active_module_trombinoscopes")=='y') && (getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes')){
		echo "<center><input type=submit value=Enregistrer /></center>\n";
	}
}
else{
	echo "<center><input type=submit value=Enregistrer /></center>\n";
}
echo "</form>\n";

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";


if(isset($eleve_login)){
	//$sql="SELECT rp.nom,rp.prenom,rp.pers_id,ra.* FROM responsables2 r, resp_adr ra, resp_pers rp WHERE r.resp_legal='1' AND r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id ORDER BY rp.nom, rp.prenom";
	//$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom,ra.* FROM responsables2 r, resp_adr ra, resp_pers rp WHERE r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id ORDER BY rp.nom, rp.prenom";
	$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp ORDER BY rp.nom, rp.prenom";
	$call_resp=mysql_query($sql);
	$nombreligne = mysql_num_rows($call_resp);
	// si la table des responsables est non vide :
	if ($nombreligne != 0) {

		echo "<br />\n";
		echo "<hr />\n";
		echo "<h3>Envoi des bulletins par voie postale</h3>\n";

		//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

		echo "<i>Si vous n'envoyez pas les bulletins scolaires par voie postale, vous pouvez ignorer cette rubrique.</i>";
		echo "<br />\n<br />\n";

		$temoin_tableau="";
		$chaine_adr1='';
		// Lorsque le $eleve_no_resp1 est non num�rique (cas sans sconet), on a p000000012 et il consid�re que p000000012==0
		// Il faut comparer des chaines de caract�res.
		//if($eleve_no_resp1==0){
		if("$eleve_no_resp1"=="0"){
			// Le responsable 1 n'est pas d�fini:
			echo "<p>Le responsable l�gal 1 n'est pas d�fini";
			if($_SESSION['statut']=="professeur") {
				echo ".";
			}
			else{
				echo ": <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1";
				if (isset($order_type)) {echo "&amp;order_type=$order_type";}
				if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
				if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D�finir le responsable l�gal 1</a>";
			}
			echo "</p>\n";
		}
		else{
			$sql="SELECT nom,prenom FROM resp_pers WHERE pers_id='$eleve_no_resp1'";
			$res_resp=mysql_query($sql);
			if(mysql_num_rows($res_resp)==0){
				// Bizarre: Le responsable 1 n'est pas d�fini:
				echo "<p>Le responsable l�gal 1 n'est pas d�fini";
				if($_SESSION['statut']=="professeur") {
					echo ".";
				}
				else{
					echo ": <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D�finir le responsable l�gal 1</a>";
				}
				echo "</p>\n";
			}
			else{
				$temoin_tableau="oui";
				$lig_resp=mysql_fetch_object($res_resp);
				echo "<table border='0' summary='Responsable l�gal 1'>\n";
				echo "<tr valign='top'>\n";
				echo "<td rowspan='2'>Le responsable l�gal 1 est: </td>\n";
				echo "<td>";
				if($_SESSION['statut']=="professeur") {
					echo casse_prenom($lig_resp->prenom)." ".strtoupper($lig_resp->nom);
				}
				else{
					//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1' target='_blank'>";
					//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y' target='_blank' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\">";
					echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y' target='_blank' onclick=\"return confirm_abandon (this, change, '$themessage');\">";
					echo casse_prenom($lig_resp->prenom)." ".strtoupper($lig_resp->nom);
					echo "</a>";
				}
				echo "</td>\n";

				if($_SESSION['statut']!="professeur") {
					//echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1'>Modifier l'association</a></td>\n";
					echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					//echo "'>Modifier le responsable</a></td>\n";
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Changer de responsable</a></td>\n";
				}
				echo "</tr>\n";

				echo "<tr valign='top'>\n";
				// La 1�re colonne est dans le rowspan

				$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp WHERE rp.pers_id='$eleve_no_resp1' AND rp.adr_id=ra.adr_id";
				$res_adr=mysql_query($sql);
				if(mysql_num_rows($res_adr)==0){
					// L'adresse du responsable 1 n'est pas d�finie:
					echo "<td colspan='2'>\n";
					if($_SESSION['statut']=="professeur") {
						echo "L'adresse du responsable l�gal 1 n'est pas d�finie.\n";
					}
					else{
						//echo "L'adresse du responsable l�gal 1 n'est pas d�finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1#adresse' target='_blank'>D�finir l'adresse du responsable l�gal 1</a>\n";
						//echo "L'adresse du responsable l�gal 1 n'est pas d�finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\">D�finir l'adresse du responsable l�gal 1</a>\n";
						echo "L'adresse du responsable l�gal 1 n'est pas d�finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"return confirm_abandon (this, change, '$themessage');\">D�finir l'adresse du responsable l�gal 1</a>\n";
					}
					echo "</td>\n";
					$adr_id_1er_resp="";
				}
				else{
					echo "<td>\n";
					$lig_adr=mysql_fetch_object($res_adr);
					$adr_id_1er_resp=$lig_adr->adr_id;
					if("$lig_adr->adr1"!=""){$chaine_adr1.="$lig_adr->adr1, ";}
					if("$lig_adr->adr2"!=""){$chaine_adr1.="$lig_adr->adr2, ";}
					if("$lig_adr->adr3"!=""){$chaine_adr1.="$lig_adr->adr3, ";}
					if("$lig_adr->adr4"!=""){$chaine_adr1.="$lig_adr->adr4, ";}
					if("$lig_adr->cp"!=""){$chaine_adr1.="$lig_adr->cp, ";}
					if("$lig_adr->commune"!=""){$chaine_adr1.="$lig_adr->commune";}
					if("$lig_adr->pays"!=""){$chaine_adr1.=" (<i>$lig_adr->pays</i>)";}
					echo $chaine_adr1;
					echo "</td>\n";
					if($_SESSION['statut']!="professeur") {
						echo "<td>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1#adresse' target='_blank'>Modifier l'adresse du responsable</a>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' onClick='affiche_message_raffraichissement();' target='_blank'>Modifier l'adresse du responsable</a>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
						echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' onclick=\"return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
						echo "</td>\n";
					}
				}
				echo "</tr>\n";
				//echo "</table>\n";
			}
		}





		$chaine_adr2='';
		//if($eleve_no_resp2==0){
		if("$eleve_no_resp2"=="0"){
			// Le responsable 2 n'est pas d�fini:
			if($temoin_tableau=="oui"){echo "</table>\n";$temoin_tableau="non";}

			if($_SESSION['statut']=="professeur") {
				echo "<p>Le responsable l�gal 2 n'est pas d�fini: </p>\n";
 			}
			else{
				echo "<p>Le responsable l�gal 2 n'est pas d�fini: <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2";
				if (isset($order_type)) {echo "&amp;order_type=$order_type";}
				if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
				if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D�finir le responsable l�gal 2</a></p>\n";
			}
		}
		else{
			$sql="SELECT nom,prenom FROM resp_pers WHERE pers_id='$eleve_no_resp2'";
			$res_resp=mysql_query($sql);
			if(mysql_num_rows($res_resp)==0){
				// Bizarre: Le responsable 2 n'est pas d�fini:
				if($temoin_tableau=="oui"){echo "</table>\n";$temoin_tableau="non";}

				if($_SESSION['statut']=="professeur") {
					echo "<p>Le responsable l�gal 2 n'est pas d�fini.</p>\n";
				}
				else{
					echo "<p>Le responsable l�gal 2 n'est pas d�fini: <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D�finir le responsable l�gal 2</a></p>\n";
				}
			}
			else{
				$lig_resp=mysql_fetch_object($res_resp);

				if($temoin_tableau!="oui"){
					echo "<table border='0' summary='Responsable l�gal 2'>\n";
					$temoin_tableau="oui";
				}
				echo "<tr valign='top'>\n";
				echo "<td rowspan='2'>Le responsable l�gal 2 est: </td>\n";
				if($_SESSION['statut']=="professeur") {
					echo "<td>".casse_prenom($lig_resp->prenom)." ".strtoupper($lig_resp->nom)."</td>\n";
				}
				else{
					//echo "<td><a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2' target='_blank'>".ucfirst(strtolower($lig_resp->prenom))." ".strtoupper($lig_resp->nom)."</a></td>\n";
					//echo "<td><a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\" target='_blank'>".ucfirst(strtolower($lig_resp->prenom))." ".strtoupper($lig_resp->nom)."</a></td>\n";
					echo "<td><a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y' onclick=\"return confirm_abandon (this, change, '$themessage');\" target='_blank'>".casse_prenom($lig_resp->prenom)." ".strtoupper($lig_resp->nom)."</a></td>\n";

					//echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2'>Modifier l'association</a></td>\n";
					echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					//echo "'>Modifier le responsable</a></td>\n";
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Changer de responsable</a></td>\n";
				}
				echo "</tr>\n";

				echo "<tr valign='top'>\n";
				// La 1�re colonne est dans le rowspan

				$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp WHERE rp.pers_id='$eleve_no_resp2' AND rp.adr_id=ra.adr_id";
				$res_adr=mysql_query($sql);
				if(mysql_num_rows($res_adr)==0){
					// L'adresse du responsable 2 n'est pas d�finie:
					echo "<td colspan='2'>\n";
					if($_SESSION['statut']=="professeur") {
						echo "L'adresse du responsable l�gal 2 n'est pas d�finie.\n";
					}
					else{
						//echo "L'adresse du responsable l�gal 2 n'est pas d�finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2#adresse' target='_blank'>D�finir l'adresse du responsable l�gal 2</a>\n";
						//echo "L'adresse du responsable l�gal 2 n'est pas d�finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\">D�finir l'adresse du responsable l�gal 2</a>\n";
						echo "L'adresse du responsable l�gal 2 n'est pas d�finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"return confirm_abandon (this, change, '$themessage');\">D�finir l'adresse du responsable l�gal 2</a>\n";
					}
					echo "</td>\n";
				}
				else{
					echo "<td>\n";
					$lig_adr=mysql_fetch_object($res_adr);

					if(!isset($adr_id_1er_resp)) {$adr_id_1er_resp='';}
					if(($lig_adr->adr_id!="")&&($lig_adr->adr_id!=$adr_id_1er_resp)){
						$adr_id_2eme_resp=$lig_adr->adr_id;
						if("$lig_adr->adr1"!=""){$chaine_adr2.="$lig_adr->adr1, ";}
						if("$lig_adr->adr2"!=""){$chaine_adr2.="$lig_adr->adr2, ";}
						if("$lig_adr->adr3"!=""){$chaine_adr2.="$lig_adr->adr3, ";}
						if("$lig_adr->adr4"!=""){$chaine_adr2.="$lig_adr->adr4, ";}
						if("$lig_adr->cp"!=""){$chaine_adr2.="$lig_adr->cp, ";}
						if("$lig_adr->commune"!=""){$chaine_adr2.="$lig_adr->commune";}
						if("$lig_adr->pays"!=""){$chaine_adr2.=" (<i>$lig_adr->pays</i>)";}

						//if("$chaine_adr1"=="$chaine_adr2"){
						if(casse_mot("$chaine_adr1",'min')==casse_mot("$chaine_adr2",'min')){
							echo "$chaine_adr2<br />\n<span style='color: red;'>Les adresses sont identiques, mais sont enregistr�es sous deux identifiants diff�rents (<i>$adr_id_1er_resp et $lig_adr->adr_id</i>); vous devriez modifier l'adresse pour pointer vers le m�me identifiant d'adresse.</span>";
						}
						else{
							echo "$chaine_adr2";
						}
					}
					else{
						echo "M�me adresse.";
					}
					echo "</td>\n";
					if($_SESSION['statut']!="professeur") {
						echo "<td>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2#adresse' target='_blank'>Modifier l'adresse du responsable</a>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' onClick='affiche_message_raffraichissement();' target='_blank'>Modifier l'adresse du responsable</a>\n";
						//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
						echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' onclick=\"return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
						if((isset($adr_id_1er_resp))&&(isset($adr_id_2eme_resp))){
							if("$adr_id_1er_resp"!="$adr_id_2eme_resp"){
								echo "<br />";
								echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;modif_adr_pers_id=$eleve_no_resp2&amp;adr_id=$adr_id_1er_resp";
								if (isset($order_type)) {echo "&amp;order_type=$order_type";}
								if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
								if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
								//echo "'>Prendre l'adresse de l'autre responsable</a>";
								echo add_token_in_url();
								echo "' onclick=\"return confirm_abandon (this, change, '$themessage');\">Prendre l'adresse de l'autre responsable</a>";
							}
						}
						echo "</td>\n";
					}
				}
				echo "</tr>\n";
				//echo "</table>\n";
			}
		}
		if($temoin_tableau=="oui"){echo "</table>\n";$temoin_tableau="non";}


		echo "<script type='text/javascript'>
	function affiche_message_raffraichissement() {
		document.getElementById('message_target_blank').innerHTML=\"Pensez � rafraichir la page apr�s modification de l'adresse responsable.<br />Cependant, si vous avez modifi� des informations dans la pr�sente page, pensez � les enregistrer avant de recharger la page.\";
	}
</script>\n";


		if("$chaine_adr2"!=""){
			if("$chaine_adr1"!=""){
				if("$chaine_adr1"!="$chaine_adr2"){
					echo "<p><b>Les adresses des deux responsables l�gaux ne sont pas identiques. Par cons�quent, le bulletin sera envoy� aux deux responsables l�gaux.</b></p>\n";
				}
				else{
					echo "<p><b>Les adresses des deux responsables l�gaux sont identiques. Par cons�quent, le bulletin ne sera envoy� qu'� la premi�re adresse.</b>";
					echo "</p>\n";
				}
			}
			else{
				echo "<p><b>Le bulletin ne sera envoy� qu'au deuxi�me responsable.</b></p>\n";
			}
		}
		else{
			if("$chaine_adr1"!=""){
				echo "<p><b>Le bulletin ne sera envoy� qu'au premier responsable.</b></p>\n";
			}
			else{
				echo "<p><b>Aucune adresse n'est renseign�e. Le bulletin ne pourra pas �tre envoy�.</b></p>\n";
			}
		}


		//if(($eleve_no_resp1==0)||($eleve_no_resp2==0)){
		if(("$eleve_no_resp1"=="0")||("$eleve_no_resp2"=="0")){
			if($_SESSION['statut']=="professeur") {
				echo "<p>Si le responsable l�gal ne figure pas dans la liste, prenez contact avec l'administrateur ou avec une personne disposant du statut 'scolarit�'.</p>\n";
			}
			else{
				echo "<p>Si le responsable l�gal ne figure pas dans la liste, vous pouvez l'ajouter � la base<br />\n";
				echo "(<i>apr�s avoir, le cas �ch�ant, sauvegard� cette fiche</i>)<br />\n";

				if($_SESSION['statut']=="scolarite") {
					echo "en vous rendant dans [<a href='../responsables/index.php'>Gestion des fiches responsables �l�ves</a>]</p>\n";
				}
				else{
					echo "en vous rendant dans [Gestion des bases-><a href='../responsables/index.php'>Gestion des responsables �l�ves</a>]</p>\n";
				}
			}
		}
	}
}



//if(isset($eleve_login)){
if((isset($eleve_login))&&(isset($reg_no_gep))&&($reg_no_gep!="")) {

	echo "<br />\n";
	echo "<hr />\n";

	echo "<h3>Etablissement d'origine</h3>\n";

	//$sql="SELECT * FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'";
	$sql="SELECT * FROM j_eleves_etablissements WHERE id_eleve='$reg_no_gep'";
	$res_etab=mysql_query($sql);
	if(mysql_num_rows($res_etab)==0) {
		echo "<p>L'�tablissement d'origine de l'�l�ve n'est pas renseign�.";
		if($_SESSION['statut']!="professeur") {
			echo "<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
			//echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;reg_no_gep=$reg_no_gep&amp;definir_etab=y";
			if (isset($order_type)) {echo "&amp;order_type=$order_type";}
			if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
			if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
			echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Renseigner l'�tablissement d'origine</a>";
		}
		echo "</p>\n";
	}
	else{
		$lig_etab=mysql_fetch_object($res_etab);

		if("$lig_etab->id_etablissement"==""){
			if($_SESSION['statut']=="professeur") {
				echo "<p>L'�tablissement d'origine de l'�l�ve n'est pas renseign�.</p>\n";
			}
			else{
				echo "<p><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
				//echo "<p><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;reg_no_gep=$reg_no_gep&amp;definir_etab=y";
				if (isset($order_type)) {echo "&amp;order_type=$order_type";}
				if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
				if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D�finir l'�tablissement d'origine</a>";
				echo "</p>\n";
			}
		}
		else{
			$sql="SELECT * FROM etablissements WHERE id='$lig_etab->id_etablissement'";
			$res_etab2=mysql_query($sql);
			if(mysql_num_rows($res_etab2)==0) {
				echo "<p>L'association avec l'identifiant d'�tablissement existe (<i>$lig_etab->id_etablissement</i>), mais les informations correspondantes n'existent pas dans la table 'etablissement'.";
				if($_SESSION['statut']!="professeur") {
					echo "<br />\n";

					echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
					//echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;reg_no_gep=$reg_no_gep&amp;definir_etab=y";

					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}

					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modifier l'�tablissement d'origine</a>";
				}
				echo "</p>\n";
			}
			else{
				echo "<p>L'�tablissement d'origine de l'�l�ve est&nbsp;:<br />\n";
				$lig_etab2=mysql_fetch_object($res_etab2);
				//echo "&nbsp;&nbsp;&nbsp;".ucfirst(strtolower($lig_etab2->niveau))." ".$lig_etab2->type." ".$lig_etab2->nom.", ".$lig_etab2->cp.", ".$lig_etab2->ville." (<i>$lig_etab->id_etablissement</i>)<br />\n";
				echo "&nbsp;&nbsp;&nbsp;";
				if($lig_etab2->niveau=="college"){
					echo "Coll�ge";
				}
				elseif($lig_etab2->niveau=="lycee"){
					echo "Lyc�e";
				}
				else{
					echo casse_prenom($lig_etab2->niveau);
				}
				echo " ".$lig_etab2->type." ".$lig_etab2->nom.", ".$lig_etab2->cp.", ".$lig_etab2->ville." (<i>$lig_etab->id_etablissement</i>)";
				if($_SESSION['statut']!="professeur") {
					echo "<br />\n";
					echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
					//echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;reg_no_gep=$reg_no_gep&amp;definir_etab=y";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modifier l'�tablissement d'origine</a>";
				}
				echo "</p>\n";
			}
		}
	}
	echo "<p><br /></p>\n";
}

if((isset($eleve_login))&&($compte_eleve_existe=="y")&&($journal_connexions=='n')&&(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite"))) {
	//$sql="SELECT 1=1 FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
	//$sql="SELECT 1=1 FROM utilisateurs WHERE login='$eleve_login';";
	//$test_compte=mysql_query($sql);
	//if(mysql_num_rows($test_compte)>0) {$compte_eleve_existe="y";} else {$compte_eleve_existe="n";}

	//if(($compte_eleve_existe=="y")&&(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite"))) {
		//$journal_connexions=isset($_POST['journal_connexions']) ? $_POST['journal_connexions'] : (isset($_GET['journal_connexions']) ? $_GET['journal_connexions'] : 'n');
		//$duree=isset($_POST['duree']) ? $_POST['duree'] : NULL;

		echo "<hr />\n";
	
		echo "<p><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;journal_connexions=y#connexion' title='Journal des connexions'>Journal des connexions</a></p>\n";
	//}
}


if((isset($eleve_login))&&($compte_eleve_existe=="y")&&($journal_connexions=='y')&&(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite"))) {
	echo "<hr />\n";
	// Journal des connexions
	echo "<a name=\"connexion\"></a>\n";
	if (isset($_POST['duree'])) {
		$duree = $_POST['duree'];
	} else {
		$duree = '7';
	}
	
	journal_connexions($eleve_login,$duree,'modify_eleve');
	echo "<p><br /></p>\n";
}


require("../lib/footer.inc.php");
?>