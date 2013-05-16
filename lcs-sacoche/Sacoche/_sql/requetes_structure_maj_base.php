<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Générale Publique GNU pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */
 
// Extension de classe qui étend DB (pour permettre l'autoload)

// Ces méthodes ne concernent qu'une base STRUCTURE.
// Ces méthodes servent à mettre à jour la base.

// Ce script est appelé automatiquement si besoin lorsque :
// - un administrateur vient de restaurer une base
// - un utilisateur vient de se connecter

// La méthode DB_version_base(), déjà définie dans la classe DB_STRUCTURE_PUBLIC, est redéfinie ici.
// Elle est invoquée systématiquement à chaque étape, au cas où des mises à jour simultanées seraient lancées (c'est déjà arrivé) malgré les précautions prises (fichier de blocage).

class DB_STRUCTURE_MAJ_BASE extends DB
{

/**
 * Retourner la version de la base de l'établissement
 *
 * @param void
 * @return string
 */
public static function DB_version_base()
{
  $DB_SQL = 'SELECT parametre_valeur ';
  $DB_SQL.= 'FROM sacoche_parametre ';
  $DB_SQL.= 'WHERE parametre_nom=:parametre_nom ';
  $DB_VAR = array(':parametre_nom'=>'version_base');
  return DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
}


/**
 * Mettre à jour la base de l'établissement
 *
 * @param string   $version_base_structure_actuelle
 * @return void
 */
public static function DB_maj_base($version_base_structure_actuelle)
{

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // On s'arrête si c'est un pb de fichier non récupéré ou de base inaccessible
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if( !VERSION_BASE_STRUCTURE || !$version_base_structure_actuelle )
  {
    exit_error( 'Erreur MAJ BDD' /*titre*/ , 'Fichier avec version de la base manquant, ou base insaccessible.' /*contenu*/ );
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-05-15 => 2010-06-03
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-05-15')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-06-03';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // date/heure de dernière connexion effective
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_connexion_date DATETIME NOT NULL AFTER user_statut' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-06-03 => 2010-06-12
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-06-03')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-06-12';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // date/heure de dernière tentative de connexion
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_tentative_date DATETIME NOT NULL AFTER user_statut' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-06-12 => 2010-07-04
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-06-12')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-07-04';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'index
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_groupe ADD INDEX groupe_type (groupe_type)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_groupe ADD INDEX groupe_prof_id (groupe_prof_id)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre ADD PRIMARY KEY (parametre_nom)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD UNIQUE (user_login)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD INDEX user_profil (user_profil)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD INDEX user_statut (user_statut)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD INDEX user_id_ent (user_id_ent)' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-07-04 => 2010-07-13
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-07-04')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-07-13';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // mise à jour majeure du socle
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_section CHANGE section_nom section_nom VARCHAR(165) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_pilier SET pilier_nom=REPLACE(pilier_nom,"Pilier ","Compétence ")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_pilier SET pilier_nom=REPLACE(pilier_nom,"\'","’")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom=REPLACE(section_nom,"\'","’")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom=REPLACE(entree_nom,"\'","’")' );
      // mise à jour du socle - palier 1 - domaines
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - vocabulaire" WHERE section_id=4' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - grammaire" WHERE section_id=5' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - orthographe" WHERE section_id=6' );
      // mise à jour du socle - palier 1 - items
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Lire seul et écouter lire des textes du patrimoine et des œuvres intégrales de la littérature de jeunesse adaptés à son âge." WHERE entree_id=5' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Distinguer le présent du futur et du passé." WHERE entree_id=20' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Repérer des cases, des nœuds d’un quadrillage." WHERE entree_id=36' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Être précis et soigneux dans les mesures et les calculs." WHERE entree_id=39' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Reconnaître les emblèmes et les symboles de la République française." WHERE entree_id=43' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Appliquer les codes de la politesse dans ses relations avec ses camarades, avec les adultes de l’école et hors de l’école, avec le maître au sein de la classe." WHERE entree_id=46' );
      // mise à jour du socle - palier 2 - domaines
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - vocabulaire" WHERE section_id=16' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - grammaire" WHERE section_id=17' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - orthographe" WHERE section_id=18' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Maîtriser des connaissances dans divers domaines scientifiques et les mobiliser dans des contextes scientifiques différents et dans des activités de la vie courante" WHERE section_id=29' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Environnement et développement durable" WHERE section_id=30' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Avoir des repères relevant du temps et de l’espace" WHERE section_id=36' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_ordre=4 WHERE section_id=38' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_section VALUES ( 73,  9, 3, "Lire et pratiquer différents langages")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Avoir une bonne maîtrise de son corps et une pratique physique (sportive ou artistique)" WHERE section_id=43' );
      // mise à jour du socle - palier 2 - items
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Dire de mémoire, de façon expressive, une dizaine de poèmes et de textes en prose." WHERE entree_id=51' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Lire seul des textes du patrimoine et des œuvres intégrales de la littérature de jeunesse, adaptés à son âge." WHERE entree_id=53' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser des instruments de mesure." WHERE entree_id=105' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Mobiliser ses connaissances pour comprendre quelques questions liées à l’environnement et au développement durable et agir en conséquence." WHERE entree_id=123' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Connaitre et maîtriser les fonctions de base d’un ordinateur et de ses périphériques." WHERE entree_id=124' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET section_id=73 , entree_nom="Lire et utiliser textes, cartes, croquis, graphiques." WHERE entree_id=132' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_ordre=entree_ordre-1 WHERE section_id=36' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Lire des œuvres majeures du patrimoine et de la littérature pour la jeunesse." WHERE entree_id=137' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Reconnaître et décrire des œuvres préalablement étudiées." WHERE entree_id=140' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Interpréter de mémoire une chanson, participer à un jeu rythmique ; repérer des éléments musicaux caractéristiques simples." WHERE entree_id=142' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Inventer et réaliser des textes, des œuvres plastiques, des chorégraphies ou des enchaînements, à visée artistique ou expressive." WHERE entree_id=143' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Respecter les règles de la vie collective." WHERE entree_id=147' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Être persévérant dans toutes les activités." WHERE entree_id=150' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Commencer à savoir s’autoévaluer dans des situations simples." WHERE entree_id=151' );
      // mise à jour du socle - palier 3 - domaines
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Dire" WHERE section_id=46' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_section WHERE section_id=47' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Savoir utiliser des connaissances dans divers domaines scientifiques" WHERE section_id=55' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Environnement et développement durable" WHERE section_id=56' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="S’approprier un environnement informatique de travail" WHERE section_id=57' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Adopter une attitude responsable" WHERE section_id=58' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Créer, produire, traiter, exploiter des données" WHERE section_id=59' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="S’informer, se documenter" WHERE section_id=60' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Communiquer, échanger" WHERE section_id=61' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Avoir des connaissances et des repères" WHERE section_id=62' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Situer dans le temps, l’espace, les civilisations" , section_ordre=2 WHERE section_id=67' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Lire et pratiquer différents langages" , section_ordre=3 WHERE section_id=66' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_section VALUES ( 74, 16, 4, "Faire preuve de sensibilité, d’esprit critique, de curiosité")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_section WHERE section_id=63' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_section WHERE section_id=64' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_section WHERE section_id=65' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Être acteur de son parcours de formation et d’orientation" WHERE section_id=70' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Être capable de mobiliser ses ressources intellectuelles et physiques dans diverses situations" WHERE section_id=71' );
      // mise à jour du socle - palier 3 - items
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Adapter son mode de lecture à la nature du texte proposé et à l’objectif poursuivi." WHERE entree_id=157' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 282, 44,  1, "Repérer les informations dans un texte à partir des éléments explicites et des éléments implicites nécessaires.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser ses capacités de raisonnement, ses connaissances sur la langue, savoir faire appel à des outils appropriés pour lire." , entree_ordre=2 WHERE entree_id=158' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Dégager, par écrit ou oralement, l’essentiel d’un texte lu." , entree_ordre=3 WHERE entree_id=159' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Manifester, par des moyens divers, sa compréhension de textes variés." , entree_ordre=4 WHERE entree_id=160' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=161' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=162' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Reproduire un document sans erreur et avec une présentation adaptée." WHERE entree_id=163' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Écrire lisiblement un texte, spontanément ou sous la dictée, en respectant l’orthographe et la grammaire." WHERE entree_id=164' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=165' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Rédiger un texte bref, cohérent et ponctué, en réponse à une question ou à partir de consignes données." , entree_ordre=2 WHERE entree_id=166' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser ses capacités de raisonnement, ses connaissances sur la langue, savoir faire appel à des outils variés pour améliorer son texte." , entree_ordre=3 WHERE entree_id=167' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=168' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=169' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 283, 46,  0, "Formuler clairement un propos simple.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Développer de façon suivie un propos en public sur un sujet déterminé." , entree_ordre=1 WHERE entree_id=170' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Adapter sa prise de parole à la situation de communication." , entree_ordre=2 WHERE entree_id=171' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Participer à un débat, à un échange verbal." , entree_ordre=3 WHERE entree_id=172' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=173' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=174' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=175' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=176' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Établir un contact social." WHERE entree_id=177' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Écrire un message simple." WHERE entree_id=190' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Écrire un court récit, une description." WHERE entree_id=192' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=198' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=199' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=200' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=201' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Nombres et calculs : connaître et utiliser les nombres entiers, décimaux et fractionnaires ; mener à bien un calcul mental, à la main, à la calculatrice, avec un ordinateur." , entree_ordre=1 WHERE entree_id=202' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=203' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=204' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Géométrie : connaître et représenter des figures géométriques et des objets de l’espace ; utiliser leurs propriétés." , entree_ordre=2 WHERE entree_id=205' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=206' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=207' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Grandeurs et mesures : réaliser des mesures (longueurs, durées, …), calculer des valeurs (volumes, vitesses, …) en utilisant différentes unités." , entree_ordre=3 WHERE entree_id=208' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=209' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="L’univers et la Terre : organisation de l’univers ; structure et évolution au cours des temps géologiques de la Terre, phénomènes physiques." WHERE entree_id=210' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Le vivant : unité d’organisation et diversité ; fonctionnement des organismes vivants, évolution des espèces, organisation et fonctionnement du corps humain." WHERE entree_id=212' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Les objets techniques : analyse, conception et réalisation ; fonctionnement et conditions d’utilisation." WHERE entree_id=214' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Mobiliser ses connaissances pour comprendre des questions liées à l’environnement et au développement durable." WHERE entree_id=215' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser, gérer des espaces de stockage à disposition." , entree_ordre=0 WHERE entree_id=218' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser les périphériques à disposition." , entree_ordre=1 WHERE entree_id=220' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser les logiciels et les services à disposition." , entree_ordre=2 WHERE entree_id=216' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=217' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=219' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=221' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Connaître et respecter les règles élémentaires du droit relatif à sa pratique." WHERE entree_id=222' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Protéger sa personne et ses données." WHERE entree_id=223' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Faire preuve d’esprit critique face à l’information et à son traitement." , entree_ordre=2 WHERE entree_id=225' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Participer à des travaux collaboratifs en connaissant les enjeux et en respectant les règles." , entree_ordre=3 WHERE entree_id=228' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=224' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=226' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=227' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Saisir et mettre en page un texte." WHERE entree_id=229' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Traiter une image, un son ou une vidéo." , entree_ordre=1 WHERE entree_id=235' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Organiser la composition du document, prévoir sa présentation en fonction de sa destination." WHERE entree_id=231' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Différencier une situation simulée ou modélisée d’une situation réelle." , entree_ordre=3 WHERE entree_id=234' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=230' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=232' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=233' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Consulter des bases de données documentaires en mode simple (plein texte)." WHERE entree_id=236' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Identifier, trier et évaluer des ressources." , entree_ordre=1 WHERE entree_id=239' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Chercher et sélectionner l’information demandée." WHERE entree_id=238' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=237' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=240' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Écrire, envoyer, diffuser, publier." , entree_ordre=0 WHERE entree_id=243' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Recevoir un commentaire, un message y compris avec pièces jointes." WHERE entree_id=242' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 284, 61,  2, "Exploiter les spécificités des différentes situations de communication en temps réel ou différé.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=241' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=244' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant de l’espace : les grands ensembles physiques et humains et les grands types d’aménagements dans le monde, les principales caractéristiques géographiques de la France et de l’Europe." WHERE entree_id=245' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=246' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=247' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant du temps : les différentes périodes de l’histoire de l’humanité ; les grands traits de l’histoire (politique, sociale, économique, littéraire, artistique, culturelle) de la France et de l’Europe." , section_id=62 WHERE entree_id=249' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=248' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant de la culture littéraire : œuvres littéraires du patrimoine." , section_id=62 , entree_ordre=2 WHERE entree_id=251' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=250' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant de la culture artistique : œuvres picturales, musicales, scéniques, architecturales ou cinématographiques du patrimoine." , section_id=62 , entree_ordre=3 WHERE entree_id=252' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=253' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 285, 62,  4, "Relevant de la culture civique : droits de l’Homme ; formes d’organisation politique, économique et sociale dans l’Union européenne ; place et rôle de l’État en France ; mondialisation ; développement durable.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 286, 67,  0, "Situer des événements, des œuvres littéraires ou artistiques, des découvertes scientifiques ou techniques, des ensembles géographiques.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Identifier la diversité des civilisations, des langues, des sociétés, des religions." , entree_ordre=1 WHERE entree_id=256' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 287, 67,  2, "Établir des liens entre les œuvres (littéraires, artistiques) pour mieux les comprendre.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Mobiliser ses connaissances pour donner du sens à l’actualité." WHERE entree_id=259' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=257' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=258' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Lire et employer différents langages : textes – graphiques – cartes – images – musique." WHERE entree_id=255' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 293, 66,  1, "Connaître et pratiquer diverses formes d’expression à visée littéraire.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Connaître et pratiquer diverses formes d’expression à visée artistique." , section_id=66 , entree_ordre=2 WHERE entree_id=254' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 288, 74,  0, "Être sensible aux enjeux esthétiques et humains d’un texte littéraire.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 289, 74,  1, "Être sensible aux enjeux esthétiques et humains d’une œuvre artistique.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 290, 74,  2, "Être capable de porter un regard critique sur un fait, un document, une œuvre.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 291, 74,  3, "Manifester sa curiosité pour l’actualité et pour les activités culturelles ou artistiques.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Principaux droits de l’Homme et du citoyen." WHERE entree_id=260' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Valeurs, symboles, institutions de la République." WHERE entree_id=261' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Règles fondamentales de la démocratie et de la justice." WHERE entree_id=262' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Grandes institutions de l’Union européenne et rôle des grands organismes internationaux." WHERE entree_id=263' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Rôle de la défense nationale." WHERE entree_id=264' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Fonctionnement et rôle de différents médias." WHERE entree_id=265' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Respecter les règles de la vie collective." WHERE entree_id=266' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Se familiariser avec l’environnement économique, les entreprises, les métiers de secteurs et de niveaux de qualification variés." , entree_ordre=0 WHERE entree_id=272' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Connaître les parcours de formation correspondant à ces métiers et les possibilités de s’y intégrer." , entree_ordre=1 WHERE entree_id=273' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=271' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 292, 70,  2, "Savoir s’autoévaluer et être capable de décrire ses intérêts, ses compétences et ses acquis.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Être autonome dans son travail : savoir l’organiser, le planifier, l’anticiper, rechercher et sélectionner des informations utiles." WHERE entree_id=274' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Identifier ses points forts et ses points faibles dans des situations variées." WHERE entree_id=275' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Mobiliser à bon escient ses capacités motrices dans le cadre d’une pratique physique (sportive ou artistique) adaptée à son potentiel." , entree_ordre=2 WHERE entree_id=277' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_ordre=3 WHERE entree_id=276' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="S’engager dans un projet individuel." WHERE entree_id=278' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="S’intégrer et coopérer dans un projet collectif." WHERE entree_id=279' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Manifester curiosité, créativité, motivation à travers des activités conduites ou reconnues par l’établissement." WHERE entree_id=280' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Assumer des rôles, prendre des initiatives et des décisions." WHERE entree_id=281' );
      // mise à jour des liens des référentiels
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=0 WHERE entree_id IN(161,162,165,168,169,173,174,175,176,241,257,258)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=197 WHERE entree_id IN(198,199,200,201)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=202 WHERE entree_id IN(203,204)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=205 WHERE entree_id IN(206,207)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=208 WHERE entree_id=209' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=216 WHERE entree_id IN(217,221)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=218 WHERE entree_id=219' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=222 WHERE entree_id=224' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=223 WHERE entree_id IN(226,227)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=229 WHERE entree_id=230' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=197 WHERE entree_id IN(232,233)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=216 WHERE entree_id=237' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=238 WHERE entree_id=240' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=243 WHERE entree_id=244' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=245 WHERE entree_id IN(246,247)' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=249 WHERE entree_id=248' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=251 WHERE entree_id=250' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=252 WHERE entree_id=253' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=273 WHERE entree_id=271' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'OPTIMIZE TABLE sacoche_socle_pilier');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'OPTIMIZE TABLE sacoche_socle_section');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'OPTIMIZE TABLE sacoche_socle_entree');
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-07-13 => 2010-07-15
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-07-13')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-07-15';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // paramétrage codes Lomer / background-color
      // La première instruction ayant été oubliée, quelques tentatives d'installations peuvent être corrompues => corrigé dans la v.2010-07-27.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_nom parametre_nom VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_NA","#ff9999")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_VA","#ffdd33")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_A","#99ff99")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_note_style","Lomer")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-07-15 => 2010-07-16
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-07-15')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-07-16';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // oubli d'une modification du socle
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Organisation et gestion de données : reconnaître des situations de proportionnalité, utiliser des pourcentages, des tableaux, des graphiques ; exploiter des données statistiques et aborder des situations simples de probabilité." , entree_ordre=0 WHERE entree_id=197' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-07-16 => 2010-07-27
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-07-16')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-07-27';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // Correction du bug signalé dans le passage de la v.2010-07-13 à la v.2010-07-15.
      $DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="css_background-color"' );
      if(!empty($DB_ROW))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="css_background-color"' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_nom parametre_nom VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_NA","#ff9999")' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_VA","#ffdd33")' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_A","#99ff99")' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_note_style","Lomer")' );
      }
      // ajout de modes de calculs
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel CHANGE referentiel_calcul_methode referentiel_calcul_methode ENUM( "geometrique", "arithmetique", "classique", "bestof1", "bestof2", "bestof3" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "geometrique" COMMENT "Coefficients en progression géométrique, arithmetique, ou moyenne classique non pondérée, ou conservation des meilleurs scores. Valeur surclassant la configuration par défaut." ' );
      // ajout de 2 tables pour la validation du socle
      // Les supprimer si elles existent : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), ces anciennes tables éventuellement existantes ne seraient pas réinitialisées.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_jointure_user_entree' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_jointure_user_entree (user_id MEDIUMINT(8) UNSIGNED NOT NULL,entree_id SMALLINT(5) UNSIGNED NOT NULL,validation_entree_etat TINYINT(1) NOT NULL COMMENT "1 si validation positive ; 0 si validation négative.",validation_entree_date DATE NOT NULL,validation_entree_info TINYTEXT COLLATE utf8_unicode_ci NOT NULL COMMENT "Enregistrement statique du nom du validateur, conservé les années suivantes.",UNIQUE KEY validation_entree_key (user_id,entree_id),KEY user_id (user_id),KEY entree_id (entree_id) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_jointure_user_pilier' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_jointure_user_pilier (user_id MEDIUMINT(8) UNSIGNED NOT NULL,pilier_id SMALLINT(5) UNSIGNED NOT NULL,                                                                                                          validation_pilier_date DATE NOT NULL,validation_pilier_info TINYTEXT COLLATE utf8_unicode_ci NOT NULL COMMENT "Enregistrement statique du nom du validateur, conservé les années suivantes.",UNIQUE KEY validation_pilier_key (user_id,pilier_id),KEY user_id (user_id),KEY pilier_id (pilier_id) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-07-27 => 2010-07-29
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-07-27')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-07-29';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'un champ qui va finalement servir pour valider les piliers
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_pilier ADD validation_pilier_etat TINYINT(1) NOT NULL COMMENT "1 si validation positive ; 0 si validation négative." AFTER pilier_id' );
      // ajout de 2 entrées pour gérer les droits de validation
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("profil_validation_entree" , "directeur,professeur")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("profil_validation_pilier" , "directeur,profprincipal")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-07-29 => 2010-07-31
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-07-29')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-07-31';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification d'un champ afin de pouvoir repérer les demandes d'évaluations en attente de saisie
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_saisie CHANGE saisie_note saisie_note ENUM( "VV", "V", "R", "RR", "ABS", "NN", "DISP", "REQ" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-07-31 => 2010-08-01
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-07-31')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-08-01';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // renommage d'un champ pour davantage de clarté en vu de l'ajout d'options
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"ms","BilanMoyenneScore") WHERE parametre_nom="eleve_options"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"pv","BilanPourcentageAcquis") WHERE parametre_nom="eleve_options"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"as","SoclePourcentageAcquis") WHERE parametre_nom="eleve_options"' );
      // suppression du palier 4 qui ne sert à rien
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_palier WHERE palier_id=4' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,",4","") WHERE parametre_nom="paliers"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"4","3") WHERE parametre_nom="paliers"' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-08-01 => 2010-08-04
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-08-01')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-08-04';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'un champ pour pouvoir décider si un item est disponible ou non à la réévaluation
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel_item ADD item_cart TINYINT(1) NOT NULL DEFAULT "1" COMMENT "0 pour empêcher les élèves de demander une évaluation sur cet item." AFTER item_coef' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-08-04 => 2010-08-05
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-08-04')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-08-05';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // finalement la table RSS ne va pas servir
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE sacoche_rss' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-08-05 => 2010-08-06
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-08-05')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-08-06';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajouter deux matières communes
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (40 , 1 , 0 , "VSPRO" , "Vie sociale et professionnelle")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (41 , 1 , 0 , "G-TPR" , "Enseignement technologique-professionnel")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-08-06 => 2010-09-27
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-08-06')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-09-27';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // oubli d'une entrée à supprimer dans sacoche_niveau() pour l'ex-palier 4
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_niveau WHERE niveau_id=4' );
      // récupérer qq infos pour maj la suite
      $DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="css_note_style"' );
      require_once(CHEMIN_DOSSIER_INCLUDE.'tableau_notes_txt.php');
      $rr = $tab_notes_txt[$DB_ROW['parametre_valeur']]['RR'];
      $r  = $tab_notes_txt[$DB_ROW['parametre_valeur']]['R'];
      $v  = $tab_notes_txt[$DB_ROW['parametre_valeur']]['V'];
      $vv = $tab_notes_txt[$DB_ROW['parametre_valeur']]['VV'];
      $DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="eleve_options"' );
      $eleve_bilans = str_replace( array(',SoclePourcentageAcquis',',SocleEtatValidation','SoclePourcentageAcquis,','SocleEtatValidation,','SoclePourcentageAcquis','SocleEtatValidation') , '' , $DB_ROW['parametre_valeur'] );
      $eleve_socle  = 'SocleAcces,'.str_replace( array(',BilanMoyenneScore',',BilanPourcentageAcquis','BilanMoyenneScore,','BilanPourcentageAcquis,','BilanMoyenneScore','BilanPourcentageAcquis') , '' , $DB_ROW['parametre_valeur'] );
      // ajouter des gestions de droits
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_validation_entree" WHERE parametre_nom="profil_validation_entree"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_validation_pilier" WHERE parametre_nom="profil_validation_pilier"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_modifier_mdp"      , "directeur,professeur,eleve")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_voir_referentiels" , "directeur,professeur,eleve")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="eleve_options"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_eleve_bilans" , "'.$eleve_bilans.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_eleve_socle"  , "'.$eleve_socle.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_eleve_demandes" WHERE parametre_nom="eleve_demandes"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="note_image_style"     WHERE parametre_nom="css_note_style"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("note_texte_RR" , "'.$rr.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("note_texte_R"  , "'.$r.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("note_texte_V"  , "'.$v.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("note_texte_VV" , "'.$vv.'")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-09-27 => 2010-10-04
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-09-27')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-10-04';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajouter une gestion de droits
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_voir_score_bilan" , "directeur,professeur,eleve")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-10-04 => 2010-10-16
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-10-04')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-10-16';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modif de champs TINYTEXT en VARCHAR
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_entree CHANGE entree_nom entree_nom VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_saisie CHANGE saisie_info saisie_info VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT "Enregistrement statique du nom du devoir et du professeur, conservé les années suivantes."' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel_item CHANGE item_nom item_nom VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel_item CHANGE item_lien item_lien VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_valeur parametre_valeur VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_pilier CHANGE validation_pilier_info validation_pilier_info VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT "Enregistrement statique du nom du validateur, conservé les années suivantes."' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_entree CHANGE validation_entree_info validation_entree_info VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT "Enregistrement statique du nom du validateur, conservé les années suivantes."' );
      // réparation de la table "sacoche_socle_section"
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_section CHANGE section_nom section_nom VARCHAR( 165 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_section WHERE section_id>28' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_section VALUES 
                                            ( 29,  7, 2, "Maîtriser des connaissances dans divers domaines scientifiques et les mobiliser dans des contextes scientifiques différents et dans des activités de la vie courante"),
                                            ( 30,  7, 3, "Environnement et développement durable"),
                                            ( 31,  8, 1, "S’approprier un environnement informatique de travail"),
                                            ( 32,  8, 2, "Adopter une attitude responsable"),
                                            ( 33,  8, 3, "Créer, produire, traiter, exploiter des données"),
                                            ( 34,  8, 4, "S’informer, se documenter"),
                                            ( 35,  8, 5, "Communiquer, échanger"),
                                            ( 36,  9, 1, "Avoir des repères relevant du temps et de l’espace"),
                                            ( 37,  9, 2, "Avoir des repères littéraires"),
                                            ( 73,  9, 3, "Lire et pratiquer différents langages"),
                                            ( 38,  9, 4, "Pratiquer les arts et avoir des repères en histoire des arts"),
                                            ( 39, 10, 1, "Connaître les principes et fondements de la vie civique et sociale"),
                                            ( 40, 10, 2, "Avoir un comportement responsable"),
                                            ( 41, 11, 1, "S’appuyer sur des méthodes de travail pour être autonome"),
                                            ( 42, 11, 2, "Faire preuve d’initiative"),
                                            ( 43, 11, 3, "Avoir une bonne maîtrise de son corps et une pratique physique (sportive ou artistique)"),
                                            ( 44, 12, 1, "Lire"),
                                            ( 45, 12, 2, "Écrire"),
                                            ( 46, 12, 3, "Dire"),
                                            ( 48, 13, 1, "Réagir et dialoguer"),
                                            ( 49, 13, 2, "Écouter et comprendre"),
                                            ( 50, 13, 3, "Parler en continu"),
                                            ( 51, 13, 4, "Lire"),
                                            ( 52, 13, 5, "Écrire"),
                                            ( 53, 14, 1, "Pratiquer une démarche scientifique et technologique, résoudre des problèmes"),
                                            ( 54, 14, 2, "Savoir utiliser des connaissances et des compétences mathématiques"),
                                            ( 55, 14, 3, "Savoir utiliser des connaissances dans divers domaines scientifiques"),
                                            ( 56, 14, 4, "Environnement et développement durable"),
                                            ( 57, 15, 1, "S’approprier un environnement informatique de travail"),
                                            ( 58, 15, 2, "Adopter une attitude responsable"),
                                            ( 59, 15, 3, "Créer, produire, traiter, exploiter des données"),
                                            ( 60, 15, 4, "S’informer, se documenter"),
                                            ( 61, 15, 5, "Communiquer, échanger"),
                                            ( 62, 16, 1, "Avoir des connaissances et des repères"),
                                            ( 67, 16, 2, "Situer dans le temps, l’espace, les civilisations"),
                                            ( 66, 16, 3, "Lire et pratiquer différents langages"),
                                            ( 74, 16, 4, "Faire preuve de sensibilité, d’esprit critique, de curiosité"),
                                            ( 68, 17, 1, "Connaître les principes et fondements de la vie civique et sociale"),
                                            ( 69, 17, 2, "Avoir un comportement responsable"),
                                            ( 70, 18, 1, "Être acteur de son parcours de formation et d’orientation"),
                                            ( 71, 18, 2, "Être capable de mobiliser ses ressources intellectuelles et physiques dans diverses situations"),
                                            ( 72, 18, 3, "Faire preuve d’initiative")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-10-16 => 2010-10-29
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-10-16')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-10-29';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajouter une entrée dans sacoche_referentiel pour paramétrer le bulletin de synthèse
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel ADD referentiel_mode_synthese ENUM( "inconnu", "sans", "domaine", "theme" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "inconnu" ' );
      // ajouter une entrée dans sacoche_matiere pour ordonner les matières dans le bulletin de synthèse
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_matiere ADD matiere_ordre TINYINT UNSIGNED NOT NULL DEFAULT "255" AFTER matiere_transversal ' );
      // ajout de valeurs par défaut dans les champs...  en tout cas de la base de la structure...
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_demande CHANGE user_id user_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE matiere_id matiere_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE item_id item_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE demande_date demande_date DATE NOT NULL DEFAULT "0000-00-00" , CHANGE demande_statut demande_statut ENUM( "eleve", "prof" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "eleve" COMMENT "[eleve] pour une demande d\'élève ; [prof] pour une prévision d\'évaluation par le prof ; une annulation de l\'élève ou du prof efface l\'enregistrement" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir CHANGE prof_id prof_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE groupe_id groupe_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE devoir_date devoir_date DATE NOT NULL DEFAULT "0000-00-00" , CHANGE devoir_info devoir_info VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_groupe CHANGE groupe_type groupe_type ENUM( "classe", "groupe", "besoin", "eval" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "classe" , CHANGE groupe_prof_id groupe_prof_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" COMMENT "Id du prof dans le cas d\'un groupe de type [eval] ; 0 sinon." , CHANGE groupe_ref groupe_ref CHAR( 8 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE groupe_nom groupe_nom VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE niveau_id niveau_id TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_devoir_item CHANGE devoir_id devoir_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE item_id item_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE jointure_ordre jointure_ordre TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_groupe_periode CHANGE groupe_id groupe_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE periode_id periode_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE jointure_date_debut jointure_date_debut DATE NOT NULL DEFAULT "0000-00-00" , CHANGE jointure_date_fin jointure_date_fin DATE NOT NULL DEFAULT "0000-00-00" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_entree CHANGE user_id user_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE entree_id entree_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE validation_entree_etat validation_entree_etat TINYINT( 1 ) NOT NULL DEFAULT "1" COMMENT "1 si validation positive ; 0 si validation négative." , CHANGE validation_entree_date validation_entree_date DATE NOT NULL DEFAULT "0000-00-00" , CHANGE validation_entree_info validation_entree_info VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Enregistrement statique du nom du validateur, conservé les années suivantes." ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_groupe CHANGE user_id user_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE groupe_id groupe_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE jointure_pp jointure_pp TINYINT( 1 ) NOT NULL DEFAULT "0" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_matiere CHANGE user_id user_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE matiere_id matiere_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE jointure_coord jointure_coord TINYINT( 1 ) NOT NULL DEFAULT "0" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_pilier CHANGE user_id user_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE pilier_id pilier_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE validation_pilier_etat validation_pilier_etat TINYINT( 1 ) NOT NULL DEFAULT "1" COMMENT "1 si validation positive ; 0 si validation négative." , CHANGE validation_pilier_date validation_pilier_date DATE NOT NULL DEFAULT "0000-00-00" , CHANGE validation_pilier_info validation_pilier_info VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Enregistrement statique du nom du validateur, conservé les années suivantes." ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_matiere CHANGE matiere_ref matiere_ref VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE matiere_nom matiere_nom VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_niveau CHANGE palier_id palier_id TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE niveau_ordre niveau_ordre TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE niveau_ref niveau_ref VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE code_mef code_mef CHAR( 11 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE niveau_nom niveau_nom VARCHAR( 55 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_nom parametre_nom VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ""  , CHANGE parametre_valeur parametre_valeur VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_periode CHANGE periode_nom periode_nom VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel CHANGE matiere_id matiere_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE niveau_id niveau_id TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE referentiel_partage_etat referentiel_partage_etat ENUM( "bof", "non", "oui", "hs" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "non" COMMENT "[oui] = référentiel partagé sur le serveur communautaire ; [non] = référentiel non partagé avec la communauté ; [bof] = référentiel dont le partage est sans intérêt (pas novateur) ; [hs] = référentiel dont le partage est sans objet (matière spécifique)" , CHANGE referentiel_partage_date referentiel_partage_date DATE NOT NULL DEFAULT "0000-00-00" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel_domaine CHANGE matiere_id matiere_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE niveau_id niveau_id TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE domaine_ordre domaine_ordre TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "1" COMMENT "Commence à 1." , CHANGE domaine_ref domaine_ref CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE domaine_nom domaine_nom VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel_item CHANGE theme_id theme_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE entree_id entree_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE item_ordre item_ordre TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" COMMENT "Commence à 0." , CHANGE item_nom item_nom VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE item_lien item_lien VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel_theme CHANGE domaine_id domaine_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE theme_ordre theme_ordre TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "1" COMMENT "Commence à 1." , CHANGE theme_nom theme_nom VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_saisie CHANGE prof_id prof_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE eleve_id eleve_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE devoir_id devoir_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE item_id item_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE saisie_date saisie_date DATE NOT NULL DEFAULT "0000-00-00" , CHANGE saisie_note saisie_note ENUM( "VV", "V", "R", "RR", "ABS", "NN", "DISP", "REQ" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "NN" , CHANGE saisie_info saisie_info VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Enregistrement statique du nom du devoir et du professeur, conservé les années suivantes." ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_entree CHANGE section_id section_id SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE entree_ordre entree_ordre TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" COMMENT "Commence à 0." , CHANGE entree_nom entree_nom VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_palier CHANGE palier_ordre palier_ordre TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE palier_nom palier_nom VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_pilier CHANGE palier_id palier_id TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE pilier_ordre pilier_ordre TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "1" COMMENT "Commence à 1." , CHANGE pilier_ref pilier_ref VARCHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE pilier_nom pilier_nom VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_section CHANGE pilier_id pilier_id TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "0" , CHANGE section_ordre section_ordre TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT "1" COMMENT "Commence à 1."  , CHANGE section_nom section_nom VARCHAR( 165 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_num_sconet user_num_sconet MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0" COMMENT "ELENOET pour un élève (entre 2000 et 5000 ; parfois appelé n° GEP avec un 0 devant) ou INDIVIDU_ID pour un prof (dépasse parfois une capacité SMALLINT UNSIGNED)" , CHANGE user_reference user_reference CHAR( 11 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Dans Sconet, ID_NATIONAL pour un élève (pour un prof ce pourrait être le NUMEN mais il n\'est pas renseigné et il faudrait deux caractères de plus). Ce champ sert aussi pour un import tableur." , CHANGE user_profil user_profil ENUM( "eleve", "professeur", "directeur", "administrateur" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "eleve" , CHANGE user_nom user_nom VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE user_prenom user_prenom VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE user_login user_login VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE user_password user_password CHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" , CHANGE user_tentative_date user_tentative_date DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00" , CHANGE user_connexion_date user_connexion_date DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00" , CHANGE eleve_classe_id eleve_classe_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT "0"  , CHANGE user_id_ent user_id_ent VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Paramètre renvoyé après une identification CAS depuis un ENT (ça peut être le login, mais ça peut aussi être un numéro interne à l\'ENT...)." , CHANGE user_id_gepi user_id_gepi VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Login de l\'utilisateur dans Gepi utilisé pour un transfert note/moyenne vers un bulletin." ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-10-29 => 2010-11-03
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-10-29')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-11-03';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("note_legende_RR"   , "A complètement échoué.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("note_legende_R"    , "A plutôt échoué.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("note_legende_V"    , "A plutôt réussi.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("note_legende_VV"   , "A complètement réussi.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("acquis_texte_NA"   , "NA")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("acquis_texte_VA"   , "VA")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("acquis_texte_A"    , "A")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("acquis_legende_NA" , "Non acquis.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("acquis_legende_VA" , "Partiellement acquis.")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("acquis_legende_A"  , "Acquis.")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-11-03 => 2010-11-04
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-11-03')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-11-04';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // mise à jour de paramètres (anciennement changés pour les nouveaux, mais modifs oubliées pour les anciens)
      $DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="connexion_mode"' );
      if($DB_ROW['parametre_valeur']=='normal')
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="sacoche" WHERE parametre_nom="connexion_nom"' );
      }
      $DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="connexion_nom"' );
      if( ($DB_ROW['parametre_valeur']=='argos') || ($DB_ROW['parametre_valeur']=='argos64') )
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="ent-cas.ac-bordeaux.fr" WHERE parametre_nom="cas_serveur_host"' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="cas" WHERE parametre_nom="cas_serveur_root"' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-11-04 => 2010-11-14
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-11-04')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-11-14';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // remise du niveau P4 pour que les lycées puissent disposer d'un "niveau transversal"
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES (4,4,159,"P4","","Palier 4 (2nde - Tle)")' );
      // ajout d'une matière "Informatique" pour gérer le b2i
      // Si une matière similaire spécifique est trouvée, la convertir... surtout si sa référence est "INFO" (sinon conflit en vue)
      $DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SELECT matiere_id FROM sacoche_matiere WHERE matiere_ref="INFO"' );
      if(empty($DB_ROW))
      {
        $DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SELECT matiere_id FROM sacoche_matiere WHERE matiere_ref IN("INFOR","B2I","ORDI","MICRO")' );
      }
      if(!empty($DB_ROW))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_jointure_user_matiere SET matiere_id=42 WHERE matiere_id='.$DB_ROW['matiere_id'] );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_domaine   SET matiere_id=42 WHERE matiere_id='.$DB_ROW['matiere_id'] );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_demande               SET matiere_id=42 WHERE matiere_id='.$DB_ROW['matiere_id'] );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel           SET matiere_id=42 , referentiel_partage_etat="non" WHERE matiere_id='.$DB_ROW['matiere_id'] );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre             SET parametre_valeur=REPLACE(parametre_valeur,",99",",42,99") WHERE parametre_nom="matieres"' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_matiere WHERE matiere_id='.$DB_ROW['matiere_id'].'' );
      }
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (42,1,0,255,"INFO","Informatique")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-11-14 => 2010-11-15
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-11-14')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-11-15';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // Niveaux 'longitudinaux' renommés en 'cycles' pour éviter la confusion avec la notion de palier du socle
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET niveau_nom="Cycle 2 (GS-CE1)"  WHERE niveau_id=1' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET niveau_nom="Cycle 3 (CE2-CM2)" WHERE niveau_id=2' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET niveau_nom="Cycle Collège"     WHERE niveau_id=3' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET niveau_nom="Cycle Lycée"       WHERE niveau_id=4' );
      // Modification du nom d'un champ qui devient en conséquence inapproprié
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET palier_id=1 WHERE palier_id>0' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_niveau CHANGE palier_id cycle_id TINYINT(1) UNSIGNED NOT NULL DEFAULT "0" COMMENT "Indique un niveau \'longitudinal\' nommé \'cycle\'."' );
      // ajout d'un paramètre "cycles" disctinct du paramètre "paliers"
      $DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="paliers"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("cycles" , "'.$DB_ROW['parametre_valeur'].'")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-11-15 => 2010-11-16
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-11-15')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-11-16';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // Double erreur dans la maj précédente : "cycle_id" au lieu de "niveau_cycle" et erreur dans la requête pour créer sacoche_niveau sur les nouvelles installations
      $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SHOW TABLES FROM '.SACOCHE_STRUCTURE_BD_NAME.' LIKE "sacoche_niveau"');
      if(!empty($DB_TAB))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_niveau CHANGE cycle_id niveau_cycle TINYINT(1) UNSIGNED NOT NULL DEFAULT "0" COMMENT "Indique un niveau \'longitudinal\' nommé \'cycle\'."' );
      }
      else
      {
        $reload_sacoche_niveau = TRUE;
        $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_niveau.sql');
        DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
        DB::close(SACOCHE_STRUCTURE_BD_NAME);
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-11-16 => 2010-11-28
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-11-16')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2010-11-28';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajouter une gestion de droits
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_voir_algorithme" , "directeur,professeur,eleve")' );
      // nouvel attribut user : daltonisme
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_daltonisme TINYINT(1) UNSIGNED NOT NULL DEFAULT "0" AFTER user_statut' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2010-11-28 => 2011-01-06
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2010-11-28')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-01-06';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajouter la matière Sciences
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 43, 1, 0, 255, "SCIEN", "Sciences")' );
      // convertir la matière "Vie sociale et professionnelle" en "Prévention-Santé-Environnement" (http://media.education.gouv.fr/file/special_2/25/5/prevention_sante_environnement_44255.pdf)
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere SET matiere_ref="PSE", matiere_nom="Prévention-Santé-Environnement" WHERE matiere_id=40' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-01-06 => 2011-03-08
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-01-06')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-03-08';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // changement des dénominations par défaut associées aux codes de couleur
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="Très insuffisant."  WHERE parametre_valeur="A complètement échoué."' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="Insuffisant."       WHERE parametre_valeur="A plutôt échoué."' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="Satisfaisant."      WHERE parametre_valeur="A plutôt réussi."' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="Très satisfaisant." WHERE parametre_valeur="A complètement réussi."' );
      // ajout de la possibilité de personnaliser suivant les matières le nombre maximal de demandes autorisées
      $valeur = (int)DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="droit_eleve_demandes"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="droit_eleve_demandes"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_matiere ADD matiere_nb_demandes TINYINT UNSIGNED NOT NULL DEFAULT "0" AFTER matiere_transversal ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere SET matiere_nb_demandes='.$valeur );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-03-08 => 2011-03-16
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-03-08')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-03-16';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // correction de bug : champ défini à tinyint(1) au lieu de tinyint(3) pour les nouvelles structures.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_matiere CHANGE matiere_nb_demandes matiere_nb_demandes TINYINT(3) UNSIGNED NOT NULL DEFAULT "0" ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-03-16 => 2011-04-04
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-03-16')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-04-04';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout des matières correspondant aux piliers, utiles pour organiser les référentiels à l'école primaire
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 90, 1, 0, 0, 255,  "P1", "1 Maîtrise de la langue française")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 91, 1, 0, 0, 255,  "P2", "2 Pratique d\'une langue vivante étrangère")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 92, 1, 0, 0, 255, "P3A", "3 Principaux éléments de mathématiques")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 93, 1, 0, 0, 255, "P3B", "3 Culture scientifique et technologique")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 94, 1, 0, 0, 255,  "P4", "4 Maîtrise des TICE")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 95, 1, 0, 0, 255,  "P5", "5 Culture humaniste")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 96, 1, 0, 0, 255,  "P6", "6 Compétences sociales et civiques")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 97, 1, 0, 0, 255,  "P7", "7 Autonomie et l\'initiative")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-04-04 => 2011-05-10
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-04-04')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-05-10';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de 2 champs pour paramétrer la date à partir de laquelle les élèves ont accès aux notes d'une évaluation
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir ADD devoir_visible_date DATE NOT NULL DEFAULT "0000-00-00" AFTER devoir_info' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_saisie ADD saisie_visible_date DATE NOT NULL DEFAULT "0000-00-00" AFTER saisie_info' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_devoir SET devoir_visible_date=devoir_date' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_saisie SET saisie_visible_date=saisie_date' );
      // suppression de cette clef qui n'accélère probablement rien et qui prend de la place (grosse table)
      // remarque : cette modification de table n'est pas anodine et prends un certain temps si beaucoup de notes sont saisies
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_saisie DROP INDEX saisie_key' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-05-10 => 2011-05-20
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-05-10')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-05-20';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // mise à jour d'un champ du socle
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Adopter des comportements favorables à sa santé et sa sécurité." WHERE entree_id=268' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-05-20 => 2011-05-22
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-05-20')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-05-22';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'une entrée pour gérer les droits de validation
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_annulation_pilier" , "directeur,aucunprof")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-05-22 => 2011-05-31
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-05-22')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-05-31';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification/réorganisation de la table user pour gérer les champs de sconet eleve_id et individu_fonction
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_sconet_id MEDIUMINT UNSIGNED NOT NULL DEFAULT "0" COMMENT "ELEVE.ELEVE.ID pour un élève ; INDIVIDU_ID pour un prof" AFTER user_num_sconet' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_sconet_elenoet SMALLINT UNSIGNED NOT NULL DEFAULT "0" COMMENT "ELENOET pour un élève (entre 2000 et 5000 ; parfois appelé n° GEP avec un 0 devant)" AFTER user_sconet_id' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_sconet_id=user_num_sconet WHERE user_profil!="eleve"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_sconet_elenoet=user_num_sconet WHERE user_profil="eleve"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user DROP user_num_sconet' );
      // ajout d'un champ dans la table user pour gérer le choix de la langue du socle
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD eleve_langue TINYINT(3) UNSIGNED NOT NULL DEFAULT "132" COMMENT "Langue choisie pour le socle." AFTER eleve_classe_id' );
      // ajout d'un champ dans la table user pour gérer la date de sortie (CNIL) ; pas de date par défaut possible dans la création du champ ou pour l'insertion d'une nouvelle ligne...
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_statut_date DATE NOT NULL DEFAULT "0000-00-00" AFTER user_connexion_date' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_statut_date=NOW()' );
      // ajout de 2 niveaux "Première générale" et "Terminale générale"
      if(empty($reload_sacoche_niveau))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES (  68, 0,  84,     "1", "2011....11.", "Première générale")' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES (  69, 0,  94,     "T", "2021....11.", "Terminale générale")' );
      }
      // Je renonce aux fonctions car la liste n'est pas claire + ça ne sert à rien sans le libellé court de la matière (qui ne correspond pas à ce qu'on trouve dans STS, il est dans Nomenclature.xml mais pas dans SACoche) + ça fait assez de modifs comme ça + ça pose problème pour le 1er degré + un prof peut avoir quitté l'établissement + on peut faire un export LPC sans spécifier qui a validé.
      // DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_fonction ENUM( "", "ENS", "DIR", "EDU", "DOC", "COP", "ORI", "FIJ", "AED", "SUR" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "INDIVIDU.FONCTION dans le fichier Sconet-STS des personnels" AFTER user_reference' );
      // DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_fonction="ENS" WHERE user_profil="professeur"' );
      // DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_fonction="DIR" WHERE user_profil="directeur"' );
      // correctif : passage du champ pilier_id de SMALLINT en TINYINT
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_pilier CHANGE pilier_id pilier_id TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_pilier CHANGE pilier_id pilier_id TINYINT(3) UNSIGNED NOT NULL DEFAULT "0" ' );
      // correctif : dénominations d'items
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Organisation et gestion de données : reconnaître des situations de proportionnalité, utiliser des pourcentages, des tableaux, des graphiques. Exploiter des données statistiques et aborder des situations simples de probabilité." WHERE entree_id=197' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Nombres et calculs : connaître et utiliser les nombres entiers, décimaux et fractionnaires. Mener à bien un calcul : mental, à la main, à la calculatrice, avec un ordinateur." WHERE entree_id=202' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Géométrie : connaître et représenter des figures géométriques et des objets de l’espace. Utiliser leurs propriétés." WHERE entree_id=205' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant du temps : les différentes périodes de l’histoire de l’humanité - Les grands traits de l’histoire (politique, sociale, économique, littéraire, artistique, culturelle) de la France et de l’Europe." WHERE entree_id=249' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant de la culture civique : Droits de l’Homme - Formes d’organisation politique, économique et sociale dans l’Union européenne - Place et rôle de l’État en France - Mondialisation - Développement durable." WHERE entree_id=285' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Respecter des comportements favorables à sa santé et sa sécurité." WHERE entree_id=268' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Respecter quelques notions juridiques de base." WHERE entree_id=269' );
      // modification des identifiants de compétence pour qu'ils correspondent à ceux de LPC (pas d'info sur le palier 1...)
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_pilier SET pilier_id=pilier_id+20' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_pilier SET pilier_id=pilier_id-31 WHERE pilier_id>31' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_pilier SET pilier_id=pilier_id-16 WHERE pilier_id>23' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_pilier SET pilier_id=pilier_id-5  WHERE pilier_id>20' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET pilier_id=pilier_id+20' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET pilier_id=pilier_id-31 WHERE pilier_id>31' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET pilier_id=pilier_id-16 WHERE pilier_id>23' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET pilier_id=pilier_id-5  WHERE pilier_id>20' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_jointure_user_pilier SET pilier_id=pilier_id+20' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_jointure_user_pilier SET pilier_id=pilier_id-31 WHERE pilier_id>31' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_jointure_user_pilier SET pilier_id=pilier_id-16 WHERE pilier_id>23' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_jointure_user_pilier SET pilier_id=pilier_id-5  WHERE pilier_id>20' );
      // modification des identifiants d'item pour qu'ils correspondent à ceux de LPC (pas d'info sur le palier 1...)
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_id=entree_id+1000' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=entree_id+1000' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_jointure_user_entree SET entree_id=entree_id+1000' );
      $tab_conv_items = array(1=>3111,2=>3112,3=>3113,4=>3121,5=>3122,6=>3123,7=>3124,8=>3125,9=>3131,10=>3132,11=>3133,12=>3141,13=>3142,14=>3143,15=>3144,16=>3145,17=>3151,18=>3152,19=>3153,20=>3154,21=>3161,22=>3162,23=>3163,24=>3311,25=>3312,26=>3313,27=>3314,28=>3315,29=>3316,30=>3317,31=>3318,32=>3321,33=>3322,34=>3323,35=>3324,36=>3325,37=>3326,38=>3331,39=>3332,40=>3333,41=>3341,42=>3342,43=>3611,44=>3621,45=>3622,46=>3623,47=>2111,48=>2112,49=>2113,50=>2114,51=>2115,52=>2121,53=>2122,54=>2123,55=>2124,56=>2125,57=>2126,58=>2127,59=>2128,60=>2129,61=>21210,62=>2131,63=>2132,64=>2133,65=>2134,66=>2141,67=>2142,68=>2143,69=>2144,70=>2151,71=>2152,72=>2153,73=>2161,74=>2162,75=>2163,76=>2211,77=>2212,78=>2213,79=>2214,80=>2221,81=>2222,82=>2223,83=>2231,84=>2232,85=>2233,86=>2241,87=>2242,88=>2251,89=>2252,90=>2253,91=>2254,92=>2255,93=>2311,94=>2312,95=>2313,96=>2314,97=>2315,98=>2316,99=>2317,100=>2318,101=>2321,102=>2322,103=>2323,104=>2324,105=>2331,106=>2332,107=>2333,108=>2334,109=>2341,110=>2342,111=>2343,112=>2351,113=>2352,114=>2353,115=>2361,116=>2362,117=>2363,118=>2364,119=>2365,120=>2366,121=>2367,122=>2368,123=>2371,124=>2411,125=>2421,126=>2431,127=>2432,128=>2441,129=>2442,130=>2443,131=>2451,133=>2511,134=>2512,135=>2513,136=>2514,137=>2521,138=>2522,132=>2531,139=>2541,140=>2542,141=>2543,142=>2544,143=>2545,144=>2611,145=>2612,146=>2613,147=>2621,148=>2622,149=>2711,150=>2712,151=>2713,152=>2714,153=>2721,154=>2731,155=>2732,156=>2733,157=>111,282=>112,158=>113,159=>114,160=>115,163=>121,164=>122,166=>123,167=>124,283=>131,170=>132,171=>133,172=>134,177=>211,178=>212,179=>213,180=>214,181=>221,182=>222,183=>231,184=>232,185=>233,186=>241,187=>242,188=>251,189=>252,190=>253,191=>254,192=>255,193=>311,194=>312,195=>313,196=>314,197=>321,202=>322,205=>323,208=>324,210=>331,211=>332,212=>333,213=>334,214=>335,215=>341,218=>411,220=>412,216=>413,222=>421,223=>422,225=>423,228=>424,229=>431,235=>432,231=>433,234=>434,236=>441,239=>442,238=>443,243=>453,242=>454,284=>455,245=>511,249=>512,251=>513,252=>514,285=>515,286=>521,256=>522,287=>523,259=>524,255=>531,293=>532,254=>533,288=>541,289=>542,290=>543,291=>544,260=>611,261=>612,262=>613,263=>614,264=>615,265=>616,266=>621,267=>622,268=>623,269=>624,270=>625,272=>711,273=>712,292=>713,274=>721,275=>722,277=>723,276=>724,278=>731,279=>732,280=>733,281=>734);
      foreach($tab_conv_items as $old => $new)
      {
        $old+=1000;
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_id='.$new.' WHERE entree_id='.$old.'' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id='.$new.' WHERE entree_id='.$old );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_jointure_user_entree SET entree_id='.$new.' WHERE entree_id='.$old );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-05-31 => 2011-06-05
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-05-31')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-06-05';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // champ oublié dans la création de table user depuis la maj précédente
      $DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SHOW CREATE TABLE sacoche_user' );
      if(!strpos($DB_ROW['Create Table'],'eleve_langue'))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD eleve_langue TINYINT(3) UNSIGNED NOT NULL DEFAULT "132" COMMENT "Langue choisie pour le socle." AFTER eleve_classe_id' );
      }
      // ajout de 2 tables pour les parents
      // Les supprimer si elles existent : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), ces anciennes tables éventuellement existantes ne seraient pas réinitialisées.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_jointure_parent_eleve' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_jointure_parent_eleve ( parent_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, eleve_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, resp_legal_num ENUM("1","2") COLLATE utf8_unicode_ci NOT NULL DEFAULT "1", UNIQUE KEY parent_eleve_key (parent_id,eleve_id), KEY parent_id (parent_id), KEY eleve_id (eleve_id) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_parent_adresse' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_parent_adresse ( parent_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, adresse_ligne1 VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT "", adresse_ligne2 VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT "", adresse_ligne3 VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT "", adresse_ligne4 VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT "", adresse_postal_code MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, adresse_postal_libelle VARCHAR(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT "", adresse_pays_nom VARCHAR(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT "", PRIMARY KEY (parent_id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
      // ajout du profil parent
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_profil user_profil ENUM("eleve","parent","professeur","directeur","administrateur") CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "eleve" ' );
      // ajout des droits parents
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"eleve","parent,eleve") WHERE parametre_nom IN("droit_modifier_mdp","droit_voir_referentiels","droit_voir_score_bilan","droit_voir_algorithme") ' );
      // modification de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_nom parametre_nom VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      $eleve_bilans = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="droit_eleve_bilans"' );
      $eleve_socle  = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="droit_eleve_socle" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="droit_eleve_bilans"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="droit_eleve_socle" ' );
      $droit_bilan_moyenne_score      = (strpos($eleve_bilans,'BilanMoyenneScore')     !==FALSE) ? 'parent,eleve' : '' ;
      $droit_bilan_pourcentage_acquis = (strpos($eleve_bilans,'BilanPourcentageAcquis')!==FALSE) ? 'parent,eleve' : '' ;
      $droit_bilan_note_sur_vingt     = (strpos($eleve_bilans,'BilanNoteSurVingt')     !==FALSE) ? 'parent,eleve' : '' ;
      $droit_socle_acces              = (strpos($eleve_socle ,'SocleAcces')            !==FALSE) ? 'parent,eleve' : '' ;
      $droit_socle_pourcentage_acquis = (strpos($eleve_socle ,'SoclePourcentageAcquis')!==FALSE) ? 'parent,eleve' : '' ;
      $droit_socle_etat_validation    = (strpos($eleve_socle ,'SocleEtatValidation')   !==FALSE) ? 'parent,eleve' : '' ;
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_bilan_moyenne_score"      , "'.$droit_bilan_moyenne_score.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_bilan_pourcentage_acquis" , "'.$droit_bilan_pourcentage_acquis.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_bilan_note_sur_vingt"     , "'.$droit_bilan_note_sur_vingt.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_socle_acces"              , "'.$droit_socle_acces.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_socle_pourcentage_acquis" , "'.$droit_socle_pourcentage_acquis.'")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_socle_etat_validation"    , "'.$droit_socle_etat_validation.'")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-06-05 => 2011-06-06
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-06-05')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-06-06';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification de la valeur par défaut pour eleve_langue
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE eleve_langue eleve_langue TINYINT(3) UNSIGNED NOT NULL DEFAULT "100" COMMENT "Langue choisie pour le socle." ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'SET group_concat_max_len = 5000');
      $listing_id = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT GROUP_CONCAT(DISTINCT user_id SEPARATOR ",") AS listing_id FROM sacoche_user LEFT JOIN sacoche_groupe ON sacoche_user.eleve_classe_id=sacoche_groupe.groupe_id WHERE niveau_id NOT IN(35,36,44,51)' );
      if($listing_id)
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET eleve_langue=100 WHERE user_id IN('.$listing_id.')' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-06-06 => 2011-06-08
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-06-06')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-06-08';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // correctif de la modification des identifiants d'item du 2011-05-31
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=0 WHERE entree_id=1000' );
      // ajout de 2 paramètres
      $valeur = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="modele_eleve"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("modele_parent" , "'.$valeur.'")' );
      $valeur = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="modele_professeur"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("modele_directeur" , "'.$valeur.'")' );
      // modification de sacoche_jointure_parent_eleve
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_parent_eleve CHANGE resp_legal_num resp_legal_num ENUM( "0", "1", "2" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "0" ' );
      // suppression des vignettes (sans rapport avec la base, mais à effectuer, à cause du changement de la couleur de fond)
      list($x,$y,$dossier) = (HEBERGEUR_INSTALLATION=='multi-structures') ? explode('_',SACOCHE_STRUCTURE_BD_NAME) : '0' ;
      FileSystem::effacer_fichiers_temporaires(CHEMIN_DOSSIER_BADGE.$dossier , 0);
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-06-08 => 2011-06-24
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-06-08')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-06-24';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification de sacoche_jointure_parent_eleve
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_parent_eleve CHANGE resp_legal_num resp_legal_num TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_parent_eleve ADD resp_legal_envoi TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 ' );
      // type de la colonne section_id
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_section CHANGE section_id section_id TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_entree  CHANGE section_id section_id TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT 0 ' );
      // type des colonnes user_nom et user_prenom
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_nom    user_nom    VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_prenom user_prenom VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-06-24 => 2011-07-03
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-06-24')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-07-03';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de niveaux BTS et modification du champ correspondant
      if(empty($reload_sacoche_niveau))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET niveau_ordre = 199 WHERE niveau_id = 4 ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_niveau CHANGE niveau_ref niveau_ref VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 121, 0, 161,  "1BTS1", "310.....11.", "BTS 1 an") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 122, 0, 162,  "1BTS2", "311.....21.", "BTS 2 ans, 1e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 123, 0, 163,  "2BTS2", "311.....22.", "BTS 2 ans, 2e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 124, 0, 164,  "1BTS3", "312.....31.", "BTS 3 ans, 1e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 125, 0, 165,  "2BTS3", "312.....32.", "BTS 3 ans, 2e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 126, 0, 166,  "3BTS3", "312.....33.", "BTS 3 ans, 3e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 131, 0, 171, "1BTS1A", "370.....11.", "BTS Agricole 1 an") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 132, 0, 172, "1BTS2A", "371.....21.", "BTS Agricole 2 ans, 1e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 133, 0, 173, "2BTS2A", "371.....22.", "BTS Agricole 2 ans, 2e année") ' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-07-03 => 2011-07-18
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-07-03')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-07-18';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification des identifiants d'item pour qu'ils correspondent à ceux de LPC : infos enfin disponibles pour le palier 1 ! (RAS pour les identifiants des compétences)
      $tab_conv_items = array(1=>3111,2=>3112,3=>3113,4=>3121,5=>3122,6=>3123,7=>3124,8=>3125,9=>3131,10=>3132,11=>3133,12=>3141,13=>3142,14=>3143,15=>3144,16=>3145,17=>3151,18=>3152,19=>3153,20=>3154,21=>3161,22=>3162,23=>3163,24=>3311,25=>3312,26=>3313,27=>3314,28=>3315,29=>3316,30=>3317,31=>3318,32=>3321,33=>3322,34=>3323,35=>3324,36=>3325,37=>3326,38=>3331,39=>3332,40=>3333,41=>3341,42=>3342,43=>3611,44=>3621,45=>3622,46=>3623);
      foreach($tab_conv_items as $new => $old)
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_id='.$new.' WHERE entree_id='.$old.'' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id='.$new.' WHERE entree_id='.$old );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_jointure_user_entree SET entree_id='.$new.' WHERE entree_id='.$old );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-07-18 => 2011-08-02
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-07-18')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-08-02';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de 3 paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("gepi_url" , "")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("gepi_rne" , "")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("gepi_certificat_empreinte" , "")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-08-02 => 2011-08-18
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-08-02')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-08-18';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // suppression de fichiers temporaires déplacés (sans rapport avec la base, mais à effectuer)
      FileSystem::effacer_fichiers_temporaires(CHEMIN_DOSSIER_COOKIE,0);
      FileSystem::effacer_fichiers_temporaires(CHEMIN_DOSSIER_RSS,0);
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-08-18 => 2011-08-20
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-08-18')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-08-20';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification de sacoche_jointure_parent_eleve
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_parent_eleve DROP resp_legal_envoi ' );
      // retrait des contacts
      $DB_SQL = 'DELETE sacoche_jointure_parent_eleve ';
      $DB_SQL.= 'FROM sacoche_jointure_parent_eleve ';
      $DB_SQL.= 'WHERE resp_legal_num>2 ';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL );
      $DB_SQL = 'DELETE sacoche_parent_adresse ';
      $DB_SQL.= 'FROM sacoche_parent_adresse ';
      $DB_SQL.= 'LEFT JOIN sacoche_jointure_parent_eleve USING (parent_id) ';
      $DB_SQL.= 'WHERE eleve_id IS NULL ';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL );
      $DB_SQL = 'DELETE sacoche_user ';
      $DB_SQL.= 'FROM sacoche_user ';
      $DB_SQL.= 'LEFT JOIN sacoche_jointure_parent_eleve ON sacoche_user.user_id=sacoche_jointure_parent_eleve.parent_id ';
      $DB_SQL.= 'WHERE user_profil="parent" AND parent_id IS NULL ';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-08-20 => 2011-10-01
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-08-20')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-10-01';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // Le nom de l'évaluation n'était plus associé aux notes mémorisées...
      $DB_SQL = 'SELECT sacoche_saisie.prof_id,eleve_id,devoir_id,item_id,devoir_info,user_nom,user_prenom ';
      $DB_SQL.= 'FROM sacoche_saisie ';
      $DB_SQL.= 'INNER JOIN sacoche_devoir USING (devoir_id) ';
      $DB_SQL.= 'INNER JOIN sacoche_user ON sacoche_saisie.prof_id = sacoche_user.user_id ';
      $DB_SQL.= 'WHERE saisie_info LIKE " (%"';
      $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL );
      $DB_SQL = 'UPDATE sacoche_saisie SET saisie_info=:saisie_info WHERE prof_id=:prof_id AND eleve_id=:eleve_id AND devoir_id=:devoir_id AND item_id=:item_id';
      foreach($DB_TAB as $DB_ROW)
      {
        $saisie_info = $DB_ROW['devoir_info'].' ('.$DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']{0}.'.)';
        $DB_VAR = array(':prof_id'=>$DB_ROW['prof_id'],':eleve_id'=>$DB_ROW['eleve_id'],':devoir_id'=>$DB_ROW['devoir_id'],':item_id'=>$DB_ROW['item_id'],':saisie_info'=>$saisie_info);
        DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-10-01 => 2011-10-09
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-10-01')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-10-09';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // suppression du champ groupe_prof_id pour un groupe de besoin ou un groupe d'évaluation (utilisation de la table de jointure existance à la place)
      $DB_SQL = 'SELECT groupe_id,groupe_prof_id FROM sacoche_groupe WHERE groupe_type IN ("besoin","eval")';
      $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL );
      $DB_SQL = 'REPLACE INTO sacoche_jointure_user_groupe (user_id,groupe_id,jointure_pp) VALUES(:user_id,:groupe_id,1)';
      foreach($DB_TAB as $DB_ROW)
      {
        $DB_VAR = array(':user_id'=>$DB_ROW['groupe_prof_id'],':groupe_id'=>$DB_ROW['groupe_id']);
        DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
      }
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_groupe DROP groupe_prof_id' );
      // ajout d'un champ pour lister avec qui on partage un devoir
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir ADD devoir_partage TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      // corrections d'entrées tronquées à cause d'un fichier SQL pas en UTF8
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="Très insuffisant." WHERE parametre_nom="note_legende_RR" AND parametre_valeur="Tr" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="Très satisfaisant." WHERE parametre_nom="note_legende_VV" AND parametre_valeur="Tr" ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-10-09 => 2011-10-23
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-10-09')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-10-23';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de 2 index
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_saisie ADD UNIQUE saisie_key ( eleve_id , devoir_id , item_id )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD INDEX ( user_id_gepi )' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-10-23 => 2011-11-12
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-10-23')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-11-12';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // depuis le 05/06/2010 les nouvelles bases voyaient le champ "sacoche_parametre" défini à VARCHAR(25) au lieu de VARCHAR(30)
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_nom parametre_nom VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ""' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_bilan_pourcentage_acquis" WHERE parametre_nom="droit_bilan_pourcentage_a"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_bilan_note_sur_vingt"     WHERE parametre_nom="droit_bilan_note_sur_ving"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_socle_pourcentage_acquis" WHERE parametre_nom="droit_socle_pourcentage_a"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_socle_etat_validation"    WHERE parametre_nom="droit_socle_etat_validati"' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-11-12 => 2011-11-20
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-11-12')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-11-20';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'un paramètre
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_voir_grilles_items" , "directeur,professeur,parent,eleve")' );
      // ajout d'un niveau cycle
      if(empty($reload_sacoche_niveau))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET niveau_ordre=niveau_ordre+3 WHERE niveau_id IN(14,15)' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES (5,1,5,"P0","","Cycle 1 (PS-GS)")' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-11-20 => 2011-12-24
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-11-20')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-12-24';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de 2 paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_gerer_referentiel" , "profcoordonnateur")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("droit_gerer_ressource" , "professeur")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-12-24 => 2011-12-30
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-12-24')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2011-12-30';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // maj liens
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6D10","http://sacoche.sesamath.net/__ress_html/2354/117.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6D11","http://sacoche.sesamath.net/__ress_html/2354/118.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6D12","http://sacoche.sesamath.net/__ress_html/2354/119.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6D20","http://sacoche.sesamath.net/__ress_html/2354/120.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6D21","http://sacoche.sesamath.net/__ress_html/2354/121.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6D22","http://sacoche.sesamath.net/__ress_html/2354/122.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N10","http://sacoche.sesamath.net/__ress_html/2354/123.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N11","http://sacoche.sesamath.net/__ress_html/2354/124.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N12","http://sacoche.sesamath.net/__ress_html/2354/125.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N13","http://sacoche.sesamath.net/__ress_html/2354/126.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N14","http://sacoche.sesamath.net/__ress_html/2354/127.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N20","http://sacoche.sesamath.net/__ress_html/2354/128.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N21","http://sacoche.sesamath.net/__ress_html/2354/129.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N22","http://sacoche.sesamath.net/__ress_html/2354/130.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N23","http://sacoche.sesamath.net/__ress_html/2354/131.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N24","http://sacoche.sesamath.net/__ress_html/2354/132.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N25","http://sacoche.sesamath.net/__ress_html/2354/133.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N26","http://sacoche.sesamath.net/__ress_html/2354/134.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N27","http://sacoche.sesamath.net/__ress_html/2354/135.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N28","http://sacoche.sesamath.net/__ress_html/2354/136.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N29","http://sacoche.sesamath.net/__ress_html/2354/137.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N30","http://sacoche.sesamath.net/__ress_html/2354/138.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N31","http://sacoche.sesamath.net/__ress_html/2354/139.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N32","http://sacoche.sesamath.net/__ress_html/2354/140.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N33","http://sacoche.sesamath.net/__ress_html/2354/141.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6N34","http://sacoche.sesamath.net/__ress_html/2354/142.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G10","http://sacoche.sesamath.net/__ress_html/2354/143.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G11","")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G12","")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G13","")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G20","http://sacoche.sesamath.net/__ress_html/2354/147.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G21","")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G22","")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G23","http://sacoche.sesamath.net/__ress_html/2354/150.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G30","http://sacoche.sesamath.net/__ress_html/2354/151.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G31","http://sacoche.sesamath.net/__ress_html/2354/152.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G32","http://sacoche.sesamath.net/__ress_html/2354/153.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G33","http://sacoche.sesamath.net/__ress_html/2354/154.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G34","")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G40","http://sacoche.sesamath.net/__ress_html/2354/155.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G41","")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G42","http://sacoche.sesamath.net/__ress_html/2354/157.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G43","http://sacoche.sesamath.net/__ress_html/2354/158.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G50","http://sacoche.sesamath.net/__ress_html/2354/159.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G51","http://sacoche.sesamath.net/__ress_html/2354/160.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G52","http://sacoche.sesamath.net/__ress_html/2354/161.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G53","http://sacoche.sesamath.net/__ress_html/2354/162.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G54","http://sacoche.sesamath.net/__ress_html/2354/163.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G55","http://sacoche.sesamath.net/__ress_html/2354/164.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G60","http://sacoche.sesamath.net/__ress_html/2354/165.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G61","http://sacoche.sesamath.net/__ress_html/2354/166.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6G62","http://sacoche.sesamath.net/__ress_html/2354/167.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M10","http://sacoche.sesamath.net/__ress_html/2354/168.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M11","http://sacoche.sesamath.net/__ress_html/2354/169.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M12","http://sacoche.sesamath.net/__ress_html/2354/170.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M13","")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M20","http://sacoche.sesamath.net/__ress_html/2354/172.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M21","http://sacoche.sesamath.net/__ress_html/2354/173.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M22","http://sacoche.sesamath.net/__ress_html/2354/174.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M23","http://sacoche.sesamath.net/__ress_html/2354/175.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M30","http://sacoche.sesamath.net/__ress_html/2354/176.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M31","http://sacoche.sesamath.net/__ress_html/2354/177.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M32","http://sacoche.sesamath.net/__ress_html/2354/178.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M33","http://sacoche.sesamath.net/__ress_html/2354/179.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M34","http://sacoche.sesamath.net/__ress_html/2354/180.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M35","")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M36","http://sacoche.sesamath.net/__ress_html/2354/182.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M40","http://sacoche.sesamath.net/__ress_html/2354/183.html")' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://college-guitres.com/cahier-de-textes/mathenpoche.php?compet=6M41","http://sacoche.sesamath.net/__ress_html/2354/184.html")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2011-12-30 => 2012-01-04
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2011-12-30')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-01-04';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // la validation d'une association de PP aux classes supprimait les associations des propriétaires de groupes de besoin et sans doute aussi des évaluations sur une sélection d'élèves ; problème potentiel probablement depuis 2010-10-29
      $DB_SQL = 'SELECT groupe_id, user_id, SUM(jointure_pp) as nb_pp ';
      $DB_SQL.= 'FROM sacoche_jointure_user_groupe ';
      $DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
      $DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
      $DB_SQL.= 'WHERE groupe_type IN ("besoin","eval") AND user_profil="professeur" ';
      $DB_SQL.= 'GROUP BY groupe_id ';
      $DB_SQL.= 'HAVING nb_pp=0 ';
      $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL );
      // Lors de la rectification, si plusieurs profs sont associés aux groupes, il n'est pas dit que l'on tombe sur le bon...
      $DB_SQL = 'REPLACE INTO sacoche_jointure_user_groupe (user_id,groupe_id,jointure_pp) VALUES(:user_id,:groupe_id,1)';
      foreach($DB_TAB as $DB_ROW)
      {
        $DB_VAR = array(':user_id'=>$DB_ROW['user_id'],':groupe_id'=>$DB_ROW['groupe_id']);
        DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-01-04 => 2012-01-13
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-01-04')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-01-13';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'un paramètre
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("mdp_longueur_mini" , "6")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-01-13 => 2012-01-16
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-01-13')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-01-16';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification de la syntaxe de mémorisation des profs associés à un devoir (le caractère "," est préférable à "_" car dans une recherche LIKE ce dernier signifie "un caractère" (même si on peut l'échapper).
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_devoir SET devoir_partage=REPLACE(devoir_partage,"_",",")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-01-16 => 2012-01-28
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-01-16')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-01-28';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification d'un champ de "sacoche_parametre"
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_nom parametre_nom VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ""' );
      // récupération de paramètre
      $denomination = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="denomination"' );
      // modification de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="webmestre_uai" WHERE parametre_nom="uai"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="webmestre_denomination" WHERE parametre_nom="denomination"' );
      // ajout de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "etablissement_denomination"     , :denomination )' , array(':denomination'=>$denomination) );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "etablissement_adresse1"         , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "etablissement_adresse2"         , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "etablissement_adresse3"         , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "etablissement_telephone"        , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "etablissement_fax"              , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "etablissement_courriel"         , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "mois_bascule_annee_scolaire"    , "8" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "enveloppe_horizontal_gauche"    , "110" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "enveloppe_horizontal_milieu"    , "100" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "enveloppe_horizontal_droite"    , "20" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "enveloppe_vertical_haut"        , "50" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "enveloppe_vertical_milieu"      , "45" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "enveloppe_vertical_bas"         , "20" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_infos_etablissement"   , "adresse,telephone,fax,courriel" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_infos_responsables"    , "non" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_nombre_exemplaires"    , "un" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_marge_gauche"          , "5" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_marge_droite"          , "5" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_marge_haut"            , "5" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_marge_bas"             , "10" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_bulletin_appreciation_generale" , "directeur,profprincipal" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_bulletin_impression_pdf"        , "directeur,aucunprof" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_modele_defaut"                        , "item_synthese" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_item_appreciation_matiere_presence"   , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_item_appreciation_matiere_longueur"   , "150" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_item_appreciation_generale_presence"  , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_item_pourcentage_acquis_presence"     , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_item_pourcentage_acquis_modifiable"   , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_item_pourcentage_acquis_classe"       , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_item_note_moyenne_score_presence"     , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_item_note_moyenne_score_modifiable"   , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_item_note_moyenne_score_classe"       , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_socle_pourcentage_acquis_presence"    , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_socle_etat_validation_presence"       , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "bulletin_socle_appreciation_generale_presence" , "1" )' );
      // ajouter de deux entrées dans sacoche_jointure_groupe_periode pour paramétrer les bulletins
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_groupe_periode ADD bulletin_modele     ENUM("item_detail","item_synthese","socle_detail","socle_synthese")   COLLATE utf8_unicode_ci NOT NULL DEFAULT "item_synthese" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_groupe_periode ADD bulletin_etat       ENUM("ferme_vierge","ouvert_profs","ouvert_synthese","ferme_complet") COLLATE utf8_unicode_ci NOT NULL DEFAULT "ferme_vierge" ' );
      // ajout d'une table pour les bulletins
      // La supprimer si elle existe : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), cette ancienne table éventuellement existante ne serait pas réinitialisée.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_bulletin' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_bulletin ( periode_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, matiere_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0, prof_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, eleve_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, bulletin_note TINYINT(3) UNSIGNED NOT NULL DEFAULT 0, bulletin_pourcentage TINYINT(3) UNSIGNED NOT NULL DEFAULT 0, bulletin_appreciation VARCHAR(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT "", UNIQUE KEY bulletin_key (periode_id,matiere_id,prof_id,eleve_id), KEY periode_id (periode_id), KEY matiere_id (matiere_id), KEY prof_id (prof_id), KEY eleve_id (eleve_id) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-01-28 => 2012-02-08
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-01-28')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-02-08';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // maj liens suite passage https
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET item_lien=REPLACE(item_lien,"http://sacoche.sesamath.net","https://sacoche.sesamath.net")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-02-08 => 2012-02-13
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-02-08')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-02-13';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // récupération des informations sur les matières
      $listing_matieres_id = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="matieres"' );
      $DB_TAB_communes     = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SELECT matiere_id, matiere_nb_demandes, matiere_ordre FROM sacoche_matiere WHERE matiere_id IN('.$listing_matieres_id.')');
      $DB_TAB_specifiques  = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SELECT matiere_id, matiere_nb_demandes, matiere_ordre, matiere_ref, matiere_nom FROM sacoche_matiere WHERE matiere_partage=0');
      // nouvelles tables sacoche_matiere (intégration native de 1900 matières) et sacoche_matiere_famille
      $reload_sacoche_matiere = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_matiere.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes ); // Attention, sur certains LCS ça bloque au dela de 40 instructions MySQL (mais un INSERT multiple avec des milliers de lignes ne pose pas de pb).
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      $reload_sacoche_matiere_famille = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_matiere_famille.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes ); // Attention, sur certains LCS ça bloque au dela de 40 instructions MySQL (mais un INSERT multiple avec des milliers de lignes ne pose pas de pb).
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // incrément des ids pour éviter tout souci
      $increment = 11000;
      $tab_tables = array('sacoche_bulletin','sacoche_jointure_user_matiere','sacoche_demande','sacoche_referentiel','sacoche_referentiel_domaine');
      foreach($tab_tables as $table_nom)
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE '.$table_nom.' SET matiere_id=matiere_id+'.$increment );
      }
      // Nettoyage champ devenu obsolète
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="matieres"' );
      // mise à jour des ids & infos des matières de l'établissement
      $tab_id_old_to_id_new = array(1=>901,2=>316,3=>328,4=>315,5=>327,6=>50,7=>813,8=>1001,9=>320,10=>332,11=>207,12=>406,13=>201,14=>613,15=>623,16=>629,17=>708,18=>54,19=>51,20=>202,21=>321,22=>333,23=>414,24=>40,25=>41,26=>42,27=>43,28=>46,29=>103,30=>507,31=>2757,32=>326,33=>338,34=>9991,35=>323,36=>335,37=>318,38=>330,39=>399,40=>3128,41=>711,42=>9992,43=>606,90=>9901,91=>9902,92=>9903,93=>9904,94=>9905,95=>9906,96=>9907,97=>9908,99=>9999);
      foreach($DB_TAB_communes as $DB_ROW)
      {
        $id_old_incremente = $DB_ROW['matiere_id']+$increment;
        $id_new = $tab_id_old_to_id_new[$DB_ROW['matiere_id']];
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere SET matiere_active=1, matiere_nb_demandes='.$DB_ROW['matiere_nb_demandes'].', matiere_ordre='.$DB_ROW['matiere_ordre'].' WHERE matiere_id='.$id_new );
        foreach($tab_tables as $table_nom)
        {
          DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE '.$table_nom.' SET matiere_id='.$id_new.' WHERE matiere_id='.$id_old_incremente );
        }
      }
      foreach($DB_TAB_specifiques as $DB_ROW)
      {
        $id_old_incremente = $DB_ROW['matiere_id']+$increment;
        $matiere_existante_id = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT matiere_id FROM sacoche_matiere WHERE matiere_ref="'.$DB_ROW['matiere_ref'].'"' );
        if(!$matiere_existante_id)
        {
          $id_new = 10000 + $DB_ROW['matiere_id'] - 100;
          DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES('.$id_new.', 1, 0, 0, '.$DB_ROW['matiere_nb_demandes'].', '.$DB_ROW['matiere_ordre'].', "'.$DB_ROW['matiere_ref'].'", "'.$DB_ROW['matiere_nom'].'")' );
        }
        else
        {
          $id_new = $matiere_existante_id;
          DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere SET matiere_active=1, matiere_nb_demandes='.$DB_ROW['matiere_nb_demandes'].', matiere_ordre='.$DB_ROW['matiere_ordre'].' WHERE matiere_id='.$id_new );
          DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel SET referentiel_partage_etat="non" WHERE matiere_id='.$id_old_incremente );
        }
        foreach($tab_tables as $table_nom)
        {
          DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE '.$table_nom.' SET matiere_id='.$id_new.' WHERE matiere_id='.$id_old_incremente );
        }
      }
      // Nettoyage des entrées qui resteraient (anciennes matières plus utilisées)
      foreach($tab_tables as $table_nom)
      {
        $DB_TAB_obsoletes = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SELECT matiere_id FROM '.$table_nom.' WHERE matiere_id>'.$increment.' GROUP BY matiere_id');
        foreach($DB_TAB_obsoletes as $DB_ROW)
        {
          $id_old_decremente = $DB_ROW['matiere_id'] - $increment;
          $id_new = isset($tab_id_old_to_id_new[$id_old_decremente]) ? $tab_id_old_to_id_new[$id_old_decremente] : 0 ;
          if($id_new)
          {
            DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE '.$table_nom.' SET matiere_id='.$id_new.' WHERE matiere_id='.$DB_ROW['matiere_id'] );
          }
          else
          {
            DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM '.$table_nom.' WHERE matiere_id='.$DB_ROW['matiere_id'] );
          }
        }
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-02-13 => 2012-02-15
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-02-13')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-02-15';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // Inversion des noms de deux matières
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere SET matiere_ref="P3A", matiere_nom="3 Principaux éléments de mathématiques"  WHERE matiere_id=9903');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere SET matiere_ref="P3B", matiere_nom="3 Culture scientifique et technologique" WHERE matiere_id=9904');
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-02-15 => 2012-02-19
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-02-15')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-02-19';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // récupération des informations sur les niveaux, cycles, paliers
      $listing_niveaux_id = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="niveaux"' );
      $listing_cycles_id  = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="cycles"' );
      $listing_paliers_id = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="paliers"' );
      // nouvelles tables sacoche_niveau et sacoche_niveau_famille
      $reload_sacoche_niveau = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_niveau.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes ); // Attention, sur certains LCS ça bloque au dela de 40 instructions MySQL (mais un INSERT multiple avec des milliers de lignes ne pose pas de pb).
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      $reload_sacoche_niveau_famille = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_niveau_famille.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes ); // Attention, sur certains LCS ça bloque au dela de 40 instructions MySQL (mais un INSERT multiple avec des milliers de lignes ne pose pas de pb).
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // ajout champ table sacoche_socle_palier
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_palier ADD palier_actif BOOLEAN NOT NULL DEFAULT 0 AFTER palier_id , ADD INDEX ( palier_actif )' );
      // Nettoyage champs devenu obsolète
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom IN ( "niveaux","cycles","paliers" )' );
      // Mise à jour des champs utilisés
      if($listing_niveaux_id)
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET niveau_actif=1 WHERE niveau_id IN('.$listing_niveaux_id.')');
      }
      if($listing_cycles_id)
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau SET niveau_actif=1 WHERE niveau_id IN('.$listing_cycles_id.')');
      }
      if($listing_paliers_id)
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_palier SET palier_actif=1 WHERE palier_id IN('.$listing_paliers_id.')');
      }
      // modification / suppression de clefs
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_bulletin DROP INDEX bulletin_key , DROP INDEX eleve_id , ADD PRIMARY KEY ( eleve_id , periode_id , matiere_id , prof_id ) ');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_demande DROP INDEX user_id');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_devoir_item DROP INDEX devoir_item_key , DROP INDEX devoir_id , ADD PRIMARY KEY ( devoir_id , item_id ) ');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_groupe_periode DROP INDEX groupe_periode_key , DROP INDEX groupe_id , ADD PRIMARY KEY ( groupe_id , periode_id ) ');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_parent_eleve DROP INDEX parent_eleve_key , DROP INDEX parent_id , ADD PRIMARY KEY ( parent_id , eleve_id ) ');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_entree DROP INDEX validation_entree_key , DROP INDEX user_id , ADD PRIMARY KEY ( user_id , entree_id ) ');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_groupe DROP INDEX user_groupe_key , DROP INDEX user_id , ADD PRIMARY KEY ( user_id , groupe_id ) ');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_matiere DROP INDEX user_matiere_key , DROP INDEX user_id , ADD PRIMARY KEY ( user_id , matiere_id ) ');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_pilier DROP INDEX validation_pilier_key , DROP INDEX user_id , ADD PRIMARY KEY ( user_id , pilier_id ) ');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel DROP INDEX referentiel_id , DROP INDEX matiere_id , ADD PRIMARY KEY ( matiere_id , niveau_id ) ');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_saisie DROP INDEX saisie_key , DROP INDEX devoir_id , ADD PRIMARY KEY ( devoir_id , eleve_id , item_id ) ');
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-02-19 => 2012-02-22
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-02-19')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-02-22';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // La dernière version n'installait pas la table sacoche_socle_palier (virgule mal placée)
      $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SHOW TABLE STATUS LIKE "sacoche_socle_palier"');
      if(empty($DB_TAB))
      {
        $reload_sacoche_socle_palier = TRUE;
        $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_socle_palier.sql');
        DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes ); // Attention, sur certains LCS ça bloque au dela de 40 instructions MySQL (mais un INSERT multiple avec des milliers de lignes ne pose pas de pb).
        DB::close(SACOCHE_STRUCTURE_BD_NAME);
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-02-22 => 2012-02-23
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-02-22')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-02-23';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // Les 2 dernières versions n'installaient pas la table sacoche_jointure_user_pilier (mot ADD en trop)
      $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SHOW TABLE STATUS LIKE "sacoche_jointure_user_pilier"');
      if(empty($DB_TAB))
      {
        $reload_sacoche_jointure_user_pilier = TRUE;
        $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_jointure_user_pilier.sql');
        DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes ); // Attention, sur certains LCS ça bloque au dela de 40 instructions MySQL (mais un INSERT multiple avec des milliers de lignes ne pose pas de pb).
        DB::close(SACOCHE_STRUCTURE_BD_NAME);
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-02-23 => 2012-02-29
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-02-23')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-02-29';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'une table sacoche_selection_item
      // La supprimer si elle existe : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), cette ancienne table éventuellement existante ne serait pas réinitialisée.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_selection_item' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_selection_item ( selection_item_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT, user_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, selection_item_nom   VARCHAR(60)  COLLATE utf8_unicode_ci NOT NULL DEFAULT "", selection_item_liste TEXT COLLATE utf8_unicode_ci NOT NULL DEFAULT "", PRIMARY KEY (selection_item_id), KEY user_id (user_id) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-02-29 => 2012-03-05
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-02-29')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-03-05';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification de 2 références d'ENT
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"scolastance02","ent_02") WHERE parametre_nom="connexion_nom"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"entea","ent_alsace") WHERE parametre_nom="connexion_nom"' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-03-05 => 2012-03-12
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-03-05')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-03-12';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'un champ pour paramétrer une date limite d'autoévaluation, et de deux champs pour l'adresse d'un sujet ou d'un corrigé
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir ADD devoir_autoeval_date DATE NOT NULL DEFAULT "0000-00-00" AFTER devoir_visible_date ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir ADD devoir_doc_sujet   VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir ADD devoir_doc_corrige VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-03-12 => 2012-03-29
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-03-12')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-03-29';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification des deux champs pour l'adresse d'un sujet ou d'un corrigé
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir CHANGE devoir_doc_sujet devoir_doc_sujet     VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir CHANGE devoir_doc_corrige devoir_doc_corrige VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      $base_id = (strpos(SACOCHE_STRUCTURE_BD_NAME,'sac_base_')===0) ? substr(SACOCHE_STRUCTURE_BD_NAME,9) : '0' ;
      $url_dossier_devoirs = URL_DIR_DEVOIR.$base_id.'/';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_devoir SET devoir_doc_sujet   = CONCAT("'.$url_dossier_devoirs.'",devoir_doc_sujet)   WHERE devoir_doc_sujet!="" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_devoir SET devoir_doc_corrige = CONCAT("'.$url_dossier_devoirs.'",devoir_doc_corrige) WHERE devoir_doc_corrige!="" ' );
      // ajout d'un champ pour ajouter un message à une demande d'évaluation
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_demande ADD demande_messages TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      // fusionner user_statut + user_statut_date = user_sortie_date
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_statut_date user_sortie_date DATE NOT NULL DEFAULT "9999-12-31" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_sortie_date="9999-12-31" WHERE user_statut=1 ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user DROP user_statut ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD INDEX user_sortie_date ( user_sortie_date ) ' );
      // modification des deux champs pour les identifiants externes
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_id_ent  user_id_ent  VARCHAR( 63 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Paramètre renvoyé après une identification CAS depuis un ENT (ça peut être le login, mais ça peut aussi être un numéro interne à l\'ENT...)." ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_id_gepi user_id_gepi VARCHAR( 63 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Login de l\'utilisateur dans Gepi utilisé pour un transfert note/moyenne vers un bulletin." ' );
      // ajout d'une table sacoche_message
      // La supprimer si elle existe : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), cette ancienne table éventuellement existante ne serait pas réinitialisée.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_message' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_message ( message_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT, user_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, message_debut_date DATE NOT NULL DEFAULT "0000-00-00", message_fin_date DATE NOT NULL DEFAULT "0000-00-00", message_destinataires TEXT COLLATE utf8_unicode_ci NOT NULL DEFAULT "", message_contenu TINYTEXT COLLATE utf8_unicode_ci NOT NULL DEFAULT "", PRIMARY KEY (message_id), KEY user_id (user_id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-03-29 => 2012-05-01
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-03-29')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-05-01';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modification sacoche_jointure_groupe_periode
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_groupe_periode DROP bulletin_modele, DROP bulletin_etat' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_groupe_periode ADD officiel_releve ENUM( "", "1vide","2rubrique","3synthese","4complet" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "", ADD officiel_bulletin ENUM( "", "1vide","2rubrique","3synthese","4complet" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "", ADD officiel_palier1 ENUM( "", "1vide","2rubrique","3synthese","4complet" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "", ADD officiel_palier2 ENUM( "", "1vide","2rubrique","3synthese","4complet" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "", ADD officiel_palier3 ENUM( "", "1vide","2rubrique","3synthese","4complet" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ""' );
      // modification sacoche_parametre
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="officiel_infos_etablissement" WHERE parametre_nom="bulletin_infos_etablissement"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="officiel_infos_responsables"  WHERE parametre_nom="bulletin_infos_responsables" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="officiel_nombre_exemplaires"  WHERE parametre_nom="bulletin_nombre_exemplaires" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="officiel_marge_gauche"        WHERE parametre_nom="bulletin_marge_gauche"       ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="officiel_marge_droite"        WHERE parametre_nom="bulletin_marge_droite"       ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="officiel_marge_haut"          WHERE parametre_nom="bulletin_marge_haut"         ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="officiel_marge_bas"           WHERE parametre_nom="bulletin_marge_bas"          ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_officiel_bulletin_appreciation_generale" WHERE parametre_nom="droit_bulletin_appreciation_generale"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_officiel_bulletin_impression_pdf" WHERE parametre_nom="droit_bulletin_impression_pdf"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_voir_officiel_releve_archive"            , "directeur,professeur" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_voir_officiel_bulletin_archive"          , "directeur,professeur" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_voir_officiel_socle_archive"             , "directeur,professeur" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_releve_appreciation_generale"   , "directeur,profprincipal" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_releve_impression_pdf"          , "directeur,aucunprof" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_socle_appreciation_generale"    , "directeur,profprincipal" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_socle_impression_pdf"           , "directeur,aucunprof" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom IN ( "bulletin_modele_defaut","cycles","paliers,bulletin_item_appreciation_matiere_presence,bulletin_item_appreciation_matiere_longueur,bulletin_item_appreciation_generale_presence,bulletin_item_pourcentage_acquis_presence,bulletin_item_pourcentage_acquis_modifiable,bulletin_item_pourcentage_acquis_classe,bulletin_item_note_moyenne_score_presence,bulletin_item_note_moyenne_score_modifiable,bulletin_item_note_moyenne_score_classe,bulletin_socle_pourcentage_acquis_presence,bulletin_socle_etat_validation_presence,bulletin_socle_appreciation_generale_presence" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_tampon_signature"                     , "remplacer" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_appreciation_rubrique"         , "300" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_appreciation_generale"         , "400" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_moyenne_scores"                , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_pourcentage_acquis"            , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_cases_nb"                      , "4" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_aff_coef"                      , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_aff_socle"                     , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_aff_domaine"                   , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_aff_theme"                     , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_couleur"                       , "oui" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_legende"                       , "oui" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_appreciation_rubrique"       , "200" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_appreciation_generale"       , "400" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_moyenne_scores"              , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_note_sur_20"                 , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_moyenne_classe"              , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_couleur"                     , "oui" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_legende"                     , "oui" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_socle_appreciation_rubrique"          , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_socle_appreciation_generale"          , "400" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_socle_only_presence"                  , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_socle_pourcentage_acquis"             , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_socle_etat_validation"                , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_socle_couleur"                        , "oui" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_socle_legende"                        , "oui" )' );
      // suppression sacoche_bulletin
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_bulletin' );
      // ajout d'une table sacoche_officiel_saisie
      // La supprimer si elle existe : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), cette ancienne table éventuellement existante ne serait pas réinitialisée.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_officiel_saisie' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_officiel_saisie ( officiel_type ENUM("releve","bulletin","palier1","palier2","palier3") COLLATE utf8_unicode_ci NOT NULL DEFAULT "bulletin", periode_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0, eleve_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, rubrique_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "matiere_id ou pilier_id ; 0 pour l\'appréciation générale", prof_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "0 pour la note, avec commentaire dans saisie_appreciation si report non automatique", saisie_note DECIMAL(3,1) UNSIGNED DEFAULT NULL COMMENT "sur 20, à multiplier par 5 pour avoir le pourcentage", saisie_appreciation TEXT COLLATE utf8_unicode_ci NOT NULL DEFAULT "", PRIMARY KEY ( eleve_id , officiel_type , periode_id , rubrique_id , prof_id ), KEY officiel_type (officiel_type), KEY periode_id (periode_id), KEY rubrique_id (rubrique_id), KEY prof_id (prof_id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
      // ajout d'une table sacoche_officiel_archive
      // La supprimer si elle existe : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), cette ancienne table éventuellement existante ne serait pas réinitialisée.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_officiel_archive' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_officiel_archive ( user_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, officiel_type ENUM("releve","bulletin","palier1","palier","palier3") COLLATE utf8_unicode_ci NOT NULL DEFAULT "bulletin", periode_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "mis à zéro lors d\'un changement d\'année", archive_date DATE NOT NULL DEFAULT "0000-00-00", archive_document MEDIUMBLOB NOT NULL, KEY user_id (user_id), KEY officiel_type (officiel_type), KEY periode_id (periode_id) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
      // ajout d'une table sacoche_signature
      // La supprimer si elle existe : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), cette ancienne table éventuellement existante ne serait pas réinitialisée.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_signature' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_signature ( user_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "0 pour le tampon de l\'établissement", signature_contenu  MEDIUMBLOB NOT NULL, signature_format CHAR(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT "", signature_largeur SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0, signature_hauteur SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0, PRIMARY KEY (user_id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
      // modification des deux champs pour les identifiants externes ; déjà fait le 29/03/2012 mais oublié pour les nouveaux établissements
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_id_ent  user_id_ent  VARCHAR( 63 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Paramètre renvoyé après une identification CAS depuis un ENT (ça peut être le login, mais ça peut aussi être un numéro interne à l\'ENT...)." ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_id_gepi user_id_gepi VARCHAR( 63 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Login de l\'utilisateur dans Gepi utilisé pour un transfert note/moyenne vers un bulletin." ' );
      // ajout d'une ligne dans sacoche_parametre pour retenir la liste des paliers car on en a besoin pour personnaliser certains menus
      $listing_paliers_id = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT GROUP_CONCAT(palier_id SEPARATOR ",") AS listing_paliers_id FROM sacoche_socle_palier WHERE palier_actif=1 ORDER BY palier_ordre ASC' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "liste_paliers_actifs" , "'.$listing_paliers_id.'" )' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-05-01 => 2012-05-14
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-05-01')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-05-14';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_moyenne_generale"      , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_releve_modifier_statut"   , "directeur,aucunprof" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_bulletin_modifier_statut" , "directeur,aucunprof" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_socle_modifier_statut"    , "directeur,aucunprof" )' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-05-14 => 2012-05-21
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-05-14')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-05-21';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // renommage d'un champ de sacoche_parametre
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"restreindre","tampon") WHERE parametre_nom="officiel_tampon_signature"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"remplacer","signature_ou_tampon") WHERE parametre_nom="officiel_tampon_signature"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"superposer","signature_ou_tampon") WHERE parametre_nom="officiel_tampon_signature"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur=REPLACE(parametre_valeur,"juxtaposer","signature_ou_tampon") WHERE parametre_nom="officiel_tampon_signature"' );
      // renommage de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_officiel_releve_voir_archive" WHERE parametre_nom="droit_voir_officiel_releve_archive"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_officiel_bulletin_voir_archive" WHERE parametre_nom="droit_voir_officiel_bulletin_archive"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_officiel_socle_voir_archive" WHERE parametre_nom="droit_voir_officiel_socle_archive"' );
      // 7 dates de saisies trouvées vides dans ma base pour des évals sur des élèves sélectionnés vers le 10/10/2010...
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_saisie SET saisie_date="2010-10-10", saisie_visible_date="2010-10-10" WHERE saisie_date="0000-00-00" ' );
      // modification mineure de sacoche_officiel_saisie
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_officiel_saisie CHANGE periode_id periode_id MEDIUMINT( 8 ) UNSIGNED NOT NULL DEFAULT 0 ' );
      // conversion de sacoche_officiel_archive (vide) en sacoche_officiel_fichier
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_officiel_archive' );
      // La supprimer si elle existe : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), cette ancienne table éventuellement existante ne serait pas réinitialisée.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_officiel_fichier' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_officiel_fichier ( user_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, officiel_type ENUM("releve","bulletin","palier1","palier","palier3") COLLATE utf8_unicode_ci NOT NULL DEFAULT "bulletin", periode_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0, fichier_date DATE NOT NULL DEFAULT "0000-00-00", UNIQUE KEY user_id (user_id,officiel_type,periode_id), KEY officiel_type (officiel_type), KEY periode_id (periode_id) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-05-21 => 2012-06-03
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-05-21')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-06-03';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // enregistrement des signatures en base64 sinon souci lors de sauvegarde/restauration de bases
      $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SELECT user_id, signature_contenu FROM sacoche_signature');
      if(!empty($DB_TAB))
      {
        foreach($DB_TAB as $DB_ROW)
        {
          DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_signature SET signature_contenu=:signature_contenu WHERE user_id='.$DB_ROW['user_id'] , array(':signature_contenu'=>base64_encode($DB_ROW['signature_contenu'])) );
        }
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-06-03 => 2012-06-07
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-06-03')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-06-07';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'un champs pour enregistrer les préférences de l'utilisateur relatives aux messages d'accueil
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_param_accueil VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "user,alert,info,help,ecolo"' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-06-07 => 2012-06-25
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-06-07')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-06-25';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // valeur renommée dans sacoche_niveau_famille
      if(empty($reload_sacoche_niveau_famille))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_niveau_famille SET niveau_famille_nom="Cycles (primaire, collège, lycée)" WHERE niveau_famille_id=1' );
      }
      // ajout de matières
      if(empty($reload_sacoche_matiere))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9911, 0, 1,  99, 0, 255, "APS"  , "Apprendre à porter secours") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9912, 0, 1,  99, 0, 255, "PSC1" , "Prévention et secours civiques de niveau 1") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9913, 0, 1,  99, 0, 255, "PSC2" , "Prévention et secours civiques de niveau 2") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9921, 0, 1,  99, 0, 255, "APER" , "Attestation de première éducation à la route") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9922, 0, 1,  99, 0, 255, "ASSR1", "Attestation scolaire de sécurité routière de niveau 1") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9923, 0, 1,  99, 0, 255, "ASSR2", "Attestation scolaire de sécurité routière de niveau 2") ' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-06-25 => 2012-06-28
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-06-25')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-06-28';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // correctifs de champs mal initialisés
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_officiel_releve_modifier_statut"   WHERE parametre_nom="droit_officiel_releve_changer_etat"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_officiel_bulletin_modifier_statut" WHERE parametre_nom="droit_officiel_bulletin_changer_etat"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_officiel_socle_modifier_statut"    WHERE parametre_nom="droit_officiel_socle_changer_etat"' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-06-28 => 2012-07-07
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-06-28')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-07-07';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // déplacement matière FLE
      $tab_tables = array(
        'sacoche_matiere'               => 'matiere_id',
        'sacoche_jointure_user_matiere' => 'matiere_id',
        'sacoche_demande'               => 'matiere_id',
        'sacoche_referentiel'           => 'matiere_id',
        'sacoche_referentiel_domaine'   => 'matiere_id',
        'sacoche_officiel_saisie'       => 'rubrique_id'
      );
      foreach($tab_tables as $table_nom => $table_champ)
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE '.$table_nom.' SET '.$table_champ.'=300 WHERE '.$table_champ.'=397' );
      }
      // ajout d'une famille de matières
      if(empty($reload_sacoche_matiere_famille))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere_famille VALUES ( 45, 3, "Métiers des arts appliqués (suite)") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere_famille SET matiere_famille_nom="Métiers des arts appliqués" WHERE matiere_famille_id=27 ' );
      }
      // ajout de matières
      if(empty($reload_sacoche_matiere))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (  74, 0, 0, 100, 0, 255, "DECPR", "Découverte professionnelle") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (  91, 0, 0, 100, 0, 255, "EPE"  , "Etude personnalisée encadrée") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (  92, 0, 0, 100, 0, 255, "ATELP", "Atelier de professionnalisation") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (  93, 0, 0, 100, 0, 255, "FILOC", "Formation d\'initiative locale") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (  94, 0, 0, 100, 0, 255, "AT-PX", "Ateliers principaux") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (  95, 0, 0, 100, 0, 255, "AT-CO", "Ateliers complémentaires") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 387, 0, 0,   3, 0, 255, "ALL8" , "Littérature étrangère en allemand") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 388, 0, 0,   3, 0, 255, "AGL8" , "Littérature étrangère en anglais") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 389, 0, 0,   3, 0, 255, "ARA8" , "Littérature étrangère en arabe") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 390, 0, 0,   3, 0, 255, "CHI8" , "Littérature étrangère en chinois") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 391, 0, 0,   3, 0, 255, "DAN8" , "Littérature étrangère en danois") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 392, 0, 0,   3, 0, 255, "ESP8" , "Littérature étrangère en espagnol") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 393, 0, 0,   3, 0, 255, "ITA8" , "Littérature étrangère en italien") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 394, 0, 0,   3, 0, 255, "JAP8" , "Littérature étrangère en japonais") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 395, 0, 0,   3, 0, 255, "POR8" , "Littérature étrangère en portugais") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 396, 0, 0,   3, 0, 255, "NEE8" , "Littérature étrangère en néerlandais") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 397, 0, 0,   3, 0, 255, "NEE8" , "Littérature étrangère en néerlandais") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 398, 0, 0,   3, 0, 255, "RUS8" , "Littérature étrangère en russe") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 521, 0, 0,   5, 0, 255, "SSPOL", "Sciences sociales et politiques") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 522, 0, 0,   5, 0, 255, "ECOAP", "Economie approfondie") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 601, 0, 0,   6, 0, 255, "EIST" , "Enseignement intégré de science et technologie") ' ); // Créée en attendant que la matière apparaisse officiellement...
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 720, 0, 0,   7, 0, 255, "TREAL", "Technologie de réalisation") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 738, 0, 0,   7, 0, 255, "STECH", "Sciences et technologie") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 739, 0, 0,   7, 0, 255, "TFABR", "Technologie de fabrication") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 918, 0, 0,   9, 0, 255, "CDESG", "Culture du design graphique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 919, 0, 0,   9, 0, 255, "CTYPO", "Culture typographique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 920, 0, 0,   9, 0, 255, "PPGRA", "Pratique plastique & graphique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES ( 921, 0, 0,   9, 0, 255, "DANAL", "Dessin analytique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (1234, 0, 0,  12, 0, 255, "CDIRP", "Conception dévelop. industrialisation realisation produits") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (1753, 0, 0,  17, 0, 255, "AHUDD", "Architecture, habitat & urbanisme, développement durable") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (3088, 0, 0,  30, 0, 255, "ECAGT", "Ecologie agronomie et territoires") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (3089, 0, 0,  30, 0, 255, "MAPHY", "Microbiologie appliquée & physiopathologie") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (3244, 0, 0,  32, 0, 255, "AOCCL", "Anatomie-occlusodontie") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (3657, 0, 0,  36, 0, 255, "DGEMC", "Droit et grands enjeux du monde contemporain") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (3659, 0, 0,  36, 0, 255, "ENVEJ", "Environnement économique et juridique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (3660, 0, 0,  36, 0, 255, "COJAT", "Cadre organisationnel juridique activité touristique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (3661, 0, 0,  36, 0, 255, "DR-VD", "Droit et veille juridique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (3731, 0, 0,  37, 0, 255, "ISCNU", "Informatique et sciences du numérique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (3831, 0, 0,  38, 0, 255, "ECOOR", "Economie et organisation") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4059, 0, 0,  40, 0, 255, "MERTC", "Tourisme et territoire") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4060, 0, 0,  40, 0, 255, "MCPT" , "Mercatique conception prestation touristique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4154, 0, 0,  41, 0, 255, "GRCLI", "Gestion de la relation client") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4155, 0, 0,  41, 0, 255, "GINFT", "Gestion de l\'information touristique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4156, 0, 0,  41, 0, 255, "IMMED", "Information et multimedias") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4157, 0, 0,  41, 0, 255, "ITOUR", "Information et tourismatique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4158, 0, 0,  41, 0, 255, "CCOMM", "Culture de la communication") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4501, 0, 0,  45, 0, 255, "HUMOD", "Humanités modernes") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4502, 0, 0,  45, 0, 255, "SMAJU", "Stratégie marketing juridique") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4503, 0, 0,  45, 0, 255, "CPRTE", "Cultures et pratiques techniques") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4504, 0, 0,  45, 0, 255, "PPLME", "Pratiques plastiques et médiations") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4505, 0, 0,  45, 0, 255, "IPRRE", "Innovation, prospective et recherche") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4506, 0, 0,  45, 0, 255, "LEXRE", "Laboratoire expérimentation et recherche") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4507, 0, 0,  45, 0, 255, "MACPR", "Macro-projet") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4508, 0, 0,  45, 0, 255, "MREPR", "Mémoire de recherche professionnel") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (4509, 0, 0,  45, 0, 255, "MELVE", "Mémoire en langue vivante etrangère") ' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-07-07 => 2012-09-10
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-07-07')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-09-10';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // renommage et modification de la table sacoche_signature (pour sacoche_image)
      // supprimer la table sacoche_image si elle existe : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), cette table éventuellement existante gène.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_image' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'RENAME TABLE sacoche_signature TO sacoche_image' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_image CHANGE signature_contenu image_contenu MEDIUMBLOB NOT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_image CHANGE signature_format image_format CHAR( 4 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ""' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_image CHANGE signature_largeur image_largeur SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT 0' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_image CHANGE signature_hauteur image_hauteur SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT 0' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_image ADD image_objet ENUM( "signature", "photo" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "photo" AFTER user_id' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_image SET image_objet="signature"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_image ADD INDEX image_objet( image_objet )' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-09-10 => 2012-10-06
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-09-10')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-10-06';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modifier un paramètre CAS pour l'ENT Mirabelle
      $connexion_nom = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="connexion_nom"' );
      if($connexion_nom=='mirabelle')
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="www.ent-place.fr" WHERE parametre_nom="cas_serveur_host"' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-10-06 => 2012-10-09
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-10-06')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-10-09';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'un paramètre de département pour les connexions ENT (sert dans l'affichage du menu déroulant) ; à initialiser.
      $connexion_nom = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="connexion_nom"' );
      $tab_correspondance = array
      (
        'sacoche' => '', 'perso' => '', 'saml' => '',
        'ent_02' => '02', 'ent_02_v2' => '02',
        'ent_04' => '04', 'ent_04_v2' => '04',
        'ent_52' => '52', 'ent_52_v2' => '52',
        'ent_06' => '06',
        'elie' => '23',
        'ent_27' => '27',
        'ent_38' => '38',
        'cybercolleges42' => '42',
        'mirabelle' => '57',
        'ent_60' => '60',
        'laclasse' => '69',
        'cartabledesavoie' => '73',
        'ent_77' => '77',
        'ent_80' => '80',
        'ent_90' => '90',
        'ent_92' => '92',
        'celia' => '93',
        'lilie' => '93',
        'ent_95' => '95',
        'ent_reunion' => '974'
      );
      if(isset($tab_correspondance[$connexion_nom]))
      {
        $connexion_departement = $tab_correspondance[$connexion_nom];
      }
      else
      {
        $webmestre_uai = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="webmestre_uai"' );
        $connexion_departement = ($webmestre_uai) ? substr($webmestre_uai,1,2) : '' ;
      }
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "connexion_departement" , "'.$connexion_departement.'" )' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-10-09 => 2012-10-10
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-10-09')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-10-10';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // Intégration des champs professionnels de SEGPA comme nouvelles matières.
      if(empty($reload_sacoche_matiere_famille))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere_famille VALUES ( 98, 3, "Champs professionnels en SEGPA")' );
      }
      if(empty($reload_sacoche_matiere))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9801, 0, 0,  98, 0, 255, "CPHAB", "Habitat")' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9802, 0, 0,  98, 0, 255, "CPHAS", "Hygiène - Alimentation - Services")' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9803, 0, 0,  98, 0, 255, "CPERE", "Espace rural et environnement")' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9804, 0, 0,  98, 0, 255, "CPVDM", "Vente - Distribution - Magasinage")' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_matiere VALUES (9805, 0, 0,  98, 0, 255, "CPPI" , "Production industrielle")' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-10-10 => 2012-10-26
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-10-10')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-10-26';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // Passage de champs DATE à NULL possible et par défaut.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user   CHANGE user_connexion_date  user_connexion_date  DATETIME NULL DEFAULT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user   CHANGE user_tentative_date  user_tentative_date  DATETIME NULL DEFAULT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir CHANGE devoir_autoeval_date devoir_autoeval_date DATE     NULL DEFAULT NULL' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user   SET  user_connexion_date=NULL WHERE  user_connexion_date="0000-00-00 00:00:00"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user   SET  user_tentative_date=NULL WHERE  user_tentative_date="0000-00-00 00:00:00"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_devoir SET devoir_autoeval_date=NULL WHERE devoir_autoeval_date="0000-00-00"' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-10-26 => 2012-11-05
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-10-26')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-11-05';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_only_socle"           , "0"   )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_retroactif"           , "non" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_conv_sur20"           , "0"   )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_only_socle"         , "0"   )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_retroactif"         , "non" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_barre_acquisitions" , "1"   )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "calcul_retroactif"                    , "non" )' );
      // ajouter une entrée dans sacoche_referentiel pour paramétrer la prise en compte des évaluations antérieures par défaut
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel ADD referentiel_calcul_retroactif ENUM("non","oui") COLLATE utf8_unicode_ci NOT NULL DEFAULT "non" COMMENT "Avec ou sans prise en compte des évaluations antérieures. Valeur surclassant la configuration par défaut." AFTER referentiel_calcul_limite' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-11-05 => 2012-11-12
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-11-05')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-11-12';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de paramètres et renommage d'autres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_releve_etat_acquisition"    , "parent,eleve" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_etat_acquisition" , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_releve_moyenne_score"          WHERE parametre_nom="droit_bilan_moyenne_score"'      );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_releve_pourcentage_acquis"     WHERE parametre_nom="droit_bilan_pourcentage_acquis"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="droit_releve_conversion_sur_20"      WHERE parametre_nom="droit_bilan_note_sur_vingt"'     );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="officiel_releve_conversion_sur_20"   WHERE parametre_nom="officiel_releve_conv_sur20"'     );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_nom="officiel_bulletin_conversion_sur_20" WHERE parametre_nom="officiel_bulletin_note_sur_20"'  );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-11-12 => 2012-11-17
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-11-12')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-11-17';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modif champ table sacoche_parametre
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_valeur parametre_valeur VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
      // nouvelle table sacoche_officiel_assiduite
      $reload_sacoche_officiel_assiduite = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_officiel_assiduite.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // ajout de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_assiduite"   , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_assiduite" , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_socle_assiduite"    , "0" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_releve_ligne_supplementaire"   , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_ligne_supplementaire" , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_socle_ligne_supplementaire"    , "" )' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-11-17 => 2012-12-01
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-11-17')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-12-01';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modifier un paramètre CAS pour l'ENT de Haute-Marne sur ItsLearning
      $connexion_nom = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="connexion_nom"' );
      if($connexion_nom=='ent_52_v2')
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="cas-enthautemarne" WHERE parametre_nom="cas_serveur_root"' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-12-01 => 2012-12-12
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-12-01')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-12-12';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modif index et champ table sacoche_image
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_image DROP INDEX image_objet' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_image DROP PRIMARY KEY , ADD UNIQUE (user_id , image_objet )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_image CHANGE image_objet image_objet ENUM( "signature", "photo", "logo" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "photo"' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-12-12 => 2012-12-14
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-12-12')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-12-14';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modif champ table sacoche_message
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_message CHANGE message_contenu message_contenu TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ' );
      // modif champ table sacoche_user
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_param_accueil user_param_accueil VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "user,alert,messages,demandes,help,ecolo" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_param_accueil=REPLACE(user_param_accueil,"info","messages,demandes")' );
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-12-14 => 2012-12-27
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-12-14')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2012-12-27';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // modifier un paramètre CAS pour l'ENT Agora06
      $connexion_nom = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="connexion_nom"' );
      if($connexion_nom=='ent_06')
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="www.agora06.fr" WHERE parametre_nom="cas_serveur_host"' );
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2012-12-27 => 2013-01-05
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2012-12-27')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-01-05';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // distinction des ENT agora06 et ent_nice
      $connexion_nom = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="connexion_nom"' );
      if($connexion_nom=='ent_06')
      {
        $sesamath_uai = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="sesamath_uai"' );
        if(substr($sesamath_uai,0,3)=='006')
        {
          DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="agora06" WHERE parametre_nom="connexion_nom"' );
        }
        else
        {
          DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="ent_nice" WHERE parametre_nom="connexion_nom"' );
          DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="cas.enteduc.fr" WHERE parametre_nom="cas_serveur_host"' );
        }
      }
    }
  }

  // ////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAJ 2013-01-05 => 2013-01-18
  // ////////////////////////////////////////////////////////////////////////////////////////////////////

  if($version_base_structure_actuelle=='2013-01-05')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-01-18';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // re-suppression ligne dans sacoche_parametre car figurait toujours dans le sql de création.
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="droit_eleve_demandes"' );
      // nouvelle table sacoche_user_profil
      $reload_sacoche_user_profil = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_user_profil.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // modif champ table sacoche_user
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user CHANGE user_profil user_profil_sigle CHAR( 3 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" COMMENT "Nomenclature issue de la BCN (table n_fonction_filiere) et de user_profils SDET." ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_profil_sigle="ADM" WHERE user_profil_sigle="adm" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_profil_sigle="ELV" WHERE user_profil_sigle="ele" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_profil_sigle="TUT" WHERE user_profil_sigle="par" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_profil_sigle="ENS" WHERE user_profil_sigle="pro" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user SET user_profil_sigle="DIR" WHERE user_profil_sigle="dir" ' );
      // transfert ligne "mdp_longueur_mini" de sacoche_parametre vers sacoche_user_profil
      $mdp_longueur_mini = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="mdp_longueur_mini"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user_profil SET user_profil_mdp_longueur_mini="'.$mdp_longueur_mini.'" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="mdp_longueur_mini"' );
      // transfert ligne "duree_inactivite" de sacoche_parametre vers sacoche_user_profil
      $duree_inactivite = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="duree_inactivite"' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user_profil SET user_profil_duree_inactivite="'.$duree_inactivite.'" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="duree_inactivite"' );
      // transfert lignes "modele_*" de sacoche_parametre vers sacoche_user_profil
      $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_nom, parametre_valeur FROM sacoche_parametre WHERE parametre_nom LIKE "modele_%"');
      foreach($DB_TAB as $DB_ROW)
      {
        $profil_type = substr($DB_ROW['parametre_nom'],7);
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_user_profil SET user_profil_login_modele="'.$DB_ROW['parametre_valeur'].'" WHERE user_profil_type="'.$profil_type.'"' );
      }
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom LIKE "modele_%"' );
      // modification valeurs "droit_*" de sacoche_parametre
      $tab_corresp = array( 'directeur'=>'DIR' , 'professeur'=>'ENS,DOC,EDU' , 'profprincipal'=>'ENS,ONLY_PP' , 'profcoordonnateur'=>'ENS,DOC,EDU,ONLY_COORD' , 'parent'=>'TUT' , 'eleve'=>'ELV' );
      $DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_nom, parametre_valeur FROM sacoche_parametre WHERE parametre_nom LIKE "droit_%"');
      foreach($DB_TAB as $DB_ROW)
      {
        $tab_valeurs = explode(',',$DB_ROW['parametre_valeur']);
        foreach($tab_valeurs as $key => $valeur)
        {
          if( ($valeur=='aucunprof') || ($valeur=='') )
          {
            unset($tab_valeurs[$key]);
          }
          else
          {
            $tab_valeurs[$key] = $tab_corresp[$valeur];
          }
        }
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.implode(',',$tab_valeurs).'" WHERE parametre_nom="'.$DB_ROW['parametre_nom'].'"' );
      }
      // ajout de paramètre
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_saisir_assiduite" , "DIR,EDU" )' );
    }
  }

  if($version_base_structure_actuelle=='2013-01-18')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-01-28';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de clefs, sauf si bonnes requêtes de création de la table déjà passées
      if(empty($reload_sacoche_user_profil))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user_profil ADD INDEX user_profil_obligatoire ( user_profil_obligatoire ) ');
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user_profil ADD INDEX user_profil_type ( user_profil_type ) ');
      }
    }
  }

  if($version_base_structure_actuelle=='2013-01-28')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-01-31';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_fusion_niveaux"              , "1"   )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_releve_corriger_appreciation"   , "DIR" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_bulletin_corriger_appreciation" , "DIR" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_officiel_socle_corriger_appreciation"    , "DIR" )' );
    }
  }

  if($version_base_structure_actuelle=='2013-01-31')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-02-04';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout champ table sacoche_message
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_message ADD message_dests_cache TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_message SET message_dests_cache="," ' );
      // modifications table sacoche_officiel_saisie
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_officiel_saisie CHANGE eleve_id eleve_ou_classe_id  MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "id élève ou classe suivant le champ saisie_type" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_officiel_saisie ADD saisie_type ENUM("eleve","classe") COLLATE utf8_unicode_ci NOT NULL DEFAULT "eleve" COMMENT "indique si la saisie concerne un élève ou une classe" AFTER prof_id , ADD INDEX ( saisie_type ) ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_officiel_saisie DROP PRIMARY KEY , ADD PRIMARY KEY ( eleve_ou_classe_id , officiel_type , periode_id , rubrique_id , prof_id , saisie_type )  ' );
      // ajout index table sacoche_user
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD INDEX eleve_classe_id ( eleve_classe_id ) ' );
      // correctif nouvelles entrées table sacoche_matiere
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_matiere SET matiere_usuelle=0 WHERE matiere_id>'.ID_MATIERE_PARTAGEE_MAX );
    }
  }

  if($version_base_structure_actuelle=='2013-02-04')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-02-22';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de 2 paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_acquis_texte_nombre" , "1" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "officiel_bulletin_acquis_texte_code"   , "1" )' );
      // ajout champ table sacoche_devoir
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_devoir ADD devoir_fini TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 ' );
      // ajout champs table sacoche_user (en prévision, car pas encore utilisé à ce jour)
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_naissance_date DATE NULL DEFAULT NULL AFTER user_prenom ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_email VARCHAR(63) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" AFTER user_naissance_date ' );
    }
  }

  if($version_base_structure_actuelle=='2013-02-22')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-03-20';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "cas_serveur_url_login"    , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "cas_serveur_url_logout"   , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "cas_serveur_url_validate" , "" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_affecter_langue" , "DIR,ENS,ONLY_LV" )' );
      // Intégration des niveaux APSA comme nouveaux niveaux.
      if(empty($reload_sacoche_niveau_famille))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau_famille VALUES ( 10, 2, 3, "APSA (activités physiques, sportives et artistiques)") ' );
      }
      if(empty($reload_sacoche_niveau))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 211, 0, 10,  32, "N1", "", "Niveau 1") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 212, 0, 10,  50, "N2", "", "Niveau 2") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 213, 0, 10,  80, "N3", "", "Niveau 3") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 214, 0, 10,  90, "N4", "", "Niveau 4") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 215, 0, 10, 180, "N5", "", "Niveau 5") ' );
      }
    }
  }

  if($version_base_structure_actuelle=='2013-03-20')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-04-22';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // nouvelle table sacoche_brevet_serie
      $reload_sacoche_brevet_serie = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_brevet_serie.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // nouvelle table sacoche_brevet_epreuve
      $reload_sacoche_brevet_epreuve = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_brevet_epreuve.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // nouvelle table sacoche_brevet_saisie
      $reload_sacoche_brevet_saisie = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_brevet_saisie.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // nouvelle table sacoche_geo_academie
      $reload_sacoche_geo_academie = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_geo_academie.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // nouvelle table sacoche_geo_departement
      $reload_sacoche_geo_departement = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_geo_departement.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // ajout champ table sacoche_user
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD eleve_brevet_serie VARCHAR(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT "X" COMMENT "Série du brevet pour Notanet." AFTER eleve_langue ' );
      // modification sacoche_groupe
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_groupe ADD fiche_brevet ENUM( "","1vide","2rubrique","3synthese","4complet" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "" ' );
      // modification sacoche_parametre (paramètres CAS pour ENT Toutatice)
      $connexion_nom = DB::queryOne(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="connexion_nom"' );
      if($connexion_nom=='toutatice')
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="casshib/shib/toutatice" WHERE parametre_nom="cas_serveur_root" ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="https://www.toutatice.fr/casshib/shib/666666/serviceValidate" WHERE parametre_nom="cas_serveur_url_validate" ' );
      }
      // réordonner un peu la table sacoche_parametre et retirer une ligne qui ne correspond à rien
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="annee_utilisation_numero" ' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre ORDER BY parametre_nom ' );
    }
  }

  if($version_base_structure_actuelle=='2013-04-22')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-04-29';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // Ajout de niveaux
      if(empty($reload_sacoche_niveau_famille))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau_famille VALUES ( 11, 1, 8, "Métiers d\'arts") ' );
      }
      if(empty($reload_sacoche_niveau))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES (  10, 0,  2,   0,    "TPS", "0041000111.", "Maternelle, très petite section") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES (  55, 0,  4,  65,   "DIMA", "115..99911.", "Dispositif d\'initiation des métiers en alternance") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 117, 0,  7, 157,     "MC", "253.....11.", "Mention complémentaire") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 118, 0,  7, 158,   "1BP2", "254.....21.", "Brevet Professionnel 2 ans, 1e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 119, 0,  7, 159,   "2BP2", "254.....22.", "Brevet Professionnel 2 ans, 2e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 141, 0, 11, 181,  "1BMA1", "250.....11.", "BMA 1 an") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 142, 0, 11, 182,  "1BMA2", "251.....21.", "BMA 2 ans, 1e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 143, 0, 11, 183,  "2BMA2", "251.....22.", "BMA 2 ans, 2e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 151, 0, 11, 191,  "1DMA1", "315.....11.", "DMA 1 an") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 152, 0, 11, 192,  "1DMA2", "316.....21.", "DMA 2 ans, 1e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 153, 0, 11, 193,  "2DMA2", "316.....22.", "DMA 2 ans, 2e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 154, 0, 11, 194,  "2DUT1", "350.....21.", "DUT 2 ans, 1e année") ' );
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_niveau VALUES ( 155, 0, 11, 195,  "2DUT2", "350.....22.", "DUT 2 ans, 2e année") ' );
      }
    }
  }

  if($version_base_structure_actuelle=='2013-04-29')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-05-05';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout de paramètres
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_fiche_brevet_appreciation_generale" , "DIR" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_fiche_brevet_corriger_appreciation" , "DIR" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_fiche_brevet_impression_pdf"        , "DIR" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_fiche_brevet_modifier_statut"       , "DIR" )' );
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ( "droit_fiche_brevet_voir_archive"          , "DIR,ENS,DOC,EDU" )' );
      // réordonner la table sacoche_parametre
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre ORDER BY parametre_nom' );
    }
  }

  if($version_base_structure_actuelle=='2013-05-05')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-05-12';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // nouvelle table sacoche_brevet_fichier
      $reload_sacoche_brevet_fichier = TRUE;
      $requetes = file_get_contents(CHEMIN_DOSSIER_SQL_STRUCTURE.'sacoche_brevet_fichier.sql');
      DB::query(SACOCHE_STRUCTURE_BD_NAME , $requetes );
      DB::close(SACOCHE_STRUCTURE_BD_NAME);
      // vider table sacoche_brevet_saisie maintenant que les choix de fonctionnement sont arrêtés
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'TRUNCATE sacoche_brevet_saisie' );
    }
  }

  if($version_base_structure_actuelle=='2013-05-12')
  {
    if($version_base_structure_actuelle==DB_STRUCTURE_MAJ_BASE::DB_version_base())
    {
      $version_base_structure_actuelle = '2013-05-14';
      DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_base_structure_actuelle.'" WHERE parametre_nom="version_base"' );
      // ajout d'une colonne à la table sacoche_user_profil
      if(empty($reload_sacoche_user_profil))
      {
        DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user_profil ADD user_profil_mdp_date_naissance TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 0 AFTER user_profil_mdp_longueur_mini ');
      }
    }
  }

}

}
?>