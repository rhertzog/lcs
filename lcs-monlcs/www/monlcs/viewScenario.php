<?php
include "includes/secure_no_header.inc.php";
extract($_POST);
extract($_GET);
	

?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="./Styles/style.css" />
	<link rel="stylesheet" type="text/css" href="./Styles/tabs.css" />
	<link rel="stylesheet" type="text/css" href="./Styles/dhtmlwindow.css" />
	<link href="./Styles/styleMC.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="./Styles/floating_window_with_tabs.css" media="screen">
	<link rel="stylesheet" href="./Styles/admin.css">
	<link rel="stylesheet" href="./Styles/dragable-boxes.css" type="text/css">

	<script type="text/javascript" src="/monlcs/lib/dhtmlwindow.js"></script>
	<script type="text/javascript" src="/lib/js/prototype.js"></script>
	<script type="text/javascript">
		var tab_active = 'scenario';

		 function trim (myString)        {
                	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
        	}
		 
		function stripslashes(str) {
                	str=str.replace(/\\'/g,'\'');
                	str=str.replace(/\\"/g,'"');
                	str=str.replace(/\\\\/g,'\\');
                	str=str.replace(/\\0/g,'\0');
        		return str;
       		 }
	

		 function liste_fen_actives() {
        	//liste des fenetres actives
                var indice=0;
                var tab= new Array();
                var fen = document.getElementsByClassName('dhtmlwindow');
                for (var i = 0; i < fen.length; i++) {
                        var t = $(fen[i].id);
                        if (t.style.display == 'block') {
                                tab[indice] = t.id;
                                indice++;
                        }
                }
                return tab;
        	}

		function maxZindex() {
                	var max = 0;
                	var z=liste_fen_actives();
        
                	for (var i=0;i<z.length;i++ ) {
                        	var reg = new RegExp("ajaxWindCmd", "i");
                        	if (reg.exec(z[i].id) == null)   {
                                	//alert($(z[i]).style.zIndex)
                                	if (parseInt($(z[i]).style.zIndex) >  max) {
                                        	max = parseInt($(z[i]).style.zIndex);
                                	}   
                        	}
                	}
                
                	return(max);
        	}

        
        	function inhibit_openmax(d) {
                	var sourceobj =$(d).controls;
                	sourceobj.childNodes[1].src = 'images/no_close.gif';
        	}

		 function inhibit_close(d) {
                	var sourceobj =$(d).controls;
                	sourceobj.lastChild.src = 'images/no_close.gif';
			//inhibit_openmax(d);
        	}

        	function inhibit_max(d) {
                	var sourceobj =$(d).controls;
                	sourceobj.childNodes[3].src = 'images/no_close.gif';
        	}

		function init() {
			var url = 'viewScenExt.php';
			var params = '?id_scen=<?php echo $id ?>';
			new Ajax.Request(url, {parameters: params, method: 'post', onComplete: function (xhr){
				try {
					alert(xhr.responseText);
					eval(xhr.responseText);
				} catch (err) {
					alert(err);
				}
			}});
		}
		Event.observe(window,'load',init,false);
	</script>
</head>
<body>
<div id="content"></div>
</body>
</html>
