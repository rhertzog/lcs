<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 10/04/2014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'edition de la liste des classes-
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */
session_name("Lcs");
@session_start();
include "../Includes/check.php";
if (!check()) exit;
//si la page est appelee par un utilisateur non identife
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non admin
elseif ($_SESSION['login']!="admin") exit;

//si clic sur le bouton Valider
if (isset($_POST['Valider']))
    {
    // Verifier $nom_lien et la debarrasser de tout antislash et tags possibles
    if (strlen($_POST['list']) > 0)
        {
        $list= addSlashes(strip_tags(stripslashes($_POST['list'])));
        }
    else
        { // Si aucun commentaire n'a ete saisi
        $list= "";
        }
    $loop=0;
    $donnees_extraites = explode( ",",$list);
    for($n=0; $n<count($donnees_extraites); $n++)
        {
        $data[$loop]=$donnees_extraites[$n];
        $loop++;
        }

    //creation du fichier
    $nom_fichier="../Includes/data.inc.php";
    $fichier=fopen($nom_fichier,"w");
    fputs($fichier, "<?php \n");
    for ($index=0;$index<count($data);$index++)
        {
        fputs($fichier,"\$classe[$index]=\"$data[$index]\";\n");
        }
    fputs($fichier, " ?>\n");
    fclose($fichier);
    header("location: ./fichier_classes.php");
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
	</head>
<body>
<fieldset id="field7">
<legend id="legende"> Modification de la liste des classes </legend>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
 <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<?php
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
        echo '<p></p><div class="perso"><textarea name="list" cols="80" rows="4"  >';
        while (!feof($NameFile))
            {
            $UneLigne= fgets($NameFile,255);
            $extr=explode("\"",$UneLigne);
            if  (count($extr)==3 ) // ce n'est pas la 1ere ou la deniere ligne
                {
                if ($extr[0]!="\$classe[0]=") echo ",";//on ne met pas de virgule devant la premiere classe
                echo ($extr[1]);
                }
            }
        echo '</textarea></div> Attention : pas de virgule ni de caract&#232;re CR &#224; la fin de la derni&#232;re ligne <br /><br />';
        //affichage du bouton
        echo '<div ><input type="submit" name="Valider" value="Valider" /></div>';
        }
    }
?>
 </div>
</form>
</fieldset>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>