<?php
/**
 * @version : $Id: plugins.class.php 4976 2010-08-01 11:43:03Z regis $
 *
 * Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

if (!isset ($_SESSION["login"]) || !$_SESSION["login"]){header("Location: ../../logout.php?auto=1");}

if (!$_SESSION["login"]){Die();}
/**
 * Classe qui v�rifie les droits et les autorisations des pages des plugins de Gepi
 *
 * @author Julien Jocal
 */
class gepiPlugIn {

  private $_plugin = NULL;
  private $_droits = NULL;

  /**
   * Constructeur de l'objet
   *
   * @param string $plugin Nom du plugin qui correspond au champ `nom` de la table plugins
   */
  public function  __construct($plugin) {
    if (!is_string($plugin)){
      $this->affErreur(1);
    }else{
      $c = new Criteria();
      $c->add(PlugInPeer::NOM, $plugin, Criteria::EQUAL);
      $infos_plugin = PlugInPeer::doSelectOne($c);
      $this->_plugin = $infos_plugin;
    }

    if ($this->_plugin->getOuvert() != "y"){
      tentative_intrusion("2", "Tentative de lecture d'un fichier du plugin ".$infos_plugin->getNom()." qui n'est pas ouvert au public.");
      $this->_logout();
    }
  }

  /**
   * M�thode qui v�rifie les droits de chaque fichier en fonction du statut de l'utilisateur
   */
  public function verifDroits(){
    $user_statut = $_SESSION["statut"];
    global $gepiPath;
    $url = parse_url($_SERVER['REQUEST_URI']);

    if (is_null($this->_plugin)){
      $this->affErreur(2);
    }else{
      // On s'attache � v�rifier les droits de ce statut
      $this->_droits = $this->_plugin->getPlugInAutorisations();
      $fichier = substr($url['path'], (strlen($gepiPath) + 1));

      $autorisation = false;
      foreach($this->_droits as $_droit){

        if ($_droit->getFichier() == $fichier AND $_droit->getAuth() == 'V' AND $_droit->getUserStatut() == $user_statut){

          $autorisation = true;

        }
      }

      if ($autorisation === false){
        $this->affErreur(3);
        return false;
      }else{
        return true;
      }

    }

  }

  private function _logout(){
    Die("Rien � afficher");
  }

  private function affErreur($number){
    if(is_numeric($number)){
      switch ($number) {
        case "1":
          Die('Il faut pr&eacute;ciser le nom du plugin dans la cr&eacute;ation de son instance : $user_auth = new gepiPlugIn("nom_plugin");');
          break;
        case "2":
          Die('L\'objet plugin n\'est pas instanci&eacute; ou n\'existe pas !');
          break;
        case "3":
          tentative_intrusion(2, "Tentative d'ouverture d'une page du plugin ".$this->_plugin->getNom()." sans avoir les droits suffisants !");
          Die('Vous n\'avez pas les droits suffisants pour afficher cette page !');
          break;

      default:
          Die("Erreur non r&eacute;pertori&eacute;e !");
        break;
  }
    }else{
      Die("L'information pass&eacute;e � la m&eacute;thode " . __METHOD__ . " n'est pas valide !");
    }
  }

  /**
   * On propose dans l'API des m�thodes statiques pour les requ�tes SQL
   * Il faut ajouter $utiliser_pdo = 'on'; au d�but de votre fichier pour utiliser PDO
   */
    /**
     * M�thode qui permet de lancer une requ�te � la base
     *
     * @param string $sql requ�te SQL
     * @param boolean $use_PDO par d�faut false. Pass� � true, il permet d'utiliser PDO (ajouter $utiliser_pdo = 'on'; au d�but de votre fichier)
     * @return ressourceMySql
     */
    public static function _sqlQuery($sql, $use_PDO = false){
      if($use_PDO){
        return $_globals["cnx"]->exec($sql);

      }else{
        return mysql_query($sql);
      }

    }
    public static function _sqlQueryArray($sql, $use_PDO = false){
      if ($use_PDO){
        $query = self::_sqlQuery($sql, $use_PDO);
        return $query->fetchAll();
      }else{
        $query = self::_sqlQuery($sql, $use_PDO);
        return mysql_fetch_array($query);
      }
    }
    public static function _sqlQueryObject($sql, $use_PDO = false){
      if ($use_PDO){
        $query = self::_sqlQuery($sql, $use_PDO);
        return $query->fetchAll();
      }else{
        $query = self::_sqlQuery($sql, $use_PDO);
        return mysql_fetch_object($query);
      }
    }
    public static function _sqlCount ($sql, $use_PDO = false){
      if ($use_PDO){
        $query = self::_sqlQuery($sql, $use_PDO);
        $result = $query->fetchAll();
        return count($result);
      }else{
        $query = self::_sqlQuery($sql, $use_PDO);
        return mysql_num_rows($query);
      }
    }

    /**
     * M�thode qui renvoie le num�ro du champ autoincr�ment� du dernier enregistrement en INSERT
     *
     * @todo Terminer de coder ce cas avec PDO
     * @param boolean $use_PDO par d�faut false. Pass� � true, il permet d'utiliser PDO (ajouter $utiliser_pdo = 'on'; au d�but de votre fichier)
     * @return integer
     */
    public static function _sqlInsertId($use_PDO = false){
      return mysql_insert_id();
    }

}
?>
