<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 31/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de traitement des pieces jointes-
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
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

function SansAccent($texte){
    $accent='ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
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
    // Verifier $nom_lien et la debarrasser de tout antislash et tags possibles
    if (strlen($_POST['nom_lien']) > 0) $nom_lien= addSlashes(strip_tags(stripslashes($_POST['nom_lien'])));
    else $nom_lien= "Fichier joint";
    // Verifier $sousrep et la debarrasser de tout antislash et tags possibles
    if (strlen($_POST['sousrep']) > 0) $sousrep= addSlashes(strip_tags(stripslashes($_POST['sousrep'])));
    else $sousrep= "";
    //verification de l'existence du repertoire
    if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep))
        {
        if (!is_dir("/home/".$_SESSION['login']."/public_html/".$sousrep))
            {
            $mess1= "<h3 class='ko'>Le nom indiqu&#233; ne correspond pas &agrave; un r&#233;pertoire"."<br /></h3>";
            $pb=1;
            }
        else $pb=0;
        }
    else
        {
        //creation du repertoire
        mkdir("/home/".$_SESSION['login']."/public_html/".$sousrep);
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/chacces.sh 770 ".$_SESSION['login']." "."/home/".$_SESSION['login']."/public_html/".$sousrep);
        $pb=0;
        }

    //traitement du  fichier
    if ((!empty($_FILES["FileSelection1"]["name"])) && $pb==0)
        {
        if ($_FILES["FileSelection1"]["size"]>0)
            {
            $nomFichier =mb_ereg_replace("[^A-Za-z0-9\.\-_]","",$_FILES["FileSelection1"]["name"]) ;
            $nomFichier=mb_ereg_replace("'|[[:blank:]]","_",$nomFichier);
            $nomTemporaire = $_FILES["FileSelection1"]["tmp_name"] ;
            //chargement du fichier
            $size = getimagesize($nomTemporaire);
            if ($size[2] >0 &&  $size[2] <=16)
                {
                $max_width = 700;
                if ($size[0] > $max_width)
                    {
                    $ratio = $max_width/$size[0];
                    $width = intval($ratio*$size[0]);
                    $height = intval($ratio*$size[1]);
                    }
                else
                    {
                    $width=$size[0];
                    $height =$size[1];
                    }
                copy($nomTemporaire,"/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier));
                //test de la presence du fichier uploade
                if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier)))
                    {
                    $pj="/~".$_SESSION['login']."/".$sousrep."/".$nomFichier;
                    if (isset($_POST['only_lien']))   $image="<a href= '". $pj ."' > image </a> ";
                    else $image="<img src= '../../..". $pj."' height='".$height."' width='".$width."' >";
                    echo '<script type="text/javascript">
                    //<![CDATA[
                    opener.tinyMCE.execCommand("mceInsertContent",false,"'.$image.'");
                    //opener.tinyMCE.activeEditor.selection.setContent("'.$image.'");//marche pas avec IE :(
                    window.close();
                    //]]>
                    </script>';
                    $image="";
                    }
                else $mess1= "<h3 class='nook'> Erreur dans le transfert du fichier"."<br /></h3>";
                }
            else $mess1= "<h3 class='nook'> Fichier non conforme "."<br /></h3>";
            }
        else $mess1= "<h3 class='nook'> Erreur dans l'importation du fichier "."<br /></h3>";
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
    echo '<li><input type="checkbox" name="only_lien"  />
              <label for="boxcheck"> Afficher le lien &agrave; la place de l\'image </label></li>';
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