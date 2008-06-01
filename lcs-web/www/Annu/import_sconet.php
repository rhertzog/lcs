<?php
	/*
		Page d'import des comptes depuis les fichiers CSV/XML de Sconet
		Auteur: St�phane Boireau (Animateur de Secteur pour les TICE sur Bernay/Pont-Audemer (27))
                Portage LCSorSE3 : Jean-Luc Chr�tien jean-luc.chretien@tice.ac-caen.fr
                Derni�re modification: 11/10/2007
	*/
        if ( file_exists("/var/www/se3") )
	   include "se3orlcs_import_sconet.php";
        else include "includes/se3orlcs_import_sconet.php";

	if (is_admin("Annu_is_admin",$login)=="Y") {
                require ( $pathlcsorse3."config.inc.php");
		require ( $path_crob_ldap_functions."crob_ldap_functions.php");

		echo "<h2>Import des comptes, groupes,...</h2>\n";


		if((isset($_GET['nettoyage']))&&(isset($_GET['dossier']))){
			//nettoyage=oui&amp;dossier=".$timestamp."_".$randval."

			echo "<h2>Suppression des fichiers CSV g�n�r�s</h2>\n";
			echo "<blockquote>\n";
			// Filtrer le $_GET['dossier']... A FAIRE
			if(file_exists($racine_www.$chemin_csv."/".$_GET['dossier'])){

				if(is_dir($racine_www.$chemin_csv."/".$_GET['dossier'])){
					echo "<p>Suppression de:</p>\n";
					echo "<ul>\n";
					if(file_exists($racine_www.$chemin_csv."/".$_GET['dossier']."/f_ele.txt")){
						echo "<li>f_ele.txt: ";
						if(unlink($racine_www.$chemin_csv."/".$_GET['dossier']."/f_ele.txt")){
							echo "<font color='green'>SUCCES</font>";
						}
						else{
							echo "<font color='red'>ECHEC</font>";
						}
						echo "</li>\n";
					}
					if(file_exists($racine_www.$chemin_csv."/".$_GET['dossier']."/f_div.txt")){
						echo "<li>f_div.txt: ";
						if(unlink($racine_www.$chemin_csv."/".$_GET['dossier']."/f_div.txt")){
							echo "<font color='green'>SUCCES</font>";
						}
						else{
							echo "<font color='red'>ECHEC</font>";
						}
						echo "</li>\n";
					}
					if(file_exists($racine_www.$chemin_csv."/".$_GET['dossier']."/f_men.txt")){
						echo "<li>f_men.txt: ";
						if(unlink($racine_www.$chemin_csv."/".$_GET['dossier']."/f_men.txt")){
							echo "<font color='green'>SUCCES</font>";
						}
						else{
							echo "<font color='red'>ECHEC</font>";
						}
						echo "</li>\n";
					}
					if(file_exists($racine_www.$chemin_csv."/".$_GET['dossier']."/f_wind.txt")){
						echo "<li>f_wind.txt: ";
						if(unlink($racine_www.$chemin_csv."/".$_GET['dossier']."/f_wind.txt")){
							echo "<font color='green'>SUCCES</font>";
						}
						else{
							echo "<font color='red'>ECHEC</font>";
						}
						echo "</li>\n";
					}
					//if(disk_total_space($racine_www."/Admin/csv/".$_GET['dossier'])==0){
						echo "<li>Dossier ".$racine_www.$chemin_csv."/".$_GET['dossier'].": ";
						if(rmdir($racine_www.$chemin_csv."/".$_GET['dossier'])){
							echo "<font color='green'>SUCCES</font>";
						}
						else{
							echo "<font color='red'>ECHEC</font>";
						}
						echo "</li>\n";
					//}
					echo "</ul>\n";
					echo "<p>Termin�.</p>\n";
				}
				else{
					echo "<p style='color:red;'>Le dossier propos� n'a pas l'air d'�tre un dossier!?</p>\n";
				}
			}
			else{
				echo "<p style='color:red;'>Le dossier propos� n'existe pas.</p>\n";
			}
			echo "</blockquote>\n";

			include $pathlcsorse3."pdp.inc.php";
			exit();
		}

		if(!isset($_POST['is_posted'])){
			$deverrouiller=isset($_GET['deverrouiller']) ? $_GET['deverrouiller'] : 'n';

			// D�verrouillage si un import �tait annonc� d�j� en cours:
			if($deverrouiller=='y'){
				$sql="UPDATE params SET value='n' WHERE name='imprt_cmpts_en_cours'";
				$res0=mysql_query($sql);

				if($res0){
					echo "<p>D�verrouillage r�ussi!</p>\n";
				}
				else{
					echo "<p style='color:red;'>Echec du d�verrouillage!</p>\n";
				}
			}

			// Un import est-il d�j� en cours?
			$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)==0){
				$imprt_cmpts_en_cours="n";
			}
			else{
				$ligtmp=mysql_fetch_object($res1);
				$imprt_cmpts_en_cours=$ligtmp->value;
			}

			if($imprt_cmpts_en_cours=="y"){
				echo "<p><b>ATTENTION:</b> Il semble qu'un import soit d�j� en cours";

				$sql="SELECT value FROM params WHERE name='dernier_import'";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res1)>0){
					$ligtmp=mysql_fetch_object($res2);
					echo ":<br />\n<a href='$urlse3/Admin/result.".$ligtmp->value.".html' target='_blank'>$urlse3/Admin/result.".$ligtmp->value.".html</a>";
				}

				echo "<br />";
				echo "Si vous �tes certain que ce n'est pas le cas, vous pouvez faire sauter le verrou.<br />Sinon, il vaut mieux patienter quelques minutes.</p>\n";

				echo "<p><a href='".$_SERVER['PHP_SELF']."?deverrouiller=y'>Faire sauter le verrou</a>.</p>\n";
			}
			else{
				echo "<h3>Choix des fichiers source</h3>\n";

				// ===========================================================
				// AJOUTS: 20070914 boireaus

				exec("ldapsearch -xLLL ou=Trash",$retour_recherche_branche_Trash);
				if(count($retour_recherche_branche_Trash)>0){
					$attribut=array("uid");
					$test_tab=get_tab_attribut("trash", "uid=*", $attribut);
					if(count($test_tab)){
						echo "<p><span style='color:red; font-weight:bold;'>ATTENTION:</span> Il semble que la Corbeille contienne des comptes.<br />Conserver des comptes avant un import peut �tre g�nant:</p>\n";
						echo "<ul>\n";
						echo "<li>Cela peut causer une p�nurie d'uidNumber libres pour les nouveaux comptes � cr�er.</li>\n";
						echo "<li>Cela rallonge le temps de traitement.</li>\n";
						echo "</ul>\n";
						echo "<p>Il est donc recommand� de proc�der au Nettoyage des comptes (<i>dans le menu Annuaire</i>) avant d'effectuer l'import de nouveaux comptes.</p>\n";
					}
				}
				// ===========================================================


				echo "<h4>Fichier �l�ves</h4>\n";

				echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
				echo "<table border='0' width='100%'>\n";

				echo "<tr>\n";
				echo "<td width='45%'><input type='radio' id='type_csv' name='type_fichier_eleves' value='csv' onchange=\"document.getElementById('id_csv').style.display='';document.getElementById('id_xml').style.display='none';\" /> Export CSV de Sconet";
				echo "&nbsp;&nbsp;";
				echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('<b>Le cheminement pour r�aliser cette extraction depuis Sconet est:</b><br />Application Sconet/Acc�s Base Eleves.<br>Choisir l\'ann�e \(<i>en cours ou en pr�paration selon que la bascule est ou non effectu�e</i>\) Exploitation-Extraction et choisir personnalis�e.<br>Les champs requis sont:<ul><li>Nom</li><li>Pr�nom 1</li><li>Date de naissance</li><li>N� Interne</li><li>Sexe</li><li>Division</li>')")."\"><img name=\"action_image1\"  src=\"$helpinfo\"></u>\n";

				echo "</td>\n";
				echo "<td width='10%' align='center'>ou</td>\n";
				echo "<td width='45%'><input type='radio' id='type_xml' name='type_fichier_eleves' value='xml' checked onchange=\"document.getElementById('id_csv').style.display='none';document.getElementById('id_xml').style.display='';\" /> Export XML de Sconet";
				echo "&nbsp;&nbsp;";
				echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('<b>Le cheminement pour r�aliser cette extraction depuis Sconet est:</b><br>Application Sconet/Acc�s Base Eleves/Extractions/Exports standard/Exports XML g�n�riques/<b>El�ves sans adresses</b><br><br><b>Attention:</b> Ces exports XML ne sont actuellement possibles qu\'avant 9H le matin et apr�s 17H le soir.</p><p>Ce fichier permet une meilleure g�n�ration des groupes Cours pour les groupes correspondant � des options.')")."\"><img name=\"action_image2\"  src=\"$helpinfo\"></u>\n";
				echo "</td>\n";
				echo "</tr>\n";


				echo "<tr>\n";
				/*
				echo "<td>\n";
				echo "<p>Les champs requis sont:</p>\n";
				echo "<ul>\n";
				echo "<li>Nom</li>\n";
				echo "<li>Pr�nom 1</li>\n";
				echo "<li>Date de naissance</li>\n";
				echo "<li>N� Interne</li>\n";
				echo "<li>Sexe</li>\n";
				echo "<li>Division</li>\n";
				echo "</ul>\n";

				echo "<p>Veuillez fournir le fichier CSV:<br />\n";
				echo "<input type=\"file\" size=\"30\" name=\"eleves_csv_file\">\n";
				echo "</p>\n";

				echo "</td>\n";

				echo "<td>&nbsp;</td>\n";

				echo "<td>\n";
				*/
				echo "<td colspan='3' align='center'>\n";
				//echo "<p>Veuillez fournir le fichier XML: ElevesSansAdresses.xml<br />\n";
				echo "<p>Veuillez fournir le fichier �l�ves (<i>CSV ou XML selon le choix effectu� ci-dessus</i>).<br />\n";
				echo "<span id='id_csv' style='display:none; background-color: lightgreen; border: 1px solid black;'>CSV:</span> ";
				echo "<span id='id_xml' style='display:none; background-color: lightblue; border: 1px solid black;'>XML:</span> ";
				echo "<input type=\"file\" size=\"30\" name=\"eleves_file\" />\n";
				//echo "<input type=\"file\" size=\"30\" name=\"eleves_xml_file\">\n";
				//echo "<input type=\"file\" size=\"30\" name=\"nomenclature_xml_file\">\n";
				echo "</p>\n";
				echo "<script type='text/javascript'>
	if(document.getElementById('type_csv').checked){
		document.getElementById('id_csv').style.display=\"\";
		document.getElementById('id_xml').style.display=\"none\";
	}
	if(document.getElementById('type_xml').checked){
		document.getElementById('id_csv').style.display=\"none\";
		document.getElementById('id_xml').style.display=\"\";
	}
</script>";
				echo "</td>\n";

				echo "</tr>\n";
				echo "</table>\n";

				echo "<h4>Fichier professeurs et emploi du temps</h4>\n";

				echo "<p>Veuillez fournir le fichier XML <i>(STS_emp_RNE_ANNEE.xml)</i>:<br />\n";
				echo "<span style='background-color: lightblue; border: 1px solid black;'>XML:</span> ";
				echo "<input type=\"file\" size=\"80\" name=\"sts_xml_file\" />\n";
				echo "&nbsp;&nbsp;";
			        echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('<b>Le cheminement pour effectuer cet export depuis Sconet est:</b><br>STS-web/Mise � jour/Exports/Emplois du temps')")."\"><img name=\"action_image1\"  src=\"$helpinfo\"></u>\n";

				echo "</p>\n";
				echo "<br>";

				echo "<h3>Configuration de l'import</h3>";

				echo "<h4>Pr�fixe �ventuel : <input type='text' name='prefix' size='5' maxlength='5' value='' />\n";
				echo "&nbsp;&nbsp;";
                                echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('<b>Ex : LEP</b><br>Ce pr�fixe est utilis� dans les noms de groupes \(ex.: Classe_<b>LEP</b>_3D, Equipe_<b>LEP</b>_3D, Cours_<b>LEP</b>_AGL1_3D, Matiere_<b>LEP</b>_MATHS\)<br>Cela est utile dans les �tablissements mixtes, avec un lyc�e et un LP par exemple.')")."\"><img name=\"action_image3\"  src=\"$helpinfo\"></u>\n";
				echo "</h4>";
				echo "<h4>Importation de d�but d'ann�e ? <input name='annuelle' type='checkbox' value='y' />\n";
				echo "&nbsp;&nbsp;";
				echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('Vous devez cocher cette case pour la premi�re importation de l\'ann�e. Cela va d�truire les classes existantes.')")."\"><img name=\"action_image4\"  src=\"$helpinfo\"></u>\n";
				echo "</h4>";

				echo "<h4>Simulation ? <input name='simulation' type='checkbox' value='y' />\n";

				echo "&nbsp;&nbsp;";
				echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('Dans le mode simulation, les nouveaux comptes et comptes retrouv�s (cr��s auparavant � la main et pour lesquels l\'employeeNumber n\'est pas renseign�) sont affich�s sans �tre pour autant cr��s.')")."\"><img name=\"action_image5\"  src=\"$helpinfo\"></u>\n";
				echo "</h4>";


				echo "<h4>G�n�rer des fichiers CSV ? <input name='temoin_creation_fichiers' type='checkbox' value='oui' />\n";

				echo "&nbsp;&nbsp;";
				echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('L\'import fonctionne tr�s bien automatiquement (sans eux), mais si vous y tenez.')")."\"><img name=\"action_image5\"  src=\"$helpinfo\"></u>\n";
				echo "</h4>";

				echo "<h4>Afficher les dates et heures dans l'import? <input name='chrono' type='checkbox' value='y' />\n";

				echo "&nbsp;&nbsp;";
				echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('Pour estimer la dur�e de l\'importation.')")."\"><img name=\"action_image5\"  src=\"$helpinfo\"></u>\n";
				echo "</h4>";


				// ===========================================================
				// AJOUTS: 20070914 boireaus
				echo "<h4>Param�tres de l'import</h4>\n";
				echo "<ul>\n";
				echo "<li>\n";
				echo "Cr�er les Equipes sans les peupler ? <input name='creer_equipes_vides' type='checkbox' value='y' />\n";
				echo "&nbsp;&nbsp;";
				echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('En d�but d\'ann�e, le fichier XML STS_emp peut contenir des informations erron�es (<i>ne prenant pas encore en compte les nouvelles associations professeurs/mati�res/classes</i>).<br />La remont�e de l\'emploi du temps vers STS r�glera ce probl�me.<br />En attendant, pour cr�er les �quipes sans y mettre les professeur de l\'ann�e pr�c�dente, vous pouvez cocher cette case.<br />Les �quipes cr��es, vous pourrez y affecter manuellement les professeurs pour lesquels l\'acc�s aux dossiers de Classes est urgent.')")."\"><img name=\"action_image5\"  src=\"$helpinfo\"></u>\n";
				echo "</li>\n";

				echo "<li>\n";
				echo "Ne pas cr�er les groupes Cours ? <input name='creer_cours' type='checkbox' value='n' />\n";
				echo "&nbsp;&nbsp;";
				echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('En d�but d\'ann�e, le fichier XML STS_emp peut contenir des informations erron�es (<i>ne prenant pas encore en compte les nouvelles associations professeurs/mati�res/classes</i>).<br />La remont�e de l\'emploi du temps vers STS r�glera ce probl�me.<br />En attendant, il vaut mieux ne pas cr�er des Cours erron�s.')")."\"><img name=\"action_image5\"  src=\"$helpinfo\"></u>\n";
				echo "</li>\n";

				echo "<li>\n";
				echo "Ne pas cr�er les groupes Mati�res ? <input name='creer_matieres' type='checkbox' value='n' />\n";
				echo "&nbsp;&nbsp;";
				echo "<u onmouseover=\"this.T_SHADOWWIDTH=5;this.T_STICKY=1;return escape".gettext("('En d�but d\'ann�e, le fichier XML STS_emp peut contenir des informations erron�es (<i>ne prenant pas encore en compte les nouvelles associations professeurs/mati�res/classes</i>).<br />La remont�e de l\'emploi du temps vers STS r�glera ce probl�me.<br />Vous pouvez ne pas cr�er les groupes Mati�res en attendant.<br />Cependant, les Mati�res changent assez peu d\'une ann�e sur l\'autre  et les professeurs changent assez peu de mati�re d\'une ann�e sur l\'autre.')")."\"><img name=\"action_image5\"  src=\"$helpinfo\"></u>\n";
				echo "</li>\n";

				echo "</ul>\n";
				// ===========================================================


				echo "<input type='hidden' name='is_posted' value='yes'>\n";
				echo "<p><input type='submit' value='Valider'></p>\n";
				echo "</form>\n";
			}
			include $pathlcsorse3."pdp.inc.php";
		}
		else{


			// Un import est-il d�j� en cours?
			$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)==0){
				$imprt_cmpts_en_cours="n";
			}
			else{
				$ligtmp=mysql_fetch_object($res1);
				$imprt_cmpts_en_cours=$ligtmp->value;
			}

			if($imprt_cmpts_en_cours=="y"){
				echo "<p><b>ATTENTION:</b> Il semble qu'un import soit d�j� en cours";

				$sql="SELECT value FROM params WHERE name='dernier_import'";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res1)>0){
					$ligtmp=mysql_fetch_object($res2);
					echo ":<br />\n<a href='$urlse3/Admin/result.".$ligtmp->value.".html' target='_blank'>$urlse3/Admin/result.".$ligtmp->value.".html</a>";
				}

				echo "<br />";
				echo "Si vous �tes certain que ce n'est pas le cas, vous pouvez faire sauter le verrou.<br />Sinon, il vaut mieux patienter quelques minutes.</p>\n";

				echo "<p><a href='".$_SERVER['PHP_SELF']."?deverrouiller=y'>Faire sauter le verrou</a>.</p>\n";
				include $pathlcsorse3."pdp.inc.php";
				exit();
			}


			$nouveaux_comptes=0;
			$comptes_avec_employeeNumber_mis_a_jour=0;
			$nb_echecs=0;

			$tab_nouveaux_comptes=array();
			$tab_comptes_avec_employeeNumber_mis_a_jour=array();

			// Cr�ation d'un t�moin de mise � jour en cours.
			$sql="SELECT value FROM params WHERE name='imprt_cmpts_en_cours'";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)==0){
				$sql="INSERT INTO params SET name='imprt_cmpts_en_cours',value='y'";
				$res0=mysql_query($sql);
			}
			else{
				// Si la valeur est d�j� � y, c'est qu'on a fait F5... un import est d�j� en cours.
				$ligtmp=mysql_fetch_object($res1);
				if($ligtmp->value=="y"){
					echo("<p>Un import est d�j� en cours");

					$sql="SELECT value FROM params WHERE name='dernier_import'";
					$res2=mysql_query($sql);
					if(mysql_num_rows($res1)>0){
						$ligtmp=mysql_fetch_object($res2);
						echo ": <a href='$urlse3/Admin/result.".$ligtmp->value.".html' target='_blank'>$urlse3/Admin/result.".$ligtmp->value.".html</a>";
					}
					else{
						echo ".";
					}

					echo("<br />Veuillez patienter.</p>\n");
					echo("<p><a href='".$_SERVER['PHP_SELF']."'>Retour</a>.</p>\n");
					echo("</body>\n</html>\n");
					exit();
				}

				$sql="UPDATE params SET value='y' WHERE name='imprt_cmpts_en_cours'";
				$res0=mysql_query($sql);
			}

			$timestamp=ereg_replace(" ","_",microtime());

			$sql="SELECT value FROM params WHERE name='dernier_import'";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)==0){
				$sql="INSERT INTO params SET name='dernier_import',value='$timestamp'";
				$res0=mysql_query($sql);
			}
			else{
				$sql="UPDATE params SET value='$timestamp' WHERE name='dernier_import'";
				$res0=mysql_query($sql);
			}




			if(($_FILES["eleves_file"]["name"]=="")&&($_FILES["sts_xml_file"]["name"]=="")){
				echo "<p style='color:red;'><b>ERREUR:</b> Aucun fichier n'a �t� fourni!</p>\n";
				echo "<p><a href='".$_SERVER['PHP_SELF']."'>Retour</a>.</p>\n";
				echo "</body>\n</html>\n";
				exit();
			}








			$type_fichier_eleves=$_POST['type_fichier_eleves'];

			$tmp_eleves_file=$HTTP_POST_FILES['eleves_file']['tmp_name'];
			$eleves_file=$HTTP_POST_FILES['eleves_file']['name'];
			$size_eleves_file=$HTTP_POST_FILES['eleves_file']['size'];

			$dest_file="$dossier_tmp_import_comptes/fichier_eleves";
			// SUR CA, IL VAUDRAIT SANS DOUTE MIEUX FORCER LE NOM DESTINATION POUR EVITER DES SALES BLAGUES
			if(file_exists($dest_file)){
				unlink($dest_file);
			}

			if(is_uploaded_file($tmp_eleves_file)){
				$source_file=stripslashes("$tmp_eleves_file");
				$res_copy=copy("$source_file" , "$dest_file");
			}


			$tmp_sts_file=$HTTP_POST_FILES['sts_xml_file']['tmp_name'];
			$sts_file=$HTTP_POST_FILES['sts_xml_file']['name'];
			$size_sts_file=$HTTP_POST_FILES['sts_xml_file']['size'];

			$dest_file="$dossier_tmp_import_comptes/fichier_sts";
			// SUR CA, IL VAUDRAIT SANS DOUTE MIEUX FORCER LE NOM DESTINATION POUR EVITER DES SALES BLAGUES
			if(file_exists($dest_file)){
				unlink($dest_file);
			}

			if(is_uploaded_file($tmp_sts_file)){
				$source_file=stripslashes("$tmp_sts_file");
				$res_copy=copy("$source_file" , "$dest_file");
			}






			//$timestamp=ereg_replace(" ","_",microtime());
			$echo_file="$racine_www/Admin/result.$timestamp.html";
			$dest_mode="file";
			$fich=fopen("$echo_file","w+");
			fwrite($fich,"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<style type='text/css'>
body{
    background: url($background) ghostwhite bottom right no-repeat fixed;
}
</style>
<!--head-->
<title>Import de comptes</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
<!--meta http-equiv='Refresh' CONTENT='120;URL=result.$timestamp.html#menu' /-->
<link type='text/css' rel='stylesheet' href='$stylecss' />
<body>
<h1 style='text-align:center;'>Import de comptes</h1>

<div id='decompte' style='float: right; border: 1px solid black;'></div>

<script type='text/javascript'>
cpt=120;
compte_a_rebours='y';

function decompte(cpt){
	if(compte_a_rebours=='y'){
		document.getElementById('decompte').innerHTML=cpt;
		if(cpt>0){
			cpt--;
		}
		else{
			document.location='result.$timestamp.html';
		}

		setTimeout(\"decompte(\"+cpt+\")\",1000);
	}
	else{
		document.getElementById('decompte').style.display='none';
	}
}

decompte(cpt);
</script>\n");
			fclose($fich);





			$chrono=isset($_POST['chrono']) ? $_POST['chrono'] : "n";



			// ===========================================================
			// AJOUTS: 20070914 boireaus
			$creer_equipes_vides=isset($_POST['creer_equipes_vides']) ? $_POST['creer_equipes_vides'] : 'n';
			$creer_cours=isset($_POST['creer_cours']) ? $_POST['creer_cours'] : 'y';
			$creer_matieres=isset($_POST['creer_matieres']) ? $_POST['creer_matieres'] : 'y';
			// ===========================================================


			// Dossier pour les CSV

			//$temoin_creation_fichiers="non";
			//$temoin_creation_fichiers="oui";
			$temoin_creation_fichiers=isset($_POST['temoin_creation_fichiers']) ? $_POST['temoin_creation_fichiers'] : "non";
			if($temoin_creation_fichiers!="non"){
				if(!file_exists($racine_www.$chemin_csv)){
					mkdir($racine_www.$chemin_csv);
				}
				mt_srand((float) microtime()*1000000);
				$randval = mt_rand();
				$chemin_http_csv=$chemin_csv."/".$timestamp."_".$randval;
				$dossiercsv=$racine_www."/".$chemin_http_csv;
				if(!mkdir($dossiercsv)){$temoin_creation_fichiers="non";}
			}

			//my_echo("disk_total_space($dossiercsv)=".disk_total_space($dossiercsv)."<br />");


			// Date et heure...
			$aujourdhui = getdate();
			$annee_aujourdhui = $aujourdhui['year'];
			$mois_aujourdhui = sprintf("%02d",$aujourdhui['mon']);
			$jour_aujourdhui = sprintf("%02d",$aujourdhui['mday']);
			$heure_aujourdhui = sprintf("%02d",$aujourdhui['hours']);
			$minute_aujourdhui = sprintf("%02d",$aujourdhui['minutes']);
			$seconde_aujourdhui = sprintf("%02d",$aujourdhui['seconds']);

			my_echo("<p>Import du $jour_aujourdhui/$mois_aujourdhui/$annee_aujourdhui � $heure_aujourdhui:$minute_aujourdhui:$seconde_aujourdhui<br />\n(<i>l'op�ration d�marre 2min apr�s; vous pouvez alors commencer � jouer avec la touche F5 pour suivre le traitement</i>)</p>\n");


			// Importation annuelle
			$annuelle=isset($_POST['annuelle']) ? $_POST['annuelle'] : "n";

			// Mode simulation
			$simulation=isset($_POST['simulation']) ? $_POST['simulation'] : "n";

			// Pr�fixe LP/LEGT,...
			$prefix=isset($_POST['prefix']) ? $_POST['prefix'] : "";
			#$prefix=strtoupper(ereg_replace("[^A-Za-z0-9_]", "", strtr(remplace_accents($prefix)," ","_")));
                        $prefix=strtoupper(ereg_replace("[^A-Za-z0-9]", "", remplace_accents($prefix)));
			if(strlen(ereg_replace("_","",$prefix))==0) $prefix="";
			if (strlen($prefix)>0) $prefix=$prefix."_";

/*
			echo "\$resultat=exec(\"/usr/bin/sudo $php $chemin/import_comptes.php '$type_fichier_eleves' '$chemin_fich/fichier_eleves' '$chemin_fich/fichier_sts' '$prefix' '$annuelle' '$simulation' '$timestamp'\",$retour);";

			$resultat=exec("/usr/bin/sudo $php $chemin/import_comptes.php '$type_fichier_eleves' '$chemin/fichier_eleves' '$chemin_fich/fichier_sts' '$prefix' '$annuelle' '$simulation' '$timestamp'",$retour);

*/

/*
			echo "\$resultat=exec(\"/usr/bin/sudo $chemin/import_comptes.php '$type_fichier_eleves' '$chemin_fich/fichier_eleves' '$chemin_fich/fichier_sts' '$prefix' '$annuelle' '$simulation' '$timestamp'\",$retour);";

			$resultat=exec("/usr/bin/sudo $chemin/import_comptes.php '$type_fichier_eleves' '$chemin/fichier_eleves' '$chemin_fich/fichier_sts' '$prefix' '$annuelle' '$simulation' '$timestamp'",$retour);
*/

			$fich=fopen("$dossier_tmp_import_comptes/import_comptes.sh","w+");
			//fwrite($fich,"#!/bin/bash\n/usr/bin/sudo $chemin/import_comptes.php '$type_fichier_eleves' '$chemin_fich/fichier_eleves' '$chemin_fich/fichier_sts' '$prefix' '$annuelle' '$simulation' '$timestamp' '$randval' '$temoin_creation_fichiers'\n");

			// ===========================================================
			// AJOUTS: 20070914 boireaus
			//fwrite($fich,"#!/bin/bash\n/usr/bin/php $chemin/import_comptes.php '$type_fichier_eleves' '$chemin_fich/fichier_eleves' '$chemin_fich/fichier_sts' '$prefix' '$annuelle' '$simulation' '$timestamp' '$randval' '$temoin_creation_fichiers' '$chrono'\n");

			fwrite($fich,"#!/bin/bash\n/usr/bin/php $chemin/import_comptes.php '$type_fichier_eleves' '$chemin_fich/fichier_eleves' '$chemin_fich/fichier_sts' '$prefix' '$annuelle' '$simulation' '$timestamp' '$randval' '$temoin_creation_fichiers' '$chrono' '$creer_equipes_vides' '$creer_cours' '$creer_matieres'\n");
			// ===========================================================



			fclose($fich);
			//chmod("/var/remote_adm/import_comptes.sh",750);
			chmod("$dossier_tmp_import_comptes/import_comptes.sh",0750);

			$d_minute_aujourdhui=sprintf("%02d",$minute_aujourdhui+2);

			//echo "\$resultat=exec(\"/usr/bin/at -f /var/remote_adm/import_comptes.sh $heure_aujourdhui:$d_minute_aujourdhui\",$retour);";
			//$resultat=exec("/usr/bin/at -f $dossier_tmp_import_comptes/import_comptes.sh $heure_aujourdhui:$d_minute_aujourdhui",$retour);
                        // sudo
                        //echo "DBG >>/usr/bin/sudo $chemin/run_import_comptes.sh $dossier_tmp_import_comptes<br />";
                        $resultat=exec("/usr/bin/sudo $chemin/run_import_comptes.sh $dossier_tmp_import_comptes", $retour);

			if(count($retour)>0){
				echo "<p>Il semble que la programmation ait �chou�...";
				for($i=0;$i<count($retour);$i++){
					echo "\$retour[$i]=$retour[$i]<br />\n";
				}
				echo "</p>\n";
			}




			echo "<p>Lancement de l'import de comptes,... en mode <b>";
			if($simulation=="y"){echo "simulation";}else{echo "cr�ation";}
			echo "</b>";
			if($prefix!=""){echo " avec le pr�fixe <b>$prefix</b>";}
			echo ".</p>\n";

			echo "<p>Patientez un peu, puis suivez ce lien: <a href='../Admin/result.$timestamp.html' target='_blank'>R�sultat</a></p>\n";

			echo("<p><i>NOTES:</i></p>\n");
			echo("<ul>\n");
			//echo("<li>Pour le moment la variable \$prefix='$prefix' n'est pas g�r�e... A FAIRE</li>\n");
			echo("<li><p>Les changements de classe, suppressions de membres de groupes, en dehors de l'import annuel, ne sont pas g�r�s.<br />Dans le cas de changement de classe d'un �l�ve, il risque d'apparaitre membre de plusieurs classes...<br />Il faut faire le m�nage � la main.<br />On pourrait par contre ajouter un test pour lister les comptes membres de plusieurs classes<br />... contr�ler aussi qu'aucun compte n'est � la fois dans plusieurs parmi les groupes Profs, Eleves, Administratifs.</p></li>\n");
			echo("<li><p>Le mode simulation ne simule que la cr�ation/r�cup�ration d'utilisateurs.<br />Cela permet d�j� de rep�rer si les cr�ations annonc�es sont conformes � ce que l'on attendait.<br />Les uid g�n�r�s/simul�s peuvent par contre �tre erron�s si jamais deux nouveaux utilisateurs correspondent � das uid en doublon, il se peut qu'ils obtiennent en simulation le m�me uid.<br /><i>Exemple:</i> Deux nouveaux arrivants Alex T�rieur et Alain T�rieur donneront tous deux l'uid 'terieura' en simulation alors qu'en mode cr�ation, le premier obtiendrait l'uid 'terieura' et le deuxi�me 'terieur2'.<br />Et si l'annuaire contenait d�j� un compte 'terieura' (<i>pour Anabelle Terieur</i>), les deux nouveaux comptes paraitraient recevoir en mode simultation l'uid 'terieur2' alors qu'en mode cr�ation, le premier obtiendrait l'uid 'terieur2' et le deuxi�me 'terieur3'.</p><p><i>Remarque:</i> Le mode simulation permet tout de m�me la g�n�ration de fichiers f_ele.txt, f_div.txt, f_men.txt et f_wind.txt</p></li>\n");
			echo("</ul>\n");

			include $pathlcsorse3."pdp.inc.php";
			flush();






/*
			//echo "On va lancer le script PHP.";

			$type_fichier_eleves=$_POST['type_fichier_eleves'];

			$tmp_eleves_file=$HTTP_POST_FILES['eleves_file']['tmp_name'];
			$eleves_file=$HTTP_POST_FILES['eleves_file']['name'];
			$size_eleves_file=$HTTP_POST_FILES['eleves_file']['size'];

			if(is_uploaded_file($tmp_eleves_file)){
				$dest_file="tmp/$eleves_file";
				// SUR CA, IL VAUDRAIT SANS DOUTE MIEUX FORCER LE NOM DESTINATION POUR EVITER DES SALES BLAGUES

				$source_file=stripslashes("$tmp_eleves_file");
				$res_copy=copy("$source_file" , "$dest_file");

				$php="/usr/bin/php";
				$chemin="/home/www/html/steph/test_php-cli";
				$resultat=exec("$php $chemin/traitement.php $type_fichier_eleves $chemin/$dest_file",$retour);
				for($i=0;$i<count($retour);$i++){
					echo "\$retour[$i]=$retour[$i]<br />";
				}

	//mer f�v 28 12:53:29 steph@fuji:~/2007_02_21/se3
	//$ cat /tmp/rapport_test.txt
	//$type_fichier_eleves=csv
	//$eleves_file=/home/www/html/steph/test_php-cli/tmp/exportCSVExtraction_20061018.csv
	//mer f�v 28 12:53:33 steph@fuji:~/2007_02_21/se3
	//$


	//On va lancer le script PHP.$retour[0]=



			}
*/

		}

		// Dans la version PHP4-CLI, envoyer le rapport par mail.
		// Envoyer le contenu de la page aussi?

		// Peut-�tre forcer une sauvegarde de l'annuaire avant de proc�der � une op�ration qui n'est pas une simulation.
		// O� placer le fichier de sauvegarde?
		// Probl�me de l'encombrement � terme.
	}
	//include $pathlcsorse3."pdp.inc.php";
?>
