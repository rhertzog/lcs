<?php 

 function liste_onglets_etab() {
 	$sql = "SELECT * from monlcs_db.ml_zones where status = 'etab' order by rang";
	$c = mysql_query($sql) or die ("Erreur $sql");
	$liste ='<select style=width: 40px; id=place_etab name=place_etab><option value=-1>-</option>';
	if (mysql_num_rows($c) != 0) {
		for($x=0;$x<mysql_num_rows($c);$x++) {
			$R = mysql_fetch_object($c);
			$liste .= "<option value=$R->rang>$R->nom</option>";
		}
	}
	$liste .= "</select>";
	return($liste);

 }

	
 function is_administratif($login) {
        global $ldap_server, $ldap_port, $dn;
        global $error;
        $error="";

        $filter = "(&(cn=Administratifs*)(memberUid=$login))";
        $ldap_groups_attr = array (
        // LDAP attribute
                "cn",
                "memberUid"    // Membre du Group Profs, Eleves, Administration
        );

        /*-----------------------------------------------------*/
        $ds = @ldap_connect ( $ldap_server, $ldap_port );
        if ( $ds ) {
                $r = @ldap_bind ( $ds );
                if (!$r) {
                        $error = "Echec du bind anonyme";
                } else {
                        // Recherche du groupe d'appartenance de l'utilisateur connecté
                        $result=@ldap_list ($ds, $dn["groups"], $filter, $ldap_groups_attr);
                        if ($result) {
                                $info = @ldap_get_entries( $ds, $result );
                                if ($info["count"]) {
                                        $is_administratif = true;
                                } else {
                                        $is_administratif = false;
                                }
                        }
                }
        }
        @ldap_unbind ($ds);
        @ldap_close ($ds);
        return $is_administratif;
    }


function is_perso_tab($tab) {
	global $uid;
	$sql = "SELECT * from monlcs_db.ml_tabs where nom = '$tab' and user ='$uid';";
	$c = mysql_query($sql) or die ("Erreur $sql");
	return(mysql_num_rows($c) > 0);
}

function is_scenarii($tab) {
	
	$sql = "SELECT * from monlcs_db.ml_tabs where nom = '$tab';";
	$c = mysql_query($sql) or die ("Erreur $sql");
	if (mysql_num_rows($c) == 0)
		return(false);
	$R = mysql_fetch_object($c);
	$id_onglet = $R->id_tab;
	$sql2 = "SELECT * from monlcs_db.ml_zones where id = '$id_onglet';";
	$c2 = mysql_query($sql2) or die ("Erreur $sql2");
	if (mysql_num_rows($c2) == 0)
		return(false);
	$R2 = mysql_fetch_object($c2);
	if ($R2->nom == 'Sc&eacute;narios')
		return(true);
	else
		return(false);

}


function is_etab_tab($tab) {
	
	$sql1 = "SELECT * from monlcs_db.ml_tabs where nom = '$tab' and user ='all';";
	$c1 = mysql_query($sql1) or die ("Erreur $sql1");
	if (mysql_num_rows($c1) == 0)
		return(false);
	$R = mysql_fetch_object($c1);
	$id_onglet = $R->id_tab;
	$sql = "SELECT * from monlcs_db.ml_zones where id = '$id_onglet' and status ='etab';";
	$c = mysql_query($sql) or die ("Erreur $sql");
	return(mysql_num_rows($c) > 0);
}


function is_shared_tab($id_tab) {
	return (false);	
}

function is_shared_menu($menu) {
	$sql = "select * from ml_tabs where nom='".$menu."';";
	$curseur=mysql_query($sql) or die(stringForJavascript("ERR $sql"));
	if ( mysql_num_rows($curseur) != 0 ) {
	$idTab = mysql_result($curseur,0,'id');
	$index_tab = mysql_result($curseur,0,'id_tab');
	return(is_shared_tab($index_tab));
	}
	else return(false);
}


function give_groups_shared($idOnglet) {
	$sql = "SELECT * from monlcs_db.ml_tabs_shared where id_tab = '$idOnglet' ;";
	$c = mysql_query($sql);
	$g = array();
	for ($x=0;$x<mysql_num_rows($c);$x++) {
		$g[] = mysql_result($c,$x,'cible');
	}
	
	return($g);

}

function shared_by($idOnglet) {
	$sql = "SELECT * from monlcs_db.ml_tabs_shared where id_tab = '$idOnglet' ;";
	$c = mysql_query($sql);
	if (mysql_num_rows($c) != 0) {
		return(mysql_result($c,0,'by'));
	}
	else
	return('...');

}



function cmp_uid ($a, $b) {
    return strcmp($a["uid"], $b["uid"]);
}


function give_users($uid) {
  global $ldap_server, $ldap_port, $dn;
  global $adminPw, $adminRdn , $ldap_base_dn ;
  global $error, $ML_Adm;
  
  if ( ($ML_Adm == 'Y') || is_administratif($uid))
  	$groupes = give_all_groupes();
  else
	$groupes = give_groupes();

  
  $r = array();
  foreach($groupes as $group) {
		if (eregi('Equipe_',$group['cn'])) {
			$info = explode('_',$group['cn']);
			$info[0] = "Classe";
			$gp = implode('_',$info);
			$r[] = $gp;
		}
  }
  

  $u = array();
    foreach($r as $g) {
			$liste = search_uids("cn=$g*",true);
						
			if (is_array($liste)) {
				usort($liste,'cmp_uid');
				$u = array_merge($u,$liste);		
			}
    }
  
  
  return($u);
  
  }

function dans_cible($cible) {
	global $uid;
	if ( ($ML_Adm == 'Y')  || is_administratif($uid))
  		$liste = give_all_groupes();
  	else
		$liste = give_groupes();

	foreach ($liste as $g) {
		$liste_plate .= '#'.$g['cn'];
	}
	
return(@eregi("$cible",$liste_plate));
}

function give_groupes() {
  global $ldap_server, $ldap_port, $dn;
  global $adminPw, $adminRdn , $ldap_base_dn ;
  global $error, $uid;
  
  $groups= array();
  $ldap_group_attr = array ( "cn", "description" );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
		
        $r = @ldap_bind ( $ds ); // Bind admin
    if ($r) {
	  $filter = "memberUid=$uid*";
	  
      $result = @ldap_list( $ds, $dn["groups"], $filter, $ldap_group_attr );
	  if ($result) {
	    
        $info = ldap_get_entries( $ds, $result );
		//die(print_r($info));
        if ( $info["count"]) {
          for ($loop=0; $loop < $info["count"]; $loop++) {
			
				$groups[$loop]["cn"] = $info[$loop]["cn"][0];
				$groups[$loop]["description"] = utf8_decode($info[$loop]["description"][0]);
			
		  }
		}
        @ldap_free_result ( $result );
      }
    }
    @ldap_close($ds);
  }
  $loop++;
  $groups[$loop]["cn"] = 'lcs-users';
  $groups[$loop]["description"] = 'Tous les utilisateurs du lcs';

  if (count($groups)) usort($groups, "cmp_cn");
  
  return($groups);
}


function give_groupes_uid($uid) {
  global $ldap_server, $ldap_port, $dn;
  global $adminPw, $adminRdn , $ldap_base_dn;
  global $error;
  
  if(is_administratif($uid))
  	$uid = 'admin';
 

  $groups= array();
  $ldap_group_attr = array ( "cn", "description" );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
		
        $r = @ldap_bind ( $ds ); // Bind admin
    if ($r) {
	  $filter = "memberUid=$uid*";
	  
      $result = @ldap_list( $ds, $dn["groups"], $filter, $ldap_group_attr );
	  if ($result) {
	    
        $info = ldap_get_entries( $ds, $result );
		//die(print_r($info));
        if ( $info["count"]) {
          for ($loop=0; $loop < $info["count"]; $loop++) {
			
				$groups[$loop]["cn"] = $info[$loop]["cn"][0];
				$groups[$loop]["description"] = utf8_decode($info[$loop]["description"][0]);
			
		  }
		}
        @ldap_free_result ( $result );
      }
    }
    @ldap_close($ds);
  }
  $loop++;
  $groups[$loop]["cn"] = 'lcs-users';
  $groups[$loop]["description"] = 'Tous les utilisateurs du lcs';

  if (count($groups)) usort($groups, "cmp_cn");
  
  return($groups);
}


function matieres_prof($uid) {
  global $ldap_server, $ldap_port, $dn;
  global $adminPw, $adminRdn , $ldap_base_dn ;
  global $error;
  
  $scan = give_groupes($uid);
  $groups = array();
  
 
 foreach($scan as $group) {

	if (eregi('Matiere_',$group['cn']))
		$groups[] = $group;
	}


  if (count($groups)) usort($groups, "cmp_cn");
  //die(print_r($groups));
  return($groups);
  
}


function give_all_groupes() {
  global $ldap_server, $ldap_port, $dn;
  global $adminPw, $adminRdn , $ldap_base_dn ;
  global $error, $uid, $ML_Adm;
  
  
  $groups= array();
  $ldap_group_attr = array ( "cn", "description" );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
		
        $r = @ldap_bind ( $ds ); // Bind admin
    if ($r) {
	  $filter = "cn=*";
	  
      $result = @ldap_list( $ds, $dn["groups"], $filter, $ldap_group_attr );
	  if ($result) {
	    
        $info = ldap_get_entries( $ds, $result );
	
        if ( $info["count"]) {
          for ($loop=0; $loop < $info["count"]; $loop++) {
			
				$groups[$loop]["cn"] = $info[$loop]["cn"][0];
				$groups[$loop]["description"] = utf8_decode($info[$loop]["description"][0]);
			
		  }
		}
        @ldap_free_result ( $result );
      }
    }
    @ldap_close($ds);
  }
  if (count($groups)) usort($groups, "cmp_cn");
  //die(print_r($groups));
  return($groups);
}



function giveLcsName($id) {
global $HOSTAUTH, $USERAUTH, $PASSAUTH;
$authlink=mysql_connect("$HOSTAUTH", "$USERAUTH", "$PASSAUTH");
$sql = "SELECT * from lcs_db.applis where id='$id';";
$c = mysql_query($sql) or die("ERR $sql");
if (mysql_num_rows($c) > 0) 
	return mysql_result($c,0,'descr');
else
	return false;

}

function giveRessName($id) {
$sql = "SELECT * from monlcs_db.ml_ressources where id='$id';";
$c = mysql_query($sql) or die("ERR $sql");
if (mysql_num_rows($c) > 0) 
	return mysql_result($c,0,'titre');
else
	return false;

}


function nbRess_scenario($idR) {
	$sql = "SELECT * from monlcs_db.ml_scenarios where id_ressource = '$idR' and type = 'ressource';";
	$c = mysql_query($sql) or die ('Erreur $sql');
	$scen = array();
	for($x=0;$x<mysql_num_rows($c);$x++) {
	$titre=mysql_result($c,$x,'titre');
		if (!in_array($titre,$scen))
			$scen[] = $titre;
			
	}
	return(count($scen));
}

function nbRess_imposees($idR) {
	$sql = "SELECT * from monlcs_db.ml_ressourcesAffect where id_ressource = '$idR';";
	$c = mysql_query($sql) or die ('Erreur $sql');
	return mysql_num_rows($c);
}

function nbRess_proposees($idR) {
	$sql = "SELECT * from monlcs_db.ml_ressourcesProposees where id_ressource = '$idR';";
	$c = mysql_query($sql) or die ('Erreur $sql');
	$scen = array();
	for($x=0;$x<mysql_num_rows($c);$x++) {
	$tab=mysql_result($c,$x,'id_menu');
		if (!in_array($tab,$scen))
			$scen[] = $tab;
			
	}
	return(count($scen));

}

function nbRess_utilisateurs($idR) {
	$sql = "SELECT * from monlcs_db.ml_geometry where id_ressource = '$idR';";
	$c = mysql_query($sql) or die ('Erreur $sql');
	return mysql_num_rows($c);
}

function Ressource_Libre($idR)
{
    	$scan = "";

	$sql = "SELECT * from monlcs_db.ml_scenarios where id_ressource = '$idR' and type = 'ressource';";
	$c = mysql_query($sql) or die ('Erreur $sql');
	if (mysql_num_rows($c) > 0)
		$scan = $scan."Scen(".mysql_num_rows($c).")";

	$sql = "SELECT * from monlcs_db.ml_ressourcesAffect where id_ressource = '$idR';";
	$c = mysql_query($sql) or die ('Erreur $sql');
	if (mysql_num_rows($c) > 0)
		$scan = $scan."Imp(".mysql_num_rows($c).")";

	$sql = "SELECT * from monlcs_db.ml_ressourcesProposees where id_ressource = '$idR';";
	$c = mysql_query($sql) or die ('Erreur $sql');
	if (mysql_num_rows($c) > 0)
		$scan = $scan."Prop(".mysql_num_rows($c).")";

	$sql = "SELECT * from monlcs_db.ml_geometry where id_ressource = '$idR';";
	$c = mysql_query($sql) or die ('Erreur $sql');
	if (mysql_num_rows($c) > 0)
		$scan = $scan."Util(".mysql_num_rows($c).")";
	
	return $scan;

}


function Tronquer_Texte($texte, $longeur_max)
{
    if (strlen($texte) > $longeur_max)
    {
    $texte = substr($texte, 0, $longeur_max);
    $dernier_espace = strrpos($texte, "");
    $texte = substr($texte, 0, $dernier_espace)."...";
    }

    return $texte;
}

function stringForJavascript($in_string) {
   	$str = ereg_replace("[\r\n]", " \\n\\\n", $in_string);
   	$str = ereg_replace('"', '\\"', $str);
	$str = ereg_replace('&amp;', '&', $str);
	$str = ereg_replace('&agrave;', 'à', $str);
	$str = ereg_replace('&acirc;', 'â', $str);
	$str = ereg_replace('&auml;', 'ä', $str);
	$str = ereg_replace('&eacute;', 'é', $str);
	$str = ereg_replace('&egrave;', 'è', $str);
	$str = ereg_replace('&ecirc;', 'ê', $str);
	$str = ereg_replace('&euml;', 'ë', $str);
	$str = ereg_replace('&icirc;', 'î', $str);
	$str = ereg_replace('&iuml;', 'ï', $str);
	$str = ereg_replace('&ocirc;', 'ô', $str);
	$str = ereg_replace('&ouml;', 'ö', $str);
	$str = ereg_replace('&ugrave;', 'ù', $str);
	$str = ereg_replace('&ucirc;', 'û', $str);
	$str = ereg_replace('&uuml;', 'ü', $str); 
	$str = ereg_replace('&ccedil;', 'ç', $str);   
		
   Return($str);
}


function cleanaccent($in_string) {
   	
	$str = ereg_replace('à','&agrave;', $in_string);
	$str = ereg_replace('â','&acirc;', $str);
	$str = ereg_replace('ä','&auml;', $str);
	$str = ereg_replace('é','&eacute;', $str);
	$str = ereg_replace('è','&egrave;', $str);
	$str = ereg_replace('ê','&ecirc;', $str);
	$str = ereg_replace('ë','&euml;', $str);
	$str = ereg_replace('î','&icirc;', $str);
	$str = ereg_replace('ï','&iuml;', $str);
	$str = ereg_replace('ô','&ocirc;', $str);
	$str = ereg_replace('ö','&ouml;', $str);
	$str = ereg_replace('ù','&ugrave;', $str);
	$str = ereg_replace('û','&ucirc;', $str);
	$str = ereg_replace('ü','&uuml;', $str);   
	$str = ereg_replace('ç','&ccedil;', $str); 
	
   Return($str);
}

function noaccent($in_string) {
   	
	$str = ereg_replace('&agrave;', 'a', $in_string);
	$str = ereg_replace('&acirc;', 'a', $str);
	$str = ereg_replace('&auml;', 'a', $str);
	$str = ereg_replace('&eacute;', 'e', $str);
	$str = ereg_replace('&egrave;', 'e', $str);
	$str = ereg_replace('&ecirc;', 'e', $str);
	$str = ereg_replace('&euml;', 'e', $str);
	$str = ereg_replace('&icirc;', 'i', $str);
	$str = ereg_replace('&iuml;', 'i', $str);
	$str = ereg_replace('&ocirc;', 'o', $str);
	$str = ereg_replace('&ouml;', 'o', $str);
	$str = ereg_replace('&ugrave;', 'u', $str);
	$str = ereg_replace('&ucirc;', 'u', $str);
	$str = ereg_replace('&uuml;', 'u', $str);   
	$str = ereg_replace('&ccedil;', 'c', $str); 
	
   Return($str);
}

function redirect($url)
{
@MYSQL_CLOSE();
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>..::LCS::..</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="refresh" content="0; url='.$url.'">
</head>
<body>
</body>
</html>';
exit();
}


function patchUrl($url,$base) {
	
	$base = substr($base,0,-1);
	$array_test = explode('/',$url);
	
	if (eregi('http',$array_test[0])) {
		return(implode('/',$array_test));
		}

	if ($array_test[0] == '..' || $array_test[0] == null ) {
		$array_test[0] = $base;
		return(implode('/',$array_test));
		}

	

	
}

function patchToXml2($string) {
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function patchToXml($in_string) {
                $str = $in_string;
		$str = ereg_replace('&amp;', '&', $str);
               
                $str = ereg_replace('&deg;', '&#176;', $str);
                $str = ereg_replace('&nbsp;', '&#160;', $str);
                $str = ereg_replace('&ccedil;', '&#231;', $str);

                $str = ereg_replace('&agrave;',  '&#224;', $str);
                $str = ereg_replace('&acirc;',  '&#226;', $str);
                $str = ereg_replace('&auml;',  '&#228;', $str);

                $str = ereg_replace('&egrave;', '&#232;', $str);
                $str = ereg_replace('&eacute;', '&#233;', $str);
                $str = ereg_replace('&ecirc;', '&#234;', $str);
                $str = ereg_replace('&euml;', '&#235;', $str);

                $str = ereg_replace('&icirc;', '&#238;', $str);
                $str = ereg_replace('&iuml;', '&#239;', $str);

                $str = ereg_replace('&ocirc;', '&#244;', $str);
                $str = ereg_replace('&ouml;', '&#246;', $str);

                $str = ereg_replace('&ugrave;', '&#249;', $str);
                $str = ereg_replace('&ucirc;', '&#251;', $str);
                $str = ereg_replace('&uuml;', '&#252;', $str);

                $str = ereg_replace('<', '&lt;', $str);
                $str = ereg_replace('>', '&gt;', $str);
                $str = ereg_replace("'", "&#92;&#39;", $str);
                $str = ereg_replace('"', "&#34;", $str);
                $str = ereg_replace('=', '&#61;', $str);
		$str = ereg_replace('&', '&amp;', $str);

                Return($str);
        }



?>
