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


/*
Copyright 2004  .... OF SUCH DAMAGE.
*/
$texte = $this->getParameter("texte"); 
$ici = $this->getParameter("ici");
$voir = $this->getParameter("voir");
$haut = $this->getParameter("haut");
$bas = $this->getParameter("bas");
if(!empty($haut)||!empty($bas)) echo "<div class=\"ancrer\">";
if(!empty($haut))  echo '<a href="#'. $haut .'">Haut </a>';
if(!empty($bas))  echo '<a href="#'. $bas .'">Bas</a>';
if(!empty($haut)||!empty($bas)) echo "</div>";
echo "<div class=\"ancre\">";
if(!empty($ici)) echo '<strong><a id="'.$ici.'"></a></strong> &nbsp;';
if(!empty($voir))  echo '<a href="#'. $voir .'">'. $voir .'</a>&nbsp;';
if(!empty($texte) ) echo $this->Format($texte);
echo "</div>";
?> 
