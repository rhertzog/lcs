/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* @jquery.desktop.js 
* base sur jquery.desktop de Nathan Smith
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version 0.2~14
* Derniere mise a jour : 28/11/2010
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/

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
			$('#date span.date').html(clock_date);
			$('#clock').html(clock_time).dblclick(function() {
				$(this).attr('title', 'agendas');
				JQD.init_link_open_win(this);
				setTimeout($(this).removeAttr('title'), 1000);
				return false;
			}).mouseenter(function() {
				$('#date').show(500);
			}).mouseleave(function() {
				$(this).next('#date').hide(500);
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
				$('#tmp_iconsfield').val()!='' ? h_d= (h_d * $('#tmp_iconsfield').val() /100) : h_d= h_d/2;
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

		// position du wallpaper
		place_wallpaper: function(){
			var w = $('#desktop').width();
			var h = $('#desktop').height();
			var x = $('#tmp_poswp').val();
			$('#wallpaper').removeAttr('style').removeClass().addClass($('#tmp_poswp').val());
			if(x.match('center_h')){   l_wp = (w-$('#wallpaper').width())/2;$('#wallpaper').css({'left':l_wp+'px'}); }
			if(x.match('center_v')){   l_hp = (h-$('#wallpaper').height())/2;$('#wallpaper').css({'top':l_hp+'px'}); }
		},
		
		//
		// .:LCS:. Load xml preferences
		//
		user_load_prefs_dev: function () {
			$.getJSON("core/json/PREFS_TEST_admin.json", function(prefs) {
			//	alert("data: " + prefs.bureau.userburo.data);
			});
		},
		
		user_load_prefs: function () {
			//couleur d'arrierre-plan
			$('body').css('background', $('#tmp_bgcolor').val()); 
			// taille des icones
			w = $('#tmp_iconsize').val();
			$("a.icon img, #vign_icon").css({ width: w +"px", height : w +"px" })
			.attr({width: w +"px", height: w +"px"});
			// affichage du dock
			$('#icons_field_height').attr('value',$('#tmp_iconsfield').val());
			JQD.init_icons();
			// Add wallpaper last, to prevent blocking.
			$('#tmp_wallpaper').val() ? x=$('#tmp_wallpaper').val()  : x="core/images/misc/RayOfLight_lcs.jpg";
			$('#tmp_wallpaper').val().match('~') ? x=x.replace('core/','') : '';
			//alert($('#tmp_wallpaper').val());
			$('body').prepend('<img id="wallpaper" class="abs wallpaper" src="'+ x.replace('thumbs/','') +'" />');
			$('#wallpaper').removeClass().addClass($('#tmp_poswp').val()) ;
		},

		// Suppression fichier
		delete_xml: function(file) { 
			$.ajax({
				type: "POST",
				url: "core/action/delete_xml.php",
				cache: false,
				data: ({
					file: file,
					user: $("#login").val()
				}),
				dataType: "json",
				complete : function(data, status) {
					var resp = eval('('+data.responseText+')');
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
				url:'core/action/load_spip_notify.php',
				datatype:"text",
				complete: function(data,status) {
					var n_idart= $(data.responseText).find('span.forum_date').text();
					var oldidart= $('#s_idart').val();
					var newidart_a= n_idart.split(' - ');
					var newidart = parseInt( newidart_a[0].replace(/-/g, '') + newidart_a[1].replace(/:/g, ''));
					//alert ( 'newidart: '+newidart+' \noldidart: '+oldidart); 
					if(newidart > oldidart){
						JQD.create_notify("withIcon", 
							{ title:'<span style="color:#509fda;">Information Forum</span>', text:data.responseText, icon:'core/images/icons/info.png'},
							{
							expires:true,
							open: function(e,instance){
								//alert($('#notify_container').length);
								$('#notify_container').find('a').each(function(){
									$(this).remove();
								});
							},
							click: function(e,instance){
								JQD.init_link_open_win('<a href="../spip/" title="Forum Lcs" rev="spip" rel="spip" class="open_win ext_link">Forum Lcs</a>');
							}
						});
						$('#s_idart').attr('value',newidart);
						JQD.save_prefs_dev('PREFS', -1, 'lkhlm');
					}
				}
			});
			
			setTimeout(function(){
					JQD.notify_forum();
				//var idart=newidart;
			},300000);	
		},
		
		//
		//__/__/sauvegarde des prefs - fichier /home/[user]/Profile/PREFS_[user].xml__/__/
		//
		save_prefs_dev: function(file, b_xml, groups) { 
			var all_icons='';
			var optionIcons = [];
			$('#desktop a.icon').each(function() { 
				el_a=$(this);
				optionIcons.push({
					"icontext" 	: el_a.text(),
					"iconurl" 	: el_a.attr('rel'),
					"iconwin" 	: el_a.attr('href'),
					"icontitle" : el_a.attr('title'),
					"iconrev" 	: el_a.attr('rev'),
					"iconimg" 	: el_a.find('img').attr('src')
				}) 
			});

			$.ajax({
				type: "POST",
				url: "core/action/save_prefs.php",
				cache: false,
				data: ({
					file          : file+'_'+$("#login").val(),
					user          : $("#login").val(),
					wallpaper     : $("#tmp_wallpaper").val(),
					pos_wallpaper : $("#tmp_poswp").val(),
					icons         : optionIcons,
					iconsize      : $("#tmp_iconsize").val(),
					iconsfield    : $("#tmp_iconsfield").val(),
					bgcolor       : $("#tmp_bgcolor").val(),
					quicklaunch   : $("#tmp_quicklaunch").val(),
					s_idart       : $("#s_idart").val(),
					winsize       : $("#tmp_winsize").val(),
					data          : b_xml
				}),
				dataType: "json",
				success: function(msg){
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					//alert("XMLHttpRequest="+XMLHttpRequest.responseText+"\ntextStatus="+textStatus+"\nerrorThrown="+errorThrown);
				},
				complete : function(data, status) {
					if( status.match('error') ){
						alert("Erreur lors de l'enregistrement"); 
						return; 
					}
					var r = JSON.parse( data.responseText );
					// Important!
					$('#wallpaper').remove();
					// A REVOIR ouvrir un panneau d'infos ? mais ou ?
					//alert( r.bureau.userburo.data );
					var pos_rr=$('ul.bar_top_right').offset().left+30;
					var respMessage= $('<div/>').addClass('respform abs').css({top:'5px', right:'5px'}).html('Vos pr&eacute;f&eacute;rences sont enregistr&eacute;es').prependTo('#desktop').show('slow');
					
					setTimeout(function(){
						$('div.respform').hide('slow').remove();
					},5000);
					// on recharge les prefs
					JQD.user_load_prefs();
						
				}
			});	
		},

		
		//
		//__/__/ Construction de l'objet fenetre __/__/
		//
		buildwin: function(url, text, title, imgsrc) {
			
			var win = 'win-'+Math.random().toString().substring(2);
			var nb_win = $('.window:visible').length;
			p_left = p_top = nb_win*25;
			if(!imgsrc || imgsrc=="") imgsrc='core/images/icons/desktop_24.png';
		
			// Construction de la fenetre
			var self      = this;
			this.img      = $('<img />');
			this.title    = title;
			this.min      = $('<a href=\"#\"/>');
			this.resize   = $('<a href=\"#\"/>');
			this.close    = $('<a href=\"#icon_dock_lcs_'+win+'\"/>')
			this.top_l    = $('<span class=\"float_left\"/>');
			this.top_r    = $('<span class=\"float_right\"/>');
			this.top      = $('<div/>');
		
			this.ctnt     = $('<div/>');
			this.main     = $('<div/>');
			this.iframe   = $('<iframe src="" name="ifr_lcs_'+win+'" src=\"" id="iframe_lcs_'+win+'"/>');
			this.spinn    = $('<div class="lcspinner" id="lcspinner"/>');
		
			this.bttm     = $('<div class=\"abs window_bottom\"/>');
		
			this.inner    = $('<div/>');
		
			this.icondock =  $('<li id="icon_dock_lcs_'+win+'">') //onglet de la barre des taches
			.append(
				$('<a/>').attr('href', '#window_lcs_'+win)
				.html(title)
				.prepend($('<img/>').attr('src',imgsrc).css('width', '22px'))
				.click(function() {
					var x = $($(this).attr('href')); // on recupere .
					if (x.is(':visible')) {
						x.hide();
					}
					else {
						JQD.window_flat();
						x.show().addClass('window_stack');
					}
					this.blur();
					return false;
				})
			);
			
			this.navigate =  $('<div/>').addClass('float_left').css({position: 'relative'})
				.append($('<input type="text"/>').attr({id: 'wp_url', name: 'wp_url'}))
				.append(
					$('<img/>').attr('src', 'core/images/gui/arrow-right-blue_16.png')
					.css({position:'absolute',top:'3px',right:'-2px',display:'none'})
				);
			// on inhibe les menus ouverts ?? 
			// Utilite a controler
			JQD.clear_active();
			
			// la fenetre
			this.win = $('<div id="window_lcs_'+win+'"/>').addClass('abs window') // la fenetre
				.append(this.inner.addClass('abs window_inner')
					.append(this.top.addClass('window_top').dblclick(function() {JQD.window_resize(this);})
						.append(this.top_r
							.append(this.min.addClass('window_min').click(function() {
								$(this).closest('div.window').hide();
							}))
							.append(this.resize.addClass('window_resize').click(function(e) {
								JQD.window_resize($(this));
							}))
							.append(this.close.addClass('window_close').click(function() {
								$(this).closest('div.window').remove();
								$('#icon_dock_lcs_'+win).remove();
								return false;
							}))
						)
						.append(this.top_l
							// le titre de la fenetre est le title de la balise a
							.text(title)
							.prepend(this.img.attr('src', imgsrc).css('width','16px')
								.dblclick(function() {
									// Traverse to the close button, and hide its taskbar button.
									$($(this).closest('div.window_top')
										.find('a.window_close').attr('href')).hide('fast');					
									// Close the window itself.
									$(this).closest('div.window').hide().find('iframe').attr('src',' ');		
									// Stop propagation to window's top bar.
									return false;
								})
							)
						)
					)
					.append(this.ctnt.addClass('abs window_content')
						.append(this.main.addClass('window_main').css({'width':'100%','height':'100%','margin':'0'})
						.append(this.spinn)
							.append(this.iframe).css({'width':'100%','height':'98%'})
						)
					)
					.append(this.bttm.addClass('abs window_bottom').html(this.title))
				)
				.append($('<span class="abs ui-resizable-handle ui-resizable-se"/>'))
				.mousedown(function() {
					JQD.window_flat();
					$(this).addClass('window_stack');// Bring window to front.
				})
				.draggable({
					containment: 'parent',// Confine to desktop.
					handle: 'div.window_top'// Movable via top bar only.
				})
				.resizable({
					containment: 'parent',// Confine to desktop.
					minWidth: 150,
					minHeight: 150
				})
				.addClass('window_stack')
				.appendTo('#desktop').removeAttr('style').show().animate({'top': '+='+p_top, 'left' : '+='+p_left},100);
				
				// insertion de l'onglet dans la barre des taches
				this.icondock.appendTo('#dock').show(); 
		
				// passage des onglets en icones si trop d'onglets
				var x_d = ($('#desktop').width() -200);
				var lastli=$('ul#dock li:last-child').position().left+$('ul#dock li:last-child').width();
				var x_lilc = lastli+$('ul#dock li:last-child').width();
				if (x_lilc > x_d){
					$('ul#dock li:visible').each(function(){
						$(this).addClass('bar-bottom-icon')
						.find('a').attr({ title: $(this).text()}).each(function(){
							$(this).find('img').attr('alt', $(this).text());
						})
						.hover(function(e){
							t = $(this).attr('title');
							var offset = $(this).closest('ul').offset();
							var lft = parseInt($(this).parent('li').position().left);
							$("#desktop").append("<p id='screenshot' class='abs'></p>").find('#screenshot').html(t);  
							var wtip = $("#screenshot").width()/2;
							$("#screenshot")
								.css("bottom","5px")
								.css("left",(lft  - wtip+12) + "px")
								.fadeIn("fast");                                              
						},
						function(){
							$("#screenshot").remove();
						});
					});

				}else {
					$(this).removeAttr('style').removeClass('bar-bottom-icon').find('a').removeAttr('style');
				}

			// traitement du iframe
			var count_load=0;
			this.iframe.attr('src', url).load(function()  {
				// on passe le contenu dans une variable
				el=$(this).contents();
				// redimenssionnement en fonction du choix du user
				// cas du choix "largeur de l'appli contenue"
				if( $('#tmp_winsize').val() == 'content' && count_load == 0 && !url.match(/^http/) && !url.match(/gepi/g)  )
					 $(this).closest('.window').css('min-width', '650px').width( el.width() + 25 );
				// cas du choix "Plein ecran"
				if( $('#tmp_winsize').val() == 'fullwin' && count_load == 0 ) JQD.window_resize( $(this) );
				// suppression du spinner
				$('#lcspinner').remove();
				// et on affiche
				$(this).show();
				count_load=1;
					
				// on essaie de fixer le bug en cas de clic sur une ancre dans une page iframe
				//el.find('body').localScroll();
					
				// on ne fait rien si gepi ou si on est sur un autre serveur
				if ( url.match(/gepi/g) || url.match(/^http/) ) return;
				
				// actions sur les liens contenus
				el.find('a').each(function(){
					if ($(this).attr('target')=='_blank') {// ouverture des target=_blank ds une fenetre du bureau
						$(this).addClass('open_win ext_link').removeAttr('target').attr('href',$(this)[0].href);
						if($(this).attr('title').length==0 || $(this).attr('title')!='') $(this).attr('title',$(this).text());
					}
					if ($(this).attr('target')=='_top') {// on inhibe les traget=_top
						$(this).removeAttr('target');
					}
					if($(this)[0].href.match('mailto:')){// Listage et modif des mailto:
						$(this)[0].href.length > 0 ? cible=$(this)[0].href.replace('?','&') : '';
						$(this).attr({
							'href':cible.replace('mailto:',document.location.href.replace('desktop/','')+'squirrelmail/src/compose.php?send_to='),
							'rel':'squirrelmail'
						}).addClass('open_win ext_link');
					} 
				});
				el.find('a.open_win').each(function(){
					$(this).click(function(){
						JQD.init_link_open_win(this);
						return false;//on inhibe le lien
					});
				});
			});
		},
		
		//
		//__/__/ contronle - modif des liens ouvrant une fenetre __/__/
		//
		init_link_open_win: function(el){
			//alert($('div.window').length);
			var url = $(el).attr('href');
			var text = $(el).text();
			var title = $(el).attr('title');
			var img = $(el).find('img').attr('src');
			if (url.match(/^#/)) {
				url = $(el).attr('rel');
				title = text;
			}
			var i=0;
			var winexist=0;
			var i_frame = "";
			if(url.match(/prefs/) && $('#user_form_prefs').length>0) {
				JQD.window_flat();
				$('#user_form_prefs').closest('div.window').addClass('window_stack').show();
				JQD.clear_active();
				return false;
			}
			$.each($('div.window iframe'),function(){
				i_src  = $(this).attr('src');
				if(i_src==url ) {
					winexist=1;
					i_frame = $(this).closest('div.window');
				}
				i+=1;
			});

			if(winexist!=1) JQD.buildwin(url, text, title, img);
			else {
				JQD.window_flat();
				i_frame.addClass('window_stack').show();
				JQD.clear_active();
			}
		},

		 //
		 //
		 //
		 desktop_space: function(){
			// gestion des bureaux secondaires // A REVOIR ENTIEREMENT
			var left_o = $('#desktop').width();
			$('#inettuts').animate({left: left_o+'px'}).hide();
			$('#monLcs').animate({left: -left_o+'px',right: left_o+'px'}).hide();

			$('#otBuro_2').click(function(){
				if ( $(this).hasClass('monlcs_dtq') ) return false;
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
						var _WPP  = $('body').find('#wallpaper');
						var _WPPO = _WPP.offset();
						$('#iLcsMenu').hide();
						if($('#wallpaper_b').length == 0){
							var _WPP_b = $('#wallpaper').clone().attr('id', 'wallpaper_b').css({'position': 'absolute', 'left': _WPPO.left}).hide().prependTo('body');
						}
						$('#desktop').animate({left: left_o+'px'},1500);
						$('#wallpaper').css('position','absolute').animate({left: '+='+left_o},1500, function(){});$('#wallpaper_b').show();
						$('#inettuts').animate({left: (2*left_o)+'px', right: (-2*left_o)+'px'},1500, function(){$('#inettuts').hide();});
						$('#monLcs').show().animate({left: 0, right: 0},1500, function(){
							$('#monLcs iframe').attr('src', '../monlcs/');
							$('a.menu_trigger, ul.bar_top_right li').hide('slow');
							$('ul.bar_top_right >li:eq(0), ul.bar_top_right >li:eq(2), ul.bar_top_right >li:eq(7)').show('slow');
						});
						var spaceOn='3';
					} else if((ind==0) && ($('#desktop').position().left!=0)) {
						$('#iLcsMenu').hide();
						$('#desktop').animate({left: 0, right: 0},1500);
						$('#wallpaper').animate({left: $('#wallpaper_b').offset().left},1500);
						$('a.menu_trigger').show('slow');
						$('#inettuts').animate({left: left_o+'px', right: -left_o+'px'},1500, function(){$('#wallpaper_b').remove();$('#inettuts').hide();});
						$('#monLcs').animate({left: -left_o+'px',right: left_o+'px'},1500, function(){$('#monLcs iframe').attr('src', '');});
						$('ul.bar_top_right >li').show('slow');
						$('#wallpaper_b').remove();
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
			
		 },
		 
		 //
		 //.:LCS:. Quicklaunch (Dock MacOs)
		 //
		 init_docks: function() {
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
		 },


		//###############
		// Initialize the desktop.
		//###############
		init_desktop: function() {
			if (window.location !== window.top.location) {
				window.top.location = window.location;
			}
			
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

			$(window).resize(function() {
				JQD.place_wallpaper();
			});
			
			// info LcsDevTeam
			$('a[title=LcsDevTeam]').click( function() {
				$('#LDT').remove();
				$('<div id="LDT"/>')
				.addClass('abs')
				.css({
					bottom: '5px',
					right:'5px',
					width:'250px',
					height:'160px',
					padding:'5px'
				})
				.append(
					$('<h3/>')
					.css({
						'padding': '5px 10px',
						'border': '1px solid #aaa',
						'-moz-border-radius': '10px 10px 0 0' ,
						'-webkit-border-radius': '10px 10px 0 0' ,
						background:'#eaeaea'
					})
					.html('LcsDevTeam')
					.append( $('<span/>').addClass('close float_right').click( function() {$('#LDT').remove()}) ) 
				)
				.append(
					$('<ul/>')
					.addClass('box_trsp_black')
					.append( $('<li/>').html('<strong>Jean-Luc Chr&eacute;tien (<em>Chef de projet</em>)</strong>') )
					.append( $('<li/>').html('Simon Cavey') )
					.append( $('<li/>').html('Yannick Chistel') )
					.append( $('<li/>').html('Philippe Leclerc') )
					.append( $('<li/>').html('Olivier Lecluse') )
					.append( $('<li/>').html('Dominique Lepaisant') )
					.append( 
						$('<li/>').html('<br/>Contact:<br/>').append(
							$('<a/>').attr({
								'href': 'mailto:LcsDevTeam@tice.ac-caen.fr&subject=[Lcs-Bureau]'
							})
							.html('<pre>LcsDevTeam@tice.ac-caen.fr</pre>') 
						)
					)
					.css('display', 'block')
				)
				.appendTo('#desktop')
				.slideDown('slow');
			});


			// .:LCS:.  reposition icons and other babioles on window resize
			// hummmm....
			$(window).resize(function() {
			 	JQD.init_icons();
			//	p = $('#user_info_bar_btn').position().left;
			//	$('#infos_user_panel').animate({left : parseInt(p)+'px'});
			//	$('#user_infos').animate({left : parseInt(p)+'px'});
			});
			
			// Notification
			// create notify welcome
			JQD.create_notify("default", { title:'Bienvenue sur Lcs-Bureau', text:'D&eacute;couvrez une nouvelle interface pour LCS. <br />Bonne nav...'});
				
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
							JQD.save_prefs_dev('PREFS', 'hjy', 'lkhlm');
							instance.close();
						}
					});
				}
			});
			
			// PARTIE A REVOIR
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
		            // on notifie les changements pour enregistrement ??
		            // A confirmer
		           	JQD.create_notify("withIcon", { title:'Enregistrer vos pr&eacute;f&eacute;rences', text:"L'ic&ocirc;ne "+$(ui.draggable).text() + " a &eacute;t&eacute; ajout&eacute;e sur le bureau.<br />Cliquez moi pour enregistrer vos pr&eacute;f&eacute;rences...", icon:'core/images/icons/info.png'},
						{
						expires:false,
						click: function(e,instance){
							JQD.save_prefs_dev('PREFS', 'hjy', 'lkhlm')
							instance.close();
						}
					});
		        }
		    });
		    
		    // drag sur les icones
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
			// appel spaces bureaux seconadaires
			JQD.desktop_space();			
			// placement et taille des icones
			JQD.init_icons();
			// Appel des icons dock (type macOs)
			JQD.init_docks();

		}
	};
// Pass in jQuery.
})(jQuery);

