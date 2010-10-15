<?
include "includes/secure_no_header.inc.php";

if ($_POST) {
	extract($_POST);
}

if ($ML_Adm == 'Y')
	die('ok');


if ($id == 'scenario_choix') {
	if (is_eleve($uid))
		die('ko');
	if ($id_scen == -1)
		die('ok');

	$sql = "select * from monlcs_db.ml_scenarios where `setter`='$uid' and `id_scen`='$id_scen';";
	$c = mysql_query($sql) or die("ERREUR $sql");
	if (mysql_num_rows($c) == 0)
		die('ko');
	else
		die('ok');
}


if ( is_administratif($uid)  && ($id == 'vs') )
	die(stringForJavascript('ok'));

if ($id == 'bureau')
	die(stringForJavascript('ok'));

if (is_perso_tab($id))
	die(stringForJavascript('ok'));

$content = 'ko';

$sql = "select * from monlcs_db.ml_notes where id='$note' and setter='$uid'";
$c = mysql_query($sql) or die ("ERR $sql");
if (mysql_num_rows($c) != 0)
	$content = 'ok';


print(stringForJavascript($content));

?>
