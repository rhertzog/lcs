<?php
/**
 * admin_save_mysql.php
 * Script de sauvegarde de la base de donn�e mysql
 * Ce script fait partie de l'application GRR
 * Derni�re modification : $Date: 2009-12-16 14:52:31 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: admin_save_mysql.php,v 1.8 2009-12-16 14:52:31 grr Exp $
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
 * $Log: admin_save_mysql.php,v $
 * Revision 1.8  2009-12-16 14:52:31  grr
 * *** empty log message ***
 *
 * Revision 1.7  2009-10-09 07:55:48  grr
 * *** empty log message ***
 *
 * Revision 1.5  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.4  2008-11-16 22:00:58  grr
 * *** empty log message ***
 *
 *
 */

/*
MOT DE PASSE
------------
  L'ex�cution de ce script requiert un mot de passe d�fini dans l'interface en ligne de GRR

*/


$grr_script_name = "admin_save_mysql.php";

if ((!isset($_GET['mdp'])) and (!isset($argv[1])) and (!isset($_GET['flag_connect']))) {
    echo "Il manque des arguments pour executer ce script. Reportez-vous a la documentation.";
    die();
}


if ((!isset($_GET['mdp'])) and isset($argv[1]))
    $_GET['mdp']=$argv[1];


if (isset($_GET['mdp'])) {
    include "include/connect.inc.php";
    include "include/config.inc.php";
    include "include/misc.inc.php";
    include "include/functions.inc.php";
    include "include/$dbsys.inc.php";
    //include "include/language.inc.php"; // Inutile d'inclure ce fichier.
    include("include/settings.inc.php");
    if (!loadSettings())
        die("Erreur chargement settings");

    if ((($_GET['mdp'] != getSettingValue("motdepasse_backup")) or (getSettingValue("motdepasse_backup")==''))) {
        if (!isset($argv[1]))
            echo begin_page("backup",$page="no_session")."<p>";
        echo "Le mot de passe fourni est invalide.";
        if (!isset($argv[1])) {
            echo "</p>";
            include "include/trailer.inc.php";
        }
        die();
    }
} else {
    include "include/admin.inc.php";
    $back = '';
    if (isset($_SERVER['HTTP_REFERER'])) $back = htmlspecialchars($_SERVER['HTTP_REFERER']);
    if(authGetUserLevel(getUserName(),-1) < 6)
    {
        $day   = date("d");
        $month = date("m");
        $year  = date("Y");
        showAccessDenied($day, $month, $year, '',$back);
        exit();
    }
}

function php_version()
{
   preg_match('`([0-9]{1,2}).([0-9]{1,2})`', phpversion(), $match);
   if (isset($match) && !empty($match[1]))
   {
      if (!isset($match[2])) $match[2] = 0;
   }
   if (!isset($match[3])) $match[3] = 0;
   return $match[1] . "." . $match[2] . "." . $match[3];
}

function mysql_version()
{
   $result = mysql_query('SELECT VERSION() AS version');
   if ($result != FALSE && @mysql_num_rows($result) > 0)
   {
      $row = mysql_fetch_array($result);
      $match = explode('.', $row['version']);
   }
   else
   {
      $result = @mysql_query('SHOW VARIABLES LIKE \'version\'');
      if ($result != FALSE && @mysql_num_rows($result) > 0)
      {
         $row = mysql_fetch_row($result);
         $match = explode('.', $row[1]);
      }
   }

   if (!isset($match) || !isset($match[0])) $match[0] = 3;
   if (!isset($match[1])) $match[1] = 21;
   if (!isset($match[2])) $match[2] = 0;
   return $match[0] . "." . $match[1] . "." . $match[2];
}

$nomsql = $dbDb."_le_".date("Y_m_d_\a_H\hi").".sql";
$now = date('D, d M Y H:i:s') . ' GMT';

header('Content-Type: text/x-csv');
header('Expires: ' . $now);
// lem9 & loic1: IE need specific headers
if (isset($_SERVER['HTTP_USER_AGENT'])) {
if (preg_match('`MSIE`', $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: inline; filename="' . $nomsql . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
} else {
    header('Content-Disposition: attachment; filename="' . $nomsql . '"');
    header('Pragma: no-cache');
}
}
$fd = '';

    $fd.="#**************** BASE DE DONNEES ".$dbDb." ****************"."\n"
   .date("\#\ \L\e\ \:\ d\ m\ Y\ \a\ H\h\ i")."\n";
if (isset($_SERVER['SERVER_NAME'])) $fd.="# Serveur : ".$_SERVER['SERVER_NAME']."\n";
    $fd.="# Version PHP : " . php_version()."\n";
    $fd.="# Version mySQL : " . mysql_version()."\n";
    $fd.="# Version GRR : " . affiche_version()."\n";
if (isset($_SERVER['REMOTE_ADDR'])) $fd.="# IP Client : ".$_SERVER['REMOTE_ADDR']."\n";
    $fd.="# Fichier SQL compatible PHPMyadmin\n#\n";
    $fd.="# ******* debut du fichier ********\n";
    $j = '0';
    while ($j < count($liste_tables)) {
        $temp = $table_prefix.$liste_tables[$j];
            if ($structure) {
                $fd.="#\n# Structure de la table $temp\n#\n";
                $fd.="DROP TABLE IF EXISTS `$temp`;\n";
                // requete de creation de la table
                $query = "SHOW CREATE TABLE $temp";
                $resCreate = mysql_query($query);
                $row = mysql_fetch_array($resCreate);
                $schema = $row[1].";";
                $fd.="$schema\n";
            }
			#On ne sauvegarde pas les donn�es de la table ".TABLE_PREFIX."_log
            if ($donnees and $temp!="".TABLE_PREFIX."_log") {
                // les donn�es de la table
                $fd.="#\n# Donn�es de $temp\n#\n";
                $query = "SELECT * FROM $temp";
                $resData = @mysql_query($query);
                //peut survenir avec la corruption d'une table, on pr�vient
                if (!$resData) {
                    $fd.="Probl�me avec les donn�es de $temp, corruption possible !\n";
                } else {
                    if (@mysql_num_rows($resData) > 0) {
                        $sFieldnames = "";
                        $num_fields = mysql_num_fields($resData);
                        if ($insertComplet) {
                            for($k=0; $k < $num_fields; $k++) {
                                $sFieldnames .= "`".mysql_field_name($resData, $k) ."`";
                                //on ajoute � la fin une virgule si n�cessaire
                                if ($k<$num_fields-1) $sFieldnames .= ", ";
                            }
                            $sFieldnames = "($sFieldnames)";
                        }
                        $sInsert = "INSERT INTO $temp $sFieldnames values ";
                        while($rowdata = mysql_fetch_row($resData)) {
                            $lesDonnees = "";
                            for ($mp = 0; $mp < $num_fields; $mp++) {
                                $lesDonnees .= "'" . mysql_real_escape_string($rowdata[$mp]) . "'";
                                //on ajoute � la fin une virgule si n�cessaire
                                if ($mp<$num_fields-1) $lesDonnees .= ", ";
                            }
                            $lesDonnees = "$sInsert($lesDonnees);\n";
                            $fd.="$lesDonnees";
                        }
                    }
                }
            }

    $j++;
    }
    $fd.="#********* fin du fichier ***********";

echo $fd;
?>