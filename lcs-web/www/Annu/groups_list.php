<?php
/* =============================================
   Projet LCS-SE3
   Consultation de l'annuaire LDAP
   Annu/groups_list.php
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   « wawa »  olivier.lecluse@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   Derniere mise à jour  : 07/05/2007
   Distribué selon les termes de la licence GPL
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  header_html();
  aff_trailer ("3");

  if (!$group) {
    $filter = "(cn=*)";
  } else {
    if ($priority_group == "contient") {
      $filter = "(cn=*$group*)";	
    } elseif ($priority_group == "commence") {
      $filter = "(|(cn=Classe_$group*)(cn=Cours_$group*)(cn=Equipe_$group*)(cn=Matiere_$group*)(cn=$group*))";
    } else {
      // finit par
      $filter = "(|(cn=Classe_*$group)(cn=Cours_*$group)(cn=Equipe_*$group)(cn=Matiere_*$group)(cn=*$group))";	
    }
  }
  $filter=ereg_replace("\*\*\*","*",$filter);
  $filter=ereg_replace("\*\*","*",$filter);
  #$TimeStamp_0=microtime();
  $groups=search_groups($filter);
  #$TimeStamp_1=microtime();
  #############
  # DEBUG     #
  #############
  #echo "<u>debug</u> :Temps de recherche = ".duree($TimeStamp_0,$TimeStamp_1)."&nbsp;s<BR>";
  #############
  # Fin DEBUG #
  #############
  // affichage de la liste des groupes trouvés
  if (count($groups)) {
    if (count($groups)==1) {
      echo "<p><STRONG>".count($groups)."</STRONG> groupe répond à ces critères de recherche</p>\n";
    } else {
      echo "<p><STRONG>".count($groups)."</STRONG> groupes répondent à ces critères de recherche</p>\n";
    }
    echo "<UL>\n";
    /*
    for ($loop=0; $loop < count($groups); $loop++) {
      echo "<LI><A href=\"group.php?filter=".$groups[$loop]["cn"]."\">".$groups[$loop]["cn"]."</A>&nbsp;&nbsp;&nbsp;<font size=\"-2\">".$groups[$loop]["description"]."</font></LI>\n";
    }
    */
    for ($loop=0; $loop < count($groups); $loop++) {
        echo "<LI><A href=\"group.php?filter=".$groups[$loop]["cn"]."\">";
        /*
        if ($groups[$loop]["type"]=="posixGroup")
          echo "<STRONG>".$groups[$loop]["cn"]."</STRONG>";
        else
        */
        echo $groups[$loop]["cn"];
        echo "</A>&nbsp;&nbsp;&nbsp;<font size=\"-2\">".$groups[$loop]["description"]."</font></LI>\n";
    }

    echo "</UL>\n";
  } else {
    echo "<STRONG>Pas de résultats</STRONG> correspondant aux critères sélectionnés.<BR>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
