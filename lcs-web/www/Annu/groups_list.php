<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 22/05/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;
$login=$_SESSION['login'];
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";


if (count($_POST)>0) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
                  //purification des variables
 	 if (isset($_POST['priority_group'])) $priority_group=$purifier->purify($_POST['priority_group']);
                   if (isset($_POST['group'])) $group=$purifier->purify($_POST['group']);
  }

@//test si un webmail est installe pour mailto vers les groupes
  $query="SELECT value from applis where name='squirrelmail' or name='roundcube'";
  $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
  if ($result)
	{
          if ( mysqli_num_rows($result) !=0 ) {
          $r=mysqli_fetch_object($result);
          $test_squir=$r->value;
          }
          else $test_squir="0";
          }
          else $test_squir="0";
   //fin test webmail
//test listes de diffusion
	exec ("/bin/grep \"#<listediffusionldap>\" /etc/postfix/mailing_list.cf", $AllOutPut, $ReturnValueShareName);
    $listediff = 0;
    if ( count($AllOutPut) >= 1) $listediff = 1;
//
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
  $filter=mb_ereg_replace("\*\*\*","*",$filter);
  $filter=mb_ereg_replace("\*\*","*",$filter);
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
  // affichage de la liste des groupes trouv�s
  if (count($groups)) {
    if (count($groups)==1) {
      echo "<p><STRONG>".count($groups)."</STRONG> groupe r&#233;pond &#224; ces crit&#232;res de recherche</p>\n";
    } else {
      echo "<p><STRONG>".count($groups)."</STRONG> groupes r&#233;pondent &#224; ces crit&#232;res de recherche</p>\n";
    }
    echo "<UL>\n";
    /*
    for ($loop=0; $loop < count($groups); $loop++) {
      echo "<LI><A href=\"group.php?filter=".$groups[$loop]["cn"]."\">".$groups[$loop]["cn"]."</A>&nbsp;&nbsp;&nbsp;<font size=\"-2\">".$groups[$loop]["description"]."</font></LI>\n";
    }
    */
    for ($loop=0; $loop < count($groups); $loop++) {
        echo "<LI><A href=\"group.php?filter=".$groups[$loop]["cn"]."&jeton=".md5($_SESSION['token'].htmlentities("/Annu/group.php"))."\">";
        /*
        if ($groups[$loop]["type"]=="posixGroup")
          echo "<STRONG>".$groups[$loop]["cn"]."</STRONG>";
        else
        */
        echo $groups[$loop]["cn"];
        echo "</A>&nbsp;&nbsp;&nbsp;<font size=\"-2\">".$groups[$loop]["description"]."</font>";
         if (! is_eleve($login) && $listediff && $test_squir=="1")
         echo "<a href=\"mailto:".$groups[$loop]["cn"]."@".$domain."\" >  <img src=\"images/mail.png\" alt=\"Envoyer un mail\"
         title=\"Envoyer un mail &#224; ce groupe\" border=0 ></a><br>\n</LI>\n";
    }

    echo "</UL>\n";
  } else {
    echo "<STRONG>Pas de r&#233;sultats</STRONG> correspondant aux crit&#232;res s&#233;lectionn&#233;s.<BR>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
