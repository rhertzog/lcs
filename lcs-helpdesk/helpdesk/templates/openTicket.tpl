var comp = new Ext.FormPanel({
        	labelWidth: 75, // label settings here cascade unless overridden
         	frame:true,
                id: 'form-edit-ticket',
                bodyStyle:'padding:5px 5px 0',
                labelAlign: 'top',
                width: 550,
                defaultType: 'textfield',
                layout: 'border',
                        items:[{
                                xtype: 'panel',
                                region: 'center',
                                layout: 'form',
                                //bodyStyle:'padding:5px 5px 0',
                                items: [
				    {
                                        //xtype: 'textfield',
                                        //fieldLabel: 'Id CAT',
	                                xtype: 'hidden',
                                        name: 'category_id',
                                        //value: '%CATEGORY_ID%',
                                        id: 'category_id',
                                    },
				    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Contact',
                                        name: 'username',
                                        id: 'username',
                                        value: '%EMAIL%',
					anchor:'95%',
                                        readOnly: true,
                                        disabled: true,
                                    },
					{
                                        html: '<div>Cat&eacute;gorie&nbsp;[<A id="arbo_link" href="#">+</A>]</div>',
						
					},
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Intitul&eacute;',
                                        name: 'title',
                                        id: 'ticket_title',
                                        anchor:'95%'
                                    },
                                    {
                                        xtype: 'htmleditor',
                                        fieldLabel: 'Description',
                                        id: 'ticket_description',
                                        name: 'description',
                                        anchor:'95%',
                                        height: '100%'
                                    },
					
                                    {
                                        xtype: 'button',
                                        text: 'Envoyer',
                                        id: 'submit_ticket',
					name: 'submit_ticket',
                                        anchor:'15%',
                                    }]

                              }]

		});

