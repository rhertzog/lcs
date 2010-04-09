
// directory of where all the images are
var cmThemeBase = 'includes/menu/themes/darkness/';

var cmTheme =
{
  // main menu display attributes
  //
  // Note. When the menu bar is horizontal,
  // mainFolderLeft and mainFolderRight are
  // put in <span></span>. When the menu
  // bar is vertical, they would be put in
  // a separate TD cell.

  // HTML code to the left of the folder item
  mainFolderLeft: '&nbsp;',
  // HTML code to the right of the folder item
  mainFolderRight: '&nbsp;',
  // HTML code to the left of the regular item
  mainItemLeft: '&nbsp;',
  // HTML code to the right of the regular item
  mainItemRight: '&nbsp;',

  // sub menu display attributes

  // 0, HTML code to the left of the folder item
  folderLeft: '<img alt="" src="includes/menu/icons/spacer.gif">',
  // 1, HTML code to the right of the folder item
  folderRight: '<img alt="" src="' + cmThemeBase + 'arrow.gif">',
  // 2, HTML code to the left of the regular item
  itemLeft: '<img alt="" src="includes/menu/icons/spacer.gif">',
  // 3, HTML code to the right of the regular item
  itemRight: '<img alt="" src="includes/menu/icons/blank.gif">',
  // 4, cell spacing for main menu
  mainSpacing: 0,
  // 5, cell spacing for sub menus
  subSpacing: 0,
  // 6, auto dispear time for submenus in milli-seconds
  delay: 500
};

// for horizontal menu split
var cmThemeHSplit = [_cmNoAction, '<td class="ThemeMenuItemLeft"></td><td colspan="2"><div class="ThemeMenuSplit"></div></td>'];
var cmThemeMainHSplit = [_cmNoAction, '<td class="ThemeMainItemLeft"></td><td colspan="2"><div class="ThemeMenuSplit"></div></td>'];
var cmThemeMainVSplit = [_cmNoAction, '&nbsp;'];

function HeureCheckEJS()
	{
	div_hr = document.getElementById("hdyna");
	krucial = new Date;
	heure = krucial.getHours();
	min = krucial.getMinutes();
	sec = krucial.getSeconds();
	jour = krucial.getDate();
	mois = krucial.getMonth()+1;
	annee = krucial.getFullYear();
	if (sec < 10)
		sec0 = "0";
	else
		sec0 = "";
	if (min < 10)
		min0 = "0";
	else
		min0 = "";
	if (heure < 10)
		heure0 = "0";
	else
		heure0 = "";
	DinaHeure = heure0 + heure + ":" + min0 + min + ":" + sec0 + sec;
	which = DinaHeure
if (div_hr)	document.getElementById("hdyna").innerHTML=which;
	
	 setTimeout("HeureCheckEJS()", 500);
	}

setTimeout("HeureCheckEJS()", 500);


