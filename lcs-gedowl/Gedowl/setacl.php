<?php

/**
 * setacl.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 * $Id: setacl.php,v 1.10 2007/05/02 22:20:36 b0zz Exp $
 */

require_once(dirname(__FILE__)."/config/owl.php");
require_once($default->owl_fs_root ."/lib/disp.lib.php");
require_once($default->owl_fs_root ."/lib/owl.lib.php");
require_once($default->owl_fs_root ."/lib/security.lib.php");
require_once($default->owl_fs_root ."/phpid3v2/class.id3.php");

if ($action == "file_acl")
{
   $sAclType = "SETTING_FILE_ACL";
}
else
{
   $sAclType = "SETTING_FOLDER_ACL";
}
$groups = fGetGroups($userid, $sAclType, $id);

$groups[-1][0] = "-1";
$groups[-1][1] = "None";

//modif misterphi
$aUserList = fGetUserInfoInMyGroups($userid, "disabled = '0'", false, $sAclType, $id);
/*$aUserList1 = array(); 
$aUserList2 = array(); 
$aUserList1 = fGetUserInfoInMyGroups($userid, "disabled = '0'", false, $sAclType, $id);
$aUserList2 = fGetUserInfoInMyGroups($userid, "disabled = '0'", true, $sAclType, $id);
echo count($aUserList1);
echo $aUserList1[1][username]."<br>";
echo $aUserList1[2][username]."<br>";
echo $aUserList1[3][username]."<br>";
echo count($aUserList2);
echo $aUserList2[1][username]."<br>";
echo $aUserList2[2][username]."<br>";
echo $aUserList2[3][username]."<br>";
exit;*/
//$aUserList = array_merge($aUserList1,$aUserList2);
//eom
//$aUserList = $aUserList2;
$aUserList[0][username] = "everybody";
$aUserList[0][name] = "EVERYBODY";
$aUserList[0][email] = "";
$aUserList[0][id] = "0";

if($edit == 1)
{
   //if (($action == "file_acl" and fIsFileCreator($id)) or ($action == "file_acl" and fisAdmin()))
   if ($action == "file_acl" and check_auth($id, "file_acl", $userid) == 1)
   {
      $selectedgroups = array(); 
      $qSetAcl = "SELECT * FROM $default->owl_advanced_acl_table where file_id = '$id'";
      $sql = new Owl_DB;
      $sql->query($qSetAcl);
      while ($sql->next_record())
      {
         if($sql->f("group_id") == null )
         {
            $selectedusers[] = $sql->f("user_id");
         }
         else
         {
            $selectedgroups[] = $sql->f("group_id");
         }
      }
 
//===

foreach ($selectedusers as $val)
         {
            if($val == -1)
            {
               continue;
            }
			
			$quser = "SELECT * FROM $default->owl_users_table where 1";
			$qGetuser = new Owl_DB;
//echo $val;
			$qGetuser->query($quser);
			while ($qGetuser->next_record())
      		{
			$sId = $qGetuser->f("id");
        	$sName = $qGetuser->f("name");
			}
			
			}

//====
}
   //elseif (($action == "folder_acl" and fIsFolderCreator($id)) or ($action == "folder_acl" and fisAdmin()))
   elseif ($action == "folder_acl" and check_auth($id, "folder_acl", $userid) == 1)
   {
      $fselectedgroups = array(); 
      $qSetAcl = "SELECT * FROM $default->owl_advanced_acl_table where folder_id = '$id'";
      $sql = new Owl_DB;
      $sql->query($qSetAcl);
      while ($sql->next_record())
      {
         if($sql->f("group_id") == null )
         {
            $fselectedusers[] = $sql->f("user_id");
         }
         else
         {
            $fselectedgroups[] = $sql->f("group_id");
         }
      }
   }
   else
   {
      include_once($default->owl_fs_root ."/lib/header.inc");
      include_once($default->owl_fs_root ."/lib/userheader.inc");
      printError($owl_lang->err_nofilemod);
   }
}

include_once($default->owl_fs_root ."/lib/header.inc");
include_once($default->owl_fs_root ."/lib/userheader.inc");
?>
<script type="text/javascript">
var IE = document.all?true:false
if (!IE) document.captureEvents(Event.MOUSEMOVE)
document.onmousemove = getMouseY;

function getMouseY(e) {
  if (IE) { // grab the x-y pos.s if browser is IE
    tempY = event.clientY + document.body.scrollTop
  } else {  // grab the x-y pos.s if browser is NS
    tempY = e.pageY
  }
  return true;
}

</script>
<?php
printModifyHeader();
//print("<pre>");
//print_r($selectedusers);
//print("==== ACTION: $action =========================================================================");
//print_r($selectedgroups);
//print("</pre>");

if ($sess == "0" && $default->anon_ro > 0)
{
   printError($owl_lang->err_login);
}

if(!isset($type))
{
   $type = "";
}

// V4B RNG Start
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
// V4B RNG End

if ($action == "folder_acl")
{
   if (check_auth($id, "folder_acl", $userid) == 1)
   {
      $urlArgs2 = $urlArgs;
      $urlArgs2['expand'] = $expand;
      $urlArgs2['id']     = $id;
      $urlArgs2['action']  = "folder_acl";

      if($edit == 1)
      {
         fPrintNavBar($id,$owl_lang->acl_edit_folder);
      }
      else
      {
         fPrintNavBar($id,$owl_lang->acl_adding_folder);
      }


      print("<form action=\"setacl.php\" method=\"post\" name=\"fcombo_box\">\n");
      print fGetHiddenFields ($urlArgs2);

      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"center\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      fPrintSectionHeader($owl_lang->acl_heading_folders);
      print("</table>");
      print("</td></tr></table><br />");
if ($default->show_users_in_group == true)
{
   ?>
<script type="text/javascript">
//Function alerts the index of the selected option within form

var ftooltipContentData = [

<?php

   $qGetUserMember = new Owl_DB;

   //$groups[] = array(0 => '13', 1 => 'RVC');
   //$groups = array();
   //$groups[0][0] = "13";
   //$groups[0][1] = "RVC";

   //print("<pre>");
   //print_r($groups);
   //print("</pre>");
   //exit;

   
   foreach($groups as $g)
   {
      if ($g[0] == -1)
      {
         continue;
      }

      if (!empty($fselectedgroups))
      {
         if (!(in_array($g[0], $fselectedgroups)))
         {
            $qSetAcl = "SELECT distinct id,name, username FROM $default->owl_users_table u left join $default->owl_users_grpmem_table m on u.id = m.userid where u.groupid = '$g[0]' or m.groupid = '$g[0]'";
            //$qSetAcl = "SELECT name, username FROM $default->owl_users_table where groupid = '$g[0]'";
            $qGetUserMember->query($qSetAcl);
            print("'<table>");
            if ($qGetUserMember->num_rows() == 0)
            {
               print("<tr><td>None</td></tr>");
            }
            while($qGetUserMember->next_record())
            {
               print("<tr><td>" . $qGetUserMember->f('name') . "</td><td>(" . $qGetUserMember->f('username') . ")</td></tr>");
            }
            print("</table>',\n");
         }
      }
      else
      {
         //$qSetAcl = "SELECT name, username FROM $default->owl_users_table where groupid = '$g[0]'";
         $qSetAcl = "SELECT distinct id,name, username FROM $default->owl_users_table u left join $default->owl_users_grpmem_table m on u.id = m.userid where u.groupid = '$g[0]' or m.groupid = '$g[0]'";
         $qGetUserMember->query($qSetAcl);
         print("'<table>");
	 if ($qGetUserMember->num_rows() == 0)
	 {
	    print("<tr><td>None</td></tr>");
	 }
         while($qGetUserMember->next_record())
         {
            print("<tr><td>" . $qGetUserMember->f('name') . "</td><td>(" . $qGetUserMember->f('username') . ")</td></tr>");
         }
         print("</table>',\n");
      }

   }
   print ("''\n");

	//'No content yet.  Click one of the links below.',
	//'What would you like to do today?',
	//'Implement tooltips on your site?  <b>Excellent!</b>',
	//'You should definitely consider using domTT.'
?>
];

var ftooltipCaptionData = [

<?php

   $qGetUserMember = new Owl_DB;

   foreach($groups as $g)
   {
      if ($g[0] == -1)
      {
         continue;
      }

      if (!empty($fselectedgroups))
      {
         if (!(in_array($g[0], $fselectedgroups)))
         {
            print("'Users in: $g[1]',");
         }
      }
      else
      {
         print("'Users in: $g[1]',");
      }

   }
   print ("''\n");

?>
];


var ftooltipContentIndex = 0;
function fgetTooltipContent() {
	return ftooltipContentData[ftooltipContentIndex];
}
function fgetTooltipCaption() {
	return ftooltipCaptionData[ftooltipContentIndex];
}

function fgetselectedvalue(gbox){
return(gbox.selectedIndex)
}
</script>
<?php
}

      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"center\" valign=\"top\">");
      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n");
      print("<tr>\n");
      print("<td align=\"center\" valign=\"top\">\n");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n");
      print("<tr>\n");
      print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">$owl_lang->acl_available_groups</td>\n");
      print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">&nbsp;</td>\n");
      print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">$owl_lang->acl_selected_groups</td>\n");
      //print("<td class=\"admin3\">&nbsp;</td>\n");
      print("</tr>\n");
      print("<tr>\n");
      print("<td class=\"form1\">\n");
      print("<select multiple=\"multiple\" size=\"10\" name=\"fallgroups[]\" class=\"fpullacl\"");
   //    print (" onchange=\"tooltipContentIndex = getselectedvalue(this); domTT_activate(this, event, 'content', getTooltipContent(), 'lifetime', 5000, 'closeAction', 'destroy', 'styleClass', 'niceTitle', 'x', 50, 'y', 50);\"");
if ($default->show_users_in_group == true)
{
   print (" onchange=\"ftooltipContentIndex = fgetselectedvalue(this); domTT_activate(this, event, 'content', fgetTooltipContent(), 'type', 'sticky', 'closeLink', '&nbsp; [x] &nbsp;', 'draggable', true, 'closeAction', 'destroy',  'caption' , fgetTooltipCaption(), 'x', 50, 'y', tempY);\"");
}
      print(">\n");
      foreach($groups as $g)
      {
         if ($g[0] == -1)
         {
            continue;
         }
         if (!empty($fselectedgroups))
         {
            if (!(in_array($g[0], $fselectedgroups)))
            {
               print("<option value=\"$g[0]\"");
               print(">$g[1]</option>\n");
               
            }
         }
         else
         {
            print("<option value=\"$g[0]\"");
            print(">$g[1]</option>\n");
         }
      }
      print("</select>\n</td>\n");

      print("<td class=\"form1\" nowrap=\"nowrap\">");
      print("&nbsp;&nbsp;<input type=\"button\" onclick=\"move(this.form.elements['fselectedgroups[]'],this.form.elements['fallgroups[]'])\" value=\"<--\"></input>\n");
      print("<input type=\"button\" onclick=\"move(this.form.elements['fallgroups[]'],this.form.elements['fselectedgroups[]'])\" value=\"-->\"></input>\n");
      print("<br />");
      print("<input type=\"button\" onclick=\"selectAllGroups(this.form.elements['fselectedgroups[]'],this.form.elements['fallgroups[]'])\" value=\"<<--\"></input>\n");
      print("<input type=\"button\" onclick=\"selectAllGroups(this.form.elements['fallgroups[]'],this.form.elements['fselectedgroups[]'])\" value=\"-->>\"></input>\n");
      print("</td>\n");
      print("<td class=\"form1\">");
      print("<select multiple=\"multiple\" size=\"10\" name=\"fselectedgroups[]\" class=\"fpullacl\">");
      if (!empty($groups))
      {
         foreach($groups as $g)
         {
            if ($g[0] == -1)
            {
               continue;
            }
            if (!empty($fselectedgroups))
            {
               if ((in_array($g[0], $fselectedgroups)))
               {
               
                  print("<option value=\"$g[0]\"");
                  print(">$g[1]</option>\n");
               }
            }
         }
      }
      print("</select>\n</td>\n");
      print("</tr>\n");
      print("<tr>\n");
      print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">$owl_lang->acl_available_users</td>\n");
      print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">&nbsp;</td>\n");
      print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">$owl_lang->acl_selected_users</td>\n");
      print("</tr>\n");
      print("<tr>\n");
      print("<td class=\"form1\">\n");
      print("<select multiple=\"multiple\" size=\"10\" name=\"fallusers[]\" class=\"fpullacl\"");



      print(">\n");
      /*
      if (!empty($aUserList))
      {
         foreach ($aUserList as $aUsers)
         {
            $sUsername = $aUsers["username"];
            $sId = $aUsers["id"];
            $sName = $aUsers["name"];
            if(!empty($aUsers["email"]))
            {
               $sEmail = " (" . $aUsers["email"] . ")";
            }
            else
            {
               $sEmail = "";
            }
            if (!empty($fselectedusers))
            {
               if (!(in_array($sId, $fselectedusers)))
               {
                  print("<option value=\"$sId\"");
                  print(">" . $sName .  $sEmail . "</option>\n");
               }
            }
            else
            {
               print("<option value=\"$sId\"");
               print(">" . $sName . $sEmail . "</option>\n");
            }
         }
      }*/
      
 //modif misterphi ==========================================================
 foreach($groups as $g)
      {
         if ($g[0] == -1)
         {
            continue;
         }
         if (!empty($fselectedgroups))
         {
            if (!(in_array($g[0], $fselectedgroups)))
            {
                        
               print("<optgroup label=\"$g[1]\">");
               //affichage des users du groupe
               
               $qSetAcl = "SELECT distinct id,name, username FROM $default->owl_users_table u left join $default->owl_users_grpmem_table m on u.id = m.userid where u.groupid = '$g[0]' or m.groupid = '$g[0]'";
            //$qSetAcl = "SELECT name, username FROM $default->owl_users_table where groupid = '$g[0]'";
            $qGetUserMember->query($qSetAcl);
           
            if ($qGetUserMember->num_rows() == 0)
            {
               //print("<tr><td>None</td></tr>");
            }
            while($qGetUserMember->next_record())
            {
               //print("<tr><td>" . $qGetUserMember->f('name') . "</td><td>(" . $qGetUserMember->f('username') . ")</td></tr>");
                $sonid=$qGetUserMember->f('id');
                print("<option value=\"$sonid\"");
            	print(">".$qGetUserMember->f('name')."</option>\n");
            }
                      
               print("</optgroup>\n");
               
            }
         }
         else
         {
           
           if (!(in_array($g[0], $fselectedgroups)))
            {
                        
               print("<optgroup label=\"$g[1]\">");
               //affichage des users du groupe
               
               $qSetAcl = "SELECT distinct id,name, username FROM $default->owl_users_table u left join $default->owl_users_grpmem_table m on u.id = m.userid where u.groupid = '$g[0]' or m.groupid = '$g[0]'";
            //$qSetAcl = "SELECT name, username FROM $default->owl_users_table where groupid = '$g[0]'";
            $qGetUserMember->query($qSetAcl);
           
            if ($qGetUserMember->num_rows() == 0)
            {
               //print("<tr><td>None</td></tr>");
            }
            while($qGetUserMember->next_record())
            {
               //print("<tr><td>" . $qGetUserMember->f('name') . "</td><td>(" . $qGetUserMember->f('username') . ")</td></tr>");
                $sonid=$qGetUserMember->f('id');
                print("<option value=\"$sonid\"");
            	print(">".$qGetUserMember->f('name')."</option>\n");
            }
                      
               print("</optgroup>\n");
               
            }
           
            /*print("<option value=\"$g[0]\"");
            print(">$g[1]</option>\n");*/
         }
      }
     
      print("</select>\n</td>\n");
 
//eom misterphi  ====================================================================================    
      
      
      print("</select>\n");
      print("</td>\n");
      print("<td class=\"form1\" nowrap=\"nowrap\">\n");
      //modif misterphi
      //print("&nbsp;&nbsp;<input type=\"button\" onclick=\"move(this.form.elements['fselectedusers[]'],this.form.elements['fallusers[]'])\" value=\"<--\"></input>\n");
      print("&nbsp;&nbsp;<input type=\"button\" onclick=\"del(this.form.elements['fselectedusers[]'])\" value=\"<--\"></input>\n");
      print("<input type=\"button\" onclick=\"add(this.form.elements['fallusers[]'],this.form.elements['fselectedusers[]'])\" value=\"-->\"></input>\n");
     
      print("<br />");
      //print("<input type=\"button\" onclick=\"selectAllUsers(this.form.elements['fselectedusers[]'],this.form.elements['fallusers[]'])\" value=\"<<--\"></input>\n");
      //print("<input type=\"button\" onclick=\"selectAllUsers(this.form.elements['fallusers[]'],this.form.elements['fselectedusers[]'])\" value=\"-->>\"></input>\n");
      //eom
      print("</td>\n");
      print("<td class=\"form1\">\n");
      print("<select multiple=\"multiple\" size=\"10\" name=\"fselectedusers[]\" class=\"fpullacl\">\n");

 //modif misterphi =========================================================
foreach ($fselectedusers as $val)
         {
            if($val == -1)
            {
               continue;
            }
			
			$quser = "SELECT id, name, username FROM $default->owl_users_table where id = '$val'";
			$qGetuser = new Owl_DB;
		
			$qGetuser->query($quser);
			while ($qGetuser->next_record())
      		{
			$sId = $qGetuser->f("id");
        	$sName = $qGetuser->f("name");
			$sUsername = $qGetuser->f("username");
			}
			if (!empty($fselectedusers))
            {
               if ((in_array($sId, $fselectedusers)))
               {
                  print("<option value=\"$sId\"");
                 
                  print(">" . $sName ." - ".$sUsername  . "</option>\n");
                  
               }
            }
			}  


/*      if (!empty($aUserList))
      {
         foreach ($aUserList as $aUsers)
         {
            $sUsername = $aUsers["username"];
            $sId = $aUsers["id"];
            $sName = $aUsers["name"];
            if(!empty($aUsers["email"]))
            {
               $sEmail = " (" . $aUsers["email"] . ")";
            }
            else
            {
               $sEmail = "";
            }
            if (!empty($fselectedusers))
            {
               if ((in_array($sId, $fselectedusers)))
               {
                  print("<option value=\"$sId\"");
                  //modif misterphi
                  print(">" . $sName ." - ".$sUsername  . "</option>\n");
                  //eom
               }
            }
         }
      }
*/
      //print("</select>\n");
//eom ===============================================
      print("</select>\n");
      print("</td>\n");
      print("</tr>\n");
      if (!fIsAdmin())
      {
         print("<tr>\n");
         print("<td colspan=\"2\" class=\"form1\">");
         fPrintButtonSpace(1, 1);
         print("</td>\n");
         print("<td class=\"form2\" width=\"100%\">");
         print("<input class=\"fbuttonup1\" type=\"submit\" name=\"submit_button\" value=\"$owl_lang->acl_set_selected\" onclick=\"selectAll(document.fcombo_box.elements['fselectedusers[]']); selectAll(document.fcombo_box.elements['fselectedgroups[]']);\">\n"); 
         print("</td>\n");
         print("</tr>\n");
      }
      print("</table>\n");
      print("</td>\n");
      print("</tr>\n");
      print("</table>\n");
      print("</td>\n");
      print("</tr>\n");
      print("</table>\n");
      if (fIsAdmin())
      {
         fPrintSelectFileUserGroups("admin");
      }
      print("</form>\n");
      print("</td>\n");
      print("</tr>\n");
      print("<tr>\n");
      print("<td align=\"left\" valign=\"top\">\n");
      print("<form action=\"dbmodify.php\" method=\"post\" name=\"set_facl\">\n");
      print fGetHiddenFields ($urlArgs2);

      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"left\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      fPrintSectionHeader("$owl_lang->acl_set_folder_permissions");
      print("</table>");
      print("</td></tr></table>");

      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"left\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      print("<tr>\n");
      print("<td class=\"title1\" align=\"center\"><b>$owl_lang->acl_heading_folder</b></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:fcheckowlread()\">$owl_lang->acl_folder_read</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:fcheckowlwrite()\">$owl_lang->acl_folder_write</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:fcheckowldelete()\">$owl_lang->acl_folder_delete</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:fcheckowlcopy()\">$owl_lang->acl_folder_copy</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:fcheckowlmove()\">$owl_lang->acl_folder_move</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:fcheckowlproperties()\">$owl_lang->acl_folder_modify</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:fcheckowlsetacl()\">$owl_lang->acl_folder_set_acl</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:fcheckowlmonitor()\">$owl_lang->acl_folder_monitor</a></td>\n");
      print("</tr>\n");

      $CountLines = 0;
      if(!empty($fselectedgroups)) 
      {
         foreach ($fselectedgroups as $val)
         {
            if($val == -1)
            {
               continue;
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
               $sLfList = "lfile2";
            }  
            print("<tr>\n");
            print("<td class=\"$sTrClass\"><a class=\"lnavbar1\" href=\"javascript:checkFG" . $val ."()\">" . group_to_name($val) . "</a></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"fgacl_owlread_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlread") . "></input>t</b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"fgacl_owlwrite_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlwrite") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"fgacl_owldelete_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owldelete") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"fgacl_owlcopy_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlcopy") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"fgacl_owlmove_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlmove") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"fgacl_owlproperties_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlproperties") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"fgacl_owlsetacl_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlsetacl") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"fgacl_owlmonitor_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlmonitor") . "></input></b></td>\n");
            print("</tr>\n");
	 }
      }
      if(!empty($fselectedusers)) 
      {
         foreach ($fselectedusers as $val)
         {
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
               $sLfList = "lfile2";
            }  
            print("<tr>\n");
            print("<td class=\"$sTrClass\"><a class=\"lnavbar1\" href=\"javascript:checkFU" . $val . "()\">" . uid_to_name($val) ."</a></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"facl_owlread_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlread", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"facl_owlwrite_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlwrite", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"facl_owldelete_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owldelete", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"facl_owlcopy_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlcopy", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"facl_owlmove_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlmove", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"facl_owlproperties_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlproperties", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"facl_owlsetacl_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlsetacl", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"facl_owlmonitor_" . $val . "\" value=\"" . $val . "\" " . fGetFAclChecked($id, $val, "owlmonitor", "users") . "></input></b></td>\n");
           print("</tr>\n");
	}
     }
     if (!fIsAdmin())
     {
        print("<tr>\n");
        print("<td colspan=\"8\" class=\"form1\" width=\"100%\">");
        fPrintButtonSpace(1, 1);
        print("</td>\n");
        print("<td class=\"form2\" nowrap=\"nowrap\">");
        fPrintSubmitButton($owl_lang->btn_acl_save, $owl_lang->alt_acl_save_folder);
        fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
        print("</td>\n");
        print("</tr>\n");
     }
     else
     {
        print("<tr>\n");
        print("<td colspan=\"8\" class=\"form1\" width=\"100%\">");
        fPrintButtonSpace(1, 1);
        print("</td>\n");
        print("<td class=\"form1\" nowrap=\"nowrap\">");
        print("<table>\n");
        print("<tr>\n");
        fPrintFormCheckBox($owl_lang->acl_propagate_folders, "folder_propagate", "1");
        print("</tr>\n");
        print("</table>\n");
        print("</td>\n");
        print("</tr>\n");
     }
     print("</table>\n");
     print("</td>\n");
     print("</tr>\n");
     print("</table>\n");

     if (fIsAdmin())
     {
        fPrintSetFileAcl(0, "admin");
     }
     print("</form>\n");
     //print("</td></tr></table>\n");
   }
}
elseif ($action == "file_acl")
{
   if (check_auth($id, "file_acl", $userid) == 1)
   {
      $urlArgs2 = $urlArgs;
      $urlArgs2['expand'] = $expand;
      $urlArgs2['id']     = $id;
      $urlArgs2['action']  = "file_acl";

      if($edit == 1)
      {
         fPrintNavBar($parent,$owl_lang->acl_edit_file, $id);
      }
      else
      {
         fPrintNavBar($parent,$owl_lang->acl_adding_file, $id);
      }

      print("<form action=\"setacl.php\" method=\"post\" name=\"combo_box\">");
      print fGetHiddenFields ($urlArgs2);
      fPrintSelectFileUserGroups();
      print("</form>\n");

      print("</td>\n");
      print("</tr>\n");
      print("<tr>\n");
      print("<td align=\"left\" valign=\"top\">\n");
      print("<form action=\"dbmodify.php\" method=\"post\" name=\"set_acl\">\n");
      print fGetHiddenFields ($urlArgs2);
      fPrintSetFileAcl($id);
      print("</form>\n");
      //print("</td></tr></table>\n");
   }
   else
   {
      printError($owl_lang->err_nofilemod);
   } 
}


fPrintButtonSpace(12, 1);

if ($default->show_prefs == 2 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar2");
}

print("</td></tr></table>\n");
include($default->owl_fs_root ."/lib/footer.inc");

function fPrintSetFileAcl($id, $type = "user")
{
      global $owl_lang, $groups, $selectedgroups, $aUserList, $selectedusers ;
      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"left\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      fPrintSectionHeader("$owl_lang->acl_set_file_permissions");
      print("</table>");
      print("</td></tr></table>");
      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"left\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      print("<tr>\n");
      print("<td class=\"title1\" align=\"center\"><b>$owl_lang->acl_heading_file</b></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlread()\">$owl_lang->acl_file_read</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlupdate()\">$owl_lang->acl_file_update</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlsetacl()\">$owl_lang->acl_file_set_acl</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowldelete()\">$owl_lang->acl_file_delete</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlcopy()\">$owl_lang->acl_file_copy</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlmove()\">$owl_lang->acl_file_move</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlproperties()\">$owl_lang->acl_file_modify</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlviewlog()\">$owl_lang->acl_file_view_log</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlcomment()\">$owl_lang->acl_file_comment</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlcheckin()\">$owl_lang->acl_file_checkin</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlemail()\">$owl_lang->acl_file_email</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlrelsearch()\">$owl_lang->acl_file_search</a></td>\n");
      print("<td class=\"title1\" align=\"center\"><a class=\"ltitle1\" href=\"javascript:checkowlmonitor()\">$owl_lang->acl_file_monitor</a></td>\n");
      print("</tr>\n");

      $CountLines = 0;
      if(!empty($selectedgroups)) 
      {
         foreach ($selectedgroups as $val)
         {
            if($val == -1)
            {
               continue;
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
              $sLfList = "lfile2";
            }  
            print("<tr>\n");
            print("<td class=\"$sTrClass\"><a class=\"lnavbar1\" href=\"javascript:checkG" . $val ."()\">" . group_to_name($val) . "</a></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlread_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlread") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlupdate_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlupdate") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlsetacl_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlsetacl") . "></input></b></td>\n");
            //print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlwrite_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlwrite") . "></input</b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owldelete_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owldelete") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlcopy_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlcopy") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlmove_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlmove") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlproperties_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlproperties") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlviewlog_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlviewlog") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlcomment_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlcomment") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlcheckin_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlcheckin") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlemail_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlemail") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlrelsearch_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlrelsearch") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"gacl_owlmonitor_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlmonitor") . "></input></b></td>\n");
            print("</tr>\n");
	 }
      }
      if(!empty($selectedusers)) 
      {
         foreach ($selectedusers as $val)
         {
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
               $sLfList = "lfile2";
            }  
            print("<tr>\n");
            print("<td class=\"$sTrClass\"><a class=\"lnavbar1\" href=\"javascript:checkU" . $val ."()\">" . uid_to_name($val) ."</a></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlread_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlread", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlupdate_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlupdate", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlsetacl_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlsetacl", "users") . "></input></b></td>\n");
            //print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlwrite_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlwrite", "users") . "></input</b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owldelete_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owldelete", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlcopy_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlcopy", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlmove_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlmove", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlproperties_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlproperties", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlviewlog_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlviewlog", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlcomment_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlcomment", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlcheckin_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlcheckin", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlemail_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlemail", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlrelsearch_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlrelsearch", "users") . "></input></b></td>\n");
            print("<td class=\"$sTrClass\" align=\"center\"><b><input type=\"checkbox\" name=\"acl_owlmonitor_" . $val . "\" value=\"" . $val . "\" " . fGetAclChecked($id, $val, "owlmonitor", "users") . "></input></b></td>\n");
            print("</tr>\n");
	 }
      }
      if ($type == "admin")
      {
         print("<tr>\n");
         print("<td colspan=\"13\" class=\"form1\" width=\"100%\">");
         fPrintButtonSpace(1, 1);
         print("</td>\n");
         print("<td class=\"form2\" nowrap=\"nowrap\">");
         print("<table>\n");
         print("<tr>\n");
         fPrintFormCheckBox($owl_lang->acl_propagate_file, "file_propagate", "1");
         print("</tr>\n");
         print("</table>\n");
         print("</td>\n");
         print("</tr>\n");
      }
      print("<tr>\n");
      print("<td colspan=\"13\" class=\"form1\" width=\"100%\">");
      fPrintButtonSpace(1, 1);
      print("</td>\n");
      print("<td class=\"form2\" nowrap=\"nowrap\">");
      fPrintSubmitButton($owl_lang->btn_acl_save, $owl_lang->alt_acl_save_file);
      fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
      print("</td>\n");
      print("</tr>\n");
      print("</table>\n");
      print("</td>\n");
      print("</tr>\n");
      print("</table>\n");
}

function fPrintSelectFileUserGroups($type = "user")
{
   global $default, $owl_lang, $groups, $selectedgroups, $aUserList, $selectedusers;

      print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
      print("<tr>\n<td align=\"center\" valign=\"top\">");
      print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
      fPrintSectionHeader($owl_lang->acl_heading_files);
      print("</table>");
      print("</td></tr></table><br />");
   
   print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
   print("<tr>\n");
   print("<td align=\"center\" valign=\"top\">\n");
   print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n");
   print("<tr>\n");
   print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">$owl_lang->acl_available_groups</td>\n");
   print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">&nbsp;</td>\n");
   print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">$owl_lang->acl_selected_groups</td>\n");
   //print("<td class=\"admin3\">&nbsp;</td>\n");
   print("</tr>");

   print("<tr>\n");
   print("<td class=\"form1\">\n");

if ($default->show_users_in_group == true)
{
   ?>
<script type="text/javascript">
//Function alerts the index of the selected option within form

var tooltipContentData = [

<?php

   $qGetUserMember = new Owl_DB;
   
   foreach($groups as $g)
   {
      if ($g[0] == -1)
      {
         continue;
      }

      if (!empty($selectedgroups))
      {
         if (!(in_array($g[0], $selectedgroups)))
         {
            $qSetAcl = "SELECT distinct id,name, username FROM $default->owl_users_table u left join $default->owl_users_grpmem_table m on u.id = m.userid where u.groupid = '$g[0]' or m.groupid = '$g[0]'";
            //$qSetAcl = "SELECT name, username FROM $default->owl_users_table where groupid = '$g[0]'";
            $qGetUserMember->query($qSetAcl);
            print("'<table>");
            if ($qGetUserMember->num_rows() == 0)
            {
               print("<tr><td>None</td></tr>");
            }
            while($qGetUserMember->next_record())
            {
               print("<tr><td>" . $qGetUserMember->f('name') . "</td><td>(" . $qGetUserMember->f('username') . ")</td></tr>");
            }
            print("</table>',\n");
         }
      }
      else
      {
         $qSetAcl = "SELECT distinct id,name, username FROM $default->owl_users_table u left join $default->owl_users_grpmem_table m on u.id = m.userid where u.groupid = '$g[0]' or m.groupid = '$g[0]'";
         //$qSetAcl = "SELECT name, username FROM $default->owl_users_table where groupid = '$g[0]'";
         $qGetUserMember->query($qSetAcl);
         print("'<table>");
         if ($qGetUserMember->num_rows() == 0)
         {
            print("<tr><td>None</td></tr>");
         }
         while($qGetUserMember->next_record())
         {
            print("<tr><td>" . $qGetUserMember->f('name') . "</td><td>(" . $qGetUserMember->f('username') . ")</td></tr>");
         }
         print("</table>',\n");
      }

   }
   print ("''\n");

?>
];

var tooltipCaptionData = [

<?php

   $qGetUserMember = new Owl_DB;

   foreach($groups as $g)
   {
      if ($g[0] == -1)
      {
         continue;
      }

      if (!empty($selectedgroups))
      {
         if (!(in_array($g[0], $selectedgroups)))
         {
            print("'Users in: $g[1]',");
         }
      }
      else
      {
         print("'Users in: $g[1]',");
      }

   }
   print ("''\n");

?>
];


var tooltipContentIndex = 0;
function getTooltipContent() {
	return tooltipContentData[tooltipContentIndex];
}
function getTooltipCaption() {
	return tooltipCaptionData[tooltipContentIndex];
}

function getselectedvalue(gbox){
return(gbox.selectedIndex)
}

</script>
<?php
}
   print("<select multiple=\"multiple\" size=\"10\" name=\"allgroups[]\" class=\"fpullacl\"");
   //print (" onchange=\"tooltipContentIndex = getselectedvalue(this); domTT_activate(this, event, 'content', getTooltipContent(), 'lifetime', 3000, 'closeAction', 'destroy', 'styleClass', 'niceTitle', 'x', 50, 'y', 50, 'type', 'sticky');\"");
if ($default->show_users_in_group == true)
{
   print (" onchange=\"tooltipContentIndex = getselectedvalue(this); domTT_activate(this, event, 'content', getTooltipContent(), 'type', 'sticky', 'closeLink', '&nbsp; [x] &nbsp; ', 'draggable', true, 'closeAction', 'destroy', 'caption' , getTooltipCaption(), 'x', 50, 'y', tempY);\"");
}
   print(">\n");

   foreach($groups as $g)
   {
      if ($g[0] == -1)
      {
         continue;
      }
      if (!empty($selectedgroups))
      {
         if (!(in_array($g[0], $selectedgroups)))
         {
            print("<option value=\"$g[0]\"");
            print(">$g[1]</option>\n");
         }
      }
      else
      {
         print("<option value=\"$g[0]\"");
         print(">$g[1]</option>\n");
      }
   }
   print("</select>\n</td>\n");
   print("<td class=\"form1\" nowrap=\"nowrap\">\n");
   print("&nbsp;&nbsp;<input type=\"button\" onclick=\"move(this.form.elements['selectedgroups[]'],this.form.elements['allgroups[]'])\" value=\"<--\"></input>");
   print("<input type=\"button\" onclick=\"move(this.form.elements['allgroups[]'],this.form.elements['selectedgroups[]'])\" value=\"-->\"></input>");
   print("<br />");
   print("<input type=\"button\" onclick=\"selectAllGroups(this.form.elements['selectedgroups[]'],this.form.elements['allgroups[]'])\" value=\"<<--\"></input>");
   print("<input type=\"button\" onclick=\"selectAllGroups(this.form.elements['allgroups[]'],this.form.elements['selectedgroups[]'])\" value=\"-->>\"></input>");
   print("</td>");
   print("<td class=\"form1\">\n");
   print("<select multiple=\"multiple\" size=\"10\" name=\"selectedgroups[]\" class=\"fpullacl\">");
   if (!empty($groups))
   {
      foreach($groups as $g)
      {
         if ($g[0] == -1)
         {
            continue;
         }
         if (!empty($selectedgroups))
         {
            if ((in_array($g[0], $selectedgroups)))
            {
               print("<option value=\"$g[0]\"");
               print(">$g[1]</option>\n");
            }
         }
      }
   }
   print("</select>\n</td>\n\n");
   //print("<td class=\"form1\" width=\"100%\">&nbsp;</td>");
   print("</tr>\n");
   print("<tr>\n");
   print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">$owl_lang->acl_available_users</td>\n");
   print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">&nbsp;</td>\n");
   print("<td class=\"admin3\" align=\"center\" nowrap=\"nowrap\">$owl_lang->acl_selected_users</td>\n");
   print("</tr>");
   print("<tr>\n");
   print("<td class=\"form1\">");
   print("<select multiple=\"multiple\" size=\"10\" name=\"allusers[]\" class=\"fpullacl\">");
   /*if (!empty($aUserList))
   {
      foreach ($aUserList as $aUsers)
      {
         $sUsername = $aUsers["username"];
         $sId = $aUsers["id"];
         $sName = $aUsers["name"];
         if(!empty($aUsers["email"]))
         {
            $sEmail = " (" . $aUsers["email"] . ")";
         }
         else
         {
            $sEmail = "";
         }

         if (!empty($selectedusers))
         {
            if (!(in_array($sId, $selectedusers)))
            {
               print("<option value=\"$sId\"");
               print(">" . $sName .  $sEmail . "</option>\n");
            }
         }
         else
         {
            print("<option value=\"$sId\"");
            print(">" . $sName . $sEmail . "</option>\n");
         }
      }
   }*/
   
   
   //modif misterphi ==========================================================
 foreach($groups as $g)
      {
         if ($g[0] == -1)
         {
            continue;
         }
         if (!empty($fselectedgroups))
         {
            if (!(in_array($g[0], $fselectedgroups)))
            {
                        
               print("<optgroup label=\"$g[1]\">");
               //affichage des users du groupe
               
               $qSetAcl = "SELECT distinct id,name, username FROM $default->owl_users_table u left join $default->owl_users_grpmem_table m on u.id = m.userid where u.groupid = '$g[0]' or m.groupid = '$g[0]'";
            //$qSetAcl = "SELECT name, username FROM $default->owl_users_table where groupid = '$g[0]'";
            $qGetUserMember->query($qSetAcl);
           
            if ($qGetUserMember->num_rows() == 0)
            {
               //print("<tr><td>None</td></tr>");
            }
            while($qGetUserMember->next_record())
            {
               //print("<tr><td>" . $qGetUserMember->f('name') . "</td><td>(" . $qGetUserMember->f('username') . ")</td></tr>");
                $sonid=$qGetUserMember->f('id');
                print("<option value=\"$sonid\"");
            	print(">".$qGetUserMember->f('name')."</option>\n");
            }
                      
               print("</optgroup>\n");
               
            }
         }
         else
         {
           
           if (!(in_array($g[0], $fselectedgroups)))
            {
                        
               print("<optgroup label=\"$g[1]\">");
               //affichage des users du groupe
               
               $qSetAcl = "SELECT distinct id,name, username FROM $default->owl_users_table u left join $default->owl_users_grpmem_table m on u.id = m.userid where u.groupid = '$g[0]' or m.groupid = '$g[0]'";
            //$qSetAcl = "SELECT name, username FROM $default->owl_users_table where groupid = '$g[0]'";
            $qGetUserMember->query($qSetAcl);
           
            if ($qGetUserMember->num_rows() == 0)
            {
               //print("<tr><td>None</td></tr>");
            }
            while($qGetUserMember->next_record())
            {
               //print("<tr><td>" . $qGetUserMember->f('name') . "</td><td>(" . $qGetUserMember->f('username') . ")</td></tr>");
                $sonid=$qGetUserMember->f('id');
                print("<option value=\"$sonid\"");
            	print(">".$qGetUserMember->f('name')."</option>\n");
            }
                      
               print("</optgroup>\n");
               
            }
           
            /*print("<option value=\"$g[0]\"");
            print(">$g[1]</option>\n");*/
         }
      }
     
      print("</select>\n</td>\n");
 
//eom misterphi  ====================================================================================    
   
   print("</select>\n");
   print("</td>\n");
	   print("<td class=\"form1\" nowrap=\"nowrap\">\n");
   print("&nbsp;&nbsp;<input type=\"button\" onclick=\"del(this.form.elements['selectedusers[]'])\" value=\"<--\"></input>\n");
   print("<input type=\"button\" onclick=\"add(this.form.elements['allusers[]'],this.form.elements['selectedusers[]'])\" value=\"-->\"></input>\n");
   print("<br />");
   //print("<input type=\"button\" onclick=\"selectAllUsers(this.form.elements['selectedusers[]'],this.form.elements['allusers[]'])\" value=\"<<--\"></input>\n");
   //print("<input type=\"button\" onclick=\"selectAllUsers(this.form.elements['allusers[]'],this.form.elements['selectedusers[]'])\" value=\"-->>\"></input>\n");
   print("</td>\n");
   print("<td class=\"form1\">\n");
   print("<select multiple=\"multiple\" size=\"10\" name=\"selectedusers[]\" class=\"fpullacl\">\n");

 //modif misterphi
foreach ($selectedusers as $val)
         {
            if($val == -1)
            {
               continue;
            }
			
			$quser = "SELECT id, name, username FROM $default->owl_users_table where id = '$val'";
			$qGetuser = new Owl_DB;
		
			$qGetuser->query($quser);
			while ($qGetuser->next_record())
      		{
			$sId = $qGetuser->f("id");
        	$sName = $qGetuser->f("name");
			$sUsername = $qGetuser->f("username");
			}
			if (!empty($selectedusers))
            {
               if ((in_array($sId, $selectedusers)))
               {
                  print("<option value=\"$sId\"");
                 
                  print(">" . $sName ." - ".$sUsername  . "</option>\n");
                  
               }
            }
			}  
//eom

 /*if (!empty($aUserList))
   {
      foreach ($aUserList as $aUsers)
      {
         $sUsername = $aUsers["username"];
         $sId = $aUsers["id"];
         $sName = $aUsers["name"];
		$nbr= count ($selectedusers);
         if(!empty($aUsers["email"]))
         {
            $sEmail = " (" . $aUsers["email"] . ")";
         }
         else
         {
            $sEmail = "";
         }
         if (!empty($selectedusers))
         {
            if ((in_array($sId, $selectedusers)))
            {
               print("<option value=\"$sId\"");
               print(">" . $sName . " - " . $sUsername . "</option>\n");
            }
         }
      }
   }*/
   //print("</select>\n");
   print("</select>\n");
   print("</td>\n");
   print("</tr>\n");
   print("<tr>\n");
   print("<td colspan=\"2\" class=\"form1\">");

   fPrintButtonSpace(1, 1);
   print("</td>\n");
   print("<td class=\"form2\">");
 
   if ($type == "admin")
   {
      print("<input class=\"fbuttonup1\" type=\"submit\" name=\"submit_button\" value=\"$owl_lang->acl_set_selected\" onclick=\"selectAll(document.fcombo_box.elements['selectedusers[]']); selectAll(document.fcombo_box.elements['selectedgroups[]']); selectAll(document.fcombo_box.elements['fselectedusers[]']); selectAll(document.fcombo_box.elements['fselectedgroups[]']);\">\n"); 
   }
   else
   {
      print("<input class=\"fbuttonup1\" type=\"submit\" name=\"submit_button\" value=\"$owl_lang->acl_set_selected\" onclick=\"selectAll(document.combo_box.elements['selectedusers[]']); selectAll(document.combo_box.elements['selectedgroups[]']);\">");  
   }


   print("</td>\n");
   print("</tr>\n");
   print("</table>\n");
   print("</td>\n");
   print("</tr>\n");
   print("</table>\n");
}
?>
