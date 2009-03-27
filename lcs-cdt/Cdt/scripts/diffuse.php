<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
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

//si la page est appel�e par un utilisateur non identifi�
if (!isset($_SESSION['login']) )exit;

//si la page est appel�e par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

include "../Includes/functions2.inc.php";
$tsmp=time();
$tsmp2=time() + 604800;//j+7


// Connexion � la base de donn�es
require_once ('../Includes/config.inc.php');

if (isset($_GET['rubrique']))
	{ $ru=$_GET['rubrique'];
	$rq = "SELECT classe,matiere FROM onglets
	WHERE id_prof='$ru'";
	// lancer la requ�te
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result); 
	//on r�cup�re les donn�es
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
	//on ajoute le premier caract�re du nom court
	$FirstCar.=$niveau[0];		
	}
		else
	{
	//cas du format court
	//premier caract�re de la classe active
	$FirstCar=$enrg[0][0];
	}
	//classe active 
	$clas_act=$enrg[0];
	//mati�re de la classe active
	$mat=$enrg[1];
	
	}
	
// Recherche des classes de m�me niveau dans la mati�re
$rq = "SELECT classe,id_prof FROM onglets
 WHERE login='{$_SESSION['login']}'  AND classe != '$clas_act' AND  matiere = '$mat' ORDER BY classe ASC ";
 
// lancer la requ�te
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
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=windows-1252">
	<TITLE>module bichaouaoua</TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<div style="position:absolute; top:0px; left:100px;z-index:3;">
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
<tr><td id="ds_calclass">
</td></tr>
</table>
</div>
<SCRIPT language="Javascript" src="../Includes/calend.js"></script>

<H1 class='title'>Diffuser un commentaire</H1>

<?

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{
	//initialisation des variables de session
	unset ($_SESSION['grp']);
	unset ($_SESSION['dif_c']);
	unset ($_SESSION['dif_af']);
	//cr�ation des tableaux de date des cours et "a faire"

	for ($loop=0; $loop < count ($_POST['grp'])  ; $loop++)
			{
			$diffuse_c[$loop]=$_POST['datejavac_dif'][$_POST['grp'][$loop]];
			$diffuse_af[$loop]=$_POST['datejavaf_dif'][$_POST['grp'][$loop]];
			}


	//m�morisation des donn�es dans des variables de session		
	$_SESSION['grp']=$_POST['grp'];
	$_SESSION['dif_c']=$diffuse_c;
	$_SESSION['dif_af']=$diffuse_af;

//fermeture du popup
	echo "<SCRIPT language='Javascript'>
					<!--
					window.close()
					// -->
					</script>";	
	}

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<fieldset>
<legend>
<FONT FACE="Trebuchet MS, sans-serif" color="#009933">  S�lection des classes   </FONT>
</legend>

<?
//affichage du formulaire
//date "cours" au jour courant
$datjc_dif=getdate($tsmp);
$jo_dif=date('d',$datjc_dif['0']);
$mo_dif=date('m',$datjc_dif['0']);
$ann_dif=date('Y',$datjc_dif['0']);
$dtajac_dif=$jo_dif."-".$mo_dif."-".$ann_dif;
//date "a faire" � j+7
$datjf_dif=getdate($tsmp2);
$jof_dif=date('d',$datjf_dif['0']);
$mof_dif=date('m',$datjf_dif['0']);
$annf_dif=date('Y',$datjf_dif['0']);
$dtajaf_dif=$jof_dif."-".$mof_dif."-".$annf_dif;

if (!isset($_POST['Valider']))
	{
	//si pas de classe de m�me niveau dans la mati�re
	if (mysql_num_rows($result)==0) 
		echo '<H4> Apparemment, vous n\'avez pas d\'autre classe  en <B>'.$mat.'</B> !<BR> V�rifiez que la mati�re n\'est
		pas orthographi�e diff�remment pour les autres classes .<BR><BR><DIV align="center" ><input type="submit" name="Valider"
		value="OK " ></DIV>';
	//sinon, affichage des classes de m�me niveau 	
	else
		{
		echo '<H4> Lorsque vous enregistrerez votre commentaire de <B>'.$mat.'</B> en <B>'.$clas_act.'</B> , il sera diffus� vers les
		classes que vous aurez s�lectionn�es ci-dessous </H4>';
		echo '<TABLE align="center" >
		<TR>
		<TD width="30" align="center"><H5></TD><TD  align="center"><H5>Classe</H5></TD><TD  align="center"><H5>Date du cours</H5></TD>
		<TD  align="center"><H5>Date "� faire" </H5></TD>
		</TR>';
		for ($loop=0; $loop < count ($data1)  ; $loop++)
			{
			echo '<TD width="30" align="center"><H5><input type="checkbox" name="grp[]" value="'.$data2[$loop].'" /></H5></TD><TD 
			align="center"><H5>'.$data1[$loop].'&nbsp&nbsp&nbsp</H5></TD> <TD  align="center"><H5> 
			<input onclick="ds_sh(this);" size="9" name="datejavac_dif['.$data2[$loop].']" value="'.$dtajac_dif.'"readonly="readonly" style="cursor: text"/>';
			
			echo '&nbsp&nbsp&nbsp</H5></TD><TD  align="center"><H5> 
			<input onclick="ds_sh(this);" size="9" name="datejavaf_dif['.$data2[$loop].']" value="'.$dtajaf_dif.'"readonly="readonly" style="cursor: text"/>';
			
			echo '</H5></TD></TR>';
			}
		echo '</TABLE><DIV align="center" ><input type="submit" name="Valider" value="Valider la s&eacute;lection " ></DIV>';
		echo '<BR><Div class="encours"> N\'oubliez pas apr&egrave;s avoir "Valider la s&eacute;lection", de cliquer sur <B>Enregistrer </B>dans le cahier de texte</div>';
		}
	}
?>
</fieldset>	
</FORM>
</BODY>
</HTML>



