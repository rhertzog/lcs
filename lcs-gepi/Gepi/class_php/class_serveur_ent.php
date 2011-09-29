<?php
/*
 * $Id: class_serveur_ent.php 7672 2011-08-10 05:57:45Z jjocal $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

include '../secure/serveur.inc.php';
if (in_array('domain_name', $_POST) AND in_array($_POST['domain_name'], $serveur) AND $serveur[$_POST['domain_name']]['RNE'] !== 'all'){
  $_GET['rne'] = isset($_POST['domain_name']) ? $serveur[$_POST['domain_name']]['RNE'] : NULL;
}else{
  // Dans le cas d'une utilisation multisite, si le demandeur � les droits 'all', on lui fait confiance pour demander la base qu'il souhaite
  $_GET['rne'] = isset($_POST['RNE']) ? $_POST['RNE'] : NULL;
}
$traite_anti_inject = 'no'; // pour �viter les �chappements dans les tableaux s�rialis�s
require_once("../lib/initialisationsPropel.inc.php");

/**
 * Classe qui impl�mente un serveur pour permettre � un ENT de se connecter � GEPI
 * Acc�s limit� � la lecture seule. Pour limiter les acc�s, on liste les m�thodes disponibles
 * Les logins des �l�ves existent sous la forme d'un tableau envoy� en POST par curl
 *
 * http://www.sylogix.org/projects/gepi/wiki/RefDoc_serveurressource
 *
 * @method notesEleve(), cdtDevoirsEleve(), cdtCREleve(), professeursEleve(), edtEleve(), listeElevesAvecClasse(), listeProfesseursAvecMatieres(), listeClassesAvecProfesseurs(), listeMatieresAvecNomlong()
 *
 * @author Julien Jocal
 * @license GPL
 */
class serveur_ent {

  /**
   * D�finit le type de demande (utilise le nom des m�thodes autoris�es)
   * @var string m�thode �voqu�e par la demande
   */
  private $_demande      = NULL;

  /**
   * D�finit la p�riode demand�e
   * @var integer Num�ro de la p�riode, 0 par d�faut �quivaut � toutes les p�riodes.
   */
  private $_periode   = 0;
  /**
   * liste des logins des enfants du parent qui demande (envoy� par le client)
   * @var array _enfants
   */
  private $_enfants     = array();
  /**
   * Le login ENT du demandeur (envoy� par le client)
   * @var string _login
   */
  private $_login       = NULL;
  /**
   * Le RNE de l'�tablissement du demandeur (envoy� par le client)
   * @var string RNE
   */
  private $_etab        = NULL;
  /**
   * la cl� secr�te entre le client et le serveur
   * @var string cl�
   */
  private $_api_key     = NULL;
  /**
   * Ce hash est envoy� par le client, le serveur le renvoie avec la r�ponse pour permettre au client de v�rifier qu'il s'agit bien de sa demande
   * @var string hash
   */
  private $_hash        = NULL;
  /**
   * Le nom du client sert de couple Mon/cl pour v�rifier
   * @var string Nom du client
   */
  private $_domain_name   = NULL;
  /**
   * Encodage des donn�es � renvoyer
   * @var string $_encodage vaut 'ISO' par d�faut, peut �tre plac� � 'utf8' par le client
   */
  private $_encodage      = 'ISO';

  /**
   * Format des informations envoy�es (tableau s�rialis� ou xml)
   * @var string vaut 'serialize' par d�faut, peut �tre plac� � 'xml' par le client
   */
  private $_format        = 'serialize';

  /**
   * Tableau de la liste des m�thodes autoris�es pour le client
   *
   * @var array D�finie dans le fichier de config
   */
  private $_config        = array();
  /**
   * Constructeur de la classe
   *
   * @example Si on est en multisite, il faut un cookie["RNE"] qui donne le bon RNE pour que GEPI se connecte sur la bonne base
   */
  public function __construct(){
    // On initialise toutes nos propri�t�s
    $this->setData();
    // V�rification de la cl�
    $this->_domain_name = isset($_POST['domain_name']) ? $_POST['domain_name'] : NULL;
    $this->verifKey($this->_domain_name);

    // On v�rifie que la demande est disponible
    if (!in_array($this->_demande, $this->getMethodesAutorisees())){
      $this->writeLog(__METHOD__, 'M�thode inexistante:'.$this->_demande, ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
      Die('M�thode inexistante !');
    }
    // On v�rifie si les logins des enfants envoy�s existent bien dans GEPI
    $reponse = array(); // permet de stocker les informations sur les enfants (tableau d'objets propel)
    $test = unserialize($this->_enfants);

    if (!empty ($test)){
        foreach ($test as $enfants){
        // On cherche si cet enfant existe
        $enf = EleveQuery::create()->filterByLogin($enfants)->find();

        if ($enf->isEmpty()){
          // Ce login n'existe pas dans cette base
          $this->writeLog(__METHOD__, 'login enfant inexistant : ' . $enfants, ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
          $reponse[] = 'inexistant';
        }else{
          // on recherche la r�ponse pour ce login
          $arenvoyer = $this->{$this->_demande}($enf[0]);
          $reponse[$enf[0]->getLogin()] = $arenvoyer;
        }

      } // foreach
    // le cas o� les enfants ne sont pas pr�sents
    }else{
      // On v�rifie si cette demande concerne un professeur
      $arg = '';
      if (strpos('Professeur', $this->_demande) !== false){
        $arg = UtilisateurProfessionnelQuery::create()
                                      ->filterByLogin($this->_login)
                                      ->findOne();
      }
      $reponse = $this->{$this->_demande}($arg);
    }

    if (is_array($reponse) OR ($this->_format == 'xml')){
      if ($this->_format == 'serialize'){
        echo serialize($reponse);
      }elseif ($this->_format == 'xml') {
        header ('Content-Type: text/xml;');
        echo $reponse;
      }
    }else{
      echo serialize(array('erreur'=>'service absent'));
    }

  }

  /**
   * renvoie le header du REQUEST_METHOD
   * @return string GET POST PUT ...
   */
  public function testRequestMethod(){
    return $_SERVER['REQUEST_METHOD'];
  }

  /**
   * Charge les donn�es envoy�es par le client
   *
   * @todo Mieux g�rer le cas o� la requ�te n'est pas en POST
   * @return void initialise les propri�t�s de l'objet
   */
  private function setData(){
    // On ne fonctionne qu'en POST
    if ($this->testRequestMethod() != 'POST'){
      $this->writeLog(__METHOD__, 'La demande n\'a pas �t� pass�e en POST', ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
      Die();
    }else{
      // On v�rifie que les donn�es demand�es existent
      $this->_etab        = (array_key_exists('etab', $_POST)) ? $_POST['etab'] : null;
      $this->_enfants     = (array_key_exists('enfants', $_POST)) ? $_POST['enfants'] : null;
      $this->_api_key     = (array_key_exists('api_key', $_POST)) ? $_POST['api_key'] : 'false';
      $this->_demande     = (array_key_exists('demande', $_POST)) ? $_POST['demande'] : null;
      $this->_hash        = (array_key_exists('hash', $_POST)) ? $_POST['hash'] : null;
      $this->_login       = (array_key_exists('login', $_POST)) ? $_POST['login'] : null;
      $this->_periode     = (array_key_exists('periode', $_POST)) ? $_POST['periode'] : $this->_periode;
      $this->_encodage    = (array_key_exists('encodage', $_POST) AND $_POST['encodage'] == 'utf8') ? 'utf8' : $this->_encodage;
      $this->_format      = (array_key_exists('format', $_POST) AND $_POST['format'] == 'xml') ? 'xml' : $this->_format;
      $this->_domain_name = isset($_POST['domain_name']) ? $_POST['domain_name'] : null;
    }
  }

  /**
   * Permet de v�rifier la cl� du demandeur
   *
   * @param string $demandeur
   */
  private function verifKey($demandeur){
    include '../secure/serveur.inc.php';
    if (!array_key_exists($demandeur, $serveur)){
      @$this->writeLog(__METHOD__, 'Compte inexistant ('.$demandeur.')', $_SERVER['HTTP_REFERER']);
      Die('Compte inexistant.');
    }else if ($this->_api_key != $serveur[$demandeur]['api_key']){
      $this->writeLog(__METHOD__, 'La cl� n\'est pas bonne ('.$this->_api_key.'|'.$key.')', ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
      Die('la cl� est obsol�te.');
    }else{
      $this->_config = $serveur[$demandeur];
      return true;
    }
  }

  /**
   * V�rifie si le client fait la demande depuis une adresse IP autoris�e
   *
   * @param string $demandeur
   */
  private function verifIPClient($demandeur){
    include '../secure/serveur.inc.php';
    if (!array_key_exists($demandeur, $serveur)){
      $this->writeLog(__METHOD__, 'Compte inexistant IP ('.$demandeur.')', $_SERVER['HTTP_REFERER']);
      Die('Compte inexistant IP.');
    }else if ($this->_api_key != $serveur[$demandeur]['ip']){
      $this->writeLog(__METHOD__, 'La cl� n\'est pas bonne ('.$this->_api_key.'|'.$key.')', ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
      Die('la cl� est obsol�te');
    }else{
      return true;
    }
  }

  /**
   * Renvoie la liste des m�thodes autoris�es par le serveur
   * @todo Penser � mettre � jour cette liste au fur et � mesure de la d�finition des m�thodes
   * @return array liste des m�thodes autoris�es
   */
  public function getMethodesAutorisees(){
    if (in_array('all', $this->_config['auth'])){
      return array('notesEleve', 'cdtDevoirsEleve', 'cdtCREleve', 'professeursEleve', 'edtEleve',
                 'listeElevesAvecClasse', 'listeProfesseursAvecMatieres', 'listeClassesAvecProfesseurs', 'listeMatieresAvecNomlong',
                 'cdtDevoirsProfesseur', 'cdtCRProfesseur');
    }else{
      return $this->_config['auth'];
    }
  }

  /**
   * Renvoie la liste des notes d'un �l�ve en fonction de son login pour les deux derniers mois
   *
   * @param string $_login login de l'�l�ve
   * @return array Liste des notes d'un �l�ve
   */
  public function notesEleve(eleve $_eleve){
    return array();
  }

  /**
   * Renvoie la liste des devoirs � faire pour un �l�ve (en fonction du login de l'�l�ve)
   *
   * @todo Pour le moment, on renvoie pour chaque mati�re le devoir le plus �loign� dans le temps, il faudrait renvoyer tous les devoirs dont la date est post�rieure
   * @param object Eleve $_eleve
   * @return array Liste des devoirs � faire du cdt de l'�l�ve
   */
  public function cdtDevoirsEleve(eleve $_eleve){
    $var = array();
    $now = new DateTime('now');
    $rep = CahierTexteTravailAFaireQuery::create()
                  ->orderByDateCt()
                  ->distinct()
                  ->useGroupeQuery()
                  ->useJEleveGroupeQuery()
                    ->filterByEleve($_eleve)
                  ->endUse()
                  ->endUse()
                  ->filterByDateCt($now->format('U'), Criteria::GREATER_THAN)
                ->find();

    foreach ($rep as $r){
        $var[$r->getDateCt()] = ($this->_encodage == 'utf8') ? utf8_encode($r->getContenu()) : $r->getContenu();
    }

    return $var;
  }

  /**
   * Renvoie la liste des derniers compte-rendus pour chaque enseignement auxquels est inscrit un �l�ve
   *
   * @param object Eleve $_eleve
   * @return array Liste des Compte-Rendus d'un �l�ve
   */
  public function cdtCREleve($_eleve){
    $var = array();
    $now = new DateTime('now');
    $rep = CahierTexteCompteRenduQuery::create()
                  ->orderByDateCt()
                  ->distinct()
                  ->useGroupeQuery()
                  ->useJEleveGroupeQuery()
                    ->filterByEleve($_eleve)
                  ->endUse()
                  ->endUse()
                  ->filterByDateCt(($now->format('U') - (6058000)), Criteria::GREATER_THAN)
                ->find();

    foreach ($rep as $r){
        $var[$r->getDateCt()] = ($this->_encodage == 'utf8') ? utf8_encode($r->getContenu()) : $r->getContenu();
    }

    return $var;
  }

  /**
   * Renvoie la liste des devoirs � faire donn�s pour chaque enseignement d'un professeur
   *
   * @param object UtilisateurProfessionnel $_professeur
   * @return array Liste des devoirs donn�s par le professeur avec les enseignements correspondants
   */
  public function cdtDevoirsProfesseur(UtilisateurProfessionnel $_professeur){
    $devoirs = $_professeur->getCahierTexteTravailAFairesJoinGroupe();
    $reponse = array();
    foreach ($devoirs as $devoir) {
      $reponse[$devoir->getDateCt()] = ($this->_encodage == 'utf8') ? utf8_encode($devoir->getContenu()) : $devoir->getContenu();
    }
    return $reponse;
  }
  /**
   * Renvoie la liste des derniers compte-rendus pour chaque enseignement d'un professeur
   *
   * @param object UtilisateurProfessionnel $_professeur
   * @return array Liste des Compte-Rendus du professeur avec les enseignements correspondants
   */
  public function cdtCRProfesseur(UtilisateurProfessionnel $_professeur){
    $crs = $_professeur->getCahierTexteCompteRendusJoinGroupe();
    $reponse = array();
    foreach ($crs as $cr){
      $reponse[$cr->getDateCt()] = ($this->_encodage == 'utf8') ? utf8_encode($cr->getContenu()) : $cr->getContenu();
    }
    return $reponse;
  }

  /**
   * Renvoie la liste des professeurs d'un �l�ve avec leur mati�re associ�e
   *
   * @param string $_login login de l'�l�ve
   * @return array Liste des professeurs de l'�l�ve
   */
  public function professeursEleve(eleve $eleve){
    $reponse = array();
    if (!is_object($eleve)){
      $this->writeLog(__METHOD__, 'objet inexistant', ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
      Die('Erreur prof-eleve');
    }else{
      foreach ($eleve->getGroupes() as $groupes) {
        $reponse[] = $groupes->getUtilisateurProfessionnels();
      }
    }
    return $reponse;
  }

  /**
   * Renvoie la liste des cours d'un �l�ve au cours de la semaine actuelle
   *
   * @param string $_login
   * @return array edt d'un �l�ve sous la forme d'un tableau php : array('lundi'=>array('M1'=>'Math�matiques',...), 'mardi'=>array(),...)
   */
  public function edtEleve($_login){
    return array();
  }

  /**
   * Renvoie la liste des �l�ves avec leur classe (nom, pr�nom, login, no_gep, eleonet, ele_id, sexe, naissance, classe)
   */
  public function listeElevesAvecClasse(){
    $eleves = EleveQuery::create()->find();
    $retour = ($this->_format == 'xml') ? '<?xml version=\'1.0\' encoding=\'ISO-8859-1\'?><eleves>' : array();
    foreach ($eleves as $eleve){
      $eleCla = $eleve->getJEleveClassesJoinClasse();
      $classes = array();
      foreach ($eleCla as $cla){
        $classes[] = $cla->getClasse()->getNomComplet();
      }
      if ($this->_format == 'xml'){
        $retour .= '
          <eleve>
            <nom>'.htmlspecialchars($eleve->getNom(), ENT_NOQUOTES).'</nom>
            <prenom>'.htmlspecialchars($eleve->getPrenom(), ENT_NOQUOTES).'</prenom>
            <sexe>'.$eleve->getSexe().'</sexe>
            <login>'.$eleve->getLogin().'</login>
            <eleid>'.$eleve->getEleId().'</eleid>
            <elenoet>'.$eleve->getElenoet().'</elenoet>';
        foreach ($classes as $per => $classe){
          $retour .= '
              <classe periode="'.$per.'">'.$classe.'</classe>';
        }
        $retour .= '
          </eleve>
                    ';
      }else{
        $retour[] = array($eleve->getNom(), $eleve->getPrenom(), $eleve->getSexe(), $eleve->getLogin(), $eleve->getEleId(), $eleve->getElenoet(), $classes);
      }
    }
    if ($this->_format == 'xml'){
      $retour .= '</eleves>';
    }
    return $retour;
  }

  /**
   * Renvoie la liste des professeurs avec leurs mati�res rattach�es (nom, pr�nom, login, liste mati�res)
   */
  public function listeProfesseursAvecMatieres(){
    $profs = UtilisateurProfessionnelQuery::create()
                        ->filterByStatut('professeur')
                        ->filterByEtat('actif')
                        ->find();
    $retour = ($this->_format == 'xml') ? '<?xml version=\'1.0\' encoding=\'ISO-8859-1\'?><professeurs>' : array();
    foreach ($profs as $prof) {
      $matieres = array();
      $profMat = $prof->getJProfesseursMatieressJoinMatiere();
      foreach ($profMat as $mat){
        $matieres[] = $mat->getMatiere()->getMatiere();
      }
      if ($this->_format == 'xml'){
        $retour .= '
          <professeur>
            <nom>'.$prof->getNom().'</nom>
            <prenom>'.$prof->getPrenom().'</prenom>
            <civilite>'.$prof->getCivilite().'</civilite>
            <login>'.$prof->getLogin().'</login>
            <numind>'.$prof->getNumind().'</numind>
            <email>'.$prof->getEmail().'</email>';
        foreach ($matieres as $matiere){
          $retour .= '
              <matiere>'.$matiere.'</matiere>';
        }
        $retour .= '
          </professeur>
                    ';
      }else{
        $retour[] = array($prof->getNom(), $prof->getPrenom(), $prof->getCivilite(), $prof->getLogin(), $prof->getNumind(), $prof->getEmail(), $matieres);
      }
    }
    if ($this->_format == 'xml'){
      $retour .= '</professeurs>';
    }
    return $retour;
  }

  /**
   * Renvoie la liste des classes de GEPI avec la liste des professeurs pour chaque classe
   * classe, liste prof dans cette classe
   *
   * @return array Tableau des classes de Gepi : nom, nom_complet, '', '', '', '', liste des logins des professeurs de la classe
   */

  public function listeClassesAvecProfesseurs(){
    $classes = ClasseQuery::create()->find();
    $retour = ($this->_format == 'xml') ? '<?xml version=\'1.0\' encoding=\'ISO-8859-1\'?><classes>' : array();
    foreach ($classes as $classe){
      $professeurs = ($this->_format == 'xml') ? '' : array();
      // Pour chaque classe, on liste les groupes
      $groupes = $classe->getJGroupesClassessJoinGroupe();
      foreach ($groupes as $groupe){
        $profs = $groupe->getGroupe()->getJGroupesProfesseurssJoinUtilisateurProfessionnel();
        // Puis on r�cup�re le login des professeurs qui ont au moins un enseignement dans cette classe.
        foreach ($profs as $prof){
          if ($this->_format == 'xml'){
            $professeurs .= '
            <professeur>' . $prof->getUtilisateurProfessionnel()->getlogin() . '</professeur>';
          }else{
            $professeurs[] = $prof->getUtilisateurProfessionnel()->getlogin();
          }
        }
      }
      if ($this->_format == 'xml'){
        $retour .= '
          <classe>
            <nom>'.$classe->getNom().'</nom>
            <nomcomplet>'.$classe->getNomComplet().'</nomcomplet>
            '.$professeurs.'
          </classe>
                    ';
      }else{
        $retour[] = array($classe->getNom(), $classe->getNomComplet(), '', '', '', '', $professeurs);
      }
    } // foreach ($classes as $classe){
    if ($this->_format == 'xml'){
      $retour .= '</classes>';
    }
    return $retour;
  }

  /**
   * Renvoie la liste des mati�res avec le nom long
   *
   * @return array Tableau des mati�res nom court - nom long - - - - - -
   */
  public function listeMatieresAvecNomlong(){
    $matieres = MatiereQuery::create()->orderByMatiere()->find();
    $retour = ($this->_format == 'xml') ? '<?xml version=\'1.0\' encoding=\'ISO-8859-1\'?><matieres>' : array();
    foreach ($matieres as $matiere){
      if ($this->_format == 'xml'){
        $retour .= '
          <matiere>
            <nom>'.htmlspecialchars($matiere->getMatiere()).'</nom>
            <nomcomplet>'.htmlspecialchars($matiere->getNomComplet()).'</nomcomplet>
          </matiere>
                    ';
      }else{
        $retour[] = array($matiere->getMatiere(), $matiere->getNomComplet(), '', '', '', '', array());
      }
    }
    if ($this->_format == 'xml'){
      $retour .= '</matieres>';
    }
    return $retour;
  }
  /**
   * Loggue les erreurs du serveur dans un fichier
   *
   * @param string $methode m�thode demand�e
   * @param string $message message d'erreur
   * @param string $login_demandeur login du demandeur
   */
  private function writeLog($methode, $message, $login_demandeur){
    // Du code pour �crire dans un fichier de log
    $fichier = fopen('../temp/serveur_ent.log', 'a+');
    fputs($fichier, ($this->_etab !== NULL ? $this->_etab : 'ETAB') . ' :: ' . $login_demandeur . ' = ' . $message . ' -> ' . $methode . ' ' . $_SERVER['REMOTE_ADDR'] . ".\n");
    fclose($fichier);

  }
}
$test = new serveur_ent();
?>