<?php


/**
 * Base class that represents a row from the 'horaires_etablissement' table.
 *
 * Table contenant les heures d'ouverture et de fermeture de l'etablissement par journee
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtHorairesEtablissement extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'EdtHorairesEtablissementPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EdtHorairesEtablissementPeer
	 */
	protected static $peer;

	/**
	 * The value for the id_horaire_etablissement field.
	 * @var        int
	 */
	protected $id_horaire_etablissement;

	/**
	 * The value for the date_horaire_etablissement field.
	 * @var        string
	 */
	protected $date_horaire_etablissement;

	/**
	 * The value for the jour_horaire_etablissement field.
	 * @var        string
	 */
	protected $jour_horaire_etablissement;

	/**
	 * The value for the ouverture_horaire_etablissement field.
	 * @var        string
	 */
	protected $ouverture_horaire_etablissement;

	/**
	 * The value for the fermeture_horaire_etablissement field.
	 * @var        string
	 */
	protected $fermeture_horaire_etablissement;

	/**
	 * The value for the pause_horaire_etablissement field.
	 * @var        string
	 */
	protected $pause_horaire_etablissement;

	/**
	 * The value for the ouvert_horaire_etablissement field.
	 * @var        boolean
	 */
	protected $ouvert_horaire_etablissement;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Get the [id_horaire_etablissement] column value.
	 * cle primaire auto-incremente
	 * @return     int
	 */
	public function getIdHoraireEtablissement()
	{
		return $this->id_horaire_etablissement;
	}

	/**
	 * Get the [optionally formatted] temporal [date_horaire_etablissement] column value.
	 * NULL (c'etait un 0 a l'origine...voir si pb) = horaires valables toute l'annee pour le jour specifie - date precise = horaires valables uniquement pour cette date
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateHoraireEtablissement($format = '%x')
	{
		if ($this->date_horaire_etablissement === null) {
			return null;
		}


		if ($this->date_horaire_etablissement === '0000-00-00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->date_horaire_etablissement);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date_horaire_etablissement, true), $x);
			}
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [jour_horaire_etablissement] column value.
	 * defini le jour de la semaine - typiquement, lundi, mardi, etc...
	 * @return     string
	 */
	public function getJourHoraireEtablissement()
	{
		return $this->jour_horaire_etablissement;
	}

	/**
	 * Get the [optionally formatted] temporal [ouverture_horaire_etablissement] column value.
	 * Heure d'ouverture de l'etablissement
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getOuvertureHoraireEtablissement($format = '%X')
	{
		if ($this->ouverture_horaire_etablissement === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->ouverture_horaire_etablissement);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->ouverture_horaire_etablissement, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [fermeture_horaire_etablissement] column value.
	 * Heure de fermeture de l'etablissement
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getFermetureHoraireEtablissement($format = '%X')
	{
		if ($this->fermeture_horaire_etablissement === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->fermeture_horaire_etablissement);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fermeture_horaire_etablissement, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [pause_horaire_etablissement] column value.
	 * champ non utilise
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getPauseHoraireEtablissement($format = '%X')
	{
		if ($this->pause_horaire_etablissement === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->pause_horaire_etablissement);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->pause_horaire_etablissement, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [ouvert_horaire_etablissement] column value.
	 * 1 = etablissement ouvert - 0 = etablissement ferme
	 * @return     boolean
	 */
	public function getOuvertHoraireEtablissement()
	{
		return $this->ouvert_horaire_etablissement;
	}

	/**
	 * Set the value of [id_horaire_etablissement] column.
	 * cle primaire auto-incremente
	 * @param      int $v new value
	 * @return     EdtHorairesEtablissement The current object (for fluent API support)
	 */
	public function setIdHoraireEtablissement($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_horaire_etablissement !== $v) {
			$this->id_horaire_etablissement = $v;
			$this->modifiedColumns[] = EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT;
		}

		return $this;
	} // setIdHoraireEtablissement()

	/**
	 * Sets the value of [date_horaire_etablissement] column to a normalized version of the date/time value specified.
	 * NULL (c'etait un 0 a l'origine...voir si pb) = horaires valables toute l'annee pour le jour specifie - date precise = horaires valables uniquement pour cette date
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtHorairesEtablissement The current object (for fluent API support)
	 */
	public function setDateHoraireEtablissement($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->date_horaire_etablissement !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->date_horaire_etablissement !== null && $tmpDt = new DateTime($this->date_horaire_etablissement)) ? $tmpDt->format('Y-m-d') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->date_horaire_etablissement = ($dt ? $dt->format('Y-m-d') : null);
				$this->modifiedColumns[] = EdtHorairesEtablissementPeer::DATE_HORAIRE_ETABLISSEMENT;
			}
		} // if either are not null

		return $this;
	} // setDateHoraireEtablissement()

	/**
	 * Set the value of [jour_horaire_etablissement] column.
	 * defini le jour de la semaine - typiquement, lundi, mardi, etc...
	 * @param      string $v new value
	 * @return     EdtHorairesEtablissement The current object (for fluent API support)
	 */
	public function setJourHoraireEtablissement($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->jour_horaire_etablissement !== $v) {
			$this->jour_horaire_etablissement = $v;
			$this->modifiedColumns[] = EdtHorairesEtablissementPeer::JOUR_HORAIRE_ETABLISSEMENT;
		}

		return $this;
	} // setJourHoraireEtablissement()

	/**
	 * Sets the value of [ouverture_horaire_etablissement] column to a normalized version of the date/time value specified.
	 * Heure d'ouverture de l'etablissement
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtHorairesEtablissement The current object (for fluent API support)
	 */
	public function setOuvertureHoraireEtablissement($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->ouverture_horaire_etablissement !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->ouverture_horaire_etablissement !== null && $tmpDt = new DateTime($this->ouverture_horaire_etablissement)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->ouverture_horaire_etablissement = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = EdtHorairesEtablissementPeer::OUVERTURE_HORAIRE_ETABLISSEMENT;
			}
		} // if either are not null

		return $this;
	} // setOuvertureHoraireEtablissement()

	/**
	 * Sets the value of [fermeture_horaire_etablissement] column to a normalized version of the date/time value specified.
	 * Heure de fermeture de l'etablissement
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtHorairesEtablissement The current object (for fluent API support)
	 */
	public function setFermetureHoraireEtablissement($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->fermeture_horaire_etablissement !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->fermeture_horaire_etablissement !== null && $tmpDt = new DateTime($this->fermeture_horaire_etablissement)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->fermeture_horaire_etablissement = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = EdtHorairesEtablissementPeer::FERMETURE_HORAIRE_ETABLISSEMENT;
			}
		} // if either are not null

		return $this;
	} // setFermetureHoraireEtablissement()

	/**
	 * Sets the value of [pause_horaire_etablissement] column to a normalized version of the date/time value specified.
	 * champ non utilise
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtHorairesEtablissement The current object (for fluent API support)
	 */
	public function setPauseHoraireEtablissement($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->pause_horaire_etablissement !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->pause_horaire_etablissement !== null && $tmpDt = new DateTime($this->pause_horaire_etablissement)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->pause_horaire_etablissement = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = EdtHorairesEtablissementPeer::PAUSE_HORAIRE_ETABLISSEMENT;
			}
		} // if either are not null

		return $this;
	} // setPauseHoraireEtablissement()

	/**
	 * Set the value of [ouvert_horaire_etablissement] column.
	 * 1 = etablissement ouvert - 0 = etablissement ferme
	 * @param      boolean $v new value
	 * @return     EdtHorairesEtablissement The current object (for fluent API support)
	 */
	public function setOuvertHoraireEtablissement($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->ouvert_horaire_etablissement !== $v) {
			$this->ouvert_horaire_etablissement = $v;
			$this->modifiedColumns[] = EdtHorairesEtablissementPeer::OUVERT_HORAIRE_ETABLISSEMENT;
		}

		return $this;
	} // setOuvertHoraireEtablissement()

	/**
	 * Indicates whether the columns in this object are only set to default values.
	 *
	 * This method can be used in conjunction with isModified() to indicate whether an object is both
	 * modified _and_ has some values set which are non-default.
	 *
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
		// otherwise, everything was equal, so return TRUE
		return true;
	} // hasOnlyDefaultValues()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (0-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
	 * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
	 * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate($row, $startcol = 0, $rehydrate = false)
	{
		try {

			$this->id_horaire_etablissement = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->date_horaire_etablissement = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->jour_horaire_etablissement = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->ouverture_horaire_etablissement = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->fermeture_horaire_etablissement = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->pause_horaire_etablissement = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->ouvert_horaire_etablissement = ($row[$startcol + 6] !== null) ? (boolean) $row[$startcol + 6] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 7; // 7 = EdtHorairesEtablissementPeer::NUM_COLUMNS - EdtHorairesEtablissementPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EdtHorairesEtablissement object", $e);
		}
	}

	/**
	 * Checks and repairs the internal consistency of the object.
	 *
	 * This method is executed after an already-instantiated object is re-hydrated
	 * from the database.  It exists to check any foreign keys to make sure that
	 * the objects related to the current object are correct based on foreign key.
	 *
	 * You can override this method in the stub class, but you should always invoke
	 * the base method from the overridden method (i.e. parent::ensureConsistency()),
	 * in case your model changes.
	 *
	 * @throws     PropelException
	 */
	public function ensureConsistency()
	{

	} // ensureConsistency

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
	 */
	public function reload($deep = false, PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("Cannot reload a deleted object.");
		}

		if ($this->isNew()) {
			throw new PropelException("Cannot reload an unsaved object.");
		}

		if ($con === null) {
			$con = Propel::getConnection(EdtHorairesEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = EdtHorairesEtablissementPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

		} // if (deep)
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(EdtHorairesEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				EdtHorairesEtablissementQuery::create()
					->filterByPrimaryKey($this->getPrimaryKey())
					->delete($con);
				$this->postDelete($con);
				$con->commit();
				$this->setDeleted(true);
			} else {
				$con->commit();
			}
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Persists this object to the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All modified related objects will also be persisted in the doSave()
	 * method.  This method wraps all precipitate database operations in a
	 * single transaction.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(EdtHorairesEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
			} else {
				$ret = $ret && $this->preUpdate($con);
			}
			if ($ret) {
				$affectedRows = $this->doSave($con);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				EdtHorairesEtablissementPeer::addInstanceToPool($this);
			} else {
				$affectedRows = 0;
			}
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs the work of inserting or updating the row in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave(PropelPDO $con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;

			if ($this->isNew() ) {
				$this->modifiedColumns[] = EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows = 1;
					$this->setIdHoraireEtablissement($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows = EdtHorairesEtablissementPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			if (($retval = EdtHorairesEtablissementPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = EdtHorairesEtablissementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		$field = $this->getByPosition($pos);
		return $field;
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getIdHoraireEtablissement();
				break;
			case 1:
				return $this->getDateHoraireEtablissement();
				break;
			case 2:
				return $this->getJourHoraireEtablissement();
				break;
			case 3:
				return $this->getOuvertureHoraireEtablissement();
				break;
			case 4:
				return $this->getFermetureHoraireEtablissement();
				break;
			case 5:
				return $this->getPauseHoraireEtablissement();
				break;
			case 6:
				return $this->getOuvertHoraireEtablissement();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. 
	 *                    Defaults to BasePeer::TYPE_PHPNAME.
	 * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true)
	{
		$keys = EdtHorairesEtablissementPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getIdHoraireEtablissement(),
			$keys[1] => $this->getDateHoraireEtablissement(),
			$keys[2] => $this->getJourHoraireEtablissement(),
			$keys[3] => $this->getOuvertureHoraireEtablissement(),
			$keys[4] => $this->getFermetureHoraireEtablissement(),
			$keys[5] => $this->getPauseHoraireEtablissement(),
			$keys[6] => $this->getOuvertHoraireEtablissement(),
		);
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = EdtHorairesEtablissementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setIdHoraireEtablissement($value);
				break;
			case 1:
				$this->setDateHoraireEtablissement($value);
				break;
			case 2:
				$this->setJourHoraireEtablissement($value);
				break;
			case 3:
				$this->setOuvertureHoraireEtablissement($value);
				break;
			case 4:
				$this->setFermetureHoraireEtablissement($value);
				break;
			case 5:
				$this->setPauseHoraireEtablissement($value);
				break;
			case 6:
				$this->setOuvertHoraireEtablissement($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = EdtHorairesEtablissementPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setIdHoraireEtablissement($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDateHoraireEtablissement($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setJourHoraireEtablissement($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setOuvertureHoraireEtablissement($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFermetureHoraireEtablissement($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPauseHoraireEtablissement($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setOuvertHoraireEtablissement($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EdtHorairesEtablissementPeer::DATABASE_NAME);

		if ($this->isColumnModified(EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT)) $criteria->add(EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT, $this->id_horaire_etablissement);
		if ($this->isColumnModified(EdtHorairesEtablissementPeer::DATE_HORAIRE_ETABLISSEMENT)) $criteria->add(EdtHorairesEtablissementPeer::DATE_HORAIRE_ETABLISSEMENT, $this->date_horaire_etablissement);
		if ($this->isColumnModified(EdtHorairesEtablissementPeer::JOUR_HORAIRE_ETABLISSEMENT)) $criteria->add(EdtHorairesEtablissementPeer::JOUR_HORAIRE_ETABLISSEMENT, $this->jour_horaire_etablissement);
		if ($this->isColumnModified(EdtHorairesEtablissementPeer::OUVERTURE_HORAIRE_ETABLISSEMENT)) $criteria->add(EdtHorairesEtablissementPeer::OUVERTURE_HORAIRE_ETABLISSEMENT, $this->ouverture_horaire_etablissement);
		if ($this->isColumnModified(EdtHorairesEtablissementPeer::FERMETURE_HORAIRE_ETABLISSEMENT)) $criteria->add(EdtHorairesEtablissementPeer::FERMETURE_HORAIRE_ETABLISSEMENT, $this->fermeture_horaire_etablissement);
		if ($this->isColumnModified(EdtHorairesEtablissementPeer::PAUSE_HORAIRE_ETABLISSEMENT)) $criteria->add(EdtHorairesEtablissementPeer::PAUSE_HORAIRE_ETABLISSEMENT, $this->pause_horaire_etablissement);
		if ($this->isColumnModified(EdtHorairesEtablissementPeer::OUVERT_HORAIRE_ETABLISSEMENT)) $criteria->add(EdtHorairesEtablissementPeer::OUVERT_HORAIRE_ETABLISSEMENT, $this->ouvert_horaire_etablissement);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(EdtHorairesEtablissementPeer::DATABASE_NAME);
		$criteria->add(EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT, $this->id_horaire_etablissement);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getIdHoraireEtablissement();
	}

	/**
	 * Generic method to set the primary key (id_horaire_etablissement column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setIdHoraireEtablissement($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getIdHoraireEtablissement();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of EdtHorairesEtablissement (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setDateHoraireEtablissement($this->date_horaire_etablissement);
		$copyObj->setJourHoraireEtablissement($this->jour_horaire_etablissement);
		$copyObj->setOuvertureHoraireEtablissement($this->ouverture_horaire_etablissement);
		$copyObj->setFermetureHoraireEtablissement($this->fermeture_horaire_etablissement);
		$copyObj->setPauseHoraireEtablissement($this->pause_horaire_etablissement);
		$copyObj->setOuvertHoraireEtablissement($this->ouvert_horaire_etablissement);

		$copyObj->setNew(true);
		$copyObj->setIdHoraireEtablissement(NULL); // this is a auto-increment column, so set to default value
	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     EdtHorairesEtablissement Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     EdtHorairesEtablissementPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EdtHorairesEtablissementPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id_horaire_etablissement = null;
		$this->date_horaire_etablissement = null;
		$this->jour_horaire_etablissement = null;
		$this->ouverture_horaire_etablissement = null;
		$this->fermeture_horaire_etablissement = null;
		$this->pause_horaire_etablissement = null;
		$this->ouvert_horaire_etablissement = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
		$this->resetModified();
		$this->setNew(true);
		$this->setDeleted(false);
	}

	/**
	 * Resets all collections of referencing foreign keys.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect objects
	 * with circular references.  This is currently necessary when using Propel in certain
	 * daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all associated objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
		} // if ($deep)

	}

	/**
	 * Catches calls to virtual methods
	 */
	public function __call($name, $params)
	{
		if (preg_match('/get(\w+)/', $name, $matches)) {
			$virtualColumn = $matches[1];
			if ($this->hasVirtualColumn($virtualColumn)) {
				return $this->getVirtualColumn($virtualColumn);
			}
			// no lcfirst in php<5.3...
			$virtualColumn[0] = strtolower($virtualColumn[0]);
			if ($this->hasVirtualColumn($virtualColumn)) {
				return $this->getVirtualColumn($virtualColumn);
			}
		}
		return parent::__call($name, $params);
	}

} // BaseEdtHorairesEtablissement
