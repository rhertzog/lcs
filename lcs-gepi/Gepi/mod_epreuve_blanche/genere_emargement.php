<?php
/* $Id: genere_emargement.php 6074 2010-12-08 15:43:17Z crob $ */
/*
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

//$variables_non_protegees = 'yes';

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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/genere_emargement.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/genere_emargement.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: G�n�ration �margement',
statut='';";
$insert=mysql_query($sql);
}

//======================================================================================
// Section checkAccess() � d�commenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$id_epreuve=isset($_POST['id_epreuve']) ? $_POST['id_epreuve'] : (isset($_GET['id_epreuve']) ? $_GET['id_epreuve'] : NULL);
$imprime=isset($_POST['imprime']) ? $_POST['imprime'] : (isset($_GET['imprime']) ? $_GET['imprime'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

include('lib_eb.php');

if(isset($imprime)) {
	check_token();

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg="L'�preuve n�$id_epreuve n'existe pas.";
	}
	else {
		$lig_ep=mysql_fetch_object($res);
		$intitule_epreuve=$lig_ep->intitule;
		$date_epreuve=formate_date("$lig_ep->date");
	
		$sql="SELECT * FROM eb_salles WHERE id_epreuve='$id_epreuve' ORDER BY salle;";
		$res_salle=mysql_query($sql);
		while($lig_salle=mysql_fetch_object($res_salle)) {
			$salle[]=$lig_salle->salle;
			$id_salle[]=$lig_salle->id;
		}
	
		if($mode=='csv') {
			$csv="";
			for($i=0;$i<count($id_salle);$i++) {
				//$sql="SELECT e.nom, e.prenom, e.login, ec.n_anonymat FROM eb_copies ec, eleves e WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' ORDER BY e.nom,e.prenom;";
				$sql="SELECT DISTINCT e.nom, e.prenom, e.login, e.naissance, c.classe, ec.n_anonymat FROM j_eleves_classes jec, eb_copies ec, eleves e, classes c WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' AND jec.id_classe=c.id AND jec.login=e.login ORDER BY e.nom, e.prenom;";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					//$csv.="Epreuve:;$intitule_epreuve ($date_epreuve);\n";
					$csv.="Epreuve:;$intitule_epreuve;\n";
					$csv.="Date:;$date_epreuve;\n";
					$csv.="Liste d'�margement;Salle $salle[$i];\n";
					
					switch ($imprime) {
						case "sans_num_anonymat":
							$csv.="Nom;Pr�nom;Signature;\n";
						break;
						case "avec_num_anonymat":
							$csv.="Nom;Pr�nom;Num�ro anonymat;Signature\n";
						break;
						case "tout":
							$csv.="Nom;Pr�nom;Classe;Date_naissance;Num�ro anonymat;Signature\n";
						break;
					}
					
					while($lig=mysql_fetch_object($res)) {
						
						switch ($imprime) {
							case "sans_num_anonymat":
								// PROBLEME: ON PEUT AVOIR DES HOMONYMES DANS UNE M�ME SALLE...
								$csv.=casse_mot($lig->nom).";".casse_mot($lig->prenom,'majf2').";;\n";
							break;
							case "avec_num_anonymat":
								$csv.=casse_mot($lig->nom).";".casse_mot($lig->prenom,'majf2').";$lig->n_anonymat;\n";
							break;
							case "tout":
								$csv.=casse_mot($lig->nom).";".casse_mot($lig->prenom,'majf2').";$lig->classe;"."$lig->naissance;"."$lig->n_anonymat;\n";
							break;
						}
					}
				}
			}
			$nom_fic="emargement_epreuve_$id_epreuve.csv";
	
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
			header('Content-Type: text/x-csv');
			header('Expires: ' . $now);
			// lem9 & loic1: IE need specific headers
			if (ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
				header('Content-Disposition: inline; filename="' . $nom_fic . '"');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			}
			else {
				header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
				header('Pragma: no-cache');
			}
	
			echo $csv;
			die();
	
		}
		elseif($mode=='pdf') {

			header('Content-Type: application/pdf');
			Header('Pragma: public');

			require('../fpdf/fpdf.php');
			require('../fpdf/ex_fpdf.php');
			
			define('FPDF_FONTPATH','../fpdf/font/');
			define('LargeurPage','210');
			define('HauteurPage','297');

			$largeur_page=210;

			session_cache_limiter('private');

			$MargeHaut=10;
			$MargeDroite=10;
			$MargeGauche=10;
			$MargeBas=10;

			class rel_PDF extends FPDF
			{
				function Footer()
				{
					global $intitule_epreuve;
					global $date_epreuve;
					global $salle_courante;
					//global $num_page;
					//global $decompte_page;

					$this->SetXY(5,287);
					$this->SetFont('arial','',7.5);

					//$texte=getSettingValue("gepiSchoolName")."  ";
					$texte=$intitule_epreuve." ($date_epreuve) - ".$salle_courante;
					$lg_text=$this->GetStringWidth($texte);
					$this->SetXY(10,287);
					$this->Cell(0,5,$texte,0,0,'L');

					//$this->SetY(287);
					$this->Cell(0,5,'Page '.$this->PageNo(),"0",1,'C');
					//$this->Cell(0,5,'Page '.($this->PageNo()-$decompte_page),"0",1,'C');
					//$this->Cell(0,5,'Page '.$this->PageNo().'-'.$decompte_page.'='.($this->PageNo()-$decompte_page),"0",1,'C');
					//$this->Cell(0,5,'Page '.$num_page,"0",1,'C');

					// Je ne parviens pas � faire reprendre la num�rotation � 1 lors d'un changement de salle
				}

				function EnteteEmargement()
				{
					global $intitule_epreuve;
					global $date_epreuve;
					global $salle_courante;
					global $fonte, $MargeDroite, $largeur_page, $MargeGauche, $sc_interligne, $salle, $i;
					//global $num_page;
					//global $decompte_page;

					$this->SetFont($fonte,'B',14);
					$this->Setxy(10,10);
					$this->Cell($largeur_page-$MargeDroite-$MargeGauche,20,getSettingValue('gepiSchoolName').' - Ann�e scolaire '.getSettingValue('gepiYear'),'LRBT',1,'C');

					$x1=$this->GetX();
					$y1=$this->GetY();

					$this->SetFont($fonte,'B',12);
					$texte='Epreuve : ';
					$largeur_tmp=$this->GetStringWidth($texte);
					$this->Cell($largeur_tmp,$this->FontSize*$sc_interligne,$texte,'',0,'L');
					$this->SetFont($fonte,'',12);
					$texte=$intitule_epreuve;
					$this->Cell($this->GetStringWidth($texte),$this->FontSize*$sc_interligne,$texte,'',1,'L');

					$this->SetFont($fonte,'B',12);
					$texte='Date : ';
					$this->Cell($largeur_tmp,$this->FontSize*$sc_interligne,$texte,'',0,'L');
					$this->SetFont($fonte,'',12);
					$texte=$date_epreuve;
					$this->Cell($this->GetStringWidth($texte),$this->FontSize*$sc_interligne,$texte,'',1,'L');

					//$x2=$this->GetX();
					$y2=$this->GetY();

					$this->SetFont($fonte,'B',12);
					$texte="Salle $salle[$i]";
					$larg_tmp=$sc_interligne*($this->GetStringWidth($texte));
					$this->SetXY($largeur_page-$larg_tmp-$MargeDroite,$y1+($y2-$y1)/4);
					$this->Cell($larg_tmp,$this->FontSize*$sc_interligne,$texte,'LRBT',1,'C');
				}
			}

			// D�finition de la page
			$pdf=new rel_PDF("P","mm","A4");
			//$pdf=new FPDF("P","mm","A4");
			$pdf->SetTopMargin($MargeHaut);
			$pdf->SetRightMargin($MargeDroite);
			$pdf->SetLeftMargin($MargeGauche);
			//$pdf->SetAutoPageBreak(true, $MargeBas);

			// Couleur des traits
			$pdf->SetDrawColor(0,0,0);
			$pdf->SetLineWidth(0.2);

			$fonte='arial';
			$sc_interligne=1.3;

			$num_page=0;

			$compteur=0;
			for($i=0;$i<count($id_salle);$i++) {
				$decompte_page=$num_page;

				$sql="SELECT e.nom, e.prenom, e.login, ec.n_anonymat FROM eb_copies ec, eleves e WHERE e.login=ec.login_ele AND ec.id_salle='$id_salle[$i]' AND ec.id_epreuve='$id_epreuve' ORDER BY e.nom,e.prenom;";
				//echo "$sql<br />";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {

					//if($compteur>0) {$pdf->Footer();}
					$num_page++;
					$pdf->AddPage("P");
					$salle_courante=$salle[$i];

					// Initialisation:
					$x1="";
					$y1="";
					$y2="";

					$pdf->EnteteEmargement();
/*
					//Ent�te du PDF
					//$pdf->SetLineWidth(0.7);
					$pdf->SetFont($fonte,'B',14);
					$pdf->Setxy(10,10);
					$pdf->Cell($largeur_page-$MargeDroite-$MargeGauche,20,getSettingValue('gepiSchoolName').' - Ann�e scolaire '.getSettingValue('gepiYear'),'LRBT',1,'C');

					$x1=$pdf->GetX();
					$y1=$pdf->GetY();

					$pdf->SetFont($fonte,'B',12);
					$texte='Epreuve : ';
					$largeur_tmp=$pdf->GetStringWidth($texte);
					$pdf->Cell($largeur_tmp,$pdf->FontSize*$sc_interligne,$texte,'',0,'L');
					$pdf->SetFont($fonte,'',12);
					$texte=$intitule_epreuve;
					$pdf->Cell($pdf->GetStringWidth($texte),$pdf->FontSize*$sc_interligne,$texte,'',1,'L');

					$pdf->SetFont($fonte,'B',12);
					$texte='Date : ';
					$pdf->Cell($largeur_tmp,$pdf->FontSize*$sc_interligne,$texte,'',0,'L');
					$pdf->SetFont($fonte,'',12);
					$texte=$date_epreuve;
					$pdf->Cell($pdf->GetStringWidth($texte),$pdf->FontSize*$sc_interligne,$texte,'',1,'L');

					//$x2=$pdf->GetX();
					$y2=$pdf->GetY();

					$pdf->SetFont($fonte,'B',12);
					$texte="Salle $salle[$i]";
					$larg_tmp=$sc_interligne*($pdf->GetStringWidth($texte));
					$pdf->SetXY($largeur_page-$larg_tmp-$MargeDroite,$y1+($y2-$y1)/4);
					$pdf->Cell($larg_tmp,$pdf->FontSize*$sc_interligne,$texte,'LRBT',1,'C');
*/

					$x1=10;
					$y1=30;
					$y2=41;

					$pdf->SetXY($x1,$y2);

					/*
					$x=$pdf->GetX();
					$y=$pdf->GetY();
					$pdf->Cell($largeur_page-$MargeDroite-$MargeGauche,10,'','LRBT',0,'L');
					$pdf->SetXY($x,$y);
					*/

					$pdf->SetFont($fonte,'B',10);
					$tab_nom=array();
					$tab_n_anonymat=array();
					$cpt=0;
					$larg_max=0;
					while($lig=mysql_fetch_object($res)) {
						$tab_nom[$cpt]=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						$tab_n_anonymat[$cpt]=$lig->n_anonymat;

						$larg_tmp=$pdf->GetStringWidth($tab_nom[$cpt]);
						if($larg_tmp>$larg_max) {$larg_max=$larg_tmp;}
						$cpt++;
					}

					//$pdf->SetFont($fonte,'B',10);
					$texte='Nom pr�nom';
					//$larg_col1=$pdf->GetStringWidth($texte);
					$larg_col1=$larg_max+4;
					$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
					$larg_col2=0;
					if($imprime=='avec_num_anonymat') {
						$texte='Num.anonymat';
						$larg_col2=$pdf->GetStringWidth($texte)+4;
						$pdf->Cell($larg_col2,10,$texte,'LRBT',0,'C');
					}
					$texte='Signature';
					$larg_col3=$largeur_page-$MargeDroite-$MargeGauche-$larg_col1-$larg_col2;
					$pdf->Cell($larg_col3,10,$texte,'LRBT',1,'C');

					$pdf->SetFont($fonte,'B',10);
					/*
					while($lig=mysql_fetch_object($res)) {
						$texte=casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
						if($imprime=='avec_num_anonymat') {
							$texte=$lig->n_anonymat;
							$pdf->Cell($larg_col2,10,$texte,'LRBT',0,'C');
						}
						$pdf->Cell($larg_col3,10,'','LRBT',1,'C');
					}
					*/
					for($j=0;$j<count($tab_nom);$j++) {
						if($pdf->GetY()>270) {
							$pdf->AddPage("P");
							$pdf->EnteteEmargement();
							$pdf->SetXY($x1,$y2);

							$texte='Nom pr�nom';
							$larg_col1=$larg_max+4;
							$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
							$larg_col2=0;
							if($imprime=='avec_num_anonymat') {
								$texte='Num.anonymat';
								$larg_col2=$pdf->GetStringWidth($texte)+4;
								$pdf->Cell($larg_col2,10,$texte,'LRBT',0,'C');
							}
							$texte='Signature';
							$larg_col3=$largeur_page-$MargeDroite-$MargeGauche-$larg_col1-$larg_col2;
							$pdf->Cell($larg_col3,10,$texte,'LRBT',1,'C');

						}

						$pdf->SetFont($fonte,'B',10);

						$largeur_dispo=$larg_col1;
						$h_cell=10;
						$hauteur_max_font=10;
						$hauteur_min_font=4;
						$bordure='LRBT';
						$v_align='C';
						$align='L';

						$texte=$tab_nom[$j];
						//$pdf->Cell($larg_col1,10,$texte,'LRBT',0,'C');
						$x=$pdf->GetX();
						$y=$pdf->GetY();
						cell_ajustee($texte,$x,$y,$largeur_dispo,$h_cell,$hauteur_max_font,$hauteur_min_font,$bordure,$v_align,$align);
						$pdf->SetXY($x+$largeur_dispo,$y);

						if($imprime=='avec_num_anonymat') {
							$texte=$tab_n_anonymat[$j];
							$pdf->Cell($larg_col2,10,$texte,'LRBT',0,'C');
						}
						$pdf->Cell($larg_col3,10,'','LRBT',1,'C');
					}

					$compteur++;
				}
			}

			//$pdf->Footer();

			$date=date("Ymd_Hi");
			$nom_fich='Emargement_'.$id_epreuve.'_'.$date.'.pdf';
			header('Content-Type: application/pdf');
			$pdf->Output($nom_fich,'I');
			die();

		}
	}
}


//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Emargement";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='index.php?id_epreuve=$id_epreuve&amp;mode=modif_epreuve'>Retour</a>";
//echo "</p>\n";
//echo "</div>\n";

if(!isset($imprime)) {
	echo "</p>\n";

	// G�n�rer des fiches par salles

	echo "<p class='bold'>Epreuve n�$id_epreuve</p>\n";
	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>L'�preuve choisie (<i>$id_epreuve</i>) n'existe pas.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	
	$lig=mysql_fetch_object($res);
	echo "<blockquote>\n";
	echo "<p><b>".$lig->intitule."</b> (<i>".formate_date($lig->date)."</i>)<br />\n";
	if($lig->description!='') {
		echo nl2br(trim($lig->description))."<br />\n";
	}
	else {
		echo "Pas de description saisie.<br />\n";
	}
	echo "</blockquote>\n";

	//========================================================
	$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve';";
	$test1=mysql_query($sql);
	
	$sql="SELECT DISTINCT n_anonymat FROM eb_copies WHERE id_epreuve='$id_epreuve';";
	$test2=mysql_query($sql);
	if(mysql_num_rows($test1)!=mysql_num_rows($test2)) {
		echo "<p style='color:red;'>Les num�ros anonymats ne sont pas uniques sur l'�preuve (<i>cela ne devrait pas arriver</i>).</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT login_ele FROM eb_copies WHERE n_anonymat='' AND id_epreuve='$id_epreuve';";
	$test3=mysql_query($sql);
	if(mysql_num_rows($test3)>0) {
		echo "<p style='color:red;'>Un ou des num�ros anonymats ne sont pas valides sur l'�preuve&nbsp;: ";
		$cpt=0;
		while($lig=mysql_fetch_object($test3)) {
			if($cpt>0) {echo ", ";}
			echo get_nom_prenom_eleve($lig->login_ele);
			$cpt++;
		}
		echo "<br />Cela ne devrait pas arriver.<br />La saisie n'est pas possible.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	//========================================================

	//========================================================
	//echo "<p style='color:red;'>A FAIRE&nbsp;: Contr�ler si certains �l�ves n'ont pas �t� affect�s dans des salles.</p>\n";
	$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND id_salle='-1';";
	//echo "$sql<br />";
	$test=mysql_query($sql);
	$nb_tmp=mysql_num_rows($test);
	if($nb_tmp==1) {
		echo "<p style='color:red;'>$nb_tmp �l�ve n'est pas affect� dans une salle.</p>\n";
	}
	elseif($nb_tmp>1) {
		echo "<p style='color:red;'>$nb_tmp �l�ves n'ont pas �t� affect�s dans des salles.</p>\n";
	}
	//========================================================

	echo "<p>Choisissez le type de liste � imprimer&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li><b>CSV</b>&nbsp;:\n";
	 	echo "<ul>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=sans_num_anonymat&amp;mode=csv".add_token_in_url()."'>Avec les colonnes 'NOM;PRENOM;SIGNATURE'</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=avec_num_anonymat&amp;mode=csv".add_token_in_url()."'>Avec les colonnes 'NOM;PRENOM;NUM_ANONYMAT;SIGNATURE'</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=tout&amp;mode=csv".add_token_in_url()."'>Avec les colonnes 'NOM;PRENOM;CLASSE;DATE_DE_NAISSANCE;NUM_ANONYMAT;SIGNATURE'</a></li>\n";
		echo "</ul>\n";
	echo "</li>\n";
	echo "<li><b>PDF</b>&nbsp;:\n";
	 	echo "<ul>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=sans_num_anonymat&amp;mode=pdf".add_token_in_url()."'>Avec les colonnes 'NOM_PRENOM;SIGNATURE'</a></li>\n";
		echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;imprime=avec_num_anonymat&amp;mode=pdf".add_token_in_url()."'>Avec les colonnes 'NOM_PRENOM;NUM_ANONYMAT;SIGNATURE'</a></li>\n";
		echo "</ul>\n";
	echo "</li>\n";
	echo "</ul>\n";
}

require("../lib/footer.inc.php");
?>
