<?php
/*
addcomment.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
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
if (!preg_match("/wakka.php/", $_SERVER['PHP_SELF'])) {
    die ("acc&egrave;s direct interdit");
}
echo $this->Header();

//Modif LLB 
if ($this->HasAccess("comment")|| $this->UserInGroup("admins")) 
{
	// find number
	if ($latestComment = $this->LoadSingle("select tag, id from ".$this->config["table_prefix"]."pages where comment_on != '' order by id desc limit 1"))
	{
		preg_match("/^Comment([0-9]+)$/", $latestComment["tag"], $matches);
		$num = $matches[1] + 1;
	}
	else
	{
		$num = "1";
	}

	$body = trim($_POST["body"]);
	if (!$body)
	{
		$this->SetMessage("Commentaire vide  -- pas de sauvegarde !");
	}
	else
	{	$commentspam= 0;
		if ($this->config[comments_spam_enable]==1) {
		//Antispam

		$lesmotsdutexte = explode(" ",$body);
		
		//comment faire simple....
		for ($x=0;$x<count($lesmotsdutexte) ;$x++) 
		{
		$reqspam="SELECT * from antispam where `aspm_string` LIKE \"".$lesmotsdutexte[$x]."\"";
			
			$rspam=mysql_query($reqspam) or die(mysql_error());
			
			
			if (mysql_num_rows($rspam)>0) 
			{
			$commentspam= 1;
				
			}
		}
		}//fin antispam
		
		if ($commentspam=="0") {
		
		if ($this->config["ask_question_comment"]=="1" && $_POST["rep"] == $_POST["calcul"]){
		// store new comment
		$this->SavePage("Comment".$num, $body, $this->tag);}
		elseif ($this->config["ask_question_comment"]=="0") {
			$this->SavePage("Comment".$num, $body, $this->tag);}
		}

		else {
			$this->SetAsSpam("Comment".$num, $body, $this->tag);
		}
				
	}

	
	// redirect to page
	$this->redirect($this->href());
}
else
{
	echo"<div class=\"page\"><i>Vous n'&ecirc;tes pas autoris&eacute; &agrave; commenter cette page.</i></div>\n";
}



echo $this->Footer();

?>
