<?php


/**
 * Base class that represents a query for the 'plugins_autorisations' table.
 *
 * Liste des autorisations pour chaque statut
 *
 * @method     PlugInAutorisationQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     PlugInAutorisationQuery orderByPluginId($order = Criteria::ASC) Order by the plugin_id column
 * @method     PlugInAutorisationQuery orderByFichier($order = Criteria::ASC) Order by the fichier column
 * @method     PlugInAutorisationQuery orderByUserStatut($order = Criteria::ASC) Order by the user_statut column
 * @method     PlugInAutorisationQuery orderByAuth($order = Criteria::ASC) Order by the auth column
 *
 * @method     PlugInAutorisationQuery groupById() Group by the id column
 * @method     PlugInAutorisationQuery groupByPluginId() Group by the plugin_id column
 * @method     PlugInAutorisationQuery groupByFichier() Group by the fichier column
 * @method     PlugInAutorisationQuery groupByUserStatut() Group by the user_statut column
 * @method     PlugInAutorisationQuery groupByAuth() Group by the auth column
 *
 * @method     PlugInAutorisationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     PlugInAutorisationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     PlugInAutorisationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     PlugInAutorisationQuery leftJoinPlugIn($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlugIn relation
 * @method     PlugInAutorisationQuery rightJoinPlugIn($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlugIn relation
 * @method     PlugInAutorisationQuery innerJoinPlugIn($relationAlias = null) Adds a INNER JOIN clause to the query using the PlugIn relation
 *
 * @method     PlugInAutorisation findOne(PropelPDO $con = null) Return the first PlugInAutorisation matching the query
 * @method     PlugInAutorisation findOneOrCreate(PropelPDO $con = null) Return the first PlugInAutorisation matching the query, or a new PlugInAutorisation object populated from the query conditions when no match is found
 *
 * @method     PlugInAutorisation findOneById(int $id) Return the first PlugInAutorisation filtered by the id column
 * @method     PlugInAutorisation findOneByPluginId(int $plugin_id) Return the first PlugInAutorisation filtered by the plugin_id column
 * @method     PlugInAutorisation findOneByFichier(string $fichier) Return the first PlugInAutorisation filtered by the fichier column
 * @method     PlugInAutorisation findOneByUserStatut(string $user_statut) Return the first PlugInAutorisation filtered by the user_statut column
 * @method     PlugInAutorisation findOneByAuth(string $auth) Return the first PlugInAutorisation filtered by the auth column
 *
 * @method     array findById(int $id) Return PlugInAutorisation objects filtered by the id column
 * @method     array findByPluginId(int $plugin_id) Return PlugInAutorisation objects filtered by the plugin_id column
 * @method     array findByFichier(string $fichier) Return PlugInAutorisation objects filtered by the fichier column
 * @method     array findByUserStatut(string $user_statut) Return PlugInAutorisation objects filtered by the user_statut column
 * @method     array findByAuth(string $auth) Return PlugInAutorisation objects filtered by the auth column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BasePlugInAutorisationQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BasePlugInAutorisationQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'PlugInAutorisation', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new PlugInAutorisationQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    PlugInAutorisationQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof PlugInAutorisationQuery) {
			return $criteria;
		}
		$query = new PlugInAutorisationQuery();
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
	 * @return    PlugInAutorisation|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = PlugInAutorisationPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(PlugInAutorisationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    PlugInAutorisation A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID, PLUGIN_ID, FICHIER, USER_STATUT, AUTH FROM plugins_autorisations WHERE ID = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new PlugInAutorisation();
			$obj->hydrate($row);
			PlugInAutorisationPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    PlugInAutorisation|array|mixed the result, formatted by the current formatter
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
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(PlugInAutorisationPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(PlugInAutorisationPeer::ID, $keys, Criteria::IN);
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
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the plugin_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPluginId(1234); // WHERE plugin_id = 1234
	 * $query->filterByPluginId(array(12, 34)); // WHERE plugin_id IN (12, 34)
	 * $query->filterByPluginId(array('min' => 12)); // WHERE plugin_id > 12
	 * </code>
	 *
	 * @see       filterByPlugIn()
	 *
	 * @param     mixed $pluginId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByPluginId($pluginId = null, $comparison = null)
	{
		if (is_array($pluginId)) {
			$useMinMax = false;
			if (isset($pluginId['min'])) {
				$this->addUsingAlias(PlugInAutorisationPeer::PLUGIN_ID, $pluginId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($pluginId['max'])) {
				$this->addUsingAlias(PlugInAutorisationPeer::PLUGIN_ID, $pluginId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::PLUGIN_ID, $pluginId, $comparison);
	}

	/**
	 * Filter the query on the fichier column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFichier('fooValue');   // WHERE fichier = 'fooValue'
	 * $query->filterByFichier('%fooValue%'); // WHERE fichier LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $fichier The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByFichier($fichier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($fichier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $fichier)) {
				$fichier = str_replace('*', '%', $fichier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::FICHIER, $fichier, $comparison);
	}

	/**
	 * Filter the query on the user_statut column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByUserStatut('fooValue');   // WHERE user_statut = 'fooValue'
	 * $query->filterByUserStatut('%fooValue%'); // WHERE user_statut LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $userStatut The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByUserStatut($userStatut = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($userStatut)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $userStatut)) {
				$userStatut = str_replace('*', '%', $userStatut);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::USER_STATUT, $userStatut, $comparison);
	}

	/**
	 * Filter the query on the auth column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAuth('fooValue');   // WHERE auth = 'fooValue'
	 * $query->filterByAuth('%fooValue%'); // WHERE auth LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $auth The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByAuth($auth = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($auth)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $auth)) {
				$auth = str_replace('*', '%', $auth);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::AUTH, $auth, $comparison);
	}

	/**
	 * Filter the query by a related PlugIn object
	 *
	 * @param     PlugIn|PropelCollection $plugIn The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByPlugIn($plugIn, $comparison = null)
	{
		if ($plugIn instanceof PlugIn) {
			return $this
				->addUsingAlias(PlugInAutorisationPeer::PLUGIN_ID, $plugIn->getId(), $comparison);
		} elseif ($plugIn instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(PlugInAutorisationPeer::PLUGIN_ID, $plugIn->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByPlugIn() only accepts arguments of type PlugIn or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the PlugIn relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function joinPlugIn($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('PlugIn');

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
			$this->addJoinObject($join, 'PlugIn');
		}

		return $this;
	}

	/**
	 * Use the PlugIn relation PlugIn object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PlugInQuery A secondary query class using the current class as primary query
	 */
	public function usePlugInQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinPlugIn($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'PlugIn', 'PlugInQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     PlugInAutorisation $plugInAutorisation Object to remove from the list of results
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function prune($plugInAutorisation = null)
	{
		if ($plugInAutorisation) {
			$this->addUsingAlias(PlugInAutorisationPeer::ID, $plugInAutorisation->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BasePlugInAutorisationQuery