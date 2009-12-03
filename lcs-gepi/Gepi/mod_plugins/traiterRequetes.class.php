<?php
/**
 * @version : $Id: traiterRequetes.class.php 3248 2009-06-24 20:06:15Z jjocal $
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

/**
 * Classe qui permet de v�rifier les requ�tes demand�es par un plugin lors de son installation
 * Des m�thodes permettent ensuite de lancer ces requ�tes
 *
 * @author jjocal
 */
class traiterRequetes {

    /**
   * R�ponse donn�e apr�s la v�rification.
   *
   * @var boolean
   */
  private $_reponse = false;

  /**
   * Message d'erreur renvoy� par les diff�rentes v�rifications
   *
   * @var string Message d'erreur
   */
  private $erreur = NULL;

  /**
   * On stocke les requ�tes dans cet attribut
   *
   * @var string La requ�te demand�e
   */
  private $_requetes = NULL;

  /**
   * D�termine la liste des type de requ�tes possibles
   *
   * @var array Liste des requ�tes possibles
   */
  private $_requetes_possibles = array('insert', 'INSERT', 'create', 'CREATE', 'update', 'UPDATE', 'drop', 'DROP');

  /**
   * V�rification et envoie des requ�tes par Propel::PDO
   *
   * @param object $requetes simpleXMLElement
   */
  public function  __construct(simpleXMLElement $requetes) {

    $this->_requetes = $requetes;

    foreach ($this->_requetes as $requete) {

      // On est face � une liste de requ�tes
      if ($this->verifRequete($requete->requete) === true){
        $this->insertRequete($requete->requete);
      }else{
        $this->retourneErreur(1, $requete->requete);
      }
    }

  }

  /**
   * M�thode de v�rification de la structure des requ�tes SQL des plugins
   *
   * @param string $requete
   * @return boolean false/true
   */
  protected function verifRequete($requete){
    $test = explode(" ", trim($requete));
    if (in_array($test[0], $this->_requetes_possibles)){
      if (in_array($test[0], array('drop', 'DROP'))){
        if (in_array($test[1], array('table', 'TABLE'))){
          return true;
        }else{
          return false;
        }
      }else{
        return true;
      }
    }else{
      return false;
    }

  }

  /**
   * M�thode qui permet de lancer des requ�tes SQL vers la base lors de la cr�ation d'un plugin
   *
   * @param string $requete Requ�te SQL
   */
  protected function insertRequete($requete){

    $con = Propel::getConnection();
    if ($con->exec($requete) !== false){
      $this->_reponse = true;
    }else{
      $this->_reponse = false;
    }

  }

  /**
   * M�thode qui retourne un type d'erreur et un message qui pr�cise o� se situe l'erreur.
   *
   * @param integer $_e Type d'erreur
   * @param string $_m noeud li� � cette erreur
   */
  private function retourneErreur($_e, $_m){
    switch ($_e) {
      case 1:
        $message = 'La requ�te ' . $_m . ' dans le fichier plugin.xml ne passe pas !';
        break;
      case 2:
        $message = '';
        break;
      case 3:
        $message = '';
        break;

      default:
        $message = "pas de message d'erreur";
      break;
    }
    $this->erreur = $message;
  }

  /**
   * M�thode qui renvoie une erreur si elle existe
   *
   * @return string Message d'erreur
   */
  public function getErreur(){
    return $this->erreur;
  }

  /**
   * M�thode qui renvoie la r�ponse apr�s traitement des requ�tes
   *
   * @return boolean false/true
   */
  public function getReponse(){
    return $this->_reponse;
  }

}
?>
