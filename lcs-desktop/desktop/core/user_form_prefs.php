<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 14/03/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
session_name("Lcs");
@session_start();
$login=$_SESSION['login'];
require  "/var/www/lcs/includes/headerauth.inc.php";
//list ($idpers, $login)= isauth();

require "/var/www/Annu/includes/ihm.inc.php";
include("includes/functions.inc.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" /><!-- Pas utf-8 du a la directive apache -->
<title>...::: Bureau LCS :::...</title>
<meta name="description" content="Pr&eacute;f&eacute;rences Lcs Bureau" />
<link href="css/html.css" rel="stylesheet" />
<link href="css/desktop.css" rel="stylesheet" />
<link href="libs/farbtastic/farbtastic.css" rel="stylesheet" type="text/css" />
<link href="finder/ui.finder.css" rel="stylesheet" media="screen,print" type="text/css">
<link href="finder/ui.theme.css" rel="stylesheet" type="text/css" media="screen">
	<link rel="stylesheet" href="libs/colorpicker/css/colorpicker.css" type="text/css" />
<script src="../../libjs/jquery/jquery.js"></script>
<!--<script src="js/jquery.desktop.js"></script>-->
<script src="../../libjs/jquery-ui/jquery-ui.js"></script>
<script src="libs/farbtastic/farbtastic.js"></script>
<script src="js/jquery.metadata.js"></script>
<script src="finder/ui.finder.js"></script>
<link rel="shortcut-icon" href="../lcs/images/favicon.ico">
	<script type="text/javascript" src="libs/colorpicker/js/colorpicker.js"></script>
<!--[if lt IE 8]>
	<link href="finder/ui.finder.ie.css" rel="stylesheet" media="screen" type="text/css" />
<![endif]-->
<!--[if gte IE 7]>
<link rel="stylesheet" href="css/ie.css" />
<![endif]-->
<style>
body{background:transparent url(images/gui/trans_white_95pc.png);}
#colorSelector {
	position: relative;
	width: 36px;
	height: 36px;
	background: url(libs/colorpicker/images/select.png);
}
#colorSelector div {
	position: absolute;
	top: 3px;
	left: 3px;
	width: 30px;
	height: 30px;
	background: url(libs/colorpicker/images/select.png) center;
}
</style>
</head>
<body style="overflow:auto;text-align:left;">
<!--<div class="bar_top">
	<span style="padding-left:10px;">Param&eacute;tres du bureau</span>
	<span class="float_right">Param&egrave;tres par d&eacute;faut</span>
</div>-->
<div class="jqd_formulaires">
	<form action="">
					<?php if ( is_admin("Lcs_is_admin",$login) == "Y" ) { ?>
	<fieldset>
		<legend>Configuration par d&eacute;faut</legend>
		<ul style="" class="clear_both ul2cols">
			<li style="" class="">
				<label for="default_change_conf"> S&eacute;lectionner cette configuration comme configuration par d&eacute;faut </label>
				<input type="checkbox" id="default_change_conf" name="default_change_conf" />
				<input type="hidden" id="default_desktop_conf" name="default_desktop_conf" value="0" />
				<span style="color:#ff0000;">En tant qu'administrateur, vous pouvez s&eacute;lectionner cette configuration comme configuration par d&eacute;faut.</span>
			</li>
			<li style="padding-left:10px;text-align:center;">
			<span class="button" id="delprefsdef">Supprimer la configuration par d&eacute;faut</span>
			</li>
		</ul>
	</fieldset>
			<?php } ?>
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
				<h3 style="">Choisir une image
					<?php if( !is_eleve($login) ) { ?>
					<span class="button"> ou <a href="#" id="btn_openFinder">Importer</a></span>
					<?php } ?>
					<span class="float_left close"></span>
				</h3>
				<?php
					$imgList=scandir("images/misc/");
					foreach($imgList as $img){
						$n="<img src=images/misc/thumbs/".$img." width=\"48\" style=\"witdh:48px;margin:5px;\"/>";
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
				<li>
                                    <label for="wp_trsp">Opacit&eacute;</label>
                                    <input type="text" id="wp_trsp" name="wp_trsp" value=50 style="width:3em;" disabled="disabled"/>
                                    <span>%</span>
                                    <div id="divOpct" class="float_left"><div class="plusTrsp">+</div><div class="moinsTrsp">-</div></div>
				</li>
				<li style="" class="">
					<label for="pos_walppr">Position/Echelle</label>
					<select id="pos_walppr">
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
				<!--	<span class="float_right bouton"><a href="#" id="ch_pos_wlppr">Appliquer</a></span> -->
				</li>
				<li>
					<label for="wp_bgcolor">Couleur d'arri&egrave;re plan</label>
					<div id="colorSelector"><div class="lcs_colorpicker" style="background-color: #123456"></div></div>
					<input type="hidden" id="wp_bgcolor" name="wp_bgcolor" value="#123456" style="width:6em;" />
				</li>
			</ul>
		</div>

	</fieldset>
	<fieldset>
	<legend>Ic&ocirc;nes</legend>
		<ul style="" class="clear_both ul2cols">
			<li>
				<label>Taille des ic&ocirc;nes</label>
				<!--<img src="./lcs/images/barre1/BP_r1_c7_f3.gif" id="vign_icon" style="width:24px;height:24px;" alt="" />-->
				<span class="span_icon_prefs icon"><img src="images/icons/logo_lcs20.png" style="width:24px" /></span>
				<span class="span_icon_prefs icon"><img src="images/icons/logo_lcs20.png" style="width:36px" /></span>
				<span class="span_icon_prefs icon"><img src="images/icons/logo_lcs20.png" style="width:48px" /></span>
				<span class="span_icon_prefs icon"><img src="images/icons/logo_lcs20.png" style="width:60px" /></span>
		<!--		<span class="bouton float_right"><a href="#" id="ch_icons_larger">Appliquer</a></span>-->
				<input type="hidden" value="36" id="icons_larger" name="icons_larger" />
			</li>
			<li>
				<label for="icons_field_height">Hauteur du champ d'affichage des ic&ocirc;nes sur le bureau</label>
				<input type="text" id="icons_field_height" style="border:0; color:#f6931f; font-weight:bold;width:30px;text-align:right;" />&nbsp;<strong style="color:#f6931f">%</strong>
<!--				<span class="bouton float_right"><a href="#" id="ch_icons_field_height">Appliquer</a></span>-->
				<div id="slider-vertical" style="width:100px;"></div>
			</li>
			<li>
				<label for="aff_quicklaunch"> Afficher un Dock (fa&ccedil;on MacOs) </label>
				<input type="checkbox" id="aff_quicklaunch" name="aff_quicklaunch" />
				<span class="mess_info float_right">N&eacute;cessite le rechargement de la page apr&egrave;s enregistrement</span>
				<br class="clear_both" />
			</li>
		</ul>
	</fieldset>
	<fieldset>
	<legend>Fen&ecirc;tres</legend>
		<ul style="" class="clear_both ul2cols">
			<li>
				<label for="winsize_w">Largeur des fen&ecirc;tres</label>
				<select id="winsize_w">
					<option value="content">Taille de la page contenue</option>
					<option value="perso">Taille personnalis&eacute;e</option>
					<option value="fullwin">Plein &eacute;cran</option>
				</select>
			</li>
		</ul>
	</fieldset>

				<p style="text-align:center;margin-top:20px;">
					<span class="bouton"><a href="#" id="valid_prefs">Enregistrer</a></span>
					<span class="bouton"><a href="#" id="delete_prefs">Supprimer les pr&eacute;f&eacute;rences</a></span>
				</p>

<script>
$(document).ready(function() {
	// recuperation du desktop
	var _WP = $('#desktop', window.parent.document);

	//on charge les valeurs des prefs
	TT_load_prefs_img($('#wallpaper', window.parent.document).attr('src').replace('core/',''));
	//le slider du champ d'affichage des icones
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
			},
			stop: function(event, ui) {
				//_WP.find('#tmp_iconsfield').attr('value', ui['value']);
				parent.JQD.options.user['iconsfield'] == ui['value'];
			//	alert(ui['value']);
				parent.JQD.utils.sortIcons({"iconsfield": ui['value']});
			}
		});
	});

	//ouverture du panneau des images wpp
	$('#btn-openWpp').click(function(){
		parent.JQD.utils.clear_active();
		$('#listImgs').hide('fast',function(){
			$(this).find('img').bind('click',function(){
				TT_load_prefs_img($(this).attr('src'));
				$('#ch_wlppr').show();
			});
		}).show().find('.close').click(function(){
			$(this).closest('div').hide();
		});
		return false;
	});
	//ouverture du finder
	$('#btn_openFinder').click(function(){
		$.ajax({
			type: "GET",
			url: "finder.php",
			data:{user:"<?php echo $login; ?>"},
			dataType: "text",
			success: function(msg){
				$('#dirWallpaper').html(msg);
			}
		});
	});

	// on met des couleurs odd-even sur les select option
	$('select').find('optgroup option:nth-child(even)').css('background','#eaeaff');

	// Save params of prefs
	$('#valid_prefs').click(function(){
		parent.JQD.options.user['quicklaunch']=$('#aff_quicklaunch').is(':checked') ? 1:0;
		parent.JQD.options.user['bgopct']=$('#wp_trsp').val();
		parent.JQD.save_prefs_dev('PREFS', 'hjy', $('#default_desktop_conf').val());
	});

	// remove pref
	$('#delete_prefs').click(function(){
		//alert('PREFS_<?php echo $login; ?>');
                var delusr = '<?php echo $login; ?>';
		parent.JQD.rm('PREFS_', delusr, 'buro');
	});

	// remove pref
	$('#delprefsdef').click(function(){
		parent.JQD.rm('PREFS_', '<?php echo $login; ?>', 'default');
	});

	//colorpicker
	// TODO: A supprimer
	//$('#colorSelector .lcs_colorpicker').css('backgroundColor',_WP.find('#tmp_bgcolor').val());
	$('#colorSelector .lcs_colorpicker').css('backgroundColor',parent.JQD.options.user['bgcolor'] );
	$('#colorSelector').ColorPicker({
		color: parent.JQD.options.user['bgcolor'],
		onShow: function (colpkr) {
			$(colpkr).css('z-index', '3').fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorSelector div').css('backgroundColor', '#' + hex);
			$('#wp_bgcolor').attr('value','#' + hex);
			//_WP.find('#tmp_bgcolor').attr('value','#' + hex)
			_WP.parents('body').css('background', '#' + hex);
			//console.log("parent.JQD.options.user['bgcolor']",parent.JQD.options.user['bgcolor']);
			parent.JQD.options.user['bgcolor']=hex;
			//console.log("parent.JQD.options.user['bgcolor']",parent.JQD.options.user['bgcolor']);
		}
	});

	// wallpaper
	$('#ch_wlppr').click(function(){
		$('#listImgs,#ch_wlppr').hide();
		var prfx=$('#select_walppr').val().match('~') ? '' : 'core/';
		$('#wallpaper', window.parent.document).removeAttr('src').attr('src',prfx+$('#select_walppr').val().replace('thumbs/',''));
		// TODO: tmp_wallpaper a supprimer.
		//_WP.find('#tmp_wallpaper').attr('value',prfx+$('#select_walppr').val().replace('thumbs/',''));
		parent.JQD.options.user['wallpaper'] = prfx+$('#select_walppr').val().replace('thumbs/','');
	});
	//position wallpaper
	$('#pos_walppr').change(function() {
		$('#wallpaper', window.parent.document).removeAttr('style').removeClass().addClass($('#pos_walppr').val()) ;
	});
	//position wallpaper au chargement de la page
	$("#pos_walppr option[value=" +parent.JQD.options.user.pos_wallpaper +"]").attr("selected","selected") ;
	// couleur d'ariere-plan
	// TODO : a quoi ca sert  ?
	$('#ch_bgcolor').click(function(){
		$('body').css('background-color', $('#wp_bgcolor').val());
	});
	// transparency
	$('#divOpct div').click(function (e) {
		var c = parseInt( $('#wp_trsp').val() );
		//console.info('c',c);
		e.preventDefault();
		if ($(this).hasClass('plusTrsp') && c < 100 ){ c = c+10;$('#wp_trsp').attr({value:c})}
		else if ($(this).hasClass('moinsTrsp') && c > 0 ){c=c-10;$('#wp_trsp').attr({value:c})}
		$('#wallpaper', window.parent.document).css({opacity:c/100});
	});


	// .:LCS:.  Change icons larger
    $('.span_icon_prefs').click(function(){
	   	$('.span_icon_prefs').removeClass('selected');
		$(this).addClass('selected');
		$('#icons_larger').attr('value',$(this).children('img').width());
		_WP.find('a.icon img').css({'width': $(this).children('img').width(), 'height': $(this).children('img').width()});

		parent.JQD.utils.sortIcons({"iconsfield": $('#icons_field_height').val()});
		// TODO: supprimer tmp_iconsize
		//_WP.find('#tmp_iconsize').attr('value',$(this).children('img').width());
		parent.JQD.options.user['wallpaper'] = $(this).children('img').width();
	});
	$('.span_icon_prefs').each(function(){
		$(this).find('img').width()==_WP.find('a.icon>img').width()?$(this).addClass('selected'):'';
	});
	//$('#icons_field_height').attr('value', _WP.parent('body').find('#tmp_iconsfield').val());
	$('#icons_field_height').attr('value', parent.JQD.options.user['iconsfield']);
	//$('#slider-vertical').slider( "option", "value", _WP.parent('body').find('#tmp_iconsfield').val() );
	$('#slider-vertical').slider( "option", "value",parent.JQD.options.user['iconsfield'] );
		parent.JQD.options.user['wallpaper'] = $(this).children('img').width();
//	parent.JQD.init_icons();

	// quicklaunch
	var _a_q = $('#aff_quicklaunch');
	var _t_q = parent.JQD.options.user['quicklaunch'];
	_a_q.change(function() {
		$(this).is(':checked') ? _t_q='1' : _t_q='0';
		//$(this).is(':checked') ? parent.JQD.options.user['quicklaunch'] = '1' :parent.JQD.options.user['quicklaunch'] = '0');
		//console.info('ql: ',_t_q);
	});
	// init au chargement
	 _t_q =='1' ? _a_q.attr('checked', 'checked') : _a_q.removeAttr('checked') ;

	// conf par defaut
	if( $('#default_desktop_conf').length > 0 ) { //si on est admin
		var _dc_c = $('#default_change_conf');
		var _dd_c = $('#default_desktop_conf');
		_dc_c.change(function() {
			$(this).is(':checked') ? _dd_c.attr('value','1') : _dd_c.attr('value','0');
			//alert(_dd_c.val() );
		});
		// init au chargement
		_dc_c.removeAttr('checked') ;
	}
});

function TT_load_prefs_img(t_img) {
	$('#select_walppr').attr('value',t_img);
	$.ajax({
		type: "POST",
		url: "action/get_metas_exif_img.php",
		cache: false,
		data: ({
			file: t_img,
			user: $("#login").val()
		}),
		dataType: "text",
		success: function(msg){
			$('#ajaxTest').html(msg).find('.triangle_updown').toggle(function(){
			 	$(this).next().show().prev().addClass('down');
			},function(){
			 	$(this).next().hide().prev().removeClass('down');
			});
		}
	});
}
/*
(function($) {
JQD.prototype.eventsManager = function(fm, el) {
	var self   = this;
	this.fm    = fm;
	this.pointer = '';
	alert(fm);
}

})(jQuery);
*/
</script>
	</form>
</div>
</body>
</html>