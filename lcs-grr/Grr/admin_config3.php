<?php
#########################################################################
#                            admin_config3.php                          #
#                                                                       #
#        Interface permettant � l'administrateur                        #
#        la configuration de certains param�tres g�n�raux               #
#                Derni�re modification : 28/03/2008                     #
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

$msg = "";
// Automatic mail
if (isset($_GET['automatic_mail'])) {
    if (!saveSetting("automatic_mail", $_GET['automatic_mail'])) {
        echo "Erreur lors de l'enregistrement de automatic_mail !<br />";
        die();
    }
}
// javascript_info_disabled
if (isset($_GET['javascript_info_disabled'])) {
    if (!saveSetting("javascript_info_disabled", $_GET['javascript_info_disabled'])) {
        echo "Erreur lors de l'enregistrement de javascript_info_disabled !<br />";
        die();
    }
}
// javascript_info_admin_disabled
if (isset($_GET['javascript_info_admin_disabled'])) {
    if (!saveSetting("javascript_info_admin_disabled", $_GET['javascript_info_admin_disabled'])) {
        echo "Erreur lors de l'enregistrement de javascript_info_admin_disabled !<br />";
        die();
    }
}

if (isset($_GET['grr_mail_method'])) {
    if (!saveSetting("grr_mail_method", $_GET['grr_mail_method'])) {
        echo "Erreur lors de l'enregistrement de grr_mail_method !<br />";
        die();
    }
}

if (isset($_GET['grr_mail_smtp'])) {
    if (!saveSetting("grr_mail_smtp", $_GET['grr_mail_smtp'])) {
        echo "Erreur lors de l'enregistrement de grr_mail_smtp !<br />";
        die();
    }
}
if (isset($_GET['grr_mail_Username'])) {
    if (!saveSetting("grr_mail_Username", $_GET['grr_mail_Username'])) {
        echo "Erreur lors de l'enregistrement de grr_mail_Username !<br />";
        die();
    }
}
if (isset($_GET['grr_mail_Password'])) {
    if (!saveSetting("grr_mail_Password", $_GET['grr_mail_Password'])) {
        echo "Erreur lors de l'enregistrement de grr_mail_Password !<br />";
        die();
    }
}

if (isset($_GET['grr_mail_from'])) {
    if (!saveSetting("grr_mail_from", $_GET['grr_mail_from'])) {
        echo "Erreur lors de l'enregistrement de grr_mail_from !<br />";
        die();
    }
}
if (isset($_GET['grr_mail_fromname'])) {
    if (!saveSetting("grr_mail_fromname", $_GET['grr_mail_fromname'])) {
        echo "Erreur lors de l'enregistrement de grr_mail_fromname !<br />";
        die();
    }
}


if (isset($_GET['grr_mail_Bcc'])) $grr_mail_Bcc = "y"; else $grr_mail_Bcc = "n";
if (!saveSetting("grr_mail_Bcc", $grr_mail_Bcc)) {
    echo "Erreur lors de l'enregistrement de grr_mail_Bcc !<br />";
    die();
}


if (isset($_GET['verif_reservation_auto'])) {
    if (!saveSetting("verif_reservation_auto", $_GET['verif_reservation_auto'])) {
        echo "Erreur lors de l'enregistrement de verif_reservation_auto !<br />";
        die();
    }
    if ($_GET['verif_reservation_auto']==0) $_GET['motdepasse_verif_auto_grr'] = "";
}

if (isset($_GET['motdepasse_verif_auto_grr'])) {
    if (($_GET['verif_reservation_auto']==1) and ($_GET['motdepasse_verif_auto_grr']==""))
        $msg .= "l'ex�cution du script verif_auto_grr.php requiert un mot de passe !\\n";
    if (!saveSetting("motdepasse_verif_auto_grr", $_GET['motdepasse_verif_auto_grr'])) {
        echo "Erreur lors de l'enregistrement de motdepasse_verif_auto_grr !<br />";
        die();
    }
}



if (!loadSettings())
    die("Erreur chargement settings");

# print the page header
print_header("","","","",$type="with_session", $page="admin");
affiche_pop_up($msg,"admin");

?>
<script src="functions.js" type="text/javascript" language="javascript"></script>
<?php


// Affichage de la colonne de gauche
include "admin_col_gauche.php";

// Affichage du tableau de choix des sous-configuration
include "include/admin_config_tableau.inc.php";

echo "<form action=\"./admin_config.php\" name=\"nom_formulaire\" method=\"get\" style=\"width: 100%;\">";

//
// Automatic mail
//********************************
//
echo "<h3>".get_vocab('title_automatic_mail').grr_help("aide_grr_mail_auto")."</h3>";
echo "<p><i>".get_vocab("warning_message_mail")."</i></p>";
echo "<p>".get_vocab("explain_automatic_mail")."</p>";
?>
<input type='radio' name='automatic_mail' value='yes' id='label_3' <?php if (getSettingValue("automatic_mail")=='yes') echo "checked";?> /> <label for='label_3'><?php echo get_vocab("mail_admin_on");
if (getSettingValue("automatic_mail") == 'yes') {
    echo " - <A HREF='admin_email_manager.php'>".get_vocab('admin_email_manager.php')."</A>";
}
?></label>
<br /><input type='radio' name='automatic_mail' value='no' id='label_4' <?php if (getSettingValue("automatic_mail")=='no') echo "checked";?> /> <label for='label_4'><?php echo get_vocab("mail_admin_off"); ?></label>
<?php
echo "<h3>".get_vocab('Parametres configuration envoi automatique mails')."</h3>";
echo get_vocab('Explications des parametres configuration envoi automatique mails');
echo "<br /><br /><input type=\"radio\" name=\"grr_mail_method\" value=\"mail\" ";
if (getSettingValue('grr_mail_method')=="mail") echo " checked ";
echo "/>\n";
echo get_vocab('methode mail');
echo "&nbsp;&nbsp;<input type=\"radio\" name=\"grr_mail_method\" value=\"smtp\" ";
if (getSettingValue('grr_mail_method')=="smtp") echo " checked ";
echo "/>\n";
echo get_vocab('methode smtp');
echo "<br /><br />".get_vocab('Explications methode smtp 1').get_vocab('deux_points');
echo "<input type = \"text\" name=\"grr_mail_smtp\" value =\"".getSettingValue('grr_mail_smtp')."\" />";
echo "<br />".get_vocab('Explications methode smtp 2');
echo "<br />".get_vocab('utilisateur smtp').get_vocab('deux_points');
echo "<input type = \"text\" name=\"grr_mail_Username\" value =\"".getSettingValue('grr_mail_Username')."\" />";
echo "<br />".get_vocab('pwd').get_vocab('deux_points');
echo "<input type = \"password\" name=\"grr_mail_Password\" value =\"".getSettingValue('grr_mail_Password')."\" />";

echo "<br />".get_vocab('Email_expediteur_messages_automatiques').get_vocab('deux_points');
if (trim(getSettingValue('grr_mail_from')) == "")
    $grr_mail_from = "noreply@mon.site.fr";
else
    $grr_mail_from = getSettingValue('grr_mail_from');
echo "<input type = \"text\" name=\"grr_mail_from\" value =\"".$grr_mail_from."\" size=\"30\" />";
echo "<br />".get_vocab('Nom_expediteur_messages_automatiques').get_vocab('deux_points');
echo "<input type = \"text\" name=\"grr_mail_fromname\" value =\"".getSettingValue('grr_mail_fromname')."\" size=\"30\" />";


echo "<br /><br />";
echo "<input type=\"checkbox\" name=\"grr_mail_Bcc\" value=\"y\" ";
if (getSettingValue('grr_mail_Bcc')=="y") echo " checked ";
echo "/>";
echo get_vocab('copie cachee');


# D�sactive les messages javascript (pop-up) apr�s la cr�ation/modificatio/suppression d'une r�servation
# 1 = Oui, 0 = Non
echo "<hr /><h3>".get_vocab("javascript_info_disabled_msg")."</h3>";
echo "<table cellspacing=\"5\">";
echo "<tr><td>".get_vocab("javascript_info_disabled0")."</td><td>";
echo "<input type='radio' name='javascript_info_disabled' value='0' "; if (getSettingValue("javascript_info_disabled")=='0') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("javascript_info_disabled1")."</td><td>";
echo "<input type='radio' name='javascript_info_disabled' value='1' "; if (getSettingValue("javascript_info_disabled")=='1') echo "checked"; echo " />";
echo "</td></tr>";
echo "</table>";

# D�sactive les messages javascript d'information (pop-up) dans les menus d'administration
# 1 = Oui, 0 = Non
echo "<hr /><h3>".get_vocab("javascript_info_admin_disabled_msg")."</h3>";
echo "<table cellspacing=\"5\">";
echo "<tr><td>".get_vocab("javascript_info_admin_disabled0")."</td><td>";
echo "<input type='radio' name='javascript_info_admin_disabled' value='0' "; if (getSettingValue("javascript_info_admin_disabled")=='0') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("javascript_info_disabled1")."</td><td>";
echo "<input type='radio' name='javascript_info_admin_disabled' value='1' "; if (getSettingValue("javascript_info_admin_disabled")=='1') echo "checked"; echo " />";
echo "</td></tr>";
echo "</table>";

# t�che automatique de suppression
echo "<hr /><h3>".get_vocab("suppression automatique des r�servations")."</h3>";
echo get_vocab('Explications suppression automatique des r�servations').grr_help("aide_grr_verif_auto_grr");
echo "<table cellspacing=\"5\">";
echo "<tr><td>".get_vocab("verif_reservation_auto0")."</td><td>";
echo "<input type='radio' name='verif_reservation_auto' value='0' "; if (getSettingValue("verif_reservation_auto")=='0') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("verif_reservation_auto1")."</td><td>";
echo "<input type='radio' name='verif_reservation_auto' value='1' "; if (getSettingValue("verif_reservation_auto")=='1') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("verif_reservation_auto2").get_vocab("deux_points")."</td><td>";
echo "<input type=\"text\" name=\"motdepasse_verif_auto_grr\" value=\"".getSettingValue("motdepasse_verif_auto_grr")."\" size=\"20\" />";
echo "</td></tr>";
echo "</table>";
echo "<input type=\"hidden\" name=\"page_config\" value=\"3\" />";
echo "<br /><center><div id=\"fixe\"><input type=\"submit\" value=\"".get_vocab("save")."\" style=\"font-variant: small-caps;\"/></div></center>";
echo "</FORM>";

// fin de l'affichage de la colonne de droite
echo "</td></tr></table>";
?>