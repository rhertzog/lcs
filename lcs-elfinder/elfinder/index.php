<?php
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>elFinder - Explorateur de fichiers</title>
	<!--
	<script type='text/javascript' src='http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'></script>
	<link rel="stylesheet" href="elfinder/css/smoothness/jquery-ui-1.8.13.custom.css" type="text/css" media="screen" title="no title" charset="utf-8">
	-->
	<link rel="stylesheet" href="../libjs/jquery-ui/css/classic-blue-lcs/jquery-ui.css" type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="elfinder/css/elfinder.css" type="text/css" media="screen" title="no title" charset="utf-8">

	<script src="elfinder/js/jquery-1.6.1.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="elfinder/js/jquery-ui-1.8.13.custom.min.js" type="text/javascript" charset="utf-8"></script>

	<script src="elfinder/js/elfinder.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="elfinder/js/i18n/elfinder.fr.js" type="text/javascript" charset="utf-8"></script>
	<!--
	<script src="js/i18n/elfinder.ru.js" type="text/javascript" charset="utf-8"></script>
	-->
	<script type="text/javascript" charset="utf-8">
		$().ready(function() {
		// redirect if no login user
		<?php
		if ($idpers==0) {
		?>
			var titre= $('<h3/>').text('Lcs-elFinder').prepend($('<img/>').attr('src','images/logo_elfinder_70.png'));
		 	$('.el-finder-workzone p.el-finder-err').show('slow').css({fontSize:'1.7em'})
		 	.append(
		 		$('<p/>').css({'color':'#123456'})
		 		.html("Acc&egrave;s refus&eacute; ! <br />Vous devez &ecirc;tre identifi&eacute; sur le LCS pour acc&eacute;der aux applications.<br />Vous allez &ecirc;tre redirig&eacute; dans ").append('<span/>')
			).prepend(titre);
			 //decompte sur 5s
			var i = 6;
			setInterval(function() {$('.el-finder-workzone p.el-finder-err').find('p span').text((i=i-1)+' s')
			i==0 ? window.location.replace("<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/'; ?>") :'';
			},1000);
		
		<?php
		} else {			
		?>
			var f = $('#finder').elfinder({
				url : 'action/connector.php',
				lang : 'fr',
				places : "Favoris",
				toolbar : [
					['back', 'reload'],
					['select'/*, 'open'*/],
					['mkdir', 'mkfile', 'upload'],
					['copy'/*, 'cut'*/, 'paste', 'rm'],
					['quicklook', 'info'], 
					['rename'/*, 'duplicate'*/, 'edit'/*, 'archive', 'extract'*/, 'resize'], 
					['icons', 'list'], 
					['help']
				],
				contextmenu : {
					// Commands that can be executed for current directory
					cwd : ['reload', 'delim', 'mkdir', 'mkfile', 'upload', 'delim', 'paste', 'delim', 'info'], 
					// Commands for only one selected file
					file : ['select'/*, 'open'*/, 'delim', 'copy', 'cut', 'rm', 'delim', 'duplicate', 'rename', 'edit', 'resize', 'delim', 'archive', 'extract', 'delim', 'quicklook', 'info'], 
					// Coommands for group of selected files
					group : ['copy', 'cut', 'rm', 'delim', 'archive', 'extract', 'delim', 'info'] 
					},
				// Callback
				editorCallback : function(url) {
					function hideSpinner() {
						$('#finder').removeClass('el-finder-disabled');
						$('div.el-finder-spinner').hide();
					}
					//console.log('url:',url);
					var ifr=0, si=$("#secretIFrame");
					$('#finder').addClass('el-finder-disabled');
					$('div.el-finder-spinner').show();
					var dl=$('<a/>').attr({
						href: url,
						title: url,
						rel:'Mon espace web'
					});
					var ext = url.split('.').pop().toLowerCase();
					if($.inArray(ext, ["jpg","jpeg","png", "gif", "swf", "html","htm","txt","mov", "avi","mp4"]) !=-1 && !url.match('/Documents')) {
						parent.JQD.init_link_open_win(dl);
						hideSpinner();
					}
					else
					{
						$("#secretIFrame").attr("src","action/download.php?file="+url);
						hideSpinner();
					}
				},
				closeOnEditorCallback : false
			})
		//	 window.console.log(f)
		<?php
		} 		
		?>
		
		})
	</script>

</head>
<body>
	<div id="finder">finder</div>
<iframe id="secretIFrame" src="" style="display:none; visibility:hidden;"></iframe>
</body>
</html>
