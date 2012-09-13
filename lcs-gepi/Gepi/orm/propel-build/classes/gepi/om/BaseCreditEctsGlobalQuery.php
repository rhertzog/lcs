<?php


/**
 * Base class that represents a query for the 'ects_global_credits' table.
 *
 * Objet qui précise la mention globale obtenue pour un eleve
 *
 * @method     CreditEctsGlobalQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CreditEctsGlobalQuery orderByIdEleve($order = Criteria::ASC) Order by the id_eleve column
 * @method     CreditEctsGlobalQuery orderByMention($order = Criteria::ASC) Order by the mention column
 *
 * @method     CreditEctsGlobalQuery groupById() Group by the id column
 * @method     CreditEctsGlobalQuery groupByIdEleve() Group by the id_eleve column
 * @method     CreditEctsGlobalQuery groupByMention() Group by the mention column
 *
 * @method     CreditEctsGlobalQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CreditEctsGlobalQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CreditEctsGlobalQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CreditEctsGlobalQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     CreditEctsGlobalQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     CreditEctsGlobalQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     CreditEctsGlobal findOne(PropelPDO $con = null) Return the first CreditEctsGlobal matching the query
 * @method     CreditEctsGlobal findOneOrCreate(PropelPDO $con = null) Return the first CreditEctsGlobal matching the query, or a new CreditEctsGlobal object populated from the query conditions when no match is found
 *
 * @method     CreditEctsGlobal findOneById(int $id) Return the first CreditEctsGlobal filtered by the id column
 * @method     CreditEctsGlobal findOneByIdEleve(int $id_eleve) Return the first CreditEctsGlobal filtered by the id_eleve column
 * @method     CreditEctsGlobal findOneByMention(string $mention) Return the first CreditEctsGlobal filtered by the mention column
 *
 * @method     array findById(int $id) Return CreditEctsGlobal objects filtered by the id column
 * @method     array findByIdEleve(int $id_eleve) Return CreditEctsGlobal objects filtered by the id_eleve column
 * @method     array findByMention(string $mention) Return CreditEctsGlobal objects filtered by the mention column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCreditEctsGlobalQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseCreditEctsGlobalQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CreditEctsGlobal', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CreditEctsGlobalQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CreditEctsGlobalQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CreditEctsGlobalQuery) {
			return $criteria;
		}
		$query = new CreditEctsGlobalQuery();
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
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 *
	 * @param     array[$id, $id_eleve] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CreditEctsGlobal|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = CreditEctsGlobalPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(CreditEctsGlobalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    CreditEctsGlobal A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID, ID_ELEVE, MENTION FROM ects_global_credits WHERE ID = :p0 AND ID_ELEVE = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new CreditEctsGlobal();
			$obj->hydrate($row);
			CreditEctsGlobalPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
	 * @return    CreditEctsGlobal|array|mixed the result, formatted by the current formatter
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
	 * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(CreditEctsGlobalPeer::ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(CreditEctsGlobalPeer::ID_ELEVE, $key[1], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(CreditEctsGlobalPeer::ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(CreditEctsGlobalPeer::ID_ELEVE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterById(1234); // WHERE id = 1234
	 * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
	 * $query->filterById(array('min' => 12)); // WHERE id > 12
	 * </code>
	 *
	 * @param     mixed $id The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CreditEctsGlobalPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the id_eleve column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdEleve(1234); // WHERE id_eleve = 1234
	 * $query->filterByIdEleve(array(12, 34)); // WHERE id_eleve IN (12, 34)
	 * $query->filterByIdEleve(array('min' => 12)); // WHERE id_eleve > 12
	 * </code>
	 *
	 * @see       filterByEleve()
	 *
	 * @param     mixed $idEleve The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByIdEleve($idEleve = null, $comparison = null)
	{
		if (is_array($idEleve) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CreditEctsGlobalPeer::ID_ELEVE, $idEleve, $comparison);
	}

	/**
	 * Filter the query on the mention column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMention('fooValue');   // WHERE mention = 'fooValue'
	 * $query->filterByMention('%fooValue%'); // WHERE mention LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $mention The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByMention($mention = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($mention)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $mention)) {
				$mention = str_replace('*', '%', $mention);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CreditEctsGlobalPeer::MENTION, $mention, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve|PropelCollection $eleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(CreditEctsGlobalPeer::ID_ELEVE, $eleve->getId(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CreditEctsGlobalPeer::ID_ELEVE, $eleve->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByEleve() only accepts arguments of type Eleve or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Eleve');

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
			$this->addJoinObject($join, 'Eleve');
		}

		return $this;
	}

	/**
	 * Use the Eleve relation Eleve object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery A secondary query class using the current class as primary query
	 */
	public function useEleveQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CreditEctsGlobal $creditEctsGlobal Object to remove from the list of results
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function prune($creditEctsGlobal = null)
	{
		if ($creditEctsGlobal) {
			$this->addCond('pruneCond0', $this->getAliasedColName(CreditEctsGlobalPeer::ID), $creditEctsGlobal->getId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(CreditEctsGlobalPeer::ID_ELEVE), $creditEctsGlobal->getIdEleve(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BaseCreditEctsGlobalQuery