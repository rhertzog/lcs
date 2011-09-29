<?php
/*
 * $Id: imprime_pdf.php 8061 2011-08-30 22:01:10Z jjacquard $
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

define('FPDF_FONTPATH','../fpdf/font/');
define('TopMargin','15');
define('RightMargin','15');
define('LeftMargin','15');
define('BottomMargin','15');
define('LargeurPage','210');

//=============================
// Initialisations files
require_once("../lib/initialisations.inc.php");
//=============================

if (!defined('FPDF_VERSION')) {
	require_once('../fpdf/fpdf.php');
}
//require('../fpdf/fpdf15.php');

// Il faut r�cup�rer l'info sur le mode avant l'appel � ex_fpdf.php pour que les accents de l'ent�te soient corrects
$mode_utf8_pdf=getSettingValue("mode_utf8_visu_notes_pdf");
if($mode_utf8_pdf=="") {$mode_utf8_pdf="n";}
require('../fpdf/ex_fpdf.php');

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
}


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$pdf=new PDF("P","mm","A4");
$pdf->SetTopMargin(TopMargin);
$pdf->SetRightMargin(RightMargin);
$pdf->SetLeftMargin(LeftMargin);
$pdf->SetAutoPageBreak(true, BottomMargin);
// Couleur des traits
$pdf->SetDrawColor(0,0,0);
$pdf->AddFont('Alakob','','Alakob.php');

// Pour les tests : permet de voir les bords des cadres
$bord = 0;
// on appelle une nouvelle page pdf
$pdf->AddPage("P");
$pdf->SetFontSize(10);
$text_classe_matiere = '';
if (isset($_GET['id_groupe'])) {
	$current_group = get_group((int)$_GET['id_groupe']);
    $text_classe_matiere = $current_group["description"];
    $text_classe_matiere .= " - Classe";
	if (count($current_group["classes"]["list"]) > 1) $text_classe_matiere .= "s";
	$text_classe_matiere .= " : ".$current_group["classlist_string"];
    if (isset($_GET['periode_num'])) {
        $text_classe_matiere .= " - P�riode : ".sql_query1("SELECT nom_periode FROM periodes WHERE
        (
        id_classe='".$current_group["classes"]["list"][0]."' and
        num_periode='".(int)$_GET['periode_num']."'
        )");
    }
}

//=========================================================
/*
$mode_utf8_pdf=getSettingValue("mode_utf8_visu_notes_pdf");
if($mode_utf8_pdf=="") {$mode_utf8_pdf="n";}
$mode_utf8_pdf="y";
*/

//debug_var();
/*
function traite_accents_utf8($chaine) {
	global $mode_utf8_pdf;
	if($mode_utf8_pdf=="y") {
		return utf8_encode($chaine);
	}
	else {
		return $chaine;
	}
}
*/
//=========================================================

//if ($text_classe_matiere != '') $pdf->Cell(100, 8, $text_classe_matiere,$bord,0,"L",0);
if ($text_classe_matiere != '') $pdf->Cell(100, 8, traite_accents_utf8($text_classe_matiere),$bord,0,"L",0);
$pdf->ln();


//isset($_GET['titre']) ? $titre = unslashes($_GET['titre']) : $titre='' ;
isset($_GET['titre']) ? $titre = traite_accents_utf8(unslashes($_GET['titre'])) : $titre='' ;
if ($titre!='') {
    //Positionnement du titre
    $w=$pdf->GetStringWidth($titre)+6;
    $pdf->SetX((LargeurPage-$w)/2);
    //Couleurs du cadre, du fond et du texte
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0,0,0);
    //Titre centr�
    $pdf->Cell($w,9,$titre,$bord,1,'C',0);
    //Saut de ligne
}

// tableau des en-t�tes
$header1 = array();
$header1 = unserialize($_SESSION['header_pdf']);

// tableau des largeurs
$w1 = array();
$w1 = unserialize($_SESSION['w_pdf']);

// tableau des donn�es
$data1 = array();
//$data1 = unserialize($_SESSION['data_pdf']);
$data1 = unserialize($_SESSION['data_pdf']);

/*
foreach($data1 as $key => $value) {
	$data1[$key]=traite_accents_utf8($data1[$value]);
}
*/

$pdf->SetFont('Arial','',8);
$pdf->FancyTable($w1,$header1,$data1,"v");

//debug_var();

//if((!isset($_GET['id_groupe']))||(!isset($_GET['nom_pdf_en_detail']))){
if(!isset($_GET['id_groupe'])) {
	send_file_download_headers('application/pdf','document.pdf');
	$pdf->Output();
}
elseif(!isset($_GET['nom_pdf_en_detail'])) {
	$ident_plus="";

	$ident_plus .= date("Ymd");
	$ident_plus = preg_replace("/[^A-Za-z0-9]/","_",$current_group["classlist_string"].'_'.$current_group["description"].'_'.$ident_plus);
	$ident_plus=str_replace(" ", "_", $ident_plus);

	send_file_download_headers('application/pdf',$ident_plus.'.pdf');

	$pdf->Output($ident_plus.'.pdf','I');
	$pdf->closeParsers();
}
else{
	//$ident_plus = date("Ymd");
	$ident_plus="";
	if(isset($_GET['periode_num'])) {
		$ident_plus .= "Periode_".$_GET['periode_num']."_";
	}
	$ident_plus .= date("Ymd");
	$ident_plus = preg_replace("/[^A-Za-z0-9]/","_",$current_group["classlist_string"].'_'.$current_group["description"].'_'.$ident_plus);
	$ident_plus=str_replace(" ", "_", $ident_plus);

	send_file_download_headers('application/pdf',$ident_plus.'.pdf');

	$pdf->Output($ident_plus.'.pdf','I');
	$pdf->closeParsers();
}
?>
