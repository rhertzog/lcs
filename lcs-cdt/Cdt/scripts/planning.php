<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.1 du 4/6/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de plannification d'un devoir par un prof -
			_-=-_
	
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
//si la page est appelée par un utilisateur non identifié ou non prof
if (!isset($_SESSION['login'])) exit;
elseif ($_SESSION['cequi']!='prof' ) exit;

//si clic sur le bouton Quitter
if (isset($_POST['quit']))
	{$numrubrique=$_POST['numrubri'];
	header ("location: cahier_texte_prof.php?rubrique=$numrubrique");
	exit();}
include_once("../Includes/basedir.inc.php");
include_once "$BASEDIR/lcs/includes/headerauth.inc.php";
include_once "$BASEDIR/Annu/includes/ldap.inc.php";
include_once "$BASEDIR/Annu/includes/ihm.inc.php";
mysql_close();
include "../Includes/data.inc.php";
include "../Includes/functions2.inc.php";	
include_once("../Includes/config.inc.php");
include_once("../Includes/fonctions.inc.php");
include_once("../Includes/creneau.inc.php");
$login=$_SESSION['login'];
//$point= '<img src="../images/help-info.png" height="" width="" alt="?" >';

//test si les listes de diffusion sont activées
if (!isset($_SESSION['liste']))
	{
	exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/main.cf", $AllOutPut1, $ReturnValueShareName); 
	exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/mailing_list.cf", $AllOutPut2, $ReturnValueShareName);
	     if (( count($AllOutPut1) >= 1) || ( count($AllOutPut2) >= 1)) $_SESSION['liste']=1; else $_SESSION['liste']=0;
	}
if (get_magic_quotes_gpc()) require_once("../Includes/class.inputfilter_clean.php");
else require_once '../Includes/htmlpur/library/HTMLPurifier.auto.php';
/************************	
Traitement du formulaire
*************************/	

//Enregistrement des données
if (isset($_POST['enregistrer']) && $_POST['TA']==$_SESSION['RT'])
	{ 
	// Traiter les données
	// Vérifier $Sujet  et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['sujet']) > 0)
		{ 
		{ 
		
		if (get_magic_quotes_gpc())
		    {
			$Sujet  =htmlentities($_POST['sujet']);
			$oMyFilter = new InputFilter($aAllowedTags, $aAllowedAttr, 0, 0, 1);
			$Sujet = $oMyFilter->process($Sujet);
			}
		else
			{
			// htlmpurifier
			$Sujet = addslashes($_POST['sujet']);
			$config = HTMLPurifier_Config::createDefault();
	    	$config->set('Core.Encoding', 'ISO-8859-15'); 
	    	$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
	   		$purifier = new HTMLPurifier($config);
	   		$Sujet = $purifier->purify($Sujet);
	   		$Sujet=mysql_real_escape_string($Sujet);
	   		}
		
		}
		//$sujet  = addSlashes(strip_tags(stripslashes($_POST['sujet'])));
		}
	else
		{ // Si aucun sujet n'a été saisi
		$Sujet = "";
		}
	
	// Vérifier $durée et la débarrasser de tout antislash et tags possibles
	if ((strlen($_POST['durée'])> 0) && is_numeric($_POST['durée']))
		{ 
		$message="";
		$durée= addSlashes(strip_tags(stripslashes($_POST['durée'])));
	
		//limitation de durée
		if ((($_POST['creneau']<=4) && ( ($_POST['creneau'] + $durée) <= 5))
			//si la fin d'un devoir de l'après-midi <= 18h
			|| (($_POST['creneau']> 4) && ( ($_POST['creneau'] + $durée) <= 10) ))
			{//pour chaque creneau du devoir 
			$lezard="false";
			for ($loop = $_POST['creneau']; $loop <= $_POST['creneau'] + $durée -1; $loop++) 
				
				{//test s'i les 2 créneaux sont occupés pour la classe active 
 				$rq ="SELECT count(*)  FROM devoir WHERE date = '{$_POST['data']}' AND classe= '{$_POST['classe']}' AND creneau+ durée-1 >= '$loop' AND `creneau` <='$loop'"; 
 				$res = mysql_query($rq);
 				while ($row = mysql_fetch_array($res, MYSQL_NUM)) 
 					{
 					if ($row[0] == 2)
 					 { 
 					 $lezard="true";
 					 $message=$_POST['classe'].",";
 					 }
 					}
 					//test s'i les 2 créneaux sont occupés pour les autres classes 
 					if ((isset($_SESSION['sel_cl']) ))
 						{
 						for ($a=0; $a < count ($_SESSION['sel_cl'])  ; $a++)
							{$groupe=$_SESSION['sel_cl'][$a];
							
							$rq ="SELECT count(*)  FROM devoir WHERE date = '{$_POST['data']}' AND classe= '$groupe' AND creneau+ durée-1 >= '$loop' AND `creneau` <='$loop'"; 
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
			
				if ($lezard=="true") $durée="";

 			} else $durée= "";
 				
		}
	else
	// Si aucune durée n'a été saisie
		{ 
		$durée= "";
		}
	//echo "duree=".$durée ;exit;
	// Créer la requête d'écriture pour l'enregistrement des données
	if ((isset($_POST['enregistrer'])) && ($durée!="") && ($Sujet!=""))
		{
		$rq = "INSERT INTO devoir (date, creneau,login, matiere, sujet, classe, durée ) 
		VALUES ( '{$_POST['data']}','{$_POST['creneau']}', '$login', '{$_POST['matière']}', '$Sujet',
		'{$_POST['classe']}', '$durée')";
							
		// lancer la requête
		$result = mysql_query($rq);
		// Si l'enregistrement est incorrect 
		if (!$result)  
			{                           
			echo "<p>Votre devoir n'a pas pu être enregistré à cause d'une erreur système".
			"<p></p>" . mysql_error() . "<p></p>";
		// refermer la connexion avec la base de données
			mysql_close();    
			exit();
			}
		else 
			{	
			
			//envoi d'un mail si les liste de difusion sont activées
			if ($_SESSION['liste']==1)
				{	
				// destinataire
				//recherche du groupe classe dans l'annuaire
				$classe_dest=$_POST['classe'];
				$grp_cl=search_groups("cn=".$_POST['classe']);
				if (count($grp_cl[0]==0)) $grp_cl=search_groups("cn=Classe_*".$classe_dest); 
				
				//on ferme la connexion lcs_db et on se reconnecte sur la bdd Cdt
				mysql_close();
				include("../Includes/config.inc.php");
				
				//mise en forme de la date du devoir
				if ($_SESSION['version']=">=432") 
					setlocale(LC_TIME,"french");
				else
					setlocale("LC_TIME","french");
				//on évalue le timestamp de la date du devoir	
				$dat_new=strToTime($_POST['data']);	
				//Le destinataire 

				if  (!mb_ereg("^Cours",$_POST['classe'])) 
				$mailTo = $grp_cl[0]["cn"];
				else $mailTo = $_POST['classe'];
				//Le sujet
				$mailSubject = "Nouveau devoir";
				//Le message
				$mailBody = " CECI EST UN MESSAGE AUTOMATIQUE, MERCI DE NE PAS REPONDRE.  \n \n Un devoir de "
				. $_POST['matière']." a été programmé le ".  strftime("%A %d %B %Y",$dat_new);
				//l'expéditeur
				$mailHeaders = "From: Cahier\ de\ textes\n";
				//envoi du mail
				if ($classe_dest!="") mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
				}
				//enregistrement dans le travail à faire
				$Dev= "Préparer le Devoir surveillé : ".$Sujet;
				$date_c=date("Ymd");
				$rq = "INSERT INTO cahiertxt (id_auteur,login,date,afaire,datafaire ) 
	       		VALUES ( '{$_POST['numrubri']}','{$_SESSION['login']}', '$date_c',  '$Dev', '{$_POST['data']}')";
	        	// lancer la requête
				$result = mysql_query($rq);
				if (!$result)  
					{                           
					echo "<p>Votre devoir n'a pas pu être enregistré à cause d'une erreur système".
					"<p></p>" . mysql_error() . "<p></p>";
					// refermer la connexion avec la base de données
					mysql_close();    
					exit();
					}
			}
			
//traitement de devoirs multiples
		if ((isset($_SESSION['sel_cl']) ))
 			{
 			for ($a=0; $a < count ($_SESSION['sel_cl'])  ; $a++)
				{
				$gr=explode('#',$_SESSION['sel_cl'][$a]);
				$groupe=$gr[0];
				$idr=$gr[1];
 				$rq = "INSERT INTO devoir (date, creneau,login, matiere, sujet, classe, durée ) 
 				VALUES ( '{$_POST['data']}','{$_POST['creneau']}', '$login', '{$_POST['matière']}', 
 				'$Sujet', '$groupe', '$durée')";
							
				// lancer la requête
				$result = mysql_query($rq);
				// Si l'enregistrement est incorrect 
				if (!$result)  
					{                           
					echo "<p>Votre devoir n'a pas pu être enregistré à cause d'une erreur système".
					"<p></p>" . mysql_error() . "<p></p>";
					// refermer la connexion avec la base de données
					mysql_close();    
					exit();
					}
					else 
			{
			if (!mb_ereg("^Cours",$_POST['classe'])) 
			{
			//enregistrement dans le travail à faire pour les autres classes
				$Dev= "Préparer le Devoir surveillé : ".$Sujet ;
				$date_c=date("Ymd");
				$rq = "INSERT INTO cahiertxt (id_auteur,login,date,afaire,datafaire ) 
	       		VALUES ( '$idr','{$_SESSION['login']}', '$date_c',  '$Dev', '{$_POST['data']}')";
	        	// lancer la requête
				$result = mysql_query($rq);
				if (!$result)  
					{                           
					echo "<p>Votre devoir n'a pas pu être enregistré à cause d'une erreur système".
					"<p></p>" . mysql_error() . "<p></p>";
					// refermer la connexion avec la base de données
					mysql_close();    
					exit();
					}
				}
//modif			
			//envoi d'un mail si les liste de difusion sont activées
			if (($_SESSION['liste']==1) && (!mb_ereg("^Cours",$_POST['classe']))) 
				{	
				// destinataire
				//recherche du groupe classe dans l'annuaire
				$classe_dest="";
				$classe_courte=explode('_',$groupe);
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
				//on évalue le timestamp de la date du devoir	
				$dat_new=strToTime($_POST['data']);	
				//Le destinataire 
				$mailTo = $grp_cl[0]["cn"];
				//Le sujet
				$mailSubject = "Nouveau devoir";
				//Le message
				$mailBody = " CECI EST UN MESSAGE AUTOMATIQUE, MERCI DE NE PAS REPONDRE.  \n \n Un devoir de "
				. $_POST['matière']." a été programmé le ".  strftime("%A %d %B %Y",$dat_new);
				//l'expéditeur
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

	
//Suppression d'un devoir après confirmation
if (isset($_GET['delrub']) && isset($_GET['numd'])  && $_GET['TA']==$_SESSION['RT']) 
	{
	$action=$_GET['delrub'];
	$cible=$_GET['numd'];
	
	//le devoir existe-t-il ?
	$rq = "SELECT id_ds,login,date,matiere,creneau,sujet,durée,classe FROM devoir WHERE id_ds='$cible' and login='$login' ";
	// lancer la requête
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
	{
	$dat_supp=$row[2];
	$mat_supp=$row[3];
	$cren_supp=$row[4];
	$suj_supp=$row[5];
	$dur_supp=$row[6];
	$cla_supp=$row[7];
	}
	//effacement de l'enregistrement
	if (($nb==1) &&($action==1245))
		{
		if (!mb_ereg("^Cours",$cla_supp))	
		$rq = "DELETE  FROM devoir WHERE id_ds='$cible' and login='$login' LIMIT 1";
		else
		$rq = "DELETE  FROM devoir WHERE  login='$login' and date='$dat_supp' and matiere='$mat_supp' and creneau='$cren_supp' and 
		sujet='$suj_supp' and durée= '$dur_supp'";
		
		$result2 = @mysql_query ($rq) or die (mysql_error());
		//envoi d'un mail
		if ($_SESSION['liste']==1)
			{	
			//recherche du groupe classe dans l'annuaire
			$classe_dest="";
			$classe_courte=explode('_',$_GET['klasse']);
			$classe_dest=$classe_courte[count($classe_courte)-1];
			//include "$BASEDIR/lcs/includes/headerauth.inc.php";
			//include "$BASEDIR/Annu/includes/ldap.inc.php";
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
				.$mat_supp." prévu le  " .strftime("%A %d %B %Y",$dat_new)." a été supprimé  ";
				//l'expéditeur
				$mailHeaders = "From: Cahier\ de\ textes\n";
				//envoi mail
				if ($classe_dest!="") mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
				
			}
		//suppression de l'enregistrement dans le cdt
		$suj_dev= "Préparer le Devoir surveillé : ".$suj_supp;			
		$rq = "DELETE  FROM cahiertxt WHERE id_auteur='{$_GET['numrubr']};' and  login='{$_SESSION['login']}' and afaire='$suj_dev' 
		and datafaire ='$dat_supp' LIMIT 1";
		$result2 = @mysql_query ($rq) or die (mysql_error());	
		}
	}
//fin de suppression d'un devoir

//mémorisation des paramètres POST classe  matière et numéro de rubrique renvoyés par le formulaire
if (isset($_POST['classe'])){$clas= $_POST['classe'];}
if (isset($_POST['matière'])){$mati= $_POST['matière'];}
if (isset($_POST['numrubri'])){$numrub= $_POST['numrubri'];}

//recherche de la matière et la classe de la rubrique active du cahier de textes
if (isset($_GET['rubrique']))
	{ 
	$rq = "SELECT classe,matiere FROM onglets
	WHERE id_prof='{$_GET['rubrique']}'  ";
	// lancer la requête
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result);  
	//on récupère les données
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{$clas=$enrg[0];//classe
		$mati=$enrg[1];//matière
		}
	$numrub=$_GET['rubrique'];
	}
	
			
			
//mémorisation des paramètres GET classe matière et numéro de rubrique renvoyés par le formulaire	
if (isset($_GET['ladate']))
 {
 $clas=$_GET['klasse'];
 $mati=$_GET['matière'];
 $numrub=$_GET['numrubr'];

 
 }
?>
<HTML>
<HEAD>
<TITLE>Planning</TITLE>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</HEAD>
<BODY>
<div class="plan-titre">Planning des Devoirs Surveillés de <?php echo $clas;?></div>
<?
//Modif groupes
//si la classe est un groupe
if (mb_ereg("^Cours",$clas)) 
	{
		
	$ct1cours=true;
	$groupe=$clas;
$uids = search_uids ("(cn=".$groupe.")", "half");
$liste_classe=array();
$i=0;
for ($loup=0; $loup < count($uids); $loup++)
	{
	$logun= $uids[$loup]["uid"];
	if (is_eleve($logun)) 
		{
		$groups=people_get_classe ($logun);
		if (count($groups))
			{
			for($n=0; $n<count($classe); $n++)
				{ 
				if ((mb_ereg("(_$classe[$n])$",$groups)) || ($classe[$n]==$groups))
					{
					if (!in_array($classe[$n], $liste_classe)) 
						{	
						$liste_classe[$i]=$classe[$n];
						$i++;
						}
					break;
					}
				}
			}
					
		}
	}
	//initialisation des variables de session
	unset ($_SESSION['sel_cl']);
	
	//mémorisation des données dans des variables de session		
	$_SESSION['sel_cl']=$liste_classe;
	}
	
//eom	
//Affichage conditionnel d'un message d'erreur
if ((isset($_POST['enregistrer'])) && ($durée=="")&& $_POST['TA']==$_SESSION['RT'])
 echo '<H6 class="erreur">'.$message. ' : Durée erronée</H6>';

//Affichage du formulaire de saisie du sujet du devoir et de la durée
if (isset($_GET['ladate']) &&(isset($_GET['inser'])))
	{
	echo '<FORM id="form-ajt-dev" ACTION="'.$_SERVER["PHP_SELF"].'" METHOD="POST">
	<SCRIPT language="Javascript" src="../Includes/barre_java.js"></SCRIPT>
	<INPUT name="JourCourant" type="hidden" id="JourCourant" value="'. $_GET["jc"].'">
	<INPUT name="classe" type="hidden" id="classe" value="'. $_GET['klasse'].'">
	<INPUT name="matière" type="hidden" id="matière" value="'. $_GET['matière'].'">
	<INPUT name="creneau" type="hidden" id="creneau" value="'. $_GET['creneau'].'">
	<INPUT name="login" type="hidden" id="login" value="'. $login.'">
	<INPUT name="data" type="hidden" id="data" value="'.$_GET["ladate"].'">
	<INPUT name="numrubri" type="hidden" id="numrubri" value="'.$numrub.'">
	<INPUT name="TA" type="hidden"  value="'. $_SESSION['RT'].'">
	<p>Intitulé du devoir :<INPUT class="text" TYPE=TEXT NAME="sujet" SIZE=28 MAXLENGTH=28>Durée :<INPUT class="text" TYPE=TEXT NAME="durée" SIZE=1 MAXLENGTH=1>heure(s)&nbsp;';
	if ($ct1cours) 
		{
		echo '</P><P> Ce devoir sera diffusé en  ';
		for ($j=0;$j<count($liste_classe);$j++)
			{
			echo $liste_classe[$j]." ";	
			}
			
		}
	else
	echo '<a id="bt-select" href="diffuse_devoirs.php?rubrique='.$numrub.'" onClick="diffusedev_popup('.$numrub.'); return false" ></A>';
	echo'	<INPUT class="bt" TYPE=SUBMIT NAME="enregistrer" VALUE=""></P>
	</FORM>';
	}
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
$rq = "SELECT id_ds, DATE_FORMAT(date,'%d'),DATE_FORMAT(date,'%m'),DATE_FORMAT(date,'%Y'), creneau, matiere, sujet,  login, durée FROM devoir 
WHERE date>='$datdebut' AND date<='$datfin'  AND classe= '$clas' ORDER BY date asc, creneau asc";
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
					$suj[$j][$h][$col] = stripslashes($row[6]);//sujet
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
<FORM action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" name="planning" id="planning">
<!-- Affichage des boutons -->	
	<INPUT name="JourCourant" type="hidden" id="JourCourant" value="<?php echo $Lundi;?>">
	<INPUT name="classe" type="hidden" id="classe" value="<?php echo $clas;?>">
	<INPUT name="matière" type="hidden" id="matière" value="<?php echo $mati;?>">
	<INPUT name="numrubri" type="hidden" id="numrubri" value="<?php echo $numrub;?>">
	<TABLE id="btn-plan-cdt">
		<TR>
			<TD width="50" align="center" ><INPUT type="submit" name="mprec" value="<<" title="Mois précédent" class="bt50"></TD>
			<TD width="50" align="center" ><INPUT type="submit" name="prec" value="<" title="Semaine précédente" class="bt50"></TD>
			<TD width="150" align="center" ><INPUT type="submit" name="quit" id="quit" value="" class="bt-exit"></TD>
			<TD width="50" align="center" ><INPUT type="submit" name="suiv" value=">" title="Semaine suivante" class="bt50"></TD>
			<TD width="50" align="center" ><INPUT type="submit" name="msuiv" value=">>" title="Mois suivant" class="bt50"></TD>
			
		</TR>
	</TABLE>
	
	<TABLE id="plan-cdt" CELLPADDING=1 CELLSPACING=2>
		<thead> 
			<th><img src="../images/livre.gif">  </th>
<?php
// Affichage des jours et dates de la semaine en haut du tableau"j-M-Y",
	for ($i=0; $i<=5; $i++) {
		$TS = $Lundi+$i*86400;
		echo '<td width="14.5%" colspan="2">'.LeJour($TS)."<BR>".datefr($TS)."</td>\n";
	}
?>
		</thead>
		<tbody>
		<tr>
<?php
	$horaire = array("M1<br>","M2<br>","M3<br>","M4<br>","M5<br>",
	"S1<br>","S2<br>","S3<br>","S4<br>","S5<br>");
	for ($h=0; $h<=9; $h++) 
		{//a
		//Affichage de la désignation des créneaux horaires
		
		echo "<TH>".$horaire[$h].date('G:i',$deb[$h])."-".date('G:i',$fin[$h])."</TH>\n";
		
		
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
									echo '<TD class="passe">Passé</TD>' ."\n";
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
								'<BR><img alt="aide"   src="../images/planifier-cdt-aide.png"  title ="'.$suj[$j][$h][0].' " />
								<a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j].
								'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&matière='.$mati.'&suppr=yes&numdev='
								.$num[$j][$h][0].'&numrubr='.$numrub.'" title="Supprimer ce devoir">'.
								'<img alt="aide"   border="0"src="../images/planifier-cdt-supp.png"   /> </a>'."</TD>\n";
								if (!isset($plan[$j][$h][1])) 
									{//o
									echo '<TD   class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
									.'&inser=yes'.'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&matière='.$mati
									.'&numrubr='.$numrub.'" title="Planifier un devoir"><img src="../images/planifier-cdt-plus.png" alt="-¤-"></a></TD>'."\n";
									}//o
								elseif ($plan[$j][$h][1]=="R")  
									{//p
									echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.$mat[$j][$h][1].
									'<BR><img alt="aide"   src="../images/planifier-cdt-aide.png"  title ="'.$suj[$j][$h][1].'" /> 
									<a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j].
									'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&matière='.$mati.'&suppr=yes&numdev='
									.$num[$j][$h][1].'&numrubr='.$numrub.'" title="Supprimer ce devoir">'.
									'<img alt="aide" src="../images/planifier-cdt-supp.png"> </a>'."</TD>\n";
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
									.'&inser=yes'.'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&matière='.$mati
									.'&numrubr='.$numrub.'" title="Planifier un devoir"><img src="../images/planifier-cdt-plus.png" alt="-¤-"></a></TD>'."\n";
									}//s
								elseif ($plan[$j][$h][1]=="R")   
									{//t
									echo '<TD  rowspan="'.$dur[$j][$h][1].'" class="reserve">'.$mat[$j][$h][1].
									'<BR><BR><img alt="aide"   src="../images/help-info.png"  title ="'.$suj[$j][$h][1].'" />&nbsp; &nbsp; &nbsp; 
									<a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j].
									'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&matière='.$mati.'&suppr=yes&numdev='
									.$num[$j][$h][1].'&numrubr='.$numrub.'" title="Supprimer ce devoir">'.
									'<img alt="aide"  border="0" src="../images/b_drop.png"> </a>'."</TD>\n";
									}//t
									}//r
								} //q
								
								elseif (!isset($plan[$j][$h][0])) 
						{//l
						if (!isset($plan[$j][$h][1]))
						echo '<TD colspan="2"  class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
						.'&inser=yes'.'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&matière='.$mati
						.'&numrubr='.$numrub.'" title="Planifier un devoir"><img src="../images/planifier-cdt-plus.png" alt="-¤-"></a></TD>'."\n";
						else
						echo '<TD class="libre"><a href="'.$_SERVER['PHP_SELF'].'?ladate='.$date[$j]
						.'&inser=yes'.'&jc='.$Lundi.'&klasse='.$clas.'&creneau='.$h.'&matière='.$mati
						.'&numrubr='.$numrub.'" title="Planifier un devoir"><img src="../images/planifier-cdt-plus.png" alt="-¤-"></a></TD>'."\n"; 
						}//l
							
					//cellules de séparation à la mi-journée			
					if (($h==4)&&($j==5)) echo '<TR border=0><TD class="mi-jour" colspan="13"></TD></TR>';	
				}	//b
			echo "</TR>\n";
		
		}//a
?>
		</tbody>
	</TABLE>
	
<H5>Pour planifier un nouveau devoir, cliquer sur le créneau horaire correspondant au début du devoir</h5>
	</FORM>
<?
//Affichage d'une boite de confirmation d'effacement
if (isset($_GET["suppr"]))
	{
	echo "<script type='text/javascript'>";
	echo "if (confirm('Confirmer la suppression de ce devoir (Vous ne pouvez supprimer que vos devoirs !!)')){";
	echo ' location.href = "';echo $_SERVER['PHP_SELF'];echo'" + "?numd=" +'." '"; echo $_GET["numdev"] ;echo "'"
	.' + "&delrub=" +'."1245".' + "&ladate=" +'."'";echo $date[0];echo "'".'+ "&jc="+'."'".$Lundi."'".'
	+ "&klasse="+'."'".$clas."'".'+ "&numrubr="+'."'".$numrub."'".'+ "&matière="+'."'".$mati."'".' + "&TA=" +'." '". $_SESSION['RT']."'".' ;}</script>';
	}
  echo '</div>'; 	//fin du contenu
echo '</div>'; 	//fin du container
include ('../Includes/pied.inc'); 
?>
</BODY>
</HTML> 
