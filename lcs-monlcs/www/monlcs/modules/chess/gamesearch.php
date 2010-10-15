<?php
	unset ($o1);
	$player = $_REQUEST["p"];

	if ($player != "") {
	
	$page = $_REQUEST["page"];
	if (($page == "") || ($page == 0)) { $page = 1; }

	$link = "http://www.chessgames.com/perl/chess.pl?page=" . $page . "&player=" . $player;
		
	$fp = @fopen($link, "r") or die ("Kann Datei nicht lesen."); 
	while ($line = fgets($fp, 1024)) {
		$content.=$line;
	}
	fclose($fp);
	
	/* Get Games */
	unset($gamearr1);
	
	$i = 0;
	while (strpos($content, "<a href=\"/perl/chessgame?") != false) {
		$mypos = strpos($content, "<a href=\"/perl/chessgame?");
		
		$content = substr($content, strpos($content, "<a href=\"/perl/chessgame?"));
		$content = substr($content, 29);
		
		/* DB ID */
		$gamearr1[$i][0] = substr($content, 0, strpos($content, "\">"));
		
		$content = substr($content, strpos($content, "\">") + 2);
		
		/* Names */
		$gamearr1[$i][1] = substr($content, 0, strpos($content, "</a>"));
		
		$content = substr($content, strpos($content, "</td>") + 4);
		$content = substr($content, strpos($content, "</td>") + 4);
		$content = substr($content, strpos($content, "size=-1>") + 8);

		/* Result */
		$gamearr1[$i][2] = substr($content, 0, strpos($content, "</font>"));

		$content = substr($content, strpos($content, "</td>") + 4);
		$content = substr($content, strpos($content, "size=-1>") + 8);

		/* Move Count */
		$gamearr1[$i][3] = substr($content, 0, strpos($content, "</font>"));

		$content = substr($content, strpos($content, "</td>") + 4);
		$content = substr($content, strpos($content, "size=-1>") + 8);
		
		/* Year */
		$gamearr1[$i][4] = substr($content, 0, strpos($content, "</font>"));

		$content = substr($content, strpos($content, "</td>") + 4);
		$content = substr($content, strpos($content, "size=-1>") + 8);

		$i++;
	}

	if (count($gamearr1) > 0) {
		$o1 = "<small>&nbsp;results for <i>$player</i> <small>(page $page)</small></small><br>";
		$o1.= "<table style='font-size:12px'>
					<tr><td><small>players</small></td><td><small>result</small></td><td><small>moves</small></td><td><small>year</small></td></tr>";
		foreach ($gamearr1 as $a) {
			$o1.= "<tr><td style='padding-right:20px'><a href='http://www.linuxuser.at/chess/chess.php?mstr=a&gid=$a[0]' id='link'>$a[1]</a></td style='padding-right:15px'><td style='padding-right:15px'>$a[2]</td><td style='padding-right:15px'>$a[3]</td><td>$a[4]</td></tr>";
		}
		$o1.= "</table>";
	} else {
		$o1 = "no player '$player' found";
	}
	
	echo $o1;
	} /* End IF $player != "" */
?>