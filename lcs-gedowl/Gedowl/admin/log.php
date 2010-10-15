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
 * $Id: log.php,v 1.12 2007/08/24 10:55:20 b0zz Exp $
 */

global $default, $whereclause;

require_once(dirname(dirname(__FILE__)). "/config/owl.php");
require_once($default->owl_fs_root . "/lib/Net_CheckIP/CheckIP.php");
require_once($default->owl_fs_root . "/lib/disp.lib.php");
require_once($default->owl_fs_root . "/lib/owl.lib.php");

//if (!fIsAdmin())
//{
   //die("$owl_lang->err_unauthorized");
//}


if (!isset($nextrecord)) $nextrecord = 0;
if (!isset($next)) $next = 0;
if (!isset($prev)) $prev = 0;
if (!isset($hideagent)) $hideagent = 0;
if (isset($fa)) $filteraction = unserialize(stripslashes($fa));
if (!isset($hidedetail)) $hidedetail = 0;

if ($next == 1) $nextrecord = $nextrecord + $default->log_rec_per_page;
if ($prev == 1)
{
   $nextrecord = $nextrecord - $default->log_rec_per_page;
   if ($nextrecord < 0)
   {
      $nextrecord = 0;
   } 
} 

$whereclause = " where 1=1";

if ($filteraction && $filteraction[0] != "0")
{
   $whereclause .= " and (";
   foreach($filteraction as $fa)
   {
      $whereclause .= " action='$fa' or";
   } 
   $whereclause .= " action='$fa')";
} 

if ($filteruser && $filteruser != "0")
{
   $whereclause .= " and userid='$filteruser'";
} 

if (($filtergroup or $filtergroup == "0") and $filtergroup != "-1")
{
   $whereclause .= " and u.groupid='$filtergroup'";
}
else
{
   $filtergroup = "-1";
}


if ($filter_file && $filter_file != "0")
{
   $whereclause .= " and filename='$filter_file'";
}

if ($filter_from && $filter_from != "0")
{
   if (strpos($filter_from, ':') > 0 )
   {
      $whereclause .= " and logdate >= '$filter_from'";
   }
   else
   {
      $whereclause .= " and logdate >= '$filter_from 00:00'";
   }

} 

if ($filter_to && $filter_to != "0")
{
   if (strpos($filter_to, ':') > 0)
   {
      $whereclause .= " and logdate <= '$filter_to'";
   }
   else
   {
      $whereclause .= " and logdate <= '$filter_to 23:59'";
   }
} 

if (!fIsAdmin(true) and !fIsLogViewer($userid))
{
   die("$owl_lang->err_unauthorized");
}

if ($action == "gen_pdf")
{

   $sName = "owl_syslog_" . date("Ymd") . "_" . date("Gis");


   $txt = "";

   $aFirstpExtension = fFindFileFirstpartExtension ($sName);
   $sFirstPart = $aFirstpExtension[0];

   $pdf=new Owl_PDF("landscape");
   $pdf->SetTitle("Owl Syslog - " . date("Y/m/d") . "  " . date("G:i:s"));
   $pdf->SetAuthor(uid_to_name($userid));
   $pdf->SetCreator($default->version);
   $pdf->SetTextColor(0,0,0);
   $pdf->sFpdfTitle = "                                                                       " . $owl_lang->owl_log_viewer;
   $pdf->sFpdfDocName = $sName;
   $pdf->AliasNbPages();
   $pdf->AddPage();

   $pdf->SetFont('Arial','b',7);
   $pdf->Cell(10,3,"Generated: " . date($owl_lang->localized_date_format));
   $pdf->Ln();

// Display the Filter Parms

   $iCountLines = 0;

   if ($filteraction)
   {
      $pdf->Cell(10,3,"Filter: ");
      foreach($filteraction as $fa)
      {
         $pdf->Cell(10,3,$logactions[$fa][1]);
         $iCountLines++;
         if ($iCountLines == 1)
         {
            if (($filtergroup or $filtergroup == "0") and $filtergroup != "-1")
            {
                $sGroupName = " (" . group_to_name($filtergroup) . ")";
            }
            else
            {
                $sGroupName = " (" . $owl_lang->log_filter_all . ")";
            }

            if ($filteruser and $filteruser != "0")
            {
               $pdf->Cell(20,3, "");
               $pdf->Cell(20,3, "User: " . uid_to_name($filteruser) . $sGroupName);
            }
            else
            {
               $pdf->Cell(20,3, "");
               $pdf->Cell(20,3, "User: " . $owl_lang->log_filter_all . $sGroupName);
            }
            if ($filter_from)
            {
               $pdf->Cell(20,3, "");
               $pdf->Cell(20,3, "From Date: " . date($owl_lang->localized_date_format, strtotime($filter_from)));
            }
         }

         if ($iCountLines == 2)
         {

            if ($filter_file)
            {
               $pdf->Cell(20,3, "");
               $pdf->Cell(20,3, "File: " . $filter_file);
               $pdf->Cell(20,3, "");
            }
            else
            {
               $pdf->Cell(60,3, "");
            }
            if ($filter_to)
            {
               $pdf->Cell(20,3, "     To Date: " . date($owl_lang->localized_date_format, strtotime($filter_to)));
            }

         }
         $pdf->Ln();
         $pdf->Cell(10,3,"        ");
      }

      if ($iCountLines < 2)
      {
            if ($filter_file)
            {
               $pdf->Cell(30,3, "");
               $pdf->Cell(20,3, "File: " . $filter_file);
               $pdf->Cell(20,3, "");
            }
            else
            {
               $pdf->Cell(70,3, "");
            }
            if ($filter_to)
            {
               $pdf->Cell(20,3, "     To Date: " . date($owl_lang->localized_date_format, strtotime($filter_to)));
            }
      }
   }
   else
   {
      $pdf->Cell(10,4,"Filter: " . $logactions[0][1]);
            if (($filtergroup or $filtergroup == "0") and $filtergroup != "-1")
            {
                $sGroupName = " (" . group_to_name($filtergroup) . ")";
            }
            else
            {
                $sGroupName = " (" . $owl_lang->log_filter_all . ")";
            }

            if ($filteruser and $filteruser != "0")
            {
               $pdf->Cell(20,3, "");
               $pdf->Cell(20,3, "User: " . uid_to_name($filteruser) . $sGroupName);
            }
            else
            {
               $pdf->Cell(20,3, "");
               $pdf->Cell(20,3, "User: " . $owl_lang->log_filter_all . $sGroupName);
            }

            if ($filter_from)
            {
               $pdf->Cell(20,3, "From Date: " . date($owl_lang->localized_date_format, strtotime($filter_from)));
            }

         $pdf->Ln();
            
            if ($filter_file)
            {
               $pdf->Cell(30,3, "");
               $pdf->Cell(20,3, "File: " . $filter_file);
               $pdf->Cell(20,3, "");
            }
            else
            {
               $pdf->Cell(70,3, "");
            }
            if ($filter_to)
            {
               $pdf->Cell(20,3, "     To Date: " . date($owl_lang->localized_date_format, strtotime($filter_to)));
            }
   }
   $pdf->Ln();
   $pdf->Ln();


// Print Column Headings

   $pdf->SetFont('Arial','b',9);
   $pdf->Cell(30,5,$owl_lang->owl_log_hd_action);
   $pdf->Cell(40,5,$owl_lang->owl_log_hd_file);
   $pdf->Cell(40,5,$owl_lang->owl_log_hd_fld_path);
   $pdf->Cell(20,5,$owl_lang->owl_log_hd_user);
   $pdf->Cell(30,5,$owl_lang->owl_log_hd_dt_tm);
   $pdf->Cell(35,5,$owl_lang->owl_log_hd_ip);
   if ($hideagent == 0 and $hidedetail == 0)
   { 
      $pdf->Cell(40,5,$owl_lang->owl_log_hd_agent . " / " . $owl_lang->owl_log_hd_dtls);
   }
   else
   {
      if ($hideagent == 0)
      {
         $pdf->Cell(40,5,$owl_lang->owl_log_hd_agent);
      }
      if ($hidedetail == 0)
      {
         $pdf->Cell(40,5,$owl_lang->owl_log_hd_dtls);
      }
   }
   $pdf->Ln();
   
   $pdf->SetFont('Arial','',7);

   $sql = new Owl_DB;
   
   $sql->query("SELECT * FROM $default->owl_log_table left outer join $default->owl_users_table  u on u.id=userid $whereclause ORDER BY logdate DESC");
   while ($sql->next_record())
   {
      $pdf->SetTextColor(0,128,0);
      $pdf->Cell(30,3,"<" . $log_file_actions[$sql->f("action")] . ">");
      $pdf->SetTextColor(0,0,0);
      if ($sql->f("type") != "LOGIN")
      {
         $pdf->Cell(40,3, $sql->f("filename"));
         $pdf->Cell(40,3, get_dirpath($sql->f("parent")));
      }
      else
      {
         $pdf->Cell(40,3,"");
         $pdf->Cell(40,3,"");
      }
      $pdf->Cell(20,3, uid_to_name($sql->f("userid")));
      $pdf->Cell(30,3, date($owl_lang->localized_date_format, strtotime($sql->f("logdate"))));
      if (Net_CheckIP::check_ip($sql->f('ip')))
      {
         $pdf->Cell(35,3, fGetHostByAddress($sql->f('ip')));
      }
      else
      {
         $pdf->Cell(35,3, $sql->f('ip'));
      }
      if ($hideagent == 0 and $hidedetail == 0)
      { 
         $pdf->Cell(40,3, $sql->f("agent"));
         $pdf->Ln();
         $pdf->Cell(195,3,"");
         $pdf->Cell(40,3, $sql->f("details"));
      }
      else
      {
         if ($hideagent == 0)
         {
            $pdf->Cell(40,3, $sql->f("agent"));
         }
         if ($hidedetail == 0)
         {
            $pdf->Cell(40,3, $sql->f("details"));
         }
      }

         $pdf->Cell(40,3,"");
      $pdf->Ln();
   }

   $pdf->Ln();
   $pdf->Output($sFirstPart . ".pdf", 'D');

}



if ($action == "clear_log")
{
   $sql = new Owl_DB;
   $sql->query("DELETE from $default->owl_log_table");
} 

include_once($default->owl_fs_root . "/lib/header.inc");
include_once($default->owl_fs_root . "/lib/userheader.inc");
print("<center>\n");

print("<table class=\"border1\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"$default->table_expand_width\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");
fPrintButtonSpace(12, 1);
print("<br />\n");
print("<table class=\"border2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\" width=\"100%\">\n");

if ($default->show_prefs == 1 or $default->show_prefs == 3)
{
      fPrintPrefs("infobar1", "top");
}
fPrintButtonSpace(12, 1);
print("<br />\n");

if (fIsAdmin(true))
{
  fPrintAdminPanel("viewlog");
}

print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\">\n");
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
print("<tr><td class=\"admin0\" width=\"100%\" colspan=\"8\">$owl_lang->owl_log_viewer</td></tr>\n");
print("<tr>\n");
print("<td class=\"title1\">$owl_lang->owl_log_hd_action</td>\n");
print("<td class=\"title1\">$owl_lang->owl_log_hd_file</td>\n");
print("<td class=\"title1\">$owl_lang->owl_log_hd_fld_path</td>\n");
print("<td class=\"title1\">$owl_lang->owl_log_hd_user</td>\n");
print("<td class=\"title1\">$owl_lang->owl_log_hd_dt_tm</td>\n");
print("<td class=\"title1\">$owl_lang->owl_log_hd_ip</td>\n");
if ($hideagent == 0)
{
   print("<td class=\"title1\">$owl_lang->owl_log_hd_agent</td>\n");
}
if ($hidedetail == 0)
{
   print("<td class=\"title1\">$owl_lang->owl_log_hd_dtls</td>\n");
}

print("</tr>\n");
// print the LOG Details
$CountLines = 0;
$sql = new Owl_DB;
$getusers = new Owl_DB; 
$groups = fGetGroups($userid);
$groups[-1][0] = "-1";
$groups[-1][1] = "ALL";

// Found out how many records we are going to retreive
$sql->query("SELECT * FROM $default->owl_log_table left outer join $default->owl_users_table  u on u.id=userid $whereclause");
$recordcount = $sql->num_rows($sql); 

// Retreive the log records for display
if ($recordcount == 0)
{
   print("<tr>\n<td colspan=\"8\" class=\"admin3\" align=\"center\"><h2>$owl_lang->owl_log_no_rec</h2></td>\n</tr>\n");
   print("<tr>\n<td colspan=\"8\" align=\"center\"><h2>&nbsp;</h2></td>\n</tr>\n");
} 
else
{
   $sql->query("SELECT * FROM $default->owl_log_table left outer join $default->owl_users_table  u on u.id=userid $whereclause ORDER BY logdate DESC LIMIT $nextrecord,$default->log_rec_per_page");
   while ($sql->next_record())
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
      $sLfList = "lfile1";
   }     
      print("<td class=\"$sTrClass\"><font color=\"green\"><b>&lsaquo;" . $log_file_actions[$sql->f("action")] . "></b></font></td>\n");
      if ($sql->f("type") != "LOGIN")
      {
         print("<td class=\"$sTrClass\">" . $sql->f("filename") . "</td>\n");
         print("<td class=\"$sTrClass\">" . get_dirpath($sql->f("parent")) . "</td>\n");
      } 
      else
      {
         print("<td class=\"$sTrClass\">&nbsp;</td>\n");
         print("<td class=\"$sTrClass\">&nbsp;</td>\n");
      } 
      print("<td class=\"$sTrClass\">" . uid_to_name($sql->f("userid")) . "</td>\n");
      print("<td class=\"$sTrClass\">" . date($owl_lang->localized_date_format, strtotime($sql->f("logdate"))) . "</td>\n");
      if (Net_CheckIP::check_ip($sql->f('ip')))
      {
         print("<td class=\"$sTrClass\">" . fGetHostByAddress($sql->f('ip')) . "</td>\n");
      }
      else
      {
         print("<td class=\"$sTrClass\">" . $sql->f('ip') . "</td>\n");
      }
      //print("<td class='$sTrClass'>" . gethostbyaddr($sql->f('ip')) . "</td>\n");
      if ($hideagent == 0)
      {
         print("<td class=\"$sTrClass\">" . $sql->f("agent") . "</td>\n");
      }
      if ($hidedetail == 0)
      {
         print("<td class=\"$sTrClass\">" . $sql->f("details") . "</td>\n");
      }
      print ("</tr>\n");
   } 
} 
print("</table>"); 
// print out the filters
$logactions[0][1] = "$owl_lang->log_filter_all";
$logactions[1][1] = $log_file_actions[LOGIN];
$logactions[2][1] = $log_file_actions[LOGIN_FAILED];
$logactions[3][1] = $log_file_actions[LOGOUT];
$logactions[4][1] = $log_file_actions[FILE_DELETED];
$logactions[5][1] = $log_file_actions[FILE_UPLOAD];
$logactions[6][1] = $log_file_actions[FILE_UPDATED];
$logactions[7][1] = $log_file_actions[FILE_DOWNLOADED];
$logactions[8][1] = $log_file_actions[FILE_CHANGED];
$logactions[9][1] = $log_file_actions[FILE_LOCKED];
$logactions[10][1] = $log_file_actions[FILE_UNLOCKED];
$logactions[11][1] = $log_file_actions[FILE_EMAILED];
$logactions[12][1] = $log_file_actions[FILE_MOVED];
$logactions[19][1] = $log_file_actions[FILE_VIEWED];
$logactions[20][1] = $log_file_actions[FILE_VIRUS];
$logactions[21][1] = $log_file_actions[FILE_COPIED];
$logactions[23][1] = $log_file_actions[FILE_LINKED];
$logactions[26][1] = $log_file_actions[FILE_ACL];
$logactions[13][1] = $log_file_actions[FOLDER_CREATED];
$logactions[14][1] = $log_file_actions[FOLDER_DELETED];
$logactions[15][1] = $log_file_actions[FOLDER_MODIFIED];
$logactions[16][1] = $log_file_actions[FOLDER_MOVED];
$logactions[22][1] = $log_file_actions[FOLDER_COPIED];
$logactions[27][1] = $log_file_actions[FOLDER_ACL];
$logactions[17][1] = $log_file_actions[FORGOT_PASS];
$logactions[18][1] = $log_file_actions[USER_REG];
$logactions[24][1] = $log_file_actions[USER_ADMIN];
$logactions[25][1] = $log_file_actions[TRASH_CAN];

print("<form enctype=\"multipart/form-data\" action=\"log.php\" method=\"post\">
                        <input type=\"hidden\" name=\"sess\" value=\"$sess\"></input>
                        <input type=\"hidden\" name=\"action\" value=\"refresh\"></input>
                        <input type=\"hidden\" name=\"id\" value=\"$id\"></input>
                        <input type=\"hidden\" name=\"whereclause\" value=\"$whereclause\"></input>");
print("<input type=\"hidden\" name=\"expand\" value=\"$expand\"></input>\n");
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
fPrintSectionHeader($owl_lang->owl_log_filter, "admin3");
print("<tr>\n<td class=\"form1\">$owl_lang->owl_log_hd_action:</td>\n<td class=\"form1\" width=\"100%\">");
print("<select size=\"8\" name=\"filteraction[]\" multiple=\"multiple\">");
foreach($logactions as $key => $fp)
{
   $isSelected = false;
   if ($filteraction[0] != "")
   {
      foreach($filteraction as $fa)
      {
         if ($fa == $key)
         {
            $isSelected = true;
         }
      } 
   } 
   print("<option value='$key' ");
   if ($isSelected)
   {
      print("selected=\"selected\"");
   }
   print(">$fp[1]</option>");
} 
print("</select>\n</td>\n</tr>\n"); 
// Print Users
$getusers->query("select name,username,id from $default->owl_users_table order by name");

print("<tr>");
print("<td class=\"form1\">$owl_lang->owl_log_hd_user:</td><td class=\"form1\" width=\"100%\"><select name=\"filteruser\">");

print("<option value=\"0\">$owl_lang->log_filter_all</option>");
while ($getusers->next_record())
{
   $uid = $getusers->f("id");
   $name = $getusers->f("name");
   $username = $getusers->f("username");

   if ($name == "")
   {
      print("<option value=\"" . $uid . "\">" . $username . "</option>");
   }
   else
   {
      if ($uid == $filteruser)
      {
         print("<option value=\"" . $uid . "\" selected=\"selected\"> " . $name . "</option>");
      }
      else
      {
         print("<option value=\"" . $uid . "\" >" . $name . "</option>");
      }
   }
} 
print("</select></td>\n</tr>\n"); 

fPrintFormSelectBox($owl_lang->group . ":" , "filtergroup", $groups, $filtergroup);
fPrintFormTextLine("File Name:" , "filter_file", 60, $filter_file);
fPrintFormTextLine("Date From (YYYY-MM-DD HH:MM:SS):" , "filter_from", 20, $filter_from);
fPrintFormTextLine("Date to   (YYYY-MM-DD HH:MM:SS):" , "filter_to", 20, $filter_to);


// print Hide columns
print("<tr>\n<td class=\"form1\">$owl_lang->owl_log_hide $owl_lang->owl_log_hd_agent:</td>\n");
if ($hideagent == 1)
{
   print("<td class=\"form1\" width=\"100%\"><input type=\"checkbox\" name=\"hideagent\" value=\"1\" checked=\"checked\"></input></td>\n");
}
else
{
   print("<td class=\"form1\" width=\"100%\"><input type=\"checkbox\" name=\"hideagent\" value=\"1\" ></input></td>\n");
}

print("</tr>\n");
print("<tr>\n<td class=\"form1\">$owl_lang->owl_log_hide $owl_lang->owl_log_hd_dtls::</td>\n");
if ($hidedetail == 1)
{
   print("<td class=\"form1\" width=\"100%\"><input type=\"checkbox\" name=\"hidedetail\" value=\"1\" checked=\"checked\"></input></td>\n");
}
else
{
   print("<td class=\"form1\" width=\"100%\"><input type=\"checkbox\" name=\"hidedetail\" value=\"1\" ></input></td>\n");
}
print("</tr>\n");

//print("<tr>\n<td>\n");
$fa = urlencode(serialize($filteraction));
print("<tr class=\"form2\">\n");
print("<td class=\"form2\">&nbsp;</td>\n");
print("<td class=\"form2\">\n");
print("<table><tr><td align=\"left\">");
fPrintSubmitButton($owl_lang->owl_log_filter, $owl_lang->alt_refresh_filter, "submit", "myaction");
print("</td>\n");
print("<td>\n");
print("<table>\n<tr>\n");
print("<td class=\"button1\">");
print("<img src=\"$default->owl_graphics_url/$default->sButtonStyle/icon_filetype/pdf.gif\"></img>");
print("</td>");
print("<td class=\"button1\">");
print("<a class=\"lbutton1\" href=\"log.php?sess=$sess&amp;action=gen_pdf&amp;next=0&amp;nextrecord=0&amp;fa=$fa&amp;filter_to=$filter_to&amp;filter_from=$filter_from&amp;filter_file=$filter_file&amp;filteruser=$filteruser&amp;filtergroup=$filtergroup&amp;hideagent=$hideagent&amp;hidedetail=$hidedetail\" title=\"$owl_lang->alt_gen_pdf\">&nbsp;" . $owl_lang->btn_gen_pdf . "&nbsp;</a>");
print("</td>\n");
print("</tr>\n");
print("</table>\n");
print("</td></tr></table>");
//</td>\n</tr>\n</table>\n</form>\n"); 

// print Footer with Record Count and PREV TOP NEXT

print("<table>\n");
print("<tr>\n");
print("<td><a href=\"log.php?sess=$sess&amp;prev=1&amp;nextrecord=$nextrecord&amp;fa=$fa&amp;filter_to=$filter_to&amp;filter_from=$filter_from&amp;filter_file=$filter_file&amp;filteruser=$filteruser&amp;filtergroup=$filtergroup&amp;hideagent=$hideagent&amp;hidedetail=$hidedetail\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_nav/prev.gif\" border=\"0\" alt=\"$owl_lang->alt_log_prev\" title=\"$owl_lang->alt_log_prev\"></img></a></td>\n");
print("<td><a href=\"log.php?sess=$sess&amp;next=0&amp;nextrecord=0&amp;fa=$fa&amp;filter_to=$filter_to&amp;filter_from=$filter_from&amp;filter_file=$filter_file&amp;filteruser=$filteruser&amp;filtergroup=$filtergroup&amp;hideagent=$hideagent&amp;hidedetail=$hidedetail\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_nav/top.gif\" border=\"0\" alt=\"$owl_lang->alt_log_top\" title=\"$owl_lang->alt_log_top\"></img></a></td>\n");
$from = $nextrecord + 1;
if ($recordcount == 0)
   $from = $recordcount;
$to = $nextrecord + $default->log_rec_per_page;
if ($to > $recordcount)
   $to = $recordcount;
if ($to < $recordcount)
{
   print("<td><a href=\"log.php?sess=$sess&amp;next=1&amp;nextrecord=$nextrecord&amp;fa=$fa&amp;filter_to=$filter_to&amp;filter_from=$filter_from&amp;filter_file=$filter_file&amp;filteruser=$filteruser&amp;filtergroup=$filtergroup&amp;hideagent=$hideagent&amp;hidedetail=$hidedetail\"><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_nav/next.gif\" border=\"0\" alt=\"$owl_lang->alt_log_next\" title=\"$owl_lang->alt_log_next\"></img></a></td>\n");
} 
else
{
   print("<td align=\"left\" ><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/transparent.gif\" border=\"0\" alt=\"\"></img></td>\n");
} 
print("<td align=\"left\" ><a href=\"log.php?sess=$sess&amp;action=clear_log\" onclick='return confirm(\"$owl_lang->reallydelete_logs ?\");'><img src=\"$default->owl_graphics_url/$default->sButtonStyle/ui_misc/log_delete.gif\" border=\"0\" alt=\"$owl_lang->alt_log_clear\" title=\"$owl_lang->alt_log_clear\"></img></a></td>\n");

print("<td>&nbsp;($from $owl_lang->log_admin_to $to) $owl_lang->log_admin_of $recordcount &nbsp;</td>\n");
print ("</tr>");
print("</table>\n");
print("</td>\n");
print("</tr>\n");
print("</table>\n</form>\n");

print("</td></tr></table>\n");
fPrintButtonSpace(12, 1);


if ($default->show_prefs == 2 or $default->show_prefs == 3)
{
fPrintPrefs("infobar2");
}

print("</td></tr></table>\n");
include($default->owl_fs_root . "/lib/footer.inc");
?>
