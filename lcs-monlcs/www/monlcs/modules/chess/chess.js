/*****************************************************************************\
|	Part of Ajax Chess										  										|
|																										|
|  written 2006 by tornamodo at linuxuser.at												|
|																										|
\*****************************************************************************/


function FieldObject(x, y) {
	this.x = parseInt(x, 10);
	this.y = parseInt(y, 10);
	
	this.str = "";
	this.set = false;
}

Field_From = new FieldObject(0, 0);
Field_To = new FieldObject(0, 0);

Color_Black = "#ce8a42";
Color_White = "#ffcf9c";
Color_Field_Active = "#eeeeee";

Color_Field_LastMove = "#555555";
Option_MarkLastMove = true;

Figures_Abbr = new Array ( "R", "N", "B", "Q", "K", "" );

LastInfo = "open";

move_info1 = "";

/* Chess Variables */
Game_Play_White = true;
Game_MyTurn = true;

Game_DO = "";
Game_SessID = "";

Game_LastMove = "";
Game_LastMove2 = "";
Game_Started = false;
Game_LastCheck = "";

Game_LastInfo = "";
Game_Status = "";

Move_History = new Array ();
Move_Count = 0;
Round_Count = 0;

MyKing_y = 8;
MyKing_x = 5;

field_names_white_cols = "abcdefgh";
field_names_white_rows = "87654321";
field_names_display = true;

Game_Replay = false;
Game_Freestyle = false;
Game_ReplaySpeed = 2500;

Game_Custom = "";
Game_ReplayMovelist = "";
CustomGame_Part = "";

intConnCheck = false;
intConnCheck2 = false;

/* Move_History [i] [j]
		[i]     ... Zug # -1
		[i] [j] ... 0=type, 1=from, 2=to
*/

Board = new Array(8);

intReplay = "";

Refresh_Timeout = 4000; /* ms */
Connection_Interval = 15000; /* ms */
Connection_Interval2 = 60000; /* ms */

Max_TimeDiff = 10000; /* no refresh in the last 10 secs, then reconnect */

CheckTest = false;



function exit_session() {
	Game_DO="exit";

	if ((Game_LastInfo == "ob") || (Game_LastInfo == "ow") || (Game_Started === true)) {
		s1 = "d=" + Game_SessID;
		call_url("unlink.php", s1);
		
		setCookie("achess_do", "");
		setCookie("achess_id", "");
	}
	
	exit_ml="";
	for (i=0;i<Move_Count;i++) { exit_ml+=Move_History[i][1] + Move_History[i][2];}
	setCookie("ajcml", exit_ml);
	setCookie("ajcdo", makesnap());
}

/* Do After Clicking */
function init() {
	init_game();
}

function loadcustomgame() {
	Check = prompt("Bitte Board-String eingeben:");
	if (Check !== null) {
		if (Check.length == 65) {
//				Game_Custom = Check;
			if (Game_Play_White === true) {
				Game_Custom = Check;
			} else {
				Game_Custom = Check;
			}
			/* init_game(); ... done after negotiation */
				Game_LastInfo = "ci" + Game_Custom;
				CustomGame_Part = "server";
				if (!Game_Freestyle) { 
					ajax_set_info( "c" + Game_LastInfo , "0" ); 
					document.getElementById("info_turn").innerHTML = "Waiting for Opponent...";
				} else {
					start_custom_game();
				}
		} else {
			alert("Muss 65 Zeichen beinhalten!");
		}
	}
}

function makesnap() {
	/* Rotate 180 ° */
	o = "";
	if (Game_Play_White === true) {
		for (l=0;l<8;l++) { o+=Board[l]; }
	} else {
		CB1 = new Array (8);
		CB1 = Board;
		/* 1. Make it An Array */
		/* 2. Manipulate */
			CB1 = array_reverse(CB1);
			CB1 = array_mirror(CB1);
		/* 3. Pack it Back into a string */
			for (l=0;l<8;l++) { o+=CB1[l]; }
	}

	if (Game_Replay) {
		o = (Move_Count % 2 === 0) ? "w"+o : "x" + o;
	} else if (Game_Freestyle) {
		if (Game_DO != "exit") {
			white_next = confirm("White Moves Next?");
		} else { white_next = true; }
		o = (white_next === true) ? "w"+o : "x"+o;		
	} else {
		if (Game_MyTurn === false) {
			o = (Game_Play_White === true) ? "x"+o : "w"+o;
		} else {
			o = (Game_Play_White === true) ? "w"+o : "x"+o;
		}
	}	
	
	o = o.replace(/\s/g, "&nbsp;");
	document.getElementById("snapcontent").innerHTML = o;
	document.getElementById("snapcontent").style.display = "block";
	return o;
}

function init_game() {
	Move_History = new Array ();
	Move_Count = 0;
	
	/* Wait 4 Opponent First */
	Game_MyTurn = false;

	Game_SessID = getCookie("achess_id");
	Game_DO = getCookie("achess_do");
	
	if (Game_DO == "create") {
		Game_Play_White = confirm("Play White?");
		str1 = (Game_Play_White === true) ? "ob" : "ow";
		ajax_set_info( "h" + str1, "0" );
		Game_LastInfo = str1;
	
		document.getElementById("info_turn").innerHTML = "Waiting for Opponent...";
		ajax_check_move();		/* Starts Game */
	
	} else if (Game_DO == "freestyle") {
		Board = FillBoard();
		PaintBoard();
		Game_MyTurn = true;
		Game_Freestyle = true;
		Game_Play_White = true;
		document.getElementById("info_turn").innerHTML = "freestyle mode";
		document.getElementById("reconnect").style.display = "none";
		
	} else if (Game_DO == "replay") {
		Board = FillBoard();
		PaintBoard();
		Game_Replay = true;
		Game_Play_White = true;
		window.setTimeout("showro()", 500);
		document.getElementById("info_turn").innerHTML = "replay mode";
		document.getElementById("loadcustom").style.display = "none";
		document.getElementById("reconnect").style.display = "none";
		
	} else {
		/* Join */
		Game_Play_White = false;
		document.getElementById("info_turn").innerHTML = "Waiting for Opponent...";

		ajax_check_move();		/* Starts Game */		
	}
}

function start_custom_game() {
//	alert("starting custom game");
	Move_Count = 0;
	/* Decide Which Side Has To Move (1st Byte: w=White else=Black) */
	if ((Game_Custom.substr(0,1) == "w") && (Game_Play_White === true)) {
		Game_MyTurn = true;
	} else if ((Game_Custom.substr(0,1) != "w") && (Game_Play_White === false)) {
		Game_MyTurn = true;
	} else if (Game_Freestyle) {
		Game_MyTurn = true;	
	} else {
		Game_MyTurn = false;
		ajax_check_move();
	}

	document.getElementById("info").innerHTML = "";
	Game_LastMove = "";
	move_info1 = "";

	Game_Custom = Game_Custom.substr(1);
	start_game();

	KingTmp = (Game_Play_White) ? FindFig("5") : FindFig("e");
	MyKing_y = parseInt(KingTmp.substr(0,1), 10);
	MyKing_x = parseInt(KingTmp.substr(1,1), 10);
}

function start_game() {
	Board = FillBoard();
	if (Game_Play_White === false) { 
		Board = array_reverse(Board);
		Board = array_mirror(Board); 
		MyKing_x = 4;
	}

	PaintBoard();

	if (Game_Custom === "") {
		if (Game_Play_White === false) {
			Game_MyTurn = false;
			ajax_check_move();
		} else {
			Game_MyTurn = true;
		}
	}
	
	myturn = (Game_MyTurn) ? "Your Turn" : "Opponents Turn";
	document.getElementById("info_turn").innerHTML = myturn;
	if ((!Game_Replay) && (!Game_Freestyle)) { window.setTimeout("open_chat()", 1000); }
	
	/* Double Holds Better - Lag Prevention */
	intConnCheck = window.setTimeout("check_connection()", Connection_Interval);
	intConnCheck2 = window.setTimeout("check_connection2()", Connection_Interval2);
	
	Game_Started = true;
}

function field_click (pos_y, pos_x) {
if (Game_MyTurn) {
	if ((!Field_From.set)) {
		/* Take A Figure */
//		alert(Board[pos_y-1]);
		if ((Board[pos_y-1].substr(pos_x-1,1) != " ") && 
			((Figure_Is_White(pos_y, pos_x) == Game_Play_White)) || Game_Freestyle) {
			/* When not Nothing */
			Field_From.x = pos_x;
			Field_From.y = pos_y;

			if (Game_Play_White === true) {
				/* Top is 8 going down to 1 (reverse of our array coordinate sys)*/
				board_y = (9 - pos_y);
				board_x = pos_x;
			} else {
				/* Turn (Output of) Board 180° */
				board_y = pos_y;
				board_x = 9 - pos_x;
			}
			
			Field_From.str = convert_to_fieldname(board_x) + board_y.toString();
			Field_From.set = true;
		
			document.getElementById("field_"+pos_y+"-"+pos_x).style.background = Color_Field_Active;
			document.getElementById("field_"+pos_y+"-"+pos_x).style.border = "3px solid " + Color_Field_Active;;
		}
	} else {
	/* Place A Figure */

		if ((Field_From.y != pos_y) || (Field_From.x != pos_x)) {
		/* Try A Move - Check if Allowed! */
			if ((move_allowed(Field_From.y, Field_From.x, pos_y, pos_x)) || (special_move_allowed(Field_From.y, Field_From.x, pos_y, pos_x)) || Game_Freestyle) {

				/* If principally Allowed, Make A Test for Setting Yourself Chess 
						1. Backup Important Lines				
						2.	Move Figure
						3. (would be chess) ? cancel : do_the_move
				*/

					T1 = Board[Field_From.y-1];
					T2 = Board[pos_y-1];

					move_figure(Field_From.y, Field_From.x, pos_y, pos_x);
					
					Figure2 = T1.substr(Field_From.x-1, 1);
					Figure2ID = convert_to_number(Figure2);
//					log(Figure2ID);
					
					if ((Figure2ID != "5") && (field_isCovered(MyKing_y, MyKing_x) === true)) { 
						/* Would Open A Free Way To The King */
						show_info("Not Possible - Would be Chess");
						window.setTimeout("hide_info()", 2000);
//						alert("Bad Move - You'd be Chess Now :P.\nThink again..."); 
						Board[Field_From.y-1] = T1;
						Board[pos_y-1] = T2;
						Move_Count--;
						
					} else if ((Figure2ID == "5") && (field_isCovered(pos_y, pos_x) === true)) {
						/* King Would Set Itself Chess */
						show_info("Not Possible - Would move into a Chess");
						window.setTimeout("hide_info()", 2000);
//						alert("No Good Move - You'd Set Your King Chess!"); 
						Board[Field_From.y-1] = T1;
						Board[pos_y-1] = T2;
						Move_Count--;
						
					} else {
						/* Move Ok */
						submit_move(Field_From.y, Field_From.x, pos_y, pos_x);
						ajax_set_info( "m" + Game_LastMove , "0" );
						
						if (Field_WhichFigureID(pos_y, pos_x) == "5") {
							MyKing_y = pos_y;
							MyKing_x = pos_x;
//							log("New King Pos: " + pos_y + " " + pos_x);
						}
					}
					
			} /* End Of Move */
		} /* End of Try */
		
		PaintBoard();
		Field_From.set = false;
		
	} /* End Place A Figure */
} /* End If Game_MyTurn */
}

function update_movelist() {
	if (Move_Count > 0) {
		figname = convert_figid_to_figabbr(Move_History[Move_Count - 1][0]);
		lm1 = Move_History[Move_Count - 1][1];
		lm2 = Move_History[Move_Count - 1][2];

		if (Move_Count%2 == 1) { move_info1+="<tr><td class='ml'>" + (((Move_Count+1)/2)) + "</td>"; }
		move_info1+="<td class='ml'>";
//		log(figname + " - " + lm);
				if ((figname == Figures_Abbr[4]) && ((lm2 == "g1") || (lm2 == "g8")) && ((lm1 == "e1") || (lm1 == "e8"))) {
					move_info1+="0-0";
				} else if ((figname == Figures_Abbr[4]) && ((lm2 == "c1") || (lm2 == "c8")) && ((lm1 == "e1") || (lm1 == "e8"))) {
					move_info1+="0-0-0";
				} else {		
					if (figname != Figures_Abbr[5])  { move_info1+=figname; }
					if (Move_History[Move_Count - 1][3] === true) { move_info1+="x"; }
					move_info1+= Move_History[Move_Count - 1][2];
				}		

		move_info1+= "</td>";
		if (Move_Count%2 === 0) { move_info1+= "</tr>"; }
	}
	document.getElementById("info").innerHTML = "<table id='movelist'>" + move_info1 + "</table>";
}

function submit_move(y1, x1, y2, x2) {
	update_movelist();	
	if (Game_Play_White === true) {
		/* Top is 8 going down to 1 (reverse of our array coordinate sys)*/
		board_y1 = (9 - y1);
		board_x1 = x1;
		board_y2 = (9 - y2);
		board_x2 = x2;
	} else {
		/* Turn (Output of) Board 180° */
		board_y1 = y1;
		board_x1 = 9 - x1;
		board_y2 = y2;
		board_x2 = 9 - x2;
	}
	str1 = convert_to_fieldname(board_x1) + board_y1.toString();
	str2 = convert_to_fieldname(board_x2) + board_y2.toString();
	
	Game_LastMove2 = Game_LastMove;
	Game_LastMove = convert_to_fieldname(board_x1) + board_y1.toString() + convert_to_fieldname(board_x2) + board_y2.toString();

	PaintBoard();
	Field_From.set = false;
	
	hide_info();
	
	if ((Game_MyTurn === false) && (field_isCovered(MyKing_y, MyKing_x) === true)) {
		show_info("King is Check");
	}
	if ((!Game_Replay) && (!Game_Freestyle)) { switch_turn(); }
	
}

function show_info(infostr) {
	document.getElementById('checkmsg').innerHTML = infostr;
	document.getElementById('checkmsg').style.display = 'block';
}
function hide_info() {
	document.getElementById('checkmsg').innerHTML = "";
	document.getElementById('checkmsg').style.display = 'none';
}

function move_figure(y1, x1, y2, x2) {
	/* 1. Move Figure
		2. Check for Special Moves
			2.1 Rochade
			2.2 Pawn at the Back Line -> Queen
		3. Add Info to Move_History[], Move_Count++;
	*/
		Figure1 = Board[y1-1].substr(x1-1,1);
		FigureID = convert_to_number(Figure1);

		hit_now = false;
		if (Board[y2-1].substr(x2-1,1) != " ") { hit_now = true; }
		hit_what = Board[y2-1].substr(x2-1,1);
		
		/* Remove Figure From Where it was */
		Temp1 = Board[y1-1].substring(0, x1-1);
		Temp2 = Board[y1-1].substring(x1);
		Board[y1-1] = Temp1 + " " + Temp2;
		
		/* Place the Figure Onto the New Position */
		Temp1 = Board[y2-1].substring(0, x2-1);
		Temp2 = Board[y2-1].substring(x2);
		Board[y2-1] = Temp1 + Figure1 + Temp2;

		/* Rochade Requires Double Move */
		steps_x = (x2 - x1); 
		steps_y = (y2 - y1); 

		tower_x = (steps_x < 0) ? 1 : 8;
		steps_betrag_x = (steps_x < 0) ? steps_x * (-1) : steps_x;
		steps_betrag_y = (steps_y < 0) ? steps_y * (-1) : steps_y;
		
		if ((((y1 == "8") || (y1 == "1"))	&& (steps_betrag_x == "2")) && (FigureID == "5")) {
			/* We Have A Rochade - And It Was Already Allowed */
				/* Move The Tower */
				FigureT = Board[y1-1].substr(tower_x-1,1);

				/* Remove Figure From Where it was */
				Temp1 = Board[y1-1].substring(0, tower_x-1);
				Temp2 = Board[y1-1].substring(tower_x);
				Board[y1-1] = Temp1 + " " + Temp2;
				
				/* Place the Figure Onto the New Position */
				tower_x2 = x2 + ((steps_x / 2) * -1);
				Temp1 = Board[y1-1].substring(0, tower_x2-1);
				Temp2 = Board[y1-1].substring(tower_x2);
				Board[y2-1] = Temp1 + FigureT + Temp2;			
		} /* End Rochade */

		/* En Passant Hit */
			if (((steps_y != 0) && (steps_betrag_x == 1)) && (FigureID == "6")) {
				if (((y1 == 4) && (y2 == 3)) || ((y1 == 5) && (y2 == 6))) {
					if (Game_Play_White) {
						lmreq1 = convert_to_fieldname(x2) + "7" + convert_to_fieldname(x2) + "5";
						lmreq2 = convert_to_fieldname(x2) + "2" + convert_to_fieldname(x2) + "4";
					} else {					
						lmreq1 = convert_to_fieldname(9-x2) + "7" + convert_to_fieldname(9-x2) + "5";
						lmreq2 = convert_to_fieldname(9-x2) + "2" + convert_to_fieldname(9-x2) + "4";
					}

//						log(lmreq1 + " " + lmreq2 + " " + Game_LastMove);
					if ((lmreq1 == Game_LastMove) || (lmreq2 == Game_LastMove)) { 
						/* We have a En Passant Here!! */
						/* Clear Pawn */
						Temp1 = Board[y1-1].substring(0, (x2-1));
						Temp2 = Board[y1-1].substring(x2);
						Board[y1-1] = Temp1 + " " + Temp2;
					 }
				}
			} /* End En Passant */
				
		if (Game_Play_White === true) {
			/* Top is 8 going down to 1 (reverse of our array coordinate sys)*/
			board_y1 = (9 - y1);
			board_x1 = x1;
			board_y2 = (9 - y2);
			board_x2 = x2;
		} else {
			/* Turn (Output of) Board 180° */
			board_y1 = y1;
			board_x1 = 9 - x1;
			board_y2 = y2;
			board_x2 = 9 - x2;
		}
		
		
		/* I know, it's not the best place to do that, but whatever... Log The Move :P */
		str1 = convert_to_fieldname(board_x1) + board_y1.toString();
		str2 = convert_to_fieldname(board_x2) + board_y2.toString();
			
		Move_History[Move_Count] = new Array (5);
		Move_History[Move_Count][0] = Figure1;
		Move_History[Move_Count][1] = str1;
		Move_History[Move_Count][2] = str2;
		Move_History[Move_Count][3] = (hit_now === true) ? true : false;
		Move_History[Move_Count][4] = hit_what;
		Move_Count++;

//		movelist+= str1 + str2;
//		setCookie("ajcml", movelist);
//		log("set: " + movelist);
		
	/* Change Farmer to Queen */
		Board[0] = Board[0].replace(/6/, "4");
		Board[0] = Board[0].replace(/f/, "d");

		Board[7] = Board[7].replace(/6/, "4");
		Board[7] = Board[7].replace(/f/, "d");

}


function switch_turn() {
	if (Game_MyTurn === true) { 
		Game_MyTurn = false; 
		ajax_check_move();
//		alert("switched to passive");
	} else { 
		Game_MyTurn = true; 
	}

	myturn = (Game_MyTurn) ? "Your Turn" : "Opponents Turn";
	document.getElementById("info_turn").innerHTML = myturn;
}

function open_chat() {
	setCookie("achess_id",Game_SessID);
	Fenster1 = window.open("sessions/chat.php", "Zweitfenster", "width=450,height=212,left=500,top=200,status=no");
	Fenster1.focus();
}
