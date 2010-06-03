<?
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.0 du 25/10/2009
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'�dition d'�quation -
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
exec("rm -f /usr/share/lcs/Plugins/Cdt/phpmathpublisher/img/*.png");
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
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/jquery-1.3.2.min.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
	<script language="Javascript" type="text/javascript" src="../Includes/barre_java.js"></script>
</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<H1 class='title'>Ins�rer une expression math�matique</H1>
<?

//si clic sur le bouton Valider
if ((isset($_POST['Valider']) || (isset($_POST['Previsualiser']))))
	{
	// V�rifier $equation et la d�barrasser de tout antislash et tags possibles
	if (strlen($_POST['equation']) > 0)
		{ 
		$equation= ($_POST['equation']);
		}
		
	}
if (isset($_POST['Valider']) )
	{		
	$sousrep='Images_Cdt';
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
		
		//cr�ation, transfert de l'image de l'image 
		
		
		include("../phpmathpublisher/mathpublisher.php") ;
		$message="<m>". $equation."</m>";
		$size='11';
		$pathtoimg='../phpmathpublisher/img/';
		
		if ( isset($equation) && $equation!='' ) 
		{	
		$retour =mathfilter($message,$size,$pathtoimg);
		$lien=explode('"',$retour);
		$name=explode('/',$lien[1]);
		$TextAlt=$lien[5];//texte alternatif
		$nomFichier=$name[count($name)-1];//nom du fichier
		$cmd=" mv ../phpmathpublisher/img/".$name[count($name)-1]." /home/".$_SESSION['login']."/public_html/".$sousrep."/";
		exec($cmd);
		//echo $pathtoimg;/**/
		}
		
		//test de la pr�sence du fichier upload�
				if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier)))
					{
					$pj="/~".$_SESSION['login']."/".$sousrep."/".$nomFichier;
					//insertion du lien
					//$image="<img src= '../../..". $pj."' >";
					$image="<img src= '../../..". $pj."' ".$lien[2]."'".$lien[3]."' >";
					echo '<SCRIPT language="Javascript">
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
<fieldset id="field7">
<legend id="legende">Edition de la formule</legend>

<?
//affichage du formulaire
if (!isset($_POST['Valider']))
	{
	echo '<ol>';
	echo '<li>Saisir la formule en respectant la syntaxe d�crite <a href="../phpmathpublisher/doc_fr/help_fr.html" onClick="aidemath_popup(); return false"> <b>ICI</b></a> : </br><TEXTAREA NAME="equation" COLS=50 ROWS=3 WRAP=virtual >'.$equation.'</TEXTAREA></li>';
	echo '<li>V�rifier l\'image de la formule car elle n\'est plus modifiable lorsqu\'elle est valid�e</li>';
	echo '<input type="submit" class="bt" name="Previsualiser" value="Pr�visualiser" >';
	if (isset($_POST['Previsualiser']))
	{
	echo '<div id="eqt">';
	include("../phpmathpublisher/mathpublisher.php") ;
	$message="<m>".$equation."</m>";
	$size='11';
	$pathtoimg='../phpmathpublisher/img/';
	if ( isset($equation) && $equation!='' ) 
	{	
	$retour =mathfilter($message,$size,$pathtoimg);
	
	echo "&nbsp".$retour;
	
	}
	echo '</div>';
	}
	
	echo '<li>Indiquer le sous-r�pertoire de votre "public_html" dans lequel sera enregistr�e l\'image : <font size=3>  s\'il n\'existe pas, il sera cr�� </font><br><input class="text" type=text name=sousrep Value=Images_Cdt SIZE=30></li>';
	echo '<li></b>Le bouton <b> Valider </b> transfert l\'image sur le serveur et ins�re automatiquement le lien</li>';
	echo '</ol>';
	echo '<input type="submit" name="Valider" value="Valider" class="bt">';
	}

?>
</fieldset>	
</FORM>
</BODY>
</HTML>