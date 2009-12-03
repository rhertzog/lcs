<?php
/*
 * $Id : $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
    die();};

// INSERT INTO droits VALUES ('/mod_annees_anterieures/nettoyer_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression de donn�es ant�rieures', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


// Si le module n'est pas activ�...
if(getSettingValue('active_annees_anterieures')!="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'acc�s illicite?

	header("Location: ../logout.php?auto=1");
	die();
}

$confirmer=isset($_POST['confirmer']) ? $_POST['confirmer'] : NULL;
$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : NULL;

$msg="";
if(isset($confirmer)){
	$nb_suppr=0;
	$nb_err=0;
	for($i=0;$i<count($suppr);$i++){
    $sql="DELETE FROM archivage_eleves WHERE ine='$suppr[$i]';";
		$res_suppr1=mysql_query($sql);

		$sql="DELETE FROM archivage_eleves2 WHERE ine='$suppr[$i]';";
		$res_suppr2=mysql_query($sql);
		$sql="DELETE FROM archivage_aid_eleve WHERE id_eleve='$suppr[$i]';";
		$res_suppr3=mysql_query($sql);
		$sql="DELETE FROM archivage_appreciations_aid WHERE id_eleve='$suppr[$i]';";
		$res_suppr4=mysql_query($sql);
		$sql="DELETE FROM archivage_disciplines WHERE INE='$suppr[$i]';";
		$res_suppr5=mysql_query($sql);
		$sql="DELETE FROM archivage_ects WHERE INE='$suppr[$i]';";
		$res_suppr6=mysql_query($sql);
		if (($res_suppr1) and ($res_suppr2) and ($res_suppr3) and ($res_suppr4)  and ($res_suppr5) and ($res_suppr6)) {
			$nb_suppr++;
		}
		else{
			$nb_err++;
		}
	}
	if($nb_suppr>0){
		if($nb_suppr==1){$s="";}else{$s="s";}
		$msg.="Les donn�es ant�rieures de $nb_suppr ancien$s �l�ve$s ont �t� supprim�es.";
	}
	if($nb_err>0){
		if($nb_err==1){$s="";}else{$s="s";}
		if($msg!=""){$msg.="<br />";}
		$msg.="Pour $nb_err ancien$s �l�ve$s, des probl�mes ont �t� rencontr�s.";
	}
}

$style_specifique="mod_annees_anterieures/annees_anterieures";

$themessage="Des modifications ont �t� effectu�es. Voulez-vous vraiment quitter sans enregistrer?";

//**************** EN-TETE *****************
$titre_page = "Nettoyage des donn�es ant�rieures";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<div class='norme'><p class=bold><a href='";
echo "index.php";
echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

echo "</div>\n";


$sql="SELECT DISTINCT a.nom,a.prenom,a.ine,a.naissance
			FROM archivage_eleves a
			LEFT JOIN eleves e
			ON a.ine=e.no_gep
			WHERE e.no_gep IS NULL;";
$res1=mysql_query($sql);
$nb_ele=mysql_num_rows($res1);
if($nb_ele==0){
	echo "<p>Tous les �l�ves pr�sents dans la table 'annees_anterieures' sont dans la table 'eleves'.</p>\n";
}
else{
	echo "<p>Voici la liste des �l�ves pr�sents dans la table 'archivage_eleves', mais absents de la table 'eleves'.<br />
	Il s'agit normalement d'�l�ves ayant quitt� l'�tablissement.<br />
	Il peut cependant arriver que des �l�ves dont le num�ro INE n'�tait pas (<i>correctement</i>) rempli lors de la conservation de l'ann�e soit propos�s dans la liste ci-dessous.<br />
	Dans ce cas, le num�ro INE utilis� a un pr�fixe LOGIN_.<br />
	Ce n'est pas un identifiant correct parce que le login d'un �l�ve n'est pas n�cessairement fixe d'une ann�e sur l'autre (<i>dans le cas des doublons</i>).<br />
	Vous pouvez �galement choisir de <a href='corriger_ine.php'>corriger des INE non renseign�s ou mal renseign�s</a></p>\n";

	echo "<form name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

	echo "<table align='center' class='table_annee_anterieure' summary='Tableau des �l�ves'>\n";
	echo "<tr style='background-color:white;'>\n";
	echo "<th>Supprimer<br />";
	echo "<a href='javascript:modif_coche(true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
	echo "<a href='javascript:modif_coche(false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout d�cocher' /></a>\n";
	echo "</th>\n";
	echo "<th>El�ve</th>\n";
	echo "<th>Date de naissance</th>\n";
	echo "<th>N�INE</th>\n";
	echo "</tr>\n";
	$cpt=0;
	while($lig_ele=mysql_fetch_object($res1)){
		echo "<tr style='text-align:center;' id='tr_$cpt'>\n";
		echo "<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value='$lig_ele->ine' onchange=\"modif_une_coche('$cpt');\" /></td>\n";
		echo "<td>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</td>\n";
		echo "<td>".formate_date($lig_ele->naissance)."</td>\n";
		echo "<td>";
		if(substr($lig_ele->ine,0,6)=="LOGIN_") {echo "<span style='color:red;'>";}
		echo $lig_ele->ine;
		if(substr($lig_ele->ine,0,6)=="LOGIN_"){echo "</span>";}
		echo "</td>\n";
		echo "</tr>\n";
		$cpt++;
	}
	echo "</table>\n";

	echo "<p align='center'><input type='submit' name='confirmer' value='Supprimer' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript' language='javascript'>
	function modif_coche(statut){
		// statut: true ou false
		for(k=0;k<$cpt;k++){
			if(document.getElementById('suppr_'+k)){
				document.getElementById('suppr_'+k).checked=statut;

				if(statut==true){
					document.getElementById('tr_'+k).style.backgroundColor='orange';
				}
				else{
					document.getElementById('tr_'+k).style.backgroundColor='';
				}
			}
		}
		changement();
	}

	function modif_une_coche(ligne){
		statut=document.getElementById('suppr_'+ligne).checked;

		if(statut==true){
			document.getElementById('tr_'+ligne).style.backgroundColor='orange';
		}
		else{
			document.getElementById('tr_'+ligne).style.backgroundColor='';
		}
		changement();
	}
</script>\n";

}

require("../lib/footer.inc.php");
?>
