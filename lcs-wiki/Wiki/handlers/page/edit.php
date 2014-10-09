<?php
/*
edit.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright 2002, 2003 Patrick PAUL
Copyright  2003  Eric FELDSTEIN
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

//vérification de sécurité
if (!eregi("wakka.php", $_SERVER['PHP_SELF'])) {
    die ("acc&egrave;s direct interdit");
}
echo $this->Header();
?>
<div class="page">
<?php
if ($this->HasAccess("write") && $this->HasAccess("read"))
{
	$output='';
	if ($_POST)
	{
		// only if saving:
		if ($_POST["submit"] == "Enregistrer")
		{
			// check for overwriting
			if ($this->page)
			{
				if ($this->page["id"] != $_POST["previous"])
				{
					$error = "ALERTE : ".
					"Cette page a &eacute;t&eacute; modifi&eacute;e par quelqu'un d'autre pendant que vous l'&eacute;ditiez.<br />\n".
					"Veuillez copier vos changements et r&eacute;&eacute;diter cette page.\n";
				}
			}
		//send e-mail notifications====================================
			if ($this->config[notifier_mail]=="oui")
			{
			if ($_POST["notifyWrite"] == "true") {
				$writeACL = $this->LoadAcl($this->GetPageTag(), "write");
				$allUsers = explode("\n", $writeACL["list"]);
				foreach ($allUsers as $userName) {
					$user = $this->LoadUser($userName);
					$mailTo = $user["email"];
					$mailSubject = "Avis de modification de la page ".$this->GetPageTag();
					$mailHeaders = "From: wikini@recitmst.qc.ca\r\n".
								   "X-Mailer: PHP/" . phpversion();
					$mailMessage = "La page ".$this->GetPageTag()." a été modifiée\n".
					               "\nPour voir les modifications suivez ce lien: ".
					               $this->href("", "", "")."\n".
								   "\nSi vous ne désirez plus recevoir de notification, veuillez en avertir le propriétaire de la page ".$this->GetPageTag().".";
					mail($mailTo, $mailSubject, $mailMessage, $mailHeaders);
				}
			}
			}//fin send e-mail notifications==============================


			// store
			if (!$error && $_POST["rep"] == $_POST["calcul"])
			{
				$body = str_replace("\r", "", $_POST["body"]);
				
				// test si la nouvelle page est differente de la précédente
				if(rtrim($body)==rtrim($this->page["body"])) {
					$this->SetMessage("Cette page n\'a pas &eacute;t&eacute; enregistr&eacute;e car elle n\'a subi aucune modification.");
					$this->Redirect($this->href());
				}

				// add page (revisions)
				$this->SavePage($this->tag, $body);

				// now we render it internally so we can write the updated link table.
				$this->ClearLinkTable();
				$this->StartLinkTracking();
				//$dummy = $this->Header();
				$dummy .= $this->Format($body);
				//$dummy .= $this->Footer();
				$this->StopLinkTracking();
				$this->WriteLinkTable();
				$this->ClearLinkTable();
					// forward
					if ($this->page['comment_on']) {
						$this->Redirect($this->href('', $this->page['comment_on']).'#'.$this->tag);
					}
					else {
						$this->Redirect($this->href());
					}
				// forward ancien
				//$this->Redirect($this->href());
			}
		}
	}

	// fetch fields
	if (!isset($_POST["previous"])) $previous = $this->page["id"];
	else $previous = $_POST["previous"];
	if (!isset($_POST["body"])) $body = $this->page["body"];
	else $body = $_POST["body"];
	//Vérifie si on peut envoyer un avis de modification et met le formulaire dasn une variable pour l'afficher plus bas.
	if ($this->config[notifier_mail]=="oui")
		{
		$notifier_mail="<input type=\"checkbox\" name=\"notifyWrite\" value=\"true\">Envoyer un avis de modification\n";
		}
	//Ajout ou non du bouton pour la correction--PL
	if ($this->config[correction_disable]=="0")
	{
	$correction="<input name=\"submit\" type=\"submit\" value=\"Correction\" />\n";
	
	}
	//Ajout d'une quesiton avant l'enregistrement--PL
		$c1 = rand(0,9);
		$c2 = rand(0,9);
		$calcul = ($c1 + $c2);
		if ($this->config[ask_question_page]=="1") {
		$question= "<span class=\"question\">$c1 + $c2 =</span>
		<input name=\"rep\" id=\"rep\" type=\"text\" size=\"3\" maxlength=\"5\" />
		<input name=\"calcul\" value=\"$calcul\" type=\"hidden\" />";
		}
		else { $calcul="1"; $rep="1";}
	// preview?
	if (!isset($_POST["submit"])) $_POST["submit"] = "";
	if ($_POST["submit"] == "Aperçu")
	{
		$output .=
			"<div class=\"prev_alert\"><strong>Aper&ccedil;u</strong></div>\n".
			$this->Format($body)."\n\n".
			$this->FormOpen("edit").
			"<input type=\"hidden\" name=\"previous\" value=\"".$previous."\" />\n".
			"<input type=\"hidden\" name=\"body\" value=\"".htmlentities($body)."\" />\n".
			"<br />\n".
			"$question".
			"<input name=\"submit\" type=\"submit\" value=\"Enregistrer\" accesskey=\"s\" />\n".
			"$correction".
			"<input name=\"submit\" type=\"submit\" value=\"R&eacute;&eacute;diter \" accesskey=\"p\" />\n".
			
			"<input type=\"button\" value=\"Annulation\" onclick=\"document.location='".$this->href("")."';\" />\n".
			"$notifier_mail". //pour le mail
			$this->FormClose()."\n";
	}
	elseif ($_POST["submit"] == "Correction")
	{
	        
		echo "<div class=\"prev_alert\"><strong>À corriger</strong></div>";
		$pl=$this->Format($body);
		require_once("correction.php");
		$output .=
		
			$this->FormOpen("edit").
			"<input type=\"hidden\" name=\"previous\" value=\"".$previous."\" />\n".
			"<input type=\"hidden\" name=\"body\" value=\"".htmlentities($body)."\" />\n".
			"<br />\n".
			"$question".
			"<input name=\"submit\" type=\"submit\" value=\"Enregistrer\" accesskey=\"s\" />\n".
			"<input name=\"submit\" type=\"submit\" value=\"R&eacute;&eacute;diter \" accesskey=\"p\" />\n".
			"<input type=\"button\" value=\"Annulation\" onclick=\"document.location='".$this->href("")."';\" />\n".
			"$notifier_mail". //pour le mail
			$this->FormClose()."\n";
	}
	else
	{
		// display form
		if (isset($error))
		{
			if (!isset($output)) $output = '';
			$output .= "<div class=\"error\">$error</div>\n";
		}

		// append a comment?
		if (isset($_REQUEST["appendcomment"]))
		{
			$body = trim($body)."\n\n----\n\n--".$this->UserName()." (".strftime("%c").")";
		}
	
    		require_once("ACeditor.buttonsBar.php");
   			

		if (!isset($output)) $output = '';
		$output .=
			$this->FormOpen("edit").
			"<input type=\"hidden\" name=\"previous\" value=\"".$previous."\" />\n".$ACbuttonsBar.
			
			"<div class=\"textarea\"><textarea onkeydown=\"fKeyDown()\" name=\"body\" cols=\"60\" rows=\"40\" wrap=\"soft\" class=\"edit\">\n".
			htmlspecialchars($body).
			"\n</textarea></div><br /><br />\n".
			//la question avant d'enregistrer
			"$question".
			($this->config["preview_before_save"] ? "" : "<input name=\"submit\" type=\"submit\" value=\"Enregistrer\" accesskey=\"s\" />\n").
			"$correction".
			"<input name=\"submit\" type=\"submit\" value=\"Aper&ccedil;u\" accesskey=\"p\" />\n".
			
			"<input type=\"button\" value=\"Annulation\" onclick=\"document.location='".$this->href("")."';\" />\n".
			"$notifier_mail". //pour le mail
			$this->FormClose();
	}
	
	echo $output;
	
}
else
{
	echo "<i>Vous n'avez pas acc&egrave;s en &eacute;criture &agrave; cette page !</i>\n";
}
?>

</div>
<?php echo $this->Footer(); ?>
