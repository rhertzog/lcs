<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Module lcs-sacoche basé sur 
   ----------------------------------------------------------------------------------
   SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences 
   par Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
   ----------------------------------------------------------------------------------
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   modifié par Thomas CRESPIN
   thomas.crespin@sesamath.net
   - script d'association user_Sacoche <-> user_LCS  via CAS -
   version 2.0 du 18/10/2010
            _-=-_
   =================================================== */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}

/**
 * recuperer_infos_user_LCS_by_NumSconet
 * Appel au LCS proposé par Philippe LECLERC permettant de récupérer l'uid d'un user à partir de son numéro Sconet.
 * 
 * @param string   $user_profil   'eleve' ou 'professeur' (pas 'directeur' car ils n'ont pas d'employeeNumber dans le LCS)
 * @param int      $user_num_sconet
 * @return array   ($code_erreur,$tab_valeurs_retournees)
 */

function recuperer_infos_user_LCS_by_NumSconet($user_profil,$user_num_sconet)
{
	$employeeNumber = ($user_profil=='eleve') ? '0'.$user_num_sconet : 'P'.$user_num_sconet ;
	$commande = 'ldapsearch -x -LLL employeeNumber='.$employeeNumber.' | grep "uid:" | cut -d " " -f2';
	exec($commande,$tab_valeurs_retournees,$code_erreur);
	return array($code_erreur,$tab_valeurs_retournees);
}

?>