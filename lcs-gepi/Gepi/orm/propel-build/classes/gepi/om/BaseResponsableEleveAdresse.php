<?php


/**
 * Base class that represents a row from the 'resp_adr' table.
 *
 * Table de jointure entre les responsables legaux et leur adresse
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseResponsableEleveAdresse extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'ResponsableEleveAdressePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ResponsableEleveAdressePeer
	 */
	protected static $peer;

	/**
	 * The value for the adr_id field.
	 * @var        string
	 */
	protected $adr_id;

	/**
	 * The value for the adr1 field.
	 * @var        string
	 */
	protected $adr1;

	/**
	 * The value for the adr2 field.
	 * @var        string
	 */
	protected $adr2;

	/**
	 * The value for the adr3 field.
	 * @var        string
	 */
	protected $adr3;

	/**
	 * The value for the adr4 field.
	 * @var        string
	 */
	protected $adr4;

	/**
	 * The value for the cp field.
	 * @var        string
	 */
	protected $cp;

	/**
	 * The value for the pays field.
	 * @var        string
	 */
	protected $pays;

	/**
	 * The value for the commune field.
	 * @var        string
	 */
	protected $commune;

	/**
	 * @var        array ResponsableEleve[] Collection to store aggregation of ResponsableEleve objects.
	 */
	protected $collResponsableEleves;

	/**
	 * @var        array AbsenceEleveNotification[] Collection to store aggregation of AbsenceEleveNotification objects.
	 */
	protected $collAbsenceEleveNotifications;

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
	 * Get the [adr_id] column value.
	 * cle primaire, genere par sconet
	 * @return     string
	 */
	public function getAdrId()
	{
		return $this->adr_id;
	}

	/**
	 * Get the [adr1] column value.
	 * 1ere ligne adresse
	 * @return     string
	 */
	public function getAdr1()
	{
		return $this->adr1;
	}

	/**
	 * Get the [adr2] column value.
	 * 2eme ligne adresse
	 * @return     string
	 */
	public function getAdr2()
	{
		return $this->adr2;
	}

	/**
	 * Get the [adr3] column value.
	 * 3eme ligne adresse
	 * @return     string
	 */
	public function getAdr3()
	{
		return $this->adr3;
	}

	/**
	 * Get the [adr4] column value.
	 * 4eme ligne adresse
	 * @return     string
	 */
	public function getAdr4()
	{
		return $this->adr4;
	}

	/**
	 * Get the [cp] column value.
	 * Code postal
	 * @return     string
	 */
	public function getCp()
	{
		return $this->cp;
	}

	/**
	 * Get the [pays] column value.
	 * Pays (quand il est autre que France)
	 * @return     string
	 */
	public function getPays()
	{
		return $this->pays;
	}

	/**
	 * Get the [commune] column value.
	 * Commune de residence
	 * @return     string
	 */
	public function getCommune()
	{
		return $this->commune;
	}

	/**
	 * Set the value of [adr_id] column.
	 * cle primaire, genere par sconet
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdrId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr_id !== $v) {
			$this->adr_id = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR_ID;
		}

		return $this;
	} // setAdrId()

	/**
	 * Set the value of [adr1] column.
	 * 1ere ligne adresse
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdr1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr1 !== $v) {
			$this->adr1 = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR1;
		}

		return $this;
	} // setAdr1()

	/**
	 * Set the value of [adr2] column.
	 * 2eme ligne adresse
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdr2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr2 !== $v) {
			$this->adr2 = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR2;
		}

		return $this;
	} // setAdr2()

	/**
	 * Set the value of [adr3] column.
	 * 3eme ligne adresse
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdr3($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr3 !== $v) {
			$this->adr3 = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR3;
		}

		return $this;
	} // setAdr3()

	/**
	 * Set the value of [adr4] column.
	 * 4eme ligne adresse
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdr4($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr4 !== $v) {
			$this->adr4 = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR4;
		}

		return $this;
	} // setAdr4()

	/**
	 * Set the value of [cp] column.
	 * Code postal
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setCp($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->cp !== $v) {
			$this->cp = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::CP;
		}

		return $this;
	} // setCp()

	/**
	 * Set the value of [pays] column.
	 * Pays (quand il est autre que France)
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setPays($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->pays !== $v) {
			$this->pays = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::PAYS;
		}

		return $this;
	} // setPays()

	/**
	 * Set the value of [commune] column.
	 * Commune de residence
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setCommune($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commune !== $v) {
			$this->commune = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::COMMUNE;
		}

		return $this;
	} // setCommune()

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

			$this->adr_id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->adr1 = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->adr2 = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->adr3 = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->adr4 = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->cp = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->pays = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->commune = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 8; // 8 = ResponsableEleveAdressePeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating ResponsableEleveAdresse object", $e);
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
			$con = Propel::getConnection(ResponsableEleveAdressePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = ResponsableEleveAdressePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collResponsableEleves = null;

			$this->collAbsenceEleveNotifications = null;

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
			$con = Propel::getConnection(ResponsableEleveAdressePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				ResponsableEleveAdresseQuery::create()
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
			$con = Propel::getConnection(ResponsableEleveAdressePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				ResponsableEleveAdressePeer::addInstanceToPool($this);
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


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows = 1;
					$this->setNew(false);
				} else {
					$affectedRows = ResponsableEleveAdressePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collResponsableEleves !== null) {
				foreach ($this->collResponsableEleves as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collAbsenceEleveNotifications !== null) {
				foreach ($this->collAbsenceEleveNotifications as $referrerFK) {
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


			if (($retval = ResponsableEleveAdressePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collResponsableEleves !== null) {
					foreach ($this->collResponsableEleves as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAbsenceEleveNotifications !== null) {
					foreach ($this->collAbsenceEleveNotifications as $referrerFK) {
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
		$pos = ResponsableEleveAdressePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAdrId();
				break;
			case 1:
				return $this->getAdr1();
				break;
			case 2:
				return $this->getAdr2();
				break;
			case 3:
				return $this->getAdr3();
				break;
			case 4:
				return $this->getAdr4();
				break;
			case 5:
				return $this->getCp();
				break;
			case 6:
				return $this->getPays();
				break;
			case 7:
				return $this->getCommune();
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
		if (isset($alreadyDumpedObjects['ResponsableEleveAdresse'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['ResponsableEleveAdresse'][$this->getPrimaryKey()] = true;
		$keys = ResponsableEleveAdressePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getAdrId(),
			$keys[1] => $this->getAdr1(),
			$keys[2] => $this->getAdr2(),
			$keys[3] => $this->getAdr3(),
			$keys[4] => $this->getAdr4(),
			$keys[5] => $this->getCp(),
			$keys[6] => $this->getPays(),
			$keys[7] => $this->getCommune(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->collResponsableEleves) {
				$result['ResponsableEleves'] = $this->collResponsableEleves->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceEleveNotifications) {
				$result['AbsenceEleveNotifications'] = $this->collAbsenceEleveNotifications->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = ResponsableEleveAdressePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAdrId($value);
				break;
			case 1:
				$this->setAdr1($value);
				break;
			case 2:
				$this->setAdr2($value);
				break;
			case 3:
				$this->setAdr3($value);
				break;
			case 4:
				$this->setAdr4($value);
				break;
			case 5:
				$this->setCp($value);
				break;
			case 6:
				$this->setPays($value);
				break;
			case 7:
				$this->setCommune($value);
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
		$keys = ResponsableEleveAdressePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setAdrId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAdr1($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAdr2($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setAdr3($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setAdr4($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCp($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setPays($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCommune($arr[$keys[7]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ResponsableEleveAdressePeer::DATABASE_NAME);

		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR_ID)) $criteria->add(ResponsableEleveAdressePeer::ADR_ID, $this->adr_id);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR1)) $criteria->add(ResponsableEleveAdressePeer::ADR1, $this->adr1);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR2)) $criteria->add(ResponsableEleveAdressePeer::ADR2, $this->adr2);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR3)) $criteria->add(ResponsableEleveAdressePeer::ADR3, $this->adr3);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR4)) $criteria->add(ResponsableEleveAdressePeer::ADR4, $this->adr4);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::CP)) $criteria->add(ResponsableEleveAdressePeer::CP, $this->cp);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::PAYS)) $criteria->add(ResponsableEleveAdressePeer::PAYS, $this->pays);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::COMMUNE)) $criteria->add(ResponsableEleveAdressePeer::COMMUNE, $this->commune);

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
		$criteria = new Criteria(ResponsableEleveAdressePeer::DATABASE_NAME);
		$criteria->add(ResponsableEleveAdressePeer::ADR_ID, $this->adr_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getAdrId();
	}

	/**
	 * Generic method to set the primary key (adr_id column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setAdrId($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getAdrId();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of ResponsableEleveAdresse (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setAdrId($this->getAdrId());
		$copyObj->setAdr1($this->getAdr1());
		$copyObj->setAdr2($this->getAdr2());
		$copyObj->setAdr3($this->getAdr3());
		$copyObj->setAdr4($this->getAdr4());
		$copyObj->setCp($this->getCp());
		$copyObj->setPays($this->getPays());
		$copyObj->setCommune($this->getCommune());

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getResponsableEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addResponsableEleve($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveNotifications() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveNotification($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
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
	 * @return     ResponsableEleveAdresse Clone of current object.
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
	 * @return     ResponsableEleveAdressePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ResponsableEleveAdressePeer();
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
		if ('ResponsableEleve' == $relationName) {
			return $this->initResponsableEleves();
		}
		if ('AbsenceEleveNotification' == $relationName) {
			return $this->initAbsenceEleveNotifications();
		}
	}

	/**
	 * Clears out the collResponsableEleves collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addResponsableEleves()
	 */
	public function clearResponsableEleves()
	{
		$this->collResponsableEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collResponsableEleves collection.
	 *
	 * By default this just sets the collResponsableEleves collection to an empty array (like clearcollResponsableEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initResponsableEleves($overrideExisting = true)
	{
		if (null !== $this->collResponsableEleves && !$overrideExisting) {
			return;
		}
		$this->collResponsableEleves = new PropelObjectCollection();
		$this->collResponsableEleves->setModel('ResponsableEleve');
	}

	/**
	 * Gets an array of ResponsableEleve objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this ResponsableEleveAdresse is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array ResponsableEleve[] List of ResponsableEleve objects
	 * @throws     PropelException
	 */
	public function getResponsableEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableEleves) {
				// return empty collection
				$this->initResponsableEleves();
			} else {
				$collResponsableEleves = ResponsableEleveQuery::create(null, $criteria)
					->filterByResponsableEleveAdresse($this)
					->find($con);
				if (null !== $criteria) {
					return $collResponsableEleves;
				}
				$this->collResponsableEleves = $collResponsableEleves;
			}
		}
		return $this->collResponsableEleves;
	}

	/**
	 * Returns the number of related ResponsableEleve objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related ResponsableEleve objects.
	 * @throws     PropelException
	 */
	public function countResponsableEleves(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableEleves) {
				return 0;
			} else {
				$query = ResponsableEleveQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByResponsableEleveAdresse($this)
					->count($con);
			}
		} else {
			return count($this->collResponsableEleves);
		}
	}

	/**
	 * Method called to associate a ResponsableEleve object to this object
	 * through the ResponsableEleve foreign key attribute.
	 *
	 * @param      ResponsableEleve $l ResponsableEleve
	 * @return     void
	 * @throws     PropelException
	 */
	public function addResponsableEleve(ResponsableEleve $l)
	{
		if ($this->collResponsableEleves === null) {
			$this->initResponsableEleves();
		}
		if (!$this->collResponsableEleves->contains($l)) { // only add it if the **same** object is not already associated
			$this->collResponsableEleves[]= $l;
			$l->setResponsableEleveAdresse($this);
		}
	}

	/**
	 * Clears out the collAbsenceEleveNotifications collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveNotifications()
	 */
	public function clearAbsenceEleveNotifications()
	{
		$this->collAbsenceEleveNotifications = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveNotifications collection.
	 *
	 * By default this just sets the collAbsenceEleveNotifications collection to an empty array (like clearcollAbsenceEleveNotifications());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAbsenceEleveNotifications($overrideExisting = true)
	{
		if (null !== $this->collAbsenceEleveNotifications && !$overrideExisting) {
			return;
		}
		$this->collAbsenceEleveNotifications = new PropelObjectCollection();
		$this->collAbsenceEleveNotifications->setModel('AbsenceEleveNotification');
	}

	/**
	 * Gets an array of AbsenceEleveNotification objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this ResponsableEleveAdresse is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 * @throws     PropelException
	 */
	public function getAbsenceEleveNotifications($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveNotifications || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveNotifications) {
				// return empty collection
				$this->initAbsenceEleveNotifications();
			} else {
				$collAbsenceEleveNotifications = AbsenceEleveNotificationQuery::create(null, $criteria)
					->filterByResponsableEleveAdresse($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceEleveNotifications;
				}
				$this->collAbsenceEleveNotifications = $collAbsenceEleveNotifications;
			}
		}
		return $this->collAbsenceEleveNotifications;
	}

	/**
	 * Returns the number of related AbsenceEleveNotification objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveNotification objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveNotifications(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveNotifications || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveNotifications) {
				return 0;
			} else {
				$query = AbsenceEleveNotificationQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByResponsableEleveAdresse($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveNotifications);
		}
	}

	/**
	 * Method called to associate a AbsenceEleveNotification object to this object
	 * through the AbsenceEleveNotification foreign key attribute.
	 *
	 * @param      AbsenceEleveNotification $l AbsenceEleveNotification
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAbsenceEleveNotification(AbsenceEleveNotification $l)
	{
		if ($this->collAbsenceEleveNotifications === null) {
			$this->initAbsenceEleveNotifications();
		}
		if (!$this->collAbsenceEleveNotifications->contains($l)) { // only add it if the **same** object is not already associated
			$this->collAbsenceEleveNotifications[]= $l;
			$l->setResponsableEleveAdresse($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ResponsableEleveAdresse is new, it will return
	 * an empty collection; or if this ResponsableEleveAdresse has previously
	 * been saved, it will retrieve related AbsenceEleveNotifications from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in ResponsableEleveAdresse.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 */
	public function getAbsenceEleveNotificationsJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveNotificationQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getAbsenceEleveNotifications($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ResponsableEleveAdresse is new, it will return
	 * an empty collection; or if this ResponsableEleveAdresse has previously
	 * been saved, it will retrieve related AbsenceEleveNotifications from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in ResponsableEleveAdresse.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 */
	public function getAbsenceEleveNotificationsJoinAbsenceEleveTraitement($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveNotificationQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveTraitement', $join_behavior);

		return $this->getAbsenceEleveNotifications($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->adr_id = null;
		$this->adr1 = null;
		$this->adr2 = null;
		$this->adr3 = null;
		$this->adr4 = null;
		$this->cp = null;
		$this->pays = null;
		$this->commune = null;
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
			if ($this->collResponsableEleves) {
				foreach ($this->collResponsableEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveNotifications) {
				foreach ($this->collAbsenceEleveNotifications as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collResponsableEleves instanceof PropelCollection) {
			$this->collResponsableEleves->clearIterator();
		}
		$this->collResponsableEleves = null;
		if ($this->collAbsenceEleveNotifications instanceof PropelCollection) {
			$this->collAbsenceEleveNotifications->clearIterator();
		}
		$this->collAbsenceEleveNotifications = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(ResponsableEleveAdressePeer::DEFAULT_STRING_FORMAT);
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

} // BaseResponsableEleveAdresse
