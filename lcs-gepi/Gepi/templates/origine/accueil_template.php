<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
 * $Id: accueil_template.php 6697 2011-03-25 21:54:27Z regis $
*/
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'ent�te -->
	<?php include('templates/origine/header_template.php');?>

	<link rel="stylesheet" type="text/css" href="./templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="./templates/origine/css/bandeau.css" media="screen" />

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='./templates/origine/css/accueil_ie7.css' media='screen' />
	<![endif]-->

<!-- Style_screen_ajout.css -->
	<?php
		if (count($Style_CSS)) {
			foreach ($Style_CSS as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
				}
			}
			unset($value);
		}
	?>

<!-- Fin des styles -->


</head>

<!-- ******************************************** -->
<!-- Appelle les sous-mod�les                     -->
<!-- templates/origine/header_template.php        -->
<!-- templates/origine/accueil_menu_template.php  -->
<!-- templates/origine/bandeau_template.php      -->
<!-- ******************************************** -->

<!-- ************************* -->
<!-- D�but du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php if($tbs_charger_observeur) echo $tbs_charger_observeur;?>">

<!-- on inclut le bandeau -->
	<?php include('templates/origine/bandeau_template.php');?>

<!-- fin bandeau_template.html      -->

<div id='container'>

<a name='haut_de_page'></a>

<div class='fixeMilieuDroit'>
	<a href='#haut_de_page'><img src='images/up.png' width='18' height='18' alt="haut de la page" title="Remonter en haut de la page" /></a>
	<br />
	<a href='#bas_de_page'><img src='images/down.png' width='18' height='18' alt="bas de la page" title="Descendre en bas de la page" /></a>
</div>

<!-- droits dossiers -->

<?php
	if (count($afficheAccueil->message_admin)){
		foreach ($afficheAccueil->message_admin as $value) {
			if ($value != "") {
?>
	<p class="rouge center">
		<?php echo $value; ?>
	</p>
<?php
			}
		}
		unset ($value);
	}
?>

<!-- messages connections -->
	<div>

<!-- Connexions	-->
<?php
	if ($afficheAccueil->gere_connect==1) {
?>
	  <p>
		Nombre de personnes actuellement connect�es :
		<?php
			if($afficheAccueil->nb_connect>1) {
				echo "<a style='font-weight:bold;' href='$afficheAccueil->nb_connect_lien' onmouseover=\"delais_afficher_div('personnes_connectees','y',-10,20,500,20,20);\">$afficheAccueil->nb_connect</a>";
			}
			else {
				echo "<b>".$afficheAccueil->nb_connect."</b>";
			}
		?>
		(
		<a href = 'gestion/gestion_connect.php?mode_navig=accueil'>
		  Gestion des connexions
		</a>
		)
	  </p>
<?php
	}
?>

<!-- Alertes s�curit�s	-->
<?php
	if ($afficheAccueil->alert_sums>0) {
?>
	  <p>
		Alertes s�curit� (niveaux cumul�s) : <?php echo "<b>".$afficheAccueil->alert_sums."</b>"; ?> (
		<a href='gestion/security_panel.php'>Panneau de contr�le</a>)
	  </p>
<?php
	}
?>

<!-- R�f�rencement	-->

<?php
	if (count($afficheAccueil->referencement)) {
	  foreach ($afficheAccueil->referencement as $value) {
?>
		<p class='referencement'>
		Votre �tablissement n'est pas r�f�renc� parmi les utilisateurs de Gepi.
		<span>
			<br />
			<a href="javascript:ouvre_popup_reference('<?php echo $value['lien'];?>')" title="<?php echo $value['titre'];?>">
				<?php echo $value['titre']; ?>
			</a>
		</span>
		</p>
<?php
	  }
	  unset($value);
	}
?>

<!-- messages de s�curit� -->
<?php
	if (count($afficheAccueil->probleme_dir)) {
	
	  foreach ($afficheAccueil->probleme_dir as $value) {
?>
		<p  class="rouge center">
			<?php echo $value; ?>
		</p>

<?php
	  }
	  unset($value);
	}
?>
	
<!-- erreurs d'affectation d'�l�ves -->


	</div>
	<a name="contenu" class="invisible">D�but de la page</a>

<!-- Signalements d'erreurs d'affectations -->
<?php
	if((isset($afficheAccueil->signalement))&&($afficheAccueil->signalement!="")) {
?>
	  <div class='infobulle_corps' style='text-align:center; margin: 3em; padding:0.5em; color:red; border: 1px dashed red;'>
		<?php echo $afficheAccueil->signalement; ?>
	  </div>

<?php
	}
?>

<!-- Actions � effectuer -->
<?php
	affiche_infos_actions();
?>

<!-- Acc�s CDT ouverts -->
<?php
	affiche_acces_cdt();
?>

<!-- messagerie -->
<?php
	if (count($afficheAccueil->message)) {
?>
	  <div id='messagerie'>
<?php
		  foreach ($afficheAccueil->message as $value) {

		  if ($value['suite']=='') {
			  echo "";
		  }else{
			  echo "<hr>";
		  }
		  echo "
			$value[message]
		  ";
		  if ($value['suite']=='') {
			  echo "";
		  }else{
			  echo "</hr>";
		  }

		}
		unset ($value);
?>
		</div>
<?php
	}

	if ($_SESSION['statut'] =="professeur") {
?>
		<p class='bold'>
		  <a href='accueil_simpl_prof.php'>
			Interface graphique
		  </a>
		</p>
<?php
	}
?>

<!-- d�but corps menu	-->


	<!-- menu	g�n�ral -->

	<?php
	if (count($afficheAccueil->titre_Menu)) {
	  foreach ($afficheAccueil->titre_Menu as $newEntreeMenu) {
      if ($newEntreeMenu->texte!='bloc_invisible') {
?>
		<h2 class="<?php echo $newEntreeMenu->classe ?>">
			<img src="<?php echo $newEntreeMenu->icone['chemin'] ?>" alt="<?php echo $newEntreeMenu->icone['alt'] ?>" /> - <?php echo $newEntreeMenu->texte ?>
		</h2>


<?php
		if ($newEntreeMenu->texte=="Votre flux RSS") {
?>
		  <div class='div_tableau'>
<?php
		  if ($afficheAccueil->canal_rss["mode"]==1) {
?>
			<h3 class="colonne ie_gauche flux_rss" title="A utiliser avec un lecteur de flux rss" onclick="changementDisplay('divuri', 'divexpli')" >
			  Votre uri pour les cahiers de textes
			</h3>
			<p class="colonne ie_droite vert">
			  <span id="divexpli" style="display: block;">
				<?php echo $afficheAccueil->canal_rss['expli']; ?>
			  </span>
			  <span id="divuri" style="display: none;">
				<a href="<?php echo $afficheAccueil->canal_rss['lien']; ?>" onclick="window.open(this.href, '_blank'); return false;" >
				  <?php echo $afficheAccueil->canal_rss['texte']; ?>
				</a>
			  </span>
			</p>

<?php
		  }else if ($afficheAccueil->canal_rss["mode"]==2){
?>
			<h3 class="colonne ie_gauche">
			  Votre uri pour les cahiers de textes
			</h3>
			<p class="colonne ie_droite vert">
			  Veuillez la demander � l'administration de votre �tablissement.
			</p>
<?php
		  }
?>
		  </div>
<?php
		}else{
		  if (count($afficheAccueil->menu_item)) {
			foreach ($afficheAccueil->menu_item as $newentree) {
			  if ($newentree->indexMenu==$newEntreeMenu->indexMenu) {
?>
				<div class='div_tableau'>

<?php
				  if ($newentree->titre=="Sauvegarde de la base") {
?>
	<div class="div_tableau cellule_1">
		<form enctype="multipart/form-data" action="gestion/accueil_sauve.php" method="post" id="formulaire" >
			<p>
				<?php
					echo add_token_field();
				?>
				<input type='hidden' name='action' value='system_dump' />
				<input type="submit" value="Lancer une sauvegarde de la base de donn�es" />
			</p>
		</form>
		<p class='small'>
			Les r�pertoires "documents" (<em>contenant les documents joints aux cahiers de texte</em>) et "photos" (<em>contenant les photos du trombinoscope</em>) ne seront pas sauvegard�s.<br />
			Un outil de sauvegarde sp�cifique se trouve en bas de la page <a href='./gestion/accueil_sauve.php#zip'>gestion des sauvegardes</a>.
		</p>
	</div>
<?php
			  }else{
?>

				  <h3 class="colonne ie_gauche">
					  <a href="<?php echo substr($newentree->chemin,1) ?>">
						  <?php echo $newentree->titre ?>
					  </a>
				  </h3>
				  <p class="colonne ie_droite">
					  <?php echo $newentree->expli ?>
				  </p>
<?php
			  }
?>
				</div>
<?php
			  }
			}
			}
			unset($newentree);
		  }
		}
	  }
	  unset($newEntreeMenu);
	}
?>

<!-- d�but RSS	-->
		<?php
/*


			if ($tbs_canal_rss_flux==1) {
							echo "
	<div>
		<h2 class='accueil'>
			<img src='./images/icons/rss.png' alt=''/> - Votre flux rss
		</h2>
				";

		echo "
<div class='div_tableau'>
			";
		if ($tbs_canal_rss[0]["mode"]==1) {
			echo "
	<h3 class=\"colonne ie_gauche flux_rss\" title=\"A utiliser avec un lecteur de flux rss\" onclick=\"changementDisplay('divuri', 'divexpli')\" >
		Votre uri pour les cahiers de textes
	</h3>
	<p class=\"colonne ie_droite vert\">
		<span id=\"divexpli\" style=\"display: block;\">
				";
				echo $tbs_canal_rss[0]['expli'];
				echo "
		</span>
		<span id=\"divuri\" style=\"display: none;\">
	<a onclick=\"window.open(this.href, '_blank'); return false;\" href=\""; echo $tbs_canal_rss[0]['lien']; echo";\">
							"; echo $tbs_canal_rss[0]['texte']; echo"
	</a>
		</span>
	</p>

</div>
	</div>
				";
		}else if ($tbs_canal_rss[0]["mode"]==2){
			echo "
	<h3 class=\"colonne ie_gauche\">
			Votre uri pour les cahiers de textes
	</h3>
	<p class=\"colonne ie_droite vert\">
					Veuillez la demander � l'administration de votre �tablissement.
	</p>
				";
		}
	}
 *
 */
?>
<!-- fin RSS	-->

<!-- D�but du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>


<?php
	//if (count($tbs_nom_connecte)) {
	if (count($afficheAccueil->nom_connecte)) {
		//echo "
?>
	<div id='personnes_connectees' class='infobulle_corps' style='color: #000000; border: 1px solid #000000; padding: 0px; position: absolute; z-index:1; width: 20em; left:0em;'>
		<div class='infobulle_entete' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; width: 20em;' onmousedown="dragStart(event, 'personnes_connectees')">
			<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>
				<a href='#' onclick="cacher_div('personnes_connectees');return false;">
					<img src='./images/icons/close16.png' width='16' height='16' alt='Fermer' />
				</a>
			</div>
			<span style="padding-left: 1px;">
				Personnes connect�es
			</span>
		</div>
		<div>
			<div style="padding-left: 1px;">
				<div style="text-align:center;">
					<table class='boireaus'>
						<tr>
							<th>Personne</th>
							<th>Statut</th>
						</tr>
<?php
		foreach ($afficheAccueil->nom_connecte as $newentree) {
?>
						<tr  class='<?php echo $newentree['style']; ?>'>
							<td>
                               <a href='mailto:<?php echo $newentree['courriel']; ?>'>
									<?php echo $newentree['texte']; ?>
								</a>
							</td>
							<td>
								<?php echo $newentree['statut']; ?>
							</td>
						</tr>

<?php
		}
		unset($newentree);
?>

					</table>
				</div>
			</div>
		</div>
	</div>
<?php
//   			";
	}
?>

	<script type='text/javascript'>
		temporisation_chargement='ok';
	</script>

	<script type='text/javascript'>
	cacher_div('personnes_connectees');
	</script>


<a name='bas_de_page'></a>
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

