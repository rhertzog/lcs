<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de traitement des hyper-liens -
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" >
	<TITLE></TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
		<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'>Insérer un lien hypertexte</H1>
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
					$Url="<a href= '". $lien ."' target='_blank'> ". $nom_lien." </a> ";			
					//insertion du lien
					echo '<SCRIPT language="Javascript">
					<!--
					opener.tinyMCE.execCommand("mceInsertContent",false,"'.$Url.'");
					//opener.tinyMCE.activeEditor.selection.setContent("'.$Url.'");
					window.close()
					// -->
					</script>';
					$Url="";
					}
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<INPUT name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>">
<fieldset id="field7">
<legend id="legende">Définition du lien</legend>
<?php
//affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo '<ol>';
	echo '<li>Indiquer l\'URL du lien  : <br /><input class="text" type=text name=lien SIZE=50 value="http://"></li>';
	echo '<li>Indiquer le nom du lien  : <br /><input class="text" type=text name=nom_lien SIZE=30></li>';
	echo '</ol>';
	echo '<input type="submit" name="Valider" value="Valider" class="bt">';
	}
?>
</fieldset>	
</FORM>
</BODY>
</HTML>

