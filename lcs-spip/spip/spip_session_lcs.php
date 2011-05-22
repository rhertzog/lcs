<?php /* spip/spip_session_lcs.php derniere revision : 07/11/2009 */
$DBG = false;
//register global
$action=$_GET['action'];
function fich_debug($texte){
        global $DBG;
	if($DBG){
		$fich=fopen("/tmp/debug_spiplcs.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}
include "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers,$login)= isauth();

if ( $action=="login" ) {
    if ($idpers != "0") {
        // ou est l'espace prive ?
        @define('_DIR_RESTREINT_ABS', 'ecrire/');
        include_once _DIR_RESTREINT_ABS.'inc_version.php';
        include_once _DIR_RESTREINT_ABS.'inc/vieilles_defs.php';

        include_spip('inc/cookie');
        include_once  _DIR_RESTREINT_ABS.'auth/ldap.php';
        include_once 'config/ldap.php';
        spip_connect();
        ### DBG
        fich_debug("DBG >> login : $login\n");
        // Si l'utilisateur figure deja dans la base, y recuperer les infos
        $result = spip_query("SELECT * FROM spip_auteurs WHERE login=" . sql_quote($login) . " AND source='ldap'");
        if (!sql_count($result)) { 
            ### DBG
            fich_debug("DBG >> Login : $login n'est pas dans la base.\n");
            fich_debug("DBG >> statut : ".$GLOBALS['meta']["ldap_statut_import"]."\n");
            fich_debug("DBG >> ldap_base : ".$GLOBALS['ldap_base']."\n");
            // sinon importer les infos depuis LDAP,
            // avec le statut par defaut a l'install
            // Inserer les infos dans la base spip
            
 ## fix temporaire du bug  vrif ldap avec sip2.1
 #          spip_connect_ldap();
 #          auth_ldap_inserer("uid=$login,ou=People,".$GLOBALS['ldap_base'], $GLOBALS['meta']["ldap_statut_import"], $login);
 #          $result = spip_query("SELECT * FROM spip_auteurs WHERE login=" . sql_quote($login) . " AND source='ldap'");
            $dn="uid=$login,ou=People,".$GLOBALS['ldap_base'];
 #            if ($GLOBALS['meta']["ldap_statut_import"]
 #           AND $desc = auth_ldap_retrouver($dn)) {
            // rajouter le statut indique  a l'install
            
 #              $desc['statut'] = $GLOBALS['meta']["ldap_statut_import"];
                $desc['statut'] = "1comite";
                $desc['login'] = $login;
                $desc['source'] = 'ldap';
                $desc['pass'] = '';
                $r = sql_insertq('spip_auteurs', $desc);
#            }
## fin fix
          $result = spip_query("SELECT * FROM spip_auteurs WHERE login=" . sql_quote($login) . " AND source='ldap'");

        }
        $row_auteur = spip_fetch_array($result);
        ### DBG
        fich_debug("DBG >> statut :".$row_auteur['statut']."\n");
        if ($row_auteur['statut'] == '0minirezo')
	   $cookie_admin = "@".$session_login;

        $var_f = charger_fonction('session', 'inc');
        $cookie_session = $var_f($row_auteur);
        ### DBG
        fich_debug("DBG >> $cookie_session\n");
        // On poste le cookie de session
        spip_setcookie('spip_session', $cookie_session);
        // On loge l'authentification
        spip_log("login de $login depuis LCS");
    
        ////////////// test de recup des zones/groupes //////////////
        // dans un premier temps on ne s'occupe que des groupes principaux
        // pour les groupes secondaire, des verifs supplementaires seront a effectuer
        // #
        // # - on teste l'existence d'une zone d'acces restreint intitulee Profs ou Administratifs
        // # - on recupere les groupe d'appartenance du user
        // # - on verifie l'appartenance du user a la/les zone/s de meme intitule que le/les groupe/s
        // # - si il n'est pas dans la zone on l'ajoute
        
        //Verifier la presence des plugins Tispip et Acces-restreint
        if ($GLOBALS['meta']["plugin"]['ACCESRESTREINT']
        AND $GLOBALS['meta']["plugin"]['TISPIPSKELET'] 
        AND $login!='admin'){
        		
	        	//on recupere le groupe principal du user 
	        	
	        	////// script de MrPhi (extrait)
	        	// Recherche des groupes d'appartenance de l'utilisateur $login
				include ("/var/www/lcs/includes/user_lcs.inc.php");
			  	list($user, $groups)=people_get_variables($login, true);
				$i=0;

				// Recherche du groupe principal 
				for ($loop=0; $loop < count ($groups) ; $loop++) {
					if ( $groups[$loop]["cn"] == "Administratifs" ) $group_principal = "Administratifs";
					elseif ( $groups[$loop]["cn"] == "Profs" ) $group_principal = "Profs";
					elseif ( $groups[$loop]["cn"] == "Eleves" ) $group_principal = "Eleves";

//					elseif ( ereg ("Classe", $groups[$loop]["cn"] ) ) {
						$groups_secondaires[$i] = $groups[$loop]["cn"];
						$i++;
//					}
/*
					elseif ( ereg ("Equipe", $groups[$loop]["cn"] ) ) {
						$groups_secondaires[$i] = $groups[$loop]["cn"];
						$i++;
					}
*/					
				}
				//////
				
				// ecrire les logs
				$group_principal ? spip_log("recuperation du groupe principal $group_principal de $login (id_auteur=".$row_auteur['id_auteur'].")") : spip_log("ERREUR : pas de retour du groupe principal ldap du user $login");
				
				if ($group_principal!='' && $group_principal!='Eleve'){
					// verification de l'existance d'une zone Profs ou Administratifs 
	        		$row_zones = sql_fetsel('*','spip_zones','titre='.sql_quote($group_principal));
	        		if ($row_zones!=''){
	        			//recuperation de l'id de la zone
		        		$id_zone =$row_zones['id_zone'] ;
		        		$id_auteur =$row_auteur['id_auteur'] ;
	        			spip_log("sql->spip_zones : recup de l'id de la zone ".$row_zones['titre']." : id_zone= $id_zone");
	        			
	        			// on verifie si le user est deja dans cette zone
	        			$champs = array('id_zone', 'id_auteur');
						$where = array( 'id_zone='.$id_zone, 'id_auteur='.$id_auteur );
						$row_zones_auteurs = sql_fetsel($champs, "spip_zones_auteurs", $where);
						
						// si il ne fait pas partie de la zone on l'ajoute
	        			if (!$row_zones_auteurs){
	        				$ids['id_zone']=$id_zone;
	        				$ids['id_auteur']=$id_auteur;
							$zone_auteur = sql_insertq("spip_zones_auteurs", $ids,'',$serveur='connect',$option=true);
							if($row_zones_auteurs = sql_fetsel($champs, "spip_zones_auteurs", $where)) spip_log("sql->spip_zones_auteurs : $login (id_auteur=".$row_zones_auteurs['id_auteur'] ." a ete ajoute a la zone ".$row_zone['titre'].". id_zone=".$row_zones_auteurs['id_zone']."!");
	        			} 
	        		}
				}
				
				if($groups_secondaires!=''){
					foreach($groups_secondaires as $val_gs){
						spip_log("le user $login est dans le groupe ldap $val_gs");

//						if ( ereg ("Equipe", $val_gs)){
							// verification de l'existance d'une zone 
			        		$row_zones = sql_fetsel('*','spip_zones','titre='.sql_quote($val_gs));
			        		if($row_zones!=''){
			        			spip_log("la zone $val_gs est creee");
			        			//recuperation de l'id de la zone et de l'id_auteur
				        		$id_zone =$row_zones['id_zone'] ;
				        		$id_auteur =$row_auteur['id_auteur'] ;
				        		// on verifie si le user est deja dans cette zone
			        			$champs = array('id_zone', 'id_auteur');
								$where = array( 'id_zone='.$id_zone, 'id_auteur='.$id_auteur );
								$row_zones_auteurs = sql_fetsel($champs, "spip_zones_auteurs", $where);
							
								// si il ne fait pas partie de la zone on l'ajoute
			        			if (!$row_zones_auteurs){
			        				$ids['id_zone']=$id_zone;
			        				$ids['id_auteur']=$id_auteur;
									$zone_auteur = sql_insertq("spip_zones_auteurs", $ids,'',$serveur='connect',$option=true);
									if($row_zones_auteurs = sql_fetsel($champs, "spip_zones_auteurs", $where)) 
										spip_log("sql->spip_zones_auteurs : $login (id_auteur="
										.$row_zones_auteurs['id_auteur'] 
										." a ete ajoute a la zone ".$row_zone['titre']
										.". id_zone=".$row_zones_auteurs['id_zone']."!");
			        			} 
			        		}
//						}

					}
				}
        }
     	  ///////////// Fin du  test de recup des zones/groupes  ////////////
     	  
    }
} elseif ( $action=="logout" ) {
    close_session($idpers);
    @define('_DIR_RESTREINT_ABS', 'ecrire/');
    include_once _DIR_RESTREINT_ABS.'inc_version.php';

    include_spip('inc/cookie');
    include_once  _DIR_RESTREINT_ABS.'auth/ldap.php';
    include_spip('inc/session');

    $result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE login='$login' AND source='ldap'");
    $row_auteur = spip_fetch_array($result);
    ### DBG
    fich_debug("DBG >> id auteur : ".$row_auteur['id_auteur']."\n");
    supprimer_sessions($row_auteur['id_auteur']);
    spip_setcookie('spip_session', '', 0);
} else fich_debug("DBG >> No login No logout\n");

#On redirige vesr la page d'accueil dans tous les cas
echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
echo "<!--\n";
echo "top.location.href = '../lcs/index.php?url_redirect=accueil.php';\n";
echo "//-->\n";
echo "</script>\n";
?>
