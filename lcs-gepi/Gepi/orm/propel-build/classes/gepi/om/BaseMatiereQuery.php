<?php


/**
 * Base class that represents a query for the 'matieres' table.
 *
 * Matières
 *
 * @method     MatiereQuery orderByMatiere($order = Criteria::ASC) Order by the matiere column
 * @method     MatiereQuery orderByNomComplet($order = Criteria::ASC) Order by the nom_complet column
 * @method     MatiereQuery orderByPriority($order = Criteria::ASC) Order by the priority column
 * @method     MatiereQuery orderByMatiereAid($order = Criteria::ASC) Order by the matiere_aid column
 * @method     MatiereQuery orderByMatiereAtelier($order = Criteria::ASC) Order by the matiere_atelier column
 * @method     MatiereQuery orderByCategorieId($order = Criteria::ASC) Order by the categorie_id column
 *
 * @method     MatiereQuery groupByMatiere() Group by the matiere column
 * @method     MatiereQuery groupByNomComplet() Group by the nom_complet column
 * @method     MatiereQuery groupByPriority() Group by the priority column
 * @method     MatiereQuery groupByMatiereAid() Group by the matiere_aid column
 * @method     MatiereQuery groupByMatiereAtelier() Group by the matiere_atelier column
 * @method     MatiereQuery groupByCategorieId() Group by the categorie_id column
 *
 * @method     MatiereQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     MatiereQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     MatiereQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     MatiereQuery leftJoinCategorieMatiere($relationAlias = null) Adds a LEFT JOIN clause to the query using the CategorieMatiere relation
 * @method     MatiereQuery rightJoinCategorieMatiere($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CategorieMatiere relation
 * @method     MatiereQuery innerJoinCategorieMatiere($relationAlias = null) Adds a INNER JOIN clause to the query using the CategorieMatiere relation
 *
 * @method     MatiereQuery leftJoinJGroupesMatieres($relationAlias = null) Adds a LEFT JOIN clause to the query using the JGroupesMatieres relation
 * @method     MatiereQuery rightJoinJGroupesMatieres($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JGroupesMatieres relation
 * @method     MatiereQuery innerJoinJGroupesMatieres($relationAlias = null) Adds a INNER JOIN clause to the query using the JGroupesMatieres relation
 *
 * @method     MatiereQuery leftJoinJProfesseursMatieres($relationAlias = null) Adds a LEFT JOIN clause to the query using the JProfesseursMatieres relation
 * @method     MatiereQuery rightJoinJProfesseursMatieres($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JProfesseursMatieres relation
 * @method     MatiereQuery innerJoinJProfesseursMatieres($relationAlias = null) Adds a INNER JOIN clause to the query using the JProfesseursMatieres relation
 *
 * @method     Matiere findOne(PropelPDO $con = null) Return the first Matiere matching the query
 * @method     Matiere findOneOrCreate(PropelPDO $con = null) Return the first Matiere matching the query, or a new Matiere object populated from the query conditions when no match is found
 *
 * @method     Matiere findOneByMatiere(string $matiere) Return the first Matiere filtered by the matiere column
 * @method     Matiere findOneByNomComplet(string $nom_complet) Return the first Matiere filtered by the nom_complet column
 * @method     Matiere findOneByPriority(int $priority) Return the first Matiere filtered by the priority column
 * @method     Matiere findOneByMatiereAid(string $matiere_aid) Return the first Matiere filtered by the matiere_aid column
 * @method     Matiere findOneByMatiereAtelier(string $matiere_atelier) Return the first Matiere filtered by the matiere_atelier column
 * @method     Matiere findOneByCategorieId(int $categorie_id) Return the first Matiere filtered by the categorie_id column
 *
 * @method     array findByMatiere(string $matiere) Return Matiere objects filtered by the matiere column
 * @method     array findByNomComplet(string $nom_complet) Return Matiere objects filtered by the nom_complet column
 * @method     array findByPriority(int $priority) Return Matiere objects filtered by the priority column
 * @method     array findByMatiereAid(string $matiere_aid) Return Matiere objects filtered by the matiere_aid column
 * @method     array findByMatiereAtelier(string $matiere_atelier) Return Matiere objects filtered by the matiere_atelier column
 * @method     array findByCategorieId(int $categorie_id) Return Matiere objects filtered by the categorie_id column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseMatiereQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseMatiereQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'Matiere', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new MatiereQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    MatiereQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof MatiereQuery) {
			return $criteria;
		}
		$query = new MatiereQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key.
	 * Propel uses the instance pool to skip the database if the object exists.
	 * Go fast if the query is untouched.
	 *
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    Matiere|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = MatierePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(MatierePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		if ($this->formatter || $this->modelAlias || $this->with || $this->select
		 || $this->selectColumns || $this->asColumns || $this->selectModifiers
		 || $this->map || $this->having || $this->joins) {
			return $this->findPkComplex($key, $con);
		} else {
			return $this->findPkSimple($key, $con);
		}
	}

	/**
	 * Find object by primary key using raw SQL to go fast.
	 * Bypass doSelect() and the object formatter by using generated code.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    Matiere A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT MATIERE, NOM_COMPLET, PRIORITY, MATIERE_AID, MATIERE_ATELIER, CATEGORIE_ID FROM matieres WHERE MATIERE = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new Matiere();
			$obj->hydrate($row);
			MatierePeer::addInstanceToPool($obj, (string) $key);
		}
		$stmt->closeCursor();

		return $obj;
	}

	/**
	 * Find object by primary key.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    Matiere|array|mixed the result, formatted by the current formatter
	 */
	protected function findPkComplex($key, $con)
	{
		// As the query uses a PK condition, no limit(1) is necessary.
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKey($key)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKeys($keys)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->format($stmt);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(MatierePeer::MATIERE, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(MatierePeer::MATIERE, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the matiere column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMatiere('fooValue');   // WHERE matiere = 'fooValue'
	 * $query->filterByMatiere('%fooValue%'); // WHERE matiere LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $matiere The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByMatiere($matiere = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($matiere)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $matiere)) {
				$matiere = str_replace('*', '%', $matiere);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MatierePeer::MATIERE, $matiere, $comparison);
	}

	/**
	 * Filter the query on the nom_complet column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNomComplet('fooValue');   // WHERE nom_complet = 'fooValue'
	 * $query->filterByNomComplet('%fooValue%'); // WHERE nom_complet LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nomComplet The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByNomComplet($nomComplet = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nomComplet)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nomComplet)) {
				$nomComplet = str_replace('*', '%', $nomComplet);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MatierePeer::NOM_COMPLET, $nomComplet, $comparison);
	}

	/**
	 * Filter the query on the priority column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPriority(1234); // WHERE priority = 1234
	 * $query->filterByPriority(array(12, 34)); // WHERE priority IN (12, 34)
	 * $query->filterByPriority(array('min' => 12)); // WHERE priority > 12
	 * </code>
	 *
	 * @param     mixed $priority The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByPriority($priority = null, $comparison = null)
	{
		if (is_array($priority)) {
			$useMinMax = false;
			if (isset($priority['min'])) {
				$this->addUsingAlias(MatierePeer::PRIORITY, $priority['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($priority['max'])) {
				$this->addUsingAlias(MatierePeer::PRIORITY, $priority['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(MatierePeer::PRIORITY, $priority, $comparison);
	}

	/**
	 * Filter the query on the matiere_aid column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMatiereAid('fooValue');   // WHERE matiere_aid = 'fooValue'
	 * $query->filterByMatiereAid('%fooValue%'); // WHERE matiere_aid LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $matiereAid The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByMatiereAid($matiereAid = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($matiereAid)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $matiereAid)) {
				$matiereAid = str_replace('*', '%', $matiereAid);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MatierePeer::MATIERE_AID, $matiereAid, $comparison);
	}

	/**
	 * Filter the query on the matiere_atelier column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMatiereAtelier('fooValue');   // WHERE matiere_atelier = 'fooValue'
	 * $query->filterByMatiereAtelier('%fooValue%'); // WHERE matiere_atelier LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $matiereAtelier The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByMatiereAtelier($matiereAtelier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($matiereAtelier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $matiereAtelier)) {
				$matiereAtelier = str_replace('*', '%', $matiereAtelier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MatierePeer::MATIERE_ATELIER, $matiereAtelier, $comparison);
	}

	/**
	 * Filter the query on the categorie_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCategorieId(1234); // WHERE categorie_id = 1234
	 * $query->filterByCategorieId(array(12, 34)); // WHERE categorie_id IN (12, 34)
	 * $query->filterByCategorieId(array('min' => 12)); // WHERE categorie_id > 12
	 * </code>
	 *
	 * @see       filterByCategorieMatiere()
	 *
	 * @param     mixed $categorieId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByCategorieId($categorieId = null, $comparison = null)
	{
		if (is_array($categorieId)) {
			$useMinMax = false;
			if (isset($categorieId['min'])) {
				$this->addUsingAlias(MatierePeer::CATEGORIE_ID, $categorieId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($categorieId['max'])) {
				$this->addUsingAlias(MatierePeer::CATEGORIE_ID, $categorieId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(MatierePeer::CATEGORIE_ID, $categorieId, $comparison);
	}

	/**
	 * Filter the query by a related CategorieMatiere object
	 *
	 * @param     CategorieMatiere|PropelCollection $categorieMatiere The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByCategorieMatiere($categorieMatiere, $comparison = null)
	{
		if ($categorieMatiere instanceof CategorieMatiere) {
			return $this
				->addUsingAlias(MatierePeer::CATEGORIE_ID, $categorieMatiere->getId(), $comparison);
		} elseif ($categorieMatiere instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(MatierePeer::CATEGORIE_ID, $categorieMatiere->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByCategorieMatiere() only accepts arguments of type CategorieMatiere or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the CategorieMatiere relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function joinCategorieMatiere($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CategorieMatiere');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CategorieMatiere');
		}

		return $this;
	}

	/**
	 * Use the CategorieMatiere relation CategorieMatiere object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CategorieMatiereQuery A secondary query class using the current class as primary query
	 */
	public function useCategorieMatiereQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCategorieMatiere($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CategorieMatiere', 'CategorieMatiereQuery');
	}

	/**
	 * Filter the query by a related JGroupesMatieres object
	 *
	 * @param     JGroupesMatieres $jGroupesMatieres  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByJGroupesMatieres($jGroupesMatieres, $comparison = null)
	{
		if ($jGroupesMatieres instanceof JGroupesMatieres) {
			return $this
				->addUsingAlias(MatierePeer::MATIERE, $jGroupesMatieres->getIdMatiere(), $comparison);
		} elseif ($jGroupesMatieres instanceof PropelCollection) {
			return $this
				->useJGroupesMatieresQuery()
				->filterByPrimaryKeys($jGroupesMatieres->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJGroupesMatieres() only accepts arguments of type JGroupesMatieres or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JGroupesMatieres relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function joinJGroupesMatieres($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JGroupesMatieres');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'JGroupesMatieres');
		}

		return $this;
	}

	/**
	 * Use the JGroupesMatieres relation JGroupesMatieres object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JGroupesMatieresQuery A secondary query class using the current class as primary query
	 */
	public function useJGroupesMatieresQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJGroupesMatieres($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JGroupesMatieres', 'JGroupesMatieresQuery');
	}

	/**
	 * Filter the query by a related JProfesseursMatieres object
	 *
	 * @param     JProfesseursMatieres $jProfesseursMatieres  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByJProfesseursMatieres($jProfesseursMatieres, $comparison = null)
	{
		if ($jProfesseursMatieres instanceof JProfesseursMatieres) {
			return $this
				->addUsingAlias(MatierePeer::MATIERE, $jProfesseursMatieres->getIdMatiere(), $comparison);
		} elseif ($jProfesseursMatieres instanceof PropelCollection) {
			return $this
				->useJProfesseursMatieresQuery()
				->filterByPrimaryKeys($jProfesseursMatieres->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJProfesseursMatieres() only accepts arguments of type JProfesseursMatieres or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JProfesseursMatieres relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function joinJProfesseursMatieres($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JProfesseursMatieres');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'JProfesseursMatieres');
		}

		return $this;
	}

	/**
	 * Use the JProfesseursMatieres relation JProfesseursMatieres object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JProfesseursMatieresQuery A secondary query class using the current class as primary query
	 */
	public function useJProfesseursMatieresQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJProfesseursMatieres($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JProfesseursMatieres', 'JProfesseursMatieresQuery');
	}

	/**
	 * Filter the query by a related Groupe object
	 * using the j_groupes_matieres table as cross reference
	 *
	 * @param     Groupe $groupe the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJGroupesMatieresQuery()
			->filterByGroupe($groupe, $comparison)
			->endUse();
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 * using the j_professeurs_matieres table as cross reference
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function filterByProfesseur($utilisateurProfessionnel, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJProfesseursMatieresQuery()
			->filterByProfesseur($utilisateurProfessionnel, $comparison)
			->endUse();
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Matiere $matiere Object to remove from the list of results
	 *
	 * @return    MatiereQuery The current query, for fluid interface
	 */
	public function prune($matiere = null)
	{
		if ($matiere) {
			$this->addUsingAlias(MatierePeer::MATIERE, $matiere->getMatiere(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseMatiereQuery