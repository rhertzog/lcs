<?
include "includes/secure_no_header.inc.php";
if ($_POST)
	extract($_POST);
$users = give_users($uid);
$content = "<select class=short_select id=liste_users>";
foreach ($users as $user_tab) {
	$content .="<option value=".$user_tab['uid'].">".$user_tab['uid']." (".$user_tab['group'].")</option>";
}

$content .= "</select>";



//die(print_r($content));
print(stringForJavascript($content));
?>
