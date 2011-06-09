<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script archives perso du cahier de textes -
			_-=-_
    "Valid XHTML 1.0 Strict"
   ============================================= */
session_name("Cdt_Lcs");
@session_start();

// autorisation d'accés ?
if ($_SESSION['cequi']!="prof")	exit;
			
if (isset($_GET['arch']))
	{
	$arch=$_GET['arch'];
	}
	else $arch="";
	
if (isset($_POST['Fermer'])) 
    echo '<script type="text/javascript">
                //<![CDATA[
                window.close();
		 //]]>
                </script>';
					
//si clic sur le bouton Copier-Coller
if (isset($_POST['copie']))
	{
	if (get_magic_quotes_gpc()) $textc1=mb_ereg_replace("\n","\\n",stripslashes($_POST['textecours']));
	else $textc1=mb_ereg_replace("\n","\\n",$_POST['textecours']);
	$textc=mb_ereg_replace("\r","\\r",$textc1);
	if (get_magic_quotes_gpc()) $textaf1=mb_ereg_replace("\n","\\n",stripslashes($_POST['texteafaire']));
	else $textaf1=mb_ereg_replace("\n","\\n",$_POST['texteafaire']);
	$textaf=mb_ereg_replace("\r","\\r",$textaf1);
		echo '<script type="text/javascript">
                //<![CDATA[
		opener.tinyMCE.execInstanceCommand("coursfield","mceInsertContent",false,"'.$textc.'");
		opener.tinyMCE.execInstanceCommand("afairefield","mceInsertContent",false,"'.$textaf.'");
		window.close();
		 //]]>
                </script>';
	exit;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Archives personnelles</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	<link  href="../style/deroulant.css" rel="stylesheet" type="text/css" />
	<link  href="../style/navlist-prof.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.all.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.datepicker.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.theme.css" rel="stylesheet" type="text/css" />
         <!--[if IE]>
        <link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
        <![endif]-->
</head>

<body >

<?php
echo "<form action='";
echo htmlentities($_SERVER["PHP_SELF"]);
echo "' method='post'>";
echo '<div id="bt-fixe" ><input class="bt2-fermer" type="submit" name="Fermer" value="" /></div></form>';

// Connexion à la base de données	
include_once "/var/www/lcs/includes/headerauth.inc.php";
 				list ($idpers,$log) = isauth();
  				if ($idpers)  $_LCSkey = urldecode( xoft_decode($HTTP_COOKIE_VARS['LCSuser'],$key_priv) );
 				$nom_bdd=mb_ereg_replace("\.","",$_SESSION['login']);
 				DEFINE ('DBP_USER', $_SESSION['login']);
				DEFINE ('DBP_PASSWORD', $_LCSkey);
				DEFINE ('DBP_HOST', 'localhost');
				DEFINE ('DBP_NAME', $nom_bdd.'_db');

				// Ouvrir la connexion et selectionner la base de donnees
				$dbcp = @mysql_connect (DBP_HOST, DBP_USER, DBP_PASSWORD) 
      			OR die ('Connexion a MySQL impossible : '.mysql_error().'<br />');
				mysql_select_db (DBP_NAME)
       			OR die ('Selection de la base de donnees impossible : '.mysql_error().'<br />');

echo '<div id="entete">';
echo '<div id="navcontainer">';
echo ("<ul id='arch-navlist'>");
/*================================
   -      Affichage des archives  -
   ================================*/
	
 	//recherche du  nom des archives
	$TablesExist= mysql_query("show tables");
	$x=0;
	while ($table=mysql_fetch_row($TablesExist))
	if (mb_ereg("^onglets",$table[0]))
	{
	$archive=explode('s',$table[0]);
	if ($archive[1]!="") $archnum=$archive[1]; else $archnum="An dernier";
	$x++;
	//archive courante
	if ($arch==$archive[1]) echo "<li  class=\"arch\"> <a href='cahier_texte_arch_perso.php?arch=$archive[1]' id='courant2'>$archnum</a></li>";
	else
	//autres archives
	echo "<li><a href='cahier_texte_arch_perso.php?arch=$archive[1]'>$archnum</a></li> ";
	}
	//s'il n'esiste pas d'archive
	if ($x==0) 
		{
		echo '<p class="vide">Aucune archive</p>';
		exit;
		}
		echo "</ul>";
	
$rq = "SELECT classe,matiere,id_prof FROM onglets$arch
 WHERE 1 ORDER BY classe ASC ";
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());
 
// si pas de rubrique
if (mysql_num_rows($result)==0) 
	{
	echo("<br /> L'archive ".$arch."de votre cahier de textes ne comporte  aucune rubrique. ");exit;
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


/*
   ================================
   - Traitement de la barre des menus  -
   ================================
*/

// Créer la requête (Récupérer les rubriques de l'utilisateur) 
$rq = "SELECT classe,matiere,id_prof FROM onglets$arch
 WHERE 1 ORDER BY id_prof ASC ";

 // lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());
$nb = mysql_num_rows($result);  // Combien y a-t-il d'enregistrements ?

//on récupère les données
$loop=0;
while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
	{$clas[$loop]=$enrg[0];
	$mat[$loop]=utf8_encode($enrg[1]);
	$numero[$loop]=$enrg[2];
	$loop++;
	}
	
//on calcule la largeur des onglets
if($cible==""){$cible=($numero[0]);}
$nmax=$nb;

echo ("<ul id='navlist'>");
for($x=0;$x < $nmax;$x++)
	{
		if ($cible == ($numero[$x]))
			{//cellule active	
			echo "<li id='select'><a href='cahier_texte_arch_perso.php?rubrique=$numero[$x]&amp;arch=$arch'
			 id='courant'>".htmlentities($mat[$x])."<br />$clas[$x] "."</a></li>";	
			$contenu_postit=stripslashes($com[$x]);
			}
		else 
			{
			if (!isset($mat[$x])) $mat[$x]="&nbsp;";
			if (!isset($clas[$x])) $clas[$x]="&nbsp;";
			if (!isset($numero[$x]))
			echo ("<li><a href='#'>$clas[$x]"."</a></li>");
			else
			{
			echo "<li><a href='cahier_texte_arch_perso.php?rubrique=$numero[$x]&amp;arch=$arch'
                        >".htmlentities($mat[$x])."<br />$clas[$x]"."</a></li>";
			}
			}
	}
echo '</ul>';
echo '</div>';
echo '</div>';
echo '<div id="container">';	

/*======================================
   - Affichage du contenu   -
   =======================================*/
   
//include_once ('../Includes/markdown.php'); //convertisseur txt-->HTML

//créer la requête
$rq = "SELECT DATE_FORMAT(date,'%d/%m/%Y'),contenu,afaire,DATE_FORMAT(datafaire,'%d/%m/%Y'),id_rubrique FROM cahiertxt$arch
 WHERE (id_auteur=$cible)  ORDER BY date desc ,id_rubrique desc";
 
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error());

// Combien y a-t-il d'enregistrements ?
$nb2 = mysql_num_rows($result); 
echo '<div id="boite5">';
echo '<table id="tb-cdt" cellpadding="1" cellspacing="2">';
while ($ligne = mysql_fetch_array($result, MYSQL_NUM))
  {
  $textcours=stripslashes($ligne[1]);
  $textafaire=stripslashes($ligne[2]);
  $textco=utf8_encode(stripslashes($ligne[1]));
  $textafa=utf8_encode(stripslashes($ligne[2]));

if ($ligne[1]!="") {
	   echo '<tbody><tr><th colspan="2"></th></tr></tbody>';
	  echo '<tbody>';
	  echo '<tr>';
	  //affichage de la seance
	  echo '<td class="seance">S&eacute;ance du <br/>'.$jour.'&nbsp;'.$ligne[0].'<br /> </td>';
	  echo '<td class="contenu">';
	  echo $textco.'</td></tr>';
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") {
	  echo '<tr><td class="afaire">A faire pour le :<br/>'.$ligne[3].'</td><td class="contenu">';
	  echo $textafa.'</td></tr>';
	  }
	  //fin

	  echo '</tbody>';
 	  echo '<tbody><tr><th class="bas" colspan="2">';
	  echo '<form action="';
	  echo htmlentities($_SERVER["PHP_SELF"]);
	  echo '" method="post"><div><input type="hidden" name="textecours" value="'.htmlentities(addslashes($textcours)).'" />';
	  echo '<input type="hidden" name="texteafaire" value="'.htmlentities(addslashes($textafaire)).'" />';
	  echo '<input type="submit" name="copie" value="" class="bt-copier" /></div></form>';
	  echo '</th></tr>';
	  echo '</tbody>';
	  echo '<tbody><tr><th colspan="2"><hr /></th></tr></tbody>';
	  }
	  else {
	  echo '<tbody><tr><th colspan="2"></th></tr></tbody>';
	  echo '<tbody>';
	  echo '<tr>';
	  //affichage de la seance
	  echo '<td class="afaire">Donn&eacute; le :&nbsp;'.$ligne[0].'<br />';
	  //affichage, s'il existe, du travail a effectuer
	  if ($ligne[2]!="") {
	  echo '<br/>Pour le :&nbsp;'.$ligne[3].'</td>';
	  echo '<td class="contenu">';
	  echo $textafa.'</td></tr>';
	  }
	  //fin

	  echo '</tbody>';
	  echo '<tbody><tr><th class="bas" colspan="2">';
          echo '<form action="';
	  echo htmlentities($_SERVER["PHP_SELF"]);
	  echo '" method="post"><div><input type="hidden" name="textecours" value="'.htmlentities(addslashes($textcours)).'" />';
	  echo '<input type="hidden" name="texteafaire" value="'.htmlentities(addslashes($textafaire)).'" />';
	  echo '<input type="submit" name="copie" value="" class="bt-copier" /></div></form>';
	  echo '</th></tr>';
	  echo '</tbody>';
	  echo '<tbody><tr><th colspan="2"><hr /></th></tr></tbody>';
	  }
}

  echo "</table>";
  echo "</div>";
  echo "</div>";
  include ('../Includes/pied.inc');
?>
</body>
</html>
