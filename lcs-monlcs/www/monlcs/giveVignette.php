<?php
include "includes/secure_no_header.inc.php";

$content = 'rien';

if ($_POST) {
	extract($_POST);
	$ref=substr($ref,8);
	if (eregi('Cmd',$ref)) 
		die(stringForJavascript('rien'));

	if (eregi('Note',$ref)) { 
		$ref=substr($ref,4);	
		die(stringForJavascript("rien"));
	}


	$sql = "select * from ml_tabs where nom='".$tab."';";
	$curseur=mysql_query($sql) or die(stringForJavascript("ERR $sql"));
	if ( mysql_num_rows($curseur) != 0 ) {
		$id_tab = mysql_result($curseur,0,'id');
	}


	if ($tab != 'lcs') {

			$sql = "SELECT * from `monlcs_db`.`ml_ressources` WHERE `id` =".$ref."  ;";
			$curseur=mysql_query($sql) or die("<ul><li>$sql requete invalide</li></ul>"); 
		}
		else { // ress = lcs
			$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");
			$sql = "SELECT * from `lcs_db`.`applis` WHERE `id` =".$ref."  ;";
			$curseur=mysql_query($sql) or die("<ul><li>$sql requete invalide</li></ul>"); 
	    } 

		if (mysql_num_rows($curseur) !=0) {
			mysql_connect($host,$userDB,$passDB) or die('Connexion mysql impossible!');
			$sqlA = "select * from ml_geometry where id_menu='$id_tab' and user = '$uid' and id_ressource='$ref' ;";
			$cA = mysql_query($sqlA) or die("ERR $sqlA");
			
			//C'est une ressource privee
			if (mysql_num_rows($cA) != 0) {
			$width = mysql_result($cA,0,'w');
			$height = mysql_result($cA,0,'h');
			}
			else {
				//proposee par defaut ?
				$sqlB = "select * from ml_geometry where id_menu='$id_tab' and user = 'skel_user' and id_ressource='$ref' ;";
				$cB = mysql_query($sqlB) or die("ERR $sqlB");
				if (mysql_num_rows($cB) != 0) {
					$width = mysql_result($cB,0,'w');
					$height = mysql_result($cB,0,'h');
				} else {
				//ressource imposée
					$sqlB2 = "select * from ml_ressourcesAffect where id_menu='$id_tab' and id_ressource='$ref' ;";
					$cB2 = mysql_query($sqlB2) or die("ERR $sqlB2");
					if (mysql_num_rows($cB2) != 0) {
						$width = mysql_result($cB2,0,'w');
						$height = mysql_result($cB2,0,'h');
					} else {
						$sqlB23 = "select * from ml_scenarios where type='ressource' and id_ressource='$ref' ;";
						$cB23 = mysql_query($sqlB23) or die("ERR $sqlB23");
						if (mysql_num_rows($cB23) != 0) {
							$width = mysql_result($cB23,0,'w');
							$height = mysql_result($cB23,0,'h');
						} else {
							$width = 250;
							$height = 250;
						}

					}	

				}


			}

			if ($tab != 'lcs') {
			$url = mysql_result($curseur,0,'url');
			if (eregi('.swf',$url))
				$url='giveCleanFlash.php?url='.$url;
			$titre = mysql_result($curseur,0,'titre');
 			$url_vignette = mysql_result($curseur,0,'url_vignette');
			} else {
				$r = mysql_fetch_object($curseur);
				if ($r->name) {
					$url = '../lcs/statandgo.php?use='.$r->name;
					$titre = $r->descr;
				}

			
			}


			if ($url_vignette != null) {
				if (eregi('thumbalizr',$url_vignette))
					$urlAffiche = 'giveCleanVignette.php?url='.$url;
				else
					$urlAffiche = $url_vignette;
			}
			else
			$urlAffiche = $url;
			
			$urlAffiche = urlencode($urlAffiche);			

			$content = "var ajaxWind$ref=dhtmlwindow.open('ajaxWind$ref','iframe','$urlAffiche','$titre',";
			$content .= "'width=$width"."px".",height=$height"."px".",left=$posx"."px".",top=$posy"."px".",";
			$content .= "resize=1,scrolling=1,center=0'";
			$content .= ");";
			$content .= "desinhibit_close(ajaxWind$idR);";
		
			if ($uid == $owner && ($imposeur != 'mrT') ) 
				$content .= "showPen(ajaxWind$idR);";
			else
				$content .= "maskPen(ajaxWind$idR);";
			}
      
    
	}//if post

print(stringForJavascript($content));
?>
