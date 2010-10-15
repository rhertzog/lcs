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


if(isset($_POST['idType'])){

	$idtype = $_POST['idType'];

	switch ($idtype) {
		case "1" : //l'utilisateur souhaite ajouter un ou plusieurs élèves. Donc on lui propose de choisir une classe;
				$res = mysql_query("SELECT distinct grname FROM ".$prefix."groups WHERE grname LIKE 'Classe%' ORDER BY grname",$link);	
				
				if ( mysql_num_rows($res) != 0 ) {
					
					echo "<br /> Veuillez sélectionner la classe dans laquelle se trouve l'élève à ajouter au groupe : ";
					echo "<select id=\"categorie\" name=\"categorie\" onchange=\"idchoix='0'; varscript='sousCategorie.php'; go(varscript,idchoix);\">";
					echo "<option value=\"0\">Sélectionnez une classe ... </option>";					
					
					while ($row = mysql_fetch_assoc($res)){
						echo "<option value='".$row["grname"]."'>".substr($row['grname'],7,strlen($row['grname']) - 7)."</option>";
					}

					echo "</select>";
				}
				else {
					echo "<br /><b> Les groupes \"Classes\" n'ont pas encore été créés.</b>";
				}

				break;
		
		case "2" : //l'utilisateur souhaite ajouter un professeur
				
                                $res = mysql_query("SELECT grmember FROM ".$prefix."groups WHERE grname=\"Profs\" ORDER BY grmember",$link);
                                
				if ( mysql_num_rows($res) != 0 ) {
					
					echo "<br /> Veuillez sélectionner le ou les professeurs que vous souhaitez ajouter au groupe : ";
					echo "<select multiple id='sous_type' name='sous_type' >";
					while ($row = mysql_fetch_assoc($res)){
						list($user, $groups) = people_get_variables($row["grmember"], false);
						echo "<option value='".$row["grmember"]."'>".$user["fullname"]." (".$row["grmember"].") </option>";
					}
 				
					echo "</select>";
 					echo "<br /><br />";
					echo "<input type=\"button\" style=\"color:#FF0000;\" value=\"Ajouter le ou les utilisateurs au groupe\" style=\"width: 300px\" onClick=\" selectione=''; for(i=0; i<document.membres.sous_type.options.length; i++) { if(document.forms.membres.sous_type.options[i].selected) { selectione+=document.forms.membres.sous_type.options[i].value+'|'; } } ajoutmembres(selectione,document.membres.group_new_members.value); \" >";
 
				}
				else {
					echo "<br /><b> Il n'y a pas encore de professeurs enregistrés dans le wiki.</b>";
				}
				
				break;

		case "3" : //l'utilisateur souhaite ajouter un administratif
                                
				$res = mysql_query("SELECT grmember FROM ".$prefix."groups WHERE grname=\"Administratifs\" ORDER BY grmember",$link);
				
				if ( mysql_num_rows($res) != 0 ) {

					echo "<br /> Veuillez sélectionner l'administratif que vous souhaitez ajouter au groupe : ";
					echo "<select multiple id='sous_type' name='sous_type'>";
				
					while ($row = mysql_fetch_assoc($res)){
						list($user, $groups) = people_get_variables($row["grmember"], false);
						echo "<option value='".$row["grmember"]."'>".$user["fullname"]." (".$row["grmember"].") </option>";
					}
				
					echo "</select>";
                                        echo "<br /><br />";
                                        echo "<input type=\"button\" style=\"color:#FF0000;\" value=\"Ajouter le ou les utilisateurs au groupe\" style=\"width: 300px\" onClick=\" selectione=''; for(i=0; i<document.membres.sous_type.options.length; i++) { if(document.forms.membres.sous_type.options[i].selected) { selectione+=document.forms.membres.sous_type.options[i].value+'|'; } } ajoutmembres(selectione,document.membres.group_new_members.value); \" >";
					
				}
				else {
					echo "<br /><b> Il n'y a pas encore d'administratifs enregistrés dans le wiki.</b>";
				}
				
				break;
	}
}

?>
