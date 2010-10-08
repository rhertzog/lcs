<?php
	include "Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
  	include "$BASEDIR/Annu/includes/ldap.inc.php";
  	include "$BASEDIR/Annu/includes/ihm.inc.php";
	include "Includes/func_maint.inc.php";
        include "Includes/config.inc.php";

        // Register Global GET
        $conf=$_GET['conf'];
        $bat=$_GET['bat'];
        $etage=$_GET['etage'];
        $salle=$_GET['salle'];
        $secteur=$_GET['secteur'];
        // Register Global POST
        $MAILMAINTCONF=$_POST['MAILMAINTCONF'];
        $mailconf=$_POST['mailconf'];
        $SECTEURNEW=$_POST['SECTEURNEW'];
        $SECTEUROLD=$_POST['SECTEUROLD'];
        $secteurconf=$_POST['secteurconf'];
        $saveconf=$_POST['saveconf'];
        $action=$_POST['action'];
        $txtfile=$_FILES['txtfile']['name'];
        $sqlfile=$_FILES['sqlfile']['name'];

        // SAUVEGARDE BASE
        if ( $saveconf == "Sauvegarde" ) {
          system("mysqldump $DBAUTHMAINT -u $USERAUTH -p$PASSAUTH  > /tmp/$DBAUTHMAINT.sql");
          header("Content-Type: octet-stream");
          header("Content-Length: ".filesize ("/tmp/$DBAUTHMAINT.sql") );
          header("Content-Disposition: attachment; filename=\"/tmp/$DBAUTHMAINT.sql\"");
          include ("/tmp/$DBAUTHMAINT.sql");
        }
        // RESTAURATION BASE   
	html();
	list ($idpers,$uid)= isauth();
  	if ($idpers == "0") {
		// L'utilisateur n'est pas authentifie
		table_alert ("Vous devez pr&#233;alablement vous authentifier sur votre «Espace perso  LCS» pour acc&#233;der &#224; cette application !");
	} else {
		// L'utilisateur est authentifie
		list($user, $groups)=people_get_variables($uid, false);
		// Initialisation de la variable mnuchoice si elle vide
		if ( !isset($mnuchoice) ) $mnuchoice="wait";
		// Recherche si l'utilisateur authentifie a le droit Maint_is_admin et System_is_admin
                if ( is_admin("Maint_is_admin",$uid)=="Y" && is_admin("system_is_admin",$uid)=="Y" ) {
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
                        if (isset ($txtfile)) {
                          // upload du fichier
                          $tmptxtfile = $_FILES["txtfile"]["tmp_name"];
                          system ("/usr/share/lcs/Plugins/Maintenance/Scripts/import_topo.sh $tmptxtfile");
	                 }    
                        // RESTAURATION BASE
                        if (isset ($sqlfile)) {
                          // upload du fichier
                          $tmpsqlfile = $_FILES["sqlfile"]["tmp_name"];
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
                        $html .= "    <a href=\"config.php?conf=mail\">Mail</a>\n";
                        $html .= "    <a href=\"config.php?conf=secteur\">Secteur</a>\n";
                        $html .= "    <a href=\"config.php?conf=topo\">Topologie</a>\n";
                        $html .= "    <a href=\"config.php?conf=save\">Sauvegarde base</a>\n";
                        $html .= "    <a href=\"config.php?conf=restor\">Restauration base</a>\n";
                        $html .= "</div>\n";
                        // Affichage de mainconfig
                        if ( !isset($conf) || $conf == "mail" ) {
                          // Config mail de diffusion
                          $html .= "<div id=\"subconfig\">\n";
                          $html .= "<div class=\"subconfigsubtitle\">Adresse de diffusion des demandes d'intervention</div>\n";
                          $html .= "<div class=\"subconfigcontainer\">\n";
                          $html .= "  <form method=\"post\" action=\"config.php?conf=mail\">\n";
                          $html .= "    <p>Mail : <input type=\"text\" size=\"50\" name=\"MAILMAINTCONF\" value=\"$MAILMAINT\"></p>\n";
                          $html .= "    <input type=\"hidden\" name=\"mailconf\" value=\"true\">\n";
                          $html .= "    <p><input type=\"submit\" value=\"Valider\"></p>\n";
                          $html .= "   </form>\n";
                          $html .= "</div>\n"; 
                          $html .= "</div>\n";                       
                        } elseif ( $conf == "secteur") {
                          // Config secteurs d'intervention (Modification/Ajout/Suppression)    
                          $html .= "<div id=\"subconfig\">\n";
                          $html .= "  <div class=\"subconfigsubtitle\">Modification/ Ajout / Suppression des secteurs d'intervention</div>\n";
                          $html .= "  <div class=\"subconfigcontainer\">\n";
                          $result = @mysql_query("SELECT descr from  secteur");
                          if ($result) { 
                          // 1er Form de selection d'un secteur
                          $html .= "  <p>S&#233;lectionnez un secteur pour modifier ou supprimer son intitul&#233;.</p>\n";
                          $html .= "      <select name=\"secteur\" size=\"5\" onChange=\"location = this.options[this.selectedIndex].value;\">\n";
                            while ( $r = @mysql_fetch_array($result) ) {
                              $html .=  "      <option value=\"config.php?conf=secteur&secteur=".$r["descr"]."\"";
                              if ( $secteur == $r["descr"] ) $html .=  "selected";
                              $html .= ">".$r["descr"]."</option>\n";
                            }
                          $html .= "      </select>\n"; 
                          } else  $html .= "Pas de secteur d&#233;finit !\n";                                                                                  
                          @mysql_free_result($result);  
                          //  2eme form de modification suppression ajout
                          $html .= "    <form method=\"post\" action=\"config.php?conf=secteur\">\n";
                          $html .= "      <p><input type=\"text\" size=\"30\" name=\"SECTEURNEW\" value=\"$secteur\"></p>\n";                              
                          $html .= "      <input type=\"hidden\" name=\"SECTEUROLD\" value=\"$secteur\">\n";  
                          $html .= "      <input type=\"hidden\" name=\"secteurconf\" value=\"true\">\n";
                          if ( ! isset($secteur) )
                            $html .= "      <p><input type=\"submit\" name=\"action\" value=\"Ajouter\">";
                          else {
                            $html .= "<input type=\"submit\" name=\"action\" value=\"Modifier\">";
                            $html .= "<input type=\"submit\" name=\"action\" value=\"Supprimer\"></p>\n"; 
                          }
                          $html .= "    </form>\n";             
                          $html.= "   </div>\n";  
                          $html .= "</div>\n";                          
                        } elseif ( $conf == "topo" ) {
                          // Config topologie de l'etablissement (Import)
                          $html .= "<div id=\"subconfig\">\n";
                          $html .= "  <div class=\"subconfigsubtitle\">Importation d'une nouvelle structure</div>\n";
                          $html .= "  <div class=\"subconfigcontainer\">\n";
   	                  // Affichage du formulaire d'import
		          $html .= "    <form action=\"config.php?conf=topo\" method=\"post\" ENCTYPE=\"multipart/form-data\">\n";
		          $html .= "      <p>Attention, les donn&#233;es existantes seront &#233;cras&#233;es!!</p>\n<p>N'effectuez cette action qu'en connaissance de cause</p>\n";
		          $html .= "      <p>Fichier texte &#224; importer : <input name='txtfile' type='file'></p>\n";
		          $html .= "      <div align='center'><INPUT type='submit' VALUE='Importer le fichier!!'></div>\n";
		          $html .= "    </form>\n";                          
                          $html .= "  </div>";
                          $html .= "<div class=\"subconfigsubtitle\">Consultation de la topologie de l'&#233;tablissement</div>\n";
                          $html .= Aff_topo();
                          $html .= "</div>\n";
                        } elseif ( $conf == "save" ) {
                          // sauvegarde base de donnees de l'appli
                          $html .= "<div id=\"subconfig\">\n";
                          $html .= "  <div class=\"subconfigsubtitle\">Sauvegarde de la base de donn&#233;es</div>\n";
                          $html .= "  <div class=\"subconfigcontainer\">\n";
                          $html .= "    <form action=\"config.php?conf=save\" method=\"post\">\n";
		          $html .= "      <p>Confirmez la sauvegarde <input type=\"submit\" name=\"saveconf\" value=\"Sauvegarde\">\n";
		          $html .= "    </form>\n";
                          $html .= "  </div>\n";
                          $html .= "</div>\n";
                        } elseif ( $conf == "restor" ) {
                          // restauration de la base de donnees de l'appli
                          $html .= "<div id=\"subconfig\">\n";
                          $html .= "    <div class=\"subconfigsubtitle\">Restauration de la base de donn&#233;es</div>\n";
          	          // Affichage du formulaire de restauration
		          $html .= "    <form action=\"config.php?conf=restor\" method=\"post\" enctype=\"multipart/form-data\">\n";
		          $html .= "      <p>Attention, les donn&#233;es existantes seront &#233;cras&#233;es!!</p>\n<p>N'effectuez cette action qu'en connaissance de cause</p>\n";
		          $html .= "      <p>Fichier sql &#224; importer : <input name='sqlfile' type='file'></p>\n";
		          $html .= "      <div align='center'><input type='submit' value='Importer le fichier!!'></div>\n";
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
 	include "Includes/pieds_de_page.inc.php";
 ?>
