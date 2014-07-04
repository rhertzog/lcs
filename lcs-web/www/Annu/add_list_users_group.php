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


if ( count($_GET)>0 || count($_POST)>0 ) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
	if ( count($_POST['new_uids'])>0 ) $new_uids=$purifier->purifyArray($_POST['new_uids']);
	if ( isset($_POST['cn']))  $cn = $purifier->purify($_POST['cn']);
	elseif ( isset($_GET['cn'])) $cn = $purifier->purify($_GET['cn']);
	if ( isset($_POST['add_list_users_group'])) $add_list_users_group=$purifier->purify($_POST['add_list_users_group']);
}


  header_html();
  aff_trailer ("31");
   if (is_admin("Annu_is_admin",$login)=="Y") {

    if ( !$add_list_users_group ) {
      echo "<H4>Ajouter des membres au groupe : $cn</H4>\n";
      // cas d'un groupe de type Equipe
      if ( mb_ereg ("Equipe_", $cn) ) {
        // Recherche de la liste des uid  des membres de ce groupe
        $uids_act = search_uids ("(cn=$cn)","half");
        // Reherche de la liste des professeurs
        $uids_profs = search_uids ("(cn=Profs)","half");
        // Constitution d'un tableau excluant les membres actuels
        $k=0;
        for ($i=0; $i < count($uids_profs); $i++ ) {
            for ($j=0; $j < count($uids_act); $j++ ) {
              if ( $uids_profs[$i]["uid"] == $uids_act[$j]["uid"] )  {
                $exist = true;
                break;
              } else { $exist = false; }
            }
            if (!$exist) {
              $uids_new_members[$k]["uid"] = $uids_profs[$i]["uid"];
              $k++;
            }
        }
         $people_new_members=search_people_groups ($uids_new_members,"(sn=*)","cat");
      } elseif   ( mb_ereg ("Classe_", $cn) ) {
        // Recherche de la liste des Eleves appartenant a une classe
        $uids_eleves_classes =   search_uids ("(cn=Classe_*)", "half");

        // Recherche de la liste des Eleves
        $uids_eleves = search_uids ("(cn=Eleves)", "half");

        // Recherche des Eleves qui ne sont pas affect&#233;s &#224; une classe
        $k=0;
        for ($i=0; $i < count($uids_eleves); $i++ ) {
          $affect = false;
          for ($j=0; $j < count($uids_eleves_classes); $j++ ) {
            if ( $uids_eleves[$i]["uid"] == $uids_eleves_classes[$j]["uid"] ) {
              $affect = true;
              break;
            }
          }
            if ($affect==false )  {
                $uids_eleves_no_affect[$k]["uid"]=$uids_eleves[$i]["uid"];
                $k++;
            }
        }
        $people_new_members = search_people_groups ($uids_eleves_no_affect,"(sn=*)","cat");
              }
      // Affichage de la liste dans une boite de s&#233;lection
      if   ( count($people_new_members)>15) $size=15; else $size=count($people_new_members);
      if ( count($people_new_members)>0) {
        $form = "<form action=\"add_list_users_group.php\" method=\"post\">\n";
        $form.="<p>S&#233;lectionnez les membres &#224; ajouter au groupe :</p>\n";
        $form.="<p><select size=\"".$size."\" name=\"new_uids[]\" multiple=\"multiple\">\n";
        echo $form;
        for ($loop=0; $loop < count($people_new_members); $loop++) {
          echo "<option value=".$people_new_members[$loop]["uid"].">".$people_new_members[$loop]["fullname"];
         }
        $form="</select></p>\n";
        $form.="<input type=\"hidden\" name=\"cn\" value=\"$cn\">\n";
         $form.='<input name="jeton" type="hidden"  value="'.md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])).'" />';
        $form.="<input type=\"hidden\" name=\"add_list_users_group\" value=\"true\">\n";
        $form.="<input type=\"reset\" value=\"R&#233;initialiser la s&#233;lection\">\n";
        $form.="<input type=\"submit\" value=\"Valider\">\n";
        $form.="</form>\n";
        echo $form;
      } else {
        echo "<font color=\"orange\">Vous ne pouvez pas ajouter d'&#233;l&#232;ves car il n'existe plus d'&#233;l&#232;ves non affect&#233;s &#224; des classes !!</font><BR>";
      }
    }   else {
      // Ajout des membres au groupe
       echo "<H4>Ajout des membres au groupe : <A href=\"group.php?filter=$cn&jeton=".md5($_SESSION['token'].htmlentities("/Annu/group.php"))."\">$cn</A></H4>\n";
       for ($loop=0; $loop < count ($new_uids) ; $loop++) {
          exec("$scriptsbinpath/groupAddUser.pl ". escapeshellarg($new_uids[$loop]) ." ". escapeshellarg($cn) ,$AllOutPut,$ReturnValue);
          echo  "Ajout de l'utilisateur&nbsp;".$new_uids[$loop]."&nbsp;";
          if ($ReturnValue == 0 ) {
            echo "<strong>R&#233;ussi</strong><BR>";
          } else { echo "</strong><font color=\"orange\">Echec</font></strong><BR>"; $err++; }
       }
    }
  } else {
    echo "<div class=error_msg>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
