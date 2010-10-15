<?php
 
echo '
.main, .main th, .main th.weekend {
	padding-top :0px;
	-moz-border-radius:10px;
    -webkit-border-radius:10px;
    -moz-box-shadow: -2px -3px 4px rgba(0,0,0,0.3);
    -webkit-box-shadow: -2px -3px 4px rgba(0,0,0,0.3);
    opacity:0.7;  
  }
  
.main td  {
    vertical-align:top;
	-moz-border-radius-bottomleft:10px;
	-moz-border-radius-bottomright:10px;
	-moz-border-radius-topleft:10px;
	-moz-border-radius-topright:10px;
	-moz-border-radius:10px;
	-moz-box-shadow: 0px 20px 10px -10px rgba(255,255,255,0.3) inset;
	-webkit-box-shadow: inset 0px 20px 10px -10px rgba(255,255,255,0.3) ;
    -webkit-border-radius:10px; 
	font-weight:normal;
	width:100px;
  }

#week .main th a:hover {
    color:rgb(252,225,11);
  }

#week .main th.today { 
 opacity:1;
 }
 
.main td.hasevents {
 -moz-box-shadow: 0px -20px 10px -10px rgba(0,0,0,0.3) inset;
 -webkit-box-shadow:  inset 0px -20px 10px -10px rgba(0,0,0,0.3);
 }
 
.main th.row{
  font-size:';
  echo intval(-$GLOBALS["TIME_SLOTS"]*0.049 + 15.2); 
  echo 'px;
  -moz-box-shadow: 0px 20px 10px -10px rgba(255,255,255,0.3) inset;
  -webkit-box-shadow: inset 0px 20px 10px -10px rgba(255,255,255,0.3) ;
 }

a.entry {
font-weight:bold;
 opacity:1;
 }
 
a.layerentry {
 font-style: italic ;
 background:#FFFFFF;
 opacity:1;
 }
 
#week .main th.empty {
 vertical-align:middle;
 color : #000086;
 opacity:2.0;
 background-color: #F8F8FF;
 -moz-box-shadow: -2px -3px 4px #000;
 -webkit-box-shadow:  -2px -3px 4px #000;
 }

#day .title .date, .title .date {
   font-size:18px;
 }

#week .main {
    border-collapse:separate;
 }

table {
    border-spacing:1px;
 }
 
#week .main th.row, #day .glance td, .glance th.row {
 height :'.intval( 1080/$GLOBALS["TIME_SLOTS"]).'px;
 vertical-align:middle; 
 }
';

?>
 