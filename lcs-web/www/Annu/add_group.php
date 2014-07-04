<?php
/* =============================================
   Projet LCS-SE3
   Consultation/ Gestion de l'annuaire LDAP
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 23/05/2014
   ============================================= */
include "includes/check-token.php";
if (!check_acces()) exit;

  $login=$_SESSION['login'];
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  $add_group=$description=$intitule=false;
  if (count($_POST)>0) {
  	//configuration objet
 	include ("../lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
 	$config = HTMLPurifier_Config::createDefault();
 	$purifier = new HTMLPurifier($config);
    //purification des variables
  	$prefix=$purifier->purify($_POST['prefix']);
  	$categorie=$purifier->purify($_POST['categorie']);
  	$intitule=$purifier->purify($_POST['intitule']);
  	$description=$purifier->purify($_POST['description']);
  	$add_group=$purifier->purify($_POST['add_group']);
  }


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
	      <td>Pr&#233;fix:</td>
	      <td valign="top"><input type="text" name="prefix" size="2">&nbsp;<font color="orange"><u>Exemple</u> : <b>LP, LT</b></font></td>
	    </tr>
	    <tr>
	      <td>Cat&#233;gorie:</td>
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
	      <td>Intitul&#233;:</td>
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
                <input name="jeton" type="hidden"  value="<?php echo md5($_SESSION['token'].htmlentities($_SERVER['PHP_SELF'])); ?>" />
                <input type="submit" value="Lancer la requ&#234;te">
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
        echo "<div class=error_msg>Le champ description comporte des caract&#232;res interdits !</div><br>\n";
      } elseif ($add_group && !verifIntituleGrp($intitule)) {
        echo "<div class=error_msg>Le champ intitul&#233; ne doit pas commencer ou se terminer par l'expresssion : Classe, Equipe ou Matiere !</div><br>\n";
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
        echo "<div class='error_msg'>Attention le groupe <font color='#0080ff'>$cn</font> est d&#233;ja pr&#233;sent dans la base, veuillez choisir un autre nom !</div><BR>\n";
      } else {
        // Ajout du groupe
        $description = stripslashes($description);
        ### MigreGon
        ### Les Euqipe et Matieres sont desormais des posixgroup
        // Test de la categorie
        //if ($categorie == "Equipe_" || $categorie == "Matiere_" ) $groupType = "2"; else $groupType = "1";
        $groupType = "1";
        exec ("$scriptsbinpath/groupAdd.pl ". escapeshellarg($groupType) . " ". escapeshellarg($cn) . " ". escapeshellarg($description),$AllOutPut,$ReturnValue);
        if ($ReturnValue == "0") {
          echo "<div class=error_msg>Le groupe <font color='#0080ff'>$cn</font> a &#233;t&#233; ajout&#233; avec succ&#232;s.</div><br>\n";
        } else {
          echo "<div class=error_msg>Echec, le groupe <font color='#0080ff'>$cn</font> n'a pas &#233;t&#233; cr&#233;&#233; !";
          if ($ReturnValue) echo "(type d'erreur : $ReturnValue),&nbsp;";
          echo "&nbsp;Veuillez contacter</div> <A HREF='mailto:$MelAdminLCS?subject=PB creation groupe'>l'administrateur du syst&#232;me</A><BR>\n";
        }
      }
    }
  } else {
    echo "<div class=error_msg>Cette fonctionnalit&#233;, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";

  }

  include ("../lcs/includes/pieds_de_page.inc.php");
?>
