<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 25/09/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
session_name("Lcs");
@session_start();
if (isset($_SESSION['login'])) $login=$_SESSION['login'];
else   $login="";
require  "/var/www/lcs/includes/headerauth.inc.php";
require "/var/www/Annu/includes/ldap.inc.php";
require "/var/www/Annu/includes/ihm.inc.php";
include("core/includes/functions.inc.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=emulateie8" />
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta http-equiv="content-language" content="fr">
<title>...::: Bureau LCS :::...</title><!-- recuperation des params etab ds lcs.conf -->
<meta name="description" content="LCS - Environnement Num&eacute;rique de Travail du <?php echo $organizationalunit."  ".$organization." de ".$locality." - ".$province; ?>. Le num&eacute;rique au service de l'&eacute;ducation." />
<meta name="keywords" content="Environnement Num&eacute;rique de Travail, Web Desktop, Bureau web, Education, FOAD, Formation &agrave; distance, communication, LCS, linux, e-learning" />
<meta name="author" content="LcsDevTeam" />
<link href="core/css/html.css" rel="stylesheet" />
<link href="core/libs/jquery-ui/css/smoothness/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
<!--<link href="core/finder/ui.theme.css" rel="stylesheet" type="text/css" media="screen"> -->
<link href="core/css/desktop.css" rel="stylesheet" />
<link href="core/css/ui.notify.css" t ype="text/css" rel="stylesheet" />
<link href="core/css/jquery.context_menu.css" rel="stylesheet" type="text/css" />
<link href="core/libs/poshytip/src/tip-twitter/tip-twitter.css" rel="stylesheet" type="text/css" />
<link href="core/libs/colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
<link href='core/libs/uniform/css/uniform.agent.css' rel="stylesheet" type='text/css' media='screen' />
<script src="core/js/jquery-1.7.js"></script>
<script src="core/js/jquery.desktop.js"></script>
<script src="core/js/i18n/desktop_fr.js"></script>
<script src="../libjs/jquery-ui/jquery-ui.js"></script>
<script src="core/js/jquery.notify.min.js"></script>
<script src="core/js/jquery.context_menu.js"></script>
<script src="core/libs/poshytip/src/jquery.poshytip.min.js"></script>
<script type="text/javascript" src="core/libs/colorpicker/js/colorpicker.js"></script>
<script type='text/javascript' src='core/libs/uniform/jquery.uniform.min.js'></script>
<link rel="shortcut-icon" href="../lcs/images/favicon.ico">
<!--[if lt IE 8]>
	<link href="core/finder/ui.finder.ie.css" rel="stylesheet" media="screen" type="text/css" />
<![endif]-->
<!--[if gte IE 7]>
<link rel="stylesheet" href="core/css/ie.css" />
<![endif]-->
</head>
<body>
<!-- desktop-->
<div class="abs" id="desktop"></div>
<div id="container" style="display:none" class="active">
	<div id="default">
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
	<div id="sticky" class="active">
		<a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
	<div id="withIcon" class="active">
		<a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="" /></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
	<div id="withIconNoClose" class="active">
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="" /></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
</div>
<div style="display:none"><iframe id="tmp_squirrelmail" style="display:none" src=""></iframe></div>
<div style="display:none" id="temp_forum_notify"></div>
<!--- container to hold notifications, and default templates --->
<script>
$(document).ready(function(){
			var tmpWpp = {opts:{pos_wallpaper:'center_v center_h', wallpaper:"core/images/gui/loading81.gif"}};
			//$('body').addClass('loading');
	//		JQD.init.wpp(tmpWpp);JQD.init.place_wppr(tmpWpp);
	JQD.settings();
/*
	JQD.defaults['iconcolor'] ='black';
	JQD.testfunc= function(){
		console.log('this =>', this);
		console.log('$(this) =>', $(this).jquery);
	//	console.log('e =>', e);
	}
	JQD.testfunc();
*/
});

</script>
<?php

// Cas service
	// Select PATH request with mod_auth params (LCS or ENT)
	if ( $auth_mod != "ENT" ) {
		$log2cas="log2cas_ajax.php";
		$path2auth="auth.php";
	} else {
		$log2cas="log2lcsentcas_ajax.php";
		$path2auth="auth_Cas.php";
	}
	if ( $login && ($lcs_cas == 1) && !isset($_COOKIE['tgt'])) {
		echo "<script type='text/javascript'>
        // <![CDATA[
                $.ajax({
                    type: 'POST',
                    url : '../lcs/includes/$log2cas',
                    async: true,
                    error: function() {
                        alert('Echec authentification CAS LCS');
                    }
         });
        //]]>
        </script>\n";
	}
?>
</body>
</html>

