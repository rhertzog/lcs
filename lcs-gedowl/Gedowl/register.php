<?php

/**
 * register.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 */

ob_start();
require_once(dirname(__FILE__)."/config/owl.php");
$out = ob_get_clean();
require_once($default->owl_fs_root ."/lib/disp.lib.php");



if (!empty($c) and is_numeric($c))
{
   $default->owl_current_db = $c;
}

require_once($default->owl_fs_root ."/lib/owl.lib.php");

if ($default->registration_using_captcha)
{
   require_once($default->owl_fs_root ."/scripts/hn_captcha/hn_captcha.class.php");
}

if ($default->self_reg == 0 && $default->forgot_pass == 0 and ($myaction != "changepass" and $myaction != "verpasschange"))
{
   header("Location: " . $default->owl_root_url . "/index.php?login=1");
}
                                                                                                                   
                                                                                                                   
if ($default->self_reg == 0 && $myaction == 'register')
{
   header("Location: " . $default->owl_root_url . "/index.php?login=1");
}
                                                                                                                   
if ($default->forgot_pass == 0 && $myaction == 'forgot')
{
   header("Location: " . $default->owl_root_url . "/index.php?login=1");
}

//if ($default->self_reg == 0 && $default->forgot_pass == 0)
//{
   //header("Location: " . $default->owl_root_url . "/index.php?login=1");
//} 
//if ($default->self_reg == 0 && $myaction == 'register')
//{
   //header("Location: " . $default->owl_root_url . "/index.php?login=1");
//} 
//if ($default->forgot_pass == 0 && $myaction == 'forgot')
//{
   //header("Location: " . $default->owl_root_url . "/index.php?login=1");
//} 

if ($default->registration_using_captcha)
{
   $CAPTCHA_INIT = array(
            'tempfolder'     => $default->owl_fs_root . '/scripts/hn_captcha/tmp/',      // string: absolute path (with trailing slash!) to a writeable tempfolder which is also accessible via HTTP!
			'TTF_folder'     => $default->owl_fs_root . '/scripts/hn_captcha/fonts/', // string: absolute path (with trailing slash!) to folder which contains your TrueType-Fontfiles.
			'TTF_RANGE'      => array('Vera.ttf','VeraBd.ttf','VeraBI.ttf','VeraIt.ttf','VeraMoBd.ttf','VeraMoBI.ttf','VeraMoIt.ttf','VeraMono.ttf','VeraSe.ttf','VeraSeBd.ttf'),

            'chars'          => 5,       // integer: number of chars to use for ID
            'minsize'        => 10,      // integer: minimal size of chars
            'maxsize'        => 30,      // integer: maximal size of chars
            'maxrotation'    => 40,      // integer: define the maximal angle for char-rotation, good results are between 0 and 30

            'noise'          => TRUE,    // boolean: TRUE = noisy chars | FALSE = grid
            'websafecolors'  => TRUE,   // boolean
            'refreshlink'    => TRUE,    // boolean
            'lang'           => 'en',    // string:  ['en'|'de']
            'maxtry'         => 3,       // integer: [1-9]

            'badguys_url'    => '/',     // string: URL
            'secretstring'   => 'hbozzUg2pEeouRoV4wOEsTaw6smAtSMa7CsESm2wAdFejOc8B0zzTuDytH6PypuSNi6FulDo',
            'secretposition' => 23,      // integer: [1-32]

            'debug'          => FALSE
	);

   global $captcha;
   $captcha =& new hn_captcha($CAPTCHA_INIT);
}

require_once($default->owl_fs_root ."/lib/security.lib.php");
require_once($default->owl_fs_root ."/scripts/phpmailer/class.phpmailer.php");

unset($userid);


function fPrintHeader ()
{
   global $default;

   include_once($default->owl_fs_root ."/lib/header.inc");
   include_once($default->owl_fs_root ."/lib/userheader.inc");

   print("<center>\n");

   print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_collapse_width\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");
   fPrintButtonSpace(12, 1);
   print("<table class=\"border2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");
   
   if ($default->show_prefs == 1 or $default->show_prefs == 3)
   {
      fPrintPrefs("infobar1", "top");
   }

   fPrintButtonSpace(12, 1);
}

function fPrintFooter ()
{
   global $default;
   if ($default->show_prefs == 2 or $default->show_prefs == 3)
   {  
      fPrintPrefs("infobar2");
   }  
   print("</td></tr></table>\n");
   include($default->owl_fs_root ."/lib/footer.inc");
}

function printThankYou($username)
{
   global $default, $language;
   global $owl_lang;
   $sql = new Owl_DB;
   $sql->query("SELECT * FROM $default->owl_users_table WHERE username = '$username' and disabled='1'");
   $sMessage = "";
   if ($sql->num_rows($sql) == 1)
   {
      $sMessage = $owl_lang->thank_you_3;
   }
   
   fPrintHeader();

   print("<table width=\"100%\"><tr><td class=\"form1\">");
   fPrintSectionHeader($owl_lang->thank_you_1, "admin3");
   print("</td></tr></table>");
   fPrintButtonSpace(12, 1);

   print("<table width=\"100%\"><tr><td class=\"form1\" align=\"center\">");
   print("<b>$username</b> Created.<br />$owl_lang->thank_you_2<br />$sMessage");
   print("</td></tr></table>");

   fPrintButtonSpace(12, 1);

   if ($default->show_prefs == 2 or $default->show_prefs == 3)
   {
      fPrintPrefs("infobar2");
   }
   print("</td></tr></table>\n");
   include($default->owl_fs_root ."/lib/footer.inc");
}

function printuser($name = "", $username = "", $email = "")
{
   global $owl_lang;
   global $default, $captcha;

   print("<form enctype=\"multipart/form-data\" action=\"register.php\" method=\"post\">\n");
   print("<input type=\"hidden\" name=\"myaction\" value=\"newuser\"></input>");
   print("<input type=\"hidden\" name=\"currentdb\" value=\"$default->owl_current_db\"></input>");
   print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
   print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"20\">$private_key $owl_lang->register</td></tr>\n");
   print("<tr>\n");
   print("<td align=\"left\" valign=\"top\">\n");
   print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
   //print("<tr>\n");
   //print("<td class=\"form1\">");
   //print($captcha->display_captcha());
   //print("<td class=\"form1\">");
if ($default->registration_using_captcha)
{
   fPrintFormTextLine( "$owl_lang->captcha_typein: " .$captcha->display_captcha() , "private_key", "5", "");
}
   //print("<input class=\"captcha\" type=\"text\" name=\"private_key\" value=\"\" maxlength=\"'.$this->chars.'" size="'.$this->chars.'">
   //print("</td>\n");
   //print("</tr>\n");
   fPrintFormTextLine($owl_lang->full_name . ":" , "name", "40", $name);
   fPrintFormTextLine($owl_lang->username . ":" , "username", "", $username);
   fPrintFormTextLine($owl_lang->email . ":" , "email", "40", $email);

   print("<tr>\n");
   print("<td class=\"form1\">");
   fPrintButtonSpace(1, 1);
   print("</td>\n");

   print("<td class=\"form2\" width=\"100%\">");
   fPrintSubmitButton($owl_lang->submit, $owl_lang->register, "submit", "register_btn_x");
   fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
   print("</td>\n");
   print("</tr>\n");
   print("</table>\n");
   print("</td></tr></table>\n");
   print("</form>\n");
   fPrintButtonSpace(12, 1);

   fPrintFooter();
} 

function fPrintChangPass()
{
   global $owl_lang;
   global $default;
   global $uid, $parent;

   print("<form enctype=\"multipart/form-data\" action=\"register.php\" method=\"post\">\n");
   print("<input type=\"hidden\" name=\"myaction\" value=\"verpasschange\"></input>");
   print("<input type=\"hidden\" name=\"currentdb\" value=\"$default->owl_current_db\"></input>");
   print("<input type=\"hidden\" name=\"uid\" value=\"$uid\"></input>");
   print("<input type=\"hidden\" name=\"parent\" value=\"$parent\"></input>");
   print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
   print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"20\">$owl_lang->change_pass_title</td></tr>\n");
   print("<tr>\n");
   print("<td align=\"left\" valign=\"top\">\n");
   print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
   fPrintFormTextLine($owl_lang->oldpassword . ":" , "oldpassword", "", "", "", false, "password");
   fPrintFormTextLine($owl_lang->newpassword . ":" , "newpassword", "", "", "", false, "password");
   fPrintFormTextLine($owl_lang->confpassword . ":" , "confpassword", "", "", "", false, "password");
   print("<td class=\"form1\">");
   fPrintButtonSpace(1, 1);
   print("</td>\n");
   print("<td class=\"form2\" width=\"100%\">");
   fPrintSubmitButton($owl_lang->btn_change_passwd, $owl_lang->alt_btn_change_passwd, "submit", "getpasswd_btn_x");
   fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
   print("</td>\n");
   print("</tr>\n");
   print("</table>\n");
   print("</td></tr></table>\n");
   fPrintButtonSpace(12, 1);
   print("</form>\n");
   fPrintFooter();

} 

function printgetpasswd()
{
   global $owl_lang;
   global $default;

   print("<form enctype=\"multipart/form-data\" action=\"register.php\" method=\"post\">\n");
   print("<input type=\"hidden\" name=\"myaction\" value=\"getpasswd\"></input>");
   print("<input type=\"hidden\" name=\"currentdb\" value=\"$default->owl_current_db\"></input>");
   print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
   print("<tr><td class=\"browse0\" width=\"100%\" colspan=\"20\">$owl_lang->send_pass</td></tr>\n");
   print("<tr>\n");
   print("<td align=\"left\" valign=\"top\">\n");
   print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");


   if (count($default->owl_db_display_name) > 1)
   {
      print("    <tr>\n");
      print("      <td class=\"form1\">$owl_lang->repository_list:<br /></td>\n");
      print("      <td class=\"form1\">\n");
      print("         <select class=\"fpull1\" name=\"currentdb\" size=\"1\">\n");
                                                                                                                   
      $i = 0;
      foreach($default->owl_db_display_name as $database)
      {
         print("<option value=\"$i\" ");
         if (isset($_COOKIE["owl_dbid"]))
         {
            $iDefaultDB = $_COOKIE["owl_dbid"];
         }
         else
         {
            $iDefaultDB = $default->owl_default_db;
         }
         if ( $i == $iDefaultDB)
         {
            print("selected=\"selected\"");
         }
         print(">$database</option>\n");
         $i++;
      }
      print("         </select>\n");
      print("       </td>\n");
      print("     </tr>\n");
   }

   fPrintFormTextLine($owl_lang->forgot_username . ":" , "username", "", $username);
   print("<tr>\n");
   print("<td class=\"form1\">");
   fPrintButtonSpace(1, 1);
   print("</td>\n");
   print("<td class=\"form2\" width=\"100%\">");
   fPrintSubmitButton($owl_lang->send_pass, $owl_lang->send_pass, "submit", "getpasswd_btn_x");
   fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
   print("</td>\n");
   print("</tr>\n");
   print("</table>\n");
   print("</td></tr></table>\n");
   fPrintButtonSpace(12, 1);
   print("</form>\n");

   fPrintFooter();
} 

if ($myaction == "newuser")
{
   $password = GenRandPassword();

if ($default->registration_using_captcha)
{
   if ($captcha->validate_submit() <> 1)
   {
      fPrintHeader();
      print("<table width=\"100%\"><tr><td>");
      fPrintSectionHeader("$owl_lang->err_captcha_auth", "admin3");
      print("</td></tr><table>");
      printuser($name, $username, $email);
      exit;
   } 
}
   if ($email == "" || $name == "" || $username == "")
   {
      fPrintHeader();
      print("<table width=\"100%\"><tr><td>");
      fPrintSectionHeader($owl_lang->err_req, "admin3");
      print("</td></tr><table>");
      //print("$owl_lang->err_req");
      printuser($name, $username, $email);
   } 
   else
   {
      if (!fbValidUsername( $username ))
      {
         $sErrorMessage = sprintf($owl_lang->err_username_not_long_enough, $username, strlen(trim($username)), $default->min_username_length);
         printError($sErrorMessage);
      }

      $sql = new Owl_DB;
      $sql->query("SELECT * FROM $default->owl_users_table WHERE username = '$username'");
      if ($sql->num_rows($sql) > 0) 
      {
         printError("$owl_lang->err_user_exists<br />$owl_lang->username");
      }
      $sql->query("SELECT * FROM $default->owl_users_table WHERE name = '$name'");
      if ($sql->num_rows($sql) > 0) 
      {
         printError("$owl_lang->err_user_exists<br />$owl_lang->full_name");
      }

      $dNow = $sql->now();


      if ($default->self_create_homedir == 1)
      {
         $path = find_path($default->self_reg_homedir);

         if (!file_exists("$default->owl_FileDir/$path/$username"))
         {
            mkdir($default->owl_FileDir . "/" . $path . "/" . $username, $default->directory_mask);
         }

         $sql->query("INSERT INTO $default->owl_folders_table (name,parent,security,description,groupid,creatorid, password, smodified) values ('$username', '$default->self_reg_homedir', '54', '', '$default->self_reg_group', '-1', '', $dNow)");
                                                                                                                                                                           
         $iHomeDir = $sql->insert_id($default->owl_folders_table, 'id');
         $iInitial = $iHomeDir;
      }
      else
      {
         $iHomeDir = $default->self_reg_homedir;
         $iInitial = $default->self_reg_firstdir;

      }

       $sql->query("INSERT INTO $default->owl_users_table (groupid,username,name,password,quota_max,quota_current,email,notify,attachfile,disabled,noprefaccess,language,maxsessions,curlogin, lastlogin,useradmin,newsadmin,buttonstyle, homedir, firstdir, user_auth ) VALUES ('$default->self_reg_group', '$username', '$name', '" . md5($password) . "', '$default->self_reg_quota', '0', '$email', '$default->self_reg_notify','$default->self_reg_attachfile', '$default->self_reg_disabled', '$default->self_reg_noprefacces', '$default->owl_lang', '$default->self_reg_maxsessions', $dNow, $dNow, '0', '0', '$default->system_ButtonStyle', '$iHomeDir','$iInitial', '$default->auth')");

      $iNewUserID =  $sql->insert_id($default->owl_users_table, 'id');

      if ($default->self_create_homedir == 1)
      {
         $sql->query("UPDATE $default->owl_folders_table set creatorid = '$iNewUserID' where id = '$iHomeDir'");
      }

      $sql->query("SELECT email FROM $default->owl_users_table WHERE username = 'admin'");
      $sql->next_record();
      $ccto = $sql->f("email");
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
         if ($ccto != "")
         {
            $mail->AddCC("$ccto");
         }
      } 
      else
      {
         if ($ccto != "")
         {
            $mail->AddAddress("$ccto");
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

      $aBody = fGetMailBodyText(SELF_REG_USER);

      $mail->Subject = $aBody['SUBJECT'];

      $aBody['HTML'] = ereg_replace("\%USERNAME\%", $username , $aBody['HTML'] );
      $aBody['TXT'] = ereg_replace("\%USERNAME\%", $username , $aBody['TXT'] );

      $aBody['HTML'] = ereg_replace("\%NEW_PASSWORD\%", $password . "<br />$sHtmlLink ", $aBody['HTML'] );
      $aBody['TXT'] = ereg_replace("\%NEW_PASSWORD\%", $password . "\n $link", $aBody['TXT'] );


      $mail->altBody = $aBody['TXT'];
      $mail->Body = $aBody['HTML'];

      if (!$mail->Send())
      {
         print("$owl_lang->err_email<br />");
         print("$mail->ErrorInfo");
         $sql->query("DELETE FROM $default->owl_users_table WHERE username = '$username' AND name = '$name' AND password = '" . md5($password) . "' AND  email='$email'");
      } 
      printThankYou($username);

      owl_syslog(USER_REG, $iNewUserID, 0, 0, "$owl_lang->self_passwd $email", "LOGIN");

      exit;
   } 
} 
elseif ($myaction == "verpasschange")
{
   $bError = false;
   $sMsg = "";
   $sql = new Owl_DB;
   $sql->query("SELECT id FROM $default->owl_users_table WHERE id = '" . $uid ."' and password = '" . md5(stripslashes($oldpassword)) . "'");
   if ($sql->num_rows() == 0)
   {
      $sMsg = $owl_lang->err_old_pass_ver_failed;
      $bError = true;
   }
   if ($newpassword != $confpassword)
   {
      $sMsg .= $owl_lang->err_new_confirm_different;
      $bError = true;
   }
   if (!fbValidPassword($newpassword))
   {
      $sMsg .= $owl_lang->err_pass_restriction_1;
      $sMsg .= $owl_lang->err_pass_restriction_2;
      $sMsg .= $owl_lang->err_pass_restriction_3;
      $bError = true;
   }
   if (fbCheckForPasswdReuse($newpassword, $uid) === true)
   {
      $sMsg .= "$owl_lang->err_cant_reuse_passwords";
      $bError = true;
   }

   if ($bError)
   {
      fPrintHeader();
      print("<table width=\"100%\"><tr><td>");
      fPrintSectionHeader($sMsg, "admin3");
      print("</td></tr><table>");
      fPrintChangPass();
   }
   else
   {
      $sql->query("UPDATE $default->owl_users_table SET change_paswd_at_login = '0', password = '" . md5($confpassword) . "' WHERE  id = '$uid' and password = '" . md5($oldpassword) . "'");

      $session = new Owl_Session;
      $vuid = $session->Open_Session(0, $uid);
      $id = 1;
                                                                                                                                                                       
      $sql->query("SELECT name, curlogin, groupid FROM $default->owl_users_table WHERE id = '" . $uid . "'");
      $sql->next_record();
      $curlogin = $sql->f("curlogin");
      $usergroupid = $sql->f("groupid");
      $sUname = $sql->f("name");

      owl_syslog(LOGIN, $uid, 0, 0, $owl_lang->log_login_det . $sUname, "LOGIN");
                                                                                                                                                                       
      $sql->query("UPDATE $default->owl_users_table SET lastlogin = '" . $curlogin . "' WHERE id = '" . $uid . "'");
      $dNow = $sql->now();
      $sql->query("UPDATE $default->owl_users_table SET passwd_last_changed = $dNow, login_failed = '0', curlogin = $dNow WHERE id = '" . $uid . "'");

      $userid = $uid;
                                                                                                                                                                       
      header("Location: browse.php?sess=" . $vuid->sessdata["sessid"] . "&parent=" . $parent );
   }
   exit;
} 
elseif ($myaction == "changepass")
{
   fPrintHeader();
   fPrintChangPass();
   exit;
} 
elseif ($myaction == "forgot")
{
   fPrintHeader();
   printgetpasswd();

} 
elseif ($myaction == "getpasswd")
{
   $password = GenRandPassword();
   $sql = new Owl_DB;

   $sql->query("SELECT * FROM $default->owl_users_table WHERE username = '$username' or email ='$username' AND id <> '1' AND disabled = '0'");

   $failed = false;

   if ($sql->num_rows() == 0) 
   {
      $failed = true;
   }

   printThankYou($username);

   if ($failed === false)
   {
      $sql->query("SELECT id, username, email, user_auth FROM $default->owl_users_table WHERE username = '$username' or email ='$username' AND id <> '1' AND disabled = '0'");
      $sql->next_record();
      $email = $sql->f("email");
      $sUserName = $sql->f("username");
      if (is_null($sql->f("user_auth")))
      {
         $sUserAuth = "0";
      }
      else
      {
         $sUserAuth = trim($sql->f("user_auth"));
      }

      $mail = new phpmailer();

      $aBody = fGetMailBodyText(NEW_PASSWORD);

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
      $mail->Subject = $aBody['SUBJECT'];

      if ($sUserAuth == "0")
      { 
         $link = $default->owl_notify_link . "index.php" ;
         $sHtmlLink = "<a href=\"" . $link . "\">$owl_lang->login</a>";

         $aBody['HTML'] = ereg_replace("\%USERNAME\%", $sUserName , $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%USERNAME\%", $sUserName , $aBody['TXT'] );

         $aBody['HTML'] = ereg_replace("\%NEW_PASSWORD\%", $password . "<br />$sHtmlLink ", $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%NEW_PASSWORD\%", $password . "\n $link", $aBody['TXT'] );

         $mail->Body =  $aBody['HTML'];
         $mail->altBody =  $aBody['TXT'];
      }
      else
      {
         $mail->Body = "<html><body>" . $owl_lang->pass_change_email_1 . "<br />";
         $mail->Body .= $owl_lang->pass_change_email_2 . $default->auth_type[$sUserAuth][1] ." <br />";
         $mail->Body .= $owl_lang->pass_change_email_3 . "<br /><br />";
         $link = $default->owl_notify_link . "index.php" ;
         $mail->Body .= "<a href=\"" . $link . "\">$owl_lang->login</a>";
         $mail->Body .= "</body></html>";
      }
      if (!$mail->Send())
      {
         print("$owl_lang->err_email<br />");
         print("$mail->ErrorInfo");
      } 
      $sql->query("UPDATE $default->owl_users_table set password = '" . md5($password) . "' WHERE username = '$sUserName'");
      $sql->query("SELECT id FROM $default->owl_users_table WHERE username = '$sUserName'");
      $sql->next_record();
      owl_syslog(FORGOT_PASS, $sql->f("id"), 0, 0, "$owl_lang->self_passwd $email", "LOGIN");
   }
   exit;
} 
elseif ($myaction == "register")
{
   fPrintHeader();
   printuser();
   exit;
} 
else
{
   header("Location: " . $default->owl_root_url . "/index.php?login=1");
   exit;
}
?>
