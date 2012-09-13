<?php


/**
 * Base class that represents a query for the 'j_eleves_classes' table.
 *
 * Table de jointure entre les eleves et leur classe en fonction de la periode
 *
 * @method     JEleveClasseQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     JEleveClasseQuery orderByIdClasse($order = Criteria::ASC) Order by the id_classe column
 * @method     JEleveClasseQuery orderByPeriode($order = Criteria::ASC) Order by the periode column
 * @method     JEleveClasseQuery orderByRang($order = Criteria::ASC) Order by the rang column
 *
 * @method     JEleveClasseQuery groupByLogin() Group by the login column
 * @method     JEleveClasseQuery groupByIdClasse() Group by the id_classe column
 * @method     JEleveClasseQuery groupByPeriode() Group by the periode column
 * @method     JEleveClasseQuery groupByRang() Group by the rang column
 *
 * @method     JEleveClasseQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JEleveClasseQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JEleveClasseQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JEleveClasseQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     JEleveClasseQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     JEleveClasseQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     JEleveClasseQuery leftJoinClasse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     JEleveClasseQuery rightJoinClasse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     JEleveClasseQuery innerJoinClasse($relationAlias = null) Adds a INNER JOIN clause to the query using the Classe relation
 *
 * @method     JEleveClasse findOne(PropelPDO $con = null) Return the first JEleveClasse matching the query
 * @method     JEleveClasse findOneOrCreate(PropelPDO $con = null) Return the first JEleveClasse matching the query, or a new JEleveClasse object populated from the query conditions when no match is found
 *
 * @method     JEleveClasse findOneByLogin(string $login) Return the first JEleveClasse filtered by the login column
 * @method     JEleveClasse findOneByIdClasse(int $id_classe) Return the first JEleveClasse filtered by the id_classe column
 * @method     JEleveClasse findOneByPeriode(int $periode) Return the first JEleveClasse filtered by the periode column
 * @method     JEleveClasse findOneByRang(int $rang) Return the first JEleveClasse filtered by the rang column
 *
 * @method     array findByLogin(string $login) Return JEleveClasse objects filtered by the login column
 * @method     array findByIdClasse(int $id_classe) Return JEleveClasse objects filtered by the id_classe column
 * @method     array findByPeriode(int $periode) Return JEleveClasse objects filtered by the periode column
 * @method     array findByRang(int $rang) Return JEleveClasse objects filtered by the rang column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJEleveClasseQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseJEleveClasseQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JEleveClasse', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JEleveClasseQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JEleveClasseQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JEleveClasseQuery) {
			return $criteria;
		}
		$query = new JEleveClasseQuery();
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
	 * $obj = $c->findPk(array(12, 34, 56), $con);
	 * </code>
	 *
	 * @param     array[$login, $id_classe, $periode] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JEleveClasse|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = JEleveClassePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(JEleveClassePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    JEleveClasse A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT LOGIN, ID_CLASSE, PERIODE, RANG FROM j_eleves_classes WHERE LOGIN = :p0 AND ID_CLASSE = :p1 AND PERIODE = :p2';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
			$stmt->bindValue(':p2', $key[2], PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new JEleveClasse();
			$obj->hydrate($row);
			JEleveClassePeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1], (string) $key[2])));
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
	 * @return    JEleveClasse|array|mixed the result, formatted by the current formatter
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
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JEleveClassePeer::LOGIN, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JEleveClassePeer::ID_CLASSE, $key[1], Criteria::EQUAL);
		$this->addUsingAlias(JEleveClassePeer::PERIODE, $key[2], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JEleveClassePeer::LOGIN, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JEleveClassePeer::ID_CLASSE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$cton2 = $this->getNewCriterion(JEleveClassePeer::PERIODE, $key[2], Criteria::EQUAL);
			$cton0->addAnd($cton2);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the login column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByLogin('fooValue');   // WHERE login = 'fooValue'
	 * $query->filterByLogin('%fooValue%'); // WHERE login LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $login The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function filterByLogin($login = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($login)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $login)) {
				$login = str_replace('*', '%', $login);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(JEleveClassePeer::LOGIN, $login, $comparison);
	}

	/**
	 * Filter the query on the id_classe column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdClasse(1234); // WHERE id_classe = 1234
	 * $query->filterByIdClasse(array(12, 34)); // WHERE id_classe IN (12, 34)
	 * $query->filterByIdClasse(array('min' => 12)); // WHERE id_classe > 12
	 * </code>
	 *
	 * @see       filterByClasse()
	 *
	 * @param     mixed $idClasse The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function filterByIdClasse($idClasse = null, $comparison = null)
	{
		if (is_array($idClasse) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JEleveClassePeer::ID_CLASSE, $idClasse, $comparison);
	}

	/**
	 * Filter the query on the periode column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPeriode(1234); // WHERE periode = 1234
	 * $query->filterByPeriode(array(12, 34)); // WHERE periode IN (12, 34)
	 * $query->filterByPeriode(array('min' => 12)); // WHERE periode > 12
	 * </code>
	 *
	 * @param     mixed $periode The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function filterByPeriode($periode = null, $comparison = null)
	{
		if (is_array($periode) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JEleveClassePeer::PERIODE, $periode, $comparison);
	}

	/**
	 * Filter the query on the rang column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRang(1234); // WHERE rang = 1234
	 * $query->filterByRang(array(12, 34)); // WHERE rang IN (12, 34)
	 * $query->filterByRang(array('min' => 12)); // WHERE rang > 12
	 * </code>
	 *
	 * @param     mixed $rang The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function filterByRang($rang = null, $comparison = null)
	{
		if (is_array($rang)) {
			$useMinMax = false;
			if (isset($rang['min'])) {
				$this->addUsingAlias(JEleveClassePeer::RANG, $rang['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($rang['max'])) {
				$this->addUsingAlias(JEleveClassePeer::RANG, $rang['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(JEleveClassePeer::RANG, $rang, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve|PropelCollection $eleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(JEleveClassePeer::LOGIN, $eleve->getLogin(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JEleveClassePeer::LOGIN, $eleve->toKeyValue('PrimaryKey', 'Login'), $comparison);
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
	 * @return    JEleveClasseQuery The current query, for fluid interface
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
	 * Filter the query by a related Classe object
	 *
	 * @param     Classe|PropelCollection $classe The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = null)
	{
		if ($classe instanceof Classe) {
			return $this
				->addUsingAlias(JEleveClassePeer::ID_CLASSE, $classe->getId(), $comparison);
		} elseif ($classe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JEleveClassePeer::ID_CLASSE, $classe->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByClasse() only accepts arguments of type Classe or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Classe relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function joinClasse($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Classe');

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
			$this->addJoinObject($join, 'Classe');
		}

		return $this;
	}

	/**
	 * Use the Classe relation Classe object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ClasseQuery A secondary query class using the current class as primary query
	 */
	public function useClasseQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinClasse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Classe', 'ClasseQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JEleveClasse $jEleveClasse Object to remove from the list of results
	 *
	 * @return    JEleveClasseQuery The current query, for fluid interface
	 */
	public function prune($jEleveClasse = null)
	{
		if ($jEleveClasse) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JEleveClassePeer::LOGIN), $jEleveClasse->getLogin(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JEleveClassePeer::ID_CLASSE), $jEleveClasse->getIdClasse(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond2', $this->getAliasedColName(JEleveClassePeer::PERIODE), $jEleveClasse->getPeriode(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BaseJEleveClasseQuery