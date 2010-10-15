	var bienvenue = '<BR /><p>Bienvenue sur le HelpDesk de l\'academie de Basse Normandie. Votre &eacute;tablissement a &eacute;t&eacute; authentifi&eacute; et nous avons';
	bienvenue += ' v&eacute;rifi&eacute; votre statut d\'administrateur avec succ&egrave;s. Ceci signifie que vous avez acc&egrave;s nativement a ce service.';
        bienvenue += 'Cet espace vous permet d\'ouvrir un ticket sur notre gestion Acad&eacute;mique.</p>';
        bienvenue += '<BR /><p><A href="#" id="openTicket">Ouvrir un nouveau ticket</A></p><BR />';
	
	var ds = new Ext.data.Store({
            id: 'ds',
            autoLoad: true,
    
		proxy: new Ext.data.HttpProxy({
                url: '/helpdesk/ajaxlib/getMyTickets.php',      
		method: 'POST'
            }),
            reader: new Ext.data.JsonReader({
                // we tell the datastore where to get his data from
                root: 'results',
                totalProperty: 'total',
                id: 'id'
            },[
                {name: 'title', type: 'string', mapping: 'title'},
                {name: 'description', type: 'string', mapping: 'description'},
                {name: 'categorie', type: 'string', mapping: 'categorie'},
                {name: 'submitter', type: 'string', mapping: 'submitter'},
                {name: 'suivipar', type: 'string', mapping: 'suivipar'},
		{name: 'statut', type: 'string', mapping: 'statut'},
		{name: 'created_at', type: 'string', mapping: 'created_at'},
                {name: 'updated_at', type: 'string', mapping: 'updated_at'},

            ]),
            sortInfo:{field: 'created_at', direction: "DESC"},
        });
/*
	var ds2 = new Ext.data.Store({
            id: 'ds2',
            autoLoad: true,
    
		proxy: new Ext.data.HttpProxy({
                url: '/helpdesk/ajaxlib/getEtabTickets.php',      
		method: 'POST'
            }),
            reader: new Ext.data.JsonReader({
                // we tell the datastore where to get his data from
                root: 'results',
                totalProperty: 'total',
                id: 'id'
            },[
                {name: 'title', type: 'string', mapping: 'title'},
                {name: 'categorie', type: 'string', mapping: 'categorie'},
                {name: 'submitter', type: 'string', mapping: 'submitter'},
                {name: 'suivipar', type: 'string', mapping: 'suivipar'},
		{name: 'statut', type: 'string', mapping: 'statut'},
		{name: 'description', type: 'string', mapping: 'description'},
                {name: 'created_at', type: 'string', mapping: 'created_at'},
                {name: 'updated_at', type: 'string', mapping: 'updated_at'},

            ]),
            sortInfo:{field: 'created_at', direction: "DESC"},
        });
	
	var ds3 = new Ext.data.Store({
            id: 'ds3',
            autoLoad: true,
    
		proxy: new Ext.data.HttpProxy({
                url: '/helpdesk/ajaxlib/getTickets.php',      
		method: 'POST'
            }),
            reader: new Ext.data.JsonReader({
                // we tell the datastore where to get his data from
                root: 'results',
                totalProperty: 'total',
                id: 'id'
            },[
                {name: 'title', type: 'string', mapping: 'title'},
                {name: 'categorie', type: 'string', mapping: 'categorie'},
                {name: 'submitter', type: 'string', mapping: 'submitter'},
                {name: 'suivipar', type: 'string', mapping: 'suivipar'},
                {name: 'statut', type: 'string', mapping: 'statut'},
                {name: 'created_at', type: 'string', mapping: 'created_at'},
                {name: 'updated_at', type: 'string', mapping: 'updated_at'},

            ]),
            sortInfo:{field: 'created_at', direction: "DESC"},
        });
		
	var grid = new Ext.grid.GridPanel({
    		id: 'grid_tickets',
		store: ds3,
    		columns: [
        	{header: "Titre", width: 120, sortable: true, dataIndex: 'title'},
        	{header: "Categorie", width: 120, sortable: true, dataIndex: 'categorie'},
        	{header: "Soumis par", width: 120, sortable: true, dataIndex: 'submitter'},
        	{header: "Statut", width: 120, sortable: true, dataIndex: 'statut'},
        	{header: "Gere par", width: 120, sortable: true, dataIndex: 'suivipar'},
        	{header: "Cree le", width: 135, sortable: true, dataIndex: 'created_at'},
        	{header: "Mis a jour le", width: 135, sortable: true, dataIndex: 'updated_at'}
    		],
    		viewConfig: {
        		forceFit: true,
        	},
    		sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
    		//width:600,
    		height:300,
    		frame:true,
    		//title:'Vos tickets',
    		iconCls:'icon-grid',
		listeners: {
                        rowdblclick: function(grid, rowindex) {
                                row = grid.getSelectionModel().getSelected();
                                showTicket(row);
                        }
                    },
	});
	*/
	var grid2 = new Ext.grid.GridPanel({
    		id: 'grid2_tickets',
		store: ds,
    		columns: [
        	{header: "Titre", width: 120, sortable: true, dataIndex: 'title'},
        	{header: "Categorie", width: 120, sortable: true, dataIndex: 'categorie'},
   	        {header: "Soumis par", width: 120, sortable: true, dataIndex: 'submitter'},
        	{header: "Statut", width: 120, sortable: true, dataIndex: 'statut'},
        	{header: "Gere par", width: 120, sortable: true, dataIndex: 'suivipar'},
		{header: "Cree le", width: 135, sortable: true, dataIndex: 'created_at'},
        	{header: "Mis a jour le", width: 135, sortable: true, dataIndex: 'updated_at'}
    		],
    		viewConfig: {
        		forceFit: true,
        	},
    		sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
    		//width:600,
    		height:300,
    		frame:true,
    		//title:'Vos tickets',
    		iconCls:'icon-grid',
		listeners: {
                        rowdblclick: function(grid, rowindex) {
                                row = grid.getSelectionModel().getSelected();
                                showTicket(row);
                        }
                    },
	});
	/*
	var grid3 = new Ext.grid.GridPanel({
    		id: 'grid3_tickets',
		store: ds2,
    		columns: [
        	{header: "Titre", width: 120, sortable: true, dataIndex: 'title'},
		{header: "Categorie", width: 120, sortable: true, dataIndex: 'categorie'},
        	{header: "Soumis par", width: 120, sortable: true, dataIndex: 'submitter'},
        	//{header: "Suivi par", width: 120, sortable: true, dataIndex: 'suivipar'},
        	{header: "Statut", width: 120, sortable: true, dataIndex: 'statut'},
        	{header: "Cree le", width: 135, sortable: true, dataIndex: 'created_at'},
        	{header: "Mis a jour le", width: 135, sortable: true, dataIndex: 'updated_at'}
    		],
    		viewConfig: {
        		forceFit: true,
        	},
    		sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
    		//width:600,
    		height:300,
    		frame:true,
    		//title:'Vos tickets',
    		iconCls:'icon-grid',
		listeners: {
                        rowdblclick: function(grid, rowindex) {
                                row = grid.getSelectionModel().getSelected();
                                showTicket(row);
                        }
                    },
	});
	
	var TicketsTab1 = new Ext.Panel({
                                        id: TicketsTab1,
                                        layout: 'fit',
                                        title: 'Tickets en cours de traitement',
                                        autoScroll: true,
                                        items : [grid],
	})
	*/
	var TicketsTab2 = new Ext.Panel({
                                        id: TicketsTab2,
                                        layout: 'fit',
                                        title: 'Vos tickets',
                                        autoScroll: true,
                                        items : [grid2],
	})
	/*
		var TicketsTab3 = new Ext.Panel({
                                        id: TicketsTab3,
                                        layout: 'fit',
                                        title: 'Tickets de votre etablissement',
                                        autoScroll: true,
                                        items : [grid3],
	})
	*/

	var TicketsTab = new Ext.TabPanel({
                                        id: TicketsTab,
                                        region: 'center',
					margins: '0 4 4 0',
                                        activeTab: 0,
					autoScroll: true,
                                        closable: true,
                                        items : [TicketsTab2],
                                        });


	var accueil = new Ext.Panel({
				title: 'Bienvenue',
				frame:true,
				bodyStyle:'padding:5px 5px 0',
				labelAlign: 'top',
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
								html: '<div id="log">%MSG%</div><div id="content">'+bienvenue+'</div>',
								}, 
 						//{
                                        	//xtype: 'button',
                                        	//text: 'Ouvrir un nouveau ticket',
                                        	//id: 'openTicket',
                                        	//name: 'openTicket',
                                        	//anchor:'15%',
                                    		//},
						TicketsTab ]
								
						}
					]
				}],
				
			});


	//ds3.startAutoRefresh(10,null,null,true);
	//ds2.startAutoRefresh(11,null,null,true);
	ds.startAutoRefresh(12,null,null,true);

