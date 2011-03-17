<?php
/*
* $Id: edit_class.php 6638 2011-03-08 11:47:17Z crob $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
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

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
if (!is_numeric($id_classe)) {$id_classe = 0;}
$classe = get_classe($id_classe);

if(isset($_GET['forcer_recalcul_rang'])) {
	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode DESC LIMIT 1;";
	$res_per=mysql_query($sql);
	if(mysql_num_rows($res_per)>0) {
		$lig_per=mysql_fetch_object($res_per);
		$recalcul_rang="";
		for($i=0;$i<$lig_per->num_periode;$i++) {$recalcul_rang.="y";}
		$sql="UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		if(!$res) {
			$msg="Erreur lors de la programmation du recalcul des rangs pour cette classe.";
		}
		else {
			$msg="Recalcul des rangs programm� pour cette classe.";
		}
	}
	else {
		$msg="Aucune p�riode n'est d�finie pour cette classe.<br />Recalcul des rangs impossible pour cette classe.";
	}
}

// =================================
// AJOUT: boireaus
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";
$res_class_tmp=mysql_query($sql);
if(mysql_num_rows($res_class_tmp)>0){
    $id_class_prec=0;
    $id_class_suiv=0;
    $temoin_tmp=0;
    $cpt_classe=0;
	$num_classe=-1;
    while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
        if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
            $temoin_tmp=1;
            if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
                $id_class_suiv=$lig_class_tmp->id;
            }
            else{
                $id_class_suiv=0;
            }
        }
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}

        if($temoin_tmp==0){
            $id_class_prec=$lig_class_tmp->id;
        }
		$cpt_classe++;
    }
}// =================================

$priority_defaut = 5;

if (isset($_POST['is_posted'])) {
	check_token();

    $error = false;

    foreach ($_POST as $key => $value) {
        $pattern = "/^priorite\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["priorite"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^coef\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["coef"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        //$pattern = "/^note\_sup\_10\_/";
        $pattern = "/^mode\_moy\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            //$options[$group_id]["mode_moy"] = "sup10";
            $options[$group_id]["mode_moy"] = $value;
			//echo "mode_moy pour $group_id : $value<br />";
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^no_saisie_ects\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["saisie_ects"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^saisie_ects\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["saisie_ects"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^valeur_ects\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["valeur_ects"] = $value;
        }
    }

    foreach ($_POST as $key => $value) {
        $pattern = "/^categorie\_/";
        if (preg_match($pattern, $key)) {
            $group_id = preg_replace($pattern, "", $key);
            $options[$group_id]["categorie_id"] = $value;
        }
    }

    foreach ($options as $key => $value) {
        // Toutes les v�rifications de s�curit� sont faites dans la fonction
        $update = update_group_class_options($key, $id_classe, $value);
    }

	$msg="Enregistrement effectu�.";

}

if (isset($_GET['action'])) {
	check_token();

    $msg = null;
    //if ($_GET['action'] == "delete_group") {
    if(($_GET['action'] == "delete_group")&&(isset($_GET['confirm_delete_group']))&&($_GET['confirm_delete_group'] == "y")) {
        if (!is_numeric($_GET['id_groupe'])) $_GET['id_groupe'] = 0;
        $verify = test_before_group_deletion($_GET['id_groupe']);
        if ($verify) {
            //================================
            // MODIF: boireaus
            $sql="SELECT * FROM groupes WHERE id='".$_GET['id_groupe']."'";
            $req_grp=mysql_query($sql);
            $ligne_grp=mysql_fetch_object($req_grp);
            //================================
            $delete = delete_group($_GET['id_groupe']);
            if ($delete == true) {
                //================================
                // MODIF: boireaus
                //$msg .= "Le groupe " . $_GET['id_groupe'] . " a �t� supprim�.";

                //$sql="SELECT * FROM groupes WHERE id='".$_GET['id_groupe']."'";
                //$req_grp=mysql_query($sql);
                //$ligne_grp=mysql_fetch_object($req_grp);
                // Le groupe n'existe d�j� plus
                $msg .= "Le groupe $ligne_grp->name (" . $_GET['id_groupe'] . ") a �t� supprim�.";
                //================================
            } else {
                $msg .= "Une erreur a emp�ch� la suppression du groupe.";
            }
        } else {
            $msg .= "Des donn�es existantes bloquent la suppression du groupe. Aucune note ni appr�ciation du bulletin ne doit avoir �t� saisie pour les �l�ves de ce groupe pour permettre la suppression du groupe.";
        }
    }
}

$themessage  = 'Des informations ont �t� modifi�es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
//$titre_page = "Gestion des groupes";
$titre_page = "Gestion des enseignements";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************


if((isset($_GET['action']))&&($_GET['action']=="delete_group")&&(!isset($_GET['confirm_delete_group']))) {
	check_token(false);

	// On va d�tailler ce qui serait supprim� en cas de confirmation
	$tmp_group=get_group($_GET['id_groupe']);
	echo "<div style='border: 2px solid red;'>\n";
	echo "<p><b>ATTENTION&nbsp;:</b> Vous souhaitez supprimer l'enseignement suivant&nbsp;: ".$tmp_group['name']." (<i>".$tmp_group['description']."</i>) en ".$tmp_group['classlist_string']."<br />\n";
	echo "Voici quelques �l�ments sur l'enseignement&nbsp;:</p>\n";
	$suppression_possible='y';

	$lien_bull_simp="";
	$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode DESC LIMIT 1;";
	//echo "$sql<br />";
	$res_per=mysql_query($sql);
	if(mysql_num_rows($res_per)>0) {
		$lig_per=mysql_fetch_object($res_per);

		$lien_bull_simp="<a href='../prepa_conseil/edit_limite.php?choix_edit=1&amp;id_classe=$id_classe&amp;periode1=1&amp;periode2=$lig_per->num_periode' target='_blank'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='Bulletin simple dans une nouvelle page' title='Bulletin simple dans une nouvelle page' /></a>";
	}

	echo "<p style='margin-left:5em;'>";
	$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_mn=mysql_query($sql);
	$nb_mn=mysql_num_rows($test_mn);
	if($nb_mn==0) {
		echo "Aucune note sur les bulletins.<br />\n";
	}
	else {
		echo "<span style='color:red;'>$nb_mn note(s) sur les bulletins</span> (<i>toutes p�riodes confondues</i>)&nbsp;: $lien_bull_simp<br />\n";
		$suppression_possible='n';
	}

	$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_ma=mysql_query($sql);
	$nb_ma=mysql_num_rows($test_ma);
	if($nb_ma==0) {
		echo "Aucune appr�ciation sur les bulletins.<br />\n";
	}
	else {
		echo "<span style='color:red;'>$nb_ma appr�ciation(s) sur les bulletins</span> (<i>toutes p�riodes confondues</i>)&nbsp;: $lien_bull_simp<br />\n";
		$suppression_possible='n';
	}

	$temoin_non_vide='n';
	// CDT
	$sql="SELECT 1=1 FROM ct_entry WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_notice_cdt=mysql_query($sql);
	$nb_notice_cdt=mysql_num_rows($test_notice_cdt);
	if($nb_notice_cdt==0) {
		echo "Aucune notice dans le cahier de textes.<br />\n";
	}
	else {
		echo "$nb_notice_cdt notice(s) dans le cahier de textes.<br />\n";
		$temoin_non_vide='y';
	}

	$sql="SELECT 1=1 FROM ct_devoirs_entry WHERE id_groupe='".$_GET['id_groupe']."';";
	$test_devoir_cdt=mysql_query($sql);
	$nb_devoir_cdt=mysql_num_rows($test_devoir_cdt);
	if($nb_devoir_cdt==0) {
		echo "Aucun devoir dans le cahier de textes.<br />\n";
	}
	else {
		echo "$nb_devoir_cdt devoir(s) dans le cahier de textes.<br />\n";
		$temoin_non_vide='y';
	}

	// NOTES
	// R�cup�rer les cahier de notes
	$sql="SELECT DISTINCT id_cahier_notes, periode FROM cn_cahier_notes WHERE id_groupe='".$_GET['id_groupe']."' ORDER BY periode;";
	$res_ccn=mysql_query($sql);
	if(mysql_num_rows($res_ccn)==0) {
		echo "Aucun cahier de notes n'est initialis� pour cet enseignement.<br />\n";
	}
	else {
		while($lig_id_cn=mysql_fetch_object($res_ccn)) {
			$sql="SELECT 1=1 FROM cn_devoirs WHERE id_racine='$lig_id_cn->id_cahier_notes';";
			$res_dev=mysql_query($sql);
			$nb_dev=mysql_num_rows($res_dev);
			if($nb_dev==0) {
				echo "P�riode $lig_id_cn->periode&nbsp;: Aucun devoir.<br />\n";
			}
			else {
				echo "P�riode $lig_id_cn->periode&nbsp;: $nb_dev devoir(s) dans le carnet de notes.<br />\n";
				$temoin_non_vide='y';
			}
		}
	}
	echo "</p>\n";

	if($suppression_possible=='y') {
		if($temoin_non_vide=='y') {
			echo "<p>Si vous souhaitez effectuer ";
			echo "malgr� tout ";
			echo "la suppression de l'enseignement&nbsp;: ";
			echo "<a href='edit_class.php?id_groupe=".$_GET['id_groupe']."&amp;action=delete_group&amp;confirm_delete_group=y&amp;id_classe=$id_classe".add_token_in_url()."' onclick=\"return confirmlink(this, 'ATTENTION !!! L\'enseignement n\'est pas totalement vide, m�me si les bulletins ne contiennent pas de r�f�rence � cet enseignement.\\nEtes-vous *VRAIMENT S�R* de vouloir continuer ?', 'Confirmation de la suppression')\">Supprimer</a>";
			echo "</p>\n";
		}
		else {
			echo "<p>Si vous souhaitez confirmer la suppression de l'enseignement&nbsp;: ";
			echo "<a href='edit_class.php?id_groupe=".$_GET['id_groupe']."&amp;action=delete_group&amp;confirm_delete_group=y&amp;id_classe=$id_classe".add_token_in_url()."'>Supprimer</a>";
			echo "</p>\n";
		}
	}
	else {
		echo "<p style='color:red;'>Des donn�es existantes bloquent la suppression du groupe.<br />Aucune note ni appr�ciation du bulletin ne doit avoir �t� saisie pour les �l�ves de ce groupe pour permettre la suppression du groupe.</p>";
	}
	echo "</div>\n";
}


$display_mat_cat="n";
$sql="SELECT display_mat_cat FROM classes WHERE id='$id_classe';";
$res_display_mat_cat=mysql_query($sql);
if(mysql_num_rows($res_display_mat_cat)>0) {
	$lig_display_mat_cat=mysql_fetch_object($res_display_mat_cat);
	$display_mat_cat=$lig_display_mat_cat->display_mat_cat;

	$url_wiki="#";
	$sql="SELECT * FROM ref_wiki WHERE ref='enseignement_invisible';";
	$res_ref_wiki=mysql_query($sql);
	if(mysql_num_rows($res_ref_wiki)>0) {
		$lig_wiki=mysql_fetch_object($res_ref_wiki);
		$url_wiki=$lig_wiki->url;
	}
	$titre="Enseignement invisible";
	$texte="<p>Cet enseignement n'appara�tra pas sur les bulletins ni sur les relev�s de notes.<br />";
	$texte.="Voir <a href='$url_wiki' target='_blank'>Enseignement invisible sur les bulletins et relev�s de notes</a>.<br />";
	$tabdiv_infobulle[]=creer_div_infobulle('enseignement_invisible',$titre,"",$texte,"",25,0,'y','y','n','n');
}
else {
	echo "<p style='color:red;'>Anomalie&nbsp;: Les infos concernant 'display_mat_cat' n'ont pas pu �tre r�cup�r�es pour cette classe.</p>\n";
}

echo "<table border='0' summary='Menu'><tr>\n";
echo "<td width='40%' align='left'>";
echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
echo "<p class='bold'>";
echo "<a href='../classes/index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
//if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe pr�c�dente</a>";}
if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe pr�c.</a>";}
if($chaine_options_classes!="") {

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";


	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}
//if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suiv.</a>";}

//=========================
// AJOUT: boireaus 20081224
$titre="Navigation";
$texte="";
$texte.="<img src='../images/icons/date.png' alt='' /> <a href='../classes/periodes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">P�riodes</a><br />";
$texte.="<img src='../images/icons/edit_user.png' alt='' /> <a href='../classes/classes_const.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">El�ves</a><br />";
//$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignements</a><br />";
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">config.simplifi�e</a><br />";
$texte.="<img src='../images/icons/configure.png' alt='' /> <a href='../classes/modify_nom_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Param�tres</a>";

$ouvrir_infobulle_nav=getSettingValue("ouvrir_infobulle_nav");
//echo "\$ouvrir_infobulle_nav=$ouvrir_infobulle_nav<br />";

if($ouvrir_infobulle_nav=="y") {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/vert.png' width='16' height='16' alt='Oui' /></a></div>\n";
}
else {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/rouge.png' width='16' height='16' alt='Non' /></a></div>\n";
}

$texte.="<script type='text/javascript'>
	// <![CDATA[
	function modif_mode_infobulle_nav() {
		new Ajax.Updater($('save_mode_nav'),'../classes/classes_ajax_lib.php?mode=ouvrir_infobulle_nav',{method: 'get'});
	}
	//]]>
</script>\n";

$tabdiv_infobulle[]=creer_div_infobulle('navigation_classe',$titre,"",$texte,"",14,0,'y','y','n','n');

echo " | <a href='#' onclick=\"afficher_div('navigation_classe','y',-100,20);\"";
echo ">";
echo "Navigation";
echo "</a>";
//=========================

echo " | <a href='menage_eleves_groupes.php?id_classe=$id_classe'>D�sinscriptions par lots</a>";

echo "</p>\n";
echo "</form>\n";


echo "<h3>Gestion des enseignements pour la classe :" . $classe["classe"]."</h3>\n";

echo "</td>";
echo "<td width='60%' align='center'>";
echo "<form enctype='multipart/form-data' action='add_group.php' name='new_group' method='get'>";
//==============================
// MODIF: boireaus
//echo "<p>Ajouter un enseignement : ";
//$query = mysql_query("SELECT matiere, nom_complet FROM matieres");
echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">";
echo "<table border='0' summary='Ajout d enseignement'><tr valign='top'><td>";
echo "Ajouter un enseignement : ";
echo "</td>";
$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
//==============================
$nb_mat = mysql_num_rows($query);

echo "<td>";
echo "<select name='matiere' size='1'>";
echo "<option value='null'>-- S�lectionner mati�re --</option>";
for ($i=0;$i<$nb_mat;$i++) {
    $matiere = mysql_result($query, $i, "matiere");
    $nom_matiere = mysql_result($query, $i, "nom_complet");
    //echo "<option value='" . $matiere . "'";
    echo "<option value='" . $matiere . "'";
    echo ">" . htmlentities($nom_matiere) . "</option>\n";
}
echo "</select>";
echo "</td>";
echo "<td>";
echo "&nbsp;dans&nbsp;";
echo "</td>";
//==============================
// MODIF: boireaus
/*
echo "<select name='mode' size='1'>";
echo "<option value='null'>-- S�lectionner mode --</option>";
echo "<option value='groupe' selected>cette classe seulement (" . $classe["classe"] .")</option>";
echo "<option value='regroupement'>plusieurs classes</option>";
echo "</select>";
*/
echo "<td>";
echo "<input type='radio' name='mode' id='mode_groupe' value='groupe' checked /><label for='mode_groupe' style='cursor: pointer;'> cette classe seulement (" . $classe["classe"] .")</label><br />\n";
echo "<input type='radio' name='mode' id='mode_regroupement' value='regroupement' /><label for='mode_regroupement' style='cursor: pointer;'> plusieurs classes</label>\n";
echo "</td>";
echo "</tr></table>\n";
//==============================

echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />";
echo "<input type='submit' value='Cr�er' />";
echo "</fieldset>";
echo "</form>";
echo "</td></tr></table>\n";

$groups = get_groups_for_class($id_classe);
if(count($groups)==0){

	if($ouvrir_infobulle_nav=='y') {
		echo "<script type='text/javascript'>
		setTimeout(\"afficher_div('navigation_classe','y',-100,20);\",1000)
	</script>\n";
	}
	
	require("../lib/footer.inc.php");

    //echo "</body></html>\n";
    die();
}
?>
<form enctype="multipart/form-data" action="edit_class.php" name="formulaire" method="post">
<?php
echo add_token_field();
?>
<!--form enctype="multipart/form-data" action="edit_class.php" name="formulaire" id="form_mat" method=post-->

<!--p>D�finir les priorit�s d'apr�s <input type='button' value="l'ordre alphab�tique" onClick="ordre_alpha();" /> / <input type='button' value="l'ordre par d�faut des mati�res" onClick="ordre_defaut();" /><br /-->
<!--table border='0' width='100%'><tr align='center'><td width='30%'>&nbsp;</td><td width='30%'>Afficher les mati�res dans l'ordre <a href='javascript:ordre_alpha();'>alphab�tique</a> ou <a href='javascript:ordre_defaut();'>des priorit�s</a>.</td>
<td width='30%'>Mettre tous les coefficients � <select name='coefficient_recop' id='coefficient_recopie'-->
<!--table border='0' width='100%'><tr align='center'><td>Afficher les mati�res dans l'ordre <a href='javascript:ordre_alpha();'>alphab�tique</a> ou <a href='javascript:ordre_defaut();'>des priorit�s</a>.</td-->

<table border='0' width='100%' summary='Param�tres'>
<tr align='center'>
<td width='40%'>
<fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;">
<p>Pour cette classe,
<input type='button' value="r�gler les priorit�s d'affichage" onClick='choix_ordre();' />:</p>
<!--ul>
<li><a href='javascript:ordre_defaut();'>�gales aux valeurs d�finies par d�faut</a>,</li>
<li><a href='javascript:ordre_alpha();'>suivant l'ordre alphab�tique des mati�res.</a></li>
</ul-->
<input type='radio' name='ordre' id='ordre_defaut' value='ordre_defaut' /><label for='ordre_defaut' style='cursor: pointer;'> �gales aux valeurs d�finies par d�faut,</label><br />
<input type='radio' name='ordre' id='ordre_alpha' value='ordre_alpha' /><label for='ordre_alpha' style='cursor: pointer;'> suivant l'ordre alphab�tique des mati�res.</label>
</fieldset>
</td>

<td><input type='submit' value='Enregistrer' /></td>

<td width='40%'>
<?php


$call_nom_class = mysql_query("SELECT * FROM classes WHERE id = '$id_classe'");
$display_rang = mysql_result($call_nom_class, 0, 'display_rang');
if($display_rang=='y') {
	$titre="Recalcul des rangs";
	$texte="<p>Un utilisateur a rencontr� un jour le probl�me suivant&nbsp;:<br />Le rang �tait calcul� pour les enseignements, mais pas pour le rang g�n�ral de l'�l�ve.<br />Ce lien permet de forcer le recalcul des rangs pour les enseignements comme pour le rang g�n�ral.<br />Le recalcul sera effectu� lors du prochain affichage de bulletin ou de moyennes.</p>";
	$tabdiv_infobulle[]=creer_div_infobulle('recalcul_rang',$titre,"",$texte,"",25,0,'y','y','n','n');
	
	echo "<fieldset style='padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;'>\n";
	echo "<p>Pour cette classe, <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;forcer_recalcul_rang=y' onclick=\"return confirm_abandon (this, change, '$themessage')\">forcer le recalcul des rangs</a> ";
	
	echo "<a href='#' onclick=\"afficher_div('recalcul_rang','y',-100,20);return false;\"";
	echo ">";
	echo "<img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Forcer le recalcul des rangs' title='Forcer le recalcul des rangs' />\n";
	echo "</a>";
	
	echo ".</p>\n";
	echo "</fieldset>\n";
}
?>

<br />

<fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;">
<!--a href='javascript:coeff();'>Mettre tous les coefficients �</a-->
<input type='button' value='Mettre tous les coefficients �' onClick='coeff(); changement();' />
<select name='coefficient_recop' id='coefficient_recopie' >
<?php
for($i=0;$i<10;$i++){
    echo "<option value='$i'>$i</option>\n";
}
?>
</select>
<!--input type='button' value='Modifier' onClick='coeff();' /-->
<!--Mettre tous les coefficients � <input type='button' value='0' onClick='coeff(0);' /> / <input type='button' value='1' onClick='coeff(1);' /-->
<!--/p-->
</fieldset>
</td></tr></table>
<!--p><i>Pour les enseignements impliquant plusieurs classes, le coefficient s'applique � tous les �l�ves de la classe courante et peut �tre r�gl� ind�pendamment d'une classe � l'autre (pour le r�gler individuellement par �l�ve, voir la liste des �l�ves inscrits).</i-->
<?php
    // si le module ECTS est activ�, on calcul la valeur total d'ECTS attribu�s aux groupes
    if ($gepiSettings['active_mod_ects'] == "y") {
        $total_ects = mysql_result(mysql_query("SELECT sum(valeur_ects) FROM j_groupes_classes WHERE (id_classe = '".$id_classe."' and saisie_ects = TRUE)"), 0);
        echo "<p style='margin-top: 10px;'>Nombre total d'ECTS actuellement attribu�s pour cette classe : ".intval($total_ects)."</p>";
        if ($total_ects < 30) {
            echo "<p style='color: red;'>Attention, le total d'ECTS pour un semestre devrait �tre au moins �gal � 30.</p>";
        }
    }

    $cpt_grp=0;
    $res = mysql_query("SELECT id, nom_court, nom_complet, priority FROM matieres_categories");
    $mat_categories = array();
    while ($row = mysql_fetch_object($res)) {
        $mat_categories[] = $row;
    }
    foreach ($groups as $group) {

        $current_group = get_group($group["id"]);
        $total = count($group["classes"]);
        echo "<br/>";
        echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">";
        echo "<table border = '0' width='100%' summary='Suppression'><tr><td width='25%'>";
        //echo "<a href='edit_class.php?id_groupe=". $group["id"] . "&amp;action=delete_group&amp;id_classe=$id_classe' onclick=\"return confirmlink(this, 'ATTENTION !!! LISEZ CET AVERTISSEMENT : La suppression d\'un enseignement est irr�versible. Une telle suppression ne devrait pas avoir lieu en cours d\'ann�e. Si c\'est le cas, cela peut entra�ner la pr�sence de donn�es orphelines dans la base. Si des donn�es officielles (notes et appr�ciations du bulletin) sont pr�sentes, la suppression sera bloqu�e. Dans le cas contraire, toutes les donn�es li�es au groupe seront supprim�es, incluant les notes saisies par les professeurs dans le carnet de notes ainsi que les donn�es pr�sentes dans le cahier de texte. Etes-vous *VRAIMENT S�R* de vouloir continuer ?', 'Confirmation de la suppression')\"><img src='../images/icons/delete.png' alt='Supprimer' style='width:13px; heigth: 13px;' /></a>";
        echo "<a href='edit_class.php?id_groupe=". $group["id"] . "&amp;action=delete_group&amp;id_classe=$id_classe".add_token_in_url()."'><img src='../images/icons/delete.png' alt='Supprimer' style='width:13px; heigth: 13px;' /></a>";
        echo " -- <span class=\"norme\">";
        echo "<b>";
        if ($total == "1") {
            echo "<a href='edit_group.php?id_groupe=". $group["id"] . "&amp;id_classe=" . $id_classe . "&amp;mode=groupe'>";
        } else {
            echo "<a href='edit_group.php?id_groupe=". $group["id"] . "&amp;id_classe=" . $id_classe . "&amp;mode=regroupement'>";
        }
        //echo $group["description"] . "</a></b>";
        echo htmlentities($group["description"]) . "</a></b>";
        //===============================
        // AJOUT: boireaus
        echo "<input type='hidden' name='enseignement_".$cpt_grp."' id='enseignement_".$cpt_grp."' value=\"".htmlentities($group["description"])."\" />\n";
        //===============================
        echo "</span>";

        //===============================
        // AJOUT: boireaus
        unset($result_matiere);
        // On r�cup�re l'ordre par d�faut des mati�res dans matieres pour permettre de fixer les priorit�s d'apr�s les priorit�s par d�faut de mati�res.
        // Sinon, pour l'affichage, c'est la priorit� dans j_groupes_classes qui est utilis�e � l'affichage dans les champs select.
        $sql="SELECT m.priority, m.categorie_id FROM matieres m, j_groupes_matieres jgc WHERE jgc.id_groupe='".$group["id"]."' AND m.matiere=jgc.id_matiere";
        //$sql="SELECT jgc.priorite, m.categorie_id FROM matieres m, j_groupes_matieres jgm, j_groupes_classes jgc WHERE jgc.id_groupe='".$group["id"]."' AND m.matiere=jgm.id_matiere AND jgc.id_groupe=jgm.id_groupe;";
        //$sql="SELECT priorite FROM j_groupes_classes jgc WHERE jgc.id_groupe='".$group["id"]."' AND id_classe='$id_classe'";
        //echo "$sql<br />\n";
        $result_matiere=mysql_query($sql);
        $ligmat=mysql_fetch_object($result_matiere);
        $mat_priorite[$cpt_grp]=$ligmat->priority;
        //$mat_priorite[$cpt_grp]=$ligmat->priorite;
        $mat_cat_id[$cpt_grp]=$ligmat->categorie_id;
        //$mat_priorite[$cpt_grp]=$ligmat->priorite;
        //echo "\$mat_priorite[$cpt_grp]=".$mat_priorite[$cpt_grp]."<br />\n";
        //===============================

        $j= 1;
        if ($total > 1) {
            echo "&nbsp;&nbsp;(avec : ";
            //==========================================
            // AJOUT: boireaus
            unset($tabclasse);
            //==========================================
            foreach ($group["classes"] as $classe) {
                //==========================================
                // MODIF: boireaus
                /*
                if ($classe["id"] != $id_classe) {
                    echo $classe["classe"];
                    if ($j < $total) echo ", ";
                }
                */
                if ($classe["id"] != $id_classe) {
                    $tabclasse[]=$classe["classe"];
                }
                //==========================================
                $j++;
            }
            //==============================
            // AJOUT: boireaus
            echo $tabclasse[0];
            for($i=1;$i<count($tabclasse);$i++){
                echo ", $tabclasse[$i]";
            }
            //==============================
            echo ")";
        }

        echo "</td>";

        $inscrits = null;
    //echo "=======================================<br />\n";
        foreach($current_group["periodes"] as $period) {
        //echo "\$period[\"num_periode\"]=".$period["num_periode"]."<br />\n";
        if($period["num_periode"]!=""){
            $inscrits .= count($current_group["eleves"][$period["num_periode"]]["list"]) . "-";
        }
        }

        $inscrits = substr($inscrits, 0, -1);

        echo "<td><b><a href='edit_eleves.php?id_groupe=". $group["id"] . "&amp;id_classe=" . $id_classe . "' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/edit_user.png' alt=''/> El�ves inscrits (" . $inscrits . ")</a>";
        echo "</b></td>";
        echo "<td width='20%'>Priorit� d'affichage";
        //=================================
        // MODIF: boireaus
        //echo "<select size=1 name='" . "priorite_" . $current_group["id"] . "'>";
        // Attention � ne pas confondre l'Id et le Name qui ne co�ncident pas.
        echo "<select onchange=\"changement()\" size=1 id='priorite_".$cpt_grp."' name='priorite_" . $current_group["id"] . "'>";
        //=================================
        echo "<option value=0";
        if  ($current_group["classes"]["classes"][$id_classe]["priorite"] == '0') echo " SELECTED";
        echo ">0";
        if ($priority_defaut == 0) echo " (valeur par d�faut)";
        echo "</option>\n";
        $k = 0;

        $k=11;
        $j = 1;
        while ($k < 61){
            echo "<option value=$k"; if ($current_group["classes"]["classes"][$id_classe]["priorite"] == $k) {echo " SELECTED";} echo ">".$j;
            if ($priority_defaut == $k) echo " (valeur par d�faut)";
            echo "</option>\n";
            $k++;
            $j = $k - 10;
        }
        echo "</select>\n";
        echo "</td>";
        // Cat�gories de mati�res
        echo "<td>Cat�gorie : ";
        echo "<select onchange=\"changement()\" size=1 id='categorie_".$cpt_grp."' name='categorie_" .$current_group["id"]. "'>";
        echo "<option value='0'";
        if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] == "0") {echo " SELECTED";}
        echo ">Aucune</option>";
		$tab_categorie_id=array();
        foreach ($mat_categories as $cat) {
			$tab_categorie_id[]=$cat->id;
            echo "<option value='".$cat->id . "'";
            if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] == $cat->id) {
               echo " SELECTED";
            }
            echo ">".html_entity_decode_all_version($cat->nom_court)."</option>";
        }
        echo "</select>";

		if(($current_group["classes"]["classes"][$id_classe]["categorie_id"]!=0)&&(!in_array($current_group["classes"]["classes"][$id_classe]["categorie_id"],$tab_categorie_id))) {
			$temoin_anomalie_categorie="y";
			echo "<a href='#' onclick=\"afficher_div('association_anormale_enseignement_categorie','y',-100,20);return false;\"'><img src='../images/icons/flag2.gif' width='17' height='18' /></a>";
		}

        if(($display_mat_cat=='y')&&($current_group["classes"]["classes"][$id_classe]["categorie_id"]=="0")) {
            //echo "<br />\n";
            $message_categorie_aucune="La mati�re n apparaitra pas sur les bulletins et relev�s de notes. Voir http://www.sylogix.org/wiki/gepi/Enseignement_invisible";
            //echo "<img src='../images/icons/ico_attention.png' width='22' height='19' alt='$message_categorie_aucune' title='$message_categorie_aucune' />\n";

            echo "<a href='#' onclick=\"afficher_div('enseignement_invisible','y',-100,20);return false;\"";
            echo ">";
            echo "<img src='../images/icons/ico_attention.png' width='22' height='19' alt='$message_categorie_aucune' title='$message_categorie_aucune' />\n";
            echo "</a>";

        }
        echo "</td>";

        // Coefficient
        //echo "<td>Coefficient : <input type=\"text\" onchange=\"changement()\" id='coef_".$cpt_grp."' name='". "coef_" . $current_group["id"] . "' value='" . $current_group["classes"]["classes"][$id_classe]["coef"] . "' size=\"5\" /></td></tr>";
        echo "<td align='center'>Coefficient : <input type=\"text\" onchange=\"changement()\" id='coef_".$cpt_grp."' name='". "coef_" . $current_group["id"] . "' value='" . $current_group["classes"]["classes"][$id_classe]["coef"] . "' size=\"5\" />";
        echo "<br />\n";

		/*
        echo "<input type='checkbox' name='note_sup_10_".$current_group["id"]."' id='note_sup_10_".$current_group["id"]."' value='y' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="sup10") {echo "checked ";}
        echo "/><label for='note_sup_10_".$current_group["id"]."'> Note&gt;10</label>\n";
		*/

		echo "<table class='boireaus' summary='Mode de prise en compte dans la moyenne'>\n";
		echo "<tr>\n";
		echo "<th class='small'><label for='note_standard_".$current_group["id"]."' title='La note compte normalement dans la moyenne.'>La note<br />compte</label></th>\n";
		echo "<th class='small'><label for='note_bonus_".$current_group["id"]."' title='Les points au-dessus de 10 coefficient�s sont ajout�s sans augmenter le total des coefficients.'>Bonus</label></th>\n";
		echo "<th class='small'><label for='note_sup_10_".$current_group["id"]."' title='La note ne compte que si elle est sup�rieure ou �gale � 10'>Sup10</label></th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>\n";
        echo "<input type='radio' name='mode_moy_".$current_group["id"]."' id='note_standard_".$current_group["id"]."' value='-' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="-") {echo "checked ";}
        echo "/>\n";
		echo "</td>\n";
		echo "<td>\n";
        echo "<input type='radio' name='mode_moy_".$current_group["id"]."' id='note_bonus_".$current_group["id"]."' value='bonus' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="bonus") {echo "checked ";}
        echo "/>\n";
		echo "</td>\n";
		echo "<td>\n";
        echo "<input type='radio' name='mode_moy_".$current_group["id"]."' id='note_sup_10_".$current_group["id"]."' value='sup10' onchange=\"changement()\" ";
        if($current_group["classes"]["classes"][$id_classe]["mode_moy"]=="sup10") {echo "checked ";}
        echo "/>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

        echo "</td>\n";
        echo "</tr>\n";

        echo "<tr>";
        echo "<td colspan=4>";
        $first = true;
        foreach($current_group["profs"]["list"] as $prof) {
            if (!$first) echo ", ";
            echo $current_group["profs"]["users"][$prof]["prenom"];
            echo " ";
            echo $current_group["profs"]["users"][$prof]["nom"];
            $first = false;
        }
        echo "</td>";
        echo "</tr>";
        if ($gepiSettings['active_mod_ects'] == "y") {
            echo "<tr><td>&nbsp;</td>";
            echo "<td><label for='saisie_ects_".$cpt_grp."'>Activer la saisie ECTS</label>&nbsp;<input id='saisie_ects_".$cpt_grp."' type='checkbox' name='saisie_ects_".$current_group["id"]."' value='1'";
            if($current_group["classes"]["classes"][$id_classe]["saisie_ects"]) {
                echo " checked";
            }
            echo "/>";
            echo "<input id='no_saisie_ects_".$cpt_grp."' type='hidden' name='no_saisie_ects_".$current_group["id"]."' value='0' />";
            echo "</td>";
            echo "<td>";
            echo "Nombre d'ECTS par d�faut pour une p�riode : ";
            echo "<select onchange=\"changement()\" id='valeur_ects_".$cpt_grp."' name='". "valeur_ects_" . $current_group["id"] . "'>";
            for($c=0;$c<31;$c++) {
                echo "<option value='$c'";
                if (intval($current_group["classes"]["classes"][$id_classe]["valeur_ects"]) == $c) echo " SELECTED ";
                echo ">$c</option>";
            }
            echo "</select>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</fieldset>";

        $cpt_grp++;
    }


if(isset($temoin_anomalie_categorie)&&($temoin_anomalie_categorie=='y')) {
	$titre="Anomalie d'association enseignement/cat�gorie";
	$texte="<p>Cet enseignement est associ� � une cat�gorie qui n'existe pas ou plus.<br />Veuillez contr�ler les param�tres et cliquer sur <b>Enregistrer</b> pour corriger.";
	$tabdiv_infobulle[]=creer_div_infobulle('association_anormale_enseignement_categorie',$titre,"",$texte,"",30,0,'y','y','n','n');
}

echo "<input type='hidden' name='is_posted' value='1' />";
echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />";
echo "<p align='center'><input type='submit' value='Enregistrer' /></p>";
echo "</form>";

//================================================
// AJOUT:boireaus
echo "<script type='text/javascript' language='javascript'>
    function choix_ordre(){
    if(document.getElementById('ordre_alpha').checked){
        ordre_alpha();
    }
    else{
        ordre_defaut();
    }
    }
    function ordre_alpha(){
        cpt=0;
        enseignement=new Array();
        while(cpt<$cpt_grp){
            enseignement[cpt]=document.getElementById('enseignement_'+cpt).value;
            cpt++;
        }
        enseignement.sort();
        cpt=0;
        while(cpt<$cpt_grp){
            for(i=0;i<$cpt_grp;i++){
                docens=document.getElementById('enseignement_'+i).value;
                if(enseignement[cpt]==document.getElementById('enseignement_'+i).value){
                    document.getElementById('priorite_'+i).selectedIndex=cpt+1;
                }
            }
            cpt++;
        }
        //document.forms['formulaire'].submit();
        changement();
    }

    function ordre_defaut(){";
        for($i=0;$i<count($mat_priorite);$i++){
            $rang=0;
            if($mat_priorite[$i]>0){$rang=$mat_priorite[$i]-10;}
            //echo "document.getElementById('priorite_'+$i).selectedIndex=$mat_priorite[$i];\n";
            echo "document.getElementById('priorite_'+$i).selectedIndex=$rang;\n";
        }
echo "}

    function coeff(){
        nombre=document.getElementById('coefficient_recopie').value;
        chaine_reg=new RegExp('[0-9]+');
        if(nombre.replace(chaine_reg,'').length!=0){
            nombre=0;
        }
        cpt=0;
        while(cpt<$cpt_grp){
            document.getElementById('coef_'+cpt).value=nombre;
            cpt++;
        }
        //document.forms['formulaire'].submit();
        changement();
    }
</script>\n";
?>


<!--form enctype="multipart/form-data" action="edit_class.php" name="formulaire2" method=post>
    <input type='button' value="D�finir les priorit�s d'apr�s l'ordre alphab�tique" onClick="ordre_alpha();" /><br />
    Mettre tous les coefficients � <input type='button' value='0' onClick='coeff(0);' /> / <input type='button' value='1' onClick='coeff(1);' />
</form-->
<p><i>Remarques:</i></p>
<ul>
<li>Un seul coefficient non nul provoque l'apparition de tous les coefficients sur les bulletins.</li>
<li>Un/des coefficients non nul(s) est/sont n�cessaire(s) pour que la ligne moyenne g�n�rale apparaisse sur le bulletin.</li>
<!--li>Les coefficients r�gl�s ici ne s'appliquent qu'� la classe <?php echo $classe["classe"]?>, m�me dans le cas des enseignements concernant d'autres classes.</li-->
<li>Pour les enseignements impliquant plusieurs classes, le coefficient s'applique � tous les �l�ves de la classe courante et peut �tre r�gl� ind�pendamment d'une classe � l'autre (pour le r�gler individuellement par �l�ve, voir la liste des �l�ves inscrits).<br />
Les coefficients r�gl�s ici ne s'appliquent donc qu'� la classe
<?php
    // Bizarre... $classe peut contenir une autre classe que celle en cours???
    $classe_tmp = get_classe($id_classe);
    echo $classe_tmp["classe"];
?>
, m�me dans le cas des enseignements concernant des regroupements de plusieurs classes.</li>
<li>
	Les modes de prise en compte de la moyenne d'un enseignement dans la moyenne g�n�rale sont les suivants&nbsp;:
	<ul>
		<li>La note compte&nbsp;: La note compte normalement dans la moyenne.</li>
		<li>Bonus&nbsp;: Les points au-dessus de 10 sont coefficient�s et ajout�s au total des points, mais le total des coefficients n'est pas augment�.</li>
		<li>Sup10&nbsp;: La note n'est compt�e que si elle est sup�rieure � 10.<br />
		Remarque&nbsp;: Cela n'am�liore pas n�cessairement la moyenne g�n�rale de l'�l�ve puisque s'il avait 13 de moyenne g�n�rale sans cette note, il perd des points s'il a 12 � un enseignement compt� sup10.<br />
		Et l'�l�ve qui a 9 � cet enseignement ne perd pas de point... injuste, non?</li>
	</ul>
</li>
</ul>
<?php

if($ouvrir_infobulle_nav=='y') {
	echo "<script type='text/javascript'>
	setTimeout(\"afficher_div('navigation_classe','y',-100,20);\",1000)
</script>\n";
}

require("../lib/footer.inc.php");

?>