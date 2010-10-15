<?php

/**
* action WikiNi permettant de placer des notes de bas de page dans le texte
* 
* @author Didier Loiseau 
* @copyright 2005   Didier Loiseau
* @license This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* @todo tree & levels
*/

/*
* Parametres:
* 	- note: le texte de la note
* 	- /disp: affiche toutes les notes précédemment générées.
*/

if ($note = $this->GetParameter('note'))
{
	if (empty($this->footnotes))
	{
		$this->footnotes = array();
		$this->footnotesNextId = 1; // le numéro de la prochaine note à afficher
	} 
	$this->footnotes[] = $note;
	$id = count($this->footnotes);
	echo '<a href="'. $this->Href(('show' == ($method = $this->GetMethod())) ? '' : $method) . '#footnote' . $id . '" title="sauter vers la note ' . $id . '"><sup id="back_footnote'.$id,'">' . $id . '</sup></a>';
} elseif ($this->GetParameter('disp') || $this->GetParameter('display'))
{
	if (empty($this->footnotes) || count($this->footnotes) < $this->foototesNextId)
	{
		echo '<strong>Erreur ActionNBP</strong>: il n\'y a aucune note restante &agrave; afficher !';
	} 
	else
	{
		echo '<ul class="footnote" style="list-style: none;">';
		$count = count($this->footnotes);
		for($id = $this->footnotesNextId; $id <= $count; $id++)
		{
			echo "\n\t" . '<li id="footnote'.$id.'"><a href="#back_footnote'.$id.'">Note ' . $id . ':</a> ' . $this->Format($this->footnotes[$id - 1]) . '</li>';
		} 
		echo "\n</ul>";
		$this->footnotesNextId = $count + 1;
	} 
} 
else
{
	echo '<strong>Erreur Action nbp</strong>: param&egrave;tre manquant. Vous devez sp&eacute;cifier soit <em>note</em> soit <em>disp</em>';
} 

?>