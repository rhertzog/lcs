<?php
/*
 * $Id: bandeau_template.php 7902 2011-08-22 14:02:17Z regis $
*/
?>

<!-- ************************* -->
<!-- D�but du corps de la page -->
<!-- ************************* -->

<!-- D�but bandeau -->
<!-- Initialisation du bandeau � la bonne couleur -->
	<div id='bandeau' class="<?php echo $tbs_modif_bandeau.' '.$tbs_degrade_entete.' '.$tbs_modif_bandeau.'_'.$tbs_degrade_entete; ?>">
	
<a href="#contenu" class="invisible"> Aller au contenu </a>
		
	<!-- Page title, access rights -->
	<!-- User name, status, main matter, home, logout, account management -->

<div class="bandeau_colonne">
	<!-- Bouton r�tr�cir le bandeau -->
		<a class='change_taille_gd' href="#" onclick="modifier_taille_bandeau();change_mode_header('y', '<?php echo $tbs_bouton_taille;?>');return false;">
			<img src="<?php echo $tbs_bouton_taille;?>/images/up.png" alt='Cacher le bandeau' title='Cacher le bandeau' />
		</a>
	<!-- Bouton agrandir le bandeau -->
		<a class='change_taille_pt' href="#" onclick="modifier_taille_bandeau();change_mode_header('n', '<?php echo $tbs_bouton_taille;?>');return false;">
			<img src="<?php echo $tbs_bouton_taille;?>/images/down.png" alt='Afficher le bandeau' title='Afficher le bandeau' />
		</a>

		<?php
			//=====================================
			if($tbs_afficher_temoin_filtrage_html=='y') {
				if($filtrage_html=='htmlpurifier') {
					echo " <img src='$gepiPath/images/bulle_verte.png' width='9' height='9' alt='Filtrage HTML avec HTMLPurifier' title='Filtrage HTML avec HTMLPurifier' />\n";
				}
				elseif($filtrage_html=='inputfilter') {
					echo " <img src='$gepiPath/images/bulle_bleue.png' width='9' height='9' alt='Filtrage HTML avec InputFilter' title='Filtrage HTML avec InputFilter' />\n";
				}
				else {
					echo " <img src='$gepiPath/images/bulle_rouge.png' width='9' height='9' alt='Pas de filtrage HTML' title='Pas de filtrage HTML' />\n";
				}
			}
			//=====================================
		?>

	<!-- titre de la page -->	
		<h1><?php echo $titre_page; ?></h1>
		
	<!-- Derni�re connexion -->
		<!-- <p id='dern_connect'> -->
		<?php
			if ($tbs_last_connection!=""){
				echo "
				<p class='colonne1'>
					$tbs_last_connection
				</p>
				";
			}
		?>
		
	<!-- num�ro de version	 -->
		<p class="rouge">
			<?php echo $tbs_version_gepi; ?>
		</p>
</div>

<div class="bandeau_colonne" id="bd_colonne_droite">
	<!-- Nom pr�nom -->
		<p id='bd_nom'>
			<?php echo $tbs_nom_prenom; ?>
		</p>
	
	<!-- statut utilisateur -->
		<?php
			if (count($tbs_statut)) {
				foreach ($tbs_statut as $value) {	
					echo "
	<p>
		<span class='$value[classe]'>
			$value[texte]
					";
					if (count($donnees_enfant)) {
						foreach ($donnees_enfant as $value2) {	
							echo "
				
						$value2[nom] (<em>$value2[classe]</em>)
							";
						}
						unset($value2);
					}
					echo "
		</span>
	</p> 
					";
				}
				unset($value);
			}
		?>
	
	<!-- On v�rifie si le module de mise � jour est activ� -->
		
		<?php
			if ($tbs_mise_a_jour !="") {
				echo "
	<a href='javascript:ouvre_popup()'>
		<img style='border: 0px; width: 15px; height: 15px;' src='$tbs_mise_a_jour/images/info.png' alt='info' title='info' align='top' />
	</a>
				";
			}
		?>
	
	
	<!-- 	christian -->
	<!-- 	menus de droite -->
	<!-- 	menu accueil -->
	<!-- <ol id='premier_menu'> -->
	<ol>
		<?php
			if (count($tbs_premier_menu)) {
				foreach ($tbs_premier_menu as $value) {
					if ("$value[texte]"!="") {
						echo "
	<li class='ligne_premier_menu'>
		<a href='$value[lien]'>
			<img src='$value[image]' alt='$value[alt]' title='$value[title]' height='16' width='16' />
			<span class='menu_bandeau'>
				&nbsp;$value[texte]
			</span>
		</a>
	</li>
						";
					}
				}
				unset($value);
			}
		?>
	</ol>
		
	<!-- s�pare les 2 menus -->
		<!-- <div class='spacer'> </div> -->
	
	<!-- menu contact	 -->
		<!-- <ol id='deux_menu'> -->
		<ol id="bandeau_menu_deux">
		<?php
			if (count($tbs_deux_menu)) {
				foreach ($tbs_deux_menu as $value) {
					if ("$value[texte]"!="") {
						echo "
	<li class='ligne_deux_menu'>
		<a href='$value[lien]' $value[onclick] title=\"Nouvelle fen�tre\">
			$value[texte]
		</a>
	</li>
						";
					}
				}
				unset($value);
			}
		?>
		</ol>
	
</div>		

</div>
	
<?php

	echo '<!--[if lt IE 7]>
<script type=text/javascript>
	// Fonction destin�e � remplacer le "li:hover" pour IE 6
	sfHover = function() {
		var sfEls = document.getElementById("menu_barre").getElementsByTagName("li");
		for (var i=0; i<sfEls.length; i++) {
			sfEls[i].onmouseover = function() {
				this.className = this.className.replace(new RegExp(" sfhover"), "");
				this.className += " sfhover";
			}
			sfEls[i].onmouseout = function() {
				this.className = this.className.replace(new RegExp(" sfhover"), "");
			}
		}
	}
	if (window.attachEvent) window.attachEvent("onload", sfHover);
</script>

<style type="text/css">#menu_barre li {
	width: 164px;
}
</style>
<![endif]-->
';

    /**
     *
     * @global string
     * @global string
     * @param type $tab
     * @param type $niveau 
     */
	function ligne_menu_barre($tab,$niveau) {
		global $gepiPath, $themessage;
             
            $afficheTitle='';
            if (isset ($tab['title']) && $tab['title'] !='') {
              $afficheTitle= ' title=\''.$tab['title'].'\'';
            }

		if(isset($tab['sous_menu'])) {
			echo "<li";
			if($niveau==1) {
				echo " class='li_inline'";
			}
			else {
				echo " class='plus'";
			}
			echo ">\n";
			//echo "<a href=\"$gepiPath/".$tab['lien']." ".insert_confirm_abandon()."\">".$tab['texte']."</a>\n";

			// �ventuellement le lien peut �tre vide
			if ($tab['lien']=="") {
				echo $tab['texte']."\n";
			}
			elseif (substr($tab['lien'],0,4) == 'http') {
				echo "<a href=\"".$tab['lien']."\"".insert_confirm_abandon().$afficheTitle.">".$tab['texte']."</a>\n";
			}
			else {
				echo "<a href=\"$gepiPath".$tab['lien']."\"".insert_confirm_abandon().$afficheTitle.">".$tab['texte']."</a>\n";
			}

			echo "<ul class='niveau".$tab['niveau_sous_menu']."'>\n";
			for($i=0;$i<count($tab['sous_menu']);$i++) {
				ligne_menu_barre($tab['sous_menu'][$i], $tab['niveau_sous_menu']);
			}
			echo "</ul>\n";
			echo "</li>\n";
		}
		else {
			echo "<li";
			if($niveau==1) {
				echo " class='li_inline'";
			}
			echo ">";
			//echo "<a href=\"$gepiPath/".$tab['lien']."\" ".insert_confirm_abandon().">".$tab['texte']."</a>";

			// �ventuellement le lien peut �tre vide
			if ($tab['lien']=="") {
				echo $tab['texte']."\n";
			}
			elseif (substr($tab['lien'],0,4) == 'http') {
				echo "<a href=\"".$tab['lien']."\"".insert_confirm_abandon().$afficheTitle.">".$tab['texte']."</a>\n";
			}
			else {
				echo "<a href=\"$gepiPath".$tab['lien']."\"".insert_confirm_abandon().$afficheTitle.">".$tab['texte']."</a>";
			}
			echo "</li>\n";
		}
	}
?>
	
<!-- menu prof -->
<?php
	if (count($tbs_menu_prof)>0) {
		echo "<div id='menu_barre'>\n";
		echo "<ul class='niveau1'>\n";
		foreach($tbs_menu_prof as $key => $value) {
			//echo "<pre>\$tbs_menu_prof[$i]:<br />".print_r($tbs_menu_prof[$i])."</pre>";
			ligne_menu_barre($value,1);
		}
		echo "</ul>\n";
		echo "</div>\n";
	}
?>

<!-- menu admin -->
<?php if (count($tbs_menu_admin)) : ?>
<div id="menu_barre">
	<div class="menu_barre_bottom"></div>
	<div class="menu_barre_container">
		<ul class="niveau1">
			<?php foreach ($tbs_menu_admin as $value) { if ("$value[li]"!="") { ?>
			<?php echo $value['li']; ?>
			<?php }} unset($value); ?>
		</ul>
	</div>
</div>
<?php endif ?>

<!-- menu scolarit� -->
<?php if (count($tbs_menu_scol)) : ?>
<div id="menu_barre">
	<div class="menu_barre_bottom"></div>
	<div class="menu_barre_container">
		<ul class="niveau1">
			<?php foreach ($tbs_menu_scol as $value) { if ("$value[li]"!="") { ?>
			<?php echo $value['li']; ?>
			<?php }} unset($value); ?>
		</ul>
	</div>
</div>
<?php endif ?>

<!-- menu cpe -->
<?php if (count($tbs_menu_cpe)) : ?>
<div id="menu_barre">
	<div class="menu_barre_bottom"></div>
	<div class="menu_barre_container">
		<ul class="niveau1">
			<?php foreach ($tbs_menu_cpe as $value) { if ("$value[li]"!="") { ?>
			<?php echo $value['li']; ?>
			<?php }} unset($value); ?>
		</ul>
	</div>
</div>
<?php endif ?>


<!-- fil d'ariane -->
<?php
  if (isset($messageEnregistrer) && $messageEnregistrer !="" ){
	affiche_ariane(TRUE,$messageEnregistrer);
  }else{
	if(isset($_SESSION['ariane']) && (count($_SESSION['ariane']['lien'])>1)){
	  affiche_ariane();
	}
  }
?>
<!-- fin fil d'ariane -->
	
	
<!-- message -->
<?php
			if ($tbs_msg !="") {
?>
	<p class='headerMessage bold<?php if($post_reussi) echo " vert" ;?>'>
<?php
		echo $tbs_msg;
?>

	</p>
<?php
			}
?>
