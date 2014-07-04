<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 04/04/2014
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
 	$filter=filter_var($purifier->purify($_GET['filter']),FILTER_SANITIZE_STRING);
  }
  header_html();
  aff_trailer ("3");
  #$TimeStamp_0=microtime();
  $group=search_groups("cn=".$filter);
  $uids = search_uids ("(cn=".$filter.")", "half");
  $people = search_people_groups ($uids,"(sn=*)","cat");
  #$TimeStamp_1=microtime();
  #############
  # DEBUG     #
  #############
  #echo "<u>debug</u> :Temps de recherche = ".duree($TimeStamp_0,$TimeStamp_1)."&nbsp;s<BR><BR>";
  #############
  # Fin DEBUG #
  #############
  if (count($people)) {
    // affichage des resultats
    // Nettoyage des _ dans l'intitule du groupe
    $intitule =  strtr($filter,"_"," ");
    echo "<U>Groupe</U> : $intitule <font size=\"-2\">".$group[0]["description"]."</font><BR>\n";
    echo "Il y a ".count($people)." membre";
    if ( count($people) >1 ) echo "s";
    echo" dans ce groupe<BR>\n";
    echo "<table border=0>\n";
    for ($loop=0; $loop < count($people); $loop++) {
      if ( ($people[$loop]["cat"] == "Equipe")  or ($people[$loop]["prof"]==1) ) {
        if ( ($people[$loop]["sexe"] == "F") ) {
          echo "<tr><td align=\"center\"><img src=\"images/prof_f_bleu.jpg\" alt=\"Professeur\" width=24 height=16 hspace=1 border=0></td>\n";
        } else {
           echo "<tr><td align=\"center\"><img src=\"images/prof_g_bleu.jpg\" alt=\"Professeur\" width=16 height=16 hspace=1 border=0></td>\n";
        }
      } else {
        if ($people[$loop]["sexe"]=="F") {
          echo "<tr><td><img src=\"images/eleve_f_bleu.jpg\" alt=\"El&egrave;ve\" width=16 height=16 hspace=3 border=0></td>\n";
        } else {
          echo "<tr><td><img src=\"images/eleve_g_bleu.jpg\" alt=\"El&egrave;ve\" width=16 height=16 hspace=3 border=0></td>\n";
        }
      }
      echo "<td><A href=\"people.php?uid=".$people[$loop]["uid"]."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/people.php"))."\">".$people[$loop]["fullname"]."</A>";
      if ( $people[$loop]["owner"] ) {
        echo "<strong><font size=\"-2\" color=\"#ff8f00\">&nbsp;&nbsp;(professeur principal)</font></strong>";
        $owner = $people[$loop]["uid"];
      }
      echo "</td></tr>\n";
    }
    echo "</table><BR>\n";
  } else {
    echo " <STRONG>Pas de membres</STRONG> dans le groupe $filter.<BR>";
  }

  // Modifi&#233; par Wawa
  // Affichage de l'equipe pedagogique associe &#224; la classe

  if (mb_ereg("Classe",$filter,$matche))
  {
    $filter2 = mb_ereg_replace("Classe_","Equipe_",$filter);
    $uids2 = search_uids ("(cn=".$filter2.")", "half");
    $people2 = search_people_groups ($uids2,"(sn=*)","cat");
    if (count($people2)) {
      // affichage des resultats
      echo "<BR><U>Professeurs de la classe</U> : <a href=\"group.php?filter=$filter2&jeton=".md5($_SESSION['token'].htmlentities("/Annu/group.php"))."\">$filter2</A><BR>\n";
      echo "<table border=0>\n";
      for ($loop=0; $loop < count($people2); $loop++) {
        if ($people2[$loop]["cat"] == "Equipe") {
          if ( ($people2[$loop]["sexe"] == "F") ) {
            echo "<tr><td align=\"center\"><img src=\"images/prof_f_bleu.jpg\" alt=\"Professeur\" width=24 height=16 hspace=1 border=0></td>\n";
          } else {
             echo "<tr><td align=\"center\"><img src=\"images/prof_g_bleu.jpg\" alt=\"Professeur\" width=16 height=16 hspace=1 border=0></td>\n";
          }
        }
        echo "<td><A href=\"people.php?uid=".$people2[$loop]["uid"]."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/people.php"))."\">".$people2[$loop]["fullname"]."</A>";
        echo "</td></tr>\n";
      }
      echo "</table><BR>\n";
    }
  }
  // Affichage du rebond sur la classe associee &#224; une equipe pdagogique

  if (mb_ereg("Equipe",$filter,$matche))
  {
    $filter2 = mb_ereg_replace("Equipe_","Classe_",$filter);
    $uids2 = search_uids ("(cn=".$filter2.")","half");
    $people2 = search_people_groups ($uids2,"(sn=*)","cat");
    if (count($people2)) {
      // affichage des resultats
      echo "<BR>Il y a ".count($people2)." &#233;l&#232;ves dans la <a href=\"group.php?filter=$filter2&jeton=".md5($_SESSION['token'].htmlentities("/Annu/group.php"))."\">$filter2</A> associ&#233;e &#224; cette &#233;quipe.\n";
      echo "<BR>\n";
    }
  }
  //Fin  Modifications de Wawa

  // Affichage menu admin
  if ( (is_admin("Annu_is_admin",$login) == "Y") && $filter!="lcs-users" ) {
    if ( $filter!="Eleves" && $filter!="Profs" && $filter!="Administratifs" ) {
      echo "<br>\n<ul style=\"color: red;\">\n";
      // Affichage du menu "Ajouter des membres" si le groupe est de type Equipe_ ou Classe_
      if (  mb_ereg ("Equipe_", $filter) || mb_ereg("Classe_", $filter) )
        echo "<li><a href=\"add_list_users_group.php?cn=$filter&jeton=".md5($_SESSION['token'].htmlentities("/Annu/add_list_users_group.php"))."\">Ajouter des membres</a></li>\n";
      if (count($people) )
        echo "<li><a href=\"del_user_group.php?cn=$filter&jeton=".md5($_SESSION['token'].htmlentities("/Annu/del_user_group.php"))."\">Enlever des membres</a></li>\n";
      echo "<li><a href=\"del_group.php?cn=$filter&jeton=".md5($_SESSION['token'].htmlentities("/Annu/del_group.php"))."\" onclick= \"return getconfirm();\">Supprimer ce groupe</a></li>\n";
      echo "<li><a href=\"mod_group_descrip.php?cn=$filter&jeton=".md5($_SESSION['token'].htmlentities("/Annu/mod_group_descrip.php"))."\">Modifier la description de ce groupe</a></li>\n";
      echo "<li><a href=\"grouplist.php?filter=$filter&jeton=".md5($_SESSION['token'].htmlentities("/Annu/grouplist.php"))."\" target='_new'>".gettext("Afficher un listing du groupe")."</a></li>\n";
    }
    if ($ad_auth_delegation == "true") {
	if (preg_match("/^Classe_/", $filter)) {
	    echo "<li><a href=\"delegate_auth_ad.php?groupcn=$filter&action=enable&verbose=1&jeton=".md5($_SESSION['token'].htmlentities("/Annu/delegate_auth_ad.php"))."\">Activer l'authentification d&eacute;port&eacute;e pour les &eacute;l&egrave;ves de ce groupe</a></li>";
	    echo "<li><a href=\"delegate_auth_ad.php?groupcn=$filter&action=disable&verbose=1&jeton=".md5($_SESSION['token'].htmlentities("/Annu/delegate_auth_ad.php"))."\">D&eacute;sactiver l'authentification d&eacute;port&eacute;e pour les &eacute;l&egrave;ves de ce groupe</a></li>";
	} else {
	    echo "<li><a href=\"delegate_auth_ad.php?groupcn=$filter&action=enable&verbose=1&all=1&jeton=".md5($_SESSION['token'].htmlentities("/Annu/delegate_auth_ad.php"))."\">Activer l'authentification d&eacute;port&eacute;e pour les membres de ce groupe</a></li>";
	    echo "<li><a href=\"delegate_auth_ad.php?groupcn=$filter&action=disable&verbose=1&all=1&jeton=".md5($_SESSION['token'].htmlentities("/Annu/delegate_auth_ad.php"))."\">D&eacute;sactiver l'authentification d&eacute;port&eacute;e pour les membres de ce groupe</a></li>";
	}
    }
    if (ldap_get_right("lcs_is_admin",$login) == "Y")
        // Affichage du menu "Deleguer un droit &#224; un groupe"
        echo "<li><a href=\"add_group_right.php?cn=$filter&jeton=".md5($_SESSION['token'].htmlentities("/Annu/add_group_right.php"))."\">D&#233;l&#233;guer un droit &#224; ce groupe</a></li>\n";
    echo "</ul>\n";
  } // Fin Affichage menu admin
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
