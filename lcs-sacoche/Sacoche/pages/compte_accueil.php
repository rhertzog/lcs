<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Générale Publique GNU pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
$TITRE = "Bienvenue dans votre espace identifié !";
?>

<ul class="puce">
	<li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=environnement_generalites__ergonomie_generale">DOC : Ergonomie générale.</a></span></li>
</ul>

<hr />

<p class="astuce">Utilisez le menu ci-dessus pour naviguer dans votre espace !</p>

<?php
if($_SESSION['USER_PROFIL']=='webmestre')
{
	echo'<p class="astuce">Pour vous connecter à cet espace, utilisez l\'adresse <b>'.SERVEUR_ADRESSE.'?webmestre</b></p>';
}
else
{
	if(HEBERGEUR_INSTALLATION=='multi-structures')
	{
		echo'<div class="astuce">Adresse à utiliser pour une sélection automatique de l\'établissement depuis n\'importe quel ordinateur :</div>';
		echo'<p class="hc"><b>'.SERVEUR_ADRESSE.'?id='.$_SESSION['BASE'].'</b></p>';
	}
	if($_SESSION['CONNEXION_MODE']=='cas')
	{
		echo'<div class="astuce">Adresse à utiliser pour une connexion automatique avec les identifiants de l\'ENT :</div>';
		echo'<p class="hc"><b>'.SERVEUR_ADRESSE.'?page=public_login_CAS&amp;f_base='.$_SESSION['BASE'].'</b></p>';
	}
}
?>

<?php
?>
