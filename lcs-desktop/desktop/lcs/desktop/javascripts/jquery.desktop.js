//
// Namespace - Module Pattern.
//
var JQD = (function($) {
	return {
		//
		// Initialize the clock.
		//
		init_clock: function() {
			// Date variables.
			var date_obj = new Date();
			var hour = date_obj.getHours();
			var minute = date_obj.getMinutes();
			var day = date_obj.getDate();
			var year = date_obj.getFullYear();
			var suffix = 'AM';

			// Array for weekday.
// .:LCS:. in french please
			/*
			var weekday = [
				'Sunday',
				'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday'
			];
			*/
			// Table des jours de la semaine - weekday.
			var weekday = [
				'Dimanche',
				'Lundi',
				'Mardi',
				'Mercredi',
				'Jeudi',
				'Vendredi',
				'Samedi'
			];
			// Array for month.
// .:LCS:. in french please
 /*
			var month = [
				'January',
				'February',
				'March',
				'April',
				'May',
				'June',
				'July',
				'August',
				'September',
				'October',
				'November',
				'December'
			];
*/
			// Table des mois - month.
			var month = [
				'Janvier',
				'F&eacute;vrier',
				'Mars',
				'Avril',
				'Mai',
				'Juin',
				'Juillet',
				'Ao&ucirc;t',
				'Septembre',
				'Octobre',
				'Novembre',
				'D&eacute;cembre'
			];

			// Assign weekday, month, date, year.
			weekday = weekday[date_obj.getDay()];
			month = month[date_obj.getMonth()];

			// AM or PM?
			if (hour >= 12) {
				suffix = 'PM';
			}

			// Convert to 12-hour.
			if (hour > 12) {
				hour = hour - 12;
			}
			else if (hour === 0) {
				// Display 12:XX instead of 0:XX.
				hour = 12;
			}

			// Leading zero, if needed.
			if (minute < 10) {
				minute = '0' + minute;
			}

			// Build two HTML strings.
			var clock_time = weekday + ' ' + hour + ':' + minute + ' ' + suffix;
			// .:LCS:. in french please
			//var clock_date = month + ' ' + day + ', ' + year;
			var clock_date = day + ' ' + month + ' ' + year;

			// Shove in the HTML.
			$('#clock').html(clock_time).attr('title', clock_date).dblclick(function() {
				$(this).attr('title', 'agendas');
				JQD.init_link_open_win(this);
				setTimeout($(this).attr('title', clock_date), 1000);
				return false;
			});

			// Update every 60 seconds.
			setTimeout(JQD.init_clock, 60000);
		},

		//
		// Clear active states, hide menus.
		//
		clear_active: function() {
			$('a.active, tr.active').removeClass('active');
			$('ul.menu').hide();
			$('#otBuro_1 ul').slideUp('slow');
		},

		//
		// Zero out window z-index.
		//
		window_flat: function() {
			$('div.window').removeClass('window_stack');
		},

		//
		// Resize modal window.
		//
		window_resize: function(el) {
			// Nearest parent window.
			var win = $(el).closest('div.window');

			// Is it maximized already?
			if (win.hasClass('window_full')) {
				// Restore window position.
				win.removeClass('window_full').css({
					'top': win.attr('data-t'),
					'left': win.attr('data-l'),
					'right': win.attr('data-r'),
					'bottom': win.attr('data-b'),
					'width': win.attr('data-w'),
					'height': win.attr('data-h')
				});
			}
			else {
				win.attr({
					// Save window position.
					'data-t': win.css('top'),
					'data-l': win.css('left'),
					'data-r': win.css('right'),
					'data-b': win.css('bottom'),
					'data-w': win.css('width'),
					'data-h': win.css('height')
				}).addClass('window_full').css({
					// Maximize dimensions.
					'top': '0',
					'left': '0',
					'right': '0',
					'bottom': '0',
					'width': '100%',
					'height': '100%'
				});
			}

			// Bring window to front.
			JQD.window_flat();
			win.addClass('window_stack');
		},
		
		
		//
		// .:LCS:.  Initialize the icons
		//
		init_icons: function() {
			// reposition icons
			var nb_icons = $('#desktop a.abs.icon').length;
			var t = 20;
			var w = 20;
			var s = 0;
			$('#desktop a.abs.icon').each(function(){
				var h_i = $(this).outerHeight(true) + 5;
				var h_d = $('#desktop').outerHeight(true);
				h_d= h_d * $('#icons_field_height').val() /10;
				s +=1;
				if(t > h_d-h_i ){ t = 20; w += 100};
//				if(s == 6 ){ t = 20; w += 125};
				$(this).css({'top': t,'left': w});
				t += h_i;
			});
		},
		
		//
		// .:LCS:.  init window. transform in function to call easyer
		//
		make_win_move: function(){
			// Make windows movable.
			$('div.window').mousedown(function() {
				// Bring window to front.
				JQD.window_flat();
				$(this).addClass('window_stack');
			}).draggable({
				// Confine to desktop.
				// Movable via top bar only.
				containment: 'parent',
				handle: 'div.window_top'
			}).resizable({
				containment: 'parent',
				minWidth: 150,
				minHeight: 150

			// Double-click top bar to resize, ala Windows OS.
			}).find('div.window_top').dblclick(function() {
				JQD.window_resize(this);

			// Double click top bar icon to close, ala Windows OS.
			}).find('img').dblclick(function() {
				// Traverse to the close button, and hide its taskbar button.
				$($(this).closest('div.window_top').find('a.window_close').attr('href')).hide('fast');

				// Close the window itself.
				$(this).closest('div.window').hide();

				// Stop propagation to window's top bar.
				return false;
			});
			// Get action buttons for each window.
			$('a.window_min, a.window_resize, a.window_close').mousedown(function() {
				JQD.clear_active();
				// Stop propagation to window's top bar.
				return false;
			});

			// Minimize the window.
			$('a.window_min').click(function() {
				$(this).closest('div.window').hide();
			});

			// Maximize or restore the window.
			$('a.window_resize').click(function() {
				JQD.window_resize(this);
			});

			// Close the window.
			$('a.window_close').click(function() {
				$(this).closest('div.window').hide();
				$($(this).attr('href')).hide('fast');
			});

			// Show desktop button, ala Windows OS.
			$('#show_desktop').click(function() {
				// If any windows are visible, hide all.
				if ($('div.window:visible').length) {
					$('div.window').hide();
				}
				else {
					// Otherwise, reveal hidden windows that are open.
					$('#dock li:visible a').each(function() {
						$($(this).attr('href')).show();
					});
				}
			});
		},
		
		//
		// .:LCS:. Load xml preferences
		//
		jqd_load_xml: function ( suser ) { 
			$.ajax({
				type: "GET",
				url: "desktop/xml/" + $("#login").val() +"/lcs_buro_" + $("#login").val() +".xml", // on cherche dans le bon rep
				cache: false, 
				dataType: "xml", 
				complete : function(data, status) {
					var resp = data.responseXML;
					// Traitement du xml
					$(resp).find('bureau').each(function(){
						$(resp).find('userburo').each(function(){
							var e_name = $(this).attr('name');
								var e_ticket = $(this).attr('ticket');			
								$('#ticket_prefs').attr('value',e_ticket);
			
							$(this).find('wallpaper').each(function(){
								e_wallppr = $(this).text() ;
								$('#wallpaper').attr('src', "desktop/images/misc/" + e_wallppr);
						        $("#vign_wlpper").attr('src', "desktop/images/misc/"+ e_wallppr);
							});
							
							$(this).find('iconsize').each(function(){
								e_icon_large = $(this).text() ;
								$('a.icon img').css('width', e_icon_large +'px').css('height', e_icon_large +'px');
					   		    $("#vign_icon").css({ width: e_icon_large+"px", height : e_icon_large+"px" }).attr({width: e_icon_large+"px", height: e_icon_large+"px"});
							});
						});
					});
		
				}
			});
		},
		
		//
		// .:LCS:. Build xml to save preferences
		//
		jqd_build_xml: function(suser) { 
			var str = "" ;
			//str = str + "<?xml version='1.0' encoding='utf-8'?>\r\n" ;
				str = str + "<bureau>" + "\r\n" ;
				str = str + "\t"+"<userburo id='" + $("#login").val() +"' name ='" + $("#login").val() +"' ticket='" + $("#ticket_prefs").val() +"_"+"' class=''>" + "\r\n" ;
				str = str + "\t\t"+"<wallpaper title='' id='' class=''>" + $("#select_walppr").val() +"</wallpaper>" + "\r\n" ;
				str = str + "\t\t"+"<iconsize id='' height='' class=''>" + $("#icons_larger").val() +"</iconsize >" + "\r\n" ;
				str = str + "\t\t"+"<iconsfield id='' height='' class=''>" + $("#icons_field_height").val() +"</iconsfield>" + "\r\n" ;
				$('#desktop a.icon').each(function(){
					str = str + "\t\t"+"<icon>" + "\r\n" ;
					str = str + "\t\t\t"+"<icontext>"+ $(this).text() +"</icontext>" + "\r\n" ;	
					strlink = $(this).attr('rel');			
					str = str + "\t\t\t"+"<iconurl>"+strlink.replace('&', '&amp;')+"</iconurl>" + "\r\n" ;				
					str = str + "\t\t\t"+"<iconwin>"+$(this).attr('href')+"</iconwin>" + "\r\n" ;				
					str = str + "\t\t\t"+"<icontitle>"+$(this).attr('title')+"</icontitle>" + "\r\n" ;				
					str = str + "\t\t\t"+"<iconimg>"+$(this).find('img').attr('src')+"</iconimg>" + "\r\n" ;				
					str = str + "\t\t"+"</icon>" + "\r\n" ;
				});
				str = str + "\t"+"</userburo>" + "\r\n" ;
				str = str + "</bureau>" + "\r\n" ;
			
			return str ;
		},

		// .:LCS:. envoi xml
		save_xml: function(file, b_xml, groups) { 
			$.ajax({
				type: "POST",
				url: "desktop/action/save_xml.php",
				cache: false,
				data: ({file: file+'_'+$("#login").val(),
						user: $("#login").val(),
						groups : groups,
						data : b_xml}),
				dataType: "text",
				success: function(msg){
					JQD.parcours_load_xml(msg.replace('lcs_list_',''));
//					alert( "Data Saved: " + msg.replace('lcs_list_','') );
					$('#mess_save_list').addClass('success').show('slow').html('Sauvegarde r&#233;ussie : '+msg)
					setTimeout(function(){$('#mess_save_list').hide(2000);},5000);
				},
				error: function(){
				},
				complete : function(data, status) {
				}
			});	
		},

		delette_xml: function(file) { 
			$.ajax({
				type: "POST",
				url: "desktop/action/delette_xml.php",
				cache: false,
				data: ({file: 'lcs_list_'+file,
						user: $("#login").val()
						}),
				dataType: "text",
				success: function(msg){
					alert('succes : ' + msg);
				},
				error: function(msg){
					alert('error : ' + msg);
				},
				complete : function(msg) {
//					var resp = data.responseXML;
					alert('resultat : ' + msg);
				}
			});	
		},

		 
		 init_link_open_win: function(el){
//				alert('1 : '+el);
				var url = $(el).attr('href');
				var text = $(el).text();
				var title = $(el).attr('title');
				if (text == '') text = title;
				var title_win =' LCS : ' + text;
//				var img = $(el).find('img').attr('src');
				var img='images/barre1/BP_r1_c1.gif';
				if ($(el).find('img').length > 0) img = $(el).find('img').attr('src');
				var rel =  $(el).attr('rel');
				if (rel=='') var rel = parseInt($(el).parent('li').index()+1);
				var p_left = 0;var p_top = 0;
				var nb_win = $('.window:visible').length;
				p_left = p_top = nb_win*25;
				el.blur();
				if (url.match(/^#/)) {
				var url = $(el).attr('rel');
				var rel = $(el).attr('title');
//				alert('2 : '+url+' / '+rel);
//					return false;
				}
//				if (url.match('://') || url.match('../') || url.match('statandgo')) {
//				alert(el);
		
					// Get the link's class.
					if($(el).hasClass('ext_link')){
						var win_o = $('#desktop').find('#window_lcs_'+rel).length;
//							alert(win_o);
						if (win_o == 0){
							$('#window_lcs_temp').clone().appendTo('#desktop')
								.attr('id', 'window_lcs_'+rel)
								.find('a.window_close').attr('href','#icon_dock_lcs_'+rel)
								.parents('div.window').find('img').attr('src', img)
								.parents('div.window').find('span.window_title, .window_bottom').text(title_win);
						JQD.make_win_move();
							$('#icon_dock_lcs_temp').clone().appendTo('#dock')
								.attr('id', 'icon_dock_lcs_'+rel)
								.find('a').attr('href','#window_lcs_'+rel).text(title_win)
								.append('<img src="'+img+'" alt="" style="height:22px;" />' );
						}
						JQD.make_win_move();
						var x = $('#icon_dock_lcs_'+rel);
						var y = $('#window_lcs_'+rel);
//						y.removeClass('window_stack');
					}else{
						JQD.make_win_move();
						var x = $('#icon_dock_lcs_path');
						var y = $('#window_lcs_path');
						$('#window_lcs_path').find('span.window_title, .window_bottom').text(title_win)
						.parents('div.window').find('img').attr('src', img);
						$('#icon_dock_lcs_path a').text(title_win)
						.append('<img src="'+img+'" alt="" style="height:22px;" />' );
					}
//					alert(x + ' - ' + y);
						JQD.make_win_move();
					JQD.clear_active();
		
					// Show the taskbar button.
					if (x.is(':hidden')) {
						x.remove().appendTo('#dock').end().show('fast');
						// .:LCS:. prepar adptation width of dock's items
						var x_d = ($('#desktop').width() -200);
						var nb_items =  $('ul#dock li:visible').length;
						if (x.position().left > x_d){
							var w_item = (x_d/nb_items-40);
							alert('on depasse : '+x_d+' / ' +nb_items+' / ' +parseInt(w_item));
							$('ul#dock li:visible').each(function(){
								$(this).attr('style', 'width:'+parseInt(w_item)+'px;overflow:hidden;').show('fast');
							});
						}
					}
					if (y.is(':hidden')) {
						var mov='ok_';
					}
					// Bring window to front.
					JQD.window_flat();
					//.: LCS :. params in urls ??
					y.addClass('window_stack').show().find('iframe').attr('src',url);
					// .:LCS:.  on repositionne la fenetre
					if (mov=='ok_') {
						y.animate({'top': '+='+p_top, 'left' : '+='+p_left});
						p_left = 0;p_top = 0;
					}
//									JQD.window_resize(y);

//				}
					return false;
		 },

		//###############
		// Initialize the desktop.
		//###############
		init_desktop: function() {
			if (window.location !== window.top.location) {
				window.top.location = window.location;
			}
			//***********
			// TESTS
			//*********
			// gestion des bureaux secondaires
			var left_o = $('#desktop').width();
			$('#inettuts').animate({left: left_o+'px'});
			$('#monLcs').animate({left: -left_o+'px',right: left_o+'px'});

			$('#otBuro_2').click(function(){
			$('#otBuro_1 ul').show('fast');
			});
			$('#otBuro_1 li a').click(function(){
//					alert($(this).text()+' / '+$(this).attr('class'));
				if($(this).not('.space')){
					if(($(this).text().substring(0,1)=='2') && ($('#inettuts').position().left!=0)){
						if($('#wallpaper_b').length == 0){
							$('#wallpaper').clone().prependTo('body').attr('id', 'wallpaper_b').css('left', left_o+'px').animate({left:0},1500);
						}
						$('#desktop, #wallpaper').animate({left:  -left_o, right: left_o},1500);
						$('#monLcs').animate({left: (-left_o*2), right: (left_o*2)},1500, function(){$('#monLcs iframe').attr('src', '');});
						$('#inettuts').animate({left: 0, right: 0},1500, function(){
							$('a.menu_trigger').hide('slow');
								$('#iLcsMenu').show('slow');
							$('#inettuts').load('desktop/includes/inc-inettuts.php', function(){
								iNettuts.init();
							});
						});
					} else if(($(this).text().substring(0,1)=='3')  && ($('#monLcs').position().left!=0)) {
						$('#iLcsMenu').hide();
						if($('#wallpaper_b').length == 0){
							$('#wallpaper').clone().prependTo('body').attr('id', 'wallpaper_b').css('left', 0);
						}
						$('#desktop, #wallpaper').animate({left: left_o+'px'},1500);
						$('#inettuts').animate({left: (2*left_o)+'px', right: (-2*left_o)+'px'},1500, function(){$('#inettuts').html(' ');});
						$('#monLcs').animate({left: 0, right: 0},1500, function(){
							$('#monLcs iframe').attr('src', '../monlcs/');
							$('a.menu_trigger').hide('slow');
						});
						var spaceOn='3';
					} else if(($(this).text().substring(0,1)=='1') && ($('#desktop').position().left!=0)) {
						$('#iLcsMenu').hide();
						$('#desktop, #wallpaper').animate({left: 0, right: 0},1500);
						$('a.menu_trigger').show('slow');
						$('#inettuts').animate({left: left_o+'px', right: -left_o+'px'},1500, function(){$('#wallpaper_b').remove();});
						$('#monLcs').animate({left: -left_o+'px',right: left_o+'px'},1500, function(){$('#monLcs iframe').attr('src', '');});
					}
					$('#otBuro_2').text($(this).text().substring(0,1));
//					alert('toto'+$(this).text().substring(0,1));
					$('#otBuro_1 li a').each(function(){
						$(this).addClass('nospace').removeClass('space').addClass('active');
					});
					$(this).addClass('space').removeClass('nospace').addClass('active');
					$('#otBuro_1 ul').slideUp('fast');
					return;
				}
			});
			
			
			//***********
			// FIN TESTS
			//*********
			
			
			// on init le login
			var login = $('#jqd_login').text();
//			alert('login');

			// Start clock.
			JQD.init_clock();
			
			// Cancel mousedown, right-click.
			$(document).mousedown(function(ev) {
				if (!$(ev.target).closest('a, input, textarea').length) { //.: LCS :. on autorise aussi le focus dans les input et textarea
					
					JQD.clear_active();
					return false;
				}
			}).bind('contextmenu', function() {
				return false;
			});

			// Relative or remote links?
			$('a').click(function() {
				var url = $(this).attr('href');
				this.blur();

				if (url.match(/^#/)) {
					return false;
				}
				else if (url.match('://')) {
					$(this).attr('target', '_blank');
					return true;
				}
			});

			// Make top menus active.
			$('a.menu_trigger').mousedown(function() {
				if ($(this).next('ul.menu').is(':hidden')) {
					JQD.clear_active();
					$(this).addClass('active').next('ul.menu').show();
				}
				else {
					JQD.clear_active();
				}
			}).mouseenter(function() {
				// Transfer focus, if already open.
				if ($('ul.menu').is(':visible')) {
					JQD.clear_active();
					$(this).addClass('active').next('ul.menu').show();
				}
			});

			// Desktop icons.
			$('a.icon').draggable({
				cancel: 'a.ui-icon',// clicking an icon won't initiate dragging
				revert: false, // when not dropped, the item will revert back to its initial position
				containment: $('#desktop, .trash'), // stick to demo-frame if present
				//helper: 'clone',
				cursor: 'move'
			}).mousedown(function() {
				// Highlight the icon.
				JQD.clear_active();
				$(this).addClass('active');
			}).dblclick(function() {
				// Get the link's target.
				JQD.init_link_open_win(this);
			});

			// .:LCS:.
			//add window open on simple-clic on link of menu bar 
			$('#user_infos ul li a, ul.found li a, a.open_win').click(function() {
				JQD.init_link_open_win(this);
				return false;
			});
			
			// ***LCS*** Save params of prefs
			$('#valid_prefs, #alert_save_prefs').click(function(){
				$("#ticket_prefs").attr('value', 1);
					JQD.save_xml('lcs_buro', JQD.jqd_build_xml());
					$('#alert_save_prefs').show().html('Enregistrement effectu&eacute; ! ').addClass('saved');
					setTimeout(function(){$('#alert_save_prefs').removeClass('saved').hide('slow');},5000)
				});
				
			// Taskbar buttons.
			$('#dock a').live('click', function() {
				// Get the link's target.
				var x = $($(this).attr('href'));

				// Hide, if visible.
				if (x.is(':visible')) {
					x.hide();
				}
				else {
					// Bring window to front.
					JQD.window_flat();
					x.show().addClass('window_stack');
				}

				// Stop the live() click.
				this.blur();
				return false;
			});

			//.:LCS:. Make windows movable.
			// voir la fonction plus haut
			JQD.make_win_move();


			$('table.data').each(function() {
				// Add zebra striping, ala Mac OS X.
				$(this).find('tr:even td').addClass('zebra');
			}).find('tr').live('click', function() {
				// Highlight row, ala Mac OS X.
				$(this).closest('tr').addClass('active');
			});

			// Add wallpaper last, to prevent blocking.
			$('body').prepend('<img id="wallpaper" class="abs" src="desktop/images/misc/Colorful.jpg" />');
			// .:LCS:. 
			$("#vign_wlpper").attr('src', $('#wallpaper').attr('src'));
			
			
			// .:LCS:.  Change wallpaper
		    $("#select_walppr").change(function() {
		        var src = $("option:selected", this).val();
		        $("#vign_wlpper").attr('src', "desktop/images/misc/"+ src);
		    });
			$('a#ch_wlppr').click(function(){
				$('#wallpaper').attr('src', "desktop/images/misc/"+$('#select_walppr').val());
			});

			// .:LCS:.  Change icons larger
		    $("#icons_larger").change(function() {
		        var large = $("option:selected", this).val();
		        $("#vign_icon").css({ width: large+"px", height : large+"px" }).attr({width: large+"px", height: large+"px"});
		    });
			$('a#ch_icons_larger').click(function(){
				$('a.icon img').css('width', $('#icons_larger').val()+'px').css('height', $('#icons_larger').val()+'px');
			});
			

			// .:LCS:.  reposition icons and other babioles on window resize
			$(window).resize(function() {
			 	JQD.init_icons();
				p = $('#user_info_bar_btn').position().left;
				$('#infos_user_panel').animate({left : parseInt(p)+'px'});
				$('#user_infos').animate({left : parseInt(p)+'px'});
			});
			// Idem on change icon's field height
			$('#ch_icons_field_height').click(function() {
			 	JQD.init_icons();
			});
			
				
			// .:LCS:.  on attend 1,5s que le xml soit charge 
			//pour apliquer la conf sur les champs du form de conf
			 setTimeout(function(){
				var img_name = $('#wallpaper').attr('src').replace('desktop/images/misc/','').replace('.jpg','');
				 $("#select_walppr option").each(function(){
				 	if($(this).val() == img_name ) $(this).attr('selected', 'selected');
				 });
				 $("#icons_larger option").each(function(){
				 	if($(this).val() == $('a.icon img').css('width').replace('px','')) $(this).attr('selected', 'selected');
				 });
				 JQD.init_icons();
			 },500);
	
			//
			//.: LCS :. trash Corbeille
			//			
			$(".trash, ").droppable({
				accept: 'a.abs.icon',
				activeClass: 'ui-state-highlight',
			    over: function(event, ui){
			        $(this).addClass("hover");
			        ui.draggable.animate({opacity:.3},100);
			    },
			    out: function(event, ui){
			        $(this).removeClass("hover");
			        $(this).text("Corbeille");
			        ui.draggable.animate({opacity:1},100);
			    },
				drop: function(ev, ui) {
			        $(this).removeClass("hover");
					deleteIcon(ui.draggable);
					JQD.init_icons();
		            $('#alert_save_prefs').show('slow').text('Enregister');
				}
			});
			
		    $("#desktop").droppable({
		        accept: '.open_win',
		        drop: function(event, ui) { 
	                // LCS Cas particulier d'un lien provenant du panneau infos user
		        	fromwhere=$(ui.draggable).closest('ul#user_infos').length; //on verifie que le lien provient de infos user
		        	newrel=$(ui.draggable).attr('href'); //on recupere la valeeur du lien
//		        	alert(fromwhere +' | '+newrel);
		        	
	        		img_w=$(this).find('a.abs.icon img').last().width();
	                $(this).find('a.abs.icon').last().after($(ui.draggable).clone());
					JQD.clear_active();
	                $("#desktop .open_win").addClass("abs").addClass("icon");
	                // LCS si le lien provient du panneau infos user
		        	if(fromwhere==1) $('#desktop a.abs.icon').last().attr('rel',newrel).attr('href', '#icon_dock_lcs_annu');
	                $("#desktop .icon").removeClass("ui-draggable open_win").dblclick(function() {
						// Get the link's target.
						JQD.init_link_open_win(this);
					}).draggable({
						cancel: 'a.ui-icon',// clicking an icon won't initiate dragging
						revert: false, // when not dropped, the item will revert back to its initial position
						containment: $('#desktop, .trash'), // stick to demo-frame if present
//						helper: 'clone',
						cursor: 'move'
					}).mousedown(function() {
						// Highlight the icon.
						JQD.clear_active();
						$(this).addClass('active');
					}).find('img').width(img_w).height(img_w).css({'width': img_w, 'height': img_w});
		            JQD.init_icons();
		            $('#alert_save_prefs').show('slow').text('Enregistrer');
		        }
		    });
		    
		    $(".open_win").draggable({
		        helper: 'clone'
		    });

			// fonction supprimer icone
			function deleteIcon($item) {
				$item.addClass('icon_trash')
				.removeClass('abs')
				.fadeOut().remove();
			}

			// fonction restaurer icone
			function restorIcon($item) {
				$item.removeClass('icon_refresh').addClass('abs').fadeOut(function() {
					$item.find('a.ui-icon-refresh').remove();
					$item.css('width','80px')
						.animate({ fontSize: '11px',opacity: 1 })
						.find('img')
						.animate({width:$("#icons_larger").val()+'px',height:$("#icons_larger").val()+'px'})
						.end().appendTo('#desktop').fadeIn();
				});
				setTimeout(function(){ JQD.init_icons(); },500);
			}
			
			 // .: LCS :. infos user panel
			 $('#user_info_bar_btn').toggle(
			 	function(){
			 		$('#user_infos').show(1000); 
			 	},
			 	function(){
			 		$('#user_infos').hide(1000); 
			 	}
			 );
			 
			 JQD.initDrop();
			 
			 JQD.init_link_open_win();
		}
	};
// Pass in jQuery.
})(jQuery);

