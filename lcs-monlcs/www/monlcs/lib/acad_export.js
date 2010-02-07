
        var compte_ress=0;
	var liste_ress;
	function scen_acad_pub() {
                if (id_scen == -1) {
                        alert('Il faut d\'abord choisir ou créér un nouveau scénario.');
                        return false;
                }

                var urlto = 'acad_publish.php';
                var params = '?id_scen='+id_scen;

                new Ajax.Request(urlto,{ method: 'post', parameters: params, onComplete: function(requester) {
                        var goto = requester.responseText;
                        var ajaxWindCmd777=dhtmlwindow.open(
				'ajaxWindCmd777',
				'iframe',goto,
				'Transmission du scénario',
				'width=505px,height=410px,left=350px,top=50px,resize=1,scrolling=1'
				);
                        pretty_cmd(ajaxWindCmd777);

                }});


        }

        function liste_acad() {

                new Ajax.Updater('acad','acad_import.php',{ method: 'post', parameters: params, onComplete: function(requester) {

                        var ajaxWindCmd778=dhtmlwindow.open(
                        'ajaxWindCmd778',
                        'div',
                        'acad',
                        'Dépot académique',
                        'width=800px,height=330px,left=35px,top=70px,resize=1,scrolling=1,center=0'
                        );
                        pretty_cmd(ajaxWindCmd778);
                        init_synchro(ajaxWindCmd778);

                }});



        }

	function add_window(id,type,content,titre,x,y,w,h,vignette) {
		var dhtml_w;
		//alert(vignette);
		//alert(id+','+type+','+content+','+x+','+y+','+w+','+h);
		if (type != 'note') {
			var source;
			if ('' != vignette)
				source = vignette;
			else
				source = content;
		dhtml_w=dhtmlwindow.open(
                        	'ajaxWind'+id,
	                        'iframe',
        	                source,
	                        titre,
                        	'width='+w+'px,height='+h+'px,left='+x+'px,top='+y+'px,resize=1,scrolling=1,center=0'
                        );
		}
		else {
			dhtml_w=dhtmlwindow.open(
                        	'ajaxWindNote'+id,
	                        'inline',
        	                HTMLDecode(content),
	                        titre,
                        	'width='+w+'px,height='+h+'px,left='+x+'px,top='+y+'px,resize=1,scrolling=1,center=0'
                        );
			
		}
              pretty_cmd(dhtml_w);
   
		
	}

	function patch() {
			purge();
			liste_ress = new Array();			
			for (var i=0;i<compte_ress;i++) {
				var id ='ress'+parseInt(i+1);
				
				//var frame_id = "_iframe-win_"+id;
			
				var type = $(id+'_type').innerHTML;
			
				if (type == 'ressource') {
					
					var x = parseInt($(id+'_x').innerHTML);
					var y = parseInt($(id+'_y').innerHTML);
					var z = parseInt($(id+'_z').innerHTML);
					var w = parseInt($(id+'_w').innerHTML);
					var h = parseInt($(id+'_h').innerHTML);
					var vignette;
					if ($(id+'_vignette')) 
						vignette = ($(id+'_vignette').innerHTML);
					else
						vignette = '';

					var titre = $(id+'_titre_ress').innerHTML;
					var url = $(id+'_url').innerHTML;
					url = url.replace("&amp;","&");
					// var Expression = new RegExp("swf","g");
				       //if ( Expression.test(url) )
					//	url = './giveCleanFlash?url='+url;
					var brique ={id: id,type: type,content: url,titre: titre,x: x,y: y,z: z,w: w,h: h,vignette: vignette}
					liste_ress[i] = brique;
					add_window(id,type,url,titre,x,y,w,h,vignette);
					
				}

				if (type=='note') {
					var x = parseInt($(id+'_x').innerHTML);
					var y = parseInt($(id+'_y').innerHTML);
					var z = parseInt($(id+'_z').innerHTML);
					var w = parseInt($(id+'_w').innerHTML);
					var h = parseInt($(id+'_h').innerHTML);
					var msg = HTMLDecode($(id+'_note_msg').innerHTML);
					var titre = $(id+'_note_title').innerHTML;
					var brique ={id: id,type: type,content: msg,titre: titre,x: x,y: y,z: z,w: w,h: h}
					liste_ress[i] = brique;
					add_window(id,type,msg,titre,x,y,w,h,'');
					

				}
			}
		
		}

	function HTMLDecode(wText){
			if(typeof(wText)!="string"){
			wText=wText.toString();};
		
			wText=wText.replace(/&lt;/g, "<") ;
			wText=wText.replace(/&gt;/g, ">") ;
			wText=wText.replace(/\\'/g, "'") ;
			return wText;
		};



        function voirAcadScen(jeton) {
		Element.hide('div_acad_import');
		var params='?jeton='+jeton;
                new Ajax.Updater('div_acad_import','viewAcadScen.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			//alert(requester.responseText);
			//eval(requester.responseText);
			compte_ress = parseInt($('ress_number').innerHTML);
			patch();
		}});

        }
	
	function saveScenarioAcad() {
		var cible ="";
		var texte = '';
		var compte = 0;
		for (var i=0;i<compte_ress;i++){
			if ($('check_ress_'+i).checked) {
				compte++;
			}
		}
		
		if ( 0 == compte ) {
			alert('Il faut choisir au moins une ressource !');
			return(false);
		}
		
		var count = $('toBox').options.length;
								cible ='#';
								for (var j=0;j<count;j++) {
									cible+= $('toBox').options[j].value+'#';
		}

		if ('#' == cible){
			alert('Il faut choisir au moins une cible !');
			return(false);
		}
				
		new Ajax.Request('giveMaxScen.php',{ method: 'post', onComplete: function(requester) {
                                        if (requester.responseText) {
					
						for (var i=0;i<compte_ress;i++){
							if ($('check_ress_'+i).checked) {
						
								var id_scen = parseInt(requester.responseText)+1;
								var params='?id_scen='+id_scen;
								var scenario_titre = $('titre').value;
								var scenario_descr = $('scenario_description').innerHTML;
								var scenario_matiere = $('matiere').value;
								params += "&titre=" + escape(cleanaccent(scenario_titre));
								params += "&descr=" + encodeURI(scenario_descr);
								params += "&x=" + liste_ress[i].x;
								params += "&y=" +liste_ress[i].y;
								params += "&z=" +liste_ress[i].z;
								params += "&w=" +liste_ress[i].w;
								params += "&h=" + liste_ress[i].h;
								params += "&type=" + liste_ress[i].type;
								params += "&min=" + 'N';
								params += '&matiere='+$('matiere').value;
								params += '&titre_ress='+liste_ress[i].titre;
								if ('note' == liste_ress[i].type)
									params += '&content='+encodeURI(liste_ress[i].content);
								else
									params += '&content='+escape(liste_ress[i].content);
								
								params += '&vignette='+escape(liste_ress[i].vignette);
								params += '&cible='+escape(cible);
								//alert(params);
								new Ajax.Request('saveScenAcad.php',{ method: 'post', parameters: params, onComplete: function(requester) {
									//alert(requester.responseText);
									//getTabData('scenario_choix');			
								}});
							}
						}					
					}
					alert('L\'import du scénario s\'est bien déroulé.');
					
		}});
		
	}
	

	function fetchScen() {

                var params ='?tab='+tab_active+'&mode='+mode+'&user='+user;
                Element.show('spinner');
                Element.hide('ressourcesAcad');
                new Ajax.Updater('ressourcesAcad','processScenAcad.php',{ method: 'post', parameters: params, onComplete: function(requester) {
        	try {        
	                 var ajaxWindCmd1=dhtmlwindow.open(
				'ajaxWindCmd1',
				'inline',
				$('ressourcesAcad').innerHTML,
				'Importer un scenario academique',
				'width=305px,height=410px,left=350px,top=70px,resize=1,scrolling=1,center=0'
			);

                        createMovableOptions('fromBox','toBox',300,100,'Groupes disponibles','Groupes selectionn&eacute;s');
                        pretty_cmd(ajaxWindCmd1);
			$('matiere').value = $('scenario_matiere').innerHTML;
                        $('titre').value += 'Acad_'+$('scenario_titre').innerHTML;
                        $('scen_descr').value = $('scenario_description').innerHTML;
                        var flux_ress = "";
                        var ress_params="";
                        for (var i=0;i<compte_ress;i++){
                        //alert(i);
                           flux_ress += '<br />&nbsp;<span><input type="checkbox" id="check_ress_'+parseInt(i)+'"></input><img id="chk_ress_'+parseInt(i)+'" style="height: 1.2em;"></img>&nbsp;'
			             + '<span id="ress_titre'+parseInt(i)+'">'+liste_ress[i].titre+'</span>';
                                
                                //lancer une requete pour voir si les ressources sont presentes sinon les ajouter
                        }
			$('scen-toto').innerHTML = flux_ress;
			//alert(flux_ress);
			//alert(compte_ress);
			 for (var i=0;i<compte_ress;i++){
				ress_params = '?titre='+encodeURI(liste_ress[i].titre)
					     +'&type='+encodeURI(liste_ress[i].type)
					     +'&id='+parseInt(i);  
					if ('note' == liste_ress[i].type)
							ress_params += '&content='+encodeURI(liste_ress[i].content);
					else {
							//liste_ress[i].content = escape(liste_ress[i].content);
							//liste_ress[i].content = liste_ress[i].content.replace('%25','%');
							//liste_ress[i].content = liste_ress[i].content.replace('%u2329','lang');
							
							//alert(unescape(liste_ress[i].content));
							ress_params =  ress_params + '&content='+escape(liste_ress[i].content);
					}		
					    
					     
					   
                                //alert(ress_params);
                                new Ajax.Request('chkRessPresent.php',{ method: 'post', parameters: ress_params, onComplete: function(requester) {
                                        if (requester.responseText) {
						//alert(requester.responseText);
						eval(requester.responseText);
						}
                                }});
                        }

                        Element.hide('spinner');   
                
		} catch (err) {
			alert(err);
		}
		}});
        }

