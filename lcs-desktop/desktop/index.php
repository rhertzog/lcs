<?
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();


require "/var/www/Annu/includes/ldap.inc.php";
require "/var/www/Annu/includes/ihm.inc.php";
include("core/includes/functions.inc.php");
// menus
include("core/includes/inc-lcs-applis.php");

# recherche des parametres etablissement
# Recuperation des parametres LCS depuis la bdd
# -----------------------------------------------------
$authlink = mysql_connect($HOSTAUTH,$USERAUTH,$PASSAUTH);
@mysql_select_db($DBAUTH) or die("Impossible de se connecter &#224; la base $DBAUTH.");
$result=mysql_query("SELECT * from params where srv_id=0");
if ($result)
while ($r=mysql_fetch_array($result))
$$r["name"]=$r["value"];
else
die ("param&#232;tres absents de la base de donn&#233;e");
mysql_free_result($result);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"><!-- iso-8859-1 du a la directive apache ?  -->
<meta http-equiv="x-ua-compatible" content="ie=emulateie8" />
<title>...::: Bureau LCS - <?php echo $organizationalunit." ".$organization." - ".$locality ." - ".$province ; ?> :::...</title><!-- recuperation des params etab ds lcs.conf -->
<meta name="description" content="LCS - Environnement Num&eacute;rique de Travail du <?php echo $organizationalunit."  ".$organization." de ".$locality." - ".$province; ?>. Le num&eacute;rique au service de l'&eacute;ducation." />
<meta name="keywords" content="Environnement Num&eacute;rique de Travail, LCS, linux, communication, serveur, e-learning" />
<meta name="author" content="LcsDevTeam" />
<link href="core/css/html.css" rel="stylesheet" />
<link href="core/finder/ui.theme.css" rel="stylesheet" type="text/css" media="screen">
<link href="core/css/inettuts.css" rel="stylesheet" type="text/css" />
<link href="core/css/inettuts.js.css" rel="stylesheet" type="text/css" />
<link href="core/css/desktop.css" rel="stylesheet" />
<link href="core/css/ui.notify.css" ype="text/css" rel="stylesheet" />
<script src="../libjs/jquery/jquery.js"></script>
<script src="core/js/jquery.desktop.js"></script>
<script src="../libjs/jquery-ui/jquery-ui.js"></script>
<script src="core/finder/jquery.scrollTo-1.4.0-min.js"></script>
<script src="core/js/jquery.localscroll-1.2.7-min.js"></script>
<script src="core/js/jquery.notify.min.js"></script>
<script src="core/js/jquery.metadata.js"></script>
<link rel="shortcut-icon" href="../lcs/images/favicon.ico">
<!--[if lt IE 8]>
	<link href="core/finder/ui.finder.ie.css" rel="stylesheet" media="screen" type="text/css" />
<![endif]-->
<!--[if gte IE 7]>
<link rel="stylesheet" href="core/css/ie.css" />
<![endif]-->
</head>
<body>
<!--monLCS-->
<?php
if (is_dir("/var/www/monlcs")) {
?>
<div class="abs" id="monLcs" style="display:none">
	<iframe src="" name="ifr_lcs_monlcs" style="width:100%;height:100%;background:red:"></iframe>
</div>
<?php
} // fin monlcs

// iNettuts
if (is_dir("inettuts")) echo "<div class=\"abs\" id=\"inettuts\" style=\"display:none\"></div>";
?>

<!-- desktop-->
<div class="abs" id="desktop">
<?php
if ( $idpers==0 ) { ?>
	<input type="hidden" id="tmp_wallpaper" value="core/images/misc/RayOfLight_lcs.jpg"/>
	<input type="hidden" id="tmp_poswp" value="wallpaper"/>
	<input type="hidden" id="tmp_iconsize" value="36"/>
	<input type="hidden" id="tmp_iconsfield" value="50"/>
	<input type="hidden" id="tmp_bgcolor" value="#414970"/>
	<input type="hidden" id="tmp_quicklaunch" value="0"/>
	<input type="hidden" id="s_idart" value="0" />
	<input type="hidden" id="tmp_winsize" value="content" />
<a class="abs icon" style="left:20px;top:20px;" href="#icon_dock_lcs_auth" title="Se connecter" rel="../lcs/auth.php"><img src="core/images/icons/icon_32_start.png" alt="" />Se connecter</a>
<?php
}else{
	include("/usr/share/lcs/desktop/core/action/load_user_prefs.php");
	$uXml="/home/".$login."/Profile/PREFS_".$login.".xml";
	if(is_file($uXml)){
		$html_icon= USERPREFS_Display_Icons($uXml,40,1,1);
	} else{
		?>
	<input type="hidden" id="tmp_wallpaper" value="core/images/misc/RayOfLight_lcs.jpg"/>
	<input type="hidden" id="tmp_poswp" value="wallpaper"/>
	<input type="hidden" id="tmp_iconsize" value="36"/>
	<input type="hidden" id="tmp_iconsfield" value="50"/>
	<input type="hidden" id="tmp_bgcolor" value="#414970"/>
	<input type="hidden" id="tmp_quicklaunch" value="0"/>
	<input type="hidden" id="s_idart" value="0" />
	<input type="hidden" id="tmp_winsize" value="content" />
	<?php
		$html_icon= $html_icon_default;
	}
	echo $html_icon;
?>
	<div id="trash" class='trash'><h3 class="trash_item"></h3></div>
<?php
}
?>
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
<input type="hidden" id="login" value="<?php echo $login; ?>"/>
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
	// on regarde si la page d'accueil est une appli lcs
	var u=$('#url_accueil').val().replace('Plugins','').replace(new RegExp(/\//gi),"");
	var url='';
	if ($('.listapplis').metadata() ) {
		$.each($('.listapplis').metadata(), function(index, value){
			if(u.toLowerCase()==value){
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
					+'class="open_win ext_link">'+$('#url_accueil').val()+'</a>');
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
				+'title="Changer de mot-de-passe" '
				+'href="../Annu/must_change_default_pwd.php" '
				+'class="open_win ext_link">Changer de mot-de-passe</a>'
				);	
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

			if (!is_file("/home/".$login."/Profile/PREFS_".$login.".xml" )) { 
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
					JQD.init_link_open_win('<a href="#icon_dock_lcs_prefs" rel="core/user_form_prefs.php" rev="Parametres" class="open_win ext_link"> ce lien ...</a>');
					instance.close();
					}
				} );
				// on enregistre les prefs a la premiere connexion
				JQD.save_prefs_dev('PREFS', -1, 'lkhlm');
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
				var pageAccueil = $('<iframe/>')
					.addClass('abs wallpaper')
					.css({'top':'23px'})
					.attr('src', '<?php echo $url_accueil; ?>')
					.insertAfter('#desktop');
				$('#bar_bottom').hide();
			},100);
		<?php
		}
		
		// le forum en page accueil
		else { 
		?>
			if ( url !="") {
			setTimeout(function(){
				var spipAccueil = $('<iframe/>')
					.addClass('abs wallpaper')
					.css({'top':'23px'})
					.attr('src',url)
					.insertAfter('#desktop');
				$('#bar_bottom').hide();
			},100);
			}
			else{
				setTimeout(function(){
					// .:LCS:. on lance l'affichage du form de connexion.
					JQD.init_link_open_win('<a rel="../lcs/auth.php" rev="auth" href="#icon_dock_lcs_auth" class="open_win ext_link">Se connecter</a>');
				},1000);
			}
		<?php 
		} 
	} 
	?>
	JQD.init_icons();
	JQD.init_desktop();
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

