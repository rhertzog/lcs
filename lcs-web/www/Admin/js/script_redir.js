jQuery().ready(function (){ 
jQuery("#sorter").jqGrid({ 
url:'redir_mail_list.php', 
datatype: "xml",
 height: 250,
 width: 900,
  colNames:['Id','Auteur','Login redirig&eacute;', 'Adresse personnelle', 'Copie','Date','Adresse'],
  colModel:[ {name:'id',index:'id', width:30, sorttype:"int",align:"right"},
   {name:'Auteur',index:'faitpar', width:90,align:"right"},
    {name:'Login redirig&eacute;',index:'pour', width:150,align:"right"},
    {name:'Adresse perso',index:'vers', width:180, align:"right"},
    {name:'Copie',index:'Copie', width:50, align:"right"},
    {name:'Date',index:'Date', width:120,align:"right",sorttype:"float"},
    {name:'remote_ip',index:'remote_ip', width:150,sorttype:"float",align:"right"}
    ], 
   rowNum:10, 
   autowidth: true, 
   rowList:[10,20,30,100], 
   pager: jQuery('#pager'), 
   sortname: 'Id', 
   viewrecords: true, 
   sortorder: "asc",
   multiselect: true,
   editurl:"redir_mail_list.php" ,
   caption:"Historique" }).navGrid('#pager',{edit:false,add:false,del:true});
   });