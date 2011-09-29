<?php
/*
 * $Id: saisie_mentions.php 7478 2011-07-21 19:07:03Z crob $
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut creer des variables non prot�g�es (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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
//include("../fckeditor/fckeditor.php") ;

// Check access
// INSERT INTO droits VALUES ('/saisie/saisie_mentions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie de mentions', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$msg="";
$saisie_mention=isset($_POST['saisie_mention']) ? $_POST['saisie_mention'] : NULL;
$nouvelle_mention=isset($_POST['nouvelle_mention']) ? $_POST['nouvelle_mention'] : NULL;
$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : NULL;
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

$associer_mentions_classes=isset($_POST['associer_mentions_classes']) ? $_POST['associer_mentions_classes'] : (isset($_GET['associer_mentions_classes']) ? $_GET['associer_mentions_classes'] : NULL);

$saisie_association_mentions_classes=isset($_POST['saisie_association_mentions_classes']) ? $_POST['saisie_association_mentions_classes'] : NULL;
$id_mention=isset($_POST['id_mention']) ? $_POST['id_mention'] : array();

$saisie_ordre_mentions=isset($_POST['saisie_ordre_mentions']) ? $_POST['saisie_ordre_mentions'] : NULL;
$ordre_id_mention=isset($_POST['ordre_id_mention']) ? $_POST['ordre_id_mention'] : array();

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

if(isset($saisie_mention)) {
	check_token();

	$cpt_suppr=0;
	$tab_mentions=get_mentions();
	$tab_mentions_aff=get_tab_mentions_affectees();
	for($i=0;$i<count($suppr);$i++) {
		if(!in_array($suppr[$i],$tab_mentions_aff)) {
			$sql="DELETE FROM j_mentions_classes WHERE id_mention='$suppr[$i]';";
			$nettoyage=mysql_query($sql);
			if(!$nettoyage) {
				$msg.="Erreur lors de la suppression de l'assoctiaition de la $gepi_denom_mention <b>".$tab_mentions[$suppr[$i]]."</b> avec une ou des classes.<br />";
			}
			else {
				$sql="DELETE FROM mentions WHERE id='$suppr[$i]';";
				$nettoyage=mysql_query($sql);
				if(!$nettoyage) {
					$msg.="Erreur lors de la suppression de la $gepi_denom_mention <b>".$tab_mentions[$suppr[$i]]."</b><br />";
				}
				else {
					$cpt_suppr++;
				}
			}
		}
	}
	if($cpt_suppr>0) {
		$msg.="$cpt_suppr $gepi_denom_mention(s) supprim�e(s).<br />";
	}

	if($nouvelle_mention!="") {
		$sql="SELECT 1=1 FROM mentions WHERE mention='$nouvelle_mention';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$msg.="La $gepi_denom_mention <b>$nouvelle_mention</b> existe d�j�.<br />";
		}
		else {
			$sql="INSERT INTO mentions SET mention='$nouvelle_mention';";
			$res=mysql_query($sql);
			if(!$res) {
				$msg.="Erreur lors de l'ajout de la $gepi_denom_mention <b>$nouvelle_mention</b><br />";
			}
			else {
				$msg.=ucfirst($gepi_denom_mention)." <b>$nouvelle_mention</b> ajout�e.<br />";
			}
		}
	}
}


if(isset($saisie_association_mentions_classes)) {
	check_token();

	$enregistrement_ok="n";

	$cpt_reg=0;
	$tab_mentions=get_mentions();
	for($i=0;$i<count($id_classe);$i++) {
		$tab_mentions_aff=get_tab_mentions_affectees($id_classe[$i]);

		$tab_mentions_classe=array();
		//$sql="SELECT DISTINCT a.id_mention FROM avis_conseil_classe a, j_eleves_classes j WHERE j.periode=a.periode AND j.login=a.login AND j.id_classe='$id_classe[$i]';";
		$sql="SELECT DISTINCT j.id_mention FROM j_mentions_classes j WHERE j.id_classe='$id_classe[$i]';";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while ($lig=mysql_fetch_object($res)) {
				$tab_mentions_classe[]=$lig->id_mention;
			}
		}

		/*
		echo "<p>\$tab_mentions_classe:<br />";
		foreach($tab_mentions_classe as $key => $value) {
		echo "\$tab_mentions_classe[$key]=$value<br />";
		}
		echo "</p>
		<hr />";
		echo "<p>\$id_mention:<br />";
		foreach($id_mention as $key => $value) {
		echo "\$id_mention[$key]=$value<br />";
		}
		echo "</p>
		<hr />";
		*/

		foreach($tab_mentions as $key => $value) {
			//echo "\$key=$key<br />";
			if((!in_array($key, $id_mention))&&(in_array($key, $tab_mentions_classe))&&(!in_array($key, $tab_mentions_aff))) {
				$sql="DELETE FROM j_mentions_classes WHERE id_classe='$id_classe[$i]' AND id_mention='$key';";
				//echo "$sql<br />";
				$nettoyage=mysql_query($sql);
				if(!$nettoyage) {
					$msg.="Erreur lors de la suppression de la $gepi_denom_mention <b>".$value."</b> pour la classe <b>".get_class_from_id($id_classe[$i])."</b>.<br />";
				}
				else {$cpt_reg++;}
			}
			elseif((in_array($key, $id_mention))&&(!in_array($key, $tab_mentions_classe))) {
				$sql="INSERT INTO j_mentions_classes SET id_classe='$id_classe[$i]', id_mention='$key';";
				//echo "$sql<br />";
				$insert=mysql_query($sql);
				if(!$insert) {
					$msg.="Erreur lors de l'association de la $gepi_denom_mention <b>".$value."</b> avec la classe <b>".get_class_from_id($id_classe[$i])."</b>.<br />";
				}
				else {$cpt_reg++;}
			}
		}
	}
	if(($msg=="")&&($cpt_reg>0)) {
		$msg.="Enregistrement effectu�.<br />";
		$enregistrement_ok="y";
	}
}

if(isset($saisie_ordre_mentions)) {
	check_token();

	$cpt_reg=0;
	$tab_mentions=get_mentions();

	for($i=0;$i<count($id_classe);$i++) {
		for($j=0;$j<count($ordre_id_mention);$j++) {
			$sql="UPDATE j_mentions_classes SET ordre='$j' WHERE id_classe='$id_classe[$i]' AND id_mention='$ordre_id_mention[$j]';";
			$update=mysql_query($sql);
			if(!$update) {
				$msg.="Erreur lors de l'enregistrement de l'ordre $j pour la $gepi_denom_mention <b>".$tab_mentions[$ordre_id_mention[$j]]."</b> pour la classe <b>".get_class_from_id($id_classe[$i])."</b>.<br />";
			}
			else {$cpt_reg++;}
		}
	}

	if(($msg=="")&&($cpt_reg>0)) {
		$msg.="Enregistrement effectu�.<br />";
		$enregistrement_ok="y";
	}
}

// Begin standart header
$titre_page = "Saisie de ".$gepi_denom_mention."s";
//====================================
// End standart header
require_once("../lib/header.inc");
if (!loadSettings()) {
	die("Erreur chargement settings");
}
//====================================

//debug_var();

function insere_mentions_par_defaut() {
	$cpt_erreur=0;
	$cpt_reg=0;
	$retour="";

	$tab_mentions=array('F�licitations', 'Mention honorable', 'Encouragements');
	for($i=0;$i<count($tab_mentions);$i++) {
		$sql="SELECT 1=1 FROM mentions WHERE mention='$tab_mentions[$i]';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$retour.="La $gepi_denom_mention '$tab_mentions[$i]' est d�j� enregistr�e.<br />\n";
		}
		else {
			$sql="INSERT INTO mentions SET mention='$tab_mentions[$i]';";
			$res=mysql_query($sql);
			if(!$res) {$cpt_erreur++;} else {$cpt_reg++;}
		}
	}

	if($cpt_erreur>0) {
		$retour.="$cpt_erreur erreur(s) lors de l'insertion des ".$gepi_denom_mention."s par d�faut.<br />\n";
	}

	if($cpt_reg>0) {
		$retour.="$cpt_reg $gepi_denom_mention(s) enregistr�e(s).<br />\n";
	}

	return $retour;
}

$sql="SHOW TABLES LIKE 'mentions';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$sql="CREATE TABLE IF NOT EXISTS mentions (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	mention VARCHAR(255) NOT NULL);";
	//echo "$sql<br />";
	$resultat_creation_table=mysql_query($sql);

	echo "<p style='color:red'>".insere_mentions_par_defaut()."</p>\n";
}

$sql="CREATE TABLE IF NOT EXISTS j_mentions_classes (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_mention INT(11) NOT NULL ,
id_classe INT(11) NOT NULL ,
ordre TINYINT(4) NOT NULL);";
$resultat_creation_table=mysql_query($sql);

echo "<p class='bold'><a href='../accueil.php'>Retour</a>";
if(!isset($associer_mentions_classes)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?associer_mentions_classes=y'>S�lectionner les ".$gepi_denom_mention."s associ�es aux classes</a>";
	echo "</p>\n";

	echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo add_token_field();

	echo "<p>Liste des ".$gepi_denom_mention."s d�finies&nbsp;:</p>";
	echo "<table class='boireaus' summary='Tableau des ".$gepi_denom_mention."s d�finies'>\n";
	echo "<tr>\n";
	echo "<th>".ucfirst($gepi_denom_mention)."</th>\n";
	echo "<th>Supprimer</th>\n";
	echo "</tr>\n";
	$tab_mentions=get_mentions();
	$tab_mentions_aff=get_tab_mentions_affectees();
	$alt=1;
	//for($i=0;$i<count($tab_mentions);$i++) {
	foreach($tab_mentions as $key => $value) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td><label for='suppr_$key'>$value</label></td>\n";
		echo "<td>";
		if(!in_array($key,$tab_mentions_aff)) {
			echo "<input type='checkbox' name='suppr[]' id='suppr_$key' value='$key' />";
		}
		else {
			echo "<img src='../images/disabled.png' width='20' height='20' alt='Suppression impossible: ".ucfirst($gepi_denom_mention)." donn�e � au moins un �l�ve.' title='Suppression impossible: ".ucfirst($gepi_denom_mention)." donn�e � au moins un �l�ve.' />";
		}
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo "<p>".ucfirst($gepi_denom_mention)." � ajouter&nbsp;: <input type='text' name='nouvelle_mention' value='' /></p>\n";
	echo "<input type='hidden' name='saisie_mention' value='y' /></p>\n";
	echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";

	echo "<p><i>NOTES</i>&nbsp;:</p>
<ul>
	<li>
		<p>L'intitul� <b>$gepi_denom_mention</b> peut �tre modifi� dans la page <a href='../gestion/param_gen.php#gepi_denom_mention'>Configuration g�n�rale</a></p>
	</li>
	<li>
		<p>Pour que le champ de saisie d'une $gepi_denom_mention n'apparaisse pas (<i>lors de la saisie de l'avis du conseil de classe</i>) pour une classe donn�e, il suffit qu'aucune $gepi_denom_mention ne soit associ�e � la classe.</p>
	</li>
	<li>
		<p>L'article Article R511-13 du code de l'�ducation rappelle que dans les lyc�es et coll�ges relevant du ministre charg� de l'�ducation, les sanctions qui peuvent �tre prononc�es � l'encontre des �l�ves sont les suivantes :</p>
		<ol>
			<li>L'avertissement</li>
			<li>Le bl�me ;</li>
			<li>L'exclusion temporaire, qui ne peut exc�der un mois, de l'�tablissement ou de l'un de ses services annexes ;</li>
			<li>L'exclusion d�finitive de l'�tablissement ou de l'un de ses services annexes.</li>
		</ol>
		<p>Les sanctions peuvent �tre assorties d'un sursis total ou partiel.<br />
		<b>Toute sanction, hormis l'exclusion d�finitive, est effac�e du dossier administratif de l'�l�ve au bout d'un an</b>
		Le r�glement int�rieur reproduit l'�chelle des sanctions. En outre, il peut pr�voir des mesures de pr�vention, d'accompagnement et de r�paration.<br />
		<a href='http://www.legifrance.gouv.fr/affichCodeArticle.do?cidTexte=LEGITEXT000006071191&idArticle=LEGIARTI000020663068&dateTexte=&categorieLien=cid'>Le lien sur L�gifrance</a>.</p>
	</li>
</ul>\n";

	echo "<p style='color:red'>Pour ne pas poser de probl�mes sur les bulletins PDF, il est recommand� pour le moment (<i>� am�liorer</i>)&nbsp;:</p>
<ul style='color:red'>
<li>de ne pas d�passer 18 caract�res dans une $gepi_denom_mention</li>
<li>de ne pas d�finir plus de 8 ".$gepi_denom_mention."s diff�rentes pour une m�me classe</li>
</ul>\n";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Saisir des ".$gepi_denom_mention."s</a>";

	if(!isset($id_classe)) {
		echo "</p>\n";

		echo "<p>Pour quelle(s) classe(s) souhaitez-vous choisir les ".$gepi_denom_mention."s&nbsp;?</p>\n";

		$sql="select distinct id,classe from classes order by classe";
		$classes_list=mysql_query($sql);
		$nb=mysql_num_rows($classes_list);
		if($nb==0){
			echo "<p>Aucune classe n'est encore d�finie...</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
		echo add_token_field();

		$nb_class_par_colonne=round($nb/3);
		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i=0;
		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while ($i < $nb) {
			$id_classe=mysql_result($classes_list, $i, 'id');
			//$temp = "id_classe_".$id_classe;
			$classe=mysql_result($classes_list, $i, 'classe');

			if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
				echo "</td>\n";
				//echo "<td style='padding: 0 10px 0 10px'>\n";
				echo "<td align='left'>\n";
			}

			echo "<input type='checkbox' name='id_classe[]' id='id_classe_$i' value='$id_classe' ";
			echo "onchange=\"checkbox_change($i)\" ";
			echo "/><label for='id_classe_$i'><span id='texte_id_classe_$i'>Classe : ".$classe.".</span></label><br />\n";
			$i++;
		}

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		//echo "<input type='hidden' name='is_posted' value='2' />\n";

		echo "<input type='hidden' name='associer_mentions_classes' value='y' />\n";
		echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";

		echo "<p><a href=\"javascript:cocher_toutes_classes('cocher')\">Cocher</a> / <a href=\"javascript:cocher_toutes_classes('decocher')\">d�cocher</a> toutes les classes.</p>\n";

		echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('id_classe_'+cpt)) {
		if(document.getElementById('id_classe_'+cpt).checked) {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='normal';
		}
	}
}

function cocher_toutes_classes(mode) {
	if(mode=='cocher') {
		for(i=0;i<$nb;i++) {
			if(document.getElementById('id_classe_'+i)) {
				//alert('i='+i);
				document.getElementById('id_classe_'+i).checked=true;
				checkbox_change(i);
			}
		}
	}
	else {
		for(i=0;i<$nb;i++) {
			if(document.getElementById('id_classe_'+i)) {
				document.getElementById('id_classe_'+i).checked=false;
				checkbox_change(i);
			}
		}
	}
}
</script>\n";

	}
	elseif(!isset($enregistrement_ok)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?associer_mentions_classes=y'>Choisir d'autres classes</a>";
		echo "</p>\n";

		echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();

		$sql="SELECT DISTINCT id_mention FROM j_mentions_classes WHERE (";
		echo "<p>Choisir les ".$gepi_denom_mention."s pour la ou les classes&nbsp;: ";
		for($i=0;$i<count($id_classe);$i++) {
			if($i>0) {
				echo ", ";
				$sql.=" OR ";
			}
			echo get_class_from_id($id_classe[$i]);
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			//echo " ($id_classe[$i])";
			$sql.="id_classe='$id_classe[$i]'";
		}
		$sql.=")";
		echo "<br />\n";

		$tab_mentions=get_mentions();
		$tab_mentions_aff=get_tab_mentions_affectees();

		$tab_mentions_classes=array();
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {
				$tab_mentions_classes[]=$lig->id_mention;
			}
		}

		echo "parmi les ".$gepi_denom_mention."s suivantes&nbsp;:</p>";
		echo "<table class='boireaus' summary='Tableau des ".$gepi_denom_mention."s'>\n";
		echo "<tr>\n";
		echo "<th>Cocher</th>\n";
		echo "<th>".ucfirst($gepi_denom_mention)."</th>\n";
		echo "<th>Classes d�j� associ�es</th>\n";
		echo "</tr>\n";
		$alt=1;
		//for($i=0;$i<count($tab_mentions);$i++) {
		foreach($tab_mentions as $key => $value) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>";

			$chaine_classes="";
			$sql="SELECT DISTINCT c.classe FROM classes c, j_mentions_classes j WHERE j.id_classe=c.id AND j.id_mention='$key' ORDER BY c.classe;";
			//echo "$sql<br />";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$cpt_classe=0;
				while($lig=mysql_fetch_object($res)) {
					if($cpt_classe>0) {$chaine_classes.=", ";}
					$chaine_classes.=$lig->classe;
					$cpt_classe++;
				}
			}

			echo "<input type='checkbox' name='id_mention[]' id='id_mention_$key'value='$key' ";
			//if($chaine_classes!="") {echo "checked ";}
			if(in_array($key, $tab_mentions_classes)) {echo "checked ";}
			echo "/>";
			echo "</td>\n";

			echo "<td><label for='id_mention_$key'>$value</label></td>\n";

			echo "<td>";
			echo $chaine_classes;
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";

		echo "<input type='hidden' name='associer_mentions_classes' value='y' />\n";
		echo "<input type='hidden' name='saisie_association_mentions_classes' value='y' />\n";
		echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
		echo "</form>\n";
	}
	else {
		echo " | <a href='".$_SERVER['PHP_SELF']."?associer_mentions_classes=y'>Choisir d'autres classes</a>";
		echo "</p>\n";

		// Ordre des mentions

		echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();

		$sql="SELECT DISTINCT id_mention FROM j_mentions_classes WHERE (";

		echo "<p>Choisir l'ordre des ".$gepi_denom_mention."s pour la ou les classes&nbsp;: ";
		for($i=0;$i<count($id_classe);$i++) {
			if($i>0) {
				echo ", ";
				$sql.=" OR ";
			}
			echo get_class_from_id($id_classe[$i]);
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			$sql.="id_classe='$id_classe[$i]'";
		}
		echo ".</p>\n";
		$sql.=") ORDER BY ordre, id_mention;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p style='color:red'>Aucune $gepi_denom_mention n'est associ�e � ces classes.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$tab_mentions=get_mentions();

		echo "<p>Les ".$gepi_denom_mention."s sont&nbsp;:</p>\n";
		echo "<ul>\n";
		$tab_mentions_classes=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_mentions_classes[]=$lig->id_mention;
			echo "<li>".$tab_mentions[$lig->id_mention];
			echo "<input type='hidden' name='id_mention[]' value='$lig->id_mention' />\n";
			echo "</li>\n";
		}
		echo "</ul>\n";

		echo "<p>Ordre choisi&nbsp;:</p>\n";
		echo "<ol>\n";
		for($i=0;$i<count($tab_mentions_classes);$i++) {
			echo "<li>\n";
			echo "<select name='ordre_id_mention[]'>\n";
			for($j=0;$j<count($tab_mentions_classes);$j++) {
				echo "<option value='".$tab_mentions_classes[$j]."'";
				if($j==$i) {echo " selected";}
				echo ">".$tab_mentions[$tab_mentions_classes[$j]]."</option>\n";
			}
			echo "</select>\n";
			echo "</li>\n";
		}
		echo "</ol>\n";

		echo "<input type='hidden' name='associer_mentions_classes' value='y' />\n";
		echo "<input type='hidden' name='saisie_ordre_mentions' value='y' />\n";
		echo "<p><input type='submit' name='valider' value='Valider' /></p>\n";
		echo "</form>\n";
	}
}

require("../lib/footer.inc.php");
die();
?>