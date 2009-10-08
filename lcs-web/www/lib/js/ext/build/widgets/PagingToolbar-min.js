/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


(function(){var T=Ext.Toolbar;Ext.PagingToolbar=Ext.extend(Ext.Toolbar,{pageSize:20,displayMsg:'Displaying {0} - {1} of {2}',emptyMsg:'No data to display',beforePageText:"Page",afterPageText:"of {0}",firstText:"First Page",prevText:"Previous Page",nextText:"Next Page",lastText:"Last Page",refreshText:"Refresh",paramNames:{start:'start',limit:'limit'},initComponent:function(){var pagingItems=[this.first=new T.Button({tooltip:this.firstText,iconCls:"x-tbar-page-first",disabled:true,handler:this.onClick,scope:this}),this.prev=new T.Button({tooltip:this.prevText,iconCls:"x-tbar-page-prev",disabled:true,handler:this.onClick,scope:this}),'-',this.beforePageText,this.inputItem=new T.Item({height:18,autoEl:{tag:"input",type:"text",size:"3",value:"1",cls:"x-tbar-page-number"}}),this.afterTextItem=new T.TextItem({text:String.format(this.afterPageText,1)}),'-',this.next=new T.Button({tooltip:this.nextText,iconCls:"x-tbar-page-next",disabled:true,handler:this.onClick,scope:this}),this.last=new T.Button({tooltip:this.lastText,iconCls:"x-tbar-page-last",disabled:true,handler:this.onClick,scope:this}),'-',this.refresh=new T.Button({tooltip:this.refreshText,iconCls:"x-tbar-loading",handler:this.onClick,scope:this})];var userItems=this.items||this.buttons||[];if(this.prependButtons){this.items=userItems.concat(pagingItems);}else{this.items=pagingItems.concat(userItems);}
delete this.buttons;if(this.displayInfo){this.items.push('->');this.items.push(this.displayItem=new T.TextItem({}));}
Ext.PagingToolbar.superclass.initComponent.call(this);this.addEvents('change','beforechange');this.on('afterlayout',this.onFirstLayout,this,{single:true});this.cursor=0;this.bindStore(this.store);},onFirstLayout:function(ii){this.mon(this.inputItem.el,"keydown",this.onPagingKeyDown,this);this.mon(this.inputItem.el,"blur",this.onPagingBlur,this);this.mon(this.inputItem.el,"focus",this.onPagingFocus,this);this.field=this.inputItem.el.dom;if(this.dsLoaded){this.onLoad.apply(this,this.dsLoaded);}},updateInfo:function(){if(this.displayItem){var count=this.store.getCount();var msg=count==0?this.emptyMsg:String.format(this.displayMsg,this.cursor+1,this.cursor+count,this.store.getTotalCount());this.displayItem.setText(msg);}},onLoad:function(store,r,o){if(!this.rendered){this.dsLoaded=[store,r,o];return;}
this.cursor=(o.params&&o.params[this.paramNames.start])?o.params[this.paramNames.start]:0;var d=this.getPageData(),ap=d.activePage,ps=d.pages;this.afterTextItem.setText(String.format(this.afterPageText,d.pages));this.field.value=ap;this.first.setDisabled(ap==1);this.prev.setDisabled(ap==1);this.next.setDisabled(ap==ps);this.last.setDisabled(ap==ps);this.refresh.enable();this.updateInfo();this.fireEvent('change',this,d);},getPageData:function(){var total=this.store.getTotalCount();return{total:total,activePage:Math.ceil((this.cursor+this.pageSize)/this.pageSize),pages:total<this.pageSize?1:Math.ceil(total/this.pageSize)};},changePage:function(page){this.doLoad(((page-1)*this.pageSize).constrain(0,this.store.getTotalCount()));},onLoadError:function(){if(!this.rendered){return;}
this.refresh.enable();},readPage:function(d){var v=this.field.value,pageNum;if(!v||isNaN(pageNum=parseInt(v,10))){this.field.value=d.activePage;return false;}
return pageNum;},onPagingFocus:function(){this.field.select();},onPagingBlur:function(e){this.field.value=this.getPageData().activePage;},onPagingKeyDown:function(e){var k=e.getKey(),d=this.getPageData(),pageNum;if(k==e.RETURN){e.stopEvent();pageNum=this.readPage(d);if(pageNum!==false){pageNum=Math.min(Math.max(1,pageNum),d.pages)-1;this.doLoad(pageNum*this.pageSize);}}else if(k==e.HOME||k==e.END){e.stopEvent();pageNum=k==e.HOME?1:d.pages;this.field.value=pageNum;}else if(k==e.UP||k==e.PAGEUP||k==e.DOWN||k==e.PAGEDOWN){e.stopEvent();if(pageNum=this.readPage(d)){var increment=e.shiftKey?10:1;if(k==e.DOWN||k==e.PAGEDOWN){increment*=-1;}
pageNum+=increment;if(pageNum>=1&pageNum<=d.pages){this.field.value=pageNum;}}}},beforeLoad:function(){if(this.rendered&&this.refresh){this.refresh.disable();}},doLoad:function(start){var o={},pn=this.paramNames;o[pn.start]=start;o[pn.limit]=this.pageSize;if(this.fireEvent('beforechange',this,o)!==false){this.store.load({params:o});}},onClick:function(button){var store=this.store;switch(button){case this.first:this.doLoad(0);break;case this.prev:this.doLoad(Math.max(0,this.cursor-this.pageSize));break;case this.next:this.doLoad(this.cursor+this.pageSize);break;case this.last:var total=store.getTotalCount();var extra=total%this.pageSize;var lastStart=extra?(total-extra):total-this.pageSize;this.doLoad(lastStart);break;case this.refresh:this.doLoad(this.cursor);break;}},bindStore:function(store,initial){if(!initial&&this.store){this.store.un("beforeload",this.beforeLoad,this);this.store.un("load",this.onLoad,this);this.store.un("loadexception",this.onLoadError,this);if(store!==this.store&&this.store.autoDestroy){this.store.destroy();}}
if(store){store=Ext.StoreMgr.lookup(store);store.on("beforeload",this.beforeLoad,this);store.on("load",this.onLoad,this);store.on("loadexception",this.onLoadError,this);this.paramNames.start=store.paramNames.start;this.paramNames.limit=store.paramNames.limit;if(store.getCount()>0){this.onLoad(store,null,{});}}
this.store=store;},unbind:function(store){this.bindStore(null);},bind:function(store){this.bindStore(store);},onDestroy:function(){this.bindStore(null);Ext.PagingToolbar.superclass.onDestroy.call(this);}});})();Ext.reg('paging',Ext.PagingToolbar);