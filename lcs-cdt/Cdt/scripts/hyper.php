<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de traitement des images jointes-
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=windows-1252">
	<TITLE></TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'>Insérer un lien hypertexte</H1>
<?

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
					echo '<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js" ></script>
					<SCRIPT language="Javascript">
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
<fieldset>
<legend>
<FONT FACE="Trebuchet MS, sans-serif" color="#009933">Définition du lien  </FONT>
</legend>

<?
//affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo '<P><H4><b>1. </b> Indiquer l\'URL du lien  : <BR><INPUT TYPE=TEXT NAME=lien SIZE=50 value="http://"></H4></P>
	<P><H4><b>2. </b> Indiquer le nom du lien  : <BR><INPUT TYPE=TEXT NAME=nom_lien SIZE=30></H4></P>
	<DIV ALIGN=LEFT>
	<input type="submit" name="Valider" value="Valider " >
	</H4></P></DIV>';
	}


?>
</fieldset>	
</FORM>
</BODY>
</HTML>



