<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="description" content="freemind flash browser, opensource, open source, freeware, windows"/>
<meta name="keywords" content="freemind,flash"/>
<title>Cartographie des solutions libres et gratuites sous windows</title>
<script type="text/javascript" src="flashobject.js"></script>
<style type="text/css">
	
	/* hide from ie on mac \*/
	html {
		height: 100%;
		overflow: hidden;
	}
	
	#flashcontent {
		height: 100%;
	}
	/* end hide */

	body {
		height: 100%;
		margin: 0;
		padding: 0;
		background-color: #9999ff;
	}

</style>
<script language="javascript">
function giveFocus() 
    { 
      document.visorFreeMind.focus();  
    }
</script></head>
<body onLoad="giveFocus();">
	
	<div id="flashcontent" onmouseover="giveFocus();">
		 Flash plugin or Javascript are turned off.

	 Activate both  and reload to view the mindmap
	</div>
	
	<script type="text/javascript">
		// <![CDATA[
		// for allowing using http://.....?mindmap.mm mode
		function getMap(map){
		  var result=map;
		  var loc=document.location+'';
		  if(loc.indexOf(".mm")>0 && loc.indexOf("?")>0){
			result=loc.substring(loc.indexOf("?")+1);
		  }
		  return result;
		}
		var fo = new FlashObject("visorFreemind.swf", "visorFreeMind", "100%", "100%", 6, "#9999ff");
		fo.addParam("quality", "high");
		fo.addParam("bgcolor", "#a0a0f0");
		fo.addVariable("openUrl", "_blank");
		fo.addVariable("startCollapsedToLevel","3");
		fo.addVariable("maxNodeWidth","200");
		//
		fo.addVariable("mainNodeShape","elipse");
		fo.addVariable("justMap","false");
		fo.addVariable("initLoadFile",getMap("opensource.mm"));
		fo.addVariable("defaultToolTipWordWrap",200);
		fo.addVariable("offsetX","center");
		fo.addVariable("offsetY","center");
		fo.addVariable("buttonsPos","bottom");
		fo.addVariable("min_alpha_buttons",60);
		fo.addVariable("max_alpha_buttons",100);
		fo.addVariable("scaleTooltips","false");
		
		
		
		fo.write("flashcontent");
		// ]]>
	</script>
</body>
</html>
