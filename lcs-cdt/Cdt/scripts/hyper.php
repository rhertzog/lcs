<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de traitement des hyper-liens -
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
//si la page est appelée par un utilisateur non identifié
if (!isset($_SESSION['login']) )exit;

//si la page est appelée par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Ins&eacute;rer un lien</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css"/>
		<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	</head>
<body>
<h1 class='title'>Ins&eacute;rer un lien hypertexte</h1>
<?php
//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{	
	
	if (strlen($_POST['nom_lien']) > 0)
		{ 
		$nom_lien= addSlashes(strip_tags(stripslashes($_POST['nom_lien'])));
		}
	else
		{ // Si aucun commentaire n'a été saisi
		$nom_lien= "site";
		}
	
	// Vérifier $nom_lien et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['lien']) > 0)
		{ 
		$lien= addSlashes(strip_tags(stripslashes($_POST['lien'])));
		}
	else
		{ // Si aucun commentaire n'a été saisi
		$lien= "";
		}

//traitement  	
					$Url="<a href= '". $lien ."' > ". $nom_lien." </a> ";			
					//insertion du lien
					echo '<script type="text/javascript">
                                         //<![CDATA[
					opener.tinyMCE.execCommand("mceInsertContent",false,"'.$Url.'");
					//opener.tinyMCE.activeEditor.selection.setContent("'.$Url.'");
					window.close();
					 //]]>
                                         </script>';
					$Url="";
					}
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
    <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<fieldset id="field7">
<legend id="legende">Définition du lien</legend>
<?php
//affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo '<ol>';
	echo '<li>Indiquer l\'URL du lien  : <br /><input class="text" type="text" name="lien" size="50" value="http://" /></li>';
	echo '<li>Indiquer le nom du lien  : <br /><input class="text" type="text" name="nom_lien" size="30" /></li>';
	echo '</ol>';
	echo '<input type="submit" name="Valider" value="Valider" class="bt" />';
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

