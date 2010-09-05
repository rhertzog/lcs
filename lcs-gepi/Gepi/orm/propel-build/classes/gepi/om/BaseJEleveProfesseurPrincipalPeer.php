<?php


/**
 * Base static class for performing query and update operations on the 'j_eleves_professeurs' table.
 *
 * Table de jointure entre les professeurs principaux et les eleves
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJEleveProfesseurPrincipalPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'j_eleves_professeurs';

	/** the related Propel class for this table */
	const OM_CLASS = 'JEleveProfesseurPrincipal';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.JEleveProfesseurPrincipal';

	/** the related TableMap class for this table */
	const TM_CLASS = 'JEleveProfesseurPrincipalTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 3;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the LOGIN field */
	const LOGIN = 'j_eleves_professeurs.LOGIN';

	/** the column name for the PROFESSEUR field */
	const PROFESSEUR = 'j_eleves_professeurs.PROFESSEUR';

	/** the column name for the ID_CLASSE field */
	const ID_CLASSE = 'j_eleves_professeurs.ID_CLASSE';

	/**
	 * An identiy map to hold any loaded instances of JEleveProfesseurPrincipal objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array JEleveProfesseurPrincipal[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Login', 'Professeur', 'IdClasse', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('login', 'professeur', 'idClasse', ),
		BasePeer::TYPE_COLNAME => array (self::LOGIN, self::PROFESSEUR, self::ID_CLASSE, ),
		BasePeer::TYPE_RAW_COLNAME => array ('LOGIN', 'PROFESSEUR', 'ID_CLASSE', ),
		BasePeer::TYPE_FIELDNAME => array ('login', 'professeur', 'id_classe', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Login' => 0, 'Professeur' => 1, 'IdClasse' => 2, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('login' => 0, 'professeur' => 1, 'idClasse' => 2, ),
		BasePeer::TYPE_COLNAME => array (self::LOGIN => 0, self::PROFESSEUR => 1, self::ID_CLASSE => 2, ),
		BasePeer::TYPE_RAW_COLNAME => array ('LOGIN' => 0, 'PROFESSEUR' => 1, 'ID_CLASSE' => 2, ),
		BasePeer::TYPE_FIELDNAME => array ('login' => 0, 'professeur' => 1, 'id_classe' => 2, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, )
	);

	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. JEleveProfesseurPrincipalPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(JEleveProfesseurPrincipalPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      Criteria $criteria object containing the columns to add.
	 * @param      string   $alias    optional table alias
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria, $alias = null)
	{
		if (null === $alias) {
			$criteria->addSelectColumn(JEleveProfesseurPrincipalPeer::LOGIN);
			$criteria->addSelectColumn(JEleveProfesseurPrincipalPeer::PROFESSEUR);
			$criteria->addSelectColumn(JEleveProfesseurPrincipalPeer::ID_CLASSE);
		} else {
			$criteria->addSelectColumn($alias . '.LOGIN');
			$criteria->addSelectColumn($alias . '.PROFESSEUR');
			$criteria->addSelectColumn($alias . '.ID_CLASSE');
		}
	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		// we may modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		// BasePeer returns a PDOStatement
		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     JEleveProfesseurPrincipal
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = JEleveProfesseurPrincipalPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return JEleveProfesseurPrincipalPeer::populateObjects(JEleveProfesseurPrincipalPeer::doSelectStmt($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// BasePeer returns a PDOStatement
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * Adds an object to the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doSelect*()
	 * methods in your stub classes -- you may need to explicitly add objects
	 * to the cache in order to ensure that the same objects are always returned by doSelect*()
	 * and retrieveByPK*() calls.
	 *
	 * @param      JEleveProfesseurPrincipal $value A JEleveProfesseurPrincipal object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(JEleveProfesseurPrincipal $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = serialize(array((string) $obj->getLogin(), (string) $obj->getProfesseur(), (string) $obj->getIdClasse()));
			} // if key === null
			self::$instances[$key] = $obj;
		}
	}

	/**
	 * Removes an object from the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doDelete
	 * methods in your stub classes -- you may need to explicitly remove objects
	 * from the cache in order to prevent returning objects that no longer exist.
	 *
	 * @param      mixed $value A JEleveProfesseurPrincipal object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof JEleveProfesseurPrincipal) {
				$key = serialize(array((string) $value->getLogin(), (string) $value->getProfesseur(), (string) $value->getIdClasse()));
			} elseif (is_array($value) && count($value) === 3) {
				// assume we've been passed a primary key
				$key = serialize(array((string) $value[0], (string) $value[1], (string) $value[2]));
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or JEleveProfesseurPrincipal object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
				throw $e;
			}

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     JEleveProfesseurPrincipal Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to j_eleves_professeurs
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
	}

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
		if ($row[$startcol] === null && $row[$startcol + 1] === null && $row[$startcol + 2] === null) {
			return null;
		}
		return serialize(array((string) $row[$startcol], (string) $row[$startcol + 1], (string) $row[$startcol + 2]));
	}

	/**
	 * Retrieves the primary key from the DB resultset row 
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, an array of the primary key columns will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     mixed The primary key of the row
	 */
	public static function getPrimaryKeyFromRow($row, $startcol = 0)
	{
		return array((string) $row[$startcol], (string) $row[$startcol + 1], (int) $row[$startcol + 2]);
	}
	
	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = JEleveProfesseurPrincipalPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = JEleveProfesseurPrincipalPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = JEleveProfesseurPrincipalPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				JEleveProfesseurPrincipalPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}
	/**
	 * Populates an object of the default type or an object that inherit from the default.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     array (JEleveProfesseurPrincipal object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = JEleveProfesseurPrincipalPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = JEleveProfesseurPrincipalPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + JEleveProfesseurPrincipalPeer::NUM_COLUMNS;
		} else {
			$cls = JEleveProfesseurPrincipalPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			JEleveProfesseurPrincipalPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}

	/**
	 * Returns the number of rows matching criteria, joining the related Eleve table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEleve(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::LOGIN, ElevePeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related UtilisateurProfessionnel table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinUtilisateurProfessionnel(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::PROFESSEUR, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Classe table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinClasse(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of JEleveProfesseurPrincipal objects pre-filled with their Eleve objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JEleveProfesseurPrincipal objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEleve(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		$startcol = (JEleveProfesseurPrincipalPeer::NUM_COLUMNS - JEleveProfesseurPrincipalPeer::NUM_LAZY_LOAD_COLUMNS);
		ElevePeer::addSelectColumns($criteria);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::LOGIN, ElevePeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JEleveProfesseurPrincipalPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JEleveProfesseurPrincipalPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = JEleveProfesseurPrincipalPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JEleveProfesseurPrincipalPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = ElevePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = ElevePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					ElevePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to $obj2 (Eleve)
				$obj2->addJEleveProfesseurPrincipal($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of JEleveProfesseurPrincipal objects pre-filled with their UtilisateurProfessionnel objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JEleveProfesseurPrincipal objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinUtilisateurProfessionnel(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		$startcol = (JEleveProfesseurPrincipalPeer::NUM_COLUMNS - JEleveProfesseurPrincipalPeer::NUM_LAZY_LOAD_COLUMNS);
		UtilisateurProfessionnelPeer::addSelectColumns($criteria);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::PROFESSEUR, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JEleveProfesseurPrincipalPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JEleveProfesseurPrincipalPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = JEleveProfesseurPrincipalPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JEleveProfesseurPrincipalPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to $obj2 (UtilisateurProfessionnel)
				$obj2->addJEleveProfesseurPrincipal($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of JEleveProfesseurPrincipal objects pre-filled with their Classe objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JEleveProfesseurPrincipal objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinClasse(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		$startcol = (JEleveProfesseurPrincipalPeer::NUM_COLUMNS - JEleveProfesseurPrincipalPeer::NUM_LAZY_LOAD_COLUMNS);
		ClassePeer::addSelectColumns($criteria);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JEleveProfesseurPrincipalPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JEleveProfesseurPrincipalPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = JEleveProfesseurPrincipalPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JEleveProfesseurPrincipalPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = ClassePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = ClassePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					ClassePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to $obj2 (Classe)
				$obj2->addJEleveProfesseurPrincipal($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::LOGIN, ElevePeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::PROFESSEUR, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of JEleveProfesseurPrincipal objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JEleveProfesseurPrincipal objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		$startcol2 = (JEleveProfesseurPrincipalPeer::NUM_COLUMNS - JEleveProfesseurPrincipalPeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::LOGIN, ElevePeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::PROFESSEUR, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JEleveProfesseurPrincipalPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JEleveProfesseurPrincipalPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = JEleveProfesseurPrincipalPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JEleveProfesseurPrincipalPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined Eleve rows

			$key2 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = ElevePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = ElevePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					ElevePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to the collection in $obj2 (Eleve)
				$obj2->addJEleveProfesseurPrincipal($obj1);
			} // if joined row not null

			// Add objects for joined UtilisateurProfessionnel rows

			$key3 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = UtilisateurProfessionnelPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to the collection in $obj3 (UtilisateurProfessionnel)
				$obj3->addJEleveProfesseurPrincipal($obj1);
			} // if joined row not null

			// Add objects for joined Classe rows

			$key4 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = ClassePeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = ClassePeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					ClassePeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to the collection in $obj4 (Classe)
				$obj4->addJEleveProfesseurPrincipal($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Eleve table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEleve(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(JEleveProfesseurPrincipalPeer::PROFESSEUR, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related UtilisateurProfessionnel table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptUtilisateurProfessionnel(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(JEleveProfesseurPrincipalPeer::LOGIN, ElevePeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Classe table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptClasse(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(JEleveProfesseurPrincipalPeer::LOGIN, ElevePeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::PROFESSEUR, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of JEleveProfesseurPrincipal objects pre-filled with all related objects except Eleve.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JEleveProfesseurPrincipal objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEleve(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		$startcol2 = (JEleveProfesseurPrincipalPeer::NUM_COLUMNS - JEleveProfesseurPrincipalPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::PROFESSEUR, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JEleveProfesseurPrincipalPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JEleveProfesseurPrincipalPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = JEleveProfesseurPrincipalPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JEleveProfesseurPrincipalPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined UtilisateurProfessionnel rows

				$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addJEleveProfesseurPrincipal($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key3 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = ClassePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ClassePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to the collection in $obj3 (Classe)
				$obj3->addJEleveProfesseurPrincipal($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of JEleveProfesseurPrincipal objects pre-filled with all related objects except UtilisateurProfessionnel.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JEleveProfesseurPrincipal objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptUtilisateurProfessionnel(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		$startcol2 = (JEleveProfesseurPrincipalPeer::NUM_COLUMNS - JEleveProfesseurPrincipalPeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::LOGIN, ElevePeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JEleveProfesseurPrincipalPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JEleveProfesseurPrincipalPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = JEleveProfesseurPrincipalPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JEleveProfesseurPrincipalPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Eleve rows

				$key2 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = ElevePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = ElevePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					ElevePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to the collection in $obj2 (Eleve)
				$obj2->addJEleveProfesseurPrincipal($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key3 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = ClassePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ClassePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to the collection in $obj3 (Classe)
				$obj3->addJEleveProfesseurPrincipal($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of JEleveProfesseurPrincipal objects pre-filled with all related objects except Classe.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JEleveProfesseurPrincipal objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptClasse(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
		$startcol2 = (JEleveProfesseurPrincipalPeer::NUM_COLUMNS - JEleveProfesseurPrincipalPeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::LOGIN, ElevePeer::LOGIN, $join_behavior);

		$criteria->addJoin(JEleveProfesseurPrincipalPeer::PROFESSEUR, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JEleveProfesseurPrincipalPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JEleveProfesseurPrincipalPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = JEleveProfesseurPrincipalPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JEleveProfesseurPrincipalPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Eleve rows

				$key2 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = ElevePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = ElevePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					ElevePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to the collection in $obj2 (Eleve)
				$obj2->addJEleveProfesseurPrincipal($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key3 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = UtilisateurProfessionnelPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (JEleveProfesseurPrincipal) to the collection in $obj3 (UtilisateurProfessionnel)
				$obj3->addJEleveProfesseurPrincipal($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseJEleveProfesseurPrincipalPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseJEleveProfesseurPrincipalPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new JEleveProfesseurPrincipalTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * If $withPrefix is true, the returned path
	 * uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @param      boolean $withPrefix Whether or not to return the path with the class name
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass($withPrefix = true)
	{
		return $withPrefix ? JEleveProfesseurPrincipalPeer::CLASS_DEFAULT : JEleveProfesseurPrincipalPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a JEleveProfesseurPrincipal or Criteria object.
	 *
	 * @param      mixed $values Criteria or JEleveProfesseurPrincipal object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from JEleveProfesseurPrincipal object
		}


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->beginTransaction();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollBack();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a JEleveProfesseurPrincipal or Criteria object.
	 *
	 * @param      mixed $values Criteria or JEleveProfesseurPrincipal object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(JEleveProfesseurPrincipalPeer::LOGIN);
			$value = $criteria->remove(JEleveProfesseurPrincipalPeer::LOGIN);
			if ($value) {
				$selectCriteria->add(JEleveProfesseurPrincipalPeer::LOGIN, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);
			}

			$comparison = $criteria->getComparison(JEleveProfesseurPrincipalPeer::PROFESSEUR);
			$value = $criteria->remove(JEleveProfesseurPrincipalPeer::PROFESSEUR);
			if ($value) {
				$selectCriteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);
			}

			$comparison = $criteria->getComparison(JEleveProfesseurPrincipalPeer::ID_CLASSE);
			$value = $criteria->remove(JEleveProfesseurPrincipalPeer::ID_CLASSE);
			if ($value) {
				$selectCriteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(JEleveProfesseurPrincipalPeer::TABLE_NAME);
			}

		} else { // $values is JEleveProfesseurPrincipal object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the j_eleves_professeurs table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(JEleveProfesseurPrincipalPeer::TABLE_NAME, $con, JEleveProfesseurPrincipalPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			JEleveProfesseurPrincipalPeer::clearInstancePool();
			JEleveProfesseurPrincipalPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a JEleveProfesseurPrincipal or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or JEleveProfesseurPrincipal object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      PropelPDO $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, PropelPDO $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			JEleveProfesseurPrincipalPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof JEleveProfesseurPrincipal) { // it's a model object
			// invalidate the cache for this single object
			JEleveProfesseurPrincipalPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			// primary key is composite; we therefore, expect
			// the primary key passed to be an array of pkey values
			if (count($values) == count($values, COUNT_RECURSIVE)) {
				// array is not multi-dimensional
				$values = array($values);
			}
			foreach ($values as $value) {
				$criterion = $criteria->getNewCriterion(JEleveProfesseurPrincipalPeer::LOGIN, $value[0]);
				$criterion->addAnd($criteria->getNewCriterion(JEleveProfesseurPrincipalPeer::PROFESSEUR, $value[1]));
				$criterion->addAnd($criteria->getNewCriterion(JEleveProfesseurPrincipalPeer::ID_CLASSE, $value[2]));
				$criteria->addOr($criterion);
				// we can invalidate the cache for this single PK
				JEleveProfesseurPrincipalPeer::removeInstanceFromPool($value);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			JEleveProfesseurPrincipalPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given JEleveProfesseurPrincipal object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      JEleveProfesseurPrincipal $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(JEleveProfesseurPrincipal $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(JEleveProfesseurPrincipalPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(JEleveProfesseurPrincipalPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach ($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		return BasePeer::doValidate(JEleveProfesseurPrincipalPeer::DATABASE_NAME, JEleveProfesseurPrincipalPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve object using using composite pkey values.
	 * @param      string $login
	 * @param      string $professeur
	 * @param      int $id_classe
	 * @param      PropelPDO $con
	 * @return     JEleveProfesseurPrincipal
	 */
	public static function retrieveByPK($login, $professeur, $id_classe, PropelPDO $con = null) {
		$_instancePoolKey = serialize(array((string) $login, (string) $professeur, (string) $id_classe));
 		if (null !== ($obj = JEleveProfesseurPrincipalPeer::getInstanceFromPool($_instancePoolKey))) {
 			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(JEleveProfesseurPrincipalPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$criteria = new Criteria(JEleveProfesseurPrincipalPeer::DATABASE_NAME);
		$criteria->add(JEleveProfesseurPrincipalPeer::LOGIN, $login);
		$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $professeur);
		$criteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $id_classe);
		$v = JEleveProfesseurPrincipalPeer::doSelect($criteria, $con);

		return !empty($v) ? $v[0] : null;
	}
} // BaseJEleveProfesseurPrincipalPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseJEleveProfesseurPrincipalPeer::buildTableMap();

