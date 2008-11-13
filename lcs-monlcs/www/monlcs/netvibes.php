<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset= iso-8859-1" />
		<link rel="stylesheet" type="text/css" href="./Styles/style.css" />	
		<script type="text/javascript" src="./lib/prototype.js"></script>
		<script type="text/javascript">
		    function init_synchro() {
				//test de lancer un triatement sur un serveur distant
				var url = 'http://mrt.maison.ac-caen.fr/monlcs/lib/mon_lcs.js';
				var params='url='+url;
				new Ajax.Request('curl_send.php',{ method: 'get', parameters: params, onComplete: function(requester) {
					alert(requester.responseText);
				}
				});
			}
			function init() {
			Event.observe('btn_test','click',init_synchro);
			}
			Event.observe(window,'load',init);
		</script>
		<title>Test de synchro Ajax<title>
	</head>
	<body>
		<input id="btn_test" name="TEST" value="Clic :)" type="button" />
		
	</body>
</html>