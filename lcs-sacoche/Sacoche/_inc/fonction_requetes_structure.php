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

/**
 * DB_STRUCTURE_recuperer_demandes_autorisees_matiere
 *
 * @param int   $matiere_id
 * @return int
 */
function DB_STRUCTURE_recuperer_demandes_autorisees_matiere($matiere_id)
{
	$DB_SQL = 'SELECT matiere_nb_demandes ';
	$DB_SQL.= 'FROM sacoche_matiere ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':matiere_id'=>$matiere_id);
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_recuperer_donnees_utilisateur
 *
 * @param string $mode_connection   'normal' | 'cas' | 'gepi' | ...
 * @param string $login
 * @return array
 */
function DB_STRUCTURE_recuperer_donnees_utilisateur($mode_connection,$login)
{
	switch($mode_connection)
	{
		case 'normal' : $champ = 'user_login';   break;
		case 'cas'    : $champ = 'user_id_ent';  break;
		case 'gepi'   : $champ = 'user_id_gepi'; break;
	}
	$DB_SQL = 'SELECT sacoche_user.*, sacoche_groupe.groupe_nom, ';
	$DB_SQL.= 'UNIX_TIMESTAMP(sacoche_user.user_tentative_date) AS tentative_unix ';
	$DB_SQL.= 'FROM sacoche_user ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe ON sacoche_user.eleve_classe_id=sacoche_groupe.groupe_id ';
	$DB_SQL.= 'WHERE '.$champ.'=:identifiant ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':identifiant'=>$login);
	return DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_recuperer_dates_periode
 *
 * @param int    $groupe_id    id du groupe
 * @param int    $periode_id   id de la période
 * @return array
 */
function DB_STRUCTURE_recuperer_dates_periode($groupe_id,$periode_id)
{
	$DB_SQL = 'SELECT jointure_date_debut, jointure_date_fin ';
	$DB_SQL.= 'FROM sacoche_jointure_groupe_periode ';
	$DB_SQL.= 'WHERE groupe_id=:groupe_id AND periode_id=:periode_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':groupe_id'=>$groupe_id,':periode_id'=>$periode_id);
	return DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_recuperer_amplitude_periodes
 *
 * @param void
 * @return array  de la forme array('tout_debut'=>... , ['toute_fin']=>... , ['nb_jours_total']=>...)
 */
function DB_STRUCTURE_recuperer_amplitude_periodes()
{
	$DB_SQL = 'SELECT MIN(jointure_date_debut) AS tout_debut , MAX(jointure_date_fin) AS toute_fin FROM sacoche_jointure_groupe_periode ';
	$DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	if(count($DB_ROW))
	{
		// On ajoute un jour pour dessiner les barres jusqu'au jour suivant (accessoirement, ça évite aussi une possible division par 0).
		$DB_SQL = 'SELECT DATEDIFF(DATE_ADD(:toute_fin,INTERVAL 1 DAY),:tout_debut) AS nb_jours_total ';
		$DB_VAR = array(':tout_debut'=>$DB_ROW['tout_debut'],':toute_fin'=>$DB_ROW['toute_fin']);
		$DB_ROX = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$DB_ROW['nb_jours_total'] = $DB_ROX['nb_jours_total'];
	}
	return $DB_ROW;
}

/**
 * DB_STRUCTURE_recuperer_niveau_groupes
 *
 * @param string   $listing_groupe_id   id des groupes séparés par des virgules
 * @return array
 */
function DB_STRUCTURE_recuperer_niveau_groupes($listing_groupe_id)
{
	$DB_SQL = 'SELECT groupe_id, niveau_id, niveau_nom ';
	$DB_SQL.= 'FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_id IN('.$listing_groupe_id.') ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_recuperer_arborescence
 * Retourner l'arborescence d'un référentiel (tableau issu de la requête SQL)
 * + pour une matière donnée / pour toutes les matières d'un professeur donné
 * + pour un niveau donné / pour tous les niveaux concernés
 *
 * @param int  $prof_id      passer 0 pour une recherche sur une matière plutôt que sur toutes les matières d'un prof
 * @param int  $matiere_id   passer 0 pour une recherche sur toutes les matières d'un prof plutôt que sur une matière
 * @param int  $niveau_id    passer 0 pour une recherche sur tous les niveaux
 * @param bool $only_socle   "true" pour ne retourner que les items reliés au socle
 * @param bool $only_item    "true" pour ne retourner que les lignes d'items, "false" pour l'arborescence complète, sans forcément descendre jusqu'à l'items (valeurs NULL retournées)
 * @param bool $socle_nom    avec ou pas le nom des items du socle associés
 * @return array
 */
function DB_STRUCTURE_recuperer_arborescence($prof_id,$matiere_id,$niveau_id,$only_socle,$only_item,$socle_nom)
{
	$select_socle_nom  = ($socle_nom)  ? 'entree_id,entree_nom ' : 'entree_id ' ;
	$join_user_matiere = ($prof_id)    ? 'LEFT JOIN sacoche_jointure_user_matiere USING (matiere_id) ' : '' ;
	$join_socle_item   = ($socle_nom)  ? 'LEFT JOIN sacoche_socle_entree USING (entree_id) ' : '' ;
	$where_user        = ($prof_id)    ? 'user_id=:user_id AND (matiere_id IN('.$_SESSION['MATIERES'].') OR matiere_partage=:partage) ' : '' ; // Test matiere car un prof peut être encore relié à des matières décochées par l'admin.
	$where_matiere     = ($matiere_id) ? 'matiere_id=:matiere_id ' : '' ;
	$where_niveau      = ($niveau_id)  ? 'AND niveau_id=:niveau_id ' : 'AND niveau_id IN('.$_SESSION['CYCLES'].','.$_SESSION['NIVEAUX'].') ' ;
	$where_item        = ($only_item)  ? 'AND item_id IS NOT NULL ' : '' ;
	$where_socle       = ($only_socle) ? 'AND entree_id !=0 ' : '' ;
	$order_matiere     = ($prof_id)    ? 'matiere_nom ASC, ' : '' ;
	$order_niveau      = (!$niveau_id) ? 'niveau_ordre ASC, ' : '' ;
	$DB_SQL = 'SELECT ';
	$DB_SQL.= 'matiere_id, matiere_ref, matiere_nom, ';
	$DB_SQL.= 'niveau_id, niveau_ref, niveau_nom, ';
	$DB_SQL.= 'domaine_id, domaine_ordre, domaine_ref, domaine_nom, ';
	$DB_SQL.= 'theme_id, theme_ordre, theme_nom, ';
	$DB_SQL.= 'item_id, item_ordre, item_nom, item_coef, item_cart, item_lien, ';
	$DB_SQL.= $select_socle_nom;
	$DB_SQL.= 'FROM sacoche_referentiel ';
	$DB_SQL.= $join_user_matiere;
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (matiere_id,niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (theme_id) ';
	$DB_SQL.= $join_socle_item;
	$DB_SQL.= 'WHERE '.$where_user.$where_matiere.$where_niveau.$where_item.$where_socle;
	$DB_SQL.= 'ORDER BY '.$order_matiere.$order_niveau.'domaine_ordre ASC, theme_ordre ASC, item_ordre ASC';
	$DB_VAR = array(':partage'=>0);
	if($prof_id)    {$DB_VAR[':user_id']    = $prof_id;}
	if($matiere_id) {$DB_VAR[':matiere_id'] = $matiere_id;}
	if($niveau_id)  {$DB_VAR[':niveau_id']  = $niveau_id;}
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_recuperer_arborescence_selection
 * Retourner l'arborescence des items travaillés et des matières concernées par des élèves selectionnés, pour les items choisis !
 * Appelé par [ releve_items_selection.ajax.php ]
 *
 * @param string $liste_eleve_id  id des élèves séparés par des virgules
 * @param string $liste_item_id   id des items séparés par des virgules
 * @param string $date_mysql_debut
 * @param string $date_mysql_fin
 * @return array
 */
function DB_STRUCTURE_recuperer_arborescence_selection($liste_eleve_id,$liste_item_id,$date_mysql_debut,$date_mysql_fin)
{
	$DB_SQL = 'SELECT item_id , ';
	$DB_SQL.= 'CONCAT(matiere_ref,".",niveau_ref,".",domaine_ref,theme_ordre,item_ordre) AS item_ref , ';
	$DB_SQL.= 'item_nom , item_coef , item_cart , entree_id AS item_socle , item_lien , ';
	$DB_SQL.= 'matiere_id , matiere_nom , ';
	$DB_SQL.= 'referentiel_calcul_methode AS calcul_methode , referentiel_calcul_limite AS calcul_limite ';
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel USING (matiere_id,niveau_id) ';
	$DB_SQL.= 'WHERE eleve_id IN('.$liste_eleve_id.') AND item_id IN('.$liste_item_id.') AND saisie_date>=:date_debut AND saisie_date<=:date_fin ';
	$DB_SQL.= 'ORDER BY matiere_ordre ASC, matiere_nom ASC, niveau_ordre ASC, domaine_ordre ASC, theme_ordre ASC, item_ordre ASC';
	$DB_VAR = array(':date_debut'=>$date_mysql_debut,':date_fin'=>$date_mysql_fin);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR , TRUE);
	$tab_matiere = array();
	foreach($DB_TAB as $item_id => $tab)
	{
		foreach($tab as $key => $DB_ROW)
		{
			unset($DB_TAB[$item_id][$key]['matiere_id'],$DB_TAB[$item_id][$key]['matiere_nom']);
		}
		$tab_matiere[$DB_ROW['matiere_id']] = $DB_ROW['matiere_nom'];
	}
	return array($DB_TAB,$tab_matiere);
}

/**
 * DB_STRUCTURE_recuperer_arborescence_bilan
 * Retourner l'arborescence des items travaillés par des élèves donnés (ou un seul), pour une matière donnée (ou toutes), durant une période donnée
 * Appelé par [ releve_items_matiere.ajax.php ] [ releve_items_multimatiere.ajax.php ]
 *
 * @param string $liste_eleve_id   id des élèves séparés par des virgules ; il peut n'y avoir qu'un id, en particulier si c'est un élève qui demande un bilan
 * @param int    $matiere_id       id de la matière ; false pour toutes les matières
 * @param bool   $only_socle       1 pour ne retourner que les items reliés au socle, 0 sinon
 * @param string $date_mysql_debut
 * @param string $date_mysql_fin
 * @return array
 */
function DB_STRUCTURE_recuperer_arborescence_bilan($liste_eleve_id,$matiere_id,$only_socle,$date_mysql_debut,$date_mysql_fin)
{
	$where_eleve      = (strpos($liste_eleve_id,',')) ? 'eleve_id IN('.$liste_eleve_id.') '    : 'eleve_id='.$liste_eleve_id.' ' ; // Pour IN(...) NE PAS passer la liste dans $DB_VAR sinon elle est convertie en nb entier
	$where_matiere    = ($matiere_id)                 ? 'AND matiere_id=:matiere '             : 'AND (matiere_id IN('.$_SESSION['MATIERES'].') OR matiere_partage=:partage) ' ; // Test matiere pour éviter des matières décochées par l'admin.
	$where_niveau     = 'AND niveau_id IN('.$_SESSION['CYCLES'].','.$_SESSION['NIVEAUX'].') ' ;
	$where_socle      = ($only_socle)                 ? 'AND entree_id !=0 '                   : '' ;
	$where_date_debut = ($date_mysql_debut)           ? 'AND saisie_date>=:date_debut '        : '';
	$where_date_fin   = ($date_mysql_fin)             ? 'AND saisie_date<=:date_fin '          : '';
	$order_matiere    = (!$matiere_id)                ? 'matiere_ordre ASC, matiere_nom ASC, ' : '' ;
	$DB_SQL = 'SELECT item_id , ';
	$DB_SQL.= 'CONCAT(matiere_ref,".",niveau_ref,".",domaine_ref,theme_ordre,item_ordre) AS item_ref , ';
	$DB_SQL.= 'item_nom , item_coef , item_cart , entree_id AS item_socle , item_lien , ';
	$DB_SQL.= (!$matiere_id) ? 'matiere_id , matiere_nom , ' : '' ;
	$DB_SQL.= 'referentiel_calcul_methode AS calcul_methode , referentiel_calcul_limite AS calcul_limite ';
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel USING (matiere_id,niveau_id) ';
	$DB_SQL.= 'WHERE '.$where_eleve.$where_matiere.$where_niveau.$where_socle.$where_date_debut.$where_date_fin;
	$DB_SQL.= 'GROUP BY item_id ';
	$DB_SQL.= 'ORDER BY '.$order_matiere.'niveau_ordre ASC, domaine_ordre ASC, theme_ordre ASC, item_ordre ASC';
	$DB_VAR = array(':matiere'=>$matiere_id,':partage'=>0,':date_debut'=>$date_mysql_debut,':date_fin'=>$date_mysql_fin);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR , TRUE);
	if($matiere_id)
	{
		return $DB_TAB;
	}
	else
	{
		// Traiter le résultat de la requête pour en extraire un sous-tableau $tab_matiere
		$tab_matiere = array();
		foreach($DB_TAB as $item_id => $tab)
		{
			foreach($tab as $key => $DB_ROW)
			{
				$tab_matiere[$DB_ROW['matiere_id']] = $DB_ROW['matiere_nom'];
				unset($DB_TAB[$item_id][$key]['matiere_id'],$DB_TAB[$item_id][$key]['matiere_nom']);
			}
		}
		return array($DB_TAB,$tab_matiere);
	}
}

/**
 * DB_STRUCTURE_recuperer_arborescence_synthese
 * Retourner l'arborescence des items travaillés par des élèves selectionnés, durant la période choisie => pour la synthèse matière ou multi-matières
 * Appelé par [ releve_synthese_matiere.ajax.php ] [ releve_synthese_multimatiere.ajax.php ]
 *
 * @param string $liste_eleve_id   id des élèves séparés par des virgules ; il peut n'y avoir qu'un id, en particulier si c'est un élève qui demande un bilan
 * @param int    $matiere_id       id de la matière ; false pour toutes les matières
 * @param int    $only_socle       1 pour ne retourner que les items reliés au socle, 0 sinon
 * @param int    $only_niveau      0 pour tous les niveaux, autre pour un niveau donné
 * @param string $mode_synthese    'predefini' ou 'domaine' ou 'theme'
 * @param string $date_mysql_debut
 * @param string $date_mysql_fin
 * @return array
 */
function DB_STRUCTURE_recuperer_arborescence_synthese($liste_eleve_id,$matiere_id,$only_socle,$only_niveau,$mode_synthese='predefini',$date_mysql_debut,$date_mysql_fin)
{
	$select_matiere    = (!$matiere_id)                ? 'matiere_id , matiere_nom , '                          : '' ;
	$select_synthese   = ($mode_synthese=='predefini') ? ', referentiel_mode_synthese AS mode_synthese '        : '' ;
	$where_eleve       = (strpos($liste_eleve_id,',')) ? 'eleve_id IN('.$liste_eleve_id.') '                    : 'eleve_id='.$liste_eleve_id.' ' ; // Pour IN(...) NE PAS passer la liste dans $DB_VAR sinon elle est convertie en nb entier
	$where_matiere     = ($matiere_id)                 ? 'AND matiere_id=:matiere '                             : 'AND (matiere_id IN('.$_SESSION['MATIERES'].') OR matiere_partage=:partage) ' ; // Test matiere pour éviter des matières décochées par l'admin.
	$where_socle       = ($only_socle)                 ? 'AND entree_id!=0 '                                    : '' ;
	$where_niveau      = ($only_niveau)                ? 'AND niveau_id='.$only_niveau.' '                      : 'AND niveau_id IN('.$_SESSION['CYCLES'].','.$_SESSION['NIVEAUX'].') ' ;
	$where_date_debut  = ($date_mysql_debut)           ? 'AND saisie_date>=:date_debut '                        : '';
	$where_date_fin    = ($date_mysql_fin)             ? 'AND saisie_date<=:date_fin '                          : '';
	$where_synthese    = ($mode_synthese=='predefini') ? 'AND referentiel_mode_synthese IN("domaine","theme") ' : '';
	$order_matiere     = (!$matiere_id)                ? 'matiere_ordre ASC, matiere_nom ASC, '                 : '' ;
	$DB_SQL = 'SELECT item_id , ';
	$DB_SQL.= 'CONCAT(matiere_ref,".",niveau_ref,".",domaine_ref,theme_ordre,item_ordre) AS item_ref , ';
	$DB_SQL.= 'item_nom , item_coef , item_cart , entree_id AS item_socle , item_lien , ';
	$DB_SQL.= 'theme_id , theme_nom , ';
	$DB_SQL.= 'domaine_id , domaine_nom , ';
	$DB_SQL.= $select_matiere;
	$DB_SQL.= 'niveau_nom , ';
	$DB_SQL.= 'referentiel_calcul_methode AS calcul_methode , referentiel_calcul_limite AS calcul_limite '.$select_synthese;
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel USING (matiere_id,niveau_id) ';
	$DB_SQL.= 'WHERE '.$where_eleve.$where_matiere.$where_socle.$where_niveau.$where_date_debut.$where_date_fin.$where_synthese;
	$DB_SQL.= 'GROUP BY item_id ';
	$DB_SQL.= 'ORDER BY '.$order_matiere.'niveau_ordre ASC, domaine_ordre ASC, theme_ordre ASC, item_ordre ASC';
	$DB_VAR = array(':matiere'=>$matiere_id,':partage'=>0,':date_debut'=>$date_mysql_debut,':date_fin'=>$date_mysql_fin);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR , TRUE);
	// Traiter le résultat de la requête pour en extraire des sous-tableaux $tab_synthese et éventuellement $tab_matiere
	$tab_synthese = array();
	$tab_matiere  = array();
	foreach($DB_TAB as $item_id => $tab)
	{
		foreach($tab as $key => $DB_ROW)
		{
			if(!$matiere_id)
			{
				$tab_matiere[$DB_ROW['matiere_id']] = $DB_ROW['matiere_nom'];
				unset($DB_TAB[$item_id][$key]['matiere_nom']);
			}
			if($mode_synthese=='predefini')
			{
				$tab_synthese[$DB_ROW['mode_synthese'].'_'.$DB_ROW[$DB_ROW['mode_synthese'].'_id']] = $DB_ROW['niveau_nom'].' - '.$DB_ROW[$DB_ROW['mode_synthese'].'_nom'] ;
				$DB_TAB[$item_id][$key]['synthese_ref'] = $DB_ROW['mode_synthese'].'_'.$DB_ROW[$DB_ROW['mode_synthese'].'_id'];
				unset($DB_TAB[$item_id][$key]['mode_synthese']);
			}
			else
			{
				$tab_synthese[$mode_synthese.'_'.$DB_ROW[$mode_synthese.'_id']] = $DB_ROW['niveau_nom'].' - '.$DB_ROW[$mode_synthese.'_nom'] ;
				$DB_TAB[$item_id][$key]['synthese_ref'] = $mode_synthese.'_'.$DB_ROW[$mode_synthese.'_id'];
			}
			unset($DB_TAB[$item_id][$key]['niveau_nom'],$DB_TAB[$item_id][$key]['domaine_id'],$DB_TAB[$item_id][$key]['domaine_nom'],$DB_TAB[$item_id][$key]['theme_id'],$DB_TAB[$item_id][$key]['theme_nom']);
		}
	}
	if($matiere_id)
	{
		return array($DB_TAB,$tab_synthese);
	}
	else
	{
		return array($DB_TAB,$tab_synthese,$tab_matiere);
	}
}

/**
 * DB_STRUCTURE_recuperer_arborescence_palier
 *
 * @param string|bool $liste_palier_id   id des paliers séparés par des virgules ; false pour retourner tous les paliers
 * @param string|bool $liste_pilier_id   id des piliers séparés par des virgules (facultatif, pour restreindre à des piliers précis)
 * @return array
 */
function DB_STRUCTURE_recuperer_arborescence_palier($liste_palier_id=false,$liste_pilier_id=false)
{
	$tab_where = array();
	if($liste_palier_id) { $tab_where[] = 'palier_id IN('.$liste_palier_id.') '; }
	if($liste_pilier_id) { $tab_where[] = 'pilier_id IN('.$liste_pilier_id.') '; }
	$DB_SQL = 'SELECT * FROM sacoche_socle_palier ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_pilier USING (palier_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_section USING (pilier_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_entree USING (section_id) ';
	$DB_SQL.= (count($tab_where)) ? 'WHERE '.implode('AND ',$tab_where) : '' ;
	$DB_SQL.= 'ORDER BY palier_ordre ASC, pilier_ordre ASC, section_ordre ASC, entree_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_recuperer_piliers
 * Retourner les piliers d'un palier donné
 *
 * @param int $palier_id   id du palier
 * @return array|string
 */
function DB_STRUCTURE_recuperer_piliers($palier_id)
{
	$DB_SQL = 'SELECT * FROM sacoche_socle_pilier ';
	$DB_SQL.= 'WHERE palier_id=:palier_id ';
	$DB_SQL.= 'ORDER BY pilier_ordre ASC';
	$DB_VAR = array(':palier_id'=>$palier_id);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_recuperer_arborescence_pilier
 *
 * @param int         $pilier_id            id du pilier
 * @param string|bool $listing_domaine_id   id des domaines séparés par des virgules (facultatif, pour restreindre à des domaines précis)
 * @return array
 */
function DB_STRUCTURE_recuperer_arborescence_pilier($pilier_id,$listing_domaine_id='')
{
	$where_domaine = ($listing_domaine_id) ? 'AND section_id IN('.$listing_domaine_id.') ' : '';
	$DB_SQL = 'SELECT * FROM sacoche_socle_pilier ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_section USING (pilier_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_entree USING (section_id) ';
	$DB_SQL.= 'WHERE pilier_id=:pilier_id '.$where_domaine;
	$DB_SQL.= 'ORDER BY section_ordre ASC, entree_ordre ASC';
	$DB_VAR = array(':pilier_id'=>$pilier_id);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_recuperer_referentiels_domaines
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_recuperer_referentiels_domaines()
{
	$DB_SQL = 'SELECT matiere_id,niveau_id,domaine_nom FROM sacoche_referentiel_domaine ';
	$DB_SQL.= 'ORDER BY domaine_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_recuperer_referentiels_themes
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_recuperer_referentiels_themes()
{
	$DB_SQL = 'SELECT matiere_id,niveau_id,theme_nom FROM sacoche_referentiel_theme ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'ORDER BY domaine_ordre ASC, theme_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_recuperer_associations_entrees_socle
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_recuperer_associations_entrees_socle()
{
	$DB_SQL = 'SELECT entree_id , item_nom , matiere_ref , niveau_ref , ';
	$DB_SQL.= 'CONCAT(domaine_ref,theme_ordre,item_ordre) AS item_ref ';
	$DB_SQL.= 'FROM sacoche_referentiel ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (matiere_id,niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (theme_id) ';
	$DB_SQL.= 'WHERE entree_id>0 AND (matiere_id IN('.$_SESSION['MATIERES'].') OR matiere_partage=:partage) AND niveau_id IN('.$_SESSION['CYCLES'].','.$_SESSION['NIVEAUX'].') ' ; // Test matiere pour éviter des matières décochées par l'admin.
	$DB_SQL.= 'GROUP BY item_id ';
	$DB_SQL.= 'ORDER BY matiere_nom ASC, niveau_ordre ASC, domaine_ordre ASC, theme_ordre ASC, item_ordre ASC';
	$DB_VAR = array(':partage'=>0);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_recuperer_item_infos
 *
 * @param int   $item_id
 * @return int
 */
function DB_STRUCTURE_recuperer_item_infos($item_id)
{
	$DB_SQL = 'SELECT item_nom , item_cart , ';
	$DB_SQL.= 'CONCAT(matiere_ref,".",niveau_ref,".",domaine_ref,theme_ordre,item_ordre) AS item_ref ';
	$DB_SQL.= 'FROM sacoche_referentiel_item ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE item_id=:item_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':item_id'=>$item_id);
	return DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_version_base
 *
 * @param void
 * @return string
 */
function DB_version_base()
{
	$DB_SQL = 'SELECT parametre_valeur ';
	$DB_SQL.= 'FROM sacoche_parametre ';
	$DB_SQL.= 'WHERE parametre_nom=:parametre_nom ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':parametre_nom'=>'version_base');
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_recuperer_statistiques
 *
 * @param void
 * @return array($prof_nb,$prof_use,$eleve_nb,$eleve_use,$score_nb)
 */
function DB_STRUCTURE_recuperer_statistiques()
{
	// nb professeurs enregistrés ; nb élèves enregistrés
	$DB_SQL = 'SELECT user_profil, COUNT(*) AS nombre FROM sacoche_user WHERE user_statut=1 GROUP BY user_profil';
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null , TRUE , TRUE);
	$prof_nb  = (isset($DB_TAB['professeur'])) ? $DB_TAB['professeur']['nombre'] : 0 ;
	$eleve_nb = (isset($DB_TAB['eleve']))      ? $DB_TAB['eleve']['nombre']      : 0 ;
	// nb professeurs connectés ; nb élèves connectés
	$DB_SQL = 'SELECT user_profil, COUNT(*) AS nombre FROM sacoche_user WHERE user_statut=1 AND user_connexion_date>DATE_SUB(NOW(),INTERVAL 6 MONTH) GROUP BY user_profil';
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null , TRUE , TRUE);
	$prof_use  = (isset($DB_TAB['professeur'])) ? $DB_TAB['professeur']['nombre'] : 0 ;
	$eleve_use = (isset($DB_TAB['eleve']))      ? $DB_TAB['eleve']['nombre']      : 0 ;
	// nb notes saisies
	$DB_SQL = 'SELECT COUNT(*) AS nombre FROM sacoche_saisie';
	$DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$score_nb = $DB_ROW['nombre'];
	// Retour
	return array($prof_nb,$prof_use,$eleve_nb,$eleve_use,$score_nb);
}

/**
 * DB_STRUCTURE_recuperer_item_popularite
 * Calculer pour chaque item sa popularité, i.e. le nb de demandes pour les élèves concernés.
 *
 * @param string $listing_demande_id   id des demandes séparés par des virgules
 * @param string $listing_user_id      id des élèves séparés par des virgules
 * @return array   [i]=>array('item_id','popularite')
 */
function DB_STRUCTURE_recuperer_item_popularite($listing_demande_id,$listing_user_id)
{
	$DB_SQL = 'SELECT item_id , COUNT(item_id) AS popularite ';
	$DB_SQL.= 'FROM sacoche_demande ';
	$DB_SQL.= 'WHERE demande_id IN('.$listing_demande_id.') AND user_id IN('.$listing_user_id.') ';
	$DB_SQL.= 'GROUP BY item_id ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_recuperer_professeurs_eleve_matiere
 * Retourner une liste de professeurs attachés à un élève identifié et une matière donnée.
 *
 * @param int $eleve_id
 * @param int $matiere_id
 * @return array
 */
function DB_STRUCTURE_recuperer_professeurs_eleve_matiere($eleve_id,$matiere_id)
{
	// On connait la classe ($_SESSION['ELEVE_CLASSE_ID']), donc on commence par récupérer les groupes éventuels associés à l'élève
	// DB::query(SACOCHE_STRUCTURE_BD_NAME , 'SET group_concat_max_len = ...'); // Pour lever si besoin une limitation de GROUP_CONCAT (group_concat_max_len est par défaut limité à une chaine de 1024 caractères).
	$DB_SQL = 'SELECT GROUP_CONCAT(DISTINCT groupe_id SEPARATOR ",") AS sacoche_liste_groupe_id ';
	$DB_SQL.= 'FROM sacoche_jointure_user_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'WHERE user_id=:user_id AND groupe_type=:type2 ';
	$DB_SQL.= 'GROUP BY user_id ';
	$DB_VAR = array(':user_id'=>$eleve_id,':type2'=>'groupe');
	$liste_groupe_id = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	if( (!$_SESSION['ELEVE_CLASSE_ID']) && (!$liste_groupe_id) )
	{
		// élève sans classe et sans groupe
		return false;
	}
	if(!$liste_groupe_id)
	{
		$liste_groupes = $_SESSION['ELEVE_CLASSE_ID'];
	}
	elseif(!$_SESSION['ELEVE_CLASSE_ID'])
	{
		$liste_groupes = $liste_groupe_id;
	}
	else
	{
		$liste_groupes = $_SESSION['ELEVE_CLASSE_ID'].','.$liste_groupe_id;
	}
	// Maintenant qu'on a la matière et la classe / les groupes, on cherche les profs à la fois dans sacoche_jointure_user_matiere et sacoche_jointure_user_groupe .
	// On part de sacoche_jointure_user_matiere qui ne contient que des profs.
	$DB_SQL = 'SELECT DISTINCT(user_id) ';
	$DB_SQL.= 'FROM sacoche_jointure_user_matiere ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (user_id) ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id AND groupe_id IN('.$liste_groupes.') ';
	$DB_VAR = array(':matiere_id'=>$matiere_id);
	return DB::queryCol(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_parametres (contenu de la table 'sacoche_parametre')
 *
 * @param void|string $listing_param   nom des paramètres entourés de guillemets et séparés par des virgules (tout si rien de transmis)
 * @return array
 */
function DB_STRUCTURE_lister_parametres($listing_param=false)
{
	$nb_params = substr_count($listing_param,',')+1;
	$DB_SQL = 'SELECT parametre_nom,parametre_valeur ';
	$DB_SQL.= 'FROM sacoche_parametre ';
	$DB_SQL.= ($listing_param==false) ? '' : 'WHERE parametre_nom IN('.$listing_param.') ' ;
	$DB_SQL.= ($listing_param==false) ? '' : 'LIMIT '.$nb_params ;
	return (($listing_param==false)||($nb_params>1)) ? DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null) : DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null) ;
}

/**
 * DB_STRUCTURE_lister_result_eleve_items
 * Retourner les résultats pour un élève, pour des items donnés
 *
 * @param int    $eleve_id
 * @param string $liste_item_id   id des items séparés par des virgules
 * @return array
 */
function DB_STRUCTURE_lister_result_eleve_items($eleve_id,$liste_item_id)
{
	$DB_SQL = 'SELECT item_id , saisie_note AS note ';
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'WHERE eleve_id=:eleve_id AND item_id IN('.$liste_item_id.') AND saisie_note!="REQ" AND saisie_visible_date<=NOW() ';
	$DB_SQL.= 'ORDER BY saisie_date ASC ';
	$DB_VAR = array(':eleve_id'=>$eleve_id);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_date_last_eleves_items
 * Retourner, pour des élèves et les items donnés, la date de la dernière évaluation (pour vérifier qu'il faut bien prendre l'item en compte)
 *
 * @param string $liste_eleve_id  id des élèves séparés par des virgules
 * @param string $liste_item_id   id des items séparés par des virgules
 * @return array
 */
function DB_STRUCTURE_lister_date_last_eleves_items($liste_eleve_id,$liste_item_id)
{
	$DB_SQL = 'SELECT eleve_id , item_id , MAX(saisie_date) AS date_last ';
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'WHERE eleve_id IN('.$liste_eleve_id.') AND item_id IN('.$liste_item_id.') AND saisie_note!="REQ" ';
	$DB_SQL.= 'GROUP BY eleve_id, item_id ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_lister_result_eleves_matiere
 * Retourner les résultats pour des élèves donnés, pour des items donnés d'une matiere donnée, sur une période donnée
 *
 * @param string $liste_eleve_id  id des élèves séparés par des virgules
 * @param string $liste_item_id   id des items séparés par des virgules
 * @param string $date_mysql_debut
 * @param string $date_mysql_fin
 * @param string $user_profil
 * @return array
 */
function DB_STRUCTURE_lister_result_eleves_matiere($liste_eleve_id,$liste_item_id,$date_mysql_debut,$date_mysql_fin,$user_profil)
{
	$sql_debut = ($date_mysql_debut)     ? 'AND saisie_date>=:date_debut '   : '';
	$sql_fin   = ($date_mysql_fin)       ? 'AND saisie_date<=:date_fin '     : '';
	$sql_view  = ($user_profil=='eleve') ? 'AND saisie_visible_date<=NOW() ' : '';
	$DB_SQL = 'SELECT eleve_id , item_id , ';
	$DB_SQL.= 'saisie_note AS note , saisie_date AS date , saisie_info AS info ';
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'LEFT JOIN sacoche_devoir USING (devoir_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE eleve_id IN('.$liste_eleve_id.') AND item_id IN('.$liste_item_id.') AND niveau_id IN('.$_SESSION['CYCLES'].','.$_SESSION['NIVEAUX'].') AND saisie_note!="REQ" '.$sql_debut.$sql_fin.$sql_view;
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, domaine_ordre ASC, theme_ordre ASC, item_ordre ASC, saisie_date ASC';
	$DB_VAR = array(':date_debut'=>$date_mysql_debut,':date_fin'=>$date_mysql_fin);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_result_eleves_matieres
 * Retourner les résultats pour des élèves donnés, pour des items donnés de plusieurs matieres, sur une période donnée
 *
 * @param string $liste_eleve_id  id des élèves séparés par des virgules
 * @param string $liste_item_id   id des items séparés par des virgules
 * @param string $date_mysql_debut
 * @param string $date_mysql_fin
 * @param string $user_profil
 * @return array
 */
function DB_STRUCTURE_lister_result_eleves_matieres($liste_eleve_id,$liste_item_id,$date_mysql_debut,$date_mysql_fin,$user_profil)
{
	$sql_debut = ($date_mysql_debut)     ? 'AND saisie_date>=:date_debut '   : '';
	$sql_fin   = ($date_mysql_fin)       ? 'AND saisie_date<=:date_fin '     : '';
	$sql_view  = ($user_profil=='eleve') ? 'AND saisie_visible_date<=NOW() ' : '';
	$DB_SQL = 'SELECT eleve_id , matiere_id , item_id , ';
	$DB_SQL.= 'saisie_note AS note , saisie_date AS date , saisie_info AS info ';
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'LEFT JOIN sacoche_devoir USING (devoir_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE eleve_id IN('.$liste_eleve_id.') AND item_id IN('.$liste_item_id.') AND niveau_id IN('.$_SESSION['CYCLES'].','.$_SESSION['NIVEAUX'].') AND saisie_note!="REQ" '.$sql_debut.$sql_fin.$sql_view;
	$DB_SQL.= 'ORDER BY matiere_ordre ASC, niveau_ordre ASC, domaine_ordre ASC, theme_ordre ASC, item_ordre ASC, saisie_date ASC';
	$DB_VAR = array(':date_debut'=>$date_mysql_debut,':date_fin'=>$date_mysql_fin);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_result_eleve_palier
 * Retourner les résultats pour 1 élève donné, pour 1 item du socle donné
 *
 * @param string $eleve_id
 * @param string $entree_id
 * @param string $user_profil
 * @return array
 */
function DB_STRUCTURE_lister_result_eleve_palier($eleve_id,$entree_id)
{
	$DB_SQL = 'SELECT item_id , saisie_note AS note , item_nom , ';
	$DB_SQL.= 'CONCAT(matiere_ref,".",niveau_ref,".",domaine_ref,theme_ordre,item_ordre) AS item_ref , ';
	$DB_SQL.= 'matiere_id , '; // Besoin s'il faut filtrer à une langue précise pour la compétence 2
	$DB_SQL.= 'referentiel_calcul_methode AS calcul_methode , referentiel_calcul_limite AS calcul_limite ';
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel USING (matiere_id,niveau_id) ';
	$DB_SQL.= 'WHERE eleve_id=:eleve_id AND entree_id=:entree_id AND niveau_id IN('.$_SESSION['CYCLES'].','.$_SESSION['NIVEAUX'].') AND saisie_note!="REQ" ';
	$DB_SQL.= 'ORDER BY matiere_nom ASC, niveau_ordre ASC, domaine_ordre ASC, theme_ordre ASC, item_ordre ASC, saisie_date ASC';
	$DB_VAR = array(':eleve_id'=>$eleve_id,':entree_id'=>$entree_id);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_result_eleves_palier_sans_infos_items
 * Retourner les résultats pour des élèves donnés, pour des entrées du socle données d'un certain palier
 * Les informations concernant les items sont collectés dans un second temps sinon on peut dépasser une capacité memory_limit de 32Mo.
 *
 * @param string $liste_eleve_id   id des élèves séparés par des virgules
 * @param string $liste_entree_id  id des entrées séparées par des virgules
 * @param string $user_profil
 * @return array
 */
function DB_STRUCTURE_lister_result_eleves_palier_sans_infos_items($liste_eleve_id,$liste_entree_id,$user_profil)
{
	$sql_view  = ($user_profil=='eleve') ? 'AND saisie_visible_date<=NOW() ' : '';
	$DB_SQL = 'SELECT eleve_id , entree_id AS socle_id , item_id , saisie_note AS note , ';
	$DB_SQL.= 'matiere_id '; // Besoin s'il faut filtrer à une langue précise pour la compétence 2
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE eleve_id IN('.$liste_eleve_id.') AND entree_id IN('.$liste_entree_id.') AND niveau_id IN('.$_SESSION['CYCLES'].','.$_SESSION['NIVEAUX'].') AND saisie_note!="REQ" '.$sql_view;
	$DB_SQL.= 'ORDER BY matiere_nom ASC, niveau_ordre ASC, domaine_ordre ASC, theme_ordre ASC, item_ordre ASC, saisie_date ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , NULL);
}

/**
 * DB_STRUCTURE_lister_infos_items
 * Complément de la fonction DB_STRUCTURE_lister_result_eleves_palier_sans_infos_items()
 *
 * @param string $liste_item_id   id des items séparés par des virgules
 * @param bool   $detail
 * @return array
 */
function DB_STRUCTURE_lister_infos_items($liste_item_id,$detail)
{
	$DB_SQL = 'SELECT item_id , ';
	if($detail)
	{
		$DB_SQL.= 'item_nom , entree_id AS socle_id , ';
		$DB_SQL.= 'CONCAT(matiere_ref,".",niveau_ref,".",domaine_ref,theme_ordre,item_ordre) AS item_ref , ';
		$DB_SQL.= 'item_coef , item_cart , item_lien , '; // Besoin pour l'élève s'il veut formuler une demande d'évaluation
		$DB_SQL.= 'matiere_id , '; // Besoin pour l'élève s'il ajoute l'item aux demandes d'évaluations
	}
	$DB_SQL.= 'referentiel_calcul_methode AS calcul_methode , referentiel_calcul_limite AS calcul_limite ';
	$DB_SQL.= 'FROM sacoche_referentiel_item ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel USING (matiere_id,niveau_id) ';
	$DB_SQL.= 'WHERE item_id IN('.$liste_item_id.') ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , NULL);
}

/**
 * DB_STRUCTURE_lister_matieres_partagees_SACoche
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_matieres_partagees_SACoche()
{
	$DB_SQL = 'SELECT * FROM sacoche_matiere ';
	$DB_SQL.= 'WHERE matiere_partage=:partage ';
	$DB_SQL.= 'ORDER BY matiere_nom ASC';
	$DB_VAR = array(':partage'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_matieres_specifiques
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_matieres_specifiques()
{
	$DB_SQL = 'SELECT * FROM sacoche_matiere ';
	$DB_SQL.= 'WHERE matiere_partage=:partage ';
	$DB_SQL.= 'ORDER BY matiere_nom ASC';
	$DB_VAR = array(':partage'=>0);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_matieres_etablissement
 *
 * @param string $listing_matieres   id des matières communes choisies séparés par des virgules
 * @param bool   $with_transversal   avec ou non la matière tranversale
 * @param bool   $order_by_name      si false, prendre le champ matiere_ordre
 * @return array
 */
function DB_STRUCTURE_lister_matieres_etablissement($listing_matieres,$with_transversal,$order_by_name)
{
	$where_trans = ($with_transversal) ? '' : 'AND matiere_transversal=0 ' ;
	$order_champ = ($order_by_name)    ? '' : 'matiere_ordre ASC, ' ;
	$DB_SQL = 'SELECT * FROM sacoche_matiere ';
	$DB_SQL.= ($listing_matieres) ? 'WHERE (matiere_id IN('.$listing_matieres.') OR matiere_partage=:partage) '.$where_trans : 'WHERE matiere_partage=:partage '.$where_trans;
	$DB_SQL.= 'ORDER BY '.$order_champ.'matiere_nom ASC';
	$DB_VAR = array(':partage'=>0);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_matieres_professeur_infos_referentiel
 *
 * @param string $listing_matieres   id des matières de l'établissement séparées par des virgules
 * @param int $user_id
 * @return array|string
 */
function DB_STRUCTURE_lister_matieres_professeur_infos_referentiel($listing_matieres,$user_id)
{
	$DB_SQL = 'SELECT matiere_id,matiere_nom,matiere_partage,matiere_nb_demandes,jointure_coord ';
	$DB_SQL.= 'FROM sacoche_jointure_user_matiere ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'WHERE (matiere_id IN('.$listing_matieres.') OR matiere_partage=:partage) AND user_id=:user_id '; // Test matiere car un prof peut être encore relié à des matières décochées par l'admin.
	$DB_SQL.= 'ORDER BY matiere_nom ASC';
	$DB_VAR = array(':user_id'=>$user_id,':partage'=>0);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_paliers_SACoche
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_paliers_SACoche()
{
	$DB_SQL = 'SELECT * FROM sacoche_socle_palier ';
	$DB_SQL.= 'ORDER BY palier_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_lister_niveaux_SACoche
 * Sans les niveaux de type 'cycles'.
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_niveaux_SACoche()
{
	$DB_SQL = 'SELECT * FROM sacoche_niveau ';
	$DB_SQL.= 'WHERE niveau_cycle=0 ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_lister_cycles_SACoche
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_cycles_SACoche()
{
	$DB_SQL = 'SELECT * FROM sacoche_niveau ';
	$DB_SQL.= 'WHERE niveau_cycle=1 ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_lister_niveaux_etablissement
 *
 * @param string      $listing_niveaux   id des niveaux séparés par des virgules ; ne peut pas être vide
 * @param string|bool $listing_cycles    id des cycles séparés par des virgules ; false pour ne pas retourner les cycles
 * @return array
 */
function DB_STRUCTURE_lister_niveaux_etablissement($listing_niveaux,$listing_cycles)
{
	$listing = ($listing_cycles) ? $listing_niveaux.','.$listing_cycles : $listing_niveaux ;
	$DB_SQL = 'SELECT * FROM sacoche_niveau ';
	$DB_SQL.= 'WHERE niveau_id IN('.$listing.') ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_lister_periodes
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_periodes()
{
	$DB_SQL = 'SELECT * FROM sacoche_periode ';
	$DB_SQL.= 'ORDER BY periode_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_lister_groupes_sauf_classes
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_groupes_sauf_classes()
{
	$DB_SQL = 'SELECT groupe_id,groupe_type FROM sacoche_groupe ';
	$DB_SQL.= 'WHERE groupe_type!=:type ';
	$DB_SQL.= 'ORDER BY groupe_ref ASC';
	$DB_VAR = array(':type'=>'classe');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_classes
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_classes()
{
	$DB_SQL = 'SELECT * FROM sacoche_groupe ';
	$DB_SQL.= 'WHERE groupe_type=:type ';
	$DB_SQL.= 'ORDER BY groupe_ref ASC';
	$DB_VAR = array(':type'=>'classe');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_groupes
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_groupes()
{
	$DB_SQL = 'SELECT * FROM sacoche_groupe ';
	$DB_SQL.= 'WHERE groupe_type=:type ';
	$DB_SQL.= 'ORDER BY groupe_ref ASC';
	$DB_VAR = array(':type'=>'groupe');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_classes_avec_niveaux
 *
 * @param string   $niveau_ordre   facultatif, ASC par défaut, DESC possible
 * @return array
 */
function DB_STRUCTURE_lister_classes_avec_niveaux($niveau_ordre='ASC')
{
	$DB_SQL = 'SELECT * FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_type=:type ';
	$DB_SQL.= 'ORDER BY niveau_ordre '.$niveau_ordre.', groupe_ref ASC';
	$DB_VAR = array(':type'=>'classe');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_groupes_avec_niveaux
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_groupes_avec_niveaux()
{
	$DB_SQL = 'SELECT * FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_type=:type ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_ref ASC';
	$DB_VAR = array(':type'=>'groupe');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_groupes_professeur
 *
 * @param int $prof_id
 * @return array
 */
function DB_STRUCTURE_lister_groupes_professeur($prof_id)
{
	$DB_SQL = 'SELECT * FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE ( user_id=:user_id OR groupe_prof_id=:user_id ) AND groupe_type!=:type4 ';
	$DB_SQL.= 'GROUP BY groupe_id '; // indispensable pour les groupes de besoin, sinon autant de lignes que de membres du groupe
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':user_id'=>$prof_id,':type4'=>'eval');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_classes_groupes_professeur
 *
 * @param int $prof_id
 * @return array
 */
function DB_STRUCTURE_lister_classes_groupes_professeur($prof_id)
{
	$DB_SQL = 'SELECT groupe_id, groupe_nom, groupe_type ';
	$DB_SQL.= 'FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE user_id=:user_id AND groupe_type IN (:type1,:type2) ';
	$DB_SQL.= 'ORDER BY groupe_type ASC, niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':user_id'=>$prof_id,':type1'=>'classe',':type2'=>'groupe');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_classes_parent
 *
 * @param int $parent_id
 * @return array
 */
function DB_STRUCTURE_lister_classes_parent($parent_id)
{
	$DB_SQL = 'SELECT groupe_id, groupe_nom, groupe_type ';
	$DB_SQL.= 'FROM sacoche_jointure_parent_eleve ';
	$DB_SQL.= 'LEFT JOIN sacoche_user ON sacoche_jointure_parent_eleve.eleve_id=sacoche_user.user_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe ON eleve_classe_id=groupe_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE parent_id=:parent_id AND user_profil="eleve" AND user_statut=:statut ';
	$DB_SQL.= 'GROUP BY groupe_id '; // si plusieurs enfants dans la même classe
	$DB_SQL.= 'ORDER BY groupe_type ASC, niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':parent_id'=>$parent_id,':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_groupes_besoins
 *
 * @param int    $prof_id
 * @return array
 */
function DB_STRUCTURE_lister_groupes_besoins($prof_id)
{
	$DB_SQL = 'SELECT groupe_id, groupe_nom, niveau_id, niveau_ordre, niveau_nom FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_prof_id=:prof_id AND groupe_type=:type ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':prof_id'=>$prof_id,':type'=>'besoin');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_classes_et_groupes_avec_niveaux
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_classes_et_groupes_avec_niveaux()
{
	$DB_SQL = 'SELECT * FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_type IN (:type1,:type2) ';
	$DB_SQL.= 'ORDER BY groupe_type ASC, niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':type1'=>'classe',':type2'=>'groupe');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_classes_avec_professeurs
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_classes_avec_professeurs()
{
	$DB_SQL = 'SELECT * FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'WHERE groupe_type=:type AND user_statut=:statut ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_ref ASC, user_nom ASC, user_prenom ASC';
	$DB_VAR = array(':type'=>'classe',':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_users_cibles
 *
 * @param string   $listing_user_id   id des utilisateurs séparés par des virgules
 * @param string   $listing_champs    nom des champs séparés par des virgules
 * @param string   $avec_info         facultatif ; "classe" pour récupérer la classe des élèves | "enfant" pour récupérer une classe et un enfant associé à un parent
 * @return array
 */
function DB_STRUCTURE_lister_users_cibles($listing_user_id,$listing_champs,$avec_info='')
{
	if($avec_info=='classe')
	{
		$DB_SQL = 'SELECT '.$listing_champs.',groupe_nom AS info ';
		$DB_SQL.= 'FROM sacoche_user ';
		$DB_SQL.= 'LEFT JOIN sacoche_groupe ON sacoche_user.eleve_classe_id=sacoche_groupe.groupe_id ';
		$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
		$DB_SQL.= 'WHERE user_id IN('.$listing_user_id.') ';
		$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_ref ASC, user_nom ASC, user_prenom ASC';
	}
	elseif($avec_info=='enfant')
	{
		$DB_SQL = 'SELECT '.$listing_champs.',GROUP_CONCAT( CONCAT(groupe_ref," ",enfant.user_nom) SEPARATOR " - ") AS info ';
		$DB_SQL.= 'FROM sacoche_user AS parent ';
		$DB_SQL.= 'LEFT JOIN sacoche_jointure_parent_eleve ON parent.user_id=sacoche_jointure_parent_eleve.parent_id ';
		$DB_SQL.= 'LEFT JOIN sacoche_user AS enfant ON sacoche_jointure_parent_eleve.eleve_id=enfant.user_id ';
		$DB_SQL.= 'LEFT JOIN sacoche_groupe ON enfant.eleve_classe_id=sacoche_groupe.groupe_id ';
		$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
		$DB_SQL.= 'WHERE parent.user_id IN('.$listing_user_id.') ';
		$DB_SQL.= 'GROUP BY parent.user_id ' ;
		$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_ref ASC, enfant.user_nom ASC, enfant.user_prenom ASC';
	}
	else
	{
		$DB_SQL = 'SELECT '.$listing_champs.' FROM sacoche_user ';
		$DB_SQL.= 'WHERE user_id IN('.$listing_user_id.') ';
		$DB_SQL.= 'ORDER BY user_nom ASC, user_prenom ASC';
	}
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_lister_eleves_cibles
 *
 * @param string   $listing_eleve_id   id des élèves séparés par des virgules
 * @param bool     $with_gepi
 * @param bool     $with_langue
 * @return array|string                le tableau est de la forme [i] => array('eleve_id'=>...,'eleve_nom'=>...,'eleve_prenom'=>...,'eleve_id_gepi'=>...,'eleve_langue'=>...);
 */
function DB_STRUCTURE_lister_eleves_cibles($listing_eleve_id,$with_gepi,$with_langue)
{
	$DB_SQL = 'SELECT user_id AS eleve_id , user_nom AS eleve_nom , user_prenom AS eleve_prenom ';
	$DB_SQL.= ($with_gepi)   ? ', user_id_gepi AS eleve_id_gepi ' : '' ;
	$DB_SQL.= ($with_langue) ? ', eleve_langue ' : '' ;
	$DB_SQL.= 'FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_id IN('.$listing_eleve_id.') AND user_profil=:profil ';
	$DB_SQL.= 'ORDER BY user_nom ASC, user_prenom ASC';
	$DB_VAR = array(':profil'=>'eleve');
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucun élève trouvé correspondant aux identifiants transmis !' ;
}

/**
 * DB_STRUCTURE_lister_eleves_cibles_actifs_avec_sconet_id
 *
 * @param string   $listing_eleve_id   id des élèves séparés par des virgules
 * @param bool     $only_sconet_id     restreindre (ou pas) aux élèves ayant un id sconet
 * @return array
 */
function DB_STRUCTURE_lister_eleves_cibles_actifs_avec_sconet_id($listing_eleve_id,$only_sconet_id)
{
	$DB_SQL = 'SELECT user_id , user_nom , user_prenom , user_sconet_id ';
	$DB_SQL.= 'FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_id IN('.$listing_eleve_id.') AND user_profil=:profil AND user_statut=:statut ';
	$DB_SQL.= $only_sconet_id ? 'AND user_sconet_id>0 ' : '' ;
	$DB_SQL.= 'ORDER BY user_nom ASC, user_prenom ASC';
	$DB_VAR = array(':profil'=>'eleve',':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_eleves_classes
 *
 * @param string   $listing_classe_id   id des classes séparés par des virgules
 * @return array
 */
function DB_STRUCTURE_lister_eleves_classes($listing_classe_id)
{
	$DB_SQL = 'SELECT * FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:statut AND eleve_classe_id IN ('.$listing_classe_id.') ';
	$DB_SQL.= 'ORDER BY user_nom ASC, user_prenom ASC';
	$DB_VAR = array(':profil'=>'eleve',':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_eleves_groupes
 *
 * @param string   $listing_groupe_id   id des groupes séparés par des virgules
 * @return array
 */
function DB_STRUCTURE_lister_eleves_groupes($listing_groupe_id)
{
	$DB_SQL = 'SELECT * FROM sacoche_user ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (user_id) ';
	$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:statut AND groupe_id IN ('.$listing_groupe_id.') ';
	$DB_SQL.= 'ORDER BY user_nom ASC, user_prenom ASC';
	$DB_VAR = array(':profil'=>'eleve',':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_adresses_parents
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_adresses_parents()
{
	$DB_SQL = 'SELECT * ';
	$DB_SQL.= 'FROM sacoche_parent_adresse ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , NULL);
}

/**
 * DB_STRUCTURE_lister_identite_coordonnateurs_par_matiere
 *
 * @param string   $listing_matieres_id   id des matières séparés par des virgules
 * @return array   matiere_id et coord_liste avec identités séparées par "]["
 */
function DB_STRUCTURE_lister_identite_coordonnateurs_par_matiere($listing_matieres_id)
{
	$DB_SQL = 'SELECT matiere_id, GROUP_CONCAT(CONCAT(user_nom," ",user_prenom) SEPARATOR "][") AS coord_liste ';
	$DB_SQL.= 'FROM sacoche_jointure_user_matiere ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'WHERE (matiere_id IN('.$listing_matieres_id.') OR matiere_partage=:partage) AND jointure_coord=:coord AND user_statut=:statut '; // Test matiere car un prof peut être encore relié à des matières décochées par l'admin.
	$DB_SQL.= 'GROUP BY matiere_id';
	$DB_VAR = array(':coord'=>1,':statut'=>1,':partage'=>0);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_parents_par_eleve
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_parents_par_eleve()
{
	$DB_SQL = 'SELECT eleve.user_id AS eleve_id,   eleve.user_sconet_id AS eleve_sconet_id,   eleve.user_nom AS eleve_nom,   eleve.user_prenom AS eleve_prenom,   ';
	$DB_SQL.=        'parent.user_id AS parent_id, parent.user_sconet_id AS parent_sconet_id, parent.user_nom AS parent_nom, parent.user_prenom AS parent_prenom, ';
	$DB_SQL.=        'sacoche_jointure_parent_eleve.resp_legal_num ';
	$DB_SQL.= 'FROM sacoche_user AS eleve ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_parent_eleve ON eleve.user_id=sacoche_jointure_parent_eleve.eleve_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_user AS parent ON sacoche_jointure_parent_eleve.parent_id=parent.user_id ';
	$DB_SQL.= 'WHERE eleve.user_profil="eleve" AND eleve.user_sconet_id!=0 ';
	$DB_SQL.= 'ORDER BY eleve_nom ASC, eleve_prenom ASC, resp_legal_num ASC ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_lister_parents_actifs_avec_infos_for_eleve
 *
 * @param int   $eleve_id
 * @return array
 */
 
function DB_STRUCTURE_lister_parents_actifs_avec_infos_for_eleve($eleve_id)
{
	$DB_SQL = 'SELECT parent.user_id, parent.user_nom, parent.user_prenom, sacoche_parent_adresse.*, resp_legal_num ';
	$DB_SQL.= 'FROM sacoche_user AS eleve ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_parent_eleve ON eleve.user_id=sacoche_jointure_parent_eleve.eleve_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_user AS parent ON sacoche_jointure_parent_eleve.parent_id=parent.user_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_parent_adresse ON sacoche_jointure_parent_eleve.parent_id=sacoche_parent_adresse.parent_id ';
	$DB_SQL.= 'WHERE eleve.user_id=:eleve_id AND parent.user_statut=:statut ';
	$DB_SQL.= 'GROUP BY parent.user_id ';
	$DB_SQL.= 'ORDER BY resp_legal_num ASC ';
	$DB_VAR = array(':eleve_id'=>$eleve_id,':statut'=>1);
	$DB_TAB_parents = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR, TRUE, TRUE);
	if(!count($DB_TAB_parents))
	{
		return array();
	}
	$listing_parent_id = implode(',',array_keys($DB_TAB_parents));
	$DB_SQL = 'SELECT parent_id, GROUP_CONCAT( CONCAT(enfant.user_nom," ",enfant.user_prenom," (resp légal ",resp_legal_num,")") SEPARATOR " ; ") AS enfants_liste ';
	$DB_SQL.= 'FROM sacoche_jointure_parent_eleve ';
	$DB_SQL.= 'LEFT JOIN sacoche_user AS enfant ON sacoche_jointure_parent_eleve.eleve_id=enfant.user_id ';
	$DB_SQL.= 'WHERE sacoche_jointure_parent_eleve.parent_id IN('.$listing_parent_id.') AND enfant.user_statut=:statut ';
	$DB_SQL.= 'GROUP BY parent_id ';
	$DB_VAR = array(':eleve_id'=>$eleve_id,':statut'=>1);
	$DB_TAB_enfants = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR , TRUE , TRUE);
	$DB_TAB = array();
	foreach($DB_TAB_parents AS $id => $tab)
	{
		$DB_TAB[] = array_merge( $DB_TAB_parents[$id] , $DB_TAB_enfants[$id] , array('parent_id'=>$id) );
	}
	return $DB_TAB;
}

/**
 * DB_STRUCTURE_lister_jointure_professeurs_matieres
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_jointure_professeurs_matieres()
{
	$DB_SQL = 'SELECT user_id,matiere_id FROM sacoche_jointure_user_matiere ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'WHERE user_statut=:statut ';
	$DB_VAR = array(':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_jointure_professeurs_coordonnateurs
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_jointure_professeurs_coordonnateurs()
{
	$DB_SQL = 'SELECT user_id,matiere_id FROM sacoche_jointure_user_matiere ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'WHERE jointure_coord=:coord AND user_statut=:statut ';
	$DB_VAR = array(':coord'=>1,':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_jointure_professeurs_principaux
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_jointure_professeurs_principaux()
{
	$DB_SQL = 'SELECT user_id,groupe_id FROM sacoche_jointure_user_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'WHERE jointure_pp=:pp AND user_statut=:statut ';
	$DB_VAR = array(':pp'=>1,':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_jointure_groupe_periode
 *
 * @param string   $listing_groupe_id   id des groupes séparés par des virgules
 * @return array
 */
function DB_STRUCTURE_lister_jointure_groupe_periode($listing_groupe_id)
{
	$DB_SQL = 'SELECT * FROM sacoche_jointure_groupe_periode ';
	$DB_SQL.= 'WHERE groupe_id IN ('.$listing_groupe_id.') ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_lister_jointure_user_entree
 * Au choix à partir : d'une liste d'entrées / d'un domaine / d'un pilier / d'un palier
 *
 * @param string   $listing_eleves   id des élèves séparés par des virgules
 * @param string   $listing_entrees  id des entrées séparées par des virgules
 * @param int      $domaine_id       id d'un domaine
 * @param int      $pilier_id        id d'un pilier
 * @param int      $palier_id        id d'un palier
 * @return array
 */
function DB_STRUCTURE_lister_jointure_user_entree($listing_eleves,$listing_entrees,$domaine_id,$pilier_id,$palier_id)
{
	if($listing_entrees)
	{
		$DB_SQL = 'SELECT sacoche_jointure_user_entree.* ';
		$DB_SQL.= 'FROM sacoche_jointure_user_entree ';
		$DB_SQL.= 'WHERE user_id IN('.$listing_eleves.') AND entree_id IN('.$listing_entrees.') ';
		$DB_VAR = array();
	}
	elseif($domaine_id)
	{
		$DB_SQL = 'SELECT sacoche_jointure_user_entree.* ';
		$DB_SQL.= 'FROM sacoche_socle_section ';
		$DB_SQL.= 'LEFT JOIN sacoche_socle_entree USING (section_id) ';
		$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_entree USING (entree_id) ';
		$DB_SQL.= 'WHERE user_id IN('.$listing_eleves.') AND section_id=:section_id ';
		$DB_VAR = array(':section_id'=>$domaine_id);
	}
	elseif($pilier_id)
	{
		$DB_SQL = 'SELECT sacoche_jointure_user_entree.* ';
		$DB_SQL.= 'FROM sacoche_socle_pilier ';
		$DB_SQL.= 'LEFT JOIN sacoche_socle_section USING (pilier_id) ';
		$DB_SQL.= 'LEFT JOIN sacoche_socle_entree USING (section_id) ';
		$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_entree USING (entree_id) ';
		$DB_SQL.= 'WHERE user_id IN('.$listing_eleves.') AND pilier_id=:pilier_id ';
		$DB_VAR = array(':pilier_id'=>$pilier_id);
	}
	elseif($palier_id)
	{
		$DB_SQL = 'SELECT sacoche_jointure_user_entree.* ';
		$DB_SQL.= 'FROM sacoche_socle_palier ';
		$DB_SQL.= 'LEFT JOIN sacoche_socle_pilier USING (palier_id) ';
		$DB_SQL.= 'LEFT JOIN sacoche_socle_section USING (pilier_id) ';
		$DB_SQL.= 'LEFT JOIN sacoche_socle_entree USING (section_id) ';
		$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_entree USING (entree_id) ';
		$DB_SQL.= 'WHERE user_id IN('.$listing_eleves.') AND palier_id=:palier_id ';
		$DB_VAR = array(':palier_id'=>$palier_id);
	}
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_jointure_user_pilier
 * Au choix à partir : d'une liste de piliers / d'un palier
 *
 * @param string   $listing_eleves   id des élèves séparés par des virgules
 * @param string   $listing_piliers  id des piliers séparées par des virgules
 * @param int      $palier_id        id d'un palier
 * @return array
 */
function DB_STRUCTURE_lister_jointure_user_pilier($listing_eleves,$listing_piliers,$palier_id)
{
	if($palier_id)
	{
		$DB_SQL = 'SELECT sacoche_jointure_user_pilier.* ';
		$DB_SQL.= 'FROM sacoche_socle_palier ';
		$DB_SQL.= 'LEFT JOIN sacoche_socle_pilier USING (palier_id) ';
		$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_pilier USING (pilier_id) ';
		$DB_SQL.= 'WHERE user_id IN('.$listing_eleves.') AND palier_id=:palier_id ';
		$DB_VAR = array(':palier_id'=>$palier_id);
	}
	elseif($listing_piliers)
	{
		$DB_SQL = 'SELECT * ';
		$DB_SQL.= 'FROM sacoche_jointure_user_pilier ';
		$DB_SQL.= 'WHERE user_id IN('.$listing_eleves.') AND pilier_id IN('.$listing_piliers.') ';
		$DB_VAR = array();
	}
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_validations_items
 *
 * @param string   $listing_eleves   id des élèves séparés par des virgules
 * @param bool     $only_positives
 * @return array
 */
function DB_STRUCTURE_lister_validations_items($listing_eleves,$only_positives)
{
	$DB_SQL = 'SELECT palier_id , pilier_id , sacoche_jointure_user_entree.* ';
	$DB_SQL.= 'FROM sacoche_jointure_user_entree ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_entree USING (entree_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_section USING (section_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_pilier USING (pilier_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_palier USING (palier_id) ';
	$DB_SQL.= 'WHERE user_id IN('.$listing_eleves.') ';
	$DB_SQL.= ($only_positives) ? 'AND validation_entree_etat=1 ' : '' ;
	$DB_SQL.= 'ORDER BY palier_ordre, pilier_ordre, section_ordre, entree_ordre ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , NULL);
}

/**
 * DB_STRUCTURE_lister_validations_competences
 *
 * @param string   $listing_eleves   id des élèves séparés par des virgules
 * @param bool   $only_positives
 * @return array
 */
function DB_STRUCTURE_lister_validations_competences($listing_eleves,$only_positives)
{
	$DB_SQL = 'SELECT palier_id , pilier_id , sacoche_jointure_user_pilier.* ';
	$DB_SQL.= 'FROM sacoche_jointure_user_pilier ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_pilier USING (pilier_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_socle_palier USING (palier_id) ';
	$DB_SQL.= 'WHERE user_id IN('.$listing_eleves.') ';
	$DB_SQL.= ($only_positives) ? 'AND validation_pilier_etat=1 ' : '' ;
	$DB_SQL.= 'ORDER BY palier_ordre, pilier_ordre ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , NULL);
}

/**
 * DB_STRUCTURE_lister_users
 *
 * @param string|array   $profil        'eleve' / 'parent' / 'professeur' / 'directeur' / 'administrateur' / ou par exemple array('eleve','professeur','directeur')
 * @param bool           $only_actifs   TRUE pour statut actif uniquement / FALSE pour tout le monde qq soit le statut
 * @param bool           $with_classe   TRUE pour récupérer le nom de la classe de l'élève / FALSE sinon
 * @param bool           $tri_statut    TRUE pour trier par statut décroissant (les actifs en premier), FALSE par défaut
 * @return array
 */
function DB_STRUCTURE_lister_users($profil,$only_actifs,$with_classe,$tri_statut=FALSE)
{
	$DB_VAR = array();
	$left_join = '';
	$where     = '';
	$order_by  = ($tri_statut) ? 'user_statut DESC, ' : '' ;
	if(is_string($profil))
	{
		$where .= 'user_profil=:profil ';
		$DB_VAR[':profil'] = $profil;
	}
	else
	{
		foreach($profil as $key => $val)
		{
			$or = ($key) ? 'OR ' : '( ' ;
			$where .= $or.'user_profil=:profil'.$key.' ';
			$DB_VAR[':profil'.$key] = $val;
		}
		$where .= ') ';
		$order_by .= 'user_profil ASC, ';
	}
	if($with_classe)
	{
		$left_join .= 'LEFT JOIN sacoche_groupe ON sacoche_user.eleve_classe_id=sacoche_groupe.groupe_id ';
		$left_join .= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
		$order_by  .= 'niveau_ordre ASC, groupe_ref ASC, ';
	}
	if($only_actifs)
	{
		$where .= 'AND user_statut=:statut ';
		$DB_VAR[':statut'] = 1;
	}
	// On peut maintenant assembler les morceaux de la requête !
	$DB_SQL = 'SELECT * FROM sacoche_user ';
	$DB_SQL.= $left_join;
	$DB_SQL.= 'WHERE '.$where;
	$DB_SQL.= 'ORDER BY '.$order_by.'user_nom ASC, user_prenom ASC ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_parents_actifs_avec_infos_enfants
 *
 * @param bool     $with_adresse
 * @param string   $debut_nom      premières lettres du nom
 * @param string   $debut_prenom   premières lettres du prénom
 * @return array
 */
function DB_STRUCTURE_lister_parents_actifs_avec_infos_enfants($with_adresse,$debut_nom='',$debut_prenom='')
{
	$DB_SQL = 'SELECT ' ;
	$DB_SQL.= ($with_adresse) ? 'parent.user_id, parent.user_nom, parent.user_prenom, sacoche_parent_adresse.*, ' : 'parent.*, ' ;
	$DB_SQL.= 'GROUP_CONCAT( CONCAT(eleve.user_nom," ",eleve.user_prenom," (resp légal ",resp_legal_num,")") SEPARATOR "§BR§") AS enfants_liste, ';
	$DB_SQL.= 'COUNT(eleve.user_id) AS enfants_nombre ';
	$DB_SQL.= 'FROM sacoche_user AS parent ';
	$DB_SQL.= ($with_adresse) ? 'LEFT JOIN sacoche_parent_adresse ON parent.user_id=sacoche_parent_adresse.parent_id ' : '' ;
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_parent_eleve ON parent.user_id=sacoche_jointure_parent_eleve.parent_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_user AS eleve ON sacoche_jointure_parent_eleve.eleve_id=eleve.user_id ';
	$DB_SQL.= 'WHERE parent.user_profil=:profil AND parent.user_statut=:statut ';
	$DB_VAR = array(':profil'=>'parent',':statut'=>1);
	if($debut_nom)
	{
		$DB_SQL .= 'AND parent.user_nom LIKE :nom ';
		$DB_VAR[':nom'] = $debut_nom.'%';
	}
	if($debut_prenom)
	{
		$DB_SQL .= 'AND parent.user_prenom LIKE :prenom ';
		$DB_VAR[':prenom'] = $debut_prenom.'%';
	}
	$DB_SQL.= 'GROUP BY parent.user_id ';
	$DB_SQL.= 'ORDER BY parent.user_nom ASC, parent.user_prenom ASC ';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_users_avec_groupe
 *
 * @param bool   $profil_eleve  true pour eleve / false pour professeur + directeur
 * @param int    $prof_id       0 pour les élèves des groupes type "groupe" , l'id du prof pour les élèves des groupes type "besoin" d'un prof
 * @param bool   $only_actifs   true pour statut actif uniquement / false pour tout le monde qq soit le statut
 * @return array
 */
function DB_STRUCTURE_lister_users_avec_groupe($profil_eleve,$prof_id,$only_actifs)
{
	$groupe_type = ($prof_id) ? 'besoin' : 'groupe' ;
	$egal_eleve = $profil_eleve ? '=' : '!=' ;
	$DB_VAR = array(':profil'=>'eleve',':type'=>$groupe_type);
	$DB_SQL = 'SELECT * FROM sacoche_user ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'WHERE user_profil'.$egal_eleve.':profil AND groupe_type=:type ';
	if($prof_id)
	{
		$DB_SQL.= 'AND groupe_prof_id=:prof_id ';
		$DB_VAR[':prof_id'] = $prof_id;
	}
	if($only_actifs)
	{
		$DB_SQL.= 'AND user_statut=:statut ';
		$DB_VAR[':statut'] = 1;
	}
	$DB_SQL.= 'ORDER BY user_nom ASC, user_prenom ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_users_desactives_obsoletes
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_users_desactives_obsoletes()
{
	$DB_SQL = 'SELECT user_id, user_profil ';
	$DB_SQL.= 'FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_statut=:user_statut AND DATE_ADD(user_statut_date,INTERVAL 3 YEAR)<NOW() ';
	$DB_VAR = array(':user_statut'=>0);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_professeurs_avec_classes
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_professeurs_avec_classes()
{
	$DB_SQL = 'SELECT * FROM sacoche_user ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'WHERE user_profil=:profil AND groupe_type=:type AND user_statut=:statut ';
	$DB_VAR = array(':profil'=>'professeur',':type'=>'classe',':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_users_actifs_regroupement
 *
 * @param string $profil        eleve | parent | professeur | directeur
 * @param string $groupe_type   all | sdf | niveau | classe | groupe | besoin
 * @param int    $groupe_id     id du niveau ou de la classe ou du groupe
 * @param string $champs        par défaut user_id,user_nom,user_prenom
 * @return array
 */
function DB_STRUCTURE_lister_users_actifs_regroupement($profil,$groupe_type,$groupe_id,$champs='user_id,user_nom,user_prenom')
{
	$DB_VAR  = array( ':profil'=>str_replace('parent','eleve',$profil) , ':user_statut'=>1 ) ;
	$as      = ($profil!='parent') ? '' : ' AS enfant' ;
	$prefixe = ($profil!='parent') ? 'sacoche_user.' : 'enfant.' ;
	$from  = 'FROM sacoche_user'.$as.' ' ; // Peut être modifié ensuite (requête optimisée si on commence par une autre table)
	$ljoin = '';
	$where = 'WHERE '.$prefixe.'user_profil=:profil AND '.$prefixe.'user_statut=:user_statut ';
	$group = ($profil!='parent') ? 'GROUP BY user_id ' : 'GROUP BY parent.user_id ' ;
	if($profil!='directeur') // Restreindre pour un directeur n'a pas de sens
	{
		switch ($groupe_type)
		{
			case 'all' :	// On veut tous les users de l'établissement
				break;
			case 'sdf' :	// On veut les users non affectés dans une classe (élèves seulements)
				$where .= 'AND '.$prefixe.'eleve_classe_id=:classe ';
				$DB_VAR[':classe'] = 0;
				break;
			case 'niveau' :	// On veut tous les users d'un niveau
				switch ($profil)
				{
					case 'eleve' :
					case 'parent' :
						$from   = 'FROM sacoche_groupe ';
						$ljoin .= 'LEFT JOIN sacoche_user'.$as.' ON sacoche_groupe.groupe_id='.$prefixe.'eleve_classe_id ';
						break;
					case 'professeur' :
						$from   = 'FROM sacoche_groupe ';
						$ljoin .= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
						$ljoin .= 'LEFT JOIN sacoche_user USING (user_id) ';
						break;
				}
				$where .= 'AND niveau_id=:niveau ';
				$DB_VAR[':niveau'] = $groupe_id;
				break;
			case 'classe' :	// On veut tous les users d'une classe
				switch ($profil)
				{
					case 'eleve' :
					case 'parent' :
						$where .= 'AND '.$prefixe.'eleve_classe_id=:groupe ';
						break;
					case 'professeur' :
						$from   = 'FROM sacoche_jointure_user_groupe ';
						$ljoin .= 'LEFT JOIN sacoche_user USING (user_id) ';
						$where .= 'AND groupe_id=:groupe ';
						break;
				}
				$DB_VAR[':groupe'] = $groupe_id;
				break;
			case 'groupe' :	// On veut tous les users d'un groupe
			case 'besoin' :	// On veut tous les users d'un groupe de besoin (élèves | parents seulements)
			case 'eval'   :	// On veut tous les users d'un groupe utilisé pour une évaluation (élèves seulements)
				switch ($profil)
				{
					case 'eleve' :
					case 'parent' :
					case 'professeur' :
						$from   = 'FROM sacoche_jointure_user_groupe ';
						$ljoin .= 'LEFT JOIN sacoche_user'.$as.' USING (user_id) ';
						$where .= 'AND groupe_id=:groupe ';
						break;
				}
				$DB_VAR[':groupe'] = $groupe_id;
				break;
		}
	}
	if($profil=='parent')
	{
		// INNER JOIN pour obliger une jointure avec un parent
		$ljoin .= 'INNER JOIN sacoche_jointure_parent_eleve ON enfant.user_id=sacoche_jointure_parent_eleve.eleve_id ';
		$ljoin .= 'INNER JOIN sacoche_user AS parent ON sacoche_jointure_parent_eleve.parent_id=parent.user_id ';
		$where .= 'AND parent.user_statut=:user_statut ';
	}
	// On peut maintenant assembler les morceaux de la requête !
	$DB_SQL = 'SELECT '.$champs.' '.$from.$ljoin.$where.$group.'ORDER BY '.$prefixe.'user_nom ASC, '.$prefixe.'user_prenom ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_demandes_prof
 *
 * @param int    $matiere_id        id de la matière du prof
 * @param int    $listing_user_id   id des élèves du prof séparés par des virgules
 * @return array
 */
function DB_STRUCTURE_lister_demandes_prof($matiere_id,$listing_user_id)
{
	$DB_SQL = 'SELECT sacoche_demande.*, ';
	$DB_SQL.= 'CONCAT(niveau_ref,".",domaine_ref,theme_ordre,item_ordre) AS item_ref , ';
	$DB_SQL.= 'item_nom, user_nom, user_prenom ';
	$DB_SQL.= 'FROM sacoche_demande ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'WHERE user_id IN('.$listing_user_id.') AND sacoche_demande.matiere_id=:matiere_id ';
	$DB_SQL.= 'ORDER BY niveau_ref ASC, domaine_ref ASC, theme_ordre ASC, item_ordre ASC';
	$DB_VAR = array(':matiere_id'=>$matiere_id);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_demandes_eleve
 *
 * @param int    $user_id   id de l'élève
 * @return array
 */
function DB_STRUCTURE_lister_demandes_eleve($user_id)
{
	$DB_SQL = 'SELECT sacoche_demande.*, ';
	$DB_SQL.= 'CONCAT(niveau_ref,".",domaine_ref,theme_ordre,item_ordre) AS item_ref , ';
	$DB_SQL.= 'item_id , item_nom , item_lien , sacoche_matiere.matiere_id AS matiere_id  , matiere_nom ';
	$DB_SQL.= 'FROM sacoche_demande ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere ON sacoche_referentiel_domaine.matiere_id=sacoche_matiere.matiere_id ';
	$DB_SQL.= 'WHERE user_id=:user_id ';
	$DB_SQL.= 'ORDER BY sacoche_demande.matiere_id ASC, niveau_ref ASC, domaine_ref ASC, theme_ordre ASC, item_ordre ASC';
	$DB_VAR = array(':user_id'=>$user_id);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_devoirs_prof
 *
 * @param int    $prof_id
 * @param int    $groupe_id        id du groupe ou de la classe pour un devoir sur une classe ou un groupe ; 0 pour un devoir sur une sélection d'élèves
 * @param string $date_debut_mysql
 * @param string $date_fin_mysql
 * @return array
 */
function DB_STRUCTURE_lister_devoirs_prof($prof_id,$groupe_id,$date_debut_mysql,$date_fin_mysql)
{
	// DB::query(SACOCHE_STRUCTURE_BD_NAME , 'SET group_concat_max_len = ...'); // Pour lever si besoin une limitation de GROUP_CONCAT (group_concat_max_len est par défaut limité à une chaine de 1024 caractères).
	// Il faut ajouter dans la requête des "DISTINCT" sinon la liaison avec "sacoche_jointure_user_groupe" duplique tout x le nb d'élèves associés pour une évaluation sur une sélection d'élèves.
	$DB_SQL = 'SELECT devoir_id, devoir_date, devoir_visible_date, devoir_info, groupe_id, groupe_type, groupe_nom, ';
	$DB_SQL.= 'GROUP_CONCAT(DISTINCT item_id SEPARATOR "_") AS items_listing, COUNT(DISTINCT item_id) AS items_nombre ';
	if(!$groupe_id)
	{
		$DB_SQL .= ', '.'GROUP_CONCAT(DISTINCT user_id SEPARATOR "_") AS users_listing, COUNT(DISTINCT user_id) AS users_nombre ';
	}
	$DB_SQL.= 'FROM sacoche_devoir ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_devoir_item USING (devoir_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	if(!$groupe_id)
	{
		$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
	}
	$DB_SQL.= 'WHERE prof_id=:prof_id ';
	$DB_SQL.= ($groupe_id) ? 'AND groupe_type!=:type4 AND groupe_id='.$groupe_id.' ' : 'AND groupe_type=:type4 ' ;
	$DB_SQL.= 'AND devoir_date>="'.$date_debut_mysql.'" AND devoir_date<="'.$date_fin_mysql.'" ' ;
	$DB_SQL.= 'GROUP BY devoir_id ';
	$DB_SQL.= 'ORDER BY devoir_date DESC, groupe_nom ASC';
	$DB_VAR = array(':prof_id'=>$prof_id,':type4'=>'eval');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_devoirs_eleve
 *
 * @param int    $eleve_id
 * @param int    $classe_id   id de la classe de l'élève ; en effet sacoche_jointure_user_groupe ne contient que les liens aux groupes, donc il faut tester aussi la classe
 * @param string $date_debut_mysql
 * @param string $date_fin_mysql
 * @return array
 */
function DB_STRUCTURE_lister_devoirs_eleve($eleve_id,$classe_id,$date_debut_mysql,$date_fin_mysql)
{
	$where_classe = ($classe_id) ? 'sacoche_devoir.groupe_id='.$classe_id.' OR ' : '';
	$DB_SQL = 'SELECT sacoche_devoir.* , sacoche_user.user_nom AS prof_nom , sacoche_user.user_prenom AS prof_prenom ';
	$DB_SQL.= 'FROM sacoche_devoir ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_user ON sacoche_devoir.prof_id=sacoche_user.user_id ';
	$DB_SQL.= 'WHERE ('.$where_classe.'sacoche_jointure_user_groupe.user_id=:eleve_id) ';
	$DB_SQL.= 'AND devoir_date>="'.$date_debut_mysql.'" AND devoir_date<="'.$date_fin_mysql.'" AND devoir_visible_date<=NOW() ' ;
	$DB_SQL.= 'GROUP BY devoir_id ';
	$DB_SQL.= 'ORDER BY devoir_date DESC ';
	$DB_VAR = array(':eleve_id'=>$eleve_id);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_items_devoir
 * Retourner les items d'un devoir et les infos associées (tableau issu de la requête SQL)
 * Dans le cas où $info_pour_eleve=TRUE, en plus d'informations supplémentaires retournées, les clefs du tableau sont les item_id car on en a besoin.
 *
 * @param int  $devoir_id
 * @param bool $info_pour_eleve   facultatif ; pour un élève, qui liste ses notes d'une éval, il faut en particulier pouvoir ensuite lui calculer son score
 * @return array
 */
function DB_STRUCTURE_lister_items_devoir($devoir_id,$info_pour_eleve=false)
{
	$select   = ($info_pour_eleve) ? 'item_cart, item_lien, matiere_id, referentiel_calcul_methode, referentiel_calcul_limite, ' : '' ;
	$leftjoin = ($info_pour_eleve) ? 'LEFT JOIN sacoche_referentiel USING (matiere_id,niveau_id) ' : '' ;
	$DB_SQL = 'SELECT ';
	$DB_SQL.= 'item_id, item_nom, entree_id, ';
	$DB_SQL.= $select;
	$DB_SQL.= 'CONCAT(matiere_ref,".",niveau_ref,".",domaine_ref,theme_ordre,item_ordre) AS item_ref ';
	$DB_SQL.= 'FROM sacoche_jointure_devoir_item ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= $leftjoin;
	$DB_SQL.= 'WHERE devoir_id=:devoir_id ';
	$DB_SQL.= 'ORDER BY jointure_ordre ASC, matiere_ref ASC, niveau_ordre ASC, domaine_ordre ASC, theme_ordre ASC, item_ordre ASC';
	$DB_VAR = array(':devoir_id'=>$devoir_id);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR , $info_pour_eleve);
}

/**
 * DB_STRUCTURE_lister_saisies_devoir
 *
 * @param int   $devoir_id
 * @param bool  $with_REQ   // Avec ou sans les repères de demandes d'évaluations
 * @return array
 */
function DB_STRUCTURE_lister_saisies_devoir($devoir_id,$with_REQ)
{
	// On évite les élèves désactivés pour ces opérations effectuées sur les pages de saisies d'évaluations
	$DB_SQL = 'SELECT eleve_id,item_id,saisie_note FROM sacoche_saisie ';
	$DB_SQL.= 'LEFT JOIN sacoche_user ON sacoche_saisie.eleve_id=sacoche_user.user_id ';
	$DB_SQL.= 'WHERE devoir_id=:devoir_id AND user_statut=:statut ';
	if(!$with_REQ)
	{
		$DB_SQL.= 'AND saisie_note!="REQ" ';
	}
	$DB_VAR = array(':devoir_id'=>$devoir_id,':statut'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_saisies_devoir_eleve
 *
 * @param int   $devoir_id
 * @param int   $eleve_id
 * @return array
 */
function DB_STRUCTURE_lister_saisies_devoir_eleve($devoir_id,$eleve_id)
{
	$DB_SQL = 'SELECT item_id,saisie_note FROM sacoche_saisie ';
	$DB_SQL.= 'WHERE devoir_id=:devoir_id AND eleve_id=:eleve_id AND saisie_note!="REQ" ';
	$DB_VAR = array(':devoir_id'=>$devoir_id,':eleve_id'=>$eleve_id);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_referentiels
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_lister_referentiels()
{
	$DB_SQL = 'SELECT matiere_id,niveau_id,matiere_nom,niveau_nom,referentiel_mode_synthese ';
	$DB_SQL.= 'FROM sacoche_referentiel ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE (matiere_id IN('.$_SESSION['MATIERES'].') OR matiere_partage=:partage) AND niveau_id IN('.$_SESSION['CYCLES'].','.$_SESSION['NIVEAUX'].') '; // Test matiere pour éviter des matières décochées par l'admin.
	$DB_SQL.= 'ORDER BY matiere_nom ASC, niveau_ordre ASC ';
	$DB_VAR = array(':partage'=>0);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_referentiels_infos_details_matieres_niveaux
 *
 * @param string   $listing_matieres_id   id des matières séparés par des virgules
 * @param string   $listing_niveaux_id    id des niveaux séparés par des virgules ; ne peut pas être vide
 * @param string   $listing_cycles_id     id des cycles séparés par des virgules ; false pour ne pas retourner les cycles
 * @return array
 */
function DB_STRUCTURE_lister_referentiels_infos_details_matieres_niveaux($listing_matieres_id,$listing_niveaux_id,$listing_cycles_id)
{
	$listing_cycles_niveaux = ($listing_cycles_id) ? $listing_niveaux_id.','.$listing_cycles_id : $listing_niveaux_id ;
	$DB_SQL = 'SELECT matiere_id,niveau_id,niveau_nom,referentiel_partage_etat,referentiel_partage_date,referentiel_calcul_methode,referentiel_calcul_limite ';
	$DB_SQL.= 'FROM sacoche_referentiel ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'WHERE (matiere_id IN('.$listing_matieres_id.') OR matiere_partage=:partage) AND niveau_id IN('.$listing_cycles_niveaux.') '; // Test matiere car un prof peut être encore relié à des matières décochées par l'admin.
	$DB_SQL.= 'ORDER BY matiere_id ASC, niveau_ordre ASC';
	$DB_VAR = array(':partage'=>0);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_lister_referentiels_infos_groupement_matieres
 *
 * @param string   $listing_matieres_id   id des matières séparés par des virgules
 * @param string   $listing_niveaux_id    id des niveaux séparés par des virgules ; ne peut pas être vide
 * @param string   $listing_cycles_id     id des cycles séparés par des virgules ; false pour ne pas retourner les cycles
 * @return array
 */
function DB_STRUCTURE_lister_referentiels_infos_groupement_matieres($listing_matieres_id,$listing_niveaux_id,$listing_cycles_id)
{
	$listing_cycles_niveaux = ($listing_cycles_id) ? $listing_niveaux_id.','.$listing_cycles_id : $listing_niveaux_id ;
	$DB_SQL = 'SELECT matiere_id,COUNT(niveau_id) AS niveau_nb ';
	$DB_SQL.= 'FROM sacoche_referentiel ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'WHERE matiere_id IN('.$listing_matieres_id.') AND niveau_id IN('.$listing_cycles_niveaux.') '; // Rechercher exclusivement parmi les matières transmises sans chercher à en ajouter d'autres (matières spécifiques)
	$DB_SQL.= 'GROUP BY matiere_id ';
	$DB_SQL.= 'ORDER BY matiere_id ASC';
	$DB_VAR = array(':partage'=>0);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_compter_devoirs
 *
 * @param void
 * @return int
 */
function DB_STRUCTURE_compter_devoirs()
{
	$DB_SQL = 'SELECT COUNT(*) AS nombre FROM sacoche_devoir';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_compter_demandes_formulees_eleve_matiere
 *
 * @param int   $eleve_id
 * @param int   $matiere_id
 * @return int
 */
function DB_STRUCTURE_compter_demandes_formulees_eleve_matiere($eleve_id,$matiere_id)
{
	$DB_SQL = 'SELECT COUNT(*) AS nombre ';
	$DB_SQL.= 'FROM sacoche_demande ';
	$DB_SQL.= 'WHERE user_id=:eleve_id AND matiere_id=:matiere_id ';
	$DB_SQL.= 'GROUP BY matiere_id';
	$DB_VAR = array(':eleve_id'=>$eleve_id,':matiere_id'=>$matiere_id);
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_compter_saisies_prof_classe
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_compter_saisies_prof_classe()
{
	$DB_SQL = 'SELECT CONCAT(user_nom," ",user_prenom) AS professeur, groupe_nom, COUNT(saisie_note) AS nombre ';
	$DB_SQL.= 'FROM sacoche_user ';
	$DB_SQL.= 'LEFT JOIN sacoche_devoir ON sacoche_user.user_id=sacoche_devoir.prof_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_saisie USING (devoir_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'WHERE user_profil=:user_profil AND groupe_type=:groupe_type ';
	$DB_SQL.= 'GROUP BY user_id,groupe_id';
	$DB_VAR = array(':user_profil'=>'professeur',':groupe_type'=>'classe');
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_compter_users_suivant_statut
 *
 * @param string|array   $profil        'eleve' / 'professeur' / 'directeur' / 'administrateur' / ou par exemple array('eleve','professeur','directeur')
 * @return array   [0]=>nb actifs , [1]=>nb inactifs
 */
function DB_STRUCTURE_compter_users_suivant_statut($profil)
{
	if(is_string($profil))
	{
		$where = 'user_profil=:profil ';
		$DB_VAR[':profil'] = $profil;
	}
	else
	{
		$DB_VAR = array();
		foreach($profil as $key => $val)
		{
			$DB_VAR[':profil'.$key] = $val;
			$profil[$key] = ':profil'.$key;
		}
		$where = 'user_profil IN('.implode(',',$profil).') ';
	}
	$DB_SQL = 'SELECT user_statut, COUNT(*) AS nombre FROM sacoche_user ';
	$DB_SQL.= 'WHERE '.$where;
	$DB_SQL.= 'GROUP BY user_statut';
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR , TRUE , TRUE);
	$nb_actif   = ( (count($DB_TAB)) && (isset($DB_TAB[1])) ) ? $DB_TAB[1]['nombre'] : 0 ;
	$nb_inactif = ( (count($DB_TAB)) && (isset($DB_TAB[0])) ) ? $DB_TAB[0]['nombre'] : 0 ;
	return array($nb_actif,$nb_inactif);
}

/**
 * DB_STRUCTURE_compter_eleves_actifs_sans_id_sconet
 *
 * @param void
 * @return int
 */
function DB_STRUCTURE_compter_eleves_actifs_sans_id_sconet()
{
	$DB_SQL = 'SELECT COUNT(*) AS nombre FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:statut AND user_sconet_id=:sconet_id ';
	$DB_VAR = array(':profil'=>'eleve',':statut'=>1,':sconet_id'=>0);
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_compter_modes_synthese_inconnu
 *
 * @param void
 * @return int
 */
function DB_STRUCTURE_compter_modes_synthese_inconnu()
{
	$DB_SQL = 'SELECT COUNT(*) AS nombre FROM sacoche_referentiel ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'WHERE referentiel_mode_synthese=:mode_inconnu AND (matiere_id IN('.$_SESSION['MATIERES'].') OR matiere_partage=:partage) '; // Test matiere pour éviter des matières décochées par l'admin.
	$DB_VAR = array(':mode_inconnu'=>'inconnu',':partage'=>0);
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_referentiel
 *
 * @param int    $matiere_id
 * @param int    $niveau_id
 * @return int
 */
function DB_STRUCTURE_tester_referentiel($matiere_id,$niveau_id)
{
	$DB_SQL = 'SELECT matiere_id FROM sacoche_referentiel ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id AND niveau_id=:niveau_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':matiere_id'=>$matiere_id,':niveau_id'=>$niveau_id);
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_prof_principal
 *
 * @param int $user_id
 * @return int
 */
function DB_STRUCTURE_tester_prof_principal($user_id)
{
	$DB_SQL = 'SELECT groupe_id FROM sacoche_jointure_user_groupe ';
	$DB_SQL.= 'WHERE user_id=:user_id AND jointure_pp=:pp ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':user_id'=>$user_id,':pp'=>1);
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_demande_existante
 *
 * @param int    $eleve_id
 * @param int    $matiere_id
 * @param int    $item_id
 * @return int
 */
function DB_STRUCTURE_tester_demande_existante($eleve_id,$matiere_id,$item_id)
{
	$DB_SQL = 'SELECT demande_id FROM sacoche_demande ';
	$DB_SQL.= 'WHERE user_id=:eleve_id AND matiere_id=:matiere_id AND item_id=:item_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':eleve_id'=>$eleve_id,':matiere_id'=>$matiere_id,':item_id'=>$item_id);
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_matiere_reference
 *
 * @param string $matiere_ref
 * @param int    $matiere_id    inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_matiere_reference($matiere_ref,$matiere_id=false)
{
	$DB_SQL = 'SELECT matiere_id FROM sacoche_matiere ';
	$DB_SQL.= 'WHERE matiere_ref=:matiere_ref ';
	$DB_VAR = array(':matiere_ref'=>$matiere_ref);
	if($matiere_id)
	{
		$DB_SQL.= 'AND matiere_id!=:matiere_id ';
		$DB_VAR[':matiere_id'] = $matiere_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_classe_reference
 *
 * @param string $groupe_ref
 * @param int    $groupe_id    inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_classe_reference($groupe_ref,$groupe_id=false)
{
	$DB_SQL = 'SELECT groupe_id FROM sacoche_groupe ';
	$DB_SQL.= 'WHERE groupe_type=:groupe_type AND groupe_ref=:groupe_ref ';
	$DB_VAR = array(':groupe_type'=>'classe',':groupe_ref'=>$groupe_ref);
	if($groupe_id)
	{
		$DB_SQL.= 'AND groupe_id!=:groupe_id ';
		$DB_VAR[':groupe_id'] = $groupe_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_groupe_reference
 *
 * @param string $groupe_ref
 * @param int    $groupe_id    inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_groupe_reference($groupe_ref,$groupe_id=false)
{
	$DB_SQL = 'SELECT groupe_id FROM sacoche_groupe ';
	$DB_SQL.= 'WHERE groupe_type=:groupe_type AND groupe_ref=:groupe_ref ';
	$DB_VAR = array(':groupe_type'=>'groupe',':groupe_ref'=>$groupe_ref);
	if($groupe_id)
	{
		$DB_SQL.= 'AND groupe_id!=:groupe_id ';
		$DB_VAR[':groupe_id'] = $groupe_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_groupe_nom
 *
 * @param string $groupe_nom
 * @param int    $groupe_id    inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_groupe_nom($groupe_nom,$groupe_id=false)
{
	$DB_SQL = 'SELECT groupe_id FROM sacoche_groupe ';
	$DB_SQL.= 'WHERE groupe_type=:groupe_type AND groupe_nom=:groupe_nom ';
	$DB_VAR = array(':groupe_type'=>'groupe',':groupe_nom'=>$groupe_nom);
	if($groupe_id)
	{
		$DB_SQL.= 'AND groupe_id!=:groupe_id ';
		$DB_VAR[':groupe_id'] = $groupe_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_periode_nom
 *
 * @param string $periode_nom
 * @param int    $periode_id    inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_periode_nom($periode_nom,$periode_id=false)
{
	$DB_SQL = 'SELECT periode_id FROM sacoche_periode ';
	$DB_SQL.= 'WHERE periode_nom=:periode_nom ';
	$DB_VAR = array(':periode_nom'=>$periode_nom);
	if($periode_id)
	{
		$DB_SQL.= 'AND periode_id!=:periode_id ';
		$DB_VAR[':periode_id'] = $periode_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_utilisateur_idENT (parmi tout le personnel de l'établissement, sauf éventuellement l'utilisateur concerné)
 *
 * @param string $user_id_ent
 * @param int    $user_id       inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_utilisateur_idENT($user_id_ent,$user_id=false)
{
	$DB_SQL = 'SELECT user_id FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_id_ent=:user_id_ent ';
	$DB_VAR = array(':user_id_ent'=>$user_id_ent);
	if($user_id)
	{
		$DB_SQL.= 'AND user_id!=:user_id ';
		$DB_VAR[':user_id'] = $user_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_utilisateur_idGepi (parmi tout le personnel de l'établissement, sauf éventuellement l'utilisateur concerné)
 *
 * @param string $user_id_gepi
 * @param int    $user_id       inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_utilisateur_idGepi($user_id_gepi,$user_id=false)
{
	$DB_SQL = 'SELECT user_id FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_id_gepi=:user_id_gepi ';
	$DB_VAR = array(':user_id_gepi'=>$user_id_gepi);
	if($user_id)
	{
		$DB_SQL.= 'AND user_id!=:user_id ';
		$DB_VAR[':user_id'] = $user_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_utilisateur_SconetId (parmi tout le personnel de l'établissement de même profil, sauf éventuellement l'utilisateur concerné)
 *
 * @param int    $user_sconet_id
 * @param string $user_profil
 * @param int    $user_id       inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_utilisateur_SconetId($user_sconet_id,$user_profil,$user_id=false)
{
	$DB_SQL = 'SELECT user_id FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_sconet_id=:user_sconet_id AND user_profil=:user_profil ';
	$DB_VAR = array(':user_sconet_id'=>$user_sconet_id,':user_profil'=>$user_profil);
	if($user_id)
	{
		$DB_SQL.= 'AND user_id!=:user_id ';
		$DB_VAR[':user_id'] = $user_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_utilisateur_SconetElenoet (parmi tous les élèves, sauf éventuellement l'utilisateur concerné)
 *
 * @param int    $user_sconet_elenoet
 * @param int    $user_id       inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_utilisateur_SconetElenoet($user_sconet_elenoet,$user_id=false)
{
	$DB_SQL = 'SELECT user_id FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_sconet_elenoet=:user_sconet_elenoet AND user_profil=:user_profil ';
	$DB_VAR = array(':user_sconet_elenoet'=>$user_sconet_elenoet,':user_profil'=>'eleve');
	if($user_id)
	{
		$DB_SQL.= 'AND user_id!=:user_id ';
		$DB_VAR[':user_id'] = $user_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_utilisateur_reference (parmi tout le personnel de l'établissement de même profil, sauf éventuellement l'utilisateur concerné)
 *
 * @param string $user_reference
 * @param string $user_profil
 * @param int    $user_id       inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_utilisateur_reference($user_reference,$user_profil,$user_id=false)
{
	$DB_SQL = 'SELECT user_id FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_reference=:user_reference AND user_profil=:user_profil ';
	$DB_VAR = array(':user_reference'=>$user_reference,':user_profil'=>$user_profil);
	if($user_id)
	{
		$DB_SQL.= 'AND user_id!=:user_id ';
		$DB_VAR[':user_id'] = $user_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_tester_login (parmi tout le personnel de l'établissement)
 *
 * @param string $user_login
 * @param int    $user_id     inutile si recherche pour un ajout, mais id à éviter si recherche pour une modification
 * @return int
 */
function DB_STRUCTURE_tester_login($user_login,$user_id=false)
{
	$DB_SQL = 'SELECT user_id FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_login=:user_login ';
	$DB_VAR = array(':user_login'=>$user_login);
	if($user_id)
	{
		$DB_SQL.= 'AND user_id!=:user_id ';
		$DB_VAR[':user_id'] = $user_id;
	}
	$DB_SQL.= 'LIMIT 1';
	return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_rechercher_login_disponible (parmi tout le personnel de l'établissement)
 *
 * @param string $login
 * @return string
 */
function DB_STRUCTURE_rechercher_login_disponible($login)
{
	$nb_chiffres = 20-mb_strlen($login);
	$max_result = 0;
	do
	{
		$login = mb_substr($login,0,20-$nb_chiffres);
		$DB_SQL = 'SELECT user_login FROM sacoche_user ';
		$DB_SQL.= 'WHERE user_login LIKE :user_login';
		$DB_VAR = array(':user_login'=>$login.'%');
		$DB_COL = DB::queryCol(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$max_result += pow(10,$nb_chiffres);
	}
	while (count($DB_COL)>=$max_result);
	$j=0;
	do
	{
		$j++;
	}
	while (in_array($login.$j,$DB_COL));
	return $login.$j ;
}

/**
 * DB_STRUCTURE_ajouter_matiere_specifique
 *
 * @param string $matiere_ref
 * @param string $matiere_nom
 * @return int
 */
function DB_STRUCTURE_ajouter_matiere_specifique($matiere_ref,$matiere_nom)
{
	$DB_SQL = 'INSERT INTO sacoche_matiere(matiere_partage,matiere_transversal,matiere_nb_demandes,matiere_ref,matiere_nom) ';
	$DB_SQL.= 'VALUES(:matiere_partage,:matiere_transversal,:matiere_nb_demandes,:matiere_ref,:matiere_nom)';
	$DB_VAR = array(':matiere_partage'=>0,':matiere_transversal'=>0,':matiere_nb_demandes'=>0,':matiere_ref'=>$matiere_ref,':matiere_nom'=>$matiere_nom);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return DB::getLastOid(SACOCHE_STRUCTURE_BD_NAME);
}

/**
 * DB_STRUCTURE_ajouter_groupe
 *
 * @param string $groupe_type      'classe' ou 'groupe' ou 'besoin' ou 'eval'
 * @param int    $groupe_prof_id   id du prof dans le cas d'un groupe de besoin ou pour une évaluation (0 sinon)
 * @param string $groupe_ref
 * @param string $groupe_nom
 * @param int    $niveau_id
 * @return int
 */
function DB_STRUCTURE_ajouter_groupe($groupe_type,$groupe_prof_id,$groupe_ref,$groupe_nom,$niveau_id)
{
	$DB_SQL = 'INSERT INTO sacoche_groupe(groupe_type,groupe_prof_id,groupe_ref,groupe_nom,niveau_id) ';
	$DB_SQL.= 'VALUES(:groupe_type,:groupe_prof_id,:groupe_ref,:groupe_nom,:niveau_id)';
	$DB_VAR = array(':groupe_type'=>$groupe_type,':groupe_prof_id'=>$groupe_prof_id,':groupe_ref'=>$groupe_ref,':groupe_nom'=>$groupe_nom,':niveau_id'=>$niveau_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return DB::getLastOid(SACOCHE_STRUCTURE_BD_NAME);
}

/**
 * DB_STRUCTURE_ajouter_periode
 *
 * @param int    $periode_ordre
 * @param string $periode_nom
 * @return int
 */
function DB_STRUCTURE_ajouter_periode($periode_ordre,$periode_nom)
{
	$DB_SQL = 'INSERT INTO sacoche_periode(periode_ordre,periode_nom) ';
	$DB_SQL.= 'VALUES(:periode_ordre,:periode_nom)';
	$DB_VAR = array(':periode_ordre'=>$periode_ordre,':periode_nom'=>$periode_nom);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return DB::getLastOid(SACOCHE_STRUCTURE_BD_NAME);
}

/**
 * DB_STRUCTURE_ajouter_utilisateur
 *
 * @param string $user_sconet_id
 * @param string $user_sconet_elenoet
 * @param string $user_reference
 * @param string $user_profil
 * @param string $user_nom
 * @param string $user_prenom
 * @param string $user_login
 * @param string $user_password
 * @param int    $eleve_classe_id   facultatif, 0 si pas de classe ou profil non élève
 * @param string $user_id_ent       facultatif
 * @param string $user_id_gepi      facultatif
 * @return int
 */
function DB_STRUCTURE_ajouter_utilisateur($user_sconet_id,$user_sconet_elenoet,$user_reference,$user_profil,$user_nom,$user_prenom,$user_login,$user_password,$eleve_classe_id=0,$user_id_ent='',$user_id_gepi='')
{
	$password_crypte = crypter_mdp($user_password);
	$DB_SQL = 'INSERT INTO sacoche_user(user_sconet_id,user_sconet_elenoet,user_reference,user_profil,user_nom,user_prenom,user_login,user_password,eleve_classe_id,user_statut_date,user_id_ent,user_id_gepi) ';
	$DB_SQL.= 'VALUES(:user_sconet_id,:user_sconet_elenoet,:user_reference,:user_profil,:user_nom,:user_prenom,:user_login,:password_crypte,:eleve_classe_id,NOW(),:user_id_ent,:user_id_gepi)';
	$DB_VAR = array(':user_sconet_id'=>$user_sconet_id,':user_sconet_elenoet'=>$user_sconet_elenoet,':user_reference'=>$user_reference,':user_profil'=>$user_profil,':user_nom'=>$user_nom,':user_prenom'=>$user_prenom,':user_login'=>$user_login,':password_crypte'=>$password_crypte,':eleve_classe_id'=>$eleve_classe_id,':user_id_ent'=>$user_id_ent,':user_id_gepi'=>$user_id_gepi);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	$user_id = DB::getLastOid(SACOCHE_STRUCTURE_BD_NAME);
	// Pour un professeur, l'affecter obligatoirement à la matière transversale
	if($user_profil=='professeur')
	{
		$DB_SQL = 'INSERT INTO sacoche_jointure_user_matiere (user_id ,matiere_id,jointure_coord) ';
		$DB_SQL.= 'VALUES(:user_id,:matiere_id,:jointure_coord)';
		$DB_VAR = array(':user_id'=>$user_id,':matiere_id'=>ID_MATIERE_TRANSVERSALE,':jointure_coord'=>0);
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
	return $user_id;
}

/**
 * DB_STRUCTURE_ajouter_adresse_parent
 *
 * @param int    $parent_id
 * @param array  $tab_adresse
 * @return void
 */
function DB_STRUCTURE_ajouter_adresse_parent($parent_id,$tab_adresse)
{
	$DB_SQL = 'INSERT INTO sacoche_parent_adresse(parent_id,adresse_ligne1,adresse_ligne2,adresse_ligne3,adresse_ligne4,adresse_postal_code,adresse_postal_libelle,adresse_pays_nom) ';
	$DB_SQL.= 'VALUES(:parent_id,:ligne1,:ligne2,:ligne3,:ligne4,:postal_code,:postal_libelle,:pays_nom)';
	$DB_VAR = array(':parent_id'=>$parent_id,':ligne1'=>$tab_adresse[0],':ligne2'=>$tab_adresse[1],':ligne3'=>$tab_adresse[2],':ligne4'=>$tab_adresse[3],':postal_code'=>$tab_adresse[4],':postal_libelle'=>$tab_adresse[5],':pays_nom'=>$tab_adresse[6]);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_ajouter_jointure_parent_eleve
 *
 * @param int    $parent_id
 * @param int    $eleve_id
 * @param int    $resp_legal_num
 * @return void
 */
function DB_STRUCTURE_ajouter_jointure_parent_eleve($parent_id,$eleve_id,$resp_legal_num)
{
	$DB_SQL = 'INSERT INTO sacoche_jointure_parent_eleve(parent_id,eleve_id,resp_legal_num) ';
	$DB_SQL.= 'VALUES(:parent_id,:eleve_id,:resp_legal_num)';
	$DB_VAR = array(':parent_id'=>$parent_id,':eleve_id'=>$eleve_id,':resp_legal_num'=>$resp_legal_num);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_ajouter_devoir
 *
 * @param int    $prof_id
 * @param int    $groupe_id
 * @param string $date_mysql
 * @param string $info
 * @param string $date_visible_mysql
 * @return int
 */
function DB_STRUCTURE_ajouter_devoir($prof_id,$groupe_id,$date_mysql,$info,$date_visible_mysql)
{
	$DB_SQL = 'INSERT INTO sacoche_devoir(prof_id,groupe_id,devoir_date,devoir_info,devoir_visible_date) ';
	$DB_SQL.= 'VALUES(:prof_id,:groupe_id,:date,:info,:visible_date)';
	$DB_VAR = array(':prof_id'=>$prof_id,':groupe_id'=>$groupe_id,':date'=>$date_mysql,':info'=>$info,':visible_date'=>$date_visible_mysql);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return DB::getLastOid(SACOCHE_STRUCTURE_BD_NAME);
}

/**
 * DB_STRUCTURE_ajouter_saisie
 * Si la note est "REQ" (pour marquer une demande d'évaluation), on utilise un REPLACE au lieu d'un INSERT car une saisie peut déjà exister (si le prof ajoute les demandes à un devoir existant).
 *
 * @param int    $prof_id
 * @param int    $eleve_id
 * @param int    $devoir_id
 * @param int    $item_id
 * @param string $item_date_mysql
 * @param string $item_note
 * @param string $item_info
 * @param string $item_date_visible_mysql
 * @return void
 */
function DB_STRUCTURE_ajouter_saisie($prof_id,$eleve_id,$devoir_id,$item_id,$item_date_mysql,$item_note,$item_info,$item_date_visible_mysql)
{
	$commande = ($item_note!='REQ') ? 'INSERT' : 'REPLACE' ;
	$DB_SQL = $commande.' INTO sacoche_saisie ';
	$DB_SQL.= 'VALUES(:prof_id,:eleve_id,:devoir_id,:item_id,:item_date,:item_note,:item_info,:item_date_visible)';
	$DB_VAR = array(':prof_id'=>$prof_id,':eleve_id'=>$eleve_id,':devoir_id'=>$devoir_id,':item_id'=>$item_id,':item_date'=>$item_date_mysql,':item_note'=>$item_note,':item_info'=>$item_info,':item_date_visible'=>$item_date_visible_mysql);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_ajouter_validation
 *
 * @param string $type   'entree' ou 'pilier'
 * @param int    $user_id
 * @param int    $element_id
 * @param int    $validation_etat
 * @param string $validation_date_mysql
 * @param string $validation_info
 * @return void
 */
function DB_STRUCTURE_ajouter_validation($type,$user_id,$element_id,$validation_etat,$validation_date_mysql,$validation_info)
{
	$DB_SQL = 'INSERT INTO sacoche_jointure_user_'.$type.' ';
	$DB_SQL.= 'VALUES(:user_id,:'.$type.'_id,:validation_etat,:validation_date_mysql,:validation_info)';
	$DB_VAR = array(':user_id'=>$user_id,':'.$type.'_id'=>$element_id,':validation_etat'=>$validation_etat,':validation_date_mysql'=>$validation_date_mysql,':validation_info'=>$validation_info);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_ajouter_demande
 *
 * @param int      $eleve_id
 * @param int      $matiere_id
 * @param int      $item_id
 * @param string   $demande_date_mysql
 * @param int|null $demande_score
 * @param string   $demande_statut
 * @return int
 */
function DB_STRUCTURE_ajouter_demande($eleve_id,$matiere_id,$item_id,$demande_date_mysql,$demande_score,$demande_statut)
{
	$DB_SQL = 'INSERT INTO sacoche_demande(user_id,matiere_id,item_id,demande_date,demande_score,demande_statut) ';
	$DB_SQL.= 'VALUES(:eleve_id,:matiere_id,:item_id,:demande_date,:demande_score,:demande_statut)';
	$DB_VAR = array(':eleve_id'=>$eleve_id,':matiere_id'=>$matiere_id,':item_id'=>$item_id,':demande_date'=>$demande_date_mysql,':demande_score'=>$demande_score,':demande_statut'=>$demande_statut);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return DB::getLastOid(SACOCHE_STRUCTURE_BD_NAME);
}

/**
 * DB_STRUCTURE_ajouter_referentiel
 *
 * @param int    $matiere_id
 * @param int    $niveau_id
 * @param string $partage_etat
 * @return void
 */
function DB_STRUCTURE_ajouter_referentiel($matiere_id,$niveau_id,$partage_etat)
{
	$DB_SQL = 'INSERT INTO sacoche_referentiel ';
	$DB_SQL.= 'VALUES(:matiere_id,:niveau_id,:partage_etat,:partage_date,:calcul_methode,:calcul_limite,:mode_synthese)';
	$DB_VAR = array(':matiere_id'=>$matiere_id,':niveau_id'=>$niveau_id,':partage_etat'=>$partage_etat,':partage_date'=>date("Y-m-d"),':calcul_methode'=>$_SESSION['CALCUL_METHODE'],':calcul_limite'=>$_SESSION['CALCUL_LIMITE'],':mode_synthese'=>'inconnu');
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_importer_arborescence_from_XML
 * Importer dans la base l'arborescence d'un référentiel à partir d'un XML récupéré sur le serveur communautaire.
 * Remarque : les ordres des domaines / thèmes / items ne sont pas dans le XML car il sont générés par leur position dans l'arborescence
 *
 * @param string $arbreXML
 * @param int    $matiere_id
 * @param int    $niveau_id
 * @return void
 */
function DB_STRUCTURE_importer_arborescence_from_XML($arbreXML,$matiere_id,$niveau_id)
{
	// décortiquer l'arbre XML
	$xml = new DOMDocument;
	$xml -> loadXML($arbreXML);
	// On passe en revue les domaines...
	$domaine_liste = $xml -> getElementsByTagName('domaine');
	$domaine_nb = $domaine_liste -> length;
	for($domaine_ordre=0; $domaine_ordre<$domaine_nb; $domaine_ordre++)
	{
		$domaine_xml = $domaine_liste -> item($domaine_ordre);
		$domaine_ref = $domaine_xml -> getAttribute('ref');
		$domaine_nom = $domaine_xml -> getAttribute('nom');
		$DB_SQL = 'INSERT INTO sacoche_referentiel_domaine(matiere_id,niveau_id,domaine_ordre,domaine_ref,domaine_nom) ';
		$DB_SQL.= 'VALUES(:matiere_id,:niveau_id,:ordre,:ref,:nom)';
		$DB_VAR = array(':matiere_id'=>$matiere_id,':niveau_id'=>$niveau_id,':ordre'=>$domaine_ordre+1,':ref'=>$domaine_ref,':nom'=>$domaine_nom);
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$domaine_id = DB::getLastOid(SACOCHE_STRUCTURE_BD_NAME);
		// On passe en revue les thèmes du domaine...
		$theme_liste = $domaine_xml -> getElementsByTagName('theme');
		$theme_nb = $theme_liste -> length;
		for($theme_ordre=0; $theme_ordre<$theme_nb; $theme_ordre++)
		{
			$theme_xml = $theme_liste -> item($theme_ordre);
			$theme_nom = $theme_xml -> getAttribute('nom');
			$DB_SQL = 'INSERT INTO sacoche_referentiel_theme(domaine_id,theme_ordre,theme_nom) ';
			$DB_SQL.= 'VALUES(:domaine_id,:ordre,:nom)';
			$DB_VAR = array(':domaine_id'=>$domaine_id,':ordre'=>$theme_ordre+1,':nom'=>$theme_nom);
			DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
			$theme_id = DB::getLastOid(SACOCHE_STRUCTURE_BD_NAME);
			// On passe en revue les items du thème...
			$item_liste = $theme_xml -> getElementsByTagName('item');
			$item_nb = $item_liste -> length;
			for($item_ordre=0; $item_ordre<$item_nb; $item_ordre++)
			{
				$item_xml   = $item_liste -> item($item_ordre);
				$item_socle = $item_xml -> getAttribute('socle');
				$item_nom   = $item_xml -> getAttribute('nom');
				$item_coef  = $item_xml -> getAttribute('coef');
				$item_cart  = $item_xml -> getAttribute('cart');
				$item_lien  = $item_xml -> getAttribute('lien');
				$DB_SQL = 'INSERT INTO sacoche_referentiel_item(theme_id,entree_id,item_ordre,item_nom,item_coef,item_cart,item_lien) ';
				$DB_SQL.= 'VALUES(:theme,:socle,:ordre,:nom,:coef,:cart,:lien)';
				$DB_VAR = array(':theme'=>$theme_id,':socle'=>$item_socle,':ordre'=>$item_ordre,':nom'=>$item_nom,':coef'=>$item_coef,':cart'=>$item_cart,':lien'=>$item_lien);
				DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
				// $item_id = DB::getLastOid(SACOCHE_STRUCTURE_BD_NAME); // inutile
			}
		}
	}
}

/**
 * DB_STRUCTURE_modifier_parametres
 *
 * @param array tableau $parametre_nom => $parametre_valeur des paramètres à modfifier
 * @return void
 */
function DB_STRUCTURE_modifier_parametres($tab_parametres)
{
	/*
		modifier_matieres_partagees
			On ne défait pas pour autant les liaisons avec les enseignants... simplement elles n'apparaitront plus dans les formulaires.
			Idem pour les jointures avec les référentiels : ainsi les scores des élèves demeurent conservés.
		modifier_niveaux
			On ne défait pas pour autant les liaisons avec les groupes... simplement ils n'apparaitront plus dans les formulaires.
			Idem pour les jointures avec les référentiels : ainsi les scores des élèves demeurent conservés.
		modifier_paliers
			On ne défait pas pour autant les jointures avec les référentiels : ainsi les scores des élèves demeurent conservés.
	*/
	$DB_SQL = 'UPDATE sacoche_parametre ';
	$DB_SQL.= 'SET parametre_valeur=:parametre_valeur ';
	$DB_SQL.= 'WHERE parametre_nom=:parametre_nom ';
	$DB_SQL.= 'LIMIT 1';
	foreach($tab_parametres as $parametre_nom => $parametre_valeur)
	{
		$DB_VAR = array(':parametre_nom'=>$parametre_nom,':parametre_valeur'=>$parametre_valeur);
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
}

/**
 * DB_STRUCTURE_recopier_identifiants (exemples : id_gepi=id_ent | id_gepi=login | id_ent=id_gepi | id_ent=login )
 *
 * @param string $champ_depart
 * @param string $champ_arrive
 * @return void
 */
function DB_STRUCTURE_recopier_identifiants($champ_depart,$champ_arrive)
{
	$DB_SQL = 'UPDATE sacoche_user ';
	$DB_SQL.= 'SET user_'.$champ_arrive.'=user_'.$champ_depart.' ';
	$DB_SQL.= 'WHERE user_'.$champ_depart.'!="" ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_modifier_utilisateur
 * On ne touche pas à "connexion_date".
 * Si "statut" modifié on maj "user_statut_date".
 * On peut envisager une modification de "user_profil" (passage professeur -> directeur par exemple)
 *
 * @param int     $user_id
 * @param array   array(':sconet_id'=>$val, ':sconet_num'=>$val, ':reference'=>$val , ':profil'=>$val , ':nom'=>$val , ':prenom'=>$val , ':login'=>$val , ':password'=>$val , ':statut'=>$val , ':daltonisme'=>$val , ':classe'=>$val , ':id_ent'=>$val , ':id_gepi'=>$val );
 * @return void
 */
function DB_STRUCTURE_modifier_utilisateur($user_id,$DB_VAR)
{
	$tab_set = array();
	foreach($DB_VAR as $key => $val)
	{
		switch($key)
		{
			case ':sconet_id' :  $tab_set[] = 'user_sconet_id='.$key;      break;
			case ':sconet_num' : $tab_set[] = 'user_sconet_elenoet='.$key; break;
			case ':reference' :  $tab_set[] = 'user_reference='.$key;      break;
			case ':profil' :     $tab_set[] = 'user_profil='.$key;         break;
			case ':nom' :        $tab_set[] = 'user_nom='.$key;            break;
			case ':prenom' :     $tab_set[] = 'user_prenom='.$key;         break;
			case ':login' :      $tab_set[] = 'user_login='.$key;          break;
			case ':password' :   $tab_set[] = 'user_password=:password_crypte'; $DB_VAR[':password_crypte'] = crypter_mdp($DB_VAR[':password']); unset($DB_VAR[':password']); break;
			case ':statut' :     $tab_set[] = 'user_statut='.$key; $tab_set[] = 'user_statut_date=NOW()'; break;
			case ':daltonisme' : $tab_set[] = 'user_daltonisme='.$key;     break;
			case ':classe' :     $tab_set[] = 'eleve_classe_id='.$key;     break;
			case ':id_ent' :     $tab_set[] = 'user_id_ent='.$key;         break;
			case ':id_gepi' :    $tab_set[] = 'user_id_gepi='.$key;        break;
		}
	}
	$DB_SQL = 'UPDATE sacoche_user ';
	$DB_SQL.= 'SET '.implode(', ',$tab_set).' ';
	$DB_SQL.= 'WHERE user_id=:user_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR[':user_id'] = $user_id;
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_adresse_parent
 *
 * @param string $parent_id
 * @param array  $tab_adresse
 * @return int
 */
function DB_STRUCTURE_modifier_adresse_parent($parent_id,$tab_adresse)
{
	$DB_SQL = 'UPDATE sacoche_parent_adresse ';
	$DB_SQL.= 'SET adresse_ligne1=:ligne1, adresse_ligne2=:ligne2, adresse_ligne3=:ligne3, adresse_ligne4=:ligne4, adresse_postal_code=:postal_code, adresse_postal_libelle=:postal_libelle, adresse_pays_nom=:pays_nom ';
	$DB_SQL.= 'WHERE parent_id=:parent_id ';
	$DB_VAR = array(':parent_id'=>$parent_id,':ligne1'=>$tab_adresse[0],':ligne2'=>$tab_adresse[1],':ligne3'=>$tab_adresse[2],':ligne4'=>$tab_adresse[3],':postal_code'=>$tab_adresse[4],':postal_libelle'=>$tab_adresse[5],':pays_nom'=>$tab_adresse[6]);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_date
 *
 * @param string  $champ   'connexion' ou 'tentative'
 * @param int     $user_id
 * @return void
 */
function DB_STRUCTURE_modifier_date($champ,$user_id)
{
	$DB_SQL = 'UPDATE sacoche_user ';
	$DB_SQL.= 'SET user_'.$champ.'_date=NOW() ';
	$DB_SQL.= 'WHERE user_id=:user_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':user_id'=>$user_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_referentiel (juste ses paramètres, pas le contenu de son arborescence)
 *
 * @param int     $matiere_id
 * @param int     $niveau_id
 * @param array   array(':partage_etat'=>$val, ':partage_date'=>$val , ':calcul_methode'=>$val , ':calcul_limite'=>$val , ':mode_synthese'=>$val );
 * @return void
 */
function DB_STRUCTURE_modifier_referentiel($matiere_id,$niveau_id,$DB_VAR)
{
	$tab_set = array();
	foreach($DB_VAR as $key => $val)
	{
		switch($key)
		{
			case ':partage_etat'   : $tab_set[] = 'referentiel_partage_etat='.$key; break;
			case ':partage_date'   : $tab_set[] = 'referentiel_partage_date='.$key; break;
			case ':calcul_methode' : $tab_set[] = 'referentiel_calcul_methode='.$key; break;
			case ':calcul_limite'  : $tab_set[] = 'referentiel_calcul_limite='.$key; break;
			case ':mode_synthese'  : $tab_set[] = 'referentiel_mode_synthese='.$key; break;
		}
	}
	$DB_SQL = 'UPDATE sacoche_referentiel ';
	$DB_SQL.= 'SET '.implode(', ',$tab_set).' ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id AND niveau_id=:niveau_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR[':matiere_id'] = $matiere_id;
	$DB_VAR[':niveau_id'] = $niveau_id;
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_mdp_utilisateur
 * Remarque : cette fonction n'est pas appelée pour un professeur ou un élève si le mode de connexion est SSO
 *
 * @param int    $user_id
 * @param string $password_ancien
 * @param string $password_nouveau
 * @return string   'ok' ou 'Le mot de passe actuel est incorrect !'
 */
function DB_STRUCTURE_modifier_mdp_utilisateur($user_id,$password_ancien,$password_nouveau)
{
	// Tester si l'ancien mot de passe correspond à celui enregistré
	$password_ancien_crypte = crypter_mdp($password_ancien);
	$DB_SQL = 'SELECT user_id FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_id=:user_id AND user_password=:password_crypte ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':user_id'=>$user_id,':password_crypte'=>$password_ancien_crypte);
	$DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	if(!count($DB_ROW))
	{
		return 'Le mot de passe actuel est incorrect !';
	}
	// Remplacer par le nouveau mot de passe
	$password_nouveau_crypte = crypter_mdp($password_nouveau);
	$DB_SQL = 'UPDATE sacoche_user ';
	$DB_SQL.= 'SET user_password=:password_crypte ';
	$DB_SQL.= 'WHERE user_id=:user_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':user_id'=>$user_id,':password_crypte'=>$password_nouveau_crypte);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return 'ok';
}

/**
 * DB_STRUCTURE_modifier_user_langue
 *
 * @param string $listing_user_id
 * @param int    $langue
 * @return void
 */
function DB_STRUCTURE_modifier_user_langue($listing_user_id,$langue)
{
	$DB_SQL = 'UPDATE sacoche_user ';
	$DB_SQL.= 'SET eleve_langue=:langue ';
	$DB_SQL.= 'WHERE user_id IN('.$listing_user_id.') ';
	$DB_VAR = array(':langue'=>$langue);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_matiere_specifique
 *
 * @param int    $matiere_id
 * @param string $matiere_ref
 * @param string $matiere_nom
 * @return void
 */
function DB_STRUCTURE_modifier_matiere_specifique($matiere_id,$matiere_ref,$matiere_nom)
{
	$DB_SQL = 'UPDATE sacoche_matiere ';
	$DB_SQL.= 'SET matiere_ref=:matiere_ref,matiere_nom=:matiere_nom ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':matiere_id'=>$matiere_id,':matiere_ref'=>$matiere_ref,':matiere_nom'=>$matiere_nom);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_matiere_ordre
 *
 * @param int   $matiere_id
 * @param int   $matiere_ordre
 * @return void
 */
function DB_STRUCTURE_modifier_matiere_ordre($matiere_id,$matiere_ordre)
{
	$DB_SQL = 'UPDATE sacoche_matiere ';
	$DB_SQL.= 'SET matiere_ordre=:matiere_ordre ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':matiere_id'=>$matiere_id,':matiere_ordre'=>$matiere_ordre);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_matiere_nb_demandes
 *
 * @param int   $matiere_id
 * @param int   $matiere_nb_demandes
 * @return void
 */
function DB_STRUCTURE_modifier_matiere_nb_demandes($matiere_id,$matiere_nb_demandes)
{
	$DB_SQL = 'UPDATE sacoche_matiere ';
	$DB_SQL.= 'SET matiere_nb_demandes=:matiere_nb_demandes ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':matiere_id'=>$matiere_id,':matiere_nb_demandes'=>$matiere_nb_demandes);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_groupe ; on ne touche pas à 'groupe_type' ni à 'groupe_prof_id'
 *
 * @param int    $groupe_id
 * @param string $groupe_ref
 * @param string $groupe_nom
 * @param int    $niveau_id
 * @return void
 */
function DB_STRUCTURE_modifier_groupe($groupe_id,$groupe_ref,$groupe_nom,$niveau_id)
{
	$DB_SQL = 'UPDATE sacoche_groupe ';
	$DB_SQL.= 'SET groupe_ref=:groupe_ref,groupe_nom=:groupe_nom,niveau_id=:niveau_id ';
	$DB_SQL.= 'WHERE groupe_id=:groupe_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':groupe_id'=>$groupe_id,':groupe_ref'=>$groupe_ref,':groupe_nom'=>$groupe_nom,':niveau_id'=>$niveau_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_ordre_item
 *
 * @param int    $devoir_id
 * @param array  $tab_items   tableau des id des items
 * @return void
 */
function DB_STRUCTURE_modifier_ordre_item($devoir_id,$tab_items)
{
	$DB_SQL = 'UPDATE sacoche_jointure_devoir_item SET jointure_ordre=:ordre ';
	$DB_SQL.= 'WHERE devoir_id=:devoir_id AND item_id=:item_id ';
	$DB_SQL.= 'LIMIT 1';
	$ordre = 1;
	foreach($tab_items as $item_id)
	{
		$DB_VAR = array(':devoir_id'=>$devoir_id,':item_id'=>$item_id,':ordre'=>$ordre);
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$ordre++;
	}
}

/**
 * DB_STRUCTURE_modifier_saisie
 *
 * @param int    $eleve_id
 * @param int    $devoir_id
 * @param int    $item_id
 * @param string $saisie_note
 * @param string $saisie_info
 * @return void
 */
function DB_STRUCTURE_modifier_saisie($eleve_id,$devoir_id,$item_id,$saisie_note,$saisie_info)
{
	$DB_SQL = 'UPDATE sacoche_saisie SET saisie_note=:saisie_note,saisie_info=:saisie_info ';
	$DB_SQL.= 'WHERE eleve_id=:eleve_id AND devoir_id=:devoir_id AND item_id=:item_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':eleve_id'=>$eleve_id,':devoir_id'=>$devoir_id,':item_id'=>$item_id,':saisie_note'=>$saisie_note,':saisie_info'=>$saisie_info);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_validation
 *
 * @param string $type   'entree' ou 'pilier'
 * @param int    $user_id
 * @param int    $element_id
 * @param int    $validation_etat
 * @param string $validation_date_mysql
 * @param string $validation_info
 * @return void
 */
function DB_STRUCTURE_modifier_validation($type,$user_id,$element_id,$validation_etat,$validation_date_mysql,$validation_info)
{
	$DB_SQL = 'UPDATE sacoche_jointure_user_'.$type.' SET validation_'.$type.'_etat=:validation_etat, validation_'.$type.'_date=:validation_date_mysql, validation_'.$type.'_info=:validation_info ';
	$DB_SQL.= 'WHERE user_id=:user_id AND '.$type.'_id=:'.$type.'_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':user_id'=>$user_id,':'.$type.'_id'=>$element_id,':validation_etat'=>$validation_etat,':validation_date_mysql'=>$validation_date_mysql,':validation_info'=>$validation_info);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_devoir
 *
 * @param int    $devoir_id
 * @param int    $prof_id
 * @param string $date_mysql
 * @param string $info
 * @param string $date_visible_mysql
 * @param array  $tab_items   tableau des id des items
 * @return void
 */
function DB_STRUCTURE_modifier_devoir($devoir_id,$prof_id,$date_mysql,$info,$date_visible_mysql,$tab_items)
{
	// sacoche_devoir (maj)
	$DB_SQL = 'UPDATE sacoche_devoir ';
	$DB_SQL.= 'SET devoir_date=:date, devoir_info=:devoir_info, devoir_visible_date=:visible_date ';
	$DB_SQL.= 'WHERE devoir_id=:devoir_id AND prof_id=:prof_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':date'=>$date_mysql,':devoir_info'=>$info,':visible_date'=>$date_visible_mysql,':devoir_id'=>$devoir_id,':prof_id'=>$prof_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// sacoche_saisie (maj)
	$saisie_info = $info.' ('.$_SESSION['USER_NOM'].' '.$_SESSION['USER_PRENOM']{0}.'.)';
	$DB_SQL = 'UPDATE sacoche_saisie ';
	$DB_SQL.= 'SET saisie_date=:date, saisie_info=:saisie_info, saisie_visible_date=:visible_date ';
	$DB_SQL.= 'WHERE prof_id=:prof_id AND devoir_id=:devoir_id ';
	$DB_VAR = array(':prof_id'=>$prof_id,':devoir_id'=>$devoir_id,':date'=>$date_mysql,':saisie_info'=>$saisie_info,':visible_date'=>$date_visible_mysql);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_liaison_user_groupe
 *
 * @param int    $user_id
 * @param string $user_profil   'eleve' ou 'professeur'
 * @param int    $groupe_id
 * @param string $groupe_type   'classe' ou 'groupe' ou 'besoin' MAIS PAS 'eval', géré par DB_STRUCTURE_modifier_liaison_devoir_user()
 * @param bool   $etat          'true' pour ajouter/modifier une liaison ; 'false' pour retirer une liaison
 * @return void
 */
function DB_STRUCTURE_modifier_liaison_user_groupe($user_id,$user_profil,$groupe_id,$groupe_type,$etat)
{
	// Dans le cas d'un élève et d'une classe, ce n'est pas dans la table de jointure mais dans la table user que ça se passe
	if( ($user_profil=='eleve') && ($groupe_type=='classe') )
	{
		$DB_SQL = 'UPDATE sacoche_user ';
		if($etat)
		{
			$DB_SQL.= 'SET eleve_classe_id=:groupe_id ';
			$DB_SQL.= 'WHERE user_id=:user_id ';
		}
		else
		{
			$DB_SQL.= 'SET eleve_classe_id=0 ';
			$DB_SQL.= 'WHERE user_id=:user_id AND eleve_classe_id=:groupe_id ';
		}
		$DB_SQL.= 'LIMIT 1';
	}
	else
	{
		if($etat)
		{
			$DB_SQL = 'REPLACE INTO sacoche_jointure_user_groupe (user_id,groupe_id) ';
			$DB_SQL.= 'VALUES(:user_id,:groupe_id)';
		}
		else
		{
			$DB_SQL = 'DELETE FROM sacoche_jointure_user_groupe ';
			$DB_SQL.= 'WHERE user_id=:user_id AND groupe_id=:groupe_id ';
			$DB_SQL.= 'LIMIT 1';
		}
	}
	$DB_VAR = array(':user_id'=>$user_id,':groupe_id'=>$groupe_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_liaison_professeur_coordonnateur
 *
 * @param int    $user_id
 * @param int    $matiere_id
 * @param bool   $etat          'true' pour ajouter/modifier une liaison ; 'false' pour retirer une liaison
 * @return void
 */
function DB_STRUCTURE_modifier_liaison_professeur_coordonnateur($user_id,$matiere_id,$etat)
{
	$coord = ($etat) ? 1 : 0 ;
	$DB_SQL = 'UPDATE sacoche_jointure_user_matiere SET jointure_coord=:coord ';
	$DB_SQL.= 'WHERE user_id=:user_id AND matiere_id=:matiere_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':user_id'=>$user_id,':matiere_id'=>$matiere_id,':coord'=>$coord);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_liaison_professeur_principal
 *
 * @param int    $user_id
 * @param int    $groupe_id
 * @param bool   $etat          'true' pour ajouter/modifier une liaison ; 'false' pour retirer une liaison
 * @return void
 */
function DB_STRUCTURE_modifier_liaison_professeur_principal($user_id,$groupe_id,$etat)
{
	$pp = ($etat) ? 1 : 0 ;
	$DB_SQL = 'UPDATE sacoche_jointure_user_groupe SET jointure_pp=:pp ';
	$DB_SQL.= 'WHERE user_id=:user_id AND groupe_id=:groupe_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':user_id'=>$user_id,':groupe_id'=>$groupe_id,':pp'=>$pp);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_liaison_professeur_matiere
 *
 * @param int    $user_id
 * @param int    $matiere_id
 * @param bool   $etat          'true' pour ajouter/modifier une liaison ; 'false' pour retirer une liaison
 * @return void
 */
function DB_STRUCTURE_modifier_liaison_professeur_matiere($user_id,$matiere_id,$etat)
{
	if($etat)
	{
		// On ne peut pas faire un REPLACE car si un enregistrement est présent ça fait un DELETE+INSERT et du coup on perd la valeur de jointure_coord.
		$DB_SQL = 'SELECT * FROM sacoche_jointure_user_matiere ';
		$DB_SQL.= 'WHERE user_id=:user_id AND matiere_id=:matiere_id ';
		$DB_SQL.= 'LIMIT 1';
		$DB_VAR = array(':user_id'=>$user_id,':matiere_id'=>$matiere_id);
		$DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		if(!count($DB_ROW))
		{
			$DB_SQL = 'INSERT INTO sacoche_jointure_user_matiere (user_id,matiere_id,jointure_coord) ';
			$DB_SQL.= 'VALUES(:user_id,:matiere_id,:coord)';
			$DB_VAR = array(':user_id'=>$user_id,':matiere_id'=>$matiere_id,':coord'=>0);
			DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		}
	}
	else
	{
		$DB_SQL = 'DELETE FROM sacoche_jointure_user_matiere ';
		$DB_SQL.= 'WHERE user_id=:user_id AND matiere_id=:matiere_id ';
		$DB_SQL.= 'LIMIT 1';
		$DB_VAR = array(':user_id'=>$user_id,':matiere_id'=>$matiere_id);
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
}

/**
 * DB_STRUCTURE_modifier_liaison_devoir_item
 *
 * @param int    $devoir_id
 * @param array  $tab_items   tableau des id des items
 * @param string $mode        {creer;dupliquer} => insertion dans un nouveau devoir || {substituer} => maj avec delete / insert || {ajouter} => maj avec insert uniquement
 * @param int    $devoir_ordonne_id   Dans le cas d'une duplication, id du devoir dont il faut récupérer l'ordre des items.
 * @return void
 */
function DB_STRUCTURE_modifier_liaison_devoir_item($devoir_id,$tab_items,$mode,$devoir_ordonne_id=0)
{
	if( ($mode=='creer') || ($mode=='dupliquer') )
	{
		// Dans le cas d'une duplication, il faut aller rechercher l'ordre éventuel des items de l'évaluation d'origine pour ne pas le perdre
		$tab_ordre = array();
		if($devoir_ordonne_id)
		{
			$DB_SQL = 'SELECT item_id,jointure_ordre FROM sacoche_jointure_devoir_item ';
			$DB_SQL.= 'WHERE devoir_id=:devoir_id AND jointure_ordre>0 ';
			$DB_VAR = array(':devoir_id'=>$devoir_ordonne_id);
			$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
			if(count($DB_TAB))
			{
				foreach($DB_TAB as $DB_ROW)
				{
					$tab_ordre[$DB_ROW['item_id']] = $DB_ROW['jointure_ordre'];
				}
			}
		}
		// Insertion des items
		$DB_SQL = 'INSERT INTO sacoche_jointure_devoir_item(devoir_id,item_id,jointure_ordre) ';
		$DB_SQL.= 'VALUES(:devoir_id,:item_id,:ordre)';
		foreach($tab_items as $item_id)
		{
			$ordre = (isset($tab_ordre[$item_id])) ? $tab_ordre[$item_id] : 0 ;
			$DB_VAR = array(':devoir_id'=>$devoir_id,':item_id'=>$item_id,':ordre'=>$ordre);
			DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		}
	}
	else
	{
		// On ne peut pas faire un REPLACE car si un enregistrement est présent ça fait un DELETE+INSERT et du coup on perd l'info sur l'ordre des items.
		// Alors on récupère la liste des items déjà présents, et on étudie les différences pour faire des DELETE et INSERT sélectifs
		// -> on récupère les items actuels
		$DB_SQL = 'SELECT item_id FROM sacoche_jointure_devoir_item ';
		$DB_SQL.= 'WHERE devoir_id=:devoir_id ';
		$DB_VAR = array(':devoir_id'=>$devoir_id);
		$tab_old_items = DB::queryCol(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		// -> on supprime si besoin les anciens items associés à ce devoir qui ne sont plus dans la liste transmise
		// -> on supprime si besoin les saisies des anciens items associés à ce devoir qui ne sont plus dans la liste transmise
		//   (concernant les saisies superflues concernant les items, voir DB_STRUCTURE_modifier_liaison_devoir_item)
		if($mode=='substituer')
		{
			$tab_items_supprimer = array_diff($tab_old_items,$tab_items);
			if(count($tab_items_supprimer))
			{
				$chaine_item_id = implode(',',$tab_items_supprimer);
				$DB_SQL = 'DELETE FROM sacoche_jointure_devoir_item ';
				$DB_SQL.= 'WHERE devoir_id=:devoir_id AND item_id IN('.$chaine_item_id.')';
				$DB_VAR = array(':devoir_id'=>$devoir_id);
				DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
				// sacoche_saisie (retirer superflu concernant les items ; concernant les élèves voir DB_STRUCTURE_modifier_liaison_devoir_user)
				$DB_SQL = 'DELETE FROM sacoche_saisie ';
				$DB_SQL.= 'WHERE devoir_id=:devoir_id AND item_id IN('.$chaine_item_id.')';
				$DB_VAR = array(':devoir_id'=>$devoir_id);
				DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
			}
		}
		// -> on ajoute les nouveaux items non anciennement présents
		$tab_items_ajouter = array_diff($tab_items,$tab_old_items);
		if(count($tab_items_ajouter))
		{
			foreach($tab_items_ajouter as $item_id)
			{
				$DB_SQL = 'INSERT INTO sacoche_jointure_devoir_item(devoir_id,item_id) ';
				$DB_SQL.= 'VALUES(:devoir_id,:item_id)';
				$DB_VAR = array(':devoir_id'=>$devoir_id,':item_id'=>$item_id);
				DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
			}
		}
	}
}

/**
 * DB_STRUCTURE_modifier_liaison_devoir_user
 * Uniquement pour les évaluations de type 'eval' ; voir DB_STRUCTURE_modifier_liaison_devoir_groupe() pour les autres
 *
 * @param int    $devoir_id
 * @param int    $groupe_id
 * @param array  $tab_eleves   tableau des id des élèves
 * @param string $mode         'creer' pour un insert dans un nouveau devoir || 'substituer' pour une maj delete / insert || 'ajouter' pour maj insert uniquement
 * @return void
 */
function DB_STRUCTURE_modifier_liaison_devoir_user($devoir_id,$groupe_id,$tab_eleves,$mode)
{
	// -> on récupère la liste des élèves actuels déjà associés au groupe (pour la comparer à la liste transmise)
	if($mode!='creer')
	{
		// DB::query(SACOCHE_STRUCTURE_BD_NAME , 'SET group_concat_max_len = ...'); // Pour lever si besoin une limitation de GROUP_CONCAT (group_concat_max_len est par défaut limité à une chaine de 1024 caractères).
		$DB_SQL = 'SELECT GROUP_CONCAT(user_id SEPARATOR " ") AS users_listing ';
		$DB_SQL.= 'FROM sacoche_jointure_user_groupe ';
		$DB_SQL.= 'LEFT JOIN sacoche_user USING(user_id) ';
		$DB_SQL.= 'WHERE groupe_id=:groupe_id AND user_profil=:profil ';
		$DB_SQL.= 'GROUP BY groupe_id';
		$DB_VAR = array(':groupe_id'=>$groupe_id,':profil'=>'eleve');
		$users_listing = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$tab_eleves_avant = ($users_listing) ? explode(' ',$users_listing) : array() ;
	}
	else
	{
		$tab_eleves_avant = array() ;
	}
	// -> on supprime si besoin les anciens élèves associés à ce groupe qui ne sont plus dans la liste transmise
	// -> on supprime si besoin les saisies des anciens élèves associés à ce devoir qui ne sont plus dans la liste transmise
	//   (pour les saisies superflues concernant les items, voir DB_STRUCTURE_modifier_liaison_devoir_item)
	if($mode=='substituer')
	{
		$tab_eleves_moins = array_diff($tab_eleves_avant,$tab_eleves);
		if(count($tab_eleves_moins))
		{
			$chaine_user_id = implode(',',$tab_eleves_moins);
			$DB_SQL = 'DELETE FROM sacoche_jointure_user_groupe ';
			$DB_SQL.= 'WHERE user_id IN('.$chaine_user_id.') AND groupe_id=:groupe_id ';
			$DB_VAR = array(':groupe_id'=>$groupe_id);
			DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
			$DB_SQL = 'DELETE FROM sacoche_saisie ';
			$DB_SQL.= 'WHERE devoir_id=:devoir_id AND eleve_id IN('.$chaine_user_id.')';
			$DB_VAR = array(':devoir_id'=>$devoir_id);
			DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		}
	}
	// -> on ajoute si besoin les nouveaux élèves dans la liste transmise qui n'étaient pas déjà associés à ce groupe
	$tab_eleves_plus = array_diff($tab_eleves,$tab_eleves_avant);
	if(count($tab_eleves_plus))
	{
		foreach($tab_eleves_plus as $user_id)
		{
			$DB_SQL = 'INSERT INTO sacoche_jointure_user_groupe (user_id,groupe_id) ';
			$DB_SQL.= 'VALUES(:user_id,:groupe_id)';
			$DB_VAR = array(':user_id'=>$user_id,':groupe_id'=>$groupe_id);
			DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		}
	}
}

/**
 * DB_STRUCTURE_modifier_liaison_devoir_groupe
 * Uniquement pour les évaluations sur une classe ou un groupe (pas de type 'eval')
 * RETIRÉ APRÈS REFLEXION : IL N'Y A PAS DE RAISON DE CARRÉMENT CHANGER LE GROUPE D'UNE ÉVALUATION => AU PIRE ON LA DUPLIQUE POUR UN AUTRE GROUPE PUIS ON LA SUPPRIME.
 *
 * @param int    $devoir_id
 * @param int    $groupe_id
 * @return void
 */
/*
function DB_STRUCTURE_modifier_liaison_devoir_groupe($devoir_id,$groupe_id)
{
	// -> on récupère l'id du groupe antérieurement associé au devoir
	$DB_SQL = 'SELECT groupe_id ';
	$DB_SQL.= 'FROM sacoche_devoir ';
	$DB_SQL.= 'WHERE devoir_id=:devoir_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':devoir_id'=>$devoir_id);
	if( $groupe_id != DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR) )
	{
		// sacoche_devoir (maj)
		$DB_SQL = 'UPDATE sacoche_devoir ';
		$DB_SQL.= 'SET groupe_id=:groupe_id ';
		$DB_SQL.= 'WHERE devoir_id=:devoir_id ';
		$DB_SQL.= 'LIMIT 1';
		$DB_VAR = array(':devoir_id'=>$devoir_id,':groupe_id'=>$groupe_id);
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		// sacoche_saisie : on ne s'embête pas à essayer de voir s'il y aurait intersection entre les deux groupes, on supprime les saisies du groupe antérieur...
		$DB_SQL = 'DELETE FROM sacoche_saisie ';
		$DB_SQL.= 'WHERE devoir_id=:devoir_id ';
		$DB_VAR = array(':devoir_id'=>$devoir_id);
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
}
*/

/**
 * DB_STRUCTURE_modifier_liaison_groupe_periode
 *
 * @param int|true   $groupe_id          id du groupe ou 'true' pour supprimer la jointure sur tous les groupes
 * @param int|true   $periode_id         id de la période ou 'true' pour supprimer la jointure sur toutes les périodes
 * @param bool       $etat               'true' pour ajouter/modifier une liaison ; 'false' pour retirer une liaison
 * @param string     $date_debut_mysql   date de début au format mysql (facultatif : obligatoire uniquement si $etat=true)
 * @param string     $date_fin_mysql     date de fin au format mysql (facultatif : obligatoire uniquement si $etat=true)
 * @return void
 */
function DB_STRUCTURE_modifier_liaison_groupe_periode($groupe_id,$periode_id,$etat,$date_debut_mysql='',$date_fin_mysql='')
{
	if($etat)
	{
		// Ajouter / modifier une liaison
		$DB_SQL = 'REPLACE INTO sacoche_jointure_groupe_periode (groupe_id,periode_id,jointure_date_debut,jointure_date_fin) ';
		$DB_SQL.= 'VALUES(:groupe_id,:periode_id,:date_debut,:date_fin)';
		$DB_VAR = array(':groupe_id'=>$groupe_id,':periode_id'=>$periode_id,':date_debut'=>$date_debut_mysql,':date_fin'=>$date_fin_mysql);
	}
	else
	{
		if( ($groupe_id===true) && ($periode_id===true) )
		{
			// Retirer toutes les liaisons
			$DB_SQL = 'DELETE FROM sacoche_jointure_groupe_periode ';
			$DB_VAR = null;
		}
		else
		{
			// Retirer une liaison
			$DB_SQL = 'DELETE FROM sacoche_jointure_groupe_periode ';
			$DB_SQL.= 'WHERE groupe_id=:groupe_id AND periode_id=:periode_id ';
			$DB_SQL.= 'LIMIT 1';
			$DB_VAR = array(':groupe_id'=>$groupe_id,':periode_id'=>$periode_id);
		}
	}
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_periode
 *
 * @param int    $periode_id
 * @param int    $periode_ordre
 * @param string $periode_nom
 * @return void
 */
function DB_STRUCTURE_modifier_periode($periode_id,$periode_ordre,$periode_nom)
{
	$DB_SQL = 'UPDATE sacoche_periode ';
	$DB_SQL.= 'SET periode_ordre=:periode_ordre,periode_nom=:periode_nom ';
	$DB_SQL.= 'WHERE periode_id=:periode_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':periode_id'=>$periode_id,':periode_ordre'=>$periode_ordre,':periode_nom'=>$periode_nom);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_modifier_statut_demandes
 *
 * @param string $listing_demande_id   id des demandes séparées par des virgules
 * @param int    $nb_demandes          nb de demandes
 * @param string $statut               parmi 'prof' ou ...
 * @return void
 */
function DB_STRUCTURE_modifier_statut_demandes($listing_demande_id,$nb_demandes,$statut)
{
	$DB_SQL = 'UPDATE sacoche_demande SET demande_statut=:demande_statut ';
	$DB_SQL.= 'WHERE demande_id IN('.$listing_demande_id.') ';
	$DB_SQL.= 'LIMIT '.$nb_demandes;
	$DB_VAR = array(':demande_statut'=>$statut);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_supprimer_matiere_specifique
 *
 * @param int $matiere_id
 * @return void
 */
function DB_STRUCTURE_supprimer_matiere_specifique($matiere_id)
{
	$DB_SQL = 'DELETE FROM sacoche_matiere ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':matiere_id'=>$matiere_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// Il faut aussi supprimer les jointures avec les enseignants
	$DB_SQL = 'DELETE FROM sacoche_jointure_user_matiere ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id ';
	$DB_VAR = array(':matiere_id'=>$matiere_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// Il faut aussi supprimer les référentiels associés, et donc tous les scores associés (orphelins de la matière)
	DB_STRUCTURE_supprimer_referentiel_matiere_niveau($matiere_id);
	// Log de l'action
	ajouter_log_SACoche('Suppression d\'une matière spécifique ('.$matiere_id.').');
}

/**
 * DB_STRUCTURE_supprimer_groupe
 * Par défaut, on supprime aussi les devoirs associés ($with_devoir=true), mais on conserve les notes, sui deviennent orphelines et non éditables ultérieurement.
 * Mais on peut aussi vouloir dans un second temps ($with_devoir=false) supprimer les devoirs associés avec leurs notes en utilisant DB_STRUCTURE_supprimer_devoir_et_saisies().
 *
 * @param int    $groupe_id
 * @param string $groupe_type   valeur parmi 'classe' ; 'groupe' ; 'besoin' ; 'eval'
 * @param bool   $with_devoir
 * @return void
 */
function DB_STRUCTURE_supprimer_groupe($groupe_id,$groupe_type,$with_devoir=true)
{
	// Il faut aussi supprimer les jointures avec les utilisateurs
	// Il faut aussi supprimer les jointures avec les périodes
	$jointure_periode_delete = ( ($groupe_type=='classe') || ($groupe_type=='groupe') ) ? ', sacoche_jointure_groupe_periode ' : '' ;
	$jointure_periode_join   = ( ($groupe_type=='classe') || ($groupe_type=='groupe') ) ? 'LEFT JOIN sacoche_jointure_groupe_periode USING (groupe_id) ' : '' ;
	// Il faut aussi supprimer les évaluations portant sur le groupe
	$jointure_devoir_delete = ($with_devoir) ? ', sacoche_devoir , sacoche_jointure_devoir_item ' : '' ;
	$jointure_devoir_join   = ($with_devoir) ? 'LEFT JOIN sacoche_devoir USING (groupe_id) LEFT JOIN sacoche_jointure_devoir_item USING (devoir_id) ' : '' ;
	// Let's go
	$DB_SQL = 'DELETE sacoche_groupe , sacoche_jointure_user_groupe '.$jointure_periode_delete.$jointure_devoir_delete;
	$DB_SQL.= 'FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
	$DB_SQL.= $jointure_periode_join.$jointure_devoir_join;
	$DB_SQL.= 'WHERE groupe_id=:groupe_id ';
	$DB_VAR = array(':groupe_id'=>$groupe_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// Sans oublier le champ pour les affectations des élèves dans une classe
	if($groupe_type=='classe')
	{
		$DB_SQL = 'UPDATE sacoche_user ';
		$DB_SQL.= 'SET eleve_classe_id=0 ';
		$DB_SQL.= 'WHERE eleve_classe_id=:groupe_id';
		$DB_VAR = array(':groupe_id'=>$groupe_id);
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
	// Log de l'action
	$complement = ($with_devoir) ? ' avec les devoirs associés' : '' ;
	ajouter_log_SACoche('Suppression d\'un groupe ('.$groupe_type.' '.$groupe_id.')'.$complement.'.');
}

/**
 * DB_STRUCTURE_supprimer_devoir_et_saisies
 *
 * @param int   $devoir_id
 * @param int   $prof_id   Seul un prof peut se supprimer une évaluation avec ses scores ; son id sert de sécurité.
 * @return void
 */
function DB_STRUCTURE_supprimer_devoir_et_saisies($devoir_id,$prof_id)
{
	// Il faut aussi supprimer les jointures du devoir avec les items
	$DB_SQL = 'DELETE sacoche_devoir, sacoche_jointure_devoir_item, sacoche_saisie ';
	$DB_SQL.= 'FROM sacoche_devoir ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_devoir_item USING (devoir_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_saisie USING (devoir_id,prof_id) ';
	$DB_SQL.= 'WHERE devoir_id=:devoir_id AND prof_id=:prof_id ';
	$DB_VAR = array(':devoir_id'=>$devoir_id,':prof_id'=>$prof_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// Log de l'action
	ajouter_log_SACoche('Suppression d\'un devoir ('.$devoir_id.') avec les saisies associées.');
}

/**
 * DB_STRUCTURE_supprimer_devoirs_sans_saisies
 * Utilisé uniquement dans le cadre d'un nettoyage annuel ; les groupes de types 'besoin' et 'eval' sont supprimés dans un second temps.
 *
 * @return void
 */
function DB_STRUCTURE_supprimer_devoirs_sans_saisies()
{
	// Il faut aussi supprimer les jointures du devoir avec les items
	$DB_SQL = 'DELETE sacoche_devoir, sacoche_jointure_devoir_item ';
	$DB_SQL.= 'FROM sacoche_devoir ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_devoir_item USING (devoir_id) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	// Log de l'action
	ajouter_log_SACoche('Suppression de tous les devoirs sans les saisies associées.');
}

/**
 * DB_STRUCTURE_supprimer_saisies_REQ
 * Utilisé uniquement dans le cadre d'un nettoyage annuel (reliquats de notes 'REQ').
 *
 * @return void
 */
function DB_STRUCTURE_supprimer_saisies_REQ()
{
	DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_saisie WHERE saisie_note="REQ"' , null);
}

/**
 * DB_STRUCTURE_supprimer_saisie
 *
 * @param int   $eleve_id
 * @param int   $devoir_id
 * @param int   $item_id
 * @return void
 */
function DB_STRUCTURE_supprimer_saisie($eleve_id,$devoir_id,$item_id)
{
	$DB_SQL = 'DELETE FROM sacoche_saisie ';
	$DB_SQL.= 'WHERE eleve_id=:eleve_id AND devoir_id=:devoir_id AND item_id=:item_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':eleve_id'=>$eleve_id,':devoir_id'=>$devoir_id,':item_id'=>$item_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_supprimer_validation
 *
 * @param string $type   'entree' ou 'pilier'
 * @param int    $user_id
 * @param int    $element_id
 * @return void
 */
function DB_STRUCTURE_supprimer_validation($type,$user_id,$element_id)
{
	$DB_SQL = 'DELETE FROM sacoche_jointure_user_'.$type.' ';
	$DB_SQL.= 'WHERE user_id=:user_id AND '.$type.'_id=:'.$type.'_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':user_id'=>$user_id,':'.$type.'_id'=>$element_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_supprimer_saisies
 *
 * @param void
 * @return void
 */
function DB_STRUCTURE_supprimer_saisies()
{
	DB::query(SACOCHE_STRUCTURE_BD_NAME , 'TRUNCATE sacoche_saisie' , null);
}

/**
 * DB_STRUCTURE_supprimer_validations
 *
 * @param void
 * @return void
 */
function DB_STRUCTURE_supprimer_validations()
{
	DB::query(SACOCHE_STRUCTURE_BD_NAME , 'TRUNCATE sacoche_jointure_user_entree' , null);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , 'TRUNCATE sacoche_jointure_user_pilier' , null);
}

/**
 * DB_STRUCTURE_supprimer_periode
 *
 * @param int $periode_id
 * @return void
 */
function DB_STRUCTURE_supprimer_periode($periode_id)
{
	$DB_SQL = 'DELETE FROM sacoche_periode ';
	$DB_SQL.= 'WHERE periode_id=:periode_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':periode_id'=>$periode_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// Il faut aussi supprimer les jointures avec les classes
	$DB_SQL = 'DELETE FROM sacoche_jointure_groupe_periode ';
	$DB_SQL.= 'WHERE periode_id=:periode_id ';
	$DB_VAR = array(':periode_id'=>$periode_id);
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// Log de l'action
	ajouter_log_SACoche('Suppression d\'une période ('.$periode_id.').');
}

/**
 * DB_STRUCTURE_supprimer_demande
 * On transmet soit l'id de la demande (1 paramètre) soit l'id de l'élève suivi de l'id de l'item (2 paramètres).
 *
 * @param int      $id1
 * @param bool|int $id2
 * @return void
 */
function DB_STRUCTURE_supprimer_demande($id1,$id2=false)
{
	$DB_SQL = 'DELETE FROM sacoche_demande ';
	if($id2)
	{
		$DB_SQL.= 'WHERE user_id=:eleve_id AND item_id=:item_id ';
		$DB_VAR = array(':eleve_id'=>$id1,':item_id'=>$id2);
	}
	else
	{
		$DB_SQL.= 'WHERE demande_id=:demande_id ';
		$DB_VAR = array(':demande_id'=>$id1);
	}
	$DB_SQL.= 'LIMIT 1';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_STRUCTURE_supprimer_demandes
 *
 * @param bool|string   $listing_demande_id   id des demandes séparées par des virgules, ou "true" pour supprimer toutes les demandes de l'établissement
 * @param bool|int      $nb_demandes          nb de demandes
 * @return void
 */
function DB_STRUCTURE_supprimer_demandes($listing_demande_id,$nb_demandes=false)
{
	$DB_SQL = 'DELETE FROM sacoche_demande ';
	if($listing_demande_id!==true)
	{
		$DB_SQL.= 'WHERE demande_id IN('.$listing_demande_id.') ';
		$DB_SQL.= 'LIMIT '.$nb_demandes;
	}
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_supprimer_jointures_parents_for_eleves
 *
 * @param bool|string   $listing_eleve_id   id des élèves séparés par des virgules
 * @return void
 */
function DB_STRUCTURE_supprimer_jointures_parents_for_eleves($listing_eleve_id)
{
	$DB_SQL = 'DELETE FROM sacoche_jointure_parent_eleve ';
	$DB_SQL.= 'WHERE eleve_id IN('.$listing_eleve_id.') ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_STRUCTURE_supprimer_utilisateur
 *
 * @param int    $user_id
 * @param string $user_profil   eleve | parent | professeur | directeur | administrateur
 * @return void
 */
function DB_STRUCTURE_supprimer_utilisateur($user_id,$user_profil)
{
	$DB_VAR = array(':user_id'=>$user_id);
	$DB_SQL = 'DELETE FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_id=:user_id ';
	$DB_SQL.= 'LIMIT 1';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	if( ($user_profil=='eleve') || ($user_profil=='professeur') )
	{
		$DB_SQL = 'DELETE FROM sacoche_jointure_user_groupe ';
		$DB_SQL.= 'WHERE user_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
	if($user_profil=='eleve')
	{
		$DB_SQL = 'DELETE FROM sacoche_jointure_parent_eleve ';
		$DB_SQL.= 'WHERE eleve_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$DB_SQL = 'DELETE FROM sacoche_saisie ';
		$DB_SQL.= 'WHERE eleve_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$DB_SQL = 'DELETE FROM sacoche_demande ';
		$DB_SQL.= 'WHERE user_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
	if($user_profil=='parent')
	{
		$DB_SQL = 'DELETE FROM sacoche_jointure_parent_eleve ';
		$DB_SQL.= 'WHERE parent_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$DB_SQL = 'DELETE FROM sacoche_parent_adresse ';
		$DB_SQL.= 'WHERE parent_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
	if($user_profil=='professeur')
	{
		$DB_SQL = 'DELETE FROM sacoche_jointure_user_matiere ';
		$DB_SQL.= 'WHERE user_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$DB_SQL = 'DELETE sacoche_jointure_devoir_item FROM sacoche_jointure_devoir_item ';
		$DB_SQL.= 'LEFT JOIN sacoche_devoir USING (devoir_id) ';
		$DB_SQL.= 'WHERE prof_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$DB_SQL = 'DELETE sacoche_groupe FROM sacoche_groupe ';
		$DB_SQL.= 'LEFT JOIN sacoche_devoir ON sacoche_groupe.groupe_prof_id=sacoche_devoir.prof_id ';
		$DB_SQL.= 'WHERE groupe_prof_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$DB_SQL = 'DELETE FROM sacoche_devoir ';
		$DB_SQL.= 'WHERE prof_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		$DB_SQL = 'UPDATE sacoche_saisie ';
		$DB_SQL.= 'SET prof_id=0 ';
		$DB_SQL.= 'WHERE prof_id=:user_id';
		DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
	// Log de l'action
	ajouter_log_SACoche('Suppression d\'un utilisateur ('.$user_profil.' '.$user_id.').');
}

/**
 * DB_STRUCTURE_supprimer_referentiel_matiere_niveau
 *
 * @param int $matiere_id
 * @param int $niveau_id    facultatif : si non fourni, tous les niveaux seront concernés
 * @return void
 */
function DB_STRUCTURE_supprimer_referentiel_matiere_niveau($matiere_id,$niveau_id=false)
{
	$DB_SQL = 'DELETE sacoche_referentiel, sacoche_referentiel_domaine, sacoche_referentiel_theme, sacoche_referentiel_item, sacoche_jointure_devoir_item, sacoche_saisie ';
	$DB_SQL.= 'FROM sacoche_referentiel ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (matiere_id,niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_devoir_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_saisie USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_demande USING (matiere_id,item_id) ';
	$DB_SQL.= 'WHERE matiere_id=:matiere_id ';
	$DB_VAR = array(':matiere_id'=>$matiere_id);
	if($niveau_id)
	{
		$DB_SQL.= 'AND niveau_id=:niveau_id ';
		$DB_VAR[':niveau_id'] = $niveau_id;
	}
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// Log de l'action
	ajouter_log_SACoche('Suppression d\'un référentiel ('.$matiere_id.' / '.$niveau_id.').');
}

/**
 * DB_STRUCTURE_supprimer_mono_structure
 *
 * @param void
 * @return void
 */
function DB_STRUCTURE_supprimer_mono_structure()
{
	global $CHEMIN_MYSQL;
	// Supprimer les tables de la base (pas la base elle-même au cas où elle serait partagée avec autre chose)
	$tab_tables = array();
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME,'SHOW TABLE STATUS LIKE "sacoche_%"');
	foreach($DB_TAB as $DB_ROW)
	{
		$tab_tables[] = $DB_ROW['Name'];
	}
	DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE '.implode(', ',$tab_tables) );
	// Supprimer le fichier de connexion
	unlink($CHEMIN_MYSQL.'serveur_sacoche_structure.php');
	// Supprimer les dossiers de fichiers temporaires par établissement : vignettes verticales, flux RSS des demandes, cookies des choix de formulaires
	Supprimer_Dossier('./__tmp/badge/'.'0');
	Supprimer_Dossier('./__tmp/cookie/'.'0');
	Supprimer_Dossier('./__tmp/rss/'.'0');
	// Supprimer les éventuels fichiers de blocage
	@unlink($CHEMIN_CONFIG.'blocage_webmestre_0.txt');
	@unlink($CHEMIN_CONFIG.'blocage_administrateur_0.txt');
	@unlink($CHEMIN_CONFIG.'blocage_automate_0.txt');
	// Log de l'action
	ajouter_log_SACoche('Résiliation de l\'inscription.');
}

/**
 * DB_STRUCTURE_creer_remplir_tables_structure
 *
 * @param string $dossier_requetes   './_sql/structure/' ou './_sql/webmestre/'
 * @return void
 */
function DB_STRUCTURE_creer_remplir_tables_structure($dossier_requetes)
{
	$tab_files = Lister_Contenu_Dossier($dossier_requetes);
	foreach($tab_files as $file)
	{
		$extension = pathinfo($file,PATHINFO_EXTENSION);
		if($extension=='sql')
		{
			$requetes = file_get_contents($dossier_requetes.$file);
			DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
			/*
			La classe PDO a un bug. Si on envoie plusieurs requêtes d'un coup ça passe, mais si on recommence juste après alors on récolte : "Cannot execute queries while other unbuffered queries are active.  Consider using PDOStatement::fetchAll().  Alternatively, if your code is only ever going to run against mysql, you may enable query buffering by setting the PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute."
			La seule issue est de fermer la connexion après chaque requête multiple en utilisant exceptionnellement la méthode ajouté par SebR suite à mon signalement : DB::close(nom_de_la_connexion);
			*/
			DB::close(SACOCHE_STRUCTURE_BD_NAME);
		}
	}
}

/**
 * DB_STRUCTURE_optimiser_tables_structure
 *
 * @param void
 * @return void
 */
function DB_STRUCTURE_optimiser_tables_structure()
{
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SHOW TABLE STATUS LIKE "sacoche_%"');
	if(count($DB_TAB))
	{
		foreach($DB_TAB as $DB_ROW)
		{
			DB::query(SACOCHE_STRUCTURE_BD_NAME , 'OPTIMIZE TABLE '.$DB_ROW['Name']);
		}
	}
}

/**
 * Recherche et correction d'anomalies : numérotation des items d'un thème, ou des thèmes d'un domaine
 *
 * @param void
 * @return array   tableau avec label et commentaire pour chaque recherche
 */
function DB_STRUCTURE_corriger_numerotations()
{
	function make_where($champ,$valeur)
	{
		return $champ.'='.$valeur;
	}
	$tab_bilan = array();
	$tab_recherche = array();
	$tab_recherche[] = array( 'contenant_nom'=>'référentiel' , 'contenant_tab_champs'=>array('matiere_id','niveau_id') , 'element_nom'=>'domaine' , 'element_champ'=>'domaine' , 'debut'=>1 , 'decalage'=>0 );
	$tab_recherche[] = array( 'contenant_nom'=>'domaine'     , 'contenant_tab_champs'=>array('domaine_id')             , 'element_nom'=>'thème'   , 'element_champ'=>'theme'   , 'debut'=>1 , 'decalage'=>0 );
	$tab_recherche[] = array( 'contenant_nom'=>'thème'       , 'contenant_tab_champs'=>array('theme_id')               , 'element_nom'=>'item'    , 'element_champ'=>'item'    , 'debut'=>0 , 'decalage'=>1 );
	foreach($tab_recherche as $tab_donnees)
	{
		extract($tab_donnees,EXTR_OVERWRITE);
		// numéros en double
		$DB_SQL = 'SELECT DISTINCT CONCAT('.implode(',",",',$contenant_tab_champs).') AS contenant_id , COUNT('.$element_champ.'_id) AS nombre ';
		$DB_SQL.= 'FROM sacoche_referentiel_'.$element_champ.' ';
		$DB_SQL.= 'GROUP BY '.implode(',',$contenant_tab_champs).','.$element_champ.'_ordre ';
		$DB_SQL.= 'HAVING nombre>1 ';
		$DB_TAB1 = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null , TRUE);
		// numéros manquants ou décalés
		$DB_SQL = 'SELECT DISTINCT CONCAT('.implode(',",",',$contenant_tab_champs).') AS contenant_id , MAX('.$element_champ.'_ordre) AS maximum , COUNT('.$element_champ.'_id) AS nombre ';
		$DB_SQL.= 'FROM sacoche_referentiel_'.$element_champ.' ';
		$DB_SQL.= 'GROUP BY '.implode(',',$contenant_tab_champs).' ';
		$DB_SQL.= 'HAVING nombre!=maximum+'.$decalage.' ';
		$DB_TAB2 = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null , TRUE);
		// en réunissant les 2 requêtes on a repéré tous les problèmes possibles
		$tab_bugs = array_unique( array_merge( array_keys($DB_TAB1) , array_keys($DB_TAB2) ) );
		$nb_bugs = count($tab_bugs);
		if($nb_bugs)
		{
			foreach($tab_bugs as $contenant_id)
			{
				$element_ordre = $debut;
				$contenant_tab_valeur = explode(',',$contenant_id);
				$tab_where = array_map('make_where', $contenant_tab_champs, $contenant_tab_valeur);
				$DB_SQL = 'SELECT '.$element_champ.'_id ';
				$DB_SQL.= 'FROM sacoche_referentiel_'.$element_champ.' ';
				$DB_SQL.= 'WHERE '.implode(' AND ',$tab_where).' ';
				$DB_SQL.= 'ORDER BY '.$element_champ.'_ordre ASC ';
				$DB_COL = DB::queryCol(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
				foreach($DB_COL as $element_champ_id)
				{
					$DB_SQL = 'UPDATE sacoche_referentiel_'.$element_champ.' ';
					$DB_SQL.= 'SET '.$element_champ.'_ordre='.$element_ordre.' ';
					$DB_SQL.= 'WHERE '.$element_champ.'_id='.$element_champ_id.' ';
					$DB_SQL.= 'LIMIT 1';
					DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
					$element_ordre++;
				}
			}
		}
		$message = (!$nb_bugs) ? 'rien à signaler' : ( ($nb_bugs>1) ? $nb_bugs.' '.$contenant_nom.'s dont le contenu a été renuméroté' : '1 '.$contenant_nom.' dont le contenu a été renuméroté' ) ;
		$classe  = (!$nb_bugs) ? 'valide' : 'alerte' ;
		$tab_bilan[] = '<label class="'.$classe.'">'.ucfirst($element_nom).'s des '.$contenant_nom.'s : '.$message.'.</label>';
	}
	return $tab_bilan;
}

/**
 * DB_STRUCTURE_corriger_anomalies
 *
 * @param void
 * @return array   tableau avec label et commentaire pour chaque recherche
 */
function DB_STRUCTURE_corriger_anomalies()
{
	$tab_bilan = array();
	// Recherche d'anomalies : référentiels associés à une matière supprimée
	$DB_SQL = 'DELETE sacoche_referentiel,sacoche_referentiel_domaine, sacoche_referentiel_theme, sacoche_referentiel_item, sacoche_jointure_devoir_item, sacoche_saisie, sacoche_demande ';
	$DB_SQL.= 'FROM sacoche_referentiel ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (matiere_id,niveau_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_devoir_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_saisie USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_demande USING (item_id) ';
	$DB_SQL.= 'WHERE sacoche_matiere.matiere_id IS NULL ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Référentiels : '.$message.'.</label>';
	// Recherche d'anomalies : domaines associés à une matière supprimée...
	$DB_SQL = 'DELETE sacoche_referentiel_domaine, sacoche_referentiel_theme, sacoche_referentiel_item, sacoche_jointure_devoir_item, sacoche_saisie, sacoche_demande ';
	$DB_SQL.= 'FROM sacoche_referentiel_domaine ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_devoir_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_saisie USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_demande USING (item_id) ';
	$DB_SQL.= 'WHERE sacoche_matiere.matiere_id IS NULL ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Domaines (arborescence) : '.$message.'.</label>';
	// Recherche d'anomalies : thèmes associés à un domaine supprimé...
	$DB_SQL = 'DELETE sacoche_referentiel_theme, sacoche_referentiel_item, sacoche_jointure_devoir_item, sacoche_saisie, sacoche_demande ';
	$DB_SQL.= 'FROM sacoche_referentiel_theme ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_domaine USING (domaine_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_devoir_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_saisie USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_demande USING (item_id) ';
	$DB_SQL.= 'WHERE sacoche_referentiel_domaine.domaine_id IS NULL ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Thèmes (arborescence) : '.$message.'.</label>';
	// Recherche d'anomalies : items associés à un thème supprimé...
	$DB_SQL = 'DELETE sacoche_referentiel_item, sacoche_jointure_devoir_item, sacoche_saisie, sacoche_demande ';
	$DB_SQL.= 'FROM sacoche_referentiel_item ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_theme USING (theme_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_devoir_item USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_saisie USING (item_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_demande USING (item_id) ';
	$DB_SQL.= 'WHERE sacoche_referentiel_theme.theme_id IS NULL ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Items (arborescence) : '.$message.'.</label>';
	// Recherche d'anomalies : demandes d'évaluations associées à un user ou une matière ou un item supprimé...
	$DB_SQL = 'DELETE sacoche_demande ';
	$DB_SQL.= 'FROM sacoche_demande ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'WHERE ( (sacoche_user.user_id IS NULL) OR (sacoche_matiere.matiere_id IS NULL) OR (sacoche_referentiel_item.item_id IS NULL) ) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Demandes d\'évaluations : '.$message.'.</label>';
	// Recherche d'anomalies : saisies de scores associées à un élève ou un item supprimé...
	// Attention, on ne teste pas le professeur ou le devoir, car les saisies sont conservées au delà
	$DB_SQL = 'DELETE sacoche_saisie ';
	$DB_SQL.= 'FROM sacoche_saisie ';
	$DB_SQL.= 'LEFT JOIN sacoche_user ON sacoche_saisie.eleve_id=sacoche_user.user_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'WHERE ( (sacoche_user.user_id IS NULL) OR (sacoche_referentiel_item.item_id IS NULL) ) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Scores : '.$message.'.</label>';
	// Recherche d'anomalies : devoirs associés à un prof ou un groupe supprimé...
	$DB_SQL = 'DELETE sacoche_devoir, sacoche_jointure_devoir_item ';
	$DB_SQL.= 'FROM sacoche_devoir ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_devoir_item USING (devoir_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_user ON sacoche_devoir.prof_id=sacoche_user.user_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'WHERE ( (sacoche_user.user_id IS NULL) OR (sacoche_groupe.groupe_id IS NULL) ) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Évaluations : '.$message.'.</label>';
	// Recherche d'anomalies : jointures période/groupe associées à une période ou un groupe supprimé...
	$DB_SQL = 'DELETE sacoche_jointure_groupe_periode ';
	$DB_SQL.= 'FROM sacoche_jointure_groupe_periode ';
	$DB_SQL.= 'LEFT JOIN sacoche_periode USING (periode_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'WHERE ( (sacoche_periode.periode_id IS NULL) OR (sacoche_groupe.groupe_id IS NULL) ) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Jointures période/groupe : '.$message.'.</label>';
	// Recherche d'anomalies : jointures user/groupe associées à un user ou un groupe supprimé...
	$DB_SQL = 'DELETE sacoche_jointure_user_groupe ';
	$DB_SQL.= 'FROM sacoche_jointure_user_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'WHERE ( (sacoche_user.user_id IS NULL) OR (sacoche_groupe.groupe_id IS NULL) ) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Jointures utilisateur/groupe : '.$message.'.</label>';
	// Recherche d'anomalies : jointures user/matière associées à un user ou une matière supprimée...
	$DB_SQL = 'DELETE sacoche_jointure_user_matiere ';
	$DB_SQL.= 'FROM sacoche_jointure_user_matiere ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'WHERE ( (sacoche_user.user_id IS NULL) OR (sacoche_matiere.matiere_id IS NULL) ) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Jointures utilisateur/matière : '.$message.'.</label>';
	// Recherche d'anomalies : jointures devoir/item associées à un devoir ou un item supprimé...
	$DB_SQL = 'DELETE sacoche_jointure_devoir_item ';
	$DB_SQL.= 'FROM sacoche_jointure_devoir_item ';
	$DB_SQL.= 'LEFT JOIN sacoche_devoir USING (devoir_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_referentiel_item USING (item_id) ';
	$DB_SQL.= 'WHERE ( (sacoche_devoir.devoir_id IS NULL) OR (sacoche_referentiel_item.item_id IS NULL) ) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Jointures évaluation/item : '.$message.'.</label>';
	// Recherche d'anomalies : adresse associée à un parent supprimé...
	$DB_SQL = 'DELETE sacoche_parent_adresse ';
	$DB_SQL.= 'FROM sacoche_parent_adresse ';
	$DB_SQL.= 'LEFT JOIN sacoche_user ON sacoche_parent_adresse.parent_id=sacoche_user.user_id ';
	$DB_SQL.= 'WHERE user_id IS NULL ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Jointures parent/adresse : '.$message.'.</label>';
	// Recherche d'anomalies : jointures parent/élève associées à un parent ou un élève supprimé...
	$DB_SQL = 'DELETE sacoche_jointure_parent_eleve ';
	$DB_SQL.= 'FROM sacoche_jointure_parent_eleve ';
	$DB_SQL.= 'LEFT JOIN sacoche_user AS parent ON sacoche_jointure_parent_eleve.parent_id=parent.user_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_user AS eleve ON sacoche_jointure_parent_eleve.eleve_id=eleve.user_id ';
	$DB_SQL.= 'WHERE ( (parent.user_id IS NULL) OR (eleve.user_id IS NULL) ) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Jointures parent/enfant : '.$message.'.</label>';
	// Recherche d'anomalies : élèves associés à une classe supprimée...
	// Attention, l'id de classe à 0 est normal pour un élève non affecté ou un autre statut
	$DB_SQL = 'UPDATE sacoche_user ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe ON sacoche_user.eleve_classe_id=sacoche_groupe.groupe_id ';
	$DB_SQL.= 'SET sacoche_user.eleve_classe_id=0 ';
	$DB_SQL.= 'WHERE ( (sacoche_user.eleve_classe_id!=0) AND (sacoche_groupe.groupe_id IS NULL) ) ';
	DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	$nb_modifs = DB::rowCount(SACOCHE_STRUCTURE_BD_NAME);
	$message = (!$nb_modifs) ? 'rien à signaler' : ( ($nb_modifs>1) ? $nb_modifs.' anomalies supprimées' : '1 anomalie supprimée' ) ;
	$classe  = (!$nb_modifs) ? 'valide' : 'alerte' ;
	$tab_bilan[] = '<label class="'.$classe.'">Jointures élève/classe : '.$message.'.</label>';
	return $tab_bilan;
}

/**
 * Retourner un tableau [valeur texte] des matières de l'établissement (communes choisies ou spécifiques ajoutées)
 *
 * @param string $listing_matieres_communes   id des matières communes séparées par des virgules
 * @param bool   $transversal                 inclure ou pas la matière tranversale à la liste
 * @return array|string
 */
function DB_STRUCTURE_OPT_matieres_etabl($listing_matieres_communes,$transversal)
{
	$DB_SQL = 'SELECT matiere_id AS valeur, matiere_nom AS texte FROM sacoche_matiere ';
	$DB_SQL.= 'WHERE matiere_partage=0 '; // les matières spécifiques
	if($listing_matieres_communes)
	{
		$DB_SQL.= ($transversal) ? 'OR matiere_id IN('.$listing_matieres_communes.') ' : 'OR ( matiere_id IN('.$listing_matieres_communes.')  AND matiere_transversal=0 ) ' ;
	}
	$DB_SQL.= 'ORDER BY matiere_nom ASC';
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	return count($DB_TAB) ? $DB_TAB : 'Aucune matière n\'est rattachée à l\'établissement !' ;
}

/**
 * Retourner un tableau [valeur texte] des matières communes (choisies ou pas par l'établissement)
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_OPT_matieres_communes()
{
	$GLOBALS['tab_select_option_first'] = array(0,'Toutes les matières','');

	$DB_SQL = 'SELECT matiere_id AS valeur, matiere_nom AS texte FROM sacoche_matiere ';
	$DB_SQL.= 'WHERE matiere_partage=:partage ';
	$DB_SQL.= 'ORDER BY matiere_nom ASC';
	$DB_VAR = array(':partage'=>1);
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * Retourner un tableau [valeur texte info] des matières du professeur identifié ; info représente le nb de demandes (utilisé par ailleurs)
 *
 * @param string $listing_matieres_communes   id des matières communes séparées par des virgules
 * @param int $user_id
 * @return array|string
 */
function DB_STRUCTURE_OPT_matieres_professeur($listing_matieres_communes,$user_id)
{
	$DB_SQL = 'SELECT matiere_id AS valeur, matiere_nom AS texte, matiere_nb_demandes AS info FROM sacoche_jointure_user_matiere ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'WHERE (matiere_id IN('.$listing_matieres_communes.') OR matiere_partage=:partage) AND user_id=:user_id '; // Test matiere car un prof peut être encore relié à des matières décochées par l'admin.
	$DB_SQL.= 'ORDER BY matiere_nom ASC';
	$DB_VAR = array(':user_id'=>$user_id,':partage'=>0);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Vous n\'êtes pas rattaché à une matière !' ;
}

/**
 * Retourner un tableau [valeur texte info] des matières d'un élève identifié ; info représente le nb de demandes (utilisé par ailleurs)
 *
 * @param string $listing_matieres_communes   id des matières communes séparées par des virgules
 * @param int $user_id
 * @return array|string
 */
function DB_STRUCTURE_OPT_matieres_eleve($listing_matieres_communes,$user_id)
{
	// On connait la classe ($_SESSION['ELEVE_CLASSE_ID']), donc on commence par récupérer les groupes éventuels associés à l'élève
	// DB::query(SACOCHE_STRUCTURE_BD_NAME , 'SET group_concat_max_len = ...'); // Pour lever si besoin une limitation de GROUP_CONCAT (group_concat_max_len est par défaut limité à une chaine de 1024 caractères).
	$DB_SQL = 'SELECT GROUP_CONCAT(DISTINCT groupe_id SEPARATOR ",") AS sacoche_liste_groupe_id ';
	$DB_SQL.= 'FROM sacoche_jointure_user_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'WHERE user_id=:user_id AND groupe_type=:type2 ';
	$DB_SQL.= 'GROUP BY user_id ';
	$DB_VAR = array(':user_id'=>$user_id,':type2'=>'groupe');
	$DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	if( (!$_SESSION['ELEVE_CLASSE_ID']) && (!count($DB_ROW)) )
	{
		// élève sans classe et sans groupe
		return 'Aucune classe et aucun groupe ne vous est affecté !';
	}
	if(!count($DB_ROW))
	{
		$liste_groupes = $_SESSION['ELEVE_CLASSE_ID'];
	}
	elseif(!$_SESSION['ELEVE_CLASSE_ID'])
	{
		$liste_groupes = $DB_ROW['sacoche_liste_groupe_id'];
	}
	else
	{
		$liste_groupes = $_SESSION['ELEVE_CLASSE_ID'].','.$DB_ROW['sacoche_liste_groupe_id'];
	}
	// Ensuite on récupère les matières des professeurs (actifs !) qui sont associés à la liste des groupes récupérés
	$DB_SQL = 'SELECT matiere_id AS valeur, matiere_nom AS texte, matiere_nb_demandes AS info FROM sacoche_user ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_matiere USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'WHERE (matiere_id IN('.$listing_matieres_communes.') OR matiere_partage=:partage) AND groupe_id IN('.$liste_groupes.') AND user_statut=:statut '; // Test matiere car un prof peut être encore relié à des matières décochées par l'admin.
	$DB_SQL.= 'GROUP BY matiere_id ';
	$DB_SQL.= 'ORDER BY matiere_nom ASC';
	$DB_VAR = array(':statut'=>1,':partage'=>0);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Vous n\'avez pas de professeur rattaché à une matière !' ;
}

/**
 * Retourner un tableau [valeur texte] des matières d'une classe ou d'un groupe
 *
 * @param int $groupe_id     id de la classe ou du groupe
 * @return array|string
 */
function DB_STRUCTURE_OPT_matieres_groupe($groupe_id)
{
	// On récupère les matières des professeurs qui sont associés au groupe
	$DB_SQL = 'SELECT matiere_id AS valeur, matiere_nom AS texte FROM sacoche_jointure_user_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_matiere USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_matiere USING (matiere_id) ';
	$DB_SQL.= 'WHERE groupe_id=:groupe_id AND user_profil=:profil ';
	$DB_SQL.= 'GROUP BY matiere_id ';
	$DB_SQL.= 'ORDER BY matiere_nom ASC';
	$DB_VAR = array(':groupe_id'=>$groupe_id,':profil'=>'professeur');
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Il n\'y a pas de professeur du groupe rattaché à une matière !' ;
}

/**
 * Retourner un tableau [valeur texte] des niveaux de l'établissement
 *
 * @param string      $listing_niveaux   id des niveaux séparés par des virgules ; ne peut pas être vide
 * @param string|bool $listing_cycles    id des cycles séparés par des virgules ; false pour ne pas retourner les cycles
 * @return array
 */
function DB_STRUCTURE_OPT_niveaux_etabl($listing_niveaux,$listing_cycles)
{
	$listing = ($listing_cycles) ? $listing_niveaux.','.$listing_cycles : $listing_niveaux ;
	$DB_SQL = 'SELECT niveau_id AS valeur, niveau_nom AS texte FROM sacoche_niveau ';
	$DB_SQL.= 'WHERE niveau_id IN('.$listing.') ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * Retourner un tableau [valeur texte] des niveaux (choisis ou pas par l'établissement)
 *
 * @param void
 * @return array
 */
function DB_STRUCTURE_OPT_niveaux()
{
	$GLOBALS['tab_select_option_first'] = array(0,'Tous les niveaux','');
	$DB_SQL = 'SELECT niveau_id AS valeur, niveau_nom AS texte FROM sacoche_niveau ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC';
	return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
}

/**
 * Retourner un tableau [valeur texte] des paliers du socle de l'établissement
 *
 * @param string $listing_paliers   id des paliers séparés par des virgules
 * @return array|string
 */
function DB_STRUCTURE_OPT_paliers_etabl($listing_paliers)
{
	if($listing_paliers)
	{
		$DB_SQL = 'SELECT palier_id AS valeur, palier_nom AS texte FROM sacoche_socle_palier ';
		$DB_SQL.= 'WHERE palier_id IN('.$listing_paliers.') ';
		$DB_SQL.= 'ORDER BY palier_ordre ASC';
		return DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	}
	else
	{
		return 'Aucun palier du socle commun n\'est rattaché à l\'établissement !';
	}
}

/**
 * Retourner un tableau [valeur texte] des piliers du socle d'un palier donné
 *
 * @param int $palier_id   id du palier
 * @return array|string
 */
function DB_STRUCTURE_OPT_piliers($palier_id)
{
	$GLOBALS['tab_select_option_first'] = array(0,'Toutes les compétences','');
	$DB_SQL = 'SELECT pilier_id AS valeur, pilier_nom AS texte FROM sacoche_socle_pilier ';
	$DB_SQL.= 'WHERE palier_id=:palier_id ';
	$DB_SQL.= 'ORDER BY pilier_ordre ASC';
	$DB_VAR = array(':palier_id'=>$palier_id);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucune compétence trouvée pour ce palier !' ;
}

/**
 * Retourner un tableau [valeur texte] des domaines du socle d'un pilier donné
 *
 * @param int $pilier_id   id du pilier
 * @return array|string
 */
function DB_STRUCTURE_OPT_domaines($pilier_id)
{
	$GLOBALS['tab_select_option_first'] = array(0,'Tous les domaines','');
	$DB_SQL = 'SELECT section_id AS valeur, section_nom AS texte FROM sacoche_socle_section ';
	$DB_SQL.= 'WHERE pilier_id=:pilier_id ';
	$DB_SQL.= 'ORDER BY section_ordre ASC';
	$DB_VAR = array(':pilier_id'=>$pilier_id);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucun domaine trouvé pour ce pilier !' ;
}

/**
 * Retourner un tableau [valeur texte liste_groupe_id] des niveaux de l'établissement pour un élève identifié
 * liste_groupe_id sert pour faire une recherche de l'id de la classe dedans afin de pouvoir préselectionner le niveau de la classe de l'élève
 *
 * @param string      $listing_niveaux   id des niveaux séparés par des virgules ; ne peut pas être vide
 * @param string|bool $listing_cycles    id des cycles séparés par des virgules ; false pour ne pas retourner les cycles
 * @param string $eleve_classe_id        id de la classe de l'élève
 * @return array|string
 */
function DB_STRUCTURE_OPT_niveaux_eleve($listing_niveaux,$listing_cycles,$eleve_classe_id)
{
	$listing = ($listing_cycles) ? $listing_niveaux.','.$listing_cycles : $listing_niveaux ;
	$DB_SQL = 'SELECT niveau_id AS valeur, niveau_nom AS texte, GROUP_CONCAT(groupe_id SEPARATOR ",") AS liste_groupe_id FROM sacoche_niveau ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (niveau_id) ';
	$DB_SQL.= 'WHERE niveau_id IN('.$listing.') ';
	$DB_SQL.= 'GROUP BY niveau_id ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC';
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	// Tester la présence de la classe parmi la liste des id de groupes
	$search_valeur = ','.$eleve_classe_id.',';
	foreach($DB_TAB as $DB_ROW)
	{
		if(mb_substr_count(','.$DB_ROW['liste_groupe_id'].',',$search_valeur))
		{
			$GLOBALS['select_option_selected'] = $DB_ROW['valeur'];
		}
		unset($DB_ROW['liste_groupe_id']);
	}
	return $DB_TAB;
}

/**
 * Retourner un tableau [valeur texte optgroup] des niveaux / classes / groupes d'un établissement
 * optgroup sert à pouvoir regrouper les options
 *
 * @param bool   $sans   TRUE par défaut => pour avoir un choix "Sans classe affectée"
 * @param bool   $tout   TRUE par défaut => pour avoir un choix "Tout l'établissement"
 * @return array|string
 */
function DB_STRUCTURE_OPT_regroupements_etabl($sans=TRUE,$tout=TRUE)
{
	// Options du select : catégorie "Divers"
	$DB_TAB_divers = array();
	if($sans)
	{
		$DB_TAB_divers[] = array('valeur'=>'d1','texte'=>'Sans classe affectée' ,'optgroup'=>'divers');
	}
	if($tout)
	{
		$DB_TAB_divers[] = array('valeur'=>'d2','texte'=>'Tout l\'établissement','optgroup'=>'divers');
	}
	// Options du select : catégorie "Niveaux" (contenant des classes ou des groupes)
	$DB_SQL = 'SELECT CONCAT("n",niveau_id) AS valeur, niveau_nom AS texte, "niveau" AS optgroup FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_type=:type ';
	$DB_SQL.= 'GROUP BY niveau_id ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC';
	$DB_VAR = array(':type'=>'classe');
	$DB_TAB_niveau = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// Options du select : catégories "Classes" et "Groupes"
	$DB_SQL = 'SELECT CONCAT(LEFT(groupe_type,1),groupe_id) AS valeur, groupe_nom AS texte, groupe_type AS optgroup FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_type IN (:type1,:type2) ';
	$DB_SQL.= 'ORDER BY groupe_type ASC, niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':type1'=>'classe',':type2'=>'groupe');
	$DB_TAB_classe_groupe = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	// On assemble tous ces tableaux à la suite
	$DB_TAB = array_merge($DB_TAB_divers,$DB_TAB_niveau,$DB_TAB_classe_groupe);
	$GLOBALS['tab_select_optgroup'] = array('divers'=>'Divers','niveau'=>'Niveaux','classe'=>'Classes','groupe'=>'Groupes');
	return $DB_TAB ;

}

/**
 * Retourner un tableau [valeur texte optgroup] des groupes d'un établissement
 *
 * @param void
 * @return array|string
 */
function DB_STRUCTURE_OPT_groupes_etabl()
{
	$DB_SQL = 'SELECT groupe_id AS valeur, groupe_nom AS texte FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_type=:type ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':type'=>'groupe');
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucun groupe n\'est enregistré !' ;
}

/**
 * Retourner un tableau [valeur texte optgroup] des classes / groupes d'un professeur identifié
 * optgroup sert à pouvoir regrouper les options
 *
 * @param int $user_id
 * @return array|string
 */
function DB_STRUCTURE_OPT_groupes_professeur($user_id)
{
	$GLOBALS['tab_select_option_first'] = array(0,'Fiche générique','');
	$DB_SQL = 'SELECT groupe_id AS valeur, groupe_nom AS texte, groupe_type AS optgroup FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE ( user_id=:user_id OR groupe_prof_id=:user_id ) AND groupe_type!=:type4 ';
	$DB_SQL.= 'GROUP BY groupe_id '; // indispensable pour les groupes de besoin, sinon autant de lignes que de membres du groupe
	$DB_SQL.= 'ORDER BY groupe_type ASC, niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':user_id'=>$user_id,':type4'=>'eval');
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	$GLOBALS['tab_select_optgroup'] = array('classe'=>'Classes','groupe'=>'Groupes','besoin'=>'Besoins');
	return count($DB_TAB) ? $DB_TAB : 'Aucune classe et aucun groupe ne vous sont affectés !' ;
}

/**
 * Retourner un tableau [valeur texte] des groupes de besoin d'un professeur identifié
 *
 * @param int $user_id
 * @return array|string
 */
function DB_STRUCTURE_OPT_besoins_professeur($user_id)
{
	$DB_SQL = 'SELECT groupe_id AS valeur, groupe_nom AS texte FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_prof_id=:user_id AND groupe_type=:type ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':user_id'=>$user_id,':type'=>'besoin');
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Vous n\'avez aucun groupe de besoin enregistré !' ;
}

/**
 * Retourner un tableau [valeur texte] des classes de l'établissement
 *
 * @param void
 * @return array|string
 */
function DB_STRUCTURE_OPT_classes_etabl()
{
	$DB_SQL = 'SELECT groupe_id AS valeur, CONCAT(groupe_nom," (",groupe_ref,")") AS texte FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_type=:type ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':type'=>'classe');
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucune classe n\'est enregistrée !' ;
}

/**
 * Retourner un tableau [valeur texte optgroup] des classes / groupes de l'établissement
 * optgroup sert à pouvoir regrouper les options
 *
 * @param void
 * @return array|string
 */
function DB_STRUCTURE_OPT_classes_groupes_etabl()
{
	$GLOBALS['tab_select_option_first'] = array(0,'Fiche générique','');
	$DB_SQL = 'SELECT groupe_id AS valeur, groupe_nom AS texte, groupe_type AS optgroup FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE groupe_type IN (:type1,:type2) ';
	$DB_SQL.= 'ORDER BY groupe_type ASC, niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':type1'=>'classe',':type2'=>'groupe');
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	$GLOBALS['tab_select_optgroup'] = array('classe'=>'Classes','groupe'=>'Groupes');
	return count($DB_TAB) ? $DB_TAB : 'Aucune classe et aucun groupe ne sont enregistrés !' ;
}

/**
 * Retourner un tableau [valeur texte] des classes où un professeur identifié est professeur principal
 *
 * @param int $user_id
 * @return array|string
 */
function DB_STRUCTURE_OPT_classes_prof_principal($user_id)
{
	$DB_SQL = 'SELECT groupe_id AS valeur, groupe_nom AS texte FROM sacoche_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE ( user_id=:user_id OR groupe_prof_id=:user_id ) AND groupe_type=:type1 AND jointure_pp=:pp ';
	$DB_SQL.= 'GROUP BY groupe_id ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_nom ASC';
	$DB_VAR = array(':user_id'=>$user_id,':type1'=>'classe',':pp'=>1);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Vous n\'êtes professeur principal d\'aucune classe !' ;
}

/**
 * Retourner un tableau [valeur texte] des classes des enfants d'un parent
 *
 * @param int   $parent_id
 * @return array|string
 */
function DB_STRUCTURE_OPT_classes_parent($parent_id)
{
	$DB_SQL = 'SELECT groupe_id AS valeur, groupe_nom AS texte, "classe" AS optgroup ';
	$DB_SQL.= 'FROM sacoche_jointure_parent_eleve ';
	$DB_SQL.= 'LEFT JOIN sacoche_user ON sacoche_jointure_parent_eleve.eleve_id=sacoche_user.user_id ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe ON sacoche_user.eleve_classe_id=sacoche_groupe.groupe_id ';
	$DB_SQL.= 'WHERE parent_id=:parent_id AND user_profil="eleve" AND user_statut=:statut ';
	$DB_SQL.= 'GROUP BY groupe_id ';
	$DB_SQL.= 'ORDER BY resp_legal_num ASC, user_nom ASC, user_prenom ASC ';
	$DB_VAR = array(':parent_id'=>$parent_id,':statut'=>1);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucune classe avec un élève au statut actif associé à ce compte !' ;
}

/**
 * Retourner un tableau [valeur texte] des périodes de l'établissement, indépendamment des rattachements aux classes
 *
 * @param bool   $alerte   affiche un message d'erreur si aucune periode n'est trouvée
 * @return array|string
 */
function DB_STRUCTURE_OPT_periodes_etabl($alerte=FALSE)
{
	$GLOBALS['tab_select_option_first'] = array(0,'Personnalisée','');
	$DB_SQL = 'SELECT periode_id AS valeur, periode_nom AS texte FROM sacoche_periode ';
	$DB_SQL.= 'ORDER BY periode_ordre ASC';
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	return count($DB_TAB) ? $DB_TAB : ( ($alerte) ? 'Aucune période n\'est enregistrée !' : array() ) ;
}

/**
 * Retourner un tableau [valeur texte] des administrateurs (forcément actifs) de l'établissement
 *
 * @param void
 * @return array|string
 */
function DB_STRUCTURE_OPT_administrateurs_etabl()
{
	$DB_SQL = 'SELECT user_id AS valeur, CONCAT(user_nom," ",user_prenom) AS texte ';
	$DB_SQL.= 'FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:statut ';
	$DB_SQL.= 'ORDER BY user_nom ASC, user_prenom ASC';
	$DB_VAR = array(':profil'=>'administrateur',':statut'=>1);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucun administrateur trouvé !' ;
}

/**
 * Retourner un tableau [valeur texte] des directeurs actifs de l'établissement
 *
 * @param void
 * @return array|string
 */
function DB_STRUCTURE_OPT_directeurs_etabl()
{
	$DB_SQL = 'SELECT user_id AS valeur, CONCAT(user_nom," ",user_prenom) AS texte ';
	$DB_SQL.= 'FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:statut ';
	$DB_SQL.= 'ORDER BY user_nom ASC, user_prenom ASC';
	$DB_VAR = array(':profil'=>'directeur',':statut'=>1);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucun directeur trouvé !' ;
}

/**
 * Retourner un tableau [valeur texte] des professeurs actifs de l'établissement
 *
 * @param string $groupe_type   facultatif ; valeur parmi [all] [niveau] [classe] [groupe] 
 * @param int    $groupe_id     facultatif ; id du niveau ou de la classe ou du groupe
 * @return array|string
 */
function DB_STRUCTURE_OPT_professeurs_etabl($groupe_type='all',$groupe_id=0)
{
	$select = 'SELECT user_id AS valeur, CONCAT(user_nom," ",user_prenom) AS texte ';
	$where  = 'WHERE user_profil=:profil AND user_statut=:statut ';
	$ljoin  = '';
	$group  = '';
	$order  = 'ORDER BY user_nom ASC, user_prenom ASC';
	$DB_VAR = array(':profil'=>'professeur',':statut'=>1);
	switch($groupe_type)
	{
		case 'all' :
			$from  = 'FROM sacoche_user ';
			break;
		case 'niveau' :
			$from  = 'FROM sacoche_groupe ';
			$ljoin.= 'LEFT JOIN sacoche_jointure_user_groupe USING (groupe_id) ';
			$ljoin.= 'LEFT JOIN sacoche_user USING (user_id) ';
			$where.= 'AND niveau_id=:niveau ';
			$group.= 'GROUP BY user_id ';
			$DB_VAR[':niveau'] = $groupe_id;
			break;
		case 'classe' :
		case 'groupe' :
			$from  = 'FROM sacoche_jointure_user_groupe ';
			$ljoin.= 'LEFT JOIN sacoche_user USING (user_id) ';
			$where.= 'AND groupe_id=:groupe ';
			$DB_VAR[':groupe'] = $groupe_id;
			break;
	}
	// On peut maintenant assembler les morceaux de la requête !
	$DB_SQL = $select.$from.$ljoin.$where.$group.$order;
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucun professeur trouvé !' ;
}

/**
 * Retourner un tableau [valeur texte] des professeurs et directeurs de l'établissement
 * optgroup sert à pouvoir regrouper les options
 *
 * @param int $user_statut   statut des utilisateurs (1 pour actif, 0 pour inactif)
 * @return array|string
 */
function DB_STRUCTURE_OPT_professeurs_directeurs_etabl($user_statut)
{
	$DB_SQL = 'SELECT user_id AS valeur, CONCAT(user_nom," ",user_prenom) AS texte, user_profil AS optgroup FROM sacoche_user ';
	$DB_SQL.= 'WHERE user_profil IN(:profil1,:profil2) AND user_statut=:user_statut ';
	$DB_SQL.= 'ORDER BY user_profil DESC, user_nom ASC, user_prenom ASC';
	$DB_VAR = array(':profil1'=>'professeur',':profil2'=>'directeur',':user_statut'=>$user_statut);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	$GLOBALS['tab_select_optgroup'] = array('directeur'=>'Directeurs','professeur'=>'Professeurs');
	return count($DB_TAB) ? $DB_TAB : 'Aucun professeur ou directeur trouvé !' ;
}

/**
 * Retourner un tableau [valeur texte] des parents de l'établissement
 *
 * @param int    $user_statut   statut des utilisateurs (1 pour actif, 0 pour inactif)
 * @param string $groupe_type   facultatif ; valeur parmi [all] [niveau] [classe] [groupe] 
 * @param int    $groupe_id     facultatif ; id du niveau ou de la classe ou du groupe
 * @return array|string
 */
function DB_STRUCTURE_OPT_parents_etabl($user_statut,$groupe_type='all',$groupe_id=0)
{
	$select = 'SELECT parent.user_id AS valeur, CONCAT(parent.user_nom," ",parent.user_prenom) AS texte ';
	$where  = 'WHERE parent.user_profil=:profil AND parent.user_statut=:statut ';
	$ljoin  = '';
	$group  = '';
	$order  = 'ORDER BY parent.user_nom ASC, parent.user_prenom ASC';
	$DB_VAR = array(':profil'=>'parent',':statut'=>$user_statut);
	switch($groupe_type)
	{
		case 'all' :
			$from  = 'FROM sacoche_user AS parent ';
			break;
		case 'niveau' :
			$from  = 'FROM sacoche_groupe ';
			$ljoin.= 'LEFT JOIN sacoche_user AS enfant ON sacoche_groupe.groupe_id=enfant.eleve_classe_id ';
			$ljoin.= 'INNER JOIN sacoche_jointure_parent_eleve ON enfant.user_id=sacoche_jointure_parent_eleve.eleve_id ';
			$ljoin.= 'INNER JOIN sacoche_user AS parent ON sacoche_jointure_parent_eleve.parent_id=parent.user_id ';
			$where.= 'AND niveau_id=:niveau ';
			$group.= 'GROUP BY parent.user_id ';
			$DB_VAR[':niveau'] = $groupe_id;
			break;
		case 'classe' :
			$from  = 'FROM sacoche_user AS enfant ';
			$ljoin.= 'INNER JOIN sacoche_jointure_parent_eleve ON enfant.user_id=sacoche_jointure_parent_eleve.eleve_id ';
			$ljoin.= 'INNER JOIN sacoche_user AS parent ON sacoche_jointure_parent_eleve.parent_id=parent.user_id ';
			$where.= 'AND enfant.eleve_classe_id=:groupe ';
			$group.= 'GROUP BY parent.user_id ';
			$DB_VAR[':groupe'] = $groupe_id;
			break;
		case 'groupe' :
			$from  = 'FROM sacoche_jointure_user_groupe ';
			$ljoin.= 'LEFT JOIN sacoche_user AS enfant USING (user_id) ';
			$ljoin.= 'INNER JOIN sacoche_jointure_parent_eleve ON enfant.user_id=sacoche_jointure_parent_eleve.eleve_id ';
			$ljoin.= 'INNER JOIN sacoche_user AS parent ON sacoche_jointure_parent_eleve.parent_id=parent.user_id ';
			$where.= 'AND groupe_id=:groupe ';
			$group.= 'GROUP BY parent.user_id ';
			$DB_VAR[':groupe'] = $groupe_id;
			break;
	}
	// On peut maintenant assembler les morceaux de la requête !
	$DB_SQL = $select.$from.$ljoin.$where.$group.$order;
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucun parent trouvé !' ;
}

/**
 * Retourner un tableau [valeur texte] des élèves d'un regroupement préselectionné
 *
 * @param string $groupe_type   valeur parmi [sdf] [all] [niveau] [classe] [groupe] [besoin] 
 * @param int    $groupe_id     id du niveau ou de la classe ou du groupe
 * @param int    $user_statut   statut des utilisateurs (1 pour actif, 0 pour inactif)
 * @return array|string
 */
function DB_STRUCTURE_OPT_eleves_regroupement($groupe_type,$groupe_id,$user_statut)
{
	if($_SESSION['USER_PROFIL']=='parent')
	{
		$DB_TAB = $_SESSION['OPT_PARENT_ENFANTS'];
		foreach($DB_TAB as $key=>$tab)
		{
			if($tab['classe_id']!=$groupe_id)
			{
				unset($DB_TAB[$key]);
			}
		}
	}
	else
	{
		$DB_SQL = 'SELECT user_id AS valeur, CONCAT(user_nom," ",user_prenom) AS texte FROM sacoche_user ';
		switch ($groupe_type)
		{
			case 'sdf' :	// On veut les élèves non affectés dans une classe
				$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:user_statut AND eleve_classe_id=:classe ';
				$DB_VAR = array(':profil'=>'eleve',':user_statut'=>$user_statut,':classe'=>0);
				break;
			case 'all' :	// On veut tous les élèves de l'établissement
				$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:user_statut ';
				$DB_VAR = array(':profil'=>'eleve',':user_statut'=>$user_statut);
				break;
			case 'niveau' :	// On veut tous les élèves d'un niveau
				$DB_SQL.= 'LEFT JOIN sacoche_groupe ON sacoche_user.eleve_classe_id=sacoche_groupe.groupe_id ';
				$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:user_statut AND niveau_id=:niveau ';
				$DB_VAR = array(':profil'=>'eleve',':user_statut'=>$user_statut,':niveau'=>$groupe_id);
				break;
			case 'classe' :	// On veut tous les élèves d'une classe (on utilise "eleve_classe_id" de "sacoche_user")
				$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:user_statut AND eleve_classe_id=:classe ';
				$DB_VAR = array(':profil'=>'eleve',':user_statut'=>$user_statut,':classe'=>$groupe_id);
				break;
			case 'groupe' :	// On veut tous les élèves d'un groupe (on utilise la jointure de "sacoche_jointure_user_groupe")
			case 'besoin' :	// On veut tous les élèves d'un groupe de besoin (on utilise la jointure de "sacoche_jointure_user_groupe")
				$DB_SQL.= 'LEFT JOIN sacoche_jointure_user_groupe USING (user_id) ';
				$DB_SQL.= 'WHERE user_profil=:profil AND user_statut=:user_statut AND groupe_id=:groupe ';
				$DB_VAR = array(':profil'=>'eleve',':user_statut'=>$user_statut,':groupe'=>$groupe_id);
				break;
		}
		$DB_SQL.= 'ORDER BY user_nom ASC, user_prenom ASC';
		$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	}
	return count($DB_TAB) ? $DB_TAB : 'Aucun élève trouvé !' ;
}

/**
 * Retourner un tableau [valeur texte] des enfants d'un parent
 *
 * @param int   $parent_id
 * @return array|string
 */
function DB_STRUCTURE_OPT_enfants_parent($parent_id)
{
	$DB_SQL = 'SELECT user_id AS valeur, CONCAT(user_nom," ",user_prenom) AS texte, eleve_classe_id AS classe_id ';
	$DB_SQL.= 'FROM sacoche_jointure_parent_eleve ';
	$DB_SQL.= 'LEFT JOIN sacoche_user ON sacoche_jointure_parent_eleve.eleve_id=sacoche_user.user_id ';
	$DB_SQL.= 'WHERE parent_id=:parent_id AND user_profil="eleve" AND user_statut=:statut ';
	$DB_SQL.= 'ORDER BY resp_legal_num ASC, user_nom ASC, user_prenom ASC ';
	$DB_VAR = array(':parent_id'=>$parent_id,':statut'=>1);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_TAB) ? $DB_TAB : 'Aucun élève au statut actif n\'est associé à ce compte !' ;
}

?>