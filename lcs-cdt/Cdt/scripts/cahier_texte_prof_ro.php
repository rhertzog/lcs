<?php
/* ===================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010 
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de consultationd'un  cahier de textes PROF -
			_-=-_
   =================================================== */
session_name("Cdt_Lcs");
@session_start();
include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
if ((ldap_get_right("Cdt_can_sign",$_SESSION['login'])=="N") && ((!isset($_SESSION['aliasprof']) || (!isset($_SESSION['proffull']))))) exit;
// Connexion a la base de donnees
include ('../Includes/config.inc.php');

// Creer la requ&egrave;ete.
$rq = "SELECT classe,matiere,id_prof FROM onglets
 WHERE login='{$_SESSION['aliasprof']}' ORDER BY classe ASC ";
 
// lancer la requ&egrave;ete
$result = @mysql_query ($rq) or die (mysql_error()); 

// si pas de rubrique, on redirige vers config_ctxt.php
if (mysql_num_rows($result)==0) 
	{
	?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<link href="../style/style.css" rel=StyleSheet type="text/css">
</head>

<body>
	<div id="first">
	<div class="prg">
	<h2><br /><br />Le cahier de textes de <?echo $_SESSION['proffull']; ?> ne comporte actuellement aucune rubrique.</h2>
		
	</div></div>
<?php
	mysql_close();	
	exit;
	}
include_once("/usr/share/lcs/Plugins/Cdt/Includes/fonctions.inc.php");	

?>

<!-- Fin de la page de premiere utilisation -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<link href="../style/style.css" rel=StyleSheet type="text/css">
	<link  href="../style/navlist-prof.css" rel=StyleSheet type="text/css">
	
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	
</head>

<body >
<?

//on recupere le parametre de la rubrique
if (isset($_GET['rubrique']))
	{
	$cible=$_GET['rubrique'];
	} 
elseif (isset($_POST['rubriq']))
	{
	$cible=$_POST['rubriq'];
	}

if ( isset($_POST['viser']))
	{
	 $dat_vis= date('Y-m-d');
	 //$rub_vis=$_POST['rub_activ'];
	 //echo $dat_vis." ".$rub_vis."-";
	 if (!isset($_SESSION['login'])) $numvisa=2; else $numvisa=1;
	 $rq = "UPDATE  onglets SET visa='$numvisa', datevisa='$dat_vis'  
				WHERE id_prof='$cible'"; 
				
	// lancer la requête
		$result = mysql_query($rq); 
		if (!$result)  // Si l'enregistrement est incorrect
			{                           
			 echo "<p>Votre rubrique n'a pas pu être enregistr&#233;e &#224; cause d'une erreur syst&#232;me".
			"<p></p>" . mysql_error() . "<p></p>";
			}
	 $dat_la=date('Y-m-d');
	 $rq = "UPDATE cahiertxt SET on_off='$numvisa' WHERE id_auteur='$cible' AND date<'$dat_la' AND datevisibi<='$dat_la' AND on_off='0' ";
	 // lancer la requête
		$result = mysql_query($rq); 
		if (!$result)  // 
			{                           
			 echo "<p>L'apposition du visa n'a pu être r&#233;alis&#233;e &#224; cause d'une erreur syst&#232;me".
			"<p></p>" . mysql_error() . "<p></p>";
			}					
	}	
	
/*
   ================================
   - Traitement de la barre des menus  -
   ================================
*/

// Creer la requete (Recuperer les rubriques de l'utilisateur) 
$rq = "SELECT classe,matiere,id_prof,visa,DATE_FORMAT(datevisa,'%d/%m/%Y') FROM onglets
 WHERE login='{$_SESSION['aliasprof']}' ORDER BY id_prof ASC ";

 // lancer la requ&egrave;ete
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?

//on recupere les donnees
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
	{$clas[$loop]=$enrg[0];
	$mat[$loop]=$enrg[1];
	$numero[$loop]=$enrg[2];
	$visa[$loop]=$enrg[3];// 
	$datvisa[$loop]=$enrg[4];
	$loop++;
	}
	
//on calcule la largeur des onglets
if($cible==""){$cible=($numero[0]);}
$nmax=$nb;
 
//cr&eacute;ation de la barre de menu 
echo '<div id="entete">';
echo '<br />';
echo '<div id="navcontainer">';
echo ("<ul id='navlist'>");
for($x=0;$x < $nmax;$x++)
	{	
		
		if ($cible == ($numero[$x]))
			{//cellule active	
			echo ("<li id='select'><a href='cahier_texte_prof_ro.php?rubrique=$numero[$x]'
			'onmouseover=\"window.status='';return true\" id='courant'>$mat[$x]<br />$clas[$x] "."</a></li>");	
			if ($visa[$x])
				{
				$vis=$visa[$x];
				$datv=$datvisa[$x];
				}
			}
		else 
			{
			if (!isset($mat[$x])) $mat[$x]="&nbsp;";
			if (!isset($clas[$x])) $clas[$x]="&nbsp;";
			if (!isset($numero[$x]))
			echo ("<li><a href='#'>$clas[$x]"."</a></li>");
			else
			{
			echo ("<li><a href='cahier_texte_prof_ro.php?rubrique=$numero[$x]'
			'onmouseover=\"window.status='';return true\">$mat[$x]<br />$clas[$x]"."</a></li>");
			}
			}
	}
echo '</ul>';
echo '</div>';
echo '</div>';
//affichage du visa
if ($vis) echo '<div id="visa-cdt'.$vis.'">'.$datv.'</div>';

?>
<!--bloc qui contient la saisie et le contenu du cahier de texte-->
<div id="container2">
<fieldset id="field7">
<legend id="legende"> Cahier de textes de <?echo $_SESSION['proffull']; ?>  &nbsp 
</legend>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
<INPUT TYPE="SUBMIT" NAME="viser" VALUE="" class="bt-visa"><br /><br />
<input type="hidden" name="rubriq" value= "<?php echo $cible; ?>">
<a href="mailto:<?php echo $_SESSION['aliasprof']."@".$domain; ?>" > <img src="../images/mail.png" alt="Envoyer un mail"
         title="Envoyer un mail &#224; <?php echo $_SESSION['proffull']; ?>" border=0 ></a>
</form>
<?
/*========================================================================
   - Affichage du contenu du cahier de textes  -
   =======================================================================*/
//include ("../Includes/fonctions.inc.php");  
include_once ('../Includes/markdown.php'); //convertisseur txt-->HTML
$dat_now=date('YmdHis');
//cr&eacute;er la requ&egrave;te
if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
	else setlocale("LC_TIME","french");
$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,on_off,DATE_FORMAT(datevisibi,'%d/%m/%Y') FROM cahiertxt
 WHERE (id_auteur=$cible) AND (login='{$_SESSION['aliasprof']}')  AND datevisibi<=$dat_now ORDER BY date desc ,id_rubrique desc";
 //echo $rq;exit;
// Ouvrir la connexion et selectionner la base de donnees
$dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) 
       OR die ('Connexion a MySQL impossible : '.mysql_error().'<br />');
mysql_select_db (DB_NAME)
       OR die ('Selection de la base de donnees impossible : '.mysql_error().'<br />');
// lancer la requete
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?
$nb2 = mysql_num_rows($result); 

echo '<div id="boite5">';
echo '<TABLE id="tb-cdt" CELLPADDING=1 CELLSPACING=2>';
while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
	  { 
	  //$textcours=stripslashes(markdown($ligne[1]));
	  $textcours=stripslashes($ligne[1]);
	  //$textafaire=stripslashes(markdown($ligne[2]));
	  $textafaire=stripslashes($ligne[2]);
	  //$day="1,0,0,12,1,2007";echo $day;
	  $jour=LeJour(strToTime($ligne[5]));
	  //debut
	  if ($ligne[1]!="") {
	  echo '<tbody><tr><th colspan=2></th></tr></tbody>';
	  echo '<tbody>';
	  echo '<tr>';
	  //affichage de la seance
	  echo '<td class="seance">S&eacute;ance du <br/>'.$jour.'&nbsp;'.$ligne[0].'<br /> </td>';
	  if($ligne[1]!="" && $ligne[6]==1) echo '<td class="contenu2">';
	  elseif($ligne[1]!="" && $ligne[6]==2) echo '<td class="contenu3">';
	  else echo '<td class="contenu">';
	  echo $textcours.'</td></tr>';
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") {
	  echo '<tr><td class="afaire">A faire pour le :<br/>'.$ligne[3].'</td><td class="contenu">';
	  echo $textafaire.'</td></tr>';
	  }
	  //fin

	  echo '</tbody>';
	   echo '<tbody><tr><th class="bas" colspan=2>';
	   echo '</th></tr>';
	  echo '</tbody>';
	  echo '<tbody><tr><th colspan=2><hr></th></tr></tbody>';
	  }
	  else {
	  echo '<tbody><tr><th colspan=2></th></tr></tbody>';
	  echo '<tbody>';
	  echo '<tr>';
	  //affichage de la seance
	  echo '<td class="afaire">Donn&eacute; le :&nbsp;'.$ligne[0].'<br />';
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") {
	  echo '<br/>Pour le :&nbsp;'.$ligne[3].'</td>';
	  if($ligne[6]==1) echo '<td class="contenu2">';
	  elseif($ligne[6]==2) echo '<td class="contenu3">';
	  else echo '<td class="contenu">';
	   echo $textafaire.'</td></tr>';
	  }
	  //fin
	
	  echo '</tbody>';
	  echo '<tbody><tr><th colspan=2><hr></th></tr></tbody>';
	  }
	  
}
if ($nb2==0) {	echo '<td class="contenu"> Aucun enregistrement </td>';}
echo '</table>';
echo '</div>';//fin boite5
echo '
</fieldset>';
echo '</div>'; //fin du div container
include ('../Includes/pied.inc');
?>
</body>
</html>
