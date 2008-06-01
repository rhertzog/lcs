<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/add_group.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   Dernière modification : 7/05/07
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers,$login)= isauth();
  if ($idpers == "0") header("Location:$urlauth");

  header_html();
  aff_trailer ("6");
   if (is_admin("Annu_is_admin",$login)=="Y") {
    // Ajout d'un groupe d'utilisateurs
    if ( (!$add_group) ||( ($add_group) && ( (!$description || !verifDescription($description) ) ||(!$intitule || !verifIntituleGrp ($intitule)) ) ) ) {
      ?>
      <form action="add_group.php" method="post">
        <table border="0">
	  <tbody>
	    <tr>
	      <td>Préfix:</td>
	      <td valign="top"><input type="text" name="prefix" size="2">&nbsp;<font color="orange"><u>Exemple</u> : <b>LP, LT</b></font></td>
	    </tr>
	    <tr>
	      <td>Catégorie:</td>
	      <td valign="top">
                 <select name="categorie">
	           <option>Classe</option>
	           <option>Cours</option>
                   <option>Equipe</option>
                   <option>Matiere</option>
                   <option>Autres</option>
                 </select>
              </td>
	    </tr>
	    <tr>
	      <td>Intitulé:</td>
	      <td valign="top"><input type="text" name="intitule" size="20"></td>
	    </tr>
	    <tr>
	      <td>Description :</td>
	      <td valign="top"><input type="text" name="description" size="40"></td>
	    </tr>
	    <tr>
	      <td></td>
	      <td></td>
	      <td >
                <input type="hidden" name="add_group" value="true">
                <input type="submit" value="Lancer la requête">
              </td>
	    </tr>
	  </tbody>
        </table>
      </form>
      <?
      // Message d'erreurs de saisie
      if ( $add_group && (!$intitule || !$description) ) {
        echo "<div class=error_msg>Vous devez saisir un nom de groupe et une description !</div><br>\n";
      } elseif ($add_group && !verifDescription($description)) {
        echo "<div class=error_msg>Le champ description comporte des caractères interdits !</div><br>\n";
      } elseif ($add_group && !verifIntituleGrp($intitule)) {
        echo "<div class=error_msg>Le champ intitulé ne doit pas commencer ou se terminer par l'expresssion : Classe, Equipe ou Matiere !</div><br>\n";
      }
    } else {
      $intitule = enleveaccents($intitule);
      // Construction du cn du nouveau groupe
      if ($prefix) $prefix=$prefix."_";
      if ($categorie=="Autres") $categorie=""; else $categorie=$categorie."_";
      $cn= $categorie.$prefix.$intitule;
      // Verification de l'existance du groupe
      $groups=search_groups("(cn=$cn)");
      if (count($groups)) {
        echo "<div class='error_msg'>Attention le groupe <font color='#0080ff'>$cn</font> est déja présent dans la base, veuillez choisir un autre nom !</div><BR>\n";
      } else {
        // Ajout du groupe
        //$description = utf8_encode(stripslashes($description));
        $description = stripslashes($description);
        ### MigreGon
        ### Les Euqipe et Matieres sont désormais des posixgroup
        // Test de la catégorie
        //if ($categorie == "Equipe_" || $categorie == "Matiere_" ) $groupType = "2"; else $groupType = "1";
        $groupType = "1";
        exec ("$scriptsbinpath/groupAdd.pl $groupType $cn \"$description\"",$AllOutPut,$ReturnValue);
        if ($ReturnValue == "0") {
          echo "<div class=error_msg>Le groupe <font color='#0080ff'>$cn</font> a été ajouté avec succès.</div><br>\n";
        } else {
          echo "<div class=error_msg>Echec, le groupe <font color='#0080ff'>$cn</font> n'a pas été créé !";
          if ($ReturnValue) echo "(type d'erreur : $ReturnValue),&nbsp;";
          echo "&nbsp;Veuillez contacter</div> <A HREF='mailto:$MelAdminLCS?subject=PB creation groupe'>l'administrateur du système</A><BR>\n";
        }
      }
    }
  } else {
    echo "<div class=error_msg>Cette fonctionnalité, nécessite les droits d'administrateur du serveur LCS !</div>";

  }

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
