<?php

/**
 *
 *
 * @version $Id: gepi_pdf.class.php 3091 2009-05-09 14:09:48Z crob $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, St�phane Boireau, Christian Chapel
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

class bul_PDF extends FPDF_MULTICELLTAG {

	/**
	* Draws text within a box defined by width = w, height = h, and aligns
	* the text vertically within the box ($valign = M/B/T for middle, bottom, or top)
	* Also, aligns the text horizontally ($align = L/C/R/J for left, centered, right or justified)
	* drawTextBox uses drawRows
	*
	* This function is provided by TUFaT.com
	*/

	function drawTextBox($strText, $w, $h, $align='L', $valign='T', $border=1) {
		$xi=$this->GetX();
		$yi=$this->GetY();

		$hrow=$this->FontSize;
		$textrows=$this->drawRows($w,$hrow,$strText,0,$align,0,0,0);
		$maxrows=floor($h/$this->FontSize);
		$rows=min($textrows,$maxrows);

		if ($border==1)
			$this->Rect($xi,$yi,$w,$h,'D');
		if ($border==2)
			$this->Rect($xi,$yi,$w,$h,'DF');

		$dy=0;
		if (strtoupper($valign)=='M')
			$dy=($h-$rows*$this->FontSize)/2;
		if (strtoupper($valign)=='B')
			$dy=$h-$rows*$this->FontSize;

		$this->SetY($yi+$dy);
		$this->SetX($xi);

		$this->drawRows($w,$hrow,$strText,0,$align,0,$rows,1);

	} //function drawTextBox

	function drawRows($w,$h,$txt,$border=0,$align='J',$fill=0,$maxline=0,$prn=0) {

		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$b=0;
		if($border){
			if($border==1)
			{
				$border='LTRB';
				$b='LRT';
				$b2='LR';
			}else{
				$b2='';
				if(is_int(strpos($border,'L')))
					$b2.='L';
				if(is_int(strpos($border,'R')))
					$b2.='R';
				$b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
			}
		}
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$ns=0;
		$nl=1;
		while($i<$nb){

			//Get next character
			$c=$s[$i];
			if($c=="\n"){
				//Explicit line break
				if($this->ws>0)
				{
					$this->ws=0;
					if ($prn==1)
						$this->_out('0 Tw');
				}
				if ($prn==1) {
					$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
				}
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$ns=0;
				$nl++;
				if($border and $nl==2)
					$b=$b2;
				if ( $maxline && $nl > $maxline )
					return substr($s,$i);
				continue;
			}
			if($c==' ')
			{
				$sep=$i;
				$ls=$l;
				$ns++;
			}
			$l+=$cw[$c];
			if($l>$wmax)
			{
				//Automatic line break
				if($sep==-1)
				{
					if($i==$j)
						$i++;
					if($this->ws>0)
					{
						$this->ws=0;
						if ($prn==1) $this->_out('0 Tw');
					}
					if ($prn==1) {
						$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
					}
				}else{
					if($align=='J')
					{
						$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
						if ($prn==1) $this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
					}
					if ($prn==1){
						$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
					}
					$i=$sep+1;
				}
				$sep=-1;
				$j=$i;
				$l=0;
				$ns=0;
				$nl++;
				if($border and $nl==2)
					$b=$b2;
				if ( $maxline && $nl > $maxline )
					return substr($s,$i);
			}
			else
				$i++;
		}
		//Last chunk
		if($this->ws>0)
		{
			$this->ws=0;
			if ($prn==1) $this->_out('0 Tw');
		}
		if($border and is_int(strpos($border,'B')))
			$b.='B';
		if ($prn==1) {
			$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
		}
		$this->x=$this->lMargin;
		return $nl;
	} //function drawRows

	function TextWithDirection($x,$y,$txt,$direction='R'){

		$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
		if ($direction=='R')
			$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$txt);
		elseif ($direction=='L')
			$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$txt);
		elseif ($direction=='U')
			$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
		elseif ($direction=='D')
			$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
		else
			$s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$txt);
		if ($this->ColorFlag)
			$s='q '.$this->TextColor.' '.$s.' Q';
		$this->_out($s);
	} // function TextWithDirection

	function TextWithRotation($x,$y,$txt,$txt_angle,$font_angle=0){

		$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));

		$font_angle+=90+$txt_angle;
		$txt_angle*=M_PI/180;
		$font_angle*=M_PI/180;

		$txt_dx=cos($txt_angle);
		$txt_dy=sin($txt_angle);
		$font_dx=cos($font_angle);
		$font_dy=sin($font_angle);

		$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
			$txt_dx,$txt_dy,$font_dx,$font_dy,
			$x*$this->k,($this->h-$y)*$this->k,$txt);
		if ($this->ColorFlag)
			$s='q '.$this->TextColor.' '.$s.' Q';
		$this->_out($s);
	} // function TextWithRotation

	// fonction graphique de niveau
	function DiagBarre($X_placement, $Y_placement, $L_diagramme, $H_diagramme, $data, $place)
	{
		$this->SetFont('Courier', '', 10);
		//encadrement g�n�ral
		$this->Rect($X_placement, $Y_placement, $L_diagramme, $H_diagramme, 'D');
		//encadrement du diagramme
		$this->SetDrawColor(180);
		$X_placement_diagramme = $X_placement+0.5;
		$Y_placement_diagramme = $Y_placement+0.5;
		$L_diagramme_affiche = $L_diagramme-1;
		$H_diagramme_affiche = $H_diagramme-1;
		$this->Rect($X_placement_diagramme, $Y_placement_diagramme, $L_diagramme_affiche, $H_diagramme_affiche, 'D');


		//calcul de la longeur de chaque barre
		$nb_valeur=count($data);
		$L_barre = $L_diagramme_affiche/$nb_valeur;
		// calcul de la somme total des informations
		$total_des_valeur = array_sum($data);

		if ( $total_des_valeur != '0' and $total_des_valeur != '' ) {
			$espace_entre = $H_diagramme_affiche / $total_des_valeur;
		} else {
			$espace_entre = $H_diagramme_affiche;
		}

		for($o=0;$o<$total_des_valeur;$o++)
		{
			$Y_echelle=$Y_placement_diagramme+($espace_entre*$o);
			//echelle
			$this->SetDrawColor(180);
			$this->Line($X_placement_diagramme, $Y_echelle, $X_placement_diagramme+$L_diagramme_affiche, $Y_echelle);
		}

		$i=0;
		foreach($data as $val) {
			//Barre
			if($place===$i) {
				$this->SetFillColor(5);
			} else {
				$this->SetFillColor(240);
			}
			$this->SetDrawColor(0, 0, 0);
			if ( $total_des_valeur != '0' and $total_des_valeur != '' ) {
				$H_barre = ($H_diagramme_affiche*$val)/$total_des_valeur;
			} else {
				$H_barre = ($H_diagramme_affiche*$val);
			}
			$Y_barre = ($Y_placement_diagramme+$H_diagramme_affiche) - $H_barre;
			$X_barre = $X_placement_diagramme+($L_barre*$i);
			$this->Rect($X_barre, $Y_barre, $L_barre, $H_barre, 'DF');
			$i++;
		}
	} // function DiagBarre

	//En-t�te du document
	function Header(){

	}

	//Pied de page du document
	function Footer() {
		// On utilise la classe bul_PDF pour les bulletins et pour les relev�s de notes et la formule de bas de page ne doit pas n�cessairement �tre la m�me.
		// Traitement du footer d�plac� dans les pages concern�es.
		/*
		//Positionnement � 1 cm du bas et 0,5cm + 0,5cm du cot� gauche
		$this->SetXY(5,-10);
		//Police Arial Gras 6
		$this->SetFont('Arial','B',8);
		// $fomule = 'Bulletin � conserver pr�cieusement. Aucun duplicata ne sera d�livr�. - GEPI : solution libre de gestion et de suivi des r�sultats scolaires.'
		$fomule = getSettingValue("bull_formule_bas");
		$this->Cell(0,4.5, $fomule,0,0,'C');
		*/
	}

} // class bul_PDF
?>