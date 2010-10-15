<?php
/*
acls.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright  2003  Eric FELDSTEIN
Copyright  2003  Jean-Pascal MILCENT
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
//v�rification de s�curit�
if (!eregi("wakka.php", $_SERVER['PHP_SELF'])) {
    die ("acc&egrave;s direct interdit");
}
echo $this->Header();
?>
<div class="page">
<?php

if (($this->UserIsOwner()) || ($this->UserInGroup("admins")))
{
	if ($_POST)
	{
		// store lists
		$this->SaveAcl($this->GetPageTag(), "read", $_POST["read_acl"]);
		$this->SaveAcl($this->GetPageTag(), "write", $_POST["write_acl"]);
		$this->SaveAcl($this->GetPageTag(), "comment", $_POST["comment_acl"]);
		$message = "Droits d\'acc&egrave;s mis &agrave; jour ";//$message = "Access control lists updated";
		
		// change owner?
		if ($newowner = $_POST["newowner"])
		{
			$this->SetPageOwner($this->GetPageTag(), $newowner);
			$message .= " et changement du propri&eacute;taire. Nouveau propri&eacute;taire : ".$newowner;//$message .= " and gave ownership to ".$newowner;
		}

		// redirect back to page
		$this->SetMessage($message."!");
		$this->Redirect($this->Href());
	}
	else
	{
		// load acls
		$readACL = $this->LoadAcl($this->GetPageTag(), "read");
		$writeACL = $this->LoadAcl($this->GetPageTag(), "write");
		$commentACL = $this->LoadAcl($this->GetPageTag(), "comment");

		// show form
		?>

		<h3><center>Liste des droits d'acc&egrave;s de la page  <?php echo  $this->ComposeLinkToPage($this->GetPageTag()) ?></center></h3><!-- Access Control Lists for-->

		<hr size="3" />
		<hr size="6" width="120" />
		
		<br />

<!-- cr�ation d'un formulaire avec un nom. C'est tr�s important pour la r�cup�ration des variables � partir de "document.forms..." -->
<form name="acls" action="<?php echo $this->config['base_url'].$this->GetPageTag()?>/lcs_acls" method="post">		

<!-- liste d�roulante pour faciliter la recherche d'utilisateurs et/ou de groupes-->

N'oubliez pas de <u>valider vos choix</u> avant de cliquer sur le bouton "enregistrer" au bas de la page.
<br /><br /><b><u>Attention</u> :</b> le bouton "valider les droits s&eacute;lectionn&eacute;s" n'apparait qu'apr&egrave;s avoir fait un choix dans la liste d&eacute;roulante ci-dessous. 
<br /><br /><br />
<select id="choix" name="choix" onchange="idchoix=document.acls.choix.value; aclchoix(idchoix);">
 	<option value="0">Vous souhaitez accorder ou refuser des droits � ...</option>
	<option value="1">un ou plusieurs �l�ves</option>
	<option value="4">un ou plusieurs professeurs</option>
	<option value="5">un ou plusieurs administratifs</option>
	<option value="3">les �l�ves d'une classe</option>
	<option value="2">l'�quipe p�dagogique d'une classe</option>
	<option value="6">un groupe (en dehors des groupes "classes" et "�quipes p�dagogiques")</option>
        <option value="7">tous les �l�ves</option>
	<option value="8">tous les professeurs</option>
	<option value="9">tout le personnel administratif</option>
	<option value="10">tous les utilisateurs (�l�ves, professeurs et administratifs)</option>
	<option value="11">tous les utilisateurs (m�me non authentifi�s)</option>
</select>

<div id="stype">
</div>

<div id="scategorie">
</div>
<br />
	
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top" style="padding-right: 20px">
					<b>Droits de lecture :</b><br /><!-- Read ACL:-->
					<textarea name="read_acl" rows="4" cols="20"><?php echo  $readACL["list"] ?></textarea>
				<td>
				<td valign="top" style="padding-right: 20px">
					<b>Droits d'&eacute;criture :</b><br /><!-- Write ACL:-->
					<textarea name="write_acl" rows="4" cols="20"><?php echo  $writeACL["list"] ?></textarea>
				<td>
				<td valign="top" style="padding-right: 20px">
					<b>Droits des commentaires :</b><br /><!-- Comments ACL:-->
					<textarea name="comment_acl" rows="4" cols="20"><?php echo  $commentACL["list"] ?></textarea>
				<td>
			</tr>
			<tr>
				<td colspan="3">
					<br />
					<b><u>Changer le propri&eacute;taire</u> :</b><!-- Set Owner:-->
					<select name="newowner">
						<option value="">Ne rien modifier</option><!-- Don't change-->
						<option value=""></option>
						<?php
						if ($users = $this->LoadUsers())
						{
							foreach($users as $user)
							{
								//ajout des noms et pr�noms � partir de la fonction LCS people_get_variables()
								list($uti, $groups) = people_get_variables($user["name"],false);
								echo "<option value=\"",htmlentities($user["name"]),"\">",$uti["fullname"],"</option>\n";
							}
						}
						?>
					</select>
				<td>
			</tr>
			</table>
			<br /><br />
				<center>
					<input type="submit" value="Enregistrer" style="width: 120px" accesskey="s"><!-- Store ACLs-->
					<input type="button" value="Annuler" onclick="history.back();" style="width: 120px"><!-- Cancel -->
				</center>
		</form>

                <br />

<hr size="3" />
<hr size="6" width="120" />

<br />

<div class="pointille">
	<br />
		<i>Pour chaque droit d'acc�s, les r�gles sont lues de bas en haut, � raison de <b>exactement une seule r�gle par ligne</b>. En l'absence de r�gle, l'acc�s est refus� � tout le monde. Syntaxe :
		<ul>
			<li>
			   	<tt>NomUtilisateur</tt> : Droit d'acc�s (lecture/�criture/commentaires) accord� �
				<tt>NomUtilisteur</tt>
			</li>
			<li>
				<tt>@legroupe</tt> : Droit d'acc�s accord� au groupe
				<tt>legroupe</tt>
			</li>
			<li>
				<tt>*</tt> : Droit d'acc�s accord� � tout le monde
			</li>
			<li>
				<tt>+</tt> : Droit d'acc�s accord� uniquement aux personnes identifi�es
			</li>
			 <li>
			 	<tt>!NomUtilisateur</tt> : Droit d'acc�s refus� �
				<tt>NomUtilisteur</tt>
			</li>
			<li>
				<tt>!@legroupe</tt> : Droit d'acc�s refus� au groupe
				<tt>legroupe</tt>
			</li>
			<li>
				<tt>!*</tt> : Droit d'acc�s refus� � tout le monde
			</li>
			<li>
				<tt>!+</tt> : Droit d'acc�s refus� aux personnes identifi�es
			</li>
		</ul>
		</i>
	<br />
</div>
		<br />
		
		<?php		
	}
}
else
{
	echo"<i>Vous n'&ecirc;tes pas le propri&eacute;taire de cette page.</i>";

}

?>
</div>
<?php echo $this->Footer(); ?>
