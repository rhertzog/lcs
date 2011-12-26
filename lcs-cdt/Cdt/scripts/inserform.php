<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 31/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'edition d'équation -
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
exec("rm -f /usr/share/lcs/Plugins/Cdt/phpmathpublisher/img/*.png");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Expression math&#233;matique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css"/>
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
<script type="text/javascript" src="../../../libjs/jquery/jquery.js"></script>
<script type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
<script type="text/javascript" src="../Includes/barre_java.js"></script>
</head>
<body>
<h1 class='title'>Ins&#233;rer une expression math&#233;matique</h1>
<?php
//si clic sur le bouton Valider
if ((isset($_POST['Valider']) || (isset($_POST['Previsualiser']))))
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
    //vérification de l'existence du repertoire
    if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep)) 
        {
        if (!is_dir("/home/".$_SESSION['login']."/public_html/".$sousrep))
            {
            $mess1= "<h3 class='ko'>1. le nom indiqu&eacute; ne correspond pas &agrave un r&eacute;pertoire"."<br /></h3>";
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
    $size=$_POST['taille'];
    $pathtoimg='../phpmathpublisher/img/';

    if ( isset($equation) && $equation!='' ) 
        {	
        $retour =mathfilter($message,$size,$pathtoimg);
        $lien=explode('"',$retour);
        $name=explode('/',$lien[1]);
        $TextAlt=$lien[5];//texte alternatif
        $nomFichier=$name[count($name)-1];//nom du fichier
        if ( $name[count($name)-1] != "") $cmd=" mv ../phpmathpublisher/img/".$name[count($name)-1]." /home/".$_SESSION['login']."/public_html/".$sousrep."/";
        exec($cmd);
        //echo $pathtoimg;/**/
        }

    //test de la presence du fichier uploade
    if (file_exists("/home/".$_SESSION['login']."/public_html/".$sousrep."/".stripslashes($nomFichier)))
        {
        $pj="/~".$_SESSION['login']."/".$sousrep."/".$nomFichier;
        //insertion du lien
        //$image="<img src= '../../..". $pj."' >";
        $image="<img src= '../../..". $pj."' ".$lien[2]."'".$lien[3]."' >";
        echo '<script type="text/javascript">
         //<![CDATA[
        opener.tinyMCE.execCommand("mceInsertContent",false,"'.$image.'");
        //window.opener.tinyMCE.activeEditor.selection.setContent("'.$image.'");
        window.close();
         //]]>
         </script>';
        $image="";
        }
    }

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
    <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<fieldset id="field7">
<legend id="legende">Edition de la formule</legend>

<?php
$choix=array("11","12","13","14","15");
//affichage du formulaire
if (!isset($_POST['Valider']))
    {
    echo '<ol>';
    echo '<li>Saisir la formule en respectant la syntaxe d&#233;crite <a href="../phpmathpublisher/doc_fr/help_fr.html" onclick="aidemath_popup(); return false"> <b>ICI</b></a> :
    <textarea name="equation" cols="50" rows="3" >'.$equation.'</textarea></li>';
    echo '<li>Taille ';
    echo "<select name='taille' style='background-color:#E6E6FA'>";
    foreach ($choix as $cle => $valeur)
        { 
        echo "<option value=\"$valeur\"";
        if ($valeur==$_POST['taille']) {echo ' selected="selected"';}
        echo ">$valeur</option>\n";
        }
    echo "</select></li>";
    echo '<li>V&#233;rifier l\'image de la formule car elle n\'est plus modifiable lorsqu\'elle est valid&#233;e<br />';
    echo '<input type="submit" class="bt" name="Previsualiser" value="Pr&#233;visualiser" />';
    if (isset($_POST['Previsualiser']))
        {
        echo '<div id="eqt">';
        include("../phpmathpublisher/mathpublisher.php") ;
        $message="<m>".$equation."</m>";
        $size=$_POST['taille'];
        $pathtoimg='../phpmathpublisher/img/';
        if ( isset($equation) && $equation!='' ) 
            {	
            $retour =mathfilter($message,$size,$pathtoimg);
            echo "&nbsp;".$retour;
            }
        echo '</div></li>';
        }
    echo '<li>Indiquer le sous-r&#233;pertoire de votre "public_html" dans lequel sera enregistr&#233;e l\'image :   s\'il n\'existe pas, il sera cr&#233;&#233; <br /><input class="text" type="text" name="sousrep" value="Images_Cdt" size="30" /></li>';
    echo '<li>Le bouton <b> Valider </b> transf&#232;re l\'image sur le serveur et ins&#232;re automatiquement le lien</li>';
    echo '</ol>';
    echo '<input type="submit" name="Valider" value="Valider" class="bt" />';
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