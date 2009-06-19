<?php

/**
 * peerreview.php
 * 
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 * 
 */

ob_start();
require_once(dirname(__FILE__)."/config/owl.php");
$out = ob_get_clean();
require_once($default->owl_fs_root ."/lib/disp.lib.php");
require_once($default->owl_fs_root ."/lib/owl.lib.php");
require_once($default->owl_fs_root ."/lib/security.lib.php");
require_once($default->owl_fs_root ."/phpid3v2/class.id3.php");
require_once($default->owl_fs_root ."/scripts/phpmailer/class.phpmailer.php");

if ($sess == "0" && $default->anon_ro > 1)
{
   printError($owl_lang->err_login);
}

// V4B RNG Start
$urlArgs = array();
$urlArgs['sess']      = $sess;
$urlArgs['parent']    = $parent;
$urlArgs['expand']    = $expand;
$urlArgs['order']     = $order;
$urlArgs['sortorder'] = $sortorder;
$urlArgs['curview']     = $curview;
// V4B RNG End

$sql = new Owl_DB;

if ($action == "reminder")
{
   $sql->query("SELECT * from $default->owl_peerreview_table WHERE file_id = '$id' AND status = '0' ");
   while ($sql->next_record())
   {
      notify_reviewer ($sql->f("reviewer_id"), $id, $message, "reminder");
   }
   $urlArgs2 = $urlArgs;
   $urlArgs2['type'] = $type;
   $sUrl = fGetURL ('showrecords.php', $urlArgs2);

   header("Location: " . ereg_replace("&amp;","&", $sUrl));
   exit;
}

if ($action == "publish")
{
   $sql->query("SELECT * from $default->owl_peerreview_table where file_id = '" . $id . "' and status <> '1'");
   if ($sql->num_rows() > 0)
   {
      printError("Sorry This Document has not been Approved Yet");
   }

   $sql->query("SELECT * FROM $default->owl_files_table WHERE id = '$id'");
   $sql->next_record();
   
   notify_users($usergroupid, 0, $sql->f("id"));
   notify_monitored_folders ($sql->f("parent"), $sql->f("filename"));

   $sql->query("UPDATE $default->owl_files_table SET approved = '1' WHERE id = '$id'"); 

   $urlArgs2 = $urlArgs;
   $urlArgs2['type'] = $type;
   $sUrl = fGetURL ('showrecords.php', $urlArgs2);

   header("Location: " . ereg_replace("&amp;","&", $sUrl));
   exit;
}


include_once($default->owl_fs_root ."/lib/header.inc");
include_once($default->owl_fs_root ."/lib/userheader.inc");

printModifyHeader();
print("<br />");

$sql->query("SELECT * from $default->owl_users_table where id = '$userid'");
$sql->next_record();
$default_reply_to = $sql->f("email");

$urlArgs2 = $urlArgs;
$urlArgs2['id']     = $id;
$urlArgs2['action'] = 'docreject';


print("<table class=\"margin2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
print("<tr>\n");
print("<td align=\"left\" valign=\"top\">\n");
print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
print("<form enctype=\"multipart/form-data\" action=\"dbmodify.php\" method=\"post\">\n");
print fGetHiddenFields ($urlArgs2);
print("<tr>\n");
print("<td class=\"form2\" width=\"100%\" colspan=\"2\">&nbsp;");
print("</td>\n");
print("</tr>\n");
                                                                        
fPrintFormTextArea($owl_lang->peer_reject_reason . ":", "reject_reason", "",20,80);
print("<tr>\n");
print("<td class=\"form1\">");
fPrintButtonSpace(1, 1);
print("</td>\n");
print("<td class=\"form2\" width=\"100%\">");
fPrintSubmitButton($owl_lang->btn_send_email, $owl_lang->alt_send_email, "submit", "send_file_x");
fPrintSubmitButton($owl_lang->btn_reset, $owl_lang->alt_reset_form, "reset");
print("</td>\n");
print("</tr>\n");
print("</form>\n");
print("</table>\n");
print("</td></tr></table>\n");
fPrintButtonSpace(12, 1);

if ($default->show_prefs == 2 or $default->show_prefs == 3)
{
   fPrintPrefs("infobar2");
}
print("</td></tr></table>\n");
include($default->owl_fs_root . "/lib/footer.inc");
?>
