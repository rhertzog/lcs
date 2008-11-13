<?
 include "includes/secure_other.inc.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset= iso-8859-1" />
<link rel="stylesheet" type="text/css" href="./Styles/style.css" />	
<link rel="stylesheet" type="text/css" href="./Styles/tabs.css" />	
<link rel="stylesheet" type="text/css" href="./Styles/dhtmlwindow.css" />	
<link href="./Styles/styleMC.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="./Styles/floating_window_with_tabs.css" media="screen">
<link rel="stylesheet" href="./Styles/admin.css">
<link rel="stylesheet" href="./Styles/dragable-boxes.css" type="text/css">

<script type="text/javascript">
var floating_window_skin = 1;
</script>
<script type="text/javascript" src="./lib/prototype.js"></script>
<script type="text/javascript" src="lib/ajax.js"></script>
<script type="text/javascript" src="lib/dragable-boxes.js"></script>
<script type="text/javascript" src="./lib/dhtmlwindow.js"></script>
<script type="text/javascript" src="./lib/choixCible.js"></script>
<script type="text/javascript" src="./lib/tabs.js"></script>
<script type="text/javascript" src="./lib/MenuContextuel.js"></script>
<script type="text/javascript" src="./fckeditor/fckeditor.js"></script>
<script type="text/javascript" src="./lib/floating_window_with_tabs.js"></script>
<script type="text/javascript" src="./lib/lazierLoad.js"></script>
<script type="text/javascript" src="./lib/mon_lcs.js"></script>
<script type="text/javascript">
var mode='other';
var user='<? echo $uid; ?>';
</script>
<title>Mon LCS</title>



</head>

<body>


<div id="ContentAddingOnglet"></div>
<div id="addOnglet"></div>
<div id="load"></div>
<div id="content"></div>
<div id="spinner"></div>
<div id="ressources"></div>
<div id="stats"></div>
<div id="mainMenu"></div>
<div id="submenu"></div>
<div id="ie5menu" class="menuclickdroit" onmouseover="highlightie5(event,'#0A0A0A','#FFFFFF');"
 onmouseout="lowlightie5(event,'','#000000');" onclick="jumptoie5(event);" style="display:block; visibility:hidden;">
</div>
<div id="view_others" title="Voir le MonLCS d'un autre utilisateur"></div>
<div id="ajaxMessage"></div>
<div id="notes"></div>
<div id="view_note"></div>
<div id="window1">
  <div id="tab1" class="floatingWindowContent">
  </div>
  <div id="tab2" class="floatingWindowContent">
  </div>
  <div id="tab3" class="floatingWindowContent">
  </div>
</div>
<div id="rssContainer">
</div>

<div id="main">
</div>		

<div id="SH_frame" onclick="javascript:SH();"></div>
<div id="cDv"></div>
<div id="virtualMenu"></div>
<div id="warning_other"></div>
<div id="descr_popup"></div>
</body>
</html>


