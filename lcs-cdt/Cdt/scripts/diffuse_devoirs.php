<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de diffusion -
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 
session_name("Cdt_Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
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
 WHERE login='{$_SESSION['login']}' AND classe != '$clas_act' and matiere = '$mat' ORDER BY classe ASC ";
 
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Devoirs multiples</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css"/>
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body >
<h1 class='title'>Planifier un devoir pour plusieurs classes</h1>
<?php

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{
	//initialisation des variables de session
	unset ($_SESSION['sel_cl']);
	//m�morisation des donn�es dans des variables de session		
	$_SESSION['sel_cl']=$_POST['sel_cl'];
	//fermeture du popup
	echo '<script type="text/javascript">
                    //<![CDATA[
                    window.close();
                   //]]>
                    </script>';
	}

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
 <div>
 <input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>"/>
<fieldset id="field7">
<legend id="legende">S&eacute;lection des classes </legend>

<?php
//affichage du formulaire

if (!isset($_POST['Valider']))
	{
	//si pas de classe de m�me niveau dans la mati�re
	if (mysql_num_rows($result)==0) 
		echo '<h4> Apparemment, vous n\'avez pas d\'autre classe  en <b>'.$mat.'</b> !<br /> V&eacute;rifiez que la mati&egrave;re n\'est
		pas orthographi&eacute;e diff&eacute;remment pour les autres classes .<br /><br /></h4><div><input type="submit" name="Valider"
		value="OK " /></div>';
	//sinon, affichage des classes de m�me niveau 	
	else
		{
		echo '<ul class="perso"> <li>Si vous avez un cours compos&eacute; d\'&eacute;l&egrave;ves issus de plusieurs classes,
		 vous pouvez planifier en une fois, ce devoir pour tous vos &eacute;l&egrave;ves.</li>
		<li>Lorsque vous enregistrerez votre devoir de <b>'.$mat.'</b> en <b>'.$clas_act.'</b> , il sera aussi planifi&eacute;  pour  les classes que vous aurez s&eacute;lectionn&eacute;es ci-dessous </li></ul>';
		echo '<div id="sel-classe"><table id="diff-dev"><tr>';
		for ($loop=1; $loop < count ($data1)+1  ; $loop++)
			{
			echo '<td><input type="checkbox" name="sel_cl[]" value="'.$data1[$loop-1].'#'.$data2[$loop-1].'" />'.$data1[$loop-1].'</td>';
			if (($loop %5)==0 ) echo'</tr><tr>';
			}
		echo '</tr></table></div>';
		echo '<div><input type="submit" name="Valider" value="Valider la s&eacute;lection" class="bt" /></div>';
		echo '<br /><div class="notabene"> N\'oubliez pas apr&egrave;s avoir Valider la s&eacute;lection, de cliquer sur <b>Enregistrer </b>dans la page "Planning" </div>';
		}
	}
?>
</fieldset>	
</div>
</form>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>