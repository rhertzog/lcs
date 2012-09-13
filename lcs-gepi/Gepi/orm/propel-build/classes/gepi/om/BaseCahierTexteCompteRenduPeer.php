<?php


/**
 * Base static class for performing query and update operations on the 'ct_entry' table.
 *
 * Compte rendu du cahier de texte
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteCompteRenduPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'ct_entry';

	/** the related Propel class for this table */
	const OM_CLASS = 'CahierTexteCompteRendu';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.CahierTexteCompteRendu';

	/** the related TableMap class for this table */
	const TM_CLASS = 'CahierTexteCompteRenduTableMap';

	/** The total number of columns. */
	const NUM_COLUMNS = 9;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 9;

	/** the column name for the ID_CT field */
	const ID_CT = 'ct_entry.ID_CT';

	/** the column name for the HEURE_ENTRY field */
	const HEURE_ENTRY = 'ct_entry.HEURE_ENTRY';

	/** the column name for the DATE_CT field */
	const DATE_CT = 'ct_entry.DATE_CT';

	/** the column name for the CONTENU field */
	const CONTENU = 'ct_entry.CONTENU';

	/** the column name for the VISE field */
	const VISE = 'ct_entry.VISE';

	/** the column name for the VISA field */
	const VISA = 'ct_entry.VISA';

	/** the column name for the ID_GROUPE field */
	const ID_GROUPE = 'ct_entry.ID_GROUPE';

	/** the column name for the ID_LOGIN field */
	const ID_LOGIN = 'ct_entry.ID_LOGIN';

	/** the column name for the ID_SEQUENCE field */
	const ID_SEQUENCE = 'ct_entry.ID_SEQUENCE';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';

	/**
	 * An identiy map to hold any loaded instances of CahierTexteCompteRendu objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array CahierTexteCompteRendu[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('IdCt', 'HeureEntry', 'DateCt', 'Contenu', 'Vise', 'Visa', 'IdGroupe', 'IdLogin', 'IdSequence', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('idCt', 'heureEntry', 'dateCt', 'contenu', 'vise', 'visa', 'idGroupe', 'idLogin', 'idSequence', ),
		BasePeer::TYPE_COLNAME => array (self::ID_CT, self::HEURE_ENTRY, self::DATE_CT, self::CONTENU, self::VISE, self::VISA, self::ID_GROUPE, self::ID_LOGIN, self::ID_SEQUENCE, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID_CT', 'HEURE_ENTRY', 'DATE_CT', 'CONTENU', 'VISE', 'VISA', 'ID_GROUPE', 'ID_LOGIN', 'ID_SEQUENCE', ),
		BasePeer::TYPE_FIELDNAME => array ('id_ct', 'heure_entry', 'date_ct', 'contenu', 'vise', 'visa', 'id_groupe', 'id_login', 'id_sequence', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('IdCt' => 0, 'HeureEntry' => 1, 'DateCt' => 2, 'Contenu' => 3, 'Vise' => 4, 'Visa' => 5, 'IdGroupe' => 6, 'IdLogin' => 7, 'IdSequence' => 8, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('idCt' => 0, 'heureEntry' => 1, 'dateCt' => 2, 'contenu' => 3, 'vise' => 4, 'visa' => 5, 'idGroupe' => 6, 'idLogin' => 7, 'idSequence' => 8, ),
		BasePeer::TYPE_COLNAME => array (self::ID_CT => 0, self::HEURE_ENTRY => 1, self::DATE_CT => 2, self::CONTENU => 3, self::VISE => 4, self::VISA => 5, self::ID_GROUPE => 6, self::ID_LOGIN => 7, self::ID_SEQUENCE => 8, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID_CT' => 0, 'HEURE_ENTRY' => 1, 'DATE_CT' => 2, 'CONTENU' => 3, 'VISE' => 4, 'VISA' => 5, 'ID_GROUPE' => 6, 'ID_LOGIN' => 7, 'ID_SEQUENCE' => 8, ),
		BasePeer::TYPE_FIELDNAME => array ('id_ct' => 0, 'heure_entry' => 1, 'date_ct' => 2, 'contenu' => 3, 'vise' => 4, 'visa' => 5, 'id_groupe' => 6, 'id_login' => 7, 'id_sequence' => 8, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
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
	 * @param      string $column The column name for current table. (i.e. CahierTexteCompteRenduPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CahierTexteCompteRenduPeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(CahierTexteCompteRenduPeer::ID_CT);
			$criteria->addSelectColumn(CahierTexteCompteRenduPeer::HEURE_ENTRY);
			$criteria->addSelectColumn(CahierTexteCompteRenduPeer::DATE_CT);
			$criteria->addSelectColumn(CahierTexteCompteRenduPeer::CONTENU);
			$criteria->addSelectColumn(CahierTexteCompteRenduPeer::VISE);
			$criteria->addSelectColumn(CahierTexteCompteRenduPeer::VISA);
			$criteria->addSelectColumn(CahierTexteCompteRenduPeer::ID_GROUPE);
			$criteria->addSelectColumn(CahierTexteCompteRenduPeer::ID_LOGIN);
			$criteria->addSelectColumn(CahierTexteCompteRenduPeer::ID_SEQUENCE);
		} else {
			$criteria->addSelectColumn($alias . '.ID_CT');
			$criteria->addSelectColumn($alias . '.HEURE_ENTRY');
			$criteria->addSelectColumn($alias . '.DATE_CT');
			$criteria->addSelectColumn($alias . '.CONTENU');
			$criteria->addSelectColumn($alias . '.VISE');
			$criteria->addSelectColumn($alias . '.VISA');
			$criteria->addSelectColumn($alias . '.ID_GROUPE');
			$criteria->addSelectColumn($alias . '.ID_LOGIN');
			$criteria->addSelectColumn($alias . '.ID_SEQUENCE');
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
		$criteria->setPrimaryTableName(CahierTexteCompteRenduPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * Selects one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     CahierTexteCompteRendu
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CahierTexteCompteRenduPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Selects several row from the DB.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return CahierTexteCompteRenduPeer::populateObjects(CahierTexteCompteRenduPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			CahierTexteCompteRenduPeer::addSelectColumns($criteria);
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
	 * @param      CahierTexteCompteRendu $value A CahierTexteCompteRendu object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getIdCt();
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
	 * @param      mixed $value A CahierTexteCompteRendu object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof CahierTexteCompteRendu) {
				$key = (string) $value->getIdCt();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CahierTexteCompteRendu object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     CahierTexteCompteRendu Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to ct_entry
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
		// Invalidate objects in CahierTexteCompteRenduFichierJointPeer instance pool,
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CahierTexteCompteRenduFichierJointPeer::clearInstancePool();
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
		if ($row[$startcol] === null) {
			return null;
		}
		return (string) $row[$startcol];
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
		return (int) $row[$startcol];
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
		$cls = CahierTexteCompteRenduPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = CahierTexteCompteRenduPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = CahierTexteCompteRenduPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				CahierTexteCompteRenduPeer::addInstanceToPool($obj, $key);
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
	 * @return     array (CahierTexteCompteRendu object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = CahierTexteCompteRenduPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = CahierTexteCompteRenduPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + CahierTexteCompteRenduPeer::NUM_HYDRATE_COLUMNS;
		} else {
			$cls = CahierTexteCompteRenduPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			CahierTexteCompteRenduPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Groupe table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinGroupe(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteCompteRenduPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

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
		$criteria->setPrimaryTableName(CahierTexteCompteRenduPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_LOGIN, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CahierTexteSequence table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCahierTexteSequence(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteCompteRenduPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_SEQUENCE, CahierTexteSequencePeer::ID, $join_behavior);

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
	 * Selects a collection of CahierTexteCompteRendu objects pre-filled with their Groupe objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteCompteRendu objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinGroupe(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		$startcol = CahierTexteCompteRenduPeer::NUM_HYDRATE_COLUMNS;
		GroupePeer::addSelectColumns($criteria);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteCompteRenduPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteCompteRenduPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CahierTexteCompteRenduPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteCompteRenduPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = GroupePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = GroupePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CahierTexteCompteRendu) to $obj2 (Groupe)
				$obj2->addCahierTexteCompteRendu($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CahierTexteCompteRendu objects pre-filled with their UtilisateurProfessionnel objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteCompteRendu objects.
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

		CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		$startcol = CahierTexteCompteRenduPeer::NUM_HYDRATE_COLUMNS;
		UtilisateurProfessionnelPeer::addSelectColumns($criteria);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_LOGIN, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteCompteRenduPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteCompteRenduPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CahierTexteCompteRenduPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteCompteRenduPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CahierTexteCompteRendu) to $obj2 (UtilisateurProfessionnel)
				$obj2->addCahierTexteCompteRendu($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CahierTexteCompteRendu objects pre-filled with their CahierTexteSequence objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteCompteRendu objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCahierTexteSequence(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		$startcol = CahierTexteCompteRenduPeer::NUM_HYDRATE_COLUMNS;
		CahierTexteSequencePeer::addSelectColumns($criteria);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_SEQUENCE, CahierTexteSequencePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteCompteRenduPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteCompteRenduPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CahierTexteCompteRenduPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteCompteRenduPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CahierTexteSequencePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CahierTexteSequencePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CahierTexteSequencePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CahierTexteSequencePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CahierTexteCompteRendu) to $obj2 (CahierTexteSequence)
				$obj2->addCahierTexteCompteRendu($obj1);

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
		$criteria->setPrimaryTableName(CahierTexteCompteRenduPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_LOGIN, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_SEQUENCE, CahierTexteSequencePeer::ID, $join_behavior);

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
	 * Selects a collection of CahierTexteCompteRendu objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteCompteRendu objects.
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

		CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		$startcol2 = CahierTexteCompteRenduPeer::NUM_HYDRATE_COLUMNS;

		GroupePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + GroupePeer::NUM_HYDRATE_COLUMNS;

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + UtilisateurProfessionnelPeer::NUM_HYDRATE_COLUMNS;

		CahierTexteSequencePeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + CahierTexteSequencePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_LOGIN, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_SEQUENCE, CahierTexteSequencePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteCompteRenduPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteCompteRenduPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CahierTexteCompteRenduPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteCompteRenduPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined Groupe rows

			$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = GroupePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = GroupePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (CahierTexteCompteRendu) to the collection in $obj2 (Groupe)
				$obj2->addCahierTexteCompteRendu($obj1);
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

				// Add the $obj1 (CahierTexteCompteRendu) to the collection in $obj3 (UtilisateurProfessionnel)
				$obj3->addCahierTexteCompteRendu($obj1);
			} // if joined row not null

			// Add objects for joined CahierTexteSequence rows

			$key4 = CahierTexteSequencePeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = CahierTexteSequencePeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = CahierTexteSequencePeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CahierTexteSequencePeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (CahierTexteCompteRendu) to the collection in $obj4 (CahierTexteSequence)
				$obj4->addCahierTexteCompteRendu($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Groupe table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptGroupe(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteCompteRenduPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY should not affect count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_LOGIN, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_SEQUENCE, CahierTexteSequencePeer::ID, $join_behavior);

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
		$criteria->setPrimaryTableName(CahierTexteCompteRenduPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY should not affect count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_SEQUENCE, CahierTexteSequencePeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CahierTexteSequence table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCahierTexteSequence(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteCompteRenduPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY should not affect count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_LOGIN, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Selects a collection of CahierTexteCompteRendu objects pre-filled with all related objects except Groupe.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteCompteRendu objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptGroupe(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		$startcol2 = CahierTexteCompteRenduPeer::NUM_HYDRATE_COLUMNS;

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + UtilisateurProfessionnelPeer::NUM_HYDRATE_COLUMNS;

		CahierTexteSequencePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + CahierTexteSequencePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_LOGIN, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_SEQUENCE, CahierTexteSequencePeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteCompteRenduPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteCompteRenduPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CahierTexteCompteRenduPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteCompteRenduPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CahierTexteCompteRendu) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addCahierTexteCompteRendu($obj1);

			} // if joined row is not null

				// Add objects for joined CahierTexteSequence rows

				$key3 = CahierTexteSequencePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CahierTexteSequencePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CahierTexteSequencePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CahierTexteSequencePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CahierTexteCompteRendu) to the collection in $obj3 (CahierTexteSequence)
				$obj3->addCahierTexteCompteRendu($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CahierTexteCompteRendu objects pre-filled with all related objects except UtilisateurProfessionnel.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteCompteRendu objects.
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

		CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		$startcol2 = CahierTexteCompteRenduPeer::NUM_HYDRATE_COLUMNS;

		GroupePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + GroupePeer::NUM_HYDRATE_COLUMNS;

		CahierTexteSequencePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + CahierTexteSequencePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_SEQUENCE, CahierTexteSequencePeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteCompteRenduPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteCompteRenduPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CahierTexteCompteRenduPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteCompteRenduPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Groupe rows

				$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = GroupePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CahierTexteCompteRendu) to the collection in $obj2 (Groupe)
				$obj2->addCahierTexteCompteRendu($obj1);

			} // if joined row is not null

				// Add objects for joined CahierTexteSequence rows

				$key3 = CahierTexteSequencePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CahierTexteSequencePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CahierTexteSequencePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CahierTexteSequencePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CahierTexteCompteRendu) to the collection in $obj3 (CahierTexteSequence)
				$obj3->addCahierTexteCompteRendu($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CahierTexteCompteRendu objects pre-filled with all related objects except CahierTexteSequence.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteCompteRendu objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCahierTexteSequence(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CahierTexteCompteRenduPeer::addSelectColumns($criteria);
		$startcol2 = CahierTexteCompteRenduPeer::NUM_HYDRATE_COLUMNS;

		GroupePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + GroupePeer::NUM_HYDRATE_COLUMNS;

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + UtilisateurProfessionnelPeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(CahierTexteCompteRenduPeer::ID_LOGIN, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteCompteRenduPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteCompteRenduPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CahierTexteCompteRenduPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteCompteRenduPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Groupe rows

				$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = GroupePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CahierTexteCompteRendu) to the collection in $obj2 (Groupe)
				$obj2->addCahierTexteCompteRendu($obj1);

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

				// Add the $obj1 (CahierTexteCompteRendu) to the collection in $obj3 (UtilisateurProfessionnel)
				$obj3->addCahierTexteCompteRendu($obj1);

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
	  $dbMap = Propel::getDatabaseMap(BaseCahierTexteCompteRenduPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseCahierTexteCompteRenduPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new CahierTexteCompteRenduTableMap());
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
		return $withPrefix ? CahierTexteCompteRenduPeer::CLASS_DEFAULT : CahierTexteCompteRenduPeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a CahierTexteCompteRendu or Criteria object.
	 *
	 * @param      mixed $values Criteria or CahierTexteCompteRendu object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from CahierTexteCompteRendu object
		}

		if ($criteria->containsKey(CahierTexteCompteRenduPeer::ID_CT) && $criteria->keyContainsValue(CahierTexteCompteRenduPeer::ID_CT) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.CahierTexteCompteRenduPeer::ID_CT.')');
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
	 * Performs an UPDATE on the database, given a CahierTexteCompteRendu or Criteria object.
	 *
	 * @param      mixed $values Criteria or CahierTexteCompteRendu object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(CahierTexteCompteRenduPeer::ID_CT);
			$value = $criteria->remove(CahierTexteCompteRenduPeer::ID_CT);
			if ($value) {
				$selectCriteria->add(CahierTexteCompteRenduPeer::ID_CT, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(CahierTexteCompteRenduPeer::TABLE_NAME);
			}

		} else { // $values is CahierTexteCompteRendu object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the ct_entry table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += CahierTexteCompteRenduPeer::doOnDeleteCascade(new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(CahierTexteCompteRenduPeer::TABLE_NAME, $con, CahierTexteCompteRenduPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			CahierTexteCompteRenduPeer::clearInstancePool();
			CahierTexteCompteRenduPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs a DELETE on the database, given a CahierTexteCompteRendu or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CahierTexteCompteRendu object or primary key or array of primary keys
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
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof CahierTexteCompteRendu) { // it's a model object
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CahierTexteCompteRenduPeer::ID_CT, (array) $values, Criteria::IN);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			// cloning the Criteria in case it's modified by doSelect() or doSelectStmt()
			$c = clone $criteria;
			$affectedRows += CahierTexteCompteRenduPeer::doOnDeleteCascade($c, $con);
			
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			if ($values instanceof Criteria) {
				CahierTexteCompteRenduPeer::clearInstancePool();
			} elseif ($values instanceof CahierTexteCompteRendu) { // it's a model object
				CahierTexteCompteRenduPeer::removeInstanceFromPool($values);
			} else { // it's a primary key, or an array of pks
				foreach ((array) $values as $singleval) {
					CahierTexteCompteRenduPeer::removeInstanceFromPool($singleval);
				}
			}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			CahierTexteCompteRenduPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * This is a method for emulating ON DELETE CASCADE for DBs that don't support this
	 * feature (like MySQL or SQLite).
	 *
	 * This method is not very speedy because it must perform a query first to get
	 * the implicated records and then perform the deletes by calling those Peer classes.
	 *
	 * This method should be used within a transaction if possible.
	 *
	 * @param      Criteria $criteria
	 * @param      PropelPDO $con
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	protected static function doOnDeleteCascade(Criteria $criteria, PropelPDO $con)
	{
		// initialize var to track total num of affected rows
		$affectedRows = 0;

		// first find the objects that are implicated by the $criteria
		$objects = CahierTexteCompteRenduPeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related CahierTexteCompteRenduFichierJoint objects
			$criteria = new Criteria(CahierTexteCompteRenduFichierJointPeer::DATABASE_NAME);
			
			$criteria->add(CahierTexteCompteRenduFichierJointPeer::ID_CT, $obj->getIdCt());
			$affectedRows += CahierTexteCompteRenduFichierJointPeer::doDelete($criteria, $con);
		}
		return $affectedRows;
	}

	/**
	 * Validates all modified columns of given CahierTexteCompteRendu object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      CahierTexteCompteRendu $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CahierTexteCompteRenduPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CahierTexteCompteRenduPeer::TABLE_NAME);

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

		return BasePeer::doValidate(CahierTexteCompteRenduPeer::DATABASE_NAME, CahierTexteCompteRenduPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     CahierTexteCompteRendu
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = CahierTexteCompteRenduPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);
		$criteria->add(CahierTexteCompteRenduPeer::ID_CT, $pk);

		$v = CahierTexteCompteRenduPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);
			$criteria->add(CahierTexteCompteRenduPeer::ID_CT, $pks, Criteria::IN);
			$objs = CahierTexteCompteRenduPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseCahierTexteCompteRenduPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCahierTexteCompteRenduPeer::buildTableMap();

