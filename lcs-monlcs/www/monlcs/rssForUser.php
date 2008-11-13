<?
include "includes/secure_no_header.inc.php";


$sq = "SELECT *FROM monlcs_db.ml_rss where user ='$uid'";
$c = mysql_query($sq) or die("ERR $sq");
if (mysql_num_rows($c) !=0 ) {
  	$pos = 0;
	for ($x=0;$x<mysql_num_rows($c);$x++) {
	$R = mysql_fetch_object($c);
	
	
	

	$place = $pos % 3 + 1;
	$content.="createARSSBox('$R->url',$place,false,5); ";
	$pos++;
	

	}

	
	}

	


else {
$sql ="SELECT * FROM monlcs_db.ml_ressources where RSS_template <> 'NULL'  and RSS_template <> '' and statut='public';";
$c = mysql_query($sql) or die ("ERR $sql");
if ($c) {

$pos = 0;

while ($R = mysql_fetch_object($c)) { 

$place = $pos % 3 + 1;

$content.="createARSSBox('$R->url',$place,false,5); ";
$pos++;
}

}
}


echo $content;
?>