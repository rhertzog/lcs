<?php

/*
spam.php

Copyright 2006 Pierre Lachance

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
if  ($this->GetUserName()== $this->config[admin_wiki] && $this->config[comments_spam_enable]==1)
{
	if (isset ($_POST[inserer]))
	{
		if (strlen($_POST[motspam])==0)
			{
			?>
			Aucun mot inscrit.
			<?php 
			}
		$r="insert into antispam (aspm_string) values ('$_POST[motspam]')";
		mysql_query($r) or die (mysql_error());
		?>
		<?php echo "$_POST[motspam]"; ?> est inséré dans la base de données.
	<?php
		
	}
	?> 
	<h3>Ajouter un mot dans la liste de mot spam</h3>
	
	<?php echo  $this->FormOpen("") ?>
	
	<input type="hidden" name="motspam" value="<?php echo $motspam; ?>">
	<input type="text" size="35" name="motspam"><br />
	<br /><br />
	<input type="submit" name="inserer" value="Enregistrer" style="width: 120px">
	<br /><br />
	<?php
	print($this->FormClose());
	$r=mysql_query("SELECT * from antispam ORDER BY aspm_string") or die ("peut pas selecter");
	WHILE ($e=mysql_fetch_array($r)) //On fait une boucle sur tous les projets--------------
	{
	print "$e[aspm_string]<br />";
	}
}
else 	{	
	
		echo"<em>Vous n'&ecirc;tes pas l'administrateur de ce wiki.</em>";
	}
	?>
