<script type="text/javascript">
	var options_el;
	var options_left = -320;
	
	function toggleabout() {
		if (document.getElementById('about').style.display == 'block') {
			document.getElementById('about').style.display='none';
		} else {
			document.getElementById('about').style.display='block';
		}	
	}


		
	function toggleoptions() {
		if (navigator.appName == "Netscape") { 	document.getElementById("bgcolortable").style.display = "block"; }

		bgblack_test.style.background = Color_Black;
		bgwhite_test.style.background = Color_White;
		bglm_test.style.background = Color_Field_LastMove;
		bgactive_test.style.background = Color_Field_Active;
	
		if (document.getElementById('options').style.display == 'block') {
			document.getElementById('options').style.display='none';
		} else {
			document.getElementById('options').style.display='block';
		}
		
//		if (options_left < -9) { showoptions(); } else { hideoptions(); }
	}
	
	function showoptions() {
		if (options_left < -10) {
			options_left+=7;
			options.style.left = options_left + "px";
			window.setTimeout("showoptions()", 1);
		} else {
			/* Finished */
		}
	}
	
	function hideoptions() {
		if (options_left > -320) {
			options_left-=7;
			options.style.left = options_left + "px";
			window.setTimeout("hideoptions()", 1);
		}
	}
	
	function setbg(color_hex) {
		document.getElementById(options_el).style.background = '#' + color_hex;
		c_hex.value = color_hex;
	}
	
	function changecolor(el) {
		if ((colorpicker.style.display == "none") || (el != options_el)) {
			tmph1 = rgb2hex(document.getElementById(el).style.background);
			c_hex.value = tmph1.substr(1);
			colorpicker.style.display = "block";
			options_el = el;			
		} else {
			colorpicker.style.display = "none";
		}
	}
	
	function rgb2hex(c_rgb1) {
		/* Format: rgb(123, 44, 55) -> #344321 */
		c_p1 = parseInt(c_rgb1.substring(c_rgb1.indexOf("(")+1, c_rgb1.indexOf(",")), 10);
		c_p2 = parseInt(c_rgb1.substring(c_rgb1.indexOf(",")+1, c_rgb1.lastIndexOf(",")), 10);
		c_p3 = parseInt(c_rgb1.substring(c_rgb1.lastIndexOf(",")+1, c_rgb1.lastIndexOf(")")), 10);
		
		h_p1 = c_p1.toString(16);
		h_p2 = c_p2.toString(16);
		h_p3 = c_p3.toString(16);
		
		if (h_p1.length == 1) { h_p1="0"+h_p1; }
		if (h_p2.length == 1) { h_p2="0"+h_p2; }
		if (h_p3.length == 1) { h_p3="0"+h_p3; }	
		
		o = "#" + h_p1 + h_p2 + h_p3;
		return o;
	}
	
	function ook() {
		field_names_display = show_fn.checked;
		Option_MarkLastMove = show_ml.checked; 

		if (navigator.appName == "Netscape") { 	
			Color_Black = rgb2hex(bgblack_test.style.background);
			Color_White = rgb2hex(bgwhite_test.style.background);
			Color_Field_LastMove = rgb2hex(bglm_test.style.background);
			Color_Field_Active = rgb2hex(bgactive_test.style.background);
		}
				
		if ((Game_Started === true) || (Game_Replay) || (Game_Freestyle)) { PaintBoard(); }
		toggleoptions();
	}

</script>

<div id="options">
	<table border="0">
		<tr>
			<td>Display Field Names:</td>
			<td><input type="checkbox" name="check_bn" id="show_fn" checked style="border:0px solid gray; background:white;"></td>
		</tr>
		<tr>
			<td>Mark Last Move:</td>
			<td><input type="checkbox" id="show_ml" checked style="border:0px solid gray; background:white;"></td>
		</tr>
	</table>

<div style="float:left;">
	<table border="0" id="bgcolortable" style="display:none;">
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>Black Fields:</td>
			<td><span  id="bgblack_test" onclick="changecolor('bgblack_test')"  style="border:1px solid gray; cursor:pointer;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
		</tr>
		<tr>
			<td>White Fields:</td>
			<td><span id="bgwhite_test" onclick="changecolor('bgwhite_test')" style="border:1px solid gray; cursor:pointer;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
		</tr>
		<tr>
			<td>Active Field:</td>
			<td><span id="bgactive_test" onclick="changecolor('bgactive_test')" style="border:1px solid gray; cursor:pointer;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
		</tr>
		<tr>
			<td>Last Move:</td>
			<td><span id="bglm_test" onclick="changecolor('bglm_test')" style="border:1px solid gray; cursor:pointer;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
		</tr>
	</table>
	
	<table border="0">
		<tr>
			<td align="right" style="padding-left:20px">
				<br>
				<span id="button1" onclick="ook()" style="text-align:center; width:40px; border:1px solid gray; padding:0px; cursor:pointer;">&nbsp;ok&nbsp;</span>&nbsp;
				<span id="button1" onclick="toggleoptions()" style="text-align:center; width:40px; border:1px solid gray; padding:0px; cursor:pointer;">&nbsp;cancel&nbsp;</span><br>
			</td>
		</tr>
	</table>

</div>
<div id="colorpicker">
	<table border="0">
		<tr>
		<?php
			unset($colors);
		
			$colors[] = "ce8a42";
			$colors[] = "ffcf9c";
			$colors[] = "abcabc";
			$colors[] = "fafafa";
			$colors[] = "fafade";
			$colors[] = "669699";
			$colors[] = "555555";
			$colors[] = "009900";
			$colors[] = "000000";
			$colors[] = "dbcacd";
			$colors[] = "214523";
			$colors[] = "854789";
			$colors[] = "cc0033";
			$colors[] = "00bbaa";
			$colors[] = "ddbb00";
			$colors[] = "770044";

			$colors[] = "FF8F35";
			$colors[] = "710000";
			$colors[] = "792C00";
			$colors[] = "5D0016";

			$colors[] = "F0C300";
			$colors[] = "006018";
			$colors[] = "001860";
			$colors[] = "003BB0";
						
			$i = 0;
			foreach ($colors as $c) {
				if ($i % 4 == 0) { echo "</tr><tr>"; }
				echo "<td><span style=\"cursor:pointer; border:1px solid gray; background:#$c\" onclick=\"setbg('" . $c . "')\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>";
				$i++;
			}
		?>
	</tr>
	</table>
	#&nbsp;<input type="text" id="c_hex" maxlength="6" size="6" onkeyup="document.getElementById(options_el).style.background='#'+this.value" style="border:1px solid gray;"><br>
</div>
</div>

<div id="about">
	<table border="0" style="font-size:12px;">
		<tr>
			<td valign="top" style="padding-right:20px;"><img src="images/bulb3.png" /></td>
			<td valign="top"><big>About that little Ajax Chess App</big><br>
			<br>
			author: <a href="mailto:tornamodo@linuxuser.at" id="link">tornamodo</a> at <a href="http://www.linuxuser.at" target="_blank" id="link">linuxuser.at</a><br>
			last modified: feb  2006<br>
			<br>
			please leave your <a href="http://forums.linuxuser.at/posting.php?mode=newtopic&f=22" target="_blank" id="link">comment</a> in the <a href="http://forums.linuxuser.at/viewforum.php?f=22" target="_blank"  id="link">forum</a><br>
			<br>
			licence: <a href="http://en.wikipedia.org/wiki/GNU_GPL" target="_blank" id="link">gnu general puclic license</a>. you may:<br>
			<table border="0" style="padding-top:4px; font-size:12px;color:gray;">
				<tr>
					<td>
						* run it for any purpose<br>
						* study the source<br>
						* modify it<br>
					</td>
					<td style="padding-left:20px;">
						* redistribute it<br>
						* improve the program<br>
						* release improvements under gpl<br>
					</td>
				</tr>
			</table>
			<br>
			<font id="link" onclick="toggleabout()">hide</font>
			</td>
		</tr>
	</table>
</div>
