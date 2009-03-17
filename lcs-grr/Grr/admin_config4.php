<?php
#########################################################################
#                            admin_config4.php                          #
#                                                                       #
#        Interface permettant à l'administrateur                        #
#        la configuration de certains paramètres généraux               #
#                Dernière modification : 23/09/2006                     #
#                                                                       #
#########################################################################
/*
 * Copyright 2003-2005 Laurent Delineau
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GRR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GRR; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
if (isset($_GET['disable_login'])) {
    if (!saveSetting("disable_login", $_GET['disable_login'])) {
        echo "Erreur lors de l'enregistrement de disable_login !<br />";
        die();
    }
}
if (isset($_GET['url_disconnect'])) {
    if (!saveSetting("url_disconnect", $_GET['url_disconnect'])) {
        echo "Erreur lors de l'enregistrement de url_disconnect ! <br />";
    }
}
// Max session length
if (isset($_GET['sessionMaxLength'])) {
    if (!(ereg ("^[0-9]{1,}$", $_GET['sessionMaxLength'])) || $_GET['sessionMaxLength'] < 1) {
        $_GET['sessionMaxLength'] = 30;
    }
    if (!saveSetting("sessionMaxLength", $_GET['sessionMaxLength'])) {
        echo "Erreur lors de l'enregistrement de sessionMaxLength !<br />";
    }
}
// pass_leng
if (isset($_GET['pass_leng'])) {
    settype($_GET['pass_leng'],"integer");
    if ($_GET['pass_leng'] < 1) $_GET['pass_leng'] = 1;
    if (!saveSetting("pass_leng", $_GET['pass_leng'])) {
        echo "Erreur lors de l'enregistrement de pass_leng !<br />";
    }
}

if (!loadSettings())
    die("Erreur chargement settings");

# print the page header
print_header("","","","",$type="with_session", $page="admin");

// Affichage de la colonne de gauche
include "admin_col_gauche.php";

// Affichage du tableau de choix des sous-configuration
include "include/admin_config_tableau.inc.php";

//echo "<h2>".get_vocab('admin_config4.php')."</h2>";


//
// dans le cas de mysql, on propose une sauvegarde de la base
//
if ($dbsys == "mysql") {;
    //
    // Saving base
    //********************************
    //
    echo "<h3>".get_vocab('title_backup')."</h3>";
    echo "<p>".get_vocab("explain_backup")."</p>";
    echo "<p><i>".get_vocab("warning_message_backup")."</i></p>";
    ?>
    <form action="admin_save_mysql.php" method='get' style="width: 100%;" name="form_backup">
    <center><input type="submit" value=" <?php echo get_vocab("submit_backup"); ?>" style="font-variant: small-caps;" /></center>
    </form>
    <?php
}

echo "<form action=\"./admin_config.php\" name=\"nom_formulaire\" method=\"get\" style=\"width: 100%;\">";
//
// Suspendre les connexions
//*************************
//
echo "<hr /><h3>".get_vocab('title_disable_login')."</h3>";
echo "<p>".get_vocab("explain_disable_login")."</p>";
?>
<input type='radio' name='disable_login' value='yes' id='label_1' <?php if (getSettingValue("disable_login")=='yes') echo "checked";?> /> <label for='label_1'><?php echo get_vocab("disable_login_on");

?></label>
<br /><input type='radio' name='disable_login' value='no' id='label_2' <?php if (getSettingValue("disable_login")=='no') echo "checked";?> /> <label for='label_2'><?php echo get_vocab("disable_login_off"); ?></label>
<?php
echo "<hr />";

//
// Durée d'une session
//********************
//
echo "<h3>".get_vocab("title_session_max_length")."</h3>";
?>
<table border='0'>
<tr><td><?php echo get_vocab("session_max_length"); ?></td><td>
<input type="text" name="sessionMaxLength" size="16" value="<?php echo(getSettingValue("sessionMaxLength")); ?>" /></td>
</tr>
</table>
<?php echo get_vocab("explain_session_max_length");


//Longueur minimale du mot de passe exigé
echo "<hr /><h3>".get_vocab("pwd")."</h3>";
echo get_vocab("pass_leng_explain").get_vocab("deux_points")."
<input type=\"text\" name=\"pass_leng\" value=\"".htmlentities(getSettingValue("pass_leng"))."\" size=\"20\" />";

//
// Url de déconnexion
//*******************
//
echo "<hr /><H3>".get_vocab("Url_de_deconnexion")."</H3>\n";
echo "<p>".get_vocab("Url_de_deconnexion_explain")."</p>\n";
echo "<p><i>".get_vocab("Url_de_deconnexion_explain2")."</i></p>";
echo "<br />".get_vocab("Url_de_deconnexion").get_vocab("deux_points")."\n";
$value_url=getSettingValue("url_disconnect");
echo "<INPUT TYPE=\"text\" name=\"url_disconnect\" size=40 value =\"$value_url\"/>\n<br /><br />";
echo "<hr />";



echo "<input type=\"hidden\" name=\"page_config\" value=\"4\" />";
echo "<br /><center><div id=\"fixe\"><input type=\"submit\" value=\"".get_vocab("save")."\" style=\"font-variant: small-caps;\"/></div></center>";
echo "</FORM>";

// fin de l'affichage de la colonne de droite
echo "</td></tr></table>";
?>