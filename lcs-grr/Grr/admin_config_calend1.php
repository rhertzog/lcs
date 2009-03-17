<?php
#########################################################################
#                            admin_config_calend1.php                   #
#                                                                       #
#        Interface permettant à l'administrateur                        #
#        la configuration des paramètres pour le module Jours Cycles    #
#                Dernière modification : 10/12/2007                     #
#                                                                       #
#########################################################################

if (!loadSettings())
    die("Erreur chargement settings");
# print the page header
print_header("","","","",$type="with_session", $page="admin");

// Affichage de la colonne de gauche
include "admin_col_gauche.php";
?>
<script src="functions.js" type="text/javascript" language="javascript"></script>
<?php

// Affichage du tableau de choix des sous-configuration
$grr_script_name = "admin_calend_jour_cycle.php";
include "include/admin_calend_jour_cycle.inc.php";


// Met à jour dans la BD le nombre de jours par cycle
if (isset($_GET['nombreJours'])) {
    if (!saveSetting("nombre_jours_Jours/Cycles", $_GET['nombreJours'])) {
        echo "Erreur lors de l'enregistrement de nombre_jours_Jours/Cycles ! <br />";
    }
}
// Met à jour dans la BD le premier jour du premier cycle
if (isset($_GET['jourDebut'])) {
    if (!saveSetting("jour_debut_Jours/Cycles", $_GET['jourDebut'])) {
        echo "Erreur lors de l'enregistrement de jour_debut_Jours/Cycles ! <br />";
    }
}

//
// Configurations du nombre de jours par Jours/Cycles et du premier jour du premier Jours/Cycles
//******************************
//
echo "<h3>".get_vocab("titre_config_Jours/Cycles").grr_help("aide_grr_jours_cycle")."</h3>";
echo "<form action=\"./admin_calend_jour_cycle.php\" name=\"configuration_Jours_Cycles\" method=\"get\" style=\"width: 100%;\" onsubmit=\"return verifierJoursCycles(false);\">\n";
echo get_vocab("explication_Jours_Cycles1");
echo "<br />".get_vocab("explication_Jours_Cycles2");
?>
<br /><br /><table border="1" cellpadding="5" cellspacing="1">
<tr>
<td>
<?php echo get_vocab("nombre_jours_Jours/Cycles").get_vocab("deux_points"); ?>
</td><td>
<!-- Pour sélectionner le nombre de jours par Cycle  -->
<?php
echo "<SELECT name='nombreJours' id='nombreJours'>\n";
for($i = 1; $i < 21; $i++) {
    if ($i == getSettingValue("nombre_jours_Jours/Cycles")){
        echo "<OPTION SELECTED>".$i."</OPTION>\n";
    }
    else echo "<OPTION>".$i."</OPTION>\n";
}
echo "</SELECT>\n";
?>
</td></tr>

<!-- Pour sélectionner le jour_cycle qui débutera le premier Jours/Cycles  -->
<tr><td><?php echo get_vocab("debut_Jours/Cycles").get_vocab("deux_points")
."<br /><i>".get_vocab("explication_debut_Jours_Cycles")."</i>"; ?></td><td>
<?php
echo "<SELECT name='jourDebut' id='jourDebut'>";
for($i = 1; $i < 21; $i++) {
    if ($i == getSettingValue("jour_debut_Jours/Cycles")){
        echo "<OPTION SELECTED>".$i."</OPTION>\n";
    }
    else echo "<OPTION>".$i."</OPTION>\n";
}
?>
</SELECT>
</td>
</tr>
</table>
<?php
echo "<center><div id=\"fixe\"><input type=\"submit\" value=\"".get_vocab("save")."\" style=\"font-variant: small-caps;\"/></div></center>";
echo "<input type=\"hidden\" value=\"1\" name=\"page_calend\" />";
echo "</form>";
// fin de l'affichage de la colonne de droite
echo "</td></tr></table>";
?>