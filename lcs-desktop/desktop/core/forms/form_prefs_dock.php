<div id="contentFormDock" class="jqd_formulaires ctn-form-left">
	<ul class="clear_both ul2cols">
		<li>
			<label for="aff_quicklaunch"> Afficher un Dock (fa&ccedil;on MacOs) </label>
			<input type="checkbox" id="aff_quicklaunch" name="aff_quicklaunch" />
		</li>
<!--
			<li>
				<label for="dispQuick">Icones par défaut affichées sur le bureau à la première connexion</label>
				<select id="dispQuick" multiple="multiple" rows="7" style="width:98%;height:100px;font-size:10px;border:1px solid aaa, color:#f0f0f0;background-color:#666666">
				</select>
			<div>
			<input type="checkbox" id="selectAllQuick" name="selectAllQuick" value="all"> 
			<label for="selectAllQuick">Tout sélectionner</label>
			</div>
			</li>
-->
	</ul>
	<br class="clear_both" />
</div>
<div id="contentCommentDock" class="jqd_formulaires ctn-form-right">
	<div class="mess_info" style="background-color:#f5f5f5">N&eacute;cessite le rechargement de la page apr&egrave;s enregistrement</div>
<p><ul><li>-</li></ul>
</p>
</div>
<script type="text/javascript" charset="utf-8">
	// is default configuration (only for admin)
	$('#contentFormDock, #contentCommentDock').css('background-color',JQD.setdefault==1 ? '#660000': '#000000' ) ;
	// quicklaunch
	var pql = JQD.options.opts.quicklaunch;
	
	$('#aff_quicklaunch').attr({ checked : ( pql =='1' ? 'checked' : '' ) }) .change( function() {
		$(this).is(':checked') ? JQD.options.opts.quicklaunch ='1' : JQD.options.opts.quicklaunch = '0';
	});
</script>