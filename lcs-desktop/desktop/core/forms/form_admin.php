<?php
require  "/var/www/lcs/includes/headerauth.inc.php";
require "/var/www/Annu/includes/ldap.inc.php";
list ($idpers, $login)= isauth();
?>
<div id="" class="jqd_formulaires ctn-form-left" style="background-color:#660000;">
<?php
	if ( acces_btn_admin($idpers, $login) == "Y") { // acces au menu d'administration
?>
		<ul style="" class="clear_both ul2cols">
<!--
			<li>
				<label for="dispIcons4">Icones par défaut affichées sur le bureau à la première connexion</label>
				<select id="dispIcons4" name="dispIcons4" multiple="multiple" rows="7" style="width:98%;height:100px;font-size:10px;">
				</select>
			<div>
			<input type="checkbox" id="selectAllApplis4" name="selectAllApplis4" value="all"> 
			<label for="selectAllApplis4">Tout sélectionner</label>
			</div>
			</li>
-->
			<li>
				<label for="maintUrl">Saisissez l'url ou l'adresse courriel à ouvrir lors d'un appel à la maintenance (<small>Clic sur l'icone "clé" dans la barre de menu</small>)</label>
				<input type="text" name="maintUrl" id="maintUrl" style="width:98%;" value=""/>			
			</li>
			<li>
				<label for="showGroups">Ne pas afficher les groupes secondaires dans les informations utilisateur pour les membres du groupe Elèves(<small>Classes, Cours, etc</small>)</label>
				<input type="checkbox" name="showGroups" id="showGroups"  value=""/>			
			</li>
			<li>
				<label for="notifForumFreq">Fréquence d'affichage de la notification d'édition d'un nouvel article du forum </label>
				<input type="text" name="notifForumFreq" id="notifForumFreq" style="width:25px;" maxlength="3" value="10"/>
				<span> minutes</span>			
			</li>
		</ul>
<script>
</script>
</div>
<div id="contentCommentIcon" class="jqd_formulaires ctn-form-right" style="background-color:#660000;">
	<p><ul><li>Cette configuration s'applique  lors de la première connexion d'un utilisateur. </li><li>Si l'utilisateur ne modifie pas ses préférences, c'est donc celle-ci qui sera enregistrée comme ses préférences perso </li></ul><br />
	</p>
<div><h3>Suppressions</h3>
	<ul class="clear_both ul2cols">
		<li style="padding-left:0;">
				<input type="button" name="delIcons" id="delIcons"  value="Supprimer les icônes par défaut"/>			
		</li>
		<li style="padding-left:0;">
				<input type="button" name="delIcons" id="delIcons"  value="Supprimer la configuration par défaut"/>			
		</li>
	</ul>
</div>
</div>

<script>
	$(document).ready(function(){
		$('#maintUrl').attr( 'value', JQD.options.prms.maintUrl ).change(function(){
			JQD.options.prms.maintUrl = $(this).val();
		});
		$('#notifForumFreq').attr( 'value', JQD.options.prms.notifForumFreq ).change(function(){
			JQD.options.prms.notifForumFreq= $(this).val();
		});
		$('#showGroups').change(function(){
			JQD.options.prms.showGroups = $(this).is(':checked') ? 1: 0;
		});
		$('#showGroups').attr('checked', parseInt(JQD.options.prms.showGroups)==1 ? 'checked':'')? 
		
	});
</script>
<?php
	}
	else {
?>
<div id="" class="jqd_formulaires ctn-form-left" style="background-color:#660000;">
<p><ul><li>Vous devez êrtre administrateur pour accéder à cette page</li></ul></p>
</div>
<?php
	}
?>
</div>
