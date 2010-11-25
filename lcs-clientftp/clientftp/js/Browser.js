// Copyright (c) 2006 Marek Blotny
// 
// Permission is hereby granted, free of charge, to any person obtaining
// a copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to
// permit persons to whom the Software is furnished to do so, subject to
// the following conditions:
// 
// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
// LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
// OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

var Browser = {
	remote: null,
	table: null,
	icons: null,
	list: null,
	imagesDir: "pict/icons",
	logger: null,
	basepath: null,
	status: null,
	statistics: null,
	details: null,
	relativePath: null,
	entries: null,
	trayEntries: null,
	tray: null,
	openedFile: null,
	allowedFileTypes: null,
	editableFileTypes: null,
	viewModeTrigger: null,
	labels: null,
	mode: null,
	selectedEntryActionsSpan: null,
	trayForm: null,
	imagesArray: null,
	
	create: function() {
		this.imagesDir = this.imagesDir + "/" + this.remote.geticonsetname();
		this.preloadImages();
		
		if (this.remote.getviewmode() == 'list') {
			this.mode = '1';
		} else {
			this.mode = '2';
		}
		
		// to make sure that proper div-s are visible
		this.changeDisplayMode();
		this.changeDisplayMode();
		
		this.load(this.remote.getstartlocation());
		this.basepath = this.remote.basepath;
	},
	
	reload: function() {
		this.load(this.relativePath);
	},
	
	load: function(relativePath) {
		relativePath = unescape(relativePath);
		
		this.log(3, "[Browser.load] loading: " + relativePath);
		
		// asynchronous call
		this.remote.getentries(relativePath, function(entries) {
			this.log(2, "[Browser.load] sorting: " + relativePath);
			
			// sort
			entries.sort(Browser.comparator);
			
			// prepare relativeNames
			for(var i=0; i < entries.length; i++) {
				entries[i].relativePath = Browser.preparePath(entries[i].relativePath);
			}
			// store in memory result
			Browser.entries = entries;
			
			this.log(2, "[Browser.load] removing old content: " + relativePath);
			// get rid of old content
			//Browser.removeAllHTMLChildren(Browser.element);
			Browser.removeAllHTMLChildren(Browser.status);
			
			this.log(2, "[Browser.load] creating new content: " + relativePath);
			// add new content
			if (Browser.mode == '1') { Browser.replaceTableContent(entries); }
			else { Browser.replaceIconsContent(entries); }
			
			// update status bar
			Browser.relativePath = relativePath;
			Browser.displayLocation(relativePath);
			
			// update statistics bar
			Browser.displayStatistics(entries);
			this.log(2, "[Browser.load] loaded: " + relativePath);
		});
	},
	
	mkDir: function(name) {
		var newDir = this.preparePath(this.relativePath) + "/" + name;
		var localRelativePath = this.relativePath;
		this.log(2, "[Browser.mkDir] creating: " +newDir);
		this.remote.makedir(newDir, function(result) {
			
			if (result) {
				Browser.reload();
			} else {
				alert(Browser.getLabel('js_error_mkdir'));
			}
		});
	},
	
	mkFile: function(name) {
		var newFile = this.preparePath(this.relativePath) + "/" + name;
		var localRelativePath = this.relativePath;
		this.log(2, "[Browser.mkFile] creating: " +newFile);
		this.remote.makefile(newFile, function(result) {
			
			if (result) {
				Browser.reload();
			} else {
				alert(Browser.getLabel('js_error_mkfile'));
			}
		});
	},
	
	getAllowedFileTypes: function() {
		
		if (this.remote == null) {
			return "";
		}
		
		if (this.allowedFileTypes == null) {
			this.allowedFileTypes = this.remote.getallowedextensions();
			this.log(2,"[Browser.getAllowedFileTypes] " + this.allowedFileTypes);
		}
		
		return this.allowedFileTypes;
	},
	
	changeDisplayMode: function() {
		if (this.mode == '2') {
			this.mode = '1';
			if (this.entries != null ) { this.replaceTableContent(this.entries); }
			this.icons.style.display = 'none';
			this.list.style.display = 'block';
			this.viewModeTrigger.innerHTML = this.getLabel('js_show_icons');
		} else { 
			this.mode = '2';
			if (this.entries != null ) { this.replaceIconsContent(this.entries); } 
			this.icons.style.display = 'block';
			this.list.style.display = 'none';
			this.viewModeTrigger.innerHTML = this.getLabel('js_show_list');
		}
	},
	
	getLabel: function(key) {
		this.log(1,"[Browser.getLabel] request label " + key);
		
		if (this.labels == null) {
			this.log(4,"[Browser.getLabel] this.labels is null!");
			
			return "";
		}
		
		return this.labels[key];
	},
	
	getEditableFileTypes: function() {
		
		if (this.remote == null) {
			return "";
		}
		
		if (this.editableFileTypes == null) {
			this.editableFileTypes = this.remote.geteditableextensions();
			this.log(2,"[Browser.getEditableFileTypes] " + this.editableFileTypes);
		}
		
		return this.editableFileTypes;
	},
	
	displayLocation: function(relativePath) {
		var path = this.preparePath(relativePath);
		if (path.length == 0) {
			Browser.status.appendChild(Builder.node('span',{className:'location'},this.getLabel('js_root'))); 
		} else {
			Browser.status.appendChild(Builder.node('span',{className:'location'},this.getLabel('js_root') + "/" + path)); 
		}
	},
	
	displayStatistics: function(entries) {
		// there is always dir ".."
		var noOfDirs = -1;
 		var noOfFiles = 0;
		var filesSize = 0;
		
		for(var i = 0; i < this.entries.length; i++) {
			
			if (this.entries[i].type == 'dir' || this.entries[i].type == 'link') {
				noOfDirs++;
			} else {
				noOfFiles++;
				filesSize += parseInt(entries[i].size);
			}
		}
		
		var content = new Array();
		content[0] = Builder.node('li', this.getLabel('js_stat_dirs') + " " + noOfDirs);
		content[1] = Builder.node('li', this.getLabel('js_stat_files')+ " " + noOfFiles);
		content[2] = Builder.node('li', this.getLabel('js_stat_total_size') + " " + this.getHumanRedableSize(filesSize));
		
		// remove old data
		this.removeAllHTMLChildren(this.statistics);
		
		// dispaly data
		var element = Builder.node('ul',{className:''},content);
		this.statistics.appendChild(element); 
	},
	
	preparePath: function(path) {
		this.log(1, "[Browser.preparePath] path: " + path);
		// replace windows-like slashes with linux-like
		var myPath = path.replace(/\\/g,"/");
		if (myPath.substring(myPath.lastIndexOf('/') + 1 , myPath.length) == "..") {
			// cut off last part "/.."
			myPath = myPath.substring(0, myPath.length - 3);
			// and cut off last element
			myPath = myPath.substring(0, myPath.lastIndexOf('/'));
		} else if (myPath == ".") {
			myPath = "";
		}
		this.log(1, "[Browser.preparePath] returning: " + myPath);
		return myPath;
	},
	
	removeAllHTMLChildren: function(element) {
		if (element == null) return;
		
		while (element.childNodes.length > 0) {
			element.removeChild(element.firstChild);
		}
	},
	
	replaceTableContent: function(entries) {
		// check if table has any content
		var tbody;
		if (this.table.childNodes.length > 0) {
			tbody = this.table.childNodes[0];
			// remove old content
			this.removeAllHTMLChildren(tbody);
		} else {
			tbody = document.createElement('tbody');
			this.table.appendChild(tbody);
		}
		
		var frag = document.createDocumentFragment();
		for( var i=0; i < entries.length; i++) {
			frag.appendChild(this.createHTMLForList(entries[i]));
		}
		
		tbody.appendChild(frag);
	},
	
	replaceIconsContent: function(entries) {
		// check if table has any content
		this.removeAllHTMLChildren(this.icons);
		
		for( var i=0; i < entries.length; i++) {
			this.icons.appendChild(this.createHTMLForIcons(entries[i]));
		}
		
		
	},
	
	createContextMenu: function(entryName, menu) {
		
		// clear menu
		this.removeAllHTMLChildren(menu);
		
		if (entryName == "..") return;
		
		var entry = this.getEntryByName(unescape(entryName));
		if (entry == null) { 
			this.log(4, "[Browser.createContextMenu] can't find entry: [" + entryName + "]");
			return "";
		}
		
		// we have the entry
		if (entry.type == 'dir' || entry.type == 'link') {
			var deleteLink = Builder.node('a', {href:'javascript:void(0)', onClick:'deleteDirectory(\'' + escape(entry.relativePath) + '\'); hidePopupMenu();' }, this.getLabel('js_icons_delete'));
			var renameLink = Builder.node('a', {href:'javascript:void(0)', onClick:'showRenameFormDiv(\'' + escape(entry.relativePath) + '\'); hidePopupMenu();' }, this.getLabel('js_icons_rename'));
			var addToTrayLink = Builder.node('a', {href:'javascript:void(0)', onClick:'Browser.addToTray(\'' + escape(entry.name) + '\'); hidePopupMenu();' }, this.getLabel('tray_icons_add'));
					
			menu.appendChild(this.createDiv(renameLink, 'popupMenuItem'));
			menu.appendChild(this.createDiv(deleteLink, 'popupMenuItem'));
			menu.appendChild(this.createDiv(addToTrayLink, 'popupMenuItem'));
		} 
		else {
			var deleteLink = Builder.node('a', {href:'javascript:void(0)', onClick:'deleteFile(\'' + escape(entry.relativePath) + '\'); hidePopupMenu();' }, this.getLabel('js_icons_delete'));
			var renameLink = Builder.node('a', {href:'javascript:void(0)', onClick:'showRenameFormDiv(\'' + escape(entry.relativePath) + '\'); hidePopupMenu();' }, this.getLabel('js_icons_rename'));
			var downloadLink = Builder.node('a', {href:'index.php?action=DownloadForm&filename=' + escape(entry.relativePath), onClick:'hidePopupMenu();' }, this.getLabel('js_icons_download'));
			//var downloadImage = Builder.node('img',{src:this.imagesDir + "/" + "download.png", align:'middle', hspace:3, title:this.getLabel('js_list_title_download'), border:0});
			var editLink = Builder.node('a', {href:'javascript:void(0)', onClick:'showFormAndFileContent(\'' + escape(entry.relativePath) + '\'); hidePopupMenu();' }, this.getLabel('js_icons_edit'));
			var addToTrayLink = Builder.node('a', {href:'javascript:void(0)', onClick:'Browser.addToTray(\'' + escape(entry.name) + '\'); hidePopupMenu();' }, this.getLabel('tray_icons_add'));
			
			menu.appendChild(this.createDiv(renameLink, 'popupMenuItem'));
			menu.appendChild(this.createDiv(downloadLink, 'popupMenuItem'));
			menu.appendChild(this.createDiv(deleteLink, 'popupMenuItem'));
			menu.appendChild(this.createDiv(addToTrayLink, 'popupMenuItem'));
			
			if (entry.editable) { menu.appendChild(this.createDiv(editLink, 'popupMenuItem')); }
		}
	},
	
	createDiv: function(element, className, icon) {
		var div = Builder.node('div', { }, '');
		div.className = className;
		if (icon) div.appendChild(icon);
		div.appendChild(element);
		return div;
	},
	
	createHTMLForIcons: function(entry) {
		
		var pictDiv = document.createElement('div');
		pictDiv.className = 'iconPict';
		
		var labelDiv = document.createElement('div');
		labelDiv.className = 'iconLabel';
		
		var content = null;
		
		// name with link (in case of dir) or without a link
		if (entry.type == 'dir' || entry.type == 'link') {
			var content = Builder.node('a', {href:'javascript:void(0)', onClick:'Browser.load(\'' + escape(entry.relativePath) + '\')', oncontextmenu:'iconSelMenu(\'' + escape(entry.name) + '\')'}, '');
			
			pictDiv.appendChild(Builder.node('img',{src:this.getIconPath(entry), align:'middle', border:0}));
			this.prepareNameForIcons(labelDiv, entry.name); 
			
			content.appendChild(pictDiv);
			content.appendChild(labelDiv);
		} else { 
			var content = Builder.node('a', {href:'javascript:void(0)', onClick:'Browser.showDetails(\'' + escape(entry.name) + '\')', oncontextmenu:'iconSelMenu(\'' + escape(entry.name) + '\')' }, '');
			
			pictDiv.appendChild(Builder.node('img',{src:this.getIconPath(entry), align:'middle', border:0}));
			this.prepareNameForIcons(labelDiv, entry.name); 
			
			content.appendChild(pictDiv);
			content.appendChild(labelDiv);
		}
				
		var element = document.createElement('div');
		element.className = 'iconEntry';
		element.appendChild(content);
		
		this.log(1, "[Browser.createHTMLForIcons] " + element.innerHTML);
		return element;
	},
	
	prepareNameForIcons: function(div, name) {
		
		var nameLength = 12;
		
		if (name.length < nameLength) {
			div.appendChild(document.createTextNode(name));
			return;
		}
		
		// find space between 1 and 12
		var position = name.indexOf(' ');
		
		// if there is no space then return 
		if (position == -1 || position > nameLength) {
			div.appendChild(document.createTextNode(name.substr(0, nameLength)));
			div.appendChild(document.createElement("br"));
			div.appendChild(document.createTextNode(this.truncate(name.substr(nameLength),nameLength)));
			return;
		}
		
		var lastPosition = position;
		while (position != -1 && position < nameLength) {
			lastPosition = position;
			position = name.indexOf(' ', lastPosition + 1);
		}
		
		div.appendChild(document.createTextNode(name.substr(0, lastPosition)));
		div.appendChild(document.createElement("br"));
		div.appendChild(document.createTextNode(this.truncate(name.substr(lastPosition + 1),nameLength)));
	},
	
	createHTMLForList: function(entry) {
		
		var cell_1 = document.createElement('td');
		cell_1.setAttribute('nowrap', 'true');
		cell_1.className = 'td100perc';
		
		// image
		cell_1.appendChild(Builder.node('img',{src:this.getImagePath(entry), align:'middle', hspace:3, vspace:1, border:0}))
		// name with link (in case of dir) or without a link
		if (entry.type == 'dir' || entry.type == 'link') {
			cell_1.appendChild(Builder.node('a',{href:'javascript:void(0)', onClick:'Browser.load(\'' + escape(entry.relativePath) + '\')'}, this.truncate(entry.name, 50))); 
		} else { 
			cell_1.appendChild(Builder.node('a',{href:'javascript:void(0)',  onClick:'Browser.showDetails(\'' + escape(entry.name) + '\')' }, this.truncate(entry.name, 50))); 
		}
				
		// cell 2
		var cell_2 = document.createElement('td');
		cell_2.setAttribute('nowrap', 'true');
		cell_2.className = 'td75';
		if (entry.type == 'dir' || entry.type == 'link') { cell_2.appendChild(document.createTextNode(this.getHumanRedableSize(0))); }
		else { cell_2.appendChild(document.createTextNode(this.getHumanRedableSize(entry.size))); }
		
		// cell 3
		var cell_3 = document.createElement('td');
		if (entry.type != 'dir' && entry.type != 'link') { 
			var image = Builder.node('img',{src:this.imagesDir + "/" + "download.png", align:'middle', hspace:2, vspace:1, title:this.getLabel('js_list_title_download'), border:0});
			cell_3.appendChild(Builder.node('a',{href:'index.php?action=DownloadForm&filename=' + escape(entry.relativePath) }, ''));
			cell_3.childNodes[0].appendChild(image);
		}
		
		// cell 4
		var cell_4 = document.createElement('td');
		if (entry.editable) { 
			var image = Builder.node('img',{src:this.imagesDir + "/" + "edit.png", align:'middle', hspace:2, vspace:1, title:this.getLabel('js_list_title_edit'), border:0});
			cell_4.appendChild(Builder.node('a',{href:'javascript:void(0)', onClick:'showFormAndFileContent(\'' + escape(entry.relativePath) + '\')' }, '')); 
			cell_4.childNodes[0].appendChild(image);
		}
		
		// cell 5
		var cell_5 = document.createElement('td');
		if (entry.name != "..") { 
			var image = Builder.node('img',{src:this.imagesDir + "/" + "delete.png", align:'middle', hspace:2, vspace:1, title:this.getLabel('js_list_title_delete'), border:0});
			if (entry.type == 'dir' || entry.type == 'link') { cell_5.appendChild(Builder.node('a',{href:'javascript:void(0)',  onClick:'deleteDirectory(\'' + escape(entry.relativePath) + '\')' }, '')); }
			else { cell_5.appendChild(Builder.node('a',{href:'javascript:void(0)',  onClick:'deleteFile(\'' + escape(entry.relativePath) + '\')' }, '')); }
			cell_5.childNodes[0].appendChild(image);
		}
		
		// cell 6
		var cell_6 = document.createElement('td');
		if (entry.name != "..") { 
			var image = Builder.node('img',{src:this.imagesDir + "/" + "rename.png", align:'middle', hspace:2, vspace:1, title:this.getLabel('js_list_title_rename'), border:0});
			cell_6.appendChild(Builder.node('a',{href:'javascript:void(0)', onClick:'showRenameFormDiv(\'' + escape(entry.relativePath) + '\')' }, '')); 
			cell_6.childNodes[0].appendChild(image);
		}

		// cell 7
		var cell_7 = document.createElement('td');
		if (entry.name != "..") { 
			var image = Builder.node('img',{src:this.imagesDir + "/" + "addtotray.png", align:'middle', hspace:2, vspace:1, title:this.getLabel('tray_icons_add'), border:0});
			cell_7.appendChild(Builder.node('a',{href:'javascript:void(0)', onClick:'Browser.addToTray(\'' + escape(entry.name) + '\')' }, '')); 
			cell_7.childNodes[0].appendChild(image);
		}

		var element = document.createElement('tr');
		
		element.appendChild(cell_1);
		element.appendChild(cell_2);
		element.appendChild(cell_4);
		element.appendChild(cell_3);
		element.appendChild(cell_6);
		element.appendChild(cell_5);
		element.appendChild(cell_7);
		element.className = 'entryrow';
				
		this.log(1, "[Browser.createHTMLForList] " + element.innerHTML);
		return element;
	},
	
	//// TRAY
	
	addToTray: function(entryName) {
		
		var entry = this.getEntryByName(unescape(entryName));
		if (entry == null) { 
			this.log(2, "[Browser.addToTray] can't find entry: [" + entryName + "]");
			alert(this.getLabel('tray_error_no_entry') + ' ' + entryName + ' ');
			return;
		}
		
		// check if entry is already in tray
		if(this.getFromTray(entry.relativePath)) {
			this.log(4, "[Browser.addToTray] entry already in tray: [" + entry.name + "]");
			alert(this.getLabel('tray_error_element_in_tray'));
			return;
		}
		
		if (!this.trayEntries) { this.trayEntries = new Array(); }
		if (this.trayEntries.length == 0) { this.removeAllHTMLChildren(this.tray); }
		
		this.trayEntries.push(entry);
		this.tray.appendChild(this.createSingleEntryToTray(entry, true));
		this.prepareActionsMenuForTray(entry);
		
		this.log(2, "[Browser.addToTray] entry added to tray: [" + entry.relativePath + "]");
	},
	
	createSingleEntryToTray: function(entry, checked) {
		var link = Builder.node('a', {href:'javascript:void(0)',  onClick:'Browser.showDetails(\'' + escape(entry.name) + '\', \'' + escape(entry.relativePath) + '\')' }, this.truncate(entry.name,25)); 
		var span = Builder.node('span', {}, ''); 
		var checkbox = Builder.node('input', {type:'checkbox', onClick:'Browser.changeColour(this);', name:'trayEntries', value:escape(entry.relativePath)}, '');
		var htmlElement = Builder.node('div', {className:'trayEntry', id:'trayElement_' + escape(entry.relativePath) }, '');
		
		if (checked) { checkbox.checked = 'true'; }
		
		span.appendChild(link);		
		htmlElement.appendChild(checkbox);
		htmlElement.appendChild(span);
		
		this.changeColour(checkbox);
		return htmlElement;
	},
	
	changeColour: function(checkbox) {
		if (checkbox.checked) { checkbox.parentNode.className = 'trayEntrySelected'; } 
		else { checkbox.parentNode.className = 'trayEntry'; }
	},
	
	removeFromTraySingleEntry: function(relativePath, exactMatch) {
		
		relativePath = this.preparePath(unescape(relativePath));
		this.log(8, "[Browser.removeFromTraySingleEntry] relativePath: [" + relativePath + "]");
		
		if (!this.trayEntries) { return; }
		
		for(var i=0; i < this.trayEntries.length; i++) {
			var pathToCheck = this.preparePath(this.trayEntries[i].relativePath);
			
			if (pathToCheck == relativePath || (pathToCheck.indexOf(relativePath) == 0 && !exactMatch)) {
				this.log(8, "[Browser.removeFromTraySingleEntry] removing : [" + pathToCheck + "]");
				this.trayEntries.splice(i,1);
				i--;
				var div = document.getElementById('trayElement_' + escape(pathToCheck));
				div.parentNode.removeChild(div);
				if (this.trayEntries.length == 0) { this.tray.innerHTML = this.getLabel('tray_no_files'); }
			}
		}
		this.log(2, "[Browser.removeFromTraySingleEntry] entry removed from tray : [" + relativePath + "]");
	},
	
	
	removeFromTray: function() {
		var selectedTrayEntries = this.getSelectedTrayEntries();
		if (selectedTrayEntries.length == 0) { alert (this.getLabel('tray_error_no_files_selected')); }
		
		for (i=0; i < selectedTrayEntries.length; i++) { this.removeFromTraySingleEntry(selectedTrayEntries[i], true); }
	},
	
	getSelectedTrayEntries: function() {
		var form = this.trayForm;
		var array = new Array();
		
		for (i=0; i < form.length; i++) {
			if (form.elements[i].type == "checkbox") { 
				if (form.elements[i].checked) { array.push(form.elements[i].value); }
			}
		}
		
		return array;
	},
	
	copyFromTray: function() {
		var selectedTrayEntries = this.getSelectedTrayEntries();
		if (selectedTrayEntries.length == 0) { alert (this.getLabel('tray_error_no_files_selected')); }
		
		var separatedList = "";
		
		for (i=0; i < selectedTrayEntries.length; i++) {
			
			var relativePath = unescape(selectedTrayEntries[i]);
		
			var entry = this.getFromTray(relativePath);
			if (entry == null) { 
				this.log(4, "[Browser.copyFromTray] can't find entry: [" + relativePath + "]");
				return;
			}
			
			var destination = this.preparePath(this.relativePath);
			if (destination.length == 0) { destination = entry.name; }
			else { destination = this.preparePath(destination + '/' + entry.name); }
			
			if (relativePath == destination) {
				alert(this.getLabel('tray_error_destination_equals_source') + ' ' + entry.name);
				return; 
			}
			
			if (this.getEntryByName(entry.name) != null) {
				alert(entry.name + ' ' + this.getLabel('tray_error_entry_exists'));
				return;
			}
			
			separatedList = separatedList + relativePath + '|';
		}
		
		if (separatedList.length != 0) {
			this.remote.copyentries(separatedList, this.preparePath(this.relativePath), function(result) {
			
				if (result) {
					Browser.reload();
				} else {
					alert(Browser.getLabel('js_error_general'));
				}
			});
		}
	},
	
	moveFromTray: function() {
		var selectedTrayEntries = this.getSelectedTrayEntries();
		if (selectedTrayEntries.length == 0) { alert (this.getLabel('tray_error_no_files_selected')); }
		
		var separatedList = "";
		
		for (i=0; i < selectedTrayEntries.length; i++) {
			
			var relativePath = unescape(selectedTrayEntries[i]);
		
			var entry = this.getFromTray(relativePath);
			if (entry == null) { 
				this.log(4, "[Browser.copyFromTray] can't find entry: [" + relativePath + "]");
				return;
			}
			
			var destination = this.preparePath(this.relativePath);
			if (destination.length == 0) { destination = entry.name; }
			else { destination = this.preparePath(destination + '/' + entry.name); }
			
			if (relativePath == destination) {
				alert(this.getLabel('tray_error_destination_equals_source') + ' ' + entry.name);
				return; 
			}
			
			if (this.getEntryByName(entry.name) != null) {
				alert(entry.name + ' ' + this.getLabel('tray_error_entry_exists'));
				return;
			}
			
			separatedList = separatedList + relativePath + '|';
		}
		
		if (separatedList.length != 0) {
			this.remote.moveentries(separatedList, this.preparePath(this.relativePath), function(result) {
			
				if (result) {
					for (i=0; i < selectedTrayEntries.length; i++) {
						Browser.removeFromTraySingleEntry(selectedTrayEntries[i], false);
					}
					Browser.reload();
				} else {
					alert(Browser.getLabel('js_error_general'));
				}
			});
		}
	},
	
	
	updateTrayEntry: function(relativePath, newName) {
		relativePath = this.preparePath(unescape(relativePath));
		var newRelativePath = relativePath.substring(0, relativePath.lastIndexOf('/') +1 ) + newName;
		
		this.log(2, "[Browser.updateTrayEntry] entry updated from : [" + relativePath + "] to [" + newRelativePath + "]");
		
		if (!this.trayEntries) { return; }
		
		for(var i=0; i < this.trayEntries.length; i++) {
			var tempEntry = this.trayEntries[i];
			var tempRelativePath = this.preparePath(tempEntry.relativePath);
			
			if (tempRelativePath.indexOf(relativePath) == 0) {
				if (tempRelativePath == relativePath) { tempEntry.name = newName; }
				tempEntry.relativePath = tempRelativePath.replace(relativePath,newRelativePath);
				
				var div = document.getElementById('trayElement_' + escape(tempRelativePath));
				var newDiv = this.addSingleEntryToTray(tempEntry, div.childNodes[0].checked);
				this.tray.replaceChild(newDiv,div);
			}
		}
		
		this.log(2, "[Browser.updateTrayEntry] entry updated from : [" + relativePath + "] to [" + newName + "]");
	},
	
	prepareActionsMenuForTray: function(entry) {
		
		// clear
		this.removeAllHTMLChildren(this.selectedEntryActionsSpan);
		
		var addSpan = document.createElement('span');
		var image = Builder.node('img',{src:this.imagesArray[this.imagesDir + "/" + "removefromtray.png"].src, align:'middle', hspace:2, vspace:1, title:this.getLabel('tray_icons_remove'), border:0});
		addSpan.appendChild(Builder.node('a',{href:'javascript:void(0)', onClick:'Browser.removeFromTray()' }, '')); 
		addSpan.childNodes[0].appendChild(image);
		this.selectedEntryActionsSpan.appendChild(addSpan);
	
		addSpan = document.createElement('span');
		image = Builder.node('img',{src:this.imagesArray[this.imagesDir + "/" + "traycopy.png"].src, align:'middle', hspace:2, vspace:1, title:this.getLabel('tray_icons_copy'), border:0});
		addSpan.appendChild(Builder.node('a',{href:'javascript:void(0)', onClick:'Browser.copyFromTray()' }, '')); 
		addSpan.childNodes[0].appendChild(image);
		this.selectedEntryActionsSpan.appendChild(addSpan);
	
		addSpan = document.createElement('span');
		image = Builder.node('img',{src:this.imagesArray[this.imagesDir + "/" + "traymove.png"].src, align:'middle', hspace:2, vspace:1, title:this.getLabel('tray_icons_move'), border:0});
		addSpan.appendChild(Builder.node('a',{href:'javascript:void(0)', onClick:'Browser.moveFromTray()' }, '')); 
		addSpan.childNodes[0].appendChild(image);
		this.selectedEntryActionsSpan.appendChild(addSpan);
	},
	
	
	getFromTray: function(relativePath)  {
		if (!this.trayEntries) { return false; }
		relativePath = this.preparePath(relativePath);
		
		for(var i = 0; i < this.trayEntries.length; i++) {
			if (this.trayEntries[i].relativePath == relativePath) { return this.trayEntries[i]; }
		}
		return null;
	},
	
	//// TRAY
	
	loadFileContent: function(relativePath) {
		relativePath = unescape(relativePath);
		this.log(2, "[Browser.loadFileContent] " + relativePath);
		this.openedFile = relativePath;
		return content = this.remote.getfilecontent(relativePath);
	},
	
	saveFileContent: function(content) {
		this.log(2, "[Browser.saveFileContent] " + this.openedFile);
		this.remote.savefilecontent(this.openedFile, content, function() {
			Browser.reload();
		});
	},
	
	updateEntryName: function(relativePath, newName) {
		relativePath = unescape(relativePath);
		this.log(2, "[Browser.updateEntryName] " + relativePath);
		this.remote.updateentryname(relativePath, newName, function(result) {
			
			if (result) {
				Browser.updateTrayEntry(relativePath, newName);
				Browser.reload();
			} else {
				alert(Browser.getLabel('js_error_general'));
			}
		});
	},
	
	deleteItem: function(relativePath) {
		relativePath = unescape(relativePath);
		this.log(2, "[Browser.deleteItem] " + relativePath);
		this.remote.deleteitem(relativePath, function(result) {
			
			if (result) {
				Browser.removeFromTraySingleEntry(escape(relativePath), false);
				Browser.reload();
			} else {
				alert(Browser.getLabel('js_error_general'));
			}
		});
	},
	
	
	showDetails: function(entryName, relativePath) {
		entryName = unescape(entryName);
		relativePath = unescape(relativePath);
		this.log(2, "[Browser.showDetails] " + entryName);
		
		var entry = null;
		
		// relative path is set when link comes from tray
		if (Browser.showDetails.arguments.length == 2) {
			entry = this.getFromTray(relativePath);
		} else {
			if (this.entries == null) return;
			entry = this.getEntryByName(entryName);
		}
		
		if (entry == null) { return; }
		
		var content = new Array();
		var counter = 0;
		var image = '';
		
		this.log(1, "[Browser.showDetails] entry.thumbnail: " + entry.thumbnail);
		
		if (entry.thumbnail) {
			image = Builder.node('img',{className:'thumbnail', id:'thumbnailImage', border:2});
			// preload image and then call back function
			new ImagePreloader('index.php?action=GetThumbnail&filename=' + this.preparePath(entry.relativePath), this.onPreloadThumbnail);
			this.log(1, "[Browser.showDetails] " + 'index.php?action=GetThumbnail&filename=' + this.preparePath(entry.relativePath));
		}
		
		var maxNameLength = 25;
		
		if (entry.name.length > maxNameLength) {
			content[counter++] = Builder.node('li', this.getLabel('js_details_name') + " " + this.truncate(entry.name,maxNameLength) );		
		} else {
			content[counter++] = Builder.node('li', this.getLabel('js_details_name') + " " + entry.name);
		}
		
		content[counter++] = Builder.node('li', this.getLabel('js_details_size') + " " + this.getHumanRedableSize(entry.size));
		content[counter++] = Builder.node('li', [this.getLabel('js_details_last_mod'), Builder.node('div', entry.lastModify)], "" );
		content[counter++] = Builder.node('li', this.getLabel('js_details_permissions') + " " + entry.permissions);
		
		// remove old data
		this.removeAllHTMLChildren(this.details);
		
		// dispaly data
		var basicInfo = Builder.node('ul',{className:''},content);
		
		if (entry.thumbnail) { this.details.appendChild(image); }
		this.details.appendChild(basicInfo);
	},
	
	onPreloadThumbnail: function (image, src) {
		$('thumbnailImage').src = image.src;
	},
	
	preloadImages: function () {
		var images = new Array ("removefromtray.png", "traycopy.png", "traymove.png", "b_directory.png",
					"txt.png", "quicktime.png", "source_css.png", "source_php.png", "sound.png",
					"html.png", "pdf.png", "ooo_writer.png", "image.png", "tar.png", "b_empty.png",
					"directory.png", "doc.png", "addtotray.png", "rename.png", "delete.png", "edit.png",
					"download.png" );
					
		for(var i = 0; i < images.length; i++) {
			this.log(8, "[Browser.preloadImages] image: " + images[i]);
			new ImagePreloader(this.imagesDir + "/" + images[i], this.onPreload);
		}
	},
	
	onPreload: function (image, src) {
		if (!Browser.imagesArray) { Browser.imagesArray = new Array; }
		Browser.log(8, "[Browser.onPreload] image: " + src);
		Browser.imagesArray[src] = image;
	},

	
	getEntryByName: function(entryName) {
		if (this.entries == null) return;
		
		for(var i = 0; i < this.entries.length; i++) {
			
			if (this.entries[i].name == entryName) {
				return this.entries[i];
			}
		}
		
		return null;
	},
	
	getEntryByRelativePath: function(relativePath) {
		if (this.entries == null) return;
		
		relativePath = unescape(relativePath);
		
		for(var i = 0; i < this.entries.length; i++) {
			
			if (this.entries[i].relativePath == relativePath) {
				return this.entries[i];
			}
		}
		
		return null;
	},
	
	fileExists: function(filename) {
		if (this.entries == null) return false;
		
		for(var i = 0; i < this.entries.length; i++) {
			
			if (this.entries[i].name == filename) {
				return true;
			}
		}
		
		return false;
	},
	
	getHumanRedableSize: function(size) {
		if (size == 0) return "";
		
		if (size < 1024) {
			return size + " B";
		} else if (size > 1024 && size < 1048576) {
			return (Math.round(eval(size) / 1024)) + " kB";
		} else if (size > 1048576) {
			return (Math.round(eval(size) / 1048576)) + " MB";
		}
	},
	
	getIconPath: function(entry) {
		if (entry.type == 'dir' || entry.type == 'link') { return this.imagesDir + "/" + "b_directory.png"; }
		else {
			var ext = this.getFileExtension(entry.name);
			this.log(2, 'file: ' + entry.name + ' extension is: ' + ext);
			
			var picture = this.imagesDir + "/";
			
			if (ext == "txt") { 
				picture = picture + "txt.png";
			} else if (ext == "avi" || ext == "mov") {
				picture = picture + "quicktime.png";
			} else if (ext == "css") {
				picture = picture + "source_css.png";
			} else if (ext == "php") {
				picture = picture + "source_php.png";
			} else if (ext == "wav" || ext == "mp3") {
				picture = picture + "sound.png";
			} else if (ext == "html" || ext == "htm") {
				picture = picture + "html.png";
			} else if (ext == "pdf") {
				picture = picture + "pdf.png";
			} else if (ext == "doc" || ext == "sxw") {
				picture = picture + "ooo_writer.png";
			} else if (ext == "gif" || ext == "jpg" || ext == "jpeg" || ext == "bmp" || ext == "png" || ext == "tif") {
				picture = picture + "image.png";
			} else if (ext == "zip" || ext == "rar" || ext == "tar" || ext == "gz") {
				picture = picture + "tar.png";
			} else { 
				picture = picture + "b_empty.png"; 
			}
			
			return picture ; 
		}
	},
	
	getImagePath: function(entry) {
		if (entry.type == 'dir' || entry.type == 'link') { return this.imagesDir + "/" + "directory.png"; }
		else { return this.imagesDir + "/" + "doc.png"; }
	},
	
	comparator: function(a,b) {

		if (a.type == b.type) {
			if (a.name == b.name) { return 0; }
			else if (a.name > b.name) { return 1; }
			else return -1;
		} else if (a.type == 'dir' && b.type != 'dir') {
			return -1;
		} else return 1;
	},
	
	getFileExtension: function(filename) {
		filename = "" + filename;
		var position = filename.lastIndexOf("."); 
		
		if (position == -1) {
			return filename;
		} else {
			return filename.substring(position + 1, filename.length);
		}
	},
	
	truncate: function(string, length) {
		if (string.length < length) {
			return string;
		} else {
			return string.substring(0, length-3) + "...";
		}
	},
	
	log: function(level, message) {
		if (this.logger != null) {
			this.logger(level, message);
		}
	}
}
