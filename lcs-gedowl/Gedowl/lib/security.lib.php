<?php

/*

  File: security.lib.php
  Author: Chris
  Date: 2000/12/14

  Owl: Copyright Chris Vincent <cvincent@project802.net>

  You should have received a copy of the GNU Public
  License along with this package; if not, write to the
  Free Software Foundation, Inc., 59 Temple Place - Suite 330,
  Boston, MA 02111-1307, USA.

*/
function getfolderpolicy($id) {
        global $default, $cCommonDBConnection;

        $sql = $cCommonDBConnection;

        if (empty($sql))
        {
           $sql = new Owl_DB;
        }
        $sql->query("select security from $default->owl_folders_table where id = '$id'");
        while ($sql->next_record()) return $sql->f("security");
}
                                                                                                                                                                                             
function getfilepolicy($id) {
        global $default, $cCommonDBConnection;

        $sql = $cCommonDBConnection;

        if (empty($sql))
        {
           $sql = new Owl_DB;
        }

        $sql->query("select security from $default->owl_files_table where id = '$id'");
        while ($sql->next_record()) return $sql->f("security");
}

function fIsInBrokenTree($folder_id)
{
   global $default, $userid, $bIsInBrokenTree, $aFoldersChecked, $aFoldersParentChecked;

   if (empty($aFoldersParentChecked[$folder_id]))
   {
      //$iParentFolderId = owlfolderparent($folder_id);
      //$aFoldersParentChecked[$folder_id] = $iParentFolderId;
      $iParentFolderId = $folder_id;
      $aFoldersParentChecked[$folder_id] = $folder_id;
      //print("<pre>");
      //print_r($aFoldersParentChecked);
      //print("</pre>");
   }
   else
   {
      $iParentFolderId = $aFoldersParentChecked[$folder_id];
   }

  // print("<br />STEVE DEBUG: P: $iParentFolderId <br />"); 

   if ($bIsInBrokenTree === false and $iParentFolderId > 1)
   {
      if ($aFoldersChecked[$iParentFolderId] == 0 or empty($aFoldersChecked[$iParentFolderId]))
      {
         if (check_auth($iParentFolderId, "folder_view", $userid, false, false) == "1")
         {
            $aFoldersChecked[$iParentFolderId] = 1;
      //print("<pre>");
      //print_r($aFoldersChecked);
      //print("</pre>");
            $bIsInBrokenTree = false;
            fIsInBrokenTree($iParentFolderId);
         }
         else
         {
            $bIsInBrokenTree = true;
         }
      }
   }
}

function fSetDefaultFolderAcl ( $iFolderID )
{
   global $default, $usergroupid, $cCommonDBConnection;

   $sql = $cCommonDBConnection;

   if (empty($sql))
   {
      $sql = new Owl_DB;
   }

   if ($default->advanced_security == 1 )
   {
      // BEGIN  Set Default Acls for this Folder
      if(!empty($default->folder_security))
      {
         foreach($default->folder_security[$default->owl_current_db] as $aFolderAcl)
         {
            $qItems = "";
            $qValues = "('$iFolderID'";
            $bUseFolderGroupForACL = false;
            if ($aFolderAcl[group_id] == "" and $aFolderAcl[user_id] == "")
            {
               $bUseFolderGroupForACL = true;
            }
            foreach($aFolderAcl as $sAcl => $values)
            {
               if (($sAcl == "user_id" and $values == "") or ($sAcl == "group_id" and $values == "" and $bUseFolderGroupForACL === false))
               {
                 continue;
               }
                                                                                                                                                                                    
               $qItems .= ", " .$sAcl;
               if($bUseFolderGroupForACL === true)
               {
                  $qValues .=  ", '" . $usergroupid . "'";
                  $bUseFolderGroupForACL = false;
               }
               else
               {
                  $qValues .=  ", '" . $values . "'";
               }
            }
            $qItems .= ")";
            $qValues .= ")";
                                                                                                                                                                                    
            $qSetAcl = "INSERT INTO $default->owl_advanced_acl_table (folder_id" . $qItems . " VALUES " . $qValues;
            //print ( "INSERT INTO $default->owl_advanced_acl_table (folder_id" . $qItems . " VALUES " . $qValues . "<br />");
            $sql->query($qSetAcl);
         }
      }
      // END  Set Default Acls for this Folder
   }
}


function fSetDefaultFileAcl ( $iFileID )
{
   global $default, $usergroupid, $cCommonDBConnection;

   $sql = $cCommonDBConnection;



   if ($default->advanced_security == 1 )
   {
      if (empty($sql))
      {
         $sql = new Owl_DB;
      }
      // BEGIN  Set Default Acls for this File
      if(!empty($default->file_security))
      {
         foreach($default->file_security[$default->owl_current_db] as $aFileAcl)
         {
            $qItems = "";
            $qValues = "('$iFileID'";
            $bUseFileGroupForACL = false;
            if ($aFileAcl[group_id] == "" and $aFileAcl[user_id] == "")
            {
               $bUseFileGroupForACL = true;
            }
            foreach($aFileAcl as $sAcl => $values)
            {
               if (($sAcl == "user_id" and $values == "") or ($sAcl == "group_id" and $values == "" and $bUseFileGroupForACL === false))
               {
                 continue;
               }
                                                                                                                                                                                    
               $qItems .= ", " .$sAcl;
               if($bUseFileGroupForACL === true)
               {
                  $qValues .=  ", '" . $usergroupid . "'";
                  $bUseFileGroupForACL = false;
               }
               else
               {
                  $qValues .=  ", '" . $values . "'";
               }
            }
            $qItems .= ")";
            $qValues .= ")";
                                                                                                                                                                                    
            $qSetAcl = "INSERT INTO $default->owl_advanced_acl_table (file_id" . $qItems . " VALUES " . $qValues;
            //print ( "INSERT INTO $default->owl_advanced_acl_table (file_id" . $qItems . " VALUES " . $qValues . "<br />");
            $sql->query($qSetAcl);
         }
      }
      // END  Set Default Acls for this File
   }
}

if ( $default->advanced_security == 1 )
{
   require_once($default->owl_fs_root ."/lib/acl_security.lib.php");
}
else
{
   require_once($default->owl_fs_root ."/lib/def_security.lib.php");
}


if ($default->owl_maintenance_mode)
{
   if(!fIsAdmin(true))
   {
      printError("SYSTEM IS NOW DOWN FOR MAINTENANCE");
   }
}
?>
