<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
  VERSION 2.0 du 25/10/2009
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de diffusion -
			_-=-_
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 
session_name("Cdt_Lcs");
@session_start();

//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;

//si la page est appelée par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

include "../Includes/functions2.inc.php";
$tsmp=time();
$tsmp2=time() + 604800;//j+7


// Connexion à la base de données
require_once ('../Includes/config.inc.php');

if (isset($_GET['rubrique']))
	{ $ru=$_GET['rubrique'];
	$rq = "SELECT classe,matiere FROM onglets
	WHERE id_prof='$ru'";
	// lancer la requête
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result); 
	//on récupère les données
	$enrg = mysql_fetch_array($result, MYSQL_NUM); 
	if (ereg("^(Classe)",$enrg[0]))
	{
	//recherche du niveau de la classe pour le format long
	$classe_courte=split('_',$enrg[0]);
	//on isole le nom court de la classe
	$niveau=$classe_courte[count($classe_courte)-1];
	$FirstCar="";
	//on recolle les premiers morceaux du nom long
	for ($loop=0; $loop <= count($classe_courte)-2  ; $loop++)
			{
			$FirstCar.=$classe_courte[$loop]."_";
			}
	//on ajoute le premier caractère du nom court
	$FirstCar.=$niveau[0];		
	}
		else
	{
	//cas du format court
	//premier caractère de la classe active
	$FirstCar=$enrg[0][0];
	}
	//classe active 
	$clas_act=$enrg[0];
	//matière de la classe active
	$mat=$enrg[1];
	
	}
	
// Recherche des classes de même niveau dans la matière
$rq = "SELECT classe,id_prof FROM onglets
 WHERE login='{$_SESSION['login']}'  AND classe != '$clas_act' AND  matiere = '$mat' ORDER BY classe ASC ";
 
// lancer la requête
$result = @mysql_query ($rq) or die (mysql_error()); 
$loop=0;
while ($row = mysql_fetch_object($result))
	{
    $data1[$loop]=$row->classe;
	$data2[$loop]=$row->id_prof;
	$loop++;
	}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" >
	<TITLE>module bichaouaoua</TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
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

<H1 class='title'>Enregistrer plusieurs classes</H1>

<?

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{
	//initialisation des variables de session
	unset ($_SESSION['grp']);
	unset ($_SESSION['dif_c']);
	unset ($_SESSION['dif_af']);
	unset ($_SESSION['dif_vi']);
	//création des tableaux de date des cours et "a faire"

	for ($loop=0; $loop < count ($_POST['grp'])  ; $loop++)
			{
			$diffuse_c[$loop]=$_POST['datejavac_dif'][$_POST['grp'][$loop]];
			$diffuse_af[$loop]=$_POST['datejavaf_dif'][$_POST['grp'][$loop]];
			$diffuse_vi[$loop]=$_POST['datejavavi_dif'][$_POST['grp'][$loop]];
			}


	//mémorisation des données dans des variables de session		
	$_SESSION['grp']=$_POST['grp'];
	$_SESSION['dif_c']=$diffuse_c;
	$_SESSION['dif_af']=$diffuse_af;
	$_SESSION['dif_vi']=$diffuse_vi;
	

//fermeture du popup
	echo "<SCRIPT language='Javascript'>
					<!--
					window.close()
					// -->
					</script>";	
	}

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<fieldset id="field7">
<legend id="legende">Sélection des classes</legend>

<?
//affichage du formulaire
//date "cours" au jour courant
$datjc_dif=getdate($tsmp);
$jo_dif=date('d',$datjc_dif['0']);
$mo_dif=date('m',$datjc_dif['0']);
$ann_dif=date('Y',$datjc_dif['0']);
$dtajac_dif=$jo_dif."/".$mo_dif."/".$ann_dif;
//date "a faire" à j+7
$datjf_dif=getdate($tsmp2);
$jof_dif=date('d',$datjf_dif['0']);
$mof_dif=date('m',$datjf_dif['0']);
$annf_dif=date('Y',$datjf_dif['0']);
$dtajaf_dif=$jof_dif."/".$mof_dif."/".$annf_dif;

$dtajavi_dif="idem Cours";
if (!isset($_POST['Valider']))
	{
	//si pas de classe de même niveau dans la matière
	if (mysql_num_rows($result)==0) 
		{
		echo '<H4> Apparemment, vous n\'avez pas d\'autre classe  en <B>'.$mat.'</B> !<BR> Vérifiez que la matière n\'est
		pas orthographiée différemment pour les autres classes .</h4>';
		echo '<input type="submit" name="Valider" value="OK" class="bt50" >';
		}
	//sinon, affichage des classes de même niveau 	
	else
		{
		echo '<H4>Lorsque vous enregistrerez votre commentaire de <B>'.$mat.'</B> en <B>'.$clas_act.'</B>, il sera diffusé vers les	classes que vous aurez sélectionnées ci-dessous </H4></br>';
		echo '<div id="classes">';
		echo '<TABLE border=0 align=center cellspacing=2>';
		echo '<TR><TD width="30" align="center"></TD><TD  align="center"><H5>Classe</H5></TD><TD  align="center"><H5>Date du cours</H5></TD><TD  align="center"><H5>Date "à faire" </H5></TD><TD  align="center"><H5>Date visiblité </H5></TD></TR>';
		for ($loop=0; $loop < count ($data1)  ; $loop++)
			{
			echo '<TD width="30" align="center"><H5><input type="checkbox" name="grp[]" value="'.$data2[$loop];
			echo '" /></H5></TD><TD align="center"><H5>'.$data1[$loop].'&nbsp&nbsp&nbsp</H5></TD> <TD  align="center"><H5><input id="datejavac'.$loop.'" size="9" name="datejavac_dif['.$data2[$loop].']" value="'.$dtajac_dif.'"readonly="readonly" style="cursor: text"/>';
			
			echo '&nbsp&nbsp&nbsp</H5></TD><TD  align="center"><H5> <input id="datejaf'.$loop.'" size="9" name="datejavaf_dif['.$data2[$loop].']" value="'.$dtajaf_dif.'"readonly="readonly" style="cursor: text"/>';
			echo '&nbsp&nbsp&nbsp</H5></TD><TD  align="center"><H5> <input id="datejavav'.$loop.'" size="9" name="datejavavi_dif['.$data2[$loop].']" value="'.$dtajavi_dif.'"readonly="readonly" style="cursor: text"/>';
			echo '</H5></TD></TR>';
			}
		echo '</TABLE></div>';
		echo '<input type="submit" name="Valider" value="" class="bt-valid-sel">';
		echo '<Div class="notabene"> N\'oubliez pas apr&egrave;s avoir "Valider la s&eacute;lection", de cliquer sur <B>Enregistrer </B>dans le cahier de texte</div>';
		}
	}
?>
</fieldset>	
</FORM>
</BODY>
</HTML>



