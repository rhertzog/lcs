<?
require ("en-tete.php");
?>

<H1>Installation du m&eacute;canisme de r&eacute;plication sur le serveur BCDI</H1>
<UL>
<LI>T&eacute;l&eacute;chargez le <A HREF="cwRsync_Server_2.0.10.3001_Installer.exe">programme d'installation</A> du programme de synchronisation</LI>
<LI>Lancez l'installationi en validant les choix par d&eacute;faut</LI>
<LI>Editez le fichier <CODE>C:\Program Files\cwRsyncServer\rsyncd.conf</CODE> &agrave; l'aide du bloc notes</LI>
<LI>Placez-y le contenu suivant
<PRE>use chroot = false
strict modes = false
hosts allow = *
log file = rsyncd.log
pid file = rsyncd.pid

# Module definitions
# Remember cygwin naming conventions : c:\work becomes /cygwin/c/work
#

[bcdi]
path = /cygdrive/c/bcdiserv/data
read only = false
transfer logging = yes
</PRE>
en prenant soin de remplacer bcdiserv par le r&eacute;pertoire d'installlation de bcdi</LI>
<LI>Pour finir, passez le serveur Rsync en mode d&eacute;marrage automatique en proc&eacute;dant ainsi:
<UL>
<LI>Clic droit sur Poste de travail / Gerer</LI>
<LI>Cliquer sur Services</LI>
<LI>Double cliquer sur <CODE>RsyncServer</CODE></LI>
<LI>Passez le d&eacute;marrage en "Automatique", puis cliquez sur "D&eacute;marrer"</LI>
</UL>
<IMG SRC="Images/rsync.png">
</LI>
</UL>
Le dispositif de r&eacute;plication de la base sur Lcs est en place.
<?
require ("pieds_de_page.php");
?>
