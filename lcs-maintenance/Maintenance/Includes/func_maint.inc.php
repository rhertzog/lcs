<?php /* includes/func_maint.inc.php maj 07/10/2009 */

function html() {
        $header = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
	$header .= "<html>\n";
	$header .= "<head>\n";
        $header .= "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"tetx/html; charset=ISO-8859-1\">\n";
   	$header .= "<title>Support Informatique</title>\n";
	$header .= "<link type='text/css' rel='stylesheet' href='Style/maintenance.css'>\n";
        $header .= "<script type=\"text/javascript\">\n";
        $header .= "<!--\n";        
        $header .= "function auth_popup() {\n";
        $header .= "  window.focus();\n";
        $header .= "  auth_popupWin = window.open(\"Doc/Html/index.html\",\"auth_supinfo\",\"scrollbars=yes,resizable=yes,width=800,height=600\");\n";
        $header .= "  auth_popupWin.focus();\n";
        $header .= "}\n";
        $header .= "//-->\n";
        $header .= "</script>\n";        
	$header .= "</head>\n";
	$header .= "<body>\n";
        $header .= "<div id=\"container\">\n";
	echo $header;
}

function nltobr ($string) {
 return str_replace("\n", "<br>", $string);
}

#function is_memberOf ($uid, $group) {
#	$uids = search_uids ("(cn=$group)", "half");
#	for ($loop = 0; $loop < count($uids);$loop++) {
#		if ( $uids[$loop]["uid"] == $uid ) return true;
#	}
#}

function Aff_mnu($mode) {
	global $uid;
	$header .= "<table class='tabletitre'>\n";
	$header .= " <tr>\n";
	$header .= "   <td><div class='titre'>Support Info.</div></td>\n";
	$header .= " </tr>\n";
	$header .= " <tr>\n";
	$header .= "   <td>\n";
	$header .= "     <div class='mnu' align='right'>\n";
	if ($mode=="user") $header.="      <a class='menu_haut' href='demande_support.php'>Demande de support</a>&nbsp;|&nbsp;";
        else $header.="<a class='menu_haut' href='demande_support.php'>Intervention non sollicitée</a>&nbsp;|&nbsp;";
	$header.= "<a class='menu_haut' href='index.php?mnuchoice=wait'>En attente</a>&nbsp;|&nbsp;";
	$header.="<a class='menu_haut' href='index.php?mnuchoice=myspool'>Votre Encours</a>&nbsp;|&nbsp;";
	if ($mode=="user") $header.= "<a class='menu_haut' href='index.php?mnuchoice=myhistorique'>Votre historique</a>&nbsp;|&nbsp;";
	if ($mode=="team" || $mode=="team_CR") {
		$header.= "<a class='menu_haut' href='index.php?mnuchoice=spool'>Encours gen.</a>&nbsp;|&nbsp;";
		$header.= "<a class='menu_haut' href='index.php?mnuchoice=myhistorique'>Votre historique</a>&nbsp;|&nbsp;";
		$header.= "<a class='menu_haut' href='index.php?mnuchoice=historique'>Historique gen.</a>&nbsp;|&nbsp;";
	}
        // Affichage du menu de configuration
        if (is_admin("system_is_admin",$uid)=="Y")
          $header.="<a class='menu_haut' href='config.php'>Configuration</a>&nbsp;|&nbsp;";
        // Affichage du menu d'aide
        $header.= "<a class='menu_haut' href='#' onClick='auth_popup(); return false'>Aide</a>&nbsp;";
	$header .= "     </div>\n";
	$header .= "   </td>\n";
	$header .= " </tr>\n";
	$header .= "</table>\n";
	$header .= "<div style=\"height:10px\"></div>\n";
 	echo $header;
}

function Aff_bar_mode ($message) {
	global $mnuchoice, $mode;
	$header  = "<table class='tablebar'>\n";
	$header .= "  <tr>\n";
	$header .= "<td><div class='titrebar'>$message</div>";
        if ( $mode == "team" ) {
        // mode team
           if ( ereg ("^En", $message) || ereg ("^Votre", $message) ||$message == "Historique gen." ) {
            if (  $mnuchoice=="spool" || $mnuchoice=="historique" )
              $header .= "<div class='order'><A class='menu_barre' href='index.php?mnuchoice=$mnuchoice&tri=prisencharge'>Prise en charge par</A><img alt=\"DESC\" src=\"Images/down.gif\" hspace=\"4\" border=0></div>";
            $header .= "<div class='order'><A class='menu_barre' href='index.php?mnuchoice=$mnuchoice&tri=emetteur'>Emetteur</A><img alt=\"ASC\" src=\"Images/up.gif\" hspace=\"4\" border=0></div>";
            $header .= "<div class='order'><A class='menu_barre'  href='index.php?mnuchoice=$mnuchoice&tri=date'>Date</A><img alt=\"DESC\" src=\"Images/down.gif\" hspace=\"4\" border=0></div></td>\n";            
          }       
        } else {
        // mode user
          if ( ereg ("^Votre", $message) ) {
            $header .= "<div class='order'><A class='menu_barre'  href='index.php?mnuchoice=$mnuchoice&tri=date'>Date</A><img alt=\"DESC\" src=\"Images/down.gif\" hspace=\"4\" border=0></div>\n";                                
            $header .= "<div class='order'><A class='menu_barre' href='index.php?mnuchoice=$mnuchoice&tri=prisencharge'>Prise en charge par</A><img alt=\"DESC\" src=\"Images/down.gif\" hspace=\"4\" border=0></div></td>";
          }
        }
	$header .= "</tr>\n";
	$header .= "</table>\n";
	echo $header;
}

function Is_feed ($mode, $filter, $message) {
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
	$requete = mysql_query("SELECT * FROM maint_task WHERE ($filter)");
	$row = mysql_fetch_row ($requete);
	if (!$row)  {
		table_alert ($message);
		return false;
	} else return true;
}

function Aff_Intro ($OpenTimeStamp, $Sector, $Building, $Room, $NumComp, $Mark, $Os) {

	// Affichage des interventions
 	$html = "<table class='tableint'>\n";
  	$html .= "<tr>\n<td>\n";
	$html .= "<em class='msg_intro'>";
	// les champs vides ne sont pas affichés
 	if (isset ($OpenTimeStamp) ) $html .= "Le ".strftime("%d - %m - %Y &#233; %H h %M mn",$OpenTimeStamp);
	$html .=", <strong>Secteur</strong> : $Sector <strong>Bat</strong> : $Building <strong>Salle</strong> : $Room <strong>Poste</strong> : $NumComp";
	if ( $Mark !="" ) $html .="&nbsp;<strong>Marque</strong> : $Mark";
	if (isset ($Os) ) $html .="&nbsp;<strong>Système d'exploitation</strong> : $Os";
	$html .="</em>\n";
	echo $html;
}

function Aff_feed_wait ($mode, $filter,$tri) {
	#	$mode = "team" ou "user"
	#	$filter = définit le filtre de la requet SQL

	global $mnuchoice;

	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
        $sens = "ASC";
	if ( $tri == "emetteur") $tri="Owner";
	elseif ( $tri == "prisencharge") $tri="Author";
	else { $tri="OpenTimeStamp"; $sens = "DESC"; }
	$requete = mysql_query("SELECT * FROM maint_task WHERE ($filter) ORDER BY $tri $sens ");
 	while($row = mysql_fetch_array($requete)) {
  		$Rid				= 	$row["Rid"];
		$Host			= 	$row["Host"];
  		$Owner			=	$row["Owner"];
    		$OwnerMail		=	$row["OwnerMail"];
		$Sector			=	$row["Sector"];
		$Building		=	$row["Building"];
  		$Room			=	$row["Room"];
    		$NumComp		=	$row["NumComp"];
		$Mark			=	$row["Mark"];
		$Os			=	$row["Os"];
		$Cat			=	$row["Cat"];
  		$Content		=	$row["Content"];
    		$OpenTimeStamp          =	$row["OpenTimeStamp"];
		$BoosTimeStamp          =	$row["BoosTimeStamp"];
		$NumBoost		=	$row["NumBoost"];

		Aff_intro($OpenTimeStamp, $Sector, $Building, $Room, $NumComp, $Mark, $Os);
		echo"<br>\n";
		if (isset ($Owner) ) echo "Emetteur  <b>$Owner</b> ";
		if ( isset($OwnerMail) ) echo "&nbsp;&nbsp;&nbsp;<i> ($OwnerMail ) </i><br>";
		if ( isset($Content) ) echo "La demande d'intervention est :&nbsp;<b>".nltobr($Content)."</b><br>";
		BoostAlert ( $NumBoost );
		if ($mode == "team") {
			echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=take&mnuchoice=$mnuchoice'>Prendre en charge</a>&nbsp;|&nbsp;";
			echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=del_task&mnuchoice=$mnuchoice'>Supprimer</a>&nbsp;|&nbsp;";
		} else {
			echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=del_task'>Supprimer</a>&nbsp;|&nbsp;";
			echo "<a class='menu_feed' href='edit_demande.php?Rid=$Rid'>Modifier</a>&nbsp;|&nbsp;";
			if ( VerifTime ($OpenTimeStamp, $BoosTimeStamp) )
				echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=relance'>Relancer</a>&nbsp;|&nbsp;";
		}
		echo "</td>\n</tr>\n</table>\n<br>\n";
	}  //fin du while
}

function Aff_feed_take ($mode, $filter,$tri) {
	#	$mode = "team" ou "team_CR" ou "user"
	#	$filter = définit le filtre de la requet SQL
	global $mnuchoice;
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
        $sens = "ASC";
	if ( $tri == "emetteur") $tri="Owner";
	elseif ( $tri == "prisencharge") $tri="Author";
	else { $tri="OpenTimeStamp"; $sens = "DESC"; }
	$requete = mysql_query("SELECT * FROM maint_task WHERE ($filter) ORDER BY $tri $sens ");
 	while($row = mysql_fetch_array($requete)) {
  		$Rid				= 	($row["Rid"]);
		$Host			= 	($row["Host"]);
  		$Owner			=	($row["Owner"]);
    		$OwnerMail		=	($row["OwnerMail"]);
		$Author			=	($row["Author"]);
		$Sector			=	($row["Sector"]);
		$Building		=	($row["Building"]);
  		$Room			=	($row["Room"]);
    		$NumComp		=	($row["NumComp"]);
		$Mark			=	($row["Mark"]);
		$Os			=	($row["Os"]);
		$Cat			=	($row["Cat"]);
  		$Content		=	($row["Content"]);
    		$OpenTimeStamp          =	$row["OpenTimeStamp"];
		$TakeTimeStamp          =	$row["TakeTimeStamp"];
		$BoosTimeStamp          =	$row["BoosTimeStamp"];
		$NumBoost		=	$row["NumBoost"];

		Aff_intro($OpenTimeStamp, $Sector, $Building, $Room, $NumComp, $Mark, $Os);

		echo"<br>\n";
		if (isset ($Owner) ) echo "Emetteur  <b>$Owner</b> ";
		if ( isset($OwnerMail) ) echo "&nbsp;&nbsp;&nbsp;<i> ($OwnerMail ) </i><br>";
		if ( isset($Content) ) echo "La demande d'intervention est :&nbsp;<b>".nltobr($Content)."</b><br>";
		BoostAlert ( $NumBoost );
		#if ( $BoosTimeStamp != 0 ) echo "<img src='images/boost.png' width='18' height='18' border='0'>";
		if ( $mode == "team" ) {
			if ( $mnuchoice == "myspool" ) {
				echo "<em class='msg_take'>Prise en charge le </em><em class='author_take'>".strftime("%d - %m - %Y &#233; %H h %M mn",$TakeTimeStamp)."</em>&nbsp;|&nbsp;";
				echo "<a class='menu_feed' href='bring_back.php?Rid=$Rid'>Rapport d'intervention</a>&nbsp;|&nbsp;";
				echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=del_task&mnuchoice=$mnuchoice'>Supprimer</a>&nbsp;|&nbsp;";
			} else {
				echo "<em class='msg_take'>Prise en charge le <em class='author_take'>".strftime("%d - %m - %Y &#233; %H h %M mn",$TakeTimeStamp)."</em><em class='msg_take'>&nbsp;par :&nbsp;</em><em class='author_take'>$Author</em>\n";
			}
		} elseif ( $mode =="team_CR" ) {
			echo "<em class='msg_take'>Prise en charge le </em><em class='author_take'>".strftime("%d - %m - %Y &#233; %H h %M mn",$TakeTimeStamp)."</em>";
		} elseif ( $mode =="user" ) {
			if ( VerifTime ($OpenTimeStamp, $BoosTimeStamp) )
				echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=relance&mnuchoice=$mnuchoice'>Relancer</a>&nbsp;|&nbsp;";
			echo "<em class='msg_take'>Votre demande a été prise en charge le <em class='author_take'>".strftime("%d - %m - %Y &#233; %H h %M mn",$TakeTimeStamp)."</em><em class='msg_take'>&nbsp;par :&nbsp;</em><em class='author_take'>$Author</em>\n";
		}
		echo "</td>\n</tr>\n</table>\n";
		// Recherche si il existe un feed de rapport d'intervention
		$requete1 = mysql_query("SELECT * FROM maint_thread WHERE (TopRid='$Rid') ORDER BY TimeStamp");
		// Affichage du feed du rapport d'intervention
		while($row1 = mysql_fetch_array($requete1)) {
			$Rid			=	$row1["Rid"];
			$Content1		=	$row1["Content"];
			$TimeStamp1		=	strftime("%d - %m - %Y &#233; %H h %M mn",$row1["TimeStamp"]);
			$TimeLife		=	$row1["TimeLife"];
			$Cost			=	$row1["Cost"];
			// En tête de la réponse
			$html = "<div align='right'><table class='tablefeed'>\n";
 			$html .="<tr><td class='tablefeed'>\n";
			$html .= "Intervention du $TimeStamp1&nbsp;\n";
			if ( $TimeLife > 0 ) $html .= "| dur&eacute;e $TimeLife mn&nbsp;";
			if ( $Cost> 0 ) $html .= "| Co&ucirc;t des pi&egrave;ces  $Cost &#8364;&nbsp;";
			$html .="</td></tr>\n";
			$html .= "</table></div>\n";
			// Réponse
			$html .= "<div align='right'><table class='tablecr'>\n";
 			$html .="<tr><td class='tablecr'>\n";
			$html .= nltobr(stripslashes($Content1));
			if ( $mode == "team" && $mnuchoice=="myspool") {
				$html .= "<br><a class='menu_feed' href='index.php?Rid=$Rid&action=del_cr&mnuchoice=$mnuchoice'>Supprimer</a>&nbsp;|&nbsp;\n";
				$html .= "<a class='menu_feed' href='bring_back_mod.php?Rid=$Rid'>Modifier</a>&nbsp;|&nbsp;\n";
			}
			$html .="</td></tr>\n";
			$html .= "</table></div>\n";
			echo "$html";
		}
		echo "<br>";
	}  //fin du while
}

function Aff_feed_close ($mode, $filter,$tri) {
	#	$mode = "team" ou "user"
	#	$filter = définit le filtre de la requet SQL
	global $mnuchoice;
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
        $sens = "ASC";
	if ( $tri == "emetteur") $tri="Owner";
	elseif ( $tri == "prisencharge") $tri="Author";
	else { $tri="OpenTimeStamp"; $sens = "DESC"; }
	$requete = mysql_query("SELECT * FROM maint_task WHERE ($filter) ORDER BY $tri $sens ");
 	while($row = mysql_fetch_array($requete)) {
  		$Rid			= 	$row["Rid"];
		$Host			= 	$row["Host"];
  		$Owner			=	$row["Owner"];
    		$OwnerMail		=	$row["OwnerMail"];
		$Author			=	$row["Author"];
		$Sector			=	$row["Sector"];
		$Building		=	$row["Building"];
  		$Room			=	$row["Room"];
    		$NumComp		=	$row["NumComp"];
		$Mark			=	$row["Mark"];
		$Os			=	$row["Os"];
		$Cat			=	$row["Cat"];
  		$Content		=	$row["Content"];
    		$OpenTimeStamp          =	$row["OpenTimeStamp"];
		$TakeTimeStamp          =	strftime("%d - %m - %Y &#233; %H h %M mn",$row["TakeTimeStamp"]);
		$CloseTimeStamp         =	strftime("%d - %m - %Y &#233; %H h %M mn",$row["CloseTimeStamp"]);
		$BoosTimeStamp          =	$row["BoosTimeStamp"];
		$NumBoost		=	$row["NumBoost"];

		Aff_intro($OpenTimeStamp, $Sector, $Building, $Room, $NumComp, $Mark, $Os);

		echo"<br>\n";
		if (isset ($Owner) ) echo "Emetteur  <b>$Owner</b> ";
		if ( isset($OwnerMail) )  echo "&nbsp;&nbsp;&nbsp;<i> ($OwnerMail ) </i><br>";
		if ( isset($Content) )  echo "La demande d'intervention est :&nbsp;<b>".nltobr($Content)."</b><br>";
		BoostAlert ($NumBoost);
		if ( $mode=="team" )
			echo "<em class='msg_take'>Cette demande a été prise en charge le </em>";
		else
			echo "<em class='msg_take'>Votre demande a été prise en charge le </em>";
		echo "<em class='author_take'>".$TakeTimeStamp."</em>
			 <em class='msg_take'>&nbsp;par :&nbsp;</em>
			 <em class='author_take'>$Author</em>
			 <em class='msg_take'>et clotur&eacute;e le&nbsp;</em>
			 <em class='author_take'>$CloseTimeStamp</em>\n";
		echo "</td>\n</tr>\n</table>\n";
		// Recherche si il existe un feed de rapport d'intervention
		$requete1 = mysql_query("SELECT * FROM maint_thread WHERE (TopRid='$Rid')");
		// Affichage du feed du rapport d'intervention
		while($row1 = mysql_fetch_array($requete1)) {
			$Content1               =	$row1["Content"];
			$TimeStamp1		=	strftime("%d - %m - %Y &#233; %H h %M mn",$row1["TimeStamp"]);
			$TimeLife		=	$row1["TimeLife"];
			$Cost			=	$row1["Cost"];
			// En tête de la réponse
			$html = "<div align='right'><table class='tablefeed'>\n";
 			$html .="<tr><td class='tablefeed'>\n";
			$html .= "Intervention du $TimeStamp1&nbsp;\n";
			if ( $TimeLife > 0 ) $html .= "| dur&eacute;e $TimeLife mn&nbsp;";
			if ( $Cost> 0 ) $html .= "| Co&ucirc;t des pi&egrave;ces  $Cost &#8364;&nbsp;";
			$html .="</td></tr>\n";
			$html .= "</table></div>\n";
			// Réponse
			$html .= "<div align='right'><table class='tablecr'>\n";
 			$html .="<tr><td class='tablecr'bla>\n";
			$html .= nltobr(stripslashes($Content1));
			$html .="</td></tr>\n";
			$html .= "</table></div>\n";
			echo "$html";
		}
		echo "<br>";
	}  //fin du while
}


function Aff_bringback_form($Rid, $Author, $AuthorMail) {
	global $DEBUG,  $MAILMAINT, $post, $bringback, $TimeLife, $unit, $Cost, $Close;

	// Relecture de la demande
	list ($Sector, $Owner, $OwnerMail, $Author, $Building, $Room, $NumComp, $Mark, $Cat, $Os, $Content, $OpenTimeStamp, $NumBoost ) = read_task ($Rid);
	### DEBUG ###
	if ($DEBUG) {
		echo "DEBUG  Aff_bringback_form >> $Rid, $Author, $AuthorMail<br>";
		echo "DEBUG  Source de la demande>> $Owner, $OwnerMail, $Author, $Content, $OpenTimeStamp<br>";
	}
	if ( !isset($post) ) {
		$html = "<table class='tableint'>\n";
 		$html .="<tr>\n<td rowspan='1' colspan='3'>\n<br>\n";
		$html .= "<form action='bring_back.php?Rid=$Rid' method='POST'>\n";
		$html .= "<textarea name='bringback' wrap='physical' rows='6' cols='100'></textarea><br>\n";
		$html .="</td>\n</tr>";
		$html .= "<tr><td>Dur&eacute;e de l'intervention&nbsp;<input type='text' name='TimeLife' size='2' maxlength='2'>";
		$html .="&nbsp;<select name='unit'>\n<option>mn</option>\n<option>h</option>\n</select>\n</td>\n";
		$html .= "<td align='left'>Co&ucirc;t des pièces de rechange&nbsp;<input type='text' name='Cost' size='4' maxlength='4'>&nbsp;&#8364;&nbsp;</td>\n";
		$html .= "<td align='left'>Cloturer l'intervention&nbsp;<input type='checkbox' name='Close' value='2'></td>\n";
		$html .= "</tr><tr><td rowspan='1' colspan='3'>\n";
		$html .= "<input type='submit' value='Poster' name='submit'>\n";
		$html .= "<input type='hidden' value='true' name='post'>\n";
		$html .= "<input type='reset' value='Recommencer' name='reset'>\n";
		$html .= "</td></tr>\n</table>\n";
		echo $html;
	} else {
		// Mail du CR
		if ( $Close != 2 ) {
			$Subject = "[MaintInfo]CR demande d'intervention";
			$Close = "1";
		} else {
			$Subject = "[MaintInfo]Cloture demande d'intervention";
			// Determination de l'heure de cloture
			$CloseTimeStamp=strtotime (date("Y-m-d H:i:s"));
			// Cloture de l'intervention
			$requete = mysql_query("UPDATE maint_task SET Acq = '2', CloseTimeStamp=' $CloseTimeStamp' WHERE Rid = '$Rid'");
		}
		$Body = stripslashes($bringback)."\n---------------------------------------------------\n Votre demande :".$Content;
		mail_to ($OwnerMail, $MAILMAINT, $Subject, $Body, $AuthorMail);
		// Transfert du CR dans la table maint_task
			$TimeStamp=strtotime (date("Y-m-d H:i:s"));
			//Enregistrement de la demande
			if ($unit=='h') $TimeLife = $TimeLife*60;
        		$result=mysql_query("INSERT INTO maint_thread (TopRid,Author,Content,TimeStamp,TimeLife, Cost)
		                                     				VALUES ('$Rid','$Author','$bringback', '$TimeStamp', '$TimeLife', '$Cost')");
		// Reaffichage du CR
		$html = "<table class='tablecr'>\n";
 		$html .="<tr>\n<td>\n";
		$html .= nltobr(stripslashes($bringback))."\n";
		$html .="</td>\n</tr>\n";
		$html .= "</table><br>\n";
		echo $html;
	}
}

function Aff_bringback_mod_form($Rid,$Author, $AuthorMail) {
	global $DEBUG,  $MAILMAINT, $post, $RidCR, $bringback, $TimeLife, $unit, $Cost, $Close;

	if ( !isset($post) ) {
		// Relecture du CR
		list ( $Rid,$TopRid,$bringback_orig, $TimeLife, $Cost) = read_cr_task ($Rid);
		### DEBUG ###
		if ($DEBUG) {
			echo "DEBUG  Aff_bringback_mod_form 1 >>$post  $bringback_orig, $TimeLife, $Cost<br>";
		}
		$html = "<table class='tableint'>\n";
 		$html .="<tr>\n<td rowspan='1' colspan='3'>\n<br>\n";
		$html .= "<form action='bring_back_mod.php?Rid=$TopRid' method='POST'>\n";
		$html .= "<textarea name='bringback' wrap='physical' rows='6' cols='100'>$bringback_orig</textarea><br>\n";
		$html .="</td>\n</tr>";
		$html .= "<tr><td>Dur&eacute;e de l'intervention&nbsp;<input type='text' name='TimeLife' Value='$TimeLife' size='2' maxlength='2'>";
		$html .="&nbsp;<select name='unit'>\n<option>mn</option>\n<option>h</option>\n</select>\n</td>\n";
		$html .= "<td align='left'>Co&ucirc;t des pièces de rechange&nbsp;<input type='text' name='Cost' value='$Cost' size='4' maxlength='4'>&nbsp;&#8364;&nbsp;</td>\n";
		$html .= "<td align='left'>Cloturer l'intervention&nbsp;<input type='checkbox' name='Close' value='2'></td>\n";
		$html .= "</tr><tr><td rowspan='1' colspan='3'>\n";
		$html .= "<input type='submit' value='Poster' name='submit'>\n";
		$html .= "<input type='hidden' value='true' name='post'>\n";
		$html .= "<input type='hidden' value='$Rid' name='RidCR'>\n";
		$html .= "<input type='reset' value='Recommencer' name='reset'>\n";
		$html .= "</td></tr>\n</table>\n";
		echo $html;
	} else {
		// Relecture de la demande
		list ( $Sector, $Owner, $OwnerMail, $Author, $Building, $Room, $NumComp, $Mark, $Cat, $Os, $Content, $OpenTimeStamp, $NumBoost) = read_task ($Rid);
		if ($DEBUG) {
			echo "DEBUG  Aff_bringback_mod_form 2 >>post  $post, Content $Content  ,Rid $Rid ,RidCR $RidCR, CR  $bringback, TimeLife $TimeLife, Cost $Cost, Close $Close <br>";
		}
		// Mail du CR
		if ( $Close != 2 ) {
			$Subject = "[MaintInfo]Modification CR demande d'intervention";
			$Close = "1";
		} else {
			$Subject = "[MaintInfo]Cloture demande d'intervention";
			// Determination de l'heure de cloture
			$CloseTimeStamp=strtotime (date("Y-m-d H:i:s"));
			// Cloture de l'intervention
			$requete = mysql_query("UPDATE maint_task SET Acq = '2', CloseTimeStamp=' $CloseTimeStamp' WHERE Rid = '$Rid'");
		}
		$Body = stripslashes($bringback)."\n---------------------------------------------------\n Votre demande :".stripslashes($Content);
		mail_to ($OwnerMail, $MAILMAINT, $Subject, $Body, $AuthorMail);
		// UPDATE du CR dans la table maint_thread
		$TimeStamp=strtotime (date("Y-m-d H:i:s"));
		//Enregistrement de la demande
		if ($unit=='h') $TimeLife = $TimeLife*60;
			$requete = mysql_query("UPDATE maint_thread SET Content='$bringback', Cost='$Cost', TimeLife='$TimeLife', TimeStamp='$TimeStamp'  WHERE Rid = '$RidCR'");
		// Reaffichage du CR
		$html = "<table class='tablecr'>\n";
 		$html .="<tr>\n<td>\n";
		$html .= stripslashes($bringback)."\n";
		$html .="</td>\n</tr>\n";
		$html .= "</table><br>\n";
		echo $html;
	}
}

function read_task ($Rid) {
	/*
	00 - Rid : Numéro unique d'identification de la demande
	01 - Acq : Statut de la demande 0 : non acquité 1 : Acquité  (In progress) 2 : Closed
	02 - Host : Hote source de la demande
	03 - Owner : Emetteur de la demande
	04 - OwnerMail : Mel de l'emetteur
	05 - Author : Responsable(s) du traitement du pb
	06 - Sector : secteur
	07 - Building : Batiment
	08 - Room : N° de la salle
	09 - Numcomp : N° du poste
	10 - Mark : Marque du PC
	11 - Os : système d'exploitation
	12 - Cat : catégorie de la demande
	13 - Content : Description de la demande
	14 - OpenTimeStamp : date heure de la demande
	15 - CloseTimeStamp : date et heure de la cloture de la demande
	16 - TakeTimeStamp : date heure de la prise en charge de la demande
	17 - BoosTimeStamp : date et heure de la relance
	18 - Nombre de relance
	*/
	global $DEBUG;
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
	$requete = mysql_query("SELECT * FROM maint_task WHERE (Rid='$Rid')");
	$row = mysql_fetch_row($requete);
 	$Owner			=	$row[3];
  	$OwnerMail		=	$row[4];
	$Author			=	$row[5];
	$Sector			=	$row[6];
	$Building		=	$row[7];
	$Room			=	$row[8];
	$NumComp		=	$row[9];
	$Mark			=	$row[10];
	$Os			=	$row[11];
	$Cat			=	$row[12];
 	$Content		=	$row[13];
  	$OpenTimeStamp          =	strftime("%d - %m - %Y &#233; %H h %M mn",$row[14]);
	$NumBoost		=	$row[18];
	return array ($Sector, $Owner, $OwnerMail, $Author, $Building, $Room, $NumComp, $Mark, $Cat, $Os, $Content, $OpenTimeStamp,$NumBoost);
}

function read_task_tmp ($Rid) {
	/*
	00 - Rid : Numéro unique d'identification de la demande
	01 - Acq : Statut de la demande 0 : non acquité 1 : Acquité  (In progress) 2 : Closed
	02 - Host : Hote source de la demande
	03 - Owner : Emetteur de la demande
	04 - OwnerMail : Mel de l'emetteur
	05 - Author : Responsable(s) du traitement du pb
	06 - Sector : secteur
	07 - Building : Batiment
	08 - Room : N° de la salle
	09 - Numcomp : N° du poste
	10 - Mark : Marque du PC
	11 - Os : système d'exploitation
	12 - Cat : catégorie de la demande
	13 - Content : Description de la demande
	14 - OpenTimeStamp : date heure de la demande
	15 - CloseTimeStamp : date et heure de la cloture de la demande
	16 - TakeTimeStamp : date heure de la prise en charge de la demande
	17 - BoosTimeStamp : date et heure de la relance
	18 - Nombre de relance
	*/
	global $DEBUG;
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
	$requete = mysql_query("SELECT * FROM maint_task WHERE (Rid='$Rid')");
	$row = mysql_fetch_row($requete);
 	$Owner			=	$row[3];
  	$OwnerMail		=	$row[4];
	$Author			=	$row[5];
 	$Content		=	$row[13];
  	$OpenTimeStamp          =	strftime("%d - %m - %Y &#233; %H h %M mn",$row[14]);
	return array ($Owner, $OwnerMail, $Author, $Content, $OpenTimeStamp);
}

function read_cr_task ($Rid) {
	/*
		01 - Rid : Numéro unique d'identification
		02 - TopRid : Rid d'attachement d'origine
		03 - Author : Emetteur
		04 - Content : Description, contenu du message
		05 - TimeStamp : date heure du CR  d'intervention
		06 - TimeLife : Durée de l'intervention
		07 - Cost : Cout des éventuels pièces de rechange
	*/
	global $DEBUG;
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
	$requete = mysql_query("SELECT * FROM maint_thread WHERE (Rid='$Rid')");
	$row = mysql_fetch_row($requete);
	$Rid			=	$row[0];
	$TopRid			=	$row[1];
 	$Content		=	$row[3];
	$TimeLife		=	$row[5];
	$Cost			=	$row[6];
	return array ($Rid,$TopRid,$Content,$TimeLife,$Cost);
}

function mail_to ($to, $Cc, $Subject, $Body, $From) {
	global $DEBUG;
	if ($DEBUG)  echo "DEBUG mail_to >> to : $to Cc : $Cc Subject : $Subject Body :$Body From: $From<br>";
	$mailHeaders = "From: $From\nCc: $Cc\n";
	mail ($to, $Subject, $Body, $mailHeaders);
}

function del_task ($Rid, $by) {
	global $DEBUG, $MAILMAINT;
	// Lecture de la demande
	list ( $Sector, $Owner, $OwnerMail, $Author, $Building, $Room, $NumComp, $Mark, $Cat, $Os, $Content, $OpenTimeStamp, $NumBoost) = read_task ($Rid);
	if ($DEBUG)  echo "DEBUG del_task >> $by $Owner, $OwnerMail, $Author, $Content, $OpenTimeStamp<br>";
	// Effacement de la demande
	$requete = mysql_query("DELETE FROM maint_task WHERE (Rid = '$Rid')");
	// Effacement des CR
	$requete = mysql_query("DELETE FROM maint_thread WHERE (TopRid = '$Rid')");
	// Préparation du mail
	$Subject =" [MaintInfo] Effacement de votre demande du $OpenTimeStamp";
	$Body = "Bonjour,\n
			Votre demande : \n
			$Content\n
			A été effacée par $by\n";
	// Poste du message d'effacement
	mail_to ($MAILMAINT, $OwnerMail, $Subject, $Body, $by);
}

function del_cr ($Rid) {
	// Effacement des CR
	$requete = mysql_query("DELETE FROM maint_thread WHERE (Rid = '$Rid')");
}


function take_task ($Rid, $Author, $AuthorMail) {
	global $DEBUG, $MAILMAINT;
	// Lecture de la demande
	list ( $Sector, $Owner, $OwnerMail, $Nul, $Building, $Room, $NumComp, $Mark, $Cat, $Os, $Content, $OpenTimeStamp, $NumBoost) = read_task ($Rid);
	// Positonnement de la date et heure de la prise en charge
        $TakeTimeStamp=strtotime (date("Y-m-d H:i:s"));
	if ($DEBUG)  echo "DEBUG take_task >> Author: $Author Owner: $Owner, OwnerMail: $OwnerMail, Content: $Content, OpenTimeStamp: $OpenTimeStamp, TakeTimeStamp: $TakeTimeStamp<br>";
	// UPDATE de la demande $Rid by $Author
	$requete = mysql_query("UPDATE maint_task SET Acq = '1' ,Author='$Author', TakeTimeStamp='$TakeTimeStamp' WHERE Rid = '$Rid'");
	// Préparation du mail
	$Subject =" [MaintInfo] prise en charge de votre demande du $OpenTimeStamp";
	$Body = "Bonjour,\n
			Votre demande : \n
			$Content\n
			A été prise en compte par $Author\n";
	// Poste d'un message d'avis de prise en compte de la demande
	mail_to ($MAILMAINT, $OwnerMail, $Subject, $Body, $AuthorMail);
}

function boost_task ($Rid) {
	global $MAILMAINT;
	// Lecture de la demande
	list ( $Sector, $Owner, $OwnerMail, $Author, $Building, $Room, $NumComp, $Mark, $Cat, $Os, $Content, $OpenTimeStamp, $NumBoost) = read_task ($Rid);
	// UPDATE de la demande
	$BoosTimeStamp=strtotime (date("Y-m-d H:i:s"));
	$NumBoost++;
	$requete = mysql_query("UPDATE maint_task SET BoosTimeStamp='$BoosTimeStamp' , NumBoost='$NumBoost' WHERE Rid = '$Rid'");
	// Préparation du mail
	$Subject =" [MaintInfo] Relance demande du $OpenTimeStamp";
	$Body = "Bonjour,\n
			Le ".date("d-m-Y H:i:s")." la demande : \n
			$Content\n
			&#233; été relancée !";
	// Poste d'un message d'avis de prise en compte de la demande
	mail_to ($MAILMAINT, $OwnerMail, $Subject, $Body, $OwnerMail);
}

function BoostAlert ($NumBoost) {
	if ( $NumBoost != 0 ) {
		for ($loop=0; $loop < $NumBoost; $loop++ )
			echo "<img src='Images/boost.png' width='18' height='18' border='0'>";
   	}
}


function VerifTime ($OpenTimeStamp, $BoosTimeStamp) {
	$Time = strtotime (date("Y-m-d H:i:s"));
	if ( $BoosTimeStamp != 0 ) $Time2Verif = $BoosTimeStamp;else $Time2Verif = $OpenTimeStamp;
	$delay = ($Time - $Time2Verif);
	if ($delay >= 86400) return true; else return false;
}



function table_alert ($message) {
		$table_alert = "<table class='table_alert'>\n";
		$table_alert .= "	<tr>\n";
		$table_alert .= "		<td height='200'>\n";
		$table_alert .= "			<div class='alert_msg'>$message</div>\n";
		$table_alert .= "		</td>\n";
		$table_alert .= "	</tr>\n";
		$table_alert .= "</table>\n";
		echo $table_alert;
}

function Aff_topo () {
  global $bat, $etage;
  // Liste des batiments
  $html .="<div class=\"subconfigcontainer\">";
  $html .= "&nbsp;B&#226;timent :&nbsp;&nbsp;&nbsp;";
  $html .= "<select name=\"bat\" onChange=\"location = this.options[this.selectedIndex].value;\">";
  // lecture de la table topologie pour affichage de la liste des batiments
  $loop=0;
  $result = @mysql_query("SELECT batiment from topologie ORDER BY batiment ASC");
  if ($result)
    while ($r = @mysql_fetch_array($result)) {
      if ( !isset ($bat ) ) $bat = $r["batiment"];
      $batiment[$loop] = $r["batiment"]; 
      if ( !isset( $batiment[$loop-1] ) || ( $batiment[$loop-1] != $r["batiment"] ) ) {
        $html .= "        <option value=\"config.php?conf=topo&bat=".$r["batiment"]."\"";
        if ( $bat == $r["batiment"] ) $html.= "selected";
        $html .= ">".$r["batiment"]."</option>\n";
      }
      $loop++;  
    }
  @mysql_free_result($result);
  $html .="</select>";
  // Liste des étages du batiment sélectionné
  $html .= " Etage :&nbsp;&nbsp;&nbsp;";
  $html .= "<select name=\"etage\" onChange=\"location = this.options[this.selectedIndex].value;\">";
  // lecture de la table topologie pour affichage de la liste des étages
  $loop=0;
  $result = @mysql_query("SELECT etage from topologie WHERE batiment='$bat' ORDER BY etage ASC");
  if ($result)
  while ($r = @mysql_fetch_array($result)) {
    if ( !isset ($etage) ) $etage = $r["etage"];
    $etage_[$loop] = $r["etage"]; 
    if ( !isset( $etage_[$loop-1] ) || ( $etage_[$loop-1] != $r["etage"] ) ) {
      $html .=  "      <option value=\"config.php?conf=topo&bat=".$bat."&etage=".$r["etage"]."\"";
      if ( $etage == $r["etage"] ) $html .=  "selected";
      $html .= ">".$r["etage"]."</option>\n";
    }    
    $loop++;  
  }
  @mysql_free_result($result); 
  $html .= "</select>"; 
  // Liste des salles correspondantes
  $html .= " Salle :&nbsp;&nbsp;&nbsp;";
  $html .= "<select name=\"salle\">";
  // lecture de la table topologie pour affichage de la liste des salles
  $loop=0;
  $result = @mysql_query("SELECT salle from topologie WHERE batiment='$bat' AND etage='$etage' ORDER BY salle ASC");
  if ($result)
  while ($r = @mysql_fetch_array($result)) {
    $salle_[$loop] = $r["salle"]; 
    if ( !isset( $salle_[$loop-1] ) || ( $salle_[$loop-1] != $r["salle"] ) ) {
      $html .= "      <option value=\"config.php?conf=topo&bat=".$bat."&etage=".$etage."&salle=".$r["salle"]."\"";
      if ( $salle == $r["salle"] ) $html .= "selected";
      $html .=  ">".$r["salle"]."</option>\n";
    }    
    $loop++;  
  }
  @mysql_free_result($result);
  $html .= "</select></div>";      
  
  return $html;
}