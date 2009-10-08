wdwAddReply = new Ext.Window({
                            layout:'fit',
                            title: 'Ajout d\'une r&#233;ponse',
                            width:450,
                            height:300,
                            closable: true,
                            resizable: false,
                            plain: true,
                            border: false,
                            draggable: true,
                            closeAction: 'hide',
                            items: [new Ext.FormPanel({
                                    labelWidth:100,
                                    frame:true,
                                    id: 'formAddRep_%ROWID%',
                                    url: '/helpdesk/ajaxlib/ReplyTicket.php',
                                    defaultType:'textfield',
                                    layout: 'form',
                                    monitorValid:true,
                                    labelAlign: 'top',
                                    // Specific attributes for the text fields for username / password.
                                    // The "name" attribute defines the name of variables sent to the server.
                                    items:[{
                                            xtype: 'hidden',
                                            name: 'id',
                                            id: 'id',
                                            value: %ROWID%
                                        },{
                                            xtype: 'htmleditor',
                                            fieldLabel: 'Message',
                                            name: 'msg',
                                            anchor:'95%',
                                            height: '100%'
                                        }],
                                    buttons:[{
                                            text:"Envoyer",
                                            formBind: true,
                                            // Function that fires when user clicks the button
                                            handler:function(){
                                                Ext.getCmp('formAddRep_%ROWID%').getForm().submit({
                                                    method:'POST',
                                                    reset: true,
                                                    waitTitle:'Ajout d\'une r&#233;ponse',
                                                    waitMsg:'Envoi des donn&#233;es...',
                                                    success: function(form, action) {
                                                        wdwAddReply.hide();
                                                        //TODO AVEC Ext.getCmp.getStore()
							//storeTicketReplies.load();
                                                        Ext.Msg.alert('', 'R&#233;ponse sauvegard&#233e;.');
							//reload du ds
							var store1 = Ext.getCmp('storeTicketReplies');
                                        		store1.load; 
                                                    },
                                                    failure: function(form, action){
                                                        obj = Ext.util.JSON.decode(response.responseText);
                                                        Ext.Msg.alert('Echec de l\'enregistrement...', obj.reason);
                                                    }
                                                });
                                            }
                                        }]
                                })
                            ]
                        });

                        wdwAddReply.show();
