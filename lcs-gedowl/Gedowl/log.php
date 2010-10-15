<?php
/**
 * log.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 * $Id: log.php,v 1.12 2007/10/29 15:02:01 b0zz Exp $
 */

require_once(dirname(__FILE__)."/config/owl.php");
require_once($default->owl_fs_root ."/lib/disp.lib.php");
require_once($default->owl_fs_root ."/lib/owl.lib.php");
require_once($default->owl_fs_root ."/lib/security.lib.php");
include_once($default->owl_fs_root ."/lib/header.inc");
include_once($default->owl_fs_root ."/lib/userheader.inc");

// store file name and extension separately

//$filename = unserialize(stripslashes(stripslashes($filename)));
$filename = ereg_replace("<amp>","&", $filename);

$aFirstpExtension = fFindFileFirstpartExtension ($filename);
$firstpart = $aFirstpExtension[0];
$file_extension = $aFirstpExtension[1];
$haveextension = $aFirstpExtension[2];


// V4B RNG Start
$urlArgs = array();
$urlArgs['sess']      = $sess;
$urlArgs['parent']    = $parent;
$urlArgs['expand']    = $expand;
$urlArgs['order']     = $order;
$urlArgs['sortorder'] = $sortorder;
$urlArgs['curview']     = $curview;
// V4B RNG End

// END 496814 Column Sorts are not persistant
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
print("<br />\n");
fPrintNavBar($parent, $owl_lang->viewlog . ":&nbsp;", $id);

print("<form enctype=\"multipart/form-data\" action=\"view.php\" method=\"post\">\n");
$urlArgs2 = $urlArgs;
$urlArgs2['action'] = 'diff_show';
$urlArgs2['expand'] = $expand;
$urlArgs2['id'] = $id;
print fGetHiddenFields ($urlArgs2);

print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
print("<tr>\n");
print("<td align=\"left\" valign=\"top\">\n");
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");

print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"20\">$filename</td></tr>\n");
print("<tr><td class=\"title1\">$owl_lang->ver</td>\n");
print("<td class=\"title1\">$owl_lang->user</td>\n");
if (!empty ($default->edit_text_files_inline))
{
   $edit_inline = $default->edit_text_files_inline;
   $ext = fFindFileExtension(flid_to_filename($id));
   if ($ext != "" && preg_grep("/\b$ext\b/", $edit_inline))
   {
      print("<td class=\"title1\">$owl_lang->diff_from</td>\n");
      print("<td class=\"title1\">$owl_lang->diff_to</td>\n");
   }
}
print("<td class=\"title1\">$owl_lang->alt_log_file</td>\n");
if ( $default->document_peer_review == 1)
{
   print("\t<td class=\"title1\" align=\"center\">$owl_lang->peer_satus</td>");
}

print("<td class=\"title1\">$owl_lang->modified</td>\n");
print("<td class=\"title1\">$owl_lang->last_modified</td></tr>");

$sql = new Owl_DB; 

// SPECIFIC SQL LOG QUERY -  NOT USED (problematic)
// This SQL log query is designed for repository assuming there is only 1
// digit in major revision, and noone decides to have a "_x-" in their
// filename.

// Has to be changed if the naming structure changes.
// Also a problem that it didn't catch the "current"
// file because of the "_x-" matching (grr)

// $sql->query("select * from $default->owl_files_table where filename LIKE '$filesearch[0]\__-%$filesearch[1]' order by major_revision desc, minor_revision desc");
// GENERIC SQL LOG QUERY - currently used.
// prone to errors when people name a set of docs
// Blah.doc
// Blah_errors.doc
// Blah_standards.doc
// etc. and search for a log on Blah.doc (it brings up all 3 docs)
// $sql->query("select * from $default->owl_files_table where filename LIKE '$filesearch[0]%$filesearch[1]' order by major_revision desc, minor_revision desc");
// $SQL = "select * from $default->owl_files_table where filename LIKE '$filesearch[0]%$filesearch[1]' order by major_revision desc, minor_revision desc";
// Fair portable way ? Filter "Blah_errors.doc" out il the while loop
if ($default->owl_use_fs)
{
   $sql->query("SELECT id FROM $default->owl_folders_table WHERE name='$default->version_control_backup_dir_name' and parent='$parent'");
   if ($sql->num_rows($sql) != 0)
   {
      while ($sql->next_record())
      {
         $backup_parent = $sql->f("id");
      } 
   } 
   else
   {
      $backup_parent = $parent;
   } 
   $sql->query("SELECT * FROM $default->owl_files_table WHERE (filename LIKE '" . $firstpart . "\\_%" . $file_extension . "' AND parent='$backup_parent') OR (filename = '$filename' AND parent = '$parent') ORDER BY major_revision desc, minor_revision desc");
} 
else
{
   // name based query -- assuming that the given name for the file doesn't change...
   // at some point, we should really look into creating a "revision_id" field so that all revisions can be linked.
   // in the meanwhile, the code for changing the Title of the file has been altered to go back and
   $name = flid_to_name($id);
   $sQuery = "select * from $default->owl_files_table where name='$name' AND parent='$parent' order by major_revision desc, minor_revision desc";

   //print("DEBUG: $sQuery");

   $sql->query($sQuery);
} 

$CountLines = 0;
while ($sql->next_record())
{
   $filename = $sql->f("filename");
   $major_revision = $sql->f("major_revision");
   $minor_revision = $sql->f("minor_revision");
   $choped = split("\.", $filename);
   $pos = count($choped);
   $ext = strtolower($choped[$pos-1]);

   if (($filename == $firstpart.".".$file_extension) ||
       ($filename == $firstpart."_".$major_revision."-".$minor_revision.".".$file_extension)) {

     if ($default->owl_use_fs )
       {
	 $sFilePattern =  preg_quote($firstpart) .  "\_[0-9]*\-[0-9]*$haveextension" . preg_quote($file_extension);
	 
	 if(!ereg("$sFilePattern", $sql->f("filename")) and  $id != $sql->f("id"))
	   {
	     continue;
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

     print("<tr><td class=\"$sTrClass\" valign=\"top\">" . $sql->f("major_revision") . "." . $sql->f("minor_revision") . "</td>
               <td class=\"$sTrClass\" valign=\"top\">" . uid_to_name($sql->f("creatorid")) . "</td>");
     if (!empty ($default->edit_text_files_inline))
       {
	 $edit_inline =$default->edit_text_files_inline;
	 if ($ext != "" && preg_grep("/\b$ext\b/", $edit_inline))
	   {
	     print("<td class=\"$sTrClass\" valign=\"top\"><input type=\"radio\" name=\"diff_from\" value=\"" . $sql->f("id") ."\"></input></td>
                <td class=\"$sTrClass\" valign=\"top\"><input type=\"radio\" name=\"diff_to\" value=\"" . $sql->f("id") ."\"></input></td>");
	   }
       }
     print("<td class=\"$sTrClass\" valign=\"top\" align=\"left\" width=\"70%\"><font size=\"2\" style=\"font-weight:bold\">");
     

     if ($sql->f("parent") == $parent)
       {
	 $is_backup_folder = false;
       }
     else
       {
	 $is_backup_folder = true;
       }
     
     printFileIcons($sql->f("id"), $sql->f("filename"), $sql->f("checked_out"), $sql->f("url"), $default->owl_version_control, $ext, $sql->f("parent"), $is_backup_folder);
     
     
     
     print("&nbsp;&nbsp;[ " . $sql->f("filename") . " ]</font><br />" . fCleanDomTTContent($sql->f("description"), 0) . "</td>");
     if ( $default->document_peer_review == 1)
       {
	 switch ($sql->f("approved"))
	   {
	   case "0":
	     $sStatus = "<div class=\"cpending\">$owl_lang->peer_satus_pending</div>";
	     break;
	   case 1:
	     $sStatus = "<div class=\"capproved\">$owl_lang->peer_satus_approved</div>";
	     break;
	   default:
	     $sStatus = "<div class=\"cpending\">$owl_lang->peer_satus_rejected</div>";
	     break;
	   }
         print("\t<td class=\"$sTrClass\" valign=\"top\" align=\"center\">$sStatus</td>");
       }

     print("<td class=\"$sTrClass\" valign=\"top\">" . date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset) . "</td>
               <td class=\"$sTrClass\" valign=\"top\">" . uid_to_name($sql->f("updatorid")) ."</td></tr>");
   }
}  
if (!empty ($default->edit_text_files_inline))
{
   $edit_inline =$default->edit_text_files_inline;
   if ($ext != "" && preg_grep("/\b$ext\b/", $edit_inline))
   {
      print("<tr>\n");
      print("<td class=\"form1\" width =\"100%\">");
      fPrintButtonSpace(1, 1);
      print("</td>\n");
      print("<td colspan=\"7\" class=\"form2\">");
      fPrintSubmitButton("Diff", "Show the differences between the selected files", "submit");
      print("</td>\n");
      print("</tr>\n");
   }
}
print("</table>");
print("</td></tr></table>\n");
print("</form>");
fPrintButtonSpace(12, 1);

if ($default->show_prefs == 2 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar2");  
}
print("</td></tr></table>\n");
include($default->owl_fs_root . "/lib/footer.inc");
?>
