<?php

/*
backlinks.php

Copyright 2002  Patrick PAUL
Copyright 2003  David DELON
Copyright 2003  Charles NEPOTE

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


	if ($this->GetParameter("page"))
	{
		$page = $this->GetParameter("page");
		$title = "Pages ayant un lien vers ".$this->ComposeLinkToPage($page)."&nbsp;: <br />\n";
	}
	else
	{
		$page = $this->getPageTag();
		$title = "Pages ayant un lien vers la page courante&nbsp;: <br />\n";
	}

	$pages = $this->LoadPagesLinkingTo($page);

	if ($pages)
	{
		//echo $title;
		if (!$exclude = $this->GetParameter("exclude"))
		{
		print("<div class=\"navigation\">");
		print("<strong>Navigation</strong>");
			foreach ($pages as $page)
			{
				echo "<strong>-></strong>".$this->ComposeLinkToPage($page["tag"]), "";
			}
			print("</div>");
		}
		else
		{
		print("<div class=\"navigation\">");
		print("<strong>Navigation-> </strong>");
			foreach ($pages as $page)
			{
				// Show link if it isn't an excluded link
				if (!preg_match("/".$page["tag"]."(;|$)/", $exclude)) echo $this->ComposeLinkToPage($page["tag"]), ",&nbsp;";
			}
		print("</div>");
		}
	}
	else
	{
		echo "<i>Aucune page n'a de lien vers ", $this->ComposeLinkToPage($page), ".</i>";
	}
?>