<?php
#########################################################################
#                            admin_config1.php                          #
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
if (isset($_GET['title_home_page'])) {
    if (!saveSetting("title_home_page", $_GET['title_home_page'])) {
        echo "Erreur lors de l'enregistrement de title_home_page !<br />";
        die();
    }
}
if (isset($_GET['message_home_page'])) {
    if (!saveSetting("message_home_page", $_GET['message_home_page'])) {
        echo "Erreur lors de l'enregistrement de message_home_page !<br />";
        die();
    }
}
if (isset($_GET['company'])) {
    if (!saveSetting("company", $_GET['company'])) {
        echo "Erreur lors de l'enregistrement de company !<br />";
        die();
    }
}
if (isset($_GET['webmaster_name'])) {
    if (!saveSetting("webmaster_name", $_GET['webmaster_name'])) {
        echo "Erreur lors de l'enregistrement de webmaster_name !<br />";
        die();
    }
}
if (isset($_GET['webmaster_email'])) {
    if (!saveSetting("webmaster_email", $_GET['webmaster_email'])) {
        echo "Erreur lors de l'enregistrement de webmaster_email !<br />";
        die();
    }
}
if (isset($_GET['technical_support_email'])) {
    if (!saveSetting("technical_support_email", $_GET['technical_support_email'])) {
        echo "Erreur lors de l'enregistrement de technical_support_email !<br />";
        die();
    }
}
if (isset($_GET['message_accueil'])) {
    if (!saveSetting("message_accueil", $_GET['message_accueil'])) {
        echo "Erreur lors de l'enregistrement de message_accueil !<br />";
        die();
    }
}
if (isset($_GET['grr_url'])) {
    if (!saveSetting("grr_url", $_GET['grr_url'])) {
        echo "Erreur lors de l'enregistrement de grr_url !<br />";
        die();
    }
}
if (isset($_GET["ok"])) {
  if (isset($_GET['use_grr_url'])) $use_grr_url = "y"; else $use_grr_url = "n";
    if (!saveSetting("use_grr_url", $use_grr_url)) {
      echo "Erreur lors de l'enregistrement de use_grr_url !<br />";
      die();
  }
}


// Style/thème
if (isset($_GET['default_css'])) {
    if (!saveSetting("default_css", $_GET['default_css'])) {
        echo "Erreur lors de l'enregistrement de default_css !<br />";
        die();
    }
}

// langage
if (isset($_GET['default_language'])) {
    if (!saveSetting("default_language", $_GET['default_language'])) {
        echo "Erreur lors de l'enregistrement de default_language !<br />";
        die();
    }
    unset ($_SESSION['default_language']);

}

// Type d'affichage des listes des domaines et des ressources
if (isset($_GET['area_list_format'])) {
    if (!saveSetting("area_list_format", $_GET['area_list_format'])) {
        echo "Erreur lors de l'enregistrement de area_list_format !<br />";
        die();
    }
}

// domaine par défaut
if (isset($_GET['id_area'])) {
    if (!saveSetting("default_area", $_GET['id_area'])) {
        echo "Erreur lors de l'enregistrement de default_area !<br />";
        die();
    }
}
if (isset($_GET['id_room'])) {
    if (!saveSetting("default_room", $_GET['id_room'])) {
        echo "Erreur lors de l'enregistrement de default_room !<br />";
        die();
    }
}

// display_info_bulle
if (isset($_GET['display_info_bulle'])) {
    if (!saveSetting("display_info_bulle", $_GET['display_info_bulle'])) {
        echo "Erreur lors de l'enregistrement de display_info_bulle !<br />";
        die();
    }
}

// display_full_description
if (isset($_GET['display_full_description'])) {
    if (!saveSetting("display_full_description", $_GET['display_full_description'])) {
        echo "Erreur lors de l'enregistrement de display_full_description !<br />";
        die();
    }
}

// display_short_description
if (isset($_GET['display_short_description'])) {
    if (!saveSetting("display_short_description", $_GET['display_short_description'])) {
        echo "Erreur lors de l'enregistrement de display_short_description !<br />";
        die();
    }
}

// remplissage de la description brève
if (isset($_GET['remplissage_description_breve'])) {
    if (!saveSetting("remplissage_description_breve", $_GET['remplissage_description_breve'])) {
        echo "Erreur lors de l'enregistrement de remplissage_description_breve !<br />";
        die();
    }
}

// pview_new_windows
if (isset($_GET['pview_new_windows'])) {
    if (!saveSetting("pview_new_windows", $_GET['pview_new_windows'])) {
        echo "Erreur lors de l'enregistrement de pview_new_windows !<br />";
        die();
    }
}

// gestion_lien_aide
if (isset($_GET['gestion_lien_aide'])) {
    if (($_GET['gestion_lien_aide']=="perso") and (trim($_GET['lien_aide'])==""))
        $_GET['gestion_lien_aide'] = "ext";
    else if ($_GET['gestion_lien_aide']!="perso")
        $_GET['lien_aide']="";

    if (!saveSetting("lien_aide", $_GET['lien_aide'])) {
        echo "Erreur lors de l'enregistrement de lien_aide !<br />";
        die();
    }

    if (!saveSetting("gestion_lien_aide", $_GET['gestion_lien_aide'])) {
        echo "Erreur lors de l'enregistrement de gestion_lien_aide !<br />";
        die();
    }

}



# Lors de l'édition d'un rapport, valeur par défaut en nombre de jours
# de l'intervalle de temps entre la date de début du rapport et la date de fin du rapport.
if (isset($_GET['default_report_days'])) {
    settype($_GET['default_report_days'],"integer");
    if ($_GET['default_report_days'] <=0) $_GET['default_report_days'] = 0;
    if (!saveSetting("default_report_days", $_GET['default_report_days'])) {
        echo "Erreur lors de l'enregistrement de default_report_days !<br />";
        die();
    }
}


$demande_confirmation = 'no';
if (isset($_GET['begin_day']) and isset($_GET['begin_month']) and isset($_GET['begin_year'])) {
    while (!checkdate($_GET['begin_month'],$_GET['begin_day'],$_GET['begin_year']))
        $_GET['begin_day']--;
    $begin_bookings = mktime(0,0,0,$_GET['begin_month'],$_GET['begin_day'],$_GET['begin_year']);
    $test_del1 = mysql_num_rows(mysql_query("select * from grr_entry WHERE (end_time < '$begin_bookings' )"));
    $test_del2 = mysql_num_rows(mysql_query("select * from grr_repeat WHERE (end_date < '$begin_bookings')"));
    if (($test_del1!=0) or ($test_del2!=0)) {
        $demande_confirmation = 'yes';
    } else {
        if (!saveSetting("begin_bookings", $begin_bookings))
        echo "Erreur lors de l'enregistrement de begin_bookings !<br />";
    }

}
if (isset($_GET['end_day']) and isset($_GET['end_month']) and isset($_GET['end_year'])) {
    while (!checkdate($_GET['end_month'],$_GET['end_day'],$_GET['end_year']))
        $_GET['end_day']--;
    $end_bookings = mktime(0,0,0,$_GET['end_month'],$_GET['end_day'],$_GET['end_year']);
    if ($end_bookings < $begin_bookings) $end_bookings = $begin_bookings;


    $test_del1 = mysql_num_rows(mysql_query("select * from grr_entry WHERE (start_time > '$end_bookings' )"));
    $test_del2 = mysql_num_rows(mysql_query("select * from grr_repeat WHERE (start_time > '$end_bookings')"));
    if (($test_del1!=0) or ($test_del2!=0)) {
        $demande_confirmation = 'yes';
    } else {
        if (!saveSetting("end_bookings", $end_bookings))
        echo "Erreur lors de l'enregistrement de end_bookings !<br />";
    }


}

if ($demande_confirmation == 'yes') {
    header("Location: ./admin_confirm_change_date_bookings.php?end_bookings=$end_bookings&begin_bookings=$begin_bookings");
    die();
}

if (!loadSettings())
    die("Erreur chargement settings");

# print the page header
print_header("","","","",$type="with_session", $page="admin");
echo "<script  type=\"text/javascript\" src=\"functions.js\" language=\"javascript\"></script>";

// Affichage de la colonne de gauche
include "admin_col_gauche.php";

// Affichage du tableau de choix des sous-configuration
include "include/admin_config_tableau.inc.php";

//echo "<h2>".get_vocab('admin_config1.php')."</h2>";
//echo "<p>".get_vocab('mess_avertissement_config')."</p>";


// Adapter les fichiers de langue
echo "<h3>".get_vocab("adapter fichiers langue")."</h3>";
echo get_vocab("adapter fichiers langue explain").grr_help("aid_grr_adapter_fichiers_langue");

//
// Config générale
//****************
//
echo "<form action=\"./admin_config.php\" name=\"nom_formulaire\" method=\"get\" style=\"width: 100%;\">";
echo "<h3>".get_vocab("miscellaneous")."</h3>";
?>
<table border='0'>

<tr><td><?php echo get_vocab("title_home_page"); ?></td>
<td><input type="text" name="title_home_page" size="40" value="<?php echo(getSettingValue("title_home_page")); ?>" /></td>
</tr>
<tr><td><?php echo get_vocab("message_home_page"); ?></td>
<td><TEXTAREA NAME="message_home_page" ROWS="3" COLS="40"><?php echo(getSettingValue("message_home_page")); ?></TEXTAREA></td>

</tr>
<tr><td><?php echo get_vocab("company"); ?></td>
<td><input type="text" name="company" size="40" value="<?php echo(getSettingValue("company")); ?>" /></td>
</tr>
<tr>
<td><?php echo get_vocab("grr_url"); ?></td>
<td><input type="text" name="grr_url" size="40" value="<?php echo(getSettingValue("grr_url")); ?>" /></td>
</tr>
<tr><td colspan=2><input type="checkbox" name="use_grr_url" value="y" <?php if (getSettingValue("use_grr_url")=='y') echo " checked "; ?> /><i><?php echo get_vocab("grr_url_explain"); ?></i></td></tr>
<tr>
<td><?php echo get_vocab("webmaster_name"); ?></td>
<td><input type="text" name="webmaster_name" size="40" value="<?php echo(getSettingValue("webmaster_name")); ?>" /></td>
</tr>
<tr>
<td><?php echo get_vocab("webmaster_email"); ?></td>
<td><input type="text" name="webmaster_email" size="40" value="<?php echo(getSettingValue("webmaster_email")); ?>" /></td>
</tr>
<tr>
<td><?php echo get_vocab("technical_support_email"); ?></td>
<td><input type="text" name="technical_support_email" size="40" value="<?php echo(getSettingValue("technical_support_email")); ?>" /></td>
</tr>
</table>
<?php
if (getSettingValue("use_fckeditor") == 1) {
    // lancement de FCKeditor
    include("./fckeditor/fckeditor.php");
    $oFCKeditor = new FCKeditor('message_accueil');
    $oFCKeditor->BasePath = './fckeditor/';
    $oFCKeditor->Config['DefaultLanguage']  = 'fr';
    $oFCKeditor->Height = '150';
    $oFCKeditor->Config['CustomConfigurationsPath'] = '../fckconfig_grr.js';
    $oFCKeditor->ToolbarSet = 'Basic_Grr';
    $oFCKeditor->Value = getSettingValue('message_accueil');
}
echo "<H3>".get_vocab("message perso")."</h3>";
echo get_vocab("message perso explain");
if (getSettingValue("use_fckeditor") != 1)
    echo " ".get_vocab("description complete2");
if (getSettingValue("use_fckeditor") == 1) {
    $oFCKeditor->Create() ;
    } else {
        echo "<textarea name=\"message_accueil\" rows=\"8\" cols=\"120\" >".htmlspecialchars(getSettingValue('message_accueil'))."</textarea>";
    }



//
// Début et fin des réservations
//******************************
//
echo "<hr /><h3>".get_vocab("title_begin_end_bookings")."</h3>";
?>
<table border='0'>
<tr><td><?php echo get_vocab("begin_bookings"); ?></td><td>
<?php
$bday = strftime("%d", getSettingValue("begin_bookings"));
$bmonth = strftime("%m", getSettingValue("begin_bookings"));
$byear = strftime("%Y", getSettingValue("begin_bookings"));
genDateSelector("begin_", $bday, $bmonth, $byear,"more_years") ?>
</td>
<td>&nbsp;</td>
</tr>
</table>
<?php echo "<i>".get_vocab("begin_bookings_explain")."</i>";

?>
<br /><br />
<table border='0'>
<tr><td><?php echo get_vocab("end_bookings"); ?></td><td>
<?php
$eday = strftime("%d", getSettingValue("end_bookings"));
$emonth = strftime("%m", getSettingValue("end_bookings"));
$eyear= strftime("%Y", getSettingValue("end_bookings"));
genDateSelector("end_",$eday,$emonth,$eyear,"more_years") ?>
</td>
</tr>
</table>
<?php echo "<i>".get_vocab("end_bookings_explain")."</i>";
//
// Configuration de l'affichage par défaut
//****************************************
//
?>
<hr />
<?php echo "<h3>".get_vocab("default_parameter_values_title")."</h3>";
echo "<p>".get_vocab("explain_default_parameter")."</p>";
//
// Choix du type d'affichage
//
echo "<h4>".get_vocab("explain_area_list_format")."</h4>";
echo "<table><tr><td>".get_vocab("liste_area_list_format")."</td><td>";
echo "<input type='radio' name='area_list_format' value='list' "; if (getSettingValue("area_list_format")=='list') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("select_area_list_format")."</td><td>";
echo "<input type='radio' name='area_list_format' value='select' "; if (getSettingValue("area_list_format")=='select') echo "checked"; echo " />";
echo "</td></tr></table>";

//
// Choix du domaine et de la ressource
// http://www.phpinfo.net/articles/article_listes.html
//
?>

<SCRIPT  type="text/javascript" LANGUAGE="JavaScript">
function ModifierListe(code_item) {
   lg = document.nom_formulaire.id_room.length;
   // On vide la liste
   for (i = lg - 1; i >= 0; i--) {
     document.nom_formulaire.id_room.options[i] = null;
   }
   code_rub = document.nom_formulaire.id_area.selectedIndex;
   <?php

   // Génération des Items par Rubriques

   // Cas où aucun domaine n'a été précisé
   echo "  if (document.nom_formulaire.id_area.options[code_rub].value == -1) {\n";
   echo "    document.nom_formulaire.id_room.length = 1;\n";
   echo "    document.nom_formulaire.id_room.options[0].value = -1;\n";
   echo "    document.nom_formulaire.id_room.options[0].text  = \"------\";\n";
   echo "    if (code_item == -1) document.nom_formulaire.id_room.options[0].selected = true;\n";
   echo "  }\n";
   // Cas où un domaine a été précisé
   $sql = "SELECT id FROM grr_area ORDER BY  order_display, area_name";
   $resultat = grr_sql_query($sql);
   $max_lignes = 0;
   $option_max = '';

   for ($enr = 0; ($row = grr_sql_row($resultat, $enr)); $enr++) {
     $sql  = "SELECT id, room_name ";
     $sql .= "FROM grr_room ";
     $sql .= "WHERE area_id='".$row[0]."'";
     $sql .= "ORDER BY room_name";
     $resultat2 = grr_sql_query($sql);
     echo "  if (document.nom_formulaire.id_area.options[code_rub].value == ".$row[0].") {\n";
     echo "    document.nom_formulaire.id_room.length = ".(grr_sql_count($resultat2)+4).";\n";
     $cpt = 0;
     echo "    document.nom_formulaire.id_room.options[0].value = -1;\n";
     echo "    document.nom_formulaire.id_room.options[0].text  = \"".get_vocab("default_room_all")."\";\n";
     echo "    if (code_item == -1) document.nom_formulaire.id_room.options[0].selected = true;\n";
     echo "    document.nom_formulaire.id_room.options[1].value = -2;\n";
     echo "    document.nom_formulaire.id_room.options[1].text  = \"".get_vocab("default_room_week_all")."\";\n";
     echo "    if (code_item == -2) document.nom_formulaire.id_room.options[1].selected = true;\n";
     echo "    document.nom_formulaire.id_room.options[2].value = -3;\n";
     echo "    document.nom_formulaire.id_room.options[2].text  = \"".get_vocab("default_room_month_all")."\";\n";
     echo "    if (code_item == -3) document.nom_formulaire.id_room.options[2].selected = true;\n";
     echo "    document.nom_formulaire.id_room.options[3].value = -4;\n";
     echo "    document.nom_formulaire.id_room.options[3].text  = \"".get_vocab("default_room_month_all_bis")."\";\n";
     echo "    if (code_item == -4) document.nom_formulaire.id_room.options[3].selected = true;\n";
     $cpt++;
     $cpt++;
     $cpt++;
     $cpt++;
     for ($enr2 = 0; ($row2 = grr_sql_row($resultat2, $enr2)); $enr2++) {
       echo "    document.nom_formulaire.id_room.options[".$cpt."].value = ".$row2[0].";\n";
       echo "    document.nom_formulaire.id_room.options[".$cpt."].text  = \"".ereg_replace('"','\"', $row2[1])." ".get_vocab("display_week")."\";\n";
       echo "    if (code_item == ".$row2[0].") document.nom_formulaire.id_room.options[".$cpt."].selected = true;\n";
       $cpt++;
       if ($cpt > $max_lignes) $max_lignes = $cpt;
       if (strlen($row2[1]) > strlen($option_max)) $option_max = $row2[1];
     }
     echo "  }\n";
   }
   ?>
}
</SCRIPT>

<?php
// ----------------------------------------------------------------------------
// Liste domaines
// ----------------------------------------------------------------------------

$sql = "SELECT id, area_name, access FROM grr_area ORDER BY  order_display, area_name";
$resultat = grr_sql_query($sql);

echo "<h4>".get_vocab("explain_default_area_and_room")."</h4>";
echo "<table><tr><td>".get_vocab("default_area")."</td><td>";
echo "<SELECT NAME='id_area' onChange='ModifierListe(-1)'>\n";
echo "<OPTION VALUE='-1'>".get_vocab("choose_an_area")."</OPTION>\n";

for ($enr = 0; ($row = grr_sql_row($resultat, $enr)); $enr++) {
  echo "<OPTION VALUE='".$row[0]."'";
  if (getSettingValue("default_area") == $row[0]) echo " SELECTED";
  echo ">".htmlspecialchars($row[1]);
  if ($row[2]=='r') echo " (".get_vocab("restricted").")";
  echo "</OPTION>\n";
}
echo "</SELECT></td></tr>\n";

// ----------------------------------------------------------------------------
// Liste ressources
// ----------------------------------------------------------------------------
echo "<tr><td>".get_vocab("default_room")."</td><td>";
echo "<SELECT NAME='id_room'>\n";
for ($cpt = 0; $cpt < $max_lignes; $cpt++) {
  echo "<OPTION>".$cpt.ereg_replace(".", "--", $option_max)."</OPTION>\n";
}
echo "</SELECT></td></tr></table>\n";
if (getSettingValue("default_room")) {
    $id_room=getSettingValue("default_room");
} else {
    $id_room = -1;
}
echo "<SCRIPT type='text/javascript' LANGUAGE='JavaScript'>\n;ModifierListe(".$id_room.");\n</SCRIPT>\n";
// ----------------------------------------------------------------------------

//
// Choix de la feuille de style
//
echo "<h4>".get_vocab("explain_css")."</h4>";
echo "<table><tr><td>".get_vocab("choose_css")."</td><td>";
echo "<SELECT NAME='default_css'>\n";
$i=0;
while ($i < count($liste_themes)) {
   echo "<OPTION VALUE='".$liste_themes[$i]."'";
   if (getSettingValue("default_css") == $liste_themes[$i]) echo " SELECTED";
   echo " >".encode_message_utf8($liste_name_themes[$i])."</OPTION>";
   $i++;
}
echo "</SELECT></td></tr></table>\n";

//
// Choix de la langue
//
echo "<h4>".get_vocab("choose_language")."</h4>";
echo "<table><tr><td>".get_vocab("choose_css")."</td><td>";
echo "<SELECT NAME='default_language'>\n";
$i=0;
while ($i < count($liste_language)) {
   echo "<OPTION VALUE='".$liste_language[$i]."'";
   if (getSettingValue("default_language") == $liste_language[$i]) echo " SELECTED";
   echo " >".encode_message_utf8($liste_name_language[$i])."</OPTION>\n";
   $i++;
}
echo "</SELECT></td></tr></table>\n";

#
# Affichage du contenu des "info-bulles" des réservations, dans les vues journées, semaine et mois.
# display_info_bulle = 0 : pas d'info-bulle.
# display_info_bulle = 1 : affichage des noms et prénoms du bénéficiaire de la réservation.
# display_info_bulle = 2 : affichage de la description complète de la réservation.
echo "<hr /><h3>".get_vocab("display_info_bulle_msg")."</h3>";
echo "<table>";
echo "<tr><td>".get_vocab("info-bulle0")."</td><td>";
echo "<input type='radio' name='display_info_bulle' value='0' "; if (getSettingValue("display_info_bulle")=='0') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("info-bulle1")."</td><td>";
echo "<input type='radio' name='display_info_bulle' value='1' "; if (getSettingValue("display_info_bulle")=='1') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("info-bulle2")."</td><td>";
echo "<input type='radio' name='display_info_bulle' value='2' "; if (getSettingValue("display_info_bulle")=='2') echo "checked"; echo " />";
echo "</td></tr>";
echo "</table>";

# Afficher la description complète de la réservation dans les vues semaine et mois.
# display_full_description=1 : la description complète s'affiche.
# display_full_description=0 : la description complète ne s'affiche pas.
echo "<hr /><h3>".get_vocab("display_full_description_msg")."</h3>";
echo "<table>";
echo "<tr><td>".get_vocab("display_full_description0")."</td><td>";
echo "<input type='radio' name='display_full_description' value='0' "; if (getSettingValue("display_full_description")=='0') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("display_full_description1")."</td><td>";
echo "<input type='radio' name='display_full_description' value='1' "; if (getSettingValue("display_full_description")=='1') echo "checked"; echo " />";
echo "</td></tr>";
echo "</table>";

# Afficher la description courte de la réservation dans les vues semaine et mois.
# display_short_description=1 : la description  s'affiche.
# display_short_description=0 : la description  ne s'affiche pas.
echo "<hr /><h3>".get_vocab("display_short_description_msg")."</h3>";
echo "<table>";
echo "<tr><td>".get_vocab("display_short_description0")."</td><td>";
echo "<input type='radio' name='display_short_description' value='0' "; if (getSettingValue("display_short_description")=='0') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("display_short_description1")."</td><td>";
echo "<input type='radio' name='display_short_description' value='1' "; if (getSettingValue("display_short_description")=='1') echo "checked"; echo " />";
echo "</td></tr>";
echo "</table>";

# Remplissage de la description courte
echo "<hr /><h3>".get_vocab("remplissage_description_breve_msg")."</h3>";
echo "<table>";
echo "<tr><td>".get_vocab("remplissage_description_breve0")."</td><td>";
echo "<input type='radio' name='remplissage_description_breve' value='0' "; if (getSettingValue("remplissage_description_breve")=='0') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("remplissage_description_breve1")."</td><td>";
echo "<input type='radio' name='remplissage_description_breve' value='1' "; if (getSettingValue("remplissage_description_breve")=='1') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("remplissage_description_breve2")."</td><td>";
echo "<input type='radio' name='remplissage_description_breve' value='2' "; if (getSettingValue("remplissage_description_breve")=='2') echo "checked"; echo " />";
echo "</td></tr>";
echo "</table>";

# Ouvrir les pages au format imprimable dans une nouvelle fenêtre du navigateur (0 pour non et 1 pour oui)
echo "<hr /><h3>".get_vocab("pview_new_windows_msg")."</h3>";
echo "<table>";
echo "<tr><td>".get_vocab("pview_new_windows0")."</td><td>";
echo "<input type='radio' name='pview_new_windows' value='0' "; if (getSettingValue("pview_new_windows")=='0') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("pview_new_windows1")."</td><td>";
echo "<input type='radio' name='pview_new_windows' value='1' "; if (getSettingValue("pview_new_windows")=='1') echo "checked"; echo " />";
echo "</td></tr>";
echo "</table>";

# Gestion du lien aide
echo "<hr /><h3>".get_vocab("Gestion lien aide bandeau superieur")."</h3>";
echo "<table>";
echo "<tr><td>".get_vocab("lien aide pointe vers documentation officielle site GRR")."</td><td>";
echo "<input type='radio' name='gestion_lien_aide' value='ext' "; if (getSettingValue("gestion_lien_aide")=='ext') echo "checked"; echo " />";
echo "</td></tr>";
echo "<tr><td>".get_vocab("lien aide pointe vers adresse perso").get_vocab("deux_points")."</td><td>";
echo "<input type='radio' name='gestion_lien_aide' value='perso' "; if (getSettingValue("gestion_lien_aide")=='perso') echo "checked"; echo " />";
echo "<input type=\"text\" name=\"lien_aide\" value=\"".getSettingValue("lien_aide")."\" size=\"40\" />";
echo "</td></tr>";
echo "</table>";
# Lors de l'édition d'un rapport, valeur par défaut en nombre de jours
# de l'intervalle de temps entre la date de début du rapport et la date de fin du rapport.
echo "<hr /><h3>".get_vocab("default_report_days_msg")."</h3>";
echo get_vocab("default_report_days_explain").get_vocab("deux_points")."<input type=\"text\" name=\"default_report_days\" value=\"".getSettingValue("default_report_days")."\" size=\"5\" />";

/*
# nb_year_calendar permet de fixer la plage de choix de l'année dans le choix des dates de début et fin des réservations
# La plage s'étend de année_en_cours - $nb_year_calendar à année_en_cours + $nb_year_calendar
# Par exemple, si on fixe $nb_year_calendar = 5 et que l'on est en 2005, la plage de choix de l'année s'étendra de 2000 à 2010
echo "<hr /><h3>".get_vocab("nb_year_calendar_msg")."</h3>";
echo get_vocab("nb_year_calendar_explain").get_vocab("deux_points");
echo "<select name=\"nb_year_calendar\" size=\"1\">\n";
$i = 1;
while ($i < 101) {
    echo "<option value=\".$i.\"";
    if (getSettingValue("nb_year_calendar") == $i) echo " selected ";
    echo ">".(date("Y") - $i)." - ".(date("Y") + $i)."</option>\n";
    $i++;
}
echo "</select>\n";
*/

echo "<br /><br /><center><div id=\"fixe\"><input type=\"submit\" name=\"ok\" value=\"".get_vocab("save")."\" style=\"font-variant: small-caps;\"/></div></center>";
echo "</FORM>";
?>
<script type="text/javascript" language="JavaScript">
document.nom_formulaire.title_home_page.focus();
</script>
<?php
	

// fin de l'affichage de la colonne de droite
echo "</td></tr></table>";
?>