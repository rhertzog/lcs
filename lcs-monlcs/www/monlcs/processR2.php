<?
include "includes/secure_no_header.inc.php";

?>
<html><head>
<link rel="stylesheet" type="text/css" href="./Styles/admin.css" />	
<script type="text/javascript" src="lib/choixCible.js"></script>
</head>
<?

mysql_connect($host,$userDB,$passDB) or die('Connexion mysql impossible!');
mysql_select_db($DB) or die('Base inconnue!');

?>

<BODY>
<div id = "divLCS">
<form method="post" action="processR2.php" onsubmit="multipleSelectOnSubmit()">
<? 
if ($_POST) {
extract($_POST);




$fen = $fen;
$ress = explode(',',$fen);
echo "<BR /><B>Menu actif:&nbsp; </B> $tab ";


echo "<BR/><BR/><center><table><tr><td class=grise>Ressource</td></tr>";
foreach($ress as $r) {

$idR =substr($r,8);


if ($tab != 'lcs') {
	$sq = "select * from monlcs_db.ml_ressources where id='$idR';";
	$c = @mysql_query($sq) or die($sq);
	if ($c)
	echo "<tr><td>".@mysql_result($c,0,'titre')."</td></tr>";
	} 

	else {
	
	//appli LCS
		require ("/var/www/lcs/includes/config.inc.php");
		require_once ("/var/www/lcs/includes/functions.inc.php");
		$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");

	$query="SELECT * from lcs_db.applis where id='$idR'";
	$result=mysql_query($query);
	if ($result) {
	$r=mysql_fetch_object($result);
	echo "<tr><td>".$r->descr."</td></tr>";
	}
	mysql_free_result($result);
	}
} //foreach
echo "</table>";
}//if get

?>

<BR /><select multiple name="fromBox" id="fromBox">
<?


if ( ($ML_Adm == 'Y') || is_administratif($uid) ) {
	$groups =search_groups("cn=*");
	} else {
	list($user,$groups)=people_get_variables($uid, true);
	}




foreach($groups as $group) {
$eq = $group['cn'];

if ($ML_Adm != 'Y') {
if (eregi('equipe',$eq)) {
	$info = explode('_',$eq);
	$info[0] = 'Classe';
	$eq2 = implode('_',$info); 
	echo "<option value='".$eq2. "' class='group'>$eq2</option>";
	}
	}


echo "<option value='".$eq. "' class='group'>$eq</option>";
}
?>
	
</select>

<select multiple name="toBox" id="toBox">
</select>
</center>
</form>
<script type="text/javascript">
createMovableOptions("fromBox","toBox",300,200,'Groupes disponibles','Groupes selectionn&eacute;s');
</script>

<p>Choissisez le(s) groupe(s) cible(s)</p>
		

<center><div  <A class="go" href="#" onclick="javascript:savePublish();">Enregistrer</a></div></center>
</div>
</body>
</html>