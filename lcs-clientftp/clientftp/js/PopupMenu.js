/** 
     based on eLouai's Javascript DHTML Popup Menu (http://elouai.com/javascript/javascript-popup-menu.php) 
     and http://www.howtocreate.co.uk/tutorials/javascript/browserwindow
**/

// detect browser
var ie	= document.all
var ns6	= document.getElementById&&!document.all

// is menu visible
var isMenu = false ;
// is cursor over the menu
var overpopupmenu = false;

// was last click on icon
var wasIconClicked = false;

// id of div which is a menu
var menuDivId = "popupMenuDiv";

function mouseSelect(e)
{
	if( isMenu )
	{
		if( overpopupmenu == false )
		{
			isMenu = false ;
			overpopupmenu = false;
			hidePopupMenu();
		}
	}
	return true;
}

function iconSelMenu(name)
{
	Browser.createContextMenu(name, document.getElementById(menuDivId));
	wasIconClicked = true;
	return false ;
}

// POP UP MENU
function itemSelMenu(e)
{
	
	if (!wasIconClicked) {
		return true;	
	}
	
	// move div to icon
	if (ns6)
	{
		document.getElementById(menuDivId).style.left = (parseInt(e.clientX) + parseInt(getScrollXY()[0])) + 'px';
		document.getElementById(menuDivId).style.top = (parseInt (e.clientY) + parseInt(getScrollXY()[1])) + 'px';
	} else
	{
		document.getElementById(menuDivId).style.pixelLeft = parseInt(event.clientX) + parseInt(getScrollXY()[0]);
		document.getElementById(menuDivId).style.pixelTop = parseInt(event.clientY) + parseInt(getScrollXY()[1]);
	}
	
	document.getElementById(menuDivId).style.display = "";
	
	isMenu = true;
	wasIconClicked = false;
	return false ;
}


// based on http://www.howtocreate.co.uk/tutorials/javascript/browserwindow
function getScrollXY() {
  var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  return [ scrOfX, scrOfY ];
}

function hidePopupMenu() { document.getElementById(menuDivId).style.display = "none" ; }

// define events handlers
document.onmousedown = mouseSelect;
document.oncontextmenu = itemSelMenu;
