<?php
/*
 * $Id: index.php 2554 2008-10-12 14:49:29Z crob $
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
/*
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// SQL : INSERT INTO droits VALUES ( '/mod_ooo/rapport_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Mod�le Ooo : Rapport incident', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/rapport_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Mod�le Ooo : Rapport Incident', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}
*/

include_once('./lib/lib_mod_ooo.php');

include_once('./lib/tbs_class.php');
include_once('./lib/tbsooo_class.php');
define( 'PCLZIP_TEMPORARY_DIR', '../mod_ooo/tmp/' );
include_once('../lib/pclzip.lib.php');

include_once('../mod_discipline/sanctions_func_lib.php'); // la librairie de fonction du module discipline pour la fonction p_nom , u_p_nom

//debug_var();

//
// Zone de traitement des donn�es qui seront fusionn�es au mod�le
// ATTENTION S'il y a des TABLEAUX � TRAITER Voir en BAS DU FICHIER PARTIE TABLEAU (Merge)
// Chacune correspond � une variable d�finie dans le mod�le
//
//On r�cup�re les coordonn�es du coll�ge dans Gepi ==> $gepiSettings['nom_setting']
$ets_anne_scol = $gepiSettings['gepiSchoolName'];
$ets_nom = $gepiSettings['gepiSchoolName'];
$ets_adr1 = $gepiSettings['gepiSchoolAdress1'];
$ets_adr2 = $gepiSettings['gepiSchoolAdress2'];
$ets_cp = $gepiSettings['gepiSchoolZipCode'];
$ets_ville = $gepiSettings['gepiSchoolCity'];
$ets_tel = $gepiSettings['gepiSchoolTel'];
$ets_fax = $gepiSettings['gepiSchoolFax'];
$ets_email = $gepiSettings['gepiSchoolEmail'];
$gepiyear =  $gepiSettings['gepiYear'];
 
 
// recup�ration des parametres
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL); // Les informations viennent d'o� ? si mode = module_discipline ==> du module discipline
$id_incident=isset($_POST['id_incident']) ? $_POST['id_incident'] : (isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL); 
//$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL); 
//$id_sanction=isset($_POST['id_sanction']) ? $_POST['id_sanction'] : (isset($_GET['id_sanction']) ? $_GET['id_sanction'] : NULL); 

//Initialisation des donn�es du mod�le Ooo Retenue
$date ='';
$objet_rapport ='';
$motif = '';
$nom_resp ='';
$fct_resp ='';
$num_incident ='';
$creneau ='';
$lieu_incident ='';


// mode = module_discipline, on vient de la page saisie incident du module discipline
// mode = module_retenue, on vient de la partie sanction du module discipline et de la sanction : retenue
if ($mode=='module_discipline') {
	//
	//les protagonistes d'un incident
	//
	$sql="SELECT * FROM s_protagonistes WHERE id_incident='$id_incident' ORDER BY qualite,statut,login;";
	$res2=mysql_query($sql);
	$cpt=0;
	$tab_protagonistes=array(); //le tableau des login
	$donnee_tab_protagonistes=array(); //le tableau des donn�es

	while($lig2=mysql_fetch_object($res2)) {
		$tab_protagonistes[]=$lig2->login;

		if($lig2->statut=='eleve') {
			$sql="SELECT nom,prenom FROM eleves WHERE login='$lig2->login';";
			//echo "$sql<br />\n";
			$res3=mysql_query($sql);
			if(mysql_num_rows($res3)>0) {
				$lig3=mysql_fetch_object($res3);					
				$donnee_tab_protagonistes[$cpt]['nom']=ucfirst(strtolower($lig3->prenom))." ".strtoupper($lig3->nom);				
			}
			else {
				echo "ERREUR: Login $lig2->login inconnu";
			}

			$tmp_tab=get_class_from_ele_login($lig2->login);
			if(isset($tmp_tab['liste'])) {
				$donnee_tab_protagonistes[$cpt]['statut']=$tmp_tab['liste'];
			}
		}
		else {
			$sql="SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$lig2->login';";
			$res3=mysql_query($sql);
			if(mysql_num_rows($res3)>0) {
				$lig3=mysql_fetch_object($res3);
				$donnee_tab_protagonistes[$cpt]['nom']=$lig3->civilite." ".strtoupper($lig3->nom)." ".ucfirst(substr($lig3->prenom,0,1));
			}
			else {
				echo "ERREUR: Login $lig2->login inconnu";
			}

			if($lig3->statut=='autre') {
				//echo " (<i>".$_SESSION['statut_special']."</i>)\n";
				$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
												WHERE du.login_user = '".$lig2->login."'
												AND du.id_statut = ds.id;";
				$query = mysql_query($sql);
				$result = mysql_fetch_array($query);

				$donnee_tab_protagonistes[$cpt]['statut']=$result['nom_statut'];					
			}
			else {
				$donnee_tab_protagonistes[$cpt]['statut']=$lig3->statut;
			}
		}
		if($lig2->qualite!='') {
			$donnee_tab_protagonistes[$cpt]['qualite']=$lig2->qualite;
		}
		$cpt++;
	}

	//affichage des donn�e pour d�bug
	/*
				echo "<pre>";
				print_r($donnee_tab_protagonistes);
				echo "</pre>";
				echo "<pre>";
				print_r($array_type2);
				echo "</pre>";
	*/


	// on r�cup�re les donn�es � transmettre au mod�le de retenue open office.
	$sql_incident="SELECT * FROM `s_incidents` WHERE `id_incident`=$id_incident";
	$res_incident=mysql_query($sql_incident);
	if(mysql_num_rows($res_incident)>0) {
		$lig_incident=mysql_fetch_object($res_incident);
		
		//traitement de la date mysql
		$date=datemysql_to_jj_mois_aaaa($lig_incident->date,'-','o');
		
		// Cr�neau horaire
		$creneau = $lig_incident->heure;
		
		//traitement du motif
		$motif = $lig_incident->description;
		
		//traitement de l'objet
		$objet_rapport = $lig_incident->nature;
		
		//recherche du lieu dans la table s_lieux_incidents
		$sql_lieu="SELECT * FROM s_lieux_incidents WHERE id='$lig_incident->id_lieu';";
		//echo "$sql_lieu<br />\n";
		$res_lieu=mysql_query($sql_lieu);
		if(mysql_num_rows($res_lieu)>0) {
			$lig_lieu=mysql_fetch_object($res_lieu);
			$lieu_incident = $lig_lieu->lieu;
		}

		//le d�clarant On r�cup�re le nom et le pr�nom (et la qualit�)
		$sql="SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$lig_incident->declarant';";
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$lig=mysql_fetch_object($res);
			//var mod�le
			$nom_resp = $lig->civilite." ".strtoupper($lig->nom)." ".ucfirst(substr($lig->prenom,0,1)).".";
		}
		else {
			echo "ERREUR: Login $lig_incident->declarant";
		}
		if($lig->statut=='autre') {
			$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
											WHERE du.login_user = '".$lig_incident->declarant."'
											AND du.id_statut = ds.id;";
			$query = mysql_query($sql);
			$result = mysql_fetch_array($query);
			//var mod�le
			$fct_resp = $result['nom_statut'] ;
		}
		else {
			$fct_resp = $lig->statut ;
		}
	$fct_resp = ucfirst($fct_resp);
		
	} else {
		return "INCIDENT INCONNU";
	}
	
	//var mod�le
	$num_incident = $id_incident;

} //if mode = module discipline  

/*
if ($mode=='formulaire_rapport_incident') { //les donn�e provenant du formulaire 
   
    $date = datemysql_to_jj_mois_aaaa($_SESSION['retenue_date'],'/','n');
	session_unregister("retenue_date");
	$nom_prenom_eleve =$_SESSION['retenue_nom_prenom_elv'];
	session_unregister("retenue_nom_prenom_elv");
	$classe = $_SESSION['retenue_classe_elv'];
	session_unregister("retenue_classe_elv");	
	
	$motif = $_SESSION['retenue_motif'];
	$motif=traitement_magic_quotes(corriger_caracteres($motif));
	// Contr�le des saisies pour supprimer les sauts de lignes surnum�raires.
	$motif=my_ereg_replace('(\\\r\\\n)+',"\r\n",$motif);
	session_unregister("retenue_motif");
	
	$travail = $_SESSION['retenue_travail'];
	$travail=traitement_magic_quotes(corriger_caracteres($travail));
	// Contr�le des saisies pour supprimer les sauts de lignes surnum�raires.
	$travail=my_ereg_replace('(\\\r\\\n)+',"\r\n",$travail);
	session_unregister("retenue_travail");
	
	$nom_resp = $_SESSION['retenue_nom_resp'];
	session_unregister("retenue_nom_resp");
	$fct_resp = $_SESSION['retenue_fct_resp'];
	session_unregister("retenue_fct_resp");

	$date_retenue ='';
	$duree ='';
	$h_deb ='';
	$num_incident = '';

} // formulaire_rapport_incident
*/
//
// Fin zone de traitement Les donn�es qui seront fusionn�es au mod�le
//

//
//Les variables � modifier pour le traitement  du mod�le ooo
//
//Le chemin et le nom du fichier ooo � traiter (le mod�le de document)
$nom_fichier_modele_ooo ='rapport_incident.odt';
// Par defaut tmp
$nom_dossier_temporaire ='tmp';
//par defaut content.xml
$nom_fichier_xml_a_traiter ='content.xml';


//Proc�dure du traitement � effectuer
//les chemins contenant les donn�es
include_once ("./lib/chemin.inc.php");


// instantiate a TBS OOo class
$OOo = new clsTinyButStrongOOo;
// setting the object
$OOo->SetProcessDir($nom_dossier_temporaire ); //dossier o� se fait le traitement (d�compression / traitement / compression)
// create a new openoffice document from the template with an unique id 
$OOo->NewDocFromTpl($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo); // le chemin du fichier est indiqu� � partir de l'emplacement de ce fichier
// merge data with openoffice file named 'content.xml'
$OOo->LoadXmlFromDoc($nom_fichier_xml_a_traiter); //Le fichier qui contient les variables et doit �tre pars� (il sera extrait)

// Traitement des tableaux
// On ins�re ici les lignes concernant la gestion des tableaux
$OOo->MergeBlock('blk1',$donnee_tab_protagonistes) ; 
// Fin de traitement des tableaux


$OOo->SaveXmlToDoc(); //traitement du fichier extrait


//G�n�ration du nom du fichier
$now = gmdate('d_M_Y_H:i:s');
$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
//$nom_fic = $nom_fichier_modele[0]."_N�_".$num_incident."_g�n�r�_le_".$now.".".$nom_fichier_modele[1];
$nom_fic = $nom_fichier_modele[0]."_N_".$num_incident."_genere_le_".$now.".".$nom_fichier_modele[1];
header('Expires: ' . $now);
// lem9 & loic1: IE need specific headers

if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: inline; filename="' . $nom_fic . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
} else {
    header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
    header('Pragma: no-cache');
}

// display
header('Content-type: '.$OOo->GetMimetypeDoc());
header('Content-Length: '.filesize($OOo->GetPathnameDoc()));
$OOo->FlushDoc(); //envoi du fichier trait�
$OOo->RemoveDoc(); //suppression des fichiers de travail
// Fin de traitement des tableaux
?>