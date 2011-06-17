<?php
require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Gestionnaire de fichiers</title>
	<link rel="stylesheet" href="js/ui-themes/base/ui.all.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<!-- elfinder.css must be called after ui.all.css -->
	<link rel="stylesheet" href="css/elfinder.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<script src="../libjs/jquery/jquery.js" type="text/javascript" charset="utf-8"></script>
	<script src="../libjs/jquery-ui/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/elcsfinder.full.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/i18n/elfinder.fr.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" charset="utf-8">
		$().ready(function() {		
			$('#finder').elfinder({
				url : 'connectors/php/connector.php',
				lang : 'fr',
				cutURL:'public_html',
				editorCallback : function(url) {
					//alert('callback');
					//if (confirm(url.replace('public_html/','')))
					parent.JQD.init_link_open_win(
						'<a href="'+url.replace('public_html/','')+'"'
						+' rel="webperso"'
						+' rev="webperso"'
						+' title="webperso"'
						+' class="open_win ext_link">'
						+'</a>'
					);					
				},
				closeOnEditorCallback : false
				// docked : true,
				// dialog : {
				// 	title : 'File manager',
				// 	height : 500
				// }
			})
			// window.console.log(f)			
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
		}
		?>
		})
	</script>
</head>
<body>
	<div id="finder">finder</div>
</body>
</html>
