<?php
//maj : 02/10/2015 
include "Includes/checking.php";
 if (! check_acces()) {exit;}
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
include "Includes/func_maint.inc.php";
include "Includes/config.inc.php";
include ("$BASEDIR/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");

    if (count($_GET)>0) {
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
        // Register Global GET
        $conf=$purifier->purify($_GET['conf']);
        $bat=$purifier->purify($_GET['bat']);
        $etage=$purifier->purify($_GET['etage']);
        $salle=$purifier->purify($_GET['salle']);
        $secteur=$purifier->purify($_GET['secteur']);
    }
    if (count($_POST)>0) {
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    // Register Global POST
    $MAILMAINTCONF=$purifier->purify($_POST['MAILMAINTCONF']);
    $mailconf=$purifier->purify($_POST['mailconf']);
    $SECTEURNEW=$purifier->purify($_POST['SECTEURNEW']);
    $SECTEUROLD=$purifier->purify($_POST['SECTEUROLD']);
    $secteurconf=$purifier->purify($_POST['secteurconf']);
    $saveconf=$purifier->purify($_POST['saveconf']);
    $action=$purifier->purify($_POST['action']);
    $txtfile=$purifier->purify($_FILES['txtfile']['tmp_name']);
    $sqlfile=$purifier->purify($_FILES['sqlfile']['name']);
    }

    // SAUVEGARDE BASE
    if ( $saveconf == "Sauvegarde" ) {
      system("mysqldump $DBAUTHMAINT -u $USERAUTH -p$PASSAUTH  > /tmp/$DBAUTHMAINT.sql");
      header("Content-Type: octet-stream");
      header("Content-Length: ".filesize ("/tmp/$DBAUTHMAINT.sql") );
      header("Content-Disposition: attachment; filename=\"/tmp/$DBAUTHMAINT.sql\"");
      include ("/tmp/$DBAUTHMAINT.sql");
    }

    html();
    $uid=  $_SESSION['login'];
    if ($uid == "") {
      // L'utilisateur n'est pas authentifie
      table_alert ("Vous devez pr&#233;alablement vous authentifier sur votre &#171;&#160;Espace perso  LCS&#160;&#187; pour acc&#233;der &#224; cette application !");
    } else {
        // L'utilisateur est authentifie
        list($user, $groups)=people_get_variables($uid, false);
        // Initialisation de la variable mnuchoice si elle vide
        if ( !isset($mnuchoice) ) $mnuchoice="wait";
        // Recherche si l'utilisateur authentifie a le droit Maint_is_admin et System_is_admin
        if ( is_admin("Maint_is_admin",$uid)=="Y" && is_admin("system_is_admin",$uid)=="Y" ) {
        # Recuperation des parametres Catégorie 1 de l'appli depuis la table params de la bdd
        # -----------------------------------------------------------------------
        $result = @mysql_query("SELECT * from params WHERE cat='1' OR cat='4'");
        if ($result)
        while ($r = @mysql_fetch_array($result))
        $$r["name"]=$r["value"];
        else   die ("paramètres absents de la base de données");
        @mysql_free_result($result);
        // Traitement des modifications de configuration
        // MAIL
        if ( $mailconf && $MAILMAINTCONF != $MAILMAINT ) {
          // modification de MAILMAINT
          $query="UPDATE params SET value=\"$MAILMAINTCONF\" WHERE name=\"MAILMAINT\"";
          $result=@mysql_query($query);
          // Relecture de la valeur
          $result = @mysql_query("SELECT * from params WHERE cat='1'");
          if ($result)
            while ($r = @mysql_fetch_array($result))
              $$r["name"]=$r["value"];
        }
        // SECTEUR
        if ( $secteurconf ) {
          if ( $action == "Modifier" && $SECTEURNEW != $SECTEUROLD ) {
              #echo "DBG SECTEURNEW $SECTEURNEW SECTEUROLD $SECTEUROLD<BR>";
              $query="UPDATE secteur SET descr=\"$SECTEURNEW\" WHERE descr=\"$SECTEUROLD\"";
          } elseif ( $action == "Supprimer" ) {
              #echo "DBG SECTEURNEW $SECTEURNEW<BR>";
              $query="DELETE FROM secteur WHERE descr=\"$SECTEURNEW\"";
          } elseif ( $action == "Ajouter" && $SECTEURNEW !="" ) {
              #echo "DBG SECTEURNEW $SECTEURNEW<BR>";
              $query="INSERT INTO `secteur` (`id`, `descr`) VALUES ('', '$SECTEURNEW')";
          }
          $result=@mysql_query($query);
        }
        // TOPOLOGIE
        if ( ($txtfile !="")) {
          // upload du fichier
           //system ("/usr/share/lcs/Plugins/Maintenance/Scripts/import_topo.sh $txtfile");
              if (($handle = fopen("$txtfile", "r")) !== FALSE) {
                  $rq="TRUNCATE TABLE topologie";
                  $result=@mysql_query($rq);
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                 $BAT= mb_split(" ",$data[1]);
                 $rq="INSERT INTO topologie ( id , batiment , etage , salle ) VALUES ('', '$BAT[1]', '$data[2]', '$data[3]') ";
                 $result=@mysql_query($rq);
                 }
                fclose($handle);
             }
        }
        // RESTAURATION BASE
        if ( ($sqlfile != "")) {
          // upload du fichier
          $tmpsqlfile =$sqlfile;
          system ("/usr/bin/mysql -u $USERAUTH -p$PASSAUTH $DBAUTHMAINT < $tmpsqlfile");
         }
        // FIN Traitement des modifications de configuration
        $mode = "team";
        // Affichage du menu haut
        Aff_mnu($mode);
        Aff_bar_mode ("Configuration");
        $html  = "<table style=\"width:100%;\"><tr><td>\n";
        // Affichage du menu de configuration
        $html .= "<div id=\"mnuconfig\">\n";
        $html .= "    <a href=\"config.php?conf=mail&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."\" class=\"mail\">Mail</a>\n";
        $html .= "    <a href=\"config.php?conf=secteur&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."\" class=\"secteur\">Secteur</a>\n";
        $html .= "    <a href=\"config.php?conf=topo&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."\" class=\"topo\">Topologie</a>\n";
        $html .= "    <a href=\"config.php?conf=save&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."\" class=\"save\">Sauvegarde base</a>\n";
        $html .= "    <a href=\"config.php?conf=restor&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."\" class=\"restor\">Restauration base</a>\n";
        $html .= "</div>\n";
        // Affichage de mainconfig
        if ( !isset($conf) || $conf == "mail" ) {
          // Config mail de diffusion
          $html .= "<div id=\"subconfig\" class=\"tableint\">\n";
          $html .= "<div class=\"subconfigsubtitle\"><img src=\"Style/img/24/email.png\" alt=\"\"/>&nbsp;Adresse de diffusion des demandes d'intervention</div>\n";
          $html .= "<div class=\"subconfigcontainer\">\n";
          $html .= "  <form method=\"post\" action=\"config.php?conf=mail&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."\">\n";
          $html .= "    <p>Mail : <input type=\"text\" size=\"50\" name=\"MAILMAINTCONF\" value=\"$MAILMAINT\"></p>\n";
          $html .= "    <input type=\"hidden\" name=\"mailconf\" value=\"true\">\n";
          $html.='  <input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" />';
          $html .= "    <p><input type=\"submit\" value=\"Valider\" class=\"button\"></p>\n";
          $html .= "   </form>\n";
          $html .= "</div>\n";
          $html .= "</div>\n";
        } elseif ( $conf == "secteur") {
          // Config secteurs d'intervention (Modification/Ajout/Suppression)
          $html .= "<div id=\"subconfig\" class=\"tableint\">\n";
          $html .= "  <div class=\"subconfigsubtitle\"><img src=\"Style/img/24/map.png\" alt=\"\"/>&nbsp;Modification/ Ajout / Suppression des secteurs d'intervention</div>\n";
          $html .= "  <div class=\"subconfigcontainer\">\n";
          $result = @mysql_query("SELECT descr from  secteur");
          if ($result) {
          // 1er Form de selection d'un secteur
          $html .= "  <p>S&#233;lectionnez un secteur pour modifier ou supprimer son intitul&#233;.</p>\n";
          $html .= "      <select name=\"secteur\" size=\"5\" onChange=\"location = this.options[this.selectedIndex].value;\">\n";
            while ( $r = @mysql_fetch_array($result) ) {
              $html .=  "      <option value=\"config.php?conf=secteur&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."&secteur=".$r["descr"]."\"";
              if ( $secteur == $r["descr"]) $html .=  "selected";
              $html .= ">".$r["descr"]."</option>\n";
            }
          $html .= "      </select>\n";
          } else  $html .= "Pas de secteur d&#233;finit !\n";
          @mysql_free_result($result);
          //  2eme form de modification suppression ajout
          $html .= "    <form method=\"post\" action=\"config.php?conf=secteur&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."\">\n";
          $html .= "      <p><input type=\"text\" size=\"30\" name=\"SECTEURNEW\" value=\"$secteur\"></p>\n";
          $html .= "      <input type=\"hidden\" name=\"SECTEUROLD\" value=\"$secteur\">\n";
          $html .= "      <input type=\"hidden\" name=\"secteurconf\" value=\"true\">\n";
          $html.='  <input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" />';
          if ( ($secteur=="") )
            $html .= "      <p><input type=\"submit\" name=\"action\" value=\"Ajouter\" class=\"button\">";
          else {
            $html .= "<input type=\"submit\" name=\"action\" value=\"Modifier\" class=\"button\">";
            $html .= "<input type=\"submit\" name=\"action\" value=\"Supprimer\" class=\"button\"></p>\n";
          }
          $html .= "    </form>\n";
          $html.= "   </div>\n";
          $html .= "</div>\n";
          //
          } elseif ( $conf == "topo" ) {
           // Config topologie de l'etablissement (Import)
           //Consultation
           // 2.4.8 Creer sa topologie
          $html .= "<div id=\"loadingContainer\" style=\"margin-left:150px;\">\n";
          $html .= "<div id=\"showdata\" class=\"ui-state-highlight ui-corner-all message\"></div>\n";
          $html .= "<div id=\"loading\">	Chargement...</div>\n";
          $html .= "</div>\n";
          $html .= "<div id=\"contentDiv\"></div>";
          $html .= "<div id=\"divAddTopo\"></div>";
              // Affichage du formulaire d'import
          $html .= "<div id=\"subconfig\" class=\"tableint\">\n";
          $html .= "  <h3 class=\"subconfigsubtitle\"><img src=\"Style/img/24/building.png\" alt=\"\"/>&nbsp;Importation d'une nouvelle structure</h3>\n";
          $html .= "  <div class=\"subconfigcontainer\">\n";
          $html .= "    <form action=\"config.php?conf=topo&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."\" method=\"post\" ENCTYPE=\"multipart/form-data\">\n";
          $html .= "      <div class=\"ui-state-highlight ui-corner-all message active\"><span class='exclam float_left'></span>&nbsp Attention, les donn&#233;es existantes seront &#233;cras&#233;es!!<br />N'effectuez cette action qu'en connaissance de cause</div>\n";
          $html .= "      <p>Fichier texte &#224; importer : <input name='txtfile' type='file'></p>\n";
          $html .= "      <div align='center'><input type='submit' VALUE='Importer le fichier!!' class=\"button\"></div>\n";
          $html.='  <input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" />';
          $html .= "    </form>\n";
          $html .= "  </div>";
          $html .= "</div>\n";
              // Suppression de la topo (vidage de table)
          $html .= "<div id=\"subconfig\" class=\"tableint\">\n";
          $html .= "  <h3 class=\"subconfigsubtitle\"><img src=\"Style/img/24/building.png\" alt=\"\"/>&nbsp;Suppression de la structure</h3>\n";
          $html .= "  <div class=\"subconfigcontainer\">\n";
          $html .= "  </div>";
          $html .= "</div>\n";
              // Affichage du formulaire d'import
          $html .= "    <form action=\"config.php?conf=topo\" method=\"post\" ENCTYPE=\"multipart/form-data\">\n";
                          //
        } elseif ( $conf == "save" ) {
          // sauvegarde base de donnees de l'appli
          $html .= "<div id=\"subconfig\" class=\"tableint\">\n";
          $html .= "  <div class=\"subconfigsubtitle\"><img src=\"Style/img/24/database_save.png\" alt=\"\"/>&nbsp;Sauvegarde de la base de donn&#233;es</div>\n";
          $html .= "  <div class=\"subconfigcontainer\">\n";
          $html .= "    <form action=\"config.php?conf=save&amp;jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/config.php'))."\" method=\"post\">\n";
          $html .= "      <p>Sauvegarde g&#233;n&#233;rale <input type=\"submit\" name=\"saveconf\" value=\"Sauvegarde\" class=\"button\">\n";
          $html .= "      <p>Sauvegarde de l'ann&#233;e en cours <input type=\"submit\" name=\"savethisyear\" value=\"Sauvegarde\" class=\"button\">\n";
          $html.='  <input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" />';
          $html .= "    </form>\n";
          $html .= "  </div>\n";
          $html .= "</div>\n";
        } elseif ( $conf == "restor" ) {
          // restauration de la base de donnees de l'appli
            $html .= "<div id=\"subconfig\" class=\"tableint\">\n";
            $html .= "<div id=\"loading\">	Chargement...</div>\n";
            $html .= "    <div class=\"subconfigsubtitle\"><img src=\"Style/img/24/database_refresh.png\" alt=\"\"/>&nbsp;Restauration de la base de donn&#233;es</div>\n";
             // Affichage du formulaire de restauration
            $html .= "<div id=\"showdata\" class=\"ui-state-highlight ui-corner-all message\"></div>\n";
            #$html .= "    <form action=\"config.php?conf=restor\" method=\"post\" enctype=\"multipart/form-data\">\n";
            $html .= "    <form action=\"action/restor_db.ajax.php?jeton=".md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/action/restor_db.ajax.php'))."\" method=\"post\" enctype=\"multipart/form-data\" id=\"submitRestor\">\n";
            $html .= "      <p class=\"ui-state-highlight ui-corner-all message active\"><span class=\"exclam float_left\"></span>&nbsp;Attention, les donn&#233;es existantes seront &#233;cras&#233;es!!<br />N'effectuez cette action qu'en connaissance de cause</p>\n";
            $html .= "      <p>Fichier sql &#224; importer : <input name='sqlfile' type='file'></p>\n";
            $html .= "      <div align='center'><input type='submit' value='Importer le fichier!!' class=\"button\" id=\"submitRestor\"></div>\n";
            $html.='  <input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/action/restor_db.ajax.php')).'" />';
            $html .= "    </form>\n";
            $html .= "  </div>";
            $html .= "</div>\n";
            }
            $html  .= "</td></tr></table>\n";
            echo $html;
        } else {
            // l'utilisateur ne fait pas partie de l'equipe de maintenance
            $mode = "user";
            Aff_mnu($mode);
            table_alert ("Vous ne poss&#233;dez pas les droits pour configurer l'application !");
    }
    }
        echo "
        <script>
                $(document).ready(function(){
                        var str=document.location.href.split('=');
                        $('div.mnu>a').removeClass('active');
                        $('div.mnu>a.config').addClass('active');
                        $('div#mnuconfig>a.'+str[1]).addClass('active');
                });
        </script>
        ";
        include "Includes/pieds_de_page.inc.php";
 ?>
