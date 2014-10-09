<?php
/* header.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003, 2004 Charles NEPOTE
Copyright 2002  Patrick PAUL
Copyright 2003  Eric DELORD
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
$message = $this->GetMessage();
$user = $this->GetUser();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">


<head>

<title><?php echo $this->GetWakkaName().":".$this->GetPageTag(); ?></title>
<?php if ($this->GetMethod() != 'show')
    echo "<meta name=\"robots\" content=\"noindex, nofollow\"/>\n";?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<meta name="keywords" content="<?php echo $this->GetConfigValue("meta_keywords") ?>" />
<meta name="description" content="<?php echo  $this->GetConfigValue("meta_description") ?>" />

<link rel="apple-touch-icon" href="http://m.recitmst.qc.ca/apple-touch-icon.png"/>

<!-- The ACeditor contribution -->
<?php if ($this->GetMethod() == "edit") {
	echo "<script type=\"text/javascript\" src=\"".$this->GetConfigValue('url_site')."ACeditor.js\"></script>";

	}
?><!-- aussi ajouté une classe dans edit.php ligne 210 pour les boutons enregistrer et autres. -->
  
<!-- End on The ACEditor Contrib -->
<?php if (!$this->HasAccess('write', $comment['tag'])) { ?>
	<style type="text/css"> 
		 .handout { display: none; }
	</style>
	<?php
	}
	?>



<?php
include_once("jquery/Mobile_Detect.php");
$detect = new Mobile_Detect();
?>
           
  	<?php if ($this->HasAccess('write', $comment['tag'])) {?>
		<style type="text/css" media="all"> @import "<?php echo $this->GetConfigValue('url_site') . (empty($_COOKIE["sitestyle"])?'wakka':$_COOKIE["sitestyle"]) ?>_m.css";</style>


<?php } 

else {?>
      <script type="text/javascript">
$(document).bind("mobileinit", function() {
      $.mobile.page.prototype.options.addBackBtn = true;
 });    
</script> 
		<link rel="stylesheet" href="<?php echo $this->GetConfigValue('base_url'); ?>jquery/jquery.mobile-1.0.1.min.css" />
		<script src="<?php echo $this->GetConfigValue('base_url'); ?>jquery/jquery-1.6.4.min.js"></script>
		<script src="<?php echo $this->GetConfigValue('base_url'); ?>jquery/jquery.mobile-1.0.1.min.js"></script>
	
<?php
}
?>  
</head>


<body
	<?php if ($this->GetMethod() == "edit") {
	/* ACeditor*/ echo  "onLoad=\"thisForm=document.ACEditor;\"";
	}
	else{
	echo $message ? "onLoad=\"alert('".$message."');\" " : "";
	}
?>>



 <?php  if ($this->HasAccess('write', $comment['tag'])) {?>

		<div id="main">

			<!-- Header -->
			<div id="header">
				
				<?php
				$header_page=$this->GetConfigValue('header_page');
				if (!empty($header_page))
				{
					// Ajout Menu de Navigation
					echo $this->Action('include page="' . $header_page . '"');
				}
				?>
				
			<div id="rss-block">
				
			</div>		
			</div>
			<!-- Header end -->

			<!-- Menu -->
			<div id="menu-box" class="cleaning-box">
			<a href="#skip-menu" class="hidden">Skip menu</a>
				<ul id="menu">
						<li><?php echo $this->ComposeLinkToPage($this->config["root_page"]); ?></li>
						<?php if ($this->HasAccess('write', $comment['tag'])) { ?><li><?php echo " <a href=\"".$this->config["url_site"] ."DerniersChangements\">DerniersChangements</a>"; ?></li>	<?php
							}
							?>
						<li><?php echo " <a href=\"".$this->config["url_site"] ."ParametresUtilisateur\">Admin</a>"; ?></li>

						<li><?php echo " <a href=\"http://recitmst.qc.ca\">Site officiel RECIT MST</a>"; ?></li>
				</ul>
			</div>
			<!-- Menu end -->
			
		<hr class="noscreen" />

		<div id="skip-menu"></div>
			
			<div id="content">
			
				<div id="content-box">
					
					<div id="content-box-in-left">
						<div id="content-box-in-left-in">

<?php




echo  $this->HasAccess("write") ? "<a href=\"".$this->href("edit")."\" title=\"Cliquez pour &eacute;diter cette page.\">&Eacute;diter</a> ::\n" : "";
echo  "<a href=\"".$this->href("imprime")."\" title=\"Cliquez pour imprimer cette page.\"
onclick=\"this.target = '_blank';\">IMP</a> ::\n";
echo  "<a href=\"".$this->href("rss")."\" title=\"Suivre les modifications de la page.\"
onclick=\"this.target = '_blank';\">RSS</a> ::\n";

echo  "<a href=\"".$this->href("v")."\" title=\"Version HTML de cette page.\"
onclick=\"this.target = '_blank';\">HTML</a> ::\n";
echo  "<a href=\"".$this->href("txt")."\" title=\"Cliquez pour exporter en texte.\">TXT</a> ::\n";
echo  "<a href=\"".$this->href("clone")."\" title=\"Cliquez pour cloner la page.\">Clone</a> ::\n";

echo  $this->GetPageTime() ? "<a href=\"".$this->href("revisions")."\" title=\"Cliquez pour voir les derni&egrave;res modifications sur cette page.\">Historique</a> ::\n" : "";
	// if this page exists
	if ($this->page)
	{
		// if owner is current user
		if ($this->UserIsOwner())
		{
			echo 
			"Propri&eacute;taire&nbsp;: vous :: \n",
			"<a href=\"",$this->href("acls")."\" title=\"Cliquez pour &eacute;diter les permissions de cette page.\">Permissions</a> :: \n",
			"<a  title=\"Supprimer la page\" href=\"",$this->href("deletepage")."\"";
			?>
			onclick="return confirm('Voulez-vous vraiment supprimer cette page?');"<?php
			echo ">X</a> :: \n";
		}
		else
		{
			if ($owner = $this->GetPageOwner())
			{
				echo "Propri&eacute;taire : ",$this->Format($owner);
				if  ($this->GetUserName()== $this->config[admin_wiki]) {echo "&nbsp::<a title=\"Supprimer la page\" href=\"",$this->href("deletepage")."\"";
			?>
						onclick="return confirm('Voulez-vous vraiment supprimer cette page?');"<?php
					echo ">X</a> :: \n";}
			}
			else
			{
				echo "Pas de propri&eacute;taire ";
				echo ($this->GetUser() ? "(<a href=\"".$this->href("claim")."\">Appropriation</a>)" : "");
			}
			echo " :: \n";
		}
	}


?>



<?php } 

else {?>
	<div data-role="page" data-add-back-btn="true" data-back-btn-text="retour" >

	<div data-role="header">
			<?php
			$header_page=$this->GetConfigValue('header_page');
			if (!empty($header_page))
				{
				// Ajout Menu de Navigation

				echo $this->Action('include page="' . $header_page . '"');

				}
			?> 
	</div>

	<div data-role="content"> 

<?php
}
?> 
