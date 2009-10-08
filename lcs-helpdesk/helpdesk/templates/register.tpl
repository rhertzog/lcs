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
					{ html: "<H1>Formulaire d'enregistrement</H1><br />"},
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Identifiant',
                                        name: 'login',
                                        value: '%LOGIN%',
                                        id: 'login',
					readOnly: true,
                                        anchor:'95%',

                                    },
                                    {
                                        xtype:'textfield' ,
         				inputType: 'password' ,
                                        fieldLabel: 'Mot de passe',
                                        name: 'passwd',
                                        id: 'passwd',
                                        //value: '',
                                        anchor:'95%',
                                    },
                                    {
                                        xtype:'textfield' ,
         				inputType: 'password' ,
                                        fieldLabel: 'Mot de passe(2)',
                                        name: 'passwd2',
                                        id: 'passwd2',
                                        //value: '',
                                        anchor:'95%',
                                    },

                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Nom',
                                        name: 'nom',
                                        value: '%NOM%',
                                        id: 'nom',
                                        anchor:'95%',

                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Prenom',
                                        name: 'prenom',
                                        id: 'prenom',
                                        value: '%PRENOM%',
                                        anchor:'95%',
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Mail',
                                        name: 'email',
                                        id: 'email',
                                        value: '%EMAIL%',
                                        anchor:'95%',
                                    },
 				    {
                                        xtype: 'button',
                                        text: 'S\'inscrire',
                                        id: 'submit_register',
                                        name: 'submit_register',
                                        anchor:'15%',
					handler: submit_register,
							
                                    }
				]
			}],
				
		});



