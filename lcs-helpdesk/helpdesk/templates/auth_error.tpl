app = new Ext.Window({
			title: 'Plateforme d\'assistance HelpDesk',
			width: 600,
			height: 400,
                        x: 20,
			y: 110,
			minWidth: 300,
			minHeight: 200,
			closable: false,
			layout: 'fit',
			plain:true,
			bodyStyle:'padding:5px;',
			buttonAlign:'center',
			closeAction: 'hide',
			items: new Ext.FormPanel({
				labelWidth: 75, // label settings here cascade unless overridden
				//url:'save-form.php',
				frame:true,
				bodyStyle:'padding:5px 5px 0',
				labelAlign: 'top',
				width: 550,
				defaults: {width: 320},
				defaultType: 'textfield',
				layout: 'fit',
				items: [{
					xtype: 'panel',
					layout: 'border',
					
					items: [
						{
							xtype: 'panel',
							region: 'center',
							layout: 'form',
							items: [
								{
								region: 'center',
								html: '<div id="log">%MSG%</div><div id="error"><img src="/lcs/images/spinner.gif"></img></div>',
								}]
								
						}
					]
				}],
				
			})
		});
		


