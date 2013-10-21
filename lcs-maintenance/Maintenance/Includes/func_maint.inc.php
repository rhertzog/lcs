<?php /* includes/func_maint.inc.php maj 07/10/2009 */
header ('Content-type" => "text/html; charset=utf-8');

function html() {
        $header = "<!DOCTYPE html\>\n";
	$header .= "<html>\n";
	$header .= "<head>\n";
        $header .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
       	$header .= "<title>Support Informatique</title>\n";
    	$header .= "<link type='text/css' rel='stylesheet' href='Style/maintenance.css'>\n";
    	
    	$header .= "<link rel='StyleSheet' href='Scripts/uniform/css/uniform.default.css' type='text/css' media='screen' />\n";
	   	$header .= "<link rel='StyleSheet' href='Style/cmxform.css' type='text/css' media='screen' />\n";
	   	$header .= "<link rel='StyleSheet' href='../../../libjs/jquery-ui/css/classic-blue-lcs/jquery-ui.css'' type='text/css' media='screen' />\n";
		$header .= "<link rel='stylesheet' href='Scripts/poshytip/tip-twitter/tip-twitter.css' type='text/css' />\n";
 //	   	$header .= "<link rel='StyleSheet' href='Style/ui.theme.css' type='text/css' media='screen' />\n";
   	
    	$header .= "<script type='text/javascript' src='../../../libjs/jquery/jquery.js'></script>";
    	$header .= "<script type='text/javascript' src='../../../libjs/jquery-ui/jquery-ui.js'></script>";
    	$header .= "<script type='text/javascript' src='Scripts/uniform/jquery.uniform.min.js'></script>";
    	$header .= "<script type='text/javascript' src='Scripts/jquery.form.js'></script>";
     	$header .= "<script type='text/javascript' src='Scripts/jquery.validate.js'></script>";
     	$header .= "<script type='text/javascript' src='Scripts/jquery.tablesorter.mod.js'></script>";
     	$header .= "<script type='text/javascript' src='Scripts/jquery.tablesorter.collapsible.js'></script>";
     	$header .= "<script type='text/javascript' src='Scripts/jquery.tablesorter.pager.js'></script>";
     	$header .= "<script type='text/javascript' src='Scripts/jquery.quicksearch.js'></script>";
     	$header .= "<script type='text/javascript' src='Scripts/poshytip/jquery.poshytip.min.js'></script>";
   		$header .= "<script type='text/javascript' src='Scripts/maint.js'></script>";

    	$header .= "<script type=\"text/javascript\">\n";
        $header .= "<!--\n";        
        $header .= "function auth_popup() {\n";
        $header .= "  window.focus();\n";
        $header .= "  auth_popupWin = window.open(\"Doc/Html/index.html\",\"auth_supinfo\",\"scrollbars=yes,resizable=yes,width=800,height=600\");\n";
        $header .= "  auth_popupWin.focus();\n";
        $header .= "}\n";
        $header .= "//-->\n";
        $header .= "</script>\n";        
        $header .= "<script type='text/javascript' charset='utf-8'>$(function(){ $('select, textarea, input:file').uniform() });</script>";
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

# if (!test_utf8($str)) $str = utf8_encode($str);
function test_utf8($str) {
	  if (is_array($str)) {
	     $str = implode('', $str);
	     // retourne FALSE si aucun caractere n'appartient au jeu utf8
	     return !((ord($str[0]) != 239) && (ord($str[1]) != 187) && (ord($str[2]) != 191));
	    } else {
	        // retourne TRUE
	        // si la chaine decoder et encoder est egale a elle meme
	        return (utf8_encode(utf8_decode($str)) == $str);
	    }    
}
//retourne la chaine en utf8 si elle ne l'est pas
function valid_utf8($str) {
	$rep = test_utf8($str) ? $str : utf8_encode($str);
	return $rep;
}
function dateFR( $time )
	{
	   setlocale( LC_TIME, "fr_FR.utf8" );
	   return strftime( "%d %b %Y" ,  $time  );
	}

#
#
#
function Aff_loading() {
		echo	"<div id=\"loadingContainer\"><div id=\"loading\">Chargement...</div></div>";
	}
function Aff_messIntro() {
	$mess = "<div class=\"tableintro divintro tableau\" style=\"\">\n";
	$mess .= "\t<img src=\"Style/img/information.png\" alt=\"Information\" />\n";
	$mess .= "\t<ul style=\"\">\n";
	$mess .= "\t\t<li style=\"\">Vous avez constat&eacute; un dysfonctionnement sur un des ordinateurs que vous utilisez et vous d&eacute;sirez l'intervention d'un technicien.&nbsp;</li>\n";
	$mess .= "\t\t<li style=\"\">Compl&eacute;tez le formulaire ci-dessous et votre demande sera prise en compte le plus rapidement possible.</li>\n";
	$mess .= "\t</ul>\n";
	$mess .= "\t<div style=\"float:right\">L'&eacute;quipe de maintenance informatique.</div>\n";
	$mess .= "\t<br class=\"cleaner\"/>\n";
	$mess .= "</div>\n";
	echo $mess;
}

function Aff_mnu($mode) {
	global $uid;
	$header .= "<div class='tabletitre'>\n";
	$header .= "   <div class='titre'>\n";
	$header .= "   <img src=\"Images/logo_maint.png\" alt=\"\" style=\"margin:0 10px;\"/>\n";
	$header .= "   <a href=\"./\" title=\"Accueil\">Support Info.</a>\n";
	$header .= "   </div>\n";
	$header .= "		<div class=\"separate\" style=\"height:10px\"></div>\n";
	$header .= "     <div class='mnu'>\n";
		/**
		 * maj-2.4.8
		 * ajout d'une icone pour chaque item du menu
		*/
	if ($mode=="user") $header.="      <a class='menu_haut demand_sup' href='demande_support.php' title='Effectuer une demande de support'><img src='Style/img/hand_point_090.png'/>&nbsp;Demande de support&nbsp;</a>";
        else $header.="<a class='menu_haut demand_sup' href='demande_support.php' title='Signaler une intervention non sollicit&#233;e'><img src='Style/img/hand_point_090.png'/>&nbsp;Intervention non sollicit&eacute;e&nbsp;</a>";
	$header.= "<a class='menu_haut wait' href='index.php?mnuchoice=wait' title='Les demandes en attente de prise en charge'><img src='Style/img/en-attente.png'/>En attente</a>&nbsp;";
	$header.="<a class='menu_haut myspool' href='index.php?mnuchoice=myspool' title='Vos demandes en cours de traitement'><img src='Style/img/bug_user.png'/>Votre Encours&nbsp;</a>";
	if ($mode=="user") $header.= "<a class='menu_haut myhistorique' href='index.php?mnuchoice=myhistorique' title='Historique de vos demandes'><img src='Style/img/en-attente.png'/>Votre historique&nbsp;</a>";
	if ($mode=="team" || $mode=="team_CR") {
		$header.= "<a class='menu_haut spool' href='index.php?mnuchoice=spool' title='En cours g&#233;n&#233;ral'><img src='Style/img/bug_go.png'/>Encours gen.&nbsp;</a>";
		$header.= "<a class='menu_haut myhistorique ' href='index.php?mnuchoice=myhistorique' title='Historique de vos interventions'><img src='Style/img/myhistorik.png'/>Votre historique&nbsp;</a>";
		$header.= "<a class='menu_haut historique' href='index.php?mnuchoice=historique' title='Historique g&#233;n&#233;ral des interventions'><img src='Style/img/clock_history_frame.png'/>Historique gen.&nbsp;</a>";
	}
        // Affichage du menu de configuration
        if (is_admin("system_is_admin",$uid)=="Y")
          $header.="<a class='menu_haut config' href='config.php?conf=mail' title=\"Configurer l'application\"><img src='Style/img/setting_tools.png'/>Configuration&nbsp;</a>";
        // Affichage du menu d'aide
        $header.= "<a class='menu_haut help' href='#' onClick='auth_popup(); return false' title='Documentation'><img src='Style/img/help.png'/></a>";
	$header .= "     <br class=\"cleaner\"/>\n";
	$header .= "     </div>\n";
	$header .= "</div>\n";
	$header .= "<div class=\"separate\" style=\"height:10px\"></div>\n";
		/**
		 * on ajoute une div content pour le background
		 * 
		*/
       	$header .= "<div id=\"content\">\n";
        $header .= "<script>
       	$('.mnu a').poshytip({
			className: 'tip-twitter',
			showTimeout: 1,
			alignTo: 'target',
			alignX: 'center',
			alignY: 'bottom',
			offsetY: 5,
			allowTipHover: true,
			fade: true,
			slide: true
		});
		</script>";

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
           if (  $mnuchoice=="historique") {
              $header .= "<div class='order' id=\"btnHystoric\">";
              $header .= "<img class=\"list disabled\" src=\"Style/img/24/list.png\" alt=\"\"/>&#160;&#160;";
              $header .= "<img src=\"Style/img/24/table.png\" class=\"tab\" alt=\"\"/>";
               $header .= "<img class=\"diag\" src=\"Style/img/24/diagramm.png\" alt=\"\"/>&#160;&#160;";
             $header .= "</div>"; }
            if (  $mnuchoice=="spool" || $mnuchoice=="historique" )
              $header .= "<div class='order'><A class='menu_barre' href='index.php?mnuchoice=$mnuchoice&tri=prisencharge'>Prise en charge par</A><img alt=\"DESC\" src=\"Images/bullet_arrow_down.png\" hspace=\"4\" border=0></div>";
            $header .= "<div class='order'><A class='menu_barre' href='index.php?mnuchoice=$mnuchoice&tri=emetteur'>Emetteur</A><img alt=\"ASC\" src=\"Images/bullet_arrow_up.png\" hspace=\"4\" border=0></div>";
            $header .= "<div class='order'><A class='menu_barre'  href='index.php?mnuchoice=$mnuchoice&tri=date'>Date</A><img alt=\"DESC\" src=\"Images/bullet_arrow_down.png\" hspace=\"4\" border=0></div></td>\n";            
         #   $header .= "<script>affHistory();</script>\n";            
          }       
        } else {
        // mode user
          if ( ereg ("^Votre", $message) ) {
            $header .= "<div class='order'><A class='menu_barre'  href='index.php?mnuchoice=$mnuchoice&tri=date'>Date</A><img alt=\"DESC\" src=\"Images/bullet_arrow_down.png\" hspace=\"4\" border=0></div>\n";                                
            $header .= "<div class='order'><A class='menu_barre' href='index.php?mnuchoice=$mnuchoice&tri=prisencharge'>Prise en charge par</A><img alt=\"DESC\" src=\"Images/bullet_arrow_down.png\" hspace=\"4\" border=0></div></td>";
          }
        }
	$header .= "</tr>\n";
	$header .= "</table>\n";
	$header .= "<div id=\"loadingContainer\"><div id=\"loading\">Chargement...</div></div>\n";
	
	echo $header;
}

function Is_feed ($mode, $filter, $message) {
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
	$requete = mysql_query("SELECT * FROM maint_task WHERE ($filter)");
	$row = mysql_fetch_row ($requete);
	if (!$row)  {
		table_alert ($message);
	//	Aff_messIntro();
		return false;
	} else return true;
}

function Aff_Intro ($OpenTimeStamp, $Sector, $Building, $Room, $NumComp, $Mark, $Os) {

	// Affichage des interventions
 	$html = "<table class='tableint'>\n";
  	$html .= "<tr class='msg_intro'>\n<td>\n";
	//$html .= "<em>";
	// les champs vides ne sont pas affichÃ©s
 	if (isset ($OpenTimeStamp) ) $html .= "Le ".strftime("%d - %m - %Y &#224; %H h %M mn",$OpenTimeStamp);
		/**
		 * maj-2.4.8
		 * on passe les contenus dans des td
		*/
	$html .="</td><td>Secteur : <span>$Sector</span></td><td> Bat : <span>$Building</span></td><td>Salle : <span>$Room</span> </td><td>Poste : <span>$NumComp</span>";
	if ( $Mark !="" ) $html .="</td><td>&nbsp;<span>Marque</span> : $Mark";
	if (isset ($Os) ) $html .="</td><td>&nbsp;<span>Syst&egrave;me d'exploitation</span> : $Os";
	//$html .="</em>\n";
	$html .="</td></tr>\n";
  	$html .= "<tr>\n<td colspan='7'>\n";
	echo $html;
}

function Aff_feed_wait ($mode, $filter,$tri) {
	#	$mode = "team" ou "user"
	#	$filter = dÃ©finit le filtre de la requet SQL

	global $mnuchoice;

	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
        $sens = "ASC";
	if ( $tri == "emetteur") $tri="Owner";
	elseif ( $tri == "prisencharge") $tri="Author";
	else { $tri="OpenTimeStamp"; $sens = "DESC"; }
	$requete = mysql_query("SELECT * FROM maint_task WHERE ($filter) ORDER BY $tri $sens ");
 	while($row = mysql_fetch_array($requete)) {
  		$Rid					= 	$row["Rid"];
		$Host					= 	$row["Host"];
  		$Owner				=	valid_utf8( $row["Owner"] );
    	$OwnerMail			=	$row["OwnerMail"];
		$Sector				=	valid_utf8( $row["Sector"] );
		$Building			=	valid_utf8( $row["Building"] );
  		$Room			    =	valid_utf8( $row["Room"] );
    	$NumComp			=	$row["NumComp"];
		$Mark	 			=	$row["Mark"];
		$Os			        = 	$row["Os"];
		$Cat			        =	$row["Cat"];
  		$Content		    =	 valid_utf8($row["Content"]);
    	$OpenTimeStamp=	$row["OpenTimeStamp"];
		$BoosTimeStamp	=	$row["BoosTimeStamp"];
		$NumBoost			=	$row["NumBoost"];

		Aff_intro($OpenTimeStamp, $Sector, $Building, $Room, $NumComp, $Mark, $Os);
		/**
		 * maj-2.4.8
		 * on suprime les <br> pour un attribut css margin
		 * on passe les contenus dans des spans et div pour le comment
		*/
	//	echo "<br>\n";
		if (isset ($Owner) ) echo "<span class='emetteur'>Emetteur  <strong>$Owner</strong></span> ";
		if ( isset($OwnerMail) ) echo "<span class='email'>&nbsp;&nbsp;&nbsp;<i> ($OwnerMail ) </i></span>";
		if ( isset($Content) ) echo "<div class='question_is'><span class=\"objet\">Objet:&nbsp;</span>".nltobr($Content)."</div>";
		BoostAlert ( $NumBoost );
		if ($mode == "team") {
		/**
		 * maj-2.4.8
		 * ajout des icones
		*/
			echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=take&mnuchoice=$mnuchoice'><img src='Style/img/16/accept.png' alt=''/>&nbsp;Prendre en charge&nbsp;</a>&nbsp;";
			echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=del_task&mnuchoice=$mnuchoice'><img src='Style/img/16/delete.png' alt=''/>&nbsp;Supprimer&nbsp;</a>&nbsp;";
		} else {
			echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=del_task'><img src='Style/img/16/delete.png' alt=''/>&nbsp;Supprimer&nbsp;</a>&nbsp;";
			echo "<a class='menu_feed' href='edit_demande.php?Rid=$Rid'><img src='Style/img/16/bug_edit.png' alt=''/>&nbsp;Modifier</a>&nbsp;";
			if ( VerifTime ($OpenTimeStamp, $BoosTimeStamp) )
				echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=relance'><img src='Style/img/16/arrow_refresh.png' alt=''/>&nbsp;Relancer</a>&nbsp;";
		}
		/**
		 * maj-2.4.8
		 * on suprime les <br> pour un attribut css margin
		*/
		//echo "</td>\n</tr>\n</table>\n<br>\n";
		echo "</td>\n</tr>\n</table>\n";
	}  //fin du while
}

function Aff_feed_take ($mode, $filter,$tri) {
	#	$mode = "team" ou "team_CR" ou "user"
	#	$filter = dÃ©finit le filtre de la requet SQL
	global $mnuchoice;
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
        $sens = "ASC";
	if ( $tri == "emetteur") $tri="Owner";
	elseif ( $tri == "prisencharge") $tri="Author";
	else { $tri="OpenTimeStamp"; $sens = "DESC"; }
	$requete = mysql_query("SELECT * FROM maint_task WHERE ($filter) ORDER BY $tri $sens ");
 	while($row = mysql_fetch_array($requete)) {
  		$Rid						= 	($row["Rid"]);
		$Host						= 	($row["Host"]);
  		$Owner					=	valid_utf8($row["Owner"]);
    	$OwnerMail				=	($row["OwnerMail"]);
		$Author					=	valid_utf8($row["Author"]);
		$Sector					=	valid_utf8($row["Sector"]);
		$Building				=	valid_utf8($row["Building"]);
  		$Room					=	valid_utf8($row["Room"]);
    	$NumComp				=	($row["NumComp"]);
		$Mark					=	valid_utf8($row["Mark"]);
		$Os						=	valid_utf8($row["Os"]);
		$Cat						=	valid_utf8($row["Cat"]);
  		$Content		    	=	valid_utf8($row["Content"]) ;
    	$OpenTimeStamp		=	$row["OpenTimeStamp"];
		$TakeTimeStamp		=	$row["TakeTimeStamp"];
		$BoosTimeStamp		=	$row["BoosTimeStamp"];
		$NumBoost				=	$row["NumBoost"];

		Aff_intro($OpenTimeStamp, $Sector, $Building, $Room, $NumComp, $Mark, $Os);

	//	echo"<br>\n";
		if (isset ($Owner) ) echo "<div><span class='emetteur'>Emetteur  <strong>$Owner</strong> </span>";
		if ( isset($OwnerMail) ) echo "<span class='email'>&nbsp;&nbsp;&nbsp;<i> ($OwnerMail ) </i></span></div>";
		
		if ( isset($Content) ) echo "<br /><span class='objet'>Objet :</span><div class='question_is'>&nbsp;".nltobr($Content)."</div>";
		BoostAlert ( $NumBoost );
		#if ( $BoosTimeStamp != 0 ) echo "<img src='images/boost.png' width='18' height='18' border='0'>";
		if ( $mode == "team" ) {
			if ( $mnuchoice == "myspool" ) {
				echo "<div style=\"float:right;\"><a class='menu_feed' href='bring_back.php?Rid=$Rid'><img src='Style/img/16/comment_edit.png' alt=''/>&nbsp;Rapport d'intervention</a>&nbsp;";
				echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=del_task&mnuchoice=$mnuchoice'><img src='Style/img/16/delete.png' alt=''/>&nbsp;Supprimer&nbsp;</a>&nbsp;</div>";
				echo "<div><em class='msg_take'>Prise en charge le </em><em class='author_take'>".strftime("%d - %m - %Y &#233; %H h %M mn",$TakeTimeStamp)."</em>&nbsp;</div>";
			} else {
				echo "<em class='msg_take'>Prise en charge le </em><em class='author_take'>".strftime("%d - %m - %Y &#233; %H h %M mn",$TakeTimeStamp)."</em><em class='msg_take'>&nbsp;par :&nbsp;</em><em class='author_take'>$Author</em>\n";
			}
		} elseif ( $mode =="team_CR" ) {
			echo "<em class='msg_take'>Prise en charge le </em><em class='author_take'>".strftime("%d - %m - %Y &#233; %H h %M mn",$TakeTimeStamp)."</em>";
		} elseif ( $mode =="user" ) {
			if ( VerifTime ($OpenTimeStamp, $BoosTimeStamp) )
				echo "<a class='menu_feed' href='index.php?Rid=$Rid&action=relance&mnuchoice=$mnuchoice'>Relancer</a>&nbsp;";
			echo "<em class='msg_take'>Votre demande a &eacute;t&eacute; prise en charge le</em> <em class='author_take'>".strftime("%d - %m - %Y &#224; %H h %M mn",$TakeTimeStamp)."</em><em class='msg_take'>&nbsp;par :&nbsp;</em><em class='author_take'>$Author</em>\n";
		}
		echo "</td>\n</tr>\n</table>\n";
		// Recherche si il existe un feed de rapport d'intervention
		$requete1 = mysql_query("SELECT * FROM maint_thread WHERE (TopRid='$Rid') ORDER BY TimeStamp");
		// Affichage du feed du rapport d'intervention
		while($row1 = mysql_fetch_array($requete1)) {
			$Rid			=	$row1["Rid"];
			$Content1		=	valid_utf8($row1["Content"]);
			$TimeStamp1		=	strftime("%d - %m - %Y &#233; %H h %M mn",$row1["TimeStamp"]);
			$TimeLife		=	$row1["TimeLife"];
			$Cost			=	$row1["Cost"];
			// En tÃªte de la rÃ©ponse
			$html = "<div><table class='tablefeed'>\n";
 			$html .="<tr><td class='tablefeed'>\n";
			$html .= "Intervention du $TimeStamp1&nbsp;\n";
			if ( $TimeLife > 0 ) $html .= "| dur&eacute;e $TimeLife mn&nbsp;";
			if ( $Cost> 0 ) $html .= "| Co&ucirc;t des pi&egrave;ces  $Cost &#8364;&nbsp;";
			$html .="</td></tr>\n";
			/**
			 * fin du container / content
			 * ANNULE : replace avant le pied de page
			*/
			//$html .= "</table></div></div>\n";
			$html .= "</table></div>\n";
			// Reponse
			$html .= "<div><table class='tablecr'>\n";
 			$html .="<tr><td class='tablecr'>\n";
			$html .= nltobr(stripslashes($Content1));
			if ( $mode == "team" && $mnuchoice=="myspool") {
				$html .= "<br><a class='menu_feed' href='index.php?Rid=$Rid&action=del_cr&mnuchoice=$mnuchoice'><img src='Style/img/16/delete.png' alt=''/>&nbsp;Supprimer&nbsp;</a>&nbsp;\n";
				$html .= "<a class='menu_feed' href='bring_back_mod.php?Rid=$Rid'><img src='Style/img/16/bug_edit.png' alt=''/>&nbsp;Modifier</a>&nbsp;\n";
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
	#	$filter = dÃ©finit le filtre de la requet SQL
	global $mnuchoice;
	
	echo "<div id=\"contentHystoric\">";
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
        $sens = "ASC";
	if ( $tri == "emetteur") $tri="Owner";
	elseif ( $tri == "prisencharge") $tri="Author";
	else { $tri="OpenTimeStamp"; $sens = "DESC"; }
	$requete = mysql_query("SELECT * FROM maint_task WHERE ($filter) ORDER BY $tri $sens LIMIT 100 ");
 	while($row = mysql_fetch_array($requete)) {
  		$Rid			= 	$row["Rid"];
		$Host			= 	$row["Host"];
  		$Owner			=	valid_utf8($row["Owner"]);
    	$OwnerMail		=	$row["OwnerMail"];
		$Author			=	valid_utf8($row["Author"]);
		$Sector			=	valid_utf8($row["Sector"]);
		$Building		=	valid_utf8($row["Building"]);
  		$Room			=	valid_utf8($row["Room"]);
    	$NumComp		=	$row["NumComp"];
		$Mark			=	$row["Mark"];
		$Os			=	$row["Os"];
		$Cat			=	$row["Cat"];
  		$Content		    =	 valid_utf8($row["Content"]) ;
    	$OpenTimeStamp          =	$row["OpenTimeStamp"];
		$TakeTimeStamp          =	strftime("%d - %m - %Y &#233; %H h %M mn",$row["TakeTimeStamp"]);
		$CloseTimeStamp         =	strftime("%d - %m - %Y &#233; %H h %M mn",$row["CloseTimeStamp"]);
		$BoosTimeStamp          =	$row["BoosTimeStamp"];
		$NumBoost		=	$row["NumBoost"];
		
		Aff_intro($OpenTimeStamp, $Sector, $Building, $Room, $NumComp, $Mark, $Os);

	//	echo"<br>\n";
		if (isset ($Owner) ) echo "<span class='emetteur'>Emetteur  <strong>$Owner</strong> </span>";
		if ( isset($OwnerMail) )  echo "<span class='email'>&nbsp;&nbsp;&nbsp;<i> ($OwnerMail ) </i></span>";
		if ( isset($Content) )  echo "<div class='question_is'><span class='objet'>Objet :</span>&nbsp;".nltobr($Content)."</div>";
		BoostAlert ($NumBoost);
		if ( $mode=="team" )
			echo "<em class='msg_take'>Cette demande a &eacute;t&eacute; prise en charge le </em>";
		else
			echo "<em class='msg_take'>Votre demande a &eacute;t&eacute; prise en charge le </em>";
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
			// En tÃªte de la rÃ©ponse
			$html = "<div><table class='tablefeed'>\n";
 			$html .="<tr><td class='tablefeed'>\n";
			$html .= "Intervention du $TimeStamp1&nbsp;\n";
			if ( $TimeLife > 0 ) $html .= "| dur&eacute;e $TimeLife mn&nbsp;";
			if ( $Cost> 0 ) $html .= "| Co&ucirc;t des pi&egrave;ces  $Cost &#8364;&nbsp;";
			$html .="</td></tr>\n";
			$html .= "</table></div>\n";
			// RÃ©ponse
			$html .= "<div><table class='tablecr'>\n";
 			$html .="<tr><td class='tablecr'bla>\n";
			$html .= nltobr(stripslashes(valid_utf8($Content1)));
			$html .="</td></tr>\n";
			$html .= "</table></div>\n";
			echo "$html";
		}
		echo "<br>";
	}  //fin du while
		echo "</div>";
}

// affiche l'entete d'un tableau
function Aff_tHead($ths) {
	$html  = "<table class=\"tabinfo tablesorter\" style=\"font-size:10px\"  cellspacing=\"0\"><thead><tr>";
	foreach($ths as $k=>$th) $html .= "<th>".$th."</th>\n";
	$html .= "</tr></thead>";
	echo $html;
}

// affiche le corps d'un tableau
function Aff_tBody($tds) {
	$html  = "<tbody style=\"font-size:10px\"><tr>";
	foreach($tds as $td) $html .= "<td>".$td."</td>\n";
	$html .= "</tr></tbody>";
	echo $html;
}

// affiche le piedd'un tableau
function Aff_tFoot($tfs) {
	$html  = "<tfoot style=\"font-size:10px\"><tr>";
	foreach($tfs as $tf) $html .= "<tf>".$tf."</tf>\n";
	$html .= "</tr></tfoot>";
	echo $html;
}


function Aff_feed_closeTab ($mode, $filter,$tri) {
	#	$mode = "team" ou "user"
	#	$filter = dÃ©finit le filtre de la requet SQL
	global $mnuchoice;
	// Recherche dans la table maint_task si il existe des demandes en attente de traitement
	$filter= mktime(0, 0, 0, 10, 0, 2010);
   $sens = "ASC";
	if ( $tri == "emetteur") $tri="Owner";
	elseif ( $tri == "prisencharge") $tri="Author";
	else { $tri="OpenTimeStamp"; $sens = "DESC"; }
		$cols = array(	
			"Rid"=>"NÂ°", 
		//	"Host"=>"Adresse", 
			"Owner"=>"Emetteur", 
			"Author"=>"Trait&#233;&#160;par", 
		//	"Sector"=>"Secteur", 
		//	"Building"=>"B&acirc;timent", 
			"Room"=>"Salle", 
			"NumComp"=>"Ordi.", 
			"Mark"=>"Marque", 
			"Os"=>"Os", 
			"Cat"=>"Origine", 
			"Content"=>"Demande", 
			"OpenTimeStamp"=>"Date", 
			"TakeTimeStamp"=>"PeC.", 
			"CloseTimeStamp"=>"Cl&ocirc;ture",
			"BoosTimeStamp"=>"Relance"/*, 
			"NimBoost"=>"Nbre de Relances"*/,
			"TimeLife"=>"Temps",
			"Cost"=>"Co&#251;t"
		 );
		Aff_tHead($cols );
	$requete = mysql_query("SELECT * FROM maint_task WHERE (OpenTimeStamp>$filter AND Acq='2' ) ORDER BY $tri $sens");
 	while($row = mysql_fetch_array($requete)) {
 		$Rid=$row["Rid"];
		$cout=0;
		$requete1 = mysql_query("SELECT * FROM maint_thread WHERE (TopRid='$Rid')");
		// Affichage du feed du rapport d'intervention
		$comments="";
		while($row1 = mysql_fetch_array($requete1)) {
			$timelife = $row1['TimeLife'] == 0 ? 5 : $timelife+$row1['TimeLife'];
			$cout = $row1['Cost'] == 0 ? $cout : $cout+$row1['Cost'];
			$comments .= "<div class=\"bold\">R&#233;ponse du ".dateFR( $row1['TimeStamp'])."</div>";
			$comments .= "<div >".$row1['Content']."</div><hr/>";
		}
		$html .=  "<tr>";
		$tempsPasse = intval($row['CloseTimeStamp']) - intval($row['TakeTimeStamp']);
		foreach($cols as $k=>$col) {
			$hTd = "<td ";
			if( $k=="Rid")
				$hTd .= " class=\"collapsible\" rowspan=\"2\"";
			if( $k=="Owner" || $k=="Author" || $k=="Room" || $k=="NumComp" || $k=="Os" || $k=="Mark" )
				$hTd .= " rowspan=\"2\"";
			if( $k=="Cat" )
				$hTd .= " class=\"collapsible_alt\" rowspan=\"2\"";
		//	if($col=="OwnerMail")
			if($k=="Owner") $hTd .= " title=\"".$row['OwnerMail']."\">".$row[$k]."</td>";
			else if($k=="Room") $hTd .= "title=\"Secteur : ".$row['Sector'].", Bat : ".valid_utf8($row['Bat'])."\">".valid_utf8($row[$k]) ."</td>";
			else if($k=="BoosTimeStamp" ){ $rl=$row[$k] ==0?0:1;$hTd .= " title=\"Date : ".strftime("%d-%m-%Y - %H.%M",$row["BoosTimeStamp"])."\">".$rl."</td>";}
			else if($k=="Content") {
				$ctnt = test_utf8( $row[$k] ) ? $row[$k] : utf8_encode($row[$k]); 
				$hTd .= "title=\"Secteur : ".$row['Sector'].", Bat : ".$row['Bat']."\" style=\"width: 265px;\">".$ctnt."</td>";
			}
			else if($k=="OpenTimeStamp") $hTd .= "> ".dateFR( $row["OpenTimeStamp"])." </td>";
			else if($k=="CloseTimeStamp") $hTd .= "> ".dateFR( $row["CloseTimeStamp"])." </td>";
			else if($k=="TakeTimeStamp") $hTd .= "> ".dateFR( $row["TakeTimeStamp"])." </td>";
			else if($k=="TimeLife") $hTd .= ">".intval($timelife)." mn </td>";
			else if($k=="Cost") $hTd .= ">".$cout." &#128;</td>";
			else {
				$ctnt = test_utf8( $row[$k] ) ? $row[$k] : utf8_encode($row[$k]); 
				$hTd .=   ">".$ctnt ."</td>\n";
			}
			$html .=   $hTd;
		}
		$html .= "</tr>";
		$html .= "<tr  class=\"expand-child\">";
		$html .= "<td style=\"display: none;\" colspan=\"7\">";
		$html .= test_utf8( $comments ) ? $comments : utf8_encode( $comments );
		$html .= "</td></tr>";
	}  //fin du while
		$html .= "</tbody>";
		$html .= "</table>";
		echo $html;
}

/*
 * bilan
 * Contenu de la table maint_thread :
 * Rid 		: id unique
 * TopRid 	: id de la demande mere
 * Author 	: auteur du comment
 * Content 	: comment
 * TimeStamp 	: date
 * TimeLife 	: duree de l'intervention
 * Cost : cout
*/
function Aff_bilan($start, $end) {
	$mtext=array(
	"01" => "Janvier",
	"02" => "F&#233;vrier",
	"03" => "Mars",
	"04" => "Avril",
	"05" => "Mai",
	"06" => "Juin",
	"07" => "Juillet",
	"08" => "Ao&#251;t",
	"09" => "Septembre",
	"10" => "Octobre",
	"11" => "Novembre",
	"12" => "D&#233;cembre"
	);
	$start = isset($start) ? $start : 0;
	$end = isset($end) ? $end: time();
	global $mnuchoice;
	$html  = "";
	$i = $tempsPasse = $tempsTotal = $t = 0;
	$posleft = 10;
	$thisTmpMonth=$maintMan='';
	$maintMen = array();
	
	$html .= "<div id=\"diagramm\">";
	$requete = mysql_query("SELECT * FROM maint_task WHERE (OpenTimeStamp >1277229651 AND Acq='2' )  ORDER BY Rid ASC");
 	while($row = mysql_fetch_array($requete)) {
 		$Rid=$row["Rid"];
 		$m=intval( strftime("%m",$row["OpenTimeStamp"]) );
		$thisMonth=$m;
		// Recherche si il existe un feed de rapport d'intervention
		$requete1 = mysql_query("SELECT * FROM maint_thread WHERE (TopRid='$Rid')");
		// Affichage du feed du rapport d'intervention
		$timelife=$cout=0;
		while($row1 = mysql_fetch_array($requete1)) {
					$timelife = $row1['TimeLife'] == 0 ? $timelife+5 : $timelife+$row1['TimeLife'];
					$cout = $row1['Cost'] == 0 ? $cout : $cout+$row1['Cost'];
		}
		if ( $thisMonth != $thisTmpMonth) 
		{
			if ($i!=0) {
				$t= $tempsMonth>59 ? round($tempsMonth/60,2)."H" : $tempsMonth."mn";
			//	$tempsTotal +=$tempsMonth;
				$html .=  $nbReq." &#160;&#160;requ&#234;tes&#160;&#160; ".$t."  </div>";
				$tempsMonth=0;
			}
			$html .= "<div class=\"float_left month\" style=\"left:".$posleft."px;\">". $mtext[strftime("%m",$row["OpenTimeStamp"])];
			$nbThisReq=$nbReq; $nbReq=0;
			$tempsMonth = $coutMonth = 0;
			$posleft+=51;
		}
		$tr= $timelife>59 ? round($timelife/60,2)."H" : $timelife."mn";
		$html .= "<hr style=\"\" title=\"Requ&#234;te ".$Rid." - Temps pass&#233; : ".$tr." \"/>";
		$tempsMonth =  $tempsMonth + $timelife ;
		$coutMonth =  $coutMonth + $cout;
		$thisTmpMonth=$thisMonth;//Important
		$tempsTotal = $tempsTotal +$timelife;
		$maintMen[$row['Author']] = $row['Author'];
//		$maintMen[$row['Author']]['pec'] ==1 ? $maintMen[$row['Author']]['pec'] = intval($maintMen[$row['Author']]['pec'])+1:$maintMen[$row['Author']]['pec']=1;
		$coutTotal = $coutTotal +$cout;
		$nbReq++;
		$i++;
 	}
	$t= $tempsMonth>59 ? round($tempsMonth/60,2)."H" : $tempsMonth."mn";
	$html .= $nbReq." &#160;&#160;requ&#234;tes&#160;&#160; ".$t."</div>";
	$html .= "</div></div>";
	$ttReq= "<div class=\"tableau tableint\">";
	$ttReq.= "<h3 class=\"subconfigsubtitle\"><img src=\"Style/img/24/diagramm.png\"/>&#160;Bilan de l'ann&#233;e en cours</h3>";
	$ttReq.= "<div class=\"bilan\">";
	$ttReq.= "<ul style=\"text-align:left\">";
	$ttReq.= "<li>Nombre total de requ&#234;tes : ".$i."</li>";
	$ttReq.="<li>Temps total d'intervention : ".intval($tempsTotal/60)."Heures </li>";
	$tempsMoyen= "";
	$ttReq.="<li>Temps moyen d'intervention par  requ&#234;te : ". round((intval($tempsTotal)/intval($i)),2)." mn.</li>";
	$ttReq.="<li>Co&#251;t total (mat&#233;riel ) : ". $coutTotal." &#128;.</li>";
	$ttReq.="</ul>";
	$ttReq.= "</div>";
	
	$ekip  = "<div class=\"tableau tableint\">";
	$ekip .= "<h3 class=\"subconfigsubtitle\"><img src=\"Style/img/24/diagramm.png\"/>&#160;Intervenants</h3>";
	$ekip .= "<div class=\"bilan\">";
	$ekip .= "<ul>";
	foreach($maintMen as $k=>$maintMan){
		$req = mysql_query("SELECT COUNT(*) as Count FROM maint_task WHERE (OpenTimeStamp >1277229651 AND Acq='2' AND Author='$maintMan')");
		if ($req)
		{
		$res = mysql_fetch_array($req);
		}		
		$ekip .= "<li>".$maintMan." : ".$res["Count"]." demandes prises en charge.</li>";
	}
	$ekip .= "</ul>";
	$ekip .= "</div>";
 	$resp = $ttReq.$html.$ekip;
 	
 	return $resp;
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
		$html = "<form action='bring_back.php?Rid=$Rid' method='POST'>\n";
		$html .= "\t<div class='tableau tableint'>\n";
		$html .= "\t\t<div class='fieldcontainer'>\n";
		$html .= "\t\t\t<label for='bringback'>Texte du rapport :\n";
		$html .= "\t\t\t<textarea name='bringback' id='bringback' wrap='physical' rows='6' cols='100'></textarea>\n";
		$html .= "\t\t</div>\n";
		$html .="<table>";
		$html .= "<tr><td>Dur&eacute;e de l'intervention&nbsp;<input type='text' name='TimeLife' size='2' maxlength='2'>";
		$html .="&nbsp;<select name='unit'>\n<option>mn</option>\n<option>h</option>\n</select>\n</td>\n";
		$html .= "<td align='left'>Co&ucirc;t des piÃ¨ces de rechange&nbsp;<input type='text' name='Cost' size='4' maxlength='4'>&nbsp;&#8364;&nbsp;</td>\n";
		$html .= "<td align='left'>Cloturer l'intervention&nbsp;<input type='checkbox' name='Close' value='2'></td>\n";
		$html .= "</tr></table>\n";
		$html .= "\t\t</div>";
		$html .= "<div class='tableau tableint tablenul' style='text-align:center;'>";
		$html .= "<input type='submit' value='Poster' name='submit' class='button'/>\n";
		$html .= "<input type='hidden' value='true' name='post'/>\n";
		$html .= "<input type='reset' value='Recommencer' name='reset' class='button'/>\n";
		$html .= "\t\t</div>";
		$html .= "</form>\n";
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
		$html .= "<tr><td class='tableau'><label for='TimeLife'>Dur&eacute;e de l'intervention</label>&nbsp;<input type='text' id='TimeLife' name='TimeLife' Value='$TimeLife' size='2' maxlength='2'>";
		$html .="&nbsp;<select name='unit'>\n<option>mn</option>\n<option>h</option>\n</select>\n</td>\n";
		$html .= "<td class='tableau'><label for='Cost'>Co&ucirc;t des piÃ¨ces de rechange</label>&nbsp;<input type='text' id='Cost' name='Cost' value='$Cost' size='4' maxlength='4'>&nbsp;&#8364;&nbsp;</td>\n";
		$html .= "<td class='tableau'><label for='Close'>Cloturer l'intervention</label>&nbsp;<input type='checkbox' id='Close' name='Close' value='2'></td>\n";
		$html .= "</tr><tr><td rowspan='1' colspan='3' class='tablenul' style='text-align:center'>\n";
		$html .= "<input type='submit' value='Poster' name='submit' class='button'>\n";
		$html .= "<input type='hidden' value='true' name='post'>\n";
		$html .= "<input type='hidden' value='$Rid' name='RidCR'>\n";
		$html .= "<input type='reset' value='Recommencer' name='reset' class='button'>\n";
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
	00 - Rid : NumÃ©ro unique d'identification de la demande
	01 - Acq : Statut de la demande 0 : non acquitÃ© 1 : AcquitÃ©  (In progress) 2 : Closed
	02 - Host : Hote source de la demande
	03 - Owner : Emetteur de la demande
	04 - OwnerMail : Mel de l'emetteur
	05 - Author : Responsable(s) du traitement du pb
	06 - Sector : secteur
	07 - Building : Batiment
	08 - Room : NÂ° de la salle
	09 - Numcomp : NÂ° du poste
	10 - Mark : Marque du PC
	11 - Os : systÃ¨me d'exploitation
	12 - Cat : catÃ©gorie de la demande
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
	00 - Rid : NumÃ©ro unique d'identification de la demande
	01 - Acq : Statut de la demande 0 : non acquitÃ© 1 : AcquitÃ©  (In progress) 2 : Closed
	02 - Host : Hote source de la demande
	03 - Owner : Emetteur de la demande
	04 - OwnerMail : Mel de l'emetteur
	05 - Author : Responsable(s) du traitement du pb
	06 - Sector : secteur
	07 - Building : Batiment
	08 - Room : NÂ° de la salle
	09 - Numcomp : NÂ° du poste
	10 - Mark : Marque du PC
	11 - Os : systÃ¨me d'exploitation
	12 - Cat : catÃ©gorie de la demande
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
		01 - Rid : NumÃ©ro unique d'identification
		02 - TopRid : Rid d'attachement d'origine
		03 - Author : Emetteur
		04 - Content : Description, contenu du message
		05 - TimeStamp : date heure du CR  d'intervention
		06 - TimeLife : DurÃ©e de l'intervention
		07 - Cost : Cout des Ã©ventuels piÃ¨ces de rechange
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
	// PrÃ©paration du mail
	$Subject =" [MaintInfo] Effacement de votre demande du $OpenTimeStamp";
	$Body = "Bonjour,\n
			Votre demande : \n
			$Content\n
			A &eacute;t&eacute; effac&eacute;e par $by\n";
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
	// PrÃ©paration du mail
	$Subject =" [MaintInfo] prise en charge de votre demande du $OpenTimeStamp";
	$Body = "Bonjour,\n
			Votre demande : \n
			$Content\n
			A &eacute;t&eacute; prise en compte par $Author\n";
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
	// PrÃ©paration du mail
	$Subject =" [MaintInfo] Relance demande du $OpenTimeStamp";
	$Body = "Bonjour,\n
			Le ".date("d-m-Y H:i:s")." la demande : \n
			$Content\n
			&#233; &eacute;t&eacute; relanc&eacute;e !";
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
		$table_alert = "<div class='ui-dialog ui-widget ui-widget-content ui-corner-all' id='tabAlert'>\n";
		$table_alert .= "	<div class='ui-widget-header'>-</div>\n";
		$table_alert .= "			<div class='alert_msg'>$message</div>\n";
		$table_alert .= "</div>\n";
		//$table_alert .= "<script type=\"text/javascript\">\n";
		//$table_alert .= "$(function() { $( '#tabAlert' ).dialog(); });\n";
		//$table_alert .= "</script>\n";
		echo $table_alert;
}
function Aff_dialog ($message) {
		$table_alert = "<div class='table_alert' id='tabDialog'>\n";
		$table_alert .= "	<tr>\n";
		$table_alert .= "		<td height='200'>\n";
		$table_alert .= "			<div class='alert_msg'>$message</div>\n";
		$table_alert .= "		</td>\n";
		$table_alert .= "	</tr>\n";
		$table_alert .= "</table>\n";
		$table_alert .= "<script type=\"text/javascript\">\n";
		$table_alert .= "$(function() { $( '#tabDialog' ).dialog(); });\n";
		$table_alert .= "</script>\n";
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
        $html .= ">".utf8_encode( $r["batiment"] )."</option>\n";
      }
      $loop++;  
    }
  @mysql_free_result($result);
  $html .="</select>";
  // Liste des etages du batiment selectionne
  $html .= " Etage :&nbsp;&nbsp;&nbsp;";
  $html .= "<select name=\"etage\" onChange=\"location = this.options[this.selectedIndex].value;\">";
  // lecture de la table topologie pour affichage de la liste des Ã©tages
  $loop=0;
  $result = @mysql_query("SELECT etage from topologie WHERE batiment='$bat' ORDER BY etage ASC");
  if ($result)
  while ($r = @mysql_fetch_array($result)) {
    if ( !isset ($etage) ) $etage = $r["etage"];
    $etage_[$loop] = $r["etage"]; 
    if ( !isset( $etage_[$loop-1] ) || ( $etage_[$loop-1] != $r["etage"] ) ) {
      $html .=  "      <option value=\"config.php?conf=topo&bat=".$bat."&etage=".$r["etage"]."\"";
      if ( $etage == $r["etage"] ) $html .=  "selected";
      $html .= ">". $r["etage"]  ."</option>\n";
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
      $html .=  ">".utf8_encode( $r["salle"] )."</option>\n";
    }    
    $loop++;  
  }
  @mysql_free_result($result);
  $html .= "</select></div>";      
  
  return $html;
}
