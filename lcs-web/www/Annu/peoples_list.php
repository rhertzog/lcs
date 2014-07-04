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

include "../lcs/includes/headerauth.inc.php";
include "includes/ldap.inc.php";
include "includes/ihm.inc.php";

if ( count($_POST)>0 ) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
  	$nom=$purifier->purify($_POST['nom']);
  	$prenom=$purifier->purify($_POST['prenom']);
  	$priority_name=$purifier->purify($_POST['priority_name']);
  	$priority_surname=$purifier->purify($_POST['priority_surname']);
  	$priority_classe=$purifier->purify($_POST['priority_classe']);
  	$classe=$purifier->purify($_POST['classe']);
}

  header_html();
  // Construction du filtre de la branche people
  if ($nom && !$prenom) {
    // Recherche sur sn
    if ($priority_name=="contient") {
      $filter_people="(sn=*$nom*)";
    } elseif($priority_name=="commence") {
      $filter_people="(sn=$nom*)";
    } else {
      $filter_people="(sn=*$nom)";
    }
  } elseif ($prenom && !$nom) {
    // Recherche sur cn
    if ($priority_surname=="contient") {
      $filter_people="(cn=*$prenom*)";
    } elseif($priority_surname=="commence") {
      $filter_people="(cn=$prenom*)";
    } else {
      $filter_people="(cn=*$prenom)";
    }
  } elseif ($prenom && $nom) {
    // Recherche sur sn ET cn
    if ($priority_name=="contient") {
      if ($priority_surname=="contient") {
        $filter_people="(&(sn=*$nom*)(cn=*$prenom*))";
      } elseif($priority_surname=="commence") {
        $filter_people="(&(sn=*$nom*)(cn=$prenom*))";
      } else {
        $filter_people="(&(sn=*$nom*)(cn=*$prenom))";
      }

    } elseif($priority_name=="commence") {
      if ($priority_surname=="contient") {
        $filter_people="(&(sn=$nom*)(cn=*$prenom*))";
      } elseif($priority_surname=="commence") {
        $filter_people="(&(sn=$nom*)(cn=$prenom*))";
      } else {
        $filter_people="(&(sn=$nom*)(cn=*$prenom))";
      }
    } else {
      if ($priority_surname=="contient") {
        $filter_people="(&(sn=*$nom)(cn=*$prenom*))";
      } elseif($priority_surname=="commence") {
        $filter_people="(&(sn=*$nom)(cn=$prenom*))";
      } else {
        $filter_people="(&(sn=*$nom)(cn=*$prenom))";
      }
    }
  }
  // Remplacement de *** ou ** par *
  $filter_people = mb_ereg_replace("\*\*\*","*",$filter_people);
  $filter_people = mb_ereg_replace("\*\*","*",$filter_people);
  if ($filter_people && !$classe) {
    // recherche dans la branche People
    #$TimeStamp_0=microtime();
    $users = search_people ($filter_people);
    #$TimeStamp_1=microtime();

    // Affichage menu haut de page
    aff_trailer("3");
    #############
    # DEBUG     #
    #############
    #echo "<u>debug</u> :Temps de recherche = ".duree($TimeStamp_0,$TimeStamp_1)."&nbsp;s<BR>";
    #############
    # Fin DEBUG #
    #############
    if (count($users)) {
      if (count($users)==1) {
        echo "<p><STRONG>".count($users)."</STRONG> utilisateur r&#233;pond &#224; ces crit&#232;res de recherche</p>\n";
      } else {
        echo "<p><STRONG>".count($users)."</STRONG> utilisateurs r&#233;pondent &#224; ces crit&#232;res de recherche</p>\n";
      }

      echo "<UL>\n";
      for ($loop=0; $loop<count($users);$loop++) {
        echo "<LI><A href=\"people.php?uid=".$users[$loop]["uid"]."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/people.php"))."\">".$users[$loop]["fullname"]."</A></LI>\n";
      }
      echo "</UL>\n";
    } else {
        echo " <STRONG>Pas de r&#233;sultats</STRONG> correspondant aux crit&#232;res s&#233;lectionn&#233;s.<BR> Retour au <A href=\"search.php\">formulaire de recherche</A><BR>\n";
    }

  } elseif ($classe) {
       // Recherche des classes et �quipes dans la branche groups de l'annuaire
       if ($priority_classe=="contient") {
         $filter_classe="(cn=Classe_*$classe*)";
       } elseif($priority_classe=="commence") {
          $filter_classe="(cn=Classe_$classe*)";
       } else {
         $filter_classe="(cn=Classe_*$classe)";
       }
       // Remplacement de *** ou ** par *
       $filter_classe = mb_ereg_replace("\*\*\*","*",$filter_classe);
       $filter_classe = mb_ereg_replace("\*\*","*",$filter_classe);
       $TimeStamp_0=microtime();
       $uids = search_uids ($filter_classe, "full");
       $people = search_people_groups ($uids,$filter_people,"group");
       $TimeStamp_1=microtime();
       // Affichage menu haut de page
       aff_trailer("3");
       #############
       # DEBUG     #
       #############
       #echo "<u>debug</u> :Temps de recherche = ".duree($TimeStamp_0,$TimeStamp_1)."&nbsp;s<BR>";
       #############
       # Fin DEBUG #
       #############
       if (count($people)) {
         if (count($people)==1) {
           echo "<p><STRONG>".count($people)."</STRONG> utilisateur r&#233;pond &#224; ces crit&#232;res de recherche.</p>\n";
         } else {
           echo "<p><STRONG>".count($people)."</STRONG> utilisateurs r&#233;pondent &#224; ces crit&#232;res de recherche.</p>\n";
         }
         // affichage des r�sultats
         echo "<table border=\"0\">\n";
         for ($loop=0; $loop < count($people); $loop++) {
           if ( $people[$loop]["group"] != $people[$loop-1]["group"]) {
             echo "<tr><td colspan=2><U>Classe</U> : ".$people[$loop]["group"]."</td></tr>\n";
           }

           if ($people[$loop]["cat"] == "Equipe") {
             if ( ($people[$loop]["sexe"] == "F") ) {
               echo "<tr><td align=\"center\"><img src=\"images/prof_f_bleu.jpg\" alt=\"Professeur\" width=24 height=16 hspace=1 border=0></td>\n";
             } else {
               echo "<tr><td align=\"center\"><img src=\"images/prof_g_bleu.jpg\" alt=\"Professeur\" width=16 height=16 hspace=1 border=0></td>\n";
             }
           } else {
             if ($people[$loop]["sexe"]=="F") {
               echo "<tr><td><img src=\"images/eleve_f_bleu.jpg\" width=16 height=16 hspace=3 border=0></td>\n";
             } else {
               echo "<tr><td><img src=\"images/eleve_g_bleu.jpg\" width=16 height=16 hspace=3 border=0></td>\n";
             }
           }
           echo "<td><A href=\"people.php?uid=".$people[$loop]["uid"]."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/people.php"))."\">".$people[$loop]["fullname"]."</A></td></tr>\n";
         }
         echo "</table>\n";
       } else {
           echo " <STRONG>Pas de r&#233;sultats</STRONG> correspondant aux crit&#232;res s&#233;lectionn&#233;s.<BR> Retour au <A href=\"search.php\">formulaire de recherche</A>...<BR>\n";
       }
  } else {
       // Aucun crit�res de recherche
       echo " <STRONG>Pas de r&#233;sultats !</STRONG><BR>
       Veuillez compl&#233;ter au moins l'un des trois champs (nom, pr&#233;nom, classe) du <A href=\"search.php\">formulaire de recherche</A> !<BR>\n";
  }


  include ("../lcs/includes/pieds_de_page.inc.php");
?>
