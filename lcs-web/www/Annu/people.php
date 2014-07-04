<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 22/06/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

$login=$_SESSION['login'];

include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";


  if (count($_GET)>0) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
  	$uid=$purifier->purify($_GET['uid']);
  	$toggle=(isset($_GET['toggle']))?$purifier->purify($_GET['toggle']):0;
  	$action=(isset($_GET['action'])) ? $purifier->purify($_GET['action']): "";// $action non used -> a supprimer ??
  }

  //test si webmail est installe pour redirection mails
  $query="SELECT value from applis where name='squirrelmail' or name='roundcube'";
  $result=mysql_query($query);
  if ($result)  {
  	if ( mysql_num_rows($result) !=0 ) {
          $r=mysql_fetch_object($result);
          $test_squir=$r->value;
     } else $test_squir="0";
   } else $test_squir="0";
   //fin test webmail

   //test listes de diffusion
   exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/mailing_list.cf", $AllOutPut, $ReturnValueShareName);
   $listediff = 0;
   if ( count($AllOutPut) >= 1) $listediff = 1;
   // test si desktop et install et actif
   $query="SELECT value from applis where name='desktop';";
   $result=mysql_query($query);
   if ($result)  {
   	if ( mysql_num_rows($result) !=0 ) {
          $r=mysql_fetch_object($result);
          $test_desktop=$r->value;
     } else $test_desktop="0";
	} else $test_desktop="0";
   //

  header_html();
  aff_trailer ("3");
  #$TimeStamp_0=microtime();
  list($user, $groups)=people_get_variables($uid, true);
  #$TimeStamp_1=microtime();
  #############
  # DEBUG     #
  #############
  #echo "<u>debug</u> :Temps de recherche = ".duree($TimeStamp_0,$TimeStamp_1)."&nbsp;s<BR>";
  #############
  # Fin DEBUG #
  #############
  echo "<H2>".$user["fullname"]."</H2>\n";
  if ($user["description"]) echo "<p>".$user["description"]."</p>";
  if ( count($groups) ) {
    echo "<U>Membre des groupes</U> :<BR><UL>\n";
    $jeton_group=md5($_SESSION['token'].htmlentities("/Annu/group.php"));
    for ($loop=0; $loop < count ($groups) ; $loop++) {
      //echo "<LI><A href=\"group.php?filter=".$groups[$loop]["cn"]."\">".$groups[$loop]["cn"]."</A>,<font size=\"-2\"> ".$groups[$loop]["description"];
      echo "<LI><A href=\"group.php?filter=".$groups[$loop]["cn"]."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/group.php"))."\">";
      if ($groups[$loop]["type"]=="posixGroup")
        echo "<STRONG>".$groups[$loop]["cn"]."</STRONG>";
      else
        echo $groups[$loop]["cn"];
      echo "</A>,<font size=\"-2\"> ".$groups[$loop]["description"]."&nbsp;";
      if (! is_eleve($login) && $listediff && $test_squir=="1")
           echo " <a href=\"mailto:".$groups[$loop]["cn"]."@".$domain."\" >  <img src=\"images/mail.png\" alt=\"Envoyer un mail\"  title=\"Envoyer un mail &#224; ce groupe\" border=0 ></a><br>\n";
     //    $login1=preg_split ("/,/",ldap_dn2ufn($groups[$loop]["owner"]),2);
     // if ( $uid == $login1[0] ) echo "<strong><font color=\"#ff8f00\">&nbsp;(professeur principal)</font></strong>";
      echo "</font></LI>\n";
      // Teste si n&#233;cessit&#233; d'affichage menu Ouverture/Fermeture Bdd et espace web perso des Eleves
      $ToggleAff=($groups[$loop]["cn"]=="Eleves") ? 1 : 0;
    }
    echo "</UL>";
  }
  if (!is_dir ("/home/".$user["uid"]) ) {
    echo "<P><font color=\"orange\">L'utilisateur&nbsp;</font>".$user["fullname"]."<font color=\"orange\">&nbsp;n'a pas encore initialis&#233; son espace perso.</font></p>\n";
  } else {
    echo "<br>Pages perso : <a href=\"../~".$user["uid"]."/\"><tt>".$baseurl."~".$user["uid"]."</tt></a><br>\n";
  }
   echo "Adresse m&#232;l : <a href=\"mailto:".$user["email"]."\"><tt>".$user["email"]."</a></tt><br>\n";
   if ((ldap_get_right("Mail_can_redir",$login)=="Y") && $user["uid"]==$login && $test_squir=="1")
    echo "<br><a href=\"mod_mail.php?jeton=".md5($_SESSION['token'].htmlentities("/Annu/mod_mail.php"))."\">Rediriger mes mails vers une boite personnelle</a>";
    // Affichage Menu people_admin
  if (is_admin("Annu_is_admin",$login) == "Y" ) {
  ?>
  <br>
  <ul style="color: red;">
    <li><a href="mod_user_entry.php?uid=<? echo $user["uid"] ."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/mod_user_entry.php"))?>">Modifier le compte</a><br>
    <li><a href="del_user.php?uid=<? echo $user["uid"]."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/del_user.php")) ?>" onclick= "return getconfirm();">Supprimer le compte</a><br>
    <li><a href="pass_user_init.php?uid=<? echo $user["uid"] ."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/pass_user_init.php")) ?>" onclick= "return getconfirminitpass();">R&#233initialiser le mot de passe</a><br>
    <?php
    	if ( $test_desktop == "1" ) {
			?>
			<li><a href="del_profil_desktop.php?uid=<? echo $user["uid"] ."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/del_profil_desktop.php"))?>">R&#233initialiser le profil bureau</a><br>
			<?
    	}
    ?>
    <li><a href="add_user_group.php?uid=<? echo $user["uid"] ."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/add_user_group.php"))?>">Ajouter &agrave; des groupes</a><br>
	<li><a href="del_group_user.php?uid=<? echo $user["uid"] ."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/del_group_user.php"))?>">Supprimer des groupes d'appartenance</a><br>
  <?
    if ($ad_auth_delegation == "true") {
	if (user_has_ad_auth($uid)) {
	    echo "<li>Compte authentifi&eacute; par l'annuaire Active Directory. <a href=\"delegate_auth_ad.php?uid=" . $user["uid"] . "&action=disable&verbose=1&jeton=".md5($_SESSION['token'].htmlentities("/Annu/delegate_auth_ad.php"))."\">Basculer vers une authentification g&eacute;r&eacute;e par le LCS.</a>.<br/>";
	} else {
	    echo "<li>Compte authentifi&eacute; par le LCS. <a href=\"delegate_auth_ad.php?uid=" . $user["uid"] . "&action=enable&verbose=1&jeton=".md5($_SESSION['token'].htmlentities("/Annu/delegate_auth_ad.php"))."\">Basculer vers une authentification g&eacute;r&eacute;e par l'annuaire Active Directory.</a>.<br/>";
	}
    }
    if (ldap_get_right("Lcs_is_admin",$login)=="Y") {
    	echo "<li><a href=\"add_user_right.php?uid=" . $user["uid"] ."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/add_user_right.php"))."\">G&#233;rer les droits</a><br>";
    	if ($user["uid"]!=$login && $test_squir=="1") echo "<li><a href=\"mod_mail.php?uid=" . $user["uid"] ."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/mod_mail.php"))."\">Rediriger les mails</a> <br>";
    }

    if ( $ToggleAff==1 && @is_dir ("/home/".$user["uid"]."/public_html") ) {
      // Ouverture/Fermeture Espace web
      exec ("ls -ld /home/". escapeshellarg($user["uid"]) ."/public_html", $ReturnValue);
      $droits = explode(" ", $ReturnValue[0]);
      if ($droits[3] == "root") {
        echo "<li><a href=\"adm_WebPerso.php?uid=".$user["uid"]."&toggle=1"."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/adm_WebPerso.php"))."\">Activer l'espace <em>Web</em></a>\n";
      } else {
        echo "<li><a href=\"adm_WebPerso.php?uid=".$user["uid"]."&toggle=0"."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/adm_WebPerso.php"))."\">D&#233;sactiver l'espace <em>Web</em></a>\n";
      }
      // Ouverture/Fermeture Espace bdd perso
      //Creation du nom de la base de donnees
      $userDb = mb_ereg_replace("\.","",$user["uid"]);
      $userDb = mb_ereg_replace("-","",$userDb);
      $userDb = mb_ereg_replace("_","",$userDb);
      $userDb = $userDb."_db";
 
 	   $res = mysql_query("SHOW DATABASES");
	   while ($row = mysql_fetch_assoc($res)) 
     		if ($row['Database'] == $userDb) { $userdb=1; break;} else $userdb=0;
	
      if ( $userdb == 0 ) {     
        echo "<li><a href=\"adm_BddPerso.php?uid=".$user["uid"]."&toggle=1"."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/adm_BddPerso.php"))."\">Activer la <em>base de donn&eacute;es</em></a><br>\n";
      } else {
        echo "<li><a href=\"adm_BddPerso.php?uid=".$user["uid"]."&toggle=0"."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/adm_BddPerso.php"))."\">D&#233;sactiver la <em>base de donn&eacute;es</em></a><br>\n";
      }
      echo "</ul>\n";
    }
	echo "</ul>\n";
  } // Fin affichage menu people_admin


  // Test de l'appartenance &#224; la classe
  if ((tstclass($login,$user["uid"])==1) and (ldap_get_right("sovajon_is_admin",$login)=="Y") and ($login != $user["uid"])) {
  	echo "<br>\n";
  	echo "<ul style=\"color: red;\">\n";
  	echo "<li><a href=\"mod_user_entry.php?uid=".$user["uid"]."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/mod_user_entry.php"))."\">Modifier le compte de mon &#233;l&#232;ve ...</a><br>\n";
  	echo "</ul>\n";
  }

   // swekey
if (( is_dir ("/usr/share/lcs/swekey")) && ($login == $user["uid"])) {
	echo '<div id="del_swekey"></div>';
	if ($_SERVER['PHP_SELF']=="/Annu/people.php"){
		echo '<SCRIPT language = "javascript" type = "text/javascript" src = "../../swekey/swekey_integrate.js"></SCRIPT>';
		echo '<SCRIPT language = "javascript" type = "text/javascript" src = "../../swekey/swekey.js"></SCRIPT>';
		echo '<SCRIPT language = "javascript" type = "text/javascript" src = "../../swekey/my_swekey.js"></SCRIPT>';
		echo '<SCRIPT language = "javascript" type = "text/javascript">
		var idk = Swekey_ListKeyIds().substring(0, 32);
		who_user(idk,"'.$login.'");
		 </SCRIPT>';
	}

}

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
