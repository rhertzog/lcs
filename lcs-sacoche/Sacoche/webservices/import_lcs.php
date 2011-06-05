<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Module lcs-sacoche basé sur 
   ----------------------------------------------------------------------------------
   SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences 
   par Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
   ----------------------------------------------------------------------------------
   par philippe LECLERC [philippe.leclerc1@ac-caen.fr]
   modifié par Thomas CRESPIN [thomas.crespin@sesamath.net]
   - script d'association user_Sacoche <-> user_LCS  via CAS -
   version 2.1 du 18/11/2010
            _-=-_
   =================================================== */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}

/**
 * recuperer_infos_user_LCS
 * Appel au LCS permettant de récupérer l'uid d'un user.
 * Dans le LDAP employeeNumber correspond :
 * -> pour un élève à son Elenoet (ELEVE.ELENOET dans Sconet) éventuellement complété par des 0 à gauche
 * -> pour un prof à son id Sconet (INDIVIDU.ID dans Sconet) préfixé par la lettre P
 * 
 * @param string   $user_profil   'eleve' ou 'professeur' (pas 'directeur' car ils n'ont pas d'employeeNumber dans le LCS)
 * @param int      $user_sconet_elenoet
 * @param int      $user_sconet_id
 * @return array   ($code_erreur,$tab_valeurs_retournees)
 */

function recuperer_infos_user_LCS($user_profil,$user_sconet_elenoet,$user_sconet_id)
{
	// Fabrication de l'employeeNumber à partir du numéro Sconet "Elenoet" (il faut compléter devant avec des 0 pour les élèves, et ajouter un P pour les profs).
	$employeeNumber = ($user_profil=='eleve') ? sprintf("%05s",$user_sconet_elenoet) : 'P'.$user_sconet_id ;
	// On interroge le LDAP.
	$commande = 'ldapsearch -x -LLL employeeNumber='.$employeeNumber.' | grep "uid:" | cut -d " " -f2';
	exec($commande,$tab_valeurs_retournees,$code_erreur);
	// On retourne le résultat.
	return array($code_erreur,$tab_valeurs_retournees);
}

?>