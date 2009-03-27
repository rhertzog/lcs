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

function SansAccent($texte){

$accent='ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
$noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
$texte = strtr($texte,$accent,$noaccent);
return $texte;

} 

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<meta http-equiv="content-type" content="<HTML>
<H; charset=ISO-8859-2" >
	<TITLE></TITLE>
	<meta name="generator" content="Bluefish 1.0.7">
	<META NAME="CREATED" CONTENT="20051226;22304481">
	<META NAME="CHANGED" CONTENT="20051226;22565970">
	<LINK href="../style/style.css" rel="stylesheet" type="text/css">
	</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'>Insérer une image</H1>
<?

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{	
	

	// Vérifier $sousrep et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['sousrep']) > 0)
		{ 
		$sousrep= addSlashes(strip_tags(stripslashes($_POST['sousrep'])));
		}
	else
		{ // Si aucun commentaire n'a été saisi
		$sousrep= "";
		}	

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
			
	//traitement du  fichier 	
			if ((!empty($_FILES["FileSelection1"]["name"])) && $pb==0)
			{
			if ($_FILES["FileSelection1"]["size"]>0)
				{
				$nomFichier = SansAccent($_FILES["FileSelection1"]["name"]) ;
				$nomFichier=str_replace(' ','',$nomFichier);
				$nomTemporaire = $_FILES["FileSelection1"]["tmp_name"] ;
				//chargement du fichier
				copy($nomTemporaire,"/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier));
							
				//test de la présence du fichier uploadé
				if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier)))
					{
					$pj="/~".$_SESSION['login']."/".$sousrep."/".$nomFichier;
					//insertion du lien
					
					$image="<img src= '../../..". $pj."' >";
					echo '<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js" ></script>
					<SCRIPT language="Javascript">
					<!--
					opener.tinyMCE.execCommand("mceInsertContent",false,"'.$image.'");			
					//opener.tinyMCE.activeEditor.selection.setContent("'.$image.'");//marche pas avec IE :(
					window.close()
					// -->
					</script>';
					$image="";
					}
			else $mess1= "<h3 class='ko'>1. Erreur dans le transfert du fichier"."<BR></h3>";
				}
			else $mess1= "<h3 class='ko'>2. Erreur dans l'importation du fichier "."<BR></h3>";
			}
	}

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<fieldset>
<legend>
<FONT FACE="Trebuchet MS, sans-serif" color="#009933">  Transfert de l'image  </FONT>
</legend>

<?
//affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo '<P><H4><b>1. </b> Sélectionner l\'image à joindre : <BR><INPUT TYPE=FILE NAME="FileSelection1" SIZE=30></H4></P>
	<DIV ALIGN=LEFT><P><H4><b>3. </b>Indiquer le sous-répertoire de votre "public_html" dans lequel sera enregistrée l\'image : 
	<font size=2>  s\'il n\'existe pas, il sera créé </font><br>
	<INPUT TYPE=TEXT NAME=sousrep Value=Images_Cdt SIZE=30></H4></P>
	<P><H4><b>4. </b>Le bouton <b> Valider </b> transfert le fichier sélectionné sur le serveur et insère automatiquement le lien  </P>
	<input type="submit" name="Valider" value="Valider " >
	</H4></P></DIV>';
	}
//affichage du résultat
	else 
	{
	if ($mess1!="") echo $mess1;
	if ($mess2!="") echo $mess2;
	}
?>
</fieldset>	
</FORM>
</BODY>
</HTML>



