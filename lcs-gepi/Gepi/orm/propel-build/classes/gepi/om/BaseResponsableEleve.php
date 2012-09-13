<?php


/**
 * Base class that represents a row from the 'resp_pers' table.
 *
 * Liste des responsables legaux des eleves
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseResponsableEleve extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'ResponsableElevePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ResponsableElevePeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

	/**
	 * The value for the pers_id field.
	 * @var        string
	 */
	protected $pers_id;

	/**
	 * The value for the login field.
	 * @var        string
	 */
	protected $login;

	/**
	 * The value for the nom field.
	 * @var        string
	 */
	protected $nom;

	/**
	 * The value for the prenom field.
	 * @var        string
	 */
	protected $prenom;

	/**
	 * The value for the civilite field.
	 * @var        string
	 */
	protected $civilite;

	/**
	 * The value for the tel_pers field.
	 * @var        string
	 */
	protected $tel_pers;

	/**
	 * The value for the tel_port field.
	 * @var        string
	 */
	protected $tel_port;

	/**
	 * The value for the tel_prof field.
	 * @var        string
	 */
	protected $tel_prof;

	/**
	 * The value for the mel field.
	 * @var        string
	 */
	protected $mel;

	/**
	 * The value for the adr_id field.
	 * @var        string
	 */
	protected $adr_id;

	/**
	 * @var        Adresse
	 */
	protected $aAdresse;

	/**
	 * @var        array ResponsableInformation[] Collection to store aggregation of ResponsableInformation objects.
	 */
	protected $collResponsableInformations;

	/**
	 * @var        array JNotificationResponsableEleve[] Collection to store aggregation of JNotificationResponsableEleve objects.
	 */
	protected $collJNotificationResponsableEleves;

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
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceEleveNotificationsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $responsableInformationsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jNotificationResponsableElevesScheduledForDeletion = null;

	/**
	 * Get the [pers_id] column value.
	 * cle primaire genere par sconet
	 * @return     string
	 */
	public function getResponsableEleveId()
	{
		return $this->pers_id;
	}

	/**
	 * Get the [login] column value.
	 * cle primaire du responsable, login utilise comme utilisateur
	 * @return     string
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * Get the [nom] column value.
	 * Nom du responsable legal
	 * @return     string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Get the [prenom] column value.
	 * Prenom du responsable legal
	 * @return     string
	 */
	public function getPrenom()
	{
		return $this->prenom;
	}

	/**
	 * Get the [civilite] column value.
	 * civilite du responsable legal : M. Mlle Mme
	 * @return     string
	 */
	public function getCivilite()
	{
		return $this->civilite;
	}

	/**
	 * Get the [tel_pers] column value.
	 * Telephone personnel du responsable legal
	 * @return     string
	 */
	public function getTelPers()
	{
		return $this->tel_pers;
	}

	/**
	 * Get the [tel_port] column value.
	 * Telephone portable du responsable legal
	 * @return     string
	 */
	public function getTelPort()
	{
		return $this->tel_port;
	}

	/**
	 * Get the [tel_prof] column value.
	 * Telephone professionnel du responsable lega
	 * @return     string
	 */
	public function getTelProf()
	{
		return $this->tel_prof;
	}

	/**
	 * Get the [mel] column value.
	 * Courriel du responsable legal
	 * @return     string
	 */
	public function getMel()
	{
		return $this->mel;
	}

	/**
	 * Get the [adr_id] column value.
	 * cle etrangere vers l'adresse du responsable lega
	 * @return     string
	 */
	public function getAdresseId()
	{
		return $this->adr_id;
	}

	/**
	 * Set the value of [pers_id] column.
	 * cle primaire genere par sconet
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setResponsableEleveId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->pers_id !== $v) {
			$this->pers_id = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::PERS_ID;
		}

		return $this;
	} // setResponsableEleveId()

	/**
	 * Set the value of [login] column.
	 * cle primaire du responsable, login utilise comme utilisateur
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setLogin($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->login !== $v) {
			$this->login = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::LOGIN;
		}

		return $this;
	} // setLogin()

	/**
	 * Set the value of [nom] column.
	 * Nom du responsable legal
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v) {
			$this->nom = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [prenom] column.
	 * Prenom du responsable legal
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setPrenom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->prenom !== $v) {
			$this->prenom = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::PRENOM;
		}

		return $this;
	} // setPrenom()

	/**
	 * Set the value of [civilite] column.
	 * civilite du responsable legal : M. Mlle Mme
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setCivilite($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->civilite !== $v) {
			$this->civilite = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::CIVILITE;
		}

		return $this;
	} // setCivilite()

	/**
	 * Set the value of [tel_pers] column.
	 * Telephone personnel du responsable legal
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setTelPers($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tel_pers !== $v) {
			$this->tel_pers = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::TEL_PERS;
		}

		return $this;
	} // setTelPers()

	/**
	 * Set the value of [tel_port] column.
	 * Telephone portable du responsable legal
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setTelPort($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tel_port !== $v) {
			$this->tel_port = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::TEL_PORT;
		}

		return $this;
	} // setTelPort()

	/**
	 * Set the value of [tel_prof] column.
	 * Telephone professionnel du responsable lega
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setTelProf($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tel_prof !== $v) {
			$this->tel_prof = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::TEL_PROF;
		}

		return $this;
	} // setTelProf()

	/**
	 * Set the value of [mel] column.
	 * Courriel du responsable legal
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setMel($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->mel !== $v) {
			$this->mel = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::MEL;
		}

		return $this;
	} // setMel()

	/**
	 * Set the value of [adr_id] column.
	 * cle etrangere vers l'adresse du responsable lega
	 * @param      string $v new value
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function setAdresseId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr_id !== $v) {
			$this->adr_id = $v;
			$this->modifiedColumns[] = ResponsableElevePeer::ADR_ID;
		}

		if ($this->aAdresse !== null && $this->aAdresse->getId() !== $v) {
			$this->aAdresse = null;
		}

		return $this;
	} // setAdresseId()

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

			$this->pers_id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->login = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->nom = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->prenom = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->civilite = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->tel_pers = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->tel_port = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->tel_prof = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->mel = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->adr_id = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 10; // 10 = ResponsableElevePeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating ResponsableEleve object", $e);
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

		if ($this->aAdresse !== null && $this->adr_id !== $this->aAdresse->getId()) {
			$this->aAdresse = null;
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
			$con = Propel::getConnection(ResponsableElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = ResponsableElevePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aAdresse = null;
			$this->collResponsableInformations = null;

			$this->collJNotificationResponsableEleves = null;

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
			$con = Propel::getConnection(ResponsableElevePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = ResponsableEleveQuery::create()
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
			$con = Propel::getConnection(ResponsableElevePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				ResponsableElevePeer::addInstanceToPool($this);
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

			if ($this->aAdresse !== null) {
				if ($this->aAdresse->isModified() || $this->aAdresse->isNew()) {
					$affectedRows += $this->aAdresse->save($con);
				}
				$this->setAdresse($this->aAdresse);
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

			if ($this->absenceEleveNotificationsScheduledForDeletion !== null) {
				if (!$this->absenceEleveNotificationsScheduledForDeletion->isEmpty()) {
					JNotificationResponsableEleveQuery::create()
						->filterByPrimaryKeys($this->absenceEleveNotificationsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->absenceEleveNotificationsScheduledForDeletion = null;
				}

				foreach ($this->getAbsenceEleveNotifications() as $absenceEleveNotification) {
					if ($absenceEleveNotification->isModified()) {
						$absenceEleveNotification->save($con);
					}
				}
			}

			if ($this->responsableInformationsScheduledForDeletion !== null) {
				if (!$this->responsableInformationsScheduledForDeletion->isEmpty()) {
					ResponsableInformationQuery::create()
						->filterByPrimaryKeys($this->responsableInformationsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->responsableInformationsScheduledForDeletion = null;
				}
			}

			if ($this->collResponsableInformations !== null) {
				foreach ($this->collResponsableInformations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jNotificationResponsableElevesScheduledForDeletion !== null) {
				if (!$this->jNotificationResponsableElevesScheduledForDeletion->isEmpty()) {
					JNotificationResponsableEleveQuery::create()
						->filterByPrimaryKeys($this->jNotificationResponsableElevesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jNotificationResponsableElevesScheduledForDeletion = null;
				}
			}

			if ($this->collJNotificationResponsableEleves !== null) {
				foreach ($this->collJNotificationResponsableEleves as $referrerFK) {
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


		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(ResponsableElevePeer::PERS_ID)) {
			$modifiedColumns[':p' . $index++]  = 'PERS_ID';
		}
		if ($this->isColumnModified(ResponsableElevePeer::LOGIN)) {
			$modifiedColumns[':p' . $index++]  = 'LOGIN';
		}
		if ($this->isColumnModified(ResponsableElevePeer::NOM)) {
			$modifiedColumns[':p' . $index++]  = 'NOM';
		}
		if ($this->isColumnModified(ResponsableElevePeer::PRENOM)) {
			$modifiedColumns[':p' . $index++]  = 'PRENOM';
		}
		if ($this->isColumnModified(ResponsableElevePeer::CIVILITE)) {
			$modifiedColumns[':p' . $index++]  = 'CIVILITE';
		}
		if ($this->isColumnModified(ResponsableElevePeer::TEL_PERS)) {
			$modifiedColumns[':p' . $index++]  = 'TEL_PERS';
		}
		if ($this->isColumnModified(ResponsableElevePeer::TEL_PORT)) {
			$modifiedColumns[':p' . $index++]  = 'TEL_PORT';
		}
		if ($this->isColumnModified(ResponsableElevePeer::TEL_PROF)) {
			$modifiedColumns[':p' . $index++]  = 'TEL_PROF';
		}
		if ($this->isColumnModified(ResponsableElevePeer::MEL)) {
			$modifiedColumns[':p' . $index++]  = 'MEL';
		}
		if ($this->isColumnModified(ResponsableElevePeer::ADR_ID)) {
			$modifiedColumns[':p' . $index++]  = 'ADR_ID';
		}

		$sql = sprintf(
			'INSERT INTO resp_pers (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'PERS_ID':
						$stmt->bindValue($identifier, $this->pers_id, PDO::PARAM_STR);
						break;
					case 'LOGIN':
						$stmt->bindValue($identifier, $this->login, PDO::PARAM_STR);
						break;
					case 'NOM':
						$stmt->bindValue($identifier, $this->nom, PDO::PARAM_STR);
						break;
					case 'PRENOM':
						$stmt->bindValue($identifier, $this->prenom, PDO::PARAM_STR);
						break;
					case 'CIVILITE':
						$stmt->bindValue($identifier, $this->civilite, PDO::PARAM_STR);
						break;
					case 'TEL_PERS':
						$stmt->bindValue($identifier, $this->tel_pers, PDO::PARAM_STR);
						break;
					case 'TEL_PORT':
						$stmt->bindValue($identifier, $this->tel_port, PDO::PARAM_STR);
						break;
					case 'TEL_PROF':
						$stmt->bindValue($identifier, $this->tel_prof, PDO::PARAM_STR);
						break;
					case 'MEL':
						$stmt->bindValue($identifier, $this->mel, PDO::PARAM_STR);
						break;
					case 'ADR_ID':
						$stmt->bindValue($identifier, $this->adr_id, PDO::PARAM_STR);
						break;
				}
			}
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
		}

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

			if ($this->aAdresse !== null) {
				if (!$this->aAdresse->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAdresse->getValidationFailures());
				}
			}


			if (($retval = ResponsableElevePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collResponsableInformations !== null) {
					foreach ($this->collResponsableInformations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJNotificationResponsableEleves !== null) {
					foreach ($this->collJNotificationResponsableEleves as $referrerFK) {
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
		$pos = ResponsableElevePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getResponsableEleveId();
				break;
			case 1:
				return $this->getLogin();
				break;
			case 2:
				return $this->getNom();
				break;
			case 3:
				return $this->getPrenom();
				break;
			case 4:
				return $this->getCivilite();
				break;
			case 5:
				return $this->getTelPers();
				break;
			case 6:
				return $this->getTelPort();
				break;
			case 7:
				return $this->getTelProf();
				break;
			case 8:
				return $this->getMel();
				break;
			case 9:
				return $this->getAdresseId();
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
		if (isset($alreadyDumpedObjects['ResponsableEleve'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['ResponsableEleve'][$this->getPrimaryKey()] = true;
		$keys = ResponsableElevePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getResponsableEleveId(),
			$keys[1] => $this->getLogin(),
			$keys[2] => $this->getNom(),
			$keys[3] => $this->getPrenom(),
			$keys[4] => $this->getCivilite(),
			$keys[5] => $this->getTelPers(),
			$keys[6] => $this->getTelPort(),
			$keys[7] => $this->getTelProf(),
			$keys[8] => $this->getMel(),
			$keys[9] => $this->getAdresseId(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aAdresse) {
				$result['Adresse'] = $this->aAdresse->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->collResponsableInformations) {
				$result['ResponsableInformations'] = $this->collResponsableInformations->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJNotificationResponsableEleves) {
				$result['JNotificationResponsableEleves'] = $this->collJNotificationResponsableEleves->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = ResponsableElevePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setResponsableEleveId($value);
				break;
			case 1:
				$this->setLogin($value);
				break;
			case 2:
				$this->setNom($value);
				break;
			case 3:
				$this->setPrenom($value);
				break;
			case 4:
				$this->setCivilite($value);
				break;
			case 5:
				$this->setTelPers($value);
				break;
			case 6:
				$this->setTelPort($value);
				break;
			case 7:
				$this->setTelProf($value);
				break;
			case 8:
				$this->setMel($value);
				break;
			case 9:
				$this->setAdresseId($value);
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
		$keys = ResponsableElevePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setResponsableEleveId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setLogin($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNom($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPrenom($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCivilite($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setTelPers($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setTelPort($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setTelProf($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setMel($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setAdresseId($arr[$keys[9]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ResponsableElevePeer::DATABASE_NAME);

		if ($this->isColumnModified(ResponsableElevePeer::PERS_ID)) $criteria->add(ResponsableElevePeer::PERS_ID, $this->pers_id);
		if ($this->isColumnModified(ResponsableElevePeer::LOGIN)) $criteria->add(ResponsableElevePeer::LOGIN, $this->login);
		if ($this->isColumnModified(ResponsableElevePeer::NOM)) $criteria->add(ResponsableElevePeer::NOM, $this->nom);
		if ($this->isColumnModified(ResponsableElevePeer::PRENOM)) $criteria->add(ResponsableElevePeer::PRENOM, $this->prenom);
		if ($this->isColumnModified(ResponsableElevePeer::CIVILITE)) $criteria->add(ResponsableElevePeer::CIVILITE, $this->civilite);
		if ($this->isColumnModified(ResponsableElevePeer::TEL_PERS)) $criteria->add(ResponsableElevePeer::TEL_PERS, $this->tel_pers);
		if ($this->isColumnModified(ResponsableElevePeer::TEL_PORT)) $criteria->add(ResponsableElevePeer::TEL_PORT, $this->tel_port);
		if ($this->isColumnModified(ResponsableElevePeer::TEL_PROF)) $criteria->add(ResponsableElevePeer::TEL_PROF, $this->tel_prof);
		if ($this->isColumnModified(ResponsableElevePeer::MEL)) $criteria->add(ResponsableElevePeer::MEL, $this->mel);
		if ($this->isColumnModified(ResponsableElevePeer::ADR_ID)) $criteria->add(ResponsableElevePeer::ADR_ID, $this->adr_id);

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
		$criteria = new Criteria(ResponsableElevePeer::DATABASE_NAME);
		$criteria->add(ResponsableElevePeer::PERS_ID, $this->pers_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getResponsableEleveId();
	}

	/**
	 * Generic method to set the primary key (pers_id column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setResponsableEleveId($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getResponsableEleveId();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of ResponsableEleve (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setLogin($this->getLogin());
		$copyObj->setNom($this->getNom());
		$copyObj->setPrenom($this->getPrenom());
		$copyObj->setCivilite($this->getCivilite());
		$copyObj->setTelPers($this->getTelPers());
		$copyObj->setTelPort($this->getTelPort());
		$copyObj->setTelProf($this->getTelProf());
		$copyObj->setMel($this->getMel());
		$copyObj->setAdresseId($this->getAdresseId());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getResponsableInformations() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addResponsableInformation($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJNotificationResponsableEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJNotificationResponsableEleve($relObj->copy($deepCopy));
				}
			}

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
			$copyObj->setResponsableEleveId(NULL); // this is a auto-increment column, so set to default value
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
	 * @return     ResponsableEleve Clone of current object.
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
	 * @return     ResponsableElevePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ResponsableElevePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Adresse object.
	 *
	 * @param      Adresse $v
	 * @return     ResponsableEleve The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAdresse(Adresse $v = null)
	{
		if ($v === null) {
			$this->setAdresseId(NULL);
		} else {
			$this->setAdresseId($v->getId());
		}

		$this->aAdresse = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Adresse object, it will not be re-added.
		if ($v !== null) {
			$v->addResponsableEleve($this);
		}

		return $this;
	}


	/**
	 * Get the associated Adresse object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Adresse The associated Adresse object.
	 * @throws     PropelException
	 */
	public function getAdresse(PropelPDO $con = null)
	{
		if ($this->aAdresse === null && (($this->adr_id !== "" && $this->adr_id !== null))) {
			$this->aAdresse = AdresseQuery::create()->findPk($this->adr_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aAdresse->addResponsableEleves($this);
			 */
		}
		return $this->aAdresse;
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
		if ('ResponsableInformation' == $relationName) {
			return $this->initResponsableInformations();
		}
		if ('JNotificationResponsableEleve' == $relationName) {
			return $this->initJNotificationResponsableEleves();
		}
	}

	/**
	 * Clears out the collResponsableInformations collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addResponsableInformations()
	 */
	public function clearResponsableInformations()
	{
		$this->collResponsableInformations = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collResponsableInformations collection.
	 *
	 * By default this just sets the collResponsableInformations collection to an empty array (like clearcollResponsableInformations());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initResponsableInformations($overrideExisting = true)
	{
		if (null !== $this->collResponsableInformations && !$overrideExisting) {
			return;
		}
		$this->collResponsableInformations = new PropelObjectCollection();
		$this->collResponsableInformations->setModel('ResponsableInformation');
	}

	/**
	 * Gets an array of ResponsableInformation objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this ResponsableEleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array ResponsableInformation[] List of ResponsableInformation objects
	 * @throws     PropelException
	 */
	public function getResponsableInformations($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collResponsableInformations || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableInformations) {
				// return empty collection
				$this->initResponsableInformations();
			} else {
				$collResponsableInformations = ResponsableInformationQuery::create(null, $criteria)
					->filterByResponsableEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collResponsableInformations;
				}
				$this->collResponsableInformations = $collResponsableInformations;
			}
		}
		return $this->collResponsableInformations;
	}

	/**
	 * Sets a collection of ResponsableInformation objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $responsableInformations A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setResponsableInformations(PropelCollection $responsableInformations, PropelPDO $con = null)
	{
		$this->responsableInformationsScheduledForDeletion = $this->getResponsableInformations(new Criteria(), $con)->diff($responsableInformations);

		foreach ($responsableInformations as $responsableInformation) {
			// Fix issue with collection modified by reference
			if ($responsableInformation->isNew()) {
				$responsableInformation->setResponsableEleve($this);
			}
			$this->addResponsableInformation($responsableInformation);
		}

		$this->collResponsableInformations = $responsableInformations;
	}

	/**
	 * Returns the number of related ResponsableInformation objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related ResponsableInformation objects.
	 * @throws     PropelException
	 */
	public function countResponsableInformations(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collResponsableInformations || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableInformations) {
				return 0;
			} else {
				$query = ResponsableInformationQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByResponsableEleve($this)
					->count($con);
			}
		} else {
			return count($this->collResponsableInformations);
		}
	}

	/**
	 * Method called to associate a ResponsableInformation object to this object
	 * through the ResponsableInformation foreign key attribute.
	 *
	 * @param      ResponsableInformation $l ResponsableInformation
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function addResponsableInformation(ResponsableInformation $l)
	{
		if ($this->collResponsableInformations === null) {
			$this->initResponsableInformations();
		}
		if (!$this->collResponsableInformations->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddResponsableInformation($l);
		}

		return $this;
	}

	/**
	 * @param	ResponsableInformation $responsableInformation The responsableInformation object to add.
	 */
	protected function doAddResponsableInformation($responsableInformation)
	{
		$this->collResponsableInformations[]= $responsableInformation;
		$responsableInformation->setResponsableEleve($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ResponsableEleve is new, it will return
	 * an empty collection; or if this ResponsableEleve has previously
	 * been saved, it will retrieve related ResponsableInformations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in ResponsableEleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array ResponsableInformation[] List of ResponsableInformation objects
	 */
	public function getResponsableInformationsJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = ResponsableInformationQuery::create(null, $criteria);
		$query->joinWith('Eleve', $join_behavior);

		return $this->getResponsableInformations($query, $con);
	}

	/**
	 * Clears out the collJNotificationResponsableEleves collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJNotificationResponsableEleves()
	 */
	public function clearJNotificationResponsableEleves()
	{
		$this->collJNotificationResponsableEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJNotificationResponsableEleves collection.
	 *
	 * By default this just sets the collJNotificationResponsableEleves collection to an empty array (like clearcollJNotificationResponsableEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJNotificationResponsableEleves($overrideExisting = true)
	{
		if (null !== $this->collJNotificationResponsableEleves && !$overrideExisting) {
			return;
		}
		$this->collJNotificationResponsableEleves = new PropelObjectCollection();
		$this->collJNotificationResponsableEleves->setModel('JNotificationResponsableEleve');
	}

	/**
	 * Gets an array of JNotificationResponsableEleve objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this ResponsableEleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JNotificationResponsableEleve[] List of JNotificationResponsableEleve objects
	 * @throws     PropelException
	 */
	public function getJNotificationResponsableEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJNotificationResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collJNotificationResponsableEleves) {
				// return empty collection
				$this->initJNotificationResponsableEleves();
			} else {
				$collJNotificationResponsableEleves = JNotificationResponsableEleveQuery::create(null, $criteria)
					->filterByResponsableEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collJNotificationResponsableEleves;
				}
				$this->collJNotificationResponsableEleves = $collJNotificationResponsableEleves;
			}
		}
		return $this->collJNotificationResponsableEleves;
	}

	/**
	 * Sets a collection of JNotificationResponsableEleve objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jNotificationResponsableEleves A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJNotificationResponsableEleves(PropelCollection $jNotificationResponsableEleves, PropelPDO $con = null)
	{
		$this->jNotificationResponsableElevesScheduledForDeletion = $this->getJNotificationResponsableEleves(new Criteria(), $con)->diff($jNotificationResponsableEleves);

		foreach ($jNotificationResponsableEleves as $jNotificationResponsableEleve) {
			// Fix issue with collection modified by reference
			if ($jNotificationResponsableEleve->isNew()) {
				$jNotificationResponsableEleve->setResponsableEleve($this);
			}
			$this->addJNotificationResponsableEleve($jNotificationResponsableEleve);
		}

		$this->collJNotificationResponsableEleves = $jNotificationResponsableEleves;
	}

	/**
	 * Returns the number of related JNotificationResponsableEleve objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JNotificationResponsableEleve objects.
	 * @throws     PropelException
	 */
	public function countJNotificationResponsableEleves(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJNotificationResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collJNotificationResponsableEleves) {
				return 0;
			} else {
				$query = JNotificationResponsableEleveQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByResponsableEleve($this)
					->count($con);
			}
		} else {
			return count($this->collJNotificationResponsableEleves);
		}
	}

	/**
	 * Method called to associate a JNotificationResponsableEleve object to this object
	 * through the JNotificationResponsableEleve foreign key attribute.
	 *
	 * @param      JNotificationResponsableEleve $l JNotificationResponsableEleve
	 * @return     ResponsableEleve The current object (for fluent API support)
	 */
	public function addJNotificationResponsableEleve(JNotificationResponsableEleve $l)
	{
		if ($this->collJNotificationResponsableEleves === null) {
			$this->initJNotificationResponsableEleves();
		}
		if (!$this->collJNotificationResponsableEleves->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJNotificationResponsableEleve($l);
		}

		return $this;
	}

	/**
	 * @param	JNotificationResponsableEleve $jNotificationResponsableEleve The jNotificationResponsableEleve object to add.
	 */
	protected function doAddJNotificationResponsableEleve($jNotificationResponsableEleve)
	{
		$this->collJNotificationResponsableEleves[]= $jNotificationResponsableEleve;
		$jNotificationResponsableEleve->setResponsableEleve($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this ResponsableEleve is new, it will return
	 * an empty collection; or if this ResponsableEleve has previously
	 * been saved, it will retrieve related JNotificationResponsableEleves from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in ResponsableEleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JNotificationResponsableEleve[] List of JNotificationResponsableEleve objects
	 */
	public function getJNotificationResponsableElevesJoinAbsenceEleveNotification($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JNotificationResponsableEleveQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveNotification', $join_behavior);

		return $this->getJNotificationResponsableEleves($query, $con);
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
	 * By default this just sets the collAbsenceEleveNotifications collection to an empty collection (like clearAbsenceEleveNotifications());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveNotifications()
	{
		$this->collAbsenceEleveNotifications = new PropelObjectCollection();
		$this->collAbsenceEleveNotifications->setModel('AbsenceEleveNotification');
	}

	/**
	 * Gets a collection of AbsenceEleveNotification objects related by a many-to-many relationship
	 * to the current object by way of the j_notifications_resp_pers cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this ResponsableEleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 */
	public function getAbsenceEleveNotifications($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveNotifications || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveNotifications) {
				// return empty collection
				$this->initAbsenceEleveNotifications();
			} else {
				$collAbsenceEleveNotifications = AbsenceEleveNotificationQuery::create(null, $criteria)
					->filterByResponsableEleve($this)
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
	 * Sets a collection of AbsenceEleveNotification objects related by a many-to-many relationship
	 * to the current object by way of the j_notifications_resp_pers cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $absenceEleveNotifications A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAbsenceEleveNotifications(PropelCollection $absenceEleveNotifications, PropelPDO $con = null)
	{
		$jNotificationResponsableEleves = JNotificationResponsableEleveQuery::create()
			->filterByAbsenceEleveNotification($absenceEleveNotifications)
			->filterByResponsableEleve($this)
			->find($con);

		$this->absenceEleveNotificationsScheduledForDeletion = $this->getJNotificationResponsableEleves()->diff($jNotificationResponsableEleves);
		$this->collJNotificationResponsableEleves = $jNotificationResponsableEleves;

		foreach ($absenceEleveNotifications as $absenceEleveNotification) {
			// Fix issue with collection modified by reference
			if ($absenceEleveNotification->isNew()) {
				$this->doAddAbsenceEleveNotification($absenceEleveNotification);
			} else {
				$this->addAbsenceEleveNotification($absenceEleveNotification);
			}
		}

		$this->collAbsenceEleveNotifications = $absenceEleveNotifications;
	}

	/**
	 * Gets the number of AbsenceEleveNotification objects related by a many-to-many relationship
	 * to the current object by way of the j_notifications_resp_pers cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related AbsenceEleveNotification objects
	 */
	public function countAbsenceEleveNotifications($criteria = null, $distinct = false, PropelPDO $con = null)
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
					->filterByResponsableEleve($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveNotifications);
		}
	}

	/**
	 * Associate a AbsenceEleveNotification object to this object
	 * through the j_notifications_resp_pers cross reference table.
	 *
	 * @param      AbsenceEleveNotification $absenceEleveNotification The JNotificationResponsableEleve object to relate
	 * @return     void
	 */
	public function addAbsenceEleveNotification(AbsenceEleveNotification $absenceEleveNotification)
	{
		if ($this->collAbsenceEleveNotifications === null) {
			$this->initAbsenceEleveNotifications();
		}
		if (!$this->collAbsenceEleveNotifications->contains($absenceEleveNotification)) { // only add it if the **same** object is not already associated
			$this->doAddAbsenceEleveNotification($absenceEleveNotification);

			$this->collAbsenceEleveNotifications[]= $absenceEleveNotification;
		}
	}

	/**
	 * @param	AbsenceEleveNotification $absenceEleveNotification The absenceEleveNotification object to add.
	 */
	protected function doAddAbsenceEleveNotification($absenceEleveNotification)
	{
		$jNotificationResponsableEleve = new JNotificationResponsableEleve();
		$jNotificationResponsableEleve->setAbsenceEleveNotification($absenceEleveNotification);
		$this->addJNotificationResponsableEleve($jNotificationResponsableEleve);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->pers_id = null;
		$this->login = null;
		$this->nom = null;
		$this->prenom = null;
		$this->civilite = null;
		$this->tel_pers = null;
		$this->tel_port = null;
		$this->tel_prof = null;
		$this->mel = null;
		$this->adr_id = null;
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
			if ($this->collResponsableInformations) {
				foreach ($this->collResponsableInformations as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJNotificationResponsableEleves) {
				foreach ($this->collJNotificationResponsableEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveNotifications) {
				foreach ($this->collAbsenceEleveNotifications as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collResponsableInformations instanceof PropelCollection) {
			$this->collResponsableInformations->clearIterator();
		}
		$this->collResponsableInformations = null;
		if ($this->collJNotificationResponsableEleves instanceof PropelCollection) {
			$this->collJNotificationResponsableEleves->clearIterator();
		}
		$this->collJNotificationResponsableEleves = null;
		if ($this->collAbsenceEleveNotifications instanceof PropelCollection) {
			$this->collAbsenceEleveNotifications->clearIterator();
		}
		$this->collAbsenceEleveNotifications = null;
		$this->aAdresse = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(ResponsableElevePeer::DEFAULT_STRING_FORMAT);
	}

} // BaseResponsableEleve
