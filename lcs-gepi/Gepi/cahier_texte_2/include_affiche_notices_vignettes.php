<?php
/*
 * $Id: include_affiche_notices_vignettes.php 8401 2011-10-01 13:23:40Z crob $
 *
 * Copyright 2009-2011 Josselin Jacquard
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

function affiche_devoir_vignette($devoir, $couleur_bord_tableau_notice, $color_fond_notices) {
	echo("<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice.";\" width=\"100%\" cellpadding=\"1\" bgcolor=\"".$color_fond_notices["t"]."\" summary=\"Tableau de...\">\n<tr>\n<td>\n");

	echo("<strong>&nbsp;A faire pour le :</strong>\n");
	echo("<b>" . strftime("%a %d %b %y", $devoir->getDateCt()) . "</b>\n");
	echo("&nbsp;&nbsp;&nbsp;&nbsp;");

	//vise
	$html_balise =("<div style='display: none; color: red; margin: 0px; float: right;' id='compte_rendu_en_cours_devoir_".$devoir->getIdCt()."'></div>");
	$html_balise .= '<div style="margin: 0px; float: left;">';
	if (($devoir->getVise() != 'y') or (isset($visa_cdt_inter_modif_notices_visees) AND $visa_cdt_inter_modif_notices_visees == 'no')) {

		//$html_balise .=("<span style='color:plum'>".$devoir->getIdLogin()."</span><br />");
		//$html_balise .=("<span style='color:coral'>".$_SESSION['login']."</span>");

		$liens_edition_suppression="y";
		if((strtoupper($devoir->getIdLogin())!=strtoupper($_SESSION['login']))&&(getSettingValue("cdt_autoriser_modif_multiprof")!="yes")) {
			$liens_edition_suppression="n";
		}

		if($liens_edition_suppression=="y") {
			$html_balise .=("<a href=\"#\" onclick=\"javascript:
									id_groupe = '".$devoir->getIdGroupe()."';
									getWinEditionNotice().setAjaxContent('ajax_edition_devoir.php?id_devoir=".$devoir->getIdCt()."',{ onComplete: function(transport) {	initWysiwyg();}});
									getWinListeNotices();
									new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$devoir->getIdGroupe()."',{ onComplete:function() {updateDivModification();}});
									updateCalendarWithUnixDate(".$devoir->getDateCt().");
									object_en_cours_edition = 'devoir';
									");
			$html_balise .=("\">");
			$html_balise .=("<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");
			$html_balise .=(" ");
			$html_balise .=("<a href=\"#\" onclick=\"javascript:
									suppressionDevoir('".strftime("%A %d %B %Y", $devoir->getDateCt())."','".$devoir->getIdCt()."', '".$devoir->getIdGroupe()."','".add_token_in_js_func()."');
									new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
									return false;
								\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n");
	
			if(($devoir->getDateVisibiliteEleve()!="")&&(mysql_date_to_unix_timestamp($devoir->getDateVisibiliteEleve())>time())) {
				$html_balise .=("<img src=\"../images/icons/visible.png\" width=\"19\" height=\"16\" alt=\"Date de visibilit� de la notice pour les �l�ves\" title=\"Date de visibilit� de la notice pour les �l�ves\" /><span style='font-size: xx-small; color:red;'>&nbsp;".get_date_heure_from_mysql_date($devoir->getDateVisibiliteEleve())."</span>\n");
			}
		}
	} else {
		$html_balise .= "<i><span  class=\"red\">Notice sign�e</span></i>";
	}
	$html_balise .= '</div>';
	echo($html_balise);
	echo "<br/>";
	//affichage contenu
	echo ($devoir->getContenu());

	//Documents joints
	$ctDevoirDocuments = $devoir->getCahierTexteTravailAFaireFichierJoints();
	echo(afficheDocuments($ctDevoirDocuments));

	echo("</td>\n</tr>\n</table>\n<br/>\n");
}

function affiche_notice_privee_vignette($notice_privee, $couleur_bord_tableau_notice, $color_fond_notices) {
	echo("<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice.";\" width=\"100%\" cellpadding=\"1\" bgcolor=\"".$color_fond_notices["p"]."\" summary=\"Tableau de...\">\n<tr>\n<td>\n");

	echo("<strong>&nbsp;Notice priv&eacute;e</strong>\n");
	echo("<b>" . strftime("%a %d %b %y", $notice_privee->getDateCt()) . "</b>\n");
	echo("&nbsp;&nbsp;&nbsp;&nbsp;");

	//vise
	$html_balise =("<div style='display: none; color: red; margin: 0px; float: right;' id='compte_rendu_en_cours_notice_privee_".$notice_privee->getIdCt()."'></div>");

		if(strtoupper($notice_privee->getIdLogin())==strtoupper($_SESSION['login'])) {
			$html_balise .= '<div style="margin: 0px; float: left;">';
				$html_balise .=("<a href=\"#\" onclick=\"javascript:
										id_groupe = '".$notice_privee->getIdGroupe()."';
										getWinEditionNotice().setAjaxContent('ajax_edition_notice_privee.php?id_ct=".$notice_privee->getIdCt()."',{ onComplete: function() {	initWysiwyg();}});
										updateCalendarWithUnixDate(".$notice_privee->getDateCt().");
										getWinListeNotices();
										new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$notice_privee->getIdGroupe()."',{ onComplete:function() {updateDivModification();}});
										object_en_cours_edition = 'notice_privee';
										");
				$html_balise .=("\">");
				$html_balise .=("<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");
				$html_balise .=(" ");
				$html_balise .=("<a href=\"#\" onclick=\"javascript:
										suppressionNoticePrivee('".strftime("%A %d %B %Y", $notice_privee->getDateCt())."','".$notice_privee->getIdCt()."', '".$notice_privee->getIdGroupe()."','".add_token_in_js_func()."');
										new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
										return false;
									\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n");
			$html_balise .= '</div>';
			echo($html_balise);
		}

	echo "<br/>";
	//affichage contenu
	echo ($notice_privee->getContenu());

	echo("</td>\n</tr>\n</table>\n<br/>\n");
}

function affiche_compte_rendu_vignette($compte_rendu, $couleur_bord_tableau_notice, $color_fond_notices) {
		echo("<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."\" width=\"100%\" cellpadding=\"1\" bgcolor=\"".$color_fond_notices["c"]."\" summary=\"Tableau de...\">\n<tr>\n<td>\n");
		echo("<b>&nbsp;" . strftime("%a %d %b %y", $compte_rendu->getDateCt()) . "</b>\n");

		$html_balise =("<div style='display: none; color: red; margin: 0px; float: right;' id='compte_rendu_en_cours_compte_rendu_".$compte_rendu->getIdCt()."'></div>");
		$html_balise .= '<div style="margin: 0px; float: left;">';
		if (($compte_rendu->getVise() != 'y') or (isset($visa_cdt_inter_modif_notices_visees) AND $visa_cdt_inter_modif_notices_visees == 'no')) {

			$liens_edition_suppression="y";
			if((strtoupper($compte_rendu->getIdLogin())!=strtoupper($_SESSION['login']))&&(getSettingValue("cdt_autoriser_modif_multiprof")!="yes")) {
				$liens_edition_suppression="n";
			}
	
			if($liens_edition_suppression=="y") {
				$html_balise .=("<a href=\"#\" onclick=\"javascript:
									id_groupe = '".$compte_rendu->getIdGroupe()."';
									getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_ct=".$compte_rendu->getIdCt()."',
										{ onComplete: function(transport) {initWysiwyg();}});
									updateCalendarWithUnixDate(".$compte_rendu->getDateCt().");
									getWinListeNotices();
									new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$compte_rendu->getIdGroupe()."',{ onComplete:function() {updateDivModification();}});
									object_en_cours_edition = 'compte_rendu';
								");
				$html_balise .=("\">");
				$html_balise .=("<img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");
	
				$html_balise .=(" ");
				$html_balise .=("<a href=\"#\" onclick=\"javascript:
								suppressionCompteRendu('".strftime("%A %d %B %Y", $compte_rendu->getDateCt())."',".$compte_rendu->getIdCt().",'".add_token_in_js_func()."');
								new Ajax.Updater('affichage_derniere_notice', 'ajax_affichage_dernieres_notices.php', {onComplete : function () {updateDivModification();}});
								return false;
							\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n");
			}
		}
		// cas d'un visa, on n'affiche rien
		if ($compte_rendu->getVisa() == 'y') {
			$html_balise = " ";
		} else {
			if ($compte_rendu->getVise() == 'y') {
				$html_balise .= "<i><span  class=\"red\">Notice sign�e</span></i>";
			}
		}
		$html_balise .= '</div>';
		echo($html_balise);

		//affichage contenu
		echo "<br/>";
    // On ajoute le nom de la s�quence si elle existe
    $aff_seq = NULL;
    if ($compte_rendu->getIdSequence() != "0"){
      $aff_seq = '<p class="bold" title="'.$compte_rendu->getCahierTexteSequence()->getDescription().'"> - <em>' . $compte_rendu->getCahierTexteSequence()->getTitre() . '</em> - </p>';
    }
		echo ($aff_seq . $compte_rendu->getContenu());

		// Documents joints
		$ctDocuments = $compte_rendu->getCahierTexteCompteRenduFichierJoints();
		echo(afficheDocuments($ctDocuments));

		echo("</td>\n</tr>\n</table>\n<br/>\n");
}

function afficheDocuments ($documents) {
	$html = '';
	if (($documents) and (count($documents)!=0)) {
		$html = "<br><span class='petit'>Document(s) joint(s):</span>";
		//$html .= "<ul type=\"disc\" style=\"padding-left: 15px;\">";
		$html .= "<ul style=\"padding-left: 15px;\">";
		foreach ($documents as $document) {
			// Ouverture dans une autre fen�tre conserv�e parce que si le fichier est un PDF, un TXT, un HTML ou tout autre document susceptible de s'ouvrir dans le navigateur, on risque de refermer sa session en croyant juste refermer le document.
			// alternative, utiliser un javascript
			$html .= "<li style=\"padding: 0px; margin: 0px; font-family: arial, sans-serif; font-size: 80%;\"><a onclick=\"window.open(this.href, '_blank'); return false;\" href=\"".$document->getEmplacement()."\">".$document->getTitre()."</a>";

			if(!$document->getVisibleEleveParent()) {
				$html.="<img src='../images/icons/invisible.png' width='19' height='16' alt='Document invisible des �l�ves et responsables' title='Document invisible des �l�ves et responsables' />";
			}

			/*
			$sql="SELECT 1=1 FROM ct_documents cd, ct_entry ce, j_groupes_professeurs jgp WHERE ce.id_ct=cd.id_ct AND cd.id='".$document->getId()."' AND jgp.id_groupe=ce.id_groupe AND jgp.login='".$_SESSION['login']."';";
			//echo "$sql<br/>";
			$test1=mysql_query($sql);

			$sql="SELECT 1=1 FROM ct_devoirs_documents cd, ct_devoirs_entry ce, j_groupes_professeurs jgp WHERE ce.id_ct=cd.id_ct_devoir AND cd.id='".$document->getId()."' AND jgp.id_groupe=ce.id_groupe AND jgp.login='".$_SESSION['login']."';";
			$test2=mysql_query($sql);

			//if($_SESSION['statut']=='professeur') {
			if(($_SESSION['statut']=='professeur')&&((mysql_num_rows($test1)>0)||(mysql_num_rows($test2)>0))) {
				if(!$document->getVisibleEleveParent()) {
					$html.="<img src='../images/icons/invisible.png' width='19' height='16' alt='Document invisible des �l�ves et responsables' title='Document invisible des �l�ves et responsables' />";
				}
			}
			*/
			$html.="</li>";

		}
		$html .= "</ul>";
	}
	return $html;
}


?>
