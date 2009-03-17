<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'édition d'équation -
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

function SansAccent($texte){

$accent='ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
$noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
$texte = strtr($texte,$accent,$noaccent);
return $texte;

} 
exec("rm -f /usr/share/lcs/Plugins/Cdt/phpmathpublisher/img/*.png");
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
<H1 class='title'>Insérer une expression mathématique</H1>
<?

//si clic sur le bouton Valider
if ((isset($_POST['Valider']) || (isset($_POST['Prévisualiser']))))
	{
	// Vérifier $equation et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['equation']) > 0)
		{ 
		$equation= ($_POST['equation']);
		}
	}
if (isset($_POST['Valider']) )
	{		
	$sousrep='Images_Cdt';
	//vérification de l'existence du répertoire
	if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep)) 
		{
		if (!is_dir("/home/".$_SESSION['login']."/public_html/".$sousrep))
			{$mess1= "<h3 class='ko'>1. le nom indiqué ne correspond pas à un répertoire"."<BR></h3>";
			$pb=1;
			}
		else $pb=0;
		} 
		else 
		{
		//création du répertoire
		mkdir("/home/".$_SESSION['login']."/public_html/".$sousrep);
		$pb=0;
		}
		
		//création, transfert de l'image de l'image 
		
		
		include("../phpmathpublisher/mathpublisher.php") ;
		$message="<m>". $equation."</m>";
		$size='11';
		$pathtoimg='../phpmathpublisher/img/';
		
		if ( isset($equation) && $equation!='' ) 
		{	
		$retour =mathfilter($message,$size,$pathtoimg);
		$lien=split('"',$retour);
		$name=split('/',$lien[1]);
		$TextAlt=$lien[5];//texte alternatif
		$nomFichier=$name[count($name)-1];//nom du fichier
		$cmd=" mv ../phpmathpublisher/img/".$name[count($name)-1]." /home/".$_SESSION['login']."/public_html/".$sousrep."/";
		exec($cmd);
		//echo $pathtoimg;/**/
		}
		
		//test de la présence du fichier uploadé
				if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier)))
					{
					$pj="/~".$_SESSION['login']."/".$sousrep."/".$nomFichier;
					//insertion du lien
					//$image="<img src= '../../..". $pj."' >";
					$image="<img src= '../../..". $pj."' ".$lien[2]."'".$lien[3]."' >";
					echo '<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js" ></script>
					<SCRIPT language="Javascript">
					<!--
					opener.tinyMCE.execCommand("mceInsertContent",false,"'.$image.'");
					//window.opener.tinyMCE.activeEditor.selection.setContent("'.$image.'");
					window.close()
					// -->
					</script>';
					$image="";
					}
		
	}

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<fieldset>
<legend>
<FONT FACE="Trebuchet MS, sans-serif" color="#009933"> Edition de la formule  </FONT>
</legend>

<?
//affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo '<P><H4><b>1. </b> Saisir la formule en respectant la syntaxe décrite <a href= ../phpmathpublisher/doc_fr/help_fr.html target="_blank"> <b>ICI</b></a> : <BR>
	<TEXTAREA NAME="equation" COLS=50 ROWS=3 WRAP=virtual >'.$equation.' </TEXTAREA></H4></P>
	<DIV ALIGN=LEFT>
	<P><H4><b>2. </b>Vérifier l\'image de la formule car elle n\'est plus modifiable lorsqu\'elle est validée </P>
	<input type="submit" name="Prévisualiser" value="Prévisualiser" >';
	if (isset($_POST['Prévisualiser']))
	{
	include("../phpmathpublisher/mathpublisher.php") ;
	$message="<m>". $equation."</m>";
	$size='11';
	$pathtoimg='../phpmathpublisher/img/';
	if ( isset($equation) && $equation!='' ) 
	{	
	$retour =mathfilter($message,$size,$pathtoimg);
	echo "&nbsp".$retour;
	
	}
	}
	
	echo '</H4></P>
	<P><H4><b>4. </b>Le bouton <b> Valider </b> transfert l\'image sur le serveur et insère automatiquement le lien  </P>
	<input type="submit" name="Valider" value="Valider " >
	</H4></P></DIV>';
	}

?>
</fieldset>	
</FORM>
</BODY>
</HTML>



