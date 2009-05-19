<?php
/* ==========================================================
   Projet LCS : Linux Communication Server
   Plugin "Biilan2 : Gestion administrative du B2i"
   par Jean-Louis ROSSIGNOL <jean-louis.rossignol@ac-caen.fr>
   et Gilles HILAIRE <gilles.hilaire@ac-caen.fr>   
   ========================================================== */

include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

require('fpdf.php');
require ("config.php");
require ("config2.php");


list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y")
{die("<div class=\"remarque\">Page réservée à l'administrateur ...<br><br></div>");}


$db=@mysql_connect($dbserver , $dbuser, $dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

$date0=date("j");
$date1=date("Y");
$date2=date("m");
$date=("$date0 / $date2 / $date1");

function resultat($a,$b)
{$c = ($a + $b);
return $c;}

if ( $date2 < "08")
{
$an2=$date1;
$x="-1";
$an1=resultat($date1,$x);
}
else
{
$an1= $date1;
$x="1";
$an2=resultat($date1,$x);
}
$anscol=("$an1 - $an2");

if ($etab=="College") {$etablissement="collège"; $logo="logoclg.jpg";}
if ($etab=="Lycee") {$etablissement="lycée"; $logo="logolyc.jpg";}
if ($etab=="Ecole") {$etablissement="école"; $logo="logoeco.jpg";}

//on vérifie avec empty si les champs sont vides

if(empty($_POST["sel"]))				{die ("
				<html>
				<head>
				<title>Gestion Administrative du B2I</title>
				<link rel=\"stylesheet\" href=\"Style/style.css\">
				</head>
				<body>
				<div class=\"texte\"><h1>Biilan</h1></div>
				<div class=avertissement><br><br>Veuillez choisir un élève<br><br><br><br><br></div>
				</body>
				</html>
				");
	}
else
  {
   
  
$pdf=new FPDF('L','mm','A5');
$pdf->Open();
 	foreach ( $sel as $id ) 		{
		list($user, $groups)=people_get_variables($id, true);
		$eleve="$user[fullname]";       // on stocke le nom de l'eleve dans la variable $eleve
				list($user, $groups)=people_get_variables($id, true);
		if ( count($groups) )
			{for ($loop=0; $loop < count ($groups) ; $loop++)
				{if ( ereg("^Classe", $groups[$loop]["cn"]) )
					{$classe="".$groups[$loop]["cn"].""; 
			}
				}
					}   // on stocke la classe de l'élève dans la variable $classe
		$pdf->AddPage();

		$pdf->Image("Images/$logo",10, 10);
		$pdf->Image("Images/logo2.jpg",10, 105);

		$pdf->SetFont('Arial','I',10);
		$pdf->Text(150,55,"Année scolaire $anscol");

		$pdf->SetFont('Arial','',14);

		$pdf->Text(35,65,"Le Brevet informatique et internet niveau $etablissement est délivré à :");

		$pdf->SetFont('Arial','B',16);
		$pdf->Text(20,80,"$eleve");

		$pdf->SetFont('Arial','',14);
		$pdf->Text(20,90," élève de la classe $classe");

		$pdf->SetFont('Arial','',7);
		$pdf->Text(35,100,"conformément à l'arrêté du 14 juin 2006 relatif aux référentiels de connaissances et capacités exigibles pour le B2i.");

		$pdf->SetFont('Arial','',8);
		$pdf->Text(40,110,"Cachet de l'établissement");
		$pdf->Text(110,110,"Le Chef d'établissement");
		$pdf->Text(170,110,"L'élève");
		$pdf->Text(40,135,"Fait le $date");
		
		}
		$pdf->Output();
	}
?>
