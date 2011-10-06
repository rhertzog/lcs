<?php
/**
 * admin_open_mysql.php
 *
 * Derni�re modification : $Date: 2009-06-04 15:30:17 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @author    Sylvain Payeur
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @copyright Copyright 2008 Sylvain Payeur
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   admin
 * @version   $Id: admin_open_mysql.php,v 1.6 2009-06-04 15:30:17 grr Exp $
 * @filesource
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GRR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GRR; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * $Log: admin_open_mysql.php,v $
 * Revision 1.6  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.5  2009-02-27 22:05:03  grr
 * *** empty log message ***
 *
 * Revision 1.4  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 * Revision 1.3  2008-11-06 21:57:34  grr
 * *** empty log message ***
 *
 * Revision 1.2  2008-11-05 15:04:16  grr
 * *** empty log message ***
 *
 *
 */
include "include/admin.inc.php";
$grr_script_name = "admin_open_mysql.php";

if(authGetUserLevel(getUserName(),-1) < 6)
{
    $day   = date("d");
    $month = date("m");
    $year  = date("Y");
    showAccessDenied($day, $month, $year, '',$back);
    exit();
}

$sql_file = isset($_FILES["sql_file"]) ? $_FILES["sql_file"] : NULL;
$file_name = isset($_GET["file_name"]) ? $_GET["file_name"] : NULL;

// Restriction dans le cas d'une d�mo
VerifyModeDemo();

if (!$file_name) {
	print_header("","","","",$type="with_session", $page="admin");
}
else {
	echo begin_page("","no_session");
}
echo "<div class=\"page_sans_col_gauche\">";
echo "<br />";

#Si aucun fichier n'a �t� s�lectionn�
if (!$file_name and !$sql_file['name'])
	exit (get_vocab("admin_import_users_csv11")."<br /><a href=\"admin_config.php?page_config=4\">".get_vocab("back")."</a></div></body></html>");

if (!$file_name) {
  #Demande de confirmation
  echo "<h3>Restauration � partir du ficher : " ;
  echo $sql_file['name']."</h3>\n";
  $file_name= str_replace("\\","/",dirname($sql_file['tmp_name'])."/".$sql_file['name']);
  $ok = @copy($sql_file['tmp_name'],$file_name);
  $file = fopen($file_name, "r") or exit("Unable to open file!");
  $line = fgets($file);
  #V�rifie si c'est bien un fichier Grr
  if (!stristr($line,'#**************** BASE DE DONNEES')) {
    fclose($file);
    echo "<span class=\"avertissement\">Il ne s'agit pas d'un fichier de sauvegarde GRR.</span><br />";
  } else {
    #Affiche les infos
    echo "<table border=\"1\" cellpadding=\"10\"><tr><td>";
    echo "Version : ".substr($line,34,strpos(substr($line,34),"*"))."<br />";
    for ($i=1; $i<6; $i++) {
      $line = substr(fgets($file),2);
      echo $line."<br />";
    }
    fclose($file);
    echo "</td></tr></table>";
  }
  echo "<h1>" .  get_vocab("sure") . "</h1>";
  echo "<h1><a href=\"admin_open_mysql.php?file_name=$file_name\">" . get_vocab("YES") . "!</a> &nbsp;&nbsp;&nbsp; <a href=\"admin_config.php?page_config=4\">" . get_vocab("NO") . "!</a></h1>";
  echo "</div>";
} else {
  #Proc�de � la restauration
  $file = fopen($file_name, "r") or exit("Erreur de lecture de fichier!");
  $ok="";
  $error = "";
  while(!feof($file)) {
    $line = fgets($file);
    while ($line[0]!='#' and !stristr($line,';') and !feof($file))
    $line .= fgets($file);
    if (grr_sql_query($line)) {
       $ok.="1";
    } else {
       $ok.="0";
       $error .="<hr />".htmlspecialchars($line);
    }
  }
  fclose($file);
  unlink($file_name);

  echo "<h3>La restauration est termin�e !</h3>" ;
  echo strlen($ok)." requ�tes ont �t� ex�cut�es " ;
  if (strrpos($ok,'0')) {
    echo "avec ".substr_count($ok,'0')." erreur(s) :";
    echo $error."<hr />";
  }	else {
    echo "sans erreurs.";
  }
  echo "<br /><a href=\"login.php\">".get_vocab("msg_logout3")."</a></div>";
}
?>
</body>
</html>