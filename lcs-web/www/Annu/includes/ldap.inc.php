<?php
/* Annu/includes/ldap.inc.php Derniere version : 23/05/2014 */

// Fonctions de comparaison utilisees dans la fonction usort

function cmp_fullname ($a, $b) {
    return strcmp($a["fullname"], $b["fullname"]);
}

function cmp_name ($a, $b) {
    return strcmp($a["name"], $b["name"]);
}

function cmp_group ($a, $b) {
    return strcmp($a["group"], $b["group"]);
}

function cmp_cat ($a, $b) {
    return strcmp($a["cat"], $b["cat"]);
}

function cmp_cn ($a, $b) {
    return strcmp($a["cn"], $b["cn"]);
}

// Retourne un login a partir d'un dn
function extract_login ($dn) {
  $login = preg_split ("/,/",$dn,4);
  $login = preg_split ("/=/",$login[0],2);
  return $login[1];
}

function getprenom($fullname,$name) {
    $expl=explode(" ","$fullname");
    $namexpl=explode(" ",$name);
    $j=0;
    $prenom="";
    for ($i=0; $i<count($expl); $i++) {
        if (mb_strtolower($expl[$i])!=mb_strtolower($namexpl[$j]))  {
             if ("$prenom" == "") $prenom=$expl[$i];
	     else $prenom.=" ".$expl[$i];
        } else $j++;
    }
    return $prenom;
}

// ---------------------------------------------------------
// Debug
function duree ($t0,$t1) {
  $result0 = preg_split ("[\ \!\?]", $t0, 2);
  $t0ms = $result0[0];
  $t0s  = $result0[1];
  $result1 = preg_split ("[\ \!\?]", $t1, 2);
  $t1ms = $result1[0];
  $t1s  = $result1[1];
  $tini= ( $t0s +  $t0ms );
  $tfin= ( $t1s +  $t1ms );
  $temps = ( $tfin - $tini );
  return ($temps);
}

function people_get_variables ($uid, $mode)
{
  global $ldap_server, $ldap_port, $dn;
  global $error;
  $error="";
  $ret_people= $ret_group=array();
  // LDAP attribute
  $ldap_people_attr = array(
    "uid",				// login
    "cn",				// Prenom  Nom
    "sn",				// Nom
    "givenname",			// Prenom
    "initials",                         // Pseudo
    "mail",				// Mail
    "telephonenumber",
    "homedirectory",                    // Home directory personnal web space
    "description",
    "loginshell",
    "gecos"				// Date de naissance,Sexe (F/M),
  );


  $ldap_group_attr = array (
    "cn",
    "memberuid",
    "description",  // Description du groupe
  );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      $result = @ldap_read ( $ds, "uid=".$uid.",".$dn["people"], "(objectclass=posixAccount)", $ldap_people_attr );
      if ($result) {
        $info = @ldap_get_entries ( $ds, $result );
        if ( $info["count"]) {
          // Traitement du champ gecos pour extraction de date de naissance, sexe
          //init variables si champ ldap non renseigne
          $gecos = (isset($info[0]["gecos"][0])) ?$info[0]["gecos"][0] :" ,000000, ,N";
          $telelph=(isset($info[0]["gelephonenumber"][0])) ?$info[0]["gelephonenumber"][0] : "";
          $descript=(isset($info[0]["description"][0]))?$info[0]["description"][0] : " ";
          $prenom=(isset($info[0]["givenname"][0]))?$info[0]["givenname"][0] : " ";
          $pseud=(isset($info[0]["initials"][0]))?$info[0]["initials"][0] : "";
          $tmp = preg_split ("/,/",$gecos,4);
          $ret_people = array (
              "uid" => $info[0]["uid"][0],
              "nom" => stripslashes( $info[0]["sn"][0] ),
              "fullname" => stripslashes( $info[0]["cn"][0] ),
              "prenom" => $prenom,
              "pseudo" => $pseud,
              "gecos"=> $gecos,
              "email" => $info[0]["mail"][0],
              "tel"=> $telelph,
              "homedirectory" => $info[0]["homedirectory"][0],
              "description"	=> $descript,
              "shell" => $info[0]["loginshell"][0],
              "sexe" => $tmp[2]
            );
        }
        @ldap_free_result ( $result );
      }
       if ($mode) {
        // Recherche des groupes d'appartenance dans la branche Groups
        //$filter = "(|(&(objectclass=groupOfNames)(member= uid=$uid,".$dn["people"]."))(&(objectclass=groupOfNames)(owner= uid=$uid,".$dn["people"]."))(&(objectclass=posixGroup)(memberuid=$uid)))";
        $filter = "(&(objectclass=posixGroup)(memberuid=$uid))";
        $result = @ldap_list ( $ds, $dn["groups"], $filter, $ldap_group_attr );
        if ($result) {
          $info = @ldap_get_entries ( $ds, $result );
          if ( $info["count"]) {
            for ($loop=0; $loop<$info["count"];$loop++) {
              //if ($info[$loop]["member"][0] == "") $typegr="posixGroup"; else $typegr="groupOfNames";
              $typegr="posixGroup";
              $descript=(isset($info[0]["description"][0]))?$info[0]["description"][0] : " ";
              $ret_group[$loop] = array (
                "cn" => $info[$loop]["cn"][0],
                "description"	=> $descript,
                "type" => $typegr
              );
            }
            usort($ret_group, "cmp_cn");
          }
          @ldap_free_result ( $result );
        }
      } // Fin recherche des groupes
    } else {
      $error = "Echec du bind anonyme";
    }
    @ldap_close ( $ds );
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }

  return array($ret_people, $ret_group);
}
// Recherche du groupe principal (posixGroup) d'appartenance
function people_get_group ($uid)
{
  global $ldap_server, $ldap_port, $dn;
  global $error;
  $error="";

  // LDAP attribute
  $ldap_group_attr = array (
    "cn",
  );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
        // Recherche du posixGroup principal d'appartenance dans la branche Groups
        $filter = "(|(&(objectclass=posixGroup)(memberuid=$uid)(cn=Eleves))
                     (&(objectclass=posixGroup)(memberuid=$uid)(cn=Profs))
                     (&(objectclass=posixGroup)(memberuid=$uid)(cn=Administratifs))
                    )";
        $result = @ldap_list ( $ds, $dn["groups"], $filter, $ldap_group_attr );
        if ($result) {
          $info = @ldap_get_entries ( $ds, $result );
          if ( $info["count"]) {
            $ret_group = $info[0]["cn"][0];
          } else $ret_group="Nogroup";
          @ldap_free_result ( $result );
        }
       // Fin recherche du groupe principal
    } else {
      $error = "Echec du bind anonyme";
    }
    @ldap_close ( $ds );
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }
  return $ret_group;
}

// Recherche d'utilisateurs dans la branche people
function search_people ($filter) {
  global $ldap_server, $ldap_port, $dn;
  global $error;
  $error="";
  $ret=array();
  //LDAP attributes
  $ldap_search_people_attr = array(
    "uid",   // login
    "cn",    // Prenom  Nom
    "sn"     // Nom
  );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      // Recherche dans la branche people
      $result = @ldap_search ( $ds, $dn["people"], $filter, $ldap_search_people_attr );
      if ($result) {
        $info = @ldap_get_entries ( $ds, $result );
        if ( $info["count"]) {
          for ($loop=0; $loop<$info["count"];$loop++) {
            $ret[$loop] = array (
            "uid" => $info[$loop]["uid"][0],
            "fullname"  => $info[$loop]["cn"][0],
            "name" => $info[$loop]["sn"][0]
            );
          }
        }
        @ldap_free_result ( $result );
      } else {
        $error = "Erreur de lecture dans l'annuaire LDAP";
      }

    } else {
      $error = "Echec du bind anonyme";
    }
    @ldap_close ( $ds );
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }
  // Tri du tableau par ordre alphabetique
  if (count($ret)) usort($ret, "cmp_name");
  return $ret;
}

function search_uids ($filter, $mode) {
  global $ldap_server, $ldap_port, $dn, $ldap_grp_attr;
  global $error;
  $error="";
  $ret=array();
  // LDAP attributs
  $ldap_grp_attr = array (
    "cn",
    "memberuid" // Membres du groupe Classe
  );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      // Debug
      //echo "filtre 1 memberuid : $filter<BR>";

      // Recherche dans la branche Groups
      $result=@ldap_list ($ds, $dn["groups"], $filter, $ldap_grp_attr);
      if ($result) {
        $info = @ldap_get_entries( $ds, $result );
        if ($info["count"]) {
          // Stockage des logins des membres des classes
          //  dans le tableau $ret
          $init=0;
          for ($loop=0; $loop < $info["count"]; $loop++) {
            $group=preg_split ("/_/",$info[$loop]["cn"][0],2);
            if (isset($info[$loop]["memberuid"])) {
                for ( $i = 0; $i < $info[$loop]["memberuid"]["count"]; $i++ ) {
              // Ajout de wawa : test si le gus est prof
              $filtre1 = "(memberUid=".$info[$loop]["memberuid"][$i].")";
              $result1=@ldap_read($ds,"cn=Profs,".$dn["groups"],$filtre1);
              $ret[$init]["prof"]=@ldap_count_entries($ds,$result1);
              @ldap_free_result ( $result1 );
              // fin patch a wawa
              $ret[$init]["uid"] = $info[$loop]["memberuid"][$i];
              if (isset($group[1]))$ret[$init]["group"] = $group[1];
               if (isset($group[0]))$ret[$init]["cat"] = $group[0];
              $init++;
            }
            }
          }
        }
        ldap_free_result ( $result );
      }
    } else $error = "Echec du bind anonyme";
    @ldap_close ( $ds );
  } else $error = "Erreur de connection au serveur LDAP";
  return $ret;
}

// Recherche une liste de groupes repondants aux criteres fixes par
// la variable $filter
// retourne un tableau $groups avec le cn et la description de chaque groupe

function search_groups ($filter) {
  global $ldap_server, $ldap_port, $dn;
  global $error;

  // LDAP attributs
  $ldap_group_attr = array (
    "objectclass",
    "cn",
    //"member",
    "description"  // Description du groupe
  );
  $groups=array();
  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      $result = @ldap_list ( $ds, $dn["groups"], $filter, $ldap_group_attr );
      if ($result) {
        $info = @ldap_get_entries ( $ds, $result );
        if ( $info["count"]) {
          for ($loop=0; $loop < $info["count"]; $loop++) {
            $groups[$loop]["cn"] = $info[$loop]["cn"][0];
            $groups[$loop]["description"] = (isset($info[$loop]["description"][0])) ?$info[$loop]["description"][0] :"";
            /* Recherche de posixGroup ou groupOfNames
            for ($i=0; $i < $info[$loop]["objectclass"]["count"]; $i++) {
              if  ($info[$loop]["objectclass"][$i] != "top") $type =  $info[$loop]["objectclass"][$i];
            }
            $groups[$loop]["type"] =  $type;
            */
          }
        }
        @ldap_free_result ( $result );
      }
    }
    @ldap_close($ds);
  }
  if (count($groups)) usort($groups, "cmp_cn");
  return $groups;
}

// Recherche des utilisateurs dans la branche people a partir
// d'un tableau d'uids nons tries
// $order = "cat"   => Tri par categorie (Eleves, Equipe...)
//          "group" => Tri par intitule de group (ex: 1GEA, TGEA...)

function search_people_groups ($uids,$filter,$order) {
  global $ldap_server, $ldap_port, $dn;
  global $error;
  $error="";
  $ret=array();
  // LDAP attributs
  $ldap_user_attr = array(
    "cn",                 // Prenom  Nom
    "sn",                 // Nom
    "gecos"               // Date de naissance,Sexe (F/M),Status administrateur LCS (Y/N)
  );

  if (!$filter) $filter="(sn=*)";
  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      $init=0;
      for ($loop=0; $loop < count($uids); $loop++) {
        $result = @ldap_read ( $ds, "uid=".$uids[$loop]["uid"].",".$dn["people"], $filter, $ldap_user_attr );
        if ($result) {
          $info = @ldap_get_entries ( $ds, $result );
          if ( $info["count"]) {
            // echo "debug".$info["count"]." ".$init."<BR>";
            // traitement du gecos pour identification du sexe
            $gecos = (isset($info[0]["gecos"][0])) ?$info[0]["gecos"][0] :" ,000000, ,N";
            $owner=(isset($uids[$loop]["owner"])) ?$uids[$loop]["owner"] :"";
            $grp=(isset($uids[$loop]["group"])) ? $uids[$loop]["group"] :" ";
            $catg=(isset($uids[$loop]["cat"])) ? $uids[$loop]["cat"] :"";
            $tmp = preg_split ("/,/",$gecos,4);
            $ret[$init] = array (
                "uid" => $uids[$loop]["uid"],
                "fullname"  => $info[0]["cn"][0],
                "name" => $info[0]["sn"][0],
                "sexe" => $tmp[2],
                "owner" => $owner,
                "group" => $grp,
                "cat" =>$catg ,
                "prof" => $uids[$loop]["prof"],
                "gecos"  => $gecos
            );
            $init++;
          }
          @ldap_free_result ( $result );
        }
      }
    } else {
      $error = "Echec du bind anonyme";
    }
    @ldap_close ( $ds );
  } else $error = "Erreur de connection au serveur LDAP";

  if (count($ret)) {
    # Correction tri du tableau
    # Tri par critere categorie ou intitule de groupe
    if ( $order == "cat" ) usort ($ret, "cmp_cat");
      elseif ( $order == "group" ) usort ($ret, "cmp_group");
    # Recherche du nombre de categories ou d'intitules de groupe
    $i = 1;
    $tab_order[0] = $ret[0][$order];
    for ( $loop=1; $loop < count($ret); $loop++) {
	 	if ( $ret[$loop][$order] != $ret[$loop-1][$order]) {
	    	$tab_order[$i] = $ret[$loop][$order];
	    	$i++;
	 	}
    }
    if (count($tab_order) > 0 ) {
    	# On decoupe le tableau $ret en autant de sous tableaux $tmp que de criteres $order
    	for ($i=0; $i < count($tab_order); $i++) {
			$j=0;
			for ( $loop=0; $loop < count($ret); $loop++) {
	   		if ( $ret[$loop][$order] == $tab_order[$i] ) {
					$ret_tmp[$i][$j] = $ret[$loop];
					$j++;
	    		}
			}
    	}
    	# Tri alpabetique des sous tableaux
    	for ( $loop=0; $loop < count($ret_tmp); $loop++) usort ($ret_tmp[$loop], "cmp_name");
    	# Reassemblage des tableaux temporaires
        $ret_final=array();
    	for ($loop=0; $loop < count($tab_order); $loop++)  $ret_final = array_merge ($ret_final, $ret_tmp[$loop]);
    	return $ret_final;
    } else {
    	  usort ($ret, "cmp_name");
		  return $ret;
    }
  }

}

// Recherche de machines
  function search_computers ($filter) {
    return search_machines($filter,"computers");
}

function search_machines ($filter,$branch) {
  global $ldap_server, $ldap_port, $dn;
  global $error;
$computers=array();
  // LDAP attributs
  if ("$branch"=="computers")
    $ldap_computer_attr = array (
    "cn",
    "ipHostNumber",   // ip Host
    "l",                        // Status de la machine
    "description"        // Description de la machine
    );
  else
    $ldap_computer_attr = array (
    "cn"
    );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      $result = @ldap_list ( $ds, $dn[$branch], $filter, $ldap_computer_attr );
      if ($result) {
        $info = @ldap_get_entries ( $ds, $result );
        if ( $info["count"]) {
          for ($loop=0; $loop < $info["count"]; $loop++) {
            $computers[$loop]["cn"] = $info[$loop]["cn"][0];
            if ("$branch"=="computers") {
                $computers[$loop]["ipHostNumber"] = $info[$loop]["iphostnumber"][0];
                $computers[$loop]["l"] = $info[$loop]["l"][0];
                $computers[$loop]["description"] = $info[$loop]["description"][0];
            }
          }
        }
        @ldap_free_result ( $result );
      }
    }
    @ldap_close($ds);
  }
  return $computers;
}

// Liste les membres du groupOfNames $gof
function gof_members ($gof,$branch,$extract) {
  global $ldap_server, $ldap_port, $dn;
  global $error;
  $error="";

  // LDAP attributs
  $members_attr = array (
      "member"   // Membres du groupe Profs
  );
  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
        $result=ldap_read ($ds, "cn=$gof,".$dn[$branch], "cn=*", $members_attr);
        if ($result) {
          $info = ldap_get_entries( $ds, $result );
          if ($info["count"]==1) {
            $init=0;
             for ($loop=0; $loop < $info[0]["member"]["count"]; $loop++) {
                if ($extract==1)
                    $ret[$loop]=extract_login($info[0]["member"][$loop]);
                else $ret[$loop]=$info[0]["member"][$loop];
                }
            }

          @ldap_free_result ( $result );
        }

    } else {
      $error = "Echec du bind anonyme";
    }
    @ldap_close ( $ds );
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }
  return $ret;
}

// WaWa ajout : teste si $eleve est dans la classe de $prof

function tstclass($prof,$eleve)
{
  $filtre= "(&(memberUid=$prof)(memberUid=$eleve))";
  $grcomm=search_groups($filtre);
  $tstclass=0;
  if (count($grcomm)>0) {
        $i=0;
        while (($i< count($grcomm)) and ($tstclass==0)) {
                if (mb_ereg("Cours",$grcomm[$i]["cn"],$matche))
                        $tstclass=1;
                $i++;
        }
  }
  return $tstclass;
}

// Fonctions de modifications des entrees LDAP
// -------------------------------------------

function user_enable_ad_auth($uid, $ds = NULL) {
    global $ldap_server, $ldap_port, $dn, $adminDn, $adminPw;
    if (! isset($ds)) {
	$ds = ldap_connect($ldap_server, $ldap_port);
	if (! $ds) {
	    return 0;
	}
	$r = ldap_bind($ds, $adminDn, $adminPw); // Bind en admin
	if (! $r) {
	    return 0;
	}
    }
    $changes["userPassword"] = "{sasl}$uid";
    return ldap_modify($ds, "uid=$uid," . $dn["people"], $changes);
}

function user_disable_ad_auth($uid, $ds = NULL) {
    global $ldap_server, $ldap_port, $dn, $adminDn, $adminPw;
    if (! isset($ds)) {
	$ds = ldap_connect($ldap_server, $ldap_port);
	if (! $ds) {
	    return 0;
	}
	$r = ldap_bind($ds, $adminDn, $adminPw); // Bind en admin
	if (! $r) {
	    return 0;
	}
    }
    $sr = ldap_read($ds, "uid=$uid," . $dn["people"], "(objectclass=*)",
		    array("userPassword", "gecos"));
    if (! $sr) {
	return 0;
    }
    $entry = ldap_first_entry($ds, $sr);
    $values = ldap_get_values($ds, $entry, "userPassword");
    $current_pass = $values[0];
    $values = ldap_get_values($ds, $entry, "gecos");
    $gecos = $values[0];
    $values = explode(",", $gecos);
    $birthdate = $values[1];
    if (strpos($current_pass, "{sasl}") === 0) {
	$changes["userPassword"] = "{crypt}" . crypt($birthdate);
	return ldap_modify($ds, "uid=$uid," . $dn["people"], $changes);
    } else {
	return 1; /* Auth through AD is already not used */
    }
}

function user_has_ad_auth($uid, $ds = NULL) {
    global $ldap_server, $ldap_port, $dn, $adminDn, $adminPw;
    if (! isset($ds)) {
	$ds = ldap_connect($ldap_server, $ldap_port);
	if (! $ds) {
	    return 0;
	}
	$r = ldap_bind($ds, $adminDn, $adminPw); // Bind en admin
	if (! $r) {
	    return 0;
	}
    }
    $sr = ldap_read($ds, "uid=$uid," . $dn["people"], "(objectclass=*)",
		    array("userPassword"));
    if (! $sr) {
	return 0;
    }
    $entry = ldap_first_entry($ds, $sr);
    $values = ldap_get_values($ds, $entry, "userPassword");
    $current_pass = $values[0];
    if (strpos($current_pass, "{sasl}") === 0) {
	return true;
    } else {
	return false;
    }
}

// Changement mot de passe
function userChangedPwd($uid, $userpwd, $old) {
  global $scriptsbinpath, $key_priv;
  exec ( escapeshellarg("$scriptsbinpath/userChangePwd.pl")." ". escapeshellarg($uid) ." ". escapeshellarg($userpwd) ." ". escapeshellarg($old) ,$AllOutPut,$ReturnValue);

  if ($ReturnValue == "0") {
    // Resynchro du mdp admin pour le mode sans echec
    if ( $uid == "admin" && user_valid_passwd ( "admin", $userpwd ) ) {
                # reencode temporarily pass with openssl
                $cryptpass = exec ("echo ". escapeshellarg($userpwd) ." | openssl aes-256-cbc -a -salt -pass pass:$key_priv");
                exec ("/usr/bin/sudo /usr/share/lcs/scripts/action.sh 'synchro_mdp' '$cryptpass'" , $AllOutput, $ReturnValue);
    }
    return true;
  } else return false;
}

// Verification du mot de passe d'un utilisateur
// retourne :
//     true si le mot de passe est valide
//     false dans les autres cas
function user_valid_passwd ( $login, $password ) {
  global $ldap_server, $ldap_port, $dn;
  $filter = "(userpassword=*)";
  $ret = false;
  $DEBUG = false;

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds,"uid=".$login.",".$dn["people"] , $password );
    if ( $r ) {
      $read_result=@ldap_read ($ds, "uid=".$login.",".$dn["people"], $filter);
      if ($read_result) {
        $entrees = @ldap_get_entries($ds,$read_result);
        if ($entrees[0]["userpassword"][0]) {
          $ret= true;
        } else {
          $error = "Mot de passe invalide";
        }
      } else {
        $error = "Login invalide";
      }
    } else {
      $error = "L'Authentification a &#233;chou&#233;e";
    }
    @ldap_unbind ($ds);
    @ldap_close ($ds);
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }
  if ($DEBUG) echo "$error<BR>\n";
  return $ret;
}

// Verification quand pwdPolicy =1 si l'utilisateur doit changer son mot de passe par defaut
// retourne true dans le cas d'un changement de mot de passe necessaire, false dans l'autre cas.
function pwdMustChange ($login) {

    global $ldap_server, $ldap_port, $dn, $pwdPolicy;

    if ( $pwdPolicy == "1" ) {
        // Recuperation de la date de naissance
        $filter="(uid=$login)";
        $ldap_search_people_attr = array("gecos");
        $ds = @ldap_connect ( $ldap_server, $ldap_port );
        if ( $ds ) {
            $r = @ldap_bind ( $ds ); // Bind anonyme
            if ($r) {
                // Recherche dans la branche people
                $result = @ldap_search ( $ds, $dn["people"], $filter, $ldap_search_people_attr );
                if ($result) {
                    $info = @ldap_get_entries ( $ds, $result );
                    if ( $info["count"]) {
                        for ($loop=0; $loop<$info["count"];$loop++) {
                            $gecos = (isset($info[0]["gecos"][0])) ?$info[0]["gecos"][0] :" ,000000, , ";
                            $tmp = explode (",",$gecos);
                            $date_naiss=$tmp[1];
                        }
                    }
                    @ldap_free_result ( $result );
                } else $error = "Erreur de lecture dans l'annuaire LDAP";
            } else $error = "Echec du bind anonyme";
    	   @ldap_close ( $ds );
  	 } else $error = "Erreur de connection au serveur LDAP";
        if (user_valid_passwd($login,$date_naiss)) return true; else return false;
    } else return false;
}

// Recherche si l'utilisateur connecte a le droit de creer un salon sur le Chat
// Retourne true si login appartient au groupe :
// Profs ou Administration ou si login = admin
// Retourne false dans les autres cas

function create_room ($login) {
  global $ldap_server, $ldap_port, $dn;
  global $error;
  $error="";

  $filter = "(&(|(cn=administratifs)(cn=profs))(memberUid=$login))";
  $ldap_groups_attr = array (
    // LDAP attribute
    "cn",
    "memberUid"    // Membre du Group Profs, Eleves, Administratifs
  );

  if ($login == "admin" ) {
    $Create_Room = true;
  } else {
    $ds = @ldap_connect ( $ldap_server, $ldap_port );
    if ( $ds ) {
      $r = ldap_bind ( $ds );
      if (!$r) {
        $error = "Echec du bind anonyme";
      } else {
        // Recherche du groupe d'appartenance de l'utilisateur connecte
        $result=@ldap_list ($ds, $dn["groups"], $filter, $ldap_groups_attr);
        if ($result) {
          $info = @ldap_get_entries( $ds, $result );
          if ($info["count"]) {
            $Create_Room = true;
          } else {
             $Create_Room = false;
          }
        }
      }
    }
    @ldap_unbind ($ds);
    @ldap_close ($ds);
  }
  return $Create_Room;
}

?>
