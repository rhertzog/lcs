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
   - script d'association user_Sacoche <-> user_LCS  via CAS -
   version 1.0 du 15/09/2010
			_-=-_
   =================================================== */
if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}

// Ouvrir la connexion et selectionner la base de donnees
	$db_ag = @mysql_connect (SACOCHE_STRUCTURE_BD_HOST, SACOCHE_STRUCTURE_BD_USER, SACOCHE_STRUCTURE_BD_PASS,SACOCHE_STRUCTURE_BD_NAME) 
	       OR die ('Connexion a MySQL impossible : '.mysql_error().'<br>');
	mysql_select_db (SACOCHE_STRUCTURE_BD_NAME)
	       OR die ('Selection de la base de donnees impossible : '.mysql_error().'<br>');      
	
//On parcours la table utilisateurs Sacoche
	
	$Sql= "SELECT user_id, user_num_sconet, user_profil, user_nom, user_prenom  FROM sacoche_user WHERE user_profil != 'administrateur' ";
	$res = @mysql_query ($Sql) or die (mysql_error());
	if (mysql_num_rows($res)>0) 
		$loop=0;$cpt=0;
		while ($enrg = mysql_fetch_array($res, MYSQL_NUM)) 
			{
			//on recherche le login LCS à partir de l'employeenumber
			if ($enrg[2]=="eleve") $empN="0".$enrg[1];
			elseif ($enrg[2]=="professeur") $empN="P".$enrg[1];
			$valeur_retournee=array();
			$cmd= 'ldapsearch -x -LLL employeeNumber='.$empN.' | grep "uid:" | cut -d " " -f2';
			exec ($cmd, $valeur_retournee,$code_retour);
			if ( !$code_retour && count($valeur_retournee)==1)
				{
				$cpt++;
				$Sql = "UPDATE `sacoche_user` SET `user_id_ent` = '$valeur_retournee[0]' WHERE user_id = $enrg[0]";
				$res1 = @mysql_query ($Sql) or die (mysql_error());
				}
			else 
				{
				$pb[$loop]=$enrg[3]." ".$enrg[4];
				$loop++;
				}
			}	 
	//Compte-rendu
	if ($loop>0) 
	  {
	  echo "Echec de l'association utilisateur Sacoche <-> utilisateur LCS pour : <br /> <br />";
	  for($i = 0; $i < $loop; $i++) 
	  echo "- ".$pb[$i]. "<br/>";
	  } 
	 else echo "ok";
	
?>