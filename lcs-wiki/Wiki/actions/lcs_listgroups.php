<?php

/*
---------------------------------------------------------
Ajout� par le lyc�e laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/


if (!defined('WIKINI_VERSION'))
{
	exit("acc�s direct interdit");
}


if(isset($_POST['grp'])) {
	$grp = $_POST['grp'];
	$this->supGroups($grp);
}


if ($grps = $this->LoadGroup(""))
{
   #r�cup�ration du login de la personne connect�e
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
   	#si ce n'est pas admin, alors c'est forc�ment un professeur car les �l�ves et "invit�" n'ont pas acc�s � cette page
	#les professeurs peuvent voir les groupes qu"ils ont cr��s
		
	$i = 0;
	foreach ($grps as $grname => $grmembers)
	{
		if (($existingUserInGroup = $this->UserInGroup($grname, $user = $login))&&($grname != "Eleves")&&($grname != "Profs")&&(substr($grname,0,6)!="Equipe")&&(substr($grname,0,6)!="Classe")) {
			
			if($i == 0) {
        			echo "<div class=\"grouplist\"><u>Liste des groupes que vous avez cr��</u> : <p>\n";
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
		echo "<b><i>Vous n'avez pas de groupe � g�rer.</i></b>";
	}
	else {
		echo "</table>";
	}
	
	echo "</p></div>\n";
		   	
   }
}



?> 
