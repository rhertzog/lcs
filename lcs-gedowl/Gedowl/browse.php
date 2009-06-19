<?php
/**
 * browse.php -- Browse page
 * 
 * Author: Steve Bourgeois <owl@bozzit.com> 
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2004 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 */
$dStartTime = time();

require_once(dirname(__FILE__) . "/config/owl.php");


require_once($default->owl_fs_root . "/lib/disp.lib.php");
require_once($default->owl_fs_root . "/lib/owl.lib.php");
require_once($default->owl_fs_root . "/lib/readhd.php");
require_once($default->owl_fs_root . "/lib/security.lib.php");

//global $cCommonDBConnection;
//$cCommonDBConnection = new Owl_DB;
//$cCommonDBConnection->connect();

if (!isset($expand) or !is_numeric($expand)) 
{
   $expand = $default->expand;
}

include_once($default->owl_fs_root . "/lib/header.inc");
include_once($default->owl_fs_root . "/lib/userheader.inc");

if (empty($parent) || !is_numeric($parent)) 
{
   $iHomeDir = $default->HomeDir;
   $iFirstDir = $default->FirstDir;
                                                                                                                                                                             
   if (  $iHomeDir <>  $iFirstDir)
   {
      $sql->query("SELECT * from $default->owl_folders_table where id = '$iFirstDir'");
      $numrows = $sql->num_rows($sql);
      if ($numrows == "1")
      {
         $parent = $iFirstDir;
      }
      else
      {
         $parent = $iHomeDir;
      }
   }
   else
   {
      $parent= $iHomeDir;
   }

   if(isset($fileid))
   {
      $parent =  owlfileparent($fileid);
   }
}
else
{
   // Check to see if the user tried to go outside his home directory
   if ($parent != $default->HomeDir )
   {
      $bIsWithinHomeDir = false;
      fCheckWithinHomeDir ( $parent );
      if (!$bIsWithinHomeDir)
      {
        printError($owl_lang->err_unauthorized);
      }
   } 

}

if (empty($curview) || !is_numeric($curview))
{
   $curview = 0;
}

$CheckPass = new Owl_DB;
$CheckPass->query("select password from " . $default->owl_folders_table . " where id='$parent'");
$CheckPass->next_record();
$password = $CheckPass->f("password");


$bPasswordFailed = false;

if ($password == md5($docpassword))
{
  $bDownloadAllowed = true;
}
else
{
  if(!empty($docpassword))
  {
     $bPasswordFailed = true;
  }
  $bDownloadAllowed = false;
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

// Initialize Page count Variables

if (!isset($iCurrentPage))
{
   $iCurrentPage = 0;
}

if (!isset($next))
{
   $next = 0;
}

if (!isset($prev))
{
   $prev = 0;
}

if ($next == 1) 
{
      $iCurrentPage++;
      $nextfiles = $nextfiles + $default->records_per_page;
      $nextfolders = $nextfolders + $default->records_per_page;
}
if ($prev == 1)
{
      $iCurrentPage--;
      $nextfiles = $nextfiles - $default->records_per_page;
      if ($nextfiles < 0)
      {
         $nextfiles = 0;
      }
      $nextfolders = $nextfolders - $default->records_per_page;
      if ($nextfolders < 0)
      {
         $nextfolders = 0;
      }
}

// V4B RNG Start
   $urlArgs = array();
   $urlArgs['sess']      = $sess;
   $urlArgs['parent']    = $parent;
   $urlArgs['expand']    = $expand;
   $urlArgs['order']     = $order;
   $urlArgs['curview']     = $curview;
   $urlArgs[${sortorder}]  = $sort;
// V4B RNG End

if (check_auth($parent, "folder_view", $userid, false, false) != "1" and !$bDownloadAllowed)
{
   $sql->query("select password from " . $default->owl_folders_table . " where id='$parent'");
   $sql->next_record();

   $password = $sql->f("password");

   if (empty($password) or (!empty($password) and $bPasswordFailed))
   {
      printError($owl_lang->err_nofolderaccess);
   }
   else
   {
      include_once($default->owl_fs_root . "/lib/header.inc");
      include_once($default->owl_fs_root . "/lib/userheader.inc");
      if ($expand == 1)
      {
         print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_expand_width\"><tr><td align=\"center\" valign=\"top\" width=\"100%\">\n");
      }
      else
      {
         print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_collapse_width\"><tr><td align=\"center\" valign=\"top\" width=\"100%\">\n");
      }

      fPrintButtonSpace(12, 1);
      print("<table class=\"border2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");

      if ($default->show_prefs == 1 or $default->show_prefs == 3)
      {
         fPrintPrefs("infobar1", "top", true);
      }

      fPrintButtonSpace(12, 1);
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");

      print("<form action=\"browse.php\" method=\"post\">\n");
      print fGetHiddenFields ($urlArgs);
      fPrintFormTextLine($owl_lang->password , "docpassword", "", "", "", false, "password"); 
      print("<tr>\n");
      print("<td class=\"form1\">");
      fPrintButtonSpace(1, 1);
      print("</td>\n");
      print("<td class=\"form2\" width=\"100%\">");
      fPrintSubmitButton($owl_lang->btn_submit, $owl_lang->btn_submit, "submit");
      fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
      print("</td>\n");
      print("</tr>\n");
      print("</form>\n");
      print("</table>\n");
      fPrintButtonSpace(12, 1);
      
      if ($default->show_prefs == 2 or $default->show_prefs == 3)
      {
         fPrintPrefs("infobar2", "", true);
      }
      print("</td></tr></table>\n");
      include_once($default->owl_fs_root. "/lib/footer.inc");

   }
   exit;
}


// Tiian changes 2003-07-31
$sql_bro = new Owl_DB;

$sql_bro->query("SELECT id FROM $default->owl_folders_table WHERE id = '$parent' AND name = '$default->version_control_backup_dir_name'");
("SELECT id FROM $default->owl_folders_table WHERE id = '$parent' AND name = '$default->version_control_backup_dir_name'");
if ($sql_bro->num_rows() > 0)
{
    $is_backup_folder = true; 
}
else
{
    $is_backup_folder = false;
}

// **************************************
// Get File statistics for the status bar
// and for controling Pages
// **************************************

$lastlogin =  fGetLastLogin();


//if ($default->show_file_stats == 1)
//{
fGetStatusBarCount();
//}

$iFileCount = $iFolderCount + $iFileCount;
   
$whereclause = "";
$DBFolderCount = 0;

$sql = new Owl_DB;

if ($default->hide_backup == 1 and !fIsAdmin())
{
   $sql->query("SELECT * from $default->owl_folders_table where parent = '$parent' AND name = '$default->version_control_backup_dir_name'");
   if ($sql->num_rows() > 0)
   {
      $DBFolderCount++; //count number of filez in db 2 use with array
      $DBFolders[$DBFolderCount] = $default->version_control_backup_dir_name; //create list if files in
   } 

   $whereclause = " AND name <> '$default->version_control_backup_dir_name'";
} 
if (isset($page))
{
   $iCurrentPage = $page;
   $nextfolders = ($default->records_per_page * $page);
   $nextfiles = 0;
}

if ($default->records_per_page > 0)
{
   $sLimit = "LIMIT $nextfolders,$default->records_per_page";
   $sql->query("SELECT * from $default->owl_folders_table where parent = '$parent' $whereclause order by name $sLimit");
   $iNumberFoldersDisplayed = $sql->num_rows();
   $iSaveNextfolders = $nextfolders;
   $iSaveNextfiles = $nextfiles  - $iNumberFoldersDisplayed;
   $iSaveDisplayFiles = $bDisplayFiles;
   $iSaveFileCount = $iFileCount;
   $iSaveCurrentPage = $iCurrentPage;
   
   if ($iNumberFoldersDisplayed < $default->records_per_page)
   {
      $bDisplayFiles = true;
      if (isset($page))
      {
         $iNumberOfPages = (int) (($iFolderCount / $default->records_per_page));
         //$iNumberOfPages = ($iNumberOfPages == 0) ? 1 :((int) round($iNumberOfPages + 0.4999));
         $iNumberOfPages = ($iNumberOfPages == 0) ? 1 :((int) round($iNumberOfPages + 0.51111));
         
         $iPageLeft = $page - $iNumberOfPages;
         
         if ($iFolderCount == 0 );
         {
            if($iPageLeft < 0)
            {
              $iPageLeft = 0;
            } 
            else
            {
              $iPageLeft++;
            } 
         }

         $iCorrection = 0;

         if($iFolderCount <> 0)
         {
           $iCorrection = $iFolderCount % $default->records_per_page;
         }

         if ($nextfiles == 0 and $iNumberFoldersDisplayed > 0)
         {
            $nextfiles = 0;
         }
         else
         {
            $nextfiles = ($default->records_per_page * $iPageLeft) - $iNumberFoldersDisplayed - $iCorrection ;
         }

         if ($nextfiles < 0)
         {
            $nextfiles = $nextfiles + $default->records_per_page;
         }
      }
   }
   else
   {
      $bDisplayFiles = false;
   }

   if ($iNumFilesPerPage != $default->records_per_page)
   {
      $inextfiles = $nextfiles - $iNumberFoldersDisplayed;
   }
}


// *********************************
// Display the Header Tool Bar BEGIN
// *********************************

print("<center>");

if ($default->owl_version_control == 1 && ! $default->owl_use_fs)
{           
   if ($order == "major_minor_revision")
   {
      $order = "major_revision $sort, f1.minor_revision ";
      $forder = "major_revision $sort, minor_revision ";
   }
   else
   {
      $forder = $order;
   }

   $qGetFiles = "SELECT f1.id as file_id , f1.major_revision, f1.minor_revision, f1.major_revision+(f1.minor_revision/1000.0) AS mval FROM $default->owl_files_table f1, $default->owl_files_table f2 WHERE f1.name=f2.name AND f1.parent=f2.parent AND f1.parent='$parent' AND f1.approved ='1' GROUP BY f1.id, f1.major_revision, f1.minor_revision, f1.$order HAVING f1.major_revision+(f1.minor_revision/1000.0) = max(f2.major_revision+(f2.minor_revision/1000.0)) ORDER BY f1.$order $sort";

    $sql->query($qGetFiles);

    $FileQuery = ("SELECT * FROM $default->owl_files_table where '1' = '0' ");

    while ($sql->next_record())
    {
      $FileQuery .= " OR id = '" . $sql->f("file_id") ."'"; 
    }

    $FileQuery .= " ORDER BY $forder $sort";

   //$FileQuery = "select f1.*, f1.major_revision+(f1.minor_revision/1000.0) as mval from $default->owl_files_table f1, $default->owl_files_table f2 where f1.approved = '1' and f1.name=f2.name AND f1.parent=f2.parent AND f1.parent=$parent group by f1.id having f1.major_revision+(f1.minor_revision/1000.0) = max(f2.major_revision+(f2.minor_revision/1000.0)) order by f1.$order $sort";
   //$FileQuery = "select f1.*, f1.major_revision+(f1.minor_revision/1000.0) as mval from $default->owl_files_table f1, $default->owl_files_table f2 where f1.name=f2.name AND f1.parent=f2.parent AND f1.parent=$parent group by f1.id having f1.major_revision+(f1.minor_revision/1000.0) = max(f2.major_revision+(f2.minor_revision/1000.0)) order by f1.$order $sort";

   $MenuFileQuery = $FileQuery;
}           
else        
{
   if ($order == "major_minor_revision")
   {
      $order_clause = "major_revision $sort, minor_revision $sort";
   }
   else
   {
      $order_clause = "$order $sort";
   }

   $sLimit = "";

   if ($default->records_per_page > 0)
   {
      $iNumFilesPerPage = $default->records_per_page - $iNumberFoldersDisplayed;
      $sLimit = "LIMIT $nextfiles,$iNumFilesPerPage";
   }

   // Query TO retreive the Files in the current Folder
   $FileQuery = "select * from $default->owl_files_table where parent = '$parent' order by $order_clause $sLimit";
   $MenuFileQuery = "select * from $default->owl_files_table where parent = '$parent' and approved = '1' order by $order_clause $sLimit";
}

$CountLines = 0;
$sLimit = '';
if ($default->records_per_page > 0)
{
   $sLimit = "LIMIT $nextfolders,$default->records_per_page";
}


if ($order == "creatorid" or $order == "smodified")
{
   $FolderQuery = "SELECT * from $default->owl_folders_table where parent = '$parent' $whereclause order by $order $sortname $sLimit";
}
else
{
   $FolderQuery = "SELECT * from $default->owl_folders_table where parent = '$parent' $whereclause order by name $sortname $sLimit";
}



if(!$default->old_action_icons)
{
   $mid = new LayersMenu();
   $mid->setDirroot($default->owl_fs_root . "/scripts/phplayersmenu/");
   $mid->setImgwww($default->owl_root_url . '/scripts/phplayersmenu/menuimages/');

   if (substr(php_uname(), 0, 7) != "Windows")
   {
      $mid->setIcondir($default->owl_fs_root . "/graphics/$default->sButtonStyle/icon_action/");
   }
   else
   {
      $mid->setIcondir(ereg_replace("([A-Z]\:|[a-z]\:)", "", ereg_replace("[\\]", "/",$default->owl_fs_root)) . "/graphics/$default->sButtonStyle/icon_action/");
   }
   $mid->setIconwww($default->owl_graphics_url . "/$default->sButtonStyle/icon_action/");
   $mid->setIconsize(17, 20);
 
   fSetupFileActionMenus($MenuFileQuery);
   fSetupFolderActionMenus($FolderQuery);

   $mid->printHeader();

}


//FOR_FOLDERS

if ($expand == 1)
{
   print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_expand_width\"><tr><td align=\"center\" valign=\"top\" width=\"100%\">\n");
}
else
{
   print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_collapse_width\"><tr><td align=\"center\" valign=\"top\" width=\"100%\">\n");
}

                                                                                                                                                                                       
fPrintButtonSpace(4, 1);
print("<table class=\"border2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");

if ($default->show_prefs == 1 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar1", "top");
}

// *******************************
// Display the Header Tool Bar END
// *******************************

fPrintPanel($default->display_file_info_panel_wide);


if ($default->show_search == 1 or $default->show_search == 3 or (fIsAdmin() and $default->show_search == 0))
{
   fPrintSearch();
   fPrintSpacer();
}

fPrintVaroriteLink();

if (check_auth($parent, "folder_create", $userid, false, false) == 1 or  check_auth($parent, "folder_view", $userid, false, false) == 1  && !$is_backup_folder)
{
   //if ($sess != "0" || ($sess == "0" && $default->anon_ro == 0))
   //{
      if ($default->show_bulk > 0 or (fIsAdmin() and $default->show_bulk == 0 ))
      {     
         if (check_auth($parent, "folder_view", $userid, false, false) == 1)
         {
            fPrintBulkButtons();
         }
      }
      if ($default->show_action == 1 or $default->show_action == 3 or (fIsAdmin() and $default->show_action == 0))
      {
         //if (check_auth($parent, "folder_create", $userid, false, false) == 1)
         //{
            fPrintActionButtons();
         //}
      }
   //}
}

if ($default->show_folder_tools == 1 or $default->show_folder_tools == 3)
{
   fPrintFolderTools($nextfolders, $inextfiles, $bDisplayFiles, $iFileCount, $iCurrentPage);
}

fPrintNavBar($parent);

if ($curview == 0)
{
   require_once ($default->owl_fs_root . "/view_default.php");
}
else
{
   require_once ($default->owl_fs_root . "/view_thumb.php");
}


if ($default->show_folder_tools == 2 or $default->show_folder_tools == 3)
{
   fPrintFolderTools($iSaveNextfolders, $inextfiles, $iSaveDisplayFiles, $iSaveFileCount, $iSaveCurrentPage);
}
if (check_auth($parent, "folder_create", $userid, false, false) == 1 or  check_auth($parent, "folder_view", $userid, false, false) == 1  && !$is_backup_folder)
//if (check_auth($parent, "folder_modify", $userid, false, false) == 1 or  check_auth($parent, "folder_upload", $userid, false, false) == 1  && !$is_backup_folder)
{
   //if ($sess != "0" || ($sess == "0" && $default->anon_ro == 0))
   //{
      if ($default->show_action == 2 or $default->show_action == 3 )
      {
         //if (check_auth($parent, "folder_create", $userid, false, false) == 1)
         //{
            fPrintActionButtons(1);
         //}
      }
      if ($default->show_bulk > 0)
      {
         if (check_auth($parent, "folder_view", $userid, false, false) == 1)
         {
            fPrintBulkButtons(1);
         }
      }
      else
      {
         print("</form>");
      }
   //}
}
else
{
   print("</form>");
}

if ($default->show_search == 2 or $default->show_search == 3)
{
   fPrintSearch(1);
   fPrintSpacer();
   fPrintButtonSpace(12, 12);
}

fPrintButtonSpace(12, 1);

if ($default->show_prefs == 2 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar2");
}
print("</td></tr>");
print("</table>");

// *******************************
// If the refresh from hard drive
// feature is enabled
// *******************************
// 
if ($default->owl_use_fs)
{
   if ($default->owl_LookAtHD != "false")
   {
      //print_r($DBFiles);
      //exit;
      if ($RefreshPage == true)
      {
         CompareDBnHD('file', $default->owl_FileDir . "/" . get_dirpath($parent), $DBFiles, $parent, $default->owl_files_table);
      } 
      else
      {
         $RefreshPage = CompareDBnHD('file', $default->owl_FileDir . "/" . get_dirpath($parent), $DBFiles, $parent, $default->owl_files_table);
      } 
      if ($RefreshPage == true)
      {

print('<script type="text/javascript">');
print('window.location.reload(true);');
print('</script>');
      } 
   } 
} 

if(!$default->old_action_icons)
{
   $mid->printFooter();
}


if($default->debug == true)
{
   $diff = time()-$dStartTime;
   $minsDiff = floor($diff/60);
   $diff -= $minsDiff*60;
   $secsDiff = $diff;
   print("<div class=\"owlbar1\">($owl_lang->elapsed_time ".$minsDiff.'m '.$secsDiff.'s)'."</div>");
}
include_once($default->owl_fs_root. "/lib/footer.inc");
?>
