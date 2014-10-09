					</div>

				</div>
				
				<div class="clearer">&nbsp;</div>

			</div>
		</div>
	</div>

</div>
<div id="dashboard">
	<div id="dashboard_content">
		<div class="center_wrapper">
<?php
/* footer.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003, 2004 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright 2002, 2003  Patrick PAUL
Copyright  2003  Eric DELORD
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

echo  $this->FormOpen("", "RechercheTexte", "get");

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
				if  ($this->GetUserName()== $this->config[admin_wiki]) {echo
                                    "::\n<a href=\"",$this->href("acls")."\" title=\"Cliquez pour &eacute;diter les permissions de cette page.\">Permissions</a> :: \n",
                                    "&nbsp::<a title=\"Supprimer la page\" href=\"",$this->href("deletepage")."\"";
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

<input name="phrase" size="15" class="searchbox" value="Rechercher" onfocus="this.value='';" />
<?php echo  $this->FormClose(); ?>

</div>
</div>
</div>




<?php
	if ($this->GetConfigValue("debug")=="yes")
	{
		echo "<span class=\"debug\"><b>Query log :</b><br />\n";
		$t_SQL=0;
        foreach ($this->queryLog as $query)
		{
			echo $query["query"]." (".round($query["time"],4).")<br />\n";
			$t_SQL = $t_SQL + $query["time"];
		}
		echo "</span>\n";

		echo "<span class=\"debug\">".round($t_SQL, 4)." s (total SQL time)</span><br />\n";
		
		list($g2_usec, $g2_sec) = explode(" ",microtime());
		define ("t_end", (float)$g2_usec + (float)$g2_sec);
		echo "<span class=\"debug\"><b>".round(t_end-t_start, 4)." s (total time)</b></span><br />\n";

		echo "<span class=\"debug\">SQL time represent : ".round((($t_SQL/(t_end-t_start))*100),2)."% of total time</span>\n";
	}
?> 

<div id="footer">
	<div class="center_wrapper">
	<div class="copyright">
		<a href="http://validator.w3.org/check/referer"><img src="images/valid_xhtml.png" alt="xhtmlvalid" /></a>
		<a href="http://jigsaw.w3.org/css-validator/check/referer"><img src="images/valid_css.png" alt="xhtmlvalid" /></a>
		<?php echo $this->GetWakkaName(); ?> fonctionne avec <a href="http://recitmst.qc.ca/wikinimst/wakka.php?wiki=SourcesDuWikiniMST">WikiNiMST <?php echo  $this->config["wikinimst_version"]; ?></a> :: <a href="http://templates.arcsin.se/">Design</a> par <a href="http://arcsin.se/">Arcsin</a>
	</div>
		<?php
		$footer_page=$this->config["footer_page"];
		if (isset($footer_page) and ($footer_page!="")){
			$wikiBody=$this;
			$pagebody=$wikiBody->tag;
			
			// Ajout Menu de Navigation

				$wikiFooter = $this;
				$wikiFooter->tag=$footer_page;
				$wikiFooter->SetPage($wikiFooter->LoadPage($wikiFooter->tag));
				echo $wikiFooter->Format($wikiFooter->page["body"], "wakka");

			$wikiBody->tag=$pagebody;
			$wikiBody->SetPage($wikiFooter->LoadPage($wikiFooter->tag));
		
		}

		?> 
		<div class="clearer">&nbsp;</div>

	</div>
</div>
</body>
</html>
