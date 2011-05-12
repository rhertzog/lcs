<?php
/*
 * $Id: import_communes.php 6746 2011-04-04 05:59:18Z crob $
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// INSERT INTO `droits` VALUES ('/eleves/import_communes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Import des communes de naissance', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";

/*
function extr_valeur($lig) {
	unset($tabtmp);
	$tabtmp=explode(">",my_ereg_replace("<",">",$lig));
	return trim($tabtmp[2]);
}

function ouinon($nombre) {
	if($nombre==1) {return "O";}elseif($nombre==0) {return "N";}else {return "";}
}
function sexeMF($nombre) {
	//if($nombre==2) {return "F";}else {return "M";}
	if($nombre==2) {return "F";}elseif($nombre==1) {return "M";}else {return "";}
}
*/

function affiche_debug($texte) {
	// Passer � 1 la variable pour g�n�rer l'affichage des infos de debug...
	$debug=0;
	if($debug==1) {
		echo "<font color='green'>".$texte."</font>";
		flush();
	}
}

// Initialisation du r�pertoire actuel de sauvegarde
$dirname = getSettingValue("backup_directory");

function info_debug($texte) {
	global $step;
	global $dirname;

	$debug=0;
	if($debug==1) {
		//$fich_debug=fopen("/tmp/debug_maj_import2.txt","a+");
		$fich_debug=fopen("../backup/".$dirname."/debug_import_communes.txt","a+");
		fwrite($fich_debug,"$step;$texte;".time()."\n");
		fclose($fich_debug);
	}
}
/*
function maj_ini_prenom($prenom) {
	$prenom2="";
	$tab1=explode("-",$prenom);
	for($i=0;$i<count($tab1);$i++) {
		if($i>0) {
			$prenom2.="-";
		}
		$tab2=explode(" ",$tab1[$i]);
		for($j=0;$j<count($tab2);$j++) {
			if($j>0) {
				$prenom2.=" ";
			}
			$prenom2.=ucfirst(strtolower($tab2[$j]));
		}
	}
	return $prenom2;
}
*/

/*
function get_commune($code_commune_insee,$mode) {
	$retour="";

	$sql="SELECT * FROM communes WHERE code_commune_insee='$code_commune_insee';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		if($mode==0) {
			$retour=$lig->commune;
		}
		else {
			$retour=$lig->commune." (<i>".$lig->departement."</i>)";
		}
	}
	return $retour;
}
*/

// Etape...
$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

$parcours_diff=isset($_POST['parcours_diff']) ? $_POST['parcours_diff'] : NULL;

//$tab_ele_id=isset($_POST['tab_ele_id']) ? $_POST['tab_ele_id'] : NULL;
//$tab_ele_id_diff=isset($_POST['tab_ele_id_diff']) ? $_POST['tab_ele_id_diff'] : NULL;

$nb_parcours=isset($_POST['nb_parcours']) ? $_POST['nb_parcours'] : NULL;

/*
$tab_pers_id=isset($_POST['tab_pers_id']) ? $_POST['tab_pers_id'] : NULL;
$tab_pers_id_diff=isset($_POST['tab_pers_id_diff']) ? $_POST['tab_pers_id_diff'] : NULL;

$total_pers_diff=isset($_POST['total_pers_diff']) ? $_POST['total_pers_diff'] : NULL;

$valid_pers_id=isset($_POST['valid_pers_id']) ? $_POST['valid_pers_id'] : NULL;
$liste_pers_id=isset($_POST['liste_pers_id']) ? $_POST['liste_pers_id'] : NULL;

$tab_adr_id=isset($_POST['tab_adr_id']) ? $_POST['tab_adr_id'] : NULL;
$tab_adr_id_diff=isset($_POST['tab_adr_id_diff']) ? $_POST['tab_adr_id_diff'] : NULL;
*/

/*
$tab_resp_id=isset($_POST['tab_resp_id']) ? $_POST['tab_resp_id'] : NULL;
$tab_resp_id_diff=isset($_POST['tab_resp_id_diff']) ? $_POST['tab_resp_id_diff'] : NULL;
*/

/*
$tab_resp=isset($_POST['tab_resp']) ? $_POST['tab_resp'] : NULL;
$tab_resp_diff=isset($_POST['tab_resp_diff']) ? $_POST['tab_resp_diff'] : NULL;

$total_diff=isset($_POST['total_diff']) ? $_POST['total_diff'] : NULL;

$liste_assoc=isset($_POST['liste_assoc']) ? $_POST['liste_assoc'] : NULL;

$ne_pas_proposer_resp_sans_eleve=isset($_POST['ne_pas_proposer_resp_sans_eleve']) ? $_POST['ne_pas_proposer_resp_sans_eleve'] : (isset($_GET['ne_pas_proposer_resp_sans_eleve']) ? $_GET['ne_pas_proposer_resp_sans_eleve'] : (isset($_SESSION['ne_pas_proposer_resp_sans_eleve']) ? $_SESSION['ne_pas_proposer_resp_sans_eleve'] : "si"));
*/

$stop=isset($_POST['stop']) ? $_POST['stop'] : (isset($_GET['stop']) ? $_GET['stop'] :'n');

//$style_specifique="responsables/maj_import2";

$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";

$mysql_collate=getSettingValue("mysql_collate") ? getSettingValue("mysql_collate") : "";
$chaine_mysql_collate="";
if($mysql_collate!="") {$chaine_mysql_collate="COLLATE $mysql_collate";}

//**************** EN-TETE *****************
$titre_page = "Import des communes de naissance";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

if(isset($step)) {
	if(($step==0)||
		($step==1)||
		($step==2)
		) {

		echo "<div style='float: right; border: 1px solid black; width: 4em;'>
<form name='formstop' action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='checkbox' name='stop' id='stop' value='y' onchange='stop_change()' ";
//if(isset($stop)) {
if($stop=='y') {
	echo "checked ";
}
echo "/> <a href='#' onmouseover=\"afficher_div('div_stop','y',10,20);\">Stop</a>";
echo add_token_field();
echo "</form>\n";
		echo "</div>\n";

		echo creer_div_infobulle("div_stop","","","Ce bouton permet s'il est coch� d'interrompre les passages automatiques � la page suivante","",12,0,"n","n","y","n");

		echo "<script type='text/javascript'>
	temporisation_chargement='ok';
	cacher_div('div_stop');
</script>\n";


							echo "<script type='text/javascript'>
function stop_change() {
	stop='n';
	if(document.getElementById('stop')) {
		if(document.getElementById('stop').checked==true) {
			stop='y';
		}
	}
	if(document.getElementById('id_form_stop')) {
		document.getElementById('id_form_stop').value=stop;
	}
}

//function test_stop(num) {
function test_stop(num,compteur,nblig) {
	stop='n';
	if(document.getElementById('stop')) {
		if(document.getElementById('stop').checked==true) {
			stop='y';
		}
	}
	//document.getElementById('id_form_stop').value=stop;
	if(stop=='n') {
		//setTimeout(\"document.location.replace('".$_SERVER['PHP_SELF']."?step=1')\",2000);
		//document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&amp;stop='+stop);
		document.location.replace('".$_SERVER['PHP_SELF']."?step='+num+'&compteur='+compteur+'&nblig='+nblig+'&stop='+stop+'".add_token_in_url(false)."');
	}
}

function test_stop2() {
	stop='n';
	if(document.getElementById('stop')) {
		if(document.getElementById('stop').checked==true) {
			stop='y';
		}
	}
	document.getElementById('id_form_stop').value=stop;
	if(stop=='n') {
		//setTimeout(\"document.forms['formulaire'].submit();\",1000);
		document.forms['formulaire'].submit();
	}
}





function test_stop_suite(num) {
	stop='n';
	if(document.getElementById('stop')) {
		if(document.getElementById('stop').checked==true) {
			stop='y';
		}
	}

	document.location.replace('".$_SERVER['PHP_SELF']."?step='+num";
// AJOUT A FAIRE VALEUR STOP
echo "+'&stop='+stop";
echo "+'".add_token_in_url(false)."'";
echo ");
}

</script>\n";

	}
}

echo "<p class=bold>";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//echo "</p>\n";

// On fournit les fichiers CSV g�n�r�s depuis les XML de SCONET...
//if (!isset($is_posted)) {
if(!isset($step)) {
	echo " | <a href=\"../utilitaires/import_pays.php\">Import des pays</a>";
	echo "</p>\n";

	echo "<h2>Import des communes de naissance des �l�ves</h2>\n";

	$sql="SELECT e.* FROM eleves e WHERE e.lieu_naissance='';";
	$res=mysql_query($sql);
	$nb_lieu_nais_non_renseignes=mysql_num_rows($res);
	if($nb_lieu_nais_non_renseignes>0) {
		if($nb_lieu_nais_non_renseignes==1) {
			echo "<p>".$nb_lieu_nais_non_renseignes." lieu de naissance n'est pas renseign�&nbsp;: \n";
			$lig=mysql_fetch_object($res);
			echo casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
			echo "</p>\n";
		}
		elseif($nb_lieu_nais_non_renseignes>1) {
			echo "<p>".$nb_lieu_nais_non_renseignes." lieux de naissance ne sont pas renseign�s&nbsp;: \n";
			$cpt=0;
			while($lig=mysql_fetch_object($res)) {
				if($cpt>0) {echo ", ";}
				echo casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
				// Pour des tests... echo " (UPDATE eleves SET lieu_naissance='' WHERE login='$lig->login';)<br />";
				$cpt++;
			}
			echo "</p>\n";
		}

		// METTRE DES LIENS VERS LES FICHES ELEVES
		// MODIFIER LA FICHE ELEVE POUR PERMETTRE LA SAISIE D'UNE COMMUNE ET FAIRE UNE RECHERCHE SUR LE CODE COMMUNE CORRESPONDANT DANS communes

		if(getSettingValue('import_maj_xml_sconet')==1) {
			echo "<p>Effectuez une <a href='../responsables/maj_import.php'>mise � jour depuis Sconet</a> pour renseigner les code_commune_insee des lieux de naissance des �l�ves.</p>\n";
		}
		echo "<p><br /></p>\n";

	}

	$sql="SELECT e.* FROM eleves e
	LEFT JOIN communes c ON c.code_commune_insee=e.lieu_naissance
	where c.code_commune_insee is NULL;";
	$res=mysql_query($sql);
	//if(mysql_num_rows($res)==0) {
	if(mysql_num_rows($res)<=$nb_lieu_nais_non_renseignes) {
		echo "<p>Tous les lieux de naissances saisis pour les �l�ves ont leur correspondant dans la table 'communes'.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="TRUNCATE TABLE tempo2;";
	$res0=mysql_query($sql);

	$retour_commune_manquante="";
	$retour_commune_etrangere="";
	$cpt=0;
	$cpt2=0;
	while($lig=mysql_fetch_object($res)) {
		if($lig->lieu_naissance!='') {

			if(strstr($lig->lieu_naissance,'@')) {
				if($cpt2>0) {$retour_commune_etrangere.="<br />";}
				$retour_commune_etrangere.=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2')." <span style='font-size:small'>(".get_commune($lig->lieu_naissance,1).")</span>";
				$cpt2++;
			}
			else {
				if($cpt>0) {$retour_commune_manquante.=", ";}
				$retour_commune_manquante.=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
				$cpt++;
			}

		}
	}

	if($cpt>0) {
		echo "<p>Les lieux de naissance sont manquants pour ";
		echo $retour_commune_manquante;
		echo "</p>\n";
	}
	else {
		echo "<p>Tous les lieux de naissance dans une commune fran�aise sont renseign�s.</p>\n";
	}

	if($retour_commune_etrangere!='') {
		echo "<p>Les lieux de naissance dans des communes �trang�res sont&nbsp;:</p>\n";
		echo "<p style='margin-left:3em;'>";
		echo $retour_commune_etrangere;
		echo "</p>\n";
		echo "<p>Si ces lieux sont correctement renseign�s, vous n'avez rien � faire.<br />Sinon... il faut attendre qu'une page soit d�velopp�e pour remplir les lieux de naissance � l'�tranger en dehors de la m�thode 'Import Sconet'.</p>\n";
	}

	echo "<p><br /></p>\n";

	if($cpt>0) {
		echo "<p>Vous allez importer les correspondances code_commune_insee/nom de commune depuis un fichier CSV.<br />
Ce fichier est volumineux (<i>la France compte quelques communes;o</i>).<br />
Il serait dommage de faire enfler inutilement votre base en la remplissant avec toutes les communes de France.<br />
Cette page va donc parcourir le fichier, remplir une table temporaire et n'en retenir finalement que les communes correspondant � vos �l�ves.<br />
Le fichier � fournir ci-dessous peut �tre t�l�charg� ici&nbsp;: <a href='https://www.sylogix.org/attachments/647/communes1102.csv.zip'>https://www.sylogix.org/attachments/647/communes1102.csv.zip</a></p>\n";

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	
		//echo "<input type=hidden name='is_posted' value='yes' />\n";
		echo "<input type=hidden name='step' value='0' />\n";
		//echo "<input type=hidden name='mode' value='1' />\n";
		if ($gepiSettings['unzipped_max_filesize']>=0) {
			echo "<p>S�lectionnez le fichier <b>communes.csv.zip</b>&nbsp;:<br />\n";
		}
		else {
			echo "<p>Veuillez d�zipper le fichier (<i>�vitez de l'ouvrir/modifier/enregistrer avec un tableur</i>) et fournissez le fichier <b>communes.csv</b>&nbsp;:<br />\n";
		}
		echo "<input type=\"file\" size=\"80\" name=\"communes_csv_file\" /><br />\n";
	
		echo "Parcourir le fichier par tranches de <input type=\"text\" size=\"6\" name=\"nblig\" value=\"500\" /> lignes.<br />\n";
		//==============================
		// AJOUT pour tenir compte de l'automatisation ou non:
		//echo "<input type='hidden' name='stop' id='id_form_stop' value='$stop' />\n";
		echo "<input type='checkbox' name='stop' id='id_form_stop' value='y' /><label for='id_form_stop' style='cursor: pointer;'> D�sactiver le mode automatique.</label></p>\n";
		//==============================

		echo add_token_field();

		echo "<p><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";
	
		echo "<p><br /></p>\n";
	
		echo "<p style='color:red;'>A FAIRE: proposer d'importer une bonne fois pour toutes les communes.<br />Signaler la taille de la table obtenue.</p>\n";
	}
}
else {
	if($step>0) {
		echo " | <a href=\"".$_SERVER['PHP_SELF']."\">Import des communes</a>";
	}
	echo " | <a href=\"../utilitaires/import_pays.php\">Import des pays</a>";
	echo "</p>\n";

	check_token(false);

	//echo "\$step=$step<br />\n";

	// On va uploader le fichier CSV dans le tempdir de l'utilisateur (administrateur, ou scolarit� pour les m�j Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir) {
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas d�fini!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}

	$post_max_size=ini_get('post_max_size');
	$upload_max_filesize=ini_get('upload_max_filesize');
	$max_execution_time=ini_get('max_execution_time');
	$memory_limit=ini_get('memory_limit');

	$nblig=isset($_POST['nblig']) ? $_POST['nblig'] : (isset($_GET['nblig']) ? $_GET['nblig'] : 500);

	//if(!isset($_POST['step'])) {
	switch($step) {
		case 0:
			// Affichage des informations �l�ves
			echo "<h2>Transfert du fichier des communes</h2>\n";

			$csv_file = isset($_FILES["communes_csv_file"]) ? $_FILES["communes_csv_file"] : NULL;

			if(!is_uploaded_file($csv_file['tmp_name'])) {
				echo "<p style='color:red;'>L'upload du fichier a �chou�.</p>\n";

				echo "<p>Les variables du php.ini peuvent peut-�tre expliquer le probl�me:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "</p>\n";

				// Il ne faut pas aller plus loin...
				// SITUATION A GERER
				require("../lib/footer.inc.php");
				die();
			}
			else {
				if(!file_exists($csv_file['tmp_name'])) {
					echo "<p style='color:red;'>Le fichier aurait �t� upload�... mais ne serait pas pr�sent/conserv�.</p>\n";

					echo "<p>Les variables du php.ini peuvent peut-�tre expliquer le probl�me:<br />\n";
					echo "post_max_size=$post_max_size<br />\n";
					echo "upload_max_filesize=$upload_max_filesize<br />\n";
					echo "et le volume de ".$csv_file['name']." serait<br />\n";
					echo "\$csv_file['size']=".volume_human($csv_file['size'])."<br />\n";
					echo "</p>\n";
					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}

				echo "<p>Le fichier a �t� upload�.</p>\n";

				/*
				echo "\$csv_file['tmp_name']=".$csv_file['tmp_name']."<br />\n";
				echo "\$tempdir=".$tempdir."<br />\n";

				echo "<p>Les variables du php.ini peuvent peut-�tre expliquer le probl�me:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "\$csv_file['size']=".volume_human($csv_file['size'])."<br />\n";
				echo "</p>\n";
				*/

				//$source_file=stripslashes($csv_file['tmp_name']);
				$source_file=$csv_file['tmp_name'];
				$dest_file="../temp/".$tempdir."/communes.csv";
				//$res_copy=copy("$source_file" , "$dest_file");
				//echo $source_file." -&gt; ".$dest_file.'<br />';

				//===============================================================
				// ajout prise en compte des fichiers ZIP: Marc Leygnac

				$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
				// $unzipped_max_filesize = 0    pas de limite de taille pour les fichiers extraits
				// $unzipped_max_filesize < 0    extraction zip d�sactiv�e
				if($unzipped_max_filesize>=0) {
					$fichier_emis=$csv_file['name'];
					$extension_fichier_emis=strtolower(strrchr($fichier_emis,"."));
					if (($extension_fichier_emis==".zip")||($csv_file['type']=="application/zip"))
						{

						$dest_zip_file="../temp/".$tempdir."/communes.csv.zip";
						$res_copy=copy("$source_file" , "$dest_zip_file");

						require_once('../lib/pclzip.lib.php');
						//$archive = new PclZip($dest_file);
						$archive = new PclZip($dest_zip_file);

						if (($list_file_zip = $archive->listContent()) == 0) {
							echo "<p style='color:red;'>Erreur : ".$archive->errorInfo(true)."</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						if(sizeof($list_file_zip)!=1) {
							echo "<p style='color:red;'>Erreur : L'archive contient plus d'un fichier.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						/*
						echo "<p>\$list_file_zip[0]['filename']=".$list_file_zip[0]['filename']."<br />\n";
						echo "\$list_file_zip[0]['size']=".$list_file_zip[0]['size']."<br />\n";
						echo "\$list_file_zip[0]['compressed_size']=".$list_file_zip[0]['compressed_size']."</p>\n";
						*/
						//echo "<p>\$unzipped_max_filesize=".$unzipped_max_filesize."</p>\n";

						if(($list_file_zip[0]['size']>$unzipped_max_filesize)&&($unzipped_max_filesize>0)) {
							echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<i>".$list_file_zip[0]['size']." octets</i>) d�passe la limite param�tr�e (<i>$unzipped_max_filesize octets</i>).</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$res_extract=$archive->extract(PCLZIP_OPT_PATH, "../temp/".$tempdir);
						if ($res_extract != 0) {
							echo "<p>Le fichier upload� a �t� d�zipp�.</p>\n";
							$fichier_extrait=$res_extract[0]['filename'];
							//echo "Fichier extrait: ".$fichier_extrait."<br />";
							//unlink("$dest_file"); // Pour Wamp...
							$res_copy=rename("$fichier_extrait" , "$dest_file");
						}
						else {
							echo "<p style='color:red'>Echec de l'extraction de l'archive ZIP.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}
					}
					else {
						$res_copy=copy("$source_file" , "$dest_file");
					}
				}
				else {
					$res_copy=copy("$source_file" , "$dest_file");
				}
				//fin  ajout prise en compte des fichiers ZIP
				//===============================================================

				if(!$res_copy) {
					echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a �chou�.<br />V�rifiez que l'utilisateur ou le groupe apache ou www-data a acc�s au dossier temp/$tempdir</p>\n";
					// Il ne faut pas aller plus loin...
					// SITUATION A GERER
					require("../lib/footer.inc.php");
					die();
				}
				else {
					echo "<p>La copie du fichier vers le dossier temporaire a r�ussi.</p>\n";

					$sql="TRUNCATE TABLE tempo2;";
					$res0=mysql_query($sql);
				
					$sql="SELECT e.* FROM eleves e
					LEFT JOIN communes c ON c.code_commune_insee=e.lieu_naissance
					where c.code_commune_insee is NULL;";
					$res=mysql_query($sql);
				
					while($lig=mysql_fetch_object($res)) {
						if($lig->lieu_naissance!='') {
							$sql="INSERT INTO tempo2 SET col1='$lig->login', col2='$lig->lieu_naissance';";
							$res2=mysql_query($sql);
						}
					}

					echo "<script type='text/javascript'>
	setTimeout(\"test_stop('1',1,$nblig)\",3000);
</script>\n";

					//echo "<a href=\"javascript:test_stop('1',0,$nblig)\">Suite</a>";
					echo "<a href=\"".$_SERVER['PHP_SELF']."?step=1&amp;compteur=1&amp;nblig=$nblig".add_token_in_url()."\">Suite</a>";

					require("../lib/footer.inc.php");
					die();
				}
			}
			break;
		case 1:
			echo "<h2>Parcours du fichier des communes</h2>\n";

			// AFFICHER UN TEMON SUR LE NOMBRE DE LIGNES ENCORE PRESENTES... OU DEJA PARCOURUES
			$compteur=isset($_GET['compteur']) ? $_GET['compteur'] : 1;
			//$nblig=isset($_GET['nblig']) ? $_GET['nblig'] : 100;

			$delais=1000;

			$nb_tranches=ceil(38894/$nblig);

			echo "<p>Tranche $compteur/$nb_tranches&nbsp;:";

			$src_file="../temp/".$tempdir."/communes.csv";
			$dest_file="../temp/".$tempdir."/_communes.csv";

			@unlink($dest_file);
			if(!rename($src_file,$dest_file)) {
				echo "<p style='color:red;'>Erreur lors du traitement initial (renommage) du fichier communes.csv en _communes.csv</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			echo " Lecture de $nblig lignes.</p>\n";

			$tab_ele=array();
			$tab_lieu=array();
			$sql="SELECT * FROM tempo2;";
			$res=mysql_query($sql);
			$cpt=0;
			while($lig=mysql_fetch_object($res)) {
				$tab_ele[$cpt]=$lig->col1;
				$tab_lieu[$cpt]=$lig->col2;
				$cpt++;
			}

			$fin_fichier='n';

			$temoin_trouve=0;
			// On ne va lire/traiter que les 100 premi�res lignes du fichier
			// Le fichier en compte 38894... �a fait 389 passages si une commune recherch�e est � la fin...
			$fich=fopen($dest_file,"r");
			for($i=0;$i<$nblig;$i++) {
				if(feof($fich)) {
					$fin_fichier='y';
					break;
				}
				$ligne=trim(fgets($fich,4096));
				//echo "<p>Ligne $i: $ligne<br />\n";

				unset($tab);
				$tab=explode(";",$ligne);
				$code_commune_insee=$tab[0];

				if(in_array($code_commune_insee,$tab_lieu)) {
					// Effectuer le traitement

					$delais=3000;

					$departement=$tab[1];
					$commune=$tab[2];

					if($temoin_trouve==0) {echo "<p>";}
					echo "Lieu de naissance trouv�&nbsp;: $code_commune_insee -&gt; $commune<br />\n";

					$sql="INSERT INTO communes SET code_commune_insee='$code_commune_insee', departement='$departement', commune='".addslashes($commune)."';";
					//echo "$sql<br />\n";
					$insert=mysql_query($sql);

					$sql="DELETE FROM tempo2 WHERE col2='$code_commune_insee';";
					//echo "$sql<br />\n";
					$del=mysql_query($sql);

					$temoin_trouve++;
				}
			}

			$sql="SELECT 1=1 FROM tempo2;";
			$res=mysql_query($sql);
			$nb_eleves_a_traiter=mysql_num_rows($res);

			if($fin_fichier=='y') {
				fclose($fich);
				if($nb_eleves_a_traiter==0) {
					echo "<p>Tous les lieux de naissance ont �t� trouv�s.</p>\n";
					unlink($dest_file);
				}
				else {
					if($nb_eleves_a_traiter==1) {
						echo "<p>Un lieu de naissance n'a pas �t� trouv� et le fichier communes.csv a �t� enti�rement parcouru&nbsp;: \n";
					}
					else {
						echo "<p>Le lieu de naissance n'a pas �t� trouv� pour $nb_eleves_a_traiter �l�ves et le fichier communes.csv a �t� enti�rement parcouru (???)&nbsp;: ";
					}
	
					// A FAIRE: Lister les �l�ves
					$sql="SELECT e.login,e.nom,e.prenom,e.lieu_naissance, t.col2 FROM tempo2 t, eleves e WHERE e.login=t.col1 ORDER BY e.nom, e.prenom;";
					$res=mysql_query($sql);
					$cpt=0;
					if(mysql_num_rows($res)==0) {
						echo "Aucun �l�ve trouv�";
						echo ".</p>\n";
					}
					else {
						echo "<table class='boireaus' summary=\"Tableau des �l�ves pour lequel le lieu de naissance n'est pas dans le CSV.\">\n";
						echo "<tr>\n";
						echo "<th>�l�ve</th>\n";
						echo "<th>Lieu</th>\n";
						echo "</tr>\n";
						$alt=1;
						while($lig=mysql_fetch_object($res)) {
							$alt=$alt*(-1);
							echo "<tr class='lig$alt white_hover'>\n";
							echo "<td>".casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2')."</td>\n";
							echo "<td>$lig->col2</td>\n";
							echo "</tr>\n";
							//if($cpt>0) {echo ", ";}
							//echo casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2')." (<i>$lig->col2</i>)";
							//$cpt++;
						}
						echo "</table>\n";
					}
					//echo ".</p>\n";

					echo "<p><b>NOTE</b>&nbsp;: Les �l�ves n�s dans une commune �trang�re peuvent appara�tre comme non trouv�s dans le fichier de communes.<br />Si les informations entre parenth�ses sont correctes, il n'y a pas lieu de s'alarmer.</p>\n";
				}
			}
			else {

				$suite=fread($fich,filesize($dest_file));
	
				$dest_file="../temp/".$tempdir."/communes.csv";
				$fich2=fopen($dest_file,"w+");
				fwrite($fich2,$suite);
				fclose($fich2);

				fclose($fich);

				if($nb_eleves_a_traiter==0) {
					echo "<p>Tous les lieux de naissance ont �t� trouv�s.</p>\n";
					unlink($dest_file);
				}
				else {
					if($nb_eleves_a_traiter==1) {
						echo "<p>Un lieu de naissance doit encore �tre recherch�.</p>\n";
					}
					else {
						echo "<p>Les lieux de naissance doivent encore �tre recherch�s pour $nb_eleves_a_traiter �l�ves.</p>\n";
					}
		
					// Si on n'a pas trouv� tous les lieux de naissance manquants: G�n�rer le code javascript pour relancer la boucle
	
					$compteur++;
	
					echo "<script type='text/javascript'>
	//setTimeout(\"test_stop('1',$compteur,$nblig)\",3000);
	setTimeout(\"test_stop('1','$compteur','$nblig')\",$delais);
</script>\n";
	
					//echo "<a href=\"javascript:test_stop('1',$compteur,$nblig)\">Suite</a>";
					echo "<a href=\"".$_SERVER['PHP_SELF']."?step=1&amp;compteur=$compteur&amp;nblig=$nblig".add_token_in_url()."\">Suite</a>";
				}
			}
			break;
	}
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
