// Copyright (c) 2006 Marek Blotny 
//
// See Browser.js for full license.


var remoteBrowser = null;
var labels = null;
var uploadInProgress = false;
var uploadingPath = null;
var logLevel = 5;

function log(level, text) {
	if (level > logLevel) {
		var dateObject = new Date();
		var myForm = document.getElementById('debugForm');
		myForm.debug.value = dateObject.getMinutes() +":" + dateObject.getSeconds()+ ":" + dateObject.getMilliseconds()+ " [" + level + "] " + text + "\n" + myForm.debug.value;
	}
}

function submitDirectoryForm() {
	var form = $('directoryForm');
	var name = form.name.value;
	if (name == null || name.length == 0) {
		alert(labels['js_error_provide_dir']);
		form.name.focus();
	} else {
		Browser.mkDir(name);
		form.reset();
		changeElementVisibility('directoryFormDiv', 'none');
	}
}


function submitFileForm() {
	var form = $('fileForm');
	var name = form.name.value;
	if (name == null || name.length == 0) {
		alert(labels['js_error_provide_file']);
		form.name.focus();
	} else {
		name = name + "." + form.extension.value;
		Browser.mkFile(name);
		form.reset();
		changeElementVisibility('fileFormDiv', 'none');
	}
}


function submitRenameForm() {
	var form = $('renameForm');
	var name = form.name.value;
	if (name == null || name.length == 0) {
		alert(labels['js_error_provide_name']);
		form.name.focus();
	} else {
		var relativepath = form.relativepath.value;
		Browser.updateEntryName(relativepath, name);
		form.reset();
		changeElementVisibility('renameFormDiv', 'none');
	}
}

function changeElementVisibility(name, state) {
	var element = $(name);
	
	if (element == null) {
		log(3, "showElement() - element [" + name +"] doesn't exist");
		return;
	}
	
	element.style.display = state;
}

function getUploadDiv() {
	return document.getElementById('uploadFormDiv');
}

function changeUploadFormVisibility(state) {
	var uploadFormDiv = getUploadDiv();
	if (state == 'block') {
		uploadFormDiv.style.display = state;
		var uploadStatus = $("uploadStatusDiv");
		uploadStatus.style.display = 'none';
		var uploadIframe = $("uploadIframe");
		uploadIframe.setAttribute("height", 100);
	} else {
		uploadFormDiv.style.display = state;
		var uploadStatus = $("uploadStatusDiv");
		uploadStatus.style.display = 'block';
		var uploadIframe = $("uploadIframe");
		uploadIframe.setAttribute("height", 0);
	}
	//uploadFormDiv.style.visibility = state;
	//uploadFormDiv.style.display = state;
}

function showUploadForm() {
	if (isUploadInProgress()) {
		alert(labels['js_error_upload_in_progress']);
		return;
	}
	
	changeUploadFormVisibility('block');
}

function isUploadInProgress() { return uploadInProgress; }

function uploadForm(startUpload) {
	
	if (startUpload) {
		log(2, "upload started");
		var uploadStatus = $("uploadStatusDiv");
		uploadStatus.style.display = 'block';
		var uploadIframe = $("uploadIframe");
		uploadIframe.setAttribute("height", 0);
		uploadInProgress = true;
		uploadingPath = Browser.relativePath;
		
	} else {
		if ( uploadInProgress ) {
			log(2, "upload finished");
			changeUploadFormVisibility('none');
			uploadInProgress = false;
			if (uploadingPath == Browser.relativePath) {
				Browser.reload();
			}
		}
	}
}


function changeFileContentEditVisibility(state) {
	var fileContentEditDiv = document.getElementById('fileContentEditDiv');
	
	if (state == 'block') {
		fileContentEditDiv.style.display = state;
	} else {
		fileContentEditDiv.style.display = state;
	}
}

function showFormAndFileContent(relativePath) {
	
	changeFileContentEditVisibility('block');
	var fileContentEditForm = document.getElementById('fileContentEditForm');
	fileContentEditForm.fileContent.value = Browser.loadFileContent(relativePath);
	fileContentEditForm.fileContent.focus();
}


function saveFileContent() {
	
	var fileContentEditForm = document.getElementById('fileContentEditForm');
	Browser.saveFileContent(fileContentEditForm.fileContent.value);
	changeFileContentEditVisibility('none');
}

function deleteDirectory(relativePath) {
	var answer = confirm (labels['js_delete_dir_confirm'])
	if (answer) { Browser.deleteItem(relativePath); }
}

function deleteFile(relativePath) {
	var answer = confirm (labels['js_delete_file_confirm'])
	if (answer) { Browser.deleteItem(relativePath); }
}

function showDirectoryFormDiv() {
	var myForm = document.getElementById('directoryForm');
	changeElementVisibility('directoryFormDiv', 'block');
	myForm.name.focus();
}

function showFileFormDiv() {
	var myForm = document.getElementById('fileForm');
	var editableExtensions = Browser.getEditableFileTypes().split(",");
	
	if (editableExtensions.length == 0) {
		alert(labels['js_no_editable_ext']);
	}
	
	for (var i=0; i < editableExtensions.length; i++) {
		myForm.extension.options[i] = new Option(editableExtensions[i],editableExtensions[i]);
	}
	changeElementVisibility('fileFormDiv', 'block');
	myForm.name.focus();
}


function showRenameFormDiv(relativePath) {
	var myForm = document.getElementById('renameForm');
	myForm.name.value = Browser.getEntryByRelativePath(relativePath).name;
	myForm.relativepath.value = relativePath;

	changeElementVisibility('renameFormDiv', 'block');
	myForm.name.focus();
}

function changeDebugVisibility(element) {
	var node = $('debug');
	
	if (node.style.display == 'none') {
		element.innerHTML = labels['js_hide_debug'];
		node.style.display = 'block';
	} else {
		element.innerHTML = labels['js_show_debug'];
		node.style.display = 'none';
	}
}


function changeViewType(element) {
	Browser.changeDisplayMode();
}

