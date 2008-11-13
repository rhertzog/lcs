<?php

	include "includes/secure_no_header.inc.php";
	$content = "document.getElementById('content').style.display='none';";
	$tab = htmlspecialchars($_POST['id']);
	
	$compte=0;
	
	if ($tab == 'rss') {
		$content .= "giveRSS();";
		die(stringForJavascript($content));
	}


	if ($tab == 'scenario_choix' ) {
		$content .="giveRessources('$tab');";
		die(stringForJavascript($content));
	}

	if (eregi('perso',$tab) ) {
		if (!is_scenarii($tab))
			$content .= "propose();";
		else
			$content .="giveRessources('$tab')";
	die(stringForJavascript($content));
	}

	$sql = "select * from ml_tabs where nom='".$tab."';";
	//$content .= $sql;
	$curseur=mysql_query($sql) or die(stringForJavascript("ERR $sql"));
	if ( mysql_num_rows($curseur) != 0 ) {
		$idTab = mysql_result($curseur,0,'id');
		$index_tab = mysql_result($curseur,0,'id_tab');
		$Raff = array();
		$mes_groupes = give_groupes($uid);
		//? ressources afectees
		$sql2 ="select * from ml_ressourcesAffect where id_menu= '$idTab' ;";
		$c2 = mysql_query($sql2) or die(stringForJavascript("ERR $sql2"));
		//analyse les ressources associees au menu
		//le bureau reste libre pour tous
		if (!eregi('bureau',$tab)) {
			for ($x=0;$x<mysql_num_rows($c2);$x++) {
				$cible= mysql_result($c2,$x,'cible');
				if (dans_cible($cible)) {
					$idR = mysql_result($c2,$x,'id_ressource');
					//ress affectee trouvee
					$imposeur= mysql_result($c2,$x,'setter');
					$posx = mysql_result($c2,$x,'x');
					$posy = mysql_result($c2,$x,'y');
					$zindex = mysql_result($c2,$x,'z');
					$min = mysql_result($c2,$x,'min');
					$width = mysql_result($c2,$x,'w');
					$height = mysql_result($c2,$x,'h');
					$Raff[] = $idR;
					//je suis concerné !
				if ($tab != 'lcs') {
					$sql3 = "select * from ml_ressources where id=$idR;";
					$c3 = mysql_query($sql3) or die(stringForJavascript("ERR $sql3"));
					if ( mysql_num_rows($c3) != 0 ) {
						$url = mysql_result($c3,0,'url');
						if (eregi('.swf',$url))
							$url='giveCleanFlash.php?url='.$url;
						$url_vignette = mysql_result($c3,0,'url_vignette');
						$owner = mysql_result($c3,0,'owner');
						$titre = (mysql_result($c3,0,'titre'));
						$tp_rss = mysql_result($c3,0,'RSS_template');
						$inhibit = ( $owner != $uid || ($imposeur == 'mrT') );
					}	
				}// if not lcs
				else {
					require ("/var/www/lcs/includes/config.inc.php");
					require_once ("/var/www/lcs/includes/functions.inc.php");
					$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");
					// Liens dynamiques vers les outils LCS
					$query="SELECT * from applis where id ='$idR'";
					$result=mysql_query($query);
					if ($result) {
						$r = mysql_fetch_object($result);
						if ($r->name) {
							$url = '../lcs/statandgo.php?use='.$r->name;
							$titre = $r->descr;
							$inhibit = ( $ML_Adm != 'Y' );
						}
					}//if result
				}//else
				if ($url != false) {
					//$compte++;
					if ($url_vignette != null) {
						if (eregi('thumbalizr',$url_vignette))
							$urlAffiche = 'giveCleanVignette.php?url='.$url;
						else
							$urlAffiche = $url_vignette;
					} else
						$urlAffiche = $url;
					$content .= "var ajaxWind$idR=dhtmlwindow.open('ajaxWind$idR','iframe','$urlAffiche','$titre',";
					$content .= "'width=$width"."px".",height=$height"."px".",left=$posx"."px".",top=$posy"."px".",";
					$content .= "resize=1,scrolling=1,center=0'";
					$content .= ");";
					$content .= "desinhibit_close(ajaxWind$idR);";
					$content .= "ajaxWind$idR.style.zIndex = $zindex ;";
					$content .= "ajaxWind$idR.zIndexvalue = $zindex;";
					if ($min == 'Y')
						$content .= "mini(ajaxWind$idR);";		
					if ($inhibit == true) 
						$content .= "inhibit_close(ajaxWind$idR);";	
					if ($uid == $owner && ($imposeur != 'mrT') ) 
						$content .= "showPen(ajaxWind$idR);";
					else
						$content .= "maskPen(ajaxWind$idR);";
				}//if url
			}//if cible
			
		
		

		}// for
	} //ib not bureau
	
	//ressources propres ?
	mysql_connect($host,$userDB,$passDB) or die('Connexion mysql impossible!');
	mysql_select_db($DB) or die('Base inconnue!');
	if (is_shared_tab($index_tab))
		$sqlA = "select * from ml_geometry where id_menu='$idTab' ;";
	else
		$sqlA = "select * from ml_geometry where id_menu='$idTab' and user = '$uid' ";
	$cA = mysql_query($sqlA) or die("ERR $sqlA");
	
	for ($z=0;$z<mysql_num_rows($cA);$z++) {
		$idRe = mysql_result($cA,$z,'id_ressource');
		$xx = mysql_result($cA,$z,'x');
		$yy = mysql_result($cA,$z,'y');
		$zz = mysql_result($cA,$z,'z');
		$ww = mysql_result($cA,$z,'w');
		$hh = mysql_result($cA,$z,'h');
		$min = mysql_result($cA,$z,'min');
		if ($tab != 'lcs') {
			$sqlB = "select * from ml_ressources where id='$idRe';";
			$cB = mysql_query($sqlB) or die("ERR $sqlB");
			if (mysql_num_rows($cB) > 0) {
				$_titre = (mysql_result($cB,0,'titre'));
				$_url = mysql_result($cB,0,'url');
				if (eregi('.swf',$_url))
					$_url='giveCleanFlash.php?url='.$_url;
				$_url_vignette = mysql_result($cB,0,'url_vignette');
				$_owner = mysql_result($cB,0,'owner');
			}//if cb
		}//if lcs
		else {
			require ("/var/www/lcs/includes/config.inc.php");
			require_once ("/var/www/lcs/includes/functions.inc.php");
			$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");

			// Liens dynamiques vers les outils LCS
			$query="SELECT * from applis where id='$idRe'";
			$result=mysql_query($query);
			if ($result) {
				$r = mysql_fetch_object($result);
				if ($r->name)
					$_url = '../lcs/statandgo.php?use='.$r->name;
				else
					$_url = false;
				$_titre = $r->descr;
			}//if result
		}//else

		if ($_url != false) {
			$compte++;
			
			if ($_url_vignette != null) {
				if (eregi('thumbalizr',$_url_vignette))
					$_urlAffiche = 'giveCleanVignette.php?url='.$_url;
				else
					$_urlAffiche = $_url_vignette;
			}
			else
				$_urlAffiche = $_url;
			$content .= "var ajaxWind$idRe=dhtmlwindow.open('ajaxWind$idRe','iframe','$_urlAffiche','$_titre',";
			$content .= "'width=$ww"."px".",height=$hh"."px".",left=$xx"."px".",top=$yy"."px".",";
			$content .= "resize=1,scrolling=1,center=0'";
			$content .= ");";
			$content .= "inhibit_close(ajaxWind$idRe);";
			$content .= "maskPen(ajaxWind$idRe);";
			$content .= "ajaxWind$idRe.style.zIndex = $zz;";
			$content .= "ajaxWind$idRe.zIndexvalue = $zz;";
			if ( $min == 'Y' )
				$content .= "mini(ajaxWind$idRe);";
			if ($uid == $_owner) 
				$content .= "showPen(ajaxWind$idRe);";
			if (!is_shared_tab($index_tab) || is_perso_tab($tab) )
				$content .= "desinhibit_close(ajaxWind$idRe);";
		}
	}//for

	//notes ?
	
	mysql_connect($host,$userDB,$passDB) or die('Connexion mysql impossible!');
	mysql_select_db($DB) or die('Base inconnue!');

	
	if (is_shared_tab($index_tab))
		$sqlAA = "select * from ml_notes where menu='$tab';";
	else
		$sqlAA = "select * from ml_notes where menu='$tab' and setter = '$uid' ;";

	$cAA = mysql_query($sqlAA) or die("ERR $sqlAA");
	$compte = $compte + mysql_num_rows($cAA);

	for ($z=0;$z<mysql_num_rows($cAA);$z++) {
		$idN = mysql_result($cAA,$z,'id');
		$titre = (mysql_result($cAA,$z,'titre'));
		$setter = mysql_result($cAA,$z,'setter');
		$xx = mysql_result($cAA,$z,'x');
		$yy = mysql_result($cAA,$z,'y');
		$zz = mysql_result($cAA,$z,'z');
		$ww = mysql_result($cAA,$z,'w');
		$hh = mysql_result($cAA,$z,'h');
		$m = mysql_result($cAA,$z,'msg');
		$min = mysql_result($cAA,$z,'min');
		$m = addCslashes($m,chr(39));
		$content .= "var ajaxWindNote$idN=dhtmlwindow.open('ajaxWindNote$idN','inline','$m','$titre',";
		$content .= "'width=$ww"."px".",height=$hh"."px".",left=$xx"."px".",top=$yy"."px".",";
		$content .= "resize=1,scrolling=1,center=0'";
		$content .= ");";
		$content .= "ajaxWindNote$idN.style.zIndex = $zz;";
		$content .= "ajaxWindNote$idN.zIndexvalue = $zz ;";
		$content .= "inhibit_close(ajaxWindNote$idN);";
		$content .= "maskPen(ajaxWindNote$idN);";
		if ($min == 'Y')
			$content .= "mini(ajaxWindNote$idN);";
		if ($uid == $setter) 
			$content .= "showPen(ajaxWindNote$idN);";
		if (!is_shared_tab($index_tab) || is_perso_tab($tab) )
			$content .= "desinhibit_close(ajaxWindNote$idN);";
	}

		
	if ("$compte" == "0") {
		
		//lister les ressources par defaut skel_user
		$sqlRech = "select * from monlcs_db.ml_geometry where user='skel_user' and id_menu='$idTab'";
		$cursRech = mysql_query($sqlRech) or die("ERR $sqlRech");
		if (mysql_num_rows($cursRech) != 0) {
				for($x=0;$x<mysql_num_rows($cursRech);$x++) {	
					$skel_idRe = mysql_result($cursRech,$x,'id_ressource');
					$skel_xx = mysql_result($cursRech,$x,'x');
					$skel_yy = mysql_result($cursRech,$x,'y');
					$skel_zz = mysql_result($cursRech,$x,'z');
					$skel_ww = mysql_result($cursRech,$x,'w');
					$skel_hh = mysql_result($cursRech,$x,'h');
					$skel_min= mysql_result($cursRech,$x,'min');
					if ($tab != 'lcs') {
						$skel_sqlB = "select * from ml_ressources where id='$skel_idRe';";
						$skel_cB = mysql_query($skel_sqlB) or die("ERR $skel_sqlB");
						if (mysql_num_rows($skel_cB) > 0) {
							$skel_titre = (mysql_result($skel_cB,0,'titre'));
							$skel_url = mysql_result($skel_cB,0,'url');
							if (eregi('.swf',$skel_url))
								$skel_url='giveCleanFlash.php?url='.$skel_url;
							$skel_url_vignette = mysql_result($skel_cB,0,'url_vignette');
						}//if cb
					}//if lcs					s
					else {
						require ("/var/www/lcs/includes/config.inc.php");
						require_once ("/var/www/lcs/includes/functions.inc.php");
						$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");
						// Liens dynamiques vers les outils LCS
						$query="SELECT * from applis where id='$skel_idRe'";
						$result=mysql_query($query);
						if ($result) {
							$r = mysql_fetch_object($result);
							if ($r->name)
								$skel_url = '../lcs/statandgo.php?use='.$r->name;
							else
								$skel_url = false;
							$skel_titre = $r->descr;
						}//if result
					}//else
					if ($skel_url) {
						if ($skel_url_vignette != null) {
							if (eregi('thumbalizr',$skel_url_vignette))
								$skel_urlAffiche = 'giveCleanVignette.php?url='.$skel_url;
							else
								$skel_urlAffiche = $skel_url_vignette;
						}
						else
							$skel_urlAffiche = $skel_url;
						$content .= "var ajaxWind$skel_idRe=dhtmlwindow.open('ajaxWind$skel_idRe','iframe','$skel_urlAffiche','$skel_titre',";
						$content .= "'width=$skel_ww"."px".",height=$skel_hh"."px".",left=$skel_xx"."px".",top=$skel_yy"."px".",";
						$content .= "resize=1,scrolling=1,center=0'";
						$content .= ");";
						if ( $skel_min == 'Y' )
							$content .= "mini(ajaxWind$skel_idRe);";
						$content .= "ajaxWind$skel_idRe.style.zIndex = $skel_zz;";
						$content .= "ajaxWind$skel_idRe.zIndexvalue = $skel_zz ;";
						}
				}//for
		}
		//notes par defaut
		mysql_connect($host,$userDB,$passDB) or die('Connexion mysql impossible!');
		mysql_select_db($DB) or die('Base inconnue!');
		$sqlRech = "select * from monlcs_db.ml_notes where cible='skel_user' and menu='$tab'";
		$cursRech = mysql_query($sqlRech) or die("ERR $sqlRech");
			if (mysql_num_rows($cursRech) != 0) {
				for($x=0;$x<mysql_num_rows($cursRech);$x++) {	
					$skelN_id = mysql_result($cursRech,$x,'id');
					$skelN_xx = mysql_result($cursRech,$x,'x');
					$skelN_yy = mysql_result($cursRech,$x,'y');
					$skelN_zz = mysql_result($cursRech,$x,'z');
					$skelN_ww = mysql_result($cursRech,$x,'w');
					$skelN_hh = mysql_result($cursRech,$x,'h');
					$skelN_min= mysql_result($cursRech,$x,'min');
					$skelN_msg= mysql_result($cursRech,$x,'msg');
					$skelN_titre = mysql_result($cursRech,$x,'titre');
					$m = addCslashes($skelN_msg,chr(39));
					$content .= "var ajaxWindNote$skelN_id=dhtmlwindow.open('ajaxWindNote$skelN_id','inline','$m','$skelN_titre',";
					$content .= "'width=$skelN_ww"."px".",height=$skelN_hh"."px".",left=$skelN_xx"."px".",top=$skelN_yy"."px".",";
					$content .= "resize=1,scrolling=1,center=0'";
					$content .= ");";
					$content .= "ajaxWindNote$skelN_id.style.zIndex = $skelN_zz;";
					$content .= "ajaxWindNote$skelN_id.zIndexvalue = $skelN_zz ;";
					//$content .= "inhibit_close(ajaxWindNote$skelN_id);";
					$content .= "maskPen(ajaxWindNote$skelN_id);";
				}
			}
	} 
		
		
		
	}
	
	if ($content == "document.getElementById('content').style.display='none';")
		$content .= "giveRessources('$tab');";
	
	print stringForJavascript($content);
	
	
	
	










?>
