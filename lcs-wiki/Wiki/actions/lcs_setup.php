<?php

/*
---------------------------------------------------------
Ajouté par le lycée laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/


if (!defined('WIKINI_VERSION'))
{
       exit("accès direct interdit");
}

if (! $this->UserInGroup("admins"))
{
  echo "<h3>Vous n'êtes pas autorisé à administrer le WIKI</h3>\n";
  exit;
}

if ($_POST["modifier"])
{	

	//Configuration qu'on vient de modifier
	$config_mod = $_POST["config"];


	//Configuration actuelle du wakka.config.php
	$config_actu = unserialize($_POST['config_actu']);


	//On crée un tableau qui mélange les deux en gardant la config modifiée
	$config_a_ecrire = array_merge($config_actu, $config_mod);

	// conversion vers le fichier php 
	$configCode = "<?php\n// wakka.config.php cr&eacute;&eacute;e ".strftime("%c")."\n// Ne changez pas la wikini_version manuellement!\n\nrequire (\"config.php\");\n\n\$wakkaConfig = array(\n";


	foreach ($config_a_ecrire as $k => $v)
	{
        	if (($k == "base_url")||(($k == "url_site"))) {
			$entries[] = "\t\"".$k."\" => ".$v."\"";
		}
		elseif ($k == "mysql_host"){
			$entries[] = "\t\"".$k."\" => ".$v;
		}
		else {
			$entries[] = "\t\"".$k."\" => \"".$v."\"";
		}
	}
	$configCode .= implode(",\n", $entries).");\n?>";

	// Essai pour écrire le fichier de configuration
	echo "<b>Modification du fichier de configuration en cours...</b><br>\n";

	function test($text, $condition, $errorText = "", $stopOnError = 1) {
        	echo "$text " ;
       		 if ($condition)
        	{
                	echo "<span class=\"ok\">OK</span><br>\n" ;
        	}
        	else
        	{
                	echo "<span class=\"failed\">ECHEC</span>" ;
                	if ($errorText) echo ": ",$errorText ;
                	echo "<br>\n" ;
                	if ($stopOnError) exit;
        	}
	}

	test("Modification du fichier de configuration <tt>".$configfile."</tt>...", $fp = @fopen("wakka.config.php", "w"), "", 0);

	if ($fp)
	{
        	fwrite($fp, $configCode);
       		 // write
        	fclose($fp);

        	echo "<p>Voici le fichier modifié : <br />";
		echo"<div style=\"background-color: #EEEEEE; padding: 10px 10px;\">\n<xmp>",$configCode,"</xmp>\n</div>\n";
		echo "<br /> Vous pouvez <a href=\"",$this->config["base_url"],"\">retourner sur votre site WikiNi</a>. Il est conseill&eacute;
de retirer l'acc&egrave;s en &eacute;criture au fichier <tt>wakka.config.php</tt>. Ceci peut &ecirc;tre une faille dans la s&eacute;curit&eacute;.</p>";
	}
	else
	{
        	// complain
       		 echo"<p><span class=\"failed\">AVERTISSEMENT:</span> Le
fichier de configuration <tt>,wakka.config.php,</tt> n'a pu &ecirc;tre
cr&eacute;&eacute;. Veuillez vous assurez que votre serveur a les droits d'acc&egrave;s en &eacute;criture pour ce fichier. Si pour une raison quelconque vous ne pouvez pas faire &ccedil;a vous devez copier les informations suivantes dans un fichier et les transf&eacute;rer au moyen d'un logiciel de transfert de fichier (ftp) sur le serveur dans un fichier <tt>wakka.config.php</tt> directement dans le r&eacute;pertoire de WikiNi. Une fois que vous aurez fait cela, votre site WikiNi devrait fonctionner correctement.</p>\n";
        	?>
		<form action="<?php echo $this->config['base_url']?>AdminGenerale" method="POST">
		<input type="hidden" name="config_actu" value="<?php echo  htmlentities(serialize($this->config)) ?>">
        	<input type="submit" value="Essayer &agrave; nouveau">
        	</form>
       		 <?php
       		 echo"<div style=\"background-color: #EEEEEE; padding: 10px 10px;\">\n<xmp>",$configCode,"</xmp>\n</div>\n";
	}

	}
else {
	?>

	<form action="<?php echo $this->config['base_url']?>AdminGenerale" method="POST">
	<table>

	<tr><td></td><td><b>Mise a jour du fichier "wakka.config.php"</b></td></tr>


	<tr><td></td><td><br>Ce programme d'installation va essayer de modifier les options de configurations dans le fichier <tt>wakka.config.php</tt>, situ&eacute; dans votre r&eacute;pertoire WikiNi. Pour que cela fonctionne, veuillez vous assurez que votre serveur a les droits d'acc&egrave;s en &eacute;criture pour ce fichier. Si pour une raison quelconque vous ne pouvez pas faire &ccedil;a vous devrez modifier ce fichier manuellement.<br /><b>NOTE : Il n'est permis de modifier &agrave; travers cette interface que des param&egrave;tres qui, th&eacute;oriquement, n'emp&ecirc;cheront pas le bon fonctionnement du syst&egrave;me.</b> </td></tr>


	<tr><td></td><td><br><b>Configuration de votre site WikiNi</b></td></tr>

	<tr><td></td><td>Le nom de votre site WikiNi. Ceci est g&eacute;n&eacute;ralement un NomWiki et EstSousCetteForme.</td></tr>
	<tr><td align="right" nowrap>Le nom de votre WikiNi :</td><td><input type="text" size="50" name="config[wakka_name]" value="<?php echo $this->config["wakka_name"] ?>"></td></tr>

	<tr><td></td><td>Le mode "redirection automatique" doit &ecirc;tre s&eacute;lectionn&eacute; uniquement si vous utilisez WikiNi avec la redirection d'URL (si vous ne savez pas ce qu'est la redirection d'URL n'activez pas cette option).</td></tr>
	<tr><td align="right" nowrap>Mode "redirection" :</td><td><input type="hidden" name="config[rewrite_mode]" value="0"><input type="checkbox" name="config[rewrite_mode]" value="1" <?php echo $this->config["rewrite_mode"] ? "checked" : "" ?>> Activation</td></tr>
	
	<tr><td></td><td>META Mots clefs/Description qui seront ins&eacute;r&eacute;s dans les codes HTML.</td></tr>
	<tr><td align="right" nowrap>Mots clefs :</td><td><input type="text" size="70" name="config[meta_keywords]" value="<?php echo $this->config["meta_keywords"] ?>"></td></tr>
	<tr><td align="right" nowrap>Description :</td><td><input type="text" size="70" name="config[meta_description]" value="<?php echo $this->config["meta_description"] ?>"></td></tr>

	<tr><td></td><td>Les liens visibles sur la barre de navigation en haut de la page.</td></tr>
        <tr><td align="right" nowrap>Navigation Links :</td><td><input type="text" size="70" name="config[navigation_links]" value="<?php echo $this->config["navigation_links"] ?>"></td></tr>

	<tr><td></td><td><br><b>Droits par défaut lorqu'une page est créée</b></td></tr>

	<tr><td></td><td>Trois possibilités : "*" pour des droits maximum (même les utilisateurs non authentifiés), "+" pour que seuls les utilisateurs authentifiés puisse se connecter et ne rien écrire pour un accès refusé à tout le monde sauf à l'auteur de la page </td></tr>
	<tr><td align="right" nowrap>Droit d'écriture :</td><td><input type="text" size="10" name="config[default_write_acl]" value="<?php echo $this->config["default_write_acl"] ?>"></td></tr>
	<tr><td align="right" nowrap>Droit de lecture :</td><td><input type="text" size="10" name="config[default_read_acl]" value="<?php echo $this->config["default_read_acl"] ?>"></td></tr>
	<tr><td align="right" nowrap>Droit de commentaire :</td><td><input type="text" size="10" name="config[default_comment_acl]" value="<?php echo $this->config["default_comment_acl"] ?>"></td></tr>


	<tr><td></td><td><br><b>Options suppl&eacute;mentaires</b></td></tr>	

	<tr><td align="right" nowrap>Aperçu obligatoire :</td><td><input type="hidden" name="config[preview_before_save]" value="0"><input type="checkbox" name="config[preview_before_save]" value="1" <?php echo $this->config["preview_before_save"] ? "checked" : "" ?>> Pour imposer de faire un aper&ccedil;u avant de pouvoir sauver une page</td></tr>

	 <tr><td></td><td>Pour activer la fonction d'attachement de fichiers à une page wiki écrire "oui" sinon écrire "non" </td></tr>
	<tr><td align="right" nowrap>Permet d'attacher un fichier :</td><td><input type="text" size="10" name="config[upload_permission]" value="<?php echo $this->config["upload_permission"] ?>"></td></tr>  

	<tr><td></td><td>Pour activer la fonction de notification de modifications par courriel écrire "oui" sinon écrire "non". Mais pour qu'un mail soit envoyé, au moins 1 nom d'un utilisateur doit être inscrit dans la zone de permission en écriture de la page (et non "*" ou "+")</td></tr>
	<tr><td align="right" nowrap>Notifier par mail :</td><td><input type="text" size="10" name="config[notifier_mail]" value="<?php echo $this->config["notifier_mail"] ?>"></td></tr> 

	<tr><td align="right" nowrap>Pas de commentaire :</td><td><input type="hidden" name="config[comments_disable]" value="0"><input type="checkbox" name="config[comments_disable]" value="1"<?php echo $this->config["comments_disable"] ? "checked" : "" ?>> Désactive les commentaires sur les pages.</td></tr>

 	<tr><td align="right" nowrap>Pas de correcteur orthographique :</td><td><input type="hidden" name="config[correction_disable]" value="0"><input type="checkbox" name="config[correction_disable]" value="1"<?php echo $this->config["correction_disable"] ? "checked" : "" ?>> Désactive la correction orthographique</td></tr>
 
	<tr><td></td><td><br><b>Activation des modules</b></td></tr>

	<tr><td align="right" nowrap>Visionneur GEONEXT :</td><td><input type="hidden" name="config[view_geonext]" value="0"><input type="checkbox" name="config[view_geonext]" value="1"<?php echo $this->config["view_geonext"] ? "checked" : "" ?>></td></tr>

 	<tr><td align="right" nowrap>Visionneur GEOGEBRA :</td><td><input type="hidden" name="config[view_geogebra]" value="0"><input type="checkbox" name="config[view_geogebra]" value="1"<?php echo $this->config["view_geogebra"] ? "checked" : "" ?>></td></tr>

	<tr><td align="right" nowrap>Visionneur GEOLABO :</td><td><input type="hidden" name="config[view_geolabo]" value="0"><input type="checkbox" name="config[view_geolabo]" value="1"<?php echo $this->config["view_geolabo"] ? "checked" : "" ?>></td></tr>
	
	<tr><td align="right" nowrap>Visionneur FLASH :</td><td><input type="hidden" name="config[view_flash]" value="0"><input type="checkbox" name="config[view_flash]" value="1"<?php echo $this->config["view_flash"] ? "checked" : "" ?>></td></tr>

	<tr><td align="right" nowrap>Visionneur MP3 :</td><td><input type="hidden" name="config[view_mp3]" value="0"><input type="checkbox" name="config[view_mp3]" value="1"<?php echo $this->config["view_mp3"] ? "checked" : "" ?>></td></tr>
	
	<tr><td align="right" nowrap>Visionneur CSV :</td><td><input type="hidden" name="config[view_csv]" value="0"><input type="checkbox" name="config[view_csv]" value="1"<?php echo $this->config["view_csv"] ? "checked" : "" ?>></td></tr>
	
	<tr><td align="right" nowrap>Visionneur FLV :</td><td><input type="hidden" name="config[view_flv]" value="0"><input type="checkbox" name="config[view_flv]" value="1"<?php echo $this->config["view_flv"] ? "checked" : "" ?>></td></tr>
	
	<tr><td align="right" nowrap>Visionneur OBJ :</td><td><input type="hidden" name="config[view_obj]" value="0"><input type="checkbox" name="config[view_obj]" value="1"<?php echo $this->config["view_obj"] ? "checked" : "" ?>></td></tr>
	
	<tr><td align="right" nowrap>Visionneur FREEMIND :</td><td><input type="hidden" name="config[view_freemind]" value="0"><input type="checkbox" name="config[view_freemind]" value="1"<?php echo $this->config["view_freemind"] ? "checked" : "" ?>></td></tr>
	
	<tr><td align="right" nowrap>Visionneur SQUEAK :</td><td><input type="hidden" name="config[view_squeak]" value="0"><input type="checkbox" name="config[view_squeak]" value="1"<?php echo $this->config["view_squeak"] ? "checked" : "" ?>></td></tr>
	
	<tr><td align="right" nowrap>Visionneur SCRATCH :</td><td><input type="hidden" name="config[view_scratch]" value="0"><input type="checkbox" name="config[view_scratch]" value="1"<?php echo $this->config["view_scratch"] ? "checked" : "" ?>></td></tr>
	
	<tr><td align="right" nowrap>Question pour enregistrement page :</td><td><input type="hidden" name="config[ask_question_page]" value="0"><input type="checkbox" name="config[ask_question_page]" value="1"<?php echo $this->config["ask_question_page"] ? "checked" : "" ?>></td></tr>

	<tr><td align="right" nowrap>Question pour enregistrement commentaire :</td><td><input type="hidden" name="config[ask_question_comment]" value="0"><input type="checkbox" name="config[ask_question_comment]" value="1"<?php echo $this->config["ask_question_comment"] ? "checked" : "" ?>></td></tr>

	<tr><td align="right" nowrap>SPAM dans les commentaires :</td><td><input type="hidden" name="config[comments_spam_enable]" value="0"><input type="checkbox" name="config[comments_spam_enable]" value="1"<?php echo $this->config["comments_spam_enable"] ? "checked" : "" ?>>Active la recherche de spam dans les commentaires</td></tr>
	
	<tr><td></td><td><input type="hidden" name="config[mysql_host]" value='$HOSTAUTH'></td></tr>
	<tr><td></td><td><input type="hidden" name="config[base_url]" value='$baseurl."Plugins/Wiki/wakka.php?wiki='></td></tr>
	<tr><td></td><td><input type="hidden" name="config[url_site]" value='$baseurl."Plugins/Wiki'></td></tr>
	
	<tr><td></td><td><input type="submit" name="modifier" value="Modifier"></td></tr>
	<tr><td></td><td><input type="hidden" name="config_mod" value='<?php echo htmlentities(serialize($config)) ?>'></td></tr>
	<tr><td></td><td><input type="hidden" name="config_actu" value='<?php echo htmlentities(serialize($this->config)) ?>'></td></tr>
</table>
</form>
<?php
}
?>
