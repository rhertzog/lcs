<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 10/04/2014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'edition LaTeX -
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

session_name("Lcs");
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
<title>Expression LaTeX</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link href="../style/style.css" rel="stylesheet" type="text/css"/>
<!--[if IE]>
<link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
<![endif]-->
<script type="text/javascript" src="../../../libjs/jquery/jquery.js"></script>
<script type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
<script type="text/javascript" src="../Includes/barre_java.js"></script>
<script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    tex2jax: {
      inlineMath: [["#","#"],["\\(","\\)"]]
    }
  });
</script>
<script type="text/javascript"
                src="../../../libjs/MathJax/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
                </script>
</head>
<body>
    <script>
   (function () {
    var QUEUE = MathJax.Hub.queue;
    var math = null;
    QUEUE.Push(function () {
    math = MathJax.Hub.getAllJax("MathOutput")[0];
    });
    window.UpdateMath = function (TeX) {
    QUEUE.Push(["Text",math,"\\displaystyle{"+TeX+"}"]);
    }
  })();
</script>
<h1 class='title'>Ins&#233;rer une expression LaTeX</h1>
<?php
//si clic sur le bouton Valider

if (isset($_POST['Valider']) )
    {
    $form="";
    if ($_POST['enligne']==1) $form.='$'.addslashes($_POST['expression']).'$';
    else $form.='$$'.addslashes($_POST['expression']).'$$';
    echo '<script type="text/javascript">
         //<![CDATA[
        opener.tinyMCE.execCommand("mceInsertContent",false,"'.$form.'");
        window.close();
         //]]>
         </script>';
    }

?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
 <div><input name="TA" type="hidden"  value="<?php echo md5($_SESSION['RT'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
<fieldset id="field7">
<legend id="legende">Edition de l'expression</legend>

<?php

//affichage du formulaire
if (!isset($_POST['Valider']))
    {
    echo '<ol>';
    echo '<li>Saisir une expression math&eacute;matique au format LaTeX
    <textarea name="expression" cols="50" rows="3" onKeyup="UpdateMath(this.value)" ></textarea>';
    echo ' <div id="MathOutput"> <br />#{}#</div>';
    echo '<li>Type d\'affichage : ';
    echo '<p><input type="radio" name="enligne" value="1"';
    echo '/> Affichage dans la continuit&eacute; du texte<br />
    <input type="radio" name="enligne" value="0"  checked="checked"';
    echo ' /> Affichage s&eacute;par&eacute; (saut de ligne) <br /></li>';
    echo '<li>';
    echo 'Remarque : Vous pouvez saisir les expressions LaTeX directement dans la zone d\'&eacute;dition du cahier de textes, sans passer par ce formulaire. Une expression
       encadr&eacute;e par  &dollar; s\'affichera dans la continuit&eacute; du texte. Encadr&eacute;e par  &dollar;&dollar;, l\'expression s\'affichera sur une autre ligne' ;
    echo '</li>';
    echo '</ol>';
   echo ' <div id="MathOutput"> #{}#</div>';
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