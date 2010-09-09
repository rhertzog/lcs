<ul id="iLcsMenu" style="display:none;">
	<li>
		<a href="#" class="menu_trigger iLcslink"><i>i</i>Lcs</a>
		<ul class="menu">
			<li><a class="ext_link" href="#">Item1</a></li>
		</ul>
	</li>
	<li>
	<a href="#" id="showWidgetsContent" class="menu_trigger iLcslink" title="">Tout cacher</a>
	</li>
</ul>
	<ul>
		<li>
			<a class="menu_trigger" href="#">LCS Bureau</a>
			<ul class="menu">
<?php			
	if ( $idpers==0 ) {
 	// Un utilisateur n'est pas authentifie  
?>				<li>
					<a class="open_win ext_link" rel="auth" title="auth" href="#icon_dock_lcs_auth"><img src="../lcs/images/deconnect.png" style="width:20px;" /> Se connecter</a>
				</li>
<?php			
	} else {
 	// Un utilisateur est authentifie  
        if ( is_admin("Lcs_is_admin",$login) == "Y" ) {// acces aux preferences
?>
<?php			
		}
?>
				<li>
					<a class="open_win ext_link" rel="prefs" title="prefs" href="#icon_dock_lcs_prefs"><img src="../lcs/images/bt-V1-4.jpg" style="height:20px;" /> Pr&eacute;f&eacute;rences...</a>
				</li>
				<li>
					<a class="open_win ext_link" href="../lcs/logout.php"><img src="../lcs/images/connect.png" style="width:20px;" /> Se d&eacute;connecter</a>
				</li>
<?php			
	}
?>
			</ul>
		</li>
	<?php			
		if ( $idpers!=0 ) {
	 	// Un utilisateur  authentifie  
		?>
		<li>
		<a class="menu_trigger" href="#">Services</a>
			<ul class="menu">
			<?php			
			echo $html_menu_services;
			?>
<!--				<li>
					<a class="open_win ext_link" href="#icon_dock_lcs_accueil">MonLCS</a>
				</li>
-->			</ul>
		</li>
		<li>
			<a class="menu_trigger" href="#">Applications</a>
			<ul class="menu">
			<?php			
			echo $html_menu;
			?>
			</ul>
		</li>
		<?php			
		}
//		if ( is_admin("Lcs_is_admin",$login) == "Y" )  // acces au menu d'administration - decommenter si ancienne version
		if ( acces_btn_admin($idpers, $login) == "Y") { // acces au menu d'administration - commenter si ancienne version
			getmenuarray(); // commenter si ancienne version
			include("core/includes/inc-menus_admin.php");
        } // commenter si ancienne version
		?>	
		<li>
			<a class="menu_trigger" href="#">Aide</a>
			<ul class="menu">
				<li>
					<a href="<?php echo $url_logo ?>"><img src='../lcs/images/barre1/BP_r1_c8.gif' style='height:20px;' /> Documentation Lcs</a>
				</li>
				<li>
					<a class='open_win ext_link' href='http://lcs.pmcurie.lyc50.ac-caen.fr/lcs/statandgo.php?use=Aide' title="legal" rel="legal"><img src='../lcs/images/barre1/BP_r1_c8.gif' style='height:20px;' /> Documentation des plugins</a>
				</li>
				<li>
					<a class='open_win ext_link' href='#icon_dock_lcs_legal' title="legal" rel="./core/a_propos.php"><img src='../lcs/images/barre1/BP_r1_c8.gif' style='height:20px;' /> A propos de LCS-Bureau</a>
				</li>
			</ul>
		</li>
	</ul>
