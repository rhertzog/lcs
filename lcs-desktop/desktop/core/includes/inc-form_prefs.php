<div class="bar_top">
	<span style="padding-left:10px;">Param&eacute;tres du bureau</span>
	<span class="float_right">Param&egrave;tres par d&eacute;faut</span>
</div>
<div class="jqd_formulaires">
	<form action="">
	<fieldset>
		<legend>Fond d'&eacute;cran</legend>
<!--	<div style="height:150px;border-bottom:1px solid #aaa;background:#efefef;">-->
		<ul><li style="border-bottom:1px solid #aaa;">
		<label>Image</label>
		<div class="float_left infos_img_col">
			<div id="ajaxTest">
			</div>
		</div>
		<div class="float_right list_img_col">
			<span class="bouton"><a href="#" id="ch_wlppr" style="display:none">Appliquer</a></span>
			<span class="bouton"><a href="#" id="btn-openWpp" >Changer d'image...</a></span>
			<div id="listImgs" class="list_img">
				<h3 style="">Choisir une image ou 
					<span class="bouton"><a href="#" id="btn_openFinder">Importer</a></span>
					<span class="float_left close"></span>
				</h3>
				<?php
					$imgList=scandir("core/images/misc/");
						createThumbs("core/images/misc/" , "core/images/misc/thumbs/",100);
					foreach($imgList as $img){
					//	imagecopyresized ( resource   dst_image  , resource   src_image  , int   dst_x  , int   dst_y  , int   src_x  , int   src_y  , int   dst_w  , int   dst_h  , int   src_w  , int   src_h  ) 
					//	imagecopyresized ( "core/images/misc/".$img , "core/images/misc/thumbs/".$img, 0, 0, 0, 0, 60 , 100 , 50 ,50  ) ;
						$n="<img src=core/images/misc/thumbs/".$img." width=\"48\" style=\"witdh:48px;margin:5px;\"/>";
						if($img{0}!="." && $img!="thumbs")
							echo $n;
					}
				?>
			</div>
			<input type="hidden" name="select_walppr" id="select_walppr" value="" />
			<div id="dirWallpaper" class="main_finder"></div>
		</div>
		<br style="clear:both;" /></li></ul>
<!--		</div>-->
		<div>
			<ul style="" class="clear_both ul2cols">
				<li style="" class="">
					<label for="pos_walppr">Position/Echelle</label>
					<select id="pos_walppr">
						<option value="wallpaper">Plein &eacute;cran</option>
						<optgroup label="Position">
							<option value="top_left">En haut &agrave; gauche</option>
							<option value="top_right">En haut &agrave; droite</option>
							<option value="bottom_left">En bas &agrave; gauche</option>
							<option value="bottom_right">En bas &agrave; droite</option>
						</optgroup>
						<optgroup label="Echelle">
							<option value="fit_width center_v">Ajuster en largeur / Centrer verticalement</option>
							<option value="fit_width_top">Ajuster en largeur / Placer en haut</option>
							<option value="fit_width_bottom">Ajuster en largeur / Placer en bas</option>
							<option value="fit_height center_h">Ajuster en hauteur / Centrer horizontalement</option>
							<option value="fit_height_left">Ajuster en hauteur / Placer &agrave; gauche</option>
							<option value="fit_height_right">Ajuster en hauteur / Placer &agrave; droite</option>
						</optgroup>
					</select>
					<span class="float_right bouton"><a href="#" id="ch_pos_wlppr">Appliquer</a></span>
				</li>
				<li>
					<label for="wp_bgcolor">Couleur d'arri&egrave;re plan</label>
					<input type="text" id="wp_bgcolor" name="wp_bgcolor" class="colorwell" value="#123456" style="width:6em;" />
					<span class="float_right bouton"><a href="#" id="ch_bgcolor">Appliquer</a></span>
					<div id="ctn_picker" style="background:#fff;border:1px solid #aaa;width:230px;">
						<span id="close_picker" class="float_right" style="border:1px solid #aaa;line-height:12px;margin: 2px 3px 0 0;cursor:pointer;">&nbsp;X&nbsp;</span>
						<div id="picker"></div>
					</div>
							
				</li>
			</ul>
		</div>
	
	</fieldset>
	<fieldset>
	<legend>Ic&ocirc;nes</legend>
		<ul style="" class="clear_both ul2cols">
			<li>
				<label>Taille des icones</label>
				<!--<img src="images/barre1/BP_r1_c7_f3.gif" id="vign_icon" style="width:24px;height:24px;" alt="" />-->					<span class="span_icon_prefs icon"><img src="images/barre1/BP_r1_c7_f3.gif" style="width:24px" /></span>
				<span class="span_icon_prefs icon"><img src="images/barre1/BP_r1_c7_f3.gif" style="width:36px" /></span>
				<span class="span_icon_prefs icon"><img src="images/barre1/BP_r1_c7_f3.gif" style="width:48px" /></span>
				<span class="bouton float_right"><a href="#" id="ch_icons_larger">Appliquer</a></span>
				<input type="hidden" value="36" id="icons_larger" name="icons_larger" />
			</li>
			<li>
				<label for="icons_field_height">Hauteur du champ d'affichage des ic&ocirc;nes sur le bureau</label>
				<input type="text" id="icons_field_height" style="border:0; color:#f6931f; font-weight:bold;width:30px;text-align:right;" />&nbsp;<strong style="color:#f6931f">%</strong>
				<span class="bouton float_right"><a href="#" id="ch_icons_field_height">Appliquer</a></span>
				<div id="slider-vertical" style="width:100px;"></div>
			</li>
			<li>
				<label for="aff_quicklaunch"><span class="red">Nouveau </span> Afficher un Dock (fa&ccedil;on MacOs) </label>
				<input type="checkbox" id="aff_quicklaunch" checked="" name="aff_quicklaunch" />
				<span class="mess_info float_right">N&eacute;cessite le rechargement de la page apr&egrave;s enregistrement</span>
				<br class="clear_both" />
			</li>
		</ul>
	</fieldset>
				<input type="hidden" name="login" id="login" value="<?php echo $login ?>" />
				<input type="hidden" name="idpers" id="idpers" value="<?php echo $idpers ?>" />
				<input type="hidden" name="ticket_prefs" id="ticket_prefs" value="none" />
				<p style="text-align:center;margin-top:20px;">
					<span class="bouton"><a href="#" id="valid_prefs">Enregistrer</a></span>
					<span class="bouton"><a href="#" id="delete_prefs">Tout supprimer</a></span>
				</p>
	
	<script>
	$(function() {
		$("#slider-vertical").slider({
//			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: $("#icons_field_height").val(),
			step: 10,
			slide: function(event, ui) {
				$("#icons_field_height").val(ui.value);
			}
		});
//		$("#icons_field_height").val($("#slider-vertical").slider("value"));
	});
	$('#btn-openWpp').click(function(){
		JQD.clear_active();
		$('#listImgs').hide('fast',function(){
			$(this).find('img').bind('click',function(){
				JQD.load_prefs_img($(this).attr('src'));
				$('#ch_wlppr').show();
			});
			$('#btn_openFinder').click(function(){
				$.ajax({
					type: "GET",
					url: "core/finder.php",
					data:{user:"<?php echo $login; ?>"},
					dataType: "text",
					success: function(msg){
					 $('#dirWallpaper').html(msg);
					},
					error: function(){
					},
					complete : function(data, status) {
					//alert('OK');
					}
				});	
			//	$('#dirWallpaper').load('core/finder/finder.inc.php').show();
			});
		}).show().find('.close').click(function(){
			$(this).closest('div').hide();
		});
			return false;
	});
	JQD.load_prefs_img($('#tmp_wallpaper').val());
	// on met des couleurs odd-even sur les select option
	$('select').find('optgroup option:nth-child(even)').css('background','#eaeaff');

	</script>
	</form>
</div>
