/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* @jquery.desktop.js 
* base sur jquery.desktop de Nathan Smith
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version 0.2~20
* Derniere mise a jour : 13/02/2011
* Licence GNU-GPL -  Copyleft 2010-2011
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/

//
// Namespace - Module Pattern.
//
var JQD = (function($, window, undefined) {
	return {
		/**
		* @function: construction et affichage du desktop
		* @name JQD.mk
		* @params: array >  JQD.options.user (options user)
		*/
		mk: function(opts) {
			for (var i in JQD.build) {
				JQD.build[i]( opts );
			}
		},
		//
		// TODO: JQD.i18n Tableau de langues (non utilise - a voir ?)
		//
		/*i18n : function(m) {
			return lang[m];
		},*/
		//
		// JQD.defaults : default options
		//
		defaults : {
			wallpaper: "core/images/misc/LCS_Desktop.jpg", // wallpaper hors connexion
			s_idart: "0", // id dernier article du forum (date)
			lang: 'es', // fichier de lang ( JQD._('chaine') )
			user : { // prefs user
				wallpaper : "core/images/misc/LCS_Desktop.jpg",
				pos_wallpaper : "wallpaper",
				bgcolor: "#6789ab",
				bgopct: "50",
				iconsize: 48,
				iconsfield: 50,
				quicklaunch: "0",
				winsize: "content",
				icons : [{
					txt : "Pr&eacute;f&eacute;rences",
					url : "core/user_form_prefs.php",
					rev : "prefs",
					img : "core/images/app/lcslogo-prefs.png"
				},
				{
					txt : "Documentation Lcs-Bureau",
					url : "../doc/desktop/html/",
					rev : "docdesk",
					img : "core/images/icons/logo-lcs_buro_168.png"
				},
				{
					txt : "A propos de Lcs-Bureau",
					url : "core/a_propos.php",
					rev : "apdesk",
					img : "../lcs/images/barre1/BP_r1_c8.gif"
				}]
			},
			applis: {
				doclcs: {
					txt : "Documentation G&eacute;n&eacute;rale",
					url : "../lcs/statandgo.php?use=Aide",
					rev : "doclcs",
					img : "core/images/app/lcslogo-docgen.png",
					typ : "aide"
				},
				docdesk : {
					txt : "Documentation Lcs-Bureau",
					url : "../doc/desktop/html/",
					rev : "docdesk",
					img : "core/images/app/lcslogo-doc.png",
					typ : "aide"
				},
				apropos : {
					txt : "A propos de Lcs-Bureau",
					url : "core/a_propos.php",
					rev : "apdesk",
					img : "core/images/app/lcslogo-deskapp.png",
					typ : "aide"
				},
				addicon : {
					txt : "Ajouter une ic&ocirc;ne",
					url : "#",
					rev : "addicon",
					img : "core/images/app/lcslogo-lcs.png",
					typ : "buro"
				}/*,
				parpeda : {
					txt : "Parcours p&eacute;dagogiques",
					url : "core/learning_path.php",
					rev : "parpeda",
					img : "core/images/app/lcslogo-lcs.png",
					typ : "srvc"
				},
				tinymce : {
					txt : "Editeur de texte",
					url : "tinymce/examples/full_jquery.html",
					rev : "tinymce",
					img : "core/images/app/lcslogo-lcs.png",
					typ : "srvc"
				},
				paintweb : {
					txt : "Editeur graphique",
					url : "paintweb/demos/",
					rev : "formallin",
					img : "core/images/app/lcslogo-default.png",
					typ : "srvc"
				},
				formallin : {
					txt : "G&eacute;n&eacute;rateur de formulaires",
					url : "formallin/admin.php?page=index",
					rev : "formallin",
					img : "core/images/app/lcslogo-formallin.png",
					typ : "srvc"
				}*/
				
			}
		},
		//
		//#JQD.init[i]( o ) all functions to init the desktop
		//
		init: {
			//
			// #JQD.init.lang( o.lang) : Initialize the language
			// TODO: Voir le doublon. Declaree dans JQD.settings()
			lang:function(o){
				$.i18n(o.lang);
				//console.log("$.i18n('jqd', 'unicorn') : ", $.i18n('jqd', 'unicorn')); // returns "Année"
				//console.log("_('unicorn') : ", JQD._('unicorn')); // returns "Année"
			},
			
			//
			// #JQD.init.clock() : Initialize the clock.
			//
			clock: function() {
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
				$('#clock').html( clock_time ).dblclick( function() {
					$(this).attr('title', 'agendas');
					JQD.init_link_open_win( this );
					setTimeout( $(this).removeAttr('title'), 1000 );
					return false;
				}).mouseenter( function() {
					$('#date').show(500);
				}).mouseleave( function() {
					$(this).next('#date').hide(500);
				}).click(function(){
					return false;
				});
	
				// Update every 60 seconds.
				setTimeout(JQD.init.clock, 60000);
			},
			//
			//#JQD.init.mdrc() - Cancel mousedown, right-click.
			//
			mdrc : function() {
				$(document).mousedown(function(ev) {
					// on autorise aussi le focus dans les input et textarea
					if (!$(ev.target).closest('a, input, textarea ,select , select option, form, div.slctname, #wp_url_m').length) { 	
						JQD.utils.cancel_editIcon();
						JQD.utils.clear_active();
						//return false;
					}
				}).bind('contextmenu', function() {
					return false;
				});
			},
			
			//
			//#JQD.init.topmnu()
			//
			topmnu : function(){
				// Make top menus active.
				$('a.menu_trigger').mousedown(function() {
					if ($(this).next('ul.menu').is(':hidden')) {
						JQD.utils.clear_active();
						$(this).addClass('active').next('ul.menu').show();
					}
					else {
						JQD.utils.clear_active();
					}
				}).mouseenter(function() {
					// Transfer focus, if already open.
					if ($('ul.menu').is(':visible')) {
						JQD.utils.clear_active();
						$(this).addClass('active').next('ul.menu').show();
					}
				});
				//add window open on simple-clic on link of menu bar 
				$('a.open_win').not(".green, #blueone")
				.bind('click', function() {
					// on agit suivant le cas renvoye
					// - "ress" Creation du tableau des ressources imposees
					dlgTtl= JQD.options.user.statut=='admin' ? 'Ajouter un lien partag\u00e9' : 'Ajouter une ic\u00f4ne';
					$(this).hasClass('addicon') ? JQD.utils.dispIcnForm({title:dlgTtl}) : $(this).hasClass('ress') ? JQD.ressEdit() : JQD.init_link_open_win(this);
					//console.info('optTable : ',optTable);
					return false;
				}).draggable({// drag sur les icones
			    	delay: 1000, // pas de dragg sur un click
			        helper: 'clone'
			    });
				$('a.addicon, a.ress').removeClass('open_win');
				
				// sort menu items in alphabetic order
				$('ul.menu').each(function(i, v){
					//console.info('ul.menu'+i, $(v));
					JQD.utils.sortLi( $(v), 'li' )
				})
			},
			//
			//JQD.init.rrlinks() : Relative or remote links?
			//
			rrlinks: function() {
				$('a').click(function() {
					// sauf pour les champs d'url
					if ( $(this).find('input').length == 0) {
						var url = $(this).attr('href');
						this.blur();
		
						if ( typeof url=='undefined' || url.match(/^#/)) {
							return false;
						}
						else if (url.match('://') || url.match(/gepi/g) ) {
							$(this).attr('target', '_blank');
							return true;
						}
					}
				});
			},
			//
			//JQD.init.btr()
			//
			btr : function(o) {
				$('a.spaces').text('1');
				$('.bar_top_right a.save').click(function(){
					JQD.save_prefs_dev('PREFS', o.user.name, 'buro');
				});
				//init tips (poshytip)
				$('.bar_top_right>li>a').not('#btrUsrInf, #otBuro_2, #clock').poshytip({
					className: 'tip-twitter',
					alignTo: 'target',
					alignX: 'center',
					alignY: 'bottom',
					offsetX: 0,
					offsetY: 10
				});	
			},
			//
			//#JQD.init.icons()
			//
			icons : function (o) {
				JQD.utils.initIcon( $('a.icon') )
			},
			//
			//#JQD.init.show_desktop( ) : Show desktop button, ala Windows OS.
			//
			show_desktop: function(){
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
			//#JQD.init.dock() :
			//
			dock: function() {
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

			},
			//
			//#JQD.init.infosUser()
			//
			infosUser: function(){
				$('#btrUsrInf').click(function(){
					JQD.window_flat();
					//JQD.tipInfUsr();
					$('#user_infos').addClass('window_stack').show()
					.css({'opacity': 0.1}).animate({'opacity': 1},1500).click(function(){
						JQD.window_flat();
						$(this).addClass('window_stack');
						return false;
					}); 
				});
			},
			//
			//#JQD.init.close() - init button close
			//
			close: function(){
				$('span.close').click(function(e){
					$(this).closest('.toClose').hide();
				//	e.stopPropagation();
				});
			},
			//
			//#JQD.init.showTeam()
			//
			showTeam: function() {
				$('a[title=LcsDevTeam]').click( function() {
					JQD.showTeam();
				});
			},
			//
			//#JQD.init.showhide()
			//
			showhide: function() {
				// pannel up/down
				$('.block_updown').hide();
				$('.triangle_updown').toggle(function(){
					$(this).addClass('down').next('.block_updown').show();
				},function(){
					$(this).removeClass('down').next('.block_updown').hide();
				});
				$('.btn_groups.triangle_updown').trigger('click');
				$('.block_updown .down').hide();
			},
			//
			//#JQD.init.trash() - Corbeille
			//
			trash : function() {			
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
						JQD.utils.rmIcon(ui.draggable);
					}
				});
			},
			//
			// .:LCS:.  reposition icons and other babioles on window resize
			// hummmm....
			wrsz : function( o ) {
				$(window).resize(function( o ) {
					JQD.init.place_wppr( o );
				 	JQD.init.icons( o );
				});
			},			
			//
			//
			//
			desktop : function() {
				// PARTIE A REVOIR
					var menuDesk= [ 
						{'Ajouter une ic&ocirc;ne':{ 
							onclick:function(menuItem,menu) {
								 JQD.utils.dispIcnForm({title:'Ajouter une ic\u00f4ne'}) 
							}, 
							icon:'core/images/gui/add-icon_16.png', 
							disabled:false 
						}  } ,
						$.contextMenu.separator, 
						{'Enregister les pr&eacute;f&eacute;rences':{ 
							onclick:function(menuItem,menu) {
								JQD.save_prefs_dev('PREFS', JQD.options.user.login, 'buro') 
							}, 
							icon:'core/images/gui/save_16.png', 
							disabled:false 
						}  }, 
						{'Supprimer les pr&eacute;f&eacute;rences':{ 
							onclick:function(menuItem,menu) {
								JQD.rm('PREFS_', JQD.options.user.login, 'buro') 
							}, 
							icon:'core/images/gui/delete.gif', 
							disabled:false 
						}  } , 
						{'Actualiser le bureau':{ 
							onclick:function(menuItem,menu) {
								 location.reload() 
							}, 
							icon:'core/images/gui/refresh_16.png', 
							disabled:false 
						}  }
					]; 
			    $("#desktop").droppable({
			        accept: '.open_win',
			        drop: function(event, ui) { 
		                // LCS Cas particulier d'un lien provenant du panneau infos user
		                //on verifie que le lien provient de infos user
			        	fromwhere=$(ui.draggable).closest('ul#user_infos').length; 
			        	newrel=$(ui.draggable).attr('href'); //on recupere la valeeur du lien		        	
		        		img_w=$(this).find('a.abs.icon img').last().width();
		           //     $(this).find('a.abs.icon').last().after($(ui.draggable).clone());
							var icnOpts={
								txt: $(ui.draggable).text(),
								url: $(ui.draggable).attr('rel'),
								iconsize: img_w,
								rev: $(ui.draggable).attr('rev'),
								img: $(ui.draggable).find('img').attr('src'),
                                top:'',
                                left:'',
                                color:'',
                                owner:'',
                                groups:[]
							};
							var icn = JQD.utils.icon( icnOpts ).css({position:'absolute',top:$('#desktop').outerHeight(true)/2, left:$('#desktop').outerWidth(true)-200});
							//icn.appendTo('#desktop');
							 $(this).find('a.abs.icon').last().after( icn );
							JQD.init.icons();
							JQD.utils.initIcon( icn );
							JQD.save_prefs_dev('PREFS', -1, 'lkhlm');

						JQD.utils.clear_active();
			        }
			    })
				// Theme name. Included themes are: 'default','xp','vista','osx','human','gloss'  
				// Multiple themes may be applied with a comma-separated list.  
			    .contextMenu(menuDesk,{theme:'lcsdesk'});
		    
			},
			//
			//JQD.init.zonurlgo()
			//
			zonurlgo: function(zurl) {
					$('#wp_url_m').click( function() {return false;} )
					.focus(function() {$(this).next('img').show();} )
					.bind('keypress', function(e) {
						if(e.keyCode==13){
							if ( $('#wp_url_m').val()!='' ) {
								var zisurl = $('#wp_url_m').val();
								zisurl.match(/^http/) ? '' : zisurl='http://'+zisurl;
								JQD.buildwin(zisurl,zisurl,zisurl.replace('http://',''),'' );
							}
						}
					})
			},
			//
			// JQD.init.wpp(o) : le wallpaper
			//
			wpp: function( o ){
				// on applique les prefs sur le body
				//console.log('o.user.bgcolor : ',o.user.bgcolor );
				var bgc = o.user.bgcolor ? o.user.bgcolor : '#123456';
				$('body').css('background', bgc); 
				
				//wallpaper
				x=o.user.wallpaper ? o.user.wallpaper :  o.wallpaper;
				x.match('~') ? x=x.replace('core/','') : '';
				if( $('#wallpaper').length > 0 ) {
					$('#wallpaper').removeClass().addClass( o.user.pos_wallpaper )
					.attr({'src': x.replace('thumbs/','')}).css({'opacity': 0}).load(function(){
								$(this).animate({'opacity': o.user.bgopct/100},2000);
							})
				} else {
					//console.log('x: ',x);
						$('body').prepend(
							$('<img/>').attr({'id':'wallpaper', 'src': x.replace('thumbs/','')})
							.addClass( o.user.pos_wallpaper ).css({'opacity': 0}).load(function(){
								$(this).animate({'opacity': o.user.bgopct/100},2000)
							})
						);
				}

			},
			//
			//#JQD.init.place_wppr() : position du wallpaper
			//
			place_wppr: function( o ){
				var w = $('#desktop').width(),
				h = $('#desktop').height(),
				x = typeof(o.user) !='undefined' ? o.user.pos_wallpaper ? o.user.pos_wallpaper : o.pos_wallpaper : 'wallpaper';
				$('#wallpaper').removeAttr('style').removeClass().addClass( x ).css({opacity: JQD.options.user.bgopct/100});
				if(x.match('center_h')){l_wp = (w-$('#wallpaper').width())/2;$('#wallpaper').css({'left':l_wp+'px'});}
				if(x.match('center_v')){l_hp = (h-$('#wallpaper').height())/2;$('#wallpaper').css({'top':l_hp+'px'});}
			},
			//
			// JQD.init.wmsess() : ouverture de la session webmail (squirrelmail)
			// TODO: A VERIFIER POUR ROUNDCUBE
			wmsess: function() {
				$('#tmp_squirrelmail').attr('src','../lcs/statandgo.php?use=squirrelmail');
				setTimeout(function(){
					JQD.notify_wmail();
				},10000);
				
			},
			//
			// JQD.init.forumess() : notification dernier message forum (spip)
			//A Confirmer 
			forumess: function(o) {
				var spipidart = $('<input/>').attr({
					type: 'hidden', 
					id: 's_idart',
					name:'s_idart',
					value:o.user.s_idart
				}).appendTo('body');
				//console.log('o.user.s_idart: ', o.user.s_idart);
				setTimeout(function(){
					JQD.notify_forum();
				},15000);
				
			},
			//test
			myurl: function(){
				JQD.mnuzonurl();
			}
		},
		//
		//#JQD.build( opts )
		//
		build: {
			//
			//#JQD.build.btop() : build bar_top
			//
			btop: function() {
				var btop = $('<div/>').attr({id:'bar_top'}).addClass('abs').append(
					$('<img/>').addClass('icon float_left').attr({src: 'core/images/icons/24/lcslogo_buro.png'})
				).appendTo('body');
			},
			//
			//#JQD.build.bbttm() : build the bottom bar
			//
			bbttm: function( o ) {
				barBttm = $('<div/>').attr({id: 'bar_bottom'}).addClass('abs').append(
					$('<a href="#"/>').addClass('float_left').attr({id: 'show_desktop', title: 'Afficher le bureau'}).append(
						$('<img/>').attr({src: 'core/images/icons/icon_22_desktop.png'})
					)
				).append(
					$('<ul id="dock"/>').append( $('<li/>') )
				).append(
					$('<a href="#"/>').addClass('float_right copyleft').text('LcsDevTeam').attr({title: 'LcsDevTeam'})
				).append(
					$('<div id="bar_bttm_icon"/>')
				).appendTo('body');
			},
			//
			//#JQD.build.btopr( opt )
			//
			btopr: function( opts ){
				var btrUl = $('<ul/>').addClass('bar_top_right float_right');
				if ( parseInt(opts.idpers) !=0 ) {
					$.each( JQD.btr, function( i, v ) {
						btrUl.append( JQD.utils.btrLi( i, v ) );
					});
					btrUl.appendTo('#bar_top');
					var btrDate = $('<div/>').append( $('<ul/>') ),
					usri = $('#btrUsrInf'),
					usrGp = opts.user.infos.group ? opts.user.infos.group.gp ? opts.user.infos.group.gp.name : 'admin' : 'default',
					usrIcon = 'core/images/annu/24/'+usrGp+'_'+opts.user.infos.sexe+'_trsp.png',
					usriDiv = JQD.infusr( opts ).insertAfter(usri);
					
					usri.html(opts.user.infos.fullname).prepend( 
						$('<img/>').attr({
							src: usrIcon.toLowerCase()
						}) 
					);
				}
				var btrAuth = $('<a/>').addClass('auth');
				if ( $('img.auth').length==0 ) 
				$('<li/>').addClass('auth').append( 
					btrAuth.attr({
						href:opts.applis.auth.url, 
						title:opts.applis.auth.txt
					}).append( 
						$('<img/>').attr({src:opts.applis.auth.img, alt:''}).addClass('auth')
					).click(function(){
						JQD.logform(parseInt(opts.idpers) == 0 ? '../lcs/auth.php' : JQD.logform('../lcs/logout.php'));
						return false;
					}) 
				).prependTo( btrUl );
				return btrUl;
				
			},
			//   
			//
			//
			clock_date: function() {
				var dat= $('<div id="date"/>').append( $('<span/>').addClass('date') ).appendTo($('#clock').parent('li'));
			},
			//
			//#JQD.build.nenu(app)
			//
			menu: function( o ) {
				//console.log('o.applis : ', o.applis)
				var app = o.applis,
				mn = $('<ul/>'),
				mntype= ({
					buro: 'Lcs-Bureau',
					srvc: 'Services',
					appl: 'Applications',
					admn: 'Administration',
					aide: 'Aide'});
				$.each(mntype, function( id, txt ) {
					if ( !app.admin && id == 'admn' ) return;
					else mn.append( JQD.utils.tymenu( id, txt ) );
				});
				mn.appendTo('#bar_top');
				// ressources pour user
				if( typeof o.icons != 'undefined' ) {
					JQD.utils.tymenu( 'ress', 'Ressources' ).insertAfter($('li.appl') )
					$.each(o.icons, function(i) {
						// on cree le menu Ressources
						o.icons[i]['typ']='ress';
						// on construit l'item
						JQD.utils.ssmenu( o.icons[i] );
						// on ajoute la couleur 
						$('li.ress>ul>li:last-child').find('a').append( 
							$('<span/>').css({width:'16px',height:'16px',margin:'4px -20px 0 5px',float:'right',backgroundColor:o.icons[i].color}) 
						)
					});
				}
				//resssources admin
				if(o.user.statut=="admin" &&  typeof o.ress!="undefined" ){
					var optRess = {
						typ:'buro',
						url:'#',
						txt:'Liens partag&eacute;s',
						'img':'core/images/app/lcslogo-lcs.png',
						rev:'ress', 
						smn:o.ress
					};
					JQD.utils.ssmenu( optRess );
					$('a.ress').append( $('<img src="core/images/annu/16/admins.png"/>').css({float:'right',margin:'5px 0 0 5px'}) )
				}

				$.each(app, function(i) {
					if ( i != 'admin' )
					JQD.utils.ssmenu( app[i] );
					else 
					$.each(app[i], function(j) {
						if( typeof app[i][j].txt !=="undefined" )
						JQD.utils.ssmenu( app[i][j] );
					});
				});
				$('li.admn>ul>li>a').removeClass('open_win');
				//JQD.init.topmnu();
			},
			
			btrSpace: function( o ) {
				var space = $('<ul/>').css({'float':'left'}).addClass('menu').append(
					$('<li/>').css({'float':'left'}).append(
						$('<a/>').html('Desktop')
					)
				).append(
					parseInt(o.monlcs) == 1 ? $('<li/>').css({'float':'left'}).append(
						$('<a/>').html('monLcs')
					) : ''
				)/*.append(
					$('<li/>').css({'float':'left'}).append(
						$('<a/>').html('iLcs')
					)
				)*/.appendTo( $('#otBuro_2').addClass('menu_trigger').parent('li').addClass('spaces') );
				//$('li.spaces ul.menu li').each(function(i, v){
				space.find('li').each(function(i, v){
					$(v).find('a').prepend($('<span/>').text(i+1) );
				});
				JQD.desktop_space();
			},
			//
			//#JQD.build.icons( o )
			//
			icons : function( o ) {
				var icons = o.user.icons,
				nb_icons = $('#desktop a.abs.icon').not('.launch').length,
				t = 20, w = 20, s = 0, h_d = $('#desktop').outerHeight(true),
				icfd = parseInt(o.user["iconsfield"])/100, iwh = o.user["iconsize"];

				if( $('#desktop > a.icon').length > 1 ) 
					$('#desktop > a.icon').each(function(){$(this).remove();});
						
				if( $('#desktop > a.icon').length ==0 )  {
					//console.info('icons.sort(JQD.utils.sortArray) : ',icons.sort(JQD.utils.sortArray));
				$.each( icons.sort( JQD.utils.sortArray ), function(idx, icn) {
					if (idx == "admin" || idx == "auth" || idx == "addicon" || idx == "doclcs" || idx == "apdesk") return;
					var icna = $('<a href="#"/>').addClass('abs icon')
						.attr({
							rel : icn["url"],
							rev : icn["rev"]
						}).html('<span>'+icn["txt"]+'</span>').prepend(
							$('<img/>').attr({
								src: icn["img"],
								title: ''
							}).width(parseInt(iwh)+'px').height(parseInt(iwh)+'px').css('background-color', typeof icn.color!='undefined' ? icn.color :'')
						).appendTo('#desktop');
                        if (typeof icn["top"] === "undefined" || icn['top']=="") {
                                var h_i = icna.outerHeight(true) + 5;
                                var h_d = $('#desktop').outerHeight(true);
                                h_d= h_d*icfd;
                                s +=1;
                                if(t > h_d-h_i ){t = 20;w += 100};
                                icna.css({'top': t,'left': w});
                                t += h_i;

                        }else{
                            icna.css({top: parseInt(icn["top"]), left: parseInt(icn["left"])})
                        }
                    });
                    // icons grpoups
                    /*
					if(  typeof o.icons != 'undefined' ) {
						var i=0,t=10,n=o.icons.length;
                    $.each(o.icons, function (icn, optIcn) {
                    	//console.log(icn,optIcn);
						var h_d = $('#desktop').outerHeight(true),
						lastIcnOffset = $('#desktop').find('a.icon:last-child'),
						h_i = lastIcnOffset.outerHeight(true) + 5;
                    	optIcn['cls'] = 'group';
                     	optIcn['top'] = t;
                     	t+= lastIcnOffset.outerHeight(true) +5;
                   	
                    	optIcn['left'] =$('#desktop').outerWidth(true)-100;
                    	if(optIcn['top'] > h_d-h_i ){optIcn['top'] = 20;	optIcn['left'] -= 100};
						  i++;
                    	// TODO: Voir le choix d'affichage cause doublons
                     	//optIcn.owner != o.user.login ? JQD.utils.icon(optIcn) : '';
                    	$('#desktop').append(JQD.utils.icon(optIcn));
                    });
					}
					*/
                }
			},
			//
			//
			//
			quicklaunch: function( o ) {
				//alert(o.user.quicklaunch);
					//console.log('o : ',o);
					//console.log('o.user.quicklaunch : ',o.user.quicklaunch);
				if( parseInt( o.user.quicklaunch ) == 1 ) {
					$('<ul/>').addClass('abs').attr('id', 'quicklaunch').appendTo($('#desktop'));
					//if (o.pref == "nul" ) {  
						ql = o.applis;
					//}else{
					//	ql = o.pref;
					//}
					$.each(o.user.icons, function(i, v){
						$('<li/>').append(
							$('<a/>').addClass('launch open_win ext_link screenshot').attr({
								href: v['url'],
								rev: v['rel'],
								rel:v['rel'],
								title: v['txt']
							}).append(
								$('<img/>').attr({src: v['img'], alt:v['txt']}).addClass('quicklaunch')
							)
						).appendTo($('#quicklaunch'));
					});
					JQD.init_docks();
				} else {
					$('#quicklaunch').remove();
				}
			},
			 
			//
			//#JQD.build.trash() : corbeille
			//
			trash: function() {
				var trash = $('<div id="trash"/>').addClass('trash').append( $('<h3/>').addClass("trash_item")).appendTo($('#desktop'));
			},
			//
			//#JQD.iconForm() : Formulaire d'ajout d'icône
			//
			iconForm: function(opts){
				var icnFrmCncl = $('<span/>').addClass('bouton').append('<a/>').attr({id:'delete_icon'}).text('Annuler'),
				icnFrmFldst = $('<fieldset/>'),
				icnFrmLgnd = $('<legend/>'),
				icnFrmUl = $('<ul/>').addClass('ul2cols'),
				icnTtl = $('<input type="text"/>').attr({id:'icnttl', name: 'icnttl'}),
				icnUrl = $('<input type="text"/>').attr({id:'icnurl', name: 'icnurl'}),
				icnMyRess = $('<input type="checkbox"/>').attr({id:'icnMyRess', name: 'icnMyRess', value:JQD.options.user.infos.uid, 'checked': 'checked'}),
				//icnColArr = {transparent:'transparent', OliveDrab : 'OliveDrab', orchid : 'orchid',rouge : 'red', OrangeRed : 'OrangeRed', orange : 'orange', violet : 'violet', green : 'green', yellow : 'yellow', blue : 'blue'},
				icnColArr =["ffffff", "eeeeee", "ffff88", "ff7400", "cdeb8b", "6bba70",
			"006e2e", "c3d9ff", "4096ee", "356aa0", "ff0096", "b02b2c", "000000"],
				icnColor= $('<select/>').attr({id:'icncolor', name: 'icncolor',size:3}),
				icnGrp = opts.user.login=="admin" ? $('<select/>').attr({id:'icngrp', name: 'icngrp', multiple:'multiple',size:3}).append( $('<option/>').attr('value','').html('--- Choisir ---') ) : '',
				icnImg = $('<input type="text"/>').attr({id:'icnimg', name: 'icnimg'}),
				icnFrmSbmt = $('<span/>').addClass('bouton').append(
					$('<a/>').attr({href:'#',id:'valid_icon'}).text('Ajouter').click( function(){
						if(icnTtl.val()=='') {icnTtl.focus();return;}
						else if (icnUrl.val()=='') {icnUrl.focus();return;}
						else{
							var icnOpts={
								txt: icnTtl.val(),
								url:icnUrl.val(),
								iconsize: $('a.icon img').width(),
								rev: 'perso_'+opts.user.login,
								img: icnImg.val() !='' ? icnImg.val() : 'core/images/app/lcslogo-group.png',
                                top:'',
                                left:'',
                                color:icnColor.val(),
                                owner:JQD.options.user.infos.fullname,
                                groups:[]
							};
							var icn = JQD.utils.icon( icnOpts ).css({position:'absolute',top:$('#desktop').outerHeight(true)/2, left:$('#desktop').outerWidth(true)-200});
							icn.appendTo('#desktop');
							JQD.init.icons();
							JQD.utils.initIcon( icn );
							// on inserre sur le bureau
							JQD.save_prefs_dev('PREFS', -1, 'lkhlm');
							//icnGrp.val()!=''? JQD.save_prefs_dev('PREFS_', 'xx', 'shared') :'';
							
							if(JQD.options.user.statut=='admin'){
                        	    var gsv = JQD.utils.getSelectValue($(icnGrp));

								var gpChecked=new Array(), myress;
								$('#icnForm').find("input:checked").each(function (i) {
									gpChecked[i] = $(this).val();
								});                            
								//console.info('where', gsv[0]);
								//console.info('gpChecked', gpChecked.length);
								if(gpChecked.length>0){
                               		icnOpts['groups']=gpChecked;
									icn.addClass('group');
								}
								// mode modification
								if(icnFrm.hasClass('mod') ) {
								JQD.save_icon({ icon:icnOpts, where:gpChecked, ou:'group', myress:myress ,mode:'mod'});
								}
								else {
									JQD.save_icon({ icon:icnOpts, where:gpChecked, ou:'group', myress:myress});
									// on inserre sur le bureau
									//icn.appendTo('#desktop');
									// on insere dans le menu
									$('li a.ress ul').append(JQD.utils.itemnu( icnOpts,icnOpts).find('a').prepend($('<img src="+icnOpts.img"/>')));
									// on inserre dans le tableau d'options
									// voir JQD.save_icon()
								}
							}
							// on ferme le form dialog
							$('#icondialog').dialog('close');
						}
					}) 
				),
				icnFrm = $('<form/>').attr({id:"icnForm"}).append(
					icnFrmFldst
					//.append( icnFrmLgnd.html('Ajouter une ic&ocirc;ne') )
					.append(
						icnFrmUl
						.append(
							$('<li/>')
							.append( $('<label for="icnttl"/>').html('Titre') )
							.append( icnTtl )
						).append(
							$('<li/>')
							.append( $('<label for="icnurl"/>').html('Url') )
							.append(icnUrl)
						).append(
							$('<li/>')
							.append( $('<label for="icncolor"/>').html('Couleur') )
							.append(icnColor)
						)/*.append(
						//typeof JQD.options.user.infos.group.gp != 'undefined' ? JQD.options.user.infos.group.gp.name !='Eleves' ?
						JQD.options.user.infos.uid == 'admin' ? //JQD.options.user.infos.group.gp.name !='Eleves' ?
							$('<li/>')
							.append( $('<label for="icnMyRess"/>').html('Ajouter &agrave; mes ressources ') )
							.append(icnMyRess) :''
						)*/.append(
						//typeof JQD.options.user.infos.group.gp != 'undefined' ? JQD.options.user.infos.group.gp.name !='Eleves' ?
						JQD.options.user.infos.uid == 'admin' ? //JQD.options.user.infos.group.gp.name !='Eleves' ?
							$('<li/>')
							.append( $('<label for="icngrp"/>').html('Partager avec ') )
							.append( JQD.bFrmIptLbl({type:'checkbox', name:'icnAdm',value:'Administratifs',text:'Administratifs'}) )
							.append( JQD.bFrmIptLbl({type:'checkbox', name:'icnPrf',value:'Profs',text:'Profs'}) )
							.append( JQD.bFrmIptLbl({type:'checkbox', name:'icnElv',value:'Eleves',text:'Eleves'}) ) : '' //: ''
						)//.append(icnGrp)
					)
				),
				icnDialog=$('<div id="icondialog"/>').addClass('jqd_formulaires')
				.attr({'title':'Ajouter une ic&ocirc;ne'})
				.append( icnFrm )
				.append( icnFrmSbmt )
				.appendTo('body').hide();
				//console.log('typeof icnGrp: ',typeof icnGrp);
				//console.log('typeof JQD.options.user.infos.group.gp: ',typeof JQD.options.user.infos.group.gp);
				if( typeof icnGrp != 'string') $.each(opts.user.infos.group, function( i, v ) {
				 	if ( i == 'gp' ) {
					//icnGrp.prepend( $('<option/>').attr('value', opts.user.infos.group.gp.name).html(opts.user.infos.group.gp.name) );
					$('<option/>').attr('value', opts.user.infos.group.gp.name).html(opts.user.infos.group.gp.name).insertAfter( icnGrp.children('option:first-child') );
				 	} 
				 	else {
				 	icnGrp.append( $('<optgroup/>').attr('label', i ) );
				   $.each(opts.user.infos.group[i], function( ig, vg ) {
					icnGrp.append( $('<option/>').attr('value', vg).html(vg) );
				   });
				 	}
				 });
				$.each(icnColArr, function(i, v) {
					//console.log(i, v);
					$('<option/>').attr({value:'#'+v}).css({'background-color': '#'+v, color:'#'+v}).text(v).appendTo(icnColor)
				})
			},
			//
			//#JQD.build.go() : lancement de l'init
			//
			go: function( opts ) {
				for (var i in JQD.init) {
					JQD.init[i]( opts );
				}
			}

		},
		//
		// utils tools and functions
		//
		utils : {
			//
			//#JQD.utils.message : affiche les messages desktop
			//
			message: function( t ) {
				$('div.respform').remove();
				var respMessage= $('<div/>').addClass('respform abs').html( t ),
				wd = $(document).width()/2,
				wm = respMessage.width(),
				posh = (wd/2 ) - wm;
				respMessage.css({top:'5px', left:posh+'px'}).prependTo('#desktop').show('slow').css({top:'5px', left:wd-respMessage.width()/2+'px'});
				setTimeout(function(){
					$('div.respform').hide('slow').remove();
				},5000);
			},
			//
			//#JQD.utils.icon( o )
			//
			icon : function( o ) {
				var o_u = JQD.options.user,
				clss=  typeof o.cls ? o.cls : '',
				icon = $('<a href="#"/>').addClass('abs icon '+clss)
				.css({top: o.top, left: o.left})
				.attr({
					rel : o.url,
					rev : o.rev
				}).html('<span>'+ o.txt+'</span>')
				.prepend(
				$('<img/>').attr({src: o.img}).css({width: o_u.iconsize+'px', height: o_u.iconsize+'px', background: typeof o.color ? o.color :'' }).width( o_u.iconsize ).height(o_u.iconsize)
				);
				if( typeof o.owner && o.owner==JQD.options.user.login ) 
				icon.prepend($('<span/>').addClass('float_right close').click(function(){ 
					JQD.utils.rmIcon( icon)
				})
				//.text('X') 
				);

				return icon;
			},
			//
			//
			//
			initIcon: function( icon ) {
				var menuIcon= [ 
					{'Renommer l\'ic&ocirc;ne':{ 
						onclick:function(menuItem,menu) {
							 JQD.utils.editIcon($(this) )
						}, 
						icon:'core/images/gui/textfield_rename.png ' 
					}  } , 
					{'Supprimer l\'ic&ocirc;ne':{ 
						onclick:function(menuItem,menu) {
							 if(confirm('Supprimer '+$(this).find('span').text()+' ?')){ JQD.utils.rmIcon($(this) ) ;} 
						}, 
						icon:'core/images/gui/delete.gif', 
						disabled:false 
					}  } ,
					{'<div id="icClr"><div style="float:left;">Choisir la couleur:</div></div><br>':{
						onclick:function(menuItem,cmenu,e) {
							var img= $(this).find('img');
							$t = $(e.target);
							if ($t.is('.swatch')) {
								//this.style.backgroundColor = $t.css('backgroundColor');
								img.css({'backgroundColor': $t.css('backgroundColor')});
								$t.parent().find('.swatch').removeClass('swatch-selected');
								$t.addClass('swatch-selected');
							}
							return false;
						} 
					}
					}
				]; 

				// Desktop icons.
				icon.draggable({
					cancel: 'a.ui-icon',// clicking an icon won't initiate dragging
					revert: false, // when not dropped, the item will revert back to its initial position
					containment: $('#desktop, .trash'), // stick to desktop and trash
					//helper: 'clone',
					cursor: 'move'
				}).mousedown(function() {
					// Highlight the icon.
					JQD.utils.clear_active();
					$(this).attr('rev').match('perso') ? $(this).addClass( 'perso' ) : '';
					$(this).addClass( 'active' );
				}).dblclick(function() {
					// Get the link's target.
					JQD.init_link_open_win( this );
				}).contextMenu(menuIcon,{theme:'lcsdesk', //.not('.group') ?
					beforeShow: function() { 
						var allClr=['transparent','red','orange','yellow','green','blue','violet','black'];
						$(this.menu).find('#icClr').each(function() { 
							if( $(this).hasClass('color') ) return ;
							$.each(allClr, function(i){
								$('#icClr').append( $('<div/>').addClass('swatch '+ allClr[i]).css('backgroundColor',allClr[i]) );
							});
							$(this).addClass('color');
						}); 
					}
				});
			},
			//
			//#JQD.utils.rmIcon() : fonction supprimer icone
			//
			rmIcon: function (item) {
				if( item.hasClass('group') && item.attr('rev').match(JQD.options.user.login) ) {
					var admOrNo = typeof JQD.options.icons!='undefined' ? JQD.options.icons : JQD.options.ress,
					itTxt=typeof item.text() !='undefined' ? item.text()!='' ? item.text() : item.attr('title'): item.attr('title')
					itName=item.attr('name') ;
					//var ifn = admOrNo['ICON_'+JQD.options.user.login+'_'+itTxt.replace(/ /g,'_')+'.json'].groups,
					var ifn = typeof admOrNo[itName]==="undefined" ? item.groups : admOrNo[itName].groups,
					uTxt = 'de vos ressources personnelles', gTxt = ' le partage de ce lien avec les groupes :',
					me = $('<p/>'), lgUl = $('<ul id="lgIcn"/>');
					console.log(ifn);
					var dialogCfrm=$('<div/>').attr({id:'dialogCfrm',title:'Supprimer un lien partag&eacute;'}).append(
						$('<div/>').css({'text-align':'left'}).text('Vous allez supprimer').prepend($('<span/>').addClass('ui-icon ui-icon-alert').css({float:'left', margin:'0 7px 0 0'}) ).append( 
							$('<div/>').html('<strong>'+item.text() +'</strong>')
							/*
							.append( $('<label for="delIcnUsr"/>').text(uTxt) ).prepend( 
								$('<input type="checkbox"/>').attr({name:'delIcnUsr',id:'delIcnUsr', checked:'checked'}) //,disabled:'disabled'
							)
							*/
						).append(
							$('<div id="txtIcnGrp"/>').html(gTxt)
						)
					).appendTo('#desktop');
				//	if( ifn.length > 0 ) {
					$.each(ifn, function(i, v) {
						$('<li/>').html('<label for="'+v+'">'+v+'</label>').prepend( $('<input type="checkbox"/>').attr({name:v,id:v, checked:'checked'}) ).appendTo(lgUl ) //,disabled:'disabled'
					});
					lgUl.appendTo(  $('#txtIcnGrp') );
								
			        $('#dialogCfrm').dialog({
			            autoOpen: true,
			            width: 400,
			            modal: true,
			            resizable: false,
			            buttons: {
			                "Supprimer": function() {
								//var u=JQD.options.user.login, f='ICON_'+u+'_'+itTxt.replace(/ /g, '_'),
								var u=JQD.options.user.login, f=itName,
								ckb=new Array(), chkbChkd= $('#lgIcn li input:checked'), gru;
								chkbChkd.each(function (i) {
									ckb[i] = $(this).attr('name');
								});                            
								//console.log('ckb : ',ckb );
								gru = chkbChkd.length == $('#lgIcn li input').length ? 'all' : ckb;
								//console.log('gru (GroupRessUser) : ',gru );
								
								JQD.rm( f, u, gru);
								item.hasClass('intr') ? item.parents('tr').remove():'';
			                    $(this).dialog("close");
			                },
			                "Annuler": function() {
			                    $(this).dialog("close");
			                }
			            }
			        });
				} else {
				item.addClass('icon_trash')
				.removeClass('abs')
				.fadeOut().remove();
				JQD.save_prefs_dev('PREFS', -1, 'lkhlm');
				}
			},
			//
			//#JQD.utils.editIcon() : fonction supprimer icone
			//
			editIcon: function ( itm ) {
				var txt = itm.find('span').text(),
				himg = itm.find('img').height()+10,
				btnval = $('<span/>').addClass('submit float_right');
				itm.find('span').hide();
				var inpt=  $('<input type="text"/>').css({width: '120px', background: '#fafafa', margin: '2px', border:'1px solid #aaa'}).focus( function(){$(this).select()}).attr({value: txt}).live("blur", function(){
					// On récupère la valeur du champ de saisie
					txt = inpt.val();
					// On insère dans le <li> la nouvelle valeur du texte
					itm.find('span').text( txt ).show();
					editDiv.remove();
					       
			    }).live("keyup", function(e) {
			        if(e.keyCode == 13) {
			          // $(this).trigger("blur");
						JQD.utils.cancel_editIcon();
			        }
			    }),
			    editDiv = $('<div/>').addClass('abs slctname').css({
			        top: itm.position().top+himg,
			        left: itm.position().left-25
			    }).insertAfter( itm ).append( inpt ).append( btnval ).live("blur", function(){
			        // On récupère la valeur du champ de saisie
			        txt = inpt.val();
			    	// On insère dans le <li> la nouvelle valeur du texte
					itm.find('span').text( txt ).show();
					editDiv.remove();
					inpt.select();
			    });
			    btnval.click( function(){
					txt = inpt.val();
					itm.find('span').text( txt ).show();
					editDiv.remove();
			    })
   			},
   			//
   			//#JQD.utils.cancel_editIcon()
   			//
   			cancel_editIcon: function() {
   				$('a.icon span').show();
   				$('.slctname').remove();
   			},

			//
			// #JQD.utls.sortIcons( ops ):.  Sort icons
			//
			sortIcons: function( opts ) {
				// reposition icons
				var nb_icons = $('#desktop a.abs.icon').not('.launch, .group').length,
				t = 20, w = 20, s = 0;
				$('#desktop a.abs.icon').not('.launch').each(function(){
				var h_i = $(this).outerHeight(true) + 5,
				h_d = $('#desktop').outerHeight(true);
				opts.iconsfield !='' ? h_d= ( h_d * opts.iconsfield /100 ) : h_d= h_d/2;
				s +=1;
				if(t > h_d-h_i ){ t = 20; w += 100};
				$(this).css({'top': t,'left': w});
				t += h_i;
				});
			},
			//
			//#JQD.utils.clear_active() Clear active states, hide menus.
			//
			clear_active: function() {
				$('a.active, tr.active').removeClass('active');
				$('ul.menu,#otBuro_1 ul').hide();
			},
			//
			//#JQD.utils.btrLi( ) : contruction du menu top-right
			//
			btrLi: function( t, o ){
				var btrLi = $('<li/>').append( $('<a/>').attr({
					href: o.url,
					id: o.id,
					title: o.txt
				}).addClass( o.cls) );
				return btrLi;
			},
            //
            // JQD.utils.sortLi( ul )
            //
            sortLi: function( ul, li ) {
				var mylist = ul;
				var listitems = mylist.children(li).get();
				listitems.sort(function(a, b) {
					var compA = $(a).text().toUpperCase();
					var compB = $(b).text().toUpperCase();
					return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
				})
				$.each(listitems, function(idx, itm) { mylist.append(itm); });
			},
			//
			//
			//
			sortArray: function(a,b)
				{
				return ((a.txt < b.txt) ? -1 : (a.txt > b.txt) ? 1 : 0);
				},
			//
			//#JQD.utils.tymenu() : construction des categories du menu deroulant
			//
			tymenu: function ( t, txt) {
				this.itm = $('<li>').addClass( t )
				.append( $('<a href="#"/>').html( txt ).addClass('menu_trigger') )
				.append( $('<ul/>').addClass('menu') );
				return this.itm;
			},
			//
			//#JQD.utils.ssmenu() :
			//
			ssmenu : function( appl ) {
				this.it = JQD.utils.itemnu( appl, appl );
				//$('.'+ appl.typ +' .menu').append( this.it );
				$('li.'+appl.typ).find('ul.menu').append( this.it );
				if (appl.smn && appl.smn.length!=0) {
					var sitUl=$('<ul/>'), sitLi='' ;
					$.each(appl.smn,  function(k){
						var itsm = JQD.utils.itemnu( appl.smn[k], appl );
						sitUl.append( itsm )
					})
                    JQD.utils.sortLi( sitUl, 'li' );
					this.it.find('.open_win').addClass('submenu').append( sitUl );
					return sitUl;
				}
			},
			//
			//#JQD.utils.itemnu()
			//
			itemnu : function( o , p) {
				var itLi = $('<li>').append( 
					$('<a href="#"/>').addClass('open_win '+ o.rev).html( o.txt ).attr({
						'rel': o.url,
						'rev': p.rev
					}).prepend( $('<img/>').attr('src', p.img) ) 
				);
				return itLi;
			},
			//
			//
			//
			dispIcnForm: function(opts) {
				if( !typeof opts ) {
					var opts={title:'Ajouter une icone'}
				}
				JQD.utils.clear_active();
				$('#icondialog').attr({'title':opts.title}).dialog({
					width: 400,
					modal: true,
					//height:270 ,
					open: function(event, ui) {},
					close: function(event, ui) {$('#icondialog').hide();}
				});
			},
			//
			//#JQD.dialog_mess(opts)
			//
			dialog_mess: function(opts) {
				//console.log('opts : ',opts);
				$( "#dialog-message" ).dialog( "destroy" );
				var dm = $('<div id="dialog-message"/>').attr({title: opts.title}).append(
					$('<p/>').html( opts.intro ).append(
						$('<span/>').addClass('ui-icon ui-icon-circle-check').css({float:'left', margin:'0 7px 50px 0'})
					)
				).append(
					$('<p/>').html(opts.txt)
				).appendTo('#desktop').dialog({
					modal: true,
					buttons: {
						Ok: function() {
							$( this ).dialog( "close" );
						}
					}
				});

			},
            //
            // #JQD.utils.gqtSelectValue( slct )
            //
            getSelectValue: function (slct) {
                if($(slct).attr('multiple') == false) {
                    return $(slct).children('option:selected').val();
                }
                var optGroup={};
                $.each($(slct).children('option'), function(i){
                    $(this).is(':selected') ? optGroup[i] = $(this).val() : '';
                });
                //console.log ("optGroup: ",optGroup);
                return optGroup;
            }                        
		},
		
		//make and init win
		win : {
			
			//...
		},

			//
			//#JQD.build.infusr( opt )
			//
			infusr: function( oiu ){
				var uiUlLg = $('<ul/>').addClass('infos_user list_groups'),
				uiGp = oiu.user.infos.group ? oiu.user.infos.group.gp ? oiu.user.infos.group.gp.name : '' : '';
				if( uiGp!=''){
					uiUlLg.append( $('<li/>').addClass('group_title').html('Groupe Principal') )
				 	.append( 
				 		$('<li/>').addClass('user_link group').append( 
					 		$('<a/>').addClass('open_win group').attr({
					 			href:'../Annu/group.php?filter=' + uiGp,
					 			title: 'Voir le groupe '+ uiGp
					 		}).html(uiGp).prepend( $('<img/>').attr({src:'core/images/annu/16/'+uiGp.toLowerCase()+'.png'}) )
					 	).prepend(
					 		$('<a/>').addClass('open_win mail float_right').append( $('<img/>').attr({src: 'core/images/annu/mail.png'}) ).attr({
					 			href:'../squirrelmail/src/compose.php?send_to=' + uiGp+'@'+oiu.domain,
					 			title: 'Envoyer un message au groupe '+ uiGp
					 		})
					 	) 
					 );
				}
				 $.each(oiu.user.infos.group, function( i, v ) {
				 	if ( i != 'gp' ) {
				 	uiUlLg.append( $('<li/>').addClass('group_title').html( i =='Equipe'? 'Equipe/Classe': i ) );
				   $.each(oiu.user.infos.group[i], function( ig, vg ) {
						uiUlLg.append( $('<li/>').addClass('user_link group').append( 
					 		$('<a/>').addClass('open_win group iRdtq').attr({
					 			href:'../Annu/group.php?filter=' + vg,
					 			title: 'Voir le groupe '+ vg
					 		}).html( vg.replace(i, '').replace(/_/g, ' ') ).prepend( $('<img/>').attr({src:'core/images/annu/16/'+ i.toLowerCase()+'.png'}) )
					 	).prepend(
					 		$('<a/>').addClass('open_win mail float_leftt').append( 
						 		$('<img/>').attr({
						 			src:  i=="Equipe"?'core/images/annu/16/equipe_mail.png':'core/images/annu/mail.png'
						 		}) 
					 		).attr({
					 			href:'../squirrelmail/src/compose.php?send_to=' + vg+'@'+oiu.domain,
					 			title: 'Envoyer un message au groupe '+ vg
					 		})
					 	)
					 	.prepend( oiu.user.infos.group.gp.name=='Profs' && i=="Equipe" ? $('<a/>').addClass('open_win mail').css({float:'left'}).append( $('<img/>').attr({src: 'core/images/annu/16/classe.png'}) ).attr({
					 			href:'../Annu/group.php?filter=' + vg.replace(/Equipe/,'Classe'),
					 			title: 'Voir le groupe '+ vg.replace(/Equipe_/,'Classe ')
					 		}) : '' ) .prepend( oiu.user.infos.group.gp.name=='Profs' && i=="Equipe" ? $('<a/>').addClass('open_win mail').append( 
						 		$('<img/>').attr({
						 			src:  i=="Equipe"?'core/images/annu/24/classe_mail.png':'core/images/annu/mail.png'
						 		}) 
					 		).attr({
					 			href:'../squirrelmail/src/compose.php?send_to=' + vg.replace(/Equipe/,'Classe')+'@'+oiu.domain,
					 			title: 'Envoyer un message au groupe '+ vg.replace(/Equipe_/,'Classe ')
					 		}) : '' ) 
					 	)			 		
				   });
				 	}
				 });
				var uiDivLg = $('<div/>').addClass('block_updown').css({'max-height':'250px','overflow-y': 'auto'}),
				uiUl = $('<div id="user_infos"/>').addClass('toClose').append(
					$('<div/>').css({display: 'block'}).addClass('box_trsp_black list_infos_user').append(
						$('<span/>').addClass('close float_right')
					).append( $('<h2/>').html( oiu.user.infos.fullname ) ).append(
						$('<div/>').addClass('info_connect').html( oiu.user.infos.connect )
					).append( 
						$('<h3/>').addClass('btn_groups triangle_updown down').html( 'Membre des groupes' ) 
					).append(
						uiDivLg.append( uiUlLg ).append( $('<br/>').css('clear','both') )
					).append(
						$('<h3/>').addClass('triangle_updown').html( 'Pages perso' ) 
					).append(
						$('<div/>').addClass('block_updown up').append(
							$('<ul/>').addClass('infos_user').css('display','block').append( 
								$('<li/>').addClass('user_link myweb').append(
									$('<a href="../~'+ oiu.user.infos.uid +'"/>').addClass('open_win')
									.html('Mon espace perso').prepend(
										$('<img/>').attr({
											src: 'core/images/icons/16/network.png', 
											alt: ''
										}).css({
											width: '20px',
											'vertical-align': 'middle'
										})
									)
								)
							)
						)
					).append( 
						$('<h3/>').addClass('triangle_updown').html( 'Webmail' ) 
					).append(
						$('<div/>').addClass('block_updown up').append(
							$('<ul/>').addClass('infos_user').append( 
								$('<li/>').addClass('user_link').css({height:'auto'}).append(
									$('<input type="text"/>').addClass('open_win')
									.attr({id: 'user_mail', value: oiu.user.infos.email}).css({
										border:'none',
										background:'#fff',
										width: '230px',
										margin:'2px 5px'
									}).click( function() {$(this).select()})
								).append(
									$('<div/>').addClass('info_connect').html('Cliquez sur l\'adresse et apppuyez sur les touches Ctrl + c (Pomme + c pour Mac) pour copier votre adresse courriel')
								)
							).append( 
								$('<li/>').addClass('user_link').append(
									$('<a href="../Annu/mod_mail.php"/>').attr({
										title: 'Aller &agrave; la redirection',
										rel: 'annu'
									}).addClass('test_ajax open_win ext_link pointer')
									.html('Rediriger vers une boite personnelle')
								)
							)
						)
					)
				).hide();
				//init tips (poshytip)
				uiUl.find('a').poshytip({
					className: 'tip-twitter',
					alignTo: 'target',
					alignX: 'center',
					alignY: 'bottom',
					offsetX: 0,
					offsetY: 5
				});	

				return uiUl;
			},
		//
		//#JQD.showTeam - info LcsDevTeam
		//
		showTeam: function() {
			// 
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
		},
			//
			//
			//
			mnuzonurl: function() {
			// cas du champ d'url navigateur
			// construction du champ url
				var urlInputField = $('<div/>').addClass('float_left').css({position: 'relative'})
				.append(
					$('<input type="text"/>').addClass('wp_url').attr({id: 'wp_url_m', name: 'wp_url', value:''})
					.click(function(){return false;})
					.focus(function(){$(this).next('img').show();})
					.bind('keypress', function(e) {
						if(e.keyCode==13){
							if ( $('#wp_url_m').val()!='' ) {
								var zisurl = $('#wp_url_m').val();
								zisurl.match(/^http/) ? '' : zisurl='http://'+zisurl;
								JQD.buildwin(zisurl,zisurl,zisurl.replace('http://',''),'' );
							}
						}
					})
				)
				.append($('<img id="wp_url_go_m" />').attr({src: 'core/images/gui/arrow-right-blue_16.png'})
					.css({position:'absolute',top:'8px',right:'5px',display:'none',height:'12px'})
					.click(function(){
						//JQD.utils.zonurlgo();
						if ( $('#wp_url_m').val()!='' ) {
							var zisurl = $('#wp_url_m').val();
							// ####### A REVOIR #######
							zisurl.match(/^http/) ? '' : zisurl='http://'+zisurl;
							// ########################
							JQD.buildwin(zisurl,zisurl,zisurl,'' );
						}
					})
				);
				var opzurl = ({
						txt : "Navigateur web",
						url : "../doc/desktop/html/",
						rev : "navigator",
						img : "core/images/app/lcslogo-doc.png",
						typ : "srvc"
					}),
					lizurl= JQD.utils.ssmenu( opzurl );
					$('a.navigator').append(
						$('<ul/>').addClass('input_url_nav').css({left:'-5px',top:'20px'}).append(
							$('<li/>').append(
								$('<a/>').css({'padding':'5px 5px 10px'}).append(urlInputField)
							)
						)
					);

					// appel de l'url ds une nouvelle fenetre
					$('a.navigator').hover(function(){
						return false;
					});
			
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
		
		// Suppression fichier
		rm: function(file, usr, ou, force) { 
			var force = typeof force ? force : 0;
			//console.info(file+'\n'+usr+'\n'+ou);
			$.ajax({
				type: "POST",
				url: "core/action/delete.php",
				cache: false,
				data: ({
					file: file,
					user: JQD.options.user.login,
					ou: ou,
					force: force
				}),
				dataType: "json",
				success : function(data, status) {
					//console.info('typeof data : ',typeof data);
					if(data!== null )
					//console.info(data.mess);
					JQD.utils.dialog_mess({title:data.title, txt:data.mess, intro:'Information'});
				},
				error: function(data,err,errThrown){
					//console.log(err, data.responseText);
				}
			});	
		},

		//
		// Notification
		// 
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
					var newidart = parseInt( newidart_a[0].replace(/-/g, '') );
					//alert ( 'newidart: '+newidart+' \noldidart: '+oldidart); 
					if(newidart > oldidart){
						JQD.create_notify("withIcon", 
							{title:'<span style="color:#509fda;">Information Forum</span>', text:data.responseText, icon:'core/images/icons/info.png'},
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
			},600000);	
		},
		//
		//
		//
		notify_wmail: function(o) {
			// if squirrelmail is enable, notify new messages
			$.get("../squirrelmail/plugins/notify/notify-desktop.php", function(data){
				if (data != '')
					JQD.create_notify("withIcon", {
						title:'Messagerie', 
						text: data + '<p><span style="text-decoration:underline;">Consulter sa messagerie</span>', 
						icon:'core/images/icons/mailicon.png' 
					},
					{ 
						expires:false,
						click: function(e,instance){
						JQD.init_link_open_win('<a title="Webmail" '
						+'rel="../lcs/statandgo.php?use=squirrelmail" '
						+'rev="squirrelmail" href="#icon_dock_lcs_squirrelmail" '
						+'class="open_win ext_link">Messagerie</a>');
						instance.close();
					}
				});
			});
		},
		testacc: function(){
			var dAcc= $('<div id="accordion"/>'), 
			blocs = {groups : 'Membre des groupes', mail : 'Webmail',web: 'Espace web'};
			$.each(blocs, function(i,v) {
				dAcc.append(
					$('<h3/>').html('<a href="#">'+v+'<a/>')
				).append(
					$('<div/>').html(JQD.user.infos.connect)
				).accordion()
			});
		},
		//
		//
		//
		buildform: function() {
			var divFrm = $('<div/>').addClass('jqd_formulaires').append(
				JQD.bFrm().append( JQD.bFrmUl() )
			);
				
		},
		bFrm : function(opts) {
			var bForm = $('<form/>').attr({
				action: opts.act,
				id:opts.id
			});
			return bForm;
		},
		bFrmUl : function(opts) {
			var bfUl = $('<ul/>').addClass('ul2form');
			return bfUl;
		},
		bFrmLi : function(opts) {
			var frmLi = $('<li/>');
			return frmLi;
		},
		bFrmIpt: function(opts) {
			var bfIpt = $('<input/>').attr({type:opts.type, id:opts.name, name:opts.name, value:opts.value});
			return bfIpt;
		},
		bFrmLbl: function(opts) {
			var bfLbl = $('<label/>').attr('for',opts.name).text(opts.text);
			return bfLbl;
		},
		bFrmItm: function(opts) { // opts:{ type:"text, radio, chackbox'}
			var bfItm = JQD.bFrmLi(opts).append( JQD.bFrmLbl(opts) ).append( JQD.bFrmIpt(opts) );
			return bfItm;
		},
		bFrmIptLbl: function(opts) { // opts:{ type:"text, radio, chackbox'}
			var bfIptLbl = $('<div/>').append( JQD.bFrmIpt(opts) ).append( JQD.bFrmLbl(opts).css({'margin-left':'0', width:'auto',display:'inline',float:'none'}) );
			return bfIptLbl;
		},
		//
		//#JQD.bDialog( opts )
		// opts={id:'id_div_dialog', class:'class_div_dialog',title:'titre_de_la_boite_dialog', ctn:'contenu,peut_etre_1_fonction'}
		bDialog: function(o){
			JQD.utils.clear_active();
			var bdialog= $('<div id="'+o.id+'"/>').addClass('ui-widget')
			.attr({'title':o.title}).html( o.ctn ).dialog({
				width: typeof o.width ? o.width:400,
				resizable: true,
				modal: false,
				//height:270 ,
				open: function(event, ui) {},
				close: function(event, ui) {$('#icondialog').hide();},
			    buttons: {
			        "Ajouter un lien partag\u00e9": function() {
						JQD.utils.dispIcnForm({title:'Ajouter un lien partag\u00e9'}) 
					},
			 		"Fermer": function() {
						$(this).dialog("close");
			 		}
				}
			});
			return bdialog;	
		},
		//
		//
		//
		bTable: function( opts ) {
			//console.log('optb : ',opts.tb.esc);
			var esc=opts.tb.esc,
			btable=$('<table/>').css({width:'100%',border:'1px solid #aaa'}).addClass('data ui-widget ui-widget-content').append( JQD.bThead(opts.th, esc) ).append( JQD.bTboby(opts.tb, esc) );
			return btable;
		},
		bThead: function( opth, esc ) {
			//console.log('opth : ',opth);
			var bhead= $('<thead/>').append( JQD.bTr( opth, esc ).css({height:'36px','font-weight':'bold',color:'#6699cc','text-align':'center'}).addClass('ui-widget-header') );
			return bhead;
		},
		bTboby: function( optb, esc ) {
			var btbody=$('<tbody/>');
			$.each(optb, function(tr) {
				if(tr!='esc') JQD.bTr( optb[tr], esc ).attr({id:tr}).appendTo( btbody );
			});
			return btbody;
		},
		bTr: function( optr, esc ) {
			var btr=$('<tr/>').css({border:'1px solid #aaa', 'barder-width':'1px 0'});
			$.each(optr, function(td,val) {
				if(val!='' && !esc.match(td) ) JQD.bTd( optr[td],td ).appendTo( btr );
			});
			return btr;
		},
		bTd: function( optd ,td ) {
			var tdCtn={
				img:'<img src='+optd+' style="width:24px;height:24px;" alt="'+optd+'"/>', 
				color:'<span style="display:block;width:24px;height:24px;background:'+optd+'" title="'+optd+'"></span>'};
			t= typeof tdCtn[td] != 'undefined' && optd!=td ? tdCtn[td] : optd;
			var btd=$('<td>'+t+'</td>' ).addClass( td ).css({padding:'5px',border:'1px solid #acf','border-width':'0 1px','line-height':'24px'});
			return btd;
		},
		//
		//
		//
		bTrInit: function( tr ) {
			var bGroups= tr.find('td:last-child').text().split(',');
			tr.find('td:eq(0) img').click(function(){
				tr.addClass('mod');
				JQD.utils.dispIcnForm({title:'Modifier un lien partag\u00e9'}) ;
				$('#icnForm').addClass('mod').append( 
					$('<input type="hidden"/>').attr({id:'trId',value:tr.attr('id') }) );
				$('#icnttl').attr('value',tr.find('td.txt').text());
				$('#icnurl').attr('value',tr.find('td.url').text());
				$('#icncolor option').each(function(){
					if(	$(this).attr('value')==tr.find('td.color span').attr('title') )  $(this).attr('selected','selected');
					else  $(this).removeAttr('selected');
				});
				$.each(bGroups, function(bG) {
					$('input[type="checkbox"][value="'+bGroups[bG]+'"]').attr('checked','checked')
				});	
			});
			tr.find('td:eq(1) img').each(function(){
				var tdClose=$(this).parent('td');
				$('<a/>').addClass('group intr').attr({
					href: '#',
					name: $(this).parents('tr').attr('id'),
					rev: 'admin',
					title: tdClose.next('td').text(),
					rel: tdClose.next('td:eq(3)').text(),
					groups: bGroups
				}).append( $(this) ).click(function(){ JQD.utils.rmIcon( $(this) ) }).appendTo(tdClose)
			});
			tr.find('td:last-child').each( function() {
				var gtxt=$(this).text().replace(JQD.options.user.login+',','');
				$(this).html( gtxt );
			})
		},
		//
		//#JQD.ressEdit()
		//
		ressEdit: function() {
			var thR=['-','-','Nom du lien','Url','Img','Couleur','Propri\u00e9taire','Partage avec'];
			var ctnTab={th:thR, tb:JQD.options.ress };
			ctnTab.tb['esc']="rev iconsize left top";
			var optTable={'id':'tabDialog', 'class':'','width':700, 'title':'Gestion des liens partag&eacute;s', 'ctn':JQD.bTable(ctnTab)};
			var tBd = optTable.ctn.find('tbody');
			optTable.ctn.find('tbody tr').each(function(i){
				var trEdit=$(this),trId=$(this).attr('id');
				$(this).prepend( 
					$('<td/>').css({'line-height':'30px','font-size':'28px'})
					.append( $('<img src="core/images/gui/delete.gif"/>') ) 
				).prepend( 
					$('<td/>').css({'line-height':'36px'})
					.append(  $('<img src="core/images/gui/link_edit.png"/>') ) 
				);
				//
				JQD.bTrInit( $(this) );
			});
			JQD.bDialog(optTable);
			
		},			
		//
		//__/__/sauvegarde des prefs - fichier /home/[user]/Profile/PREFS_[user].xml__/__/
		//
		save_prefs_dev: function(koa, ki, ou) {
			//console.log('JQD.options.user["quicklaunch"] : ',JQD.options.user['quicklaunch']);
			var optionIcons = [];
			$('#desktop a.icon').not('.group').each(function() { 
				var el_a=$(this);
				optionIcons.push({
					txt 	: el_a.text(),
					url 	: el_a.attr('rel'),
					win 	: el_a.attr('href'),
					title   : el_a.attr('title'),
					rev 	: el_a.attr('rev'),
					top 	: el_a.position().top,
					left 	: el_a.position().left,
					color : el_a.find('img').css('backgroundColor'),
					img 	: el_a.find('img').attr('src')
				}) 
			});
			$.ajax({
				type: "POST",
				url: "core/action/save.php",
				cache: false,
				data: ({
					// TODO: tout passer dans JQD.options.user en amont
					file          : koa+'_'+ki,
					user          : ki,
					wallpaper     : $("#wallpaper").attr('src'),
					pos_wallpaper : $("#wallpaper").attr('class'),
					icons         : optionIcons,
					iconsize      : $("#desktop > a.icon:first img").width(),
					iconsfield    : JQD.options.user['iconsfield'],
					bgcolor       : $("body").css('background-color'),
					bgopct        : JQD.options.user['bgopct'],
					quicklaunch   : JQD.options.user['quicklaunch'],
					s_idart       : $("#s_idart") ? $("#s_idart").val() : '0',
					winsize       : JQD.options.user['winsize'],
					ki            : ki, // Non utilise
					ou            : ou
				}),
				dataType: "json",
				success: function(msg){
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					alert("XMLHttpRequest="+XMLHttpRequest.responseText+"\ntextStatus="+textStatus+"\nerrorThrown="+errorThrown);
				},
				complete : function(data, status) {
					if( status.match('error') ){
						//console.log("Erreur lors de l'enregistrement",data); 
						return; 
					}
					var r = JSON.parse( data.responseText );
					JQD.utils.message(' Vos pr&eacute;f&eacute;rences ont &eacute;t&eacute; enregistr&eacute;es');
				}
			});	
		},
		//
		// #JQD.save_icon
		//
		save_icon: function( opts) {
			$.ajax({
				type: "POST",
				url: "core/action/save.php",
				data: ({
                    ou: opts.ou,
                    icons: opts.icon,
                    myress: opts.myress,
                    groups: opts.where
				}),
				dataType: "json",
				success: function(msg){
                   // console.info('msg.filename: ',msg.filename)
					if( typeof msg.filename && msg.filename!='') JQD['tmpfile']=msg.filename;
                            // on inserre dans le tableau d'options
                            //rev iconsize left top
                            // si on est en mode modification, on recupere l'id de la ligne du tableau
                            opts.icon['name']= typeof opts.trId ? opts.trId : JQD.tmpfile;
                            
                           	var bTrNew = JQD.bTr( opts.icon, 'rev iconsize left top name' ).prepend($('<td/>').css({'line-height':'24px'}).append(  $('<img src="core/images/gui/delete.gif"/>') ) ).prepend($('<td/>').append(  $('<img src="core/images/gui/link_edit.png"/>') ) );
                            if( typeof opts.mode && typeof $('#trId') && opts.mode=='mod') {
                            	 $('#tabDialog table tbody').find('tr.mod').animate({opacity:0},1000,function(){
                            	 	JQD.rm($('#trId').val(), JQD.options.user.login, 'all','force');
                            	 	$(this).hide();
                            	 	JQD.bTrInit( bTrNew.attr({id:JQD.tmpfile}).insertAfter( $(this) ) );
                            	 	$(this).remove();
                            	 });
                           }
                           else {
                            	 //console.info('id: ',JQD.tmpfile);
                            	bTrNew.attr({id:JQD.tmpfile}).appendTo( $('#tabDialog table tbody'));
								JQD.bTrInit( bTrNew );
							}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					//console.error("XMLHttpRequest= ",XMLHttpRequest);
					//console.error("textStatus= ",textStatus);
					//console.error("errorThrown= ",errorThrown);
				},
				complete : function(data, status) {
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
			this.reloadnav  = $('<a href=\"#\"/>');
			this.nextnav    = $('<a href=\"#\"/>');
			this.prevnav    = $('<a href=\"#\"/>');
			this.barnav     = $('<span class=\"float_left window_barnav\"/>');
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
			).prepend( $('<span/>').addClass('float_left close').click(function(){ $('#window_lcs_'+win).remove();$(this).parent().remove()}).hide() )
			.mouseover( function() {
					$(this).find('span.close').show();
				}).mouseout( function() {
					$(this).find('span.close').hide();
				});
			
			this.navigate =  $('<div/>').addClass('float_left').css({position: 'relative'})
				.append($('<input type="text"/>').attr({id: 'wp_url', name: 'wp_url'}))
				.append(
					$('<img/>').attr('src', 'core/images/gui/arrow-right-blue_16.png')
					.css({position:'absolute',top:'3px',right:'-2px',display:'none'})
				);
			// on inhibe les menus ouverts ?? 
			// Utilite a controler
			JQD.utils.clear_active();
			
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
					iframeFix: true, //
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
						.find('a').attr({title: $(this).text()}).each(function(){
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
				
			// barre de navigation
			//JQD.insertBarNav(this.iframe,  win );		
			this.barnav.append(
				$('<button/>').addClass( 'button' )
				.append(
					$('<img/>')
					.attr({src: 'core/images/icons/user-home.png', alt:'Retours à l\'accueil'})
				)
				.click( function( event )
				{
					event.preventDefault();
					frames['ifr_lcs_'+win].location.href = url;
				} )
			)
			.append(
			$('<button/>').addClass('button')
				.append(
					$('<img/>')
					.attr({src: 'core/images/gui/nav-prev.png', title:'Page précédente'})
				)
				.click( function( event )
				{
					event.preventDefault();
					history.back();
				} )
			)
			.append(
			$('<button/>').addClass('button')
				.append(
					$('<img/>')
					.attr({src: 'core/images/gui/nav-next.png', title:'Page suivante'})
				)
				.click( function( event )
				{
					event.preventDefault();
					frames['ifr_lcs_'+win].history.forward()
				} )
			).append(
			$('<button/>').addClass('button')
				.append(
					$('<img/>')
					.attr({src: 'core/images/gui/reload.png', title:'Actualiser'})
				)
				.click( function( event )
				{
					event.preventDefault();
				if (typeof window.frames['ifr_lcs_'+win].location)	frames['ifr_lcs_'+win].location.reload();
				} )
			).append(
			$('<button/>').addClass('button')
				.append(
					$('<img/>')
					.attr({src: 'core/images/gui/printer.png', title:'Imprimer'})
				)
				.click( function( event )
				{
					event.preventDefault();
					if (typeof window.frames['ifr_lcs_'+win].innerHTML && window.frames['ifr_lcs_'+win].innerHTML != "") {
			            window.frames['ifr_lcs_'+win].focus();
			            window.frames['ifr_lcs_'+win].print();
			        } else {
			            return false;
			        }
				} )
			)
 			.appendTo(this.top);


			// traitement du iframe
			var count_load=0;
			this.iframe.attr('src', url).load(function()  {
				if ( url.match(/squirrelmail/g) ) {
					//alert('squirrelmail');
					$(this).show();
					$('#lcspinner').remove();
					return false;
				}
				// on passe le contenu dans une variable
				el=$(this).contents();


				// redimenssionnement en fonction du choix du user
				// cas du choix "largeur de l'appli contenue"
				// TODO: A supprimer
				//if( $('#tmp_winsize').val() == 'content' && count_load == 0 && !url.match(/^http/) && !url.match(/gepi/g)  )
				if( JQD.options.user.winsize == 'content' && count_load == 0 && !url.match(/^http/) && !url.match(/gepi/g)  )
					 $(this).closest('.window').css('min-width', '650px').width( el.width() + 25 );
				// cas du choix "Plein ecran"
				// TODO: A supprimer
				//if( $('#tmp_winsize').val() == 'fullwin' && count_load == 0 ) JQD.window_resize( $(this) );
				if( JQD.options.user.winsize == 'fullwin' && count_load == 0 ) JQD.window_resize( $(this) );
				// suppression du spinner
				$('#lcspinner').remove();
				// et on affiche
				$(this).show();
				count_load++;
					
				// on essaie de fixer le bug en cas de clic sur une ancre dans une page iframe
				//el.find('body').localScroll();
					
				// on ne fait rien si gepi ou si on est sur un autre serveur
				if ( url.match(/gepi/g) || url.match(/^http/) ) return false;
				
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
			var text = $(el).hasClass('icon') ? $(el).text() : $(el).clone().find('*').remove().end().text();
			var title = $(el).attr('title');
			var img = $(el).find('img').attr('src');
			if (url.match(/^#/)) {
				url = $(el).attr('rel');
				title = text;
			}
			if( url.match(/gepi/g) || url.match(/Claroline/g) ) {
                            //alert(url);
                            window.open( url);
                            return false;
			}
			var i=0;
			var winexist=0;
			var i_frame = "";
			if(url.match(/prefs/) && $('#user_form_prefs').length>0) {
				JQD.window_flat();
				$('#user_form_prefs').closest('div.window').addClass('window_stack').show();
				JQD.utils.clear_active();
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
				JQD.utils.clear_active();
			}
		},
		//
		// JQD.pgaccueil( opt ) : appel de la page d'accueil hors connexion'
		// TODO : Passer la largeur de page (iframe) en parametre
		pgaccueil: function(opt) {
                    var u_a = opt.url_accueil;
                    // TODO: commenter les logs
                    //console.log('opt.idpers : ', opt.idpers );
                    //console.log('url_accueil : ', u_a );
        	    if (!u_a.match("auth\.php|accueil\.phps|monlcs") ) {
                        u_a = u_a.match(/^http/) ? u_a : '../'+u_a;
        		setTimeout(function(){
                            JQD.build.btopr( opt );

                            var spipAccueil = $('<iframe/>').hide()
                            .addClass('abs')
                            .attr({src: u_a,width:'960'}).load(function(){
                               // console.info('largeur fenetre: ', $(this).contents().width().length>0  ? 'yex' :'no');
                                //console.info('largeur document: ', $('#desktop').width());
                                $(this).show()
                                .css({
                                	opacity:0,
                                    top:'23px',
                                    'min-width': '960px',
                                    width:  u_a.match(/^http/) ? '960px' : $(this).contents().width()+50,
                                    height: '100%',
                                    bottom: '0px',
                                    left : ($('#desktop').width()-$(spipAccueil).width()-50)/2
                                })
                                .animate({height:'-=24px'},100).animate({opacity:1},1500)
                            }).insertAfter('#desktop');
                            $('#bar_bottom').hide();
                            $('#bar_top').append(
                                JQD.build.btopr(opt)
                            ).find('a.auth').prepend(
                                    $('<span/>').addClass('connect').html('&nbsp;&nbsp;&nbsp;Se connecter >>')
                                    .click(function(){JQD.logform('../lcs/auth.php')})
                                );
			},100)
                    }
                    else JQD.logform('../lcs/auth.php') ;
		},
		//
		// JQD.btr : bar-top right side -
		//
		btr : {  
			date: {
				txt:"",
				id:"clock",
				url: "../lcs/statandgo.php?use=Agendas",
				rev: 'agendas',
				cls: 'date'
			},
			reload: {
				txt:"Actualiser le bureau",
				id:"reload",
				url: "./",
				rev: '',
				cls: 'reload'
			},
			userinf: {
				txt:"",
				id:"btrUsrInf",
				url: "#",
				rev: '',
				cls: ''
			},
			maint: {
				txt:"Demande d'assistance informatique",
				id:"maintinfo",
				url: "../Plugins/Maintenance/demande_support.php",
				rev: 'maintinfo',
				cls: 'maintinfo open_win'
			},
			wmail: {
				txt:"Envoyer un message",
				id:"mess",
				url: "../squirrelmail/src/compose.php?mailbox=INBOX&startMessage=1",
				rev: 'webmail',
				cls: 'wmail open_win'
			},
			annu: {
				txt:"Trouver un utilisateur, une classe, un groupe...",
				id:"found",
				url: "../Annu/search.php",
				rev: '',
				cls: 'found open_win'
			},
			savdesk: {
				txt:"Enregistrer votre bureau",
				id:"save",
				url: "#",
				rev: '',
				cls: 'save'
			},
			spaces: {
				txt:"",
				id:"otBuro_2",
				url: "#",
				rev: '',
				cls: 'spaces'
			}
		},
		//
		//
		//
		desktop_space: function(){
			// gestion des bureaux secondaires // A REVOIR ENTIEREMENT
			var left_o = $('#desktop').width(),
			inettuts = $('<div id="inettuts"/>').addClass('abs').append(
				$('<iframe/>').attr({name: 'ifr_iLcs'}).css({width:'100%', height:'100%'}) 
			).prependTo('body'),
			monlcs = $('<div id="monLcs"/>').addClass('abs').append(
				$('<iframe/>').attr({name: 'ifr_lcs_monlcs'}).css({width:'100%', height:'100%'})
			).prependTo('body');
			inettuts.animate({left: left_o+'px'}).hide();
			monlcs.animate({left: -left_o+'px',right: left_o+'px'}).hide();

			/*
			$('#otBuro_2').click(function(){
				if ( $(this).hasClass('monlcs_dtq') ) return false;
				$('#otBuro_1 ul').show('fast');
			});
			*/
			$('li.spaces>ul>li>a').click(function(){
				if($(this).not('.space')){
				JQD.utils.clear_active();
					//console.log('spaces:',$(this).find('span').text())
					$('#inettuts, #monLcs').show();
					var ind=$(this).parent().index();
						var _WPP  = $('body').find('#wallpaper');
						var _WPPO = _WPP.offset();
						// inettuts
					/**/if((ind==2) && ($('#inettuts').position().left!=0)){
						// reduction des fenetres window_full
						$('.window_full').each( function(){
							JQD.window_resize(this);
						});
						if($('#wallpaper_b').length == 0){
							var _WPP_b = $('#wallpaper').clone().attr('id', 'wallpaper_b').css({'position': 'absolute', 'left': _WPPO.left}).hide().prependTo('body');
						}
						//$('#desktop, #wallpaper').animate({left:  -left_o, right: left_o},1500);
						$('#desktop').animate({left: -left_o+'px'},1500);
						$('#wallpaper').css('position','absolute').animate({left: '-='+left_o},1500, function(){});$('#wallpaper_b').show();
						$('#monLcs').animate({left: (-left_o*2), right: (left_o*2)},1500, function(){$('#monLcs iframe').removeAttr('src');});
						$('#inettuts').animate({left: 0, right: 0},1500, function(){
							$('#inettuts iframe').attr('src', 'core/inettuts.php')
							.css('height',$(this).height()-3+'px');
						});
							$('ul.bar_top_right>li').hide('slow');
							$('ul.bar_top_right >li:eq(0), ul.bar_top_right >li:eq(2), ul.bar_top_right li.spaces').show('slow');
					} else if((ind==1)  && ($('#monLcs').position().left!=0)) {
						$('#iLcsMenu').hide();
						if($('#wallpaper_b').length == 0){
							var _WPP_b = $('#wallpaper').clone().attr('id', 'wallpaper_b').css({'position': 'absolute', 'left': _WPPO.left}).hide().prependTo('body');
						}
						$('#desktop').animate({left: left_o+'px'},1500);
						$('#wallpaper').css('position','absolute').animate({left: '+='+left_o},1500, function(){});$('#wallpaper_b').show();
						$('#inettuts').animate({left: (2*left_o)+'px', right: (-2*left_o)+'px'},1500, function(){$('#inettuts').hide();});
						$('#monLcs').show().animate({left: 0, right: 0},1500, function(){
							$('#monLcs iframe').attr('src', '../monlcs/');
							$('ul.bar_top_right>li').hide('slow');
							$('ul.bar_top_right >li:eq(0), ul.bar_top_right >li:eq(2), ul.bar_top_right li.spaces').show('slow');
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
					.css("left",(parseInt(offset.left) + (26*nel) - wtip+12) + "px")
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
		 //
		 //#JQD.logform() : Affichage du formulaire de login
		 //
		 logform: function(u){
			var logdialog=$('<div id="logdialog"/>').attr({'title':'Formulaire de connexion'}).append(
				$('<iframe/>').attr({id:'logFrame',src:''}).css({width:'100%', height: '95%'})
				.load(function() {
					$(this).contents().find('body').css({'font-size':'0.75em'}).find('div.pdp').hide()
				})
			).hide().appendTo('#desktop');
			//console.log('u: ',u);
			setTimeout(function(  ) {
				$('#logdialog').dialog({
					width: 530,
					modal: true,
					height:350 ,
					open: function(event, ui) {$('#logFrame').attr({src:u}).show()},
					close: function(event, ui) {
						$('#logFrame').empty().hide();window.location = './';}
				});
			},1000);
		},
		//
		//#JQD._(str, args) : Permet d'appeler la chaine de lang plus simplement
		// alert(JQD._('Year')); // returns "Année"
		_:function(str, args){
     	   return $.i18n('jqd', str, args); 
		},
		//
		//#JQD.connect() : Affichage de base si non connecte ou mot de passe inchange
		//
		connect: function( opts, st){
			//console.log('Options - ', opts)
			if(parseInt(opts.idpers) == 0 ) {
                JQD.build.btop() ;JQD.build.bbttm();JQD.init.wpp( opts );
                if ( opts.url_accueil !='' &&  opts.url_accueil !='../lcs/auth.php' && opts.url_accueil !='../lcs/accueil.php') JQD.pgaccueil( opts );
                else JQD.logform('../lcs/auth.php') ;
            }
			else if(opts.pwchg == 'N' ) {
               //JQD.connect(opts, 'pwchg');
                JQD.build.btop() ;JQD.build.bbttm();JQD.init.wpp( opts );
                JQD.logform('../Annu/mod_pwd.php')
                $('#bar_top').append( JQD.build.btopr(opts) );
                JQD.logform('../lcs/auth.php') ;
            }
			else JQD.mk(  opts  );
		},
		//
		//#JQD.settings : chargement de prefs user
		//
		settings : function() {
			var o = {};
			$.ajax({
				type: "POST",
				url: 'core/action/load_settings.php',
				dataType: 'json',
				data: ({
					fn:'all',
					user: true
				}),
				error: function ( data, status, error ) {
					//console.error(status, error);
				},
				success: function( data, status ) {
					//console.info('JQD.defaults = ', JQD.defaults);
					//console.info('data = ', data);
					JQD['options']  = $.extend( true, {}, JQD.defaults, data );
					//console.info('JQD.options = ', JQD.options)
                    JQD.connect( JQD.options );
				}
			});
                      //  return o.result;
		}
	};
// Pass in jQuery.
})(jQuery, this);