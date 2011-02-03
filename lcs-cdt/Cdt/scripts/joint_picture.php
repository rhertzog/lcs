<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de traitement des pi�ces jointes-
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

function SansAccent($texte){

$accent='�����������������������������������������������������';
$noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
$texte = strtr($texte,$accent,$noaccent);
return $texte;
} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Joindre une image</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css"/>
	<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
	</head>
<body>
<h1 class='title'>Ins&#233;rer une image</h1>
<?php

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{	
	// V�rifier $nom_lien et la d�barrasser de tout antislash et tags possibles
	if (strlen($_POST['nom_lien']) > 0)
		{ 
		$nom_lien= addSlashes(strip_tags(stripslashes($_POST['nom_lien'])));
		}
	else
		{ // Si aucun commentaire n'a �t� saisi
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

		//v�rification de l'existence du r�pertoire
	if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep)) 
		{
		if (!is_dir("/home/".$_SESSION['login']."/public_html/".$sousrep))
			{$mess1= "<h3 class='ko'>1. le nom indiqu&#233; ne correspond pas � un r�pertoire"."<br /></h3>";
			$pb=1;
			}
		else $pb=0;
		} 
		else 
		{
		//cr�ation du r�pertoire
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
				$nomFichier=mb_ereg_replace("'|[[:blank:]]","_",$nomFichier);
				$nomTemporaire = $_FILES["FileSelection1"]["tmp_name"] ;
				//chargement du fichier
				copy($nomTemporaire,"/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier));							
				//test de la presence du fichier uploade
				if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier)))
					{
					$pj="/~".$_SESSION['login']."/".$sousrep."/".$nomFichier;
					//insertion du lien
					
					$image="<img src= '../../..". $pj."' >";
					echo '<script type="text/javascript">
                                         //<![CDATA[
					opener.tinyMCE.execCommand("mceInsertContent",false,"'.$image.'");			
					//opener.tinyMCE.activeEditor.selection.setContent("'.$image.'");//marche pas avec IE :(
					window.close();
					 //]]>
                                         </script>';
					$image="";
					}
			else $mess1= "<h3 class='ko'>1. Erreur dans le transfert du fichier"."<br /></h3>";
				}
			else $mess1= "<h3 class='ko'>2. Erreur dans l'importation du fichier "."<br /></h3>";
			}
	}
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
    <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<fieldset id="field7">
<legend id="legende">Transfert de l'image</legend>
<?php
///affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo '<ol>';
	echo '<li>S&#233;lectionner l\'image &#224; joindre (';
	echo ini_get( 'upload_max_filesize');
	echo '  maxi): <br /><input type="file" name="FileSelection1" size="40" /></li>';
	echo '<li>Indiquer le sous-r&#233;pertoire de votre "public_html" dans lequel sera enregistr&#233;e l\'image :  s\'il n\'existe pas, il sera cr&#233;&#233; <br /><input class="text" type="text" name="sousrep" value="Images_Cdt" size="30" /></li>';
	echo '<li>Le bouton <b> Valider </b> transfert le fichier s&#233;lectionn&#233; sur le serveur et ins&#232;re automatiquement le lien.</li>';
	echo '</ol>';
	echo '<input type="submit" name="Valider" value="Valider" class="bt" />';
	}
//affichage du resultat
	else 
	{
	if ($mess1!="") echo $mess1;
	if ($mess2!="") echo $mess2;
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
