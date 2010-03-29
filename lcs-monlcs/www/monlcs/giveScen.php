<?
session_start();
include "includes/secure_no_header.inc.php";
echo "&nbsp;<B>Matiere: </B>&nbsp;<select id=\"scen_matiere\" name=\"scen_matiere\" onchange=javascript:ScenByMat();>";
echo "<option value='".'wawa'. "' class='group'>-</option>";


$matieres = array();
$sql = "select matiere from monlcs_db.ml_scenarios where 1";
$c = mysql_query($sql) or die ("ERR $sql");
if ($c) {
while ($r = mysql_fetch_object($c)) {
	if (!in_array($r->matiere,$matieres))
		$matieres[] = $r->matiere;
}
}

foreach($matieres as $mat) {
	$eq = $mat;
	if (isset($_SESSION['SCEN_MAT']) && ($_SESSION['SCEN_MAT'] == $eq))
		$plus = "selected";
	else
		$plus ="";
	echo "<option value='".$eq. "' class='group' $plus>$eq</option>";
}


echo "</select><BR /><BR /><BR />";
?>
<div id="update"></id>

