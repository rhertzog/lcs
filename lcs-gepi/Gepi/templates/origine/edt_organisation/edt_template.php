<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: edt_template.php 5003 2010-08-03 21:43:14Z regis $
 * *
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
* ******************************************** *
* Appelle les sous-mod�les                     *
* templates/origine/header_template.php        *
* templates/origine/bandeau_template.php       *
* ******************************************** *
*/

/**
 *
 * @author regis
 */

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'ent�te -->
	<?php
	  $tbs_bouton_taille = "..";
	  include('../templates/origine/header_template.php');
	?>

  <script type="text/javascript" src="../templates/origine/lib/fonction_change_ordre_menu.js"></script>

	<link rel="stylesheet" type="text/css" href="../templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/gestion.css" media="screen" />

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie7.css' media='screen' />
	<![endif]-->


<!-- Style_screen_ajout.css -->
	<?php
		if (count($Style_CSS)) {
			foreach ($Style_CSS as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
				}
			}
			unset ($value);
		}
	?>

<!-- Fin des styles -->


</head>


<!-- ************************* -->
<!-- D�but du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php echo $tbs_charger_observeur;?>">

<!-- on inclut le bandeau -->
	<?php include('../templates/origine/bandeau_template.php');?>

<!-- fin bandeau_template.html      -->

  <div id='container'>
	
	<h2>Gestion des acc�s � l'emploi du temps</h2>
	
	<p>
	  (Tous les comptes sauf �l�ve et responsable)
	</p>
	
	<hr />
	
	<form action="edt.php" method="post" id="autorise_edt">
		<fieldset class="no_bordure">
		  <legend class="invisible">Activation de l'EDT</legend>
		  <em>
			La d�sactivation des emplois du temps n'entra�ne aucune suppression des donn�es. Lorsque le module
			est d�sactiv�, personne n'a acc�s au module et la consultation des emplois du temps est impossible.
		  </em>
		  <br />
		  
		  <input name="activ_tous"
				 id="activTous"
				 value="y"
				 type="radio"<?php echo eval_checked("autorise_edt_tous", "y"); ?>
				 onclick="document.getElementById('autorise_edt').submit();"
				 />
		  <label for="activTous">
			Activer les emplois du temps pour tous les utilisateurs
		  </label>
		  <br />
		  <input name="activ_tous"
				 id="activPas"
				 value="n"
				 type="radio"<?php echo eval_checked("autorise_edt_tous", "n"); ?>
				 onclick="document.getElementById('autorise_edt').submit();"
				 />
		  <label for="activPas">
			D�sactiver les emplois du temps pour tous les utilisateurs
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_active" />
		  </span>
		</fieldset>

	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_active').className = 'invisible';

		//]]>
	</script>

	<form action="edt.php" method="post" id="autorise_prof">
		<fieldset class="no_bordure grandEspaceHaut">
		  <legend class="invisible">Activation pour les enseignants</legend>
		  <input type="radio"
				 name="autorise_saisir_prof"
				 id="autoProf"
				 value="y"<?php echo eval_checked("edt_remplir_prof", "y"); ?>
				 onclick="document.getElementById('autorise_prof').submit();"
				 />
		  <label for="autoProf">
			Autoriser le professeur � saisir son emploi du temps
		  </label>
		  <br />

		  <input type="radio"
				 name="autorise_saisir_prof"
				 id="autoProfNon"
				 value="n"<?php echo eval_checked("edt_remplir_prof", "n"); ?>
				 onclick="document.getElementById('autorise_prof').submit();"
				 />
		  <label for="autoProfNon">
			Interdire au professeur de saisir son emploi du temps
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_prof" />
		  </span>
		</fieldset>
	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_prof').className = 'invisible';
		//]]>
	</script>
	
	<form action="edt.php" method="post" id="autorise_admin">
		<fieldset class="no_bordure grandEspaceHaut">
		  <legend class="invisible">Activation pour les administrateurs</legend>
		  <em>Les comptes </em>administrateur<em> ont acc�s aux emplois du temps si celui-ci est activ� pour eux.
		  Si vous avez d�sactiv�; l'acc�s pour tous, vous pouvez quand m�me autoriser les comptes
		  </em>administrateur<em> � y avoir acc�s.</em>
		  <br />
		  <input name="activ_ad"
				 id="activAdY"
				 value="y"
				 type="radio"<?php echo eval_checked("autorise_edt_admin", "y"); ?>
				 onclick="document.getElementById('autorise_admin').submit();"
				 class="grandEspaceHaut"
				 />
		  <label for="activAdY">
			Activer les emplois du temps pour les administrateurs
		  </label>

		  <br />
		  <input name="activ_ad"
				 id="activAdN"
				 value="n"
				 type="radio"<?php echo eval_checked("autorise_edt_admin", "n"); ?>
				 onclick="document.getElementById('autorise_admin').submit();"
				 />
		  <label for="activAdN">
			D�sactiver les emplois du temps pour les administrateurs
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_admin" />
		  </span>
		</fieldset>
	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_admin').className = 'invisible';
		//]]>
	</script>
	
	<hr />

	<h2>Gestion de l'acc�s pour les �l�ves et leurs responsables</h2>

	<form action="edt.php" method="post" id="autorise_ele">
	  <p>
		<em>
		  Si vous souhaitez rendre accessible leur emploi du temps aux �l�ves et � leurs responsables,
		  il faut imp�rativement l'autoriser ici.
		</em>
	  </p>

		<fieldset class="no_bordure grandEspaceHaut">
		  <legend class="invisible">Activation pour les �l�ves et leurs responsables</legend>
		  <input name="activ_ele"
				 id="activEleY"
				 value="yes"
				 type="radio"<?php echo eval_checked("autorise_edt_eleve", "yes"); ?>
				 onclick="document.getElementById('autorise_ele').submit();"
				 />
		  <label for="activEleY">
			Activer les emplois du temps pour les �l�ves et leurs responsables
		  </label>

		  <br />
		  <input name="activ_ele"
				 id="activEleN"
				 value="no"
				 type="radio"<?php echo eval_checked("autorise_edt_eleve", "no"); ?>
				 onclick="document.getElementById('autorise_ele').submit();"
				 />
		  <label for="activEleN">
			D�sactiver les emplois du temps pour les �l�ves et leurs responsables
		  </label>
		  <br />
		  <span class="block center">
			<input type="submit" value="Enregistrer" id="btn_eleve" />
		  </span>
		</fieldset>
	</form>

	<script type="text/javascript">
		//<![CDATA[
	  document.getElementById('btn_eleve').className = 'invisible';
		//]]>
	</script>
	
	
	
	
	


<!-- D�but du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>


	<script type='text/javascript'>
		temporisation_chargement='ok';
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

