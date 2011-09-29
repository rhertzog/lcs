<?php
/*
* $Id: help.php 7805 2011-08-17 13:43:12Z dblanqui $
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
<h2>Ce module sert � cr�er une correspondance entre les logins G�pi et ENT dans le cas d'une authentification CAS</h2>
<h3>Il y a trois possibilit�s pour la mise en place de la correspondance :</h3>
<ol>
	<li><strong>Par importation des correspondances depuis un fichier csv :</strong></li>
	<p>Cliquer sur Import de donn�es</p>
	<p>Le fichier � fournir doit s'appeler correspondances.csv</p>
	<p>Il ne doit contenir par ligne que deux donn�es s�par�es par un ;</p>
	<p>La premi�re donn�e est le login G�pi et la deuxi�me le login sso (de l'ent par exemple)</p>
	<p>Voici un exemple</p>
	<img src='img/fichier.png' />
	<p class='message_red'>V�rifiez bien dans un logiciel comme notepad++ par exemple qu'il n'y a pas de lignes vides.. </p>
	<p class='message_red'>Attention au formattage des donn�es avec des tableurs comme Excel par exemple.. </p>
	<p>Une fois le traitement effectu� vous obtiendrez un tableau avec les r�sultats :</p>
	<img src='img/resultat.png' />
	<p>Si l'utilisateur n'existe pas dans G�pi , ou si une entr�e existe d�ja dans la table de correspondance (login G�pi ou sso),
	aucune correspondance n'est mis en place.</p>
	<p>Si l'utilisateur existe dans G�pi mais que le compte n'est pas param�tr� en sso la correspondance est mise en place mais le mode de connexion doit �tre modifi� dans G�pi </p>
	<p>Dans les autres cas la correspondance est mise en place.</p>
	<br />

	<li><strong>Par mise en place manuelle de la correspondance pour un utilisateur de G�pi :</strong></li>
	<p>Cliquer sur <em>Mise � jour de donn�es </em></p>
	<p>Rechercher le nom d'un utilisateur de G�pi </p>
	<p class='message_red'>Attention cet utilisateur doit avoir son mode d'authentification param�tr� en sso</p>
	<p>Cliquez sur le login de l'utilisateur choisi :</p>
	<p>Vous pouvez entrer le login sso pour la correspondance avec G�pi.</p>
	<p>Si une correspondance existe d�ja le login sso s'affiche. Vous pouvez le mettre � jour.</p>
	<p>Voici une copie d'�cran :</p>
	<img src='img/maj.png' />
	<br />
	<br />

	<li><strong>Par recherche des correspondances sur les noms et pr�noms, � partir d'un fichier csv :</strong></li>	
	<p>Cliquer sur <em>CVS export ENT</em></p>
	<p>Le fichier � fournir doit s'appeler <em>ENT-Identifiants.csv</em></p>
	<p>Il doit contenir par ligne treize champs s�par�s par un ;</p>
	<ol>
	  <li>RNE de l'�tablissement : non utilis�</li>
	  <li>UID : identifiant SSO dans l'ENT, c'est ce champ qui sert de jointure</li>
	  <li>classe de l'�l�ve : sert � rep�rer les comptes parents et �l�ves</li>
	  <li>profil : sert � diff�rencier les doublons parents et �l�ves, les intitul�s peuvent �tre diff�rents de ceux de G�pi mais doivent �tre coh�rents</li>
	  <li>pr�nom : le premier doit correspondre � celui de G�pi</li>
	  <li>nom : doit correspondre � celui de G�pi</li>
	  <li>login : login dans l'ENT, non utilis�</li>
	  <li>mot de passe : mot de passe dans l'ENT, non utilis�</li>
	  <li>cle de jointure : non utilis�</li>
	  <li>uid p�re : sert � rep�rer les �l�ves et � retrouver les responsables en cas de doublon</li>
	  <li>uid m�re : sert � rep�rer les �l�ves et � retrouver les responsables en cas de doublon</li>
	  <li>uid tuteur1 : sert � rep�rer les �l�ves et � retrouver les responsables en cas de doublon</li>
	  <li>uid tuteur2 : sert � rep�rer les �l�ves et � retrouver les responsables en cas de doublon</li>
	</ol>
	<p>Les champs non utilis�s peuvent �tre laiss�s vides</p>
	<p>Voici un exemple</p>
	<img src='img/identifiants.png' />
	<p class='message_red'>
		V�rifiez bien dans un logiciel comme notepad++ par exemple qu'il n'y a pas de lignes vides..
	</p>
	<p>
		Vous pouvez laisser la premi�re ligne avec les noms de champs. Lors du traitement, vous obtiendrez un enregistrement en erreur dans lequel vous pourrez v�rifier sur quels champs vous faites la recherche
	</p>
	<img src='img/cvs_ent_id.png' />
	<p class='message_red'>Attention au formatage des donn�es avec des tableurs comme Excel par exemple.. </p>

	<p>Avant de mettre en place les correspondances dans la base, vous pouvez tester le r�sultat de l'import :</p>
	<img src='img/cvs_ent.png' />
	<ul>
	<li>Rechercher des erreurs : Toutes les erreurs sont affich�es, aucune donn�e n'est �crite dans la base</li>
	<li>Test : Toutes les lignes sont trait�es et affich�es mais aucune donn�e n'est �crite dans la base</li>
	<li>Inscription dans la base : Toutes les lignes sont trait�es et affich�es, les correspondances sont �crites au besoin dans la base</li>

	</ul>
	<p>Une fois le traitement effectu� vous obtiendrez un tableau avec les r�sultats :</p>
	<img src='img/resultat.png' />
	<p>Si l'utilisateur n'existe pas dans G�pi, aucune correspondance n'est mise en place.</p>
	<p>Si une entr�e existe d�ja dans la table de correspondance, aucune correspondance n'est mise en place et on affiche si la correspondance est diff�rente.</p>
	<p>Si l'utilisateur existe dans G�pi mais que le compte n'est pas param�tr� en SSO, la correspondance est mise en place mais le mode de connexion doit �tre modifi� dans G�pi.</p>
	<p>Dans les autres cas la correspondance est mise en place.</p>

</ol>
</body>
 </html>