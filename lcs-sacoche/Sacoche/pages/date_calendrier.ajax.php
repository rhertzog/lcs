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
if($_SESSION['SESAMATH_ID']==ID_DEMO) {}

$jour_actuel    = date('j');
$mois_actuel    = date('n');
$annee_actuelle = date('Y');
$annee_mini     = 1970;
$annee_maxi     = $annee_actuelle+10;
// Création du calendrier
$j = (isset($_GET['j'])) ? (int)$_GET['j'] : $jour_actuel ;
$m = (isset($_GET['m'])) ? (int)$_GET['m'] : $mois_actuel ;
$a = (isset($_GET['a'])) ? (int)$_GET['a'] : $annee_actuelle ;
$tab_mois = array('','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre');
// On vérifie la cohérence des données pour éviter des soucis ensuite
if(!isset($tab_mois[$m]))
{
	$m = $mois_actuel;
}
if( ($a<$annee_mini) || ($a>$annee_maxi) )
{
	$a = $annee_actuelle;
}
// Pour éventuellement mettre en valeur le jour actuel
$test_jour_actuel = ( ($j==$jour_actuel) && ($m==$mois_actuel) && ($a==$annee_actuelle) ) ? true : false ;
// Jour de la semaine
$dayone = date('w',mktime(1,1,1,$m,1,$a));
if($dayone==0)
	$dayone=7;
$aplus  = $a+10;
$amoins = $a-10;
// Choix préliminaire de la période : Formulaires select pour choisir un mois et une année
$calendrier_navigation = '<div>';
$calendrier_navigation.= '<select id="m" name="m" class="actu">';
for($i=1;$i<=12;$i++)
{
	$selected = ($i==$m) ? ' selected="selected"' : '';
	$calendrier_navigation .= '<option value="'.$i.'"'.$selected.'>'.$tab_mois[$i].'</option>';
}
$calendrier_navigation .= '</select>';
$calendrier_navigation .= '&nbsp;';
$calendrier_navigation .= '<select id="a" name="a" class="actu">';
if($amoins>=$annee_mini)
	$calendrier_navigation .= '<option value="'.$amoins.'">10 ans avant</option>';
for($i=$a-5;$i<=$a+5;$i++)
{
	if( ($i>=$annee_mini) && ($i<=$annee_maxi) )
	{
		$selected = ($i==$a) ? ' selected="selected"' : '';
		$calendrier_navigation .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	}
}
if($aplus <= $annee_maxi)
	$calendrier_navigation .= '<option value="'.$aplus.'">10 ans apres</option>';
$calendrier_navigation .= '</select>';
$calendrier_navigation .= '</div>';
// Choix préliminaire de la période : Affichage du mois en cours et lien de navigation +- 1mois
$calendrier_navigation .= '<div class="v1 t12 g cr">';
$an   = ($m==1) ? $a-1 : $a;
$mois = ($m==1) ? 12   : $m-1;
if( ( ($m==1) && ($a<=$annee_mini) )==false)
	$calendrier_navigation .= '<input type="image" class="actu" src="./_img/fleche_g1.gif" lang="'.$mois.'_'.$an.'" />&nbsp;';
else
	$calendrier_navigation .= '<img alt="1 mois avant" src="./_img/fleche_g0.gif" />&nbsp;';
$calendrier_navigation .= $tab_mois[$m].' '.$a;
$an   = ($m==12) ? $a+1 : $a;
$mois = ($m==12) ? 1   : $m+1;
if( ($m<$mois_actuel) || ($a<$annee_maxi) )
	$calendrier_navigation .= '&nbsp;<input type="image" class="actu" src="./_img/fleche_d1.gif" lang="'.$mois.'_'.$an.'" />';
else
	$calendrier_navigation .= '<img alt="1 mois apres" src="./_img/fleche_d0.gif" />&nbsp;';
$calendrier_navigation .= '</div>';
// Choix final de la période : tableau du calendrier du mois sélectionné
$calendrier_affichage = '<table summary="calendrier" cellspacing="1" border="1" style="margin:auto">';
$calendrier_affichage.= '<tr class="c1"><th>L</th><th>M</th><th>M</th><th>J</th><th>V</th><th>S</th><th>D</th>';
for($i=1;$i<=42;$i++)
{
	if(($i%7)==1)
		$calendrier_affichage.='</tr><tr>';
	if( ($i<(cal_days_in_month(CAL_GREGORIAN,$m,$a)+$dayone)) && ($i>=$dayone) )
	{
		$val = $i-$dayone+1;
		$class = ( ($val==$jour_actuel) && ($m==$mois_actuel) && ($a==$annee_actuelle) ) ? ' class="v"' : '';
		$calendrier_affichage .= '<td'.$class.'><a class="actu" href="'.sprintf("%02u",$val).'-'.sprintf("%02u",$m).'-'.$a.'">'.$val.'</a></td>';
	} 
	else
	{
		$calendrier_affichage .= '<td style="background-color:silver">&nbsp;</td>';
	}
}
$calendrier_affichage .= '</table>';
echo'<h5>Calendrier</h5>';
echo'<form id="form_calque" action="">';
echo'	<h6>Choisir une période :</h6>';
echo'	<div>'.$calendrier_navigation.'</div>';
echo'	<h6>Puis cliquer sur une date :</h6>';
echo'	<div>'.$calendrier_affichage.'</div>';
echo'	<div><button id="fermer_calque" type="button"><img alt="" src="./_img/bouton/annuler.png" /> Annuler / Fermer</button></div>';
echo'</form>';

?>
