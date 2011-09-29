<?php
	/* $Id: textes.inc.php 7120 2011-06-05 08:59:33Z crob $
	
		Fichier destin� � stocker des portions de texte susceptibles d'�tre appel�es dans diverses pages.
		Exemple:
			require("../lib/textes.inc.php");
			echo $cdt_texte_bo;
	*/

	//=============================================
	// Cahiers de textes
	$cdt_texte_bo="<p>Le \"<em>cahier de textes de classe [...] constitue un document officiel, � valeur juridique.<br />
[...]<br />
Le cahier de textes mentionnera, d'une part, le contenu de la s�ance et, d'autre part, le travail � effectuer, accompagn�s l'un et l'autre de tout document, ressource ou conseil � l'initiative du professeur, sous forme de textes, de fichiers joints ou de liens. [...] Les travaux donn�s aux �l�ves porteront, outre la date du jour o� ils sont donn�s, l'indication du jour o� ils doivent �tre pr�sent�s ou remis par l'�l�ve.<br />
Les textes des devoirs et des contr�les figureront au cahier de textes, sous forme de textes ou de fichiers joints. Il en sera de m�me du texte des exercices ou des activit�s lorsque ceux-ci ne figureront pas sur les manuels scolaires.<br />
En ce qui concerne les travaux effectu�s dans le cadre de groupes, ou de sous-groupes d'�l�ves de diff�rents niveaux de comp�tences, et en vue de favoriser un accompagnement plus personnalis�, le contenu de ces activit�s sp�cifiques sera �galement mentionn� dans le cahier de textes.</em>\"<br />
(<i>r�f. : <a href='http://www.education.gouv.fr/cid53060/mene1020076c.html'>B.O. n�32 du 09/09/2010</a></i>)</p>";
	//=============================================
	// Bulletins, graphes,...
	$explication_bulletin_ou_graphe_vide="Les moyennes et appr�ciations des bulletins ne sont remplies qu'en fin de p�riode.<br />
Si aucune moyenne n'apparait sur le graphique, c'est probablement parce que les professeurs n'ont pas encore rempli les bulletins.<br />
Avant la fin de la p�riode, il se peut que seules les notes des devoirs soient saisies.<br />
Elles seront consultables dans le Relev� de notes si vous y avez acc�s.";
?>