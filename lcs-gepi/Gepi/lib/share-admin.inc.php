<?php

/**
 * Fonctions utiles uniquement pour l'administrateur
 * 
 * $Id $
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Initialisation
 * @subpackage general
 */

/**
 * V�rifie que le dossier photos est bien configur� pour le multisite
 * 
 * Cr�e au besoin les dossiers 
 * - photos/xxxRNExxx
 * - photos/xxxRNExxx/eleves
 * - photos/xxxRNExxx/personnels
 * 
 * @return boolean TRUE si les dossiers existent
 */
function check_photos_multisite(&$messageErreur) {
  /* On r�cup�re le r�pertoire courant */
  $courantDir= dirname(dirname(__FILE__));
  $dirEtablissement=$courantDir.'/photos/'.$_COOKIE['RNE'];
  $dirEleve=$dirEtablissement.'/eleves';
  $dirPersonnels=$dirEtablissement.'/personnels';
  
  /* V�rifier si photos/RNE existe */    
  if (!is_dir($dirEtablissement)) { 
    /* cr�er photos/RNE au besoin */
    if (!mkdir($dirEtablissement,0770)) {
      $messageErreur = "Echec de la cr�ation du dossier $dirEtablissement, v�rifier les droits sur le dossier photos";
      return FALSE;
    } 
  }
  /* V�rifier si photos/RNE est prot�g� */
  if (!is_file($dirEtablissement.'/index.html')) { 
    /* prot�ger le dossier en copiant /lib/index.html dedans */
    if (!copy($courantDir.'/lib/index.html',$dirEtablissement.'/index.html' )){
      $messageErreur = "Echec lors de l'�criture dans le dossier $dirEtablissement, v�rifier les droits sur le dossier $dirEtablissement";
      return FALSE;
    }
  }   
  
  /* V�rifier si photos/RNE/eleves existe */
  if (!is_dir($dirEleve)) {
    /* cr�er photos/RNE/eleves au besoin */
    if (!mkdir($dirEleve,0770))  {
      $messageErreur = "Echec de la cr�ation du dossier $dirEleve, v�rifier les droits sur le dossier $dirEtablissement";
      return FALSE;
    }  
  }
  /* V�rifier si photos/RNE/eleves est prot�g� */
  if (!is_file($dirEleve.'/index.html')) { 
    /* prot�ger le dossier en copiant /lib/index.html dedans */
    if (!copy($courantDir.'/lib/index.html',$dirEleve.'/index.html' )){
      $messageErreur = "Echec lors de l'�criture dans le dossier $dirEleve, v�rifier les droits sur le dossier $dirEleve";
      return FALSE;
    }
  } 
  
  /* V�rifier si photos/RNE/personnels existe */
  if (!is_dir($dirPersonnels)) {
    /* cr�er photos/RNE/personnels au besoin */
    if (!mkdir($dirPersonnels,0770))  {
      $messageErreur = "Echec de la cr�ation du dossier $dirPersonnels, v�rifier les droits sur le dossier $dirEtablissement";
      return FALSE;
    }  
  }
  /* V�rifier si photos/RNE/personnels est prot�g� */
  if (!is_file($dirPersonnels.'/index.html')) { 
    /* prot�ger le dossier en copiant /lib/index.html dedans */
    if (!copy($courantDir.'/lib/index.html',$dirPersonnels.'/index.html' )){
      $messageErreur = "Echec lors de l'�criture dans le dossier $dirPersonnels, v�rifier les droits sur le dossier $dirPersonnels";
      return FALSE;
    }
  } 
  
  return TRUE;
} 





?>
