<?
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();
$query = "SELECT * from applis";
$result=@mysql_db_query("$DBAUTH",$query, $authlink);
if ($result)
    while ($r=@mysql_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("parametres absents de la base de donnees");
@mysql_free_result($result);
  
/* lcs/barre.php derniere mise a jour : 12/06/2008 */
//require "includes/headerauth.inc.php";
require "/var/www/Annu/includes/ldap.inc.php";
require "/var/www/Annu/includes/ihm.inc.php";
include("core/includes/functions.inc.php");

// menus
include("core/includes/inc-lcs-applis.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" /><!-- Pas utf-8 du a la directive apache -->
<title>...::: Bureau LCS :::...</title>
<meta name="description" content="LCS environnement num&eacute;rique de travail" />
<link href="core/css/html.css" rel="stylesheet" />
<link href="core/finder/ui.theme.css" rel="stylesheet" type="text/css" media="screen">
<link href="core/css/inettuts.css" rel="stylesheet" type="text/css" />
<link href="core/css/inettuts.js.css" rel="stylesheet" type="text/css" />
<link href="core/css/desktop.css" rel="stylesheet" />
<link href="core/libs/farbtastic/farbtastic.css" rel="stylesheet" type="text/css" />
<link href="core/finder/ui.finder.css" rel="stylesheet" media="screen,print" type="text/css">
<link href="core/css/ui.notify.css" ype="text/css" rel="stylesheet" />
<script src="../libjs/jquery/jquery.js"></script>
<script src="core/js/jquery.desktop.js"></script>
<script src="core/js/jquery.notification.js"></script>
<script src="../libjs/jquery-ui/jquery-ui.js"></script>
<script src="core/libs/farbtastic/farbtastic.js"></script>
<script src="core/finder/jquery.scrollTo-1.4.0-min.js"></script>
<script src="core/js/jquery.notify.min.js"></script>
<script src="core/finder/ui.finder.js"></script>
<link rel="shortcut-icon" href="../lcs/images/favicon.ico">
<!--[if lt IE 8]>
	<link href="core/finder/ui.finder.ie.css" rel="stylesheet" media="screen" type="text/css" />
<![endif]-->
<!--[if gte IE 7]>
<link rel="stylesheet" href="core/css/ie.css" />
<![endif]-->
</head>
<body>
// monLCS
<div class="abs" id="monLcs">
	<iframe src="" name="ifr_lcs_monlcs" style="width:100%;height:100%;"></iframe>
</div>
// iNettuts
<div class="abs" id="inettuts"></div>
<div class="abs" id="desktop">
<?php
if ( $idpers==0 ) { 
	echo '<a class="abs icon" style="left:20px;top:20px;" href="#icon_dock_lcs_auth" title="Se connecter" rel="../lcs/auth.php"><img src="../lcs/images/barre1/BP_r1_c3_f3.gif" alt="" />Se connecter</a>';
}else{
	include("/usr/share/lcs/desktop/core/action/load_user_prefs.php");
	$uXml="/home/".$login."/Profile/lcs_buro_".$login.".xml";
	if(is_file($uXml)){
		$html_icon= USERPREFS_Display_Icons($uXml,40,1,1);
	} else{
		$html_icon= $html_icon_default;
	}

	echo $html_icon;
	
?>

	<div id="trash" class='trash'><h3 class="trash_item"></h3></div>
<!--	
	<div id="annonce" class="box_trsp_black">
		<h3 style="font-wheight:bold;text-shadow:0px 1px 0px #dddddd;">Annonce</h3>
		<p style="padding:10px;margin:10px;line-height:14px;">Vous n'avez pas encore enregistr&eacute; vos pr&eacute;f&eacute;rences de bureau</p>
		<p class="center bg_white" style=""><span class="open_win bouton"><a class="open_win bouton" href="#icon_dock_lcs_prefs">Modifier mon bureau...</a></span></p>
	</div>
-->	
<?php
}
?>
	<div id="window_lcs_auth" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="core/images/icons/icon_16_lcs.png" alt="" />
					LCS - Formulaire de connexion
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_auth" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
				<div class="window_main" style="width:100%;height:100%;margin:0;">
					<a class="appli_link" href="auth.php" title="auth"></a>
					<iframe src="" name="iframe_lcs_auth" style="width:100%;height:98%;height:98%;" id="iframe_lcs_auth"></iframe>
				</div>
			</div>
			<div class="abs window_bottom">
				LCS -  Formulaire de connexion
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>
	
	<div id="window_lcs_admin" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="../lcs/images/barre1/BP_r1_c7_f3.gif" alt="" style="width:16px;" />
					LCS - Administration
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_admin" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
				<div class="window_main" style="width:100%;height:100%;margin:0;">
					<a class="appli_link" href="../Admin" title="Admin"></a>
					<iframe src="" name="ifr_lcs_admin" style="width:100%;height:98%;" id="iframe_lcs_admin"></iframe>
				</div>
			</div>
			<div class="abs window_bottom">
				LCS -Administration
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

	<div id="window_lcs_prefs" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="../lcs/images/barre1/BP_r1_c7_f3.gif"  alt="" style="width:16px;" />
					Pr&eacute;f&eacute;rences
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_prefs" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
			<?php
			include("core/includes/inc-form_prefs.php");
			?>
			</div>
			<div class="abs window_bottom">
				Pr&eacute;f&eacute;rences<?php echo  $url_redirect; ?>
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

	<div id="window_lcs_legal" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="../lcs/images/barre1/BP_r1_c7_f3.gif"  alt="" style="width:16px;" />
					LCS - A propos
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_legal" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
			<iframe src="" style="width:100%;height:98%;" id="iframe_lcs_legal"></iframe>
			</div>
			<div class="abs window_bottom">
				LCS - A propos
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

<!-- LCS window  -->
	<div id="window_lcs_path" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="../lcs/images/barre1/BP_r1_c7_f3.gif"  alt="" style="width:16px;" />
					<span class="window_title">LCS - </span>
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_path" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
			<iframe src="" style="width:100%;height:98%;" id="iframe_lcs_path"></iframe>
			</div>
			<div class="abs window_bottom">
				LCS - 
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>
<!--End of window -->
	
<!-- LCS window  -->
	<div id="window_lcs_temp" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="../lcs/images/barre1/BP_r1_c7_f3.gif"  alt="" style="width:16px;" />
					<span class="window_title">LCS - test lien ext</span>
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_temp" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
			<iframe src="" style="width:100%;height:98%;" id="iframe_lcs_temp"></iframe>
			</div>
			<div class="abs window_bottom">
				LCS -  test lien ext
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>
	<div id="window_lcs_webperso" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="../lcs/images/barre1/BP_r1_c7_f3.gif"  alt="" style="width:16px;" />
					<span class="window_title">Mon espace web</span>
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_webperso" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
			<iframe src="" style="width:100%;height:98%;" id="iframe_lcs_webperso"></iframe>
			</div>
			<div class="abs window_bottom">
				Mon espace web
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

	<div id="window_lcs_sfbrowser" class="abs window" style="height:550px;">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="../lcs/images/barre1/BP_r1_c7_f3.gif"  alt="" style="width:16px;" />
					LCS-Navigateur
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_sfbrowser" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
				<div class="window_main" style="width:100%;height:100%;margin:0;">
				<iframe src="" style="width:100%;height:98%;"></iframe>
				</div>
			</div>
			<div class="abs window_bottom">
				LCS-Navigateur
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

	<div id="window_lcs_change_pass" class="abs window  window_full" style="height:550px;top:0;left:0;">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="../lcs/images/barre1/BP_r1_c7_f3.gif"  alt="" style="width:16px;" />
					Changement de mot de passe
				</span>
			</div>
			<div class="abs window_content">
				<div class="window_main" style="width:100%;height:100%;margin:0;">
				<iframe src="" style="width:100%;height:98%;"></iframe>
				</div>
			</div>
			<div class="abs window_bottom">
			</div>
		</div>
	</div>

<?php

	if ( $idpers!=0 ) { 
		echo $html;
	}
?>
<!--End of window -->

</div>
<?php
	// bar-top, bar-bottom
	include('core/includes/inc-bars_topbottom.php');
	
	// Page d'accueil 
	if ( $url_redirect == "accueil.php" || $url_redirect == "../squidGuard/pageinterdite.html" ) $url_accueil = $url_redirect;
	// (voir pour monLcs)
	if ( $url_accueil == "accueil.php" && is_dir ("/var/www/monlcs") )  
  $url_accueil = "../spip/";
?>
<!--- container to hold notifications, and default templates --->
<div id="container" style="display:none">
	<div id="default">
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
	<div id="sticky">
		<a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
	<div id="withIcon">
		<a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" /></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
	<div id="withIconNoClose">
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" /></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
</div>
<div style="display:none"><iframe id="temp_squirrelmail" style="display:none" src=""></iframe></div>

<script>
	JQD.init_icons();
	JQD.init_desktop();
</script>
	<?php 	if ( $url_accueil=="../spip/" ) { ?>
	// .:LCS:. on lance l'affichage de la page &url_accueil.
<script>
	setTimeout(function(){
		$('#window_lcs_spip').addClass('large_win');
		JQD.init_link_open_win('<a title="spip" rel="../lcs/statandgo.php?use=spip" href="#icon_dock_lcs_spip" class="open_win ext_link">Forum</a>');
	},1500);
</script>
	<?php } if ( $idpers!=0 ) { ?>
<script>
	setTimeout(function(){
	//on apppelle squirrelmail
	<?php if (pwdMustChange($login)) { ?>
		//alert('Pass no changed');
		JQD.init_link_open_win('<a rel="change_pass" title="auth" href="../Annu/must_change_default_pwd.php" class="open_win ext_link">Cnager de mot-de-passe</a>');	
		// le user doit recharger la page après modif pass
		JQD.create_notify("withIconNoClose", { title:'Attention!', text:'N&rsquo;oubliez pas d&rsquo;actualiser votre bureau apr&egrave;s avoir modifi&eacute; votre mot de passe. <br /><span style="text-decoration:underline;">Cliquez-moi pour actualiser votre bureau</span>', icon:'core/images/icons/alert.png' }, {
			expires:false,
			click: function(e,instance){
				window.location='./'; 
			}
		});

	<?php }else { ?>
		$('#temp_squirrelmail').attr('src','../lcs/statandgo.php?use=squirrelmail');
		setTimeout(function(){
			$('#temp_squirrelmail').attr('src','');
		},10000);
	<?php }?>
	},2500);
	<?php if (!is_file("/home/".$login."/Profile/lcs_buro_".$login.".xml" )) { ?>
			JQD.create_notify("withIcon", { title:'Personnalisez Lcs-Bureau', text:'Pour modifier votre fond d&rsquo;&eacute;cran, afficher un dock d&rsquo;ic&ocirc;nes, ... allez dans Lcs-Bureau/Pr&eacute;f&eacute;rences <span style="text-decoration:underline;">ou cliquez-moi...</span><br /> <small>Cliquez sur la X pour me fermer</small>', icon:'core/images/icons/tip.png' },
			{
			expires:false,
			click: function(e,instance){
				JQD.init_link_open_win('<a href="#icon_dock_lcs_prefs" title="prefs" rel="prefs" class="open_win ext_link"> ce lien ...</a>');
				}
			} );
	<?php } ?>
			
</script>
	<?php }	if ( $idpers==0 ) { ?>
	// .:LCS:. on lance l'affichage du form de connexion.
<script>
	setTimeout(function(){
		$('#window_lcs_spip').css({'top':0,'left':0}).removeClass('large_win').addClass('window_full');
		JQD.init_link_open_win('<a rel="../lcs/auth" title="auth" href="#icon_dock_lcs_auth" class="open_win ext_link">Se connecter</a>');
		$('#window_lcs_auth').addClass('small_win small_height').animate({top : 0,left :0,width:550,height:340},1).find('.window_main').css('background-color','transparent');
		setTimeout(function(){
			$('#iframe_lcs_auth').contents().find('head').append('<style>jqd.h3{color:red;}</style>');
			$('#iframe_lcs_auth').contents().find('body').removeClass().addClass('jqd').css({'font-size':'.75em','background-color':'transparent','background-image':'none'}).find('.pdp').remove();
		},500);
	},3000);

</script>
<?php } ?>

<?php
// Cas service authentification
   if ( $login && ($lcs_cas == 1) && !isset($_COOKIE['tgt'])) 
	echo "<script type='text/javascript'>
        // <![CDATA[
		$.ajax({
                    type: 'POST',
                    url : 'includes/log2cas_ajax.php',
                    async: true,
                    error: function() {
                        alert('Echec authentification CAS');
                    }
         });
        //]]>
        </script>\n";
?>
</body>
</html>
