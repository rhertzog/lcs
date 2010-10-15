<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: $
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
*
*/

/**
* Appelle les sous-mod�les
* templates/origine/header_template.php
* templates/origine/bandeau_template.php
 *
 * @author regis
 */


?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'ent�te -->
	<?php
	  $tbs_bouton_taille = "..";
	  include('../../templates/origine/header_template.php');
	?>

  <script type="text/javascript" src="../../templates/origine/lib/fonction_change_ordre_menu.js"></script>

	<link rel="stylesheet" type="text/css" href="../../templates/origine/css/bandeau.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../../templates/origine/css/gestion.css" media="screen" />

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../../templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='../../templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../../templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../../templates/origine/css/accueil_ie7.css' media='screen' />
	<![endif]-->


<!-- Style_screen_ajout.css -->
	<?php
		if (count($Style_CSS)) {
			foreach ($Style_CSS as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
				}
			}
		}
	?>

<!-- Fin des styles -->



</head>


<!-- ************************* -->
<!-- D�but du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php echo $tbs_charger_observeur;?>">

<!-- on inclut le bandeau -->
	<?php include('../../templates/origine/bandeau_template.php');?>

<!-- fin bandeau_template.html      -->

  <div id='container'>
<!-- Fin haut de page -->
  
  <form action="index.php" id="form1" method="post">
	
	<p class="center grandEspaceHaut">
	  <input type="submit" value="Enregistrer"/>
	</p>
	
	<h2 class="colleHaut">Gestion des absences par les CPE</h2>
	<p>
	  <em>
		La d�sactivation du module de la gestion des absences n'entra�ne aucune suppression des donn�es. 
		Lorsque le module est d�sactiv�, les CPE n'ont pas acc�s au module.
	  </em>
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activation CPE</legend>
	  <input type="radio" 
			 id="activerY" 
			 name="activer" 
			 value="y"
			<?php if (getSettingValue("active_module_absence")=='y') echo ' checked="checked"'; ?> />
	  <label for="activerY">
		Activer le module de la gestion des absences
	  </label>
	  <br />
	  <input type="radio" 
			 id="activerN" 
			 name="activer" 
			 value="n"
			<?php if (getSettingValue("active_module_absence")!='y') echo ' checked="checked"'; ?> />
	  <label for="activerN">
		D�sactiver le module de la gestion des absences
	  </label>
	  <input type="hidden" name="is_posted" value="1" />
	</fieldset>
	
	<h2>Saisie des absences par les professeurs</h2>
	<p>
	  <em>
		La d�sactivation du module de la gestion des absences n'entra�ne aucune suppression des donn�es saisies 
		par les professeurs. Lorsque le module est d�sactiv�, les professeurs n'ont pas acc�s au module.
		Normalement, ce module ne devrait �tre activ� que si le module ci-dessus est lui-m�me activ�.
	  </em>
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activation professeurs</legend>
	  <input type="radio" 
			 id="activerProfY" 
			 name="activer_prof" 
			 value="y"
			<?php if (getSettingValue("active_module_absence_professeur")=='y') echo " checked='checked'"; ?> />
	  <label for="activerProfY">
		Activer le module de la saisie des absences par les professeurs
	  </label>
	  <a href="./interface_abs.php">
		Param�trer l'interface des professeurs
	  </a>
	  <br />
	  <input type="radio" 
			 id="activerProfN" 
			 name="activer_prof" 
			 value="n"
			<?php if (getSettingValue("active_module_absence_professeur")=='n') echo " checked='checked'"; ?> />
	  <label for="activerProfN">
		D�sactiver le module de la saisie des absences par les professeurs
	  </label>
	  <!-- <input type="hidden" name="is_posted" value="1" /> -->
	</fieldset>
	
	<h2>G�rer l'acc�s des responsables d'�l�ves</h2>
	<p>
	  <em>
		Vous pouvez permettre aux responsables d'acc�der aux donn�es brutes entr�es dans Gepi par le biais 
		du module absences.
	  </em>
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activation responsables d'�l�ves</legend>
	  <input type="radio" 
			 id="activerRespOk" 
			 name="activer_resp" 
			 value="y"
			<?php if (getSettingValue("active_absences_parents") == 'y') echo ' checked="checked"'; ?> />
	  <label for="activerRespOk">
		Permettre l'acc�s aux responsables
	  </label>
	  <br />
	  <input type="radio" 
			 id="activerRespKo" 
			 name="activer_resp" 
			 value="n"
			<?php if (getSettingValue("active_absences_parents") == 'n') echo ' checked="checked"'; ?> />
	  <label for="activerRespKo">
		Ne pas permettre cet acc�s
	  </label>
	</fieldset>
	
	<h2>Param�trer le classement des absences (par d�faut TOP 10)</h2>
	<p>
	  <label for="classement">Nombre de lignes pour le classement</label>
	  <select id="classement" name="classement">
		<option value="10"<?php echo $selected10; ?>>10</option>
		<option value="20"<?php echo $selected20; ?>>20</option>
		<option value="30"<?php echo $selected30; ?>>30</option>
		<option value="40"<?php echo $selected40; ?>>40</option>
		<option value="50"<?php echo $selected50; ?>>50</option>
	  </select>
	</p>

	<p class="center">
	  <input type="submit" value="Enregistrer"/>
	</p>
  </form>
  
  <h2>Configuration avanc�e</h2>
	<blockquote>
  <p>
	  <a href="../../edt_organisation/admin_horaire_ouverture.php?action=visualiser">
		D�finir les horaires d'ouverture de l'�tablissement
	  </a>
	  <br />
	  <a href="../../edt_organisation/admin_periodes_absences.php?action=visualiser">
		D�finir les cr�neaux horaires
	  </a>
	  <br />
<?php 
if (count($lien_sup)){
  foreach ($lien_sup as $lien){
?>
	  <a href="<?php echo $lien['adresse'];?>">
		<?php echo $lien['texte'];?>
	  </a>
	  <br />
<?php 	
  }
  unset ($lien);
}
?>
	  <a href="../../edt_organisation/admin_config_semaines.php?action=visualiser">
		D�finir les types de semaine
	  </a>
	  <br />
	  <a href="admin_motifs_absences.php?action=visualiser">
		D�finir les motifs des absences
	  </a>
	  <br />
	  <a href="admin_actions_absences.php?action=visualiser">
		D�finir les actions sur le suivi des �l�ves
	  </a>
  </p>
	</blockquote>


<!-- D�but du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
	  //<![CDATA[
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
	  //]]>
	</script>


	<script type='text/javascript'>
	  //<![CDATA[
		temporisation_chargement='ok';
	  //]]>
	</script>

</div>

		<?php
			if ($tbs_microtime!="") {
				echo "
   <p class='microtime'>Page g�n�r�e en ";
   			echo $tbs_microtime;
				echo " sec</p>
   			";
	}
?>

		<?php
			if ($tbs_pmv!="") {
				echo "
	<script type='text/javascript'>
		//<![CDATA[
   			";
				echo $tbs_pmv;
				echo "
		//]]>
	</script>
   			";

	}
?>

</body>
</html>

