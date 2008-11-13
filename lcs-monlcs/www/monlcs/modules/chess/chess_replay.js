/* Replay Stuff */

function replay_init() {
	Move_History = new Array ();
	Move_Count = 0;
	move_info1 = "";
	Game_LastMove = "";
	
	Board = FillBoard();
	PaintBoard();

	document.getElementById("info").innerHTML = "<table id='movelist'>" + move_info1 + "</table>";

	MyKing_y = 8;
	MyKing_x = 5;
}

function replay_step() {

	test = trim(Game_ReplayMovelist);
	if (test.substr(0,1) == "x") {
		test = test.substr(1);
	}
	if (trim(test) == "end") { Game_ReplayMovelist = ""; }
	
	if (Game_ReplayMovelist.length > 3) { 
		if (Game_ReplayMovelist.substr(0,1) == "x") {
		/* PGN Format like Qxb3+ */
			currml = trim(Game_ReplayMovelist.substr(1));
			
			if (parseInt(currml.substr(0,1), 10) >= 0) { currml = currml.substr(1); } /* from 10 > 99 */
			if (parseInt(currml.substr(0,1), 10) >= 0) { currml = currml.substr(1); } /* from 10 > 99 */
			
			currml = trim(currml);
			currmove = currml.substr(0, currml.indexOf(" ")) + " ";
			
			Game_ReplayMovelist = "x" + currml.substr(currml.indexOf(" ")) + " ";
//			log (currmove);
			Trans_Move(currmove);

			Round_Count = ((Move_Count + 1)/ 2);
			Round_Count = parseInt(Round_Count, 10);
			document.getElementById('moveno').innerHTML=Round_Count;
			
		} else {
		/* Format like h2h4 */
		
			currmove = Game_ReplayMovelist.substr(0,4);
	
				r_x1 = convert_to_number(currmove.substr(0,1));
				r_y1 = (9 - currmove.substr(1,1));
				r_x2 = convert_to_number(currmove.substr(2,1));
				r_y2 = (9 - currmove.substr(3,1));
			
			move_figure(r_y1, r_x1, r_y2, r_x2);
			submit_move(r_y1, r_x1, r_y2, r_x2);
			
			Game_ReplayMovelist = Game_ReplayMovelist.substr(4);
//			document.getElementById("replay_movelist").value = Game_ReplayMovelist;

			Round_Count = ((Move_Count + 1) / 2);
			Round_Count = parseInt(Round_Count, 10);
			document.getElementById('moveno').innerHTML=Round_Count;
	//		window.setTimeout("replay_step()", 4000);
		}
	} else {
		/* Finished Slideshow */
			document.getElementById('button_play').innerHTML = "&nbsp;&gt;&nbsp;";
			document.getElementById('button_play').style.background = "#ffffff";
			window.clearInterval(intReplay);
	}	
	hide_info();
}

function replay_step_back() {
		r_x1 = convert_to_number(Move_History[Move_Count-1][2].substr(0,1));
		r_y1 = (9 - Move_History[Move_Count-1][2].substr(1,1));
		r_x2 = convert_to_number(Move_History[Move_Count-1][1].substr(0,1));
		r_y2 = (9 - Move_History[Move_Count-1][1].substr(1,1));
		
		move_figure(r_y1, r_x1, r_y2, r_x2);

		if (Move_History[Move_Count-2][4] != " ") {
			Board[r_y1-1] = Board[r_y1-1].substr(0, r_x1-1) + Move_History[Move_Count-2][4] + Board[r_y1-1].substr(r_x1);
		}

		Move_Count--;
		Move_Count--;
		
		Game_LastMove = (Move_Count > 0) ? Move_History[Move_Count-1][1] + Move_History[Move_Count-1][2] : "";

//		alert(move_info1.lastIndexOf("<td"));
		move_info1=move_info1.substr(0, move_info1.lastIndexOf("<td"));
		
		if (move_info1.substr(move_info1.lastIndexOf("<td class=")-4,4) == "<tr>") { move_info1 = move_info1.substr(0, move_info1.lastIndexOf("<tr>")); }
		document.getElementById("info").innerHTML = "<table id='movelist'>" + move_info1 + "</table>";

		PaintBoard();

		Round_Count = ((Move_Count + 1)/ 2);
		Round_Count = parseInt(Round_Count, 10);
		document.getElementById('moveno').innerHTML=Round_Count;

		Game_ReplayMovelist=convert_to_fieldname(r_x2) + "" + (9 - r_y2) + "" + convert_to_fieldname(r_x1) + "" + (9 - r_y1) + "" + Game_ReplayMovelist;
//		alert(Game_ReplayMovelist);
}


/* translates a move from PGN-format like Qxd3 to c4h3 and submits it */
function Trans_Move(MoveStr) {
	pos_str = "";
	MoveStrOrig = MoveStr;
	
	MoveNo = Move_Count + 1;
	MoveStr = trim(MoveStr);
	
	if (MoveStr.substr(MoveStr.length - 1, 1) == "+") { MoveStr = MoveStr.substr(0, MoveStr.length - 1); }
	if (MoveStr.substr(MoveStr.length - 1, 1) == "#") { MoveStr = MoveStr.substr(0, MoveStr.length - 1); }

	T_IsWhite = (MoveNo % 2 == 1);

//	log("no: " + MoveNo + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Str: " + MoveStrOrig + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MoveStr: " + MoveStr);
	
	if ((MoveStr == "0-0") || (MoveStr == "O-O")) {
		T_FieldFrom = (T_IsWhite) ? "e1" : "e8";
		T_FieldTo = (T_IsWhite) ? "g1" : "g8";
			tx1 = convert_to_number(T_FieldFrom.substr(0,1));
			ty1 = 9 - T_FieldFrom.substr(1,1);
			tx2 = convert_to_number(T_FieldTo.substr(0,1));
			ty2 = 9 - T_FieldTo.substr(1,1);
		MyKing_y = parseInt(ty2, 10);
		MyKing_x = parseInt(tx2, 10);

	} else if ((MoveStr == "0-0-0") || (MoveStr == "O-O-O")) {
		T_FieldFrom = (T_IsWhite) ? "e1" : "e8";
		T_FieldTo = (T_IsWhite) ? "c1" : "c8";
			tx1 = convert_to_number(T_FieldFrom.substr(0,1));
			ty1 = 9 - T_FieldFrom.substr(1,1);
			tx2 = convert_to_number(T_FieldTo.substr(0,1));
			ty2 = 9 - T_FieldTo.substr(1,1);
		MyKing_y = parseInt(ty2, 10);
		MyKing_x = parseInt(tx2, 10);
	
	} else {
		/* No Rochade */
		/* Which Figure */
		T_FigID = (T_IsWhite) ? "6" : "f";
		
		if (MoveStr.substr(0,1) != MoveStr.substr(0,1).toLowerCase()) {
			T_FigID = convert_figabbr_to_figid(MoveStr.substr(0,1));
			if (!T_IsWhite) { T_FigID = convert_to_fieldname(T_FigID); }
			MoveStr = MoveStr.substr(1);
		}
		
		T_FieldTo = MoveStr.substr(MoveStr.length - 2);
		MoveStr = MoveStr.substr(0, MoveStr.length - 2);
	
		T_IsHitting = (MoveStr.indexOf("x") > -1);
		if (T_IsHitting) { MoveStr = MoveStr.substr(0, MoveStr.length - 1); }
	
		MoveStr = trim(MoveStr);	
		T_FieldFrom = MoveStr;
		
		ty1 = 0;
		tx1 = 0;
		tx2 = convert_to_number(T_FieldTo.substr(0,1));
		ty2 = 9 - T_FieldTo.substr(1,1);
	
		FigFields = FindFig(T_FigID);
	
		if (T_FieldFrom.length === 0) {
			/* there is only 1 possibility, but we have to find it */
	//		alert(FigFields + " " + ty2 + " " + tx2);
			
			if ((T_FigID == "1") || (T_FigID == "a")) {
				/* Only 1 Tower with straight shoot */
				ty1 = FigFields.substr(0,1);
				tx1 = FigFields.substr(1,1);
		
				FigFields = FigFields.substr(2); /* Cut the First Pos */
				dy = ty1 - ty2; dx = tx1 - tx2;
				
//				alert("rook way free: " + ty1 + " " + tx1  + " " + ty2  + " " + tx2);
//				alert(way_isfree(ty1, tx1, ty2, tx2, 1));
				
				if (((dy === 0) || (dx === 0)) && (way_isfree(ty1, tx1, ty2, tx2, 1))) {
					/* Thats the one */
				} else {
//					alert("way not free");
					ty1 = parseInt(FigFields.substr(0,1), 10);
					tx1 = parseInt(FigFields.substr(1,1), 10);
				}
	
			} else if ((T_FigID == "2") || (T_FigID == "b")) {
				/* Night */
				ty1 = FigFields.substr(0,1);
				tx1 = FigFields.substr(1,1);
				FigFields = FigFields.substr(2); /* Cut the First Pos */
	
				dy = ty1 - ty2; if (dy < 0) { dy = dy * (-1); }
				dx = tx1 - tx2; if (dx < 0) { dx = dx * (-1); }

				if (((dy == 2) && (dx == 1)) || ((dy == 1) && (dx == 2))) {
					/* We have the right one */
				} else {
					ty1 = FigFields.substr(0,1);
					tx1 = FigFields.substr(1,1);								
				}
	
			} else if ((T_FigID == "3") || (T_FigID == "c")) {
				/* Bishop */
				ty1 = FigFields.substr(0,1);
				tx1 = FigFields.substr(1,1);
	
				dy = ty1 - ty2; if (dy < 0) { dy = dy * (-1); }
				dx = tx1 - tx2; if (dx < 0) { dx = dx * (-1); }
				
				FigFields = FigFields.substr(2); /* Cut the First Pos */
				
				if ((dy == dx) && (way_isfree(ty1, tx1, ty2, tx2, 1))) {
					/* We have picked the right one */				
				} else {
	//				alert(FigFields + " " + ty2 + " " + tx2);
					ty1 = FigFields.substr(0,1);
					tx1 = FigFields.substr(1,1);				
				}
	
			} else if ((T_FigID == "4") || (T_FigID == "d")) {
				ty1 = FigFields.substr(0,1);
				tx1 = FigFields.substr(1,1);
	
			} else if ((T_FigID == "5") || (T_FigID == "e")) {
				ty1 = FigFields.substr(0,1);
				tx1 = FigFields.substr(1,1);
	
			} else if ((T_FigID == "6") || (T_FigID == "f")) {
				/* Pawn */
				if (T_IsHitting) {
					/* Hitting Stuff */
					if (T_IsWhite) {
						if (Board[(ty2-1)+1].substr(tx2-2,1) == T_FigID) { 
						 	tx1 = tx2-1;
							ty1 = ty2+1; 
						} else if (Board[(ty2-1)+1].substr(tx2,1) == T_FigID) { 
						 	tx1 = tx2+1;
							ty1 = ty2+1; 
						}	
					} else {
						 if (Board[(ty2-1)-1].substr(tx2-2,1) == T_FigID) { 
						 	tx1 = tx2-1;
							ty1 = ty2-1; 
//							log("hit1: " + ty1 + " " + tx1);
						} else if (Board[(ty2-1)-1].substr(tx2,1) == T_FigID) { 
						 	tx1 = tx2+1;
							ty1 = ty2-1; 
//							log("hit2: " + ty1 + " " + tx1);
						}
					}
//					alert(ty1 + " " + tx1 + " " + ty2 + " " + tx2);

				} else { 
					/* No Hitting*/
					/* x no diff | y = +-1, +-2 */
					tx1 = tx2;				
				
					if (T_IsWhite) {
						if (Board[(ty2-1)+1].substr(tx1-1,1) == T_FigID) { 
							ty1 = ty2+1; 
						} else if (Board[(ty2-1)+2].substr(tx1-1,1) == T_FigID) { 
							ty1 = ty2+2; 
						}
					} else {
						if (Board[(ty2-1)-1].substr(tx1-1,1) == T_FigID) { 
							ty1 = ty2-1; 
						} else if (Board[(ty2-1)-2].substr(tx1-1,1) == T_FigID) { 
							ty1 = ty2-2; 					
						}
					}

				} /* End No Hitting */
			} /* End Pawn */

		T_FieldFrom = convert_to_fieldname(tx1) + "" + (9 - ty1);

		} else if (MoveStr.length == 1) {
			/* there are 2 possibilities, but only 1 in given column */
	
			while (FigFields.length > 1) {
	//			alert(FigFields);
				ty1 = parseInt(FigFields.substr(0,1), 10);
				tx1 = parseInt(FigFields.substr(1,1), 10);
				
				FigFields = FigFields.substr(2);
				
				/* x-coord is a..h | MoveStr contains only c or so. */
				if (tx1 == convert_to_number(MoveStr)) { 

						if ((T_FigID == "1") || (T_FigID == "a")) {
							if (way_isfree(ty1, tx1, ty2, tx2, 1)) { FigFields = ""; }

						}	else if ((T_FigID == "2") || (T_FigID == "b")) {
							FigFields = ""; 

						}	else if ((T_FigID == "3") || (T_FigID == "c")) {
							if (way_isfree(ty1, tx1, ty2, tx2, 1)) { FigFields = ""; }

						}	else if ((T_FigID == "4") || (T_FigID == "d")) {
							FigFields = ""; 

						}	else if ((T_FigID == "5") || (T_FigID == "e")) {
							FigFields = ""; 

						}	else if ((T_FigID == "6") || (T_FigID == "f")) {
							if (T_IsHitting) {
								if (ty1 != ty2) {
									FigFields = "";
								}
							} else {							
								FigFields = ""; 							
							}
						}
				}
	
			}
	
			T_FieldFrom = convert_to_fieldname(tx1) + "" + (9 - ty1);
	
		} else if (MoveStr.length == 2) {
			/* there are 2 possibilities, abd we have the from_field here */
			T_FieldFrom = MoveStr;
			tx1 = convert_to_number(T_FieldFrom.substr(0,1));
			ty1 = 9 - T_FieldFrom.substr(1,1);
		}

//	log("no: " + MoveNo + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Str: " + MoveStrOrig + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FigId: " + T_FigID + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hit: " + T_IsHitting + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IsWhite: " + T_IsWhite + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;From:" + T_FieldFrom + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To:" + T_FieldTo + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MoveString: " + T_FieldFrom + "" + T_FieldTo);
	} /* End Something else than a Rochade */

	move_figure(ty1, tx1, ty2, tx2);
	submit_move(ty1, tx1, ty2, tx2);
	return pos_str;	
}