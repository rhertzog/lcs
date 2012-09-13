<?php


/**
 * Base class that represents a row from the 'ct_private_entry' table.
 *
 * Notice privee du cahier de texte
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteNoticePrivee extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'CahierTexteNoticePriveePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CahierTexteNoticePriveePeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

	/**
	 * The value for the id_ct field.
	 * @var        int
	 */
	protected $id_ct;

	/**
	 * The value for the heure_entry field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $heure_entry;

	/**
	 * The value for the date_ct field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $date_ct;

	/**
	 * The value for the contenu field.
	 * @var        string
	 */
	protected $contenu;

	/**
	 * The value for the id_groupe field.
	 * @var        int
	 */
	protected $id_groupe;

	/**
	 * The value for the id_login field.
	 * @var        string
	 */
	protected $id_login;

	/**
	 * The value for the id_sequence field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $id_sequence;

	/**
	 * @var        Groupe
	 */
	protected $aGroupe;

	/**
	 * @var        UtilisateurProfessionnel
	 */
	protected $aUtilisateurProfessionnel;

	/**
	 * @var        CahierTexteSequence
	 */
	protected $aCahierTexteSequence;

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
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->heure_entry = '00:00:00';
		$this->date_ct = 0;
		$this->id_sequence = 0;
	}

	/**
	 * Initializes internal state of BaseCahierTexteNoticePrivee object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id_ct] column value.
	 * Cle primaire de la cotice privee
	 * @return     int
	 */
	public function getIdCt()
	{
		return $this->id_ct;
	}

	/**
	 * Get the [optionally formatted] temporal [heure_entry] column value.
	 * heure de l'entree
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getHeureEntry($format = '%X')
	{
		if ($this->heure_entry === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->heure_entry);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->heure_entry, true), $x);
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
	 * Get the [date_ct] column value.
	 * date du compte rendu
	 * @return     int
	 */
	public function getDateCt()
	{
		return $this->date_ct;
	}

	/**
	 * Get the [contenu] column value.
	 * contenu redactionnel du compte rendu
	 * @return     string
	 */
	public function getContenu()
	{
		return $this->contenu;
	}

	/**
	 * Get the [id_groupe] column value.
	 * Cle etrangere du groupe auquel appartient le compte rendu
	 * @return     int
	 */
	public function getIdGroupe()
	{
		return $this->id_groupe;
	}

	/**
	 * Get the [id_login] column value.
	 * Cle etrangere de l'utilisateur auquel appartient le compte rendu
	 * @return     string
	 */
	public function getIdLogin()
	{
		return $this->id_login;
	}

	/**
	 * Get the [id_sequence] column value.
	 * Cle etrangere de la sequence auquel appartient la notice privee
	 * @return     int
	 */
	public function getIdSequence()
	{
		return $this->id_sequence;
	}

	/**
	 * Set the value of [id_ct] column.
	 * Cle primaire de la cotice privee
	 * @param      int $v new value
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 */
	public function setIdCt($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_ct !== $v) {
			$this->id_ct = $v;
			$this->modifiedColumns[] = CahierTexteNoticePriveePeer::ID_CT;
		}

		return $this;
	} // setIdCt()

	/**
	 * Sets the value of [heure_entry] column to a normalized version of the date/time value specified.
	 * heure de l'entree
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 */
	public function setHeureEntry($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->heure_entry !== null || $dt !== null) {
			$currentDateAsString = ($this->heure_entry !== null && $tmpDt = new DateTime($this->heure_entry)) ? $tmpDt->format('H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('H:i:s') : null;
			if ( ($currentDateAsString !== $newDateAsString) // normalized values don't match
				|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
				 ) {
				$this->heure_entry = $newDateAsString;
				$this->modifiedColumns[] = CahierTexteNoticePriveePeer::HEURE_ENTRY;
			}
		} // if either are not null

		return $this;
	} // setHeureEntry()

	/**
	 * Set the value of [date_ct] column.
	 * date du compte rendu
	 * @param      int $v new value
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 */
	public function setDateCt($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->date_ct !== $v) {
			$this->date_ct = $v;
			$this->modifiedColumns[] = CahierTexteNoticePriveePeer::DATE_CT;
		}

		return $this;
	} // setDateCt()

	/**
	 * Set the value of [contenu] column.
	 * contenu redactionnel du compte rendu
	 * @param      string $v new value
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 */
	public function setContenu($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->contenu !== $v) {
			$this->contenu = $v;
			$this->modifiedColumns[] = CahierTexteNoticePriveePeer::CONTENU;
		}

		return $this;
	} // setContenu()

	/**
	 * Set the value of [id_groupe] column.
	 * Cle etrangere du groupe auquel appartient le compte rendu
	 * @param      int $v new value
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 */
	public function setIdGroupe($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_groupe !== $v) {
			$this->id_groupe = $v;
			$this->modifiedColumns[] = CahierTexteNoticePriveePeer::ID_GROUPE;
		}

		if ($this->aGroupe !== null && $this->aGroupe->getId() !== $v) {
			$this->aGroupe = null;
		}

		return $this;
	} // setIdGroupe()

	/**
	 * Set the value of [id_login] column.
	 * Cle etrangere de l'utilisateur auquel appartient le compte rendu
	 * @param      string $v new value
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 */
	public function setIdLogin($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id_login !== $v) {
			$this->id_login = $v;
			$this->modifiedColumns[] = CahierTexteNoticePriveePeer::ID_LOGIN;
		}

		if ($this->aUtilisateurProfessionnel !== null && $this->aUtilisateurProfessionnel->getLogin() !== $v) {
			$this->aUtilisateurProfessionnel = null;
		}

		return $this;
	} // setIdLogin()

	/**
	 * Set the value of [id_sequence] column.
	 * Cle etrangere de la sequence auquel appartient la notice privee
	 * @param      int $v new value
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 */
	public function setIdSequence($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_sequence !== $v) {
			$this->id_sequence = $v;
			$this->modifiedColumns[] = CahierTexteNoticePriveePeer::ID_SEQUENCE;
		}

		if ($this->aCahierTexteSequence !== null && $this->aCahierTexteSequence->getId() !== $v) {
			$this->aCahierTexteSequence = null;
		}

		return $this;
	} // setIdSequence()

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
			if ($this->heure_entry !== '00:00:00') {
				return false;
			}

			if ($this->date_ct !== 0) {
				return false;
			}

			if ($this->id_sequence !== 0) {
				return false;
			}

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

			$this->id_ct = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->heure_entry = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->date_ct = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->contenu = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->id_groupe = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->id_login = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->id_sequence = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 7; // 7 = CahierTexteNoticePriveePeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating CahierTexteNoticePrivee object", $e);
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

		if ($this->aGroupe !== null && $this->id_groupe !== $this->aGroupe->getId()) {
			$this->aGroupe = null;
		}
		if ($this->aUtilisateurProfessionnel !== null && $this->id_login !== $this->aUtilisateurProfessionnel->getLogin()) {
			$this->aUtilisateurProfessionnel = null;
		}
		if ($this->aCahierTexteSequence !== null && $this->id_sequence !== $this->aCahierTexteSequence->getId()) {
			$this->aCahierTexteSequence = null;
		}
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
			$con = Propel::getConnection(CahierTexteNoticePriveePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CahierTexteNoticePriveePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aGroupe = null;
			$this->aUtilisateurProfessionnel = null;
			$this->aCahierTexteSequence = null;
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
			$con = Propel::getConnection(CahierTexteNoticePriveePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = CahierTexteNoticePriveeQuery::create()
				->filterByPrimaryKey($this->getPrimaryKey());
			$ret = $this->preDelete($con);
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
			$con = Propel::getConnection(CahierTexteNoticePriveePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CahierTexteNoticePriveePeer::addInstanceToPool($this);
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

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aGroupe !== null) {
				if ($this->aGroupe->isModified() || $this->aGroupe->isNew()) {
					$affectedRows += $this->aGroupe->save($con);
				}
				$this->setGroupe($this->aGroupe);
			}

			if ($this->aUtilisateurProfessionnel !== null) {
				if ($this->aUtilisateurProfessionnel->isModified() || $this->aUtilisateurProfessionnel->isNew()) {
					$affectedRows += $this->aUtilisateurProfessionnel->save($con);
				}
				$this->setUtilisateurProfessionnel($this->aUtilisateurProfessionnel);
			}

			if ($this->aCahierTexteSequence !== null) {
				if ($this->aCahierTexteSequence->isModified() || $this->aCahierTexteSequence->isNew()) {
					$affectedRows += $this->aCahierTexteSequence->save($con);
				}
				$this->setCahierTexteSequence($this->aCahierTexteSequence);
			}

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

		$this->modifiedColumns[] = CahierTexteNoticePriveePeer::ID_CT;
		if (null !== $this->id_ct) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . CahierTexteNoticePriveePeer::ID_CT . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::ID_CT)) {
			$modifiedColumns[':p' . $index++]  = 'ID_CT';
		}
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::HEURE_ENTRY)) {
			$modifiedColumns[':p' . $index++]  = 'HEURE_ENTRY';
		}
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::DATE_CT)) {
			$modifiedColumns[':p' . $index++]  = 'DATE_CT';
		}
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::CONTENU)) {
			$modifiedColumns[':p' . $index++]  = 'CONTENU';
		}
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::ID_GROUPE)) {
			$modifiedColumns[':p' . $index++]  = 'ID_GROUPE';
		}
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::ID_LOGIN)) {
			$modifiedColumns[':p' . $index++]  = 'ID_LOGIN';
		}
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::ID_SEQUENCE)) {
			$modifiedColumns[':p' . $index++]  = 'ID_SEQUENCE';
		}

		$sql = sprintf(
			'INSERT INTO ct_private_entry (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'ID_CT':
						$stmt->bindValue($identifier, $this->id_ct, PDO::PARAM_INT);
						break;
					case 'HEURE_ENTRY':
						$stmt->bindValue($identifier, $this->heure_entry, PDO::PARAM_STR);
						break;
					case 'DATE_CT':
						$stmt->bindValue($identifier, $this->date_ct, PDO::PARAM_INT);
						break;
					case 'CONTENU':
						$stmt->bindValue($identifier, $this->contenu, PDO::PARAM_STR);
						break;
					case 'ID_GROUPE':
						$stmt->bindValue($identifier, $this->id_groupe, PDO::PARAM_INT);
						break;
					case 'ID_LOGIN':
						$stmt->bindValue($identifier, $this->id_login, PDO::PARAM_STR);
						break;
					case 'ID_SEQUENCE':
						$stmt->bindValue($identifier, $this->id_sequence, PDO::PARAM_INT);
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
		$this->setIdCt($pk);

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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aGroupe !== null) {
				if (!$this->aGroupe->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aGroupe->getValidationFailures());
				}
			}

			if ($this->aUtilisateurProfessionnel !== null) {
				if (!$this->aUtilisateurProfessionnel->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aUtilisateurProfessionnel->getValidationFailures());
				}
			}

			if ($this->aCahierTexteSequence !== null) {
				if (!$this->aCahierTexteSequence->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCahierTexteSequence->getValidationFailures());
				}
			}


			if (($retval = CahierTexteNoticePriveePeer::doValidate($this, $columns)) !== true) {
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
		$pos = CahierTexteNoticePriveePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIdCt();
				break;
			case 1:
				return $this->getHeureEntry();
				break;
			case 2:
				return $this->getDateCt();
				break;
			case 3:
				return $this->getContenu();
				break;
			case 4:
				return $this->getIdGroupe();
				break;
			case 5:
				return $this->getIdLogin();
				break;
			case 6:
				return $this->getIdSequence();
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
		if (isset($alreadyDumpedObjects['CahierTexteNoticePrivee'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['CahierTexteNoticePrivee'][$this->getPrimaryKey()] = true;
		$keys = CahierTexteNoticePriveePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getIdCt(),
			$keys[1] => $this->getHeureEntry(),
			$keys[2] => $this->getDateCt(),
			$keys[3] => $this->getContenu(),
			$keys[4] => $this->getIdGroupe(),
			$keys[5] => $this->getIdLogin(),
			$keys[6] => $this->getIdSequence(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aGroupe) {
				$result['Groupe'] = $this->aGroupe->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aUtilisateurProfessionnel) {
				$result['UtilisateurProfessionnel'] = $this->aUtilisateurProfessionnel->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aCahierTexteSequence) {
				$result['CahierTexteSequence'] = $this->aCahierTexteSequence->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
		$pos = CahierTexteNoticePriveePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setIdCt($value);
				break;
			case 1:
				$this->setHeureEntry($value);
				break;
			case 2:
				$this->setDateCt($value);
				break;
			case 3:
				$this->setContenu($value);
				break;
			case 4:
				$this->setIdGroupe($value);
				break;
			case 5:
				$this->setIdLogin($value);
				break;
			case 6:
				$this->setIdSequence($value);
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
		$keys = CahierTexteNoticePriveePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setIdCt($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setHeureEntry($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDateCt($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setContenu($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setIdGroupe($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setIdLogin($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setIdSequence($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CahierTexteNoticePriveePeer::DATABASE_NAME);

		if ($this->isColumnModified(CahierTexteNoticePriveePeer::ID_CT)) $criteria->add(CahierTexteNoticePriveePeer::ID_CT, $this->id_ct);
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::HEURE_ENTRY)) $criteria->add(CahierTexteNoticePriveePeer::HEURE_ENTRY, $this->heure_entry);
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::DATE_CT)) $criteria->add(CahierTexteNoticePriveePeer::DATE_CT, $this->date_ct);
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::CONTENU)) $criteria->add(CahierTexteNoticePriveePeer::CONTENU, $this->contenu);
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::ID_GROUPE)) $criteria->add(CahierTexteNoticePriveePeer::ID_GROUPE, $this->id_groupe);
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::ID_LOGIN)) $criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $this->id_login);
		if ($this->isColumnModified(CahierTexteNoticePriveePeer::ID_SEQUENCE)) $criteria->add(CahierTexteNoticePriveePeer::ID_SEQUENCE, $this->id_sequence);

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
		$criteria = new Criteria(CahierTexteNoticePriveePeer::DATABASE_NAME);
		$criteria->add(CahierTexteNoticePriveePeer::ID_CT, $this->id_ct);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getIdCt();
	}

	/**
	 * Generic method to set the primary key (id_ct column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setIdCt($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getIdCt();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of CahierTexteNoticePrivee (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setHeureEntry($this->getHeureEntry());
		$copyObj->setDateCt($this->getDateCt());
		$copyObj->setContenu($this->getContenu());
		$copyObj->setIdGroupe($this->getIdGroupe());
		$copyObj->setIdLogin($this->getIdLogin());
		$copyObj->setIdSequence($this->getIdSequence());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
			$copyObj->setIdCt(NULL); // this is a auto-increment column, so set to default value
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
	 * @return     CahierTexteNoticePrivee Clone of current object.
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
	 * @return     CahierTexteNoticePriveePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CahierTexteNoticePriveePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Groupe object.
	 *
	 * @param      Groupe $v
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setGroupe(Groupe $v = null)
	{
		if ($v === null) {
			$this->setIdGroupe(NULL);
		} else {
			$this->setIdGroupe($v->getId());
		}

		$this->aGroupe = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Groupe object, it will not be re-added.
		if ($v !== null) {
			$v->addCahierTexteNoticePrivee($this);
		}

		return $this;
	}


	/**
	 * Get the associated Groupe object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Groupe The associated Groupe object.
	 * @throws     PropelException
	 */
	public function getGroupe(PropelPDO $con = null)
	{
		if ($this->aGroupe === null && ($this->id_groupe !== null)) {
			$this->aGroupe = GroupeQuery::create()->findPk($this->id_groupe, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aGroupe->addCahierTexteNoticePrivees($this);
			 */
		}
		return $this->aGroupe;
	}

	/**
	 * Declares an association between this object and a UtilisateurProfessionnel object.
	 *
	 * @param      UtilisateurProfessionnel $v
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setUtilisateurProfessionnel(UtilisateurProfessionnel $v = null)
	{
		if ($v === null) {
			$this->setIdLogin(NULL);
		} else {
			$this->setIdLogin($v->getLogin());
		}

		$this->aUtilisateurProfessionnel = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the UtilisateurProfessionnel object, it will not be re-added.
		if ($v !== null) {
			$v->addCahierTexteNoticePrivee($this);
		}

		return $this;
	}


	/**
	 * Get the associated UtilisateurProfessionnel object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     UtilisateurProfessionnel The associated UtilisateurProfessionnel object.
	 * @throws     PropelException
	 */
	public function getUtilisateurProfessionnel(PropelPDO $con = null)
	{
		if ($this->aUtilisateurProfessionnel === null && (($this->id_login !== "" && $this->id_login !== null))) {
			$this->aUtilisateurProfessionnel = UtilisateurProfessionnelQuery::create()->findPk($this->id_login, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aUtilisateurProfessionnel->addCahierTexteNoticePrivees($this);
			 */
		}
		return $this->aUtilisateurProfessionnel;
	}

	/**
	 * Declares an association between this object and a CahierTexteSequence object.
	 *
	 * @param      CahierTexteSequence $v
	 * @return     CahierTexteNoticePrivee The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCahierTexteSequence(CahierTexteSequence $v = null)
	{
		if ($v === null) {
			$this->setIdSequence(0);
		} else {
			$this->setIdSequence($v->getId());
		}

		$this->aCahierTexteSequence = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CahierTexteSequence object, it will not be re-added.
		if ($v !== null) {
			$v->addCahierTexteNoticePrivee($this);
		}

		return $this;
	}


	/**
	 * Get the associated CahierTexteSequence object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CahierTexteSequence The associated CahierTexteSequence object.
	 * @throws     PropelException
	 */
	public function getCahierTexteSequence(PropelPDO $con = null)
	{
		if ($this->aCahierTexteSequence === null && ($this->id_sequence !== null)) {
			$this->aCahierTexteSequence = CahierTexteSequenceQuery::create()->findPk($this->id_sequence, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aCahierTexteSequence->addCahierTexteNoticePrivees($this);
			 */
		}
		return $this->aCahierTexteSequence;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id_ct = null;
		$this->heure_entry = null;
		$this->date_ct = null;
		$this->contenu = null;
		$this->id_groupe = null;
		$this->id_login = null;
		$this->id_sequence = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
		$this->applyDefaultValues();
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
		} // if ($deep)

		$this->aGroupe = null;
		$this->aUtilisateurProfessionnel = null;
		$this->aCahierTexteSequence = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(CahierTexteNoticePriveePeer::DEFAULT_STRING_FORMAT);
	}

} // BaseCahierTexteNoticePrivee
