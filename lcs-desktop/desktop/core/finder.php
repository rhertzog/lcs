<?php
isset($_GET['user'])?$who=$_GET['user']:$who='DtQ';
require  "../../lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();
 if($login!=$who) {echo "<div>Probl&egrave;me d'acc&egrave;s &agrave; votre dossier. Veuillez v&eacute;rifier votre connexion</div>";
 return;
 } 
 else {
?>
<script type="text/javascript">
$(function(){
	
	var initFinder = function() {
	/*
	 * Initialise Finder
	 */
	$('#finder').finder({
		title : 'Documents',
		url:'core/includes/inc-finder.php?user=<?php echo $login; ?>',
		onRootReady: function(rootList,finderObj){
//			debug('Root ready',arguments)
//			$('ol li:nth-child(odd)').css('background','#eaeaff');
			$('span.close').click(function(){
				$('#dirWallpaper').html(' ');
			});
//			$('.ui-finder').find('span.mess_info a').click(function(){
//					JQD.init_link_open_win(this);
//			});
			$('.ui-finder').find('a.open_win').each(function(){
					//alert("click="+$(this).attr('href'));
				$(this).click(function(){
					//alert("click="+$(this).attr('href'));
					JQD.init_link_open_win(this);
					setTimeout(function(){
						$('#iframe_lcs_ftpclient').contents().find('table').each(function(){
							$(this).find('td.td100perc').attr('style',"width:300px;");
							$(this).find('tr:odd td').css({'background-image':'url(../../lcs/fdecran/espaceweb_odd.jpg)','background-attachment':'fixed','background-position':'80% 10%','background-repeat':'no-repeat'});
							$(this).find('tr').each(function(){
								$(this).append("<td><a class=\"select_this_image\" href=\"#\"><img hspace=\"2\" border=\"0\" align=\"middle\" style=\"width:16px;\" vspace=\"1\" title=\"Choisir cette image\" src=\"pict/icons/nuvola/green-go-up.png\"></a></td>").find('a.select_this_image').click(function(){
									alert($(this).closest('tr').find('td.td100perc a').text());
//									var current =  $(this);
//									var currentName=current.text();
//									current = (current) ? current.attr('rev') : 'no';
								});
							});
						});
					},4000);
				});
			});
		},
		onInit : function(finderObj) {
			
//			debug('Finder initialised',arguments)
		$('.ui-finder-action-current').click(function(){
			$('[name="getCurrent"]').click();
		});

			
		},
		onItemSelect : function(listItem,eventTarget,finderObject){			
			var anchor = $('a',listItem),
				href = anchor.attr('href');
			
		// Debug is a function specified in Finder script for debugging purposes
		// Remove it if unnecessary
//			debug('onItemSelect - URL: ',href)
		
		// By returning false, the url specified is not fetched
		// ie. Do not display new column if selected item is not an image
//			alert(href.indexOf('.png'));
			if(href.indexOf('.png') == -1) {return false;}
		},
		onFolderSelect : function(listItem,eventTarget,finderObject){
			var anchor = $('a',listItem),
				href = anchor.attr('rel');
//				$('a',listItem).closest('.ui-finder-column').next('.ui-finder-column').find('.ui-finder-content ol li:nth-child(odd)').css('background','#eaeaff');
				
//			debug('onFolderSelect - URL: ',href)
		},
		onItemOpen : function(listItem,newColumn,finderObject){
			var anchor = $('a',listItem),
				href = anchor.attr('href'),
				aText = anchor.attr('rev').split('/');
			newColumn.find('.img_infos_more span').toggle(function(){
				$(this).next('ul').show();
				newColumn.find('.img_infos_more span').addClass('down');
			},function(){
				$(this).next('ul').hide();
				newColumn.find('.img_infos_more span').removeClass('down');
			});
			var aDir= '';
			$.each(aText, function(index, value) { 
				value!=''? aDir+= '<span class="img_infos_more">'+value+'</span>':'';
				//  alert(index + ': ' + value +aDir); 
			});

			setTimeout(function(){
				$('.ui-finder-title').html('Documents'+aDir);
				$('.select_this').remove();
				$('.ui-finder-header').append('<span style="" class="select_this">S&eacute;lectionner cette image</span>');				/*	$('.file-preview').find('.triangle_updown').toggle(function(){
					 $(this).next().show().prev().addClass('down');
				},function(){
					 $(this).next().hide().prev().removeClass('down');
				}); */
				newColumn.find('.triangle_updown').click(function () {
					$(this).toggleClass("down");
					$(this).next().toggleClass("down")
				});
				
			},1000);

			
//			debug('onItemOpen - Column source: ',newColumn.attr('data-finder-list-source'))

		},
		onFolderOpen : function(listItem,newColumn,finderObject){
			var anchor = $('a',listItem),
				href = anchor.attr('href'),
				aText = anchor.attr('rev').split('/');
				var aDir= '';
			$.each(aText, function(index, value) { 
				aDir+= '<span class="img_infos_more">'+value+'</span>';
				//  alert(index + ': ' + value +aDir); 
			});
			$('.select_this').remove();
			$('.ui-finder-title').html('Documents'+aDir);
			$('.ui-finder').find('a.open_win').each(function(){
				//alert("click="+$(this).attr('href'));
				$(this).click(function(){
					JQD.init_link_open_win(this);
				});
			});
				
			
//			alert('toto');
//			$('ol li:nth-child(odd)').css('background','#eaeaff');
			
//			debug('onFolderOpen - Column source: ',newColumn.attr('data-finder-list-source'))
		},
		toolbarActions : function() {
			return '\
			<div class=" float_right" title="Fermer">\
			<span class="close"></span>\
			</div>\
			<div class="ui-finder-action-current float_right" title="Choisir">\
			<span class="select"></span>\
			</div>\
			';
			/*<div class="ui-finder-button ui-state-default ui-corner-right ui-finder-action-refresh" title="Refresh">\
				<span class="ui-icon ui-icon-refresh"/>\
			</div>\
			<div class="ui-finder-button ui-state-default ui-finder-action-open" title="Open ..">\
				<span class="ui-icon ui-icon-folder-open"/>\
			</div>\
			<div class="ui-finder-button ui-state-default ui-finder-action-current ui-corner-left" title="Get current">\
				<span class="ui-icon ui-icon-help"/>\
			</div>\
			<div class="ui-finder-button ui-state-default ui-finder-action-destroy ui-corner-all" title="Destroy">\
				<span class="ui-icon ui-icon-closethick"/>\
			</div>\*/
		} 
		
	});
	
	
};
	$('[name="createFinder"]').toggle(function(){
		initFinder();
		$('button[disabled]').attr('disabled',false);
		$(this)
			.attr('data-code',$(this).next('code').text())
			.text('Destroy Finder');
		$(this).next('code').text('$(selector).finder(\'destroy\')')
	},function(){
		$('#finder').finder('destroy');
		$('button').slice(1).attr('disabled',true);
		$(this).text('Create Finder');
		$(this).next('code').text( $(this).attr('data-code') )
	});

/*	$('[name="createFinder"]').toggle(function(){
		initFinder();
		$('button[disabled]').attr('disabled',false);
		$(this)
			.attr('data-code',$(this).next('code').text())
			.text('Destroy Finder');
		$(this).next('code').text('$(selector).finder(\'destroy\')')
	},function(){
		$('#finder').finder('destroy');
		$('button').slice(1).attr('disabled',true);
		$(this).text('Create Finder');
		$(this).next('code').text( $(this).attr('data-code') )
	});
*/	
/*
 * Some events to allow API interaction with buttons
 * Not a part of Finder script, they just show how to interact with API
 */
	$('[name="getCurrent"]').click(function(){
		var current =  $('#finder').finder('current');
		var currentName=current.attr('title');
		current = (current) ? current.attr('rev') : 'no';
		if(current=="no") {
		alert("Vous devez choisir une image");
			return false;
		}
		else {
		var currentPath=currentName.replace('../','core/').replace('/home/','../~').replace('public_html/','');
		alert(currentPath);
			$('#vign_wlpper').attr('src',currentPath);
			$('#select_walppr').attr('value',currentPath);
			$('#dirWallpaper').html(' ');
			$('#ch_wlppr').show();
		}
			
	});

			$('.ui-finder-content').find('span.mess_info a').click(function(){
					JQD.init_link_open_win(this);
			});

	$('[name="select"]').click(function(){
		var which = prompt('URL of item to select', $('a:first','#finder').attr('rel'));
		if(which && which.length>0) {$('#finder').finder('select',which);}
	});

	$('[name="refresh"]').click(function(){$('#finder').finder('refresh');});
	$('[name="test"]').click(function(){test();});
	$('[name="test2"]').click(function(){test2();});


	$('[name="createFinder"]').click();
	
	
//	$('#switcher').themeswitcher();

/*
 * A Click event to toggle the image preview, when viewing an image
 * Not a part of Finder script 
 */	
	$(document)
	.unbind('click.FinderPreview') // Click event to handle file previews etc
	.bind('click.FinderPreview',function(e){
		var title = $(e.target);
		
		if( !title.hasClass('ui-finder-preview-heading')
			&& title.parent('.ui-finder-preview-heading').length === 0 )
			{ return; }
		
		title = ( title.hasClass('ui-finder-preview-heading') )
					? title
					: title.parent('.ui-finder-preview-heading') ;
		
		var image = title.siblings('.ui-finder-image'),
			span = $('span',title);
		
		if( image.length != 1  ) { return; }
		
		if(image.is(':visible')) {
			image.slideUp();
			title.addClass('ui-finder-preview-heading-closed');
			span.removeClass('ui-icon-circle-triangle-s');
			span.addClass('ui-icon-circle-triangle-e');
		} else {
			image.slideDown();
			title.removeClass('ui-finder-preview-heading-closed');
			span.removeClass('ui-icon-circle-triangle-e');
			span.addClass('ui-icon-circle-triangle-s');
		};
		
		return false;
	});
	
});
</script>
<div id="switcher"></div>

<ol id="finder">
</ol>
<!--<a href="#" id="btn_fInfos">infos</a>-->
<div id="fInfos" style="display:none;">
<hr>
<div style="float:left;width:45%">
<h3>Public methods</h3>
<ol>
	<li><button name="createFinder">Create finder</button> <code>$(selector).finder([options])</code></li>
	<li><button name="select" disabled>Select Item</button> <code>$(selector).finder('select',URL || DOM or jQuery object || Array of URLs)</code></li>
	<li><button name="getCurrent" disabled>Get Current</button> <code>$(selector).finder('current')</code></li>
	<li><button name="refresh" disabled>Refresh Current</button> <code>$(selector).finder('refresh')</code></li>
</ol>
You can also use the toolbar buttons for these actions.
</div>
</div>
<?php
 }
 ?>