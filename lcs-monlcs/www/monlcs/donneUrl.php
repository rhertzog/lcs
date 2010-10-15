<?php
include "includes/secure_no_header.inc.php";

if ($_POST) {
extract($_POST);
$ref=substr($ref,8);
if (eregi('Cmd',$ref)) 
	die(stringForJavascript('rien'));

if (eregi('Note',$ref)) { 
	$ref=substr($ref,4);	
	die(stringForJavascript("maxNote.php?id=$ref"));
	}



if ($tab != 'lcs') {

$sql = "SELECT * from `monlcs_db`.`ml_ressources` WHERE `id` =".$ref."  ;";
$curseur=mysql_query($sql) or die("<ul><li>$sql requete invalide</li></ul>"); 

if (mysql_num_rows($curseur) !=0) {
 $url = mysql_result($curseur,0,'url');
 if (mysql_result($curseur,0,'RSS_template')!='null' ) {
	$rss = mysql_result($curseur,0,'RSS_template');
	if ($rss == 'RSS_img')
		$TEMP = "rss2html/template.html";
	else {
		$TEMP = "rss2html/template_no_img.html";
	}
	$url = "rss2html/rss2html.php?XMLFILE=$url&TEMPLATE=$TEMP";
	}
	
	print(stringForJavascript($url));
} 
} else {
		require ("/var/www/lcs/includes/config.inc.php");
		require_once ("/var/www/lcs/includes/functions.inc.php");
		$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");
		// Liens dynamiques vers les plugins installés 
		$query="SELECT * from applis where id='$ref';";
		$result=mysql_query($query);
		if ($result) {
		while ($r=mysql_fetch_object($result)) {
		$url = '../lcs/statandgo.php?use='.$r->name;
		//$content .= print_r($r);
		}
		}
		print(stringForJavascript($url));
		} 


}
?>
