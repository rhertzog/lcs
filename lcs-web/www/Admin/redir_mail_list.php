<?php
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS 
   execution requetes jqGrid
   redir_mail_list.php
   Equipe Tice academie de Caen
   25/05/2012 
   Distribue selon les termes de la licence GPL
   ============================================= */

include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ldap.inc.php");
list ($idpers, $login)= isauth();
if (ldap_get_right("lcs_is_admin",$login)!="Y") exit;
//recherche
$wh = "";
$searchOn = $_REQUEST['_search'];
if($searchOn=='true') {
	$fld = $_REQUEST['searchField'];
	$fldata = $_REQUEST['searchString'];
		$foper = $_REQUEST['searchOper'];
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
$oper=$_POST['oper']; 
$del_ids=mb_split(",",$_POST['id']);
for ($i=0;$i<count($del_ids);$i++) {
$SQL = "DELETE from `redirmail` where `id`=".$del_ids[$i];
$result = mysql_query( $SQL ) or die("Couldn t execute query.".mysql_error()); 
}
}else {
//affichage
$page = $_GET['page']; 
$limit = $_GET['rows'];  
$sidx = $_GET['sidx']; 
$sord = $_GET['sord'];  
if(!$sidx) $sidx =1;
$query= "SELECT COUNT(*) AS count FROM `redirmail`"; 
$result=mysql_query($query);
$row = mysql_fetch_array($result,MYSQL_ASSOC); 
$count = $row['count'];
if( $count >0 ) { $total_pages = ceil($count/$limit); } else { $total_pages = 0; } 
if ($page > $total_pages) $page=$total_pages; $start = $limit*$page - $limit; 
$SQL = "SELECT * from `redirmail` where 1 ".$wh." ORDER BY $sidx $sord LIMIT $start , $limit";
$result = mysql_query( $SQL ) or die("Couldn t execute query.".mysql_error()); 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
 header("Content-type: application/xhtml+xml;charset=utf-8"); } 
 else { header("Content-type: text/xml;charset=utf-8"); } 
 $et = ">"; 
 echo "<?xml version='1.0' encoding='utf-8'?$et\n"; 
 echo "<rows>"; 
 echo "<page>".$page."</page>"; 
 echo "<total>".$total_pages."</total>"; 
 echo "<records>".$count."</records>"; 
 while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
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