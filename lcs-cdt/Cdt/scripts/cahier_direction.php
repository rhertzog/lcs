<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 13/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'interface personnel de direction  -
			_-=-_
   =================================================== */
session_name("Cdt_Lcs");
@session_start(); 
//error_reporting(0);
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//fichiers necessaires e l'exploitation de l'API
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";   
include "../Includes/config.inc.php";
//si la page est appelee par un utilisateur non "direction"
if (ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N") exit;
$cmd="hostname -f"; 
exec($cmd,$hn,$retour);
$hostn= $hn[0];

//redirection
if (isset($_POST['Laclasse']))
	{
	$cla=$_POST['CLASSE'];
	header ("location: cahier_text_eleve.php?mlec547trg2s5hy=$cla");
	exit();
	}
	
if (isset($_POST['Leprof']))
	{
	$prof2=preg_split('/#/',$_POST['PROF']);
	$_SESSION['aliasprof']=$prof2[0];
	$_SESSION['proffull']=$prof2[1];
	header("location: cahier_texte_prof_ro.php");
	}
	
if (isset($_POST['Exporter']))
	{	
	header ("location: ./exporthtml.php");
	}
	
if (isset($_POST['Abs']))
	{	
	header ("location: ./cpe.php");
	}	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<TITLE></TITLE>
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'>Cahier de textes : Personnel de direction</H1>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" TARGET="_BLANK">
<fieldset id="field7">
<legend id="legende"> Cahier de textes d'une classe </legend>
<?php
echo '<p>Visualisation des commentaires de la classe enti&#232;re et des cours &#224; effectifs r&#233;duits</p>';
echo '<ul>';
echo "<li>S&#233;lectionner la classe : ";
//affichage de la liste des classes
include ("../Includes/data.inc.php");
$jo="";
echo "<select name='CLASSE' style='background-color:#E6E6FA'>";
foreach ($classe as $clef => $valeur)
  { 
  echo "<option valeur=\"$valeur\"";
  if ($valeur==$jo) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
echo '</select></ul><input type="submit" name="Laclasse" value="" class="bt-valid" >';
?>
</fieldset>
</form>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" TARGET="_BLANK">
<fieldset id="field7">
<legend id="legende"> Cahier de textes d'un professeur</legend>
<?php
echo '<p>Visualisation de tout les commentaires du professeur</p>';
echo '<ul>';
echo "<li>S&#233;lectionner le professeur : ";
$uids = search_uids ("cn=Profs", "half");
$people = search_people_groups ($uids,"(sn=*)","cat");
echo "<select name='PROF' style='background-color:#E6E6FA'>";
for ($loop=0; $loop <count($people); $loop++) 
 	{
 	$uname = $people[$loop]['uid'];
 	$nom = $people[$loop]["fullname"];
 	echo "<option value=\"$uname#$nom\"";
 	if ($uname.'#'.$nom ==$_POST['PROF']) {echo 'selected';}
	echo ">$nom</option>\n";
	}
echo ' </select></ul><input type="submit" name="Leprof" value="" class="bt-valid" >';      							
?>
</fieldset>
</form>

<?php
if ($FLAG_ABSENCE==1) 
	{
	echo '<form action="'. htmlentities($_SERVER['PHP_SELF']).'" method="post"  TARGET="_BLANK" >
	<fieldset id="field7">
	<legend id="legende">Absences</legend>';
	echo '<p>Visualisation des absences par cr&#233;neau, par classe ou par &#233;l&#232;ve </p>';
	echo '<input type="submit" name="Abs" value="" class="bt-valid" >';
	echo '</fieldset></form>';
	}
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']).'#cryp'; ?>" method="post" ">
<a name="cryp"></a>
<fieldset id="field7">
<legend id="legende"> G&#233;n&#233;ration d'un d'acc&#232;s (IA,IPR,IEN,...) </legend>
<?php
echo "<p>G&#233;n&#233;ration d'un lien d'acc&#232;s crypt&#233; au cahier de textes d'un professeur</p>";
echo '<ul>';
echo "<li>S&#233;lectionner le professeur : ";
echo "<select name='PROF2' style='background-color:#E6E6FA'>";
for ($loop=0; $loop <count($people); $loop++) 
 	{
 	$uname = $people[$loop]['uid'];
 	$nom = $people[$loop]["fullname"];
 	echo "<option value=\"$uname#$nom\"";
 	if ($uname.'#'.$nom ==$_POST['PROF2']) {echo 'selected';}
	echo ">$nom</option>\n";
	}
echo '</select></ul><input type="submit" name="Lien" value="" class="bt-valid" >';
//generation du lien
if (isset($_POST['Lien']))
	{
$prof=preg_split('/#/',$_POST['PROF2']);
$aliasprof=$prof[0];
$grain='$1$'.$Grain.'$';
$nextWeek = time() + (7 * 24 * 60 * 60);
$chaine=$aliasprof.$nextWeek;
$logcrypt=crypt($chaine,$grain);
$key= substr($logcrypt,-20,20);
echo '<br /><br /><legend id="legende">';
echo "<a href= 'http://".$hostn."/Plugins/Cdt/index.php";
echo "?prof=".$aliasprof.'&limit='.$nextWeek.'&key='.$key;
echo "'> Lien d'acc&egrave;s au cahier de texte de $prof[1]</a>
</legend>";
echo '
<ol><H4 class="perso">
<li> Ce lien est valide 7 jours &#224; dater de sa cr&#233;ation.
<li> Copiez ce lien avec  un clic droit, et copiez le dans un mail destin&#233; au demandeur.
<li> N\'essayez pas ce lien, il n\'est pas valide lorsque vous &#234;tes connect&#233; au LCS.
</ol></H4>';
}
?>
</fieldset>
</form>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']) ?>" method="post" >
<fieldset id="field7">
<legend id="legende">Archives</legend>
<p>Ce formulaire vous permet de g&#233;n&#233;rer une archive du cahier de texte au format HTML devant &#234;tre conserv&#233;e pendant 5 ans. </p>
<input type="submit" name="Exporter" value="" class="bt-valid" >
</fieldset>
</form>
</BODY>
</HTML>
