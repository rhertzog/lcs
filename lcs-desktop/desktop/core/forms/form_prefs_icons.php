<div id="contentFormIcn" class="jqd_formulaires">
		<ul style="" class="clear_both ul2cols">
			<li style="padding-left:150px">
				<label style="margin-left:-140px;width:140px;">Taille des ic&ocirc;nes</label>
				<!--<img src="./lcs/images/barre1/BP_r1_c7_f3.gif" id="vign_icon" style="width:24px;height:24px;" alt="" />-->
				<span class="span_icon_prefs icon"><img src="core/images/icons/logo_lcs20.png" style="width:24px" /></span>
				<span class="span_icon_prefs icon"><img src="core/images/icons/logo_lcs20.png" style="width:36px" /></span>
				<span class="span_icon_prefs icon"><img src="core/images/icons/logo_lcs20.png" style="width:48px" /></span>
				<span class="span_icon_prefs icon"><img src="core/images/icons/logo_lcs20.png" style="width:60px" /></span>
		<!--		<span class="bouton float_right"><a href="#" id="ch_icons_larger">Appliquer</a></span>-->
				<input type="hidden" value="36" id="icons_larger" name="icons_larger" />
			</li>
			<li>
				<label for="icons_field_height">Hauteur du champ d'affichage des ic&ocirc;nes sur le bureau</label>
				<input type="text" id="icons_field_height"  class="slider-input" value="" />&nbsp;<strong style="color:#f6931f">%</strong><br />
<!--				<span class="bouton float_right"><a href="#" id="ch_icons_field_height">Appliquer</a></span>-->
				<div id="slider-iconfield" style="width:100px;"></div>
			</li>
			<li>
				<label for="icons_color">Couleur du texte</label>
				<select type="text" id="icons_color" >
				<option value="white">Blanc ombré noir</option>
				<option value="black">Noir ombré blanc</option>
				</select>
			</li>
		</ul>
</div>
<div id="contentCommentIcon" class="jqd_formulaires">
<p><ul><li>Pour personaliser davantage votre bureau, placez le curseur du champd'icône à 100% et glissez déposez les icônes sur le bureau ou bon vous semble.</li><li> Pour supprimer une icône, glissez la dans la corbeille, ou utilisez le menu contextuel (clic droit). </li></ul>
</p>
</div>
<script type="text/javascript" charset="utf-8">
	// is default configuration (only for admin)
	$('#contentFormIcn, #contentCommentIcon').css('background-color',JQD.setdefault==1 ? '#660000': '#000000' ) ;
		$("#slider-iconfield").slider({
			value: JQD.options.opts.iconsfield,
			range: "min",
			min: 0,
			max: 100,
			step: 10,
			slide: function(event, ui) {
				$("#icons_field_height").val(ui.value);
			},
			stop: function(event, ui) {
				//_WP.find('#tmp_iconsfield').attr('value', ui['value']);
				JQD.options.opts['iconsfield'] == ui['value'];
			//	alert(ui['value']);
				JQD.utils.sortIcons({"iconsfield": ui['value']});
			}
		});
		$("#icons_field_height").val(JQD.options.opts.iconsfield);
	// Change icons larger
    $('.span_icon_prefs').click(function(){
	   	$('.span_icon_prefs').removeClass('selected');
		$(this).addClass('selected');
		$('#icons_larger').attr('value',$(this).children('img').width());
		$('#desktop').find('a.icon img').css({'width': $(this).children('img').width(), 'height': $(this).children('img').width()});

		JQD.utils.sortIcons({"iconsfield": $('#icons_field_height').val()});
		// TODO: supprimer tmp_iconsize
		//_WP.find('#tmp_iconsize').attr('value',$(this).children('img').width());
		JQD.options.opts['wallpaper'] = $(this).children('img').width();
	});
	$('select#icons_color').find('option[value*="'+JQD.options.opts.iconcolor+'"]').attr('selected','selected');
	$('select#icons_color')
	.change(function() {
		$('a.icon').each(function(){
			$(this).toggleClass('black');
		})		
		JQD.options.opts.iconcolor=$(this).val();
	//	$('#uniform-icons_color span').text(  $('select#icons_color').find('option[value*="'+$(this).val()+'"]').text() )
	}).uniform()

	//$('#uniform-icons_color span').text(  $('select#icons_color').find('option[value*="'+JQD.options.opts.iconcolor+'"]').text() )
	/*
	.each(function(){
		$(this).attr('value') ==  JQD.options.opts.iconcolor ? $(this).attr('selected','selected') : '';
	});
	*/
	$('.span_icon_prefs').each(function(){
		$(this).find('img').width()==JQD.options.opts.iconsize ?$(this).addClass('selected'):'';
	});
</script>