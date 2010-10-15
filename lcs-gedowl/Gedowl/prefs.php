<?php

/**
 * prefs.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 * $Id: prefs.php,v 1.7 2007/08/12 12:05:40 b0zz Exp $
 */

require_once(dirname(__FILE__)."/config/owl.php");
require_once($default->owl_fs_root ."/lib/disp.lib.php");
require_once($default->owl_fs_root ."/lib/owl.lib.php");
require_once($default->owl_fs_root ."/lib/security.lib.php");
include_once($default->owl_fs_root ."/lib/header.inc");
include_once($default->owl_fs_root ."/lib/userheader.inc");

if ($sess == "0" && $default->anon_ro > 0)
{
   printError($owl_lang->err_login);
}


   $urlArgs = array();
   $urlArgs['sess']      = $sess;
   $urlArgs['parent']    = $parent;
   $urlArgs['expand']    = $expand;
   $urlArgs['order']     = $order;
   $urlArgs['curview']     = $curview;
   $urlArgs[$sortorder] = $sortname;

if(empty($expand))
{
   $expand = $default->expand;
}

print("<center>");
 
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

if (!$action) 
{
   $action = "users";
}

function printuser($id)
{
   global $order, $sortname, $sort;
   global $sess, $change, $default, $expand, $parent, $userid;
   global $owl_lang, $urlArgs;

   $showuser = false;
   
   if (prefaccess($userid) and $userid == $id)
   {
      $showuser = true;
   }
   if ($default->show_user_info == 0 and $showuser == false)
   {
      printError("$owl_lang->err_general");
   }

   if (isset($change))
   {
       fPrintSectionHeader($owl_lang->saved, "admin3");
   }

   $sql = new Owl_DB;
   $sql->query("SELECT id,name FROM $default->owl_groups_table");
   $i = 0;
   while ($sql->next_record())
   {
      $groups[$i][0] = $sql->f("id");
      $groups[$i][1] = $sql->f("name");
      $i++;
   } 

   $sql->query("SELECT * FROM $default->owl_users_table where id = '$id'");

   while ($sql->next_record())
   {
      $urlArgs2 = $urlArgs;
      $urlArgs2['id'] = $sql->f("id");
      $urlArgs2['action'] = 'user';
      $urlArgs2['sortname'] = $sortname;

      print("<form enctype=\"multipart/form-data\" action=\"dbmodify.php\" method=\"post\">\n");
      print fGetHiddenFields ($urlArgs2);
      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"20\">$owl_lang->preference</td></tr>\n");
      print("<tr>\n");
      print("<td align=\"left\" valign=\"top\">\n");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");


     
      // 
      // Show the Title
      // 
      if ($showuser)
      {
         fPrintFormTextLine($owl_lang->title . ":" , "name", "",  $sql->f("name"));
      } 
      else
      {
         fPrintFormTextLine($owl_lang->title . ":" , "", "",  $sql->f("name") , "", true);
      } 
      // 
      // Display the Primary group
      // and groups the user is a member
      // of
      // 
      fPrintFormTextLine($owl_lang->group . ":" , "", "",  group_to_name($sql->f("groupid"))  , "", true);

      $sqlmemgroup = new Owl_DB;
      $sqlmemgroup->query("SELECT * FROM $default->owl_users_grpmem_table where groupid is not NULL and userid = '" . $sql->f("id") . "'");
      if ($sqlmemgroup->num_rows() > 0)
      {
         $sqlmemgroup->next_record();
         fPrintFormTextLine($owl_lang->groupmember . ":" , "", "",  group_to_name($sqlmemgroup->f("groupid"))  , "", true);
         while ($sqlmemgroup->next_record())
         {
            fPrintFormTextLine("", "", "",  group_to_name($sqlmemgroup->f("groupid"))  , "", true);
         } 
      } 
      else
      {
         fPrintFormTextLine($owl_lang->groupmember . ":" , "", "",  $owl_lang->not_member , "", true);
      } 
      // 
      // Display the Language dropdown
      // 
      if ($showuser)
      {
         print("<tr>\n");
         print("<td class=\"form1\">$owl_lang->userlang:</td>\n");
         print("<td class=\"form1\" width=\"100%\">");
         print("<select class=\"fpull1\" name=\"newlanguage\" size=\"1\">\n");

         $aLanguages = fGetLocales();
         foreach ($aLanguages as $file)
         {
            print("<option value=\"$file\"");
               if ($file == $sql->f("language"))
               {
                  print (" selected=\"selected\"");
               }
               print(">$file</option>\n");

         }
         print("</select>\n</td>\n</tr>\n");
      } 
      else
      {
         fPrintFormTextLine($owl_lang->userlang, "", "",  $sql->f("language") , "", true);
      } 

      print("<tr>\n");
      print("<td class=\"form1\">$owl_lang->buttonstyle:</td>\n");
      print("<td class=\"form1\" width=\"100%\">");
      print("<select class=\"fpull1\" name=\"newbuttons\" size=\"1\">");
      $dir = dir($default->owl_fs_root . "/graphics");
      $dir->rewind();

      while ($file = $dir->read())
      {
         if ($file != "." and $file != ".." and $file != "CVS" and $file != "favicon.ico")
         {
            print("<option value=\"$file\"");
            if ($file == $sql->f("buttonstyle"))
            {
               print (" selected=\"selected\"");
            }
            print(">$file</option>\n");
         }
      }
      $dir->close();
      print("</select></td></tr>");

      // 
      // Display the Password
      // change input text
      // 
      if ($showuser)
      {
         if ($sql->f("user_auth") == 0 )
         {
            fPrintFormTextLine($owl_lang->oldpassword . ":" , "oldpassword", "", "", "", false, "password");
            fPrintFormTextLine($owl_lang->newpassword . ":" , "newpassword", "", "", "", false, "password");
            fPrintFormTextLine($owl_lang->confpassword . ":" , "confpassword", "", "", "", false, "password");
         }
      } 
      // 
      // Display the Email
      // 
      if ($showuser)
      {
         fPrintFormTextLine($owl_lang->email . ":" , "email", "40", $sql->f("email"));
      } 
      else
      {
         fPrintFormTextLine($owl_lang->email . ":" , "", "",  $sql->f("email") , "", true);
      } 

      if ($showuser)
      {
         if ($sql->f("notify") == 1)
         {
            fPrintFormCheckBox($owl_lang->notification, "notify", "1", "checked");
         }
         else
         {
            fPrintFormCheckBox($owl_lang->notification, "notify", "1");
         }
         if ($sql->f("attachfile") == 1)
         {
            fPrintFormCheckBox($owl_lang->attach_file, "attachfile", "1", "checked");
         }
         else
         {
            fPrintFormCheckBox($owl_lang->attach_file, "attachfile", "1");
         }
         if ($sql->f("logintonewrec") == 1)
         {
            fPrintFormCheckBox($owl_lang->user_login_to_newrecords, "logintonewrec", "1", "checked");
         }
         else
         {
            fPrintFormCheckBox($owl_lang->user_login_to_newrecords, "logintonewrec", "1");
         }
         if ($sql->f("comment_notify") == 1)
         {
            fPrintFormCheckBox($owl_lang->comment_notif, "comment_notify", "1", "checked");
         }
         else
         {
            fPrintFormCheckBox($owl_lang->comment_notif, "comment_notify", "1");
         }
         if ($sql->f("useradmin") == 1)
         {
            fPrintFormTextLine("$owl_lang->useradmin:" , "", "",  $owl_lang->status_yes , "", true);
         }
         else
         {
            fPrintFormTextLine("$owl_lang->useradmin:" , "", "",  $owl_lang->status_no , "", true);
         }
         if ($sql->f("newsadmin") == 1)
         {
            fPrintFormTextLine($owl_lang->newsadmin . ":" , "", "",  $owl_lang->status_yes , "", true);
         }
         else
         {
            fPrintFormTextLine($owl_lang->newsadmin . ":" , "", "",  $owl_lang->status_no , "", true);
         }
         if ($sql->f("groupadmin") == 1)
         {
            fPrintFormTextLine($owl_lang->user_group_admin , "", "",  $owl_lang->status_yes , "", true);
         }
         else
         {
            fPrintFormTextLine($owl_lang->user_group_admin , "", "",  $owl_lang->status_no , "", true);
         }
      } 
      else
      {
         if ($sql->f("notify") == 1)
         {
            fPrintFormTextLine($owl_lang->notification, "", "",  $owl_lang->status_yes , "", true);
         }
         else
         {
            fPrintFormTextLine($owl_lang->notification, "", "",  $owl_lang->status_no , "", true);
         }
         if ($sql->f("attachfile") == 1)
         {
            fPrintFormTextLine($owl_lang->attach_file, "", "",  $owl_lang->status_yes , "", true);
         }
         else
         {
            fPrintFormTextLine($owl_lang->attach_file, "", "",  $owl_lang->status_no , "", true);
         }
         if ($sql->f("logintonewrec") == 1)
         {
            fPrintFormTextLine($owl_lang->user_login_to_newrecords, "", "", $owl_lang->status_yes, "", true);
         }
         else
         {
            fPrintFormTextLine($owl_lang->user_login_to_newrecords, "", "", $owl_lang->status_no, "", true);
         }

         if ($sql->f("comment_notify") == 1)
         {
            fPrintFormTextLine($owl_lang->comment_notif, "", "",  $owl_lang->status_yes , "", true);
         }
         else
         {
            fPrintFormTextLine($owl_lang->comment_notif, "", "",  $owl_lang->status_no , "", true);
         }
         if ($sql->f("useradmin") == 1)
         {
            fPrintFormTextLine("$owl_lang->useradmin:" , "", "",  $owl_lang->status_yes , "", true);
         }
         else
         {
            fPrintFormTextLine("$owl_lang->useradmin:" , "", "",  $owl_lang->status_no , "", true);
         }
         if ($sql->f("newsadmin") == 1)
         {
            fPrintFormTextLine($owl_lang->newsadmin . ":" , "", "",  $owl_lang->status_yes , "", true);
         }
         else
         {
            fPrintFormTextLine($owl_lang->newsadmin . ":" , "", "",  $owl_lang->status_no , "", true);
         }
         if ($sql->f("groupadmin") == 1)
         {
            fPrintFormTextLine($owl_lang->user_group_admin , "", "",  $owl_lang->status_yes , "", true);
         }
         else
         {
            fPrintFormTextLine($owl_lang->user_group_admin , "", "",  $owl_lang->status_no , "", true);
         }
      } 
      if ($showuser)
      {
         print("<tr>");
         print("<td class=\"form1\">");
         fPrintButtonSpace(1, 1);
         print("</td>");
         print("<td class=\"form2\" width=\"100%\">");
         fPrintSubmitButton($owl_lang->change, $owl_lang->alt_change);
         fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
         print("</td>");
         print("</tr>");
      }
      print("</table>\n");
      print("</td></tr></table>\n");
      print("</form>\n");
   } 
} 

if ($action)
{
   if (isset($owluser)) 
   { 
      printuser($owluser);
   }
} 
else
{
   printError("$owl_lang->err_general");
} 

fPrintButtonSpace(12, 1);

if ($default->show_prefs == 2 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar2");
}
print("</td></tr></table>\n");
include($default->owl_fs_root ."/lib/footer.inc");
?>
