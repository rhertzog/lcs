<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
function exec_creer_zones_groupes_lcs_dist(){
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'peupler_zones_lcs')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'peupler_zones_lcs'),'data'=>''));
	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	// titre, partie, sous_partie (pour le menu)
	echo $commencer_page(_T('tispipskelet:titre_peupler_zones_lcs'), "editer", "editer");
	
	// titre
	echo "<br /><br /><br />\n"; // outch ! aie aie aie ! au secours !
	$ze_logo='<img src="'._DIR_PLUGIN_TISPIPSKELET.'/img_pack/logo_biduls_acces-restreint_120.png" style="width:96px">';
	echo gros_titre(_T('tispipskelet:Cr&eacute;ation des zones d&rsquo;acc&egrave;s restreint issues des groupes de l&rsquo;annuaire LCS'), $ze_logo, false);
	
	// colonne gauche
	echo debut_gauche('', true);
	
	echo debut_boite_info(true);
	echo propre(_T('tispipskelet:info_creer_zones_groupes_ldap'));	
	echo fin_boite_info(true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'peupler_zones_lcs'),'data'=>''));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'peupler_zones_lcs'),'data'=>''));
		if (autoriser('webmestre')) {
		$res= icone_horizontale(_L('Acc&egrave;s restreint'), generer_url_ecrire("acces_restreint"), "../"._DIR_PLUGIN_TISPIPSKELET."/img_pack/logo_acces_restreint_48.png", "",false);
		echo bloc_des_raccourcis($res);
	}

	// centre
	echo debut_droite('', true);
	// contenu
 
include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");
function my_people_get_variables ($serveur,$port,$Dn)
{
  $ldap_server=$serveur;
  $ldap_port=$port;
  $dn=$Dn;
  global $error;
  $error="";
  // LDAP attribute
 
  $ldap_group_attr = array (
    "cn",
    "memberuid",
    "description",  // Description du groupe
  );

  $ds = @ldap_connect ( $ldap_server, $ldap_port );
  if ( $ds ) {
    $r = @ldap_bind ( $ds ); // Bind anonyme
    if ($r) {
       
        // Recherche des groupes d'appartenance dans la branche Groups
        
        $filter = "(&(objectclass=posixGroup))";
        $result = @ldap_list ( $ds, $dn["groups"], $filter, $ldap_group_attr );
        if ($result) {
          $info = @ldap_get_entries ( $ds, $result );
          if ( $info["count"]) {
            for ($loop=0; $loop<$info["count"];$loop++) {
              //if ($info[$loop]["member"][0] == "") $typegr="posixGroup"; else $typegr="groupOfNames";
              $typegr="posixGroup";
              $ret_group[$loop] = array (
                "cn"           => $info[$loop]["cn"][0],
                //"owner"        => $info[$loop]["owner"][0],
                "description"  => utf8_decode($info[$loop]["description"][0]),
                "type" => $typegr
              );
            }
            usort($ret_group, "cmp_cn");
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
 return array( $ret_group);
}

// Recherche des groupes d'appartenance de l'utilisateur $login
  	list($groups)=my_people_get_variables($ldap_server,$ldap_port,$dn);
	$i=0;
// Recherche du groupe principal 

	for ($loop=0; $loop < count ($groups) ; $loop++) {
		if ( $groups[$loop]["cn"] == "Administratifs" ) $group_principal[] = "Administratifs";
		elseif ( $groups[$loop]["cn"] == "Profs" ) $group_principal[] = "Profs";
		elseif ( $groups[$loop]["cn"] == "Eleves" ) $group_principal[] = "Eleves";
		 
//recherche des groupes secondaires 			

// la tu filtres les groupes que tu veux garder parmi Classe, Equipe, Cours, Matière ou autre

			elseif ( ereg ("Classe", $groups[$loop]["cn"] ) ) {
				$groups_secondaires[$i] = $groups[$loop]["cn"];
				$i++;
				$classes[] = $groups[$loop]["cn"];
			}
			elseif ( ereg ("Equipe", $groups[$loop]["cn"] ) ) {
				$groups_secondaires[$i] = $groups[$loop]["cn"];
				$i++;
				$equipes[] = $groups[$loop]["cn"];
			}
		
			elseif ( ereg ("Cours", $groups[$loop]["cn"] ) ) {
				$groups_secondaires[$i] = $groups[$loop]["cn"];
				$i++;
				$cours[] = $groups[$loop]["cn"];
			}
			elseif ( ereg ("Matiere", $groups[$loop]["cn"] ) ) {
				$groups_secondaires[$i] = $groups[$loop]["cn"];
				$i++;
				$matieres[] = $groups[$loop]["cn"];
			}
			else {
			$groups_secondaires[$i] = $groups[$loop]["cn"];
				$i++;
				$autres[] = $groups[$loop]["cn"];
			}
		
		}
		$ret_all_groups=array('Groupe principal'=>$group_principal,'Groupes secondaires'=>$groups_secondaires,'Equipes'=>$equipes,'Cours'=>$cours,'Matières'=>$matieres,'Classes'=>$classes,'Autres'=>$autres);
		
		
		//	echo recuperer_fond("prive/contenu/tispip_lcs_groupes_ldap",$_GET);
echo "<div class='ajax'>".recuperer_fond("prive/contenu/tispip_lcs_groupes_ldap",array('all_groupes'=>$ret_all_groups, $_GET))."</div>";
/*
echo "<div class='ajax'>".recuperer_fond("prive/contenu/ajouter_zones_groupes_annuaire",array('groupes_secondaires'=>$group_principal,'titre'=>'Groupe principal', $_GET))."</div>";
echo "<div class='ajax'>".recuperer_fond("prive/contenu/ajouter_zones_groupes_annuaire",array('groupes_secondaires'=>$equipes,'titre'=>'Equipes' ,'ajax' , $_GET))."</div>";
echo "<div class='ajax'>".recuperer_fond("prive/contenu/ajouter_zones_groupes_annuaire",array('groupes_secondaires'=>$cours,'titre'=>'Cours','p'=>'cours', 'ajax', $_GET))."</div>";
echo "<div class='ajax'>".recuperer_fond("prive/contenu/ajouter_zones_groupes_annuaire",array('groupes_secondaires'=>$matieres ,'titre'=>'Mati&egrave;res','p'=>'matieres', 'ajax', $_GET))."</div>";
echo "<div class='ajax'>".recuperer_fond("prive/contenu/ajouter_zones_groupes_annuaire",array('groupes_secondaires'=>$classes,'titre'=>'Classes' , $_GET))."</div>";
*/
	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'peupler_zones_lcs'),'data'=>''));
	echo fin_gauche(), fin_page();
}
?>