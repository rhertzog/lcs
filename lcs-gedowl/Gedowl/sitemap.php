<?php

/**
 * sitemap.php
 * 
 * Author: B0zz
 *
 * Copyright (c) 1999-2004 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 */

require_once(dirname(__FILE__)."/config/owl.php");
require_once($default->owl_fs_root ."/lib/disp.lib.php");
require_once($default->owl_fs_root ."/lib/owl.lib.php");
require_once($default->owl_fs_root ."/lib/security.lib.php");

if ($sess == "0" && $default->anon_ro > 0)
{
   printError($owl_lang->err_login);
}

if (empty($parent) || !is_numeric($parent))
{
   $parent = $default->HomeDir;
}

include($default->owl_fs_root ."/lib/header.inc");
include($default->owl_fs_root ."/lib/userheader.inc");

print("<center>\n");

if ($expand == 1)
{
   print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_expand_width\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");
}
else
{
   print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_collapse_width\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");
}
fPrintButtonSpace(12, 1);
print("<br />\n");

print("<table class=\"border2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");                                                                                                                                             
if ($default->show_prefs == 1 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar1", "top");
}
fPrintButtonSpace(12, 1);
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
fPrintSectionHeader("$owl_lang->alt_site_map");
print("</table>\n");
print("<br />\n");

?>

<div align="center">
<table cellspacing="0" border="1" cellpadding="4" bgcolor="white"><tr><td align="left">

<?php 
function fShowSiteMapTree($fid, $folder)
{
   global $owl_lang, $folderList, $fCount, $fDepth, $sess, $id, $default, $userid, $expand, $sort, $sortorder, $sortname, $order, $curview, $sFolderTreeList ;
   // If restricted view is in effect only show the folders you do have access to
   $showfolder = 1;
   if ($default->restrict_view == 1)
   {
      if (check_auth($fid, "folder_create", $userid) != 0 && $fid != 0)
      {
         $showfolder = 1;
      }
      else
      {
         if (check_auth($fid, "folder_view", $userid) == 1 && $fid != 0)
         {
            $showfolder = 1;
         }
         else
         {
            $showfolder = 0;
         }
      }
   }
   if ($showfolder == 1)
   {
      //if (check_auth($fid, "folder_modify", $userid) == 0 and check_auth($fid, "folder_upload", $userid) == 0)
      if (check_auth($fid, "folder_view", $userid) == 0)
      {
         $gray = 1; //       check for permissions
      }
      for ($c = 0 ;$c < ($fDepth+1) ; $c++)
      {
        $sFolderTreeList .= ".";
      }
      if ($gray)
      {
         $sFolderTreeList .= "|$folder||$owl_lang->title_return_folder $folder|||\n";
      }
      else
      {
            $sFolderTreeList .= "|$folder|$default->owl_root_url/browse.php?sess=$sess&amp;parent=$fid&amp;expand=$expand&amp;order=$order&amp;curview=$curview|$owl_lang->title_return_folder $folder|||0\n";
      }
   }
   for ($c = 0; $c < $fCount; $c++)
   {
      if ($folderList[$c][2] == $fid)
      {
         //$sFolderTreeList .= "<br />";
         $fDepth++;
         fShowSiteMapTree($folderList[$c][0] , $folderList[$c][1]);
         $fDepth--;
      }
   }
}


// Get list of folders sorted by name
$whereclause = "";

if ($default->hide_backup == 1 and !fIsAdmin())
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


//fShowSiteMap($default->HomeDir, fid_to_name($default->HomeDir));
fShowSiteMapTree($default->HomeDir, fid_to_name($default->HomeDir));
//print("<pre>$sFolderTreeList</pre>");
if ($default->old_action_icons)
{
   require_once ($default->owl_fs_root . "/scripts/phplayersmenu/lib/PHPLIB.php");
   require_once ($default->owl_fs_root . "/scripts/phplayersmenu/lib/layersmenu-common.inc.php");
   require_once ($default->owl_fs_root . "/scripts/phplayersmenu/lib/layersmenu.inc.php");
}

require_once ($default->owl_fs_root . "/scripts/phplayersmenu/lib/treemenu.inc.php");

$mid = new TreeMenu();
$mid->setDirroot($default->owl_fs_root . "/scripts/phplayersmenu/");
$mid->setImgwww($default->owl_root_url . '/scripts/phplayersmenu/menuimages/');
$mid->setIconwww($default->owl_root_url . '/scripts/phplayersmenu/menuicons/');

$mid->setMenuStructureString($sFolderTreeList);
$mid->setIconsize(16, 16);
$mid->parseStructureForMenu('treemenu1');
$mid->setSelectedItemByUrl('treemenu1', basename(__FILE__));
//$mid->setTreeLeafImage('folder_closed');
print $mid->newTreeMenu('treemenu1');


print("</td></tr></table>\n");
fPrintButtonSpace(12, 1);
                                                                                                                                             
print("</div>");
                                                                                                                                             
if ($default->show_prefs == 2 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar2");
}
print("</td></tr></table>\n");
include($default->owl_fs_root ."/lib/footer.inc");
?>
