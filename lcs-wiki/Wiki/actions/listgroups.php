<?php

if ($grps = $this->LoadGroup(""))
{
    echo "<div class=\"grouplist\">\n";
    foreach ($grps as $grname => $grmembers)
    {
        $grmembers = str_replace("\n", " ", $grmembers);
        echo $this->Link("EditGroup", "&group=$grname", "$grname")." . . . ".$grmembers."<br />\n";
    }
    echo "</div>\n";
}

?> 