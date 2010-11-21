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
include "../Includes/check.php";
if (!check()) exit; 
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
	
if (isset($_POST['datelim']))
	{
	$dtajaf=$_POST['datelim'];
	}	
else 
	{$nextWeek = time() + (7 * 24 * 60 * 60);
	$dtajaf= date ('d/m/Y',$nextWeek);
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<TITLE></TITLE>
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	<link  href="../style/ui.all.css" rel=StyleSheet type="text/css">
	<link  href="../style/ui.datepicker.css" rel=StyleSheet type="text/css">
	<link  href="../style/ui.theme.css" rel=StyleSheet type="text/css">
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/jquery-1.3.2.min.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/ui.core.js"></script>  
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/ui.datepicker.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
	</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'>Cahier de textes : Personnel de direction</H1>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" TARGET="_BLANK">
<INPUT name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>">
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
<INPUT name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>">
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
<INPUT name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>">
<a name="cryp"></a>
<fieldset id="field7">
<legend id="legende"> G&#233;n&#233;ration d'un d'acc&#232;s (IA,IPR,IEN,...) </legend>
<?php
echo "<p>G&#233;n&#233;ration d'un lien d'acc&#232;s crypt&#233; au cahier de textes d'un professeur</p>";
echo '<ul>';
echo "<div id='afRle'><li>S&#233;lectionner le professeur : ";
echo "<select name='PROF2' style='background-color:#E6E6FA'>";
for ($loop=0; $loop <count($people); $loop++) 
 	{
 	$uname = $people[$loop]['uid'];
 	$nom = $people[$loop]["fullname"];
 	echo "<option value=\"$uname#$nom\"";
 	if ($uname.'#'.$nom ==$_POST['PROF2']) {echo 'selected';}
	echo ">$nom</option>\n";
	}
echo '</select>
<li>Date limite de validit&#233; :
<input id="datejaf" size="10" name="datelim" value="'. $dtajaf.'" readonly="readonly" style="cursor: text" >
<li><input type="checkbox" name="mel" value="yes"';
if ($_POST['mel']=="yes" || (!isset($_POST['Lien']))) echo 'checked';
echo '>Informer par mail le professeur concern&#233; 
</div></ul>
<input type="submit" name="Lien" value="" class="bt-valid" >';
//generation du lien
if (isset($_POST['Lien']))
	{
	$Morceaux=explode('/',$_POST['datelim']);
	$datelimite=mktime (23,59,1,$Morceaux[1],$Morceaux[0],$Morceaux[2]);	
	$prof=preg_split('/#/',$_POST['PROF2']);
	$aliasprof=$prof[0];
	$grain='$1$'.$Grain.'$';
	$chaine=$aliasprof.$datelimite;
	$logcrypt=crypt($chaine,$grain);
	$key= substr($logcrypt,-20,20);
	echo '<br /><br />';
	if (!stripos($_SERVER['HTTP_USER_AGENT'], "msie"))  
	echo '<legend id=legende>';
	echo "<a href= 'http://".$hostn."/Plugins/Cdt/index.php";
	echo "?prof=".$aliasprof.'&limit='.$datelimite.'&key='.$key;
	echo "'> Lien d'acc&egrave;s au cahier de texte de $prof[1]</a>	";
	if (!stripos($_SERVER['HTTP_USER_AGENT'], "msie"))  
	echo '</legend>';
	echo '
	<ol><H4 class="perso">
	<li> Copiez ce lien avec  un clic droit, et copiez le dans un mail destin&#233; au demandeur.
	<li> N\'essayez pas ce lien, il n\'est pas valide lorsque vous &#234;tes connect&#233; au LCS.';
	//envoi du mail
	if ($_POST['mel']=="yes")
		{
		//Le destinataire 
		$mailTo = $aliasprof;
		//Le sujet
		$mailSubject = "Lien d'acc\350s ";
		//Le message
		$mailBody1 = " CECI EST UN MESSAGE AUTOMATIQUE, MERCI DE NE PAS REPONDRE.\n \n ";
		$mailBody2 = " Un lien d'acc\350s \340 votre cahier de textes a \351t\351 g\351n\351r\351 par  "
		. $_SESSION['nomcomplet'].".\n\nCe lien est valide jusqu'au ". $_POST['datelim'];
		//l'expediteur
		$mailHeaders = "From: Cahier\ de\ textes\n";
		//envoi du mail
		 mail($mailTo, $mailSubject, $mailBody1.$mailBody2, $mailHeaders);
		 echo '<li> Le message suivant a &#233;t&#233; envoy&#233; &#224 '.$prof[1].' :<p class="absmod">'.$mailBody2.'</p>';
		}
	}
?>
</ol></H4>
</fieldset>
</form>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']) ?>" method="post" >
<INPUT name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>">
<fieldset id="field7">
<legend id="legende">Archives</legend>
<p>Ce formulaire vous permet de g&#233;n&#233;rer une archive du cahier de texte au format HTML devant &#234;tre conserv&#233;e pendant 5 ans. </p>
<input type="submit" name="Exporter" value="" class="bt-valid" >
</fieldset>
</form>
</BODY>
</HTML>
