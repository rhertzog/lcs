		var listeCategories = new Ext.FormPanel({
				//title: 'Choix des cat&eacute;gories',
				//frame:true,
				bodyStyle:'padding:5px 5px 0',
				labelAlign: 'top',
				defaultType: 'textfield',
				layout: 'fit',
				items: [{
					xtype: 'panel',
					layout: 'fit',
					autoScroll: true,
					items: [
						{
							xtype: 'panel',
							region: 'center',
							layout: 'fit',
							autoScroll: true,

								items: [
								{
								autoScroll: true,
								region: 'center',
								layout: 'fit',
								html: '<ul class="folder" id="-1"><span>Cat&eacute;gories</span>%TREE%</ul>',
								}]
								
						}
					]
				}],
				
			});

		var appListe = new Ext.Window({
			id: 'winCategories',
			title: 'Choix de la cat&eacute;gorie',
			width: 400,
			height: 400,
                        x: 60,
			y: 110,
			minWidth: 300,
			minHeight: 200,
			closable: true,
			layout: 'fit',
			plain:true,
			autoScroll: true,
			bodyStyle:'padding:5px;',
			buttonAlign:'center',
			closeAction: 'hide',
			items: [listeCategories] 
		});
		


