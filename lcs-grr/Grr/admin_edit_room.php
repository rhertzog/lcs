<?php
#########################################################################
#                         admin_edit_room                               #
#                                                                       #
#                       Interface de création/modification              #
#                     des domaines et des ressources                    #
#                                                                       #
#                  Dernière modification : 28/03/2008                   #
#                                                                       #
#########################################################################
/*
 * Copyright 2003-2006 Laurent Delineau, Mathieu Ignacio
 * D'après http://mrbs.sourceforge.net/
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
include "include/admin.inc.php";
$grr_script_name = "admin_edit_room.php";
$ok = NULL;
$add_area = isset($_POST["add_area"]) ? $_POST["add_area"] : (isset($_GET["add_area"]) ? $_GET["add_area"] : NULL);
$area_id = isset($_POST["area_id"]) ? $_POST["area_id"] : (isset($_GET["area_id"]) ? $_GET["area_id"] : NULL);
$retour_page = isset($_POST["retour_page"]) ? $_POST["retour_page"] : (isset($_GET["retour_page"]) ? $_GET["retour_page"] : NULL);
$room = isset($_POST["room"]) ? $_POST["room"] : (isset($_GET["room"]) ? $_GET["room"] : NULL);
$area = isset($_POST["area"]) ? $_POST["area"] : (isset($_GET["area"]) ? $_GET["area"] : NULL);
$change_area = isset($_POST["change_area"]) ? $_POST["change_area"] : NULL;
$area_name = isset($_POST["area_name"]) ? $_POST["area_name"] : NULL;
$access = isset($_POST["access"]) ? $_POST["access"] : NULL;
$ip_adr = isset($_POST["ip_adr"]) ? $_POST["ip_adr"] : NULL;
$room_name = isset($_POST["room_name"]) ? $_POST["room_name"] : NULL;
$description = isset($_POST["description"]) ? $_POST["description"] : NULL;
$capacity = isset($_POST["capacity"]) ? $_POST["capacity"] : NULL;
$duree_max_resa_area1  = isset($_POST["duree_max_resa_area1"]) ? $_POST["duree_max_resa_area1"] : NULL;
$duree_max_resa_area2  = isset($_POST["duree_max_resa_area2"]) ? $_POST["duree_max_resa_area2"] : NULL;
$delais_max_resa_room  = isset($_POST["delais_max_resa_room"]) ? $_POST["delais_max_resa_room"] : NULL;
$delais_min_resa_room  = isset($_POST["delais_min_resa_room"]) ? $_POST["delais_min_resa_room"] : NULL;
$delais_option_reservation  = isset($_POST["delais_option_reservation"]) ? $_POST["delais_option_reservation"] : NULL;
$allow_action_in_past  = isset($_POST["allow_action_in_past"]) ? $_POST["allow_action_in_past"] : NULL;
$dont_allow_modify  = isset($_POST["dont_allow_modify"]) ? $_POST["dont_allow_modify"] : NULL;
$qui_peut_reserver_pour  = isset($_POST["qui_peut_reserver_pour"]) ? $_POST["qui_peut_reserver_pour"] : NULL;
$max_booking = isset($_POST["max_booking"]) ? $_POST["max_booking"] : NULL;
$statut_room = isset($_POST["statut_room"]) ? "0" : "1";
$show_fic_room = isset($_POST["show_fic_room"]) ? "y" : "n";
if (isset($_POST["active_ressource_empruntee"])) {
    $active_ressource_empruntee = 'y';
} else {
    $active_ressource_empruntee = 'n';
    // toutes les réservations sont considérées comme restituée
    grr_sql_query("update grr_entry set statut_entry = '-' where room_id = '".$room."'");
}
$picture_room = isset($_POST["picture_room"]) ? $_POST["picture_room"] : NULL;
$comment_room = isset($_POST["comment_room"]) ? $_POST["comment_room"] : NULL;
$change_done = isset($_POST["change_done"]) ? $_POST["change_done"] : NULL;
$area_order = isset($_POST["area_order"]) ? $_POST["area_order"] : NULL;
$room_order = isset($_POST["room_order"]) ? $_POST["room_order"] : NULL;
$change_room = isset($_POST["change_room"]) ? $_POST["change_room"] : NULL;
$number_periodes = isset($_POST["number_periodes"]) ? $_POST["number_periodes"] : NULL;
$type_affichage_reser = isset($_POST["type_affichage_reser"]) ? $_POST["type_affichage_reser"] : NULL;
$retour_resa_obli = isset($_POST["retour_resa_obli"]) ? $_POST["retour_resa_obli"] : NULL;
$moderate = isset($_POST['moderate']) ? $_POST["moderate"] : NULL;
if ($moderate == 'on') $moderate = 1;
else $moderate = 0;
settype($type_affichage_reser,"integer");

$back = '';
if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);

if (isset($_POST["change_room_and_back"])) {
    $change_room = "yes";
    $change_done = "yes";
}

if (isset($_POST["change_area_and_back"])) {
    $change_area = "yes";
    $change_done = "yes";
}


// mémorisation du chemin de retour
if (!isset($retour_page)) {
    $retour_page = $back;
    // on nettoie la chaine :
    $long_chaine_a_supprimer = strlen(strstr($retour_page,"&amp;msg=")); // longueur de la chaine à partir de la première occurence de &amp;msg=
    $long = strlen($retour_page) - $long_chaine_a_supprimer;
    $retour_page = substr($retour_page,0,$long);
}
$day   = date("d");
$month = date("m");
$year  = date("Y");

// modification d'une resource : admin ou gestionnaire
if (authGetUserLevel(getUserName(),-1) < 5)
{
    if (isset($room))
      {
        // Il s'agit d'une modif de ressource
        if ((authGetUserLevel(getUserName(),$room) < 3)) {
            showAccessDenied($day, $month, $year, $area,$back);
            exit();
        }
    } else {
        if (isset($area_id)) {
            // On vérifie que le domaine $area_id existe
            $test = grr_sql_query1("select id from grr_area where id='".$area_id."'");
            if ($test == -1) {
                showAccessDenied($day, $month, $year, $area,$back);
                exit();
            }
            // Il s'agit de l'ajout d'une ressource
            // On vérifie que l'utilisateur a le droit d'ajouter des ressources
            if ((authGetUserLevel(getUserName(),$area_id,'area') < 4)) {
                showAccessDenied($day, $month, $year, $area,$back);
               exit();
            }
        } else if (isset($area)) {
            // On vérifie que le domaine $area existe
            $test = grr_sql_query1("select id from grr_area where id='".$area."'");
            if ($test == -1) {
                showAccessDenied($day, $month, $year, $area,$back);
                exit();
            }
            // Il s'agit de la modif d'un domaine
            if ((authGetUserLevel(getUserName(),$area,'area') < 4)) {
                showAccessDenied($day, $month, $year, $area,$back);
               exit();
            }
        }
    }
}

$msg ='';

// Gestion des ressources
if ((!empty($room)) or (isset($area_id))) {

    // Enregistrement d'une ressource
    if (isset($change_room))
    {
        if (isset($_POST['sup_img'])) {
            $dest = './images/';
            $ok1 = false;
            if ($f = @fopen("$dest/.test", "w")) {
                @fputs($f, '<'.'?php $ok1 = true; ?'.'>');
                @fclose($f);
                include("$dest/.test");
            }
            if (!$ok1) {
                $msg .= "L\'image n\'a pas pu être supprimée : problème d\'écriture sur le répertoire. Veuillez signaler ce problème à l\'administrateur du serveur.\\n";
                $ok = 'no';
            } else {
                if (@file_exists($dest."img_".$room.".jpg")) unlink($dest."img_".$room.".jpg");
                if (@file_exists($dest."img_".$room.".png")) unlink($dest."img_".$room.".png");
                if (@file_exists($dest."img_".$room.".gif")) unlink($dest."img_".$room.".gif");

                $picture_room = "";
            }
        }
        if ($room_name=='') $room_name = "...";
        if (empty($capacity)) $capacity = 0;
        if ($capacity<0) $capacity = 0;
        settype($delais_max_resa_room,"integer");
        if ($delais_max_resa_room<0) $delais_max_resa_room = -1;
        settype($delais_min_resa_room,"integer");
        if ($delais_min_resa_room<0) $delais_min_resa_room = 0;
        settype($delais_option_reservation,"integer");
        if ($delais_option_reservation<0) $delais_option_reservation = 0;
        if ($allow_action_in_past == '') $allow_action_in_past = 'n';
        if ($dont_allow_modify == '') $dont_allow_modify = 'n';
        if ($max_booking=='') $max_booking = -1;
        if ($max_booking<-1) $max_booking = -1;
        if (isset($room)) {
            $sql = "UPDATE grr_room SET
            room_name='".protect_data_sql($room_name)."',
            description='".protect_data_sql($description)."', ";
            if ($picture_room != '') $sql .= "picture_room='".protect_data_sql($picture_room)."', ";
            $sql .= "comment_room='".protect_data_sql(corriger_caracteres($comment_room))."',
            show_fic_room='".$show_fic_room."',
            active_ressource_empruntee = '".$active_ressource_empruntee."',
            capacity='".$capacity."',
            delais_max_resa_room='".$delais_max_resa_room."',
            delais_min_resa_room='".$delais_min_resa_room."',
            delais_option_reservation='".$delais_option_reservation."',
            allow_action_in_past='".$allow_action_in_past."',
            dont_allow_modify='".$dont_allow_modify."',
            qui_peut_reserver_pour = '".$qui_peut_reserver_pour."',
            order_display='".protect_data_sql($area_order)."',
            type_affichage_reser='".$type_affichage_reser."',
            max_booking='".$max_booking."',
            moderate='".$moderate."',
            statut_room='".$statut_room."'
            WHERE id=$room";
            if (grr_sql_command($sql) < 0) {
                fatal_error(0, get_vocab('update_room_failed') . grr_sql_error());
                $ok = 'no';
            }
        } else {
            $sql = "insert into grr_room
            SET room_name='".protect_data_sql($room_name)."',
            area_id='".$area_id."',
            description='".protect_data_sql($description)."',
            picture_room='".protect_data_sql($picture_room)."',
            comment_room='".protect_data_sql(corriger_caracteres($comment_room))."',
            show_fic_room='".$show_fic_room."',
            active_ressource_empruntee = '".$active_ressource_empruntee."',
            capacity='".$capacity."',
            delais_max_resa_room='".$delais_max_resa_room."',
            delais_min_resa_room='".$delais_min_resa_room."',
            delais_option_reservation='".$delais_option_reservation."',
            allow_action_in_past='".$allow_action_in_past."',
            dont_allow_modify='".$dont_allow_modify."',
            qui_peut_reserver_pour = '".$qui_peut_reserver_pour."',
            order_display='".protect_data_sql($area_order)."',
            type_affichage_reser='".$type_affichage_reser."',
            max_booking='".$max_booking."',
            moderate='".$moderate."',
            statut_room='".$statut_room."'";
            if (grr_sql_command($sql) < 0) fatal_error(1, "<p>" . grr_sql_error());
            $room = mysql_insert_id();
        }
        $doc_file = isset($_FILES["doc_file"]) ? $_FILES["doc_file"] : NULL;
        if (ereg("\.([^.]+)$", $doc_file['name'], $match)) {
            $ext = strtolower($match[1]);
            if ($ext!='jpg' and $ext!='png'and $ext!='gif') {
                $msg .= "L\'image n\'a pas pu être enregistrée : les seules extentions autorisées sont gif, png et jpg.\\n";
                $ok = 'no';
            } else {
                $dest = './images/';
                $ok1 = false;
                if ($f = @fopen("$dest/.test", "w")) {
                    @fputs($f, '<'.'?php $ok1 = true; ?'.'>');
                    @fclose($f);
                    include("$dest/.test");
                }
                if (!$ok1) {
                    $msg .= "L\'image n\'a pas pu être enregistrée : problème d\'écriture sur le répertoire IMAGES. Veuillez signaler ce problème à l\'administrateur du serveur.\\n";
                    $ok = 'no';
                } else {
                    $old = getSettingValue("logo_etab");
                    $ok1 = @copy($doc_file['tmp_name'], $dest.$doc_file['name']);
                    if (!$ok1) $ok1 = @move_uploaded_file($doc_file['tmp_name'], $dest.$doc_file['name']);
                    if (!$ok1) {
                        $msg .= "L\'image n\'a pas pu être enregistrée : problème de transfert. Le fichier n\'a pas pu être transféré sur le répertoire IMAGES. Veuillez signaler ce problème à l\'administrateur du serveur.\\n";
                        $ok = 'no';
                    } else {
                        $tab = explode(".", $doc_file['name']);
                        $ext = strtolower($tab[1]);

                        if (@file_exists($dest."img_".$room.".".$ext)) @unlink($dest."img_".$room.".".$ext);
                        rename($dest.$doc_file['name'],$dest."img_".$room.".".$ext);
                        @chmod($dest."img_".$room.".".$ext, 0666);
                        $picture_room = "img_".$room.".".$ext;
                        $sql_picture = "UPDATE grr_room SET picture_room='".protect_data_sql($picture_room)."' WHERE id=".$room;
                        if (grr_sql_command($sql_picture) < 0) {
                            fatal_error(0, get_vocab('update_room_failed') . grr_sql_error());
                            $ok = 'no';
                        }
                    }
               }
           }
        } else if ($doc_file['name'] != '') {
           $msg .= "L\'image n\'a pas pu être enregistrée : le fichier image sélectionné n'est pas valide !\\n";
           $ok = 'no';
        }
        $msg .= get_vocab("message_records");

    }
    // Si pas de problème, retour à la page d'accueil après enregistrement
    if ((isset($change_done)) and (!isset($ok))) {
        if ($msg != '') {
            $_SESSION['displ_msg'] = 'yes';
            if (strpos($retour_page, ".php?") == "") $param = "?msg=".$msg; else $param = "&msg=".$msg;
        } else
            $param = '';

        Header("Location: ".$retour_page.$param);
        exit();
    }

    # print the page header
    print_header("","","","",$type="with_session", $page="admin");
    affiche_pop_up($msg,"admin");

    echo "<script  type=\"text/javascript\" src=\"functions.js\" language=\"javascript\"></script>";

    // affichage du formulaire
    if (isset($room)) {
        // Il s'agit d'une modification d'une ressource
        $res = grr_sql_query("SELECT * FROM grr_room WHERE id=$room");
        if (! $res) fatal_error(0, get_vocab('error_room') . $room . get_vocab('not_found'));
        $row = grr_sql_row_keyed($res, 0);
        grr_sql_free($res);
        $temp = grr_sql_query1("select area_id from grr_room where id='".$room."'");
        $area_name = grr_sql_query1("select area_name from grr_area where id='".$temp."'");
        echo "<h2 ALIGN=center>".get_vocab("match_area").get_vocab('deux_points')." ".$area_name."<br />".get_vocab("editroom")."</h2>\n";
    } else {
        // Il s'agit de l'enregistrement d'une nouvelle ressource
        $row['picture_room'] = '';
        $row["id"] = '';
        $row["room_name"]= '';
        $row["description"] = '';
        $row['comment_room'] = '';
        $row["capacity"]   = '';
        $row["delais_max_resa_room"] = -1;
        $row["delais_min_resa_room"] = 0;
        $row["delais_option_reservation"] = 0;
        $row["allow_action_in_past"] = 'n';
        $row["dont_allow_modify"] = 'n';
        $row["qui_peut_reserver_pour"] = 5;
        $row["order_display"]  = 0;
        $row["type_affichage_reser"]  = 0;
        $row["max_booking"] = -1;
        $row['statut_room'] = '';
        $row['moderate'] = '';
        $row['show_fic_room'] = '';
        $row['active_ressource_empruntee'] = 'n';
        $area_name = grr_sql_query1("select area_name from grr_area where id='".$area_id."'");
        echo "<h2 ALIGN=center>".get_vocab("match_area").get_vocab('deux_points')." ".$area_name."<br />".get_vocab("addroom")."</h2>\n";

    }
    ?>
    <form enctype="multipart/form-data" action="admin_edit_room.php" method="post" name="main">

    <?php
    if ($row["id"] != '') echo "<input type=\"hidden\" name=\"room\" value=\"".$row["id"]."\" />\n";
    if (isset($retour_page)) echo "<input type=\"hidden\" name=\"retour_page\" value=\"".$retour_page."\" />\n";
    if (isset($area_id)) echo "<input type=\"hidden\" name=\"area_id\" value=\"".$area_id."\" />\n";
    ?>

    <?php
    $nom_picture = '';
    if ($row['picture_room'] != '') $nom_picture = "./images/".$row['picture_room'];

    if (getSettingValue("use_fckeditor") == 1) {
        // lancement de FCKeditor
        include("./fckeditor/fckeditor.php") ;
        $oFCKeditor = new FCKeditor('comment_room') ;
        $oFCKeditor->BasePath = './fckeditor/' ;
        $oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
        $oFCKeditor->Height = '300' ;
        $oFCKeditor->Config['CustomConfigurationsPath'] = '../fckconfig_grr.js';
        $oFCKeditor->ToolbarSet = 'Basic_Grr';
        $oFCKeditor->Value = $row['comment_room'] ;
    }
    echo "<TABLE border=\"0\" cellspacing=\"0\" cellpadding=\"6\">\n";
    echo "<TR><TD width=\"30%\">".get_vocab("name").get_vocab("deux_points")."</TD><TD>\n";
    // seul l'administrateur peut modifier le nom de la ressource
    if ((authGetUserLevel(getUserName(),$area_id,"area") >=4) or (authGetUserLevel(getUserName(),$room) >=4)) {
        echo "<input type=\"text\" name=\"room_name\" size=\"50\" value=\"".htmlspecialchars($row["room_name"])."\" />\n";
    } else {
        echo "<input type=\"hidden\" name=\"room_name\" value=\"".htmlspecialchars($row["room_name"])."\" />\n";
        echo "<b>".htmlspecialchars($row["room_name"])."</b>\n";
    }
    echo "</TD></TR>\n";
    // Description
    echo "<TR><TD>".get_vocab("description")."</TD><TD><input type=\"text\" name=\"description\"  size=\"50\" value=\"".htmlspecialchars($row["description"])."\" /></TD></TR>\n";
    // Description complète
    echo "<TR><TD colspan=\"2\">".get_vocab("description complete");
    if (getSettingValue("use_fckeditor") != 1)
        echo " ".get_vocab("description complete2");
    echo get_vocab("deux_points")."</TD></tr><tr><TD colspan=\"2\">";
    if (getSettingValue("use_fckeditor") == 1) {
        $oFCKeditor->Create() ;
    } else {
        echo "<textarea name=\"comment_room\" rows=\"8\" cols=\"120\" >".$row['comment_room']."</textarea>";
    }
    echo "</TD></TR>\n";
    // Ordre d'affichage du domaine
    echo "<tr><TD>".get_vocab("order_display").get_vocab("deux_points")."</TD>\n";
    echo "<TD><input type=text name=\"area_order\" value=\"".htmlspecialchars($row["order_display"])."\" /></TD>\n";
    echo "</TR>\n";

    // Type d'affichage : durée ou heure/date de fin de réservation
    echo "<tr><TD>".get_vocab("type_affichage_reservation").get_vocab("deux_points")."</TD>\n";
    echo "<TD>";
    echo "<input type=\"radio\" name=\"type_affichage_reser\" value=\"0\" ";
    if (($row["type_affichage_reser"]) == 0) echo " checked ";
    echo "/>";
    echo get_vocab("affichage_reservation_duree");
    echo "<br /><input type=\"radio\" name=\"type_affichage_reser\" value=\"1\" ";
    if (($row["type_affichage_reser"]) == 1) echo " checked ";
    echo "/>";
    echo get_vocab("affichage_reservation_date_heure");
    echo "</TD>\n";
    echo "</TR>\n";
    echo "</table>\n<TABLE border=\"1\" cellspacing=\"0\" cellpadding=\"6\">\n";

    // Capacité
    echo "<TR><TD>".get_vocab("capacity").": </TD><TD><input type=text name=capacity value=\"".$row["capacity"]."\" /></TD></TR>\n";
    // seul les administrateurs de la ressource peuvent modifier le nombre max de réservation par utilisateur
    if ((authGetUserLevel(getUserName(),$area_id,"area") >=4) or (authGetUserLevel(getUserName(),$room) >=4)) {
        echo "<TR><TD>".get_vocab("max_booking")." ";
        echo grr_help("aide_grr_max_reservation");
        echo "</TD><TD><input type=\"text\" name=\"max_booking\" value=\"".$row["max_booking"]."\" /></TD></TR>";

    } else if ($row["max_booking"] != "-1") {
        echo "<TR><TD>".get_vocab("msg_max_booking")."</TD><TD>
        <input type=\"hidden\" name=\"max_booking\" value=\"".$row["max_booking"]."\" />
        <b>".htmlspecialchars($row["max_booking"])."</b>
        </TD></TR>";
    }
    // L'utilisateur ne peut pas réserver au-delà d'un certain temps
    echo "<TR><TD>".get_vocab("delais_max_resa_room").": </TD><TD><input type=text name=delais_max_resa_room value=\"".$row["delais_max_resa_room"]."\" /></TD></TR>\n";
    // L'utilisateur ne peut pas réserver en-dessous d'un certain temps
    echo "<TR><TD>".get_vocab("delais_min_resa_room").": ";
    echo "</TD><TD><input type=text name=delais_min_resa_room value=\"".$row["delais_min_resa_room"]."\" /></TD></TR>\n";
    // L'utilisateur peut poser poser une option de réservation
    echo "<TR><TD>".get_vocab("msg_option_de_reservation")." ".grr_help("aide_grr_reservation_sous_reserve")."</TD>
    <TD><input type=text name=delais_option_reservation value=\"".$row["delais_option_reservation"]."\" /></TD></TR>\n";

    // Les demandes de réservations sont modérés
    echo "<tr><td>".get_vocab("msg_moderation_reservation").get_vocab("deux_points");
    echo grr_help("aide_grr_moderation");
    echo "</td>" .
      "<td><input type='checkbox' name='moderate' ";
    if ($row['moderate']) echo 'checked';
    echo " /></td></tr>\n";

    // L'utilisateur peut réserver dans le passé
    echo "<TR><TD>".get_vocab("allow_action_in_past")."<br /><i>".get_vocab("allow_action_in_past_explain")."</i></TD><TD><input type=\"checkbox\" name=\"allow_action_in_past\" value=\"y\" ";
    if ($row["allow_action_in_past"] == 'y') echo " checked";
    echo " /></TD></tr>\n";

    // L'utilisateur ne peut pas modifier ou supprimer ses propres réservations
    echo "<TR><TD>".get_vocab("dont_allow_modify")."</TD><TD><input type=\"checkbox\" name=\"dont_allow_modify\" value=\"y\" ";
    if ($row["dont_allow_modify"] == 'y') echo " checked";
    echo " /></TD></tr>\n";

    // Quels utilisateurs ont le droit de réserver cette ressource au nom d'un autre utilisateur ?
    echo "<TR><TD>".get_vocab("qui peut reserver pour autre utilisateur")."</TD><TD>
    <select name=\"qui_peut_reserver_pour\" size=\"1\">\n
    <option value=\"5\" ";
    if ($row["qui_peut_reserver_pour"]==5) echo " selected ";
    echo ">".get_vocab("personne")."</option>\n
    <option value=\"4\" ";
    if ($row["qui_peut_reserver_pour"]==4) echo " selected ";
    echo ">".get_vocab("les administrateurs restreints")."</option>\n
    <option value=\"3\" ";
    if ($row["qui_peut_reserver_pour"]==3) echo " selected ";
    echo ">".get_vocab("les gestionnaires de la ressource")."</option>\n
    <option value=\"2\" ";
    if ($row["qui_peut_reserver_pour"]==2) echo " selected ";
    echo ">".get_vocab("tous les utilisateurs")."</option>\n
    </select></TD></tr>\n";

    // Déclarer ressource indisponible
    echo "<TR><TD>".get_vocab("declarer_ressource_indisponible")."<br /><i>".get_vocab("explain_max_booking")."</i></TD>
    <TD><input type=\"checkbox\" name=\"statut_room\" ";
    if ($row['statut_room'] == "0") echo " checked ";
    echo "/></TD></TR>\n";
    // Activer la fonctionalité "ressource empruntée/restituée"
    echo "<tr><td>".get_vocab("activer fonctionalité ressource empruntee restituee").grr_help("aide_grr_ressource_empruntee")."</td>
    <td><input type=\"checkbox\" name=\"active_ressource_empruntee\" ";
    if ($row['active_ressource_empruntee'] == "y") echo " checked ";
    echo "/></td></tr>\n";
    // Afficher la fiche de présentation de la ressource
    echo "<TR><TD>".get_vocab("montrer_fiche_présentation_ressource")."</TD>
    <TD><input type=\"checkbox\" name=\"show_fic_room\" ";
    if ($row['show_fic_room'] == "y") echo " checked ";
    echo "/></TD></TR>\n";
    // Choix de l'image de la ressource
    echo "<TR><TD>".get_vocab("choisir_image_ressource")."</TD>
    <TD><INPUT TYPE=\"file\" name=\"doc_file\" /></TD></TR>\n";

    if (@file_exists($nom_picture)) {
    echo "<TR><TD>".get_vocab("supprimer_image_ressource")."</TD><TD><input type=\"checkbox\" name=\"sup_img\" /></TD></TR>";
    }
    echo "</TABLE><br />\n";
    echo "<center><TABLE><tr><td>\n";
    echo "<input type=\"submit\" name=\"change_room\"  value=\"".get_vocab("save")."\" />\n";
    echo "</td><td>\n";
    echo "<input type=\"submit\" name=\"change_done\" value=\"".get_vocab("back")."\" />";
    echo "</td><td>\n";
    echo "<input type=\"submit\" name=\"change_room_and_back\" value=\"".get_vocab("save_and_back")."\" />";
    echo "</td></tr></table>";
    if (@file_exists($nom_picture) && $nom_picture) {
        echo "<br /><br /><b>".get_vocab("Image de la ressource").get_vocab("deux_points")."</b><br /><IMG SRC=\"".$nom_picture."\" BORDER=0 ALT=\"logo\">";
    } else {
        echo "<br /><br /><b>".get_vocab("Pas image disponible")."</b>";
    }
    ?>
    </center>
    </form>
<?php

}
// Ajout ou modification d'un domaine
if ((!empty($area)) or (isset($add_area)))
{
  if (isset($change_area)) {
      // la valeur par défaut ne peut être infériure au plus petit bloc réservable
      if ($_POST['duree_par_defaut_reservation_area'] < $_POST['resolution_area']) $_POST['duree_par_defaut_reservation_area'] = $_POST['resolution_area'];
      // la valeur par défaut doit être un multiple du plus petit bloc réservable
      $_POST['duree_par_defaut_reservation_area']= intval($_POST['duree_par_defaut_reservation_area']/$_POST['resolution_area'])*$_POST['resolution_area'];
      // Durée maximale de réservation
      if (isset($_POST['enable_periods'])) {
        if ($_POST['enable_periods'] == 'y')
          $duree_max_resa_area = $duree_max_resa_area2*1440;
        else {
          $duree_max_resa_area = $duree_max_resa_area1;
          if ($duree_max_resa_area >= 0)
              $duree_max_resa_area = max ($duree_max_resa_area, $_POST['resolution_area']/60, $_POST['duree_par_defaut_reservation_area']/60);
        }
        settype($duree_max_resa_area,"integer");
        if ($duree_max_resa_area<0) $duree_max_resa_area = -1;
      }

      $display_days = "";
      for ($i = 0; $i < 7; $i++) {
          if (isset($_POST['display_day'][$i]))
              $display_days .= "y";
          else
              $display_days .= "n";
      }
      if ($display_days != "nnnnnnn") {
        while(!isset($_POST['display_day'][$_POST['weekstarts_area']])) {
          $_POST['weekstarts_area']++;
        }
      }
      if ($_POST['morningstarts_area'] > $_POST['eveningends_area'])
          $_POST['eveningends_area'] = $_POST['morningstarts_area'];
      if ($access) {$access='r';} else {$access='a';}
      if ($area_name == '') $area_name = "...";
      if (isset($area)) {
            // s'il y a changement de type de créneaux, on efface les réservations du domaines
            $old_enable_periods = grr_sql_query1("select enable_periods from grr_area WHERE id='".$area."'");
            if ($old_enable_periods != $_POST['enable_periods']) {
                $del = grr_sql_query("DELETE grr_entry FROM grr_entry, grr_room, grr_area WHERE
                grr_entry.room_id = grr_room.id and
                grr_room.area_id = grr_area.id and
                grr_area.id = '".$area."'");
                $del = grr_sql_query("DELETE grr_repeat FROM grr_repeat, grr_room, grr_area WHERE
                grr_repeat.room_id = grr_room.id and
                grr_room.area_id = grr_area.id and
                grr_area.id = '".$area."'");
            }

            $sql = "UPDATE grr_area SET
            area_name='".protect_data_sql($area_name)."',
            access='".protect_data_sql($access)."',
            order_display='".protect_data_sql($area_order)."',
            ip_adr='".protect_data_sql($ip_adr)."',
            calendar_default_values = 'n',
            duree_max_resa_area = '".protect_data_sql($duree_max_resa_area)."',
            morningstarts_area = '".protect_data_sql($_POST['morningstarts_area'])."',
            eveningends_area = '".protect_data_sql($_POST['eveningends_area'])."',
            resolution_area = '".protect_data_sql($_POST['resolution_area'])."',
            duree_par_defaut_reservation_area = '".protect_data_sql($_POST['duree_par_defaut_reservation_area'])."',
            eveningends_minutes_area = '".protect_data_sql($_POST['eveningends_minutes_area'])."',
            weekstarts_area = '".protect_data_sql($_POST['weekstarts_area'])."',
            enable_periods = '".protect_data_sql($_POST['enable_periods'])."',
            twentyfourhour_format_area = '".protect_data_sql($_POST['twentyfourhour_format_area'])."',
            display_days = '".$display_days."'
            WHERE id=$area";
            if (grr_sql_command($sql) < 0) {
                fatal_error(0, get_vocab('update_area_failed') . grr_sql_error());
                $ok = 'no';
            }
        } else {
            $sql = "INSERT INTO grr_area SET
            area_name='".protect_data_sql($area_name)."',
            access='".protect_data_sql($access)."',
            order_display='".protect_data_sql($area_order)."',
            ip_adr='".protect_data_sql($ip_adr)."',
            calendar_default_values = 'n',
            duree_max_resa_area = '".protect_data_sql($duree_max_resa_area)."',
            morningstarts_area = '".protect_data_sql($_POST['morningstarts_area'])."',
            eveningends_area = '".protect_data_sql($_POST['eveningends_area'])."',
            resolution_area = '".protect_data_sql($_POST['resolution_area'])."',
            duree_par_defaut_reservation_area = '".protect_data_sql($_POST['duree_par_defaut_reservation_area'])."',
            eveningends_minutes_area = '".protect_data_sql($_POST['eveningends_minutes_area'])."',
            weekstarts_area = '".protect_data_sql($_POST['weekstarts_area'])."',
            enable_periods = '".protect_data_sql($_POST['enable_periods'])."',
            twentyfourhour_format_area = '".protect_data_sql($_POST['twentyfourhour_format_area'])."',
            display_days = '".$display_days."',
            id_type_par_defaut = '-1'
            ";
            if (grr_sql_command($sql) < 0) fatal_error(1, "<p>" . grr_sql_error());
            $area = grr_sql_insert_id("grr_area", "id");
        }
        $msg = get_vocab("message_records");


      if (isset($number_periodes)) {
        settype($number_periodes,"integer");
        if ($number_periodes < 1) $number_periodes = 1;
        $del_periode = grr_sql_query("delete from grr_area_periodes where id_area='".$area."'");
        $i = 0;
        $num = 0;
        while ($i < $number_periodes) {
          $temp = "periode_".$i;
          if (isset($_POST[$temp])) {
              $nom_periode = corriger_caracteres($_POST[$temp]);
              if ($nom_periode != "") {
                    $reg_periode = grr_sql_query("insert into grr_area_periodes set
                    id_area='".$area."',
                    num_periode='".$num."',
                    nom_periode='".protect_data_sql($nom_periode)."'
                    ");
                    $num++;
              }
          }
          $i++;
        }
      }

    }

  if ($access=='a')
    {
        $sql = "DELETE FROM grr_j_user_area WHERE id_area='$area'";
        if (grr_sql_command($sql) < 0)
            fatal_error(0, get_vocab('update_area_failed') . grr_sql_error());
    }
    if ((isset($change_done)) and (!isset($ok))) {
        if ($msg != '') {
            $_SESSION['displ_msg'] = 'yes';
            if (strpos($retour_page, ".php?") == "") $param = "?msg=".$msg; else $param = "&msg=".$msg;
        } else
            $param = '';

        Header("Location: ".$retour_page.$param);
        exit();
    }
    # print the page header
    print_header("","","","",$type="with_session", $page="admin");
    affiche_pop_up($msg,"admin");
    $avertissement = get_vocab("avertissement_change_type");
    ?>
    <script  type="text/javascript" src="functions.js" language="javascript"></script>
    <SCRIPT type="text/javascript" LANGUAGE="JavaScript">
    function bascule()
    {
    //Booléen reconnaissant le navigateur
    isIE = (document.all)
    isNN6 = (!isIE) && (document.getElementById)
    //Compatibilité : l'objet menu est détecté selon le navigateur
    if (isIE) menu_1 = document.all['menu1'];
    if (isNN6) menu_1 = document.getElementById('menu1');
    if (isIE) menu_2 = document.all['menu2'];
    if (isNN6) menu_2 = document.getElementById('menu2');
    if (document.forms["main"].enable_periods[0].checked)
    {
        menu_1.style.display = "";
        menu_2.style.display = "none";
    }
    if (document.forms["main"].enable_periods[1].checked)
    {
        menu_1.style.display = "none";
        menu_2.style.display = "";
   }
   alert("<?php echo $avertissement; ?>");
    }

    </SCRIPT>


    <?php

    if (isset($area)) {
        $res = grr_sql_query("SELECT * FROM grr_area WHERE id=$area");
        if (! $res) fatal_error(0, get_vocab('error_area') . $area . get_vocab('not_found'));
        $row = grr_sql_row_keyed($res, 0);
        grr_sql_free($res);
        echo "<h2 ALIGN=center>".get_vocab("editarea")."</h2>";
        if ($row["calendar_default_values"] == 'y') {
            $row["morningstarts_area"] = $morningstarts;
            $row["eveningends_area"] = $eveningends;
            $row["resolution_area"] = $resolution;
            $row["duree_par_defaut_reservation_area"] = $duree_par_defaut_reservation_area;
            $row["duree_max_resa_area"] = $duree_max_resa;
            $row["eveningends_minutes_area"] = $eveningends_minutes;
            $row["weekstarts_area"] = $weekstarts;
            $row["twentyfourhour_format_area"] = $twentyfourhour_format;
            $row["display_days"] = $display_days;
        }
        if ($row["enable_periods"] != 'y') $row["enable_periods"] = 'n';
  }
 else
   {
        $row["id"] = '';
        $row["area_name"] = '';
        $row["order_display"]  = '';
        $row["access"] = '';
        $row["ip_adr"] = '';
        $row["morningstarts_area"] = $morningstarts;
        $row["eveningends_area"] = $eveningends;
        $row["resolution_area"] = $resolution;
        $row["duree_par_defaut_reservation_area"] = $resolution;
        $row["duree_max_resa_area"] = $duree_max_resa;
        $row["eveningends_minutes_area"] = $eveningends_minutes;
        $row["weekstarts_area"] = $weekstarts;
        $row["twentyfourhour_format_area"] = $twentyfourhour_format;
        $row["enable_periods"] = 'n';
        $row["display_days"] = "yyyyyyy";
        echo "<h2 ALIGN=center>".get_vocab('addarea')."</h2>";
    }
    ?>
    <form action="admin_edit_room.php" method="post" name="main">
    <?php
    if (isset($retour_page)) echo "<input type=\"hidden\" name=\"retour_page\" value=\"".$retour_page."\" />";
    if ($row['id'] != '') echo "<input type=\"hidden\" name=\"area\" value=\"".$row["id"]."\" />";
    if (isset($add_area)) echo "<input type=\"hidden\" name=\"add_area\" value=\"".$add_area."\" />\n";

    echo "<center><TABLE border=1><TR>";
    // Nom du domaine
    echo "<TD>".get_vocab("name").get_vocab("deux_points")."</TD>\n";
    echo "<TD><input type=text name=\"area_name\" value=\"".htmlspecialchars($row["area_name"])."\" /></TD>\n";
    echo "</TR><TR>\n";
    // Ordre d'affichage du domaine
    echo "<TD>".get_vocab("order_display").get_vocab("deux_points")."</TD>\n";
    echo "<TD><input type=text name=\"area_order\" value=\"".htmlspecialchars($row["order_display"])."\" /></TD>\n";
    echo "</TR><TR>\n";
    // Accès restreint ou non ?
    echo "<TD>".get_vocab("access").get_vocab("deux_points")."</TD>\n";
    echo "<TD><input type=checkbox name=\"access\"";
    if ($row["access"] == 'r') echo "checked";
    echo " /></TD>\n";
    echo "</TR>";
    // Adresse IP client :
    if (OPTION_IP_ADR==1) {
        echo "<TR>\n";
        echo "<TD>".get_vocab("ip_adr").get_vocab("deux_points")."</TD>";
        echo "<TD><input type=text name=\"ip_adr\" value=\"".htmlspecialchars($row["ip_adr"])."\" /></TD>\n";
        echo "</TR>\n";
    }
    echo "</TABLE>";
    // Configuration des plages horaires ...
    echo "<H3>".get_vocab("configuration_plages_horaires").grr_help("Configuration_affichage","conf_planning")."</h3>";


    // Début de la semaine: 0 pour dimanche, 1 pou lundi, etc.
    echo "<TABLE border=1>";
    echo "<TR>\n";
    echo "<TD>".get_vocab("weekstarts_area").get_vocab("deux_points")."</TD>\n";
    echo "<TD><select name=\"weekstarts_area\" size=\"1\">\n";
    $k = 0;
    while ($k < 7) {
        $tmp=mktime(0,0,0,10,2+$k,2005);
        echo "<option value=\"".$k."\" ";
        if ($k == $row['weekstarts_area']) echo " selected";
        echo ">".utf8_strftime("%A", $tmp)."</option>\n";
        $k++;
    }
    echo "</select></TD>\n";
    echo "</TR></table>";

    // Définition des jours de la semaine à afficher sur les plannings et calendriers
    echo "<TABLE border=1>";
    echo "<TR>\n";
    echo "<TD colspan=\"7\">".get_vocab("cocher_jours_a_afficher")."</TD>\n</TR>\n";
    echo "<TR>\n";
    for ($i = 0; $i < 7; $i++)
    {
      echo "<TD><INPUT NAME=\"display_day[".$i."]\" TYPE=CHECKBOX";
      if (substr($row["display_days"],$i,1) == 'y') echo " CHECKED";
      echo " />" . day_name($i) . "</td>\n";
    }
	echo "</tr></table>";

    echo "<fieldset style=\"padding-top: 10px; padding-bottom: 10px; width: 80%; margin-left: auto; margin-right: auto;\">";
    echo "<legend style=\"font-variant: small-caps;\">".encode_message_utf8("Type de créneaux")."</legend>";
    //echo "<p style=\"text-align:left;\"><b>ATTENTION :</b> Les deux types de configuration des créneaux sont incompatibles entre eux : un changement du type de créneaux entraîne donc, après validation, un <b>effacement de toutes les réservations  de ce domaine</b></p>.";
    echo "<table border=\"0\">";
    echo "<tr><td><input type=\"radio\" name=\"enable_periods\" value=\"n\" onclick=\"bascule()\" ";
    if ($row["enable_periods"] == 'n') echo "checked";
    echo " /></td><td>".get_vocab("creneaux_de_reservation_temps")."</td></tr>";
    echo "<tr><td><input type=\"radio\" name=\"enable_periods\" value=\"y\" onclick=\"bascule()\" ";
    if ($row["enable_periods"] == 'y') echo "checked";
    echo " /></td><td>".get_vocab("creneaux_de_reservation_pre_definis")."</td></tr>";
    echo "</table>";

    //Les créneaux de réservation sont basés sur des intitulés pré-définis.
    $sql_periode = grr_sql_query("SELECT num_periode, nom_periode FROM grr_area_periodes where id_area='".$area."' order by num_periode");
    $num_periodes = grr_sql_count($sql_periode);
    if (!isset($number_periodes))
        if ($num_periodes == 0)
            $number_periodes = 10;
        else
            $number_periodes = $num_periodes;

    if ($row["enable_periods"] == 'y')
        echo "<TABLE border=\"1\" id=\"menu2\" cellpadding=\"5\">";
    else
        echo "<TABLE style=\"display:none\" border=\"1\" id=\"menu2\" cellpadding=\"5\">";
    echo "<TR><TD><i>".get_vocab("nombre_de_creneaux").get_vocab("deux_points")."</i></TD>";
    echo "<td><select name=\"number_periodes\" size=\"1\">\n";

    $j = 1;
    while ($j < 51) {
        echo "<option ";
        if ($j == $number_periodes) echo " selected ";
        echo ">".($j)."</option>\n";
        $j++;
    }
    echo "</select></td></TR>\n";
    $i = 0;
    while ($i < $number_periodes) {
        $nom_periode = grr_sql_query1("select nom_periode FROM grr_area_periodes where id_area='".$area."' and num_periode= '".$i."'");
        if ($nom_periode == -1) $nom_periode = "";
        echo "<TR><TD>".get_vocab("intitule_creneau").($i+1)."</TD>";
        echo "<td><input type=\"text\" name=\"periode_".$i."\" value=\"".htmlentities($nom_periode)."\" size=\"30\" /></td></TR>\n";
        $i++;
    }
        // L'utilisateur ne peut réserver qu'une durée limitée (-1 désactivée), exprimée en jours
    if ($row["duree_max_resa_area"] > 0)
        $nb_jour = max(round($row["duree_max_resa_area"]/1440,0),1);
    else
        $nb_jour = -1;
    echo "<TR><TD>".get_vocab("duree_max_resa_area2").": ";
    echo "</TD><TD><input type=text name=duree_max_resa_area2 value=\"".$nb_jour."\" /></TD>\n";
    echo "</TR>";

    echo "</table>";

    // Cas ou les créneaux de réservations sont basés sur le temps
    if ($row["enable_periods"] == 'n')
        echo "<TABLE border=\"1\" id=\"menu1\" cellpadding=\"5\">";
    else
        echo "<TABLE style=\"display:none\" border=\"1\" id=\"menu1\" cellpadding=\"5\">";
    // Heure de début de réservation
    echo "<TR>";
    echo "<TD>".get_vocab("morningstarts_area").get_vocab("deux_points")."</TD>\n";
    echo "<TD><select name=\"morningstarts_area\" size=\"1\">\n";
    $k = 0;
    while ($k < 24) {
        echo "<option value=\"".$k."\" ";
        if ($k == $row['morningstarts_area']) echo " selected";
        echo ">".$k."</option>\n";
        $k++;
    }
    echo "</select></TD>\n";
    echo "</TR>";

    // Heure de fin de réservation
    echo "<TR>\n";
    echo "<TD>".get_vocab("eveningends_area").get_vocab("deux_points")."</TD>\n";
    echo "<TD><select name=\"eveningends_area\" size=\"1\">\n";
    $k = 0;
    while ($k < 24) {
        echo "<option value=\"".$k."\" ";
        if ($k == $row['eveningends_area']) echo " selected";
        echo ">".$k."</option>\n";
        $k++;
    }
    echo "</select></TD>\n";
    echo "</TR>";

    // Minutes à ajouter à l'heure $eveningends pour avoir la fin réelle d'une journée.
    echo "<TR>\n";
    echo "<TD>".get_vocab("eveningends_minutes_area").get_vocab("deux_points")."</TD>\n";
    echo "<TD><input type=text name=\"eveningends_minutes_area\" value=\"".htmlspecialchars($row["eveningends_minutes_area"])."\" /></TD>\n";
    echo "</TR>";

    // Resolution - quel bloc peut être réservé, en secondes
    echo "<TR>\n";
    echo "<TD>".get_vocab("resolution_area").get_vocab("deux_points")."</TD>\n";
    echo "<TD><input type=text name=\"resolution_area\" value=\"".htmlspecialchars($row["resolution_area"])."\" /></TD>\n";
    echo "</TR><TR>\n";
    echo "</TR>";

    // Valeur par défaut de la durée d'une réservation
    echo "<TR>\n";
    echo "<TD>".get_vocab("duree_par_defaut_reservation_area").get_vocab("deux_points")."</TD>\n";
    echo "<TD><input type=text name=\"duree_par_defaut_reservation_area\" value=\"".htmlspecialchars($row["duree_par_defaut_reservation_area"])."\" /></TD>\n";
    echo "</TR><TR>\n";
    echo "</TR>";


    // Format d'affichage du temps : valeur 0 pour un affichage « 12 heures » et valeur 1 pour un affichage  « 24 heure ».
    echo "<TR>\n";
    echo "<TD>".get_vocab("twentyfourhour_format_area").get_vocab("deux_points")."</TD>\n";
    echo "<td><table><tr><td>";
    echo get_vocab("twentyfourhour_format_12")."</td><td><input type=\"radio\" name=\"twentyfourhour_format_area\" value=\"0\" ";
    if ($row['twentyfourhour_format_area'] == 0) echo " checked";
    echo " /></td></tr><tr><td>";
    echo get_vocab("twentyfourhour_format_24")."</td><td><input type=\"radio\" name=\"twentyfourhour_format_area\" value=\"1\" ";
    if ($row['twentyfourhour_format_area'] == 1) echo " checked";
    echo " /></td></tr></table>";
    echo "</td>";
    echo "</tr>";

    // L'utilisateur ne peut réserver qu'une durée limitée (-1 désactivée), exprimée en minutes
    echo "<TR><TD>".get_vocab("duree_max_resa_area").grr_help("Configuration_affichage","duree_max_reser").get_vocab("deux_points");
    echo "</TD><TD><input type=text name=duree_max_resa_area1 value=\"".$row["duree_max_resa_area"]."\" /></TD>\n";
    echo "</TR>";

    echo "</table>";
    echo "</fieldset>";

    echo "<TABLE><tr><td>\n";
    echo "<input type=\"submit\" name=\"change_area\"  value=\"".get_vocab("save")."\" />\n";
    echo "</td><td>\n";
    echo "<input type=\"submit\" name=\"change_done\" value=\"".get_vocab("back")."\" />";
    echo "</td><td>\n";
    echo "<input type=\"submit\" name=\"change_area_and_back\" value=\"".get_vocab("save_and_back")."\" />";
    echo "</td></tr></table>";
    echo "</center></form>";
    if (OPTION_IP_ADR==1) {
       echo "<br />".get_vocab("ip_adr_explain");
    }

 } ?>
</body>
</html>