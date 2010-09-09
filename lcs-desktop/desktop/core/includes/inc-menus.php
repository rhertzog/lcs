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
					<a class="open_win ext_link" rel="auth" title="auth" href="#icon_dock_lcs_auth">Se connecter</a>
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
					<a class="open_win ext_link" rel="prefs" title="prefs" href="#icon_dock_lcs_prefs"><img src="images/bt-V1-4.jpg" style="height:20px;" /> Pr&eacute;f&eacute;rences...</a>
				</li>
				<li>
					<a class="open_win ext_link" href="logout.php"><img src="images/connect.png" style="width:20px;" /> Se deconnecter</a>
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
//		if ( is_admin("Lcs_is_admin",$login) == "Y" )  // acces au menu d'administration // ancienne version
		if ( acces_btn_admin($idpers, $login) == "Y") { // acces au menu d'administration
			getmenuarray();
			include("core/includes/inc-menus_admin.php");
        }
		?>	
		<li>
			<a class="menu_trigger" href="#">Aide</a>
			<ul class="menu">
				<li>
					<a href="<?php echo $url_logo ?>"><img src='images/barre1/BP_r1_c8.gif' style='height:20px;' /> Documentation Lcs</a>
				</li>
				<li>
					<a class='open_win submenu ext_link' href='#icon_dock_lcs_legal' title="legal" rel="./core/a_propos.php"><img src='images/barre1/BP_r1_c8.gif' style='height:20px;' /> Documentation des plugins</a>
					<ul>
						<li>
							<a class='open_win ext_link' href='#icon_dock_lcs_legal' title="legal" rel="./core/a_propos.php"><img src='images/barre1/BP_r1_c8.gif' style='height:20px;' /> Annuaire </a>
						</li>
						<li>
							<a class='open_win ext_link' href='#icon_dock_lcs_legal' title="legal" rel="./core/a_propos.php"><img src='images/barre1/BP_r1_c8.gif' style='height:20px;' /> Cahier de texte </a>
						</li>
						<li>
							<a class='open_win ext_link' href='#icon_dock_lcs_legal' title="legal" rel="./core/a_propos.php"><img src='images/barre1/BP_r1_c8.gif' style='height:20px;' /> Lcs-Forum</a>
						</li>
					</ul>
					
				</li>
				<li>
					<a class='open_win ext_link' href='#icon_dock_lcs_legal' title="legal" rel="./core/a_propos.php"><img src='images/barre1/BP_r1_c8.gif' style='height:20px;' /> A propos de LCS-Bureau</a>
				</li>
			</ul>
		</li>
	</ul>
