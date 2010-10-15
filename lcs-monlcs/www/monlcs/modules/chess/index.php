<?php 
	define("IN_CHESS", true); 

	$fn = "sessions/log_gamecount.txt";
	$handle = fopen ($fn, 'r'); 
	$gamedots = fread($handle, filesize($fn)); fclose($handle);

	exec("ls sessions/*_chat.txt", $fl[], $retval);
	$gamesbeingplayed = floor(count($fl[0]) / 2) + 3;
?>

<html>
<head>
<title>ajax chess</title>
	<link rel="stylesheet" type="text/css" href="chess.css" />
<!--	<script src="chess.js" type="text/javascript"></script>
	<script src="chess_core.js" type="text/javascript"></script>
	<script src="chess_replay.js" type="text/javascript"></script>
	<script src="chess_tools.js" type="text/javascript"></script>
	<script src="chess_ajax.js" type="text/javascript"></script>
	<script src="chess_protocol.js" type="text/javascript"></script>
-->
	<script src="chess_cookies.js" type="text/javascript"></script>
	<meta name="keywords" content="ajax, chess, schach, multiplayer, online, tournament, challenge, club, ranked, games, free, gratis, js, javascript, échecs, schaak, &#963;&#954;&#940;&#954;&#953;, scacchi, &#12481;&#12455;&#12473;, ajedrez">
</head>

<body onfocus="do_focus();">

	<div id="index_field">
		<img src="images/ChessSet_Cover.jpg" /><br>
<!--  cursor:pointer;" title="click for Changelog" onclick="window.open('CHANGELOG.txt', 'Zweitfenster', 'width=660,height=400,left=100,top=200,scrollbars=yes');" -->
		<small style="font-size:10px; color:gray;">
			v. 0.13b
		</small><br>
		<br>
			1. <font id="link" onclick="creategame()">create a game</font><br>
			2. <font id="link" onclick="joingame()">join a game</font><br>
			<br>
			3. <font id="link" onclick="startreplay()">replay mode</font><br>
			4. <font id="link" onclick="findgames.style.display='block';">search for games</font><br>
			5. <font id="link" onclick="startfreestyle()">freestyle mode</font><br>
			<br>
		
			6. <a href="http://www.linuxuser.at/index.php?title=Chess_Club" id="link">chess club</a> <font id="link" style="font-size:14px;" onclick="window.open('../clubchat/index.php', 'win2', 'width=580,height=234,left=500,top=200,status=no');">(chat)</font><br>
			7. <font id="link" onclick="toggle_chat()">toggle chat</font><br>
			<br>
			<small style="font-size:10px;">
				<a href="http://www.linuxuser.at/index.php?title=Ajax_Chess" id="link">infos</a> / <a href="http://forums.linuxuser.at" id="link">forum</a> / <a href="mailto:tornamodo@linuxuser.at" id="link">mail</a>
				<br><br>
				<font style="color:gray">games served: <?=$gamedots; ?><br>
				games online: <?=$gamesbeingplayed; ?><br>
				<br>
				<br>
				check out the</font><br><a href="http://www.linuxuser.at/index.php?title=ChessGames_Plugin" id="link">ChessGames Firefox Plugin</a>
			</small>
			<br>
			<br>

	</div>
	
	<div id="chatcontainer">
		<?php include("chat.html"); ?>
	</div>
	
	<div id="droppedoutinfo">
		<table border="0">
			<tr>
				<td valign="top" style="padding-right:20px;"><img src="images/bulb3.png" /></td>
				<td><h2 style="color:gray;margin-bottom:0px;">The Game Is Over</h2>
				<small><font style="color:gray" id="timeused"></font></small><br>
				<br>
				This was the last board setting:<br>
				<br>
				</td>
			</tr>
		</table>
	<pre style="padding:0px; margin:0px;"><span id="droppedouttext" style="border:1px solid gray; border-top:0px; padding:0px; margin:0px; padding-left:5px; padding-right:5px"></span></pre>
	<small>Copy it in order to load it back into a multiplayer game : )</small><br>
	<br>
	<table border="0">
		<tr>
			<td valign="top">Movelist:&nbsp;</td>
			<td><textarea id="movelisttxt" rows="3" cols="20" style="border:2px dashed gray; padding:2px;"></textarea><br></td>
			<td valign="top" style="padding-left:20px;">
				<font id="link" onclick="replaygame();">Replay</font> - <font id="link" onclick="do_save();">Save</font><br>
				<font id="link" onclick="document.getElementById('droppedoutinfo').style.display='none';">Close Window</font>
			</td>
		</tr>
	</table>
	</div>

<script type="text/javascript">


setCookie("test","test");
b1=getCookie("test");
if(b1!="test"){alert("you do not have cookies enabled,\nbut it's required for this game!");}

setCookie("achess_do","");
setCookie("achess_id","");
setCookie("achess_fs","");
setCookie("ajcrs", "");
	
fml = "";
do_focus(); 

function do_focus() {
	b2 = getCookie("ajcml"); 
		if ((b2 !== null) && (b2 !== "")) { 
			/* Dropped Out - Display Movelist */
			setCookie("ajcml", "");

			fml='';
			
			s2=getCookie("ajcdo");
			if (s2 === null) { s2 = ""; }	
	
			document.getElementById("droppedouttext").innerHTML=s2;
			
			for (i=0; i<b2.length/4; i++) {
				if (i % 2 === 0) { 
					if (i>0) { fml+="\n"; }
					fml+=((i/2)+1); 
				}
				fml+="  " + b2.substr(i*4, 4);
			}

			document.getElementById("movelisttxt").value=fml;
			document.getElementById("droppedoutinfo").style.display="block";
			
			Stamp = new Date();
			ms1 = parseInt(getCookie("ajcstart"), 10);
			ms2 = Stamp.getTime();
			
			msD = ms2 - ms1;
			msD = parseInt(msD / 1000, 10);
			
			tSec = msD % 60;
			msD = parseInt(msD / 60, 10);

			tMin = msD % 60;
			msD = parseInt(msD / 60, 10);

			tHr = msD % 60;
			
//			alert(tHr + " " + tMin + " " + tSec);						
			tStr = tHr + ":" + tMin + ":" + tSec;
			timeused.innerHTML = "length: " + tStr;
			
			setCookie("ajcml", "");
			setCookie("ajcdo","");
		}
}

function toggle_chat() {
		if (document.getElementById('chatcontainer').style.display == 'none') {
			document.getElementById('chatcontainer').style.display='block';
		} else {
			document.getElementById('chatcontainer').style.display='none';
		}	
}
	
function togglevis(){if(document.getElementById("settinglist").style.visibility=="hidden"){document.getElementById("settinglist").style.visibility="visible";}else{document.getElementById("settinglist").style.visibility="hidden";}}

function startfreestyle() {
		Stamp = new Date(); setCookie("ajcstart", Stamp.getTime());

		setCookie("achess_do","freestyle");
		setCookie("achess_id","");
		location.href="chess.php";
}

function startreplay(){
		Stamp = new Date(); setCookie("ajcstart", Stamp.getTime());

		setCookie("achess_do","replay");
		setCookie("achess_id","");
		location.href="chess.php";
}

function do_save() {
		Stamp = new Date(); setCookie("ajcstart", Stamp.getTime());
		setCookie("ajcrs", fml);
		window.open('escape.php', 'win3', 'width=480,height=440');
}

function creategame(){
	Check=prompt("Enter Game-ID to Create:");
	if ((Check!==null) && (Check !== "")) {
		Stamp = new Date(); setCookie("ajcstart", Stamp.getTime());

		setCookie("achess_do","create");
		setCookie("achess_id",Check);
		location.href="chess.php";
	}
}

function joingame(){
	Check=prompt("Enter Game-ID to Join:");
	if((Check!==null) && (Check !== "")){
		Stamp = new Date(); setCookie("ajcstart", Stamp.getTime());

		setCookie("achess_do","join");
		setCookie("achess_id",Check);
		location.href="chess.php";
	}
}

function reload(){document.location.reload();}

function replaygame() {
	setCookie("ajcrs", fml);
	startreplay();
}



</script>

<?php include("findgames.php"); ?>

</body>
</html>
