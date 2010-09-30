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
$TITRE = "Évaluer des élèves sélectionnés";
$VERSION_JS_FILE += 7;
?>

<?php
// Dates par défaut de début et de fin
$annee_debut = (date('n')>8) ? date('Y') : date('Y')-1 ;
$date_debut  = '01/09/'.$annee_debut;
$date_fin    = date("d/m/Y");
?>

<ul class="puce">
	<li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_professeur__evaluations_gestion">DOC : Gestion des évaluations.</a></span></li>
	<li><span class="astuce">Choisir des evaluations existantes à afficher, ou cliquer sur le "<span style="background:transparent url(./_img/sprite9.png) 0 0 no-repeat;background-position:-20px 0;width:16px;height:16px;display:inline-block;vertical-align:middle"></span>" pour créer une nouvelle évaluation.</span></li>
	<li><span class="danger">Une évaluation dont la saisie a commencé ne devrait pas voir ses élèves ou ses items modifiés (sinon vous n'aurez plus accès à certaines données) !</span></li>
</ul>

<hr />

<form action="" id="form0"><fieldset>
	<label class="tab" for="f_aff_periode">Période :</label>
		du <input id="f_date_debut" name="f_date_debut" size="9" type="text" value="<?php echo $date_debut ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
		au <input id="f_date_fin" name="f_date_fin" size="9" type="text" value="<?php echo $date_fin ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
	<br />
	<span class="tab"></span><input type="hidden" name="f_action" value="Afficher_evaluations" /><button id="actualiser" type="submit"><img alt="" src="./_img/bouton/actualiser.png" /> Actualiser l'affichage.</button><label id="ajax_msg0">&nbsp;</label>
</fieldset></form>

<form action="" id="form1">
	<hr />
	<table class="form">
		<thead>
			<tr>
				<th>Date</th>
				<th>Élèves</th>
				<th>Description</th>
				<th>Items</th>
				<th class="nu"><q class="ajouter" title="Ajouter une évaluation."></q></th>
			</tr>
		</thead>
		<tbody>
			<tr><td class="nu" colspan="5"></td></tr>
		</tbody>
	</table>
</form>

<script type="text/javascript">var input_date="<?php echo date("d/m/Y") ?>";</script>

<form action="" id="zone_compet" class="hide">
	<p>
		<span class="tab"></span><button id="valider_compet" type="button"><img alt="" src="./_img/bouton/valider.png" /> Valider ce choix</button>&nbsp;&nbsp;&nbsp;<button id="annuler_compet" type="button"><img alt="" src="./_img/bouton/annuler.png" /> Annuler / Retour</button>
	</p>
	<?php
	// Affichage de la liste des items pour toutes les matières d'un professeur, sur tous les niveaux
	$DB_TAB = DB_STRUCTURE_recuperer_arborescence($_SESSION['USER_ID'],$matiere_id=0,$niveau_id=0,$only_socle=false,$only_item=false,$socle_nom=false);
	echo afficher_arborescence_matiere_from_SQL($DB_TAB,$dynamique=true,$reference=true,$aff_coef=false,$aff_cart=false,$aff_socle='texte',$aff_lien=false,$aff_input=true);
	?>
</form>

<form action="" id="zone_eleve" class="hide">
	<p>
		<span class="tab"></span><button id="valider_eleve" type="button"><img alt="" src="./_img/bouton/valider.png" /> Valider ce choix</button>&nbsp;&nbsp;&nbsp;<button id="annuler_eleve" type="button"><img alt="" src="./_img/bouton/annuler.png" /> Annuler / Retour</button>
	</p>
	<?php
	$tab_regroupements = array();
	$tab_id = array('classe'=>'','groupe'=>'');
	// Recherche de la liste des classes et des groupes du professeur
	$DB_TAB = DB_STRUCTURE_lister_classes_groupes_professeur($_SESSION['USER_ID']);
	foreach($DB_TAB as $DB_ROW)
	{
		$tab_regroupements[$DB_ROW['groupe_id']] = array('nom'=>$DB_ROW['groupe_nom'],'eleve'=>array());
		$tab_id[$DB_ROW['groupe_type']][] = $DB_ROW['groupe_id'];
	}
	// Recherche de la liste des élèves pour chaque classe du professeur
	if(is_array($tab_id['classe']))
	{
		$listing = implode(',',$tab_id['classe']);
		$DB_TAB = DB_STRUCTURE_lister_eleves_classes($listing);
		foreach($DB_TAB as $DB_ROW)
		{
			$tab_regroupements[$DB_ROW['eleve_classe_id']]['eleve'][$DB_ROW['user_id']] = $DB_ROW['user_nom'].' '.$DB_ROW['user_prenom'].' ('.$DB_ROW['user_login'].')';
		}
	}
	// Recherche de la liste des élèves pour chaque groupe du professeur
	if(is_array($tab_id['groupe']))
	{
		$listing = implode(',',$tab_id['groupe']);
		$DB_TAB = DB_STRUCTURE_lister_eleves_groupes($listing);
		foreach($DB_TAB as $DB_ROW)
		{
			$tab_regroupements[$DB_ROW['groupe_id']]['eleve'][$DB_ROW['user_id']] = $DB_ROW['user_nom'].' '.$DB_ROW['user_prenom'].' ('.$DB_ROW['user_login'].')';
		}
	}
	// Affichage de la liste des élèves (du professeur) pour chaque classe et groupe
	foreach($tab_regroupements as $groupe_id => $tab_groupe)
	{
		echo'<ul class="ul_m1">'."\r\n";
		echo'	<li class="li_m1"><span>'.html($tab_groupe['nom']).'</span>'."\r\n";
		echo'		<ul class="ul_n3">'."\r\n";
		foreach($tab_groupe['eleve'] as $eleve_id => $eleve_nom)
		{
			// C'est plus compliqué que pour les items car un élève peut appartenir à une classe et plusieurs groupes => id du groupe mélé à l'id et id de l'élève dans l'attribut "lang"
			echo'			<li class="li_n3"><input id="id_'.$eleve_id.'_'.$groupe_id.'" lang="'.$eleve_id.'" name="f_eleves[]" type="checkbox" value="'.$eleve_id.'" /><label for="id_'.$eleve_id.'_'.$groupe_id.'"> '.html($eleve_nom).'</label></li>'."\r\n";
		}
		echo'		</ul>'."\r\n";
		echo'	</li>'."\r\n";
		echo'</ul>'."\r\n";
	}
	?>
</form>

<form action="" id="zone_ordonner" class="hide">
	<p class="hc"><b id="titre_ordonner"></b><br /><label id="msg_ordonner"></label></p>
	<div id="div_ordonner">
	</div>
</form>

<!-- Sans "javascript:return false" une soumission incontrôlée s'effectue quand on presse "entrée" dans le cas d'un seul élève évalué sur un seul item. -->
<form action="javascript:return false" id="zone_saisir" class="hide">
	<p class="hc"><b id="titre_saisir"></b><br /><label id="msg_saisir"></label></p>
	<table id="table_saisir" class="scor_eval">
		<tbody><tr><td></td></tr></tbody>
	</table>
	<div id="td_souris_container"><div class="td_souris">
		<img alt="RR" src="./_img/note/<?php echo $_SESSION['NOTE_IMAGE_STYLE'] ?>/RR.gif" /><img alt="ABS" src="./_img/note/ABS.gif" /><br />
		<img alt="R" src="./_img/note/<?php echo $_SESSION['NOTE_IMAGE_STYLE'] ?>/R.gif" /><img alt="NN" src="./_img/note/NN.gif" /><br />
		<img alt="V" src="./_img/note/<?php echo $_SESSION['NOTE_IMAGE_STYLE'] ?>/V.gif" /><img alt="DISP" src="./_img/note/DISP.gif" /><br />
		<img alt="VV" src="./_img/note/<?php echo $_SESSION['NOTE_IMAGE_STYLE'] ?>/VV.gif" /><img alt="X" src="./_img/note/X.gif" />
	</div></div>
	<p />
	<div>
		<a lang="zone_saisir_deport" href="#td_souris_container"><img src="./_img/toggle_plus.gif" alt="" title="Voir / masquer la saisie déportée." class="toggle" /></a> Saisie déportée
		<div id="zone_saisir_deport" class="hide">
			<input type="hidden" name="filename" id="filename" value="<?php echo './__tmp/export/saisie_'.$_SESSION['BASE'].'_'.$_SESSION['USER_ID'].'_'; ?>" />
			<span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_professeur__evaluations_saisie_deportee">DOC : Saisie déportée.</a></span>
			<ul class="puce">
				<li><a id="export_file1" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Récupérer un fichier vierge au format CSV pour une saisie déportée.</a></li>
				<li><a id="export_file4" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Récupérer au format PDF un tableau vierge utilisable pour un report manuel des notes.</a></li>
				<li><button id="import_file" type="button"><img alt="" src="./_img/bouton/fichier_import.png" /> Envoyer un fichier complété au format CSV.</button><label id="msg_import">&nbsp;</label></li>
			</ul>
		</div>
	</div>
</form>

<div id="zone_voir" class="hide">
	<p class="hc"><b id="titre_voir"></b><br /><label id="msg_voir"></label></p>
	<table id="table_voir" class="scor_eval">
		<tbody><tr><td></td></tr></tbody>
	</table>
	<p>
		<a lang="zone_voir_deport" href="#td_souris_container"><img src="./_img/toggle_plus.gif" alt="" title="Voir / masquer la saisie déportée." class="toggle" /></a> Saisie déportée
		<div id="zone_voir_deport" class="hide">
			<span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_professeur__evaluations_saisie_deportee">DOC : Saisie déportée.</a></span>
			<ul class="puce">
				<li><a id="export_file2" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Récupérer un fichier des scores au format CSV pour archivage ou une saisie déportée.</a></li>
				<li><a id="export_file3" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Récupérer au format PDF un tableau vierge utilisable pour un report manuel des notes.</a></li>
			</ul>
		</div>
	</p>
</div>

<?php
// Fabrication des éléments select du formulaire
$tab_cookie = load_cookie_select($_SESSION['BASE'],$_SESSION['USER_ID']);
$select_orientation = afficher_select($tab_select_orientation , $select_nom='f_orientation' , $option_first='non' , $selection=$tab_cookie['orientation'] , $optgroup='non');
$select_marge_min   = afficher_select($tab_select_marge_min   , $select_nom='f_marge_min'   , $option_first='non' , $selection=$tab_cookie['marge_min']   , $optgroup='non');
$select_couleur     = afficher_select($tab_select_couleur     , $select_nom='f_couleur'     , $option_first='non' , $selection=$tab_cookie['couleur']     , $optgroup='non');
?>

<form action="" id="zone_imprimer" class="hide"><fieldset>
	<p class="hc"><b id="titre_imprimer"></b><br /><button id="fermer_zone_imprimer" type="button"><img alt="" src="./_img/bouton/retourner.png" /> Retour</button></p>
	<label class="tab" for="f_contenu">Remplissage :</label><select id="f_contenu" name="f_contenu"><option value="SANS_nom_SANS_result">cartouche SANS les noms d'élèves et SANS les résultats</option><option value="AVEC_nom_SANS_result">cartouche AVEC les noms d'élèves mais SANS les résultats</option><option value="AVEC_nom_AVEC_result">cartouche AVEC les noms d'élèves et AVEC les résultats (si saisis)</option></select><br />
	<label class="tab" for="f_detail">Détail :</label><select id="f_detail" name="f_detail"><option value="complet">cartouche avec la dénomination complète de chaque item</option><option value="minimal">cartouche minimal avec uniquement les références des items</option></select><br />
	<div class="toggle">
		<span class="tab"></span><a href="#" class="puce_plus toggle">Afficher plus d'options</a>
	</div>
	<div class="toggle hide">
		<span class="tab"></span><a href="#" class="puce_moins toggle">Afficher moins d'options</a><br />
		<label class="tab" for="f_orientation">Orientation :</label><?php echo $select_orientation ?> en <?php echo $select_couleur ?> avec marges minimales de <?php echo $select_marge_min ?><br />
	</div>
	<span class="tab"></span><button id="f_submit_imprimer" type="button" value="'.$ref.'"><img alt="" src="./_img/bouton/valider.png" /> Générer le cartouche</button><label id="msg_imprimer">&nbsp;</label>
	<p id="zone_imprimer_retour"></p>
</fieldset></form>
