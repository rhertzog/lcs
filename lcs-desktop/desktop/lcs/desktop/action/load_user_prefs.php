<?php
/*
	RSS Extractor and Displayer
	(c) 2007-2010  Scriptol.com - Licence Mozilla 1.1.
	rsslib.php
	
	Requirements:
	- PHP 5.
	- A RSS feed.
	
	Using the library:
	Insert this code into the page that displays the RSS feed:
	
	<?php
	require_once("rsslib.php");
	echo RSS_Display("http://www.xul.fr/rss.xml", 15);
	? >
	
*/

$RSS_Content = array();

function RSS_Tags($item, $type)
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

		$tnl = $item->getElementsByTagName("iconimg");
		$tnl = $tnl->item(0);
		$img = $tnl->firstChild->textContent;

		$y["text"] = $text;
		$y["link"] = $link;
		$y["win"] = $win;		
		$y["title"] = $title;		
		$y["img"] = $img;
		$y["type"] = $type;
		
		return $y;
}


function RSS_Channel($channel)
{
	global $RSS_Content;

	$items = $channel->getElementsByTagName("icon");
	
	// Processing channel
	
	$y = RSS_Tags($channel, 0);		// get description of channel, type 0
	array_push($RSS_Content, $y);
	
	// Processing articles
	
	foreach($items as $item)
	{
		$y = RSS_Tags($item, 1);	// get description of article, type 1
		array_push($RSS_Content, $y);
	}
}

function RSS_Retrieve($url)
{
	global $RSS_Content;

	$doc  = new DOMDocument();
	$doc->load($url);

	$channels = $doc->getElementsByTagName("userburo");
	
	$RSS_Content = array();
	
	foreach($channels as $channel)
	{
		 RSS_Channel($channel);
	}
	
}


function RSS_RetrieveLinks($url)
{
	global $RSS_Content;

	$doc  = new DOMDocument();
	$doc->load($url);

	$channels = $doc->getElementsByTagName("userburo");
	
	$RSS_Content = array();
	
	foreach($channels as $channel)
	{
		$items = $channel->getElementsByTagName("icon");
		foreach($items as $item)
		{
			$y = RSS_Tags($item, 1);	// get description of article, type 1
			array_push($RSS_Content, $y);
		}
		 
	}

}


function RSS_Links($url, $size = 15)
{
	global $RSS_Content;

	$page = "";

	RSS_RetrieveLinks($url);
	if($size > 0)
		$recents = array_slice($RSS_Content, 0, $size + 1);

	foreach($recents as $article)
	{
		$type = $article["type"];
		if($type == 0) continue;
		$text = $article["text"];
		$link = $article["link"];
		$title = $article["title"];
		$page .= "<a href=\"$win\" rel=\"$link\" title=\"\" class=\"abs icon ext_link\"><img src=\"$img\" />".htmlentities($text)."</a>\n";			
	}

	$page .="\n";

	return $page;
	
}



function RSS_Display($url, $size = 40, $site = 0, $withdate = 0)
{
	global $RSS_Content;

	$opened = false;
	$page = "";
	$site = (intval($site) == 0) ? 1 : 0;

	RSS_Retrieve($url);
	if($size > 0)
		$recents = array_slice($RSS_Content, $site, $size + 1 - $site);

	foreach($recents as $article)
	{
		$type = $article["type"];
		$text = $article["text"];
		$link = $article["link"];
		$win = $article["win"];
		$title = $article["title"];
		$img = $article["img"];
		$page .= '<a class="abs icon ext_link" style="left:'.$left.'px;top:'.$top.'px;" href="'.$win.'" rel="'.htmlentities($link).'" title="'.$title.'"><img src="'.$img.'" />'.utf8_decode($text).'</a>';
	}
	return $page."\n";
	
}


?>
