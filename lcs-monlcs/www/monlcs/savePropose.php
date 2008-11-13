<?
include "includes/secure_no_header.inc.php";


if($_POST) {
	extract($_POST);




//ouvre l'envellope

$cible[0]='';
$cible[strlen($cible)-1]='';
//deserialise le flux #
$arr_cible = explode('#',$cible);


$sql2 =  "SELECT * from `monlcs_db`.`ml_tabs` WHERE nom='$tab';";
$curseur2=mysql_query($sql2) or die("ERR $sql2");
if (mysql_num_rows($curseur2) !=0)
	$idTab = mysql_result($curseur2,0,id);



$idR = $idUrl;


for ($z=0; $z< count($arr_cible); $z++) {
	$cible = trim($arr_cible[$z]);

	$sql1 = "SELECT * FROM `monlcs_db`.`ml_ressourcesProposees` WHERE `id_ressource` ='$idR' and  `id_menu`='$idTab' and `cible`='$cible' ;";
	$curseur1=mysql_query($sql1) or die("ERR $sql1");
		if (mysql_num_rows($curseur1) == 0) {
			$sql = "INSERT INTO `monlcs_db`.`ml_ressourcesProposees` ("
			."`id` ,"
			."`id_ressource` ,"
			."`id_menu` ,"
			."`cible` ,"
			."`matiere` ,"
			."`setter`"
			.")"
			." VALUES ("
			."NULL , '$idR', '$idTab', '$cible','$matiere', '$uid'"
			.");";

			$curseur=mysql_query($sql) or die("ERR $sql");
		}
}

 if ($acad_pub == 'true') {
	//	créer un jeton
        //       sauvegarder le xml
        //       appeller le script distant sur CRDP ?jeton= ?etab=
        //       prefixe de jeton RP (ressource proposée) ou SC (scénario)
        //       die(openwin script distant + params) - initialise le traitement sur machien distante
         
       
	$jeton = 'RP_'.md5("$baseurl/$uid/$idTab/$idR/");
       //il faut sauvegarder en sql $jeton $uid $idR $idTab
    
       $now = date("Y-m-d H:i:s");
       
	$sql = "DELETE FROM `monlcs_db`.`ml_acad_propose` WHERE `jeton` = '$jeton' ;"; 
	$c = mysql_query($sql) or die("ERR mysql $sql"); 

       $sql = "INSERT INTO `monlcs_db`.`ml_acad_propose` ("
		."`id` ,"
		."`jeton` ,"
		."`etab` ,"
		."`uid` ,"
		."`id_ress` ,"
		."`menu` ,"
		."`date`"
		.")"
		." VALUES ("
		."NULL , '$jeton', '$baseurl', '$uid', '$idR', '$idTab','$now'"
		.");";
       $c = mysql_query($sql) or die("ERR mysql $sql"); 

       $url_distante = "http://mrt2.demo.ac-caen.fr/monlcs/import_monlcs_acad.php?etab=$baseurl&jeton=$jeton";
       die($url_distante);

 }  else {
 	die("RAS");
 }


}
?>


