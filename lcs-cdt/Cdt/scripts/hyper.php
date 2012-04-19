<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 20/04/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de traitement des hyper-liens -
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
<title>Ins&eacute;rer un lien</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css"/>
		<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
</head>
<body>
<h1 class='title'>Ins&eacute;rer un lien hypertexte</h1>
<?php
//si clic sur le bouton Valider
if (isset($_POST['Valider']))
    {	
    if (strlen($_POST['nom_lien']) > 0) $nom_lien= addSlashes(strip_tags(stripslashes($_POST['nom_lien'])));
    else $nom_lien= "site";
    if (strlen($_POST['lien']) > 0) $lien= addSlashes(strip_tags(stripslashes($_POST['lien'])));
    else  $lien= "";
    //traitement
    $url_parsee = parse_url($lien);  
    $host = $url_parsee["host"];
    $path = isset($url_parsee["path"]) ? trim($url_parsee['path']) : '';
    $no_code = 0;
    //connexion par socket
    if ($fp = @fsockopen($host,80))
        {
        //traitement du path
        if(substr($path,strlen($path)-1) != '/')
            {
            if(!ereg("\.",$path))
            $path .= "/";
            }
        //envoi de la requete HTTP
        fputs($fp,"GET ".$path." HTTP/1.1\r\n"); 
        fputs($fp,"Host: ".$host."\r\n");
        fputs($fp,"Connection: close\r\n\r\n");
        //on lit le fichier
        $line = fread($fp,255);
        $en_tete = $line;
        //on lit tant qu'on n'est pas la fin du fichier ou
        // qu'on trouve le debut du code html...
        while (!feof($fp) && !ereg("<",$line) )
            {
            $en_tete .= $line;
            $line = fread($fp,255);
            }
        fclose($fp);
        //on switch sur le code HTTP renvoye
        $no_code = substr($en_tete,9,3);
         if ($no_code >=200 && $no_code < 308 )
            {
            $Url="<a href= '". $lien ."' > ". $nom_lien." </a> ";			
            //insertion du lien
            echo '<script type="text/javascript">
             //<![CDATA[
            opener.tinyMCE.execCommand("mceInsertContent",false,"'.$Url.'");
            //opener.tinyMCE.activeEditor.selection.setContent("'.$Url.'");
            window.close();
             //]]>
             </script>';
            $Url="";
            }
        else $mess1= "<h3 class='ko'> l'URL fournie n'est pas valide"."</h3>";
        }
       else $mess1= "<h3 class='nook'> l'URL fournie n'est pas valide"."</h3>"; 
    }
?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
<div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<fieldset id="field7">
<legend id="legende">D&eacute;finition du lien</legend>
<?php
//affichage du formulaire
if (!isset($_POST['Valider']))
    {
    echo '<ol>';
    echo '<li>Indiquer l\'URL du lien  : <br /><input class="text" type="text" name="lien" size="50" value="http://" /></li>';
    echo '<li>Indiquer le nom du lien  : <br /><input class="text" type="text" name="nom_lien" size="30" /></li>';
    echo '</ol>';
    echo '<input type="submit" name="Valider" value="Valider" class="bt" />';
    }
else 
    
    if ($mess1!="") echo $mess1;
?>
</fieldset>
</div>
</form>
<?php
include ('../Includes/pied.inc');
?>
</body>
</html>
