<?php



/**
 * Skeleton subclass for performing query and update operations on the 'a_agregation_decompte' table.
 *
 * Table d'agregation des decomptes de demi journees d'absence et de retard
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceAgregationDecomptePeer extends BaseAbsenceAgregationDecomptePeer {
	/**
	 *
	 * V�rifie que l'ensemble de la table d'agr�gation est � jours, pour tous les �l�ves. Corrige automatiquement la table dans certain cas non couteux, sinon renvoi faux
	 *
	 * @param      DateTime $dateDebut date de d�but pour la prise en compte du test
	 * @param      DateTime $dateFin date de fin pour la prise en compte du test
	 * @return		Boolean
	 *
	 */
	public static function checkSynchroAbsenceAgregationTable(DateTime $dateDebut = null, DateTime $dateFin = null) {
		throw new Exception('Not fully tested');
		$debug = false;
		if ($debug) {
			print_r('AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable() called<br/>');
		}
		
		//on initialise les date clone qui seront manipul�s dans l'algoritme, c'est n�cessaire pour ne pas modifier les dates pass�es en param�tre.
		$dateDebutClone = null;
		$dateFinClone = null;
		
		if ($dateDebut != null) {
			if ($debug) {
				print_r('Date d�but '.$dateDebut->format('Y-m-d').'<br/>');
			}
			$dateDebutClone = clone $dateDebut;
			$dateDebutClone->setTime(0,0);
		}
		if ($dateFin != null) {
			if ($debug) {
				print_r('Date fin '.$dateFin->format('Y-m-d').'<br/>');
			}
			$dateFinClone = clone $dateFin;
			$dateFinClone->setTime(23,59);
		}
		
		//on va v�rifier que tout les marqueurs de fin des calculs de mise � jour sont bien pr�sents pour tout les �l�ves
		$query = '
			SELECT distinct eleves.ID_ELEVE
			FROM `eleves` 
			LEFT JOIN (
				SELECT distinct ELEVE_ID
				FROM `a_agregation_decompte`
				WHERE date_demi_jounee IS NULL) as a_agregation_decompte_selection
			ON (eleves.ID_ELEVE=a_agregation_decompte_selection.ELEVE_ID)
			WHERE a_agregation_decompte_selection.ELEVE_ID IS NULL';
		$result = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		if ($num_rows>0 && $num_rows < 50) {
			if ($debug) {
				print_r('Il manque des marqueurs de fin de calcul<br/>');
			}
			//on va corriger la table pour ces �l�ves l�
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
				$eleve = EleveQuery::create()->findOneByIdEleve($row[0]);
				if ($debug) {
					print_r('recalcul pour l eleve '.$eleve->getIdEleve().'<br/>');
				}
				$eleve->checkAndUpdateSynchroAbsenceAgregationTable($dateDebutClone,$dateFinClone);
			}
			//apr�s avoir corrig� on relance le test
			return(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable($dateDebutClone, $dateFinClone));
		} elseif ($num_rows>0) {
			if ($debug) {
				print_r('retourne faux : Il manque trop de marqueurs de fin de calcul<br/>');
			}
			return false;
		}
		
		//conditions sql sur les dates
		$date_saisies_selection = ' 1=1 ';
		$date_agregation_selection = ' 1=1 ';
		if ($dateDebutClone != null) {
			$date_saisies_selection .= ' and a_saisies.fin_abs >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
			$date_agregation_selection .= ' and a_agregation_decompte.DATE_DEMI_JOUNEE >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
		}
		if ($dateFinClone != null) {
			$date_saisies_selection .= ' and a_saisies.debut_abs <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
			$date_agregation_selection .= ' and a_agregation_decompte.DATE_DEMI_JOUNEE <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
		}
				
		//on va v�rifier que tout les �l�ves ont bien le bon nombres entr�es dans la table d'agr�gation pour cette p�riode
		$query = '
			SELECT eleves.ID_ELEVE, count(*) as count_entrees
			FROM `eleves` 
			LEFT JOIN (
				SELECT ELEVE_ID
				FROM `a_agregation_decompte`
				WHERE '.$date_agregation_selection.') as a_agregation_decompte_selection
			ON (eleves.ID_ELEVE=a_agregation_decompte_selection.ELEVE_ID)
			group by eleves.ID_ELEVE';
		$result = mysql_query($query);
		$wrong_eleve = array();
		$nbre_demi_journees=(int)(($dateFinClone->format('U')+3600-$dateDebutClone->format('U'))/(3600*12));
		while($row = mysql_fetch_array($result)){
			if ($row[1]!=$nbre_demi_journees) {
				if ($debug) {
					print_r('Il manque des entrees pour l eleve '.$row[0].'<br/>');
				}
				$wrong_eleve[]=$row[0];
			}
		}
		if (count($wrong_eleve) > 0 && count($wrong_eleve) < 50) {
			//on va corriger la table pour ces �l�ves l�
			foreach($wrong_eleve as $idEleve) {
				$eleve = EleveQuery::create()->findOneByIdEleve($idEleve);
				if ($debug) {
					print_r('recalcul pour l eleve '.$eleve->getIdEleve().'<br/>');
				}
				$eleve->checkAndUpdateSynchroAbsenceAgregationTable($dateDebutClone,$dateFinClone);
			}
			//apr�s avoir corrig� on relance le test
			return(AbsenceAgregationDecomptePeer::checkSynchroAbsenceAgregationTable($dateDebutClone, $dateFinClone));
		} elseif (!empty($wrong_eleve) > 0) {
			if ($debug) {
				print_r('retourne faux : Il manque des saisies sur '.count($wrong_eleve).' eleves<br/>');
			}
			return false;
		}
		
		
		/* on va r�cup�r� trois informations en base de donn�e :
		 * - est-ce qu'il y a bien le marqueur de fin de calcul (entr�e avec a_agregation_decompte.DATE_DEMI_JOUNEE IS NULL)
		 * - est-ce que la date updated_at de mise � jour de la table est bien post�rieure aux date de modification des saisies et autres entr�es
		 * - on va compter le nombre de demi journ�e, elle doivent �tre toutes remplies
		 */
		$query = 'select union_date, updated_at
		
		FROM
			(SELECT updated_at 
			FROM a_agregation_decompte WHERE '.$date_agregation_selection.'	
			ORDER BY updated_at DESC LIMIT 1) as updated_at_select

		LEFT JOIN (
			(SELECT union_date from 
				(SELECT updated_at as union_date FROM a_saisies WHERE a_saisies.deleted_at is null and '.$date_saisies_selection.'
				UNION ALL
					SELECT deleted_at as union_date  FROM a_saisies WHERE a_saisies.deleted_at is not null and '.$date_saisies_selection.'
				UNION ALL
					SELECT a_traitements.updated_at as union_date  FROM a_traitements join j_traitements_saisies on a_traitements.id = j_traitements_saisies.a_traitement_id join a_saisies on a_saisies.id = j_traitements_saisies.a_saisie_id WHERE  a_traitements.deleted_at is null and a_saisies.deleted_at is null and '.$date_saisies_selection.'
				UNION ALL
					SELECT a_traitements.deleted_at as union_date  FROM a_traitements join j_traitements_saisies on a_traitements.id = j_traitements_saisies.a_traitement_id join a_saisies on a_saisies.id = j_traitements_saisies.a_saisie_id WHERE a_traitements.deleted_at is not null and a_saisies.deleted_at is null and '.$date_saisies_selection.'
				
				ORDER BY union_date DESC LIMIT 1
				) AS union_date_union_all_select
			) AS union_date_select
		) ON 1=1;';
			
		$result_query = mysql_query($query);
		if ($result_query === false) {
			echo 'Erreur sur la requete : '.$query.'<br/>'.mysql_error().'<br/>';
			return false;
		}
		$row = mysql_fetch_array($result_query);
		mysql_free_result($result_query);
		if ($row['union_date'] && (!$row['updated_at'] || $row['union_date'] > $row['updated_at'])){//si on a pas de updated_at dans la table d'agr�gation, ou si la date de mise � jour des saisies est post�rieure � updated_at ou 
			if ($debug) {
				print_r('retourne faux : Les date de mise a jour de la table sont trop anciennes<br/>');
			}
			return false;
		} else {
			if ($debug) {
				print_r('retourne vrai<br/>');
			}
			return true;//on ne v�rifie pas le nombre d'entr�e car les dates ne sont pas pr�cis�e
		}
	}
} // AbsenceAgregationDecomptePeer
