//var ticket_id = '-1';
var windowModifReply = new Ext.Window({
        title: 'Modification de la réponse',
        width: 600,
        height:350,
        minWidth: 300,
        minHeight: 200,
        layout: 'fit',
        plain:true,
        bodyStyle:'padding:5px;',
        buttonAlign:'center',
        closeAction: 'hide',
        autoScroll: true,
        items: new Ext.FormPanel({
            labelWidth: 75, // label settings here cascade unless overridden
            url: '/helpdesk/ajaxlib/modifReply.php',
            frame:true,
            id: 'formModifReply',
            bodyStyle:'padding:5px 5px 0',
            labelAlign: 'top',
            width: 550,
            defaults: {width: 320},
            autoScroll: true,
            defaultType: 'textfield',
            layout: 'fit',
            items: [{
                    xtype: 'panel',
                    layout: 'border',
                    items : [
                        {
                            xtype: 'panel',
                            region: 'center',
                            layout: 'form',
                            items: [
                                
                                {
                                    xtype: 'hidden',
				    name: 'ticket_id',
                                    id: 'modifTicketId'+ticket,
							    },
								{
                                    xtype: 'hidden',
				    name: 'ticket_reply_id',
                                    id: 'modifReplyId'+ticket,
									
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Titre',
                                    id: 'modifReplyTitle'+ticket,
				    name: 'title',
				    anchor:'95%'
                                }, {
                                    xtype: 'htmleditor',
                                    fieldLabel: 'Contenu',
				    id: 'modifReplyContent'+ticket,
                                    name: 'content',
                                    anchor:'95%',
                                    height: 150,
                                },
                            ]
                        }
                    ]
                }],
            buttons: [{
                    text: 'Enregistrer',
                    handler: function() {
                        Ext.getCmp('formModifReply').getForm().submit({
                                                    method:'POST',
                                                    reset: true,
                                                    waitTitle:'Modification du post',
                                                    waitMsg:'Envoi des données...',
                                                    success: function(form, action) {
                                                        try {
								windowModifReply.hide();
                                                        	//storeTickets.load();
								storeTicketReplies.load();                   									
                                                        	Ext.Msg.alert('', 'Post modifi&#233;.');	
							} catch (error) {
								Ext.Msg.alert('ERREUR',error );
							}
														
                                                    },
                                                    failure: function(form, action){
                                                        objRep = Ext.util.JSON.decode(response.responseText);
                                                        Ext.Msg.alert('Echec de la modification...', objRep.reason);
                                                    }
                                                });
                    
                    }
                }]
        })
    });


 var toolBar1 = new Ext.Toolbar({
        xtype: 'toolbar',
        region: 'north',
        height: 25,
        items: ['->','-',
	{
                    
		    text: '%TEXT_REP%',
                    id: 'btnRep_ticket_%ROWID%',
                    handler : answer_show,
                    
                },'-'	
	],
 });

var ticketRepliesReader = new Ext.data.JsonReader({
        // we tell the datastore where to get his data from
        root: 'results',
        totalProperty: 'total',
        id: 'id'
    },[
        {name: 'resume', type: 'string', mapping: 'resume'},
        {name: 'content', type: 'string', mapping: 'content'},
        {name: 'submitter', type: 'string', mapping: 'submitter'},
        {name: 'is_new', type: 'string', mapping: 'is_new'},
        {name: 'created_at', type: 'date', dateFormat: 'Y-m-d h:i:s', mapping: 'created_at'},
    ]);


var storeTicketReplies = new Ext.data.Store({
            id: 'storeTicketReplies',
            autoLoad: true,
	    proxy: new Ext.data.HttpProxy({
                url: '/helpdesk/ajaxlib/GetTicketReplies.php',      // File to connect to
                method: 'POST'
            }),
            reader: ticketRepliesReader,
            sortInfo:{field: 'created_at', direction: "DESC"},
            listeners: {
                'beforeload' : function(){
                    this.baseParams.id = %ROWID%;
                }
            }
        });


var TextRenderer = function (value, metadata, record) {
        result = value;

        if(record.data['is_new'] == '1')
            result = '<b>'+result+'</b>';

        if(record.data['is_submitted_by_me'] == '1')
            result = '<i>'+result+'</i>';

        if(record.data['is_assigned_to_me'] == '1')
            result = '<span style="color: blue;">'+result+'</span>';

        return result;
    };

var ticketExpander = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
        '<h4>Affect&#233; &#224; : {assigned}</h4>',
        '<h4>Cr&#233;e le : {created_at}</h4>'
    )
    });

var TicketColModel = new Ext.grid.ColumnModel([
        ticketExpander,
        {header: '#', width: 20, sortable: true, dataIndex: 'is_new', renderer: function(value){
                if(value == '1')
                    return '<img src="./images/flash.png" style="width: 14px; height: 14px;" />';
                else
                    return '';
            }},
        {id: 'titre', header: "Titre", width: 150, sortable: true, dataIndex: 'titre', renderer: TextRenderer},
        {header: "Emetteur", width: 120, sortable: true, dataIndex: 'submitter', renderer: TextRenderer},
        {header: "Categorie", width: 150, sortable: true, dataIndex: 'categorie', renderer: TextRenderer},
        {header: "Etablissement", width: 150, sortable: true, dataIndex: 'etablissement', renderer: TextRenderer},
        {header: "Maj le", width: 100, sortable: true, renderer: Ext.util.Format.dateRenderer('d/m/Y h:i'), dataIndex: 'updated_at'},
    ]);

var RepliesExpander = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
        '<h4>Contenu du message : </h4><p style="border-top: 1px solid #8D8D8D;border-bottom: 1px solid #8D8D8D; padding: 5px;">{content}</p>'
    )
    });

var TicketRepliesColModel = new Ext.grid.ColumnModel([
        RepliesExpander,
        {header: '#', width: 24, sortable: true, dataIndex: 'is_new', renderer: function(value){
                if(value == '1')
                    return '<img src="./images/flash.png" style="width: 14px; height: 14px;" />';
                else
                    return '';
            }},
        {id: 'resume', header: "Resum&#233;", width: 150, sortable: true, dataIndex: 'resume', renderer: TextRenderer},
        {header: "Emetteur", width: 130, sortable: true, dataIndex: 'submitter', renderer: TextRenderer},
        {header: "Date", width: 100, sortable: true, renderer: Ext.util.Format.dateRenderer('d/m/Y h:i'), dataIndex: 'created_at'},
    ]);

var comp =  new Ext.Panel({
            closable: true,
            title: 'Ticket : %TITRE%',
            layout: 'border',
            autoScroll: true,
            ticketId: %ROWID%,
	    tbar: toolBar1,	
	    items: [ 
                {
                    region:'north',
                    height: 75,
                    layout: 'fit',

                    id: 'infoTicket-'+%ROWID%,
                    border: false,
                    frame: true,
                    items: [{html: '<h3 class="post-title">%TITRE%</h3><span class="post-infos"><b>Categorie :</b> %CATEGORIE% </span><span class="post-infos"><b>Affect&#233; &#224; :</b> %SUBMITTER% </span>'}]
                },
                {
                    region: 'center',
                    xtype: 'grid',
                    title: 'R&#233;ponses',
                    store: storeTicketReplies,
                    cm: TicketRepliesColModel,
                    autoExpandColumn: 'resume',
                    plugins: RepliesExpander,
		    listeners: {
                        rowdblclick: function(grid, rowindex) {
                            
							rowTicketRep = storeTicketReplies.getAt(rowindex);
							rowTicket =  Ext.getCmp('grid2_tickets').getStore().getById(ticket);
							var user = '%USER% ';
							if ((rowTicket.data.statut == 'en attente de traitement') && (rowTicket.data.submitter == user)) {
								windowModifReply.show();
								Ext.getCmp('modifReplyTitle'+ticket).setValue(rowTicket.data.title);
								Ext.getCmp('modifReplyContent'+ticket).setValue(rowTicketRep.data.content);
								Ext.getCmp('modifTicketId'+ticket).setValue(ticket);	
								Ext.getCmp('modifReplyId'+ticket).setValue(rowTicketRep.id);	
							}
							
							 
                        }
                },
                }
            ],	
});

storeTicketReplies.startAutoRefresh(10, null,null, true);
