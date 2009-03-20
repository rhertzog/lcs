<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/add_user_group.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.4 maj : 20/03/2009
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";
  
  $new_categorie=$_POST['new_categorie'];
  $classe_gr=$_POST['classe_gr'];
  $matiere_gr=$_POST['matiere_gr'];
  $cours_gr=$_POST['cours_gr'];
  $equipe_gr=$_POST['equipe_gr'];
  $autres_gr=$_POST['autres_gr'];
  $categorie=$_POST['categorie'];
  $add_user_group=$_POST['add_user_group'];  
  

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  header_html();
  aff_trailer ("31");

   if (is_admin("Annu_is_admin",$login)=="Y") {
    if ( !$add_user_group ) {
      // Ajout de groupes
      list($user, $groups)=people_get_variables($uid, true);
      // Affichage du nom et de la description de l'utilisateur
      echo "<H2>".$user["fullname"]."</H2>\n";
      if ($user["description"]) echo $user["description"]."<BR>";

      // Recherche si le user appartient a une categorie principale
      if ( count($groups) ) {
        for ($loop=0; $loop < count ($groups) ; $loop++) {
          if ( ($groups[$loop]["cn"] == "Profs") || ($groups[$loop]["cn"] == "Eleves")|| ($groups[$loop]["cn"] == "Administratifs") ) {
            $categorie =  $groups[$loop]["cn"];
          }
        }
      }
      // Affichage boite de réaffectation du groupe principal
      if ( $categorie ) {
      	$html = "   <form action='add_user_group.php?uid=$uid' method='post'>\n";
      	$html .= "<table>\n";
        $html .= " <tr>\n";
        $html .= " <td><u>Membre de la cat&#233;gorie</u> :&nbsp;</td>\n";
        $html .= " <td>\n";
        
        $html .= "     <select name='new_categorie'>\n";
        if ($categorie == "Administratifs" ) {
        	$html .= "      <option>Administratifs</option>\n";
                $html .= "      <option>Profs</option>\n";
                $html .= "      <option>Eleves</option>\n";
        } elseif ($categorie == "Profs" ) {
          	$html .= "      <option>Profs</option>\n";
                $html .= "      <option>Administratifs</option>\n";
                $html .= "      <option>Eleves</option>\n";
        } else {
          	$html .= "      <option>Eleves</option>\n";
                $html .= "      <option>Profs</option>\n";
                $html .= "      <option>Administratifs</option>\n";
        }
        $html .= "     </select>\n";
        $html .= " </td>\n";
        $html .= " </tr>\n";
        $html .= "</table>\n<br>\n";
	echo $html;     
      } else {
      // Affichage du menu d'affectation de l'utilisateur a une categorie principal
      	$html = "    <form action='add_user_group.php?uid=$uid' method='post'>\n";
        $html.= "<table>\n <tr>\n";
        $html .= "  <td><u>Affectation de l'utilisateur &#224; une cat&#233;gorie </u> :&nbsp;</td>\n";
        $html .= "  <td>\n";
        
        $html .= "      <select name='new_categorie'>\n";
	$html .= "            <option>Eleves</option>\n";
	$html .= "            <option>Profs</option>\n";
        $html .= "            <option>Administratifs</option>\n";
        $html .= "      </select>\n";
        $html .= "  </td>\n";
        $html .= " </tr>\n";
	$html .= "</table>\n<br>\n";
	echo $html;
      }
      // Affichage des groupes secondaires
      if ( count($groups) > 1  ) {
        $html = "<P><U>Membre des groupes secondaires</U> :\n<UL>\n";
        for ( $loop=0; $loop < count ($groups) ; $loop++ ) {
          if ( ($groups[$loop]["cn"] != "Profs") && ($groups[$loop]["cn"] != "Eleves") && ($groups[$loop]["cn"] != "Administratifs") ) {
            $html.= "  <LI><A href=\"group.php?filter=".$groups[$loop]["cn"]."\">".$groups[$loop]["cn"]."</A>,<font size=\"-2\"> ".$groups[$loop]["description"];
            $login=split ("[\,\]",ldap_dn2ufn($groups[$loop]["owner"]),2);
            if ( $login[0] == $uid ) $html .= "<strong><font color=\"#ff8f00\">&nbsp;(professeur principal)</font></strong>";
            $html .= "</font></LI>\n";
            // constitution d'un filtre pour exclure les groupes d'appartenance
            // de la liste des groupes proposés
            $filter = $filter."(!(cn=".$groups[$loop]["cn"]."))";
          }
        }
        $html .= "</UL>\n";
	echo $html;
      }
      if ( $categorie ) {
         // Etablissement des listes des groupes disponibles
         $list_groups=search_groups("(&(cn=*) $filter )");
         // Etablissement des sous listes de groupes :
         $i = 0; $j =0; $k =0; $l = 0 ; $m = 0;
         for ($loop=0; $loop < count ($list_groups) ; $loop++) {
            // Cours
            if ( ereg ("Cours_", $list_groups[$loop]["cn"]) ) {
               $cours[$i]["cn"] = $list_groups[$loop]["cn"];
               $cours[$i]["description"] = $list_groups[$loop]["description"];
               $i++;
              // Classe
            } elseif ( ereg ("Classe_", $list_groups[$loop]["cn"]) ) {
               $classe[$j]["cn"] = $list_groups[$loop]["cn"];
               $classe[$j]["description"] = $list_groups[$loop]["description"];
               $j++;
               // Equipe
            } elseif ( ereg ("Equipe_", $list_groups[$loop]["cn"]) ) {
               $equipe[$k]["cn"] = $list_groups[$loop]["cn"];
               $equipe[$k]["description"] = $list_groups[$loop]["description"];
               $k++;
               // Matiere
            } elseif ( ereg ("Matiere_", $list_groups[$loop]["cn"]) ) {
               $matiere[$l]["cn"] = $list_groups[$loop]["cn"];
               $matiere[$l]["description"] = $list_groups[$loop]["description"];
               $l++;
               // Autres
	     } elseif ( !ereg( "^(Administratifs)|(Eleves)|(lcs-users)|(machines)|(overfil)|(Profs)$",$list_groups[$loop]["cn"] ) ) {		   
               $autres[$m]["cn"] = $list_groups[$loop]["cn"];
               $autres[$m]["description"] = $list_groups[$loop]["description"];
               $m++;
             }
         }
         // Affichage des boites de sélection des nouveaux groupes secondaires
      	 $html = "<h4>Ajouter aux groupes secondaires :</h4>\n";
         $html .= "  <table border='0' cellspacing='10'>\n";
         $html .= "  <thead>\n";
         $html .= "    <tr>\n";
         if ( $categorie == "Eleves" ) $html .= "<td>Classes</td>\n";
	 else $html .= "      <td>Matieres</td>\n";
         $html .= "      <td>Cours</td>\n";
         if ( $categorie != "Eleves" ) $html .= "      <td>Equipes</td>\n";
         $html .= "      <td>Autres</td>\n";
         $html .= "    </tr>\n";
         $html .= "  </thead>\n";
         $html .= "  <tbody>\n";
         $html .= "    <tr>\n";
         $html .= "      <td valign='top'>\n";
         if ( $categorie == "Eleves" ) {
            $html .= "        <select name='classe_gr[]' size='10' multiple='multiple'>\n";
	     for ($loop=0; $loop < count ($classe) ; $loop++)
               $html .= "          <option value='".$classe[$loop]["cn"]."'>".$classe[$loop]["cn"]."\n";
         } else {
               $html .= "        <select name= 'matiere_gr[]' size='10' multiple='multiple'>\n";
               for ($loop=0; $loop < count ($matiere) ; $loop++)
                  $html .= "          <option value='".$matiere[$loop]["cn"]."'>".$matiere[$loop]["cn"]."\n";
         }
         $html .= "        </select>\n";
         $html .= "      </td>\n";
         $html .= "      <td valign='top'>\n";
         $html .= "        <select name= 'cours_gr[]' size='10' multiple='multiple'>\n";
         for ($loop=0; $loop < count ($cours) ; $loop++)
            $html .= "          <option value='".$cours[$loop]["cn"]."'>".$cours[$loop]["cn"]."\n";
         $html .= "        </select>\n";
         $html .= "      </td>\n";
         if ( $categorie == "Profs" || $categorie == "Administratifs" || !$categorie) {
            $html .= "      <td>\n";
	    $html .= "        <select name= 'equipe_gr[]' size='10' multiple='multiple'>\n";
            for ($loop=0; $loop < count ($equipe) ; $loop++)
               $html .= "        <option value='".$equipe[$loop]["cn"]."'>".$equipe[$loop]["cn"]."\n";
            $html .= "      </select></td>\n";
         }
         $html .= "      <td valign='top'>\n";
         $html .= "        <select name= 'autres_gr[]' size='5' multiple='multiple'>\n";
         for ($loop=0; $loop < count ($autres) ; $loop++)
            $html .= "          <option value='".$autres[$loop]["cn"]."'>".$autres[$loop]["cn"]."\n";
         $html .= "      </select>\n";
         $html .= "      </td>\n";
         $html .= "    </tr>\n";
         $html .= "    <tr>\n";
         $html .= "      <td>\n";
         $html .= "        <input type='reset' value='R&#233;initialiser la s&#233;lection'>\n";
         $html .= "      </td>\n";
      } else {
         $html = "<table>\n";
         $html .= "  <tbody>\n";
         $html .= "    <tr>\n";  	
      }	
      $html .= "      <td >\n";
      $html .= "        <input type='hidden' name='categorie' value='$categorie'>\n";
      $html .= "        <input type='hidden' name='add_user_group' value='true'>\n";
      $html .= "        <input type='submit' value='Lancer la requ&#234;te'>\n";
      $html .= "      </td>\n";
      $html .= "      <td></td>\n";
      $html .= "    </tr>\n";
      $html .= "  </tbody>\n";
      $html .= "</table>\n";
      $html .= "</form>\n";
      echo $html;
    } else {
      // Reaffectation de l'utilisateur dans une nouvelle categorie
      if ( $categorie && ($categorie !=  $new_categorie) ) {
        // Suppression de l'utilisateur de la categorie $categorie
        exec ("$scriptsbinpath/groupDelUser.pl $uid $categorie",$AllOutPut,$ReturnValue0);
        // Affectation de l'utilisateur à la categorie $new_categorie
        exec("$scriptsbinpath/groupAddUser.pl $uid $new_categorie" ,$AllOutPut,$ReturnValue1);
        if ( $ReturnValue1==0 && $ReturnValue1==0) {
          echo "L'utilisateur <strong>$uid</strong> a &#233;t&#233; r&#233;affect&#233; de la cat&#233;gorie <b>$categorie</b> &#224; la cat&#233;gorie <b>$new_categorie</b>.</BR></BR>\n";
        } else {
          echo "<div class=error_msg>
                    La r&#233;affectation de cat&#233;gorie $categorie vers $new_categorie de l'utilisateur
                    <font color='#0080ff'>$uid</font> a &#233;chou&#233;
                    , veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB Reaffectation categorie $categorie vers $new_categorie de $uid'>l'administrateur du syst&#232;me</A>
                </div><BR>\n";
        }
      } elseif ( !$categorie && $new_categorie ) {
        exec("$scriptsbinpath/groupAddUser.pl $uid $new_categorie" ,$AllOutPut,$ReturnValue);
        if ( $ReturnValue==0 ) {
          echo "L'utilisateur <strong>$uid</strong> a &#233;t&#233; affect&#233; &#224; la cat&#233;gorie <b>$new_categorie</b>.</BR></BR>\n";
        } else {
          echo "<div class=error_msg>
                    L'affectation &#224; la cat&#233;gorie $new_categorie de l'utilisateur
                    <font color='#0080ff'>$uid</font> a &#233;chou&#233;
                    , veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB Affectation categorie $new_categorie de $uid'>l'administrateur du syst&#232;me</A>
                </div><BR>\n";
        }
      }
      // Ajout des groupes secondaires
      // Message d'information
      echo "Ajout de l'utilisateur <a href='people.php?uid=$uid'>$uid</a> ";
      if (count($classe_gr) > 0 || count($matiere_gr) > 0 || count($cours_gr) > 0  || count($equipe_gr) > 0 || count($autres_gr) > 0 )
      	echo "dans les <a href='add_user_group.php?uid=$uid'>groupes secondaires</a> :<BR>";
      else echo "dans aucun <a href='add_user_group.php?uid=$uid'>groupe secondaire</a>.<BR>";
      // Classe
      if (count($classe_gr) ) {
        for ($loop=0; $loop < count ($classe_gr) ; $loop++) {
          exec("$scriptsbinpath/groupAddUser.pl $uid $classe_gr[$loop]" ,$AllOutPut,$ReturnValue);
          echo $classe_gr[$loop]."&nbsp;";
          if ($ReturnValue == 0 ) {
            echo "<stong><strong>R&#233;ussi</strong></strong><BR>";
          } else { echo "<font color=\"orange\">Echec</font><BR>"; $err++; }
        }
      }
      // Matiere
      if (count($matiere_gr) ) {
        for ($loop=0; $loop < count ($matiere_gr) ; $loop++) {
          exec("$scriptsbinpath/groupAddUser.pl $uid $matiere_gr[$loop]" ,$AllOutPut,$ReturnValue);
          echo $matiere_gr[$loop]."&nbsp;";
          if ($ReturnValue == 0 ) {
            echo "<strong>R&#233;ussi</strong><BR>";
          } else { echo "</strong><font color=\"orange\">Echec</font></strong><BR>"; $err++; }
        }
      }
      // Cours
      if (count($cours_gr) ) {
        for ($loop=0; $loop < count ($cours_gr) ; $loop++) {
          exec("$scriptsbinpath/groupAddUser.pl $uid $cours_gr[$loop]" ,$AllOutPut,$ReturnValue);
          echo $cours_gr[$loop]."&nbsp;";
          if ($ReturnValue == 0 ) {
            echo "<strong>R&#233;ussi</strong><BR>";
          } else { echo "</strong><font color=\"orange\">Echec</font></strong><BR>"; $err++; }
        }
      }
      // Equipe
      if (count($equipe_gr) ) {
        for ($loop=0; $loop < count ($equipe_gr) ; $loop++) {
          exec("$scriptsbinpath/groupAddUser.pl $uid $equipe_gr[$loop]" ,$AllOutPut,$ReturnValue);
          echo $equipe_gr[$loop]."&nbsp;";
          if ($ReturnValue == 0 ) {
            echo "<strong>R&#233;ussi</strong><BR>";
          } else { echo "</strong><font color=\"orange\">Echec</font></strong><BR>"; $err++; }
        }
      }
      // Autres
      if (count($autres_gr) ) {
        for ($loop=0; $loop < count ($autres_gr) ; $loop++) {
          exec("$scriptsbinpath/groupAddUser.pl $uid $autres_gr[$loop]" ,$AllOutPut,$ReturnValue);
          echo $autres_gr[$loop]."&nbsp;";
          if ($ReturnValue == 0 ) {
            echo "<strong>R&#233;ussi</strong><BR>";
          } else { echo "</strong><font color=\"orange\">Echec</font></strong><BR>"; $err++; }
        }
      }

      // Compte rendu
      if ($err ) {
        echo "<div class=error_msg>
                Veuillez contacter <A HREF='mailto:$MelAdminLCS?subject=PB Affectation de $uid a des groupes secondaires !'>l'administrateur du syst&#232;me</A>
              </div><BR>\n";
      }
    }
  } else {
    echo "<div class=error_msg>Cette application, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
  }
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
