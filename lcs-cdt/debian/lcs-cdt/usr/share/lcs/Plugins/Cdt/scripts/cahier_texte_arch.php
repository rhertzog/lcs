<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script archives du cahier de textes -
			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();

// autorisation d'accés ?
if ($_SESSION['cequi']!="prof") 
	{// si pas prof, non autorisé
        print('<p align="center"> </p> <p align="center"><font face="Arial" size="5" color="#FF6600">
		Désolé, vous ne pouvez pas accéder à cette page</font></p>');
       	exit;
			}
if (isset($_GET['arch']))
	{
	$arch=$_GET['arch'];
	}
	else $arch="";
	
//si clic sur le bouton Copier-Coller
if (isset($_POST['copie']))
	{
	$textc1=ereg_replace("\n","\\n",stripslashes($_POST['textecours']));
	$textc=ereg_replace("\r","\\r",$textc1);
	$textaf1=ereg_replace("\n","\\n",stripslashes($_POST['texteafaire']));
	$textaf=ereg_replace("\r","\\r",$textaf1);
		echo '<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js" ></script>
					<SCRIPT language="Javascript">
					<!--
					opener.tinyMCE.execInstanceCommand("coursfield","mceInsertContent",false,"'.$textc.'");
					opener.tinyMCE.execInstanceCommand("afairefield","mceInsertContent",false,"'.$textaf.'");
	
	window.close();
	// -->
	</script>';
	exit;
	}

?>

<! cahier_textes_prof.php version 2.0 par Ph LECLERC - Lgt "Arcisse de Caumont" 14400 BAYEUX - philippe.leclerc1@ac-caen.fr >
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Archives du Cahier de textes numérique</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body  link="#000000" vlink="#000000" alink="#000000" BACKGROUND="../images/espperso.jpg" >
 <style>
<!--
a:link {text-decoration:none; color: #000000; font-family:   arial, verdana ; font-size : 8pt }
a:visited {text-decoration: none; color: #cc0000; font-family: arial, verdana ; font-size: 8pt}
a:active {text-decoration: none; color: #000099; font-family: arial, verdana ; font-size: 10pt}
a:hover {text-decoration: none; color: #990000; background-color: #FFFFCC ;font-family: arial, verdana ; font-size: 8pt}
-->
</style>

<?
// Connexion à la base de données	
include "../Includes/config.inc.php";


/*================================
   -      Affichage des archives  -
   ================================*/
	echo (' <B><FONT COLOR="#0000FF" >Archives disponibles :</font></B> ');
 	//recherche du  nom des archives
	$TablesExist= mysql_list_tables(DB_NAME);
	$x=0;
	while ($table=mysql_fetch_row($TablesExist))
	if (ereg("^onglets[[:alnum:]]",$table[0]))
	{
	$archive=split('s',$table[0]);
	$x++;
	//archive courante
	if ($arch==$archive[1]) echo '<B><FONT COLOR="#000033" >'.$archive[1].' &nbsp </B>';
	else
	//autres archives
	echo "<a href='cahier_texte_arch.php?arch=$archive[1]'>$archive[1]</a> &nbsp ";
	}
	//s'il n'esiste pas d'archive
	if ($x==0) 
		{
		echo '<B><FONT COLOR="#CC0000" >Aucune </FONT></B>';
		exit;
		}
	
    if ($arch=="") exit;


$rq = "SELECT classe,matiere,id_prof FROM onglets$arch
 WHERE login='{$_SESSION['login']}' ORDER BY classe ASC ";
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());
 
// si pas de rubrique
if (mysql_num_rows($result)==0) 
	{
	echo("<br> L'archive ".$arch."de votre cahier de textes ne comporte  aucune rubrique. ");exit;
	mysql_close();	
	exit;
	}

//on récupère le paramètre de la rubrique
if (isset($_GET['rubrique']))
	{
	$cible=$_GET['rubrique'];
	} 
elseif 
	(isset($_POST['rubriq']))
	{
	$cible=$_POST['rubriq'];
	}
else
	{
	$cible="";
	}

// Connexion à la base de données
include "../Includes/config.inc.php";



/*
   ================================
   - Traitement de la barre des menus  -
   ================================
*/

// Créer la requête (Récupérer les rubriques de l'utilisateur) 
$rq = "SELECT classe,matiere,id_prof FROM onglets$arch
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

 // lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?

//on récupère les données
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
	{$clas[$loop]=$enrg[0];
	$mat[$loop]=$enrg[1];
	$numero[$loop]=$enrg[2];
	$loop++;
	}
	
//on calcule la largeur des onglets
if($cible==""){$cible=($numero[0]);}
$pourcen = 100/$nb;

//création de la barre de menu , couleur de fond # pour l'onglet actif
echo("<div align='left'><table width='100%' border='1' bordercolor='#E6E6FF'  cellspacing='0' cellpadding='0'><tr bgcolor='#cccccc' >");
for($x=0;$x < $nb;$x++)
{if ($cible == ($numero[$x])){
echo("<td width='$pourcen%' bgcolor='#4169E1' color='#ffffff'><font face=\"arial\"color=\"#FFFFFF\"size=2>
			<B><div align='center' color='#FFFFFF'><B> $mat[$x]<br>$clas[$x]"."</div></td>");}
else {
echo("<td width='$pourcen%'><div align='center'><a href='cahier_texte_arch.php
?rubrique=$numero[$x]&arch=$arch''onmouseover=\"window.status='';return true\" >$mat[$x]<br>$clas[$x]</a></div></td>");
}
}
echo("</tr></table></div>");
echo ('	<TABLE align="center" >
		<TR>
		<TD><font face="Arial" size="2" color="#FF6600" align="left"> Le bouton <B>Copier-coller</B> insère le commentaire dans la rubrique 
		<B>active </B> du cahier de textes
		<TD width="150" align="center" border="1"><a href="imprim.php?rubrique='.$cible.'&arch='.$arch.'" target="blank"  
    ><img src="../images/imprimante.gif"border=0 title="Imprimer"> </a></TD>
		
		</TR>
	</TABLE>
');

/*================================
   -      Affichage du formulaire  -
   ================================*/
?>

<?
/*======================================
   - Affichage du contenu du cahier de textes  -
   =======================================*/
   
include_once ('../Includes/markdown.php'); //convertisseur txt-->HTML

//créer la requête
$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique FROM cahiertxt$arch
 WHERE (id_auteur=$cible) AND (login='{$_SESSION['login']}') ORDER BY date desc ,id_rubrique desc";
 
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?
$nb2 = mysql_num_rows($result); 

echo('<TABLE WIDTH=100% BORDER=1 CELLPADDING=1 CELLSPACING=1  >
	');
while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
  { 
  $textcours=markdown($ligne[1]);
  $textafaire=markdown($ligne[2]);
  
 
	//debut 
	echo ("  <TR><TD color=\"#FFFFFF\" ></TD></TR>
  
  <TR VALIGN=middle >
		<TD align=center WIDTH=15% BGCOLOR=\"#4169E1\">			
		<font face=\"Arial\"color=\"#FFFFFF\"size=2><B>Séance du $ligne[0]</B></font>
		</TD>"); 
		echo "<FORM ACTION='"; echo htmlentities($_SERVER['PHP_SELF']);  
		echo "' METHOD='POST'><TD align=left WIDTH=7% colspan=1>
		<input type='hidden' name='textecours' value=\"";echo htmlentities(addslashes($textcours));echo "\" >
		<input type='hidden' name='texteafaire' value= \"";echo htmlentities(addslashes($textafaire));echo "\">
		<INPUT TYPE='SUBMIT' NAME='copie' VALUE='Copier-Coller' style='background-color:#E6E6FA'></td></form>"."
		<TD></TD>
		</TR>
		<TR VALIGN=TOP >
		<TD align=left WIDTH=100% colspan=3 BGCOLOR=\"#E6E6FA\">
						<font face='Arial' color='#0000cc' size=2>$textcours</font>
		</TD>
		
	</TR>";
	if($ligne[2]!="") echo ("
	<TR VALIGN=TOP >
		<TD WIDTH=100% colspan=3 BGCOLOR=\"#E6E6FA\">
			
			<font face=\"Arial\"color=\"#cc0000\"size=2> <B>A faire pour le :</B> $ligne[3] <BR> </font>
			<font face=\"Arial\"color=\"#009900\"size=2>$textafaire</font>
		
	</TR>");
	//fin
	echo("<TR></TR><TR></TR><TR></TR><TR></TR><TR></TR><TR></TR>");

  }
  echo "</table>";
  include ('../Includes/pied.inc'); 
?>
</body>
</html>
