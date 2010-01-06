<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
function exec_accorder_comptes_lcs_dist(){
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'accorder_comptes_lcs')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'accorder_comptes_lcs'),'data'=>''));
	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	// titre, partie, sous_partie (pour le menu)
	echo $commencer_page(_T('tispipskelet:titre_accorder_comptes_spip_lcs'), "editer", "editer");
	
	// titre
	echo "<br /><br /><br />\n"; // outch ! aie aie aie ! au secours !
	$ze_logo='<img src="'._DIR_PLUGIN_TISPIPSKELET.'/img_pack/nettoyer_comptes_lcs_spip.png" style="width:96px;vertical-align:middle;">';
	echo gros_titre(_T('tispipskelet:Nettoyage des comptes : Synchronisation des comptes avec l&rsquo;annuaire LCS'), $ze_logo, false);
	
	// colonne gauche
	echo debut_gauche('', true);
	
	echo debut_boite_info(true);
	echo propre(_T('tispipskelet:info_accorder_comptes_spip_lcs'));	
	echo fin_boite_info(true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'peupler_zones_lcs'),'data'=>''));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'accorder_comptes_lcs'),'data'=>''));
		if (autoriser('webmestre')) {
#		$res= icone_horizontale(_L('Acc&egrave;s restreint'), generer_url_ecrire("acces_restreint"), "../"._DIR_PLUGIN_TISPIPSKELET."/img_pack/logo_acces_restreint_48.png", "",false);
#		echo bloc_des_raccourcis($res);
	}

	// centre
	echo debut_droite('', true);
	// contenu
 
include ("/var/www/lcs/includes/headerauth.inc.php");
include ("/var/www/Annu/includes/ihm.inc.php");

function search_uidspip ($filter,$ldap_server, $ldap_port, $dn) {
  global  $ldap_grp_attr;
  
  // LDAP attributs
  $ldap_grp_attr = array (
    "cn",
    "memberuid"  );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
      $result=@ldap_list ($ds, $dn["groups"], $filter, $ldap_grp_attr);
      if ($result) {
        $info = ldap_get_entries( $ds, $result );
        if ($info["count"]) {
          // Stockage des logins des membres des classes
          //  dans le tableau $ret
          $init=0;
          for ($loop=0; $loop < $info["count"]; $loop++) {
            $group=split ("[\_\]",$info[$loop]["cn"][0],2);
            for ( $i = 0; $i < $info[$loop]["memberuid"]["count"]; $i++ ) {
              $ret[$init]["uid"] = $info[$loop]["memberuid"][$i];
              $ret[$init]["cat"] = $group[0];
              $init++;
            }
          }
        }
        ldap_free_result ( $result );
      }
    } 
    @ldap_close ( $ds );
  } 
  return $ret;
}

$grp_primaire= array ('Administratifs','Profs','Eleves');
	$$lcs_list_allusers=array();
	for ($index=0; $index < count($grp_primaire); $index++) {
	//recherche des membres (on recupere un tableau (login,groupe principal)
		$membres = search_uidspip("(cn=".$grp_primaire[$index].")",$ldap_server, $ldap_port, $dn);
	//traitement des données renvoyées: ici affichage		
			for ($loup=0; $loup < count($membres); $loup++) {
//			        echo $membres[$loup]["uid"].":".$membres[$loup]["cat"]."<br />";
						$lcs_list_allusers[]=$membres[$loup]['uid'];
	        }	
	}
	/*
	foreach ($lcs_list_allusers as $k => $lcsUser){
		echo "lcsUser_".$k." = ".$lcsUser."<br />";
	} 
	*/
		//	echo recuperer_fond("prive/contenu/tispip_lcs_groupes_ldap",$_GET);
	echo "<div class='ajax'>".recuperer_fond("prive/contenu/tispip_lcs_users_ldap",array('all_users_lcs'=>$lcs_list_allusers, 'afficher'=>'actifs', 'membres'=>$membres, $_GET))."</div>";

	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'accorder_comptes_lcs'),'data'=>''));
	echo fin_gauche(), fin_page();
}
?>