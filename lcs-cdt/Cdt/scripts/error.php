<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script "erreur lien non valide" -
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */
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
	<div id="first">
	<div class="prg">
		<h2><br /><br />Le lien fourni n'est <? echo $_GET['error']; ?> valide.</h2>
	</div></div>
</body>
</html>	