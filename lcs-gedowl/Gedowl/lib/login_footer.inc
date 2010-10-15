<?php
/*

  File: login_footer.inc
  Author: B0zz
  Date: 2005/11/14

  You should have received a copy of the GNU Public
  License along with this package; if not, write to the
  Free Software Foundation, Inc., 59 Temple Place - Suite 330,
  Boston, MA 02111-1307, USA.

*/

if(!isset($sortname))
{
   $sortname = "ASC";
}

if ($_SERVER["PHP_SELF"] != $default->owl_root_url . "/index.php")
{
   print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n");
   if ($default->debug === false)
   {
      print("<tr><td class=\"version1\" width=\"100%\"><a class=\"version2\" href=\"http://validator.w3.org/check?uri=referer\">" . $owl_lang->engine . ", " . $owl_lang->version . " " . $default->version . "<br></a></td></tr>\n");
   }
   else
   {
      print("<tr><td class=\"version1\" width=\"100%\">" . $owl_lang->engine . ", " . $owl_lang->version . " " . $default->version . "<br /></td></tr>\n");
   }
   print("</table>\n");
}

?>
</center>
</body>
</html>
