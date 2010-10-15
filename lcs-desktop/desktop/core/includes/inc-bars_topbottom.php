<div class="abs" id="bar_top">
<img src="core/images/icons/icon_32_lcs.png" style="float:left;width:24px;"/>
<ul class="bar_top_right float_right">
	<?php
		if ( $idpers!=0 ) { 
	?>
	<li>
		<a href="../lcs/logout.php" title="Se d&eacute;connecter" style="cursor:pointer;color:#999;font-size:.8em;" rel="auth">
			<img alt="En ligne" src="../lcs/images/connect.png" alt="" style="cursor:pointer;" />
		</a>
	</li>
	<li>
	<a rev="agendas" rel="../lcs/statandgo.php?use=Agendas" href="#icon_dock_lcs_agendas" class="ext_link" id="clock"></a>
	</li>
	<li class="">
		<a class="reload" id="reload" href="" title="Recharger le bureau"></a>
	</li>
	<li id="user_info_bar_btn">
			<?php 
			//
			include("core/includes/inc-user_infos.php");			
			?>			
		<span id="jqd_login"><?php echo "<img src=\"core/images/annu/".strtolower($group_principal)."_" .strtolower($user["sexe"])."_trsp.png\" height=\"16\" style=\"height:16px;vertical-align:middle;margin-top:-3px;\" /> ".$user["fullname"]; ?></span>
		<ul class="toClose" id="user_infos">
			<li class="box_trsp_black list_infos_user" style="display:block;">
			<span class="close float_right"></span>
			<?php 
			//
		   	echo $text_infos;
		   	$url_s="../squirrelmail/src/compose.php?send_to=admin@".$_SERVER['HTTP_HOST']."&subject=LCS-BUREAU : demande d'assistance";
		   	$url_m="../Plugins/Maintenance/demande_support.php";
		   	$MaintInfo ==1  ? $url_maint=array($url_m,'maintinfo') : $url_maint=array($url_s,'squirrelmail');
			?>			
			</li>
		</ul>
	</li>
	<li>
<!--		<a class="open_win float_right msg ext_link icon_16" id="aForumRss" href="../spip/?page=backend" rel="forumrss" title="Last News"></a>-->
	<a title="Demande d'assistance informatique" rel="<?php echo  $url_maint[1]?>" href="<?php echo $url_maint[0];?>" class="open_win float_right msg ext_link icon_16" style="background-position:-176px -112px;"></a>
	</li>
<!--	<li>
		<a class="open_win float_right msg ext_link icon_16" id="aForumRss" href="../spip/?page=backend" rel="forumrss" title="Last News"></a>
	</li> -->
	<li>
		<a class="open_win float_right msg ext_link icon_16" id="compose_msg" href="../squirrelmail/src/compose.php?mailbox=INBOX&startMessage=1" rel="squirrelmail" title="Envoyer un message"></a>
	</li>
	<li>
		<a class="open_win float_right ext_link search icon_16" id="found" href="../Annu/search.php" title="Trouver un utilisateur, une classe, un groupe..." rel="annu"></a>
	</li>
	<li>
	<!--	<span class="float_right" id="mylinks_bar_btn">Listes&nbsp;&nbsp;|</span> -->
		<span class="float_right" id="otBuro_1" style="position:relative;display:block;">
			<span class="float_left checked"></span>
			<span id="otBuro_2">1</span>
			<ul style="position:absolute;" class="menu">
				<li><a href="#desktop" class="space">1&nbsp;&nbsp;Lcs Bureau</a></li>
				<li class="nospace"><a href="#inettuts">2&nbsp;&nbsp;<strong><i>i</i></strong>Lcs</a></li>
				<li class="nospace"><a href="#monLcs">3&nbsp;&nbsp;MonLcs</a></li>
			</ul>
		</span>
	</li>
	
	

<?php
	}else{
?>
	<li>
		<a class="open_win ext_link" href="#icon_dock_lcs_auth" rev="auth" style="cursor:pointer;color:#999;font-size:.8em;" rel="../lcs/auth.php">
			&nbsp;&nbsp;&nbsp;&nbsp;Se connecter&nbsp;&nbsp;
			<img alt="Acceder au formulaire de connexion" src="../lcs/images/deconnect.png" style="cursor:pointer;vertical-align:middle;" />&nbsp;&nbsp;&nbsp;&nbsp;
		</a>
	</li>
<?php
	}
?>
</ul>


<?php
	include('core/includes/inc-menus.php');
	if ( $idpers!=0 ) { 
	}
?>
</div>
<div class="abs" id="bar_bottom">
	<a class="float_left" href="#" id="show_desktop" title="Show Desktop">
		<img src="core/images/icons/icon_22_desktop.png" />
	</a>
	<ul id="dock">
		<li id="icon_dock_lcs_auth">
			<a href="#window_lcs_auth">
				<img src="core/images/icons/icon_22_lcs.png" />
				Authentification
			</a>
		</li>
		<li id="icon_dock_lcs_admin">
			<a href="#window_lcs_admin">
				<img src="../lcs/images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				Administration
			</a>
		</li>
		<li id="icon_dock_lcs_helpdesk">
			<a href="#window_lcs_helpdesk">
				<img src="../lcs/images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				Helpdesk
			</a>
		</li>
		<li id="icon_dock_lcs_prefs">
			<a href="#window_lcs_prefs">
				<img src="../lcs/images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				Pr&eacute;f&eacute;rences
			</a>
		</li>
		<li id="icon_dock_lcs_path">
			<a href="#window_lcs_path">
				<img src="../lcs/images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				Parcours
			</a>
		</li>
		<li id="icon_dock_lcs_legal">
			<a href="#window_lcs_legal">
				<img src="../lcs/images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				A propos
			</a>
		</li>
		<li id="icon_dock_lcs_temp">
			<a href="#window_lcs_temp">
				<img src="../lcs/images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				test lien ext
			</a>
		</li>
		<li id="icon_dock_lcs_webperso">
			<a href="#window_lcs_webperso">
				<img src="../lcs/images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				Mon espace web
			</a>
		</li>
<?php
	echo $html_status_bar;
?>
	</ul>
<!--	<a class="float_right" id="test_dialog" href="#" title="test" style="font-style:italic;text-shadow:2px 2px 2px #aaaaaa;">dialog?</a> -->
	<a class="float_right" href="#" title="LcsDevTeam" style="font-style:italic;text-shadow:2px 2px 2px #aaaaaa;">Lcs-Team
	<!--	<img src="core/images/misc/firehost.png" /> -->
	</a>
<div id="bar_bttm_icon" style="width:300px;float:right;"></div>
</div>
	
