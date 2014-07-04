<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010-2014
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre Affero GPL 3 <https://www.gnu.org/licenses/agpl-3.0.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU Affero General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Publique Générale GNU Affero pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Publique Générale GNU Affero avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2013-12-15 => 2014-01-07
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2013-12-15')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-01-07';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // ajout de paramètre
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_pages_nb" , "optimise" )' );
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-01-07 => 2014-01-14
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-01-07')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-01-14';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // ajout de paramètres
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "date_last_import_professeurs" , "0000-00-00" )' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "date_last_import_eleves"      , "0000-00-00" )' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "date_last_import_parents"     , "0000-00-00" )' );
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-01-14 => 2014-01-20
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-01-14')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-01-20';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // modification sacoche_parametre (paramètres CAS pour ENT Cartable de Savoie)
    $connexion_nom = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="connexion_nom"' );
    if($connexion_nom=='cartabledesavoie')
    {
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="www.cartabledesavoie.com" WHERE parametre_nom="cas_serveur_host" ' );
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-01-20 => 2014-01-31
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-01-20')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-01-31';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // suppression de la Note de Vie Scolaire des fiches brevet
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_brevet_epreuve WHERE brevet_epreuve_code=112 ' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_brevet_saisie ' ); // retirer seulement brevet_epreuve_code IN(112,255) ne suffit pas
    // matière renommée
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere SET matiere_nom="7 Autonomie et initiative" WHERE matiere_id=9908 ' );
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-01-31 => 2014-02-07
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-01-31')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-02-07';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // valeur renommée dans sacoche_niveau
    if(empty($reload_sacoche_niveau))
    {
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET niveau_nom="Classe pour l\'inclusion scolaire" WHERE niveau_id=21' );
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-02-07 => 2014-02-11
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-02-07')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-02-11';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // niveau ajouté
    if(empty($reload_sacoche_niveau))
    {
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 6, 0, 1, 68, "P3S", "", "Cycle SEGPA") ' );
    }
    // table sacoche_demande modifiée
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_demande CHANGE user_id eleve_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 ' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_demande ADD prof_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "Dans le cas où l\'élève adresse sa demande à un prof donné." AFTER item_id ' );
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-02-11 => 2014-02-12
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-02-11')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-02-12';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // Intégration des spécialités de baccalauréat professionnel comme nouvelles matières.
    if(empty($reload_sacoche_matiere_famille))
    {
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere_famille VALUES ( 97, 4, "Spécialités de baccalauréat professionnel")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere_famille SET matiere_famille_categorie=4 WHERE matiere_famille_id=98 ' );
    }
    if(empty($reload_sacoche_matiere))
    {
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9701, 0, 0,  97, 0, 255, "BPASS", "Accompagnement soins et services à la personne")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9702, 0, 0,  97, 0, 255, "BPARC", "Accueil - relation clients et usagers")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9703, 0, 0,  97, 0, 255, "BPAOA", "Aéronautique - option mécanicien systèmes avionique")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9704, 0, 0,  97, 0, 255, "BPAOC", "Aéronautique - option mécanicien système cellule")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9705, 0, 0,  97, 0, 255, "BPA3O", "Aéronautique à trois options (avionique - syst. et structures)")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9706, 0, 0,  97, 0, 255, "BPAEA", "Agencement de l\'espace architectural")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9707, 0, 0,  97, 0, 255, "BPAFB", "Aménagement et finition du bâtiment")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9708, 0, 0,  97, 0, 255, "BPAMP", "Artisanat et métiers d\'art - option arts de la pierre")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9709, 0, 0,  97, 0, 255, "BPAMC", "Artisanat et métiers d\'art - option comm. visuelle pluri média")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9710, 0, 0,  97, 0, 255, "BPAME", "Artisanat et métiers d\'art - option ébéniste")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9711, 0, 0,  97, 0, 255, "BPAMM", "Artisanat et métiers d\'art - option marchandisage visuel")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9712, 0, 0,  97, 0, 255, "BPAMT", "Artisanat et métiers d\'art - option tapissier d\'ameublement")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9713, 0, 0,  97, 0, 255, "BPAMV", "Artisanat et métiers d\'art - verrerie + enseigne / signalétique")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9714, 0, 0,  97, 0, 255, "BPAG" , "Aviation générale")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9715, 0, 0,  97, 0, 255, "BPBIT", "Bio-industries de transformation")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9716, 0, 0,  97, 0, 255, "BPBP" , "Boulanger - pâtissier")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9717, 0, 0,  97, 0, 255, "BPBCT", "Boucher charcutier traiteur")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9718, 0, 0,  97, 0, 255, "BPCOM", "Commerce")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9719, 0, 0,  97, 0, 255, "BPCSR", "Commercialisation et services en restauration")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9720, 0, 0,  97, 0, 255, "BPCPT", "Comptabilité")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9721, 0, 0,  97, 0, 255, "BPCTR", "Conducteur transport routier marchandises")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9722, 0, 0,  97, 0, 255, "BPCR" , "Construction des carrosseries")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9723, 0, 0,  97, 0, 255, "BPCUI", "Cuisine")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9724, 0, 0,  97, 0, 255, "BPCM" , "Cultures marines")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9725, 0, 0,  97, 0, 255, "BPEEE", "Électrotechnique, énergie, équipement communicants")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9726, 0, 0,  97, 0, 255, "BPEN" , "Environnement nucléaire")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9727, 0, 0,  97, 0, 255, "BPECP", "Esthétique cosmétique parfumerie")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9728, 0, 0,  97, 0, 255, "BPEPI", "Étude et définition de produits industriels")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9729, 0, 0,  97, 0, 255, "BPFPI", "Façonnage de produits imprimés, routage")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9730, 0, 0,  97, 0, 255, "BPFON", "Fonderie")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9731, 0, 0,  97, 0, 255, "BPGA" , "Gestion-administration")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9732, 0, 0,  97, 0, 255, "BPGP" , "Gestion des pollutions et protection de l\'environnement")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9733, 0, 0,  97, 0, 255, "BPHE" , "Hygiène et environnement")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9734, 0, 0,  97, 0, 255, "BPHPS", "Hygiène, propreté et stérilisation")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9735, 0, 0,  97, 0, 255, "BPIP" , "Industrie de procédés")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9736, 0, 0,  97, 0, 255, "BPIPC", "Industries des pates, papiers et cartons")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9737, 0, 0,  97, 0, 255, "BPIPB", "Interventions sur le patrimoine bâti")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9738, 0, 0,  97, 0, 255, "BPLOG", "Logistique")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9739, 0, 0,  97, 0, 255, "BPMVA", "Maintenance de véhicules automobiles")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9740, 0, 0,  97, 0, 255, "BPMEI", "Maintenance des équipements industriels")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9741, 0, 0,  97, 0, 255, "BPMM" , "Maintenance des matériels")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9742, 0, 0,  97, 0, 255, "BPMN" , "Maintenance nautique")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9743, 0, 0,  97, 0, 255, "BPMMV", "Métiers de la mode - vêtements")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9744, 0, 0,  97, 0, 255, "BPMCC", "Métiers du cuir option chaussures")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9745, 0, 0,  97, 0, 255, "BPMCM", "Métiers du cuir option maroquinerie")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9746, 0, 0,  97, 0, 255, "BPMPB", "Métiers du pressing et de la blanchisserie")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9747, 0, 0,  97, 0, 255, "BPMIC", "Microtechniques")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9748, 0, 0,  97, 0, 255, "BPMIT", "Mise en œuvre des matériaux option industries textiles")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9749, 0, 0,  97, 0, 255, "BPMMC", "Mise en œuvre des matériaux opt. mat. métal. moulés, céramiques")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9750, 0, 0,  97, 0, 255, "BPOL" , "Optique lunetterie")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9751, 0, 0,  97, 0, 255, "BPOBS", "Ouvrage du bâtiment : aluminium, verre et matériaux de synthèse")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9752, 0, 0,  97, 0, 255, "BPOBM", "Ouvrage du bâtiment : métallerie")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9753, 0, 0,  97, 0, 255, "BPPP" , "Perruquier posticheur")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9754, 0, 0,  97, 0, 255, "BPPHO", "Photographie")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9755, 0, 0,  97, 0, 255, "BPPSP", "Pilotage de systèmes de production automatisée")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9756, 0, 0,  97, 0, 255, "BPPLP", "Pilote de ligne de production")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9757, 0, 0,  97, 0, 255, "BPPC" , "Plastiques et composites")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9758, 0, 0,  97, 0, 255, "BPPET", "Poissonnier écailler traiteur")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9759, 0, 0,  97, 0, 255, "BPPCE", "Procédés de la chimie, de l\'eau et des papiers-cartons")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9760, 0, 0,  97, 0, 255, "BPPG" , "Production graphique")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9761, 0, 0,  97, 0, 255, "BPPI" , "Production imprimée")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9762, 0, 0,  97, 0, 255, "BPPMD", "Productique mécanique - option décolletage")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9763, 0, 0,  97, 0, 255, "BPPD" , "Prothèse dentaire")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9764, 0, 0,  97, 0, 255, "BPRC" , "Réparation des carrosseries")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9765, 0, 0,  97, 0, 255, "BPSEC", "Secrétariat")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9766, 0, 0,  97, 0, 255, "BPSP" , "Sécurité prévention")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9767, 0, 0,  97, 0, 255, "BPSPL", "Services de proximité et vie locale")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9768, 0, 0,  97, 0, 255, "BPSEN", "Systèmes électroniques numériques")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9769, 0, 0,  97, 0, 255, "BPTA" , "Technicien aérostructure")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9770, 0, 0,  97, 0, 255, "BPTCB", "Technicien constructeur bois")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9771, 0, 0,  97, 0, 255, "BPTFB", "Technicien de fabrication bois et matériaux associes")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9772, 0, 0,  97, 0, 255, "BPTEC", "Technicien de maintenance de syst. énergétiques et climatiques")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9773, 0, 0,  97, 0, 255, "BPTSC" , "Technicien de scierie")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9774, 0, 0,  97, 0, 255, "BPTB" , "Technicien du bâtiment : organisation réalisation du gros œuvre")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9775, 0, 0,  97, 0, 255, "BPTFC", "Technique du froid et du conditionnement de l\'air")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9776, 0, 0,  97, 0, 255, "BPTEB", "Technicien d\'études du bâtiment")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9777, 0, 0,  97, 0, 255, "BPTU" , "Technicien d\'usinage")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9778, 0, 0,  97, 0, 255, "BPTCI", "Technicien en chaudronnerie industrielle")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9779, 0, 0,  97, 0, 255, "BPTIC", "Technicien en installation de syst. énergétiques et climatiques")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9780, 0, 0,  97, 0, 255, "BPTGT", "Technicien géomètre topographe")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9781, 0, 0,  97, 0, 255, "BPTMA", "Technicien menuisier agenceur")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9782, 0, 0,  97, 0, 255, "BPTM" , "Technicien modeleur")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9783, 0, 0,  97, 0, 255, "BPTO" , "Technicien outilleur")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9784, 0, 0,  97, 0, 255, "BPTSU", "Traitements de surfaces")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9785, 0, 0,  97, 0, 255, "BPTRA", "Transport")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9786, 0, 0,  97, 0, 255, "BPTF" , "Transport fluvial")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9787, 0, 0,  97, 0, 255, "BPTP" , "Travaux publics")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9788, 0, 0,  97, 0, 255, "BPV"  , "Vente (prospection-négociation-suivi de clientèle)")' );
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-02-12 => 2014-02-15
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-02-12')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-02-15';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // La table sacoche_demande manquait sur les nouvelles installations suite à un bug de création de cette table en date du 2014-02-11.
    $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SHOW TABLES FROM '.SACOCHE_STRUCTURE_BD_NAME.' LIKE "sacoche_demande"');
    if(empty($DB_TAB))
    {
      $reload_sacoche_demande = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_demande.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-02-15 => 2014-02-17
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-02-15')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-02-17';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // Encore une boulette de commise sur la table sacoche_demande
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_demande DROP INDEX demande_key , ADD UNIQUE demande_key (eleve_id,matiere_id,item_id) ' );
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-02-17 => 2014-03-09
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-02-17')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-03-09';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // Modification format champ niveau_id
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_niveau              CHANGE niveau_id niveau_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT ' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_groupe              CHANGE niveau_id niveau_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 ' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel         CHANGE niveau_id niveau_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 ' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel_domaine CHANGE niveau_id niveau_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 ' );
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-03-09 => 2014-03-18
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-03-09')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-03-18';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // ajout de paramètre
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "deconnexion_adresse_redirection" , "" )' );
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-03-18 => 2014-04-01
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-03-18')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-04-01';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // Modification champ sacoche_user
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_param_accueil user_param_accueil VARCHAR(83) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "user,alert,messages,resultats,faiblesses,demandes,saisies,officiel,socle,help,ecolo" ' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_param_accueil=CONCAT(user_param_accueil,",resultats,faiblesses,saisies,officiel,socle") ' );
    // Modification champ sacoche_parent_adresse
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parent_adresse CHANGE adresse_postal_code adresse_postal_code VARCHAR(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "@see http://fr.wikipedia.org/wiki/Code_postal" ' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parent_adresse SET adresse_postal_code="" WHERE adresse_postal_code="0" ' );
    // réordonner la table sacoche_parametre (ligne à déplacer vers la dernière MAJ lors d'ajout dans sacoche_parametre)
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre ORDER BY parametre_nom' );
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-04-01 => 2014-04-04
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-04-01')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-04-04';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // Modification champ sacoche_user
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_param_accueil user_param_accueil VARCHAR(93) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "user,alert,messages,resultats,faiblesses,reussites,demandes,saisies,officiel,socle,help,ecolo" ' );
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_param_accueil=CONCAT(user_param_accueil,",reussites") ' );
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-04-04 => 2014-05-03
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-04-04')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-05-03';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // Modification champs sacoche_officiel_assiduite
    if(empty($reload_sacoche_officiel_assiduite))
    {
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_officiel_assiduite CHANGE assiduite_non_justifie assiduite_absence_nj TINYINT(3) UNSIGNED NULL DEFAULT NULL COMMENT "nombre d\'absences non justifiées" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_officiel_assiduite ADD assiduite_retard_nj TINYINT UNSIGNED NULL DEFAULT NULL COMMENT "nombre de retards non justifiés" ' );
    }
  }
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// MAJ 2014-05-03 => 2014-05-24
// ////////////////////////////////////////////////////////////////////////////////////////////////////

if($version_base_structure_actuelle=='2014-05-03')
{
  if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
  {
    $version_base_structure_actuelle = '2014-05-24';
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
    // Erreur champ sacoche_officiel_fichier
    DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_officiel_fichier CHANGE officiel_type officiel_type ENUM("releve","bulletin","palier1","palier2","palier3") COLLATE utf8_unicode_ci NOT NULL DEFAULT "bulletin" ' );
  }
}

?>