<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
	<head>
		<title>test ent</title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="../libjs/jquery-ui/css/redmond/jquery-ui.css" />
		<link rel="stylesheet" href="../c/ent.css" />
		<script  type="text/javascript" src="../libjs/jquery/jquery.js"></script>
		<script  type="text/javascript" src="../libjs/jquery-ui/jquery-ui.js"></script>
		<script  type="text/javascript" src="ent.js"></script>
	</head>
<body>
    <div id="accordion_ent">
    <h3><a href="#">Authentification ENT</a></h3>
	    <div id="form_ent" >
	    <p class="ui-state-highlight validateTips"> &nbsp; Permet d'acc&#232;der aux applications ENT <b>et</b> LCS</p>
	    <iframe  id="frame_ent" src="auth_Cas.php"width="99%" height="410px" >
		</iframe>
		</div>
	<h3><a href="#">Authentification LCS</a></h3>

	    <div id="form_lcs">
	    <p class="ui-state-highlight validateTips"> &nbsp; Permet d'acc&#232;der aux applications  LCS pour les utilisateurs n'ayant pas de compte sur l'ENT </p>
	    <iframe  id="frame_lcs" src="auth.php"width="99%" height="400px" >
		</iframe>
		</div>
	</div>
</body>
</html>