<?php
/* ==========================================================
   Projet LCS : Linux Communication Server
   Plugin "Biilan2 : Gestion administrative du B2i"
   par Jean-Louis ROSSIGNOL <jean-louis.rossignol@ac-caen.fr>
   et Gilles HILAIRE <gilles.hilaire@ac-caen.fr>   
   ========================================================== */
/*
 * This file is part of GRR.
 */
 
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";

list ($idpers,$username) = isauth();
if (is_admin("Biilan2_is_admin",$username)!="Y"){die("L'acc�s � BiiLan est ouvert, toutefois<br>n'&eacute;tant pas administrateur, vos droits sont restreints<br><br>");}

require ("config.php");
$db=@mysql_connect($dbserver , $dbuser, $dbpass) or die ("erreur de connexion");
mysql_select_db($dbbase,$db) or die ("erreur de connexion base");

$liste_tables = array(
"bii_tbl",
"disc_clg_tbl",
"disc_eco_tbl",
"disc_lyc_tbl",
"disc_tbl",
"dmd_tbl",
"domaines_tbl",
"items_clg_tbl",
"items_eco_tbl",
"items_lyc_tbl",
"profs_tbl",
"resultat_tbl"
);
$structure = true;
$donnees = true;
$insertComplet = false;




function php_version()
{
   ereg('([0-9]{1,2}).([0-9]{1,2})', phpversion(), $match);
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

$nomsql = $dbbase."_le_".date("Y_m_d_\a_H\hi").".sql";
$now = date('D, d M Y H:i:s') . ' GMT';

header('Content-Type: text/x-csv');
header('Expires: ' . $now);
// lem9 & loic1: IE need specific headers
if (ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: inline; filename="' . $nomsql . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
} else {
    header('Content-Disposition: attachment; filename="' . $nomsql . '"');
    header('Pragma: no-cache');
}
$fd = '';

$liste2 = array();
$tableNames = @mysql_list_tables($dbbase);
if (! $tableNames) $fd.="Impossible de lister les tables de la base $dbbase\n";
if ($tableNames) {
    $fd.="#**************** BASE DE DONNEES ".$dbbase." ****************"."\n"
   .date("\#\ \L\e\ \:\ d\ m\ Y\ \a\ H\h\ i")."\n";
    $fd.="# Serveur : ".$_SERVER['SERVER_NAME']."\n";
    $fd.="# Version PHP : " . php_version()."\n";
    $fd.="# Version mySQL : " . mysql_version()."\n";
    $fd.="# IP Client : ".$_SERVER['REMOTE_ADDR']."\n";
    $fd.="# Fichier SQL compatible PHPMyadmin\n#\n";
    $fd.="# ******* debut du fichier ********\n";
    $j = '0';
    while ($j < mysql_num_rows($tableNames)) {
        $liste2[$j] = mysql_tablename($tableNames, $j);
        $j++;
    }
    $j = '0';
    while ($j < count($liste_tables)) {
        $temp = $liste_tables[$j];
        if (in_array($temp, $liste2)) {
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
            if ($donnees) {
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
                                $lesDonnees .= "'" . addslashes($rowdata[$mp]) . "'";
                                //on ajoute � la fin une virgule si n�cessaire
                                if ($mp<$num_fields-1) $lesDonnees .= ", ";
                            }
                            $lesDonnees = "$sInsert($lesDonnees);\n";
                            $fd.="$lesDonnees";
                        }
                    }
                }
            }
        }
    $j++;
    }
    $fd.="#********* fin du fichier ***********";
}
echo $fd;
?>
