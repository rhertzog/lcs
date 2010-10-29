<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.2 du 25/10/2010
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'�dition de la liste des classes-
			_-=-_
   =================================================== */
session_name("Cdt_Lcs");
@session_start();
//si la page est appel�e par un utilisateur non identifi�
if (!isset($_SESSION['login']) )exit;

//si la page est appel�e par un utilisateur non admin
elseif ($_SESSION['login']!="admin") exit;

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
	{	
	// V�rifier $nom_lien et la d�barrasser de tout antislash et tags possibles
	if (strlen($_POST['list']) > 0)
		{ 
		$list= addSlashes(strip_tags(stripslashes($_POST['list'])));
		}
	else
		{ // Si aucun commentaire n'a �t� saisi
		$list= "";
		}
			$loop=0;
			$donnees_extraites = explode( ",",$list);
					for($n=0; $n<count($donnees_extraites); $n++)
					{				
					$data[$loop]=$donnees_extraites[$n];
					$loop++;
					}
	
	//cr�ation du fichier
	$nom_fichier="../Includes/data.inc.php";
	$fichier=fopen($nom_fichier,"w");
	fputs($fichier, "<?php \n");
	for ($index=0;$index<count($data);$index++)
	{fputs($fichier,"\$classe[$index]=\"$data[$index]\";\n");}
	fputs($fichier, " ?>\n");
	fclose($fichier);
	header("location: ./fichier_classes.php");
	
	}
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
	</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
<legend id="legende"> Modification de la liste des classes </legend>
<H4> Attention : pas de virgule ni de caract&#232;re CR &#224; la fin de la derni&#232;re ligne </H4>
<?
//affichage du formulaire
if (!isset($_POST['Valider']))
	{
	$NameFile= fopen("../Includes/data.inc.php","r");
if (! ($NameFile) )
	{
	echo "le fichier  ne peut &#234;tre &#233;dit&#233;";
	}
	else
	{
	echo '<DIV><TEXTAREA NAME="list" COLS=80 ROWS=4 WRAP=virtual >'; 
	while (!feof($NameFile))
		{
		$UneLigne= fgets($NameFile,255);
		$extr=explode("\"",$UneLigne);
		
		if  (count($extr)==3 ) // ce n'est pas la 1ere ou la deni�re ligne
		{
		if ($extr[0]!="\$classe[0]=") echo ",";//on ne met pas de virgule devant la premi�re classe
		echo ($extr[1]);
		}
	}
	echo '</TEXTAREA></div>';
	//affichage du bouton
		echo '<div align="left"><input type="submit" name="Valider" value="Valider" ></div>';
	}
	}

?>

</form>
</BODY>
</HTML>



