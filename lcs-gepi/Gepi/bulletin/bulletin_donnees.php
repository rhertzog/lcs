<?php

/**
 *
 *
 * @version $Id: bulletin_donnees.php 1499 2008-02-12 22:09:24Z jjocal $
 * @copyright 2008
 */

// Donn�es relatives � l'�tablissement
	$gepiSchoolName=getSettingValue("gepiSchoolName");
	$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1");
	$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2");
	$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode");
	$gepiSchoolCity=getSettingValue("gepiSchoolCity");
	$RneEtablissement=getSettingValue("gepiSchoolRne");

// Donn�es relatives au temps
	$gepiYear = getSettingValue('gepiYear');

// Donn�es relatives � l'en-t�te
	// le genre de la p�riode (trimestre, p�riode, semestre,...)
	if(getSettingValue("genre_periode")){
		$genre_periode=getSettingValue("genre_periode");
	}
	else{
		$genre_periode="M";
	}
	// Faire apparaitre le nom de l'�tablissement

	// Faire apparaitre le tel, fax.
	$gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
	$gepiSchoolTel = getSettingValue("gepiSchoolTel");
	$gepiSchoolFax = getSettingValue("gepiSchoolFax");

	/*
	if(!getSettingValue("bull_affiche_tel")){
		$bull_affiche_tel="n";
	}else{
		$bull_affiche_tel=getSettingValue("bull_affiche_tel");
	}
	if(!getSettingValue("bull_affiche_fax")){
		$bull_affiche_fax = "n";
	}else{
		$bull_affiche_fax = getSettingValue("bull_affiche_fax");
	}
	if($bull_affiche_fax=="y"){

	}
	if($bull_affiche_tel=="y"){
		$gepiSchoolTel = getSettingValue("gepiSchoolTel");
	}
	*/
?>