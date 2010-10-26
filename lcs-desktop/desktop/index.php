<?
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();

if (!@mysql_select_db($DBAUTH, $authlink)) 
    die ("S&#233;lection de base de donn&#233;es impossible.");
$query = "SELECT * from applis";
$result = @mysql_query($query, $authlink);
if ($result)
    while ($r=@mysql_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("Param&#232;tres absents de la base de donn&#233;es.");
@mysql_free_result($result);

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
<script src="../libjs/jquery-ui/jquery-ui.js"></script>
<script src="core/libs/farbtastic/farbtastic.js"></script>
<script src="core/finder/jquery.scrollTo-1.4.0-min.js"></script>
<script src="core/js/jquery.localscroll-1.2.7-min.js"></script>
<script src="core/js/jquery.notify.min.js"></script>
<script src="core/js/jquery.metadata.js"></script>
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
					<img src="../lcs/images/deconnect.png" alt=""  style="width:16px;"/>
					Lcs-Bureau : Formulaire de connexion
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
					<iframe src="" name="iframe_lcs_auth" style="width:100%;height:98%;" height="98%" id="iframe_lcs_auth"></iframe>
				</div>
			</div>
			<div class="abs window_bottom">
				Lcs-Bureau : Formulaire de connexion
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
			<iframe src="" id="iframe_lcs_legal"></iframe>
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
			<!--<div class="bar_top"></div>-->
			<div class="abs window_content">
			<iframe src="" style="width:100%;height:98%;" id="iframe_lcs_webperso"></iframe>
			</div>
			<div class="abs window_bottom">
				Mon espace web
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

	<div id="window_lcs_change_pass" class="abs window" style="top:0;left:0;">
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
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" /></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
	<div id="withIconNoClose" class="active">
		<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" /></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
</div>
<input type="hidden" id="s_idart" value="0" />
<input type="hidden" id="url_accueil" value="<?php echo $url_accueil; ?>" />
<input type="hidden" id="list_applis" value="<?php echo $list_applis; ?>" class="<?php echo $list_applis; ?>"/>
<div style="display:none"><iframe id="temp_squirrelmail" style="display:none" src=""></iframe></div>
<div style="display:none" id="temp_forum_notify"></div>
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
<script>
$(document).ready(function(){
	JQD.init_icons();
	JQD.init_desktop();
	// on regarde si la page d'accueil est une appli lcs
	var u=$('#url_accueil').val().replace('Plugins','').replace(new RegExp(/\//gi),"");
	var url='';
	if ($('.listapplis').metadata() ) {
		//alert($('.listapplis').metadata());
		$.each($('.listapplis').metadata(), function(index, value){
			if(u.toLowerCase()==value){
				//alert(value);
				url="../"+$('#url_accueil').val();
			}
		});
	}
	<?php 
	// .:LCS:. si user est connecte
	if ( $idpers!=0 ) { 
	?>
		<?php
		// cas de page externe en page accueil
		if ( preg_match('/http/', $url_accueil) ) {
		?>
			//alert($('#url_accueil').val());
			setTimeout(function(){
				JQD.init_link_open_win('<a title="webperso" '
					+'rev="webperso" '
					+'rel="'+$('#url_accueil').val()+'" '
					+'href="#icon_dock_lcs_webperso" '
					+'class="open_win ext_link">WP</a>');
			},100);
		<?php
		}
		?>
		// User must change pass
		<?php 
		if (pwdMustChange($login)) { 
		?>
			setTimeout(function(){
				//alert('Pass no changed');
				JQD.init_link_open_win('<a rel="change_pass" '
				+'rev="change_pass" '
				+'title="auth" href="../Annu/must_change_default_pwd.php" '
				+'class="open_win ext_link">Changer de mot-de-passe</a>'
				);	
				// le user doit recharger la page apres modif pass, on lui notifie
				$('#window_lcs_change_pass').find('iframe').load(function(){
					$(this).contents().find('td :submit').click(function(){
						//alert('toto');
						JQD.create_notify("withIconNoClose", { 
							title:'Attention!', 
							text:'N&rsquo;oubliez pas d&rsquo;actualiser votre bureau apr&egrave;s avoir modifi&eacute; votre mot de passe. <br />'
								+'<span style="text-decoration:underline;">Cliquez-moi pour actualiser votre bureau</span>', 
							icon:'core/images/icons/alert.png' 
						}, 
						{
							expires:false,
							click: function(e,instance){
								window.location='./'; 
							}
						});
					});
				});
			},1500);
				
		<?php 
		} // End of User must change pass
		else{

			// if spip is enable init notification of forum
			if ( $spip == 1 ) { 
			?>
				setTimeout(function(){
					//.:LCS:. Init forum notification
					var idart=0;
					JQD.notify_forum();
				},1500);
			<?php 
			} // End  spip enable

			// if squirrelmail is enable, notify new messages
			if ( $squirrelmail == 1 ) { 
			?>
				setTimeout(function(){
				$('#temp_squirrelmail').attr('src','../lcs/statandgo.php?use=squirrelmail');
				},1500);
				setTimeout(function(){
		        	$.get("../squirrelmail/plugins/notify/notify-desktop.php", function(data){
						if (data != '')
							JQD.create_notify("withIcon", {
								title:'Messagerie', 
								text: data + '<p><span style="text-decoration:underline;">Consulter sa messagerie</span>', 
								icon:'core/images/icons/mailicon.png' 
							},
							{ 
								expires:false,
								click: function(e,instance){
									JQD.init_link_open_win('<a title="Webmail" '
										+'rel="../lcs/statandgo.php?use=squirrelmail" '
										+'rev="squirrelmail" href="#icon_dock_lcs_squirrelmail" '
										+'class="open_win ext_link">Messagerie</a>');
									instance.close();
								}
							});
					});
				},10000);
			<?php 
			} // End  squirrelmail enable

			if (!is_file("/home/".$login."/Profile/lcs_buro_".$login.".xml" )) { 
			?>
				JQD.create_notify("withIcon", { 
					title:'Personnalisez Lcs-Bureau', 
					text:'Pour modifier votre fond d&rsquo;&eacute;cran, afficher un dock d&rsquo;ic&ocirc;nes, ... '
						+'allez dans Lcs-Bureau/Pr&eacute;f&eacute;rences '
						+'<span style="text-decoration:underline;">ou cliquez-moi...</span><br />'
						+'<small>Cliquez sur la X pour me fermer</small>', 
					icon:'core/images/icons/tip.png' 
				},
				{
				expires:false,
				click: function(e,instance){
					JQD.init_link_open_win('<a href="#icon_dock_lcs_prefs" rev="prefs" rel="prefs" class="open_win ext_link"> ce lien ...</a>');
					instance.close();
					}
				} );
			<?php 
			} 
		} 

	} 
	// .:LCS:. si user n'est pas connecte
	else { 
		?>

		<?php
		// cas de page externe en page accueil
		if ( preg_match('/http/', $url_accueil) ) {
		?>
			//alert($('#url_accueil').val());
			setTimeout(function(){
				$('#bar_bottom').hide();$('#desktop').css({bottom:0});
				//$('#window_lcs_spip').addClass('large_win');
				$('#window_lcs_webperso').addClass('window_full').css({'top':0,'left':0})
					.find('div.window_top').hide()
					.next('div.window_content').css({top:0,bottom:0})
					.next('div.window_bottom').hide();
					$('#iframe_lcs_webperso').load(function()  {
						$(this).contents().find('a.open_win').each(function(){
							$(this).click(function(){
								JQD.init_link_open_win(this);
								return false;
							});
						});
					});
				JQD.init_link_open_win('<a title="webperso" '
					+'rev="webperso" '
					+'rel="'+$('#url_accueil').val()+'" '
					+'href="#icon_dock_lcs_webperso" '
					+'class="open_win ext_link">WP</a>');
			},100);
		<?php
		}
		
		// le forum en page accueil
		else { 
		?>
			if ( url !="") {
			setTimeout(function(){
				$('#bar_bottom').hide();$('#desktop').css({bottom:0});
				//$('#window_lcs_spip').addClass('large_win');
				$('#window_lcs_temp').addClass('window_full').css({'top':0,'left':0})
					.find('div.window_top').hide()
					.next('div.window_content').css({top:0,bottom:0})
					.next('div.window_bottom').hide();
					$('#iframe_lcs_temp').load(function()  {
						$(this).contents().find('a.open_win').each(function(){
							$(this).click(function(){
								JQD.init_link_open_win(this);
								return false;
							});
						});
					});
				JQD.init_link_open_win('<a title="temp" '
					+'rev="temp" '
					+'rel="'+url+'" '
					+'href="#icon_dock_lcs_temp" '
					+'class="open_win ext_link">Forum</a>');
			},100);
			}
			else{
				setTimeout(function(){
					// .:LCS:. on lance l'affichage du form de connexion.
					JQD.init_link_open_win('<a rel="../lcs/auth" rev="auth" href="#icon_dock_lcs_auth" class="open_win ext_link">Se connecter</a>');
				},1000);
			}
		<?php 
		} 
	} 
	?>
});
</script>

<?php
// Cas service authentification
   if ( $login && ($lcs_cas == 1) && !isset($_COOKIE['tgt'])) 
	echo "<script type='text/javascript'>
        // <![CDATA[
		$.ajax({
                    type: 'POST',
                    url : '../lcs/includes/log2cas_ajax.php',
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

