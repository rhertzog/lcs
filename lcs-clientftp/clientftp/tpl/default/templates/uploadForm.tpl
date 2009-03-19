<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>{$labels.up_title}</title>
<link rel="stylesheet" type="text/css" href="css/default/stdtheme.css" />
<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript">
	
	var status = '{$result}';
	
	var labels = new Array();
	labels['up_js_error_provide_file'] = '{$labels.up_js_error_provide_file|escape:'quotes'}';
	labels['up_js_error_upload_failed'] = '{$labels.up_js_error_upload_failed|escape:'quotes'}';
	labels['up_js_confirm_file_exist'] = '{$labels.up_js_confirm_file_exist|escape:'quotes'}';
	
	
	{literal}
		function postIt() {
			var myForm = document.getElementById("fileUploadForm");
			var val  = myForm.file.value;
			
			if (!val || val.length==0) {
				alert(labels['up_js_error_provide_file']);
				return false;
			}
			
			var lastSlash = myForm.file.value.lastIndexOf("/") + 1;
			var lastBackSlash = myForm.file.value.lastIndexOf("\\") + 1;
			var filename = "";
			
			if (lastSlash > lastBackSlash) {
				filename = myForm.file.value.substr(lastSlash,myForm.file.value.length - lastSlash);
			} else {
				filename = myForm.file.value.substr(lastBackSlash,myForm.file.value.length - lastBackSlash);
			}
			
			// check if filename already exists
			if (parent.Browser.fileExists(filename)) {
				if (!confirm(labels['up_js_confirm_file_exist'])) {
					return false;
				}
			}
			
			// get info from parent about current relative path
			myForm.relativePath.value = parent.Browser.relativePath;
			parent.uploadForm(true);
			return true;
		}
		
		/**
		 * function will tell parent that file upload is finished. 
		 * And another one upload could be done.
		 */
		function readyToUpload() {
			if (parent.isUploadInProgress()) {
				parent.log(3, 'upload iframe status: [' + status + ']');
				if (status == 'failed') {
					alert(labels['up_js_error_upload_failed']);
				}
			}
			
			try { parent.uploadForm(false); } catch(e) {}
			try { 
				var content = parent.Browser.getAllowedFileTypes();
				setAllowedFileTypes(content);
			} catch(e) {}
		}
		
		function setAllowedFileTypes(content) {
			// get info about allowed file types
			var element = document.getElementById("allowedFileTypes");
			element.innerHTML = "*." + content.replace(/,/g," *.");
		}
		
		/**
		 * function will hide that form.
		 */
		function hideThis() {
			try { parent.changeUploadFormVisibility('none'); } catch(e) {}
		}
	{/literal}	
</script>

</head>
<body 	leftmargin="0" 
	rightmargin="0" 
	topmargin="0" 
	bottommargin="0" 
	marginheight="0" 
	marginwidth="0"
	style="background-color:#C4D1DF;"
	onLoad="readyToUpload();">

<form name="fileUploadForm" id="fileUploadForm" ENCTYPE="multipart/form-data" 
	ACTION="" METHOD="POST" onSubmit="return postIt()";>
<table width="100%">
	<tr>
		<td>{$labels.up_form_file}</td>
		<td><input NAME="file" TYPE="file" style="width:300px;" class="textfield"></td>
	</tr>
	<tr>
		<td>{$labels.up_form_allowed_types}</td>
		<td><span id="allowedFileTypes"></span></td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			<input type="hidden" name="relativePath">
			<input type=submit value="{$labels.up_form_button_upload}" class="button">
			<input type=button value="{$labels.up_form_button_cancel}" onClick="hideThis()" class="button">
		</td>
	</tr>
</table>
</form>
</body>
</html>
