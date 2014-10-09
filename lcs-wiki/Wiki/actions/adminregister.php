<?php
/*
usersettings.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright 2002  Patrick PAUL
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
if (!isset($_REQUEST["action"])) $_REQUEST["action"] = '';
{
if  ($this->GetUserName()== $this->config[admin_wiki]) {

			$name = trim($_POST["name"]);
			$email = trim($_POST["email"]);
			$password = $_POST["password"];
			$confpassword = $_POST["confpassword"];

			// check if name is WikkiName style
			if (!$this->IsWikiName($name)) $error = "Votre nom d'utilisateur doit &ecirc;tre format&eacute; en NomWiki.";
			else if (!$email) $error = "Vous devez sp&eacute;cifier une adresse de messagerie &eacute;lectronique.";
			else if (!preg_match("/^.+?\@.+?\..+$/", $email)) $error = "Ceci ne ressemble pas &agrave; une adresse de messagerie &eacute;lectronique.";
			else if ($confpassword != $password) $error = "Les mots de passe n'&eacute;taient pas identiques";
			else if (preg_match("/ /", $password)) $error = "Les espaces ne sont pas permis dans un mot de passe.";
			else if (strlen($password) < 5) $error = "Mot de passe trop court. Un mot de passe doit contenir au minimum 5 caract&egrave;res alphanum&eacute;riques.";
			else
			{
				$this->Query("insert into ".$this->config["table_prefix"]."users set ".
					"signuptime = now(), ".
					"name = '".mysql_escape_string($name)."', ".
					"email = '".mysql_escape_string($email)."', ".
					"password = md5('".mysql_escape_string($_POST["password"])."')");

			echo "<div style=\"color: red;text-align: center;font-weight:bold;\">Inscription ok!</div>";

			
			}

	echo $this->FormOpen();
	?>
	
	<table>
		<tr>
			<td></td>
			<td><h4><?php echo $this->Format("Formulaire d'inscription d'un utilisateur (par l'Admin Wiki)"); ?></h4><br /></td>
		</tr>
		<tr>
			<td align="right">Le NomWiki&nbsp;:</td>
			<td><input name="name" size="40" value="" /></td>
		</tr>
		<tr>
			<td align="right">Mot de passe (5 caract&egrave;res minimum)&nbsp;:</td>
			<td>
			<input type="password" name="password" size="40" />
			<input type="hidden" name="remember" value="0" />
			
			</td>
		</tr>

		<tr>
			<td align="right">Confirmation du mot de passe&nbsp;:</td>
			<td><input type="password" name="confpassword" size="40" /></td>
		</tr>
		<tr>
			<td align="right">Adresse de messagerie &eacute;lectronique.&nbsp;:</td>
			<td><input name="email" size="40" value="" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Nouveau compte" size="40" /></td>
		</tr>
	</table>
	<?php
	echo $this->FormClose();
}
}
?>

