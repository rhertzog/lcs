<div class="jqd_formulaires">
	<div class="jqd_form_side">
		<form>
			<fieldset>
				<legend>Param&eacute;tres de la liste</legend>
			</fieldset>
				<ul class='add clear_both'>
					<li><label for="name_newlist">Nom du fichier</label>
						<input type="text" class='addValue' id="name_newlist" name="name_newlist" size="18" value="nom_du_fichier"/>
					</li>
					<li><label for="title_newlist">Titre de la liste</label>
						<input type="text" class='addValue' id="title_newlist" name="title_newlist" size="18" value="Ma liste"/>
					</li>
					<li><label for="comment_newlist"><small>Descriptif</small></label>
						<textarea class='addValue' id="comment_newlist" name="comment_newlist" rows="2" cols="15"> </textarea>
					</li>
					<li><label for="author_newlist"><small>Auteur</small>&nbsp;&nbsp;&nbsp;</label>
						<input type="text" class='addValue' id="author_newlist" name="author_newlist" size="18" value="<?php echo $user['fullname'];?>"/>
					</li>
					<!--
					<li><label for="referentiel_newlist"><small>Comp&eacute;tences vis&eacute;es</small></label>
						<input type="text" class='addValue' id="referentiel_newlist_newlist" name="referentiel_newlist_newlist" size="18" value=""/>
					</li>
					-->
				</ul>
		</form>
	<?php
		if($group_principal != 'Eleves'){
	?>
		<div>
			<h4>Attribuer aux classes</h4>
			<?php
			echo preg_replace('/Equipe_/', "", $equipes_select);
			if(is_array($equips)){
				echo '<div id="classhaslist" style="display:none;">';
				foreach($equips as $classe){
	//				echo $classe;
				echo "<ul class='".$classe."'>";
					scanxml('/var/www/lcs/desktop/xml/'.preg_replace('/Equipe_/', '',$classe));
				echo "</ul>";
				}
				echo '</div>';
			}
			?>
		</div>
	<?php
		}
	?>
		<div>
		<h4>Ajouter un &eacute;l&eacute;ment</h4>
		<ul id="rbMenu"  class="rb_menu">
			<li id="add_links"><img src="desktop/images/icons/new_label.png" />Lien</li>
			<li id="add_video"><img src="desktop/images/icons/new_image.png" />Video</li>		
		</ul>
		<p><small><em>Pour ajouter un &eacute;l&eacute;ment, cliquez sur l&rsquo;&eacute;l&eacute;ment choisi et glissez le dans la liste num&eacute;rot&eacute;e. </em></small></p>
		</div>
	</div>
	<div class="listes_config">
		<div class="add_link" id="addLinkList">
        <h1>Nom de ma liste</h1>
 			<ul id="my_links_list" class="list_add_link my_links_list">
				<li><span class="check unchecked"></span><span class="count rang"> </span><span class="cross"></span><a class="item" href="#" title="">Titre &agrave; modifier</a><span class="myurl" style="display:inline">http:// (double-clic pour modifier)</span><span class="check_sh share"></span><span class="float_right close"></span>
				</li>
			</ul>
		</div>
		<div id="mess_save_list" style="display:none;"></div>
		<p style="text-align:center;margin-top:20px;margin-left:-200px:" class="buttons">
			<span class="bouton"><a href="#" id="valid_list_links">Valider</a></span>
<!--			<span class="bouton"><a href="#" id="maj_list_links">Mettre &agrave; jour</a></span> -->
		</p>
	</div>
	<hr />
		<div id="all_my_lists">
		<h4>Toutes mes listes</h4>
		<?php
//		opendir('/var/www/lcs/desktop/xml/'.$login);
		echo "<ul class='activ_list' id='myPersonalLists'>";
		$t_classes= scanxml('/var/www/lcs/desktop/xml/'.$login);
		echo $t_classes[0];
		echo "</ul>";
		?>
		</div>
		<div class="help">
		Pour cr&eacute;er une liste, il faut :
		<ul>
		<li>
			Nommer cette liste dans le champ "Nom de la liste" situ&eacute; en haiut de la colonne de gauche (si elle n'est pas renomm&eacute;e, elle viendra &eacute;craser l'existante). 
		</li>
		<li>
			Modifier le titre et l'url du premier lien (double-clic sur le titre et l'url pour modifier)
		</li>
		<li>
			Ajouter des liens en glissant d&eacute;posant l'ic&ocirc;ne/nom Lien ou vid&eacute;o dans la liste ordonn&eacute;e et en compl&eacute;tant le formulaire associ&eacute; (seuls les champs url et nom sont pris en compte. Aucune diff&eacute;rence entre lien et video pour l'instant, l'id&eacute;e &eacute;tant d'ouvrir les vid&eacute;os dans un lecteur/fen&ecirc;tre).
		</li>
		<li>
			Ordonner la liste en d&eacute;pla&ccedil;ant les liens verticalement
		</li>
		<li>
			Attribuer la liste &agrave; une ou plusieurs classes si n&eacute;cessaire 
		</li>
		<li>
			Valider
		</li>
		</ul>
		Au rechargement de la page, la nouvelle liste appara&icirc;tra dans "Toutes mes liste".
		<p style="margin:30px;">
			<h5>l&eacute;gende des boutons des liens</h5>
			<ul>
			<li>
				<span class="check unchecked"></span><span class="check checked" style="position:relative;"></span>Situ&eacute; dans la liste de liens, il doit valider le lien dans la liste. Non op&eacute;rationnel.
			</li>
			<li>
				&nbsp;&nbsp;&nbsp;<span class="count">1</span>&nbsp;&nbsp;&nbsp;Rang du lien.
			</li>
			<li>
				<span class="cross"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Poign&eacute;e pour d&eacute;placer le lien dans la liste.
			</li>
			<li>
				<span class="share" style="right:auto;;margin-left:5px;"></span><span class="share_ok" style="right:auto;margin-left:30px;"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ouvre le lien dans une nouvelle fen&ecirc;tre du Lcs-Buro si valid&eacute;. Non op&eacute;rationnel
			</li>
			<li>
				<span class="close" style="right:auto;;margin-left:5px;"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Supprime le lien
			</li>
			</ul>
		</p>
		<br style="clear:both;" />
		</div>
</div>
<script>

</script>