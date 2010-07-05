<ul id="iLcsMenu" style="display:none;">
	<li>
		<a href="#" id="" class="menu_trigger iLcslink"><i>i</i>Lcs</a>
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
					<a class="open_win" href="#icon_dock_lcs_auth">Se connecter</a>
				</li>
<?php			
	} else {
 	// Un utilisateur est authentifie  
        if ( is_admin("Lcs_is_admin",$login) == "Y" ) {// acces au menu d'administration	
?>
				<li>
					<a class="open_win ext_link" rel="../helpdesk/" title="helpdesk" href="#icon_dock_lcs_helpdesk"><img src="images/barre1/helpdesk-on.gif" style="height:20px;" /> Helpdesk</a>
				</li>
				<!--
				<li>
					<a class="open_win" href="#icon_dock_lcs_texteditor">Envoyer un message</a>
				</li>
				-->
<?php			
		}
?>
<!--
				<li>
					<a class="open_win ext_link" rel="add_links" title="add_links" href="#icon_dock_lcs_add_links"><img src="images/bt-V1-2.jpg" style="height:20px;" /> Mes sites ressources</a>
				</li>
				<li>
					<a class="open_win ext_link" rel="Tiny"  title="" href="../lcs/desktop/libs/tiny.php"><img src="images/bt-V1-3.jpg" style="height:20px;" /> Ecrire un texte</a>
				</li>					
-->
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
		<?php	
			if ( $squirrelmail==1 || $spip==1 ){ // Webmail ou spip on cree le menu Services
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
		}
/*
		if ( is_admin("Lcs_is_admin",$login) == "Y" ) { // acces au menu d'administration
			$liens=array(0);
			exec("ls /var/www/lcs/includes/menu.d/*.inc",$files,$return);
			for ($i=0; $i< count($files); $i++)
    			include ($files[$i]);	
*/	
                if ( acces_btn_admin($idpers, $login) == "Y") { // acces au menu d'administration
                    getmenuarray();
 
		?>
		<li>
			<a class="menu_trigger" href="#">Administration</a>
			<ul class="menu">
				<?php 			
				for ($i=0; $i< count($liens); $i++) {
					// Affichage item menu
					if ( (strlen($liens[$i][0]) > 0) && ( ldap_get_right($liens[$i][1],$login)=="Y" ) ) echo "<li>\n<a href='#' class='submenu'><img src='images/barre1/BP_r1_c7_f3.gif' style='height:20px;' /> ".$liens[$i][0]."</a>\n";
					if ( count($liens[$i]) > 0 ) echo "<ul>\n";
					for ($j=2; $j< count($liens[$i]); $j=$j+3) {
						if ( ldap_get_right($liens[$i][$j+2],$login)=="Y" ) {
							// On vire le target quand il existe (cas de pla)
							$tmp = explode ("\"",$liens[$i][$j+1]);
							if ( $tmp[1] = "target='_new'" ) $liens[$i][$j+1] = $tmp[0];
							echo "<li><a class='open_win' href='#icon_dock_lcs_admin' rel='../Admin/".$liens[$i][$j+1]."'><img src='images/barre1/BP_r1_c7_f3.gif' style='height:20px;' /> ".$liens[$i][$j]."</a></li>\n";
						}
					}	
					if ( count($liens[$i]) > 0 ) echo "</ul>\n";
					echo "<li>\n";    				
				}	
				?>
			</ul>
		</li>				
	<?php			
		} // Fin acces menu administration
	?>	

		<li>
			<a class="menu_trigger" href="#">Aide</a>
			<ul class="menu">
				<li>
					<a href="<?php echo $url_logo ?>"><img src='images/barre1/BP_r1_c8.gif' style='height:20px;' /> Documentation Lcs</a>
				</li>
				<li>
					<a class='open_win' href='#icon_dock_lcs_legal' title="legal" rel="./desktop/a_propos.html"><img src='images/barre1/BP_r1_c8.gif' style='height:20px;' /> A propos de LCS-Bureau</a>
				</li>
			</ul>
		</li>
	</ul>
