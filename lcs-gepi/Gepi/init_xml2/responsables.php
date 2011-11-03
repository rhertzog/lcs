<?php
	@set_time_limit(0);

	// $Id: responsables.php 8556 2011-10-28 09:44:45Z crob $

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

	//**************** EN-TETE *****************
	$titre_page = "Outil d'initialisation de l'ann�e : Importation des responsables des �l�ves";
	require_once("../lib/header.inc");
	//**************** FIN EN-TETE *****************

	require_once("init_xml_lib.php");

	function extr_valeur($lig){
		unset($tabtmp);
		$tabtmp=explode(">",my_ereg_replace("<",">",$lig));
		return trim($tabtmp[2]);
	}

	function ouinon($nombre){
		if($nombre==1){return "O";}elseif($nombre==0){return "N";}else{return "";}
	}
	function sexeMF($nombre){
		//if($nombre==2){return "F";}else{return "M";}
		if($nombre==2){return "F";}elseif($nombre==1){return "M";}else{return "";}
	}

	function maj_min_comp($chaine){
		$tmp_tab1=explode(" ",$chaine);
		$new_chaine="";
		for($i=0;$i<count($tmp_tab1);$i++){
			$tmp_tab2=explode("-",$tmp_tab1[$i]);
			$new_chaine.=ucfirst(strtolower($tmp_tab2[0]));
			for($j=1;$j<count($tmp_tab2);$j++){
				$new_chaine.="-".ucfirst(strtolower($tmp_tab2[$j]));
			}
			$new_chaine.=" ";
		}
		$new_chaine=trim($new_chaine);
		return $new_chaine;
	}

	function affiche_debug($texte){
		// Passer � 1 la variable pour g�n�rer l'affichage des infos de debug...
		$debug=0;
		if($debug==1){
			echo "<font color='green'>".$texte."</font>";
		}
	}

	// Etape...
	$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);

	$verif_tables_non_vides=isset($_POST['verif_tables_non_vides']) ? $_POST['verif_tables_non_vides'] : NULL;

	// Passer � 'y' pour afficher les requ�tes
	$debug_resp='n';

	if(isset($_GET['ad_retour'])){
		$_SESSION['ad_retour']=$_GET['ad_retour'];
	}
	//echo "\$_SESSION['ad_retour']=".$_SESSION['ad_retour']."<br />";

	include("../lib/initialisation_annee.inc.php");
	$liste_tables_del = $liste_tables_del_etape_resp;


	// On va uploader les fichiers XML dans le tempdir de l'utilisateur (administrateur, ou scolarit� pour les m�j Sconet)
	$tempdir=get_user_temp_directory();
	if(!$tempdir){
		echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas d�fini!?</p>\n";
		// Il ne faut pas aller plus loin...
		// SITUATION A GERER
	}


	// =======================================================
	// EST-CE ENCORE UTILE?
	if(isset($_GET['nettoyage'])) {
		check_token(false);
		//echo "<h1 align='center'>Suppression des CSV</h1>\n";
		echo "<h2>Suppression des XML</h2>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])){
			echo $_SESSION['ad_retour'];
		}
		else{
			echo "index.php";
		}
		echo "'> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
		echo "<a href='".$_SERVER['PHP_SELF']."'> | Autre import</a></p>\n";
		//echo "</div>\n";

		echo "<p>Si des fichiers XML existent, ils seront supprim�s...</p>\n";
		//$tabfich=array("f_ele.csv","f_ere.csv");
		$tabfich=array("responsables.xml");

		for($i=0;$i<count($tabfich);$i++){
			if(file_exists("../temp/".$tempdir."/$tabfich[$i]")) {
				echo "<p>Suppression de $tabfich[$i]... ";
				if(unlink("../temp/".$tempdir."/$tabfich[$i]")){
					echo "r�ussie.</p>\n";
				}
				else{
					echo "<font color='red'>Echec!</font> V�rifiez les droits d'�criture sur le serveur.</p>\n";
				}
			}
		}

		require("../lib/footer.inc.php");
		die();
	}
	// =======================================================
	else {
		echo "<center><h3 class='gepi'>Deuxi�me phase d'initialisation<br />Importation des responsables</h3></center>\n";
		echo "<p class=bold><a href='";
		if(isset($_SESSION['ad_retour'])){
			// On peut venir de l'index init_xml, de la page de conversion ou de la page de mise � jour Sconet
			echo $_SESSION['ad_retour'];
		}
		else{
			echo "index.php";
		}
		echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

		//echo " | <a href='".$_SERVER['PHP_SELF']."'>Autre import</a>";
		echo " | <a href='".$_SERVER['PHP_SELF']."?nettoyage=oui".add_token_in_url()."'>Suppression des fichiers XML existants</a>";
		echo "</p>\n";
		//echo "</div>\n";

		//debug_var();

		//if(!isset($_POST['is_posted'])){
		if(!isset($step)) {
			if(!isset($verif_tables_non_vides)) {
				$j=0;
				$flag=0;
				$chaine_tables="";
				while (($j < count($liste_tables_del)) and ($flag==0)) {
					if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
						$flag=1;
					}
					$j++;
				}
				for($loop=0;$loop<count($liste_tables_del);$loop++) {
					if($chaine_tables!="") {$chaine_tables.=", ";}
					$chaine_tables.="'".$liste_tables_del[$loop]."'";
				}

				if ($flag != 0){
					echo "<p><b>ATTENTION ...</b><br />\n";
					echo "Des donn�es concernant les responsables sont actuellement pr�sentes dans la base GEPI<br /></p>\n";
					echo "<p>Si vous poursuivez la proc�dure ces donn�es seront effac�es.</p>\n";

					echo "<p>Les tables vid�es seront&nbsp;: $chaine_tables</p>\n";

					echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
					echo add_token_field();
					echo "<input type=hidden name='verif_tables_non_vides' value='y' />\n";
					echo "<input type='submit' name='confirm' value='Poursuivre la proc�dure' />\n";
					echo "</form>\n";
					require("../lib/footer.inc.php");
					die();
				}
			}

			//echo "<p>Cette page permet de remplir des tables temporaires avec les informations responsables.<br />\n";
			//echo "</p>\n";

			// Sauvegarde temporaire:
			$sql="CREATE TABLE IF NOT EXISTS tempo_utilisateurs_resp
			(login VARCHAR( 50 ) NOT NULL PRIMARY KEY,
			password VARCHAR(128) NOT NULL,
			salt VARCHAR(128) NOT NULL,
			email VARCHAR(50) NOT NULL,
			pers_id VARCHAR( 10 ) NOT NULL ,
			statut VARCHAR( 20 ) NOT NULL ,
			auth_mode ENUM('gepi','ldap','sso') NOT NULL default 'gepi',
			temoin VARCHAR( 50 ) NOT NULL
			);";
			//auth_mode ENUM('gepi','ldap','sso') NOT NULL ,
			//auth_mode ENUM('gepi','ldap','sso') NOT NULL default 'gepi',
			//auth_mode VARCHAR( 4 ) NOT NULL ,
			if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
			$creation_table=mysql_query($sql);

			$sql="TRUNCATE TABLE tempo_utilisateurs_resp;";
			if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
			$nettoyage=mysql_query($sql);

			// Il faut faire cette �tape avant de vider la table resp_pers via $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
			$sql="INSERT INTO tempo_utilisateurs_resp SELECT u.login,u.password,u.salt,u.email,rp.pers_id,u.statut,u.auth_mode,u.statut FROM utilisateurs u, resp_pers rp WHERE u.login=rp.login AND rp.login!='' AND u.statut='responsable';";
			if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
			$svg_insert=mysql_query($sql);

	
			if(isset($verif_tables_non_vides)) {
				check_token(false);
				$j=0;
				while ($j < count($liste_tables_del)) {
					if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
						$sql="DELETE FROM $liste_tables_del[$j];";
						if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
						$del=@mysql_query($sql);
					}
					$j++;
				}
	
				// Suppression des comptes de responsables:
				$sql="DELETE FROM utilisateurs WHERE statut='responsable';";
				//echo "$sql<br />";
				if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
				$del=mysql_query($sql);
			}

			echo "<p><b>ATTENTION ...</b><br />Vous ne devez proc�der � cette op�ration uniquement si la constitution des classes a �t� effectu�e !</p>\n";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo add_token_field();
			echo "<p>Veuillez fournir le fichier ResponsablesAvecAdresses.xml:<br />\n";
			echo "<input type=\"file\" size=\"65\" name=\"responsables_xml_file\" /><br />\n";
			if ($gepiSettings['unzipped_max_filesize']>=0) {
				echo "<p style=\"font-size:small; color: red;\"><i>REMARQUE&nbsp;:</i> Vous pouvez fournir � Gepi le fichier compress� issu directement de SCONET. (Ex : ResponsablesAvecAdresses.zip)</p>";
			}
			echo "<input type='hidden' name='step' value='0' />\n";
			echo "<input type='hidden' name='is_posted' value='yes' />\n";
			echo "<p><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";
		}
		else {
			check_token(false);
			$post_max_size=ini_get('post_max_size');
			$upload_max_filesize=ini_get('upload_max_filesize');
			$max_execution_time=ini_get('max_execution_time');
			$memory_limit=ini_get('memory_limit');


			if($step==0){
				$xml_file = isset($_FILES["responsables_xml_file"]) ? $_FILES["responsables_xml_file"] : NULL;
				if(!is_uploaded_file($xml_file['tmp_name'])) {
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
				else{
					if(!file_exists($xml_file['tmp_name'])){
						echo "<p style='color:red;'>Le fichier aurait �t� upload�... mais ne serait pas pr�sent/conserv�.</p>\n";

						echo "<p>Les variables du php.ini peuvent peut-�tre expliquer le probl�me:<br />\n";
						echo "post_max_size=$post_max_size<br />\n";
						echo "upload_max_filesize=$upload_max_filesize<br />\n";
						echo "et le volume de ".$xml_file['name']." serait<br />\n";
						echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
						echo "</p>\n";
						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}

					echo "<p>Le fichier a �t� upload�.</p>\n";

					//$source_file=stripslashes($xml_file['tmp_name']);
					$source_file=$xml_file['tmp_name'];
					$dest_file="../temp/".$tempdir."/responsables.xml";
					$res_copy=copy("$source_file" , "$dest_file");

					//===============================================================
					// ajout prise en compte des fichiers ZIP: Marc Leygnac

					$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
					// $unzipped_max_filesize = 0    pas de limite de taille pour les fichiers extraits
					// $unzipped_max_filesize < 0    extraction zip d�sactiv�e
					if($unzipped_max_filesize>=0) {
						$fichier_emis=$xml_file['name'];
						$extension_fichier_emis=strtolower(strrchr($fichier_emis,"."));
						if (($extension_fichier_emis==".zip")||($xml_file['type']=="application/zip"))
							{
							require_once('../lib/pclzip.lib.php');
							$archive = new PclZip($dest_file);

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
								unlink("$dest_file"); // Pour Wamp...
								$res_copy=rename("$fichier_extrait" , "$dest_file");
							}
							else {
								echo "<p style='color:red'>Echec de l'extraction de l'archive ZIP.</p>\n";
								require("../lib/footer.inc.php");
								die();
							}
						}
					}
					//fin  ajout prise en compte des fichiers ZIP
					//===============================================================

					if(!$res_copy){
						echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a �chou�.<br />V�rifiez que l'utilisateur ou le groupe apache ou www-data a acc�s au dossier temp/$tempdir</p>\n";
						// Il ne faut pas aller plus loin...
						// SITUATION A GERER
						require("../lib/footer.inc.php");
						die();
					}
					else{
						echo "<p>La copie du fichier vers le dossier temporaire a r�ussi.</p>\n";

						//$sql="CREATE TABLE IF NOT EXISTS temp_resp_pers_import (
						$sql="CREATE TABLE IF NOT EXISTS resp_pers (
								`pers_id` varchar(10) NOT NULL,
								`login` varchar(50) NOT NULL,
								`nom` varchar(30) NOT NULL,
								`prenom` varchar(30) NOT NULL,
								`civilite` varchar(5) NOT NULL,
								`tel_pers` varchar(255) NOT NULL,
								`tel_port` varchar(255) NOT NULL,
								`tel_prof` varchar(255) NOT NULL,
								`mel` varchar(100) NOT NULL,
								`adr_id` varchar(10) NOT NULL,
							PRIMARY KEY  (`pers_id`));";
						$create_table = mysql_query($sql);

						//$sql="TRUNCATE TABLE temp_resp_pers_import;";
						$sql="TRUNCATE TABLE resp_pers;";
						$vide_table = mysql_query($sql);

						/*
						// On va lire plusieurs fois le fichier pour remplir des tables temporaires.
						$fp=fopen($dest_file,"r");
						if($fp){
							echo "<p>Lecture du fichier Responsables...<br />\n";
							//echo "<blockquote>\n";
							while(!feof($fp)){
								$ligne[]=fgets($fp,4096);
							}
							fclose($fp);
							//echo "<p>Termin�.</p>\n";
						}
						*/
						flush();

						$resp_xml=simplexml_load_file($dest_file);
						if(!$resp_xml) {
							echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$nom_racine=$resp_xml->getName();
						if(strtoupper($nom_racine)!='BEE_RESPONSABLES') {
							echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'�tre un fichier XML Responsables.<br />Sa racine devrait �tre 'BEE_RESPONSABLES'.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						echo "<p>Analyse du fichier pour extraire les informations de la section PERSONNES...<br />\n";

						$personnes=array();

						$tab_champs_personne=array("NOM",
						"PRENOM",
						"LC_CIVILITE",
						"TEL_PERSONNEL",
						"TEL_PORTABLE",
						"TEL_PROFESSIONNEL",
						"MEL",
						"ACCEPTE_SMS",
						"ADRESSE_ID",
						"CODE_PROFESSION",
						"COMMUNICATION_ADRESSE"
						);

						// PARTIE <PERSONNES>
						// Compteur personnes:
						$i=-1;

						$objet_personnes=($resp_xml->DONNEES->PERSONNES);
						foreach ($objet_personnes->children() as $personne) {
							//echo("<p><b>Personne</b><br />");

							$i++;
							$personnes[$i]=array();

							foreach($personne->attributes() as $key => $value) {
								// <PERSONNE PERSONNE_ID="294435">
								$personnes[$i][strtolower($key)]=traitement_magic_quotes(corriger_caracteres(trim($value)));
							}

							foreach($personne->children() as $key => $value) {
								if(in_array(strtoupper($key),$tab_champs_personne)) {
									$personnes[$i][strtolower($key)]=traitement_magic_quotes(corriger_caracteres(preg_replace('/"/','',trim(traite_utf8($value)))));
									//echo "\$structure->$key=".$value."<br />";
								}
							}

							if($debug_import=='y') {
								echo "<pre style='color:green;'><b>Tableau \$personnes[$i]&nbsp;:</b>";
								print_r($personnes[$i]);
								echo "</pre>";
							}
						}

						//traitement_magic_quotes(corriger_caracteres())
						$nb_err=0;
						$stat=0;
						$i=0;
						while($i<count($personnes)){
							//$sql="INSERT INTO temp_resp_pers_import SET ";
							$sql="INSERT INTO resp_pers SET ";
							$sql.="pers_id='".$personnes[$i]["personne_id"]."', ";
							$sql.="nom='".$personnes[$i]["nom"]."', ";
							$sql.="prenom='".$personnes[$i]["prenom"]."', ";
							if(isset($personnes[$i]["lc_civilite"])){
								$sql.="civilite='".ucfirst(strtolower($personnes[$i]["lc_civilite"]))."', ";
							}
							if(isset($personnes[$i]["tel_personnel"])){
								$sql.="tel_pers='".$personnes[$i]["tel_personnel"]."', ";
							}
							if(isset($personnes[$i]["tel_portable"])){
								$sql.="tel_port='".$personnes[$i]["tel_portable"]."', ";
							}
							if(isset($personnes[$i]["tel_professionnel"])){
								$sql.="tel_prof='".$personnes[$i]["tel_professionnel"]."', ";
							}
							if(isset($personnes[$i]["mel"])){
								$sql.="mel='".$personnes[$i]["mel"]."', ";
							}
							if(isset($personnes[$i]["adresse_id"])){
								$sql.="adr_id='".$personnes[$i]["adresse_id"]."';";
							}
							else{
								$sql.="adr_id='';";
								// IL FAUDRAIT PEUT-ETRE REMPLIR UN TABLEAU
								// POUR SIGNALER QUE CE RESPONSABLE RISQUE DE POSER PB...
								// ... CEPENDANT, CEUX QUE J'AI REP�R�S ETAIENT resp_legal=0
								// ILS NE DEVRAIENT PAS ETRE DESTINATAIRES DE BULLETINS,...
							}
							affiche_debug("$sql<br />\n");
							$res_insert=mysql_query($sql);
							if(!$res_insert){
								echo "Erreur lors de la requ�te $sql<br />\n";
								flush();
								$nb_err++;
							}
							else{

								$sql="SELECT * FROM tempo_utilisateurs_resp WHERE pers_id='".$personnes[$i]["personne_id"]."';";
								if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
								$res_tmp_u=mysql_query($sql);
								if(mysql_num_rows($res_tmp_u)>0) {
									$lig_tmp_u=mysql_fetch_object($res_tmp_u);

									$sql="INSERT INTO utilisateurs SET login='".$lig_tmp_u->login."', nom='".$personnes[$i]["nom"]."', prenom='".$personnes[$i]["prenom"]."', ";
									if(isset($personnes[$i]["lc_civilite"])){
										$sql.="civilite='".ucfirst(strtolower($personnes[$i]["lc_civilite"]))."', ";
									}
									$sql.="password='".$lig_tmp_u->password."', salt='".$lig_tmp_u->salt."', email='".$lig_tmp_u->email."', statut='responsable', etat='inactif', change_mdp='n', auth_mode='".$lig_tmp_u->auth_mode."';";
									if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
									$insert_u=mysql_query($sql);
									if(!$insert_u) {
										echo "Erreur lors de la cr�ation du compte utilisateur pour ".$personnes[$i]["nom"]." ".$personnes[$i]["prenom"].".<br />";
									}
									else {
										$sql="UPDATE resp_pers SET login='".$lig_tmp_u->login."' WHERE pers_id='".$personnes[$i]["personne_id"]."';";
										if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
										$update_rp=mysql_query($sql);

										$sql="UPDATE tempo_utilisateurs_resp SET temoin='recree' WHERE pers_id='".$personnes[$i]["personne_id"]."';";
										if($debug_resp=='y') {echo "<span style='color:green;'>$sql</span><br />";}
										$update_tmp_u=mysql_query($sql);
									}
								}

								$stat++;
							}

							$i++;
						}

						if ($nb_err != 0) {
							echo "<p>Lors de l'enregistrement des donn�es PERSONNES, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la proc�dure avant de passer � l'�tape suivante.</p>\n";
						} else {
							echo "<p>L'importation des personnes (responsables) dans la base GEPI a �t� effectu�e avec succ�s (".$stat." enregistrements au total).</p>\n";
						}

						//echo "<p>$stat enregistrement(s) ont �t� ins�r�(s) dans la table 'temp_resp_pers_import'.</p>\n";
						//echo "<p>$stat enregistrement(s) ont �t� ins�r�(s) dans la table 'resp_pers'.</p>\n";

						echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=1".add_token_in_url()."'>Suite</a></p>\n";

						require("../lib/footer.inc.php");
						die();
					}
				}
			} // Fin du $step=0
			elseif($step==1) {
				check_token(false);

				$dest_file="../temp/".$tempdir."/responsables.xml";

				$resp_xml=simplexml_load_file($dest_file);
				if(!$resp_xml) {
					echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				$nom_racine=$resp_xml->getName();
				if(strtoupper($nom_racine)!='BEE_RESPONSABLES') {
					echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'�tre un fichier XML Responsables.<br />Sa racine devrait �tre 'BEE_RESPONSABLES'.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				//$sql="CREATE TABLE IF NOT EXISTS temp_responsables2_import (
				$sql="CREATE TABLE IF NOT EXISTS responsables2 (
						`ele_id` varchar(10) NOT NULL,
						`pers_id` varchar(10) NOT NULL,
						`resp_legal` varchar(1) NOT NULL,
						`pers_contact` varchar(1) NOT NULL
						);";
				$create_table = mysql_query($sql);

				//$sql="TRUNCATE TABLE temp_responsables2_import;";
				$sql="TRUNCATE TABLE responsables2;";
				$vide_table = mysql_query($sql);

				/*
				echo "<p>Lecture du fichier Responsables...<br />\n";
				while(!feof($fp)){
					$ligne[]=fgets($fp,4096);
				}
				fclose($fp);
				*/
				flush();

				echo "<p>";
				echo "Analyse du fichier pour extraire les informations de la section RESPONSABLES...<br />\n";

				$responsables=array();

				$tab_champs_responsable=array("ELEVE_ID",
				"PERSONNE_ID",
				"RESP_LEGAL",
				"CODE_PARENTE",
				"RESP_FINANCIER",
				"PERS_PAIMENT",
				"PERS_CONTACT"
				);

				// PARTIE <RESPONSABLES>
				// Compteur responsables:
				$i=-1;

				$objet_resp=($resp_xml->DONNEES->RESPONSABLES);
				foreach ($objet_resp->children() as $responsable_eleve) {
					//echo("<p><b>Personne</b><br />");

					$i++;
					$responsables[$i]=array();

					foreach($responsable_eleve->children() as $key => $value) {
						if(in_array(strtoupper($key),$tab_champs_responsable)) {
							$responsables[$i][strtolower($key)]=traitement_magic_quotes(corriger_caracteres(preg_replace('/"/','',trim(traite_utf8($value)))));
							//echo "\$structure->$key=".$value."<br />";
						}
					}

					if($debug_import=='y') {
						echo "<pre style='color:green;'><b>Tableau \$responsables[$i]&nbsp;:</b>";
						print_r($responsables[$i]);
						echo "</pre>";
					}
				}

				$nb_err=0;
				$stat=0;
				$i=0;
				while($i<count($responsables)){
					// VERIFICATION: Il arrive que Sconet contienne des anomalies
					$sql="SELECT 1=1 FROM responsables2 WHERE ";
					$sql.="ele_id='".$responsables[$i]["eleve_id"]."' AND ";
					$sql.="resp_legal='".$responsables[$i]["resp_legal"]."' AND ";
					$sql.="(resp_legal='1' OR resp_legal='2');";
					affiche_debug("$sql<br />\n");
					$res_test=mysql_query($sql);
					if(mysql_num_rows($res_test)==0){
						//$sql="INSERT INTO temp_responsables2_import SET ";
						$sql="INSERT INTO responsables2 SET ";
						$sql.="ele_id='".$responsables[$i]["eleve_id"]."', ";
						$sql.="pers_id='".$responsables[$i]["personne_id"]."', ";
						$sql.="resp_legal='".$responsables[$i]["resp_legal"]."', ";
						$sql.="pers_contact='".$responsables[$i]["pers_contact"]."';";
						affiche_debug("$sql<br />\n");
						$res_insert=mysql_query($sql);
						if(!$res_insert){
							echo "Erreur lors de la requ�te $sql<br />\n";
							flush();
							$nb_err++;
						}
						else{
							$stat++;
						}
					}
					else {
						$sql="SELECT nom, prenom FROM eleves WHERE ele_id='".$responsables[$i]["eleve_id"]."';";
						affiche_debug("$sql<br />\n");
						$res_ele_anomalie=mysql_query($sql);
						if(mysql_num_rows($res_ele_anomalie)>0){
							$lig_ele_anomalie=mysql_fetch_object($res_ele_anomalie);
							echo "<p><b style='color:red;'>Anomalie sconet:</b> Plusieurs responsables l�gaux n�<b>".$responsables[$i]["resp_legal"]."</b> sont d�clar�s pour l'�l�ve ".$lig_ele_anomalie->prenom." ".$lig_ele_anomalie->nom."<br />Seule la premi�re responsabilit� a �t� enregistr�e.<br />Vous devriez faire le m�nage dans Sconet et faire une mise � jour par la suite.</p>\n";

							$nb_err++;
						}
						else {

							$sql="SELECT ELENOM, ELEPRE, DIVCOD FROM temp_gep_import2 WHERE ELE_ID='".$responsables[$i]["eleve_id"]."';";
							affiche_debug("$sql<br />\n");
							$res_ele_anomalie=mysql_query($sql);
							if(mysql_num_rows($res_ele_anomalie)>0){
								// Si l'�l�ve associ� n'est ni dans 'eleves', ni dans 'temp_gep_import2', on ne s'en occupe pas.

								$sql="SELECT civilite,nom,prenom FROM temp_resp_pers_import WHERE pers_id='".$responsables[$i]["personne_id"]."';";
								$res_resp_anomalie=mysql_query($sql);
								if(mysql_num_rows($res_resp_anomalie)>0){
									$lig_resp_anomalie=mysql_fetch_object($res_resp_anomalie);

									echo "<p><b style='color:red;'>Anomalie sconet:</b> Plusieurs responsables l�gaux n�<b>".$responsables[$i]["resp_legal"]."</b> sont d�clar�s pour l'�l�ve ".$lig_ele_anomalie->ELEPRE." ".$lig_ele_anomalie->ELENOM." (<em>".$lig_ele_anomalie->DIVCOD."</em>).<br />L'un d'eux est: ".$lig_resp_anomalie->civilite." ".$lig_resp_anomalie->nom." ".$lig_resp_anomalie->prenom."</p>\n";
								}
								else {
									echo "<p><b style='color:red;'>Anomalie sconet:</b> Plusieurs responsables l�gaux n�<b>".$responsables[$i]["resp_legal"]."</b> sont d�clar�s pour l'�l�ve ".$lig_ele_anomalie->ELEPRE." ".$lig_ele_anomalie->ELENOM." (<em>".$lig_ele_anomalie->DIVCOD."</em>)<br />L'�l�ve n'a semble-t-il pas �t� ajout� � la table 'eleves'.<br />Par ailleurs, la personne responsable semble inexistante, mais l'association avec l'identifiant de responsable n�".$responsables[$i]["personne_id"]." existe dans le XML fourni???<br />L'anomalie n'est pas grave pour Gepi; par contre il serait bon de corriger dans Sconet.</p>\n";
								}

								$nb_err++;
							}
						}
						//$nb_err++;
					}

					$i++;
				}

				$sql="SELECT r.pers_id,r.ele_id FROM responsables2 r LEFT JOIN eleves e ON e.ele_id=r.ele_id WHERE e.ele_id is NULL;";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0){
					echo "<p>Suppression de responsabilit�s sans �l�ve.\n";
					flush();
					$cpt_nett=0;
					while($lig_nett=mysql_fetch_object($test)){
						//if($cpt_nett>0){echo ", ";}
						//echo "<a href='modify_resp.php?pers_id=$lig_nett->pers_id' target='_blank'>".$lig_nett->pers_id."</a>";
						$sql="DELETE FROM responsables2 WHERE pers_id='$lig_nett->pers_id' AND ele_id='$lig_nett->ele_id';";
						$nettoyage=mysql_query($sql);
						//flush();
						$cpt_nett++;
					}
					//echo ".</p>\n";
					echo "<br />$cpt_nett associations aberrantes supprim�es.</p>\n";
				}

				if ($nb_err!=0) {
					echo "<p>Lors de l'enregistrement des donn�es de RESPONSABLES, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la proc�dure avant de passer � l'�tape suivante.</p>\n";
				}
				else {
					echo "<p>L'importation des relations eleves/responsables dans la base GEPI a �t� effectu�e avec succ�s (".$stat." enregistrements au total).</p>\n";
				}

				//echo "<p>$stat enregistrement(s) ont �t� ins�r�(s) dans la table 'temp_responsables2_import'.</p>\n";

				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=2".add_token_in_url()."'>Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			} // Fin du $step=1
			elseif($step==2){
				check_token(false);
				$dest_file="../temp/".$tempdir."/responsables.xml";

				$resp_xml=simplexml_load_file($dest_file);
				if(!$resp_xml) {
					echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				$nom_racine=$resp_xml->getName();
				if(strtoupper($nom_racine)!='BEE_RESPONSABLES') {
					echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'�tre un fichier XML Responsables.<br />Sa racine devrait �tre 'BEE_RESPONSABLES'.</p>\n";
					require("../lib/footer.inc.php");
					die();
				}

				//$sql="CREATE TABLE IF NOT EXISTS temp_resp_adr_import (
				$sql="CREATE TABLE IF NOT EXISTS resp_adr (
						`adr_id` varchar(10) NOT NULL,
						`adr1` varchar(100) NOT NULL,
						`adr2` varchar(100) NOT NULL,
						`adr3` varchar(100) NOT NULL,
						`adr4` varchar(100) NOT NULL,
						`cp` varchar(6) NOT NULL,
						`pays` varchar(50) NOT NULL,
						`commune` varchar(50) NOT NULL,
					PRIMARY KEY  (`adr_id`));";
				$create_table = mysql_query($sql);

				//$sql="TRUNCATE TABLE temp_resp_adr_import;";
				$sql="TRUNCATE TABLE resp_adr;";
				$vide_table = mysql_query($sql);

				/*
				echo "<p>Lecture du fichier Responsables...<br />\n";
				while(!feof($fp)){
					$ligne[]=fgets($fp,4096);
				}
				fclose($fp);
				*/
				flush();


				echo "Analyse du fichier pour extraire les informations de la section ADRESSES...<br />\n";

				$adresses=array();

				$tab_champs_adresse=array("LIGNE1_ADRESSE",
				"LIGNE2_ADRESSE",
				"LIGNE3_ADRESSE",
				"LIGNE4_ADRESSE",
				"CODE_POSTAL",
				"LL_PAYS",
				"CODE_DEPARTEMENT",
				"LIBELLE_POSTAL",
				"COMMUNE_ETRANGERE"
				);

				// PARTIE <ADRESSES>
				// Compteur adresses:
				$i=-1;

				$objet_adresses=($resp_xml->DONNEES->ADRESSES);
				foreach ($objet_adresses->children() as $adresse) {
					//echo("<p><b>Adresse</b><br />");

					$i++;
					$adresses[$i]=array();

					foreach($adresse->attributes() as $key => $value) {
						// <ADRESSE ADRESSE_ID="228114">
						$adresses[$i][strtolower($key)]=$value;
					}

					foreach($adresse->children() as $key => $value) {
						if(in_array(strtoupper($key),$tab_champs_adresse)) {
							$adresses[$i][strtolower($key)]=traitement_magic_quotes(corriger_caracteres(preg_replace('/"/','',trim(traite_utf8($value)))));
							//echo "\$structure->$key=".$value."<br />";
						}
					}

					if($debug_import=='y') {
						echo "<pre style='color:green;'><b>Tableau \$adresses[$i]&nbsp;:</b>";
						print_r($adresses[$i]);
						echo "</pre>";
					}
				}

				$nb_err=0;
				$stat=0;
				$i=0;
				while($i<count($adresses)){
					//$sql="INSERT INTO temp_resp_adr_import SET ";
					$sql="INSERT INTO resp_adr SET ";
					$sql.="adr_id='".$adresses[$i]["adresse_id"]."', ";
					if(isset($adresses[$i]["ligne1_adresse"])){
						$sql.="adr1='".$adresses[$i]["ligne1_adresse"]."', ";
					}
					if(isset($adresses[$i]["ligne2_adresse"])){
						$sql.="adr2='".$adresses[$i]["ligne2_adresse"]."', ";
					}
					if(isset($adresses[$i]["ligne3_adresse"])){
						$sql.="adr3='".$adresses[$i]["ligne3_adresse"]."', ";
					}
					if(isset($adresses[$i]["ligne4_adresse"])){
						$sql.="adr4='".$adresses[$i]["ligne4_adresse"]."', ";
					}
					if(isset($adresses[$i]["code_postal"])){
						$sql.="cp='".$adresses[$i]["code_postal"]."', ";
					}
					if(isset($adresses[$i]["ll_pays"])){
						$sql.="pays='".$adresses[$i]["ll_pays"]."', ";
					}
					if(isset($adresses[$i]["libelle_postal"])){
						$sql.="commune='".$adresses[$i]["libelle_postal"]."', ";
					} elseif(isset($adresses[$i]["commune_etrangere"])) {
						$sql.="commune='".$adresses[$i]["commune_etrangere"]."', ";
					}
					$sql=substr($sql,0,strlen($sql)-2);
					$sql.=";";
					affiche_debug("$sql<br />\n");
					$res_insert=mysql_query($sql);
					if(!$res_insert){
						echo "Erreur lors de la requ�te $sql<br />\n";
						flush();
						$nb_err++;
					}
					else{
						$stat++;
					}

					$i++;
				}

				if ($nb_err != 0) {
					echo "<p>Lors de l'enregistrement des donn�es ADRESSES des responsables, il y a eu $nb_err erreurs. Essayez de trouvez la cause de l'erreur et recommencez la proc�dure avant de passer � l'�tape suivante.</p>\n";
				} else {
					echo "<p>L'importation des adresses de responsables dans la base GEPI a �t� effectu�e avec succ�s (".$stat." enregistrements au total).</p>\n";
				}
				//echo "<p>$stat enregistrement(s) ont �t� mis � jour dans la table 'temp_resp_adr_import'.</p>\n";

				echo "<p align='center'><a href='".$_SERVER['PHP_SELF']."?step=3".add_token_in_url()."'>Suite</a></p>\n";

				require("../lib/footer.inc.php");
				die();
			}
			else{
				check_token(false);
				// TERMIN�?
				// A LA DERNIERE ETAPE, IL FAUT SUPPRIMER LE FICHIER "../temp/".$tempdir."/responsables.xml"

				if(file_exists("../temp/".$tempdir."/responsables.xml")) {
					echo "<p>Suppression de responsables.xml... ";
					if(unlink("../temp/".$tempdir."/responsables.xml")){
						echo "r�ussie.</p>\n";
					}
					else{
						echo "<font color='red'>Echec!</font> V�rifiez les droits d'�criture sur le serveur.</p>\n";
					}
				}

				/*
				if(($nb_reg_no1==0)&&($nb_reg_no2==0)&&($nb_reg_no3==0)){
					echo "<p>Vous pouvez � pr�sent retourner � l'accueil et effectuer toutes les autres op�rations d'initialisation manuellement ou bien proc�der � la troixi�me phase d'importation des mati�res et de d�finition des options suivies par les �l�ves.</p>\n";
					echo "<center><p><a href='../accueil.php'>Retourner � l'accueil</a></p></center>\n";
					echo "<center><p><a href='disciplines_csv.php'>Proc�der � la troisi�me phase</a>.</p></center>\n";
				}
				*/

				//echo "<center><p><a href='../accueil.php'>Retourner � l'accueil</a></p></center>\n";
				echo "<center><p><a href='matieres.php'>Proc�der � la troisi�me phase</a>.</p></center>\n";

				require("../lib/footer.inc.php");
				die();
			}
		}
	}

	require("../lib/footer.inc.php");
?>