<?php
/*
raw.php

Copyright 2002  David DELON
Copyright 2003  Eric FELDSTEIN
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
		// display raw page
		echo $this->page["body"];
	}
}
else
{
	return;
}
?>