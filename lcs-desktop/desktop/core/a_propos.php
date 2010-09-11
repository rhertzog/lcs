<!DOCTYPE html>
<html lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="robots" content="noindex" />
<title>LCS Bureau - A propos</title>
<style>
body{margin:25px;color:#444;}
h3{
	color:#123456;
}
li{
	list-style:none;
}
</style>
</head>
<body style="background:#fff;">
	<div style="min-height:100%;height:100%;margin-bottom:-60px;padding-bottom:60px;border-bottom:1px solid #aaa;">
		<h3>Lcs-Bureau</h3>
		<?php
		require  "/var/www/lcs/includes/headerauth.inc.php";
		$query = "SELECT * from applis where name = \"desktop\"";
		$result=@mysql_db_query("$DBAUTH",$query, $authlink);
		$r_=@mysql_fetch_array($result);
		echo "Version : 1.0 (<span style=\"font-style:italic;font-size:.85em;\">version paquet :".$r_[5]."</span>)<br />";
		?>
		LCS-Bureau est distribu&eacute; sous licence libre <abbr title="Licence Publique Générale GNU">GNU GPL</abbr>.<br />Lcs-Bureau &eacute;tant distribu&eacute; gratuitement sous licence libre, ces auteurs n&rsquo;offrent aucune garantie d&rsquo;aucune sorte quant &agrave; l&rsquo;utilisation que vous en ferez et ne peuvent &ecirc;tre tenus pour responsables des dommages que celle-ci pourrait induire.</li>
		<h5>Test&eacute; sous :</h5>
		<ul>
			<li>Firefox 3.6.8</li>
			<li>Safari 5.0.1 <small>(6533.17.8)</small></li>
			<li>Google Chrome 6.0 <small>(6.0.472.53)</small></li>
			<li>Internet Explorer 8</li>
		</ul>
		<h3>Propuls&eacute; par :</h3>
		<ul>
			<li>
			<a href="http://sonspring.com/journal/jquery-desktop" title="Jquery">Jquery</a>, <a href="http://sonspring.com/journal/jquery-desktop" title="Jquery Desktop">Jquery Desktop</a> de Nathan Smith
			</li>
		</ul>
		<h3>Auteurs :</h3>
		Lcs-Bureau est d&eacute;velopp&eacute; par l'&eacute;quipe TICE du CRDP de Basse-Normandie.<br />
		Lcs-Team :
		<ul style="font-size:.8em;font-family:tahoma;">
			<li>Jean-Luc Chr&eacute;tien - Chef de projet - syt&egrave;mes <small>&lt;(-_&deg;)/&gt;</small></li>
			<li>Simon Cavey -  S&eacute;curit&eacute; - syt&egrave;mes <small>6|&lsquo;v&rsquo;|&copy;</small> </li>
			<li>Philippe Leclerc -  Plugins -  syt&egrave;mes <small>&lsaquo;(&deg;&#8169;&deg;)&rsaquo;</small></li>
			<li>Olivier Lecluse - Communication - syt&egrave;mes <small>w@w@</small></li> 
			<li>Yannick Chistel - Graphisme <small>y@y@</small></li>
			<li>Dominique Lepaisant <small>&lsaquo;|0|&deg;v&deg;|0</small></li>
		</ul>
		<h5>Contact</h5>
		<pre>LcsDevTeam@tice.ac-caen.fr</pre>
	</div>
<div id="remerciements" style="height:48px;font-size:.5em;border-top:1px solid #aaa;padding:10px;">
				 Nous remercions : nos chiens, les mouettes rieuses, Robert et Simone, la machine &agrave; caf&eacute;, les after-kermesses, les trois p'tits cochons, les Digitals Natives, la mob &agrave; Lulu, la machine &agrave; caf&eacute;, la dame du self qui dit toujours <i>"Bon app&eacute;tit et bon week-end, monsieur</i>", Qbi4, m&egrave;re Denis, la bm &agrave; Rom, les bons anti-virage, la machine &agrave; caf&eacute;, le tracteur &agrave; David qu'a perdu son marteau, Darkwador, les sauvageons, Bob l'Eponge, la machine &agrave; caf&eacute;, euhhh, non, pas Bob l'Eponge, les marais de Carentan...
</div>
<script src="js/jquery-1.4.2.min.js"></script>
<script>
$(document).ready(function(){
$('a').not('open_win').attr('target', '_blank');
});
</script>
<div id="footer"></div>
</body>
</html>