<?php 
	require "/var/www/monlcs/includes/secure_no_header.inc.php";
	require "/var/www/lcs/includes/headerauth.inc.php";
	//die($baseurl) 
?>
	var encours='';
	var mode = 'user';
	var sauver_note = false;
	var quiC = '?'; 
	var tab_active;
	var old_width;
	var old_height;
	var id_scen=-1;
	var vignette_mode = 'rien';

	var maxScreen;
	var maxX;
	var maxY;
	var minY;
	var minX;
	var posx = 0;
	var posy = 0;

	var msg="";
	var floating_window_skin = 1;
	var up = true;

	function addslashes( str ) {
    		return (str+'').replace(/([\\"'])/g, "\\$1").replace(/\0/g, "\\0");
	}

	function stripslashes(str) {
		str=str.replace(/\\'/g,'\'');
		str=str.replace(/\\"/g,'"');
		str=str.replace(/\\\\/g,'\\');
		str=str.replace(/\\0/g,'\0');
	return str;
	}



	function noaccent(chaine) {
		temp = escape(chaine).replace(/[àâä]/gi,"a")
  		temp = temp.replace(/[éèêë]/gi,"e")
	     	temp = temp.replace(/[îï]/gi,"i")
  		temp = temp.replace(/[ôö]/gi,"o")
  		temp = temp.replace(/[ùûü]/gi,"u")
  		temp = temp.replace(/[ç]/gi,"c")
  		temp = temp.replace(/[']/gi,"_")
		
		//encodage url	

		temp = temp.replace(/%E2/gi,"a")
		temp = temp.replace(/%E4/gi,"a")
		temp = temp.replace(/%E0/gi,"a")
                temp = temp.replace(/%E9/gi,"e")
		temp = temp.replace(/%E8/gi,"e")
		temp = temp.replace(/%EA/gi,"e")
		temp = temp.replace(/%EB/gi,"e")
		temp = temp.replace(/%EE/gi,"i")
		temp = temp.replace(/%EF/gi,"i")
		temp = temp.replace(/%F4/gi,"o")
		temp = temp.replace(/%F6/gi,"o")
		temp = temp.replace(/%FB/gi,"u")
		temp = temp.replace(/%FC/gi,"u")
		temp = temp.replace(/%F9/gi,"u")
		temp = temp.replace(/%5C/gi,"/")
		temp = temp.replace(/%20/gi,' ')

		//encodage unicode
		temp = temp.replace(/%u0300/gi,"")
		temp = temp.replace(/%u0302/gi,"")
		temp = temp.replace(/%u0308/gi,"")
		temp = temp.replace(/%u0301/gi,"")

		return (temp)
	}

	function cleanaccent(chaine) {
		if (!chaine)
			return;
  		temp = chaine.replace(/[à]/gi,"&agrave;")
		temp = temp.replace(/[â]/gi,"&acirc;")
		temp = temp.replace(/[ä]/gi,"&auml;")
		temp = temp.replace(/[é]/gi,"&eacute;")
		temp = temp.replace(/[è]/gi,"&egrave;")
		temp = temp.replace(/[ê]/gi,"&ecirc;")
		temp = temp.replace(/[ë]/gi,"&euml;")
		temp = temp.replace(/[î]/gi,"&icirc;")
		temp = temp.replace(/[ï]/gi,"&iuml;")
		temp = temp.replace(/[ô]/gi,"&ocirc;")
		temp = temp.replace(/[ö]/gi,"&ouml;")
		temp = temp.replace(/[ù]/gi,"&ugrave;")
		temp = temp.replace(/[û]/gi,"&ucirc;")
		temp = temp.replace(/[ü]/gi,"&uuml;")
		temp = temp.replace(/[ç]/gi,"&ccedil;")



  		return temp
	}
	

	function trim (myString) 	{
		return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
	} 

	

	function gen_vignette() {

      		$('upload_panel').style.display='none';
       	vignette_mode = 'thumbalizr';    
	}

	function gen_upload() {
      
		$('upload_panel').style.display='block';
       	vignette_mode = 'upload';
	}

	function gen_clean() {
      	
		$('upload_panel').style.display='none';
       	vignette_mode = 'rien';
	}

	function check_vignette() {
    
		//if ($('siteTV').checked) {
		//	var urlView = 'http://www.lesite.tv/g_images/298_175/'+$('urlAdd').value+'.jpg';
		
              	//	var ajaxWindCmd6=dhtmlwindow.open('ajaxWindCmd6','iframe',urlView,'Miniature','width=250px,height=200px,left=250px,top=150px,resize=1,scrolling=1,center=0');	
		//	pretty_cmd(ajaxWindCmd6);
 			

		//	return true;
		//}

		if (vignette_mode == 'rien') {
			alert('Aucune vignette');
       		return true;
		}
    
   		if (vignette_mode == 'thumbalizr') {
			if ($('urlAdd').value != '') {
       			var urlView = 'giveCleanVignette.php?url='+escape($('urlAdd').value);
              		var ajaxWindCmd6=dhtmlwindow.open('ajaxWindCmd6','iframe',urlView,'Miniature','width=250px,height=200px,left=250px,top=150px,resize=1,scrolling=1,center=0');	
				pretty_cmd(ajaxWindCmd6);
 			}
       	return true;
		}
    		if (vignette_mode == 'upload') {
			//alert($('filename').value);
       		var urlView = './vignettes/'+$('filename').value;
       		var ajaxWindCmd6=dhtmlwindow.open('ajaxWindCmd6','iframe',urlView,'Miniature','width=250px,height=200px,left=250px,top=150px,resize=1,scrolling=1,center=0');	
			pretty_cmd(ajaxWindCmd6);

 			return true;
		}
	}

	function jsUpload(upload_field) {
    		var re_text = /\.gif|\.png|\.jpg|\.jpeg/i;
                var filename = upload_field.value;
		
    		if (filename.search(re_text) == -1) {
        		alert("Il faut fournir une image gif png jpg ou jpeg");
        		upload_field.form.reset();
        		return false;
    		}

    		upload_field.form.submit();
   		var image;
    		var temp = new Array();
    		temp = filename.split('\\');
    		if (temp.length > 1 )
    			image = temp[temp.length-1];
    		alert("Le fichier est en cours de chargement. Ceci est plus ou moins long en fonction de la taille du fichier. Patientez SVP. Merci.");
    		return true;
	}

	function jsUpload2(upload_field) {
    		var re_text = /\.pdf|\.ggb|\.swf|\.flv|\.mm/i;
                var filename = noaccent(upload_field.value);
		filename = filename.replace(/ /gi,'_')

		if (filename.search(re_text) == -1) {
        		alert("Il faut fournir un fichier pdf ou ggb ou swf ou flv ou mm"); 
        		upload_field.form.reset();
        		return false;
    		}

		var temp = new Array();
    		temp = filename.split('.');
    		if (temp.length > 1 )
    			extension = trim(temp[temp.length-1]);

		if ('pdf' == extension ) {
			extension = 'swf';
			filename = filename.replace('.pdf','_pdf.swf');
		}

		//ne recuperer que le fichier.extension

    		filename = filename.replace('\\','/');

		temp = filename.split('/');
    		if (temp.length > 1 )
			filename = temp[temp.length-1];

		var url = './chkRessHome.php';
		
		var fichier;
		if ( 'pr' != extension)
		 	fichier = '/~'+user+'/monlcs_'+extension+'/'+addslashes(filename);
		else
		 	fichier = '<?php echo $baseurl; ?>~'+user+'/monlcs_'+extension+'/'+addslashes(filename);
			
		var params = 'file='+escape(fichier);
			
		//test ajax si le fichier existe deja
			new Ajax.Request(url,{ method: 'post', parameters: params, onComplete: function(requester) {
				if ('1' != trim(requester.responseText)) {
					upload_field.form.submit();
    					$('urlAdd').value = stripslashes(fichier);
					alert("Le fichier est en cours de chargement. Ceci est plus ou moins long en fonction de la taille du fichier. Apres le click, merci de patientez SVP.");
				} else {
					var rep = confirm("Le fichier se trouve deja dans votre home utilisateur souhaitez vous le mettre a jour ? S'il s'agit d'un fichier different mais portant le meme nom songez a le renommer ");
                                	if (rep) {
						upload_field.form.submit();
    						$('urlAdd').value = stripslashes(fichier);
						alert("Le fichier est en cours de chargement. Ceci est plus ou moins long en fonction de la taille du fichier. Patientez SVP. Merci.");

					}	
				}
	        		upload_field.form.reset();

			}});
		


    		return true;
	}
	

	function maxZindex() {
		var max = 0;
		var z=liste_fen_actives();
		
	 	for (var i=0;i<z.length;i++ ) {
			var reg = new RegExp("ajaxWindCmd", "i");
			if (reg.exec(z[i].id) == null)   {
				//alert($(z[i]).style.zIndex)
				if (parseInt($(z[i]).style.zIndex) >  max) {
					max = parseInt($(z[i]).style.zIndex);
				}
			}
		}
		
		return(max);	
	}

	function getMaxCoords() {
		
		var z=liste_fen_actives();
      
      		if (!$(z[z.length-1])) {
       		minY = 60;
              	minX = 0;
              	maxX = minX;
              	maxY = minY;
		} else { 
			minX = parseInt($(z[z.length-1]).style.left);
			minY = parseInt($(z[z.length-1]).style.top);
       		maxX = minX+parseInt($(z[z.length-1]).style.width);
			maxY = minY+parseInt($(z[z.length-1]).contentarea.style.height);
      		}
	}

	function showRightMenu() {

		$('virtualMenu').innerHTML = menuobj.innerHTML;
		Element.toggle('virtualMenu');
	}

	function gest_tab(id) {
		var params = '?id='+id;
		var url='/monlcs/gestOnglets.php';
		new Ajax.Updater('gest_tab',url,{ method: 'post', parameters: params, onComplete: function(requester) {
			var ajaxWindCmd998=dhtmlwindow.open('ajaxWindCmd998','div','gest_tab','Gestion des onglets','width=330px,height=230px,left=250px,top=150px,resize=1,scrolling=1,center=1');	
			pretty_cmd(ajaxWindCmd998);

		}});
	}

	function gest_tab_etab(id) {
		var params = '?id='+id;
		var url='/monlcs/gestOngletsEtab.php';
		new Ajax.Updater('gest_tab',url,{ method: 'post', parameters: params, onComplete: function(requester) {
			var ajaxWindCmd998=dhtmlwindow.open('ajaxWindCmd998','div','gest_tab','Gestion des onglets','width=330px,height=230px,left=250px,top=150px,resize=1,scrolling=1,center=1');	
			pretty_cmd(ajaxWindCmd998);

		}});
	}

	function remove_tab(id) {
		var rep = confirm('Etez vous certain de supprimer cet onglet? Cette opération est irréversible!');
		if (rep == true) {
			var params = '?id='+id;
			new Ajax.Request('removeTabs.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			if (requester.responseText != 'erreur') {
				new Ajax.Updater('mainMenu','giveOnglets.php',{ method: 'post', onComplete: function(requester) {
					new Ajax.Updater('submenu','giveTabs.php',{ method: 'post', onComplete: function(requester) {
						initMenu();
                                  		enableOngletsDblC();
						enableTabsHandler();
						tab_active='lcs';
						getTabData('bureau');
					}});
				}});
			}	
			}});
   		}
	}

	function remove_sub_tab(id) {
		var rep = confirm('Etez vous certain de supprimer ce sous-menu? Cette opération est irréversible!');
		if (rep == true) {
			var params = '?id='+id;
			new Ajax.Request('removeSubTabs.php',{ method: 'post', parameters: params, onComplete: function(requester) {
				alert(requester.responseText);
				if (requester.responseText != 'erreur') {
					new Ajax.Updater('mainMenu','giveOnglets.php',{ method: 'post', onComplete: function(requester) {
						new Ajax.Updater('submenu','giveTabs.php',{ method: 'post', onComplete: function(requester) {
							initMenu();
                                  			enableOngletsDblC();
							enableTabsHandler();
							tab_active='lcs';
							getTabData('bureau');
						}});
					}});
				}	
			}});
   		}
	}


	
	function add_sub_tab(id) {
		
		var caption = cleanaccent($('caption_new_tab').value);
		if (caption == '?') {
			alert('Il faut changer le nom du sous-menu');
			return false;
		}
		if (trim(caption) == '') {
			alert('Il faut entrer un nom du sous-menu non vide!');
			return false;
		}
		var params = '?id_onglet='+id+'&caption='+escape(caption);
		new Ajax.Request('addSubTabs.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			alert(requester.responseText);
			if (requester.responseText != 'erreur') {
				new Ajax.Updater('mainMenu','giveOnglets.php',{ method: 'post', onComplete: function(requester) {
					new Ajax.Updater('submenu','giveTabs.php',{ method: 'post', onComplete: function(requester) {
						initMenu();
                                  		enableOngletsDblC();
						enableTabsHandler();
						tab_active='lcs';
						getTabData('bureau');
					}});
				}});
			}	
		}});
   	}

	function addOnglet () {
	
		new Ajax.Updater('ContentAddingOnglet','addOnglet.php',{ method: 'post', onComplete: function(requester) {
			var ajaxWindCmd9=dhtmlwindow.open('ajaxWindCmd9','div','ContentAddingOnglet','Ajouter un onglet','width=285px,height=200px,left=250px,top=150px,resize=1,scrolling=1,center=1');	
			pretty_cmd(ajaxWindCmd9);

		}});
	}

	function sauverOnglets () {
		var onglet_name =cleanaccent($('onglet_name').value);
		var liste_menu =cleanaccent($('liste_menu').value);
		if ($('tab_is_etab')) 
			var tab_is_etab = $('tab_is_etab').checked;
		if (tab_is_etab) {
			var place_etab = $('place_etab').value;
			var params = '?onglet_name='+escape(onglet_name)+'&liste_menu='+escape(liste_menu)+'&place_etab='+place_etab;	
			var manager = 'sauverOngletsEtab.php';
		} else {
			var params = '?onglet_name='+escape(onglet_name)+'&liste_menu='+escape(liste_menu);
			var manager = 'sauverOnglets.php';
		}
		if ((onglet_name != '') && (liste_menu != '')) {
			new Ajax.Request(manager,{ method: 'post', parameters: params, onComplete: function(requester) {
				alert(requester.responseText);
				if (requester.responseText != 'erreur') {
					new Ajax.Updater('mainMenu','giveOnglets.php',{ method: 'post', onComplete: function(requester) {
						new Ajax.Updater('submenu','giveTabs.php',{ method: 'post', onComplete: function(requester) {
							initMenu();
                                  			enableOngletsDblC();
							enableTabsHandler();
							tab_active='lcs';
							getTabData('bureau');
						}});
					}});
				}
		
			}});
		}
	}

	function view_others() {

		var other = $('liste_users').value;
		mode = 'other';
		user = other;
		var params = '?login='+other;
		var url = '/monlcs/other.php'+params; 
		alert('Vous passez en mode vue élève!');
		window.top.frames[1].location= url;
	}

	function init() {
		
		//initialise les onglets
		if (self.innerWidth!=undefined)
			maxScreen = self.innerWidth;
		else {
			var D= document.documentElement;
			if(D) maxScreen= D.clientWidth;
		}
		$('view_others').style.display = 'none';
		var params = '?mode='+mode+'&user='+user;
		new Ajax.Updater('mainMenu','giveOnglets.php',{ parameters: params, method: 'post', onComplete: function(requester) {
		//cree le systeme d'onglets
			new Ajax.Updater('submenu','giveTabs.php',{ parameters: params, method: 'post', onComplete: function(requester) {
				Element.hide('spinner');
				if (mode == 'user') {
					new Ajax.Request('whoami.php' , { onComplete: function(requester) { 
						var personne = requester.responseText;
                				quiC = personne;
                				var reg = new RegExp("eleve", "i");
						if (reg.exec(personne) == null)   {
							$('addOnglet').style.display = 'block';
							Event.observe('addOnglet','click',addOnglet,'false');
								new Ajax.Request('giveListeUsers.php' , { onComplete: function(requester) { 
									$('view_others').style.display = 'block';
									$('view_others').innerHTML = requester.responseText+'<div id="img_user"></div>';
									$('img_user').style.display = 'block';
									Event.observe('img_user','click',view_others);
								}});
						} else {
							$('addOnglet').style.display = 'none';
						}
					}});
				} else {
					quiC = 'eleve';
					$('warning_other').innerHTML ='<a href="#">Mode vue utilisateur('+user+')</a>';
					$('warning_other').onclick = function() {
						alert("L'écran en cours n'est pas votre écran, il faut cliquer sur le bouton MonLCS");
					}
				
				}
				initFloatingWindowWithTabs('window1',Array(' Ress. proposées',' Pot de ress.',' Ajouter'),200,200,800,150,false,false,false,false,false,false);
				$('dhtmlgoodies_floating_window0').style.display = 'none';
				initMenu();
    				if (mode == 'user')
					enableOngletsDblC();
				Set_Cookie('floating_window_activeTab0',0,100);
				$('ressources').style.display='none';
				//initialise les menus contextuels
				if (MCie5||MCns6) menuobj=document.getElementById("ie5menu");	/* Détermine le menu de départ */
					Event.observe('virtualMenu','click', function() { Element.hide('virtualMenu'); } ,false);
				Element.toggle('virtualMenu');
				Event.observe('cDv','click', showRightMenu,false);
				$('content').style.display='none';		
				enableTabsHandler();
				tab_active ='bureau';
				getTabData('bureau');
				if (mode == 'other') {
					$('cDv').style.display = 'none';
					$('SH_frame').style.display = 'none';
				}
	
			}});
	
		}});

	}

	Event.observe(window,'load',init,false);


	function statsFor(Ress_id) {
	
		var params = '?ress='+parseInt(Ress_id)+'&tab='+tab_active;
		var ajax0 = new Ajax.Updater('stats','statsFor.php', { method: 'get', parameters: params, onComplete: function(requester) {
			var ajaxWindCmd4=dhtmlwindow.open('ajaxWindCmd4','div','stats','Statistiques','width=350px,height=150px,left=250px,top=150px,resize=1,scrolling=1,center=0');	
			pretty_cmd(ajaxWindCmd4);

		}});
	}

	function SH() {
		
		if (up == true) {
			window.top.document.body.rows = "0,*";
			up = false;
			$('SH_frame').style.backgroundImage= 'url(./images/down.png)';
			return;
		} else	{
			window.top.document.body.rows = "90,*";
			up = true;
			$('SH_frame').style.backgroundImage= 'url(./images/up.png)';
			return;
		}
	}

	function addUrl() {

		if ($('urlAdd').value == '') {
			alert('Le champ Url est vide!');
			return true;
		}


		var urlV = 'null';
		if (vignette_mode == 'rien')
			urlV = 'null';
		if (vignette_mode == 'thumbalizr')
			urlV = escape('http://www.thumbalizr.com/api/?url='+$('urlAdd').value+'&width=250');
		if ((vignette_mode == 'upload') && ($('filename').value != ''))
			urlV =  escape('./vignettes/'+$('filename').value);
		
		//var isSiteTV = $('siteTV').checked;
		var params;
		//if (isSiteTV)
		//	params = '?sitetv='+$('urlAdd').value+'&url='+escape('/lcs/includes/cas_sitetv2.php?vid='+($('urlAdd').value));
		//else
			params = '?sitetv=0&url='+escape($('urlAdd').value);
		params += '&titre='+escape(cleanaccent($('titreAdd').value))+'&statut='+$('statut').checked+'&rss='+$('RSS').checked+'&url_vignette='+urlV;
		params += '&descrAdd='+escape(cleanaccent($('descrAdd').value));
		params += '&statutP='+$('statutP').checked;
		
		
		var ajax0 = new Ajax.Request('addRessources.php', { method: 'post', parameters: params, onComplete: function(requester) {
       			alert(requester.responseText);
			giveRessources(tab_active);	
       		}});
	}
	

	function liste_rss() {

		var indice=0;
		var tab= new Array();
		var fen = document.getElementsByClassName('dragableBox');
		for (var i = 0; i < fen.length; i++) {
			var t = $(fen[i].id);
			tab[indice] = t.id;
			indice++;
		}
		return tab;
	}

	function rssSave() {

		var liste = liste_rss();
		var ajax10 = new Ajax.Request('cleanRss.php', { method: 'post', onComplete: function(requester) {	
			for (var i =0; i< liste.length; i++) {
				var numericId = parseInt(liste[i].substring('dragableBox'.length,liste[i].length));
				if ($('dragableBox'+numericId).style.display  != 'none')  {
					var params = '?url='+dragableBoxesArray[numericId]['rssUrl'];
					var ajax0 = new Ajax.Request('saveRss.php', { method: 'post', parameters:  params ,onComplete: function(requester) {
						giveRessources(tab_active);
					}});
				}
			}
	
		}});

	}

	function changeStatut(id) {
		
		var etat = $('affStatut'+parseInt(id)).innerHTML;
		if (etat == 'public') {
			$('affStatut'+parseInt(id)).innerHTML='private';
			etat = 'private';
		} else {
			$('affStatut'+parseInt(id)).innerHTML='public';
			etat = 'public';
		}

		var params ='?id='+parseInt(id)+'&statut='+etat;
		var ajax0 = new Ajax.Request('changeStatut.php',{ method: 'post', parameters: params, onComplete: function(requester) {
		}});
	}

	function ajoutNote() {
		
		var params = '?id='+tab_active;
		var ajax0 = new Ajax.Request('giveMaxNotes.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			note = parseInt(requester.responseText)+1;
			var note_active_id = 'ajaxWindNote'+parseInt(note);
			getMaxCoords();
			if ( (maxX + 300) < maxScreen) {
				posx = maxX+5;
              		posy = 60;
 	      		}  
      			var note_active_id=dhtmlwindow.open(note_active_id,'inline','<div style=padding: 5px; background-color: #DFB;>Double clic ici pour modifier</div>','Note'+note,'width=300px,height=200px,left='+posx+'px,top='+posy+'px,resize=1,scrolling=1,center=0');
			$(note_active_id).ondblclick = function () {
				note = (this.id).substr(12, 5);
				switch2Editor(note);
				return false;
			}
		}});
	}

	function FCKeditor_OnComplete(editorInstance) {
		
		editorInstance.LinkedField.form.onsubmit = function() { 
						send(editorInstance);
						return false;
						}
		editorInstance.SetHTML(msg);
	}


	function send(editorInstance) {
		
		var note_active = 'ajaxWindNote'+parseInt(note);
		msg = editorInstance.GetXHTML();

		for (var k=0; k<10000;k++) {}
		var params = '?id='+tab_active;
		params += "&x=" + parseInt($(note_active).style.left);
		params += "&y=" + parseInt($(note_active).style.top);
		params += "&z=50";
		params += "&w=" + old_width;
		params += "&h=" + old_height;
		params += "&note=" + note;
		$(note_active).contentarea.innerHTML=msg;
		params += "&msg="+escape(msg);
		var win_title =$(note_active).handle.childNodes[0].nodeValue;
		params += "&titre="+escape(cleanaccent(win_title));
		var rep = confirm('Faut-il publier cette note au format HTML dans le home utilisateur?\nLa note sera alors automatiquement placée dans le pot de ressources.\nVotre espace doit être au préalable activé!');
		if (rep == true)
			params += "&save_html=Y";	
		else
			params += "&save_html=N";
		
		new Ajax.Request('/monlcs/saveNote.php', { method: 'post', parameters: params, onComplete: function(requester) {
			
			$(note_active).style.width = old_width;
			$(note_active).contentarea.style.height = old_height ;
			showPen($(note_active).id);
			$(note_active).ondblclick = function () {
				note = (this.id).substr(12, 5);
				switch2Editor(note);
				return false;
			}

			sauver_note = false;
		}});

		return false;
	}


	function switch2Editor(note) {

		sauver_note = true;
		var note_active = 'ajaxWindNote'+parseInt(note);

		$(note_active).ondblclick = function () {
			return false;
		}

		msg = $(note_active).contentarea.innerHTML;
		maskPen(note_active);
		// ? droit de modifier 
		var params = '?note='+note+'&id='+tab_active+'&id_scen='+id_scen;
		var ajax0 = new Ajax.Request('canEdit.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			//alert(requester.responseText);
			
			if (trim(requester.responseText) == 'ko')
				return false;
			showPen(note_active);
			var ajax1 = new Ajax.Updater('notes','mkEdit.php',{ method: 'post', parameters: params, onComplete: function(requester) {
				$('notes').style.display='none';
				var fck = new FCKeditor('edit');
				fck.BasePath='./fckeditor/';
				fck.Height='300';
				fck.ReplaceTextarea();	
				old_width = $(note_active).style.width;
				old_height = $(note_active).contentarea.style.height;
				$(note_active).style.width = '410px';
				$(note_active).contentarea.style.height = '300px';
				$(note_active).contentarea.innerHTML = $('notes').innerHTML;
			}});
		}});
	}

	function deleteRessources(id) {

		var rep = confirm('Etez vous certain de supprimer cette ressource? Cette opération est irréversible!');
		if (rep == true) {
			var params =  '?id='+parseInt(id);
			new Ajax.Request('deleteRessources.php',{ method: 'post', parameters: params, onComplete: function(requester) {
				giveRessources(tab_active);
				alert('La suppression demandée à été réalisée.');
			}});
		}
	}

	function deletePropose(id) {

		var rep = confirm('Etez vous certain de supprimer cette ressource? Cette opération est irréversible!');
		if (rep == true) {
			var params =  '?tab='+tab_active+'&id='+parseInt(id);
			
			new Ajax.Request('deletePropose.php',{ method: 'post', parameters: params, onComplete: function(requester) {
				giveRessources(tab_active);
				alert('La suppression demandée à été réalisée.');
			}});
		}
	}

	function deleteImpose(id) {

		var rep = confirm('Etez vous certain de supprimer cette ressource? Cette opération est irréversible!');
		if (rep == true) {
			var params =  '?id='+parseInt(id);
			new Ajax.Request('deleteImpose.php',{ method: 'post', parameters: params, onComplete: function(requester) {
				giveRessources(tab_active);
				alert('La suppression demandée à été réalisée.');
			}});
		}

	}


	function ScenByMat() {

		var params =  '?matiere='+$('scen_matiere').value;
		params += '&mode='+mode+'&user='+user;


		new Ajax.Updater('update','giveScen2.php',{ method: 'post', parameters:params, onComplete: function(requester) {
			var liste = liste_filtree('helpS');
			enablePopup(liste);	
		}});
	}

	function ProposeByMat() {
		
		var params =  '?matiere='+$('scen_matiere').value;
		params += '&mode='+mode+'&user='+user;
		new Ajax.Updater('update','givePropose2.php',{ method: 'post', parameters:params, onComplete: function(requester) {
			var liste = liste_filtree('helpP');
			enablePopup(liste);	

		}});
	}

	function update_context() {

		var params='?id='+tab_active+'&id_scen='+id_scen;
		new Ajax.Updater('ie5menu','updateContextMenu.php',{ method: 'post', parameters:params, onComplete: function(requester) {
			if (requester.responseText == 'Aucune action') 
				$('ie5menu').style.display = 'none';
			else { 
				if (MCie5||MCns6) {
					menuobj=document.getElementById("ie5menu");
					menuobj.style.display='block';
				}
			}
		}});
	}

	function viewScen(idS) {
		id_scen = idS;
		purge();
		
		var params =  '?id_scen='+id_scen;
		params +=  '&mode='+mode;
		params +=  '&user='+user;
		
		new Ajax.Updater('content','viewScen.php',{ method: 'post', parameters:params, onComplete: function(requester) {
			eval(requester.responseText);
			//retirer fen ressources
			$('dhtmlgoodies_floating_window0').style.display = 'none';

			//activer l'edition des notes
			var z=liste_fen_actives();
			
			for (var i=0; i<z.length; i++) {
				var Expression = new RegExp("Note","g");
				if ( Expression.test($(z[i]).id) ) {
					var fen = $(z[i]).id;
					enableNoteEditing(fen);			
				}
			}
		}});

	}

	function deleteScen(idS) {
		
		var rep = confirm('Etez vous certain de supprimer ce scénario? Cette opération est irréversible!');
		if (rep == true) {
			var params =  '?id_scen='+idS;
			new Ajax.Updater('content','deleteScen.php',{ method: 'post', parameters:params, onComplete: function(requester) {
				getTabData(tab_active);
			}});

		}
	}


	function showGuide() {

		$('ressources').innerHTML='';
		$('ressources').style.display='none';

		var ajaxWindCmd0=dhtmlwindow.open('ajaxWindCmd0','iframe','/monlcs/Aide/','Ressources','width=410px,height=350px,left=5px,top=60px,resize=1,scrolling=1,center=0');	
		pretty_cmd(ajaxWindCmd0);
	}

	function showTuto() {

		var ajaxWindCmd012=dhtmlwindow.open('ajaxWindCmd012','iframe','/monlcs/Aide/tutos_scen/index.html','Tutoriels','width=792px,height=508px,left=250px,top=160px,resize=1,scrolling=1,center=0');	
		pretty_cmd(ajaxWindCmd012);
	}



	function resetTab() {
	
		var tab = Get_Cookie('floating_window_activeTab0');
		//alert(tab);
	
		$('tab2').innerHTML ='';
		$('tab3').innerHTML ='';
		$('tab1').style.display = 'block';
		$('tab2').style.display = 'none';
		$('tab3').style.display = 'none';
	
		$('floatingWindowTab0').className = 'floatingWindowTab_active';
		$('floatingWindowTab0').style.zIndex = 300;
		var img = $('floatingWindowTab0').getElementsByTagName('IMG')[0];
		img.src = tabRightActive;	
					
		$('floatingWindowTab1').className = 'floatingWindowTab_inactive';
		$('floatingWindowTab1').style.zIndex = 400;
		var img = $('floatingWindowTab1').getElementsByTagName('IMG')[0];
		img.src = tabRightInActive;	
					
		$('floatingWindowTab2').className = 'floatingWindowTab_inactive';
		$('floatingWindowTab2').style.zIndex = 500;
		var img = $('floatingWindowTab2').getElementsByTagName('IMG')[0];
		img.src = tabRightInActive;	
				
		Set_Cookie('floating_window_activeTab0',0,100);		
		
	}

	function ReturnXdoc(e) {
		var event=typeof window.event!='undefined'?window.event:e;
		var Xfen, Xdoc, scrollL;
		if(document.documentElement.scrollLeft!=0) 
			scrollL=document.documentElement.scrollLeft;
		else if(document.body.scrollLeft!=0)
			scrollL=document.body.scrollLeft;
		else scrollL=0;	
		Xfen=event.clientX ;
		return(Xfen+scrollL);
	}

	function ReturnYdoc(e) {
		var event=typeof window.event!='undefined'?window.event:e;
		var Yfen, Ydoc, scrollT;
		if(document.documentElement.scrollTop!=0) scrollT=document.documentElement.scrollTop;
		else if(document.body.scrollTop!=0) scrollT=document.body.scrollTop;
		else scrollT=0;
		Yfen=event.clientY || 50;
		return Yfen+scrollT;	
	}

	function popupOn(e,id) {
		if (!id)
			return false;
		var x = ReturnXdoc(e)+10;
		var y = ReturnYdoc(e)-10;
	
		var style = { position: 'absolute', backgroundColor: '#DFDFFF', overload: 'auto', border: '1px solid #333333', zIndex: 200000, padding: '2px 0' , width: '300px', height: 'auto', textAlign: 'center' };
		Element.setStyle('descr_popup',style);
		$('descr_popup').style.left=parseInt(x)+'px';
		$('descr_popup').style.top=parseInt(y)+'px';
		$('descr_popup').onclick = function(){
			Element.hide($('descr_popup'));
		}	
		
		var real_id = parseInt(id.substr(5,4));
		var params = '?id='+real_id;
			
		var help_mode;	

		var help1 = new RegExp("helpP", "i");
		if (help1.exec(id) != null) {
			help_mode ='P';
			var url = 'giveDescription.php';
		}
	
		var help2 = new RegExp("helpR", "i");
		if (help2.exec(id) != null) {
			help_mode ='R';
			var url = 'giveDescription.php';
		}

		var help3 = new RegExp("helpS", "i");
		if (help3.exec(id) != null) {
			help_mode ='S';
			var url = 'giveScenDescription.php';
		}

		new Ajax.Request(url,{ method: 'post', parameters: params, onComplete: function(requester) {
			if (trim(requester.responseText) != 'Changer description') {
				$('descr_popup').innerHTML = requester.responseText;
				Element.show('descr_popup');	
			} else 
				gestHelpUpdate(help_mode,real_id,escape('<entrez ici une courte decription>'));
					
		}});
	
		return true;
		}

	function gestHelpUpdate(help_mode,real_id,probe) {
	
		var new_descr = prompt('Changer la description',unescape(probe));
				if ( (trim(new_descr) == '') || (new_descr == 'null') )
					return false;
				var rep = confirm('Nouvelle description= '+new_descr+' ?');
				if (rep) {
					if  (help_mode != 'S')
						url = 'saveDescription.php';
					else
						url = 'saveScenDescription.php';
					var params = '?id='+real_id;
					params += '&descr='+escape(cleanaccent(new_descr));
					new Ajax.Request(url,{ method: 'post', parameters: params, onComplete: function(requester) {
						alert(requester.responseText);
					}});
				}
	}
	
	function wantChangeDescr(id) {
		Element.hide('descr_popup');
		var params = '?id='+id+'&mode=other&user='+user;
		new Ajax.Request('canChangeDescription.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			var rep = trim(requester.responseText);
			if (trim(rep) == 'ko')
				return false;
			var help_mode;	
			var help1 = new RegExp("helpP", "i");
			if (help1.exec(id) != null)
				help_mode ='P';
			var help2 = new RegExp("helpR", "i");
			if (help2.exec(id) != null) 
				help_mode ='R';
			var help3 = new RegExp("helpS", "i");
			if (help3.exec(id) != null) 
				help_mode ='S';
			var real_id = parseInt(id.substr(5,4));
			gestHelpUpdate(help_mode,real_id,escape(rep));		


			
		}});

	}

	function enablePopup(liste) {
	
		for (var i=0; i<liste.length; i++) {
			var elem = $(liste[i]);
			elem.onmousedown = function(e) {
				//var e = window.event || e;
				popupOn(e,this.id);
			}

			elem.ondblclick = function(e) {
				var e = window.event || e;
				Element.hide('descr_popup');
				wantChangeDescr(this.id);
			}
		

			elem.onmouseout = function() {
				Element.hide('descr_popup');
			}

		}

	}

	function giveRessources(id) {

		$('ressources').innerHTML='';
		$('ressources').style.display='none';

		params = '?id='+id;
		params += '&mode='+mode+'&user='+user;

		new Ajax.Request('whoami.php' , { onComplete: function(requester) { 
			var personne = requester.responseText;
			if (mode == 'other')
				personne ='eleve';
			if (trim(personne) == 'eleve')
				Element.hide('addOnglet');
			
			$('dhtmlgoodies_floating_window0').style.display = 'none';
			$('floatingWindowTab2').style.display='none';
			$('floatingWindowTab1').style.display='none';
			$('floatingWindowTab0').style.display='none';
			
			new Ajax.Updater('tab3','giveAddRessources.php',{ method: 'post', parameters: params, onComplete: function(requester2) {
				if ( (trim(personne) != 'eleve') && (tab_active != 'lcs') ){
					$('floatingWindowTab2').style.display='block';
					$('upload_panel').style.display='none';
					var elem = $('status');
					elem.onmouseup = function() {
						alert('clic');
					}
					var elem = $('statusP');
					elem.onmouseup = function() {
						alert('clic');
					}

				}
			}});	
			
			if ((trim(personne) != 'eleve') || (tab_active == 'bureau') || (tab_active == 'rss')){
				if (tab_active != 'lcs') {
					new Ajax.Updater('tab2','giveRessources.php',{ method: 'post', parameters: params, onComplete: function(requester3) {
						$('floatingWindowTab1').style.display='block';
						var liste = liste_filtree('helpR');
						enablePopup(liste);	
					}});
				}//if lcs
			}//if personne
			
			var manager = 'giveRessourcesP.php';
				
			if (id == 'scenario_choix')
				var manager = 'giveScen.php';
			if (id == 'lcs') 
				var manager = 'giveLcs.php';
			
			if (id == 'peda')
				var manager = 'givePropose.php';
				
			new Ajax.Updater('tab1',manager,{ method: 'post', parameters: params, onComplete: function(requester) {
				if ((id == 'lcs') || (mode == 'other')) {
					$('floatingWindowTab2').style.display='none';
				}
				$('floatingWindowTab0').style.display='block';
				var liste = liste_filtree('helpP');
				enablePopup(liste);	
			}});
	
			$('dhtmlgoodies_floating_window0').style.display = 'block';
			resetTab();
			Set_Cookie('floating_window_activeTab0',tab,100);		
			showHideWindowTab;
		}});	//whoami
	}


	function checkStatus() {
		$('statutP').disabled =$('statut').checked;
			
	}

	function checkStatusP() {
		$('statut').disabled =$('statutP').checked;
	}

	

	function giveRessourcesP(id) {

		$('ressources').innerHTML='';
		$('ressources').style.display='none';

		params = '?id='+id;
		new Ajax.Updater('ressources','giveRessourcesP.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			var ajaxWindCmd0=dhtmlwindow.open('ajaxWindCmd0','inline',$('ressources').innerHTML,'Ressources','width=250px,height=250px,left=5px,top=60px,resize=1,scrolling=1,center=0');	
			pretty_cmd(ajaxWindCmd0);
		}});
	}

	function giveScen() {

		$('ressources').innerHTML='';
		$('ressources').style.display='none';
		new Ajax.Updater('ressources','giveScen.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			var ajaxWindCmd2=dhtmlwindow.open('ajaxWindCmd2','inline',$('ressources').innerHTML,'Scenarios','width=500px,height=250px,left=5px,top=60px,resize=1,scrolling=1,center=0');	
			pretty_cmd(ajaxWindCmd2);
		}});
	}

	function viewRSS(url) {

		var lurl = ''+escape(url);
		createARSSBox(lurl,1,false,5);
		rssSave();
	}

	function giveRSS() {

		$('rssContainer').innerHTML='';
		$('rssContainer').style.display='none';

		new Ajax.Updater('rssContainer','rssSpace.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			$('rssContainer').style.display='block';	
			$('addNewFeed').style.display='none';
			initDragableBoxesScript();
			deleteAllDragableBoxes();
			//recuperer ici les fils RSS de l'user
			new Ajax.Request('rssForUser.php',{ method: 'post', parameters: params, onComplete: function(requester) {
				eval(requester.responseText);
			}});
		}});
	}


	function givePropose() {
		
		$('ressources').innerHTML='';
		$('ressources').style.display='none';
		new Ajax.Updater('ressources','givePropose.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			var ajaxWindCmd2=dhtmlwindow.open('ajaxWindCmd2','inline',$('ressources').innerHTML,'Choisir la matière','width=500px,height=250px,left=5px,top=60px,resize=1,scrolling=1,center=0');	
			pretty_cmd(ajaxWindCmd2);
		}});
	}

	function viewUrl() {

		viewRessource($('url').value);
	}

	function viewUrl2() {
		var lurl;
		var height = '250px';
		var width = '410px';

		//if ($('siteTV').checked)
		//	lurl = escape('/lcs/includes/cas_sitetv2.php?vid='+$('urlAdd').value);

		//else
			lurl = escape($('urlAdd').value);
		
		//patch videos en flv
                var trouveFlv = /\.flv$/;
                if (trouveFlv.exec(lurl)) {
                        lurl = escape('/monlcs/modules/flv/flvplayer.swf?file='+$('urlAdd').value);
                }



		//patch swf
		var trouveSwf = /\_pdf.swf$/;
		if (trouveSwf.exec(lurl)) {
			height = '750px';
			width = '550px';
			lurl = escape('/monlcs/giveCleanFlash.php?url='+$('urlAdd').value);
		}
		
		//patch videos en ggb
		var trouveGgb = /\.ggb$/;
		if (trouveGgb.exec(lurl)) {
			lurl = escape('/monlcs/modules/geogebra/viewer.php?ggb='+$('urlAdd').value);
		}
		
		//patch article spip
		var trouveArticleSpip = /^spip_article/;
		if (trouveArticleSpip.exec(trim(lurl))) {
			lurl = unescape(lurl);
			lurl = lurl.replace('spip_article','/spip/?page=lcs-article&id_article');
			//$('urlAdd').value = lurl;
		}
		//patch site spip
		var trouveSiteSpip = /^spip_site/;
		if (trouveSiteSpip.exec(trim(lurl))) {
			lurl = unescape(lurl);
			lurl = lurl.replace('spip_site','/spip/?page=lcs-sites&id_rubrique');
			//$('urlAdd').value = lurl;
		}
		//patch fichier mm
		var trouveMm = /\.mm$/;
		if (trouveMm.exec(lurl)) {
			lurl = escape('/monlcs/modules/mm/?'+$('urlAdd').value);
		}
	
        	var ltitre = escape(cleanaccent($('titreAdd').value));
		var ajaxWindCmd3=dhtmlwindow.open('ajaxWindCmd3','iframe',unescape(lurl),unescape(ltitre),'width='+width+',height='+height+',left=5px,top=60px,resize=1,scrolling=1,center=0');	
		pretty_cmd(ajaxWindCmd3);
	}

	function view_Url(id,url) {
		var fen = 'ajaxWind'+id;
		if (maxX + 410 > maxScreen) {
			posX = 0;
			posY = maxY;
		} else {
			posX = maxX;
			posY = 60;
		}
		var fen = dhtmlwindow.open(fen,'iframe',url,'Vue','width=410px,height=250px,left='+parseInt(posX)+'px,top='+parseInt(posY)+'px,resize=1,scrolling=1,center=0');	
	}

	function liste_fen() {

		var indice=0;
		var tab= new Array();
		var fen = document.getElementsByClassName('dhtmlwindow');
		for (var i = 0; i < fen.length; i++) {
			var t = $(fen[i].id);
			tab[indice] = t.id;
			indice++;
		}
		return tab;
	}

	function liste_filtree(filtre) {
		
		var indice=0;
		var tab= new Array();
		var fen = document.getElementsByClassName(filtre);
		for (var i = 0; i < fen.length; i++) {
			var t = $(fen[i].id);
			tab[indice] = t.id;
			indice++;
		}
		return tab;
	}

	function liste_fen_actives() {
	//liste des fenetres actives
		var indice=0;
		var tab= new Array();
		var fen = document.getElementsByClassName('dhtmlwindow');
		for (var i = 0; i < fen.length; i++) {
			var t = $(fen[i].id);
			if (t.style.display == 'block') {
				tab[indice] = t.id;
				indice++;
			}
		}
		return tab;
	}

	function inhibit_close(d) {
		var sourceobj =$(d).controls;
		sourceobj.lastChild.src = 'images/no_close.gif';
	}

	function inhibit_openmax(d) {
		var sourceobj =$(d).controls;
		sourceobj.childNodes[1].src = 'images/no_close.gif';
	}

	function inhibit_max(d) {
		var sourceobj =$(d).controls;
		sourceobj.childNodes[3].src = 'images/no_close.gif';
	}

	function pretty_cmd(d) {
		
		inhibit_openmax(d);
		inhibit_max(d);
		var mZ = maxZindex()+5;
		d.style.zIndex = mZ;
		d.zIndexvalue = mZ;
		d.onmousedown = function() {
			return true;
		}
		
	}

	function fixZindex(d) {
		
		var mZ = maxZindex()+1;
		d.style.zIndex = mZ;
		d.zIndexvalue = mZ;
		d.onmousedown = function() {
			return true;
		}
		
	}


	function desinhibit_close(d) {
		var sourceobj =$(d).controls;
		sourceobj.lastChild.src = 'images/close.png';
	}

	function renameRessources(id) {

		var fen = id;
		var titre = trim($('titre_'+id).firstChild.nodeValue);
		var new_titre = trim(prompt('Nouveau titre:',titre));
		if (( new_titre != titre ) && ( new_titre != '' )) {
			var url='changeTitre.php';
			var strParams = '?titre='+escape(cleanaccent(new_titre))+'&tab='+tab_active+'&id='+fen;
			//alert(strParams);
			new Ajax.Request(url,{ method: 'post', parameters:strParams , onComplete: function(requester) {
        			giveRessources(tab_active);
			}});
		}	
	}

	function renameScenario(id) {
		var elem = trim("scen_"+id);
		
		var titre = trim($(elem).firstChild.nodeValue);
		
		
		var new_titre = trim(prompt('Nouveau titre:',titre));
		if (( new_titre != titre ) && ( new_titre != '' )) {
			var url='changeTitreScen.php';
			var strParams = '?titre='+escape(cleanaccent(new_titre))+'&tab='+tab_active+'&id_scen='+id;
			//alert(strParams);
			new Ajax.Request(url,{ method: 'post', parameters:strParams , onComplete: function(requester) {
        			giveRessources(tab_active);
			}});
		}
			
	}


	function showPen(id) {

		if (id.handle)
			id.handle.childNodes[1].childNodes[0].src = 'images/crayon.png';
		else
			if ($(id).controls) {
				var sourceobj =$(id).controls;
			sourceobj.firstChild.src = 'images/crayon.png';
			}
	}

	function maskPen(id) {
		
		if (id.handle)
			id.handle.childNodes[1].childNodes[0].src = 'images/no_close.gif';
		else
		if ($(id).controls) {
			var sourceobj =$(id).controls;
			sourceobj.firstChild.src = 'images/no_close.gif';
		}
	}


	function desktopSave() {
	
		getMaxCoords();

		if (tab_active == 'scenario_choix')  {
			saveScenario2();
			return false;
		} else {
			
			var z= liste_fen();
			for (var i=0;i<z.length;i++ ) {
				var go_min = new RegExp(dhtmlwindow.imagefiles[0], "i");
				var go_restore = new RegExp(dhtmlwindow.imagefiles[2], "i");
				var image = $(z[i]).controls.childNodes[2].src;
				if (go_min.exec(image) != null) 
					var min='N';
				else if (go_restore.exec(image) != null)
					var min='Y';
				
				winProps = "ref=" + $(z[i]).id;
				winProps += "&tab=" + tab_active;
				winProps += "&x=" + parseInt($(z[i]).style.left);
				winProps += "&vis=" + $(z[i]).style.display;
				winProps += "&y=" + parseInt($(z[i]).style.top);
				winProps += "&z=" + parseInt($(z[i]).style.zIndex);
				winProps += "&w=" + parseInt($(z[i]).style.width);
				winProps += "&h=" + parseInt($(z[i]).contentarea.style.height);
				winProps += "&min=" +min;
				var url = 'saveSettings.php';
				var strParams = winProps;
				
				new Ajax.Request(url,{ method: 'post', parameters:strParams , onComplete: function(requester) {
					
				}});
			}

		}
	}

	function defaultSave() {

		if (tab_active == 'scenario_choix')
			return;
		else {
			
			var z= liste_fen();

			for (var i=0;i<z.length;i++ ) {
				var go_min = new RegExp(dhtmlwindow.imagefiles[0], "i");
				var go_restore = new RegExp(dhtmlwindow.imagefiles[2], "i");
				var image = $(z[i]).controls.childNodes[2].src;
				if (go_min.exec(image) != null)
					var min='N';
				else if (go_restore.exec(image) != null)
					var min='Y';
				winProps = "ref=" + $(z[i]).id;
				winProps += "&tab=" + tab_active;
				winProps += "&x=" + parseInt($(z[i]).style.left);
				winProps += "&vis=" + $(z[i]).style.display;
				winProps += "&y=" + parseInt($(z[i]).style.top);
				winProps += "&z=" + parseInt($(z[i]).style.zIndex);
				winProps += "&w=" + parseInt($(z[i]).style.width);
				winProps += "&h=" + parseInt($(z[i]).contentarea.style.height);
				winProps += "&min=" +min;
				var url = 'saveDefault.php';
				var strParams = winProps;
				
				new Ajax.Request(url,{ method: 'post', parameters:strParams , onComplete: function(requester) {
					//alert(requester.responseText);
				}});
			}
		}
	}


	function savePublish() {

		
		var z = liste_fen_actives();
		var count_fen = z.length;
		for (var i=0;i<count_fen;i++) {
			var go_min = new RegExp(dhtmlwindow.imagefiles[0], "i");
			var go_restore = new RegExp(dhtmlwindow.imagefiles[2], "i");
			var fen= $(z[i]).id;
			var image = $(z[i]).controls.childNodes[2].src;
			if (go_min.exec(image) != null) {
				var min='N';
			} else if (go_restore.exec(image) != null) {
				var min='Y';
			}
			var indice = fen.substr(8, 5);
			var params='';
			params += "?x=" + parseInt($(z[i]).style.left);
			params += "&y=" + parseInt($(z[i]).style.top);
			params += "&z=" + parseInt($(z[i]).style.zIndex);
			params += "&w=" + parseInt($(z[i]).style.width);
			params += "&h=" + parseInt($(z[i]).contentarea.style.height);
			params += "&min=" + min;
			if (indice != 'Cmd1') {
				var count = $('toBox').options.length;
				if (count == 0) {
					alert('il faut au moins une cible');
					return false;
				}
				for (var j=0;j<count;j++) {
					var cible= $('toBox').options[j].value;
					params +='&idR='+indice+'&cible='+cible+'&tab='+tab_active;
					new Ajax.Request('saveR2.php',{ method: 'post', parameters: params, onComplete: function(requester) {
						//alert(requester.responseText);
					}});
				}
	  		}
		}
		alert('Les enregistrements se sont bien réalisés.');
	}


	function NR_switch() {
		if ($('isNote').checked) {
			$('divN').style.display ='block';
			$('divR').style.display ='none';
		} else {
			$('divR').style.display ='block';
			$('divN').style.display ='none';
		}
	}

function viewNote() {
var lanote =$('noteV').value;
var params = '?id='+lanote;
var ajax0 = new Ajax.Updater('view_note','viewNote.php', { method: 'post', parameters: params, onComplete: function(requester) {
	eval(requester.responseText);
	}
	});

}

function view_Note(lanote) {
var params = '?id='+lanote;
var ajax0 = new Ajax.Updater('view_note','viewNote.php', { method: 'post', parameters: params, onComplete: function(requester) {
	//afficher la note
	eval(requester.responseText);
	}
	});

}


	function savePropose() {
		
		if ($('acad_pub'))
		  	var acad_pub = $('acad_pub').checked;
		else
			var acad_pub = false;
                
              var count = $('toBox').options.length;
              if (count == 0) {
              	alert('Il faut choisir au moins une cible.');
                     return;
              }

              if (($('tab').value=='peda') && ($('matiere').value =='-1'))
              	alert('Il faut renseigner une matière!');
              else {
		
              	//serialise les cibles dans un flux
              	var cible = '#';
              	for (var j=0;j<count;j++)
                     	cible += $('toBox').options[j].value+'#';
              
			params ='&idUrl='+$('url').value+'&cible='+escape(cible)+'&tab='+$('tab').value+'&matiere='+$('matiere').value+'&acad_pub='+acad_pub;
              	new Ajax.Request('savePropose.php',{ method: 'post', parameters: params, onComplete: function(requester) {
				alert(requester.responseText);
		        	if (trim(requester.responseText) != 'RAS' ) {
                      		var url_distante = requester.responseText;
                      		var ajaxWindCmd7=dhtmlwindow.open('ajaxWindCmd7','iframe',url_distante,'Exporter les ressources','width=500px,height=280px,left=350px,top=70px,resize=1,scrolling=1,center=0');
					pretty_cmd(ajaxWindCmd7);
				}
              	}});
			alert('Les enregistrements se sont bien réalisés.');
		}//else

	}


	function propose() {
		Element.show('spinner');
		var params ='?tab='+tab_active;
		new Ajax.Updater('ressources','processR3.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			var ajaxWindCmd1=dhtmlwindow.open('ajaxWindCmd1','inline',$('ressources').innerHTML,'Proposer une ressource','width=550px,height=280px,left=350px,top=70px,resize=1,scrolling=1,center=0');
			createMovableOptions('fromBox','toBox',300,200,'Groupes disponibles','Groupes selectionn&eacute;s');
			pretty_cmd(ajaxWindCmd1);
			$('divN').style.display ='none';
			Element.hide('spinner');
		}});
	}



	function publish() {
		z = liste_fen_actives();
		var params ='?tab='+tab_active+'&fen='+z.join();
		new Ajax.Updater('ressources','processR2.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			var ajaxWindCmd1=dhtmlwindow.open('ajaxWindCmd1','inline',$('ressources').innerHTML,'Publier une ressource','width=500px,height=350px,left=350px,top=60px,resize=1,scrolling=1,center=0');
			createMovableOptions('fromBox','toBox',300,200,'Groupes disponibles','Groupes selectionn&eacute;s');
			pretty_cmd(ajaxWindCmd1);
		}});
	}

	function scenario() {

		z = liste_fen_actives();
		var params ='?tab='+tab_active+'&mode='+mode+'&user='+user+'&fen='+z.join();
		Element.show('spinner');
		new Ajax.Updater('ressources','processScen.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			var ajaxWindCmd1=dhtmlwindow.open('ajaxWindCmd1','inline',$('ressources').innerHTML,'Publier un scénario','width=305px,height=410px,left=350px,top=70px,resize=1,scrolling=1,center=0');
			createMovableOptions('fromBox','toBox',300,200,'Groupes disponibles','Groupes selectionn&eacute;s');
			pretty_cmd(ajaxWindCmd1);
			Element.hide('spinner');
		}});
	}


	function saveScenario() {

		var cible;

		var ajax0 = new Ajax.Request('giveMaxScen.php',{ onComplete: function(requester) {
			id_scen = parseInt(requester.responseText)+1;
			var z = liste_fen_actives();
			var count_fen = z.length;
			for (var i=0;i<count_fen;i++) {
				var go_min = new RegExp(dhtmlwindow.imagefiles[0], "i");
				var go_restore = new RegExp(dhtmlwindow.imagefiles[2], "i");
				var fen= z[i];
				var indice = fen;
				var image = $(z[i]).controls.childNodes[2].src;
				if (go_min.exec(image) != null)
					var min='N';
				else if (go_restore.exec(image) != null)
					var min='Y';
				
				var params='?id_scen='+id_scen;
				scenario_titre = $('titre').value;
				var scenario_descr = escape(cleanaccent($('scen_descr').value));

				if (trim(scenario_titre) == '') {
					alert('Il manque le titre');
					return false;
				}
				scenario_matiere = $('matiere').value;

				params += "&titre=" + escape(cleanaccent(scenario_titre));
				params += "&descr=" + scenario_descr;
				params += "&x=" + parseInt($(z[i]).style.left);
				params += "&y=" + parseInt($(z[i]).style.top);
				params += "&z=" + parseInt($(z[i]).style.zIndex);
				params += "&w=" + parseInt($(z[i]).style.width);
				params += "&h=" + parseInt($(z[i]).contentarea.style.height);
				params += "&min=" + min;
				params += '&matiere='+$('matiere').value;
				params +='&idR='+indice;

	  			if (indice != 'ajaxWindCmd1') {
					var count = $('toBox').options.length;
					cible ='#';
					for (var j=0;j<count;j++) {
						cible+= $('toBox').options[j].value+'#';
						
					}
					params += '&cible='+escape(cible);
					//alert(params);
					new Ajax.Request('saveScen.php',{ method: 'post', parameters: params, onComplete: function(requester) {
							//alert(requester.responseText);
							getTabData('scenario_choix');			
					}});
	  			}
			}
		alert('Les enregistrements se sont bien réalisés.');
		}});
		
	}

	function saveScenario2() {

		

		if (id_scen == -1) {
			alert('il faut choisir un scénario!');
			return false;
		}

		var z = liste_fen();
		var count_fen = z.length;
		for (var i=0;i<count_fen;i++) {
			var go_min = new RegExp(dhtmlwindow.imagefiles[0], "i");
			var go_restore = new RegExp(dhtmlwindow.imagefiles[2], "i");
			var fen= z[i];
			var image = $(z[i]).controls.childNodes[2].src;
			if (go_min.exec(image) != null)
				var min='N';
			else if (go_restore.exec(image) != null)
				var min='Y';
			var indice = fen;
			var params='';
			params += "?id_scen="+id_scen;
			params += "&x=" + parseInt($(z[i]).style.left);
			params += "&y=" + parseInt($(z[i]).style.top);
			params += "&z=" + parseInt($(z[i]).style.zIndex);
			params += "&w=" + parseInt($(z[i]).style.width);
			params += "&vis=" + $(z[i]).style.display;
			params += "&h=" + parseInt($(z[i]).contentarea.style.height);
			params += "&min=" + min;
			if (indice != 'ajaxWindCmd1') {
				params +='&idR='+indice;
				new Ajax.Request('saveScen2.php',{ method: 'post', parameters: params, onComplete: function(requester) {
					//alert(requester.responseText);
				}});
			}
		}
		alert('Les enregistrements se sont bien réalisés.');
	}

	function viewRessource(id) {

		getMaxCoords();
		var params = "?id="+parseInt(id)+"&maxX="+parseInt(maxX)+"&maxY="+parseInt(maxY)+"&maxScreen="+parseInt(maxScreen)+"&minY="+parseInt(minY)+"&minX="+parseInt(minX);
		new Ajax.Updater('content','viewRessource.php',{ method: 'post',  parameters:params, onComplete: function(requester) {
			eval(unescape(requester.responseText));
			
			getMaxCoords();
			//$('dhtmlgoodies_floating_window0').style.zIndex = 0;
		}});
	}

	function viewOutil(id) {
		
		var params = "?id="+id;
		new Ajax.Updater('content','viewOutil.php',{ method: 'post', parameters:params, onComplete: function(requester) {
			eval(requester.responseText);
		}});
	}

	function purge() {
		
		var z= liste_fen_actives();
		for (var i=0;i<z.length;i++ ) {
			$(z[i]).hide();
		}
	}

	//fonctions pour renommage onglet
	
	function renameTab(id) {
	
		var tab=id;
	
       	var params='tab='+tab; 
		new Ajax.Request('canRenameTab.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			if (trim(requester.responseText) == 'Y') {
				//on peut renommer le sous menu
				var bebe = $(id).innerHTML;
                           	var new_titre = cleanaccent(prompt('Nouveau titre:', bebe));
                           	if (trim(new_titre) != '') {
					$(id).innerHTML = new_titre;
					params += '&nouveau_titre='+escape(new_titre);
                                  new Ajax.Request('renameTab.php',{ method: 'post', parameters: params, onComplete: function(requester) {
						alert(requester.responseText);
						new Ajax.Updater('mainMenu','giveOnglets.php',{ method: 'post', onComplete: function(requester) {
							//recree le systeme d'onglets
							new Ajax.Updater('submenu','giveTabs.php',{ method: 'post', onComplete: function(requester) {
								initMenu();
                                  				enableOngletsDblC();
								enableTabsHandler();
								tab_active='lcs';
								getTabData('bureau');
							}});
						}});
					}});
				}
			}
		}});
       
	}


	function onglet_dbl_click(id) {
		var bebe = $(id).innerHTML;
       	var pos = bebe.indexOf('<');
       	if (pos != -1) {
             		var tete_bebe = bebe.substring(0,pos);
             		var fin_bebe = bebe.substring(pos, bebe.length);
             		var new_titre = (prompt('Nouveau titre:', tete_bebe));
             		if (trim(new_titre) != '') {
                 		$(id).innerHTML = new_titre+fin_bebe;
                 		var params ='?tab='+tab_active+'&ancien_titre='+escape(cleanaccent(tete_bebe))+'&nouveau_titre='+escape(cleanaccent(new_titre));
		   		new Ajax.Request('renameOnglet.php',{ method: 'post', parameters: params, onComplete: function(requester) {
					alert(requester.responseText);
				}});
             		}
             }
       }

	function enableTabsHandler() {
		var tabs = document.getElementsByClassName('tabs');
			for (var i = 0; i < tabs.length; i++) {
				$(tabs[i].id).onclick = function () {
					getTabData(this.id);
				}
				$(tabs[i].id).onmousedown = function () {
					return false;
				}
				$(tabs[i].id).ondblclick = function () {
					renameTab(this.id);
				}
			}
	}

	function enableOngletsDblC() {
       	
		var active_ongl = document.getElementsByClassName('activeMenuItem');
        	for (var i = 0; i < active_ongl.length; i++) {
			$(active_ongl[i].id).ondblclick = function () {
                     	onglet_dbl_click(this.id);
        		}
        		$(active_ongl[i].id).onmousedown = function () {
                     	return false;
        		}
		} 
       	
        	var ongl = document.getElementsByClassName('inactiveMenuItem');
		for (var i = 0; i < ongl.length; i++) {
			$(ongl[i].id).ondblclick = function () {
				onglet_dbl_click(this.id);
			}
              	$(ongl[i].id).onmousedown = function () {
                     	return false;
              	}
		}

	}

	function enableNoteEditing(fen) {
		
		if ($(fen).handle)
			var image = $(fen).handle.childNodes[1].childNodes[0].src;
		else
			if ($(fen).controls) {
				var sourceobj =$(fen).controls;
				var image = sourceobj.firstChild.src 
			}
		
		var Expression = new RegExp("images/crayon.png","g");
					
		if ( Expression.test(image) ) {
	
			$(fen).ondblclick = function () {
			note = fen.substr(12, 5);
			switch2Editor(note);
			return false;
			}
		} else {
			$($(z[i]).id).ondblclick = function () {
			return false;
			}
		}
	}


	function getTabData(tab) {
		
		id_scen = -1;
		$('dhtmlgoodies_floating_window0').style.display = 'none';
	
		var onglet = 'id='+ tab;
		purge();
		if (tab_active) {
			//modif : sous-menu en blanc + écriture normale
			$(tab_active).style.color ='#ffffff';
			$(tab_active).style.fontWeight ='normal';
		}
	
		tab_active=tab;
		//modif : sous-menu actif en orange + écriture normale
		$(tab_active).style.color ='#fdb218';
		$(tab_active).style.fontWeight ='normal';
	
		if (tab != 'rss') {
			$('rssContainer').innerHTML = '';
		}
	
		var params = onglet+'&mode='+mode+'&user='+user;
		Element.show('spinner');
		new Ajax.Updater('content','process2.php',{ method: 'post',  parameters: params, onComplete: function(requester) {
			//alert(unescape(requester.responseText));
			eval(unescape(requester.responseText));
			getMaxCoords();
       		$('ie5menu').style.display='block';
			var z=liste_fen_actives();
			
			
			for (var i=0; i<z.length; i++) {
				var Expression = new RegExp('Note','g');
				if ( Expression.test($(z[i]).id) ) {
					var fen = $(z[i]).id;
					enableNoteEditing(fen);
				}
			}
		}});
	
		var params = onglet+'&mode='+mode+'&user='+user;
		new Ajax.Updater('ie5menu','giveContextMenu.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			if (requester.responseText == 'Aucune action') {
				$('ie5menu').style.display = 'none';
			}
			else
			{ 
				if (MCie5||MCns6) menuobj=document.getElementById("ie5menu");
				menuobj.style.display='block';
			}
			Element.hide('spinner');
		}});
	
	}
