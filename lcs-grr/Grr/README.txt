Derni�re modification du fichier : 15/07/2008

GRR1.9.5
=======================================

Laurent DELINEAU <laurent.delineau@ac-poitiers.fr>
Mathieu Ignacio <mignacio@april.org>

http://grr.mutualibre.org


GRR est un outil de gestion et de r�servation de ressources. GRR est une adaptation d'une
application MRBS.


1. Installation
2. License
3. Remarques concernant la s�curit�


1. Installation
=======================================

Pour obtenir une description compl�te de la proc�dure d'installation,
veuillez vous reporter au fichier "INSTALL.txt".

Pour une installation simplifi�e, d�compressez simplement cette archive sur un
serveur, et indiquez l'adresse o� se trouvent les fichiers extraits dans un navigateur (ex: http://www.monsite.fr/grr).

* Pr�alables pour l'installation automatis�e :
- disposer d'un espace FTP sur un serveur, pour y transf�rer les fichiers
- disposer d'une base de donn�es MySQL (adresse du serveur MySQL, login, mot de passe)

2. Licence
=======================================

GRR est publi� sous les termes de la GNU General Public Licence, dont le
contenu est disponible dans le fichier "license.txt", en anglais et dans le fichiers "licence_fr.html" en fran�ais.
GRR est gratuit, vous pouvez le copier, le distribuer, et le modifier, �
condition que chaque partie de GRR r�utilis�e ou modifi�e reste sous licence
GNU GPL.
Par ailleurs et dans un soucis d'efficacit�, merci de rester en contact avec
le d�veloppeur de GRR pour �ventuellement int�grer vos contributions � une distribution ult�rieure.

Enfin, GRR est livr� en l'�tat sans aucune garantie. Les auteurs de cet outil
ne pourront en aucun cas �tre tenus pour responsables d'�ventuels bugs.


3. Remarques concernant la s�curit�
=======================================

La s�curisation de GRR est d�pendante de celle du serveur. Nous vous recommandons d'utiliser
un serveur Apache sous Linux, en utilisant le protocole https (transferts de donn�es crypt�es), et en
veillant � toujours utiliser les derni�res versions des logiciels impliqu�s
(notamment Apache et PHP).

L'EQUIPE DE DEVELOPPEMENT DE GRR NE SAURAIT EN AUCUN CAS ETRE TENUE
POUR RESPONSABLE EN CAS D'INTRUSION EXTERIEURE LIEE A UNE FAIBLESSE DE GRR OU
DE SON SUPPORT SERVEUR.