		var app = null;
		var appListe = null;
		var tab = null;
		var ticket = null;
		//var ds = null;		


function inspect(obj, maxLevels, level)
{
  var str = '', type, msg;

    // Start Input Validations
    // Don't touch, we start iterating at level zero
    if(level == null)  level = 0;

    // At least you want to show the first level
    if(maxLevels == null) maxLevels = 1;
    if(maxLevels < 1)     
        return '<font color="red">Error: Levels number must be > 0</font>';

    // We start with a non null object
    if(obj == null)
    return '<font color="red">Error: Object <b>NULL</b></font>';
    // End Input Validations

    // Each Iteration must be indented
    str += '<ul>';

    // Start iterations for all objects in obj
    for(property in obj)
    {
      try
      {
          // Show "property" and "type property"
          type =  typeof(obj[property]);
          str += '<li>(' + type + ') ' + property + 
                 ( (obj[property]==null)?(': <b>null</b>'):('')) + '</li>';

          // We keep iterating if this property is an Object, non null
          // and we are inside the required number of levels
          if((type == 'object') && (obj[property] != null) && (level+1 < maxLevels))
          str += inspect(obj[property], maxLevels, level+1);
      }
      catch(err)
      {
        // Is there some properties in obj we can't access? Print it red.
        if(typeof(err) == 'string') msg = err;
        else if(err.message)        msg = err.message;
        else if(err.description)    msg = err.description;
        else                        msg = 'Unknown';

        str += '<li><font color="red">(Error) ' + property + ': ' + msg +'</font></li>';
      }
    }

      // Close indent
      str += '</ul>';

    return str;
}



	Ext.override(Ext.data.Store, {
    		startAutoRefresh : function(interval, params, callback, refreshNow){
        		if(refreshNow){
            			this.load({
                		params:params,
                		callback:callback
            			});
        		}
        		if(this.autoRefreshProcId){
            			clearInterval(this.autoRefreshProcId);
        		}
        		this.autoRefreshProcId = setInterval(this.load.createDelegate(this, [{
            			params:params,
            			callback:callback
        		}]), interval*1000);
    		},
	
    		stopAutoRefresh: function() {
        		if ( this.autoRefreshProcId ) {
            			clearInterval( this.autoRefreshProcId ) ;
        		}
    }});		

   function noaccent(chaine) {
      temp = chaine.replace(/[אגה]/gi,"a")
      temp = temp.replace(/[יטךכ]/gi,"e")
      temp = temp.replace(/[מן]/gi,"i")
      temp = temp.replace(/[פצ]/gi,"o")
      temp = temp.replace(/[שûü]/gi,"u")
      return temp
   }

		function answer_show() {
			//alert('toto');
			var url = '/helpdesk/ajaxlib/answer.php';
			var params = '?ticket='+ticket;
			//alert(params);
			new Ajax.Request(url, { method: 'post', parameters: params, onComplete: function(xhr) {
				try {
					eval(xhr.responseText);
				} catch(erreur) {
					alert(erreur);
				}
			}});
			
		}	


		function showTicket(ligne) {
			var url = '/helpdesk/ajaxlib/editTicket.php';
			ticket = ligne.id;
			var params = '?ticket='+ticket;
			params += '&title='+encodeURIComponent(ligne.data.title);
			params += '&description='+encodeURIComponent(ligne.data.description);
			params += '&categorie='+encodeURIComponent(ligne.data.categorie);
			params += '&submitter='+encodeURIComponent(ligne.data.suivipar);
			params += '&statut='+encodeURIComponent(ligne.data.statut);
			//alert(params);
			try {
					Ext.getCmp('mainTab').remove(Ext.getCmp('showTicket'));
					openTab('showTicket', ligne.data.title, escape(url), null, params); 
			}
			catch(e) {
				Ext.Msg.alert(e);
			}
		} 

		function openTab(idTab, titre, link, callback, params) {
		//lance une requete ajax puis appelle un callback	
			if (link) {
				new Ajax.Request(link, { method: 'post', parameters: params, onComplete: function(xhr) {
					var elem = xhr.responseText;
					//alert(elem);
			
					var myTabPanel = Ext.getCmp('mainTab');
					try {
						eval(elem);
					} catch(erreur) {
						alert(erreur);
						var comp = new Ext.FormPanel({
							frame:true,
							bodyStyle:'padding:5px 5px 0',
							labelAlign: 'top',
							defaultType: 'textfield',
							layout: 'fit',
							items: [
								{
									xtype: 'panel',
									region: 'center',
									layout: 'fit',
									items: [
										{
										layout: 'fit',
										region: 'center',
										html: elem,
										}]
								
								}]
						});
					}
					tab = new Ext.Panel({
					id: idTab,
					layout: 'fit',
					title: titre,
					closable: true,
					items : [comp],
					});
            				myTabPanel.add(tab);
					myTabPanel.setActiveTab(tab);
					//myTabPanel.setSize(myTabPanel.getSize());
					//mytabPanel.doLayout(true);
					if (callback)
						callback();
				}});
			}

		}

		function showCategories() {
			//alert('ceci affichera une window avec les categories');
			var url = '/helpdesk/ajaxlib/selectCategorie.php';
			var params= null;
		
			var win = Ext.getCmp('winCategories');
			if (!win) {
				new Ajax.Request(url, { method: 'post', parameters: params, onComplete: function(xhr) {
					eval(xhr.responseText); 
					if (appListe) {
						appListe.show();
						init_tree($('arbo_link'));
						HideAll();	
					}
				}});
			}

		}

		function submitTicket() {
			var params = '?category_id=' + $('category_id').value;
			params += '&title=' + escape($('ticket_title').value);
			params += '&description=' + escape($('ticket_description').value);
			
			if ($('category_id').value == -1) {
				alert('Merci de choisir une categorie.');
				return true;
			}
			if ($('ticket_title').value == '') {
				alert('Merci de saisir un titre.');
				return true;
			}
			if ($('ticket_description').value == '') {
				alert('Merci de saisir un descriptif de votre probleme.');
				return true;
			}
			

			var url = '/helpdesk/ajaxlib/submitTicket.php';
			new Ajax.Request(url, { method: 'post', parameters: params, onSuccess: function(xhr) {
				Ext.getCmp('grid2_tickets').getStore().load();
				//Ext.getCmp('grid2_tickets').getStore().load();
				//Ext.getCmp('grid3_tickets').getStore().load();
				Ext.getCmp('mainTab').remove(Ext.getCmp('newTicket'));
				Ext.getCmp('winCategories').close();
 
			}});

		}

		function linkTicket() {
			Event.observe('arbo_link','click',showCategories,true);
			Event.observe('submit_ticket','click',submitTicket,true);
		}		

		function openTicket() {
			var url = '/helpdesk/ajaxlib/openTicket.php';
			var params = null;
			Ext.getCmp('mainTab').remove(Ext.getCmp('newTicket'));
			openTab('newTicket', 'Nouveau Ticket', url, linkTicket , params);
			
		}

		function change_user() {
			var url = '/helpdesk/ajaxlib/changeUser.php';
			var newUser = $('login').value;
			
			if (newUser != '-') {
				var params = '?login='+newUser;
				
				new Ajax.Request(url, { method: 'post', parameters: params, onComplete: function(xhr) {
					if (xhr.responseText == 'SUCCESS')
						doHelpDesk(false);
					else
						alert(xhr.responseText);	
				}});
			}
		}

		function submit_register() {
			var url = '/helpdesk/ajaxlib/register.php';
			if ( !$('passwd').value || !$('passwd2').value ) {
				alert('Les mots de passe ne peuvent etre vides! ');
				return false;
			}


			if ( $('passwd').value != $('passwd2').value ) {
				alert('Les mots de passe ne concordent pas! Veuillez les saisir a nouveau.');
				return false;
			}

			var params = '?xhr=1';
			params += '&passwd=' + escape($('passwd').value);
			params += '&login=' + escape($('login').value);
			params += '&nom=' + escape($('nom').value);
			params += '&prenom=' + escape($('prenom').value);
			params += '&email=' + escape($('email').value);
			//alert(params);
			
			new Ajax.Request(url, { method: 'post', parameters: params, onComplete: function(requester) {
				if (requester.responseText == 'SUCCESS') { 	
					Ext.getCmp('mainTab').remove(Ext.getCmp('register')); 
					update(false);
				} else {
					//alert('Une erreur s\'est produite votre insription est impossible, si ceci se produit a maintes reprises merci de contacter l\'Equipe Tice.');
					alert(requester.responseText);
				}
			}});
				
		}

		function update(mustRegister) {
		
			//if ($('content')) {
				var params2 = '?xhr=1';
				params2 += '&mustRegister='+mustRegister;
	
				var url2 = null;

				if (mustRegister != 0) {
					url2 = '/helpdesk/ajaxlib/register2.php';
				} else {
					url2 = '/helpdesk/ajaxlib/accueil2.php';
				}
	
				var ajax2 = new Ajax.Request(url2, {method: 'post', parameters: params2, onComplete: function(requester) {

					try {
						eval(requester.responseText);
					} catch (erreur) {
						alert(erreur);
					}
					//alert(requester.responseText);
					Ext.getCmp('mainTab').add(accueil);
					Ext.getCmp('mainTab').setActiveTab(accueil);
					app.doLayout();
	
					if ($('openTicket'))
						Event.observe('openTicket','click',openTicket,true);
					
					return true;	
				}});
			//}

		}

		function doHelpDesk(start) {
			
			
			var url = '/helpdesk/goHelpDesk.php';
			var params = '?xhr=1';
				var i=0;
				var ajax1 = new Ajax.Request(url, {method: 'post', parameters: params, onComplete: function(requester) {
					if (app)
						app.close();
					
						try {
							eval(requester.responseText);
						} catch(err) {
							Ext.Msg.alert('Erreur',err);
	
						}
					//do { i++  } while (i < 100);	
					app.show();
					update(mustRegister);
			
				}});


		}
		Ext.BLANK_IMAGE_URL = '/lib/js/ext/resources/images/default/s.gif';
		
