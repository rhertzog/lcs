<!DOCTYPE html>
<html lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="robots" content="noindex" />
<link href="css/desktop.css" rel="stylesheet" />
<title>LCS Bureau - A propos</title>
<style>
body{margin:10px 25px;color:#444;}
#a_propos{
	min-height:100%;
	height:100%;
	margin-bottom:60px;
	padding-bottom:60px;
	border-bottom:1px solid #aaa;
	text-align:left;
	line-height:16px;
}
#a_propos h1{
	color:#123456;
	margin:0;
	padding:3px 10px;
	line-height:22px;
	background-position:bottom left;
}
#a_propos h3{
	margin:10px 0 0 0;
	color:#123456;
	padding:0 10px;
	line-height:20px;
	background-position:bottom left;
}
#a_propos ul{
	list-style:none;
	margin:0 0 0 25px;
	padding:0;
}
#a_propos ul.demi li{
	width:205px;
	float:left;
font-size:.8em;font-family:tahoma;
}
#a_propos ul.demi li.titre{
	width:auto;
	float:none;
}
#a_propos li{
	list-style:none;
	line-height:12px;
}
#a_propos small{
	font-style:italic;
}
#a_propos p{
	padding:0;
	margin:5px 5px 0 25px;
}
#a_propos blockquote{
	border:1px solid #aaa;
	padding:5px;
	font-style:italic;
	font-size:.85em;
	font-color:#123456;
}
</style>
		<?php
		require  "/var/www/lcs/includes/headerauth.inc.php";
		$query = "SELECT * from applis where name = \"desktop\"";
		$result=@mysql_db_query("$DBAUTH",$query, $authlink);
		$r_=@mysql_fetch_array($result);
		$ver= "<p>Version paquet LCS :".$r_['version']."</p>";
		?>
</head>
<body style="background:#fff;">
	<div id="a_propos">
		<h1>Lcs-Bureau</h1>
		<h3>Version : 1.0 - XP</h3> 
		<?php echo " ".$ver; ?>
		<h3>License</h3>
		<p>LCS-Bureau est distribu&eacute; sous licence libre <abbr title="Licence Publique G&eacute;n&eacute;rale GNU">GNU GPL</abbr></p>
		<h3>Auteurs :</h3>
		<p>Lcs-Bureau est bas&eacute; sur <a href="http://sonspring.com/journal/jquery-desktop" rev="web" class="open_win ext_link" title="jquery-Desktop">JQuery Desktop</a> by Nathan Smith et d&eacute;velopp&eacute; et maintenu par l' &Eacute;quipe TICE du CRDP de Basse-Normandie. (<small>Auteur : <a href="mailto:dlepaisant@ac-caen.fr?subject=[Lcs-Bureau]" title="dlepaisant AT ac-caen.fr"> Dominique Lepaisant</a></small>)
		<ul  class="demi">
			<li class="titre"><strong>LcsDevTeam</strong></li>
			<li>Jean-Luc Chr&eacute;tien - Chef de projet Lcs <small>&lt;(-_&deg;)/&gt;</small></li>
			<li>Simon Cavey <small>6|&lsquo;v&rsquo;|&copy;</small> </li>
			<li>Philippe Leclerc <small>&lsaquo;(&deg;&#8169;&deg;)&rsaquo;</small></li>
			<li>Olivier Lecluse <small>w@w@</small></li> 
			<li>Yannick Chistel <small>y@y@</small></li>
			<li>Dominique Lepaisant <small>&lsaquo;|0|&deg;v&deg;|0</small></li>
		</ul><br style="clear:both" /></p>
		<h3>Contact : <small><a href="mailto:LcsDevTeam@tice.ac-caen.fr?subject=[Lcs-Bureau]" title="LcsDevTeamATtice.ac-caen.fr">LcsDevTeam@tice.ac-caen.fr</a></small></h3>
		<p><strong>Test&eacute; sous : </strong>Firefox 3.6.x, Safari 5.x.x, Chrome 6.0, Internet Explorer 8</p>
		<blockquote>Lcs-Bureau &eacute;tant distribu&eacute; gratuitement sous licence libre, ces auteurs n&rsquo;offrent aucune garantie d&rsquo;aucune sorte quant &agrave; l&rsquo;utilisation que vous en ferez et ne peuvent &ecirc;tre tenus pour responsables des dommages que celle-ci pourrait induire.</blockquote>
	</div>
<div id="remerciements" style="height:48px;font-size:.5em;border-top:1px solid #aaa;padding:10px;display:none;">
				 Nous remercions : nos chiens, les mouettes rieuses, Robert et Simone, la machine &agrave; caf&eacute;, les after-kermesses, les trois p'tits cochons, les Digitals Natives, la mob &agrave; Lulu, la machine &agrave; caf&eacute;, la dame du self qui dit toujours <i>"Bon app&eacute;tit et bon week-end, monsieur</i>", Qbi4, m&egrave;re Denis, la bm &agrave; Rom, les bons anti-virage, la machine &agrave; caf&eacute;, le tracteur &agrave; David qu'a perdu son marteau, Darkwador, les sauvageons, Bob l'Eponge, la machine &agrave; caf&eacute;, euhhh, non, pas Bob l'Eponge, les marais de Carentan...
</div>
<script src="js/jquery-1.4.2.min.js"></script>
<script>
/*$(document).ready(function(){
$('a').not('open_win').attr('target', '_blank');
});*/
</script>
</body>
</html>