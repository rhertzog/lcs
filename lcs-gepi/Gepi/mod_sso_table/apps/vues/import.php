<?php
/*
* $Id: import.php 7744 2011-08-14 13:07:15Z dblanqui $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
// On emp�che l'acc�s direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};
?>
[onload;file=menu.php]
<p>Vous allez mettre en place les correspondances entre les logins de g&eacute;pi et ceux d'un logiciel tiers :</p>
<p class="title-page">Veuillez fournir le fichier csv :</p>
<form action="index.php?ctrl=import&action=result" enctype='multipart/form-data' method="post">
    <p>
        <input type="radio" name="choix" value="erreur" checked="checked" />Recherche des erreurs : seules les erreurs sont affich�es, aucune donn�e n'est �crite dans la base<br/>
        <input type="radio" name="choix" value="test" />Test : toutes les entr�es sont list�es avec leur �tat, aucune donn�e n'est �crite dans la base<br/>
        <input type="radio" name="choix" value="ecrit" />Inscription dans la base : toutes les entr�es sont trait�es puis list�es avec leur �tat. Les donn�es sont �crites dans la base <br/>
    </p>
    <input type='file'  name='fichier'  />
    <input type='submit' value='T&eacute;lechargement' />
</form>
</body>
</html>