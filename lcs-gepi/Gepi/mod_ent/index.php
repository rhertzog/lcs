<?php

/**
 * @version $Id: index.php 5228 2010-09-11 14:07:10Z jjocal $
 *
 * Module d'intégration de Gepi dans un ENT réalisé au moment de l'intégration de Gepi dans ARGOS dans l'académie de Bordeaux
 * Fichier permettant de récupérer de nouveaux élèves dans le ldap de l'ENT
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stéphane boireau, Julien Jocal
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


// Initialisations files
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

// Sécurité supplémentaire pour éviter d'aller voir ce fichier si on n'est pas dans un ent
if (getSettingValue("use_ent") != 'y') {
	DIE('Fichier interdit.');
}

// ======================= Initialisation des variables ==========================
//$ = isset($_POST[""]) ? $_POST[""] : NULL;
$aff_continuer = NULL;
$msg2   = NULL;
$etape  = isset($_GET["etape"]) ? $_GET["etape"] : isset($_POST["etape"]) ? $_POST["etape"] : NULL;
$mode   = isset($_GET["mode"]) ? $_GET["mode"] : isset($_POST["mode"]) ? $_POST["mode"] : NULL; // ldap ou csv
$type   = isset($_GET["type"]) ? $_GET["type"] : isset($_POST["type"]) ? $_POST["type"] : NULL; // eleve ou prof

// ======================= Traitement des données ================================
// On récupère le RNE de l'établissement en question
$RNE = getSettingValue("gepiSchoolRne");
if ($RNE === '') {
	$msg = "Attention, votre RNE n'est pas renseigné dans la page des <a href=\"gestion/param_gen.php\">paramètres généraux.</a>";
}else{

	$msg = "<p>Votre RNE est ".$RNE.". S'il est exact, vous pouvez passer à l'étape suivante.
				&nbsp;<a href=\"index.php?etape=2&mode=ldap\">Enregistrer les utilisateurs par l'annuaire LDAP (assurez vous d'y avoir un accès en lecture)</a></p>

        <br />
        <p color=\"red\">Par fichier, il est impératif de conserver cet ordre : les élèves d'abord puis les enseignants.</p>
        <form method=\"post\" action=\"index.php\" enctype=\"multipart/form-data\">
          <input type=\"hidden\" name=\"etape\" value=\"2\" />
          <label for=\"idEleveInput\" title='structure fichier : identifiant;nom prénom;login;classe'>
            Enregistrer les élèves par fichier csv</label>
          <input id=\"idEleveInput\" type=\"file\" name=\"nom\" />
          <input type=\"hidden\" name=\"mode\" value=\"csv\" />
          <input type=\"hidden\" name=\"type\" value=\"eleve\" />
          <input type=\"submit\" name=\"envoyer\" value=\"Envoyer\" />
        </form>
        <br />
        <form method=\"post\" action=\"index.php\" enctype=\"multipart/form-data\">
          <input type=\"hidden\" name=\"etape\" value=\"2\" />
          <label for=\"idEleveInput\" title='structure fichier : identifiant;nom prénom;login;titre;spécialité'>
            Enregistrer les professeurs par fichier csv</label>
          <input id=\"idEleveInput\" type=\"file\" name=\"nom\" />
          <input type=\"hidden\" name=\"mode\" value=\"csv\" />
          <input type=\"hidden\" name=\"type\" value=\"prof\" />
          <input type=\"submit\" name=\"envoyer\" value=\"Envoyer\" />
        </form>
        <br />
        <p style=\"cursor:pointer;\" onclick=\"changementDisplay('idInfosPlus', '');\">Plus d'informations</p>
        <div id='idInfosPlus' style='display:none;'>Pour le csv des enseignants, la spécialité est :<br />
        - ENS : enseignant<br />
        - DIR : personnel de direction<br />
        - AED : personnel de vie scolaire<br />
        - ADF : personnel administratif<br />
        - EDU : Conseiller principal d'éducation<br />
        </div>";

}

// On teste pour la table
if ($etape == 2) {

	$msg = NULL;
	// On crée la table si nécessaire

	$result = "&nbsp;->Ajout de la table ldap_bx. <br />";
	$test1 = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'ldap_bx'"));
	if ($test1 == 0) {
			$sql = "CREATE TABLE `ldap_bx` (
					`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
					`login_u` VARCHAR( 200 ) NOT NULL ,
					`nom_u` VARCHAR( 200 ) NOT NULL ,
					`prenom_u` VARCHAR( 200 ) NOT NULL ,
					`statut_u` VARCHAR( 50 ) NOT NULL ,
					`identite_u` VARCHAR( 50 ) NOT NULL ,
					PRIMARY KEY ( `id` ));";
		$query = mysql_query($sql);
		if ($query) {
			$msg = "<font style=\"color: green;\">Ok !</font><br />";
		} else {
			$msg = "<font style=\"color: red;\">Erreur</font><br />";
		}
	}else{
		$msg = "<font style=\"color: blue;\">La table existe déjà.</font><br />";
    $result .= "&nbsp;->Ajout d'un champ info_u à la table 'ldap_bx'<br />";
    $test_date_decompte=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ldap_bx LIKE 'info_u';"));
    if ($test_date_decompte>0) {
      $result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
    }else {
    	$query = mysql_query("ALTER TABLE ldap_bx ADD info_u VARCHAR(255) NOT NULL DEFAULT '';");
      if ($query) {
        $result .= "<font color=\"green\">Ok !</font><br />";
      } else {
        $result .= "<font color=\"red\">Erreur</font><br />";
      }
    }
	}

	// On truncate la table
	//$tr = mysql_query("TRUNCATE TABLE ldap_bx");


  /******************************************************
   * Deux solutions sont proposées :                    *
   *  - se connecter au ldap pour faire la récupération *
   *  - fournir un fichier csv pour assurer la jointure *
   ******************************************************/
  if ($mode == 'csv'){
    // Méthode par fichier CSV
    // erreur ?
     if ($_FILES['nom']['error'] > 0){
       $msg = "Erreur lors du transfert";
     // fichier csv ?
     }elseif(strtolower(  substr(  strrchr($_FILES['nom']['name'], '.')  ,1)  ) != 'csv'){
       $msg = "Il faut fournir un fichier csv";
     // On peut y aller
     }else{
        $taille = 1024;
        $delimiteur = ";";
        $marqueur = 0;
        if($fp = fopen($_FILES['nom']['tmp_name'],"r")) {
          if ($type == 'eleve'){
            // On truncate la table
            $tr = mysql_query("TRUNCATE TABLE ldap_bx");
            while ($ligne = fgetcsv($fp, $taille, $delimiteur)) {
              /* On peut faire le traitement de chaque ligne */
              if ($marqueur >= 1){
                //echo utf8_decode($ligne[$a]) . '<br/>';
                $it = explode(' ', $ligne[1]);
                $sql = "INSERT INTO ldap_bx (id, login_u, nom_u, prenom_u, statut_u, identite_u, info_u)
                VALUES ('',
            				'".$ligne[2]."',
      							'".mysql_real_escape_string(utf8_decode($it[0]))."',
                  	'".mysql_real_escape_string(utf8_decode($it[1]))."',
            				'student',
      							'".$ligne[0]."',
                    '".mysql_real_escape_string($ligne[3])."')";
                $query = @mysql_query($sql);
                if ($query) {
              		$msg2 .= '<br />L\'utilisateur '.$ligne[2].' a été enregistré.';
            		}else{
                	$msg2 .= '<br /><span style="color: red;">L\'utilisateur '.$ligne[2].' n\'a pas été enregistré.</span>';
               	}
              }
              ++$marqueur;
            } // while
            $msg2 .= '<p><a href="index.php">Continuer avec les professeurs</a></p>';
          } // if ($type == 'eleve'){
          if ($type == 'prof'){
            while ($ligne = fgetcsv($fp, $taille, $delimiteur)) {
              /* On peut faire le traitement de chaque ligne */
              if ($marqueur >= 1){
                //echo utf8_decode($ligne[$a]) . '<br/>';
                $it = explode(' ', $ligne[1]);
                $sql = "INSERT INTO ldap_bx (id, login_u, nom_u, prenom_u, statut_u, identite_u, info_u)
                VALUES ('',
            				'".$ligne[2]."',
      							'".mysql_real_escape_string(utf8_decode($it[0]))."',
                  	'".mysql_real_escape_string(utf8_decode($it[1]))."',
            				'teacher',
      							'".$ligne[0]."',
                    '".mysql_real_escape_string($ligne[4])."')";
                $query = @mysql_query($sql);
                if ($query) {
              		$msg2 .= '<br />L\'utilisateur '.$ligne[2].' a été enregistré.';
            		}else{
                	$msg2 .= '<br /><span style="color: red;">L\'utilisateur '.$ligne[2].' n\'a pas été enregistré.</span>';
               	}
              }
              ++$marqueur;
            } // while
          } // if ($type == 'prof'){

    /* fermeture fichier */
    fclose ($fp);
} else {
    echo "Ouverture impossible.";
}

     }
  }else{
    // On truncate la table
    $tr = mysql_query("TRUNCATE TABLE ldap_bx");
    // méthode LDAP
    	// On ouvre une connexion avec le ldap
    $ldap = new LDAPServer;
    $info = $ldap->get_all_users('rne', $RNE);

    // $infos est donc un tableau de tous les utilisateurs du LDAP qui ont ce $RNE en attribut (sic)
    for($a=0; $a < $info["count"]; $a++){

      if (file_exists("../secure/config_ldap.inc.php")) {
        require("../secure/config_ldap.inc.php");
      }
        $ldap_login		= (isset($ldap_champ_login) AND $ldap_champ_login != '') ? $ldap_champ_login : 'uid';
        $ldap_nom		= (isset($ldap_champ_nom) AND $ldap_champ_nom != '') ? $ldap_champ_nom : 'sn';
      	$ldap_prenom	= (isset($ldap_champ_prenom) AND $ldap_champ_prenom != '') ? $ldap_champ_prenom : 'givenname';
    		$ldap_statut	= (isset($ldap_champ_statut) AND $ldap_champ_statut != '') ? $ldap_champ_statut : 'edupersonaffiliation';
  			$ldap_numero	= (isset($ldap_champ_numero) AND $ldap_champ_numero != '') ? $ldap_champ_numero : 'employeenumber';

  		if (isset($info[$a][$ldap_numero][0])) {
      	$ident = $info[$a][$ldap_numero][0];
    	}else{
  			$ident = 'non';
      }

    	$sql = "INSERT INTO ldap_bx (id, login_u, nom_u, prenom_u, statut_u, identite_u)
					VALUES ('',
							'".$info[$a][$ldap_login][0]."',
							'".mysql_real_escape_string($info[$a][$ldap_nom][0])."',
							'".mysql_real_escape_string($info[$a][$ldap_prenom][0])."',
							'".$info[$a][$ldap_statut][0]."',
							'".$ident."')";
  		$query = mysql_query($sql);

      if ($query) {
    		$msg2 .= '<br />L\'utilisateur '.$info[$a][$ldap_login][0].' a été enregistré.';
  		}else{
      	$msg2 .= '<br /><span style="color: red;">L\'utilisateur '.$info[$a][$ldap_login][0].' n\'a pas été enregistré.</span>';
    	}
  	}
  }
	$aff_continuer = '<p>Vous pouvez retourner sur la page d\'initialisation par sconet/STSweb <a href="../init_xml2/index.php">CONTINUER</a></p>
	<p><a href="miseajour_ent_eleves.php">Ajouter de nouveaux utilisateurs arrivés en cours d\'année</a></p>';

}

// =========== fichiers spéciaux ==========
$style_specifique = "edt_organisation/style_edt";
//**************** EN-TETE *****************
$titre_page = "Les utilisateurs de l'ENT";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
//debug_var(); // à enlever en production
?>

<!-- Mise à jour à partir de l'ENT -->
<p class="bold"><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>R&eacute;cup&eacute;ration des informations de l'ENT</h2>

<?php echo $msg2 . $aff_continuer; ?>



<?php require_once("../lib/footer.inc.php");