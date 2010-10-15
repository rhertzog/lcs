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
$TITRE = "Référentiels";
?>

<div class="hc noprint">
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=gestion">Créer / paramétrer les référentiels.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=edition">Modifier le contenu des référentiels.</a>
	<hr />
</div>


<?php
// Afficher la bonne page et appeler le bon js / ajax par la suite
$fichier_section = './pages/'.$PAGE.'_'.$SECTION.'.php';
if(is_file($fichier_section))
{
	$PAGE = $PAGE.'_'.$SECTION ;
	require($fichier_section);
}
else
{
	echo'<ul class="puce">';
	echo'	<li><a href="./index.php?page='.$PAGE.'&amp;section=gestion"><img alt="" src="./_img/menu/professeur_referentiel_gestion.png" /> Créer / paramétrer les référentiels (importer, supprimer, partager, mode de calcul).</a></li>';
	echo'	<li><a href="./index.php?page='.$PAGE.'&amp;section=edition"><img alt="" src="./_img/menu/professeur_referentiel_edition.png" /> Modifier le contenu des référentiels (domaines, thèmes, items).</a></li>';
	echo'</ul>';
}
?>
