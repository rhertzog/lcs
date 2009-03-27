<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 1.0 du 29/10/2008
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de traitement des pi�ces jointes-
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

function SansAccent($texte){

$accent='�����������������������������������������������������';
$noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
$texte = strtr($texte,$accent,$noaccent);
return $texte;

} 
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
<H1 class='title'>Joindre un document</H1>
<?

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

	// V�rifier $sousrep et la d�barrasser de tout antislash et tags possibles
	if (strlen($_POST['sousrep']) > 0)
		{ 
		$sousrep= addSlashes(strip_tags(stripslashes($_POST['sousrep'])));
		}
	else
		{ // Si aucun commentaire n'a �t� saisi
		$sousrep= "";
		}	

		//v�rification de l'existence du r�pertoire
	if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep)) 
		{
		if (!is_dir("/home/".$_SESSION['login']."/public_html/".$sousrep))
			{$mess1= "<h3 class='ko'>1. le nom indiqu� ne correspond pas � un r�pertoire"."<BR></h3>";
			$pb=1;
			}
		else $pb=0;
		} 
		else 
		{
		//cr�ation du r�pertoire
		mkdir("/home/".$_SESSION['login']."/public_html/".$sousrep);
		$pb=0;
		}
			
	//traitement du  fichier 	
			if ((!empty($_FILES["FileSelection1"]["name"])) && $pb==0)
			{
			if ($_FILES["FileSelection1"]["size"]>0)
				{
				$nomFichier = SansAccent($_FILES["FileSelection1"]["name"]) ;
				$nomTemporaire = $_FILES["FileSelection1"]["tmp_name"] ;
				//chargement du fichier
				copy($nomTemporaire,"/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier));
							
				//test de la pr�sence du fichier upload�
				if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier)))
					{
					$pj="/~".$_SESSION['login']."/".$sousrep."/".$nomFichier;
					//insertion du lien
					$Doc ="<a href= '../../..". $pj."' > ". $nom_lien." </a> ";
					echo '<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js" ></script>
					<SCRIPT language="Javascript">
					<!--
					opener.tinyMCE.execCommand("mceInsertContent",false,"'.$Doc.'");
					//opener.tinyMCE.activeEditor.selection.setContent("'.$Doc.'");
					window.close()
					// -->
					</script>';
					$Doc ="";
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
<FONT FACE="Trebuchet MS, sans-serif" color="#009933">  Transfert d'un fichier   </FONT>
</legend>

<?
//affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo '
	<DIV ALIGN=LEFT>
	<P><H4><b>1. </b> S�lectionner le document � joindre (2 Mo maxi) : <BR><INPUT TYPE=FILE NAME="FileSelection1" SIZE=30></H4></P>
	<P><H4><b>2. </b> Indiquer le nom du lien  : <BR><INPUT TYPE=TEXT NAME=nom_lien SIZE=30></H4></P>
	<P><H4><b>4. </b>Indiquer le sous-r�pertoire de votre "public_html" dans lequel sera enregistr� le fichier : 
	<font size=2>  s\'il n\'existe pas, il sera cr�� </font><br>
	<INPUT TYPE=TEXT NAME=sousrep Value=Docs_Cdt SIZE=30></H4></P>
	<P><H4><b>5. </b>Le bouton <b> Valider </b> transfert le fichier s�lectionn� sur le serveur et ins�re automatiquement le lien  </P>
	<input type="submit" name="Valider" value="Valider " >
	</H4></P></DIV>';
	}
//affichage du r�sultat
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


