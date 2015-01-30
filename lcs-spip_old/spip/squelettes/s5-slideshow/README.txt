S5 version 1.1
--------------
Eric Meyer
http://meyerweb.com/eric/tools/s5/


Themes issus de http://yatil.de/s5/


Adapté pour SPIP par Pierre Bourgeois, Emmanuel Saint-James, Fil
http://www.spip-contrib.net/Squelette-S5-Slide-Show

Compatible avec le plugin Crayons !


Licence : Creative Commons by-sa



---------------------------------------------------------------

Installation :

mettre le répertoire s5-slideshow dans le répertoire squelettes/ (ou quelque
part sur le "chemin" du système)

Rubrique > Présentations   (ex: id_rubrique = 196)

Une sous-rubrique par slideshow
	> Présentations > Présentation 1
	> Présentations > Présentation 2


Créer alors dans squelettes/ un fichier rubrique-196.html
contenant exactement :

<INCLUDE{fond=s5-slideshow/s5}{id_rubrique}{theme=yatil}>

la partie {theme=yatil} permet de spécifier un thème, à choisir parmi ceux qu'on aura installés dans le sous-répertoire s5-slideshow/ui/ :
	blue
	default
	flower
	i18n
	pixel
	yatil



