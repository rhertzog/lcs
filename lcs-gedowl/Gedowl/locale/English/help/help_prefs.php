<?php

require_once("../../../config/owl.php");
require_once("$default->owl_fs_root/lib/disp.lib.php");
require_once("$default->owl_fs_root/lib/owl.lib.php");
require_once("$default->owl_fs_root/lib/security.lib.php");
include_once("$default->owl_fs_root/lib/header.inc");
include_once("$default->owl_fs_root/lib/userheader.inc");


   $urlArgs = array();
   $urlArgs['sess']      = $sess;
   $urlArgs['parent']    = $parent;
   $urlArgs['expand']    = $expand;
   $urlArgs['order']     = $order;
   $urlArgs[$sortorder] = $sortname;

   if(empty($expand))
   {
      $expand = $default->expand;
   }
  
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
         fPrintPrefs("infobar1", "top");
  }
   fPrintButtonSpace(12, 1);
   print("<br />\n");
?>

<center><h1><?php echo $owl_lang->alt_btn_help ?></h1>
<img src="../../../graphics/<? echo $default->sButtonStyle ?>/ui_misc/owl_logo1.gif" border="0" alt="Owl Logo"></img><br /></center>
<hr></hr>

<!-- Help Begins Here -->

<h2><?php echo $owl_lang->preference ?></h2>
Here, all your personal informations are displayed and may be changed if necessary.
<p>The following is displayed</p>

<ul>
<li>In the field &ldquo;<?php echo $owl_lang->title ?>&rdquo; your name is displayed. If it is written incorrectly just fill in your correct name.</li>
<li>In &ldquo;<?php echo $owl_lang->group ?>&rdquo; you see the group where you belong to. All files and folders you create belong to this group if not changed.</li>
<li>The list of groups displayed in &ldquo;<?php echo $owl_lang->groupmember ?>&rdquo; are all groups where you have additional membership.</li>
<li>Owl Intranet is available in more than twenty languages. In the field &ldquo;<?php echo $owl_lang->userlang ?>&rdquo; choose your preferred language. If your language is missing please contact the administrator.</li>
<li>There are different button styles available. Choose whatever you like.</li>
<li>To change you password you have to enter your old password in the field &ldquo;<?php echo $owl_lang->oldpassword ?>&rdquo;.</li>
<li>In the field &ldquo;<?php echo $owl_lang->newpassword ?>&rdquo; enter you new password. Be careful in choosing a good password that contains numbers as well as letters in small and big caps. Don't use any word from that can be found in a dictionary as your password.</li>
<li>It is crucial that your e-mail address is correctly written in the field &ldquo;<?php echo $owl_lang->email ?>&rdquo; as Owl Intranet may send you your password if forgotten. You may change your e-mail address by typing your new one into the field, e.g. user@domain.com.</li>
<li>If you would like to receive notifications about files and folders such as &ldquo;new file uploaded&rdquo; or &ldquo;file updated&rdquo; by e-mail mark the field &ldquo;<?php echo $owl_lang->notification ?>&rdquo;.</li>
<li>If you want to receive your notifications with the file concerned attached to your notification just mark the field &ldquo;<?php echo $owl_lang->attach_file ?>&rdquo;.</li>
<li>Mark the field &ldquo;<?php echo $owl_lang->comment_notif ?>&rdquo; if you want to receive any comments made by other users as e-mail.</li>
<li>The field &ldquo;<?php echo $owl_lang->newsadmin ?>&rdquo; displayes your status concering news administration. If &ldquo;<?php echo $owl_lang->status_yes ?>&rdquo; is displayed you may generate news for the users of your group. These news are then displayed if the members of your group login into Owl Intranet.</li>
</ul>

<p>After you have changed anything in your preferences click on the button &ldquo;<?php echo $owl_lang->change ?>&rdquo;. To reset the form push the button &ldquo;<?php echo $owl_lang->btn_reset ?>&rdquo;.</p>

<!-- Help Ends Here -->

<?php

      fPrintButtonSpace(12, 1);
                                                                                                                                                                                                    
      if ($default->show_prefs == 2 or $default->show_prefs == 3)
      {
         fPrintPrefs("infobar2");
      }
      print("</td></tr></table>\n");
      include("../../../lib/footer.inc");
?>
