jQuery().ready(function (){ 
jQuery("#sorter_rappro").jqGrid({ 
url:'rapproche_list.php', 
datatype: "xml",
 height: 250,
 width: 850,
  colNames:['Id','Nom pr&eacute;nom','Login Lcs', 'ID ENT rapproch&eacute;'],
  colModel:[ {name:'id',index:'id', width:30, sorttype:"int",align:"right"},
   {name:'Nom pr&eacte;nom', width:120, align:"right",sortable:false},
    {name:'Login Lcs;',index:'login_lcs', width:120, align:"right"},
    {name:'ID ENT',index:'id_ent', width:120, align:"right"}
    ], 
   rowNum:10, 
   autowidth: false, 
   rowList:[10,20,30,100], 
   pager: jQuery('#pager_rappro'), 
   sortname: 'Id', 
   viewrecords: true, 
   sortorder: "asc",
   multiselect: true,
   editurl:"rapproche_list.php" ,
   caption:"Journal des rapprochements" }).navGrid('#pager_rappro',{edit:false,add:false,del:true});
   });