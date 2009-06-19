<?php

$CountLines = 0;
$sql = new Owl_DB;
$sql2 = new Owl_DB;
$sql->query("SELECT * FROM $default->owl_folders_table");
while ($sql->next_record())
{
   $formdocid = "documentid_" . $sql->f("id");
   if (${$formdocid} > 0)
   {
      $documentid = ${$formdocid};
      break;
   }
}

$sql->query("SELECT * FROM $default->owl_folders_table order by name");

print("<tr>\n");
print("<td align=\"left\" colspan=\"3\">&nbsp;</td>\n");
print("<td align=\"left\">&nbsp;</td>\n");
print("</tr>\n");

// 
// User File Stats BEGIN
// 
print("<tr>\n");
print("<td class=\"admin2\" align=\"left\" colspan=\"4\">MASTER Document List</td>\n");
print("<td align=\"left\">&nbsp;</td>\n");
print("</tr>\n");
print("<tr>\n");
print("<td align=\"left\" colspan=\"3\">&nbsp;</td>\n");
print("<td align=\"left\">&nbsp;</td>\n");
print("</tr>\n");

if (!empty($documentid))
{
   print("<tr>\n");
   print("<td class=\"admin2\" align=\"left\" colspan=\"4\">Users with Download Access to: " . flid_to_name($documentid) ."</td>\n");
   print("<td align=\"left\">&nbsp;</td>\n");
   print("</tr>\n");
   print("<tr>\n<td class=\"title1\" nowrap=\"nowrap\">Username</td>\n");
   print("<td colspan=\"3\" class=\"title1\">Name</td>\n</tr>");
                                                                                                                                                                                      
   $sql2->query("SELECT * FROM $default->owl_users_table");
                                                                                                                                                                                      
   $fid = $documentid;
   while ($sql2->next_record())
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
      $uid = $sql2->f("id");
      if (check_auth($fid, "file_download", $uid, true) == 1)
      {
         print("<tr>\n<td class=\"$sTrClass\" nowrap=\"nowrap\">" . $sql2->f("username") . "</td>\n");
         print("<td class=\"$sTrClass\" colspan=\"3\">"  . $sql2->f("name") . "</td>\n</tr>");
      }
   }
   print("<tr>\n<td class=\"admin2\" colspan=\"4\" nowrap=\"nowrap\">&nbsp;</td>\n");
   print("</tr>");


   print("<tr>\n");
   print("<td align=\"left\" colspan=\"4\">&nbsp;</td>\n");
   print("</tr>\n");
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
      
   print("\t\t\t\t<tr>\n");
   print("<td class=\"$sTrClass\">Folder: </td>\n");
   print("<td class=\"$sTrClass\">" . $sql->f("name") . "</td>\n");
   print("<td width=\"100%\" class=\"$sTrClass\" colspan=\"2\">");
   $sql2->query("SELECT * FROM $default->owl_files_table WHERE approved='1' AND parent ='" .$sql->f("id") ."'");
   print("<select class=\"fpull1\" name=\"documentid_" . $sql->f("id") ."\" size=\"1\" onchange=\"javascript:this.form.submit();\">\n");
   print("<option value=\"0\">--- Select a Document ---</option>\n");
   while ($sql2->next_record())
   {
      print("<option value=\"" . $sql2->f("id") ."\">" . $sql2->f("name") ." (". $sql2->f("filename") .")</option>\n");
   }
   print("</select>\n");
   print("</td>\n");
   print("</tr>\n");
} 

?>
