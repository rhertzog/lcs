<?php
/* =============================================
   Projet LCS-SE3
   Consultation de l'annuaire LDAP
   Annu/includes/ihm.inc.php
   � jLCF >:> � jean-luc.chretien@tice.ac-caen.fr
   � wawa �  olivier.lecluse@crdp.ac-caen.fr
   Equipe Tice academie de Caen
   Derniere mise � jour 18/06/2008
   Distribu� selon les termes de la licence GPL
   ============================================= */

# Model caracteres speciaux pour les mots de passe
$char_spec = "&_#@�%�:!?*$";

// Remplace les caract�res accentu�s par leurs equivalents

// et l'espace par underscore
function enleveaccents($chaine){
  $chaine = strtr($chaine,
                  "����������������������������������������������������� ",
                  "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn_");
  return $chaine;
}

function unac_string_with_space ($chaine){
  $motif="������������������������������������������������������";
  $motifr="aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuyynnY";
  $retour=strtr(ereg_replace("�","AE",ereg_replace("�","ae",ereg_replace("�","OE",ereg_replace("�","oe","$chaine")))),"$motif","$motifr");
  return $retour;
}

function unac_string_with_underscore ($chaine){
  $motif="����������������������������������������������������� ";
  $motifr="aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynnY_";
  $retour=strtr(ereg_replace("�","AE",ereg_replace("�","ae",ereg_replace("�","OE",ereg_replace("�","oe","$chaine")))),"$motif","$motifr");
  return $retour;
}

// Verification de l'intitul� d'un groupe
// L'intitul� d'un groupe ne doit pas commencer et finir par les mots :
//   Classe, Cours, Equipe, Matiere

function verifIntituleGrp ($intitule) {
  $motif1 = "^Classe$";
  $motif2 = "^Cours$";
  $motif3 = "^Equipe$";
  $motif4 = "^Matiere$";
  if ( ereg($motif1,$intitule)||ereg($motif2,$intitule)||ereg($motif3,$intitule)||ereg($motif4,$intitule) ) {
    $ret = false;
  } else $ret = true;
  return $ret;
}

// Verification de la validit� d'un mot de passe
// longueur de 4 � 8 caract�res
// compos� de lettre et d'au moins un chiffre ou des caract�res
// sp�ciaux suivants : $char_spec

function verifPwd ($password) {
  global $char_spec;

  if ( ereg("(^[a-zA-Z]*$)|(^[0-9]*$)", $password) )
  	return false;
  elseif ( ereg("^[[:alnum:]$char_spec]{4,8}$", $password) )
    	return true; else return false;
}

// Verification format date de naissance
function verifDateNaissance ($date) {
$motif = "^[0-9]{8}$";

 if ( ereg($motif, $date) ) {
   // V�rification de l'ann�e
   if ( (date(Y) - substr ($date,0,4) < 75) && (date(Y) - substr ($date,0,4) > 4) ) {
     // Verification du mois
     if ( (substr ($date,4,2) > 0) && (substr ($date,4,2) <= 12 ) ) {
       if ( (substr ($date,6,2) > 0) && (substr ($date,6,2) <= 31) ) {
         $ret = true;
       }
     }
   }
 } else {
  $ret = false;
 }
 return $ret;
}

// Verification d'une entree de type Nom ou Prenom
function verifEntree($entree) {
  $motif = "^[-a-zA-Z0-9 \'����������������������������������������������������� ]{1,20}$";

  if ( ereg($motif, $entree) ) {
     $ret= true;
  } else {
    $ret= false;
  }
  return $ret;
}

// Verification du format du pseudo
function verifPseudo($pseudo) {
  $motif = "[\|,/ ]";

  if ( ereg($motif, $pseudo) || strlen ($pseudo) > 20 || strlen ($pseudo) == 0 ) {
    $ret = false;
  } else {
    $ret = true;
  }
  return $ret;
}

// Verification du champ description
function verifDescription($entree) {
  $motif = "/^[a-zA-Z0-9\s,.;\"\'\/:&�����������������������������������������������������-]{0,80}$/";
  if ( preg_match($motif, stripslashes($entree)) ) {
     $ret= true;
  } else {
    $ret= false;
  }
  return $ret;
}

// Verification numero de telephone
function verifTel ($tel) {
  $motif ="^[0-9]{10}$";

  if ( ereg($motif, $tel) || strlen ($tel) == 0 ) {
    $ret = true;
  } else {
    $ret = false;
  }
  return $ret;
}

function header_html()
{
  echo "<HTML>
          <HEAD>
			<TITLE>Interface d'administration LCS</TITLE>
            <LINK  href='style.css' rel='StyleSheet' type='text/css'>
            <SCRIPT language = 'javascript'
               type = 'text/javascript'
               src = 'includes/check.inc.js'>
            </SCRIPT>
          </HEAD>
          <BODY>\n";
}

/*
function is_admin ($idpers)
{
  global $DBAUTH, $authlink;
  // Recherche si l'utilisateur connect� est admin
  if ($idpers) {
    // Recherche le login dans la base personne
    $result=mysql_db_query("$DBAUTH","SELECT login FROM personne WHERE id=$idpers", $authlink);
    if ($result && mysql_num_rows($result)) {
      $login=mysql_result($result,0,0);
      mysql_free_result($result);
    } else {
      $login="";
    }
  }
  $people_attr=people_get_variables ($login, false);
  return ($people_attr[0]["admin"]);
}
*/

function is_admin ($droit,$login)
{
   if ((ldap_get_right("lcs_is_admin",$login)=="Y")||(ldap_get_right($droit,$login)=="Y"))
    $srch="Y";
  else
    $srch="N";
  return $srch;
}

function  aff_mnu_search($user_type)
{
  if ($user_type=="Y") {
    // Affichage menu admin
    echo"
     <ul>
       <li><a href=\"search.php\">Effectuer une recherche...</a> (pour d'�ventuelles modifications)</li>
       <li><a href=\"me.php\">Voir ma fiche</a></li>
       <li><a href=\"mod_entry.php\">Modifier mon entr�e dans l'annuaire...</a></li>
       <li>Ajouter :
         <ul>
           <li><a href=\"add_user.php\">un utilisateur...</a></li>
           <li><a href=\"add_group.php\">un groupe...</a></li>
           <li><a href=\"add_sous-group.php\">un sous-groupe...</a></li>
         </ul>
       </li>
       <li><a href=\"../lcs-doc/web/html/import/\">G�n�rer les comptes de l'annuaire...</a></li>
     </ul>\n";


  } else {
    // Affichage menu user
    echo"
     <ul>
       <li><a href=\"search.php\">Effectuer une recherche...</a></li>
       <li><a href=\"me.php\">Voir ma fiche</a></li>
       <li><a href=\"mod_entry.php\">Modifier ma fiche</a></li>
       <li><a href=\"mod_pwd.php\">Changer de mot de passe...</a></li>
     </ul>\n";
  }
}

// Affichage de la barre remorqu�e de haut de page
// mode 1  : lien Annuaire
// mode 2  : lien Annuaire -> Recherche
// mode 3  : lien Annuaire -> Lien Recherche
// mode 31 : lien Annuaire -> Modification
// mode 4  : lien Annuaire -> lien Modification pseudo
// mode 5  : lien Annuaire -> lien Modification pwd
// mode 6  : lien Annuaire -> lien Ajout groupe
// mode 7  : lien Annuaire -> lien Ajout utilisateur
function aff_trailer ($mode)
{
  global $imagespath;
    echo"<h2><a href=\"index.php\">Annuaire</a>&nbsp;";
    if ($mode == 1 ) {
      echo "</h2>";
    } elseif ($mode == 2) {
      echo "-> Recherche</h2>";
    } elseif ($mode == 3 ) {
      echo "-> <a href=\"search.php\">Recherche</a></h2>";
    } elseif ($mode == 31 ) {
      echo "-> <a href=\"search.php\">Recherche</a> -> Modification</h2>";
    } elseif ($mode == 4 ) {
      echo "-> <a href=\"mod_entry.php\">Modification</a></h2>";
    } elseif ($mode == 5 ) {
      echo "-> <a href=\"mod_pwd.php\">Modification</a></h2>";
    } elseif ($mode == 6 ) {
      echo "-> <a href=\"add_group.php\">Ajout d'un groupe</a></h2>";
    } elseif ($mode == 7 ) {
      echo "-> <a href=\"add_user.php\">Ajout d'un utilisateur</a></h2>";
    } elseif ($mode == 8 ) {
      echo "-> <a href=\"add_sous-group.php\">Ajout d'un sous-groupe</a></h2>";
    } else {
      echo "</h2>";
    }
    echo "<CENTER>
            <IMG SRC=\"$imagespath/line.png\"WIDTH=\"90%\" HEIGHT=\"2\" BORDER=\"0\">
          </CENTER><BR>\n";
}

?>
