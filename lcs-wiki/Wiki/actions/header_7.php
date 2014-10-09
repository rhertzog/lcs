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
<?php
if ($_SESSION['box']=="oui") {  //ajout pour l'action box, ça écrit après avoir passé 1 fois sur un lien box.
	echo "
	<script type=\"text/javascript\" src=\"".$urlmst."lightwindow/javascript/prototype.js\"></script>
	<script type=\"text/javascript\" src=\"".$urlmst."lightwindow/javascript/scriptaculous.js?load=effects\"></script>
	<script type=\"text/javascript\" src=\"".$urlmst."lightwindow/javascript/lightwindow.js\"></script>
	<link rel=\"stylesheet\" href=\"".$urlmst."lightwindow/css/lightwindow.css\" type=\"text/css\" media=\"screen\" />";
}
?>


<style type="text/css" media="all"> @import "<?php echo $this->GetConfigValue('url_site') . (empty($_COOKIE["sitestyle"])?'wakka':$_COOKIE["sitestyle"]) ?>_7.css";</style>

<script type="text/javascript">
function fKeyDown()	{
	if (event.keyCode == 9) {
		event.returnValue= false;
		document.selection.createRange().text = String.fromCharCode(9) } }
</script>

<!-- The ACeditor contribution -->
<?php if ($this->GetMethod() == "edit") {
echo "<script type=\"text/javascript\" src=\"".$this->GetConfigValue('url_site')."ACeditor.js\"></script>";
}
?>   
<!-- End on The ACEditor Contrib -->
<?php if (!$this->HasAccess('write', $comment['tag'])) { ?>
	<style type="text/css"> 
		 .handout { display: none; }
	</style>
	<?php
	}
	?>

</head>


<body <?php echo (!$user || ($user["doubleclickedit"] == 'Y')) && ($this->GetMethod() == "show") ? "ondblclick=\"document.location='".$this->href("edit")."';\" " : "" ?>
	<?php if ($this->GetMethod() == "edit") {
	/* ACeditor*/ echo  "onLoad=\"thisForm=document.ACEditor;\"";
	}
	else{
	echo $message ? "onLoad=\"alert('".$message."');\" " : "";
	}
?>>

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
		<?php echo $this->Format($this->GetUserName()); echo ""; if ($user = $this->GetUser()) echo " <a href=\"".$this->config["base_url"] ."ParametresUtilisateur&amp;action=logout\" title=\"D&eacute;connexion\">X</a>"; ?>
	</div>		
	</div>
	<!-- Header end -->

	<!-- Menu -->
	<div id="menu-box" class="cleaning-box">
	<a href="#skip-menu" class="hidden">Skip menu</a>
		<ul id="menu">
				<li><?php echo $this->ComposeLinkToPage($this->config["root_page"]); ?></li>
				
				<li><?php echo " <a href=\"".$this->config["base_url"] ."DerniersChangements\">DerniersChangements</a>"; ?></li>
				<li><?php echo " <a href=\"".$this->config["base_url"] ."DerniersCommentaires\">DerniersCommentaires</a>"; ?></li>
				<li><?php echo " <a href=\"".$this->config["base_url"] ."ParametresUtilisateur\">ParametresUtilisateur</a>"; ?></li>
		</ul>
	</div>
	<!-- Menu end -->
	
<hr class="noscreen" />

<div id="skip-menu"></div>
	
	<div id="content">
	
		<div id="content-box">
			
			<!-- Right column -->
			<div id="content-box-in-right">
				<div id="content-box-in-right-in">
										<?php
					$menu_page=$this->GetConfigValue('menu_page');
					if (!empty($menu_page)){
						// Ajout Menu de Navigation
							echo $this->Action('include page="' . $menu_page . '"');
					}
					?> 
			</div>
			</div>
			
			<!-- Right column end -->

			<!-- Left column -->
			<div id="content-box-in-left">
				<div id="content-box-in-left-in">
