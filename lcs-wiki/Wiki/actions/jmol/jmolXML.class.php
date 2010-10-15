<?php
/**
 * jmolXML.class.php
 *
 *		UTILISATION D'UN DESCRIPTIF AU FORMAT XML POUR CREER<BR />
 *		LES OBJETS HTML POUR COMMANDER L'APPLET JMOL
 * 		HERITE DE PHPJMOL L'INTERFACE DE JMOL
 *

 *
 * Description Xml
 * ---------------
 * parent JMOL
 *   1)Attribut TITLE: Titre de l'applet
 *   2)Attribut SIZE: dimension de l'applet. Ex: size="300x400", size="400"
 *   3)Attribut:
 *			a)SCRIPT: script Jmol
 *			b)MODEL: modele d'atome dont la description est contenue dans unfichier
 *			c)LOAD: script a charger dont la description est contenue dans un fichier
 *		enfant
 *		------
 *		CONTROL controle applet ou objets html pour commander l'applet Jmol
 *		1)attribut NAME: non de l'objet HTML
 *		2)attribut ctrl: type de contole HTML
 *			a)jmolLink: lien ancre
 *			b)jmolButton: bouton
 *			c)jmolSelect :selection d'option
 *			d)jmolRadio: bouton radio
 *			e)jmolCheckBox: case a cocher
 *			f)jmolTextArea: zone de texte
 *		3)attribut
 *			a)TITLE: titre ou texte accompagnant le controle
 *			b)SCRIPT ou SCRIPT1 ou SCRIPT2: script Jmol
 *			c)MODEL: modele d'atome dont la description est contenue dans unfichier
 *			d)LOAD: script a charger dont la description est contenue dans un fichier
 *				enfant
 *				-----
 *				OPTION objet HTML select ou radio
 *					a)TITLE: titre ou texte accompagnant le controle
 *					b)Attribut au choix:
 *						SCRIPT: script Jmol
 *						MODEL: modele d'atome dont la description est contenue dans unfichier
 *						LOAD: script a charger dont la description est contenue dans un fichier
 *					c)DISPLAY: inline ou block  uniquement pour bouton radio (affichage en ligne ou en etage)
 *EXEMPLE
 *<jmol title="Aspirine" size="350" script="load ./jmol/samples/aspirina.mol">
 *	<control name="recharger" ctrl="jmolLink" title="Recharger le script" script=""/>
 * 	<control name="spin" ctrl="jmolCheckBox" title="Spin" script1="spin on" script2="spin off"/>
 * 	<control name="reset" ctrl="jmolButton" title="Reset Position" script="reset"/>
 * 	<control name="couleur" ctrl="jmolSelect">
 * 		<option title="Gris lumineux" script="background lightslategray" status="selected"/>
 * 		<option title="Blanc" script="background white"/>
 * 		<option title="Jaune" script="background yellow"/>
 * 		<option title="Saumon" script="background salmon"/>
 * 		<option title="Bleu marine" script="background navy"/>
 * 	</control>
 * 	<control name="VanderWaals" ctrl="jmolRadio" title="VanderWaals" display="block">
 * 		<option title="pas de points" script="dots off" status="checked"/>
 * 		<option title="surface de vanderWaals" script="set solvent off; dots on"/>
 * 		<option title="surface accessible au solvant" script="set solvent on; dots on"/>
 * 	</control>
 * 	<control name="Commande" ctrl="jmolTextArea" title="Ligne de commande" rows="8" cols="40" script=""/>
 *</jmol>
 *
 ***   This program is free software.
 ***   You can redistribute it and/or modify it under the terms of the GNU General
 ***   Public License as published by the Free Software Foundation; either version 2
 ***   of the License, or (at your option) any later version.
 ***
 ***   This program is distributed in the hope that it will be useful, but WITHOUT ANY
 ***   WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 ***   PARTICULAR PURPOSE.
 ***   See the GNU General Public License for more details.
 ***
 ***   You should have received a copy of the GNU General Public License along with
 ***   this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 ***   Place - Suite 330, Boston, MA  02111-1307, USA.
 ***   A copy of the GPL can be found at http://www.gnu.org/copyleft/gpl.html or in
 ***   the file GNU_license.txt.
 ***
 *
 * @copyright (C) Defolie Denis
 * @author	Defolie Denis (denis.defolie@free.fr)
 * @version 1.0
 * @date 10/10/2006
 */

if (!defined("PHPJMOL") )
	define("PHPJMOL", "./jmol");

if ( !defined("CHARSET") )
	define ("CHARSET", "UTF-8");

require_once( PHPJMOL . "phpJmol.class.php");

class jmolXML extends phpJmol {
	var $method = '';
	var $script = '';
	var $control= array();
	var $controlMode = 'no';

	/**
	 * PHP 4 Contructor
	 *
	 *@param	string			$data		donnees xml
	 *@param	string 			$control	nom des controles
	 *@param	array string	 $JMOL		tableau des param�tres Jmol si xml absent
	 *
	 */
	function jmolXML($data, $control='no', $JMOL=null) {
		$this->__construct($data, $control, $JMOL);
	}

	/**
	 * PHP 5 Constructeur
	 *
	 */
	function __construct($data, $control='no', $JMOL=null)	{
		$this->controlMode = $control;

		if ( $data ) {
			$parser = new xml_parser($data);
			if (($this->root=$parser->root) === false || $this->root->tag != 'JMOL' ) {
				$this->controlMode = 'no';
				$title = $parser->error .' - ! JMOL tag';
			} else {
				$title = $this->root->attrs['TITLE'];
				$this->size = $this->root->attrs['SIZE'];
				$this->script = $this->root->attrs['SCRIPT'];

				if ( ($this->model=$this->root->attrs['MODEL']) ) {
					$this->method='MODEL';
				}
				if ( ($this->load=$this->root->attrs['LOAD']) ) {
					$this->method.='LOAD';
				}
			}
		} else {
			$this->controlMode = 'no';
			$title = $JMOL['TITLE'];
			$this->size = $JMOL['SIZE'];
			$this->script = $JMOL['SCRIPT'];

			if ( ($this->model=$JMOL['MODEL']) ) {
				$this->method='MODEL';
			}
			if( ($this->load=$JMOL['LOAD']) ) {
				$this->method.='LOAD';
			}
		}
		parent::__construct($title);
	}
	/**
	 * Appel d'un script ou modele externe
	 * Methode a implementer dans les classes d�rivees
	 *
	 * @param string $file	fichier ou autre
	 * @return string		contenu du fichier ou autre
	 */
	function loadExternalScript($file) {
		return "";
	}

	/**
	 * Init de l'applet en fonction du type de demande
	 * @public
	 */
	function jmolXMLApplet() {
		switch ($this->method) {
			case 'MODEL':
				if (!$this->script)
					$this->script = "select *";
				parent::jmolAppletInline ($this->size, $this->loadExternalScript($this->model), $this->script);
				break;
			case 'LOAD' :
				parent::jmolAppletLoad( $this->size, $this->loadExternalScript($this->load));
				break;
			case 'MODELLOAD':
				$this->script = $this->_jmolConvertScript($this->loadExternalScript($this->load));
				parent::jmolAppletInline ($this->size, $this->loadExternalScript($this->model), $this->script);
				break;
			default:
				parent::jmolApplet($this->size, $this->script);
				return;
		}
		$this-> getControl_();
	}

	/**
	 * Retourne un objet html
	 *
	 * @param	string	$controlName	Nom du controle
	 * @return	string					Contenu HTML
	 */
	function control($controlName) {
		if ($this->controlMode != 'no') {
			return $this->control[$controlName];
		}
	}

	/**
	 * Recuperer les objets HTML
	 *
	 * Stockage dans tableau $this->control[]
	 * @private
	 */
	function getControl_() {
		for ($i=0; $i < $this->root->childCount; $i++) {
			$ctrl = $this->root->children[$i];
			if ( $ctrl->tag == 'CONTROL' ) {
				$name = $ctrl->attrs['NAME'];

				$this->getScrip_t( $ctrl->attrs, $script='', $script2='', $name );
				switch ( $ctrl->attrs['CTRL'] ) {
					case 'jmolLink':
						if (!$script)
							$script=$this->script;
						$this->control[$name]=$this->jmolLink($script, $ctrl->attrs['TITLE']);
						break;
					case 'jmolCheckBox':
						$this->control[$name]=$this->jmolCheckBox($script, $script2, $ctrl->attrs['TITLE']);
						break;
					case 'jmolButton':
						$this->control[$name]=$this->jmolButton($script, $ctrl->attrs['TITLE']);
						break;
					case 'jmolTextArea':
						$this->control[$name]=$this->jmolTextArea($script, $ctrl->attrs['ROWS'], $ctrl->attrs['COLS'], $ctrl->attrs['TITLE']);
						break;
					case 'jmolSelect':
						$this->control[$name]=$this->jmolSelect($this->option_($ctrl->children, $name));
						break;
					case 'jmolRadio':
						$option = $this->option_($ctrl->children, $name);
						$this->control[$name]=$this->jmolRadio($option, $ctrl->attrs['DISPLAY'], $ctrl->attrs['TITLE']);
						break;
				}
			}
		}
	}
	/**
	 * Recuperation et Convertion des script externes
	 * @private
	 * @param array		$attrs 		enfants
	 * @param string	&$scritp1	scritp 1
	 * @param string	&$scritp2	scritp 2
	 * @param string	$name		nom objet
	 */
	function getScrip_t( $attrs, &$script1, &$script2, $name ) {
		foreach( $attrs as $tag=>$val ) {
			$e = $b = "";
			switch ( $tag ) {
				case 'SCRIPT':
				case 'SCRIPT1':
				case 'SCRIPT2':
					if (substr($tag,-1) != '2') {
						$script1 =$val;
					} else {
						$script2 = $val;
					}
					break;
				case 'MODEL': //don't wook
					$b = "data \"model $name\";";
					$e = ";end \"model $name\"; show data;";
					$script1 = $this->_jmolConvertScript($b.$this->loadExternalScript($val).$e);
					break;
				case 'LOAD':
					$script1 = $this->_jmolConvertScript($this->loadExternalScript($val));
					break;
			}
		}
	}
	/**
	 * Retourne les options dans un tableau
	 * @private
	 * @param	string array	tableau d'enfants
	 * @return	string array	tableau d'options
	 */
	function option_($tablo, $name) {
		$option = array();
		foreach($tablo as $k => $v ) {
			if ($v->tag=='OPTION') {
				$this->getScrip_t( $v->attrs, $script='', $script2='', $name );
				$option[$k]['script'] = $script;
				$option[$k]['text'] = $v->attrs['TITLE'];
				$option[$k]['status'] = $v->attrs['STATUS'];
			}
		}
		return $option;
	}
}
///////////////////
 /**
 * Class Xml parser
 *
 * D'apr�s le travail de
 * @author    Marcos Pont SimpleXmlParser
 */

class xml_parser {
	var $root = null;
	var $vals = array();
	var $index= array();

    function xml_parser( $data ) {
		$data = eregi_replace(">"."[[:space:]]+"."<","><", $data);

		$parser = xml_parser_create(CHARSET);
		xml_parser_set_option( $parser, XML_OPTION_TARGET_ENCODING, CHARSET );
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
		if (!@xml_parse_into_struct( $parser, $data, $this->vals, $this->index)) {
			$this->error = xml_error_string(xml_get_error_code($parser)).": ".xml_get_current_line_number($parser);
			return false;
		}
		xml_parser_free($parser);
		$i = 0;
		$this->root = new xml_node(
			$this->vals[$i]['tag'],
			isset($this->vals[$i]['attributes']) ? $this->vals[$i]['attributes'] : null,
			$this->getChildren($this->vals, $i),
			isset($this->vals[$i]['value']) ? $this->vals[$i]['value'] : null
		);
     }

	function getChildren($vals, &$i) {
		$children = array();
		while (++$i < sizeof($vals)) {
			switch ($vals[$i]['type']) {
				case 'cdata':
					array_push($children, $vals[$i]['value']);
					break;
				case 'complete':
					array_push(
						$children,
						new xml_node(
							$vals[$i]['tag'],
							isset($vals[$i]['attributes']) ? $vals[$i]['attributes'] : null,
							null, isset($vals[$i]['value']) ? $vals[$i]['value'] : null
						)
					);
					break;
				 case 'open':
				 	array_push(
				 		$children,
				 		new xml_node(
				 			$vals[$i]['tag'],
				 			isset($vals[$i]['attributes']) ? $vals[$i]['attributes'] : null,
				 			$this->getChildren($vals, $i),
				 			isset($vals[$i]['value']) ? $vals[$i]['value'] : null
				 		)
				 	);
					break;
				case 'close':
					return $children;
			}
		}
	}

}

class xml_node {
	var $tag		= "";
	var $attrs		= array();
	var $children	= array();
	var $value		= "";
	var $childCount = 0;

	function xml_node($nodeTag, $nodeAttrs, $nodeChildren=null, $nodeValue=null) {
		$this->tag		= $nodeTag;
		$this->attrs	= $nodeAttrs;
		$this->children = $nodeChildren;
		$this->value	= $nodeValue;
		$this->childCount = is_array($nodeChildren) ? sizeOf($nodeChildren) : 0;
	}
}
?>
