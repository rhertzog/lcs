var form = new Ext.FormPanel({
				title: 'Bienvenue',
				frame: true,
				plain: true,
				bodyStyle: 'padding:5px 5px 0',
				labelAlign: 'top',
				defaultType: 'textfield',
				layout: 'fit',
				
			});



var tab = new Ext.TabPanel({
	     id: 'mainTab',
	     //frame: true,
	     //items : [form],
             activeTab : 0,
});

app = new Ext.Window({
			title: 'Plateforme d\'assistance HelpDesk',
			width: 1180,
			height: 800,
                     	x: 20,
			y: 20,
			minWidth: 800,
			minHeight: 200,
			closable: false,
			layout: 'fit',
			plain:true,
			bodyStyle:'padding:5px;',
			buttonAlign:'center',
			closeAction: 'hide',
			items: [tab] 
		});
		


