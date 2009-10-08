<?php require_once('../include/common.inc.php'); ?>
<?php extract($_POST); ?>
<?php extract($_GET); ?>

	<BR /><p>Bienvenue sur le HelpDesk de l'academie de Basse Normandie.
	Votre &eacute;tablissement a &eacute;t&eacute; authentifi&eacute; et nous avons v&eacute;rifi&eacute; votre statut d'administrateur avec succ&egrave;s. Ceci signifie que vous avez acc&egrave;s nativement &agrave; ce service.
	Cet espace vous permet d'ouvrir un ticket sur notre gestion Acad&eacute;mique.</p>

	<?php if ($mustRegister)  { ?>
			<BR />
			<H1>Merci de vous inscrire.</H1>
			<BR /><dd><label for="login">Identifiant:   </label></dd><input readonly="true" id="login" class="decal" value="<?php echo $login.'@'.$domain ?>"></input>
			<BR /><dd><label for="mail2">Mail de secours:   </label></dd><input id="mail2" class="decal" value="<?php echo 'prenom.nom@ac-caen.fr' ?>"></input>
			<BR /><dd><label for="nom">Nom:             </label></dd><input id="nom" class="decal" value="<?php echo $array_user['nom'] ?>"></input>
			<BR /><dd><label for="prenom">Prenom:          </label></dd><input id="prenom" class="decal" value="<?php echo $array_user['prenom'] ?>"></input>
			<BR /><dd><label for="passwd">Mot de passe: </label></dd><input id="passwd" class="decal" type="password" value="wawa"></input>
			<BR /><dd><label for="passwd2">Mot de passe: </label></dd><input id="passwd2" class="decal" type="password" value="wawa"></input>
			<BR />
				<BR /><input id="submit_register" type="button" value="S'inscrire">
			<BR />

	<?php } else { ?> 
		<BR /><A href="#" id="openTicket">Ouvrir un nouveau ticket.</A>
		<BR />
		<BR /><H1>Vos tickets:</H1><div id="mes_tickets"><img src="/lcs/images/spinner.gif"></img></div>
	<?php } ?>

