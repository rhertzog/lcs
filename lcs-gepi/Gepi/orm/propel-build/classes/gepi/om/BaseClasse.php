<?php


/**
 * Base class that represents a row from the 'classes' table.
 *
 * Classe regroupant des eleves
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseClasse extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'ClassePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ClassePeer
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
	 * The value for the classe field.
	 * @var        string
	 */
	protected $classe;

	/**
	 * The value for the nom_complet field.
	 * @var        string
	 */
	protected $nom_complet;

	/**
	 * The value for the suivi_par field.
	 * @var        string
	 */
	protected $suivi_par;

	/**
	 * The value for the formule field.
	 * @var        string
	 */
	protected $formule;

	/**
	 * The value for the format_nom field.
	 * @var        string
	 */
	protected $format_nom;

	/**
	 * The value for the display_rang field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $display_rang;

	/**
	 * The value for the display_address field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $display_address;

	/**
	 * The value for the display_coef field.
	 * Note: this column has a database default value of: 'y'
	 * @var        string
	 */
	protected $display_coef;

	/**
	 * The value for the display_mat_cat field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $display_mat_cat;

	/**
	 * The value for the display_nbdev field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $display_nbdev;

	/**
	 * The value for the display_moy_gen field.
	 * Note: this column has a database default value of: 'y'
	 * @var        string
	 */
	protected $display_moy_gen;

	/**
	 * The value for the modele_bulletin_pdf field.
	 * @var        string
	 */
	protected $modele_bulletin_pdf;

	/**
	 * The value for the rn_nomdev field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_nomdev;

	/**
	 * The value for the rn_toutcoefdev field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_toutcoefdev;

	/**
	 * The value for the rn_coefdev_si_diff field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_coefdev_si_diff;

	/**
	 * The value for the rn_datedev field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_datedev;

	/**
	 * The value for the rn_sign_chefetab field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_sign_chefetab;

	/**
	 * The value for the rn_sign_pp field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_sign_pp;

	/**
	 * The value for the rn_sign_resp field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_sign_resp;

	/**
	 * The value for the rn_sign_nblig field.
	 * Note: this column has a database default value of: 3
	 * @var        int
	 */
	protected $rn_sign_nblig;

	/**
	 * The value for the rn_formule field.
	 * @var        string
	 */
	protected $rn_formule;

	/**
	 * The value for the ects_type_formation field.
	 * @var        string
	 */
	protected $ects_type_formation;

	/**
	 * The value for the ects_parcours field.
	 * @var        string
	 */
	protected $ects_parcours;

	/**
	 * The value for the ects_code_parcours field.
	 * @var        string
	 */
	protected $ects_code_parcours;

	/**
	 * The value for the ects_domaines_etude field.
	 * @var        string
	 */
	protected $ects_domaines_etude;

	/**
	 * The value for the ects_fonction_signataire_attestation field.
	 * @var        string
	 */
	protected $ects_fonction_signataire_attestation;

	/**
	 * @var        array PeriodeNote[] Collection to store aggregation of PeriodeNote objects.
	 */
	protected $collPeriodeNotes;

	/**
	 * @var        array JScolClasses[] Collection to store aggregation of JScolClasses objects.
	 */
	protected $collJScolClassess;

	/**
	 * @var        array JGroupesClasses[] Collection to store aggregation of JGroupesClasses objects.
	 */
	protected $collJGroupesClassess;

	/**
	 * @var        array JEleveClasse[] Collection to store aggregation of JEleveClasse objects.
	 */
	protected $collJEleveClasses;

	/**
	 * @var        array AbsenceEleveSaisie[] Collection to store aggregation of AbsenceEleveSaisie objects.
	 */
	protected $collAbsenceEleveSaisies;

	/**
	 * @var        array JCategoriesMatieresClasses[] Collection to store aggregation of JCategoriesMatieresClasses objects.
	 */
	protected $collJCategoriesMatieresClassess;

	/**
	 * @var        array Groupe[] Collection to store aggregation of Groupe objects.
	 */
	protected $collGroupes;

	/**
	 * @var        array CategorieMatiere[] Collection to store aggregation of CategorieMatiere objects.
	 */
	protected $collCategorieMatieres;

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
	protected $categorieMatieresScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $periodeNotesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jScolClassessScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jGroupesClassessScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jEleveClassesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceEleveSaisiesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jCategoriesMatieresClassessScheduledForDeletion = null;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->display_rang = 'n';
		$this->display_address = 'n';
		$this->display_coef = 'y';
		$this->display_mat_cat = 'n';
		$this->display_nbdev = 'n';
		$this->display_moy_gen = 'y';
		$this->rn_nomdev = 'n';
		$this->rn_toutcoefdev = 'n';
		$this->rn_coefdev_si_diff = 'n';
		$this->rn_datedev = 'n';
		$this->rn_sign_chefetab = 'n';
		$this->rn_sign_pp = 'n';
		$this->rn_sign_resp = 'n';
		$this->rn_sign_nblig = 3;
	}

	/**
	 * Initializes internal state of BaseClasse object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id] column value.
	 * Cle primaire de la classe
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [classe] column value.
	 * nom de la classe. Le nom court est différent pour chaque classe.
	 * @return     string
	 */
	public function getNom()
	{
		return $this->classe;
	}

	/**
	 * Get the [nom_complet] column value.
	 * nom complet de la classe. Le nom long n'est pas toujours différent pour chaque classe. Le nom long peu servir à catégoriser le niveau.
	 * @return     string
	 */
	public function getNomComplet()
	{
		return $this->nom_complet;
	}

	/**
	 * Get the [suivi_par] column value.
	 * 
	 * @return     string
	 */
	public function getSuiviPar()
	{
		return $this->suivi_par;
	}

	/**
	 * Get the [formule] column value.
	 * 
	 * @return     string
	 */
	public function getFormule()
	{
		return $this->formule;
	}

	/**
	 * Get the [format_nom] column value.
	 * 
	 * @return     string
	 */
	public function getFormatNom()
	{
		return $this->format_nom;
	}

	/**
	 * Get the [display_rang] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayRang()
	{
		return $this->display_rang;
	}

	/**
	 * Get the [display_address] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayAddress()
	{
		return $this->display_address;
	}

	/**
	 * Get the [display_coef] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayCoef()
	{
		return $this->display_coef;
	}

	/**
	 * Get the [display_mat_cat] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayMatCat()
	{
		return $this->display_mat_cat;
	}

	/**
	 * Get the [display_nbdev] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayNbdev()
	{
		return $this->display_nbdev;
	}

	/**
	 * Get the [display_moy_gen] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayMoyGen()
	{
		return $this->display_moy_gen;
	}

	/**
	 * Get the [modele_bulletin_pdf] column value.
	 * 
	 * @return     string
	 */
	public function getModeleBulletinPdf()
	{
		return $this->modele_bulletin_pdf;
	}

	/**
	 * Get the [rn_nomdev] column value.
	 * 
	 * @return     string
	 */
	public function getRnNomdev()
	{
		return $this->rn_nomdev;
	}

	/**
	 * Get the [rn_toutcoefdev] column value.
	 * 
	 * @return     string
	 */
	public function getRnToutcoefdev()
	{
		return $this->rn_toutcoefdev;
	}

	/**
	 * Get the [rn_coefdev_si_diff] column value.
	 * 
	 * @return     string
	 */
	public function getRnCoefdevSiDiff()
	{
		return $this->rn_coefdev_si_diff;
	}

	/**
	 * Get the [rn_datedev] column value.
	 * 
	 * @return     string
	 */
	public function getRnDatedev()
	{
		return $this->rn_datedev;
	}

	/**
	 * Get the [rn_sign_chefetab] column value.
	 * 
	 * @return     string
	 */
	public function getRnSignChefetab()
	{
		return $this->rn_sign_chefetab;
	}

	/**
	 * Get the [rn_sign_pp] column value.
	 * 
	 * @return     string
	 */
	public function getRnSignPp()
	{
		return $this->rn_sign_pp;
	}

	/**
	 * Get the [rn_sign_resp] column value.
	 * 
	 * @return     string
	 */
	public function getRnSignResp()
	{
		return $this->rn_sign_resp;
	}

	/**
	 * Get the [rn_sign_nblig] column value.
	 * 
	 * @return     int
	 */
	public function getRnSignNblig()
	{
		return $this->rn_sign_nblig;
	}

	/**
	 * Get the [rn_formule] column value.
	 * 
	 * @return     string
	 */
	public function getRnFormule()
	{
		return $this->rn_formule;
	}

	/**
	 * Get the [ects_type_formation] column value.
	 * 
	 * @return     string
	 */
	public function getEctsTypeFormation()
	{
		return $this->ects_type_formation;
	}

	/**
	 * Get the [ects_parcours] column value.
	 * 
	 * @return     string
	 */
	public function getEctsParcours()
	{
		return $this->ects_parcours;
	}

	/**
	 * Get the [ects_code_parcours] column value.
	 * 
	 * @return     string
	 */
	public function getEctsCodeParcours()
	{
		return $this->ects_code_parcours;
	}

	/**
	 * Get the [ects_domaines_etude] column value.
	 * 
	 * @return     string
	 */
	public function getEctsDomainesEtude()
	{
		return $this->ects_domaines_etude;
	}

	/**
	 * Get the [ects_fonction_signataire_attestation] column value.
	 * 
	 * @return     string
	 */
	public function getEctsFonctionSignataireAttestation()
	{
		return $this->ects_fonction_signataire_attestation;
	}

	/**
	 * Set the value of [id] column.
	 * Cle primaire de la classe
	 * @param      int $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = ClassePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [classe] column.
	 * nom de la classe. Le nom court est différent pour chaque classe.
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->classe !== $v) {
			$this->classe = $v;
			$this->modifiedColumns[] = ClassePeer::CLASSE;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [nom_complet] column.
	 * nom complet de la classe. Le nom long n'est pas toujours différent pour chaque classe. Le nom long peu servir à catégoriser le niveau.
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setNomComplet($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom_complet !== $v) {
			$this->nom_complet = $v;
			$this->modifiedColumns[] = ClassePeer::NOM_COMPLET;
		}

		return $this;
	} // setNomComplet()

	/**
	 * Set the value of [suivi_par] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setSuiviPar($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->suivi_par !== $v) {
			$this->suivi_par = $v;
			$this->modifiedColumns[] = ClassePeer::SUIVI_PAR;
		}

		return $this;
	} // setSuiviPar()

	/**
	 * Set the value of [formule] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setFormule($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->formule !== $v) {
			$this->formule = $v;
			$this->modifiedColumns[] = ClassePeer::FORMULE;
		}

		return $this;
	} // setFormule()

	/**
	 * Set the value of [format_nom] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setFormatNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->format_nom !== $v) {
			$this->format_nom = $v;
			$this->modifiedColumns[] = ClassePeer::FORMAT_NOM;
		}

		return $this;
	} // setFormatNom()

	/**
	 * Set the value of [display_rang] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayRang($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_rang !== $v) {
			$this->display_rang = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_RANG;
		}

		return $this;
	} // setDisplayRang()

	/**
	 * Set the value of [display_address] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayAddress($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_address !== $v) {
			$this->display_address = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_ADDRESS;
		}

		return $this;
	} // setDisplayAddress()

	/**
	 * Set the value of [display_coef] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayCoef($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_coef !== $v) {
			$this->display_coef = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_COEF;
		}

		return $this;
	} // setDisplayCoef()

	/**
	 * Set the value of [display_mat_cat] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayMatCat($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_mat_cat !== $v) {
			$this->display_mat_cat = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_MAT_CAT;
		}

		return $this;
	} // setDisplayMatCat()

	/**
	 * Set the value of [display_nbdev] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayNbdev($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_nbdev !== $v) {
			$this->display_nbdev = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_NBDEV;
		}

		return $this;
	} // setDisplayNbdev()

	/**
	 * Set the value of [display_moy_gen] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayMoyGen($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_moy_gen !== $v) {
			$this->display_moy_gen = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_MOY_GEN;
		}

		return $this;
	} // setDisplayMoyGen()

	/**
	 * Set the value of [modele_bulletin_pdf] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setModeleBulletinPdf($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->modele_bulletin_pdf !== $v) {
			$this->modele_bulletin_pdf = $v;
			$this->modifiedColumns[] = ClassePeer::MODELE_BULLETIN_PDF;
		}

		return $this;
	} // setModeleBulletinPdf()

	/**
	 * Set the value of [rn_nomdev] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnNomdev($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_nomdev !== $v) {
			$this->rn_nomdev = $v;
			$this->modifiedColumns[] = ClassePeer::RN_NOMDEV;
		}

		return $this;
	} // setRnNomdev()

	/**
	 * Set the value of [rn_toutcoefdev] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnToutcoefdev($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_toutcoefdev !== $v) {
			$this->rn_toutcoefdev = $v;
			$this->modifiedColumns[] = ClassePeer::RN_TOUTCOEFDEV;
		}

		return $this;
	} // setRnToutcoefdev()

	/**
	 * Set the value of [rn_coefdev_si_diff] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnCoefdevSiDiff($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_coefdev_si_diff !== $v) {
			$this->rn_coefdev_si_diff = $v;
			$this->modifiedColumns[] = ClassePeer::RN_COEFDEV_SI_DIFF;
		}

		return $this;
	} // setRnCoefdevSiDiff()

	/**
	 * Set the value of [rn_datedev] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnDatedev($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_datedev !== $v) {
			$this->rn_datedev = $v;
			$this->modifiedColumns[] = ClassePeer::RN_DATEDEV;
		}

		return $this;
	} // setRnDatedev()

	/**
	 * Set the value of [rn_sign_chefetab] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnSignChefetab($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_sign_chefetab !== $v) {
			$this->rn_sign_chefetab = $v;
			$this->modifiedColumns[] = ClassePeer::RN_SIGN_CHEFETAB;
		}

		return $this;
	} // setRnSignChefetab()

	/**
	 * Set the value of [rn_sign_pp] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnSignPp($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_sign_pp !== $v) {
			$this->rn_sign_pp = $v;
			$this->modifiedColumns[] = ClassePeer::RN_SIGN_PP;
		}

		return $this;
	} // setRnSignPp()

	/**
	 * Set the value of [rn_sign_resp] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnSignResp($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_sign_resp !== $v) {
			$this->rn_sign_resp = $v;
			$this->modifiedColumns[] = ClassePeer::RN_SIGN_RESP;
		}

		return $this;
	} // setRnSignResp()

	/**
	 * Set the value of [rn_sign_nblig] column.
	 * 
	 * @param      int $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnSignNblig($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->rn_sign_nblig !== $v) {
			$this->rn_sign_nblig = $v;
			$this->modifiedColumns[] = ClassePeer::RN_SIGN_NBLIG;
		}

		return $this;
	} // setRnSignNblig()

	/**
	 * Set the value of [rn_formule] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnFormule($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_formule !== $v) {
			$this->rn_formule = $v;
			$this->modifiedColumns[] = ClassePeer::RN_FORMULE;
		}

		return $this;
	} // setRnFormule()

	/**
	 * Set the value of [ects_type_formation] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsTypeFormation($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_type_formation !== $v) {
			$this->ects_type_formation = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_TYPE_FORMATION;
		}

		return $this;
	} // setEctsTypeFormation()

	/**
	 * Set the value of [ects_parcours] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsParcours($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_parcours !== $v) {
			$this->ects_parcours = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_PARCOURS;
		}

		return $this;
	} // setEctsParcours()

	/**
	 * Set the value of [ects_code_parcours] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsCodeParcours($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_code_parcours !== $v) {
			$this->ects_code_parcours = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_CODE_PARCOURS;
		}

		return $this;
	} // setEctsCodeParcours()

	/**
	 * Set the value of [ects_domaines_etude] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsDomainesEtude($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_domaines_etude !== $v) {
			$this->ects_domaines_etude = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_DOMAINES_ETUDE;
		}

		return $this;
	} // setEctsDomainesEtude()

	/**
	 * Set the value of [ects_fonction_signataire_attestation] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsFonctionSignataireAttestation($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_fonction_signataire_attestation !== $v) {
			$this->ects_fonction_signataire_attestation = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_FONCTION_SIGNATAIRE_ATTESTATION;
		}

		return $this;
	} // setEctsFonctionSignataireAttestation()

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
			if ($this->display_rang !== 'n') {
				return false;
			}

			if ($this->display_address !== 'n') {
				return false;
			}

			if ($this->display_coef !== 'y') {
				return false;
			}

			if ($this->display_mat_cat !== 'n') {
				return false;
			}

			if ($this->display_nbdev !== 'n') {
				return false;
			}

			if ($this->display_moy_gen !== 'y') {
				return false;
			}

			if ($this->rn_nomdev !== 'n') {
				return false;
			}

			if ($this->rn_toutcoefdev !== 'n') {
				return false;
			}

			if ($this->rn_coefdev_si_diff !== 'n') {
				return false;
			}

			if ($this->rn_datedev !== 'n') {
				return false;
			}

			if ($this->rn_sign_chefetab !== 'n') {
				return false;
			}

			if ($this->rn_sign_pp !== 'n') {
				return false;
			}

			if ($this->rn_sign_resp !== 'n') {
				return false;
			}

			if ($this->rn_sign_nblig !== 3) {
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

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->classe = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->nom_complet = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->suivi_par = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->formule = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->format_nom = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->display_rang = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->display_address = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->display_coef = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->display_mat_cat = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->display_nbdev = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->display_moy_gen = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->modele_bulletin_pdf = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->rn_nomdev = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->rn_toutcoefdev = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->rn_coefdev_si_diff = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->rn_datedev = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->rn_sign_chefetab = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->rn_sign_pp = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->rn_sign_resp = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->rn_sign_nblig = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->rn_formule = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->ects_type_formation = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->ects_parcours = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->ects_code_parcours = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
			$this->ects_domaines_etude = ($row[$startcol + 25] !== null) ? (string) $row[$startcol + 25] : null;
			$this->ects_fonction_signataire_attestation = ($row[$startcol + 26] !== null) ? (string) $row[$startcol + 26] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 27; // 27 = ClassePeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating Classe object", $e);
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
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = ClassePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collPeriodeNotes = null;

			$this->collJScolClassess = null;

			$this->collJGroupesClassess = null;

			$this->collJEleveClasses = null;

			$this->collAbsenceEleveSaisies = null;

			$this->collJCategoriesMatieresClassess = null;

			$this->collGroupes = null;
			$this->collCategorieMatieres = null;
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
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = ClasseQuery::create()
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
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				ClassePeer::addInstanceToPool($this);
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

			if ($this->groupesScheduledForDeletion !== null) {
				if (!$this->groupesScheduledForDeletion->isEmpty()) {
					JGroupesClassesQuery::create()
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

			if ($this->categorieMatieresScheduledForDeletion !== null) {
				if (!$this->categorieMatieresScheduledForDeletion->isEmpty()) {
					JCategoriesMatieresClassesQuery::create()
						->filterByPrimaryKeys($this->categorieMatieresScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->categorieMatieresScheduledForDeletion = null;
				}

				foreach ($this->getCategorieMatieres() as $categorieMatiere) {
					if ($categorieMatiere->isModified()) {
						$categorieMatiere->save($con);
					}
				}
			}

			if ($this->periodeNotesScheduledForDeletion !== null) {
				if (!$this->periodeNotesScheduledForDeletion->isEmpty()) {
					PeriodeNoteQuery::create()
						->filterByPrimaryKeys($this->periodeNotesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->periodeNotesScheduledForDeletion = null;
				}
			}

			if ($this->collPeriodeNotes !== null) {
				foreach ($this->collPeriodeNotes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jScolClassessScheduledForDeletion !== null) {
				if (!$this->jScolClassessScheduledForDeletion->isEmpty()) {
					JScolClassesQuery::create()
						->filterByPrimaryKeys($this->jScolClassessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jScolClassessScheduledForDeletion = null;
				}
			}

			if ($this->collJScolClassess !== null) {
				foreach ($this->collJScolClassess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jGroupesClassessScheduledForDeletion !== null) {
				if (!$this->jGroupesClassessScheduledForDeletion->isEmpty()) {
					JGroupesClassesQuery::create()
						->filterByPrimaryKeys($this->jGroupesClassessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jGroupesClassessScheduledForDeletion = null;
				}
			}

			if ($this->collJGroupesClassess !== null) {
				foreach ($this->collJGroupesClassess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jEleveClassesScheduledForDeletion !== null) {
				if (!$this->jEleveClassesScheduledForDeletion->isEmpty()) {
					JEleveClasseQuery::create()
						->filterByPrimaryKeys($this->jEleveClassesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jEleveClassesScheduledForDeletion = null;
				}
			}

			if ($this->collJEleveClasses !== null) {
				foreach ($this->collJEleveClasses as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->absenceEleveSaisiesScheduledForDeletion !== null) {
				if (!$this->absenceEleveSaisiesScheduledForDeletion->isEmpty()) {
					AbsenceEleveSaisieQuery::create()
						->filterByPrimaryKeys($this->absenceEleveSaisiesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->absenceEleveSaisiesScheduledForDeletion = null;
				}
			}

			if ($this->collAbsenceEleveSaisies !== null) {
				foreach ($this->collAbsenceEleveSaisies as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jCategoriesMatieresClassessScheduledForDeletion !== null) {
				if (!$this->jCategoriesMatieresClassessScheduledForDeletion->isEmpty()) {
					JCategoriesMatieresClassesQuery::create()
						->filterByPrimaryKeys($this->jCategoriesMatieresClassessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jCategoriesMatieresClassessScheduledForDeletion = null;
				}
			}

			if ($this->collJCategoriesMatieresClassess !== null) {
				foreach ($this->collJCategoriesMatieresClassess as $referrerFK) {
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

		$this->modifiedColumns[] = ClassePeer::ID;
		if (null !== $this->id) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . ClassePeer::ID . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(ClassePeer::ID)) {
			$modifiedColumns[':p' . $index++]  = 'ID';
		}
		if ($this->isColumnModified(ClassePeer::CLASSE)) {
			$modifiedColumns[':p' . $index++]  = 'CLASSE';
		}
		if ($this->isColumnModified(ClassePeer::NOM_COMPLET)) {
			$modifiedColumns[':p' . $index++]  = 'NOM_COMPLET';
		}
		if ($this->isColumnModified(ClassePeer::SUIVI_PAR)) {
			$modifiedColumns[':p' . $index++]  = 'SUIVI_PAR';
		}
		if ($this->isColumnModified(ClassePeer::FORMULE)) {
			$modifiedColumns[':p' . $index++]  = 'FORMULE';
		}
		if ($this->isColumnModified(ClassePeer::FORMAT_NOM)) {
			$modifiedColumns[':p' . $index++]  = 'FORMAT_NOM';
		}
		if ($this->isColumnModified(ClassePeer::DISPLAY_RANG)) {
			$modifiedColumns[':p' . $index++]  = 'DISPLAY_RANG';
		}
		if ($this->isColumnModified(ClassePeer::DISPLAY_ADDRESS)) {
			$modifiedColumns[':p' . $index++]  = 'DISPLAY_ADDRESS';
		}
		if ($this->isColumnModified(ClassePeer::DISPLAY_COEF)) {
			$modifiedColumns[':p' . $index++]  = 'DISPLAY_COEF';
		}
		if ($this->isColumnModified(ClassePeer::DISPLAY_MAT_CAT)) {
			$modifiedColumns[':p' . $index++]  = 'DISPLAY_MAT_CAT';
		}
		if ($this->isColumnModified(ClassePeer::DISPLAY_NBDEV)) {
			$modifiedColumns[':p' . $index++]  = 'DISPLAY_NBDEV';
		}
		if ($this->isColumnModified(ClassePeer::DISPLAY_MOY_GEN)) {
			$modifiedColumns[':p' . $index++]  = 'DISPLAY_MOY_GEN';
		}
		if ($this->isColumnModified(ClassePeer::MODELE_BULLETIN_PDF)) {
			$modifiedColumns[':p' . $index++]  = 'MODELE_BULLETIN_PDF';
		}
		if ($this->isColumnModified(ClassePeer::RN_NOMDEV)) {
			$modifiedColumns[':p' . $index++]  = 'RN_NOMDEV';
		}
		if ($this->isColumnModified(ClassePeer::RN_TOUTCOEFDEV)) {
			$modifiedColumns[':p' . $index++]  = 'RN_TOUTCOEFDEV';
		}
		if ($this->isColumnModified(ClassePeer::RN_COEFDEV_SI_DIFF)) {
			$modifiedColumns[':p' . $index++]  = 'RN_COEFDEV_SI_DIFF';
		}
		if ($this->isColumnModified(ClassePeer::RN_DATEDEV)) {
			$modifiedColumns[':p' . $index++]  = 'RN_DATEDEV';
		}
		if ($this->isColumnModified(ClassePeer::RN_SIGN_CHEFETAB)) {
			$modifiedColumns[':p' . $index++]  = 'RN_SIGN_CHEFETAB';
		}
		if ($this->isColumnModified(ClassePeer::RN_SIGN_PP)) {
			$modifiedColumns[':p' . $index++]  = 'RN_SIGN_PP';
		}
		if ($this->isColumnModified(ClassePeer::RN_SIGN_RESP)) {
			$modifiedColumns[':p' . $index++]  = 'RN_SIGN_RESP';
		}
		if ($this->isColumnModified(ClassePeer::RN_SIGN_NBLIG)) {
			$modifiedColumns[':p' . $index++]  = 'RN_SIGN_NBLIG';
		}
		if ($this->isColumnModified(ClassePeer::RN_FORMULE)) {
			$modifiedColumns[':p' . $index++]  = 'RN_FORMULE';
		}
		if ($this->isColumnModified(ClassePeer::ECTS_TYPE_FORMATION)) {
			$modifiedColumns[':p' . $index++]  = 'ECTS_TYPE_FORMATION';
		}
		if ($this->isColumnModified(ClassePeer::ECTS_PARCOURS)) {
			$modifiedColumns[':p' . $index++]  = 'ECTS_PARCOURS';
		}
		if ($this->isColumnModified(ClassePeer::ECTS_CODE_PARCOURS)) {
			$modifiedColumns[':p' . $index++]  = 'ECTS_CODE_PARCOURS';
		}
		if ($this->isColumnModified(ClassePeer::ECTS_DOMAINES_ETUDE)) {
			$modifiedColumns[':p' . $index++]  = 'ECTS_DOMAINES_ETUDE';
		}
		if ($this->isColumnModified(ClassePeer::ECTS_FONCTION_SIGNATAIRE_ATTESTATION)) {
			$modifiedColumns[':p' . $index++]  = 'ECTS_FONCTION_SIGNATAIRE_ATTESTATION';
		}

		$sql = sprintf(
			'INSERT INTO classes (%s) VALUES (%s)',
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
					case 'CLASSE':
						$stmt->bindValue($identifier, $this->classe, PDO::PARAM_STR);
						break;
					case 'NOM_COMPLET':
						$stmt->bindValue($identifier, $this->nom_complet, PDO::PARAM_STR);
						break;
					case 'SUIVI_PAR':
						$stmt->bindValue($identifier, $this->suivi_par, PDO::PARAM_STR);
						break;
					case 'FORMULE':
						$stmt->bindValue($identifier, $this->formule, PDO::PARAM_STR);
						break;
					case 'FORMAT_NOM':
						$stmt->bindValue($identifier, $this->format_nom, PDO::PARAM_STR);
						break;
					case 'DISPLAY_RANG':
						$stmt->bindValue($identifier, $this->display_rang, PDO::PARAM_STR);
						break;
					case 'DISPLAY_ADDRESS':
						$stmt->bindValue($identifier, $this->display_address, PDO::PARAM_STR);
						break;
					case 'DISPLAY_COEF':
						$stmt->bindValue($identifier, $this->display_coef, PDO::PARAM_STR);
						break;
					case 'DISPLAY_MAT_CAT':
						$stmt->bindValue($identifier, $this->display_mat_cat, PDO::PARAM_STR);
						break;
					case 'DISPLAY_NBDEV':
						$stmt->bindValue($identifier, $this->display_nbdev, PDO::PARAM_STR);
						break;
					case 'DISPLAY_MOY_GEN':
						$stmt->bindValue($identifier, $this->display_moy_gen, PDO::PARAM_STR);
						break;
					case 'MODELE_BULLETIN_PDF':
						$stmt->bindValue($identifier, $this->modele_bulletin_pdf, PDO::PARAM_STR);
						break;
					case 'RN_NOMDEV':
						$stmt->bindValue($identifier, $this->rn_nomdev, PDO::PARAM_STR);
						break;
					case 'RN_TOUTCOEFDEV':
						$stmt->bindValue($identifier, $this->rn_toutcoefdev, PDO::PARAM_STR);
						break;
					case 'RN_COEFDEV_SI_DIFF':
						$stmt->bindValue($identifier, $this->rn_coefdev_si_diff, PDO::PARAM_STR);
						break;
					case 'RN_DATEDEV':
						$stmt->bindValue($identifier, $this->rn_datedev, PDO::PARAM_STR);
						break;
					case 'RN_SIGN_CHEFETAB':
						$stmt->bindValue($identifier, $this->rn_sign_chefetab, PDO::PARAM_STR);
						break;
					case 'RN_SIGN_PP':
						$stmt->bindValue($identifier, $this->rn_sign_pp, PDO::PARAM_STR);
						break;
					case 'RN_SIGN_RESP':
						$stmt->bindValue($identifier, $this->rn_sign_resp, PDO::PARAM_STR);
						break;
					case 'RN_SIGN_NBLIG':
						$stmt->bindValue($identifier, $this->rn_sign_nblig, PDO::PARAM_INT);
						break;
					case 'RN_FORMULE':
						$stmt->bindValue($identifier, $this->rn_formule, PDO::PARAM_STR);
						break;
					case 'ECTS_TYPE_FORMATION':
						$stmt->bindValue($identifier, $this->ects_type_formation, PDO::PARAM_STR);
						break;
					case 'ECTS_PARCOURS':
						$stmt->bindValue($identifier, $this->ects_parcours, PDO::PARAM_STR);
						break;
					case 'ECTS_CODE_PARCOURS':
						$stmt->bindValue($identifier, $this->ects_code_parcours, PDO::PARAM_STR);
						break;
					case 'ECTS_DOMAINES_ETUDE':
						$stmt->bindValue($identifier, $this->ects_domaines_etude, PDO::PARAM_STR);
						break;
					case 'ECTS_FONCTION_SIGNATAIRE_ATTESTATION':
						$stmt->bindValue($identifier, $this->ects_fonction_signataire_attestation, PDO::PARAM_STR);
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


			if (($retval = ClassePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collPeriodeNotes !== null) {
					foreach ($this->collPeriodeNotes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJScolClassess !== null) {
					foreach ($this->collJScolClassess as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJGroupesClassess !== null) {
					foreach ($this->collJGroupesClassess as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJEleveClasses !== null) {
					foreach ($this->collJEleveClasses as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAbsenceEleveSaisies !== null) {
					foreach ($this->collAbsenceEleveSaisies as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJCategoriesMatieresClassess !== null) {
					foreach ($this->collJCategoriesMatieresClassess as $referrerFK) {
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
		$pos = ClassePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getNomComplet();
				break;
			case 3:
				return $this->getSuiviPar();
				break;
			case 4:
				return $this->getFormule();
				break;
			case 5:
				return $this->getFormatNom();
				break;
			case 6:
				return $this->getDisplayRang();
				break;
			case 7:
				return $this->getDisplayAddress();
				break;
			case 8:
				return $this->getDisplayCoef();
				break;
			case 9:
				return $this->getDisplayMatCat();
				break;
			case 10:
				return $this->getDisplayNbdev();
				break;
			case 11:
				return $this->getDisplayMoyGen();
				break;
			case 12:
				return $this->getModeleBulletinPdf();
				break;
			case 13:
				return $this->getRnNomdev();
				break;
			case 14:
				return $this->getRnToutcoefdev();
				break;
			case 15:
				return $this->getRnCoefdevSiDiff();
				break;
			case 16:
				return $this->getRnDatedev();
				break;
			case 17:
				return $this->getRnSignChefetab();
				break;
			case 18:
				return $this->getRnSignPp();
				break;
			case 19:
				return $this->getRnSignResp();
				break;
			case 20:
				return $this->getRnSignNblig();
				break;
			case 21:
				return $this->getRnFormule();
				break;
			case 22:
				return $this->getEctsTypeFormation();
				break;
			case 23:
				return $this->getEctsParcours();
				break;
			case 24:
				return $this->getEctsCodeParcours();
				break;
			case 25:
				return $this->getEctsDomainesEtude();
				break;
			case 26:
				return $this->getEctsFonctionSignataireAttestation();
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
		if (isset($alreadyDumpedObjects['Classe'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Classe'][$this->getPrimaryKey()] = true;
		$keys = ClassePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getNom(),
			$keys[2] => $this->getNomComplet(),
			$keys[3] => $this->getSuiviPar(),
			$keys[4] => $this->getFormule(),
			$keys[5] => $this->getFormatNom(),
			$keys[6] => $this->getDisplayRang(),
			$keys[7] => $this->getDisplayAddress(),
			$keys[8] => $this->getDisplayCoef(),
			$keys[9] => $this->getDisplayMatCat(),
			$keys[10] => $this->getDisplayNbdev(),
			$keys[11] => $this->getDisplayMoyGen(),
			$keys[12] => $this->getModeleBulletinPdf(),
			$keys[13] => $this->getRnNomdev(),
			$keys[14] => $this->getRnToutcoefdev(),
			$keys[15] => $this->getRnCoefdevSiDiff(),
			$keys[16] => $this->getRnDatedev(),
			$keys[17] => $this->getRnSignChefetab(),
			$keys[18] => $this->getRnSignPp(),
			$keys[19] => $this->getRnSignResp(),
			$keys[20] => $this->getRnSignNblig(),
			$keys[21] => $this->getRnFormule(),
			$keys[22] => $this->getEctsTypeFormation(),
			$keys[23] => $this->getEctsParcours(),
			$keys[24] => $this->getEctsCodeParcours(),
			$keys[25] => $this->getEctsDomainesEtude(),
			$keys[26] => $this->getEctsFonctionSignataireAttestation(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->collPeriodeNotes) {
				$result['PeriodeNotes'] = $this->collPeriodeNotes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJScolClassess) {
				$result['JScolClassess'] = $this->collJScolClassess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJGroupesClassess) {
				$result['JGroupesClassess'] = $this->collJGroupesClassess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJEleveClasses) {
				$result['JEleveClasses'] = $this->collJEleveClasses->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceEleveSaisies) {
				$result['AbsenceEleveSaisies'] = $this->collAbsenceEleveSaisies->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJCategoriesMatieresClassess) {
				$result['JCategoriesMatieresClassess'] = $this->collJCategoriesMatieresClassess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = ClassePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setNomComplet($value);
				break;
			case 3:
				$this->setSuiviPar($value);
				break;
			case 4:
				$this->setFormule($value);
				break;
			case 5:
				$this->setFormatNom($value);
				break;
			case 6:
				$this->setDisplayRang($value);
				break;
			case 7:
				$this->setDisplayAddress($value);
				break;
			case 8:
				$this->setDisplayCoef($value);
				break;
			case 9:
				$this->setDisplayMatCat($value);
				break;
			case 10:
				$this->setDisplayNbdev($value);
				break;
			case 11:
				$this->setDisplayMoyGen($value);
				break;
			case 12:
				$this->setModeleBulletinPdf($value);
				break;
			case 13:
				$this->setRnNomdev($value);
				break;
			case 14:
				$this->setRnToutcoefdev($value);
				break;
			case 15:
				$this->setRnCoefdevSiDiff($value);
				break;
			case 16:
				$this->setRnDatedev($value);
				break;
			case 17:
				$this->setRnSignChefetab($value);
				break;
			case 18:
				$this->setRnSignPp($value);
				break;
			case 19:
				$this->setRnSignResp($value);
				break;
			case 20:
				$this->setRnSignNblig($value);
				break;
			case 21:
				$this->setRnFormule($value);
				break;
			case 22:
				$this->setEctsTypeFormation($value);
				break;
			case 23:
				$this->setEctsParcours($value);
				break;
			case 24:
				$this->setEctsCodeParcours($value);
				break;
			case 25:
				$this->setEctsDomainesEtude($value);
				break;
			case 26:
				$this->setEctsFonctionSignataireAttestation($value);
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
		$keys = ClassePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNom($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNomComplet($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setSuiviPar($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFormule($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setFormatNom($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDisplayRang($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDisplayAddress($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDisplayCoef($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setDisplayMatCat($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDisplayNbdev($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDisplayMoyGen($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setModeleBulletinPdf($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setRnNomdev($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setRnToutcoefdev($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setRnCoefdevSiDiff($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setRnDatedev($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setRnSignChefetab($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setRnSignPp($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setRnSignResp($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setRnSignNblig($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setRnFormule($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setEctsTypeFormation($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setEctsParcours($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setEctsCodeParcours($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setEctsDomainesEtude($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setEctsFonctionSignataireAttestation($arr[$keys[26]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ClassePeer::DATABASE_NAME);

		if ($this->isColumnModified(ClassePeer::ID)) $criteria->add(ClassePeer::ID, $this->id);
		if ($this->isColumnModified(ClassePeer::CLASSE)) $criteria->add(ClassePeer::CLASSE, $this->classe);
		if ($this->isColumnModified(ClassePeer::NOM_COMPLET)) $criteria->add(ClassePeer::NOM_COMPLET, $this->nom_complet);
		if ($this->isColumnModified(ClassePeer::SUIVI_PAR)) $criteria->add(ClassePeer::SUIVI_PAR, $this->suivi_par);
		if ($this->isColumnModified(ClassePeer::FORMULE)) $criteria->add(ClassePeer::FORMULE, $this->formule);
		if ($this->isColumnModified(ClassePeer::FORMAT_NOM)) $criteria->add(ClassePeer::FORMAT_NOM, $this->format_nom);
		if ($this->isColumnModified(ClassePeer::DISPLAY_RANG)) $criteria->add(ClassePeer::DISPLAY_RANG, $this->display_rang);
		if ($this->isColumnModified(ClassePeer::DISPLAY_ADDRESS)) $criteria->add(ClassePeer::DISPLAY_ADDRESS, $this->display_address);
		if ($this->isColumnModified(ClassePeer::DISPLAY_COEF)) $criteria->add(ClassePeer::DISPLAY_COEF, $this->display_coef);
		if ($this->isColumnModified(ClassePeer::DISPLAY_MAT_CAT)) $criteria->add(ClassePeer::DISPLAY_MAT_CAT, $this->display_mat_cat);
		if ($this->isColumnModified(ClassePeer::DISPLAY_NBDEV)) $criteria->add(ClassePeer::DISPLAY_NBDEV, $this->display_nbdev);
		if ($this->isColumnModified(ClassePeer::DISPLAY_MOY_GEN)) $criteria->add(ClassePeer::DISPLAY_MOY_GEN, $this->display_moy_gen);
		if ($this->isColumnModified(ClassePeer::MODELE_BULLETIN_PDF)) $criteria->add(ClassePeer::MODELE_BULLETIN_PDF, $this->modele_bulletin_pdf);
		if ($this->isColumnModified(ClassePeer::RN_NOMDEV)) $criteria->add(ClassePeer::RN_NOMDEV, $this->rn_nomdev);
		if ($this->isColumnModified(ClassePeer::RN_TOUTCOEFDEV)) $criteria->add(ClassePeer::RN_TOUTCOEFDEV, $this->rn_toutcoefdev);
		if ($this->isColumnModified(ClassePeer::RN_COEFDEV_SI_DIFF)) $criteria->add(ClassePeer::RN_COEFDEV_SI_DIFF, $this->rn_coefdev_si_diff);
		if ($this->isColumnModified(ClassePeer::RN_DATEDEV)) $criteria->add(ClassePeer::RN_DATEDEV, $this->rn_datedev);
		if ($this->isColumnModified(ClassePeer::RN_SIGN_CHEFETAB)) $criteria->add(ClassePeer::RN_SIGN_CHEFETAB, $this->rn_sign_chefetab);
		if ($this->isColumnModified(ClassePeer::RN_SIGN_PP)) $criteria->add(ClassePeer::RN_SIGN_PP, $this->rn_sign_pp);
		if ($this->isColumnModified(ClassePeer::RN_SIGN_RESP)) $criteria->add(ClassePeer::RN_SIGN_RESP, $this->rn_sign_resp);
		if ($this->isColumnModified(ClassePeer::RN_SIGN_NBLIG)) $criteria->add(ClassePeer::RN_SIGN_NBLIG, $this->rn_sign_nblig);
		if ($this->isColumnModified(ClassePeer::RN_FORMULE)) $criteria->add(ClassePeer::RN_FORMULE, $this->rn_formule);
		if ($this->isColumnModified(ClassePeer::ECTS_TYPE_FORMATION)) $criteria->add(ClassePeer::ECTS_TYPE_FORMATION, $this->ects_type_formation);
		if ($this->isColumnModified(ClassePeer::ECTS_PARCOURS)) $criteria->add(ClassePeer::ECTS_PARCOURS, $this->ects_parcours);
		if ($this->isColumnModified(ClassePeer::ECTS_CODE_PARCOURS)) $criteria->add(ClassePeer::ECTS_CODE_PARCOURS, $this->ects_code_parcours);
		if ($this->isColumnModified(ClassePeer::ECTS_DOMAINES_ETUDE)) $criteria->add(ClassePeer::ECTS_DOMAINES_ETUDE, $this->ects_domaines_etude);
		if ($this->isColumnModified(ClassePeer::ECTS_FONCTION_SIGNATAIRE_ATTESTATION)) $criteria->add(ClassePeer::ECTS_FONCTION_SIGNATAIRE_ATTESTATION, $this->ects_fonction_signataire_attestation);

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
		$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		$criteria->add(ClassePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Classe (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setNom($this->getNom());
		$copyObj->setNomComplet($this->getNomComplet());
		$copyObj->setSuiviPar($this->getSuiviPar());
		$copyObj->setFormule($this->getFormule());
		$copyObj->setFormatNom($this->getFormatNom());
		$copyObj->setDisplayRang($this->getDisplayRang());
		$copyObj->setDisplayAddress($this->getDisplayAddress());
		$copyObj->setDisplayCoef($this->getDisplayCoef());
		$copyObj->setDisplayMatCat($this->getDisplayMatCat());
		$copyObj->setDisplayNbdev($this->getDisplayNbdev());
		$copyObj->setDisplayMoyGen($this->getDisplayMoyGen());
		$copyObj->setModeleBulletinPdf($this->getModeleBulletinPdf());
		$copyObj->setRnNomdev($this->getRnNomdev());
		$copyObj->setRnToutcoefdev($this->getRnToutcoefdev());
		$copyObj->setRnCoefdevSiDiff($this->getRnCoefdevSiDiff());
		$copyObj->setRnDatedev($this->getRnDatedev());
		$copyObj->setRnSignChefetab($this->getRnSignChefetab());
		$copyObj->setRnSignPp($this->getRnSignPp());
		$copyObj->setRnSignResp($this->getRnSignResp());
		$copyObj->setRnSignNblig($this->getRnSignNblig());
		$copyObj->setRnFormule($this->getRnFormule());
		$copyObj->setEctsTypeFormation($this->getEctsTypeFormation());
		$copyObj->setEctsParcours($this->getEctsParcours());
		$copyObj->setEctsCodeParcours($this->getEctsCodeParcours());
		$copyObj->setEctsDomainesEtude($this->getEctsDomainesEtude());
		$copyObj->setEctsFonctionSignataireAttestation($this->getEctsFonctionSignataireAttestation());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getPeriodeNotes() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addPeriodeNote($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJScolClassess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJScolClasses($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJGroupesClassess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJGroupesClasses($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveClasses() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveClasse($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveSaisies() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveSaisie($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJCategoriesMatieresClassess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJCategoriesMatieresClasses($relObj->copy($deepCopy));
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
	 * @return     Classe Clone of current object.
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
	 * @return     ClassePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ClassePeer();
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
		if ('PeriodeNote' == $relationName) {
			return $this->initPeriodeNotes();
		}
		if ('JScolClasses' == $relationName) {
			return $this->initJScolClassess();
		}
		if ('JGroupesClasses' == $relationName) {
			return $this->initJGroupesClassess();
		}
		if ('JEleveClasse' == $relationName) {
			return $this->initJEleveClasses();
		}
		if ('AbsenceEleveSaisie' == $relationName) {
			return $this->initAbsenceEleveSaisies();
		}
		if ('JCategoriesMatieresClasses' == $relationName) {
			return $this->initJCategoriesMatieresClassess();
		}
	}

	/**
	 * Clears out the collPeriodeNotes collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addPeriodeNotes()
	 */
	public function clearPeriodeNotes()
	{
		$this->collPeriodeNotes = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collPeriodeNotes collection.
	 *
	 * By default this just sets the collPeriodeNotes collection to an empty array (like clearcollPeriodeNotes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initPeriodeNotes($overrideExisting = true)
	{
		if (null !== $this->collPeriodeNotes && !$overrideExisting) {
			return;
		}
		$this->collPeriodeNotes = new PropelObjectCollection();
		$this->collPeriodeNotes->setModel('PeriodeNote');
	}

	/**
	 * Gets an array of PeriodeNote objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Classe is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array PeriodeNote[] List of PeriodeNote objects
	 * @throws     PropelException
	 */
	public function getPeriodeNotes($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collPeriodeNotes || null !== $criteria) {
			if ($this->isNew() && null === $this->collPeriodeNotes) {
				// return empty collection
				$this->initPeriodeNotes();
			} else {
				$collPeriodeNotes = PeriodeNoteQuery::create(null, $criteria)
					->filterByClasse($this)
					->find($con);
				if (null !== $criteria) {
					return $collPeriodeNotes;
				}
				$this->collPeriodeNotes = $collPeriodeNotes;
			}
		}
		return $this->collPeriodeNotes;
	}

	/**
	 * Sets a collection of PeriodeNote objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $periodeNotes A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setPeriodeNotes(PropelCollection $periodeNotes, PropelPDO $con = null)
	{
		$this->periodeNotesScheduledForDeletion = $this->getPeriodeNotes(new Criteria(), $con)->diff($periodeNotes);

		foreach ($periodeNotes as $periodeNote) {
			// Fix issue with collection modified by reference
			if ($periodeNote->isNew()) {
				$periodeNote->setClasse($this);
			}
			$this->addPeriodeNote($periodeNote);
		}

		$this->collPeriodeNotes = $periodeNotes;
	}

	/**
	 * Returns the number of related PeriodeNote objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related PeriodeNote objects.
	 * @throws     PropelException
	 */
	public function countPeriodeNotes(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collPeriodeNotes || null !== $criteria) {
			if ($this->isNew() && null === $this->collPeriodeNotes) {
				return 0;
			} else {
				$query = PeriodeNoteQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByClasse($this)
					->count($con);
			}
		} else {
			return count($this->collPeriodeNotes);
		}
	}

	/**
	 * Method called to associate a PeriodeNote object to this object
	 * through the PeriodeNote foreign key attribute.
	 *
	 * @param      PeriodeNote $l PeriodeNote
	 * @return     Classe The current object (for fluent API support)
	 */
	public function addPeriodeNote(PeriodeNote $l)
	{
		if ($this->collPeriodeNotes === null) {
			$this->initPeriodeNotes();
		}
		if (!$this->collPeriodeNotes->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddPeriodeNote($l);
		}

		return $this;
	}

	/**
	 * @param	PeriodeNote $periodeNote The periodeNote object to add.
	 */
	protected function doAddPeriodeNote($periodeNote)
	{
		$this->collPeriodeNotes[]= $periodeNote;
		$periodeNote->setClasse($this);
	}

	/**
	 * Clears out the collJScolClassess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJScolClassess()
	 */
	public function clearJScolClassess()
	{
		$this->collJScolClassess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJScolClassess collection.
	 *
	 * By default this just sets the collJScolClassess collection to an empty array (like clearcollJScolClassess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJScolClassess($overrideExisting = true)
	{
		if (null !== $this->collJScolClassess && !$overrideExisting) {
			return;
		}
		$this->collJScolClassess = new PropelObjectCollection();
		$this->collJScolClassess->setModel('JScolClasses');
	}

	/**
	 * Gets an array of JScolClasses objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Classe is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JScolClasses[] List of JScolClasses objects
	 * @throws     PropelException
	 */
	public function getJScolClassess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJScolClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJScolClassess) {
				// return empty collection
				$this->initJScolClassess();
			} else {
				$collJScolClassess = JScolClassesQuery::create(null, $criteria)
					->filterByClasse($this)
					->find($con);
				if (null !== $criteria) {
					return $collJScolClassess;
				}
				$this->collJScolClassess = $collJScolClassess;
			}
		}
		return $this->collJScolClassess;
	}

	/**
	 * Sets a collection of JScolClasses objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jScolClassess A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJScolClassess(PropelCollection $jScolClassess, PropelPDO $con = null)
	{
		$this->jScolClassessScheduledForDeletion = $this->getJScolClassess(new Criteria(), $con)->diff($jScolClassess);

		foreach ($jScolClassess as $jScolClasses) {
			// Fix issue with collection modified by reference
			if ($jScolClasses->isNew()) {
				$jScolClasses->setClasse($this);
			}
			$this->addJScolClasses($jScolClasses);
		}

		$this->collJScolClassess = $jScolClassess;
	}

	/**
	 * Returns the number of related JScolClasses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JScolClasses objects.
	 * @throws     PropelException
	 */
	public function countJScolClassess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJScolClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJScolClassess) {
				return 0;
			} else {
				$query = JScolClassesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByClasse($this)
					->count($con);
			}
		} else {
			return count($this->collJScolClassess);
		}
	}

	/**
	 * Method called to associate a JScolClasses object to this object
	 * through the JScolClasses foreign key attribute.
	 *
	 * @param      JScolClasses $l JScolClasses
	 * @return     Classe The current object (for fluent API support)
	 */
	public function addJScolClasses(JScolClasses $l)
	{
		if ($this->collJScolClassess === null) {
			$this->initJScolClassess();
		}
		if (!$this->collJScolClassess->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJScolClasses($l);
		}

		return $this;
	}

	/**
	 * @param	JScolClasses $jScolClasses The jScolClasses object to add.
	 */
	protected function doAddJScolClasses($jScolClasses)
	{
		$this->collJScolClassess[]= $jScolClasses;
		$jScolClasses->setClasse($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JScolClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JScolClasses[] List of JScolClasses objects
	 */
	public function getJScolClassessJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JScolClassesQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getJScolClassess($query, $con);
	}

	/**
	 * Clears out the collJGroupesClassess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJGroupesClassess()
	 */
	public function clearJGroupesClassess()
	{
		$this->collJGroupesClassess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJGroupesClassess collection.
	 *
	 * By default this just sets the collJGroupesClassess collection to an empty array (like clearcollJGroupesClassess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJGroupesClassess($overrideExisting = true)
	{
		if (null !== $this->collJGroupesClassess && !$overrideExisting) {
			return;
		}
		$this->collJGroupesClassess = new PropelObjectCollection();
		$this->collJGroupesClassess->setModel('JGroupesClasses');
	}

	/**
	 * Gets an array of JGroupesClasses objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Classe is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JGroupesClasses[] List of JGroupesClasses objects
	 * @throws     PropelException
	 */
	public function getJGroupesClassess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJGroupesClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJGroupesClassess) {
				// return empty collection
				$this->initJGroupesClassess();
			} else {
				$collJGroupesClassess = JGroupesClassesQuery::create(null, $criteria)
					->filterByClasse($this)
					->find($con);
				if (null !== $criteria) {
					return $collJGroupesClassess;
				}
				$this->collJGroupesClassess = $collJGroupesClassess;
			}
		}
		return $this->collJGroupesClassess;
	}

	/**
	 * Sets a collection of JGroupesClasses objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jGroupesClassess A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJGroupesClassess(PropelCollection $jGroupesClassess, PropelPDO $con = null)
	{
		$this->jGroupesClassessScheduledForDeletion = $this->getJGroupesClassess(new Criteria(), $con)->diff($jGroupesClassess);

		foreach ($jGroupesClassess as $jGroupesClasses) {
			// Fix issue with collection modified by reference
			if ($jGroupesClasses->isNew()) {
				$jGroupesClasses->setClasse($this);
			}
			$this->addJGroupesClasses($jGroupesClasses);
		}

		$this->collJGroupesClassess = $jGroupesClassess;
	}

	/**
	 * Returns the number of related JGroupesClasses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JGroupesClasses objects.
	 * @throws     PropelException
	 */
	public function countJGroupesClassess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJGroupesClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJGroupesClassess) {
				return 0;
			} else {
				$query = JGroupesClassesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByClasse($this)
					->count($con);
			}
		} else {
			return count($this->collJGroupesClassess);
		}
	}

	/**
	 * Method called to associate a JGroupesClasses object to this object
	 * through the JGroupesClasses foreign key attribute.
	 *
	 * @param      JGroupesClasses $l JGroupesClasses
	 * @return     Classe The current object (for fluent API support)
	 */
	public function addJGroupesClasses(JGroupesClasses $l)
	{
		if ($this->collJGroupesClassess === null) {
			$this->initJGroupesClassess();
		}
		if (!$this->collJGroupesClassess->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJGroupesClasses($l);
		}

		return $this;
	}

	/**
	 * @param	JGroupesClasses $jGroupesClasses The jGroupesClasses object to add.
	 */
	protected function doAddJGroupesClasses($jGroupesClasses)
	{
		$this->collJGroupesClassess[]= $jGroupesClasses;
		$jGroupesClasses->setClasse($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JGroupesClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JGroupesClasses[] List of JGroupesClasses objects
	 */
	public function getJGroupesClassessJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JGroupesClassesQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getJGroupesClassess($query, $con);
	}

	/**
	 * Clears out the collJEleveClasses collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJEleveClasses()
	 */
	public function clearJEleveClasses()
	{
		$this->collJEleveClasses = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJEleveClasses collection.
	 *
	 * By default this just sets the collJEleveClasses collection to an empty array (like clearcollJEleveClasses());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJEleveClasses($overrideExisting = true)
	{
		if (null !== $this->collJEleveClasses && !$overrideExisting) {
			return;
		}
		$this->collJEleveClasses = new PropelObjectCollection();
		$this->collJEleveClasses->setModel('JEleveClasse');
	}

	/**
	 * Gets an array of JEleveClasse objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Classe is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JEleveClasse[] List of JEleveClasse objects
	 * @throws     PropelException
	 */
	public function getJEleveClasses($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJEleveClasses || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveClasses) {
				// return empty collection
				$this->initJEleveClasses();
			} else {
				$collJEleveClasses = JEleveClasseQuery::create(null, $criteria)
					->filterByClasse($this)
					->find($con);
				if (null !== $criteria) {
					return $collJEleveClasses;
				}
				$this->collJEleveClasses = $collJEleveClasses;
			}
		}
		return $this->collJEleveClasses;
	}

	/**
	 * Sets a collection of JEleveClasse objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jEleveClasses A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJEleveClasses(PropelCollection $jEleveClasses, PropelPDO $con = null)
	{
		$this->jEleveClassesScheduledForDeletion = $this->getJEleveClasses(new Criteria(), $con)->diff($jEleveClasses);

		foreach ($jEleveClasses as $jEleveClasse) {
			// Fix issue with collection modified by reference
			if ($jEleveClasse->isNew()) {
				$jEleveClasse->setClasse($this);
			}
			$this->addJEleveClasse($jEleveClasse);
		}

		$this->collJEleveClasses = $jEleveClasses;
	}

	/**
	 * Returns the number of related JEleveClasse objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JEleveClasse objects.
	 * @throws     PropelException
	 */
	public function countJEleveClasses(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJEleveClasses || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveClasses) {
				return 0;
			} else {
				$query = JEleveClasseQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByClasse($this)
					->count($con);
			}
		} else {
			return count($this->collJEleveClasses);
		}
	}

	/**
	 * Method called to associate a JEleveClasse object to this object
	 * through the JEleveClasse foreign key attribute.
	 *
	 * @param      JEleveClasse $l JEleveClasse
	 * @return     Classe The current object (for fluent API support)
	 */
	public function addJEleveClasse(JEleveClasse $l)
	{
		if ($this->collJEleveClasses === null) {
			$this->initJEleveClasses();
		}
		if (!$this->collJEleveClasses->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJEleveClasse($l);
		}

		return $this;
	}

	/**
	 * @param	JEleveClasse $jEleveClasse The jEleveClasse object to add.
	 */
	protected function doAddJEleveClasse($jEleveClasse)
	{
		$this->collJEleveClasses[]= $jEleveClasse;
		$jEleveClasse->setClasse($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JEleveClasses from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveClasse[] List of JEleveClasse objects
	 */
	public function getJEleveClassesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveClasseQuery::create(null, $criteria);
		$query->joinWith('Eleve', $join_behavior);

		return $this->getJEleveClasses($query, $con);
	}

	/**
	 * Clears out the collAbsenceEleveSaisies collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveSaisies()
	 */
	public function clearAbsenceEleveSaisies()
	{
		$this->collAbsenceEleveSaisies = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveSaisies collection.
	 *
	 * By default this just sets the collAbsenceEleveSaisies collection to an empty array (like clearcollAbsenceEleveSaisies());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAbsenceEleveSaisies($overrideExisting = true)
	{
		if (null !== $this->collAbsenceEleveSaisies && !$overrideExisting) {
			return;
		}
		$this->collAbsenceEleveSaisies = new PropelObjectCollection();
		$this->collAbsenceEleveSaisies->setModel('AbsenceEleveSaisie');
	}

	/**
	 * Gets an array of AbsenceEleveSaisie objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Classe is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 * @throws     PropelException
	 */
	public function getAbsenceEleveSaisies($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveSaisies || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveSaisies) {
				// return empty collection
				$this->initAbsenceEleveSaisies();
			} else {
				$collAbsenceEleveSaisies = AbsenceEleveSaisieQuery::create(null, $criteria)
					->filterByClasse($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceEleveSaisies;
				}
				$this->collAbsenceEleveSaisies = $collAbsenceEleveSaisies;
			}
		}
		return $this->collAbsenceEleveSaisies;
	}

	/**
	 * Sets a collection of AbsenceEleveSaisie objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $absenceEleveSaisies A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAbsenceEleveSaisies(PropelCollection $absenceEleveSaisies, PropelPDO $con = null)
	{
		$this->absenceEleveSaisiesScheduledForDeletion = $this->getAbsenceEleveSaisies(new Criteria(), $con)->diff($absenceEleveSaisies);

		foreach ($absenceEleveSaisies as $absenceEleveSaisie) {
			// Fix issue with collection modified by reference
			if ($absenceEleveSaisie->isNew()) {
				$absenceEleveSaisie->setClasse($this);
			}
			$this->addAbsenceEleveSaisie($absenceEleveSaisie);
		}

		$this->collAbsenceEleveSaisies = $absenceEleveSaisies;
	}

	/**
	 * Returns the number of related AbsenceEleveSaisie objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveSaisie objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveSaisies(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveSaisies || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveSaisies) {
				return 0;
			} else {
				$query = AbsenceEleveSaisieQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByClasse($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveSaisies);
		}
	}

	/**
	 * Method called to associate a AbsenceEleveSaisie object to this object
	 * through the AbsenceEleveSaisie foreign key attribute.
	 *
	 * @param      AbsenceEleveSaisie $l AbsenceEleveSaisie
	 * @return     Classe The current object (for fluent API support)
	 */
	public function addAbsenceEleveSaisie(AbsenceEleveSaisie $l)
	{
		if ($this->collAbsenceEleveSaisies === null) {
			$this->initAbsenceEleveSaisies();
		}
		if (!$this->collAbsenceEleveSaisies->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddAbsenceEleveSaisie($l);
		}

		return $this;
	}

	/**
	 * @param	AbsenceEleveSaisie $absenceEleveSaisie The absenceEleveSaisie object to add.
	 */
	protected function doAddAbsenceEleveSaisie($absenceEleveSaisie)
	{
		$this->collAbsenceEleveSaisies[]= $absenceEleveSaisie;
		$absenceEleveSaisie->setClasse($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('Eleve', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinEdtCreneau($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('EdtCreneau', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinEdtEmplacementCours($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('EdtEmplacementCours', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinAidDetails($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('AidDetails', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinAbsenceEleveLieu($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveLieu', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}

	/**
	 * Clears out the collJCategoriesMatieresClassess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJCategoriesMatieresClassess()
	 */
	public function clearJCategoriesMatieresClassess()
	{
		$this->collJCategoriesMatieresClassess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJCategoriesMatieresClassess collection.
	 *
	 * By default this just sets the collJCategoriesMatieresClassess collection to an empty array (like clearcollJCategoriesMatieresClassess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJCategoriesMatieresClassess($overrideExisting = true)
	{
		if (null !== $this->collJCategoriesMatieresClassess && !$overrideExisting) {
			return;
		}
		$this->collJCategoriesMatieresClassess = new PropelObjectCollection();
		$this->collJCategoriesMatieresClassess->setModel('JCategoriesMatieresClasses');
	}

	/**
	 * Gets an array of JCategoriesMatieresClasses objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Classe is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JCategoriesMatieresClasses[] List of JCategoriesMatieresClasses objects
	 * @throws     PropelException
	 */
	public function getJCategoriesMatieresClassess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJCategoriesMatieresClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJCategoriesMatieresClassess) {
				// return empty collection
				$this->initJCategoriesMatieresClassess();
			} else {
				$collJCategoriesMatieresClassess = JCategoriesMatieresClassesQuery::create(null, $criteria)
					->filterByClasse($this)
					->find($con);
				if (null !== $criteria) {
					return $collJCategoriesMatieresClassess;
				}
				$this->collJCategoriesMatieresClassess = $collJCategoriesMatieresClassess;
			}
		}
		return $this->collJCategoriesMatieresClassess;
	}

	/**
	 * Sets a collection of JCategoriesMatieresClasses objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jCategoriesMatieresClassess A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJCategoriesMatieresClassess(PropelCollection $jCategoriesMatieresClassess, PropelPDO $con = null)
	{
		$this->jCategoriesMatieresClassessScheduledForDeletion = $this->getJCategoriesMatieresClassess(new Criteria(), $con)->diff($jCategoriesMatieresClassess);

		foreach ($jCategoriesMatieresClassess as $jCategoriesMatieresClasses) {
			// Fix issue with collection modified by reference
			if ($jCategoriesMatieresClasses->isNew()) {
				$jCategoriesMatieresClasses->setClasse($this);
			}
			$this->addJCategoriesMatieresClasses($jCategoriesMatieresClasses);
		}

		$this->collJCategoriesMatieresClassess = $jCategoriesMatieresClassess;
	}

	/**
	 * Returns the number of related JCategoriesMatieresClasses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JCategoriesMatieresClasses objects.
	 * @throws     PropelException
	 */
	public function countJCategoriesMatieresClassess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJCategoriesMatieresClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJCategoriesMatieresClassess) {
				return 0;
			} else {
				$query = JCategoriesMatieresClassesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByClasse($this)
					->count($con);
			}
		} else {
			return count($this->collJCategoriesMatieresClassess);
		}
	}

	/**
	 * Method called to associate a JCategoriesMatieresClasses object to this object
	 * through the JCategoriesMatieresClasses foreign key attribute.
	 *
	 * @param      JCategoriesMatieresClasses $l JCategoriesMatieresClasses
	 * @return     Classe The current object (for fluent API support)
	 */
	public function addJCategoriesMatieresClasses(JCategoriesMatieresClasses $l)
	{
		if ($this->collJCategoriesMatieresClassess === null) {
			$this->initJCategoriesMatieresClassess();
		}
		if (!$this->collJCategoriesMatieresClassess->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJCategoriesMatieresClasses($l);
		}

		return $this;
	}

	/**
	 * @param	JCategoriesMatieresClasses $jCategoriesMatieresClasses The jCategoriesMatieresClasses object to add.
	 */
	protected function doAddJCategoriesMatieresClasses($jCategoriesMatieresClasses)
	{
		$this->collJCategoriesMatieresClassess[]= $jCategoriesMatieresClasses;
		$jCategoriesMatieresClasses->setClasse($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JCategoriesMatieresClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JCategoriesMatieresClasses[] List of JCategoriesMatieresClasses objects
	 */
	public function getJCategoriesMatieresClassessJoinCategorieMatiere($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JCategoriesMatieresClassesQuery::create(null, $criteria);
		$query->joinWith('CategorieMatiere', $join_behavior);

		return $this->getJCategoriesMatieresClassess($query, $con);
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
	 * to the current object by way of the j_groupes_classes cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Classe is new, it will return
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
					->filterByClasse($this)
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
	 * to the current object by way of the j_groupes_classes cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $groupes A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setGroupes(PropelCollection $groupes, PropelPDO $con = null)
	{
		$jGroupesClassess = JGroupesClassesQuery::create()
			->filterByGroupe($groupes)
			->filterByClasse($this)
			->find($con);

		$this->groupesScheduledForDeletion = $this->getJGroupesClassess()->diff($jGroupesClassess);
		$this->collJGroupesClassess = $jGroupesClassess;

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
	 * to the current object by way of the j_groupes_classes cross-reference table.
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
					->filterByClasse($this)
					->count($con);
			}
		} else {
			return count($this->collGroupes);
		}
	}

	/**
	 * Associate a Groupe object to this object
	 * through the j_groupes_classes cross reference table.
	 *
	 * @param      Groupe $groupe The JGroupesClasses object to relate
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
		$jGroupesClasses = new JGroupesClasses();
		$jGroupesClasses->setGroupe($groupe);
		$this->addJGroupesClasses($jGroupesClasses);
	}

	/**
	 * Clears out the collCategorieMatieres collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCategorieMatieres()
	 */
	public function clearCategorieMatieres()
	{
		$this->collCategorieMatieres = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCategorieMatieres collection.
	 *
	 * By default this just sets the collCategorieMatieres collection to an empty collection (like clearCategorieMatieres());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCategorieMatieres()
	{
		$this->collCategorieMatieres = new PropelObjectCollection();
		$this->collCategorieMatieres->setModel('CategorieMatiere');
	}

	/**
	 * Gets a collection of CategorieMatiere objects related by a many-to-many relationship
	 * to the current object by way of the j_matieres_categories_classes cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Classe is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array CategorieMatiere[] List of CategorieMatiere objects
	 */
	public function getCategorieMatieres($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCategorieMatieres || null !== $criteria) {
			if ($this->isNew() && null === $this->collCategorieMatieres) {
				// return empty collection
				$this->initCategorieMatieres();
			} else {
				$collCategorieMatieres = CategorieMatiereQuery::create(null, $criteria)
					->filterByClasse($this)
					->find($con);
				if (null !== $criteria) {
					return $collCategorieMatieres;
				}
				$this->collCategorieMatieres = $collCategorieMatieres;
			}
		}
		return $this->collCategorieMatieres;
	}

	/**
	 * Sets a collection of CategorieMatiere objects related by a many-to-many relationship
	 * to the current object by way of the j_matieres_categories_classes cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $categorieMatieres A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCategorieMatieres(PropelCollection $categorieMatieres, PropelPDO $con = null)
	{
		$jCategoriesMatieresClassess = JCategoriesMatieresClassesQuery::create()
			->filterByCategorieMatiere($categorieMatieres)
			->filterByClasse($this)
			->find($con);

		$this->categorieMatieresScheduledForDeletion = $this->getJCategoriesMatieresClassess()->diff($jCategoriesMatieresClassess);
		$this->collJCategoriesMatieresClassess = $jCategoriesMatieresClassess;

		foreach ($categorieMatieres as $categorieMatiere) {
			// Fix issue with collection modified by reference
			if ($categorieMatiere->isNew()) {
				$this->doAddCategorieMatiere($categorieMatiere);
			} else {
				$this->addCategorieMatiere($categorieMatiere);
			}
		}

		$this->collCategorieMatieres = $categorieMatieres;
	}

	/**
	 * Gets the number of CategorieMatiere objects related by a many-to-many relationship
	 * to the current object by way of the j_matieres_categories_classes cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related CategorieMatiere objects
	 */
	public function countCategorieMatieres($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCategorieMatieres || null !== $criteria) {
			if ($this->isNew() && null === $this->collCategorieMatieres) {
				return 0;
			} else {
				$query = CategorieMatiereQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByClasse($this)
					->count($con);
			}
		} else {
			return count($this->collCategorieMatieres);
		}
	}

	/**
	 * Associate a CategorieMatiere object to this object
	 * through the j_matieres_categories_classes cross reference table.
	 *
	 * @param      CategorieMatiere $categorieMatiere The JCategoriesMatieresClasses object to relate
	 * @return     void
	 */
	public function addCategorieMatiere(CategorieMatiere $categorieMatiere)
	{
		if ($this->collCategorieMatieres === null) {
			$this->initCategorieMatieres();
		}
		if (!$this->collCategorieMatieres->contains($categorieMatiere)) { // only add it if the **same** object is not already associated
			$this->doAddCategorieMatiere($categorieMatiere);

			$this->collCategorieMatieres[]= $categorieMatiere;
		}
	}

	/**
	 * @param	CategorieMatiere $categorieMatiere The categorieMatiere object to add.
	 */
	protected function doAddCategorieMatiere($categorieMatiere)
	{
		$jCategoriesMatieresClasses = new JCategoriesMatieresClasses();
		$jCategoriesMatieresClasses->setCategorieMatiere($categorieMatiere);
		$this->addJCategoriesMatieresClasses($jCategoriesMatieresClasses);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->classe = null;
		$this->nom_complet = null;
		$this->suivi_par = null;
		$this->formule = null;
		$this->format_nom = null;
		$this->display_rang = null;
		$this->display_address = null;
		$this->display_coef = null;
		$this->display_mat_cat = null;
		$this->display_nbdev = null;
		$this->display_moy_gen = null;
		$this->modele_bulletin_pdf = null;
		$this->rn_nomdev = null;
		$this->rn_toutcoefdev = null;
		$this->rn_coefdev_si_diff = null;
		$this->rn_datedev = null;
		$this->rn_sign_chefetab = null;
		$this->rn_sign_pp = null;
		$this->rn_sign_resp = null;
		$this->rn_sign_nblig = null;
		$this->rn_formule = null;
		$this->ects_type_formation = null;
		$this->ects_parcours = null;
		$this->ects_code_parcours = null;
		$this->ects_domaines_etude = null;
		$this->ects_fonction_signataire_attestation = null;
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
			if ($this->collPeriodeNotes) {
				foreach ($this->collPeriodeNotes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJScolClassess) {
				foreach ($this->collJScolClassess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJGroupesClassess) {
				foreach ($this->collJGroupesClassess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveClasses) {
				foreach ($this->collJEleveClasses as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveSaisies) {
				foreach ($this->collAbsenceEleveSaisies as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJCategoriesMatieresClassess) {
				foreach ($this->collJCategoriesMatieresClassess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collGroupes) {
				foreach ($this->collGroupes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCategorieMatieres) {
				foreach ($this->collCategorieMatieres as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collPeriodeNotes instanceof PropelCollection) {
			$this->collPeriodeNotes->clearIterator();
		}
		$this->collPeriodeNotes = null;
		if ($this->collJScolClassess instanceof PropelCollection) {
			$this->collJScolClassess->clearIterator();
		}
		$this->collJScolClassess = null;
		if ($this->collJGroupesClassess instanceof PropelCollection) {
			$this->collJGroupesClassess->clearIterator();
		}
		$this->collJGroupesClassess = null;
		if ($this->collJEleveClasses instanceof PropelCollection) {
			$this->collJEleveClasses->clearIterator();
		}
		$this->collJEleveClasses = null;
		if ($this->collAbsenceEleveSaisies instanceof PropelCollection) {
			$this->collAbsenceEleveSaisies->clearIterator();
		}
		$this->collAbsenceEleveSaisies = null;
		if ($this->collJCategoriesMatieresClassess instanceof PropelCollection) {
			$this->collJCategoriesMatieresClassess->clearIterator();
		}
		$this->collJCategoriesMatieresClassess = null;
		if ($this->collGroupes instanceof PropelCollection) {
			$this->collGroupes->clearIterator();
		}
		$this->collGroupes = null;
		if ($this->collCategorieMatieres instanceof PropelCollection) {
			$this->collCategorieMatieres->clearIterator();
		}
		$this->collCategorieMatieres = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(ClassePeer::DEFAULT_STRING_FORMAT);
	}

} // BaseClasse
