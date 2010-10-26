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
			var clock_date = day + ' ' + month + ' ' + year;

			// Shove in the HTML.
			$('#clock').html(clock_time).dblclick(function() {
				$(this).attr('title', 'agendas');
				JQD.init_link_open_win(this);
				setTimeout($(this).removeAttr('title'), 1000);
				return false;
			}).hover(function() {
				setTimeout($('#date').html(clock_date).show(500), 500);
			},
			function() {
				$('#date').html(clock_date).hide(500);
			});

			// Update every 60 seconds.
			setTimeout(JQD.init_clock, 60000);
		},

		//
		// Clear active states, hide menus.
		//
		clear_active: function() {
			$('a.active, tr.active').removeClass('active');
			$('ul.menu,#otBuro_1 ul').hide();
		},

		//
		// Zero out window z-index.
		//
		window_flat: function() {
			$('div.window, #user_infos').removeClass('window_stack');
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
				//show bottom-bar
				win.find('div.window_bottom').show();
				$('div.window_content').height()
				win.find('div.window_content, iframe').css('height', '');
				//var winheight=win.find('.window_content').height();
				//win.find('iframe').css('height',(  100% -4));
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
				//hide bottom-bar
				win.find('div.window_bottom').hide();
				var winheight=win.find('.window_content').height()+20;
				win.find('div.window_content').css('height', winheight)
				.find('iframe').css('height', winheight-4);
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
			var nb_icons = $('#desktop a.abs.icon').not('.launch').length;
			var t = 20;
			var w = 20;
			var s = 0;
			$('#desktop a.abs.icon').not('.launch').each(function(){
				var h_i = $(this).outerHeight(true) + 5;
				var h_d = $('#desktop').outerHeight(true);
				$('#icons_field_height').val()!='' ? h_d= (h_d * $('#icons_field_height').val() /100) : h_d= h_d/2;
				//h_d= h_d * $('#icons_field_height').val() /100;
				s +=1;
				if(t > h_d-h_i ){ t = 20; w += 100};
//				if(s == 6 ){ t = 20; w += 125};
				$(this).css({'top': t,'left': w});
				t += h_i;
			});
		},
		// fonction supprimer icone
		deleteIcon: function ($item) {
			$item.addClass('icon_trash')
			.removeClass('abs')
			.fadeOut().remove();
		},

		place_wallpaper: function(){
			var w = $('#desktop').width();
			var h = $('#desktop').height();
			var x = $('#pos_walppr').val();
			$('#wallpaper').removeClass().addClass('abs '+ x).removeAttr('style');
			if(x.match('center_h')){   l_wp = (w-$('#wallpaper').width())/2;$('#wallpaper').css({'left':l_wp+'px'}); }
			if(x.match('center_v')){   l_hp = (h-$('#wallpaper').height())/2;$('#wallpaper').css({'top':l_hp+'px'}); }
		},
		
		//
		// .:LCS:.  init window move. transform in function to call easyer
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
				$(this).closest('div.window').hide().find('iframe').attr('src',' ');

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
				$(this).closest('div.window').hide().find('iframe').attr('src',' ');
				$($(this).attr('href')).removeClass('bar-bottom-icon').hide('fast');
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
		user_load_prefs: function () {
			// src image wallpaper
			$('#tmp_wallpaper').length ==1 ? $('#wallpaper').attr('src',$('#tmp_wallpaper').val().replace('thumbs/','')) : '';
			if( $('#tmp_poswp').length ==1 ) { $('#wallpaper').removeClass().addClass($('#tmp_poswp').val()) ;
			$('#pos_walppr').attr('value',$('#tmp_poswp').val());
			//JQD.place_wallpaper();
			}
			
			if( $('#tmp_bgcolor').length==1 ) { 
				$('body').css('background-color', $('#tmp_bgcolor').val()); $('#wp_bgcolor').attr('value',$('#tmp_bgcolor').val()); 
			}
			// taille des icones
			w = $('#tmp_iconsize').val();
			$("a.icon img, #vign_icon").css({ width: w +"px", height : w +"px" })
			.attr({width: w +"px", height: w +"px"});
			// affichage du dock
			//$('#tmp_quicklaunch').val()=='1' ? $('#aff_quicklaunch').attr('checked', 'checked') : $('#aff_quicklaunch').attr('checked', ' ') ;
			$('#tmp_quicklaunch').val()=='1' ? $('#aff_quicklaunch').attr('checked', 'checked') : $('#aff_quicklaunch').removeAttr('checked') ;
			$('#icons_field_height').attr('value',$('#tmp_iconsfield').val());
			!$('#tmp_wallpaper').length? JQD.load_prefs_img('core/images/misc/RayOfLight_lcs.jpg'):'';
			JQD.init_icons();
		},
		//
		//
		//
		load_prefs_img: function(t_img) {
				//var t_img=$(this).attr('src');
//				$('#ch_wlppr').show();
				$('#vign_wlpper').attr('src',t_img);
//				$('#select_walppr').attr('value',t_img.replace('core/images/misc/', '').replace('.jpg', ''));
				$('#select_walppr').attr('value',t_img);
				$.ajax({
					type: "POST",
					url: "core/action/get_metas_exif_img.php",
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
					},
					error: function(){
					},
					complete : function(data, status) {
					}
				});	
			 	JQD.init_icons();

		},

		//
		// .:LCS:. Build xml to save preferences
		//
		jqd_build_xml: function(suser) { 
			var str = "" ;
			//str = str + "<?xml version='1.0' encoding='utf-8'?>\r\n" ;
				str = str + "<bureau>" + "\r\n" ;
				str = str + "\t"+"<userburo id='" + $("#login").val() +"' name ='" + $("#login").val() +"' ticket='" + $("#ticket_prefs").val() +"_"+"' class=''>" + "\r\n" ;
				str = str + "\t\t"+"<wallpaper>" + $("#select_walppr").val() +"</wallpaper>" + "\r\n" ;
				str = str + "\t\t"+"<pos_wallpaper>" + $("#pos_walppr").val() +"</pos_wallpaper>" + "\r\n" ;
				str = str + "\t\t"+"<iconsize>" + $("#icons_larger").val() +"</iconsize >" + "\r\n" ;
				str = str + "\t\t"+"<iconsfield>" + $("#icons_field_height").val() +"</iconsfield>" + "\r\n" ;
				str = str + "\t\t"+"<bgcolor>" + $("#wp_bgcolor").val() +"</bgcolor>" + "\r\n" ;
				$('#desktop').find('a.icon').each(function(){
					str = str + "\t\t"+"<icon>" + "\r\n" ;
					str = str + "\t\t\t"+"<icontext>"+ $(this).text() +"</icontext>" + "\r\n" ;	
					strlink = $(this).attr('rel');			
					str = str + "\t\t\t"+"<iconurl>"+strlink.replace('&', '&amp;')+"</iconurl>" + "\r\n" ;				
					str = str + "\t\t\t"+"<iconwin>"+$(this).attr('href')+"</iconwin>" + "\r\n" ;				
					str = str + "\t\t\t"+"<icontitle>"+$(this).attr('title')+"</icontitle>" + "\r\n" ;				
					str = str + "\t\t\t"+"<iconrev>"+$(this).attr('rev')+"</iconrev>" + "\r\n" ;				
					str = str + "\t\t\t"+"<iconimg>"+$(this).find('img').attr('src')+"</iconimg>" + "\r\n" ;				
					str = str + "\t\t"+"</icon>" + "\r\n" ;
				});
				str = str + "\t\t"+"<quicklaunch>" + $("#aff_quicklaunch:checked").length +"</quicklaunch>" + "\r\n" ;
				str = str + "\t"+"</userburo>" + "\r\n" ;
				str = str + "</bureau>" + "\r\n" ;
			
			return str ;
		},

		// .:LCS:. envoi xml
		save_xml: function(file, b_xml, groups) { 
			$.ajax({
				type: "POST",
				url: "core/action/save_xml.php",
				cache: false,
				data: ({file: file+'_'+$("#login").val(),
						user: $("#login").val(),
						groups : groups,
						data : b_xml}),
				dataType: "json",
				success: function(msg){
					//JQD.create_notify("withIcon", { title:'Info', text:msg, icon:'core/images/icons/info.png'});
				},
				error: function(){
				},
				complete : function(data, status) {
					var resp = eval('('+data.responseText+')');
					 //var resp = JSON.parse( data.responseText );
					JQD.create_notify("withIcon", 
						{ title:resp['title'], text:resp['mess']+resp['infos'], icon:'core/images/icons/'+resp['img']+'.png'},
						{
						//expires:false,
						click: function(e,instance){
							window.location='./'; 
						}
					});
				}
			});	
		},

		delette_xml: function(file) { 
			$.ajax({
				type: "POST",
				url: "core/action/delette_xml.php",
				cache: false,
				data: ({file: file,
						user: $("#login").val()
						}),
				dataType: "json",
				success: function(data){
				//	alert(data);
				},
				error: function(msg){
				//	alert('error : ' + msg);
				},
				complete : function(data, status) {
					var resp = eval('('+data.responseText+')');
					 //var resp = JSON.parse( data.responseText );
					JQD.create_notify("withIcon", 
						{ title:resp['title'], text:resp['mess'], icon:'core/images/icons/'+resp['img']+'.png'},
						{
						expires:false,
						click: function(e,instance){
							window.location='./'; 
						}
					});
				}
			});	
		},

		//
		//
		// Notification
		create_notify: function( template, vars, opts ){
						// init notify container
			var notifContainer = $("#container").notify();

			return notifContainer.notify("create", template, vars, opts);
		},
		
		//
		//.:LCS:. Notification Forum_spip
		//
		notify_forum: function(){
			$.ajax({
				type:'GET',
				url:'../spip/?page=lcs-notify',
				datatype:"text",
				complete: function(data,status) {
					var newidart= $(data.responseText).find('span.spip_id_article').text();
					var oldidart=$('#s_idart').val();
					if(newidart!=oldidart){
						JQD.create_notify("withIcon", 
							{ title:'<span style="color:#509fda;">Information Forum</span>', text:data.responseText, icon:'core/images/icons/info.png'},
							{
							expires:true,
							click: function(e,instance){
								JQD.init_link_open_win('<a href="../spip/" title="Forum Lcs" rev="spip" rel="spip" class="open_win ext_link">Forum Lcs</a>');
							}
						});
						$('#s_idart').attr('value',newidart);
					}
				}
			});
			setTimeout(function(){
					JQD.notify_forum();
				//var idart=newidart;
			},50000);	
//				$('#temp_forum_notify').load('../spip/?page=lcs-notify #container_notify');
		},
		
		//
		// .:LCS:.  init window. transform in function to call easyer
		//
		init_link_open_win: function(el){
				var url = $(el).attr('href');
				var text = $(el).text();
				var title = $(el).attr('title');
				var rev = $(el).attr('rev');
				if (text == '') text = title;
				var title_win = text;
				var img='images/barre1/BP_r1_c1.gif';
				if ($(el).find('img').length > 0) img = $(el).find('img').attr('src');
				var rel =  $(el).attr('rel');
				if (rel=='') var rel = parseInt($(el).parent('li').index()+1);
				var p_left = 0;var p_top = 0;
				var nb_win = $('.window:visible').length;
				p_left = p_top = nb_win*25;
				$(el).blur();
				if (url.match(/^#/)) {
				var url = $(el).attr('rel');
				var rel = $(el).attr('rev');
				}
		
				// Get the link's class.
				if($(el).hasClass('ext_link')){
					var win_o = $('#desktop').find('#window_lcs_'+rel).length;
					if (win_o == 0){
						$('#window_lcs_temp').clone().appendTo('#desktop')
							.attr('id', 'window_lcs_'+rel)
							.find('a.window_close').attr('href','#icon_dock_lcs_'+rel)
							.parents('div.window').find('img').attr('src', img)
							.parents('div.window').find('span.window_title, .window_bottom').text(title_win);
						$('#icon_dock_lcs_temp').clone().appendTo('#dock')
							.attr('id', 'icon_dock_lcs_'+rel)
							.find('a').attr('href','#window_lcs_'+rel).text(title_win)
							.append('<img src="'+img+'" alt="" style="height:22px;" />' );
					}
					var x = $('#icon_dock_lcs_'+rel);
					var y = $('#window_lcs_'+rel);
				}else{
					rel="path";
					var x = $('#icon_dock_lcs_path');
					var y = $('#window_lcs_path');
					$('#window_lcs_path').find('span.window_title, .window_bottom').text(title_win)
					.parents('div.window').find('img').attr('src', img);
					$('#icon_dock_lcs_path a').text(title_win)
						.append('<img src="'+img+'" alt="" style="height:22px;" />' );
				}
				
				JQD.clear_active();
		
				// Show the taskbar button.
				if (x.is(':hidden')) {
					x.remove().appendTo('#dock').end().show('fast');
					// .:LCS:. prepar adptation width of dock's items
					var x_d = ($('#desktop').width() -200);
					var nb_items =  $('ul#dock li:visible').length;
					if (x.position().left > x_d){
						var w_item = (x_d/nb_items-40);
						$('ul#dock li:visible').each(function(){
							$(this).show('fast').addClass('bar-bottom-icon')
							.find('a').attr({ title: $(this).text()}).each(function(){
								$(this).find('img').attr('alt', $(this).text());
							});
						});
					}
					else {
						$(this).attr('style','').removeClass('bar-bottom-icon').find('a').attr('style','');
					}
				}
				if (y.is(':hidden')) var mov='ok_';
				// Bring window to front.
				JQD.window_flat();
				//.: LCS :. IMPORTANT iframe or no
				if(y.find('iframe').length) {
					//Agir sur les elements du iframe
					// init des liens de class open_win
						y.find('iframe').attr('src',url).load(function()  {
							el = $(this).contents();
							// Listage et modif des mailto:
							el.find('a').each(function(){
								//alert($(this)[0].href);
								$(this).removeAttr('target');
								$(this)[0].href.length > 0 ? cible=$(this)[0].href.replace('?','&') : '';
								if($(this)[0].href.match('mailto:')){
									$(this).attr({
										'href':cible.replace('mailto:','../squirrelmail/src/compose.php?send_to='),
										'rel':'squirrelmail'
									}).addClass('open_win ext_link');
								} 
								if($(this)[0].href.match('/^#/')){
									$(this).click(function(){
										alert('toto');
										//alert(el.find('head').length);
										//return false;//on inhibe le lien
									});
								} 
							});
							el.find('a.open_win').each(function(){
								$(this).click(function(){
									JQD.init_link_open_win(this);
									return false;//on inhibe le lien
								});
							});
							el.find('a:not(:visible)').each(function(){
								//alert($(this).attr('href'));
								$(this).click(function(){
									//JQD.init_link_open_win(this);
									//return false;//on inhibe le lien
								});
							});

						});
					}
				url.match('src/compose') ? y.removeClass('large_win'):'';
				//.: LCS :. On affiche au premier plan
				y.addClass('window_stack').show();
				JQD.make_win_move();
				// .:LCS:.  on repositionne la fenetre
				if (mov=='ok_') {
					y.not('.window_full').animate({'top': '+='+p_top, 'left' : '+='+p_left});
					p_left = 0;p_top = 0;
				}
				setTimeout(function(){
					$('ul#dock li:visible.bar-bottom-icon a').each(function(){
						$(this).hover(function(e){
							t = $(this).attr('title');
							var offset = $(this).closest('ul').offset();
							var lft = parseInt($(this).parent('li').position().left);
							$("#desktop").append("<p id='screenshot' class='abs'></p>").find('#screenshot').html(t);  
							var wtip = $("#screenshot").width()/2;
							$("#screenshot")
								.css("bottom","5px")
								.css("left",(lft  - wtip+12) + "px")
								.fadeIn("fast");                                              
						},function(){$("#screenshot").remove();});
					});
			},500);
			JQD.make_win_move();
			return false;
		 },


		//###############
		// Initialize the desktop.
		//###############
		init_desktop: function() {
			if (window.location !== window.top.location) {
				window.top.location = window.location;
			}

			// gestion des bureaux secondaires // A REVOIR ENTIEREMENT
			var left_o = $('#desktop').width();
			$('#inettuts').animate({left: left_o+'px'}).hide();
			$('#monLcs').animate({left: -left_o+'px',right: left_o+'px'}).hide();

			$('#otBuro_2').click(function(){
			$('#otBuro_1 ul').show('fast');
			});
			$('#otBuro_1 li a').click(function(){
				if($(this).not('.space')){
					$('#inettuts, #monLcs').show();
					var ind=$(this).parent().index();
					/*if((ind==1) && ($('#inettuts').position().left!=0)){
						if($('#wallpaper_b').length == 0){
							$('#wallpaper').clone().prependTo('body').attr('id', 'wallpaper_b').css('left', left_o+'px').animate({left:0},1500);
						}
						$('#desktop, #wallpaper').animate({left:  -left_o, right: left_o},1500);
						$('#monLcs').animate({left: (-left_o*2), right: (left_o*2)},1500, function(){$('#monLcs iframe').attr('src', '');});
						$('#inettuts').animate({left: 0, right: 0},1500, function(){
							$('a.menu_trigger').hide('slow');
								$('#iLcsMenu').show('slow');
							$('#inettuts').load('core/includes/inc-inettuts.php', function(){
								iNettuts.init();
							});
						});
					} else */if((ind==2)  && ($('#monLcs').position().left!=0)) {
						$('#iLcsMenu').hide();
						if($('#wallpaper_b').length == 0){
							$('#wallpaper').clone().prependTo('body').attr('id', 'wallpaper_b').css('left', 0);
						}
						$('#desktop, #wallpaper').animate({left: left_o+'px'},1500);
						$('#inettuts').animate({left: (2*left_o)+'px', right: (-2*left_o)+'px'},1500, function(){$('#inettuts').hide();});
						$('#monLcs').show().animate({left: 0, right: 0},1500, function(){
							$('#monLcs iframe').attr('src', '../monlcs/');
							$('a.menu_trigger').hide('slow');
						});
						var spaceOn='3';
					} else if((ind==0) && ($('#desktop').position().left!=0)) {
						$('#iLcsMenu').hide();
						$('#desktop, #wallpaper').animate({left: 0, right: 0},1500);
						$('a.menu_trigger').show('slow');
						$('#inettuts').animate({left: left_o+'px', right: -left_o+'px'},1500, function(){$('#wallpaper_b').remove();$('#inettuts').hide();});
						$('#monLcs').animate({left: -left_o+'px',right: left_o+'px'},1500, function(){$('#monLcs iframe').attr('src', '');});
					}
					$('#otBuro_2').text($(this).text().substring(0,1));
					$('#otBuro_1 li a').each(function(){
						$(this).addClass('nospace').removeClass('space').addClass('active');
					});
					$(this).addClass('space').removeClass('nospace').addClass('active');
					$('#otBuro_1 ul').slideUp('fast');
					return;
				}
			});
			
			// 
			//.:LCS:. Quicklaunch (Dock MacOs)
			//
			var x_d = ($('#desktop').width() - $('#quicklaunch').width())/2;
			$('#quicklaunch').css("left", x_d+"px").each(function() {
				$.each($(this).find('li a'), function() {
					
					var x = parseInt($(this).width());
					var y = parseInt($(this).height());
							
					$(this).data("init_w", x);
					$(this).data("init_h", y);
					$(this).data("new_w", x * 2);
					$(this).data("new_h", y * 2);
				});
				$(this).find('a').hover(function(e){
//				t = $(this).attr('title');
//				$(this).attr('title','');
				var offset = $(this).closest('ul').offset();
				var nel = parseInt($(this).parent('li').index());
//				var c = (this.t != "") ? "<br/>" + this.t : "Pas d'info";
				$("#desktop").append("<p id='screenshot' class='abs'>"+ this.title +"</p>");  
				var wtip = $("#screenshot").width()/2;
				$("#screenshot")
					.css("bottom","54px")
					.css("left",(parseInt(offset.left) + (30*nel) - wtip+12) + "px")
					.fadeIn("fast");                                              
				},
				function(){
//				$(this).attr('title',t);
					$("#screenshot").remove();
				}).bind("mouseenter",function(event) {
					$(this).animate({"width": $(this).data("new_w") + "px", "height": $(this).data("new_h") + "px"},"fast");
				}).bind("mouseleave",function(event) {
					if($(this).width()==$(this).data("new_w")){
						$(this).animate({"width": $(this).data("init_w") + "px", "height": $(this).data("init_h") + "px"},"fast");
					}else{
						$(this).stop().width($(this).data("init_w")).height($(this).data("init_h"));
					}
				});
			});
			
			// on init le login
			var login = $('#jqd_login').text();

			// Start clock.
			JQD.init_clock();
			
			// Cancel mousedown, right-click.
			$(document).mousedown(function(ev) {
				//.: LCS :. on autorise aussi le focus dans les input et textarea
				if (!$(ev.target).closest('a, input, textarea','select','select option','form').length) { 					
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
			$('a.open_win').bind('click', function() {
				JQD.init_link_open_win(this);
				return false;
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

			// ***LCS*** Save params of prefs
			$('#valid_prefs').click(function(){
				$("#ticket_prefs").attr('value', 1);
				JQD.save_xml('lcs_buro', JQD.jqd_build_xml());
			});
			
			// remove pref
			$('#delete_prefs').click(function(){
				JQD.delette_xml('lcs_buro_'+$("#login").val());
			});
				
			//.:LCS:. Make windows movable.
			// voir la fonction plus haut
			JQD.make_win_move();

			// background table odd even
			$('table.data').each(function() {
				// Add zebra striping, ala Mac OS X.
				$(this).find('tr:even td').addClass('zebra');
			}).find('tr').live('click', function() {
				// Highlight row, ala Mac OS X.
				$(this).closest('tr').addClass('active');
			});
			
			//
			// pannel up/down
			$('.triangle_updown').toggle(function(){
				$(this).addClass('down').next('.block_updown').show();
			},function(){
				$(this).removeClass('down').next('.block_updown').hide();
			});
			$('.btn_groups.triangle_updown').trigger('click');


			
			// Add wallpaper last, to prevent blocking.
			$('#tmp_wallpaper').val() ? x=$('#tmp_wallpaper').val()  : x="core/images/misc/RayOfLight_lcs.jpg";
			//alert($('#tmp_wallpaper').val());
			$('body').prepend('<img id="wallpaper" class="abs wallpaper" src="'+ x +'" />');
			// .:LCS:. 
			$("#vign_wlpper").attr('src', $('#wallpaper').attr('src'));
			
			
			// .:LCS:.  Change wallpaper
			$('#ch_wlppr').click(function(){
				$('#listImgs,#ch_wlppr').hide();
				$('#wallpaper').attr('src',$('#select_walppr').val().replace('thumbs/',''));
			});
			$('#ch_pos_wlppr').click(function(){
				JQD.place_wallpaper();
			});
			$('#ch_bgcolor').click(function(){
				$('body').css('background-color', $('#wp_bgcolor').val());
			});

			$(window).resize(function() {
				JQD.place_wallpaper();
			});

			// .:LCS:. colorPicker (Farbtastic)
		    var f = $.farbtastic('#picker');
		    var p = $('#picker');
		    $('#ctn_picker').hide();
		    var selected;
		    $('.colorwell')
		      .each(function () { 
		      	f.linkTo(this);
		      	 $(this).css('opacity', 0.75); 
		      })
		      .focus(function() {
		        if (selected) {
		          $(selected).css('opacity', 0.75).removeClass('colorwell-selected');
		        }
		        f.linkTo(this);
		      	$('#ctn_picker').show();
		        p.css('opacity', 1);
		        $(selected = this).css('opacity', 1).addClass('colorwell-selected');
		      })
		      .blur(function() {
		      	 	$('#ctn_picker').hide();
		      });
		    $('#close_picker').click(function(){$('#ctn_picker').hide();$('.colorwell').blur();});
		      
			// .:LCS:.  Change icons larger
		    $("#icons_larger").change(function() {
		        var large = $("option:selected", this).val();
		        $("#vign_icon").css({ width: large+"px", height : large+"px" }).attr({width: large+"px", height: large+"px"});
		    });
		    $('.span_icon_prefs').click(function(){
		    	$('.span_icon_prefs').removeClass('selected');
		    	$(this).addClass('selected');
		    	$('#icons_larger').attr('value',$(this).children('img').width());
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
			
			// Notification
			// create notify welcome
			JQD.create_notify("default", { title:'Bienvenue sur Lcs-Bureau', text:'D&eacute;couvrez une nouvelle interface pour LCS. <br />Bonne nav...'});
				
			// .:LCS:.  on attend 1,5s que le xml soit charge 
			//pour apliquer la conf sur les champs du form de conf
			 setTimeout(function(){
				var img_name = $('#wallpaper').attr('src').replace('core/images/misc/','').replace('.jpg','');
				 $("#select_walppr option").each(function(){
				 	if($(this).val() == img_name ) $(this).attr('selected', 'selected');
				 });
				 $('.span_icon_prefs').each(function(){
				 	$(this).find('img').css('width').replace('px','')==$('#tmp_iconsize').val()?$(this).addClass('selected'):'';
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
			        $(this).addClass("hover").find('h3').text("Corbeille");
			        ui.draggable.animate({opacity:.3},100);
			    },
			    out: function(event, ui){
			        $(this).removeClass("hover").find('h3').text("");
			        ui.draggable.animate({opacity:1},100);
			    },
				drop: function(ev, ui) {
			        $(this).removeClass("hover").find('h3').text('');
					JQD.deleteIcon(ui.draggable);
					JQD.init_icons();
					JQD.create_notify("withIcon", 
						{ title:'Enregistrer vos pr&eacute;f&eacute;rences', text:"Cliquez moi pour enregistrer vos pr&eacute;f&eacute;rences...", icon:'core/images/icons/alert.png'},
						{
						expires:false,
						click: function(e,instance){
							JQD.save_xml('lcs_buro', JQD.jqd_build_xml());
							instance.close();
						}
					});
				}
			});
			
		    $("#desktop").droppable({
		        accept: '.open_win',
		        drop: function(event, ui) { 
	                // LCS Cas particulier d'un lien provenant du panneau infos user
	                //on verifie que le lien provient de infos user
		        	fromwhere=$(ui.draggable).closest('ul#user_infos').length; 
		        	newrel=$(ui.draggable).attr('href'); //on recupere la valeeur du lien		        	
	        		img_w=$(this).find('a.abs.icon img').last().width();
	                $(this).find('a.abs.icon').last().after($(ui.draggable).clone());
					JQD.clear_active();
	                $("#desktop .open_win").not('.launch').addClass("abs").addClass("icon");
	                // LCS si le lien provient du panneau infos user
		        	if(fromwhere==1) $('#desktop a.abs.icon').last().attr({'rel':newrel,'href': '#icon_dock_lcs_annu', 'title':'annu'});
	                $("#desktop .icon").removeClass("ui-draggable open_win").dblclick(function() {
						// Get the link's target.
						JQD.init_link_open_win(this);
					}).draggable({
						cancel: 'a.ui-icon',// clicking an icon won't initiate dragging
						revert: false, // when not dropped, the item will revert back to its initial position
						containment: $('#desktop, .trash'), // stick to demo-frame if present
						//helper: 'clone',
						cursor: 'move'
					}).mousedown(function() {
						// Highlight the icon.
						JQD.clear_active();
						$(this).not('.launch').addClass('active');
					}).find('img').not('.quicklaunch').width(img_w).height(img_w).css({'width': img_w, 'height': img_w});
		            JQD.init_icons();
		           	JQD.create_notify("withIcon", { title:'Enregistrer vos pr&eacute;f&eacute;rences', text:"L'ic&ocirc;ne "+$(ui.draggable).text() + " a &eacute;t&eacute; ajout&eacute;e sur le bureau.<br />Cliquez moi pour enregistrer vos pr&eacute;f&eacute;rences...", icon:'core/images/icons/info.png'},
						{
						expires:false,
						click: function(e,instance){
							JQD.save_xml('lcs_buro', JQD.jqd_build_xml());
							instance.close();
						}
					});

		        }
		    });
		    
		    $(".open_win").draggable({
		    	delay: 1000, // pas de dragg sur un click
		        helper: 'clone'
		    });

			 // .: LCS :. infos user panel
			$('#user_info_bar_btn').click(function(){
				JQD.window_flat();
				$('#user_infos').addClass('window_stack').show(1000).click(function(){
				JQD.window_flat();
				$(this).addClass('window_stack');
					return false;
				}); 
			});
			//
			// .:LCS:. init button close
			$('span.close').click(function(){
				$(this).closest('.toClose').hide();
			});
			
			//.:LCS:. Init forum notification
			var idart=0;
			
			//.:LCS:. preferences user
			 JQD.user_load_prefs();
//			 JQD.init_link_open_win();
		}
	};
// Pass in jQuery.
})(jQuery);

