<?php
/* =============================================
   Projet LCS-SE3
   Consultation de l'annuaire LDAP
   Annu/group.php
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « wawa »  olivier.lecluse@crdp.ac-caen.fr
   Equipe Tice academie de Caen
   Derniere modification : 21/05/2010
   Distribue selon les termes de la licence GPL
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  
  $filter=$_GET['filter'];
  
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
      echo "<td><A href=\"people.php?uid=".$people[$loop]["uid"]."\">".$people[$loop]["fullname"]."</A>";
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

  if (ereg("Classe",$filter,$matche))
  {
    $filter2 = ereg_replace("Classe_","Equipe_",$filter);
    $uids2 = search_uids ("(cn=".$filter2.")", "half");
    $people2 = search_people_groups ($uids2,"(sn=*)","cat");
    if (count($people2)) {
      // affichage des resultats
      echo "<BR><U>Professeurs de la classe</U> : <a href=\"group.php?filter=$filter2\">$filter2</A><BR>\n";
      echo "<table border=0>\n";
      for ($loop=0; $loop < count($people2); $loop++) {
        if ($people2[$loop]["cat"] == "Equipe") {
          if ( ($people2[$loop]["sexe"] == "F") ) {
            echo "<tr><td align=\"center\"><img src=\"images/prof_f_bleu.jpg\" alt=\"Professeur\" width=24 height=16 hspace=1 border=0></td>\n";
          } else {
             echo "<tr><td align=\"center\"><img src=\"images/prof_g_bleu.jpg\" alt=\"Professeur\" width=16 height=16 hspace=1 border=0></td>\n";
          }
        }
        echo "<td><A href=\"people.php?uid=".$people2[$loop]["uid"]."\">".$people2[$loop]["fullname"]."</A>";
        echo "</td></tr>\n";
      }
      echo "</table><BR>\n";
    }
  }
  // Affichage du rebond sur la classe associee &#224; une equipe pdagogique

  if (ereg("Equipe",$filter,$matche))
  {
    $filter2 = ereg_replace("Equipe_","Classe_",$filter);
    $uids2 = search_uids ("(cn=".$filter2.")","half");
    $people2 = search_people_groups ($uids2,"(sn=*)","cat");
    if (count($people2)) {
      // affichage des resultats
      echo "<BR>Il y a ".count($people2)." &#233;l&#232;ves dans la <a href=\"group.php?filter=$filter2\">$filter2</A> associ&#233;e &#224; cette &#233;quipe.\n";
      echo "<BR>\n";
    }
  }
  //Fin  Modifications de Wawa

  // Affichage menu admin
  if ( (is_admin("Annu_is_admin",$login) == "Y") && $filter!="lcs-users" ) {
    if ( $filter!="Eleves" && $filter!="Profs" && $filter!="Administratifs" ) {
      echo "<br>\n<ul style=\"color: red;\">\n";
      // Affichage du menu "Ajouter des membres" si le groupe est de type Equipe_ ou Classe_
      if (  ereg ("Equipe_", $filter) || ereg("Classe_", $filter) )
        echo "<li><a href=\"add_list_users_group.php?cn=$filter\">Ajouter des membres</a></li>\n";
      if (count($people) )
        echo "<li><a href=\"del_user_group.php?cn=$filter\">Enlever des membres</a></li>\n";
      echo "<li><a href=\"del_group.php?cn=$filter\" onclick= \"return getconfirm();\">Supprimer ce groupe</a></li>\n";
      echo "<li><a href=\"mod_group_descrip.php?cn=$filter\">Modifier la description de ce groupe</a></li>\n";
      echo "<li><a href=\"grouplist.php?filter=$filter\" target='_new'>".gettext("Afficher un listing du groupe")."</a></li>\n";
    }
    if (ldap_get_right("lcs_is_admin",$login) == "Y")
        // Affichage du menu "Deleguer un droit &#224; un groupe"
        echo "<li><a href=\"add_group_right.php?cn=$filter\">D&#233;l&#233;guer un droit &#224; ce groupe</a></li>\n";
    echo "</ul>\n";
  } // Fin Affichage menu admin
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
