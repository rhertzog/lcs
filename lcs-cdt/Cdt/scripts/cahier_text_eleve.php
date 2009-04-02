<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script du cahier de textes ELEVE -
   			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) ) exit; 
include ('../Includes/data.inc.php');
include_once("../Includes/fonctions.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");	
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");  

//si clic sur Planning des  devoirs  
if (isset($_POST['plan']))
	{$clas=$_POST['numrub'];
	header ("location:plan_eleves.php?classe=$clas");
	exit();
	}


//teste si $x fait partie des classes autoris�es 
function is_authorized($x) {
	$flg="false";
	foreach ($_SESSION['saclasse'] as $cl� => $valeur)
	  { 
	  if ($valeur==$x)
	  	 {
	  		$flg="true";
	 	 	break;
	  	 }
	  }
	return $flg;
}

//contr�le des param�tres $_GET
if ((isset($_GET['div'])) && (isset($_SESSION['saclasse'])))
	{	
	if (is_authorized($_GET['div'])=="false")   exit;
	}

if ((isset($_GET['mlec547trg2s5hy'])) && (isset($_SESSION['saclasse'])))
	{
	if (is_authorized($_GET['mlec547trg2s5hy'])=="false")  exit;
	}
		
//si clic sur un lien 	
if(isset($_GET['nvlkzei5qd1egz65'])) 
	$code=$_GET['nvlkzei5qd1egz65'];	
	else
	$code="";

//premi�re passe
	if ($code!='cv5e8da�rfz8ge69&�') 
	{//initialisation de la variable 
	if (isset($_SESSION['saclasse'][1])) $ch=$_SESSION['saclasse'][1]; 
	else $ch=$classe[0];
	} 
//initialisation 
$tsmp=0;
if(isset($_GET['div'])) 
$ch=$_GET['div'];
?>
<! Cahier_texte/_eleve.php version 1.3 par Ph LECLERC - Lgt "Arcisse de Caumont" 14400 BAYEUX - philippe.leclerc1@ac-caen.fr >
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes num�rique</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
#navlist
{
color: white;
background:#336699;
border-bottom: 0.2em solid #336699;
border-right: 0.2em solid #336699;
padding: 0 1px;
margin-left: 0;
width: 12em;
font: normal 0.8em Verdana, sans-serif;
}

#navlist li
{
list-style: none;
margin: 0;
font-size: 1em;
}

#navlist a
{
display: block;
text-decoration: none;
margin-bottom: 0.5em;
margin-top: 0.5em;
color: white;
background: rgb(0,64,128);
border-width: 1px;
border-style: solid;
border-color: #5bd #035 #068 #6cf;
border-left: 1em solid #fc0;
padding: 0.25em 0em 0.4em 0.75em;
}

#navlist a#courant { border-color: #5bd #035 #068 #f30; }

#navlist a


{
width: 99%;
/* necessaire seulement pour Internet Explorer */
}

#navlist a
{
voice-family: "\"}\"";
voice-family: inherit;
width: 9.6em;
/* Tantek-hack should only used if Internet-Explorer 6 is in standards-compliant mode */
}

#navcontainer>#navlist a
{
width: auto;
/* only necessary if you use the hacks above for the Internet Explorer */
}

#navlist a:hover, #navlist a#courant:hover
{	
background: #28b;
border-color: rgb(51,51,51) #6cf #5bd #fc0;
padding: 0.4em 0.35em 0.25em 0.9em;
}

#navlist a:active, #navlist a#courant:active
{
background: #336699;
border-color: #069 #6cf #5bd white;
padding: 0.4em 0.35em 0.25em 0.9em;
}
</style>
</head>

<body bgcolor="#FFFFFF" link="#000000" vlink="#000000" alink="#000000" BACKGROUND="../images/espperso.jpg">
 <style>
 <!--
a:link {text-decoration:none; color: #000FF; font-family:   arial, verdana ; font-size :10pt }
a:visited {text-decoration: none; color: #999999; font-family: arial, verdana ; font-size: 10pt}
a:active {text-decoration: none; color: #000099; font-family: arial, verdana ; font-size: 10pt}
a:hover {text-decoration: none; color: #990000; font-family: arial, verdana ; font-size: 10pt}
a.actif:link {text-decoration:none;color: #ff0000;background-color: #4169E1; font-family:   arial, verdana ; font-size : 10pt }

<?
if (eregi('msie', $HTTP_USER_AGENT) )
echo '
#boite { position:absolute; top:45px; left:160px;}';
else echo '
#boite { position:absolute; top:40px; left:140px; }';

?>

-->
</style>

<?
$tsmp=time();

// Cette fonction cr�e un menu calendier : mois, jours et ann�es, initialis� � la date du timestamp + offset 
function calendrier_auto($offset,$var_j,$var_m,$var_a,$tsmp)
//offset=nbre de jours / au timestmp ,var_j,var_m,var_a=nom des variables associ�es pour la bd ,$tsmp=timestamp
{ 
// Tableau index� des jours
$jours = array (1 => '01', '02', '03', '04', '05','06', '07', '08', '09', '10', '11','12','13','14','15',
						'16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');

// Tableau index� des mois
  $mois = array (1 => '01', '02', '03', '04', '05', 
          '06', '07', '08', '09', '10', '11','12');
$dateinfo=getdate($tsmp);
$jo=date('d',$dateinfo['0']+($offset*86400));
$mo=date('m',$dateinfo['0']+($offset*86400));
$ann=date('Y',$dateinfo['0']+($offset*86400)); 
// Cr�ation des menus d�roulants
 //les jours
 echo "<select name=$var_j>\n";
  foreach ($jours as $cl� => $valeur)
 	 { echo "<option valeur=\"$cl�\"";
 	 if ($cl�==$jo) {echo 'selected';}
  	echo ">$valeur</option>\n";
  	}
  echo "</select>\n";
//les mois
  echo "<select name=$var_m>";
  foreach ($mois as $cl� => $valeur)
 	 { echo "<option valeur=\"$cl�\"";
  	if ($cl�==$mo) {echo 'selected';}
 	 echo ">$valeur</option>\n";
 	 }
 echo "</select>\n";
//les ann�es
  echo "<select name=$var_a>";
  $ann�e = 2008;
  while ($ann�e <= 2015)
  	{ echo "<option valeur=\"$ann�e\"";
 	 if ($ann�e==$ann) {echo 'selected';}
  	echo ">$ann�e</option>\n";
   $ann�e++;
  }
  echo "</select>\n";
}
//Fin de la fonctioncalendrier_auto().

// Connexion � la base de donn�es
require_once ('../Includes/config.inc.php');

//r�cup�ration du param�tre classe pour m�morisation
if (isset($_GET['mlec547trg2s5hy'])) 
	{$ch=$_GET['mlec547trg2s5hy'];} 
//r�cup�ration du param�tre timestamp pour m�morisation
if (isset($_GET['tsmp'])) 
	{$tsmp=$_GET['tsmp'];} 
// si OK a �t� cliqu�
if ((isset($_POST['valider'])) || (isset($_POST['viser'])))
	{ // Traiter le formulaire
		//la classe
		if (strlen($_POST['mlec547trg2s5hy']) > 0)
		{ 
		$ch = stripslashes($_POST['mlec547trg2s5hy']);
		}
		//la date, le timestamp 
		$date_c = $_POST['an_c'].$_POST['mois_c'].$_POST['jour_c'];
		$tsmp=mkTime(0,0,1,$_POST['mois_c'],$_POST['jour_c'],$_POST['an_c']) + 5184000;
	}
	
if ( isset($_POST['viser']))
	{
	 $dat_vis= date('Y-m-d');
	 $rub_vis=$_POST['rub_activ'];
	 //echo $dat_vis." ".$rub_vis."-";
	 $rq = "UPDATE  onglets SET visa='1', datevisa='$dat_vis'  
				WHERE id_prof='$rub_vis'"; 
				
	// lancer la requ�te
		$result = mysql_query($rq); 
		if (!$result)  // Si l'enregistrement est incorrect
			{                           
			 echo "<p>Votre rubrique n'a pas pu �tre enregistr�e � cause d'une erreur syst�me".
			"<p></p>" . mysql_error() . "<p></p>";
			}			
	}	
	
//on r�cup�re le param�tre de la rubrique (prof/matiere/classe)
if (isset($_GET['rubrique'])) 
	$cible=$_GET['rubrique'];
	else $cible="";
	
if ((isset($_POST['rub_activ'])) &&  (isset($_POST['viser']))) 
	{
	$cible=$_POST['rub_activ'];
	}

//fin traitement formulaire

//Traitement de la barre des menus

// Cr�er la requ�te (R�cup�rer les rubriques de la classe) 
$rq = "SELECT id_prof FROM onglets
 WHERE classe='$ch' ORDER BY 'id_prof' asc ";

 // lancer la requ�te
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?
if ($nb>0)
	{
	//on r�cup�re les donn�es
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$num_ero[$loop]=$enrg[0];//num�ro de l'onglet
		$loop++;
		}
	if($cible==""){$cible=($num_ero[0]);}	
		}

//affichage du formulaire
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?nvlkzei5qd1egz65=cv5e8da�rfz8ge69&�" method="post">
<SCRIPT language="Javascript" src="../Includes/barre_java.js"></SCRIPT>
<fieldset>
<I><FONT FACE="Arial"></I> 
<div align="left">
<FONT size="2" color="#003399">Visualiser le contenu du cahier de textes de la classe  de
<?
//si on ne  connait pas la classe , on affiche un menu d�roulant  
if (!isset($_SESSION['saclasse']))
	{
	echo "<select name='mlec547trg2s5hy'style='background-color:#E6E6FA'>";
	foreach ($classe as $cl� => $valeur)
	  { echo "<option valeur=\"$valeur\"";
	  if ($valeur==$ch) {echo 'selected';}
	  echo ">$valeur</option>\n";
	  }
	echo "</select>";
	}
	//sinon on affiche uniquement la classe de l'�l�ve 
	elseif (count($_SESSION['saclasse'])==1) echo '<B>'.$_SESSION['saclasse'][1].'</B>';
	else 
	{
	echo "<select name='mlec547trg2s5hy'style='background-color:#E6E6FA'>";
	foreach ($_SESSION['saclasse'] as $cl� => $valeur)
	  { echo "<option valeur=\"$valeur\"";
	  if ($valeur==$ch) {echo 'selected';} 
	  echo ">$valeur</option>\n";
	  }
	echo "</select>";
	}
  ?>

depuis le :	<?calendrier_auto(-60,'jour_c','mois_c','an_c',$tsmp);?>
<input type="hidden" name="numrub" value= "<? echo $ch ; ?>">
<input type="hidden" name="rub_activ" value= "<? echo $cible; ?>">
<input type="submit" name="valider" value="OK"  style='background-color:#E6E6FA'> 
<INPUT TYPE="button" VALUE="Annuler" onClick="history.back()">&nbsp;&nbsp;&nbsp;&nbsp;
<INPUT TYPE="SUBMIT" NAME="plan" VALUE="Planning des devoirs" style='background-color:#E6E6FA'>
</div>
</fieldset>
<?
//Affichage du bouton pour apposer le visa  
if (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="Y")
	{
	echo '<div style="position:absolute; top:65px; left:15px;width:30px">
	<INPUT TYPE="SUBMIT" NAME="viser" VALUE="Apposer le visa" ></div>';
	}

?>
<p> <center>
<?
//Affichage du lien des absences 
if (ldap_get_right("Cdt_is_cpe",$_SESSION['login'])=="Y")
	{
	echo '<a href="./cpe.php"  target="_blank" ><b>Consultation des absences &nbsp;&nbsp;&nbsp;&nbsp;</b></A>';
	}
if ($_SESSION['cequi']=="eleve"	&& $FLAG_ABSENCE==1)
	echo 'Absences <A href="#" title="Absences " onClick="abs_popup(\''.$_SESSION['login'].'\',\'Vos absences et retards\'); return false" >
	 <img src="../images/b_calendar.png"  alt="hebdo"border="0"/>&nbsp;&nbsp;&nbsp;&nbsp;</A>';
	 
if (( isset($_SESSION['parentde'])) && ($FLAG_ABSENCE==1) && (!isset($_SESSION['login'])))
	{
	echo 'Absences : ';	
	foreach ( $_SESSION['parentde'] as $cle => $valcla)
		{
		echo '<A href="#" title="Absences de '.$valcla[1].'" onClick="abs_popup(\''.$valcla[0].'\',\''.$valcla[1].'\'); return false" >
		<img src="../images/b_calendar.png"  alt="hebdo"border="0"/>&nbsp;&nbsp;&nbsp;&nbsp;</A>';
		}
	}
?>

<a href="#"  onClick="taf_popup('<?echo $ch;?>'); return false" ><b> Travail A Faire des 5 prochains jours </b></A></center>
</p>
</FORM>
<?
// Cr�er la requ�te (R�cup�rer les rubriques de la classe) 
$rq = "SELECT prof,matiere,id_prof,prefix,visa,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets
 WHERE classe='$ch' ORDER BY 'id_prof' asc ";

 // lancer la requ�te
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
if ($nb>0)
	{
	//on r�cup�re les donn�es 
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$prof[$loop]=$enrg[0];//nom du prof
		$mat[$loop]=$enrg[1];//mati�re 
		$numero[$loop]=$enrg[2];//num�ro de l'onglet
		$pref[$loop]=$enrg[3];// pr�fixe
		$restr[$loop]=$enrg[4];//restric
		$visa[$loop]=$enrg[5];// 
		$datvisa[$loop]=$enrg[6];
		$loop++;
		}

	//modif

	echo "<table border=0 cellspacing=2 cellpadding=2>";
	// Affichage de la colonne de gauche

?>
<tr VALIGN=TOP><td width="160">
<?	
if($cible==""){$cible=($numero[0]);}
	
//cr�ation de la barre de menu , couleur de fond # pour cellule active
	//cr�ation du tableau , onglets �l�ves
	
	echo("<div align='left'><table  border='1' bordercolor='#C0C0C0'  cellspacing='0' cellpadding='0' bgcolor='#336699'>");
	echo "<tr><td align='center' bgcolor='#000066'><b><font face=\"arial\"color=\"#FFFFFF\"size=2> Rubriques  </font></b></td></tr>";
?>
<tr><td>
<div id="navcontainer">
<ul id="navlist">
<?		
	for($x=0;$x < $nb;$x++)
		{
		if ($cible == ($numero[$x])) 
		
			{
				echo ("<li id='active'><a href='cahier_text_eleve.php?nvlkzei5qd1egz65=cv5e8da�rfz8ge69&�&rubrique=
			$numero[$x]&mlec547trg2s5hy=$ch&tsmp=$tsmp' id='courant'>&nbsp;$mat[$x]&nbsp;<br>&nbsp;$pref[$x]  $prof[$x]&nbsp;"."</a></li>");
			
			/*echo("<tr><td   bgcolor='#4169E1' color='#ffffff'><font face=\"arial\"color=\"#FFFFFF\"size=2><b><div align='center' color='#FFFFFF'>
			&nbsp $mat[$x] &nbsp<br> &nbsp $pref[$x]   $prof[$x]&nbsp".
			"</a></div></B></font></td></tr>");
			//if (($restr[$x]) &&  $_SESSION['saclasse']!=$ch && $_SESSION['cequi']!="prof" && $_SESSION['cequi']!="administratif") $noAff=true;*/	
			if ($visa[$x]) {
			$vis="ok";
			$datv=$datvisa[$x];
			}
			
			}
			else 
			{
			echo ("<li><a href='cahier_text_eleve.php?nvlkzei5qd1egz65=cv5e8da�rfz8ge69&�&rubrique=
			$numero[$x]&mlec547trg2s5hy=$ch&tsmp=$tsmp'>&nbsp;$mat[$x]&nbsp;<br>&nbsp;$pref[$x]  $prof[$x]&nbsp;"."</a></li>");
			/*echo("<tr><td ><div align='center'><a href='cahier_text_eleve.php?nvlkzei5qd1egz65=cv5e8da�rfz8ge69&�&rubrique=
			$numero[$x]&mlec547trg2s5hy=$ch&tsmp=$tsmp'>&nbsp $mat[$x] &nbsp <br> &nbsp $pref[$x]  $prof[$x] &nbsp "."</a></div></td></tr>");
			*/
			}
		
		
		}
	 
	echo '</ul></div>';
	echo '</td></tr>';	
	echo('<tr VALIGN=TOP ><td  align="center" bgcolor="#FFFFFF"><img src="../images/author1.png" onMouseover="this.src=\'../images/author.png\'"
		onMouseout="this.src=\'../images/author1.png\'" border="0"></td></tr></table></div></td>');
		
	}
else 
	{ 
	// Il y a eu un os !
	  echo "<p><FONT face='Arial'size='3' COLOR='#FF0000'>Aucun enregistrement dans le cahier de textes de  $ch !</p><p></p></font>";
	}

//affichage du contenu du cahier de textes
if (!$noAff) 
		{
		include_once ('../Includes/markdown.php');//convertisseur text-->HTML

		//cr�er la requ�te
		if ($cible!="") 
		{//�laboration de la date limite � partir de la date selectionn�e
		$dat=date('Ymd',$tsmp-5184000);//2592000=nbre de secondes dans 30 jours
if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
		$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date FROM cahiertxt
		 WHERE id_auteur=$cible AND date>=$dat ORDER BY date desc";
		 
		// lancer la requ�te
		$result = @mysql_query ($rq) or die (mysql_error());

		// Combien y a-t-il d'enregistrements ?
		$nb2 = mysql_num_rows($result);

		echo '<td >';
		echo('<TABLE  width="800" BORDER=1 CELLPADDING=3 CELLSPACING=1 bordercolor=#E6E6FF >
			');
		while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
		  { 
		  $textcours=stripslashes(markdown($ligne[1]));//conversion du contenu
		 $textafaire=stripslashes(markdown($ligne[2]));//conversion du travail a faire
		  $jour=LeJour(strToTime($ligne[5]));
		  echo ("  
		  <TR VALIGN=TOP >
				<TD  align=left WIDTH=30% BGCOLOR=\"#336699\"> 
					<font face=\"Arial\"color=\"#FFFFFF\"size=2><B>S�ance du  ".$jour." $ligne[0]</B></font>
					</td><td>
				</TD>
				</TR>
				<TR VALIGN=TOP >
				<TD align=left WIDTH=100% colspan=2 BGCOLOR=\"#E6E6FA\">
					<font face='Arial' color='#0000cc' size=2>$textcours</font></TD></TR>");
								
			if($ligne[2]!="") 
			echo ("
			<TR VALIGN=TOP >
				<TD WIDTH=80% colspan=2 BGCOLOR=\"#E6E6FA\">
					
					<font face=\"Arial\"color=\"#cc0000\"size=2> <B>A faire pour le :</B> $ligne[3] <BR> </font>
					<font face=\"Arial\"color=\"#009900\"size=2>$textafaire</font></TD></TR>");
					
			echo("<TR></TR><TR></TR><TR></TR><TR></TR><TR></TR><TR></TR>");

		  }
		  echo "</table></td></tr></table>";
		}
	}
	else 
	echo "<td><font face='Arial' color='#CC0000'> L'acc�s � cette rubrique est r�serv� aux �l�ves de la classe !</font> </td></tr></table>";

	
	if ($vis == "ok") echo '<div id="boite"  >
			<img src="../images/tampon.png" " alt="visa" ; align="middle";  ><font color="#FF0000";>le '.$datv.'</font></div>';
include ('../Includes/pied.inc');
?>
</body>
</html>
