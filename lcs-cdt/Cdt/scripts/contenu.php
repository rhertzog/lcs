 <?php
 /* =============================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
   VERSION 2.4 du 22/03/2012
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - contenu.php -
   			_-=-_
  * "Valid XHTML 1.0 Strict"
   ============================================= */
 session_name("Cdt_Lcs");
@session_start();

if ((!isset($_SESSION['version'])) || (!isset( $_SESSION['saclasse']) && !isset($_SESSION['login'])) && !isset($_SESSION['aliasprof'])) exit;
 //elaboration des dates limites
$dat_now=date('YmdHis');
if (mb_ereg('eleve', $_SERVER['REQUEST_URI']))
    {
    $dlm1=" AND date>='".$dat."'";
    $dlm2=" AND datevisibi<='$dat_now'";
    }
 elseif (mb_ereg('prof_ro', $_SERVER['REQUEST_URI']))
    {
    $dlm1="";
    $dlm2=" AND datevisibi<='$dat_now'";
    }
 elseif (mb_ereg('prof\.php', $_SERVER['REQUEST_URI']))  $dlm1=$dlm2="";
 else exit;
include_once("/usr/share/lcs/Plugins/Cdt/Includes/fonctions.inc.php");
$cib=$cible; $i=$j=$k=0;$list_art=$list_seq=$Sekance=array();
// Connexion a la base de donnees Cdt
include ('../Includes/config.inc.php');
$rq = "SELECT id_seq  FROM sequences WHERE id_ong='$cib' order by ordre asc ";
 // recuperer les sequences dans l'ordre d affichage
 $result = @mysql_query ($rq) or die (mysql_error());
if (mysql_num_rows($result) >0)
    {
    while ($idr = mysql_fetch_object($result))
        {
        $Sek[$i]= $idr ->id_seq ;
        $i++;
        }
    mysql_free_result($result);
    }
 //dates debut&fin des sequences non vides et comprises ente les dates limites
 for ($index = 0; $index < count($Sek); $index++)
    {
    $rq = "SELECT MAX(date),MIN(date) from cahiertxt  WHERE seq_id='$Sek[$index]'".$dlm1.$dlm2;
    $result = @mysql_query ($rq) or die (mysql_error());
    if (mysql_num_rows($result) >0)
        {
        $r = mysql_fetch_array($result);
        if ($r[0]!=null)
            {
            $der_seq[$k] = $r[0] ;
            $prem_seq[$k] =$r[1] ;
            $Sekance[$k]=$Sek[$index];
            $k++;
            }
        }
    }
////fin for
mysql_free_result($result);
//recherche articles + recents que n et -recents que n-1
for ($index2 = 0; $index2 < count($der_seq); $index2++)
    {
    if ($index2==0) $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib' AND date >= '".$prem_seq[$index2]."'".$dlm1.$dlm2." order by date desc";
    else $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib' AND date >= '".$prem_seq[$index2]."' AND date < '".$prem_seq[$index2-1]."'".$dlm1.$dlm2." order by date desc";
    $result = @mysql_query ($rq) or die (mysql_error());
    if (mysql_num_rows($result) >0)
        {
        while ($row = mysql_fetch_array($result))
            {
            if ( ! in_array($row[0], $list_art))
                {
                $list_art[$j]=$row[0];
                $list_seq[$j]='0';
                $j++;
                }
            }
        }
    $list_art[$j]='0';
    $list_seq[$j]=$Sekance[$index2];
    $j++;
    }
//seances anterieures a la  sequence la plus ancienne
if (isset($prem_seq[$index2-1]))
 $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib' AND  date < '".$prem_seq[$index2-1]."'".$dlm1.$dlm2." order by date desc";
else  $rq="SELECT id_rubrique from cahiertxt where seq_id='0' AND id_auteur='$cib'".$dlm1.$dlm2."  order by date desc";
$result = @mysql_query ($rq) or die (mysql_error());
if (mysql_num_rows($result) >0)
    {
    while ($row = mysql_fetch_array($result))
        {
        if ( ! in_array($row[0], $list_art))
            {
            $list_art[$j]=$row[0];
            $list_seq[$j]='0';
            $j++;
            }
        }
    }
//affichage
if (count($list_seq)>0)
    {
    echo '<table id="tb-cdt" cellpadding="1" cellspacing="2" >';
    for ($index1 = 0; $index1 < count($list_seq); $index1++)
        {
        if ($list_art[$index1]!='0') Affiche_seance ($list_art[$index1]); else Affiche_seq( $list_seq[$index1]);
        }
    echo '</table>';
    }
?>