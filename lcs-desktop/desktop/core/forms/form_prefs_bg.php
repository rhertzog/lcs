<?php
include "/usr/share/lcs/desktop/core/includes/desktop_check.php";
require  "/var/www/lcs/includes/headerauth.inc.php";
require "/var/www/Annu/includes/ldap.inc.php";
?>
<div id="contentSlider">
	<div class="sliderGallery">
		<ul>
		<?php
			$imgList=scandir("../images/misc/");
			foreach($imgList as $img){
				$n="<li><a href=\"#\"><img src=\"core/images/misc/thumbs/".$img."\" style=\"witdh:118px:\"alt=\"\" width=\"118\"/></a></li>";
				if($img{0}!="." && $img!="thumbs")
					echo $n;
			}
		?>
		</ul>
		<div id="divWpp" class="slider ui-slider"></div>
		<br style="clear:both"/>
	</div>
	<div>
		<span class="slider-lbl1">Glisser le bouton pour faire defiler</span>
		<br class="clear_both"/>
	</div>
<?php
if ( !is_eleve($login) && is_dir('/usr/share/lcs/elfinder') ) {
?>
<script>
$('head').append('<link rel="stylesheet" href="../elfinder/elfinder/css/elfinder.css" type="text/css" media="screen" title="no title" charset="utf-8"/>');
</script>
<script src="../elfinder/elfinder/js/elfinder.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../elfinder/elfinder/js/i18n/elfinder.fr.js" type="text/javascript" charset="utf-8"></script>
	<div class="align_center">
		<a class="button" id="btnOpenElfinder">
			<img src="core/images/icons/arrow_up_32.png" alt="" style="vertical-align:middle;"/>Téléverser une image depuis votre espace web
		</a>
	</div>
<?php
}
?>

<br class="clear_both"/>
</div>
<div id="contentFormBg" class="jqd_formulaires">
<form>
	<ul class="clear_both ul2cols">
		<li style="padding-left:110px;">
					<label for="pos_walppr" style="margin-left:-110px;width:110px;">Position/Echelle</label>
					<select id="pos_walppr" style="width:130px;">
						<option value="wallpaper">Plein &eacute;cran</option>
						<optgroup label="Position">
							<option value="abs top_left">En haut &agrave; gauche</option>
							<option value="abs top_right">En haut &agrave; droite</option>
							<option value="abs bottom_left">En bas &agrave; gauche</option>
							<option value="abs bottom_right">En bas &agrave; droite</option>
						</optgroup>
						<optgroup label="Echelle">
							<option value="fit_width center_v">Ajuster en largeur / Centrer verticalement</option>
							<option value="abs fit_width_top">Ajuster en largeur / Placer en haut</option>
							<option value="abs fit_width_bottom">Ajuster en largeur / Placer en bas</option>
							<option value="fit_height center_h">Ajuster en hauteur / Centrer horizontalement</option>
							<option value="abs fit_height_left">Ajuster en hauteur / Placer &agrave; gauche</option>
							<option value="abs fit_height_right">Ajuster en hauteur / Placer &agrave; droite</option>
						</optgroup>
					</select>
		</li>
		<li style="padding-left:110px;">
			<label for="" style="margin-left:-110px;width:110px;">Opacité</label>
			<input type="text" id="opctSelect"name="opctSelect" class="slider-input" value="" />&nbsp;<strong style="color:#f6931f">%</strong>
			<br />
			<div>
		        <div id="divOpct" class="slider ui-slider"></div>
			</div>
			<br />
		</li>
		<li style="padding-left:110px;"><label for="" style="margin-left:-110px;width:110px;">Couleur d'arrière-plan</label>
			<div id="colorSelector" style="z-index:3"><div style="background-color: #0000ff;height:24px;border:1px solid #fff;width:24px"></div></div>
		</li>
	</ul>
</form>
<?php
if ( acces_btn_admin($login) == "Y" ) {
	$cnfDflt = '<div id="" style="background-color:#660000;width:100%;margin-top:25px;">'
 					.'<label for="defaultBg"><input type="checkbox" name="defaultBg" id="defaultBg" value=""/>'
 					.' Enregistrer comme configuration par défault</label>'
 					.'</div>';
 	echo $cnfDflt;
}

?>
</div>
<script type="text/javascript" charset="utf-8">
(function () {
JQD.forms.bg=function() {
	$('select#pos_walppr').uniform() ;
	var container = $('div.sliderGallery');
	var ul = $('ul', container);

	var itemsWidth = ul.innerWidth() - container.outerWidth();

	$('.sliderGallery>ul li img').click(function(){
		var prfx=$(this).attr('src');
		$('#wallpaper', window.document).removeAttr('src').attr('src',prfx.replace('thumbs/',''));
		JQD.options.opts['wallpaper'] = prfx.replace('thumbs/','');
	});

	$('#divWpp.slider', container).slider({
		min: 0,
		animate: true,
		max: itemsWidth,
		stop: function (event, ui) {
			ul.animate({'left' : ui.value * -1}, 500);
		},
		slide: function (event, ui) {
//			ul.animate({'left' : ui.value * -1}, 500);

		}
	});

	$('#divOpct.slider').slider({
		value: JQD.options.opts.bgopct,
		slide: function (event, ui) {
			$( "#opctSelect" ).val(ui.value );
			$('#wallpaper', window.document).css({opacity:ui.value /100});
			JQD.options.opts['bgopct']= ui.value;
		}
	});
	$( "#opctSelect" ).val( JQD.options.opts.bgopct );

	$('#colorSelector').ColorPicker({
		color: RGBtoHEX(JQD.options.opts.bgcolor),
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorSelector div, body').css('backgroundColor', '#' + hex);
			JQD.options.opts['bgcolor']=hex;
		}
	});
	$('#colorSelector div').css('backgroundColor', JQD.options.opts.bgcolor);

		//position wallpaper
	$('#pos_walppr').change(function() {
		$('#wallpaper', window.document).removeClass().addClass($('#pos_walppr').val()) ;
		JQD.options.opts['pos_wallpaper'] = $('#pos_walppr').val();
	});

	//configuration par defaut ?
	$('#defaultBg').live('change',function(){
		$('#contentSlider, #contentFormBg').css('background-color',$('#defaultBg').is(':checked') ? '#660000': '#000000') ;
		if ( $('#defaultBg').is(':checked') )
		{
		//	$('.admin_only').show();
		JQD.setdefault=1;
		}
		else
		{
		// 	$('.admin_only').hide();
			JQD.setdefault=0 ;
		}
	});
	if (JQD.setdefault==1)
	{
		$('#defaultBg').attr('checked','checked')  ;
		//$('.admin_only').show();
	}
	$('#contentSlider, #contentFormBg').css('background-color', JQD.setdefault==1 ? '#660000': '#000000') ;

	//position wallpaper au chargement de la page
	$("#pos_walppr option[value='" +JQD.options.opts.pos_wallpaper +"']").attr("selected","selected") ;

	//options elfinder
	var fOpts = {
		url : 'core/action/connector.php',
		lang : 'fr',
		places : "Favoris",
		toolbar : [
			['back', 'reload'],
			['select'/*, 'open'*/],
			['upload'],
			['quicklook', 'info'],
			['rename', 'resize'],
			['icons', 'list'],
			['help']
		],
		contextmenu : {
			// Commands that can be executed for current directory
			cwd : ['reload', 'delim', 'upload', 'delim', 'info'],
			// Commands for only one selected file
			file : ['select'/*, 'open'*/, 'delim', 'copy', 'cut', 'rm', 'delim', 'duplicate', 'rename', 'edit', 'rtedit', 'resize', 'delim', 'archive', 'extract', 'delim', 'quicklook', 'info'],
			// Coommands for group of selected files
			group : ['copy', 'cut', 'rm', 'delim', 'archive', 'extract', 'delim', 'color', 'delim', 'info']
			},
		// Callback
		editorCallback : function(url){
			var si = $('<span/>');
			si.load(url, function(response, status, xhr) {
			});
			JQD.options.opts.wallpaper = url;
			$('#wallpaper').attr({src: url});
		},
		closeOnEditorCallback : false
	};
	//appel et création de elfinder
	$('#btnOpenElfinder').click(function() {
	var f = $('<div id="fpFinder"/>').attr('title', 'Choisir une image').css({'text-align':'left', 'font-size':'1..2em'}).elfinder(fOpts).dialog({
			dialogClass : 'el-finder-dialog el-finder-dialog-info',
			width 		: 690,
			height 		:300,
	//		position 		: p,
			title  			: 'Choisir une image',
			close 			: function() { $(this).dialog('destroy'); },
			buttons 		: { Fermer : function() { $('#fpFinder').elfinder('close').elfinder('destroy'); $(this).dialog('destroy'); }}
		})
	});
}
JQD.forms.bg();

//actual converter function called by main function
function toHex(N) {
	if (N==null) return "00";
	N=parseInt(N); if (N==0 || isNaN(N)) return "00";
	N=Math.max(0,N); N=Math.min(N,255); N=Math.round(N);
	return "0123456789ABCDEF".charAt((N-N%16)/16) + "0123456789ABCDEF".charAt(N%16);
}

//function called to return hex string value
function RGBtoHEX(str)
{
	//check that string starts with 'rgb'
	if (str.substring(0, 3) == 'rgb') {
		var arr = str.split(",");
		var r = arr[0].replace('rgb(','').trim(), g = arr[1].trim(), b = arr[2].replace(')','').trim();
		var hex = [
			toHex(r),
			toHex(g),
			toHex(b)
		];
		return "#" + hex.join('');
	}
	else{
		//string not rgb so return original string unchanged
    return str;
	}
}

})();

</script>
