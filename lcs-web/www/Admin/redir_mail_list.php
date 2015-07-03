<?php
/*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
   ============================================= */
include "../Annu/includes/check-token.php";
if (!check_acces(1)) exit;

$login=$_SESSION['login'];
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");

if (ldap_get_right("lcs_is_admin",$login)!="Y") exit;
//configuration objet
  include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  $wh = "";
$searchOn = $purifier->purify($_REQUEST['_search']);
if($searchOn=='true') {
	$fld = $purifier->purify($_REQUEST['searchField']);
	$fldata = $purifier->purify($_REQUEST['searchString']);
		$foper = $purifier->purify($_REQUEST['searchOper']);
		// construct where
		$wh .= " AND ".$fld;
		switch ($foper) {
			case "bw":
				$fldata .= "%";
				$wh .= " LIKE '".$fldata."'";
				break;
			case "eq":
				if(is_numeric($fldata)) {
					$wh .= " = ".$fldata;
				} else {
					$wh .= " = '".$fldata."'";
				}
				break;
			case "ne":
				if(is_numeric($fldata)) {
					$wh .= " <> ".$fldata;
				} else {
					$wh .= " <> '".$fldata."'";
				}
				break;
			case "lt":
				if(is_numeric($fldata)) {
					$wh .= " < ".$fldata;
				} else {
					$wh .= " < '".$fldata."'";
				}
				break;
			case "le":
				if(is_numeric($fldata)) {
					$wh .= " <= ".$fldata;
				} else {
					$wh .= " <= '".$fldata."'";
				}
				break;
			case "gt":
				if(is_numeric($fldata)) {
					$wh .= " > ".$fldata;
				} else {
					$wh .= " > '".$fldata."'";
				}
				break;
			case "ge":
				if(is_numeric($fldata)) {
					$wh .= " >= ".$fldata;
				} else {
					$wh .= " >= '".$fldata."'";
				}
				break;
			case "ew":
				$wh .= " LIKE '%".$fldata."'";
				break;
			case "cn":
				$wh .= " LIKE '%".$fldata."%'";
				break;
			default :
				$wh = "";
		}
	}
//suppression
if (isset($_POST['oper'])){
$oper=$purifier->purify($_POST['oper']);
$idp=$purifier->purify($_POST['id']);
$del_ids=mb_split(",",$idp);
for ($i=0;$i<count($del_ids);$i++) {
$SQL = "DELETE from `redirmail` where `id`=".$del_ids[$i];
$result = mysqli_query($GLOBALS["___mysqli_ston"],  $SQL ) or die("Couldn t execute query.".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
}
}else {
//affichage
$page = $purifier->purify($_GET['page']);
$limit = $purifier->purify($_GET['rows']);
$sidx = $purifier->purify($_GET['sidx']);
$sord =$purifier->purify( $_GET['sord']);
if(!$sidx) $sidx =1;
$query= "SELECT COUNT(*) AS count FROM `redirmail`";
$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$count = $row['count'];
if( $count >0 ) { $total_pages = ceil($count/$limit); } else { $total_pages = 0; }
if ($page > $total_pages) $page=$total_pages; $start = $limit*$page - $limit;
$SQL = "SELECT * from `redirmail` where 1 ".$wh." ORDER BY $sidx $sord LIMIT $start , $limit";
$result = mysqli_query($GLOBALS["___mysqli_ston"],  $SQL ) or die("Couldn t execute query.".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
 header("Content-type: application/xhtml+xml;charset=utf-8"); }
 else { header("Content-type: text/xml;charset=utf-8"); }
 $et = ">";
 echo "<?xml version='1.0' encoding='utf-8'?$et\n";
 echo "<rows>";
 echo "<page>".$page."</page>";
 echo "<total>".$total_pages."</total>";
 echo "<records>".$count."</records>";
 while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
 echo "<row id='". $row[id]."'>";
 echo "<cell>". $row[id]."</cell>";
 echo "<cell>". $row[faitpar]."</cell>";
 echo "<cell><![CDATA[". $row[pour]."]]></cell>";
 echo "<cell>". $row[vers]."</cell>";
echo "<cell>". $row[copie]."</cell>";
echo "<cell>". $row[date]."</cell>";
echo "<cell><![CDATA[". $row[remote_ip]."]]></cell>";
echo "</row>"; }
echo "</rows>";
}
?>