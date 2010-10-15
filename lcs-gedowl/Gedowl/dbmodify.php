<?php
/**
 * dbmodify.php
 * 
 *
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 * $Id: dbmodify.php,v 1.81 2007/10/02 20:13:03 b0zz Exp $
 */

//$post_data_file = "$tmp_dir/$sessionid"."_postdata";
//$monitor_file = "$tmp_dir/$sessionid"."_flength";
//$signal_file = "$tmp_dir/$sessionid"."_signal";
//$qstring_file = "$tmp_dir/$sessionid"."_qstring";
//modif1 misterphi
function SansAccent($texte){
$accent=utf8_decode('ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ');
$noaccent=utf8_decode('AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn');
$texte = strtr($texte,$accent,$noaccent);
return $texte;
}
//eom1
ob_start();
require_once(dirname(__FILE__) ."/config/owl.php");
$out = ob_get_clean();

if ($default->use_progress_bar == 1)
{
   $sid = $_GET['sid'];
   if (file_exists($default->progress_bar_tmp_dir . "/{$sid}_qstring"))
   {
      $qstr = join("",file($default->progress_bar_tmp_dir . "/{$sid}_qstring"));
      parse_str($qstr);
   }
}


require_once($default->owl_fs_root ."/lib/disp.lib.php");
require_once($default->owl_fs_root ."/lib/owl.lib.php");
require_once($default->owl_fs_root ."/lib/security.lib.php");
require_once($default->owl_fs_root ."/scripts/phpmailer/class.phpmailer.php");
require_once($default->owl_fs_root ."/lib/pclzip/pclzip.lib.php");
// Code to Handle Document Type page refresh

if (($action == "file_upload" or $action == "zip_upload") and !isset($send_file_x))
{
   header("Location: modify.php?sess=$sess&action=$action&parent=$parent&expand=$expand&order=$order&sortname=$sort&doctype=$doctype&type=$type");
   exit;
}




// Code to handle the click on the bulk action
// image button;
if (isset($bemailaction_x))
{
   $action = $owl_lang->email_selected;
} elseif (isset($bmoveaction_x))
{
   $action = $owl_lang->move_selected;
} elseif (isset($bcheckout_x))
{
   $action = "bulk_checkout";
} elseif (isset($bdlaction_x))
{
   $action = "bulk_download";
} elseif (isset($bdeleteaction_x))
{
   $action = $owl_lang->del_selected;
} 
if ($sess == "0" && $default->anon_ro > 0)
{
   printError($owl_lang->err_login);
}

if (!isset($type))
{
   $type = "";
}

if (!isset($doctype))
{
   $doctype = "1";
}

if ($default->make_file_indexing_user_selectable == 1)
{
   $index_file = fIntializeCheckBox($index_file);
}
else
{
   $index_file = "1";
}

if ($action == "go_fav")
{
   if (!empty($del_favorite_0) and empty($add_favorite_0))
   {
      $qFavorite = new Owl_DB;
      $qFavorite->query("DELETE FROM $default->owl_user_favorites WHERE userid = '$userid' and folder_id = '$favorite_id_0'");
      displayBrowsePage($parent);
      exit;
   }
   if (empty($del_favorite_0) and !empty($add_favorite_0))
   {
      $qFavorite = new Owl_DB;
      $qFavorite->query("DELETE FROM $default->owl_user_favorites WHERE userid = '$userid' and folder_id = '$parent'");
      $qFavorite->query("INSERT INTO $default->owl_user_favorites VALUES ('$userid','$parent')");
      displayBrowsePage($parent);
      exit;
   }

   $parent = $favorite_id_0;
   displayBrowsePage($favorite_id_0);
   exit;
}

function fSetFileAcl( $file_id )
{
   global $default, $userid;

   $qSetAcl = "DELETE FROM $default->owl_advanced_acl_table where file_id = '$file_id'";
   $sql = new Owl_DB;
   $sql->query($qSetAcl);
 
   $keys=array_keys($_POST);
   $total_fields=count($keys);
 
   $FirstTimeThrough = true;
   $bChangeTypeSameId = false;
 
   for($index=0;$index<$total_fields;$index++)
   {
      $temp_key=$keys[$index];
      $temp=$_POST[$temp_key];
      list ($type, $acl, $gid_uid) = split("_", $temp_key);
     
      if ( $type == "gacl" or $type == "acl")
      {
         if ($iPrevType != $type)
         {
            $bChangeTypeSameId = true;
         }
         if (($iPrevUidGid != $gid_uid and $FirstTimeThrough === false) or $bChangeTypeSameId === true)
         {
            if ($iPrevType == "gacl")
            {
               $qSetAcl .= ", group_id , file_id )";
               $qSetAclValues .= ", '$iPrevUidGid', '$file_id')";
            }
            if ($iPrevType == "acl")
            {
               $qSetAcl .= ", user_id , file_id )";
               $qSetAclValues .= ", '$iPrevUidGid', '$file_id')";
            }
            $sql->query($qSetAcl . $qSetAclValues);
            $FirstTimeThrough = true;
            $bChangeTypeSameId = false;
         }
 
         if($FirstTimeThrough)
         {
            $qSetAcl = "INSERT INTO $default->owl_advanced_acl_table (";
            $qSetAclValues = " VALUES ( ";
            $FirstTimeThrough = false;
         }
         else
         {
            $qSetAcl .= ", ";
            $qSetAclValues .= ", ";
         }
	 $qSetAcl .= "$acl ";
	 $qSetAclValues .= "'1' ";
         $iPrevType = $type;
         $iPrevUidGid = $gid_uid;
      }
   }
   if ($iPrevType == "gacl")
   {
      $qSetAcl .= ", group_id , file_id )";
      $qSetAclValues .= ", '$iPrevUidGid', '$file_id')";
   }
   if ($iPrevType == "acl")
   {
      $qSetAcl .= ", user_id , file_id )";
      $qSetAclValues .= ", '$iPrevUidGid', '$file_id')";
   }
   $sql->query($qSetAcl . $qSetAclValues);
   owl_syslog(FILE_ACL, $userid, flid_to_filename($file_id), $parent, "", "FILE");
}

function fSetFolderAcl( $folder_id , $folder_propagate = 0, $file_propagate = 0)
{
   global $default, $userid;

   //print("FOLDER='$folder_id' <br />");
   $qSetAcl = "DELETE FROM $default->owl_advanced_acl_table where folder_id = '$folder_id'";
   $sql = new Owl_DB;
   $sql->query($qSetAcl);
 
   $keys=array_keys($_POST);
   $total_fields=count($keys);
 
   $FirstTimeThrough = true;
   $bChangeTypeSameId = false;
   
   for($index=0;$index<$total_fields;$index++)
   {
      $temp_key=$keys[$index];
      $temp=$_POST[$temp_key];
      list ($type, $acl, $gid_uid) = split("_", $temp_key);
     
      if ( $type == "fgacl" or $type == "facl")
      {
         if ($iPrevType != $type)
         {
            $bChangeTypeSameId = true;
         }
         if (($iPrevUidGid != $gid_uid and $FirstTimeThrough === false) or $bChangeTypeSameId === true)
         {
            if ($iPrevType == "fgacl")
            {
               $qSetAcl .= ", group_id , folder_id )";
               $qSetAclValues .= ", '$iPrevUidGid', '$folder_id')";
            }
            if ($iPrevType == "facl")
            {
               $qSetAcl .= ", user_id , folder_id )";
               $qSetAclValues .= ", '$iPrevUidGid', '$folder_id')";
            }
            $sql->query($qSetAcl . $qSetAclValues);
            $FirstTimeThrough = true;
            $bChangeTypeSameId = false;
         }
 
         if($FirstTimeThrough)
         {
            $qSetAcl = "INSERT INTO $default->owl_advanced_acl_table (";
            $qSetAclValues = " VALUES ( ";
            $FirstTimeThrough = false;
         }
         else
         {
            $qSetAcl .= ", ";
            $qSetAclValues .= ", ";
         }
	 $qSetAcl .= "$acl ";
	 $qSetAclValues .= "'1' ";
         $iPrevType = $type;
         $iPrevUidGid = $gid_uid;
      }
   }
   if ($iPrevType == "fgacl")
   {
      $qSetAcl .= ", group_id , folder_id )";
      $qSetAclValues .= ", '$iPrevUidGid', '$folder_id')";
   }
   if ($iPrevType == "facl")
   {
      $qSetAcl .= ", user_id , folder_id )";
      $qSetAclValues .= ", '$iPrevUidGid', '$folder_id')";
   }
   $sql->query($qSetAcl . $qSetAclValues);

   if(fIsAdmin()) 
   {
      $qSubFolder = new Owl_DB;
      if ($file_propagate == 1)
      {
         $qSubFolder->query("SELECT id from $default->owl_files_table where parent='$folder_id'");
         while($qSubFolder->next_record())
         {
            fSetFileAcl($qSubFolder->f("id"));
         }
      }
      if($folder_propagate == 1) 
      {
         $qSubFolder->query("SELECT id from $default->owl_folders_table where parent='$folder_id'");
         while($qSubFolder->next_record())
         {
            fSetFolderAcl($qSubFolder->f("id"), $folder_propagate, $file_propagate);
         }
      }
   }
   owl_syslog(FOLDER_ACL, $userid, fid_to_name($folder_id), $parent, "", "FILE");
}

if ($action == "folder_acl")
{
   if (check_auth($id, "folder_acl", $userid) == 1)
   {
      fSetFolderAcl($id, $folder_propagate, $file_propagate);
      displayBrowsePage($parent);
   }
   else
   {
      printError($owl_lang->err_nofoldermod);
   }
}

if ($action == "file_acl")
{
   if (check_auth($id, "file_acl", $userid) == 1)
   {
      fSetFileAcl($id);
      notify_monitored($id, $type);
      notify_monitored_folders ($parent, flid_to_filename($id));
      displayBrowsePage($parent);
   }
   else
   {
      printError($owl_lang->err_nofilemod);
   }
}

if ($action == "folder_thumb" and fisAdmin())
{
   fGenFolderThumbNails($id);
   displayBrowsePage($parent);
}
if ($action == "file_thumb" and fisAdmin())
{
   fGenerateThumbNail($id);
   displayBrowsePage($parent);
}

if ( $default->document_peer_review == 1)
{
   if (($action == "approvedoc" or $action == "docreject") and fCheckIfReviewer($id))
   {
      $sql_custom = new Owl_DB;

      // *****************************
      // PEER Review feature END
      // *****************************
      if ($action == "approvedoc")
      {
         $sql_custom->query("SELECT * FROM $default->owl_peerreview_table WHERE file_id ='$id' and status <> '1'");
         if ($sql_custom->num_rows() > 1)
         {
            notify_reviewer (owlfilecreator($id), $id, $message, "approved");
         }
         else
         {
            if($default->peer_auto_publish[$default->owl_current_db] == "true") 
            {
               notify_reviewer (owlfilecreator($id), $id, $message, "final_approved_auto", $owl_lang->peer_final_approval);

               $sql_custom->query("SELECT * FROM $default->owl_files_table WHERE id = '$id'");
               $sql_custom->next_record();

               notify_users($usergroupid, NEW_FILE, $sql_custom->f("id"));
               notify_monitored_folders ($sql_custom->f("parent"), $sql_custom->f("filename"));

               $sql_custom->query("UPDATE $default->owl_files_table SET approved = '1' WHERE id = '$id'");
               $sql_custom->query("DELETE from $default->owl_peerreview_table where file_id = '" . $id . "'");
            }
            elseif($default->peer_auto_publish[$default->owl_current_db] == "false")
            {
                 notify_reviewer (owlfilecreator($id), $id, $message, "final_approved", $owl_lang->peer_final_approval);
            }
         }
         $docstatus = "1";
      }
      else
      {
         notify_reviewer (owlfilecreator($id), $id, $message, "rejected", $reject_reason);
         $docstatus = "2";
      }

      $result = $sql_custom->query("UPDATE $default->owl_peerreview_table SET status = '$docstatus' WHERE reviewer_id = '$userid' and file_id ='$id'");

      // *****************************
      // PEER Review feature END
      // *****************************
      $urlArgs = array();
      $urlArgs['sess']      = $sess;
      $urlArgs['parent']    = $parent;
      $urlArgs['expand']    = $expand;
      $urlArgs['order']     = $order;
      $urlArgs['sortorder'] = $sortorder;
      $urlArgs['curview']     = $curview;

      $urlArgs2 = $urlArgs;
      $urlArgs2['type'] = "wa";
      $sUrl = fGetURL ('showrecords.php', $urlArgs2);

      header("Location: " . ereg_replace("&amp;","&", $sUrl));
      exit;

   }
}


if ($action == "set_intial")
{
   if (check_auth($parent, "folder_view", $userid) != "1")
   {
      printError($owl_lang->err_nofolderaccess);
   }
   else
   {
      $sql = new Owl_DB;
      $sql->query("UPDATE $default->owl_users_table SET firstdir='$parent' WHERE id = '$userid'");
   }
}


if ($action == "file_update")
{
   if (check_auth($id, "file_update", $userid) == 1)
   {
      $sql = new Owl_DB;
      if ($sign_close == "Cancel")
      {
         $sql->query("UPDATE $default->owl_files_table set checked_out='0' WHERE id='$id'");
         owl_syslog(FILE_UNLOCKED, $userid, flid_to_filename($id), $parent, $owl_lang->log_detail, "FILE");
         displayBrowsePage($parent);
         exit;
      }

      if ($inline == 1)
      {
         $new_name = flid_to_filename($id);
         $doc_size = strlen($document_content);
      }
      else
      {
         if ($default->use_progress_bar == 1)
         {
            $userfile['name'] = file_basename($file['name'][0]);
            $userfile['size'] = $file['size'][0];
            $userfile['tmp_name'] = $file['tmp_name'][0];
         }
         else
         {
            $userfile = uploadCompat("userfile");
         }

         fVirusCheck($userfile["tmp_name"], $userfile["name"]);
      
         //modif misterphi
         $new_name=SansAccent($userfile["name"]);
         //$new_name = trim(ereg_replace("[^$default->list_of_valid_chars_in_file_names]", "", ereg_replace("%20|^-", "_", $userfile["name"])));
         //eom
         $doc_size = $userfile['size'];
      }

      $newpath = $default->owl_FileDir . "/" . find_path($parent) . "/" . $new_name;

      /**
       * Begin Daphne Change - backups of files
       * If user requests automatic backups of files 
       * get current details from db and save file state information
       */
      if ($default->owl_version_control == 1)
      {
         if ($default->owl_use_fs)
         {
            $sql->query("SELECT * FROM $default->owl_files_table WHERE id='$id'");
         } 
         else
         { 
            // this is guaranteed to get the ID of the most recent revision, just in case we're updating a previous rev.
            $sql->query("SELECT distinct b.* FROM $default->owl_files_table a, $default->owl_files_table b WHERE b.id='$id' AND a.name=b.name AND a.parent=b.parent order by major_revision, minor_revision desc");
         } 

         while ($sql->next_record())
         { 
           
            // save state information
            if ($sql->f("checked_out") > 0 and $sql->f("checked_out") <>  $userid)
            {
               printError($owl_lang->err_update_file_lock . " " . uid_to_name($sql->f("checked_out")));
            }

            $major_revision = $backup_major = $sql->f("major_revision");
            $minor_revision = $backup_minor = $sql->f("minor_revision");
            $linkedto = $backup_linkedto = $sql->f("linkedto");
            $backup_filename = $sql->f("filename");
            $backup_name = $sql->f("name");

            // Tiian 2004-02-18
            // this stuff prevent errors when title contains apostrophes
            //$backup_name = ereg_replace("[\\]'", "'", $backup_name);
            $backup_name = stripslashes($backup_name);
            $backup_name = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", $backup_name));

            $backup_size = $sql->f("f_size");
            $backup_creatorid = $sql->f("creatorid"); 
            $backup_updatorid = $sql->f("updatorid"); 
            // $backup_modified = $sql->f("modified");
            $backup_smodified = $sql->f("smodified");
            //$dCreateDate = date("Y-m-d H:i:s");
            $dCreateDate = $sql->now();
            $backup_description = $sql->f("description"); 
            // This is a hack to deal with ' in the description field
            // on some system the ' is automaticaly changed to \' and that works
            // on other system it stays as ' I have no idea why
            // the 2 lines bellow should take care of any case.
            //$backup_description = ereg_replace("[\\]'", "'", $backup_description);
            $backup_description = stripslashes($backup_description);
            $backup_description = ereg_replace("'", "\\'" , $backup_description);
            $backup_name = stripslashes($backup_name);
            $backup_name = ereg_replace("'", "\\'" , $backup_name);
            $backup_metadata = $sql->f("metadata");
            $backup_metadata = stripslashes($backup_metadata);
            $backup_metadata = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", $backup_metadata));

            $backup_parent = $sql->f("parent");
            $backup_security = $sql->f("security");
            $backup_groupid = $groupid = $sql->f("groupid");
            $new_quota = fCalculateQuota($userfile['size'], $userid, "ADD");
            $filename = $sql->f(filename);
            $title = $sql->f(name);
            $description = $sql->f(description); 

            // This is a hack to deal with ' in the description field
            // on some system the ' is automaticaly changed to \' and that works
            // on other system it stays as ' I have no idea why
            // the 2 lines bellow should take care of any case.
            //$description = ereg_replace("[\\]'", "'", $description);
            $description = stripslashes($description);
            $description = ereg_replace("'", "\\'" , $description);
            //$title = ereg_replace("[\\]'", "'", $title);
            $title = stripslashes($title);
            $title = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", $title));

            if ($default->owl_use_fs)
            {
               if ($default->owl_FileDir . "/" . find_path($parent) . "/" . $sql->f(filename) != $newpath)
               {
                  printError("$owl_lang->err_file_update");
               }
            } 
         } 
      } 

      /**
       * Begin Daphne Change
       * copy old version to backup folder
       * change version numbers, 
       * update database entries
       * upload new file over the old
       * backup filename will be 'name_majorrev-minorrev' e.g. 'testing_1-2.doc'
       */

      if ($default->owl_use_fs)
      {
         if ($default->owl_version_control == 1)
         {
            if (!(file_exists($newpath) == 1) || $backup_filename != $new_name) 
            {
               printError("$owl_lang->err_file_not_exist"); 
            }
            // Get the file extension.
            $extension = explode(".", $new_name); 
            // rename the new, backed up (versioned) filename
            // $version_name = $extension[0]."_$major_revision-$minor_revision.$extension[1]";
            // BUG FIX BEGIN
            // 657896 filenames in backup folder not correct - SOLUTION
            // by: Gerald McMillen (mrshadow76)
            $extensioncounter = 0;
            while ($extension[$extensioncounter + 1] != null)
            { 
               // pre-append a "." separator in the name for each
               // subsequent part of the the name of the file.
               if ($extensioncounter != 0)
               {
                  $version_name = $version_name . ".";
               } 
               $version_name = $version_name . $extension[$extensioncounter];
               $extensioncounter++;
            } 

            if ($extensioncounter != 0)
            {
               $version_name = $version_name . "_$major_revision-$minor_revision.$extension[$extensioncounter]";
            }
            else
            {
               $version_name = $extension[0] . "_$major_revision-$minor_revision"; 
            }
            // BUG FIX END
            // specify path for new file in the /backup/ file of each directory.
            $backuppath = $default->owl_FileDir . "/" . find_path($parent) . "/$default->version_control_backup_dir_name/$version_name"; 
            // Danilo change
            if (!is_dir("$default->owl_FileDir/" . find_path($parent) . "/$default->version_control_backup_dir_name"))
            {
               mkdir("$default->owl_FileDir/" . find_path($parent) . "/$default->version_control_backup_dir_name", $default->directory_mask); 
               // End Danilo change
               // is there already a backup directory for current dir?
               if (is_dir("$default->owl_FileDir/" . find_path($parent) . "/$default->version_control_backup_dir_name"))
               {
                  $sql->query("INSERT into $default->owl_folders_table (name, parent, security, groupid, creatorid, description)  values ('$default->version_control_backup_dir_name', '$parent', '" . fCurFolderSecurity($parent) ."', '" . owlfoldergroup($parent) ."', '" . owlfoldercreator($parent) . "', '')");
               } 
               else
               {
                  printError("$owl_lang->err_backup_folder_create");
               } 
            } 
            copy($newpath, $backuppath); // copy existing file to backup folder
         } 

         // End Daphne Change
         if (!file_exists($newpath) == 1) 
         {
           printError("$owl_lang->err_file_update");
         }
         if($inline == 1)
         {
            if ($default->owl_use_fs)
            {
               $iOldSize =  filesize($newpath);
               $iCurrentSize = strlen($document_content);

               $fp = fopen($newpath, "wb");
               fwrite($fp, stripslashes($document_content));
               fclose($fp);
            }
            else
            {
               $sOldSize =  filesize( $default->owl_tmpdir . "/" . $new_name);
               $iCurrentSize = strlen($document_content);
               $tmpfile = $default->owl_tmpdir . "/" . $new_name;
               $filedata = addSlashes($document_content);
               $fp = fopen($tmpfile, "wb");
               fwrite($fp, $document_content);
               fclose($fp);
            }

            $new_current_quota = fCalculateQuota($iOldSize, $backup_creatorid, "DEL");

            $new_quota = fCalculateQuota($iCurrentSize, $backup_creatorid, "ADD");

            if (fIsQuotaEnabled($backup_creatorid))
            {
               $sql->query("UPDATE $default->owl_users_table SET quota_current = '$new_quota' WHERE id = '$backup_creatorid'");
            }
         }
         else
         {
            copy($userfile['tmp_name'], $newpath);
            unlink($userfile['tmp_name']);
         }

         if (!file_exists($newpath))
         {
            if ($default->debug == true)
            {
               printError("DEBUG: " . $owl_lang->err_upload, $newpath);
            }
            else
            {
               printError($owl_lang->err_upload); 
            }
         }
         // Begin Daphne Change
         if ($default->owl_version_control == 1)
         {
            if (!file_exists($backuppath))
            {
               printError("$owl_lang->err_backup_file"); 
            }
            // find id of the backup folder you are saving the old file to
            $sql->query("SELECT id FROM $default->owl_folders_table WHERE name='$default->version_control_backup_dir_name' AND parent='$parent'");
            while ($sql->next_record())
            {
                $backup_parent = $sql->f("id");
            } 
         } 
      } 

      if ($versionchange == 'major_revision')
      { 
         // if someone requested a major revision, must
         // make the minor revision go back to 0
         // $versionchange = "minor_revision='0', major_revision";
         // $new_version_num = $major_revision + 1;
         $new_major = $major_revision + 1;
         $new_minor = 0;
         $versionchange = "minor_revision='0', major_revision";
         $new_version_num = $major_revision + 1;
      } 
      else
      { 
         // simply increment minor revision number
         $new_version_num = $minor_revision + 1;
         $new_minor = $minor_revision + 1;
         $new_major = $major_revision;
      } 
      // End Daphne Change
      $groupid = owlusergroup($userid); 
      $smodified = $sql->now();
      // Begin Daphne Change

      if ( $default->document_peer_review == 1)
      {
         $iOneWasFound = false;
         if (isset($reviewers))
         {
            foreach ($reviewers as $iReviewerId)
            {
               if (is_numeric($iReviewerId))
               {
                  $iOneWasFound = true;
               }
            }
            if ($default->document_peer_review_optional == 1 and $iOneWasFound === false)
            {
               $iDocApproved = 1;
            }
            else
            {
               if ($iOneWasFound === false)
               {
                  if (!$default->owl_use_fs)
                  {
                     unlink($newpath);
                  }
                  printError("$owl_lang->err_select_reviewer");
               }
               else
               {
                  $iDocApproved = 0;
               }
            }
         }
      }
      else
      {
         $iDocApproved = 1;
      }

      if ($default->owl_version_control == 1)
      {
         if ($default->owl_use_fs)
         { 
            // insert entry for backup file
            $result = $sql->query("INSERT into $default->owl_files_table (name,filename,f_size,creatorid,updatorid,parent,created, smodified,groupid,description,metadata,security,major_revision,minor_revision, doctype, linkedto, approved) values ('$backup_name','$version_name','$backup_size','$backup_creatorid','$backup_updatorid','$backup_parent',$dCreateDate,'$backup_smodified','$backup_groupid', '$backup_description','$backup_metadata','$backup_security','$backup_major','$backup_minor', '$doctype', '$backup_linkedto', '1')") or unlink($backuppath);
            if (!$result && $default->owl_use_fs) unlink($newpath);

            $idbackup = $sql->insert_id($default->owl_files_table, 'id'); 
            $sql->query("UPDATE $default->owl_files_table SET f_size='$doc_size', smodified=$smodified, $versionchange='$new_version_num',description='$newdesc', approved = '$iDocApproved', updatorid='$userid'  WHERE id='$id'") or unlink($newpath);
            // UPDATE THE VERSION of the linked files as well.

            $sql->query("UPDATE $default->owl_files_table SET f_size='$doc_size', smodified=$smodified, $versionchange='$new_version_num',description='$newdesc', updatorid='$userid'  WHERE linkedto='$id'") or unlink($newpath);


               $sql->query("UPDATE $default->owl_searchidx SET owlfileid='$idbackup'  WHERE owlfileid='$id'");
               fIndexAFile($backup_filename, $newpath, $id);

            owl_syslog(FILE_UPDATED, $userid, $userfile["name"], $parent, $version_name, "FILE");
         } 
         else
         { 
            // BEGIN wes change
            // insert entry for current version of file
            $compressed = '0';
            $userfile = uploadCompat("userfile");
            fVirusCheck($userfile["tmp_name"], $userfile["name"]);
            $fsize = filesize($userfile['tmp_name']);

            
            $sql->query("INSERT INTO $default->owl_files_table (name,filename,f_size,creatorid,updatorid,parent, created, smodified,groupid,description,metadata,security,major_revision,minor_revision, doctype, linkedto, approved) VALUES ('$backup_name','" . $userfile['name'] . "','" . $userfile['size'] . "','$backup_creatorid','$userid','$parent',$dCreateDate,$smodified,'$backup_groupid', '$newdesc', '$backup_metadata','$backup_security','$new_major','$new_minor', '$doctype', '$backup_linkedto', '$iDocApproved')");

            $fid = $id;
            $id = $sql->insert_id($default->owl_files_table, 'id');

            $monitorSQL = new Owl_DB;
// Move ACL's for this file
// make them the same as the file Originally updated.
            $sql->query("UPDATE $default->owl_advanced_acl_table SET file_id='$id' WHERE file_id = '$fid'");
// 

            $monitorSQL = new Owl_DB;
            $monitorSQL->query("SELECT * FROM $default->owl_monitored_file_table WHERE fid = $fid and userid = '$userid'");
            if ($monitorSQL->num_rows() != 0)
            {
               $monitorSQL->query("SELECT id FROM $default->owl_files_table WHERE name = '$backup_name' and parent = '$parent' and major_revision = '$new_major' and minor_revision = '$new_minor'");
               $monitorSQL->next_record();
               $newmonitorid = $monitorSQL->f("id");
               $monitorSQL->query("UPDATE $default->owl_monitored_file_table SET fid = '$newmonitorid'");
            } 

            // If pdftotext was set and exists
            // Create a search index for this text file.
            fIndexAFile($userfile['name'], $userfile['tmp_name'], $id);

            if ($default->owl_compressed_database && file_exists($default->gzip_path))
            {
               system($default->gzip_path . " " . escapeshellarg($userfile['tmp_name']));
               $fd = fopen($userfile['tmp_name'] . ".gz", 'rb');
               $userfile['tmp_name'] = $userfile['tmp_name'] . ".gz";
               $fsize = filesize($userfile['tmp_name']);
               $compressed = '1';
            } 
            else
            {
               $fd = fopen($userfile['tmp_name'], 'rb');
            } 
            $filedata = fread($fd, $fsize);
            fclose($fd);
            unlink($userfile['tmp_name']);

            if ($id !== null && $filedata)
            {
               $sql->query("INSERT into $default->owl_files_data_table (id, data, compressed) values ('$id', '" . addslashes($filedata) ."','$compressed')");
            } 
            owl_syslog(FILE_UPDATED, $userid, $userfile["name"], $parent, $backup_name, "FILE"); 
         } 
      } 
      else // versioning not included in the DB update
      {
         $filename = $userfile['name'];
            // BEGIN Bozz Change
         if (getfilepolicy($id) == 5 || getfilepolicy($id) == 6)
         {
            $sql->query("UPDATE $default->owl_files_table SET f_size='" . $userfile['size'] . "',smodified=$smodified, updatorid='$userid', approved='$iDocApproved' WHERE id='$id'") or unlink($newpath);
         } 
         else
         {
            $sql->query("UPDATE $default->owl_files_table SET f_size='" . $userfile['size'] . "',updatorid='$userid', smodified=$smodified, approved='$iDocApproved' WHERE id='$id'") or unlink($newpath);
         } 

         if ($default->owl_use_fs === false)
         { 
            fDeleteFileIndexID($id);
            fIndexAFile($userfile['name'], $userfile['tmp_name'], $id);

            $fsize = filesize($userfile['tmp_name']);
            if ($default->owl_compressed_database && file_exists($default->gzip_path))
            {
               system($default->gzip_path . " " . escapeshellarg($userfile['tmp_name']));
               $fd = fopen($userfile['tmp_name'] . ".gz", 'rb');
               $userfile['tmp_name'] = $userfile['tmp_name'] . ".gz";
               $fsize = filesize($userfile['tmp_name']);
               $compressed = '1';
            }
            else
            {
               $fd = fopen($userfile['tmp_name'], 'rb');
            }
            $filedata = fread($fd, $fsize);
            fclose($fd);
            unlink($userfile['tmp_name']);

            if ($filedata)
            {
               $sql->query("UPDATE $default->owl_files_data_table set data = '" . addslashes($filedata) ."' where id ='$id'");
            }
         }
            owl_syslog(FILE_UPDATED, $userid, $userfile["name"], $parent, $backup_name, "FILE"); 
      } 
      // End Daphne Change
      if (fIsQuotaEnabled($userid)) 
      {
         $sql->query("UPDATE $default->owl_users_table SET quota_current = '$new_quota' WHERE id = '$userid'"); 
      }
      // 
      // Yes Yes Yes, I know you may get 3 notification
      // for the same file, I should probably check
      // if a notification was already sent by
      // the previous notification, but I wait and see
      // I'll probably get complaints and feed back
      // and I'll fix this later if need be.
      // 
      // *****************************
      // PEER Review feature END
      // *****************************
      if ( $default->document_peer_review == 1)
      {
         $sql_custom = new Owl_DB;
         // clean up Old review request records
         $sql_custom->query("DELETE from $default->owl_peerreview_table where file_id = '" . $id . "'");
         foreach ($reviewers as $iReviewerId)
         {
            if(!empty($iReviewerId))
            {
               $result = $sql_custom->query("INSERT INTO $default->owl_peerreview_table (reviewer_id, file_id, status) values ('$iReviewerId', '$id', '0')");
               notify_reviewer ($iReviewerId, $id , $message);
            }
         }
      }
      // *****************************
      // PEER Review feature END
      // *****************************
      if ($inline == 1)
      {
          $sql->query("UPDATE $default->owl_files_table set checked_out='0' WHERE id='$id'");
          owl_syslog(FILE_UNLOCKED, $userid, flid_to_filename($id), $parent, $owl_lang->log_detail, "FILE");
      }

      if ($iDocApproved == 1)
      {     
         notify_monitored($id, $type);
         notify_users($groupid, UPDATED_FILE, $id, $type);
         notify_monitored_folders ($parent, $filename);
      }
      displayBrowsePage($parent);
      // END BUG FIX: #433932 Fileupdate and Quotas
   } 
   else
   {
      printError($owl_lang->err_noupload);
   } 
} 

if ($action == "zip_upload")
{
   // Progress bar fix
   if ($default->use_progress_bar == 1)
   {
      $userfile['name'] = file_basename($file['name'][0]);
      $userfile['size'] = $file['size'][0];
      $userfile['tmp_name'] = $file['tmp_name'][0];
   }
   else
   {
      $userfile = uploadCompat("userfile");
   }
   fVirusCheck($userfile["tmp_name"], $userfile["name"]);
   // If the File Size is 0 File was too big.
   if ($userfile["size"] == 0)
   {
      if ($default->debug == true)
      {
         printError("DEBUG: " . "  $owl_lang->err_upload   ", $owl_lang->err_file_too_big . " " . $default->max_filesize);
      }
      else
      {
         printError("$owl_lang->err_upload");
      }
   }

   
 //modif misterphi
     //$new_name = trim(ereg_replace("[^$default->list_of_valid_chars_in_file_names]", "", ereg_replace("%20|^-", "_", $userfile["name"]))); 
      $new_name = SansAccent($userfile["name"]);
      //eom
   $dirname = preg_split("/\.zip/", $new_name);

   $newpath = $default->owl_FileDir . "/" . find_path($parent) . "/" . $dirname[0];

   if (file_exists($newpath) == 1 and $default->owl_use_fs)
   {
      if ($default->debug == true)
      {
         printError("DEBUG: " . $owl_lang->err_file_exists, $newpath);
      }
      else
      {
         printError($owl_lang->err_file_exists);
      }
   }

   copy($userfile["tmp_name"],  $default->owl_tmpdir . "/" . $new_name);

   $archive = new PclZip($default->owl_tmpdir . "/" . $new_name);

   if (($list = $archive->listContent()) == 0) 
   {
      if ($default->debug == true)
      {
         printError($owl_lang->err_not_zip, "DEBUG: " .$archive->errorInfo(true));
      }
      else
      {
         printError($owl_lang->err_not_zip );
      }

   }

   if ($default->owl_use_fs )
   {
      mkdir($newpath, $default->directory_mask);
   }
   else
   {
      mkdir($default->owl_tmpdir . "/" . $dirname[0], $default->directory_mask);
   }

   $FolderPolicy = $policy;
   $smodified = $sql->now();

   $sql->query("INSERT into $default->owl_folders_table (name,parent,security,description,groupid,creatorid, smodified) values ('$dirname[0]', '$parent', '$FolderPolicy', '$description', '$groupid', '$userid', $smodified)");
   $newParent = $sql->insert_id($default->owl_folders_table, 'id');

   fSetDefaultFolderAcl($newParent);
   fSetInheritedAcl($parent, $newParent, "FOLDER");

   if (!is_dir($newpath) and $default->owl_use_fs)
   {
      if ($default->debug == true)
      {
         printError("DEBUG:" . $owl_lang->err_folder_create, $newpath);
      }
      else
      {
         printError($owl_lang->err_folder_create);
      }
   }

   if (!$default->owl_use_fs )
   {
      $newpath = $default->owl_tmpdir . "/" . $dirname[0];
   }
   if ($archive->extract(PCLZIP_OPT_PATH, $newpath) == 0) 
   {
      if ($default->debug == true)
      {
         printError("DEBUG: " .$archive->errorInfo(true));
      }
   }
   fInsertUnzipedFiles($newpath, $newParent, $FolderPolicy, $security, $description, $groupid, $userid, $metadata, $title, $major_revision, $minor_revision, $doctype);

   if (!$default->owl_use_fs )
   {
      myDelete($default->owl_tmpdir . "/" . $dirname[0]);
   }
   unlink($default->owl_tmpdir . "/" . $new_name);
   unlink($userfile["tmp_name"]);
}

if ($action == "file_upload")
{
   if (check_auth($parent, "folder_create", $userid) == 1)
   {
      fCheckCustomRequiredFields($doctype, $id);

      $sql_custom = new Owl_DB;


      if ($default->file_desc_req == "1" and trim($description) == "")
      {
         printError("$owl_lang->err_doc_field_req ", "Description");
      }

      if (!isset($groupid))
      {
         $groupid = owlusergroup($userid);
      } 
      $sql = new Owl_DB; 
      // This is a hack to deal with ' in the description field
      // on some system the ' is automaticaly changed to \' and that works
      // on other system it stays as ' I have no idea why
      // the 2 lines bellow should take care of any case.
      //$description = ereg_replace("[\\]'", "'", $description);
      $description = stripslashes($description);
      $description = ereg_replace("'", "\\'" , $description);
      if ($type == "url")
      {
        // $smodified = date("Y-m-d H:i:s");
         $smodified = $sql->now();
         //$dCreateDate = date("Y-m-d H:i:s");
         $dCreateDate = $sql->now();
         $new_name = $userfile;
         if ($major_revision == "") $major_revision = $default->major_revision;
         if ($minor_revision == "") $minor_revision = $default->minor_revision;

         if (trim($userfile) == 'http://' or trim($userfile) == '')
         {
            printError("$owl_lang->err_bad_url");
         }
         if ($title == "")
         {
            $title = get_title_tag($userfile); 
            if ($title === false)
            {
               $title = trim($userfile);
            }
         }
         // $title = $userfile;
         // This is a hack to deal with ' in the description field
         // on some system the ' is automaticaly changed to \' and that works
         // on other system it stays as ' I have no idea why
         // the 2 lines bellow should take care of any case.

         $title = stripslashes($title);
         $title = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", $title));

         if ($default->save_keywords_to_db)
         {
            $currentvalue = array_unique(preg_split("/\s+/", strtolower($metadata)));
            $metadata = "";
   
            foreach ($currentvalue as $word)
            {
               $word = addslashes($word);
               if(!preg_grep("/$word/", $keywordpick))
               {
                  $metadata .= " " . $word;
               }
            }
   
            foreach ($keywordpick as $word)
            {
               if($word <> $owl_lang->none_selected)
               {
                  $metadata .= " " . $word;
               }
            }
         }
         else
         {
            $currentvalue = array_unique(preg_split("/\s+/", strtolower($metadata)));
            $metadata = "";
   
            foreach ($currentvalue as $word)
            {
                  $metadata .= " " . $word;
            }
         }

         $metadata = stripslashes($metadata);
         $metadata = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", strtolower($metadata)));

         $userfile = ereg_replace('\\\\', "/" , $userfile);
         $userfile = trim($userfile); 
         #$userfile = ereg_replace("file://", "file://" , $userfile);
         $userfile = ereg_replace(" ", "%20", $userfile);

         if ($major_revision == "") $major_revision = $default->major_revision;
         if ($minor_revision == "") $minor_revision = $default->minor_revision;
         if ($checked_out == "") $checked_out = 0; 


         $sql->query("INSERT into $default->owl_files_table (name,filename,f_size,creatorid, updatorid,parent,created, description,metadata,security,groupid,smodified,checked_out, major_revision, minor_revision, url, doctype, approved) values ('$title', '" . $userfile . "', '0', '$userid', '$userid', '$parent', $dCreateDate,'$description', '$metadata', '$security', '$groupid',$smodified,'$checked_out','$major_revision','$minor_revision','1','$doctype','1')");

         $id = $sql->insert_id($default->owl_files_table, 'id');  



       $sql_custom->query("SELECT * FROM $default->owl_docfields_table  WHERE doc_type_id = '$doctype'");
         while ($sql_custom->next_record())
         {
             switch ($sql_custom->f("field_type"))
             {
                case "seperator":
                   break;
                case "mcheckbox":
                      $aMultipleCheckBox = split("\|",  $sql_custom->f("field_values"));
                       $i = 0;
                       $sFieldValues = "";
                       foreach ($aMultipleCheckBox as $sValues)
                       {
                          $sFieldName = $sql_custom->f("field_name") . "_".$i;
                                                                                                                                                                                                    
                          if ($i > 0)
                          {
                             $sFieldValues .= ",";
                          }
                          $sFieldValues .= ${$sFieldName};
                          $i++;
                       }
                       $result = $sql->query("INSERT INTO $default->owl_docfieldvalues_table (file_id, field_name, field_value) values ('$id', '" . $sql_custom->f("field_name") ."', '" . $sFieldValues ."');");
                    break;
                 default:
                       $result = $sql->query("INSERT INTO $default->owl_docfieldvalues_table (file_id, field_name, field_value) values ('$id', '" . $sql_custom->f("field_name") ."', '" . ${$sql_custom->f("field_name")} ."');");
                    break;
           }
         }

         $iDocApproved = 1;
      }
      elseif ($type == "note")
      {
         $smodified = $sql->now();
         $dCreateDate = $sql->now();
         $new_name = trim(ereg_replace("\.\./", "", $title)) . ".txt";
         //modif misterphi
         //$new_name = trim(ereg_replace("[^$default->list_of_valid_chars_in_file_names]", "", ereg_replace("%20|^-", "_", $new_name)));
         $new_name = SansAccent($new_name);
         //eom
         
	
         if ($major_revision == "") 
         {
            $major_revision = $default->major_revision; 
         }
         if ($minor_revision == "") 
         {
            $minor_revision = $default->minor_revision;
         }

         if ($title == "")
         {
            printError("$owl_lang->err_note_title");
         } 
         // This is a hack to deal with ' in the description field
         // on some system the ' is automaticaly changed to \' and that works
         // on other system it stays as ' I have no idea why
         // the 2 lines bellow should take care of any case.
         //$title = ereg_replace("[\\]'", "'", $title);
         $title = stripslashes($title);
         $title = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", $title));

         if ($default->save_keywords_to_db)
         {
            $currentvalue = array_unique(preg_split("/\s+/", strtolower($metadata)));
            $metadata = "";

            foreach ($currentvalue as $word)
            {
               $word = addslashes($word);
               if(!preg_grep("/$word/", $keywordpick))
               {
                  $metadata .= " " . $word;
               }
            }

            foreach ($keywordpick as $word)
            {
               if($word <> $owl_lang->none_selected)
               {
                  $metadata .= " " . $word;
               }
            }
         }
         else
         {
            $currentvalue = array_unique(preg_split("/\s+/", strtolower($metadata)));
            $metadata = "";

            foreach ($currentvalue as $word)
            {
                  $metadata .= " " . $word;
            }
         }

         $metadata = stripslashes($metadata);
         $metadata = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", strtolower($metadata)));
         $note_size = strlen($note_content);

         if ($major_revision == "") 
         {
            $major_revision = $default->major_revision;
         }
         if ($minor_revision == "")
         {
            $minor_revision = $default->minor_revision;
         }
         if (empty($checked_out))
         {
            $checked_out = 0; 
         }

         $tmpfile = $default->owl_FileDir . "/" . find_path($parent) . "/" . $new_name;
         if (file_exists($tmpfile))
         {
            printError("$owl_lang->err_note_title_exists");
         } 

         $new_quota = fCalculateQuota($note_size, $userid, "ADD");

         $sql->query("INSERT into $default->owl_files_table (name,filename,f_size,creatorid, updatorid,parent,created, description,metadata,security,groupid,smodified,checked_out, major_revision, minor_revision, url, doctype, approved) values ('$title', '" . $new_name . "', '$note_size', '$userid', '$userid', '$parent', $dCreateDate,'$description', '$metadata', '$security', '$groupid',$smodified,'$checked_out','$major_revision','$minor_revision','2', '1', '1')");


         if ($default->owl_use_fs)
         {
            $fp = fopen($tmpfile, "wb");
            fwrite($fp, stripslashes($note_content));
            fclose($fp); 
            $searchid = $sql->insert_id($default->owl_files_table, 'id');
            fIndexAFile($new_name, $tmpfile, $searchid);
         } 
         else
         {
            $tmpfile = $default->owl_tmpdir . "/" . $new_name;
            $filedata = addslashes($note_content);
            $fp = fopen($tmpfile, "wb");
            fwrite($fp, $note_content);
            fclose($fp);
            $searchid = $sql->insert_id($default->owl_files_table, 'id'); 
            fIndexAFile($new_name, $tmpfile, $searchid);
            unlink($tmpfile);
            if ($searchid !== null && $filedata)
            {
               $sql->query("INSERT into $default->owl_files_data_table (id, data, compressed) values ('$searchid', '$filedata', '$compressed')");
            } 
         } 
         $id = $searchid;
         if ( fIsQuotaEnabled($userid) )     
         {
            $sql->query("UPDATE $default->owl_users_table set quota_current = '$new_quota' WHERE id = '$userid'");
         }
         $iDocApproved = 1;
      } 
      else
      {

         if ($default->use_progress_bar == 1)
         {
            $userfile['name'] = file_basename($file['name'][0]);
            $userfile['size'] = $file['size'][0];
            $userfile['tmp_name'] = $file['tmp_name'][0];
         }
         else
         {
            $userfile = uploadCompat("userfile"); 
         }

         if (isset($default->upload_ommit_ext))
         {
            $file_extension = fFindFileExtension ( $userfile["name"]);
            foreach ($default->upload_ommit_ext as $omit)
            {
               if ($file_extension == $omit)
               {
                  $bOmitFile = true;
               }
            }
            if($bOmitFile)
            {
               printError($owl_lang->err_forbidden_file);
            }
         }

         // If the File Size is 0 File was too big.
         if ($default->display_password_override == 1)
         {
            if ($newpassword <> $confpassword)
            {
               printError($owl_lang->err_pass_missmatch);
            }
            else
            {
               if (!empty($newpassword))
               {
                  $newpassword = md5(stripslashes($newpassword));
               }
               else
               {
 
               }
            }
         }
         else
         {
            $newpassword = "";
         }

         if ($userfile["size"] == 0)
         {
            if ($default->debug == true)
            {
               $iDiskFree = gen_filesize(disk_free_space($userfile["tmp_name"]));

               printError("DEBUG: " . $owl_lang->err_upload . "PHP tempdir Free Space: $iDiskFree, " . $owl_lang->err_file_too_big . " " . $default->max_filesize);
            }
            else
            {
               printError("$owl_lang->err_upload");
            }
         } 
             
         fVirusCheck($userfile["tmp_name"], $userfile["name"]);

         $new_quota = fCalculateQuota($userfile["size"], $userid, "ADD");
	
	//modif misterphi
	//$new_name = trim(ereg_replace("[^$default->list_of_valid_chars_in_file_names]", "", ereg_replace("%20|^-", "_", $userfile["name"])));
	$new_name = SansAccent($userfile["name"]);
	//eom
         
         if ($default->owl_use_fs)
         {
            $newpath = $default->owl_FileDir . "/" . find_path($parent) . "/" . $new_name;
            if (file_exists($newpath) == 1)
            {
               if ($default->debug == true)
               {
                  printError("DEBUG: " . $owl_lang->err_file_exists, $newpath);
               }
               else
               {
                  printError($owl_lang->err_file_exists);
               }
            }

            if ($default->use_progress_bar == 1)
            {
               copy($userfile["tmp_name"], $newpath);
            }
            else
            {
               move_uploaded_file( $userfile['tmp_name'], $newpath );
            }

            if (!file_exists($newpath))
            {
               if ($default->debug == true)
               {
                  printError("DEBUG: " . $owl_lang->err_upload, $newpath);
               }
               else
               {
                  printError($owl_lang->err_upload);
               }
            }
         } 
         else
         { 
            // is name already used?
            $sql->query("SELECT filename FROM $default->owl_files_table WHERE filename = '$new_name' and parent='$parent'");
            while ($sql->next_record())
            {
               if ($sql->f("filename"))
               { 
                  // can't move...
                  printError("$owl_lang->err_fexist1", $owl_lang->err_fexist2 . "<i>$new_name</i>" . $owl_lang->err_fexist3);
               } 
            } 
         } 

         $smodified = $sql->now();
         $dCreateDate = $sql->now();
         if ($title == "") $title = $new_name;
         if ($major_revision == "") $major_revision = $default->major_revision;
         if ($minor_revision == "") $minor_revision = $default->minor_revision;
         if ($checked_out == "") $checked_out = 0; 

         $title = stripslashes($title);
         $title = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", $title));

         if ($default->save_keywords_to_db)
         {
            $currentvalue = array_unique(preg_split("/\s+/", strtolower($metadata)));
            $metadata = "";

            foreach ($currentvalue as $word)
            {
               $word = addslashes($word);
               if(!preg_grep("/$word/", $keywordpick))
               {
                  $metadata .= " " . $word;
               }
            }

            foreach ($keywordpick as $word)
            {
               if($word <> $owl_lang->none_selected)
               {
                  $metadata .= " " . $word;
               }
            }
         }
         else
         {
            $currentvalue = array_unique(preg_split("/\s+/", strtolower($metadata)));
            $metadata = "";

            foreach ($currentvalue as $word)
            {
                  $metadata .= " " . $word;
            }
         }

         $metadata = stripslashes($metadata);
         $metadata = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", strtolower($metadata)));

         if ( $default->document_peer_review == 1)
         {
            $iOneWasFound = false;
            if (isset($reviewers))
            {
               foreach ($reviewers as $iReviewerId)
               {
                  if (is_numeric($iReviewerId))
                  {
                     $iOneWasFound = true;
                  }
               }
               if ($default->document_peer_review_optional == 1 and $iOneWasFound === false)
               {
                  $iDocApproved = 1;
               } 
               else
               {
                  if ($iOneWasFound === false)
                  {
                     if ($default->owl_use_fs)
                     {
                        unlink($newpath); 
                     }
                     printError("$owl_lang->err_select_reviewer");
                  }
                  else
                  {
                     $iDocApproved = 0;
                  }
               }
            }
            else
            {
               if ($default->document_peer_review_optional == 1 and $iOneWasFound === false)
               {
                  $iDocApproved = 1;
               } 
               else
               {
                  $iDocApproved = 0;
               }
            }
         }
         else
         {
            $iDocApproved = 1;
         }
         // IF the Folder is in the special folder array
         // Use the values from the array

         if(!empty($default->special_folder_defaults[$parent]))
         {
            $iCreatorID = $default->special_folder_defaults[$parent]['creatorid'];
            $iGroupID = $default->special_folder_defaults[$parent]['groupid'];
            $iSecurity = $default->special_folder_defaults[$parent]['security'];
            if (empty($description) or strlen(trim($description)) == 0)
            {
               $sDescription = $default->special_folder_defaults[$parent]['description'];
            }
            else
            {
               $sDescription = $description;
            }
            if (empty($metadata) or strlen(trim($metadata)) == 0)
            {
               $sMetadata = $default->special_folder_defaults[$parent]['metadata'];
            }
            else
            {
               $sMetadata = $metadata;
            }
         }
         else
         {
            $iCreatorID = $userid;
            $iGroupID = $groupid;
            $iSecurity = $security;
            $sDescription = $description;
            $sMetadata = $metadata;
         }

         $result = $sql->query("INSERT INTO $default->owl_files_table (name,filename,f_size,creatorid,updatorid,parent,created,description,metadata,security,groupid,smodified,checked_out, major_revision, minor_revision, url, doctype, password, linkedto, approved) values ('$title', '$new_name', '" . $userfile['size'] . "', '$iCreatorID', '$iCreatorID', '$parent', $dCreateDate, '$sDescription', '$sMetadata', '$security', '$iGroupID',$smodified,'$checked_out','$major_revision','$minor_revision', '0', '$doctype', '$newpassword' ,'0', '$iDocApproved')") or unlink($newpath);

         if (!$result && $default->owl_use_fs) unlink($newpath); 
         // IF the file was inserted in the database now INDEX it for SEARCH.
         if (!$default->owl_use_fs)
         {
            $newpath = $userfile['tmp_name'];
         } 

         $id = $sql->insert_id($default->owl_files_table, 'id');  

         $sql_custom->query("SELECT * FROM $default->owl_docfields_table  WHERE doc_type_id = '$doctype'");
         while ($sql_custom->next_record())
         {
             switch ($sql_custom->f("field_type"))
             {
                case "seperator":
                   break;
                case "mcheckbox":
                      $aMultipleCheckBox = split("\|",  $sql_custom->f("field_values"));
                       $i = 0;
                       $sFieldValues = "";
                       foreach ($aMultipleCheckBox as $sValues)
                       {
                          $sFieldName = $sql_custom->f("field_name") . "_".$i;

                          if ($i > 0)
                          {
                             $sFieldValues .= ",";
                          }
                          $sFieldValues .= ${$sFieldName};
                          $i++;
                       }
                       $result = $sql->query("INSERT INTO $default->owl_docfieldvalues_table (file_id, field_name, field_value) values ('$id', '" . $sql_custom->f("field_name") ."', '" . $sFieldValues ."');"); 
                    break;
                 default:
                       $result = $sql->query("INSERT INTO $default->owl_docfieldvalues_table (file_id, field_name, field_value) values ('$id', '" . $sql_custom->f("field_name") ."', '" . ${$sql_custom->f("field_name")} ."');"); 
                    break;
           }
         }

         // *****************************
         // PEER Review feature END
         // *****************************
         if ( $default->document_peer_review == 1)
         {
            foreach ($reviewers as $iReviewerId)
            {
               if(!empty($iReviewerId))
               { 
                  $result = $sql_custom->query("INSERT INTO $default->owl_peerreview_table (reviewer_id, file_id, status) values ('$iReviewerId', '$id', '0')");
                  notify_reviewer ($iReviewerId, $id , $message);
               }
            }
         }
         // *****************************
         // PEER Review feature END
         // *****************************

         // If pdftotext was set and exists
         // Create a search index for this text file.

         fIndexAFile($new_name, $newpath, $id);

         $compressed = '0';
         $file = uploadCompat("userfile");
         //fVirusCheck($userfile["tmp_name"], $userfile["name"]);

         $fsize = $userfile['size'];
         if (!$default->owl_use_fs && $default->owl_compressed_database && file_exists($default->gzip_path))
         {
            system($default->gzip_path . " " . escapeshellarg($userfile['tmp_name']));
            $userfile['tmp_name'] = $userfile['tmp_name'] . ".gz";
            $fsize = filesize($userfile['tmp_name']);
            $compressed = '1';
         } 
         // BEGIN wes change
         if (!$default->owl_use_fs)
         {
            $fd = fopen($userfile['tmp_name'], 'rb');
            $filedata = fread($fd, $fsize);
            fclose($fd);
            unlink($userfile['tmp_name']);

            if ($id !== null && $filedata)
            {
               $sql->query("INSERT into $default->owl_files_data_table (id, data, compressed) values ('$id', '" . addslashes($filedata) ."', '$compressed')");
            } 
         } 

         fGenerateThumbNail($id);

         if ( fIsQuotaEnabled($userid) )     
         {
            $sql->query("UPDATE $default->owl_users_table set quota_current = '$new_quota' WHERE id = '$userid'");
         }
      } 

      if ($savekeyword == 1 and $default->save_keywords_to_db)
      {
         $newkeywords = preg_split("/\s+/", $metadata);
         $sql = new Owl_DB;
         foreach ($newkeywords as $word)
         {
            $word = trim(strtolower($word));
            if (!empty($word))
            {
               $sql->query("SELECT * FROM $default->owl_keyword_table WHERE keyword_text = '$word' ");
               if ($sql->num_rows() == 0)
               {
                  $sql->query("INSERT INTO $default->owl_keyword_table (keyword_text) values ('$word') ");
               }
            }
         }
      }

      fSetDefaultFileAcl($id);
      fSetInheritedAcl($parent, $id, "FILE");

      if ($iDocApproved == 1)
      {
         notify_users($groupid, NEW_FILE, $id, $type);
         notify_monitored_folders ($parent, $new_name);
      }
      owl_syslog(FILE_UPLOAD, $userid, $new_name, $parent, $owl_lang->log_detail, "FILE");

      if ($set_acl == 1)
      {
         header("Location: " . $default->owl_root_url . "/setacl.php?sess=$sess&expand=$expand&action=file_acl&order=$order&sortname=$sortname&edit=1&id=" . $id . "&parent=" . $parent);
         exit;
      }
      else
      {
         displayBrowsePage($parent);
      }
   } 
   else
   {
      printError($owl_lang->err_noupload);
   } 
} 

if ($action == "file_modify")
{
   if (check_auth($id, "file_property", $userid) == 1)
   {
      $sql = new Owl_DB; 
      $smodified = $sql->now();
      if ($default->display_password_override == 1)
      {
          if ($newpassword <> $confpassword)
          {
             printError($owl_lang->err_pass_missmatch);
          }
          else
          {
             if (!empty($newpassword))
             {
                $newpassword = md5($newpassword);
             }
             else
             {
                $sql->query("select password FROM " . $default->owl_files_table . " WHERE id='$id'");
                $sql->next_record();
                $newpassword = $sql->f("password");
             }
         
          }
      }
      else
      {
         $newpassword = "";
      }

      if ($default->file_desc_req == "1" and trim($description) == "")
      {
         printError("$owl_lang->err_doc_field_req ", "Description");
      }

      if ($saved_doctype == $doctype)
      {
         fCheckCustomRequiredFields($doctype, $id);
         $sql_custom = new Owl_DB;
      }
      // Begin Bozz Change
      if (!isset($groupid))
      {
         if (owlfilecreator($id) ==  $file_owner)
         {
            $groupid = owlusergroup($userid);
         }
         else
         {
            $groupid = owlusergroup($file_owner);
         }
     
      } 
      // BEGIN WES change
      if ($default->save_keywords_to_db)
      {
         $currentvalue = array_unique(preg_split("/\s+/", strtolower($metadata)));
         $metadata = "";

         foreach ($currentvalue as $word)
         {
            $word = addslashes($word);
            if(!preg_grep("/$word/", $keywordpick))
            {
               $metadata .= " " . $word;
            }
         }

         foreach ($keywordpick as $word)
         {
            $word = addslashes($word);
            $metadata .= " " . $word;
         }
      }
      else
      {
         $currentvalue = array_unique(preg_split("/\s+/", strtolower($metadata)));
         $metadata = "";

         foreach ($currentvalue as $word)
         {
               $metadata .= " " . $word;
         }
      } 

      $metadata = stripslashes($metadata);
      $metadata = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", strtolower($metadata)));

      if (!$default->owl_use_fs)
      {
         $name = flid_to_name($id);
         if ($name != $title)
         { 
            // we're changing the name ... need to roll this to other revisions
            // is name already used?
            //$title = ereg_replace("[\\]'", "'", $title);
            $title = stripslashes($title);
            $title = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", $title));

            $sql->query("SELECT name FROM $default->owl_files_table WHERE name = '$title' and parent='$parent'");
            while ($sql->next_record())
            {
               if ($sql->f("name"))
               { 
                  // can't move...
                  printError("$owl_lang->err_fexist1", $owl_lang->err_fexist2 . "<i>$new_name</i>" . $owl_lang->err_fexist3);
               } 
            } 
            //$title = ereg_replace("[\\]'", "'", $title);
            $title = stripslashes($title);
            $title = ereg_replace("'", "\\'" , ereg_replace("[<>]", "", $title));
            $sql->query("UPDATE $default->owl_files_table set smodified = $smodified, name='$title', filename='$filename' WHERE parent='$parent' AND name = '$name'");
         } 
      } 
      // This is a hack to deal with ' in the description field
      // on some system the ' is automaticaly changed to \' and that works
      // on other system it stays as ' I have no idea why
      // the 2 lines bellow should take care of any case.
      //$description = ereg_replace("[\\]'", "'", $description);
      $description = stripslashes($description);
      $description = ereg_replace("'", "\\'" , $description);

      //$title = ereg_replace("[\\]'", "'", $title);
      $title = stripslashes($title);
      $title = ereg_replace("'", "\\'" , $title);

      if (isset($note_content))
      {
         if (strlen(trim($new_filename)) == 0 or empty($new_filename))
         {
            printError("The filename cannot be empty");
         }

         $tmpfile = $default->owl_FileDir . "/" . find_path($parent) . "/" . $new_filename;
         $sOldFile = $default->owl_FileDir . "/" . find_path($parent) . "/" . flid_to_filename($id);
         if ($tmpfile <> $sOldFile and file_exists($tmpfile))
         {
                  printError("$owl_lang->err_fexist1", $owl_lang->err_fexist2 . "<i>$new_filename</i>" . $owl_lang->err_fexist3);
         }

         $note_size = strlen($note_content);
         $updquota = new Owl_DB;
         $updquota->query("SELECT creatorid, f_size FROM $default->owl_files_table WHERE id = '$id'");
         $updquota->next_record();
         $iCurrentCreatorid = $updquota->f("creatorid");
         $iSize = $updquota->f("f_size");

         if ($iCurrentCreatorid != $file_owner)
         {
            $new_current_quota = fCalculateQuota($iSize, $file_owner, "DEL");
            $new_quota = fCalculateQuota($iSize, $file_owner, "ADD");

            if (fIsQuotaEnabled($file_owner))
            {
               $updquota->query("UPDATE $default->owl_users_table set quota_current = '$new_quota' WHERE id = '$file_owner'");
            } 
            if (fIsQuotaEnabled($iCurrentCreatorid))
            {
               $updquota->query("UPDATE $default->owl_users_table set quota_current = '$new_current_quota' WHERE id = '$iCurrentCreatorid'");
            } 
         } 
         else
         {
            $new_quota = fCalculateQuota($iSize, $file_owner, "DEL");
            $new_quota = $new_quota + $note_size;
            $updquota->query("UPDATE $default->owl_users_table set quota_current = '$new_quota' WHERE id = '$file_owner'");
            
         }

         if ($default->owl_use_fs)
         {
            unlink($sOldFile);
            $fp = fopen($tmpfile, "wb");
            fwrite($fp, stripslashes($note_content));
            fclose($fp);
         } 
         else
         {
            $filedata = $note_content;
            $sql->query("UPDATE $default->owl_files_data_table set data = '$filedata' WHERE id = '$id'");
         } 

         $sql->query("UPDATE $default->owl_files_table SET name='$title', filename='$new_filename', security='$security', metadata='$metadata', description='$description',groupid='$groupid', creatorid ='$file_owner' , updatorid = '$userid', smodified = $smodified, f_size = '$note_size' , password = '$newpassword', major_revision = '$major_revision', minor_revision = '$minor_revision' WHERE id = '$id'");
      } 
      else
      {
         $updquota = new Owl_DB;
         $updquota->query("SELECT creatorid, f_size FROM $default->owl_files_table WHERE id = '$id'");
         $updquota->next_record();
         $iCurrentCreatorid = $updquota->f("creatorid");
         $iSize = $updquota->f("f_size");

         if ($iCurrentCreatorid != $file_owner)
         {
            $current_quota_max = 0;
            $new_current_quota = fCalculateQuota($iSize, $iCurrentCreatorid, "DEL");

            $new_quota = fCalculateQuota($iSize, $file_owner, "ADD");

            if (fIsQuotaEnabled($iCurrentCreatorid))
            {
               $updquota->query("UPDATE $default->owl_users_table set quota_current = '$new_current_quota' WHERE id = '$iCurrentCreatorid'");
            } 
            if (fIsQuotaEnabled($file_owner))
            {
               $updquota->query("UPDATE $default->owl_users_table set quota_current = '$new_quota' WHERE id = '$file_owner'");
            } 
         } 
         // ianm wants to put the file rename code here.  then use the following update command to update the database.
         if ($filename != $new_filename and $type <> "url")
         {
         //modif misterphi
	//$new_filename = trim(ereg_replace("[^$default->list_of_valid_chars_in_file_names]", "", ereg_replace("%20|^-", "_", $new_filename)));
	$new_filename = SansAccent($new_filename);
	//eom
            
            if ($default->owl_use_fs) 
            {
               if (!file_exists($default->owl_FileDir . "/" . find_path($parent) . "/" .  $new_filename)) 
               {
                  // Also rename backup versions of the filesA
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

                     $aFirstpExtension = fFindFileFirstpartExtension ($new_filename);
                     $new_firstpart = $aFirstpExtension[0];
                     $new_file_extension = $aFirstpExtension[1];

                     $sql->query("SELECT * FROM $default->owl_files_table WHERE (filename LIKE '" . $firstpart . "\\\_%" . $file_extension . "' AND parent = '$backup_parent') OR (filename = '$filename' AND parent = '$parent') order by major_revision desc, minor_revision desc");
                     while ($sql->next_record())
                     {
                        $major_revision = $sql->f("major_revision");
                        $minor_revision = $sql->f("minor_revision");
                        if ($filename == $firstpart.'_'.$major_revision.'-'.$minor_revision.".".$file_extension)
                        {
                           $new_filename = str_replace("$firstpart","$new_firstpart", $sql->f("filename"));
                           $sFilePath = $default->owl_FileDir . "/" . find_path($sql->f("parent"));
                           rename($sFilePath ."/". $sql->f("filename"), $sFilePath ."/". $new_filename);
                           $filename = $new_filename;
                           $upd_sql = new Owl_DB; 
                           $iId = $sql->f("id");
                           $upd_sql->query("UPDATE $default->owl_files_table set smodified = $smodified, name='$title', filename='$filename', security='$security', metadata='$metadata', description='$description',groupid='$groupid', creatorid ='$file_owner' ,updatorid = '$userid',  password = '$newpassword' WHERE id = '$iId'");
                        }
                     }
                 }   
                 else
                 {
                    $oldWD = getcwd();
                    chdir ($default->owl_FileDir . "/" . find_path($parent));
                    rename($filename, $new_filename);
                    chdir($oldWD);
                    $filename = $new_filename;
                    $sql->query("UPDATE $default->owl_files_table set smodified = $smodified, name='$title', filename='$filename', security='$security', metadata='$metadata', description='$description',groupid='$groupid', creatorid ='$file_owner',updatorid = '$userid',  password = '$newpassword'  WHERE id = '$id'");
                 }
                 // END
               } 
               else 
               {
                  printError($owl_lang->err_filemove_exist);
               }
            } // end owl use fs  nothing in yet for DB only.
            else
            {
               $filename = $new_filename;
               $sql->query("UPDATE $default->owl_files_table set smodified = $smodified, name='$title', filename='$filename', security='$security', metadata='$metadata', description='$description',groupid='$groupid', creatorid ='$file_owner' , updatorid = '$userid', password = '$newpassword' WHERE id = '$id'");
            }
         } // end fildname change if.
         // End of ianm change.
         else
         {
            if ($type == "url")
            {
               $filename = $new_filename;
            }

            if (empty($major_revision))
            {
               $major_revision = $default->major_revision;
            }
   
            if (empty($minor_revision))
            {
               $minor_revision = $default->minor_revision;
            }

            $sql->query("UPDATE $default->owl_files_table set name='$title', smodified = $smodified, filename='$filename', security='$security', metadata='$metadata', description='$description',groupid='$groupid', creatorid ='$file_owner' ,updatorid = '$userid',  password = '$newpassword', doctype='$doctype',major_revision = '$major_revision', minor_revision = '$minor_revision' WHERE id = '$id'");

            if ($saved_doctype == $doctype)
            {
               $sql_custom->query("SELECT * FROM $default->owl_docfields_table  WHERE doc_type_id = '$doctype'");
               while ($sql_custom->next_record())
               {
                   switch ($sql_custom->f("field_type"))
                   {
                      case "seperator":
                         break;
                      case "mcheckbox":
                            $aMultipleCheckBox = split("\|",  $sql_custom->f("field_values"));
                             $i = 0;
                             $sFieldValues = "";
                             foreach ($aMultipleCheckBox as $sValues)
                             {
                                $sFieldName = $sql_custom->f("field_name") . "_".$i;
      
                                if ($i > 0)
                                {
                                   $sFieldValues .= ",";
                                }
                                $sFieldValues .= ${$sFieldName};
                                $i++;
                             }
                             $result = $sql->query("UPDATE $default->owl_docfieldvalues_table set field_value = '" . $sFieldValues . "' WHERE file_id = '$id' and field_name = '" . $sql_custom->f("field_name") ."';");
                          break;
                      case "radio":
		       $result = $sql->query("UPDATE $default->owl_docfieldvalues_table set field_value = '" . ${$sql_custom->f("field_name") . $id} . "' WHERE file_id = '$id' and field_name = '" . $sql_custom->f("field_name") ."';");
                          break;
                       default:
                             $result = $sql->query("UPDATE $default->owl_docfieldvalues_table set field_value = '" . ${$sql_custom->f("field_name")} . "' WHERE file_id = '$id' and field_name = '" . $sql_custom->f("field_name") ."';");
                          break;
                  }
               }
            }
            else
            {
               $sql_custom = new Owl_DB;
               $sql_custom_2 = new Owl_DB;
               $sql_custom->query("SELECT * FROM $default->owl_docfields_table  WHERE doc_type_id = '$doctype'");
               if ($sql_custom->num_rows() > 0)
               {
                  $sWhereClause = " AND (";
                  while ($sql_custom->next_record())
                  {
                     $sWhereClause .= "field_name <> '" . $sql_custom->f('field_name') . "' OR ";
                  }
                  $sWhereClause .= " 1=0)";
               }
               else
               {
                  $sWhereClause = "";
               }
               $sql_custom->query("DELETE FROM $default->owl_docfieldvalues_table  WHERE file_id = '$id' $sWhereClause");

               $sql_custom->query("SELECT * FROM $default->owl_docfields_table  WHERE doc_type_id = '$doctype'");
               if ($sql_custom->num_rows() > 0)
               {
                  while($sql_custom->next_record())
                  {
                     $sql_custom_2->query("SELECT id FROM $default->owl_docfieldvalues_table  WHERE file_id = '$id' and field_name = '" . $sql_custom->f('field_name') . "'");
                     if ($sql_custom_2->num_rows() == 0 )
                     {
                        $sql_custom_2->query("INSERT INTO $default->owl_docfieldvalues_table (file_id, field_name) VALUES ( '$id' , '" . $sql_custom->f("field_name") ."');");
                     }
                  }
               }
            }
         }
      } 

      if ($savekeyword == 1 and $default->save_keywords_to_db)
      {
         $newkeywords = preg_split("/\s+/", $metadata);
         $sql = new Owl_DB;
         foreach ($newkeywords as $word)
         {
            $word = trim(strtolower($word));
            if (!empty($word))
            {
               $sql->query("SELECT * FROM $default->owl_keyword_table WHERE keyword_text = '$word' ");
               if ($sql->num_rows() == 0)
               {
                  $sql->query("INSERT INTO $default->owl_keyword_table (keyword_text) values ('$word') ");
               }
            }
         }
      }


      owl_syslog(FILE_CHANGED, $userid, flid_to_filename($id), $parent, $owl_lang->log_detail, "FILE"); 
      // End Bozz Change
      if ($saved_doctype == $doctype)
      {
         displayBrowsePage($parent);
         exit;
      }
      else
      {
         header("Location: " . $default->owl_root_url . "/modify.php?sess=$sess&expand=$expand&action=file_modify&order=$order&sortname=$sortname&id=" . $id . "&parent=" . $parent);
         exit;
      }
   } 
   else
   {
      printError($owl_lang->err_nofilemod);
   } 
} 
// 
// Delete Requested file
// 
if ($action == "file_delete")
{
   delFile($id, "file_delete");
} 
// Begin Daphne Change
// the file policy authorisation has been taken from file_modify
// (it's assumed that if you can't modify the file you can't check it out)
if ($action == "file_lock")
{
   if (check_auth($id, "file_lock", $userid) == 1)
   {
      $sql = new Owl_DB; 
      // Begin Bozz Change
      if (owlusergroup($userid) != 0)
      {
         $groupid = owlusergroup($userid);
      } 
      // check that file hasn't been reserved while updates have gone through
      $sql->query("SELECT checked_out FROM $default->owl_files_table WHERE id = '$id'");

      while ($sql->next_record())
      {
         $file_lock = $sql->f("checked_out");
      } 

      if ($file_lock == 0)
      { 
         // reserve the file
         $sql->query("UPDATE $default->owl_files_table set checked_out='$userid' WHERE id='$id'");
         owl_syslog(FILE_LOCKED, $userid, flid_to_filename($id), $parent, $owl_lang->log_detail, "FILE");
      } 
      else
      {
         if ($file_lock == $userid || fIsAdmin())
         { 
            // check the file back in
            $sql->query("UPDATE $default->owl_files_table set checked_out='0' WHERE id='$id'");
            owl_syslog(FILE_UNLOCKED, $userid, flid_to_filename($id), $parent, $owl_lang->log_detail, "FILE");
         } 
         else
         {
            printError("$owl_lang->err_file_lock " . uid_to_name($file_lock) . ".");
         } 
      } 
      displayBrowsePage($parent);
   } 
   else
   {
      printError("$owl_lang->err_nofilemod");
   } 
} 
// End Daphne Change
if ($action == "del_comment")
{
   if (check_auth($id, "file_comment", $userid) == 1)
   {
      $sql = new Owl_DB;
      $sql->query("DELETE FROM $default->owl_comment_table WHERE id = '$cid'");

      header("Location: " . $default->owl_root_url . "/modify.php?sess=$sess&expand=$expand&action=file_comment&type=url&order=$order&sortname=$sortname&id=" . $id . "&parent=" . $parent);
      exit;
   } 
} 
if ($action == "file_comment")
{
   if (empty($newcomment))
   {
      printError("ERROR: $owl_lang->err_comment_empty");
   }
   if (check_auth($id, "file_comment", $userid) == 1)
   {
      // This is a hack to deal with ' in the comment field
      // on some system the ' is automaticaly changed to \' and that works
      // on other system it stays as ' I have no idea why
      // the 2 lines bellow should take care of any case.
      //$newcomment = ereg_replace("[\\]'", "'", $newcomment);
      $newcomment = stripslashes($newcomment);
      $newcomment = ereg_replace("'", "\\'" , $newcomment);

      $sql = new Owl_DB;
      //$dTimeStamp = date("Y-m-d H:i:s"); 
      $dTimeStamp = $sql->now();
      if (empty($cid))
      {
         $sql->query("INSERT into $default->owl_comment_table (userid, fid, comment_date, comments)  values ('$userid', '$id', $dTimeStamp, '$newcomment')");
      }
      else
      {
         $sql->query("UPDATE $default->owl_comment_table set comments = '$newcomment', comment_date = $dTimeStamp where id = '$cid' and fid = '$id' and userid = '$userid'");
      }
      notify_file_owner($id, $newcomment);

      header("Location: " . $default->owl_root_url . "/modify.php?sess=$sess&expand=$expand&action=file_comment&type=url&order=$order&sortname=$sortname&id=" . $id . "&parent=" . $parent);
      exit;
   } 
   else
   {
      printError($lang_nofileaccess);
   }

} 

if ($action == "email" and fIsEmailToolAccess($userid))
{
      $mail = new phpmailer();

      //print("<pre>");
      //$mail->SMTPDebug = true;
      //$mail->do_debug = 99;

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

      if (trim($mailto) != "")
      {
         $r = preg_split("(\;|\,)", $mailto);
         reset ($r);
         while (list ($occ, $email) = each ($r))
         {
            $mail->AddAddress($email);
         }
      } 
      else
      {
         $mail->AddAddress($pick_mailto);
      } 

      if ($replyto == "")
      {
         $mail->AddReplyTo("$default->owl_email_replyto", "OWL Intranet");
      }
      else
      {
         $mail->AddReplyTo("$replyto");
      }

      if ($ccto != "")
      {
         $mail->AddCC($ccto);
      }

      $mail->WordWrap = 50; // set word wrap to 50 characters
      $mail->IsHTML(true); // set email format to HTML
      $mail->Subject = "$subject";
      $mail->Body = "<html><body>" .  fCleanDomTTContent($mailbody);
      $mail->Body .= "</body></html>";
      $mail->altBody = $mailbody;

      if (!$mail->Send())
      {
         printError($owl_lang->err_email, $mail->ErrorInfo);
      } 
} 

if ($action == "file_email")
{
   if (check_auth($id, "file_email", $userid) == 1)
   //if (check_auth($id, "file_download", $userid) == 1)
   {
      $sql = new Owl_DB;
      $path = "";
      $id = fGetPhysicalFileId($id);
      $disppath = find_path(owlfileparent($id));
      $filename = flid_to_filename($id);
      if ($default->owl_use_fs)
      {
         $fID = owlfileparent($id);
         do
         {
            $sql->query("SELECT name,parent FROM $default->owl_folders_table WHERE id='$fID'");
            while ($sql->next_record())
            {
               $tName = $sql->f("name");
               $fID = $sql->f("parent");
            } 
            $path = $tName . "/" . $path;
         } 
         while ($fID != 0);
      } 
      $sql->query("SELECT name, filename, description FROM $default->owl_files_table WHERE id='$id'");
      $sql->next_record();
      $name = $sql->f("name");
      $desc = $sql->f("description");
      //$desc = ereg_replace("[\\]", "", $desc);
      $desc = stripslashes($desc);
      $filename = $sql->f("filename");

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
      $mail->From = "$default->owl_email_from";
      $mail->FromName = "$default->owl_email_fromname";

      if (trim($mailto) != "")
      {
         $r = preg_split("(\;|\,)", $mailto);
         reset ($r);
         while (list ($occ, $email) = each ($r))
         {
            $mail->AddAddress($email);
         }
         $mail->CharSet = "$owl_lang->charset"; // set the email charset to the language file charset 
      } 
      else
      {
         $getuser = new Owl_DB;
         $getuser->query("SELECT id, email,language,attachfile FROM $default->owl_users_table WHERE email = '$pick_mailto'");
         $getuser->next_record();
         $DefUserLang = $getuser->f("language");
         require("$default->owl_fs_root/locale/$DefUserLang/language.inc");
         $mail->CharSet = "$owl_lang->charset"; // set the email charset to the language file charset 
         $mail->AddAddress($pick_mailto);
      } 

      if ($replyto == "")
         $mail->AddReplyTo("$default->owl_email_replyto", "OWL Intranet");
      else
         $mail->AddReplyTo("$replyto");

      if ($ccto != "")
      {
         $mail->AddCC($ccto);
      }

      $mail->WordWrap = 50; // set word wrap to 50 characters
      $mail->IsHTML(true); // set email format to HTML
      $mail->Subject = "$owl_lang->file: $name  -- $subject";
      if ($type != "url")
      {
         $mail->Body = "<html><body>" . fCleanDomTTContent($mailbody) . "<br /><br />" . "$owl_lang->description: <br /><br />$desc";
         $mail->altBody = fCleanDomTTContent($mailbody) . "\n\n" . "$owl_lang->description: \n\n $desc"; 

         if ($fileattached == 1)
         {
            $sFsPath = fCreateWaterMark($id);

            if (! $sFsPath === false)
            {
               $sAttachPath = $sFsPath;
            }
            else
            {
               if (!$default->owl_use_fs)
               {
                  $sAttachPath = fGetFileFromDatbase($id);
               } 
               else
               {
                  $sAttachPath = $default->owl_FileDir . "/" . $path . $filename;
               }
            }

            $mimeType = fGetMimeType($filename);
            $mail->AddAttachment($sAttachPath, "" , "base64" , "$mimeType");
            $mail->Body .= $owl_lang->owl_path . $disppath . "/" . $filename;
         } 
         else
         {
            $link = $default->owl_notify_link . "browse.php?sess=0&parent=" . $parent . "&expand=1&fileid=" . $id ;
            $mail->Body .= "<br /><a href=" . $link . ">" . $link . "</a><br /><br />";
            $mail->Body .= $owl_lang->owl_path . $disppath . "/" . $filename;
         } 
      } 
      else
      {
         $mail->Body = "<html><body>" . "<a href=" . $filename . ">" . $filename . "</a><br /><br />" . fCleanDomTTContent($mailbody) . "<br /><br />" . "$owl_lang->description: <br /><br />$desc <br /><br />";

         $mail->Body .= $owl_lang->owl_path . $disppath . "/" . $filename;
         $mail->altBody = "$filename" . "\n\n" . fCleanDomTTContent($mailbody) . "\n\n" . "$owl_lang->description: \n\n $desc \n\n";
         $mail->altBody .= $owl_lang->owl_path . $disppath . "/" . $filename;
      } 
      $mail->Body .= "</body></html>";

      if (!$mail->Send())
      {
         printError($owl_lang->err_email, $mail->ErrorInfo);
      } 
      if ($fileattached == 1)
      {
         owl_syslog(FILE_EMAILED, $userid, flid_to_filename($id), $parent, "TO: $mailto $pick_mailto and file was attached", "FILE");
      }
      else
      {
         owl_syslog(FILE_EMAILED, $userid, flid_to_filename($id), $parent, "TO: $mailto $pick_mailto", "FILE");
      }
   } 
   else
   {
      printError($lang_nofileaccess);
   } 
} 

if ($action == "file_monitor")
{
   //if (check_auth($id, "file_download", $userid) == 1)
   if (check_auth($id, "file_monitor", $userid) == 1)
   {
      $sql = new Owl_DB;
      $sql->query("SELECT * FROM $default->owl_monitored_file_table WHERE fid = '$id' and userid = '$userid'");

      if ($sql->num_rows($sql) == 0)
      {
         $sql->query("INSERT into $default->owl_monitored_file_table (userid, fid)  values ('$userid', '$id')");
      } 
      else
      {
         $sql->query("DELETE FROM $default->owl_monitored_file_table WHERE fid = '$id' and userid = '$userid'");
      } 
   } 
} 

if ($action == "folder_monitor")
{
   //if (check_auth($id, "folder_view", $userid) == 1)
   if (check_auth($id, "folder_monitor", $userid) == 1)
   {
      $sql = new Owl_DB;
      $sql->query("SELECT * FROM $default->owl_monitored_folder_table WHERE fid = '$id' and userid = '$userid'");

      if ($sql->num_rows($sql) == 0)
      {
         $sql->query("INSERT into $default->owl_monitored_folder_table (userid, fid)  values ('$userid', '$id')");
      } 
      else
      {
         $sql->query("DELETE FROM $default->owl_monitored_folder_table WHERE fid = '$id' and userid = '$userid'");
      } 
   } 
} 

if ($action == "folder_create")
{
   if (check_auth($parent, "folder_create", $userid) == 1)
   {
      if ($default->display_password_override == 1)
      {
         if ($newpassword <> $confpassword)
         {
            printError($owl_lang->err_pass_missmatch);
         }
         else
         {
            if (!empty($newpassword))
            {
               $newpassword = md5($newpassword);
            }
         }
      }
      else
      {
         $newpassword = "";
      }

      if ($default->folder_desc_req == "1" and trim($description) == "")
      {
         printError("$owl_lang->err_doc_field_req ", "Descrtiption");
      }

      $sql = new Owl_DB; 
      $smodified = $sql->now();
      if (empty($groupid) and fIsAdmin())
      {
      	$groupid = "0";
      }
      // This is a hack to deal with ' in the description field
      // on some system the ' is automaticaly changed to \' and that works
      // on other system it stays as ' I have no idea why
      // the 2 lines bellow should take care of any case.
      //$description = ereg_replace("[\\]'", "'", $description);
      $description = stripslashes($description);
      $description = ereg_replace("'", "\\'" , $description); 
      // we have to be careful with the name just like with the files
      // Comment this one out TRACKER : 603887, this was not done for renaming a folder
      // So lets see if it causes problems while creating folders.
      // Seems it causes a problem, so I put it back.
       
      //modif misterphi
      //$name = trim(ereg_replace("[^$default->list_of_valid_chars_in_file_names]", "", ereg_replace("%20|^-", "_", $name)));
      $name=SansAccent($name);
      //eom
      
	
      $sql->query("SELECT * FROM $default->owl_folders_table WHERE name = '$name' and parent = '$parent'");
      if ($sql->num_rows() > 0)
      {
         printError("$owl_lang->err_folder_exist");
      }

      if ($name == '')
         printError($owl_lang->err_nameempty);

      if ($default->owl_use_fs)
      {
         if (strtolower($name) == $default->version_control_backup_dir_name)
         {
            printError($owl_lang->err_specialfoldername);
         }

         $path = find_path($parent);

         if (file_exists("$default->owl_FileDir/$path/$name"))
         {
            printError($owl_lang->err_folder_exist);
         }

         mkdir($default->owl_FileDir . "/" . $path . "/" . $name, $default->directory_mask);

         if (!is_dir("$default->owl_FileDir/$path/$name"))
         {
            if ($default->debug == true)
            {
               printError("DEBUG:" . $owl_lang->err_folder_create, "$default->owl_FileDir/$path/$name");
            }
            else
            {
               printError($owl_lang->err_folder_create);
            }
         } 
      } 
      $sql->query("INSERT into $default->owl_folders_table (name,parent,security,description,groupid,creatorid, password, smodified) values ('$name', '$parent', '$policy', '$description', '$groupid', '$userid', '$newpassword', $smodified)");

      $iOldParent = $parent;
      $parent = $sql->insert_id($default->owl_folders_table, 'id');
      owl_syslog(FOLDER_CREATED, $userid, $name, $parent, $owl_lang->log_detail, "FILE");
      //$qAclInsert = new Owl_DB; 

      fSetDefaultFolderAcl($parent);
      fSetInheritedAcl($iOldParent, $parent, "FOLDER");

      if ($set_acl == 1)
      {
         header("Location: " . $default->owl_root_url . "/setacl.php?sess=$sess&expand=$expand&action=folder_acl&order=$order&sortname=$sortname&edit=1&id=" . $parent . "&parent=" . $iOldParent);
         exit;
      }
      else
      {
         displayBrowsePage($parent);
      }
   } 
   else
   {
      printError($owl_lang->err_nosubfolder);
   } 
} 

if ($action == "folder_modify")
{
   if (check_auth($id, "folder_property", $userid) == 1)
   {
      if ($default->folder_desc_req == "1" and trim($description) == "")
      {
         printError("$owl_lang->err_doc_field_req ", "Description");
      }

      $sql = new Owl_DB;
      $smodified = $sql->now();

      if (empty($groupid) and fIsAdmin())
      {
      	$groupid = "0";
      }

      if ($default->display_password_override == 1)
      {
         if ($newpassword <> $confpassword)
         {
            printError($owl_lang->err_pass_missmatch);
         }
         else
         {
            if (!empty($newpassword))
            {
               $newpassword = md5($newpassword);
            }
         }
      }
      else
      {
         $newpassword = "";
      }

      $origname = fid_to_name($id); 
      // This is a hack to deal with ' in the description field
      // on some system the ' is automaticaly changed to \' and that works
      // on other system it stays as ' I have no idea why
      // the 2 lines bellow should take care of any case.
      //$description = ereg_replace("[\\]'", "'", $description);

      $description = stripslashes($description);
      $description = ereg_replace("'", "\\'" , $description);

      $sql->query("SELECT parent FROM $default->owl_folders_table WHERE id = '$id'");
      while ($sql->next_record()) 
      {
         if ( $sql->f("parent") > 0 )
         {
            $parent = $sql->f("parent");
            $path = $default->owl_FileDir . "/" . find_path($parent) . "/";
         }
         else
         {
            $parent = 1;
            $path = $default->owl_FileDir . "/" ;
         }
      }
        
      $source = $path . $origname;
      //modif misterphi
      //$name = trim(ereg_replace("[^$default->list_of_valid_chars_in_file_names]", "", ereg_replace("%20|^-", "_", $name)));      
      $name = SansAccent($name);
      //eom

      $dest = $path . $name;

      if ($default->owl_use_fs)
      {
         if (!file_exists($path . $name) == 1 || $source == $dest)
         {
            if (substr(php_uname(), 0, 7) != "Windows")
            {
               if ($source != $dest)
               {
                  $cmd = "mv \"$path$origname\" \"$path$name\" 2>&1";
                  $lines = array();
                  $errco = 0;
                  $result = myExec($cmd, $lines, $errco);
                  if ($errco != 0)
                  {
                     printError($owl_lang->err_movecancel, $result);
                  }
               } 
            } 
            else
            { 
               // IF Windows just do a rename and hope for the best
               rename ("$path$origname", "$path$name");
            } 
         } 
         else
            printError($owl_lang->err_folderexists);
      } 
      else
      {
         if ($source != $dest)
         {
            $sql->query("SELECT * FROM $default->owl_folders_table WHERE parent = '$parent' and name = '$name'");
            if ($sql->num_rows($sql) != 0)
            {
               printError($owl_lang->err_folderexists);
            }
         } 
      } 
      /**
       * BEGIN Bozz Change
       * If your not part of the Administartor Group
       * the Folder will have your group ID assigned to it
       */
      //if (owlusergroup($userid) == 0 || owlusergroup($userid) == $default->file_admin_group || $userid == owlfoldercreator($id))
      if (fIsAdmin() || $userid == owlfoldercreator($id))
      {
         $sql->query("UPDATE $default->owl_folders_table set smodified =$smodified, name='$name', security='$policy', creatorid ='$folder_owner', description='$description' , groupid='$groupid', password = '$newpassword'  WHERE id = '$id'");
      } 
      else
      {
         $sql->query("UPDATE $default->owl_folders_table set smodified =$smodified, name='$name', security='$policy', description='$description' , password = '$newpassword' WHERE id = '$id'");
      } 

      // Changes by ianm -- allowing permissions to propagate
      if ($propagate)
      {
         change_ownership_perms($name, $id, $parent, $folder_owner, $groupid, $policy, $prop_file_sec);
      }
      // End changes by ianm

      owl_syslog(FOLDER_MODIFIED, $userid, $name, $parent, $owl_lang->log_detail, "FILE"); 
      // Bozz change End
      displayBrowsePage($parent);
   } 
   else
   {
      printError($owl_lang->err_nofoldermod);
   } 
} 

if ($action == "folder_delete")
{
   if ($id == 1) // Document Folder
   {
      printError("$owl_lang->err_root_delete");
   } 

   if (check_auth($id, "folder_delete", $userid) == 1)
   {
      $sql = new Owl_DB;
      $sql->query("SELECT id,name,parent FROM $default->owl_folders_table order by name");
      $fCount = ($sql->nf());
      $i = 0;
      while ($sql->next_record())
      {
         $folderList[$i][0] = $sql->f("id");
         $folderList[$i][2] = $sql->f("parent");
         $i++;
      } 

      if ($default->owl_use_fs)
      { 
         // This is WHERE we move the file to
         // the trash can
         if ($default->collect_trash == 1)
         {
            $path = find_path($id);
            $sTrashDir = explode('/', $path);
            $sCreatePath = $default->trash_can_location . "/" . $default->owl_current_db;
            if (!file_exists($sCreatePath))
            {
               mkdir("$sCreatePath", $default->directory_mask);
            }
            foreach($sTrashDir as $sDir)
            {
               $sDestPath = $sCreatePath;
               $sCreatePath .= "/" . $sDir;
               if (!file_exists($sCreatePath))
               {
                  mkdir("$sCreatePath", $default->directory_mask);
               } 
            } 
            if (substr(php_uname(), 0, 7) != "Windows")
            {
               $cmd = "cp -r " . '"' . $default->owl_FileDir . "/" . $path . '" "' . $sDestPath . '" 2>&1';
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
               fWindowsMoveFolders($default->owl_FileDir . "/". $path, $default->trash_can_location . "/" . $default->owl_current_db . "/" . $path);
            } 
         } 

         myDelete($default->owl_FileDir . "/" . find_path($id));
      } 

      $log_name = fid_to_name($id);
      delTree($id);
      owl_syslog(FOLDER_DELETED, $userid, $log_name, $parent, $owl_lang->log_del_det, "FILE");
      sleep(.5);
      displayBrowsePage($parent);
   } 
   else
   {
      printError($owl_lang->err_nofolderdelete);
   } 
} 

if ($action == $owl_lang->email_selected)
{
   $bIsAnyFiles = false;
   if (isset($batch))
   {
      foreach($batch as $fid)
      {
         if (check_auth($fid, "file_email", $userid) == 1)
         {
            $bIsAnyFiles = true;
         } 
      } 
   } 

   $fa = urlencode(serialize($batch));

   if ($bIsAnyFiles)
   {
      header("Location: " . $default->owl_root_url . "/modify.php?sess=$sess&expand=$expand&action=bulk_email&type=url&order=$order&sortname=$sortname&id=" . $fa . "&parent=" . $parent);
      exit();
   } 
   else
   {
      printError($owl_lang->err_no_access, $owl_lang->err_no_access_info);
   } 
} 
//***************************************************************
//  Bulk Download files and Folders
//***************************************************************
 
if ($action == "bulk_download")
{
   $tmpDir = $default->owl_tmpdir . "/owltmp.$sess";
   if (file_exists($tmpDir))
   {
      myDelete($tmpDir);
   }
   mkdir($tmpDir, $default->directory_mask);

   $bIsAnyFiles = false;
   if (isset($batch))
   {
      foreach($batch as $fid)
      {
         $oldid = $fid;
         $fid = fGetPhysicalFileId($fid);

         if (check_auth($fid, "file_download", $userid) == 1)
         {
            $path = fCreateWaterMark($fid);

            if (! $path === false)
            {
               $fspath = $path;
// Currently these files are ommited if Watermark is turned on
// as the path to those files are very different from the 
// files that are in the Documents directory.
// Will have to fix that
               $pdffilelist[] = $fspath;
            }
            else
            {
               $fspath = $default->owl_FileDir . "/" . get_dirpath(owlfileparent($fid)) . "/" .  flid_to_filename($fid);
               if ($oldid == $fid)
               {
                  $filelist[] = $fspath;
               }
               else
               {
                  $aLinkedFileList[$fid] = array($fspath => $oldid);
               }
            }
            
         }
      }
   }

   if (isset($fbatch))
   {
      foreach($fbatch as $ffid)
      {
// files here are not watermarked either.
// This could be a problem
         fGetBulkDownloadFiles($ffid);
      }
   }

   if (count($filelist) > 0 or count($aLinkedFileList) > 0 or count($pdffilelist) > 0)
   {

      if ($default->use_zip_for_folder_download)
      {
         $sSourceFolderName = find_path($parent);

         if (file_exists($tmpDir . "/" . fid_to_name($parent) . ".zip"))
         {
            unlink($tmpDir . "/" . fid_to_name($parent) . ".zip");
         }
         $archive = new PclZip($tmpDir . "/" . fid_to_name($parent) . ".zip");
         $v_list = $archive->create($filelist, PCLZIP_OPT_REMOVE_PATH, $default->owl_FileDir . "/" . $sSourceFolderName);
         if (count($pdffilelist) > 0)
         {
            $v_pdflist = $archive->add($pdffilelist, PCLZIP_OPT_REMOVE_PATH, $tmpDir);
         }
         if (count($aLinkedFileList) > 0)
         {
            foreach($aLinkedFileList as $iFileid => $sFileInfo)
            {
               foreach($sFileInfo as $sPath => $iOldid)
               { 
                  $sRemovePath = $default->owl_FileDir . "/" . get_dirpath(owlfileparent($iFileid));
                  $v_linkedFilelist = $archive->add($sPath, PCLZIP_OPT_REMOVE_PATH, $sRemovePath);
               }
            }
         }

         if ($default->debug == true)
         {
            if ($v_list == 0 and $v_linkedFilelist == 0 and $v_pdflist == 0) 
            {
               printError("DEBUG: " . $archive->errorInfo(true));
            }
         } 
      }
      else
      {
         if (file_exists($default->tar_path))
         {
            if (file_exists($default->gzip_path))
            {
               $sTarAchiveName = $tmpDir . "/" . fid_to_name(1) . ".tar";
               foreach($filelist as $file)
               { 
                  system("$default->tar_path -rf " . escapeshellarg($sTarAchiveName) . " -C " . escapeshellarg($default->owl_FileDir) . "  " .  escapeshellarg(substr_replace($file, '', 0, strlen($default->owl_FileDir) + 1)));
               }
               system($default->gzip_path . ' "' . $sTarAchiveName . '"');
            }
            else
            {
                  printError("$owl_lang->err_gzip_not_found $default->gzip_path");
            }
         }
         else
         {
            myDelete($tmpdir);
            printError("$owl_lang->err_tar_not_found $default->tar_path");
         } 
      } 

      $clean = ob_get_contents();
      ob_end_clean();
      header ("Location: download.php?sess=$sess&action=bulk_download&parent=$parent");
      print(" ");
      exit;
   }
   else
   {
      printError($owl_lang->err_no_access, $owl_lang->err_no_access_info);
   }
}


if ($action == $owl_lang->move_selected)
{
   $bIsAnyFiles = false;
   if (isset($batch))
   {
      foreach($batch as $fid)
      {
         if (check_auth($fid, "file_move", $userid) == 1)
         {
            $bIsAnyFiles = true;
         } 
      } 
   } 

   if (isset($fbatch))
   {
      foreach($fbatch as $fid)
      {
         if (check_auth($fid, "folder_move", $userid) == 1)
         {
            $bIsAnyFiles = true;
         } 
      } 
   } 

   $fa = urlencode(serialize($batch));
   $ffa = urlencode(serialize($fbatch));

   if ($bIsAnyFiles)
   {
      header("Location: " . $default->owl_root_url . "/move.php?sess=$sess&expand=$expand&action=bulk_move&type=url&order=$order&sortname=$sortname&id=" . $fa . "&folders=" . $ffa . "&parent=" . $parent);
      exit();
   } 
   else
   {
      printError($owl_lang->err_no_access, $owl_lang->err_no_access_info);
   } 
} 
// 
// Batch Delete Selected files
// 
if ($action == "bulk_checkout")
{
   $bIsAnyFiles = false;
   if (isset($batch))
   {
      foreach($batch as $fid)
      {
         if (check_auth($fid, "file_lock", $userid) == 1)
         {
            $sql->query("SELECT checked_out FROM $default->owl_files_table WHERE id = '$fid'");
                                                                                                                                                                                            
            while ($sql->next_record())
            {
               $file_lock = $sql->f("checked_out");
            }
                                                                                                                                                                                            
            if ($file_lock == 0)
            {
               // reserve the file
               $sql->query("UPDATE $default->owl_files_table set checked_out='$userid' WHERE id='$fid'");
               owl_syslog(FILE_LOCKED, $userid, flid_to_filename($fid), $parent, $owl_lang->log_detail, "FILE");
            }
            else
            {
               if ($file_lock == $userid || fIsAdmin())
               {
                  // check the file back in
                  $sql->query("UPDATE $default->owl_files_table set checked_out='0' WHERE id='$fid'");
                  owl_syslog(FILE_UNLOCKED, $userid, flid_to_filename($fid), $parent, $owl_lang->log_detail, "FILE");
               }
            }
            $bIsAnyFiles = true;
         }
      }
   }

   if (!$bIsAnyFiles)
   {
      printError($owl_lang->err_no_access, $owl_lang->err_no_access_info);
   }
}

if ($action == $owl_lang->del_selected)
{
   $bIsAnyFiles = false;
   if (isset($batch))
   {
      foreach($batch as $fid)
      {
         if (check_auth($fid, "file_delete", $userid) == 1)
         {
            delFile($fid, "Delete Selected");
            $bIsAnyFiles = true;
         } 
      } 
   } 
   if (!$bIsAnyFiles)
   {
      printError($owl_lang->err_no_access, $owl_lang->err_no_access_info);
   } 
} 
if ($action == "user")
{ 
   // the following should prevent users from changing others passwords.
   if (!isset($notify))
   {
      $notify = 0;
   }
   if (!isset($logintonewrec))
   {
      $logintonewrec = 0;
   }
   if (!isset($comment_notify))
   {
      $comment_notify = 0;
   }
   if (!isset($attachfile))
   {
      $attachfile = 0;
   }

   $sql = new Owl_DB;
   $sql->query("SELECT * FROM $default->owl_sessions_table WHERE usid = '$id' AND sessid = '$sess'");
   if ($sql->num_rows() <> 1)
   {
      printError($owl_lang->err_unauthorized);
   } 

   if ($newpassword <> '')
   {
      $sql = new Owl_DB;
      $sql->query("SELECT * FROM $default->owl_users_table WHERE id = '$id' AND password = '" . md5(stripslashes($oldpassword)) . "'");
      if ($sql->num_rows() == 0)
      {
         printError($owl_lang->err_pass_wrong);
      }
      if ($newpassword == $confpassword)
      {
         if (!fbValidPassword($newpassword))
         {
            $sMsg .= $owl_lang->err_pass_restriction_1;
            $sMsg .= $owl_lang->err_pass_restriction_2;
            $sMsg .= $owl_lang->err_pass_restriction_3;
            printError($sMsg);
         }

         if (fbCheckForPasswdReuse($newpassword, $id) === true)
         {
            printError("CANT RE USE PASSWORS");
         }
         $dNow = $sql->now();
         $sql->query("UPDATE $default->owl_users_table SET  passwd_last_changed = $dNow, name='$name',password='" . md5("$newpassword") . "' WHERE id = '$id'");
      }
      else
      {
         printError($owl_lang->err_pass_missmatch);
      }
   } 
   else
   {
      if ($oldpassword <> '')
      {
         printError($owl_lang->err_pass_restriction_1);
      }
   }


   if (trim($email) == "")
   {
      printError($owl_lang->err_email_required);
   } 

   $sql->query("UPDATE $default->owl_users_table SET name='$name', buttonstyle='$newbuttons', email='$email', notify='$notify', attachfile='$attachfile', language='$newlanguage', comment_notify = '$comment_notify', logintonewrec='$logintonewrec' WHERE id = '$id'");
} 

if ($action == "bulk_email")
{
   $aFileid = array();
   $aFileid = unserialize(stripslashes(stripslashes($id)));

   $sql = new Owl_DB;
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

   if (trim($mailto) != "")
   {
      $r = preg_split("(\;|\,)", $mailto);
      reset ($r);
      while (list ($occ, $email) = each ($r))
      {
         $mail->AddAddress($email);
      }
      $mail->CharSet = "$owl_lang->charset"; // set the email charset to the language file charset
   }
   else
   {
      $getuser = new Owl_DB;
      $getuser->query("SELECT id, email,language,attachfile FROM $default->owl_users_table WHERE email = '$pick_mailto'");
      $getuser->next_record();
      $DefUserLang = $getuser->f("language");
      require("$default->owl_fs_root/locale/$DefUserLang/language.inc");
      $mail->CharSet = "$owl_lang->charset"; // set the email charset to the language file charset
      $mail->AddAddress($pick_mailto);
   }

   if ($replyto == "")
   {
      $mail->AddReplyTo("$default->owl_email_replyto", "OWL Intranet");
   }
   else
   {
      $mail->AddReplyTo("$replyto");
   }

   if ($ccto != "")
   {
      $mail->AddCC("$ccto");
   }

   $mail->WordWrap = 50; // set word wrap to 50 characters
   $mail->IsHTML(true); // set email format to HTML
   $mail->Subject = "$default->owl_email_subject -- $subject -- ";
   $mail->Body = "<html><body>" . "$mailbody" . "<br /><br />";

   foreach($aFileid as $fileid)
   {
      $sql->query("select name, parent FROM $default->owl_files_table WHERE id='$fileid'");
      $sql->next_record();
      $name = $sql->f("name");
      $parent = $sql->f("parent");

      if (check_auth($fileid, "file_email", $userid) == 1)
      {
         $path = "";
         $disppath = find_path($parent);
         $filename = flid_to_filename($fileid);
         $sql->query("SELECT url FROM $default->owl_files_table WHERE id='$fileid'");
         $sql->next_record();
         if ($sql->f("url") == 1)
         {
            $type = "url";
         } 
         else
         {
            $type = "";
         } 

         if ($default->owl_use_fs)
         {
            $fID = $parent;
            do
            {
               $sql->query("SELECT name,parent FROM $default->owl_folders_table WHERE id='$fID'");
               while ($sql->next_record())
               {
                  $tName = $sql->f("name");
                  $fID = $sql->f("parent");
               } 
               $path = $tName . "/" . $path;
            } 
            while ($fID != 0);
         } 
         $sql->query("SELECT name, filename, description FROM $default->owl_files_table WHERE id='$fileid'");
         $sql->next_record();
         $name = $sql->f("name");
         $desc = $sql->f("description");
         //$desc = ereg_replace("[\\]", "", $desc);
         $desc = stripslashes($desc);
         $filename = $sql->f("filename");

         if ($type != "url")
         {
            $mail->Body .= "$owl_lang->description: <br />$desc<br /><br />";
            $mail->altBody .= "$owl_lang->description: \n $desc \n\n"; 


            // BEGIN wes change
            if ($fileattached == 1)
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
                        $sAttachPath = "$default->owl_FileDir/$path$filename";
                     }
                  }


               $mimeType = fGetMimeType($filename);
               $mail->AddAttachment($sAttachPath, "" , "base64" , "$mimeType"); 
               $mail->Body .= $owl_lang->owl_path . $disppath . "/" . $filename . "<br /><br />";
            } 
            else
            {
               $link = $default->owl_notify_link . "browse.php?sess=0&parent=" . $parent . "&expand=1&fileid=" . $fileid ;
               $mail->Body .= "<a href=" . $link . ">" . $filename . "</a><br /><br />";
               $mail->Body .= $owl_lang->owl_path . $disppath . "/" . $filename . "<br /><br />";
            } 
         } 
         else
         {
            $mail->Body .= "<a href=" . $filename . ">" . $filename . ": </a><br /><br />" . "$mailbody" . "<br /><br />" . "$owl_lang->description: <br /><br />$desc<br /><br />";
            $mail->Body .= $owl_lang->owl_path . $path . "/" . $filename . "<br /><br />";
            $mail->altBody .= "$filename" . "\n\n" . "$mailbody" . "\n\n" . "$owl_lang->description: \n\n $desc\n\n";
            $mail->altBody .= $owl_lang->owl_path . $path . "/" . $filename . "\n\n" ;
         } 
      } 
   } 
   $mail->Body .= "</body></html>";
   if (!$mail->Send())
   {
      if ($default->debug == true)
      {
         printError("DEBUG: $owl_lang->err_email", $mail->ErrorInfo);
      } 
   } 
   foreach($aFileid as $fileid)
   {
      if ($fileattached == 1)
      {
         owl_syslog(FILE_EMAILED, $userid, flid_to_filename($fileid), $parent, "TO: $mailto and file was attached", "FILE");
      }
      else
      {
         owl_syslog(FILE_EMAILED, $userid, flid_to_filename($fileid), $parent, "TO: $mailto", "FILE");
      }

      if (!$default->owl_use_fs)
      {
         $path = "";
         $filename = flid_to_filename($fileid);
         $sql->query("SELECT url FROM $default->owl_files_table WHERE id='$fileid'");
         $sql->next_record();
         if ($sql->f("url") == 1)
         {
            $type = "url";
         } 
         else
         {
            $type = "";
         } 

         if ($default->owl_use_fs)
         {
            $fID = $parent;
            do
            {
               $sql->query("SELECT name,parent FROM $default->owl_folders_table WHERE id='$fID'");
               while ($sql->next_record())
               {
                  $tName = $sql->f("name");
                  $fID = $sql->f("parent");
               } 
               $path = $tName . "/" . $path;
            } 
            while ($fID != 0);
         } 

         if (file_exists("$default->owl_FileDir/$path$filename"))
         {
            unlink("$default->owl_FileDir/$path$filename");
         } 
      } 
   } 
} 

displayBrowsePage($parent);
?>
