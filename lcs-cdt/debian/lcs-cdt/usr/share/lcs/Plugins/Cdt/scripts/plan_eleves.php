<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de consultation du planning des devoirs -
			_-=-_
	
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
//si la page est appelée par son URL
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit;
if (isset($_POST['quit']))
	{$clas=$_POST['class'];
	header ("location:cahier_text_eleve.php?div=$clas");
	exit();}
	
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
<HTML>
<HEAD>
<TITLE>Planning</TITLE>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK href="../style/style.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY>
<H3 align="center"><U>Planning des Devoirs Surveillés de  <?php echo $clas;?></U></H3>
<?

//Détermination du jour courant
if (isset($_REQUEST['JourCourant'])) 
	{
	$JourCourant = $_REQUEST['JourCourant'];
	
	//mois suivant
	if (isset($_REQUEST['msuiv'])) 
		{
		$Lundi=DebutSemaine($JourCourant + 28 * 86400);
		} 
	//semaine suivante
	elseif (isset($_REQUEST['suiv'])) 
		{
		$Lundi=DebutSemaine($JourCourant + 7 * 86400);
		} 
		//semaine précédente
	elseif (isset($_REQUEST['mprec']))
		{			
		$Lundi=DebutSemaine($JourCourant - 28 * 86400);
		}		
		elseif (isset($_REQUEST['prec']))
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
					$mat[$j][$h][$col] = $row[5];//matière
					$suj[$j][$h][$col] = $row[6];//sujet
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
<!-- affichage du calendrier hebdomadaire -->
<FORM action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" name="planning" id="planning">
	<!-- Affichage des boutons -->
	</TABLE>
	<INPUT name="JourCourant" type="hidden" id="JourCourant" value="<?php echo $Lundi;?>">
	<INPUT name="class" type="hidden" id="class" value="<?php echo $clas;?>">
	<TABLE align="center" >
		<TR>
			<TD width="150" align="center" ><INPUT type="submit" name="prec" value=" << "></TD>
			<TD width="150" align="center" ><INPUT type="submit" name="quit" id="quit" value="  Quitter  "></TD>
			<TD width="150" align="center" ><INPUT type="submit" name="suiv" value=" >> "></TD>
			</TR>
	</TABLE>
	
	
	<TABLE width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bgcolor="#C0C0C0" >
		<TR> 
			<TH> <img src="../images/livre.gif"> </TH>
<?php
// Affichage des jours et dates de la semaine en haut du tableau"j-M-Y", 
	for ($i=0; $i<=5; $i++) {
		$TS = $Lundi+$i*86400;
		echo '<TH width="15%" colspan="2"> <font face="Arial" size="2" color="#FFFFFF" >'.LeJour($TS)."<BR>".datefr($TS)."</TH>\n";
	}
?>
		</TR>
<?php
	$horaire = array("M1<br>","M2<br>","M3<br>","M4<br>","M5<br>",
	"S1<br>","S2<br>","S3<br>","S4<br>","S5<br>");
	for ($h=0; $h<=9; $h++) 
		{//a
		//Affichage de la désignation des créneaux horaires
		
		echo "<TH><font face='cursive'size='2' color='#FFFFFF' >".$horaire[$h].date('G:i',$deb[$h])."-".date('G:i',$fin[$h])."</TH>\n";
		
		
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
							echo '<TD  rowspan="'.$dur[$j][$h][0].'" class="encours">Devoir en cours </TD>'."\n";
							//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir
							for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "EC"; }
							
						// s'il y a un devoir simultane 
						if ((isset($plan[$j][$h][1])) && (isset($dur[$j][$h][1]))) 
							{//f
							//et si on est pendant un devoir (cas "R" "R")
							if (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//g
								// on écrit devoir en cours
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
								//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir 
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC"; }
								}//g
							
								}//f
							
							//cas "R" "-" ou "R" "O"	
								if 	 ((!isset($plan[$j][$h][1])) )  
									{//h
									echo '<TD   class="passe">Passé</TD>' ."\n";
									}//h
						 
							}	//e //fin du cas "R" pendant un devoir
					
						//cas d'une cellule "EC"	
						
						//si on est pas dans le premier creneau du devoir
							if ($plan[$j][$h][0]=="EC")	
								{//i
								//cas "EC"-"O" "EC"-"-" "EC"-"Rp"
								if  (($plan[$j][$h][1]=="O" || (!isset($plan[$j][$h][1]))) ||(($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R")))
								 echo '<TD   class="passe">'.$plan[$j][$h][1].'Passé</TD>' ."\n";
								 //cas "EC"-"R"
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								// on écrit devoir en cours
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
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
								echo '<TD   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</TD>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}	
								echo '<TD   class="passe">Passé</TD>' ."\n";															
								}
								//"Rp"- "R"
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//
								// on ecrit devoir1 passe,devoir 2 encours 
								echo '<TD   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</TD>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
								//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}								
								}//
								//"Rp"-"O"
								if  (( isset($plan[$j][$h][1]) && $plan[$j][$h][1]=="O"))
								{echo '<TD   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</TD>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
								}
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								//"Rp"-"Rp"
								// on écrit devoir2 passe
								echo '<TD   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</TD>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
								echo '<TD   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.$mat[$j][$h][1].'</TD>'."\n";
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "O";}
								}//gg
								
								}//k
								
								//creneau O passe 
								if ((($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][0]]) < mktime()) && ($plan[$j][$h][0]=="O")) )
								{
								//O -
								if (!isset($plan[$j][$h][1]) ) 
								{
								echo '<TD   class="passe">Passé</TD>' ."\n";		
								}
								//O-R 
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
								//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}	
								}
								//O-Rp 
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
								{
								echo '<TD   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.$mat[$j][$h][1].'</TD>'."\n";
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "O";}
								}
								}
							}//d
						else
						//sinon  c'est un créneau vide  
						{
						//"-"-"O" + "-"-"-"+
						if  ( !isset($plan[$j][$h][1]) )
						echo '<TD colspan="2"  class="passe">Passé</TD>'."\n";
						//"-"-"R"
						else echo '<TD   class="passe">Passé</TD>' ."\n";
						if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								// on écrit devoir en cours
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
								//on marque les créneaux suivants pour ne pas les marqués "passé" avant la fin du devoir
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}
								}//gg
					
						//"-"-"Rp"
						if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								//"
								// on écrit devoir2 passe
								echo '<TD   class="passe">Passé</TD>'."\n";
								echo '<TD   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.$mat[$j][$h][1].'</TD>'."\n";
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
								echo '<TD  rowspan="'.$dur[$j][$h][0].'" class="reserve">'.$mat[$j][$h][0].
								'<BR><BR><img alt="aide"   src="../images/help-info.png"  title ="'.$suj[$j][$h][0].' " /> 
								'."</TD>\n";
								if (!isset($plan[$j][$h][1])) 
									{//o
									echo '<TD   class="libre">---</TD>'."\n";  
									}//o
								elseif ($plan[$j][$h][1]=="R")  
									{//p
									echo ' <TD  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.$mat[$j][$h][1].
									'<BR><BR><img alt="aide"   src="../images/help-info.png"  title ="'.$suj[$j][$h][1].'" />
									'."</TD>\n";
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
									echo '<TD   class="libre">---</TD>'."\n";
									}//s
								elseif ($plan[$j][$h][1]=="R")   
									{//t
									echo ' <TD  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.$mat[$j][$h][1].
									'<BR><BR><img alt="aide"   src="../images/help-info.png"  title ="'.$suj[$j][$h][1].'" />
									'."</TD>\n";
									}//t
									}//r
								} //q
								
								elseif (!isset($plan[$j][$h][0])) 
						{//l
						if (!isset($plan[$j][$h][1]))
						echo '<TD colspan="2"  class="libre">---</TD>'."\n";
						else
						echo '<TD class="libre">---</TD>'."\n"; 
						}//l
							
					//cellules de séparation à la mi-journée			
					if (($h==4)&&($j==5)) echo '</TR><TH></TH><TD ></TD><TD ></TD><TD ></TD><TD ></TD><TD ></TD></TR>';	
				}	//b
			echo "</TR>\n";
		
		}//a
?>

	</FORM>
<?
Include ('../Includes/pied.inc'); 
?>
</BODY>
</HTML> 
