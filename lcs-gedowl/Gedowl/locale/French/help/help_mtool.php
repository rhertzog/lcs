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

<h2>E-Mail Tool</h2>
The e-mail tool of Owl Intranet lets you send an e-mail with or without a file to anybody.

<p>To send an e-mail you need to do the following:</p>

<ol>
<li>If you got to the e-mail tool by choosing '<?php echo $owl_lang->sendfile?>' you can decide whether to attach the file or just send the weblink to the file.</li>
<li>In the field '<?php echo $owl_lang->email_to  ?>' fill in the correct e-mail address, e.g. user@domain.com. If you want to send your e-mail to another Owl user let the field '<?php echo $owl_lang->email_to  ?>' empty and choose this user from the list.</li>
<li>If you want to send a copy of your mail to another person fill in the address into the field '<?php echo $owl_lang->email_cc  ?>'</li>
<li>Check if your own e-mail address is correctly written in the field '<?php echo $owl_lang->email_reply_to  ?>'. If this is not the case fill in your correct e-mail address and don't forget to update your address in your <?php echo $owl_lang->preference  ?>. It is very important that your correct e-mail address appears in the '<?php echo $owl_lang->email_reply_to  ?>' field as this permits the recipient to answer your mail.</li>
<li>Fill in the subject of your mail into the field '<?php echo $owl_lang->email_subject  ?>'.</li>
<li>In the field '<?php echo $owl_lang->email_body  ?>' write your e-mail. You can write in any language you like. The e-mail will be displayed correctly.</li>
<li>To send your e-mail push the button '<?php echo $owl_lang->btn_send_email  ?>'. You will be brought back to the browser. If you want to reset the whole form click on '<?php echo $owl_lang->btn_reset  ?>'.</li>
</ol>



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
