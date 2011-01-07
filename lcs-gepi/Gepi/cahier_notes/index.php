<?php
/*
 * $Id: index.php 5961 2010-11-22 22:25:23Z crob $
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

/*
$fich=fopen("/tmp/test_img.txt","a+");
fwrite($fich,"HTTP_REFERER=".$_SERVER['HTTP_REFERER']."\n");
fwrite($fich,"\$_SERVER['REQUEST_URI']=".$_SERVER['REQUEST_URI']."\n");
fwrite($fich,date("Y m d - H i s")."\n");
//fwrite($fich,"==================================\n");
fclose($fich);
*/
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

//On v�rifie si le module est activ�
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activ�.");
}

unset($id_groupe);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : (isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);
if ($id_groupe == "no_group") {
    $id_groupe = NULL;
    unset($_GET['id_groupe']);
    $_SESSION['id_groupe_session'] = "";
}

//on met le groupe dans la session, pour naviguer entre absence, cahier de texte et autres
if ($id_groupe != NULL) {
    $_SESSION['id_groupe_session'] = $id_groupe;
} else if (isset($_SESSION['id_groupe_session']) && $_SESSION['id_groupe_session'] != "") {
     $_GET['id_groupe'] = $_SESSION['id_groupe_session'];
     $id_groupe = $_SESSION['id_groupe_session'];
}

if (is_numeric($id_groupe) && $id_groupe > 0) {

    $sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' AND login='".$_SESSION['login']."';";
    $test_prof_groupe=mysql_query($sql);
    if(mysql_num_rows($test_prof_groupe)==0) {
        $mess=rawurlencode("Vous tentez de p�n�trer dans un carnet de notes qui ne vous appartient pas !");
		unset($_SESSION['id_groupe_session']);
		tentative_intrusion(1, "Tentative d'acc�s � un carnet de notes qui ne lui appartient pas (id_groupe=$id_groupe)");
        // Sans le unset($_SESSION['id_groupe_session']) avec ces tentative_intrusion(), une modif d'id_groupe en barre d'adresse me provoquait 7 insertions... d'o� un score � +7 et une d�connexion
        header("Location: index.php?msg=$mess");
        die();
    }

    $current_group = get_group($id_groupe);
}

// On teste si le carnet de notes appartient bien � la personne connect�e
if ((isset($_POST['id_racine'])) or (isset($_GET['id_racine']))) {
    $id_racine = isset($_POST['id_racine']) ? $_POST['id_racine'] : (isset($_GET['id_racine']) ? $_GET['id_racine'] : NULL);
    if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
        $mess=rawurlencode("Vous tentez de p�n�trer dans un carnet de notes qui ne vous appartient pas !");
		unset($_SESSION['id_groupe_session']);
		tentative_intrusion(1, "Tentative d'acc�s � un carnet de notes qui ne lui appartient pas (id_racine=$id_racine)");
        header("Location: index.php?msg=$mess");
        die();
    }
}

/*
$fich=fopen("/tmp/test_img.txt","a+");
fwrite($fich,"Juste avant Header\n");
fclose($fich);
*/
//**************** EN-TETE *****************
$titre_page = "Carnet de notes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************

/*
$fich=fopen("/tmp/test_img.txt","a+");
fwrite($fich,"Juste apr�s Header\n");
//fwrite($fich,"==================================\n");
fclose($fich);
*/

//debug_var();

//-----------------------------------------------------------------------------------
if (isset($_GET['id_groupe']) and isset($_GET['periode_num'])) {
//if (isset($id_groupe) and isset($periode_num)) {
    $id_groupe = $_GET['id_groupe'];
    $periode_num = $_GET['periode_num'];
    $login_prof = $_SESSION['login'];
    $appel_cahier_notes = mysql_query("SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe' and periode='$periode_num')");
    $nb_cahier_note = mysql_num_rows($appel_cahier_notes);
    if ($nb_cahier_note == 0) {
        $nom_complet_matiere = $current_group["matiere"]["nom_complet"];
        $nom_court_matiere = $current_group["matiere"]["matiere"];
        $reg = mysql_query("INSERT INTO cn_conteneurs SET id_racine='', nom_court='".traitement_magic_quotes($current_group["description"])."', nom_complet='". traitement_magic_quotes($nom_complet_matiere)."', description = '', mode = '2', coef = '1.0', arrondir = 's1', ponderation = '0.0', display_parents = '0', display_bulletin = '1', parent = '0'");
        if ($reg) {
            $id_racine = mysql_insert_id();
            $reg = mysql_query("UPDATE cn_conteneurs SET id_racine='$id_racine', parent = '0' WHERE id='$id_racine'");
            $reg = mysql_query("INSERT INTO cn_cahier_notes SET id_groupe = '$id_groupe', periode = '$periode_num', id_cahier_notes='$id_racine'");
        }
    } else {
        $id_racine = mysql_result($appel_cahier_notes, 0, 'id_cahier_notes');
    }
}

// Recopie de la structure de la periode pr�c�dente
if ((isset($_GET['creer_structure'])) and ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2)) {
  check_token();

  function recopie_arbo($id_racine, $id_prec,$id_new) {
    global $vide;
    $query_cont = mysql_query("SELECT * FROM cn_conteneurs
    WHERE (
        id != id_racine and
        parent = '".$id_prec."'
        )");
    $nb_lignes = mysql_num_rows($query_cont);
    $i = 0;
    while ($i < $nb_lignes) {
        $id_prec = mysql_result($query_cont,$i,'id');
        $val2 = mysql_result($query_cont,$i,'id_racine');
        $val3 = mysql_result($query_cont,$i,'nom_court');
        $val4 = mysql_result($query_cont,$i,'nom_complet');
        $val5 = mysql_result($query_cont,$i,'description');
        $val6 = mysql_result($query_cont,$i,'mode');
        $val7 = mysql_result($query_cont,$i,'coef');
        $val8 = mysql_result($query_cont,$i,'arrondir');
        $val9 = mysql_result($query_cont,$i,'ponderation');
        $val10 = mysql_result($query_cont,$i,'display_parents');
        $val11 = mysql_result($query_cont,$i,'display_bulletin');
        $val12 = mysql_result($query_cont,$i,'parent');
        $query_insert = mysql_query("INSERT INTO cn_conteneurs
        set id_racine = '".$id_racine."',
        nom_court = '".traitement_magic_quotes($val3)."',
        nom_complet = '".traitement_magic_quotes($val4)."',
        description = '".traitement_magic_quotes($val5)."',
        mode = '".$val6."',
        coef = '".$val7."',
        arrondir = '".$val8."',
        ponderation = '".$val9."',
        display_parents = '".$val10."',
        display_bulletin = '".$val11."',
        parent = '".$id_new."' ");
        $vide = 'no';
        $id_new1 = mysql_insert_id();
        recopie_arbo($id_racine, $id_prec, $id_new1);
        $i++;
    }

  }

    $periode_num = $_GET['periode_num'];
    $id_cahier_prec = sql_query1("SELECT id_cahier_notes FROM cn_cahier_notes
    WHERE (
        id_groupe = '".$id_groupe."' and
        periode = '".($periode_num-1)."'
        )
    ");
    $vide = 'yes';
    recopie_arbo($id_racine,$id_cahier_prec,$id_racine);
    if ($vide == 'yes') {
		echo "<p><center><b><font color='red'>Structure vide : aucun";
		if(getSettingValue('gepi_denom_boite_genre')=="f") {$accord_f="e";} else {$accord_f="";}
		//echo "e bo�te";
		echo "$accord_f ";
		echo getSettingValue('gepi_denom_boite');
		echo " n'a �t� cr�$accord_f dans le carnet de notes de la p�riode pr�c�dente.</font></b></center></p><hr />";
	}
}
/*
$fich=fopen("/tmp/test_img.txt","a+");
fwrite($fich,"Avant isset(\$id_racine)\n");
fclose($fich);
*/
if  (isset($id_racine) and ($id_racine!='')) {
    $appel_conteneurs = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='$id_racine'");
    $nom_court = mysql_result($appel_conteneurs, 0, 'nom_court');

    $appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes = '$id_racine'");
    $id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
    if (!isset($current_group)) $current_group = get_group($id_groupe);
    $periode_num = mysql_result($appel_cahier_notes, 0, 'periode');
    include "../lib/periodes.inc.php";

	/*
	$fich=fopen("/tmp/test_img.txt","a+");
	fwrite($fich,"Juste avant test suppr\n");
	//fwrite($fich,"\$_GET['del_dev']=".$_GET['del_dev']."\n");
	//fwrite($fich,"\$_GET['js_confirmed']=".$_GET['js_confirmed']."\n");
	fwrite($fich,"++++++++++++++++++++++++++\n");
	fclose($fich);
	*/

    //
    // Suppression d'une �valuation
    //
    if ((isset($_GET['del_dev'])) and ($_GET['js_confirmed'] ==1)) {
        $temp = $_GET['del_dev'];
		/*
		$fich=fopen("/tmp/test_img.txt","a+");
		fwrite($fich,"\$_GET['del_dev']=".$_GET['del_dev']."\n");
		fwrite($fich,"==================================\n");
		fclose($fich);
		*/
	    //if((isset($_GET['alea']))&&($_GET['alea']==$_SESSION['gepi_alea'])) {
		check_token();

			$sql0="SELECT id_conteneur FROM cn_devoirs WHERE id='$temp'";
			//echo "$sql0<br />";
			$sql= mysql_query($sql0);
			if(mysql_num_rows($sql)==0) {
				echo "<p style='color:red'>Le devoir $temp n'a pas �t� trouv�.</p>\n";
			}
			else {
				$id_cont = mysql_result($sql, 0, 'id_conteneur');
				$sql = mysql_query("DELETE FROM cn_notes_devoirs WHERE id_devoir='$temp'");
				$sql = mysql_query("DELETE FROM cn_devoirs WHERE id='$temp'");
		
				// On teste si le conteneur est vide
				$sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$id_cont'");
				$nb_dev = mysql_num_rows($sql);
				$sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$id_cont'");
				$nb_cont = mysql_num_rows($sql);
				if (($nb_dev == 0) or ($nb_cont == 0)) {
					$sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_cont'");
				}
		
				// On teste si le carnet de notes est vide
				$sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$id_racine'");
				$nb_dev = mysql_num_rows($sql);
				$sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$id_racine'");
				$nb_cont = mysql_num_rows($sql);
				if (($nb_dev == 0) and ($nb_cont == 0)) {
					$sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_racine'");
				} else {
					$arret = 'no';
					mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_racine,$arret);
				}
			}
		/*
		}
		else {
			$texte_mail="Tentative de suppression de devoir avec un al�a qui ne co�ncide pas avec celui de la session\nLa suppression tent�e �tait \$_SERVER['REQUEST_URI']=".$_SERVER['REQUEST_URI']."\n";
			mail_alerte("Anomalie de suppression de devoir",$texte_mail,'y');
			echo "<p style='color:red'>$texte_mail</p>\n";
		}
		*/
    }
    //
    // Supression d'un conteneur
    //
    if ((isset($_GET['del_cont'])) and ($_GET['js_confirmed'] ==1)) {
		check_token();

	    //if((isset($_GET['alea']))&&($_GET['alea']==$_SESSION['gepi_alea'])) {
			$temp = $_GET['del_cont'];
			$sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$temp'");
			$nb_dev = mysql_num_rows($sql);
			$sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$temp'");
			$nb_cont = mysql_num_rows($sql);
			if (($nb_dev != 0) or ($nb_cont != 0)) {
				echo "<script type=\"text/javascript\" language=\"javascript\">\n";
				echo 'alert("Impossible de supprimer une bo�te qui n\'est pas vide !");\n';
				echo "</script>\n";
			} else {
				$sql = mysql_query("DELETE FROM cn_conteneurs WHERE id='$temp'");
				$sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$temp'");
				// On teste si le carnet de notes est vide
				$sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$id_racine'");
				$nb_dev = mysql_num_rows($sql);
				$sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$id_racine'");
				$nb_cont = mysql_num_rows($sql);
				if (($nb_dev == 0) and ($nb_cont == 0)) {
					$sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_racine'");
				} else {
					$arret = 'no';
					mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_racine,$arret);
				}
	
			}
		/*
		}
		else {
			$texte_mail="Tentative de suppression d'un conteneur avec un al�a qui ne co�ncide pas avec celui de la session\nLa suppression tent�e �tait \$_SERVER['REQUEST_URI']=".$_SERVER['REQUEST_URI']."\n";
			mail_alerte("Anomalie de suppression de conteneur",$texte_mail,'y');
			echo "<p style='color:red'>$texte_mail</p>\n";
		}
		*/
    }

    //echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"index.php\" method=\"POST\">\n";
    echo "<div class='norme'>\n";
	echo "<form enctype=\"multipart/form-data\" name= \"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"get\">\n";
    echo "<p class='bold'>\n";
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a> | \n";
    echo "<a href='index.php?id_groupe=no_group'> Mes enseignements </a> | \n";



if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
	if($_SESSION['statut']=='professeur') {
		$login_prof_groupe_courant=$_SESSION["login"];
	}
	else {
		$tmp_current_group=get_group($id_groupe);

		$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
	}

	$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis mati�re");

	if(!empty($tab_groups)) {

		$chaine_options_classes="";

		$num_groupe=-1;
		$nb_groupes_suivies=count($tab_groups);

		//echo "count(\$tab_groups)=".count($tab_groups)."<br />";

		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		//foreach($tab_groups as $tmp_group) {
		for($loop=0;$loop<count($tab_groups);$loop++) {
			// On ne retient que les groupes qui ont un nombre de p�riodes au moins �gal � la p�riode s�lectionn�e
			if($tab_groups[$loop]["nb_periode"]>=$periode_num) {
				if($tab_groups[$loop]['id']==$id_groupe){
					$num_groupe=$loop;

					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='true'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";

					$temoin_tmp=1;
					if(isset($tab_groups[$loop+1])){
						$id_grp_suiv=$tab_groups[$loop+1]['id'];

						//$chaine_options_classes.="<option value='".$tab_groups[$loop+1]['id']."'>".$tab_groups[$loop+1]['name']." (".$tab_groups[$loop+1]['classlist_string'].")</option>\n";
					}
					else{
						$id_grp_suiv=0;
					}
				}
				else {
					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['description']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
				}

				if($temoin_tmp==0){
					$id_grp_prec=$tab_groups[$loop]['id'];

					//$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
				}
			}
		}
		// =================================

		/*
		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_num=$periode_num";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement pr�c�dent</a>";
			}
		}
		*/

		if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {

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
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";

			echo "<input type='hidden' name='periode_num' id='periode_num' value='$periode_num' />\n";
			//echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
			echo "P�riode $periode_num: <select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
			echo $chaine_options_classes;
			echo "</select> | \n";
		}

		/*
		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num";
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignement suivant</a>";
				}
		}
		*/
	}
	// =================================
}

    //echo "<a href='index.php?id_groupe=" . $current_group["id"] . "'>" . $current_group["description"] . " : Choisir une autre p�riode</a>|";
    //echo "<a href='index.php?id_groupe=" . $current_group["id"] . "'> " . htmlentities($current_group["description"]) . " : Choisir une autre p�riode</a> | \n";
    echo "<a href='index.php?id_groupe=" . $current_group["id"] . "'> Choisir une autre p�riode</a> | \n";

	// Recuperer la liste des cahiers de notes
	$sql="SELECT * FROM cn_cahier_notes ccn where id_groupe='$id_groupe' ORDER BY periode;";
	$res_cn=mysql_query($sql);
	if(mysql_num_rows($res_cn)>1) {
		// On ne propose pas de champ SELECT pour un seul canier de notes
		echo "<script type='text/javascript'>
var tab_per_cn=new Array();\n";


		$chaine_options_periodes="";
		while($lig_cn=mysql_fetch_object($res_cn)) {
			$chaine_options_periodes.="<option value='$lig_cn->id_cahier_notes'";
			if($lig_cn->periode==$periode_num) {$chaine_options_periodes.=" selected='true'";}
			$chaine_options_periodes.=">$lig_cn->periode</option>\n";

			//echo "var tab_per_cn[$lig_cn->id_cahier_notes]=$lig_cn->periode;\n";
			echo "tab_per_cn[$lig_cn->id_cahier_notes]=$lig_cn->periode;\n";
		}

		$index_num_periode=$periode_num-1;

		echo "
	change='no';

	function confirm_changement_periode(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			//alert(document.getElementById('id_racine').selectedIndex);
			//alert(document.getElementById('id_racine').options[document.getElementById('id_racine').selectedIndex].value);
			//alert(document.form1.elements['id_racine'].options[document.form1.elements['id_racine'].selectedIndex].value);
			//i=document.getElementById('id_racine').options[document.getElementById('id_racine').selectedIndex].value;

			document.getElementById('periode_num').value=tab_per_cn[document.getElementById('id_racine').options[document.getElementById('id_racine').selectedIndex].value];
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.getElementById('periode_num').value=tab_per_cn[document.getElementById('id_racine').options[document.getElementById('id_racine').selectedIndex].value];
				document.form1.submit();
			}
			else{
				document.getElementById('id_racine').selectedIndex=$index_num_periode;
			}
		}
	}
</script>\n";
	
		//echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo "<span title='Acc�der au cahier de notes de la p�riode (ne sont propos�es que les p�riodes pour lesquelles le cahier de notes a �t� initialis�)'>P�riode</span> <select name='id_racine' id='id_racine' onchange=\"confirm_changement_periode(change, '$themessage');\">\n";
		echo $chaine_options_periodes;
		echo "</select> | \n";
	}


	//==================================
	// AJOUT: boireaus EXPORT...
    echo "<a href='export_cahier_notes.php?id_racine=".$id_racine."'>Exporter les notes</a> | \n";
	//==================================

    if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {

		//==================================
		// AJOUT: boireaus EXPORT...
		echo "<a href='import_cahier_notes.php?id_racine=".$id_racine."'>Importer les notes</a> | \n";
		//==================================

        //echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&mode_navig=retour_index'>Cr�er une bo�te</a>|";

        //echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_index'>Cr�er une bo�te</a>|\n";

        //echo "<br/><a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_index'> Cr�er un";
        echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_index'> Cr�er un";
        if(getSettingValue("gepi_denom_boite_genre")=='f'){echo "e";}
        echo " ".htmlentities(strtolower(getSettingValue("gepi_denom_boite")))." </a> | \n";

        //echo "<a href='add_modif_dev.php?id_conteneur=$id_racine&mode_navig=retour_index'>Cr�er une �valuation</a>|";
        echo "<a href='add_modif_dev.php?id_conteneur=$id_racine&amp;mode_navig=retour_index'> Cr�er une �valuation </a> | \n";
        if ($periode_num!='1')  {
            $themessage = 'En cliquant sur OK, vous allez cr�er la m�me structure de bo�tes que celle de la p�riode pr�c�dente. Si des bo�tes existent d�j�, elles ne seront pas supprim�es.';
            //echo "<a href='index.php?id_groupe=$id_groupe&periode_num=$periode_num&creer_structure=yes'  onclick=\"return confirm_abandon (this, 'yes', '$themessage')\">Cr�er la m�me structure que la p�riode pr�c�dent</a>|";
            echo "<a href='index.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;creer_structure=yes".add_token_in_url()."'  onclick=\"return confirm_abandon (this, 'yes', '$themessage')\"> Cr�er la m�me structure que la p�riode pr�c�dente</a> | \n";
			//echo "&nbsp;| \n";
        }
    }

	// Le retour n'est pas parfait... il faudrait aussi periode_num dans chemin_retour
	// ou alors stocker ici l'info en session pour la p�riode...
	echo "<a href=\"../groupes/signalement_eleves.php?id_groupe=$id_groupe&amp;chemin_retour=../cahier_notes/index.php?id_groupe=$id_groupe\"> Signaler des erreurs d'affectation</a>";

    //echo "</b>\n";
    echo "</p>\n";
	echo "</form>\n";
	echo "</div>\n";

    //echo "<h2 class='gepi'>Carnet de notes : ". $current_group["description"] . " ($nom_periode[$periode_num])</h2>\n";
    echo "<h2 class='gepi'>Carnet de notes : ". htmlentities($current_group["description"]) . " ($nom_periode[$periode_num])</h2>\n";
    //echo "<p class='bold'> Classe(s) : " . $current_group["classlist_string"] . " | Mati�re : " . $current_group["matiere"]["nom_complet"] . "(" . $current_group["matiere"]["matiere"] . ")";
    echo "<p class='bold'> Classe(s) : " . $current_group["classlist_string"] . " | Mati�re : " . htmlentities($current_group["matiere"]["nom_complet"]) . "(" . htmlentities($current_group["matiere"]["matiere"]) . ")";
    // On teste si le carnet de notes est partag� ou non avec d'autres utilisateurs
    $login_prof = $_SESSION['login'];
    if (count($current_group["profs"]["list"]) > 1) {
        echo " | Carnet de notes partag� avec : ";
        $flag = 0;
        foreach($current_group["profs"]["users"] as $prof) {
            $l_prof = $prof["login"];
            $nom_prof = $prof["nom"];
            $prenom_prof = $prof["prenom"];
            if ($l_prof != $login_prof) {
                if ($flag > 0) echo ", ";
                echo $prenom_prof." ".$nom_prof;
                $flag++;
            }
        }
    }
    echo "</p>\n";

    echo "<h3 class='gepi'>Liste des �valuations du carnet de notes</h3>\n";
    $empty = affiche_devoirs_conteneurs($id_racine,$periode_num, $empty, $current_group["classe"]["ver_periode"]["all"][$periode_num]);
    echo "</ul>\n";
    if ($empty == 'yes') echo "<p><b>Actuellement, aucune �valuation.</b> Vous devez cr�er au moins une �valuation.</p>\n";
    if ($empty != 'yes') {
        if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {
            echo "<h3 class='gepi'>Saisie du bulletin ($nom_periode[$periode_num])</h3>\n";

			$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND periode='$periode_num';";
			$res_ele_grp=mysql_query($sql);
			$nb_ele_grp=mysql_num_rows($res_ele_grp);

			$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='$id_groupe' AND periode='$periode_num' AND statut!='-';";
			$res_mn=mysql_query($sql);
			$nb_mn=mysql_num_rows($res_mn);
			if($nb_mn==0) {
				$info_mn="<span style='color:red; font-size: small;'>(actuellement vide)</span>";
			}
			else {
				if($nb_mn==$nb_ele_grp) {
					$info_mn="<span style='color:green; font-size: small;'>($nb_mn/$nb_ele_grp)</span>";
				}
				else {
					$info_mn="<span style='color:red; font-size: small;'>($nb_mn/$nb_ele_grp)</span>";
				}
			}

			$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='$id_groupe' AND periode='$periode_num' AND appreciation!='';";
			$res_ma=mysql_query($sql);
			$nb_ma=mysql_num_rows($res_ma);
			if($nb_ma==0) {
				$info_ma="<span style='color:red; font-size: small;'>(actuellement vide)</span>";
			}
			else {
				if($nb_ma==$nb_ele_grp) {
					$info_ma="<span style='color:green; font-size: small;'>($nb_ma/$nb_ele_grp)</span>";
				}
				else {
					$info_ma="<span style='color:red; font-size: small;'>($nb_ma/$nb_ele_grp)</span>";
				}
			}

            echo "<ul><li><a href='../saisie/saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num&amp;retour_cn=yes'>Saisie des moyennes</a> $info_mn</li>\n";
            echo "<li><a href='../saisie/saisie_appreciations.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num'>Saisie des appr�ciations</a> $info_ma</li></ul>\n";
        } else {
            echo "<h3 class='gepi'>Visualisation du bulletin ($nom_periode[$periode_num])</h3>\n";
            echo "<ul><li><a href='../saisie/saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num&amp;retour_cn=yes'>Visualisation des moyennes</a> (<b>".$gepiClosedPeriodLabel."</b>).</li>\n";
            echo "<li><a href='../saisie/saisie_appreciations.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num'>Visualisation des appr�ciations</a> (<b>".$gepiClosedPeriodLabel."</b>).</li></ul>\n";
        }

    }

}

if (isset($_GET['id_groupe']) and !(isset($_GET['periode_num'])) and !(isset($id_racine))) {

    $matiere_nom = $current_group["matiere"]["nom_complet"];
    $matiere_nom_court = $current_group["matiere"]["matiere"];

    $nom_classes = $current_group["classlist_string"];

    echo "<p class=bold>";
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|";
    echo "<a href='index.php?id_groupe=no_group'> Mes enseignements </a>|</p>\n";
    echo "<p class='bold'>Enseignement : ".htmlentities($current_group["description"])." (" . $current_group["classlist_string"] .")</p>\n";

    echo "<H3>Visualisation/modification - Choisissez la p�riode : </H3>\n";
    $i="1";
    while ($i < ($current_group["nb_periode"])) {
        echo "<p><a href='index.php?id_groupe=$id_groupe&amp;periode_num=$i'>".ucfirst($current_group["periodes"][$i]["nom_periode"])."</a>";

	$sql="SELECT * FROM periodes WHERE num_periode='$i' AND id_classe='".$current_group["classes"]["list"][0]."' AND verouiller='N'";
	//echo "<br />$sql<br />";
	$res_test=mysql_query($sql);
	if(mysql_num_rows($res_test)==0){
		echo " (<i>p�riode close</i>)";
	}

	echo "</p>\n";
    $i++;
    }
    echo "<h3>Visualisation uniquement : </h3>\n";
    echo "<p><a href='toutes_notes.php?id_groupe=$id_groupe'>Voir toutes les �valuations de l'ann�e</a></p>\n";

}

if (!(isset($_GET['id_groupe'])) and !(isset($_GET['periode_num'])) and !(isset($id_racine))) {
    ?>
    <p class=bold><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a></p>
    <p>Acc�der au carnet de notes : </p>
    <?php
    //$groups = get_groups_for_prof($_SESSION["login"]);
    $groups = get_groups_for_prof($_SESSION["login"],"classe puis mati�re");

    if (empty($groups)) {
        echo "<br /><br />";
        //echo "<b>Aucun cahier de texte n'est disponible.</b>";
        echo "<b>Aucun cahier de notes n'est disponible.</b>";
        echo "<br /><br />";
    }

    foreach($groups as $group) {
       echo "<p><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
       //echo "<a href='index.php?id_groupe=" . $group["id"] ."'>" . $group["description"] . "</a> <span class=small>(" . $group["matiere"]["nom_complet"] .")</span></p>";
       echo "<a href='index.php?id_groupe=" . $group["id"] ."'>" . htmlentities($group["description"]) . "</a>";
       echo "</span></p>\n";
    }
}
require("../lib/footer.inc.php");
?>
