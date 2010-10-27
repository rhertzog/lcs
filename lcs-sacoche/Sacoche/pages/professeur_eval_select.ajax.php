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
if(($_SESSION['SESAMATH_ID']==ID_DEMO)&&($_POST['f_action']!='Afficher_evaluations')&&($_POST['f_action']!='ordonner')&&($_POST['f_action']!='saisir')&&($_POST['f_action']!='voir')){exit('Action désactivée pour la démo...');}

$action      = (isset($_POST['f_action']))      ? clean_texte($_POST['f_action'])      : '';
$date_debut  = (isset($_POST['f_date_debut']))  ? clean_texte($_POST['f_date_debut'])  : '';
$date_fin    = (isset($_POST['f_date_fin']))    ? clean_texte($_POST['f_date_fin'])    : '';
$ref         = (isset($_POST['f_ref']))         ? clean_texte($_POST['f_ref'])         : '';
$date        = (isset($_POST['f_date']))        ? clean_texte($_POST['f_date'])        : '';
$info        = (isset($_POST['f_info']))        ? clean_texte($_POST['f_info'])        : '';
$descriptif  = (isset($_POST['f_descriptif']))  ? clean_texte($_POST['f_descriptif'])  : '';
$contenu     = (isset($_POST['f_contenu']))     ? clean_texte($_POST['f_contenu'])     : '';
$detail      = (isset($_POST['f_detail']))      ? clean_texte($_POST['f_detail'])      : '';
$orientation = (isset($_POST['f_orientation'])) ? clean_texte($_POST['f_orientation']) : '';
$marge_min   = (isset($_POST['f_marge_min']))   ? clean_texte($_POST['f_marge_min'])   : '';
$couleur     = (isset($_POST['f_couleur']))     ? clean_texte($_POST['f_couleur'])     : '';

$dossier_export = './__tmp/export/';
$fnom = 'saisie_'.$_SESSION['BASE'].'_'.$_SESSION['USER_ID'].'_'.$ref;

// Si "ref" est renseigné (pour Éditer ou Retirer ou Saisir ou ...), il contient l'id de l'évaluation + '_' + l'initiale du type de groupe + l'id du groupe
// Dans le cas d'une duplication, "ref" sert à retrouver l'évaluation d'origine pour évenuellement récupérer l'ordre des items
if(mb_strpos($ref,'_'))
{
	list($devoir_id,$groupe) = explode('_',$ref,2);
	$devoir_id = clean_entier($devoir_id);
	$groupe  = clean_texte($groupe);
}
else
{
	$devoir_id = 0;
	$groupe = '';
}

// Si "groupe" est transmis via "ref", il contient l'initiale du type de groupe + l'id du groupe
$groupe_type = 'eval';
$groupe_id   = ($groupe) ? clean_entier(mb_substr($groupe,1)) : 0 ;

function positif($n) {return $n;}
// Contrôler la liste des items transmis
$tab_id = (isset($_POST['tab_id'])) ? array_map('clean_entier',explode(',',$_POST['tab_id'])) : array() ;
$tab_id = array_filter($tab_id,'positif');
// Contrôler la liste des items transmis
$tab_items = (isset($_POST['f_compet_liste'])) ? explode('_',$_POST['f_compet_liste']) : array() ;
$tab_items = array_map('clean_entier',$tab_items);
$tab_items = array_filter($tab_items,'positif');
$nb_items  = count($tab_items);
// Contrôler la liste des élèves transmis
$tab_eleves = (isset($_POST['f_eleve_liste']))  ? explode('_',$_POST['f_eleve_liste'])  : array() ;
$tab_eleves = array_map('clean_entier',$tab_eleves);
$tab_eleves = array_filter($tab_eleves,'positif');
$nb_eleves  = count($tab_eleves);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Afficher une liste d'évaluations
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

if( ($action=='Afficher_evaluations') && $date_debut && $date_fin )
{
	// Formater les dates
	$date_debut_mysql = convert_date_french_to_mysql($date_debut);
	$date_fin_mysql   = convert_date_french_to_mysql($date_fin);
	// Vérifier que la date de début est antérieure à la date de fin
	if($date_debut_mysql>$date_fin_mysql)
	{
		exit('Erreur : la date de début est postérieure à la date de fin !');
	}
	$DB_TAB = DB_STRUCTURE_lister_devoirs_prof($_SESSION['USER_ID'],0,$date_debut_mysql,$date_fin_mysql);
	foreach($DB_TAB as $DB_ROW)
	{
		// Formater la date et la référence de l'évaluation
		$date_affich = convert_date_mysql_to_french($DB_ROW['devoir_date']);
		$ref = $DB_ROW['devoir_id'].'_'.strtoupper($DB_ROW['groupe_type']{0}).$DB_ROW['groupe_id'];
		$cs = ($DB_ROW['items_nombre']>1) ? 's' : '';
		$us = ($DB_ROW['users_nombre']>1) ? 's' : '';
		// Afficher une ligne du tableau
		echo'<tr>';
		echo	'<td><i>'.html($DB_ROW['devoir_date']).'</i>'.html($date_affich).'</td>';
		echo	'<td lang="'.html($DB_ROW['users_listing']).'">'.html($DB_ROW['users_nombre']).' élève'.$us.'</td>';
		echo	'<td>'.html($DB_ROW['devoir_info']).'</td>';
		echo	'<td lang="'.html($DB_ROW['items_listing']).'">'.html($DB_ROW['items_nombre']).' item'.$cs.'</td>';
		echo	'<td class="nu" lang="'.$ref.'">';
		echo		'<q class="modifier" title="Modifier cette évaluation (date, description, ...)."></q>';
		echo		'<q class="ordonner" title="Réordonner les items de cette évaluation."></q>';
		echo		'<q class="dupliquer" title="Dupliquer cette évaluation."></q>';
		echo		'<q class="supprimer" title="Supprimer cette évaluation."></q>';
		echo		'<q class="imprimer" title="Imprimer un cartouche pour cette évaluation."></q>';
		echo		'<q class="saisir" title="Saisir les acquisitions des élèves à cette évaluation."></q>';
		echo		'<q class="voir" title="Voir les acquisitions des élèves à cette évaluation."></q>';
		echo	'</td>';
		echo'</tr>';
	}
	exit();
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Ajouter une nouvelle évaluation
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

elseif( (($action=='ajouter')||(($action=='dupliquer')&&($devoir_id))) && $date && $nb_eleves && $nb_items )
{
	// Il faut commencer par créer un nouveau groupe de type "eval", utilisé uniquement pour cette évaluation (c'est transparent pour le professeur)
	$groupe_id = DB_STRUCTURE_ajouter_groupe($groupe_type,$_SESSION['USER_ID'],'','',0);
	// Il faut y affecter tous les élèves choisis
	DB_STRUCTURE_modifier_liaison_devoir_user($groupe_id,$tab_eleves,'creer');
	// Maintenant on peut insérer l'enregistrement de l'évaluation
	$date_mysql = convert_date_french_to_mysql($date);
	$devoir_id2 = DB_STRUCTURE_ajouter_devoir($_SESSION['USER_ID'],$groupe_id,$date_mysql,$info);
	// Insérer les enregistrements des items de l'évaluation
	DB_STRUCTURE_modifier_liaison_devoir_item($devoir_id2,$tab_items,'dupliquer',$devoir_id);
	// Afficher le retour
	$ref = $devoir_id2.'_'.strtoupper($groupe_type{0}).$groupe_id;
	$cs = ($nb_items>1) ? 's' : '';
	$us = ($nb_eleves>1)      ? 's' : '';
	echo'<td><i>'.html($date_mysql).'</i>'.html($date).'</td>';
	echo'<td lang="'.implode('_',$tab_eleves).'">'.$nb_eleves.' élève'.$us.'</td>';
	echo'<td>'.html($info).'</td>';
	echo'<td lang="'.implode('_',$tab_items).'">'.$nb_items.' item'.$cs.'</td>';
	echo'<td class="nu" lang="'.$ref.'">';
	echo	'<q class="modifier" title="Modifier cette évaluation (date, description, ...)."></q>';
	echo	'<q class="ordonner" title="Réordonner les items de cette évaluation."></q>';
	echo	'<q class="dupliquer" title="Dupliquer cette évaluation."></q>';
	echo	'<q class="supprimer" title="Supprimer cette évaluation."></q>';
	echo	'<q class="imprimer" title="Imprimer un cartouche pour cette évaluation."></q>';
	echo	'<q class="saisir" title="Saisir les acquisitions des élèves à cette évaluation."></q>';
	echo	'<q class="voir" title="Voir les acquisitions des élèves à cette évaluation."></q>';
	echo'</td>';
	exit();
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Modifier une évaluation existante
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else if( ($action=='modifier') && $devoir_id && $groupe_id && $date && $nb_eleves && $nb_items )
{
	// On commence par modifier l'affectation des élèves choisis
	// sacoche_jointure_user_groupe (maj)
	DB_STRUCTURE_modifier_liaison_devoir_user($groupe_id,$tab_eleves,'substituer');
	// Maintenant on peut modifier les autres données de l'évaluation (paramètres, items)
	$date_mysql = convert_date_french_to_mysql($date);
	// sacoche_devoir (maj) ainsi que sacoche_saisie (retirer superflu + maj)
	DB_STRUCTURE_modifier_devoir($devoir_id,$_SESSION['USER_ID'],$date_mysql,$info,$tab_items);
	// ************************ dans sacoche_saisie faut-il aussi virer certains scores élèves en cas de changement de groupe ... ???
	// sacoche_jointure_devoir_item
	DB_STRUCTURE_modifier_liaison_devoir_item($devoir_id,$tab_items,'substituer');
	// Afficher le retour
	$ref = $devoir_id.'_'.strtoupper($groupe_type{0}).$groupe_id;
	$cs = ($nb_items>1) ? 's' : '';
	$us = ($nb_eleves>1)      ? 's' : '';
	echo'<td><i>'.html($date_mysql).'</i>'.html($date).'</td>';
	echo'<td lang="'.implode('_',$tab_eleves).'">'.$nb_eleves.' élève'.$us.'</td>';
	echo'<td>'.html($info).'</td>';
	echo'<td lang="'.implode('_',$tab_items).'">'.$nb_items.' item'.$cs.'</td>';
	echo'<td class="nu" lang="'.$ref.'">';
	echo	'<q class="modifier" title="Modifier cette évaluation (date, description, ...)."></q>';
	echo	'<q class="ordonner" title="Réordonner les items de cette évaluation."></q>';
	echo	'<q class="dupliquer" title="Dupliquer cette évaluation."></q>';
	echo	'<q class="supprimer" title="Supprimer cette évaluation."></q>';
	echo	'<q class="imprimer" title="Imprimer un cartouche pour cette évaluation."></q>';
	echo	'<q class="saisir" title="Saisir les acquisitions des élèves à cette évaluation."></q>';
	echo	'<q class="voir" title="Voir les acquisitions des élèves à cette évaluation."></q>';
	echo'</td>';
	exit();
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Supprimer une évaluation existante
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else if( ($action=='supprimer') && $devoir_id && $groupe_id )
{
	// supprimer le groupe spécialement associé (invisible à l'utilisateur) et les entrées dans sacoche_jointure_user_groupe pour une évaluation avec des élèves piochés en dehors de tout groupe prédéfini
	DB_STRUCTURE_supprimer_groupe($groupe_id,$groupe_type,$with_devoir=false);
	// la suite est commune aux évals sur une classe ou un groupe
	DB_STRUCTURE_supprimer_devoir_et_saisies($devoir_id,$_SESSION['USER_ID']);
	// Afficher le retour
	exit('<ok>');
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Afficher le formulaire pour réordonner les items d'une évaluation
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else if( ($action=='ordonner') && $devoir_id )
{
	// liste des items
	$DB_TAB_COMP = DB_STRUCTURE_lister_items_devoir($devoir_id);
	if(!count($DB_TAB_COMP))
	{
		exit('Aucun item n\'est associé à cette évaluation !');
	}
	$tab_affich  = array();
	foreach($DB_TAB_COMP as $DB_ROW)
	{
		$item_ref = $DB_ROW['item_ref'];
		$texte_socle = ($DB_ROW['entree_id']) ? ' [S]' : ' [–]';
		$tab_affich[] = '<div id="i'.$DB_ROW['item_id'].'"><b>'.html($item_ref.$texte_socle).'</b> - '.html($DB_ROW['item_nom']).'</div>';
	}
	echo implode('<div class="ti"><input type="image" src="./_img/action_ordonner.png" /></div>',$tab_affich);
	echo'<p>';
	echo	'<button id="Enregistrer_ordre" type="button" value="'.$ref.'"><img alt="" src="./_img/bouton/valider.png" /> Enregistrer cet ordre</button>&nbsp;&nbsp;&nbsp;';
	echo	'<button id="fermer_zone_ordonner" type="button"><img alt="" src="./_img/bouton/annuler.png" /> Annuler / Retour</button>&nbsp;&nbsp;&nbsp;';
	echo	'<label id="ajax_msg">&nbsp;</label>';
	echo'</p>';
	exit();
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Afficher le formulaire pour saisir les items acquis par les élèves à une évaluation
//	Générer en même temps un csv à récupérer pour une saisie déportée
//	Générer en même temps un pdf contenant un tableau de saisie vide
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else if( ($action=='saisir') && $devoir_id && $groupe_id && $date && $descriptif ) // $date au format MySQL ; $descriptif séparé par ::: ; $info (facultative) reportées dans input hidden
{
	// liste des items
	$DB_TAB_COMP = DB_STRUCTURE_lister_items_devoir($devoir_id);
	// liste des élèves
	$DB_TAB_USER = DB_STRUCTURE_lister_eleves_actifs_regroupement($groupe_type,$groupe_id);
	// Let's go
	$item_nb = count($DB_TAB_COMP);
	if(!$item_nb)
	{
		exit('Aucun item n\'est associé à cette évaluation !');
	}
	$eleve_nb = count($DB_TAB_USER);
	if(!$eleve_nb)
	{
		exit('Aucun élève n\'est associé à cette évaluation !');
	}
	$separateur = ';';
	$tab_affich  = array(); // tableau bi-dimensionnel [n°ligne=id_item][n°colonne=id_user]
	$tab_user_id = array(); // pas indispensable, mais plus lisible
	$tab_comp_id = array(); // pas indispensable, mais plus lisible
	$tab_affich[0][0] = '<td>';
	$tab_affich[0][0].= '<span class="manuel"><a class="pop_up" href="'.SERVEUR_DOCUMENTAIRE.'?fichier=support_professeur__evaluations_saisie_resultats">DOC : Saisie des résultats.</a></span><p />';
	$tab_affich[0][0].= '<label for="radio_clavier"><input type="radio" id="radio_clavier" name="mode_saisie" value="clavier" /> <img alt="" src="./_img/pilot_keyboard.png" /> Pilotage au clavier</label> <img alt="" src="./_img/bulle_aide.png" title="Sélectionner un rectangle blanc<br />au clavier (flèches) ou à la souris<br />puis utiliser les touches suivantes :<br />&nbsp;1 ; 2 ; 3 ; 4 ; A ; N ; D ; suppr" /><br />';
	$tab_affich[0][0].= '<label for="radio_souris"><input type="radio" id="radio_souris" name="mode_saisie" value="souris" /> <img alt="" src="./_img/pilot_mouse.png" /> Pilotage à la souris</label> <img alt="" src="./_img/bulle_aide.png" title="Survoler une case du tableau avec la souris<br />puis cliquer sur une des images proposées." /><p />';
	$tab_affich[0][0].= '<button id="Enregistrer_saisie" type="button"><img alt="" src="./_img/bouton/valider.png" /> Enregistrer les saisies</button><input type="hidden" name="f_ref" id="f_ref" value="'.$ref.'" /><input id="f_date" name="f_date" type="hidden" value="'.$date.'" /><input id="f_info" name="f_info" type="hidden" value="'.html($info).'" /><br />';
	$tab_affich[0][0].= '<button id="fermer_zone_saisir" type="button"><img alt="" src="./_img/bouton/annuler.png" /> Annuler / Retour</button><br />';
	$tab_affich[0][0].= '<label id="ajax_msg">&nbsp;</label>';
	$tab_affich[0][0].= '</td>';
	// première ligne (noms prénoms des élèves)
	$csv_ligne_eleve_nom = $separateur;
	$csv_ligne_eleve_id  = $separateur;
	$csv_nb_colonnes = 1;
	foreach($DB_TAB_USER as $DB_ROW)
	{
		$tab_affich[0][$DB_ROW['user_id']] = '<th><img alt="'.html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']).'" src="./_img/php/etiquette.php?dossier='.$_SESSION['BASE'].'&amp;nom='.urlencode($DB_ROW['user_nom']).'&amp;prenom='.urlencode($DB_ROW['user_prenom']).'&amp;br" /></th>';
		$tab_user_id[$DB_ROW['user_id']] = html($DB_ROW['user_prenom'].' '.$DB_ROW['user_nom']);
		$csv_ligne_eleve_nom .= '"'.$DB_ROW['user_prenom'].' '.$DB_ROW['user_nom'].'"'.$separateur;
		$csv_ligne_eleve_id  .= $DB_ROW['user_id'].$separateur;
		$csv_nb_colonnes++;
	}
	$export_csv = $csv_ligne_eleve_id."\r\n";
	// première colonne (noms items)
	foreach($DB_TAB_COMP as $DB_ROW)
	{
		$item_ref = $DB_ROW['item_ref'];
		$texte_socle = ($DB_ROW['entree_id']) ? ' [S]' : ' [–]';
		$tab_affich[$DB_ROW['item_id']][0] = '<th><b>'.html($item_ref.$texte_socle).'</b><br />'.html($DB_ROW['item_nom']).'</th>';
		$tab_comp_id[$DB_ROW['item_id']] = $item_ref;
		$export_csv .= $DB_ROW['item_id'].str_repeat($separateur,$csv_nb_colonnes).$item_ref.$texte_socle.' '.$DB_ROW['item_nom']."\r\n";
	}
	$export_csv .= $csv_ligne_eleve_nom."\r\n\r\n";
	// cases centrales avec un champ input de base
	$num_colonne = 0;
	foreach($tab_user_id as $user_id=>$val_user)
	{
		$num_colonne++;
		$num_ligne=0;
		foreach($tab_comp_id as $comp_id=>$val_comp)
		{
			$num_ligne++;
			$tab_affich[$comp_id][$user_id] = '<td class="td_clavier" lang="C'.$num_colonne.'L'.$num_ligne.'"><input type="text" class="X" value="X" id="C'.$num_colonne.'L'.$num_ligne.'" name="'.$comp_id.'x'.$user_id.'" readonly="readonly" /></td>';
		}
	}
	// configurer le champ input
	$DB_TAB = DB_STRUCTURE_lister_saisies_devoir($devoir_id,$with_REQ=true);
	$bad = 'class="X" value="X"';
	foreach($DB_TAB as $DB_ROW)
	{
		// Test pour éviter les pbs des élèves changés de groupes ou des items modifiés en cours de route
		if(isset($tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']]))
		{
			$bon = 'class="'.$DB_ROW['saisie_note'].'" value="'.$DB_ROW['saisie_note'].'"';
			$tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']] = str_replace($bad,$bon,$tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']]);
		}
	}
	// affichage
	foreach($tab_affich as $comp_id => $tab_user)
	{
		if(!$comp_id)
		{
			echo'<thead>';
		}
		echo'<tr>';
		foreach($tab_user as $user_id => $val)
		{
			echo $val;
		}
		echo'</tr>';
		if(!$comp_id)
		{
			echo'</thead><tbody>';
		}
	}
	echo'</tbody>';
	// Enregistrer le csv
	$export_csv .= str_replace(':::',"\r\n",$descriptif)."\r\n\r\n";
	$export_csv .= 'CODAGES AUTORISÉS : 1 2 3 4 A N D'."\r\n";
	$zip = new ZipArchive();
	if ($zip->open($dossier_export.$fnom.'.zip', ZIPARCHIVE::CREATE)===TRUE)
	{
		$zip->addFromString($fnom.'.csv',csv($export_csv));
		$zip->close();
	}
	//
	// pdf contenant un tableau de saisie vide ; on a besoin de tourner du texte à 90°
	//
	require('./_fpdf/fpdf.php');
	require('./_fpdf/rpdf.php');
	require('./_inc/class.PDF.php');
	$sacoche_pdf = new PDF($orientation='landscape',$marge_min=10,$couleur='non');
	$sacoche_pdf->tableau_saisie_initialiser($eleve_nb,$item_nb);
	// 1ère ligne : référence devoir, noms élèves
	$sacoche_pdf->tableau_saisie_reference_devoir($descriptif);
	foreach($DB_TAB_USER as $DB_ROW)
	{
		$sacoche_pdf->tableau_saisie_reference_eleve($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']);
	}
	// ligne suivantes : référence item, cases vides
	$sacoche_pdf->SetXY($sacoche_pdf->marge_gauche , $sacoche_pdf->marge_haut+$sacoche_pdf->etiquette_hauteur);
	foreach($DB_TAB_COMP as $DB_ROW)
	{
		$item_ref = $DB_ROW['item_ref'];
		$texte_socle = ($DB_ROW['entree_id']) ? ' [S]' : ' [–]';
		$sacoche_pdf->tableau_saisie_reference_item($item_ref.$texte_socle,$DB_ROW['item_nom']);
		for($i=0 ; $i<$eleve_nb ; $i++)
		{
			$sacoche_pdf->Cell($sacoche_pdf->cases_largeur , $sacoche_pdf->cases_hauteur , '' , 1 , 0 , 'C' , false , '');
		}
		$sacoche_pdf->SetXY($sacoche_pdf->marge_gauche , $sacoche_pdf->GetY()+$sacoche_pdf->cases_hauteur);
	}
	$sacoche_pdf->Output($dossier_export.$fnom.'_sans_notes.pdf','F');
	exit();
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Voir les items acquis par les élèves à une évaluation
//	Générer en même temps un csv à récupérer pour une saisie déportée
//	Générer en même temps un pdf contenant un tableau de saisie vide
//	Générer en même temps un pdf contenant un tableau de saisie plein
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else if( ($action=='voir') && $devoir_id && $groupe_id && $date && $descriptif ) // $date française pour le csv ; $descriptif séparé par :::
{
	// liste des items
	$DB_TAB_COMP = DB_STRUCTURE_lister_items_devoir($devoir_id);
	// liste des élèves
	$DB_TAB_USER = DB_STRUCTURE_lister_eleves_actifs_regroupement($groupe_type,$groupe_id);
	// Let's go
	$item_nb = count($DB_TAB_COMP);
	if(!$item_nb)
	{
		exit('Aucun item n\'est associé à cette évaluation !');
	}
	$eleve_nb = count($DB_TAB_USER);
	if(!$eleve_nb)
	{
		exit('Aucun élève n\'est associé à cette évaluation !');
	}
	$separateur = ';';
	$tab_affich  = array(); // tableau bi-dimensionnel [n°ligne=id_item][n°colonne=id_user]
	$tab_user_id = array(); // pas indispensable, mais plus lisible
	$tab_comp_id = array(); // pas indispensable, mais plus lisible
	$tab_affich[0][0] = '<td><button id="fermer_zone_voir" type="button"><img alt="" src="./_img/bouton/retourner.png" /> Retour</button></td>';
	// première ligne (noms prénoms des élèves)
	$csv_ligne_eleve_nom = $separateur;
	$csv_ligne_eleve_id  = $separateur;
	foreach($DB_TAB_USER as $DB_ROW)
	{
		$tab_affich[0][$DB_ROW['user_id']] = '<th><img alt="'.html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']).'" src="./_img/php/etiquette.php?dossier='.$_SESSION['BASE'].'&amp;nom='.urlencode($DB_ROW['user_nom']).'&amp;prenom='.urlencode($DB_ROW['user_prenom']).'&amp;br" /></th>';
		$tab_user_id[$DB_ROW['user_id']] = html($DB_ROW['user_prenom'].' '.$DB_ROW['user_nom']);
		$csv_ligne_eleve_nom .= '"'.$DB_ROW['user_prenom'].' '.$DB_ROW['user_nom'].'"'.$separateur;
		$csv_ligne_eleve_id  .= $DB_ROW['user_id'].$separateur;
	}
	$export_csv = $csv_ligne_eleve_id."\r\n";
	$csv_lignes_scores = array();
	$csv_colonne_texte = array();
	// première colonne (noms items)
	foreach($DB_TAB_COMP as $DB_ROW)
	{
		$item_ref = $DB_ROW['item_ref'];
		$texte_socle = ($DB_ROW['entree_id']) ? ' [S]' : ' [–]';
		$tab_affich[$DB_ROW['item_id']][0] = '<th><b>'.html($item_ref.$texte_socle).'</b><br />'.html($DB_ROW['item_nom']).'</th>';
		$tab_comp_id[$DB_ROW['item_id']] = $item_ref;
		$csv_lignes_scores[$DB_ROW['item_id']][0] = $DB_ROW['item_id'];
		$csv_colonne_texte[$DB_ROW['item_id']]    = $item_ref.$texte_socle.' '.$DB_ROW['item_nom'];
	}
	// cases centrales vierges
	foreach($tab_user_id as $user_id=>$val_user)
	{
		foreach($tab_comp_id as $comp_id=>$val_comp)
		{
			$tab_affich[$comp_id][$user_id] = '<td title="'.$val_user.'<br />'.$val_comp.'">-</td>';
			$csv_lignes_scores[$comp_id][$user_id] = '';
		}
	}
	// ajouter le contenu
	$tab_dossier = array( ''=>'' , 'RR'=>$_SESSION['NOTE_IMAGE_STYLE'].'/' , 'R'=>$_SESSION['NOTE_IMAGE_STYLE'].'/' , 'V'=>$_SESSION['NOTE_IMAGE_STYLE'].'/' , 'VV'=>$_SESSION['NOTE_IMAGE_STYLE'].'/' , 'ABS'=>'' , 'NN'=>'' , 'DISP'=>'' , 'REQ'=>'' );
	$DB_TAB = DB_STRUCTURE_lister_saisies_devoir($devoir_id,$with_REQ=true);
	foreach($DB_TAB as $DB_ROW)
	{
		// Test pour éviter les pbs des élèves changés de groupes ou des items modifiés en cours de route
		if(isset($tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']]))
		{
			$tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']] = str_replace('>-<','><img alt="'.$DB_ROW['saisie_note'].'" src="./_img/note/'.$tab_dossier[$DB_ROW['saisie_note']].$DB_ROW['saisie_note'].'.gif" /><',$tab_affich[$DB_ROW['item_id']][$DB_ROW['eleve_id']]);
			$csv_lignes_scores[$DB_ROW['item_id']][$DB_ROW['eleve_id']] = $DB_ROW['saisie_note'];
		}
	}
	// affichage
	foreach($tab_affich as $comp_id => $tab_user)
	{
		if(!$comp_id)
		{
			echo'<thead>';
		}
		echo'<tr>';
		foreach($tab_user as $user_id => $val)
		{
			echo $val;
		}
		echo'</tr>';
		if(!$comp_id)
		{
			echo'</thead><tbody>';
		}
	}
	echo'</tbody>';
	// assemblage du csv
	$tab_conversion = array( ''=>' ' , 'RR'=>'1' , 'R'=>'2' , 'V'=>'3' , 'VV'=>'4' , 'ABS'=>'A' , 'NN'=>'N' , 'DISP'=>'D' , 'REQ'=>'?' );
	foreach($tab_comp_id as $comp_id=>$val_comp)
	{
		$export_csv .= $csv_lignes_scores[$comp_id][0].$separateur;
		foreach($tab_user_id as $user_id=>$val_user)
		{
			$export_csv .= $tab_conversion[$csv_lignes_scores[$comp_id][$user_id]].$separateur;
		}
		$export_csv .= $csv_colonne_texte[$comp_id]."\r\n";
	}
	$export_csv .= $csv_ligne_eleve_nom."\r\n\r\n";
	// Enregistrer le csv
	$export_csv .= str_replace(':::',"\r\n",$descriptif)."\r\n\r\n";
	$export_csv .= 'CODAGES AUTORISÉS : 1 2 3 4 A N D'."\r\n";
	$fnom = 'saisie_'.$_SESSION['BASE'].'_'.$_SESSION['USER_ID'].'_'.$ref;
	$zip = new ZipArchive();
	if ($zip->open($dossier_export.$fnom.'.zip', ZIPARCHIVE::CREATE)===TRUE)
	{
		$zip->addFromString($fnom.'.csv',csv($export_csv));
		$zip->close();
	}
	//
	// pdf contenant un tableau de saisie vide ; on a besoin de tourner du texte à 90°
	//
	require('./_fpdf/fpdf.php');
	require('./_fpdf/rpdf.php');
	require('./_inc/class.PDF.php');
	$sacoche_pdf = new PDF($orientation='landscape',$marge_min=10,$couleur='non');
	$sacoche_pdf->tableau_saisie_initialiser($eleve_nb,$item_nb);
	// 1ère ligne : référence devoir, noms élèves
	$sacoche_pdf->tableau_saisie_reference_devoir($descriptif);
	foreach($DB_TAB_USER as $DB_ROW)
	{
		$sacoche_pdf->tableau_saisie_reference_eleve($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']);
	}
	// ligne suivantes : référence item, cases vides
	$sacoche_pdf->SetXY($sacoche_pdf->marge_gauche , $sacoche_pdf->marge_haut+$sacoche_pdf->etiquette_hauteur);
	foreach($DB_TAB_COMP as $DB_ROW)
	{
		$item_ref = $DB_ROW['item_ref'];
		$texte_socle = ($DB_ROW['entree_id']) ? ' [S]' : ' [–]';
		$sacoche_pdf->tableau_saisie_reference_item($item_ref.$texte_socle,$DB_ROW['item_nom']);
		for($i=0 ; $i<$eleve_nb ; $i++)
		{
			$sacoche_pdf->Cell($sacoche_pdf->cases_largeur , $sacoche_pdf->cases_hauteur , '' , 1 , 0 , 'C' , false , '');
		}
		$sacoche_pdf->SetXY($sacoche_pdf->marge_gauche , $sacoche_pdf->GetY()+$sacoche_pdf->cases_hauteur);
	}
	$sacoche_pdf->Output($dossier_export.$fnom.'_sans_notes.pdf','F');
	//
	// pdf contenant un tableau de saisie plein ; on a besoin de tourner du texte à 90°
	//
	$sacoche_pdf = new PDF($orientation='landscape',$marge_min=10,$couleur='oui');
	$sacoche_pdf->tableau_saisie_initialiser($eleve_nb,$item_nb);
	// 1ère ligne : référence devoir, noms élèves
	$sacoche_pdf->tableau_saisie_reference_devoir($descriptif);
	foreach($DB_TAB_USER as $DB_ROW)
	{
		$sacoche_pdf->tableau_saisie_reference_eleve($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']);
	}
	// ligne suivantes : référence item, cases vides
	$sacoche_pdf->SetXY($sacoche_pdf->marge_gauche , $sacoche_pdf->marge_haut+$sacoche_pdf->etiquette_hauteur);
	foreach($DB_TAB_COMP as $DB_ROW_COMP)
	{
		$item_ref = $DB_ROW_COMP['item_ref'];
		$texte_socle = ($DB_ROW_COMP['entree_id']) ? ' [S]' : ' [–]';
		$sacoche_pdf->tableau_saisie_reference_item($item_ref.$texte_socle,$DB_ROW_COMP['item_nom']);
		foreach($DB_TAB_USER as $DB_ROW_USER)
		{
			
			$sacoche_pdf->afficher_note_lomer( $csv_lignes_scores[$DB_ROW_COMP['item_id']][$DB_ROW_USER['user_id']] );
			$sacoche_pdf->Cell($sacoche_pdf->cases_largeur , $sacoche_pdf->cases_hauteur , '' , 1 , 0 , 'C' , false , '');
		}
		$sacoche_pdf->SetXY($sacoche_pdf->marge_gauche , $sacoche_pdf->GetY()+$sacoche_pdf->cases_hauteur);
	}
	$sacoche_pdf->Output($dossier_export.$fnom.'_avec_notes.pdf','F');
	exit();
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Mettre à jour l'ordre des items d'une évaluation
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else if( ($action=='Enregistrer_ordre') && $devoir_id && count($tab_id) )
{
	DB_STRUCTURE_modifier_ordre_item($devoir_id,$tab_id);
	exit('<ok>');
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Mettre à jour les items acquis par les élèves à une évaluation
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else if( ($action=='Enregistrer_saisie') && $devoir_id && $date )
{
	// Tout est transmis : il faut comparer avec le contenu de la base pour ne mettre à jour que ce dont il y a besoin
	// On récupère les données transmises dans $tab_post
	$tab_post = array();
	foreach($_POST as $key => $note)
	{
		$tab_key = explode('x',$key);
		if(count($tab_key)==2)
		{
			$item_id = clean_entier($tab_key[0]);
			$eleve_id = clean_entier($tab_key[1]);
			if( $item_id && $eleve_id )
			{
				$tab_post[$item_id.'x'.$eleve_id] = $note;
			}
		}
	}
	// On recupère le contenu de la base déjà enregistré pour le comparer ; on remplit au fur et à mesure $tab_nouveau_modifier / $tab_nouveau_supprimer
	// $tab_demande_supprimer sert à supprimer des demandes d'élèves dont on met une note.
	$tab_nouveau_modifier = array();
	$tab_nouveau_supprimer = array();
	$tab_demande_supprimer = array();
	$DB_TAB = DB_STRUCTURE_lister_saisies_devoir($devoir_id,$with_REQ=true);
	foreach($DB_TAB as $DB_ROW)
	{
		$key = $DB_ROW['item_id'].'x'.$DB_ROW['eleve_id'];
		if($tab_post[$key]!=$DB_ROW['saisie_note'])
		{
			if($tab_post[$key]=='X')
			{
				// valeur de la base à supprimer
				$tab_nouveau_supprimer[$key] = $key;
			}
			else
			{
				// valeur de la base à modifier
				$tab_nouveau_modifier[$key] = $tab_post[$key];
				if($DB_ROW['saisie_note']=='REQ')
				{
					// demande d'évaluation à supprimer
					$tab_demande_supprimer[$key] = $key;
				}
			}
		}
		unset($tab_post[$key]);
	}
	// Il reste dans $tab_post les données à ajouter (mises dans $tab_nouveau_ajouter) et les données vides qui ne servent pas (non enregistrées et non saisies)
	function nonvide($note) {return (($note!='X')&&($note!='REQ')) ? true : false;}
	$tab_nouveau_ajouter = array_filter($tab_post,'nonvide');
	// Il n'y a plus qu'à mettre à jour la base
	if( !count($tab_nouveau_ajouter) && !count($tab_nouveau_modifier) && !count($tab_nouveau_supprimer) )
	{
		exit('Aucune modification détectée !');
	}
	// L'information associée à la note comporte le nom de l'évaluation + celui du professeur (c'est une information statique, conservée sur plusieurs années)
	$info = $info.' ('.$_SESSION['USER_NOM'].' '.$_SESSION['USER_PRENOM']{0}.'.)';
	foreach($tab_nouveau_ajouter as $key => $note)
	{
		list($item_id,$eleve_id) = explode('x',$key);
		DB_STRUCTURE_ajouter_saisie($_SESSION['USER_ID'],$eleve_id,$devoir_id,$item_id,$date,$note,$info);
	}
	foreach($tab_nouveau_modifier as $key => $note)
	{
		list($item_id,$eleve_id) = explode('x',$key);
		DB_STRUCTURE_modifier_saisie($eleve_id,$devoir_id,$item_id,$note,$info);
	}
	foreach($tab_nouveau_supprimer as $key => $key)
	{
		list($item_id,$eleve_id) = explode('x',$key);
		DB_STRUCTURE_supprimer_saisie($eleve_id,$devoir_id,$item_id);
	}
	foreach($tab_demande_supprimer as $key => $key)
	{
		list($item_id,$eleve_id) = explode('x',$key);
		DB_STRUCTURE_supprimer_demande($eleve_id,$item_id);
	}
	exit('<ok>');
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Imprimer un cartouche d'une évaluation
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else if( ($action=='Imprimer_cartouche') && $devoir_id && $groupe_id && $date && $contenu && $detail && $orientation && $marge_min && $couleur )
{
	// liste des items
	$DB_TAB_COMP = DB_STRUCTURE_lister_items_devoir($devoir_id);
	// liste des élèves
	$DB_TAB_USER = DB_STRUCTURE_lister_eleves_actifs_regroupement($groupe_type,$groupe_id);
	// Let's go
	if(!count($DB_TAB_COMP))
	{
		exit('Aucun item n\'est associé à cette évaluation !');
	}
	if(!count($DB_TAB_USER))
	{
		exit('Aucun élève n\'est associé à cette évaluation !');
	}
	$tab_result  = array(); // tableau bi-dimensionnel [n°ligne=id_item][n°colonne=id_user]
	$tab_user_id = array(); // pas indispensable, mais plus lisible
	$tab_comp_id = array(); // pas indispensable, mais plus lisible
	// enregistrer noms prénoms des élèves
	foreach($DB_TAB_USER as $DB_ROW)
	{
		$tab_user_id[$DB_ROW['user_id']] = (substr($contenu,0,8)=='AVEC_nom') ? html($DB_ROW['user_prenom'].' '.$DB_ROW['user_nom']) : '' ;
	}
	// enregistrer refs noms items
	foreach($DB_TAB_COMP as $DB_ROW)
	{
		$item_ref = $DB_ROW['item_ref'];
		$texte_socle = ($DB_ROW['entree_id']) ? '[S] ' : '[–] ';
		$tab_comp_id[$DB_ROW['item_id']] = array($item_ref,$texte_socle.$DB_ROW['item_nom']);
	}
	// résultats vierges
	foreach($tab_user_id as $user_id=>$val_user)
	{
		foreach($tab_comp_id as $comp_id=>$val_comp)
		{
			$tab_result[$comp_id][$user_id] = '-';
		}
	}
	// compléter avec les résultats
	if(strpos($contenu,'AVEC_result')!==false)
	{
		$DB_TAB = DB_STRUCTURE_lister_saisies_devoir($devoir_id,$with_REQ=false);
		foreach($DB_TAB as $DB_ROW)
		{
			// Test pour éviter les pbs des élèves changés de groupes ou des items modifiés en cours de route
			if(isset($tab_result[$DB_ROW['item_id']][$DB_ROW['eleve_id']]))
			{
				$tab_result[$DB_ROW['item_id']][$DB_ROW['eleve_id']] = $DB_ROW['saisie_note'];
			}
		}
	}
	// On attaque l'élaboration des sorties HTML, CSV et PDF
	$fnom = 'cartouche_'.$_SESSION['BASE'].'_'.$devoir_id.'_'.time();
	$sacoche_htm = '<hr /><a class="lien_ext" href="'.$dossier_export.$fnom.'.pdf">Récupérez les cartouches de cette évaluation dans un fichier pdf (à imprimer).</a><br />';
	$sacoche_htm.= '<a class="lien_ext" href="'.$dossier_export.$fnom.'.zip">Récupérez les cartouches de cette évaluation dans un fichier csv tabulé pour tableur.</a><p />';
	$sacoche_csv = '';
	// Appel de la classe et définition de qqs variables supplémentaires pour la mise en page PDF
	$item_nb = count($tab_comp_id);
	$colspan = ($detail=='minimal') ? $item_nb : 3 ;
	require('./_fpdf/fpdf.php');
	require('./_fpdf/rpdf.php');
	require('./_inc/class.PDF.php');
	$sacoche_pdf = new PDF($orientation,$marge_min,$couleur);
	$sacoche_pdf->cartouche_initialiser($detail,$item_nb);
	if($detail=='minimal')
	{
		// dans le cas d'un cartouche minimal
		foreach($tab_user_id as $user_id=>$val_user)
		{
			$texte_entete = $date.' - '.$info.' - '.$val_user;
			$sacoche_htm .= '<table class="bilan"><thead><tr><th colspan="'.$colspan.'">'.html($texte_entete).'</th></tr></thead><tbody>';
			$sacoche_csv .= $texte_entete."\r\n";
			$sacoche_pdf->cartouche_entete($texte_entete);
			$ligne1_csv = ''; $ligne1_html = '';
			$ligne2_csv = ''; $ligne2_html = '';
			foreach($tab_comp_id as $comp_id=>$tab_val_comp)
			{
				$ligne1_html .= '<td>'.html($tab_val_comp[0]).'</td>';
				$ligne2_html .= '<td class="hc">'.affich_note_html($tab_result[$comp_id][$user_id],$date,$info,false).'</td>';
				$ligne1_csv .= $tab_val_comp[0]."\t";
				$ligne2_csv .= $tab_result[$comp_id][$user_id]."\t";
				$sacoche_pdf->cartouche_minimal_competence($tab_val_comp[0] , $tab_result[$comp_id][$user_id]);
			}
			$sacoche_htm .= '<tr>'.$ligne1_html.'</tr><tr>'.$ligne2_html.'</tr></tbody></table><p />';
			$sacoche_csv .= $ligne1_csv."\r\n".$ligne2_csv."\r\n\r\n";
			$sacoche_pdf->cartouche_interligne(4);
		}
	}
	elseif($detail=='complet')
	{
		// dans le cas d'un cartouche complet
		foreach($tab_user_id as $user_id=>$val_user)
		{
			$texte_entete = $date.' - '.$info.' - '.$val_user;
			$sacoche_htm .= '<table class="bilan"><thead><tr><th colspan="'.$colspan.'">'.html($texte_entete).'</th></tr></thead><tbody>';
			$sacoche_csv .= $texte_entete."\r\n";
			$sacoche_pdf->cartouche_entete($texte_entete);
			foreach($tab_comp_id as $comp_id=>$tab_val_comp)
			{
				$sacoche_htm .= '<tr><td>'.html($tab_val_comp[0]).'</td><td>'.html($tab_val_comp[1]).'</td><td>'.affich_note_html($tab_result[$comp_id][$user_id],$date,$info,false).'</td></tr>';
				$sacoche_csv .= $tab_val_comp[0]."\t".$tab_val_comp[1]."\t".$tab_result[$comp_id][$user_id]."\r\n";
				$sacoche_pdf->cartouche_complet_competence($tab_val_comp[0] , $tab_val_comp[1] , $tab_result[$comp_id][$user_id]);
			}
			$sacoche_htm .= '</tbody></table><p />';
			$sacoche_csv .= "\r\n";
			$sacoche_pdf->cartouche_interligne(2);
		}
	}
	// On archive le cartouche dans un fichier tableur zippé (csv tabulé)
	$zip = new ZipArchive();
	if ($zip->open($dossier_export.$fnom.'.zip', ZIPARCHIVE::CREATE)===TRUE)
	{
		$zip->addFromString($fnom.'.csv',csv($sacoche_csv));
		$zip->close();
	}
	// On archive le cartouche dans un fichier pdf
	$sacoche_pdf->Output($dossier_export.$fnom.'.pdf','F');
	// Affichage
	exit($sacoche_htm);
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Traiter une demande d'importation d'une saisie déportée ; on n'enregistre rien, on ne fait que le décrypter pour que javascript le traite
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else if( (isset($_GET['f_action'])) && ($_GET['f_action']=='importer_saisie_csv') )
{
	// Récupérer le contenu du fichier
	$tab_file = $_FILES['userfile'];
	$fnom_transmis = $tab_file['name'];
	$fnom_serveur = $tab_file['tmp_name'];
	$ftaille = $tab_file['size'];
	$ferreur = $tab_file['error'];
	if( (!file_exists($fnom_serveur)) || (!$ftaille) || ($ferreur) )
	{
		exit('Erreur : erreur avec le fichier transmis (taille dépassant probablement upload_max_filesize ) !');
	}
	$extension = strtolower(pathinfo($fnom_transmis,PATHINFO_EXTENSION));
	if(!in_array($extension,array('txt','csv')))
	{
		exit('Erreur : l\'extension du fichier transmis est incorrecte !');
	}
	$contenu_csv = file_get_contents($fnom_serveur);
	$contenu_csv = utf8($contenu_csv); // Mettre en UTF-8 si besoin
	$tab_lignes = extraire_lignes($contenu_csv); // Extraire les lignes du fichier
	$separateur = extraire_separateur_csv($tab_lignes[0]); // Déterminer la nature du séparateur
	// Pas de ligne d'en-tête à supprimer
	// Mémoriser les eleve_id de la 1ère ligne
	$tab_eleve = array();
	$tab_elements = explode($separateur,$tab_lignes[0]);
	unset($tab_elements[0]);
	foreach ($tab_elements as $num_colonne => $element_contenu)
	{
		$eleve_id = clean_entier($element_contenu);
		if($eleve_id)
		{
			$tab_eleve[$num_colonne] = $eleve_id ;
		}
	}
	// Parcourir les lignes suivantes et mémoriser les scores
	$retour = '|';
	unset($tab_lignes[0]);
	$scores_autorises = '1234AaNnDd';
	foreach ($tab_lignes as $ligne_contenu)
	{
		$tab_elements = explode($separateur,$ligne_contenu);
		$item_id = clean_entier($tab_elements[0]);
		if($item_id)
		{
			foreach ($tab_eleve as $num_colonne => $eleve_id)
			{
				if( (isset($tab_elements[$num_colonne])) && ($tab_elements[$num_colonne]!='') )
				{
					$score = $tab_elements[$num_colonne];
					if(strpos($scores_autorises,$score)!==false)
					{
						$retour .= $eleve_id.'.'.$item_id.'.'.strtoupper($score).'|';
					}
				}
			}
		}
	}
	exit($retour);
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	On ne devrait pas en arriver là !
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

else
{
	echo'Erreur avec les données transmises !';
}
?>