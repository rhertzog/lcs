<?php
include "includes/secure_no_header.inc.php";
extract($_POST);


$content = "document.getElementById('content').style.display='none';";

$sql = "select * from monlcs_db.ml_scenarios where id_scen ='$id_scen';";
//$content .= $sql;

$c2=mysql_query($sql) or die(stringForJavascript("ERR $sql"));

	for ($x=0;$x<mysql_num_rows($c2);$x++) {
		$idR = mysql_result($c2,$x,'id_ressource');
		$posx = mysql_result($c2,$x,'x');
		$posy = mysql_result($c2,$x,'y');
		$zI = mysql_result($c2,$x,'z');
		$min = mysql_result($c2,$x,'min');
		$width = mysql_result($c2,$x,'w');
		$height = mysql_result($c2,$x,'h');
		$type = mysql_result($c2,$x,'type');
		$auteur = mysql_result($c2,$x,'setter');

		if ($type == 'ressource') {
		//chercher url ...
		$sql3 = "select * from ml_ressources where id=$idR;";
		$c3 = mysql_query($sql3) or die(stringForJavascript("ERR $sql3"));
		if ( mysql_num_rows($c3) != 0 ) {
			
			$url = mysql_result($c3,0,'url');
			if (eregi('.swf',$url))
				$url='giveCleanFlash.php?url='.$url;
			$url_vignette = mysql_result($c3,0,'url_vignette');

			$owner = trim(mysql_result($c3,0,'owner'));
			$titre = stringForJavascript(mysql_result($c3,0,'titre'));
			$tp_rss = mysql_result($c3,0,'RSS_template');
			
			
		
		if ( $tp_rss != 'null') { 
		if ($tp_rss =='RSS_img'){
		$TEMP ="rss2html/template.html";
		} else {
		$TEMP ="rss2html/template_no_img.html";
		}
		$url = "rss2html/rss2html.php?XMLFILE=$url&TEMPLATE=$TEMP";
		}//if rss	
		}
		
		if ($url) {	

		if ($url_vignette != null) {
			if (eregi('thumbalizr',$url_vignette))
				$urlAffiche = 'giveCleanVignette.php?url='.$url;
			else
				$urlAffiche = $url_vignette;
		}
		else
			$urlAffiche = $url;

		

		$content .= "var ajaxWind$idR=dhtmlwindow.open('ajaxWind$idR','iframe','$urlAffiche','$titre',";
		$content .= "'width=$width"."px".",height=$height"."px".",left=$posx"."px".",top=$posy"."px".",";
		$content .= "resize=1,scrolling=1,center=0'";
		$content .= ");";
		
		$content .= "inhibit_close(ajaxWind$idR);";
			
		
		if ( $min == 'Y' ) {
			$content .= "mini(ajaxWind$idR);";
			$content .= "ajaxWind$idR.style.zIndex=90;";
			$content .= "ajaxWind$idR.zIndexvalue=90;";

		} else {
			$content .= "ajaxWind$idR.style.zIndex=$zI;";
			$content .= "ajaxWind$idR.zIndexvalue=$zI;";
		}
		}		

		
				
		}// if ressource
		
		if ($type == 'note') {
			$sql3 = "select * from monlcs_db.ml_notes where id='$idR'";
			$c3 = mysql_query($sql3) or die(stringForJavascript("ERR $sql3"));
			if ( mysql_num_rows($c3) != 0 ) {
				$leTit = stringForJavascript(mysql_result($c3,0,'titre'));
				$setter = trim(mysql_result($c3,0,'setter'));
				$m = mysql_result($c3,0,'msg');
				$m = stripCslashes($m);
				$m = addCslashes($m,chr(39));

				$content .= "var ajaxWindNote$idR=dhtmlwindow.open('ajaxWindNote$idR','inline','$m','$leTit',";
				$content .= "'width=$width"."px".",height=$height"."px".",left=$posx"."px".",top=$posy"."px".",";
				$content .= "resize=1,scrolling=1,center=0'";
				$content .= ");";
				$content .= "inhibit_close(ajaxWindNote$idR);";

				
				if ( $min == 'Y' ) {
					$content .= "mini(ajaxWindNote$idR);";
					$content .= "ajaxWindNote$idR.style.zIndex=90;";
					$content .= "ajaxWindNote$idR.zIndexvalue=90;";

				} else {
					$content .= "ajaxWindNote$idR.style.zIndex=$zI;";
					$content .= "ajaxWindNote$idR.zIndexvalue=$zI;";
				}

				
		     	}
		
		}//if note

		}
	
	
	print stringForJavascript($content);
	

?>
