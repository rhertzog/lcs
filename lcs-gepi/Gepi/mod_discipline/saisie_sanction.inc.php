<?php
/*
$Id: saisie_sanction.inc.php 6723 2011-03-29 10:53:51Z crob $
*/

// Page incluse dans saisie_sanction.php ou appel�e via ajax depuis saisie_sanction.php->ajout_sanction.php

//Configuration du calendrier

include("../lib/calendrier/calendrier.class.php");

//Variable : $dernier  on afficher le dernier cr�neau si $dernier='o' (param�tre pour une exclusion)
function choix_heure2($champ_heure,$selected,$dernier) {
	$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
	$res_abs_cren=mysql_query($sql);
	$num_row = mysql_num_rows($res_abs_cren); //le nombre de ligne de la requ�te
	if($num_row==0) {
		echo "La table edt_creneaux n'est pas renseign�e!";
	}
	else {
        $cpt=1;	
		//echo "<select name='$champ_heure' id='$champ_heure' onchange='changement();' >\n";
		echo "<select name='$champ_heure' id='$champ_heure' onchange=\"if(document.getElementById('display_heure_main')) {document.getElementById('display_heure_main').value=document.getElementById('$champ_heure').options[document.getElementById('$champ_heure').selectedIndex].value};changement();\" >\n";
		
		while($lig_ac=mysql_fetch_object($res_abs_cren)) {
			echo "<option value='$lig_ac->nom_definie_periode'";
			if(($lig_ac->nom_definie_periode==$selected)||(($dernier=='o')&&($cpt==$num_row))) {echo " selected='selected'";}
			echo ">$lig_ac->nom_definie_periode&nbsp;: $lig_ac->heuredebut_definie_periode � $lig_ac->heurefin_definie_periode</option>\n";
			$cpt++;
		}
		echo "</select>\n";
	}
}

//if((!isset($cpt))||(!isset($valeur))) {
if(!isset($valeur)) {
	echo "<p><strong>Erreur&nbsp;:</strong> Des param�tres n'ont pas �t� transmis.</p>\n";
	die();
}

if($valeur=='travail') {
	echo "<table class='boireaus' border='1'>\n";

	$cal = new Calendrier("formulaire", "date_retour");

	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");
	$date_retour=$jour."/".$mois."/".$annee;

	$travail="";
	$heure_retour=strftime("%H").":".strftime("%M");
	if(isset($id_sanction)) {
		$sql="SELECT * FROM s_travail WHERE id_sanction='$id_sanction';";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$lig_sanction=mysql_fetch_object($res_sanction);
			$date_retour=formate_date($lig_sanction->date_retour);
			$heure_retour=$lig_sanction->heure_retour;
			$travail=$lig_sanction->travail;
		}
	}

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date de retour&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='date_retour' id='date_retour' size='10' value=\"".$date_retour."\" onchange='changement();' />\n";
	echo "<input type='text' name='date_retour' id='date_retour' size='10' value=\"".$date_retour."\" onchange='changement();' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
	echo "<a href=\"#calend\" onclick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
	echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
	echo "</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de retour&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	choix_heure2('heure_retour',$heure_retour,'');
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature du travail&nbsp;: </td>\n";
	echo "<td style='text-align:left;'><textarea name='no_anti_inject_travail' cols='30' onchange='changement();'>$travail</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td colspan='2'>\n";
	echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
}
elseif($valeur=='retenue') {

	$cal = new Calendrier("formulaire", "date_retenue");

	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");
	$date_retenue=$jour."/".$mois."/".$annee;

	//$heure_debut=strftime("%H").":".strftime("%M");
	$heure_debut='00:00';
	$duree_retenue=1;
	$lieu_retenue="";
	$travail="";
	if(isset($id_sanction)) {
		$sql="SELECT * FROM s_retenues WHERE id_sanction='$id_sanction';";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$lig_sanction=mysql_fetch_object($res_sanction);
			$date_retenue=formate_date($lig_sanction->date);
			$heure_debut=$lig_sanction->heure_debut;
			$duree_retenue=$lig_sanction->duree;
			$lieu_retenue=$lig_sanction->lieu;
			$travail=$lig_sanction->travail;
		}
	}

	//echo "<div id='div_liste_retenues_jour' style='float:right; border:1px solid black;background-color: honeydew;'>\n";
	echo "<div id='div_liste_retenues_jour' style='float:right; text-align: center; border:1px solid black; margin-top: 2px; min-width: 19px;'>\n";
	echo "<a href='#' onclick=\"maj_div_liste_retenues_jour();return false;\" title='Retenues du jour'><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Retenues du jour' /></a>";
	echo "</div>\n";

	echo "<table class='boireaus' border='1' summary='Retenue'>\n";
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='date_retenue' id='date_retenue' value='$date_retenue' size='10' onchange='maj_div_liste_retenues_jour();changement();' onblur='maj_div_liste_retenues_jour();' />\n";
	echo "<input type='text' name='date_retenue' id='date_retenue' value='$date_retenue' size='10' onchange='maj_div_liste_retenues_jour();changement();' onblur='maj_div_liste_retenues_jour();' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
	echo "<a href=\"#calend\" onclick=\"$('date_retenue').focus();".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
	echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
	echo "</a>\n";

	// Si le module EDT est actif et si l'EDT est renseign�
	if(param_edt($_SESSION["statut"]) == 'yes') {
		//echo "<a href='#' onclick=\"edt_eleve('$id_sanction');return false;\" title='EDT �l�ve'><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='EDT �l�ve' /></a>";
		echo "<a href='#' onclick=\"edt_eleve();return false;\" title='EDT �l�ve'><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='EDT �l�ve' /></a>";
		//echo "<input type='hidden' name='ele_login' id='ele_login' value='$ele_login' />\n";
	}

	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de d�but&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='heure_debut' value='' />\n";
	echo "<input type='text' name='heure_debut_main' id='display_heure_main' size='5' value=\"$heure_debut\" onKeyDown=\"clavier_heure(this.id,event);\" AutoComplete=\"off\" /> ou \n";
	choix_heure2('heure_debut',$heure_debut,'');
	
	//pour infobulle
	$texte="- 2 choix possibles pour inscrire l'heure de d�but de la retenue<br />Le premier grace � la liste d�roulante. Vous choisissez un cr�neau. Dans ce cas, c'est l'heure d�but de cr�naux HH:MM qui sera pris en compte pour l'impression de la retenue.<br/>Dans l'autre cas, vous saisissez l'heure � la place de '00:00' sous ce format.";
	
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Dur�e&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='duree_retenue' id='duree_retenue' size='2' value='$duree_retenue' onchange='changement();' /> en heures\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='lieu_retenue' id='lieu_retenue' value='$lieu_retenue' onchange='changement();' />\n";
	// S�lectionner parmi des lieux d�j� saisis?
	//$sql="SELECT DISTINCT lieu FROM s_retenues WHERE lieu!='' ORDER BY lieu;";
	$sql="(SELECT DISTINCT lieu FROM s_retenues WHERE lieu!='')";
	if(param_edt($_SESSION["statut"]) == 'yes') {
		$sql.=" UNION (SELECT DISTINCT nom_salle AS lieu FROM salle_cours WHERE nom_salle!='')";
	}
	$sql.=" ORDER BY lieu;";
	//echo "$sql<br />";
	$res_lieu=mysql_query($sql);
	//$tab_lieux=array();
	//$chaine_lieux="";
	if(mysql_num_rows($res_lieu)>0) {
		echo "<select name='choix_lieu' id='choix_lieu' onchange=\"maj_lieu('lieu_retenue','choix_lieu');changement();\">\n";
		echo "<option value=''>---</option>\n";
		while($lig_lieu=mysql_fetch_object($res_lieu)) {
			echo "<option value=\"$lig_lieu->lieu\">$lig_lieu->lieu</option>\n";
			//$tab_lieux[]=urlencode($lig_lieu->lieu);
			//$chaine_lieux.=", '".urlencode($lig_lieu->lieu)."'";
		}
		echo "</select>\n";

		echo "<a href='#' onclick=\"occupation_lieu_heure('$id_sanction');return false;\"><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='Occupation du lieu pour la date/heure choisie' /></a>";
	}

	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature du travail&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<textarea name='no_anti_inject_travail' cols='30' onchange='changement();'>$travail</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Report&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	
	echo "<b>Gestion d'un report :</b><br/>";
	
	echo "1- Cocher cette case pour traiter un report : <input type='checkbox' name='report_demande' id='report_demande' value='OK' onchange=\"changement();\" /><br/>\n";
	echo "2- Saisir le motif du report : <select name='choix_motif_report' id='choix_motif_report' changement();\">\n";
	echo "<option value=''>---</option>\n";
	echo "<option value='absent'>Absent</option>\n";
	echo "<option value='aucun_motif'>Aucun motif</option>\n";
	echo "<option value='report_demande'>Report demand�</option>\n";
	echo "<option value='autre'>Autre</option>\n";
	echo "</select><br/>\n";
	echo "3- Modifier les donn�es (date, heure, ...) pour le report<br/>";
	echo "4- Enregistrer les modifications<br/>";
	echo "5- Imprimer le document sur la page suivante<br/>\n";
	
	if (isset($id_sanction)) {
	echo "<b>Liste des reports</b><br/>\n";
	echo afficher_tableau_des_reports($id_sanction);
	}
	echo "</td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig1'>\n";
	echo "<td colspan='2'>\n";
	echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";

	echo "<script type='text/javascript'>
	// Le lancement ci-dessous n'est pas pris en compte pour l'ajout d'une retenue, seulement pour la modification d'une retenue.
	// J'ai donc mis un lien dans le DIV div_liste_retenues_jour
	maj_div_liste_retenues_jour();
</script>\n";
}
elseif($valeur=='exclusion') {
	echo "<table class='boireaus' border='1' summary='Exclusion'>\n";

	$cal1 = new Calendrier("formulaire", "date_debut");

	$annee = strftime("%Y");
	$mois = strftime("%m");
	$jour = strftime("%d");
	$date_debut=$jour."/".$mois."/".$annee;
	$date_fin=$date_debut;

	$heure_debut=strftime("%H").":".strftime("%M");
	$heure_fin=$heure_debut;
	$afficher_creneau_final = 'o';

	$lieu_exclusion="";
	$travail="";
	
	$nombre_jours="";
	$qualification_faits="";
	$numero_courrier="";
	$type_exclusion="";
	$fct_autorite="";
	$nom_autorite="";
	$fct_delegation="";
	
	if(isset($id_sanction)) {
		$sql="SELECT * FROM s_exclusions WHERE id_sanction='$id_sanction';";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
			$lig_sanction=mysql_fetch_object($res_sanction);
			$date_debut=formate_date($lig_sanction->date_debut);
			$date_fin=formate_date($lig_sanction->date_fin);
			$heure_debut=$lig_sanction->heure_debut;
			$heure_fin=$lig_sanction->heure_fin;
			$lieu_exclusion=$lig_sanction->lieu;
			$travail=$lig_sanction->travail;
			$afficher_creneau_final='';
			$nombre_jours=$lig_sanction->nombre_jours;
			$qualification_faits=$lig_sanction->qualification_faits;
			$numero_courrier=$lig_sanction->num_courrier;
			$type_exclusion=$lig_sanction->type_exclusion;
			$signataire=$lig_sanction->id_signataire;
		} 
	}
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date de d�but&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='date_debut' id='date_debut' value='$date_debut' size='10' onchange='changement();' />\n";
	echo "<input type='text' name='date_debut' id='date_debut' value='$date_debut' size='10' onchange='changement();' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
	echo "<a href=\"#calend\" onclick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
	echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
	echo "</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de d�but&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='heure_debut' value='' />\n";
	choix_heure2('heure_debut',$heure_debut,'');
	echo "</td>\n";
	echo "</tr>\n";

	$cal2 = new Calendrier("formulaire", "date_fin");

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Date de fin&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='date_fin' id='date_fin' value='$date_fin' size='10' onchange='changement();' />\n";
	echo "<input type='text' name='date_fin' id='date_fin' value='$date_fin' size='10' onchange='changement();' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
	echo "<a href=\"#calend\" onclick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\">\n";
	echo "<img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" />\n";
	echo "</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Heure de fin&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	//echo "<input type='text' name='heure_debut' value='' />\n";
	choix_heure2('heure_fin',$heure_fin,$afficher_creneau_final);
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Lieu&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='lieu_exclusion' id='lieu_exclusion' value=\"$lieu_exclusion\" onchange='changement();' />\n";
	// S�lectionner parmi des lieux d�j� saisis?
	$sql="SELECT DISTINCT lieu FROM s_exclusions WHERE lieu!='' ORDER BY lieu;";
	$res_lieu=mysql_query($sql);
	if(mysql_num_rows($res_lieu)>0) {
		echo "<select name='choix_lieu' id='choix_lieu' onchange=\"maj_lieu('lieu_exclusion','choix_lieu');changement();\">\n";
		echo "<option value=''>---</option>\n";
		while($lig_lieu=mysql_fetch_object($res_lieu)) {
			echo "<option value=\"$lig_lieu->lieu\">$lig_lieu->lieu</option>\n";
		}
		echo "</select>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nature du travail&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<textarea name='no_anti_inject_travail' cols='30' onchange='changement();'>$travail</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";

// Ajout Eric g�n�ration Ooo de l'exclusion
	echo "<tr>\n";
	echo "<td colspan=2 style='text-align:center;'>\n";
	echo "Donn�es � renseigner pour l'impression Open Office de l'exclusion temporaire :</td>\n";
	echo "</tr>\n";

	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Numero de courrier&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='numero_courrier' id='numero_courrier' value=\"$numero_courrier\" onchange='changement();' />\n";
	echo "<i>La r�f�rence du courrier dans le registre courrier d�part. Ex : ADM/SD/012/11</i></td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Type d'exclusion&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='type_exclusion' id='type_exclusion' value=\"$type_exclusion\" onchange='changement();' />\n";
	echo "<select name='type_exclusion' id='type_exclusion_select' onchange=\"maj_lieu('type_exclusion','type_exclusion_select','type_exclusion');changement();\">\n";
	if ($type_exclusion=='exclusion temporaire') {
	    echo "<option value=\"exclusion temporaire\" selected>Exclusion temporaire</option>\n";
	} else {
	    echo "<option value=\"exclusion temporaire\">Exclusion temporaire</option>\n";
	}
	if ($type_exclusion=='exclusion-inclusion temporaire') {
	    echo "<option value=\"exclusion-inclusion temporaire\" selected>Exclusion-inclusion temporaire</option>\n";
	} else {
	    echo "<option value=\"exclusion-inclusion temporaire\">Exclusion-inclusion temporaire</option>\n";
	}
	
	
	echo "</select>\n";
	echo "<i>Choisir le type dans la liste.</i></td>\n";
	echo "</tr>\n";	
	
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Nombre de jours d'exclusion&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<input type='text' name='nombre_jours' id='nombre_jours' value=\"$nombre_jours\" onchange='changement();' />\n";
	echo "<i>en toutes lettres</i></td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig-1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Qualification des faits&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	echo "<textarea name='no_anti_inject_qualification_faits' cols='100' onchange='changement();'>$qualification_faits</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig1'>\n";
	echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Choix du signataire de l'exclusion&nbsp;: </td>\n";
	echo "<td style='text-align:left;'>\n";
	// S�lectionner parmi les signataires d�j� saisis?
	$sql="SELECT * FROM s_delegation ORDER BY fct_autorite";
	$res_signataire=mysql_query($sql);
	if(mysql_num_rows($res_signataire)>0) {
		echo "<select name='signataire' id='choix_signataire' onchange=\"changement();\">\n";
		echo "<option value=''>---</option>\n";
		while($lig_signataire=mysql_fetch_object($res_signataire)) {
		    if ($signataire==$lig_signataire->id_delegation) {
			echo "<option value=\"$lig_signataire->id_delegation\" selected >$lig_signataire->fct_autorite</option>\n";
			} else {
			echo "<option value=\"$lig_signataire->id_delegation\">$lig_signataire->fct_autorite</option>\n";
			}
		}
		echo "</select>\n";
	} else {
	    echo "<i>Aucun signataire n'est saisi dans la base. Demandez � votre administrateur de saisir cette liste en admin du module</i>";
	};
	echo "</td>\n";
	echo "</tr>\n";
	
	echo "<tr class='lig-1'>\n";
	echo "<td colspan='2'>\n";
	echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
}
else {
	$sql="SELECT * FROM s_types_sanctions WHERE id_nature='$valeur';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);

		echo "<table class='boireaus' border='1' summary=\"$lig->nature\">\n";

		$description="";

		if(isset($id_sanction)) {
			$sql="SELECT * FROM s_autres_sanctions WHERE id_sanction='$id_sanction';";
			$res_sanction=mysql_query($sql);
			if(mysql_num_rows($res_sanction)>0) {
				$lig_sanction=mysql_fetch_object($res_sanction);
				$description=$lig_sanction->description;
			}
		}

		echo "<tr>\n";
		echo "<th colspan='2'>$lig->nature</th>\n";
		echo "</tr>\n";

		echo "<tr class='lig-1'>\n";
		echo "<td style='font-weight:bold;vertical-align:top;text-align:left;'>Description&nbsp;: </td>\n";
		echo "<td style='text-align:left;'>\n";
		echo "<textarea name='no_anti_inject_description' cols='30' onchange='changement();'>$description</textarea>\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr class='lig1'>\n";
		echo "<td colspan='2'>\n";
		echo "<input type='submit' name='enregistrer_sanction' value='Enregistrer' />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "</table>\n";
	}
	else {
		echo "<p style='color:red;'>Type de sanction inconnu.</p>\n";
	}
}
?>