<?php
/*
show.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright  2003  Eric DELORD
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
?>
<?php
include_once("jquery/Mobile_Detect.php");
$detect = new Mobile_Detect();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">


<?php if ($detect->isMobile()) {?>
<head>
<title><?php echo $this->GetWakkaName().":".$this->GetPageTag(); ?></title>
<?php if ($this->GetMethod() != 'show')
    echo "<meta name=\"robots\" content=\"noindex, nofollow\"/>\n";?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<meta name="keywords" content="<?php echo $this->GetConfigValue("meta_keywords") ?>" />
<meta name="description" content="<?php echo  $this->GetConfigValue("meta_description") ?>" />
<link rel="stylesheet" href="<?php echo $this->GetConfigValue('base_url'); ?>jquery/jquery.mobile-1.0a2.min.css" />
<script src="<?php echo $this->GetConfigValue('base_url'); ?>jquery/jquery-1.4.4.min.js"></script>
<script src="<?php echo $this->GetConfigValue('base_url'); ?>jquery/jquery.mobile-1.0a2.min.js"></script>


</head>


<body>
 	<div data-role="page">

	<div data-role="header">
			<h1><?php echo $this->GetWakkaName(); ?></h1> 
	</div>

	<div data-role="content"> 
		<div class="page">
		<?php
		if ($HasAccessRead=$this->HasAccess("read"))
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
					<input type="hidden" name="body" value="<?php echo  htmlentities($this->page["body"],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1") ?>">
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


		</div><!-- fin page -->
	</div><!-- fin data role content -->
</div><!-- fin data-role page -->

</body>

<?php } 

else {?>


<head>
<title><?php echo $this->GetWakkaName().":".$this->GetPageTag(); ?></title>
<?php if ($this->GetMethod() != 'show')
    echo "<meta name=\"robots\" content=\"noindex, nofollow\"/>\n";?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<meta name="keywords" content="<?php echo $this->GetConfigValue("meta_keywords") ?>" />
<meta name="description" content="<?php echo  $this->GetConfigValue("meta_description") ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->config["url_site"] ?>wakka.v.css" />


</head>


<body>
<div id="page_v">
<div class="page">
<?php
if ($HasAccessRead=$this->HasAccess("read"))
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
			<input type="hidden" name="body" value="<?php echo  htmlentities($this->page["body"],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE, "ISO8859-1") ?>">
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
</div>
</body>
<?php
}
?>

</html>
