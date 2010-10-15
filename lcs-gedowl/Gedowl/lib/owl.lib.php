<?php

/**
 * owl.lib.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 * $Id: owl.lib.php,v 1.128 2007/10/07 13:42:37 b0zz Exp $
 */

// 
// Controle the level of PHP Messages that are
// Reported

require_once($default->owl_fs_root ."/lib/indexing.lib.php");
require_once($default->owl_fs_root ."/scripts/phpmailer/class.phpmailer.php");

if (file_exists($default->owl_fs_root ."/install"))
{
   die("Please Remove the Intall Directory");
}


if ($default->debug == true)
{ 
   // error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
   // I think we have a bit of work to get Owl to run with E_NOTICE turned On ;-(
   error_reporting (E_ERROR | E_WARNING | E_PARSE);
} 
else
{ 
   error_reporting (0);
} 
// 
// Support for reg.globals off WES
if (substr(phpversion(), 0, 5) >= "4.1.0")
{
   //import_request_variables('pgc', 'owl_');
   import_request_variables('pgc');
}
else
{
   if (!EMPTY($_POST))
   {
      extract($_POST);
   } 
   else
   {
      extract($HTTP_POST_VARS);
   } 
   if (!EMPTY($_GET))
   {
      extract($_GET);
   } 
   else
   {
      extract($HTTP_GET_VARS);
   } 
   if (!EMPTY($_FILE))
   {
      extract($_FILE);
   } 
   else
   {
      extract($HTTP_POST_FILES);
   } 
} 

require_once($default->owl_fs_root ."/lib/sort.lib.php");

if(!empty($_GET[currentdb]))
{
   $default->owl_current_db = $_GET[currentdb];
}
else
{
   if(!empty($_POST[currentdb]))
   {
      $default->owl_current_db = $_POST[currentdb];
   }
  else
  {
      if(empty($default->owl_current_db))
      {
         $default->owl_current_db = 0;
      }
  }
}

if (!isset($sess)) 
{
   if (!isset($_COOKIE["owl_sessid"]))
   {
      $sess = 0;
   }
   else
   {
      $sess = $_COOKIE["owl_sessid"];
   }
}
else
{
   if (isset($_COOKIE["owl_sessid"]))
   {
     $sess = $_COOKIE["owl_sessid"];
   }

}

  // Sanitize the sessions for tampering
   $sess = ereg_replace(" ", "", $sess);

   if (! ereg("^[a-fA-F0-9]", $sess))
   {
      $sess="1";
   }

   if (strlen($sess) > 32)
   {
      $sess="1";
   }

if(!isset($default->owl_FileDir))
{
   $default->owl_FileDir  =  $default->owl_db_FileDir[$default->owl_default_db];
}

if (empty($parent) || !is_numeric($parent))
{
   $parent = $default->HomeDir;
}

if (!isset($expand) or !is_numeric($expand))
{
   $expand = $default->expand;
}

if (empty($curview) || !is_numeric($curview))
{
   $curview = 0;
}

if (!isset($loginname)) 
{
   $loginname = 0;
}
if (!isset($login))
{
   $login = 0;
}

class Owl_DB extends DB_Sql
{
   var $classname = "Owl_DB"; 
   // BEGIN wes changes -- moved these settings to config/owl.php
   // Server where the database resides
   var $Host = ""; 
   // Database name
   var $Database = ""; 
   // User to access database
   var $User = ""; 
   // Password for database
   var $Password = "";

   function Owl_DB()
   {
      global $default;
      if(!isset($default->owl_current_db))
      {
         $db = $default->owl_default_db;
      }
      else
      {
         $db = $default->owl_current_db;
      }

      $this->Host = $default->owl_db_host[$db];
      $this->Database = $default->owl_db_name[$db];
      $this->User = $default->owl_db_user[$db];
      $this->Password = $default->owl_db_pass[$db];

      //$this->Host = $default->owl_db_host[0];
      //$this->Database = $default->owl_db_name[0];
      //$this->User = $default->owl_db_user[0];
      //$this->Password = $default->owl_db_pass[0];
   } 
   // END wes changes
   function haltmsg($msg)
   {
      printf("</td></table><b>$owl_lang->err_database:</b> %s<br>\n", $msg);
      printf("<b>$owl_lang->err_sql</b>: %s (%s)<br>\n",
         $this->Errno, $this->Error);
   } 
} 


if(!empty($sess))
{
   foreach ( $default->owl_db_id as $database )
   {
      $default->owl_current_db = $database;

      $sql = new Owl_DB;
      $sql->query("SELECT * from $default->owl_sessions_table where sessid = '$sess'");
      $sql->next_record();
      $numrows = $sql->num_rows($sql);
      if ($numrows == 1)
      {
         break;
      }
      $default->owl_current_db = null;
   }
}
getprefs();
getuserprefs();

// 
// Set the language from default or from the users file.
// NOTE: the messages here cannot be internationalized
// 
if (!isset($default->sButtonStyle)) 
{
   $default->sButtonStyle = $default->system_ButtonStyle;
}

gethtmlprefs();


if (isset($default->owl_lang))
{
   $langdir = "$default->owl_fs_root/locale/$default->owl_lang";
   if (is_dir("$langdir") != 1)
   {
      die("<br /><font size='4'><center>Path to the 'locale' directory was Not found: $langdir</center></font>");
   } 
   else
   {
      $sql = new Owl_DB;
      $sql->query("SELECT * from $default->owl_sessions_table where sessid = '$sess'");
      $sql->next_record();
      $numrows = $sql->num_rows($sql);
      $getuid = $sql->f("usid");
      if ($numrows == 1)
      {
         $sql->query("SELECT * from $default->owl_users_table where id = '$getuid'");
         $sql->next_record();
         $language = $sql->f("language"); 
         $default->sButtonStyle = $sql->f("buttonstyle");
         if (!$language)
         {
            $language = $default->owl_lang;
         } 
         if (file_exists("$default->owl_fs_root/locale/$language/language.inc"))
         {
            if (is_readable("$default->owl_fs_root/locale/$language/language.inc"))
            {
               require_once("$default->owl_fs_root/locale/$language/language.inc");
               $default->owl_lang = $language;
               if (!empty($owl_lang->charset))
               {
                  $default->charset = $owl_lang->charset;
               }
   $default->auth_type[0][0] = "0";
   $default->auth_type[0][1] = $owl_lang->auth_owl;
   $default->auth_type[1][0] = "1";
   $default->auth_type[1][1] = $owl_lang->auth_http;
   $default->auth_type[2][0] = "2";
   $default->auth_type[2][1] = $owl_lang->auth_pop3;
   $default->auth_type[3][0] = "3";
   $default->auth_type[3][1] = $owl_lang->auth_ldap;

            } 
            else
            {
               die("<br /><font size='4'><center>The webserver does not have read access to:
					     <br />The Language file '$default->owl_fs_root/locale/$language/language.inc'
				             <br />Please fix the permissions and try again</center></font>");
            } 
         } 
         else
         {
            die("<br /><font size='4'><center>The Language file '$default->owl_fs_root/locale/$language/language.inc' does not exists.</center></font>");
         } 
      } 
      else
      {
         if ($sess == 0)
         {
            $language = $default->owl_lang;
         } 
         if (file_exists("$default->owl_fs_root/locale/$default->owl_lang/language.inc"))
         {
            if (is_readable("$default->owl_fs_root/locale/$default->owl_lang/language.inc"))
            {
               require_once("$default->owl_fs_root/locale/$default->owl_lang/language.inc");
            } 
            else
            {
               die("<br /><font size='4'><center>The webserver does not have read access to:
				      	<br />The Language file '$default->owl_fs_root/locale/$default->owl_lang/language.inc'.
					<br />Please fix the permissions and try again</center></font>");
            } 
         } 
         else
         {
            die("<br /><font size='4'><center>The Language file '$default->owl_fs_root/locale/$default->owl_lang/language.inc' does not exists.</center></font>");
         } 
      } 
   } 
} 
else
{
   die("<br /><font size='4'><center>Unable to find language, please specify in config/owl.php.</center></font>");
} 

class Owl_Session
{
   var $sessid;
   var $sessuid;
   var $sessdata;

   function Open_Session($sessid = 0, $sessuid = 0)
   {
      global $default;
      global $rememberme;
      $this->sessid = $sessid;
      $this->sessuid = $sessuid;

      if ($sessid == "0") // if there is no user loged in, then create a session for them
      {

         $current = time();
         $random = $this->sessuid . $current;
         $this->sessid = md5($random);


         $OpenSess = new Owl_DB;

         if ($_SERVER["HTTP_CLIENT_IP"])
         {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
         } 
         elseif ($_SERVER["HTTP_X_FORWARDED_FOR"])
         {
            $forwardedip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            list($ip, $ip2, $ip3, $ip4) = split (",", $forwardedip);
         }
         else
         {
            $ip = $_SERVER["REMOTE_ADDR"];
         }

         if (!$default->active_session_ip)
         {
            $ip = 0;
         }

         if ($rememberme == 1)
         {
            $current = time() +60*60*24*$default->cookie_timeout; 
         }

         if (empty($_POST[currentdb]))
         {
            $iCurrentDB = "0";
         }
         else
         {
            $iCurrentDB = $_POST[currentdb];
         }


         // Clean up the Old sessions before a new one is created 
         // this is in case the user deleted his cookie, but the active session
         // is still in the sessions table
         if ($rememberme == 1 and $default->remember_me)
         {
            $OpenSess->query("DELETE FROM $default->owl_sessions_table  where usid = '$this->sessuid' and currentdb = '$iCurrentDB'");
         }

         $CheckSess = new Owl_DB;
         $CheckSess->query("SELECT * FROM $default->owl_sessions_table WHERE sessid = '$this->sessid' and usid = '$this->sessuid'"); 
         // any matching session ids?
         $numrows = $CheckSess->num_rows($CheckSess);
      if (!$numrows) 
      {
         
         $result = $OpenSess->query("INSERT INTO $default->owl_sessions_table  VALUES ('$this->sessid', '$this->sessuid', '$current', '$ip', '$iCurrentDB')");
      

         if (!$result) 
         {
            die("$owl_lang->err_sess_write");
         }
      }


         if ($rememberme == 1 and $default->remember_me)
         {
            setcookie("owl_dbid", $iCurrentDB, time()+60*60*24*$default->cookie_timeout);
            setcookie ("owl_sessid", $this->sessid, time()+60*60*24*$default->cookie_timeout);
         }
      } 
      // else we have a session id, try to validate it...
      $CheckSess->query("SELECT * FROM $default->owl_sessions_table WHERE sessid = '$this->sessid'"); 
      // any matching session ids?
      $numrows = $CheckSess->num_rows($CheckSess);
      if (!$numrows) die("$owl_lang->err_sess_notvalid"); 
      // return if we are a.o.k.
      while ($CheckSess->next_record())
      {
         $this->sessdata["sessid"] = $CheckSess->f("sessid");
      } 
      return $this;
   } 
} 

function delFile($id, $action, $historical = 0)
{
   global $default, $userid, $parent, $order, $sortorder, $sortname;
   global $owl_lang, $self, $usergroupid;
   if (check_auth($id, "file_delete", $userid) == 1)
   {
      $sql = new Owl_DB;
      if ($type == "url")
      {
         $sql->query("DELETE FROM $default->owl_files_table WHERE id = '$id'");
      } 
      else
      {
         $sql->query("SELECT * FROM $default->owl_files_table WHERE id = '$id'");
         while ($sql->next_record())
         {
            $path = find_path($sql->f("parent"));
            $filename = $sql->f("filename");
            $filesize = $sql->f("f_size");
            $owner = $sql->f("creatorid");
            $parent = $sql->f("parent");
         } 

         $new_quota = fCalculateQuota($filesize, $owner, "DEL");

         if (fIsQuotaEnabled($owner)) 
         {
            $sql->query("UPDATE $default->owl_users_table set quota_current = '$new_quota' WHERE id = '$owner'");
         }

         if ($default->owl_use_fs)
         {
            if (file_exists($default->owl_FileDir . "/" . $path . "/" . $filename))
            { 
               // This is where we move the file to
               // the trash can
               if ($default->collect_trash == 1)
               {
                  $sTrashDir = explode('/', $path);

                  $sCreatePath = $default->trash_can_location . "/" . $default->owl_current_db;
                  if (!file_exists($sCreatePath))
                  {
                     mkdir("$sCreatePath", $default->directory_mask);
                  } 
                  foreach($sTrashDir as $sDir)
                  {
                     $sCreatePath .= "/" . $sDir;
                     if (!file_exists($sCreatePath))
                     {
                        mkdir("$sCreatePath", $default->directory_mask);
                     } 
                  } 
                  if (substr(php_uname(), 0, 7) != "Windows")
                  {
                     //$sTrashSource = escapeshellcmd($default->owl_FileDir . "/" . $path . "/" . $filename);
                     //$sTrashDest = escapeshellcmd($default->trash_can_location . "/" . $default->owl_current_db . "/" . $path . "/" . $filename);
                     $sTrashSource = $default->owl_FileDir . "/" . $path . "/" . $filename;
                     $sTrashDest = $default->trash_can_location . "/" . $default->owl_current_db . "/" . $path . "/" . $filename;
                     $cmd = "mv " . "\"" . $sTrashSource . "\" \"" . $sTrashDest . "\" 2>&1";

                     $lines = array();
                     $errco = 0;
                     $result = myExec($cmd, $lines, $errco);
                     if ($errco != 0)
                     {
                        printError($owl_lang->err_general, $result);
                     }
                  } 
                  else
                  {
                     copy("$default->owl_FileDir/$path/$filename", "$default->trash_can_location/$default->owl_current_db/$path/$filename");
                     unlink($default->owl_FileDir . "/" . $path . "/" . $filename);
                  } 
               } 
               else
               {
                  unlink($default->owl_FileDir . "/" . $path . "/" . $filename);
               } 
            } 
            owl_syslog(FILE_DELETED, $userid, $filename, $parent, $owl_lang->log_detail, "FILE");

            if (file_exists($default->thumbnails_location))
            {
               $handle = opendir($default->thumbnails_location);
               while(false !== ($file = readdir($handle)))
               {
                  //print("F: $file Sub:" . substr($file, 0, 3) . "<br />");
                  list($sThumbFileDb, $sThumbFileId, $sThumbFileName) = split("_", $file);
                                                                                                                                                                          
                  $sDelFileCheck = $sThumbFileDb . "_". $sThumbFileId;
                  if ($sDelFileCheck == $default->owl_current_db . "_" .$id)
                  {
                     unlink($default->thumbnails_location . "/" .$file);
                  }
               }
            }

            if ($action == "file_delete" or $action == "Delete Selected")
            {
               notify_users($usergroupid, 3, $id);
            }

            $sql->query("DELETE FROM $default->owl_files_table WHERE id = '$id'"); 
            // Clean up all monitored files with that id
            $sql->query("DELETE FROM $default->owl_monitored_file_table WHERE fid = '$id'"); 
            // Clean up all comments with this file
            $sql->query("DELETE FROM $default->owl_comment_table WHERE fid = '$id'"); 
            // Clean up all comments with this file
            $sql->query("DELETE FROM $default->owl_docfieldvalues_table WHERE file_id = '$id'"); 
            // Clean up all linked files
            $sql->query("DELETE FROM $default->owl_files_table WHERE linkedto = '$id'"); 
            // Clean up all linked files
            $sql->query("DELETE FROM $default->owl_peerreview_table WHERE file_id = '$id'");
            // Clean up all Acls for this file
            $sql->query("DELETE FROM $default->owl_advanced_acl_table where file_id = '$id'");
            // Clean Up SEARCH Indexes for this file
            fDeleteFileIndexID($id); 
            // Clean up all previous versions as well
            $sql->query("SELECT id FROM $default->owl_folders_table WHERE name='$default->version_control_backup_dir_name' and parent='$parent'");
            if ($sql->num_rows($sql) != 0)
            {
               while ($sql->next_record())
               {
                  $backup_parent = $sql->f("id");
               }                
               $aFirstpExtension = fFindFileFirstpartExtension ($filename);
               $firstpart = $aFirstpExtension[0];
               $file_extension = $aFirstpExtension[1];

               $Quota = new Owl_DB;
               $sql->query("SELECT * FROM $default->owl_files_table WHERE (filename LIKE '" . $firstpart . "\\\_%" . $file_extension . "' AND parent = '$backup_parent') OR (filename = '$filename' AND parent = '$parent') order by major_revision desc, minor_revision desc");
               while ($sql->next_record())
               {
                  $path = find_path($sql->f("parent"));
                  $filename = $sql->f("filename"); 
                  $major_revision = $sql->f("major_revision");
                  $minor_revision = $sql->f("minor_revision");
                  //if (preg_match('/'.$firstpart.'_\d+-\d+'.$file_extension.'/', $filename))
                  if ($filename == $firstpart.'_'.$major_revision.'-'.$minor_revision.".".$file_extension)
                  {
                     owl_syslog(FILE_DELETED, $userid, $filename, $parent, $path, "FILE");
                     // Clean Up SEARCH Indexes for the Backup files
                     fDeleteFileIndexID($sql->f("id"));
                     // Update the Quota for the Backup files
                     if (fIsQuotaEnabled($sql->f("creatorid")))
                     {
                        $new_quota = fCalculateQuota($sql->f("f_size"), $sql->f("creatorid"), "DEL");
                        $Quota->query("UPDATE $default->owl_users_table set quota_current = '$new_quota' WHERE id = '". $sql->f("creatorid") . "'");
                     }
   
                     if (file_exists($default->owl_FileDir . "/" . $path . "/" . $filename))
                     {
                        if ($default->collect_trash == 1)
                        {
                           $sTrashDir = explode('/', $path);
                           $sCreatePath = $default->trash_can_location . "/" . $default->owl_current_db;
                           if (!file_exists($sCreatePath))
                           {
                              mkdir("$sCreatePath", $default->directory_mask);
                           }
                           foreach($sTrashDir as $sDir)
                           {
                              $sCreatePath .= "/" . $sDir ;
                              if (!file_exists($sCreatePath))
                              {
                                 mkdir("$sCreatePath", $default->directory_mask);
                              } 
                           } 
                           if (substr(php_uname(), 0, 7) != "Windows")
                           {
                              $cmd = "mv " . "\"" . $default->owl_FileDir . "/" . $path . "/" . $filename . "\" \"" . $default->trash_can_location  . "/" . $default->owl_current_db . "/" . $path . "/" . $filename . "\" 2>&1";
                              $lines = array();
                              $errco = 0;
                              $result = myExec($cmd, $lines, $errco);
                              if ($errco != 0)
                                 printError($owl_lang->err_general, $result);
                           } 
                           else
                           {
                              rename("$default->owl_FileDir/$path/$filename", "$default->trash_can_location/$default->owl_current_db/$path/$filename");
                           } 
                        } 
                        else
                        {
                           unlink($default->owl_FileDir . "/" . $path . "/" . $filename);
   
                        } 
                        $sql->query("DELETE FROM $default->owl_files_table WHERE (filename = '$filename' AND parent = '$backup_parent')");
                     }
                  } 
                  else
                  {
                    owl_syslog(FILE_CHANGED, $userid, $filename, $parent, $firstpart.'_'.$major_revision.'-'.$minor_revision.$file_extension, "FILE");
                  }
               } 
            } 
         } 
         else
         {
            $sql->query("DELETE FROM $default->owl_files_table WHERE id = '$id'");
            // Clean up all monitored files with that id
            $sql->query("DELETE FROM $default->owl_monitored_file_table WHERE fid = '$id'");
            $sql->query("DELETE FROM $default->owl_files_data_table WHERE id = '$id'");
            $sql->query("DELETE FROM $default->owl_comment_table WHERE fid = '$id'");
            // Clean up all comments with this file
            $sql->query("DELETE FROM $default->owl_docfieldvalues_table WHERE file_id = '$id'");
            // Clean up all linked files
            $sql->query("DELETE FROM $default->owl_files_table WHERE linkedto = '$id'");
            // Clean up all linked files
            $sql->query("DELETE FROM $default->owl_peerreview_table WHERE file_id = '$id'");
            // Clean Up SEARCH Indexes for this file
            fDeleteFileIndexID($id);
            owl_syslog(FILE_DELETED, $userid, $filename, $parent, $owl_lang->log_detail, "FILE");
         } 
         sleep(.5);
      } 

      if (fid_to_name($parent) == "$default->version_control_backup_dir_name" and $self == "log")
      {
         $parent = owlfolderparent($parent);
      }
      if ($historical == 0)
      {
         displayBrowsePage($parent);
      }
   } 
   else
   {
      if ($action == "file_delete")
      {
         printError($owl_lang->err_nofiledelete);
      } 
   } 
} 
function fCheckIfReviewer ($file_id)
{
   global $default, $userid;
   $dbCheck = new Owl_DB;

   $dbCheck->query("SELECT file_id from $default->owl_peerreview_table where reviewer_id = '$userid' and file_id = '$file_id' ");
   if ($dbCheck->num_rows() > 0)
   {
      return true;
   }
   return false;   
}

function fCountFileType ($id, $type)
{
   global $default, $userid;
   $GetItems = new Owl_DB;

   $GetItems->query("SELECT id FROM $default->owl_files_table WHERE url = '$type' AND parent = '$id' AND approved = '1'");

   if ($default->restrict_view == 1)
   {
      while ($GetItems->next_record())
      {
         $bFileDownload = check_auth($GetItems->f("id"), "file_download", $userid, false, false);
         if ($bFileDownload)
         {
            $iFileCount++;
         }
     }
   }
   else
   {
      $iFileCount = $GetItems->num_rows();
   }
   return $iFileCount;
}

// --------------------------------
function check_for_sess ($uid)
{
   global $default;

   $mysess = 0;
   $sql = new Owl_DB;
   $sql->query("SELECT * from $default->owl_sessions_table where usid = '$uid' and ip = '0' ORDER BY lastused ASC");
   while ($sql->next_record())
   {
      $time = time();
      if (($time - $sql->f("lastused")) <= $default->owl_timeout)
      {
         $mysess = $sql->f("sessid");
         if (!($default->remember_me))
         {
            $sql->query("UPDATE $default->owl_sessions_table set lastused = '$time' where sessid = '$mysess'");
         }
         break;
      } 
   } 
   if ($mysess == 0)
   {
      $session = new Owl_Session;
      $userid = $session->Open_Session(0, $uid);
      $mysess = $userid->sessdata["sessid"];
      $sql->query("UPDATE $default->owl_sessions_table set ip = '0' where sessid = '$mysess'");
   } 
   return $mysess;
} 

function notify_file_owner($iFileId, $comment)
{
   global $default, $userid;
   //global $owl_lang;

   $sql = new Owl_DB;
   $getuser = new Owl_DB;

   $sql->query("SELECT * from $default->owl_files_table where id = '$iFileId'");

   $sql->next_record();

   $iCreatorId = $sql->f("creatorid");
   $sFileName = $sql->f("filename");
   $iParent = $sql->f("parent");

   $getuser->query("SELECT language, email,comment_notify,name from $default->owl_users_table where id = '$iCreatorId' and disabled = '0'");
   $getuser->next_record();

   if ($getuser->f("comment_notify") == 1 and $userid <> $iCreatorId)
   {
      $language = $getuser->f("language");
      if (empty($language))
      {
         $language = $default->owl_lang;
      }
      if (file_exists("$default->owl_fs_root/locale/$language/language.inc"))
      {
         include("$default->owl_fs_root/locale/$language/language.inc");
      }

      $aBody = fGetMailBodyText(NEW_COMMENT,  $iFileId );

      $aBody['HTML'] = ereg_replace("\%FILE_COMMENT\%", $comment, $aBody['HTML'] );
      $aBody['TXT'] = ereg_replace("\%FILE_COMMENT\%", $comment, $aBody['TXT'] );

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
      $mail->AddAddress($getuser->f("email"));
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
} 

function notify_monitored_folders ($parent, $filename)
{
   global $default, $userid;
   //global $owl_lang;

   $sql = new Owl_DB;
   $getuser = new Owl_DB; 
   // For each user that want to receive notification of an UPDATE of this file
   
   if ($default->owl_monitor_subfolders) 
   { 
      $sql->query(get_parents_sql($parent, $filename)); 
   } 
   else 
   { 
      $sql->query("SELECT f.id, fid, name, description, parent, userid, filename from $default->owl_files_table f, $default->owl_monitored_folder_table m where f.filename = '$filename' and f.parent = '$parent' and m.fid = '$parent'"); 
   } 

   while ($sql->next_record())
   {
      $CurrentUser = $sql->f("userid");
      $getuser->query("SELECT id, email,language,attachfile from $default->owl_users_table where id = '$CurrentUser' and disabled = '0'");
      $getuser->next_record();

      if (check_auth($sql->f("id"), "file_download", $getuser->f("id")) == 1 and $getuser->f("id") != $userid)
      {
         // END BUG 548994 More Below
         $path = find_path($sql->f("parent"));
         $filename = $sql->f("filename"); 
         // $newpath = ereg_replace(" ","%20",$path);
         $newpath = $path; 
         // $newfilename = ereg_replace(" ","%20",$sql->f("filename"));
         $newfilename = $sql->f("filename");
         $DefUserLang = $getuser->f("language");
         if(empty($DefUserLang))
         {         
            $DefUserLang = $default->owl_lang; 
         }
         require("$default->owl_fs_root/locale/$DefUserLang/language.inc");

         $r = preg_split("(\;|\,)", $getuser->f("email"));
         reset ($r);
         while (list ($occ, $email) = each ($r))
         { 
            if ($default->generate_notify_link_session === true)
            {
               $tempsess = check_for_sess($getuser->f("id"));
            }
            else
            {
               $tempsess = "0";
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
            $mail->AddAddress($email);
            $mail->AddReplyTo("$default->owl_email_replyto", "OWL Intranet");
            $mail->WordWrap = 50; // set word wrap to 50 characters
            $mail->IsHTML(true); // set email format to HTML
            $mail->Subject = "$default->owl_email_subject $owl_lang->notif_subject_monitor";
            if ($type != "url")
            {
               if ($getuser->f("attachfile") == 1)
               {
                  $desc = stripslashes($sql->f("description"));
                  $mail->Body = "<html><body>" . "$owl_lang->notif_msg<br /><br />" . "$owl_lang->title: " . $sql->f("name") . "<br /><br />$owl_lang->description: $desc <br /><br />";
                  $mail->Body .= $owl_lang->owl_path . $path . "/" . $filename;
                  $mail->Body .= "<br /><br />" . $owl_lang->notif_user . " " .uid_to_name($userid);

                  $sFsPath = fCreateWaterMark($sql->f("id"));

                  if (! $sFsPath === false)
                  {
                     $sAttachPath = $sFsPath;
                  }
                  else
                  {
                     if (!$default->owl_use_fs)
                     {
                        $sAttachPath = fGetFileFromDatbase($fid);
                     }
                     else
                     {
                        $sAttachPath = "$default->owl_FileDir/$newpath/$newfilename";
                     }
                  }

                  $mimeType = fGetMimeType($newfilename);
                  $mail->AddAttachment($sAttachPath, "" , "base64" , "$mimeType");
               } 
               else
               {
                  $desc = stripslashes($sql->f("description"));
                  $link = $default->owl_notify_link . "browse.php?sess=$tempsess&parent=" . $sql->f("parent") . "&expand=1&fileid=" . $sql->f("id");
                  $mail->Body = "<html><body>" . "$owl_lang->notif_msg<br /><br />" . "$owl_lang->title: " . $sql->f("name") . "<br /><br />URL: <A HREF=" . $link . ">" . $link . "</A><br /><br />$owl_lang->description: $desc <br /><br />";
                  $mail->Body .= $owl_lang->owl_path . $path . "/" . $filename;
                  $mail->Body .= "<br /><br />" . $owl_lang->notif_user . " " .uid_to_name($userid);
               } 
            } 
            else
            {
               $desc = stripslashes($sql->f("description"));
               $mail->Body = "<html><body>" . "URL: <A HREF=" . $newfilename . ">" . $newfilename . "</A> <br /><br />$owl_lang->notif_msg<br /><br />" . "$owl_lang->title: " . $sql->f("name") . "<br /><br />$owl_lang->description: $desc <br /><br />";
               $mail->Body .= $owl_lang->owl_path . $path . "/" . $filename;
               $mail->Body .= "<br /><br />" . $owl_lang->notif_user . " " .uid_to_name($userid);
               //$mail->altBody = "URL: $newfilename \n\n$owl_lang->notif_msg_alt\n\n" . "$owl_lang->title: " . $sql->f("name") . "\n\n $owl_lang->description: $desc \n\n";
               //$mail->altBody .= $owl_lang->owl_path . $path . "/" . $filename;
            } 
            $mail->Body .= "</body></html>";
            if (!$mail->Send() && $default->debug == true)
            {
               printError("DEBUG: " . $owl_lang->err_email, $mail->ErrorInfo);
            } 
            if (!$default->owl_use_fs && $sql->f("attachfile") == 1)
            {
               unlink("$default->owl_FileDir/$newfilename");
            } 
         } 
      } 
   } 
} 
// --------------------------------
function notify_monitored ($fid, $type)
{
   global $default, $userid;
   //global $owl_lang;

   $sql = new Owl_DB;
   $getuser = new Owl_DB; 
   // For each user that want to receive notification of an UPDATE of this file
   
   $sql->query("SELECT * from $default->owl_files_table f, $default->owl_monitored_file_table m where f.id = m.fid and m.fid = '$fid'");

   while ($sql->next_record())
   {
      $CurrentUser = $sql->f("userid");
      $getuser->query("SELECT id, email,language,attachfile from $default->owl_users_table where id = '$CurrentUser' and disabled = '0' and notify = '0'");
      $getuser->next_record();

      if (check_auth($fid, "file_download", $getuser->f("id")) == 1 and $getuser->f("id") != $userid)
      {
         // END BUG 548994 More Below
         $path = find_path($sql->f("parent"));
         $filename = $sql->f("filename"); 
         $newpath = $path;
         $newfilename = $sql->f("filename"); 
         $DefUserLang = $getuser->f("language");
         if(empty($DefUserLang))
         {
            $DefUserLang = $default->owl_lang;
         }

         require("$default->owl_fs_root/locale/$DefUserLang/language.inc");

         $r = preg_split("(\;|\,)", $getuser->f("email"));
         reset ($r);
         while (list ($occ, $email) = each ($r))
         { 
            if ($default->generate_notify_link_session === true)
            {
               $tempsess = check_for_sess($getuser->f("id"));
            }
            else
            {
               $tempsess = "0";
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
            $mail->AddAddress($email);
            $mail->AddReplyTo("$default->owl_email_replyto", "OWL Intranet");
            $mail->WordWrap = 50; // set word wrap to 50 characters
            $mail->IsHTML(true); // set email format to HTML
            $mail->Subject = "$default->owl_email_subject $owl_lang->notif_subject_monitor";
            if ($type != "url")
            {
               if ($getuser->f("attachfile") == 1)
               {
                  //$desc = ereg_replace("[\\]", "", $sql->f("description"));
                  $desc = stripslashes($sql->f("description"));
                  $mail->Body = "<html><body>" . "$owl_lang->notif_msg<br /><br />" . "$owl_lang->title: " . $sql->f("name") . "<br /><br />$owl_lang->description: $desc <br /><br />";
                  $mail->Body .= $owl_lang->owl_path . $path . "/" . $filename;
                  $mail->Body .= "<br /><br />" . $owl_lang->notif_user . " " .uid_to_name($userid);
                  //$mail->altBody = "$owl_lang->notif_msg_alt\n\n" . "$owl_lang->title: " . $sql->f("name") . "\n\n $owl_lang->description: $desc \n\n";
                  //$mail->altBody .= $owl_lang->owl_path . $path . "/" . $filename;
                  $sFsPath = fCreateWaterMark($fid);

                  if (! $sFsPath === false)
                  {
                     $sAttachPath = $sFsPath;
                  }
                  else
                  {
                     if (!$default->owl_use_fs)
                     {
                        $sAttachPath = fGetFileFromDatbase($fid);
                     }
                     else
                     {
                        $sAttachPath = "$default->owl_FileDir/$newpath/$newfilename";
                     }
                  }

                  $mimeType = fGetMimeType($newfilename);
                  $mail->AddAttachment($sAttachPath, "" , "base64" , "$mimeType");
               } 
               else
               {
                  //$desc = ereg_replace("[\\]", "", $sql->f("description"));
                  $desc = stripslashes($sql->f("description"));
                  $link = $default->owl_notify_link . "browse.php?sess=$tempsess&parent=" . $sql->f("parent") . "&expand=1&fileid=" . $sql->f("fid");
                  $mail->Body = "<html><body>" . "$owl_lang->notif_msg<br /><br />" . "$owl_lang->title: " . $sql->f("name") . "<br /><br />URL: <A HREF=" . $link . ">" . $link . "</A><br /><br />$owl_lang->description: $desc <br /><br />";
                  $mail->Body .= $owl_lang->owl_path . $path . "/" . $filename;
                  $mail->Body .= "<br /><br />" . $owl_lang->notif_user . " " .uid_to_name($userid);
                  //$mail->altBody = "$owl_lang->notif_msg_alt\n\n" . "$owl_lang->title: " . $sql->f("name") . "\n\n $owl_lang->description: $desc \n\n";
                  //$mail->altBody .= $owl_lang->owl_path . $path . "/" . $filename;
               } 
            } 
            else
            {
               //$desc = ereg_replace("[\\]", "", $sql->f("description"));
               $desc = stripslashes($sql->f("description"));
               $mail->Body = "<html><body>" . "URL: <A HREF=" . $newfilename . ">" . $newfilename . "</A> <br /><br />$owl_lang->notif_msg<br /><br />" . "$owl_lang->title: " . $sql->f("name") . "<br /><br />$owl_lang->description: $desc <br /><br />";
               $mail->Body .= $owl_lang->owl_path . $path . "/" . $filename;
               $mail->Body .= "<br /><br />" . $owl_lang->notif_user . " " .uid_to_name($userid);
               //$mail->altBody = "URL: $newfilename \n\n$owl_lang->notif_msg_alt\n\n" . "$owl_lang->title: " . $sql->f("name") . "\n\n $owl_lang->description: $desc \n\n";
               //$mail->altBody .= $owl_lang->owl_path . $path . "/" . $filename;
            } 
            $mail->Body .= "</body></html>";
            if (!$mail->Send() && $default->debug == true)
            {
               printError("DEBUG: " . $owl_lang->err_email, $mail->ErrorInfo);
            } 
            if (!$default->owl_use_fs && $sql->f("attachfile") == 1)
            {
               unlink("$default->owl_FileDir/$newfilename");
            } 
         } 
      } 
   } 
} 



function notify_reviewer ($iUserId, $iFileId , $usermessage, $doc_action = "", $reason = "")
{
   global $default, $userid;

   $sql = new Owl_DB; 

   $sql->query("SELECT email,language,attachfile from $default->owl_users_table where id = '$iUserId'");
   $sql->next_record();

   $DefUserLang = $sql->f("language");
   if(empty($DefUserLang))
   {
      $DefUserLang = $default->owl_lang;
   }

   $email = $sql->f("email");

   require("$default->owl_fs_root/locale/$DefUserLang/language.inc");

   switch ($doc_action)
   {
      case "approved":
         $aBody = fGetMailBodyText(APPROVED, $iFileId);
         $aBody['HTML'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['TXT'] );
         break;
      case "final_approved_auto":
      case "final_approved":
         if ($doc_action == "final_approved" or $doc_action == "approved" )
         {
            $aBody = fGetMailBodyText(FINAL_APPROVED, $iFileId);
            $aBody['HTML'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['HTML'] );
            $aBody['TXT'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['TXT'] );
         }
         elseif ($doc_action == "final_approved_auto")
         {
            $aBody = fGetMailBodyText(FINAL_AUTO_APPROVED, $iFileId);
            $aBody['HTML'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['HTML'] );
            $aBody['TXT'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['TXT'] );
         }
         break;
      case "rejected":
         $aBody = fGetMailBodyText(REJECT_APPROVED, $iFileId);
         $aBody['HTML'] = ereg_replace("\%USER_MESSAGE\%", $usermessage . $reason, $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%USER_MESSAGE\%", $usermessage . $reason, $aBody['TXT'] );
         break;
      case "reminder":
         $aBody = fGetMailBodyText(REMINDER_APPROVED, $iFileId);
         $aBody['HTML'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['TXT'] );
         break;
      default:
         $aBody = fGetMailBodyText(NEW_APPROVED, $iFileId);
         $aBody['HTML'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['HTML'] );
         $aBody['TXT'] = ereg_replace("\%USER_MESSAGE\%", $usermessage, $aBody['TXT'] );
         break;
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
   $mail->Host = "$default->owl_email_server"; // specify main and backup server
   $mail->CharSet = "$owl_lang->charset"; // set the email charset to the language file charset 
   $mail->From = "$default->owl_email_from";
   $mail->FromName = "$default->owl_email_fromname";
   $mail->AddAddress($email);
   $mail->AddReplyTo("$default->owl_email_replyto", "OWL Intranet");
   $mail->WordWrap = 50; // set word wrap to 50 characters
   $mail->IsHTML(true); // set email format to HTML

   //print("<H1> ACTION: $doc_action </H1>");
   //fDebugDisplayArray($aBody);
   
   $mail->Subject = $aBody['SUBJECT'];
   $mail->Body =  $aBody['HTML'];
   $mail->altBody =  $aBody['TXT'];

   if (!$mail->Send() && $default->debug == true)
   {
      printError("DEBUG: " . $owl_lang->err_email, $mail->ErrorInfo);
   }
}

function notify_users($groupid, $flag, $fileid, $type = "")
{
   global $default, $userid;

   $sql = new Owl_DB; 

   $sql->query("SELECT * from $default->owl_files_table where id = '$fileid'");
   $sql->next_record();

   if (empty($type) and $sql->f('url') == 1)
   {
      $type = "url";
   }

   $path = find_path($sql->f('parent'));
   $filename = $sql->f('filename');

   $sql->query("SELECT distinct id, email,language,attachfile from $default->owl_users_table u left join $default->owl_users_grpmem_table m on u.id=m.userid where notify = '1' and disabled = '0' and (u.groupid='$groupid' or m.groupid='$groupid') and u.id <> '$userid' and m.groupid is not NULL");
     
   while ($sql->next_record())
   {
      if (check_auth($fileid, "file_download", $sql->f("id")) == 1)
      {
         $newpath = $path; 
         $newfilename = $filename;
         $DefUserLang = $sql->f("language");
         if(empty($DefUserLang))
         {
            $DefUserLang = $default->owl_lang;
         }

         require("$default->owl_fs_root/locale/$DefUserLang/language.inc");

         $r = preg_split("(\;|\,)", $sql->f("email"));
         reset ($r);
         while (list ($occ, $email) = each ($r))
         {
            $mail = new phpmailer(); 

            if ($default->generate_notify_link_session === true)
            {
               $tempsess = check_for_sess($sql->f("id"));
            }
            else
            {
               $tempsess = "0";
            }

            $aBody = fGetMailBodyText($flag, $fileid, $tempsess, $type);

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

            if ($type != "url")
            {
               if ($sql->f("attachfile") == 1)
               {
   		  $sFsPath = fCreateWaterMark($fileid);

                  if (! $sFsPath === false)
                  {
                     $sAttachPath = $sFsPath;
                  }
                  else
                  {
                     if (!$default->owl_use_fs)
                     {
                        $sAttachPath = fGetFileFromDatbase($fileid);
                     }
                     else
                     {
                        $sAttachPath = "$default->owl_FileDir/$newpath/$newfilename";
                     }
                  }

                  $mimeType = fGetMimeType($newfilename);
                  $mail->AddAttachment($sAttachPath, "" , "base64" , "$mimeType");
               } 
            }

            $mail->Body =  $aBody['HTML'];
            $mail->altBody =  $aBody['TXT'];

            if (!$mail->Send() && $default->debug === true)
            {
               printError("DEBUG: " . $owl_lang->err_email, $mail->ErrorInfo);
            } 

            if (!$default->owl_use_fs && $sql->f("attachfile") == 1)
            {
               if ($type == "")
               {
                  //unlink("$default->owl_FileDir/$newfilename");
                  unlink($sAttachPath);
               }
            } 
         } 
      } 
   } 
} 

function fInsertUnzipedFiles($path, $cParent, $FolderPolicy, $FilePolicy, $description, $groupid, $iCreatorID, $metadata, $title, $major_revision, $minor_revision, $doctype, $bRemoveFiles = true)
{
   global $default, $sess;
   $sql = new OWL_DB;
   $sql_custom = new OWL_DB;

         $dir = dir($path);
         $dir->rewind();

         while (false !== ($file = $dir->read()))
         {
            if ($file != "." and $file != ".." and $file != "CVS")
            {
               if(is_dir($path . "/" . $file)) 
               {

                  $original_name = $file;
                  $file = trim(ereg_replace("[^$default->list_of_valid_chars_in_file_names]", "",  ereg_replace("%20|^-", "_", $file)));
                  if($original_name != $file)
                  {
                     rename($path. "/" . $original_name,$path . "/" . $file);
                  }
                  $smodified = $sql->now();
                  $sql->query("INSERT into $default->owl_folders_table (name,parent,security,description,groupid,creatorid,smodified) values ('$file', '$cParent', '$FolderPolicy', '$description', '$groupid', '$iCreatorID', $smodified)");

                  $newParent = $sql->insert_id($default->owl_folders_table, 'id');
                  fSetDefaultFolderAcl($newParent);
                  fSetInheritedAcl($cParent, $newParent, "FOLDER");

                  fInsertUnzipedFiles($path . "/" .$file, $newParent, $FolderPolicy, $FilePolicy, $description, $groupid, $iCreatorID, $metadata, $title, $major_revision, $minor_revision, $doctype, $bRemoveFiles);
               }
               else
               {
                  $TheFileSize = filesize($path . "/" . $file);  //get filesize
                  $TheFileTime = date("Y-m-d H:i:s", filemtime($path . "/" . $file));
     
                  $original_name = $file;
                  $file = trim(ereg_replace("[^$default->list_of_valid_chars_in_file_names]", "",  ereg_replace("%20|^-", "_", $file)));
                  if($original_name != $file)
                  {
                     rename($path. "/" . $original_name,$path . "/" . $file);
                  }

                  if ($title == "") 
                  {
                     $ctitle = $file;
                  }
                  else
                  {
                     $ctitle = $title;
                  }
                  $ctitle = stripslashes($ctitle);
                  $ctitle = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", $ctitle));

                  $new_quota = fCalculateQuota($TheFileSize, $iCreatorID, "ADD");

                  $result = $sql->query("INSERT INTO $default->owl_files_table (name,filename,f_size,creatorid,parent,created,description,metadata,security,groupid,smodified,checked_out, major_revision, minor_revision, url, doctype, approved) values ('$ctitle', '$file', '$TheFileSize', '$iCreatorID', '$cParent', '$TheFileTime' , '$description', '$metadata', '$FilePolicy', '$groupid', '$TheFileTime', '0','$major_revision','$minor_revision', '0', '$doctype', '1')");
                  $searchid = $sql->insert_id($default->owl_files_table, 'id');

                  fSetDefaultFileAcl($searchid);
                  fSetInheritedAcl($cParent, $searchid, "FILE");

                  if ( fIsQuotaEnabled($iCreatorID) )
                  {
                     $sql->query("UPDATE $default->owl_users_table set quota_current = '$new_quota' where id = '$iCreatorID'");
                  }


                  //*****************************************************
                  // This line was commented out because on large
                  // Zip files with allot of indexable files 
                  // it would cause the Script to time out and/or run out of resources.
                  // Run admin/tools/bigindex.pl instead.
                  // 1171872 Error in "Add Archive"
                  //*****************************************************

                  if ($default->index_files_on_archive_add == 1)
                  {
                     fIndexAFile($file, $path . "/" . $file, $searchid);
                  }
                  fGenerateThumbNail($searchid);
               
                  $sql_custom->query("SELECT * from $default->owl_docfields_table  where doc_type_id = '$doctype'");
                  while ($sql_custom->next_record())
                  {
                     $result = $sql->query("INSERT INTO $default->owl_docfieldvalues_table (file_id, field_name, field_value) values ('$searchid', '" . $sql_custom->f("field_name") ."', '" . ${$sql_custom->f("field_name")} ."');");
                  }
                  if ( !$default->owl_use_fs )
                  {
                     if ($default->owl_compressed_database && file_exists($default->gzip_path))
                     {
                        if ($bRemoveFiles === true)
                        {
                           $aFileExtension = fFindFileFirstpartExtension($file);
                           if ($aFileExtension[1] == "gz")
                           {
                              rename($path . "/" . $file, $path . "/" . $aFileExtension[0]);
                              $sCurrentFile = $path . "/" . $aFileExtension[0];
                           }
                           else
                           {
                              $sCurrentFile = $path . "/" . $file;
                           }

                           system($default->gzip_path . " " . escapeshellarg($sCurrentFile));
                           $sCurrentFile = $sCurrentFile . ".gz";
                           $fsize = filesize($sCurrentFile);
                           $fd = fopen($sCurrentFile, 'rb');
                           $filedata = fread($fd, $fsize);
                           fclose($fd);
                           unlink($sCurrentFile);
                        }
                        else
                        {
                           $tmpDir = $default->owl_tmpdir . "/owltmp.$sess";

                           if (file_exists($tmpDir))
                           {
                              myDelete($tmpDir);
                           }

                           mkdir($tmpDir,$default->directory_mask);
                           $aFileExtension = fFindFileFirstpartExtension($file);
                           if ($aFileExtension[1] == "gz")
                           {
                              copy($path . "/" . $file, $tmpDir . "/" . $aFileExtension[0]);
                              $sCurrentFile = $tmpDir . "/" . $aFileExtension[0];
                              $zipedfile = $tmpDir . "/" . $file;
                           }
                           else
                           {
                              $sCurrentFile = $path . "/" . $file;
                              $zipedfile = $tmpDir . "/" . $file . ".gz";
                           }
                           system($default->gzip_path . " -c  " . escapeshellarg($sCurrentFile) . " > " . escapeshellarg($zipedfile));
                           $fsize = filesize($zipedfile);
                           $fd = fopen($zipedfile, 'rb');
                           $filedata = fread($fd, $fsize);
                           fclose($fd);
                           unlink($zipedfile);
                        }
                        $compressed = '1';
                     }
                     else
                     {
                        $sCurrentFile = $path . "/" . $file;
                        $fsize = filesize($sCurrentFile);
                        $fd = fopen($sCurrentFile, 'rb');
                        $filedata = fread($fd, $fsize);
                        fclose($fd);
                        $compressed = '0';
                        if ($bRemoveFiles === true)
                        {
                           unlink($sCurrentFile);
                        }
                     }

                     if ($searchid !== null && $filedata)
                     {
                        $sql->query("INSERT into $default->owl_files_data_table (id, data, compressed) values ('$searchid', '". addslashes($filedata) ."', '$compressed')");
                     }

                  } 
               }
            }
         }
         $dir->close();
         $tmpDir = $default->owl_tmpdir . "/owltmp_initial_load";

         if (file_exists($tmpDir))
         {
            myDelete($tmpDir);
         }

}

function fGenFolderThumbNails($Folderid)
{
   global $default, $owl_lang;
   $GetFolder = new Owl_DB;
   $GetFiles = new Owl_DB;

   $GetFiles->query("SELECT * from $default->owl_files_table where parent ='" . $Folderid . "'");
   while ( $GetFiles->next_record() )
   {         
      fGenerateThumbNail($GetFiles->f("id"));
   }

   $GetFolder->query("SELECT * from $default->owl_folders_table where parent ='$Folderid'");
   while ($GetFolder->next_record())
   {
      fGenFolderThumbNails($GetFolder->f("id"));
   }
}

function fGenerateThumbNail($fid)
{
   global $default, $owl_lang, $sess;
   // gotta grab the image size

   if ($default->thumbnails == 1)
   {
      $filename = fid_to_filename($fid);
      $sFileExtension = fFindFileExtension($filename);
      $aImageExtensionList = $default->thumbnail_image_type;
      $aVideoExtensionList = $default->thumbnail_video_type;
      $iStartParent = owlfileparent($fid);


      $tmpDir = $default->owl_tmpdir . "/owltmp.$sess";
      if ($default->owl_use_fs === true)
      {
         $path = $default->owl_FileDir . "/" . find_path($iStartParent) . "/" . $filename;

         if (file_exists($tmpDir))
         {
            myDelete($tmpDir);
         }

         mkdir($tmpDir,$default->directory_mask);
      }
      else
      {
         $path = fGetFileFromDatbase($fid);
      }
      if ((preg_grep("/$sFileExtension/", $aImageExtensionList)) and file_exists($default->thumbnails_tool_path))
      {
 
         // Some image types have to be converted then resized

         if ($sFileExtension == "png")
         {
            copy($path , $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png");
         }
         else if ($sFileExtension == "pdf" and file_exists($default->pdf_thumb_path))
         {
            //if (substr(php_uname(), 0, 7) != "Windows")
            //{
               exec($default->pdf_thumb_path . " -f 1 -l 1 \"".$path."\" \"". $tmpDir ."/". $default->owl_current_db . "_" . $fid . "\"");
               exec($default->thumbnails_tool_path . " \"" . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "-000001.ppm\" \"" . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png\"");
            //}
            //else
            //{
               //exec($default->pdf_thumb_path . " -f 1 -l 1 ".$path." ". $tmpDir ."/". $default->owl_current_db . "_" . $fid);
               //exec($default->thumbnails_tool_path . " " . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "-000001.ppm " . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png");
            //}
         }
         else if ($sFileExtension == "doc" and file_exists($default->wordtotext_path))
         {
            // USE antiword to generate a postscript file
            exec($default->wordtotext_path . " -p letter -m 8859-1 \"".$path."\" > \"". $tmpDir ."/". $default->owl_current_db . "_" . $fid . ".ps\"");

            $sFileContent = file_get_contents($tmpDir ."/". $default->owl_current_db . "_" . $fid . ".ps");
            $pos = strpos($sFileContent,"%%Page: 2");

            if ($pos!==false) 
            {

               $str = substr($sFileContent,0,$pos);
               $str .= "%%Trailer\n%%Pages: 1\n%%EOF\n";

               $fp = fopen($tmpDir ."/". $default->owl_current_db . "_" . $fid . ".ps","w");
               fwrite($fp,$str);
               fclose($fp);

             }

            //if (substr(php_uname(), 0, 7) != "Windows")
            //{
            // USE imagemagik convert tool to generate a  thumbnail
               exec($default->thumbnails_tool_path . " \"" . $tmpDir ."/". $default->owl_current_db . "_" . $fid . ".ps\" \"" . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png\"");
            //}
            //else
            //{
               //exec($default->thumbnails_tool_path . " " . $tmpDir ."/". $default->owl_current_db . "_" . $fid . ".ps " . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png");
            //}
         }
         else
         {
            //if (substr(php_uname(), 0, 7) != "Windows")
            //{
               exec($default->thumbnails_tool_path . " \"" . $path . "\" \"" . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png\"");
            //}
            //else
            //{
               //exec($default->thumbnails_tool_path . " " . $path . " " . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png");
            //}
         }

         $imagedata = @GetImageSize($tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png");
         $imagewidth = $imagedata[0];
      
         if ($imagewidth > 0)
         {
            if ($default->thumbnails_small_width > 0)
            {
               $iPercentShrinkSmall = 100 *  $default->thumbnails_small_width / $imagewidth;
               //if (substr(php_uname(), 0, 7) != "Windows")
               //{
                  exec($default->thumbnails_tool_path . " -resize " . $iPercentShrinkSmall ."% \"" . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png\"" . " \"" . $default->thumbnails_location ."/". $default->owl_current_db . "_" . $fid . "_small.png\"");
               //}
               //else
               //{
                  //exec($default->thumbnails_tool_path . " -resize " . $iPercentShrinkSmall ."% " . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png " . $default->thumbnails_location ."/". $default->owl_current_db . "_" . $fid . "_small.png");
               //}
            }

            if ($default->thumbnails_med_width > 0)
            {
               $iPercentShrinkMed =  100 *  $default->thumbnails_med_width / $imagewidth;
               //if (substr(php_uname(), 0, 7) != "Windows")
               //{
                  exec($default->thumbnails_tool_path . " -resize " . $iPercentShrinkMed ."% \"" . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png\"" . " \"" . $default->thumbnails_location ."/". $default->owl_current_db . "_" . $fid . "_med.png\"");
               //}
               //else
               //{
                  //exec($default->thumbnails_tool_path . " -resize " . $iPercentShrinkMed ."% " . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png " . $default->thumbnails_location ."/". $default->owl_current_db . "_" . $fid . "_med.png");
               //}
            }

            if ($default->thumbnails_large_width > 0)
            {
               $iPercentShrinkLarge = 100 *  $default->thumbnails_large_width / $imagewidth;
               //if (substr(php_uname(), 0, 7) != "Windows")
               //{
                  exec($default->thumbnails_tool_path . " -resize " . $iPercentShrinkLarge ."% \"" . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png\"" . " \"" . $default->thumbnails_location ."/". $default->owl_current_db . "_" . $fid . "_large.png\"");
               //}
               //else
               //{
                  //exec($default->thumbnails_tool_path . " -resize " . $iPercentShrinkLarge ."% " . $tmpDir ."/". $default->owl_current_db . "_" . $fid . "_tmp.png " . $default->thumbnails_location ."/". $default->owl_current_db . "_" . $fid . "_large.png");
               //}
            }
         }
      }
      if ((preg_grep("/$sFileExtension/", $aVideoExtensionList)) and file_exists($default->thumbnails_video_tool_path))
      {

         exec("cd '$tmpDir' ; $default->thumbnails_video_tool_path $default->thumbnails_video_tool_opt '$path' > /dev/null 2>&1");
         if (file_exists($tmpDir . "/00000002.png"))
         {
            $imagedata = @GetImageSize($tmpDir . "/00000002.png");
            $imagewidth = $imagedata[0];
            if ($imagewidth > 0)
            {
               $iPercentShrinkSmall = 100 *  $default->thumbnails_small_width / $imagewidth;
               $iPercentShrinkMed =  100 *  $default->thumbnails_med_width / $imagewidth;
               $iPercentShrinkLarge = 100 *  $default->thumbnails_large_width / $imagewidth;
               exec($default->thumbnails_tool_path . " -resize " . $iPercentShrinkSmall ."% '" . $tmpDir . "/00000002.png' '" . $default->thumbnails_location ."/". $default->owl_current_db . "_" . $fid . "_small.png'");
               exec($default->thumbnails_tool_path . " -resize " . $iPercentShrinkMed ."% '" . $tmpDir . "/00000002.png' '" . $default->thumbnails_location ."/". $default->owl_current_db . "_" . $fid . "_med.png'");
               exec($default->thumbnails_tool_path . " -resize " . $iPercentShrinkLarge ."% '" . $tmpDir . "/00000002.png' '" . $default->thumbnails_location ."/". $default->owl_current_db . "_" . $fid . "_large.png'");
            }
         }
      }
      //print("cd $tmpDir ; $default->thumbnails_video_tool_path $default->thumbnails_video_tool_opt $path > /dev/null 2>&1");
      myDelete($tmpDir);
   }
}

function fVirusCheck($filename, $name)
{
   global $default, $userid, $parent, $owl_lang;
   
   //print("DEBUG: Calling Virus Function <br />");

   if (trim($default->virus_path) <> "")
   {
      $command = $default->virus_path . " \"" . $filename . "\"";
      system($command , $retval);

      if ($retval > 0)
      {
         owl_syslog(FILE_VIRUS, $userid, $name, $parent, $owl_lang->log_detail, "FILE");
         if ($default->debug == true)
         {
            printError("DEBUG: $owl_lang->virus_infected -- $filename" , "DEBUG: $owl_lang->virus_return_val " . $retval . "<br />COMMAND: " . $command);
         }
         else
         {
            printError($owl_lang->virus_infected);
         }
      }
   }
}

function verify_login($username, $password)
{
   global $default;
   $sql = new Owl_DB;

   $sql->query("SELECT user_auth from $default->owl_users_table where username = '" . addslashes($username) ."'");
   $sql->next_record();
   $sUserAuth = trim($sql->f("user_auth"));
   
   if (!empty($sUserAuth))
   {
      $default->auth = $sUserAuth;
   }
   
   
   if ($username == "admin" and $default->auth > 1)
   {
      $default->auth = 0;
   }

   if ( $default->auth == 1)
   {
      //$username = addslashes($username);
      $username = $_SERVER['PHP_AUTH_USER'];
      $password = addslashes($password);
      $sql->query("SELECT * from $default->owl_users_table where username = '$username'");
   }
   else if ( $default->auth == 2)
   {
      $mbox = @imap_open ("{" . $default->auth_host . "/pop3/notls:" . $default->auth_port . "}INBOX", $username, $password);
      if($mbox)
      {
            $username = addslashes($username);
            $sql->query("SELECT * from $default->owl_users_table where username = '$username'");
            imap_close($mbox);
      }
      else
      {
            $sql->query("SELECT * from $default->owl_users_table where 1=0");
      }
   }
   else if ( $default->auth == 3)
   {
      // LDAP - authenticate the user and if successful get his details from owl db
      // then if he's not in the owl db, login wil fail...
      $error = ldap_authenticate($username, $password);
      if ($error == "0")
      {
         $sql->query("SELECT * from $default->owl_users_table where username = '$username'");
      }
      else
      {
            $sql->query("SELECT * from $default->owl_users_table where 1=0");
      }
   }
   else
   {
      $username = addslashes($username);
      $password = stripslashes($password);
      $sql->query("SELECT * from $default->owl_users_table where username = '$username' and password = '" . md5($password) . "'");
   }

   $numrows = $sql->num_rows($sql); 

      //print("SELECT * from $default->owl_users_table where username = '$username' and password = '" . md5($password) . "'");
      //exit("<br> D: $numrows  username = '$username' and password = '" . $password . "'");
   // Bozz Begin added Password Encryption above, but for now
   // I will allow admin to use non crypted password until he
   // upgrades all users
   if ($numrows == "1")
   {
      $sql->next_record();
      $iFirstDir = $sql->f("firstdir"); 
      $iHomeDir = $sql->f("homedir"); 
      $iMaxSession = $sql->f("maxsessions"); 

      if ($sql->f("disabled") == 1)
      {
         $verified["bit"] = 2;
      }
      else
      {
         $verified["bit"] = 1;
      }
      $verified["user"] = $sql->f("username");
      $verified["uid"] = $sql->f("id");
      $verified["group"] = $sql->f("groupid");
      if (  $iHomeDir <>  $iFirstDir)
      {
         $sql->query("SELECT * from $default->owl_folders_table where id = '$iFirstDir'");
         $numrows = $sql->num_rows($sql);
         if ($numrows == "1")
         {
            $verified["homedir"] = $iFirstDir;
         } 
         else
         {
            $verified["homedir"] = $iHomeDir;
         }
      }
      else
      {
         $verified["homedir"] = $iHomeDir;
      }
      $maxsessions = $iMaxSession + 1;
   } 
   else
   { 
      // LOGIN has FAILED, lets see if a valid username has been used
      // 
      $sql->query("SELECT * from $default->owl_users_table where username = '$username'");
      $numrows = $sql->num_rows($sql);
      if ($numrows == "1")
      {
         while ($sql->next_record())
         {
            $verified["uid"] = $sql->f("id");
            $verified["user"] = $sql->f("username");
         } 
      } 
      else
      {
         if ($default->auth == 1)
         {
            die("ACCESS DENIED");
            exit;
         }
      }

   } 
   // remove stale sessions from the database for the user
   // that is signing on.
   
   $time = time() - $default->owl_timeout;
   if ($verified["group"] == 0)
   {
      $sql = new Owl_DB;
      $sql->query("DELETE from $default->owl_sessions_table where lastused <= $time ");
   } 
   else
   {
      $sql = new Owl_DB;
      $sql->query("DELETE from $default->owl_sessions_table where usid = '" . $verified["uid"] . "' and lastused <= $time ");
   } 
   // Check if Maxsessions has been reached
   
   $sql = new Owl_DB;
   $sql->query("SELECT * from $default->owl_sessions_table where ip <> '0' and usid = '" . $verified["uid"] . "'");

   if ($sql->num_rows($sql) >= $maxsessions && $verified["bit"] != 0)
   {
      if ($verified["group"] == 0)
      {
         $verified["bit"] = 1;
      }
      else
      {
         $verified["bit"] = 3;
      }
   } 
   return $verified;
} 

function verify_session($sess)
{
   global $default;
   global $owl_lang;
   global $parent, $fileid, $sess;

   // Sanitize the sessions for tampering
   $sess = ereg_replace(" ", "", $sess);

   if (! ereg("^[a-fA-F0-9]", $sess))
   {
      $sess="1";
   }

   if (strlen($sess) > 32)
   {
      $sess="1";
   }

   $sess = ltrim($sess);
   $verified["bit"] = 0;
   $sql = new Owl_DB;
   $sql->query("SELECT * from $default->owl_sessions_table where sessid = '$sess'");
   $numrows = $sql->num_rows($sql);
   $time = time();
   if ($numrows == "1")
   {
      while ($sql->next_record())
      {
         if (getenv("HTTP_CLIENT_IP"))
         {
            $ip = getenv("HTTP_CLIENT_IP");
         } elseif (getenv("HTTP_X_FORWARDED_FOR"))
         {
            $forwardedip = getenv("HTTP_X_FORWARDED_FOR");
            list($ip, $ip2, $ip3, $ip4) = split (",", $forwardedip);
         } 
         else
         {
            $ip = getenv("REMOTE_ADDR");
         } 
         if ($ip == $sql->f("ip") || 0 == $sql->f("ip"))
         {
            if (($time - $sql->f("lastused")) <= $default->owl_timeout)
            {
               $verified["bit"] = 1;
               $verified["userid"] = $sql->f("usid");
               $verified["currentdb"] = $sql->f("currentdb");
               $sql->query("SELECT * from $default->owl_users_table where id = '" . $verified["userid"] . "'");
               while ($sql->next_record()) $verified["groupid"] = $sql->f("groupid");
            } 
            else
            { 
               if ($default->remember_me)
               {
                  setcookie ("owl_sessid", "");
               }
               include_once($default->owl_fs_root . "/lib/header.inc");
               include_once($default->owl_fs_root . "/lib/userheader.inc");

               print("<center>");
               print("<table class='border1' cellspacing='0' cellpadding='0' border='0' width='$default->table_collapse_width'><tr><td align='left' valign='top' width='100%'>\n");
               fPrintButtonSpace(12, 1);
               print("<br />\n");
               print("<table class='border2' cellspacing='0' cellpadding='0' border='0' width='100%'><tr><td align='left' valign='top' width='100%'>\n");
                                       
               if ($default->show_prefs == 1 or $default->show_prefs == 3)
               {                       
                  fPrintPrefs();       
               }                       
         
               fPrintButtonSpace(12, 1);
               print("<br />\n");
               print("<table class='margin2' cellspacing='0' cellpadding='0' border='0' width='100%'>\n");
               print("<tr>\n");        
               print("<td align='left' valign='top'>\n");
               print("<table cellspacing='0' cellpadding='0' border='0' width='100%'>\n");
               fPrintFormTextLine("&nbsp;", "", "",  $owl_lang->sesstimeout , "", true);
               print("<tr>");
               print("<td class='form1'>");
               fPrintButtonSpace(1, 1);
               print("</td>");
               print("<td>");          
               print("<table class='border1' cellspacing='0' cellpadding='0' border='0' width='100%'>");
               print("<tr><td valign='top' width='100%'>");
               fPrintButtonSpace(1, 1);
               print("</td>");             
               if ($parent == "" || $fileid == "")
               {                    
                  fPrintButton("$default->owl_root_url/index.php", "btn_login");
               }                 
               else              
               {                 
                  fPrintButton("$default->owl_root_url/index.php?parent=$parent&fileid=$fileid", "btn_login");
               }              
               //print("\t\t<td class='button1' background='$default->owl_graphics_url/$default->sButtonStyle/ui_misc/button1_fill.jpg'>");
               print("\t\t<td class='button1'>");
               print("<input class='fbuttonup1' type='submit' value='$owl_lang->btn_back' alt='$owl_lang->alt_back' title='$owl_lang->alt_back' onclick='history.back();' onmouseover=\"highlightButton('fbuttondown1', this)\" onmouseout=\"highlightButton('fbuttonup1', this)\"></input>");
               print("</td>");
               print("</tr></table>\n");
               
               print("</td></tr>");
               print("</table>\n");
               fPrintButtonSpace(12, 1);
               print("<br />\n");
               print("</td></tr></table>\n");

               fPrintButtonSpace(12, 1);

               if ($default->show_prefs == 2 or $default->show_prefs == 3)
               {
                  fPrintPrefs();
               }
               print("</td></tr></table>\n");
               include_once($default->owl_fs_root . "/lib/footer.inc");
               exit();
            } 
         } 
         else
         {
            include_once($default->owl_fs_root . "/lib/header.inc");
            include_once($default->owl_fs_root . "/lib/userheader.inc");

            print("<center>\n");
            print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"90%\"><tr><td align=\"center\" valign=\"top\" width=\"100%\">\n");
            print("<table class=\"border2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");
            print("<table>\n<tr>\n<td class=\"form1\">" . $owl_lang->sessinuse . "</td>\n");
            if ($parent == "" || $fileid == "")
            {
               fPrintButton("$default->owl_root_url/index.php", "btn_login");
            }
            else 
            {
               fPrintButton("$default->owl_root_url/index.php?parent=$parent&fileid=$fileid", "btn_login");
            }
            print("\n</tr>\n</table>\n");
            print("</td>\n</tr>\n</table>\n");
            include_once($default->owl_fs_root . "/lib/footer.inc");
            exit;
         } 
      } 
   } 
   return $verified;
} 

function delTree($fid)
{
   global $fCount, $folderList, $default; 
   // delete from database
   $sql = new Owl_DB;
   $del = new Owl_DB;
   $sql->query("DELETE FROM $default->owl_folders_table WHERE id = '$fid'");
   $sql->query("DELETE FROM $default->owl_monitored_folder_table WHERE fid = '$fid'");

   $sql->query("SELECT id, f_size, creatorid FROM $default->owl_files_table WHERE parent = '$fid'"); 
   // Clean up Comments and Monitored Files from each file we are going to
   // delete
   while ($sql->next_record())
   {
      $iFileid = $sql->f("id");

      $new_current_quota = fCalculateQuota($sql->f("f_size"), $sql->f("creatorid"), "DEL");

      if (fIsQuotaEnabled($sql->f("creatorid")))
      {
        $del->query("UPDATE $default->owl_users_table set quota_current = '$new_current_quota' WHERE id = '" . $sql->f("creatorid") . "'");
      }

      $del->query("DELETE FROM $default->owl_monitored_file_table WHERE fid = '$iFileid'");
      $del->query("DELETE FROM $default->owl_comment_table WHERE fid = '$iFileid'");
      if (!$default->owl_use_fs)
      {
         $del->query("DELETE FROM $default->owl_files_data_table  WHERE id = '$iFileid'");
      }
            // Clean up all comments with this file
      $del->query("DELETE FROM $default->owl_docfieldvalues_table WHERE file_id = '$iFileid'");
      // Clean up all linked files
      $del->query("DELETE FROM $default->owl_files_table WHERE linkedto = '$iFileid'");
      // Clean up all linked files
      $del->query("DELETE FROM $default->owl_peerreview_table WHERE file_id = '$iFileid'");
      // Clean Up SEARCH Indexes for this file
      fDeleteFileIndexID($iFileid);
   } 

  // Clean up Folder ACL's
   $sql->query("DELETE FROM $default->owl_advanced_acl_table WHERE folder_id = '$fid'");
                                                                                                                                                                                              
   // Clean up File ACL's
   $sql->query("SELECT id FROM $default->owl_files_table WHERE parent = '$fid'");
   while ($sql->next_record())
   {
      $iFileid = $sql->f("id");
      $del->query("DELETE FROM $default->owl_advanced_acl_table WHERE file_id = '$iFileid'");
   }



   $sql->query("DELETE FROM $default->owl_files_table WHERE parent = '$fid'");

   for ($c = 0; $c < $fCount; $c++)
   {
      if ($folderList[$c][2] == $fid)
      {
         delTree($folderList[$c][0]);
      } 
   } 
} 
function find_path($parent, $bDisplayOnly = false)
{
   global $default;
   $path = fid_to_name($parent);
   $sql = new Owl_DB;

   if ($bDisplayOnly === true)
   {
      $iStopFolder = $default->HomeDir;
   }
   else
   {
      $iStopFolder = 1;
   }

   while ($parent != $iStopFolder)
   {
      $sql->query("SELECT parent from $default->owl_folders_table where id = '$parent' and 1=1");
      while ($sql->next_record())
      {
         $path = fid_to_name($sql->f("parent")) . "/" . $path;
         $parent = $sql->f("parent");
      } 
   } 
   return $path;
} 

function fid_to_filename($id)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $sql->query("SELECT filename from $default->owl_files_table where id = '$id'");
   while ($sql->next_record()) return $sql->f("filename");
} 

function fid_to_name($parent)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   if (empty($parent))
   {
      $parent=0;
   }
   $sql->query("SELECT name from $default->owl_folders_table where id = '$parent'");
   while ($sql->next_record())
   {
      return $sql->f("name");
   } 
} 

function flid_to_name($id)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $sql->query("SELECT name from $default->owl_files_table where id = '$id'");
   while ($sql->next_record()) return $sql->f("name");
} 

function flid_to_filename($id)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $sql->query("SELECT filename from $default->owl_files_table where id = '$id'");
   while ($sql->next_record()) return $sql->f("filename");
} 

function owlusergroup($userid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $sql->query("SELECT groupid from $default->owl_users_table where id = '$userid'");
   while ($sql->next_record()) $groupid = $sql->f("groupid");
   return $groupid;
} 

function owlfilecreator($fileid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $filecreator = 0;
   $sql->query("SELECT creatorid from " . $default->owl_files_table . " where id = '$fileid'");
   while ($sql->next_record()) $filecreator = $sql->f("creatorid");
   return $filecreator;
} 

function fIsFolderCreator($folderid)
{
   global $default, $userid, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $filecreator = 0;
   $sql->query("SELECT creatorid from " . $default->owl_folders_table . " where id = '$folderid' and creatorid = '$userid'");
   if ($sql->num_rows() == 1)
   {
      return true;
   }
   else
   {
      return false;
   }
} 
function fIsFileCreator($fileid)
{
   global $default, $userid, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $filecreator = 0;
   $sql->query("SELECT creatorid from " . $default->owl_files_table . " where id = '$fileid' and creatorid = '$userid'");
   if ($sql->num_rows() == 1)
   {
      return true;
   }
   else
   {
      return false;
   }
} 

function fSetInheritedAcl($parent, $id, $type)
{
   global $default;
   $sql = new Owl_DB;
   $qAclInsert = new Owl_DB;

   if ($default->inherit_acl_from_parent_folder == true)
   {
        
        $sql->query("SELECT * FROM $default->owl_advanced_acl_table where folder_id = '$parent'");
        while ($sql->next_record())
        {
           $sCheckQuery = "SELECT * FROM $default->owl_advanced_acl_table WHERE (";
           $sQuery = "INSERT INTO $default->owl_advanced_acl_table (group_id, user_id, file_id, folder_id, owlread, owlwrite, owlviewlog, owldelete, owlcopy, owlmove, owlproperties, owlupdate, owlcomment, owlcheckin, owlemail, owlrelsearch, owlsetacl, owlmonitor) VALUES (";
           $sValue = $sql->f('group_id');
           if( !isset( $sValue))
           {
              $sQuery .= "NULL, ";
              $sCheckQuery .= "group_id is NULL AND ";
           }
           else
           {
              $sQuery .= "'" . $sql->f('group_id') . "', ";
              $sCheckQuery .= "group_id = '" . $sql->f('group_id') . "' AND ";
           }
           $sValue = $sql->f('user_id');
           if(!isset($sValue))
           {
              $sQuery .= "NULL, ";
              $sCheckQuery .= "user_id is NULL AND ";
           }
           else
           {
              $sQuery .= "'" . $sql->f('user_id') . "', ";
              $sCheckQuery .= "user_id = '" . $sql->f('user_id') . "' AND ";
           }
           if ($type == "FOLDER")
           {
              $sQuery .=  "NULL, '" .  $id  . "', '";
              $sCheckQuery .= "file_id is NULL AND folder_id ='" .  $id  . "')";
              $iOwlUpdate = $sql->f('owlupdate');
           }
           else
           {
              $sQuery .=  "'" . $id  . "', NULL, '";
              $sCheckQuery .= "file_id ='" .  $id  . "' AND folder_id is NULL)";
              $iOwlUpdate = $sql->f('owlwrite');
           }
           $sQuery .=  $sql->f('owlread') . "', '" . $sql->f('owlwrite') . "', '" . $sql->f('owlviewlog') . "', '" . $sql->f('owldelete') . "', '" . $sql->f('owlcopy') . "', '" . $sql->f('owlmove') . "', '" . $sql->f('owlproperties') . "', '" . $iOwlUpdate . "', '" . $sql->f('owlcomment') . "', '" . $sql->f('owlcheckin') . "', '" . $sql->f('owlemail') . "', '" . $sql->f('owlrelsearch') . "', '" . $sql->f('owlsetacl', '') . "', '" . $sql->f('owlmonitor') . "')";
           $qAclInsert->query($sCheckQuery);
           if ($qAclInsert->num_rows() == 0)
           {
              $qAclInsert->query($sQuery);
           }
        }
   }
}

function fGetFAclChecked($file_id, $gid_uid, $acl, $type = "group")
{
   global $default;
   if (empty($acl))
   {
      return "";
   }
   if ($type == "group")
   {
      if (is_array($gid_uid))
      {
         $sAclType = "(";
         $iCount = 0;
         foreach($gid_uid as $g)
         {
            $sAclType .= "group_id = '$g[0]' OR ";
         }
         $sAclType .= "1=0)";
      }
      else
      {
         $sAclType = "(group_id = '$gid_uid')";
      }
   }
   else
   {
      $sAclType = "(user_id = '$gid_uid')";
   }   
   $qGetAcl = "SELECT * FROM $default->owl_advanced_acl_table where folder_id = '$file_id' and $acl = '1' and $sAclType";
   
   $sql = new Owl_DB;
   $sql->query($qGetAcl);
   if ($sql->num_rows() > 0)
   {         
      return "checked=\"checked\"";
   }
   else
   {
      return "";
   }
}

function fGetAclChecked($file_id, $gid_uid, $acl, $type = "group")
{
   global $default, $dbOwlQueries;

   static $dbOwlQueries;
   if(empty($dbOwlQueries))
   {
      $dbOwlQueries=new Owl_DB;
      $dbOwlQueries->connect();
   }

   if (empty($acl))
   {
      return "";
   }
   if ($type == "group")
   {
      if (is_array($gid_uid))
      {
         $sAclType = "(";
         $iCount = 0;
         foreach($gid_uid as $g)
         {
            $sAclType .= "group_id = '$g[0]' OR ";
         }
         $sAclType .= "1=0)";
      }
      else
      {
         $sAclType = "(group_id = '$gid_uid')";
      }
   }
   else
   {
      $sAclType = "(user_id = '$gid_uid')";
   }   
   $qGetAcl = "SELECT * FROM $default->owl_advanced_acl_table where file_id = '$file_id' and $acl = '1' and $sAclType";
   
   $dbOwlQueries->query($qGetAcl);
   if ($dbOwlQueries->num_rows() > 0)
   {         
      return "checked=\"checked\"";
   }
   else
   {
      return "";
   }
}

function fGetAllAclChecked($file_id, $groups )
{
   global $default;
   global $userid;

   $aFileAccess = array();

   if(owlfilecreator($file_id) == $userid or fIsAdmin())
   {
       $aFileAccess['owlread'] = 1;
       $aFileAccess['owlwrite'] = 1;
       $aFileAccess['owlviewlog'] = 1;
       $aFileAccess['owldelete'] = 1;
       $aFileAccess['owlcopy'] = 1;
       $aFileAccess['owlmove'] = 1;
       $aFileAccess['owlproperties'] = 1;
       $aFileAccess['owlupdate'] = 1;
       $aFileAccess['owlcomment'] = 1;
       $aFileAccess['owlcheckin'] = 1;
       $aFileAccess['owlemail'] = 1;
       $aFileAccess['owlrelsearch'] = 1;
       $aFileAccess['owlsetacl'] = 1;
       $aFileAccess['owlmonitor'] = 1;
       return $aFileAccess;
   }

   static $dbOwlQueries;
   if(empty($dbOwlQueries))
   {
      $dbOwlQueries=new Owl_DB;
      $dbOwlQueries->connect();
   }

   if (is_array($groups))
   {
      $sAclType = "(";
      $iCount = 0;
      foreach($groups as $g)
      {
         $sAclType .= "group_id = '$g[0]' OR ";
      }
      $sAclType .= "1=0)";
   }
   else
   {
      $sAclType = "(group_id = '$groups')";
   }

   $sAclUser = "(user_id = '$userid' or user_id = '0')";

   $qGetAcl = "SELECT * FROM $default->owl_advanced_acl_table where file_id = '$file_id' and ( $sAclUser or $sAclType )";

   //$dbOwlQueries = new Owl_DB;
   $dbOwlQueries->query($qGetAcl);
   while ( $dbOwlQueries->next_record())
   {
       if ($aFileAccess['owlread'] == 0)
       {
          $aFileAccess['owlread'] += $dbOwlQueries->f('owlread');
       }
       if ($aFileAccess['owlwrite'] == 0)
       {
          $aFileAccess['owlwrite'] += $dbOwlQueries->f('owlwrite');
       }
       if ($aFileAccess['owlviewlog'] == 0)
       {
          $aFileAccess['owlviewlog'] += $dbOwlQueries->f('owlviewlog');
       }
       if ($aFileAccess['owldelete'] == 0)
       {
          $aFileAccess['owldelete'] += $dbOwlQueries->f('owldelete');
       }
       if ($aFileAccess['owlcopy'] == 0)
       {
          $aFileAccess['owlcopy'] += $dbOwlQueries->f('owlcopy');
       }
       if ($aFileAccess['owlmove'] == 0)
       {
          $aFileAccess['owlmove'] += $dbOwlQueries->f('owlmove');
       }
       if ($aFileAccess['owlproperties'] == 0)
       {
          $aFileAccess['owlproperties'] += $dbOwlQueries->f('owlproperties');
       }
       if ($aFileAccess['owlupdate'] == 0)
       {
          $aFileAccess['owlupdate'] += $dbOwlQueries->f('owlupdate');
       }
       if ($aFileAccess['owlcomment'] == 0)
       {
          $aFileAccess['owlcomment'] += $dbOwlQueries->f('owlcomment');
       }
       if ($aFileAccess['owlcheckin'] == 0)
       {
          $aFileAccess['owlcheckin'] += $dbOwlQueries->f('owlcheckin');
       }
       if ($aFileAccess['owlemail'] == 0)
       {
          $aFileAccess['owlemail'] += $dbOwlQueries->f('owlemail');
       }
       if ($aFileAccess['owlrelsearch'] == 0)
       {
          $aFileAccess['owlrelsearch'] += $dbOwlQueries->f('owlrelsearch');
       }
       if ($aFileAccess['owlsetacl'] == 0)
       {
          $aFileAccess['owlsetacl'] += $dbOwlQueries->f('owlsetacl');
       }
       if ($aFileAccess['owlmonitor'] == 0)
       {
          $aFileAccess['owlmonitor'] += $dbOwlQueries->f('owlmonitor');
       }
   }
   return $aFileAccess;
}

function uid_to_lastlogon($creatorid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $sql2 = new Owl_DB;
   $sql2->query("SELECT lastlogin from $default->owl_users_table where id = '" . $creatorid . "'");
   $sql2->next_record();
   if ( $sql2->num_rows() == 0 )
   {
      $logindate = 0;
   }
   else
   {
      $logindate = $sql2->f("lastlogin");
   }
   return $logindate;
}


function fid_to_creator_lastlogon($id)
{
   global $default, $cCommonDBConnection;

   $sql2 = $cCommonDBConnection;

   if (empty($sql2))
   {
      $sql2 = new Owl_DB;
   }

   $sql2 = new Owl_DB;
   $creatorid = owlfilecreator($id);
   $sql2->query("SELECT lastlogin from $default->owl_users_table where id = '" . $creatorid . "'");
   $sql2->next_record();
   if ( $sql2->num_rows() == 0 )
   {
      $logindate = 0;
   }
   else
   {
      $logindate = $sql2->f("lastlogin");
   }
   return $logindate;
}

function fid_to_creator($id)
{
   global $default, $owl_lang, $cCommonDBConnection;

   $sql2 = $cCommonDBConnection;

   if (empty($sql2))
   {
      $sql2 = new Owl_DB;
   }

   $creatorid = owlfilecreator($id);
   $sql2->query("SELECT name from $default->owl_users_table where id = '" . $creatorid . "'");
   $sql2->next_record();
   if ( $sql2->num_rows() == 0 )
   {
      $name = "<font class=\"url\">" . $owl_lang->orphaned . "</font>";
   }
   else
   {
      $name = $sql2->f("name");
   }
   return $name;
} 

function owlfoldercreator($folderid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $foldercreator = 0;
   $sql->query("SELECT creatorid from " . $default->owl_folders_table . " where id = '$folderid'");
   while ($sql->next_record()) $foldercreator = $sql->f("creatorid");
   return $foldercreator;
} 

function flid_to_creator($folderid)
{
   global $default, $owl_lang, $cCommonDBConnection;

   $sql2 = $cCommonDBConnection;

   if (empty($sql2))
   {
      $sql2 = new Owl_DB;
   }

   //$sql = new Owl_DB;
   //$sql->query("SELECT creatorid from " . $default->owl_files_table . " where id = '$id'");
   //while ($sql->next_record())
   //{
      //$creatorid = $sql->f("creatorid");
      $creatorid = owlfoldercreator($folderid);
      $sql2->query("SELECT name from $default->owl_users_table where id = '" . $creatorid . "'");
      $sql2->next_record();
      if ( $sql2->num_rows() == 0 )
      {
         $name = "<font class=url>" . $owl_lang->orphaned . "</font>";
      }
      else
      {
         $name = $sql2->f("name");
      }
   //} 
   return $name;
} 

function owlfiletype ($fileid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $filecreator = 0;
   $sql->query("SELECT url from " . $default->owl_files_table . " where id = '$fileid'");
   while ($sql->next_record()) $filetype = $sql->f("url");
   return $filetype;
} 
function owlfilegroup($fileid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $filegroup = 0;
   $sql->query("SELECT groupid from $default->owl_files_table where id = '$fileid'");
   while ($sql->next_record()) $filegroup = $sql->f("groupid");
   return $filegroup;
} 

function owlfoldergroup($folderid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $foldergroup = 0;
   $sql->query("SELECT groupid from $default->owl_folders_table where id = '$folderid'");
   while ($sql->next_record()) $foldergroup = $sql->f("groupid");
   return $foldergroup;
} 


function fCurFolderSecurity($folderid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }


   $sql->query("SELECT security from $default->owl_folders_table where id = '$folderid'");
   while ($sql->next_record()) 
   {
      $iFoldSecurity = $sql->f("security");
   }
   return $iFoldSecurity;
} 

function owlfolderparent($folderid)
{
   global $default, $cCommonDBConnection;



   if ( $default->HomeDir == $folderid )
   {
      $folderparent = 1;
   }
   else
   {
      $sql = $cCommonDBConnection;

      if (empty($sql))
      {
         $sql = new Owl_DB;
      }
      $sql->query("SELECT parent from $default->owl_folders_table where id = '$folderid'");
      while ($sql->next_record()) 
      {
         $folderparent = $sql->f("parent");
      }
   }
   return $folderparent;
} 



function owlfileparent($fileid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $sql = new Owl_DB;
   $sql->query("SELECT parent from $default->owl_files_table where id = '$fileid'");
   while ($sql->next_record()) $fileparent = $sql->f("parent");
   return $fileparent;
} 


function group_to_name($id)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $sql->query("SELECT name from $default->owl_groups_table where id = '$id'");
   while ($sql->next_record()) return $sql->f("name");
} 

function uid_to_name($id)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $name = "";

   if (empty($id))
   {
      $name = "Owl";
   }
   else
   {
      $sql->query("SELECT name from $default->owl_users_table where id = '$id'");
      while ($sql->next_record()) $name = $sql->f("name");
      if ($name == "") $name = "Owl";
   }
   return $name;
} 

function uid_to_uname($id)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $name = "";
   $sql->query("SELECT username from $default->owl_users_table where id = '$id'");
   while ($sql->next_record()) $username = $sql->f("username");
   if ($username == "") $username = "Owl";
   return $username;
}

function prefaccess($id)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $prefaccess = 1;
   $sql->query("SELECT noprefaccess from $default->owl_users_table where id = '$id'");
   while ($sql->next_record()) $prefaccess = !($sql->f("noprefaccess"));
   return $prefaccess;
} 
// only get dir path from db
function get_dirpath($parent)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   global $sess, $expand;
   $name = fid_to_name($parent);
   $navbar = "$name";
   $new = $parent;
   while ($new != "1")
   {
      $sql->query("SELECT parent from $default->owl_folders_table where id = '$new'");
      while ($sql->next_record()) $newparentid = $sql->f("parent");
      if ($newparentid == "") break;
      $name = fid_to_name($newparentid);
      $navbar = "$name/" . $navbar;
      $new = $newparentid;
   } 
   return $navbar;
} 

function get_dirpathfs($parent)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   global $sess, $expand;
   $name = fid_to_name($parent);
   $navbar = "$name";
   $new = $parent;
   while ($new != "1")
   {
      $sql->query("SELECT parent from $default->owl_folders_table where id = '$new'");
      while ($sql->next_record()) $newparentid = $sql->f("parent");
      if ($newparentid == "") break;
      $name = fid_to_name($newparentid);
      $navbar = "$name\\" . $navbar;
      $new = $newparentid;
   } 
   return $navbar;
} 

function fIsGroupAdmin($userid, $iGroup, $sSettingACL = "", $iObjectID = "0")
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }


   if ($sSettingACL == "FOLDER")
   {
      $iGroup = owlfoldergroup($iObjectID);
   }

   if ($sSettingACL == "FILE")
   {
      $iGroup = owlfilegroup($iObjectID);
   }


   if ($sSettingACL == "SETTING_FOLDER_ACL")
   {
      $sql->query("SELECT groupid from $default->owl_folders_table where id = '$iObjectID' and groupid='$iGroup'");
      $sql->next_record();
      if ($sql->num_rows($sql) == 0)
      {
         return false;
      }
   }

   if ($sSettingACL == "SETTING_FILE_ACL")
   {
      $sql->query("SELECT groupid from $default->owl_files_table where id = '$iObjectID' and groupid='$iGroup'");
      $sql->next_record();
      if ($sql->num_rows($sql) == 0)
      {
         return false;
      }
   }

   $sql->query("SELECT groupadmin from $default->owl_users_table where id = '$userid' and groupid='$iGroup'");
   $sql->next_record();
   if ($sql->f("groupadmin") == 1)
   {
      return true;
   } 

   $sql->query("SELECT userid FROM $default->owl_users_grpmem_table WHERE userid='$userid' and groupadmin='$iGroup'");
   $sql->next_record();
   if ($sql->num_rows($sql) > 0)
   {
      return true;
   }
   return false;

}

function fIsAdmin($Admin = false)
{
   global $default, $usergroupid, $userid, $cCommonDBConnection, $bAdminCache;


   if (! empty($bAdminCache)) 
   { 
      return $bAdminCache; 
   } 

   if (empty($userid))
   {
      $userid = 0;
      $usergroupid = -1;
   }

   if($Admin)
   {
      if ($usergroupid == "0")
      {
         $bAdminCache = true; 
         return true;
      }
      else
      {
         $sql = $cCommonDBConnection;
         if (empty($sql))
         {
            $sql = new Owl_DB;
         }
         $sql->query("SELECT userid,groupid from $default->owl_users_grpmem_table where userid = '$userid' and groupid = '0'");
   
         if ($sql->num_rows($sql) == 0)
         {
            $bAdminCache = false; 
            return false;
         }
         else 
         {    
            $bAdminCache = true; 
            return true;
         }
      }
   }
   else
   {
      if ($usergroupid == "0" or $usergroupid == $default->file_admin_group)
      {
         $bAdminCache = true; 
         return true;
      }
      else
      {
         $sql = $cCommonDBConnection;
         if (empty($sql))
         {
            $sql = new Owl_DB;
         }
         $sql->query("SELECT userid,groupid from $default->owl_users_grpmem_table where userid = '$userid' and (groupid = '$default->file_admin_group' or groupid = '0')");
   
         if ($sql->num_rows($sql) == 0)
         {
            $bAdminCache = false; 
            return false;
         }
         else 
         {
            $bAdminCache = true; 
            return true;
         }
      }
   }
   $bAdminCache = false; 
   return false;
}

function fIsEmailToolAccess($userid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $sql->query("SELECT email_tool from $default->owl_users_table where id = '$userid'");
   $sql->next_record();
   if ($sql->f("email_tool") == 1)
   {
      return true;
   } 
   return false;
} 

function fIsReportViewer($userid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }


   $sql->query("SELECT viewreports from $default->owl_users_table where id = '$userid'");
   $sql->next_record();
   if ($sql->f("viewreports") == 1)
   {
      return true;
   }
   return false;
}

function fIsLogViewer($userid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }


   $sql->query("SELECT viewlogs from $default->owl_users_table where id = '$userid'");
   $sql->next_record();
   if ($sql->f("viewlogs") == 1)
   {
      return true;
   }
   return false;
}

function fIsUserAdmin($userid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }
   $sql->query("SELECT useradmin from $default->owl_users_table where id = '$userid'");
   $sql->next_record();
   if ($sql->f("useradmin") == 1)
   {
      return true;
   }
   return false;
}

function fIsNewsAdmin($userid)
{
   global $default, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $sql->query("SELECT newsadmin from $default->owl_users_table where id = '$userid'");
   $sql->next_record();
   if ($sql->f("newsadmin") == 1)
   {
      return true;
   } 
   return false;
} 

function gen_filesize($file_size)
{
   global $owl_lang;

   if (ereg("[^0-9]", $file_size)) return $file_size;

   if ($file_size >= 1073741824)
   {
      $file_size = round($file_size / 1073741824 * 100) / 100 . $owl_lang->file_size_gigabyte;
   } elseif ($file_size >= 1048576)
   {
      $file_size = round($file_size / 1048576 * 100) / 100 . $owl_lang->file_size_megabyte;
   } elseif ($file_size >= 1024)
   {
      $file_size = round($file_size / 1024 * 100) / 100 . $owl_lang->file_size_kilobyte;
   } 
   else
   {
      if(!empty($file_size))
      {
         $file_size = $file_size . $owl_lang->file_size_byte;
      }
      else
      {
         $file_size = "0". $owl_lang->file_size_byte;
      }
   } 
   return $file_size;
} 

function uploadCompat($varname)
{
   global $HTTP_POST_FILES;

   if ($_FILES[$varname]) return $_FILES[$varname];
   if ($HTTP_POST_FILES[$varname]) return $HTTP_POST_FILES[$varname];
   $tmp = "$varname_name";
   global $$tmp;
   $retfile['name'] = $$tmp;
   $tmp = "$varname_type";
   global $$tmp;
   $retfile['type'] = $$tmp;
   $tmp = "$varname_size";
   global $$tmp;
   $retfile['size'] = $$tmp;
   $tmp = "$varname_error";
   global $$tmp;
   $retfile['error'] = $$tmp;
   $tmp = "$varname_tmp_name";
   global $$tmp;
   $retfile['tmp_name'] = $$tmp;
   return $retfile;
} 

function fGetMimeType ($filename)
{
   global $default;

   $mimeType = "application/octet-stream";

   if ($filetype = strrchr($filename, "."))
   {
      $filetype = substr($filetype, 1);
      $sql = new Owl_DB;
      $sql->query("SELECT * from $default->owl_mime_table where filetype = '$filetype'");
      while ($sql->next_record()) $mimeType = $sql->f("mimetype");
   } 
   return $mimeType;
} 

if ($sess)
{
   $ok = verify_session($sess);
   $temporary_ok = $ok["bit"];
   $userid = $ok["userid"];
   $default->owl_current_db = $ok["currentdb"];
   $default->owl_FileDir  =  $default->owl_db_FileDir[$default->owl_current_db];

   global $cCommonDBConnection;
   $cCommonDBConnection = new Owl_DB;
   $cCommonDBConnection->connect();

   getuserprefs();
   gethtmlprefs();
   $usergroupid = $ok["groupid"];



   if ($ok["bit"] != "1")
   { 
      if ($default->remember_me or isset($_COOKIE['owl_sessid']))
      {
         setcookie ("owl_sessid", "");
      }

      include_once($default->owl_fs_root . "/lib/header.inc");
      include_once($default->owl_fs_root . "/lib/userheader.inc");

      print("<center>\n");
      if ($expand == 1)
      {
         print("<table class='border1' cellspacing='0' cellpadding='0' border='0' width='$default->table_expand_width'><tr><td align='left' valign='top' width='100%'>\n");
      }
      else
      {
         print("<table class='border1' cellspacing='0' cellpadding='0' border='0' width='$default->table_collapse_width'><tr><td align='left' valign='top' width='100%'>\n");
      }
      fPrintButtonSpace(12, 1);
      print("<br />\n");
      print("<table class='border2' cellspacing='0' cellpadding='0' border='0' width='100%'><tr><td align='left' valign='top' width='100%'>\n");
      if ($default->show_prefs == 1 or $default->show_prefs == 3)
      {
         fPrintPrefs();
      }

      fPrintButtonSpace(12, 1);
      print("<br />\n");
      print("<table class='margin2' cellspacing='0' cellpadding='0' border='0' width='100%'>\n");
      print("<tr>\n");
      print("<td align='left' valign='top'>\n");
      print("<table cellspacing='0' cellpadding='0' border='0' width='100%'>\n");
      fPrintFormTextLine("&nbsp;", "", "",  $owl_lang->invalidsess , "", true);
      print("<tr>\n");
      print("<td class='form1'>");
      fPrintButtonSpace(1, 1);
      print("</td>\n");
      print("<td>\n");
      print("<table class='border1' cellspacing='0' cellpadding='0' border='0' width='100%'>\n");
      print("<tr>\n<td valign='top' width='100%'>");
      fPrintButtonSpace(1, 1);
      print("</td>");
      if ($parent == "" || $fileid == "")
      {
         fPrintButton("$default->owl_root_url/index.php", "btn_login");
      }
      else
      {
         fPrintButton("$default->owl_root_url/index.php?parent=$parent&fileid=$fileid", "btn_login");
      }   
      //print("\t\t<td class='button1' background=$default->owl_graphics_url/$default->sButtonStyle/ui_misc/button1_fill.jpg>");
      print("\t\t<td class='button1'>");
      print("<input class='fbuttonup1' type='submit' value='$owl_lang->btn_back' alt='$owl_lang->alt_back' title='$owl_lang->alt_back' onclick='history.back();' onmouseover=\"highlightButton('fbuttondown1', this)\" onmouseout=\"highlightButton('fbuttonup1', this)\"></input>");
      print("</td>\n</tr>\n</table>\n");
      print("</td>\n</tr>\n");

      
      print("</table>\n");
      print("</td>\n</tr>\n");
      print("</table>\n");

      fPrintButtonSpace(12, 1);
      print("</td>\n</tr>\n");
      print("</table>\n");
      include_once($default->owl_fs_root . "/lib/footer.inc");
      exit;
   } 
   else
   {
         $lastused = time();
         $sql = new Owl_DB;
      if (!($default->remember_me))
      {
         $sql->query("UPDATE $default->owl_sessions_table set lastused = '$lastused' where usid = '$userid' and sessid = '$sess'");
      } 
      elseif (!(isset($_COOKIE["owl_sessid"])))
      {
         $sql->query("UPDATE $default->owl_sessions_table set lastused = '$lastused' where usid = '$userid' and sessid = '$sess'");
      }
   } 
} 
else
{
  $usergroupid = "-1";
  $user = "-1";
}

function checkrequirements()
{
   global $default;
   global $owl_lang;

   $status = 0;
   if (ini_get("file_uploads") == 0)
   {
      print("<center><h3>FILE UPLOAD NEEDS TO BE ON IN YOUR php.ini</h3>");
      $status =  1;
   }
   if (version_compare(phpversion(), $default->phpversion) == -1)
   {
      print("<center><h3>$owl_lang->err_bad_version_1<br />");
      print("$default->phpversion<br />");
      print("$owl_lang->err_bad_version_2<br />");
      print phpversion();
      print("<br />$owl_lang->err_bad_version_3</h3></center>");
      $status =  1;
   } 

   if ($default->debug == true)
   {
      if (!file_exists($default->owl_tmpdir))
      {
         print("<center><h3>$owl_lang->debug_tmp_not_exists</h3></center>");
         $status =  1;
      } 
      else
      {
         if (!is_writable($default->owl_tmpdir))
         {
            print("<center><h3>$owl_lang->debug_tmp_not_writeable</h3></center>");
            print("</h3>");
            $status =  1;
         } 
      } 

      if (!file_exists($default->owl_FileDir . "/" . fid_to_name(1)))
      {
         print("<center><h3>$owl_lang->debug_doc_not_exists: $default->owl_FileDir</h3></center>");
         $status =  1;
      } 
      else
      {
         if (!is_writable($default->owl_FileDir . "/" . fid_to_name(1)))
         {
            print("<center><h3>$owl_lang->debug_doc_not_writeable</h3></center>");
            $status =  1;
         } 
      } 


      if(ini_get('safe_mode') == 1)
      {
            print("<center><h3>OWL REQUIRES SAFE MODE TO BE Off</h3></center>");
            $status =  1;
      }
   } 

   return $status;
} 

function myExec($_cmd, &$lines, &$errco)
{
   $cmd = "$_cmd ; echo $?";
   exec($cmd, $lines); 
   // Get rid of the last errco line...
   $errco = (integer) array_pop($lines);
   if (count($lines) == 0)
   {
      return "";
   } 
   else
   {
      return $lines[count($lines) - 1];
   } 
} 

function myDelete($file)
{
   if (file_exists($file))
   { 
      if (is_dir($file))
      {
         $handle = @opendir($file);
         while ($filename = @readdir($handle))
         {
            if ($filename != "." && $filename != "..")
            {
               myDelete($file . "/" . $filename);
            } 
         } 
         @closedir($handle);
         @rmdir($file);
      } 
      else
      {
         @unlink($file);
      } 
   } 
} 

function printError($message, $submessage = "", $type = "ERROR")
{
   global $default;
   global $sess, $parent, $expand, $order, $sortorder , $sortname, $userid;
   global $language;
   global $owl_lang;

   include_once("$default->owl_fs_root/lib/header.inc");
   include_once("$default->owl_fs_root/lib/userheader.inc");
   if (isset($parent))
   {
      if (check_auth($parent, "folder_view", $userid) != "1")
      {
         $sql = new Owl_DB;
         $sql->query("SELECT * from $default->owl_folders_table WHERE id = '$parent'");
         $sql->next_record();
         $parent = $sql->f("parent");
      } 
   } 
   print("<center>");
   if ($expand == 1)
   {
      print("<table class='border1' cellspacing='0' cellpadding='0' border='0' width='$default->table_expand_width'><tr><td align='left' valign='top' width='100%'>\n");
   }
   else
   {
      print("<table class='border1' cellspacing='0' cellpadding='0' border='0' width='$default->table_collapse_width'><tr><td align='left' valign='top' width='100%'>\n");
   }
   fPrintButtonSpace(12, 1);
   print("<br />\n");

   print("<table class='border2' cellspacing='0' cellpadding='0' border='0' width='100%'><tr><td align='left' valign='top' width='100%'>\n");

   if ($default->show_prefs == 1 or $default->show_prefs == 3)
   {
      if ($default->owl_maintenance_mode == 0)
      {
         fPrintPrefs();
      }
   }
   fPrintButtonSpace(12, 1);
   print("<br />\n");

   print("<table class='margin2' cellspacing='0' cellpadding='0' border='0' width='100%'>\n");
   print("<tr>\n");
   print("<td align='left' valign='top'>\n");
   print("<table cellspacing='0' cellpadding='0' border='0' width='100%'>\n");
   //print("<tr>\n");

   fPrintFormTextLine("--- $type  ---", "", "",  $message , "", true);

   if(!empty($submessage))
   {
      fPrintFormTextLine("--- DETAILS ---", "", "",  $submessage , "", true);
   }

   print("<tr>");
   print("<td class='form1'>");
   fPrintButtonSpace(1, 1);
   print("</td>");
   print("<td class='form2'>");
   print("<input class='fbuttonup1' type='submit' value='$owl_lang->btn_back' alt='$owl_lang->alt_back' title='$owl_lang->alt_back' onclick='history.back();' onmouseover=\"highlightButton('fbuttondown1', this)\" onmouseout=\"highlightButton('fbuttonup1', this)\"></input>");
   print("</td>");
   print("</tr>");

   print("</table>\n");
   print("</td></tr></table>\n");
   fPrintButtonSpace(12, 1);
         
   print("</td></tr></table>\n");
   include_once($default->owl_fs_root . "/lib/footer.inc");
   exit();
} 

function getuserprefs ()
{
   global $default, $userid, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

 
   if ($userid == "" )
   {
     $iUid = $default->anon_user;
   }
   else
   {
     $iUid = $userid;
   }
  

   $sql->query("SELECT user_offset, firstdir, homedir, buttonstyle from $default->owl_users_table WHERE id = '$iUid'");
   $sql->next_record();
   if ($sql->num_rows() == 1)
   {
      $default->HomeDir = $sql->f("homedir");
      $default->FirstDir = $sql->f("firstdir");
      $default->sButtonStyle = $sql->f("buttonstyle");
      $iOffsetDifference = $sql->f("user_offset") - $default->machine_time_zone + date("I");
      if ($iOffsetDifference == 0)
      {
         $default->time_offset = 0;
      }
      else
      {
         $default->time_offset = 3600 * ($iOffsetDifference - date("I"));
		
      }
   }
}

function getprefs ()
{
   global $default, $userid, $owl_lang, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   define ("NEW_FILE", "0");
   define ("UPDATED_FILE", "1");
   define ("NEW_COMMENT", "2");
   define ("DELETED_FILE", "3");
   define ("NEW_PASSWORD", "4");
   define ("NEW_APPROVED", "5");
   define ("REMINDER_APPROVED", "6");
   define ("FINAL_APPROVED", "7");
   define ("FINAL_AUTO_APPROVED", "8");
   define ("REJECT_APPROVED", "9");
   define ("ADMIN_PASSWORD", "10");
   define ("APPROVED", "11");
   define ("SELF_REG_USER", "12");

   define ("LOGIN", "1");
   define ("LOGIN_FAILED", "2");
   define ("LOGOUT", "3");
   define ("FILE_DELETED", "4");
   define ("FILE_UPLOAD", "5");
   define ("FILE_UPDATED", "6");
   define ("FILE_DOWNLOADED", "7");
   define ("FILE_CHANGED", "8");
   define ("FILE_LOCKED", "9");
   define ("FILE_UNLOCKED", "10");
   define ("FILE_EMAILED", "11");
   define ("FILE_MOVED", "12");
   define ("FOLDER_CREATED", "13");
   define ("FOLDER_DELETED", "14");
   define ("FOLDER_MODIFIED", "15");
   define ("FOLDER_MOVED", "16");
   define ("FORGOT_PASS", "17");
   define ("USER_REG", "18");
   define ("FILE_VIEWED", "19");
   define ("FILE_VIRUS", "20");
   define ("FILE_COPIED", "21");
   define ("FOLDER_COPIED", "22");
   define ("FILE_LINKED", "23");
   define ("USER_ADMIN", "24");
   define ("TRASH_CAN", "25");
   define ("FILE_ACL", "26");
   define ("FOLDER_ACL", "27");

   $sql->query("SELECT * from $default->owl_prefs_table");
   $sql->next_record();

   $default->owl_email_from = $sql->f("email_from");
   $default->owl_email_fromname = $sql->f("email_fromname");
   $default->owl_email_replyto = $sql->f("email_replyto");
   $default->owl_email_server = $sql->f("email_server");
   $default->owl_email_subject = $sql->f("email_subject");
   $default->use_smtp = $sql->f("use_smtp");
   $default->use_smtp_auth = $sql->f("use_smtp_auth");
   $default->smtp_auth_login  = $sql->f("smtp_auth_login");
   $default->smtp_passwd = $sql->f("smtp_passwd"); 
   // 
   // LookAtHD is not supported with $default->owl_use_fs = false
   // 
   if ($default->owl_use_fs)
   {
      $default->owl_LookAtHD = $sql->f("lookathd");
   } 
   else
   {
      if (substr(php_uname(), 0, 7) == "Windows")
      {
         $default->owl_compressed_database = 0;
      } 
      $default->owl_LookAtHD = "false";
   } 

   $default->owl_lookAtHD_del = $sql->f("lookathddel");
   $default->owl_def_file_security = $sql->f("def_file_security");
   $default->owl_def_file_group_owner = $sql->f("def_file_group_owner");
   $default->owl_def_file_owner = $sql->f("def_file_owner");
   $default->owl_def_file_title = $sql->f("def_file_title");
   $default->owl_def_file_meta = $sql->f("def_file_meta");
   $default->owl_def_fold_security = $sql->f("def_fold_security");
   $default->owl_def_fold_group_owner = $sql->f("def_fold_group_owner");
   $default->owl_def_fold_owner = $sql->f("def_fold_owner");
   $default->max_filesize = $sql->f("max_filesize");
   $default->owl_timeout = $sql->f("timeout");
   if ($sql->f("tmpdir") == "")
   {
      $default->owl_tmpdir = $default->owl_FileDir;
      $default->owl_tmpdir .= "/" . fid_to_name(1);
   } 
   else
   {
      $default->owl_tmpdir = $sql->f("tmpdir");
   } 
   $default->expand = $sql->f("expand");
   $default->owl_version_control = $sql->f("version_control");
   $default->major_revision = $sql->f("major_revision");
   $default->minor_revision = $sql->f("minor_revision");

   $default->restrict_view = $sql->f("restrict_view");
   $default->dbdump_path = $sql->f("dbdump_path");
   $default->gzip_path = $sql->f("gzip_path");
   $default->tar_path = $sql->f("tar_path");
   $default->unzip_path = $sql->f("unzip_path");
   $default->pod2html_path = $sql->f("pod2html_path");
   $default->pdftotext_path = $sql->f("pdftotext_path");
   $default->rtftotext_path = $sql->f("rtftotext_path");
   $default->wordtotext_path = $sql->f("wordtotext_path");
   $default->file_perm = $sql->f("file_perm");
   $default->folder_perm = $sql->f("folder_perm");

   $default->anon_ro = $sql->f("anon_ro");
   $default->anon_user = $sql->f("anon_user");
   $default->file_admin_group = $sql->f("file_admin_group");

   $default->hide_backup = $sql->f("hide_backup");

   $default->collect_trash = $sql->f("collect_trash");
   if ($sql->f("trash_can_location") == "")
   {
      $default->trash_can_location = $default->owl_FileDir . "/TrashCan";
   } 
   else
   {
      $default->trash_can_location = $sql->f("trash_can_location");
   } 

   $default->allow_popup = $sql->f("allow_popup");
   $default->allow_custfieldspopup=$sql->f("allow_custpopup");
   $default->show_file_stats = $sql->f("status_bar_location");
   $default->display_file_info_panel_wide = $sql->f("info_panel_wide");
   $default->allow_track_favorites = $sql->f("track_favorites");

   $default->show_prefs = $sql->f("pref_bar");
   $default->show_search = $sql->f("search_bar");
   $default->show_bulk = $sql->f("bulk_buttons");
   $default->show_action = $sql->f("action_buttons");
   $default->show_folder_tools = $sql->f("folder_tools");

   //$default->hide_bulk = $sql->f("hide_bulk"); 
   // 
   // Logging options
   // 
   $default->logging = $sql->f("logging");
   $default->log_file = $sql->f("log_file");
   $default->log_login = $sql->f("log_login");
   $default->log_rec_per_page = $sql->f("log_rec_per_page"); 
   //
   // Sticky loggin (remember me Link)
   //
   $default->remember_me = $sql->f("remember_me");
   $default->cookie_timeout = $sql->f("cookie_timeout");

   // 
   // Self Register options
   // 
   $default->self_reg = $sql->f("self_reg");
   $default->self_reg_quota = $sql->f("self_reg_quota");
   $default->self_reg_notify = $sql->f("self_reg_notify");
   $default->self_reg_attachfile = $sql->f("self_reg_attachfile");
   $default->self_reg_disabled = $sql->f("self_reg_disabled");
   $default->self_reg_noprefacces = $sql->f("self_reg_noprefacces");
   $default->self_reg_maxsessions = $sql->f("self_reg_maxsessions");
   $default->self_reg_group = $sql->f("self_reg_group");
   $default->forgot_pass = $sql->f("forgot_pass");
   $default->records_per_page = $sql->f("rec_per_page");
   $default->self_reg_homedir = $sql->f("self_reg_homedir");
   $default->self_reg_firstdir = $sql->f("self_reg_firstdir");
   $default->self_create_homedir = $sql->f("self_create_homedir");
   $default->registration_using_captcha = $sql->f("self_captcha");

   $default->expand_disp_status = $sql->f("expand_disp_status");
   $default->expand_disp_doc_num = $sql->f("expand_disp_doc_num");
   $default->expand_disp_doc_type = $sql->f("expand_disp_doc_type");
   $default->expand_disp_doc_fields =$sql->f("expand_disp_doc_fields");
   $default->expand_disp_title = $sql->f("expand_disp_title");
   $default->expand_disp_version = $sql->f("expand_disp_version");
   $default->expand_disp_file = $sql->f("expand_disp_file");
   $default->expand_disp_size = $sql->f("expand_disp_size");
   $default->expand_disp_posted = $sql->f("expand_disp_posted");
   $default->expand_disp_updated = $sql->f("expand_disp_updated");
   $default->expand_disp_modified = $sql->f("expand_disp_modified");
   $default->expand_disp_action = $sql->f("expand_disp_action");
   $default->expand_disp_held = $sql->f("expand_disp_held");

   $default->collapse_disp_status = $sql->f("collapse_disp_status");
   $default->collapse_disp_doc_num = $sql->f("collapse_disp_doc_num");
   $default->collapse_disp_doc_type = $sql->f("collapse_disp_doc_type");
   $default->collapse_disp_doc_fields =$sql->f("collapse_disp_doc_fields");
   $default->collapse_disp_title = $sql->f("collapse_disp_title");
   $default->collapse_disp_version = $sql->f("collapse_disp_version");
   $default->collapse_disp_file = $sql->f("collapse_disp_file");
   $default->collapse_disp_size = $sql->f("collapse_disp_size");
   $default->collapse_disp_posted = $sql->f("collapse_disp_posted");
   $default->collapse_disp_updated = $sql->f("collapse_disp_updated");
   $default->collapse_disp_modified = $sql->f("collapse_disp_modified");
   $default->collapse_disp_action = $sql->f("collapse_disp_action");
   $default->collapse_disp_held = $sql->f("collapse_disp_held");

   $default->expand_search_disp_score =  $sql->f("expand_search_disp_score");
   $default->expand_search_disp_folder_path = $sql->f("expand_search_disp_folder_path");
   $default->expand_search_disp_doc_type = $sql->f("expand_search_disp_doc_type");
   $default->expand_search_disp_doc_fields = $sql->f("expand_search_disp_doc_fields");
   $default->expand_search_disp_file = $sql->f("expand_search_disp_file");
   $default->expand_search_disp_size = $sql->f("expand_search_disp_size");
   $default->expand_search_disp_posted = $sql->f("expand_search_disp_posted");
   $default->expand_search_disp_updated = $sql->f("expand_search_disp_updated");
   $default->expand_search_disp_modified = $sql->f("expand_search_disp_modified");
   $default->expand_search_disp_action = $sql->f("expand_search_disp_action");
   $default->expand_search_disp_doc_num = $sql->f("expand_search_disp_doc_num");


   $default->collapse_search_disp_score =  $sql->f("collapse_search_disp_score");
   $default->collapse_search_disp_folder_path = $sql->f("colps_search_disp_fld_path");
   $default->collapse_search_disp_doc_type = $sql->f("collapse_search_disp_doc_type");
   $default->colps_search_disp_doc_fields = $sql->f("colps_search_disp_doc_fields");
   $default->collapse_search_disp_file = $sql->f("collapse_search_disp_file");
   $default->collapse_search_disp_size = $sql->f("collapse_search_disp_size");
   $default->collapse_search_disp_posted = $sql->f("collapse_search_disp_posted");
   $default->collapse_search_disp_updated = $sql->f("collapse_search_disp_updated");
   $default->collapse_search_disp_modified = $sql->f("collapse_search_disp_modified");
   $default->collapse_search_disp_action = $sql->f("collapse_search_disp_action");
   $default->collapse_search_disp_doc_num = $sql->f("collapse_search_disp_doc_num");

   $default->hide_folder_doc_count	= $sql->f("hide_folder_doc_count");
   $default->old_action_icons	= $sql->f("old_action_icons");
   $default->search_result_folders	= $sql->f("search_result_folders");
   $default->restore_file_prefix	= $sql->f("restore_file_prefix");


   $default->doc_id_prefix = $sql->f("doc_id_prefix");
   $default->doc_id_num_digits = $sql->f("doc_id_num_digits");

   $default->view_doc_in_new_window = $sql->f("view_doc_in_new_window");

   $default->admin_login_to_browse_page = $sql->f("admin_login_to_browse_page");

   $default->save_keywords_to_db = $sql->f("save_keywords_to_db");
   $default->anon_access = $sql->f("anon_ro");

   $default->document_peer_review = $sql->f("peer_review");
   $default->document_peer_review_optional = $sql->f("peer_opt");
   $default->hide_folder_size = $sql->f("folder_size");
   $default->use_zip_for_folder_download = $sql->f("download_folder_zip");
   $default->display_password_override = $sql->f("display_password_override");
   $default->virus_path = $sql->f("virus_path");

   $default->thumb_disp_status =  $sql->f("thumb_disp_status");
   $default->thumb_disp_doc_num =  $sql->f("thumb_disp_doc_num");
   $default->thumb_disp_image_info =  $sql->f("thumb_disp_image_info");
   $default->thumb_disp_version =  $sql->f("thumb_disp_version");
   $default->thumb_disp_size =  $sql->f("thumb_disp_size");
   $default->thumb_disp_posted =  $sql->f("thumb_disp_posted");
   $default->thumb_disp_updated =  $sql->f("thumb_disp_updated");
   $default->thumb_disp_modified =  $sql->f("thumb_disp_modified");
   $default->thumb_disp_action =  $sql->f("thumb_disp_action");
   $default->thumb_disp_held =  $sql->f("thumb_disp_held");
   $default->thumbnails_tool_path = $sql->f("thumbnails_tool_path");
   $default->thumbnails_video_tool_path = $sql->f("thumbnails_video_tool_path");
   $default->thumbnails_video_tool_opt = $sql->f("thumbnails_video_tool_opt");

   $default->thumbnails_small_width = $sql->f("thumbnails_small_width");
   $default->thumbnails_med_width = $sql->f("thumbnails_med_width");
   $default->thumbnails_large_width = $sql->f("thumbnails_large_width");
   $default->thumbnail_view_columns= $sql->f("thumbnail_view_columns");
   $default->thumbnails= $sql->f("thumbnails");


   $default->min_pass_length = $sql->f("min_pass_length");
   $default->min_username_length = $sql->f("min_username_length");
   $default->min_pass_numeric = $sql->f("min_pass_numeric");
   $default->min_pass_special = $sql->f("min_pass_special");
   $default->enable_lock_account = $sql->f("enable_lock_account");
   $default->lock_account_bad_password = $sql->f("lock_account_bad_password");
   $default->track_user_passwords = $sql->f("track_user_passwords");
   $default->change_password_every = $sql->f("change_password_every");

   $default->file_desc_req = $sql->f("filedescreq");
   $default->folder_desc_req = $sql->f("folderdescreq");
   $default->show_user_info = $sql->f("show_user_info");

   // 0 = Old Standard Owl Authentication
   // 1 = .htaccess authentication (username must also exists as the Owl users Table)
   // 2 = pop3 authentication (username must also exists as the Owl users Table)
   // 3 = LDAP authentication (username must also exists as the Owl users Table)

   $default->lookHD_ommit_ext[] = "owlctl";

   if (!$default->old_action_icons)
   {
      require_once ($default->owl_fs_root . "/scripts/phplayersmenu/lib/PHPLIB.php");
      require_once ($default->owl_fs_root . "/scripts/phplayersmenu/lib/layersmenu-common.inc.php");
      require_once ($default->owl_fs_root . "/scripts/phplayersmenu/lib/layersmenu.inc.php");
   }
}

function fIsQuotaEnabled($current_user)
{
   global $default ;
   global $owl_lang;

   $quota_max = 0;
   $sql = new Owl_DB;
   $sql->query("SELECT * from $default->owl_users_table where id = '$current_user'");
   while ($sql->next_record())
   {
      $quota_max = $sql->f("quota_max");
   }
   if ( $quota_max == 0)
   {
      return false;
   }
   else
   {
      return true;
   }
}
function fCalculateQuota($size, $current_user, $type)
{
   global $default;
   global $owl_lang;

   $sql = new Owl_DB;
   $sql->query("SELECT * from $default->owl_users_table where id = '$current_user'");
   while ($sql->next_record())
   {
      $quota_max = $sql->f("quota_max");
      $quota_current = $sql->f("quota_current");
      if ($type == "ADD")
      {
         $new_quota = $quota_current + $size;
      }
      elseif ($type == "DEL")
      {
         $new_quota = $quota_current - $size;
      }
   }
   if (($new_quota > $quota_max) and fIsQuotaEnabled($current_user))
   {
      printError("<b class=hilite>" . uid_to_name($current_user) ."</b>: $owl_lang->err_quota" . gen_filesize($size) . "$owl_lang->err_quota_needed" . gen_filesize($quota_max - $quota_current) . "$owl_lang->err_quota_avail");
      if (($quota_max - $quota_current) <= 0)
      {
         printError("$owl_lang->err_quota_exceed");
      }
   }
   return $new_quota;
}

function printfileperm($currentval, $namevariable, $printmessage, $type)
{
   global $default;
   global $owl_lang;

   $file_perm[0][0] = 0;
   $file_perm[1][0] = 1;
   $file_perm[2][0] = 2;
   $file_perm[3][0] = 3;
   $file_perm[4][0] = 4;
   $file_perm[5][0] = 5;
   $file_perm[6][0] = 6;
   $file_perm[7][0] = 7;
   $file_perm[8][0] = 8;

   if ($type == "admin")
   {
      $file_perm[0][1] = "$owl_lang->everyoneread_ad";
      $file_perm[1][1] = "$owl_lang->everyonewrite_ad";
      $file_perm[2][1] = "$owl_lang->groupread_ad";
      $file_perm[3][1] = "$owl_lang->groupwrite_ad";
      $file_perm[4][1] = "$owl_lang->onlyyou_ad";
      $file_perm[5][1] = "$owl_lang->groupwrite_ad_nod";
      $file_perm[6][1] = "$owl_lang->everyonewrite_ad_nod";
      $file_perm[7][1] = "$owl_lang->groupwrite_worldread_ad";
      $file_perm[8][1] = "$owl_lang->groupwrite_worldread_ad_nod";
   } 
   else
   {
      $file_perm[0][1] = "$owl_lang->everyoneread";
      $file_perm[1][1] = "$owl_lang->everyonewrite";
      $file_perm[2][1] = "$owl_lang->groupread";
      $file_perm[3][1] = "$owl_lang->groupwrite";
      $file_perm[4][1] = "$owl_lang->onlyyou";
      $file_perm[5][1] = "$owl_lang->groupwrite_nod";
      $file_perm[6][1] = "$owl_lang->everyonewrite_nod";
      $file_perm[7][1] = "$owl_lang->groupwrite_worldread";
      $file_perm[8][1] = "$owl_lang->groupwrite_worldread_nod";
   } 

   print("<tr>\n");
   print("<td class='form1'>$printmessage</td>\n");
   print("<td class='form1' width='100%'>");
   print("<select class='fpull1' name='$namevariable' size='1'>\n");
   foreach($file_perm as $fp)
   {
      print("<option value='$fp[0]'");
      if ($fp[0] == $currentval)
      {
         print(" selected='selected'");
      }
      print(">$fp[1]</option>\n");
   }
   print("</select>\n</td>\n</tr>\n");
} ;

function owl_syslog($action, $userid, $filename, $logparent, $detail, $type)
{
   global $default;

   if ($default->logging == 1)
   {
      $sql = new Owl_DB;
      $log = 0;

      $logdate = date("Y-m-d G:i:s");
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
      $agent = $_SERVER["HTTP_USER_AGENT"];
      if ($default->log_file == 1 && $type == "FILE")
      {
         $log = 1;
      } 
      if ($default->log_login == 1 && $type == "LOGIN")
      {
         $log = 1;
      } 
      if ($type == "ADMIN")
      {
         $log = 1;
      } 
      if ($log == 1)
      {
         if (empty($logparent))
         {
            $logparent = 0;
         }
         $sql->query("INSERT into $default->owl_log_table (userid, filename, action, parent, details, logdate, ip, agent, type) values ('$userid', '$filename', '$action', '$logparent', '$detail', '$logdate', '$ip', '$agent', '$type')");
      } 
   } 
} 


function change_ownership_perms($file, $id, $func_parent, $fileowner, $groupid, $policy, $prop_file_sec)
{
   global $default;

   if ( $id == "1")
   {
      $file = "";
   }
   //if (is_dir($default->owl_FileDir . "/" . find_path($func_parent) . "/" . $file)) 
   //{
      $sql = new Owl_DB;
      $smodified = $sql->now();
      $sql->query("UPDATE $default->owl_folders_table SET creatorid='$fileowner', groupid='$groupid', security='$policy', smodified=$smodified WHERE id='$id'");
      if ($prop_file_sec >= 0 )
      {
         $sql = new Owl_DB;
         $sql->query("UPDATE $default->owl_files_table SET creatorid='$fileowner', groupid='$groupid', security='$prop_file_sec', smodified=$smodified where parent='$id'");
      }
   
      $sql = new Owl_DB;
      $sql->query("SELECT name, id from $default->owl_folders_table where parent='$id'");
      while($sql->next_record())
      {
         $newfile = $sql->f("name");
         $newid = $sql->f("id");
         change_ownership_perms($newfile, $newid, $id, $fileowner, $groupid, $policy, $prop_file_sec);
      }
   //} 
   //else 
   //{
      //if ($default->debug == true)
      //{
         //printError("DEBUG: Security Propagation attempt on a file");
      //} 
   //}
}


function printgroupperm($currentval, $namevariable, $printmessage, $type)
{
   global $default;
   global $owl_lang;

   $group_perm[0][0] = 50;
   $group_perm[1][0] = 51;
   $group_perm[2][0] = 52;
   $group_perm[3][0] = 53;
   $group_perm[4][0] = 54;
   $group_perm[5][0] = 55;
   $group_perm[6][0] = 56;
   $group_perm[7][0] = 57;
   $group_perm[8][0] = 58;

   if ($type == "admin")
   {
      $group_perm[0][1] = "$owl_lang->geveryoneread_ad";
      $group_perm[1][1] = "$owl_lang->geveryonewrite_ad";
      $group_perm[2][1] = "$owl_lang->ggroupread_ad";
      $group_perm[3][1] = "$owl_lang->ggroupwrite_ad";
      $group_perm[4][1] = "$owl_lang->gonlyyou_ad";
      $group_perm[5][1] = "$owl_lang->ggroupwrite_ad_nod";
      $group_perm[6][1] = "$owl_lang->geveryonewrite_ad_nod";
      $group_perm[7][1] = "$owl_lang->ggroupwrite_worldread_ad";
      $group_perm[8][1] = "$owl_lang->ggroupwrite_worldread_ad_nod";
   } 
   else
   {
      $group_perm[0][1] = "$owl_lang->geveryoneread";
      $group_perm[1][1] = "$owl_lang->geveryonewrite";
      $group_perm[2][1] = "$owl_lang->ggroupread";
      $group_perm[3][1] = "$owl_lang->ggroupwrite";
      $group_perm[4][1] = "$owl_lang->gonlyyou";
      $group_perm[5][1] = "$owl_lang->ggroupwrite_nod";
      $group_perm[6][1] = "$owl_lang->geveryonewrite_nod";
      $group_perm[7][1] = "$owl_lang->ggroupwrite_worldread";
      $group_perm[8][1] = "$owl_lang->ggroupwrite_worldread_nod";
   } 

   print("<tr>\n");
   print("<td class='form1'>$printmessage</td>\n");
   print("<td class='form1' width='100%'>");
   print("<select class='fpull1' name='$namevariable' size='1'>\n");
   foreach($group_perm as $fp)
   {
      print("<option value='$fp[0]' ");
      if ($fp[0] == $currentval)
      {
         print("selected='selected'");
      }
      print(">$fp[1]</option>\n");
   } 
   print("</select></td>\n</tr>\n");
} ;

function get_title_tag($chaine)
{
   $fp = @fopen ($chaine, 'r');
   if ($fp)
   {
      while (! feof ($fp))
      {
         $contenu .= fgets ($fp, 1024);
         if (stristr($contenu, '</title>'))
         {
            break;
         } 
      } 
      if (eregi("<title>(.*)</title>", $contenu, $out))
      {
         return $out[1];
      } 
      else
      {
         return false;
      } 
   }
   else
   {
      return false;
   }
} 

function RndInt($Format)
{
   switch ($Format)
   {
      case "letter":
      case "special":
         $Rnd = rand(0, 25);
         if ($Rnd > 25)
         {
            $Rnd = $Rnd - 1;
         } 
         break;
      case "number":
         $Rnd = rand(0, 9);
         if ($Rnd > 9)
         {
            $Rnd = $Rnd - 1;
         } 
         break;
   } 
   return $Rnd;
} 

function GenRandPassword()
{
   /**
    * RANDOM PASSWORD GENERATION ALGORITHM
    * PROGRAMMED BY: BRIAN GRIFFIN
    * January 1, 2003
    * MXrider005@hotmail.com
    * 
    * You can use this freely. Just don't credit it as your own work! And please e-mail me if you do just to let me know. Thanks.
    */
   // DEFINE STRINGS TO USE FOR CHARACTER C // OMBINATIONS IN THE PASSWORD
   $LCase = "abcdefghijklmnopqrstuvwxyz";
   $UCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
   $Special = "~!@#$%^&*()_+}{|:;'>?<,/\\";
   $Integer = "0123456789"; 
   // DEFINE CONSTANTS FOR ALGORTTHM
   define("LEN", "1");
   /**
    * THIS FUNCTION GENERATES A RANDOM NUMBER THAT WILL BE USED TO
    * RANDOMLY SELECT CHARACTERS FROM THE STRINGS ABOVE
    */

   /**
    * RUN THE FUNCTION TO GENERATE RANDOM INTEGERS FOR EACH OF THE
    * 6 CHARACTERS IN THE PASSWORD PRODUCED.
    */
   $a = RndInt("letter");
   $b = RndInt("letter");
   $c = RndInt("letter");
   $d = RndInt("letter");
   $e = RndInt("number");
   $f = RndInt("number"); 
   $g = RndInt("special"); 
   $h = RndInt("letter"); 

   // EXTRACT 6 CHARACTERS RANDOMLY FROM TH // E DEFINITION STRINGS
   $L1 = substr($LCase, $a, LEN);
   $L2 = substr($LCase, $b, LEN);
   $U1 = substr($UCase, $c, LEN);
   $U2 = substr($UCase, $d, LEN);
   $I1 = substr($Integer, $e, LEN);
   $I2 = substr($Integer, $f, LEN); 
   $S1 = substr($Special, $g, LEN); 
   $L3 = substr($LCase, $h, LEN); 

   // COMBINE THE CHARACTERS AND DISPLAY TH // E NEW PASSWORD
   $PW = $L1 . $U2 . $I1 . $L2 . $I2 . $U1 . $L3 . $S1;
   return $PW;
} 


if (!$sess && !$loginname && !$login)
{
//modif1 misterphi
header("Location: " . $default->owl_root_url . "/index.php");
//eom1
   if (!isset($fileid))
   { 
      $sql = new Owl_DB;
      $sql->query("SELECT * from $default->owl_users_table where id = '$default->anon_user'");
      $sql->next_record();
      if ($sql->num_rows() == 1)
      {
         $accountname = $sql->f("name");
         if ($sql->f("disabled") != 1)
         {
            $userid = $default->anon_user;
         }
         else
         {
            if ($_SERVER["PHP_SELF"] != $default->owl_root_url . "/register.php")
            {
               if(isset($parent) and is_numeric($parent))
               {
                  header("Location: " . $default->owl_root_url . "/index.php?login=1&parent=$parent&currentdb=$default->owl_current_db");
               }
               else
               {
                  header("Location: " . $default->owl_root_url . "/index.php?login=1&currentdb=$default->owl_current_db");
               }

            }
         } 
      } 
      else
      {
         if ($_SERVER["PHP_SELF"] != $default->owl_root_url . "/register.php")
         {
            if(isset($parent) and is_numeric($parent))
            {
               header("Location: " . $default->owl_root_url . "/index.php?login=1&parent=$parent&currentdb=$default->owl_current_db");
            }
            else
            {
               header("Location: " . $default->owl_root_url . "/index.php?login=1&currentdb=$default->owl_current_db");
            }
         }
      } 
   } 
   else
   {
      //if ($default->anon_ro > 3)
      //{
         //header("Location: " . $default->owl_root_url . "/index.php?login=1&fileid=$fileid&parent=$parent");
      //}
      //else
      //{
         $sql = new Owl_DB;
         $sql->query("SELECT * from $default->owl_users_table where id = '$default->anon_user'");
         $sql->next_record();
         if ($sql->num_rows() == 1)
         {
            $accountname = $sql->f("name");
            if ($sql->f("disabled") != 1)
            {
               $userid = $default->anon_user;
            }
            else
            {
               if ($_SERVER["PHP_SELF"] != $default->owl_root_url . "/register.php")
               {
                  header("Location: " . $default->owl_root_url . "/index.php?login=1&fileid=$fileid&parent=$parent&currentdb=$default->owl_current_db");
               }
            } 
         } 
         else
         {
            if ($_SERVER["PHP_SELF"] != $default->owl_root_url . "/register.php")
            {	
               header("Location: " . $default->owl_root_url . "/index.php?login=1&failure=4&fileid=$fileid&parent=$parent&currentdb=$default->owl_current_db");
            }
         } 
      //}
   } 
} 

//print("DB: $default->owl_current_db") ;
//print("L: $_POST[loginname] -- $_GET[loginname]") ;
//print("<br />S: $sess") ;
//exit;

if (!$sess && $loginname)
{
//modif2misterphi
header("Location: " . $default->owl_root_url . "/index.php");
//eom2
   $sql = new Owl_DB;
   $sql->query("SELECT * from $default->owl_users_table where id = '$default->anon_user'");
   $sql->next_record();
   if ($sql->num_rows() == 1)
   {
      if ($sql->f("disabled") != 1)
      {
         $userid = $default->anon_user;
      } 
      else
      {
         header("Location: " . $default->owl_root_url . "/index.php?login=1");
      } 
   } 
   else
   {
      header("Location: " . $default->owl_root_url . "/index.php?login=1");
   } 
} 

if (!$sess && $login)
{
//modif3 misterphi
header("Location: " . $default->owl_root_url . "/index.php");
//eom3
   if ($_SERVER["PHP_SELF"] != $default->owl_root_url . "/index.php")
      header("Location: " . $default->owl_root_url . "/index.php?login=1");
} 

function fFindFileExtension ($filename)
{
   $filesearch = explode('.', $filename);
   $extensioncounter = 0;
   while ($filesearch[$extensioncounter + 1] != null)
   { 
      // pre-append a "." separator in the name for each
      // subsequent part of the the name of the file.
      if ($extensioncounter != 0)
      {
         $firstpart = $firstpart . ".";
      } 
      $firstpart = $firstpart . $filesearch[$extensioncounter];
      $extensioncounter++;
   } 
   if ($extensioncounter == 0)
   {
      $firstpart = $filename;
      $file_extension = '';
   } 
   else
   {
      $file_extension = $filesearch[$extensioncounter];
   } 
   return strtolower($file_extension);
} 

function fFindFileFirstpartExtension ($filename, $sDelimiter = ".")
{
   $filesearch = explode($sDelimiter, $filename);
   $extensioncounter = 0;
   while ($filesearch[$extensioncounter + 1] != null)
   {
      // pre-append a "." separator in the name for each
      // subsequent part of the the name of the file.
      if ($extensioncounter != 0)
      {
         $firstpart = $firstpart . $sDelimiter;
      }
      $firstpart = $firstpart . $filesearch[$extensioncounter];
      $extensioncounter++;
   }
   if ($extensioncounter == 0)
   {
      $firstpart = $filename;
      $file_extension = '';
      $haveextension="";
   }
   else
   {
      $file_extension = $filesearch[$extensioncounter];
      $haveextension=".";
   }
   $aFileLog = array();
   $aFileLog[0] = $firstpart;
   $aFileLog[1] = $file_extension;
   $aFileLog[2] = $haveextension;

   return $aFileLog;
}
   

if (!function_exists("file_get_contents")) 
{
   function file_get_contents($filename, $use_include_path = 0) 
   {
      $data = ""; // just to be safe. Dunno, if this is really needed
      $file = @fopen($filename, "rb", $use_include_path);
      if ($file) 
      {
         while (!feof($file)) $data .= fread($file, 1024);
         fclose($file);
      }
      return $data;
   }
}


function my_copy($oldname, $newname)
{
   if(is_file($oldname))
   {
      $perms = fileperms($oldname);
      return copy($oldname, $newname) && chmod($newname, $perms);
   }
   else if(is_dir($oldname))
   {
      my_dir_copy($oldname, $newname);
   }
   else
   {
      die("Cannot copy file: $oldname (it's neither a file nor a directory)");
   }
}
 
function my_dir_copy($oldname, $newname)
{

   global $default;

   if(!is_dir($newname))
   {
      mkdir($newname, $default->directory_mask);
   }

   $dir = opendir($oldname);
   while($file = readdir($dir))
   {
      if($file == "." || $file == "..")
      {
         continue;
      }
      my_copy("$oldname/$file", "$newname/$file");
   }
   closedir($dir);
}

function fCopyFolder ($Folderid, $destparent)
{
   global $default;
   $GetFolder = new Owl_DB;
   $InsertFolder = new Owl_DB;
   $smodified = $InsertFolder->now();
   $GetFolder->query("SELECT * from $default->owl_folders_table where id ='$Folderid'");
   $GetFolder->next_record();

   if ($GetFolder->num_rows() == 1)
   {
      $InsertFolder->query("INSERT into $default->owl_folders_table (name, parent, security, groupid, creatorid, description, smodified)  values ('". $GetFolder->f("name") ."', '" . $destparent ."', '" . $GetFolder->f("security") . "', '" . $GetFolder->f("groupid") . "', '" . $GetFolder->f("creatorid") . "', '" . $GetFolder->f("description") . "', $smodified)");

      $newParent = $InsertFolder->insert_id($default->owl_folders_table, 'id');

      // COPY ACL's

      $GetAcl = new Owl_DB;
      $PutAcl = new Owl_DB;
     
      $GetAcl->query("SELECT * from $default->owl_advanced_acl_table  where folder_id ='" . $Folderid . "'");

      while ( $GetAcl->next_record() )
      {
         if (is_null($GetAcl->f("group_id")))
         {
            $iGroup_Id = "NULL, ";
         }
         else
         {
            $iGroup_Id = "'" . $GetAcl->f("group_id") . "', ";
         }
         if (is_null($GetAcl->f("user_id")))
         {
            $iUser_Id = "NULL, ";
         }
         else
         {
            $iUser_Id = "'" . $GetAcl->f("user_id"). "', ";
         }

         $PutAcl->query("INSERT INTO $default->owl_advanced_acl_table (group_id, user_id, file_id, folder_id, owlread, owlwrite, owlviewlog, owldelete, owlcopy, owlmove, owlproperties, owlupdate, owlcomment, owlcheckin, owlemail, owlrelsearch, owlsetacl, owlmonitor) values (" . $iGroup_Id  .  $iUser_Id . "NULL , '" .  $newParent . "', '" .  $GetAcl->f("owlread") . "', '" .  $GetAcl->f("owlwrite") . "', '" .  $GetAcl->f("owlviewlog") . "', '" .  $GetAcl->f("owldelete") . "', '" .  $GetAcl->f("owlcopy") . "', '" .  $GetAcl->f("owlmove") . "', '" .  $GetAcl->f("owlproperties") . "', '" .  $GetAcl->f("owlupdate") . "', '" .  $GetAcl->f("owlcomment") . "', '" .  $GetAcl->f("owlcheckin") . "', '" .  $GetAcl->f("owlemail") . "', '" .  $GetAcl->f("owlrelsearch") . "', '" .  $GetAcl->f("owlsetacl") . "', '" .  $GetAcl->f("owlmonitor") . "')");
      }


      $GetFiles = new Owl_DB;
      $PutFiles = new Owl_DB;
      $GetFileData = new Owl_DB;
      $PutFileData = new Owl_DB;
      $GetDoctype = new Owl_DB;
      $PutDoctype = new Owl_DB;
      $GetFiles->query("SELECT * from $default->owl_files_table where parent ='" . $GetFolder->f("id") . "'");
      while ( $GetFiles->next_record() )
      {         
         // INSERT Files
         $PutFiles->query("INSERT into $default->owl_files_table (name,filename,f_size,creatorid,parent,created, description,metadata,security,groupid,smodified,checked_out, major_revision, minor_revision, url, doctype, approved) values ('" . $GetFiles->f("name") . "' , '" . $GetFiles->f("filename") . "' , '" . $GetFiles->f("f_size") . "' , '" . $GetFiles->f("creatorid") . "' , '$newParent', '" . $GetFiles->f("created") . "' , '" . $GetFiles->f("description") . "' , '" . $GetFiles->f("metadata") . "' , '" . $GetFiles->f("security") . "' , '" . $GetFiles->f("groupid") . "' , '" . $GetFiles->f("smodified") . "' , '" . $GetFiles->f("checked_out") . "' , '" . $GetFiles->f("major_revision") . "' , '" . $GetFiles->f("minor_revision") . "' , '" . $GetFiles->f("url") . "' , '" . $GetFiles->f("doctype") . "' , '1')");

         $newFile = $PutFiles->insert_id($default->owl_files_table, 'id');

         $GetAcl->query("SELECT * from $default->owl_advanced_acl_table  where file_id ='" . $GetFiles->f("id") . "'");

         while ( $GetAcl->next_record() )
         {
            if (is_null($GetAcl->f("group_id")))
            {
               $iGroup_Id = "NULL, ";
            }
            else
            {
               $iGroup_Id = "'" . $GetAcl->f("group_id") . "', ";
            }
            if (is_null($GetAcl->f("user_id")))
            {
               $iUser_Id = "NULL, ";
            }
            else
            {
               $iUser_Id = "'" . $GetAcl->f("user_id"). "', ";
            }
      
            $PutAcl->query("INSERT INTO $default->owl_advanced_acl_table (group_id, user_id, folder_id, file_id, owlread, owlwrite, owlviewlog, owldelete, owlcopy, owlmove, owlproperties, owlupdate, owlcomment, owlcheckin, owlemail, owlrelsearch, owlsetacl, owlmonitor) values (" . $iGroup_Id  .  $iUser_Id . "NULL , '" .  $newFile . "', '" .  $GetAcl->f("owlread") . "', '" .  $GetAcl->f("owlwrite") . "', '" .  $GetAcl->f("owlviewlog") . "', '" .  $GetAcl->f("owldelete") . "', '" .  $GetAcl->f("owlcopy") . "', '" .  $GetAcl->f("owlmove") . "', '" .  $GetAcl->f("owlproperties") . "', '" .  $GetAcl->f("owlupdate") . "', '" .  $GetAcl->f("owlcomment") . "', '" .  $GetAcl->f("owlcheckin") . "', '" .  $GetAcl->f("owlemail") . "', '" .  $GetAcl->f("owlrelsearch") . "', '" .  $GetAcl->f("owlsetacl") . "', '" .  $GetAcl->f("owlmonitor") . "')");
         }
         // INSERT Associated Data
 
         if (!$default->owl_use_fs)
         {
            $GetFileData->query("SELECT * from $default->owl_files_data_table where id = '" . $GetFiles->f("id") . "'");
            $GetFileData->next_record();
            $PutFileData->query("INSERT into $default->owl_files_data_table (id, data, compressed) values ('$newFile', '" . addslashes($GetFileData->f("data")) . "','" . $GetFileData->f("compressed") . "')");
         }
 
         // INSERT Thumbnails
         $id = $GetFiles->f("id");
         if (file_exists($default->thumbnails_location . "/" . $default->owl_current_db . "_" . $id . "_small.png"))
         {
            copy ($default->thumbnails_location . "/" . $default->owl_current_db . "_" . $id . "_small.png", $default->thumbnails_location . "/" . $default->owl_current_db .  "_" . $newFile . "_small.png");
            copy ($default->thumbnails_location . "/" . $default->owl_current_db . "_" . $id . "_med.png", $default->thumbnails_location . "/" . $default->owl_current_db .  "_" . $newFile . "_med.png");
            copy ($default->thumbnails_location . "/" . $default->owl_current_db . "_" . $id . "_large.png", $default->thumbnails_location . "/" . $default->owl_current_db .  "_" . $newFile . "_large.png");
         }

         // INSERT Associated Custom Fields
         $GetDoctype->query("SELECT * from $default->owl_docfieldvalues_table  where file_id ='" . $GetFiles->f("id") . "'");
         while ( $GetDoctype->next_record() )
         {
            $PutDoctype->query("INSERT into $default->owl_docfieldvalues_table (file_id, field_name, field_value) values ('$newFile', '" . $GetDoctype->f("field_name") . "' , '" . $GetDoctype->f("field_value") . "')");
         }

      }

      $GetFolders = new Owl_DB;
      $GetFolders->query("SELECT * from $default->owl_folders_table where parent ='" . $GetFolder->f("id") . "'");
      while($GetFolders->next_record())
      {
         fCopyFolder($GetFolders->f("id"), $newParent);
      }

   }
}
function array_sort($array, $key)
{
   if( is_array($array))
   {
      for ($i = 1; $i <= sizeof($array); $i++) {
          $sort_values[$i] = $array[$i][$key];
      }
      asort ($sort_values);
      reset ($sort_values);

      $i = 1;
      while (list ($arr_key, $arr_val) = each ($sort_values)) {
            $sorted_arr[$i] = $array[$arr_key];
            $i++;
      }

      return $sorted_arr;
   }
   else
   {
      return $array;
   }
}

function fGetUserInfoInMyGroups($uid, $condition = "", $primary_group = false, $sSettingACL = "", $iObjectID = "")
{
   global $default, $usergroupid, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   $i = 1;
   $UserGroupid =  $usergroupid;

   if (empty($condition))
   {
      $sWhere = "";
   }
   else
   {
      $sWhere = " and $condition ";
   }

   if(fIsAdmin() or fIsGroupAdmin($uid, $UserGroupid, $sSettingACL, $iObjectID))
   {
      $sql->query("SELECT id, language,attachfile, username,name ,email from $default->owl_users_table where 1=1 $sWhere");
      while ($sql->next_record())
      {
         $users[$i][username] = $sql->f("username");
         $users[$i][name] = $sql->f("name");
         $users[$i][email] = $sql->f("email");
         $users[$i][id] = $sql->f("id");
         $users[$i][language] = $sql->f("language");
         $users[$i][attachfile] = $sql->f("attachfile");
         $i++;
      }
      return array_sort($users, "name");
   }
   else
   {
      $sql->query("SELECT id, language,attachfile, username,name ,email from $default->owl_users_table WHERE groupid = '$UserGroupid' $sWhere");
      
      
      while ($sql->next_record())
      {
         $users[$i][username] = $sql->f("username");
         $users[$i][name] = $sql->f("name");
         $users[$i][email] = $sql->f("email");
         $users[$i][id] = $sql->f("id");
         $users[$i][language] = $sql->f("language");
         $users[$i][attachfile] = $sql->f("attachfile");
         $i++;
      }
   }

   if ($primary_group)
   {
      $sql->query("SELECT username,name ,email from $default->owl_users_table where groupid = '$UserGroupid' and disabled='0' $sWhere");
      while ($sql->next_record())
      {
         $bAddUser = true;
         if(!empty($users))
         {
            foreach ($users as $aUsers)
            {  
              $sId = $aUsers["id"];
              if($sId  == $sql->f("id"))
              {  
                 $bAddUser = false;
              }  
            }  
         }

         if($bAddUser)
         {
            $users[$i][username] = $sql->f("username");
            $users[$i][name] = $sql->f("name");
            $users[$i][email] = $sql->f("email");
            $users[$i][id] = $sql->f("id");
            $users[$i][language] = $sql->f("language");
            $users[$i][attachfile] = $sql->f("attachfile");
            $i++;
         }
      }
   }
   else
   {
   
      $membersql = $cCommonDBConnection;

      if (empty($membersql))
      {
         $membersql = new Owl_DB;
      }

      $membersql->query("SELECT userid,groupid from $default->owl_users_grpmem_table where userid = '$uid'");
       
      while ($membersql->next_record())
         
      {echo $loop;
         $CurrentGroupid = $membersql->f("groupid");
 	// modif misterphi
  	$sql = new Owl_DB;
  	//eom
         $sql->query("SELECT * from $default->owl_users_table where groupid = '$CurrentGroupid' and disabled='0' $sWhere");
         while ($sql->next_record())
         {
            $bAddUser = true;
            if(!empty($users))
            {
               foreach ($users as $aUsers)
               {  
                 $sId = $aUsers["id"];
                 if($sId  == $sql->f("id"))
                 {  
                    $bAddUser = false;
                 }  
               }  
            }

            if($bAddUser)
            {
               $users[$i][username] = $sql->f("username");
               $users[$i][name] = $sql->f("name");
               $users[$i][email] = $sql->f("email");
               $users[$i][id] = $sql->f("id");
               $users[$i][language] = $sql->f("language");
               $users[$i][attachfile] = $sql->f("attachfile");
               $i++;
            }
         }
      }
   } 
   return array_sort($users, "name");
}

function fGetGroups ($uid, $sSettingACL = "", $iObjectID = "")
{
   global $default, $usergroupid, $cCommonDBConnection;

   $sql = $cCommonDBConnection;



   $bUserAdmin = fIsUserAdmin($uid);

   if (fIsAdmin() or ($bUserAdmin and $sSettingACL == ""))
   {
      if (empty($sql))
      {
         $sql = new Owl_DB;
      }
      $groups[0][0] = $usergroupid;
      $groups[0][1] = group_to_name($usergroupid);

      $sql->query("SELECT id,name FROM $default->owl_groups_table WHERE id <> '$usergroupid' ORDER BY name");
      $i = 1;
      while ($sql->next_record())
      {
         if ($bUserAdmin and $sql->f("id") == 0)
         {
            continue;
         }
         $groups[$i][0] = $sql->f("id");
         $groups[$i][1] = $sql->f("name");
         $i++;
      }
   }
   elseif (fIsGroupAdmin($uid, $usergroupid, $sSettingACL, $iObjectID))
   {
      if (empty($sql))
      {
         $sql = new Owl_DB;
      }
      $sql->query("SELECT id,name from $default->owl_groups_table where id <> '0' and id <> '$default->file_admin_group' order by name");
      $i = 0;
      while ($sql->next_record())
      {
         //print("D: $default->file_admin_group I: " . $sql->f("id") . "<br />");
         $groups[$i][0] = $sql->f("id");
         $groups[$i][1] = $sql->f("name");
         $i++;
      }
   }
   else
   {
      $agroups = array();
      if (empty($sql))
      {
         $sql = new Owl_DB;
      }
      $sql->query("SELECT groupid from $default->owl_users_table where id = '$uid'");
      while ($sql->next_record())
      {
          $agroups[] = $sql->f("groupid");
          $maingroup = $sql->f("groupid");
      }

      $sql->query("SELECT groupid from $default->owl_users_grpmem_table where (groupid is not NULL AND userid = '$uid' and groupid <> '$maingroup')");

      $sGroupAdminQuery = "";
      while ($sql->next_record())
      {
         $agroups[] = $sql->f("groupid");
         $sGroupAdminQuery .= " groupadmin <> '" . $sql->f("groupid") . "' AND";
      }
      $sGroupAdminQuery .= " 1=1 ";

      $sql->query("SELECT groupadmin from $default->owl_users_grpmem_table where ($sGroupAdminQuery) AND userid = '$uid' and groupadmin is not NULL");
      while ($sql->next_record())
      {
         $agroups[] = $sql->f("groupadmin");
      }

      $sGroupAdminQuery = "";
      foreach ($agroups as $usergroup)
      {
         $sGroupAdminQuery .= " id = '" . $usergroup . "' OR";
      }
      $sGroupAdminQuery .= " 1=0 ";

      $sql->query("SELECT id, name from $default->owl_groups_table where ($sGroupAdminQuery) order by name;");
      //print("SELECT id, name from $default->owl_groups_table where ($sGroupAdminQuery) order by name;");
      $i = 0;
      while ($sql->next_record())
      {
         $groups[$i][0] = $sql->f("id");
         $groups[$i][1] = $sql->f("name");
         $i++;
      }
   }
   return $groups;
}

function fGetLastLogin()
{
   global $default, $userid, $cCommonDBConnection;

   $getlastlogin = $cCommonDBConnection;

   if (empty($getlastlogin))
   {
      $getlastlogin = new Owl_DB;
   }

   if ($default->anon_user <> $userid and !empty($userid))
   {     
      $getlastlogin->query("SELECT lastlogin FROM $default->owl_users_table where id = '" . $userid . "'");
      $getlastlogin->next_record();
      $lastlogin = $getlastlogin->f("lastlogin");
   }
   else
   {
      $lastlogin = ereg_replace("'", "", $getlastlogin->now());
   }     
   return $lastlogin;
}

function fCheckWithinHomeDir ( $currentparent )
{
   global $default, $parent, $bIsWithinHomeDir;

   $sql = new Owl_DB;
   $sql->query("select id,name,parent from $default->owl_folders_table where id='$currentparent' ");

   while ($sql->next_record())
   {
      if ($bIsWithinHomeDir)
      {
         break;
      }
      if ($sql->f("parent") == $default->HomeDir)
      {
         $bIsWithinHomeDir = true;
         break;
      }

      fCheckWithinHomeDir ($sql->f("parent"));
   }
}

function fGetFolderSize($iFolderId, $iFolderSize = 0, $getfiles = 0)
{
   global $default, $userid;

   if ($getfiles == 0)
   {
      $getfiles = new Owl_DB;
   }
   $getfolders = new Owl_DB;

   
   if ($default->restrict_view == 1)
   { 
      $getfiles->query("SELECT id, f_size from $default->owl_files_table where parent = '$iFolderId'");

      while ($getfiles->next_record())
      {
          $iFileId = $getfiles->f("id");
   
          if (check_auth($iFileId, "file_download", $userid, false, false) == 1)
          { 
             $iFolderSize += $getfiles->f("f_size");
          }
      }
   }
   else
   {
      $getfiles->query("SELECT sum(f_size) as fsize from $default->owl_files_table where parent = '$iFolderId'");
      $getfiles->next_record();
      
      if(!is_null($getfiles->f("fsize")))
      {
         $iFolderSize += $getfiles->f("fsize");
      }
   }

   $getfolders->query("SELECT id from $default->owl_folders_table where parent = '$iFolderId'");

   while ($getfolders->next_record())
   {
      $iFolderSize = fGetFolderSize($getfolders->f("id") , $iFolderSize, $getfiles);
   }

   return $iFolderSize;
}

function fGetBulkDownloadFiles($iFolderId)
{
   global $filelist, $default, $userid, $pdffilelist;

   $getfiles = new Owl_DB;

   // DONT DOWNLOAD URLS
   $getfiles->query("SELECT * from $default->owl_files_table where parent = '$iFolderId' and url <> '1'");

   while ($getfiles->next_record())
   {
       $iFileId = $getfiles->f("id");
       if (check_auth($iFileId, "file_download", $userid) == 1)
       {

            $path = fCreateWaterMark($iFileId);

            if (! $path === false)
            {
               $fspath = $path;
               $pdffilelist[] = $fspath;
            }
            else
            {
               $fspath = $default->owl_FileDir . "/" . get_dirpath(owlfileparent($iFileId)) . "/" .  flid_to_filename($iFileId);
               $filelist[] = $fspath;
            }
          //$filelist[] = $default->owl_FileDir . "/" . get_dirpath(owlfileparent($iFileId)) . "/" .  flid_to_filename($iFileId);
       }
   }

   $getfolders = new Owl_DB;
   $getfolders->query("SELECT * from $default->owl_folders_table where parent = '$iFolderId'");

   while ($getfolders->next_record())
   {
      fGetBulkDownloadFiles($getfolders->f("id"));
   }
}



function fGetPhysicalFileId ( $iFileId )
{
   global $default, $cCommonDBConnection;

   $getfiles = $cCommonDBConnection;

   if (empty($getfiles))
   {
      $getfiles = new Owl_DB;
   }

                                                                                                                                                                                                   
   $RealId = 0;

   $getfiles->query("SELECT linkedto from $default->owl_files_table where id = '$iFileId'");
 
   while ($getfiles->next_record())
   {
      $RealId = $getfiles->f("linkedto");
   }

   if(empty($RealId) or $RealId == 0)
   {
      $RealId = $iFileId;
   }

   return $RealId;
}


function fCleanDomTTContent ($sDescription , $bDomPopup = 1)
{
   $sReturnDesc = ereg_replace("\n", "%OWLNEWLIINE%", $sDescription);
   $sReturnDesc = ereg_replace("\"", "'", $sReturnDesc);
   $sReturnDesc = ereg_replace("'", "\\'", $sReturnDesc);
   $sReturnDesc = ereg_replace("\r", '', $sReturnDesc);
   $sReturnDesc = ereg_replace("\r", '', $sReturnDesc);
   if ($bDomPopup == 1)
   {
      //$sReturnDesc = ereg_replace("<","&amp;lt;",$sReturnDesc);
      $sReturnDesc = ereg_replace("%OWLNEWLIINE%", "<br />", $sReturnDesc);
   }
   else
   {
      $sReturnDesc = ereg_replace("%OWLNEWLIINE%", "<br />", $sReturnDesc);
      $sReturnDesc = htmlentities($sReturnDesc);
   }

   return $sReturnDesc;
}                                                                                                                                                                                                   
/* function ldap_authenticate($u, $p)
{
   global $default; 

   // Generate a DN from a uid
   $dn = "$default->ldapuserattr=$u, " . $default->ldapserverroot;

   // Connect to ldap server
   $dsCon = ldap_connect($default->ldapserver);

   // Make sure we connected
   if (!($dsCon))
   {
      printError("Sorry, cannot contact LDAP server");
      return(1);
   }

   // Attempt to bind, if it works, the password is acceptable
   ldap_set_option($dsCon, LDAP_OPT_PROTOCOL_VERSION, $default->ldapprotocolversion);
   $bind = ldap_bind($dsCon, $dn, $p);
   if(!($bind))
   {
      return(1);
   }
   else
   {
      // If we got here, the username/password worked.
      ldap_close($dsCon);
      return (0);
   }
}
*/

function ldap_authenticate($usr, $pswd)
{
   global $default; 

   if(empty($usr) or empty($pswd))
   {
      return(1);
   }

   if(empty($default->owl_current_db))
   {
      $db = $default->owl_default_db;
   }
   else
   {
      $db = $default->owl_current_db;
   }

   //list ($user, $domain) = split('@', $u);
   
   $domain = $default->owl_db_ldapdomain["$db"];

   if (!($connect=ldap_connect($default->owl_db_ldapserver["$db"]))) {
    return(1);
     //die("Could not connect to a domain controller");
   }
   ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, $default->ldapprotocolversion);
   ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);

   // bind to server
   if (!($bind=@ldap_bind($connect,"$usr@$domain","$pswd"))) {
      return(1);
   }
   else
   {
      ldap_close($connect);
      return (0);
   }
}


function fGetLocales()
{ 
   global $default;
   $aLanguages = array();

   $dir = dir($default->owl_LangDir);
   if (empty($dir))
   {
      $aLanguages[] = "ERROR: CANT READ LOCALES";
      return $aLanguages;
   }

   $dir->rewind();


   while ($file = $dir->read())
   {
      if ($file != "." and $file != ".." and $file != "CVS" and $file != "favicon.ico")
      {
         $aLanguages[] = $file;
      }
   }
   $dir->close();
   $aLanguages = iSort($aLanguages);
   reset($aLanguages);
   return $aLanguages;
}


function iSort($sortarr) {
   $bakarr = $sortarr;

   for($arrnum = 0; $arrnum < Count($sortarr); $arrnum++) {
       $sortarr[$arrnum] = strtolower($sortarr[$arrnum])."|".$arrnum;
   }

   sort($sortarr);
   reset($sortarr);

   for($arrnum = 0; $arrnum < Count($sortarr); $arrnum++) {
       $temp = explode("|", $sortarr[$arrnum]);
       $newarr[] = $bakarr[$temp[Count($temp)-1]];
   }

   return $newarr;
}

function fCleanupUserInput ( $UserInputField )
{
   return ereg_replace("[\]","/",stripslashes($UserInputField));
}

function fIntializeCheckBox ( $iCheckboxItem )
{
   if (!empty($iCheckboxItem))
   {
      return $iCheckboxItem;
   }
   else
   {
      return "0";
   }
}

function fbCheckForPasswdReuse ( $sPassword , $uid )
{
   global $default;

   $bReused = false;
   $sql = new Owl_DB;
   $sql->query("SELECT * FROM $default->owl_trackpasswd_table WHERE userid='$uid' and password='" . md5($sPassword) . "'");
   if ($sql->num_rows() == 0)
   {
      $sql->query("INSERT INTO $default->owl_trackpasswd_table (userid, password) VALUES ('$uid', '" . md5($sPassword) . "')");
      $sql->query("SELECT * FROM $default->owl_trackpasswd_table WHERE userid='$uid'");
      if ($sql->num_rows() > $default->track_user_passwords)
      {
         $qCleanup = new Owl_DB;
         $sql->query("SELECT * FROM $default->owl_trackpasswd_table WHERE userid='$uid' ORDER BY id DESC");
         $iCountPassRecords = 0;
         while ($sql->next_record())
         {
            $iCountPassRecords++;
            if ( $iCountPassRecords > $default->track_user_passwords)
            {
               $qCleanup->query("DELETE FROM $default->owl_trackpasswd_table WHERE id='" . $sql->f("id") . "'");
            }
         }
      }
   }
   else
   {
      $bReused = true;
   }
   return $bReused;
}

function fbValidUsername( $sUsername )
{
   global $default;
   $iStrLen = strlen($sUsername);
   $bValid = true;
   if ($iStrLen < $default->min_username_length)
   {
      $bValid = false;
   }
   return $bValid;
}

function fbValidPassword( $sPassword )
{
   global $default;
   $iStrLen = strlen($sPassword);
   $bValid = true;
                                                                                                                                       
   if ($iStrLen < $default->min_pass_length)
   {
      $bValid = false;
   }
                                                                                                                                       
   $iNumOfAlpha = 0;
   $iNumOfNum = 0;
   $iNumOfSpec = 0;
                                                                                                                                       
   for ( $c = 0; $c < $iStrLen; $c++)
   {
      $cLetter = substr($sPassword, $c, 1);
      if (preg_match("/[0-9]/", $cLetter))
      {
         $iNumOfNum++;
      }
      if (preg_match("/[\\\~\!\.\/\,\"\=\-\@\#\$\%\^\&\*\(\)\_\+\{\}\|\[\]\;\'\?\>\<\:]/", $cLetter))
      {
         $iNumOfSpec++;
      }
   }
   if ($iNumOfNum < $default->min_pass_numeric or $iNumOfSpec < $default->min_pass_special)
   {
      $bValid = false;
   }
   return $bValid;
}


/**
 * CODE FROM: php.net
 *
 * Note Contributed by: mariano at cricava dot com
 * http://ca.php.net/manual/en/function.mktime.php
 *
 * Calculates the difference for two given dates, and returns the result
 * in specified unit.
 *
 * @param string    Initial date (format: [dd-mm-YYYY hh:mm:ss], hh is in 24hrs format)
 * @param string    Last date (format: [dd-mm-YYYY hh:mm:ss], hh is in 24hrs format)
 * @param char    'd' to obtain results as days, 'h' for hours, 'm' for minutes, 's' for seconds, and 'a' to get an indexed array of days, hours, minutes, and seconds
 *
 * @return mixed    The result in the unit specified (float for all cases, except when unit='a', in which case an indexed array), or null if it could not be obtained
 *
 * USAGE:

$dateFrom = "25-03-2005 14:20:00";
$dateTo = date("d-m-Y H:i:s", strtotime('now'));


$diffd = getDateDifference($dateFrom, $dateTo, 'd');
$diffh = getDateDifference($dateFrom, $dateTo, 'h');
$diffm = getDateDifference($dateFrom, $dateTo, 'm');
$diffs = getDateDifference($dateFrom, $dateTo, 's');
$diffa = getDateDifference($dateFrom, $dateTo, 'a');

echo 'Calculating difference between ' . $dateFrom . ' and ' . $dateTo . ' <br /><br />';

echo $diffd . ' days.<br />';
echo $diffh . ' hours.<br />';
echo $diffm . ' minutes.<br />';
echo $diffs . ' seconds.<br />';

echo '<br />In other words, the difference is ' . $diffa['days'] . ' days, ' . $diffa['hours'] . ' hours, ' . $diffa['minutes'] . ' minutes and ' . $diffa['seconds'] . ' seconds.<br>';

 */
function getDateDifference($dateFrom, $dateTo, $unit = 'd')
{
   $difference = null;

   $dateFromElements = split(' ', $dateFrom);
   $dateToElements = split(' ', $dateTo);

   $dateFromDateElements = split('-', $dateFromElements[0]);
   $dateFromTimeElements = split(':', $dateFromElements[1]);
   $dateToDateElements = split('-', $dateToElements[0]);
   $dateToTimeElements = split(':', $dateToElements[1]);

   // Get unix timestamp for both dates
   for ( $i = 0; $i < 3; $i++ )
   {
   if ( $dateFromTimeElements[$i] == "")
         $dateFromTimeElements[$i] = 0;
   if ( $dateToTimeElements[$i] == "")
         $dateToTimeElements[$i]  = 0;
   }

   $date1 = mktime($dateFromTimeElements[0], $dateFromTimeElements[1], $dateFromTimeElements[2], $dateFromDateElements[1], $dateFromDateElements[0], $dateFromDateElements[2]);
   $date2 = mktime($dateToTimeElements[0], $dateToTimeElements[1], $dateToTimeElements[2], $dateToDateElements[1], $dateToDateElements[0], $dateToDateElements[2]);

   if( $date1 > $date2 )
   {
       return null;
   }

   $diff = $date2 - $date1;

   $days = 0;
   $hours = 0;
   $minutes = 0;
   $seconds = 0;

   if ($diff % 86400 <= 0)  // there are 86,400 seconds in a day
   {
       $days = $diff / 86400;
   }

   if($diff % 86400 > 0)
   {
       $rest = ($diff % 86400);
       $days = ($diff - $rest) / 86400;

       if( $rest % 3600 > 0 )
       {
           $rest1 = ($rest % 3600);
           $hours = ($rest - $rest1) / 3600;

           if( $rest1 % 60 > 0 )
           {
               $rest2 = ($rest1 % 60);
               $minutes = ($rest1 - $rest2) / 60;
               $seconds = $rest2;
           }
           else
           {
               $minutes = $rest1 / 60;
           }
       }
       else
       {
           $hours = $rest / 3600;
       }
   }

   switch($unit)
   {
       case 'd':
       case 'D':

           $partialDays = 0;

           $partialDays += ($seconds / 86400);
           $partialDays += ($minutes / 1440);
           $partialDays += ($hours / 24);

           $difference = $days + $partialDays;

           break;

       case 'h':
       case 'H':

           $partialHours = 0;

           $partialHours += ($seconds / 3600);
           $partialHours += ($minutes / 60);

           $difference = $hours + ($days * 24) + $partialHours;

           break;

       case 'm':
       case 'M':

           $partialMinutes = 0;

           $partialMinutes += ($seconds / 60);

           $difference = $minutes + ($days * 1440) + ($hours * 60) + $partialMinutes;

           break;

       case 's':
       case 'S':

           $difference = $seconds + ($days * 86400) + ($hours * 3600) + ($minutes * 60);

           break;

       case 'a':
       case 'A':

           $difference = array (
               "days" => $days,
               "hours" => $hours,
               "minutes" => $minutes,
               "seconds" => $seconds
           );

           break;
   }

   return $difference;
}

function fGetHostByAddress($ip='') 
{ 
   if ($ip=='') 
   {
      $ip = $_SERVER['REMOTE_ADDR'];
   } 
   $longisp = @gethostbyaddr($ip); 
   $isp = explode('.', $longisp); 
 
   if (preg_match("/[0-9]{1,3}\.[0-9]{1,3}/", $longisp)) 
   {
      return 'ISP lookup failed.'; 
   }
 
   return $longisp; 
} 

require_once ($default->owl_fs_root . "/scripts/fpdf/fpdf.php");

class Owl_PDF extends FPDF
{

   //Page header
   var $sFpdfTitle = "";
   var $sFpdfDocName = "";
   var $sFpdfDocLocation = "";

   function Header()
   {
      //Logo
      //Arial bold 15
      $this->SetFont('Arial','B',15);
      $this->SetTextColor(0,0,0);
      //Move to the right
      $this->Cell(80);
      //Title
      $this->Cell(30,10,$this->sFpdfTitle,0,0,'C');
      //Line break
      $this->Ln(20);
      }
 
   //Page footer
   function Footer()
   {
      global $default;
      //Position at 1.5 cm from bottom
      $this->SetY(-15);
      //Arial italic 8
      $this->SetFont('Arial','',8);
      $this->SetTextColor(0,0,0);
      //Page number
      $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'C');
      $this->SetY(-12);
      $this->SetX(-55);
      $this->SetFontSize(6);
      $this->Cell(0,10,"Name: " . $this->sFpdfDocName,0,1);
      $this->SetY(-10);
      $this->SetX(-57);
      if (!empty($this->sFpdfDocLocation))
      {
         $this->Cell(0,10,"Location: " .$this->sFpdfDocLocation,0,1);
      }
      else
      {
         $this->Cell(0,10,"",0,1);
      }
      if ($this->DefOrientation == "L")
      {
         $this->Image("$default->owl_fs_root/graphics/$default->sButtonStyle/ui_misc/owl_pdf.png",8,197,20,0,'','http://owl.sourceforge.net');
      }
      else
      {
         $this->Image("$default->owl_fs_root/graphics/$default->sButtonStyle/ui_misc/owl_pdf.png",8,280,26,0,'','http://owl.sourceforge.net');
      }
      $this->SetY(-8);
      $this->SetX(8);
      $this->PutLink("http://owl.sourceforge.net", "http://owl.sourceforge.net");
   }

   function SetStyle($tag,$enable)
   {
      //Modify style and select corresponding font
      $this->$tag+=($enable ? 1 : -1);
      $style='';
      foreach(array('B','I','U') as $s)
      {
         if($this->$s>0)
         {
            $style.=$s;
         }
      }
      $this->SetFont('',$style);
   }


   function PutLink($URL,$txt)
   {
      //Put a hyperlink
      $this->SetTextColor(0,0,255);
      $this->SetStyle('U',true);
      $this->Write(5,$txt,$URL);
      $this->SetStyle('U',false);
      $this->SetTextColor(0);
   }
}

function fGeneratePdfFile($iFileId)
{
   global $default, $parent, $owl_lang;

   $sLocation = find_path($parent);
   $sName = flid_to_filename($iFileId);

   $path = $default->owl_FileDir . "/" . $sLocation . "/" . $sName;

   $txt = file_get_contents("$path");

   $aFirstpExtension = fFindFileFirstpartExtension ($sName);
   $sFirstPart = $aFirstpExtension[0];


   $sql = new Owl_DB;
   $sql->query("SELECT metadata, name from $default->owl_files_table where id = '$iFileId'");
   $sql->next_record();

   $pdf=new Owl_PDF();
   $pdf->SetTitle($sql->f("name"));
   $pdf->SetAuthor(fid_to_creator($iFileId));
   $pdf->SetCreator($default->version);
   $pdf->SetKeywords($sql->f("metadata"));
   $pdf->sFpdfTitle = $sql->f("name");
   $pdf->sFpdfDocName = $sName;
   $pdf->sFpdfDocLocation = $sLocation;
   $pdf->AliasNbPages();
   $pdf->AddPage();
   $pdf->SetFont('Times','',12);
   $pdf->MultiCell(0,5,$txt);
   $pdf->Ln();
   $pdf->Output($sFirstPart . ".pdf", 'D');
}

function fWindowsMoveFolders($source, $dest)
{
   if (file_exists($source))
   {
      if($hDocDir = opendir($source))
      {
         if(!file_exists($dest))
         {
            mkdir($dest, $default->directory_mask);
         }
         while ($file = readdir($hDocDir))
         {
            if ($file[0] == '.')
            {
               continue;
            }
            if (!is_file("$source/$file"))
            {
               fWindowsMoveFolders($source. "/". $file, $dest . "/" . $file);
            }
            else
            {
               rename("$source/$file", "$dest/$file");
            }
         }
      }
   }
   else
   {
      if ($default->debug == true)
      {
         printError("DEBUG: File does not exist: $source");
      }
   }
}

function return_bytes($val) {
   $val = trim($val);
   $last = strtolower($val{strlen($val)-1});
   switch($last) {
       // The 'G' modifier is available since PHP 5.1.0
       case 'g':
           $val *= 1024;
       case 'm':
           $val *= 1024;
       case 'k':
           $val *= 1024;
   }

   return $val;
}

function fGetMailBodyText($iTypeFlag, $iFileId = -1, $tempsess = 0, $type = "0")
{
   global $default, $language, $owl_lang, $userid;
   $aBody = array();

   $sql = new Owl_DB; 

   $sFileCreated = ""; 
   $sFileModified = "";

   if($iFileId > 0)
   {
      $sql->query("SELECT * from $default->owl_files_table where id='$iFileId'");
      $sql->next_record();

      $sFileCreated = $sql->f("created"); 
      $sFileModified = $sql->f("smodified");


      if(!empty($sFileCreated))
      {
         $sFileCreated = date($owl_lang->localized_date_format, strtotime($sql->f("created")) + $default->time_offset);
      }
      
      if (!empty($sFileModified))
      {
         $sFileModified = date($owl_lang->localized_date_format, strtotime($sql->f("smodified")) + $default->time_offset);
      }

      if ($type == "url")
      {
         $link = $sql->f('filename');
         $path = find_path($sql->f('parent')) . "/" . $sql->f('name');
      }
      else
      {
         $path = find_path($sql->f('parent')) . "/" . $sql->f('filename');
         $link = $default->owl_notify_link . "browse.php?sess=$tempsess&parent=" .$sql->f('parent') . "&expand=1&fileid=$iFileId";
      }
   }
 
   switch ($iTypeFlag)
   {
      case NEW_FILE:
       $aBody['SUBJECT'] =  $owl_lang->notif_subject_new;
       $sFile = "new_document";
       break;
      case UPDATED_FILE:
       $aBody['SUBJECT'] =  $owl_lang->notif_subject_upd;
       $sFile = "upd_document";
       break;
      case NEW_COMMENT:
       $aBody['SUBJECT'] =  $owl_lang->notif_subject_comment;
       $sFile = "new_comment";
       break;
      case DELETED_FILE:
       $aBody['SUBJECT'] =  $owl_lang->notif_subject_file_deleted;
       $sFile = "del_document";
       break;
      case NEW_PASSWORD:
       $aBody['SUBJECT'] =  $owl_lang->self_reg_subj;
       $sFile = "forgot_password";
       break;
      case NEW_APPROVED:
       $aBody['SUBJECT'] =  $owl_lang->peer_subj_review;
       $sFile = "new_review";
       break;
      case APPROVED:
       $aBody['SUBJECT'] =   $owl_lang->peer_subj_approved;
       $sFile = "approved_review";
       break;
      case REMINDER_APPROVED:
       $aBody['SUBJECT'] =   $owl_lang->peer_subj_reminder;
       $sFile = "new_review";
       break;
      case FINAL_APPROVED:
       $aBody['SUBJECT'] =   $owl_lang->peer_subj_approved;
       $sFile = "final_review";
       break;
      case FINAL_AUTO_APPROVED:
       $aBody['SUBJECT'] =   $owl_lang->peer_subj_approved;
       $sFile = "final_auto_review";
       break;
      case REJECT_APPROVED:
       $aBody['SUBJECT'] =   $owl_lang->peer_subj_rejected;
       $sFile = "reject_review";
       break;
      case ADMIN_PASSWORD:
       $aBody['SUBJECT'] =   $owl_lang->notif_subj_new_account;
       $sFile = "new_account";
       break;
      case SELF_REG_USER:
       $aBody['SUBJECT'] =   $owl_lang->self_reg_subj;
       $sFile = "self_reg";
       break;
   }

   $sHtmlFilePath = "$default->owl_fs_root/locale/$language/emailtemplates/$sFile.html";
   $sTxtFilePath = "$default->owl_fs_root/locale/$language/emailtemplates/$sFile.txt";

   $handle = @fopen($sTxtFilePath, "r");

   $sBody = "";
   if ($handle) 
   {
      while (!feof($handle)) 
      {
          $buffer = fgets($handle, 4096);
          $cUnixComment = $buffer[0];
          $cHtmlCommentStart = $buffer[0] . $buffer[1] . $buffer[2] . $buffer[3] ;
          $cHtmlCommentEnd = $buffer[0] . $buffer[1] . $buffer[2];
   
          if($cUnixComment != '#' and $cHtmlCommentStart != "<!--" and $cHtmlCommentEnd != "-->")
          {
             $buffer = ereg_replace("\%FILE_TITLE\%", $sql->f('name'), $buffer);
             $buffer = ereg_replace("\%FILE_NAME\%", $sql->f('filename'), $buffer);
             $buffer = ereg_replace("\%OWL_FILE_PATH\%", $path, $buffer);
             $buffer = ereg_replace("\%FILE_SIZE\%",  $sql->f('f_size'), $buffer);
             $buffer = ereg_replace("\%FILE_OWNER\%",  uid_to_name($sql->f('creatorid')), $buffer);
             $buffer = ereg_replace("\%FILE_UPDATOR\%", uid_to_name($sql->f("updatorid")), $buffer);
             $buffer = ereg_replace("\%FILE_CREATION_DATE\%",  $sFileCreated, $buffer);
             $buffer = ereg_replace("\%FILE_DESC\%", $sql->f("description"), $buffer);
             $buffer = ereg_replace("\%FILE_MODIFICATION_DATE\%", $sFileModified, $buffer);
             $buffer = ereg_replace("\%FILE_VERSION\%", $sql->f("major_revision") . "." . $sql->f("minor_revision"), $buffer);
             $buffer = ereg_replace("\%FILE_URL\%", $link, $buffer);
             $buffer = ereg_replace("\%CURRENT_USER\%", uid_to_name($userid), $buffer);
             $sBody .= $buffer;
          }
      }
      fclose($handle);
   }
   
   $aBody['TXT'] = $sBody;
   $sBody = "";
   
   $handle = @fopen($sHtmlFilePath, "r");
   
   if ($handle) 
   {
      while (!feof($handle)) 
      {
          $buffer = fgets($handle, 4096);
          $cUnixComment = $buffer[0];
          $cHtmlCommentStart = $buffer[0] . $buffer[1] . $buffer[2] . $buffer[3] ;
          $cHtmlCommentEnd = $buffer[0] . $buffer[1] . $buffer[2];
   
          if($cUnixComment != '#' and $cHtmlCommentStart != "<!--" and $cHtmlCommentEnd != "-->")
          {
             $buffer = ereg_replace("\%FILE_TITLE\%", $sql->f('name'), $buffer);
             $buffer = ereg_replace("\%FILE_NAME\%", $sql->f('filename'), $buffer);
             $buffer = ereg_replace("\%OWL_FILE_PATH\%", $path, $buffer);
             $buffer = ereg_replace("\%FILE_SIZE\%",  $sql->f('f_size'), $buffer);
             $buffer = ereg_replace("\%FILE_OWNER\%",  uid_to_name($sql->f('creatorid')), $buffer);
             $buffer = ereg_replace("\%FILE_UPDATOR\%", uid_to_name($sql->f("updatorid")), $buffer);
             $buffer = ereg_replace("\%FILE_CREATION_DATE\%", $sFileCreated , $buffer);
             $buffer = ereg_replace("\%FILE_DESC\%", $sql->f("description"), $buffer);
             $buffer = ereg_replace("\%FILE_MODIFICATION_DATE\%", $sFileModified , $buffer);
             $buffer = ereg_replace("\%FILE_VERSION\%", $sql->f("major_revision") . "." . $sql->f("minor_revision"), $buffer);
             $buffer = ereg_replace("\%FILE_URL\%", "<a href=\"" . $link . "\">" . $sql->f('filename') . "</a>", $buffer);
             $buffer = ereg_replace("\%LANG_CODE\%", $owl_lang->lang_code, $buffer);
             $buffer = ereg_replace("\%LANG_CHARSET\%", $default->charset, $buffer);
             $buffer = ereg_replace("\%TITLE\%", $default->site_title . " " . $default->version , $buffer);
             $buffer = ereg_replace("\%CURRENT_USER\%", uid_to_name($userid), $buffer);
             $sBody .= $buffer;
          }
      }
      fclose($handle);
   }
   $aBody['HTML'] = $sBody;
   return $aBody;
}

function fDebugDisplayArray($aArray)
{
   if(empty($aArray))
   {
      print("<pre>");
      print("ARRAY IS EMPTY");
      print("</pre>");
   }
   else
   {
      print("<pre>");
      print_r($aArray);
      print("</pre>");
   }
}

// CODE Contributed By: wmswms

function get_parents_sql($parent, $filename) 
{ 
   global $default, $userid; 
   $sql = new Owl_DB; 
 
   // list of folder id's from $parent up to id=1 (Documents) 
   $folders = array(); 
 
   // get file id and folder (parent) id 
   $sql->query("SELECT f.id, name, description, parent, filename from $default->owl_files_table f where f.filename = '$filename' and f.parent = '$parent'"); 
 
   $sql->next_record(); 
   $next_parent = $sql->f("parent"); 
 
   // walk up folder hierarchy 
   while ($next_parent != 1) 
   { 
      $folders[] = $next_parent; 
      $sql->query("SELECT parent from $default->owl_folders_table where id='$next_parent'"); 
      $sql->next_record(); 
      $next_parent = $sql->f("parent"); 
   } 

   // for Documents root folder 
   $folders[] = 1; 

   // convert to comma-sep list for SQL 
   $folders = implode(',', $folders); 
    
   return "SELECT f.id, fid, name, description, parent, userid, filename from $default->owl_files_table f, $default->owl_monitored_folder_table m where f.filename = '$filename' and f.parent = '$parent' and m.fid IN ($folders)"; 
 
} 


function fMoveBackupVersions ($fname, $source, $dest, $parent)
{
   global $default, $newFolder;
   $sql = new Owl_DB;

   $aFirstpExtension = fFindFileFirstpartExtension ($fname);
   $firstpart = $aFirstpExtension[0];
   $file_extension = $aFirstpExtension[1];

   // Get the id of the backup folder for the SOURCE Directory
   if ($default->owl_use_fs)
   {
      $sql->query("SELECT id FROM $default->owl_folders_table WHERE name='$default->version_control_backup_dir_name' and parent='$parent'");
      if ($sql->num_rows($sql) != 0)
      {
         while ($sql->next_record())
         {
            $backup_parent = $sql->f("id");
         }

         // Get the id of the backup folder for the Destination Directory
         // If it doesn't exists create one

         $sql->query("SELECT id FROM $default->owl_folders_table WHERE name='$default->version_control_backup_dir_name' and parent='$newFolder'");
         if ($sql->num_rows($sql) != 0)
         {
            while ($sql->next_record())
            {
               $BackupFolderId = $sql->f("id");
            }
         }
         else
         {
            $sDestinationBackupDir = "$default->owl_FileDir/$dest/$default->version_control_backup_dir_name";
            if (!is_dir($sDestinationBackupDir))
            {
               mkdir($sDestinationBackupDir, $default->directory_mask);
               if (is_dir($sDestinationBackupDir))
               {
                  $sql->query("INSERT INTO $default->owl_folders_table (name, parent, security, groupid, creatorid, description)  values ('$default->version_control_backup_dir_name', '$newFolder', '" . fCurFolderSecurity($newFolder) ."', '" . owlfoldergroup($newFolder) ."', '" . owlfoldercreator($newFolder) . "', '')");
                  $BackupFolderId = $sql->insert_id($default->owl_folders_table, 'id');

               }
               else
               {
                  printError("$owl_lang->err_backup_folder_create");
               }
            }
         }
         // Move the Backup files to the Destination backup Directory
         $qMoveBackupFiles = new Owl_DB;
         $sql->query("SELECT * FROM $default->owl_files_table WHERE (filename LIKE '" . $firstpart . "\\\_%" . $file_extension . "' AND parent = '$backup_parent') OR (filename = '$filename' AND parent = '$parent') ORDER BY major_revision desc, minor_revision desc");
         while ($sql->next_record())
         {
            $major_revision = $sql->f("major_revision");
            $minor_revision = $sql->f("minor_revision");
            if ($filename == $firstpart.'_'.$major_revision.'-'.$minor_revision.".".$file_extension)
            {
               $qMoveBackupFiles->query("UPDATE $default->owl_files_table SET parent='$BackupFolderId' WHERE id='" . $sql->f("id") ."'");
               rename("$default->owl_FileDir/$source" . "$default->version_control_backup_dir_name/" . $sql->f("filename") , "$default->owl_FileDir/$dest" . "$default->version_control_backup_dir_name/". $sql->f("filename"));
            }
         }
      }
   }
}


class RPDF extends FPDF {


   function Header()
   {
      global $userid, $owl_lang;
      //Logo
      //Arial bold 15
      $this->SetFont('Arial','B',10);
      $this->SetTextColor(255,192,203);
      //Move to the right
      $this->Cell(80);
      //Title
      $this->Cell(30,10,"$owl_lang->watermark_string - (" . uid_to_name($userid) . " " . date("F j, Y, g:i a") . ")",0,0,'C');
      //Line break
      $this->Ln(20);
      }

   function TextWithDirection($x,$y,$txt,$direction='R')
   {
       $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
       if ($direction=='R')
           $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$txt);
       elseif ($direction=='L')
           $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$txt);
       elseif ($direction=='U')
           $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
       elseif ($direction=='D')
           $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
       else
           $s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$txt);
       if ($this->ColorFlag)
           $s='q '.$this->TextColor.' '.$s.' Q';
       $this->_out($s);
   }

   function TextWithRotation($x,$y,$txt,$txt_angle,$font_angle=0)
   {
       $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));

       $font_angle+=90+$txt_angle;
       $txt_angle*=M_PI/180;
       $font_angle*=M_PI/180;

       $txt_dx=cos($txt_angle);
       $txt_dy=sin($txt_angle);
       $font_dx=cos($font_angle);
       $font_dy=sin($font_angle);

       $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
                $txt_dx,$txt_dy,$font_dx,$font_dy,
                $x*$this->k,($this->h-$y)*$this->k,$txt);
       if ($this->ColorFlag)
           $s='q '.$this->TextColor.' '.$s.' Q';
       $this->_out($s);
   }
}

function fisUserWaterMark()
{
   global $default, $userid;

   // THIS CODE IS LEFT HERE BECAUSE THE NEXT VERSION 
   // WILL Allow to set watermark on PDF based on USER 

   //$sql = new Owl_DB;
   //$sql->query("SELECT water_mark from $default->owl_users_table where id = '$userid'");
   //while ($sql->next_record())
   //{
      //return fIntializeCheckBox($sql->f('water_mark'));
   //}

   if (file_exists($default->pdf_watermark_path))
   { 
      return 1;
   }
   else
   {
      return 0;
   }
}

function fCreateWaterMark($fid)
{
   global $default, $userid, $sess, $owl_lang;

   $filename = fid_to_filename($fid);
   $sFileExtension = fFindFileExtension($filename);

   if ($sFileExtension == "pdf" and fisUserWaterMark() == 1)
   {
      $iStartParent = owlfileparent($fid);
  

      $tmpDir = $default->owl_tmpdir . "/owltmp.$sess";

      if (!file_exists($tmpDir))
      {
         mkdir($tmpDir,$default->directory_mask);
      }


      $sTmpDirPath = $tmpDir;
      if ($default->owl_use_fs)
      {
         $sFilePath = find_path($iStartParent);
         $path = $default->owl_FileDir . "/" . $sFilePath . "/" . $filename;

         $aFilePath = explode( "/" , $sFilePath );

         foreach ($aFilePath as $sDirectory)
         {
            
            $sTmpDirPath .= "/" . $sDirectory;
            if (!file_exists($sTmpDirPath))
            {
               mkdir($sTmpDirPath, $default->directory_mask);
            }
         }
         
         copy($path , $sTmpDirPath . "/" . $filename);
      }
      else
      {
         $path = fGetFileFromDatbase($fid);
      }

      if ($default->pdf_pdftk_tool_greater_than_1_40 == "1")
      {
         $sPdftkAction = "stamp";
      }
      else
      {
         $sPdftkAction = "background";
      }
      if (file_exists($default->pdf_custom_watermark_filepath))
      {
         if (substr(php_uname(), 0, 7) != "Windows")
         {
            exec($default->pdf_watermark_path . " '" . $path . "' $sPdftkAction '" . $default->pdf_custom_watermark_filepath . "' output '" . $sTmpDirPath . "/stamp_" . $filename . "'", $output);
         }
         else
         {
            exec($default->pdf_watermark_path . " \"" . $path . "\" $sPdftkAction \"" . $default->pdf_custom_watermark_filepath . "\" output \"" . $sTmpDirPath . "/stamp_" . $filename . "\" dont_ask", $output);
            //exec($default->pdf_watermark_path . " " . $path . " $sPdftkAction " . $default->pdf_custom_watermark_filepath . " output " . $sTmpDirPath . "/stamp_" . $filename, $output);
         }
      }
      else
      { 
         $pdf=new RPDF();
         $pdf->Open();
         $pdf->AddPage();
         $pdf->SetFont('Arial','',40);
         $pdf->SetTextColor(255,192,203);
         $pdf->TextWithRotation(50,145, $owl_lang->watermark_string ,45,-45);
         $pdf->TextWithRotation(50,180, uid_to_name($userid) ,45,-45);
         $pdf->TextWithRotation(50,210,$owl_lang->watermark_source ,45,-45);
         $pdf->TextWithRotation(50,240,date("F j, Y, g:i a") ,45,-45);
         $pdf->Output($tmpDir . "/water.pdf");
         if (substr(php_uname(), 0, 7) != "Windows")
         {
            exec($default->pdf_watermark_path . " '" . $path . "' $sPdftkAction '" . $tmpDir ."/water.pdf' output '" . $sTmpDirPath . "/stamp_" . $filename . "'", $output);
         }
         else
         {
            //exec($default->pdf_watermark_path . " " . $path . " $sPdftkAction " . $tmpDir ."/water.pdf output " . $sTmpDirPath . "/stamp_" . $filename, $output);
            exec($default->pdf_watermark_path . " \"" . $path . "\" $sPdftkAction \"" . $tmpDir ."/water.pdf\" output \"" . $sTmpDirPath . "/stamp_" . $filename . "\" dont_ask", $output);
         }
      }
      //exit($default->pdf_watermark_path . " '" . $path . "' $sPdftkAction '" . $tmpDir ."/water.pdf' output '" . $tmpDir . "/stamp_" . $filename . "'");
      return ( $sTmpDirPath . "/stamp_" . $filename);
   }
   return false;
}

function fGetFileFromDatbase($id)
{
   global $sess, $default;

   $uncomptmpfile = $default->owl_tmpdir . "/owltmp.$sess/"  . flid_to_filename($id);

   $tmpDir = $default->owl_tmpdir . "/owltmp.$sess";

   if (file_exists($tmpDir))
   {
      myDelete($tmpDir);
   }

   mkdir($tmpDir,$default->directory_mask);

   $getfile = new Owl_DB;
   $getfile->query("SELECT data,compressed FROM $default->owl_files_data_table WHERE id='$id'");

   while ($getfile->next_record())
   {
      if ($getfile->f("compressed"))
      {
         $tmpfile = $uncomptmpfile . ".gz";
         if ($default->debug)
         {
            if (!file_exists($default->owl_tmpdir))
            {
               printError("$owl_lang->debug_tmp_not_exists");
            }
            if (!is_writable($default->owl_tmpdir))
            {
               printError("$owl_lang->debug_tmp_not_writeable");
            }
         }
         if (file_exists($tmpfile)) unlink($tmpfile);

         $fp = fopen($tmpfile, "wb");
         fwrite($fp, $getfile->f("data"));
         fclose($fp);

         system($default->gzip_path . " -df $tmpfile");

         $fsize = filesize($uncomptmpfile);
         $fd = fopen($uncomptmpfile, 'rb');
         $filedata = fread($fd, $fsize);
         fclose($fd);
      }
      else
      {
         $tmpfile = $uncomptmpfile;
         if ($default->debug)
         {
            if (!file_exists($default->owl_tmpdir))
            {
               printError("$owl_lang->debug_tmp_not_exists");
            }
            if (!is_writable($default->owl_tmpdir))
            {
               printError("$owl_lang->debug_tmp_not_writeable");
            }
         }
         if (file_exists($tmpfile)) unlink($tmpfile);

         $fp = fopen($tmpfile, "wb");
         fwrite($fp, $getfile->f("data"));
         fclose($fp);
      }
   }
   return $uncomptmpfile;
}

function bIsPearAvailable()
{
   $pathArray = explode( PATH_SEPARATOR, get_include_path() );

   foreach ($pathArray as $sPath)
   {
      if (file_exists($sPath . "/PEAR.php"))
      {
        return true;
      }
   }
  return false;
}
// progress bar file fix
function file_basename($file= null) {
   if($file=== null || strlen($file)<= 0) {
       return null;
   }

   $file= explode('?', $file);
   $file= explode('\\', $file[0]);
   $basename= $file[count($file)-1];

   return $basename;
}

function bIsMicrosoftBrowser()
{

   if (eregi("(MSIE.6)", $_SERVER['HTTP_USER_AGENT']))
   {
      return true;
   }
   else if (eregi("(MSIE.7)", $_SERVER['HTTP_USER_AGENT']))
   {
      return true;
   }
   else
   {
      return false;
   }
}
function fCheckCustomRequiredFields($doctype, $iFileID)
{
   global $default, $owl_lang;
   $sql_custom = new Owl_DB;
   $sql_custom->query("SELECT * FROM $default->owl_docfields_table  WHERE doc_type_id = '$doctype'");
   $bError = 0;
   $sFieldName = "";
   while ($sql_custom->next_record())
   {
        switch ($sql_custom->f("field_type"))
        {
            case "mcheckbox":
               $iValueCount = 0;
               $bThisFieldError = false;
               $aValues = split('\|', $sql_custom->f("field_values"));
               $sThisFieldName = $sql_custom->f("field_name");
               foreach ($aValues as $sValue)
               {
                  if ( $sql_custom->f("required") == "1")
                  {
                     if ($_POST[$sql_custom->f("field_name") . "_" . $iValueCount] ==  "")
                     {
                        $bThisFieldError = true;
                     }
                     else
                     {
                        $bThisFieldError = false;
                        break;
                     }
                  }
                  else
                  {
                     break;
                  } 
                  $iValueCount++;
               }

               if ($bThisFieldError === true)
               {
                  $bError++;
                  $sFieldName .= " " . $sThisFieldName;
               }
             break;
             case "radio":
               if ( $sql_custom->f("required") == "1" and $_POST[$sql_custom->f("field_name") . $iFileID] == "")
               {
                     $bError++;
                     $sFieldName .= " " . $sql_custom->f("field_name");
               }
             break;
             default:
               if ( $sql_custom->f("required") == "1" and $_POST[$sql_custom->f("field_name")] == "")
               {
                     $bError++;
                     $sFieldName .= " " . $sql_custom->f("field_name");
               }

             break;
         }
   }
   if ($bError > 0)
   {
      printError($owl_lang->err_doc_field_req ." B:" .$bError, $sFieldName);
   }
}

?>
