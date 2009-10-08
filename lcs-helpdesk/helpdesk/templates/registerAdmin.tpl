var accueil = new Ext.FormPanel({
			title: 'Bienvenue',
			id: 'register',
			frame:true,
			bodyStyle:'padding:5px 5px 0',
			labelAlign: 'top',
			defaultType: 'textfield',
			layout: 'fit',
                        items:[{
                                xtype: 'panel',
                                region: 'center',
                                layout: 'form',
                                bodyStyle:'padding:5px 5px 0',
                                items: [
					{ html: "<H1>L'authentification requiert un compte plus explicite</H1><br />\
					<p>Le compte admin ne peut convenir, merci de choisir un administrateur dans la liste donn&eacute;e ci-dessous. Si la liste est vide alors vous devrez accorder le droit lcs_is_admin a un utilisateur du LCS</p><BR /><label for=\"login\">Identifiant:</label>\
					<div id=\"listeAdmins\"><select id=\"login\" name=\"login\"><option>-</option>%LISTEADMINS%</select></div><BR />"},
             
 				    {
                                        xtype: 'button',
                                        text: 'Poursuivre',
                                        id: 'change_user',
                                        name: 'change_user',
                                        anchor:'15%',
										handler: change_user,
							
                                    }
				]
			}],
				
		});



