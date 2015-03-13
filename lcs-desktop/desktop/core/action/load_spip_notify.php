<?php
/*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/
* Projet LCS - Lcs-Desktop
* auteur Dominique Lepaisant (DomZ0) - dlepaisant@ac-caen.fr
* Equipe Tice academie de Caen
* version  Lcs-2.4.10
* Derniere mise a jour " => mrfi =>" 14/03/2015
* Licence GNU-GPL -  Copyleft 2010
*__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/__/*/
$site = "http://".$_SERVER['HTTP_HOST']."/spip/?page=backend";

$file = @fopen($site, 'r');
if(is_dir('/usr/share/lcs/spip/') && $file){
	$rss = simplexml_load_file($site);
	$resp="<div id=\"notify_container\">";
	$ptrn = array("/T/","/Z/");
	$repl = array(" - "," ");
	foreach ($rss->channel as $channel) {
		$i=0;
		foreach ($channel->item as $item) {
			if($i<1){
				$resp.="<h3>".$item->title."</h3>";
				$resp.="<p>".$item->description."<br />";
		  		foreach ($item->children('http://purl.org/dc/elements/1.1/') as $tag => $itm) {
		  			$resp.=$tag=="date"? "<span class=\"forum_date\" style=\"font-size:.85em;font-style:italic;\">".preg_replace($ptrn, $repl, $itm)."</span>" : '';
	    		}
				$resp.="</p>";
				$i++;
			}
		}
	}
	$resp.="<span class=\"spip_id_article\" style=\"display:none\">-1</span></div";
	echo $resp;
}
else{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="#LANG">
<head>
	<title>Erreur de lecture de fichier</title>
</head>
<body>
	<div id="notify_container">
		<h3>Erreur</h3>
		<p>
			Le module spip ne semble pas install&eacute; ou n'offre pas de fil RSS.<br />
		</p>
		<br style="clear:both;" />
		<span class="spip_id_article" style="display:none">-1</span>
	</div>
</body>
</html>
<?php
}
?>
