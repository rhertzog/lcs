<?php
/**
 * showrecords.php -- Browse page
 * 
 *  Author: Steve Bourgeois owl@bozzit.com
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 */

require_once(dirname(__FILE__)."/config/owl.php");
require_once($default->owl_fs_root ."/lib/disp.lib.php");
require_once($default->owl_fs_root ."/lib/owl.lib.php");
require_once($default->owl_fs_root ."/lib/readhd.php");
require_once($default->owl_fs_root ."/lib/security.lib.php");

if ($default->anon_user == $userid)
{
   die("$owl_lang->err_unauthorized");
} 

if (!isset($parent) || $parent == "" || !is_numeric($parent))
{
   $parent = $default->HomeDir;
}

if (!isset($expand) or !is_numeric($expand))
{
   $expand = $default->expand;
}
if (check_auth($parent, "folder_view", $userid) != "1")
{
   printError($owl_lang->err_nofolderaccess);
   exit;
} 

// V4B RNG Start
$urlArgs = array();
$urlArgs['sess']      = $sess;
$urlArgs['expand']    = $expand;
$urlArgs['order']     = $order;
$urlArgs['sortorder'] = $sort;
$urlArgs['curview']     = $curview;
// V4B RNG End


$getlastlogin = new Owl_DB;
$getlastlogin->query("SELECT lastlogin FROM $default->owl_users_table where id = '" . $userid . "'");
$getlastlogin->next_record();
$lastlogin = $getlastlogin->f("lastlogin");

// **************************************
// SETTING UP QUERY FOR FILES
// **************************************

if ($order == "major_minor_revision")
{
   $order_clause = "major_revision $sort, minor_revision $sort";
}
else
{
   $order_clause = "$order $sort";
}
if ($type == "n")
{
   if (!empty($filter))
   {
      $dSince =  date("Y-m-d H:i:s", (mktime(0, 0, 0, date("m")  , date("d") - $filter, date("Y")))); 
   }
   else
   {
      $dSince =  $lastlogin;
   }

   $qFileQuery = "SELECT * FROM $default->owl_files_table where approved = '1' and created > '$dSince' order by $order_clause";
} elseif ($type == "u")
{
   if (!empty($filter))
   {
      $dSince =  date("Y-m-d H:i:s", (mktime(0, 0, 0, date("m")  , date("d") - $filter, date("Y")))); 
   }
   else
   {
      $dSince =  $lastlogin;
   }
   $qFileQuery = "SELECT * FROM $default->owl_files_table where approved = '1' and smodified > '$dSince' and created < '$dSince' order by $order_clause ";
} elseif ($type == "c")
{
   $qFileQuery = "SELECT * FROM $default->owl_files_table where checked_out = '$userid'  order by $order $sort";
} elseif ($type == "pa")
{
   $qFileQuery = "SELECT * FROM $default->owl_files_table where creatorid = '$userid' and approved = '0' order by $order $sort";
} elseif ($type == "wa")
{
   $qSqlQuery = "(id ='-1' ";
   $glue = "";
   $sql->query("SELECT file_id from $default->owl_peerreview_table where reviewer_id = '$userid' and status ='0'");
   while ($sql->next_record())
   {
     $qSqlQuery .= " OR id ='" . $sql->f('file_id') . "'";
   }
   $qSqlQuery .= ")";

   $qFileQuery = "SELECT * FROM $default->owl_files_table where  $qSqlQuery order by $order_clause ";
} elseif ($type == "t")
{
   $qSqlQuery = "(id ='-1' ";
   $glue = "";
   $sql->query("SELECT fid FROM $default->owl_monitored_file_table  where userid = '$userid'");
   while ($sql->next_record())
   {
     $qSqlQuery .= " OR id ='" . $sql->f('fid') . "'";
   }
   $qSqlQuery .= ")";

   $qFileQuery = "SELECT * FROM $default->owl_files_table where  $qSqlQuery order by $order_clause ";
} elseif ($type == "br")
{
      $groups = fGetGroups($userid);
      $glue = "";
      $qQuery = "SELECT distinct id, parent FROM $default->owl_files_table f, $default->owl_advanced_acl_table a where a.file_id = id and (a.user_id = '0' or a.user_id = '$userid'";
      
      foreach ($groups as $aGroups)
      {
        $qQuery .= " or a.group_id ='" .$aGroups["0"] . "'";
      }
      $qQuery .= ")";

      $qSqlQuery = "('1'='0' ";
 
      //print("DEBUG: QUERY: $qQuery <br />");
      $sql->query($qQuery);

      while ($sql->next_record())
      {
         
         $bIsInBrokenTree = false;
         fIsInBrokenTree($sql->f('parent'));
//print("<br />PARE: " . $sql->f('parent') );
         if ($bIsInBrokenTree === false)
         {
            continue;
         }

         $glue = " OR ";
         $qSqlQuery .= $glue . " id ='" . $sql->f('id') . "'";
      }
      $qSqlQuery .= ")";

      $qFileQuery = "SELECT * FROM $default->owl_files_table where $qSqlQuery order by $order_clause ";
      //print("DEBUG: QUERY: $qFileQuery");

} elseif ($type == "m")
{
   $qFileQuery = "SELECT * FROM $default->owl_files_table where creatorid = '$userid' order by $order_clause ";

} elseif ($type == "g")
{
   $sqlmemgroup = new Owl_DB;
   $sqlmemgroup->query("select * from $default->owl_users_grpmem_table where groupid is not null and userid = '" . $userid . "'");
   $sFilesGroupsWhereClause = "( groupid = '-1' OR groupid = '$usergroupid'";
                                                                                                                                                                                        
   while($sqlmemgroup->next_record())
   {
      $sFilesGroupsWhereClause .= " OR groupid = '" . $sqlmemgroup->f("groupid") . "'";
   }
   $sFilesGroupsWhereClause .= ")";

   $qFileQuery = "SELECT * from $default->owl_files_table where $sFilesGroupsWhereClause order by $order_clause ";

} 

// **************************************
// SETTING UP QUERY FOR FILES
// **************************************

   if ($type == "t")
   {   
      $qSqlQuery = "('1'='0' ";
      $sql->query("SELECT fid FROM $default->owl_monitored_folder_table  where userid = '$userid'");
      $glue = "";
      while ($sql->next_record())
      {
        $glue = " OR ";
        $qSqlQuery .= $glue . " id ='" . $sql->f('fid') . "'";
      }
      $qSqlQuery .= ")";
   }
   if ($type == "br")
   {  
      $qQuery = "SELECT distinct id FROM $default->owl_folders_table f, $default->owl_advanced_acl_table a where a.folder_id=id and a.folder_id <> '1' and (a.user_id = '0' or a.user_id = '$userid'";
      foreach ($groups as $aGroups)
      {
        $qQuery .= " or a.group_id ='" .$aGroups["0"] . "'";
      }
      $qQuery .= ")";

      //print("DEBUG: Q: $qQuery <br />");

      $qSqlQuery = "('1'='0' ";
      $glue = "";
 
      $sql->query($qQuery);


      while ($sql->next_record())
      {  
         $bIsInBrokenTree = false;
         fIsInBrokenTree($sql->f('id'));
         if ($bIsInBrokenTree === false)
         {
            continue;
         }
         $glue = " OR ";
         $qSqlQuery .= $glue . " id ='" . $sql->f('id') . "'";
      }


      $qSqlQuery .= ")";
    }

   $qFolderQuery = "SELECT * FROM $default->owl_folders_table where  $qSqlQuery";

   //print("DEBUG: Q: $qFolderQuery <br />");

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

   fSetupFileActionMenus($qFileQuery);
   if (!empty($qSqlQuery))
   {
      fSetupFolderActionMenus($qFolderQuery);
   }
   $mid->printHeader();
}

include_once($default->owl_fs_root ."/lib/header.inc");
include_once($default->owl_fs_root ."/lib/userheader.inc");

print("<center>\n");
if ($expand == 1)
{
   print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_expand_width\"><tr><td align=\"center\" valign=\"top\" width=\"100%\">\n");
}
else
{
   print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_collapse_width\"><tr><td align=\"center\" valign=\"top\" width=\"100%\">\n");
}
fPrintButtonSpace(1, 4);
print("<table class=\"border2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");

if ($default->show_prefs == 1 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar1", "top");
}

fPrintButtonSpace(12);

fGetStatusBarCount();
$iFileCount = $iFolderCount + $iFileCount;
fPrintPanel($default->display_file_info_panel_wide);

switch ($type)
{
  case "u":
     $sPageTitle = $owl_lang->title_view_updated;
     $urlArgs = array();
     $urlArgs['sess']      = $sess;
     $urlArgs['type']    = $type;
     $urlArgs['expand']    = $expand;
     $urlArgs['curview']     = $curview;
     print("<!-- BEGIN: Filter -->\n");
     print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
     print("<form action=\"showrecords.php\" method=\"post\">\n");
     print fGetHiddenFields ($urlArgs);
     fPrintFormTextLine($owl_lang->past_number, "filter", 10);
     print("<tr>\n<td colspan=\"2\" class=\"form2\" width=\"100%\">");
     fPrintSubmitButton($owl_lang->btn_submit, $owl_lang->alt_btn_submit, "submit", "submit");
     fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
     print("</td>\n</tr>\n");
     print("</table>");
     print("<!-- END: Filter -->\n");
     break;
  case "n":
     $sPageTitle = $owl_lang->title_view_new;
     $urlArgs = array();
     $urlArgs['sess']      = $sess;
     $urlArgs['type']    = $type;
     $urlArgs['expand']    = $expand;
     $urlArgs['curview']     = $curview;
     print("<!-- BEGIN: Filter -->\n");
     print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
     print("<form action=\"showrecords.php\" method=\"post\">\n");
     print fGetHiddenFields ($urlArgs);
     fPrintFormTextLine($owl_lang->past_number , "filter", 10);
     print("<tr>\n<td colspan=\"2\" class=\"form2\" width=\"100%\">");
     fPrintSubmitButton($owl_lang->btn_submit, $owl_lang->alt_btn_submit, "submit", "submit");
     fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
     print("</td>\n</tr>\n");
     print("</table>");
     print("<!-- END: Filter -->\n");
     break;
  case "g":
     $sPageTitle = $owl_lang->files_in_my_group_title;
     break;
  case "m":
     $sPageTitle = $owl_lang->my_files_title;
     break;
  case "t":
     $sPageTitle = $owl_lang->my_monitored_title;
     break;
  case "br":
     $sPageTitle = $owl_lang->my_special_access_title;
     break;
  case "pa":
     $sPageTitle = $owl_lang->peer_pending_title;
     break;
  case "wa":
     $sPageTitle = $owl_lang->peer_approval_title;
     break;
  default:
     $sPageTitle = "";
     break;
}

print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\">\n");
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
fPrintSectionHeader($sPageTitle);
print("</table>\n");
print("</td>\n</tr>\n</table>\n");
print("<br />");

if ($default->show_search == 1 or $default->show_search == 3 or (fIsAdmin() and $default->show_search == 0))
{
   fPrintSearch();
}
         

print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\">\n");
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr>");

   if (($default->expand_disp_status and $expand == 1) or ($default->collapse_disp_status and $expand == 0))
   {  
      print("<td class=\"title1\">&nbsp;</td>\n");
   }  

   if ($default->thumbnails == 1)
   {
      print("<td class=\"title1\">&nbsp;</td>\n");
   }
      
   if (($default->expand_disp_doc_num and $expand == 1) or ($default->collapse_disp_doc_num and $expand == 0))
   {  
      show_link("id", "sortid", $sortid, $order, $sess, $expand, $parent, $owl_lang->doc_number);
   }
   
   if (($default->expand_disp_doc_type and $expand == 1) or ($default->collapse_disp_doc_type and $expand == 0))
   {
      print("<td class=\"title1\">&nbsp;</td>\n");
   }
   if (($default->expand_disp_title and $expand == 1) or ($default->collapse_disp_title and $expand == 0))
   {
      show_link("name", "sortname", $sortname, $order, $sess, $expand, $parent, $owl_lang->title);
   }
   if ($default->owl_version_control == 1)
   {
      if (($default->expand_disp_version and $expand == 1) or ($default->collapse_disp_version and $expand == 0))
      {
         show_link("major_minor_revision", "sortver", $sortver, $order, $sess, $expand, $parent, $owl_lang->ver);
      }
   }
   if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
   {
      show_link("filename", "sortfilename", $sortfilename, $order, $sess, $expand, $parent, $owl_lang->file);
   }
   if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
   {
      show_link("f_size", "sortsize", $sortsize, $order, $sess, $expand, $parent, $owl_lang->size);
   }
   if (($default->expand_disp_posted and $expand == 1) or ($default->collapse_disp_posted and $expand == 0))
   {
      show_link("creatorid", "sortposted", $sortposted, $order, $sess, $expand, $parent, $owl_lang->postedby);
   }
   if (($default->expand_disp_modified and $expand == 1) or ($default->collapse_disp_modified and $expand == 0))
   {
      show_link("smodified", "sortmod", $sortmod, $order, $sess, $expand, $parent, $owl_lang->modified);
   }
   if($type == "wa" or $type == "pa") 
   {
      //if (($default->expand_disp_action and $expand == 1) or ($default->collapse_disp_action and $expand == 0))
      //{
         print("<td class=\"title1\">$owl_lang->actions</td>\n");
      //}
   }
   if ($default->owl_version_control == 1)
   {
      if (($default->expand_disp_held and $expand == 1) or ($default->collapse_disp_held and $expand == 0))
      {
         show_link("checked_out", "sortcheckedout", $sortcheckedout, $order, $sess, $expand, $parent, $owl_lang->held);
      }
   }
   if ( $default->document_peer_review == 1 and $type == "pa")
   {
      print("\t<td class=\"title1\" align=\"center\">$owl_lang->peer_satus</td>");
   }
   print("</tr>\n");

$sql = new Owl_DB;

if ($type == "t" or $type == "br")
{
   if ($glue == " OR ")
   {
      $sql->query("SELECT * FROM $default->owl_folders_table where  $qSqlQuery ");
      while ($sql->next_record())
      {
         // Looping out Folders
         $GetItems = new Owl_DB;
   
         $iFolderCount = 0;
         $iParent = $sql->f("parent");
         $GetItems->query("SELECT id from $default->owl_folders_table where parent = '" . $sql->f("id") . "'" . $whereclause);
 
         if ($default->restrict_view == 1)
         {
            while ($GetItems->next_record())
            {
               $bFileDownload = check_auth($GetItems->f("id"), "folder_view", $userid);
               if ($bFileDownload)
               {
                  $iFolderCount++;
               }
            }
         }
         else
         {
            $iFolderCount = $GetItems->num_rows();
         }
   
         $iFileCount = fCountFileType ($sql->f("id"), '0');
         $iUrlCount = fCountFileType ($sql->f("id"), '1');
         $iNoteCount = fCountFileType ($sql->f("id"), '2');
 
         $CountLines++;
         $PrintLines = $CountLines % 2;

         if ($PrintLines == 0)
         {
            $sTrClass = "hover1";
            $sLfList = "lfile1";
            $sTrClassHilite = "mouseover1";
            $sTrClassHiliteAlt = "mouseover3";
         }
         else
         {
            $sTrClass = "hover2";
            $sLfList = "lfile1";
            $sTrClassHilite = "mouseover2";
            $sTrClassHiliteAlt = "mouseover3";
         }
      
         print("\t\t\t\t<tr class=\"$sTrClassHilite\" onMouseOver=\"this.className='$sTrClassHiliteAlt'\" onMouseOut=\"this.className='$sTrClassHilite'\">");

      //*******************************************************************************************************

   if(($default->expand_disp_status and $expand == 1) or ($default->collapse_disp_status and $expand == 0))
   {
      print("<td class=\"$sTrClass\">&nbsp;<br /></td>");
   }

   if ($default->thumbnails == 1)
   {
      print("<td class=\"$sTrClass\">&nbsp;</td>\n");
   }

   if(($default->expand_disp_doc_num and $expand == 1) or ($default->collapse_disp_doc_num and $expand == 0))
   {
      print("<td class=\"$sTrClass\">&nbsp;<br /></td>");
   }
   if(($default->expand_disp_doc_type and $expand == 1) or ($default->collapse_disp_doc_type and $expand == 0))
   {
      print("<td class=\"$sTrClass\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/folder_closed.gif\" border=\"0\" alt=\"\"></img><br /></td>");
   }
   if(($default->expand_disp_title and $expand == 1) or ($default->collapse_disp_status and $expand == 0))
   {
      print("<td class=\"$sTrClass\">");
      $sPopupDescription = nl2br($sql->f("description"));
   
      $urlArgs2 = $urlArgs;
      $urlArgs2['parent'] = $sql->f("id");
      $url = fGetURL ('browse.php', $urlArgs2);
   
      print("\n<a class=\"$sLfList\" href=\"$url\" title=\"$owl_lang->title_browse_folder\">" . $sql->f("name") . "</a>");
   
      if(!$default->hide_folder_doc_count)
      {
         if ($iFolderCount > 0 or $iFileCount > 0 or $iUrlCount  > 0 or $iNoteCount > 0)
         {
            print("&nbsp;(");
         } 
         if ($iFolderCount > 0 )
         {
            print("<a class=\"cfolders1\">$iFolderCount</a>");
         }
         if ($iFileCount > 0 )
         {
            if ($iFolderCount > 0)
            {
               print(":");
            }
            print("<a class=\"cfiles1\">$iFileCount</a>");
         }
         if ($iUrlCount  > 0 )
         {
            if ($iFileCount > 0)
            {
               print(":");
            }
            print("<a class=\"curl1\">$iUrlCount</a>");
         }
         if ($iNoteCount > 0)
         {
            print(":<a class=\"cnotes1\">$iNoteCount</a></b>");
         }
         if ($iFolderCount > 0 or $iFileCount > 0 or $iUrlCount  > 0 or $iNoteCount > 0)
         {
            print(")");
         }
      }
   
      if ($sql->f("description"))
      {
         print("<br /><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/transparent.gif\" border=\"0\" alt=\"\"></img><font class=\"DESC\">" . str_replace("\n", "<br /><img src=$default->owl_graphics_url/$default->sButtonStyle/ui_misc/transparent.gif border=\"0\" alt=\"\"></img>", $sql->f("description")) . "</font>");
      }

      print("</td>\n");
   }

   //if ($default->records_per_page == 0)
   //{
      //$DBFolderCount++; //count number of filez in db 2 use with array
      //$DBFolders[$DBFolderCount] = $sql->f("name"); //create list if files in
   //}


   //if ($expand == 1)
   //{
      if ($default->owl_version_control == 1)
      {
         if (($default->expand_disp_version and $expand == 1) or ($default->collapse_disp_version and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\">&nbsp;</td>\n");
         }
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            if($default->old_action_icons)
            {
               print("\t\t\t\t<td class=\"$sTrClass\">&nbsp;</td>\n");
            }
            else
            {
               print("\t\t\t\t<td class=\"$sTrClass\">");
               $mid->printMenu('vermenuf' .$sql->f("id"));
               print("</td>\n");
            }
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\">&nbsp;</td>\n");
         }
         if (($default->expand_disp_posted and $expand == 1) or ($default->collapse_disp_posted and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"left\">" . flid_to_creator($sql->f("id")) . "</td>\n");
         }
         if (($default->expand_disp_modified and $expand == 1) or ($default->collapse_disp_modified and $expand == 0))
         {
            print("<td class=\"$sTrClass\">&nbsp;</td>\n");
         }
      } 
      else
      {
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\">&nbsp;</td>\n");
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\">&nbsp;</td>\n");
         }
         if (($default->expand_disp_posted and $expand == 1) or ($default->collapse_disp_posted and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"left\">" . flid_to_creator($sql->f("id")) . "</td>\n");
         }
         if (($default->expand_disp_modified and $expand == 1) or ($default->collapse_disp_modified and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\">&nbsp;</td>\n");
         }
      } 

      if (($default->expand_disp_action and $expand == 1) or ($default->collapse_disp_action and $expand == 0))
      {
         if($default->old_action_icons)
         {
            print("<td class=\"$sTrClass\" align=\"left\">");

            // *****************************************
            // There is not Log Icon for folders so put A space
            // *****************************************
      
            if ($default->owl_version_control == 1)
            {
               fPrintButtonSpace(1,21);
            } 
            else
            {
               fPrintButtonSpace(1,2);
            }
   
            // *****************************************
            // Display the Delete Icons For the Folders
            // *****************************************
       
            if (check_auth($sql->f("id"), "folder_delete", $userid) == 1)
            {
               $urlArgs2 = $urlArgs;
               $urlArgs2['id'] = $sql->f("id");
               $urlArgs2['action'] = 'folder_delete';
               $url = fGetURL ('dbmodify.php', $urlArgs2);
      
               print("<a href=\"$url\" onclick=\"return confirm('$owl_lang->reallydelete " . htmlspecialchars($sql->f("name"), ENT_QUOTES) . "?');\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/trash.gif\" alt=\"$owl_lang->alt_del_folder\" title=\"$owl_lang->alt_del_folder\" border=\"0\"></img></a>");
               fPrintButtonSpace(1,4);
            }
            else
            {
                fPrintButtonSpace(1,21);
            }
   
            // *****************************************
            // Display the Property Icons For the Folders
            // *****************************************
       
            if (check_auth($sql->f("id"), "folder_property", $userid) == 1)
            {
               $urlArgs2 = $urlArgs;
               $urlArgs2['id'] = $sql->f("id");
               $urlArgs2['action'] = 'folder_modify';
               $url = fGetURL ('modify.php', $urlArgs2);
      
               print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/edit.gif\" border=\"0\" alt=\"$owl_lang->alt_mod_folder\" title=\"$owl_lang->alt_mod_folder\"></img></a>");
               fPrintButtonSpace(1,25);
            }
            else
            {
                fPrintButtonSpace(1,42);
            }
      
            // *****************************************
            // Display the move Icons For the Folders
            // *****************************************
    
            if (check_auth($sql->f("id"), "folder_modify", $userid) == 1)
            {
                $urlArgs2 = $urlArgs;
                $urlArgs2['id'] = $sql->f("id");
                $urlArgs2['action'] = 'cp_folder';
                $urlArgs2['parent'] = $parent;
                $url = fGetURL ('move.php', $urlArgs2);
      
                print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/copy.gif\" border=\"0\" alt=\"$owl_lang->alt_copy_folder\" title=\"$owl_lang->alt_copy_folder\"></img></a>");
      
                fPrintButtonSpace(1,4);
   
                $urlArgs2 = $urlArgs;
                $urlArgs2['id'] = $sql->f("id");
                $urlArgs2['action'] = 'folder';
                $urlArgs2['parent'] = $parent;
                $url = fGetURL ('move.php', $urlArgs2);
   
                print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/move.gif\" border=\"0\" alt=\"$owl_lang->alt_move_folder\" title=\"$owl_lang->alt_move_folder\"></img></a>");
   
                fPrintButtonSpace(1,88);
            } 
            else
            {
                // In some cases that needs to be 129 why?
                fPrintButtonSpace(1,106);
            }
      
   
            if (check_auth($sql->f("id"), "folder_view", $userid) == 1)
            {
               $folder_id = $sql->f("id");
               $checksql = new Owl_DB;
               $checksql->query("select * from $default->owl_monitored_folder_table where fid = '$folder_id' and userid = '$userid'");
               $checknumrows = $checksql->num_rows($checksql);
      
               $checksql->query("SELECT * from $default->owl_users_table where id = '$userid'");
               $checksql->next_record();
               if ($default->owl_version_control == 1)
               {
                  fPrintButtonSpace(1,21);
               } 
               if (trim($checksql->f("email")) != "")
               {
                  if ($checknumrows == 0)
                  {
                     $urlArgs2 = $urlArgs;
                     $urlArgs2['id'] = $folder_id;
                     $urlArgs2['parent'] = $parent;
                     $urlArgs2['action'] = 'folder_monitor';
                     $url = fGetURL ('dbmodify.php', $urlArgs2);
      
                     print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/monitor.gif\" border=\"0\" alt=\"$owl_lang->alt_monitor\" title=\"$owl_lang->alt_monitor\"></img></a>");
                  } 
                  else
                  {
                     $urlArgs2 = $urlArgs;
                     $urlArgs2['id'] = $folder_id;
                     $urlArgs2['parent'] = $parent;
                     $urlArgs2['action'] = 'folder_monitor';
                     $url = fGetURL ('dbmodify.php', $urlArgs2);
      
                     print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/monitored.gif\" border=\"0\" alt=\"$owl_lang->alt_monitored\" title=\"$owl_lang->alt_monitored\"></img></a>");
                  } 
                  fPrintButtonSpace(1,43);
               } 
               else
               {
                  fPrintButtonSpace(1,42);
               }
            } 
   
            if (check_auth($sql->f("id"), "folder_view", $userid) == 1)
            {
               $urlArgs2 = array();
               $urlArgs2['sess']   = $sess;
               $urlArgs2['id']     = $sql->f("id");
               $urlArgs2['parent'] = $sql->f("parent");
               $urlArgs2['action'] = 'folder';
               $urlArgs2['binary'] = 1;
               $urlArgs2['expand']    = $expand;
               $urlArgs2['order']     = $order;
               $urlArgs2['sortorder'] = $sort;
               $url = fGetURL ('download.php', $urlArgs2);
               //fPrintButtonSpace(20);
               print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/zip.gif\" border=\"0\" alt=\"$owl_lang->alt_get_file\" title=\"$owl_lang->alt_get_file\"></img></a>");
               fPrintButtonSpace(1,4);
            } 
   
            print("</td>\n");
         }
   
         if ($default->owl_version_control == 1)
         {
            if (($default->expand_disp_held and $expand == 1) or ($default->collapse_disp_held and $expand == 0))
            {
               print ("<td class=\"$sTrClass\">&nbsp;</td>\n");
            }
         }
   
   
         print("</tr>\n");
         //*******************************************************************************************************
         }
      }
   }
}  
         


// 
// BEGIN Print Files
// 

// Looping out files from DB!

$sql->query($qFileQuery);

while ($sql->next_record())
{
   $iRealFileID = fGetPhysicalFileId($sql->f('id'));
   $bPrintNew = false;
   $bPrintUpdated = false;
   // Tiian change 2003-07-31
   $sDirectoryPath = get_dirpath($sql->f("parent"));
   $pos = strpos($sDirectoryPath, "$default->version_control_backup_dir_name");
   if (is_integer($pos) && $pos)
   {
      $is_backup_folder = true;
   }
   else
   {
      $is_backup_folder = false;
   }

   if ($type == "n")
   {
      $sDirectoryPath = get_dirpath($sql->f("parent"));
      $pos = strpos($sDirectoryPath, "$default->version_control_backup_dir_name");
      if (is_integer($pos) && $pos)
      {
         continue;
      } 
   } 

   if ($default->restrict_view == 1 and $type != "wa")
   {
      
      if (!check_auth($sql->f("id"), "file_download", $userid))
      {
         continue;
      }
   } 
   // 
   // Find New files
   // 
   if (check_auth($sql->f("id"), "file_download", $userid) == 1)
   {
      if ($sql->f("created") > $lastlogin)
      {
         $bPrintNew = true;
         $iNewFileCount++;
      } 
      if ($sql->f("smodified") > $lastlogin && $sql->f("created") < $lastlogin)
      {
         $bPrintUpdated = true;
         $iUpdatedFileCount++;
      } 
   } 
   else
   {
      if ($type <> "wa" and $type <> "br")
      {
         continue;
      }
   }

   $CountLines++;
   $PrintLines = $CountLines % 2;
 if ($PrintLines == 0)
   {
      $sTrClass = "hover1";
      $sLfList = "lfile1";
      $sTrClassHilite = "mouseover1";
      $sTrClassHiliteAlt = "mouseover3";
   }
   else
   {
      $sTrClass = "hover2";
      $sLfList = "lfile1";
      $sTrClassHilite = "mouseover2";
      $sTrClassHiliteAlt = "mouseover3";
   }

   print("\t\t\t\t<tr class=\"$sTrClassHilite\" onMouseOver=\"this.className='$sTrClassHiliteAlt'\" onMouseOut=\"this.className='$sTrClassHilite'\">");

   if(($default->expand_disp_status and $expand == 1) or ($default->collapse_disp_status and $expand == 0))
   {
      print("<td class=\"$sTrClass\" align=\"left\">");
      if ($bHasComments)
      {
         if ($bPrintNewComment)
         {
            print("<b class=\"hilite\">");
         }
         
         $urlArgs2 = $urlArgs;
         $urlArgs2['id']     = $sql->f("id");
         $urlArgs2['parent'] = $parent;
         $urlArgs2['action'] = 'file_comment';
         $url = fGetURL ('modify.php', $urlArgs2);
   
         print("<a class=\"$sLfList\" href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/comment.gif\" border=\"0\" alt=\"$owl_lang->alt_comments\" title=\"$owl_lang->alt_comments\"></img></a><font color=\"darkblue\">&nbsp;($iTotalComments)</font>");
         if ($bPrintNewComment)
         {
            print("</B>");
         }
      } 
      if ($default->anon_user <> $userid)
      {
         if ($bPrintNew)
         {
            print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_icons/new.gif\" border=\"0\" alt=\"$owl_lang->alt_new\"></img>");
         } 
         if ($bPrintUpdated)
         {
            print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_icons/updated.gif\" border=\"0\" alt=\"$owl_lang->alt_updated\"></img>");
         } 
         if ($bWasIndexed)
         {
            print("&nbsp;<a class=\"curl1\">*</a>");
         }
      } 
   
      print("<br /></td>");
   }
   if ($default->thumbnails == 1)
   {
      print("<td class=\"$sTrClass\">");
      $sThumbUrl = $default->thumbnails_url . "/" . $default->owl_current_db . "_" . $iRealFileID . "_small.png";
      $sThumbLoc = $default->thumbnails_location . "/" . $default->owl_current_db . "_" . $iRealFileID . "_small.png";
      if (file_exists($sThumbLoc))
      {
         print("<img src=\"$sThumbUrl\" border=\"1\" alt=\"$owl_lang->alt_thumb_small\" title=\"$owl_lang->alt_thumb_small\"></img>");
      }
      else
      {
         print("&nbsp;\n");
      }
      print("</td>\n");
   }

   if (($default->expand_disp_doc_num and $expand == 1) or ($default->collapse_disp_doc_num and $expand == 0))
   {
      $sZeroFilledId = str_pad($sql->f("id"),$default->doc_id_num_digits, "0", STR_PAD_LEFT);
      print("<td class=\"$sTrClass\" align=\"left\">". $default->doc_id_prefix . $sZeroFilledId ."</td>");
   }

   if (($default->expand_disp_doc_type and $expand == 1) or ($default->collapse_disp_doc_type and $expand == 0))
   {
      print("<td class=\"$sTrClass\" align=\"left\">");
      $choped = split("\.", $sql->f("filename"));
      $pos = count($choped);
      if ( $pos > 1 )
      {
         $ext = strtolower($choped[$pos-1]);
         if ($iRealFileID == $sql->f('id'))
         {
            $sDispIcon = $ext;
         }
         else
         {
            $sDispIcon = $ext . "_lnk";
         }
      }
      else
      {
         $sDispIcon = "NoExtension";
      }
   
      if (($ext == "gz") && ($pos > 2))
      {
         $exttar = strtolower($choped[$pos-2]);
         if (strtolower($choped[$pos-2]) == "tar")
            $ext = "tar.gz";
      } 
   
      if ($sql->f("url") == "1")
      {
         print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/url.gif\" border=\"0\" alt=\"\"></img>");
      }
      else
      {
         if (!file_exists("$default->owl_fs_root/graphics/$default->sButtonStyle/icon_filetype/$sDispIcon.gif"))
         {
            $sDispIcon = "file";
         }
         print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/$sDispIcon.gif\" border=\"0\" alt=\"\"></img>");
      } 

      print("<br /></td>\n"); 
   }

   if (($default->expand_disp_title and $expand == 1) or ($default->collapse_disp_title and $expand == 0))
   {
      print("<td class=\"$sTrClass\" align=\"left\">");
      $sPopupDescription = fCleanDomTTContent($sql->f("description"));

      if ($sPopupDescription == "") 
      {
         $sPopupDescription = $owl_lang->no_description;
      }

      $urlArgs2 = $urlArgs;
      $urlArgs2['sess']   = $sess;
      $urlArgs2['id']     = $sql->f("id");
      //$urlArgs2['parent'] = $parent;
      $urlArgs2['parent'] = $sql->f("parent");
      $urlArgs2['action'] = 'file_details';
      $url = fGetURL ('view.php', $urlArgs2);
   
      print("\n<a class=\"$sLfList\" href=\"$url\" onmouseover=" . '"' . "return makeTrue(domTT_activate(this, event, 'caption', '" . $owl_lang->description . "', 'content', '" . $sPopupDescription . "', 'lifetime', 3000, 'fade', 'both', 'delay', 10, 'statusText', ' ', 'trail', true));" . '"');

      print(">\n");
      print("\n");
   
      print $sql->f("name") . "</a>";
      print("</td>\n");
   }

   if ($default->owl_version_control == 1)
   {
      if (($default->expand_disp_version and $expand == 1) or ($default->collapse_disp_version and $expand == 0))
      {
         print("\n<td class=\"$sTrClass\" align=\"left\">" . $sql->f("major_revision") . "." . $sql->f("minor_revision") . "</td>");
      }
   } 

   if ($sql->f("url") == "1")
   {
      if ($bFileDownload == 1)
      {
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            print("\n<td class=\"$sTrClass\" align=\"left\"><a class=\"$sLfList\" href=\"" . $sql->f("filename") . "\" target=\"new\" title=\"$owl_lang->title_browse_site : " . $sql->f("filename") . "\">" . $sql->f("filename") . " </a></td>\n");
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"right\">&nbsp;</td>\n");
         }
      } 
      else
      {
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            print("\n<td class=\"$sTrClass\" align=\"left\">" . $sql->f("filename") . "</td>\n");
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"right\">&nbsp;</td>\n");
         }
      } 
   }
   else
   {
      if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
      {
         print("\n<td class=\"$sTrClass\" align=\"left\">");
         if (check_auth($sql->f("id"), "file_download", $userid))
         {
            if($default->old_action_icons)
            {
               $urlArgs2 = $urlArgs;
               $urlArgs2['id']     = $sql->f("id");
               $urlArgs2['parent'] = $sql->f("parent");
               $url = fGetURL ('download.php', $urlArgs2);
               print("\n<a class=\"$sLfList\" href=\"$url\" title=\"$owl_lang->title_download_view\">" . $sql->f("filename") . "</a>");
            }
            else
            {
               $mid->printMenu('vermenu' .$sql->f("id"));
            }
         }
         else
         {
            print($sql->f("filename"));
         }
         print("</td>\n");
      }
      if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
      {
         print("<td class=\"$sTrClass\" align=\"right\">" . gen_filesize($sql->f("f_size")) . "</td>");
      }
   }

   //if ($expand == 1)
   //{
      if (($default->expand_disp_posted and $expand == 1) or ($default->collapse_disp_posted and $expand == 0))
      {
         print("\t\t\t\t<td class=\"$sTrClass\" align=\"left\"><a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("creatorid") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=" . $sql->f("parent") . "&amp;order=$order&amp;sortname=$sortname\">" . fid_to_creator($sql->f("id")) . "</a></td>\n");
      }
      if (($default->expand_disp_modified and $expand == 1) or ($default->collapse_disp_modified and $expand == 0))
      {
         print("<td class=\"$sTrClass\" align=\"left\">" . date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset) . "</td>\n");
      }

      //if (($default->expand_disp_action and $expand == 1) or ($default->collapse_disp_action and $expand == 0))
      //{
         if ($type == "wa")
         {
            print("\t\t\t\t<td class=\"$sTrClass\" align=\"left\">");
            $urlArgs2 = $urlArgs;
            $urlArgs2['binary'] = 1;
            $urlArgs2['id'] = $sql->f("id");
            $urlArgs2['parent'] = $parent;

            $sUrl = fGetURL ('download.php', $urlArgs2);
            print("<a href=\"$sUrl\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/bin.gif\" border=\"0\" alt=\"$owl_lang->alt_get_file\" title=\"$owl_lang->alt_get_file\"></img></a>");

            $urlArgs2 = $urlArgs;
            $urlArgs2['id'] = $sql->f("id");
            $urlArgs2['action'] = "approvedoc";
            $urlArgs2['parent'] = $parent;
            $sUrl = fGetURL ('dbmodify.php', $urlArgs2);
            print("&nbsp;<a href=\"$sUrl\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/approve.gif\" border=\"0\" alt=\"$owl_lang->alt_approve_file\" title=\"$owl_lang->alt_approve_file\"></img></a>");

            $urlArgs2 = $urlArgs;
            $urlArgs2['id'] = $sql->f("id");
            $urlArgs2['action'] = "rejectdoc";
            $urlArgs2['parent'] = $parent;
            $sUrl = fGetURL ('peerreview.php', $urlArgs2);

            print("&nbsp;<a href=\"$sUrl\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/reject.gif\" border=\"0\" alt=\"$owl_lang->alt_reject_file\" title=\"$owl_lang->alt_reject_file\"></img></a>");

            print("</td>\n");
         }
         elseif ($type == "pa")
         {
            $qGetDocReviewer = new Owl_DB;
            $qGetDocReviewer->query("SELECT * from $default->owl_peerreview_table where file_id = '" . $sql->f("id") . "' and status <> '1'");
            $iReviewersLeft = $qGetDocReviewer->num_rows();
            
            print("\t\t\t\t<td class=\"$sTrClass\" align=\"left\">");

            $urlArgs2 = $urlArgs;
            $urlArgs2['action'] = 'reminder';
            $urlArgs2['id'] = $sql->f("id");
            $urlArgs2['type'] = $type;
            $urlArgs2['parent'] = $sql->f("parent");
            $sUrl = fGetURL ('peerreview.php', $urlArgs2);
            print("<a href=\"$sUrl\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/email.gif\" border=\"0\"  title=\"$owl_lang->alt_email_reminder\" alt=\"$owl_lang->alt_email_reminder\"></img></a>");
            fPrintButtonSpace(1, 4);

            $urlArgs2 = $urlArgs;
            $urlArgs2['action'] = 'file_delete';
            $urlArgs2['id']     = $sql->f("id");
            $urlArgs2['type'] = $type;
            $urlArgs2['parent'] = $sql->f("parent");
            $sUrl = fGetURL ('dbmodify.php', $urlArgs2);

            print("<a href=\"$sUrl\" onclick=\"return confirm('$owl_lang->reallydelete ".$filename."?');\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/trash.gif\" alt=\"$owl_lang->alt_del_file\" title=\"$owl_lang->alt_del_file\" border=\"0\"></img></a>");
            fPrintButtonSpace(1, 4);

            $urlArgs2 = $urlArgs;
            $urlArgs2['action'] = 'file_update';
            $urlArgs2['id'] = $sql->f("id");
            $urlArgs2['parent'] = $sql->f("parent");
            $sUrl = fGetURL ('modify.php', $urlArgs2);

            print("<a href=\"$sUrl\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/update.gif\" border=\"0\" alt=\"$owl_lang->alt_upd_file\" title=\"$owl_lang->alt_upd_file\"></img></a>");
            fPrintButtonSpace(1, 4);
 
            if ($iReviewersLeft == 0)
            {
               $urlArgs2 = $urlArgs;
               $urlArgs2['action'] = 'publish';
               $urlArgs2['id'] = $sql->f("id");
               $urlArgs2['type'] = $type;
               $urlArgs2['parent'] = $sql->f("parent");
               $sUrl = fGetURL ('peerreview.php', $urlArgs2);
               print("&nbsp;<a href=\"$sUrl\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/publish.gif\" border=\"0\" alt=\"$owl_lang->alt_publish_file\" title=\"$owl_lang->alt_publish_file\"></img></a>");
            }
            print("</td>\n");
    //     }
         //else
         //{
            //if($default->old_action_icons)
            //{
               //print("\t\t\t\t<td class=\"$sTrClass\" align=\"left\">");
               //printFileIcons($sql->f("id"), $sql->f("filename"), $sql->f("checked_out"), $sql->f("url"), $default->owl_version_control, $ext, $parent, $is_backup_folder);
               //print("</td>\n");
            //}
         //}
      }
   //} 
   if ($default->owl_version_control == 1)
   {
      if (($default->expand_disp_held and $expand == 1) or ($default->collapse_disp_held and $expand == 0))
      {
         if (($holder = uid_to_name($sql->f("checked_out"))) == "Owl")
         {
            print("\t<td class=\"$sTrClass\" align=\"center\">-</td>");
         } 
         else
         {
            print("\t<td class=\"$sTrClass\" align=\"left\"><a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("checked_out") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=" . $sql->f("parent") . "&amp;order=$order&amp;sortname=$sortname\">$holder</a></td>");
         } 
      }
   } 

   if ( $default->document_peer_review == 1 and $type == "pa")
   {
      $qGetDocReviewer = new Owl_DB;
      $qGetDocReviewer->query("SELECT * from $default->owl_peerreview_table where file_id = '" . $sql->f("id") . "' ");

      print ("<td class=\"$sTrClass\">");
      print ("<table>\n");
      while($qGetDocReviewer->next_record())
      {
        print("<tr>\n");
        print("<td nowrap=\"nowrap\">" . uid_to_name($qGetDocReviewer->f("reviewer_id")) . ":&nbsp;</td>\n");

        switch ($qGetDocReviewer->f("status"))
        {
           case 1:
              $sStatus = "<div class=\"capproved\">$owl_lang->peer_satus_approved</div>";
              break;
           case 2:
              $sStatus = "<div class=\"crejected\">$owl_lang->peer_satus_rejected</div>";
              break;
           default:
              $sStatus = "<div class=\"cpending\">$owl_lang->peer_satus_pending</div>";
              break;
        }
        print("<td  nowrap=\"nowrap\" align=\"center\">$sStatus</td>\n");
        print("</tr>\n");
      }
      print ("</table>\n");
      print("</td>\n");
   }
   print("</tr>\n");

} 

   print("</table>");
   print("</td>\n</tr></table>\n");

   if ($default->show_search == 2 or $default->show_search == 3)
   {
      fPrintSpacer();
      fPrintSearch(1);
   }

   fPrintButtonSpace(12, 1);

   if ($default->show_prefs == 2 or $default->show_prefs == 3)
   {
      fPrintPrefs("infobar2");
   }
   print("</td></tr>");
   print("</table>");

if(!$default->old_action_icons)
{
   $mid->printFooter();
}
   include($default->owl_fs_root ."/lib/footer.inc");
?>
