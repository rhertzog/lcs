<?php

/**
 * tree.php
 * 
 * Copyright (c) 1999-2003 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 */

global $order, $sortorder, $sortname;

require_once(dirname(__FILE__)."/config/owl.php");
require_once($default->owl_fs_root ."/lib/disp.lib.php");
require_once($default->owl_fs_root ."/lib/owl.lib.php");
require_once($default->owl_fs_root ."/lib/temp.php");
require_once($default->owl_fs_root ."/lib/security.lib.php");
include_once($default->owl_fs_root ."/lib/header.inc");
?>
<script src="tree/TreeMenu.js" language="JavaScript" type="text/javascript"></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>


<?php
if ($sess == "0" && $default->anon_ro > 0)
{
   printError($owl_lang->err_login);
}

if (!isset($parent) || $parent == "") 
{
   $parent = "1";
}
if (!isset($nextfiles)) 
{
   $nextfiles = 0;
}
if (!isset($nextfolders)) 
{
   $nextfolders = 0;
}

if (!isset($bDisplayFiles))
{
 $bDisplayFiles = false;
}

if (!isset($expand)) 
{
   $expand = $default->expand;
}
//*******************************************************************************************************************************
  $pgEmailSubj = 'HTML_TreeMenu_Page';

  // Control dynamic style sheet
  $styleBodyIndent=true;
  $styleBodyBGcolor="#FFDBB7";
  
  include_once($default->owl_fs_root ."/tree/TreeMenuXL.php");
  
//*******************************************************************************************************************************

if ($myaction == "$owl_lang->cancel_button")
{
   displayBrowsePage($parent);
   exit();
} 
//$bDisplayFooterTools = true;
// Tiian changes 2003-07-31
$sql_bro = new Owl_DB;
$sql_bro->query("SELECT id FROM $default->owl_folders_table WHERE id = '$parent' AND name = '$default->version_control_backup_dir_name'");
if ($sql_bro->num_rows() > 0)
{
    $is_backup_folder = true; 
}
else
{
    $is_backup_folder = false;
}

$getlastlogin = new Owl_DB;
$getlastlogin->query("SELECT lastlogin FROM $default->owl_users_table where id = '" . $userid . "'");
$getlastlogin->next_record();
$lastlogin = $getlastlogin->f("lastlogin");
// **************************************
// Get File statistics for the status bar
// and for controling Pages
// **************************************
fGetStatusBarCount();

$iFileCount = $iFolderCount + $iFileCount;
   
$whereclause = "";
$DBFolderCount = 0;

$sql = new Owl_DB;
if ($default->hide_backup == 1 && ($usergroupid != "0" || $usergroupid != $default->file_admin_group))
{
   $sql->query("SELECT * from $default->owl_folders_table where parent = '$parent' AND name = '$default->version_control_backup_dir_name'");
   if ($sql->num_rows() > 0)
   {
      $DBFolderCount++; //count number of filez in db 2 use with array
      $DBFolders[$DBFolderCount] = '$default->version_control_backup_dir_name'; //create list if files in
   } 

   $whereclause = " AND name <> '$default->version_control_backup_dir_name'";
} 


function checkForNewFolder()
{
   global $_POST, $newFolder;
   if (!is_array($_POST)) return;
   while (list($key, $value) = each ($_POST))
   {
      if (substr($key, 0, 2) == "ID")
      {
         $newFolder = intval(substr($key, 2));
         break;
      } 
   } 
} 

function showFoldersIn($fid, $folder)
{
   global $folderList, $fCount, $fDepth, $excludeID, $action, $id, $default, $userid, $sess, $expand, $menu ; 
   // If restricted view is in effect only show the folders you do have access to
   $showfolder = 1;
 
   
   //  End
 
   if ($default->restrict_view == 1)
      if (check_auth($fid, "folder_view", $userid) != 0 && $fid != 0)
         $showfolder = 1;
      else
         $showfolder = 0;

      if ($showfolder == 1)
      {
	   // Identation visuelle
		  for ($c = 0 ;$c < ($fDepth-1) ; $c++) print "<img src='$default->owl_graphics_url/$default->sButtonStyle/ui_misc/16x16.gif' align=top>";
         if ($fDepth) print "<img src='$default-owl_graphics_urls/$default->sButtonStyle/ui_misc/link.gif' align=top>";
		$gray = 0; //	Work out when to gray out folders ...
        
		// if ($fid == $excludeID) $gray = 1; //	current parent for all moves
       //  if (($action == "folder") && ($fid == $id)) $gray = 1; //	subtree for folder moves
         
		 if (check_auth($fid, "folder_view", $userid) == 0)
         { $gray = 1; //	check for permissions       
		 } 
		 if ($gray)
         {
		 // Montre le nom du repertoire mais ne donne pas l'accés
		 	print "<img src='$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/folder_gray.gif' align=top>";
            print " <font color=\"silver\">$folder</font><br>\n";
         } 
         else
         {	print("<A CLASS='BAR' HREF='browse.php?sess=$sess&parent=$fid&expand=$expand' TITLE='$owl_lang->title_browse_folder'><IMG SRC='$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/folder_closed.gif' BORDER=0>$folder</A><br>\n");
         } 
      } 
     // if (($action == "folder") && ($fid == $id)) return; //	Don't show subtree of selected folder as target for folder move
      for ($c = 0; $c < $fCount; $c++)
      {
         if ($folderList[$c][2] == $fid)
         {
            $fDepth++;
            showFoldersIn($folderList[$c][0] , $folderList[$c][1]);
            $fDepth--;
       } 
    } 
 } 



function &menuIn($fid, $folder)
{
global $folderList, $fCount, $fDepth, $excludeID, $action, $id, $default, $userid, $sess, $expand ; 

//$nodeProperties = array("icon"=>"folder.gif");  
$nodeProperties = array("cssClass"=>"auto","icon"=>"folder.gif");
//$nodeProperties = array("cssClass"=>"auto");

if (check_auth($fid, "folder_view", $userid) != 0 && $fid != 0)
{$node = new HTML_TreeNodeXL($folder, "browse.php?sess=$sess&parent=$fid&expand=$expand", $nodeProperties);}
 else return false;  
for ($c = 0; $c < $fCount; $c++)
	{ 
	 	if ($folderList[$c][2] == $fid)
	  		{ 	$fDepth++;
	  			if ($fDepth < 5) 
				{$addnode=&menuIn($folderList[$c][0] , $folderList[$c][1]);}
				$fDepth--;
				$node->addItem($addnode);
       		} 
		}
	 return $node;
} 
 

   checkForNewFolder();
   if (isset($newFolder))
   // First time through. Generate screen for selecting target directory
   include($default->owl_fs_root ."/lib/header.inc");
   include($default->owl_fs_root ."/lib/userheader.inc");
   print("<table width='$default->table_expand_width' cellspacing='0' cellpadding='0' height='$default->table_header_height'>\n");

   ?>
<tr><td align='left'>
<?php
   fPrintPrefs("infobar1", "top");
   ?> 
<FONT SIZE=-1>

<?php 
fPrintButton("index.php?login=logout&amp;sess=$sess", "btn_logout");
?>
    </FONT></TD><TD ALIGN=RIGHT>
<?php 
fPrintButton("browse.php?sess=$sess&parent=$parent&expand=$expand&order=$order&$sortorder=$sortname", "btn_browse");
?>
        </TD></tr></TABLE>
<?php

   print("<CENTER>");
   
   //fPrintTreeStatusBar($nextfolders, $nextfiles, $bDisplayFiles, $iFileCount, $iCurrentPage);
   print("<br />\n");
   if ($default->status_bar_location == 0 || $default->status_bar_location == 2)
{
        fPrintTreeStatusBar($nextfolders, $nextfiles, $bDisplayFiles, $iFileCount, $iCurrentPage);
} 
   //print "<font size=2>$owl_lang->menu</font>";
   
?>
<form method="POST">

  <input type="hidden" name="parent" value="<?php print $parent; ?>">
  <input type="hidden" name="expand" value="<?php print $expand; ?>">
  <input type="hidden" name="order" value="<?php print $order; ?>">
  <input type="hidden" name="action" value="<?php print $action; ?>">
  <input type="hidden" name="myaction" value="<?php print $myaction; ?>">
  <input type="hidden" name="fname" value="<?php print $fname; ?>">
  <input type="hidden" name="id" value="<?php print $id; ?>">
  
   <?php 
   // Get list of folders sorted by name
   $whereclause = "";

   if ($default->hide_backup == 1 && ($usergroupid != "0" || $usergroupid != $default->file_admin_group))
   {
      $whereclause = " WHERE name <> '$default->version_control_backup_dir_name'";
   } 

   $sql->query("select id,name,parent from $default->owl_folders_table $whereclause order by name");

   $i = 0;
   while ($sql->next_record())
   {
      $folderList[$i][0] = $sql->f("id");
      $folderList[$i][1] = $sql->f("name");
      $folderList[$i][2] = $sql->f("parent");
      $i++;
   } 

   $fCount = count($folderList);
   $fDepth = 0;
   $excludeID = $parent; // current location should not be a offered as a target
   	
	//showFoldersIn(1, fid_to_name("1"));
	
	$menu  = new HTML_TreeMenuXL();
	$menu->addItem(menuIn(1, fid_to_name("1")));
   ?>
     
<table border="0"  cellpadding="1" cellspacing="1">
    <tr> 
    <td valign="top"><div align="left"> 
 <?php 
//***********************************************************************************************
 		 // Menu 3.1
        $example031 = &new HTML_TreeMenu_DHTMLXL($menu, array("images"=>"tree/TMimagesDB"));
        $example031->printMenu();
//*********************************************************************************************  
?>
      
<?php
   print("</p></td></tr></table>");
   print("</form>");
  if ($default->status_bar_location == 1 || $default->status_bar_location == 2)
   {
         fPrintTreeStatusBar($nextfolders, $nextfiles, $bDisplayFiles, $iFileCount, $iCurrentPage);
   } 
   
 
   include($default->owl_fs_root ."/lib/footer.inc");

?>
