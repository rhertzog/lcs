<?php

/**
 * admin_dbmodify.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2003 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 * $Id: admin_dbmodify.php,v 1.28 2007/08/20 20:00:43 b0zz Exp $
 */

ob_start();
require_once(dirname(dirname(__FILE__)) . "/config/owl.php");
$out = ob_get_clean();
require_once($default->owl_fs_root . "/lib/disp.lib.php");
require_once($default->owl_fs_root . "/lib/owl.lib.php");
require_once($default->owl_fs_root . "/scripts/phpmailer/class.phpmailer.php");

// Code to handle the click on the bulk action
// image button, If the button is not there and
// the alternate text is shown, then this doesn't
// work.
if (isset($bdeletegroup_x))
{
   $action = $owl_lang->deletegroup;
} elseif (isset($bdeleteuser_x))
{
   $action = $owl_lang->deleteuser;
} elseif (isset($btn_ed_user_x))
{
   header("Location: " . "index.php?sess=$sess&action=users&owluser=$owluser");
   exit;
} elseif (isset($btn_ed_group_x))
{
   header("Location: " . "index.php?sess=$sess&action=groups&group=$group");
   exit;
} elseif (isset($btn_cancel_news_x))
{
   header("Location: " . "news.php?sess=$sess");
   exit;
} 

if (!fIsAdmin(true) and !fIsUserAdmin($userid) and !fIsNewsAdmin($userid))
{
   die("$owl_lang->err_unauthorized");
}

if ($action == "edit_news")
{
   global $default;
   $sql = new Owl_DB;

   $news_end_date = $year . "-" . $month . "-" . "$day $hour" . ":" . $minute . ":00";
   //$newsdesc = ereg_replace("[\\]'", "'", $newsdesc);
   $newsdesc = stripslashes($newsdesc);
   $newsdesc = ereg_replace("'", "\\'" , $newsdesc);
   //$news_title = ereg_replace("[\\]'", "'", $news_title);
   $news_title = stripslashes($news_title);
   $news_title = ereg_replace("'", "\\'" , $news_title);

   if (trim($newsdesc) == "" || trim($news_title) == "")
   {
      printError($owl_lang->err_news_required);
   } 

   //$sql->query("INSERT INTO $default->owl_news_table (gid, news_title, news_date, news, news_end_date) VALUES ( '$audience', '$news_title', now(), '$newsdesc', '$news_end_date')");
   $sMyQuery = "INSERT INTO $default->owl_news_table (gid, news_title, news_date, news, news_end_date) VALUES ( '$audience', '$news_title', " . $sql->now() . ", '$newsdesc', " . $sql->now($news_end_date) .")";
   $sql->query($sMyQuery);
   $sql->query("DELETE FROM $default->owl_news_table  where id = '$nid'");
   header("Location: news.php?sess=$sess&change=1");
} 

if ($action == "add_news")
{
   global $default;
   $sql = new Owl_DB;

   $news_end_date = $year . "-" . $month . "-" . "$day $hour" . ":" . $minute . ":00";
   //$newsdesc = ereg_replace("[\\]'", "'", $newsdesc);
   $newsdesc = stripslashes($newsdesc);
   $newsdesc = ereg_replace("'", "\\'" , $newsdesc);
   //$news_title = ereg_replace("[\\]'", "'", $news_title);
   $news_title = stripslashes($news_title);
   $news_title = ereg_replace("'", "\\'" , $news_title);
   if (trim($newsdesc) == "" || trim($news_title) == "")
   {
      printError($owl_lang->err_news_required);
   } 

   $sMyQuery = "INSERT INTO $default->owl_news_table (gid, news_title, news_date, news, news_end_date) VALUES ( '$audience', '$news_title', " . $sql->now() . ", '$newsdesc', " . $sql->now($news_end_date) .")";
   $sql->query($sMyQuery);
   //$sql->query("INSERT INTO $default->owl_news_table (gid, news_title, news_date, news, news_end_date) VALUES ( '$audience', '$news_title', now(), '$newsdesc', '$news_end_date')");
   header("Location: news.php?sess=$sess&change=1");
} 

if (!fIsAdmin(true) and !fIsUserAdmin($userid))
{
   exit("$owl_lang->err_unauth_area");
}

if ($action == "user")
{
   if (fIsUserAdmin($userid) and $id == 1)
   {
      printError($owl_lang->err_unauthorized);
   }

   $notify = fIntializeCheckBox($notify);
   $attachfile = fIntializeCheckBox($attachfile);
  
   $disabled = fIntializeCheckBox($disabled);

   // if the user was disabled by the admin LOG Who dnne it

   if ($old_disabled == 0 and $disabled == 1)
   {
      owl_syslog(USER_ADMIN, $userid, 0, 0, $owl_lang->log_admin_disabled . uid_to_name($id) . " ID: $id", "ADMIN");
   }
   $noprefaccess = fIntializeCheckBox($noprefaccess);
   $newsadmin = fIntializeCheckBox($newsadmin);
   $viewlogs = fIntializeCheckBox($viewlogs);
   $viewreports = fIntializeCheckBox($viewreports);
   $useradmin = fIntializeCheckBox($useradmin);
   $logintonewrec = fIntializeCheckBox($logintonewrec);
   $groupadmin = fIntializeCheckBox($groupadmin);
   $comment_notify = fIntializeCheckBox($comment_notify);
   $quota_current = fIntializeCheckBox($quota_current);
   $email_tool = fIntializeCheckBox($email_tool);
   $change_paswd_at_login = fIntializeCheckBox($change_paswd_at_login);

   if ($newlanguage != $oldlanguage)
   {
      $newbuttons = $default->system_ButtonStyle;
   }

   $maxsessions = $maxsessions - 1; // always is stored - 1
   $sql = new Owl_DB;
   $sql->query("SELECT * FROM $default->owl_users_table WHERE id = '$id'");
   $sql->next_record();

   $newpass = $sql->f("password");

   $sql->query("SELECT SUM(f_size) AS actual_quota FROM $default->owl_files_table WHERE creatorid = '$id'");
   $sql->next_record();
   if (is_numeric($sql->f("actual_quota")))
   {
      $quota_current = $sql->f("actual_quota");
   }
   else
   {
      $quota_current = "0";
   }

   if ($newpass == $edit_password)
   {
      $pass = $edit_password;
   } 
   else
   {
      if ($edit_password == $edit_confpassword)
      {
         $pass = md5(stripslashes($edit_password));
      }
      else
      {
         if ($default->debug == true)
         {
            printError($owl_lang->err_new_confirm_different, "Password: $edit_password -- Confirm: $edit_confpassword");
         }
         else
         {
            printError($owl_lang->err_new_confirm_different);
         }
      }
   } 

   $sql->query("UPDATE $default->owl_users_table SET groupid='$groupid',username='$edit_loginname',name='$name',password='$pass',quota_current = '$quota_current', quota_max='$quota', email='$email',notify='$notify',attachfile='$attachfile',disabled='$disabled',noprefaccess='$noprefaccess',language='$newlanguage',maxsessions='$maxsessions',useradmin='$useradmin', newsadmin='$newsadmin', comment_notify = '$comment_notify', buttonstyle = '$newbuttons', homedir = '$homedir', firstdir = '$firstdir' , email_tool = '$email_tool' , change_paswd_at_login = '$change_paswd_at_login', expire_account = '$expire_account', user_auth='$user_auth', logintonewrec='$logintonewrec', groupadmin='$groupadmin', user_offset='$user_offset', viewreports='$viewreports', viewlogs='$viewlogs' where id = '$id'");

   fbCheckForPasswdReuse($pass, $id);
   // Bozz Change BEGIN
   // Clean Up the member group table first
   $sql->query("DELETE FROM $default->owl_users_grpmem_table WHERE userid = $id"); 
   // Insert the new Choices the member group table with selected groups
   $bPrimaryGroupMemberShip = true;
   for ($i = 0 ; $i <= $no_groups_displayed; $i++)
   {
      $checkboxfields = 'group' . $i;
      if ($$checkboxfields != '')
      {
         $checkboxvalue = $$checkboxfields;
         $sql->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupid) VALUES ('$id', '$checkboxvalue')");
         if ($groupid == $checkboxvalue)
         {
            $bPrimaryGroupMemberShip = false;
         }
         
      }
   }
   if ($bPrimaryGroupMemberShip === true)
   {
      $sql->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupid) VALUES ('$id', '$groupid')");
   }

   for ($i = 0 ; $i <= $no_groups_displayed; $i++)
   {
      $checkboxfields = 'mgroupadmin' . $i;
      if ($$checkboxfields != '')
      {
         $checkboxvalue = $$checkboxfields;
         $sql->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupadmin) VALUES ('$id', '$checkboxvalue')");
      }
   }

   /**
    * Bozz Change END
    */
   owl_syslog(USER_ADMIN, $userid, 0, 0, $owl_lang->log_admin_changed_user . $name ."(" .$edit_loginname . ")", "ADMIN");

   header("Location: index.php?sess=$sess&action=users&owluser=$id&change=1");
} 

if ($action == "group")
{
   global $default;

   if (fIsUserAdmin($userid) and $id == 0)
   {
      printError($owl_lang->err_unauthorized);
   }

   $sql = new Owl_DB;
   $sql->query("UPDATE $default->owl_groups_table SET name='$name' where id = '$id'");

   if (!empty($aRemoveMember))
   {
      foreach ($aRemoveMember as $iMemberId)
      {
         $sql->query("DELETE FROM $default->owl_users_grpmem_table WHERE userid = '$iMemberId' and groupid = '$id'"); 
      }
   }

   if (!empty($aRemoveGroupAdmin))
   {
      foreach ($aRemoveGroupAdmin as $iGroupAdminId)
      {
         $sql->query("DELETE FROM $default->owl_users_grpmem_table WHERE userid = '$iGroupAdminId' and groupid = '$id'"); 
      }
   }

   //$sql->query("DELETE FROM $default->owl_users_grpmem_table WHERE groupid = '$id'"); 
   if (!empty($aAddOwlUser))
   {
      foreach ($aAddOwlUser as $iUser)
      {
         $sql->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupid) VALUES ('$iUser', '$id')");
      }
   }

   if (!empty($fselectedgroups))
   {
      foreach ($fselectedgroups as $iGroupId)
      {
         $sql->query("INSERT INTO $default->owl_group_grpmem_table (groupid, subgroupid) VALUES ('$id', '$iGroupId')");
      }
   } 

   owl_syslog(USER_ADMIN, $userid, 0, 0, $owl_lang->log_admin_changed_group . $name . " ID: $id", "ADMIN");

   header("Location: index.php?sess=$sess&action=groups&group=$id&change=1");
} 

if ($action == $owl_lang->deleteuser)
{
   $sql = new Owl_DB;
   $sUidtoName = uid_to_name($id);
   $sql->query("DELETE FROM $default->owl_users_table WHERE id = '$id'"); 
   // Bozz Change Begin
   // Also Clean up the groupmember table when a user is deleted
   $sql->query("DELETE FROM $default->owl_users_grpmem_table WHERE userid = $id"); 
   // Also Clean up the any active sessions from the  table when a user is deleted
   $sql->query("DELETE FROM $default->owl_sessions_table WHERE usid = $id"); 
   // Also Clean up the any active sessions from the  table when a user is deleted
   $sql->query("DELETE FROM $default->owl_advanced_acl_table WHERE user_id = $id"); 
   // Bozz Change End
   owl_syslog(USER_ADMIN, $userid, 0, 0, $owl_lang->log_admin_del_user . $sUidtoName . " ID: $id", "ADMIN");
   header("Location: index.php?sess=$sess&action=users");
} 


if ($action == $owl_lang->deletegroup)
{
   global $default;
   $sGidtoName = group_to_name($id);
                                                                                                                   
   $sql = new Owl_DB;

   $sGidtoName = group_to_name($id);
   $sql->query("SELECT id FROM $default->owl_files_table WHERE groupid = '$id'");
   $iFileCount = $sql->num_rows($sql) ;
   $sql->query("SELECT id FROM $default->owl_folders_table WHERE groupid = '$id'");
   $iFolderCount = $sql->num_rows($sql) ;
   $sql->query("SELECT * FROM $default->owl_users_table WHERE groupid = '$id'");
   $iPrimaryGroup = $sql->num_rows($sql) ;
   $sql->query("SELECT * FROM $default->owl_users_grpmem_table WHERE groupid = '$id'");
   $iMemberGroup = $sql->num_rows($sql) ;
   //$sql->query("SELECT * FROM $default->owl_group_grpmem_table WHERE groupid = '$id'");
   //$iGroupOfGroup = $sql->num_rows($sql) ;

   //if ($iFileCount == 0 and $iFolderCount == 0 and $iPrimaryGroup == 0 and $iMemberGroup == 0 and $iGroupOfGroup == 0)
   if ($iFileCount == 0 and $iFolderCount == 0 and $iPrimaryGroup == 0 and $iMemberGroup == 0)
   {
      $sql->query("DELETE FROM $default->owl_groups_table WHERE id = '$id'");
      $sql->query("DELETE FROM $default->owl_users_grpmem_table WHERE groupid = '$id'");
      //$sql->query("DELETE FROM $default->owl_group_grpmem_table WHERE groupid = '$id'");
   }
   else
   {
      $sErrorMessage = sprintf($owl_lang->err_group_delete, $iFileCount, $iFolderCount, $iPrimaryGroup, $iMemberGroup);
      //$sErrorMessage = sprintf($owl_lang->err_group_delete, $iFileCount, $iFolderCount, $iPrimaryGroup, $iMemberGroup, $iGroupOfGroup);
      printError($sErrorMessage);
   }

   owl_syslog(USER_ADMIN, $userid, 0, 0, $owl_lang->log_admin_del_group . $sGidtoName . " ID: $id", "ADMIN");
   header("Location: index.php?sess=$sess&action=groups");
} 

if ($action == "add")
{
   if ($type == "user")
   {
      $notify = fIntializeCheckBox($notify);
      $attachfile = fIntializeCheckBox($attachfile);
      $disabled = fIntializeCheckBox($disabled);
      $noprefaccess = fIntializeCheckBox($noprefaccess);
      $newsadmin = fIntializeCheckBox($newsadmin);
      $viewlogs = fIntializeCheckBox($viewlogs);
      $viewreports = fIntializeCheckBox($viewreports);
      $useradmin = fIntializeCheckBox($useradmin);
      $comment_notify = fIntializeCheckBox($comment_notify);
      $email_tool = fIntializeCheckBox($email_tool);
      $change_paswd_at_login = fIntializeCheckBox($change_paswd_at_login);

      $logintonewrec = fIntializeCheckBox($logintonewrec);
      $groupadmin = fIntializeCheckBox($groupadmin);

      $maxsessions = $maxsessions - 1; // always is stored - 1
      
      $sql = new Owl_DB;
      $sql->query("SELECT * FROM $default->owl_users_table WHERE username = '$edit_loginname'");
      if ($sql->num_rows($sql) > 0) printError("$owl_lang->err_user_exists", $owl_lang->username);
      $sql->query("SELECT * FROM $default->owl_users_table WHERE name = '$name'");
      if ($sql->num_rows($sql) > 0) printError("$owl_lang->err_user_exists", $owl_lang->full_name);

      //$dNow = date("Y-m-d H:i:s");
      $dNow = $sql->now();


      if ($edit_password == $edit_confpassword)
      {
         $pass = md5($edit_password);
      }
      else
      {
         if ($default->debug == true)
         {
            printError($owl_lang->err_new_confirm_different, "Password: $edit_password -- Confirm: $edit_confpassword");
         }
         else
         {
            printError($owl_lang->err_new_confirm_different);
         }
      }


      $sql->query("INSERT INTO $default->owl_users_table (groupid,username,name,password,quota_max,quota_current,email,notify,attachfile,disabled,noprefaccess,language,maxsessions,curlogin,lastlogin,useradmin, newsadmin, comment_notify, buttonstyle, homedir,firstdir, email_tool,change_paswd_at_login, expire_account, user_auth, logintonewrec, groupadmin, user_offset,passwd_last_changed, viewlogs, viewreports ) VALUES ('$groupid', '$edit_loginname', '$name', '" . $pass . "', '$quota', '0', '$email', '$notify','$attachfile', '$disabled', '$noprefaccess', '$newlanguage', '$maxsessions', $dNow, $dNow, '$useradmin', '$newsadmin', '$comment_notify', '$default->system_ButtonStyle', '$homedir', '$firstdir','$email_tool', '$change_paswd_at_login', '$expire_account', '$user_auth' , '$logintonewrec', '$groupadmin', '$user_offset', $dNow ,'$viewlogs', '$viewreports')"); 
      // Bozz Change BEGIN
      // Populated the member group table with selected groups
      $sql->query("SELECT id FROM $default->owl_users_table WHERE username = '$edit_loginname'");
      $sql->next_record();
      $newuid = $sql->f("id");

      fbCheckForPasswdReuse($pass, $newuid);

      $bPrimaryGroupMemberShip = true;
      for ($i = 0 ; $i <= $no_groups_displayed; $i++)
      {
         $checkboxfields = 'group' . $i;
         if ($$checkboxfields != '')
         {
            $checkboxvalue = $$checkboxfields;
            $sql->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupid) VALUES ('$newuid', '$checkboxvalue')");
         }
         if ($groupid == $checkboxvalue)
         {
            $bPrimaryGroupMemberShip = false;
         }
      }
      if ($bPrimaryGroupMemberShip === true)
      {
         $sql->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupid) VALUES ('$newuid', '$groupid')");
      }


      for ($i = 0 ; $i <= $no_groups_displayed; $i++)
      {
         $checkboxfields = 'mgroupadmin' . $i;
         if ($$checkboxfields != '')
         {
            $checkboxvalue = $$checkboxfields;
            $sql->query("INSERT INTO $default->owl_users_grpmem_table (userid,groupadmin) VALUES ('$newuid', '$checkboxvalue')");
         }
      }

      /**
       * Bozz Change END
       */
      if ($home == "1")
      {
         $sql->query("SELECT * FROM $default->owl_users_table WHERE username = '$edit_loginname'");
         while ($sql->next_record()) $id = $sql->f("id");
         $sql->query("insert into $default->owl_folders_table values (0, '$edit_loginname', '2', '54', '$groupid', '$id')");
         mkdir($default->owl_fs_root . "/" . fid_to_name("1") . "/Home/$edit_loginname", $default->directory_mask);
      } 


      if ($email_password == 1 and strlen($email) > 0)
      {
         $aBody = fGetMailBodyText(ADMIN_PASSWORD);

         $link = $default->owl_notify_link . "index.php" ;
         $sHtmlLink = "<a href=\"" . $link . "\">$owl_lang->login</a>";

         $aBody['HTML'] = ereg_replace("\%USER_FULLNAME\%", $name, $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%USER_FULLNAME\%", $name, $aBody['TXT'] );
         $aBody['HTML'] = ereg_replace("\%USERNAME\%", $edit_loginname, $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%USERNAME\%", $edit_loginname, $aBody['TXT'] );
         $aBody['HTML'] = ereg_replace("\%PASSWORD\%", $edit_password, $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%PASSWORD\%", $edit_password, $aBody['TXT'] );
         $aBody['HTML'] = ereg_replace("\%LINK\%", $sHtmlLink, $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%LINK\%", $link, $aBody['TXT'] );

         $mail = new phpmailer();
         if ($default->use_smtp)
         {
            $mail->IsSMTP(); // set mailer to use SMTP
            if ($default->use_smtp_auth)
            {
               $mail->SMTPAuth = "true"; // turn on SMTP authentication
               $mail->Username = "$default->smtp_auth_login"; // SMTP username
               $mail->Password = "$default->smtp_passwd"; // SMTP password
            }
         }
         $mail->CharSet = "$owl_lang->charset"; // set the email charset to the language file charset
         $mail->Host = "$default->owl_email_server"; // specify main and backup server
         $mail->From = "$default->owl_email_from";
         $mail->FromName = "$default->owl_email_fromname";
         $mail->AddAddress($email);
         $mail->AddReplyTo("$default->owl_email_replyto", "OWL Intranet");
         $mail->WordWrap = 50; // set word wrap to 50 characters
         $mail->IsHTML(true); // set email format to HTML
         $mail->Subject = $default->owl_email_subject . " " . $aBody['SUBJECT'];
         $mail->Body =  $aBody['HTML'];
         $mail->altBody =  $aBody['TXT'];
   
         if (!$mail->Send() && $default->debug == true)
         {
            printError("DEBUG: " . $owl_lang->err_email, $mail->ErrorInfo);
         }
      }
      owl_syslog(USER_ADMIN, $userid, 0, 0, $owl_lang->log_admin_created_user . $name ."(" .$edit_loginname . ") ", "ADMIN");

      header("Location: index.php?sess=$sess");
   } elseif ($type == "group")
   {
      $sql = new Owl_DB;
      $sql->query("SELECT id from  $default->owl_groups_table where name = '$name'");
      if ($sql->num_rows() > 0)
      {
         printError($owl_lang->err_group_exists);
      } 
      $sql->query("INSERT INTO $default->owl_groups_table (name) VALUES ('$name')");

      owl_syslog(USER_ADMIN, $userid, 0, 0, $owl_lang->log_admin_created_group . $name , "ADMIN");
      header("Location: index.php?sess=$sess");
   } 
} 

if (!fIsAdmin(true))
{
   exit("$owl_lang->err_unauth_area");
}

if ($action == "edhtml")
{
   $sql = new Owl_DB;
   $sql->query("UPDATE $default->owl_html_table SET body_textcolor='$body_textcolor',body_link='$body_link',body_vlink='$body_vlink',table_expand_width='$expand_width',table_collapse_width='$collapse_width', body_background='$body_background',owl_logo = '$owl_logo' ");

   header("Location: index.php?sess=$sess&action=edhtml&change=1");
} 

if ($action == "edprefs")
{
   $sql = new Owl_DB;

   $use_smtp_auth = fIntializeCheckBox($use_smtp_auth);
   $info_panel_wide = fIntializeCheckBox($info_panel_wide);
   $track_favorites = fIntializeCheckBox($track_favorites);
   $use_smtp = fIntializeCheckBox($use_smtp);
   $collect_trash = fIntializeCheckBox($collect_trash);
   $allow_custpopup=fIntializeCheckBox($allow_custpopup);
   $allow_popup = fIntializeCheckBox($allow_popup);
   $forget_pass = fIntializeCheckBox($forget_pass);
   $restrict_view = fIntializeCheckBox($restrict_view);
   $hide_backup = fIntializeCheckBox($hide_backup);
   $logging = fIntializeCheckBox($logging);
   $forgot_pass = fIntializeCheckBox($forgot_pass);
   $self_reg = fIntializeCheckBox($self_reg);
   $self_reg_notify = fIntializeCheckBox($self_reg_notify);
   $self_reg_attachfile = fIntializeCheckBox($self_reg_attachfile);
   $self_reg_disabled = fIntializeCheckBox($self_reg_disabled);
   $self_reg_noprefacces = fIntializeCheckBox($self_reg_noprefacces);
   $self_create_homedir = fIntializeCheckBox($self_create_homedir);
   $self_captcha = fIntializeCheckBox($self_captcha);
   $rec_per_page =fIntializeCheckBox($rec_per_page);
   $remember_me =fIntializeCheckBox($remember_me);

   if ($lookAtHD != "false")
   {
      $lookAtHD = "true";
   }
   if ($owl_expand != "1")
   {
      $owl_expand = "0";
   }
   if ($version_control != "1")
   {
      $version_control = "0";
   }

   $maxsess = $self_reg_maxsessions - 1;

   if ($default->owl_FileDir == $owl_tmpdir)
   {
      $owl_tmpdir = "";
   } 
   if ($trash_can_location == $default->owl_FileDir . "/TrashCan")
   {
      $trash_can_location = "";
   } 

   // Restricted View Does not work with Records Per Page
   // Disable Records Per page if its set.

   if ($restrict_view == 1)
   {
      $rec_per_page = 0;
   }

   $expand_disp_status = fIntializeCheckBox($expand_disp_status);
   $expand_disp_doc_num = fIntializeCheckBox($expand_disp_doc_num);
   $expand_disp_doc_type = fIntializeCheckBox($expand_disp_doc_type);
   $expand_disp_doc_fields = fIntializeCheckBox($expand_disp_doc_fields);
   $expand_disp_title = fIntializeCheckBox($expand_disp_title);
   $expand_disp_version = fIntializeCheckBox($expand_disp_version);
   $expand_disp_file = fIntializeCheckBox($expand_disp_file);
   $expand_disp_size = fIntializeCheckBox($expand_disp_size);
   $expand_disp_posted = fIntializeCheckBox($expand_disp_posted);
   $expand_disp_updated = fIntializeCheckBox($expand_disp_updated);
   $expand_disp_modified = fIntializeCheckBox($expand_disp_modified);
   $expand_disp_action = fIntializeCheckBox($expand_disp_action);
   $expand_disp_held = fIntializeCheckBox($expand_disp_held);

   $collapse_disp_status = fIntializeCheckBox($collapse_disp_status);
   $collapse_disp_doc_num = fIntializeCheckBox($collapse_disp_doc_num);
   $collapse_disp_doc_type = fIntializeCheckBox($collapse_disp_doc_type);
   $collapse_disp_doc_fields = fIntializeCheckBox($collapse_disp_doc_fields);
   $collapse_disp_title = fIntializeCheckBox($collapse_disp_title);
   $collapse_disp_version = fIntializeCheckBox($collapse_disp_version);
   $collapse_disp_file = fIntializeCheckBox($collapse_disp_file);
   $collapse_disp_size = fIntializeCheckBox($collapse_disp_size);
   $collapse_disp_posted = fIntializeCheckBox($collapse_disp_posted);
   $collapse_disp_updated = fIntializeCheckBox($collapse_disp_updated);
   $collapse_disp_modified = fIntializeCheckBox($collapse_disp_modified);
   $collapse_disp_action = fIntializeCheckBox($collapse_disp_action);
   $collapse_disp_held = fIntializeCheckBox($collapse_disp_held);

   $expand_search_disp_score = fIntializeCheckBox($expand_search_disp_score);
   $expand_search_disp_folder_path = fIntializeCheckBox($expand_search_disp_folder_path);
   $expand_search_disp_doc_num = fIntializeCheckBox($expand_search_disp_doc_num);
   $expand_search_disp_doc_type = fIntializeCheckBox($expand_search_disp_doc_type);
   $expand_search_disp_doc_fields = fIntializeCheckBox($expand_search_disp_doc_fields);
   $expand_search_disp_file = fIntializeCheckBox($expand_search_disp_file);
   $expand_search_disp_size = fIntializeCheckBox($expand_search_disp_size);
   $expand_search_disp_posted = fIntializeCheckBox($expand_search_disp_posted);
   $expand_search_disp_updated = fIntializeCheckBox($expand_search_disp_updated);
   $expand_search_disp_modified = fIntializeCheckBox($expand_search_disp_modified);
   $expand_search_disp_action = fIntializeCheckBox($expand_search_disp_action);

   $collapse_search_disp_score = fIntializeCheckBox($collapse_search_disp_score);
   $collapse_search_disp_folder_path = fIntializeCheckBox($collapse_search_disp_folder_path);
   $collapse_search_disp_doc_num = fIntializeCheckBox($collapse_search_disp_doc_num);
   $collapse_search_disp_doc_type = fIntializeCheckBox($collapse_search_disp_doc_type);
   $colps_search_disp_doc_fields = fIntializeCheckBox($colps_search_disp_doc_fields);
   $collapse_search_disp_file = fIntializeCheckBox($collapse_search_disp_file);
   $collapse_search_disp_size = fIntializeCheckBox($collapse_search_disp_size);
   $collapse_search_disp_posted = fIntializeCheckBox($collapse_search_disp_posted);
   $collapse_search_disp_updated = fIntializeCheckBox($collapse_search_disp_updated);
   $collapse_search_disp_modified = fIntializeCheckBox($collapse_search_disp_modified);
   $collapse_search_disp_action = fIntializeCheckBox($collapse_search_disp_action);
   $thumb_disp_status = fIntializeCheckBox($thumb_disp_status);
   $thumb_disp_doc_num  = fIntializeCheckBox($thumb_disp_doc_num);
   $thumb_disp_image_info  = fIntializeCheckBox($thumb_disp_image_info);
   $thumb_disp_version  = fIntializeCheckBox($thumb_disp_version);
   $thumb_disp_size  = fIntializeCheckBox($thumb_disp_size);
   $thumb_disp_posted  = fIntializeCheckBox($thumb_disp_posted);
   $thumb_disp_updated  = fIntializeCheckBox($thumb_disp_updated);
   $thumb_disp_modified  = fIntializeCheckBox($thumb_disp_modified);
   $thumb_disp_action  = fIntializeCheckBox($thumb_disp_action);
   $thumb_disp_held  = fIntializeCheckBox($thumb_disp_held);

   $thumbnails  = fIntializeCheckBox($thumbnails);

   if(empty($thumbnails_small_width))
   {
      $thumbnails_small_width = "25";
   }
   if(empty($thumbnails_med_width))
   {
      $thumbnails_med_width = "50";
   }
   if(empty($thumbnails_large_width))
   {
      $thumbnails_large_width = "100";
   }
   if(empty($thumbnail_view_columns))
   {
      $thumbnail_view_columns = "4";
   }

   $hide_folder_doc_count = fIntializeCheckBox($hide_folder_doc_count);
   $hide_folder_size = fIntializeCheckBox($hide_folder_size);
   $old_action_icons = fIntializeCheckBox($old_action_icons);
   $search_result_folders = fIntializeCheckBox($search_result_folders);

   if(empty($major_revision))
   {
      $major_revision = "0";
   }

   if(empty($minor_revision))
   {
      $minor_revision = "0";
   }

   if(empty($doc_id_num_digits))
   {
      $doc_id_num_digits = 3;
   }

   if(empty($anon_ro))
   {
      $anon_ro = '0';
   }

   $view_doc_in_new_window = fIntializeCheckBox($view_doc_in_new_window);
   $admin_login_to_browse_page = fIntializeCheckBox($admin_login_to_browse_page);

   $save_keywords_to_db = fIntializeCheckBox($save_keywords_to_db);
   $peer_opt = fIntializeCheckBox($peer_opt);
   $peer_review = fIntializeCheckBox($peer_review);
   $password_override = fIntializeCheckBox($password_override);
   $use_zip = fIntializeCheckBox($use_zip);
   $filedescreq =fIntializeCheckBox($filedescreq);
   $folderdescreq =fIntializeCheckBox($folderdescreq);
   $show_user_info =fIntializeCheckBox($show_user_info);
   $lookAtHD_del = fIntializeCheckBox($lookAtHD_del);

   if(empty($def_anon_user))
   {
      $def_anon_user = 2;
   }

   $file_admin_group = fIntializeCheckBox($file_admin_group);

   $dbdump_path = fCleanupUserInput($dbdump_path);
   $gzip_path = fCleanupUserInput($gzip_path);
   $tar_path = fCleanupUserInput($tar_path);
   $virus_path = fCleanupUserInput($virus_path);
   $pdftotext_path = fCleanupUserInput($pdftotext_path);
   $rtftotext_path = fCleanupUserInput($rtftotext_path);
   $wordtotext_path = fCleanupUserInput($wordtotext_path);
   $unzip_path = fCleanupUserInput($unzip_path);
   $owl_tmpdir = fCleanupUserInput($owl_tmpdir);
   $pod2html_path = fCleanupUserInput($pod2html_path);
   $thumbnails_tool_path = fCleanupUserInput($thumbnails_tool_path);
   $thumbnails_video_tool_path = fCleanupUserInput($thumbnails_video_tool_path);

   if (empty($min_pass_length))
   {
      $min_pass_length = "0";
   }
   if (empty($min_username_length))
   {
      $min_username_length = "0";
   }
   if (empty($min_pass_numeric))
   {
      $min_pass_numeric = "0";
   }
   if (empty($min_pass_special))
   {
      $min_pass_special = "0";
   }

   $enable_lock_account = fIntializeCheckBox($enable_lock_account);

   if (empty($lock_account_bad_password))
   {
      $lock_account_bad_password = "0";
   }
   if (empty($track_user_passwords))
   {
      $track_user_passwords = "0";
   }
   if (empty($change_password_every))
   {
      $change_password_every = "0";
   }
   if (empty($file_security))
   {
      $file_security = "0";
   }
   if (empty($folder_security))
   {
      $folder_security = "0";
   }

   
   $qPrefsQuery = "UPDATE $default->owl_prefs_table SET ";
   $qPrefsQuery .= "email_from='$email_from'";
   $qPrefsQuery .= ", email_fromname='$email_fromname'";
   $qPrefsQuery .= ", email_replyto='$email_replyto'";
   $qPrefsQuery .= ", email_server='$email_server'";
   $qPrefsQuery .= ", lookathd='$lookAtHD'";
   $qPrefsQuery .= ", lookathddel='$lookAtHD_del'";
   $qPrefsQuery .= ", def_file_security='$def_file_security'";
   $qPrefsQuery .= ", def_file_group_owner='$def_file_group_owner'";
   $qPrefsQuery .= ", def_file_owner='$def_file_owner'";
   $qPrefsQuery .= ", def_file_title='$def_file_title'";
   $qPrefsQuery .= ", def_file_meta='$def_file_meta'";
   $qPrefsQuery .= ", def_fold_security='$def_fold_security'";
   $qPrefsQuery .= ", def_fold_group_owner='$def_fold_group_owner'";
   $qPrefsQuery .= ", def_fold_owner='$def_fold_owner'";
   $qPrefsQuery .= ", max_filesize='$max_filesize'";
   $qPrefsQuery .= ", timeout='$owl_timeout'";
   $qPrefsQuery .= ", expand='$owl_expand'";
   $qPrefsQuery .= ", version_control='$version_control'";
   $qPrefsQuery .= ", restrict_view='$restrict_view'";
   $qPrefsQuery .= ", dbdump_path='$dbdump_path'";
   $qPrefsQuery .= ", gzip_path='$gzip_path'";
   $qPrefsQuery .= ", tar_path='$tar_path'";
   $qPrefsQuery .= ", file_perm='$file_security'";
   $qPrefsQuery .= ", folder_perm='$folder_security'";
   $qPrefsQuery .= ", anon_ro='$anon_ro'";
   $qPrefsQuery .= ", hide_backup='$hide_backup'";
   $qPrefsQuery .= ", logging='$logging'";
   $qPrefsQuery .= ", log_file='$log_file'";
   $qPrefsQuery .= ", log_login='$log_login'";
   $qPrefsQuery .= ", log_rec_per_page='$log_rec_per_page'";
   $qPrefsQuery .= ", self_reg='$self_reg'";
   $qPrefsQuery .= ", self_reg_quota='$self_reg_quota'";
   $qPrefsQuery .= ", self_reg_notify='$self_reg_notify'";
   $qPrefsQuery .= ", self_reg_attachfile='$self_reg_attachfile'";
   $qPrefsQuery .= ", self_reg_disabled='$self_reg_disabled'";
   $qPrefsQuery .= ", self_reg_noprefacces='$self_reg_noprefacces'";
   $qPrefsQuery .= ", self_reg_maxsessions='$maxsess'";
   $qPrefsQuery .= ", self_reg_group='$self_reg_group'";
   $qPrefsQuery .= ", self_reg_homedir='$self_reg_homedir'";
   $qPrefsQuery .= ", self_reg_firstdir='$self_reg_firstdir'";
   $qPrefsQuery .= ", self_create_homedir='$self_create_homedir'";
   $qPrefsQuery .= ", self_captcha='$self_captcha'";
   $qPrefsQuery .= ", forgot_pass = '$forgot_pass'";
   $qPrefsQuery .= ", email_subject = '$email_subject'";
   $qPrefsQuery .= ", tmpdir = '$owl_tmpdir'";
   $qPrefsQuery .= ", anon_user = '$def_anon_user'";
   $qPrefsQuery .= ", file_admin_group = '$file_admin_group'";
   $qPrefsQuery .= ", collect_trash = '$collect_trash'";
   $qPrefsQuery .= ", trash_can_location = '$trash_can_location'";
   $qPrefsQuery .= ", allow_custpopup = '$allow_custpopup' ";
   $qPrefsQuery .= ", allow_popup = '$allow_popup' ";
   $qPrefsQuery .= ", status_bar_location = '$status_bar_location'";
   $qPrefsQuery .= ", virus_path = '$virus_path'";
   $qPrefsQuery .= ", rtftotext_path = '$rtftotext_path'";
   $qPrefsQuery .= ", pdftotext_path = '$pdftotext_path'";
   $qPrefsQuery .= ", wordtotext_path = '$wordtotext_path'";
   $qPrefsQuery .= ", unzip_path = '$unzip_path'";
   $qPrefsQuery .= ", pod2html_path = '$pod2html_path'";
   $qPrefsQuery .= ", use_smtp = '$use_smtp'";
   $qPrefsQuery .= ", use_smtp_auth = '$use_smtp_auth'";
   $qPrefsQuery .= ", smtp_passwd = '$smtp_passwd' ";
   $qPrefsQuery .= ", smtp_auth_login = '$smtp_auth_login'";
   $qPrefsQuery .= ", rec_per_page = '$rec_per_page'";
   $qPrefsQuery .= ", remember_me = '$remember_me'";
   $qPrefsQuery .= ", cookie_timeout = '$cookie_timeout'";
   $qPrefsQuery .= ", search_bar = '$search_bar'";
   $qPrefsQuery .= ", pref_bar = '$pref_bar'";
   $qPrefsQuery .= ", bulk_buttons = '$bulk_buttons'";
   $qPrefsQuery .= ", action_buttons = '$action_buttons'";
   $qPrefsQuery .= ", folder_tools = '$folder_tools' ";
   $qPrefsQuery .= ", expand_disp_status = '$expand_disp_status'";
   $qPrefsQuery .= ", expand_disp_doc_num = '$expand_disp_doc_num'";
   $qPrefsQuery .= ", expand_disp_doc_type = '$expand_disp_doc_type'";
   $qPrefsQuery .= ", expand_disp_doc_fields = '$expand_disp_doc_fields'";
   $qPrefsQuery .= ", expand_disp_title = '$expand_disp_title'";
   $qPrefsQuery .= ", expand_disp_version = '$expand_disp_version'";
   $qPrefsQuery .= ", expand_disp_file = '$expand_disp_file'";
   $qPrefsQuery .= ", expand_disp_size = '$expand_disp_size'";
   $qPrefsQuery .= ", expand_disp_posted = '$expand_disp_posted'";
   $qPrefsQuery .= ", expand_disp_updated = '$expand_disp_updated'";
   $qPrefsQuery .= ", expand_disp_modified = '$expand_disp_modified'";
   $qPrefsQuery .= ", expand_disp_action = '$expand_disp_action'";
   $qPrefsQuery .= ", expand_disp_held = '$expand_disp_held'";
   $qPrefsQuery .= ", collapse_disp_status = '$collapse_disp_status'";
   $qPrefsQuery .= ", collapse_disp_doc_num = '$collapse_disp_doc_num'";
   $qPrefsQuery .= ", collapse_disp_doc_type = '$collapse_disp_doc_type'";
   $qPrefsQuery .= ", collapse_disp_doc_fields = '$collapse_disp_doc_fields'";
   $qPrefsQuery .= ", collapse_disp_title = '$collapse_disp_title'";
   $qPrefsQuery .= ", collapse_disp_version = '$collapse_disp_version'";
   $qPrefsQuery .= ", collapse_disp_file = '$collapse_disp_file'";
   $qPrefsQuery .= ", collapse_disp_size = '$collapse_disp_size'";
   $qPrefsQuery .= ", collapse_disp_posted = '$collapse_disp_posted'";
   $qPrefsQuery .= ", collapse_disp_updated = '$collapse_disp_updated'";
   $qPrefsQuery .= ", collapse_disp_modified = '$collapse_disp_modified'";
   $qPrefsQuery .= ", collapse_disp_action = '$collapse_disp_action'";
   $qPrefsQuery .= ", collapse_disp_held = '$collapse_disp_held'";
   $qPrefsQuery .= ", expand_search_disp_score = '$expand_search_disp_score'";
   $qPrefsQuery .= ", expand_search_disp_folder_path = '$expand_search_disp_folder_path'";
   $qPrefsQuery .= ", expand_search_disp_doc_num = '$expand_search_disp_doc_num'";
   $qPrefsQuery .= ", expand_search_disp_doc_type = '$expand_search_disp_doc_type'";
   $qPrefsQuery .= ", expand_search_disp_doc_fields = '$expand_search_disp_doc_fields'";
   $qPrefsQuery .= ", expand_search_disp_file = '$expand_search_disp_file'";
   $qPrefsQuery .= ", expand_search_disp_size = '$expand_search_disp_size'";
   $qPrefsQuery .= ", expand_search_disp_posted = '$expand_search_disp_posted'";
   $qPrefsQuery .= ", expand_search_disp_updated = '$expand_search_disp_updated'";
   $qPrefsQuery .= ", expand_search_disp_modified = '$expand_search_disp_modified'";
   $qPrefsQuery .= ", expand_search_disp_action = '$expand_search_disp_action'";
   $qPrefsQuery .= ", collapse_search_disp_score = '$collapse_search_disp_score'";
   $qPrefsQuery .= ", colps_search_disp_fld_path = '$collapse_search_disp_folder_path'";
   $qPrefsQuery .= ", collapse_search_disp_doc_num = '$collapse_search_disp_doc_num'";
   $qPrefsQuery .= ", collapse_search_disp_doc_type = '$collapse_search_disp_doc_type'";
   $qPrefsQuery .= ", colps_search_disp_doc_fields = '$colps_search_disp_doc_fields'";
   $qPrefsQuery .= ", collapse_search_disp_file = '$collapse_search_disp_file'";
   $qPrefsQuery .= ", collapse_search_disp_size = '$collapse_search_disp_size'";
   $qPrefsQuery .= ", collapse_search_disp_posted = '$collapse_search_disp_posted'";
   $qPrefsQuery .= ", collapse_search_disp_updated = '$collapse_search_disp_updated'";
   $qPrefsQuery .= ", collapse_search_disp_modified = '$collapse_search_disp_modified'";
   $qPrefsQuery .= ", collapse_search_disp_action = '$collapse_search_disp_action'";
   $qPrefsQuery .= ", hide_folder_doc_count = '$hide_folder_doc_count'";
   $qPrefsQuery .= ", old_action_icons = '$old_action_icons'";
   $qPrefsQuery .= ", search_result_folders = '$search_result_folders'";
   $qPrefsQuery .= ", restore_file_prefix = '" . ereg_replace("^-", "_", $restore_file_prefix) . "'";
   $qPrefsQuery .= ", major_revision = '$major_revision'";
   $qPrefsQuery .= ", minor_revision = '$minor_revision'";
   $qPrefsQuery .= ", doc_id_prefix = '$doc_id_prefix'";
   $qPrefsQuery .= ", doc_id_num_digits = '$doc_id_num_digits'";
   $qPrefsQuery .= ", view_doc_in_new_window = '$view_doc_in_new_window'";
   $qPrefsQuery .= ", admin_login_to_browse_page = '$admin_login_to_browse_page'";
   $qPrefsQuery .= ", save_keywords_to_db = '$save_keywords_to_db'";
   $qPrefsQuery .= ", peer_review = '$peer_review'";
   $qPrefsQuery .= ", peer_opt = '$peer_opt'";
   $qPrefsQuery .= ", folder_size = '$hide_folder_size'";
   $qPrefsQuery .= ", download_folder_zip='$use_zip'";
   $qPrefsQuery .= ", display_password_override = '$password_override'";  
   $qPrefsQuery .= ", thumb_disp_status='$thumb_disp_status'";
   $qPrefsQuery .= ", thumb_disp_doc_num='$thumb_disp_doc_num'";
   $qPrefsQuery .= ", thumb_disp_image_info='$thumb_disp_image_info'";
   $qPrefsQuery .= ", thumb_disp_version='$thumb_disp_version'";
   $qPrefsQuery .= ", thumb_disp_size='$thumb_disp_size'";
   $qPrefsQuery .= ", thumb_disp_posted='$thumb_disp_posted'";
   $qPrefsQuery .= ", thumb_disp_updated='$thumb_disp_updated'";
   $qPrefsQuery .= ", thumb_disp_modified='$thumb_disp_modified'";
   $qPrefsQuery .= ", thumb_disp_action='$thumb_disp_action'";
   $qPrefsQuery .= ", thumb_disp_held='$thumb_disp_held'"; 
   $qPrefsQuery .= ", thumbnails_tool_path='$thumbnails_tool_path'";
   $qPrefsQuery .= ", thumbnails_video_tool_path='$thumbnails_video_tool_path'";
   $qPrefsQuery .= ", thumbnails_video_tool_opt='$thumbnails_video_tool_opt'";
   $qPrefsQuery .= ", thumbnails_small_width='$thumbnails_small_width'";
   $qPrefsQuery .= ", thumbnails_med_width='$thumbnails_med_width'";
   $qPrefsQuery .= ", thumbnails_large_width='$thumbnails_large_width'";
   $qPrefsQuery .= ", thumbnail_view_columns='$thumbnail_view_columns'";
   $qPrefsQuery .= ", thumbnails='$thumbnails'";
   $qPrefsQuery .= ", min_pass_length='$min_pass_length'";
   $qPrefsQuery .= ", min_username_length='$min_username_length'";
   $qPrefsQuery .= ", min_pass_numeric='$min_pass_numeric'";
   $qPrefsQuery .= ", min_pass_special='$min_pass_special'";
   $qPrefsQuery .= ", enable_lock_account='$enable_lock_account'";
   $qPrefsQuery .= ", lock_account_bad_password='$lock_account_bad_password'";
   $qPrefsQuery .= ", track_user_passwords='$track_user_passwords'";
   $qPrefsQuery .= ", change_password_every='$change_password_every'";
   $qPrefsQuery .= ", filedescreq ='$filedescreq'";
   $qPrefsQuery .= ", folderdescreq ='$folderdescreq'";
   $qPrefsQuery .= ", info_panel_wide ='$info_panel_wide'";
   $qPrefsQuery .= ", track_favorites ='$track_favorites'";
   $qPrefsQuery .= ", show_user_info ='$show_user_info'";
 
   $sql->query($qPrefsQuery);

   header("Location: index.php?sess=$sess&action=edprefs&change=1");
} 
?>
