<?php
/*
 * $Id: index.php 3822 2009-11-27 17:52:32Z jjacquard $
 *
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
if ($resultat_session == 'c') {
   header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
   die();
} else if ($resultat_session == '0') {
   header("Location: ../logout.php?auto=1");
   die();
};
// Check access
if (!checkAccess()) {
   header("Location: ../logout.php?auto=1");
   die();
}
if (isset($_POST['activer'])) {
    if (!saveSetting("active_cahiers_texte", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du param�tre activation/d�sactivation !";
}

if (isset($_POST['version'])) {
    if (!saveSetting("GepiCahierTexteVersion", $_POST['version'])) $msg = "Erreur lors de l'enregistrement du numero de version du cahier de texte !";
}

if (isset($_POST['cahiers_texte_login_pub'])) {
    $mdp = $_POST['cahiers_texte_passwd_pub'];
    $user_ct = $_POST['cahiers_texte_login_pub'];

    if ((trim($mdp)=='') and (trim($user_ct) !='')) {
        $_POST['cahiers_texte_login_pub'] = '';
        $msg .= "Vous devez choisir un mot de passe.";
    }
    if ((trim($mdp) !='')and (trim($user_ct) == '')) {
       $_POST['cahiers_texte_passwd_pub'] = '';
       $msg .= "Vous devez choisir un identifiant.";
    }

    if (!saveSetting("cahiers_texte_passwd_pub", $_POST['cahiers_texte_passwd_pub'])) $msg .= "Erreur lors de l'enregistrement du mot de passe !";
//    include_once( '../lib/class.htaccess.php' );
    if (!saveSetting("cahiers_texte_login_pub", $_POST['cahiers_texte_login_pub'])) $msg .= "Erreur lors de l'enregistrement du login !";

}

if (isset($_POST['begin_day']) and isset($_POST['begin_month']) and isset($_POST['begin_year'])) {
    $begin_bookings = mktime(0,0,0,$_POST['begin_month'],$_POST['begin_day'],$_POST['begin_year']);
    if (!saveSetting("begin_bookings", $begin_bookings)) $msg .= "Erreur lors de l'enregistrement de begin_bookings !";
}
if (isset($_POST['end_day']) and isset($_POST['end_month']) and isset($_POST['end_year'])) {
    $end_bookings = mktime(0,0,0,$_POST['end_month'],$_POST['end_day'],$_POST['end_year']);
    if (!saveSetting("end_bookings", $end_bookings)) $msg .= "Erreur lors de l'enregistrement de end_bookings !";
}

if (isset($_POST['cahier_texte_acces_public'])) {
	if ($_POST['cahier_texte_acces_public'] == "yes") {
	    $temp = "yes";
	} else {
	    $temp = "no";
	    }
	if (!saveSetting("cahier_texte_acces_public", $temp)) {
	    $msg .= "Erreur lors de l'enregistrement de cahier_texte_acces_public !";
	}
}

//ajout Eric visa CDT
if (isset($_POST['visa_cdt_inter_modif_notices_visees'])) {
	if ($_POST['visa_cdt_inter_modif_notices_visees'] == "yes") {
	    $temp = "yes";
	} else {
	    $temp = "no";
	    }
	if (!saveSetting("visa_cdt_inter_modif_notices_visees", $temp)) {
	    $msg .= "Erreur lors de l'enregistrement de visa_cdt_inter_modif_notices_visees !";
	}
}
//Fin ajout Eric

if (isset($_POST['is_posted'])) $msg = "Les modifications ont �t� enregistr�es !";
// header
$titre_page = "Gestion des cahiers de textes";
require_once("../lib/header.inc");

if (isset($_POST['delai_devoirs'])) {
    if (!saveSetting("delai_devoirs", $_POST['delai_devoirs'])) $msg .= "Erreur lors de l'enregistrement du d�lai de visualisation des devoirs";
}

?>
<p class=bold><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<h2>Activation des cahiers de textes</h2>
<form action="index.php" name="form1" method="post">
<i>La d�sactivation des cahiers de textes n'entra�ne aucune suppression des donn�es. Lorsque le module est d�sactiv�, les professeurs n'ont pas acc�s au module et la consultation publique des cahiers de textes est impossible.</i>
<br />
<label for='activer_y' style='cursor: pointer;'><input type="radio" name="activer" id="activer_y" value="y" <?php if (getSettingValue("active_cahiers_texte")=='y') echo " checked='checked'"; ?> />
&nbsp;Activer les cahiers de textes (consultation et �dition)</label><br />
<label for='activer_n' style='cursor: pointer;'><input type="radio" name="activer" id="activer_n" value="n" <?php if (getSettingValue("active_cahiers_texte")=='n') echo " checked='checked'"; ?> />

&nbsp;D�sactiver les cahiers de textes (consultation et �dition)</label><br />
<h2>Version des cahiers de textes</h2>
<?php $extensions = get_loaded_extensions();
		if(!in_array('pdo_mysql',$extensions)) {
		    echo "<span style='color:red'>ATTENTION&nbsp;</span> Il semble que l'extension php 'pdo_mysql' ne soit pas pr�sente.<br />Cela risque de rendre impossible l'utilisation de la version 2 du cahier de texte<br />";
		}?>
<p style="font-style: italic;">La version 2 du cahier de texte necessite php 5.2.x minimum</p>
	<label for='version_1' style='cursor: pointer;'>
	<input type="radio" name="version" id="version_1" value="1" <?php if (getSettingValue("GepiCahierTexteVersion")=='1') echo " checked='checked'"; ?> />
&nbsp;Cahier de texte version 1</label> (<span style='font-size: small; font-style: italic;'>le cahier de texte version 1 ne sera plus support� dans la future version 1.5.3</span>)<br />
	<label for='version_2' style='cursor: pointer;'>
	<input type="radio" name="version" id="version_2" value="2" <?php if (getSettingValue("GepiCahierTexteVersion")=='2') echo " checked='checked'"; ?> />
&nbsp;Cahier de texte version 2</label><br />

<h2>D�but et fin des cahiers de textes</h2>
<i>Seules les rubriques dont la date est comprise entre la date de d�but et la date de fin des cahiers de textes sont visibles dans
l'interface de consultation publique.
<br />L'�dition (modification/suppression/ajout) des cahiers de textes par les utilisateurs de GEPI n'est pas affect�e par ces dates.
</i><br />
<table>
     <tr>
        <td>
        Date de d�but des cahiers de textes :
        </td>
        <td><?php
        $bday = strftime("%d", getSettingValue("begin_bookings"));
        $bmonth = strftime("%m", getSettingValue("begin_bookings"));
        $byear = strftime("%Y", getSettingValue("begin_bookings"));
        genDateSelector("begin_", $bday, $bmonth, $byear,"more_years") ?>
        </td>
    </tr>
    <tr>
        <td>
        Date de fin des cahiers de textes :
        </td>
        <td><?php
        $eday = strftime("%d", getSettingValue("end_bookings"));
        $emonth = strftime("%m", getSettingValue("end_bookings"));
        $eyear= strftime("%Y", getSettingValue("end_bookings"));
        genDateSelector("end_",$eday,$emonth,$eyear,"more_years") ?>
        </td>
    </tr>
</table>
<input type="hidden" name="is_posted" value="1" />
<h2>Acc�s public</h2>
<label for='cahier_texte_acces_public_n' style='cursor: pointer;'><input type='radio' name='cahier_texte_acces_public' id='cahier_texte_acces_public_n' value='no'<?php if (getSettingValue("cahier_texte_acces_public") == "no") echo " checked='checked'";?> /> D�sactiver la consultation publique des cahiers de textes (seuls des utilisateurs logu�s pourront y avoir acc�s en consultation, s'ils y sont autoris�s)</label><br />
<label for='cahier_texte_acces_public_y' style='cursor: pointer;'><input type='radio' name='cahier_texte_acces_public' id='cahier_texte_acces_public_y' value='yes'<?php if (getSettingValue("cahier_texte_acces_public") == "yes") echo " checked='checked'";?> /> Activer la consultation publique des cahiers de textes (tous les cahiers de textes visibles directement, ou par la saisie d'un login/mdp global)</label><br />
<p>-> Acc�s � l'<a href='../public/index.php?id_classe=-1' target='_blank'>interface publique de consultation des cahiers de textes</a></p>
<i>En l'absence de mot de passe et d'identifiant, l'acc�s � l'interface publique de consultation des cahiers de textes est totalement libre.</i>
<br />
Identifiant :
<input type="text" name="cahiers_texte_login_pub" value="<?php echo getSettingValue("cahiers_texte_login_pub"); ?>" size="20" />
<br />Mot de passe :
<input type="text" name="cahiers_texte_passwd_pub" value="<?php echo getSettingValue("cahiers_texte_passwd_pub"); ?>" size="20" />
<h2>D�lai de visualisation des devoirs</h2>
<i>Indiquez ici le d�lai en jours pendant lequel les devoirs seront visibles, � compter du jour de visualisation s�lectionn�, dans l'interface publique de consulation des cahiers de textes.
<br />Mettre la valeur 0 si vous ne souhaitez pas activer le module de remplissage des devoirs.
Dans ce cas, les professeurs font figurer les devoirs � faire dans la m�me case que le contenu des s�ances.
</i>
<br />D�lai :
<input type="text" name="delai_devoirs" value="<?php echo getSettingValue("delai_devoirs"); ?>" size="2" /> jours

<br /><br />

<h2>Visa des cahiers de texte</h2>
<label for='visa_cdt_inter_modif_notices_visees_y' style='cursor: pointer;'><input type='radio' name='visa_cdt_inter_modif_notices_visees' id='visa_cdt_inter_modif_notices_visees_y' value='yes'<?php if (getSettingValue("visa_cdt_inter_modif_notices_visees") == "yes") echo " checked='checked'";?> /> Activer l'interdiction pour les enseignants de modifier une notice apr�s la signature des cahiers de textes</label><br />
<label for='visa_cdt_inter_modif_notices_visees_n' style='cursor: pointer;'><input type='radio' name='visa_cdt_inter_modif_notices_visees' id='visa_cdt_inter_modif_notices_visees_n' value='no'<?php if (getSettingValue("visa_cdt_inter_modif_notices_visees") == "no") echo " checked='checked'";?> /> D�sactiver l'interdiction pour les enseignants de modifier une notice apr�s la signature des cahiers de textes</label><br />

<br /><br />

<center>
	<input type="submit" value="Enregistrer" style="font-variant: small-caps;" />
</center>
</form>

<hr />
<h2>Gestion des cahiers de textes</h2>
<ul>
	<li><a href='modify_limites.php'>Espace disque maximal, taille maximale d'un fichier</a></li>
	<li><a href='modify_type_doc.php'>Types de fichiers autoris�s en t�l�chargement</a></li>
	<li><a href='admin_ct.php'>Administration des cahiers de textes</a> (recherche des incoh�rences, modifications, suppressions)</li>
	<li><a href='visa_ct.php'>Viser les cahiers de textes</a> (Signer les cahiers de textes)</li>
</ul>
<?php
	require("../lib/footer.inc.php");
?>
