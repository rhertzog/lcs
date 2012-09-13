<?php


/**
 * Base class that represents a row from the 'a_justifications' table.
 *
 * Liste des justifications possibles pour une absence
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveJustification extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'AbsenceEleveJustificationPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceEleveJustificationPeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the nom field.
	 * @var        string
	 */
	protected $nom;

	/**
	 * The value for the commentaire field.
	 * @var        string
	 */
	protected $commentaire;

	/**
	 * The value for the sortable_rank field.
	 * @var        int
	 */
	protected $sortable_rank;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the updated_at field.
	 * @var        string
	 */
	protected $updated_at;

	/**
	 * @var        array AbsenceEleveTraitement[] Collection to store aggregation of AbsenceEleveTraitement objects.
	 */
	protected $collAbsenceEleveTraitements;

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

	// sortable behavior
	
	/**
	 * Queries to be executed in the save transaction
	 * @var        array
	 */
	protected $sortableQueries = array();

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceEleveTraitementsScheduledForDeletion = null;

	/**
	 * Get the [id] column value.
	 * cle primaire auto-incrementee
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [nom] column value.
	 * Nom de la justification
	 * @return     string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Get the [commentaire] column value.
	 * commentaire saisi par l'utilisateur
	 * @return     string
	 */
	public function getCommentaire()
	{
		return $this->commentaire;
	}

	/**
	 * Get the [sortable_rank] column value.
	 * 
	 * @return     int
	 */
	public function getSortableRank()
	{
		return $this->sortable_rank;
	}

	/**
	 * Get the [optionally formatted] temporal [created_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->created_at === null) {
			return null;
		}


		if ($this->created_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->created_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
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
	 * Get the [optionally formatted] temporal [updated_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUpdatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->updated_at === null) {
			return null;
		}


		if ($this->updated_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->updated_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
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
	 * Set the value of [id] column.
	 * cle primaire auto-incrementee
	 * @param      int $v new value
	 * @return     AbsenceEleveJustification The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceEleveJustificationPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [nom] column.
	 * Nom de la justification
	 * @param      string $v new value
	 * @return     AbsenceEleveJustification The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v) {
			$this->nom = $v;
			$this->modifiedColumns[] = AbsenceEleveJustificationPeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [commentaire] column.
	 * commentaire saisi par l'utilisateur
	 * @param      string $v new value
	 * @return     AbsenceEleveJustification The current object (for fluent API support)
	 */
	public function setCommentaire($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commentaire !== $v) {
			$this->commentaire = $v;
			$this->modifiedColumns[] = AbsenceEleveJustificationPeer::COMMENTAIRE;
		}

		return $this;
	} // setCommentaire()

	/**
	 * Set the value of [sortable_rank] column.
	 * 
	 * @param      int $v new value
	 * @return     AbsenceEleveJustification The current object (for fluent API support)
	 */
	public function setSortableRank($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->sortable_rank !== $v) {
			$this->sortable_rank = $v;
			$this->modifiedColumns[] = AbsenceEleveJustificationPeer::SORTABLE_RANK;
		}

		return $this;
	} // setSortableRank()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveJustification The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->created_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveJustificationPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveJustification The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->updated_at !== null || $dt !== null) {
			$currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->updated_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveJustificationPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

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

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->nom = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->commentaire = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->sortable_rank = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->created_at = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->updated_at = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 6; // 6 = AbsenceEleveJustificationPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceEleveJustification object", $e);
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
			$con = Propel::getConnection(AbsenceEleveJustificationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceEleveJustificationPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collAbsenceEleveTraitements = null;

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
			$con = Propel::getConnection(AbsenceEleveJustificationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = AbsenceEleveJustificationQuery::create()
				->filterByPrimaryKey($this->getPrimaryKey());
			$ret = $this->preDelete($con);
			// sortable behavior
			
			AbsenceEleveJustificationPeer::shiftRank(-1, $this->getSortableRank() + 1, null, $con);
			AbsenceEleveJustificationPeer::clearInstancePool();

			if ($ret) {
				$deleteQuery->delete($con);
				$this->postDelete($con);
				$con->commit();
				$this->setDeleted(true);
			} else {
				$con->commit();
			}
		} catch (Exception $e) {
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
			$con = Propel::getConnection(AbsenceEleveJustificationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			// sortable behavior
			$this->processSortableQueries($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
				// sortable behavior
				if (!$this->isColumnModified(AbsenceEleveJustificationPeer::RANK_COL)) {
					$this->setSortableRank(AbsenceEleveJustificationQuery::create()->getMaxRank($con) + 1);
				}

				// timestampable behavior
				if (!$this->isColumnModified(AbsenceEleveJustificationPeer::CREATED_AT)) {
					$this->setCreatedAt(time());
				}
				if (!$this->isColumnModified(AbsenceEleveJustificationPeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			} else {
				$ret = $ret && $this->preUpdate($con);
				// timestampable behavior
				if ($this->isModified() && !$this->isColumnModified(AbsenceEleveJustificationPeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			}
			if ($ret) {
				$affectedRows = $this->doSave($con);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				AbsenceEleveJustificationPeer::addInstanceToPool($this);
			} else {
				$affectedRows = 0;
			}
			$con->commit();
			return $affectedRows;
		} catch (Exception $e) {
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

			if ($this->isNew() || $this->isModified()) {
				// persist changes
				if ($this->isNew()) {
					$this->doInsert($con);
				} else {
					$this->doUpdate($con);
				}
				$affectedRows += 1;
				$this->resetModified();
			}

			if ($this->absenceEleveTraitementsScheduledForDeletion !== null) {
				if (!$this->absenceEleveTraitementsScheduledForDeletion->isEmpty()) {
					AbsenceEleveTraitementQuery::create()
						->filterByPrimaryKeys($this->absenceEleveTraitementsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->absenceEleveTraitementsScheduledForDeletion = null;
				}
			}

			if ($this->collAbsenceEleveTraitements !== null) {
				foreach ($this->collAbsenceEleveTraitements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

	/**
	 * Insert the row in the database.
	 *
	 * @param      PropelPDO $con
	 *
	 * @throws     PropelException
	 * @see        doSave()
	 */
	protected function doInsert(PropelPDO $con)
	{
		$modifiedColumns = array();
		$index = 0;

		$this->modifiedColumns[] = AbsenceEleveJustificationPeer::ID;
		if (null !== $this->id) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . AbsenceEleveJustificationPeer::ID . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::ID)) {
			$modifiedColumns[':p' . $index++]  = 'ID';
		}
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::NOM)) {
			$modifiedColumns[':p' . $index++]  = 'NOM';
		}
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::COMMENTAIRE)) {
			$modifiedColumns[':p' . $index++]  = 'COMMENTAIRE';
		}
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::SORTABLE_RANK)) {
			$modifiedColumns[':p' . $index++]  = 'SORTABLE_RANK';
		}
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::CREATED_AT)) {
			$modifiedColumns[':p' . $index++]  = 'CREATED_AT';
		}
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::UPDATED_AT)) {
			$modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
		}

		$sql = sprintf(
			'INSERT INTO a_justifications (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'ID':
						$stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
						break;
					case 'NOM':
						$stmt->bindValue($identifier, $this->nom, PDO::PARAM_STR);
						break;
					case 'COMMENTAIRE':
						$stmt->bindValue($identifier, $this->commentaire, PDO::PARAM_STR);
						break;
					case 'SORTABLE_RANK':
						$stmt->bindValue($identifier, $this->sortable_rank, PDO::PARAM_INT);
						break;
					case 'CREATED_AT':
						$stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
						break;
					case 'UPDATED_AT':
						$stmt->bindValue($identifier, $this->updated_at, PDO::PARAM_STR);
						break;
				}
			}
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
		}

		try {
			$pk = $con->lastInsertId();
		} catch (Exception $e) {
			throw new PropelException('Unable to get autoincrement id.', $e);
		}
		$this->setId($pk);

		$this->setNew(false);
	}

	/**
	 * Update the row in the database.
	 *
	 * @param      PropelPDO $con
	 *
	 * @see        doSave()
	 */
	protected function doUpdate(PropelPDO $con)
	{
		$selectCriteria = $this->buildPkeyCriteria();
		$valuesCriteria = $this->buildCriteria();
		BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
	}

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


			if (($retval = AbsenceEleveJustificationPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAbsenceEleveTraitements !== null) {
					foreach ($this->collAbsenceEleveTraitements as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
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
		$pos = AbsenceEleveJustificationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getId();
				break;
			case 1:
				return $this->getNom();
				break;
			case 2:
				return $this->getCommentaire();
				break;
			case 3:
				return $this->getSortableRank();
				break;
			case 4:
				return $this->getCreatedAt();
				break;
			case 5:
				return $this->getUpdatedAt();
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
	 * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
	{
		if (isset($alreadyDumpedObjects['AbsenceEleveJustification'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['AbsenceEleveJustification'][$this->getPrimaryKey()] = true;
		$keys = AbsenceEleveJustificationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getNom(),
			$keys[2] => $this->getCommentaire(),
			$keys[3] => $this->getSortableRank(),
			$keys[4] => $this->getCreatedAt(),
			$keys[5] => $this->getUpdatedAt(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->collAbsenceEleveTraitements) {
				$result['AbsenceEleveTraitements'] = $this->collAbsenceEleveTraitements->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
		}
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
		$pos = AbsenceEleveJustificationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setId($value);
				break;
			case 1:
				$this->setNom($value);
				break;
			case 2:
				$this->setCommentaire($value);
				break;
			case 3:
				$this->setSortableRank($value);
				break;
			case 4:
				$this->setCreatedAt($value);
				break;
			case 5:
				$this->setUpdatedAt($value);
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
		$keys = AbsenceEleveJustificationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNom($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCommentaire($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setSortableRank($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCreatedAt($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setUpdatedAt($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceEleveJustificationPeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceEleveJustificationPeer::ID)) $criteria->add(AbsenceEleveJustificationPeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::NOM)) $criteria->add(AbsenceEleveJustificationPeer::NOM, $this->nom);
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::COMMENTAIRE)) $criteria->add(AbsenceEleveJustificationPeer::COMMENTAIRE, $this->commentaire);
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::SORTABLE_RANK)) $criteria->add(AbsenceEleveJustificationPeer::SORTABLE_RANK, $this->sortable_rank);
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::CREATED_AT)) $criteria->add(AbsenceEleveJustificationPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(AbsenceEleveJustificationPeer::UPDATED_AT)) $criteria->add(AbsenceEleveJustificationPeer::UPDATED_AT, $this->updated_at);

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
		$criteria = new Criteria(AbsenceEleveJustificationPeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveJustificationPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getId();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of AbsenceEleveJustification (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setNom($this->getNom());
		$copyObj->setCommentaire($this->getCommentaire());
		$copyObj->setSortableRank($this->getSortableRank());
		$copyObj->setCreatedAt($this->getCreatedAt());
		$copyObj->setUpdatedAt($this->getUpdatedAt());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getAbsenceEleveTraitements() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveTraitement($relObj->copy($deepCopy));
				}
			}

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
			$copyObj->setId(NULL); // this is a auto-increment column, so set to default value
		}
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
	 * @return     AbsenceEleveJustification Clone of current object.
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
	 * @return     AbsenceEleveJustificationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceEleveJustificationPeer();
		}
		return self::$peer;
	}


	/**
	 * Initializes a collection based on the name of a relation.
	 * Avoids crafting an 'init[$relationName]s' method name
	 * that wouldn't work when StandardEnglishPluralizer is used.
	 *
	 * @param      string $relationName The name of the relation to initialize
	 * @return     void
	 */
	public function initRelation($relationName)
	{
		if ('AbsenceEleveTraitement' == $relationName) {
			return $this->initAbsenceEleveTraitements();
		}
	}

	/**
	 * Clears out the collAbsenceEleveTraitements collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveTraitements()
	 */
	public function clearAbsenceEleveTraitements()
	{
		$this->collAbsenceEleveTraitements = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveTraitements collection.
	 *
	 * By default this just sets the collAbsenceEleveTraitements collection to an empty array (like clearcollAbsenceEleveTraitements());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAbsenceEleveTraitements($overrideExisting = true)
	{
		if (null !== $this->collAbsenceEleveTraitements && !$overrideExisting) {
			return;
		}
		$this->collAbsenceEleveTraitements = new PropelObjectCollection();
		$this->collAbsenceEleveTraitements->setModel('AbsenceEleveTraitement');
	}

	/**
	 * Gets an array of AbsenceEleveTraitement objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveJustification is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 * @throws     PropelException
	 */
	public function getAbsenceEleveTraitements($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveTraitements || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveTraitements) {
				// return empty collection
				$this->initAbsenceEleveTraitements();
			} else {
				$collAbsenceEleveTraitements = AbsenceEleveTraitementQuery::create(null, $criteria)
					->filterByAbsenceEleveJustification($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceEleveTraitements;
				}
				$this->collAbsenceEleveTraitements = $collAbsenceEleveTraitements;
			}
		}
		return $this->collAbsenceEleveTraitements;
	}

	/**
	 * Sets a collection of AbsenceEleveTraitement objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $absenceEleveTraitements A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAbsenceEleveTraitements(PropelCollection $absenceEleveTraitements, PropelPDO $con = null)
	{
		$this->absenceEleveTraitementsScheduledForDeletion = $this->getAbsenceEleveTraitements(new Criteria(), $con)->diff($absenceEleveTraitements);

		foreach ($absenceEleveTraitements as $absenceEleveTraitement) {
			// Fix issue with collection modified by reference
			if ($absenceEleveTraitement->isNew()) {
				$absenceEleveTraitement->setAbsenceEleveJustification($this);
			}
			$this->addAbsenceEleveTraitement($absenceEleveTraitement);
		}

		$this->collAbsenceEleveTraitements = $absenceEleveTraitements;
	}

	/**
	 * Returns the number of related AbsenceEleveTraitement objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveTraitement objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveTraitements(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveTraitements || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveTraitements) {
				return 0;
			} else {
				$query = AbsenceEleveTraitementQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveJustification($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveTraitements);
		}
	}

	/**
	 * Method called to associate a AbsenceEleveTraitement object to this object
	 * through the AbsenceEleveTraitement foreign key attribute.
	 *
	 * @param      AbsenceEleveTraitement $l AbsenceEleveTraitement
	 * @return     AbsenceEleveJustification The current object (for fluent API support)
	 */
	public function addAbsenceEleveTraitement(AbsenceEleveTraitement $l)
	{
		if ($this->collAbsenceEleveTraitements === null) {
			$this->initAbsenceEleveTraitements();
		}
		if (!$this->collAbsenceEleveTraitements->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddAbsenceEleveTraitement($l);
		}

		return $this;
	}

	/**
	 * @param	AbsenceEleveTraitement $absenceEleveTraitement The absenceEleveTraitement object to add.
	 */
	protected function doAddAbsenceEleveTraitement($absenceEleveTraitement)
	{
		$this->collAbsenceEleveTraitements[]= $absenceEleveTraitement;
		$absenceEleveTraitement->setAbsenceEleveJustification($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveJustification is new, it will return
	 * an empty collection; or if this AbsenceEleveJustification has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveJustification.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitementsJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getAbsenceEleveTraitements($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveJustification is new, it will return
	 * an empty collection; or if this AbsenceEleveJustification has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveJustification.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveType($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveType', $join_behavior);

		return $this->getAbsenceEleveTraitements($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveJustification is new, it will return
	 * an empty collection; or if this AbsenceEleveJustification has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveJustification.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveMotif($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveMotif', $join_behavior);

		return $this->getAbsenceEleveTraitements($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveJustification is new, it will return
	 * an empty collection; or if this AbsenceEleveJustification has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveJustification.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitementsJoinModifieParUtilisateur($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('ModifieParUtilisateur', $join_behavior);

		return $this->getAbsenceEleveTraitements($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->nom = null;
		$this->commentaire = null;
		$this->sortable_rank = null;
		$this->created_at = null;
		$this->updated_at = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
		$this->resetModified();
		$this->setNew(true);
		$this->setDeleted(false);
	}

	/**
	 * Resets all references to other model objects or collections of model objects.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect
	 * objects with circular references (even in PHP 5.3). This is currently necessary
	 * when using Propel in certain daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all referrer objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
			if ($this->collAbsenceEleveTraitements) {
				foreach ($this->collAbsenceEleveTraitements as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collAbsenceEleveTraitements instanceof PropelCollection) {
			$this->collAbsenceEleveTraitements->clearIterator();
		}
		$this->collAbsenceEleveTraitements = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(AbsenceEleveJustificationPeer::DEFAULT_STRING_FORMAT);
	}

	// sortable behavior
	
	/**
	 * Wrap the getter for rank value
	 *
	 * @return    int
	 */
	public function getRank()
	{
		return $this->sortable_rank;
	}
	
	/**
	 * Wrap the setter for rank value
	 *
	 * @param     int
	 * @return    AbsenceEleveJustification
	 */
	public function setRank($v)
	{
		return $this->setSortableRank($v);
	}
	
	/**
	 * Check if the object is first in the list, i.e. if it has 1 for rank
	 *
	 * @return    boolean
	 */
	public function isFirst()
	{
		return $this->getSortableRank() == 1;
	}
	
	/**
	 * Check if the object is last in the list, i.e. if its rank is the highest rank
	 *
	 * @param     PropelPDO  $con      optional connection
	 *
	 * @return    boolean
	 */
	public function isLast(PropelPDO $con = null)
	{
		return $this->getSortableRank() == AbsenceEleveJustificationQuery::create()->getMaxRank($con);
	}
	
	/**
	 * Get the next item in the list, i.e. the one for which rank is immediately higher
	 *
	 * @param     PropelPDO  $con      optional connection
	 *
	 * @return    AbsenceEleveJustification
	 */
	public function getNext(PropelPDO $con = null)
	{
		return AbsenceEleveJustificationQuery::create()->findOneByRank($this->getSortableRank() + 1, $con);
	}
	
	/**
	 * Get the previous item in the list, i.e. the one for which rank is immediately lower
	 *
	 * @param     PropelPDO  $con      optional connection
	 *
	 * @return    AbsenceEleveJustification
	 */
	public function getPrevious(PropelPDO $con = null)
	{
		return AbsenceEleveJustificationQuery::create()->findOneByRank($this->getSortableRank() - 1, $con);
	}
	
	/**
	 * Insert at specified rank
	 * The modifications are not persisted until the object is saved.
	 *
	 * @param     integer    $rank rank value
	 * @param     PropelPDO  $con      optional connection
	 *
	 * @return    AbsenceEleveJustification the current object
	 *
	 * @throws    PropelException
	 */
	public function insertAtRank($rank, PropelPDO $con = null)
	{
		$maxRank = AbsenceEleveJustificationQuery::create()->getMaxRank($con);
		if ($rank < 1 || $rank > $maxRank + 1) {
			throw new PropelException('Invalid rank ' . $rank);
		}
		// move the object in the list, at the given rank
		$this->setSortableRank($rank);
		if ($rank != $maxRank + 1) {
			// Keep the list modification query for the save() transaction
			$this->sortableQueries []= array(
				'callable'  => array('AbsenceEleveJustificationPeer', 'shiftRank'),
				'arguments' => array(1, $rank, null, )
			);
		}
	
		return $this;
	}
	
	/**
	 * Insert in the last rank
	 * The modifications are not persisted until the object is saved.
	 *
	 * @param PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveJustification the current object
	 *
	 * @throws    PropelException
	 */
	public function insertAtBottom(PropelPDO $con = null)
	{
		$this->setSortableRank(AbsenceEleveJustificationQuery::create()->getMaxRank($con) + 1);
	
		return $this;
	}
	
	/**
	 * Insert in the first rank
	 * The modifications are not persisted until the object is saved.
	 *
	 * @return    AbsenceEleveJustification the current object
	 */
	public function insertAtTop()
	{
		return $this->insertAtRank(1);
	}
	
	/**
	 * Move the object to a new rank, and shifts the rank
	 * Of the objects inbetween the old and new rank accordingly
	 *
	 * @param     integer   $newRank rank value
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveJustification the current object
	 *
	 * @throws    PropelException
	 */
	public function moveToRank($newRank, PropelPDO $con = null)
	{
		if ($this->isNew()) {
			throw new PropelException('New objects cannot be moved. Please use insertAtRank() instead');
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveJustificationPeer::DATABASE_NAME);
		}
		if ($newRank < 1 || $newRank > AbsenceEleveJustificationQuery::create()->getMaxRank($con)) {
			throw new PropelException('Invalid rank ' . $newRank);
		}
	
		$oldRank = $this->getSortableRank();
		if ($oldRank == $newRank) {
			return $this;
		}
	
		$con->beginTransaction();
		try {
			// shift the objects between the old and the new rank
			$delta = ($oldRank < $newRank) ? -1 : 1;
			AbsenceEleveJustificationPeer::shiftRank($delta, min($oldRank, $newRank), max($oldRank, $newRank), $con);
	
			// move the object to its new rank
			$this->setSortableRank($newRank);
			$this->save($con);
	
			$con->commit();
			return $this;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Exchange the rank of the object with the one passed as argument, and saves both objects
	 *
	 * @param     AbsenceEleveJustification $object
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveJustification the current object
	 *
	 * @throws Exception if the database cannot execute the two updates
	 */
	public function swapWith($object, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveJustificationPeer::DATABASE_NAME);
		}
		$con->beginTransaction();
		try {
			$oldRank = $this->getSortableRank();
			$newRank = $object->getSortableRank();
			$this->setSortableRank($newRank);
			$this->save($con);
			$object->setSortableRank($oldRank);
			$object->save($con);
			$con->commit();
	
			return $this;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Move the object higher in the list, i.e. exchanges its rank with the one of the previous object
	 *
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveJustification the current object
	 */
	public function moveUp(PropelPDO $con = null)
	{
		if ($this->isFirst()) {
			return $this;
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveJustificationPeer::DATABASE_NAME);
		}
		$con->beginTransaction();
		try {
			$prev = $this->getPrevious($con);
			$this->swapWith($prev, $con);
			$con->commit();
	
			return $this;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Move the object higher in the list, i.e. exchanges its rank with the one of the next object
	 *
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveJustification the current object
	 */
	public function moveDown(PropelPDO $con = null)
	{
		if ($this->isLast($con)) {
			return $this;
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveJustificationPeer::DATABASE_NAME);
		}
		$con->beginTransaction();
		try {
			$next = $this->getNext($con);
			$this->swapWith($next, $con);
			$con->commit();
	
			return $this;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Move the object to the top of the list
	 *
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveJustification the current object
	 */
	public function moveToTop(PropelPDO $con = null)
	{
		if ($this->isFirst()) {
			return $this;
		}
		return $this->moveToRank(1, $con);
	}
	
	/**
	 * Move the object to the bottom of the list
	 *
	 * @param     PropelPDO $con optional connection
	 *
	 * @return integer the old object's rank
	 */
	public function moveToBottom(PropelPDO $con = null)
	{
		if ($this->isLast($con)) {
			return false;
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveJustificationPeer::DATABASE_NAME);
		}
		$con->beginTransaction();
		try {
			$bottom = AbsenceEleveJustificationQuery::create()->getMaxRank($con);
			$res = $this->moveToRank($bottom, $con);
			$con->commit();
	
			return $res;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Removes the current object from the list.
	 * The modifications are not persisted until the object is saved.
	 *
	 * @return    AbsenceEleveJustification the current object
	 */
	public function removeFromList()
	{
		// Keep the list modification query for the save() transaction
		$this->sortableQueries []= array(
			'callable'  => array('AbsenceEleveJustificationPeer', 'shiftRank'),
			'arguments' => array(-1, $this->getSortableRank() + 1, null)
		);
		// remove the object from the list
		$this->setSortableRank(null);
	
		return $this;
	}
	
	/**
	 * Execute queries that were saved to be run inside the save transaction
	 */
	protected function processSortableQueries($con)
	{
		foreach ($this->sortableQueries as $query) {
			$query['arguments'][]= $con;
			call_user_func_array($query['callable'], $query['arguments']);
		}
		$this->sortableQueries = array();
	}

	// timestampable behavior
	
	/**
	 * Mark the current object so that the update date doesn't get updated during next save
	 *
	 * @return     AbsenceEleveJustification The current object (for fluent API support)
	 */
	public function keepUpdateDateUnchanged()
	{
		$this->modifiedColumns[] = AbsenceEleveJustificationPeer::UPDATED_AT;
		return $this;
	}

} // BaseAbsenceEleveJustification
