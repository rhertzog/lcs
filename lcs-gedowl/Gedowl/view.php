<?php

/**
 * view.php
 * 
 * Copyright (c) 1999-2003 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 * $Id: view.php,v 1.20 2007/01/05 15:22:07 b0zz Exp $
 */

require_once(dirname(__FILE__)."/config/owl.php");
require_once($default->owl_fs_root ."/lib/disp.lib.php");
require_once($default->owl_fs_root ."/lib/owl.lib.php");
require_once($default->owl_fs_root ."/lib/security.lib.php");
require_once($default->owl_fs_root ."/phpid3v2/class.id3.php");

if (bIsPearAvailable())
{
   require_once($default->owl_fs_root ."/lib/Mail_Mime/mimeDecode.php");
}

 
//$clean = ob_get_contents();  
//ob_end_clean();  

if ($sess == "0" && $default->anon_ro == 1)
{
   printError($owl_lang->err_login);
}

$urlArgs = array();
$urlArgs['sess']      = $sess;
if(!empty($page))
{
   $urlArgs['page']    = $page;
}
$urlArgs['parent']    = $parent;
$urlArgs['expand']    = $expand;
$urlArgs['order']     = $order;
$urlArgs['sortorder'] = $sortorder;
$urlArgs['curview']     = $curview;


if (!empty($fileid) and is_numeric($fileid))
{
   if (check_auth($fileid, "file_download", $userid) == 1)
   {
     fGeneratePdfFile($fileid);
   }
   else
   {
       printError($owl_lang->err_nofileaccess);
   }
}

// BEGIN what Richard Bartz added to show PDF, DOC, and TXT special view
// While I was at it I added xls, mp3, and ppt.

if ($action == "pdf_show" || $action == "xls_show" || $action == "doc_show" || $action == "ppt_show" || $action == "mp3_play" or $action == "inline")
{
   if (check_auth($id, "file_download", $userid) == 1)
   {
      if ($default->owl_use_fs)
      {
         $fid = fGetPhysicalFileId($id);
         $path = $default->owl_FileDir . "/" . find_path(owlfileparent($fid)) . "/" . flid_to_filename($fid);
      } 
      else
      {
         $path = fGetFileFromDatbase($id);
      } 
   } 
   else
   {
      printError($owl_lang->err_nofileaccess);
   } 
} 

if ($action == "pdf_show" || $action == "xls_show" || $action == "doc_show" || $action == "ppt_show" || $action == "mp3_play" || $action == "inline")
{

   $sFileName = flid_to_filename($id);

   if ($action == "pdf_show")
   {
      $fspath = fCreateWaterMark($id);
   }

   if (! $fspath === false)
   {
      $path = $fspath;
   }

   $mimetyp = fGetMimeType(flid_to_filename($id));

   $len = filesize($path);
   ob_clean();
   header("Content-type: $mimetyp");
   header("Content-Length: $len");
   header("Content-Disposition: inline; filename=" . $sFileName);
   header("Content-Transfer-Encoding: binary");
   readfile($path);

   if (!$default->owl_use_fs)
   {
      unlink($path);
   } 
   owl_syslog(FILE_VIEWED, $userid, flid_to_filename($id), $parent, "", "FILE");
   exit;
} 

// end of what Richard Bartz added to show PDF, DOC, and TXT special view
// cv change for security, should deny documents directory
// added image_show that passes the image through
if ($action != "image_show")
{
   include($default->owl_fs_root ."/lib/header.inc");
   include($default->owl_fs_root ."/lib/userheader.inc");
   print("<center>\n");
} 

if ($action == "image_show")
{
   if (check_auth($id, "file_download", $userid) == 1)
   {
      if ($default->owl_use_fs)
      {
         $path = $default->owl_FileDir . "/" . find_path($parent) . "/" . flid_to_filename($id);
         readfile("$path");
      } 
      else
      {
         $sql = new Owl_DB;
         $filename = flid_to_filename($id);
         if ($filetype = strrchr($filename, "."))
         {
            $filetype = substr($filetype, 1);
            $sql->query("SELECT * FROM $default->owl_mime_table WHERE filetype = '$filetype'");
            while ($sql->next_record()) $mimeType = $sql->f("mimetype");
         } 
         if ($mimeType)
         {
/* BETTER WAY TO DO THINGS MAYBE?

if (function_exists("imagegif")) {
   header("Content-type: image/gif");
   imagegif($im);
} elseif (function_exists("imagejpeg")) {
   header("Content-type: image/jpeg");
   imagejpeg($im, "", 0.5);
} elseif (function_exists("imagepng")) {
   header("Content-type: image/png");
   imagepng($im);
} elseif (function_exists("imagewbmp")) {
   header("Content-type: image/vnd.wap.wbmp");
   imagewbmp($im);
} else {
   die("No image support in this PHP server");
}

*/
            header("Content-Type: $mimeType");
            $sql->query("SELECT data,compressed FROM " . $default->owl_files_data_table . " WHERE id='$id'");
            while ($sql->next_record())
            {
               if ($sql->f("compressed"))
               {
                  $tmpfile = $default->owl_tmpdir . "/owltmp.$id";
                  if (file_exists($tmpfile)) unlink($tmpfile);
                  $fp = fopen($tmpfile, "wb");
                  fwrite($fp, $sql->f("data"));
                  fclose($fp);
                  flush(passthru($default->gzip_path . " -dfc $tmpfile"));
                  unlink($tmpfile);
               } 
               else
               {
                  print $sql->f("data");
               } 
            } 
         } 
      } 
   } 
   else
   {
      print($owl_lang->err_nofileaccess);
   } 
   die;
} 

if ($action == "file_details")
{
   if (check_auth($parent, "folder_view", $userid) == 1)
   {
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
     print("<br />\n");

      fPrintNavBar($parent);
      $sql = new Owl_DB;
      $sql->query("SELECT * FROM $default->owl_files_table WHERE id = '$id'");
      while ($sql->next_record())
      {
         $security = $sql->f("security");
         if ($security == "0") $security = $owl_lang->everyoneread;
         if ($security == "1") $security = $owl_lang->everyonewrite;
         if ($security == "2") $security = $owl_lang->groupread;
         if ($security == "3") $security = $owl_lang->groupwrite;
         if ($security == "4") $security = $owl_lang->onlyyou;
         if ($security == "5") $security = $owl_lang->groupwrite_nod;
         if ($security == "6") $security = $owl_lang->everyonewrite_nod;
         if ($security == "7") $security = $owl_lang->groupwrite_worldread;
         if ($security == "8") $security = $owl_lang->groupwrite_worldread_nod;

         $choped = split("\.", $sql->f("filename"));
         $pos = count($choped);
         $ext = strtolower($choped[$pos-1]);
         print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
         print("<tr>\n");
         print("<td align=\"left\" valign=\"top\">\n");
         print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
         print("<tr>\n");
         print("<td class=\"form1\">$owl_lang->title:</td>\n");
         print("<td class=\"form1\" width=\"100%\">". $sql->f("name") ." &nbsp;&nbsp;");

         // Tiian change 2003-07-31
         $pos = strpos(get_dirpath($sql->f("parent")), "$default->version_control_backup_dir_name");
         if (is_integer($pos) && $pos)
         {
             $is_backup_folder = true;
         }
         else
         {
             $is_backup_folder = false;
         }
         printFileIcons($sql->f("id"), $sql->f("filename"), $sql->f("checked_out"), $sql->f("url"), $default->owl_version_control, $ext, $sql->f("parent"),$is_backup_folder);

         print("</td>\n");
         print("</tr>\n");

         
         $link = $default->owl_notify_link . "browse.php?sess=0&parent=" . $parent . "&expand=1&fileid=" . $id;
         fPrintFormTextLine($owl_lang->notify_link . ":" , "", "",  $link , "", true);
         fPrintFormTextLine($owl_lang->file . ":" , "", "", $sql->f("filename"), gen_filesize($sql->f("f_size")), true );
         // if a MP3 tag was found Display the information
         $filepath = $default->owl_FileDir . "/" . get_dirpath($sql->f("parent")) . "/" . $sql->f("filename");
         if ($sql->f("url") != 1 && file_exists($filepath))
         {
            $id3 = new id3($filepath);

            if ($id3->id3v11 || $id3->id3v1 || $id3->id3v2)
            {
               $id3->study();
               print("<tr><td align=\"right\" valign=\"top\"><br />$owl_lang->disp_mp3<br /><br /></td>");
               print("<td align=\"left\">");
               print("<b>$id3->artists - $id3->name <br />");
               print("$id3->album <br />");
               print("$id3->bitrate kbps&nbsp;&nbsp;$id3->frequency Hz&nbsp;$id3->mode <br />");
               print("$id3->length<br />");
               print("$id3->genre<br />");
               print("$id3->comment</b>");
               print("</td></tr>");
            } 
         } 

         fPrintFormTextLine($owl_lang->ownership . ":" , "", "",  fid_to_creator($id) . "&nbsp;(" . group_to_name(owlfilegroup($id)) . ")" , "", true);
         if ($default->advanced_security == 0 )
         {
            fPrintFormTextLine($owl_lang->permissions . ":" , "", "",  $security , "", true);
         }
         fPrintFormTextLine($owl_lang->keywords . ":" , "", "",  $sql->f("metadata")  , "", true);
         $sql_custom = new Owl_db;
         $sql_custom_values = new Owl_db;
                                                                                                                                                                                               
         $sql_custom->query("SELECT * FROM $default->owl_docfields_table WHERE doc_type_id = '" . $sql->f("doctype") . "' order by field_position");
         $bPrintInitialHeading = true;

         $qFieldLabel = new Owl_DB;
         while ($sql_custom->next_record())
         {
            $sql_custom_values->query("SELECT  field_value FROM $default->owl_docfieldvalues_table WHERE file_id = '" . $sql->f("id") . "' AND field_name = '" . $sql_custom->f("field_name") ."'");
            $values_result = $sql_custom_values->next_record();
                                                                                                                                                                                            
            $qFieldLabel->query("SELECT field_label FROM $default->owl_docfieldslabel_table WHERE locale = '$language' AND doc_field_id='" . $sql_custom->f("id") . "'");
            $qFieldLabel->next_record();
                           if($bPrintInitialHeading)
                  {
                     if ($sql_custom->f("field_position") == 1 and $sql_custom->f("field_type") == "seperator")
                     {
                        print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"2\">" . $qFieldLabel->f("field_label") ."</td></tr>\n");
                     }
                     else
                     {
                        print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"2\">$owl_lang->doc_specific</td></tr>\n");
                     }
                     $bPrintInitialHeading = false;
                  }
                                                                                                                                                                                   
                        if ($sql_custom->f("required") == "1")
                        {
                           $required = "<font color=\"red\"><b>&nbsp;*&nbsp;</b></font>";
                        }
                        else
                        {
                           $required = "<font color=\"red\"><b>&nbsp;&nbsp;&nbsp;</b></font>";
                        }
                                                                                                                                                                                   
            switch ($sql_custom->f("field_type"))
               {
                  case "seperator":
                        if ($sql_custom->f("field_position") > 1)
                        {
                           print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"2\">" . $qFieldLabel->f("field_label") ."</td></tr>\n");
                        }
                     break;
                  case "checkbox":
                         if($sql_custom_values->f("field_value"))
                         {
                           $checked = "checked";
                         }
                         else
                         {
                            $checked = "";
                         }
                         fPrintFormCheckBox($qFieldLabel->f("field_label"). ": $required", $sql_custom->f("field_name"), $qFieldLabel->f("field_label"), $checked);
                       break;
                  case "mcheckbox":
                    $aMultipleCheckBoxLabel = split("\|",  $sql_custom->f("field_values"));
                    $aMultipleCheckBox = split(",",  $sql_custom_values->f("field_value"));
                    $i = 0;
                    $iNumberColumns  = $sql_custom->f("field_size");
                    print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"2\">" . $qFieldLabel->f("field_label") ."</td></tr>\n");
                    print("<tr>\n<td colspan=\"2\">\n<table class=\"form1\" width=\"100%\">\n");
                    foreach ($aMultipleCheckBox as $sValues)
                    {
                       if (!empty($sValues))
                       {
                          $checked = "checked=\"checked\"";
                       }
                       else
                       {
                          $checked = "";
                       }
                       $iColumnCount = $i % $iNumberColumns;
                       if ($iColumnCount == 0)
                       {
                         print("<tr>\n");
                       }
                       print("<td  class=\"form9\" width=\"1%\">");
                       print("<input class=\"fcheckbox1\" type=\"checkbox\" name=\"" . $sql_custom->f("field_name") . "_$i\" value=\"".$aMultipleCheckBoxLabel[$i]."\" $checked></input>");
                       print("</td>\n");

                       print("<td  class=\"form9\">");
                       print($aMultipleCheckBoxLabel[$i]);
                       print("</td>\n");
                       if ($iCoumnCount == ($iNumberColumns - 1))
                       {
                         print("</tr>\n");
                       }
                       $aMultipleCheckBox[$i]= $sValues;
                       $i++;
                    }
                    for ($c = 0; $c < $iNumberColumns - $iCoumnCount - 1; $c++)
                    {
                       print("<td  class=\"form9\">&nbsp;</td>\n");
                       print("<td  class=\"form9\">&nbsp;</td>\n");
                    }
                    print("</tr>\n</table>\n");
                    print("</td>\n</tr>\n");
                  break;
                  case "radio":
                        $aRadioButtons = array();

                        $aRadioButtons = split("\|",  $sql_custom->f("field_values"));
                        $i = 0;
                        foreach ($aRadioButtons as $sValues)
                        {
                           $aRadioButtonValues[$i]= $sValues;
                           $i++;
                        }
                        fPrintFormRadio($qFieldLabel->f("field_label") .": $required" , $sql_custom->f("field_name"), $sql_custom_values->f("field_value"), $aRadioButtonValues);
                     break;
                  default:
                        print("<tr><td align=\"right\" class=\"form1\">". $qFieldLabel->f("field_label") .":");
                        print($required);
                        print("</td><td align=\"left\" class=\"form1\">" . $sql_custom_values->f("field_value") ."</td></tr>");
                  break;
               }

         }
                                                                                                                                                                                            
         if ($sql_custom->num_rows($sql_custom) > 0)
         {
            print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"2\">&nbsp;</td></tr>\n");
         }

         fPrintFormTextArea($owl_lang->description. ":", "description", $sql->f("description"));

      print("</table>\n");
      print("</td></tr></table>\n");
      fPrintButtonSpace(12, 1);
      if ($default->show_prefs == 2 or $default->show_prefs == 3)
      {
         fPrintPrefs("infobar2");
      }
      print("</td></tr></table>\n");
      include($default->owl_fs_root ."/lib/footer.inc");

      } 
   } 
} 

if ($action == "image_preview")
{
   if (check_auth($id, "file_download", $userid) == 1)
   {
      owl_syslog(FILE_VIEWED, $userid, flid_to_filename($id), $parent, "", "FILE");
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
        fPrintPrefs();
     }

     fPrintButtonSpace(12, 1);

      if ($default->owl_use_fs)
      {
         $fid = fGetPhysicalFileId($id);
         $path = $default->owl_FileDir . "/" . find_path(owlfileparent($fid)) . "/" . flid_to_filename($fid);
      }
      else
      {
         $path = fGetFileFromDatbase($id);
      }

     fPrintNavBar($parent, $owl_lang->viewing . ":&nbsp;", $id);

     $fid = fGetPhysicalFileId($id);
     $sImagePreviewLocation = $default->thumbnails_location . "/" . $default->owl_current_db ."_". $fid ."_". flid_to_filename($fid);
 
     copy($path, $sImagePreviewLocation);
     print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
     print("<tr>\n");
     print("<td align=\"left\" valign=\"top\">\n");
     print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
     print('<tr>');
     print("<td align=\"left\">");
     print('<p align="center">');
     print("<img src=\"" . $default->thumbnails_url ."/" . $default->owl_current_db ."_". $fid ."_". flid_to_filename($fid) ."\" alt=\"\"></img></p>");
     print("</td></tr></table>");
     print("</td></tr></table>");
     fPrintButtonSpace(12, 1);
     if ($default->show_prefs == 2 or $default->show_prefs == 3)
     {
        fPrintPrefs("infobar2");
     }
     print("</td></tr></table>");
     include($default->owl_fs_root ."/lib/footer.inc");
     //sleep(1); 
     //unlink($sImagePreviewLocation);
   } 
   else
   {
      printError($owl_lang->err_nofileaccess);
   } 
} 

if ($action == "zip_preview")
{
   if (check_auth($id, "file_download", $userid) == 1)
   {
      owl_syslog(FILE_VIEWED, $userid, flid_to_filename($id), $parent, "", "FILE");
      $name = flid_to_filename($id);

      if ($default->owl_use_fs)
      {
         $path = find_path($parent) . "/" . $name;
      } 
      else
      {
         $path = $name;
         if (file_exists($default->owl_FileDir . "/$path")) unlink($default->owl_FileDir . "/$path");
         $file = fopen($default->owl_FileDir . "/$path", 'wb');
         $sql->query("SELECT data,compressed FROM $default->owl_files_data_table WHERE id='$id'");
         while ($sql->next_record())
         {
            if ($sql->f("compressed"))
            {
               $tmpfile = $default->owl_tmpdir . "/owltmp.$id.gz";
               $uncomptmpfile = $default->owl_tmpdir . "/owltmp.$id";
               if (file_exists($tmpfile)) unlink($tmpfile);

               $fp = fopen($tmpfile, "wb");
               fwrite($fp, $sql->f("data"));
               fclose($fp);

               system($default->gzip_path . " -df $tmpfile");

               $fsize = filesize($uncomptmpfile);
               $fd = fopen($uncomptmpfile, 'rb');
               $filedata = fread($fd, $fsize);
               fclose($fd);

               fwrite($file, $filedata);
               unlink($uncomptmpfile);
            } 
            else
            {
               fwrite($file, $sql->f("data"));
            } 
            fclose($file);
         } 
      } 
   

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
         fPrintPrefs();
      }

      fPrintButtonSpace(12, 1);


      fPrintNavBar($parent, $owl_lang->viewing . ":&nbsp;", $id);

      print("<table><tr><td align=\"left\"><pre>");

      if ($filext == "tar")
      {
         $expr = "-tvf ";
         $unzipbin = "$default->tar_path $expr " . "\"" . "./" . $path . "\" ";
         if (substr(php_uname(), 0, 7) != "Windows")
         {
            $unzipbin .= " 2>&1";
         } 
         passthru("$unzipbin");
      } 
      else if (($filext == "tar.gz") || ($filext == "tgz"))
      {
         $expr = "-tz ";
         $unzipbin = "$default->tar_path $expr  < " . "\"" . "./" . $path . "\" ";
         if (substr(php_uname(), 0, 7) != "Windows")
         {
            $unzipbin .= " 2>&1";
         } 
         passthru("$unzipbin");
      } elseif ($filext == "gz")
      {
         $expr = "-lt";
         $unzipbin = "$default->gzip_path $expr " . "\"" . "./" . $path . "\" ";
         if (substr(php_uname(), 0, 7) != "Windows")
         {
            $unzipbin .= " 2>&1";
         } 
         passthru("$unzipbin");
      } 
      else if ($filext == "zip")
      {
         $expr = "-l";
         $unzipbin = "$default->unzip_path $expr " . "\"" .  $default->owl_FileDir  . "/" . $path . "\" ";
         if (substr(php_uname(), 0, 7) != "Windows")
         {
            $unzipbin .= " 2>&1";
         } 
         passthru("$unzipbin");
      } 
      else
      {
         exit();
      }

      if (!$default->owl_use_fs)
      {
         unlink($default->owl_FileDir . "/$path");
      } 
      print("</pre></td></tr></table>");

      fPrintButtonSpace(12, 1);

      if ($default->show_prefs == 2 or $default->show_prefs == 3)
      {
         fPrintPrefs("infobar2");
      }
      print("</td></tr></table>\n");
      include($default->owl_fs_root ."/lib/footer.inc");
   } 
   else
   {
      print($owl_lang->err_nofileaccess);
   } 
} 
// BEGIN wes change
if ($action == "html_show" || $action == "text_show" || $action == "note_show" || $action == "pod_show" || $action == "php_show" or $action == "email_show" or $action == "diff_show")
{


   if (check_auth($id, "file_download", $userid) == 1)
   {
      owl_syslog(FILE_VIEWED, $userid, flid_to_filename($id), $parent, "", "FILE");
      if ($default->owl_use_fs)
      {
         $fid = fGetPhysicalFileId($id);
         $path = $default->owl_FileDir . "/" . find_path(owlfileparent($fid)) . "/" . flid_to_filename($fid);
         //$path = $default->owl_FileDir . "/" . find_path($parent) . "/" . flid_to_filename($id);
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
            fPrintPrefs();
         }

         fPrintButtonSpace(12, 1);
         print("<br />\n");

         fPrintNavBar($parent, $owl_lang->viewing . ":&nbsp;", $id);
         print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
         print("<tr>\n");
         print("<td align=\"left\" valign=\"top\">\n");
         print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
         print('<tr>');
         print("<td align=\"left\" colspan=\"2\">");
         print('<p align="left">');
         if ($action == "text_show" or $action == "note_show" or $action == "html_show") 
         {
            //print("<table><tr><td style:white-space normal;>"); 
            print("<table><tr><td>"); 
         }

         if ($action == "pod_show")
         {
            if (file_exists($default->pod2html_path))
            {
               $sOwltmpview = $default->owl_tmpdir . "/owltmpview.$id.$sess";
               $mystring = system("$default->pod2html_path --cachedir=$default->owl_tmpdir --infile=$path --outfile=$sOwltmpview");
               readfile("$sOwltmpview"); 
               myDelete($sOwltmpview); 
            }
            else 
            {
               print("<H2>$owl_lang->err_pod2html_not_found $default->pod2html_path</H2>");
            }
         }
         elseif ($action == "php_show")
         {
               highlight_file($path); 
         }
         elseif ($action == "diff_show")
         {
            include_once($default->owl_fs_root ."/lib/Text/Diff.php");;
            include_once($default->owl_fs_root ."/lib/Text/Diff/Renderer.php");
            include_once($default->owl_fs_root ."/lib/Text/Diff/Renderer/unified.php");
           
            $sFromFile = flid_to_filename($diff_from);
            $sToFile = flid_to_filename($diff_to);
 
            print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
            print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"20\">Difference between $sFromFile and $sToFile</td></tr>\n");
            print("</table>");
            
            $from_backup = "/$default->version_control_backup_dir_name/";
            $to_backup = "/$default->version_control_backup_dir_name/";
            if ($diff_from == $id)
            {
               $from_backup = "/";
            }
            if ($diff_to == $id)
            {
               $to_backup = "/";
            }
            $lines1 = file($default->owl_FileDir . "/" . find_path($parent) . $from_backup . $sFromFile);
            $lines2 = file($default->owl_FileDir . "/" . find_path($parent) . $to_backup . $sToFile);
                                                                                                                                                                                    
            $diff = new Text_Diff($lines1, $lines2);
            $renderer = &new Text_Diff_Renderer_unified();
            print("<pre>");
            echo $renderer->render($diff);
            print("</pre>");
         }
         elseif ($action == "email_show")
         {

    	    $input = implode('', file($path));

	    $params['include_bodies'] = true;
	    $params['decode_bodies']  = true;
	    $params['decode_headers'] = true;

	    $decoder = new Mail_mimeDecode($input);
	    $structure = $decoder->decode($params);
            //print_r($structure);
            //exit;
	    $from = $structure->headers[from];
	    $to = $structure->headers[to];
	    $cc = $structure->headers[cc];
	    $date = $structure->headers[date];
	    $subject = $structure->headers[subject];
	    $message = $structure->parts[0]->body;

            print("<b>To:&nbsp;</b>" .htmlentities($to). "<br />");
            print("<b>From:&nbsp;</b>" .htmlentities($from). " <br />");
            if (!empty($cc))
            {
               print("<b>CC:&nbsp;</b>" .htmlentities($cc). " <br />");
            }
            print("<b>Date:&nbsp;</b>" .htmlentities($date). " <br />");
            print("<b>Subject:&nbsp;</b>" .htmlentities($subject). " <br />");
            print("<br /><b>Message:</b><br />");
	    $multipart = strpos($structure->headers['content-type'], 'ultipart');

	    if ( $multipart == 1 ) 
            {
            //************************************************
            //* Multipart message
            //****
		foreach ($structure->parts as $part) {
			if ($part->ctype_primary == 'text') {
				if ($part->ctype_secondary == 'plain') {
					print("<xmp>".$part->body."</xmp>");
				}
				elseif ($part->ctype_secondary == 'html') {
					print("<br />______________________________<br /><b>HTML-Message:</b><br /><br />");
					print(strip_tags($part->body,'<p><br><br />'));
				}
			}
			if (isset($part->disposition))
				print("<br />______________________________<br /><b>Attachments:</b><br />");
                           $tmpfilename = $part->d_parameters['filename'];
      $choped = split("\.", $tmpfilename);
      $pos = count($choped);
      if ( $pos > 1 )
      {
         $ext = strtolower($choped[$pos-1]);
         $sDispIcon = $ext . ".gif";
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

         if (!file_exists("$default->owl_fs_root/graphics/$default->sButtonStyle/icon_filetype/$sDispIcon"))
         {
            $sDispIcon = "file.gif";
         }

				if ($part->disposition=='inline') {
					$relpath = $default->owl_fs_root . "/Attachments";
					$savefile = $relpath."/".$id."_inline-file_".$tmpfilename;
					if (file_exists($savefile)) unlink($savefile);
					$fp = fopen($savefile, "wb");
					fwrite($fp, $part->body);
					fclose($fp);
					print('<br />inline object: ');
					print('&nbsp;<a class="lfile1" href='.$default->owl_root_url.'/Attachments/'.$id."_inline-file_".$tmpfilename.' target=_new>'.$part->d_parameters['filename']."</a><br />");
				}
				elseif ($part->disposition=='attachment') {
					$relpath = $default->owl_fs_root . "/Attachments";
					$savefile = $relpath."/".$id."_attachment_".$tmpfilename;
					if (file_exists($savefile)) unlink($savefile);
					$fp = fopen($savefile, "wb");
					fwrite($fp, $part->body);
					fclose($fp);
					print('<br />attachment: ');
                                        print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/$sDispIcon\" border=\"0\" alt=\"\"></img>");
					print('&nbsp;<a class="lfile1" href='.$default->owl_root_url.'/Attachments/'.$id."_attachment_".$tmpfilename.' target=_new>'.$part->d_parameters['filename']."</a><br />");
				}
			}
	    } else {
            //************************************************
            //* Singlepart message
         //****
	    	if ($structure->ctype_primary == 'text') {
			if ($structure->ctype_secondary == 'plain') {
				print("<xmp>".$structure->body."</xmp>");
			}
			elseif ($structure->ctype_secondary == 'html') {
				print(strip_tags($structure->body,'<p><br />'));
			} else {
				print("Not supported secondary content type to text: ".$structure->ctype_secondary."<br>");
			}
	   	 }
	    }
         }
         else
         {
            //exit("File is HERE: $path");
            //$fileContent = readfile("$path");
            //echo wordwrap(file_get_contents("$path"), 80, "<br /><br />\n"); 
            echo nl2br(htmlentities(file_get_contents("$path"))); 
         }
      } 
      else
      {
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
            fPrintPrefs();
         }

         fPrintButtonSpace(12, 1);
         print("<br />\n");

         fPrintNavBar($parent, $owl_lang->viewing . ":&nbsp;", $id);
         print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
         print("<tr>\n");
         print("<td align=\"left\" valign=\"top\">\n");
         print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
         print('<tr>');
         print("<td align=\"left\">");
         print('<p align="left">');

         if ($action == "text_show" or $action == "note_show" or $action == "html_show") 
         {
            print("<xmp>");
         }

         $sql->query("SELECT data,compressed FROM " . $default->owl_files_data_table . " WHERE id='$id'");

         while ($sql->next_record())
         {
            if ($sql->f("compressed"))
            {
                  print("<xmp>");
                  $tmpfile = $default->owl_tmpdir . "/owltmp.$id.$sess";
                  if (file_exists($tmpfile)) unlink($tmpfile);
                  $fp = fopen($tmpfile, "wb");
                  fwrite($fp, $sql->f("data"));
                  fclose($fp);
                  flush(stripslashes(passthru($default->gzip_path . " -dfc $tmpfile")));
                  print("</xmp>");
                  unlink($tmpfile);
            } 
            else
            {
               if ($action == "php_show")
               {
                     $sOwltmpview = $default->owl_tmpdir . "/owltmpview.$id.$sess";
                     $tmpfile = $default->owl_tmpdir . "/owltmpview2.$id.$sess";
                     if (file_exists($tmpfile)) unlink($tmpfile);
                     $fp = fopen($tmpfile, "wb");
                     fwrite($fp, stripslashes($sql->f("data")));
                     fclose($fp);
                     highlight_file($tmpfile); 
                     myDelete($tmpfile);
               }
               else
               {
                  print stripslashes($sql->f("data"));
               }
            } 
         } 
      } 

      if ($action == "text_show" or $action == "note_show" or $action == "html_show") 
      {
 
      print("</td></tr></table>");
      print("</td>");
      print("</tr>");
      $urlArgs2 = $urlArgs;
      $urlArgs2['fileid']     = $id;
      $url = fGetURL ('view.php', $urlArgs2);


      print("<tr>\n");
      print("<td class=\"form1\" width=\"100%\">");
      fPrintButtonSpace(0, 1);
      print("</td>\n");
      print("<td class=\"form2\">");
      print("<table>\n<tr>\n");
      print("<td class=\"button1\">");
      print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/pdf.gif\"></img>");
      print("</td>");
      print("<td class=\"button1\">");
      print("<a class=\"lbutton1\" href=\"$url\" title=\"$owl_lang->alt_gen_pdf\">&nbsp;" . $owl_lang->btn_gen_pdf . "&nbsp;</a>");
      print("</td>\n");
      print("</tr>\n");
      print("</table>\n");
      print fGetHiddenFields ($urlArgs2);
      print("</td>\n");
      print("</form>");
      print("</tr>\n");

      }

      print('</td>');
      print('</tr>');
      print('</table>');
      $path = find_path($parent) . "/" . flid_to_filename($id);

      print("</td></tr></table>");

      fPrintButtonSpace(12, 1);

       if ($default->show_prefs == 2 or $default->show_prefs == 3)
       {
          fPrintPrefs("infobar2");
       }
       print("</td></tr></table>\n");
       include($default->owl_fs_root ."/lib/footer.inc");
   } 
   else
   {
      print($owl_lang->err_nofileaccess);
   } 
} 
?>
