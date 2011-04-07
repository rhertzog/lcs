<?php

/**
 * Description of PeriodeNoteHelper
 *  Classe qui implemente des methodes statiques pour g�r� es periode de notes
 *
 * @author joss
 */
class PeriodeNoteHelper {
 
 	/**
	 * 
	 * Classe un tableau de groupe par ordre alphab�tique de leur nom (avec les noms de classes d'eleves associ�e)
	 *
	 * @param      array $groupes Le tableau de groupes
	 * @return     array $groupes Un tableau de groupe ordonn�s
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public static function getNumPeriode($periode) {
		if ($periode === null || $periode === '') {
			return null;
		} elseif ($periode instanceof PeriodeNote) {
			return $periode->getNumPeriode();
		} else {
			return $periode;
		}
	}
}
?>