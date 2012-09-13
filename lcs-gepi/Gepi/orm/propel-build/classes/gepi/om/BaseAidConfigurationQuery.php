<?php


/**
 * Base class that represents a query for the 'aid_config' table.
 *
 * Liste des categories d'AID (Activites inter-Disciplinaires)
 *
 * @method     AidConfigurationQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     AidConfigurationQuery orderByNomComplet($order = Criteria::ASC) Order by the nom_complet column
 * @method     AidConfigurationQuery orderByNoteMax($order = Criteria::ASC) Order by the note_max column
 * @method     AidConfigurationQuery orderByOrderDisplay1($order = Criteria::ASC) Order by the order_display1 column
 * @method     AidConfigurationQuery orderByOrderDisplay2($order = Criteria::ASC) Order by the order_display2 column
 * @method     AidConfigurationQuery orderByTypeNote($order = Criteria::ASC) Order by the type_note column
 * @method     AidConfigurationQuery orderByDisplayBegin($order = Criteria::ASC) Order by the display_begin column
 * @method     AidConfigurationQuery orderByDisplayEnd($order = Criteria::ASC) Order by the display_end column
 * @method     AidConfigurationQuery orderByMessage($order = Criteria::ASC) Order by the message column
 * @method     AidConfigurationQuery orderByDisplayNom($order = Criteria::ASC) Order by the display_nom column
 * @method     AidConfigurationQuery orderByIndiceAid($order = Criteria::ASC) Order by the indice_aid column
 * @method     AidConfigurationQuery orderByDisplayBulletin($order = Criteria::ASC) Order by the display_bulletin column
 * @method     AidConfigurationQuery orderByBullSimplifie($order = Criteria::ASC) Order by the bull_simplifie column
 * @method     AidConfigurationQuery orderByOutilsComplementaires($order = Criteria::ASC) Order by the outils_complementaires column
 * @method     AidConfigurationQuery orderByFeuillePresence($order = Criteria::ASC) Order by the feuille_presence column
 *
 * @method     AidConfigurationQuery groupByNom() Group by the nom column
 * @method     AidConfigurationQuery groupByNomComplet() Group by the nom_complet column
 * @method     AidConfigurationQuery groupByNoteMax() Group by the note_max column
 * @method     AidConfigurationQuery groupByOrderDisplay1() Group by the order_display1 column
 * @method     AidConfigurationQuery groupByOrderDisplay2() Group by the order_display2 column
 * @method     AidConfigurationQuery groupByTypeNote() Group by the type_note column
 * @method     AidConfigurationQuery groupByDisplayBegin() Group by the display_begin column
 * @method     AidConfigurationQuery groupByDisplayEnd() Group by the display_end column
 * @method     AidConfigurationQuery groupByMessage() Group by the message column
 * @method     AidConfigurationQuery groupByDisplayNom() Group by the display_nom column
 * @method     AidConfigurationQuery groupByIndiceAid() Group by the indice_aid column
 * @method     AidConfigurationQuery groupByDisplayBulletin() Group by the display_bulletin column
 * @method     AidConfigurationQuery groupByBullSimplifie() Group by the bull_simplifie column
 * @method     AidConfigurationQuery groupByOutilsComplementaires() Group by the outils_complementaires column
 * @method     AidConfigurationQuery groupByFeuillePresence() Group by the feuille_presence column
 *
 * @method     AidConfigurationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AidConfigurationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AidConfigurationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AidConfigurationQuery leftJoinAidDetails($relationAlias = null) Adds a LEFT JOIN clause to the query using the AidDetails relation
 * @method     AidConfigurationQuery rightJoinAidDetails($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AidDetails relation
 * @method     AidConfigurationQuery innerJoinAidDetails($relationAlias = null) Adds a INNER JOIN clause to the query using the AidDetails relation
 *
 * @method     AidConfiguration findOne(PropelPDO $con = null) Return the first AidConfiguration matching the query
 * @method     AidConfiguration findOneOrCreate(PropelPDO $con = null) Return the first AidConfiguration matching the query, or a new AidConfiguration object populated from the query conditions when no match is found
 *
 * @method     AidConfiguration findOneByNom(string $nom) Return the first AidConfiguration filtered by the nom column
 * @method     AidConfiguration findOneByNomComplet(string $nom_complet) Return the first AidConfiguration filtered by the nom_complet column
 * @method     AidConfiguration findOneByNoteMax(int $note_max) Return the first AidConfiguration filtered by the note_max column
 * @method     AidConfiguration findOneByOrderDisplay1(string $order_display1) Return the first AidConfiguration filtered by the order_display1 column
 * @method     AidConfiguration findOneByOrderDisplay2(int $order_display2) Return the first AidConfiguration filtered by the order_display2 column
 * @method     AidConfiguration findOneByTypeNote(string $type_note) Return the first AidConfiguration filtered by the type_note column
 * @method     AidConfiguration findOneByDisplayBegin(int $display_begin) Return the first AidConfiguration filtered by the display_begin column
 * @method     AidConfiguration findOneByDisplayEnd(int $display_end) Return the first AidConfiguration filtered by the display_end column
 * @method     AidConfiguration findOneByMessage(string $message) Return the first AidConfiguration filtered by the message column
 * @method     AidConfiguration findOneByDisplayNom(string $display_nom) Return the first AidConfiguration filtered by the display_nom column
 * @method     AidConfiguration findOneByIndiceAid(int $indice_aid) Return the first AidConfiguration filtered by the indice_aid column
 * @method     AidConfiguration findOneByDisplayBulletin(string $display_bulletin) Return the first AidConfiguration filtered by the display_bulletin column
 * @method     AidConfiguration findOneByBullSimplifie(string $bull_simplifie) Return the first AidConfiguration filtered by the bull_simplifie column
 * @method     AidConfiguration findOneByOutilsComplementaires(string $outils_complementaires) Return the first AidConfiguration filtered by the outils_complementaires column
 * @method     AidConfiguration findOneByFeuillePresence(string $feuille_presence) Return the first AidConfiguration filtered by the feuille_presence column
 *
 * @method     array findByNom(string $nom) Return AidConfiguration objects filtered by the nom column
 * @method     array findByNomComplet(string $nom_complet) Return AidConfiguration objects filtered by the nom_complet column
 * @method     array findByNoteMax(int $note_max) Return AidConfiguration objects filtered by the note_max column
 * @method     array findByOrderDisplay1(string $order_display1) Return AidConfiguration objects filtered by the order_display1 column
 * @method     array findByOrderDisplay2(int $order_display2) Return AidConfiguration objects filtered by the order_display2 column
 * @method     array findByTypeNote(string $type_note) Return AidConfiguration objects filtered by the type_note column
 * @method     array findByDisplayBegin(int $display_begin) Return AidConfiguration objects filtered by the display_begin column
 * @method     array findByDisplayEnd(int $display_end) Return AidConfiguration objects filtered by the display_end column
 * @method     array findByMessage(string $message) Return AidConfiguration objects filtered by the message column
 * @method     array findByDisplayNom(string $display_nom) Return AidConfiguration objects filtered by the display_nom column
 * @method     array findByIndiceAid(int $indice_aid) Return AidConfiguration objects filtered by the indice_aid column
 * @method     array findByDisplayBulletin(string $display_bulletin) Return AidConfiguration objects filtered by the display_bulletin column
 * @method     array findByBullSimplifie(string $bull_simplifie) Return AidConfiguration objects filtered by the bull_simplifie column
 * @method     array findByOutilsComplementaires(string $outils_complementaires) Return AidConfiguration objects filtered by the outils_complementaires column
 * @method     array findByFeuillePresence(string $feuille_presence) Return AidConfiguration objects filtered by the feuille_presence column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAidConfigurationQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseAidConfigurationQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AidConfiguration', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AidConfigurationQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AidConfigurationQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AidConfigurationQuery) {
			return $criteria;
		}
		$query = new AidConfigurationQuery();
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
	 * @return    AidConfiguration|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = AidConfigurationPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    AidConfiguration A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT NOM, NOM_COMPLET, NOTE_MAX, ORDER_DISPLAY1, ORDER_DISPLAY2, TYPE_NOTE, DISPLAY_BEGIN, DISPLAY_END, MESSAGE, DISPLAY_NOM, INDICE_AID, DISPLAY_BULLETIN, BULL_SIMPLIFIE, OUTILS_COMPLEMENTAIRES, FEUILLE_PRESENCE FROM aid_config WHERE INDICE_AID = :p0';
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
			$obj = new AidConfiguration();
			$obj->hydrate($row);
			AidConfigurationPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    AidConfiguration|array|mixed the result, formatted by the current formatter
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
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AidConfigurationPeer::INDICE_AID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AidConfigurationPeer::INDICE_AID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the nom column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNom('fooValue');   // WHERE nom = 'fooValue'
	 * $query->filterByNom('%fooValue%'); // WHERE nom LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nom The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nom)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nom)) {
				$nom = str_replace('*', '%', $nom);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::NOM, $nom, $comparison);
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
	 * @return    AidConfigurationQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AidConfigurationPeer::NOM_COMPLET, $nomComplet, $comparison);
	}

	/**
	 * Filter the query on the note_max column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNoteMax(1234); // WHERE note_max = 1234
	 * $query->filterByNoteMax(array(12, 34)); // WHERE note_max IN (12, 34)
	 * $query->filterByNoteMax(array('min' => 12)); // WHERE note_max > 12
	 * </code>
	 *
	 * @param     mixed $noteMax The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByNoteMax($noteMax = null, $comparison = null)
	{
		if (is_array($noteMax)) {
			$useMinMax = false;
			if (isset($noteMax['min'])) {
				$this->addUsingAlias(AidConfigurationPeer::NOTE_MAX, $noteMax['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($noteMax['max'])) {
				$this->addUsingAlias(AidConfigurationPeer::NOTE_MAX, $noteMax['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::NOTE_MAX, $noteMax, $comparison);
	}

	/**
	 * Filter the query on the order_display1 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByOrderDisplay1('fooValue');   // WHERE order_display1 = 'fooValue'
	 * $query->filterByOrderDisplay1('%fooValue%'); // WHERE order_display1 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $orderDisplay1 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByOrderDisplay1($orderDisplay1 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($orderDisplay1)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $orderDisplay1)) {
				$orderDisplay1 = str_replace('*', '%', $orderDisplay1);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::ORDER_DISPLAY1, $orderDisplay1, $comparison);
	}

	/**
	 * Filter the query on the order_display2 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByOrderDisplay2(1234); // WHERE order_display2 = 1234
	 * $query->filterByOrderDisplay2(array(12, 34)); // WHERE order_display2 IN (12, 34)
	 * $query->filterByOrderDisplay2(array('min' => 12)); // WHERE order_display2 > 12
	 * </code>
	 *
	 * @param     mixed $orderDisplay2 The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByOrderDisplay2($orderDisplay2 = null, $comparison = null)
	{
		if (is_array($orderDisplay2)) {
			$useMinMax = false;
			if (isset($orderDisplay2['min'])) {
				$this->addUsingAlias(AidConfigurationPeer::ORDER_DISPLAY2, $orderDisplay2['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($orderDisplay2['max'])) {
				$this->addUsingAlias(AidConfigurationPeer::ORDER_DISPLAY2, $orderDisplay2['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::ORDER_DISPLAY2, $orderDisplay2, $comparison);
	}

	/**
	 * Filter the query on the type_note column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByTypeNote('fooValue');   // WHERE type_note = 'fooValue'
	 * $query->filterByTypeNote('%fooValue%'); // WHERE type_note LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $typeNote The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByTypeNote($typeNote = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($typeNote)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $typeNote)) {
				$typeNote = str_replace('*', '%', $typeNote);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::TYPE_NOTE, $typeNote, $comparison);
	}

	/**
	 * Filter the query on the display_begin column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayBegin(1234); // WHERE display_begin = 1234
	 * $query->filterByDisplayBegin(array(12, 34)); // WHERE display_begin IN (12, 34)
	 * $query->filterByDisplayBegin(array('min' => 12)); // WHERE display_begin > 12
	 * </code>
	 *
	 * @param     mixed $displayBegin The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByDisplayBegin($displayBegin = null, $comparison = null)
	{
		if (is_array($displayBegin)) {
			$useMinMax = false;
			if (isset($displayBegin['min'])) {
				$this->addUsingAlias(AidConfigurationPeer::DISPLAY_BEGIN, $displayBegin['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($displayBegin['max'])) {
				$this->addUsingAlias(AidConfigurationPeer::DISPLAY_BEGIN, $displayBegin['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::DISPLAY_BEGIN, $displayBegin, $comparison);
	}

	/**
	 * Filter the query on the display_end column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayEnd(1234); // WHERE display_end = 1234
	 * $query->filterByDisplayEnd(array(12, 34)); // WHERE display_end IN (12, 34)
	 * $query->filterByDisplayEnd(array('min' => 12)); // WHERE display_end > 12
	 * </code>
	 *
	 * @param     mixed $displayEnd The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByDisplayEnd($displayEnd = null, $comparison = null)
	{
		if (is_array($displayEnd)) {
			$useMinMax = false;
			if (isset($displayEnd['min'])) {
				$this->addUsingAlias(AidConfigurationPeer::DISPLAY_END, $displayEnd['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($displayEnd['max'])) {
				$this->addUsingAlias(AidConfigurationPeer::DISPLAY_END, $displayEnd['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::DISPLAY_END, $displayEnd, $comparison);
	}

	/**
	 * Filter the query on the message column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMessage('fooValue');   // WHERE message = 'fooValue'
	 * $query->filterByMessage('%fooValue%'); // WHERE message LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $message The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByMessage($message = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($message)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $message)) {
				$message = str_replace('*', '%', $message);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::MESSAGE, $message, $comparison);
	}

	/**
	 * Filter the query on the display_nom column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayNom('fooValue');   // WHERE display_nom = 'fooValue'
	 * $query->filterByDisplayNom('%fooValue%'); // WHERE display_nom LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $displayNom The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByDisplayNom($displayNom = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($displayNom)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $displayNom)) {
				$displayNom = str_replace('*', '%', $displayNom);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::DISPLAY_NOM, $displayNom, $comparison);
	}

	/**
	 * Filter the query on the indice_aid column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIndiceAid(1234); // WHERE indice_aid = 1234
	 * $query->filterByIndiceAid(array(12, 34)); // WHERE indice_aid IN (12, 34)
	 * $query->filterByIndiceAid(array('min' => 12)); // WHERE indice_aid > 12
	 * </code>
	 *
	 * @param     mixed $indiceAid The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByIndiceAid($indiceAid = null, $comparison = null)
	{
		if (is_array($indiceAid) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AidConfigurationPeer::INDICE_AID, $indiceAid, $comparison);
	}

	/**
	 * Filter the query on the display_bulletin column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayBulletin('fooValue');   // WHERE display_bulletin = 'fooValue'
	 * $query->filterByDisplayBulletin('%fooValue%'); // WHERE display_bulletin LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $displayBulletin The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByDisplayBulletin($displayBulletin = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($displayBulletin)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $displayBulletin)) {
				$displayBulletin = str_replace('*', '%', $displayBulletin);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::DISPLAY_BULLETIN, $displayBulletin, $comparison);
	}

	/**
	 * Filter the query on the bull_simplifie column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByBullSimplifie('fooValue');   // WHERE bull_simplifie = 'fooValue'
	 * $query->filterByBullSimplifie('%fooValue%'); // WHERE bull_simplifie LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $bullSimplifie The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByBullSimplifie($bullSimplifie = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($bullSimplifie)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $bullSimplifie)) {
				$bullSimplifie = str_replace('*', '%', $bullSimplifie);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::BULL_SIMPLIFIE, $bullSimplifie, $comparison);
	}

	/**
	 * Filter the query on the outils_complementaires column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByOutilsComplementaires('fooValue');   // WHERE outils_complementaires = 'fooValue'
	 * $query->filterByOutilsComplementaires('%fooValue%'); // WHERE outils_complementaires LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $outilsComplementaires The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByOutilsComplementaires($outilsComplementaires = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($outilsComplementaires)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $outilsComplementaires)) {
				$outilsComplementaires = str_replace('*', '%', $outilsComplementaires);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::OUTILS_COMPLEMENTAIRES, $outilsComplementaires, $comparison);
	}

	/**
	 * Filter the query on the feuille_presence column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFeuillePresence('fooValue');   // WHERE feuille_presence = 'fooValue'
	 * $query->filterByFeuillePresence('%fooValue%'); // WHERE feuille_presence LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $feuillePresence The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByFeuillePresence($feuillePresence = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($feuillePresence)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $feuillePresence)) {
				$feuillePresence = str_replace('*', '%', $feuillePresence);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidConfigurationPeer::FEUILLE_PRESENCE, $feuillePresence, $comparison);
	}

	/**
	 * Filter the query by a related AidDetails object
	 *
	 * @param     AidDetails $aidDetails  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function filterByAidDetails($aidDetails, $comparison = null)
	{
		if ($aidDetails instanceof AidDetails) {
			return $this
				->addUsingAlias(AidConfigurationPeer::INDICE_AID, $aidDetails->getIndiceAid(), $comparison);
		} elseif ($aidDetails instanceof PropelCollection) {
			return $this
				->useAidDetailsQuery()
				->filterByPrimaryKeys($aidDetails->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByAidDetails() only accepts arguments of type AidDetails or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AidDetails relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function joinAidDetails($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AidDetails');

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
			$this->addJoinObject($join, 'AidDetails');
		}

		return $this;
	}

	/**
	 * Use the AidDetails relation AidDetails object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery A secondary query class using the current class as primary query
	 */
	public function useAidDetailsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAidDetails($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AidDetails', 'AidDetailsQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AidConfiguration $aidConfiguration Object to remove from the list of results
	 *
	 * @return    AidConfigurationQuery The current query, for fluid interface
	 */
	public function prune($aidConfiguration = null)
	{
		if ($aidConfiguration) {
			$this->addUsingAlias(AidConfigurationPeer::INDICE_AID, $aidConfiguration->getIndiceAid(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseAidConfigurationQuery