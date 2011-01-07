<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id: index_template.php 6074 2010-12-08 15:43:17Z crob $
 *
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

  <form action="index.php" id="form1" method="post">
<?php
	echo add_token_field();
?>
	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer" />
	</p>
	
	<h2 class="colleHaut">Configuration g�n�rale</h2>
	<p class="italic">
	  La d�sactivation des carnets de notes n'entra�ne aucune suppression des donn�es. 
	  Lorsque le module est d�sactiv�, les professeurs n'ont pas acc�s au module.
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activ� ou non</legend>
	  <input type="radio" 
			 name="activer" 
			 id='activer_y' 
			 value="y" 
			<?php if (getSettingValue("active_carnets_notes")=='y') echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_y' style='cursor: pointer;'>
		Activer les carnets de notes
	  </label>
	<br />
	  <input type="radio" 
			 name="activer" 
			 id='activer_n' 
			 value="n" 
			<?php if (getSettingValue("active_carnets_notes")=='n') echo " checked='checked'"; ?>
			 onchange='changement();' />
	  <label for='activer_n' style='cursor: pointer;'>
		D�sactiver les carnets de notes
	  </label>
	</fieldset>
	
	<p class="grandEspaceHaut">
<?php
	if(file_exists("../lib/ss_zip.class.php")){ 
?>
	  <input type='checkbox' 
			 name='export_cn_ods'
			 id='export_cn_ods'
			 value='y'
			 onchange='changement();'
<?php
	  if(getSettingValue('export_cn_ods')=='y'){
?>
		checked="checked"
<?php
	  }
?>
	  />
	  <label for='export_cn_ods' style='cursor: pointer;'>
		Permettre l'export des carnets de notes au format ODS.
	  </label>
	  <br />
	  (<em>si les professeurs ne font pas le m�nage apr�s g�n�ration des exports, ces fichiers peuvent prendre de la place sur le serveur</em>)\n";
<?php
	}
	else{
?>
	  En mettant en place la biblioth�que 'ss_zip_.class.php' dans le dossier '/lib/', vous pouvez g�n�rer des fichiers tableur ODS pour permettre des saisies hors ligne, la conservation de donn�es,...
	  <br />
	  Voir <a href='http://smiledsoft.com/demos/phpzip/'>http://smiledsoft.com/demos/phpzip/</a>
	</p>
	<p>
	  Une version limit�e est disponible gratuitement.
	  <br />
	  Emplacement alternatif:
	  <a href='http://stephane.boireau.free.fr/informatique/gepi/ss_zip.class.php.zip'>
		http://stephane.boireau.free.fr/informatique/gepi/ss_zip.class.php.zip
	  </a>

<?php
	  // Comme la biblioth�que n'est pas pr�sente, on force la valeur � 'n':
	  $svg_param=saveSetting("export_cn_ods", 'n');
	}
?>
	</p>

	<h2>
	  R�f�rentiel des notes :
	</h2>
	<p>
	  R�f�rentiel des notes par d�faut : 
	  <input type="text" 
			 name="referentiel_note" 
			 size="8"
			 title="notes sur"
			 value="<?php echo(getSettingValue("referentiel_note")); ?>" />
	</p>
	<fieldset class="no_bordure">
	  <legend class="invisible">R�f�rentiel ou non</legend>
	  <input type="radio" 
			 name="note_autre_que_sur_referentiel" 
			 id="note_sur_referentiel" 
			 value="V" 
			 <?php if(getSettingValue("note_autre_que_sur_referentiel")=="V"){echo "checked='checked'";} ?> />
	  <label for='note_sur_referentiel'> 
		Autoriser les notes autre que sur le r�f�rentiel par d�faut
	  </label>
	  <br />
	  <input type="radio" 
			 name="note_autre_que_sur_referentiel" 
			 id="note_autre_que_referentiel" 
			 value="F" 
			 <?php if(getSettingValue("note_autre_que_sur_referentiel")=="F"){echo "checked='checked'";} ?> />
	  <label for='note_autre_que_referentiel'> 
		Notes uniquement sur le r�f�rentiel par d�faut
	  </label>
	</fieldset>

	<p class="center">
	  <input type="hidden" name="is_posted" value="1" />
	  <input type="submit" value="Enregistrer" />
	</p>

</form>






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


