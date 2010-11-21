<?
/* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'impression -
			_-=-_
   ============================================= */
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit; 

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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<LINK rel="stylesheet" type="text/css" href="../style/style.css"  media="screen">
<link rel="stylesheet" href="../style/style_imp.css" type="text/css" media="print" />
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>

<body style="background:#fff">

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

//Affichage du cahier de textes à imprimer
echo('<TABLE id="impr" CELLPADDING=2 CELLSPACING=1 >');
echo '<caption>';	
	if ($nb2>0 ) {echo "<h2>Classe : <B>".$classe_active ."</B>&nbsp;&nbsp;&nbsp;Mati&egrave;re : &nbsp;<b> ".$mati_active."</b></h2>";}
	if ($tampon == 1) echo '<div id="visa-cdt">'.$datetampon.'</div>';
echo '</caption>';

	while ($ligne = mysql_fetch_array($result, MYSQL_NUM)) 
  { 
  $textcours=markdown($ligne[1]);
  $textafaire=markdown($ligne[2]);
  
	//debut
	echo '<tbody><tr><th>S&eacute;ance du '.$ligne[0].'</th></tr>';
	echo '<tr><td>'.$textcours.'</td></tr>';
	if($ligne[2]!="") 
	{
		echo ('<tr><td class="afR">A faire pour le :</B> '.$ligne[3].'</td></tr>');
		echo ('<tr><td>'.$textafaire.'</td></tr></tbody>');
	}
	//fin
  }
  echo '</table>';	exit;
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
<form id="impression" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?rubrique=<?echo $cible;?>&arch=<?echo $arch;?>" method="post" name= "cahtxt" >
<INPUT name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>">
<H1 class='title'>Impression du Cahier de Textes</h1>
<fieldset id="field7">
<legend id="legende">Classe  de <B> <?echo $classe_active .'</B> en <B>'. $mati_active ?></B></legend>
<p>Imprimer le contenu du cahier de textes de la classe  de <B> <?echo $classe_active .'</B> en <B>'. $mati_active ?></B>
 depuis le : <?calendrier_auto(-365,'jour_c','mois_c','an_c',$tsmp);?><input type="hidden" name="numrub" value= "<?php echo $ch ; ?>"></p>
<div id="chrono">
<INPUT TYPE=RADIO NAME="Option1" VALUE="asc" <?php IF (($sens=="asc")||($sens=="")) echo "CHECKED" ; ?>>par date croissante &nbsp;&nbsp;&nbsp;&nbsp
<INPUT TYPE=RADIO NAME="Option1" VALUE="desc"<?php IF ($sens=="desc") echo "CHECKED" ; ?>>par	date d&eacute;croissante 
</div>
<UL>
	<LI>En cliquant sur OK, le contenu du cahier de texte appara&icirc;t dans un nouvel onglet. </li>
	<LI>Avec Firefox, cocher l'option &quot;Imprimer le fond de page&quot; du menu mise en page.</li>
	<LI>Avec Internet Explorer, cliquez sur Outils, options Internet, onglet  &quot;avanc&eacute;&quot; et cocher la case &quot;imprimer les couleurs et les images&quot;</li>
	<LI>Utiliser la fonction Imprimer de votre navigateur pour imprimer le contenu de la fen&ecirc;tre</li>
	<LI>Fermer l'onglet pour revenir au cahier de texte</li>
</UL>


<input align="center" type="submit" name="valider" value="OK" class="bt50"> 

</fieldset>
</FORM>
<?

  include ('../Includes/pied.inc'); 
?>
</body>
</html>
