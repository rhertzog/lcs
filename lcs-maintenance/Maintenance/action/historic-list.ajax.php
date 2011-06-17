<?php
/**
 * historic_list.ajax.php
 * Affichage de l'historique sous forme de tableau
 * 
 * 
*/
include "../Includes/config.inc.php";
include "../Includes/func_maint.inc.php";
$mode="team";
$filter = "Acq='2' ";
#Aff_bar_mode ("Historique gen.");
Aff_feed_close ($mode, $filter,"desc")
?>