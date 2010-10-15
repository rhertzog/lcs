<?php
/*
*
* $Id: bilan_absences_quotidien_pdf.php 4098 2010-02-26 18:33:42Z crob $
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

$niveau_arbo = 2;
//================================
// REMONT�: boireaus 20080102
// Initialisations files
require_once('../../lib/initialisations.inc.php');

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un probl�me qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../../logout.php?auto=1");
	die();
}


if (!checkAccess()) {
//	header("Location: ../logout.php?auto=1");
//	die();
}
//================================

//================================
// AJOUT: boireaus 20080102
if(!isset($_SESSION['pdf_debug']))
{

	header('Content-Type: application/pdf');

	// Global configuration file
	// Quand on est en SSL, IE n'arrive pas � ouvrir le PDF.
	//Le probl�me peut �tre r�solu en ajoutant la ligne suivante :
	Header('Pragma: public');

}
else
{

	echo "<p style='color:red'>DEBUG:<br />
	      La g�n�ration du PDF va �chouer parce qu'on affiche ces informations de debuggage,<br />
              mais il se peut que vous ayez des pr�cisions sur ce qui pose probl�me.<br />
              </p>\n";

}
//================================

$mode_utf8_pdf=getSettingValue('mode_utf8_abs_pdf');
if($mode_utf8_pdf!="y") {$mode_utf8_pdf="";}

require('../../fpdf/fpdf.php');

include("../../edt_organisation/fonctions_edt.php");
include("../../edt_organisation/fonctions_calendrier.php");

include("../lib/functions.php");

define('FPDF_FONTPATH','../../fpdf/font/');

// d�finition des marge
	define('TopMargin','5');
	define('RightMargin','2');
	define('LeftMargin','2');
	define('BottomMargin','5');
	define('LargeurPage','210');
	define('HauteurPage','297');


/* ************************* */
/*    fonction sp�cifique    */
/* ************************* */
function redimensionne_image_logo($photo, $L_max, $H_max)
{
	// prendre les informations sur l'image
	$info_image = getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur = $info_image[0];
	$hauteur = $info_image[1];
	// largeur et/ou hauteur maximum � afficher en pixel
	$taille_max_largeur = $L_max;
	$taille_max_hauteur = $H_max;

	// calcule le ratio de redimensionnement
	$ratio_l = $largeur / $taille_max_largeur;
	$ratio_h = $hauteur / $taille_max_hauteur;
	$ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

	// d�finit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur = $largeur / $ratio;
	$nouvelle_hauteur = $hauteur / $ratio;

	// des Pixels vers Millimetres
	$nouvelle_largeur = $nouvelle_largeur / 2.8346;
	$nouvelle_hauteur = $nouvelle_hauteur / 2.8346;

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

/* ************************* */


/* ******************************************** */
/*     initialisation des variable d'entr�      */
/* ******************************************** */

	// si aucune date de demand� alors on met celle du jour au format jj/mm/aaaa
	if (empty($_GET['date_choisie']) and empty($_POST['date_choisie'])) { $date_choisie = date("d/m/Y"); }
	  else { if (isset($_GET['date_choisie'])) { $date_choisie = $_GET['date_choisie']; } if (isset($_POST['date_choisie'])) { $date_choisie = $_POST['date_choisie']; } }
	if (empty($_GET['du']) and empty($_POST['du'])) { $du = ''; }
	  else { if (isset($_GET['du'])) { $du = $_GET['du']; } if (isset($_POST['du'])) { $du = $_POST['du']; } }

	if ( $du != '' ) { $date_choisie = $du; }



/* ******************************************** */

/* *********************************************/
/* information sur la pr�sentation du document */
/* *********************************************/

	$caractere_utilse = 'arial'; // caract�re utilis�
	$affiche_logo_etab = '1';
	$nom_etab_gras = '0';
	$entente_mel = '1'; // afficher l'adresse mel dans l'ent�te
		$courrier_image = '';
		$courrier_texte = '';
	$entente_tel = '1'; // afficher le num�ro de t�l�phone dans l'ent�te
		$tel_image = '';
		$tel_texte = '';
	$entente_fax = '1'; // afficher le num�ro de fax dans l'ent�te
		$fax_image = '';
		$fax_texte = '';
	$L_max_logo = 75; $H_max_logo=75; //dimension du logo
	$centrage_logo = '1'; // centrer le logo de l'�tablissement
	$Y_centre_logo = '18'; // centre du logo sur la page
	$affiche_date_edition = '1'; // affiche la date d'�dition
	$taille_texte_date_edition = '8'; // d�finit la taille de la date d'�dition

	// point de commencement du tableau sur la page
	$x_tab = '5';
	$y_tab = '40';

	// hauteur de l'ent�te
	$hau_entete = '6';

	// largeur de la colonne de l'intitul� de la classe
	$lar_col_classe = '20';

	// largeur de la colonne du nom de l'�l�ve
	$lar_col_eleve = '50';

	// largeur de la colonne du nom des cr�neaux
	$lar_col_creneaux = '130';

	// nombre de ligne � affich� sur 1 page
	$nb_ligne_parpage = '25';

	// largeur total du tableau
	$lar_total_tableau = $lar_col_classe + $lar_col_eleve + $lar_col_creneaux;

	// hauteur de la cellule des donn�es
	$hau_donnee = '5';

	// avec couleur ou sans
	$couleur_fond = '1';


/* *********************************************/


/* ******************************************** */
/*     construction du tableau des donn�es      */
/* ******************************************** */
// chargement des information de la base de donn�es

	// les donn�es concernerons la journ�e du (date au format timestamp)
	$choix_date = explode("/",$date_choisie);
	$date_choisie_ts = mktime(0,0,0, $choix_date[1], $choix_date[0], $choix_date[2]);

	// On r�cup�re le nom des cr�neaux
	$creneaux = retourne_creneaux();

	// on compte le nombre de cr�neaux
	$nb_creneaux = count($creneaux);

	// On d�termine le jour au format texte en Fran�ais actuel
	$jour_choisi = retourneJour(date("w", $date_choisie_ts));

	// on recherche l'horaire d'ouverture et de fermetture de l'�tablissement
	$requete = mysql_query("SELECT ouverture_horaire_etablissement, fermeture_horaire_etablissement
				FROM horaires_etablissement
				WHERE jour_horaire_etablissement = '" . $jour_choisi . "'");
	$nbre_rep = mysql_num_rows($requete);
	if ($nbre_rep >= 1)
	{

		// Avec le r�sultat, on calcule les timestamps UNIX
		$req = mysql_fetch_array($requete);
		$rep_deb = explode(":", $req["ouverture_horaire_etablissement"]);
		$rep_fin = explode(":", $req["fermeture_horaire_etablissement"]);
		$time_actu_deb = mktime($rep_deb[0], $rep_deb[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);
		$time_actu_fin = mktime($rep_fin[0], $rep_fin[1], 0, $choix_date[1], $choix_date[0], $choix_date[2]);

	}
	else
	{

		// Si on ne r�cup�re rien, on donne par d�faut les ts du jour actuel
		$time_actu_deb = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$time_actu_fin = mktime(23, 59, 0, date("m"), date("d"), date("Y"));

	}

	// nous recherchons tout les �l�ves absence le jour choisie
	$requete = "SELECT ar.id, ar.eleve_id, ar.retard_absence, ar.creneau_id, ar.debut_ts, ar.fin_ts, jec.login, jec.id_classe, e.login, e.nom, e.prenom, c.id, c.nom_complet, c.classe
		    FROM " . $prefix_base . "absences_rb ar, " . $prefix_base . "j_eleves_classes jec, " . $prefix_base . "eleves e, " . $prefix_base . "classes c
		    WHERE ( jec.login = ar.eleve_id
		      AND jec.id_classe = c.id
		      AND jec.login = e.login
		      AND eleve_id != 'appel'
		      AND
		      (
		      (
				debut_ts BETWEEN '" . $time_actu_deb . "' AND '" . $time_actu_fin . "'
				AND fin_ts BETWEEN '".$time_actu_deb . "' AND '" . $time_actu_fin . "'
       		  )
       		  OR
       		  (
				'" . $time_actu_deb . "' BETWEEN debut_ts AND fin_ts
				OR '" . $time_actu_fin . "' BETWEEN debut_ts AND fin_ts
       		  )
       		  AND debut_ts != '" . $time_actu_fin . "'
         	  AND fin_ts != '" . $time_actu_deb . "'
         	  )
		    )
		    GROUP BY ar.id
		    ORDER BY c.classe ASC, eleve_id ASC";

	// on ins�re toute les classes dans un tableau
	// initialisation du tableau
	$tab_classe = '';

	// requete de liste des classes pr�sente dans la base de donn�e
	$requete_classes = "SELECT nom_complet, classe
		    			FROM " . $prefix_base . "classes
		    			ORDER BY classe ASC";

	// compteur de classe temporaire
	$cpt_classe = 0;

	$execution_classes = mysql_query($requete_classes) or die('Erreur SQL !'.$requete_classes.'<br />'.mysql_error());
	while ( $donnee_classes = mysql_fetch_array($execution_classes) )
	{

		$tab_classe[$cpt_classe] = $donnee_classes['classe'];

		// incr�mentation du compteur
		$cpt_classe = $cpt_classe + 1;

	}


	// compteur temporaire pour la boucle ci-dessous
	// compteur �l�ve et classe
	$i = 0;
	// compteur des classes pass�
	$ic = 0;

	// nom de l'�l�ve pr�c�dent
	$eleve_precedent = '';
	$classe_precedent = '';

	$execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());

	while ( $donnee = mysql_fetch_array($execution))
	{

		$passe = 0;
		// si l'enregistrement s�lectionner correspond ne correspond pas � la $ic classe
		while ( $tab_classe[$ic] != $donnee['classe'] )
		{

			$tab_donnee[$i]['classe'] = $tab_classe[$ic];
			$tab_donnee[$i]['ident_eleve'] = '';

			// type A ou R -- absence ou retard
			$type = 'entete_classe';

			$i = $i + 1;
			$ic = $ic + 1;
			$passe = 1;

		}

		// si l'�l�ve pr�cedent et diff�rent de celui que nous traiton et que la variable de l'�l�ve pr�c�dent
		// est diff�rent de rien alors on incr�ment notre tableau de donn�e
		if ( $eleve_precedent != $donnee['login'] and $eleve_precedent != '' and $passe != 1)
		{
			$i = $i + 1;
		}

		if ( $eleve_precedent != $donnee['login'] )
		{

			if ( $classe_precedent != $donnee['classe'] )
			{

				// on ins�re les informations sur la classe de l'�l�ve
				$tab_donnee[$i]['classe'] = $donnee['classe'];
				$tab_donnee[$i]['ident_eleve'] = '';

				$classe_precedent = $donnee['classe'];

				// on incr�mente le tableau principal
				$i = $i + 1;

			}

			// on incr�mente le compteur de classe
			//$ic = $ic + 1;

			// nom de l'�l�ve et pr�nom
			$tab_donnee[$i]['classe'] = $donnee['classe'];
			$tab_donnee[$i]['ident_eleve'] = strtoupper($donnee['nom']) . ' ' . ucfirst($donnee['prenom']);

			// type A ou R -- absence ou retard
			$type = $donnee['retard_absence'];

			// fonction qui d�code un timestamps en date et heure un tableau $donnee['heure'] $donnee['date']
			$heure_debut = timestamps_decode($donnee['debut_ts'], 'fr');
				$heure_debut = $heure_debut['heure'];
			$heure_fin = timestamps_decode($donnee['fin_ts'], 'fr');
				$heure_fin = $heure_fin['heure'];

			// fonction permettant de savoir dans quelle p�riode nous nous trouvons par rapport � une heur donn�e
			$periode = '';
			$periode = creneau_absence_du_jour($donnee['login'],$date_choisie,$type);

			// si des p�riode existe
			if ( $periode != '' )
			{

				// appr�s la r�cup�ration des p�riodes sur lesquelle l'absence ce tient on l'explose en talbeau
				$periode_tab = explode(';',$periode);

				// compteur temporaire de p�riode
				$compteur_periode = 0;

				while ( !empty($periode_tab[$compteur_periode]) )
				{

					// nom de la p�riode s�lectionn�
					$periode_select = $periode_tab[$compteur_periode];

					// d�finition des donn�e de $tab_donnee_sup
					$tab_donnee_sup[$i][$periode_select][$type] = '1';

					// compteur des passages
					$compteur_periode = $compteur_periode + 1;

				}

			}

			$eleve_precedent = $donnee['login'];
			$classe_precedent = $donnee['classe'];

		}
		else
		{

			// type A ou R -- absence ou retard
			$type = $donnee['retard_absence'];

			// fonction qui d�code un timestamps en date et heure un tableau $donnee['heure'] $donnee['date']
			$heure_debut = timestamps_decode($donnee['debut_ts'], 'fr');
				$heure_debut = $heure_debut['heure'];
			$heure_fin = timestamps_decode($donnee['fin_ts'], 'fr');
				$heure_fin = $heure_fin['heure'];

			// fonction permettant de savoir dans quelle p�riode nous nous trouvons par rapport � une heur donn�e
			//$periode = periode_active_nom($heure_debut, $heure_fin);
			$periode = '';
			$periode = creneau_absence_du_jour($donnee['login'],$date_choisie,$type);

			// si des p�riode existe
			if ( $periode != '' )
			{

				// appr�s la r�cup�ration des p�riodes sur lesquelle l'absence ce tient on l'explose en talbeau
				$periode_tab = explode(';',$periode);

				// compteur temporaire de p�riode
				$compteur_periode = 0;

				while ( !empty($periode_tab[$compteur_periode]) )
				{

					// nom de la p�riode s�lectionn�
					$periode_select = $periode_tab[$compteur_periode];

					// d�finition des donn�e de $tab_donnee_sup
					$tab_donnee_sup[$i][$periode_select][$type] = '1';

					// compteur des passages
					$compteur_periode = $compteur_periode + 1;

				}

			}

		}

	}

		// si l'enregistrement s�lectionner correspond ne correspond pas � la $ic classe
		$ic = $ic + 1;

		// on fait une boucle s'il reste des classes
		while ( !empty($tab_classe[$ic]) )
		{

			// on incr�ment le compteur du tableau g�n�ral
			$i = $i + 1;

			$tab_donnee[$i]['classe'] = $tab_classe[$ic];
			$tab_donnee[$i]['ident_eleve'] = '';

			// type A ou R -- absence ou retard
			$type = 'entete_classe';

			// on incr�mente le compteur de classe
			$ic = $ic + 1;

		}


	// nombre d'entr�e total
	$nb_d_entree_total = $i;

	// si le compteur de classe = 0 alors on lui indique 1
	if ( $ic == 0 )
	{

		$ic = 1;

	};

	// nombre de page � cr�er, arrondit au nombre sup�rieur
	$nb_page_total = ceil( $nb_d_entree_total / $nb_ligne_parpage );


/* ******************************************** */


/* **************************** */
/*     variable invariable      */
/* **************************** */

	$gepiYear = getSettingValue('gepiYear');
	$RneEtablissement = getSettingValue("gepiSchoolRne");
	$annee_scolaire = $gepiYear;
	$datation_fichier = date("Ymd_Hi");

/* **************************** */


// d�finition d'une variable
	$hauteur_pris = 0;

/* d�but de la g�n�ration du fichier PDF */

	//cr�ation du PDF en mode Portrait, unit�e de mesure en mm, de taille A4
	$pdf=new FPDF('p', 'mm', 'A4');

	// compteur pour le nombre d'�l�ve � affich�
	$nb_eleve_aff = 1;

	// si la variable $gepiSchoolName est vide alors on cherche les informations dans la base
	if ( empty($gepiSchoolName) )
	{

		$gepiSchoolName=getSettingValue('gepiSchoolName');

	}

	// cr�ation du document
	$pdf->SetCreator($gepiSchoolName);
	// auteur du document
	$pdf->SetAuthor($gepiSchoolName);
	// mots cl�
	$pdf->SetKeywords('');
	// sujet du document
	$pdf->SetSubject('Bilan journalier des absences');
	// titre du document
	$pdf->SetTitle('Bilan journalier des absences');
	// m�thode d'affichage du document � son ouverture
	$pdf->SetDisplayMode('fullwidth', 'single');
	// compression du document
	$pdf->SetCompression(TRUE);
	// change automatiquement de page � 5mm du bas
	$pdf->SetAutoPageBreak(TRUE, 5);


/* **************************** */
/* d�but de la boucle des pages */

// comptage du nombre de page trait�
$nb_page_traite = 0;

// initialiser la variable compteur de ligne pass� pour le tableau
$nb_ligne_passe = 0;

// initialiser un compteur temporaire autre que i
// il serviras pour savoir � quelle endroit de la liste nous somme rendus
$j = 0;

// boucle des page
while ($nb_page_traite < $nb_page_total)
{

	// ajout de l'initialisation d'une nouvelle page dans le document
	$pdf->AddPage();

	// police de caract�re utilis�
	$pdf->SetFont('Arial');

/* ENTETE - DEBUT */

	//bloc identification etablissement
	$logo = '../../images/'.getSettingValue('logo_etab');
	$format_du_logo = str_replace('.','',strstr(getSettingValue('logo_etab'), '.'));

	if($affiche_logo_etab==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png'))
	{

		$valeur=redimensionne_image_logo($logo, $L_max_logo, $H_max_logo);
		$X_logo = 5;
		$Y_logo = 5;
		$L_logo = $valeur[0];
		$H_logo = $valeur[1];
		$X_etab = $X_logo + $L_logo + 1;
		$Y_etab = $Y_logo;

		if ( !isset($centrage_logo) or empty($centrage_logo) )
		{

			$centrage_logo = '0';

		}
		if ( $centrage_logo === '1' )
		{

			// centrage du logo
			$centre_du_logo = ( $H_logo / 2 );
			$Y_logo = $Y_centre_logo - $centre_du_logo;

		}

		//logo
		$pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);

	}

	//adresse
	if ( !isset($X_etab) or empty($X_etab) )
	{

		$X_etab = '5';
		$Y_etab = '5';

	}
	$pdf->SetXY($X_etab,$Y_etab);
	$pdf->SetFont($caractere_utilse,'',14);
	$gepiSchoolName = getSettingValue('gepiSchoolName');

	// mettre en gras le nom de l'�tablissement si $nom_etab_gras = 1
	if ( $nom_etab_gras === '1' )
	{

		$pdf->SetFont($caractere_utilse,'B',14);

	}
	$pdf->Cell(90,7, traite_accents_utf8($gepiSchoolName),0,2,'');

	$pdf->SetFont($caractere_utilse,'',10);
	$gepiSchoolAdress1 = getSettingValue('gepiSchoolAdress1');

	if ( $gepiSchoolAdress1 != '' )
	{

		$pdf->Cell(90,5, traite_accents_utf8($gepiSchoolAdress1),0,2,'');

	}
	$gepiSchoolAdress2 = getSettingValue('gepiSchoolAdress2');

	if ( $gepiSchoolAdress2 != '' )
	{

		$pdf->Cell(90,5, traite_accents_utf8($gepiSchoolAdress2),0,2,'');

	}

	$gepiSchoolZipCode = getSettingValue('gepiSchoolZipCode');
	$gepiSchoolCity = getSettingValue('gepiSchoolCity');
	$pdf->Cell(90,5, traite_accents_utf8($gepiSchoolZipCode." ".$gepiSchoolCity),0,2,'');
	$gepiSchoolTel = getSettingValue('gepiSchoolTel');
	$gepiSchoolFax = getSettingValue('gepiSchoolFax');

	$passealaligne = '0';
	// ent�te t�l�phone
			// emplacement du cadre t�l�come
			$x_telecom = $pdf->GetX();
			$y_telecom = $pdf->GetY();

	if( $entente_tel==='1' )
	{

		$grandeur = '';
		$text_tel = '';

		if ( $tel_image != '' )
		{

			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../../images/imabulle/'.$tel_image.'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_tel = '      '.$gepiSchoolTel;
			$grandeur = $pdf->GetStringWidth($text_tel);
			$grandeur = $grandeur + 2;

		}
		if ( $tel_texte != '' and $tel_image === '' )
		{

			$text_tel = $tel_texte.''.$gepiSchoolTel;
			$grandeur = $pdf->GetStringWidth($text_tel);

		}

		$pdf->Cell($grandeur,5, $text_tel,0,$passealaligne,'');

	}

	$passealaligne = '2';
	// ent�te fax
	if( $entente_fax==='1' )
	{

		$text_fax = '';

		if ( $fax_image != '' )
		{

			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../../images/imabulle/'.$fax_image.'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_fax = '      '.$gepiSchoolFax;

		}
		if ( $fax_texte != '' and $fax_image === '' )
		{

			$text_fax = $fax_texte.''.$gepiSchoolFax;

		}
		$pdf->Cell(90,5, $text_fax,0,$passealaligne,'');

	}


	if($entente_mel==='1')
	{

		$text_mel = '';
		$y_telecom = $y_telecom + 5;
		$pdf->SetXY($x_telecom,$y_telecom);
		$gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
		$text_mel = $gepiSchoolEmail;

		if ( $courrier_image != '' )
		{

			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../../images/imabulle/'.$courrier_image.'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_mel = '      '.$gepiSchoolEmail;

		}
		if ( $courrier_texte != '' and $courrier_image === '' )
		{

			$text_mel = $courrier_texte.' '.$gepiSchoolEmail;

		}
		$pdf->Cell(90,5, $text_mel,0,2,'');

	}

/* ENTETE - FIN */


/* ENTETE TITRE - DEBUT */

		$pdf->SetFont('Arial','B',18);

		$pdf->SetXY(85, 10);

		$pdf->Cell(120, 6, 'Bilan journalier des absences', 0, 1, 'C');

		$pdf->SetFont('Arial','',12);

		$pdf->SetX(85);
		$pdf->Cell(120, 8, 'du '.$date_choisie, 0, 1, 'C');

		$pdf->SetX(85);
		$pdf->Cell(120, 5, traite_accents_utf8('ann�e'), 0, 1, 'C');

		$pdf->SetX(85);
		$pdf->Cell(120, 5, traite_accents_utf8($annee_scolaire), 0, 1, 'C');


/* ENTETE TITRE - FIN */


/* ENTETE TABLEAU - DEBUT */

	//S�lection de la police
	$pdf->SetFont($caractere_utilse, 'B', 10);

	// placement du point de commencement du tableau
	$pdf->SetXY($x_tab, $y_tab);

	// Cellule ent�te classe
	$pdf->Cell($lar_col_classe, $hau_entete, 'Classe', 1, 0, 'C');

	// Cellule identit�
	$pdf->Cell($lar_col_eleve, $hau_entete, traite_accents_utf8('Nom Pr�nom'), 1, 0, 'C');

	// Cellule cr�neaux
	// un divisie l'espace r�server � la colonne des cr�neaux par le nombre de cr�neaux
	$largeur_1_creneau = $lar_col_creneaux / $nb_creneaux;

	//compteur temporaire pour la boucle ci-dessous
	$i = 0;

	// boucle des cellule de cr�neau
	while ( $nb_creneaux > $i )
	{

		$pdf->Cell($largeur_1_creneau, $hau_entete, $creneaux[$i], 1, 0, 'C');
		$i = $i + 1;

	}

	// variable qui contient le point Y suivant pour la ligne suivante
	$y_dernier = $y_tab + $hau_entete;


/* ENTETE TABLEAU - FIN */



	/* ***************************************** */
	/* d�but de la boucle du tableau des donn�es */

	// initialisation des classe pass�
	$classe_pass = '';

	// initialiser la variable compteur de ligne pour la page actuel
	$nb_ligne_passe_reel = 0;

	// tant qu'on a pas atteind le nombre de ligne maximum par page on fait la boucle
	while ( $nb_ligne_passe_reel <= $nb_ligne_parpage )
	{

	/* TABLEAU DONNEES - DEBUT */

		// s'il reste des donn�es alors on les affiches
		if ( !empty($tab_donnee[$nb_ligne_passe]) )
		{

			// Si c'est un typdes $tab_donnee[$nb_ligne_passe]['ident_eleve'] vide
			// alors on n'affiche l'ent�te de la classe
			if ( $tab_donnee[$nb_ligne_passe]['ident_eleve'] === '' )
			{

				// initialisation du point X et Y de la ligne du nom des classes
				$pdf->SetXY($x_tab, $y_dernier);

				$pdf->Cell($lar_total_tableau, $hau_donnee, traite_accents_utf8($tab_donnee[$nb_ligne_passe]['classe']), 0, 1, '');
				$classe_pass = $tab_donnee[$nb_ligne_passe]['classe'];

				// variable qui contient le point Y suivant pour la ligne suivante
				$y_dernier = $y_dernier + $hau_donnee;

				// on incr�mente le nombre de ligne pass� sur la page
				$nb_ligne_passe_reel = $nb_ligne_passe_reel + 1;

			}
			else
			{

				// initialisation du point X et Y de la ligne des donn�es
				$pdf->SetXY($x_tab, $y_dernier);

				// colonne vide pour le d�calage des classes
				$pdf->Cell($lar_col_classe, $hau_donnee, '', 0, 0, '');

				// colonne du nom et pr�nom de l'�l�ve
				$pdf->Cell($lar_col_eleve, $hau_donnee, traite_accents_utf8($tab_donnee[$nb_ligne_passe]['ident_eleve']), 1, 0, '');

				// variable qui contient le point Y suivant pour la ligne suivante
				$y_dernier = $y_dernier + $hau_donnee;

				// compteur temporaire pour la boucle ci-dessous
				$k = 0;

				// passage des creneaux en revus
				while ( $nb_creneaux > $k )
				{

					// nom du creneau sur lequelle nous travaillons actuellement
					$nom_creneau = $creneaux[$k];

					if ( !empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A']) and $tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A'] === '1' and empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R']))
					{

						// si la couleur � �tait demand� alors on l'initialise
						if ( $couleur_fond === '1' )
						{

							// couleur de caract�re
							$pdf->SetTextColor(255, 0, 0);
							// couleur du fond de cellule
							$pdf->SetFillColor(255, 223, 223);

						}

						// construction de la cellule du tableau
						$pdf->Cell($largeur_1_creneau, $hau_donnee, 'A', 1, 0, 'C', $couleur_fond);

						// remise de la couleur du caract�re � noir
						$pdf->SetTextColor(0, 0, 0);

					}
					elseif ( !empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R']) and $tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R'] === '1'  and empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A']) )
					{

						// si la couleur � �tait demand� alors on l'initialise
						if ( $couleur_fond === '1' )
						{

							// couleur de caract�re
							$pdf->SetTextColor(33, 223, 0);
							// couleur du fond de cellule
							$pdf->SetFillColor(228, 255, 223);

						}

						// construction de la cellule du tableau
						$pdf->Cell($largeur_1_creneau, $hau_donnee, 'R', 1, 0, 'C', $couleur_fond);

						// remise de la couleur du caract�re � noir
						$pdf->SetTextColor(0, 0, 0);

					}
					elseif ( !empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R']) and $tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['R'] === '1'  and !empty($tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A']) and $tab_donnee_sup[$nb_ligne_passe][$nom_creneau]['A'] === '1' )
					{

						// si la couleur � �tait demand� alors on l'initialise
						if ( $couleur_fond === '1' )
						{

							// couleur de caract�re
							$pdf->SetTextColor(255, 0, 0);
							// couleur du fond de cellule
							$pdf->SetFillColor(255, 223, 223);

						}

						// construction de la cellule du tableau pour l'absence
						$pdf->Cell($largeur_1_creneau/2, $hau_donnee, 'A', 1, 0, 'C', $couleur_fond);

						// si la couleur � �tait demand� alors on l'initialise
						if ( $couleur_fond === '1' )
						{

							// couleur de caract�re
							$pdf->SetTextColor(33, 223, 0);
							// couleur du fond de cellule
							$pdf->SetFillColor(228, 255, 223);

						}

						// construction de la cellule du tableau pour le retard
						$pdf->Cell($largeur_1_creneau/2, $hau_donnee, 'R', 1, 0, 'C', $couleur_fond);

						// remise de la couleur du caract�re � noir
						$pdf->SetTextColor(0, 0, 0);

					}
					else
					{

						$pdf->Cell($largeur_1_creneau, $hau_donnee, '', 1, 0, 'C');

					}

					// compteur de passage pour les cr�neaux
					$k = $k + 1;

				}

				// on incr�mente le nombre de ligne pass� sur la page
				$nb_ligne_passe_reel = $nb_ligne_passe_reel + 1;

			}

				// on incr�mente le nombre de ligne trait� dans le tableau des donn�es
				$nb_ligne_passe = $nb_ligne_passe + 1;

		}
		else
		{

			// s'il n'y a plus de donn�e � afficher alors on lui dit que le
			// maximum de ligne � �tait atteint pour qu'il termine la boucle
			$nb_ligne_passe_reel = $nb_ligne_parpage + 1;

		}

	/* TABLEAU DONNEES - FIN */

	}
	/* fin de la boucle du tableau des donn�es */
	/* *************************************** */

/* PIED DE PAGE - DEBUT */

	//Positionnement � 1 cm du bas et 0,5cm + 0,5cm du cot� gauche
	$pdf->SetXY(5,-10);

	//Police Arial Gras 6
	$pdf->SetFont('Arial','B',8);

	// formule du pied de page
	$fomule = 'Bilan journalier du ' . date("d/m/Y H:i:s") . ' - page ' . $nb_page_traite . '/' . $nb_page_total;

	// cellule de pied de page
	$pdf->Cell(0, 4.5, $fomule, 0, 0, 'C');

/* PIED DE PAGE - FIN */

	// on incr�ment le nombre d'entr�e pass�
	$nb_d_entree_passe = $nb_ligne_passe;

	// on incr�ment le nombre de page trait�
	$nb_page_traite = $nb_page_traite + 1;
}
/* fin de la boucle des pages */
/* ************************** */

// fermeture du fichier pdf et lecture dans le navigateur 'nom', 'I/D'

	// g�n�ration du nom du document
	$nom_fichier = 'bilan_journalier_'.$datation_fichier.'.pdf';

	// g�n�ration du document
	$pdf->Output($nom_fichier,'I');

?>
