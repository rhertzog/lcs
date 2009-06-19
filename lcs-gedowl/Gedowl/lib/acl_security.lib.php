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


define('EVERYONE', '0');
define('PERMIT', '1');
define('DENY', '0');
//
// This function is simple...it returns either a 1 or 0
// If the authentication is good, it returns 1
// If the authentication is bad, it returns 0
//

function check_auth($id, $action, $checkuserid, $report = false, $recursive = true) 
{
   global $default;
   global $owl_lang;
   global $usergroupid, $userid;

   if ($userid == $checkuserid)
   {
      $usergroup = $usergroupid;
   }
   else
   {
      $usergroup = owlusergroup($checkuserid);
   }


   //print(" FUNCTION CALLED: $action <br />");

	//$filecreator = owlfilecreator($id);
	//$foldercreator = owlfoldercreator($id);

    $bCheckFolder = false;
    list($type, $sub) = split("_", $action);
    if ($type == "folder")
    {
       $bCheckFolder = true;
    }

    switch ($action)
        {
           case "folder_delete":
           case "file_delete":
              $acl = "owldelete";
              break;
           case "folder_property":
           case "file_property":
              $acl = "owlproperties";
              break;
           case "folder_cp":
           case "file_cp":
              $acl = "owlcopy";
              break;
           case "folder_move":
           case "file_move":
           case "file_lnk":
              $acl = "owlmove";
              break;
           case "folder_acl":
              $acl = "owlsetacl";
              $sAclType = "SETTING_FILE_ACL";
              break;
           case "file_acl":
              $acl = "owlsetacl";
              $sAclType = "SETTING_FOLDER_ACL";
              break;
           case "folder_view":
           case "file_download":
              $acl = "owlread";
              break;
           case "folder_create":
              $acl = "owlwrite";
              break;
           case "folder_monitor":
           case "file_monitor":
              $acl = "owlmonitor";
              break;
           case "file_comment":
              $acl = "owlcomment";
              break;
           case "file_update":
              $acl = "owlupdate";
              break;
           case "file_log":
              $acl = "owlviewlog";
              break;
           case "file_lock":
              $acl = "owlcheckin";
              break;
           case "file_email":
              $acl = "owlemail";
              break;
           case "file_all":
              $acl = "file_all";
              break;
           default:
              $acl = "";
              break;
        }

/* $aAclList[] = "owlread";
$aAclList[] = "owlwrite";
$aAclList[] = "owlviewlog";
$aAclList[] = "owldelete";
$aAclList[] = "owlcopy";
$aAclList[] = "owlmove";
$aAclList[] = "owlproperties";
$aAclList[] = "owlupdate";
$aAclList[] = "owlcomment";
$aAclList[] = "owlcheckin";
$aAclList[] = "owlemail";
$aAclList[] = "owlrelsearch";
$aAclList[] = "owlsetacl";
$aAclList[] = "owlmonitor";
*/

   if ($bCheckFolder and empty($sAclType))
   {
      $sAclType = "FOLDER";
   }

   if (empty($sAclType))
   {
      $sAclType = "FILE";
   }

   $groups = fGetGroups($checkuserid, $sAclType, $id);

   if ($acl == "file_all")
   {
      return fGetAllAclChecked($id, $groups );
   }

   if ($bCheckFolder)
   {
      if(owlfoldercreator($id) == $checkuserid)
      {
         return PERMIT;
      }

      $foldergroup = owlfoldergroup($id);

      if (fIsGroupAdmin($checkuserid, $foldergroup))
      {
        return PERMIT;
      } 


      //foreach($groups as $g)
      //{
         //$result = fGetFAclChecked($id, $g[0], $acl, "group");
         $result = fGetFAclChecked($id, $groups, $acl, "group");

         if(!empty($result))
         {
           return PERMIT;
         }
      //}

      $result = fGetFAclChecked($id, $checkuserid, $acl, "user");
      if(!empty($result))
      {
         return PERMIT;
      }
      $result = fGetFAclChecked($id, EVERYONE, $acl, "user");
      if(!empty($result))
      {
         return PERMIT;
      }
   }
   else
   {
      if(owlfilecreator($id) == $checkuserid)
      {
         return PERMIT;
      }

      $filegroup = owlfilegroup($id);
      if (fIsGroupAdmin($checkuserid, $filegroup))
      {
         return PERMIT;
      } 
      //foreach($groups as $g)
      //{
         //$result = fGetAclChecked($id, $g[0], $acl, "group");
         $result = fGetAclChecked($id, $groups, $acl, "group");

         if(!empty($result))
         {
           return PERMIT;
         }
      //}
      $result = fGetAclChecked($id, $checkuserid, $acl, "user");
      if(!empty($result))
      {
         return PERMIT;
      }
      $result = fGetAclChecked($id, EVERYONE, $acl, "user");
      if(!empty($result))
      {
         return PERMIT;
      }
   }
  
   if (fIsAdmin()) 
   {
      if( !$report )
      {
         return PERMIT;
      }
   }
   //print("DEBUG: ID: $id UID: $userid ACL: $acl <br />");
   return DENY;
}
?>
