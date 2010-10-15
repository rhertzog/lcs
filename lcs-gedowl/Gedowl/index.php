<?php
/**
 * index.php -- Main page
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 * small change here
 * 
 * $Id: index.php,v 1.20 2007/07/11 00:27:47 b0zz Exp $
 */
//modif misterphi
include "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers,$loguin) = isauth();
if ($idpers) {
		
     $_POST[password]= "";
     $_POST[loginname]=$loguin;
	 

	}
else {header("Location: ../../lcs/auth.php");  exit;}
//eom 
ob_start();
   
if (bcheckLibExists(dirname(__FILE__)."/config/owl.php")) require_once(dirname(__FILE__)."/config/owl.php");
$out = ob_get_clean();

if (bcheckLibExists($default->owl_fs_root ."/lib/disp.lib.php")) require_once($default->owl_fs_root ."/lib/disp.lib.php");

$default->owl_lang =  fGetBrowserLanguage();

if (bcheckLibExists($default->owl_fs_root ."/lib/owl.lib.php")) require_once($default->owl_fs_root ."/lib/owl.lib.php"); 
if (bcheckLibExists($default->owl_fs_root ."/lib/security.lib.php")) require_once($default->owl_fs_root ."/lib/security.lib.php"); 
if (bcheckLibExists($default->owl_fs_root ."/scripts/phpmailer/class.phpmailer.php")) require_once($default->owl_fs_root ."/scripts/phpmailer/class.phpmailer.php");

//modif misterphi
$sql = new Owl_DB;
$sqluser="SELECT * FROM $default->owl_users_table WHERE username = '$loguin'";
        $sql->query($sqluser);
		if ($sql->num_rows() == 0)
            {
			header("Location: ./error_user.php");  exit;
			}
//eom

if (isset($_COOKIE["owl_sessid"]) and $default->remember_me)
{
   if ($login ==  "0")
   {
      if (!(strcmp($login, "logout") == 0))
      {
         if ( isset($_POST[loginname]) and isset($_POST[password]))
         {
            $sql = new Owl_DB;

            $sess = $_COOKIE["owl_sessid"];

            if ($default->active_session_ip) 
            {
               $sql->query("DELETE FROM $default->owl_sessions_table WHERE sessid = '$sess' and ip <> '0'");
            } 
            else 
            {
               $sql->query("DELETE FROM $default->owl_sessions_table WHERE sessid = '$sess' and ip = '0'");
            }
            setcookie ("owl_sessid", "");
         }
         else
         {
            $sess = $_COOKIE["owl_sessid"];
            $sql = new Owl_DB;
            $sql->query("SELECT usid FROM $default->owl_sessions_table WHERE sessid = '$sess'");
            $sql->next_record();
            $uid = $sql->f("usid");

            $sql->query("SELECT curlogin FROM $default->owl_users_table WHERE id = '$uid'");
            $sql->next_record();
            $curlogin = $sql->f("curlogin");

            $sql->query("update $default->owl_users_table set lastlogin = '" . $curlogin . "' WHERE id = '$uid'");
            $dNow = $sql->now();
            $sql->query("update $default->owl_users_table set curlogin = $dNow WHERE id = '$uid'");

            
            if (isset($parent) and is_numeric($parent))
            {
               header("Location: browse.php?sess=$sess&parent=$parent");
            }
            else
            {
               header("Location: browse.php?sess=$sess");
            }
            exit;
         }
      }
   }
}
else
{
   setcookie ("owl_sessid", "");
}

// 
// Function to check if the required libraries exists
// and are readable by the web server.
// and issue a more significant message
// Maybe we need this in other files as well, I'll wait and
// see.


function fPrintLoginPage($message = "")
{
   global $default, $owl_lang, $language, $parent, $fileid, $anon_disabled, $folderid ;


   print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
   print("<tr><td class=\"logo\" width=\"100%\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/$default->owl_logo\" border=\"0\" alt=\"$default->site_title\"></img></td></tr>\n");
   print("</table>\n\n");

   if (!empty($message))
   {
      print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
      print("<tr><td class=\"note\" colspan=\"3\">$message</td></tr>\n");
      print("</table>\n\n");
   }
   else
   {
      print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
      print("<tr><td class=\"note\" colspan=\"3\">&nbsp;<br /></td></tr>\n");
      print("</table>\n\n");
   }
   print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">");
   print("<form name=\"login\" action=\"index.php\" method=\"post\">");
   
   if (isset($fileid) and is_numeric($fileid))
   {
      print "<input type=\"hidden\" name=\"parent\" value=\"$parent\"></input>\n";
      print "<input type=\"hidden\" name=\"fileid\" value=\"$fileid\"></input>\n";
   }
   else
   {
      if (isset($parent) and is_numeric($parent))
      {
         print "<input type=\"hidden\" name=\"parent\" value=\"$parent\"></input>\n";
      }
   }

   if (isset($folderid) and is_numeric($folderid))
   {
      print "<input type=\"hidden\" name=\"folderid\" value=\"$folderid\"></input>\n";
   }

   print("<tr>\n");
   print("  <td width=\"50%\">&nbsp;<br /></td>\n");
   
   print("  <td class=\"shadow\">\n\n");
   print("    <table class=\"box\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
   print("    <tr><td class=\"title\">$default->site_title<br /></td></tr>\n");
   print("    </table>\n");
   
   if (count($default->owl_db_display_name) > 1)
   {
      print("    <table class=\"box\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
      print("    <tr>\n");
      print("    	<td width=\"50%\">&nbsp;<br /></td>\n");
      print("      <td class=\"repo1\">$owl_lang->repository_list:<br /></td>\n");
      print("      <td class=\"repo2\">\n");
      print("         <select class=\"repo3\" name=\"currentdb\" size=\"1\" onchange=\"submit();\">\n");

      $i = 0;
      if (isset($_COOKIE["owl_dbid"]))
      {
         $iDefaultDB = $_COOKIE["owl_dbid"];
      }
      elseif (isset($_POST['currentdb']) and is_numeric($_POST['currentdb']))
      {
         $iDefaultDB = $_POST['currentdb'];
      }
      elseif (isset($default->owl_current_db))
      {
         $iDefaultDB = $default->owl_current_db;
      }
      else
      {
         $iDefaultDB = $default->owl_default_db;
      }

      foreach($default->owl_db_display_name as $database)
      {
         print("<option value=\"$i\""); 
         
         if ( $i == $iDefaultDB)
         {
            print(" selected=\"selected\"");
         }
         print(">$database</option>\n");
         $i++;
      }
      print("         </select>\n");
      print("       </td>\n");
      print("    	<td width=\"50%\">&nbsp;<br /></td>\n");
      print("     </tr>\n");
      print("     </table>\n\n");
   }

   print("    <table class=\"box\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
   print("    <tr><td colspan=\"4\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/x_clear.gif\" width=\"1\" height=\"18\" border=\"0\" alt=\"\"></img></td></tr>\n");
   print("    <tr>\n");
   print("    	<td width=\"50%\">&nbsp;<br /></td>\n");
   print("      <td class=\"text1\"><label for=\"loginname\">" . $owl_lang->username . ":</label><br /></td>\n");
   print("      <td class=\"text2\"><input id=\"loginname\" class=\"input1\" type=\"text\" name=\"loginname\" size=\"20\" maxlength=\"255\" tabindex=\"1\"></input></td>\n");
   print("    	<td width=\"50%\">&nbsp;<br /></td>\n");
   print("    </tr>\n");
   print("    <tr>\n");
   print("    	<td>&nbsp;<br /></td>\n");
   print("      <td class=\"text1\"><label for=\"password\">". $owl_lang->password . ":</label><br /></td>\n");
   print("      <td class=\"text2\"><input id=\"password\" class=\"input1\" type=\"password\" name=\"password\" size=\"20\" maxlength=\"255\" tabindex=\"2\"></input></td>\n");
   print("      <td>&nbsp;<br /></td>\n");
   print("    </tr>\n");

   print("    <tr>\n");
   print("    	<td colspan=\"2\">&nbsp;<br /></td>\n");
   print("     <td class=\"xbutton1\">\n");
   fPrintSubmitButton($owl_lang->btn_login, $owl_lang->alt_btn_login, "submit", "", "", "xbutton2", "xbutton2", "tabindex=\"3\"");
   print("     </td>\n");
   print("      <td>&nbsp;<br /></td>\n");
   print("    </tr>\n");

   if ($default->remember_me)
   {
      print("    <tr>\n");
      print("      <td colspan=\"2\">&nbsp;<br /></td>\n");
      print("      <td class=\"remember\">$owl_lang->remember_me_checkbox:<input type=\"checkbox\" id=\"remember\" name=\"rememberme\" value=\"1\"></input></td>\n");
      print("      <td>&nbsp;<br /></td>\n");
      print("    </tr>\n");
   }

   print("    </table>\n");

   if ($anon_disabled != 1)
   {


      if(isset($fileid))
      {
         $sHilite = "?fileid=$fileid";
      }
      print("    <table class=\"box\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
      print("    <tr><td class=\"anon1\"><a class=\"anon2\" href=\"browse.php$sHilite\" onclick=\"delete_cookie ( 'owl_sessid' );\">$owl_lang->anonymous<br /></a></td></tr>\n");
      print("    </table>\n");
   }

   print('<script type="text/javascript">');
   print('document.login.loginname.focus();');
   print('</script> ');

   print("  </td>\n");
   print("  <td width=\"50%\">&nbsp;</td>\n");
   print("</tr>\n");

   print("<tr><td class=\"link1\" colspan=\"3\">&nbsp;\n");

   if ($default->self_reg == 1)
   {
      print("<a class=\"link2\" href=\"register.php?myaction=register&c=$iDefaultDB\">$owl_lang->like_register</a>&nbsp;&nbsp;");
   }

   if ($default->self_reg == 1 and $default->forgot_pass == 1)
   {
      print("|&nbsp;&nbsp;");
   }

   if ($default->forgot_pass == 1)
   {
      print("<a class=\"link2\" href=\"register.php?myaction=forgot&c=$iDefaultDB\">$owl_lang->forgot_pass<br /></a>");
   }

   print("</td></tr>\n");
   print("</form>");
   print("</table>");

   print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
   print("<tr><td class=\"version1\" width=\"15%\">&nbsp;</td><td class=\"version1\" align=\"center\" width=\"70%\"><a class=\"version2\" href=\"http://owl.sourceforge.net/\" target=\"_blank\">" . $owl_lang->engine . ", " . $owl_lang->version . " " . $default->version . "</a></td><td class=\"version1\" width=\"15%\">&nbsp;</td></tr>\n");
   print("</table>\n");
}

// 
function bcheckLibExists ($filename)
{
   global $default;
   if (file_exists("$filename"))
   {
      if (is_readable("$filename"))
      {
         return true;
      } 
      else
      {
         die("<br /><font size=\"4\"><center>$owl_lang->debug_webserver_no_access</center></font>");
      } 
   } 
   else
   {
      die("<br /><font size=\"4\"><center>$owl_lang->debug_file_not_exist</center></font>");
   } 
} 

//if (checkrequirements() == 1)
//{
    //exit;
//}

if (!isset($failure)) $failure = 0;
if (!$login) $login = 1;

if($default->auth == 1 and isset($_SERVER['PHP_AUTH_USER']))
{
   $_POST[loginname] = $_SERVER['PHP_AUTH_USER'];
}



//if (($_POST[loginname] && $_POST[password]) or ($default->auth == 1 and $_POST[loginname] and $login <> "logout"))
if (($_POST[loginname]) or ($default->auth == 1 and $_POST[loginname] and $login <> "logout"))
{
   $verified["bit"] = 0;
   $verified = verify_login($_POST[loginname], $_POST[password]);

   if ($verified["bit"] == 1)
   {
       if ($default->auth == 0)
       {
          $sql = new Owl_DB;
          $sql->query("SELECT change_paswd_at_login, passwd_last_changed, expire_account FROM $default->owl_users_table WHERE id = '" . $verified["uid"] . "'");
          $sql->next_record();
          $sExpiredAccount = $sql->f("expire_account");
          if (empty($sExpiredAccount))
          {
             $dAccountExpire = 0;
          }
          else
          {
             $dAccountExpire = date("d-m-Y H:i:s", strtotime($sql->f("expire_account")));
          } 
          $sPasswdLastChanged = $sql->f("passwd_last_changed");
          if (isset($sPasswdLastChanged))
          {
             $dLastChanged = date("d-m-Y H:i:s", strtotime($sPasswdLastChanged));
          }
          else
          {
             $dLastChanged = 0;
          } 
          $dateTo = date("d-m-Y H:i:s", strtotime('now'));
    
          $diffd = getDateDifference($dLastChanged, $dateTo, 'd');
          $dExpireDiff = getDateDifference($dAccountExpire, $dateTo, 'd');
    
          $userid = $verified["uid"];
          $usergroupid = $verified["group"];
          if ($dExpireDiff > 0 and $sql->f("expire_account") != "")
          {
             owl_syslog(LOGIN_FAILED, $verified["uid"], 0, 0, $owl_lang->log_login_expired . $verified["user"], "LOGIN");
             header("Location: index.php?login=1&failure=2");
             exit;
          }
          
          if (isset($parent) and is_numeric($parent))
          {
             $verified["homedir"] = $parent;
          }
          if ( $sql->f("change_paswd_at_login") == 1 or $diffd > $default->change_password_every)
          {
             if ( $sql->f("change_paswd_at_login") == 1)
             {
                header("Location: register.php?myaction=changepass&uid=" . $verified["uid"] . "&parent=" . $verified["homedir"] . "&c=" . $default->owl_current_db);
                exit;
             }
             else
             {
                if (!fIsAdmin() and $default->change_password_every > 0)
                {
                   header("Location: register.php?myaction=changepass&uid=" . $verified["uid"] . "&parent=" . $verified["homedir"] . "&c=" . $default->owl_current_db);
                   exit;
                }
             }
          } 
          $userid = $verified["uid"];
          $usergroupid = $verified["group"];
       }
       else
       {
          $userid = $verified["uid"];
          $usergroupid = $verified["group"];
       }

      $session = new Owl_Session;
      $uid = $session->Open_Session(0, $verified["uid"]);
      $id = 1;

      /**
       * If an admin signs on We want to se the admin menu
       * Not the File Browser.
       */
      owl_syslog(LOGIN, $verified["uid"], 0, 0, $owl_lang->log_login_det . $verified["user"], "LOGIN");

     if ($default->notify_of_admin_login == 1 and $verified["uid"] == 1) // uid 1 = Administrator
     {
        if ($_SERVER["HTTP_CLIENT_IP"])
        {
           $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif ($_SERVER["HTTP_X_FORWARDED_FOR"])
        {
           $forwardedip = $_SERVER["HTTP_X_FORWARDED_FOR"];
           list($ip, $ip2, $ip3, $ip4) = split (",", $forwardedip);
        } 
        else
        {
           $ip = $_SERVER["REMOTE_ADDR"];
        } 

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
        $mail->AddAddress("$default->notify_of_admin_login_email");
        $mail->AddReplyTo("$default->owl_email_replyto", "OWL Intranet");
                                                                                                                                                                                                    
        $mail->WordWrap = 50; // set word wrap to 50 characters
        $mail->IsHTML(true); // set email format to HTML
        $mail->Subject = $owl_lang->admin_login_subject;
        $mail->Body = "<html><body>";
        $mail->Body .= $owl_lang->admin_login_date  . date($owl_lang->localized_date_format, mktime()) . "<br />";
        $mail->Body .= $owl_lang->admin_login_from . " " . $ip . " (" .  fGetHostByAddress($ip) . ")<br /><br />";
        $mail->Body .= "</body></html>";

        if (!$mail->Send())
        {
           printError("$owl_lang->err_email", $mail->ErrorInfo);
        }
     }  


      $sql->query("SELECT curlogin, logintonewrec FROM $default->owl_users_table WHERE id = '" . $verified["uid"] . "'");
      $sql->next_record();
      $curlogin = $sql->f("curlogin");
      $logintonewrec = $sql->f("logintonewrec");
      $sql->query("UPDATE $default->owl_users_table SET lastlogin = '" . $curlogin . "' WHERE id = '" . $verified["uid"] . "'");
      $dNow = $sql->now();
      $sql->query("UPDATE $default->owl_users_table SET login_failed = '0' , curlogin = $dNow WHERE id = '" . $verified["uid"] . "'");

      //$usergroupid = $verified["group"];
      //$userid = $verified["uid"];

      $clean = ob_get_contents(); 
      ob_end_clean();  
      if (fIsAdmin(true))
      {
         if (!isset($fileid))
         {
            if($default->admin_login_to_browse_page)
            {
               header("Location: browse.php?sess=" . $uid->sessdata["sessid"] . "&parent=" . $verified["homedir"]);
               exit;
            }
            else
            {
               header("Location: admin/index.php?sess=" . $uid->sessdata["sessid"]);
               exit;
            }
         }
         else
         {
            header("Location: browse.php?sess=" . $uid->sessdata["sessid"] . "&parent=$parent&fileid=$fileid");
            exit;
         }
      } 
      else
      {
         if ($logintonewrec == 1)
         {
            $bNewFiles = 0;
            $sql->query("SELECT id, parent FROM $default->owl_files_table where created > '$curlogin' AND approved = '1'");
            while($sql->next_record())
            {
               if(check_auth($sql->f("id"), "file_download", $userid, false, false) == 1)
               {
                  $sDirectoryPath = get_dirpath($sql->f("parent"));
                  $pos = strpos($sDirectoryPath, "$default->version_control_backup_dir_name");
                  if (!(is_integer($pos) && $pos))
                  {
                     $bNewFiles = 1;
                     break;
                  }
               }
            }

            if ($bNewFiles)
            {
               header("Location: showrecords.php?sess=" . $uid->sessdata["sessid"] . "&type=n");
               exit;
            }
         }


         if (!isset($fileid))
         {
            header("Location: browse.php?sess=" . $uid->sessdata["sessid"] . "&parent=" . $verified["homedir"] );
            exit;
         }
         else
         {
            header("Location: browse.php?sess=" . $uid->sessdata["sessid"] . "&parent=$parent&fileid=$fileid");
            exit;
         }
      } 
   } 
   else
   {
      if ($default->enable_lock_account == 1 and is_numeric($verified["uid"]))
      {
         $sql->query("SELECT login_failed FROM $default->owl_users_table WHERE disabled = '0' AND id = '" . $verified["uid"] . "'");
         while($sql->next_record())
         {
            $iFailures = $sql->f("login_failed") + 1;
            if ($iFailures >=  $default->lock_account_bad_password)
            {
               $sql->query("UPDATE $default->owl_users_table SET disabled = '1', login_failed = '0' WHERE id = '" . $verified["uid"] . "'");
               owl_syslog(LOGIN_FAILED, $verified["uid"], 0, 0, $owl_lang->log_login_too_many_attempts . $verified["user"], "LOGIN");
            }
            else
            {
               $sql->query("UPDATE $default->owl_users_table SET login_failed = '" . $iFailures . "' WHERE id = '" . $verified["uid"] . "'");
            }
         }
      }


      if ($verified["bit"] == 2)
      {
         owl_syslog(LOGIN_FAILED, $verified["uid"], 0, 0, $owl_lang->log_login_det . $verified["user"] . " " . $owl_lang->logindisabled, "LOGIN");
         header("Location: index.php?login=1&failure=2");
      }
      else
      {
         if ($verified["bit"] == 3)
         {
            if ($default->auth == 0)
            {
               owl_syslog(LOGIN_FAILED, $verified["uid"], 0, 0, $owl_lang->log_login_det . $verified["user"] . " " . $owl_lang->toomanysessions, "LOGIN");
               header("Location: index.php?login=1&failure=3");
            }
            else
            {
               printError("$owl_lang->toomanysessions");
            }
         }
         else
         {
            owl_syslog(LOGIN_FAILED, $verified["uid"], 0, 0, $owl_lang->log_login_det . $verified["user"] , "LOGIN");
            header("Location: index.php?login=1&failure=1");
         }
      }
   }
} 

// CHECK IF THE ANONYMOUS USER IS DISABLELD
$sql = new Owl_DB;
$anon_disabled = 1;


$sql->query("SELECT * FROM $default->owl_users_table WHERE id = '$default->anon_user'");
if ($sql->num_rows() == 1)
{
   $sql->next_record();
   $anon_disabled = $sql->f("disabled");
} 

if (($login == 1) || ($failure > 0))
{
   include_once($default->owl_fs_root . "/lib/header.inc");
   //include_once($default->owl_fs_root . "/lib/userheader.inc");

   print("<center>\n");
   if ($failure == 1) 
   {
      $message = "$owl_lang->loginfail<br />\n";
   }
   if ($failure == 2) 
   {
      $message = "$owl_lang->logindisabled<br /><br />\n";
   }
   if ($failure == 3) 
   {
      $message = "$owl_lang->toomanysessions<br />\n";
   }
   if ($failure == 4) 
   {
      $message = "$owl_lang->err_login<br />\n";
   }
   fPrintLoginPage($message);
   include_once($default->owl_fs_root . "/lib/login_footer.inc");
   exit;
} 

if ($login == "logout")
{
   include_once($default->owl_fs_root . "/lib/header.inc");
   //include_once($default->owl_fs_root . "/lib/userheader.inc");
   print("<center>\n");
   if ($default->auth == 0 or $default->auth == 2)
   {
      if (!isset($_COOKIE["owl_sessid"]))
      {
         $sql = new Owl_DB;
         if ($default->active_session_ip) 
         {
            $sql->query("DELETE FROM $default->owl_sessions_table WHERE sessid = '$sess' and ip <> '0'");
         } 
         else 
         {
            $sql->query("DELETE FROM $default->owl_sessions_table WHERE sessid = '$sess' and ip = '0'");
         }
      }
      $tmpDir = $default->owl_tmpdir . "/owltmp.$sess";
      if (file_exists($tmpDir))
      {
         myDelete($tmpDir);
      }

      $message = "$owl_lang->successlogout<br />\n";

      owl_syslog(LOGOUT, $userid, 0, 0, $owl_lang->log_detail, "LOGIN");
   header("Location: ../../lcs/accueil.php");
   echo "ok";exit;
      fPrintLoginPage($message);
   }
   else
   {
      if (!isset($_COOKIE["owl_sessid"]))
      {
         $sql = new Owl_DB;
         if ($default->active_session_ip) 
         {
            $sql->query("DELETE FROM $default->owl_sessions_table WHERE sessid = '$sess' and ip <> '0'");
         } 
         else 
         {
            $sql->query("DELETE FROM $default->owl_sessions_table WHERE sessid = '$sess' and ip = '0'");
         }
      }
      $tmpDir = $default->owl_tmpdir . "/owltmp.$sess";
      if (file_exists($tmpDir))
      {
         myDelete($tmpDir);
      }

      $message = "$owl_lang->successlogout<br />\n";
      owl_syslog(LOGOUT, $userid, 0, 0, $owl_lang->log_detail, "LOGIN");
      fPrintLoginPage($message);

   }
   include_once($default->owl_fs_root . "/lib/login_footer.inc");
   exit;
} 
include_once($default->owl_fs_root . "/lib/login_footer.inc");
?>
