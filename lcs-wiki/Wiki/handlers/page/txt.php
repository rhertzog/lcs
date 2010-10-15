<?php
/*
txt.php

Copyright 2006 Pierre Lachance et Pascal Lapalme

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

//vérification de sécurité
if (!eregi("wakka.php", $_SERVER['PHP_SELF'])) {
    die ("acc&egrave;s direct interdit");
}

if ($this->HasAccess("read"))
{
	if (!$this->page)
	{
		return;
	}
	else
	{
	header("Content-type: text/plain");
	header("Content-disposition: attachment; filename=".htmlentities($this->GetPageTag())."_" . date("Ymd").".txt");
	echo $this->page["body"];
	exit;
	
	}
}
else
{
	return;
}
?>