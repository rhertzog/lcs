<?php

/**
 * stats.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 */

require_once(dirname(dirname(__FILE__)) . "/config/owl.php");
require_once($default->owl_fs_root . "/lib/Net_CheckIP/CheckIP.php");
require_once($default->owl_fs_root . "/lib/disp.lib.php");
require_once($default->owl_fs_root . "/lib/owl.lib.php");
require_once($default->owl_fs_root . "/lib/security.lib.php");

if (empty($export))
{
   include_once($default->owl_fs_root . "/lib/header.inc");
   include_once($default->owl_fs_root . "/lib/userheader.inc");

   print("<center>\n");
}
if (!fIsAdmin(true) and !fIsReportViewer($userid))
{
   die("$owl_lang->err_unauthorized");
} 
$groups[$i][0] = $sql->f("id");
$groups[$i][1] = $sql->f("name");

$ListOfReports["1"]["0"] = 1;
$ListOfReports["1"]["1"] = "User / Files and Folders Per User";
$ListOfReports["1"]["2"] = "file_activity.php";

$ListOfReports["2"]["0"] = 2;
$ListOfReports["2"]["1"] = "Inactive Users report";
$ListOfReports["2"]["2"] = "user_inactive.php";

//$ListOfReports["3"]["0"] = 3;
//$ListOfReports["3"]["1"] = "File Read/Download access Per Folder";
//$ListOfReports["3"]["2"] = "folder_file_read_access.php";

$ListOfReports["4"]["0"] = 4;
$ListOfReports["4"]["1"] = "User Entitlement Report";
$ListOfReports["4"]["2"] = "user_entilement.php";

$ListOfReports["5"]["0"] = 5;
$ListOfReports["5"]["1"] = "Disabled Users report";
$ListOfReports["5"]["2"] = "user_disabled.php";

if (empty($export))
{
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

   fPrintAdminPanel("viewstats");

   print("<form action=\"stats.php\" method=\"post\">\n");
   print("<input type=\"hidden\" name=\"sess\" value=\"$sess\"></input>");
   print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\">\n");
   print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">");
   print("<tr><td class=\"admin0\" width=\"100%\" colspan=\"19\">$owl_lang->owl_stats_viewer</td></tr>\n");

   print("<td class=\"form1\">$owl_lang->report_available_reports</td>\n");
   print("<td colspan=\"18\" class=\"form1\" width=\"100%\">\n<select class=\"fpull1\" name=\"execreport\" size=\"1\" onchange=\"javascript:this.form.submit();\">\n");
   print("<option value=\"0\">$owl_lang->report_select_report</option>\n");

   foreach ($ListOfReports as $Report)
   {
      print("<option value=\"" . $Report["0"] ."\"");
      if ($execreport == $Report["0"])
      {
         print(" selected=\"selected\"");
      }
      print(">" . $Report["1"]);
      print("</option>\n");
   }
   print("</select>\n");
   print("</td>\n");
}

if (!empty($execreport))
{
   require_once ("reports/" .$ListOfReports["$execreport"]["2"]);
}

if (empty($export))
{
   print("</table>\n");
   print("</td></tr></table>\n");
   print("</form>");
   fPrintButtonSpace(12, 1);


   if ($default->show_prefs == 2 or $default->show_prefs == 3)
   {
      fPrintPrefs("infobar2");
   }
   print("</td></tr></table>\n");

   include($default->owl_fs_root . "/lib/footer.inc");
}
?>
