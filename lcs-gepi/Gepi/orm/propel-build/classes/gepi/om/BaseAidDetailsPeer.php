<?php


/**
 * Base static class for performing query and update operations on the 'aid' table.
 *
 * Liste des AID (Activites Inter-Disciplinaires)
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAidDetailsPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'aid';

	/** the related Propel class for this table */
	const OM_CLASS = 'AidDetails';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.AidDetails';

	/** the related TableMap class for this table */
	const TM_CLASS = 'AidDetailsTableMap';

	/** The total number of columns. */
	const NUM_COLUMNS = 24;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 24;

	/** the column name for the ID field */
	const ID = 'aid.ID';

	/** the column name for the NOM field */
	const NOM = 'aid.NOM';

	/** the column name for the NUMERO field */
	const NUMERO = 'aid.NUMERO';

	/** the column name for the INDICE_AID field */
	const INDICE_AID = 'aid.INDICE_AID';

	/** the column name for the PERSO1 field */
	const PERSO1 = 'aid.PERSO1';

	/** the column name for the PERSO2 field */
	const PERSO2 = 'aid.PERSO2';

	/** the column name for the PERSO3 field */
	const PERSO3 = 'aid.PERSO3';

	/** the column name for the PRODUCTIONS field */
	const PRODUCTIONS = 'aid.PRODUCTIONS';

	/** the column name for the RESUME field */
	const RESUME = 'aid.RESUME';

	/** the column name for the FAMILLE field */
	const FAMILLE = 'aid.FAMILLE';

	/** the column name for the MOTS_CLES field */
	const MOTS_CLES = 'aid.MOTS_CLES';

	/** the column name for the ADRESSE1 field */
	const ADRESSE1 = 'aid.ADRESSE1';

	/** the column name for the ADRESSE2 field */
	const ADRESSE2 = 'aid.ADRESSE2';

	/** the column name for the PUBLIC_DESTINATAIRE field */
	const PUBLIC_DESTINATAIRE = 'aid.PUBLIC_DESTINATAIRE';

	/** the column name for the CONTACTS field */
	const CONTACTS = 'aid.CONTACTS';

	/** the column name for the DIVERS field */
	const DIVERS = 'aid.DIVERS';

	/** the column name for the MATIERE1 field */
	const MATIERE1 = 'aid.MATIERE1';

	/** the column name for the MATIERE2 field */
	const MATIERE2 = 'aid.MATIERE2';

	/** the column name for the ELEVE_PEUT_MODIFIER field */
	const ELEVE_PEUT_MODIFIER = 'aid.ELEVE_PEUT_MODIFIER';

	/** the column name for the PROF_PEUT_MODIFIER field */
	const PROF_PEUT_MODIFIER = 'aid.PROF_PEUT_MODIFIER';

	/** the column name for the CPE_PEUT_MODIFIER field */
	const CPE_PEUT_MODIFIER = 'aid.CPE_PEUT_MODIFIER';

	/** the column name for the FICHE_PUBLIQUE field */
	const FICHE_PUBLIQUE = 'aid.FICHE_PUBLIQUE';

	/** the column name for the AFFICHE_ADRESSE1 field */
	const AFFICHE_ADRESSE1 = 'aid.AFFICHE_ADRESSE1';

	/** the column name for the EN_CONSTRUCTION field */
	const EN_CONSTRUCTION = 'aid.EN_CONSTRUCTION';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';

	/**
	 * An identiy map to hold any loaded instances of AidDetails objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array AidDetails[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Nom', 'Numero', 'IndiceAid', 'Perso1', 'Perso2', 'Perso3', 'Productions', 'Resume', 'Famille', 'MotsCles', 'Adresse1', 'Adresse2', 'PublicDestinataire', 'Contacts', 'Divers', 'Matiere1', 'Matiere2', 'ElevePeutModifier', 'ProfPeutModifier', 'CpePeutModifier', 'FichePublique', 'AfficheAdresse1', 'EnConstruction', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'nom', 'numero', 'indiceAid', 'perso1', 'perso2', 'perso3', 'productions', 'resume', 'famille', 'motsCles', 'adresse1', 'adresse2', 'publicDestinataire', 'contacts', 'divers', 'matiere1', 'matiere2', 'elevePeutModifier', 'profPeutModifier', 'cpePeutModifier', 'fichePublique', 'afficheAdresse1', 'enConstruction', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::NOM, self::NUMERO, self::INDICE_AID, self::PERSO1, self::PERSO2, self::PERSO3, self::PRODUCTIONS, self::RESUME, self::FAMILLE, self::MOTS_CLES, self::ADRESSE1, self::ADRESSE2, self::PUBLIC_DESTINATAIRE, self::CONTACTS, self::DIVERS, self::MATIERE1, self::MATIERE2, self::ELEVE_PEUT_MODIFIER, self::PROF_PEUT_MODIFIER, self::CPE_PEUT_MODIFIER, self::FICHE_PUBLIQUE, self::AFFICHE_ADRESSE1, self::EN_CONSTRUCTION, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'NOM', 'NUMERO', 'INDICE_AID', 'PERSO1', 'PERSO2', 'PERSO3', 'PRODUCTIONS', 'RESUME', 'FAMILLE', 'MOTS_CLES', 'ADRESSE1', 'ADRESSE2', 'PUBLIC_DESTINATAIRE', 'CONTACTS', 'DIVERS', 'MATIERE1', 'MATIERE2', 'ELEVE_PEUT_MODIFIER', 'PROF_PEUT_MODIFIER', 'CPE_PEUT_MODIFIER', 'FICHE_PUBLIQUE', 'AFFICHE_ADRESSE1', 'EN_CONSTRUCTION', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'nom', 'numero', 'indice_aid', 'perso1', 'perso2', 'perso3', 'productions', 'resume', 'famille', 'mots_cles', 'adresse1', 'adresse2', 'public_destinataire', 'contacts', 'divers', 'matiere1', 'matiere2', 'eleve_peut_modifier', 'prof_peut_modifier', 'cpe_peut_modifier', 'fiche_publique', 'affiche_adresse1', 'en_construction', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Nom' => 1, 'Numero' => 2, 'IndiceAid' => 3, 'Perso1' => 4, 'Perso2' => 5, 'Perso3' => 6, 'Productions' => 7, 'Resume' => 8, 'Famille' => 9, 'MotsCles' => 10, 'Adresse1' => 11, 'Adresse2' => 12, 'PublicDestinataire' => 13, 'Contacts' => 14, 'Divers' => 15, 'Matiere1' => 16, 'Matiere2' => 17, 'ElevePeutModifier' => 18, 'ProfPeutModifier' => 19, 'CpePeutModifier' => 20, 'FichePublique' => 21, 'AfficheAdresse1' => 22, 'EnConstruction' => 23, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'nom' => 1, 'numero' => 2, 'indiceAid' => 3, 'perso1' => 4, 'perso2' => 5, 'perso3' => 6, 'productions' => 7, 'resume' => 8, 'famille' => 9, 'motsCles' => 10, 'adresse1' => 11, 'adresse2' => 12, 'publicDestinataire' => 13, 'contacts' => 14, 'divers' => 15, 'matiere1' => 16, 'matiere2' => 17, 'elevePeutModifier' => 18, 'profPeutModifier' => 19, 'cpePeutModifier' => 20, 'fichePublique' => 21, 'afficheAdresse1' => 22, 'enConstruction' => 23, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::NOM => 1, self::NUMERO => 2, self::INDICE_AID => 3, self::PERSO1 => 4, self::PERSO2 => 5, self::PERSO3 => 6, self::PRODUCTIONS => 7, self::RESUME => 8, self::FAMILLE => 9, self::MOTS_CLES => 10, self::ADRESSE1 => 11, self::ADRESSE2 => 12, self::PUBLIC_DESTINATAIRE => 13, self::CONTACTS => 14, self::DIVERS => 15, self::MATIERE1 => 16, self::MATIERE2 => 17, self::ELEVE_PEUT_MODIFIER => 18, self::PROF_PEUT_MODIFIER => 19, self::CPE_PEUT_MODIFIER => 20, self::FICHE_PUBLIQUE => 21, self::AFFICHE_ADRESSE1 => 22, self::EN_CONSTRUCTION => 23, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'NOM' => 1, 'NUMERO' => 2, 'INDICE_AID' => 3, 'PERSO1' => 4, 'PERSO2' => 5, 'PERSO3' => 6, 'PRODUCTIONS' => 7, 'RESUME' => 8, 'FAMILLE' => 9, 'MOTS_CLES' => 10, 'ADRESSE1' => 11, 'ADRESSE2' => 12, 'PUBLIC_DESTINATAIRE' => 13, 'CONTACTS' => 14, 'DIVERS' => 15, 'MATIERE1' => 16, 'MATIERE2' => 17, 'ELEVE_PEUT_MODIFIER' => 18, 'PROF_PEUT_MODIFIER' => 19, 'CPE_PEUT_MODIFIER' => 20, 'FICHE_PUBLIQUE' => 21, 'AFFICHE_ADRESSE1' => 22, 'EN_CONSTRUCTION' => 23, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'nom' => 1, 'numero' => 2, 'indice_aid' => 3, 'perso1' => 4, 'perso2' => 5, 'perso3' => 6, 'productions' => 7, 'resume' => 8, 'famille' => 9, 'mots_cles' => 10, 'adresse1' => 11, 'adresse2' => 12, 'public_destinataire' => 13, 'contacts' => 14, 'divers' => 15, 'matiere1' => 16, 'matiere2' => 17, 'eleve_peut_modifier' => 18, 'prof_peut_modifier' => 19, 'cpe_peut_modifier' => 20, 'fiche_publique' => 21, 'affiche_adresse1' => 22, 'en_construction' => 23, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
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
	 * @param      string $column The column name for current table. (i.e. AidDetailsPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(AidDetailsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(AidDetailsPeer::ID);
			$criteria->addSelectColumn(AidDetailsPeer::NOM);
			$criteria->addSelectColumn(AidDetailsPeer::NUMERO);
			$criteria->addSelectColumn(AidDetailsPeer::INDICE_AID);
			$criteria->addSelectColumn(AidDetailsPeer::PERSO1);
			$criteria->addSelectColumn(AidDetailsPeer::PERSO2);
			$criteria->addSelectColumn(AidDetailsPeer::PERSO3);
			$criteria->addSelectColumn(AidDetailsPeer::PRODUCTIONS);
			$criteria->addSelectColumn(AidDetailsPeer::RESUME);
			$criteria->addSelectColumn(AidDetailsPeer::FAMILLE);
			$criteria->addSelectColumn(AidDetailsPeer::MOTS_CLES);
			$criteria->addSelectColumn(AidDetailsPeer::ADRESSE1);
			$criteria->addSelectColumn(AidDetailsPeer::ADRESSE2);
			$criteria->addSelectColumn(AidDetailsPeer::PUBLIC_DESTINATAIRE);
			$criteria->addSelectColumn(AidDetailsPeer::CONTACTS);
			$criteria->addSelectColumn(AidDetailsPeer::DIVERS);
			$criteria->addSelectColumn(AidDetailsPeer::MATIERE1);
			$criteria->addSelectColumn(AidDetailsPeer::MATIERE2);
			$criteria->addSelectColumn(AidDetailsPeer::ELEVE_PEUT_MODIFIER);
			$criteria->addSelectColumn(AidDetailsPeer::PROF_PEUT_MODIFIER);
			$criteria->addSelectColumn(AidDetailsPeer::CPE_PEUT_MODIFIER);
			$criteria->addSelectColumn(AidDetailsPeer::FICHE_PUBLIQUE);
			$criteria->addSelectColumn(AidDetailsPeer::AFFICHE_ADRESSE1);
			$criteria->addSelectColumn(AidDetailsPeer::EN_CONSTRUCTION);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.NOM');
			$criteria->addSelectColumn($alias . '.NUMERO');
			$criteria->addSelectColumn($alias . '.INDICE_AID');
			$criteria->addSelectColumn($alias . '.PERSO1');
			$criteria->addSelectColumn($alias . '.PERSO2');
			$criteria->addSelectColumn($alias . '.PERSO3');
			$criteria->addSelectColumn($alias . '.PRODUCTIONS');
			$criteria->addSelectColumn($alias . '.RESUME');
			$criteria->addSelectColumn($alias . '.FAMILLE');
			$criteria->addSelectColumn($alias . '.MOTS_CLES');
			$criteria->addSelectColumn($alias . '.ADRESSE1');
			$criteria->addSelectColumn($alias . '.ADRESSE2');
			$criteria->addSelectColumn($alias . '.PUBLIC_DESTINATAIRE');
			$criteria->addSelectColumn($alias . '.CONTACTS');
			$criteria->addSelectColumn($alias . '.DIVERS');
			$criteria->addSelectColumn($alias . '.MATIERE1');
			$criteria->addSelectColumn($alias . '.MATIERE2');
			$criteria->addSelectColumn($alias . '.ELEVE_PEUT_MODIFIER');
			$criteria->addSelectColumn($alias . '.PROF_PEUT_MODIFIER');
			$criteria->addSelectColumn($alias . '.CPE_PEUT_MODIFIER');
			$criteria->addSelectColumn($alias . '.FICHE_PUBLIQUE');
			$criteria->addSelectColumn($alias . '.AFFICHE_ADRESSE1');
			$criteria->addSelectColumn($alias . '.EN_CONSTRUCTION');
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
		$criteria->setPrimaryTableName(AidDetailsPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AidDetailsPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     AidDetails
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = AidDetailsPeer::doSelect($critcopy, $con);
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
		return AidDetailsPeer::populateObjects(AidDetailsPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			AidDetailsPeer::addSelectColumns($criteria);
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
	 * @param      AidDetails $value A AidDetails object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getId();
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
	 * @param      mixed $value A AidDetails object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof AidDetails) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AidDetails object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     AidDetails Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to aid
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
		// Invalidate objects in JAidUtilisateursProfessionnelsPeer instance pool,
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JAidUtilisateursProfessionnelsPeer::clearInstancePool();
		// Invalidate objects in JAidElevesPeer instance pool,
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JAidElevesPeer::clearInstancePool();
		// Invalidate objects in AbsenceEleveSaisiePeer instance pool,
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		AbsenceEleveSaisiePeer::clearInstancePool();
		// Invalidate objects in EdtEmplacementCoursPeer instance pool,
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		EdtEmplacementCoursPeer::clearInstancePool();
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
		return (string) $row[$startcol];
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
		$cls = AidDetailsPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = AidDetailsPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = AidDetailsPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				AidDetailsPeer::addInstanceToPool($obj, $key);
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
	 * @return     array (AidDetails object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = AidDetailsPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + AidDetailsPeer::NUM_HYDRATE_COLUMNS;
		} else {
			$cls = AidDetailsPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			AidDetailsPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}


	/**
	 * Returns the number of rows matching criteria, joining the related AidConfiguration table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAidConfiguration(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AidDetailsPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AidDetailsPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AidDetailsPeer::INDICE_AID, AidConfigurationPeer::INDICE_AID, $join_behavior);

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
	 * Selects a collection of AidDetails objects pre-filled with their AidConfiguration objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AidDetails objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAidConfiguration(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol = AidDetailsPeer::NUM_HYDRATE_COLUMNS;
		AidConfigurationPeer::addSelectColumns($criteria);

		$criteria->addJoin(AidDetailsPeer::INDICE_AID, AidConfigurationPeer::INDICE_AID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AidDetailsPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AidDetailsPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AidDetailsPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AidConfigurationPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AidConfigurationPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AidConfigurationPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AidConfigurationPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AidDetails) to $obj2 (AidConfiguration)
				$obj2->addAidDetails($obj1);

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
		$criteria->setPrimaryTableName(AidDetailsPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AidDetailsPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AidDetailsPeer::INDICE_AID, AidConfigurationPeer::INDICE_AID, $join_behavior);

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
	 * Selects a collection of AidDetails objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AidDetails objects.
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

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol2 = AidDetailsPeer::NUM_HYDRATE_COLUMNS;

		AidConfigurationPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + AidConfigurationPeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(AidDetailsPeer::INDICE_AID, AidConfigurationPeer::INDICE_AID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AidDetailsPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AidDetailsPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AidDetailsPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined AidConfiguration rows

			$key2 = AidConfigurationPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = AidConfigurationPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AidConfigurationPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					AidConfigurationPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (AidDetails) to the collection in $obj2 (AidConfiguration)
				$obj2->addAidDetails($obj1);
			} // if joined row not null

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
	  $dbMap = Propel::getDatabaseMap(BaseAidDetailsPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseAidDetailsPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new AidDetailsTableMap());
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
		return $withPrefix ? AidDetailsPeer::CLASS_DEFAULT : AidDetailsPeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a AidDetails or Criteria object.
	 *
	 * @param      mixed $values Criteria or AidDetails object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from AidDetails object
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
	 * Performs an UPDATE on the database, given a AidDetails or Criteria object.
	 *
	 * @param      mixed $values Criteria or AidDetails object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(AidDetailsPeer::ID);
			$value = $criteria->remove(AidDetailsPeer::ID);
			if ($value) {
				$selectCriteria->add(AidDetailsPeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(AidDetailsPeer::TABLE_NAME);
			}

		} else { // $values is AidDetails object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the aid table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += AidDetailsPeer::doOnDeleteCascade(new Criteria(AidDetailsPeer::DATABASE_NAME), $con);
			AidDetailsPeer::doOnDeleteSetNull(new Criteria(AidDetailsPeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(AidDetailsPeer::TABLE_NAME, $con, AidDetailsPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			AidDetailsPeer::clearInstancePool();
			AidDetailsPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs a DELETE on the database, given a AidDetails or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or AidDetails object or primary key or array of primary keys
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
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof AidDetails) { // it's a model object
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(AidDetailsPeer::ID, (array) $values, Criteria::IN);
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
			$affectedRows += AidDetailsPeer::doOnDeleteCascade($c, $con);
			
			// cloning the Criteria in case it's modified by doSelect() or doSelectStmt()
			$c = clone $criteria;
			AidDetailsPeer::doOnDeleteSetNull($c, $con);
			
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			if ($values instanceof Criteria) {
				AidDetailsPeer::clearInstancePool();
			} elseif ($values instanceof AidDetails) { // it's a model object
				AidDetailsPeer::removeInstanceFromPool($values);
			} else { // it's a primary key, or an array of pks
				foreach ((array) $values as $singleval) {
					AidDetailsPeer::removeInstanceFromPool($singleval);
				}
			}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			AidDetailsPeer::clearRelatedInstancePool();
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
		$objects = AidDetailsPeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related JAidUtilisateursProfessionnels objects
			$criteria = new Criteria(JAidUtilisateursProfessionnelsPeer::DATABASE_NAME);
			
			$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_AID, $obj->getId());
			$affectedRows += JAidUtilisateursProfessionnelsPeer::doDelete($criteria, $con);

			// delete related JAidEleves objects
			$criteria = new Criteria(JAidElevesPeer::DATABASE_NAME);
			
			$criteria->add(JAidElevesPeer::ID_AID, $obj->getId());
			$affectedRows += JAidElevesPeer::doDelete($criteria, $con);

			// delete related EdtEmplacementCours objects
			$criteria = new Criteria(EdtEmplacementCoursPeer::DATABASE_NAME);
			
			$criteria->add(EdtEmplacementCoursPeer::ID_AID, $obj->getId());
			$affectedRows += EdtEmplacementCoursPeer::doDelete($criteria, $con);
		}
		return $affectedRows;
	}

	/**
	 * This is a method for emulating ON DELETE SET NULL DBs that don't support this
	 * feature (like MySQL or SQLite).
	 *
	 * This method is not very speedy because it must perform a query first to get
	 * the implicated records and then perform the deletes by calling those Peer classes.
	 *
	 * This method should be used within a transaction if possible.
	 *
	 * @param      Criteria $criteria
	 * @param      PropelPDO $con
	 * @return     void
	 */
	protected static function doOnDeleteSetNull(Criteria $criteria, PropelPDO $con)
	{

		// first find the objects that are implicated by the $criteria
		$objects = AidDetailsPeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {

			// set fkey col in related AbsenceEleveSaisie rows to NULL
			$selectCriteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
			$updateValues = new Criteria(AidDetailsPeer::DATABASE_NAME);
			$selectCriteria->add(AbsenceEleveSaisiePeer::ID_AID, $obj->getId());
			$updateValues->add(AbsenceEleveSaisiePeer::ID_AID, null);

			BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

		}
	}

	/**
	 * Validates all modified columns of given AidDetails object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AidDetails $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(AidDetailsPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(AidDetailsPeer::TABLE_NAME);

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

		return BasePeer::doValidate(AidDetailsPeer::DATABASE_NAME, AidDetailsPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     AidDetails
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = AidDetailsPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
		$criteria->add(AidDetailsPeer::ID, $pk);

		$v = AidDetailsPeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
			$criteria->add(AidDetailsPeer::ID, $pks, Criteria::IN);
			$objs = AidDetailsPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseAidDetailsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAidDetailsPeer::buildTableMap();

