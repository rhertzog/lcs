<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>{$labels.ofb_title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />

<link rel="stylesheet" type="text/css" href="css/default/stdtheme.css" />

<script src="js/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="js/scriptaculous/builder.js" type="text/javascript"></script>
<script src="js/niftycube.js" type="text/javascript"></script>
<script src="js/ImagePreloader.js" type="text/javascript" CHARSET="iso-8859-1"></script>
<script src="js/PopupMenu.js" type="text/javascript" CHARSET="iso-8859-1"></script>
<script src="js/Browser.js" type="text/javascript" CHARSET="iso-8859-1"></script>
<script src="js/ui-functions.js" type="text/javascript" CHARSET="iso-8859-1"></script>

<script type="text/javascript">
{literal}
	window.onload=function(){
		go();
		Nifty("div#directoryFormDiv,div#uploadFormDiv,div#renameFormDiv,div#fileFormDiv", "small");
		Nifty("div#optionsBorder,div#contentBorder","small");
		Nifty("div#optionsBorder h3","top");
	}
{/literal}
</script>


{php} echo XOAD_Utilities::header('lib/xoad'); {/php}

</head>
<body class="bodyBackground">
	<!-- popupmenu div -->
	<div id="popupMenuDiv" style="position:absolute;display:none;top:0px;left:0px;z-index:10000;border-color:#000033;border:solid 1px;" onmouseover="javascript:overpopupmenu=true;" onmouseout="javascript:overpopupmenu=false;"></div>
	
	
	<!-- main div -->
	<div id="contentMainFrame">
		
		<!-- *********** baner******************** -->
		
		<div id="bannerDiv">
			
			<!-- menu -->
			<div id="menuDiv">
				<a href="javascript:void(0)" onClick="showUploadForm()">{$labels.menu_link_upload}</a> |
				<a href="javascript:void(0)" onClick="Browser.reload()">{$labels.menu_link_reload}</a> |
				<a href="javascript:void(0)" onClick="showDirectoryFormDiv()">{$labels.menu_link_mkdir}</a> |
				<a href="javascript:void(0)" onClick="showFileFormDiv()">{$labels.menu_link_mkfile}</a> 
				<!--a href="index.php?action=Logout">{$labels.menu_link_logout}</a-->
			</div>
			
			<!-- logo -->
			<div id="logoDiv"><!--img src="pict/OFB-logo-120.png"/--> <span id="title">O.F.B.</span></div>
		</div>
		
		<!-- *********** real content *************** -->
		
		<!--div id="underBannerDiv"-->
			
			<!-- directory form -->
			<div id="directoryFormDiv" style="display:none">
				<form id="directoryForm" method="POST" action="" onSubmit="submitDirectoryForm(); return false;">
					<table width="100%">
						<TR><TD>{$labels.mkdir_title}</TD><TD align="left">
							<INPUT TYPE="TEXT" NAME="name" VALUE="" size="30" class="textfield"/>
							<inpUT type="button" value="{$labels.button_save}" onClick="submitDirectoryForm()" class="button"/>
							<inpUT type="button" value="{$labels.button_cancel}" onClick="changeElementVisibility('directoryFormDiv', 'none')" class="button"/>
						</TD></tr>
					</table>
				</form>
			</div>
			
			<!-- file form -->
			<div id="fileFormDiv" style="display:none">
				<form id="fileForm" method="POST" action="" onSubmit="submitFileForm(); return false;">
					<table width="100%">
						<TR><TD>{$labels.mkfile_title}</TD><TD align="left">
							<INPUT TYPE="TEXT" NAME="name" VALUE="" size="30" class="textfield"/>
							<SELECT NAME="extension" class="textfield"></SELECT>
							<inpUT type="button" value="{$labels.button_save}" onClick="submitFileForm()" class="button"/>
							<inpUT type="button" value="{$labels.button_cancel}" onClick="changeElementVisibility('fileFormDiv', 'none')" class="button"/>
						</TD></tr>
					</table>
				</form>
			</div>
			
			<!-- rename form -->
			<div id="renameFormDiv" style="display:none">
				<form id="renameForm" method="POST" action="" onSubmit="submitRenameForm(); return false;">
					<table width="100%">
						<TR><TD>{$labels.rename_title}</TD><TD align="left">
							<INPUT TYPE="TEXT" NAME="name" VALUE="" size="30" class="textfield"/>
							<INPUT TYPE="hidden" NAME="relativepath" VALUE=""/>
							<inpUT type="button" value="{$labels.button_save}" onClick="submitRenameForm()" class="button"/>
							<inpUT type="button" value="{$labels.button_cancel}" onClick="changeElementVisibility('renameFormDiv', 'none')" class="button"/>
						</TD></tr>
						<tr>
							<td>{$labels.rename_allowed_ext}</td>
							<td><span id="allowedFileTypes"></span></td>
						</tr>
					</table>
				</form>
			</div>
			
			<!-- upload iframe -->
			<div id="uploadFormDiv" style="display:none">
				<iframe width="100%" 
					height="0" 
					id="uploadIframe"
					name="uploadIframe" 
					src="index.php?action=UploadForm" 
					frameborder="0" 
					marginwidth="0" 
					marginheight="0" 
					noresize="" 
					scrolling="no"> </iframe>
				<div id="uploadStatusDiv" style="display:none">{$labels.upload_in_progress}</div>
			</div>
				
		<div id="underBannerDiv">			
			
			<!-- here are information - general info, details, thumbnails -->
			<div id="optionsBorder">
				<div id="optionsBoxInfo">
					<h3 class="title">{$labels.info_title}</h3>
					<div id="statisticsContentInnerDiv"></div>
				</div>
				
				<div id="optionsBoxDetails">
					<h3 class="title">{$labels.details_title}</h3>
					<div id="detailedContentInnerDiv">{$labels.details_default}</div>
				</div>

				<div id="optionsBoxEntriesTray">
					<form id="trayEntries" method="POST" action="" onSubmit="return false;">
					<h3 class="title">{$labels.tray_title}</h3>
					<div id="entriesTrayInnerDiv">{$labels.tray_no_files}</div>
					<div id="traySelectedEntryActions"></div>
					</form>
				</div>
				
				<div id="optionsBoxOptions">
					<h3 class="title">{$labels.options_title}</h3>
					<div id="optionsBoxInnerDiv">
						<ul>
							<!--li><a href="javascript:void(0)" onClick="changeDebugVisibility(this)">{$labels.options_show_debug}</a> </li-->
							<li><a id="viewModeTrigger" href="javascript:void(0)" onClick="changeViewType(this)">{$labels.options_show_icons}</a> </li>
						</ul>
					</div>
				</div>
				
			</div>
			
			<!-- this is div where all content goes, including informaiton about current location and content itself -->
			<div id="contentBorder">
				<!-- status -->
				<div id="location">{$labels.location_title} <span id="statusDiv"></span></div>
				
				<!-- content list-->
				<div id="contentListDiv">
					<table id="contentTable" cellspacing="0" border="0"></table>
				</div>
				
				<!-- content icons-->
				<div id="contentIconsDiv" style="display:none;"></div>
			</div>
		</div>
		<div style="clear:both;">
			<hr size="1" noshade />
                        <div style="float:right;"><a href="../doc/clientftp/html/" title="{$labels.help_page}">{$labels.help_page}&nbsp;</a></div>
			<!--div style="float:right;">{$labels.visit_proj} <a href="http://sourceforge.net/projects/filebrowser/">{$labels.home_page}</a></div-->
			{$labels.version} 0.1.7
		</div>
	</div> <!-- contentMainFrame -->
	
	<!-- Edit (modif jlcf) -->
	<div id="fileContentEditDiv" style="display:none;">
		<form id="fileContentEditForm" method="POST" action="">
                    <textarea name="fileContent" rows="23" cols="80"></textarea>
                    <div>
                        <input type="button" onClick="changeFileContentEditVisibility('none')" value="{$labels.button_cancel}"/>
                        <input type="button" onClick="saveFileContent()" value="{$labels.button_save}"/>
                    </div>
		</form>
	</div>
	
	<!-- debug -->
	<div id="debug" style="display:none;">
	
		<form id="debugForm" method="POST" action="">
			<textarea name="debug" rows="15" cols="100"></textarea>
		</form>
	</div>
	
	<script type="text/javascript">
		remoteBrowser = {php} echo XOAD_Client::register(new Browser()); {/php};
		
		labels = new Array();
		
		labels['js_show_icons'] = '{$labels.js_show_icons|escape:'quotes'}';
		labels['js_show_list'] = '{$labels.js_show_list|escape:'quotes'}';
		labels['js_error_mkfile'] = '{$labels.js_error_mkfile|escape:'quotes'}';
		labels['js_error_mkdir'] = '{$labels.js_error_mkdir|escape:'quotes'}';
		labels['js_root'] = '{$labels.js_root|escape:'quotes'}';
		labels['js_stat_dirs'] = '{$labels.js_stat_dirs|escape:'quotes'}';
		labels['js_stat_files'] = '{$labels.js_stat_files|escape:'quotes'}';
		labels['js_stat_total_size'] = '{$labels.js_stat_total_size|escape:'quotes'}';
		labels['js_icons_delete'] = '{$labels.js_icons_delete|escape:'quotes'}';
		labels['js_icons_rename'] = '{$labels.js_icons_rename|escape:'quotes'}';
		labels['js_icons_download'] = '{$labels.js_icons_download|escape:'quotes'}';
		labels['js_icons_edit'] = '{$labels.js_icons_edit|escape:'quotes'}';
		labels['js_list_title_download'] = '{$labels.js_list_title_download|escape:'quotes'}';
		labels['js_list_title_edit'] = '{$labels.js_list_title_edit|escape:'quotes'}';
		labels['js_list_title_delete'] = '{$labels.js_list_title_delete|escape:'quotes'}';
		labels['js_list_title_rename'] = '{$labels.js_list_title_rename|escape:'quotes'}';
		labels['js_error_general'] = '{$labels.js_error_general|escape:'quotes'}';
		labels['js_details_name'] = '{$labels.js_details_name|escape:'quotes'}';
		labels['js_details_size'] = '{$labels.js_details_size|escape:'quotes'}';
		labels['js_details_last_mod'] = '{$labels.js_details_last_mod|escape:'quotes'}';
		labels['js_details_permissions'] = '{$labels.js_details_permissions|escape:'quotes'}';
		
		labels['js_error_provide_dir'] = '{$labels.js_error_provide_dir|escape:'quotes'}';
		labels['js_error_provide_file'] = '{$labels.js_error_provide_file|escape:'quotes'}';
		labels['js_error_provide_name'] = '{$labels.js_error_provide_name|escape:'quotes'}';
		labels['js_error_upload_in_progress'] = '{$labels.js_error_upload_in_progress|escape:'quotes'}';
		labels['js_delete_dir_confirm'] = '{$labels.js_delete_dir_confirm|escape:'quotes'}';
		labels['js_delete_file_confirm'] = '{$labels.js_delete_file_confirm|escape:'quotes'}';
		labels['js_no_editable_ext'] = '{$labels.js_no_editable_ext|escape:'quotes'}';
		labels['js_hide_debug'] = '{$labels.js_hide_debug|escape:'quotes'}';
		labels['js_show_debug'] = '{$labels.js_show_debug|escape:'quotes'}';
		
		labels['tray_title'] = '{$labels.tray_title|escape:'quotes'}';
		labels['tray_no_files'] = '{$labels.tray_no_files|escape:'quotes'}';
		labels['tray_icons_remove'] = '{$labels.tray_icons_remove|escape:'quotes'}';
		labels['tray_icons_copy'] = '{$labels.tray_icons_copy|escape:'quotes'}';
		labels['tray_icons_move'] = '{$labels.tray_icons_move|escape:'quotes'}';
		labels['tray_error_destination_equals_source'] = '{$labels.tray_error_destination_equals_source|escape:'quotes'}';
		labels['tray_error_entry_exists'] = '{$labels.tray_error_entry_exists|escape:'quotes'}';
		labels['tray_error_no_files_selected'] = '{$labels.tray_error_no_files_selected|escape:'quotes'}';
		labels['tray_error_element_in_tray'] = '{$labels.tray_error_element_in_tray|escape:'quotes'}';
		labels['tray_error_no_entry'] = '{$labels.tray_error_no_entry|escape:'quotes'}';
		labels['tray_icons_add'] = '{$labels.tray_icons_add|escape:'quotes'}';
		
		{literal}
			function go(){
				Browser.log = log;
				Browser.mode = '1';
				Browser.remote = remoteBrowser;
				Browser.table = document.getElementById('contentTable');
				Browser.icons = document.getElementById('contentIconsDiv');
				Browser.list = document.getElementById('contentListDiv');
				Browser.status = document.getElementById('statusDiv');
				Browser.statistics = document.getElementById('statisticsContentInnerDiv');
				Browser.details = document.getElementById('detailedContentInnerDiv');
				Browser.viewModeTrigger = document.getElementById('viewModeTrigger');
				Browser.tray = document.getElementById('entriesTrayInnerDiv');
				Browser.selectedEntryActionsSpan = document.getElementById('traySelectedEntryActions');
				Browser.labels = labels;
				Browser.trayForm = document.getElementById('trayEntries');
				Browser.create();
				
				var uploadIframe = document.getElementById("uploadIframe");
				uploadIframe.src = 'index.php?action=UploadForm';
				window.frames.uploadIframe.setAllowedFileTypes(Browser.getAllowedFileTypes());
				
				var element = document.getElementById("allowedFileTypes");
				element.innerHTML = "*." + Browser.getAllowedFileTypes().replace(/,/g," *.");
			}
		{/literal}
		
	</script>
	
</body>
