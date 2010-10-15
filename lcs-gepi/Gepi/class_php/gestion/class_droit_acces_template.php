<?php
/*
 * $Id: $
*/

/**
 * Contenu � afficher dans /gestion/droit_acces.php
 *
 * @author regis
 */
class class_droit_acces_template {

  private $msg = "";
  private $statut="";
  private $valeur="";
  private $name="";
  private $texte="";
  private  $item="";
  private $enregistre="";


/**
 * Contenu � afficher dans /gestion/droit_acces.php
 *
 * @author regis
 */
  function  __construct($donneesPassee=NULL) {

	$this->enregistre=$donneesPassee;

  }

  private function enregistre($nom){
	if (isset($_POST[$nom])) {
		$temp = 'yes';
	} else {
		$temp = 'no';
	}
	if (!saveSetting($nom, $temp)) {
		$msg .= "Erreur lors de l'enregistrement de ".$nom." avec la valeur ".$temp." !<br />";
	}
  }
 
/**
 * R�cup�re les donn�es � afficher et enregistre au besoin les r�glages dans la table setting
 *
 * @var $statutPasse : Statut � r�gler
 * @var $namePasse : Nom de la variable � enregistrer dans la table setting
 * @var $valuePasse : Valeur de la variable � enregistrer dans la table setting
 * @var $textePasse : Texte � afficher dans la page
 */
  public function set_entree($statutPasse, $namePasse, $textePasse){

	if ($this->enregistre){
	  $this->enregistre($namePasse);
	}
	
	$this->item[]=array('statut' => $statutPasse, 'name' => $namePasse, 'texte' => $textePasse);

	return TRUE;

  }

/**
 * Renvoie les donn�es � afficher d'un item
 *
 */
  public function get_item(){

	return $this->item;
  }

/**
 * Renvoie les messages d'erreurs
 *
 */
  public function get_erreurs(){

	return $this->msg;
  }

/**
 * Modifie les droits dans la table droits
 *
 * @var $statutPasse : statut � mettre � jour
 * @var $titreItem : nom du droit � v�rifier
 * @var $namePasse : nom de la page � mettre � jour (\xxxxx\xxxxx.xxx)
 * @var $force : permet de forcer le droit � V avec 'yes' et � F avec toute autre valeur.
 * Laisser vide pour que le droit soit recherch� dans setting
 *
 */
  public function ouvreDroits($statutPasse, $titreItem, $namePasse, $force= NULL ){
	if ($force == NULL){
	  $droit=getSettingValue( $titreItem);
	}else{
	  $droit=$force;
	}
	if ($droit=='yes'){
	  $sql="UPDATE `droits` SET `".$statutPasse."` =  'V' 
		WHERE `id` = '".$namePasse."'";
	}else{
	  $sql="UPDATE `droits` SET `".$statutPasse."` =  'F'
		WHERE `id` = '".$namePasse."'";
	}
    $res = sql_query($sql);

	return TRUE;
  }


}
?>
