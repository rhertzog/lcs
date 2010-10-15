/*****************************************************************************\
|	Part of Ajax Chess										  										|
|																										|
|  written 2006 by tornamodo at linuxuser.at												|
|																										|
\*****************************************************************************/

function way_isfree(y1, x1, y2, x2, stop_before) {
	out = true;

	steps_y = (y2 - y1); 
	steps_x = (x2 - x1); 

	if (steps_y === 0) {
		steps = steps_x;
	} else  {
		steps = steps_y;
	}

	onestep_y = parseInt(steps_y, 10);
	onestep_x = parseInt(steps_x, 10);

	if (steps < 0) { steps = steps * (-1); }

	onestep_y = parseInt(onestep_y / steps, 10);
	onestep_x = parseInt(onestep_x / steps, 10);
	

	check_y = parseInt(y1, 10);
	check_x = parseInt(x1, 10);

	for (i=0; i<(steps - stop_before); i++) {
		if (steps_y !== 0) { check_y+=onestep_y; }
		if (steps_x !== 0) { check_x+=onestep_x; }
		if (Board[check_y - 1].substr(check_x - 1, 1) != " ") { out = false; }
	}

	return out;
}

function field_isCovered(to_y, to_x) {
	CheckTest = true;
	out2 = false;
	
	if (Game_Play_White === true) {
		for (cy=0; cy<8; cy++) {
			for (cx=0; cx<8; cx++) {
				cc1 = Board[cy].substr(cx,1);
				if (parseInt(cc1, 10) > 0) { 
				} else {
					if (move_allowed(cy+1, cx+1, to_y, to_x) === true) { out2 = true; }
				}
			} /* End For x */
		} /* End For y */
	/* End If Game_Play_White */
	} else {
		for (cy=0; cy<8; cy++) {
			for (cx=0; cx<8; cx++) {
				cc1 = Board[cy].substr(cx,1);
				if (parseInt(cc1, 10) > 0) { 
					if (move_allowed(cy+1, cx+1, to_y, to_x) === true) { out2 = true; }
				}
			} /* End For x */
		} /* End For y */
	} /* End If Game_Play_Black */
	
	CheckTest = false;
	return out2;
}


function move_allowed(y1, x1, y2, x2) {
//	log("al");
	Field_Target_IsFree = Field_Is_Free(y2,x2);
	Field_Target_IsOpponent = Field_Is_Opponent(y2,x2);

	Figure1 = Board[y1-1].substr(x1-1,1);
	FigureID = convert_to_number(Figure1);

	allowed = false;
	
		/* Play B/W Only : ) */
		/* FigureID: 1=Rook, 2=Night, 3=Bishop, 4=Queen, 5=King, 6=Pawn */
	
		steps_y = (y2 - y1); 
		steps_x = (x2 - x1); 
	
		steps_betrag_y = (steps_y < 0) ? steps_y * (-1) : steps_y;
		steps_betrag_x = (steps_x < 0) ? steps_x * (-1) : steps_x;

		if (FigureID == "1") { /* Rook */
			allowed = false;
			if ((steps_betrag_y === 0) || (steps_betrag_x === 0)) {
					if (way_isfree(y1, x1, y2, x2, 0)) {
						/* Free Way To Empty Field */
						allowed = true;
					}
					if ((Field_Target_IsOpponent === true) && (way_isfree(y1, x1, y2, x2, 1) === true)) { 
						/* Free Way & Opponent on target Field */
						allowed = true; 
					}
			}
		} /* End Rook */
	
			
		else if (FigureID == "2") { /* Night */
			if (((steps_betrag_y == 2) && (steps_betrag_x == 1)) || ((steps_betrag_y == 1) && (steps_betrag_x == 2))) {
				allowed = true;
			}
			if ((Field_Target_IsOpponent === false) && (Field_Target_IsFree === false)) { allowed = false; }
		} /* End Night */


		else if (FigureID == "3") { /* Bishop */
			if (steps_betrag_y == steps_betrag_x) {
					if (way_isfree(y1, x1, y2, x2, 0)) {
								allowed = true;
					} else { allowed = false; }
					
					/* Free Way & Opponent on target Field */
					if ((Field_Target_IsOpponent === true) && (way_isfree(y1, x1, y2, x2, 1) === true)) { allowed = true; }
					/* CheckTest IsOpponent doesn't Work */
			}	else { }		
		} /* End Bishop */

	
		else if (FigureID == "4") { /* Queen */
			allowed = false;
			if (((steps_y === 0) || (steps_x === 0)) || (steps_betrag_y == steps_betrag_x)) {
					if (way_isfree(y1, x1, y2, x2, 0)) {
						/* Free Way To Empty Field */
						allowed = true;
					}
					if ((Field_Target_IsOpponent === true) && (way_isfree(y1, x1, y2, x2, 1) === true)) { 
						/* Free Way & Opponent on target Field */
						allowed = true; 
					}
			}
		} /* End Queen */

		else if (FigureID == "5") { /* King */
			allowed = false;
			if (((steps_betrag_y == 1) || (steps_betrag_x == 1)) && ((steps_betrag_y < 2) && (steps_betrag_x < 2))) {
				if (CheckTest === false) {
					fIsOpp = Field_Target_IsOpponent;
					if (field_isCovered(y2, x2) === false) {
							if ((fIsOpp === true) && (way_isfree(y1, x1, y2, x2, 1) === true)) { allowed = true; }
							if (way_isfree(y1, x1, y2, x2, 0)) { allowed = true; }
					} else {
						show_info("Not Possible - Would be Chess");
						window.setTimeout("hide_info()", 2000);
						allowed = false;
					}
				} else {
					/* Running the Checktest */
					allowed = true;
				}
			}
		}

		else if (FigureID == "6") { /* Pawn */
			/* Our Farmer y+ | Other Farmer: y- */
			allowed_steps_y = (Figure_Is_White(y1, x1) == Game_Play_White) ? -1 : 1;

			allowed = false;
			
			if (way_isfree(y1, x1, y2, x2, 0)) {
				/* Single Step Forward */
				if ((steps_y == allowed_steps_y) && (steps_x === 0)) { allowed = true; }
				/* Double Step From The Start (line 2 or 7) */
				if ((steps_y == (allowed_steps_y*2)) && (steps_x === 0)) { if ((y1 == 2) || (y1 == 7)) { allowed = true; } } 
				if (CheckTest === true) { allowed = false; } /* CheckTest checks for Hitting Moves */
			}

			/* Hitting */
			if ((Field_Target_IsOpponent === true) || (CheckTest === true)) {
				allowed = false;	
				/* Diagonal One */
				if ((steps_y == allowed_steps_y) && (steps_betrag_x == 1)) {
					allowed = true;
				}
			}
			
			/* En Passant */
			if ((steps_y == allowed_steps_y) && (steps_betrag_x == 1)) {
				if ((y1 == 4) && (y2 == 3)) {
					if (Game_Play_White) {
						lmreq = convert_to_fieldname(x2) + "7" + convert_to_fieldname(x2) + "5";
					} else {					
						lmreq = convert_to_fieldname(9-x2) + "2" + convert_to_fieldname(9-x2) + "4";
					}
					if (lmreq == Game_LastMove) { allowed = true; }
				}
			} /* End En Passant */
			
		} /* End Pawn */
			
	/* Prevent Blocking The Own Position */
	if ((steps_y === 0) && (steps_x === 0)) { allowed = false; }
	
	return allowed;
}



function special_move_allowed(sy1, sx1, sy2, sx2) {
	/* For Example Rochade */
	Field_Target_IsFree = Field_Is_Free(sy2,sx2);
	Field_Target_IsOpponent = Field_Is_Opponent(sy2,sx2);

	Figure1 = Board[sy1-1].substr(sx1-1,1);
	FigureID = convert_to_number(Figure1);

	allowed = false;
	
	s_steps_y = (sy2 - sy1); 
	s_steps_x = (sx2 - sx1);
	
	s_steps_betrag_y = (steps_y < 0) ?s_steps_y * (-1) :s_steps_y;
	s_steps_betrag_x = (steps_x < 0) ?s_steps_x * (-1) :s_steps_x;

	/* ROCHADE */
	if (FigureID == "5") {
	if (((sy1 == "8") || (sy1 == "1"))	&& (s_steps_betrag_x == "2")) {
		/* 1. King Not Covered
			2. Field which King Moves Over not Covered
			3. Way_Free 
			4. King now moved before
			5. Tower not moved before
		*/
			/* First Check If King & Tower were moved before */
			tower_x = (s_steps_x < 0) ? 1 : 8;
			
			if (was_moved(sy1, sx1) === true) {
				alert("King Moved Before");
			} else if (was_moved(sy1, tower_x) === true) {
				alert("Tower Moved Before");
			} else {
				/* King And Tower Not Moved */
				if (field_isCovered(MyKing_y, MyKing_x) === false) {
//					log(MyKing_y + " " + MyKing_x + " " + s_steps_x);
					if ((field_isCovered(MyKing_y, MyKing_x + (s_steps_x / 2)) === false) && (field_isCovered(MyKing_y, MyKing_x + (s_steps_x)) === false)) {
							if ((way_isfree(sy1, sx1, sy2, sx2, 0)) && (way_isfree(sy1, tower_x, sy2, sx2, 0))) {
								allowed = true;
							}
					} else { alert("King cannot make a Rochade over a covered field"); allowed = false; }
				} else { alert("King cannot make a Rochade when in chess"); allowed = false; }
				/* End King And Tower Not Moved */
			}
	}
	} /* End Rochade */

	return allowed;	
	
}

function was_moved (y, x) {
	/* Since we save the names in the format h2 h4, we have to transform out Array-Coord. into Fieldnames */
	if (Game_Play_White === true) {
		/* Top is 8 going down to 1 (reverse of our array coordinate sys)*/
		board_y = (9 - y);
		board_x = x;
	} else {
		/* Turn (Output of) Board 180° */
		board_y = y;
		board_x = 9 - x;
	}
	fieldname = convert_to_fieldname(board_x) + board_y.toString();
	
	found = false;
	for (l=0; l<Move_Count; l++) {
		if (Move_History[l][1] == fieldname) { found = true; }
	}
		
	return found;
}

