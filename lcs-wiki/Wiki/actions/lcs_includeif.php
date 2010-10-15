<?php
/*
include_if.php : Permet d'inclure une page Wiki dans un autre page
                 selon l'appartenance au groupe

---------------------------------------------------------
Ajout� par le lyc�e laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------

*/

if (!defined('WIKINI_VERSION'))
{
	exit("acc�s direct interdit");
}

if ($this->UserInGroup("eleves"))$incPageName="MenuEleves";
if ($this->UserInGroup("administratifs"))$incPageName="MenuAdministratifs";				
if ($this->UserInGroup("profs")) $incPageName="MenuProfs";
if ($this->UserInGroup("admins")) $incPageName="MenuAdmin"; 

foreach(explode(",",$incPageName) as $incPageName)
{
  if (!empty($incPageName))
  {
    // Affichage de la page ou d'un message d'erreur
    if (empty($incPageName)) {
        echo $this->Format("//Pas de menu d'administration car vous ne fa�tes partie d'aucun groupe.//");
    } else {
        if (eregi("^".$incPageName."$",$this->GetPageTag())) {
            echo $this->Format("//Impossible � une page de s'inclure dans elle m�me.//");
        } else {
            if (!$this->HasAccess("read",$incPageName)){
                echo $this->Format("//Lecture de la page inclue $page non autoris�e.//");
            } else {
                $incPage = $this->LoadPage($incPageName);
                $output = $this->Format($incPage["body"]);
                if ($classes) echo "<div class=\"", $classes,"\">\n", $output, "</div>\n";
                else echo $output;
            }
        }
    }
  }
}

?>  
