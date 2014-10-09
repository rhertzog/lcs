<?php

// actions/mypages.php
// written by Carlo Zottmann
// http://wakkawikki.com/CarloZottmann
/*
mypages.php
Copyright (c) 2003, Carlo Zottmann
Copyright 2003 David DELON
Copyright 2003 Jean Pascal MILCENT
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
$nompage = $this->getPageTag();	
if ($_REQUEST["action"] == "update")
{
$r=mysql_query("SELECT * FROM wikini_pages where comment_on='$nompage'");//ok
$qte=mysql_num_rows($r);
echo $pl;
for ($x=0;$x<$qte;$x++) 
	{
	$id=$modif[$x];
	mysql_query("UPDATE `wikini_pages` SET `latest` = 'N' WHERE `tag` = '$id'");//ok
	}
}

if ($user = $this->GetUser())
{


	echo "<b>Liste des commentaires reliés à la page $nompage.</b><br /><br />\n" ;

	$my_pages_count = 0;

	if ($pages = $this->LoadAllPages())
	{
				
		//Liste des commentaires sur les pages-----
		
		foreach ($pages as $page)
		{
		if ($page["comment_on"] == $nompage) {
		$nomcomment=$page["tag"]; ?>
		<a href="<?php echo  $this->href("supcomment", "", "supp=Comment8") ?>"><?php echo $page["tag"] ?></a>
		<?
		
		echo "<br />";
		}
		}//Fin liste des commentaires
			
	}
	
	else
	{
		echo "<i>Aucune page trouv&eacute;e.</i>" ;
	}

}
else
{
	echo "<i>Vous n'&ecirc;tes pas identifi&eacute; : impossible d'afficher la liste des pages que vous avez modifi&eacute;es.</i>" ;
}

?>
