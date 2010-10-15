<?
include "includes/secure_no_header.inc.php";

$content="	<div id=header> ";
$content.="		<div id=addNewFeed>";
$content.="			<form method=post action=#>";
$content.="			<table cellpadding=0 cellspacing=0>";
$content.="				<tr><td colspan=4><strong>New feed</strong></td></tr>";
$content.="				<tr><td>RSS url: </td><td colspan=4><input type=text name=rssUrl size=30 value=http://www.dhtmlgoodies.com/rss/dhtmlgoodies.xml maxlength=255></td></tr>";
$content.="				<tr>";
$content.="					<td>Items: </td>";
$content.="					<td><input type=text name=items value=10 size=2 maxlength=2></td>";
$content.="					<td>&nbsp;Refresh every:</td>";
$content.="					<td><input type=text name=reloadInterval value=10 size=2 maxlength=2></td>";
$content.="					<td>&nbsp;minute</td>";
$content.="				</tr>";
$content.="				<tr>";
$content.="					<td>Fixed height:</td>";
$content.="					<td><input type=text name=height value=150 size=2 maxlength=3> </td>";
$content.="					<td><input type=button onclick=createFeed(this.form) value=Create></td>";
$content.="				</tr>";
$content.="			</table>";
$content.="		</form>";
$content.="		</div>";
$content.="	</div>";
$content.="	<div id=floatingBoxParentContainer>";
$content.="	</div>";


echo $content;
?>