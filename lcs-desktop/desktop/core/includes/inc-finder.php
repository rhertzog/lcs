<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 14/03/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
include "/usr/share/lcs/desktop/core/includes/desktop_check.php";
require  "/var/www/lcs/includes/headerauth.inc.php";

	$u!=''?$_u=$u.'/':$_u='';
	$iDir="/home/".$login."/public_html/".$_u;
	$itemList=scandir($iDir);
	$iExts=array('gif','jpg','png');
	$res="<ol>";
	$resItems='';
//TEST
//	$res.="<li class=\"ui-finder-folder folder ".$cl."\"><a href=\"core/includes/inc-finder.php?u=\" rev=\"\"><img src=\"core/images/app/folder.png\"/>".$login." / ".$user["fullname"]."</a></li>";
	$i=0;
	foreach($itemList as $item){
		($i&1)?$cl="odd":$cl="even";
		$path_info = pathinfo($iDir.$item);
		if($item{0}!=".") {
			if(is_dir($iDir.$item)){
				$resItems.="<li class=\"ui-finder-folder folder ".$cl."\"><a href=\"includes/inc-finder.php?u=".$_u.$item."\" rev=\"".$_u.$item."\"><img src=\"images/app/folder.png\"/>".$path_info['filename']."</a></li>";
			$i++;
			}
			else if(in_array($path_info['extension'], $iExts)) {
				$resItems.="<li class=\"ui-finder-file ".$cl."\"><a href=\"action/get_metas_exif_img.php?file=/home/".$login."/public_html/".$_u.$item."&p=finder.png\" rev=\"".$_u."\" title=\"".$iDir.$item."\"><img src=\"core/images/app/".$path_info['extension'].".png\"/>".$item."</a></li>";
			$i++;
			}
		}
	}
	if ($resItems==""){$resItems="<li><span class=\"mess_alert\">Votre dossier <strong>\"public_html/$_u\"</strong> ne contient pas d'image.</span><br /><span class=\"mess_info\">Vous pouvez t&eacute;l&eacute;charger une ou plusieurs images dans votre dossier  <strong>\"public_html\"</strong> en utilisant le <a title=\"filexplorer\" onclick=\"parent.JQD.init_link_open_win($(this));\" rel=\"../../filexplorer/\" rev=\"filexplorer\" href=\"#icon_dock_lcs_filexplorer\" class=\"ext_link  open_win bouton button \"><strong>&nbsp;&nbsp;Explorateur&nbsp;de&nbsp;fichiers&nbsp;&nbsp;</strong></a></span></li>";
}
	$res.=$resItems;
	$res.="</ol>";
	echo $res;
?>
