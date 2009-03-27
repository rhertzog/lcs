<?
/* =============================================
   Projet LCS : Linux Communication Server
   functions2.inc.php
   Daprès le projet Intranet CRDP
   « wawa » olivier.lecluse@ac-caen.fr
   [LCS CoreTeam]
   jLCF >:>  jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.4 maj : 11/05/2004
   modif par ph lelclerc pour la VERSION 0.4 du 09 mars 2007
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
  $année = 2006;
  while ($année <= 2015)
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
			        
						if ($classe_criptee==crypt(substr($groups[$loup]["cn"],-8,8),$Grain) )
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
 			break;
 			}
 												
   	}
	return $uid_decrypte;
}

?>
