<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de consultation du planning des devoirs -
			_-=-_
   "Valid XHTML 1.0 Strict"
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
//si la page est appelée par son URL
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
if (isset($_POST['quit']))
	{$clas=$_POST['class'];
	header ("location:cahier_text_eleve.php?div=$clas");
	exit();}
$cren_off=array();	
include ("../Includes/config.inc.php");
include ("../Includes/fonctions.inc.php");
include ("../Includes/creneau.inc.php");


function is_authorized($x) {
$flg="false";
foreach ($_SESSION['saclasse'] as $clé => $valeur)
	  { 
	  if ($valeur==$x) {
	  	$flg="true";
	 	 break;
	  	}
	  }
return $flg;
}



//contrôle des paramètres $_GET
if ((isset($_GET['classe'])) && (isset($_SESSION['saclasse']))) {	
	if (is_authorized($_GET['classe'])=="false")   exit;
	}
//mémorisation des paramètres POST classe et matière renvoyés par le formulaire	
if (isset($_GET['classe'])){$clas= $_GET['classe'];}
if (isset($_POST['class'])){$clas= $_POST['class'];}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Planning des devoirs</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
<div class="plan-titre">Planning des Devoirs Surveill&#233;s de <?php echo $clas;?></div>
<?php

//Détermination du jour courant
if (isset($_POST['JourCourant'])) 
	{
	$JourCourant = $_POST['JourCourant'];
	
	//mois suivant
	if (isset($_POST['msuiv'])) 
		{
		$Lundi=DebutSemaine($JourCourant + 28 * 86400);
		} 
	//semaine suivante
	elseif (isset($_POST['suiv'])) 
		{
		$Lundi=DebutSemaine($JourCourant + 7 * 86400);
		} 
		//semaine précédente
	elseif (isset($_POST['mprec']))
		{			
		$Lundi=DebutSemaine($JourCourant - 28 * 86400);
		}		
		elseif (isset($_POST['prec']))
		{
			//tableau hebdomadaire commençant au Lundi
		$Lundi=DebutSemaine($JourCourant - 7 * 86400);
		} 
		else 
		{
		$Lundi=DebutSemaine($JourCourant);
		}
	}
	else 
	{
		$Lundi=DebutSemaine();
		}
//mémorisation de la date courante
if (isset($_GET['ladate'])) {$Lundi=$_GET['jc'];}

//fin de la semaine
$Samedi = $Lundi + 432000; //5 * 86400 
//Date du début de la semaine courante
$datdebut=date('Ymd',$Lundi);
//Date de la fin de la semaine courante
$datfin=date('Ymd',$Samedi);
//Recherche des devoirs programmés pour la semaine courante
$rq = "SELECT id_ds, DATE_FORMAT(date,'%d'),DATE_FORMAT(date,'%m'),DATE_FORMAT(date,'%Y'), 
creneau, matiere, sujet,  login, durée FROM devoir WHERE date>='$datdebut' AND date<='$datfin'  AND classe= '$clas' ORDER BY date asc , creneau asc";
$res = mysql_query($rq);
$nb = mysql_num_rows($res);
//si des devoirs ont été programmés

if ($nb>0) 
	{
	//pour chaque devoir programmé
	while ($row = mysql_fetch_array($res, MYSQL_NUM)) 
		{
		//détermination du timestamp du jour du devoir
		$tsmp=mkTime(8,0,0,$row[2],$row[1],$row[3]);
		// on parcourt les jours de la semaine
		for ($j=0; $j<=5; $j++)
			{
			$jour = $Lundi + $j * 86400;
			// on parcourt les heures de la  journée
			for ($h=0; $h<=9; $h++)
				{
				//si un devoir est programmé pour ce créneau
				if (($jour==$tsmp) &&($row[4] == $h))
					{
					$col = 0;
					if ((isset($plan[$j][$h][$col]))) $col = 1 ;
					//on mémorise les données dans des tableaux à 3 dimensions
					$num[$j][$h][$col] = $row[0];//numéro
					$plan[$j][$h] [$col]= "R";//on pose une marque (Réservé) pour le créneau
					$mat[$j][$h][$col] = utf8_encode($row[5]);//matière
					$suj[$j][$h][$col] = utf8_encode(stripslashes($row[6]));//sujet
					$log[$j][$h][$col] = $row[7];//login de l'auteur
					$dur[$j][$h][$col] = $row[8];//durée
					//on marque les autres créneaux utilisés par le devoir
					for ($x=1; $x<$row[8]; $x++) {$plan[$j][$h+$x][$col] = "O";}
					}
					$col = 0;
				}
			}
		}
	}
	
?>
<div id="cfg-container">
<div id="cfg-contenu">
<!-- affichage du calendrier hebdomadaire -->
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post"  id="planning">
	<!-- Affichage des boutons -->
        <div><input name="JourCourant" type="hidden" id="JourCourant" value="<?php echo $Lundi;?>" />
	<input name="class" type="hidden" id="class" value="<?php echo $clas;?>" />
	<table id="btn-plan-cdt">
		<tr>
			<td ><input type="submit" name="prec" value="&lt;&lt;" class="bt50" /></td>
			<td><input type="submit" name="quit" id="quit" value="" class="bt-exit" /></td>
			<td ><input type="submit" name="suiv" value="&gt;&gt;" class="bt50" /> </td>
			</tr>
	</table>
	<table id="plan-cdt" cellpadding="1" cellspacing="1">
		<thead> 
			<tr><th> <img src="../images/livre.gif" alt="." /> </th>
<?php
// Affichage des jours et dates de la semaine en haut du tableau"j-M-Y", 
	for ($i=0; $i<=5; $i++) {
		$TS = $Lundi+$i*86400;
		echo '<td colspan="2"> '.LeJour($TS)."<br />".datefr($TS)."</td>\n";
	}
?>
                        </tr>
                </thead>
	<tbody>
<?php
	$horaire = array("M1<br />","M2<br />","M3<br />","M4<br />","M5<br />",
	"S1<br />","S2<br />","S3<br />","S4<br />","S5<br />");
	for ($h=0; $h<=9; $h++) 
		{
                                    
                                    if (in_array(substr($horaire[$h],0,2), $cren_off)) 
                                    {
                                         echo '<tr><td class="mi-jour" colspan="13"></td></tr>';
                                        continue;
                                    }
                                    
		//Affichage de la désignation des créneaux horaires
		echo "<tr><th>".$horaire[$h].date('G:i',$deb[$h])."-".date('G:i',$fin[$h])."</th>\n";
		//Affichage du contenu des créneaux horaires 
		for ($j=0; $j<=5; $j++) 
			{//b
				$date[$j]=date('Ymd',$Lundi + $j * 86400);
				if (($Lundi + $j * 86400 + $dif[$h] < mktime()) )//Début du créneau"h"  passé 
					{//c
					//si c'est du passé, on l'écrit !!
					
					//sauf si on est dans le premier créneau d'un devoir en cours alors $plan[$j][$h]=="R"
					if ((isset($plan[$j][$h][0])) )
						{//d
						//et si on est pendant un devoir
						if (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][0]]) >= mktime()) && ($plan[$j][$h][0]=="R"))
							{//e
							
							// on écrit devoir en cours
							echo '<td  rowspan="'.$dur[$j][$h][0].'" class="encours">Devoir en cours </td>'."\n";
							//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir
							for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "EC"; }
							
						// s'il y a un devoir simultane 
						if ((isset($plan[$j][$h][1])) && (isset($dur[$j][$h][1]))) 
							{//f
							//et si on est pendant un devoir (cas "R" "R")
							if (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//g
								// on écrit devoir en cours
								echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
								//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir 
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC"; }
								}//g
							
								}//f
							
							//cas "R" "-" ou "R" "O"	
								if 	 ((!isset($plan[$j][$h][1])) )  
									{//h
									echo '<td   class="passe">Pass&#233;</td>' ."\n";
									}//h
						 
							}	//e //fin du cas "R" pendant un devoir
					
						//cas d'une cellule "EC"	
						
						//si on est pas dans le premier creneau du devoir
							if ($plan[$j][$h][0]=="EC")	
								{//i
								//cas "EC"-"O" "EC"-"-" "EC"-"Rp"
								if  (($plan[$j][$h][1]=="O" || (!isset($plan[$j][$h][1]))) ||(($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R")))
								 echo '<td   class="passe">'.$plan[$j][$h][1].'Pass&#233;</td>' ."\n";
								 //cas "EC"-"R"
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								// on écrit devoir en cours
								echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
								//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}
								}//gg
								
								
								}//i
						//creneau "R" passé 
						if ((($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][0]]) < mktime()) && ($plan[$j][$h][0]=="R")) )
								{//k
								//"Rp"-"-""
								if (!isset($plan[$j][$h][1]) ) 
								{
								echo '<td   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.substr($mat[$j][$h][0],0,10).'</td>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}	
								echo '<td   class="passe">Pass&#233;</td>' ."\n";
								}
								//"Rp"- "R"
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//
								// on ecrit devoir1 passe,devoir 2 encours 
								echo '<td   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.substr($mat[$j][$h][0],0,10).'</td>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
								echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
								//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}								
								}//
								//"Rp"-"O"
								if  (( isset($plan[$j][$h][1]) && $plan[$j][$h][1]=="O"))
								{echo '<td   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.substr($mat[$j][$h][0],0,10).'</td>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
								}
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								//"Rp"-"Rp"
								// on écrit devoir2 passe
								echo '<td   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.substr($mat[$j][$h][0],0,10).'</td>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
								echo '<td   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.substr($mat[$j][$h][1],0,10).'</td>'."\n";
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "O";}
								}//gg
								
								}//k
								
								//creneau O passe 
								if ((($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][0]]) < mktime()) && ($plan[$j][$h][0]=="O")) )
								{
								//O -
								if (!isset($plan[$j][$h][1]) ) 
								{
								echo '<td   class="passe">Pass&#233;</td>' ."\n";
								}
								//O-R 
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//
								echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
								//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}	
								}
								//O-Rp 
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
								{
								echo '<td   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.substr($mat[$j][$h][1],0,10).'</td>'."\n";
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "O";}
								}
								}
							}//d
						else
						//sinon  c'est un créneau vide  
						{
						//"-"-"O" + "-"-"-"+
						if  ( !isset($plan[$j][$h][1]) )
						echo '<td colspan="2"  class="passe">Pass&#233;</td>'."\n";
						//"-"-"R"
						else echo '<td   class="passe">Pass&#233;</td>' ."\n";
						if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								// on écrit devoir en cours
								echo '<td  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </td>'."\n";
								//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}
								}//gg
					
						//"-"-"Rp"
						if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								//"
								// on écrit devoir2 passe
								echo '<td   class="passe">Pass&#233;</td>'."\n";
								echo '<td   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.substr($mat[$j][$h][1],0,10).'</td>'."\n";
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "Op";}
								}//gg
									}
						}//c
						
						
//ici ce nest plus du passé
						
					//si ce n'est pas du passé et si rien n'est programmé on affiche un lien permettant de programmer un devoir		
					
						//si un devoir est programmé, on remplit les cellules correspondant à la durée, on affiche la matière et le sujet
						//plus un lien pour supprimer le devoir
						elseif ($plan[$j][$h][0]=="R") 
							{//m
							if (($Lundi + $j * 86400 + $dif[$h]) > mktime())  //MODIF
								{//n
								echo '<td  rowspan="'.$dur[$j][$h][0].'" class="reserve">'.substr($mat[$j][$h][0],0,10).
								'<br /><img alt="aide"   src="../images/planifier-cdt-aide.png"  title ="'.$suj[$j][$h][0].' " />'."</td>\n";
								if (!isset($plan[$j][$h][1])) 
									{//o
									echo '<td   class="libre">---</td>'."\n";  
									}//o
								elseif ($plan[$j][$h][1]=="R")  
									{//p
									echo ' <td  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.substr($mat[$j][$h][1],0,10).'<br /><br /><img alt="aide"   src="../images/planifier-cdt-aide.png"  title ="'.$suj[$j][$h][1].'" />'."</td>\n";
									}//p
								} //n
							}//m
						//cas des cellules "O"
						elseif (($plan[$j][$h][0]=="O") || ($plan[$j][$h][0]=="EC") ) 
							{//q
							if (($Lundi + $j * 86400 + $dif[$h]) > mktime())  //MODIF
								{//r
								
								if (!isset($plan[$j][$h][1])) 
									{//s
									echo '<td   class="libre">---</td>'."\n";
									}//s
								elseif ($plan[$j][$h][1]=="R")   
									{//t
									echo ' <td  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.substr($mat[$j][$h][1],0,10).
									'<br /><img alt="aide"   src="../images/help-info.png"  title ="'.$suj[$j][$h][1].'" />'."</td>\n";
									}//t
									}//r
								} //q
								
								elseif (!isset($plan[$j][$h][0])) 
						{//l
						if (!isset($plan[$j][$h][1]))
						echo '<td colspan="2"  class="libre">---</td>'."\n";
						else
						echo '<td class="libre">---</td>'."\n"; 
						}//l
							
					//cellules de séparation à la mi-journée			
					//if (($h==4)&&($j==5)) echo '</tr><tr><td class="mi-jour" colspan="13"></td>';
				}	//b
			echo "</tr>\n";
		
		}//a
?>
		</tbody>
	</table>
        </div>
	</form>
</div>
</div>
<?php
Include ('../Includes/pied.inc'); 
?>
</body>
</html> 