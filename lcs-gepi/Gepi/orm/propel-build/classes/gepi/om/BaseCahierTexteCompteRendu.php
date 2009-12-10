<?php

/**
 * Base class that represents a row from the 'ct_entry' table.
 *
 * Compte rendu du cahier de texte
 *
 * @package    gepi.om
 */
abstract class BaseCahierTexteCompteRendu extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CahierTexteCompteRenduPeer
	 */
	protected static $peer;

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
	 * The value for the vise field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $vise;

	/**
	 * The value for the visa field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $visa;

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
	 * @var        array CahierTexteCompteRenduFichierJoint[] Collection to store aggregation of CahierTexteCompteRenduFichierJoint objects.
	 */
	protected $collCahierTexteCompteRenduFichierJoints;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCahierTexteCompteRenduFichierJoints.
	 */
	private $lastCahierTexteCompteRenduFichierJointCriteria = null;

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
	 * Initializes internal state of BaseCahierTexteCompteRendu object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

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
		$this->vise = 'n';
		$this->visa = 'n';
		$this->id_sequence = 0;
	}

	/**
	 * Get the [id_ct] column value.
	 * Cle primaire du compte rendu
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
	 * Get the [vise] column value.
	 * vise
	 * @return     string
	 */
	public function getVise()
	{
		return $this->vise;
	}

	/**
	 * Get the [visa] column value.
	 * visa
	 * @return     string
	 */
	public function getVisa()
	{
		return $this->visa;
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
	 * Cle etrangere de la sequence auquel appartient le compte rendu
	 * @return     int
	 */
	public function getIdSequence()
	{
		return $this->id_sequence;
	}

	/**
	 * Set the value of [id_ct] column.
	 * Cle primaire du compte rendu
	 * @param      int $v new value
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
	 */
	public function setIdCt($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_ct !== $v) {
			$this->id_ct = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduPeer::ID_CT;
		}

		return $this;
	} // setIdCt()

	/**
	 * Sets the value of [heure_entry] column to a normalized version of the date/time value specified.
	 * heure de l'entree
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
	 */
	public function setHeureEntry($v)
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

		if ( $this->heure_entry !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->heure_entry !== null && $tmpDt = new DateTime($this->heure_entry)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->heure_entry = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CahierTexteCompteRenduPeer::HEURE_ENTRY;
			}
		} // if either are not null

		return $this;
	} // setHeureEntry()

	/**
	 * Set the value of [date_ct] column.
	 * date du compte rendu
	 * @param      int $v new value
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
	 */
	public function setDateCt($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->date_ct !== $v || $v === 0) {
			$this->date_ct = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduPeer::DATE_CT;
		}

		return $this;
	} // setDateCt()

	/**
	 * Set the value of [contenu] column.
	 * contenu redactionnel du compte rendu
	 * @param      string $v new value
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
	 */
	public function setContenu($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->contenu !== $v) {
			$this->contenu = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduPeer::CONTENU;
		}

		return $this;
	} // setContenu()

	/**
	 * Set the value of [vise] column.
	 * vise
	 * @param      string $v new value
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
	 */
	public function setVise($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->vise !== $v || $v === 'n') {
			$this->vise = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduPeer::VISE;
		}

		return $this;
	} // setVise()

	/**
	 * Set the value of [visa] column.
	 * visa
	 * @param      string $v new value
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
	 */
	public function setVisa($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->visa !== $v || $v === 'n') {
			$this->visa = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduPeer::VISA;
		}

		return $this;
	} // setVisa()

	/**
	 * Set the value of [id_groupe] column.
	 * Cle etrangere du groupe auquel appartient le compte rendu
	 * @param      int $v new value
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
	 */
	public function setIdGroupe($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_groupe !== $v) {
			$this->id_groupe = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduPeer::ID_GROUPE;
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
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
	 */
	public function setIdLogin($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id_login !== $v) {
			$this->id_login = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduPeer::ID_LOGIN;
		}

		if ($this->aUtilisateurProfessionnel !== null && $this->aUtilisateurProfessionnel->getLogin() !== $v) {
			$this->aUtilisateurProfessionnel = null;
		}

		return $this;
	} // setIdLogin()

	/**
	 * Set the value of [id_sequence] column.
	 * Cle etrangere de la sequence auquel appartient le compte rendu
	 * @param      int $v new value
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
	 */
	public function setIdSequence($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_sequence !== $v || $v === 0) {
			$this->id_sequence = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduPeer::ID_SEQUENCE;
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
			// First, ensure that we don't have any columns that have been modified which aren't default columns.
			if (array_diff($this->modifiedColumns, array(CahierTexteCompteRenduPeer::HEURE_ENTRY,CahierTexteCompteRenduPeer::DATE_CT,CahierTexteCompteRenduPeer::VISE,CahierTexteCompteRenduPeer::VISA,CahierTexteCompteRenduPeer::ID_SEQUENCE))) {
				return false;
			}

			if ($this->heure_entry !== '00:00:00') {
				return false;
			}

			if ($this->date_ct !== 0) {
				return false;
			}

			if ($this->vise !== 'n') {
				return false;
			}

			if ($this->visa !== 'n') {
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
			$this->vise = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->visa = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->id_groupe = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->id_login = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->id_sequence = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = CahierTexteCompteRenduPeer::NUM_COLUMNS - CahierTexteCompteRenduPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CahierTexteCompteRendu object", $e);
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
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CahierTexteCompteRenduPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
			$this->collCahierTexteCompteRenduFichierJoints = null;
			$this->lastCahierTexteCompteRenduFichierJointCriteria = null;

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
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			CahierTexteCompteRenduPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
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
			$con = Propel::getConnection(CahierTexteCompteRenduPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			CahierTexteCompteRenduPeer::addInstanceToPool($this);
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

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CahierTexteCompteRenduPeer::ID_CT;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = CahierTexteCompteRenduPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setIdCt($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += CahierTexteCompteRenduPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCahierTexteCompteRenduFichierJoints !== null) {
				foreach ($this->collCahierTexteCompteRenduFichierJoints as $referrerFK) {
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


			if (($retval = CahierTexteCompteRenduPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCahierTexteCompteRenduFichierJoints !== null) {
					foreach ($this->collCahierTexteCompteRenduFichierJoints as $referrerFK) {
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
		$pos = CahierTexteCompteRenduPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getVise();
				break;
			case 5:
				return $this->getVisa();
				break;
			case 6:
				return $this->getIdGroupe();
				break;
			case 7:
				return $this->getIdLogin();
				break;
			case 8:
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
	 * @param      string $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                        BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. Defaults to BasePeer::TYPE_PHPNAME.
	 * @param      boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns.  Defaults to TRUE.
	 * @return     an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true)
	{
		$keys = CahierTexteCompteRenduPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getIdCt(),
			$keys[1] => $this->getHeureEntry(),
			$keys[2] => $this->getDateCt(),
			$keys[3] => $this->getContenu(),
			$keys[4] => $this->getVise(),
			$keys[5] => $this->getVisa(),
			$keys[6] => $this->getIdGroupe(),
			$keys[7] => $this->getIdLogin(),
			$keys[8] => $this->getIdSequence(),
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
		$pos = CahierTexteCompteRenduPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setVise($value);
				break;
			case 5:
				$this->setVisa($value);
				break;
			case 6:
				$this->setIdGroupe($value);
				break;
			case 7:
				$this->setIdLogin($value);
				break;
			case 8:
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
		$keys = CahierTexteCompteRenduPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setIdCt($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setHeureEntry($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDateCt($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setContenu($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setVise($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setVisa($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setIdGroupe($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setIdLogin($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setIdSequence($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);

		if ($this->isColumnModified(CahierTexteCompteRenduPeer::ID_CT)) $criteria->add(CahierTexteCompteRenduPeer::ID_CT, $this->id_ct);
		if ($this->isColumnModified(CahierTexteCompteRenduPeer::HEURE_ENTRY)) $criteria->add(CahierTexteCompteRenduPeer::HEURE_ENTRY, $this->heure_entry);
		if ($this->isColumnModified(CahierTexteCompteRenduPeer::DATE_CT)) $criteria->add(CahierTexteCompteRenduPeer::DATE_CT, $this->date_ct);
		if ($this->isColumnModified(CahierTexteCompteRenduPeer::CONTENU)) $criteria->add(CahierTexteCompteRenduPeer::CONTENU, $this->contenu);
		if ($this->isColumnModified(CahierTexteCompteRenduPeer::VISE)) $criteria->add(CahierTexteCompteRenduPeer::VISE, $this->vise);
		if ($this->isColumnModified(CahierTexteCompteRenduPeer::VISA)) $criteria->add(CahierTexteCompteRenduPeer::VISA, $this->visa);
		if ($this->isColumnModified(CahierTexteCompteRenduPeer::ID_GROUPE)) $criteria->add(CahierTexteCompteRenduPeer::ID_GROUPE, $this->id_groupe);
		if ($this->isColumnModified(CahierTexteCompteRenduPeer::ID_LOGIN)) $criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $this->id_login);
		if ($this->isColumnModified(CahierTexteCompteRenduPeer::ID_SEQUENCE)) $criteria->add(CahierTexteCompteRenduPeer::ID_SEQUENCE, $this->id_sequence);

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
		$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);

		$criteria->add(CahierTexteCompteRenduPeer::ID_CT, $this->id_ct);

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
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of CahierTexteCompteRendu (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setHeureEntry($this->heure_entry);

		$copyObj->setDateCt($this->date_ct);

		$copyObj->setContenu($this->contenu);

		$copyObj->setVise($this->vise);

		$copyObj->setVisa($this->visa);

		$copyObj->setIdGroupe($this->id_groupe);

		$copyObj->setIdLogin($this->id_login);

		$copyObj->setIdSequence($this->id_sequence);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getCahierTexteCompteRenduFichierJoints() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCahierTexteCompteRenduFichierJoint($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

		$copyObj->setIdCt(NULL); // this is a auto-increment column, so set to default value

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
	 * @return     CahierTexteCompteRendu Clone of current object.
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
	 * @return     CahierTexteCompteRenduPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CahierTexteCompteRenduPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Groupe object.
	 *
	 * @param      Groupe $v
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
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
			$v->addCahierTexteCompteRendu($this);
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
			$this->aGroupe = GroupePeer::retrieveByPK($this->id_groupe, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aGroupe->addCahierTexteCompteRendus($this);
			 */
		}
		return $this->aGroupe;
	}

	/**
	 * Declares an association between this object and a UtilisateurProfessionnel object.
	 *
	 * @param      UtilisateurProfessionnel $v
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
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
			$v->addCahierTexteCompteRendu($this);
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
			$this->aUtilisateurProfessionnel = UtilisateurProfessionnelPeer::retrieveByPK($this->id_login, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aUtilisateurProfessionnel->addCahierTexteCompteRendus($this);
			 */
		}
		return $this->aUtilisateurProfessionnel;
	}

	/**
	 * Declares an association between this object and a CahierTexteSequence object.
	 *
	 * @param      CahierTexteSequence $v
	 * @return     CahierTexteCompteRendu The current object (for fluent API support)
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
			$v->addCahierTexteCompteRendu($this);
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
			$this->aCahierTexteSequence = CahierTexteSequencePeer::retrieveByPK($this->id_sequence, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCahierTexteSequence->addCahierTexteCompteRendus($this);
			 */
		}
		return $this->aCahierTexteSequence;
	}

	/**
	 * Clears out the collCahierTexteCompteRenduFichierJoints collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCahierTexteCompteRenduFichierJoints()
	 */
	public function clearCahierTexteCompteRenduFichierJoints()
	{
		$this->collCahierTexteCompteRenduFichierJoints = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCahierTexteCompteRenduFichierJoints collection (array).
	 *
	 * By default this just sets the collCahierTexteCompteRenduFichierJoints collection to an empty array (like clearcollCahierTexteCompteRenduFichierJoints());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCahierTexteCompteRenduFichierJoints()
	{
		$this->collCahierTexteCompteRenduFichierJoints = array();
	}

	/**
	 * Gets an array of CahierTexteCompteRenduFichierJoint objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this CahierTexteCompteRendu has previously been saved, it will retrieve
	 * related CahierTexteCompteRenduFichierJoints from storage. If this CahierTexteCompteRendu is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CahierTexteCompteRenduFichierJoint[]
	 * @throws     PropelException
	 */
	public function getCahierTexteCompteRenduFichierJoints($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteCompteRenduFichierJoints === null) {
			if ($this->isNew()) {
			   $this->collCahierTexteCompteRenduFichierJoints = array();
			} else {

				$criteria->add(CahierTexteCompteRenduFichierJointPeer::ID_CT, $this->id_ct);

				CahierTexteCompteRenduFichierJointPeer::addSelectColumns($criteria);
				$this->collCahierTexteCompteRenduFichierJoints = CahierTexteCompteRenduFichierJointPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CahierTexteCompteRenduFichierJointPeer::ID_CT, $this->id_ct);

				CahierTexteCompteRenduFichierJointPeer::addSelectColumns($criteria);
				if (!isset($this->lastCahierTexteCompteRenduFichierJointCriteria) || !$this->lastCahierTexteCompteRenduFichierJointCriteria->equals($criteria)) {
					$this->collCahierTexteCompteRenduFichierJoints = CahierTexteCompteRenduFichierJointPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCahierTexteCompteRenduFichierJointCriteria = $criteria;
		return $this->collCahierTexteCompteRenduFichierJoints;
	}

	/**
	 * Returns the number of related CahierTexteCompteRenduFichierJoint objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CahierTexteCompteRenduFichierJoint objects.
	 * @throws     PropelException
	 */
	public function countCahierTexteCompteRenduFichierJoints(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collCahierTexteCompteRenduFichierJoints === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CahierTexteCompteRenduFichierJointPeer::ID_CT, $this->id_ct);

				$count = CahierTexteCompteRenduFichierJointPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CahierTexteCompteRenduFichierJointPeer::ID_CT, $this->id_ct);

				if (!isset($this->lastCahierTexteCompteRenduFichierJointCriteria) || !$this->lastCahierTexteCompteRenduFichierJointCriteria->equals($criteria)) {
					$count = CahierTexteCompteRenduFichierJointPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCahierTexteCompteRenduFichierJoints);
				}
			} else {
				$count = count($this->collCahierTexteCompteRenduFichierJoints);
			}
		}
		$this->lastCahierTexteCompteRenduFichierJointCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CahierTexteCompteRenduFichierJoint object to this object
	 * through the CahierTexteCompteRenduFichierJoint foreign key attribute.
	 *
	 * @param      CahierTexteCompteRenduFichierJoint $l CahierTexteCompteRenduFichierJoint
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteCompteRenduFichierJoint(CahierTexteCompteRenduFichierJoint $l)
	{
		if ($this->collCahierTexteCompteRenduFichierJoints === null) {
			$this->initCahierTexteCompteRenduFichierJoints();
		}
		if (!in_array($l, $this->collCahierTexteCompteRenduFichierJoints, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCahierTexteCompteRenduFichierJoints, $l);
			$l->setCahierTexteCompteRendu($this);
		}
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
			if ($this->collCahierTexteCompteRenduFichierJoints) {
				foreach ((array) $this->collCahierTexteCompteRenduFichierJoints as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collCahierTexteCompteRenduFichierJoints = null;
			$this->aGroupe = null;
			$this->aUtilisateurProfessionnel = null;
			$this->aCahierTexteSequence = null;
	}

} // BaseCahierTexteCompteRendu