<?php
/**
 * view_default.php -- Default view for Browse page
 * 
 * Author: Steve Bourgeois <owl@bozzit.com> 
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 */

   print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n<tr>\n<td align=\"left\" valign=\"top\">\n");
   print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");

   if ($default->show_bulk > 0 or (fIsAdmin() and $default->show_bulk == 0 ))
   {
      if ($sess != "0" || ( $sess == "0" && $default->anon_ro == 0 ))
      {
         print("<tr>\n<td class=\"title1\">");
         fPrintButtonSpace(1,4);
         print("<a href=\"#\" onclick=\"CheckAll();\">");
         print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_icons/tg_check.gif\" alt=\"$owl_lang->alt_toggle_check_box\" title=\"$owl_lang->alt_toggle_check_box\" border=\"0\"></img></a>");
   print("</td>\n");
      }
   }
   if (($default->expand_disp_status and $expand == 1) or ($default->collapse_disp_status and $expand == 0))
   {
      print("<td class=\"title1\">&nbsp;</td>\n"); 
   }

   if (($default->expand_disp_doc_num and $expand == 1) or ($default->collapse_disp_doc_num and $expand == 0))
   {
      show_link("id", "sortid", $sortid, $order, $sess, $expand, $parent, $owl_lang->doc_number);
   }
   if ($default->thumbnails == 1 and $default->thumbnails_small_width > 0)
   {
      print("<td class=\"title1\">&nbsp;</td>\n"); 
   }

   if (($default->expand_disp_doc_type and $expand == 1) or ($default->collapse_disp_doc_type and $expand == 0))
   {
      print("<td class=\"title1\">&nbsp;</td>\n"); 
   }
   if (($default->expand_disp_title and $expand == 1) or ($default->collapse_disp_title and $expand == 0))
   {
      show_link("name", "sortname", $sortname, $order, $sess, $expand, $parent, $owl_lang->title);
   }

   if (($default->expand_disp_doc_fields and $expand == 1) or ($default->collapse_disp_doc_fields and $expand == 0))
   {
       print("<td class=\"title1\">$owl_lang->doc_fields</td>\n");
   }

// STUARTS'S CHANGE 
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
   if (($default->expand_disp_updated and $expand == 1) or ($default->collapse_disp_updated and $expand == 0))
   {
      show_link("updatorid", "sortupdator", $sortupdator, $order, $sess, $expand, $parent, $owl_lang->updated_by);
   }
   if (($default->expand_disp_modified and $expand == 1) or ($default->collapse_disp_modified and $expand == 0))
   {
      show_link("smodified", "sortmod", $sortmod, $order, $sess, $expand, $parent, $owl_lang->modified);
   }
   if ((($default->expand_disp_action and $expand == 1) or ($default->collapse_disp_action and $expand == 0)) and $default->old_action_icons)
   {
      print("<td class=\"title1\">$owl_lang->actions</td>\n"); 
   }
   if ($default->owl_version_control == 1)
   {  
      if (($default->expand_disp_held and $expand == 1) or ($default->collapse_disp_held and $expand == 0))
      {
         show_link("checked_out", "sortcheckedout", $sortcheckedout, $order, $sess, $expand, $parent, $owl_lang->held);
      }
   } 
   print("</tr>\n");

// Looping out Folders

   if ($default->owl_LookAtHD != "false")
   {
      $sql->query("SELECT * from $default->owl_folders_table where parent = '$parent' $whereclause");
      while ($sql->next_record())
      {
         $DBFolderCount++; //count number of filez in db 2 use with array
         $DBFolders[$DBFolderCount] = $sql->f("name"); //create list if files in
      }
   }

$sql = new Owl_DB;
$sql->query($FolderQuery);

?>
<script language="JavaScript">

function mark_selected(id, checkbox, currentclass) {

identity=document.getElementById(id);
if (checkbox.checked)
{
   identity.className="mouseover3";
}
else
{
   identity.className=currentclass;
}

}


function alt_css_style(checkboxid, tr, newclassname) {


checkbox=document.getElementById(checkboxid);
if (!checkbox.checked)
{
   tr.className = newclassname;
}

}

</script>
<?php





// **********************
// BEGIN Print Folders
// **********************

while ($sql->next_record())
{
   if ($default->restrict_view == 1)
   {
      if (!check_auth($sql->f("id"), "folder_view", $userid, false, false))
      {
         if ($default->records_per_page == 0) 
         {
            $DBFolderCount++; //count number of filez in db 2 use with array
            $DBFolders[$DBFolderCount] = $sql->f("name"); //create list if files in
         }
         continue;
      } 
   } 
   // *******************************************
   // Find out how many items (Folders and Files)
   // *******************************************
   if(!$default->hide_folder_doc_count)
   {
      $GetItems = new Owl_DB;

      $iFolderCount = 0;
      $iParent = $sql->f("parent");
      $GetItems->query("SELECT id from $default->owl_folders_table where parent = '" . $sql->f("id") . "'" . $whereclause);
   
      if ($default->restrict_view == 1)
      {
         while ($GetItems->next_record())
         {
            $bFileDownload = check_auth($GetItems->f("id"), "folder_view", $userid, false, false);
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
 
   //print("\t\t\t\t<tr id=\"foldertr" . $sql->f("id") . "\" class=\"$sTrClassHilite\" onmouseover=\"alt_css_style('checkid" . $sql->f("id") . "', this, '$sTrClassHiliteAlt')\" this.className='$sTrClassHiliteAlt'\" onmouseout=\"this.className='$sTrClassHilite'\">\n");
   print("\t\t\t\t<tr id=\"foldertr" . $sql->f("id") . "\" class=\"$sTrClassHilite\" onmouseover=\"alt_css_style('fcheckid" . $sql->f("id") . "', this, '$sTrClassHiliteAlt')\"  onmouseout=\"alt_css_style('fcheckid" . $sql->f("id") . "', this, '$sTrClassHilite')\">\n");
   //print("\t\t\t\t<tr id=\"foldertr" . $sql->f("id") . "\" class=\"$sTrClassHilite\">\n");
   if ($default->show_bulk > 0 or (fIsAdmin() and $default->show_bulk == 0 ))
   {
      if ($sess != "0" || ($sess == "0" && $default->anon_ro == 0))
      {
         print("<td class=\"$sTrClass\">");
         print("<input id=\"fcheckid" .  $sql->f("id") . "\" type=\"checkbox\" name=\"fbatch[]\" value=\"" . $sql->f("id") . "\" onclick=\"mark_selected('foldertr" . $sql->f("id") . "', this, '$sTrClassHilite')\"></input>");
         print("</td>");
      } 
   } 

   if(($default->expand_disp_status and $expand == 1) or ($default->collapse_disp_status and $expand == 0))
   {
      print("<td class=\"$sTrClass\">");
      if ($default->show_bulk == 0 and ! fIsAdmin())
      {
         print("<input id=\"fcheckid" .  $sql->f("id") . "\" type=\"hidden\" name=\"fstyle_change\" value=\"" . $sql->f("id") . "\"></input>");
      }
      print ("&nbsp;<br /></td>");
   }
   if(($default->expand_disp_doc_num and $expand == 1) or ($default->collapse_disp_doc_num and $expand == 0))
   {
      print("<td class=\"$sTrClass\">&nbsp;<br /></td>");
   }

   if ($default->thumbnails == 1 and $default->thumbnails_small_width > 0)
   {
      print("<td class=\"$sTrClass\">&nbsp;</td>\n");
   }

   if(($default->expand_disp_doc_type and $expand == 1) or ($default->collapse_disp_doc_type and $expand == 0))
   {
      $urlArgs2 = $urlArgs;
      $urlArgs2['parent'] = $sql->f("id");
      $url = fGetURL ('browse.php', $urlArgs2);
      print("<td class=\"$sTrClass\">");
      print("<a class=\"$sLfList\" href=\"$url\" title=\"$owl_lang->title_browse_folder\">");
      print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/folder_closed.gif\" border=\"0\" alt=\"\"></img>");
      print("</a>");
      print("</td>");
   }
 

   if(($default->expand_disp_title and $expand == 1) or ($default->collapse_disp_title and $expand == 0))
   {
      print("<td class=\"$sTrClass\">");
      //$sPopupDescription = ereg_replace("\n", '<br />', trim($sql->f("description")));
      $sPopupDescription = nl2br(trim(htmlentities($sql->f("description"))));
   
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
            print("<a href=\"#\" class=\"cfolders1\" title=\"$owl_lang->folder_count_pre $iFolderCount $owl_lang->folder_count_folder\">$iFolderCount</a>");
         }
         if ($iFileCount > 0 )
         {
            if ($iFolderCount > 0)
            {
               print(":");
            }
            print("<a href=\"#\" class=\"cfiles1\" title=\"$owl_lang->folder_count_pre $iFileCount $owl_lang->folder_count_file\">$iFileCount</a>");
         }
         if ($iUrlCount  > 0 )
         {
            if ($iFileCount > 0)
            {
               print(":");
            }
            print("<a href=\"#\" class=\"curl1\" title=\"$owl_lang->folder_count_pre $iUrlCount $owl_lang->folder_count_url\">$iUrlCount</a>");
         }
         if ($iNoteCount > 0)
         {
            if ($iUrlCount  > 0)
            {
               print(":");
            }
            print("<a href=\"#\" class=\"cnotes1\" title=\"$owl_lang->folder_count_pre $iNoteCount $owl_lang->folder_count_note\">$iNoteCount</a>");
         }
         if ($iFolderCount > 0 or $iFileCount > 0 or $iUrlCount  > 0 or $iNoteCount > 0)
         {
            print(")");
         }
      }
   
      if (trim($sql->f("description")))
      {
         print("<br /><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/transparent.gif\" border=\"0\"><a class=\"desc\">" . str_replace("\n", "<br /><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/transparent.gif\" border=\"0\"></img>", $sql->f("description")) . "</a>");
      }

      print("</td>\n");
   }

   if ($default->records_per_page == 0)
   {
      $DBFolderCount++; //count number of filez in db 2 use with array
      $DBFolders[$DBFolderCount] = $sql->f("name"); //create list if files in
   }
   if (($default->expand_disp_doc_fields and $expand == 1) or ($default->collapse_disp_doc_fields and $expand == 0))
   {
       print("<td class=\"$sTrClass\" align=\"left\">&nbsp;</td>\n");
   }


      if ($default->owl_version_control == 1)
      {
         if (($default->expand_disp_version and $expand == 1) or ($default->collapse_disp_version and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\">&nbsp;</td>\n");
         }
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\">");
            if(!$default->old_action_icons)
            {
               $mid->printMenu('vermenuf' .$sql->f("id"));
            }
            else
            {
               print("&nbsp;");
            }
            print("</td>\n");
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            if ($default->hide_folder_size)
            {
               print("\t\t\t\t<td class=\"$sTrClass\">&nbsp;</td>\n");
            }
            else
            {
               $FolderSize = fGetFolderSize($sql->f("id"));
               print("\t\t\t\t<td class=\"$sTrClass\">" . gen_filesize($FolderSize) . "</td>\n");
            }
         }

         if( $default->show_user_info == 1)
         {
            $sLinkToUser = "<a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("creatorid") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=$parent&amp;order=$order&amp;sortname=$sortname\">" . flid_to_creator($sql->f("id")) . "</a>";
         }
         else
         {
            $sLinkToUser =  uid_to_name($sql->f("creatorid"));
         }

         if (($default->expand_disp_posted and $expand == 1) or ($default->collapse_disp_posted and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"left\">$sLinkToUser</td>\n");
         }
         if (($default->expand_disp_updated and $expand == 1) or ($default->collapse_disp_updated and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"left\">&nbsp;</td>\n");
         }
         if (($default->expand_disp_modified and $expand == 1) or ($default->collapse_disp_modified and $expand == 0))
         {
            if ($sql->f("smodified"))
            {
               print("<td class=\"$sTrClass\">" . date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset) . "</td>\n");
            }
            else
            {
               print("<td class=\"$sTrClass\">&nbsp;</td>\n");
            }
         }
      } 
      else
      {
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\">");
            if(!$default->old_action_icons)
            {
               $mid->printMenu('vermenuf' .$sql->f("id"));
            }
            else
            {
               print("&nbsp;");
            }
            print("</td>\n");
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\">&nbsp;</td>\n");
         }
         if (($default->expand_disp_posted and $expand == 1) or ($default->collapse_disp_posted and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"left\">" . uid_to_name($sql->f("creatorid")) . "</td>\n");
         }
         if (($default->expand_disp_updated and $expand == 1) or ($default->collapse_disp_updated and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"left\">" . uid_to_name($sql->f("updatorid")) . "</td>\n");
         }
         if (($default->expand_disp_modified and $expand == 1) or ($default->collapse_disp_modified and $expand == 0))
         {
            if ($sql->f("smodified"))
            {
               print("<td class=\"$sTrClass\">" . date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset) . "</td>\n");
            }
            else
            {
               print("<td class=\"$sTrClass\">&nbsp;</td>\n");
            }
         }
      } 

      if ((($default->expand_disp_action and $expand == 1) or ($default->collapse_disp_action and $expand == 0)) and $default->old_action_icons)
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
    
         if (check_auth($sql->f("id"), "folder_delete", $userid, false, false) == 1)
         {
            $urlArgs2 = $urlArgs;
            $urlArgs2['id'] = $sql->f("id");
            $urlArgs2['action'] = 'folder_delete';
            $url = fGetURL ('dbmodify.php', $urlArgs2);
   
            print("<a href=\"$url\" onclick=\"return confirm('$owl_lang->reallydelete " . htmlspecialchars($sql->f("name"), ENT_QUOTES) . "?');\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/trash.gif\" title=\"$owl_lang->alt_del_folder\" border=\"0\"></img></a>");
            fPrintButtonSpace(1,4);
         }
         else
         {
             fPrintButtonSpace(1,18);
         }

         // *****************************************
         // Display the Property Icons For the Folders
         // *****************************************
    
         if (check_auth($sql->f("id"), "folder_property", $userid, false, false) == 1)
         {
            $urlArgs2 = $urlArgs;
            $urlArgs2['id'] = $sql->f("id");
            $urlArgs2['action'] = 'folder_modify';
            $url = fGetURL ('modify.php', $urlArgs2);
   
            print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/edit.gif\" border=\"0\" alt=\"$owl_lang->alt_mod_folder\" title=\"$owl_lang->alt_mod_folder\"></img></a>");
            fPrintButtonSpace(1,4);
         }
         else
         {
             fPrintButtonSpace(1,21);
         }

         if ( $default->advanced_security == 1 )
         {
            if (check_auth($sql->f("id"), "folder_acl", $userid, false, false) == 1)
            {
               $urlArgs2 = $urlArgs;
               $urlArgs2['id'] = $sql->f("id");
               $urlArgs2['parent'] = $parent;
               $urlArgs2['edit'] = 1;
               $urlArgs2['action'] = "folder_acl";
               $sUrl = fGetURL ('setacl.php', $urlArgs2);
               print("<a href=\"$sUrl\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/setacl.png\" border=\"0\" alt=\"$owl_lang->alt_set_folder_acl\" title=\"$owl_lang->alt_set_folder_acl\"></img></a>");
                                                                                                                                                                 
                fPrintButtonSpace(1,25);
            }
         }
         else
         {
            fPrintButtonSpace(1,39);
         }
 
         // *****************************************
         // Display the move Icons For the Folders
         // *****************************************
 
         //if (check_auth($sql->f("id"), "folder_modify", $userid, false, false) == 1 and check_auth($sql->f("id"), "folder_delete", $userid, false, false) == 1)
         if (check_auth($sql->f("id"), "folder_cp", $userid, false, false) == 1)
         {
             $urlArgs2 = $urlArgs;
             $urlArgs2['id'] = $sql->f("id");
             $urlArgs2['action'] = 'cp_folder';
             $urlArgs2['parent'] = $parent;
             $url = fGetURL ('move.php', $urlArgs2);
   
             print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/copy.gif\" border=\"0\" alt=\"$owl_lang->alt_copy_folder\" title=\"$owl_lang->alt_copy_folder\"></img></a>");
   
             fPrintButtonSpace(1,4);
         }
         
         if (check_auth($sql->f("id"), "folder_move", $userid, false, false) == 1)
         {

             $urlArgs2 = $urlArgs;
             $urlArgs2['id'] = $sql->f("id");
             $urlArgs2['action'] = 'folder';
             $urlArgs2['parent'] = $parent;
             $url = fGetURL ('move.php', $urlArgs2);

             print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/move.gif\" border=\"0\" alt=\"$owl_lang->alt_move_folder\" title=\"$owl_lang->alt_move_folder\"></img></a>");

             fPrintButtonSpace(1,92);
         } 
         else
         {
             fPrintButtonSpace(1,127);
         }
   

         //if (check_auth($sql->f("id"), "folder_view", $userid, false, false) == 1)
         if (check_auth($sql->f("id"), "folder_monitor", $userid, false, false) == 1)
         {
            $folder_id = $sql->f("id");
            $checksql = new Owl_DB;
            $checksql->query("select * from $default->owl_monitored_folder_table where fid = '$folder_id' and userid = '$userid'");
            $checknumrows = $checksql->num_rows($checksql);
   
            $checksql->query("SELECT * from $default->owl_users_table where id = '$userid'");
            $checksql->next_record();
            if ($default->owl_version_control == 1)
            {
               fPrintButtonSpace(1,18);
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
   
                  print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/monitor.gif\" border=\"0\" alt=\"$owl_lang->alt_monitor_folder\" title=\"$owl_lang->alt_monitor_folder\"></img></a>");
               } 
               else
               {
                  $urlArgs2 = $urlArgs;
                  $urlArgs2['id'] = $folder_id;
                  $urlArgs2['parent'] = $parent;
                  $urlArgs2['action'] = 'folder_monitor';
                  $url = fGetURL ('dbmodify.php', $urlArgs2);
   
                  print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/monitored.gif\" border=\"0\" alt=\"$owl_lang->alt_monitored_folder\" title=\"$owl_lang->alt_monitored_folder\"></img></a>");
               } 
               fPrintButtonSpace(1,40);
            } 
            else
            {
               fPrintButtonSpace(1,39);
            }
         } 

         if (check_auth($sql->f("id"), "folder_view", $userid, false, false) == 1)
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
   
            if (file_exists($default->tar_path) && trim($default->tar_path) != "" && file_exists($default->gzip_path) && trim($default->gzip_path) != "")
            {
               print("<a href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/zip.gif\" border=\"0\" alt=\"$owl_lang->alt_get_folder\" title=\"$owl_lang->alt_get_folder\"></img></a>");
               fPrintButtonSpace(1,1);
            }
            else
            {
               fPrintButtonSpace(1,17);
            }
         } 
         if ($default->thumbnails == 1 and fisAdmin())
         {
            $urlArgs2 = $urlArgs;
            $urlArgs2['id'] = $sql->f("id");
            $urlArgs2['parent'] = $sql->f("parent");
            $urlArgs2['action'] = 'folder_thumb';
            $sUrl = fGetURL ('dbmodify.php', $urlArgs2);
            print("<a href=\"$sUrl\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/thumb.png\" border=\"0\" alt=\"$owl_lang->thumb_re_generate\" title=\"$owl_lang->thumb_re_generate\"></img></a>");
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
} 

if ($default->owl_LookAtHD != "false")
{
   $DBFolders[$DBFolderCount + 1] = "[END]"; //end DBfolder array
   $RefreshPage = CompareDBnHD('folder', $default->owl_FileDir . "/" . get_dirpath($parent), $DBFolders, $parent, $default->owl_folders_table);
} 

//$midf->printFooter();


//*************************************
// BEGIN Print Files
//*************************************
// 

$DBFileCount = 0;

$sql = new Owl_DB;

   if ($default->records_per_page > 0)
   {
      $sql->query("select * from $default->owl_files_table where parent = '$parent'");
      while ($sql->next_record())
      {
         $DBFileCount++; //count number of filez in db 2 use with array
         $DBFiles[$DBFileCount] = $sql->f("filename"); //create list if files in
      }
   }

//print("Q: $FileQuery");
$sql->query($FileQuery);

while ($sql->next_record())
{
   $bPrintNew = false;
   $bPrintUpdated = false;
   $bFileDownload = check_auth($sql->f("id"), "file_download", $userid, false, false);
   if ($default->restrict_view == 1)
   {
      if (!$bFileDownload)
      {
         if ($default->records_per_page == 0)
         {
            $DBFileCount++; //count number of filez in db 2 use with array
            $DBFiles[$DBFileCount] = $sql->f("filename"); //create list if files in
         }
         continue;
      } 
   } 

   if ($sql->f("approved") == 0)
   {
      $DBFileCount++; //count number of filez in db 2 use with array
      $DBFiles[$DBFileCount] = $sql->f("filename"); //create list if files in
      continue;
   } 

   // 
   // Find New files
   // 
   
   if ($bFileDownload == 1)
   {
      if ($sql->f("created") > $lastlogin)
      {
         $bPrintNew = true;
      } 
      if ($sql->f("smodified") > $lastlogin && $sql->f("created") < $lastlogin)
      {
         $bPrintUpdated = true;
      } 
   } 

   // ******************************************
   // Check to see if this file as any comments
   // ******************************************

   $bHasComments = true;
   $bPrintNewComment = false;

   $CheckComments = $cCommonDBConnection;

   if (empty($CheckComments))
   {
      $CheckComments = new Owl_DB;
   }

   $CheckComments->query("SELECT * from $default->owl_comment_table where fid = '" . $sql->f("id") . "' order by comment_date desc");

   $iTotalComments = $CheckComments->num_rows();

   $CheckComments->next_record();

   if ($CheckComments->f("comment_date") > $lastlogin)
   {
      $bPrintNewComment = true;
   }


   if ($iTotalComments == 0)
   {
      $bHasComments = false;
   } 

   // ******************************************
   // Check to see if this file is Word Indexed 
   // ******************************************

   $CheckComments->query("SELECT * from $default->owl_searchidx where owlfileid = '" . $sql->f("id") . "'");

   if ($CheckComments->num_rows() > 0)
   {
      $bWasIndexed = true;
   }
   else
   {
      $bWasIndexed = false;
   }

   $iRealFileID = fGetPhysicalFileId($sql->f('id'));

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
 
   //print("\t\t\t\t<tr id=\"filetr" . $sql->f("id") . \" class=\"$sTrClassHilite\" onmouseover=\"this.className='$sTrClassHiliteAlt'\" onmouseout=\"this.className='$sTrClassHilite'\">");
   //print("\t\t\t\t<tr id=\"filetr" . $sql->f("id") . "\" class=\"$sTrClassHilite\">");
   print("\t\t\t\t<tr id=\"filetr" . $sql->f("id") . "\" class=\"$sTrClassHilite\" onmouseover=\"alt_css_style('checkid" . $sql->f("id") . "', this, '$sTrClassHiliteAlt')\"  onmouseout=\"alt_css_style('checkid" . $sql->f("id") . "', this, '$sTrClassHilite')\">\n");


   if ($default->show_bulk > 0 or (fIsAdmin() and $default->show_bulk == 0 ))
   {
      if ($sess != "0" || ($sess == "0" && $default->anon_ro == 0))
      {
         print("<td class=\"$sTrClass\">");
         print("<input id=\"checkid" .  $sql->f("id") . "\" type=\"checkbox\" name=\"batch[]\" value=\"" . $sql->f("id") . "\" onclick=\"mark_selected('filetr" . $sql->f("id") . "', this, '$sTrClassHilite')\"></input>");

         print("</td>");
      } 
   } 
   
   if(($default->expand_disp_status and $expand == 1) or ($default->collapse_disp_status and $expand == 0))
   {
      print("<td class=\"$sTrClass\" align=\"left\">");
      if ($default->show_bulk == 0 and ! fIsAdmin())
      {
         print("<input id=\"checkid" .  $sql->f("id") . "\" type=\"hidden\" name=\"style_change\" value=\"" . $sql->f("id") . "\"></input>");
      }
      if ($bHasComments)
      {
         if ($bPrintNewComment)
         {
            $iImage = "newcomment";
         }
         else
         {
            $iImage = "comment";
         }
         
         $urlArgs2 = $urlArgs;
         $urlArgs2['id']     = $sql->f("id");
         $urlArgs2['parent'] = $parent;
         $urlArgs2['action'] = 'file_comment';
         $url = fGetURL ('modify.php', $urlArgs2);
   
         print("<a class=\"$sLfList\" href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/$iImage.gif\" border=\"0\" alt=\"$iTotalComments --- $owl_lang->alt_comments\" title=\"$iTotalComments --- $owl_lang->alt_comments\"></img></a>");
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

   if (($default->expand_disp_doc_num and $expand == 1) or ($default->collapse_disp_doc_num and $expand == 0))
   {
      $sZeroFilledId = str_pad($sql->f("id"),$default->doc_id_num_digits, "0", STR_PAD_LEFT);
      print("<td class=\"$sTrClass\" align=\"left\">");
      if ($fileid == $sql->f("id"))
      {
         print("<b class=\"hilite\">" . $default->doc_id_prefix . $sZeroFilledId . "</b>");
      }
      else
      {
         print $default->doc_id_prefix . $sZeroFilledId;
      } 
      print("</td>");

   }
   if ($default->thumbnails == 1 and $default->thumbnails_small_width > 0)
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
            $sDispIcon = $ext . ".gif";
         }
         else
         {
            $sDispIcon = $ext . "_lnk.gif";
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
         if (!file_exists("$default->owl_fs_root/graphics/$default->sButtonStyle/icon_filetype/$sDispIcon"))
         {
            if ($iRealFileID == $sql->f('id'))
            {
               $sDispIcon = "file.gif";
            }
            else
            {
               $sDispIcon = "file_lnk.gif";
            }
         }

         $urlArgs2 = $urlArgs;
         $urlArgs2['id']     = $sql->f("id");
         $urlArgs2['parent'] = $sql->f("parent");
         $url = fGetURL ('download.php', $urlArgs2);

         print("<a  class=\"$sLfList\" href=\"$url\" title=\"$owl_lang->title_download_view : " . $sql->f("filename") ."\">");
         print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/$sDispIcon\" border=\"0\" alt=\"\"></img></a>");
      } 

      print("<br /></td>\n"); 
   }

   if (($default->expand_disp_title and $expand == 1) or ($default->collapse_disp_title and $expand == 0))
   {
      print("<td class=\"$sTrClass\" align=\"left\">");


     //check if display custom fields in description window is allowed - added by maurizio (madal2005)
      if ($default->allow_custfieldspopup == 1)
      {
         //build a string with custom fields to add at description string
         if (strlen(fPopCustomFields ($sql->f("doctype"), $sql->f("id"), $sql->f("required")))==0)
         {
            $sPopupDescription= fCleanDomTTContent($sql->f("description"));
         }
         else
         {
           $sPopupDescription =   fCleanDomTTContent(fPopCustomFields ($sql->f("doctype"), $sql->f("id")));
           $sPopupDescription.= fCleanDomTTContent($sql->f("description"));
         }
      }
      else
      {
          $sPopupDescription= fCleanDomTTContent($sql->f("description"));
      }
      // end check

      if (trim($sPopupDescription) == "") 
      {
         $sPopupDescription = $owl_lang->no_description;
      }

      $urlArgs2 = $urlArgs;
      $urlArgs2['sess']   = $sess;
      $urlArgs2['id']     = $sql->f("id");
      $urlArgs2['parent'] = $parent;
      $urlArgs2['action'] = 'file_details';
      $url = fGetURL ('view.php', $urlArgs2);
  
      print("\n<a class=\"$sLfList\" href=\"$url\" onmouseover=" . '"' . "return makeTrue(domTT_activate(this, event, 'caption', '" . $owl_lang->description . "', 'content', '" . $sPopupDescription . "', 'lifetime', 3000, 'fade', 'both', 'delay', 10, 'statusText', ' ', 'trail', true));" . '"');
   
      print(">\n");
      print("\n");
   
      if ($fileid == $sql->f("id"))
      {
         print("<b class=\"hilite\">" . $sql->f("name") . "</b></a>");
      }
      else
      {
         print $sql->f("name") . "</a>";
      } 
      print("</td>\n");
   }

   if (($default->expand_disp_doc_fields and $expand == 1) or ($default->collapse_disp_doc_fields and $expand == 0))
   {
      print("<td class=\"$sTrClass\">");
      print("<table>\n");
      fPrintCustomFields ($sql->f("doctype"), $sql->f("id"), $sql->f("required"), "visible", "readonly");
      print("</table>\n");
      print("</td>\n");
   }

   if ($default->owl_version_control == 1)
   {
      if ($fileid == $sql->f("id"))
      {
         if (($default->expand_disp_version and $expand == 1) or ($default->collapse_disp_version and $expand == 0))
         {
            print("\n<td class=\"$sTrClass\" align=\"left\"><b class=\"hilite\">" . $sql->f("major_revision") . "." . $sql->f("minor_revision") . "</b></td>");
         }
      }
      else
      {
         if (($default->expand_disp_version and $expand == 1) or ($default->collapse_disp_version and $expand == 0))
         {
            print("\n<td class=\"$sTrClass\" align=\"left\">" . $sql->f("major_revision") . "." . $sql->f("minor_revision") . "</td>");
         }
      }
   } 

   if ($sql->f("url") == "1")
   {
      if ($fileid == $sql->f("id"))
      {
         if ($bFileDownload == 1)
         {
            if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
            {
               print("\n<td class=\"$sTrClass\" align=\"left\"><a class=\"$sLfList\" href=\"" . $sql->f("filename") . "\" target=\"new\" title=\"$owl_lang->title_browse_site : " . $sql->f("filename") . "\"><b class=\"hilite\">" . $sql->f("filename") . " </b></a></td>\n");
            }
            if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
            {
               print("<td class=\"$sTrClass\" align=\"right\"><b class=\"hilite\">&nbsp;</b></td>\n");
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
               print("<td class=\"$sTrClass\" align=\"right\"><b class=\"hilite\">&nbsp;</b></td>");
            }
         } 
      } 
      else
      {
         //if ($bFileDownload == 1)
         //{
            if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
            {
               if($default->old_action_icons)
               {
               print("\n<td class=\"$sTrClass\" align=\"left\"><a class=\"$sLfList\" href=\"" . $sql->f("filename") . "\" target=\"new\" title=\"$owl_lang->title_browse_site : " . $sql->f("filename") . "\">" . $sql->f("filename") . "</a></td>\n");
                  print("</td>\n");
               }
               else
               {
                  print("\n<td class=\"$sTrClass\" align=\"left\">");
                  if(!$default->old_action_icons)
                  {
                     $mid->printMenu('vermenu'.$sql->f("id"));
                  }
                  else
                  {
                     print("&nbsp;");
                  }
                  print("</td>\n");
               }
            }
            if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
            {
               print("<td class=\"$sTrClass\" align=\"right\">&nbsp;</td>\n");
            }
         //} 
         //else
         //{
            //if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
            //{
               //print("\n<td class=\"$sTrClass\" align=\"left\">" . $sql->f("filename") . "</td>\n");
            //}
            //if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
            //{
               //print("<td class=\"$sTrClass\" align=\"right\">&nbsp;</td>\n");
            //}
         //} 
      } 
   }
   else
   {
      $urlArgs2 = $urlArgs;
      $urlArgs2['id']     = $sql->f("id");
      $urlArgs2['parent'] = $sql->f("parent");
      $url = fGetURL ('download.php', $urlArgs2);

      if ($fileid == $sql->f("id"))
      {
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            if(!$default->old_action_icons)
            {
               print("\n<td class=\"$sTrClass\" align=\"left\"><b class=\"hilite\">");
               $mid->printMenu('vermenu'.$sql->f("id"));
               print("</b></a>");
            }
            else
            {
               print("\n<td class=\"$sTrClass\"  align=\"left\"><a  class=\"$sLfList\" href=\"$url\" title=\"$owl_lang->title_download_view\"><b class=\"hilite\">" . $sql->f("filename") . "</b></a>");
            }
            print("</td>\n");
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"right\"><b class=\"hilite\">" . gen_filesize($sql->f("f_size")) . "</b></td>\n");
         }
      }
      else
      { 
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            if($default->old_action_icons)
            {
               print("\n<td class=\"$sTrClass\" align=\"left\"><a class=\"$sLfList\" href=\"$url\" title=\"$owl_lang->title_download_view\">" . $sql->f("filename") . "</a>");
               print("</td>\n");
            }
            else
            {
               print("\n<td class=\"$sTrClass\" align=\"left\">");
            if(!$default->old_action_icons)
            {
               $mid->printMenu('vermenu'.$sql->f("id"));
            }
               print("</td>\n");
            }
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            print("<td class=\"$sTrClass\" align=\"right\">" . gen_filesize($sql->f("f_size")) . "</td>");
         }
      }
      if ($default->records_per_page == 0)
      {
         if ($sql->f("linkedto") == 0)
         {
            $DBFileCount++; //count number of filez in db 2 use with array
            $DBFiles[$DBFileCount] = $sql->f("filename"); //create list if files in
         }
      }
   }
// SET THE user link if requested if not thne just the name is shown
      if( $default->show_user_info == 1)
      {
         $dDateLastLoging =  date($owl_lang->localized_date_format , strtotime(fid_to_creator_lastlogon($sql->f("id"))) + $default->time_offset);
         $sLinkToUser = "<a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("creatorid") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=$parent&amp;order=$order&amp;sortname=$sortname\"title=\"$owl_lang->last_logged " .  $dDateLastLoging  . "\">" . uid_to_name($sql->f("creatorid")) . "</a>";
         $sLinkToUpdator = "<a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("updatorid") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=$parent&amp;order=$order&amp;sortname=$sortname\"title=\"$owl_lang->last_logged " . $dDateLastLoging  . "\">" . uid_to_name($sql->f("updatorid")) . "</a>";
      }
      else
      {
         $sLinkToUser = uid_to_name($sql->f("creatorid"));
         $sLinkToUpdator = uid_to_name($sql->f("updatorid"));
      }

      if ($fileid == $sql->f("id"))
      {
         if (($default->expand_disp_posted and $expand == 1) or ($default->collapse_disp_posted and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\" align=\"left\"><b class=\"hilite\">$sLinkToUser</b></td>\n");
         }
         if (($default->expand_disp_updated and $expand == 1) or ($default->collapse_disp_updated and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\" align=\"left\"><b class=\"hilite\">$sLinkToUpdator</b></td>\n");
         }
         if (($default->expand_disp_modified and $expand == 1) or ($default->collapse_disp_modified and $expand == 0))
         {
            if ($sql->f("smodified"))
            {
               print("<td class=\"$sTrClass\" align=\"left\"><b class=\"hilite\">" . date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset) . "</b></td>\n");
            }
            else
            {
               print("<td class=\"$sTrClass\">&nbsp;</td>\n");
            }
         }
      }
      else
      {
         if (($default->expand_disp_posted and $expand == 1) or ($default->collapse_disp_posted and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\" align=\"left\">$sLinkToUser</td>\n");
         }
         if (($default->expand_disp_updated and $expand == 1) or ($default->collapse_disp_updated and $expand == 0))
         {
            print("\t\t\t\t<td class=\"$sTrClass\" align=\"left\">$sLinkToUpdator</td>\n");
         }
         if (($default->expand_disp_modified and $expand == 1) or ($default->collapse_disp_modified and $expand == 0))
         {
            if ($sql->f("smodified"))
            {
               print("<td class=\"$sTrClass\" align=\"left\">" . date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset) . "</td>\n");
            }
            else
            {
               print("<td class=\"$sTrClass\">&nbsp;</td>\n");
            }
         }
      }
      if ((($default->expand_disp_action and $expand == 1) or ($default->collapse_disp_action and $expand == 0)) and $default->old_action_icons)
      {
         print("\t\t\t\t<td class=\"$sTrClass\" align=\"left\">");
         printFileIcons($sql->f("id"), $sql->f("filename"), $sql->f("checked_out"), $sql->f("url"), $default->owl_version_control, $ext, $parent, $is_backup_folder);
      }
   if ($default->owl_version_control == 1)
   {
      if (($default->expand_disp_held and $expand == 1) or ($default->collapse_disp_held and $expand == 0))
      {
         if (($holder = uid_to_name($sql->f("checked_out"))) == "Owl")
         {
            print("\t<td class=\"$sTrClass\" align=\"center\">-</td></tr>");
         } 
         else
         {      
            if( $default->show_user_info == 1)
            {
               $sLinkToUser = "<a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("checked_out") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=$parent&amp;order=$order&amp;sortname=$sortname\" title=\"$owl_lang->last_logged " . date($owl_lang->localized_date_format , strtotime(fid_to_creator_lastlogon($sql->f("id"))) + $default->time_offset)  . "\">$holder</a>";
            }
            else
            {
               $sLinkToUser = $holder;
            }

            print("\t<td class=\"$sTrClass\" align=\"left\">$sLinkToUser</td></tr>");
         } 
      }
   } 
} 

   $DBFiles[$DBFileCount + 1] = "[END]"; //end DBfile array
   print("</table>");
   print("</td></tr></table>\n");

?>
