<?php
/*
$Id: filtrage_html.inc.php 4344 2010-04-17 11:36:06Z crob $
*/

	$enregistrer_filtrage_html="n";
	$sql="SELECT value FROM setting WHERE name='filtrage_html';";
	$res_filtrage_html=mysql_query($sql);
	if(mysql_num_rows($res_filtrage_html)==0) {
		$filtrage_html='inputfilter';
		$enregistrer_filtrage_html="y";
	}
	else {
		$lig_fh=mysql_fetch_object($res_filtrage_html);
		$filtrage_html=$lig_fh->value;
	}

	if(($filtrage_html!='inputfilter')&&
	($filtrage_html!='pas_de_filtrage_html')) {
		$filtrage_html='inputfilter';
		$enregistrer_filtrage_html="y";
	}

	if($enregistrer_filtrage_html=="y") {
		$sql="DELETE FROM setting WHERE name='filtrage_html';";
		$del_fh=mysql_query($sql);
		$sql="INSERT INTO setting SET name='filtrage_html', value='$filtrage_html';";
		$ins_fh=mysql_query($sql);
	}

?>