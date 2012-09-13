<?php


/**
 * Base class that represents a row from the 'matieres' table.
 *
 * Matières
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseMatiere extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'MatierePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        MatierePeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

	/**
	 * The value for the matiere field.
	 * @var        string
	 */
	protected $matiere;

	/**
	 * The value for the nom_complet field.
	 * @var        string
	 */
	protected $nom_complet;

	/**
	 * The value for the priority field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $priority;

	/**
	 * The value for the matiere_aid field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $matiere_aid;

	/**
	 * The value for the matiere_atelier field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $matiere_atelier;

	/**
	 * The value for the categorie_id field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $categorie_id;

	/**
	 * @var        CategorieMatiere
	 */
	protected $aCategorieMatiere;

	/**
	 * @var        array JGroupesMatieres[] Collection to store aggregation of JGroupesMatieres objects.
	 */
	protected $collJGroupesMatieress;

	/**
	 * @var        array JProfesseursMatieres[] Collection to store aggregation of JProfesseursMatieres objects.
	 */
	protected $collJProfesseursMatieress;

	/**
	 * @var        array Groupe[] Collection to store aggregation of Groupe objects.
	 */
	protected $collGroupes;

	/**
	 * @var        array UtilisateurProfessionnel[] Collection to store aggregation of UtilisateurProfessionnel objects.
	 */
	protected $collProfesseurs;

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
	protected $groupesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $professeursScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jGroupesMatieressScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jProfesseursMatieressScheduledForDeletion = null;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->priority = 0;
		$this->matiere_aid = 'n';
		$this->matiere_atelier = 'n';
		$this->categorie_id = 1;
	}

	/**
	 * Initializes internal state of BaseMatiere object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [matiere] column value.
	 * 
	 * @return     string
	 */
	public function getMatiere()
	{
		return $this->matiere;
	}

	/**
	 * Get the [nom_complet] column value.
	 * Nom complet
	 * @return     string
	 */
	public function getNomComplet()
	{
		return $this->nom_complet;
	}

	/**
	 * Get the [priority] column value.
	 * Priorite d'affichage
	 * @return     int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * Get the [matiere_aid] column value.
	 * Matiere AID
	 * @return     string
	 */
	public function getMatiereAid()
	{
		return $this->matiere_aid;
	}

	/**
	 * Get the [matiere_atelier] column value.
	 * Matiere Atelier
	 * @return     string
	 */
	public function getMatiereAtelier()
	{
		return $this->matiere_atelier;
	}

	/**
	 * Get the [categorie_id] column value.
	 * Association avec Categories de matieres
	 * @return     int
	 */
	public function getCategorieId()
	{
		return $this->categorie_id;
	}

	/**
	 * Set the value of [matiere] column.
	 * 
	 * @param      string $v new value
	 * @return     Matiere The current object (for fluent API support)
	 */
	public function setMatiere($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->matiere !== $v) {
			$this->matiere = $v;
			$this->modifiedColumns[] = MatierePeer::MATIERE;
		}

		return $this;
	} // setMatiere()

	/**
	 * Set the value of [nom_complet] column.
	 * Nom complet
	 * @param      string $v new value
	 * @return     Matiere The current object (for fluent API support)
	 */
	public function setNomComplet($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom_complet !== $v) {
			$this->nom_complet = $v;
			$this->modifiedColumns[] = MatierePeer::NOM_COMPLET;
		}

		return $this;
	} // setNomComplet()

	/**
	 * Set the value of [priority] column.
	 * Priorite d'affichage
	 * @param      int $v new value
	 * @return     Matiere The current object (for fluent API support)
	 */
	public function setPriority($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->priority !== $v) {
			$this->priority = $v;
			$this->modifiedColumns[] = MatierePeer::PRIORITY;
		}

		return $this;
	} // setPriority()

	/**
	 * Set the value of [matiere_aid] column.
	 * Matiere AID
	 * @param      string $v new value
	 * @return     Matiere The current object (for fluent API support)
	 */
	public function setMatiereAid($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->matiere_aid !== $v) {
			$this->matiere_aid = $v;
			$this->modifiedColumns[] = MatierePeer::MATIERE_AID;
		}

		return $this;
	} // setMatiereAid()

	/**
	 * Set the value of [matiere_atelier] column.
	 * Matiere Atelier
	 * @param      string $v new value
	 * @return     Matiere The current object (for fluent API support)
	 */
	public function setMatiereAtelier($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->matiere_atelier !== $v) {
			$this->matiere_atelier = $v;
			$this->modifiedColumns[] = MatierePeer::MATIERE_ATELIER;
		}

		return $this;
	} // setMatiereAtelier()

	/**
	 * Set the value of [categorie_id] column.
	 * Association avec Categories de matieres
	 * @param      int $v new value
	 * @return     Matiere The current object (for fluent API support)
	 */
	public function setCategorieId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->categorie_id !== $v) {
			$this->categorie_id = $v;
			$this->modifiedColumns[] = MatierePeer::CATEGORIE_ID;
		}

		if ($this->aCategorieMatiere !== null && $this->aCategorieMatiere->getId() !== $v) {
			$this->aCategorieMatiere = null;
		}

		return $this;
	} // setCategorieId()

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
			if ($this->priority !== 0) {
				return false;
			}

			if ($this->matiere_aid !== 'n') {
				return false;
			}

			if ($this->matiere_atelier !== 'n') {
				return false;
			}

			if ($this->categorie_id !== 1) {
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

			$this->matiere = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->nom_complet = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->priority = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->matiere_aid = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->matiere_atelier = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->categorie_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 6; // 6 = MatierePeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating Matiere object", $e);
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

		if ($this->aCategorieMatiere !== null && $this->categorie_id !== $this->aCategorieMatiere->getId()) {
			$this->aCategorieMatiere = null;
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
			$con = Propel::getConnection(MatierePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = MatierePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCategorieMatiere = null;
			$this->collJGroupesMatieress = null;

			$this->collJProfesseursMatieress = null;

			$this->collGroupes = null;
			$this->collProfesseurs = null;
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
			$con = Propel::getConnection(MatierePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = MatiereQuery::create()
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
			$con = Propel::getConnection(MatierePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				MatierePeer::addInstanceToPool($this);
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

			if ($this->aCategorieMatiere !== null) {
				if ($this->aCategorieMatiere->isModified() || $this->aCategorieMatiere->isNew()) {
					$affectedRows += $this->aCategorieMatiere->save($con);
				}
				$this->setCategorieMatiere($this->aCategorieMatiere);
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

			if ($this->groupesScheduledForDeletion !== null) {
				if (!$this->groupesScheduledForDeletion->isEmpty()) {
					JGroupesMatieresQuery::create()
						->filterByPrimaryKeys($this->groupesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->groupesScheduledForDeletion = null;
				}

				foreach ($this->getGroupes() as $groupe) {
					if ($groupe->isModified()) {
						$groupe->save($con);
					}
				}
			}

			if ($this->professeursScheduledForDeletion !== null) {
				if (!$this->professeursScheduledForDeletion->isEmpty()) {
					JProfesseursMatieresQuery::create()
						->filterByPrimaryKeys($this->professeursScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->professeursScheduledForDeletion = null;
				}

				foreach ($this->getProfesseurs() as $professeur) {
					if ($professeur->isModified()) {
						$professeur->save($con);
					}
				}
			}

			if ($this->jGroupesMatieressScheduledForDeletion !== null) {
				if (!$this->jGroupesMatieressScheduledForDeletion->isEmpty()) {
					JGroupesMatieresQuery::create()
						->filterByPrimaryKeys($this->jGroupesMatieressScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jGroupesMatieressScheduledForDeletion = null;
				}
			}

			if ($this->collJGroupesMatieress !== null) {
				foreach ($this->collJGroupesMatieress as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jProfesseursMatieressScheduledForDeletion !== null) {
				if (!$this->jProfesseursMatieressScheduledForDeletion->isEmpty()) {
					JProfesseursMatieresQuery::create()
						->filterByPrimaryKeys($this->jProfesseursMatieressScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jProfesseursMatieressScheduledForDeletion = null;
				}
			}

			if ($this->collJProfesseursMatieress !== null) {
				foreach ($this->collJProfesseursMatieress as $referrerFK) {
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
		if ($this->isColumnModified(MatierePeer::MATIERE)) {
			$modifiedColumns[':p' . $index++]  = 'MATIERE';
		}
		if ($this->isColumnModified(MatierePeer::NOM_COMPLET)) {
			$modifiedColumns[':p' . $index++]  = 'NOM_COMPLET';
		}
		if ($this->isColumnModified(MatierePeer::PRIORITY)) {
			$modifiedColumns[':p' . $index++]  = 'PRIORITY';
		}
		if ($this->isColumnModified(MatierePeer::MATIERE_AID)) {
			$modifiedColumns[':p' . $index++]  = 'MATIERE_AID';
		}
		if ($this->isColumnModified(MatierePeer::MATIERE_ATELIER)) {
			$modifiedColumns[':p' . $index++]  = 'MATIERE_ATELIER';
		}
		if ($this->isColumnModified(MatierePeer::CATEGORIE_ID)) {
			$modifiedColumns[':p' . $index++]  = 'CATEGORIE_ID';
		}

		$sql = sprintf(
			'INSERT INTO matieres (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'MATIERE':
						$stmt->bindValue($identifier, $this->matiere, PDO::PARAM_STR);
						break;
					case 'NOM_COMPLET':
						$stmt->bindValue($identifier, $this->nom_complet, PDO::PARAM_STR);
						break;
					case 'PRIORITY':
						$stmt->bindValue($identifier, $this->priority, PDO::PARAM_INT);
						break;
					case 'MATIERE_AID':
						$stmt->bindValue($identifier, $this->matiere_aid, PDO::PARAM_STR);
						break;
					case 'MATIERE_ATELIER':
						$stmt->bindValue($identifier, $this->matiere_atelier, PDO::PARAM_STR);
						break;
					case 'CATEGORIE_ID':
						$stmt->bindValue($identifier, $this->categorie_id, PDO::PARAM_INT);
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

			if ($this->aCategorieMatiere !== null) {
				if (!$this->aCategorieMatiere->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCategorieMatiere->getValidationFailures());
				}
			}


			if (($retval = MatierePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJGroupesMatieress !== null) {
					foreach ($this->collJGroupesMatieress as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJProfesseursMatieress !== null) {
					foreach ($this->collJProfesseursMatieress as $referrerFK) {
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
		$pos = MatierePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getMatiere();
				break;
			case 1:
				return $this->getNomComplet();
				break;
			case 2:
				return $this->getPriority();
				break;
			case 3:
				return $this->getMatiereAid();
				break;
			case 4:
				return $this->getMatiereAtelier();
				break;
			case 5:
				return $this->getCategorieId();
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
		if (isset($alreadyDumpedObjects['Matiere'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Matiere'][$this->getPrimaryKey()] = true;
		$keys = MatierePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getMatiere(),
			$keys[1] => $this->getNomComplet(),
			$keys[2] => $this->getPriority(),
			$keys[3] => $this->getMatiereAid(),
			$keys[4] => $this->getMatiereAtelier(),
			$keys[5] => $this->getCategorieId(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCategorieMatiere) {
				$result['CategorieMatiere'] = $this->aCategorieMatiere->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->collJGroupesMatieress) {
				$result['JGroupesMatieress'] = $this->collJGroupesMatieress->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJProfesseursMatieress) {
				$result['JProfesseursMatieress'] = $this->collJProfesseursMatieress->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = MatierePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setMatiere($value);
				break;
			case 1:
				$this->setNomComplet($value);
				break;
			case 2:
				$this->setPriority($value);
				break;
			case 3:
				$this->setMatiereAid($value);
				break;
			case 4:
				$this->setMatiereAtelier($value);
				break;
			case 5:
				$this->setCategorieId($value);
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
		$keys = MatierePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setMatiere($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNomComplet($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPriority($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setMatiereAid($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setMatiereAtelier($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCategorieId($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(MatierePeer::DATABASE_NAME);

		if ($this->isColumnModified(MatierePeer::MATIERE)) $criteria->add(MatierePeer::MATIERE, $this->matiere);
		if ($this->isColumnModified(MatierePeer::NOM_COMPLET)) $criteria->add(MatierePeer::NOM_COMPLET, $this->nom_complet);
		if ($this->isColumnModified(MatierePeer::PRIORITY)) $criteria->add(MatierePeer::PRIORITY, $this->priority);
		if ($this->isColumnModified(MatierePeer::MATIERE_AID)) $criteria->add(MatierePeer::MATIERE_AID, $this->matiere_aid);
		if ($this->isColumnModified(MatierePeer::MATIERE_ATELIER)) $criteria->add(MatierePeer::MATIERE_ATELIER, $this->matiere_atelier);
		if ($this->isColumnModified(MatierePeer::CATEGORIE_ID)) $criteria->add(MatierePeer::CATEGORIE_ID, $this->categorie_id);

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
		$criteria = new Criteria(MatierePeer::DATABASE_NAME);
		$criteria->add(MatierePeer::MATIERE, $this->matiere);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getMatiere();
	}

	/**
	 * Generic method to set the primary key (matiere column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setMatiere($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getMatiere();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of Matiere (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setNomComplet($this->getNomComplet());
		$copyObj->setPriority($this->getPriority());
		$copyObj->setMatiereAid($this->getMatiereAid());
		$copyObj->setMatiereAtelier($this->getMatiereAtelier());
		$copyObj->setCategorieId($this->getCategorieId());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getJGroupesMatieress() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJGroupesMatieres($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJProfesseursMatieress() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJProfesseursMatieres($relObj->copy($deepCopy));
				}
			}

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
			$copyObj->setMatiere(NULL); // this is a auto-increment column, so set to default value
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
	 * @return     Matiere Clone of current object.
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
	 * @return     MatierePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new MatierePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CategorieMatiere object.
	 *
	 * @param      CategorieMatiere $v
	 * @return     Matiere The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCategorieMatiere(CategorieMatiere $v = null)
	{
		if ($v === null) {
			$this->setCategorieId(1);
		} else {
			$this->setCategorieId($v->getId());
		}

		$this->aCategorieMatiere = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CategorieMatiere object, it will not be re-added.
		if ($v !== null) {
			$v->addMatiere($this);
		}

		return $this;
	}


	/**
	 * Get the associated CategorieMatiere object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CategorieMatiere The associated CategorieMatiere object.
	 * @throws     PropelException
	 */
	public function getCategorieMatiere(PropelPDO $con = null)
	{
		if ($this->aCategorieMatiere === null && ($this->categorie_id !== null)) {
			$this->aCategorieMatiere = CategorieMatiereQuery::create()->findPk($this->categorie_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aCategorieMatiere->addMatieres($this);
			 */
		}
		return $this->aCategorieMatiere;
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
		if ('JGroupesMatieres' == $relationName) {
			return $this->initJGroupesMatieress();
		}
		if ('JProfesseursMatieres' == $relationName) {
			return $this->initJProfesseursMatieress();
		}
	}

	/**
	 * Clears out the collJGroupesMatieress collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJGroupesMatieress()
	 */
	public function clearJGroupesMatieress()
	{
		$this->collJGroupesMatieress = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJGroupesMatieress collection.
	 *
	 * By default this just sets the collJGroupesMatieress collection to an empty array (like clearcollJGroupesMatieress());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJGroupesMatieress($overrideExisting = true)
	{
		if (null !== $this->collJGroupesMatieress && !$overrideExisting) {
			return;
		}
		$this->collJGroupesMatieress = new PropelObjectCollection();
		$this->collJGroupesMatieress->setModel('JGroupesMatieres');
	}

	/**
	 * Gets an array of JGroupesMatieres objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Matiere is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JGroupesMatieres[] List of JGroupesMatieres objects
	 * @throws     PropelException
	 */
	public function getJGroupesMatieress($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJGroupesMatieress || null !== $criteria) {
			if ($this->isNew() && null === $this->collJGroupesMatieress) {
				// return empty collection
				$this->initJGroupesMatieress();
			} else {
				$collJGroupesMatieress = JGroupesMatieresQuery::create(null, $criteria)
					->filterByMatiere($this)
					->find($con);
				if (null !== $criteria) {
					return $collJGroupesMatieress;
				}
				$this->collJGroupesMatieress = $collJGroupesMatieress;
			}
		}
		return $this->collJGroupesMatieress;
	}

	/**
	 * Sets a collection of JGroupesMatieres objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jGroupesMatieress A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJGroupesMatieress(PropelCollection $jGroupesMatieress, PropelPDO $con = null)
	{
		$this->jGroupesMatieressScheduledForDeletion = $this->getJGroupesMatieress(new Criteria(), $con)->diff($jGroupesMatieress);

		foreach ($jGroupesMatieress as $jGroupesMatieres) {
			// Fix issue with collection modified by reference
			if ($jGroupesMatieres->isNew()) {
				$jGroupesMatieres->setMatiere($this);
			}
			$this->addJGroupesMatieres($jGroupesMatieres);
		}

		$this->collJGroupesMatieress = $jGroupesMatieress;
	}

	/**
	 * Returns the number of related JGroupesMatieres objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JGroupesMatieres objects.
	 * @throws     PropelException
	 */
	public function countJGroupesMatieress(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJGroupesMatieress || null !== $criteria) {
			if ($this->isNew() && null === $this->collJGroupesMatieress) {
				return 0;
			} else {
				$query = JGroupesMatieresQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByMatiere($this)
					->count($con);
			}
		} else {
			return count($this->collJGroupesMatieress);
		}
	}

	/**
	 * Method called to associate a JGroupesMatieres object to this object
	 * through the JGroupesMatieres foreign key attribute.
	 *
	 * @param      JGroupesMatieres $l JGroupesMatieres
	 * @return     Matiere The current object (for fluent API support)
	 */
	public function addJGroupesMatieres(JGroupesMatieres $l)
	{
		if ($this->collJGroupesMatieress === null) {
			$this->initJGroupesMatieress();
		}
		if (!$this->collJGroupesMatieress->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJGroupesMatieres($l);
		}

		return $this;
	}

	/**
	 * @param	JGroupesMatieres $jGroupesMatieres The jGroupesMatieres object to add.
	 */
	protected function doAddJGroupesMatieres($jGroupesMatieres)
	{
		$this->collJGroupesMatieress[]= $jGroupesMatieres;
		$jGroupesMatieres->setMatiere($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Matiere is new, it will return
	 * an empty collection; or if this Matiere has previously
	 * been saved, it will retrieve related JGroupesMatieress from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Matiere.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JGroupesMatieres[] List of JGroupesMatieres objects
	 */
	public function getJGroupesMatieressJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JGroupesMatieresQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getJGroupesMatieress($query, $con);
	}

	/**
	 * Clears out the collJProfesseursMatieress collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJProfesseursMatieress()
	 */
	public function clearJProfesseursMatieress()
	{
		$this->collJProfesseursMatieress = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJProfesseursMatieress collection.
	 *
	 * By default this just sets the collJProfesseursMatieress collection to an empty array (like clearcollJProfesseursMatieress());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJProfesseursMatieress($overrideExisting = true)
	{
		if (null !== $this->collJProfesseursMatieress && !$overrideExisting) {
			return;
		}
		$this->collJProfesseursMatieress = new PropelObjectCollection();
		$this->collJProfesseursMatieress->setModel('JProfesseursMatieres');
	}

	/**
	 * Gets an array of JProfesseursMatieres objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Matiere is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JProfesseursMatieres[] List of JProfesseursMatieres objects
	 * @throws     PropelException
	 */
	public function getJProfesseursMatieress($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJProfesseursMatieress || null !== $criteria) {
			if ($this->isNew() && null === $this->collJProfesseursMatieress) {
				// return empty collection
				$this->initJProfesseursMatieress();
			} else {
				$collJProfesseursMatieress = JProfesseursMatieresQuery::create(null, $criteria)
					->filterByMatiere($this)
					->find($con);
				if (null !== $criteria) {
					return $collJProfesseursMatieress;
				}
				$this->collJProfesseursMatieress = $collJProfesseursMatieress;
			}
		}
		return $this->collJProfesseursMatieress;
	}

	/**
	 * Sets a collection of JProfesseursMatieres objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jProfesseursMatieress A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJProfesseursMatieress(PropelCollection $jProfesseursMatieress, PropelPDO $con = null)
	{
		$this->jProfesseursMatieressScheduledForDeletion = $this->getJProfesseursMatieress(new Criteria(), $con)->diff($jProfesseursMatieress);

		foreach ($jProfesseursMatieress as $jProfesseursMatieres) {
			// Fix issue with collection modified by reference
			if ($jProfesseursMatieres->isNew()) {
				$jProfesseursMatieres->setMatiere($this);
			}
			$this->addJProfesseursMatieres($jProfesseursMatieres);
		}

		$this->collJProfesseursMatieress = $jProfesseursMatieress;
	}

	/**
	 * Returns the number of related JProfesseursMatieres objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JProfesseursMatieres objects.
	 * @throws     PropelException
	 */
	public function countJProfesseursMatieress(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJProfesseursMatieress || null !== $criteria) {
			if ($this->isNew() && null === $this->collJProfesseursMatieress) {
				return 0;
			} else {
				$query = JProfesseursMatieresQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByMatiere($this)
					->count($con);
			}
		} else {
			return count($this->collJProfesseursMatieress);
		}
	}

	/**
	 * Method called to associate a JProfesseursMatieres object to this object
	 * through the JProfesseursMatieres foreign key attribute.
	 *
	 * @param      JProfesseursMatieres $l JProfesseursMatieres
	 * @return     Matiere The current object (for fluent API support)
	 */
	public function addJProfesseursMatieres(JProfesseursMatieres $l)
	{
		if ($this->collJProfesseursMatieress === null) {
			$this->initJProfesseursMatieress();
		}
		if (!$this->collJProfesseursMatieress->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJProfesseursMatieres($l);
		}

		return $this;
	}

	/**
	 * @param	JProfesseursMatieres $jProfesseursMatieres The jProfesseursMatieres object to add.
	 */
	protected function doAddJProfesseursMatieres($jProfesseursMatieres)
	{
		$this->collJProfesseursMatieress[]= $jProfesseursMatieres;
		$jProfesseursMatieres->setMatiere($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Matiere is new, it will return
	 * an empty collection; or if this Matiere has previously
	 * been saved, it will retrieve related JProfesseursMatieress from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Matiere.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JProfesseursMatieres[] List of JProfesseursMatieres objects
	 */
	public function getJProfesseursMatieressJoinProfesseur($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JProfesseursMatieresQuery::create(null, $criteria);
		$query->joinWith('Professeur', $join_behavior);

		return $this->getJProfesseursMatieress($query, $con);
	}

	/**
	 * Clears out the collGroupes collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addGroupes()
	 */
	public function clearGroupes()
	{
		$this->collGroupes = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collGroupes collection.
	 *
	 * By default this just sets the collGroupes collection to an empty collection (like clearGroupes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initGroupes()
	{
		$this->collGroupes = new PropelObjectCollection();
		$this->collGroupes->setModel('Groupe');
	}

	/**
	 * Gets a collection of Groupe objects related by a many-to-many relationship
	 * to the current object by way of the j_groupes_matieres cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Matiere is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array Groupe[] List of Groupe objects
	 */
	public function getGroupes($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collGroupes || null !== $criteria) {
			if ($this->isNew() && null === $this->collGroupes) {
				// return empty collection
				$this->initGroupes();
			} else {
				$collGroupes = GroupeQuery::create(null, $criteria)
					->filterByMatiere($this)
					->find($con);
				if (null !== $criteria) {
					return $collGroupes;
				}
				$this->collGroupes = $collGroupes;
			}
		}
		return $this->collGroupes;
	}

	/**
	 * Sets a collection of Groupe objects related by a many-to-many relationship
	 * to the current object by way of the j_groupes_matieres cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $groupes A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setGroupes(PropelCollection $groupes, PropelPDO $con = null)
	{
		$jGroupesMatieress = JGroupesMatieresQuery::create()
			->filterByGroupe($groupes)
			->filterByMatiere($this)
			->find($con);

		$this->groupesScheduledForDeletion = $this->getJGroupesMatieress()->diff($jGroupesMatieress);
		$this->collJGroupesMatieress = $jGroupesMatieress;

		foreach ($groupes as $groupe) {
			// Fix issue with collection modified by reference
			if ($groupe->isNew()) {
				$this->doAddGroupe($groupe);
			} else {
				$this->addGroupe($groupe);
			}
		}

		$this->collGroupes = $groupes;
	}

	/**
	 * Gets the number of Groupe objects related by a many-to-many relationship
	 * to the current object by way of the j_groupes_matieres cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related Groupe objects
	 */
	public function countGroupes($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collGroupes || null !== $criteria) {
			if ($this->isNew() && null === $this->collGroupes) {
				return 0;
			} else {
				$query = GroupeQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByMatiere($this)
					->count($con);
			}
		} else {
			return count($this->collGroupes);
		}
	}

	/**
	 * Associate a Groupe object to this object
	 * through the j_groupes_matieres cross reference table.
	 *
	 * @param      Groupe $groupe The JGroupesMatieres object to relate
	 * @return     void
	 */
	public function addGroupe(Groupe $groupe)
	{
		if ($this->collGroupes === null) {
			$this->initGroupes();
		}
		if (!$this->collGroupes->contains($groupe)) { // only add it if the **same** object is not already associated
			$this->doAddGroupe($groupe);

			$this->collGroupes[]= $groupe;
		}
	}

	/**
	 * @param	Groupe $groupe The groupe object to add.
	 */
	protected function doAddGroupe($groupe)
	{
		$jGroupesMatieres = new JGroupesMatieres();
		$jGroupesMatieres->setGroupe($groupe);
		$this->addJGroupesMatieres($jGroupesMatieres);
	}

	/**
	 * Clears out the collProfesseurs collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addProfesseurs()
	 */
	public function clearProfesseurs()
	{
		$this->collProfesseurs = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collProfesseurs collection.
	 *
	 * By default this just sets the collProfesseurs collection to an empty collection (like clearProfesseurs());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initProfesseurs()
	{
		$this->collProfesseurs = new PropelObjectCollection();
		$this->collProfesseurs->setModel('UtilisateurProfessionnel');
	}

	/**
	 * Gets a collection of UtilisateurProfessionnel objects related by a many-to-many relationship
	 * to the current object by way of the j_professeurs_matieres cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Matiere is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array UtilisateurProfessionnel[] List of UtilisateurProfessionnel objects
	 */
	public function getProfesseurs($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collProfesseurs || null !== $criteria) {
			if ($this->isNew() && null === $this->collProfesseurs) {
				// return empty collection
				$this->initProfesseurs();
			} else {
				$collProfesseurs = UtilisateurProfessionnelQuery::create(null, $criteria)
					->filterByMatiere($this)
					->find($con);
				if (null !== $criteria) {
					return $collProfesseurs;
				}
				$this->collProfesseurs = $collProfesseurs;
			}
		}
		return $this->collProfesseurs;
	}

	/**
	 * Sets a collection of UtilisateurProfessionnel objects related by a many-to-many relationship
	 * to the current object by way of the j_professeurs_matieres cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $professeurs A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setProfesseurs(PropelCollection $professeurs, PropelPDO $con = null)
	{
		$jProfesseursMatieress = JProfesseursMatieresQuery::create()
			->filterByUtilisateurProfessionnel($professeurs)
			->filterByMatiere($this)
			->find($con);

		$this->professeursScheduledForDeletion = $this->getJProfesseursMatieress()->diff($jProfesseursMatieress);
		$this->collJProfesseursMatieress = $jProfesseursMatieress;

		foreach ($professeurs as $professeur) {
			// Fix issue with collection modified by reference
			if ($professeur->isNew()) {
				$this->doAddUtilisateurProfessionnel($professeur);
			} else {
				$this->addUtilisateurProfessionnel($professeur);
			}
		}

		$this->collProfesseurs = $professeurs;
	}

	/**
	 * Gets the number of UtilisateurProfessionnel objects related by a many-to-many relationship
	 * to the current object by way of the j_professeurs_matieres cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related UtilisateurProfessionnel objects
	 */
	public function countProfesseurs($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collProfesseurs || null !== $criteria) {
			if ($this->isNew() && null === $this->collProfesseurs) {
				return 0;
			} else {
				$query = UtilisateurProfessionnelQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByMatiere($this)
					->count($con);
			}
		} else {
			return count($this->collProfesseurs);
		}
	}

	/**
	 * Associate a UtilisateurProfessionnel object to this object
	 * through the j_professeurs_matieres cross reference table.
	 *
	 * @param      UtilisateurProfessionnel $utilisateurProfessionnel The JProfesseursMatieres object to relate
	 * @return     void
	 */
	public function addProfesseur(UtilisateurProfessionnel $utilisateurProfessionnel)
	{
		if ($this->collProfesseurs === null) {
			$this->initProfesseurs();
		}
		if (!$this->collProfesseurs->contains($utilisateurProfessionnel)) { // only add it if the **same** object is not already associated
			$this->doAddProfesseur($utilisateurProfessionnel);

			$this->collProfesseurs[]= $utilisateurProfessionnel;
		}
	}

	/**
	 * @param	Professeur $professeur The professeur object to add.
	 */
	protected function doAddProfesseur($professeur)
	{
		$jProfesseursMatieres = new JProfesseursMatieres();
		$jProfesseursMatieres->setProfesseur($professeur);
		$this->addJProfesseursMatieres($jProfesseursMatieres);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->matiere = null;
		$this->nom_complet = null;
		$this->priority = null;
		$this->matiere_aid = null;
		$this->matiere_atelier = null;
		$this->categorie_id = null;
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
			if ($this->collJGroupesMatieress) {
				foreach ($this->collJGroupesMatieress as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJProfesseursMatieress) {
				foreach ($this->collJProfesseursMatieress as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collGroupes) {
				foreach ($this->collGroupes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collProfesseurs) {
				foreach ($this->collProfesseurs as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collJGroupesMatieress instanceof PropelCollection) {
			$this->collJGroupesMatieress->clearIterator();
		}
		$this->collJGroupesMatieress = null;
		if ($this->collJProfesseursMatieress instanceof PropelCollection) {
			$this->collJProfesseursMatieress->clearIterator();
		}
		$this->collJProfesseursMatieress = null;
		if ($this->collGroupes instanceof PropelCollection) {
			$this->collGroupes->clearIterator();
		}
		$this->collGroupes = null;
		if ($this->collProfesseurs instanceof PropelCollection) {
			$this->collProfesseurs->clearIterator();
		}
		$this->collProfesseurs = null;
		$this->aCategorieMatiere = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(MatierePeer::DEFAULT_STRING_FORMAT);
	}

} // BaseMatiere
