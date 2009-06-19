<?php
/**
 * view_thumb.php -- Thumb Nail view for Browse page
 * 
 * Author: Steve Bourgeois <owl@bozzit.com> 
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 */


$iColumnWidth = round(100 / $default->thumbnail_view_columns);



print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n<tr>\n<td align=\"left\" valign=\"top\">\n");
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");

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

$sql->query($FolderQuery);
// **********************
// BEGIN Print Folders
// **********************
$aRenderLine = array();
$RowCount = 0;

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
   $iFolderId = $sql->f("id");
   
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
   $RowCount++;
   $PrintLines = $CountLines % 2;
   
    
   $aRenderLine['type'][$RowCount] = "FOLDER";
   $aRenderLine['id'][$RowCount] = $sql->f("id");
   if ($PrintLines == 0)
   {
      $sTrClass = "file1";
      $sLfList = "lfile1";
   }
   else
   {
      $sTrClass = "file2";
      $sLfList = "lfile1";
   }
    
   if ($default->show_bulk > 0 or (fIsAdmin() and $default->show_bulk == 0 ))
   {
      if ($sess != "0" || ($sess == "0" && $default->anon_ro == 0))
      {
         $aRenderLine['bulk'][$RowCount] = "<input type=\"checkbox\" name=\"fbatch[]\" value=\"" . $sql->f("id") . "\"></input>";
      } 
   } 
   
   if(($default->expand_disp_doc_type and $expand == 1) or ($default->collapse_disp_doc_type and $expand == 0))
   {
      $aRenderLine['icon'][$RowCount] = "<img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/folder_closed.gif\" border=\"0\" alt=\"\"></img>";
   }
    
   
   if(($default->expand_disp_title and $expand == 1) or ($default->collapse_disp_title and $expand == 0))
   {
      $sPopupDescription = nl2br(trim($sql->f("description")));
    
      $urlArgs2 = $urlArgs;
      $urlArgs2['parent'] = $sql->f("id");
      $url = fGetURL ('browse.php', $urlArgs2);
       
      $aRenderLine['name'][$RowCount] = "\n<a class=\"$sLfList\" href=\"$url\" title=\"$owl_lang->title_browse_folder\">" . $sql->f("name") . "</a>";
       
      if(!$default->hide_folder_doc_count)
      {
         if ($iFolderCount > 0 or $iFileCount > 0 or $iUrlCount  > 0 or $iNoteCount > 0)
         {
            $aRenderLine['name'][$RowCount] .= "&nbsp;(";
         } 
         if ($iFolderCount > 0 )
         {
            $aRenderLine['name'][$RowCount] .= "<a href=\"#\" class=\"cfolders1\" title=\"$owl_lang->folder_count_pre $iFolderCount $owl_lang->folder_count_folder\">$iFolderCount</a>";
         }
         if ($iFileCount > 0 )
         {
            if ($iFolderCount > 0)
            {
               $aRenderLine['name'][$RowCount] .= ":";
            }
            $aRenderLine['name'][$RowCount] .= "<a href=\"#\" class=\"cfiles1\" title=\"$owl_lang->folder_count_pre $iFileCount $owl_lang->folder_count_file\">$iFileCount</a>";
         }
         if ($iUrlCount  > 0 )
         {
            if ($iFileCount > 0)
            {
               $aRenderLine['name'][$RowCount] .= ":";
            }
            $aRenderLine['name'][$RowCount] .= "<a href=\"#\" class=\"curl1\" title=\"$owl_lang->folder_count_pre $iUrlCount $owl_lang->folder_count_url\">$iUrlCount</a>";
         }
         if ($iNoteCount > 0)
         {
            $aRenderLine['name'][$RowCount] .= ":<a href=\"#\" class=\"cnotes1\" title=\"$owl_lang->folder_count_pre $iNoteCount $owl_lang->folder_count_note\">$iNoteCount</a>";
         }
         if ($iFolderCount > 0 or $iFileCount > 0 or $iUrlCount  > 0 or $iNoteCount > 0)
         {
            $aRenderLine['name'][$RowCount] .= ")";
         }
      }
    
      if (trim($sql->f("description")))
      {
            $aRenderLine['description'][$RowCount] = "<br /><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/transparent.gif\" border=\"0\"><a class=\"desc\">" . str_replace("\n", "<br /><img src=$default->owl_graphics_url/$default->sButtonStyle/ui_misc/transparent.gif border=\"0\"></img>", $sql->f("description")) . "</a>";
      }
   
   }
   
   if ($default->records_per_page == 0)
   {
      $DBFolderCount++; //count number of filez in db 2 use with array
      $DBFolders[$DBFolderCount] = $sql->f("name"); //create list if files in
   }
   
   
   if ($default->owl_version_control == 1)
   {
      if ($default->hide_folder_size)
      {
         $aRenderLine['size'][$RowCount] = "";
      }
      else
      {
         $FolderSize = fGetFolderSize($sql->f("id"));
         $aRenderLine['size'][$RowCount] = gen_filesize($FolderSize);
      }
   
      $aRenderLine['creator'][$RowCount] = "<a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("creatorid") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=$parent&amp;order=$order&amp;sortname=$sortname&amp;curview=$curview\">" . flid_to_creator($sql->f("id")) . "</a>";
      if ($sql->f("smodified"))
      {
         $aRenderLine['smodified'][$RowCount] = date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset);
      }
      else
      {
         $aRenderLine['smodified'][$RowCount] = "&nbsp;";
      }
   } 
   else
   {
      $aRenderLine['creator'][$RowCount] = flid_to_creator($sql->f("id"));
      if ($sql->f("smodified"))
      {
         $aRenderLine['smodified'][$RowCount] = date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset);
      }
      else
      {
         $aRenderLine['smodified'][$RowCount] = "&nbsp;";
      }
   } 

   $aRenderLine['status'][$RowCount] = "";
   $aRenderLine['docid'][$RowCount] = "";
   
   $urlArgs2 = $urlArgs;
   $urlArgs2['parent'] = $sql->f("id");
   $url = fGetURL ('browse.php', $urlArgs2);
   
   $aRenderLine['thumb'][$RowCount] = "\n<a class=\"$sLfList\" href=\"$url\" title=\"$owl_lang->title_browse_folder\">";
   $aRenderLine['thumb'][$RowCount] .= "<img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/thumb_folder.png\" border=\"0\" alt=\"" . $sql->f("id") ."\"></img>";
   $aRenderLine['thumb'][$RowCount] .= "</a>";
   
   if($default->old_action_icons)
   {
      $aRenderLine['filename'][$RowCount] = $sql->f("id");
   }
   $aRenderLine['version'][$RowCount] = "";
   $aRenderLine['checkedout'][$RowCount] = "";
   $aRenderLine['imageattr'][$RowCount] = "";
   
   $PrintRow = $CountLines % $default->thumbnail_view_columns;
    
   if ($PrintRow == "0")
   {
      $aRenderLine = fRenderThumbNails($aRenderLine, $sTrClass);
      $RowCount = 0;
   }
} 

if ($default->owl_LookAtHD != "false")
{
   $DBFolders[$DBFolderCount + 1] = "[END]"; //end DBfolder array
   $RefreshPage = CompareDBnHD('folder', $default->owl_FileDir . "/" . get_dirpath($parent), $DBFolders, $parent, $default->owl_folders_table);
} 

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
   
   $CheckComments = new Owl_DB;
   
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
   $RowCount++;
    
   $iFileId     = $sql->f("id");
   $aRenderLine['type'][$RowCount] = "FILE";
   $aRenderLine['id'][$RowCount] = $sql->f("id");
   
   $PrintLines = $CountLines % 2;
   if ($PrintLines == 0)
   {
      $sTrClass = "file1";
      $sLfList = "lfile1";
   }
   else
   {
      $sTrClass = "file2";
      $sLfList = "lfile1";
   }

   if ($default->show_bulk > 0 or (fIsAdmin() and $default->show_bulk == 0 ))
   {
      if ($sess != "0" || ($sess == "0" && $default->anon_ro == 0))
      {
         $aRenderLine['bulk'][$RowCount] = "<input type=\"checkbox\" name=\"batch[]\" value=\"" . $sql->f("id") . "\"></input>";
      } 
   } 
 
   if(($default->expand_disp_status and $expand == 1) or ($default->collapse_disp_status and $expand == 0))
   {
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
       
         $aRenderLine['status'][$RowCount] = "<a class=\"$sLfList\" href=\"$url\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_action/$iImage.gif\" border=\"0\" alt=\"$iTotalComments --- $owl_lang->alt_comments\" title=\"$iTotalComments --- $owl_lang->alt_comments\"></img></a>";
      } 
      if ($default->anon_user <> $userid)
      {
         if ($bPrintNew)
         {
            $aRenderLine['status'][$RowCount] .= "<img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_icons/new.gif\" border=\"0\" alt=\"$owl_lang->alt_new\"></img>";
         } 
         if ($bPrintUpdated)
         {
            $aRenderLine['status'][$RowCount] .= "<img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_icons/updated.gif\" border=\"0\" alt=\"$owl_lang->alt_updated\"></img>";
         } 
         if ($bWasIndexed)
         {
            $aRenderLine['status'][$RowCount] .= "&nbsp;<a class=\"curl1\">*</a>";
         }
      } 
   }

   $sZeroFilledId = str_pad($sql->f("id"),$default->doc_id_num_digits, "0", STR_PAD_LEFT);
   $aRenderLine['docid'][$RowCount] = $default->doc_id_prefix . $sZeroFilledId;
 
   $sThumbUrl = $default->thumbnails_url . "/". $default->owl_current_db . "_"  . $iRealFileID . "_med.png";
   $sThumbLoc = $default->thumbnails_location . "/" . $default->owl_current_db . "_" . $iRealFileID . "_med.png";
   
   $urlArgs2 = $urlArgs;
   $urlArgs2['binary'] = 1;
   $urlArgs2['id'] = $sql->f("id");
   $urlArgs2['parent'] = $sql->f("parent");
   $sUrl = fGetURL ('download.php', $urlArgs2);

   if (file_exists($sThumbLoc))
   {
      $aRenderLine['thumb'][$RowCount] = "<a href=\"$sUrl\" title=\"" . $sql->f("filename"). "\">";
      $aRenderLine['thumb'][$RowCount] .= "<img src=\"$sThumbUrl\" border=\"0\" alt=\"" . $sql->f("filename"). "\"></img>";
   }
   else
   {
      $aRenderLine['thumb'][$RowCount] = "<a href=\"$sUrl\" title=\"$owl_lang->alt_no_thumb\">";
      $aRenderLine['thumb'][$RowCount] .= "<img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/thumb_no.png\" border=\"0\" alt=\"$owl_lang->alt_no_thumb\" title=\"$owl_lang->alt_no_thumb\"></img>";
   }

   $aRenderLine['thumb'][$RowCount] .= "</a>";
 
   $sFileExtension = fFindFileExtension($sql->f("filename"));
   $aImageExtensionList = $default->thumbnail_image_type;
   $aVideoExtensionList = $default->thumbnail_video_type;
   $path = $default->owl_FileDir . "/" . find_path($parent) . "/" . $sql->f("filename");
   
   $imagedata = array();

   if ((preg_grep("/$sFileExtension/", $aImageExtensionList)))
   {
      $imagedata = @GetImageSize("$path");
      if($imagedata)
      {
         $aRenderLine['imageattr'][$RowCount] = $imagedata[0] . "x" . $imagedata[1];
      }
      else
      {
         $aRenderLine['imageattr'][$RowCount] = " Unknown";
      }
   }
   else
   {
      $aRenderLine['imageattr'][$RowCount] = "";
   }



   if (($default->expand_disp_doc_type and $expand == 1) or ($default->collapse_disp_doc_type and $expand == 0))
   {
     
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
         {
            $ext = "tar.gz";
         }
      } 
 
      if ($sql->f("url") == "1")
      {
         $aRenderLine['icon'][$RowCount] = "<img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/url.gif\" border=\"0\" alt=\"\"></img>";
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
         $urlArgs2['sess']   = $sess;
         $urlArgs2['id']     = $sql->f("id");
         $urlArgs2['parent'] = $parent;
         $url = fGetURL ('download.php', $urlArgs2);
                                                                                                                                                                                        
         $aRenderLine['icon'][$RowCount] =  "<a class=\"$sLfList\" href=\"$url\" title=\"$owl_lang->title_download_view : " . $sql->f("filename") ."\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/$sDispIcon\" border=\"0\" alt=\"\"></img></a>";
      } 
   }

   if (($default->expand_disp_title and $expand == 1) or ($default->collapse_disp_title and $expand == 0))
   {
      $sPopupDescription = fCleanDomTTContent($sql->f("description"));

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
    
      $aRenderLine['name'][$RowCount] =  "\n<a class=\"$sLfList\" href=\"$url\" onmouseover=" . '"' . "return makeTrue(domTT_activate(this, event, 'caption', '" . $owl_lang->description . "', 'content', '" . $sPopupDescription . "', 'lifetime', 3000, 'fade', 'both', 'delay', 10, 'statusText', ' ', 'trail', true));" . '"';
 
      $aRenderLine['name'][$RowCount] .= ">\n\n";
 
      $aRenderLine['name'][$RowCount] .=  $sql->f("name") . "</a>";
   }

   if ($default->owl_version_control == 1)
   {
      if (($default->expand_disp_version and $expand == 1) or ($default->collapse_disp_version and $expand == 0))
      {
         $aRenderLine['version'][$RowCount] = $sql->f("major_revision") . "." . $sql->f("minor_revision");
      }
   } 

   if ($sql->f("url") == "1")
   {
      if ($bFileDownload == 1)
      {
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            if($default->old_action_icons)
            {
               $aRenderLine['filename'][$RowCount] = "<a class=\"$sLfList\" href=\"" . $sql->f("filename") . "\" target=\"new\" title=\"$owl_lang->title_browse_site : " . $sql->f("filename") . "\">" . $sql->f("filename") . "</a>";
            }
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            $aRenderLine['size'][$RowCount] = "&nbsp;";
         }
      } 
      else
      {
         if (($default->expand_disp_file and $expand == 1) or ($default->collapse_disp_file and $expand == 0))
         {
            $aRenderLine['filename'][$RowCount] = $sql->f("filename");
         }
         if (($default->expand_disp_size and $expand == 1) or ($default->collapse_disp_size and $expand == 0))
         {
            $aRenderLine['size'][$RowCount] = "&nbsp;";
         }
      } 
   }
   else
   {
      $urlArgs2 = $urlArgs;
      $urlArgs2['id']     = $sql->f("id");
      $urlArgs2['parent'] = $sql->f("parent");
      $url = fGetURL ('download.php', $urlArgs2);
      $aRenderLine['size'][$RowCount] = gen_filesize($sql->f("f_size"));
   }

   if ($default->records_per_page == 0)
   {
      if ($sql->f("linkedto") == 0)
      {
         $DBFileCount++; //count number of filez in db 2 use with array
         $DBFiles[$DBFileCount] = $sql->f("filename"); //create list if files in
      }
   }

   $aRenderLine['creator'][$RowCount] = "<a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("creatorid") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=$parent&amp;order=$order&amp;sortname=$sortname&amp;curview=$curview\" title=\"$owl_lang->last_logged " . date($owl_lang->localized_date_format , strtotime(fid_to_creator_lastlogon($sql->f("id"))) + $default->time_offset)  . "\">" . fid_to_creator($sql->f("id")) . "</a>";
   $aRenderLine['updator'][$RowCount] = "<a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("updatorid") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=$parent&amp;order=$order&amp;sortname=$sortname&amp;curview=$curview\" title=\"$owl_lang->last_logged " . date($owl_lang->localized_date_format , strtotime(fid_to_creator_lastlogon($sql->f("id"))) + $default->time_offset)  . "\">" . uid_to_name($sql->f("updatorid")) . "</a>";
   if ($sql->f("smodified"))
   {
      $aRenderLine['smodified'][$RowCount] = date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset);
   }
   else
   {
      $aRenderLine['smodified'][$RowCount] = "&nbsp;";
   }
   if ($default->owl_version_control == 1)
   {
      if (($holder = uid_to_name($sql->f("checked_out"))) == "Owl")
      {
         $aRenderLine['checkedout'][$RowCount] = "-";
      } 
      else
      {
         $aRenderLine['checkedout'][$RowCount] = "<a class=\"$sLfList\" href=\"prefs.php?owluser=" . $sql->f("checked_out") . "&amp;sess=$sess&amp;expand=$expand&amp;parent=$parent&amp;order=$order&amp;sortname=$sortname&amp;curview=$curview\" title=\"$owl_lang->last_logged " . date($owl_lang->localized_date_format , strtotime(fid_to_creator_lastlogon($sql->f("id"))) + $default->time_offset)  . "\">$holder</a>";
      } 
   } 

   $PrintRow = $CountLines % $default->thumbnail_view_columns;

   if ($PrintRow == "0")
   {
      $aRenderLine = fRenderThumbNails($aRenderLine, $sTrClass);
      $RowCount = 0;
   }
}
 
$CountLines++;
$PrintLines = $CountLines % 2;
if ($PrintLines == 0)
{
   $sTrClass = "file1";
   $sLfList = "lfile1";
}
else
{
   $sTrClass = "file2";
   $sLfList = "lfile1";
}


if ($PrintRow > 0)
{
   $aRenderLine = fRenderThumbNails($aRenderLine, $sTrClass);
}


$DBFiles[$DBFileCount + 1] = "[END]"; //end DBfile array
print("</table>");
print("</td></tr></table>\n");


function fRenderThumbNails($aRenderLine, $sTrClass)
{
   global $default, $owl_lang, $mid;

   print("<tr>");
   for ($c = 1; $c <= $default->thumbnail_view_columns; $c++)
   {
      print("<td class=\"title1\" nowrap=\"nowrap\" width=\"$iColumnWidth%\">". $aRenderLine['bulk'][$c] . "&nbsp;");
      if ($default->thumb_disp_status == 1)
      {
         print($aRenderLine['status'][$c]. "&nbsp;");
      }
      print($aRenderLine['icon'][$c] . "&nbsp;" . $aRenderLine['name'][$c] ."</td>\n");
   }
   print("</tr>\n");

   print("<tr>");
   $CountLines = 0;
   for ($c = 1; $c <= $default->thumbnail_view_columns; $c++)
   {
      $CountLines++;
      $PrintLines = $CountLines % 2;
      if ($PrintLines == 0)
      {
         $sTrClass = "file1";
         $sLfList = "lfile1";
      }
      else
      {
         $sTrClass = "file2";
         $sLfList = "lfile1";
      }
      print("<td class=\"$sTrClass\">");
   
      print($aRenderLine['thumb'][$c] . "<br />"); 
      if(strlen($aRenderLine['imageattr'][$c]) > 1)
      {
         if ($default->thumb_disp_image_info == 1)
         {
            print($owl_lang->thumb_image_size . $aRenderLine['imageattr'][$c] . "<br />");
         }
      }
      else
      {
         print("<br />");
      }

 
      if(!$default->old_action_icons)
      {
         if ($default->thumb_disp_action == 1)
         {
            if ($aRenderLine['type'][$c] == "FOLDER")
            {
               $mid->printMenu('vermenuf'.$aRenderLine['id'][$c]);
            }
            else
            {
               $mid->printMenu('vermenu'.$aRenderLine['id'][$c]);
            }
         }
      }

      if($default->thumb_disp_version == 1)
      {
         if(strlen($aRenderLine['version'][$c]) > 0)
         {
            print($owl_lang->ver . ":&nbsp;" . $aRenderLine['version'][$c] . "<br />");
         }
      }

      print($aRenderLine['filename'][$c]);
    
      if(strlen($aRenderLine['docid'][$c]) > 0 and $default->thumb_disp_doc_num == 1)
      {
         print($owl_lang->doc_number . ":&nbsp;" . $aRenderLine['docid'][$c] . "<br />");
      }
      if ($default->thumb_disp_size == 1)
      {
         if(strlen($aRenderLine['size'][$c]) > 0)
         {
            print($owl_lang->size . "&nbsp;" . $aRenderLine['size'][$c] . "<br />");
         }
      }
 
      if(strlen($aRenderLine['creator'][$c]) > 0 and $default->thumb_disp_posted == 1)
      {
         print($owl_lang->postedby .": " . $aRenderLine['creator'][$c] . "<br />"); 
      }
      if(strlen($aRenderLine['updator'][$c]) > 0 and $default->thumb_disp_updated == 1)
      {
         print($owl_lang->updated_by . ": " . $aRenderLine['updator'][$c] . "<br />"); 
      }
 
      if(strlen($aRenderLine['smodified'][$c]) > 0 and $default->thumb_disp_modified == 1)
      {
         print($owl_lang->modified .": " . $aRenderLine['smodified'][$c] . "<br />"); 
      }
 
      if(strlen($aRenderLine['checkedout'][$c]) > 0 and $default->thumb_disp_held == 1)
      {
         print($owl_lang->held .":&nbsp;" . $aRenderLine['checkedout'][$c]); 
      }
 
      print("</td>\n"); 
 
   }
   print("</tr>\n");
   $aRenderLine = array();
 
   return $aRenderLine;
}

?>
