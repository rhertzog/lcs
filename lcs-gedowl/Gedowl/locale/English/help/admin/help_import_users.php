<?php

require_once("../../../../config/owl.php");
require_once("$default->owl_fs_root/lib/disp.lib.php");
require_once("$default->owl_fs_root/lib/owl.lib.php");
require_once("$default->owl_fs_root/lib/security.lib.php");
include_once("$default->owl_fs_root/lib/header.inc");
include_once("$default->owl_fs_root/lib/userheader.inc");

if ($sess == "0" && $default->anon_ro > 0)
{
   printError($owl_lang->err_login);
}

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
<img src="../../../../graphics/<? echo $default->sButtonStyle ?>/ui_misc/owl_logo1.gif" border="0" alt="Owl Logo"></img><br /></center>
<hr></hr>

<!-- Help Begins Here -->
<h2>User Import</h2>
This option allows the administrator to upload a CVS file that contains the users to be imported.
The script ensures that the user does not exist before inserting the users from the CVS file.

<h3>CVS Example</h3>

<code>
groupid_name,username,name,password,quota_max,quota_current,email,notify,attachfile,disabled,noprefaccess,language,maxsessions,newsadmin,comment_notify,buttonstyle,homedir,firstdir,email_tool
2,user2,User 2,password,0,0,myemail@domain.com,1,0,0,0,English,1,0,1,rsdx_blue1,1,1,1;
newgroup,user3,User 3,password,0,0,myemail@domain.com,1,0,0,0,English,1,0,1,rsdx_blue1,1,1,1;
10,user4,User 4,password,0,0,myemail@domain.com,1,0,0,0,English,1,0,1,rsdx_blue1,1,1,1;
newgroup,user5,User 5,password,0,0,myemail@domain.com,1,0,0,0,English,1,0,1,rsdx_blue1,1,1,1;
newgroup,user1,User 1,password,0,0,myemail@domain.com,1,0,0,0,English,1,0,1,rsdx_blue1,1,1,1;
Administrators,user6,User 6,password,0,0,myemail@domain.com,1,0,0,0,English,1,0,1,rsdx_blue1,1,1,1;
</code>

<h3>Explanation of the Variables</h3>

<table border="1">
<tbody>
<tr>
<td>groupid_name</td>
<td>This column can be a
pre-existing groupid or a string. In the case of an ID a check is done
to the database to ensure that it does in fact exist and if not the
user is skipped. However, in the case of a string, a check is done to
see if the group exist, if
it does this existing group is used to create the user. If it does not
exist the group is created.</td>
</tr>
<tr>
<td>username</td>
<td>A check is done to ensure that
this username does not already exist, if it does the User is skipped.</td>
</tr>
<tr>
<td>name
</td>
<td>Full name of the user.
</td>
</tr>
<tr>
<td>password
</td>
<td>Clear text password.
</td>
</tr>
<tr>
<td>quota_max
</td>
<td>File quota in bytes&nbsp; (0 =
disabled).</td>
</tr>
<tr>
<td>quota_current</td>
<td>0 (obviously this is zero as the
new user has not yet any files on the system)
</td>
</tr>
<tr>
<td>email
</td>
<td>(optional) E-mail address of the
user.
</td>
</tr>
<tr>
<td>notify
</td>
<td>1 = receive notifications, 0 =
disabled
</td>
</tr>
<tr>
<td>attachfile
</td>
<td>1 = attach the file to the email
when notify is turned on, 0 = do not attach the file
</td>
</tr>
<tr>
<td>disabled
</td>
<td>1 = the account is created but
is disabled (i.e. the user cannot login), 0 = account is created and
enabled
</td>
</tr>
<tr>
<td>noprefaccess</td>
<td>1 = the user will not have
access to his preferences, 0 = otherwise
</td>
</tr>
<tr>
<td>language
</td>
<td>Preferred language of the user
(must be installed on the system), e.g. English
</td>
</tr>
<tr>
<td>maxsessions
</td>
<td>Maximum number of concurrent
sessions a user can have (at least 1). Default = 1
</td>
</tr>
<tr>
<td>newsadmin
</td>
<td>1 = user is administrator of Owl
News, 0 = otherwise
</td>
</tr>
<tr>
<td>comment_notify
</td>
<td>1 = user receives notification
when someone adds a comment to one if his files</td>
</tr>
<tr>
<td>buttonstyle
</td>
<td>rsdx_blue1 or one of the
directory name (style) in the graphics directory</td>
</tr>
<tr>
<td>homedir
</td>
<td>The folder id (must exist) of
the folder that will act as the users home directory. Default = 1
</td>
</tr>
<tr>
<td>firstdir
</td>
<td>The folder id (must exist) of
the folder that will act as the users initial directory. Default = 1
</td>
</tr>
<tr>
<td>email_tool</td>
<td>1 = user may use the e-mail
tool, 0 = user may not use the e-mail tool
</td>
</tr>
</tbody>
</table>

<!-- Help Ends Here -->

<?php

      fPrintButtonSpace(12, 1);
                                                                                                                                                                                                    
      if ($default->show_prefs == 2 or $default->show_prefs == 3)
      {
         fPrintPrefs("infobar2");
      }
      print("</td></tr></table>\n");
      include("$default->owl_fs_root/lib/footer.inc");
?>
