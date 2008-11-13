<?php
include('class_input_filter.php');
$host ='localhost';
$userDB='monlcs_user';
$passDB='#PASS#';
$DB='monlcs_db';
$cx = mysql_connect($host,$userDB,$passDB) or die('ERREUR ACCES SQL');
mysql_select_db($DB) or die('choix base ko');
$cheminPlugins = '/usr/share/lcs/Plugins/';

$aAllowedTags = array("a", "b", "blink", "blockquote", "br", "caption", "center", "col", "colgroup", "comment", "div", 
                      "em", "font", "h1", "h2", "h3", "h4", "h5", "h6", "hr", "img", "li", "marquee", "ol", "p", "pre", "s",
                      "small", "span", "strike", "strong", "sub", "sup", "table", "tbody", "td", "tfoot", "th", 
                      "thead", "tr", "tt", "u", "ul");


$aAllowedAttr = array("abbr", "align", "alt", "axis", "background", "behavior", "bgcolor", "border", "bordercolor", 
                      "bordercolordark", "bordercolorlight", "bottompadding", "cellpadding", "cellspacing", "char", 
                      "charoff", "cite", "clear", "color", "cols", "direction", "face", "font-weight", "headers", 
                      "height", "href", "hspace", "leftpadding", "loop", "noshade", "nowrap", "point-size", "rel", 
                      "rev", "rightpadding", "rowspan", "rules", "scope", "scrollamount", "scrolldelay", "size", 
                      "span", "src", "start", "style" ,"summary", "target", "title", "toppadding", "type", "valign", 
                      "value", "vspace", "width", "wrap");


$user_img = '<img title=Bureau_utilisateur src=images/user.gif />';
$group_img = '<img title=Partager src=images/class.gif />';
$delete_img = '<img title=Effacer src=images/delete.png />';
$view_img = '<img title=Voir/placer src=images/oeil.gif />';
$add_img = '<img title=Enregistrer src=images/sauve.png />';
$rename_img = '<img title=Renommer src=images/crayon.png />';
$stats_img = '<img title=Statistiques src=images/stats.png />';
$help_img = '<img src=images/help-info.gif />';
$bourre = '<td>&nbsp;&nbsp;</td>';
$bourre1 = '<td>&nbsp;</td>';
?>
