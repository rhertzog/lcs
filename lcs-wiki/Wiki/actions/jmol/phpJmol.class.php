<?php

if ( !defined("JMOL_CODEBASE") )
	/**
	 * Repertoire de l'applet ou de l'archive applet
	 * @constante	JMOL_CODEBASE
	 */
	define ("JMOL_CODEBASE", "./jmol");

/**
 * Nom de l'applet
 * @constante	JMOL_APPLET
 */
define ("JMOL_APPLET", "JmolApplet");

/**
 * Nom de l'archive Jmol applet
 * @constante	JMOL_ARCHIVE_APPLET
 */
 define ("JMOL_ARCHIVE_APPLET", JMOL_APPLET . "0.jar");

/**
 * Message lors du chargement de l'applet
 * @constante	BOX_MESSAGE
 */
define ("BOX_MESSAGE", "Applet en cour de chargement...!");

/**
 * Mode d'emulation moleculaire jmol
 * @constante	string	JMOL
 */
define ("JMOL", "jmol");

/**
 * Mode d'emulation moleculaire chime
 * @constante	CHIME
 */
define ("CHIME", "chime");

if ( !defined("CHARSET") )
	/**
	 * Charset
	 * @name	CHARSET
	 */
	define ("CHARSET", "UTF-8");

/**
 * Compteur d'applets et initialisation de cette variable systeme
 * @name	$countApplet
 * @global	integer $countApplet
 */
$countApplet = 0;

/**
 * Interface php pour l'applet Jmol.
 *
 * PhpJmol est une interface PHP de l'outil de visualisation de molecules Jmol.<br />
 * Elle simplifie largement la creation d'application web utilisant l'applet JMol.<br />
 * En effet, il sera tres facile de creer autant d'objet Jmol pour les analyser, les comparer, etc...<br />
 * Couple avec le puissant language script Jmol, cet outil vous permettra de realiser pour vos eleves<br />
 * a peu pres tout ce que vous voulez.<br />
 * interface javascript<br />
 * Le fichier javascript phpJmol.js realise l'interface<br />
 * entre la page html et l'applet Jmol par l'intermediaire d'objets html<br />
 * comme bouton, liste, radio groupe, checkbox, lien, zone de texte.<br />
 * Applet Jmol<br />
 * Jmol est un open source pour les etudiants, les educateurs et les chercheurs en chimie et en biochimie.<br />
 * Il est multi-plateformes, fonctionnant sous Windows, Mac OS X et les systemes Linux/Unix.<br />
 *
 * This program is free software.
 * You can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place - Suite 330, Boston, MA  02111-1307, USA.
 * A copy of the GPL can be found at http://www.gnu.org/copyleft/gpl.html or in
 * the file GNU_license.txt.
 *
 * @link http://jmol.sourceforge.net/	Le projet Jmol chez Sourceforge.net
 * @filesource phpJmol.class.php
 * @editor Eclipse & phpEclipse plugin
 * @copyright (C) Defolie Denis
 * @author	Defolie Denis
 * @link mailto:denis.defolie@free.fr
 * @version 1.0
 * @date 16/02/2006
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class phpJmol {
	/**#@+
	 * @access public
	 * @var	string
	 */
	/**
	 * Repertoire de l'archive applet JmolApplet.jar
	 *
	 *  JMOL_CODEBASE ne peut pas etre une Url.
	 * 	Par defaut codebaseDirectory est "./jmol" ( documentRoot/jmol ).
	 * 	Il vaut mieux utiliser un repertoire relatifs superieur comme "./xxx/jmol"
	 * ou inferieur comme "../../yyy/jmol"
	 */
	var $codebaseDirectory = JMOL_CODEBASE;
	/**
	 * Nom de l'applet
	 */
	var $appletName	= JMOL_APPLET;
	/**
	 * Type d'emulation JMOL ou CHIME
	 */
	var $emulate = JMOL;
	/**
	 * Tableau de fonctions internes a l'applet
	 */
	var $jsInternalFunctionTab = array();
	/**
	 * Taille Applet par defaut
	 */
	 var $appletDefaultSize = 300;
	/**
	 * Taille Maxi Applet
	 */
	 var $appletMaxiSize = 800;
	/**
	 * Taille Mini Applet
	 */
	 var $appletMiniSize = 25;
	/**
	 * Suffixe pour differencier les instances JmolApplet
	 * dans les javascripts lancees par elles
	 */
	var $suffix	= 0;
	/**
	 * Nom de la classe Css par defaut sur l'objet html applet ou object
	 */
	var $AppletCssClass	= "class='jmol-applet'";
	/**
	 * Nom de la classe Css par defaut sur l'objet html link
	 */
	var $LinkCssClass = "class='jmol-link'";
	/**
	 * Nom de la classe Css par defaut sur l'objet html button
	 */
	var $ButtonCssClass	= "class='jmol-button'";
	/**
	 * Nom de la classe Css par defaut sur l'objet html radio group
	 */
	var $RadioCssClass = "class='jmol-radio'";
	/**
	 * Nom de la classe Css par defaut sur l'objet html checkbox
	 */
	var $BoxCssClass = "class='jmol-box'";
	/**
	 * Nom de la classe Css par defaut sur l'objet html select
	 */
	var $SelectCssClass	= "class='jmol-select'";
	/**
	 * Nom de la classe Css par defaut sur l'objet html textarea
	 */
	var $TextAreaCssClass = "class='jmol-text'";
	 /**
	  * Compteur d'objet Link
	  */
	var $countLink = 0;
	 /**
	  * Compteur d'objet Button
	  */
	var $countButton = 0;
	 /**
	  * Compteur d'objet Radio Group
	  */
	var $countRadio = 0;
	 /**
	  * Compteur d'objet CheckBox
	  */
	var $countBox = 0;
	 /**
	  * Compteur d'objet Select
	  */
	var $countSelect = 0;
	/**
	 * Presence de la barre de progression de l'applet
	 */
	var $progressbar = "true";
	/**
	 * Couleur par defaut barre de progression de l'applet
	 */
	var $progresscolor = "blue";
	/**
	 * Message au chargement de l'applet
	 */
	var $boxmessage	= BOX_MESSAGE;
	/**
	 * Couleur de fond par defaut de l'applet
	 */
	var $boxbgcolor	= "lightslategray";
	/**
	 * Couleur de caractere par defaut de l'applet
	 */
	var $boxfgcolor	= "floralwhite";
	/**#@-*/

	/**
	 * Creation de l'objet phpJmol (PHP 4 Contructeur)
	 *
 	 * @param 	string	$title	Titre de l'applet
	 */
	function phpJmol( $title )	{
		$this->__construct( $title ) ;
	}
	/**
	 * Creation de l'objet phpJmol (PHP 5 Constructeur)
	 *
 	 * @param 	string	$title	Titre de l'applet
	 */
	function __construct( $title )	{
		global $countApplet;

		$this->suffix = $countApplet++;
		$this->appletName .= $this->suffix;
		$this->codebaseDirectory = $this->_jmolSetCodebase( JMOL_CODEBASE );
		$this->title = $title;
		$this->init();
	}

	/**
	 * Init: implementer si besoin
	 * @abstract
	 */
	function init() {

	}
	/**
	 * Force les couleurs de l'applet
	 *
	 * @access public
	 * @param string	$boxbgcolor		Couleur de fond applet
	 * @param string	$boxfgcolor		Couleur caractere applet
	 * @param string	$progresscolor	Couleur message de chargement
	 */
	function jmolSetAppletColor( $boxbgcolor, $boxfgcolor, $progresscolor ) {
		$this->progresscolor= $progresscolor;
		$this->boxbgcolor	= $boxbgcolor;
		$this->boxfgcolor	= $boxfgcolor;
	}
	/**
	 * Force la feuille de style sur applet
	 *
	 * @access public
	 * @param string 	$AppletCssClass 	feuille de style
	 */
	function jmolSetAppletCssClass($AppletCssClass) {
	    $this->AppletCssClass = "class='$AppletCssClass'";
	}
	/**
	 * Init JmolApplet avec un script
	 *
	 * @access public
	 * @param	int or array	$size 	dimensions applet tableau LxH ou carre X
	 * @param	string			$script script interne ex: "load url" ou "load ./xxx/samples/caffeine.xyz"
	 */
	function jmolApplet( $size=200, $script ) {
		$this->jmolAppletInline ($size, "", $script);
	}
	/**
	 * Init JmolApplet avec un script externe
	 *
	 * @access public
	 * @param	string	$size 	dimensions applet tableau LxH ou carre X
	 * @param	string	$script script externe format texte
	 *
	 */
	function jmolAppletLoad ($size=200, $script ) {
		$this->jmolAppletInline ($size, "", $this->_jmolConvertScript($script));
	}
	/**
	 * Init JmolApplet avec un modele moleculaire en ligne et un script
	 *
	 * Passe le contenu du modele moleculaire plutot qu'un nom de fichier ou une URL.
	 * @access public
	 * @param	string	$size 			dimensions applet tableau LxH ou carre X
	 * @param	string	$inlineModel	modele moleculaire en ligne
	 * @param	string	$script 		script interne ex: "load url" ou "load ./samples/caffeine.xyz"
	 *
	 */
	function jmolAppletInline ($size=200, $inlineModel, $script="select *" ) {
		$this->_jmolGetAppletSize( $size );
		$this->script = $script;
		if ($inlineModel != "") {
			$this->inlineModel = $this->_jmolConvertInline($inlineModel);
		}
	}
	/**
	 * Chargement d'un modele
	 *
	 * Utilise dans des applications de bases de donnees ou
	 * le modele moleculaire est disponible sous la forme d'une chaine de caracteres.<br />
	 * la methode charge directement le modele dans l'applet.<br />
	 * Ce n'est pas le nom de fichier qui est charge, mais le contenu du fichier.
	 * @access public
	 * @param string	$model	modele moleculaire a charger
	 */
	function jmolLoadInline( $model ) {
		if ( $model ) {
			$this->inlineModel = $this->_jmolConvertInline($model);
		}
	}

	/**
	 * Clefs du tableau des fonctions js appellees par JmolApplet
	 *
	 * 	4 fonctions possibles a prioris:
	 * 	 - AnimFrameCallback
	 * 	 - LoadStructCallback
	 * 	 - MessageCallback
	 * 	 - PauseCallback
	 *   - PickCallback
	 * @access public
	 * @param	string	$name		nom des clefs ci-dessus
	 * @param	string	$function	nom de la fonction
	 */
	function add_javascript( $name, $function ) {
		$this->jsInternalFunctionTab[$name] = $function;
	}
	/**
	 * Force le type d'emulation moleculaire. Par defaut il est jmol
	 *
	 * @access public
	 * @param	string	$emul	EMUL ou CHIME
	 */
	function emulate( $emul ) {
		$this->emulate = ( $emul != CHIME || $emul != JMOL ) ? JMOL : $emul;
	}
	/**
	 * Implemente l'objet html applet ou object
	 *
	 * Parametre par defaut 'object'.
	 * @access public
	 * @param	string	$mode	object ou applet
	 * @return	string	applet ou objet applet html
	 */
	function applet( $mode="object") {
		$size = " width='$this->width' height='$this->height'";
		$s = "<h1 $this->AppletCssClass>$this->title</h1>\n<p $this->AppletCssClass>\n";
		if ( $mode == "applet") {
			$s.= "<applet name='$this->appletName' id='$this->appletName'";
			$s.= " codebase='$this->codebaseDirectory' code='" . JMOL_APPLET . "'";
			$s.= " archive='" . JMOL_ARCHIVE_APPLET ."'";
			$s.= " $size>\n";
		} else {
			$s.= "<object name='$this->appletName' id='$this->appletName'";
			$s.= " classid='java:" . JMOL_APPLET . "' type='application/x-java-applet'";
			$s.= " $size>\n";
			$s.= $this->_get_params( "codebase", $this->codebaseDirectory );
			$s.= $this->_get_params( "archive", JMOL_ARCHIVE_APPLET);
		}

		$s.= $this->_get_params( "progressbar", $this->progressbar );
		$s.= $this->_get_params( "progresscolor", $this->progresscolor );
		$s.= $this->_get_params( "boxmessage", $this->boxmessage );
		$s.= $this->_get_params( "boxbgcolor", $this->boxbgcolor );
		if ( $this->script != "") {
			$s.= $this->_get_params( "script", $this->script );
		}
		$s.= $this->_get_params( "emulate", $this->emulate );

		if ( sizeof( $this->jsInternalFunctionTab ) != 0 ) {
			$s.= $this->_get_params( "mayscript", "true" );
			foreach( $this->jsInternalFunctionTab as $name => $func ) {
				$s.= $this->_get_params( $name, $func );
			}
		}
		if ( $this->inlineModel != "" ) {
			$s.= $this->_get_params( "loadInline", $this->inlineModel );
		}
		if ( $mode == "applet") {
			$s.= "</applet>\n";
		} else {
			$s.= "</object>\n";
		}
		return "$s</p>\n";
	}
/**
 * Objets html<br />
 * Le script est execute dans l'applet quand l'utilisateur clique dessus
 */
	/**
	 * Affiche une balise texterea html
	 *
	 * C'est la zone texte qui contient le script a executer
	 * @public
	 * @param string	$script	script a executer
	 * @param integer	$rows	nombre de lignes
	 * @param integer	$cols	nombre de colonnes
	 * @param string	$text	texte descriptif
	 */
	function jmolTextArea($script="", $rows=8, $cols=40, $text="Ligne de commande") {
		$id = "text" . $this->suffix . "_" . $this->countLink++;
		$bt = "ok_" . $this->countLink;
		$s = "<fieldset $this->TextAreaCssClass>";
		$s.= "<legend>$text</legend>";
		$s.= "<textarea  $this->TextAreaCssClass id='$id' name='$id' rows='$rows' cols='$cols' wrap='off'>\n";
		$s.= $this->jmolLoadInline( $script );
		$s.= "\n</textarea><br />\n";
		$s.= "<input type='button' id='$bt' name='$bt' value='Ok'";
		$s.= " onclick=\"jmolTextArea('$this->appletName', '$id');\" />\n";
		$s.= "</fieldset>";
		return $s;
	}
	/**
	 * Force la classe de style de la balise lien html
	 *
	 * @public
	 * @param string	$TextAreaCssClass	nouvelle classe de style
	 */
	function jmolSetTextAreaCssClass($TextAreaCssClass) {
	    $this->TextAreaCssClass = "class='$TextAreaCssClass'";
	}
	/**
	 * Affiche une balise lien html
	 *
	 * @public
	 * @param string	$script	script a executer
	 * @param string	$text	texte du lien
	 */
	function jmolLink($script, $text) {
		$id = "link" . $this->suffix . "_" . $this->countLink++;
		return "<a $this->LinkCssClass id='$id' href=\"javascript:jmolLink('$this->appletName', '$script')\">$text</a>\n";
	}
	/**
	 * Force la classe de style de la balise lien html
	 *
	 * @public
	 * @param string	$LinkCssClass	nouvelle classe de style
	 */
	function jmolSetLinkCssClass($LinkCssClass) {
	    $this->LinkCssClass = "class='$LinkCssClass'";
	}
	/**
	 * Affiche une balise case a cocher checkbox html
	 *
	 * @public
	 * @param string	$scriptWhenChecked		script a executer case cochee
	 * @param string	$scriptWhenUnchecked	script a executer case decochee
	 * @param string	$text					texte case a cocher
	 */
	function jmolCheckBox($scriptWhenChecked, $scriptWhenUnchecked, $text) {
		$id= "box" . $this->suffix . "_" . $this->countBox++;
		$text = htmlentities($text, ENT_QUOTES, CHARSET);
		$s = "<input $this->BoxCssClass type='checkbox' id='$id' name='$id' value='0'";
		$s.= " onclick=\"jmolCheckbox('$this->appletName', '$id', '$scriptWhenChecked', '$scriptWhenUnchecked');\"/>";
		$s.= "<span $this->BoxCssClass>$text</span>\n";
		return $s;
	}
	/**
	 * Force la classe de style de la balise checkbox html
	 *
	 * @public
	 * @param string	$BoxCssClass	nouvelle classe de style
	 */
	function jmolSetBoxCssClass($BoxCssClass) {
	    $this->BoxCssClass = "class='$BoxCssClass'";
	}
	/**
	 * Affiche cases a cocher radio groupe html
	 *
	 * Le tableau contient les parametres scripts et textes
	 * Si $text est null alors c'est le script qui est affiche a la place
	 * @public
	 * @param array		$tablo	scripts et textes
	 * @param string	$align	alignement inline ou block
	 */
	function jmolRadio($tablo, $align="inline", $text) {
		$id= "radio" . $this->suffix . "_" . $this->countRadio++;
		$i = 0;
		$s = "<dl $this->RadioCssClass>\n";
		$s.= "<dt $this->RadioCssClass>$text</dt>\n";
		foreach ( $tablo as $n => $v ) {
			$script = $v['script'];
			$status = $v['status'];
			$text	= $v['text'];
			if ( $text == "" ) {
				$text = $script;
			}
			$text = htmlentities($text, ENT_QUOTES, CHARSET);
			$s.="<dd $this->RadioCssClass style='display:$align;'>\n";
			$s.= "<input $this->RadioCssClass type='radio' id='$id-". $i++ ."' name='$id' value='0'";
			$s.= " $status onclick=\"jmolRadio('$this->appletName', '$script');\"/>$text\n";
			$s.="</dd>\n";
		}
		$s.= "</dl>\n";
		return $s;
	}
	/**
	 * Force la classe de style de la balise radio html
	 *
	 * @public
	 * @param string	$RadioCssClass	nouvelle classe de style
	 */
	function jmolSetRadioCssClass($RadioCssClass) {
	    $this->RadioCssClass = "class='$RadioCssClass'";
	}
	/**
	 * Affiche une balise button html
	 *
	 * @public
	 * @param string	$script		script a executer
	 * @param string	$text		texte bouton
	 */
	function jmolButton($script, $text) {
		$id= "button" . $this->suffix . "_" . $this->countButton++;
		$text = htmlentities($text, ENT_QUOTES, CHARSET);
		$s = "<input $this->ButtonCssClass type='button' id=\"$id\" name='$id' value='$text'";
		$s.= " onclick=\"javascript:jmolButton('$this->appletName', '$script')\" />\n";
		return $s;
	}
	/**
	 * Force la classe de style de la balise button html
	 *
	 * @public
	 * @param string	$ButtonCssClass	nouvelle classe de style
	 */
	function jmolSetButtonCssClass($ButtonCssClass) {
	    $this->ButtonCssClass = "class='$ButtonCssClass'";
	}
	/**
	 * Affiche balise select option html
	 *
	 * Le tableau contient les parametres scripts et textes
	 * @public
	 * @param array		$tablo	scripts et textes
	 */
	function jmolSelect($tablo) {
		$id= "select" . $this->suffix . "_" . $this->countSelect++;
		$i = 0;
		$s = "<select id='$id' $this->SelectCssClass onchange=\"jmolSelect('$this->appletName', '$id')\">\n";
		foreach ( $tablo as $n => $v ) {
			$text 	= htmlentities($v['text'], ENT_QUOTES, CHARSET);
			$script = $v['script'];
			$status = $v['status'];
			$s.="<option $this->SelectCssClass value='$script' $status>$text</option>\n";
		}
		$s.= "</select>\n";
		return $s;
	}
	/**
	 * Force la classe de style de la balise select html
	 *
	 * @public
	 * @param string	$SelectCssClass	nouvelle classe de style
	 */
	function jmolSetSelectCssClass($SelectCssClass) {
	    $this->SelectCssClass = "class='$SelectCssClass'";
	}
	/**
	 * Conversion d'une ligne de texte d'un modele moleculaire
	 *
	 * @public
	 * @param	string	$model description du modele
	 * @return	string	modele moleculaire converti
	 */
	function _jmolConvertInline( $model ) {
		$modl = str_replace("\r\n", "\n", $model);
		$modl = str_replace("\n", "|", $modl);
		$modl = str_replace("\r", "|", $modl);
		return '|' . $modl . '|';
	}
	/**
	 * Conversion d'un script texte
	 *
	 * @private
	 * @param	string	$script script texte
	 * @return	string	script converti
	 */
	function _jmolConvertScript( $script ) {
		$s = str_replace("\r\n", "\n", $script);
		$s = str_replace("\n", ";", $s);
		$s = str_replace("\r", ";", $s);
		return htmlentities($s, ENT_QUOTES);
	}

/**
 * Methodes privees
 */
	/**
	 * Determine le repertoire du code de l'Applet
	 *
	 * @private
	 * @param	string	$codebaseDirectory	Repertoire du code
	 * @return 	string	Repertoire du code
	 */
	function _jmolSetCodebase( $codebaseDirectory ) {
		if ( $codebaseDirectory != "") {
			$reg = array();
			if ( ereg("([://]{3})", $codebaseDirectory, $reg) ) {
				die ("Pas d'Url dans  CodeBaseDirectory<br />");
			}
		}
		return $codebaseDirectory;
	}
	/**
	 * Determine le taille de l'applet
	 *
	 * @private
	 * @param	String	$size	Taille: format carre ou lxh.
	 * @return	void
	 */
	function _jmolGetAppletSize( $size ) {
		$t = split("x", strtolower($size));
		$x = (int) $t[0];
		$y = (int) $t[1];
		$this->width = ( $x < $this->appletMiniSize || $x > $this->appletMaxiSize ) ? $this->appletDefaultSize : $x;
		$this->height= $y != "" ? (($y < $this->appletMiniSize || $y > $this->appletMaxiSize) ? $this->appletDefaultSize : $y) : $this->width;
	}
	/**
	 * Retourne la balise param de l'applet
	 *
	 * @public
	 * @param	string	$name	nom de la valeur
	 * @param	string	$value	valeur a passer
	 * @return	string 	balise param de l'applet
	 */
	function _get_params( $name, $value) {
		return "<param name='$name' value='$value'>\n";
	}
}//end of class
?>