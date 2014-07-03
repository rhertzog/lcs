<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.5 du 10/04/2014
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script "erreur edt" -
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */
session_name("Lcs");
@session_start();
//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;
if (isset($_POST['retour']))
    {
    header("location: cahier_texte_prof.php");
    exit;
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Cahier de textes num&eacute;rique</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../Includes/cdt.js"></script>
        <style type='text/css'>
            .nobord {margin: 1px 100px;}
            #cfg-btn {  margin: -10px;}
        </style>
</head>
<body>

    <form  action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
	<div id="first">
	<div class="prg">
	<h2>Vous devez d'abord importer votre emploi du temps.</h2>
                <img src="../images/img_edt.png " />
                <a href="#" class="open_wi" onclick="open_new_win('http://linux.crdp.ac-caen.fr/pluginsLcs/doc_help/raccourcis.php#edt')"  ><img class="nobord" src="../images/planifier-cdt-aide.png" alt="Aide" title="Aide" /></a>

                <span id="cfg-btn"><input class="retour" type="submit" name="retour" value="" />
	</span></div></div></form>

</body>
</html>