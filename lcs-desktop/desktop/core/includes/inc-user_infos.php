<?php 
	//test si squirrelmail est installe pour redirection mails
	$uid = $_GET[uid];
	$toggle = $_GET[toggle];
	$action = $_Get[action];
	
	$query="SELECT value from applis where name='squirrelmail'";
	$result=mysql_query($query);
	if ($result) 
	{
		if ( mysql_num_rows($result) !=0 ) {
			$r=mysql_fetch_object($result);
			$test_squir=$r->value;
		}
		else $test_squir="0";
	}
	else $test_squir="0";
	//fin test squirrelmail
	//test listes de diffusion
	exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/mailing_list.cf", $AllOutPut, $ReturnValueShareName);
    $listediff = 0;
    if ( count($AllOutPut) >= 1) $listediff = 1;
	// fin test listes de diffusion
	$text_infos = "<h2>".$user["fullname"]."</h2>\n";
	if ($user["description"]) $lstDesc = "<p>".$user["description"]."</p>";
	list ($idpers, $login)= isauth();
	if (!displogin($idpers)) {
	$lstLastConnect= "<ul class=\"infos_user list_groups\"><li><tt>F&#233;licitations, vous venez de vous connecter pour la 1&#232;re fois sur votre
          espace perso Lcs. Afin de garantir la confidentialit&#233; de vos donn&#233;es, nous
          vous encourageons, &agrave; changer votre mot de passe <a class=\"open_win ext_link\" href=\"../Annu/mod_pwd.php\" rel=\"annu\" title=\"\">en suivant ce lien... </a>
          </tt></li></ul>\n";
	} else {
    $accord == "";
    if ($user["sexe"] == "F") $accord="e";
		$lstLastConnect="<ul class=\"infos_user list_groups\"><li><tt>Derni\xe8re connexion le : " . displogin($idpers) . "</tt>\n";
		/* Affichage des stats user */
		$lstLastConnect.= "<br /><tt>Vous vous \xeates connect\xe9".$accord." " . dispstats($idpers) . " fois \xe0 votre espace perso.</tt></li></ul>\n";
	}

	if ( count($groups) ) {
		$lstIntroGrps = "<h3 class=\"btn_groups triangle_updown\" style=\"padding-left:20px;\">Membre des groupes</h3>\n"
//		."<span class=\"triangle_updown float_left\" style=\"padding:5px;\"></span>\n"
   		."<ul class=\"infos_user list_groups block_updown\" style=\"display:none;\">";
    	$co=$ma=$eq=$di=$cl=0;
    		$tbl_gp = array("Administratifs","Profs","Eleves");
    	for ($loop=0; $loop < count ($groups) ; $loop++) {
    		if (in_array($groups[$loop]["cn"], $tbl_gp)){ 
    			$group_principal = $groups[$loop]["cn"];
  				$lgp ="<li class=\"group_title\"><strong>Goupe principal :</strong></li>";
				$lgp .=info_item_group($groups[$loop]["cn"],$groups[$loop]["type"],$domain,$login,$listediff,$test_squir);
    		}
 			else if ( preg_match("/Cours/", $groups[$loop]["cn"] )) {
 				if($co==0 ) {
 					$lst_co .="<li class=\"group_title\"><strong>Cours</strong></li>";
 					$co++;
 				}
		 		$lst_co .= info_item_group($groups[$loop]["cn"],$groups[$loop]["type"],$domain,$login,$listediff,$test_squir);
 			}
		 	else if ( preg_match("/Equipe/", $groups[$loop]["cn"] )) {
		 		if ($eq==0 ) {
		 			$lst_eq .="<li class=\"group_title\"><strong>Equipes</strong></li>";
		 			$eq++;
		 		}
		 		$lst_eq .= info_item_group($groups[$loop]["cn"],$groups[$loop]["type"],$domain,$login,$listediff,$test_squir);
		 	}
		 	else if ( preg_match("/Matiere/", $groups[$loop]["cn"] )) {
		 		if ($ma==0 ) {
		 			$lst_ma .="<li class=\"group_title\"><strong>Mati&egrave;res</strong></li>";
		 			$ma++;
		 		}
		 		$lst_ma .= info_item_group($groups[$loop]["cn"],$groups[$loop]["type"],$domain,$login,$listediff,$test_squir);
		 	}
		 	else if ( preg_match("/Classe/", $groups[$loop]["cn"] ) ) {
		 		if ($cl==0){
		 			$lst_cl .="<li class=\"group_title\"><strong>Ma classe : </strong></li>";
		 			$cl++;
		 		}
		 		$lst_cl .= info_item_group($groups[$loop]["cn"],$groups[$loop]["type"],$domain,$login,$listediff,$test_squir);
		 	}
		 	else {
		 		if ($di==0)  {
		 		$lst_di .="<li class=\"group_title\"><strong>Divers</strong></li>";
		 			$di++;
		 		}
		 		$lst_di .= info_item_group($groups[$loop]["cn"],$groups[$loop]["type"],$domain,$login,$listediff,$test_squir);
		 	}
			if($group_principal == ""){
//				$lst .= info_item_group($groups[$loop]["cn"],$groups[$loop]["type"],$domain);
		 	}
		// Teste si n&#233;cessit&#233; d'affichage menu Ouverture/Fermeture Bdd et espace web perso des Eleves
		if ($groups[$loop]["cn"]=="Eleves") $ToggleAff=1;
    }

   	$lst .=  $lst_cl.$lst_co.$lst_eq.$lst_ma.$lst_di."</ul>";
   	$lst .=  "<h3 class=\"triangle_updown\" style=\"padding-left:20px;\">Pages perso</h3><ul class=\"infos_user list_groups block_updown\" style=\"display:none;\">";

	if (!is_dir ("/home/".$user["uid"]) ) {
		$lst .= "<li>".$user["fullname"]." : Vous n'avez pas encore initialis&#233; votre espace perso.</li>\n";
  	} else {
    	$lst .="<li class=\"user_link\">"
    	."<a href=\"../~".$user["uid"]."/\""
    	." class=\"test_ajax open_win ext_link pointer\""
    	." rel=\"webperso\" title=\"".$baseurl."~".$user["uid"]."\">"
    	."<img src=\"core/images/icons/network.png\" alt=\"\"".
    	" style=\"width:20px;vertical-align:middle;\" width=\"20\" />"
    	."<tt>&nbsp;Mon espace web</tt></a></li></ul>\n";
  	}
	$lst .="<h3 class=\"triangle_updown\" style=\"padding-left:20px;\">Courriel</h3><ul class=\"infos_user list_groups block_updown\" style=\"display:none;\"><li class=\"user_link\">"
	."<input type=\"text\" id=\"user_mail\"style=\"border:none;background:#fff;width:230px;margin:2px 5px;\" value=\"".$user["email"]."\" onclick=\"$(this).select();\">"
	."<div class=\"small\">Cliquez sur l'adresse et apppuyez sur les touches Ctrl + c (Pomme + c pour Mac) pour copier votre adresse courriel</div>"
	."</li>\n";
	// Pourquoi une restriction aux eleves ????
	if (!is_eleve($login) && $user["uid"]==$login && $test_squir=="1") {
		$lst .="<li class=\"user_link\"><a href=\"../Annu/mod_mail.php\""
		." title=\"Aller &agrave; la redirection\""
		." rel=\"annu\" class=\"test_ajax open_win ext_link pointer\">"
		."<img src=\"core/images/annu/mail-redirect.png\" style=\"width:20px;vertical-align:middle;\" width=\"20\"/> "
		."Rediriger vers une boite personnelle</a>"
	."<div class=\"small\">Vous pouvez rediriger vos courriels vers votre boite personnelle. Les courriels redirig&eacute;s pourrons  n&eacute;anmoins &ecirc;tre conserv&eacute;s dans votre boite &agrave; lettre Lcs</div>"
		."</li>"; 
	}

   	$lst .=  "</ul>";
  	$text_infos .=$lstLastConnect.$lstIntroGrps.$lgp.$lst;
  }
  function info_item_group($group,$type,$domain,$login,$listediff,$test_squir) {
  			$ret .= "<li class=\"user_link\"><a class=\"test_ajax open_win pointer\""
			." href=\"../Annu/group.php?filter=".$group."\""
			." rel=\"path\" title=\"Voir le groupe "
			.preg_replace('/_/',' ',$group)."\">";
			if ($type=="posixGroup"){
			$imgs = explode('_', $group);
			$ret .= "<img src=\"core/images/annu/".preg_replace('/ /','',strtolower($imgs[0])).".png\" style=\"width:20px;vertical-align:middle;\" width=\"20\"/> ";
			$ret .= " <strong>".preg_replace('/_/',' ',$group)."</strong>";
			} else{
			$ret .= $group;
			}
		$ret .=  "</a>";
		if (! is_eleve($login) && $listediff && $test_squir=="1") 
		$ret .="<a href=\"../squirrelmail/src/compose.php?send_to=".$group."@".$domain."\" class=\"open_win ext_link\" rel=\"squirrelmail\" title=\"Envoyer un message &agrave; ce groupe\">  <img src=\"core/images/annu/mail.png\" alt=\"\" class=\"float_right\" style=\"margin:5px 5px 0 0;\"/></a>";
		/*
		$ret .=  ",<small> ".$groups[$loop]["description"];
		$uid=$login;
		$login1=split ("[\,\]",ldap_dn2ufn($groups[$loop]["owner"]),2);
		if ( $uid == $login1[0] ) $ret .= "<strong><span class=\"ff8f00\">".$uid."&nbsp;(professeur principal)".$login1[0]."</span> ".ldap_dn2ufn($groups[$loop]["owner"])."</strong>";
		$ret .=  "</small>";
		*/
		$ret .=  "</li>\n";
return $ret;
  }
	?>
			
