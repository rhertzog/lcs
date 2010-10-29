<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - Archives du cahier de textes ELEVE -
   			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
//si la page est appelee par un utilisateur non identifiee
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

include ('../Includes/data.inc.php');
include_once("../Includes/fonctions.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");	
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");  
include "../Includes/functions2.inc.php";

// Connexion a la base de donn�es
require_once ('../Includes/config.inc.php');
//destruction des variables de sessions
unset ($_SESSION['prof']);
unset ($_SESSION['mat']);
unset ($_SESSION['numero']);
unset ($_SESSION['pref']);
unset ($_SESSION['visa']);
unset ($_SESSION['datvisa']);
$prof=$mat=$numero=$pref=$restr=$visa=$datvisa=array();
if (isset($_POST['an_archive']))
	{
	 $an_arch=$_POST['an_archive'];
	 //liste des classes
	 $list_classe=array();

//liste des classes
$rq="SELECT DISTINCT `classe` FROM `onglets".$an_arch."` WHERE `classe` IS NOT NULL";
//echo $rq;exit;
$result = @mysql_query ($rq);
	if ($result)
		{
		$n=0;	
		while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
			{
			$list_classe[$n]=$enrg[0];
			$n++;
			}
		}
mysql_free_result($result);

	 }
//initialisation 
$tsmp=mktime(1, 2, 3, 4, 5, 2000);
$ch="--";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Cahier de textes num�rique</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<LINK  href="../style/style.css" rel=StyleSheet type="text/css">
<link  href="../style/deroulant.css" rel=StyleSheet type="text/css">
<LINK  href="../style/navlist-eleve.css" rel=StyleSheet type="text/css">
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	<script language="Javascript" type="text/javascript" src="../Includes/cdt_eleve.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/jquery-1.3.2.min.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/cdt-ele-script.js"></script>
	<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
	<script language="javascript" type="text/javascript" src="../Includes/conf-tiny_mce.js"></script>
</head>

<body>
<?php

if ((isset($_POST['klasse']) && ($_POST['an_archive']==$_POST['an_activ'])))
	{ // Traiter le formulaire
		//la classe
		if (strlen($_POST['klasse']) != "--")
		{ 
		$ch = stripslashes($_POST['klasse']);
	
	
	
//fin traitement formulaire

//Traitement de la barre des menus

// Cr�er la requ�te (R�cup�rer les rubriques de la classe) 
$rq = "SELECT id_prof FROM onglets".$an_arch."
 WHERE classe='$ch' ORDER BY 'id_prof' asc ";

 // lancer la requ�te
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?
if ($nb>0)
	{
	//on r�cupere les donn�es
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$num_ero[$loop]=$enrg[0];//num�ro de l'onglet
		$loop++;
		}
	if($cible==""){$cible=($num_ero[0]);}	
	}
	}
}
//affichage du formulaire
?>
<form NAME="monform" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
<SCRIPT language="Javascript" src="../Includes/barre_java.js"></SCRIPT>
<fieldset id="field7">
<legend id="legende">Archives des Cahiers de textes &#233;l&#232;ve</legend>
S&#233;lectionnez une ann&#233;e
<?php
//si on ne  connait pas la classe , on affiche un menu d�roulant  
	//recherche du  nom des archives
				$TablesExist= mysql_query("show tables");
				$x=0;
				while ($table=mysql_fetch_row($TablesExist))
				if (mb_ereg("^onglets[[:alnum:]]",$table[0])) {
					$archive=explode('s',$table[0]);
					$archnum[$x]=$archive[1];
					$x++;
					}

	echo "<select name='an_archive' onChange='javascript:document.monform.submit()' style='background-color:#E6E6FA'>";
	echo "<option valeur='--'>--</option>\n";
	foreach ($archnum as $cl� => $valeur)
	  { 
	  echo "<option valeur=\"$valeur\"";
	  if ($valeur==$an_arch) {echo 'selected';}
	  echo ">$valeur</option>\n";
	  }
	echo "</select>";
	
if (isset($_POST['an_archive']))	
	{  
	echo ' S&#233;lectionnez  une classe : ' ;

	//si on ne  connait pas la classe , on affiche un menu d�roulant  
		echo "<select name='klasse' onChange='javascript:document.monform.submit()'style='background-color:#E6E6FA'>";
		echo "<option valeur='--'>--</option>\n";
		foreach ($list_classe as $cl� => $valeur)
		  { echo "<option valeur=\"$valeur\"";
		  if ($valeur==$ch) {echo 'selected';}
		  echo ">$valeur</option>\n";
		  }
		echo '</select></fieldset><input type="hidden" name="an_activ" value= "'.$an_arch.'"></FORM>';
		
if ($ch!="--")	
	{
	// Cr�er la requ�te (R�cup�rer les rubriques de la classe) 
	$rq = "SELECT prof,matiere,id_prof,prefix FROM onglets".$an_arch."
	 WHERE classe='$ch' ORDER BY 'id_prof' asc ";
	
	 // lancer la requ�te
	$result = @mysql_query ($rq) or die (mysql_error());
	$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ? 
	if ($nb>0)
		{
		//on r�cupere les donn�es 
		$loop=0;
		while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
			{
			$prof[$loop]=$enrg[0];//nom du prof
			$mat[$loop]=$enrg[1];//matiere 
			$numero[$loop]=$enrg[2];//num�ro de l'onglet
			$pref[$loop]=$enrg[3];// pr�fixe
			$restr[$loop]=0;//restric
			$visa[$loop]=0;// 
			$datvisa[$loop]=0;
			$loop++;
			}
	$_SESSION['prof']=$prof;
	$_SESSION['mat']=$mat;
	$_SESSION['numero']=$numero;
	$_SESSION['pref']=$pref;
	$_SESSION['visa']=$visa;
	$_SESSION['datvisa']=$datvisa;
	$delta=(count($_SESSION['prof']) -7) *40 > 0 ? ((count($_SESSION['prof']) -7) *40) :0;
	echo '<script language="javascript" type="text/javascript" >
	var offset = '.$delta.'	</script>';	
		
		echo '<div id="onglev">';
		if ($delta >0) 
			{
			echo '<div id="switch-ongletsup" ></div>';
			echo '<div id="switch-ongletsdown"></div>';
			}
		echo '<div id="onglev_refresh">';
		// Affichage de la colonne de gauche
		if($cible==""){$cible=($numero[0]);}	
		//cr�ation de la barre de menu , couleur de fond # pour cellule active
		echo '<ul id="navlist-elv">';	
		for($x=0;$x < $loop;$x++)
			{
			if ($cible == ($numero[$x])) 
				{
				echo '<li id="active"><a href="#" title="" onclick="refresh_cdt_arch('. $numero[$x].','.$tsmp.',\''.$an_arch.'\')" id="courant">&nbsp;'.$mat[$x].'&nbsp;<br />&nbsp;'.$pref[$x].'  '.$prof[$x].'&nbsp;</a></li>';
				}
				else 
				{
				echo '<li><a href="#" title="" onclick="refresh_cdt_arch('. $numero[$x].','.$tsmp.',\''.$an_arch.'\')" >&nbsp;'.$mat[$x].'&nbsp;<br />&nbsp;'.$pref[$x].'  '.$prof[$x].'&nbsp;</a>';
				}
			}
	
		echo '</ul>';
		echo '</div>';
		echo '</div>';
		}	
		else
			{ 
	// Il y a eu un os !
		 		echo "<p><FONT face='Arial'size='3' COLOR='#FF0000'>Aucun enregistrement dans le cahier de textes de  $ch !</p><p></p></font>";
			}
	
	//affichage du contenu du cahier de textes
	
			include_once ('../Includes/markdown.php');//convertisseur text-->HTML
	
			//cr�er la requ�te
			if ($cible!="") 
			{//�laboration de la date limite a partir de la date selectionn�e
			if ($_SESSION['version']=">=432") setlocale(LC_TIME,"french");
			else setlocale("LC_TIME","french");
			$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique,date,date FROM cahiertxt".$an_arch." 
			WHERE id_auteur=$cible  ORDER BY date asc";
			 
			// lancer la requ�te
			$result = @mysql_query ($rq) or die (mysql_error());
	
			// Combien y a-t-il d'enregistrements ?
			$nb2 = mysql_num_rows($result);
			echo '<div id="boite5elv">';
			echo '<TABLE id="tb-cdt" CELLPADDING=1 CELLSPACING=2>';
			while ($ligne = mysql_fetch_array($result, MYSQL_NUM))
				{ 
				  $textcours=stripslashes(markdown($ligne[1]));
				  //$textcours=$ligne[1];
				  $textafaire=stripslashes(markdown($ligne[2]));
				  //$day="1,0,0,12,1,2007";echo $day;
				  $jour=LeJour(strToTime($ligne[5]));
				  //debut
				  if ($ligne[1]!="") 
				  	{
					  echo '<tbody><tr><th colspan=2></th></tr></tbody>';
					  echo '<tbody>';
					  echo '<tr>';
					  //affichage de la seance
					  echo '<td class="seance">S&eacute;ance du <br/>'.$jour.'&nbsp;'.$ligne[0].'</td>';
					  if($ligne[6]==1) echo '<td class="contenu2">';
					  elseif($ligne[6]==2) echo '<td class="contenu3">';
					  else echo '<td class="contenu">';
					  echo $textcours.'</td></tr>';
					  //affichage, s'il existe, du travail a effectuer
					  if ($ligne[2]!="") 
					  echo '<tr><td class="afaire">A faire pour le :<br/>'.$ligne[3].'</td><td class="contenu">'.$textafaire.'</td></tr>';
					  //fin
				
					  echo '</tbody>';
					  echo '<tbody><tr><th colspan=2>(�-�)</tr></tbody>';
				  	}
				  else
				   {
					  echo '<tbody><tr><th colspan=2></th></tr></tbody>';
					  echo '<tbody>';
					  echo '<tr>';
					  //affichage de la seance
					  echo '<td class="afaire">Donn&eacute; le :&nbsp;'.$ligne[0];
					  //affichage, s'il existe, du travail a effectuer
					  if ($ligne[2]!="") 
					  echo '<br/>Pour le :&nbsp;'.$ligne[3].'</td>';
					  if($ligne[6]==1) echo '<td class="contenu2">';
					  elseif($ligne[6]==2) echo '<td class="contenu3">';
					  else echo '<td class="contenu">';
					  echo $textafaire.'</td></tr>';
					  //fin
					  echo '<tbody><tr><th colspan=2>(�-�)</th></tr></tbody>';
				  	}
			} //fin du while
		echo '</table>';
		if (stripos($_SERVER['HTTP_USER_AGENT'], "msie"))  
			{ 
			include ('../Includes/pied.inc');}
			echo "</div>"; //fin du div boite5elv
			}
		echo '</div>';//fin du div container
		if (!stripos($_SERVER['HTTP_USER_AGENT'], "msie"))  
			{
			include ('../Includes/pied.inc');
			}
	}
}
?>
</body>
</html>
