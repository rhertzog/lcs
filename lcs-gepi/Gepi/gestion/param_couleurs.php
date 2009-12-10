<?php
/*
 * $Id: param_couleurs.php 3442 2009-09-21 13:39:18Z crob $
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

// Initialisation des feuilles de style apr�s modification pour am�liorer l'accessibilit�
$accessibilite="y";

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

// INSERT INTO droits VALUES ('/gestion/param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'D�finition des couleurs pour Gepi', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


// Liste des composantes
$comp=array('R','V','B');



function hex2nb($carac) {
	switch(strtoupper($carac)) {
		case "A":
			return 10;
			break;
		case "B":
			return 11;
			break;
		case "C":
			return 12;
			break;
		case "D":
			return 13;
			break;
		case "E":
			return 14;
			break;
		case "F":
			return 15;
			break;
		default:
			return $carac;
			break;
	}
}

function tab_rvb($couleur) {
	$compR=substr($couleur,0,2);
	$compV=substr($couleur,2,2);
	$compB=substr($couleur,4,2);

	//echo "\$compR=$compR<br />";
	//echo "\$compV=$compV<br />";
	//echo "\$compB=$compB<br />";

	$tabcomp=array();

	$tabcomp['R']=hex2nb(substr($compR,0,1))*16+hex2nb(substr($compR,1,1));
	$tabcomp['V']=hex2nb(substr($compV,0,1))*16+hex2nb(substr($compV,1,1));
	$tabcomp['B']=hex2nb(substr($compB,0,1))*16+hex2nb(substr($compB,1,1));

	return $tabcomp;
}



function genere_degrade($couleur_haut,$couleur_bas,$hauteur,$chemin_img) {
	//$hauteur=100;

	$im=imagecreate(1,$hauteur);

	$comp=array('R','V','B');

	$tab_haut=array();
	$tab_haut=tab_rvb($couleur_haut);

	$tab_bas=array();
	$tab_bas=tab_rvb($couleur_bas);

	for($x=0;$x<$hauteur;$x++) {
		$ratio=array();
		for($i=0;$i<count($comp);$i++) {
			$ratio[$comp[$i]]=$tab_haut[$comp[$i]]+$x*($tab_bas[$comp[$i]]-$tab_haut[$comp[$i]])/$hauteur;
		}
		$color=imagecolorallocate($im,$ratio['R'],$ratio['V'],$ratio['B']);
		imagesetpixel($im,0,$x,$color);
	}
	imagepng($im,$chemin_img);
}



// Liste des couleurs,... param�trables
$tab=array();
$tab[0]='style_body_backgroundcolor';
// NOTE: Pour JavaScript, on n'a pas le droit au '-' dans un nom de variable



//if(isset($_POST['ok'])) {
if(isset($_POST['is_posted'])) {
	$err_no=0;
	$msg="";

	//if(isset($_POST['style_body_backgroundcolor']))�{

	$reinitialiser="n";
	if(isset($_POST['secu'])) {
		if($_POST['secu']=='y') {
			$reinitialiser='y';
		}
	}

	if($reinitialiser=='y') {
		if(saveSetting('style_body_backgroundcolor','')) {
			if ($GLOBALS['multisite'] == 'y') {
				$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
			} else {
				$fich=fopen("../style_screen_ajout.css","w+");
			}
			fwrite($fich,"/*
Ce fichier est destin� � recevoir des param�tres d�finis depuis la page /gestion/param_couleurs.php
Charg� juste avant la section <body> dans le /lib/header.inc,
ses propri�t�s �crasent les propri�t�s d�finies auparavant dans le </head>.
*/
");
			fclose($fich);
			$msg.="R�initialisation effectu�e.";
		}
		else {
			$msg.="Erreur lors de la r�initialisation.";
		}
	}
	else {
		$temoin_modif=0;
		$temoin_fichier_regenere=0;
		$nb_err=0;

		if(isset($_POST['utiliser_couleurs_perso'])) {
			if(!saveSetting('utiliser_couleurs_perso','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso'. ";
				$nb_err++;
			}

			if(isset($_POST['style_body_backgroundcolor'])) {
				if(saveSetting('style_body_backgroundcolor',$_POST['style_body_backgroundcolor'])) {
					if ($GLOBALS['multisite'] == 'y') {
						$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
					} else {
						$fich=fopen("../style_screen_ajout.css","w+");
					}
					fwrite($fich,"/*
Ce fichier est destin� � recevoir des param�tres d�finis depuis la page /gestion/param_couleurs.php
Charg� juste avant la section <body> dans le /lib/header.inc,
ses propri�t�s �crasent les propri�t�s d�finies auparavant dans le </head>.
*/

@media screen  {
	body {
		background: #".$_POST['style_body_backgroundcolor'].";
	}
}
");
					fclose($fich);
					//$msg.="Enregistrement effectu�. ";
					$temoin_modif++;
					$temoin_fichier_regenere++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'style_body_backgroundcolor'. ";
					$nb_err++;
				}
			}
		}
		else {
			if(!saveSetting('utiliser_couleurs_perso','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso'. ";
				$nb_err++;
			}

			if ($GLOBALS['multisite'] == 'y') {
				$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
			} else {
				$fich=fopen("../style_screen_ajout.css","w+");
			}
			fwrite($fich,"/*
Ce fichier est destin� � recevoir des param�tres d�finis depuis la page /gestion/param_couleurs.php
Charg� juste avant la section <body> dans le /lib/header.inc,
ses propri�t�s �crasent les propri�t�s d�finies auparavant dans le </head>.
*/
");
			fclose($fich);
			//$msg.="Enregistrement effectu�. ";
			$temoin_modif++;
			$temoin_fichier_regenere++;
		}

		if(isset($_POST['utiliser_degrade'])) {
			if(!saveSetting('utiliser_degrade','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_degrade'. ";
				$nb_err++;
			}

			if(isset($_POST['degrade_haut'])) {
				if((strlen(my_ereg_replace("[0-9A-F]","",strtoupper($_POST['degrade_haut'])))!=0)||(strlen($_POST['degrade_haut'])!=6)) {
					$degrade_haut="020202";
				}
				else {
					$degrade_haut=$_POST['degrade_haut'];
				}

				if(saveSetting('degrade_haut',$degrade_haut)) {
					//$msg.="Enregistrement effectu�. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'degrade_haut'. ";
					$nb_err++;
				}
			}

			if(isset($_POST['degrade_bas'])) {
				if((strlen(my_ereg_replace("[0-9A-F]","",strtoupper($_POST['degrade_bas'])))!=0)||(strlen($_POST['degrade_bas'])!=6)) {
					$degrade_bas="4A4A59";
				}
				else {
					$degrade_bas=$_POST['degrade_bas'];
				}

				if(saveSetting('degrade_bas',$_POST['degrade_bas'])) {
					//$msg.="Enregistrement effectu�. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'degrade_bas'. ";
					$nb_err++;
				}
			}

			if($nb_err==0) {
				/*
				if($temoin_fichier_regenere==0) {
				}
				else {
				}
				*/


				// G�n�rer l'image...

				genere_degrade($degrade_haut,$degrade_bas,100,"../images/background/degrade1.png");
				genere_degrade($degrade_haut,$degrade_bas,40,"../images/background/degrade1_small.png");


				if ($GLOBALS['multisite'] == 'y') {
					$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
				} else {
					$fich=fopen("../style_screen_ajout.css","a+");
				}

				fwrite($fich,"

div#header {
	background-color: #$degrade_bas;
}

fieldset#login_box div#header {
	background-image: url(\"./images/background/degrade1_small.png\");
}

#table_header�{
	background-image: url(\"./images/background/degrade1.png\");
}

#div_login_entete {
	background: #$degrade_bas;
	background-image: url(\"./images/background/degrade1.png\");
}

#essaiMenu {
	background: #$degrade_bas;
}

/* Bandeau */
.degrade1 {
	background-color:#$degrade_bas;
}
");

/*
//Pour old-style... probl�me: Si on le met cela s'applique m�me en new-style.

#td_headerTopRight {
	background-color: #$degrade_haut;
}

#td_headerBottomRight{
	background-color: #$degrade_haut;
}
*/

				fclose($fich);
			}
		}
		else {
			if(file_exists("../images/background/degrade1.png")) {unlink("../images/background/degrade1.png");}
			if(file_exists("../images/background/degrade1_small.png")) {unlink("../images/background/degrade1_small.png");}
			if(!saveSetting('utiliser_degrade','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_degrade'. ";
				$nb_err++;
			}
		}





		if(isset($_POST['utiliser_couleurs_perso_infobulles'])) {
			if(!saveSetting('utiliser_couleurs_perso_infobulles','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso_infobulles'. ";
				$nb_err++;
			}

			//couleur_infobulle_fond_corps
			//couleur_infobulle_fond_entete

			if(isset($_POST['couleur_infobulle_fond_entete'])) {
				if((strlen(my_ereg_replace("[0-9A-F]","",strtoupper($_POST['couleur_infobulle_fond_entete'])))!=0)||(strlen($_POST['couleur_infobulle_fond_entete'])!=6)) {
					$couleur_infobulle_fond_entete="4a4a59";
				}
				else {
					$couleur_infobulle_fond_entete=$_POST['couleur_infobulle_fond_entete'];
				}

				if(saveSetting('couleur_infobulle_fond_entete',$couleur_infobulle_fond_entete)) {
					//$msg.="Enregistrement effectu�. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'couleur_infobulle_fond_entete'. ";
					$nb_err++;
				}
			}

			if(isset($_POST['couleur_infobulle_fond_corps'])) {
				if((strlen(my_ereg_replace("[0-9A-F]","",strtoupper($_POST['couleur_infobulle_fond_corps'])))!=0)||(strlen($_POST['couleur_infobulle_fond_corps'])!=6)) {
					$couleur_infobulle_fond_corps="EAEAEA";
				}
				else {
					$couleur_infobulle_fond_corps=$_POST['couleur_infobulle_fond_corps'];
				}

				if(saveSetting('couleur_infobulle_fond_corps',$couleur_infobulle_fond_corps)) {
					//$msg.="Enregistrement effectu�. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'couleur_infobulle_fond_corps'. ";
					$nb_err++;
				}
			}

			if($nb_err==0) {
				if ($GLOBALS['multisite'] == 'y') {
					$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
				} else {
					$fich=fopen("../style_screen_ajout.css","a+");
				}
				fwrite($fich,"
.infobulle_entete {
	background-color: #$couleur_infobulle_fond_entete;
}
.infobulle_corps {
	background-color: #$couleur_infobulle_fond_corps;
}
");
				fclose($fich);
			}

		}
		else {
			if(!saveSetting('utiliser_couleurs_perso_infobulles','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso_infobulles'. ";
				$nb_err++;
			}
		}




		//=========================================
		if(isset($_POST['utiliser_couleurs_perso_lig_tab_alt'])) {
			if(!saveSetting('utiliser_couleurs_perso_lig_tab_alt','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso_lig_tab_alt'. ";
				$nb_err++;
			}

			if(isset($_POST['couleur_lig_alt1'])) {
				if((strlen(my_ereg_replace("[0-9A-F]","",strtoupper($_POST['couleur_lig_alt1'])))!=0)||(strlen($_POST['couleur_lig_alt1'])!=6)) {
					$couleur_lig_alt1="ffefd5";
				}
				else {
					$couleur_lig_alt1=$_POST['couleur_lig_alt1'];
				}

				if(saveSetting('couleur_lig_alt1',$couleur_lig_alt1)) {
					//$msg.="Enregistrement effectu�. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'couleur_lig_alt1'. ";
					$nb_err++;
				}
			}

			if(isset($_POST['couleur_lig_alt_1'])) {
				if((strlen(my_ereg_replace("[0-9A-F]","",strtoupper($_POST['couleur_lig_alt_1'])))!=0)||(strlen($_POST['couleur_lig_alt_1'])!=6)) {
					$couleur_lig_alt_1="F0FFF0";
				}
				else {
					$couleur_lig_alt_1=$_POST['couleur_lig_alt_1'];
				}

				if(saveSetting('couleur_lig_alt_1',$couleur_lig_alt_1)) {
					//$msg.="Enregistrement effectu�. ";
					$temoin_modif++;
				}
				else {
					$msg.="Erreur lors de la sauvegarde de 'couleur_lig_alt_1'. ";
					$nb_err++;
				}
			}

			if($nb_err==0) {
				if ($GLOBALS['multisite'] == 'y') {
					$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
				} else {
					$fich=fopen("../style_screen_ajout.css","a+");
				}
				fwrite($fich,"
.gestion_temp_dir .lig-1 {
	background-color: #$couleur_lig_alt_1;
}
.gestion_temp_dir .lig1 {
	background-color: #$couleur_lig_alt1;
}

.boireaus .lig-1 {
	background-color: #$couleur_lig_alt_1;
}
.boireaus .lig1 {
	background-color: #$couleur_lig_alt1;
}
");
				fclose($fich);
			}

		}
		else {
			if(!saveSetting('utiliser_couleurs_perso_lig_tab_alt','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_couleurs_perso_lig_tab_alt'. ";
				$nb_err++;
			}
		}





		//=========================================
		// utiliser_cahier_texte_perso
		//=========================================

		$poste_notice_nom=array("fond_notices_c", "entete_fond_c", "cellule_c", "cellule_alt_c", "fond_notices_t", "entete_fond_t", "cellule_t", "cellule_alt_t", "fond_notices_i", "entete_fond_i", "cellule_i", "cellule_alt", "fond_notices_f", "cellule_f", "police_travaux", "police_matieres", "bord_tableau_notice", "cellule_gen");
		$poste_notice_couleur=array("C7FF99", "C7FF99", "E5FFCF", "D3FFAF", "FFCCCF", "FFCCCF", "FFEFF0", "FFDFE2", "ACACFF", "ACACFF", "EFEFFF", "C8C8FF", "FFFF80", "FFFFDF", "FF4444", "green", "6F6968", "F6F7EF");
		$poste_notice_classe=array("color_fond_notices_c", "couleur_entete_fond_c", "couleur_cellule_c", "couleur_cellule_alt_c", "color_fond_notices_t", "couleur_entete_fond_t", "couleur_cellule_t", "couleur_cellule_alt_t", "color_fond_notices_i", "couleur_entete_fond_i", "couleur_cellule_i", "couleur_cellule_alt_i", "color_fond_notices_f", "couleur_cellule_f", "color_police_travaux", "color_police_matieres ", "couleur_bord_tableau_notice", "couleur_cellule_gen");
		$poste_type_couleur=array("background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "background-color", "color", "color", "border-color", "background-color");

		if(isset($_POST['utiliser_cahier_texte_perso'])) {
			if(!saveSetting('utiliser_cahier_texte_perso','y')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_cahier_texte_perso'. ";
				$nb_err++;
			}

			if($nb_err==0) {
				if ($GLOBALS['multisite'] == 'y') {
					$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
				} else {
					$fich=fopen("../style_screen_ajout.css","a+");
				}
					fwrite($fich,"
/* Classes des notices du cahier de texte */
");
				fclose($fich);
			}

			for($i=0;$i<count($poste_notice_nom);$i++) {
				if(isset($_POST[$poste_notice_nom[$i]])) {
					if((strlen(my_ereg_replace("[0-9A-F]","",strtoupper($_POST[$poste_notice_nom[$i]])))!=0)||(strlen($_POST[$poste_notice_nom[$i]])!=6)) {
						$couleur_poste=$poste_notice_couleur[$i];
					}
					else {
						$couleur_poste=$_POST[$poste_notice_nom[$i]];
					}

					if(saveSetting($poste_notice_nom[$i],$couleur_poste)) {
						$temoin_modif++;
					}
					else {
						$msg.="Erreur lors de la sauvegarde de '".$poste_notice_nom[$i]."'. ";
						$nb_err++;
					}
				}

				if($nb_err==0) {
					if ($GLOBALS['multisite'] == 'y') {
						$fich=fopen("../style_screen_ajout_".getSettingValue('gepiSchoolRne').".css","w+");
					} else {
						$fich=fopen("../style_screen_ajout.css","a+");
					}
						fwrite($fich,"
.".$poste_notice_classe[$i]." {
	".$poste_type_couleur[$i].": #".$couleur_poste.";
}
	");
					fclose($fich);
				}

			}
		}
		else {
			if(!saveSetting('utiliser_cahier_texte_perso','n')) {
				$msg.="Erreur lors de la sauvegarde de 'utiliser_cahier_texte_perso'. ";
				$nb_err++;
			}
			else {
				for($i=0;$i<count($poste_notice_nom);$i++) {
					if(saveSetting($poste_notice_nom[$i],$poste_notice_couleur[$i])) {
						$temoin_modif++;
					}
					else {
						$msg.="Erreur lors de la sauvegarde de '".$poste_notice_nom[$i]."'. ";
						$nb_err++;
					}
				}
			}

		}


		//=========================================

/*
//$temoin_fichier_regenere
				if($temoin_modif==0) {
				}
				else {
				}
				fclose($fich);
*/
	}
}


//**************** EN-TETE *****************
$titre_page = "Choix des couleurs GEPI";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

include("../lib/couleurs_ccm.php");

//echo "<div class='norme'><p class='bold'><a href='param_gen.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
echo "<div class='norme'>\n";
	echo "<p class='bold'>\n";
		echo "<a href='index.php'>\n";
			echo "<img src='../images/icons/back.png' alt='' class='back_link'/>\n";
			echo " Retour\n";
		echo "</a>\n";

	echo " | Choix mod�le&nbsp;: <select name='choix_modele' id='choix_modele' onchange=\"valide_modele(document.getElementById('choix_modele').options[document.getElementById('choix_modele').selectedIndex].value)\">
	<option value=''>---</option>
	<option value='rose'>Rose</option>
	<option value='vert'>Vert</option>
	<option value='bleu'>Bleu</option>
	<option value='chocolat'>Chocolat</option>
</select>\n";

	echo "</p>\n";
echo "</div>\n";


/*
foreach($_POST as $post => $val) {
	echo $post.' : '.$val."<br />\n";
}
*/

/*
echo "<div id='div_tmp'>";
aff_tab_couleurs_ccm('div_tmp','id_style_body_backgroundcolor_R','id_style_body_backgroundcolor_V','id_style_body_backgroundcolor_B','style_body_backgroundcolor');
echo "</div>";
*/
aff_tab_couleurs_ccm('div_choix_couleur');

?>


<script type='text/javascript'>
// <![CDATA[



	var hexa=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");

	/*
	function affichecouleur(motif) {
		compR=eval("document.forms['tab']."+motif+"_R.value");
		compV=eval("document.forms['tab']."+motif+"_V.value");
		compB=eval("document.forms['tab']."+motif+"_B.value");

		hex1=Math.floor(compR/16);
		hex2=compR-hex1*16;
		couleur=hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compV/16);
		hex2=compV-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compB/16);
		hex2=compB-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		//alert(couleur);

		document.getElementById(motif).style.backgroundColor="#"+couleur;
	}
	*/


	function calculecouleur(motif) {
		compR=eval("document.forms['tab']."+motif+"_R.value");
		compV=eval("document.forms['tab']."+motif+"_V.value");
		compB=eval("document.forms['tab']."+motif+"_B.value");

		hex1=Math.floor(compR/16);
		hex2=compR-hex1*16;
		couleur=hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compV/16);
		hex2=compV-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		hex1=Math.floor(compB/16);
		hex2=compB-hex1*16;
		couleur=couleur+""+hexa[hex1]+""+hexa[hex2];

		return couleur;
	}

	function affichecouleur(motif) {
		document.getElementById(motif).style.backgroundColor="#"+calculecouleur(motif);
	}

	function delai_affichecouleur(motif) {
		//alert('motif='+motif);
		setTimeout("affichecouleur("+motif+")",1000);
	}


	//var liste=new Array('style_body_backgroundcolor');
	// var liste=new Array('style_body_backgroundcolor', 'degrade_haut', 'degrade_bas', 'couleur_infobulle_fond_corps', 'couleur_infobulle_fond_entete', 'couleur_lig_alt1', 'couleur_lig_alt_1');
	var liste=new Array('style_body_backgroundcolor', 'degrade_haut', 'degrade_bas', 'couleur_infobulle_fond_corps', 'couleur_infobulle_fond_entete', 'couleur_lig_alt1', 'couleur_lig_alt_1', 'police_travaux', 'police_matieres', 'bord_tableau_notice', 'cellule_gen', 'fond_notices_c', 'fond_notices_t', 'fond_notices_i', 'fond_notices_f', 'entete_fond_c', 'entete_fond_t', 'entete_fond_i', 'cellule_c', 'cellule_t', 'cellule_i', 'cellule_f', 'cellule_alt_c', 'cellule_alt_t', 'cellule_alt_i');

	function init() {
		for(i=0;i<liste.length;i++) {
			eval("affichecouleur('"+liste[i]+"')");
		}
	}

	function calcule_et_valide() {
		for(i=0;i<liste.length;i++) {
			champ=eval("document.forms['tab']."+liste[i])
			champ.value=calculecouleur(liste[i]);
		}
		document.forms['tab'].submit();
	}

	//function reinitialiser() {
	function reinit() {
		document.forms['tab'].secu.value='y';
		document.forms['tab'].submit();
	}



	var tabmotif=new Array();
	// #EAEAEA
	// 14*16+10
	tabmotif['style_body_backgroundcolor_R']="234";
	tabmotif['style_body_backgroundcolor_V']="234";
	tabmotif['style_body_backgroundcolor_B']="234";
	//020202
	tabmotif['degrade_haut_R']="2";
	tabmotif['degrade_haut_V']="2";
	tabmotif['degrade_haut_B']="2";
	//4A4A59
	// 4*16+10 et 5*16+9
	tabmotif['degrade_bas_R']="74";
	tabmotif['degrade_bas_V']="74";
	tabmotif['degrade_bas_B']="89";

	// papayawhip #FFEFD5
	tabmotif['couleur_lig_alt1_R']=255;
	tabmotif['couleur_lig_alt1_V']=239;
	tabmotif['couleur_lig_alt1_B']=213;

	// honeydew #F0FFF0
	tabmotif['couleur_lig_alt_1_R']=240;
	tabmotif['couleur_lig_alt_1_V']=255;
	tabmotif['couleur_lig_alt_1_B']=240;

	// Cahier de texte : Compte rendu
	// #C7FF99
	tabmotif['fond_notices_c_R']=199;
	tabmotif['fond_notices_c_V']=255;
	tabmotif['fond_notices_c_B']=153;
	tabmotif['entete_fond_c_R']=199;
	tabmotif['entete_fond_c_V']=255;
	tabmotif['entete_fond_c_B']=153;
	// #E5FFCF
	tabmotif['cellule_c_R']=229;
	tabmotif['cellule_c_V']=255;
	tabmotif['cellule_c_B']=207;
	// #D3FFAF
	tabmotif['cellule_alt_c_R']=211;
	tabmotif['cellule_alt_c_V']=255;
	tabmotif['cellule_alt_c_B']=175;

	// Cahier de texte : Travail � faire
	//	#FFCCCF
	tabmotif['fond_notices_t_R']=255;
	tabmotif['fond_notices_t_V']=204;
	tabmotif['fond_notices_t_B']=207;
	tabmotif['entete_fond_t_R']=255;
	tabmotif['entete_fond_t_V']=204;
	tabmotif['entete_fond_t_B']=207;
	// #FFEFF0
	tabmotif['cellule_t_R']=255;
	tabmotif['cellule_t_V']=239;
	tabmotif['cellule_t_B']=240;
	// #FFDFE2
	tabmotif['cellule_alt_t_R']=255;
	tabmotif['cellule_alt_t_V']=223;
	tabmotif['cellule_alt_t_B']=226;

	// Cahier de texte : Informations g�n�rales
	//	#ACACFF
	tabmotif['fond_notices_i_R']=172;
	tabmotif['fond_notices_i_V']=172;
	tabmotif['fond_notices_i_B']=255;
	// #EFEFFF
	tabmotif['entete_fond_i_R']=172;
	tabmotif['entete_fond_i_V']=172;
	tabmotif['entete_fond_i_B']=255;
	tabmotif['cellule_i_R']=239;
	tabmotif['cellule_i_V']=239;
	tabmotif['cellule_i_B']=255;
	// #C8C8FF
	tabmotif['cellule_alt_i_R']=200;
	tabmotif['cellule_alt_i_V']=200;
	tabmotif['cellule_alt_i_B']=255;

	// Cahier de texte : Travaux futurs
	//	#FFFF80
	tabmotif['fond_notices_f_R']=255;
	tabmotif['fond_notices_f_V']=255;
	tabmotif['fond_notices_f_B']=128;
	// #FFFFDF
	tabmotif['cellule_f_R']=255;
	tabmotif['cellule_f_V']=255;
	tabmotif['cellule_f_B']=223;

	// Cahier de texte : Couleurs g�n�rales
	//	#FF4444
	tabmotif['police_travaux_R']=255;
	tabmotif['police_travaux_V']=68;
	tabmotif['police_travaux_B']=68;
	//	#008000
	tabmotif['police_matieres_R']=0;
	tabmotif['police_matieres_V']=128;
	tabmotif['police_matieres_B']=0;
	//	#6F6968
	tabmotif['bord_tableau_notice_R']=111;
	tabmotif['bord_tableau_notice_V']=105;
	tabmotif['bord_tableau_notice_B']=104;
	//	#F6F7EF
	tabmotif['cellule_gen_R']=246;
	tabmotif['cellule_gen_V']=247;
	tabmotif['cellule_gen_B']=239;


	function reinit_couleurs(motif) {
		comp_motif=motif+"_R";
		champ_R=eval("document.forms['tab']."+comp_motif);
		champ_R.value=tabmotif[comp_motif];

		comp_motif=motif+"_V";
		champ_V=eval("document.forms['tab']."+comp_motif);
		champ_V.value=tabmotif[comp_motif];

		comp_motif=motif+"_B";
		champ_B=eval("document.forms['tab']."+comp_motif);
		champ_B.value=tabmotif[comp_motif];

		//calcule_et_valide();
		affichecouleur(motif);

		//return false;
	}

//]]>
</script>
<!--noscript>
</noscript-->

<p>Cette page est destin�e � choisir les couleurs pour l'interface GEPI.
<!--Dans sa version actuelle, seule la couleur de fond de la page peut �tre param�tr�e depuis cette page.-->
</p>

<?php

/*
// Tableau des couleurs HTML:
$tab_html_couleurs=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");
*/

	// Initialisation
	$tabcouleurs=array();
	$tabcouleurs['style_body_backgroundcolor']=array();
	$style_body_backgroundcolor=getSettingValue('style_body_backgroundcolor');
	//echo "\$style_body_backgroundcolor=$style_body_backgroundcolor<br />";
	if($style_body_backgroundcolor!="") {
/*
		$compR=substr($style_body_backgroundcolor,0,2);
		$compV=substr($style_body_backgroundcolor,2,2);
		$compB=substr($style_body_backgroundcolor,4,2);

		//echo "\$compR=$compR<br />";
		//echo "\$compV=$compV<br />";
		//echo "\$compB=$compB<br />";

		$nb_compR=hex2nb(substr($compR,0,1))*16+hex2nb(substr($compR,1,1));
		$nb_compV=hex2nb(substr($compV,0,1))*16+hex2nb(substr($compV,1,1));
		$nb_compB=hex2nb(substr($compB,0,1))*16+hex2nb(substr($compB,1,1));

		//echo "\$nb_compR=$nb_compR<br />";
		//echo "\$nb_compV=$nb_compV<br />";
		//echo "\$nb_compB=$nb_compB<br />";

		$tabcouleurs['style_body_backgroundcolor']['R']=$nb_compR;
		$tabcouleurs['style_body_backgroundcolor']['V']=$nb_compV;
		$tabcouleurs['style_body_backgroundcolor']['B']=$nb_compB;
*/

	$tabcouleurs['style_body_backgroundcolor']=tab_rvb($style_body_backgroundcolor);
} else {
	// #EAEAEA
	// 14*16+10
	$tabcouleurs['style_body_backgroundcolor']['R']=234;
	$tabcouleurs['style_body_backgroundcolor']['V']=234;
	$tabcouleurs['style_body_backgroundcolor']['B']=234;
}


/*
	$tabcouleurs['style_body_backgroundcolor']=array();
	$style_body_backgroundcolor=getSettingValue('style_body_backgroundcolor');
	//echo "\$style_body_backgroundcolor=$style_body_backgroundcolor<br />";
	if($style_body_backgroundcolor!="") {
		$tabcouleurs['style_body_backgroundcolor']=tab_rvb($style_body_backgroundcolor);
	}
	else {
		// #EAEAEA
		// 14*16+10
		$tabcouleurs['style_body_backgroundcolor']['R']=234;
		$tabcouleurs['style_body_backgroundcolor']['V']=234;
		$tabcouleurs['style_body_backgroundcolor']['B']=234;
	}
*/

echo "<form id='tab' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	echo "<h2><strong>Couleurs:</strong></h2>\n";
	// echo "<blockquote>\n";
		echo "<div class='tableau_param_couleur'>\n";
			// echo "<tr>\n";
				// echo "<td>\n";
					echo "<input type='checkbox' name='utiliser_couleurs_perso' id='utiliser_couleurs_perso' value='y' ";
					if(getSettingValue('utiliser_couleurs_perso')=='y') {
						echo "checked='checked' ";
					}
					echo "/> ";
					echo "<label for='utiliser_couleurs_perso' style='cursor: pointer;'>Utiliser des couleurs personnalis�es.</label>\n";
				// echo "</td>\n";
			// echo "</tr>\n";
			// echo "<tr>\n";
			// 	echo "<td>\n";
			// 		echo "&nbsp;";
			// 	echo "</td>\n";
				// echo "<td>\n";
					echo "<table class='tableau_change_couleur' summary=\"arri�re plan changement de couleur : colonne 3 rouge, colonne 4 vert, colonne 5 bleu, colonne 7 validation\">\n";
						echo "<tr class='fond_blanc'>\n";
							echo "<td class='texte_gras'>\nMotif\n</td>\n";
							echo "<td class='texte_gras'>\nPropri�t�\n</td>\n";
							for($j=0;$j<count($comp);$j++) {
								echo "<td class='texte_gras'>\n$comp[$j]\n</td>\n";
							}
							echo "<td class='texte_gras'>\nAper�u\n</td>\n";
							echo "<td class='texte_gras'>\nR�initialisation\n</td>\n";
						echo "</tr>\n";
						for($i=0;$i<count($tab);$i++) {
							echo "<tr>\n";
								//echo "<td>$tab[$i]</td>\n";
								//echo "<td>Couleur de fond de page: <a href='couleur.php?objet=Fond'></a></td>\n";
								echo "<td>\n";
									echo "Couleur de fond de page\n";
									//echo "<a href='couleur.php?objet=".$tab[$i]."'></a>
								echo "</td>\n";
								echo "<td>\nbody{background-color: #XXXXXX;}\n</td>\n";
								for($j=0;$j<count($comp);$j++) {
									/*
									$sql="SELECT value FROM setting WHERE name='".$tab[$i]."_".$comp[$j]."'";
									$res_couleur=mysql_query($sql);
									if(mysql_num_rows($res_couleur)>0) {
										$tmp=mysql_fetch_object($res_couleur);
										$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
									}
									*/
									echo "<td>\n";
									//echo "$sql<br />";
									//echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onblur='affichecouleur(\"".$tab[$i]."\")' />\n";

									//echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' id='id_".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onChange='delai_affichecouleur(\"".$tab[$i]."\")' onkeydown=\"clavier_2(this.id,event);\" />\n";

									echo "<label for='id_".$tab[$i]."_".$comp[$j]."' class='invisible'>".$comp[$j]." fond ".$comp[$j]."</label>\n";
									echo "<input type='text' name='".$tab[$i]."_".$comp[$j]."' id='id_".$tab[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab[$i]][$comp[$j]]."' size='3' onblur='affichecouleur(\"".$tab[$i]."\")' onkeydown=\"clavier_2(this.id,event,0,255);\" />\n";
									echo "</td>\n";
								}

								//echo "<td id='".$tab[$i]."'>\n";
								echo "<td id='".$tab[$i]."'";

								echo " onclick=\"document.getElementById('id_couleur_r').value='id_".$tab[$i]."_R';";
								echo "document.getElementById('id_couleur_v').value='id_".$tab[$i]."_V';";
								echo "document.getElementById('id_couleur_b').value='id_".$tab[$i]."_B';";
								echo "document.getElementById('id_couleur_motif').value='".$tab[$i]."';";
								echo "afficher_div('div_choix_couleur','y',10,-200)\">";

								// Champ calcul�/mis � jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
								echo "<input type='hidden' name='$tab[$i]' value='$tab[$i]' />\n";
								echo "&nbsp;&nbsp;&nbsp;</td>\n";

								echo "<td>\n";
								//echo "<a href='#' onclick='reinit_couleurs(\"$tab[$i]\");return false;'>R�initialiser</a>\n";
								echo "<a href='#' onclick='reinit_couleurs(\"$tab[$i]\");return false;'>R�initialiser</a>\n";
								//echo "<a href='javascript:reinit_couleurs(\"$tab[$i]\");'>R�initialiser</a>\n";
								//echo "<input type='button' name='reinit$i' value='R�initialiser' onclick='javascript:reinit_couleurs(\"$tab[$i]\");' />\n";

								/*
								echo " <a href='#' onclick=\"document.getElementById('id_couleur_r').value='id_".$tab[$i]."_R';";
								echo "document.getElementById('id_couleur_v').value='id_".$tab[$i]."_V';";
								echo "document.getElementById('id_couleur_b').value='id_".$tab[$i]."_B';";
								echo "document.getElementById('id_couleur_motif').value='".$tab[$i]."';";
								echo "afficher_div('div_choix_couleur','y',10,-200)\">Choix</a>";
								*/

								echo "</td>\n";
							echo "</tr>\n";
						}
					echo "</table>\n";
				// echo "</td>\n";
			// echo "</tr>\n";
		echo "</div>\n";
	// echo "</blockquote>\n";





	echo "<h2>\n<strong>D�grad�:</strong>\n</h2>\n";
	// echo "<blockquote>\n";
		echo "<div class='tableau_param_couleur'>\n";
			// echo "<tr>\n";
				// echo "<td>\n";
					echo "<input type='checkbox' name='utiliser_degrade' id='utiliser_degrade' value='y' ";
					if(getSettingValue('utiliser_degrade')=='y') {
						echo "checked='checked' ";
					}
					echo "/> ";
				// echo "</td>\n";
				// echo "<td>\n";
					echo "<label for='utiliser_degrade' style='cursor: pointer;'>G�n�rer/utiliser un d�grad� personnalis� pour l'ent�te de page.</label>\n";
				// echo "</td>\n";
			// echo "</tr>\n";

			// echo "<tr>\n";
				// echo "<td>\n";
					// echo "&nbsp;";
				// echo "</td>\n";
				// echo "<td>\n";
					echo "<table class='tableau_change_couleur' summary=\"bandeau changement de couleur : ligne 2 d�grad� haut, ligne 3 d�grad� bas, colonne 2 rouge, colonne 3 vert, colonne 4 bleu, colonne 6 validation\">\n";
						echo "<tr class='fond_blanc'>\n";
							echo "<td class='texte_gras'>Couleur</td>\n";
							for($j=0;$j<count($comp);$j++) {
								echo "<td class='texte_gras'>$comp[$j]</td>\n";
							}
							echo "<td class='texte_gras'>Aper�u</td>\n";
							echo "<td class='texte_gras'>R�initialisation</td>\n";
						echo "</tr>\n";

						$tab_degrade=array("degrade_haut","degrade_bas");

						$degrade_haut=getSettingValue('degrade_haut');
						if($degrade_haut!="") {
							$tabcouleurs['degrade_haut']=tab_rvb($degrade_haut);
						}
						else {
							$tabcouleurs['degrade_haut']['R']=2;
							$tabcouleurs['degrade_haut']['V']=2;
							$tabcouleurs['degrade_haut']['B']=2;
						}

						$degrade_bas=getSettingValue('degrade_bas');
						if($degrade_bas!="") {
							$tabcouleurs['degrade_bas']=tab_rvb($degrade_bas);
						}
						else {
							$tabcouleurs['degrade_bas']['R']=74;
							$tabcouleurs['degrade_bas']['V']=74;
							$tabcouleurs['degrade_bas']['B']=89;
						}

						for($i=0;$i<count($tab_degrade);$i++) {
							echo "<tr>\n";

								echo "<td>$tab_degrade[$i]";
								echo "</td>\n";

								for($j=0;$j<count($comp);$j++) {
									/*
									$sql="SELECT value FROM setting WHERE name='".$tab_degrade[$i]."_".$comp[$j]."'";
									$res_couleur=mysql_query($sql);
									if(mysql_num_rows($res_couleur)>0) {
										$tmp=mysql_fetch_object($res_couleur);
										$tabcouleurs[$tab_degrade[$i]][$comp[$j]]=$tmp->value;
									}
									*/
									echo "<td>\n";
									echo "<label for='id_".$tab_degrade[$i]."_".$comp[$j]."' class='invisible'>".($i+1)."$comp[$j] degrade ".($i+1)."</label>\n";
										echo "<input type='text' name='".$tab_degrade[$i]."_".$comp[$j]."' id='id_".$tab_degrade[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab_degrade[$i]][$comp[$j]]."' size='3' onblur='affichecouleur(\"".$tab_degrade[$i]."\")' onkeydown=\"clavier_2(this.id,event,0,255);\" />\n";

									echo "</td>\n";
								}
								//echo "<td id='".$tab_degrade[$i]."'>\n";

								echo "<td id='".$tab_degrade[$i]."'";

								echo " onclick=\"document.getElementById('id_couleur_r').value='id_".$tab_degrade[$i]."_R';";
								echo "document.getElementById('id_couleur_v').value='id_".$tab_degrade[$i]."_V';";
								echo "document.getElementById('id_couleur_b').value='id_".$tab_degrade[$i]."_B';";
								echo "document.getElementById('id_couleur_motif').value='".$tab_degrade[$i]."';";
								echo "afficher_div('div_choix_couleur','y',10,-200)\">";

								// Champ calcul�/mis � jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
									echo "<input type='hidden' name='$tab_degrade[$i]' value='$tab_degrade[$i]' />\n";
								echo "&nbsp;&nbsp;&nbsp;</td>\n";

								echo "<td>\n";
									echo "<a href='#' onclick='reinit_couleurs(\"$tab_degrade[$i]\");return false;'>R�initialiser</a>\n";
								echo "</td>\n";


							echo "</tr>\n";
						}
					echo "</table>\n";
				// echo "</td>\n";
			// echo "</tr>\n";
		echo "</div>\n";
	// echo "</blockquote>\n";











	$tabcouleurs['couleur_infobulle_fond_entete']=array();
	$couleur_infobulle_fond_entete=getSettingValue('couleur_infobulle_fond_entete');
	if($couleur_infobulle_fond_entete!="") {
		$tabcouleurs['couleur_infobulle_fond_entete']=tab_rvb($couleur_infobulle_fond_entete);
	}
	else {
		// #4a4a59
		// 4*16+10=74 et 5*16+9=89
		$tabcouleurs['couleur_infobulle_fond_entete']['R']=74;
		$tabcouleurs['couleur_infobulle_fond_entete']['V']=74;
		$tabcouleurs['couleur_infobulle_fond_entete']['B']=89;
	}

	$tabcouleurs['couleur_infobulle_fond_corps']=array();
	$couleur_infobulle_fond_corps=getSettingValue('couleur_infobulle_fond_corps');
	if($couleur_infobulle_fond_corps!="") {
		$tabcouleurs['couleur_infobulle_fond_corps']=tab_rvb($couleur_infobulle_fond_corps);
	}
	else {
		// #EAEAEA
		// 14*16+10=234
		$tabcouleurs['couleur_infobulle_fond_corps']['R']=234;
		$tabcouleurs['couleur_infobulle_fond_corps']['V']=234;
		$tabcouleurs['couleur_infobulle_fond_corps']['B']=234;
	}

	echo "<h2><strong>Couleurs des 'infobulles':</strong></h2>\n";
	// echo "<blockquote>\n";
		echo "<div class='tableau_param_couleur'>\n";
		// echo "<tr>\n";
			// echo "<td>\n";
				echo "<input type='checkbox' name='utiliser_couleurs_perso_infobulles' id='utiliser_couleurs_perso_infobulles' value='y' ";
				if(getSettingValue('utiliser_couleurs_perso_infobulles')=='y') {
					echo "checked='checked' ";
				}
				echo "/> ";
			// echo "</td>\n";
			// echo "<td>\n";
				echo "<label for='utiliser_couleurs_perso_infobulles' style='cursor: pointer;'>Utiliser des couleurs personnalis�es pour les infobulles.</label>\n";
			// echo "</td>\n";
		// echo "</tr>\n";

		// echo "<tr>\n";
			// echo "<td>\n";
				// echo "&nbsp;";
			// echo "</td>\n";
			// echo "<td>\n";
				echo "<table class='tableau_change_couleur' summary=\"infobulles changement de couleurs : ligne 2 ent�te, ligne 3 corps, colonne 2 rouge, colonne 3 vert, colonne 4 bleu, colonne 6 validation\">\n";

					echo "<tr class='fond_blanc'>\n";
						echo "<td class='texte_gras'>\nMotif\n</td>\n";
						for($j=0;$j<count($comp);$j++) {
							echo "<td class='texte_gras'>\n$comp[$j]\n</td>\n";
						}
						echo "<td class='texte_gras'>\nAper�u\n</td>\n";
						echo "<td class='texte_gras'>\nR�initialisation\n</td>\n";

					echo "</tr>\n";

					echo "<tr>\n";
						echo "<td>\n";
							echo "Couleur de fond de l'ent�te des infobulles\n";
						echo "</td>\n";
						for($j=0;$j<count($comp);$j++) {
							/*
							$sql="SELECT value FROM setting WHERE name='".$tab[$i]."_".$comp[$j]."'";
							$res_couleur=mysql_query($sql);
							if(mysql_num_rows($res_couleur)>0) {
								$tmp=mysql_fetch_object($res_couleur);
								$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
							}
							*/

							echo "<td>\n";
							echo "<label for='id_couleur_infobulle_fond_entete_".$comp[$j]."' class='invisible'>".$comp[$j]."E ent�te ".$comp[$j]."</label>\n";
							echo "<input type='text' name='couleur_infobulle_fond_entete_".$comp[$j]."' id='id_couleur_infobulle_fond_entete_".$comp[$j]."' value='".$tabcouleurs['couleur_infobulle_fond_entete'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_infobulle_fond_entete\")' onkeydown=\"clavier_2(this.id,event,0,255);\" />\n";
							echo "</td>\n";
						}
						//echo "<td id='couleur_infobulle_fond_entete'>\n";

						echo "<td id='couleur_infobulle_fond_entete'";

						echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_infobulle_fond_entete_R';";
						echo "document.getElementById('id_couleur_v').value='id_couleur_infobulle_fond_entete_V';";
						echo "document.getElementById('id_couleur_b').value='id_couleur_infobulle_fond_entete_B';";
						echo "document.getElementById('id_couleur_motif').value='couleur_infobulle_fond_entete';";
						echo "afficher_div('div_choix_couleur','y',10,-200)\">";

						// Champ calcul�/mis � jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
							echo "<input type='hidden' name='couleur_infobulle_fond_entete' value='couleur_infobulle_fond_entete' />\n";
						echo "&nbsp;&nbsp;&nbsp;</td>\n";
						echo "<td>\n";
							echo "<a href='#' onclick='reinit_couleurs(\"couleur_infobulle_fond_entete\");return false;'>R�initialiser</a>\n";
						echo "</td>\n";
						echo "</tr>\n";

						echo "<tr>\n";
							echo "<td>\n";
								echo "Couleur de fond du corps des infobulles";
							echo "</td>\n";
							for($j=0;$j<count($comp);$j++) {
								/*
								$sql="SELECT value FROM setting WHERE name='".$tab[$i]."_".$comp[$j]."'";
								$res_couleur=mysql_query($sql);
								if(mysql_num_rows($res_couleur)>0) {
									$tmp=mysql_fetch_object($res_couleur);
									$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
								}
								*/

								echo "<td>\n";
									echo "<label for='id_couleur_infobulle_fond_corps_".$comp[$j]."' class='invisible'>".$comp[$j]."C corps ".$comp[$j]."</label>\n";
									echo "<input type='text' name='couleur_infobulle_fond_corps_".$comp[$j]."' id='id_couleur_infobulle_fond_corps_".$comp[$j]."' value='".$tabcouleurs['couleur_infobulle_fond_corps'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_infobulle_fond_corps\")' onkeydown=\"clavier_2(this.id,event,0,255);\" />\n";
								echo "</td>\n";
							}
							//echo "<td id='couleur_infobulle_fond_corps'>\n";
							echo "<td id='couleur_infobulle_fond_corps'";

							echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_infobulle_fond_corps_R';";
							echo "document.getElementById('id_couleur_v').value='id_couleur_infobulle_fond_corps_V';";
							echo "document.getElementById('id_couleur_b').value='id_couleur_infobulle_fond_corps_B';";
							echo "document.getElementById('id_couleur_motif').value='couleur_infobulle_fond_corps';";
							echo "afficher_div('div_choix_couleur','y',10,-200)\">";

							// Champ calcul�/mis � jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
								echo "<input type='hidden' name='couleur_infobulle_fond_corps' value='couleur_infobulle_fond_corps' />\n";
							echo "&nbsp;&nbsp;&nbsp;</td>\n";
							echo "<td>\n";
								echo "<a href='#' onclick='reinit_couleurs(\"couleur_infobulle_fond_corps\");return false;'>R�initialiser</a>\n";
							echo "</td>\n";
						echo "</tr>\n";

					echo "</table>\n";
				// echo "</td>\n";
			// echo "</tr>\n";
		echo "</div>\n";
	// echo "</blockquote>\n";




		//=========================================

		$tabcouleurs['couleur_lig_alt1']=array();
		$couleur_lig_alt1=getSettingValue('couleur_lig_alt1');
		if($couleur_lig_alt1!="") {
			$tabcouleurs['couleur_lig_alt1']=tab_rvb($couleur_lig_alt1);
		}
		else {
			// papayawhip #FFEFD5
			$tabcouleurs['couleur_lig_alt1']['R']=255;
			$tabcouleurs['couleur_lig_alt1']['V']=239;
			$tabcouleurs['couleur_lig_alt1']['B']=213;
		}

		$tabcouleurs['couleur_lig_alt_1']=array();
		$couleur_lig_alt_1=getSettingValue('couleur_lig_alt_1');
		if($couleur_lig_alt_1!="") {
			$tabcouleurs['couleur_lig_alt_1']=tab_rvb($couleur_lig_alt_1);
		}
		else {
			// honeydew #F0FFF0
			$tabcouleurs['couleur_lig_alt_1']['R']=240;
			$tabcouleurs['couleur_lig_alt_1']['V']=255;
			$tabcouleurs['couleur_lig_alt_1']['B']=240;
		}

	echo "<h2><strong>Couleurs des lignes altern�es dans les tableaux:</strong></h2>\n";
	// echo "<blockquote>\n";
		// echo "<table border='0'>\n";
		echo "<div class='tableau_param_couleur'>\n";
				// echo "<td>\n";
					echo "<input type='checkbox' name='utiliser_couleurs_perso_lig_tab_alt' id='utiliser_couleurs_perso_lig_tab_alt' value='y' ";
					if(getSettingValue('utiliser_couleurs_perso_lig_tab_alt')=='y') {
						echo "checked='checked' ";
					}
					echo "/> ";
				// echo "</td>\n";
				// echo "<td>\n";
					echo "<label for='utiliser_couleurs_perso_lig_tab_alt' style='cursor: pointer;'>couleurs des tableaux.</label>\n";
				// echo "</td>\n";
			// echo "</tr>\n";

			// echo "<tr>\n";
				// echo "<td>\n";
			// 	echo "&nbsp;";
				// echo "</td>\n";
				// echo "<td>\n";
					echo "<table class='tableau_change_couleur' summary=\"tableau changement de couleur : ligne 2 lignes impaires, ligne 3 lignes paires, colonne 2 rouge, colonne 3 vert, colonne 4 bleu, colonne 6 validation\">\n";

						echo "<tr class='fond_blanc'>\n";
							echo "<td class='texte_gras'>Motif</td>\n";
							for($j=0;$j<count($comp);$j++) {
								echo "<td class='texte_gras'>$comp[$j]</td>\n";
							}
							echo "<td class='texte_gras'>Aper�u</td>\n";
							echo "<td class='texte_gras'>R�initialisation</td>\n";

						echo "</tr>\n";

						echo "<tr>\n";
							echo "<td>Couleur de ligne 1";
							echo "</td>\n";
							for($j=0;$j<count($comp);$j++) {
								/*
								$sql="SELECT value FROM setting WHERE name='".$tab[$i]."_".$comp[$j]."'";
								$res_couleur=mysql_query($sql);
								if(mysql_num_rows($res_couleur)>0) {
									$tmp=mysql_fetch_object($res_couleur);
									$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
								}
								*/

								echo "<td>\n";
									echo "<label for='id_couleur_lig_alt1_".$comp[$j]."' class='invisible'>".$comp[$j]." P lignes paires ".$comp[$j]."</label>\n";
									echo "<input type='text' name='couleur_lig_alt1_".$comp[$j]."' id='id_couleur_lig_alt1_".$comp[$j]."' value='".$tabcouleurs['couleur_lig_alt1'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_lig_alt1\")' onkeydown=\"clavier_2(this.id,event,0,255);\" />\n";
								echo "</td>\n";
							}
							//echo "<td id='couleur_lig_alt1'>\n";
							echo "<td id='couleur_lig_alt1'";

							echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_lig_alt1_R';";
							echo "document.getElementById('id_couleur_v').value='id_couleur_lig_alt1_V';";
							echo "document.getElementById('id_couleur_b').value='id_couleur_lig_alt1_B';";
							echo "document.getElementById('id_couleur_motif').value='couleur_lig_alt1';";
							echo "afficher_div('div_choix_couleur','y',10,-200)\">";

							// Champ calcul�/mis � jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
								echo "<input type='hidden' name='couleur_lig_alt1' value='couleur_lig_alt1' />\n";
							echo "&nbsp;&nbsp;&nbsp;</td>\n";
							echo "<td>\n";
								echo "<a href='#' onclick='reinit_couleurs(\"couleur_lig_alt1\");return false;'>R�initialiser</a>\n";
							echo "</td>\n";
						echo "</tr>\n";

						echo "<tr>\n";
							echo "<td>Couleur de ligne -1";
							echo "</td>\n";
							for($j=0;$j<count($comp);$j++) {
								/*
								$sql="SELECT value FROM setting WHERE name='".$tab[$i]."_".$comp[$j]."'";
								$res_couleur=mysql_query($sql);
								if(mysql_num_rows($res_couleur)>0) {
									$tmp=mysql_fetch_object($res_couleur);
									$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
								}
								*/

								echo "<td>\n";
							echo "<label for='id_couleur_lig_alt_1_".$comp[$j]."' class='invisible'>".$comp[$j]." I lignes impaires ".$comp[$j]."</label>\n";
									echo "<input type='text' name='couleur_lig_alt_1_".$comp[$j]."' id='id_couleur_lig_alt_1_".$comp[$j]."' value='".$tabcouleurs['couleur_lig_alt_1'][$comp[$j]]."' size='3' onblur='affichecouleur(\"couleur_lig_alt_1\")' onkeydown=\"clavier_2(this.id,event,0,255);\" />\n";
								echo "</td>\n";
							}
							//echo "<td id='couleur_lig_alt_1'>\n";
							echo "<td id='couleur_lig_alt_1'";

							echo " onclick=\"document.getElementById('id_couleur_r').value='id_couleur_lig_alt_1_R';";
							echo "document.getElementById('id_couleur_v').value='id_couleur_lig_alt_1_V';";
							echo "document.getElementById('id_couleur_b').value='id_couleur_lig_alt_1_B';";
							echo "document.getElementById('id_couleur_motif').value='couleur_lig_alt_1';";
							echo "afficher_div('div_choix_couleur','y',10,-200)\">";

							// Champ calcul�/mis � jour par la fonction JavaScript calcule_et_valide() lors de la validation du formulaire:
								echo "<input type='hidden' name='couleur_lig_alt_1' value='couleur_lig_alt_1' />\n";
							echo "&nbsp;&nbsp;&nbsp;</td>\n";
							echo "<td>\n";
								echo "<a href='#' onclick='reinit_couleurs(\"couleur_lig_alt_1\");return false;'>R�initialiser</a>\n";
							echo "</td>\n";
						echo "</tr>\n";

					echo "</table>\n";
				// echo "</td>\n";
			// echo "</tr>\n";
		echo "</div>\n";
	// echo "</blockquote>\n";



/* ===== couleurs du cahier de texte ===== */

	echo "<h2>\n<strong>Notices cahier de textes :</strong>\n</h2>\n";
	echo "<div class='tableau_param_couleur'>\n";
		echo "<input type='checkbox' name='utiliser_cahier_texte_perso' id='utiliser_cahier_texte_perso' value='y' ";
		if(getSettingValue('utiliser_cahier_texte_perso')=='y') {
			echo "checked='checked' ";
		}
		echo "/> ";
		echo "<label for='utiliser_cahier_texte_perso' style='cursor: pointer;'>Couleurs personnalis�es dans le cahier de textes.</label>\n";

//=== initialisation des couleurs ===

		// tableaux de noms
		$tab_ct_couleur_fond=array("fond_notices", "entete_fond", "cellule", "cellule_alt");
		$tab_ct_couleur_classe=array("color_fond_notices", "couleur_entete_fond", "couleur_cellule", "couleur_cellule_alt");
		$tab_ct_nom_couleur_fond=array("fond des notices", "ent�te des notices", "notices", "cellule_alt");
		$tab_ct_notice=array("c","t","i","f");
		$tab_ct_nom_notice=array("Compte rendu de s�ance","Travail � faire","Informations g�n�rales","Rappel des travaux � faire");
		$tab_ct_police_bordure=array("police_travaux","police_matieres","bord_tableau_notice","cellule_gen");
		$tab_ct_nom_police_bordure=array("police des notices travaux","police des matieres","bord des tableaux","Couleur g�n�rale des cellules");

// ----- Notices -----

		// Couleurs d'origine
		$tab_ct_couleur_origine=array("fond_notices", "entete_fond", "cellule", "cellule_alt");
		$tab_ct_couleur_origine[]=array("c","t","i","f");
		$tab_ct_couleur_origine[][]=array();
		$tab_ct_couleur_origine["fond_notices"]["c"]="C7FF99";
		$tab_ct_couleur_origine["entete_fond"]["c"]="C7FF99";
		$tab_ct_couleur_origine["cellule"]["c"]="E5FFCF";
		$tab_ct_couleur_origine["cellule_alt"]["c"]="D3FFAF";
		$tab_ct_couleur_origine["fond_notices"]["t"]="FFCCCF";
		$tab_ct_couleur_origine["entete_fond"]["t"]="FFCCCF";
		$tab_ct_couleur_origine["cellule"]["t"]="FFEFF0";
		$tab_ct_couleur_origine["cellule_alt"]["t"]="FFDFE2";
		$tab_ct_couleur_origine["fond_notices"]["i"]="ACACFF";
		//$tab_ct_couleur_origine["entete_fond"]["i"]="EFEFFF";
		$tab_ct_couleur_origine["entete_fond"]["i"]="ACACFF";
		$tab_ct_couleur_origine["cellule"]["i"]="EFEFFF";
		$tab_ct_couleur_origine["cellule_alt"]["i"]="C8C8FF";
		$tab_ct_couleur_origine["fond_notices"]["f"]="FFFF80";
		$tab_ct_couleur_origine["cellule"]["f"]="FFFFDF";

		// Affectation des couleurs
		for($i=0;$i<count($tab_ct_notice);$i++) {
			for($j=0;$j<count($tab_ct_couleur_fond);$j++) {
				$tabcouleurs[$tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]]=array();
				$couleur_traite=getSettingValue($tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]);
				if($couleur_traite!="") {
					$tabcouleurs[$tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]]=tab_rvb($couleur_traite);
				} else {
					if (isset($tab_ct_couleur_origine[$tab_ct_couleur_fond[$j]][$tab_ct_notice[$i]])) {
						$tabcouleurs[$tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]]=tab_rvb($tab_ct_couleur_origine[$tab_ct_couleur_fond[$j]][$tab_ct_notice[$i]]);
					}
				}
			}
		}

		// Tableau de r�glage des couleurs
		for($i=0;$i<count($tab_ct_notice);$i++) {
		// Titre de la notice
			echo "<h3>$tab_ct_nom_notice[$i]</h3>";
			echo "<table class='tableau_change_couleur' summary=\"cahier de texte changement de couleur\">\n";
				// ent�te
				echo "<tr class='fond_blanc'>\n";
					echo "<td class='texte_gras'>Couleur</td>\n";
					for($j=0;$j<count($comp);$j++) {
						echo "<td class='texte_gras'>$comp[$j]</td>\n";
					}
					echo "<td class='texte_gras'>Aper�u</td>\n";
					echo "<td class='texte_gras'>R�initialisation</td>\n";
				echo "</tr>\n";
				// Donn�es de la couleur
					for($j=0;$j<count($tab_ct_couleur_fond);$j++) {
						if (isset($tab_ct_couleur_origine[$tab_ct_couleur_fond[$j]][$tab_ct_notice[$i]])) {
							echo "<tr>\n";
								echo "<td>".$tab_ct_nom_couleur_fond[$j]."</td>";
								// couleurs RVB
								for($k=0;$k<count($comp);$k++) {
									echo "<td>\n";
								echo "<label for='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_".$comp[$k]."' class='invisible'>".$comp[$k]." ".$tab_ct_notice[$i]." ".$tab_ct_couleur_fond[$j]."</label>\n";
								echo "<input type='text' name='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_".$comp[$k]."' id='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_".$comp[$k]."' value='".$tabcouleurs[$tab_ct_couleur_fond[$j].'_'.$tab_ct_notice[$i]][$comp[$k]]."' size='3' onblur='affichecouleur(\"".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."\")' onkeydown=\"clavier_2(this.id,event,0,255);\" />\n</td>\n";
								}

								//echo "<td id='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."'>";

								echo "<td id='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."'";

								echo " onclick=\"document.getElementById('id_couleur_r').value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_R';";
								echo "document.getElementById('id_couleur_v').value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_V';";
								echo "document.getElementById('id_couleur_b').value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."_B';";
								echo "document.getElementById('id_couleur_motif').value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."';";
								echo "afficher_div('div_choix_couleur','y',10,-200)\">";


									echo "<input type='hidden' name='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."' value='".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."' />\n";
								echo "</td>\n";
								echo "<td>\n";
									echo "<a href='#' onclick='reinit_couleurs(\"".$tab_ct_couleur_fond[$j]."_".$tab_ct_notice[$i]."\");return false;'>R�initialiser</a>\n";
								echo "</td>\n";
							echo "</tr>\n";
						}
					}
			// Fin nom de la couleur
			echo "</table>\n";
			// Fin nom de la notice
		}
// ----- Fin fonds des notices -----

// ----- Couleurs communes � toutes les notices -----

		// Couleurs d'origine
		$tab_ct_couleur_gen_origine=array("police_travaux","police_matieres","bord_tableau_notice","cellule_gen");
		$tab_ct_couleur_gen_origine["police_travaux"]="FF4444";
		$tab_ct_couleur_gen_origine["police_matieres"]="008000";
		$tab_ct_couleur_gen_origine["bord_tableau_notice"]="6F6968";
		$tab_ct_couleur_gen_origine["cellule_gen"]="F6F7EF";

		// Affectation des couleurs
		for($j=0;$j<count($tab_ct_police_bordure);$j++) {
			$tabcouleurs[$tab_ct_police_bordure[$j]]=array();
			$couleur_traite=getSettingValue($tab_ct_police_bordure[$j]);
			if($couleur_traite!="") {
				$tabcouleurs[$tab_ct_police_bordure[$j]]=tab_rvb($couleur_traite);
			} else {
				if (isset($tab_ct_couleur_gen_origine[$tab_ct_police_bordure[$j]])) {
					$tabcouleurs[$tab_ct_police_bordure[$j]]=tab_rvb($tab_ct_couleur_gen_origine[$tab_ct_police_bordure[$j]]);
				}
			}
		}

		// Tableau de r�glage des couleurs
		// Titre de la notice
		echo "<h3>Polices, bordures ...</h3>";
		echo "<table class='tableau_change_couleur' summary=\"cahier de texte changement de couleur\">\n";
			// ent�te
			echo "<tr class='fond_blanc'>\n";
				echo "<td class='texte_gras'>Couleur</td>\n";
				for($j=0;$j<count($comp);$j++) {
					echo "<td class='texte_gras'>$comp[$j]</td>\n";
				}
				echo "<td class='texte_gras'>Aper�u</td>\n";
				echo "<td class='texte_gras'>R�initialisation</td>\n";
			echo "</tr>\n";
			// Donn�es de la couleur
			for($i=0;$i<count($tab_ct_police_bordure);$i++) {
				echo "<tr>\n";
					echo "<td>".$tab_ct_nom_police_bordure[$i]."</td>\n";
						// couleurs RVB
						for($j=0;$j<count($comp);$j++) {
							echo "<td>\n";
								echo "<label for='".$tab_ct_police_bordure[$i]."_".$comp[$j]."' class='invisible'>".$tab_ct_police_bordure[$i]."_".$comp[$j]."</label>\n";
								echo "<input type='text' name='".$tab_ct_police_bordure[$i]."_".$comp[$j]."' id='".$tab_ct_police_bordure[$i]."_".$comp[$j]."' value='".$tabcouleurs[$tab_ct_police_bordure[$i]][$comp[$j]]."' size='3' onblur='affichecouleur(\"".$tab_ct_police_bordure[$i]."\")' onkeydown=\"clavier_2(this.id,event,0,255);\" />\n";
							echo "</td>\n";
						}
					//echo "<td id='".$tab_ct_police_bordure[$i]."'>\n";
					echo "<td id='".$tab_ct_police_bordure[$i]."'";

					echo " onclick=\"document.getElementById('id_couleur_r').value='".$tab_ct_police_bordure[$i]."_R';";
					echo "document.getElementById('id_couleur_v').value='".$tab_ct_police_bordure[$i]."_V';";
					echo "document.getElementById('id_couleur_b').value='".$tab_ct_police_bordure[$i]."_B';";
					echo "document.getElementById('id_couleur_motif').value='".$tab_ct_police_bordure[$i]."';";
					echo "afficher_div('div_choix_couleur','y',10,-200)\">";



						echo "<input type='hidden' name='".$tab_ct_police_bordure[$i]."' value='".$tab_ct_police_bordure[$i]."' />\n";
					echo "</td>\n";
					echo "<td>\n";
						echo "<a href='#' onclick='reinit_couleurs(\"".$tab_ct_police_bordure[$i]."\");return false;'>R�initialiser</a>\n";
					echo "</td>\n";
				echo "</tr>\n";
			}
		echo "</table>\n";

// Fin couleurs communes

	echo "</div>\n";




/*============================================*/
/* 	 �a marche, il manque enregistrement    */
/*============================================*/


		//=========================================


	echo "<p class='decale_bas'>\n";
	echo "<input type='hidden' name='is_posted' value='1' />\n";
	// echo "<p style='text-align:center;'><input type='submit' name='ok' value='Valider' /></p>\n";
	echo "<input type='button' name='ok' value='Valider' onclick='calcule_et_valide()' />\n</p>\n";


	echo "<h2><strong>Remarque:</strong></h2>";
	// echo "<blockquote>\n";
		echo "<p>Il peut arriver qu'il faille insister apr�s validation pour que le navigateur recharge bien la page (<em>probl�me de cache du navigateur</em>).<br />Vous pouvez forcer le rechargement avec CTRL+MAJ+R.</p>\n";
	// echo "</blockquote>\n";

	/*
		//echo "<input type='text' name='truc' value='100' size='3' onkeypress='test_clavier(\"truc\")' />\n";
		//echo "<input type='text' name='truc' value='100' size='3' onkeypress='xKey(\"Event\")' />\n";
		//echo "<input type='text' name='truc' value='100' size='3' onkeypress='xKey(Event)' />\n";
		echo "<input type='text' name='truc' id='id_truc' value='100' size='3' onkeydown=\"clavier_2(this.id,event);\" />\n";
		//echo "<input type='text' name='truc' id='id_truc' value='100' size='3' onKeyPress=\"clavier_2(this.id,event);\" />\n";
	*/
	//echo "<p><br /></p>\n";

	// echo "<div class='centre_texte'>\n";
		echo "<div class='panneau_secour'>\n";
			echo "Le bouton ci-dessous est une 's�curit�'<br />pour r�initialiser les couleurs<br />si jamais vous en arriviez � obtenir quelque chose<br />comme du texte noir sur un fond noir.<br />\n";
			echo "<input type='hidden' name='secu' value='n' />\n";
			//echo "<input type='button' name='reinitialiser' value='R�initialiser' onclick='reinitialiser()' /></div>\n";
			echo "<input type='button' name='reinitialiser' value='R�initialiser' onclick='reinit()' />\n";
		echo "</div>\n";
	// echo "</div>\n";

	echo "<script type='text/javascript'>
		setTimeout('init()',500);





// Liste simplifi�e
var liste_style=new Array('style_body_backgroundcolor', 'degrade_haut', 'degrade_bas', 'couleur_infobulle_fond_corps', 'couleur_infobulle_fond_entete');

function valide_modele(choix) {
	var choix_valide='n';

	// Rose
	if(choix=='rose') {
		choix_valide='y';

		var id_style_body_backgroundcolor_R=250
		var id_style_body_backgroundcolor_V=220
		var id_style_body_backgroundcolor_B=220
		
		// Haut du d�grad�
		var id_degrade_haut_R=160
		var id_degrade_haut_V=80
		var id_degrade_haut_B=80
		
		// Bas du d�grad�
		var id_degrade_bas_R=200
		var id_degrade_bas_V=80
		var id_degrade_bas_B=80
		
		// Couleur de fond de l'ent�te des infobulles
		var id_couleur_infobulle_fond_entete_R=200
		var id_couleur_infobulle_fond_entete_V=80
		var id_couleur_infobulle_fond_entete_B=80
		
		// Couleur de fond du corps des infobulles
		var id_couleur_infobulle_fond_corps_R=250
		var id_couleur_infobulle_fond_corps_V=180
		var id_couleur_infobulle_fond_corps_B=180
	}
	//=============================================================
	// Vert
	if(choix=='vert') {	
		choix_valide='y';

		var id_style_body_backgroundcolor_R=230
		var id_style_body_backgroundcolor_V=250
		var id_style_body_backgroundcolor_B=230
		
		// Haut du d�grad�
		var id_degrade_haut_R=80
		var id_degrade_haut_V=140
		var id_degrade_haut_B=80
		
		// Bas du d�grad�
		var id_degrade_bas_R=80
		var id_degrade_bas_V=180
		var id_degrade_bas_B=80
		
		// Couleur de fond de l'ent�te des infobulles
		var id_couleur_infobulle_fond_entete_R=80
		var id_couleur_infobulle_fond_entete_V=180
		var id_couleur_infobulle_fond_entete_B=80
		
		// Couleur de fond du corps des infobulles
		var id_couleur_infobulle_fond_corps_R=200
		var id_couleur_infobulle_fond_corps_V=250
		var id_couleur_infobulle_fond_corps_B=200
	}	
	//=============================================================
	// Bleu
	if(choix=='bleu') {
		choix_valide='y';

		var id_style_body_backgroundcolor_R=230
		var id_style_body_backgroundcolor_V=230
		var id_style_body_backgroundcolor_B=250
		
		// Haut du d�grad�
		var id_degrade_haut_R=60
		var id_degrade_haut_V=60
		var id_degrade_haut_B=100
		
		// Bas du d�grad�
		var id_degrade_bas_R=80
		var id_degrade_bas_V=80
		var id_degrade_bas_B=160
		
		// Couleur de fond de l'ent�te des infobulles
		var id_couleur_infobulle_fond_entete_R=80
		var id_couleur_infobulle_fond_entete_V=80
		var id_couleur_infobulle_fond_entete_B=160
		
		// Couleur de fond du corps des infobulles
		var id_couleur_infobulle_fond_corps_R=200
		var id_couleur_infobulle_fond_corps_V=200
		var id_couleur_infobulle_fond_corps_B=250
	}
	//=============================================================
	// Chocolat
	if(choix=='chocolat') {
		choix_valide='y';

		var id_style_body_backgroundcolor_R=226
		var id_style_body_backgroundcolor_V=198
		var id_style_body_backgroundcolor_B=170
		
		// Haut du d�grad�
		var id_degrade_haut_R=53
		var id_degrade_haut_V=26
		var id_degrade_haut_B=0
		
		// Bas du d�grad�
		var id_degrade_bas_R=147
		var id_degrade_bas_V=77
		var id_degrade_bas_B=0
		
		// Couleur de fond de l'ent�te des infobulles
		var id_couleur_infobulle_fond_entete_R=180
		var id_couleur_infobulle_fond_entete_V=100
		var id_couleur_infobulle_fond_entete_B=0
		
		// Couleur de fond du corps des infobulles
		var id_couleur_infobulle_fond_corps_R=216
		var id_couleur_infobulle_fond_corps_V=180
		var id_couleur_infobulle_fond_corps_B=160
	}

	if(choix_valide=='y') {
		for(i=0;i<liste_style.length;i++) {
			document.getElementById('id_'+liste_style[i]+'_R').value=eval('id_'+liste_style[i]+'_R');
			document.getElementById('id_'+liste_style[i]+'_V').value=eval('id_'+liste_style[i]+'_V');
			document.getElementById('id_'+liste_style[i]+'_B').value=eval('id_'+liste_style[i]+'_B');

			affichecouleur(liste_style[i]);
		}

		document.getElementById('utiliser_couleurs_perso').checked=true;
		document.getElementById('utiliser_degrade').checked=true;
		document.getElementById('utiliser_couleurs_perso_infobulles').checked=true;

		//document.forms['tab'].submit();
		calcule_et_valide();
	}
}

</script>\n<!--noscript></noscript-->";

	/*
	echo "<a href=\"javascript:valide_modele('rose')\">Rose</a><br />";
	echo "<a href=\"javascript:valide_modele('vert')\">Vert</a><br />";
	echo "<a href=\"javascript:valide_modele('bleu')\">Bleu</a><br />";
	*/

echo "</form>\n";

require("../lib/footer.inc.php");
?>