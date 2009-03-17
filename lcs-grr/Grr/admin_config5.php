<?php
#########################################################################
#                            admin_config5.php                          #
#                                                                       #
#        Interface permettant à l'administrateur                        #
#        la configuration des paramètres pour le module Jours Cycles    #
#                Dernière modification : 10/12/2007                     #
#                                                                       #
#########################################################################

if (!loadSettings())
    die("Erreur chargement settings");

// Met à jour dans la BD le champ qui détermine si les fonctionnalités Jours/Cycles sont activées ou désactivées
if (isset($_GET['actif'])) {
    if (!saveSetting("jours_cycles_actif", $_GET['actif'])) {
        echo "Erreur lors de l'enregistrement de jours_cycles_actif ! <br />";
    }
}

# print the page header
print_header("","","","",$type="with_session", $page="admin");

?>
<script src="functions.js" type="text/javascript" language="javascript"></script>
<?php

// Affichage de la colonne de gauche
include "admin_col_gauche.php";

// Affichage du tableau de choix des sous-configuration
include "include/admin_config_tableau.inc.php";

// use_fckeditor
if (isset($_GET['use_fckeditor'])) {
    if (!saveSetting("use_fckeditor", $_GET['use_fckeditor'])) {
        echo "Erreur lors de l'enregistrement de use_fckeditor !<br />";
        die();
    }
}



//
// Configurations du nombre de jours par Cycle et du premier jour du premier Jours/Cycles
//******************************
//
echo "<h3>".get_vocab("Activer_module_jours_cycles").grr_help("aide_grr_jours_cycle")."</h3>";
echo "<form action=\"./admin_config.php\" name=\"configuration_Jours_Cycles\" method=\"get\" style=\"width: 100%;\" onsubmit=\"return verifierJoursCycles(false);\">\n";
?>
<table border='0'>
<tr>
<td>
<?php echo get_vocab("Activer_module_jours_cycles").get_vocab("deux_points");
?>
<!-- Affiche Oui/Non, pour activer ou désactiver les Jours/Cycles -->
<?php
echo "<SELECT name='actif'>\n";
if (getSettingValue("jours_cycles_actif") == "Oui") {
    echo "<OPTION value=\"Oui\" SELECTED>Oui</OPTION>\n";
    echo "<OPTION value=\"Non\">Non</OPTION>\n";
}
else {
    echo "<OPTION value=\"Oui\">Oui</OPTION>\n";
    echo "<OPTION value=\"Non\" SELECTED>Non</OPTION>\n";
}
echo "</select>";
?>
</td>
</tr>
</table>

<?php

# La page de modification de la configuration d'une ressource utilise pour le champ "description complète"
# l'application FckEditor permettant une mise en forme "wysiwyg" de la page.
# "0" pour ne pas utiliser cette application (le répertoire "fckeditor" et tout ce qu'il contient n'est alors pas nécessaire au bon fonctionnement de GRR).
echo "<hr /><h3>".get_vocab("use_fckeditor_msg")."</h3>";
echo "<p>".get_vocab("use_fckeditor_explain")."</p>";
echo "<table>";
echo "<tr><td>".get_vocab("use_fckeditor0")."</td><td>";
echo "<input type='radio' name='use_fckeditor' value='0' "; if (getSettingValue("use_fckeditor")=='0') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("use_fckeditor1")."</td><td>";
echo "<input type='radio' name='use_fckeditor' value='1' "; if (getSettingValue("use_fckeditor")=='1') echo "checked"; echo " />";
echo "</td></tr>";
echo "</table>";


echo "<center><div id=\"fixe\"><input type=\"submit\" value=\"".get_vocab("save")."\" style=\"font-variant: small-caps;\"/></div></center>";
echo "<input type=hidden value=5 name=page_config />";
echo "</form>";
// fin de l'affichage de la colonne de droite
echo "</td></tr></table>";
?>