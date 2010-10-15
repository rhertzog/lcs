/*****************************************************************************\
|	Part of Ajax Chess										  										|
|																										|
|  written 2006 by tornamodo at linuxuser.at												|
|																										|
\*****************************************************************************/

/* alertContents 	... receives the content of the com-file
	Handshake			... handles the handshake procedure
*/

function alertContents() {

	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
				str1 = trim(http_request.responseText);
//				intCheck = window.setTimeout("ajax_check_move()", Refresh_Timeout);

//				log("r0: " + str1);
				
				if (str1 !== "") {
					whattodo = str1.substr(0,1);
					str1 = str1.substr(1);
				
					if (whattodo == "m") {
						if ((str1 != Game_LastMove) && (str1 != Game_LastMove2) && (str1 !== "")) {
								/* Found New Move! */
								x1 = convert_to_number(str1.substr(0,1));
								y1 = str1.substr(1,1);
								x2 = convert_to_number(str1.substr(2,1));
								y2 = str1.substr(3,1);
							
									if (Game_Play_White === true) {
										/* Top is 8 going down to 1 (reverse of our array coordinate sys)*/
										arr_y1 = (9 - y1);
										arr_x1 = x1;
										arr_y2 = (9 - y2);
										arr_x2 = x2;
									} else {
										/* Play Black: Turn (Output of) Board 180° */
										arr_y1 = y1;
										arr_x1 = 9 - x1;
										arr_y2 = y2;
										arr_x2 = 9 - x2;
									}							
	
									move_figure(arr_y1, arr_x1, arr_y2, arr_x2);
									submit_move(arr_y1, arr_x1, arr_y2, arr_x2); 
						} else {
							/* No New Move */
						}
					} else if (whattodo == "h") {
						Handshake(str1);
					} else if (whattodo == "c") {					
						com_CustomGame(str1);
					}
				}
				window.clearTimeout(intCheck);
				intCheck = window.setTimeout("ajax_check_move()", Refresh_Timeout);

		} else {
			lost_connection();
		}
	}

}


function Handshake(str1) {
	/* Handles the Handshake */				
	
            	if ((str1 != Game_LastInfo) && (str1 !== "")) { 
//            		log(Game_LastInfo + " -> " + str1);
            		Game_LastInfo = str1;
            		if (str1 == "ob") {
            			/* Start Joined Game as Black */
            			Game_Play_White = false;
							Game_LastInfo = "go";
							ajax_set_info( "h" + Game_LastInfo , "0" );
							log ("Found Open Game - set go & start");
            			start_game();

            		} else if (str1 == "ow") {
            			/* Start Joined Game as White */           		
            			Game_Play_White = true;
							Game_LastInfo = "go";
							ajax_set_info( "h" + Game_LastInfo , "0" );
							log ("Found Open Game - set go & start");
            			start_game();
            		
            		} else if (str1 == "go") {
							/* Server Got GO Signal from Client */
							Game_LastInfo = "ok";
							ajax_set_info( Game_LastInfo , "0" );
            			start_game();
							log ("Go Signal From Client - start - set ok");
						
						} else if (str1.substr(0,1) == "c") {
						/* Part For Negotiating a Custom Game
							********************************** */
							com_CustomGame(str1);

            		} else if (Game_Started === false) {
            			/* Entered a running Game */
							alert("Game Already Running");
//							window.history.back();
						}
            		
            	} /* End If (str1 != Game_LastInfo) && (str1 != "") */

	}
		


function com_CustomGame(com_str) {

	log("customgame: " + com_str);
//	log("me: " + CustomGame_Part);
	
	/* Game needs to be started To negotiate about a custom game */
	if (Game_Started === true) {
		if (CustomGame_Part != "server")	{
		/* Client Stuff */
				if (com_str.substr(0,2) == "ci") {
					/* Got An Invitation */
					CustomGame_Part = "";
  					Acc = confirm("Accept Custom Game:\n" + com_str.substr(2));
  					
  					if (Acc === true) {
						/* Accept */
						Game_LastInfo = "co";
						ajax_set_info( "c" + Game_LastInfo , "0" );
		
						Game_Custom = com_str.substr(2);
						start_custom_game();
  					} else {
  						/* Decline */
						Game_LastInfo = "cn";
						ajax_set_info( "c" + Game_LastInfo , "0" );
  					}
				} /* End of Got An Invite */
		/* End of Clients Work */
		} else {
		/* Servers Work */
				if (com_str == "cn") {
					/* Invitation Was Rejected */
	  				Game_Custom = "";
	  				CustomGame_Part = "";
	  				alert("custom game rejected");
							
					/* Reset The Info Field */
					myturn = (Game_MyTurn) ? "Your Turn" : "Opponents Turn";
					document.getElementById("info_turn").innerHTML = myturn;
							
					/* Set Status Back into OK */
					Game_LastInfo = "ok";
					ajax_set_info( Game_LastInfo , "0" );
					
        		} else if (str1 == "co") {
        			/* Invitation to Custom Game Accepted */
	  				CustomGame_Part = "";
					Game_LastInfo = "ok";
					ajax_set_info( Game_LastInfo , "0" );

					start_custom_game();
				}
		/* End of Servers Work */
		} 
	} /* End If (Game_Started === true) */
	
	return true;
}