/*****************************************************************************\
|	Part of Ajax Chess										  										|
|																										|
|  written 2006 by tornamodo at linuxuser.at												|
|																										|
\*****************************************************************************/

function is_numeric(arg1) {
	isn_out = false;
	if ((parseInt(arg1, 10) > 0) || (parseInt(arg1, 10) < 1)) {
		isn_out = true;
	} 
	return isn_out;
}

/* Converting */
function convert_figid_to_figabbr(figid) {
	if (parseInt(figid, 10) > 0) { /* 0..9 */
		fignumber = figid;
	} else {							/* a..f */
		fignumber = convert_to_number(figid);		
	}
	return Figures_Abbr[fignumber - 1];
}

function convert_figabbr_to_figid(figabbr) {
	out = -2;
	if (parseInt(figabbr, 10) > 0) { /* 0..9 */
		out = figabbr-1;
	} else {							/* a..f */
		for (i=0; i<5; i++) { if (Figures_Abbr[i] == figabbr) { out = i; } }
	}
	return out + 1;
}

function convert_to_number (fieldname) {
	var numbers = new Array("a", "b", "c", "d", "e", "f", "g", "h");
	var out = -1;
	if (parseInt(fieldname, 10) > 0) { /* 0..9 */
		out = fieldname - 1;
	} else {							/* a..f */
		for (i=0; i<8; i++) { if (numbers[i] == fieldname) { out = i; } }
	}
	return out+1;
}

function convert_to_fieldname (fieldnumber) {
	var numbers = new Array("a", "b", "c", "d", "e", "f", "g", "h");
	return numbers[fieldnumber-1];
}

function convert_id_to_imagefn (typeid) {
	/* Gets a String */
	if (parseInt(typeid, 10) > 0) { /* White */
		out = "w" + typeid + ".gif";
	} else { /* Black */
		out = "b" + convert_to_number(typeid) + ".gif";
	}
	return out;
}


/* Color Stuff */
function mouseoverfield (pos_y, pos_x)  { 
				if (Game_Play_White === true) {
					/* Top is 8 going down to 1 (reverse of our array coordinate sys)*/
					board_y = (9 - pos_y);
					board_x = pos_x;
				} else {
					/* Turn (Output of) Board 180° */
					board_y = pos_y;
					board_x = 9 - pos_x;
				}		

				document.getElementById("field_info").innerHTML = convert_to_fieldname(board_x) + board_y.toString();
}

function log(str) {
	x = str + "<br>" + document.getElementById("loggy").innerHTML;
	document.getElementById("loggy").innerHTML = x;
	
}

function mouseoutoffield (pos_y, pos_x) { 
//	PaintBoard();
}

function trim(sString)
{
	while (sString.substring(0,1) == ' ') { sString = sString.substring(1, sString.length); }
	while (sString.substring(sString.length-1, sString.length) == ' ') { sString = sString.substring(0,sString.length-1); }
	return sString;
}


function array_reverse(arr1) {
		out = new Array( arr1.length );
//		alert(arr1.length);
		
		for (i=0; i<arr1.length; i++) {
			j = arr1.length - 1 - i;
			out[i] = arr1[j];
		}
		return out;
}

function array_mirror(arr1) {
	/* Mirrors The Columns of an Array */
	out2 = new Array( arr1.length );

		/* Spalten jeder Zeile vertauschen */
		i2 = arr1[0].length;
		i2 = i2 / 2;
				
		for (i=0; i<arr1[0].length; i++) {
		/* Für Alle Zeilen: */
			rightpart = "";
			for (j=0; j<i2; j++) { rightpart = arr1[i].substr(j,1) + rightpart; }
	
			leftpart = "";
			for (j=arr1[0].length-1; j>=i2; j--) { leftpart = leftpart + arr1[i].substr(j,1); }
			
			out2[i] = leftpart + rightpart;
//			alert(out2[i]);
		}

	return out2;
}

function FillBoard() {
//	out = new Array ( "abcdecba","ffffffff","        ","        ","        ","        ","66666666","12345321" );
	if (Game_Custom === "") {
		out = new Array ( "abcdecba","ffffffff","        ","        ","        ","        ","66666666","12345321" );
	} else {
		out = new Array (8);
		
		for (l=0;l<8;l++) {
			out[l] = Game_Custom.substr((l*8), 8);
		}
//		alert("custom board filled");
	}
	return out;
}

function Fill_FieldNames() {
	for (i1=1;i1<9;i1++) {
	
		if (field_names_display === true) {
			if (Game_Play_White === true) {
				document.getElementById("c" + i1).innerHTML = convert_to_fieldname(i1);
				document.getElementById("r" + i1).innerHTML = 9 - i1;
			} else {
				document.getElementById("c" + i1).innerHTML = convert_to_fieldname(9 - i1);
				document.getElementById("r" + i1).innerHTML = i1;
			}
		} else {
			document.getElementById("c" + i1).innerHTML = "&nbsp;";
			document.getElementById("r" + i1).innerHTML = "&nbsp;";
		}
		
	}
}

function PaintFigures() {
//	document.getElementById("field_1-1").style.background = "url(images/w3.gif) no-repeat center center;"

	for (ty=1; ty<9; ty++) {
		for (tx=1; tx<9; tx++) {
//			str1 = "images/" + convert_id_to_imagefn(Board[y-1][x-1]);
//			document.getElementById("fieldf_" + y + "-" + x).style.background = "url(" + str1 + ") no-repeat center center;"
			/* IE & Opera Want It With ClassNames */
			str1 = "f" + Board[ty-1].substr(tx-1,1);
			document.getElementById("fieldf_" + ty + "-" + tx).className=str1 + " fieldf";
		}
	}

}

function MarkLastMove() {

	if ((Game_LastMove !== "")) {
		tx1 = convert_to_number(Game_LastMove.substr(0,1));
		ty1 = Game_LastMove.substr(1,1);
		tx2 = convert_to_number(Game_LastMove.substr(2,1));
		ty2 = Game_LastMove.substr(3,1);

//		log ("lastmove: " + Game_LastMove + " - " + ty1 + " " + tx1 + " > " + ty2 + " " + tx2);
		if (Game_Play_White === true) {
			/* Top is 8 going down to 1 (reverse of our array coordinate sys)*/
			field_y1 = (9 - ty1);
			field_x1 = tx1;
			field_y2 = (9 - ty2);
			field_x2 = tx2;
		} else {
			/* Turn (Output of) Board 180° */
			field_y1 = ty1;
			field_x1 = 9 - tx1;
			field_y2 = ty2;
			field_x2 = 9 - tx2;
		}
			
		/* Mark Last Moves */
			document.getElementById("field_"+field_y1+"-"+field_x1).style.border = "3px solid " + Color_Field_LastMove;
			document.getElementById("field_"+field_y2+"-"+field_x2).style.border = "3px solid " + Color_Field_LastMove;
	}
	
}

function PaintBoard() {
	/* Paints Black and White Columns */
	for (y=1;y<9;y++) {
		for (x=1;x<9;x++) {
			if ((x+y) % 2 === 0) { farbe = Color_White; } else { farbe = Color_Black; }
			document.getElementById("field_"+y+"-"+x).style.background = farbe;
			document.getElementById("field_"+y+"-"+x).style.border = "3px solid " + farbe;
		}
	}
		
	Fill_FieldNames();
	PaintFigures();

	if (Option_MarkLastMove) {	MarkLastMove(); }
	
}

function Field_Is_Free(field_y, field_x) {
	out = false;
	if (Board[field_y-1].substr(field_x-1,1) == " ") { out = true; }
	return out;
}

function Field_Is_Opponent(field_y, field_x) {
	out = Figure_Is_White(field_y, field_x);
	if (Game_Play_White) { out = !out; }
	if (CheckTest === true ) { out = !out; }

	if (Board[field_y-1].substr(field_x-1,1) == " ") { out = false; 	/* Empty Is NO Enemy */ }
	return out;
}


function Figure_Is_White(field_y, field_x) {
	Figure1 = Board[field_y-1].substr(field_x-1,1);
	if (parseInt(Figure1, 10) > 0) { out = true; } else { out = false; }
	return out;	
}

function Field_WhichFigureID(field_y, field_x) {
	Figure1 = Board[field_y-1].substr(field_x-1,1);
	FigureID = convert_to_number(Figure1);
	return FigureID;
}

function FindFig(ffFigID) {
	/* 1-6, a-f, returns arr coords 2843 or only 1 at queen or so like 14 (y|x) */
	ffout="";
	
	for (ffy=0;ffy<8;ffy++) {
		for (ffx=0;ffx<8;ffx++) {
			if (Board[ffy].substr(ffx,1) == ffFigID) { ffout+=(ffy+1) + "" + (ffx+1); }
		}	
	}
	
	return ffout;
}




function toggle_log() {
		if (document.getElementById("loggy").style.display != "block") {
			document.getElementById("loggy").style.display = "block";
		} else {
			document.getElementById("loggy").style.display = "none";
		}
}
	

function reconnect(silent) {
	if ((!Game_Replay) && (!Game_Freestyle) && (Game_Started)) {
		/* Re-Init Timer */
			window.clearTimeout(intCheck);
			intCheck = window.setTimeout("ajax_check_move()", Refresh_Timeout);

		if (Game_MyTurn) {
			show_info("place move first");
		} else {
			/* Re-Write Last Move */
			ajax_set_info( "m" + Game_LastMove , "0" );	

			if (!silent) { 
				show_info("re-connecting...");
				window.setTimeout("hide_info()", 2000);
			}
		}	
	}
}	

function check_connection() {
	/* Frequent Connection Check */
	last_ms = parseInt(Game_LastCheck, 10);
	now_ms = timestamp_ms();
	
	if ((now_ms - last_ms) > Max_TimeDiff) {
		reconnect(true);
		log("routine connection check: reconnect");
	} else {
		log("routine connection check ok");
	}

	window.clearTimeout(intConnCheck);
	intConnCheck = window.setTimeout("check_connection()", Connection_Interval);

	window.clearTimeout(intConnCheck2);
	intConnCheck2 = window.setTimeout("check_connection2()", Connection_Interval2);
}

function check_connection2() {
	/* Just to make sure */
	check_connection();
	log("restart routine connection check");

	window.clearTimeout(intConnCheck2);
	intConnCheck2 = window.setTimeout("check_connection2()", Connection_Interval2);
}