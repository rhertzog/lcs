<?php
/*
 * $Id: avis_pdf.php 3789 2009-11-24 17:24:11Z crob $
 *
 * Copyright 2001, 2006 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

//INSERT INTO droits VALUES ('/impression/avis_pdf.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F','Impression des avis trimestrielles des conseils de classe. Module PDF', '');
 
// Global configuration file
// Quand on est en SSL, IE n'arrive pas � ouvrir le PDF.
//Le probl�me peut �tre r�solu en ajoutant la ligne suivante :
Header('Pragma: public');

//=============================
// REMONT�:
// Initialisations files
require_once("../lib/initialisations.inc.php");
//=============================

require('../fpdf/fpdf.php');
require('../fpdf/ex_fpdf.php');

define('FPDF_FONTPATH','../fpdf/font/');
define('LargeurPage','210');
define('HauteurPage','297');

/*
// Initialisations files
require_once("../lib/initialisations.inc.php");
*/

require_once("./class_pdf.php");
require_once ("./liste.inc.php"); //fonction qui retourne le nombre d'�l�ves par classe (ou groupe) pour une p�riode donn�e.

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un probl�me qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// LES OPTIONS DEBUT
if (!isset($_SESSION['marge_haut'])) { $MargeHaut = 10 ; } else {$MargeHaut =  $_SESSION['marge_haut'];}
if (!isset($_SESSION['marge_droite'])) { $MargeDroite = 10 ; } else {$MargeDroite =  $_SESSION['marge_droite'];}
if (!isset($_SESSION['marge_gauche'])) { $MargeGauche = 10 ; } else {$MargeGauche =  $_SESSION['marge_gauche'];}
if (!isset($_SESSION['marge_bas'])) { $MargeBas = 10 ; } else {$MargeBas =  $_SESSION['marge_bas'];}
if (!isset($_SESSION['marge_reliure'])) { $avec_reliure = 1 ; } else {$avec_reliure =  $_SESSION['marge_reliure'];}
if (!isset($_SESSION['avec_emplacement_trous'])) { $avec_emplacement_trous = 1 ; } else {$avec_emplacement_trous =  $_SESSION['avec_emplacement_trous'];}

//Gestion de la marge � gauche pour une reliure �ventuelle ou des feuilles perfor�es.
if ($avec_reliure==1) {
  if ($MargeGauche < 18) {$MargeGauche = 18;}
}

//Calcul de la Zone disponible
$EspaceX = LargeurPage - $MargeDroite - $MargeGauche ;
$EspaceY = HauteurPage - $MargeHaut - $MargeBas;
$X_tableau = $MargeGauche;

//ent�te classe et ann�e scolaire
$L_entete_classe = 65;
$H_entete_classe = 14;
$X_entete_classe = $EspaceX - $L_entete_classe + $MargeGauche;
$Y_entete_classe = $MargeHaut;

$X_entete_matiere = $MargeGauche;
$Y_entete_matiere = $MargeHaut;
$L_entete_discipline = 65;
$H_entete_discipline = 14;

// LES OPTIONS suite
if (!isset($_SESSION['h_ligne'])) { $h_cell = 8 ; } else {$h_cell =  $_SESSION['h_ligne'];} //hauteur d'une ligne du tableau
if (!isset($_SESSION['l_nomprenom'])) { $l_cell_nom = 40 ; } else {$l_cell_nom =  $_SESSION['l_nomprenom'];} // la largeur de la colonne nom - pr�nom
if (!isset($_SESSION['affiche_pp'])) { $option_affiche_pp = 1 ; } else {$option_affiche_pp =  $_SESSION['affiche_pp'];}// 0 On n'affiche pas le PP 1 on l'affiche
if (!isset($_SESSION['une_seule_page'])) { $option_tout_une_page = 1 ; } else {$option_tout_une_page =  $_SESSION['une_seule_page'];} // Faire tenir sur une seule page la classe 0 nom - 1 oui

$ligne_texte = "Avis du conseil de classe." ;
$texte = '';

// D�finition de la page
$pdf=new rel_PDF("P","mm","A4");
$pdf->SetTopMargin($MargeHaut);
$pdf->SetRightMargin($MargeDroite);
$pdf->SetLeftMargin($MargeGauche);
$pdf->SetAutoPageBreak(true, $MargeBas);



//On recup�re les variables pour l'affichage et on traite leur existance.
// DE   IMPRIME.PHP
$id_classe=isset($_GET['id_classe']) ? $_GET["id_classe"] : NULL;
$id_groupe=isset($_GET['id_groupe']) ? $_GET["id_groupe"] : NULL;
$id_periode=isset($_GET['periode_num']) ? $_GET["periode_num"] : NULL;

//On recup�re les variables pour l'affichage
// DE  IMPRIME_SERIE.PHP
// les tableaux contienent la liste des id.
$id_liste_classes=isset($_POST['id_liste_classes']) ? $_POST["id_liste_classes"] : NULL;
$id_liste_groupes=isset($_POST['id_liste_groupes']) ? $_POST["id_liste_groupes"] : NULL;
if ($id_periode==NULL){$id_periode=isset($_POST['id_periode']) ? $_POST["id_periode"] : NULL;} 
if (!(is_numeric($id_periode))) {
	$id_periode=1;
}

$nb_pages = 0;
$nb_eleves = 0;

// DEFINIR LE NOMBRE DE BOUCLES A FAIRE
// Impressions RAPIDES
if ($id_groupe!=NULL) { // C'est un groupe
    $nb_pages=1;
}

if ($id_classe!=NULL) { // C'est une classe
    $nb_pages=1;
} //fin c'est une classe

//IMPRESSION A LA CHAINE
if ($id_liste_classes!=NULL) {
    $nb_pages = sizeof($id_liste_classes);
//echo $nb_pages;
}

//IMPRESSION A LA CHAINE
if ($id_liste_groupes!=NULL) {
    $nb_pages = sizeof($id_liste_groupes);
//echo $nb_pages;
}

	// Cette boucle cr�e les diff�rentes pages du PDF
	for ($i_pdf=0; $i_pdf<$nb_pages ; $i_pdf++) {
		// Impressions RAPIDES
		if ($id_groupe!=NULL) { // C'est un groupe
			$donnees_eleves = traite_donnees_groupe($id_groupe,$id_periode,$nb_eleves);
			//$id_classe=$donnees_eleves['id_classe'][0];
			$id_classe=$donnees_eleves[0]['id_classe'];
		} elseif ($id_classe!=NULL) { // C'est une classe
			$donnees_eleves = traite_donnees_classe($id_classe,$id_periode,$nb_eleves);
		} //fin c'est une classe
			
		//IMPRESSION A LA CHAINE
		if ($id_liste_groupes!=NULL) {
			$donnees_eleves = traite_donnees_groupe($id_liste_groupes[$i_pdf],$id_periode,$nb_eleves);
			$id_groupe=$id_liste_groupes[$i_pdf];
			//$id_classe=$donnees_eleves['id_classe'][0];
			$id_classe=$donnees_eleves[0]['id_classe'];
		}

		if ($id_liste_classes!=NULL) {
			$donnees_eleves = traite_donnees_classe($id_liste_classes[$i_pdf],$id_periode,$nb_eleves);
			$id_classe=$id_liste_classes[$i_pdf];
		}

		// CALCUL de VARIABLES
		//Calcul de la hauteur de la ligne dans le cas de l'option tout sur une ligne
		if ($option_tout_une_page == 1) {
			  $hauteur_disponible = HauteurPage - $MargeHaut - $MargeBas - $H_entete_classe - 5 - 2.5; //2.5 ==> avant le pied de page

				$hauteur_disponible = $hauteur_disponible - 12.5;
				
			  // le nombre de lignes demand�es.
			  $nb_ligne_demande = $nb_eleves;

			  $h_cell = $hauteur_disponible / $nb_ligne_demande ;
		}

		$pdf->AddPage("P");
		// Couleur des traits
		$pdf->SetDrawColor(0,0,0);

		// caract�re utilis� dans le document
		$caractere_utilise = 'arial';

		// on appelle une nouvelle page pdf
		$nb_eleves_i = 0;

		//Ent�te du PDF
			$pdf->SetLineWidth(0.7);
			$pdf->SetFont($caractere_utilise,'B',14);
			$pdf->Setxy($X_entete_classe,$Y_entete_classe);

			if ($id_classe != NULL) {
			  $calldata = mysql_query("SELECT * FROM classes WHERE id = '$id_classe'");
			  $current_classe = mysql_result($calldata, 0, "classe");
			} else {
			   $sql = "SELECT * FROM classes WHERE id = '$id_classe'";
			   $calldata = mysql_query($sql);
			   $current_classe = mysql_result($calldata, 0, "classe");
			}

			if (($option_affiche_pp==1)) {
			   $pdf->CellFitScale($L_entete_classe,$H_entete_classe / 2,'Classe de '.$current_classe,'LTR',2,'C');
			   $pdf->SetFont($caractere_utilise,'I',8.5);

			   //PP de la classe
			  if ($id_groupe != NULL) {
				 $id_classe=$donnees_eleves['id_classe'][0];
			   }
			   //$sql = "SELECT professeur FROM j_eleves_professeurs WHERE (login = '".$donnees_eleves['login'][0]."' and id_classe='$id_classe')";
			   $sql = "SELECT professeur FROM j_eleves_professeurs WHERE (login = '".$donnees_eleves[0]['login']."' and id_classe='$id_classe')";
				//echo "$sql<br />\n";
			   $call_profsuivi_eleve = mysql_query($sql);
			   $current_eleve_profsuivi_login = @mysql_result($call_profsuivi_eleve, '0', 'professeur');

			   $pdf->CellFitScale($L_entete_classe,$H_entete_classe / 2,ucfirst(getSettingValue("gepi_prof_suivi")).' : '.affiche_utilisateur($current_eleve_profsuivi_login,$id_classe),'LRB',0,'L');//'Ann�e scolaire '.getSettingValue('gepiYear')
			} else {

			  if ($id_groupe != NULL) {
				//$current_classe = $donnees_eleves['id_classe'][0]; // on suppose qu'il n'y a dans un groupe que des personnes d'une m�me classe ... Bof Bof
				$current_classe = $donnees_eleves[0]['id_classe']; // on suppose qu'il n'y a dans un groupe que des personnes d'une m�me classe ... Bof Bof
			  }
			  $pdf->CellFitScale($L_entete_classe,$H_entete_classe,'Classe de '.$current_classe,'LTRB',2,'C');
			}

			$pdf->Setxy($X_entete_matiere,$Y_entete_matiere);
			$pdf->SetFont($caractere_utilise,'B',14);

			
			//Si on peut conna�tre le nom de la mati�re (id_groupe existe !)
			if ($id_groupe != NULL) {
				$current_group = get_group($id_groupe);
				$matiere = $current_group["description"];
                 //echo $matiere."<br/>";
				$pdf->CellFitScale($L_entete_discipline,$H_entete_discipline /2 ,$matiere,'LTR',2,'C');
				$pdf->SetFont($caractere_utilise,'I',11);
				$pdf->Cell($L_entete_classe,$H_entete_classe / 2,'Ann�e scolaire '.getSettingValue('gepiYear'),'LRB',2,'C');
			} else {
			// On demande une classe ==> on ajoute la p�riode.
				$pdf->SetFont($caractere_utilise,'I',11);

				$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$id_classe' AND num_periode=$id_periode ORDER BY num_periode";
				$res_per=mysql_query($sql);
				if(mysql_num_rows($res_per)==0){
					die("Probl�me avec les infos de la classe $id_classe</body></html>");
				}
				else{
					$lig_tmp=mysql_fetch_object($res_per);
					$periode=$lig_tmp->nom_periode;
					$pdf->Cell($L_entete_classe,$H_entete_classe / 2,'Ann�e scolaire '.getSettingValue('gepiYear'),'TLR',2,'C');
					$pdf->CellFitScale($L_entete_discipline,$H_entete_classe / 2 ,$periode,'LBR',2,'C');
				}
			}

			$Y_courant=$pdf->GetY()+2.5;
			$pdf->Setxy($MargeGauche,$Y_courant);

		//La ligne de texte apr�s les ent�tes
				$pdf->CellFitScale(0,10,$ligne_texte,'',2,'C');
				$Y_courant=$pdf->GetY()+2.5;
			
// requete � faire pour r�cup�rer les Avis pour la classe / la p�riode !!!

		//debut tableau;
			$pdf->SetLineWidth(0.3);
			$pdf->SetFont($caractere_utilise,'',9);
			$y_tmp = $Y_courant;

			// Le tableau
			while($nb_eleves_i < $nb_eleves) {	
			    //$login_elv = $donnees_eleves['login'][$nb_eleves_i];
			    $login_elv = $donnees_eleves[$nb_eleves_i]['login'];
                $sql_current_eleve_avis = "SELECT avis FROM avis_conseil_classe WHERE (login='$login_elv' AND periode='$id_periode')";
				//echo "$sql_current_eleve_avis<br />\n";
         	    $current_eleve_avis_query = mysql_query($sql_current_eleve_avis);
                $current_eleve_avis = @mysql_result($current_eleve_avis_query, 0, "avis");

				$y_tmp = $pdf->GetY();
				$pdf->Setxy($X_tableau,$y_tmp);
				$pdf->SetFont($caractere_utilise,'B',9);
				//$texte = strtoupper($donnees_eleves['nom'][$nb_eleves_i])." ".ucfirst($donnees_eleves['prenom'][$nb_eleves_i]);
				$texte = strtoupper($donnees_eleves[$nb_eleves_i]['nom'])." ".ucfirst($donnees_eleves[$nb_eleves_i]['prenom']);
				$pdf->CellFitScale($l_cell_nom,$h_cell,$texte,1,0,'L',0); //$l_cell_nom.' - '.$h_cell.' / '.$X_tableau.' - '.$y_tmp
			
				$y_tmp = $pdf->GetY();
				$pdf->Setxy($X_tableau+$l_cell_nom,$y_tmp);
				
				$l_cell_avis=$EspaceX - $l_cell_nom;
				
				if ($current_eleve_avis != '') {
				  $avis =	$current_eleve_avis;
				} else {
				  $avis =' ';
				}
				//$avis = $sql_current_eleve_avis;
				
				$pdf->SetFont($caractere_utilise,'',7.5);
				//$pdf->CellFitScale($l_cell_avis,$h_cell,$avis,1,0,'L',0); //le quadrillage

				$taille_texte_total = $pdf->GetStringWidth($avis);

				$largeur_appreciation2 = $l_cell_avis ;

				$nb_ligne_app = '4.8';
				$taille_texte_max = $nb_ligne_app * ($largeur_appreciation2-4);
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
					if($taille_texte_max < $taille_texte_total)
					{
						$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.3;
						$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_appreciation);
						$taille_texte_total = $pdf->GetStringWidth($avis);
					} else { $grandeur_texte='ok'; }
				}
				$grandeur_texte='test';
				
				$pdf->drawTextBox($avis, $largeur_appreciation2, $h_cell, 'J', 'M', 1);
				
				$pdf->SetFont($caractere_utilise,'',7.5);

				$pdf->Setxy($X_tableau+$l_cell_nom,$y_tmp+$h_cell);
				$nb_eleves_i = $nb_eleves_i + 1;
			}
			$y_tmp = $pdf->GetY();
		} // FOR
		
	// sortie PDF sur �cran
	$nom_releve=date("Ymd_Hi");
	$nom_releve = 'Avis_conseil_'.$nom_releve.'.pdf';
	$pdf->Output($nom_releve,'I');
?>
