<?
	include "includes/secure_no_header.inc.php";
	echo "&nbsp;<B>Matiere: </B>&nbsp;<select id=\"scen_matiere\" name=\"scen_matiere\" onchange=javascript:ProposeByMat();>";
	echo "<option value='".'wawa'. "' class='group'>-</option>";

	$matieres = array();
	$sql = "select matiere from monlcs_db.ml_ressourcesProposees where 1";
	$c = mysql_query($sql) or die ("ERR $sql");
	if ($c) {
		while ($r = mysql_fetch_object($c)) {
			if (!in_array($r->matiere,$matieres)  && ($r->matiere != '-1'  && ($r->matiere != '-')))
			$matieres[] = $r->matiere;
		}
	}

	foreach($matieres as $mat) {
		$eq = $mat;
		echo "<option value='".$eq. "' class='group'>$eq</option>";
	}

	echo "</select><BR /><BR /><BR />";
?>
<div id="update"></id>

