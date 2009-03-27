<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de plannification d'un devoir par un prof -
			_-=-_
	
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
//si la page est appel�e par un utilisateur non identifi� ou non prof
if (!isset($_SESSION['login'])) exit;
elseif ($_SESSION['cequi']!='prof' ) exit;

//si clic sur le bouton Quitter
if (isset($_POST['quit']))
	{$numrubrique=$_POST['numrubri'];
	header ("location: cahier_texte_prof.php?rubrique=$numrubrique");
	exit();}

	
include_once("../Includes/config.inc.php");
include_once("../Includes/fonctions.inc.php");
include_once("../Includes/creneau.inc.php");
include_once("../Includes/basedir.inc.php");

$login=$_SESSION['login'];

$point= '<img src="../images/help-info.png" height="" width="" alt="?" >';

//test si les listes de diffusion sont activ�es
if (!isset($_SESSION['liste']))
	{
	exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/main.cf", $AllOutPut1, $ReturnValueShareName); 
	exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/mailing_list.cf", $AllOutPut2, $ReturnValueShareName);
	     if (( count($AllOutPut1) >= 1) || ( count($AllOutPut2) >= 1)) $_SESSION['liste']=1; else $_SESSION['liste']=0;
	}

/************************	
Traitement du formulaire
*************************/	

//Enregistrement des donn�es
if (isset($_POST['enregistrer']))
	{ 
	// Traiter les donn�es
	// V�rifier $Sujet  et la d�barrasser de tout antislash et tags possibles
	if (strlen($_POST['sujet']) > 0)
		{ 
		$sujet  = addSlashes(strip_tags(stripslashes($_POST['sujet'])));
		}
	else
		{ // Si aucun sujet n'a �t� saisi
		$sujet = "";
		}
	
	// V�rifier $dur�e et la d�barrasser de tout antislash et tags possibles
	if ((strlen($_POST['dur�e'])> 0) && is_numeric($_POST['dur�e']))
		{ 
		$message="";
		$dur�e= addSlashes(strip_tags(stripslashes($_POST['dur�e'])));
	
		//limitation de dur�e
		if ((($_POST['creneau']<=4) && ( ($_POST['creneau'] + $dur�e) <= 5))
			//si la fin d'un devoir de l'apr�s-midi <= 18h
			|| (($_POST['creneau']> 4) && ( ($_POST['creneau'] + $dur�e) <= 10) ))
			{//pour chaque creneau du devoir 
			$lezard="false";
			for ($loop = $_POST['creneau']; $loop <= $_POST['creneau'] + $dur�e -1; $loop++) 
				
				{//test s'i les 2 cr�neaux sont occup�s pour la classe active 
 				$rq ="SELECT count(*)  FROM devoir WHERE date = '{$_POST['data']}' AND classe= '{$_POST['classe']}' AND creneau+ dur�e-1 >= '$loop' AND `creneau` <='$loop'"; 
 				$res = mysql_query($rq);
 				while ($row = mysql_fetch_array($res, MYSQL_NUM)) 
 					{
 					if ($row[0] == 2)
 					 { 
 					 $lezard="true";
 					 $message=$_POST['classe'].",";
 					 }
 					}
 					//test s'i les 2 cr�neaux sont occup�s pour les autres classes 
 					if ((isset($_SESSION['sel_cl']) ))
 						{
 						for ($a=0; $a < count ($_SESSION['sel_cl'])  ; $a++)
							{$groupe=$_SESSION['sel_cl'][$a];
							
							$rq ="SELECT count(*)  FROM devoir WHERE date = '{$_POST['data']}' AND classe= '$groupe' AND creneau+ dur�e-1 >= '$loop' AND `creneau` <='$loop'"; 
 							$res = mysql_query($rq);
 							while ($row = mysql_fetch_array($res, MYSQL_NUM)) 
 								{
 								if ($row[0] == 2) 
 									{
 									$lezard="true";
 									$message.=$groupe.",";
 									}
 								}
							}
 						}
 					} 				
			
				if ($lezard=="true") $dur�e="";

 			} else $dur�e= "";
 				
		}
	else
	// Si aucune dur�e n'a �t� saisie
		{ 
		$dur�e= "";
		}
	//echo "duree=".$dur�e ;exit;
	// Cr�er la requ�te d'�criture pour l'enregistrement des donn�es
	if ((isset($_POST['enregistrer'])) && ($dur�e!="") && ($sujet!=""))
		{
		$rq = "INSERT INTO devoir (date, creneau,login, matiere, sujet, classe, dur�e ) 
		VALUES ( '{$_POST['data']}','{$_POST['creneau']}', '$login', '{$_POST['mati�re']}', '{$_POST['sujet']}',
		'{$_POST['classe']}', '$dur�e')";
							
		// lancer la requ�te
		$result = mysql_query($rq);
		// Si l'enregistrement est incorrect 
		if (!$result)  
			{                           
			echo "<p>Votre devoir n'a pas pu �tre enregistr� � cause d'une erreur syst�me".
			"<p></p>" . mysql_error() . "<p></p>";
		// refermer la connexion avec la base de donn�es
			mysql_close();    
			exit();
			}
		else 
			{	
			
			//envoi d'un mail si les liste de difusion sont activ�es
			if ($_SESSION['liste']==1)
				{	
				// destinataire
				//recherche du groupe classe dans l'annuaire
				$classe_dest="";
				$classe_courte=split('_',$_POST['classe']);
				$classe_dest=$classe_courte[count($classe_courte)-1];
				include "$BASEDIR/lcs/includes/headerauth.inc.php";
				include "$BASEDIR/Annu/includes/ldap.inc.php";
				$grp_cl=search_groups("cn=".$_POST['classe']);
				if (count($grp_cl[0]==0)) $grp_cl=search_groups("cn=Classe_*".$classe_dest); 
				//echo "clc=".$grp_cl[0]["cn"];
				//on ferme la connexion lcs_db et on se reconnecte sur la bdd Cdt
				mysql_close();
				include("../Includes/config.inc.php");
				
				//mise en forme de la date du devoir
				if ($_SESSION['version']=">=432") 
					setlocale(LC_TIME,"french");
				else
					setlocale("LC_TIME","french");
				//on �value le timestamp de la date du devoir	
				$dat_new=strToTime($_POST['data']);	
				//Le destinataire 
				$mailTo = $grp_cl[0]["cn"];
				//Le sujet
				$mailSubject = "Nouveau devoir";
				//Le message
				$mailBody = " CECI EST UN MESSAGE AUTOMATIQUE, MERCI DE NE PAS REPONDRE.  \n \n Un devoir de "
				. $_POST['mati�re']." a �t� programm� le ".  strftime("%A %d %B %Y",$dat_new);
				//l'exp�diteur
				$mailHeaders = "From: Cahier\ de\ textes\n";
				//envoi du mail
				if ($classe_dest!="") mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
				}
			}
			
//traitement de devoirs multiples
		if ((isset($_SESSION['sel_cl']) ))
 			{
 			for ($a=0; $a < count ($_SESSION['sel_cl'])  ; $a++)
				{
				$groupe=$_SESSION['sel_cl'][$a];
 				$rq = "INSERT INTO devoir (date, creneau,login, matiere, sujet, classe, dur�e ) 
 				VALUES ( '{$_POST['data']}','{$_POST['creneau']}', '$login', '{$_POST['mati�re']}', 
 				'{$_POST['sujet']}', '$groupe', '$dur�e')";
							
				// lancer la requ�te
				$result = mysql_query($rq);
				// Si l'enregistrement est incorrect 
				if (!$result)  
					{                           
					echo "<p>Votre devoir n'a pas pu �tre enregistr� � cause d'une erreur syst�me".
					"<p></p>" . mysql_error() . "<p></p>";
					// refermer la connexion avec la base de donn�es
					mysql_close();    
					exit();
					}
					else 
			{	
			
			//envoi d'un mail si les liste de difusion sont activ�es
			if ($_SESSION['liste']==1)
				{	
				// destinataire
				//recherche du groupe classe dans l'annuaire
				$classe_dest="";
				$classe_courte=split('_',$groupe);
				$classe_dest=$classe_courte[count($classe_courte)-1];
				$grp_cl=search_groups("cn=".$groupe);
				if (count($grp_cl[0]==0)) $grp_cl=search_groups("cn=Classe_*".$classe_dest); 
				//on ferme la connexion lcs_db et on se reconnecte sur la bdd Cdt
				mysql_close();
				include("../Includes/config.inc.php");
				
				//mise en forme de la date du devoir
				if ($_SESSION['version']=">=432") 
					setlocale(LC_TIME,"french");
				else
					setlocale("LC_TIME","french");
				//on �value le timestamp de la date du devoir	
				$dat_new=strToTime($_POST['data']);	
				//Le destinataire 
				$mailTo = $grp_cl[0]["cn"];
				//Le sujet
				$mailSubject = "Nouveau devoir";
				//Le message
				$mailBody = " CECI EST UN MESSAGE AUTOMATIQUE, MERCI DE NE PAS REPONDRE.  \n \n Un devoir de "
				. $_POST['mati�re']." a �t� programm� le ".  strftime("%A %d %B %Y",$dat_new);
				//l'exp�diteur
				$mailHeaders = "From: Cahier\ de\ textes\n";
				//envoi du mail
				if ($classe_dest!="") mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
				}
			}
					
					
				}
 			}
			//fin devoirs multiples 
			unset ($_SESSION['sel_cl']);
		}
		 
	}

	
//Suppression d'un devoir apr�s confirmation
if (isset($_GET['delrub'])&& isset($_GET['numd'])) 
	{
	$action=$_GET['delrub'];
	$cible=$_GET['numd'];
	
	//le devoir existe-t-il ?
	$rq = "SELECT id_ds,login,date,matiere FROM devoir WHERE id_ds='$cible' and login='$login' ";
	// lancer la requ�te
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
	{
	$dat_supp=$row[2];
	$mat_supp=$row[3];
	}
	//effacement de l'enregistrement
	if (($nb==1) &&($action==1245))
		{
		$rq = "DELETE  FROM devoir WHERE id_ds='$cible' and login='$login' LIMIT 1";
		$result2 = @mysql_query ($rq) or die (mysql_error());
		//envoi d'un mail
		if ($_SESSION['liste']==1)
			{	
			//recherche du groupe classe dans l'annuaire
			$classe_dest="";
			$classe_courte=split('_',$_GET['klasse']);
			$classe_dest=$classe_courte[count($classe_courte)-1];
			include "$BASEDIR/lcs/includes/headerauth.inc.php";
			include "$BASEDIR/Annu/includes/ldap.inc.php";
			$grp_cl=search_groups("cn=Classe_*".$classe_dest);
			$dest=$grp_cl[0]["cn"];
			mysql_close();
			include("../Includes/config.inc.php");	
			//mise en forme de la date du devoir
			if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
				else setlocale("LC_TIME","french");	
				$dat_new=strToTime($dat_supp);	
				//destinataire
				$mailTo = $grp_cl[0]["cn"];
				//Le sujet
				$mailSubject = "Devoir supprime";
				//Le  message
				$mailBody = " CECI EST UN MESSAGE AUTOMATIQUE, MERCI DE NE PAS REPONDRE.  \n \n Le devoir de "
				.$mat_supp." pr�vu le  " .strftime("%A %d %B %Y",$dat_new)." a �t� supprim�  ";
				//l'exp�diteur
				$mailHeaders = "From: Cahier\ de\ textes\n";
				//envoi mail
				if ($classe_dest!="") mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
				
			}
		}
	}
//fin de suppression d'un devoir

//m�morisation des param�tres POST classe  mati�re et num�ro de rubrique renvoy�s par le formulaire
if (isset($_POST['classe'])){$clas= $_POST['classe'];}
if (isset($_POST['mati�re'])){$mati= $_POST['mati�re'];}
if (isset($_POST['numrubri'])){$numrub= $_POST['numrubri'];}

//recherche de la mati�re et la classe de la rubrique active du cahier de textes
if (isset($_GET['rubrique']))
	{ 
	$rq = "SELECT classe,matiere FROM onglets
	WHERE id_prof='{$_GET['rubrique']}'  ";
	// lancer la requ�te
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result);  
	//on r�cup�re les donn�es
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{$clas=$enrg[0];//classe
		$mati=$enrg[1];//mati�re
		}
	$numrub=$_GET['rubrique'];
	}
	
			
			
//m�morisation des param�tres GET classe mati�re et num�ro de rubrique renvoy�s par le formulaire	
if (isset($_GET['ladate']))
 {
 $clas=$_GET['klasse'];
 $mati=$_GET['mati�re'];
 $numrub=$_GET['numrubr'];

 
 }
?>
<HTML>
<HEAD>
<TITLE>Planning</TITLE>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK href="../style/style.css" rel="stylesheet" type="text/css">

</HEAD>
<BODY>
<H3 align="center"><U>Planning des Devoirs Surveill�s de <?php echo $clas;?></U></H3>
<?
//Affichage conditionnel d'un message d'erreur
if ((isset($_POST['enregistrer'])) && ($dur�e=="")) echo '<H6 class="erreur">'.$message. ' : Dur�e erron�e</H6>';

//Affichage du formulaire de saisie du sujet du devoir et de la dur�e
if (isset($_GET['ladate']) &&(isset($_GET['inser'])))
	{
	echo '<FORM ACTION="'.$_SERVER["PHP_SELF"].'" METHOD="POST">
	<SCRIPT language="Javascript" src="../Includes/barre_java.js"></SCRIPT>
	<INPUT name="JourCourant" type="hidden" id="JourCourant" value="'. $_GET["jc"].'">
	<INPUT name="classe" type="hidden" id="classe" value="'. $_GET['klasse'].'">
	<INPUT name="mati�re" type="hidden" id="mati�re" value="'. $_GET['mati�re'].'">
	<INPUT name="creneau" type="hidden" id="creneau" value="'. $_GET['creneau'].'">
	<INPUT name="login" type="hidden" id="login" value="'. $login.'">
	<INPUT name="data" type="hidden" id="data" value="'.$_GET["ladate"].'">
	<INPUT name="numrubri" type="hidden" id="numrubri" value="'.$numrub.'">
	<P STYLE="margin-left: 1cm"><FONT color="#003366"><B>Intitul� du devoir : 
	</B><INPUT TYPE=TEXT NAME="sujet" SIZE=28 MAXLENGTH=28> &nbsp &nbsp
	<B>Dur�e </B><INPUT TYPE=TEXT NAME="dur�e" SIZE=1 MAXLENGTH=1>heure(s)
	</FONT>	&nbsp<FONT size="2"> <a href="diffuse_devoirs.php?rubrique='.$numrub.'" onClick="diffusedev_popup('.$numrub.'); return false" > S&eacute;lectionner d\'autres classes  
		</A></font>
	<INPUT TYPE=SUBMIT NAME="enregistrer" VALUE="Enregistrer">
	</P>
	</FORM>';
	}
//D�termination du jour courant
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
		//semaine pr�c�dente
	elseif (isset($_REQUEST['mprec']))
		{			
		$Lundi=DebutSemaine($JourCourant - 28 * 86400);
		}		
		elseif (isset($_REQUEST['prec']))
		{
			//tableau hebdomadaire commen�ant au Lundi
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
//m�morisation de la date courante
if (isset($_GET['ladate'])) {$Lundi=$_GET['jc'];}

//fin de la semaine
$Samedi = $Lundi + 432000; //5 * 86400 
//Date du d�but de la semaine courante
$datdebut=date('Ymd',$Lundi);
//Date de la fin de la semaine courante
$datfin=date('Ymd',$Samedi);
//Recherche des devoirs programm�s pour la semaine courante
$rq = "SELECT id_ds, DATE_FORMAT(date,'%d'),DATE_FORMAT(date,'%m'),DATE_FORMAT(date,'%Y'), creneau, matiere, sujet,  login, dur�e FROM devoir 
WHERE date>='$datdebut' AND date<='$datfin'  AND classe= '$clas' ORDER BY date asc, creneau asc";
$res = mysql_query($rq);
$nb = mysql_num_rows($res);
//si des devoirs ont �t� programm�s
if ($nb>0) 
	{
	//pour chaque devoir programm�
	while ($row = mysql_fetch_array($res, MYSQL_NUM)) 
		{
		//d�termination du timestamp du jour du devoir
		$tsmp=mkTime(8,0,0,$row[2],$row[1],$row[3]);
		// on parcourt les jours de la semaine
		for ($j=0; $j<=5; $j++)
			{
			$jour = $Lundi + $j * 86400;
			// on parcourt les heures de la  journ�e
			for ($h=0; $h<=9; $h++)
				{
				
				//si un devoir est programm� pour ce cr�neau
				if (($jour==$tsmp) &&($row[4] == $h))
					{
					$col = 0;
					if ((isset($plan[$j][$h][$col]))) $col = 1 ;
					//on m�morise les donn�es dans des tableaux � 3 dimensions
					$num[$j][$h][$col] = $row[0];//num�ro
					$plan[$j][$h] [$col]= "R";//on pose une marque (R�serv�) pour le cr�neau
					$mat[$j][$h][$col] = $row[5];//mati�re
					$suj[$j][$h][$col] = $row[6];//sujet
					$log[$j][$h][$col] = $row[7];//login de l'auteur
					$dur[$j][$h][$col] = $row[8];//dur�e
					//on marque les autres cr�neaux utilis�s par le devoir
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
	<INPUT name="JourCourant" type="hidden" id="JourCourant" value="<?php echo $Lundi;?>">
	<INPUT name="classe" type="hidden" id="classe" value="<?php echo $clas;?>">
	<INPUT name="mati�re" type="hidden" id="mati�re" value="<?php echo $mati;?>">
	<INPUT name="numrubri" type="hidden" id="numrubri" value="<?php echo $numrub;?>">
	<TABLE align="center" >
		<TR>
			<TD width="150" align="center" ><INPUT type="submit" name="mprec" value="  <<  " title="Mois pr�c�dent"></TD>
			<TD width="150" align="center" ><INPUT type="submit" name="prec" value="  <  " title="Semaine pr�c�dente"></TD>
			<TD width="150" align="center" ><INPUT type="submit" name="quit" id="quit" value="  Quitter  "></TD>
			<TD width="150" align="center" ><INPUT type="submit" name="suiv" value="  >  "title="Semaine suivante"></TD>
			<TD width="150" align="center" ><INPUT type="submit" name="msuiv" value="  >>  "title="Mois suivant"></TD>
			
		</TR>
	</TABLE>
	
	<TABLE width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bgcolor="#C0C0C0" >
		<TR> 
			<TH><img src="../images/livre.gif"> �</TH>
<?php
// Affichage des jours et dates de la semaine en haut du tableau"j-M-Y",
	for ($i=0; $i<=5; $i++) {
		$TS = $Lundi+$i*86400;
		echo '<TH width="15%" colspan="2"> <font face="Arial" size="2" color="#FFFFFF">'.LeJour($TS)."<BR>".datefr($TS)."</TH>\n";
	}
?>
		</TR>
<?php
	$horaire = array("M1<br>","M2<br>","M3<br>","M4<br>","M5<br>",
	"S1<br>","S2<br>","S3<br>","S4<br>","S5<br>");
	for ($h=0; $h<=9; $h++) 
		{//a
		//Affichage de la d�signation des cr�neaux horaires
		
		echo "<TH><font face='Arial'size='2' color='#FFFFFF' >".$horaire[$h].date('G:i',$deb[$h])."-".date('G:i',$fin[$h])."</TH>\n";
		
		
		//Affichage du contenu des cr�neaux horaires 
		for ($j=0; $j<=5; $j++) 
			{//b
				$date[$j]=date('Ymd',$Lundi + $j * 86400);
				if (($Lundi + $j * 86400 + $dif[$h] < mktime()) )//D�but du cr�neau"h"  pass� 
					{//c
					//si c'est du pass�, on l'�crit !!
					
					//sauf si on est dans le premier cr�neau d'un devoir en cours alors $plan[$j][$h]=="R"
					if ((isset($plan[$j][$h][0])) )
						{//d
						//et si on est pendant un devoir
						if (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][0]]) >= mktime()) && ($plan[$j][$h][0]=="R"))
							{//e
							
							// on �crit devoir en cours
							echo '<TD  rowspan="'.$dur[$j][$h][0].'" class="encours">Devoir en cours </TD>'."\n";
							//on marque les cr�neaux suivants pour ne pas les marqu�s "pass�" avant la fin du devoir
							for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "EC"; }
							
						// s'il y a un devoir simultane 
						if ((isset($plan[$j][$h][1])) && (isset($dur[$j][$h][1]))) 
							{//f
							//et si on est pendant un devoir (cas "R" "R")
							if (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//g
								// on �crit devoir en cours
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
								//on marque les cr�neaux suivants pour ne pas les marqu�s "pass�" avant la fin du devoir 
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC"; }
								}//g
							
								}//f
							
							//cas "R" "-" ou "R" "O"	
								if 	 ((!isset($plan[$j][$h][1])) )  
									{//h
									echo '<TD   class="passe">Pass�</TD>' ."\n";
									}//h
						 
							}	//e //fin du cas "R" pendant un devoir
					
						//cas d'une cellule "EC"	
						
						//si on est pas dans le premier creneau du devoir
							if ($plan[$j][$h][0]=="EC")	
								{//i
								//cas "EC"-"O" "EC"-"-" "EC"-"Rp"
								if  (($plan[$j][$h][1]=="O" || (!isset($plan[$j][$h][1]))) ||(($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R")))
								 echo '<TD   class="passe">'.$plan[$j][$h][1].'Pass�</TD>' ."\n";
								 //cas "EC"-"R"
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								// on �crit devoir en cours
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
								//on marque les cr�neaux suivants pour ne pas les marqu�s "pass�" avant la fin du devoir
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}
								}//gg
								
								
								}//i
						//creneau "R" pass� 
						if ((($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][0]]) < mktime()) && ($plan[$j][$h][0]=="R")) )
								{//k
								//"Rp"-"-""
								if (!isset($plan[$j][$h][1]) ) 
								{
								echo '<TD   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</TD>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}	
								echo '<TD   class="passe">Pass�</TD>' ."\n";															
								}
								//"Rp"- "R"
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//
								// on ecrit devoir1 passe,devoir 2 encours 
								echo '<TD   rowspan="'.$dur[$j][$h][0].'" class="devoir">'.$mat[$j][$h][0].'</TD>'."\n";
								for ($x=1; $x<$dur[$j][$h][0]; $x++) {$plan[$j][$h+$x][0] = "O";}
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
								//on marque les cr�neaux suivants pour ne pas les marqu�s "pass�" avant la fin du devoir
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
								// on �crit devoir2 passe
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
								echo '<TD   class="passe">Pass�</TD>' ."\n";		
								}
								//O-R 
								if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
								//on marque les cr�neaux suivants pour ne pas les marqu�s "pass�" avant la fin du devoir
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
						//sinon  c'est un cr�neau vide  
						{
						//"-"-"O" + "-"-"-"+
						if  ( !isset($plan[$j][$h][1]) )
						echo '<TD colspan="2"  class="passe">Pass�</TD>'."\n";
						//"-"-"R"
						else echo '<TD   class="passe">Pass�</TD>' ."\n";
						if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) >= mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								// on �crit devoir en cours
								echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="encours">Devoir en cours </TD>'."\n";
								//on marque les cr�neaux suivants pour ne pas les marqu�s "pass�" avant la fin du devoir
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "EC";}
								}//gg
					
						//"-"-"Rp"
						if  (($Lundi + $j * 86400 + ($dif[$h+$dur[$j][$h][1]]) < mktime()) && ($plan[$j][$h][1]=="R"))
								{//gg
								//"
								// on �crit devoir2 passe
								echo '<TD   class="passe">Pass�</TD>'."\n";
								echo '<TD   rowspan="'.$dur[$j][$h][1].'" class="devoir">'.$mat[$j][$h][1].'</TD>'."\n";
								for ($x=1; $x<$dur[$j][$h][1]; $x++) {$plan[$j][$h+$x][1] = "Op";}
								}//gg
									}
						}//c
						
						
//ici ce nest plus du pass�
						
					//si ce n'est pas du pass� et si rien n'est programm� on affiche un lien permettant de programmer un devoir		
					
						//si un devoir est programm�, on remplit les cellules correspondant � la dur�e, on affiche la mati�re et le sujet
						//plus un lien pour supprimer le devoir
						elseif ($plan[$j][$h][0]=="R") 
							{//m
							if (($Lundi + $j * 86400 + $dif[$h]) > mktime())  //MODIF
								{//n
								echo '<TD  rowspan="'.$dur[$j][$h][0].'" class="reserve">'.$mat[$j][$h][0].
								'<BR><BR><img alt="aide"   src="../images/help-info.png"  title ="'.$suj[$j][$h][0].' " />&nbsp; &nbsp; &nbsp; 
								<a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j].
								'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&mati�re='.$mati.'&suppr=yes&numdev='
								.$num[$j][$h][0].'&numrubr='.$numrub.'" title="Supprimer ce devoir">'.
								'<img alt="aide"   border="0"src="../images/b_drop.png"   /> </a>'."</TD>\n";
								if (!isset($plan[$j][$h][1])) 
									{//o
									echo '<TD   class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
									.'&inser=yes'.'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&mati�re='.$mati
									.'&numrubr='.$numrub.'" title="Planifier un devoir">-�-</a></TD>'."\n";
									}//o
								elseif ($plan[$j][$h][1]=="R")  
									{//p
									echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.$mat[$j][$h][1].
									'<BR><BR><img alt="aide"   src="../images/help-info.png"  title ="'.$suj[$j][$h][1].'" />&nbsp; &nbsp; &nbsp; 
									<a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j].
									'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&mati�re='.$mati.'&suppr=yes&numdev='
									.$num[$j][$h][1].'&numrubr='.$numrub.'" title="Supprimer ce devoir">'.
									'<img alt="aide"   border="0"src="../images/b_drop.png"> </a>'."</TD>\n";
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
									echo '<TD   class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
									.'&inser=yes'.'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&mati�re='.$mati
									.'&numrubr='.$numrub.'" title="Planifier un devoir">-�-</a></TD>'."\n";
									}//s
								elseif ($plan[$j][$h][1]=="R")   
									{//t
									echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.$mat[$j][$h][1].
									'<BR><BR><img alt="aide"   src="../images/help-info.png"  title ="'.$suj[$j][$h][1].'" />&nbsp; &nbsp; &nbsp; 
									<a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j].
									'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&mati�re='.$mati.'&suppr=yes&numdev='
									.$num[$j][$h][1].'&numrubr='.$numrub.'" title="Supprimer ce devoir">'.
									'<img alt="aide"   border="0"src="../images/b_drop.png"> </a>'."</TD>\n";
									}//t
									}//r
								} //q
								
								elseif (!isset($plan[$j][$h][0])) 
						{//l
						if (!isset($plan[$j][$h][1]))
						echo '<TD colspan="2"  class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
						.'&inser=yes'.'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&mati�re='.$mati
						.'&numrubr='.$numrub.'" title="Planifier un devoir">-�-</a></TD>'."\n";
						else
						echo '<TD class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
						.'&inser=yes'.'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&mati�re='.$mati
						.'&numrubr='.$numrub.'" title="Planifier un devoir">-�-</a></TD>'."\n"; 
						}//l
							
					//cellules de s�paration � la mi-journ�e			
					if (($h==4)&&($j==5)) echo '</TR><TH></TH><TD ></TD><TD ></TD><TD ></TD><TD ></TD><TD ></TD></TR>';	
				}	//b
			echo "</TR>\n";
		
		}//a
?>

	</TABLE>
	
<H5>Pour planifier un nouveau devoir, cliquer sur le cr�neau horaire correspondant au d�but du devoir</h5>
	</FORM>
<?
//Affichage d'une boite de confirmation d'effacement
if (isset($_GET["suppr"]))
	{
	echo "<script type='text/javascript'>";
	echo "if (confirm('Confirmer la suppression de ce devoir (Vous ne pouvez supprimer que vos devoirs !!)')){";
	echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'" + "?numd=" +'." '"; echo $_GET["numdev"] ;echo "'"
	.' + "&delrub=" +'."1245".' + "&ladate=" +'."'";echo $date[0];echo "'".'+ "&jc="+'."'".$Lundi."'".'
	+ "&klasse="+'."'".$clas."'".'+ "&numrubr="+'."'".$numrub."'".'+ "&mati�re="+'."'".$mati."'".' ;}</script>';
	}
include ('../Includes/pied.inc'); 
?>
</BODY>
</HTML> 
