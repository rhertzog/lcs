<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/people.php
   Equipe Tice academie de Caen
   Derniere mise à jour : 16/10/2008
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  $uid = $_GET[uid];
  $toggle = $_GET[toggle];
  $action = $_Get[action];

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  header_html();
  aff_trailer ("3");
  #$TimeStamp_0=microtime();
  // correctif provisoire
  $user_tmp = $user;
  // fin correctif
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
    for ($loop=0; $loop < count ($groups) ; $loop++) {
      //echo "<LI><A href=\"group.php?filter=".$groups[$loop]["cn"]."\">".$groups[$loop]["cn"]."</A>,<font size=\"-2\"> ".$groups[$loop]["description"];
      echo "<LI><A href=\"group.php?filter=".$groups[$loop]["cn"]."\">";
      if ($groups[$loop]["type"]=="posixGroup")
        echo "<STRONG>".$groups[$loop]["cn"]."</STRONG>";
      else
        echo $groups[$loop]["cn"];
      echo "</A>,<font size=\"-2\"> ".$groups[$loop]["description"];
      $login1=split ("[\,\]",ldap_dn2ufn($groups[$loop]["owner"]),2);
      if ( $uid == $login1[0] ) echo "<strong><font color=\"#ff8f00\">&nbsp;(professeur principal)</font></strong>";
      echo "</font></LI>\n";
      // Teste si nécessité d'affichage menu Ouverture/Fermeture Bdd et espace web perso des Eleves
      if ($groups[$loop]["cn"]=="Eleves") $ToggleAff=1;
    }
    echo "</UL>";
  }
  if (!is_dir ("/home/".$user["uid"]) ) {
    echo "<P><font color=\"orange\">L'utilisateur&nbsp;</font>".$user["fullname"]."<font color=\"orange\">&nbsp;n'a pas encore initialisé son espace perso.</font></p>\n";
  } else {
    echo "<br>Pages perso : <a href=\"../~".$user["uid"]."/\"><tt>".$baseurl."~".$user["uid"]."</tt></a><br>\n";
  }
   echo "Adresse mèl : <a href=\"mailto:".$user["email"]."\"><tt>".$user["email"]."</a></tt><br>\n";
  // Affichage Menu people_admin
  if (is_admin("Annu_is_admin",$login) == "Y" ) {
  ?>
  <br>
  <ul style="color: red;">
    <li><a href="mod_user_entry.php?uid=<? echo $user["uid"] ?>">Modifier</a><br>
    <li><a href="del_user.php?uid=<? echo $user["uid"] ?>" onclick= "return getconfirm();">Supprimer</a><br>
    <li><a href="add_user_group.php?uid=<? echo $user["uid"] ?>">Ajouter &agrave; des groupes</a><br>
	<li><a href="del_group_user.php?uid=<? echo $user["uid"] ?>">Supprimer des groupes d'appartenance</a><br>
  <?
    if (ldap_get_right("Lcs_is_admin",$login)=="Y") {
    	echo "<li><a href=\"add_user_right.php?uid=" . $user["uid"] ."\">Gérer les droits</a><br>";
    }
    if ( $ToggleAff==1 && @is_dir ("/home/".$user["uid"]."/public_html") ) {
      // Ouverture/Fermeture Espace web
      exec ("ls -ld /home/".$user["uid"]."/public_html", $ReturnValue);
      $droits = explode(" ", $ReturnValue[0]); 
      if ($droits[3] == "root") {
        echo "<li><a href=\"adm_WebPerso.php?uid=".$user["uid"]."&toggle=1"."\">Activer l'espace <em>Web</em></a>\n";
      } else {
        echo "<li><a href=\"adm_WebPerso.php?uid=".$user["uid"]."&toggle=0"."\">Désactiver l'espace <em>Web</em></a>\n";
        // Gestion du type php
        // Cas des groupes principaux autres qu'Eleves
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh exist $uid", $ReturnExist);
        if ($ReturnExist[0] == "No" ) {
          exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh nbr", $ReturnNbr);
          if ( $ReturnNbr[0] < 5 )
            echo "<li><a href=\"userdirphptype.php?uid=".$user["uid"]."&action=add"."\">Autoriser le type php</a>\n</li>\n";
          else
            echo "<li><a href=\"userdirphptype.php?action=list"."\">Liste des utilisateurs possédant le type php</a>\n</li>\n";
        } elseif ($ReturnExist[0] == "Yes" )
          echo "<li><a href=\"userdirphptype.php?uid=".$user["uid"]."&action=rm"."\">Retirer le type php</a>\n</li>\n";
        else
        echo "<li>Impossible de modifier le type php.</li>\n"; 
      }
      // Ouverture/Fermeture Espace bdd perso
      //Creation du nom de la base de donnees
      $userDb = ereg_replace("\.","",$user["uid"]);
      $userDb = ereg_replace("-","",$userDb);
      $userDb = ereg_replace("_","",$userDb);
      $userDb = $userDb."_db";
      if ( !is_dir ("/var/lib/mysql/$userDb") ) {
        echo "<li><a href=\"adm_BddPerso.php?uid=".$user["uid"]."&toggle=1"."\">Activer la <em>base de donn&eacute;es</em></a><br>\n";
      } else {
        echo "<li><a href=\"adm_BddPerso.php?uid=".$user["uid"]."&toggle=0"."\">Désactiver la <em>base de donn&eacute;es</em></a><br>\n";
      }
      echo "</ul>\n";
    } else {
      // Gestion du type php
      // Cas des groupes principaux autres qu'Eleves
      exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh exist $uid", $ReturnExist);
      if ($ReturnExist[0] == "No" ) {
        exec ("/usr/bin/sudo /usr/share/lcs/scripts/userdirphp.sh nbr", $ReturnNbr);
        if ( $ReturnNbr[0] < 5 )
            echo "<li><a href=\"userdirphptype.php?uid=".$user["uid"]."&action=add"."\">Autoriser le type php</a>\n</li>\n";
        else
            echo "<li><a href=\"userdirphptype.php?action=list"."\">Liste des utilisateurs possédant le type php</a>\n</li>\n";
      } elseif ($ReturnExist[0] == "Yes" )
        echo "<li><a href=\"userdirphptype.php?uid=".$user["uid"]."&action=rm"."\">Retirer le type php</a>\n</li>\n";
      else
        echo "<li>Impossible de modifier le type php.</li>\n";
      echo "</ul>\n";
      echo "</ul>\n";
    }
  } // Fin affichage menu people_admin


  // Test de l'appartenance à la classe
  if ((tstclass($login,$user["uid"])==1) and (ldap_get_right("sovajon_is_admin",$login)=="Y") and ($login != $user["uid"])) {
  echo "<br>\n";
  echo "<ul style=\"color: red;\">\n";
  echo "<li><a href=\"mod_user_entry.php?uid=".$user["uid"]."\">Modifier le compte de mon élève ...</a><br>\n";
  echo "</ul>\n";
  }

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
