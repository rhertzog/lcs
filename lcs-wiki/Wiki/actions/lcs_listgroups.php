<?php

/*
---------------------------------------------------------
Ajouté par le lycée laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/


if (!defined('WIKINI_VERSION'))
{
	exit("accès direct interdit");
}


if(isset($_POST['grp'])) {
	$grp = $_POST['grp'];
	$this->supGroups($grp);
}


if ($grps = $this->LoadGroup(""))
{
   #récupération du login de la personne connectée
   $login = $this->GetUserName();
   
   if ($this->UserInGroup("admins")) {
	#le groupe "admins" peut voir/modifier et/ou supprimer tous les groupes    	
	echo "<div class=\"grouplist\"><u>Liste de tous les groupes</u> : <p>\n";
	
	echo "<table border=\"1\">";
	echo "<tr><td align=\"center\">NOM DU GROUPE</td><td align=\"center\">MEMBRES DU GROUPE</td><td align=\"center\">ACTION</td>";	
	
	foreach ($grps as $grname => $grmembers)
	{
	    $grmembers = str_replace("\n", ", ", $grmembers);
	    
	    echo "<tr><td>";
	    //le substr($grmembers,0,strlen($grmembers) - 2) permet d'enlever la virgule de la fin
	    echo $this->Link("EditGroup", "&group=$grname", "$grname")." </td><td> ".substr($grmembers,0,strlen($grmembers) - 2)."</td><td>".$this->FormOpen("")."<input type=\"hidden\" name=\"grp\" value=\"".$grname."\" ><input type=\"submit\" name=\"suppr\" value=\"SUPPRIMER\" >".$this->FormClose("")."</td></tr>\n";
	}
	echo "</table></p></div>\n";
	
   }
   else {
   	#si ce n'est pas admin, alors c'est forcément un professeur car les élèves et "invité" n'ont pas accès à cette page
	#les professeurs peuvent voir les groupes qu"ils ont créés
		
	$i = 0;
	foreach ($grps as $grname => $grmembers)
	{
		if (($existingUserInGroup = $this->UserInGroup($grname, $user = $login))&&($grname != "Eleves")&&($grname != "Profs")&&(substr($grname,0,6)!="Equipe")&&(substr($grname,0,6)!="Classe")) {
			
			if($i == 0) {
        			echo "<div class=\"grouplist\"><u>Liste des groupes que vous avez créé</u> : <p>\n";
	        		echo "<table border=\"1\">";
		        	echo "<tr><td align=\"center\">NOM DU GROUPE</td><td align=\"center\">MEMBRES DU GROUPE</td><td align=\"center\">ACTION</td>";
			}
			
			$grmembers = str_replace("\n", ", ", $grmembers);			
			echo "<tr><td>";
			echo $this->Link("EditGroup", "&group=$grname", "$grname")." </td><td> ".substr($grmembers,0,strlen($grmembers) - 2)."</td><td>".$this->FormOpen("")."<input type=\"hidden\" name=\"grp\" value=\"".$grname."\" ><input type=\"submit\" name=\"suppr\" value=\"SUPPRIMER\" >".$this->FormClose("")."</td></tr>\n";
			$i++;
		}
		
	}
	
	if ( $i == 0 ) {
		echo "<b><i>Vous n'avez pas de groupe à gérer.</i></b>";
	}
	else {
		echo "</table>";
	}
	
	echo "</p></div>\n";
		   	
   }
}



?> 
