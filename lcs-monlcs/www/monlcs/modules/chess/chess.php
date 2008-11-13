<?php
	if ($_REQUEST["mstr"] != "") {
		setcookie("achess_do", "replay");
		setcookie("ajcrs", $_REQUEST["mstr"]);
	}

	$do1 = $_COOKIE["achess_do"];
	$id1 = $_COOKIE["achess_id"];
		
	$_SESSION["ajcid"] = $id1;
	
	$fn = "sessions/" . $id1 . ".txt";
	$fn2 = "sessions/" . $id1 . "_chat.txt";
 
 	$headtitle = $id1;
	if (($do1 == "create") && ($id1 != "")) {
		/* Start a new Game */
		if (file_exists($fn)) {
			/* Name In Use */		
			die("Session Name Already Taken.<br><a href=\"index.php\">back</a>");
		} else {
			/* Create The Files */
			$str1 = " ";

			if (file_exists($fn)) unlink($fn);
			if (file_exists($fn2)) unlink($fn2);

			$handle = fopen ($fn, 'w'); fwrite ($handle, $str1); fclose($handle);				
			$handle = fopen ($fn2, 'w'); fwrite ($handle, $str1); fclose($handle);				
			

			/* Log Fun */
			$fn = "sessions/log_gamecount.txt";
			$handle = fopen ($fn, 'r'); 
			$playedgames = fread($handle, filesize($fn)); fclose($handle);
			
			$playedgames++;
			
			$fn = "sessions/log_gamecount.txt";
			$handle = fopen ($fn, 'w'); fwrite ($handle, $playedgames); fclose($handle);				
			/* All Done */
		}
		
	} else if (($do1 == "join") && ($id1 != "")) {
		if (file_exists($fn)) {
			$handle = fopen ($fn, 'r'); 
			$gamestatus = fread($handle, filesize($fn)); fclose($handle);
			if (substr($gamestatus, 0, 1) != "h") {
				echo "sorry, game already begun.<br><a href=\"index.php\">back</a>";
				die();
			}
			/* Join A Game */
		} else {
			die("No Game with name '$id1' found.<br><a href=\"index.php\">back</a>");
		}
	
	} else if ((($do1 == "replay") && ($id1 == "")) || ($_REQUEST["mstr"] != "")) {
		$headtitle = "replay";
		
	} else if (($do1 == "freestyle") && ($id1 == "")) {
		$headtitle = "freestyle";
	} else {
//		print_r($_COOKIE);
		die("Please start From the Main window:<br><a href=\"index.php\">here</a>");
	}
	
?>

<html>
<head>
<title>ajax chess - <?=$headtitle?></title>
	<link rel="stylesheet" type="text/css" href="chess.css" />
	<script src="chess.js" type="text/javascript"></script>
	<script src="chess_core.js" type="text/javascript"></script>
	<script src="chess_replay.js" type="text/javascript"></script>
	<script src="chess_tools.js" type="text/javascript"></script>
	<script src="chess_ajax.js" type="text/javascript"></script>
	<script src="chess_cookies.js" type="text/javascript"></script>
	<script src="chess_protocol.js" type="text/javascript"></script>
</head>

<!--
<body onunload="exit_session()" onublur="exit_session()" ondblclick="Trans_Move('asd')" onkeypress="document.getElementById('loggy').style.display = 'block';" onkeyup="document.getElementById('loggy').style.display = 'none';">
-->
<body onunload="exit_session()" onublur="exit_session()">

	<table id="board1">
		<tr id="name_row">
			<td style="padding-bottom:10px; padding-right:30px;">&nbsp;</td> <td id="c1"> </td> <td id="c2"> </td> <td id="c3"> </td> <td id="c4"> </td> <td id="c5"> </td> <td id="c6"> </td> <td id="c7"> </td> <td id="c8"> </td>
		</tr>

		<?php
			for ($y=1; $y<9; $y++) {
				echo "<tr><td id=\"r$y\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
				for ($x=1; $x<9; $x++) {
					echo "<td id=\"field_$y-$x\" class=\"field0\">&nbsp;</td>\n";
				}
				echo "</tr>\n";
			}
		
		?>
	</table>

	<table id="figures_board1">
		<tr>
			<td style="padding-bottom:10px; padding-right:30px;">&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td>
		</tr>
		
		<?php
			for ($y=1; $y<9; $y++) {
				echo "<tr><td id=\"r$y\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
				for ($x=1; $x<9; $x++) {
					echo "<td id=\"fieldf_$y-$x\" class=\"fieldf\" onclick=\"field_click($y, $x)\" onmouseover=\"mouseoverfield($y, $x)\" onmouseout=\"mouseoutoffield($y, $x)\">&nbsp;</td>\n";
				}
				echo "</tr>\n";
			}
		
		?>
	</table>
	
	<div id="game_name"></div>
	<div id="game_status"></div>
	
	<div id="field_info"></div>
	<div id="info"></div>
	<div id="info_turn"></div>
	
	<div id="showchat"><font id="link" onclick="open_chat()">Open Chat</font></div>
	<div id="toggleoptions"><font id="link" onclick="toggleoptions()">Options</font> - <font id="link" onclick="toggleabout()">About</font></div>
	<div id="loadcustom" onclick="loadcustomgame()">Load Custom Game</div>
	<div id="makesnap" onclick="makesnap()">Make Snapshot</div>
	<div id="reconnect" onclick="reconnect(false)">Reconnect</div>
	<pre id="snapcontent" title="doubleclick to hide" ondblclick="snapcontent.style.display='none'"></pre>
		
	<div id="loggy"></div>
	<div id="checkmsg" title="click to hide" onclick="checkmsg.style.display='none';">You are Check</div>

	<script type="text/javascript">
		init();
	</script>
	
	<?php 
		include("options.php"); 
		include("replay.php");	
	?>
	
</body>

</html>