<?php
include("../includes/functions.inc.php");
	$p=="finder.png"?$url_=$file:$url_=$_POST['file'];
	$p=="finder.png"?$user_=$user:$user_=$_POST['user'];
	$pat = array();
	$pat[0] = '/core/';
	$pat[1] = '/thumbs/';
	$pat[2] = '/\/home\//';
	$pat[3] = '/public_html\//';
	$rep = array();
	$rep[0] = '..';
	$rep[1] = '';
	$rep[2] = '../~';
	$rep[3] = '';
	$url=preg_replace($pat, $rep, $url_);
	$p=="finder.png"?$urlImg=$url:$urlImg=$url_;

	$path_img = pathinfo($url);
	createThumbs("../images/misc/","../images/misc/thumbs/",150);
/*
				echo $path_img['dirname'], "\n";
				echo $path_img['basename'], "\n";
				echo $path_img['extension'], "\n";
*/
			echo "<div class=\"ui-finder-file-preview file-preview\">"
//			."<h3 class=\"ui-finder-preview-heading\"><span class=\"ui-icon ui-icon-circle-triangle-s\"></span>Pr&eacute;visualiser</h3>"
			."<p class=\"ui-finder-image content_updown down\">"
			."<span class=\"triangle_updown down\">Pr&eacute;visualiser</span>"
			."<img src=\"".$urlImg."\"  id=\"vign_wlpper\" class=\"float_left finder_img\" /></p>";
			echo "<div class=\"img_infos\"><a name=\"moreInfosFile\"></a><h3 class=\"img_name\">".ucfirst(preg_replace('/_/',' ',$path_img['filename']))
			."</h3>"
			."<em>".$path_img['filename'].".".$path_img['extension']."</em>"; // depuis PHP 5.2.0
//			$p=="finder.png"? $padd="padding-left:175px;": $padd="padding-left:0;";
			$padd="padding-left:0;";
			$res.="<div style=\"".$padd."\">";
//			$MetaTags=alt_stat(preg_replace("/core/","..",$url));
//			$eXimg=exif_read_data(preg_replace("/core/","..",$url));
			$MetaTags=alt_stat(preg_replace("/core/","..",preg_replace("/thumbs/","",preg_replace("/..\/~".$user_."/","/home/".$user_."/public_html",$url_))));
			$ext = pathinfo($url ,PATHINFO_EXTENSION);
			$eDate = array('/Feb/','/Apr/','/May/','/Jun/','/Jul/','/Aug/');
			$fDate = array('Fev','Avr','Mai','Juin','Juil','Aout');
			$res.= '<ul>';
			$res.= "<li><img src=\"core/images/app/".$ext.".png\" style=\"width:64px;\" width=\"64\"/></li>";
			$res.= "<li><strong>Taille : </strong>".round(($MetaTags['size']['size']/1000),1)."&nbsp;Ko</li>";
			$res.= "<li><strong>Type : </strong>".mime_content_type(preg_replace("/core/","..",preg_replace("/thumbs/","",preg_replace("/..\/~".$user_."/","/home/".$user_."/public_html",$url_))))."</li>";
			if(mime_content_type(preg_replace("/core/","..",preg_replace("/thumbs/","",preg_replace("/..\/~".$user_."/","/home/".$user_."/public_html",$url_))))=="image/jpeg"){
				$eXimg=exif_read_data(preg_replace("/core/","..",preg_replace("/thumbs/","",preg_replace("/..\/~".$user_."/","/home/".$user_."/public_html",$url_))));
				$resCopy= "<li><strong>Copyright :</strong> ".$eXimg['COMPUTED']['Copyright']."</li>";
				$res.= "<strong>Dim.</strong> : ".$eXimg['COMPUTED']['Width']."x".$eXimg['COMPUTED']['Height']." pixels<br />";
			}
			$res.= $resCopy;
			$res.= '</ul>';
//			echo "<strong>Hauteur</strong> : ".$eXimg['COMPUTED']['Height']." pixels<br />";
$res.= "</div><div class=\"img_infos_more\"><span class=\"triangle_updown\">Plus d'infos</span><ul style=\"display:none;\">";
			$res.= '<ul class=\"content_updown\">';
			$res.= "<li><strong>Cr&eacute;&eacute; le : </strong>".preg_replace($eDate, $fDate,$MetaTags['time']['created'])."</li>";
			$res.= "<li><strong>Derni&egrave;re visite : </strong>".preg_replace($eDate, $fDate,$MetaTags['time']['accessed'])."</li>";
			$res.= "<li><strong>Derni&egrave;re modification : </strong>".preg_replace($eDate, $fDate,$MetaTags['time']['modified'])."</li></ul></div>";
			$res.="</div></div>";
echo $res;
//		print_r($eXimg);

			?>
