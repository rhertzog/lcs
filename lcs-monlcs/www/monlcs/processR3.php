<?
include "includes/secure_no_header.inc.php";
?>
<body>
<div id = "divLCS">
<form method="post" action="processR3.php" onsubmit="multipleSelectOnSubmit()">
<?
$liste = "<input id=tab name=tab value=$liste readonly />";

if ($_POST) {
extract($_POST);

if (eregi('perso',$tab)) {

	$num = substr($tab,5);
	$num = $num +1;
	
		$sql21 =  "SELECT * from `monlcs_db`.`ml_zones` WHERE rang='$num' and user='all' ;";
		$c21 = mysql_query($sql21) or die ("ERR $sql21");
		if (mysql_num_rows($c21) != 0) {
			$idT = mysql_result($c21,0,'id');
			$sql2 =  "SELECT * from `monlcs_db`.`ml_tabs` WHERE id_tab='$idT' and user='all' ;";
			$c2 = mysql_query($sql2) or die ("ERR $sql2");
			if (mysql_num_rows($c2) != 0) {
				$liste = "<select id=\"tab\" name=\"tab\">";
				for ($x=0;$x<mysql_num_rows($c2);$x++) {
					$R = mysql_fetch_object($c2);
					if (($R->nom != 'actu') && ($R->nom != 'lcs'))
						$liste .="<option value=$R->nom>$R->caption</option>";
				}
				$liste .="</select>";
			}
		}
}



    if ($ML_Adm == 'Y' ) 
	$sql22 = "SELECT * from `monlcs_db`.`ml_ressources` where owner != 'mrT' ORDER by titre;";
    else
	$sql22 =  "SELECT * from `monlcs_db`.`ml_ressources` WHERE (owner='$uid' OR statut='public' OR owner = 'all_profs' AND owner != 'mrT') order by titre ;";
    $c22 = mysql_query($sql22) or die ("ERR $sql22");

    if (mysql_num_rows($c22) != 0) {
        $listeRess = "<select id=url name=url>";
        for ($x=0;$x<mysql_num_rows($c22);$x++) {
            $R = mysql_fetch_object($c22);
            $url2 = $R->url;
           
            if (eregi('RSS',$R->RSS_template))
                $brique = ' [RSS] ';
            else
                $brique = '';
            if (($R->url != ''))
                $listeRess .= "<option value=".$R->id.">".$brique.$R->titre."</option>";
           

        }
    $listeRess .="</select>";
    }

 
echo "<div id=bcg>";
echo "<div id=divR><B>Ressource:</B><BR />
        <div id=ress>$listeRess<BR /></div><div id=img onclick=javascript:viewUrl();>$view_img</div>";
echo "</div>";
echo "<div id=divN><div id=img onclick=javascript:viewNote();>$view_img</div><B>Note:</B><BR />$listeNote<BR /></div>";


echo "<BR /><BR /><BR /><div class=div1><B>Menu actif:&nbsp; </B></div><div class=div2> $liste </div>";
echo "<div class=div1><B>Matiere: </B></div><div class=div2> <select id=\"matiere\" name=\"matiere\">";



echo "<option value=-1>-</option>";
if (($ML_Adm == 'Y') ||  is_administratif($uid))
	$matieres = search_groups("cn=mati*");
else {	
	$matieres = matieres_prof($uid);
     }



foreach($matieres as $mat) {
$eq = $mat['cn'];
echo "<option value='".$eq. "' class='group'>$eq</option>";
}
echo "</select></div>";




$idR =substr($r,8);
echo "</div>";

?>

<div class=ldapBOX><select multiple name="fromBox" id="fromBox">
<?


if ( ($ML_Adm == 'Y') || is_administratif($uid) ) {
    $groups = search_groups("cn=*");
    	$gr = array();
			foreach($groups as $group) {
				$gr[] = $group['cn'];
			}
			////remouliner le $groups pour presenter Admins Profs Eleves
			
			$flux = implode('#', $gr);
			$pattern = array("Profs#","Eleves#","Admins#");
			$repl = array("","","");
			$flux = str_replace($pattern, $repl, $flux);
			$flux = "Admins#Eleves#Profs#".$flux;
			$gr = explode('#',$flux);
			$groups = array();
			for ($x=0;$x<count($gr);$x++) {
				$g = array();
				$g['cn'] = $gr[$x];
				$groups[] = $g;
			}
    } else {
    list($user,$groups)=people_get_variables($uid, true);
    }




foreach($groups as $group) {
$eq = $group['cn'];

if ( ($ML_Adm != 'Y') && !is_administratif($uid) ) {
if (eregi('equipe',$eq)) {
    $info = explode('_',$eq);
    $info[0] = 'Classe';
    $eq2 = implode('_',$info);     echo "<option value='".$eq2. "' class='group'>$eq2</option>";
    }
    }

echo "<option value='".$eq. "' class='group'>$eq</option>";
    
}
?>
    
</select>

<select multiple name="toBox" id="toBox">
</select>
<p>Choissisez le(s) groupe(s) cible(s)</p>
</div>


<!-- Ajouté le 30 juin par MrT -->
<!-- <div id="publication_acad"><input type="checkbox" id="acad_pub" name="acad_pub" />Publier sur le d&eacute;pot <BR />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;acad&eacute;mique.</div> -->
<!-- Fin ajout -->



</form>

<div id="bouton-sauve">
    <a class="go" href="#" onclick="javascript:savePropose();">Enregistrer</a>
</div>

</body>
</html>

<?
}//if get
?> 

