<?php

include("includes/config.inc.php");
include("includes/fonctions.inc.php");

if ($_GET) {
extract($_GET);

if ( $tab != 'rss') {

$largeur = 300;  
$hauteur = 180;  
// on dessine une image vide de 100 pixels sur 18  

$im = imagecreate($largeur, $hauteur); 

$blanc = ImageColorAllocate ($im, 255, 255, 255);  
$transp = imagecolortransparent ( $im , $blanc);
$noir = ImageColorAllocate ($im, 0, 0, 0);  
$bleu_fonce = ImageColorAllocate ($im, 75, 130, 195);  
$bleu_intense = ImageColorAllocate ($im, 0, 0, 255); 
$bleu_clair = ImageColorAllocate ($im, 95, 160, 240);  
$jaune = ImageColorAllocate ($im, 20, 150, 20);  
$couleur_fond = ImageColorAllocate ($im, 155, 255, 255); 
$rouge = ImageColorAllocate ($im, 255, 0, 0);
$vert = ImageColorAllocate ($im, 0, 255, 0);

imageRectangle($im,0,0,$largeur,$hauteur,$blanc);
	
	$nb1 = nbRess_scenario($ress);
	$nb2 = nbRess_imposees($ress);
	$nb3 = nbRess_proposees($ress);
	$nb4 = nbRess_utilisateurs($ress);

	$nb = $nb1 + $nb2 + $nb3 + $nb4;

	$eX = 20;
	$left = 100;
	$top = 25;
	$pas = 5;

		

	imagestring($im, 5, $eX, 0, "STATISTIQUES D'UTILISATION", $bleu_intense);
	//imagestring($im, 4, $eX, 20, "$R->titre", $bleu_intense);

	
	imagestring($im, 3, $eX, $top, "Utilis.($nb4)", $jaune);
	imagefilledrectangle($im,$left,$top+2, $left+($nb4*$pas),$top+10,$jaune);

	$top = $top + 15;
	imagestring($im, 3, $eX, $top, "Scenario($nb1)", $bleu_clair);
	imagefilledrectangle($im,$left,$top+2, $left+($nb1*$pas),$top+10,$bleu_clair);


	$top = $top + 15;
	imagestring($im, 3, $eX, 55, "Proposée($nb3)", $bleu_fonce);
	imagefilledrectangle($im,$left,$top+2, $left+($nb3*$pas),$top+10,$bleu_fonce);

	$top = $top + 15;
	imagestring($im, 3, $eX, 70, "Imposée($nb2)", $vert);
	imagefilledrectangle($im,$left,$top+2, $left+($nb2*$pas),$top+10,$vert);

	$top = $top + 15;
	imagestring($im, 3, $eX, 85, "Total($nb)", $rouge);
	imagefilledrectangle($im,$left,$top+2, $left+($nb)*$pas,$top+10,$rouge);



// on sp&eacute;cifie le type de document que l'on va cr&eacute;er (ici une image au format JPEG
header("Content-type: image/png");
// on dessine notre image PNG  
if (!imagepng($im))
	die('Image KO');
}
}
?> 