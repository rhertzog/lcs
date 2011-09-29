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
$TITRE = "Bilan d'items pluridisciplinaire";
$VERSION_JS_FILE += 8;
?>

<?php
// Fabrication des éléments select du formulaire
$tab_cookie = load_cookie_select('releve_items');
if($_SESSION['USER_PROFIL']=='directeur')
{
	$tab_groupes  = DB_STRUCTURE_OPT_classes_groupes_etabl();
	$of_g = 'oui'; $sel_g = false; $class_form_option = 'show'; $class_form_eleve = 'show'; $class_form_periode = 'hide';
	$select_eleves = '<option></option>'; // maj en ajax suivant le choix du groupe
	$check_option_lien = '';
	$check_bilan_MS        = ' checked';
	$check_bilan_PA        = ' checked';
	$check_conv_sur20      = '';
}
if($_SESSION['USER_PROFIL']=='professeur')
{
	$tab_groupes  = DB_STRUCTURE_OPT_groupes_professeur($_SESSION['USER_ID']);
	$of_g = 'oui'; $sel_g = false; $class_form_option = 'show'; $class_form_eleve = 'show'; $class_form_periode = 'hide';
	$select_eleves = '<option></option>'; // maj en ajax suivant le choix du groupe
	$check_option_lien = '';
	$check_bilan_MS        = ' checked';
	$check_bilan_PA        = ' checked';
	$check_conv_sur20      = '';
}
if( ($_SESSION['USER_PROFIL']=='parent') && ($_SESSION['NB_ENFANTS']!=1) )
{
	$tab_groupes  = $_SESSION['OPT_PARENT_CLASSES']; $GLOBALS['tab_select_optgroup'] = array('classe'=>'Classes');
	$of_g = 'oui'; $sel_g = false; $class_form_option = 'hide'; $class_form_eleve = 'show'; $class_form_periode = 'hide';
	$select_eleves = '<option></option>'; // maj en ajax suivant le choix du groupe
	$check_option_lien     = ' checked';
	$check_bilan_MS        = (mb_substr_count($_SESSION['DROIT_BILAN_MOYENNE_SCORE'],$_SESSION['USER_PROFIL']))      ? ' checked' : '';
	$check_bilan_PA        = (mb_substr_count($_SESSION['DROIT_BILAN_POURCENTAGE_ACQUIS'],$_SESSION['USER_PROFIL'])) ? ' checked' : '';
	$check_conv_sur20      = (mb_substr_count($_SESSION['DROIT_BILAN_NOTE_SUR_VINGT'],$_SESSION['USER_PROFIL']))     ? ' checked' : '';
}
if( ($_SESSION['USER_PROFIL']=='parent') && ($_SESSION['NB_ENFANTS']==1) )
{
	$tab_groupes  = array(0=>array('valeur'=>$_SESSION['ELEVE_CLASSE_ID'],'texte'=>$_SESSION['ELEVE_CLASSE_NOM'],'optgroup'=>'classe')); $GLOBALS['tab_select_optgroup'] = array('classe'=>'Classes');
	$of_g = 'non'; $sel_g = true; $class_form_option = 'hide'; $class_form_eleve = 'hide'; $class_form_periode = 'show';
	$select_eleves = '<option value="'.$_SESSION['OPT_PARENT_ENFANTS'][0]['valeur'].'" selected>'.html($_SESSION['OPT_PARENT_ENFANTS'][0]['texte']).'</option>';
	$check_option_lien     = ' checked';
	$check_bilan_MS        = (mb_substr_count($_SESSION['DROIT_BILAN_MOYENNE_SCORE'],$_SESSION['USER_PROFIL']))      ? ' checked' : '';
	$check_bilan_PA        = (mb_substr_count($_SESSION['DROIT_BILAN_POURCENTAGE_ACQUIS'],$_SESSION['USER_PROFIL'])) ? ' checked' : '';
	$check_conv_sur20      = (mb_substr_count($_SESSION['DROIT_BILAN_NOTE_SUR_VINGT'],$_SESSION['USER_PROFIL']))     ? ' checked' : '';
}
if($_SESSION['USER_PROFIL']=='eleve')
{
	$tab_groupes = array(0=>array('valeur'=>$_SESSION['ELEVE_CLASSE_ID'],'texte'=>$_SESSION['ELEVE_CLASSE_NOM'],'optgroup'=>'classe')); $GLOBALS['tab_select_optgroup'] = array('classe'=>'Classes');
	$of_g = 'non'; $sel_g = true; $class_form_option = 'hide'; $class_form_eleve = 'hide'; $class_form_periode = 'show';
	$select_eleves = '<option value="'.$_SESSION['USER_ID'].'" selected>'.html($_SESSION['USER_NOM'].' '.$_SESSION['USER_PRENOM']).'</option>';
	$check_option_lien = ' checked';
	$check_bilan_MS        = (mb_substr_count($_SESSION['DROIT_BILAN_MOYENNE_SCORE'],$_SESSION['USER_PROFIL']))      ? ' checked' : '';
	$check_bilan_PA        = (mb_substr_count($_SESSION['DROIT_BILAN_POURCENTAGE_ACQUIS'],$_SESSION['USER_PROFIL'])) ? ' checked' : '';
	$check_conv_sur20      = (mb_substr_count($_SESSION['DROIT_BILAN_NOTE_SUR_VINGT'],$_SESSION['USER_PROFIL']))     ? ' checked' : '';
}
$tab_periodes          = DB_STRUCTURE_OPT_periodes_etabl();

$select_groupe      = afficher_select($tab_groupes            , $select_nom='f_groupe'      , $option_first=$of_g , $selection=$sel_g                       , $optgroup='oui'); // optgroup à oui y compris pour les élèves (formulaire invisible) car recherche du type de groupe dans le js
$select_periode     = afficher_select($tab_periodes           , $select_nom='f_periode'     , $option_first='val' , $selection=false                        , $optgroup='non');
$select_orientation = afficher_select($tab_select_orientation , $select_nom='f_orientation' , $option_first='non' , $selection=$tab_cookie['orientation']   , $optgroup='non');
$select_marge_min   = afficher_select($tab_select_marge_min   , $select_nom='f_marge_min'   , $option_first='non' , $selection=$tab_cookie['marge_min']     , $optgroup='non');
$select_pages_nb    = afficher_select($tab_select_pages_nb    , $select_nom='f_pages_nb'    , $option_first='non' , $selection=false                        , $optgroup='non');
$select_couleur     = afficher_select($tab_select_couleur     , $select_nom='f_couleur'     , $option_first='non' , $selection=$tab_cookie['couleur']       , $optgroup='non');
$select_legende     = afficher_select($tab_select_legende     , $select_nom='f_legende'     , $option_first='non' , $selection=$tab_cookie['legende']       , $optgroup='non');
$select_cases_nb    = afficher_select($tab_select_cases_nb    , $select_nom='f_cases_nb'    , $option_first='non' , $selection=$tab_cookie['cases_nb']      , $optgroup='non');
$select_cases_larg  = afficher_select($tab_select_cases_size  , $select_nom='f_cases_larg'  , $option_first='non' , $selection=$tab_cookie['cases_largeur'] , $optgroup='non');
// Dates par défaut de début et de fin
$annee_debut = (date('n')>8) ? date('Y') : date('Y')-1 ;
$date_debut = '01/09/'.$annee_debut;
$date_fin   = date("d/m/Y");

// Fabrication du tableau javascript "tab_groupe_periode" pour les jointures groupes/périodes
$tab_groupe_periode_js = 'var tab_groupe_periode = new Array();';
if(is_array($tab_groupes))
{
	$tab_id_classe_groupe = array();
	foreach($tab_groupes as $tab_groupe_infos)
	{
		$tab_id_classe_groupe[] = $tab_groupe_infos['valeur'];
	}
	$tab_memo_groupes = array();
	$DB_TAB = DB_STRUCTURE_lister_jointure_groupe_periode($listing_groupe_id = implode(',',$tab_id_classe_groupe));
	foreach($DB_TAB as $DB_ROW)
	{
		if(!isset($tab_memo_groupes[$DB_ROW['groupe_id']]))
		{
			$tab_memo_groupes[$DB_ROW['groupe_id']] = true;
			$tab_groupe_periode_js .= 'tab_groupe_periode['.$DB_ROW['groupe_id'].'] = new Array();';
		}
		$tab_groupe_periode_js .= 'tab_groupe_periode['.$DB_ROW['groupe_id'].']['.$DB_ROW['periode_id'].']="'.$DB_ROW['jointure_date_debut'].'_'.$DB_ROW['jointure_date_fin'].'";';
	}
}
?>

<script type="text/javascript">
	var date_mysql="<?php echo date("Y-m-d") ?>";
	<?php echo $tab_groupe_periode_js ?> 
</script>

<div><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=releves_bilans__releve_items_multimatiere">DOC : Bilan d'items pluridisciplinaire.</a></span></div>
<div class="astuce">Un administrateur doit effectuer certains réglages préliminaires (<a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_administrateur__gestion_releves_bilans">DOC</a>).</div>

<hr />

<form action="" method="post" id="form_select"><fieldset>
	<p class="<?php echo $class_form_option ?>">
		<label class="tab" for="f_opt_bilan">Opt. relevé <img alt="" src="./_img/bulle_aide.png" title="Deux lignes de synthèse peuvent être ajoutées.<br />Dans ce cas, une note sur 20 peut aussi être affichée." /> :</label><label for="f_bilan_MS"><input type="checkbox" id="f_bilan_MS" name="f_bilan_MS" value="1"<?php echo $check_bilan_MS ?> /> Moyenne des scores</label>&nbsp;&nbsp;&nbsp;<label for="f_bilan_PA"><input type="checkbox" id="f_bilan_PA" name="f_bilan_PA" value="1"<?php echo $check_bilan_PA ?> /> Pourcentage d'items acquis</label>&nbsp;&nbsp;&nbsp;<label for="f_conv_sur20"><input type="checkbox" id="f_conv_sur20" name="f_conv_sur20" value="1"<?php echo $check_conv_sur20 ?> /> Proposition de note sur 20</label>
	</p>
	<p class="<?php echo $class_form_eleve ?>">
		<label class="tab" for="f_groupe">Classe / groupe :</label><?php echo $select_groupe ?><input type="hidden" id="f_groupe_nom" name="f_groupe_nom" value="" /><label id="ajax_maj">&nbsp;</label><br />
		<label class="tab" for="f_eleve"><img alt="" src="./_img/bulle_aide.png" title="Utiliser la touche &laquo&nbsp;Shift&nbsp;&raquo; pour une sélection multiple contiguë.<br />Utiliser la touche &laquo&nbsp;Ctrl&nbsp;&raquo; pour une sélection multiple non contiguë." /> Élève(s) :</label><select id="f_eleve" name="f_eleve[]" multiple size="9"><?php echo $select_eleves ?></select><input type="hidden" id="eleves" name="eleves" value="" />
	</p>
	<p id="zone_periodes" class="<?php echo $class_form_periode ?>">
		<label class="tab" for="f_periode"><img alt="" src="./_img/bulle_aide.png" title="Les items pris en compte sont ceux qui sont évalués<br />au moins une fois sur cette période." /> Période :</label><?php echo $select_periode ?>
		<span id="dates_perso" class="show">
			du <input id="f_date_debut" name="f_date_debut" size="9" type="text" value="<?php echo $date_debut ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
			au <input id="f_date_fin" name="f_date_fin" size="9" type="text" value="<?php echo $date_fin ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
		</span><br />
		<span class="radio"><img alt="" src="./_img/bulle_aide.png" title="Le bilan peut être établi uniquement sur la période considérée<br />ou en tenant compte d'évaluations antérieures des items concernés." /> Prise en compte des évaluations antérieures :</span><label for="f_retro_oui"><input type="radio" id="f_retro_oui" name="f_retroactif" value="oui" checked /> oui</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="f_retro_non"><input type="radio" id="f_retro_non" name="f_retroactif" value="non" /> non</label>
	</p>
	<div class="toggle">
		<span class="tab"></span><a href="#" class="puce_plus toggle">Afficher plus d'options</a>
	</div>
	<div class="toggle hide">
		<span class="tab"></span><a href="#" class="puce_moins toggle">Afficher moins d'options</a><br />
		<label class="tab" for="f_restriction">Restriction :</label><input type="checkbox" id="f_restriction" name="f_restriction" value="1" /> <label for="f_restriction">Uniquement les items liés du socle</label><br />
		<label class="tab" for="f_indication">Indications <img alt="" src="./_img/bulle_aide.png" title="Les paramètres des items peuvent être affichés." /> :</label><label for="f_coef"><input type="checkbox" id="f_coef" name="f_coef" value="1" /> Coefficients</label>&nbsp;&nbsp;&nbsp;<label for="f_socle"><input type="checkbox" id="f_socle" name="f_socle" value="1" checked /> Socle</label>&nbsp;&nbsp;&nbsp;<label for="f_lien"><input type="checkbox" id="f_lien" name="f_lien" value="1"<?php echo $check_option_lien ?> /> Liens de remédiation</label><br />
		<label class="tab" for="f_impression"><img alt="" src="./_img/bulle_aide.png" title="Pour le format pdf." /> Impression :</label><?php echo $select_orientation ?> <?php echo $select_couleur ?> <?php echo $select_legende ?> <?php echo $select_marge_min ?> <?php echo $select_pages_nb ?></label><br />
		<label class="tab" for="f_cases_nb">Évaluations :</label><?php echo $select_cases_nb ?> de largeur <?php echo $select_cases_larg ?><p />
	</div>
	<span class="tab"></span><button id="bouton_valider" type="submit"><img alt="" src="./_img/bouton/generer.png" /> Générer.</button><label id="ajax_msg">&nbsp;</label><br />
</fieldset></form>

<hr />

<div id="bilan">
</div>

