/************************************************************************************************************
	(C) www.dhtmlgoodies.com, November 2005
	
	This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	
	
	Terms of use:
	You are free to use this script as long as the copyright message is kept intact. However, you may not
	redistribute, sell or repost it without our permission.
	
	Thank you!
	
	www.dhtmlgoodies.com
	Alf Magne Kalleland
	
	************************************************************************************************************/
		
	var dhtmlgoodies_tree;
	var imageFolder = 'images/';	// Path to images
	var folderImage = 'dhtmlgoodies_folder.gif';
	var plusImage = 'dhtmlgoodies_plus.gif';
	var minusImage = 'dhtmlgoodies_minus.gif';
	var initExpandedNodes = '';	// Cookie - initially expanded nodes;
	var fileName = 'updateNode.php';	// External file called by AJAX	
	var timeoutEdit = 20;	// Lower value = shorter delay from mouse is pressed down to textbox appears.
	
	
	
	
	/*
	These cookie functions are downloaded from 
	http://www.mach5.com/support/analyzer/manual/html/General/CookiesJavaScript.htm
	*/
	function Get_Cookie(name) { 
	   var start = document.cookie.indexOf(name+"="); 
	   var len = start+name.length+1; 
	   if ((!start) && (name != document.cookie.substring(0,name.length))) return null; 
	   if (start == -1) return null; 
	   var end = document.cookie.indexOf(";",len); 
	   if (end == -1) end = document.cookie.length; 
	   return unescape(document.cookie.substring(len,end)); 
	} 
	// This function has been slightly modified
	function Set_Cookie(name,value,expires,path,domain,secure) { 
		expires = expires * 60*60*24*1000;
		var today = new Date();
		var expires_date = new Date( today.getTime() + (expires) );
	    var cookieString = name + "=" +escape(value) + 
	       ( (expires) ? ";expires=" + expires_date.toGMTString() : "") + 
	       ( (path) ? ";path=" + path : "") + 
	       ( (domain) ? ";domain=" + domain : "") + 
	       ( (secure) ? ";secure" : ""); 
	    document.cookie = cookieString; 
	} 
	
	function expandAll()
	{
		var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI');
		for(var no=0;no<menuItems.length;no++){
			var subItems = menuItems[no].getElementsByTagName('UL');
			if(subItems.length>0 && subItems[0].style.display!='block'){
				showHideNode(false,menuItems[no].id.replace(/[^0-9]/g,''));
			}			
		}
	}
	
	function collapseAll()
	{
		var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI');
		for(var no=0;no<menuItems.length;no++){
			var subItems = menuItems[no].getElementsByTagName('UL');
			if(subItems.length>0 && subItems[0].style.display=='block'){
				showHideNode(false,menuItems[no].id.replace(/[^0-9]/g,''));
			}			
		}		
	}
			
	function showHideNode(e,inputId)
	{
		if(inputId){
			if(!document.getElementById('dhtmlgoodies_treeNode'+inputId))return;
			thisNode = document.getElementById('dhtmlgoodies_treeNode'+inputId).getElementsByTagName('IMG')[0]; 
		}else {
			thisNode = this;
		}
		if(thisNode.style.visibility=='hidden')return;
		var parentNode = thisNode.parentNode;
		inputId = parentNode.id.replace(/[^0-9]/g,'');
		if(thisNode.src.indexOf('plus')>=0){
			thisNode.src = thisNode.src.replace('plus','minus');
			parentNode.getElementsByTagName('UL')[0].style.display='block';
			if(!initExpandedNodes)initExpandedNodes = ',';
			if(initExpandedNodes.indexOf(',' + inputId + ',')<0) initExpandedNodes = initExpandedNodes + inputId + ',';
			
		}else{
			thisNode.src = thisNode.src.replace('minus','plus');
			parentNode.getElementsByTagName('UL')[0].style.display='none';
			initExpandedNodes = initExpandedNodes.replace(',' + inputId,'');
		}	
		Set_Cookie('dhtmlgoodies_expandedNodes',initExpandedNodes,500);
	}

	function okToNavigate()
	{
		if(editCounter<10)return true;
		return false;		
	}
	
	var editCounter = -1;
	var editEl = false;
	
	function initEditLabel()
	{	
		if(editEl)hideEdit();
		editCounter = 0;
		editEl = this;	// Referenc to a Tag
		startEditLabel();
	}
	
	function startEditLabel()
	{
		if(editCounter>=0 && editCounter<10){
			editCounter = editCounter + 1;
			setTimeout('startEditLabel()',timeoutEdit);
			return;
		}
		if(editCounter==10){
			var el = editEl.previousSibling;
			el.value = editEl.innerHTML;
			editEl.style.display='none';
			el.style.display='inline';	
			el.select();
			return;
		}		
	}
	
	
	
	function hideEdit()
	{				
		var editObj = editEl.previousSibling;	
		if(editObj.value.length>0){
			editEl.innerHTML = editObj.value;	
			var url = fileName ;
			var params = '?updateNode='+editObj.id.replace(/[^0-9]/g,'') + '&newValue='+editObj.value;
			new Ajax.Updater('ajaxMessage',url,{ method: 'get' , parameters:params, onComplete: function(requester) {
		//eval(requester.responseText);
		}
		});
					
		}
		editEl.style.display='inline';
		editObj.style.display='none';
		editEl = false;			
		editCounter=-1;
	}
	
	function mouseUpEvent()
	{
		editCounter=-1;		
	}
	
	function initTree()
	{
		dhtmlgoodies_tree = document.getElementById('dhtmlgoodies_tree');
		var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI');	// Get an array of all menu items
		for(var no=0;no<menuItems.length;no++){
			var subItems = menuItems[no].getElementsByTagName('UL');
			var img = document.createElement('IMG');
			img.src = imageFolder + plusImage;
			img.onclick = showHideNode;
			if(subItems.length==0)img.style.visibility='hidden';
			var aTag = menuItems[no].getElementsByTagName('A')[0];
			
			if(aTag.id)numericId = aTag.id.replace(/[^0-9]/g,'');else numericId = (no+1);
			
			aTag.id = 'dhtmlgoodies_treeNodeLink' + numericId;
			
			var input = document.createElement('INPUT');
			input.style.width = '200px';
			input.style.display='none';
			menuItems[no].insertBefore(input,aTag);
			input.id = 'dhtmlgoodies_treeNodeInput' + numericId;
			input.onblur = hideEdit;
						
			menuItems[no].insertBefore(img,input);
			menuItems[no].id = 'dhtmlgoodies_treeNode' + numericId;
			aTag.onclick = okToNavigate;
			aTag.onmousedown = initEditLabel;
			var folderImg = document.createElement('IMG');
			if(menuItems[no].className){
				folderImg.src = imageFolder + menuItems[no].className;
			}else{
				folderImg.src = imageFolder + folderImage;
			}
			menuItems[no].insertBefore(folderImg,input);
		}	
		
		initExpandedNodes = Get_Cookie('dhtmlgoodies_expandedNodes');
		if(initExpandedNodes){
			var nodes = initExpandedNodes.split(',');
			for(var no=0;no<nodes.length;no++){
				if(nodes[no])showHideNode(false,nodes[no]);	
			}			
		}	
		
		document.documentElement.onmouseup = mouseUpEvent;
	}
	

	Event.observe(window,'load',initTree,false);