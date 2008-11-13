


<script type="text/javascript">
 	function reducespaces(spstr1) {
 		spstr2 = ""; splastchar = "";
 		for (rsi=0;rsi<spstr1.length;rsi++) {
			if ((spstr1.substr(rsi,1) != " ") || (splastchar != " ")) {
				spstr2+=spstr1.substr(rsi, 1);
	 			splastchar = spstr1.substr(rsi, 1);
 			}
 		}
 		return spstr2;
 	}

	function showro() {	
		document.getElementById('replayo').style.display = 'block';
		document.getElementById('replayq').style.display = 'block';
	}
	
	function countspaces(csparg1) {
		csp_cnt = 0;
		for (cspi=0; cspi<csparg1.length; cspi++) {
			if (csparg1.substr(cspi,1) == " ") { csp_cnt++; }
		}
		return csp_cnt;
	}
	
	function rok1() {
		/* Enter Movelist -> Game_ReplayMovelist */
		Game_ReplayMovelist = "";
		tml = document.getElementById("replay_movelist").value;
		origmlstr = tml;

 		tml = tml.replace(/L/g, "B");
 		tml = tml.replace(/S/g, "N");
 		tml = tml.replace(/D/g, "Q");
 		tml = tml.replace(/T/g, "R");
		tml = tml.replace(/=Q/g, "");
		tml = tml.replace(/=N/g, "");
		
	if (tml.indexOf("x") == -1) {
			tml = tml.replace(/[.]/g, " ");
			tml = tml.replace(/\s/g, "");
			tml = tml.replace(/\t/g, "");
			tml = tml.replace(/\n/g, "");
			tml = tml.replace(/ /g, "");
		
		mc1=0;
		if (tml.substr(0,1) == "1") {
			/* Basic Formatted with Round Numbers '1  h2h4  h6h5' */
			while (tml.length > 3) {
				if (mc1 % 2 === 0) { 
					tml = tml.substr(1); /* from 0 > 9 */
					if (parseInt(tml.substr(0,1), 10) >= 0) { tml = tml.substr(1); } /* from 10 > 99 */
					if (parseInt(tml.substr(0,1), 10) >= 0) { tml = tml.substr(1); } /* from 100 > 999 */
				}
				Game_ReplayMovelist+=tml.substr(0,4);
				tml = tml.substr(4);
				mc1++;
			}

		} else {
			/* Plain Formatted 'h2h4h6h5' */
			mc1 = tml.length / 4;
			Game_ReplayMovelist = tml;
		}		

	} else {
		/* PGN Notation Qxh3+
			1. Cut Meta Tags
			2. Reduce . and spaces to 1 space
		*/


 		if (tml.indexOf("1.") > -1) { tml = tml.substr(tml.indexOf("1.")); }
 		if (tml.indexOf("[") > -1) { tml = tml.substr(0, tml.indexOf("[")); } 		

		tml = tml.replace(/[.]/g, " ");
		tml = tml.replace(/\s/g, " ");
		tml = tml.replace(/\n/g, " ");

 		tml = reducespaces(tml);
 		tml = trim(tml);


		tml = "x" + tml + " ";

		mc1 =	parseInt(countspaces(tml) / 3, 10);
		mc1 = (mc1 * 2);
		if (countspaces(tml) % 3 != 0) { mc1++; }
				
		tml+=" end ";
		Game_ReplayMovelist = tml;
	}

	
		document.getElementById('replayo').style.display = 'none';

		rc1 = parseInt((mc1+1)/ 2, 10);
		document.getElementById('movecount').innerHTML=rc1;
		document.getElementById('moveno').innerHTML="0";
		replay_init();
		do_play();
	}
	
	function step_first() {
		rok1();
		do_pause();
	}
	
	function do_play() {
		document.getElementById('button_play').innerHTML = "&nbsp;||&nbsp;";
		document.getElementById('button_play').style.background = "#cccc00";
		window.clearInterval(intReplay);	
		intReplay = window.setInterval("replay_step()", Game_ReplaySpeed);
	}
	
	function do_pause() {
		document.getElementById('button_play').innerHTML = "&nbsp;&gt;&nbsp;";
		document.getElementById('button_play').style.background = "#ffffff";
		window.clearInterval(intReplay);	
	}
	
	function play_pause() {
		if (document.getElementById('button_play').innerHTML == "&nbsp;&gt;&nbsp;") {
			do_play();
		} else {
			do_pause();
		}
	}
	
	function fillml(arg1) {
		if (arg1 == 1) { 
			mstr1="1.d4%20d5%202.c4%20c6%203.Nf3%20Nf6%204.Nc3%20e6%205.e3%20Nbd7%206.Qc2%20b6%207.Bd3%20Bb7%208.O-O%20Be7%209.b3%0A%0ARc8%2010.Bb2%20Qc7%2011.Rad1%20h6%2012.e4%20dxe4%2013.Nxe4%20O-O%2014.c5%20Nxe4%2015.Bxe4%20a5%2016.Rfe1%0A%0ARfd8%2017.Bh7+%20Kh8%2018.Bd3%20Kg8%2019.Bh7+%20Kh8%2020.Bd3%20Kg8%2021.Ba3%20Qb8%2022.Qc1%20bxc5%2023.Bb1%0A%0AQa7%2024.Qc2%20Nf6%2025.Ne5%20Rd5%2026.Ng4%20Rf5%2027.Nxf6+%20gxf6%2028.h4%20Kh8%2029.Qe2%20Ba6%2030.Qe3%0A%0ARh5%2031.Bxc5%20Bxc5%2032.dxc5%20Qxc5%2033.Qf3%20Kg7%2034.Qg4+%20Kh8%2035.Rd7%20Rg8%2036.Qf3%20Re5%0A%0A37.Qxf6+%20";
		} else if (arg1 == 2) {
			mstr1="1.d4%20d5%202.c4%20dxc4%203.Nf3%20e6%204.e3%20a6%205.Bxc4%20c5%206.O-O%20Nf6%207.dxc5%20Bxc5%208.Qxd8+%20Kxd8%0A%0A9.Ne5%20Ke7%2010.Be2%20Bd7%2011.Bf3%20Nc6%2012.Nxc6+%20Bxc6%2013.Bxc6%20bxc6%2014.Bd2%20Rhb8%2015.Rc1%0A%0ABd6%2016.Bc3%20Nd5%2017.Rc2%20";
		} else if (arg1 == 3) {
			mstr1="1.e4%20c5%202.Nf3%20d6%203.d4%20cxd4%204.Nxd4%20Nf6%205.Nc3%20a6%206.Be3%20e5%207.Nb3%20Be6%208.f3%20Nbd7%0A%0A9.Qd2%20b5%2010.a4%20b4%2011.Nd5%20Bxd5%2012.exd5%20Nb6%2013.Bxb6%20Qxb6%2014.a5%20Qb7%2015.Bc4%20g6%0A%0A16.Ra4%20Rb8%2017.Qd3%20Ra8%2018.Qd2%20Rb8%2019.Nc1%20h5%2020.Nd3%20Bh6%2021.Qe2%20O-O%2022.Nxb4%20Qd7%0A%0A23.Nc6%20Rxb2%2024.O-O%20h4%2025.Bb3%20h3%2026.g3%20e4%2027.fxe4%20Qg4%2028.Qd3%20Qg5%2029.e5%20dxe5%0A%0A30.Rh4%20e4%2031.Qd4%20Ng4%2032.Rxh6%20Nxh6%2033.Qxb2%20Qe3+%2034.Rf2%20Qe1+%2035.Rf1%20Qe3+%20";
		} else if (arg1 == 4) {
			mstr1="1%20%20g1f3%20%20g8f6%0A2%20%20c2c4%20%20c7c5%0A3%20%20b1c3%20%20b8c6%0A4%20%20d2d4%20%20c5d4%0A5%20%20f3d4%20%20e7e6%0A6%20%20a2a3%20%20c6d4%0A7%20%20d1d4%20%20b7b6%0A8%20%20d4f4%20%20f8e7%0A9%20%20e2e4%20%20d7d6%0A10%20%20f4g3%20%20e8g8%0A11%20%20c1h6%20%20f6e8%0A12%20%20h6f4%20%20c8b7%0A13%20%20a1d1%20%20e7h4%0A14%20%20g3h3%20%20d8f6%0A15%20%20f4e3%20%20h4g5%0A16%20%20f1e2%20%20g5e3%0A17%20%20h3e3%20%20f6e7%0A18%20%20e1g1%20%20e8f6%0A19%20%20d1d2%20%20f8d8%0A20%20%20f1d1%20%20b7c6%0A21%20%20f2f4%20%20h7h5%0A22%20%20e2f3%20%20e7c7%0A23%20%20h2h3%20%20e6e5%0A24%20%20f4f5%20%20h5h4%0A25%20%20e3f2%20%20c6b7%0A26%20%20c3b5%20%20c7c4%0A27%20%20b5d6%20%20c4c7%0A28%20%20f2h4%20%20b7c6%0A29%20%20g2g4%20%20c6a4%0A30%20%20g4g5%20%20a4d1%0A31%20%20g5f6%20%20d8d6%0A32%20%20d2g2%20%20g7g6%0A33%20%20f5g6";
		} else if (arg1 == 5) {
			mstr1="1.d4%20Nf6%202.c4%20g6%203.Nc3%20Bg7%204.e4%20d6%205.f3%20O-O%206.Be3%20e5%207.d5%20c6%208.Qd2%20cxd5%209.cxd5%0A%0ANbd7%2010.Nge2%20a6%2011.Nc1%20Nh5%2012.Bd3%20f5%2013.N1e2%20Ndf6%2014.exf5%20gxf5%2015.Ng3%20e4%2016.Nxh5%0A%0ANxh5%2017.fxe4%20f4%2018.Bf2%20Bg4%2019.h3%20Bd7%2020.O-O-O%20Be5%2021.Kb1%20Qf6%2022.Be2%20Ng3%2023.Bxg3%0A%0Afxg3%2024.Bf3%20Rac8%2025.Ne2%20Qg6%2026.Rc1%20Rxc1+%2027.Qxc1%20Rc8%2028.Qe3%20Qf6%2029.Qd2%20Rc5%0A%0A30.Nc1%20Bf4%2031.Qb4%20Bb5%2032.Nb3%20Bd3+%2033.Ka1%20Rc2%2034.Rb1%20Be5%2035.Nc1%20Bxb2+%2036.Qxb2%0A%0AQxb2+%20";
		} else if (arg1 == 6) {
			mstr1="1.%20e4%20e5%202.%20Nf3%20Nc6%203.%20Bb5%20a6%204.%20Ba4%20Nf6%205.%20O-O%20b5%206.%20Bb3%20d6%207.%20c3%20Bg4%208.%0Ah3%20Bh5%209.%20d3%20Be7%2010.%20Nbd2%20O-O%2011.%20Re1%20Qd7%2012.%20Nf1%20Na5%2013.%20Bc2%20h6%2014.%20g4%20Bg6%0A15.%20Ng3%20Nh7%2016.%20Nf5%20Nb7%2017.%20d4%20exd4%2018.%20cxd4%20Nd8%2019.%20Nxe7+%20Qxe7%2020.%20d5%20c5%0A21.%20Bf4%20Nb7%2022.%20Bg3%20Rfe8%2023.%20a4%20Qf6%2024.%20axb5%20axb5%2025.%20Kg2%20Ng5%2026.%20Nxg5%20hxg5%0A27.%20Rxa8%20Rxa8%2028.%20e5%20Bxc2%2029.%20Qxc2%20dxe5%2030.%20Bxe5%20Qd8%2031.%20d6%20c4%2032.%20Qe4%20Nc5%0A33.%20Qc6%20Nd3%2034.%20Re3%20Rc8%2035.%20Qb7%20Rb8%2036.%20Qd5%20Nb4%2037.%20Qc5%20Nd3%2038.%20Qd4%20Rb6%2039.%0Ad7%20Rb7%2040.%20Bc7%20Nf4+%2041.%20Kf1";
		} else if (arg1 == 7) {
			mstr1="1.%20e4%20e5%202.%20Bc4%20c6%203.%20Nc3%20Bd6%204.%20d3%20Bc7%205.%20Qf3%20Qe7%206.%20Bg5%20Nf6%207.%20Nge2%0Ad6%208.%20h3%20Be6%209.%20Bb3%20b5%2010.%20O-O-O%20h6%2011.%20Bxf6%20Qxf6%2012.%20Qxf6%20gxf6%2013.%20d4%0Aa5%2014.%20f4%20exd4%2015.%20Rxd4%20a4%2016.%20Bxe6%20fxe6%2017.%20Rhd1%20Ke7%2018.%20Ng1%20Nd7%2019.%0ANf3%20h5%2020.%20e5%20fxe5%2021.%20Nxe5%20Nxe5%2022.%20fxe5%20d5%2023.%20Re1%20Raf8%2024.%20Rd3%20Rf5%0A25.%20b3%20axb3%2026.%20axb3%20Rxe5%2027.%20Rf1%20Rg5%2028.%20g3%20Rhg8%2029.%20Ne2%20e5%2030.%20Rdf3%0AR8g7%2031.%20Rf6%20Bd6%2032.%20Rh6%20R7g6%2033.%20Rxg6%20Rxg6%2034.%20Rf5%20e4%2035.%20Rxh5%20Bxg3%0A36.%20Nxg3%20Rxg3%2037.%20Kd2%20Kd6%2038.%20Rh8%20Kc5%2039.%20c3%20b4%2040.%20cxb4+%20Kd4%2041.%20Rh6%0ARg2+%2042.%20Kc1%20e3%2043.%20Rxc6%20Rg1+%2044.%20Kb2%20e2%2045.%20Re6%20e1=Q%2046.%20Rxe1%20Rxe1";
		} else if (arg1 == 8) {
			mstr1="1.e4%20e5%202.Bc4%20c6%203.Qe2%20d6%204.c3%20f5%205.d3%20Nf6%206.exf5%20Bxf5%207.d4%20e4%208.Bg5%20d5%20%0A9.Bb3%20Bd6%2010.Nd2%20Nbd7%2011.h3%20h6%2012.Be3%20Qe7%2013.f4%20h5%2014.c4%20a6%20%0A15.cxd5%20cxd5%2016.Qf2%20O-O%2017.Ne2%20b5%2018.O-O%20Nb6%2019.Ng3%20g6%20%0A20.Rac1%20Nc4%2021.Nxf5%20gxf5%2022.Qg3+%20Qg7%2023.Qxg7+%20Kxg7%2024.Bxc4%20bxc4%20%0A25.g3%20Rab8%2026.b3%20Ba3%2027.Rc2%20cxb3%2028.axb3%20Rfc8%2029.Rxc8%20Rxc8%20%0A30.Ra1%20Bb4%2031.Rxa6%20Rc3%2032.Kf2%20Rd3%2033.Ra2%20Bxd2%2034.Rxd2%20Rxb3%20%0A35.Rc2%20h4%2036.Rc7+%20Kg6%2037.gxh4%20Nh5%2038.Rd7%20Nxf4%2039.Bxf4%20Rf3+%20%0A40.Kg2%20Rxf4%2041.Rxd5%20Rf3%2042.Rd8%20Rd3%2043.d5%20f4%2044.d6%20Rd2+%20%0A45.Kf1%20Kf7%2046.h5%20e3%2047.h6%20f3%20";
		} 
					
		mstr1=mstr1.replace(/%20/g, " ");
		mstr1=mstr1.replace(/%0A/g, "\n");
		document.getElementById("replay_movelist").value = mstr1;
		document.getElementById('replaylist').style.display='none';
	}
	
	function skip_to_end() {
		do_play();
		window.clearInterval(intReplay);
		intReplay = window.setInterval('replay_step()', 1);
	}

	function showmlist() {
		document.getElementById('replaylist').style.display='block';
	}
	
	function adjdelay(arg1) {
		Game_ReplaySpeed = Game_ReplaySpeed + parseInt(arg1, 10);
		window.clearInterval(intReplay);	
		intReplay = window.setInterval("replay_step()", Game_ReplaySpeed);		
	}
</script>

<span id="replayo">
	Enter Movelist (pgn):<br>
	<textarea id="replay_movelist" rows="10" cols="20" style="border:2px dashed gray; padding:2px;"></textarea><br>
	<font id="link" onclick="showmlist()" style="font-size:11px">(click for a list of games)</font>
	<font style="font-size:8px"><br><br></font>
	<span id="button1" onclick="rok1()" style="text-align:center; width:40px; border:1px solid gray; padding:2px; cursor:pointer">ok</span> 
	<span id="button1" onclick="document.getElementById('replayo').style.display='none'" style="text-align:center; width:40px; border:1px solid gray; padding:2px; cursor:pointer">cancel</span>
</span>

<div id="replayq">
	<span id="button1" onclick="step_first()" title="first" style="text-align:center; width:40px; border:1px solid gray; padding:2px; cursor:pointer">|<</span>
	<span id="button1" onclick="replay_step_back()" title="back"  style="text-align:center; width:40px; border:1px solid gray; padding:2px; cursor:pointer"><<</span>
	<span id="button_play" onclick="play_pause()" title="play/pause"  style="text-align:center; width:40px; border:1px solid gray; padding:2px; cursor:pointer">&nbsp;&gt;&nbsp;</span>
	<span id="button1" onclick="replay_step()" title="next"  style="text-align:center; width:40px; border:1px solid gray; padding:2px; cursor:pointer">>></span>
	<span id="button1" onclick="skip_to_end()" title="last"  style="text-align:center; width:40px; border:1px solid gray; padding:2px; cursor:pointer">>|</span>
	<font style="font-size:8px;"><br><br></font>
	<font style="font-size:12px;">
		<font id="link" onclick="showro()" >round </font> <span id="moveno">0</span>/<span id="movecount" style="padding-right:6px;">0</span>
		speed <font id="link" onclick="adjdelay(-500);"><b>+</b></font> <font id="link" onclick="adjdelay(500);" style="font-size:16px"><b>-</b></font>
	</font>
</div>

<div id="replaylist">
	1. <font id="link" onclick="fillml(1)">Kasparov - Vallejo (2004.02.26)</font><br>
	2. <font id="link" onclick="fillml(2)">Kramnik - Kasparov (2004.03.03)</font><br>
	3. <font id="link" onclick="fillml(3)">Shirov - Kasparov (2004.02.19)</font><br>
	4. <font id="link" onclick="fillml(4)">Kasparov - Van Wely (2004)</font><br>
	5. <font id="link" onclick="fillml(5)">Gheorghiu - Kasparov (1988.11.16)</font><br>
	6. <font id="link" onclick="fillml(6)">Fischer - diCamillo (1-0) (Nov. 1956)</font><br>
	7. <font id="link" onclick="fillml(7)">Conway - Philidor (0-1) (1790)</font><br>
	8. <font id="link" onclick="fillml(8)">John Bruhl - Philidor (0-1) (1783)</font><br>
	<br>
	<span id="button1" onclick="document.getElementById('replaylist').style.display='none'" style="text-align:center; width:40px; border:1px solid gray; padding:2px; font-size:12px; cursor:pointer">close</span>
</div>

<?php 
	$req_mstr = $_REQUEST["mstr"];
	if ($_REQUEST["gid"] != "") {
		$link = "http://www.chessgames.com/perl/nph-chesspgndownload?gid=" . $_REQUEST["gid"];

		$fp = @fopen($link, "r") or die ("Kann Datei nicht lesen."); 
		while ($line = fgets($fp, 1024)) {
			$content.=$line;
		}
		fclose($fp);	

		$content = ereg_replace("[[].*[]]", "", $content);

		$c2 = "";
		$addn = true;
		
		for ($i=0; $i<strlen($content); $i++) {
			$nowchar = substr($content, $i, 1);
			if ($nowchar == "{") {
				$addn = false;
			} else if ($nowchar == "}") {
				$addn = true;
			} else {
				if ($addn) { $c2.=$nowchar; }
			} 
		}
		
		$content = trim($c2);
		$content = substr($content, 0, strrpos($content, " "));
		$req_mstr = $content;
		
		echo "<div id='xx' style='display:none'>$req_mstr</div>";
		echo "<script type=\"text/javascript\">
					showro(); 
					myMl = document.getElementById('xx').innerHTML;
					document.getElementById('replay_movelist').value=myMl; 
				</script>";
	}
?>

<script type="text/javascript">
	mstr1 = getCookie("ajcrs");
	if (mstr1 === null) { mstr1 = ""; }
	<?php if ($req_mstr != "") { echo "mstr1 = '" . $req_mstr . "';"; } ?>
	mstr1 = unescape(mstr1);
	document.getElementById("replay_movelist").value = mstr1;
</script>