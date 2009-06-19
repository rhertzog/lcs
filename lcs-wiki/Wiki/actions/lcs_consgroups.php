<?php

/*
---------------------------------------------------------
Ajouté par le lycée laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/


if ($grps = $this->LoadGroup(""))
{   
  
  if ($this->UserInGroup("admins")){
    echo "<div class=\"grouplist\">\n";
    foreach ($grps as $grname => $grmembers)
    {
        $grmembers = str_replace("\n", " ", $grmembers);
        echo $this->Link("EditGroup", "&group=$grname", "$grname")." . . . ".$grmembers."<br />\n";
    }
    echo "</div>\n";
  }
  else {
    echo "<div class=\"grouplist\">\n";
    foreach ($grps as $grname => $grmembers)
    {
    	$grmembers = str_replace("\n", " ", $grmembers);
	echo "<b>".$grname."</b> . . . ".$grmembers."<br />\n";
    }
    echo "</div>\n";
  }
}

?>
