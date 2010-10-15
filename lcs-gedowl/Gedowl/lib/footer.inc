<?php
/*

  File: footer.inc
  Author: Chris
  Date: 2000/12/14

  Owl: Copyright Chris Vincent <cvincent@project802.net>

  You should have received a copy of the GNU Public
  License along with this package; if not, write to the
  Free Software Foundation, Inc., 59 Temple Place - Suite 330,
  Boston, MA 02111-1307, USA.

*/

if(!isset($sortname)) 
{
   $sortname = "ASC";
}

//if(!isset($bDisplayFooterTools))
//{
   //$bDisplayFooterTools = false;
//}

print("<!-- BEGIN: Footer -->\n");
print("<a name=\"bottom\"></a>\n");

if ($_SERVER["PHP_SELF"] != $default->owl_root_url . "/index.php")
{
   print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
   print("<tr>\n");
   if ($default->debug === true)
   {
      print("<td class=\"owlbar1\" align=\"right\"><a class=\"lbutton1\" href=\"http://validator.w3.org/check?uri=referer\">$owl_lang->engine $default->version</a></td>\n");
   }
   else
   {
      print("<td class=\"owlbar1\" align=\"right\">$owl_lang->engine $default->version</td>\n");
   }
   print("</tr>\n");
   print("</table>\n");
}
else
{
   print("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n");
   print("<tr>\n<td class=\"owlbar1\" align=\"right\">&nbsp;</td>\n</tr>\n");
   print("</table>\n");
}

include_once($default->owl_fs_root . "/lib/userfooter.inc");

print("<!-- END: Footer -->\n");

print("</td></tr></table></center>");
?>
</body>
</html>
