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

if((isset($_POST['idCat']))&&($_POST['idCat'] != "0")){

	$idcat = $_POST['idCat'];

	echo "Liste des élèves de cette classe : ";
	echo "<select multiple id=\"sous_categorie\" name=\"sous_categorie\">";
		$res = mysql_query("SELECT grmember FROM ".$prefix."groups WHERE grname=\"".$_POST['idCat']."\" ORDER BY grmember",$link);	
		while ($row = mysql_fetch_assoc($res)){
			list($user, $groups) = people_get_variables($row["grmember"], false);
			echo "<option value='".$row["grmember"]."'>".$user["fullname"]." (".$row["grmember"].")</option>";
		}
		
		echo "</select>";
                echo "<br /><br />";
                echo "<input type=\"button\" style=\"color:#FF0000;\"  value=\"Ajouter le ou les utilisateurs au groupe\" style=\"width: 300px\" onClick=\" selectione=''; for(i=0; i<document.membres.sous_categorie.options.length; i++) { if(document.forms.membres.sous_categorie.options[i].selected) { selectione+=document.forms.membres.sous_categorie.options[i].value+'|'; } } ajoutmembres(selectione,document.membres.group_new_members.value); \" >";
					
}


?>
