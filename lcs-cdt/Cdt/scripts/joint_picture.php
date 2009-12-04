<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.0 du 25/10/2009
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de traitement des pièces jointes-
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
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" >
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
<H1 class='title'>Ins&#233;rer une image</H1>
<?

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{	
	// Vérifier $nom_lien et la débarrasser de tout antislash et tags possibles
	if (strlen($_POST['nom_lien']) > 0)
		{ 
		$nom_lien= addSlashes(strip_tags(stripslashes($_POST['nom_lien'])));
		}
	else
		{ // Si aucun commentaire n'a été saisi
		$nom_lien= "Fichier joint";
		}

	// Verifier $sousrep et la debarrasser de tout antislash et tags possibles
	if (strlen($_POST['sousrep']) > 0)
		{ 
		$sousrep= addSlashes(strip_tags(stripslashes($_POST['sousrep'])));
		}
	else
		{ // Si aucun commentaire n'a ete saisi
		$sousrep= "";
		}	

		//vérification de l'existence du répertoire
	if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep)) 
		{
		if (!is_dir("/home/".$_SESSION['login']."/public_html/".$sousrep))
			{$mess1= "<h3 class='ko'>1. le nom indiqu&#233; ne correspond pas à un répertoire"."<BR></h3>";
			$pb=1;
			}
		else $pb=0;
		} 
		else 
		{
		//création du répertoire
		mkdir("/home/".$_SESSION['login']."/public_html/".$sousrep);
		exec ("/usr/bin/sudo /usr/share/lcs/scripts/chacces.sh 770 ".$_SESSION['login']." "."/home/".$_SESSION['login']."/public_html/".$sousrep);
		$pb=0;
		}
			
	//traitement du  fichier 	
			if ((!empty($_FILES["FileSelection1"]["name"])) && $pb==0)
			{
			if ($_FILES["FileSelection1"]["size"]>0)
				{
				$nomFichier = SansAccent($_FILES["FileSelection1"]["name"]) ;
				$nomFichier=ereg_replace("'|[[:blank:]]","_",$nomFichier);
				$nomTemporaire = $_FILES["FileSelection1"]["tmp_name"] ;
				//chargement du fichier
				copy($nomTemporaire,"/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier));							
				//test de la presence du fichier uploade
				if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier)))
					{
					$pj="/~".$_SESSION['login']."/".$sousrep."/".$nomFichier;
					//insertion du lien
					
					$image="<img src= '../../..". $pj."' >";
					echo '<SCRIPT language="Javascript">
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
<fieldset id="field7">
<legend id="legende">Transfert de l'image</legend>
<?
///affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo '<ol>';
	echo '<li>S&#233;lectionner l\'image &#224; joindre (';
	echo ini_get( 'upload_max_filesize');
	echo '  maxi): <BR><input type=file name="FileSelection1" size=40></li>';
	echo '<li>Indiquer le sous-r&#233;pertoire de votre "public_html" dans lequel sera enregistr&#233;e l\'image : <font size=3>  s\'il n\'existe pas, il sera cr&#233;&#233; </font><br><input class="text" type=text name=sousrep Value=Images_Cdt SIZE=30></li>';
	echo '<li>Le bouton <b> Valider </b> transfert le fichier s&#233;lectionn&#233; sur le serveur et ins&#232;re automatiquement le lien.</li>';
	echo '</ol>';
	echo '<input type="submit" name="Valider" value="Valider" class="bt">';
	}
//affichage du resultat
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



