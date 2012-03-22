<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.4 du 15/03/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'insertion de video -
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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Vid&eacute;o</title>
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
<h1 class='title'>Ins&#233;rer une vid&eacute;o</h1>
<?php
//si clic sur le bouton Valider

if (isset($_POST['Valider']) ||  isset($_POST['Preview']))
    {
        $video="";
        $match=array();
        $pre_regex = '#<iframe .+?'.'></iframe>#';
        preg_match($pre_regex,$_POST['code'],$match);
       $video = $match[0];
       if ($video=="")
       {
       $pre_regex = '#<OBJECT .+?'.'></OBJECT>#';
        preg_match($pre_regex,$_POST['code'],$match);
       $video = $match[0];
       }
       if (isset($_POST['Valider']))
       {
        echo '<script type="text/javascript">
         //<![CDATA[
        opener.tinyMCE.execCommand(\'mceInsertContent\',false,\''.$video.'\');
        window.close();
         //]]>
         </script>';
        $video="";
       }
    }

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
    <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<fieldset id="field7">
<legend id="legende">Lien de la vid&eacute;o</legend>

<?php
//affichage du formulaire
if (! get_magic_quotes_gpc()) {
if (!isset($_POST['Valider']))
    {
    echo '<ol><li>Vid&#233;os externes<ul>';
    echo '<li>Seules des vid&#233;os YouTube, Dailymotion peuvent  &ecirc;tre ins&eacute;r&eacute;es </li>
            <li> Copiez le code d\'importation qui doit commencer par &lt;iframe ...  et se terminer par &lt;/iframe></li>
                <ul>
                <li>Sur YouTube.com faites "Partager -> Int&eacute;grer " </li>
                <li> Sur Dailymotion.com faites "Partager -> Exporter "</li>
                </ul></ul></li>
                <li> Vid&#233;os Mediacenter (Claroline)
                <ul><li> Copiez le code d\'importation qui doit commencer par &lt;OBJECT ...  et se terminer par &lt;/OBJECT></li></ul>
            </li>
        <li> Coller ci-dessous le code d\'importation </li></ol>';

    echo'<textarea name="code" cols="50" rows="3" >'.$_POST['code'].'</textarea>';
    echo '</ul>';
    echo '<input type="submit" name="Preview" value="Pr&eacute;visualiser" class="bt" />';
    if (isset($_POST['Preview']))
       {
       echo $video;
       }
    echo '<input type="submit" name="Valider" value="Valider" class="bt" />';
    }
}
else
{
  echo ' La configuration de votre serveur (magic_quotes_gpc=on) ne permet pas d\'utiliser cette fonctionnalit&eacute;. <br /> Contactez votre administrateur r&eacute;seau';
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