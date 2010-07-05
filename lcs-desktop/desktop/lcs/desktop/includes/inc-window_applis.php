<div class="abs" id="bar_top">
	<a class="open_window float_right reload" id="reload" href="" title="Recharger le bureau"></a>
	<a title="agendas" rel="statandgo.php?use=Agendas" href="#icon_dock_lcs_agendas" class="float_right open_win ext_link" id="clock"></a>
	
<?php
	if ( $idpers!=0 ) { 
?>
		
	<span class="float_right bar_link" id="view-connect">
		<a href="logout.php" title="Se d&eacute;connecter" style="cursor:pointer;color:#999;font-size:.8em;">&nbsp;
			<img alt="En ligne" src="images/connect.png" alt="" style="cursor:pointer;" />
		</a>&nbsp;|&nbsp;&nbsp;
	</span>
	<ul class="float_right" id="user_info_bar_btn">
		<li>
			|<span id="jqd_login"><?php echo "<img src=\"../Annu/images/".strtolower($group_principal)."_" .strtolower($user["sexe"])."_trsp.png\" height=\"16\" style=\"height:16px;vertical-align:middle;margin-top:-3px;\" /> ".$user["fullname"]; ?></span>&nbsp;&nbsp;
			|
			<ul class="" id="user_infos">
			<li class="box_trsp_black list_infos_user" style="display:block;">
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
 $text_infos = "<h2>".$user["fullname"]."</h2>\n";
  if ($user["description"]) $lst = "<p>".$user["description"]."</p>";
  if ( count($groups) ) {
    $lst .= "<h4>Membre des groupes:</h4>\n"
    	."<ul class=\"infos_user list_groups\">";
    for ($loop=0; $loop < count ($groups) ; $loop++) {
      //echo "<li><a href=\"group.php?filter=".$groups[$loop]["cn"]."\">".$groups[$loop]["cn"]."</a>,<span class=\"small\"> ".$groups[$loop]["description"]."</span>";
       $lst .= "<li style=\"float:none;line-height:20px;height:20px;margin:0;padding:0;\"><a class=\"test_ajax open_win pointer\" href=\"../Annu/group.php?filter=".$groups[$loop]["cn"]."\" rel=\"\" title=\"Voir le groupe\">";
      if ($groups[$loop]["type"]=="posixGroup"){
  	    $imgs = explode('_', $groups[$loop]["cn"]);
        $lst .= "<img src=\"../Annu/images/".strtolower($imgs[0]).".png\" style=\"width:20px;vertical-align:middle;\" width=\"20\"/> ";
        $lst .= " <strong>".preg_replace('/_/',' ',$groups[$loop]["cn"])."</strong>";
      } else{
        $lst .= $groups[$loop]["cn"];
      }
      $lst .=  "</a>";
/*      $lst .=  ",<small> ".$groups[$loop]["description"];
      $uid=$login;
      $login1=split ("[\,\]",ldap_dn2ufn($groups[$loop]["owner"]),2);
      if ( $uid == $login1[0] ) $lst .= "<strong><span class=\"ff8f00\">".$uid."&nbsp;(professeur principal)".$login1[0]."</span> ".ldap_dn2ufn($groups[$loop]["owner"])."</strong>";
      $lst .=  "</small>";
*/      $lst .=  "</li>\n";
      // Teste si n&#233;cessit&#233; d'affichage menu Ouverture/Fermeture Bdd et espace web perso des Eleves
      if ($groups[$loop]["cn"]=="Eleves") $ToggleAff=1;
    }

   if (!is_dir ("/home/".$user["uid"]) ) {
    $lst .= "<li><span class=\"orange\">L'utilisateur&nbsp;</span>".$user["fullname"]."<span class=\"orange\">&nbsp;n'a pas encore initialis&#233; son espace perso.</span></li>\n";
  } else {
    $lst .="<li><h3>Pages perso </h3> <a href=\"../~".$user["uid"]."/\" class=\"test_ajax open_win ext_link pointer\" title=\"Aller √† mon espace perso\"><tt>".$baseurl."~".$user["uid"]."</tt></a></li>\n";
  }
   $lst .="<li><h3>Adresse m&#232;l :  </h3><a href=\"mailto:".$user["email"]."\" class=\"pointer\"><tt>".$user["email"]."</tt></a></li>\n";
   if (!is_eleve($login) && $user["uid"]==$login && $test_squir=="1") $lst .="<li><a href=\"../Annu/mod_mail.php\" title=\"Aller √† la redirection\" class=\"test_ajax open_win ext_link pointer\">Rediriger mes mails vers une boite personnelle</a></li>";
   	$lst .=  "</ul>";
  	
   	echo $lst;
  }
	?>
			
			
			</li>
			</ul>
		</li>
	</ul>	
	<ul class="float_right found">
		<li>
			|<a class="open_window float_right msg ext_link" id="compose_msg" href="../squirrelmail/src/compose.php?mailbox=INBOX&startMessage=1" rel="squirrelmail" title="Envoyer un message"></a>
		</li>
	</ul>
	<ul class="float_right found">
		<li>
			|<a class="open_window float_right search ext_link" id="found" href="../Annu/search.php" title="Trouver un utilisateur, une classe, un groupe..." rel="annu"></a>
		</li>
	</ul>
<!--	<span class="float_right" id="mylinks_bar_btn">Listes&nbsp;&nbsp;|</span> -->
	<span class="float_right" id="otBuro_1" style="position:relaive;display:block;"><span class="float_left checked"></span><span id="otBuro_2">1</span><ul style="position:absolute;" class="menu"><li><a href="#desktop" class="space">1&nbsp;&nbsp;Lcs Bureau</a></li><li class="nospace"><a href="#inettuts">2&nbsp;&nbsp;<strong><i>i</i></strong>Lcs</a></li><li class="nospace"><a href="#monLcs">3&nbsp;&nbsp;MonLcs</a></li></ul></span>
	<span class='float_right btn_bar_top' id='alert_save_prefs' title='Enregistrer votre bureau' style='display:none;'></span>
<?php
	}else{
?>
	<span class="float_right" id="view-deconnect">
		<a class="open_win" href="#icon_dock_lcs_auth" title="Acc&eacute;der au formulaire de connexion" style="cursor:pointer;color:#999;font-size:.8em;">
			&nbsp;&nbsp;&nbsp;&nbsp;Se connecter&nbsp;&nbsp;
			<img alt="Acceder au formulaire de connexion" src="images/deconnect.png" style="cursor:pointer;" />&nbsp;&nbsp;&nbsp;&nbsp;
		</a>
	</span>
<?php
	}
?>


<?php
include('desktop/includes/inc-menu_applis.php');
	if ( $idpers!=0 ) { 
	}
?>
</div>
<div class="abs" id="bar_bottom">
	<a class="float_left" href="#" id="show_desktop" title="Show Desktop">
		<img src="desktop/images/icons/icon_22_desktop.png" />
	</a>
	<ul id="dock">
		<li id="icon_dock_lcs_auth">
			<a href="#window_lcs_auth">
				<img src="desktop/images/icons/icon_22_lcs.png" />
				LCS - Authentification
			</a>
		</li>
		<li id="icon_dock_lcs_admin">
			<a href="#window_lcs_admin">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				LCS - Administration
			</a>
		</li>
		<li id="icon_dock_lcs_helpdesk">
			<a href="#window_lcs_helpdesk">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				LCS - Helpdesk
			</a>
		</li>
		<li id="icon_dock_lcs_prefs">
			<a href="#window_lcs_prefs">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				LCS - Pr&eacute;f&eacute;rences
			</a>
		</li>
		<li id="icon_dock_lcs_texteditor">
			<a href="#window_lcs_texteditor">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				LCS - Editer un texte
			</a>
		</li>
		<li id="icon_dock_lcs_add_links">
			<a href="#window_lcs_add_links">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				LCS - Ajout de liens
			</a>
		</li>
		<li id="icon_dock_lcs_path">
			<a href="#window_lcs_path">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				LCS - Parcours
			</a>
		</li>
		<li id="icon_dock_lcs_listes">
			<a href="#window_lcs_listes">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				LCS - Listes
			</a>
		</li>
		<li id="icon_dock_lcs_legal">
			<a href="#window_lcs_listes">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				LCS - A propos
			</a>
		</li>
		<li id="icon_dock_lcs_temp">
			<a href="#window_lcs_temp">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:22px;" />
				LCS - test lien ext
			</a>
		</li>
<?php
	echo $html_status_bar;
?>
	</ul>
	
