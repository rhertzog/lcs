<?php
/*
*/

$USERPREFS_Content = array();

function USERPREFS_Display_Tags($item, $type)
{
		$y = array();
		$tnl = $item->getElementsByTagName("icontext");
		$tnl = $tnl->item(0);
		$text = $tnl->firstChild->textContent;

		$tnl = $item->getElementsByTagName("iconurl");
		$tnl = $tnl->item(0);
		$link = $tnl->firstChild->textContent;
		
		$tnl = $item->getElementsByTagName("iconwin");
		$tnl = $tnl->item(0);
		$win = $tnl->firstChild->textContent;		

		$tnl = $item->getElementsByTagName("icontitle");
		$tnl = $tnl->item(0);
		$title = $tnl->firstChild->textContent;		

		$tnl = $item->getElementsByTagName("iconrev");
		$tnl = $tnl->item(0);
		$rev = $tnl->firstChild->textContent;		

		$tnl = $item->getElementsByTagName("iconimg");
		$tnl = $tnl->item(0);
		$img = $tnl->firstChild->textContent;

		$y["text"] = $text;
		$y["link"] = $link;
		$y["win"] = $win;		
		$y["title"] = $title;		
		$y["rev"] = $rev;
		$y["img"] = $img;
		$y["type"] = $type;
		
		return $y;
}

function USERPREFS_Display_Icons($url, $size = 40, $site = 0, $withdate = 0)
{
	global $USERPREFS_Content;

	$opened = false;
	$page = "";
	$iconsDock ="";
	$site = (intval($site) == 0) ? 1 : 0;

	$doc  = new DOMDocument();
	$doc->load($url);

	$channels = $doc->getElementsByTagName("userburo");
	
	$USERPREFS_Content = array();
	
	foreach($channels as $channel)
	{
	$items = $channel->getElementsByTagName("icon");
		foreach($items as $item)
		{
			$y = USERPREFS_Display_Tags($item, 1);	// recuperation des icones
			array_push($USERPREFS_Content, $y);
		}
	}

	if($size > 0)
		$recents = array_slice($USERPREFS_Content, $site, $size + 1 - $site);

	$tnl= $channel->getElementsByTagName("quicklaunch");
	$tnl = $tnl->item(0);
	$display_ql = $tnl->firstChild->textContent;

	$display_ql == 1 ? $iconsDock .='<ul class="abs" id="quicklaunch">' : '';
	foreach($recents as $icon)
	{
		$type = $icon["type"];
		$text = $icon["text"];
		$link = $icon["link"];
		$win = $icon["win"];
		$title = $icon["title"];
		$rev = $icon["rev"];
		$img = $icon["img"];
		$page .= '<a class="abs icon ext_link" style="left:'.$left.'px;top:'.$top.'px;" href="'.$win.'" rel="'.htmlentities($link).'" title="'.$title.'" rev="'.$rev.'"><img src="'.$img.'" />'.utf8_decode($text).'</a>';
		$display_ql == 1 ? $iconsDock .=' <li><a class="launch open_win ext_link screenshot" href="'.$win.'" rel="'.htmlentities($link).'"  rev="'.preg_replace('/#icon_dock_lcs_/','',$win).'" title="'.utf8_decode($text).'"><img src="'.$img.'" alt="'.$title.'" class="quicklaunch"/></a></li>' : '';
	}
	$display_ql == 1 ? $iconsDock .='</ul>' : '';
	
	$temp_prefs="";
	
	$tnl= $channel->getElementsByTagName("wallpaper");
	$tnl = $tnl->item(0);
	$wallpaper_src = $tnl->firstChild->textContent;

	$tnl= $channel->getElementsByTagName("pos_wallpaper");
	$tnl = $tnl->item(0);
	$pos_wallpaper = $tnl->firstChild->textContent;

	$tnl= $channel->getElementsByTagName("bgcolor");
	$tnl = $tnl->item(0);
	$bgcolor = $tnl->firstChild->textContent;
	
	$tnl= $channel->getElementsByTagName("iconsize");
	$tnl = $tnl->item(0);
	$icons_size = $tnl->firstChild->textContent;

	$tnl= $channel->getElementsByTagName("iconsfield");
	$tnl = $tnl->item(0);
	$icons_field = $tnl->firstChild->textContent;
	
	$temp_prefs .='<input type="hidden" id="tmp_wallpaper" value="'.$wallpaper_src.'" />'."\n";
	$temp_prefs .='<input type="hidden" id="tmp_poswp" value="'.$pos_wallpaper.'" />'."\n";
	$temp_prefs .='<input type="hidden" id="tmp_bgcolor" value="'.$bgcolor.'" />'."\n";
	$temp_prefs .='<input type="hidden" id="tmp_iconsize" value="'.$icons_size.'" />'."\n";
	$temp_prefs .='<input type="hidden" id="tmp_iconsfield" value="'.$icons_field.'" />'."\n";
	$temp_prefs .='<input type="hidden" id="tmp_quicklaunch" value="'.$display_ql.'" />'."\n";
	
	return $temp_prefs.$page."\n".$iconsDock."\n";
	
}


?>
