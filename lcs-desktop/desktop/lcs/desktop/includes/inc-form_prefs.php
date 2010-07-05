<div class="jqd_formulaires">
	<div class="jqd_form_side">
		<div style="height:150px;border-bottom:1px solid #aaa;background:#efefef;">
			<h5>Fond d'&eacute;cran actuel</h5>
			<img src="desktop/images/misc/colorful.jpg" alt="" id="vign_wlpper" style="width:150px" />
		</div>
		<div style="height:80px;border-bottom:1px solid #aaa;background:#efefef;">
			<h5>Taille des ic&ocirc;nes</h5>
			<div><img src="images/barre1/BP_r1_c7_f3.gif" id="vign_icon" style="width:24px;height:24px;" alt="" width="150" height="150" /></div>
			</div>
	</div>
	<div class="">
		<div id="prefs_user_form" class="tabs_content">
			<h1>Param&eacute;tres du bureau</h1>
			<form action="" class="jqd_form">
				<fieldset>
				<legend>Fond d'&eacute;cran</legend>
					<ul>
						<li>
							<label for="select_walppr">Choisir une image</label>
							<select name="select_walppr" id="select_walppr">
								<option value="Abstract_Frozen_Blue">Abstract Frozen Blue</option>
								<option value="Colorful" selected="selected">ColorFull</option>
								<option value="Red_Sea_fishes">Red_Sea_fishes</option>
								<option value="Snake">Snake</option>
								<option value="wallpaper">Stries</option>
								<option value="Summer_Euphoria">Summer Euphoria</option>
								<option value="Superconductivity_Phenomenon">Superconductivity Phenomenon</option>
								<option value="Virtual_Octopus">Virtual Octopus</option>
								<option value="X4_Internals_dual_spark">X4 Internals dual spark</option>
<!--								<option value="favre_leuba">Favre-Leuba</option>
-->							</select>
							<span class="float_right bouton"><a href="#" id="ch_wlppr">Appliquer</a></span>
						</li>
					</ul>
				</fieldset>
				
				<fieldset>
				<legend>Icones</legend>
					<ul>
						<li>
							<label for="icons_larger">Taille des icones</label>
							<select name="icons_choice" id="icons_larger">
								<option value="24">Petites</option>
								<option value="36" selected="selected">Moyennes</option>
								<option value="48">Grande</option>
							</select>
							<span class="bouton float_right"><a href="#" id="ch_icons_larger">Appliquer</a></span>
						</li>
						<li>
							<label for="icons_field_height">Hauteur du champ d'affichage des ic&ocirc;nes sur le bureau</label>
							<select name="icons_field_height" id="icons_field_height">
								<option value="1">10</option>
								<option value="2">20</option>
								<option value="3">30</option>
								<option value="4">40</option>
								<option value="5">50</option>
								<option value="6" selected="selected">60</option>
								<option value="7">70</option>
								<option value="8">80</option>
								<option value="9">90</option>
								<option value="10">100</option>
							</select>%
							<span class="bouton float_right"><a href="#" id="ch_icons_field_height">Appliquer</a></span>
							<br style="clear:both" />
						</li>
					</ul>
				</fieldset>

				<input type="hidden" name="login" id="login" value="<?php echo $login ?>" />
				<input type="hidden" name="idpers" id="idpers" value="<?php echo $idpers ?>" />
				<input type="hidden" name="ticket_prefs" id="ticket_prefs" value="none" />
				<p style="text-align:center;margin-top:20px;">
					<span class="bouton"><a href="#" id="valid_prefs">Valider</a></span>
				</p>
			</form>
		</div>
		<div id="prefs_gen_form">
		</div>
	</div>
</div>
