<?php

/**
 * Description of GroupeHelper
 *  Classe qui implemente des methodes statiques pour g�r� un groupe ou un tableau de groupe
 *
 * @author joss
 */
class GroupeHelper {
 
 	/**
	 * Compare deux groupes par ordre alphab�tique de leur nom (avec les noms de classes d'eleves associ�e)
	 *
	 * @param      array $groupeA Le premier groupe a coparer
	 * @param      array $groupeB Le deuxieme groupe a comparer
	 * @return     int un entier, qui sera inf�rieur, �gal ou sup�rieur � z�ro suivant que le premier argument est consid�r� comme plus petit, �gal ou plus grand que le second argument.
	 */
	public static function compareGroupe($a, $b) {
		//echo($a->getDescriptionAvecClasses());
		return strcmp($a->getNameAvecClasses(), $b->getNameAvecClasses());
	}

 	/**
	 * 
	 * Classe un tableau de groupe par ordre alphab�tique de leur nom (avec les noms de classes d'eleves associ�e)
	 *
	 * @param      array $groupes Le tableau de groupes
	 * @return     array $groupes Un tableau de groupe ordonn�s
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public static function orderByGroupNameWithClasses(PropelObjectCollection $groupes) {
		$groupes->uasort(array("GroupeHelper", "compareGroupe"));
		return $groupes;
	}
}
?>