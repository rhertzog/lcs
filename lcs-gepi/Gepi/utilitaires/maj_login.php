<?php
/*
 * $Id: maj_login.php 2147 2008-07-23 09:01:04Z tbelliard $
 *
 * Cette proc�dure sp�ciale de mise � jour de la base MySql permet de r�gler les probl�mes li�s
 * � la pr�sence dans certains identifiants d'�l�ves du caract�re \"-\".
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$valid = isset($_POST["valid"]) ? $_POST["valid"] : 'no';

if (isset($_POST['submit'])) {
    if (isset($_POST['login']) && isset($_POST['password'])) {
        $sql = "select upper(login) login, password, prenom, nom, statut from utilisateurs where login = '" . $_POST['login'] . "' and password = md5('" . $_POST['password'] . "') and etat != 'inactif' and statut='administrateur' ";
        $res_user = sql_query($sql);
        $num_row = sql_count($res_user);
        if ($num_row == 1) {
            $valid='yes';
        } else {
            $message = "Identifiant ou mot de passe incorrect, ou bien vous n'�tes pas administrateur.";
        }
    }
}
?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    <HTML>
    <HEAD>
    <link REL="stylesheet" href="style.css" type="text/css" />
    <TITLE>Mise � jour sp�ciale de la base de donn�e GEPI</TITLE>
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/ico" href="../favicon.ico" />
    </head>
    <BODY>
<?php

if (($resultat_session == '0') and ($valid!='yes')) {
    ?>
    <form action="maj_login.php" method='POST' style="width: 100%; margin-top: 24px; margin-bottom: 48px;">
    <div class="center">
    <H2 align="center"><?php echo "Mise � jour sp�ciale de la base de donn�e GEPI<br />(Acc�s administrateur)"; ?></H2>

    <?php
    if (isset($message)) {
        echo("<p><font color=red>" . $message . "</font></p>");
    }
    ?>
    <fieldset style="padding-top: 8px; padding-bottom: 8px; width: 40%; margin-left: auto; margin-right: auto;">
    <legend style="font-variant: small-caps;">Identifiez-vous</legend>
    <table style="width: 100%; border: 0;" cellpadding="5" cellspacing="0">
    <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="login">Identifiant</label></td>
    <td style="text-align: center; width: 60%;"><input type="text" name="login" size="16" /></td>
    </tr>
    <tr>
    <td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="password">Mot de passe</label></td>
    <td style="text-align: center; width: 60%;"><input type="password" name="password" size="16" /></td>
    </tr>
    </table>
    <input type="submit" name="submit" value="Envoyer" style="font-variant: small-caps;" />
    </fieldset>
    </div>
    </form>
    </body>
    </html>
    <?php
    die();
};

if ((isset($_SESSION['statut'])) and ($_SESSION['statut'] != 'administrateur')) {
   echo "<center><p class=grand><font color=red>Mise � jour sp�ciale de la base MySql de GEPI.<br />Vous n'avez pas les droits suffisants pour acc�der � cette page.</font><p></center></body></html>";
   die();
}



if (isset($_POST['maj'])) {

$liste_tables = array(
"absences",
"aid_appreciations",
"avis_conseil_classe",
"cn_notes_conteneurs",
"cn_notes_devoirs",
"eleves",
"j_aid_eleves",
"j_aid_eleves_resp",
"j_eleves_classes",
"j_eleves_professeurs",
"j_eleves_regime",
"matieres_appreciations",
"matieres_notes"
);
$message = '';
$j=0;
while ($j < count($liste_tables))  {
  $req = mysql_query("SELECT login FROM $liste_tables[$j]");
  $nb_lignes = mysql_num_rows($req);
  $i = 0;
  $affiche = 'yes';
  while ($i < $nb_lignes) {
      $temp = mysql_result($req, $i, 'login');
      $pos = strpos($temp, "-");
      if ($pos >=1) {
          $tempo = str_replace("-", "_", $temp);
          $up = mysql_query("UPDATE $liste_tables[$j] set login = '".$tempo."' where login ='".$temp."'");
          if ($affiche == 'yes') {
              $message .= "Dans la table ".$liste_tables[$j].", l'identifiant : ".$temp." a �t� chang� en : ".$tempo."<br>";
              $affiche = 'no';
          }
      }
      $i++;
  }
  $j++;
}
// cas particulier de j_eleves_etablissements
$req = mysql_query("SELECT id_eleve FROM j_eleves_etablissements");
$nb_lignes = mysql_num_rows($req);
$affiche = 'yes';
$i = 0;
while ($i < $nb_lignes) {
    $temp = mysql_result($req, $i, 'id_eleve');
    $pos = strpos($temp, "-");
    if ($pos >=1) {
		// A ce stade, la table 'eleves' a d�j� �t� mise � jour
		$sql="SELECT elenoet FROM eleves WHERE login='$tempo';";
		$res_elenoet=mysql_query($sql);
		if(mysql_num_rows($res_elenoet)>0) {
			$lig_elenoet=mysql_fetch_object($res_elenoet);
			$reg_no_gep=$lig_elenoet->elenoet;
			if($reg_no_gep!="") {
				//$up = mysql_query("UPDATE j_eleves_etablissements set id_eleve = '".$tempo."' where id_eleve ='".$temp."'");
				$up = mysql_query("UPDATE j_eleves_etablissements set id_eleve = '".$res_elenoet."' where id_eleve ='".$temp."'");
				if ($affiche == 'yes') {
					//$message .= "Dans la table j_eleves_etablissements, l'identifiant : ".$temp." a �t� chang� en : ".$tempo."<br>";
					$message .= "Dans la table j_eleves_etablissements, l'identifiant : ".$temp." a �t� chang� en : ".$res_elenoet."<br>";
					$affiche = 'no';
				}
			}
		}
    }
    $i++;
}


}

if (isset($mess)) echo "<center><p class=grand><font color=red>".$mess."</font><p></center>";
echo "<center><p class=grand>Mise � jour sp�ciale de la base de donn�es MySql de GEPI<p></center>";
if (isset($message)) {
    if ($message == '') {
        echo "<p>Aucun changement n'a �t� effectu�. Pas de probl�me dans la base.</p>";
    } else {
        echo "<p>R�sultat de la mise � jour :</p>".$message;
    }
} else {
    if (isset($_SESSION['statut'])) {
        echo "<p>Il est vivement conseill� de faire une sauvegarde de la base MySql avant de proc�der � la mise � jour</p>";
        echo "<center><form enctype=\"multipart/form-data\" action=\"../gestion/accueil_sauve.php?action=dump\" method=post name=formulaire>";
        echo "<input type=\"submit\" value=\"Lancer une sauvegarde de la base de donn�es\" /></form></center>";
    }
    echo "<p>Remarque : Cette proc�dure sp�ciale de mise � jour de la base MySql permet de r�gler les probl�mes li�s
    � la pr�sence dans certains identifiants d'�l�ves du caract�re \"-\".
    <br><br>Si vous avez utilis� la proc�dure d'initialisation des bases de GEPI � partir des
    fichiers GEP avec une version de GEPI ant�rieure � la version 1.3, il se peut que certains identifiants d'�l�ves
    contiennent le caract�re \"-\". Ceci entra�ne des dysfonctionnements dans la saisie des notes et appr�ciations de
    ces �l�ves.</p>";
    echo "<form action=\"maj_login.php\" method=\"post\">";
    echo "<p><b>Cliquez sur le bouton suivant pour effectuer la mise � jour de la base</b></p>";
    echo "<center><input type=submit value='Mettre � jour' /></center>";
    echo "<input type=hidden name='maj' value='yes' />";
    echo "<input type=hidden name='valid' value='$valid' />";
    echo "</form>";
}
echo "<br><center><a href=\"".$gepiPath."/logout.php?auto=0\">D�connexion</a></center>";
?>
</body></html>