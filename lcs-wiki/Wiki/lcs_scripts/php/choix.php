<?php

/*
---------------------------------------------------------
Ajouté par le lycée laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/


//ouvre la connexion vers la base de données
include("connexion_mysql.php");

//pour pouvoir utiliser la fonction LCS people_get_variables pour récupérer le nom et le prenom des utilisateurs
include "/var/www/lcs/includes/headerauth.inc.php";
include "/var/www/Annu/includes/ldap.inc.php";


if(isset($_POST['idchoix'])){

	$idchoix = $_POST['idchoix'];

	switch ($idchoix) {
				
		case "1" : //affiche la liste des classes et affiche, après avoir sélectionné une classe, la liste des élèves de cette classe 
				$res = mysql_query("SELECT distinct grname FROM ".$prefix."groups WHERE grname LIKE 'Classe%' ORDER BY grname",$link);
		
				if ( mysql_num_rows($res) != 0 ) {

					echo "<br /> Veuillez sélectionner une classe : ";
					echo "<select id=\"categorie\" name=\"categorie\" onchange=\"idchoix=document.acls.idchoice.value; varscript='sousChoix.php'; go(varscript,idchoix);\">";
					echo "<option value=\"0\">Sélectionnez une classe ... </option>";

					while ($row = mysql_fetch_assoc($res)){
						echo "<option value='".$row["grname"]."'>".substr($row['grname'],7,strlen($row['grname']) - 7)."</option>";
					}

					echo "</select>";
					//champ caché contenant le numéro de choix de la première liste
					//permet de pouvoir le récupérer dans les scripts suivants
					echo "<input type=\"hidden\" name=\"idchoice\" value=\"".$idchoix."\">";
				}
				else {
					echo "<br /><b> Les groupes \"Classes\" n'ont pas encore été créés.</b>";
				}
				
				 break;
				
		case (($idchoix > 1)&&($idchoix < 4)) : //affiche uniquement la liste des classes
	
				if($idchoix == "2"){//Equipe
					$res = mysql_query("SELECT distinct grname FROM ".$prefix."groups WHERE grname LIKE 'Equipe%' ORDER BY grname",$link);
				}
				else{//Classe
					$res = mysql_query("SELECT distinct grname FROM ".$prefix."groups WHERE grname LIKE 'Classe%' ORDER BY grname",$link);
				}
				
				if ( mysql_num_rows($res) != 0 ) {
					
					echo "<br /> Veuillez sélectionner une classe : ";
					echo "<select multiple id=\"categorie\" name=\"categorie\" onchange=\";\">";
					echo "<option value=\"0\">Sélectionnez une classe ... </option>";					
					
					while ($row = mysql_fetch_assoc($res)){
						echo "<option value='".$row["grname"]."'>".substr($row['grname'],7,strlen($row['grname']) - 7)."</option>";
					}

					echo "</select>";
                			echo "<br /><br />";
		                	echo "<b><u>Quel(s) droit(s) souhaitez vous modifier</u> ? (cochez la ou les cases correspondantes)</b>";
				        echo "<br />";
			                echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
					echo "<tr>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"lect\" value=\"aut\" >autoriser la lecture <br/>";
					echo "<input type=\"radio\" name=\"lect\" value=\"ref\" >refuser la lecture <br/>";
					echo "</td>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"ecr\" value=\"aut\" >autoriser l'écriture <br/>";
					echo "<input type=\"radio\" name=\"ecr\" value=\"ref\" >refuser l'écriture <br/>";
					echo "</td>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"com\" value=\"aut\" >autoriser l'ajout de commentaire <br/>";
					echo "<input type=\"radio\" name=\"com\" value=\"ref\" >refuser l'ajout de commentaire <br/>";
					echo "</td>";
					echo "</tr>";
					echo "</table>";

					//champ caché pour pouvoir récupérer idchoice ( envoyé grâce à la fonction javascript go() )dans le script javascript
					echo "<input type=\"hidden\" name=\"idchoi\" value=\"".$idchoix."\">";
					echo "<br />";
					echo "<input type=\"button\" style=\"color:#FF0000;\" value=\"Valider les droits sélectionnés\" style=\"width: 300px\" onClick=\" selectione=''; for(i=0; i<document.acls.categorie.options.length; i++) { if(document.acls.categorie.options[i].selected) { selectione+=document.acls.categorie.options[i].value+'|'; } } if(document.acls.lect[0].checked){ lecture=document.acls.lect[0].value; }else if(document.acls.lect[1].checked){ lecture=document.acls.lect[1].value; }else{ lecture='0'; } if(document.acls.ecr[0].checked){ ecriture=document.acls.ecr[0].value; }else if(document.acls.ecr[1].checked){ ecriture=document.acls.ecr[1].value; }else{ ecriture='0'; } if(document.acls.com[0].checked){ commentaire=document.acls.com[0].value; }else if(document.acls.com[1].checked){ commentaire=document.acls.com[1].value; }else{ commentaire='0'; }  modifdroits(selectione,document.acls.read_acl.value,document.acls.write_acl.value,document.acls.comment_acl.value,lecture,ecriture,commentaire,document.acls.idchoi.value); \" >";
					
				}
				else {
					echo "<br /><b> Les groupes \"Classes\" n'ont pas encore été créés.</b>";
				}

				break;
		
		case "4" : //affiche la liste des professeurs
				
                                $res = mysql_query("SELECT grmember FROM ".$prefix."groups WHERE grname=\"Profs\" ORDER BY grmember",$link);
                                
				if ( mysql_num_rows($res) != 0 ) {
					
					echo "<br /> Veuillez sélectionner le ou les professeurs pour lesquels vous souhaitez configurer des droits : ";
					echo "<select multiple id='categorie' name='categorie' >";
					while ($row = mysql_fetch_assoc($res)){
						list($user, $groups) = people_get_variables($row["grmember"], false);
						echo "<option value='".$row["grmember"]."'>".$user["fullname"]." (".$row["grmember"].") </option>";
					}
 				
					echo "</select>";
                			echo "<br /><br />";
		                	echo "<b><u>Quel(s) droit(s) souhaitez vous modifier</u> ? (cochez la ou les cases correspondantes)</b>";
				        echo "<br />";
					echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
					echo "<tr>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"lect\" value=\"aut\" >autoriser la lecture <br/>";
					echo "<input type=\"radio\" name=\"lect\" value=\"ref\" >refuser la lecture <br/>";
					echo "</td>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"ecr\" value=\"aut\" >autoriser l'écriture <br/>";
					echo "<input type=\"radio\" name=\"ecr\" value=\"ref\" >refuser l'écriture <br/>";
					echo "</td>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"com\" value=\"aut\" >autoriser l'ajout de commentaire <br/>";
					echo "<input type=\"radio\" name=\"com\" value=\"ref\" >refuser l'ajout de commentaire <br/>";
					echo "</td>";
					echo "</tr>";
					echo "</table>";
                                        //champ caché pour pouvoir récupérer idchoice ( envoyé grâce à la fonction javascript go() )dans le script javascript
					echo "<input type=\"hidden\" name=\"idchoi\" value=\"".$idchoix."\">";
					echo "<br />";
					echo "<input type=\"button\" style=\"color:#FF0000;\" value=\"Valider les droits sélectionnés\" style=\"width: 300px\" onClick=\" selectione=''; for(i=0; i<document.acls.categorie.options.length; i++) { if(document.acls.categorie.options[i].selected) { selectione+=document.acls.categorie.options[i].value+'|'; } } if(document.acls.lect[0].checked){ lecture=document.acls.lect[0].value; }else if(document.acls.lect[1].checked){ lecture=document.acls.lect[1].value; }else{ lecture='0'; } if(document.acls.ecr[0].checked){ ecriture=document.acls.ecr[0].value; }else if(document.acls.ecr[1].checked){ ecriture=document.acls.ecr[1].value; }else{ ecriture='0'; } if(document.acls.com[0].checked){ commentaire=document.acls.com[0].value; }else if(document.acls.com[1].checked){ commentaire=document.acls.com[1].value; }else{ commentaire='0'; }  modifdroits(selectione,document.acls.read_acl.value,document.acls.write_acl.value,document.acls.comment_acl.value,lecture,ecriture,commentaire,document.acls.idchoi.value); \" >";
					
				}
				else {
					echo "<br /><b> Il n'y a pas encore de professeurs enregistrés dans le wiki.</b>";
				}
				
				break;

		case "5" : //liste des administratifs
                                
				$res = mysql_query("SELECT grmember FROM ".$prefix."groups WHERE grname=\"Administratifs\" ORDER BY grmember",$link);
				
				if ( mysql_num_rows($res) != 0 ) {

					echo "<br /> Veuillez sélectionner le ou les administratifs pour lesquels vous souhaitez configurer des droits : ";
					echo "<select multiple id='categorie' name='categorie'>";
				
					while ($row = mysql_fetch_assoc($res)){
						list($user, $groups) = people_get_variables($row["grmember"], false);
						echo "<option value='".$row["grmember"]."'>".$user["fullname"]." (".$row["grmember"].") </option>";
					}
				
					echo "</select>";
                                        echo "<br /><br />";
					echo "<b><u>Quel(s) droit(s) souhaitez vous modifier</u> ? (cochez la ou les cases correspondantes)</b>";
					echo "<br />";
					echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
					echo "<tr>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"lect\" value=\"aut\" >autoriser la lecture <br/>";
					echo "<input type=\"radio\" name=\"lect\" value=\"ref\" >refuser la lecture <br/>";
					echo "</td>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"ecr\" value=\"aut\" >autoriser l'écriture <br/>";
					echo "<input type=\"radio\" name=\"ecr\" value=\"ref\" >refuser l'écriture <br/>";
					echo "</td>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"com\" value=\"aut\" >autoriser l'ajout de commentaire <br/>";
					echo "<input type=\"radio\" name=\"com\" value=\"ref\" >refuser l'ajout de commentaire <br/>";
					echo "</td>";
					echo "</tr>";
					echo "</table>";
					
					//champ caché pour pouvoir récupérer idchoice ( envoyé grâce à la fonction javascript go() )dans le script javascript
					echo "<input type=\"hidden\" name=\"idchoi\" value=\"".$idchoix."\">";
					echo "<br />";
					echo "<input type=\"button\" style=\"color:#FF0000;\" value=\"Valider les droits sélectionnés\" style=\"width: 300px\" onClick=\" selectione=''; for(i=0; i<document.acls.categorie.options.length; i++) { if(document.acls.categorie.options[i].selected) { selectione+=document.acls.categorie.options[i].value+'|'; } } if(document.acls.lect[0].checked){ lecture=document.acls.lect[0].value; }else if(document.acls.lect[1].checked){ lecture=document.acls.lect[1].value; }else{ lecture='0'; } if(document.acls.ecr[0].checked){ ecriture=document.acls.ecr[0].value; }else if(document.acls.ecr[1].checked){ ecriture=document.acls.ecr[1].value; }else{ ecriture='0'; } if(document.acls.com[0].checked){ commentaire=document.acls.com[0].value; }else if(document.acls.com[1].checked){ commentaire=document.acls.com[1].value; }else{ commentaire='0'; }  modifdroits(selectione,document.acls.read_acl.value,document.acls.write_acl.value,document.acls.comment_acl.value,lecture,ecriture,commentaire,document.acls.idchoi.value); \" >";
				}
				else {
					echo "<br /><b> Il n'y a pas encore d'administratifs enregistrés dans le wiki.</b>";
				}
				
				break;
				
		case "6" : //liste des groupes wiki
				$res = mysql_query("SELECT distinct grname FROM ".$prefix."groups WHERE grname not like 'Classe%' and grname not like 'Equipe%' and grname not like 'Prof%' and grname not like 'Eleve%' and grname not like 'admin%' and grname not like 'Administratif%' ORDER BY grmember",$link);
				
				if ( mysql_num_rows($res) != 0 ) {	
					echo "<br /> Veuillez sélectionner le ou les groupes pour lesquels vous souhaitez configurer des droits : ";
					echo "<select multiple id='categorie' name='categorie'>";

					while ($row = mysql_fetch_assoc($res)){
						echo "<option value='".$row["grname"]."'>".$row["grname"]."</option>";
					}

					echo "</select>";
					echo "<br /><br />";
                                        echo "<b><u>Quel(s) droit(s) souhaitez vous modifier</u> ? (cochez la ou les cases correspondantes)</b>";
					echo "<br />";
					echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
					echo "<tr>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"lect\" value=\"aut\" >autoriser la lecture <br/>";
					echo "<input type=\"radio\" name=\"lect\" value=\"ref\" >refuser la lecture <br/>";
					echo "</td>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"ecr\" value=\"aut\" >autoriser l'écriture <br/>";
					echo "<input type=\"radio\" name=\"ecr\" value=\"ref\" >refuser l'écriture <br/>";
					echo "</td>";
					echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
					echo "<input type=\"radio\" name=\"com\" value=\"aut\" >autoriser l'ajout de commentaire <br/>";
					echo "<input type=\"radio\" name=\"com\" value=\"ref\" >refuser l'ajout de commentaire <br/>";
					echo "</td>";
					echo "</tr>";
					echo "</table>";
					//champ caché pour pouvoir récupérer idchoice ( envoyé grâce à la fonction javascript go() )dans le script javascript
					echo "<input type=\"hidden\" name=\"idchoi\" value=\"".$idchoix."\">";
					echo "<br />";
					echo "<input type=\"button\" style=\"color:#FF0000;\" value=\"Valider les droits sélectionnés\" style=\"width: 300px\" onClick=\" selectione=''; for(i=0; i<document.acls.categorie.options.length; i++) { if(document.acls.categorie.options[i].selected) { selectione+=document.acls.categorie.options[i].value+'|'; } } if(document.acls.lect[0].checked){ lecture=document.acls.lect[0].value; }else if(document.acls.lect[1].checked){ lecture=document.acls.lect[1].value; }else{ lecture='0'; } if(document.acls.ecr[0].checked){ ecriture=document.acls.ecr[0].value; }else if(document.acls.ecr[1].checked){ ecriture=document.acls.ecr[1].value; }else{ ecriture='0'; } if(document.acls.com[0].checked){ commentaire=document.acls.com[0].value; }else if(document.acls.com[1].checked){ commentaire=document.acls.com[1].value; }else{ commentaire='0'; }  modifdroits(selectione,document.acls.read_acl.value,document.acls.write_acl.value,document.acls.comment_acl.value,lecture,ecriture,commentaire,document.acls.idchoi.value); \" >";
				}
				else {
					echo "<br /><b> Aucun groupe n'a été créé.</b>";
				}

				break;

		case ($idchoix > 6) : //pas de liste déroulante
                                echo "<br /><br />";
				echo "<b><u>Quel(s) droit(s) souhaitez vous modifier</u> ? (cochez la ou les cases correspondantes)</b>";
				echo "<br />";
				echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
				echo "<tr>";
				echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
				echo "<input type=\"radio\" name=\"lect\" value=\"aut\" >autoriser la lecture <br/>";
				echo "<input type=\"radio\" name=\"lect\" value=\"ref\" >refuser la lecture <br/>";
				echo "</td>";
				echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
				echo "<input type=\"radio\" name=\"ecr\" value=\"aut\" >autoriser l'écriture <br/>";
				echo "<input type=\"radio\" name=\"ecr\" value=\"ref\" >refuser l'écriture <br/>";
				echo "</td>";
				echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
				echo "<input type=\"radio\" name=\"com\" value=\"aut\" >autoriser l'ajout de commentaire <br/>";
				echo "<input type=\"radio\" name=\"com\" value=\"ref\" >refuser l'ajout de commentaire <br/>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				//champ caché pour pouvoir récupérer idchoice ( envoyé grâce à la fonction javascript go() )dans le script javascript
				echo "<input type=\"hidden\" name=\"idchoi\" value=\"".$idchoix."\">";
				echo "<br />";
				echo "<input type=\"button\" style=\"color:#FF0000;\" value=\"Valider les droits sélectionnés\" style=\"width: 300px\" onClick=\" selectione=''; if(document.acls.lect[0].checked){ lecture=document.acls.lect[0].value; }else if(document.acls.lect[1].checked){ lecture=document.acls.lect[1].value; }else{ lecture='0'; } if(document.acls.ecr[0].checked){ ecriture=document.acls.ecr[0].value; }else if(document.acls.ecr[1].checked){ ecriture=document.acls.ecr[1].value; }else{ ecriture='0'; } if(document.acls.com[0].checked){ commentaire=document.acls.com[0].value; }else if(document.acls.com[1].checked){ commentaire=document.acls.com[1].value; }else{ commentaire='0'; }  modifdroits(selectione,document.acls.read_acl.value,document.acls.write_acl.value,document.acls.comment_acl.value,lecture,ecriture,commentaire,document.acls.idchoi.value); \" >";
				break;
		
	}
}

?>
