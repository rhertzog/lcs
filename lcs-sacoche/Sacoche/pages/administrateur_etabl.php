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
$TITRE = "Paramétrages";
?>

<div class="hc">
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=identite">Identité de l'établissement.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=matiere">Matières.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=palier">Paliers du socle.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=niveau">Niveaux.</a>	<br />
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=login">Format des noms d'utilisateurs.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=connexion">Mode d'identification.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=duree_inactivite">Délai avant déconnexion.</a>	<br />
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=demandes_eval">Demandes d'évaluations.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=options_eleve">Options de l'environnement élève.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=profils_validation">Profils autorisés à valider le socle.</a>
</div>

<hr />

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
	echo'<p><span class="astuce">Choisissez une rubrique ci-dessus...</span></p>';
}
?>