<?
/* =============================================
   Projet LCS : Linux Communication Server
   functions2.inc.php
   Daprès le projet Intranet CRDP
   « wawa » olivier.lecluse@ac-caen.fr
   [LCS CoreTeam]
   jLCF >:>  jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.4 maj : 11/05/2004
   modif par ph lelclerc pour la VERSION 2.0 du 10/10/20010
   ============================================= */

// Clé privée pour cryptage du cookie LCSuser dans fonction open_session()
   

    function is_prof ($login) {
        global $ldap_server, $ldap_port, $dn;
        global $error;
        $error="";

        $filter = "(&(cn=profs*)(memberUid=$login))";
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
                                        $is_prof = true;
                                } else {
                                        $is_prof = false;
                                }
                        }
                }
        }
        @ldap_unbind ($ds);
        @ldap_close ($ds);
        return $is_prof;
    }


	function is_administratif ($login) {
        global $ldap_server, $ldap_port, $dn;
        global $error;
        $error="";

        $filter = "(&(cn=administratifs*)(memberUid=$login))";
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

// Cette fonction crée un menu calendier : mois, jours et années, initialisé à la date du timestamp précisé  + offset 
function calendrier_auto($offset,$var_j,$var_m,$var_a,$tsmp)
//offset=nbre de jours / au timestmp ,var_j,var_m,var_a=nom des variables associées pour la bd ,$tsmp=timestamp
{ 
// Tableau indexé des jours
$jours = array (1 => '01', '02', '03', '04', '05','06', '07', '08', '09', '10', '11','12','13','14','15',
						'16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');

// Tableau indexé des mois
  $mois = array (1 => '01', '02', '03', '04', '05', 
          '06', '07', '08', '09', '10', '11','12');
$dateinfo=getdate($tsmp);
$jo=date('d',$dateinfo['0']+($offset*86400));
$mo=date('m',$dateinfo['0']+($offset*86400));
$ann=date('Y',$dateinfo['0']+($offset*86400)); 
// Création des menus déroulants
 //les jours
  echo "<select name=$var_j>\n";
  foreach ($jours as $clé => $valeur)
  { echo "<option valeur=\"$clé\"";
  if ($clé==$jo) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
  echo "</select>\n";
//les mois
  echo "<select name=$var_m>";
  foreach ($mois as $clé => $valeur)
  { echo "<option valeur=\"$clé\"";
  if ($clé==$mo) {echo 'selected';}
  echo ">$valeur</option>\n";
  }
  echo "</select>\n";
//les années
  echo "<select name=$var_a>";
  $année = (date('Y') - 5);
  while ($année <= (date('Y')))
  { echo "<option valeur=\"$année\"";
  if ($année==$ann) {echo 'selected';}
  echo ">$année</option>\n";
    $année++;
  }
  echo "</select>\n";
}
    
function decripte_classe($classe_criptee){

include "config.inc.php";
include "data.inc.php";

$groups=search_groups('cn=classe*');

		  if (count($groups))
		  	{    
			for ($loup=0; $loup < count($groups); $loup++)
			        {
			        
						if ($classe_criptee==substr(crypt(substr($groups[$loup]["cn"],-8,8),$Grain),2))
							{
							$classe_en_clair=$groups[$loup]["cn"];
							break;
							}
						}
			}

 for($n=0; $n<count($classe); $n++)
						{
						if (ereg("($classe[$n])$",$classe_en_clair))
							{
							$classe_decriptee=$classe[$n];
							break;
							}
						}
return $classe_decriptee;				
}

function decripte_uid($uid_cripte,$saclasse) {
	$uid_decrypte=array();
	$groups=search_groups('cn=classe*');

		  if (count($groups))
		  	{    
			for ($loup=0; $loup < count($groups); $loup++)
			        {
			        
						if (ereg("($saclasse)$",$groups[$loup]["cn"]))
							{
							$full_classe =$groups[$loup]["cn"];
							break;
							}
						}
			}	
	
	$membres = search_uids ("(cn=".$full_classe.")", "half");
	
	for ($iteration = 0; $iteration <= count($membres); $iteration++) 
   	{
   	$uidgugus=$membres[$iteration]["uid"]; 
   	if ( (substr(md5($uidgugus),2,5).substr(md5($uidgugus),-5,5))==  $uid_cripte) 
 			{
 			$uid_decrypte[0]=$membres[$iteration]["uid"];
 			list($user, $groups)=people_get_variables($uidgugus, true);
 			$uid_decrypte[1]=getprenom($user['fullname'],$user['nom']);
 			$uid_decrypte[2]=$saclasse;
 			break;
 			}
 			 												
   	}
	return $uid_decrypte;
}

function people_get_classe ($uid)
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
        // Recherche du posixGroup Classe d'appartenance dans la branche Groups
        $filter = "(|(&(objectclass=posixGroup)(memberuid=$uid)(cn=Classe*))
                    )";
        $result = @ldap_list ( $ds, $dn["groups"], $filter, $ldap_group_attr );
        if ($result) {
          $info = @ldap_get_entries ( $ds, $result );
          if ( $info["count"]) {
            $ret_group = $info[0]["cn"][0];
          } else $ret_group="Nogroup";
          @ldap_free_result ( $result );
        }
       // Fin recherche du groupe Classe
    } else {
      $error = "Echec du bind anonyme";
    }
    @ldap_close ( $ds );
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }
  return $ret_group;
}

function people_get_cours ($uid)
{
  
  global $ldap_server, $ldap_port, $dn;
  global $error;
  $error="";

  // LDAP attribute
  $ldap_people_attr = array(
    "uid",				// login
      );


  $ldap_group_attr = array (
    "cn",
      );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
              // Recherche des groupes d'appartenance dans la branche Groups
        //$filter = "(|(&(objectclass=groupOfNames)(member= uid=$uid,".$dn["people"]."))(&(objectclass=groupOfNames)(owner= uid=$uid,".$dn["people"]."))(&(objectclass=posixGroup)(memberuid=$uid)))";
        $filter = "(&(objectclass=posixGroup)(memberuid=$uid)(cn=Cours*))";
        $result = @ldap_list ( $ds, $dn["groups"], $filter, $ldap_group_attr );
        if ($result) {
          $info = @ldap_get_entries ( $ds, $result );
          if ( $info["count"]) {
            for ($loop=0; $loop<$info["count"];$loop++) {
              //if ($info[$loop]["member"][0] == "") $typegr="posixGroup"; else $typegr="groupOfNames";
              $typegr="posixGroup";
              $ret_group[$loop] = array (
                "cn"           => $info[$loop]["cn"][0]              );
            }
            
            //usort($ret_group, "cmp_cn");
          }
          @ldap_free_result ( $result );
        }
       // Fin recherche des groupes
    } else {
      $error = "Echec du bind anonyme";
    }
    @ldap_close ( $ds );
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }

  return  $ret_group;

}
function people_get_datenaissance ($uid)
{
  global $ldap_server, $ldap_port, $dn;
  global $error;
  $error="";

  // LDAP attribute
  $ldap_people_attr = array(
    "uid",				// login
    "gecos"				// Date de naissance,Sexe (F/M),
  );
  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      $result = @ldap_read ( $ds, "uid=".$uid.",".$dn["people"], "(objectclass=posixAccount)", $ldap_people_attr );
      if ($result) {
        $info = @ldap_get_entries ( $ds, $result );
        if ( $info["count"]) {
          // Traitement du champ gecos pour extraction de date de naissance
          $gecos = $info[0]["gecos"][0];
          $tmp = split ("[\,\]",$info[0]["gecos"][0],4);
          $ddn = $tmp[1];
             }
        @ldap_free_result ( $result );
      }
       
    @ldap_close ( $ds );
  } else {
    $error = "Erreur de connection au serveur LDAP";
  }
}
  return $ddn;
}
/*********************/

?>
