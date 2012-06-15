<?php
/*
show.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright  2003  Eric DELORD
Copyright  2003  Eric FELDSTEIN
Copyright  2009 Pierre Lachance
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
function UserIsOwner($tag = "") {
		// check if user is logged in
		if (!$this->GetUser()) return false;

		// set default tag
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		
		// check if user is owner
		if ($this->GetPageOwner($tag) == $this->GetUserName()) return true;
	}
	// returns true if logged in user is in the given group
	function UserIsInGroup($group) { return false; }
function UserIsAdmin() { return $this->UserIsInGroup("admin"); }
//vérification de sécurité
if (!eregi("wakka.php", $_SERVER['PHP_SELF'])) {
    die ("acc&egrave;s direct interdit");
}
echo $this->Header();
?>
<div class="page">
<?php
//Modif LLB
//if ($HasAccessRead=$this->HasAccess("read"))
if ($HasAccessRead=$this->HasAccess("read")||$this->UserInGroup("admins"))
{
	if (!$this->page)
	{
		echo "Cette page n'existe pas encore, voulez vous la <a href=\"".$this->href("edit")."\">cr&eacute;er</a> ?" ;
	}
	else
	{
		// comment header?
		if ($this->page["comment_on"])
		{
			echo "<div class=\"commentinfo\">Ceci est un commentaire sur ",$this->ComposeLinkToPage($this->page["comment_on"], "", "", 0),", post&eacute; par ",$this->Format($this->page["user"])," &agrave; ",$this->page["time"],"</div>";
		}

		if ($this->page["latest"] == "N")
		{
			echo "<div class=\"revisioninfo\">Ceci est une version archiv&eacute;e de <a href=\"",$this->href(),"\">",$this->GetPageTag(),"</a> &agrave; ",$this->page["time"],".</div>";
		}


		// display page
		echo $this->Format($this->page["body"], "wakka");

		// if this is an old revision, display some buttons
		if (($this->page["latest"] == "N") && $this->HasAccess("write"))
		{
			$latest = $this->LoadPage($this->tag);
			?>
			<br />
			<?php echo  $this->FormOpen("edit") ?>
			<input type="hidden" name="previous" value="<?php echo  $latest["id"] ?>">
			<input type="hidden" name="body" value="<?php echo  htmlentities($this->page["body"]) ?>">
			<input type="submit" value="R&eacute;&eacute;diter cette version archiv&eacute;e">
			<?php echo  $this->FormClose(); ?>
			<?php
		}
	}
}
else
{
	echo "<i>Vous n'&ecirc;tes pas autoris&eacute; &agrave; lire cette page</i>" ;
}
?>

</div>


<?php
if ($this->config[comments_disable]=="0")
{

if ($HasAccessRead)
{
	// load comments for this page
	$comments = $this->LoadComments($this->tag);
	$commentspam = $this->LoadCommentspam($this->tag);
	// store comments display in session
	$tag = $this->GetPageTag();
	if (!isset($_SESSION["show_comments"][$tag]))
		$_SESSION["show_comments"][$tag] = ($this->UserWantsComments() ? "1" : "0");
	if (isset($_REQUEST["show_comments"])){	
	switch($_REQUEST["show_comments"])
	{
	case "0":
		$_SESSION["show_comments"][$tag] = 0;
		break;
	case "1":
		$_SESSION["show_comments"][$tag] = 1;
		break;
	}
	}
	// display comments!
	if ($this->page && $_SESSION["show_comments"][$tag])
	{
		// display comments header
		?>
		<div class="commentsheader">
			Commentaires [<a href="<?php echo  $this->href("", "", "show_comments=0") ?>">Cacher commentaires/formulaire</a>]
		</div>
		<?php
		
		// display comments themselves
		if ($comments)
		{
			foreach ($comments as $comment)
			{
				echo "<br /><a name=\"",$comment["tag"],"\"></a>\n" ;
				echo "<div class=\"comment\">\n" ;
				//Ajouter pour l'édition et suppression des commentaires
				if ($this->HasAccess('write', $comment['tag'])
				 || $this->UserIsOwner($comment['tag'])
				 || $this->UserIsOwner() || ($this->GetUserName()== $this->config[admin_wiki]) )
				{
					echo '<div class="commenteditlink">';
					if ($this->HasAccess('write', $comment['tag']))
					{
						echo '<a href="',$this->href('edit',$comment['tag']),'">&Eacute;diter ce commentaire</a>';
					}
					if ($this->UserIsOwner($comment['tag'])
					 || $this->UserIsOwner() || ($this->GetUserName()== $this->config[admin_wiki]))
					{
						echo '<br />','<a href="',$this->href('deletecomment',$comment['tag']),'"';?>
						onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire?');"
						<?php echo '>Supprimer ce commentaire</a>';
					}
					//echo "$comment[comment_on]";
					echo "</div>\n";
				}
				//FIn de edition suppression commentaire
				echo $this->Format($comment["body"]),"\n" ;
				echo "<div class=\"commentinfo\">\n-- ",$this->Format($comment["user"])," (".$comment["time"],")\n</div>\n" ;
				echo "</div>\n" ;
			}
			
		}
		
		if ($commentspam && ($this->GetUserName()== $this->config[admin_wiki]) && ($this->config[comments_spam_enable]==1))
		{
			foreach ($commentspam as $spam)
			{
				echo "<br /><a name=\"",$spam["tag"],"\"></a>\n" ;
				echo "<div class=\"commentspam\">\n" ;
				//Ajouter pour l'édition et suppression des commentaires
				if ($this->GetUserName()== $this->config[admin_wiki]) 
				{
					echo '<div class="commenteditlink">';
					
					if ($this->GetUserName()== $this->config[admin_wiki])
					{
						echo '<br />','<a href="',$this->href('deletespam',$spam['tag']),'"';?>
						onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire?');"
						<?php echo '>Supprimer ce commentaire</a>';
					}
					
					echo "</div>\n";
				}
				//FIn de edition suppression commentaire
				echo $this->Format($spam["body"]),"\n" ;
				echo "<div class=\"commentinfo\">\n-- ",$this->Format($spam["user"])," (".$spam["time"],")\n</div>\n" ;
				echo "</div>\n" ;
			}
			
		}
		echo "<br />" ;
		// display comment form
		echo "<div class=\"commentform\">\n" ;
		//Modif LLB
		if ($this->HasAccess("comment")||$this->UserInGroup("admins"))
		{
		$c1 = rand(0,9);
		$c2 = rand(0,9);
		$calcul = ($c1 + $c2);
		//echo $calcul;
			?>
			Ajouter un commentaire &agrave; cette page<?php 
				if ($this->config[ask_question_comment]=="1") { ?> <span class="question">(n'oubliez pas de donner la réponse au calcul)</span><?php } ?>:<br />
				<?php echo  $this->FormOpen("addcomment"); ?>
				<textarea name="body" rows="6" style="width: 99%" value="<?php echo $_POST[body]; ?>"></textarea><br />
					
				<?php 
				if ($this->config[ask_question_comment]=="1") {
				echo "<span class=\"question\">$c1 + $c2 =</span>"; 
				?>
				<input name="rep" id="rep" type="text" size="3" maxlength="5" />
				<input name="calcul" value="<?php echo $calcul; ?>" type="hidden" />
				<?php } ?>
				<input type="submit" value="Ajouter Commentaire" accesskey="s" />
				<?php echo  $this->FormClose(); ?>
			<?php
		}
		echo "</div>\n" ;
	}
	else
	{
		?>
		<div class="commentsheader">
		<?php
			switch (count($comments))
			{
			case 0:
				echo "Il n'y a pas de commentaire sur cette page." ;
				break;
			case 1:
				echo "Il y a un commentaire sur cette page." ;
				break;
			default:
				echo "Il y a ",count($comments)," commentaires sur cette page." ;
			}
		?>
		
		[<a href="<?php echo  $this->href("", "", "show_comments=1") ?>">Afficher commentaires/formulaire</a>]

		</div>
		<?php
	}
}
}


echo $this->Footer();
?>
