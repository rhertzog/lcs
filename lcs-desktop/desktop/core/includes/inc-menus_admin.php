		<li>
			<a class="menu_trigger" href="#">Administration</a>
			<ul class="menu">
				<?php 			
				getmenuarray();
				for ($i=0; $i< count($liens); $i++) {
					// Affichage item menu
					if ( (strlen($liens[$i][0]) > 0) && ( ldap_get_right($liens[$i][1],$login)=="Y" ) ) echo "<li>\n<a href='#' class='submenu'><img src='../lcs/images/barre1/BP_r1_c7_f3.gif' style='height:20px;' /> ".$liens[$i][0]."</a>\n";
					if ( count($liens[$i]) > 0 ) echo "<ul>\n";
					for ($j=2; $j< count($liens[$i]); $j=$j+3) {
						if ( ldap_get_right($liens[$i][$j+2],$login)=="Y" ) {
							// On vire le target quand il existe (cas de pla)
							$tmp = explode ("\"",$liens[$i][$j+1]);
							if ( $tmp[1] == "target='_new'" ) $liens[$i][$j+1] = $tmp[0];
							echo "<li><a class='open_win' href='#icon_dock_lcs_admin' rel='../Admin/".preg_replace("/\/Admin\//","",$liens[$i][$j+1])."'><img src='../lcs/images/barre1/BP_r1_c7_f3.gif' style='height:20px;' /> ".$liens[$i][$j]."</a></li>\n";
						}
					}	
					if ( count($liens[$i]) > 0 ) echo "</ul>\n";
					echo "<li>\n";    				
				}	
				?>
			</ul>
		</li>				
