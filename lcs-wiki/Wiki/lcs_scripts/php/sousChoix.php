<?php

//ouvre la connexion vers la base de donn�es
include("connexion_mysql.php");

//pour pouvoir utiliser la fonction LCS people_get_variables pour r�cup�rer le nom et le prenom des utilisateurs
include "/var/www/lcs/includes/headerauth.inc.php";
include "/var/www/Annu/includes/ldap.inc.php";

if((isset($_POST['idCat']))&&($_POST['idCat'] != "0")){

	$idcat = $_POST['idCat'];

	echo "Liste des �l�ves de cette classe : ";
	echo "<select multiple id=\"sous_categorie\" name=\"sous_categorie\">";
	
	$res = mysql_query("SELECT grmember FROM ".$prefix."groups WHERE grname=\"".$_POST['idCat']."\" ORDER BY grmember",$link);	
	while ($row = mysql_fetch_assoc($res)){
		list($user, $groups) = people_get_variables($row["grmember"], false);
		echo "<option value='".$row["grmember"]."'>".$user["fullname"]." (".$row["grmember"].")</option>";
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
	echo "<input type=\"radio\" name=\"ecr\" value=\"aut\" >autoriser l'�criture <br/>";
	echo "<input type=\"radio\" name=\"ecr\" value=\"ref\" >refuser l'�criture <br/>";
	echo "</td>";
        echo "<td valign=\"top\" style=\"padding-right: 20px\"> ";
	echo "<input type=\"radio\" name=\"com\" value=\"aut\" >autoriser l'ajout de commentaire <br/>";
	echo "<input type=\"radio\" name=\"com\" value=\"ref\" >refuser l'ajout de commentaire <br/>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
		
	//champ cach� poue pouvoir r�cup�rer idchoice ( envoy� gr�ca � la fonction javascript go() )dans le script javascript
	echo "<input type=\"hidden\" name=\"idchoi\" value=\"".$_POST['idchoice']."\">";
		
	echo "<br />";
        echo "<input type=\"button\" style=\"color:#FF0000;\" value=\"Valider les droits s�lectionn�s\" style=\"width: 300px\" onClick=\" selectione=''; for(i=0; i<document.acls.sous_categorie.options.length; i++) { if(document.acls.sous_categorie.options[i].selected) { selectione+=document.acls.sous_categorie.options[i].value+'|'; } } if(document.acls.lect[0].checked){ lecture=document.acls.lect[0].value; }else if(document.acls.lect[1].checked){ lecture=document.acls.lect[1].value; }else{ lecture='0'; } if(document.acls.ecr[0].checked){ ecriture=document.acls.ecr[0].value; }else if(document.acls.ecr[1].checked){ ecriture=document.acls.ecr[1].value; }else{ ecriture='0'; } if(document.acls.com[0].checked){ commentaire=document.acls.com[0].value; }else if(document.acls.com[1].checked){ commentaire=document.acls.com[1].value; }else{ commentaire='0'; }  modifdroits(selectione,document.acls.read_acl.value,document.acls.write_acl.value,document.acls.comment_acl.value,lecture,ecriture,commentaire,document.acls.idchoi.value); \" >";
					
}

?>
