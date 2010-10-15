<?php
/**
 * user_inactive.php
 * 
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 * 
 * $Id: user_inactive.php,v 1.1.1.1 2005/11/06 15:40:59 b0zz Exp $
 */


$CountLines = 0;
$sql = new Owl_DB;
if (empty($since))
{
   $since = $sql->now();
}
else
{
  $since = date("Y-m-d H:i:s", strtotime($since));
  $since = "'" . $since . "'";
}

$sql->query("SELECT * from $default->owl_users_table WHERE lastlogin < $since ORDER BY name");

if (empty($export))
{
   print("<tr>\n");
   print("<td class=\"form1\">$owl_lang->report_filter_since</td>\n");
   print("<td colspan=\"3\" class=\"form1\" width=\"100%\">");
   print("<input type=\"text\" name=\"since\" value=\"" . ereg_replace("'", "",$since) ."\"></input>");
   fPrintSubmitButton($owl_lang->btn_submit, "Submit");
   fPrintSubmitButton($owl_lang->btn_export , "Export", "submit", "export");
   print("</td>");
   print("</tr>\n");
   
   print("<tr>\n");
   print("<td align=\"left\" colspan=\"3\">&nbsp;</td>\n");
   print("<td align=\"left\">&nbsp;</td>\n");
   print("</tr>\n");
   print("<tr>\n");
   
   // 
   // User File Stats BEGIN
   // 
   
   print("<td class=\"admin2\" align=\"left\" colspan=\"4\">$owl_lang->report_users_inactive_title</td>\n");
   print("<td align=\"left\">&nbsp;</td>\n");
   print("</tr>\n");
   print("<tr>\n");
   print("<td align=\"left\" colspan=\"3\">&nbsp;</td>\n");
   print("<td align=\"left\">&nbsp;</td>\n");
   print("</tr>\n");
   print("<tr>\n");
   print("<td align=\"left\" class=\"title1\">$owl_lang->name</td>\n");
   print("<td align=\"left\" class=\"title1\">$owl_lang->username</td>\n");
   print("<td align=\"left\" colspan=\"2\" width=\"100%\" class=\"title1\">$owl_lang->last_logged</td>\n");
   print("</tr>\n");
}
else
{
   header( 'Pragma: ' );
   header( 'Cache-Control: ' );
   header( 'Content-Type: application/vnd-ms.excel' );
   $aDate = getdate();
   $sExportFilename = 'User_Inactive_' . $aDate[ 'month' ] . '_' . $aDate[ 'mday' ] . '_' . $aDate[ 'year' ] . '.xls';
   header( 'Content-Disposition: attachment; filename="' . $sExportFilename . '"' );
   print($owl_lang->name . "\t");
   print($owl_lang->username . "\t");
   print($owl_lang->last_logged . "\t\n");
}
   
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
      
   if (empty($export))
   {
      print("\t\t\t\t<tr>\n");
      print("<td class=\"$sTrClass\">" . $sql->f("name") . "</td>\n");
      print("<td class=\"$sTrClass\">" . $sql->f("username") . "</td>\n");
      print("<td class=\"$sTrClass\" colspan=\"2\">" .  date($owl_lang->localized_date_format, strtotime($sql->f("lastlogin"))) . "</td>\n");
      print("</tr>\n");
   }
   else
   {
      print($sql->f("name") . "\t");
      print($sql->f("username") . "\t");
      print(date($owl_lang->localized_date_format, strtotime($sql->f("lastlogin"))) . "\t\n");
   }

} 

// 
// User File Stats END
?>
