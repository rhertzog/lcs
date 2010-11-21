<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
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
include "../Includes/check.php";
if (!check()) exit;
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
	if (mb_ereg("^(Classe)",$enrg[0]))
	{
	//recherche du niveau de la classe pour le format long
	$classe_courte=explode('_',$enrg[0]);
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
 WHERE login='{$_SESSION['login']}' AND classe != '$clas_act' and matiere = '$mat' ORDER BY classe ASC ";
 
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
	<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<TITLE>devoirs multiples</TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
		<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	<STYLE>
	
	</STYLE>
</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'>Planifier un devoir pour plusieurs classes</H1>

<?php

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{
	//initialisation des variables de session
	unset ($_SESSION['sel_cl']);
	
	//mémorisation des données dans des variables de session		
	$_SESSION['sel_cl']=$_POST['sel_cl'];
	

	//fermeture du popup
	echo "<SCRIPT language='Javascript'>
					<!--
					window.close()
					// -->
					</script>";	
	}

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<INPUT name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>">
<fieldset id="field7">
<legend id="legende">
<FONT FACE="Trebuchet MS, sans-serif" color="#009933">  Sélection des classes   </FONT>
</legend>

<?php
//affichage du formulaire

if (!isset($_POST['Valider']))
	{
	//si pas de classe de même niveau dans la matière
	if (mysql_num_rows($result)==0) 
		echo '<H4> Apparemment, vous n\'avez pas d\'autre classe  en <B>'.$mat.'</B> !<br /> V&eacute;rifiez que la mati&egrave;re n\'est
		pas orthographi&eacute;e diff&eacute;remment pour les autres classes .<br /><br /><DIV align="center" ><input type="submit" name="Valider"
		value="OK " ></DIV>';
	//sinon, affichage des classes de même niveau 	
	else
		{
		echo '<ul> <Li>Si vous avez un cours compos&eacute; d\'&eacute;l&egrave;ves issus de plusieurs classes,
		 vous pouvez planifier en une fois, ce devoir pour tous vos d\'&eacute;l&egrave;ves.</li><br />
		<LI>Lorsque vous enregistrerez votre devoir de <B>'.$mat.'</B> en <B>'.$clas_act.'</B> , il sera aussi planifi&eacute;  pour  les		classes que vous aurez s&eacute;lectionn&eacute;es ci-dessous </li></ul>';
		echo '<div id="sel-classe"><TABLE id="diff-dev"><TR>';
		for ($loop=1; $loop < count ($data1)+1  ; $loop++)
			{
			echo '<TD><H5><input type="checkbox" name="sel_cl[]" value="'.$data1[$loop-1].'#'.$data2[$loop-1].'" />'.$data1[$loop-1].'&nbsp&nbsp&nbsp</H5>
			
			</TD>';
			if (($loop %5)==0 ) echo'</TR><TR>';
			}
		echo '</TR></TABLE></div>';
		echo '<DIV align="center" ><input type="submit" name="Valider" value="Valider la s&eacute;lection" class="bt"></DIV>'; 
		echo '<br /><Div class="notabene"> N\'oubliez pas apr&egrave;s avoir Valider la s&eacute;lection, de cliquer sur <B>Enregistrer </B>dans la page "Planning" </div>';
		}
	}
?>

</fieldset>	
</FORM>
</BODY>
</HTML>



