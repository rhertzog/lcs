<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'impression -
			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();

//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;
//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
//si prof, connexion à la base de donnees
require_once ('../Includes/config.inc.php');
?>

<!-- cahier_textes_prof.php version 1.0 par Ph LECLERC - Lgt "Arcisse de Caumont" 14400 BAYEUX - philippe.leclerc1@ac-caen.fr -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes numerique</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK rel="stylesheet" type="text/css" href="../style/style.css"  media="screen">
<link rel="stylesheet" href="../style/style_imp.css" type="text/css" media="print" />
</head>

<body >
 
 <style>
<!--
@page { size: 21cm 29.7cm; margin-left: 1.2cm; margin-right: 1cm }
a:link {text-decoration:none; color: #000000; font-family:   arial, verdana ; font-size : 8pt }
a:visited {text-decoration: none; color: #000000; font-family: arial, verdana ; font-size: 8pt}
a:active {text-decoration: none; color: #000000; font-family: arial, verdana ; font-size: 10pt}
a:hover {text-decoration: none; color: #000000; background-color: #FFFFCC ;font-family: arial, verdana ; font-size: 8pt}
-->
</style>

<?
$tsmp=time();
$tsmp2=time();
$cours="";//variable temporaire de $Cours (avec une majuscule) pour un UPDATE
$afaire="";//variable temporaire de $Afaire (avec une majuscule) pour un UPDATE
$article="";

// Cette fonction cree un menu calendier : mois, jours et annees, initialise à la date du timestamp precise  + offset 
function calendrier_auto($offset,$var_j,$var_m,$var_a,$tsmp)
//offset=nbre de jours / au timestmp ,var_j,var_m,var_a=nom des variables associees pour la bd ,$tsmp=timestamp
{ 
// Tableau indexe des jours
$jours = array (1 => '01', '02', '03', '04', '05','06', '07', '08', '09', '10', '11','12','13','14','15',
						'16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');

// Tableau indexe des mois
  $mois = array (1 => '01', '02', '03', '04', '05', 
          '06', '07', '08', '09', '10', '11','12');
$dateinfo=getdate($tsmp);
$jo=date('d',$dateinfo['0']+($offset*86400));
$mo=date('m',$dateinfo['0']+($offset*86400));
$ann=date('Y',$dateinfo['0']+($offset*86400)); 

// Creation des menus deroulants
 //les jours
  echo "<select name=$var_j>\n";
  foreach ($jours as $cle => $valeur)
  { echo "<option valeur=\"$cle\"";
  if ($cle==$jo) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
  echo "</select>\n";
//les mois
  echo "<select name=$var_m>";
  foreach ($mois as $cle => $valeur)
  { echo "<option valeur=\"$cle\"";
  if ($cle==$mo) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
  echo "</select>\n";
//les annees
  echo "<select name=$var_a>";
  $annee = 2005;
  while ($annee <= 2015)
  { echo "<option valeur=\"$annee\"";
  if ($annee==$ann) {echo 'selected';}
  echo ">$annee</option>\n";
    $annee++;
  }
  echo "</select>\n";
}
//Fin de la fonction calendrier_auto().

$sens="";
//on recupère le nom de l'archive
if (isset($_GET['arch'])) {
$arch=$_GET['arch'];} 
elseif 
(isset($_POST['archi'])) {
$arch=$_POST['archi'];}
else
{$arch="";}
//on recupère le paramètre de la rubrique
if (isset($_GET['rubrique'])) {
$cible=$_GET['rubrique'];} 
elseif 
(isset($_POST['rubriq'])) {
$cible=$_POST['rubriq'];}
else
{$cible="";}

//recherche de la matière et la classe de la rubrique active du cahier de textes
if (isset($cible))
	{ 
	$rq = "SELECT classe,matiere,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets$arch
	WHERE id_prof='$cible'  ";
	// lancer la requête
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result); 
	//on recupère les donnees
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{$classe_active=$enrg[0];//classe
		$mati_active=$enrg[1];//matière
		$tampon=$enrg[2];
		$datetampon=$enrg[3];
		}
	
	}
//recuperation du paramètre timestamp pour memorisation
if (isset($_GET['tsmp'])) 
{$tsmp=$_GET['tsmp'];} 
// si OK a ete cliquer
if (isset($_POST['valider']))
	{ // Traiter le formulaire
		if (isset($_POST['Option1'])) {$sens=$_POST['Option1'];}
	//la date, le timestamp 
		$date_c = $_POST['an_c'].$_POST['mois_c'].$_POST['jour_c'];
		$tsmp=mkTime(0,0,1,$_POST['mois_c'],$_POST['jour_c'],$_POST['an_c']) + 2592000;

include_once ('../Includes/markdown.php'); //convertisseur txt-->HTML

// Connexion à la base de donnees
require_once ('../Includes/config.inc.php');

//creer la requête
if ($cible!="") 
{//laboration de la date limite à partir de la date selectionnee
$dat=date('Ymd',$tsmp-2592000);//2592000=nbre de secondes dans 30 jours
}

	

//creer la requête
$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique FROM cahiertxt$arch
 WHERE id_auteur=$cible AND login='{$_SESSION['login']}' AND date>=$dat ORDER BY date $sens ,id_rubrique desc";
 
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?
$nb2 = mysql_num_rows($result); 
if ($tampon == 1) echo '<img src="../images/tampon.png" " alt="visa" ; align="middle";  ><font color="#FF0000" size="2">le '.$datetampon.'</font>';

echo('<TABLE WIDTH=100% BORDER=0 CELLPADDING=2 CELLSPACING=1 bordercolor=#E6E6FF >
	');
 
	
  if ($nb2>0 )
		{echo "<caption><h5>Classe : <B>".$classe_active ."</B>&nbsp;&nbsp;&nbsp;Mati&egrave;re : &nbsp;<b> ".$mati_active."</b></h5></caption>";}

	while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
  { 
  $textcours=markdown($ligne[1]);
  $textafaire=markdown($ligne[2]);
  
	//debut
	echo ("  <TR><TD color=\"#FFFFFF\" ></TD></TR>
  
  <TR VALIGN=middle >
		<TD align=left  BGCOLOR=\"#D4D4D4\">			
		<font face=\"Arial\"color=\"#000000\"size='1'><B>S&eacute;ance du $ligne[0]</B></font>
		</TD> <TD color=\"#FFFFFF\" ></TD>");
		echo "
		</TR>
		<TR VALIGN=TOP >
		<TD align=left WIDTH=100% colspan=3 BGCOLOR=\"#FFFFFF\">
		<font face='Arial'   size='2'>$textcours</font>
		</TD>
		
	</TR>";
	if($ligne[2]!="") echo ("
	<TR VALIGN=TOP >
		<TD WIDTH=100% colspan=3 BGCOLOR=\"#FFFFFF\">
			
			<font face=\"Arial\"color=\"#000000\"size='1'> <B>A faire pour le :</B> $ligne[3] <BR> </font>
			<font face=\"Arial\" size='2'>$textafaire</font>
		
	</TR>");
	//fin
	echo("<TR></TR><TR></TR>");

  }
  echo "</table>";	exit;
	}




/*
   ================================
   - Traitement de la barre des menus  -
   ================================
*/


/*================================
   -      Affichage du formulaire  -
   ================================*/
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?rubrique=<?echo $cible;?>&arch=<?echo $arch;?>" method="post" name= "cahtxt" >
<fieldset>
<I><FONT FACE="Arial"></I> 
<div align="left">
<H4>Imprimer le contenu du cahier de textes de la classe  de <B> <?echo $classe_active .'</B> en <B>'. $mati_active ?></B>
 depuis le :
	<?calendrier_auto(-365,'jour_c','mois_c','an_c',$tsmp);?>
<input type="hidden" name="numrub" value= "<? echo $ch ; ?>">

<P><INPUT TYPE=RADIO NAME="Option1" VALUE="asc" <? IF (($sens=="asc")||($sens=="")) echo "CHECKED" ; ?>>par date croissante &nbsp;&nbsp;&nbsp;&nbsp
	<INPUT TYPE=RADIO NAME="Option1" VALUE="desc"<? IF ($sens=="desc") echo "CHECKED" ; ?>>par	date d&eacute;croissante 
	 </P>
	 <UL>
	<LI><P>En cliquant sur OK, le contenu du cahier de texte appara&icirc;t
	dans une nouvelle fen&ecirc;tre. 
	</P>
	<LI><P>Avec Firefox, cocher l'option &quot;Imprimer le fond de page&quot;
	du menu mise en page.</P>
	<LI><P>Avec Internet Explorer, cliquez sur Outils, options Internet
	, onglet  &quot;avanc&eacute;&quot; et cocher la case &quot;imprimer
	les couleurs et les images&quot;</P>
	<LI><P>Utiliser la fonction Imprimer de votre navigateur pour
	imprimer le contenu de la fen&ecirc;tre</P>
	<LI><P>Fermer la fen&ecirc;tre pour revenir au cahier de texte</P>
</UL>
</div>
<div align="center">
<input align="center" type="submit" name="valider" value="OK" > 
</div>
</fieldset></H4>
</FORM>
<?

  include ('../Includes/pied.inc'); 
?>
</body>
</html>
