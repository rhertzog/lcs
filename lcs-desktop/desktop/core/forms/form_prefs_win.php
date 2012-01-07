<div id="contentFormWin" class="jqd_formulaires ctn-form-left">
		<ul style="" class="clear_both ul2cols">
			<li>
				<label for="winsize_w">Largeur des fen&ecirc;tres</label>
				<select id="winsize_w">
					<option value="content">Taille de la page contenue</option>
					<option value="perso">Taille personnalis&eacute;e</option>
					<option value="fullwin">Plein &eacute;cran</option>
				</select>
			</li>
			<li style="display:none;" id="winsize_perso">
				<label for="win_w">Largeur</label>
				<input type="text" id="win_w" size="3"/><div id="slider_wsp_w" class="slider"></div><br />
				<label for="win_h">Hauteur</label>
				<input type="text" id="win_h" size="3"/><div id="slider_wsp_h" class="slider"></div>
			</li>
		</ul>
</div>
<div id="contentCommentWin" class="jqd_formulaires ctn-form-right">
<p><ul><li>Pour personnaliser davantage votre bureau, placez le curseur du champ d'icônes à 100% et glissez déposez les icônes sur le bureau où bon vous semble.</li><li> Pour supprimer une icône, glissez la dans la corbeille, ou utilisez le menu contextuel (clic droit). </li></ul>
</p>
</div>
<script type="text/javascript" charset="utf-8">
	// is default configuration (only for admin)
	$('#contentFormWin, #contentCommentWin').css('background-color',JQD.setdefault==1 ? '#660000': '#000000' ) ;
	
	$( "#winsize_perso > div" ).each(function() {
		// read initial values from markup and remove that
		$( this ).slider({
			value: JQD.options.win_w || 60 ,
			range: "min",
			animate: true,
			stop: function (event, ui) {
				$(this).prev('input').attr('value',ui.value);
				var inpt = $(this).prev('input').attr('id');
				JQD.options.opts[inpt] =ui.value;
			}
		});
	});
	$('#winsize_w').attr('value', JQD.options.opts.winsize);
	JQD.options.opts.winsize == 'perso' ? $('#winsize_perso').show() : $('#winsize_perso').hide();		
	$('select#winsize_w').uniform() ;
	$('#winsize_w').change(function() {
		JQD.options.opts.winsize=$(this).val();
		$('#winsize_perso').hide();
		if ( $(this).attr('value') == 'perso' )
		$('#winsize_perso').show();
	});
	$('.slider').slider({
	//	value: JQD.options.opts.bgopct,
		slide: function (event, ui) {
		}
	});
	$( "#win_w" ).val( JQD.options.opts.win_w || 60 );
	$( "#win_h" ).val( JQD.options.opts.win_h || 60 );
</script>