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
					<a class="open_win ext_link" rel="../lcs/auth.php" rev="auth"  href="#icon_dock_lcs_auth"><img src="../lcs/images/deconnect.png" style="width:20px;" /> Se connecter</a>
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
					<a class="open_win ext_link" rel="prefs" rev="prefs" href="#icon_dock_lcs_prefs"><img src="../lcs/images/bt-V1-4.jpg" style="height:20px;" /> Pr&eacute;f&eacute;rences...</a>
				</li>
<!--
				<li>
					<a class="open_win ext_link" rev="temp" rel="temp" title="Upload Images" href="jquery_upload_crop/upload_crop.php"><img src="../lcs/images/barre1/BP_r1_c8.gif" style="width:20px;" /> testadom</a>
				</li>
-->
				<li>
					<a class="open_win ext_link" href="../lcs/logout.php"><img src="../lcs/images/connect.png" style="width:20px;" /> Se d&eacute;connecter</a>
				</li>
<?php			
	}
?>
			</ul>
		</li>
	<?php			
	 	// Un utilisateur est authentifie  et a modifie son mot de passe
	 	// on affiche les menus applis
		if ( $idpers!=0 && !pwdMustChange($login)) {
		?>
		<li>
		<a class="menu_trigger" href="#">Services</a>
			<ul class="menu">
				<?php			
				echo $html_menu_services;
				?>
			</ul>
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
		if ( acces_btn_admin($idpers, $login) == "Y") { // acces au menu d'administration
			include("core/includes/inc-menus_admin.php");
        } 
		?>	
		<li>
			<a class="menu_trigger" href="#">Aide</a>
			<ul class="menu">
				<li>
					<a href="<?php echo $url_logo ?>"><img src='../lcs/images/barre1/BP_r1_c8.gif' style='height:20px;' /> Documentation Lcs</a>
				</li>
				<li>
					<a class='open_win ext_link' href='../doc/desktop/html/' rev="legal" rel="legal"><img src='../lcs/images/barre1/BP_r1_c8.gif' style='height:20px;' /> Documentation Lcs-Bureau</a>
				</li>
				<li>
					<a class='open_win ext_link' href='../lcs/statandgo.php?use=Aide' rev="legal" rel="legal"><img src='../lcs/images/barre1/BP_r1_c8.gif' style='height:20px;' /> Documentation des plugins</a>
				</li>
				<li>
					<a class='open_win ext_link' href='#icon_dock_lcs_legal' rev="legal" rel="./core/a_propos.php"><img src='../lcs/images/barre1/BP_r1_c8.gif' style='height:20px;' /> A propos de LCS-Bureau</a>
				</li>
			</ul>
		</li>
	</ul>
